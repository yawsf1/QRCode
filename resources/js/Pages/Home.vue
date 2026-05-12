<template>
    <div class="home">
        <div class="container">
            <h1>Laravel + Inertia + Vue is working</h1>
            <p>Count: {{ counter.count }}</p>

            <div class="actions">
                <button @click="counter.increment">+</button>
                <button @click="counter.decrement">−</button>
                <button @click="counter.reset">Reset</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useCounterStore } from "@/stores/counter";

const counter = useCounterStore();
</script>

<style scoped lang="scss">
.home {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    p {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .actions {
        display: flex;
        gap: 1rem;

        button {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s ease;

            &:hover {
                background: #1d4ed8;
                transform: translateY(-1px);
            }
        }
    }
}
</style>
