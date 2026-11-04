<script setup>
defineProps(['currentStep', 'totalSteps'])
</script>
<template>
    <div class="flex w-full">
        <template v-if="currentStep == 0">
            <div
                class="border-none w-12rem font-bold flex align-items-center"
            >
                Indicativo de Período
            </div>
        </template>
        <template v-if="currentStep !== 0">
        <div class="font-bold justify-content-end text-orange-500"> 
                <b style="margin-right: 8px">Etapa</b> {{ currentStep }}
                <b style="margin: 0 8px 0 8px;">de</b> {{ totalSteps }}
            </div>
        </template>
    </div>
</template>
