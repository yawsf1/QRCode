<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import LoginLayout from "../../Layouts/LoginLayout.vue";
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
    <div class="loginPage">
        <!-- Background -->
        <div class="bg">
            <div class="gridPattern"></div>
            <div class="glowOrb orb1"></div>
            <div class="glowOrb orb2"></div>
        </div>

        <!-- Card -->
        <div class="card">
            <!-- Back link -->
            <a :href="route('home')" class="backLink">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path
                        d="M9 2L4 7l5 5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
                Accueil
            </a>

            <!-- Brand -->
            <div class="brand">
                <div class="logoMark">
                    <span class="material-symbols-rounded">qr_code_2</span>
                </div>
                <span class="logoText">QR<span class="thin">Coded</span></span>
            </div>

            <h1 class="title">Connexion</h1>
            <p class="subtitle">Accédez à votre espace de gestion</p>

            <form class="form" @submit.prevent="login">
                <div class="field">
                    <label for="email">Email</label>
                    <div class="inputWrap" :class="{ hasError: errors.email }">
                        <span class="material-symbols-rounded inputIcon"
                            >mail</span
                        >
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            placeholder="vous@entreprise.ma"
                            autocomplete="email"
                        />
                    </div>
                    <p v-if="errors.email" class="error">{{ errors.email }}</p>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div
                        class="inputWrap"
                        :class="{ hasError: errors.password }"
                    >
                        <span class="material-symbols-rounded inputIcon"
                            >lock</span
                        >
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                        />
                    </div>
                    <p v-if="errors.password" class="error">
                        {{ errors.password }}
                    </p>
                </div>

                <button
                    class="submitBtn"
                    type="submit"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Se connecter</span>
                    <span v-else class="loadingRow">
                        <span class="spinner"></span>
                        Connexion en cours…
                    </span>
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap");

.loginPage {
    --bg: #0a0a0f;
    --surface: #111118;
    --surface2: #16161f;
    --border: rgba(255, 255, 255, 0.08);
    --border-strong: rgba(255, 255, 255, 0.14);
    --border-focus: rgba(79, 124, 255, 0.6);
    --text-primary: #f0f0f8;
    --text-secondary: #8888aa;
    --text-muted: #55556a;
    --accent: #4f7cff;
    --accent-dim: rgba(79, 124, 255, 0.12);
    --error: #ff6b6b;
    --error-dim: rgba(255, 107, 107, 0.1);

    font-family: "Sora", sans-serif;
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: var(--bg);
    position: relative;
    overflow: hidden;
}

* {
    box-sizing: border-box;
}

/* Background effects */
.bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.gridPattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(
        ellipse 70% 70% at 50% 50%,
        black 0%,
        transparent 100%
    );
}

.glowOrb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    &.orb1 {
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, #4f7cff 0%, transparent 70%);
        top: -200px;
        right: -150px;
        opacity: 0.12;
    }
    &.orb2 {
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, #a78bfa 0%, transparent 70%);
        bottom: -100px;
        left: -100px;
        opacity: 0.1;
    }
}

/* Card */
.card {
    position: relative;
    width: 100%;
    max-width: 420px;
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 20px;
    padding: 36px 32px;
    box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.02),
        0 32px 64px rgba(0, 0, 0, 0.5),
        0 0 60px rgba(79, 124, 255, 0.06);
}

/* Back link */
.backLink {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    text-decoration: none;
    margin-bottom: 28px;
    transition: color 0.15s;
    &:hover {
        color: var(--text-secondary);
    }
}

/* Brand */
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 28px;

    .logoMark {
        width: 34px;
        height: 34px;
        background: var(--accent);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        span {
            font-size: 18px;
            color: #fff;
        }
    }

    .logoText {
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: var(--text-primary);
    }
    .thin {
        font-weight: 300;
        color: var(--text-secondary);
    }
}

/* Heading */
.title {
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.8px;
    color: var(--text-primary);
    margin: 0 0 6px;
}

.subtitle {
    font-size: 13.5px;
    color: var(--text-secondary);
    margin: 0 0 28px;
    line-height: 1.5;
}

/* Form */
.form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 7px;

    label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--text-secondary);
    }
}

.inputWrap {
    display: flex;
    align-items: center;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition:
        border-color 0.2s,
        box-shadow 0.2s;
    overflow: hidden;

    &:focus-within {
        border-color: var(--border-focus);
        box-shadow: 0 0 0 3px rgba(79, 124, 255, 0.1);
    }

    &.hasError {
        border-color: rgba(255, 107, 107, 0.4);
        &:focus-within {
            border-color: rgba(255, 107, 107, 0.7);
            box-shadow: 0 0 0 3px var(--error-dim);
        }
    }

    .inputIcon {
        font-size: 17px;
        color: var(--text-muted);
        padding: 0 0 0 14px;
        flex-shrink: 0;
        user-select: none;
    }

    input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        padding: 13px 14px;
        font-family: "Sora", sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);

        &::placeholder {
            color: var(--text-muted);
            font-weight: 400;
        }

        /* hide browser autofill yellow */
        &:-webkit-autofill,
        &:-webkit-autofill:hover,
        &:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text-primary);
            -webkit-box-shadow: 0 0 0px 1000px var(--surface2) inset;
            transition: background-color 5000s ease-in-out 0s;
        }
    }
}

.error {
    font-size: 12px;
    color: var(--error);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 5px;
    &::before {
        content: "!";
        width: 14px;
        height: 14px;
        background: var(--error-dim);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: var(--error);
        flex-shrink: 0;
    }
}

/* Submit button */
.submitBtn {
    width: 100%;
    height: 46px;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: "Sora", sans-serif;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.02em;
    cursor: pointer;
    margin-top: 4px;
    transition:
        background 0.15s,
        transform 0.1s,
        box-shadow 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(79, 124, 255, 0.3);

    &:hover:not(:disabled) {
        background: #6b93ff;
        box-shadow: 0 6px 28px rgba(79, 124, 255, 0.45);
    }

    &:active:not(:disabled) {
        transform: scale(0.98);
    }

    &:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
}

.loadingRow {
    display: flex;
    align-items: center;
    gap: 10px;
}

.spinner {
    width: 15px;
    height: 15px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */
@media (max-width: 480px) {
    .card {
        padding: 28px 20px;
        border-radius: 16px;
    }
}
</style>
