<script setup>
import { ref, onMounted } from 'vue'
import {ProcessoInscricaoService} from "../Services/ProcessoInscricaoService.js";
import axios from "axios";
const props = defineProps(['dados'])
const emits = defineEmits(['fechar-inscricao'])
const service = new ProcessoInscricaoService()
const comprovante = ref(props.dados.path)
const protocolo = ref(props.dados.protocolo)
service.limparFormularioStorage()

const title = ref()
localStorage.removeItem('edicao')

const mensagem = ref()
const encerraIncricao = () => {
	service.limparFormularioStorage()
	emits('fechar-inscricao')
}

const print = () => {
	window.open(comprovante.value)
}

onMounted(async () => {
    mensagem.value = (await window.axios.get('v4/api/educacao/files/mensagens')).data.documentos_para_matricula.conteudo
})

</script>

<template>
	<div class="border-2 surface-border border-round surface-ground flex-auto flex justify-content-center align-items-center font-large">
		<Card style="width: 700px; overflow: hidden" class="mt-4 mb-4">
            <template #title>
                <h3>
                    <i class="pi pi-check" style="font-size: 2em;color: green; font-weight: bold; "></i>
                    PARABÉNS
                </h3>
                <Divider />
            </template>
            <template #subtitle>
                <h4>A INSCRIÇÃO FOI CONCLUÍDA COM SUCESSO!</h4>
                <p>
                    Anote ou imprima o número abaixo e
                    guarde com você. Ele é a prova de que a inscrição
                    foi concluída!
                </p>
                <Divider />
            </template>
            
            <template #content>
                <h4>
					<b>NÚMERO DO PROTOCOLO:</b> <span style="color: red; display: block;">{{ protocolo }}</span>
				</h4>
            </template>
            <template #footer>
                <Divider />
                <div class="flex gap-3">
                    <Button label="Fechar" severity="secondary" icon="pi pi-times" @click="encerraIncricao" />
                    <Button label="Imprimir" severity="secondary" icon="pi pi-print" @click="print" />
                </div>
            </template>
        </Card>
    </div>
</template>

<style scoped>
@import url('https://fonts.cdnfonts.com/css/sippin-on-sunshine');

@font-face {
    font-family: 'Sippin On Sunshine', sans-serif;
}
.fonte {
    margin: 0 auto;
    font-family: 'Sippin On Sunshine', sans-serif;
}
</style>
