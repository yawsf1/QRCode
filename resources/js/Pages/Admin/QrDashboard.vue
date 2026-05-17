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
    min-height: calc(100vh - 50px);
}

.sidebar {
    width: 220px;
    flex-shrink: 0;
    background: #ffffff;
    border-right: 1px solid #f1f5f9;
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
    color: #94a3b8;
    padding: 0 8px 12px;
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
    color: #0f172a;
    cursor: pointer;
    transition: background 0.15s ease;
    text-align: left;
    width: 100%;

    span {
        font-size: 18px;
        color: #94a3b8;
    }

    &:hover {
        background: #f8fafc;
        span {
            color: #0f172a;
        }
    }

    &.active {
        background: #f1f5f9;
        span {
            color: #0f172a;
        }
    }
}

.content {
    flex: 1;
    background: #f8fafc;
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
    color: #0f172a;
    margin: 0;
}

.pageSubtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

/* WIDER THAN TALLER QR CARD ENGINEERING */
.qr-dashboard-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 50vh;
    background: #f8fafc;
    width: 100%;
}

.qr-card.wide-format {
    display: flex;
    flex-direction: row; /* Arranges elements side-by-side */
    align-items: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    padding: 32px;
    width: 100%;
    max-width: 680px; /* Increased width bounds */
    gap: 36px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}

.qr-graphic-side {
    flex-shrink: 0;
}

.qr-viewport {
    background: #ffffff;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    display: flex;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01);
}

.qr-info-side {
    flex: 1;
    display: flex;
    flex-direction: column;
    text-align: left; /* Changes text-alignment to match landscape profile */
}

.badge-row {
    margin-bottom: 12px;
}

.status-badge {
    font-size: 11px;
    font-weight: 700;
    color: #059669;
    text-transform: uppercase;
    background: #d1fae5;
    padding: 4px 10px;
    border-radius: 20px;
    letter-spacing: 0.05em;
}

h2 {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 8px 0;
}

.subtitle {
    font-size: 14px;
    color: #64748b;
    line-height: 1.5;
    margin: 0 0 24px 0;
}

.security-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #94a3b8;
    border-top: 1px solid #f1f5f9;
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
    }
    .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        padding: 12px;
        border-right: none;
        border-bottom: 1px solid #f1f5f9;
    }
    .sidebarLabel {
        display: none;
    }
    .content {
        padding: 20px 16px;
    }
    .qr-card.wide-format {
        flex-direction: column; /* Soft fallback to vertical stack on mobile phones */
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
