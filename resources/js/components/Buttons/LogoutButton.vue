<script setup>
defineProps({
    text: String,
});
</script>

<template>
    <button class="mainButton">
        <span class="material-symbols-rounded">logout</span>{{ text }}
    </button>
</template>

<style lang="scss" scoped>
.mainButton {
    background-color: black;
    color: #f8fafc;
    padding: 5px 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-family: "Poppins", sans-serif;
    font-weight: 500;
    font-size: 0.8rem;
    transition: background-color 0.6s ease;
    box-shadow: 0 0px 1px 1px black;
    display: flex;
    align-items: center;
    gap: 10px;
    &:hover {
        background-color: rgb(61, 61, 61);
    }
}
</style>
