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
        color: "#4f7cff",
    },
]);

const totalLateMinutesThisWeek = computed(() => {
    return (props.chartData?.delays || []).reduce((acc, curr) => acc + curr, 0);
});
</script>

<template>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebarTop">
                <div class="brand">
                    <div class="logoMark">
                        <span class="material-symbols-rounded">qr_code_2</span>
                    </div>
                    <span class="logoText"
                        >QR<span class="thin">Coded</span></span
                    >
                </div>

                <div class="sidebarSection">
                    <span class="sectionLabel">Espace Employé</span>
                    <button
                        class="navBtn active"
                        @click="router.visit(route('employe.dashboard'))"
                    >
                        <span class="material-symbols-rounded">grid_view</span>
                        Tableau de bord
                    </button>
                    <Link
                        v-if="!props.checkedInToday"
                        :href="route('employe.scan.form')"
                        class="navBtn scan"
                    >
                        <span class="material-symbols-rounded"
                            >qr_code_scanner</span
                        >
                        Scanner QR
                    </Link>
                </div>
            </div>
        </aside>

        <main class="content">
            <div class="pageHeader">
                <div>
                    <h1 class="pageTitle">Tableau de bord</h1>
                    <p class="pageSubtitle">
                        Suivi analytique de vos présences et ponctualité
                    </p>
                </div>
                <div class="headerRight">
                    <div class="todayBadge">
                        <span class="material-symbols-rounded">today</span>
                        {{
                            new Date().toLocaleDateString("fr-MA", {
                                weekday: "long",
                                day: "numeric",
                                month: "long",
                            })
                        }}
                    </div>
                </div>
            </div>

            <section
                class="statusCard"
                :class="{
                    done: props.checkedInToday,
                    pending: !props.checkedInToday,
                }"
            >
                <div class="statusLeft">
                    <div class="statusIconWrap">
                        <span class="material-symbols-rounded">
                            {{
                                props.checkedInToday
                                    ? "verified"
                                    : "pending_actions"
                            }}
                        </span>
                    </div>
                    <div class="statusText">
                        <h2>
                            {{
                                props.checkedInToday
                                    ? "Présence enregistrée"
                                    : "Pointage requis"
                            }}
                        </h2>
                        <p>
                            {{
                                props.checkedInToday
                                    ? "Votre journée de travail a été validée avec succès."
                                    : "Veuillez scanner le code QR de la borne de votre administrateur."
                            }}
                        </p>
                    </div>
                </div>
                <div v-if="props.checkedInToday" class="statusBadge">
                    <span class="material-symbols-rounded">check_circle</span>
                    En règle aujourd'hui
                </div>
                <Link v-else :href="route('employe.scan.form')" class="scanCta">
                    <span class="material-symbols-rounded"
                        >qr_code_scanner</span
                    >
                    Scanner maintenant
                </Link>
            </section>

            <div class="kpiRow">
                <div class="kpiCard">
                    <span class="kpiIcon blue">
                        <span class="material-symbols-rounded"
                            >calendar_check</span
                        >
                    </span>
                    <div>
                        <div class="kpiVal">
                            {{ props.stats?.total_days || 0 }}
                        </div>
                        <div class="kpiLabel">Jours pointés</div>
                    </div>
                </div>
                <div class="kpiCard">
                    <span class="kpiIcon red">
                        <span class="material-symbols-rounded">schedule</span>
                    </span>
                    <div>
                        <div class="kpiVal">
                            {{ totalLateMinutesThisWeek
                            }}<span class="kpiUnit">min</span>
                        </div>
                        <div class="kpiLabel">Retard cumulé</div>
                    </div>
                </div>
                <div class="kpiCard">
                    <span class="kpiIcon green">
                        <span class="material-symbols-rounded"
                            >trending_up</span
                        >
                    </span>
                    <div>
                        <div class="kpiVal">
                            {{ props.stats?.punctuality_rate || "100%" }}
                        </div>
                        <div class="kpiLabel">Taux de ponctualité</div>
                    </div>
                </div>
                <div class="kpiCard">
                    <span class="kpiIcon amber">
                        <span class="material-symbols-rounded">event_busy</span>
                    </span>
                    <div>
                        <div class="kpiVal">
                            {{ props.stats?.absent_days || 0 }}
                        </div>
                        <div class="kpiLabel">Absences</div>
                    </div>
                </div>
            </div>

            <div class="chartsGrid">
                <div class="chartCard">
                    <div class="chartCardHeader">
                        <div>
                            <span class="chartTag">Rapport hebdomadaire</span>
                            <h3 class="chartTitle">Évolution des retards</h3>
                        </div>
                        <div class="chartMeta">
                            <span class="chartMetaVal red"
                                >{{ totalLateMinutesThisWeek }}m</span
                            >
                            <span class="chartMetaLabel">cette semaine</span>
                        </div>
                    </div>
                    <div class="chartWrap">
                        <LineChart
                            :labels="props.chartData?.labels || []"
                            :datasets="chartDatasets"
                        />
                    </div>
                </div>

                <div class="chartCard">
                    <div class="chartCardHeader">
                        <div>
                            <span class="chartTag">Statistiques globales</span>
                            <h3 class="chartTitle">Répartition ponctualité</h3>
                        </div>
                    </div>
                    <div class="chartWrap">
                        <PieChart
                            :labels="props.pieLabels || []"
                            :employeeCounts="props.pieCounts || []"
                            suffix="jour(s)"
                        />
                    </div>
                </div>
            </div>

            <section class="historySection">
                <div class="historyHeader">
                    <span class="material-symbols-rounded">history</span>
                    <h4>Historique récent des pointages</h4>
                </div>

                <div v-if="props.history.length === 0" class="emptyState">
                    <span class="material-symbols-rounded">rule</span>
                    <p>
                        Aucun enregistrement de présence trouvé pour le moment.
                    </p>
                </div>

                <div v-else class="historyList">
                    <div
                        v-for="log in props.history"
                        :key="log.id"
                        class="historyItem"
                    >
                        <div class="historyLeft">
                            <div class="statusDot" :class="log.statut"></div>
                            <div>
                                <p class="logDate">{{ log.date }}</p>
                                <p class="logTime">
                                    <template v-if="log.statut === 'absent'"
                                        >Non présenté</template
                                    >
                                    <template v-else
                                        >Scan à {{ log.heure }}</template
                                    >
                                </p>
                            </div>
                        </div>
                        <span class="pill" :class="log.statut">
                            <template
                                v-if="
                                    log.statut === 'a_lheure' ||
                                    log.statut === 'punctual'
                                "
                            >
                                À l'heure
                            </template>
                            <template v-else-if="log.statut === 'en_avance'">
                                En avance
                            </template>
                            <template v-else-if="log.statut === 'absent'">
                                Absent
                            </template>
                            <template
                                v-else-if="
                                    log.statut === 'en_retard' ||
                                    log.statut === 'late'
                                "
                            >
                                En retard
                            </template>
                            <template v-else>
                                {{ log.statut }}
                            </template>
                        </span>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap");

