<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";
import MainButton from "../Buttons/MainButton.vue";
import { computed } from "vue";
import MainLayout from "../../Layouts/MainLayout.vue";
import LogoutButton from "../Buttons/LogoutButton.vue";

const form = useForm({});
const page = usePage();
const user = computed(() => page.props.auth.user);
function logout() {
    form.post(route("logout"));
}
</script>

<template>
    <div class="navBarContainer">
        <ul class="navBar">
            <li class="navBarItem">
                <Link href="/"> ScanQR </Link>
            </li>
            <li v-if="user" class="navBarItem">
                <LogoutButton @click="logout" text="Se déconnecter" />
            </li>
        </ul>
    </div>
</template>

<style scoped lang="scss">
.navBarContainer {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 50px;
    background-color: #f8f8f8;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;

    .navBar {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
        width: 90%;
        align-items: center;
        justify-content: space-between;

        .navBarItem {
            margin: 0 0;
            a {
                color: #333;
                text-decoration: none;
                font-weight: 600;
                font-size: 16px;
            }
        }
    }
    form {
        margin-top: 80px;
    }
}
</style>
