<script setup lang="ts">
import type { ExchangeRate } from '@/types';
import { onMounted, ref } from 'vue';
import { useEchoPublic } from '@laravel/echo-vue';
import LatestRatesInfo from '@/components/ExchangeRates/LatestRates/LatestRatesInfo.vue';

const rates = ref<ExchangeRate[]>([]);
const error = ref<string | null>(null);

const fetchRates = async (): Promise<void> => {
    try {
        const response = await fetch(
            '/api/exchange-rates/latest-exchange-rates',
        );

        if (!response.ok) {
            error.value =
                'Failed to load exchange rates. Please try again later.';
            return;
        }

        rates.value = await response.json();
    } catch (e) {
        error.value = 'Something went wrong. Please try again later.';
    }
};

useEchoPublic('exchange-rates', '.rates.updated', fetchRates);
onMounted(fetchRates);
</script>

<template>
    <div>
        <h1 class="mb-8 text-center text-2xl font-bold">
            Latest Exchange Rates
        </h1>

        <div v-if="error" class="text-red-500">
            {{ error }}
        </div>
        <div v-else-if="rates.length === 0">
            Loading...
        </div>
        <div v-else>
            <LatestRatesInfo :rates="rates" />
        </div>
    </div>
</template>