.dashboard {
    --bg: #0a0a0f;
    --surface: #111118;
    --surface2: #16161f;
    --surface3: #1c1c27;
    --border: rgba(255, 255, 255, 0.07);
    --border-strong: rgba(255, 255, 255, 0.12);
    --text-primary: #f0f0f8;
    --text-secondary: #8888aa;
    --text-muted: #55556a;
    --accent: #4f7cff;
    --accent-dim: rgba(79, 124, 255, 0.12);
    --green: #22c97a;
    --green-dim: rgba(34, 201, 122, 0.12);
    --red: #ff6b6b;
    --red-dim: rgba(255, 107, 107, 0.12);
    --amber: #f5a623;
    --amber-dim: rgba(245, 166, 35, 0.12);
    --purple: #a78bfa;

    font-family: "Sora", sans-serif;
    display: flex;
    width: 100%;
    min-height: calc(100vh - 60px);
    background: var(--bg);
    color: var(--text-primary);
}

* {
    box-sizing: border-box;
}

/* ===========================
   SIDEBAR
   =========================== */
.sidebar {
    width: 224px;
    flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px 12px;
    position: sticky;
    top: 60px;
    height: calc(100vh - 60px);
}

.sidebarTop {
    display: flex;
    flex-direction: column;
    gap: 28px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 8px;

    .logoMark {
        width: 32px;
        height: 32px;
        background: var(--accent);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        span {
            font-size: 17px;
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

.sidebarSection {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.sectionLabel {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0 10px 8px;
}

.navBtn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 8px;
    border: none;
    background: transparent;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    text-decoration: none;
    width: 100%;
    transition:
        background 0.15s,
        color 0.15s;

    span.material-symbols-rounded {
        font-size: 17px;
        color: var(--text-muted);
        transition: color 0.15s;
    }

    &:hover {
        background: var(--surface2);
        color: var(--text-primary);
        span.material-symbols-rounded {
            color: var(--text-secondary);
        }
    }

    &.active {
        background: var(--accent-dim);
        color: var(--text-primary);
        span.material-symbols-rounded {
            color: var(--accent);
        }
    }

    &.scan {
        color: var(--accent);
        span.material-symbols-rounded {
            color: var(--accent);
        }
        &:hover {
            background: var(--accent-dim);
        }
    }
}

.logoutBtn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    width: 100%;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: transparent;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.15s;
    span {
        font-size: 17px;
    }

    &:hover {
        border-color: rgba(255, 107, 107, 0.3);
        color: var(--red);
        background: var(--red-dim);
    }
}

/* ===========================
   MAIN CONTENT
   =========================== */
.content {
    flex: 1;
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    overflow-y: auto;
    background: var(--bg);
}

/* Page Header */
.pageHeader {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pageTitle {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.5px;
    margin: 0 0 4px;
    color: var(--text-primary);
}

.pageSubtitle {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0;
}

.todayBadge {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 14px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    span {
        font-size: 16px;
        color: var(--text-muted);
    }
    text-transform: capitalize;
}

/* Status Card */
.statusCard {
    border-radius: 14px;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid var(--border);

    &.done {
        background: linear-gradient(
            135deg,
            rgba(34, 201, 122, 0.06) 0%,
            var(--surface) 60%
        );
        border-color: rgba(34, 201, 122, 0.2);
    }
    &.pending {
        background: linear-gradient(
            135deg,
            rgba(79, 124, 255, 0.06) 0%,
            var(--surface) 60%
        );
        border-color: rgba(79, 124, 255, 0.2);
    }
}

.statusLeft {
    display: flex;
    align-items: center;
    gap: 16px;
}

.statusIconWrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--surface2);
    border: 1px solid var(--border);

    .done & {
        background: var(--green-dim);
        border-color: rgba(34, 201, 122, 0.2);
        span {
            color: var(--green);
        }
    }
    .pending & {
        background: var(--accent-dim);
        border-color: rgba(79, 124, 255, 0.2);
        span {
            color: var(--accent);
        }
    }

    span {
        font-size: 24px;
        color: var(--text-secondary);
    }
}

.statusText {
    h2 {
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 4px;
        color: var(--text-primary);
    }
    p {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
    }
}

.statusBadge {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--green-dim);
    color: var(--green);
    border: 1px solid rgba(34, 201, 122, 0.2);
    font-size: 12px;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
    span {
        font-size: 16px;
    }
}

