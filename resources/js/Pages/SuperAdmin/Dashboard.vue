<script setup>
import { route } from "ziggy-js";
import { router } from "@inertiajs/vue3";
import LineChart from "../../components/Charts/LineChart.vue";
import PieChart from "../../components/Charts/PieChart.vue";
import AppBrand from "../../components/Layout/AppBrand.vue";
import DashboardSidebarLogout from "../../components/Layout/DashboardSidebarLogout.vue";
import { computed, ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    months: Array,
    salesData: Array,
    salesData2: Array,
    entrepriseNames: Array,
    employeeCounts: Array,
    currentMonthIndex: { type: Number, default: () => new Date().getMonth() },
});

const liveSalesData = ref([...(props.salesData || [])]);
const liveSalesData2 = ref([...(props.salesData2 || [])]);
const liveEntrepriseNames = ref([...(props.entrepriseNames || [])]);
const liveEmployeeCounts = ref([...(props.employeeCounts || [])]);

const monthIndex = props.currentMonthIndex ?? new Date().getMonth();

onMounted(() => {
    window.Echo.channel("platform-channel").listen(".stats-updated", (e) => {
        const { type, payload } = e;

        if (type === "admin_created") {
            const idx = payload?.monthIndex ?? monthIndex;
            liveSalesData.value[idx] = (liveSalesData.value[idx] || 0) + 1;
        }

        if (type === "employe_created") {
            const idx = payload?.monthIndex ?? monthIndex;
            liveSalesData2.value[idx] = (liveSalesData2.value[idx] || 0) + 1;

            if (payload?.departement) {
                const nameIdx = liveEntrepriseNames.value.indexOf(
                    payload.departement,
                );
                if (nameIdx >= 0) {
                    liveEmployeeCounts.value[nameIdx]++;
                }
            }
        }

        if (type === "employe_deleted" && payload?.departement) {
            const nameIdx = liveEntrepriseNames.value.indexOf(
                payload.departement,
            );
            if (nameIdx >= 0 && liveEmployeeCounts.value[nameIdx] > 0) {
                liveEmployeeCounts.value[nameIdx]--;
            }
        }
    });
});

onUnmounted(() => {
    window.Echo.leaveChannel("platform-channel");
});

const chartDatasets = computed(() => [
    {
        label: "Administrateurs",
        data: liveSalesData.value,
        color: "#4f7cff",
    },
    {
        label: "Employés",
        data: liveSalesData2.value,
        color: "#06b6d4",
    },
]);

const totalAdminsThisYear = computed(() =>
    liveSalesData.value.reduce((acc, curr) => acc + curr, 0),
);

const totalEmployesThisYear = computed(() =>
    liveSalesData2.value.reduce((acc, curr) => acc + curr, 0),
);
</script>

<template>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="sidebarMain">
                <AppBrand class="sidebarBrand" />
                <p class="sidebarLabel">Super administrateur</p>

                <button
                    class="navBtn active"
                    @click="router.visit(route('super-admin.dashboard'))"
                >
                    <span class="material-symbols-rounded">dashboard</span>
                    Tableau de bord
                </button>

                <button
                    class="navBtn"
                    @click="router.visit(route('admin.register.form'))"
                >
                    <span class="material-symbols-rounded">person_add</span>
                    Ajouter un admin
                </button>

                <button
                    class="navBtn"
                    @click="router.visit(route('admin.list'))"
                >
                    <span class="material-symbols-rounded">group</span>
                    Afficher les admins
                </button>
            </div>

            <DashboardSidebarLogout />
        </aside>

        <main class="content">
            <div class="pageHeader">
                <h1 class="pageTitle">Tableau de bord</h1>
                <p class="pageSubtitle">
                    Vue d'ensemble de la plateforme
                    <span class="liveBadge">● Temps réel</span>
                </p>
            </div>

            <div class="dashboard-grid">
                <div class="chartCard line-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">Année en cours</span>
                            <h2 class="cardTitle">
                                Évolution des créations de comptes
                            </h2>
                        </div>
                        <div class="statSummaryContainer">
                            <div class="statSummary adminColor">
                                <span class="statLabel">Total admins</span>
                                <span class="statValue">{{
                                    totalAdminsThisYear
                                }}</span>
                            </div>
                            <div class="statSummary employeColor">
                                <span class="statLabel">Total employés</span>
                                <span class="statValue">{{
                                    totalEmployesThisYear
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="chartContainer">
                        <LineChart :labels="months" :datasets="chartDatasets" />
                    </div>
                </div>

                <div class="chartCard pie-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">Effectifs globaux</span>
                            <h2 class="cardTitle">Top 5 entreprises</h2>
                            <p class="cardSubtitle">
                                Classement des structures par nombre d'employés
                            </p>
                        </div>
                    </div>

                    <div class="chartContainer">
                        <PieChart
                            :labels="liveEntrepriseNames"
                            :employeeCounts="liveEmployeeCounts"
                        />
                    </div>
                </div>
            </div>

            <slot />
        </main>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap");

.dashboard {
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

    font-family: "Sora", sans-serif;
    display: flex;
    width: 100%;
    max-width: 100%;
    min-height: 100vh;
    background: var(--bg);
    color: var(--text-primary);
    box-sizing: border-box;
}

.sidebar {
    width: 220px;
    flex-shrink: 0;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 24px 12px;
    min-height: 100vh;
    gap: 4px;
}

.sidebarMain {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebarBrand {
    margin-bottom: 28px;
}

.sidebarLabel {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0 8px 12px;
    margin: 0;
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
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.15s ease;
    text-align: left;
    width: 100%;
    font-family: inherit;

    span {
        font-size: 18px;
        color: var(--text-muted);
    }

    &:hover {
        background: var(--surface2);
        color: var(--text-primary);
        span {
            color: var(--text-secondary);
        }
    }

    &.active {
        background: rgba(255, 255, 255, 0.08);
        color: var(--text-primary);
        font-weight: 600;
        span {
            color: var(--accent);
        }
    }
}

.content {
    flex: 1;
    background: var(--bg);
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    min-width: 0;
    overflow-y: auto;
}

.pageHeader {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.pageTitle {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.5px;
}

.pageSubtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
}

.liveBadge {
    color: #22c97a;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1.6fr 1.4fr;
    gap: 24px;
    width: 100%;
}

.chartCard {
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.chartHeader {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    gap: 16px;
}

.meta {
    min-width: 0;
}

.badge {
    display: inline-block;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    margin-bottom: 8px;
    letter-spacing: 0.03em;
}

.cardTitle {
    font-size: 16px;
    font-weight: 700;
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
    gap: 24px;
    flex-shrink: 0;
}

.statSummary {
    display: flex;
    flex-direction: column;
    text-align: right;
}

.statLabel {
    font-size: 11px;
    color: var(--text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.statValue {
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
    margin-top: 2px;
}

.adminColor .statValue {
    color: var(--accent);
}
.employeColor .statValue {
    color: #06b6d4;
}

.chartContainer {
    margin-top: auto;
    width: 100%;
    min-width: 0;
    min-height: 260px;
    position: relative;
    overflow-x: auto;
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
        border-bottom: 1px solid var(--border);
    }
    .sidebarLabel {
        display: none;
    }
    .content {
        padding: 20px 16px;
    }
    .chartHeader {
        flex-direction: column;
        align-items: stretch;
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
