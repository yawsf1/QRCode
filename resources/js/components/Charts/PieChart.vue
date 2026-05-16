<script setup>
import { computed } from "vue";
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

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: "right",
            align: "center",
            labels: {
                boxWidth: 12,
                usePointStyle: true,
                pointStyle: "circle",
                font: {
                    size: 12,
                    family: "'Inter', sans-serif",
                    weight: "500",
                },
                padding: 16,
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
                    return ` ${context.label}: ${value} employés (${percentage}%)`;
                },
            },
        },
    },
};
</script>

<template>
    <div
        class="chart-container"
        style="position: relative; height: 300px; width: 100%"
    >
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
