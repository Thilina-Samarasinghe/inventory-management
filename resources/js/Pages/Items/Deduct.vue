<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import type { Item, DeductionForm } from '@/types/inventory';

interface Props {
  items: Item[];
}

const props = defineProps<Props>();

const form = useForm({
  deductions: [] as DeductionForm[]
});

const selectedItems = ref<number[]>([]);
const selectedItemId = ref<string>('');

const addDeduction = () => {
  const itemId = parseInt(selectedItemId.value);
  if (itemId && !selectedItems.value.includes(itemId)) {
    selectedItems.value.push(itemId);
    form.deductions.push({
      id: itemId,
      quantity: 0,
      notes: '',
    });
    selectedItemId.value = '';
  }
};

const removeDeduction = (index: number) => {
  const itemId = form.deductions[index].id;
  selectedItems.value = selectedItems.value.filter(id => id !== itemId);
  form.deductions.splice(index, 1);
};

const getItem = (itemId: number) => {
  return props.items.find(item => item.id === itemId);
};

const submit = () => {
  form.post(route('items.deduct'), {
    onSuccess: () => {
      form.reset();
      selectedItems.value = [];
    },
  });
};

const availableItems = computed(() => {
  return props.items.filter(item => !selectedItems.value.includes(item.id));
});

// Calculate new quantity after deduction
const getNewQuantity = (itemId: number, deductionQty: number) => {
  const item = getItem(itemId);
  if (!item) return 0;
  return Math.max(0, item.current_quantity - deductionQty);
};

// Check if deduction will cause low stock
const willBeLowStock = (itemId: number, deductionQty: number) => {
  const item = getItem(itemId);
  if (!item || !item.minimum_quantity) return false;
  const newQty = getNewQuantity(itemId, deductionQty);
  return newQty <= item.minimum_quantity && newQty > 0;
};

// Check if deduction will cause out of stock
const willBeOutOfStock = (itemId: number, deductionQty: number) => {
  const newQty = getNewQuantity(itemId, deductionQty);
  return newQty === 0;
};
</script>

