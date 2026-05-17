<script setup>
import { route } from "ziggy-js";
import { router, Link } from "@inertiajs/vue3";
import LineChart from "../../components/Charts/LineChart.vue";
import PieChart from "../../components/Charts/PieChart.vue";
import { computed } from "vue";

const props = defineProps({
    history: Array,
    checkedInToday: Boolean,
    stats: Object,
    chartData: Object,
    pieLabels: Array,
    pieCounts: Array,
});

const handleLogout = () => {
    router.post(route("logout"));
};

const chartDatasets = computed(() => [
    {
        label: "Minutes de retard",
        data: props.chartData?.delays || [],
        color: "#4f46e5",
    },
]);

const totalLateMinutesThisWeek = computed(() => {
    return (props.chartData?.delays || []).reduce((acc, curr) => acc + curr, 0);
});
</script>

<template>
    <div class="dashboard">
        <aside class="sidebar">
            <p class="sidebarLabel">Espace Employé</p>

            <button
                class="navBtn active"
                @click="router.visit(route('employe.dashboard'))"
            >
                <span class="material-symbols-rounded">dashboard</span>
                Tableau de bord
            </button>

            <Link
                v-if="!props.checkedInToday"
                :href="route('employe.scan.form')"
                class="navBtn scan-btn"
            >
                <span class="material-symbols-rounded">photo_camera</span>
                Ouvrir le Scanner
            </Link>
        </aside>

        <main class="content">
            <div class="pageHeader flex-header">
                <div>
                    <h1 class="pageTitle">Tableau de bord</h1>
                    <p class="pageSubtitle">
                        Suivi analytique de vos présences et ponctualité
                    </p>
                </div>

                <button
                    class="logout-action-btn"
                    @click="handleLogout"
                    title="Déconnexion"
                >
                    <span class="material-symbols-rounded">logout</span>
                    Déconnexion
                </button>
            </div>

            <section
                class="status-card"
                :class="{ 'is-completed': props.checkedInToday }"
            >
                <div class="status-info">
                    <span class="material-symbols-rounded status-icon">
                        {{
                            props.checkedInToday
                                ? "verified"
                                : "pending_actions"
                        }}
                    </span>
                    <div>
                        <h2 class="cardTitle">
                            {{
                                props.checkedInToday
                                    ? "Présence Enregistrée"
                                    : "Pointage Requis"
                            }}
                        </h2>
                        <p class="cardSubtitle">
                            {{
                                props.checkedInToday
                                    ? "Votre journée de travail a été validée avec succès."
                                    : "Veuillez scanner le code QR de la borne de votre administrateur."
                            }}
                        </p>
                    </div>
                </div>

                <div v-if="props.checkedInToday" class="success-badge">
                    <span class="material-symbols-rounded">check</span> En règle
                    aujourd'hui
                </div>
            </section>

            <div class="dashboard-grid">
                <div class="chartCard line-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">Rapport Hebdomadaire</span>
                            <h2 class="cardTitle">
                                Évolution des retards (min)
                            </h2>
                        </div>
                        <div class="statSummaryContainer">
                            <div class="statSummary adminColor">
                                <span class="statLabel">Jours Pointés</span>
                                <span class="statValue">{{
                                    props.stats?.total_days || 0
                                }}</span>
                            </div>
                            <div class="statSummary dangerColor">
                                <span class="statLabel">Cumul Retards</span>
                                <span class="statValue"
                                    >{{ totalLateMinutesThisWeek }}m</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="chartContainer">
                        <LineChart
                            :labels="props.chartData?.labels || []"
                            :datasets="chartDatasets"
                        />
                    </div>
                </div>

                <div class="chartCard pie-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">Statistiques Globales</span>
                            <h2 class="cardTitle">
                                Taux de Ponctualité :
                                {{ props.stats?.punctuality_rate || "100%" }}
                            </h2>
                            <p class="cardSubtitle">
                                Répartition de votre ponctualité globale
                            </p>
                        </div>
                    </div>

                    <div class="chartContainer">
                        <PieChart
                            :labels="props.pieLabels || []"
                            :employeeCounts="props.pieCounts || []"
                            suffix="jour(s)"
                        />
                    </div>
                </div>
            </div>

            <section class="history-section">
                <div class="section-header">
                    <span class="material-symbols-rounded">history</span>
                    <h4>Historique récent des pointages</h4>
                </div>

                <div v-if="props.history.length === 0" class="empty-history">
                    <span class="material-symbols-rounded">rule</span>
                    <p>
                        Aucun enregistrement de présence trouvé pour le moment.
                    </p>
                </div>

                <div v-else class="history-list">
                    <div
                        v-for="log in props.history"
                        :key="log.id"
                        class="history-item"
                    >
                        <div class="item-left">
                            <div class="status-dot" :class="log.statut"></div>
                            <div>
                                <p class="log-date">{{ log.date }}</p>
                                <p class="log-time">
                                    Heure de scan : {{ log.heure }}
                                </p>
                            </div>
                        </div>

                        <span class="pill-badge" :class="log.statut">
                            <template
                                v-if="
                                    log.statut === 'punctual' ||
                                    log.statut === 'a_lheure'
                                "
                                >À l'heure</template
                            >
                            <template v-else-if="log.statut === 'en_avance'"
                                >En avance</template
                            >
                            <template v-else-if="log.statut === 'absent'"
                                >Absent</template
                            >
                            <template v-else>En retard</template>
                        </span>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped lang="scss">
