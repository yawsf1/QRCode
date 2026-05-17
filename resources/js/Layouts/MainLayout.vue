<script setup>
import HorizonatlBar from "../components/NavigationBars/HorizonatlBar.vue";
</script>

<template>
    <div class="layout-container">
        <HorizonatlBar />
        <main class="main">
            <slot />
        </main>
    </div>
</template>

<style lang="scss" scoped>
.layout-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100%;
    background-color: #0a0a0f;
}

.main {
    padding-top: 60px;
    width: 100%;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    overflow: visible;
    background-color: #0a0a0f;
}
</style>
