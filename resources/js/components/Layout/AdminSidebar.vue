<script setup>
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import AppBrand from "./AppBrand.vue";
import DashboardSidebarLogout from "./DashboardSidebarLogout.vue";

defineProps({
    active: {
        type: String,
        default: "dashboard",
    },
    open: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <aside class="adminSidebar" :class="{ open }">
        <div class="sidebarMain">
            <AppBrand class="sidebarBrand" />

            <p class="sidebarLabel">Administrateur</p>

            <button
                class="navBtn"
                :class="{ active: active === 'dashboard' }"
                @click="router.visit(route('admin.dashboard'))"
            >
                <span class="material-symbols-rounded">dashboard</span>
                Tableau de bord
            </button>

            <button
                class="navBtn"
                :class="{ active: active === 'register' }"
                @click="router.visit(route('employe.register.form'))"
            >
                <span class="material-symbols-rounded">person_add</span>
                Ajouter un employé
            </button>

            <button
                class="navBtn"
                :class="{ active: active === 'list' }"
                @click="router.visit(route('employe.list'))"
            >
                <span class="material-symbols-rounded">group</span>
                Afficher les employés
            </button>

            <button
                class="navBtn"
                :class="{ active: active === 'qr' }"
                @click="router.visit(route('admin.qr.show'))"
            >
                <span class="material-symbols-rounded">qr_code_scanner</span>
                Générer Borne QR
            </button>
        </div>

        <DashboardSidebarLogout />
    </aside>
</template>

<style scoped lang="scss">
.adminSidebar {
    width: 260px;
    flex-shrink: 0;
    background: #111118;
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 32px 18px;
    min-height: 100vh;
    z-index: 10;
}

.sidebarMain {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.sidebarBrand {
    margin-bottom: 36px;
}

.sidebarLabel {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #55556a;
    padding: 0 12px 10px;
    margin: 0;
}

.navBtn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid transparent;
    background: transparent;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #8888aa;
    cursor: pointer;
    transition: all 0.15s ease;
    text-align: left;
    width: 100%;

    span {
        font-size: 18px;
        color: #55556a;
        transition: color 0.15s;
    }

    &:hover {
        background: #16161f;
        color: #f0f0f8;

        span {
            color: #f0f0f8;
        }
    }

    &.active {
        background: rgba(79, 124, 255, 0.12);
        color: #f0f0f8;
        border-color: rgba(79, 124, 255, 0.2);

        span {
            color: #4f7cff;
        }
    }
}
</style>
