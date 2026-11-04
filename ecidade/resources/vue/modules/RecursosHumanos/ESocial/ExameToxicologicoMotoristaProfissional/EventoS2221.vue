<template>
    <div class="container">
        <fieldset>
            <legend>Exame Toxicológico do Motorista Profissional Empregado</legend>
            <EventoS2221MultiStepForm
                :instituicao="props.instituicao"
                :form="[Step1,Step2]"
            :steps="['Informações de identificação do trabalhador e do vínculo.',
            'Informações do exame toxicológico do motorista profissional.']"/>
        </fieldset>
    </div>
</template>
<script setup>
import Step1 from './Components/Step1.vue';
import Step2 from './Components/Step2.vue';
import EventoS2221MultiStepForm from './Components/EventoS2221MultiStepForm'
const props = defineProps(['instituicao'])

</script>
