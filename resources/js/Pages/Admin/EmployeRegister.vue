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
    departement: "",
    telephone: "",

    heure_debut: "",
    heure_fin: "",
    jours_ouvres: [],
    tolerance_retard: "",
});

const {
    showModal,
    modalError,
    startVerification,
    closeModal,
    confirmRegistration,
} = useRegistrationVerification({
    sendRoute: route("register.employe.verification.send"),
    registerRoute: route("admin.employe.register"),
});

const daysOfWeek = [
    { value: "Lun", label: "Lundi" },
    { value: "Mar", label: "Mardi" },
    { value: "Mer", label: "Mercredi" },
    { value: "Jeu", label: "Jeudi" },
    { value: "Ven", label: "Vendredi" },
    { value: "Sam", label: "Samedi" },
    { value: "Dim", label: "Dimanche" },
];

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

    return "Ajouter l'employé";
});

function register() {
    startVerification(form);
}
</script>

<template>
    <div class="pageContainer">
        <div class="bg">
            <div class="gridPattern"></div>
            <div class="glowOrb orb1"></div>
            <div class="glowOrb orb2"></div>
        </div>

        <div class="scrollWorkspace">
            <div class="card" :class="{ isDimmed: showModal }">
                <div class="topSection">
                    <SmallSecondaryLink
                        :link="route('admin.dashboard')"
                        text="← Retour"
                    />
                </div>

                <h2 class="title">Ajouter un employé</h2>
                <p class="subtitle">Créer un nouveau compte d'employé</p>

                <form class="form" @submit.prevent="register">
                    <div class="grid">
                        <div class="field">
                            <label>Nom <span class="required">*</span></label>
                            <input
                                type="text"
                                v-model="form.nom"
                                placeholder="Nom"
                            />
                            <p v-if="errors.nom" class="error">
                                {{ errors.nom }}
                            </p>
                        </div>

                        <div class="field">
                            <label
                                >Prénom <span class="required">*</span></label
                            >
                            <input
                                type="text"
                                v-model="form.prenom"
                                placeholder="Prénom"
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
                            />
                            <p v-if="errors.email" class="error">
                                {{ errors.email }}
                            </p>
                        </div>

                        <div class="field full">
                            <label
                                >Mot de passe
                                <span class="required">*</span></label
                            >
                            <input
                                type="password"
                                v-model="form.password"
                                placeholder="Mot de passe"
                            />
                            <p v-if="errors.password" class="error">
                                {{ errors.password }}
                            </p>
                        </div>

                        <div class="field full">
                            <label
                                >Département
                                <span class="required">*</span></label
                            >
                            <input
                                type="text"
                                v-model="form.departement"
                                placeholder="Département"
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
                                placeholder="Téléphone"
                            />
                            <p v-if="errors.telephone" class="error">
                                {{ errors.telephone }}
                            </p>
                        </div>

                        <div class="field">
                            <label
                                >Heure de début
                                <span class="required">*</span></label
                            >
                            <input type="time" v-model="form.heure_debut" />
                            <p v-if="errors.heure_debut" class="error">
                                {{ errors.heure_debut }}
                            </p>
                        </div>

                        <div class="field">
                            <label
                                >Heure de fin
                                <span class="required">*</span></label
                            >
                            <input type="time" v-model="form.heure_fin" />
                            <p v-if="errors.heure_fin" class="error">
                                {{ errors.heure_fin }}
                            </p>
                        </div>

                        <div class="field full">
                            <label>Tolérance de retard (minutes)</label>
                            <input
                                type="number"
                                v-model="form.tolerance_retard"
                                placeholder="Ex: 15"
                                min="0"
                                max="60"
                            />
                            <p v-if="errors.tolerance_retard" class="error">
                                {{ errors.tolerance_retard }}
                            </p>
                        </div>

                        <div class="field full">
                            <label
                                >Jours ouvrés
                                <span class="required">*</span></label
                            >
                            <div class="checkboxGroup">
                                <label
                                    v-for="day in daysOfWeek"
                                    :key="day.value"
                                    class="checkboxLabel"
                                    :class="{
                                        'is-checked':
                                            form.jours_ouvres.includes(
                                                day.value,
                                            ),
                                    }"
                                >
                                    <input
                                        type="checkbox"
                                        :value="day.value"
                                        v-model="form.jours_ouvres"
                                        class="hiddenCheckbox"
                                    />
                                    {{ day.label }}
                                </label>
                            </div>
                            <p v-if="errors.jours_ouvres" class="error">
                                {{ errors.jours_ouvres }}
                            </p>
                        </div>
                    </div>

                    <div class="actionRow">
                        <MainButton
                            type="submit"
                            :disabled="form.processing"
                            :text="submitLabel"
                        />
                    </div>
                </form>
            </div>
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
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap");

