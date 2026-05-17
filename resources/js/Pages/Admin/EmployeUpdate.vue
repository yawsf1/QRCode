<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { route } from "ziggy-js";
import MainButton from "../../components/Buttons/MainButton.vue";
import SmallMainLink from "../../components/Links/SmallMainLink.vue";
import SmallSecondaryLink from "../../components/Links/SmallSecondaryLink.vue";

const props = defineProps({
    employe: Object,
});

const form = useForm({
    nom: props.employe.nom || "",
    prenom: props.employe.prenom || "",
    email: props.employe.email || "",
    departement: props.employe.departement || "",
    telephone: props.employe.telephone || "",

    heure_debut: props.employe.horaire?.heure_debut || "",
    heure_fin: props.employe.horaire?.heure_fin || "",
    jours_ouvres: props.employe.horaire?.jours_ouvres || [],
    tolerance_retard: props.employe.horaire?.tolerance_retard ?? "",
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

function update() {
    form.put(route("admin.employe.update", props.employe.id));
}
</script>

<template>
    <div class="pageContainer">
        <div class="card">
            <div class="topSection">
                <SmallSecondaryLink
                    :link="route('employe.list')"
                    text="← Retour au registre"
                />
            </div>

            <h2 class="title">Modifier l'employé : {{ employe.prenom }}</h2>
            <p class="subtitle">
                Mettre à jour les informations de profil et de régulation
                horaire
            </p>

            <form class="form" @submit.prevent="update">
                <div class="grid">
                    <div class="field">
                        <label>Nom <span class="required">*</span></label>
                        <input
                            type="text"
                            v-model="form.nom"
                            :placeholder="employe.nom"
                        />
                        <p v-if="errors.nom" class="error">{{ errors.nom }}</p>
                    </div>

                    <div class="field">
                        <label>Prénom <span class="required">*</span></label>
                        <input
                            type="text"
                            v-model="form.prenom"
                            :placeholder="employe.prenom"
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
                            :placeholder="employe.email"
                        />
                        <p v-if="errors.email" class="error">
                            {{ errors.email }}
                        </p>
                    </div>

                    <div class="field full">
                        <label
                            >Département <span class="required">*</span></label
                        >
                        <input
                            type="text"
                            v-model="form.departement"
                            :placeholder="employe.departement"
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
                            :placeholder="employe.telephone || 'Non renseigné'"
                        />
                        <p v-if="errors.telephone" class="error">
                            {{ errors.telephone }}
                        </p>
                    </div>

                    <div class="field">
                        <label>
                            Heure de début
                            <span class="context-label"
                                >({{
                                    employe.horaire?.heure_debut || "—"
                                }})</span
                            >
                            <span class="required">*</span>
                        </label>
                        <input type="time" v-model="form.heure_debut" />
                        <p v-if="errors.heure_debut" class="error">
                            {{ errors.heure_debut }}
                        </p>
                    </div>

                    <div class="field">
                        <label>
                            Heure de fin
                            <span class="context-label"
                                >({{ employe.horaire?.heure_fin || "—" }})</span
                            >
                            <span class="required">*</span>
                        </label>
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
                            :placeholder="
                                employe.horaire?.tolerance_retard ?? '0'
                            "
                            min="0"
                            max="60"
                        />
                        <p v-if="errors.tolerance_retard" class="error">
                            {{ errors.tolerance_retard }}
                        </p>
                    </div>

                    <div class="field full">
                        <label
                            >Jours ouvrés <span class="required">*</span></label
                        >
                        <div class="checkboxGroup">
                            <label
                                v-for="day in daysOfWeek"
                                :key="day.value"
                                class="checkboxLabel"
                                :class="{
                                    'is-checked': form.jours_ouvres.includes(
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

                <div class="formActions">
                    <MainButton
                        type="submit"
                        :disabled="form.processing"
                        :text="
                            form.processing
                                ? 'Modification en cours...'
                                : 'Enregistrer les modifications'
                        "
                    />
                </div>
            </form>
        </div>
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
    --success: #10b981;

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

.topSection {
    margin-bottom: 24px;
    display: flex;
    justify-content: flex-start;
}

.card {
    width: 100%;
    max-width: 560px;
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
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

/* FORM */
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

/* FIELD */
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
    gap: 6px;
}

.context-label {
    font-size: 11.5px;
    color: var(--text-secondary);
    font-weight: 400;
}

.required {
    color: var(--error);
    font-weight: 700;
}

/* INPUTS */
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

    /* Dark variant setup for time elements */
    &[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(0.9);
        cursor: pointer;
    }
}

.error {
    font-size: 12px;
    color: var(--error);
    margin: 4px 0 0 0;
    font-weight: 500;
}

/* CUSTOM CHECKBOX PILLS */
.checkboxGroup {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
}

.checkboxLabel {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 20px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;

    &:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--text-muted);
        color: var(--text-primary);
    }

    &.is-checked {
        background: rgba(79, 124, 255, 0.15);
        border-color: var(--accent);
        color: var(--text-primary);
        font-weight: 600;
    }
}

.hiddenCheckbox {
    display: none;
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

/* MOBILE RESPONSIVE CLOSURES */
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
