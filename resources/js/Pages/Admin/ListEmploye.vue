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
    form.delete(route("employe.delete", form.employeId), {
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
                    <button class="btnConfirm" @click="deleteEmploye">
                        Oui, supprimer
                    </button>
                    <button class="btnCancel" @click="showDeleteModal = false">
                        Annuler
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
                    <SecondaryButton text="Par Nom" @click="sortBy = 'name'" />

                    <SecondaryButton
                        text="Plus récent"
                        @click="sortBy = 'recent'"
                    />
                </div>

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
                        <td data-label="Nom Complet">
                            {{ employe.nom }} {{ employe.prenom }}
                        </td>
                        <td data-label="Email">{{ employe.email }}</td>
                        <td data-label="Téléphone">
                            {{ employe.telephone || "—" }}
                        </td>

                        <td data-label="Date création">
                            {{ formatDate(employe.created_at) }}
                        </td>

                        <td data-label="Statut">
                            <span
                                class="status"
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
                        <td colspan="10" class="emptyState">
                            Aucun employé trouvé
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination" v-if="employes.data.length > 0">
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
.containerOfList {
    width: 100%;
    min-height: calc(100vh - 50px);
    padding: 40px;
    background: #fafafa;
    color: #111111;
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.modalOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modalBox {
    background: #ffffff;
    padding: 40px;
    border-radius: 12px;
    max-width: 440px;
    width: 90%;
    text-align: left;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid #111111;
    display: flex;
    flex-direction: column;

    .modalIcon {
        font-size: 32px;
        color: #ef4444;
        margin-bottom: 16px;
        align-self: flex-start;
    }

    h3 {
        font-size: 18px;
        font-weight: 700;
        color: #111111;
        margin: 0 0 12px 0;
    }

    p {
        font-size: 13px;
        color: #555555;
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
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
    }

    .btnConfirm {
        background: #ef4444;
        color: #ffffff;

        &:hover {
            background: #dc2626;
        }
    }

    .btnCancel {
        background: #f3f4f6;
        color: #4b5563;

        &:hover {
            background: #e5e7eb;
        }
    }
}

.topSection {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    border-bottom: 1px solid #eeeeee;
    padding-bottom: 20px;
}

.titleSection {
    display: flex;
    flex-direction: column;
    gap: 4px;

    h2 {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.3px;
        margin: 0;
    }

    p {
        color: #666666;
        font-size: 13px;
        margin: 0;
    }
}

.actionsSection {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.search input {
    width: 260px;
    padding: 8px 16px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    outline: none;
    transition: 0.15s ease;
    font-size: 13px;
    color: #111111;

    &:focus {
        border-color: #111111;
    }
}

.filtering {
    display: flex;
    gap: 8px;
}

.tableWrapper {
    background: transparent;
    border-radius: 0;
    overflow-x: auto;
    box-shadow: none;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

th {
    text-align: left;
    padding: 16px 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: #999999;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #eeeeee;

    &.text-right {
        text-align: right;
    }
}

.adminRow {
    border-bottom: 1px solid #eeeeee;
    transition: background 0.15s ease;

    &:hover {
        background: #fdfdfd;
    }

    td {
        padding: 16px 12px;
        font-size: 14px;
        color: #222222;
        font-weight: 500;
        vertical-align: middle;

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
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid transparent;
    background: transparent;

    span {
        font-size: 16px;
    }
}

.tableViewBtn {
    border-color: #e5e7eb;
    color: #475569;

    &:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
}

.tableEditBtn {
    background: #111111;
    color: #ffffff;

    &:hover {
        background: #333333;
    }
}

.tableDeleteBtn {
    border-color: #e5e7eb;
    color: #ef4444;

    &:hover {
        background: #fef2f2;
        border-color: #fee2e2;
    }
}

.status {
    font-size: 12px;
    font-weight: 600;
    color: #ef4444;

    &.active {
        color: #10b981;
    }
}

.emptyState {
    text-align: center;
    padding: 40px;
    color: #999999;
    font-size: 14px;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 16px;
}

.pagination button {
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #4b5563;
    transition: 0.15s ease;

    &:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    &.active {
        background: #111111;
        color: white;
        border-color: #111111;
    }

    &:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
}

@media (max-width: 768px) {
    .containerOfList {
        padding: 20px;
    }

    .topSection {
        flex-direction: column;
        align-items: stretch;
    }

    .actionsSection {
        flex-direction: column;
        align-items: stretch;
    }

    .search input {
        width: 100%;
    }

    .filtering {
        justify-content: space-between;
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
        padding: 16px 0;
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
            font-weight: 600;
            color: #999999;
            letter-spacing: 0.5px;
        }
    }

    .actionsCell {
        justify-content: flex-end;
        width: 100%;
        margin-top: 8px;
    }
}
</style>
