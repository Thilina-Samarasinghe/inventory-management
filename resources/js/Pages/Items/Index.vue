<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import type { Item } from '@/types/inventory';

interface Props {
  items: {
    data: Item[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search: string | null;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const filterStatus = ref<string>('all');

// Debounced search
let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get(route('items.index'), { search: value }, {
      preserveState: true,
      replace: true,
    });
  }, 500);
});

const deleteItem = (item: Item) => {
  if (confirm(`Are you sure you want to delete "${item.name}"?\n\nThis will permanently remove the item and all its transaction history.`)) {
    router.delete(route('items.destroy', item.id), {
      preserveScroll: true,
    });
  }
};

const getStockStatusColor = (status: string) => {
  switch (status) {
    case 'in_stock':
      return 'bg-emerald-100 text-emerald-700 border-emerald-200';
    case 'low_stock':
      return 'bg-amber-100 text-amber-700 border-amber-200';
    case 'out_of_stock':
      return 'bg-rose-100 text-rose-700 border-rose-200';
    default:
      return 'bg-gray-100 text-gray-700 border-gray-200';
  }
};

const getStockStatusGradient = (status: string) => {
  switch (status) {
    case 'in_stock':
      return 'from-emerald-500 to-emerald-600';
    case 'low_stock':
      return 'from-amber-500 to-orange-600';
    case 'out_of_stock':
      return 'from-rose-500 to-red-600';
    default:
      return 'from-gray-500 to-gray-600';
  }
};

const getStockStatusLabel = (status: string) => {
  switch (status) {
    case 'in_stock':
      return 'In Stock';
    case 'low_stock':
      return 'Low Stock';
    case 'out_of_stock':
      return 'Out of Stock';
    default:
      return 'Unknown';
  }
};

const getStockStatusIcon = (status: string) => {
  switch (status) {
    case 'in_stock':
      return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
    case 'low_stock':
      return 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
    case 'out_of_stock':
      return 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    default:
      return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
  }
};

// Filter items by status
const filteredItems = computed(() => {
  if (filterStatus.value === 'all') return props.items.data;
  return props.items.data.filter(item => item.stock_status === filterStatus.value);
});

// Statistics
const statsData = computed(() => {
  const data = props.items.data;
  return {
    total: data.length,
    inStock: data.filter(i => i.stock_status === 'in_stock').length,
    lowStock: data.filter(i => i.stock_status === 'low_stock').length,
    outOfStock: data.filter(i => i.stock_status === 'out_of_stock').length,
  };
});
</script>

