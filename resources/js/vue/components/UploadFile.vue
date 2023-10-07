<template>
    <div class="col-6 my-auto" @drop.prevent="handleDrop($event)">
        <div class="badge bg-primary position-relative" v-if="modelValue != null">
            {{ modelValue.name }}
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                @click.prevent="emit('update:modelValue', null)" style="cursor: pointer;">
                &times;
            </span>
        </div>
        <label for="file" class="text-primary text-decoration-underline" style="cursor: pointer;" v-else>Select
            file/Drag and drop</label>
        <input type="file" id="file" class="d-none" @change="uploadFile($event)">
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';

defineProps({
    modelValue: {
        type: File,
        required: false,
    }
})

const emit = defineEmits([
    "update:modelValue"
]);

const uploadFile = ($event: Event) => {
    const target = $event.target as HTMLInputElement;
    if (target && target.files) {
        emit('update:modelValue', target.files[0])
        target.value = "";
    }
}

const handleDrop = ($event: DragEvent) => {
    const dataTransfer: DataTransfer = $event.dataTransfer;

    if (dataTransfer != null) {
        emit('update:modelValue', dataTransfer.files[0])
    }
}

const events = ['dragenter', 'dragover', 'dragleave', 'drop']

onMounted(() => {
    events.forEach((eventName) => {
        document.body.addEventListener(eventName, function (e) {
            e.preventDefault();
        })
    })
})

onUnmounted(() => {
    events.forEach((eventName) => {
        document.body.removeEventListener(eventName, function (e) {
            e.preventDefault();
        })
    })
})
</script>

<style scoped></style>