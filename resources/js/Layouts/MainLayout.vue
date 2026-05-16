<script setup>
import HorizonatlBar from "../components/NavigationBars/HorizonatlBar.vue";
</script>

<template>
    <HorizonatlBar />
    <main class="main">
        <slot />
    </main>
</template>

<style lang="scss" scoped>
.main {
    width: 100%;
    min-height: calc(100vh - 50px);
    margin-top: 50px;
    display: flex;
    flex-direction: column;
}
</style>
