<script setup>
import { route } from "ziggy-js";
import MainLink from "../../components/Links/MainLink.vue";
import { ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import SecondaryButton from "../../components/Buttons/SecondaryButton.vue";

defineProps({
    employes: Object,
});

const search = ref("");
const sortBy = ref("recent");

let timeout = null;

watch([search, sortBy], () => {
    clearTimeout(timeout);

    timeout = setTimeout(() => {
        router.get(
            route("employe.list"),
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
    employeId: null,
});

const showDeleteModal = ref(false);

function deleteEmploye() {
    form.delete(route("admin.employe.delete", form.employeId), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
}

function confirmDelete(employeId) {
    form.employeId = employeId;
    showDeleteModal.value = true;
}

function updateEmploye(id) {
    router.get(route("employe.update.form", id));
}

function showEmploye(id) {
    router.get(route("employe.show", id));
}
</script>

<template>
    <div class="containerOfList">
        <div v-if="showDeleteModal" class="modalOverlay">
            <div class="modalBox">
                <span class="material-symbols-rounded modalIcon">warning</span>
                <h3>Confirmer la suppression</h3>
                <p>
                    Êtes-vous sûr de vouloir supprimer cet employé ? Cette
                    action est irréversible.
                </p>
                <div class="modalActions">
                    <button class="btnCancel" @click="showDeleteModal = false">
                        Annuler
                    </button>
                    <button class="btnConfirm" @click="deleteEmploye">
                        Oui, supprimer
                    </button>
                </div>
            </div>
        </div>

        <div class="topSection">
            <div class="titleSection">
                <h2>Liste des employés</h2>
                <p>{{ employes.total }} employé(s) enregistré(s)</p>
            </div>

            <div class="actionsSection">
                <div class="search">
                    <input
                        type="text"
                        placeholder="Rechercher un employé..."
                        v-model="search"
                    />
                </div>

                <div class="filtering">
                    <SecondaryButton
                        :class="{ 'is-active': sortBy === 'name' }"
                        text="Par Nom"
                        @click="sortBy = 'name'"
                    />
                    <SecondaryButton
                        :class="{ 'is-active': sortBy === 'recent' }"
                        text="Plus récent"
                        @click="sortBy = 'recent'"
                    />
                </div>

                <div class="navigationGroup">
                    <MainLink
                        :link="route('employe.register.form')"
                        text="Ajouter un employé"
                    />
                    <MainLink
                        :link="route('admin.dashboard')"
                        text="← Retour au dashboard"
                    />
                </div>
            </div>
        </div>

        <div class="tableWrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nom Complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Date création</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="employe in employes.data"
                        :key="employe.id"
                        class="adminRow"
                    >
                        <td data-label="Nom Complet" class="primaryText">
                            {{ employe.nom }} {{ employe.prenom }}
                        </td>
                        <td data-label="Email" class="secondaryText">
                            {{ employe.email }}
                        </td>
                        <td data-label="Téléphone" class="secondaryText">
                            {{ employe.telephone || "—" }}
                        </td>
                        <td data-label="Date création" class="secondaryText">
                            {{ formatDate(employe.created_at) }}
                        </td>
                        <td data-label="Statut">
                            <span
                                class="statusBadge"
                                :class="
                                    employe.est_actif ? 'active' : 'inactive'
                                "
                            >
                                {{ employe.est_actif ? "Actif" : "Inactif" }}
                            </span>
                        </td>
                        <td data-label="Actions" class="text-right actionsCell">
                            <button
                                class="tableViewBtn"
                                @click="showEmploye(employe.id)"
                            >
                                <span class="material-symbols-rounded"
                                    >visibility</span
                                >
                                Voir
                            </button>

                            <button
                                class="tableEditBtn"
                                @click="updateEmploye(employe.id)"
                            >
                                <span class="material-symbols-rounded"
                                    >edit</span
                                >
                                Modifier
                            </button>

                            <button
                                class="tableDeleteBtn"
                                @click="confirmDelete(employe.id)"
                            >
                                <span class="material-symbols-rounded"
                                    >delete</span
                                >
                                Supprimer
                            </button>
                        </td>
                    </tr>

                    <tr v-if="employes.data.length === 0">
                        <td colspan="6" class="emptyState">
                            Aucun employé trouvé
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="pagination"
            v-if="employes.links && employes.links.length > 0"
        >
            <button
                v-for="link in employes.links"
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
    --success-dim: rgba(16, 185, 129, 0.12);

    font-family: "Sora", sans-serif;
    width: 100%;
    flex-grow: 1;
    padding: 30px 40px;
    background: var(--bg);
    color: var(--text-primary);
    display: flex;
    flex-direction: column;
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
        color: #ffffff;

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
    margin-bottom: 24px;
}

.titleSection {
    display: flex;
    flex-direction: column;
    gap: 6px;

    h2 {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin: 0;
        color: var(--text-primary);
    }

    p {
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 500;
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
    padding: 10px 16px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--surface2);
    outline: none;
    transition: all 0.15s ease;
    font-size: 13px;
    font-family: "Sora", sans-serif;
    color: var(--text-primary);

    &::placeholder {
        color: var(--text-muted);
    }

    &:focus {
        border-color: var(--accent);
    }
}

.filtering {
    display: flex;
    gap: 8px;

    :deep(.secondaryButton) {
        height: 38px;
        border-radius: 10px;
        font-size: 13px;

        &.is-active {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-primary);
            border-color: var(--text-muted);
        }
    }
}

.navigationGroup {
    display: flex;
    align-items: center;
    gap: 12px;
}

.tableWrapper {
    background: var(--surface);
    border-radius: 16px;
    border: 1px solid var(--border-strong);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    margin-bottom: 24px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

th {
    text-align: left;
    padding: 18px 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border-strong);
    background: rgba(0, 0, 0, 0.1);
}

.adminRow {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;

    &:last-child {
        border-bottom: none;
    }

    &:hover {
        background: rgba(255, 255, 255, 0.015);
    }

    td {
        padding: 16px 20px;
        font-size: 13.5px;
        vertical-align: middle;

        &.primaryText {
            color: var(--text-primary);
            font-weight: 600;
        }

        &.secondaryText {
            color: var(--text-secondary);
            font-weight: 500;
        }

        &.text-right {
            text-align: right;
        }
    }
}

.actionsCell {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}

.tableViewBtn,
.tableEditBtn,
.tableDeleteBtn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    font-family: "Sora", sans-serif;
    transition: all 0.15s ease;
    border: 1px solid var(--border);
    background: var(--surface2);

    span {
        font-size: 15px;
    }
}

.tableViewBtn {
    color: var(--text-secondary);

    &:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        border-color: var(--border-strong);
    }
}

.tableEditBtn {
    background: var(--accent);
    border: none;
    color: #ffffff;

    &:hover {
        background: var(--accent-hover);
    }
}

.tableDeleteBtn {
    background: transparent;
    border-color: rgba(255, 107, 107, 0.2);
    color: var(--error);

    &:hover {
        background: var(--error-dim);
        border-color: var(--error);
    }
}

.statusBadge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    background: rgba(255, 107, 107, 0.1);
    color: var(--error);

    &.active {
        background: var(--success-dim);
        color: var(--success);
    }
}

