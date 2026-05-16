<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { route } from "ziggy-js";
import MainButton from "../../components/Buttons/MainButton.vue";
import SmallMainLink from "../../components/Links/SmallMainLink.vue";
import SmallSecondaryLink from "../../components/Links/SmallSecondaryLink.vue";

const form = useForm({
    nom: "",
    prenom: "",
    email: "",
    departement: "",
    telephone: "",

    heure_debut: "",
    heure_fin: "",
    jours_ouvres: [],
    tolerance_retard: "",
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

function register() {
    form.post(route("employe.register"), {
        onSuccess: () => {
            form.reset();
        },
    });
}

defineProps({
    employe: Object,
});
</script>

<template>
    <div class="pageContainer">
        <div class="card">
            <div class="topSection">
                <SmallSecondaryLink
                    :link="route('admin.dashboard')"
                    text="← Retour"
                />
            </div>
            <h2 class="title">Modifier d'employé : {{ employe.prenom }}</h2>
            <p class="subtitle">Créer un nouveau compte d'employé</p>

            <form class="form" @submit.prevent="register">
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
                            :placeholder="employe.telephone"
                        />
                        <p v-if="errors.telephone" class="error">
                            {{ errors.telephone }}
                        </p>
                    </div>

                    <div class="field">
                        <label
                            >Heure de début ({{ employe.horaire.heure_debut }})
                            <span class="required">*</span></label
                        >
                        <input type="time" v-model="form.heure_debut" />
                        <p v-if="errors.heure_debut" class="error">
                            {{ errors.heure_debut }}
                        </p>
                    </div>

                    <div class="field">
                        <label
                            >Heure de fin ({{ employe.horaire.heure_fin }})<span
                                class="required"
                                >*</span
                            ></label
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
                            :placeholder="employe.horaire.tolerance_retard"
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
                                    'is-checked':
                                        employe.horaire.jours_ouvres.includes(
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

                <MainButton
                    type="submit"
                    :disabled="form.processing"
                    :text="form.processing ? 'Ajout en cours...' : 'Ajouter'"
                />
            </form>
        </div>
    </div>
</template>

<style scoped lang="scss">
.pageContainer {
    width: 100%;
    min-height: calc(100vh - 50px);
    flex-direction: column;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background: #ffffff;
    position: relative;
}
.topSection {
    width: 100%;
    max-width: 520px;
    display: flex;
    justify-content: flex-end;
    margin-bottom: 10px;
    position: absolute;
    top: 20px;
    right: 20px;
}
.card {
    width: 100%;
    max-width: 520px;
    background: #ffffff;
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    position: relative;
}

.title {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 5px;
}

.subtitle {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 10px;
}

/* FORM */
.form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

/* FIELD */
.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.field.full {
    grid-column: span 2;
}

/* INPUTS */
input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    outline: none;
    font-size: 14px;
    transition: 0.2s ease;
    background: #fff;
}

input:focus {
    border-color: #2563eb;
}

.error {
    font-size: 12px;
    color: #dc2626;
}

/* CUSTOM CHECKBOX PILLETS */
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
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;

    &:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    &.is-checked {
        background: #e0f2fe;
        border-color: #0284c7;
        color: #0369a1;
    }
}

.hiddenCheckbox {
    display: none;
}

/* BUTTON */
button {
    margin-top: 8px;
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: none;
    background: #2563eb;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}

button:hover {
    background: #1d4ed8;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

label {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 4px;
}

.required {
    color: #dc2626;
    font-weight: 700;
}

/* MOBILE */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: span 1;
    }

    .card {
        padding: 18px;
    }
}
</style>
