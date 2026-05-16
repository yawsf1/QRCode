<script setup>
import { route } from "ziggy-js";
import { router } from "@inertiajs/vue3";
</script>

<template>
    <div class="dashboard">
        <aside class="sidebar">
            <p class="sidebarLabel">Super admin</p>

            <button
                class="navBtn"
                :class="{ active: route().current('super-admin.dashboard') }"
                @click="router.visit(route('super-admin.dashboard'))"
            >
                <span class="material-symbols-rounded">dashboard</span>
                Tableau de bord
            </button>

            <button
                class="navBtn"
                :class="{ active: route().current('admin.register.form') }"
                @click="router.visit(route('admin.register.form'))"
            >
                <span class="material-symbols-rounded">person_add</span>
                Ajouter un admin
            </button>

            <button
                class="navBtn"
                :class="{ active: route().current('admin.list') }"
                @click="router.visit(route('admin.list'))"
            >
                <span class="material-symbols-rounded">group</span>
                Afficher les admins
            </button>
        </aside>

        <main class="content">
            <div class="pageHeader">
                <h1 class="pageTitle">Tableau de bord</h1>
                <p class="pageSubtitle">Vue d'ensemble de la plateforme</p>
            </div>

            <slot />
        </main>
    </div>
</template>

<style lang="scss" scoped>
.main {
    width: 100%;
    min-height: calc(100vh - 50px);
    margin-top: 50px;
    display: flex;
    flex-direction: column;
}
</style>
