/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
import { showErrorOverlay } from "./vue/helpers";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.baseURL = `${window.location.origin}/api/`;
axios.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response != null) {
      if ((err.response.status || 0) >= 500) {
        let stack = null;
        const { message } = err.response.data;
        const { trace } = err.response.data;

        for (const t of trace || []) {
          if (t.file != null && t.line != null) {
            stack += `at ${t.file}:${t.line}:0\n`;
          }
        }

        if (message != null && stack != null) {
          showErrorOverlay({ stack, message });
        }
      }
    }
    return err.response;
  }
);

