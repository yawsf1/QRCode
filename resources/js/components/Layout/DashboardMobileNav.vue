<script setup>
import { useUiStore } from "../../stores/ui";

const ui = useUiStore();
</script>

<template>
    <button
        type="button"
        class="mobileMenuBtn"
        aria-label="Menu"
        @click="ui.toggleMobileSidebar()"
    >
        <span class="material-symbols-rounded">menu</span>
    </button>
</template>

<style scoped>
.mobileMenuBtn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.04);
    color: #f0f0f8;
    cursor: pointer;
}

@media (max-width: 768px) {
    .mobileMenuBtn {
        display: flex;
    }
}
</style>
