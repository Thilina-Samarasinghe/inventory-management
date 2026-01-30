<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Item, Transaction } from '@/types/inventory';

// Props from DashboardController with REAL DATABASE DATA
interface Props {
  stats: {
    total_items: number;
    low_stock_items: number;
    out_of_stock_items: number;
    total_transactions: number;
  };
  recentTransactions: Transaction[];
  lowStockItems: Item[];
}

const props = defineProps<Props>();

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
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
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            Dashboard
          </h2>
          <p class="text-sm text-gray-600 mt-1">Welcome back! Here's your inventory overview</p>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
        
        <!-- Statistics Cards with Modern Design -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          
          <!-- Total Items - Blue Gradient -->
          <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                  <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">Total Items</h3>
              <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-white">{{ stats.total_items }}</p>
                <span class="text-white text-opacity-75 text-sm">items</span>
              </div>
            </div>
          </div>

          <!-- Low Stock - Orange Gradient -->
          <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                  <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">Low Stock</h3>
              <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-white">{{ stats.low_stock_items }}</p>
                <span class="text-white text-opacity-75 text-sm">alerts</span>
              </div>
            </div>
          </div>

          <!-- Out of Stock - Red Gradient -->
          <div class="relative overflow-hidden bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                  <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">Out of Stock</h3>
              <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-white">{{ stats.out_of_stock_items }}</p>
                <span class="text-white text-opacity-75 text-sm">urgent</span>
              </div>
            </div>
          </div>

          <!-- Total Transactions - Purple Gradient -->
          <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                  <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                </div>
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-2">Transactions</h3>
              <div class="flex items-end justify-between">
                <p class="text-4xl font-bold text-white">{{ stats.total_transactions }}</p>
                <span class="text-white text-opacity-75 text-sm">total</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions - Modern Card Design -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="p-8">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
                <p class="text-sm text-gray-600 mt-1">Manage your inventory efficiently</p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <!-- Add Items -->
              <Link 
                :href="route('items.create')"
                class="group relative overflow-hidden flex items-center justify-center px-8 py-6 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
              >
                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-lg">Add Items</span>
              </Link>

              <!-- Deduct Items -->
              <Link 
                :href="route('items.deduct.create')"
                class="group relative overflow-hidden flex items-center justify-center px-8 py-6 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-indigo-500 transition-all duration-300 transform hover:scale-105"
              >
                <svg class="w-6 h-6 mr-3 text-gray-600 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                <span class="text-lg">Deduct Items</span>
              </Link>

              <!-- Search Items -->
              <Link 
                :href="route('items.index')"
                class="group relative overflow-hidden flex items-center justify-center px-8 py-6 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-indigo-500 transition-all duration-300 transform hover:scale-105"
              >
                <svg class="w-6 h-6 mr-3 text-gray-600 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="text-lg">Search Items</span>
              </Link>
            </div>
          </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Low Stock Alerts -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-8 py-6">
              <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Low Stock Alerts
              </h3>
              <p class="text-white text-opacity-90 text-sm mt-1">Items requiring immediate attention</p>
            </div>
            
            <div class="p-6">
              <div v-if="lowStockItems.length > 0" class="space-y-4">
                <div 
                  v-for="item in lowStockItems" 
                  :key="item.id"
                  class="group relative overflow-hidden p-5 bg-gradient-to-r from-orange-50 to-orange-100 border-l-4 border-orange-500 rounded-xl hover:shadow-md transition-all duration-300"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <p class="font-bold text-gray-900 text-lg mb-2">{{ item.name }}</p>
                      <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                          <span class="text-sm text-gray-600">Current:</span>
                          <span class="font-bold text-orange-700">{{ item.current_quantity }} {{ item.unit_type }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="text-sm text-gray-500">Min:</span>
                          <span class="font-semibold text-gray-700">{{ item.minimum_quantity }} {{ item.unit_type }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center gap-3">
                      <span 
                        :class="['px-4 py-2 text-xs font-bold rounded-full border-2', getStockStatusColor(item.stock_status)]"
                      >
                        {{ getStockStatusLabel(item.stock_status) }}
                      </span>
                      <Link 
                        :href="route('items.history', item.id)"
                        class="p-3 text-gray-600 hover:text-orange-600 hover:bg-orange-100 rounded-xl transition-all duration-200"
                        title="View History"
                      >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                      </Link>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty State -->
              <div v-else class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 rounded-full mb-4">
                  <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-2">All items well stocked!</p>
                <p class="text-sm text-gray-600">No items below minimum quantity</p>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
              <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Recent Activity
              </h3>
              <p class="text-white text-opacity-90 text-sm mt-1">Latest 10 transactions</p>
            </div>
            
            <div class="p-6">
              <div v-if="recentTransactions.length > 0" class="space-y-3">
                <div 
                  v-for="transaction in recentTransactions" 
                  :key="transaction.id"
                  class="group relative overflow-hidden p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-all duration-200"
                >
                  <div class="flex items-start gap-4">
                    <!-- Transaction Icon -->
                    <div 
                      :class="[
                        'flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center',
                        transaction.transaction_type === 'addition' 
                          ? 'bg-emerald-100' 
                          : 'bg-rose-100'
                      ]"
                    >
                      <svg 
                        v-if="transaction.transaction_type === 'addition'"
                        class="w-6 h-6 text-emerald-600" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                      </svg>
                      <svg 
                        v-else
                        class="w-6 h-6 text-rose-600" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                      </svg>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                      <p class="font-bold text-gray-900 mb-1">
                        {{ transaction.item?.name }}
                      </p>
                      <div class="flex items-center gap-2 mb-2">
                        <span 
                          :class="[
                            'px-3 py-1 text-xs font-bold rounded-lg',
                            transaction.transaction_type === 'addition' 
                              ? 'bg-emerald-100 text-emerald-700' 
                              : 'bg-rose-100 text-rose-700'
                          ]"
                        >
                          {{ transaction.transaction_type === 'addition' ? '+' : '-' }}{{ transaction.quantity }} {{ transaction.item?.unit_type }}
                        </span>
                      </div>
                      <p class="text-xs text-gray-500">
                        {{ formatDate(transaction.created_at) }}
                      </p>
                      <p v-if="transaction.notes" class="text-xs text-gray-600 mt-2 italic">
                        "{{ transaction.notes }}"
                      </p>
                    </div>
                    
                    <div class="text-right flex-shrink-0">
                      <p class="text-lg font-bold text-gray-900">
                        {{ transaction.new_quantity }}
                      </p>
                      <p class="text-xs text-gray-500">{{ transaction.item?.unit_type }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty State -->
              <div v-else class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                  <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-2">No transactions yet</p>
                <p class="text-sm text-gray-600">Start by adding items to your inventory</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>