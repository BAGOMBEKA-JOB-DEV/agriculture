<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ title }}</h3>
    <div class="h-64">
      <Doughnut :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
  title: {
      type: String,
      default: 'Availability Distribution'
  },
  products: {
    type: Array,
    default: () => []
  },
  groupByKey: {
    type: String, // Key to group by, e.g., 'status', 'role', 'category.name'
    default: 'status'
  }
})

const chartData = computed(() => {
  const statusCounts = props.products.reduce((acc, item) => {
    // Resolve deep keys? For now simple key.
    // If key is 'roles', handle array?
    let key = item[props.groupByKey] || 'Unknown';
    if (props.groupByKey === 'roles' && Array.isArray(key)) {
        key = key[0]?.name || 'User';
    }
    // Deep access hack (e.g. category.name)
    if (props.groupByKey.includes('.')) {
        key = props.groupByKey.split('.').reduce((o, i) => o?.[i], item) || 'Unknown';
    }
    
    // Normalize string
    if (typeof key === 'string') key = key.toLowerCase();
    
    acc[key] = (acc[key] || 0) + 1
    return acc
  }, {})

  return {
    labels: Object.keys(statusCounts).map(status =>
      status.charAt(0).toUpperCase() + status.slice(1)
    ),
    datasets: [{
      data: Object.values(statusCounts),
      backgroundColor: [
        '#1B4332', // Deep Forest
        '#2D6A4F', // Lush Green
        '#D4A373', // Earth Sand
        '#6B7280'  // Neutral Gray
      ],
      borderWidth: 2,
      borderColor: '#ffffff'
    }]
  }
})

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        padding: 20,
        usePointStyle: true
      }
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const label = context.label || ''
          const value = context.parsed || 0
          const total = context.dataset.data.reduce((a, b) => a + b, 0)
          const percentage = Math.round((value / total) * 100)
          return `${label}: ${value} (${percentage}%)`
        }
      }
    }
  }
})
</script>
