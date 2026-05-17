<template>
    <div class="home">
        <div class="container">
            <span class="badge">
                <span class="material-symbols-rounded">auto_awesome</span>
                Plateforme RH moderne
            </span>

            <h1>
                Gérez vos équipes<br />
                avec <span class="accent">simplicité</span>
            </h1>
            <p v-if="user">{{ user.role }}</p>
            <p>
                Une solution complète pour administrer vos employés, suivre vos
                départements et piloter votre organisation depuis un seul
                endroit.
            </p>

            <div class="buttonContainer">
                <MainLink
                    v-if="!user"
                    :link="route('user.login')"
                    text="Commencer"
                />

                <MainLink
                    v-else-if="user && user.role === 'super_admin'"
                    :link="route('super-admin.dashboard')"
                    text="Dashboard"
                />

                <MainLink
                    v-else-if="user && user.role === 'admin'"
                    :link="route('admin.dashboard')"
                    text="Dashboard"
                />

                <MainLink
                    v-else-if="user && user.role === 'employe'"
                    :link="route('employe.dashboard')"
                    text="Dashboard"
                />

                <SecondaryLink href="/" text="En savoir plus" />
            </div>

            <div class="imgContainer">
                <div class="windowBar">
                    <span class="dot red"></span>
                    <span class="dot amber"></span>
                    <span class="dot green"></span>
                    <span class="urlBar">app.votre-domaine.ma/dashboard</span>
                </div>
                <div class="screenBody">
                    <img
                        src="/public/images/website-example-justinmind-768x492.png"
                        alt="Aperçu du tableau de bord"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import MainLink from "../components/Links/MainLink.vue";
import SecondaryLink from "../components/Links/SecondaryLink.vue";
import { route } from "ziggy-js";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<style scoped lang="scss">
.home {
    width: 100%;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 64px 24px;
    background: #ffffff;
}

.container {
    max-width: 640px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    border-radius: 99px;
    background: #f4f4f5;
    color: #3f3f46;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 24px;
    border: 1px solid #e4e4e7;

    span {
        font-size: 14px;
        color: #71717a;
    }
}

h1 {
    font-size: 42px;
    font-weight: 700;
    color: #09090b;
    text-align: center;
    line-height: 1.2;
    letter-spacing: 0em;
    margin-bottom: 16px;

    .accent {
        color: #71717a;
    }
}

p {
    font-size: 15px;
    color: #71717a;
    text-align: center;
    line-height: 1.75;
    margin-bottom: 32px;
}

.buttonContainer {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 48px;
    flex-wrap: wrap;
}

.imgContainer {
    width: 100%;
    border-radius: 16px;
    border: 1px solid #e4e4e7;
    overflow: hidden;
    background: #ffffff;
}

.windowBar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    background: #fafafa;
    border-bottom: 1px solid #e4e4e7;

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;

        &.red {
            background: #ef4444;
        }
        &.amber {
            background: #f59e0b;
        }
        &.green {
            background: #22c55e;
        }
    }

    .urlBar {
        flex: 1;
        margin: 0 8px;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 11px;
        color: #a1a1aa;
    }
}

.screenBody {
    width: 100%;
    height: 320px;
    overflow: hidden;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
        display: block;
    }
}

/* MOBILE */
@media (max-width: 640px) {
    .home {
        padding: 40px 16px;
        align-items: flex-start;
    }

    h1 {
        font-size: 28px;
    }

    p {
        font-size: 14px;
    }
}
</style>
