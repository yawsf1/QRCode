<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import { Pie } from "vue-chartjs";
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps({
    labels: {
        type: Array,
        required: true,
    },
    employeeCounts: {
        type: Array,
        required: true,
    },
    suffix: {
        type: String,
        default: "employés",
    },
});

const isMobile = ref(false);

const updateBreakpoint = () => {
    isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
    updateBreakpoint();
    window.addEventListener("resize", updateBreakpoint);
});

onUnmounted(() => {
    window.removeEventListener("resize", updateBreakpoint);
});

const pieColors = ["#4f46e5", "#06b6d4", "#10b981", "#f59e0b", "#ec4899"];

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            data: props.employeeCounts,
            backgroundColor: pieColors.slice(0, props.labels.length),
            borderColor: "#ffffff",
            borderWidth: 2,
            hoverBorderWidth: 3,
            hoverOffset: 8,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: isMobile.value ? "bottom" : "right",
            align: "center",
            labels: {
                boxWidth: 12,
                usePointStyle: true,
                pointStyle: "circle",
                font: {
                    size: isMobile.value ? 10 : 12,
                    family: "'Inter', sans-serif",
                    weight: "500",
                },
                padding: isMobile.value ? 10 : 16,
            },
        },
        tooltip: {
            backgroundColor: "#0f172a",
            titleColor: "#94a3b8",
            bodyColor: "#ffffff",
            titleFont: {
                size: 12,
                weight: "600",
                family: "'Inter', sans-serif",
            },
            bodyFont: {
                size: 13,
                weight: "500",
                family: "'Inter', sans-serif",
            },
            padding: 12,
            cornerRadius: 8,
            boxPadding: 6,
            usePointStyle: true,
            callbacks: {
                label: function (context) {
                    const value = context.raw;
                    const total = context.dataset.data.reduce(
                        (acc, curr) => acc + curr,
                        0,
                    );
                    const percentage =
                        total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                    return ` ${context.label}: ${value} ${props.suffix} (${percentage}%)`;
                },
            },
        },
    },
}));
</script>

<template>
    <div class="chart-container">
        <Pie
            v-if="props.labels.length > 0"
            :data="chartData"
            :options="chartOptions"
        />
        <div v-else class="empty-state">
            <span class="material-symbols-rounded">pie_chart</span>
            <p>Aucune donnée disponible</p>
        </div>
    </div>
</template>

<style scoped>
.chart-container {
    position: relative;
    width: 100%;
    height: clamp(240px, 50vw, 320px);
    min-height: 240px;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #94a3b8;
    gap: 8px;
}

@media (max-width: 768px) {
    .chart-container {
        height: clamp(260px, 60vw, 300px);
        min-height: 260px;
    }
}
</style>
