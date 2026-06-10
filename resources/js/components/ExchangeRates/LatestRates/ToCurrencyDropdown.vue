<script setup lang="ts">
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuCheckboxItem,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { ChevronDown } from 'lucide-vue-next';
import type { Currency } from '@/types';

const props = defineProps<{
    modelValue: Currency[];
    toCurrencies: Currency[];
}>();

const emit = defineEmits<{
    'update:modelValue': [value: Currency[]];
}>();

const areAllSelected = computed(
    () =>
        props.toCurrencies.length > 0 &&
        props.toCurrencies.every((c) =>
            props.modelValue.some((m) => m.id === c.id),
        ),
);

const toggleToCurrency = (currency: Currency): void => {
    emit(
        'update:modelValue',
        props.modelValue.some((c) => c.id === currency.id)
            ? props.modelValue.filter((c) => c.id !== currency.id)
            : [...props.modelValue, currency],
    );
};
</script>

<template>
    <div>Select target currencies</div>
    <div class="pb-1">({{ modelValue.length }} selected)</div>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="outline"
                class="w-64 justify-between !border-gray-400 font-normal"
            >
                Select Currencies
                <ChevronDown class="size-4 opacity-50" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-64">
            <DropdownMenuCheckboxItem
                :model-value="areAllSelected"
                @select.prevent="emit('update:modelValue', [...toCurrencies])"
            >
                Select All
            </DropdownMenuCheckboxItem>
            <DropdownMenuCheckboxItem
                :model-value="modelValue.length === 0"
                @select.prevent="emit('update:modelValue', [])"
            >
                Deselect All
            </DropdownMenuCheckboxItem>
            <DropdownMenuSeparator />
            <DropdownMenuCheckboxItem
                v-for="currency in toCurrencies"
                :key="currency.id"
                :model-value="modelValue.some((c) => c.id === currency.id)"
                @select.prevent="toggleToCurrency(currency)"
            >
                {{ currency.code }} ({{ currency.name }})
            </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
