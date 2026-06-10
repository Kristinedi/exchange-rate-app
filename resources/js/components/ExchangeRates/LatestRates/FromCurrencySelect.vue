<script setup lang="ts">
import { computed } from 'vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Currency, ExchangeRate } from '@/types';

const props = defineProps<{
    modelValue: Currency;
    rates: ExchangeRate[];
}>();

const emit = defineEmits<{
    'update:modelValue': [value: Currency];
}>();
const fromCurrencies = computed(() => [
    ...new Map(
        props.rates.map((r) => [
            r.from_currency,
            {
                id: r.from_currency_id,
                code: r.from_currency,
                name: r.from_currency_name,
            },
        ]),
    ).values(),
]);
</script>

<template>
    <div class="md:pb-6">
        <div class="pb-1">Select currency</div>
        <Select
            :model-value="modelValue"
            @update:model-value="emit('update:modelValue', $event as Currency)"
        >
            <SelectTrigger class="w-64 border-gray-400">
                <SelectValue placeholder="Select currency" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="currency in fromCurrencies"
                    :key="currency.id"
                    :value="currency"
                >
                    {{ currency.code }} ({{ currency.name }})
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
