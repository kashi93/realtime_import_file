import axios from "axios";
import { defineStore } from "pinia";

export const useFileStore = defineStore("file", {
    state: () => ({
        files: []
    }),

    actions: {
        async store(file: File) {
            if (file == null) return false;

            const formData = new FormData();
            formData.append("file", file);

            const response = await axios.post("file/store", formData);

            if (response.status == 200) {
                const { data } = response.data;

                if (data != null) {
                    if (data.file != null) {
                        this.files.unshift(data.file);
                        this.updateFileInstanceStatus(data.file.id, "Successfully uploaded!")
                        return true;
                    }
                }
            }

            if (response.status == 422) {
                alert("Invalid input!");
            }

            return false;
        },
        update(file) {
            const i = this.files.findIndex((f) => f.id == file.id);

            if (i != -1) {
                this.files[i] = file;
            }
        },
        updateFileInstanceStatus(id, message) {
            const self = this;
            const i = self.files.findIndex((f) => f.id == id);

            if (i != -1) {
                self.files[i].file_instant_status = message;

                setTimeout(() => {
                    delete self.files[i].file_instant_status;
                }, 1000);
            }
        },
        async init() {
            const response = await axios.get("file");

            if (response.status == 200) {
                const { data } = response.data;

                if (data != null) {
                    if (data.files != null) {
                        this.files = data.files;
                    }
                }

            }
        }
    }
})