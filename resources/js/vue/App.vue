<script setup lang="ts">
import { RouterView } from 'vue-router'

import { showErrorOverlayEvent } from "@/helpers";
showErrorOverlayEvent();

import { usePusherStore } from "@/stores/pusher";
import { onMounted,nextTick } from 'vue';
const { init, subscribe } = usePusherStore();

onMounted(() => {
  nextTick().then(()=>{
    init();
    subscribe();
  })
})
</script>

<template>
  <RouterView />
</template>