<template>
  <Head title="Deduct Items" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            Deduct Inventory
          </h2>
          <p class="text-sm text-gray-600 mt-1">Remove items from stock with transaction tracking</p>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        <!-- Back Navigation -->
        <div class="mb-6">
          <Link 
            :href="route('items.index')"
            class="group inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-rose-600 transition-colors duration-200"
          >
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Items
          </Link>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          
          <!-- Header Card with Rose/Red Gradient Theme -->
          <div class="relative overflow-hidden bg-gradient-to-br from-rose-500 via-red-500 to-red-600 rounded-2xl shadow-xl">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-40 h-40 bg-white opacity-5 rounded-full"></div>
            
            <div class="relative p-8">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                      </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">
                      {{ form.deductions.length > 0 ? `Deducting ${form.deductions.length} Item${form.deductions.length > 1 ? 's' : ''}` : 'Deduct Items' }}
                    </h3>
                  </div>
                  <p class="text-white text-opacity-90 text-sm max-w-2xl">
                    Select items from your inventory and specify quantities to deduct. All transactions are logged with timestamps and notes.
                  </p>
                </div>
                
                <!-- Deduction counter badge -->
                <div class="flex-shrink-0 ml-6">
                  <div class="px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl border-2 border-white border-opacity-30">
                    <div class="text-center">
                      <p class="text-3xl font-bold text-white">{{ form.deductions.length }}</p>
                      <p class="text-xs text-white text-opacity-90 uppercase tracking-wider mt-1">Items</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Item Selector Card -->
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-rose-500 to-red-600 px-8 py-6">
              <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Select Items to Deduct
              </h3>
              <p class="text-white text-opacity-90 text-sm mt-1">Choose items from your inventory</p>
            </div>
            
            <div class="p-6">
              <div class="space-y-2">
                <InputLabel for="item-select" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                  <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  Available Items
                </InputLabel>
                <div class="flex gap-3">
                  <div class="relative flex-1">
                    <select
                      id="item-select"
                      v-model="selectedItemId"
                      class="block w-full border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500 rounded-xl shadow-sm appearance-none pr-10 transition-all duration-200"
                    >
                      <option value="" disabled>Choose an item to deduct...</option>
                      <option 
                        v-for="item in availableItems" 
                        :key="item.id" 
                        :value="item.id"
                      >
                        {{ item.name }} • Available: {{ item.current_quantity }} {{ item.unit_type }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                      </svg>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addDeduction"
                    :disabled="!selectedItemId"
                    class="group relative overflow-hidden flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 disabled:transform-none"
                  >
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                    <svg class="w-5 h-5 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="relative">Add Item</span>
                  </button>
                </div>
                <p class="text-xs text-gray-600 flex items-center gap-1 mt-2">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ availableItems.length }} item{{ availableItems.length !== 1 ? 's' : '' }} available for deduction
                </p>
              </div>
            </div>
          </div>

          <!-- Selected Items for Deduction -->
          <div v-if="form.deductions.length > 0" class="space-y-6">
            <div class="flex items-center justify-between">
              <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <div class="p-2 bg-rose-100 rounded-lg">
                  <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                </div>
                Items to Deduct ({{ form.deductions.length }})
              </h4>
            </div>
            
            <div 
              v-for="(deduction, index) in form.deductions" 
              :key="index"
              class="group relative bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden"
            >
              <!-- Colored top border indicator - Red theme -->
              <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-red-500"></div>
              
              <!-- Card Header -->
              <div class="px-8 py-6 bg-gradient-to-r from-rose-50 to-red-50 border-b border-rose-200">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-4">
                    <!-- Item number badge with minus icon -->
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl shadow-md">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                      </svg>
                    </div>
                    <div>
                      <h5 class="text-lg font-bold text-gray-900">{{ getItem(deduction.id)?.name }}</h5>
                      <div class="flex items-center gap-4 mt-1">
                        <span class="text-sm text-gray-600 flex items-center gap-1">
                          <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                          </svg>
                          Available: <strong class="text-gray-900">{{ getItem(deduction.id)?.current_quantity }} {{ getItem(deduction.id)?.unit_type }}</strong>
                        </span>
                        <span v-if="getItem(deduction.id)?.minimum_quantity" class="text-sm text-gray-500 flex items-center gap-1">
                          <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                          </svg>
                          Min: {{ getItem(deduction.id)?.minimum_quantity }} {{ getItem(deduction.id)?.unit_type }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Remove Button -->
                  <button
                    type="button"
                    @click="removeDeduction(index)"
                    class="group/btn flex items-center gap-2 px-4 py-2 text-sm font-semibold text-rose-600 hover:text-white bg-white hover:bg-rose-600 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md border-2 border-rose-200 hover:border-rose-600"
                    title="Remove item"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span class="hidden group-hover/btn:inline">Remove</span>
                  </button>
                </div>
              </div>

              <!-- Card Body -->
              <div class="p-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Quantity to Deduct -->
                  <div class="space-y-2">
                    <InputLabel :for="`quantity-${index}`" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                      <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                      </svg>
                      Quantity to Deduct *
                    </InputLabel>
                    <div class="relative">
                      <TextInput
                        :id="`quantity-${index}`"
                        v-model.number="deduction.quantity"
                        type="number"
                        step="0.01"
                        min="0.01"
                        :max="getItem(deduction.id)?.current_quantity"
                        class="mt-1 block w-full border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500 rounded-xl shadow-sm pl-4 pr-16 transition-all duration-200"
                        required
                        placeholder="0.00"
                      />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <span class="text-sm font-semibold text-gray-500">{{ getItem(deduction.id)?.unit_type }}</span>
                      </div>
                    </div>
                    <p class="text-xs text-gray-600 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Maximum: {{ getItem(deduction.id)?.current_quantity }} {{ getItem(deduction.id)?.unit_type }}
                    </p>
                    <InputError :message="form.errors[`deductions.${index}.quantity`]" class="mt-2" />
                  </div>

                  <!-- Notes / Reason -->
                  <div class="space-y-2">
                    <InputLabel :for="`notes-${index}`" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                      <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Notes / Reason
                      <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                    </InputLabel>
                    <textarea
                      :id="`notes-${index}`"
                      v-model="deduction.notes"
                      rows="3"
                      class="mt-1 block w-full border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500 rounded-xl shadow-sm transition-all duration-200"
                      placeholder="e.g., Sold to customer, Used in production, Damaged goods, etc."
                    />
                    <p class="text-xs text-gray-600 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Document the reason for this deduction
                    </p>
                    <InputError :message="form.errors[`deductions.${index}.notes`]" class="mt-2" />
                  </div>
                </div>

                <!-- Stock Level Preview After Deduction -->
                <div v-if="deduction.quantity > 0" class="mt-6 p-5 rounded-xl border-2 transition-all duration-300"
                     :class="[
                       willBeOutOfStock(deduction.id, deduction.quantity) 
                         ? 'bg-gradient-to-r from-red-50 to-rose-50 border-red-300' 
                         : willBeLowStock(deduction.id, deduction.quantity)
                         ? 'bg-gradient-to-r from-orange-50 to-amber-50 border-orange-300'
                         : 'bg-gradient-to-r from-emerald-50 to-green-50 border-emerald-300'
                     ]"
                >
                  <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2">
                      <svg 
                        class="w-5 h-5" 
                        :class="[
                          willBeOutOfStock(deduction.id, deduction.quantity) 
                            ? 'text-red-600' 
                            : willBeLowStock(deduction.id, deduction.quantity)
                            ? 'text-orange-600'
                            : 'text-emerald-600'
                        ]"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span class="text-sm font-bold" 
                            :class="[
                              willBeOutOfStock(deduction.id, deduction.quantity) 
                                ? 'text-red-700' 
                                : willBeLowStock(deduction.id, deduction.quantity)
                                ? 'text-orange-700'
                                : 'text-emerald-700'
                            ]"
                      >
                        {{ willBeOutOfStock(deduction.id, deduction.quantity) 
                           ? 'Warning: Will be Out of Stock' 
                           : willBeLowStock(deduction.id, deduction.quantity)
                           ? 'Warning: Will be Low Stock'
                           : 'Stock Level After Deduction' 
                        }}
                      </span>
                    </div>
                    <div class="text-right">
                      <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-600">Current:</span>
                        <span class="text-sm font-bold text-gray-900">{{ getItem(deduction.id)?.current_quantity }}</span>
                        <span class="text-xs text-gray-600">→</span>
                        <span class="text-sm font-bold"
                              :class="[
                                willBeOutOfStock(deduction.id, deduction.quantity) 
                                  ? 'text-red-700' 
                                  : willBeLowStock(deduction.id, deduction.quantity)
                                  ? 'text-orange-700'
                                  : 'text-emerald-700'
                              ]"
                        >
                          {{ getNewQuantity(deduction.id, deduction.quantity) }}
                        </span>
                        <span class="text-xs text-gray-600">{{ getItem(deduction.id)?.unit_type }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="relative h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                      class="absolute inset-y-0 left-0 rounded-full transition-all duration-300"
                      :class="[
                        willBeOutOfStock(deduction.id, deduction.quantity) 
                          ? 'bg-gradient-to-r from-red-600 to-red-700' 
                          : willBeLowStock(deduction.id, deduction.quantity)
                          ? 'bg-gradient-to-r from-orange-500 to-orange-600'
                          : 'bg-gradient-to-r from-emerald-500 to-emerald-600'
                      ]"
                      :style="{ 
                        width: `${Math.max(5, Math.min((getNewQuantity(deduction.id, deduction.quantity) / getItem(deduction.id)?.current_quantity) * 100, 100))}%` 
                      }"
                    ></div>
                  </div>
                  <p v-if="willBeLowStock(deduction.id, deduction.quantity) || willBeOutOfStock(deduction.id, deduction.quantity)" 
                     class="text-xs mt-2"
                     :class="[
                       willBeOutOfStock(deduction.id, deduction.quantity) 
                         ? 'text-red-700' 
                         : 'text-orange-700'
                     ]"
                  >
                    {{ willBeOutOfStock(deduction.id, deduction.quantity) 
                       ? '⚠️ This item will be completely out of stock after deduction' 
                       : '⚠️ This item will fall below minimum stock level' 
                    }}
                  </p>
                </div>

              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="text-center py-16 px-6">
              <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-rose-100 to-red-100 rounded-full mb-6">
                <svg class="w-12 h-12 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
              </div>
              <h4 class="text-xl font-bold text-gray-900 mb-2">No Items Selected</h4>
              <p class="text-gray-600 mb-1">Choose items from the dropdown above to deduct from inventory</p>
              <p class="text-sm text-gray-500">All deductions will be tracked with timestamps and notes</p>
            </div>
          </div>

          <!-- Action Footer -->
          <div v-if="form.deductions.length > 0" class="sticky bottom-0 z-10 bg-white rounded-2xl shadow-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-rose-100 to-red-100 rounded-xl">
                  <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900">Ready to deduct {{ form.deductions.length }} item{{ form.deductions.length > 1 ? 's' : '' }}</p>
                  <p class="text-xs text-gray-600">This action will update inventory levels and create transaction records</p>
                </div>
              </div>

              <div class="flex items-center gap-3">
                <Link 
                  :href="route('items.index')"
                  class="px-6 py-3 bg-white hover:bg-gray-50 border-2 border-gray-300 hover:border-gray-400 rounded-xl font-semibold text-sm text-gray-700 uppercase tracking-wider shadow-sm hover:shadow-md transition-all duration-200"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="group relative overflow-hidden px-8 py-3 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105"
                >
                  <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                  <span class="relative flex items-center gap-2">
                    <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Processing...' : `Deduct ${form.deductions.length} Item${form.deductions.length > 1 ? 's' : ''}` }}
                  </span>
                </button>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Custom animations for smooth interactions */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group:hover .group-hover\:inline {
  display: inline;
}

/* Smooth focus states */
input:focus,
select:focus,
textarea:focus {
  transform: translateY(-1px);
}

/* Enhanced shadow on hover */
.hover\:shadow-xl {
  transition: box-shadow 0.3s ease;
}
</style>