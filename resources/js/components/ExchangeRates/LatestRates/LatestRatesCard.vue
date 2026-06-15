<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import FromCurrencySelect from '@/components/ExchangeRates/LatestRates/FromCurrencySelect.vue';
import LatestRatesTable from '@/components/ExchangeRates/LatestRates/LatestRatesTable.vue';
import ToCurrencyDropdown from '@/components/ExchangeRates/LatestRates/ToCurrencyDropdown.vue';
import type { Currency, ExchangeRate, Table } from '@/types';

const props = defineProps<{
    table: Table;
    rates: ExchangeRate[];
    newName: string;
    existingNames: string[];
}>();

const emit = defineEmits<{
    'update:fromCurrency': [currency: Currency];
    'update:toCurrencies': [currencies: Currency[]];
    'update:newName': [value: string];
    'delete-table': [];
    'rename-table': [newName: string];
}>();

const selectedFromCurrency = ref<Currency>(props.table.fromCurrency);
const selectedToCurrencies = ref<Currency[]>(props.table.toCurrencies);
const renameError = ref('');
const showControls = ref(false);

const baseFilteredRates = computed(() =>
    props.rates.filter(
        (r) => r.from_currency_id === selectedFromCurrency.value.id,
    ),
);

const toCurrencies = computed(() => [
    ...new Map(
        baseFilteredRates.value.map((r) => [
            r.to_currency,
            {
                id: r.to_currency_id,
                code: r.to_currency,
                name: r.to_currency_name,
            },
        ]),
    ).values(),
]);

const filteredRates = computed(() =>
    baseFilteredRates.value.filter((r) =>
        selectedToCurrencies.value.some((c) => c.id === r.to_currency_id),
    ),
);

const nameModel = computed({
    get: () => props.newName,
    set: (value) => emit('update:newName', value),
});

const rename = () => {
    if (props.existingNames.includes(props.newName)) {
        renameError.value = 'A table with this name already exists.';

        return;
    }

    emit('rename-table', props.newName);
    emit('update:newName', '');
};

watch(
    selectedFromCurrency,
    (currency) => emit('update:fromCurrency', currency),
    { immediate: true },
);

watch(selectedToCurrencies, (currencies) =>
    emit('update:toCurrencies', currencies),
);

watch(
    () => props.newName,
    () => {
        renameError.value = '';
    },
);

watch(toCurrencies, (newCurrencies) => {
    selectedToCurrencies.value = [...newCurrencies];
});
</script>

<template>
    <div
        class="mb-4 flex sm:w-lg lg:w-md xl:w-3xl flex-col xl:gap-16 rounded bg-amber-100 p-4 sm:p-8 xl:flex-row xl:flex-wrap"
    >
        <button
            @click="showControls = !showControls"
            class="mb-6 self-start rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600 xl:hidden"
        >
            {{ showControls ? 'Hide edit' : 'Edit' }}
        </button>
        <div
            class="flex flex-col justify-between xl:order-2 xl:ml-auto"
            :class="{ 'hidden xl:flex': !showControls }"
        >
            <FromCurrencySelect v-model="selectedFromCurrency" :rates="rates" />
            <ToCurrencyDropdown
                v-model="selectedToCurrencies"
                :to-currencies="toCurrencies"
            />
            <div class="w-64">
                <Input
                    v-model="nameModel"
                    type="text"
                    maxlength="30"
                    placeholder="Enter table name"
                    class="mt-7 border-gray-400 !bg-white"
                />
                <div class="text-right text-xs text-gray-400">
                    {{ 30 - newName.length }} / 30
                </div>
                <div class="flex justify-end mb-6">
                    <button
                        @click="rename"
                        :disabled="newName.length === 0"
                        class="mt-1 rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        Rename
                    </button>
                </div>
                <div v-if="renameError" class="mt-1 text-sm text-red-500">
                    {{ renameError }}
                </div>
            </div>
        </div>
        <div class="flex h-full flex-col xl:order-1">
            <LatestRatesTable
                :from-currency="selectedFromCurrency"
                :filtered-rates="filteredRates"
            />
        </div>
        <div class="flex w-full justify-end xl:order-3">
            <button
                @click="emit('delete-table')"
                class="mt-7 rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
            >
                Delete table
            </button>
        </div>
    </div>
</template>
