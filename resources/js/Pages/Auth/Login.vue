<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import LoginLayout from "../../Layouts/LoginLayout.vue";
import MainButton from "../../components/Buttons/MainButton.vue";
import SmallSecondaryLink from "../../components/Links/SmallSecondaryLink.vue";
import { route } from "ziggy-js";

const page = usePage();
const errors = computed(() => page.props.errors);

const form = useForm({
    email: "",
    password: "",
});

function login() {
    form.post(route("user.login"));
}

defineOptions({
    layout: LoginLayout,
});
</script>

<template>
    <div class="pageContainer">
        <div class="card">
            <div class="topSection">
                <SmallSecondaryLink :link="route('home')" text="← Accueil" />
            </div>
            <h2 class="title">Connexion</h2>
            <p class="subtitle">Accéder à votre espace de gestion</p>

            <form class="form" @submit.prevent="login">
                <div class="grid">
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
                </div>

                <MainButton
                    type="submit"
                    :disabled="form.processing"
                    :text="
                        form.processing
                            ? 'Connexion en cours...'
                            : 'Se connecter'
                    "
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
    margin-bottom: 20px;
}

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

.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.field.full {
    grid-column: span 2;
}

input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    outline: none;
    font-size: 14px;
    transition: 0.2s ease;
    background: #fff;

    &:focus {
        border-color: #2563eb;
    }
}

.error {
    font-size: 12px;
    color: #dc2626;
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
