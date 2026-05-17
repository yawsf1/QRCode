<script setup>
import { route } from "ziggy-js";
import { router, usePage } from "@inertiajs/vue3";
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

// 2. Map the structural datasets cleanly using our brand accent configuration tokens
const lineChartDatasets = computed(() => [
    {
        label: "À l'heure",
        data: props.chartData.punctuals || [],
        color: "#4f7cff", // Core Brand Accent Blue
    },
    {
        label: "En Retard",
        data: props.chartData.lates || [],
        color: "#eab308", // Amber Warning
    },
    {
        label: "Départs Anticipés",
        data: props.chartData.earlies || [],
        color: "#a78bfa", // Purple Highlight
    },
    {
        label: "Absents",
        data: props.chartData.absents || [],
        color: "#ff6b6b", // Coral Crimson Red
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
    <div class="dashboardPage">
        <div class="bg">
            <div class="gridPattern"></div>
            <div class="glowOrb orb1"></div>
            <div class="glowOrb orb2"></div>
        </div>

        <aside class="sidebar">
            <div class="brand">
                <div class="logoMark">
                    <span class="material-symbols-rounded">qr_code_2</span>
                </div>
                <span class="logoText">QR<span class="thin">Coded</span></span>
            </div>

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
                        <span class="metricValue">
                            {{ companyMetrics?.productivityScore ?? 0
                            }}<em class="unit">%</em>
                        </span>
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
                        <span class="metricValue">
                            {{ companyMetrics?.presenceRate ?? 0
                            }}<em class="unit">%</em>
                        </span>
                        <span class="metricLabel">Taux de Présence</span>
                    </div>
                    <div class="trendLine">
                        <span class="trendBadge up">● Live</span>
                        <span class="contextLabel">disponibilité brute</span>
                    </div>
                </div>

                <div class="typoMetricWidget">
                    <div class="mainDisplay">
                        <span class="metricValue">
                            {{ companyMetrics?.punctualityRate ?? 0
                            }}<em class="unit">%</em>
                        </span>
                        <span class="metricLabel">Taux de Ponctualité</span>
                    </div>
                    <div class="trendLine">
                        <span class="trendBadge textAccent">i Info</span>
                        <span class="contextLabel">
                            Tolérance:
                            {{ companyMetrics?.toleranceImpact ?? 0 }}h
                        </span>
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
                                    <span class="name">
                                        {{ emp.nom }} {{ emp.prenom }}
                                    </span>
                                    <span class="countBadge active">
                                        {{ emp.punctual_count }} scans
                                    </span>
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
                                    <span class="name">
                                        {{ emp.nom }} {{ emp.prenom }}
                                    </span>
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
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap");

.dashboardPage {
    --bg: #0a0a0f;
    --surface: #111118;
    --surface2: #16161f;
    --border: rgba(255, 255, 255, 0.08);
    --border-strong: rgba(255, 255, 255, 0.14);
    --border-focus: rgba(79, 124, 255, 0.6);
    --text-primary: #f0f0f8;
    --text-secondary: #8888aa;
    --text-muted: #55556a;
    --accent: #4f7cff;
    --accent-dim: rgba(79, 124, 255, 0.12);
    --error: #ff6b6b;
    --error-dim: rgba(255, 107, 107, 0.1);
    --success: #059669;
    --success-dim: rgba(5, 150, 105, 0.12);

    font-family: "Sora", sans-serif;
    /* Changed to 100% to cleanly adapt to MainLayout's internal height boundary */
    height: 100%;
    width: 100%;
    display: flex;
    background: var(--bg);
    position: relative;
    overflow: hidden;
}

* {
    box-sizing: border-box;
}

/* Background Micro-patterns & Glow Orbs */
.bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
}

.gridPattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
    background-size: 50px 50px;
    mask-image: radial-gradient(
        ellipse 80% 80% at 50% 40%,
        black 0%,
        transparent 100%
    );
}

.glowOrb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    &.orb1 {
        width: 600px;
        height: 600px;
        background: radial-gradient(
            circle,
            rgba(79, 124, 255, 0.08) 0%,
            transparent 70%
        );
        top: -150px;
        right: -100px;
    }
    &.orb2 {
        width: 500px;
        height: 500px;
        background: radial-gradient(
            circle,
            rgba(167, 139, 250, 0.06) 0%,
            transparent 70%
        );
        bottom: -150px;
        left: -100px;
    }
}

/* Sidebar Branding Layout rules */
.sidebar {
    width: 260px;
    flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border-strong);
    display: flex;
    flex-direction: column;
    padding: 32px 18px;
    gap: 6px;
    z-index: 10;

    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 36px;
        padding-left: 6px;

        .logoMark {
            width: 30px;
            height: 30px;
            background: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            span {
                font-size: 16px;
                color: #fff;
            }
        }

        .logoText {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--text-primary);
        }
        .thin {
            font-weight: 300;
            color: var(--text-secondary);
        }
    }
}

