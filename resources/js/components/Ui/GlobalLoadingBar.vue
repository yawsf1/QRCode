<script setup>
import { useUiStore } from "../../stores/ui";

const ui = useUiStore();
</script>

<template>
    <div v-if="ui.globalLoading" class="loadingBar" role="status" aria-live="polite">
        <div class="loadingBarInner"></div>
    </div>
</template>

<style scoped>
.loadingBar {
    position: fixed;
    top: var(--layout-header-offset, 0px);
    left: 0;
    right: 0;
    height: 3px;
    z-index: 9999;
    background: rgba(79, 124, 255, 0.15);
    overflow: hidden;
}

.loadingBarInner {
    height: 100%;
    width: 40%;
    background: linear-gradient(90deg, #4f7cff, #a78bfa);
    animation: slide 1s ease-in-out infinite;
}

@keyframes slide {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(350%);
    }
}
</style>
