<script setup>
import { ref, reactive, computed } from "vue";
const num = ref(0);
function add() {
    num.value++;
}
function subtract() {
    num.value--;
}
</script>

<template>
    <div class="home">
        <div class="container">
            <h1>{{ num }}</h1>
        </div>
    </div>
    <button @click="add">+</button>
    <button @click="subtract">-</button>
</template>

<style scoped lang="scss"></style>
