<script setup>
import {onMounted, ref} from "vue";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";

const assunto = ref('');
const mensagem = ref('');
const dia = ref(null);
const diasList = ref([]);
const dadosMensageriaAcordo = ref();
const modalLoading = ref(false);
const textLoad = ref(null);
const toast = useToast();

onMounted(() => {
    buscarConfiguracoes();
});

const buscarConfiguracoes = async () => {
    textLoad.value = "Buscando Configurações...";
    modalLoading.value = true;

    try {
        const resp = await window.axios.get(
            `v4/api/patrimonial/contratos/acordo/buscar-configuracoes`,
        );

        dadosMensageriaAcordo.value = resp.data.data;
        assunto.value = dadosMensageriaAcordo.value.ac51_assunto;
        mensagem.value = dadosMensageriaAcordo.value.ac51_mensagem;
        diasList.value = dadosMensageriaAcordo.value.dias;
    } catch (e) {

    } finally {
        modalLoading.value = false;
    }
}

const salvarDias = async () => {
    if (!dia.value) {
        toast.add({severity: 'warn', summary: 'campo obrigatório', detail: 'Preencha o campo dia.', life: 3000});
        return;
    }

    textLoad.value = "Salvando dia...";
    modalLoading.value = true;

    const parametros = {};
    parametros.dia = dia.value;


    try {
        const resp = await window.axios.post(
            `v4/api/patrimonial/contratos/acordo/salvar-dias/${dadosMensageriaAcordo.value.ac51_sequencial}`,
                parametros
        );

        if (resp.status === 200) {
            modalLoading.value = false
            await buscarConfiguracoes();
            toast.add({severity: 'success', summary: 'Sucesso', detail: 'Dia incluído com sucesso.', life: 3000});
        }

    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro', detail: e.message, life: 3000});
    } finally {
        modalLoading.value = false;
        dia.value = null;
    }
}


const removerDia = async (dia) => {
    textLoad.value = "Removendo dia...";
    modalLoading.value = true;

    try {
        const resp = await window.axios.delete(
            `v4/api/patrimonial/contratos/acordo/deletar-dia/${dia.id}`
        );

        if (resp.status === 200) {
            modalLoading.value = false;
            await buscarConfiguracoes();
            toast.add({severity: 'success', summary: 'Sucesso', detail: 'Dia removido com sucesso.', life: 3000});
        }

    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro', detail: e.message, life: 3000});
    } finally {
        modalLoading.value = false;
    }
}

const salvarConfiguracao = async () => {

    if (assunto.value === '') {
        toast.add({severity: 'warn', summary: 'campo obrigatório', detail: 'Preencha o campo assunto.', life: 3000});
        return;
    }

    if (mensagem.value === '') {
        toast.add({severity: 'warn', summary: 'campo obrigatório', detail: 'Preencha o campo mensagem.', life: 3000});
        return;
    }

    const parametros = {};
    parametros.ac51_assunto = assunto.value;
    parametros.ac51_mensagem = mensagem.value;

    textLoad.value = "Buscando Configurações...";
    modalLoading.value = true;

    try {
        const resp = await window.axios.put(
            `v4/api/patrimonial/contratos/acordo/salvar-config/${dadosMensageriaAcordo.value.ac51_sequencial}`,
            parametros
        );

        if (resp.status === 200) {
            modalLoading.value = false;
            await buscarConfiguracoes();
            toast.add({severity: 'success', summary: 'Sucesso', detail: 'Configuração salva com sucesso.', life: 3000});
        }

    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro', detail: e.message, life: 3000});
    } finally {
        modalLoading.value = false;
    }
}

</script>

<template>
    <ModalLoading
        :is-loading="modalLoading"
        :message="textLoad"
    />
    <div class="corpo">
        <Fieldset legend="Parâmetros de Contratos a Vencer" style="width: 60%;margin-top: 10px;">
            <Fieldset legend="Variáveis Disponíveis" :toggleable="true">
                <div>
                    <p>[dias]	- Dias que antecedem o vencimento.</p>
                    <p>[ano]	- Ano do acordo.</p>
                    <p>[numero]	- Número do acordo.</p>
                    <p>[data_inicial]	- Inicio da vigência do acordo.</p>
                    <p>[data_final]	- Final da vigência do acordo.</p>
                </div>
            </Fieldset>
            <div class="div-assunto">
                <label for="assunto"><b>Assunto:</b></label>
                <InputText id="assunto" v-model="assunto" style="width: 260px;"></InputText>
            </div>
            <Fieldset legend="Mensagem" style="margin-top: 10px">
                <Textarea v-model="mensagem" rows="5" cols="30" style="width:100%"/>
            </Fieldset>

            <Fieldset legend="Dias a Vencer" style="margin-top: 10px">
                <div class="div-dia">
                    <label for="dia"><b>Dia:</b></label>
                    <InputNumber id="dia" v-model="dia"></InputNumber>
                    <Button label="Adicionar" @click="salvarDias"/>
                </div>
                <DataTable
                    :value="diasList"
                    style="margin-top: 10px;"
                    v-if="diasList.length > 0"
                >
                    <Column field="dia" header="Dias" style="width: 200px" />
                    <Column style="width: 150px" field="acao" header="Ação">
                        <template #body="{ data }">
                            <i
                                class="pi pi-trash" style="font-size: 1rem; color:red;"
                                @click="removerDia(data)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </Fieldset>
            <div class="div-btn-salvar">
                <Button  @click="salvarConfiguracao" label="Salvar" style="width: 150px"/>
            </div>
        </Fieldset>
    </div>
</template>

<style scoped>
.corpo {
    display:flex;
    justify-content: center;
}
.div-assunto {
    display:flex;
    flex-direction: row;
    gap:10px;
    margin-top:10px;
    align-items: center;
}
.div-dia {
    display:flex;
    flex-direction: row;
    gap:10px;
    margin-top:10px;
    align-items: center;
}
.div-btn-salvar {
    display:flex;
    justify-content: center;
    margin-top:20px;
}
</style>
