<script setup>
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
</script>

<template>
    <Link :href="route('home')" class="brand">
        <div class="logoMark">
            <span class="material-symbols-rounded">qr_code_2</span>
        </div>
        <span class="logoText">QR<span class="thin">Coded</span></span>
    </Link>
</template>

<style scoped lang="scss">
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    padding-left: 6px;

    .logoMark {
        width: 30px;
        height: 30px;
        background: #4f7cff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;

        span {
            font-size: 16px;
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
</style>