.sidebarLabel {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0 12px 10px;
    margin: 0;
}

.navBtn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid transparent;
    background: transparent;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.15s ease;
    text-align: left;
    width: 100%;

    span {
        font-size: 18px;
        color: var(--text-muted);
        transition: color 0.15s;
    }

    &:hover {
        background: var(--surface2);
        color: var(--text-primary);
        span {
            color: var(--text-primary);
        }
    }

    &.active {
        background: var(--accent-dim);
        color: var(--text-primary);
        border-color: rgba(79, 124, 255, 0.2);
        span {
            color: var(--accent);
        }
    }
}

/* Workspace Core Blocks */
.content {
    flex: 1;
    height: 100%;
    padding: 40px 48px;
    display: flex;
    flex-direction: column;
    gap: 40px;
    overflow-y: auto; /* Content handles its own layout depth scrolls elegantly here */
    z-index: 10;
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
}

.pageTitle {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.8px;
    color: var(--text-primary);
    margin: 0 0 4px 0;
}

.pageSubtitle {
    font-size: 13.5px;
    color: var(--text-secondary);
    margin: 0;
}

/* Typographic High-End Metric Widgets */
.executiveSummaryGrid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
}

.typoMetricWidget {
    display: flex;
    flex-direction: column;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px 28px;
    gap: 16px;

    .mainDisplay {
        display: flex;
        flex-direction: column;
        gap: 6px;

        .metricValue {
            font-size: 42px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -1.5px;
            color: var(--text-primary);

            .unit {
                font-style: normal;
                font-size: 22px;
                font-weight: 400;
                color: var(--text-secondary);
                margin-left: 2px;
            }
        }

        .metricLabel {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
        }
    }

    .trendLine {
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid var(--border);
        padding-top: 14px;
        width: 100%;

        .trendBadge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;

            &.up {
                background: var(--success-dim);
                color: var(--success);
            }
            &.down {
                background: var(--error-dim);
                color: var(--error);
            }
            &.textAccent {
                background: var(--accent-dim);
                color: var(--accent);
            }
        }

        .contextLabel {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }
    }
}

/* Cards & Layout Alignments */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1.6fr 1.4fr;
    gap: 32px;
    width: 100%;
}

.chartCard {
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
}

.chartHeader {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    gap: 16px;
}

.badge {
    display: inline-block;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    font-size: 10px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    margin-bottom: 8px;
    letter-spacing: 0.05em;
}

.cardTitle {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -0.3px;
    color: var(--text-primary);
    margin: 0;
}

.cardSubtitle {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 4px 0 0 0;
}

.statSummaryContainer {
    display: flex;
    gap: 20px;
}

.statSummary {
    display: flex;
    flex-direction: column;
    text-align: right;
}

.statLabel {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
}

.statValue {
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
}

.adminColor .statValue {
    color: var(--accent);
}
.employeColor .statValue {
    color: var(--error);
}

.chartContainer {
    margin-top: auto;
    width: 100%;
    background: var(--surface2);
    border-radius: 12px;
    padding: 16px;
    border: 1px solid var(--border);
}

/* Leaderboards Visual Alignment Blocks */
.leaderboardGrid {
    display: flex;
    flex-direction: column;
    gap: 28px;
    margin-top: 6px;
}

.rankingBlock {
    display: flex;
    flex-direction: column;
    gap: 12px;

    .rankingTitle {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.04em;

        &.textSuccess {
            color: var(--success);
        }
        &.textDanger {
            color: var(--error);
        }
    }
}

.rankList {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.rankRow {
    display: flex;
    align-items: center;
    background: var(--surface2);
    border: 1px solid var(--border);
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13.5px;
    transition: border-color 0.15s;

    &:hover {
        border-color: var(--border-strong);
    }

    .position {
        font-weight: 700;
        width: 32px;
        color: var(--text-muted);
    }

    .name {
        flex: 1;
        font-weight: 600;
        color: var(--text-primary);
    }

    .countBadge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;

        &.active {
            background: var(--success-dim);
            color: var(--success);
        }

        &.alert {
            background: var(--error-dim);
            color: var(--error);
        }
    }
}

.emptyLabel {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0;
    font-style: italic;
}

/* Adaptive Responsiveness Controls */
@media (max-width: 1340px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 1024px) {
    .executiveSummaryGrid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}

@media (max-width: 768px) {
    .dashboardPage {
        flex-direction: column;
        height: auto;
        overflow: auto;
    }
    .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        padding: 16px;
        border-right: none;
        border-bottom: 1px solid var(--border-strong);

        .brand {
            margin-bottom: 0;
            margin-right: 16px;
        }
    }
    .sidebarLabel {
        display: none;
    }
    .content {
        padding: 24px 20px;
        height: auto;
        overflow-y: visible;
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
