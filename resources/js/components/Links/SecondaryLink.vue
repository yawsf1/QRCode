<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    text: {
        type: String,
        required: true,
    },
    link: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link class="secondaryLink" :href="link">
        <span>{{ text }}</span>
    </Link>
</template>

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@500;600&display=swap");

.secondaryLink {
    --surface-tint: rgba(255, 255, 255, 0.04);
    --surface-hover: rgba(255, 255, 255, 0.08);
    --border: rgba(255, 255, 255, 0.08);
    --border-hover: rgba(255, 255, 255, 0.16);
    --text: #8888aa;
    --text-hover: #f0f0f8;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 36px; /* Coordinates identically with MainLink dimensions */
    padding: 0 16px;
    background: var(--surface-tint);
    color: var(--text);
    border-radius: 8px; /* Matching profile radius sharpness */
    border: 1px solid var(--border);
    cursor: pointer;
    font-family: "Sora", sans-serif;
    font-weight: 600;
    font-size: 12.5px;
    letter-spacing: 0.01em;
    text-decoration: none;
    white-space: nowrap;
    user-select: none;
    transition:
        background-color 0.15s ease,
        border-color 0.15s ease,
        color 0.15s ease,
        transform 0.1s ease;

    &:hover {
        background-color: var(--surface-hover);
        border-color: var(--border-hover);
        color: var(--text-hover);
    }

    &:active {
        transform: scale(0.97);
    }
}
</style>
