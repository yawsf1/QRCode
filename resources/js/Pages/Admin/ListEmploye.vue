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

                        <td data-label="Actions" class="text-right">
                            <button
                                class="tableDeleteBtn"
                                @click="confirmDelete(employe.id)"
                            >
                                <span class="material-symbols-rounded"
                                    >delete</span
                                >
                                Supprimer
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
                                class="tableViewBtn"
                                @click="showEmploye(employe.id)"
                            >
                                <span class="material-symbols-rounded"
                                    >visibility</span
                                >
                                Voir
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
/* Your SCSS styling block remains completely unchanged and functional here! */
.containerOfList {
    width: 100%;
    min-height: calc(100vh - 50px);
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    position: relative;
}

.modalOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.modalBox {
    background: #ffffff;
    padding: 32px;
    border-radius: 16px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow:
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    align-items: center;

    .modalIcon {
        font-size: 48px;
        color: #ef4444;
        margin-bottom: 16px;
    }

    h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px 0;
    }

    p {
        font-size: 14px;
        color: #64748b;
        line-height: 1.5;
        margin: 0 0 24px 0;
    }
}

.modalActions {
    display: flex;
    gap: 12px;
    width: 100%;

    button {
        flex: 1;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btnConfirm {
        background: #ef4444;
        color: #ffffff;
        border: none;

        &:hover {
            background: #dc2626;
        }
    }

    .btnCancel {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;

        &:hover {
            background: #e2e8f0;
        }
    }
}

.topSection {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.titleSection {
    display: flex;
    flex-direction: column;
    gap: 4px;

    h2 {
        font-size: 28px;
        font-weight: 700;
    }

    p {
        color: #64748b;
        font-size: 14px;
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
    border: 1px solid #e2e8f0;
    outline: none;
    transition: 0.2s ease;
    font-size: 14px;

    &:focus {
        border-color: #2563eb;
    }
}

.filtering {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.tableWrapper {
    background: white;
    border-radius: 22px;
    overflow-x: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

thead {
    background: #f8fafc;

    th {
        text-align: left;
        padding: 18px 20px;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;

        &.text-right {
            text-align: right;
        }
    }
}

tbody {
    .adminRow {
        transition: 0.2s ease;

        &:hover {
            background: #f8fafc;
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            vertical-align: middle;

            &.text-right {
                text-align: right;
            }
        }
    }
}

.tableDeleteBtn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fee2e2;
    color: #ef4444;
    border: 1px solid #fecaca;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;

    span {
        font-size: 16px;
    }

    &:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }
}

.status {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;

    &.active {
        background: #dcfce7;
        color: #15803d;
    }

    &.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }
}

.emptyState {
    text-align: center;
    padding: 30px;
    color: #94a3b8;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.pagination button {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.2s ease;
}

.pagination button:hover {
    background: #f8fafc;
}

.pagination button.active {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.pagination button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .containerOfList {
        padding: 16px;
    }

    .topSection {
        flex-direction: column;
    }

    .actionsSection {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }

    .search input {
        width: 100%;
    }

    .filtering {
        width: 100%;
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
        padding: 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    td {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0 !important;
        border: none !important;
        text-align: left !important;

        &::before {
            content: attr(data-label);
            font-weight: 600;
            color: #64748b;
        }

        &.text-right {
            justify-content: space-between;
        }
    }

    .tableDeleteBtn {
        width: auto;
    }
}
</style>
