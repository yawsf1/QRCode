<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import { Line } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    PointElement,
    LineElement,
    CategoryScale,
    LinearScale,
    Filler,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    PointElement,
    LineElement,
    CategoryScale,
    LinearScale,
    Filler,
);

const props = defineProps({
    labels: {
        type: Array,
        required: true,
    },
    datasets: {
        type: Array,
        required: true,
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

const chartData = computed(() => ({
    labels: props.labels,
    datasets: props.datasets.map((dataset) => ({
        label: dataset.label,
        data: dataset.data,
        borderColor: dataset.color,
        borderWidth: 3,
        tension: 0.35,
        fill: true,
        backgroundColor: (context) => {
            const chart = context.chart;
            const { ctx, chartArea } = chart;
            if (!chartArea) return null;

            const gradient = ctx.createLinearGradient(
                0,
                chartArea.top,
                0,
                chartArea.bottom,
            );
            gradient.addColorStop(0, `${dataset.color}25`);
            gradient.addColorStop(1, `${dataset.color}00`);
            return gradient;
        },
        pointBackgroundColor: "#ffffff",
        pointBorderColor: dataset.color,
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHitRadius: 20,
        pointHoverRadius: 6,
        pointHoverBorderWidth: 3,
        pointHoverBackgroundColor: dataset.color,
        pointHoverBorderColor: "#ffffff",
    })),
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: isMobile.value ? "bottom" : "top",
            align: isMobile.value ? "center" : "end",
            labels: {
                boxWidth: 12,
                usePointStyle: true,
                pointStyle: "circle",
                font: {
                    size: isMobile.value ? 10 : 12,
                    family: "'Inter', sans-serif",
                    weight: "500",
                },
                padding: isMobile.value ? 12 : 20,
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
                    return ` ${context.dataset.label}: ${context.raw}`;
                },
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: {
                color: "#64748b",
                maxRotation: isMobile.value ? 45 : 0,
                minRotation: isMobile.value ? 45 : 0,
                font: {
                    size: isMobile.value ? 9 : 11,
                    family: "'Inter', sans-serif",
                    weight: "500",
                },
            },
            border: { display: false },
        },
        y: {
            min: 0,
            grid: { color: "#f1f5f9" },
            ticks: {
                color: "#64748b",
                font: { size: isMobile.value ? 9 : 11, family: "'Inter', sans-serif" },
                stepSize: 1,
                precision: 0,
            },
            border: { dash: [4, 4], display: false },
        },
    },
    interaction: {
        intersect: false,
        mode: "index",
    },
}));
</script>

<template>
    <div class="chart-container">
        <Line
            v-if="props.labels.length > 0"
            :data="chartData"
            :options="chartOptions"
        />
        <div v-else class="empty-state">
            <span class="material-symbols-rounded">monitoring</span>
            <p>Aucune donnée disponible</p>
        </div>
    </div>
</template>

<style scoped>
.chart-container {
    position: relative;
    width: 100%;
    height: clamp(220px, 45vw, 340px);
    min-height: 220px;
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
        height: clamp(200px, 55vw, 280px);
        min-height: 200px;
    }
}
</style>
