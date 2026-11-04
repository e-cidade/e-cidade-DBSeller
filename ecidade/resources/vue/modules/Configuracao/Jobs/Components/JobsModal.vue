<script setup>

import Dialog from 'primevue/dialog';

const props = defineProps({
    dadosModal: {
        type: Object,
        default: () => ({
            titulo: 'Modal',
            conteudo: ''
        })
    },
    modalShow: {
        type: Boolean,
        default: false
    },
    trataHtml: Function,
});

</script>

<template>
    <Dialog
        :visible="modalShow"
        :header="dadosModal.titulo"
        :style="{ width: '50rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        maximizable
        modal
    >
        <p class="m-0">
            <div v-if="dadosModal.titulo === 'Exception'" v-html="trataHtml(dadosModal.conteudo)"></div>
            <div v-if="dadosModal.titulo === 'Payload'">
                {{ dadosModal.conteudo }}
            </div>
        </p>
    </Dialog>
</template>
