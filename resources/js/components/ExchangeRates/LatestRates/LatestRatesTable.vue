<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { Currency, ExchangeRate } from '@/types';

const props = defineProps<{
    fromCurrency: Currency;
    filteredRates: ExchangeRate[];
}>();

const colTitles = ['Code', 'Currency', 'Rate'];
</script>

<template>
    <div class="w-full max-w-lg">
        <h3 class="pb-5 text-lg">
            Exchange rates for {{ fromCurrency.code }} ({{ fromCurrency.name }}):
        </h3>
        <Table>
            <TableHeader>
                <TableRow class="border-gray-600">
                    <TableHead v-for="title in colTitles" :key="title">
                        {{ title }}
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow
                    v-for="rate in filteredRates"
                    :key="rate.to_currency_id"
                    class="border-gray-400"
                >
                    <TableCell>{{ rate.to_currency }}</TableCell>
                    <TableCell>{{ rate.to_currency_name }}</TableCell>
                    <TableCell>{{ rate.rate }}</TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <div
            v-if="filteredRates.length === 0"
            class="pt-4 pl-1 text-muted-foreground"
        >
            Please select target currencies to view rates.
        </div>
    </div>
</template>
