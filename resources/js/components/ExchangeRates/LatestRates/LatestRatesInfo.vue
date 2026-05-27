<script setup lang="ts">
import { computed, ref } from 'vue';
import type { Currency, ExchangeRate } from '@/types';
import LatestRatesCard from '@/components/ExchangeRates/LatestRates/LatestRatesCard.vue';
import { DEFAULT_CURRENCY } from '@/constants/currencies';

const props = defineProps<{
    rates: ExchangeRate[];
}>();

interface Table {
    id: number;
    name: string;
    isRenamed: boolean;
    newName: string;
}

let nextId = 2;
const tables = ref<Table[]>([
    {
        id: 1,
        name: DEFAULT_CURRENCY.code,
        isRenamed: false,
        newName: '',
    },
]);
const activeTableId = ref<number>(1);

const addTable = () => {
    if (tables.value.length >= 5) return;

    const table = {
        id: nextId,
        name: DEFAULT_CURRENCY.code,
        isRenamed: false,
        newName: '',
    };

    tables.value.push(table);
    activeTableId.value = table.id;
    nextId++;
};

const updateFromCurrency = (tableId: number, currency: Currency) => {
    const table = tables.value.find((t) => t.id === tableId);

    if (!table || table.isRenamed) return;

    table.name = currency.code;
};

const displayNames = computed(() => {
    const tableNameCount: Record<string, number> = {};
    const result = new Map<number, string>();

    for (const table of tables.value) {
        const name = table.name;

        tableNameCount[name] = (tableNameCount[name] ?? 0) + 1;

        result.set(
            table.id,
            tableNameCount[name] === 1
                ? name
                : `${name} (${tableNameCount[name]})`,
        );
    }

    return result;
});

const switchTable = (tableId: number) => {
    const current = tables.value.find((t) => t.id === activeTableId.value);
    if (current) current.newName = '';
    activeTableId.value = tableId;
};

const renameTable = (tableId: number, newName: string) => {
    const table = tables.value.find((t) => t.id === tableId);
    if (!table) return;
    table.name = newName;
    table.isRenamed = true;
};

const removeTable = (tableId: number) => {
    tables.value = tables.value.filter((t) => t.id !== tableId);

    if (activeTableId.value === tableId) {
        activeTableId.value = tables.value[tables.value.length - 1]?.id;
    }
};
</script>

<template>
    <div class="mb-6 flex items-center gap-4">
        <button
            @click="addTable"
            :disabled="tables.length >= 5"
            class="rounded bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
        >
            + Add new table
        </button>
        <div class="text-sm text-muted-foreground">Add up to 5 tables</div>
    </div>

    <div v-if="tables.length === 0" class="pt-4 pl-1 text-muted-foreground">
        Please add a table to view rates.
    </div>

    <div v-else class="flex items-stretch">
        <div class="my-6 flex w-[150px] flex-col gap-2 break-words">
            <button
                v-for="table in tables"
                :key="table.id"
                @click="switchTable(table.id)"
                :class="[
                    'rounded-l px-3 py-2 text-left text-sm',
                    activeTableId === table.id
                        ? 'bg-amber-100'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                ]"
            >
                {{ displayNames.get(table.id) }}
            </button>
        </div>

        <div class="flex-1">
            <LatestRatesCard
                v-for="table in tables"
                :key="table.id"
                v-show="activeTableId === table.id"
                v-model:new-name="table.newName"
                :rates="rates"
                :existing-names="tables.filter(t => t.id !== table.id).map(t => t.name)"
                @update:from-currency="
                    (currency) => updateFromCurrency(table.id, currency)
                "
                @rename-table="(newName) => renameTable(table.id, newName)"
                @delete-table="() => removeTable(table.id)"
            />
        </div>
    </div>
</template>
