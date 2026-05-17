<script setup>
import { ref, computed } from "vue";
import { route } from "ziggy-js";
import { router, useForm } from "@inertiajs/vue3";
import SecondaryLink from "../../components/Links/SmallSecondaryLink.vue";
import { Pie } from "vue-chartjs";
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps({
    employe: Object,
    stats: Object,
});

function formatDate(date) {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

// Stacking matching structural arrays for ChartJS
const chartData = computed(() => ({
    labels: ["À l'heure", "En retard", "Départs Anticipés", "Absences"],
    datasets: [
        {
            data: [
                props.stats?.on_time ?? 0,
                props.stats?.late ?? 0,
                props.stats?.early ?? 0,
                props.stats?.absent ?? 0,
            ],
            backgroundColor: ["#10b981", "#f59e0b", "#3b82f6", "#ef4444"],
            borderColor: "#ffffff",
            borderWidth: 2,
            hoverOffset: 6,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "right",
            labels: {
                boxWidth: 12,
                usePointStyle: true,
                pointStyle: "circle",
                font: { size: 12, family: "'Inter', sans-serif" },
                padding: 14,
            },
        },
        tooltip: {
            backgroundColor: "#111111",
            padding: 12,
            cornerRadius: 6,
            callbacks: {
                label: function (context) {
                    const value = context.raw || 0;
                    const total = context.dataset.data.reduce(
                        (a, b) => a + b,
                        0,
                    );
                    const percentage =
                        total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                    return ` ${context.label}: ${value} jour(s) (${percentage}%)`;
                },
            },
        },
    },
}));

const showDeleteModal = ref(false);
const deleteForm = useForm({});

function handleFormDelete() {
    deleteForm.delete(route("admin.employe.delete", props.employe.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
}

function navigateToEdit() {
    router.get(route("employe.update.form", props.employe.id));
}
</script>

<template>
    <div class="workspaceCanvas">
        <div v-if="showDeleteModal" class="dialogOverlay">
            <div class="dialogBox">
                <h3>Révocation définitive</h3>
                <p>
                    Cette action supprimera le matricule de
                    <strong>{{ employe.prenom }} {{ employe.nom }}</strong>
                    ainsi que l'intégralité de son historique de pointage QR.
                </p>
                <div class="dialogActions">
                    <button class="btnConfirm" @click="handleFormDelete">
                        Confirmer la suppression
                    </button>
                    <button class="btnCancel" @click="showDeleteModal = false">
                        Annuler
                    </button>
                </div>
            </div>
        </div>

        <div class="actionRibbon">
            <SecondaryLink
                :link="route('employe.list')"
                text="← Revenir au registre global"
            />
            <div class="controlCluster">
                <button class="actionBtn edit" @click="navigateToEdit">
                    <span class="material-symbols-rounded">edit</span>Modifier
                </button>
                <button
                    class="actionBtn delete"
                    @click="showDeleteModal = true"
                >
                    <span class="material-symbols-rounded">delete</span
                    >Supprimer le profil
                </button>
            </div>
        </div>

        <main class="profileLayout">
            <section class="identityMetaBlock">
                <div class="glanceInfo">
                    <div class="textInitialAvatar">
                        {{ employe.prenom?.[0] }}{{ employe.nom?.[0] }}
                    </div>
                    <div class="titleStack">
                        <h1>{{ employe.prenom }} {{ employe.nom }}</h1>
                        <p class="roleSubtitle">
                            {{
                                employe.departement ||
                                "Département non spécifié"
                            }}
                        </p>
                    </div>
                </div>

                <div class="metaGrid">
                    <div class="metaRow">
                        <span class="label">Canal Email</span>
                        <span class="value">{{ employe.email }}</span>
                    </div>
                    <div class="metaRow">
                        <span class="label">Ligne Directe</span>
                        <span class="value">{{
                            employe.telephone || "—"
                        }}</span>
                    </div>
                    <div class="metaRow">
                        <span class="label">Enregistrement</span>
                        <span class="value">{{
                            formatDate(employe.created_at)
                        }}</span>
                    </div>
                    <div class="metaRow">
                        <span class="label">Statut Système</span>
                        <span
                            class="value systemStatus"
                            :class="{ 'is-active': employe.est_actif }"
                        >
                            {{
                                employe.est_actif
                                    ? "Actif & Autorisé"
                                    : "Accès Révoqué"
                            }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="dataMetricsBlock">
                <div class="metricDataRow">
                    <div class="metricCell">
                        <span class="num">{{ stats.total_scans ?? 0 }}</span>
                        <span class="lbl">Scans Totaux</span>
                    </div>
                    <div class="metricCell punctual">
                        <span class="num">{{ stats.on_time ?? 0 }}</span>
                        <span class="lbl">À l'heure</span>
                    </div>
                    <div class="metricCell late">
                        <span class="num">{{ stats.late ?? 0 }}</span>
                        <span class="lbl">En retard</span>
                    </div>
                    <div class="metricCell early">
                        <span class="num">{{ stats.early ?? 0 }}</span>
                        <span class="lbl">Départs Anticipés</span>
                    </div>
                    <div class="metricCell absent">
                        <span class="num">{{ stats.absent ?? 0 }}</span>
                        <span class="lbl">Absences</span>
                    </div>
                </div>

                <div class="operationalSplit">
                    <div class="panelArea chartFrame">
                        <div class="panelTitle">Analyse des Scans</div>
                        <div class="canvasWrapper">
                            <Pie :data="chartData" :options="chartOptions" />
                        </div>
                    </div>

                    <div class="panelArea scheduleFrame">
                        <div class="panelTitle">Régulation Horaire</div>

                        <div class="ruleCardList">
                            <div class="ruleDataLine">
                                <span class="ruleLabel"
                                    >Fenêtre de pointage</span
                                >
                                <span class="ruleValue textAccent">
                                    {{ employe.horaire?.heure_debut || "—" }} —
                                    {{ employe.horaire?.heure_fin || "—" }}
                                </span>
                            </div>
                            <div class="ruleDataLine">
                                <span class="ruleLabel"
                                    >Marge de retard tolérée</span
                                >
                                <span class="ruleValue">
                                    {{
                                        employe.horaire?.tolerance_retard ?? "0"
                                    }}
                                    minutes
                                </span>
                            </div>
                            <div class="ruleDataLine">
                                <span class="ruleLabel"
                                    >Jours de service obligatoires</span
                                >
                                <span class="ruleValue daysHighlight">
                                    {{
                                        employe.horaire?.jours_ouvres
                                            ? employe.horaire.jours_ouvres.join(
                                                  " · ",
                                              )
                                            : "—"
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped lang="scss">
.workspaceCanvas {
    width: 100%;
    min-height: calc(100vh - 50px);
    padding: 40px;
    background: #fafafa;
    color: #111111;
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.actionRibbon {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eeeeee;
    padding-bottom: 20px;

    .controlCluster {
        display: flex;
        gap: 12px;
    }
}

.actionBtn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
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

    &.edit {
        background: #111111;
        color: #ffffff;
        &:hover {
            background: #333333;
        }
    }

    &.delete {
        border-color: #e5e7eb;
        color: #ef4444;
        &:hover {
            background: #fef2f2;
            border-color: #fee2e2;
        }
    }
}

.profileLayout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 64px;
    align-items: start;
}

.identityMetaBlock {
    display: flex;
    flex-direction: column;
    gap: 32px;

    .glanceInfo {
        display: flex;
        align-items: center;
        gap: 16px;

        .textInitialAvatar {
            width: 56px;
            height: 56px;
            background: #e2e8f0;
            color: #475569;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .titleStack {
            h1 {
                font-size: 20px;
                font-weight: 700;
                color: #111111;
                margin: 0;
                letter-spacing: -0.3px;
            }
            .roleSubtitle {
                font-size: 13px;
                color: #666666;
                margin: 4px 0 0 0;
            }
        }
    }

    .metaGrid {
        display: flex;
        flex-direction: column;
        gap: 18px;
        border-top: 1px solid #eeeeee;
        padding-top: 24px;

        .metaRow {
            display: flex;
            flex-direction: column;
            gap: 4px;

            .label {
                font-size: 11px;
                text-transform: uppercase;
                color: #999999;
                font-weight: 600;
                letter-spacing: 0.5px;
            }
            .value {
                font-size: 14px;
                color: #222222;
                font-weight: 500;
            }

            .systemStatus {
                font-weight: 600;
                color: #ef4444;
                &.is-active {
                    color: #10b981;
                }
            }
        }
    }
}

.dataMetricsBlock {
    display: flex;
    flex-direction: column;
    gap: 48px;
}

.metricDataRow {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    border-bottom: 1px solid #eeeeee;
    padding-bottom: 32px;

    .metricCell {
        display: flex;
        flex-direction: column;

        .num {
            font-size: 32px;
            font-weight: 700;
            color: #111111;
            letter-spacing: -1px;
        }
        .lbl {
            font-size: 12px;
            color: #777777;
            margin-top: 4px;
            font-weight: 500;
        }

        &.punctual .num {
            color: #10b981;
        }
        &.late .num {
            color: #f59e0b;
        }
        &.early .num {
            color: #3b82f6;
        }
        &.absent .num {
            color: #ef4444;
        }
    }
}

.operationalSplit {
    display: grid;
    grid-template-columns: 1.3fr 1fr;
    gap: 48px;
}

.panelArea {
    display: flex;
    flex-direction: column;
    gap: 20px;

    .panelTitle {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #111111;
    }
}

.canvasWrapper {
    height: 220px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ruleCardList {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 100%;
}

.ruleDataLine {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1px dashed #e5e7eb;
    gap: 16px;

    .ruleLabel {
        font-size: 13px;
        color: #666666;
        white-space: nowrap; /* Forces label to stick to a single line */
        flex-shrink: 0;
    }

    .ruleValue {
        font-size: 14px;
        font-weight: 600;
        color: #111111;
        text-align: right;
    }

    .textAccent {
        color: #3b82f6;
    }

    .daysHighlight {
        font-size: 12px;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 4px;
        color: #475569;
        white-space: nowrap; /* Prevents day lists from snapping down */
    }
}

.dialogOverlay {
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

.dialogBox {
    background: #ffffff;
    padding: 40px;
    border-radius: 12px;
    max-width: 460px;
    width: 90%;
    text-align: left;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid #111111;

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

    .dialogActions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        button {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }
        .btnConfirm {
            background: #ef4444;
            color: white;
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
}

@media (max-width: 960px) {
    .profileLayout {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    .metricDataRow {
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    .operationalSplit {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}
</style>
