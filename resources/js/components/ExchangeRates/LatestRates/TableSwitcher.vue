<script setup lang="ts">
import { useMediaQuery } from '@vueuse/core';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/components/ui/select';
import type { Table } from '@/types';

defineProps<{
    tables: Table[];
    activeTableId: number | null;
    displayNames: Record<number, string>;
}>();

const emit = defineEmits<{
    'update:activeTableId': [id: number];
}>();

const isMobile = useMediaQuery('(max-width: 767px)');
</script>

<template>
    <div v-if="isMobile" class="pb-2">
        <Select
            :model-value="activeTableId?.toString()"
            @update:model-value="emit('update:activeTableId', Number($event))"
        >
            <SelectTrigger class="w-full">
                <span>{{
                    activeTableId !== null
                        ? displayNames[activeTableId]
                        : 'Select table'
                }}</span>
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="table in tables"
                    :key="table.id"
                    :value="table.id.toString()"
                >
                    {{ displayNames[table.id] }}
                </SelectItem>
            </SelectContent>
        </Select>
    </div>

    <div v-else class="my-6 flex w-[150px] flex-col gap-2 break-words">
        <button
            v-for="table in tables"
            :key="table.id"
            @click="emit('update:activeTableId', table.id)"
            :class="[
                'rounded-l px-3 py-2 text-left text-sm',
                activeTableId === table.id
                    ? 'bg-amber-100'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
        >
            {{ displayNames[table.id] }}
        </button>
    </div>
</template>
