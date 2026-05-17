<script setup>
import { onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import QRCodeVue3 from "qrcode-vue3";

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
    if (refreshInterval) clearInterval(refreshInterval);
});
</script>

<template>
    <div class="dashboard">
        <aside class="sidebar">
            <p class="sidebarLabel">Super admin</p>

            <button
                class="navBtn"
                @click="router.visit(route('admin.dashboard'))"
            >
                <span class="material-symbols-rounded">dashboard</span>
                Tableau de bord
            </button>

            <button
                class="navBtn"
                @click="router.visit(route('employe.register.form'))"
            >
                <span class="material-symbols-rounded">person_add</span>
                Ajouter un employé
            </button>

            <button class="navBtn" @click="router.visit(route('employe.list'))">
                <span class="material-symbols-rounded">group</span>
                Afficher les employés
            </button>

            <button
                class="navBtn active"
                @click="router.visit(route('admin.qr.show'))"
            >
                <span class="material-symbols-rounded">qr_code_scanner</span>
                Générer Borne QR
            </button>
        </aside>

        <main class="content">
            <div class="pageHeader">
                <h1 class="pageTitle">Borne de Pointage</h1>
                <p class="pageSubtitle">
                    Génération en direct des accès sécurisés de présence
                </p>
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
                        <h2>Scanner pour pointer</h2>
                        <p class="subtitle">
                            Présentez votre application mobile face à l'écran
                            pour enregistrer votre présence instantanément.
                        </p>

                        <div class="security-footer">
                            <span class="material-symbols-rounded animate-spin"
                                >refresh</span
                            >
                            <small
                                >Actualisation automatique active (12s)</small
                            >
                        </div>
                    </div>
                </div>
            </div>

            <slot />
        </main>
    </div>
</template>

<style scoped lang="scss">
/* Core Master Dashboard Rules */
.dashboard {
    display: flex;
    width: 100%;
    flex-grow: 1;
    min-height: calc(100vh - 60px);
    background: #0a0a0f;
}

.sidebar {
    width: 220px;
    flex-shrink: 0;
    background: #111118;
    border-right: 1px solid rgba(255, 255, 255, 0.06);
    display: flex;
    flex-direction: column;
    padding: 24px 12px;
    gap: 4px;
}

.sidebarLabel {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #55556a;
    padding: 0 8px 12px;
    margin: 0;
}

.navBtn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 10px;
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 500;
    color: #8888aa;
    cursor: pointer;
    transition: all 0.15s ease;
    text-align: left;
    width: 100%;
    font-family: inherit;

    span {
        font-size: 18px;
        color: #55556a;
    }

    &:hover {
        background: #16161f;
        color: #f0f0f8;
        span {
            color: #8888aa;
        }
    }

    &.active {
        background: rgba(255, 255, 255, 0.08);
        color: #f0f0f8;
        span {
            color: #4f7cff;
        }
    }
}

.content {
    flex: 1;
    background: #0a0a0f;
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    overflow-y: auto;
}

.pageHeader {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.pageTitle {
    font-size: 22px;
    font-weight: 700;
    color: #f0f0f8;
    margin: 0;
}

.pageSubtitle {
    font-size: 14px;
    color: #8888aa;
    margin: 0;
}

/* WIDER THAN TALLER QR CARD ENGINEERING */
.qr-dashboard-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-grow: 1;
    background: #0a0a0f;
    width: 100%;
}

.qr-card.wide-format {
    display: flex;
    flex-direction: row;
    align-items: center;
    background: #111118;
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 24px;
    padding: 32px;
    width: 100%;
    max-width: 680px;
    gap: 36px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.qr-graphic-side {
    flex-shrink: 0;
}

.qr-viewport {
    background: #ffffff; /* Keeping the immediate frame white for pristine QR rendering scanner capability */
    padding: 12px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    display: flex;
}

.qr-info-side {
    flex: 1;
    display: flex;
    flex-direction: column;
    text-align: left;
}

.badge-row {
    margin-bottom: 12px;
}

.status-badge {
    font-size: 11px;
    font-weight: 700;
    color: #10b981;
    text-transform: uppercase;
    background: rgba(16, 185, 129, 0.12);
    padding: 4px 10px;
    border-radius: 20px;
    letter-spacing: 0.05em;
}

h2 {
    font-size: 22px;
    font-weight: 700;
    color: #f0f0f8;
    margin: 0 0 8px 0;
}

.subtitle {
    font-size: 14px;
    color: #8888aa;
    line-height: 1.5;
    margin: 0 0 24px 0;
}

.security-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #55556a;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    padding-top: 16px;

    small {
        font-size: 12px;
        font-weight: 500;
    }
    span {
        font-size: 16px;
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.animate-spin {
    animation: spin 3s linear infinite;
}

/* Responsive Adaptability Layout Handlers */
@media (max-width: 768px) {
    .dashboard {
        flex-direction: column;
        min-height: auto;
    }
    .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        padding: 12px;
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .sidebarLabel {
        display: none;
    }
    .content {
        padding: 20px 16px;
    }
    .qr-card.wide-format {
        flex-direction: column;
        text-align: center;
        padding: 24px;
        gap: 20px;

        .qr-info-side {
            text-align: center;
            align-items: center;
        }
        .security-footer {
            justify-content: center;
            width: 100%;
        }
    }
}
</style>
