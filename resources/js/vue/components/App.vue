<template>
    <Navbar />
    <main class="py-4">
        <slot/>
    </main>
</template>

<script setup lang="ts">
import Navbar from "@/components/Navbar.vue"
import { computed, watch } from "vue";
import { usePusherStore } from "@/stores/pusher";

const { listen } = usePusherStore();
const pusherData: any = computed(() => {
    const e = listen("test_pusher-event");

    if (e != null) return e.data;
    return {};
});

watch(() => pusherData, (data) => {
    console.log(data.value);

}, { deep: true })
</script>