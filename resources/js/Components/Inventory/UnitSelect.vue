<script setup lang="ts">
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Props {
  modelValue: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:modelValue': [value: string];
}>();

const units = [
  { value: 'kg', label: 'Kilograms (kg)' },
  { value: 'm', label: 'Meters (m)' },
  { value: 'cm', label: 'Centimeters (cm)' },
  { value: 'units', label: 'Units' },
];
</script>

<template>
  <Select :model-value="modelValue" @update:model-value="emit('update:modelValue', $event)">
    <SelectTrigger>
      <SelectValue placeholder="Select unit type" />
    </SelectTrigger>
    <SelectContent>
      <SelectItem v-for="unit in units" :key="unit.value" :value="unit.value">
        {{ unit.label }}
      </SelectItem>
    </SelectContent>
  </Select>
</template>