<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import JourneyRenderer from '@/components/Journey/JourneyRenderer.vue';

defineProps<{
    journey: App.Data.LearningJourneyData;
}>();
</script>

<template>
    <Head :title="journey.title" />
    <JourneyRenderer :journey="journey" />
</template>