.emptyState {
    text-align: center;
    padding: 60px 40px;
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 500;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: auto;
    padding-bottom: 10px;
}

.pagination button {
    padding: 8px 14px;
    border: 1px solid var(--border);
    background: var(--surface);
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    font-family: "Sora", sans-serif;
    color: var(--text-secondary);
    transition: all 0.15s ease;

    &:hover {
        background: var(--surface2);
        color: var(--text-primary);
        border-color: var(--border-strong);
    }

    &.active {
        background: var(--text-primary);
        color: var(--bg);
        border-color: var(--text-primary);
    }

    &:disabled {
        opacity: 0.25;
        cursor: not-allowed;
        border-color: var(--border);
        background: var(--surface);
        color: var(--text-muted);
    }
}

@media (max-width: 1024px) {
    .topSection {
        flex-direction: column;
        align-items: flex-start;
    }
    .actionsSection {
        width: 100%;
        justify-content: space-between;
    }
}

@media (max-width: 768px) {
    .containerOfList {
        padding: 20px;
    }

    .actionsSection {
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
    }

    .search input {
        width: 100%;
    }

    .filtering {
        justify-content: flex-start;
        :deep(.secondaryButton) {
            flex: 1;
        }
    }

    .navigationGroup {
        flex-direction: column;
        align-items: stretch;
        :deep(a) {
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
    }

    .adminRow {
        padding: 20px 16px;
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 12px;
    }

    td {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0 !important;
        border: none !important;

        &::before {
            content: attr(data-label);
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.05em;
        }
    }

    .actionsCell {
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 12px;
        padding-top: 12px !important;
        border-top: 1px solid var(--border) !important;
        button {
            flex: 1;
            justify-content: center;
        }
    }
}
</style>
