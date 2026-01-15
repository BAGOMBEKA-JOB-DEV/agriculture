<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ title }}</h3>
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
  title: {
      type: String,
      default: 'Market Presence Overview'
  },
  products: {
    type: Array,
    default: () => []
  },
  rfqs: {
    type: Array,
    default: () => []
  },
  labelA: {
    type: String,
    default: 'Active Listings'
  },
  labelB: {
    type: String,
    default: 'Procurement RFQs'
  },
  valueKeyA: {
    type: String,
    default: null // If null, count occurrences. If set, plot average of this key.
  },
  valueKeyB: {
    type: String,
    default: null
  }
})

const chartData = computed(() => {
  // Helper to detect granularity and group
  const processData = (data, valueKey) => {
    if (!data || data.length === 0) return {};
    
    // Sort by date
    const sorted = [...data].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    const start = new Date(sorted[0].created_at);
    const end = new Date(sorted[sorted.length - 1].created_at);
    const diffHours = (end - start) / (1000 * 60 * 60);
    
    let formatKey;
    if (diffHours <= 3) {
        // Less than 3 hours, show minutes
        formatKey = (d) => {
            const date = new Date(d);
            return date.getHours() + ':' + String(date.getMinutes()).padStart(2, '0');
        };
    } else if (diffHours <= 24) {
        formatKey = (d) => {
            const date = new Date(d);
            return date.getHours() + ':00';
        };
    } else if (diffHours <= 60 * 24) { // 60 days
         formatKey = (d) => {
             const date = new Date(d);
             return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
         };
    } else {
         formatKey = (d) => {
             const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
             return months[new Date(d).getMonth()];
         };
    }

    const groups = {};
    const counts = {}; // Track count for averaging

    sorted.forEach(item => {
        if (!item.created_at) return;
        const key = formatKey(item.created_at);
        
        if (valueKey) {
            groups[key] = (groups[key] || 0) + (parseFloat(item[valueKey]) || 0);
            counts[key] = (counts[key] || 0) + 1;
        } else {
            groups[key] = (groups[key] || 0) + 1;
        }
    });

    // Average if valueKey (Keep precision for small metrics like CPU Load)
    if (valueKey) {
        Object.keys(groups).forEach(k => {
            groups[k] = Number((groups[k] / counts[k]).toFixed(2));
        });
    }

    return groups;
  };

  const mpA = processData(props.products, props.valueKeyA);
  const mpB = processData(props.rfqs, props.valueKeyB);
  
  // Merge labels
  const allKeys = [...new Set([...Object.keys(mpA), ...Object.keys(mpB)])];
  // Sort keys isn't trivial since meaningful order was lost in Set, but provided data was chronological.
  // We'll rely on the order appearing in the data or simple alphabetical/time sort if standard format.
  // For 'HH:00', simple sort works. For 'Jan', need map.
  // Quick fix: assuming lists are relatively sorted.
  const labels = allKeys; // Simplified

  const dataA = labels.map(l => mpA[l] || 0);
  const dataB = labels.map(l => mpB[l] || 0);

  return {
    labels,
    datasets: [
      {
        label: props.labelA,
        data: dataA,
        borderColor: '#1B4332',
        backgroundColor: '#1B4332',
        tension: 0.4,
        fill: false
      },
      {
        label: props.labelB,
        data: dataB,
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
