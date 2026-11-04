<script setup>
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
import ModalLoading from "../../../../Components/ModalLoading";
import MultiselectPrevidencia from '../../Components/MultiselectPrevidencia.vue';
import MultiselectRegime from '../../Components/MultiselectRegime.vue';
import * as FileSaver from 'file-saver';
import MultiselectTipoFolha from '../../Components/MultiselectTipoFolha.vue';
import MultiselectLotacoes from '../../Components/MultiselectLotacoes.vue';

const apiUrl = 'v4/api/recursos-humanos/pessoal/relatorios/previdenciaencargostributarios'
/**
 * Utils
 */
const loading = ref(false);
const mes = ref(null)
const ano = ref(null)
const lotacoes = ref({});
const previdencia = ref({});

const arquivoSelecionado = ref([]);
const multiselectLotacao = ref([]);
const multiselectRegime = ref([]);
const multiselectPrevidencia = ref([]);


const toast = useToast();

const gerar = async (arquivo) => {

    const dados = {}

    if (mes.value === "" || mes.value === null) {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Mês da competência não informado.', life: 5000 });
        return false;
    }
    dados.mes = mes.value

    if (ano.value === "" || ano.value === null) {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Ano da competência não informado.', life: 5000 });
        return false;
    }
    dados.ano = ano.value

    if (arquivoSelecionado.value.folhasSelecionadas.length === 0) {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Tipo de folha não informado.', life: 5000 });
        return false;
    }

    dados.tipo = arquivoSelecionado.value.folhasSelecionadas

    if (multiselectLotacao.value.todasLotacoes.length === 0) {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Lotação não informado.', life: 5000 });
        return false;
    }

    lotacoes.value = JSON.stringify(multiselectLotacao.value.todasLotacoes)
    dados.lotacoes = multiselectLotacao.value.todasLotacoes

    if (multiselectPrevidencia.value.previdenciaSelecionada.length === 0) {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Previdência não informado.', life: 5000 });
        return false;
    }
    previdencia.value = JSON.stringify(multiselectPrevidencia.value.previdenciaSelecionada)
    dados.previdencia = multiselectPrevidencia.value.previdenciaSelecionada

    if (multiselectRegime.value.previdenciaRegime.length === 0) {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Vinculo não informado.', life: 5000 });
        return false;
    }
    dados.regime = multiselectRegime.value.previdenciaRegime
    dados.arquivo = arquivo

    const action = apiUrl + '/emitir';
    loading.value = true;

    try {
        await axios.post(action, dados).then((result) => {
           
            if (result.data.data == 0) {
                toast.add({ severity: 'warn', summary: 'Aviso', detail: result.data.message, life: 5000 });
                return false;
            } else {
                FileSaver.saveAs(result.data.data.pathExterno, result.data.data.path)
                toast.add({ severity: 'success', summary: 'Aviso', detail: result.data.message, life: 5000 });
                return true;
            }
              
        })
       


    } catch (error) {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível emitir a guia.', life: 5000 });
    }

    loading.value = false;

}


</script>
<template>
    <ModalLoading :is-loading="loading" />
    <section>
        <div class="card">
            <div class="mt-3">
                <div class="container">
                    <Panel header="Competência" class="mb-2">
                        <div class="flex justify-content-center">
                            <div style="margin-right:10px">
                                <InputNumber placeholder="Mês" v-model="mes" :max="12" :min="1"
                                    inputStyle="width: 70px;" :useGrouping="false" />
                                /
                                <InputNumber placeholder="Ano" v-model="ano" inputStyle="width: 90px;" :max="2030"
                                    :min="1900" :useGrouping="false" />
                            </div>
                        </div>
                    </Panel>

                    <Panel header="Tipo de Folha" class="mb-3" toggleable collapsed="true">
                        <MultiselectTipoFolha v-model="arquivoSelecionado" ref="arquivoSelecionado" />
                    </Panel>

                    <Panel header="Lotação" class="mb-3" toggleable collapsed="true">
                        <MultiselectLotacoes v-model="multiselectLotacao" ref="multiselectLotacao" />
                    </Panel>

                    <Panel header="Tipo de Prevídência" class="mb-3" toggleable collapsed="true">
                        <MultiselectPrevidencia v-model="multiselectPrevidencia" ref="multiselectPrevidencia" />
                    </Panel>

                    <Panel header="Tabela de Vínculo" class="mb-3" toggleable collapsed="true">
                        <MultiselectRegime v-model="multiselectRegime" ref="multiselectRegime" />
                    </Panel>

                    <Panel header="Impressão">
                        <div class="flex overflow-hidden ">
                            <div class="flex-grow-1 flex-shrink-1 flex align-items-center justify-content-center gap-4">
                                <Button label="Imprimir" class="p-button" @click="gerar('pdf')">
                                    PDF
                                </Button>
                            </div>
                        </div>

                    </Panel>
                </div>
            </div>
        </div>

    </section>
</template>
