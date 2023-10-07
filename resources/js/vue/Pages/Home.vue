<template>
    <App>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <UploadFile v-model="file" />
                                <div class="col-6 d-flex justify-content-end">
                                    <button class="btn btn-primary" @click.prevent="submitFile">Upload File</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <FileTable />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </App>
</template>

<script setup lang="ts">
import App from "@/components/App.vue"
import FileTable from "@/components/FileTable.vue";
import UploadFile from "@/components/UploadFile.vue";
import { useFileStore } from "@/stores/file";
import { ref, type Ref } from "vue";

const file: Ref<File> = ref(null)
const { store } = useFileStore();

const submitFile = async () => {
    if (await store(file.value)) {
        file.value = null;
    }
}
</script>