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
    margin-top: 50px;
}
</style>