.dashboard {
    display: flex;
    width: 100%;
    min-height: calc(100vh - 50px);
}

.sidebar {
    width: 220px;
    flex-shrink: 0;
    background: #ffffff;
    border-right: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    padding: 24px 12px;
    gap: 4px;
}

.sidebarLabel {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 0 8px 12px;
}

.navBtn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 10px;
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 500;
    color: #0f172a;
    cursor: pointer;
    transition: background 0.15s ease;
    text-align: left;
    width: 100%;
    text-decoration: none;

    span {
        font-size: 18px;
        color: #94a3b8;
    }

    &:hover {
        background: #f8fafc;
        span {
            color: #0f172a;
        }
    }

    &.active {
        background: #f1f5f9;
        span {
            color: #0f172a;
        }
    }

    &.scan-btn {
        margin-top: 4px;
        color: #4f46e5;
        span {
            color: #4f46e5;
        }
        &:hover {
            background: #eeebff;
        }
    }
}

.content {
    flex: 1;
    background: #f8fafc;
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    overflow-y: auto;
}

.pageHeader {
    display: flex;
    flex-direction: column;
    gap: 4px;

    &.flex-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.logout-action-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;

    span {
        font-size: 16px;
    }
    &:hover {
        background: #fef2f2;
        color: #ef4444;
        border-color: #fca5a5;
    }
}

.pageTitle {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.pageSubtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.status-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.01);

    .status-info {
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .status-icon {
        font-size: 28px;
        color: #3b82f6;
        padding: 10px;
        background: #eff6ff;
        border-radius: 12px;
    }

    &.is-completed {
        border-color: #a7f3d0;
        .status-icon {
            color: #059669;
            background: #d1fae5;
        }
    }

    .success-badge {
        background: #d1fae5;
        color: #065f46;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        span {
            font-size: 16px;
        }
    }
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1.6fr 1.4fr;
    gap: 24px;
    width: 100%;
}

.chartCard {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.01);
    display: flex;
    flex-direction: column;
}

.chartHeader {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.badge {
    display: inline-block;
    background: #f1f5f9;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    margin-bottom: 6px;
    letter-spacing: 0.03em;
}

.cardTitle {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.cardSubtitle {
    font-size: 13px;
    color: #64748b;
    margin: 4px 0 0 0;
}

.statSummaryContainer {
    display: flex;
    gap: 24px;
}

.statSummary {
    display: flex;
    flex-direction: column;
    text-align: right;
}

.statLabel {
    font-size: 12px;
    color: #64748b;
    font-weight: 500;
}

.statValue {
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
}

.adminColor .statValue {
    color: #4f46e5;
}
.dangerColor .statValue {
    color: #ef4444;
}

.chartContainer {
    margin-top: auto;
}

.history-section {
    display: flex;
    flex-direction: column;
    gap: 12px;

    .section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #475569;
        padding-left: 4px;
        h4 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }
        span {
            font-size: 18px;
        }
    }
}

.empty-history {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 40px 20px;
    text-align: center;
    color: #94a3b8;
    span {
        font-size: 36px;
        margin-bottom: 8px;
    }
    p {
        font-size: 13px;
        margin: 0;
    }
}

.history-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.history-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;

    .item-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        &.punctual,
        &.a_lheure {
            background: #10b981;
        }
        &.en_avance {
            background: #0ea5e9;
        }
        &.absent {
            background: #64748b;
        }
        &.late,
        &.en_retard {
            background: #ef4444;
        }
    }

    .log-date {
        font-size: 13px;
        font-weight: 600;
        margin: 0;
        color: #1e293b;
    }
    .log-time {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0 0;
    }
}

.pill-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;

    &.punctual,
    &.a_lheure {
        background: #d1fae5;
        color: #065f46;
    }
    &.en_avance {
        background: #e0f2fe;
        color: #0369a1;
    }
    &.absent {
        background: #f1f5f9;
        color: #475569;
    }
    &.late,
    &.en_retard {
        background: #fef2f2;
        color: #991b1b;
    }
}

@media (max-width: 1200px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        padding: 12px;
        border-right: none;
        border-bottom: 1px solid #f1f5f9;
    }
    .sidebarLabel {
        display: none;
    }
    .content {
        padding: 20px 16px;
    }
    .chartHeader {
        flex-direction: column;
        gap: 16px;
    }
    .statSummaryContainer {
        width: 100%;
        justify-content: space-between;
    }
    .statSummary {
        text-align: left;
    }
    .status-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
    }
}
</style>
