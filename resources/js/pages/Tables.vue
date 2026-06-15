<script setup lang="ts">
import { tables } from '@/routes';
import LatestExchangeRates from '@/components/ExchangeRates/LatestRates/LatestExchangeRates.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tables',
                href: tables(),
            },
        ],
    },
});
</script>

<template>
    <div class="mx-auto max-w-7xl mt-5 px-4 sm:px-6 lg:px-8">
        <LatestExchangeRates />
    </div>
</template>
