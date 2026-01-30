<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import type { ItemForm } from '@/types/inventory';

const form = useForm({
  items: [
    {
      name: '',
      description: '',
      unit_type: '',
      quantity: 0,
      minimum_quantity: null,
    }
  ] as ItemForm[]
});

const addRow = () => {
  form.items.push({
    name: '',
    description: '',
    unit_type: '',
    quantity: 0,
    minimum_quantity: null,
  });
};

const removeRow = (index: number) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
  }
};

const submit = () => {
  form.post(route('items.store'), {
    onSuccess: () => form.reset(),
  });
};
</script>

<template>
  <Head title="Add Items" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">
            Add New Items
          </h2>
          <p class="text-sm text-gray-600 mt-1">Create and configure inventory items with initial stock levels</p>
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        
        <!-- Back Navigation with modern styling -->
        <div class="mb-6">
          <Link 
            :href="route('items.index')"
            class="group inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors duration-200"
          >
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Items
          </Link>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          
          <!-- Header Card with gradient -->
          <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 rounded-2xl shadow-xl">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-40 h-40 bg-white opacity-5 rounded-full"></div>
            
            <div class="relative p-8">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                      </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Adding {{ form.items.length }} Item{{ form.items.length > 1 ? 's' : '' }}</h3>
                  </div>
                  <p class="text-white text-opacity-90 text-sm max-w-2xl">
                    Fill in the details below to add items to your inventory. You can add multiple items at once by clicking "Add Another Item".
                  </p>
                </div>
                
                <!-- Item counter badge -->
                <div class="flex-shrink-0 ml-6">
                  <div class="px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl border-2 border-white border-opacity-30">
                    <div class="text-center">
                      <p class="text-3xl font-bold text-white">{{ form.items.length }}</p>
                      <p class="text-xs text-white text-opacity-90 uppercase tracking-wider mt-1">Items</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Item Cards -->
          <div class="space-y-6">
            <div 
              v-for="(item, index) in form.items" 
              :key="index"
              class="group relative bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden"
            >
              <!-- Colored top border indicator -->
              <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
              
              <!-- Card Header -->
              <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-4">
                    <!-- Item number badge -->
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-md">
                      <span class="text-xl font-bold text-white">{{ index + 1 }}</span>
                    </div>
                    <div>
                      <h4 class="text-lg font-bold text-gray-900">Item {{ index + 1 }}</h4>
                      <p class="text-sm text-gray-600">Configure item details and stock levels</p>
                    </div>
                  </div>

                  <!-- Remove Button -->
                  <button
                    v-if="form.items.length > 1"
                    type="button"
                    @click="removeRow(index)"
                    class="group/btn flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 hover:text-white bg-red-50 hover:bg-red-600 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
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
                
                <!-- Row 1: Item Name & Unit Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Item Name -->
                  <div class="space-y-2">
                    <InputLabel :for="`name-${index}`" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                      <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                      </svg>
                      Item Name *
                    </InputLabel>
                    <TextInput
                      :id="`name-${index}`"
                      v-model="item.name"
                      type="text"
                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-200"
                      required
                      placeholder="e.g., Rice, Cement, Wire, Bolts"
                    />
                    <InputError :message="form.errors[`items.${index}.name`]" class="mt-2" />
                  </div>

                  <!-- Unit Type -->
                  <div class="space-y-2">
                    <InputLabel :for="`unit-${index}`" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                      <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                      </svg>
                      Unit Type *
                    </InputLabel>
                    <div class="relative">
                      <select
                        :id="`unit-${index}`"
                        v-model="item.unit_type"
                        required
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-xl shadow-sm appearance-none pr-10 transition-all duration-200"
                      >
                        <option value="" disabled>Select unit type</option>
                        <option value="kg">Kilograms (kg)</option>
                        <option value="m">Meters (m)</option>
                        <option value="cm">Centimeters (cm)</option>
                        <option value="units">Units</option>
                        <option value="liters">Liters (L)</option>
                        <option value="pieces">Pieces</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                      </div>
                    </div>
                    <InputError :message="form.errors[`items.${index}.unit_type`]" class="mt-2" />
                  </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                  <InputLabel :for="`description-${index}`" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    Description
                    <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                  </InputLabel>
                  <textarea
                    :id="`description-${index}`"
                    v-model="item.description"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-200"
                    placeholder="Add any relevant details about this item (specifications, supplier info, storage location, etc.)"
                  />
                  <InputError :message="form.errors[`items.${index}.description`]" class="mt-2" />
                </div>

                <!-- Row 2: Quantity & Minimum Stock -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Quantity -->
                  <div class="relative">
                    <div class="space-y-2">
                      <InputLabel :for="`quantity-${index}`" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Initial Quantity *
                      </InputLabel>
                      <div class="relative">
                        <TextInput
                          :id="`quantity-${index}`"
                          v-model.number="item.quantity"
                          type="number"
                          step="0.01"
                          min="0"
                          class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 rounded-xl shadow-sm pl-4 pr-16 transition-all duration-200"
                          required
                          placeholder="0.00"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                          <span class="text-sm font-semibold text-gray-500">{{ item.unit_type || 'units' }}</span>
                        </div>
                      </div>
                      <p class="text-xs text-gray-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Starting stock level for this item
                      </p>
                      <InputError :message="form.errors[`items.${index}.quantity`]" class="mt-2" />
                    </div>
                  </div>

                  <!-- Minimum Quantity -->
                  <div class="relative">
                    <div class="space-y-2">
                      <InputLabel :for="`minimum-${index}`" class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Minimum Stock Alert
                        <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                      </InputLabel>
                      <div class="relative">
                        <TextInput
                          :id="`minimum-${index}`"
                          v-model.number="item.minimum_quantity"
                          type="number"
                          step="0.01"
                          min="0"
                          class="mt-1 block w-full border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500 rounded-xl shadow-sm pl-4 pr-16 transition-all duration-200"
                          placeholder="0.00"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                          <span class="text-sm font-semibold text-gray-500">{{ item.unit_type || 'units' }}</span>
                        </div>
                      </div>
                      <p class="text-xs text-gray-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Get notified when stock falls below this level
                      </p>
                      <InputError :message="form.errors[`items.${index}.minimum_quantity`]" class="mt-2" />
                    </div>
                  </div>
                </div>

                <!-- Visual Stock Preview -->
                <div v-if="item.quantity && item.minimum_quantity" class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-indigo-100">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-700">Stock Level Preview</span>
                    <span class="text-xs text-gray-600">{{ item.quantity }} / {{ item.minimum_quantity }} {{ item.unit_type }}</span>
                  </div>
                  <div class="relative h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                      class="absolute inset-y-0 left-0 rounded-full transition-all duration-300"
                      :class="item.quantity > item.minimum_quantity ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' : 'bg-gradient-to-r from-orange-500 to-red-600'"
                      :style="{ width: `${Math.min((item.quantity / (item.minimum_quantity * 2)) * 100, 100)}%` }"
                    ></div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- Add Another Item Button -->
          <button
            type="button"
            @click="addRow"
            class="group w-full py-6 px-6 bg-white hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 border-3 border-dashed border-gray-300 hover:border-indigo-400 rounded-2xl transition-all duration-300 shadow-md hover:shadow-lg"
          >
            <div class="flex items-center justify-center gap-3">
              <div class="p-3 bg-gradient-to-br from-indigo-100 to-purple-100 group-hover:from-indigo-200 group-hover:to-purple-200 rounded-xl transition-all duration-300">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
              </div>
              <div class="text-left">
                <p class="text-base font-bold text-gray-700 group-hover:text-indigo-700 transition-colors">Add Another Item</p>
                <p class="text-sm text-gray-500 group-hover:text-indigo-600 transition-colors">Create multiple items in one go</p>
              </div>
            </div>
          </button>

          <!-- Action Footer -->
          <div class="sticky bottom-0 z-10 bg-white rounded-2xl shadow-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl">
                  <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900">Ready to add {{ form.items.length }} item{{ form.items.length > 1 ? 's' : '' }}</p>
                  <p class="text-xs text-gray-600">Review your entries and submit when ready</p>
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
                  class="group relative overflow-hidden px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105"
                >
                  <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                  <span class="relative flex items-center gap-2">
                    <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Adding Items...' : `Add ${form.items.length} Item${form.items.length > 1 ? 's' : ''}` }}
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