<script setup>
import { route } from "ziggy-js";
import MainLink from "../../components/Links/MainLink.vue";
import { ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import SecondaryDropButton from "../../components/Buttons/SecondaryDropButton.vue";
import MainButton from "../../components/Buttons/MainButton.vue";
import SecondaryButton from "../../components/Buttons/SecondaryButton.vue";

defineProps({
    admins: Object,
});

const search = ref("");
const sortBy = ref("recent");

let timeout = null;

watch([search, sortBy], () => {
    clearTimeout(timeout);

    timeout = setTimeout(() => {
        router.get(
            route("admin.list"),
            {
                search: search.value,
                sortBy: sortBy.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 300);
});

function formatDate(date) {
    return new Date(date).toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
}

function goToPage(url) {
    if (!url) return;

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    });
}

const form = useForm({
    adminId: null,
});

const showDeleteModal = ref(false);

function deleteAdmin() {
    form.delete(route("admin.delete", form.adminId), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
}

function deleteAdminWithId(adminId) {
    form.adminId = adminId;
    showDeleteModal.value = true;
}
</script>

<template>
    <div class="containerOfList">
        <div v-if="showDeleteModal" class="modalOverlay">
            <div class="modalBox">
                <span class="material-symbols-rounded modalIcon">warning</span>
                <h3>Confirmer la suppression</h3>
                <p>
                    Êtes-vous sûr de vouloir supprimer cet administrateur ?
                    Cette action est irréversible et révoquera tous ses accès
                    associés.
                </p>
                <div class="modalActions">
                    <button class="btnCancel" @click="showDeleteModal = false">
                        Annuler
                    </button>
                    <button class="btnConfirm" @click="deleteAdmin">
                        Oui, supprimer
                    </button>
                </div>
            </div>
        </div>

        <div class="topSection">
            <div class="titleSection">
                <h2>Liste des admins</h2>
                <p class="counterText">
                    {{ admins.total }} administrateur(s) inscrit(s)
                </p>
            </div>

            <div class="actionsSection">
                <div class="search">
                    <input
                        type="text"
                        placeholder="Rechercher un admin..."
                        v-model="search"
                    />
                </div>

                <div class="filtering">
                    <SecondaryButton
                        text="Nb. employés"
                        :class="{ 'filter-active': sortBy === 'employees' }"
                        @click="sortBy = 'employees'"
                    />

                    <SecondaryButton
                        text="Plus récent"
                        :class="{ 'filter-active': sortBy === 'recent' }"
                        @click="sortBy = 'recent'"
                    />
                </div>

                <div class="linkCluster">
                    <MainLink
                        :link="route('admin.register.form')"
                        text="Ajouter un admin"
                    />

                    <MainLink
                        :link="route('super-admin.dashboard')"
                        text="← Tableau de bord"
                    />
                </div>
            </div>
        </div>

        <div class="tableWrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Date création</th>
                        <th>Employés</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="admin in admins.data"
                        :key="admin.id"
                        class="adminRow"
                    >
                        <td data-label="Nom" class="text-highlight">
                            {{ admin.nom }}
                        </td>
                        <td data-label="Prénom">{{ admin.prenom }}</td>
                        <td data-label="Email" class="text-secondary-dim">
                            {{ admin.email }}
                        </td>
                        <td data-label="Téléphone">
                            {{ admin.telephone || "—" }}
                        </td>

                        <td data-label="Date création">
                            {{ formatDate(admin.created_at) }}
                        </td>

                        <td data-label="Employés" class="text-center-desktop">
                            <span class="countBadge">{{
                                admin.employes_count
                            }}</span>
                        </td>

                        <td data-label="Statut">
                            <span
                                class="status"
                                :class="admin.est_actif ? 'active' : 'inactive'"
                            >
                                {{ admin.est_actif ? "Actif" : "Inactif" }}
                            </span>
                        </td>

                        <td data-label="Action" class="text-right">
                            <button
                                class="tableDeleteBtn"
                                @click="deleteAdminWithId(admin.id)"
                            >
                                <span class="material-symbols-rounded"
                                    >delete</span
                                >
                                Supprimer
                            </button>
                        </td>
                    </tr>

                    <tr v-if="admins.data.length === 0">
                        <td colspan="8" class="emptyState">
                            Aucun administrateur trouvé dans le système
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination" v-if="admins.data.length > 0">
            <button
                v-for="link in admins.links"
                :key="link.label"
                v-html="link.label"
                :disabled="!link.url"
                :class="{ active: link.active }"
                @click="goToPage(link.url)"
            />
        </div>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap");

.containerOfList {
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
    --error-dim: rgba(255, 107, 107, 0.1);
    --success: #10b981;
    --success-dim: rgba(16, 185, 129, 0.15);

    font-family: "Sora", sans-serif;
    width: 100%;
    min-height: calc(100vh - 60px);
    padding: 32px 40px;
    display: flex;
    flex-direction: column;
    gap: 28px;
    background: var(--bg);
    color: var(--text-primary);
    box-sizing: border-box;
}

.modalOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(5, 5, 8, 0.75);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modalBox {
    background: var(--surface);
    padding: 36px;
    border-radius: 16px;
    max-width: 440px;
    width: 90%;
    text-align: left;
    box-shadow: 0 24px 48px rgba(0, 0, 0, 0.5);
    border: 1px solid var(--border-strong);
    display: flex;
    flex-direction: column;

    .modalIcon {
        font-size: 32px;
        color: var(--error);
        margin-bottom: 16px;
        align-self: flex-start;
    }

    h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 12px 0;
        letter-spacing: -0.3px;
    }

    p {
        font-size: 13.5px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0 0 28px 0;
    }
}

.modalActions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    width: 100%;

    button {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: "Sora", sans-serif;
        transition: all 0.15s ease;
    }

    .btnConfirm {
        background: var(--error);
        color: white;
        &:hover {
            background: #e55b5b;
        }
    }

    .btnCancel {
        background: var(--surface2);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        &:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }
    }
}

