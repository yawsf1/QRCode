<script setup>
import DashboardSidebarLogout from "../../components/Layout/DashboardSidebarLogout.vue";
import { onMounted, onUnmounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { Html5Qrcode } from "html5-qrcode";

const page = usePage();
const scannerId = "mobile-camera-stream";
let html5QrcodeInstance = null;
const isScanning = ref(false);
const isSubmitting = ref(false);
const feedbackMessage = ref("");

const stopCamera = async () => {
    if (html5QrcodeInstance?.isScanning) {
        await html5QrcodeInstance.stop();
    }
    isScanning.value = false;
};

const startCamera = () => {
    if (isSubmitting.value || page.props.checkedInToday) {
        return;
    }

    feedbackMessage.value = "";
    isScanning.value = true;

    html5QrcodeInstance
        .start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
            },
            (decodedText) => {
                handleSuccessfulScan(decodedText);
            },
            () => {},
        )
        .catch(() => {
            feedbackMessage.value = "Erreur d'accès à la caméra.";
            isScanning.value = false;
        });
};

const handleSuccessfulScan = async (tokenString) => {
    if (isSubmitting.value) {
        return;
    }

    isSubmitting.value = true;
    await stopCamera();

    router.post(
        route("employe.checkin"),
        { token: tokenString },
        {
            preserveScroll: false,
            onFinish: () => {
                isSubmitting.value = false;
            },
        },
    );
};

onMounted(() => {
    if (page.props.checkedInToday) {
        router.replace(route("employe.dashboard"));
        return;
    }

    html5QrcodeInstance = new Html5Qrcode(scannerId);
    startCamera();
});

onUnmounted(() => {
    stopCamera();
});
</script>

<template>
    <div class="scanner-mobile-viewport">
        <header class="scannerTopBar">
            <button
                type="button"
                class="backBtn"
                @click="router.visit(route('employe.dashboard'))"
            >
                <span class="material-symbols-rounded">arrow_back</span>
                Retour
            </button>
            <DashboardSidebarLogout />
        </header>

        <div class="scanner-header">
            <h3>Enregistrement Présence</h3>
            <p>Cadrez le code QR de la borne dans l'espace indiqué</p>
        </div>

        <div class="camera-card">
            <div :id="scannerId" class="video-canvas"></div>

            <div
                v-if="isSubmitting || (!isScanning && feedbackMessage)"
                class="feedback-overlay"
            >
                <p class="status-text">
                    {{
                        isSubmitting
                            ? "Enregistrement en cours..."
                            : feedbackMessage
                    }}
                </p>
                <button
                    v-if="!isSubmitting && feedbackMessage"
                    class="retry-btn"
                    @click="startCamera"
                >
                    Réessayer
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.scanner-mobile-viewport {
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #0f172a;
    min-height: 100vh;
    color: #ffffff;
}

.scannerTopBar {
    width: 100%;
    max-width: 360px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;

    :deep(.sidebarLogoutBtn) {
        width: auto;
        margin-top: 0;
        padding: 8px 12px;
        font-size: 12px;
    }
}

.backBtn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    color: #94a3b8;
    font-family: "Sora", sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 12px;
    cursor: pointer;

    span {
        font-size: 16px;
    }

    &:hover {
        color: #fff;
        border-color: rgba(255, 255, 255, 0.3);
    }
}

.scanner-header {
    text-align: center;
    margin: 40px 0 24px 0;

    h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    p {
        font-size: 13px;
        color: #94a3b8;
    }
}

.camera-card {
    position: relative;
    width: 100%;
    max-width: 360px;
    aspect-ratio: 1;
    background: #000000;
    border-radius: 20px;
    overflow: hidden;
    border: 2px solid #334155;
}

.video-canvas {
    width: 100% !important;
    height: 100% !important;

    :deep(video) {
        object-fit: cover !important;
    }
}

.feedback-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
    text-align: center;

    .status-text {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 16px;
    }
}

.retry-btn {
    background: #ffffff;
    color: #0f172a;
    border: none;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
}

@media (max-width: 480px) {
    .scanner-mobile-viewport {
        padding: 16px;
    }

    .scanner-header {
        margin: 24px 0 16px;
    }
}
</style>
