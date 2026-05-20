<script setup>
import { route } from "ziggy-js";
import { router, usePage } from "@inertiajs/vue3";
import LineChart from "../../components/Charts/LineChart.vue";
import MainLink from "../../components/Links/MainLink.vue";
import NotificationBell from "../../components/Notifications/NotificationBell.vue";
import AdminSidebar from "../../components/Layout/AdminSidebar.vue";
import DashboardMobileNav from "../../components/Layout/DashboardMobileNav.vue";
import { useUiStore } from "../../stores/ui";
import { computed, ref, onMounted, onUnmounted, watch } from "vue";

const ui = useUiStore();

const page = usePage();

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

const liveMetrics = ref({ ...props.companyMetrics });
const liveChartData = ref({
    punctuals: [...(props.chartData.punctuals || [])],
    lates: [...(props.chartData.lates || [])],
    earlies: [...(props.chartData.earlies || [])],
    absents: [...(props.chartData.absents || [])],
});
const liveTopEmployees = ref(props.topEmployees.map((e) => ({ ...e })));
const liveWorstEmployees = ref(props.worstEmployees.map((e) => ({ ...e })));
const liveRecentScans = ref([]);

const syncFromServer = () => {
    liveMetrics.value = { ...props.companyMetrics };
    liveChartData.value = {
        punctuals: [...(props.chartData?.punctuals || [])],
        lates: [...(props.chartData?.lates || [])],
        earlies: [...(props.chartData?.earlies || [])],
        absents: [...(props.chartData?.absents || [])],
    };
    liveTopEmployees.value = (props.topEmployees || []).map((e) => ({ ...e }));
    liveWorstEmployees.value = (props.worstEmployees || []).map((e) => ({ ...e }));
};

watch(
    () => [
        props.chartData,
        props.companyMetrics,
        props.topEmployees,
        props.worstEmployees,
    ],
    () => syncFromServer(),
    { deep: true },
);

const sumArray = (arr) => (arr || []).reduce((acc, curr) => acc + curr, 0);

const recalculateMetrics = () => {
    const punctCount =
        sumArray(liveChartData.value.punctuals) +
        sumArray(liveChartData.value.earlies);
    const lateCount = sumArray(liveChartData.value.lates);
    const absentCount = sumArray(liveChartData.value.absents);
    const total = punctCount + lateCount + absentCount;

    if (total <= 0) return;

    liveMetrics.value.presenceRate = parseFloat(
        (((total - absentCount) / total) * 100).toFixed(1),
    );

    if (total - absentCount > 0) {
        liveMetrics.value.punctualityRate = parseFloat(
            ((punctCount / (total - absentCount)) * 100).toFixed(1),
        );
    }

    liveMetrics.value.productivityScore = parseFloat(
        (
            liveMetrics.value.presenceRate * 0.6 +
            liveMetrics.value.punctualityRate * 0.4
        ).toFixed(1),
    );
};

const upsertTopEmployee = (scan) => {
    if (!["a_lheure", "en_avance", "punctual"].includes(scan.statut)) return;

    const user = scan.user || {};
    let entry = liveTopEmployees.value.find((e) => e.id === scan.user_id);

    if (entry) {
        entry.punctual_count = (entry.punctual_count || 0) + 1;
    } else {
        entry = {
            id: scan.user_id,
            nom: user.nom || "",
            prenom: user.prenom || "",
            punctual_count: 1,
        };
        liveTopEmployees.value.push(entry);
    }

    liveTopEmployees.value.sort(
        (a, b) => (b.punctual_count || 0) - (a.punctual_count || 0),
    );
    liveTopEmployees.value = liveTopEmployees.value.slice(0, 5);
};

const upsertWorstEmployee = (scan, field) => {
    const user = scan.user || {};
    let entry = liveWorstEmployees.value.find((e) => e.id === scan.user_id);

    if (entry) {
        entry[field] = (entry[field] || 0) + 1;
    } else {
        entry = {
            id: scan.user_id,
            nom: user.nom || "",
            prenom: user.prenom || "",
            late_count: field === "late_count" ? 1 : 0,
            absent_count: field === "absent_count" ? 1 : 0,
        };
        liveWorstEmployees.value.push(entry);
    }

    liveWorstEmployees.value.sort(
        (a, b) =>
            (b.late_count || 0) +
            (b.absent_count || 0) * 2 -
            ((a.late_count || 0) + (a.absent_count || 0) * 2),
    );
    liveWorstEmployees.value = liveWorstEmployees.value.slice(0, 5);
};

