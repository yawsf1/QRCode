<script setup>
import { route } from "ziggy-js";
import { router } from "@inertiajs/vue3";
import LineChart from "../../components/Charts/LineChart.vue";
import PieChart from "../../components/Charts/PieChart.vue";
import { computed } from "vue";

const props = defineProps({
    months: Array,
    salesData: Array,
    salesData2: Array,
    entrepriseNames: Array,
    employeeCounts: Array,
});

const chartDatasets = computed(() => [
    {
        label: "Administrateurs",
        data: props.salesData || [],
        color: "#4f46e5",
    },
    {
        label: "Employés",
        data: props.salesData2 || [],
        color: "#06b6d4",
    },
]);

const pieColors = ["#4f46e5", "#06b6d4", "#10b981", "#f59e0b", "#ec4899"];

const pieDatasets = computed(() => [
    {
        data: props.employeeCounts || [],
        colors: pieColors.slice(0, (props.entrepriseNames || []).length),
    },
]);

const totalAdminsThisYear = computed(() => {
    return (props.salesData || []).reduce((acc, curr) => acc + curr, 0);
});

const totalEmployesThisYear = computed(() => {
    return (props.salesData2 || []).reduce((acc, curr) => acc + curr, 0);
});
</script>

<template>
    <div class="dashboard">
        <aside class="sidebar">
            <p class="sidebarLabel">Super admin</p>

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

            <button class="navBtn" @click="router.visit(route('admin.list'))">
                <span class="material-symbols-rounded">group</span>
                Afficher les admins
            </button>
        </aside>

        <main class="content">
            <div class="pageHeader">
                <h1 class="pageTitle">Tableau de bord</h1>
                <p class="pageSubtitle">Vue d'ensemble de la plateforme</p>
            </div>

            <div class="dashboard-grid">
                <div class="chartCard line-card">
                    <div class="chartHeader">
                        <div class="meta">
                            <span class="badge">Rapport Annuel Global</span>
                            <h2 class="cardTitle">
                                Évolution des Créations de Comptes
                            </h2>
                        </div>
                        <div class="statSummaryContainer">
                            <div class="statSummary adminColor">
                                <span class="statLabel">Total Admins</span>
                                <span class="statValue">{{
                                    totalAdminsThisYear
                                }}</span>
                            </div>
                            <div class="statSummary employeColor">
                                <span class="statLabel">Total Employés</span>
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
                            <span class="badge">Effectifs Globaux</span>
                            <h2 class="cardTitle">Top 5 Entreprises</h2>
                            <p class="cardSubtitle">
                                Classement des structures par nombre d'employés
                            </p>
                        </div>
                    </div>

                    <div class="chartContainer">
                        <PieChart
                            :labels="props.entrepriseNames || []"
                            :employeeCounts="props.employeeCounts || []"
                        />
                    </div>
                </div>
            </div>

            <slot />
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
    gap: 24px;
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
    color: #0f172a;
    margin: 0;
}

.pageSubtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
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
.employeColor .statValue {
    color: #06b6d4;
}
.chartContainer {
    margin-top: auto;
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
}
</style>
