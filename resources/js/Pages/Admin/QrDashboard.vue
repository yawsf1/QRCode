<script setup>
import { onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import QRCodeVue3 from "qrcode-vue3";
import AdminSidebar from "../../components/Layout/AdminSidebar.vue";
import DashboardMobileNav from "../../components/Layout/DashboardMobileNav.vue";
import { useUiStore } from "../../stores/ui";

const ui = useUiStore();

const props = defineProps({
    qrToken: String,
    expiresAt: String,
});

let refreshInterval = null;

onMounted(() => {
    refreshInterval = setInterval(() => {
        router.post(
            route("admin.qr.refresh"),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ["qrToken", "expiresAt"],
            },
        );
    }, 12000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>

<template>
    <div class="dashboard">
        <div
            v-if="ui.mobileSidebarOpen"
            class="sidebarBackdrop"
            @click="ui.closeMobileSidebar()"
        ></div>

        <AdminSidebar active="qr" :open="ui.mobileSidebarOpen" />

        <main class="content">
            <div class="pageHeader">
                <div class="headerRow">
                    <div>
                        <h1 class="pageTitle">Borne de Pointage</h1>
                        <p class="pageSubtitle">
                            Génération en direct des accès sécurisés de présence
                        </p>
                    </div>
                    <DashboardMobileNav />
                </div>
            </div>

            <div class="qr-dashboard-wrapper">
                <div class="qr-card wide-format">
                    <div class="qr-graphic-side">
                        <div class="qr-viewport">
                            <QRCodeVue3
                                :width="220"
                                :height="220"
                                :value="props.qrToken"
                                :key="props.qrToken"
                                :qr-options="{ errorCorrectionLevel: 'H' }"
                                :background-options="{ color: '#ffffff' }"
                                :dots-options="{
                                    type: 'square',
                                    color: '#0f172a',
                                }"
                                class="qr-svg"
                            />
                        </div>
                    </div>

                    <div class="qr-info-side">
                        <div class="badge-row">
                            <span class="status-badge">● Borne en Direct</span>
                        </div>
                        <h2>Scanner pour pointer l'arrivée</h2>
                        <p class="subtitle">
                            Les employés ouvrent QRCoded sur leur smartphone et
                            scannent ce code (un pointage par jour). Le QR est
                            renouvelé automatiquement.
                        </p>

                        <div class="security-footer">
                            <span class="material-symbols-rounded animate-spin"
                                >refresh</span
                            >
                            <small
                                >Actualisation automatique toutes les 12 s (code valide ~15 s)</small
                            >
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap");

.dashboard {
    font-family: "Sora", sans-serif;
    display: flex;
    width: 100%;
    min-height: 100vh;
    background: #0a0a0f;
    color: #f0f0f8;
}

.sidebarBackdrop {
    display: none;
}

.content {
    flex: 1;
    padding: 32px 40px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.pageHeader {
    margin-bottom: 8px;
}

.headerRow {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

.pageTitle {
    font-size: 22px;
    font-weight: 800;
    margin: 0 0 4px;
}

.pageSubtitle {
    font-size: 13px;
    color: #8888aa;
    margin: 0;
}

.qr-dashboard-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
}

.qr-card.wide-format {
    display: flex;
    gap: 40px;
    background: #111118;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 40px;
    max-width: 800px;
    width: 100%;
}

.qr-graphic-side {
    flex-shrink: 0;
}

.qr-viewport {
    background: #fff;
    padding: 16px;
    border-radius: 12px;
}

.qr-info-side {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 12px;

    h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .subtitle {
        color: #8888aa;
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
    }
}

.status-badge {
    color: #22c97a;
    font-size: 12px;
    font-weight: 700;
}

.security-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #55556a;
    margin-top: 8px;

    small {
        font-size: 12px;
    }
}

.animate-spin {
    animation: spin 2s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 768px) {
    .sidebarBackdrop {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 40;
    }

    :deep(.adminSidebar) {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: min(280px, 85vw);
        z-index: 50;
        transform: translateX(-100%);
        transition: transform 0.25s ease;

        &.open {
            transform: translateX(0);
        }
    }

    .content {
        padding: 20px 16px;
    }

    .qr-card.wide-format {
        flex-direction: column;
        padding: 24px;
        align-items: center;
        text-align: center;

        .qr-info-side {
            align-items: center;
        }
    }
}
</style>
