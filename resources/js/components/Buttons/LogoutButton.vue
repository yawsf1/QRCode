<script setup>
defineProps({
    text: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <button class="mainButton">
        <span class="material-symbols-rounded">logout</span>
        <span>{{ text }}</span>
    </button>
</template>

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@500;600&display=swap");

.mainButton {
    --accent: #4f7cff;
    --accent-hover: #5d87ff;
    --border-glow: rgba(79, 124, 255, 0.25);

    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    padding: 0 16px;
    background: var(--accent);
    color: #ffffff;
    border-radius: 8px; /* Consistent sharp look */
    border: none;
    cursor: pointer;
    font-family: "Sora", sans-serif;
    font-weight: 600;
    font-size: 12.5px;
    letter-spacing: 0.01em;
    white-space: nowrap;
    user-select: none;
    gap: 8px; /* Balanced icon-to-text spacing */
    box-shadow:
        0 2px 10px rgba(0, 0, 0, 0.2),
        0 0 0 1px var(--border-glow);
    transition:
        background-color 0.15s ease,
        box-shadow 0.15s ease,
        transform 0.1s ease;

    .material-symbols-rounded {
        font-size: 18px; /* Clean scale profile fit */
        display: flex;
        align-items: center;
    }

    &:hover {
        background-color: var(--accent-hover);
        box-shadow:
            0 4px 14px rgba(79, 124, 255, 0.2),
            0 0 0 1px rgba(79, 124, 255, 0.4);
    }

    &:active {
        transform: scale(0.97);
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
}
</style>
