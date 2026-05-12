<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-3xl p-10">
      <slot />
    </div>
  </AppLayout>
</template>
