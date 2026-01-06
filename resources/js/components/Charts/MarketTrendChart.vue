<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Market Presence Overview</h3>
    <div class="h-64">
      <Line :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({
  products: {
    type: Array,
    default: () => []
  },
  rfqs: {
    type: Array,
    default: () => []
  }
})

const chartData = computed(() => {
  // Mock trend data based on products/rfqs counts if timeline is missing
  const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']
  
  return {
    labels,
    datasets: [
      {
        label: 'Active Listings',
        data: [65, 59, 80, 81, 56, 55, 40],
        borderColor: '#1B4332',
        backgroundColor: '#1B4332',
        tension: 0.4,
        fill: false
      },
      {
        label: 'Procurement RFQs',
        data: [28, 48, 40, 19, 86, 27, 90],
        borderColor: '#D4A373',
        backgroundColor: '#D4A373',
        tension: 0.4,
        fill: false
      }
    ]
  }
})

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top'
    },
    tooltip: {
      mode: 'index',
      intersect: false
    }
  },
  scales: {
    y: {
      beginAtZero: true
    },
    x: {
      ticks: {
        maxRotation: 45,
        minRotation: 45
      }
    }
  },
  interaction: {
    mode: 'nearest',
    axis: 'x',
    intersect: false
  }
})
</script>
