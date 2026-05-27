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

let colTitles = ['Code', 'Currency', 'Rate'];

</script>

<template>
    <div class="w-full max-w-lg">
        <h3 class="text-lg pb-5">
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
            <div v-if="filteredRates.length === 0" class="text-muted-foreground pl-1 pt-4">
                Please select currencies to view rates.
            </div>
            <TableBody v-else>
                <TableRow v-for="rate in filteredRates" :key="rate.to_currency_id" class="border-gray-400">
                    <TableCell>{{ rate.to_currency }}</TableCell>
                    <TableCell>{{ rate.to_currency_name }}</TableCell>
                    <TableCell>{{ rate.rate }}</TableCell>
                </TableRow>
            </TableBody>
            
        </Table>
    </div>
</template>