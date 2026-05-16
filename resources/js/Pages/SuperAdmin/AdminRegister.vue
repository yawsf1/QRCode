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
    password: "",
    telephone: "",
    departement: "",
});

const errors = computed(() => usePage().props.errors);

function register() {
    form.post(route("admin.register"), {
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>
    <div class="pageContainer">
        <div class="card">
            <div class="topSection">
                <SmallSecondaryLink
                    :link="route('super-admin.dashboard')"
                    text="← Retour"
                />
            </div>
            <h2 class="title">Ajouter un admin</h2>
            <p class="subtitle">Créer un nouveau compte administrateur</p>

            <form class="form" @submit.prevent="register">
                <div class="grid">
                    <div class="field">
                        <label>Nom <span class="required">*</span></label>
                        <input
                            type="text"
                            v-model="form.nom"
                            placeholder="Nom"
                        />
                        <p v-if="errors.nom" class="error">{{ errors.nom }}</p>
                    </div>

                    <div class="field">
                        <label>Prénom <span class="required">*</span></label>
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
                            placeholder="Email"
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
                            placeholder="Mot de passe"
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
                            v-model="form.Departement"
                            placeholder="Nom d'entreprise"
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
