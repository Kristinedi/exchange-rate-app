<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useEchoPublic } from '@laravel/echo-vue';
import axios from 'axios';
import { useAuth } from '@/composables/useAuth';
import LatestRatesInfo from '@/components/ExchangeRates/LatestRates/LatestRatesInfo.vue';
import type { ExchangeRate, SavedTable } from '@/types';

const { isAuthenticated } = useAuth();

const rates = ref<ExchangeRate[]>([]);
const error = ref<string | null>(null);
const isLoading = ref(true);
const savedTables = ref<SavedTable[]>([]);

const fetchRates = async (): Promise<void> => {
    const response = await axios.get('/api/exchange-rates/latest-exchange-rates');

    rates.value = response.data;
};

useEchoPublic('exchange-rates', '.rates.updated', async () => {
    try {
        await fetchRates();
    } catch (e) {
        // keep showing previous rates
    }
});

onMounted(async () => {
    try {
        await fetchRates();

        if (!isAuthenticated.value) return;

        const response = await axios.get('/api/user-exchange-rate-tables');

        savedTables.value = response.data;
    } catch (e) {
        error.value = 'Failed to load data. Please try again later.';
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <div>
        <h1 class="mb-8 text-center text-2xl font-bold">
            Latest Exchange Rates
        </h1>

        <div v-if="error" class="text-red-500">
            {{ error }}
        </div>
        <div v-else-if="isLoading">Loading...</div>
        <div v-else-if="rates.length === 0">No rates available.</div>
        <div v-else>
            <LatestRatesInfo :rates="rates" :saved-tables="savedTables" />
        </div>
    </div>
</template>
