<script setup>
import { route } from "ziggy-js";
import { router } from "@inertiajs/vue3";
import LineChart from "../../components/Charts/LineChart.vue";
import PieChart from "../../components/Charts/PieChart.vue";
import MainLink from "../../components/Links/MainLink.vue";
import { computed } from "vue";

// 1. Accept everything passed down from our Laravel Admin Dashboard controller
const props = defineProps({
    totalEmployees: { type: Number, default: 0 },
    months: { type: Array, default: () => [] },
    chartData: {
        type: Object,
        default: () => ({ absents: [], lates: [], earlies: [], punctuals: [] }),
    },
    companyMetrics: {
        type: Object,
        default: () => ({
            presenceRate: 0,
            toleranceImpact: 0,
            punctualityRate: 0,
            productivityScore: 0,
        }),
    },
    topEmployees: { type: Array, default: () => [] },
    worstEmployees: { type: Array, default: () => [] },
});

// 2. Map the structural datasets cleanly for your custom LineChart component
const lineChartDatasets = computed(() => [
    {
        label: "À l'heure",
        data: props.chartData.punctuals || [],
        color: "#059669", // Premium Emerald Green
    },
    {
        label: "En Retard",
        data: props.chartData.lates || [],
        color: "#eab308", // Amber Yellow
    },
    {
        label: "Départs Anticipés",
        data: props.chartData.earlies || [],
        color: "#2563eb", // Royal Blue
    },
    {
        label: "Absents",
        data: props.chartData.absents || [],
        color: "#dc2626", // Crimson Red
    },
]);

// 3. Simple aggregate computations for the high-end counter displays
const aggregatePunctuals = computed(() => {
    return (props.chartData.punctuals || []).reduce(
        (acc, curr) => acc + curr,
        0,
    );
});

const aggregateIncidents = computed(() => {
    const lates = (props.chartData.lates || []).reduce(
        (acc, curr) => acc + curr,
        0,
    );
    const absents = (props.chartData.absents || []).reduce(
        (acc, curr) => acc + curr,
        0,
    );
    return lates + absents;
});
</script>

