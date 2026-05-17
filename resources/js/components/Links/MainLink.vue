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
    <Link class="mainLink" :href="link">
        <span>{{ text }}</span>
    </Link>
</template>

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@600;700&display=swap");

.mainLink {
    --accent: #4f7cff;
    --accent-hover: #5d87ff;
    --border-glow: rgba(79, 124, 255, 0.25);

    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 36px; /* Compact height */
    padding: 0 16px; /* Reduced side real-estate */
    background: var(--accent);
    color: #ffffff;
    border-radius: 8px; /* Tighter radius for a sharper profile */
    border: none;
    cursor: pointer;
    font-family: "Sora", sans-serif;
    font-weight: 600; /* Sophisticated medium weight */
    font-size: 12.5px; /* Scaled down typography */
    letter-spacing: 0.01em;
    text-decoration: none;
    box-shadow:
        0 2px 10px rgba(0, 0, 0, 0.2),
        0 0 0 1px var(--border-glow); /* Controlled micro-glow */
    transition:
        background-color 0.15s ease,
        transform 0.1s ease,
        box-shadow 0.15s ease;
    white-space: nowrap;
    user-select: none;

    &:hover {
        background-color: var(--accent-hover);
        box-shadow:
            0 4px 14px rgba(79, 124, 255, 0.2),
            0 0 0 1px rgba(79, 124, 255, 0.4);
    }

    &:active {
        transform: scale(0.97);
    }
}
</style>