<template>
  <Head title="Items" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            Inventory Items
          </h2>
          <p class="text-sm text-gray-600 mt-1">Manage and track all your inventory items</p>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <!-- Total Items -->
          <button
            @click="filterStatus = 'all'"
            :class="[
              'relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-left',
              filterStatus === 'all' ? 'ring-4 ring-blue-400 ring-opacity-50' : ''
            ]"
          >
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6">
              <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
              <div class="relative">
                <div class="flex items-center justify-between mb-3">
                  <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                </div>
                <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">Total Items</h3>
                <p class="text-4xl font-bold text-white">{{ statsData.total }}</p>
              </div>
            </div>
          </button>

          <!-- In Stock -->
          <button
            @click="filterStatus = 'in_stock'"
            :class="[
              'relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-left',
              filterStatus === 'in_stock' ? 'ring-4 ring-emerald-400 ring-opacity-50' : ''
            ]"
          >
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-6">
              <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
              <div class="relative">
                <div class="flex items-center justify-between mb-3">
                  <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">In Stock</h3>
                <p class="text-4xl font-bold text-white">{{ statsData.inStock }}</p>
              </div>
            </div>
          </button>

          <!-- Low Stock -->
          <button
            @click="filterStatus = 'low_stock'"
            :class="[
              'relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-left',
              filterStatus === 'low_stock' ? 'ring-4 ring-amber-400 ring-opacity-50' : ''
            ]"
          >
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-6">
              <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
              <div class="relative">
                <div class="flex items-center justify-between mb-3">
                  <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                  </div>
                </div>
                <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">Low Stock</h3>
                <p class="text-4xl font-bold text-white">{{ statsData.lowStock }}</p>
              </div>
            </div>
          </button>

          <!-- Out of Stock -->
          <button
            @click="filterStatus = 'out_of_stock'"
            :class="[
              'relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-left',
              filterStatus === 'out_of_stock' ? 'ring-4 ring-rose-400 ring-opacity-50' : ''
            ]"
          >
            <div class="bg-gradient-to-br from-rose-500 to-red-600 p-6">
              <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
              <div class="relative">
                <div class="flex items-center justify-between mb-3">
                  <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">Out of Stock</h3>
                <p class="text-4xl font-bold text-white">{{ statsData.outOfStock }}</p>
              </div>
            </div>
          </button>
        </div>

        <!-- Search & Actions Bar -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-2xl">
              <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input
                v-model="search"
                type="text"
                placeholder="Search items by name or description..."
                class="pl-12 pr-4 py-3 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-200"
              />
              <div v-if="search" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <button
                  @click="search = ''"
                  class="p-1 hover:bg-gray-100 rounded-lg transition-colors"
                >
                  <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
              <Link 
                :href="route('items.deduct.create')"
                class="group relative overflow-hidden flex items-center gap-2 px-5 py-3 bg-white hover:bg-gray-50 border-2 border-gray-300 hover:border-rose-500 rounded-xl font-semibold text-sm text-gray-700 hover:text-rose-700 shadow-sm hover:shadow-md transition-all duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                <span>Deduct Items</span>
              </Link>

              <Link 
                :href="route('items.create')"
                class="group relative overflow-hidden flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
              >
                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <svg class="w-5 h-5 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="relative">Add Items</span>
              </Link>
            </div>
          </div>

          <!-- Active Filter Indicator -->
          <div v-if="filterStatus !== 'all' || search" class="mt-4 flex items-center gap-3">
            <p class="text-sm text-gray-600">Active filters:</p>
            <div class="flex gap-2">
              <span v-if="filterStatus !== 'all'" class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium">
                Status: {{ getStockStatusLabel(filterStatus) }}
                <button @click="filterStatus = 'all'" class="hover:bg-indigo-200 rounded p-0.5 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </span>
              <span v-if="search" class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium">
                Search: "{{ search }}"
                <button @click="search = ''" class="hover:bg-indigo-200 rounded p-0.5 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </span>
            </div>
          </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-indigo-500 to-purple-600">
                <tr>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                    Item
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                    Description
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                    Current Stock
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                    Minimum Stock
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <!-- Empty State -->
                <tr v-if="filteredItems.length === 0">
                  <td colspan="6" class="px-6 py-20 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-6">
                      <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                      </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Items Found</h3>
                    <p class="text-gray-600 mb-6">
                      {{ search ? 'Try adjusting your search terms or filters' : 'Start by adding your first item to the inventory' }}
                    </p>
                    <Link 
                      :href="route('items.create')" 
                      class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                      </svg>
                      Add Your First Item
                    </Link>
                  </td>
                </tr>

                <!-- Item Rows -->
                <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200 group">
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-center gap-4">
                      <div :class="['p-3 rounded-xl bg-gradient-to-br shadow-md group-hover:shadow-lg transition-all duration-200', getStockStatusGradient(item.stock_status)]">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-bold text-gray-900">{{ item.name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ item.unit_type }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-5">
                    <p class="text-sm text-gray-600 max-w-md line-clamp-2">
                      {{ item.description || '-' }}
                    </p>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-baseline gap-2">
                      <p class="text-2xl font-bold text-gray-900">{{ item.current_quantity }}</p>
                      <span class="text-sm font-medium text-gray-500">{{ item.unit_type }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-baseline gap-2">
                      <p class="text-2xl font-bold text-gray-900">{{ item.minimum_quantity || '-' }}</p>
                      <span v-if="item.minimum_quantity" class="text-sm font-medium text-gray-500">{{ item.unit_type }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <span 
                      :class="['inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border-2 shadow-sm', getStockStatusColor(item.stock_status)]"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStockStatusIcon(item.stock_status)"/>
                      </svg>
                      {{ getStockStatusLabel(item.stock_status) }}
                    </span>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-right">
                    <div class="flex justify-end gap-2">
                      <Link 
                        :href="route('items.history', item.id)"
                        class="p-3 text-purple-600 hover:text-white bg-purple-100 hover:bg-purple-600 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                        title="View History"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </Link>
                      <button
                        @click="deleteItem(item)"
                        class="p-3 text-rose-600 hover:text-white bg-rose-100 hover:bg-rose-600 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                        title="Delete Item"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="items.last_page > 1" class="flex justify-center">
          <div class="flex items-center gap-2 bg-white rounded-2xl shadow-lg p-2">
            <Link 
              v-for="page in items.last_page" 
              :key="page"
              :href="route('items.index', { page, search })"
              preserve-state
              :class="[
                'px-5 py-3 text-sm font-bold rounded-xl transition-all duration-200',
                page === items.current_page
                  ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg'
                  : 'text-gray-700 hover:bg-gray-100'
              ]"
            >
              {{ page }}
            </Link>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Smooth transitions */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
}
</style>