.pageContainer {
    --bg: #0a0a0f;
    --surface: #111118;
    --surface2: #16161f;
    --border: rgba(255, 255, 255, 0.08);
    --border-strong: rgba(255, 255, 255, 0.14);
    --border-focus: rgba(79, 124, 255, 0.4);
    --text-primary: #f0f0f8;
    --text-secondary: #8888aa;
    --text-muted: #55556a;
    --accent: #4f7cff;
    --accent-dim: rgba(79, 124, 255, 0.12);
    --error: #ff6b6b;
    --error-dim: rgba(255, 107, 107, 0.1);

    font-family: "Sora", sans-serif;
    width: 100%;
    height: 100%; 
    background: var(--bg);
    position: relative;
    overflow: hidden; 
}

.bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
}

.gridPattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
    background-size: 50px 50px;
    mask-image: radial-gradient(
        ellipse 80% 80% at 50% 50%,
        black 0%,
        transparent 100%
    );
}

.glowOrb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    &.orb1 {
        width: 500px;
        height: 500px;
        background: radial-gradient(
            circle,
            rgba(79, 124, 255, 0.06) 0%,
            transparent 70%
        );
        top: -100px;
        right: -50px;
    }
    &.orb2 {
        width: 400px;
        height: 400px;
        background: radial-gradient(
            circle,
            rgba(167, 139, 250, 0.04) 0%,
            transparent 70%
        );
        bottom: -100px;
        left: -50px;
    }
}

.scrollWorkspace {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: flex-start; 
    padding: 40px 24px;
    overflow-y: auto;
    z-index: 10;
    position: relative;
}

.topSection {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.card {
    width: 100%;
    max-width: 600px;
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 20px;
    padding: 36px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    margin-bottom: 20px;
    transition:
        opacity 0.25s ease,
        filter 0.25s ease;

    &.isDimmed {
        opacity: 0.35;
        pointer-events: none;
        filter: blur(1px);
    }
}

.title {
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.6px;
    color: var(--text-primary);
    margin: 0 0 6px 0;
}

.subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0 0 32px 0;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
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
    font-size: 12px;
    font-weight: 700;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.required {
    color: var(--error);
    font-weight: 700;
}

input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid var(--border);
    background: var(--surface2);
    color: var(--text-primary);
    font-family: "Sora", sans-serif;
    font-size: 14px;
    font-weight: 500;
    outline: none;
    transition: all 0.15s ease;

    &::placeholder {
        color: var(--text-muted);
    }

    &:focus {
        border-color: var(--border-focus);
        box-shadow: 0 0 0 1px var(--border-focus);
    }

    &[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(0.9);
        cursor: pointer;
    }

    &[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;

        &::-webkit-outer-spin-button,
        &::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    }
}

.error {
    font-size: 11px;
    font-weight: 600;
    color: var(--error);
    margin: 4px 0 0 0;
}

.checkboxGroup {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
}

.checkboxLabel {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    border-radius: 20px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;

    &:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: var(--border-strong);
        color: var(--text-primary);
    }

    &.is-checked {
        background: var(--accent-dim);
        border-color: rgba(79, 124, 255, 0.4);
        color: var(--accent);
    }
}

.hiddenCheckbox {
    display: none;
}

.actionRow {
    margin-top: 12px;
    width: 100%;
}

@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: span 1;
    }

    .card {
        padding: 24px;
    }
}
</style>
