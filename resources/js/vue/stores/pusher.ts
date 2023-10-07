/// <reference types="vite/client" />
import { defineStore } from "pinia";
import Pusher from 'pusher-js';

export const usePusherStore = defineStore("pusher", {
    state: () => ({
        pusher: null as Pusher,
        subscribes: [
            {
                init: false,
                channel: "test-channel",
                event: "test_pusher-event",
                data: null
            },
            {
                init: false,
                channel: "file_processing-channel",
                event: "file_processing-event",
                data: null
            },
        ] as {
            init: Boolean,
            channel: string,
            event: string,
            data: any
        }[]
    }),
    actions: {
        init() {
            if (this.pusher == null) {
                this.pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
                    cluster: 'ap1'
                });
            }
        },
        subscribe() {
            for (const [i, s] of this.subscribes.entries()) {
                if (s.init) return;

                const channel = this.pusher.subscribe(s.channel);
                channel.bind(s.event, (data: any) => this.subscribes[i].data = data);
                this.subscribes[i].init = true;
            }
        },
        listen(event: string) {
            const [e] = this.subscribes.filter((s) => s.event == event);
            return e;
        }
    }
})