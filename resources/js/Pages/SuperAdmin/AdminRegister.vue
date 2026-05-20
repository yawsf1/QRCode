<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import { route } from "ziggy-js";
import RegistrationVerificationModal from "../../components/Auth/RegistrationVerificationModal.vue";
import MainButton from "../../components/Buttons/MainButton.vue";
import SmallSecondaryLink from "../../components/Links/SmallSecondaryLink.vue";
import { useRegistrationVerification } from "../../composables/useRegistrationVerification";

const form = useForm({
    nom: "",
    prenom: "",
    email: "",
    verification_code: "",
    password: "",
    telephone: "",
    departement: "",
});

const {
    showModal,
    modalError,
    startVerification,
    closeModal,
    confirmRegistration,
} = useRegistrationVerification({
    sendRoute: route("register.admin.verification.send"),
    registerRoute: route("admin.register"),
});

const errors = computed(() => usePage().props.errors);

watch(
    () => form.email,
    () => {
        if (showModal.value) {
            closeModal(form);
        }
    },
);

const submitLabel = computed(() => {
    if (form.processing && !showModal.value) {
        return "Vérification du formulaire…";
    }

    if (form.processing && showModal.value) {
        return "Création en cours…";
    }

    return "Ajouter l'administrateur";
});

function register() {
    startVerification(form);
}
</script>

<template>
    <div class="pageContainer">
        <div class="card" :class="{ isDimmed: showModal }">
            <div class="topSection">
                <SmallSecondaryLink
                    :link="route('super-admin.dashboard')"
                    text="← Retour"
                />
            </div>

            <h2 class="title">Ajouter un admin</h2>
            <p class="subtitle">
                Créer un nouveau compte administrateur d'entreprise
            </p>

            <form class="form" @submit.prevent="register">
                <div class="grid">
                    <div class="field">
                        <label>Nom <span class="required">*</span></label>
                        <input
                            type="text"
                            v-model="form.nom"
                            placeholder="Nom"
                            :disabled="showModal"
                        />
                        <p v-if="errors.nom" class="error">{{ errors.nom }}</p>
                    </div>

                    <div class="field">
                        <label>Prénom <span class="required">*</span></label>
                        <input
                            type="text"
                            v-model="form.prenom"
                            placeholder="Prénom"
                            :disabled="showModal"
                        />
                        <p v-if="errors.prenom" class="error">
                            {{ errors.prenom }}
                        </p>
                    </div>

                    <div class="field full">
                        <label>Email <span class="required">*</span></label>
                        <input
                            type="email"
                            v-model="form.email"
                            placeholder="adresse@exemple.com"
                            autocomplete="email"
                            :disabled="showModal"
                        />
                        <p v-if="errors.email" class="error">
                            {{ errors.email }}
                        </p>
                    </div>

                    <div class="field full">
                        <label
                            >Mot de passe <span class="required">*</span></label
                        >
                        <input
                            type="password"
                            v-model="form.password"
                            placeholder="••••••••"
                            :disabled="showModal"
                        />
                        <p v-if="errors.password" class="error">
                            {{ errors.password }}
                        </p>
                    </div>

                    <div class="field full">
                        <label
                            >Nom d'entreprise
                            <span class="required">*</span></label
                        >
                        <input
                            type="text"
                            v-model="form.departement"
                            placeholder="Structure ou Entreprise SAS"
                            :disabled="showModal"
                        />
                        <p v-if="errors.departement" class="error">
                            {{ errors.departement }}
                        </p>
                    </div>

                    <div class="field full">
                        <label>Téléphone</label>
                        <input
                            type="text"
                            v-model="form.telephone"
                            placeholder="N° de ligne directe"
                            :disabled="showModal"
                        />
                        <p v-if="errors.telephone" class="error">
                            {{ errors.telephone }}
                        </p>
                    </div>
                </div>

                <div class="formActions">
                    <MainButton
                        type="submit"
                        :disabled="form.processing"
                        :text="submitLabel"
                    />
                </div>
            </form>
        </div>

        <RegistrationVerificationModal
            :open="showModal"
            :email="form.email"
            :verification-code="form.verification_code"
            :loading="form.processing"
            :sending-code="false"
            :error="modalError || errors.verification_code"
            @close="closeModal(form)"
            @confirm="confirmRegistration(form)"
            @update:verification-code="
                (value) => (form.verification_code = value)
            "
        />
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap");

.pageContainer {
    --bg: #0a0a0f;
    --surface: #111118;
    --surface2: #16161f;
    --border: rgba(255, 255, 255, 0.06);
    --border-strong: rgba(255, 255, 255, 0.12);
    --text-primary: #f0f0f8;
    --text-secondary: #8888aa;
    --text-muted: #55556a;
    --accent: #4f7cff;
    --accent-hover: #3b66eb;
    --error: #ff6b6b;

    font-family: "Sora", sans-serif;
    width: 100%;
    min-height: calc(100vh - 60px);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
    background: var(--bg);
    position: relative;
    box-sizing: border-box;
}

.card {
    width: 100%;
    max-width: 540px;
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    transition:
        opacity 0.25s ease,
        filter 0.25s ease;

    &.isDimmed {
        opacity: 0.35;
        pointer-events: none;
        filter: blur(1px);
    }
}

.topSection {
    margin-bottom: 24px;
    display: flex;
    justify-content: flex-start;
}

.title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 6px 0;
    letter-spacing: -0.5px;
}

.subtitle {
    font-size: 13.5px;
    color: var(--text-secondary);
    margin: 0 0 28px 0;
    line-height: 1.5;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.field.full {
    grid-column: span 2;
}

label {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 4px;
}

.required {
    color: var(--error);
    font-weight: 700;
}

input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--surface2);
    color: var(--text-primary);
    outline: none;
    font-size: 14px;
    font-family: "Sora", sans-serif;
    transition: all 0.15s ease;
    box-sizing: border-box;

    &::placeholder {
        color: var(--text-muted);
        opacity: 0.8;
    }

    &:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 1px var(--accent);
    }

    &:disabled {
        cursor: not-allowed;
    }
}

.error {
    font-size: 12px;
    color: var(--error);
    margin: 4px 0 0 0;
    font-weight: 500;
}

.formActions {
    margin-top: 12px;

    :deep(button) {
        width: 100%;
        padding: 14px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        font-family: "Sora", sans-serif;
        background: var(--accent);
        color: white;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease;

        &:hover {
            background: var(--accent-hover);
        }

        &:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    }
}

@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: span 1;
    }

    .card {
        padding: 24px 20px;
    }
}
</style>