const statutLabel = (statut) => {
    if (statut === "a_lheure" || statut === "punctual") return "À l'heure";
    if (statut === "en_avance") return "En avance";
    if (statut === "en_retard" || statut === "late") return "En retard";
    if (statut === "absent") return "Absent";
    return statut;
};

onMounted(() => {
    syncFromServer();

    const adminId = page.props?.auth?.user?.id;

    window.Echo.channel("attendance-channel").listen(".checked-in", (e) => {
        const scan = e.presence;

        if (Number(scan.admin_id) !== Number(adminId)) return;

        const idx = 5;

        if (scan.statut === "a_lheure" || scan.statut === "punctual") {
            liveChartData.value.punctuals[idx]++;
        } else if (scan.statut === "en_retard" || scan.statut === "late") {
            liveChartData.value.lates[idx]++;
            liveMetrics.value.toleranceImpact = parseFloat(
                (
                    liveMetrics.value.toleranceImpact +
                    (scan.ecart_minutes || 0) / 60
                ).toFixed(1),
            );
            upsertWorstEmployee(scan, "late_count");
        } else if (scan.statut === "en_avance") {
            liveChartData.value.earlies[idx]++;
            upsertTopEmployee(scan);
        } else if (scan.statut === "absent") {
            liveChartData.value.absents[idx]++;
            upsertWorstEmployee(scan, "absent_count");
        }

        if (scan.statut === "a_lheure" || scan.statut === "punctual") {
            upsertTopEmployee(scan);
        }

        recalculateMetrics();

        const user = scan.user || {};
        liveRecentScans.value.unshift({
            id: scan.id,
            nom: user.nom || "",
            prenom: user.prenom || "",
            statut: scan.statut,
            time: new Date(scan.date_heure_scan).toLocaleTimeString("fr-FR", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            }),
        });

        if (liveRecentScans.value.length > 8) {
            liveRecentScans.value.pop();
        }

        router.reload({
            only: [
                "chartData",
                "companyMetrics",
                "topEmployees",
                "worstEmployees",
                "totalEmployees",
                "months",
                "notifications",
            ],
            preserveScroll: true,
        });
    });
});

onUnmounted(() => {
    window.Echo.leaveChannel("attendance-channel");
});

const lineChartDatasets = computed(() => [
    {
        label: "À l'heure",
        data: liveChartData.value.punctuals || [],
        color: "#4f7cff",
    },
    {
        label: "En Retard",
        data: liveChartData.value.lates || [],
        color: "#eab308",
    },
    {
        label: "En avance",
        data: liveChartData.value.earlies || [],
        color: "#a78bfa",
    },
    {
        label: "Absents",
        data: liveChartData.value.absents || [],
        color: "#ff6b6b",
    },
]);

const aggregatePunctuals = computed(() =>
    sumArray(liveChartData.value.punctuals) + sumArray(liveChartData.value.earlies),
);

const aggregateIncidents = computed(
    () =>
        sumArray(liveChartData.value.lates) +
        sumArray(liveChartData.value.absents),
);
</script>

