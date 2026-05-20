<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const form = useForm({});

function logout() {
    if (form.processing) {
        return;
    }
    form.post(route("logout"));
}
</script>

<template>
    <button
        type="button"
        class="sidebarLogoutBtn"
        :disabled="form.processing"
        @click="logout"
    >
        <span class="material-symbols-rounded">logout</span>
        Déconnexion
    </button>
</template>

<style scoped lang="scss">
.sidebarLogoutBtn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    width: 100%;
    margin-top: auto;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: transparent;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #8888aa;
    cursor: pointer;
    transition: all 0.15s ease;

    span {
        font-size: 17px;
    }

    &:hover:not(:disabled) {
        border-color: rgba(255, 107, 107, 0.3);
        color: #ff6b6b;
        background: rgba(255, 107, 107, 0.12);
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
}
</style>
