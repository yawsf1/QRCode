<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { Html5Qrcode } from "html5-qrcode";

const scannerId = "mobile-camera-stream";
let html5QrcodeInstance = null;
const isScanning = ref(false);
const feedbackMessage = ref("");

onMounted(() => {
    html5QrcodeInstance = new Html5Qrcode(scannerId);
    startCamera();
});

const startCamera = () => {
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
            (errorMessage) => {},
        )
        .catch((err) => {
            feedbackMessage.value = "Erreur d'accès à la caméra.";
            isScanning.value = false;
        });
};

const handleSuccessfulScan = (tokenString) => {
    if (html5QrcodeInstance && html5QrcodeInstance.isScanning) {
        html5QrcodeInstance.stop().then(() => {
            isScanning.value = false;

            router.post(
                route("employe.checkin"),
                { token: tokenString },
                {
                    onSuccess: () => {
                        const flashMessage =
                            usePage().props.flash?.flash_message ||
                            "Opération terminée";
                        feedbackMessage.value = flashMessage;
                    },
                    onError: () => {
                        feedbackMessage.value =
                            "Une erreur serveur s'est produite.";
                    },
                },
            );
        });
    }
};

onUnmounted(() => {
    if (html5QrcodeInstance && html5QrcodeInstance.isScanning) {
        html5QrcodeInstance.stop();
    }
});
</script>

<template>
    <div class="scanner-mobile-viewport">
        <div class="scanner-header">
            <h3>Enregistrement Présence</h3>
            <p>Cadrez le code QR de la borne dans l'espace indiqué</p>
        </div>

        <div class="camera-card">
            <div :id="scannerId" class="video-canvas"></div>

            <div v-if="!isScanning && feedbackMessage" class="feedback-overlay">
                <p class="status-text">{{ feedbackMessage }}</p>
                <button class="retry-btn" @click="startCamera">
                    Relancer le scan
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
    background: #0f172a; /* Tech dark aesthetic looks great for camera viewports */
    min-height: 100vh;
    color: #ffffff;
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
    ::v-deep(video) {
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
</style>
