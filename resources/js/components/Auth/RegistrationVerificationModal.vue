<script setup>
import { computed } from "vue";

const props = defineProps({
    open: { type: Boolean, default: false },
    email: { type: String, default: "" },
    verificationCode: { type: String, default: "" },
    loading: { type: Boolean, default: false },
    sendingCode: { type: Boolean, default: false },
    error: { type: String, default: "" },
});

const emit = defineEmits([
    "close",
    "confirm",
    "update:verificationCode",
]);

const isBusy = computed(() => props.loading || props.sendingCode);

function onBackdropClick() {
    if (!isBusy.value) {
        emit("close");
    }
}

function onCodeInput(event) {
    const value = event.target.value.replace(/\D/g, "").slice(0, 6);
    emit("update:verificationCode", value);
}

function onSubmit() {
    if (!isBusy.value) {
        emit("confirm");
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-fade">
            <div
                v-if="open"
                class="modalOverlay"
                role="dialog"
                aria-modal="true"
                aria-labelledby="verification-modal-title"
                @click.self="onBackdropClick"
            >
                <div class="modalCard" @click.stop>
                    <button
                        type="button"
                        class="closeBtn"
                        aria-label="Fermer"
                        :disabled="isBusy"
                        @click="emit('close')"
                    >
                        <span class="material-symbols-rounded">close</span>
                    </button>

                    <div class="modalHeader">
                        <span class="material-symbols-rounded icon"
                            >mark_email_read</span
                        >
                        <div>
                            <h2 id="verification-modal-title" class="title">
                                Vérification de l'e-mail
                            </h2>
                            <p class="hint">
                                Un code à 6 chiffres a été envoyé à
                                <strong>{{ email }}</strong
                                >. Saisissez-le pour créer le compte.
                            </p>
                        </div>
                    </div>

                    <form class="modalForm" @submit.prevent="onSubmit">
                        <label for="modal_verification_code" class="codeLabel"
                            >Code de vérification</label
                        >
                        <input
                            id="modal_verification_code"
                            type="text"
                            inputmode="numeric"
                            maxlength="6"
                            :value="verificationCode"
                            placeholder="000000"
                            autocomplete="one-time-code"
                            :disabled="isBusy"
                            @input="onCodeInput"
                        />

                        <p v-if="error" class="errorMsg" role="alert">
                            {{ error }}
                        </p>

                        <button
                            type="submit"
                            class="confirmBtn"
                            :disabled="
                                isBusy || verificationCode.length !== 6
                            "
                        >
                            {{
                                loading
                                    ? "Création en cours…"
                                    : "Valider et créer le compte"
                            }}
                        </button>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped lang="scss">
.modalOverlay {
    position: fixed;
    inset: 0;
    z-index: 10050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(5, 5, 10, 0.72);
    backdrop-filter: blur(6px);
}

.modalCard {
    position: relative;
    width: 100%;
    max-width: 420px;
    padding: 28px 28px 24px;
    border-radius: 18px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: #111118;
    box-shadow:
        0 24px 64px rgba(0, 0, 0, 0.55),
        0 0 0 1px rgba(79, 124, 255, 0.08);
}

.closeBtn {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.04);
    color: #8888aa;
    cursor: pointer;
    transition: all 0.15s ease;

    span {
        font-size: 20px;
    }

    &:hover:not(:disabled) {
        color: #f0f0f8;
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.08);
    }

    &:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
}

.modalHeader {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    padding-right: 36px;
    margin-bottom: 22px;

    .icon {
        font-size: 28px;
        color: #4f7cff;
        flex-shrink: 0;
    }
}

.title {
    margin: 0 0 6px;
    font-family: "Sora", sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: #f0f0f8;
}

.hint {
    margin: 0;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    line-height: 1.55;
    color: #8888aa;

    strong {
        color: #f0f0f8;
        font-weight: 600;
    }
}

.modalForm {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.codeLabel {
    font-family: "Sora", sans-serif;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #8888aa;
}

input {
    width: 100%;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: #16161f;
    color: #f0f0f8;
    font-family: "Sora", sans-serif;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 0.35em;
    text-align: center;
    outline: none;
    box-sizing: border-box;

    &:focus {
        border-color: rgba(79, 124, 255, 0.6);
        box-shadow: 0 0 0 3px rgba(79, 124, 255, 0.12);
    }

    &:disabled {
        opacity: 0.6;
    }
}

.errorMsg {
    margin: 0;
    font-family: "Sora", sans-serif;
    font-size: 12px;
    font-weight: 500;
    color: #ff6b6b;
    padding: 10px 12px;
    border-radius: 8px;
    background: rgba(255, 107, 107, 0.1);
    border: 1px solid rgba(255, 107, 107, 0.25);
}

.confirmBtn {
    margin-top: 4px;
    width: 100%;
    padding: 14px 16px;
    border: none;
    border-radius: 10px;
    background: #4f7cff;
    color: #fff;
    font-family: "Sora", sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s ease;

    &:hover:not(:disabled) {
        background: #6b93ff;
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active .modalCard,
.modal-fade-leave-active .modalCard {
    transition:
        transform 0.22s ease,
        opacity 0.22s ease;
}

.modal-fade-enter-from .modalCard,
.modal-fade-leave-to .modalCard {
    transform: translateY(12px) scale(0.98);
    opacity: 0;
}
</style>
