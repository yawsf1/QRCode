<script setup>
import { ref } from "vue";

defineProps({
    text: String,
});
</script>

<template>
    <button @click="rotate = !rotate" class="secondaryButton">
        {{ text }}
        <span
            :style="
                rotate
                    ? 'transform: rotate(180deg); transition: transform 0.3s ease;'
                    : 'transform: rotate(0deg); transition: transform 0.3s ease;'
            "
            class="material-symbols-rounded"
        >
            keyboard_arrow_down
        </span>
    </button>
</template>

<style lang="scss" scoped>
.secondaryButton {
    background-color: #eeeeee;
    color: black;
    padding: 5px 15px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-family: "Poppins", sans-serif;
    font-weight: 500;
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
