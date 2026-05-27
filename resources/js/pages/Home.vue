<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import LatestExchangeRates from '@/components/ExchangeRates/LatestRates/LatestExchangeRates.vue';
// import ExchangeRateChart from '@/components/ExchangeRateChart.vue';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <div class="items-top relative min-h-screen bg-gray-100 sm:p-36">
        <div
            v-if="canRegister"
            class="fixed top-0 right-0 hidden px-6 py-4 sm:block"
        >
            <Link
                v-if="$page.props.auth.user"
                :href="dashboard()"
                class="text-sm text-gray-700 underline dark:text-gray-500"
            >
                Dashboard
            </Link>
            <Link
                v-else
                :href="login()"
                class="text-sm text-gray-700 underline dark:text-gray-500"
            >
                Log in
            </Link>
            <Link
                v-if="!$page.props.auth.user && canRegister"
                :href="register()"
                class="ml-4 text-sm text-gray-700 underline dark:text-gray-500"
            >
                Register
            </Link>
        </div>
        <div class="mx-auto max-w-7xl">
            <LatestExchangeRates />
        </div>
        <div>
            <!-- <ExchangeRateChart /> -->
        </div>
    </div>
</template>