<template>
    <div class="dashboardPage">
        <div class="bg">
            <div class="gridPattern"></div>
            <div class="glowOrb orb1"></div>
            <div class="glowOrb orb2"></div>
        </div>

        <div
            v-if="ui.mobileSidebarOpen"
            class="sidebarBackdrop"
            @click="ui.closeMobileSidebar()"
        ></div>

        <AdminSidebar
            active="dashboard"
            :open="ui.mobileSidebarOpen"
        />

        <main class="content">
            <div class="pageHeader">
                <div class="headerFlexRow">
                    <div>
                        <h1 class="pageTitle">Tableau de bord</h1>
                        <p class="pageSubtitle">
                            {{ totalEmployees }} employé(s) sous votre direction
                        </p>
                    </div>
                    <div class="headerActions">
                        <NotificationBell />
                        <DashboardMobileNav />
                        <MainLink
                            :link="route('employe.register.form')"
                            text="Ajouter un employé"
                        />
                    </div>
                </div>
            </div>

            <section class="executiveSummaryGrid">
                <div class="typoMetricWidget">
                    <div class="mainDisplay">
                        <span class="metricValue">
                            {{ liveMetrics.productivityScore ?? 0
                            }}<em class="unit">%</em>
                        </span>
                        <span class="metricLabel">Indice de Productivité</span>
                    </div>
                    <div class="trendLine">
                        <span
                            class="trendBadge"
                            :class="
                                (liveMetrics.productivityScore ?? 0) >= 85
                                    ? 'up'
                                    : 'down'
                            "
                        >
                            {{
                                (liveMetrics.productivityScore ?? 0) >= 85
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
                            {{ liveMetrics.presenceRate ?? 0
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
                            {{ liveMetrics.punctualityRate ?? 0
                            }}<em class="unit">%</em>
                        </span>
                        <span class="metricLabel">Taux de Ponctualité</span>
                    </div>
                    <div class="trendLine">
                        <span class="trendBadge textAccent">i Info</span>
                        <span class="contextLabel">
                            Tolérance:
                            {{ liveMetrics.toleranceImpact ?? 0 }}h
                        </span>
                    </div>
                </div>
            </section>

            <section v-if="liveRecentScans.length" class="liveFeedSection">
                <div class="liveFeedHeader">
                    <span class="material-symbols-rounded">sensors</span>
                    <h3>Pointages en direct</h3>
                    <span class="livePulse">Live</span>
                </div>
                <div class="liveFeedList">
                    <div
                        v-for="scan in liveRecentScans"
                        :key="scan.id"
                        class="liveFeedItem"
                    >
                        <span class="liveFeedName"
                            >{{ scan.prenom }} {{ scan.nom }}</span
                        >
                        <span class="liveFeedStatut" :class="scan.statut">{{
                            statutLabel(scan.statut)
                        }}</span>
                        <span class="liveFeedTime">{{ scan.time }}</span>
                    </div>
                </div>
            </section>

            <div class="dashboard-grid">
                <div class="chartCard line-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">6 derniers mois</span>
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
                                    v-for="(emp, index) in liveTopEmployees"
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
                                    v-if="liveTopEmployees.length === 0"
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
                                    v-for="(emp, index) in liveWorstEmployees"
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
                                    v-if="liveWorstEmployees.length === 0"
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
    min-height: 100vh;
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

.content {
    flex: 1;
    height: 100%;
    padding: 40px 48px;
    display: flex;
    flex-direction: column;
    gap: 40px;
    overflow-y: auto;
    z-index: 10;
}

.headerFlexRow {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    gap: 12px;
    flex-wrap: wrap;
}

.headerActions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.sidebarBackdrop {
    display: none;
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
    min-width: 0;
    overflow-x: auto;
    background: var(--surface2);
    border-radius: 12px;
    padding: 16px;
    border: 1px solid var(--border);
}

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

.liveFeedSection {
    background: var(--surface);
    border: 1px solid rgba(34, 201, 122, 0.25);
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.liveFeedHeader {
    display: flex;
    align-items: center;
    gap: 10px;

    span.material-symbols-rounded {
        font-size: 20px;
        color: #22c97a;
    }

    h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        flex: 1;
    }
}

.livePulse {
    font-size: 11px;
    font-weight: 700;
    color: #22c97a;
    background: rgba(34, 201, 122, 0.12);
    padding: 3px 8px;
    border-radius: 6px;
}

.liveFeedList {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.liveFeedItem {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 13px;
    animation: fadeSlideIn 0.3s ease;
}

@keyframes fadeSlideIn {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.liveFeedName {
    flex: 1;
    font-weight: 600;
    color: var(--text-primary);
}

.liveFeedStatut {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;

    &.a_lheure,
    &.punctual {
        background: var(--success-dim);
        color: var(--success);
    }
    &.en_avance {
        background: rgba(167, 139, 250, 0.15);
        color: #a78bfa;
    }
    &.en_retard,
    &.late {
        background: var(--error-dim);
        color: var(--error);
    }
    &.absent {
        background: rgba(245, 166, 35, 0.12);
        color: #f5a623;
    }
}

.liveFeedTime {
    font-size: 11px;
    color: var(--text-muted);
    font-variant-numeric: tabular-nums;
}

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

    .sidebarBackdrop {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 40;
    }

    :deep(.adminSidebar) {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: min(280px, 85vw);
        z-index: 50;
        transform: translateX(-100%);
        transition: transform 0.25s ease;
        overflow-y: auto;

        &.open {
            transform: translateX(0);
        }
    }

    .sidebarLabel {
        display: block;
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
