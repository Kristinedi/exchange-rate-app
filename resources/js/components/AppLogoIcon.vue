<script setup lang="ts">
import { HandCoins } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <HandCoins :class="className" v-bind="$attrs" />
</template>