.scanCta {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--accent);
    color: #fff;
    font-family: "Sora", sans-serif;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 18px;
    border-radius: 9px;
    text-decoration: none;
    transition:
        background 0.15s,
        box-shadow 0.15s;
    box-shadow: 0 4px 16px rgba(79, 124, 255, 0.25);
    span {
        font-size: 18px;
    }
    &:hover {
        background: #6b93ff;
        box-shadow: 0 6px 24px rgba(79, 124, 255, 0.4);
    }
}

/* KPI Row */
.kpiRow {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
}

.kpiCard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: border-color 0.15s;
    &:hover {
        border-color: var(--border-strong);
    }
}

.kpiIcon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    span {
        font-size: 20px;
    }

    &.blue {
        background: var(--accent-dim);
        span {
            color: var(--accent);
        }
    }
    &.red {
        background: var(--red-dim);
        span {
            color: var(--red);
        }
    }
    &.green {
        background: var(--green-dim);
        span {
            color: var(--green);
        }
    }
    &.amber {
        background: var(--amber-dim);
        span {
            color: var(--amber);
        }
    }
}

.kpiVal {
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 3px;
    font-family: "Sora", sans-serif;
}

.kpiUnit {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-muted);
    margin-left: 2px;
}

.kpiLabel {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
}

