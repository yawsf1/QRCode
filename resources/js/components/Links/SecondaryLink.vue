<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    text: String,
    link: String,
});
</script>

<template>
    <Link class="secondaryLink" :href="link">{{ text }}</Link>
</template>

<style lang="scss" scoped>
.secondaryLink {
    background-color: #eeeeee;
    color: black;
    padding: 5px 15px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-family: "Poppins", sans-serif;
    font-weight: 500;
    font-size: 0.9rem;
    text-decoration: none;
    transition: background-color 0.6s ease;
    box-shadow: 0 0px 1px 1px black;
    display: flex;
    align-items: center;
    justify-content: center;
    &:hover {
        background-color: #fdfdfd;
    }
}
</style>
