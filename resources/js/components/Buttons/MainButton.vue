<script setup>
defineProps({
    text: String,
});
</script>

<template>
    <button class="mainButton">{{ text }}</button>
</template>

<style lang="scss" scoped>
.mainButton {
    background-color: black;
    color: #f8fafc;
    padding: 5px 15px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-family: "Poppins", sans-serif;
    font-weight: 500;
    transition: background-color 0.6s ease;
    box-shadow: 0 0px 1px 1px black;
    &:hover {
        background-color: rgb(61, 61, 61);
    }
}
</style>
