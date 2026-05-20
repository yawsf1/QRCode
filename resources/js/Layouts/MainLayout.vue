<script setup>
import HorizontalBar from "../components/NavigationBars/HorizontalBar.vue";
import GlobalLoadingBar from "../components/Ui/GlobalLoadingBar.vue";
</script>

<template>
    <div class="layout-container">
        <GlobalLoadingBar />
        <HorizontalBar />
        <main class="main">
            <slot />
        </main>
    </div>
</template>

<style lang="scss" scoped>
.layout-container {
    --layout-header-offset: 60px;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100%;
    max-width: 100vw;
    background-color: #0a0a0f;
    overflow-x: hidden;
}

.main {
    padding-top: 60px;
    width: 100%;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
    background-color: #0a0a0f;
}
</style>
