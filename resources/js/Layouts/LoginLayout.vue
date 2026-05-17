<script setup></script>

<template>
    <main class="main">
        <slot></slot>
    </main>
</template>

<style scoped lang="scss">
.main {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    min-height: 100vh; // ← was margin-top: 50px
}
</style>
