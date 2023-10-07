<template>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <div class="navbar-brand">
                {{ appName }}
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
let appName = null;

if (document.querySelector('meta[name="app-name"]')) {
    const val = document.querySelector('meta[name="app-name"]').getAttribute("content");

    if (val.trim().length > 0) {
        appName = val;
    }
}
</script>