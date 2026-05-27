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
import type { Currency } from '@/types';
import { ChevronDown } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: Currency[];
    toCurrencies: Currency[];
}>();

const emit = defineEmits<{
    'update:modelValue': [value: Currency[]];
}>();

const toggleToCurrency = (currency: Currency): void => {
    emit('update:modelValue',
        props.modelValue.some(c => c.code === currency.code)
            ? props.modelValue.filter(c => c.code !== currency.code)
            : [...props.modelValue, currency]
    );
};

const toggleAll = (): void => {
    emit('update:modelValue', 
        props.modelValue.length === props.toCurrencies.length ? [] : [...props.toCurrencies]
    );
};

const areAllSelected = computed(() =>
    props.toCurrencies.length > 0 && 
    props.toCurrencies.every(c => props.modelValue.some(m => m.code === c.code))
);

</script>

<template>
    <div>
        Select target currencies
    </div>
    <div class="pb-1">
        ({{props.modelValue.length }} selected)
    </div>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" class="w-64 justify-between font-normal !border-gray-400">
                Select Currencies
                <ChevronDown class="size-4 opacity-50" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-64">
            <DropdownMenuCheckboxItem
                :model-value="areAllSelected"
                @select.prevent="toggleAll"
            >
                Select All
            </DropdownMenuCheckboxItem>
            <DropdownMenuCheckboxItem
                :model-value="props.modelValue.length === 0"
                @select.prevent="emit('update:modelValue', [])"
            >
                Deselect All
            </DropdownMenuCheckboxItem>
            <DropdownMenuSeparator />
            <DropdownMenuCheckboxItem
                v-for="currency in props.toCurrencies"
                :key="currency.code"
                :model-value="props.modelValue.some(c => c.code === currency.code)"
                @select.prevent="toggleToCurrency(currency)"
            >
                 {{ currency.code }} ({{ currency.name }})
            </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>