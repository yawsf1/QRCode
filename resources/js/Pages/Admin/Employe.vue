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

const chartData = computed(() => ({
    labels: ["À l'heure", "En retard", "En avance", "Absences"],
    datasets: [
        {
            data: [
                props.stats?.on_time ?? 0,
                props.stats?.late ?? 0,
                props.stats?.early ?? 0,
                props.stats?.absent ?? 0,
            ],
            backgroundColor: ["#10b981", "#f59e0b", "#3b82f6", "#ef4444"],
            borderColor: "#111118",
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
                font: { size: 12, family: "'Sora', sans-serif" },
                padding: 14,
                color: "#8888aa",
            },
        },
        tooltip: {
            backgroundColor: "#16161f",
            titleColor: "#f0f0f8",
            bodyColor: "#8888aa",
            padding: 12,
            cornerRadius: 6,
            borderColor: "rgba(255, 255, 255, 0.06)",
            borderWidth: 1,
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

function exportRapport(type) {
    window.location.href = route("admin.employe.rapport", {
        user: props.employe.id,
        type,
    });
}
</script>

<template>
    <div class="workspaceCanvas">
        <div v-if="showDeleteModal" class="dialogOverlay">
            <div class="dialogBox">
                <span class="material-symbols-rounded dialogIcon">warning</span>
                <h3>Révocation définitive</h3>
                <p>
                    Cette action supprimera le matricule de
                    <strong>{{ employe.prenom }} {{ employe.nom }}</strong>
                    ainsi que l'intégralité de son historique de pointage QR.
                </p>
                <div class="dialogActions">
                    <button class="btnCancel" @click="showDeleteModal = false">
                        Annuler
                    </button>
                    <button class="btnConfirm" @click="handleFormDelete">
                        Confirmer la suppression
                    </button>
                </div>
            </div>
        </div>

        <div class="actionRibbon">
            <div class="backLinkContainer">
                <SecondaryLink
                    :link="route('employe.list')"
                    text="← Revenir au registre global"
                />
            </div>
            <div class="controlCluster">
                <button class="actionBtn export" @click="exportRapport('mensuel')">
                    <span class="material-symbols-rounded">download</span>
                    Rapport mensuel
                </button>
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
                        <span class="lbl">En avance</span>
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
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap");

.workspaceCanvas {
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

    font-family: "Sora", sans-serif;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    padding: 30px 40px;
    background: var(--bg);
    color: var(--text-primary);
    display: flex;
    flex-direction: column;
    gap: 32px;
    overflow-x: hidden; 
}

.actionRibbon {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border);
    padding-bottom: 20px;
    gap: 16px;
    flex-wrap: wrap;

    .backLinkContainer {
        min-width: 0;
        display: flex;
        align-items: center;
    }

    .controlCluster {
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }
}

.actionBtn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid var(--border);
    background: var(--surface2);
    font-family: "Sora", sans-serif;
    white-space: nowrap;

    span {
        font-size: 16px;
    }

    &.edit {
        background: var(--accent);
        border: none;
        color: #ffffff;
        &:hover {
            background: var(--accent-hover);
        }
    }

    &.export {
        color: var(--text-primary);
        &:hover {
            border-color: rgba(79, 124, 255, 0.4);
            background: rgba(79, 124, 255, 0.1);
        }
    }

    &.delete {
        border-color: rgba(255, 107, 107, 0.2);
        color: var(--error);
        background: transparent;
        &:hover {
            background: var(--error-dim);
            border-color: var(--error);
        }
    }
}

.profileLayout {
    display: grid;
    grid-template-columns: 300px minmax(0, 1fr); 
    gap: 40px;
    align-items: start;
    width: 100%;
}

.identityMetaBlock {
    display: flex;
    flex-direction: column;
    gap: 32px;
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    min-width: 0;

    .glanceInfo {
        display: flex;
        align-items: center;
        gap: 16px;
        width: 100%;

        .textInitialAvatar {
            width: 56px;
            height: 56px;
            background: var(--surface2);
            color: var(--text-primary);
            border: 1px solid var(--border);
            border-radius: 10px;
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .titleStack {
            min-width: 0;
            h1 {
                font-size: 18px;
                font-weight: 700;
                color: var(--text-primary);
                margin: 0;
                letter-spacing: -0.3px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .roleSubtitle {
                font-size: 13px;
                color: var(--text-secondary);
                margin: 4px 0 0 0;
                font-weight: 500;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    }

    .metaGrid {
        display: flex;
        flex-direction: column;
        gap: 18px;
        border-top: 1px solid var(--border);
        padding-top: 24px;

        .metaRow {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;

            .label {
                font-size: 11px;
                text-transform: uppercase;
                color: var(--text-muted);
                font-weight: 700;
                letter-spacing: 0.5px;
            }
            .value {
                font-size: 13px;
                color: var(--text-primary);
                font-weight: 500;
                word-break: break-all; 
            }

            .systemStatus {
                font-weight: 700;
                color: var(--error);
                &.is-active {
                    color: var(--success);
                }
            }
        }
    }
}

.dataMetricsBlock {
    display: flex;
    flex-direction: column;
    gap: 32px;
    min-width: 0;
}

.metricDataRow {
    display: grid;
    grid-template-columns: repeat(
        5,
        minmax(0, 1fr)
    ); 
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 24px;
    gap: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);

    .metricCell {
        display: flex;
        flex-direction: column;
        min-width: 0;

        .num {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -1px;
        }
        .lbl {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        &.punctual .num {
            color: var(--success);
        }
        &.late .num {
            color: #f59e0b;
        }
        &.early .num {
            color: #3b82f6;
        }
        &.absent .num {
            color: var(--error);
        }
    }
}

.operationalSplit {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 32px;
    width: 100%;
}

.panelArea {
    display: flex;
    flex-direction: column;
    gap: 20px;
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    min-width: 0;

    .panelTitle {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
    }
}

.canvasWrapper {
    height: 220px;
    width: 100%;
    position: relative; 
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
    border-bottom: 1px dashed var(--border);
    gap: 16px;

    .ruleLabel {
        font-size: 13px;
        color: var(--text-secondary);
        white-space: nowrap;
        flex-shrink: 0;
        font-weight: 500;
    }

    .ruleValue {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        text-align: right;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .textAccent {
        color: var(--accent);
    }

    .daysHighlight {
        font-size: 12px;
        background: var(--surface2);
        border: 1px solid var(--border);
        padding: 4px 10px;
        border-radius: 6px;
        color: var(--text-primary);
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

.dialogOverlay {
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

.dialogBox {
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

    .dialogIcon {
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

    .dialogActions {
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
}

@media (max-width: 1150px) {
    .profileLayout {
        grid-template-columns: 1fr;
    }
    .identityMetaBlock {
        max-width: 100%;
    }
}

@media (max-width: 850px) {
    .metricDataRow {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .operationalSplit {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 550px) {
    .workspaceCanvas {
        padding: 20px;
    }
    .metricDataRow {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
