<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, onUnmounted } from "vue";

const form = useForm({});
const page = usePage();
const user = computed(() => page.props.auth.user);
const isScrolled = ref(false);

function logout() {
    form.post(route("logout"));
}

const handleScroll = () => {
    isScrolled.value = window.scrollY > 12;
};

onMounted(() => window.addEventListener("scroll", handleScroll));
onUnmounted(() => window.removeEventListener("scroll", handleScroll));
</script>

<template>
    <header class="navbar" :class="{ scrolled: isScrolled }">
        <div class="navInner">
            <!-- Brand -->
            <Link href="/" class="brand">
                <div class="logoMark">
                    <span class="material-symbols-rounded">qr_code_2</span>
                </div>
                <span class="logoText">QR<span class="thin">Coded</span></span>
            </Link>

            <!-- Actions -->
            <div class="navActions">
                <button
                    v-if="user"
                    class="logoutBtn"
                    @click="logout"
                    :disabled="form.processing"
                >
                    <span class="material-symbols-rounded">logout</span>
                    Se déconnecter
                </button>
            </div>
        </div>
    </header>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@300;600;700&display=swap");

.navbar {
    background: rgba(10, 10, 15, 0.85);

    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60px;
    z-index: 10000;
    display: flex;
    align-items: center;
    transition:
        background 0.25s ease,
        border-color 0.25s ease,
        backdrop-filter 0.25s ease;
    border-bottom: 1px solid transparent;

    &.scrolled {
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom-color: rgba(255, 255, 255, 0.08);
    }
}

.navInner {
    width: 100%;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* BRAND */
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;

    .logoMark {
        width: 32px;
        height: 32px;
        background: #4f7cff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;

        span {
            font-size: 17px;
            color: #fff;
        }
    }

    &:hover .logoMark {
        background: #6b93ff;
    }

    .logoText {
        font-family: "Sora", sans-serif;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: #f0f0f8;
    }

    .thin {
        font-weight: 300;
        color: #8888aa;
    }
}

/* ACTIONS */
.navActions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logoutBtn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    height: 34px;
    padding: 0 14px;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: #8888aa;
    cursor: pointer;
    transition:
        border-color 0.15s,
        color 0.15s,
        background 0.15s;

    span {
        font-size: 16px;
    }

    &:hover {
        border-color: rgba(255, 80, 80, 0.35);
        color: #ff7070;
        background: rgba(255, 80, 80, 0.06);
    }

    &:active {
        transform: scale(0.97);
    }

    &:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
}
</style>
