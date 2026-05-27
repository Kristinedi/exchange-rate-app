<script setup lang="ts">
import { computed, ref, watch, watchEffect } from 'vue';
import type { Currency, ExchangeRate } from '@/types';
import FromCurrencySelect from '@/components/ExchangeRates/LatestRates/FromCurrencySelect.vue';
import ToCurrencyDropdown from '@/components/ExchangeRates/LatestRates/ToCurrencyDropdown.vue';
import LatestRatesTable from '@/components/ExchangeRates/LatestRates/LatestRatesTable.vue';
import { DEFAULT_CURRENCY } from '@/constants/currencies';

const props = defineProps<{
    rates: ExchangeRate[];
    newName: string;
    existingNames: string[];
}>();

const emit = defineEmits<{
    'update:fromCurrency': [currency: Currency];
    'update:newName': [value: string];
    'delete-table': [];
    'rename-table': [newName: string];
}>();

const selectedFromCurrency = ref<Currency>(DEFAULT_CURRENCY);
const selectedToCurrencies = ref<Currency[]>([]);

watch(
    selectedFromCurrency,
    (currency) => emit('update:fromCurrency', currency),
    { immediate: true },
);

const baseFilteredRates = computed(() =>
    props.rates.filter(
        (r) => r.from_currency === selectedFromCurrency.value.code,
    ),
);

const toCurrencies = computed(() => [
    ...new Map(
        baseFilteredRates.value.map((r) => [
            r.to_currency,
            { code: r.to_currency, name: r.to_currency_name },
        ]),
    ).values(),
]);

const filteredRates = computed(() =>
    baseFilteredRates.value.filter((r) =>
        selectedToCurrencies.value.some((c) => c.code === r.to_currency),
    ),
);

const renameError = ref('');

const rename = () => {
    if (props.existingNames.includes(props.newName)) {
        renameError.value = 'A table with this name already exists.';
        return;
    }

    renameError.value = '';
    emit('rename-table', props.newName);
    emit('update:newName', '');
};

watch(() => props.newName, () => {
    renameError.value = '';
});

watchEffect(() => {
    selectedToCurrencies.value = [...toCurrencies.value];
});
</script>

<template>
    <div class="mb-4 min-h-full gap-16 rounded bg-amber-100 p-8 md:flex">
        <div>
            <FromCurrencySelect
                v-model="selectedFromCurrency"
                :rates="props.rates"
            />
            <ToCurrencyDropdown
                v-model="selectedToCurrencies"
                :to-currencies="toCurrencies"
            />
            <div>
                <input
                    :value="props.newName"
                    @input="
                        emit(
                            'update:newName',
                            ($event.target as HTMLInputElement).value,
                        )
                    "
                    type="text"
                    maxlength="30"
                    placeholder="Enter table name"
                    class="mt-7 w-full rounded border border-gray-400 bg-white px-2 py-1 text-sm"
                />
                <div class="text-right text-xs text-gray-400">
                    {{ 30 - props.newName.length }} / 30
                </div>
                <button
                    @click="rename"
                    class="mt-1 rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600"
                >
                    Rename
                </button>
                <div v-if="renameError" class="mt-1 text-red-500 text-sm">
                    {{ renameError }}
                </div>
            </div>
            <div>
                <button
                    @click="emit('delete-table')"
                    class="mt-7 rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                >
                    Delete table
                </button>
            </div>
        </div>
        <LatestRatesTable
            :from-currency="selectedFromCurrency"
            :filtered-rates="filteredRates"
        />
    </div>
</template>
