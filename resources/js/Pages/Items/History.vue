<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Item, Transaction } from '@/types/inventory';

interface Props {
  item: Item;
  transactions: Transaction[];
}

const props = defineProps<Props>();

const formatDate = (date: string) => {
  try {
    return new Date(date).toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  } catch (error) {
    return date;
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

// Calculate statistics with safety checks
const totalAdditions = props.transactions?.filter(t => t.transaction_type === 'addition').length || 0;
const totalDeductions = props.transactions?.filter(t => t.transaction_type === 'deduction').length || 0;
const totalAdded = props.transactions
  ?.filter(t => t.transaction_type === 'addition')
  .reduce((sum, t) => sum + (Number(t.quantity) || 0), 0) || 0;
const totalDeducted = props.transactions
  ?.filter(t => t.transaction_type === 'deduction')
  .reduce((sum, t) => sum + (Number(t.quantity) || 0), 0) || 0;
</script>

<template>
  <Head :title="`History - ${item.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            Transaction History
          </h2>
          <p class="text-sm text-gray-600 mt-1">Complete audit trail for {{ item.name }}</p>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Back Navigation -->
        <div>
          <Link 
            :href="route('items.index')"
            class="group inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-purple-600 transition-colors duration-200"
          >
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Items
          </Link>
        </div>

        <!-- Item Header Card with Gradient -->
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 rounded-2xl shadow-xl">
          <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full"></div>
          <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-40 h-40 bg-white opacity-5 rounded-full"></div>
          
          <div class="relative p-8">
            <div class="flex items-start justify-between mb-6">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                  <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-3xl font-bold text-white">{{ item.name }}</h3>
                    <p v-if="item.description" class="text-white text-opacity-90 text-sm mt-1">{{ item.description }}</p>
                  </div>
                </div>
              </div>
              
              <!-- Status Badge -->
              <span 
                :class="['inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold border-2 backdrop-blur-sm bg-white bg-opacity-90 shadow-lg', getStockStatusColor(item.stock_status)]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStockStatusIcon(item.stock_status)"/>
                </svg>
                {{ getStockStatusLabel(item.stock_status) }}
              </span>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- Current Stock -->
              <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3 mb-2">
                  <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  <p class="text-sm text-white text-opacity-90 font-medium">Current Stock</p>
                </div>
                <p class="text-3xl font-bold text-white">{{ item.current_quantity }}</p>
                <p class="text-sm text-white text-opacity-75 mt-1">{{ item.unit_type }}</p>
              </div>

              <!-- Minimum Stock -->
              <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3 mb-2">
                  <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                  </div>
                  <p class="text-sm text-white text-opacity-90 font-medium">Minimum Stock</p>
                </div>
                <p class="text-3xl font-bold text-white">{{ item.minimum_quantity || '-' }}</p>
                <p class="text-sm text-white text-opacity-75 mt-1">{{ item.minimum_quantity ? item.unit_type : 'Not set' }}</p>
              </div>

              <!-- Total Additions -->
              <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3 mb-2">
                  <div class="p-2 bg-emerald-500 bg-opacity-80 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                  </div>
                  <p class="text-sm text-white text-opacity-90 font-medium">Additions</p>
                </div>
                <p class="text-3xl font-bold text-white">{{ totalAdditions }}</p>
                <p class="text-sm text-white text-opacity-75 mt-1">+{{ totalAdded.toFixed(2) }} {{ item.unit_type }}</p>
              </div>

              <!-- Total Deductions -->
              <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-5 border border-white border-opacity-20">
                <div class="flex items-center gap-3 mb-2">
                  <div class="p-2 bg-rose-500 bg-opacity-80 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                  </div>
                  <p class="text-sm text-white text-opacity-90 font-medium">Deductions</p>
                </div>
                <p class="text-3xl font-bold text-white">{{ totalDeductions }}</p>
                <p class="text-sm text-white text-opacity-75 mt-1">-{{ totalDeducted.toFixed(2) }} {{ item.unit_type }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Transaction Timeline -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-8 py-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Transaction Timeline
                </h3>
                <p class="text-white text-opacity-90 text-sm mt-1">Complete history of all changes</p>
              </div>
              <div class="px-5 py-2 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl border border-white border-opacity-30">
                <p class="text-2xl font-bold text-white">{{ transactions.length }}</p>
                <p class="text-xs text-white text-opacity-90 uppercase tracking-wider">Total</p>
              </div>
            </div>
          </div>
          
          <div class="p-8">
            <!-- Transactions List with Enhanced Timeline -->
            <div v-if="transactions && transactions.length > 0" class="relative">
              <!-- Timeline Line -->
              <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-purple-200 via-purple-300 to-purple-200"></div>

              <div class="space-y-6">
                <div 
                  v-for="(transaction, index) in transactions" 
                  :key="transaction.id" 
                  class="relative pl-16 group"
                >
                  <!-- Timeline Dot with Icon -->
                  <div class="absolute left-0 top-0">
                    <div 
                      :class="[
                        'w-12 h-12 rounded-xl shadow-lg flex items-center justify-center border-4 border-white transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl',
                        transaction.transaction_type === 'addition' 
                          ? 'bg-gradient-to-br from-emerald-500 to-emerald-600' 
                          : 'bg-gradient-to-br from-rose-500 to-red-600'
                      ]"
                    >
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path 
                          stroke-linecap="round" 
                          stroke-linejoin="round" 
                          stroke-width="2.5" 
                          :d="transaction.transaction_type === 'addition' ? 'M12 4v16m8-8H4' : 'M20 12H4'"
                        />
                      </svg>
                    </div>
                  </div>

                  <!-- Transaction Card -->
                  <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 group-hover:border-purple-300">
                    <!-- Card Header -->
                    <div class="px-6 py-4 bg-white border-b border-gray-200 flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <span 
                          :class="[
                            'inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold',
                            transaction.transaction_type === 'addition' 
                              ? 'bg-emerald-100 text-emerald-700 border-2 border-emerald-200' 
                              : 'bg-rose-100 text-rose-700 border-2 border-rose-200'
                          ]"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path 
                              stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2" 
                              :d="transaction.transaction_type === 'addition' ? 'M12 4v16m8-8H4' : 'M20 12H4'"
                            />
                          </svg>
                          {{ transaction.transaction_type === 'addition' ? 'Addition' : 'Deduction' }}
                        </span>
                        
                        <div v-if="transaction.user" class="flex items-center gap-2 text-sm text-gray-600">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                          </svg>
                          <span class="font-medium">{{ transaction.user.name }}</span>
                        </div>
                      </div>

                      <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ formatDate(transaction.created_at) }}
                      </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Quantity Changed -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border-2"
                             :class="transaction.transaction_type === 'addition' ? 'border-emerald-200' : 'border-rose-200'"
                        >
                          <div class="flex items-center gap-2 mb-2">
                            <div 
                              :class="[
                                'p-2 rounded-lg',
                                transaction.transaction_type === 'addition' ? 'bg-emerald-100' : 'bg-rose-100'
                              ]"
                            >
                              <svg 
                                :class="[
                                  'w-4 h-4',
                                  transaction.transaction_type === 'addition' ? 'text-emerald-600' : 'text-rose-600'
                                ]"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                              >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                              </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity Changed</p>
                          </div>
                          <div class="flex items-baseline gap-2">
                            <p 
                              :class="[
                                'text-3xl font-bold',
                                transaction.transaction_type === 'addition' ? 'text-emerald-600' : 'text-rose-600'
                              ]"
                            >
                              {{ transaction.transaction_type === 'addition' ? '+' : '-' }}{{ transaction.quantity }}
                            </p>
                            <span class="text-sm font-medium text-gray-600">{{ item.unit_type }}</span>
                          </div>
                        </div>

                        <!-- Previous Stock -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border-2 border-gray-200">
                          <div class="flex items-center gap-2 mb-2">
                            <div class="p-2 bg-gray-100 rounded-lg">
                              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                              </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Previous</p>
                          </div>
                          <div class="flex items-baseline gap-2">
                            <p class="text-3xl font-bold text-gray-900">{{ transaction.previous_quantity }}</p>
                            <span class="text-sm font-medium text-gray-600">{{ item.unit_type }}</span>
                          </div>
                        </div>

                        <!-- New Stock -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border-2 border-purple-200">
                          <div class="flex items-center gap-2 mb-2">
                            <div class="p-2 bg-purple-100 rounded-lg">
                              <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                              </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">New Stock</p>
                          </div>
                          <div class="flex items-baseline gap-2">
                            <p class="text-3xl font-bold text-purple-600">{{ transaction.new_quantity }}</p>
                            <span class="text-sm font-medium text-gray-600">{{ item.unit_type }}</span>
                          </div>
                        </div>
                      </div>

                      <!-- Visual Change Indicator -->
                      <div class="mt-6 flex items-center gap-3">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                          <div 
                            class="h-full transition-all duration-500 rounded-full"
                            :class="transaction.transaction_type === 'addition' ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' : 'bg-gradient-to-r from-rose-500 to-red-600'"
                            :style="{ width: `${Math.min((transaction.new_quantity / Math.max(transaction.previous_quantity, transaction.new_quantity)) * 100, 100)}%` }"
                          ></div>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 tabular-nums">
                          {{ ((transaction.new_quantity / Math.max(transaction.previous_quantity, transaction.new_quantity, 1)) * 100).toFixed(0) }}%
                        </span>
                      </div>

                      <!-- Notes -->
                      <div v-if="transaction.notes" class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                          <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                          </svg>
                          <div class="flex-1">
                            <p class="text-xs font-semibold text-blue-900 uppercase tracking-wider mb-1">Notes</p>
                            <p class="text-sm text-blue-800 leading-relaxed">{{ transaction.notes }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20">
              <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full mb-6">
                <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <h4 class="text-xl font-bold text-gray-900 mb-2">No Transactions Yet</h4>
              <p class="text-gray-600 mb-1">This item has no transaction history</p>
              <p class="text-sm text-gray-500">Add or deduct inventory to see transactions here</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Smooth animations for timeline items */
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.group {
  animation: slideInLeft 0.5s ease-out;
}

/* Stagger animation for multiple items */
.group:nth-child(1) { animation-delay: 0s; }
.group:nth-child(2) { animation-delay: 0.1s; }
.group:nth-child(3) { animation-delay: 0.2s; }
.group:nth-child(4) { animation-delay: 0.3s; }
.group:nth-child(5) { animation-delay: 0.4s; }

/* Hover effect for timeline dots */
.group:hover .absolute div {
  transform: scale(1.1);
}

/* Smooth transitions */
* {
  transition-property: transform, box-shadow, border-color;
  transition-duration: 200ms;
  transition-timing-function: ease-in-out;
}
</style>