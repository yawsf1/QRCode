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
    <button @click="rotate = !rotate" class="secondaryButton">
        <span>{{ text }}</span>
        <span
            class="material-symbols-rounded icon"
            :class="{ 'is-rotated': rotate }"
        >
            keyboard_arrow_down
        </span>
    </button>
</template>

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@500;600&display=swap");

.secondaryButton {
    --surface-tint: rgba(255, 255, 255, 0.04);
    --surface-hover: rgba(255, 255, 255, 0.08);
    --border: rgba(255, 255, 255, 0.08);
    --border-hover: rgba(255, 255, 255, 0.16);
    --text: #8888aa;
    --text-hover: #f0f0f8;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 42px;
    width: 100%;
    padding: 0 24px;
    background: var(--surface-tint);
    color: var(--text);
    border-radius: 12px;
    border: 1px solid var(--border);
    cursor: pointer;
    font-family: "Sora", sans-serif;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.01em;
    white-space: nowrap;
    user-select: none;
    gap: 8px; /* Smooth gap between text and arrow indicator */
    transition:
        background-color 0.15s ease,
        border-color 0.15s ease,
        color 0.15s ease,
        transform 0.1s ease;

    .icon {
        font-size: 20px;
        display: flex;
        align-items: center;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);

        &.is-rotated {
            transform: rotate(180deg);
        }
    }

    &:hover {
        background-color: var(--surface-hover);
        border-color: var(--border-hover);
        color: var(--text-hover);
    }

    &:active {
        transform: scale(0.98);
    }
}
</style>