.topSection {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    border-bottom: 1px solid var(--border);
    padding-bottom: 20px;
}

.titleSection {
    display: flex;
    flex-direction: column;
    gap: 4px;

    h2 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .counterText {
        color: var(--text-secondary);
        font-size: 13.5px;
        margin: 0;
    }
}

.actionsSection {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.search input {
    width: 260px;
    padding: 11px 16px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-primary);
    outline: none;
    transition: all 0.15s ease;
    font-size: 13.5px;
    font-family: inherit;

    &::placeholder {
        color: var(--text-muted);
    }

    &:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 1px var(--accent);
    }
}

.filtering {
    display: flex;
    gap: 8px;

    :deep(button) {
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        padding: 10px 16px;
    }
}

.linkCluster {
    display: flex;
    gap: 12px;
    align-items: center;
}

.tableWrapper {
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    overflow-x: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    width: 100%;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 950px;
}

thead {
    background: var(--surface2);

    th {
        text-align: left;
        padding: 16px 20px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-strong);

        &.text-right {
            text-align: right;
        }
    }
}

tbody {
    .adminRow {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s ease;

        &:last-child {
            border-bottom: none;
        }

        &:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        td {
            padding: 16px 20px;
            font-size: 14px;
            color: var(--text-primary);
            vertical-align: middle;

            &.text-right {
                text-align: right;
            }

            &.text-center-desktop {
                text-align: left;
                padding-left: 32px;
            }
        }
    }
}

.text-highlight {
    font-weight: 600;
    color: var(--text-primary) !important;
}

.text-secondary-dim {
    color: var(--text-secondary) !important;
}

.countBadge {
    display: inline-flex;
    padding: 2px 8px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
}

.tableDeleteBtn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    color: var(--error);
    border: 1px solid rgba(255, 107, 107, 0.2);
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s ease;

    span {
        font-size: 16px;
    }

    &:hover {
        background: var(--error-dim);
        border-color: var(--error);
    }
}

.status {
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;

    &.active {
        background: var(--success-dim);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    &.inactive {
        background: var(--error-dim);
        color: var(--error);
        border: 1px solid rgba(255, 107, 107, 0.2);
    }
}

.emptyState {
    text-align: center;
    padding: 40px !important;
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 500;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.pagination button {
    padding: 8px 14px;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-secondary);
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-family: inherit;
    font-weight: 500;
    transition: all 0.15s ease;

    &:hover:not(:disabled) {
        background: var(--surface2);
        color: var(--text-primary);
        border-color: var(--text-muted);
    }

    &.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
        font-weight: 600;
    }

    &:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
}

@media (max-width: 850px) {
    .containerOfList {
        padding: 24px 16px;
    }

    .topSection {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .actionsSection {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .search input {
        width: 100%;
    }

    .filtering {
        width: 100%;

        :deep(button) {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
    }

    .linkCluster {
        width: 100%;
        flex-direction: column;

        :deep(a),
        :deep(button) {
            width: 100%;
            text-align: center;
        }
    }

    table {
        min-width: unset;
    }

    thead {
        display: none;
    }

    tbody,
    tr,
    td {
        display: block;
        width: 100%;
        box-sizing: border-box;
    }

    .adminRow {
        padding: 16px;
        border-bottom: 1px solid var(--border-strong) !important;

        &:last-child {
            border-bottom: none !important;
        }
    }

    td {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0 !important;
        border: none !important;
        text-align: left !important;

        &::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 12.5px;
            color: var(--text-secondary);
        }

        &.text-right {
            justify-content: space-between;
            margin-top: 4px;
        }

        &.text-center-desktop {
            padding-left: 0 !important;
        }
    }

    .tableDeleteBtn {
        width: auto;
    }
}
</style>
