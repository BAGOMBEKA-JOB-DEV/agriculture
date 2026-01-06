<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-medium text-gray-900 mb-4">RFQ Activity Distribution</h3>
    <div class="h-64">
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({
  rfqs: {
    type: Array,
    default: () => []
  }
})

const chartData = computed(() => {
  const statusCounts = props.rfqs.reduce((acc, rfq) => {
    acc[rfq.status] = (acc[rfq.status] || 0) + 1
    return acc
  }, {})

  return {
    labels: Object.keys(statusCounts).map(status =>
      status.charAt(0).toUpperCase() + status.slice(1)
    ),
    datasets: [{
      label: 'Request for Quotes',
      data: Object.values(statusCounts),
      backgroundColor: [
        '#1B4332', // Deep Forest
        '#2D6A4F', // Lush Green
        '#D4A373', // Earth Sand
        '#EF4444'  // Alert Red
      ],
      borderWidth: 1,
      borderColor: '#ffffff'
    }]
  }
})

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `RFQs: ${context.parsed.y}`
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        stepSize: 1
      }
    }
  }
})
</script>
