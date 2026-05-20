<script setup>
import GlobalLoadingBar from "../components/Ui/GlobalLoadingBar.vue";
</script>

<template>
    <div class="layout-dashboard">
        <GlobalLoadingBar />
        <div class="layout-dashboard__content">
            <slot />
        </div>
    </div>
</template>

<style lang="scss" scoped>
.layout-dashboard {
    --layout-header-offset: 0px;
    min-height: 100vh;
    width: 100%;
    max-width: 100vw;
    display: flex;
    flex-direction: column;
    background-color: #0a0a0f;
    overflow-x: hidden;
}

.layout-dashboard__content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    width: 100%;
}
</style>
