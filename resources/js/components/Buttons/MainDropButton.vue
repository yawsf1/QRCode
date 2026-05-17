<script setup>
import { ref } from "vue";

defineProps({
    text: {
        type: String,
        required: true,
    },
});

const rotate = ref(false);
</script>

<template>
    <button @click="rotate = !rotate" class="mainButton">
        <span>{{ text }}</span>
    </button>
</template>

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@500;600&display=swap");

.mainButton {
    --accent: #4f7cff;
    --accent-hover: #3b66eb;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 42px;
    width: 100%;
    padding: 0 24px;
    background: var(--accent);
    color: #ffffff;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-family: "Sora", sans-serif;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.01em;
    white-space: nowrap;
    user-select: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    transition:
        background-color 0.15s ease,
        transform 0.1s ease;

    &:hover {
        background-color: var(--accent-hover);
    }

    &:active {
        transform: scale(0.98);
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
}
</style>