<template>
    <div class="dashboard">
        <aside class="sidebar">
            <p class="sidebarLabel">Super admin</p>

            <button
                class="navBtn active"
                @click="router.visit(route('admin.dashboard'))"
            >
                <span class="material-symbols-rounded">dashboard</span>
                Tableau de bord
            </button>

            <button
                class="navBtn"
                @click="router.visit(route('employe.register.form'))"
            >
                <span class="material-symbols-rounded">person_add</span>
                Ajouter un employé
            </button>

            <button class="navBtn" @click="router.visit(route('employe.list'))">
                <span class="material-symbols-rounded">group</span>
                Afficher les employés
            </button>
            <button
                class="navBtn"
                @click="router.get(route('admin.qr.show'))"
                :class="{ active: route().current('admin.qr.show') }"
            >
                <span class="material-symbols-rounded">qr_code_scanner</span>
                Générer Borne QR
            </button>
        </aside>

        <main class="content">
            <div class="pageHeader">
                <div class="headerFlexRow">
                    <div>
                        <h1 class="pageTitle">Tableau de bord</h1>
                        <p class="pageSubtitle">
                            {{ totalEmployees }} employé(s) sous votre direction
                        </p>
                    </div>
                    <MainLink
                        :link="route('employe.register.form')"
                        text="Ajouter un employé"
                    />
                </div>
            </div>

            <section class="executiveSummaryGrid">
                <div class="typoMetricWidget">
                    <div class="mainDisplay">
                        <span class="metricValue"
                            >{{ companyMetrics?.productivityScore ?? 0 }}%</span
                        >
                        <span class="metricLabel">Indice de Productivité</span>
                    </div>
                    <div class="trendLine">
                        <span
                            class="trendBadge"
                            :class="
                                (companyMetrics?.productivityScore ?? 0) >= 85
                                    ? 'up'
                                    : 'down'
                            "
                        >
                            {{
                                (companyMetrics?.productivityScore ?? 0) >= 85
                                    ? "▲ Stable"
                                    : "▼ Critique"
                            }}
                        </span>
                        <span class="contextLabel">performance globale</span>
                    </div>
                </div>

                <div class="typoMetricWidget">
                    <div class="mainDisplay">
                        <span class="metricValue"
                            >{{ companyMetrics?.presenceRate ?? 0 }}%</span
                        >
                        <span class="metricLabel">Taux de Présence</span>
                    </div>
                    <div class="trendLine">
                        <span class="trendBadge up">●</span>
                        <span class="contextLabel">disponibilité brute</span>
                    </div>
                </div>

                <div class="typoMetricWidget">
                    <div class="mainDisplay">
                        <span class="metricValue"
                            >{{ companyMetrics?.punctualityRate ?? 0 }}%</span
                        >
                        <span class="metricLabel">Taux de Ponctualité</span>
                    </div>
                    <div class="trendLine">
                        <span class="trendBadge textAccent">i</span>
                        <span class="contextLabel"
                            >impact tolérance:
                            {{ companyMetrics?.toleranceImpact ?? 0 }}h</span
                        >
                    </div>
                </div>
            </section>

            <div class="dashboard-grid">
                <div class="chartCard line-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">Rapport Annuel Global</span>
                            <h2 class="cardTitle">
                                Analyse Chronologique des Présences
                            </h2>
                        </div>
                        <div class="statSummaryContainer">
                            <div class="statSummary adminColor">
                                <span class="statLabel">Scans Ponctuels</span>
                                <span class="statValue">{{
                                    aggregatePunctuals
                                }}</span>
                            </div>
                            <div class="statSummary employeColor">
                                <span class="statLabel">Total Anomalies</span>
                                <span class="statValue">{{
                                    aggregateIncidents
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="chartContainer">
                        <LineChart
                            :labels="months"
                            :datasets="lineChartDatasets"
                        />
                    </div>
                </div>

                <div class="chartCard pie-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">Performances Mensuelles</span>
                            <h2 class="cardTitle">
                                Suivi d'Activité de l'Équipe
                            </h2>
                            <p class="cardSubtitle">
                                Top 5 des profils pour le mois en cours
                            </p>
                        </div>
                    </div>

                    <div class="leaderboardGrid">
                        <div class="rankingBlock">
                            <h4 class="rankingTitle textSuccess">
                                Top 5 - Ponctualité
                            </h4>
                            <div class="rankList">
                                <div
                                    v-for="(emp, index) in topEmployees"
                                    :key="emp.id"
                                    class="rankRow"
                                >
                                    <span class="position"
                                        >#{{ index + 1 }}</span
                                    >
                                    <span class="name"
                                        >{{ emp.nom }} {{ emp.prenom }}</span
                                    >
                                    <span class="countBadge active"
                                        >{{ emp.punctual_count }} scans</span
                                    >
                                </div>
                                <p
                                    v-if="topEmployees.length === 0"
                                    class="emptyLabel"
                                >
                                    Aucune donnée enregistrée
                                </p>
                            </div>
                        </div>

                        <div class="rankingBlock">
                            <h4 class="rankingTitle textDanger">
                                Alertes Suivi - Retards / Absences
                            </h4>
                            <div class="rankList">
                                <div
                                    v-for="(emp, index) in worstEmployees"
                                    :key="emp.id"
                                    class="rankRow"
                                >
                                    <span class="position textDanger"
                                        >#{{ index + 1 }}</span
                                    >
                                    <span class="name"
                                        >{{ emp.nom }} {{ emp.prenom }}</span
                                    >
                                    <span class="countBadge alert">
                                        {{ emp.late_count }}R /
                                        {{ emp.absent_count }}A
                                    </span>
                                </div>
                                <p
                                    v-if="worstEmployees.length === 0"
                                    class="emptyLabel"
                                >
                                    Zéro anomalie détectée
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <slot />
        </main>
    </div>
</template>

<style scoped lang="scss">
/* [Your pristine layouts rules remain active and unchanged] */
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
}

.content {
    flex: 1;
    background: #f8fafc;
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 36px; /* Expanded for high-end typographic separation spacing layout */
    overflow-y: auto;
}

.headerFlexRow {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.pageHeader {
    display: flex;
    flex-direction: column;
    gap: 4px;
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

/* Luxury Metric Typography Architecture Injector */
.executiveSummaryGrid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 48px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 32px;
}

.typoMetricWidget {
    display: flex;
    flex-direction: column;
    gap: 12px;

    .mainDisplay {
        display: flex;
        flex-direction: column;
        gap: 4px;

        .metricValue {
            font-size: 44px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
            color: #0f172a;
        }

        .metricLabel {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
    }

    .trendLine {
        display: flex;
        align-items: center;
        gap: 6px;
        border-top: 1px solid #e2e8f0;
        padding-top: 10px;
        width: 100%;
        max-width: 180px;

        .trendBadge {
            font-size: 11px;
            font-weight: 700;

            &.up {
                color: #059669;
            }
            &.down {
                color: #dc2626;
            }
            &.textAccent {
                color: #2563eb;
            }
        }

        .contextLabel {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
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
    color: #059669;
}
.employeColor .statValue {
    color: #dc2626;
}

.chartContainer {
    margin-top: auto;
    width: 100%;
}

/* Leaderboards Visual Alignment Blocks */
.leaderboardGrid {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-top: 12px;
}

.rankingBlock {
    display: flex;
    flex-direction: column;
    gap: 10px;

    .rankingTitle {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.02em;

        &.textSuccess {
            color: #059669;
        }
        &.textDanger {
            color: #dc2626;
        }
    }
}

.rankList {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.rankRow {
    display: flex;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 13px;

    .position {
        font-weight: 700;
        width: 28px;
        color: #94a3b8;
    }

    .name {
        flex: 1;
        font-weight: 600;
        color: #1e293b;
    }

    .countBadge {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;

        &.active {
            background: #d1fae5;
            color: #065f46;
        }

        &.alert {
            background: #fee2e2;
            color: #991b1b;
        }
    }
}

.emptyLabel {
    font-size: 12px;
    color: #94a3b8;
    margin: 0;
    font-style: italic;
}

@media (max-width: 1200px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    .executiveSummaryGrid {
        grid-template-columns: 1fr;
        gap: 24px;
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
}
</style>