/* Charts Grid */
.chartsGrid {
    display: grid;
    grid-template-columns: 1.6fr 1.4fr;
    gap: 16px;
}

.chartCard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.chartCardHeader {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.chartTag {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--accent);
    background: var(--accent-dim);
    padding: 3px 8px;
    border-radius: 4px;
    display: inline-block;
    margin-bottom: 6px;
}

.chartTitle {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.3px;
}

.chartMeta {
    text-align: right;
    .chartMetaVal {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        display: block;
        line-height: 1;
        &.red {
            color: var(--red);
        }
    }
    .chartMetaLabel {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
    }
}

.chartWrap {
    width: 100%;
}

/* History */
.historySection {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.historyHeader {
    display: flex;
    align-items: center;
    gap: 8px;
    span {
        font-size: 17px;
        color: var(--text-muted);
    }
    h4 {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        margin: 0;
        letter-spacing: 0.01em;
    }
}

.emptyState {
    background: var(--surface);
    border: 1px dashed var(--border-strong);
    border-radius: 14px;
    padding: 48px 20px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    span {
        font-size: 36px;
        color: var(--text-muted);
    }
    p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }
}

.historyList {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.historyItem {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: border-color 0.15s;
    &:hover {
        border-color: var(--border-strong);
    }
}

.historyLeft {
    display: flex;
    align-items: center;
    gap: 12px;
}

.statusDot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
    &.punctual,
    &.a_lheure {
        background: var(--green);
    }
    &.en_avance {
        background: #38bdf8;
    }
    &.absent {
        background: var(--amber);
    }
    &.late,
    &.en_retard {
        background: var(--red);
    }
}

.logDate {
    font-size: 13px;
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
}
.logTime {
    font-size: 11px;
    color: var(--text-muted);
    margin: 2px 0 0;
    font-family: "DM Mono", monospace;
}

.pill {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 11px;
    border-radius: 20px;
    border: 1px solid transparent;

    &.punctual,
    &.a_lheure {
        background: var(--green-dim);
        color: var(--green);
        border-color: rgba(34, 201, 122, 0.2);
    }
    &.en_avance {
        background: rgba(56, 189, 248, 0.1);
        color: #38bdf8;
        border-color: rgba(56, 189, 248, 0.2);
    }
    &.absent {
        background: var(--amber-dim);
        color: var(--amber);
        border-color: rgba(245, 166, 35, 0.2);
    }
    &.late,
    &.en_retard {
        background: var(--red-dim);
        color: var(--red);
        border-color: rgba(255, 107, 107, 0.2);
    }
}

/* ===========================
   RESPONSIVE
   =========================== */
@media (max-width: 1200px) {
    .chartsGrid {
        grid-template-columns: 1fr;
    }
    .kpiRow {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        height: auto;
        position: static;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px;
        border-right: none;
        border-bottom: 1px solid var(--border);

        .sidebarTop {
            flex-direction: row;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        .sidebarSection {
            flex-direction: row;
            gap: 4px;
        }
        .sectionLabel {
            display: none;
        }
    }

    .content {
        padding: 20px 16px;
    }
    .kpiRow {
        grid-template-columns: repeat(2, 1fr);
    }
    .statusCard {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    .pageHeader {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}

@media (max-width: 480px) {
    .kpiRow {
        grid-template-columns: 1fr 1fr;
    }
}
</style>
