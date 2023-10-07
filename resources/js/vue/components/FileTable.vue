<template>
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col">Times</th>
                <th scope="col">File Name</th>
                <th scope="col">Progress</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="files.length < 1">
                <tr>
                    <td class="text-center" colspan="4">No data found!</td>
                </tr>
            </template>
            <template v-else>
                <tr v-for="file in files">
                    <template v-if="file.file_instant_status != null">
                        <td class="text-center" colspan="4">
                            <div class="alert alert-info" role="alert">
                                {{ file.file_instant_status }}
                            </div>
                        </td>
                    </template>
                    <template v-else>
                        <td scope="row" class="align-middle">
                            <div>
                                {{ file.updated_date }}
                            </div>
                            <div>
                                ( {{ file.updated_diff }} )
                            </div>
                        </td>
                        <td class="align-middle">
                            {{ file.file_name }}
                        </td>
                        <td class="align-middle">
                            <div class="progress" role="progressbar" aria-label="Animated striped example"
                                :aria-valuenow="$progress(file)" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                    :style="{ width: `${$progress(file)}%` }">
                                    {{ $progress(file) }}%
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <span class="badge rounded-pill bg-primary" v-if="file.status == 'Pending'">{{ file.status
                            }}</span>
                            <span class="badge rounded-pill bg-warning" v-if="file.status == 'Processing'">{{ file.status
                            }}</span>
                            <span class="badge rounded-pill bg-danger" v-if="file.status == 'Failed'">{{ file.status
                            }}</span>
                            <span class="badge rounded-pill bg-success" v-if="file.status == 'Completed'">{{ file.status
                            }}</span>
                        </td>
                    </template>
                </tr>
            </template>
        </tbody>
    </table>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useFileStore } from "@/stores/file";
import { storeToRefs } from "pinia";

const fileStore = useFileStore();

const { files } = storeToRefs(fileStore)

onMounted(() => {
    nextTick().then(() => {
        fileStore.init();
    })
})

const $progress = (file: { [key: string]: any }) => {
    const current_value = file.collected_current_row;
    const total_value = file.collected_total_row;
    const percentage_complete = (current_value / total_value) * 100
    const progress_bar_width = (percentage_complete / 100) * 100
    return parseInt(String(progress_bar_width));
}

import { computed, nextTick, watch } from "vue";
import { usePusherStore } from "@/stores/pusher";

const { listen } = usePusherStore();
const { updateFileInstanceStatus } = useFileStore()
let pauseWatcherId = 0;
const pusherData: any = computed(() => {
    const e = listen("file_processing-event");

    if (e != null) return e.data;
    return {};
});

watch(() => pusherData, (data) => {
    const { file } = data.value;

    if (pauseWatcherId == file.id) return;

    fileStore.update(file);
    if (file.status == "Completed") {
        updateFileInstanceStatus(file.id, "Import data successfull")
        pauseWatcherId = file.id;
    }
}, { deep: true })

</script>

<style scoped>
.table>tbody>tr:last-child>td {
    border-bottom: 0;
}
</style>