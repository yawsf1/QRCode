<script setup>
import { computed } from "vue";
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
    // We now accept a pre-formatted array of datasets for complete control
    datasets: {
        type: Array,
        required: true,
    },
});

// Format datasets with high-fidelity defaults while preserving custom backend styling
const chartData = computed(() => ({
    labels: props.labels,
    datasets: props.datasets.map((dataset) => ({
        label: dataset.label,
        data: dataset.data,
        borderColor: dataset.color,
        borderWidth: 3,
        tension: 0.35,
        fill: true,

        // Advanced Dynamic Linear Gradient for multi-lines
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
            gradient.addColorStop(0, `${dataset.color}25`); // Lower opacity to avoid color mixing clutter
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

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true, // Set to true now to differentiate multiple lines
            position: "top",
            align: "end",
            labels: {
                boxWidth: 12,
                usePointStyle: true,
                pointStyle: "circle",
                font: {
                    size: 12,
                    family: "'Inter', sans-serif",
                    weight: "500",
                },
                padding: 20,
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
                font: {
                    size: 11,
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
                font: { size: 11, family: "'Inter', sans-serif" },
                stepSize: 1,
                precision: 0,
            },
            border: { dash: [4, 4], display: false },
        },
    },
    interaction: {
        intersect: false,
        mode: "index", // Tooltip displays BOTH users data when hovering an index column
    },
};
</script>

<template>
    <div
        class="chart-container"
        style="position: relative; height: 340px; width: 100%"
    >
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
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #94a3b8;
    gap: 8px;
}
</style>
