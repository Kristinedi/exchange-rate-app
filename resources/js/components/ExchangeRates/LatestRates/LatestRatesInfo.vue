<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useAuth } from '@/composables/useAuth';
import LatestRatesCard from '@/components/ExchangeRates/LatestRates/LatestRatesCard.vue';
import TableSwitcher from '@/components/ExchangeRates/LatestRates/TableSwitcher.vue';
import type { Currency, ExchangeRate, Table, SavedTable } from '@/types';

const props = defineProps<{
    rates: ExchangeRate[];
    savedTables: SavedTable[];
}>();

const { isAuthenticated } = useAuth();

const activeTableId = ref<number | null>(null);
const tables = ref<Table[]>([]);
const notification = ref<{
    message: string;
    type: 'success' | 'error';
} | null>(null);
let nextId = 0;

const defaultFromCurrency = computed<Currency>(() => {
    const first = props.rates[0];

    return {
        id: first.from_currency_id,
        code: first.from_currency,
        name: first.from_currency_name,
    };
});

const displayNames = computed(() => {
    const tableNameCount: Record<string, number> = {};
    const result: Record<number, string> = {};

    for (const table of tables.value) {
        const name = table.name;
        tableNameCount[name] = (tableNameCount[name] ?? 0) + 1;
        result[table.id] =
            tableNameCount[name] === 1
                ? name
                : `${name} (${tableNameCount[name]})`;
    }

    return result;
});

const createTable = (id: number, rates: ExchangeRate[]): Table => ({
    id,
    dbId: null,
    name: defaultFromCurrency.value.code,
    isRenamed: false,
    newName: '',
    fromCurrency: defaultFromCurrency.value,
    toCurrencies: [
        ...new Map(
            rates
                .filter(
                    (r) => r.from_currency_id === defaultFromCurrency.value.id,
                )
                .map((r) => [
                    r.to_currency,
                    {
                        id: r.to_currency_id,
                        code: r.to_currency,
                        name: r.to_currency_name,
                    },
                ]),
        ).values(),
    ],
});

const setFirstTableActive = () => {
    activeTableId.value = tables.value[0]?.id ?? null;
};

const setUserTables = () => {
    if (props.savedTables.length === 0) {
        return;
    }

    tables.value = props.savedTables.map((t) => ({
        id: t.id,
        dbId: t.id,
        name: t.name,
        isRenamed: true,
        newName: '',
        fromCurrency: t.from_currency,
        toCurrencies: t.to_currencies,
    }));

    setFirstTableActive();
};

const addTable = () => {
    if (tables.value.length >= 5) return;

    const table = createTable(--nextId, props.rates);

    tables.value.push(table);
    activeTableId.value = table.id;
};

const updateFromCurrency = (tableId: number, currency: Currency) => {
    const table = tables.value.find((t) => t.id === tableId);

    if (!table) return;

    table.fromCurrency = currency;

    if (!table.isRenamed) table.name = currency.code;
};

const updateToCurrencies = (tableId: number, currencies: Currency[]) => {
    const table = tables.value.find((t) => t.id === tableId);

    if (!table) return;

    table.toCurrencies = currencies;
};

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
        activeTableId.value = tables.value[tables.value.length - 1]?.id ?? null;
    }
};

const showNotification = (message: string, type: 'success' | 'error') => {
    notification.value = { message, type };
    setTimeout(() => {
        notification.value = null;
    }, 4000);
};

const saveTables = async () => {
    try {
        const response = await axios.put('/api/user-exchange-rate-tables', {
            tables: tables.value.map((t) => ({
                id: t.dbId,
                name: t.name,
                from_currency_id: t.fromCurrency.id,
                to_currency_ids: t.toCurrencies.map((c) => c.id),
            })),
        });

        response.data.forEach((savedTable: SavedTable, index: number) => {
            tables.value[index].dbId = savedTable.id;
        });

        showNotification('Tables saved successfully!', 'success');
    } catch {
        showNotification(
            'Failed to save tables. Please try again later.',
            'error',
        );
    }
};

onMounted(() => {
    if (!isAuthenticated.value) {
        tables.value = [createTable(--nextId, props.rates)];
        setFirstTableActive();

        return;
    }

    setUserTables();
});
</script>

<template>
    <div>
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

        <div v-if="tables.length === 0" class="py-4 pl-1 text-muted-foreground">
            Please add a table to view rates.
        </div>

        <div v-else class="items-stretch md:flex">
            <TableSwitcher
                :tables="tables"
                :active-table-id="activeTableId"
                :display-names="displayNames"
                @update:active-table-id="switchTable"
            />

            <div class="flex-1">
                <LatestRatesCard
                    v-for="table in tables"
                    :key="table.id"
                    v-show="activeTableId === table.id"
                    v-model:new-name="table.newName"
                    :table="table"
                    :rates="rates"
                    :existing-names="
                        tables
                            .filter((t) => t.id !== table.id)
                            .map((t) => t.name)
                    "
                    @update:from-currency="
                        (currency) => updateFromCurrency(table.id, currency)
                    "
                    @update:to-currencies="
                        (currencies) => updateToCurrencies(table.id, currencies)
                    "
                    @rename-table="(newName) => renameTable(table.id, newName)"
                    @delete-table="() => removeTable(table.id)"
                />
            </div>
        </div>
        <div>
            <button
                v-if="isAuthenticated"
                @click="saveTables"
                class="my-2 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
            >
                Save
            </button>
        </div>
        <div
            v-if="notification"
            :class="
                notification.type === 'success'
                    ? 'text-green-500'
                    : 'text-red-500'
            "
        >
            {{ notification.message }}
        </div>
    </div>
</template>
