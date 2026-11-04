<script setup>
import {ref} from "vue";
import ConfirmDialog from 'primevue/confirmdialog';
import {useConfirm} from "primevue/useconfirm";
import {useToast} from "primevue/usetoast";
import Message from 'primevue/message';
import ModalLoading from "../../../../Components/ModalLoading.vue";
import MultiDownload from "../../../../Components/MultiDownload.vue";

const toast = useToast();
const confirm = useConfirm();

const props = defineProps(['exercicio']);

/**
 * loading assume valores inteiros nesse programa para controlar o spin aplicado nos botões de processamento.
 * loading = 0 não faz nada
 * loading = 1 aplica spin no Processar do PCASP e abre o loader do e-cidade
 * loading = 2 aplica spin no Processar da Despesa e abre o loader do e-cidade
 * loading = 3 aplica spin no Processar da Receita e abre o loader do e-cidade
 * loading >= 4 Abre o loader do e-cidade
 * @type {Ref<UnwrapRef<number>>}
 */
const loading = ref(0);
const mensagemLoad = ref(null);
const multiDownload = ref(null);
const planos = ref([
    {name: 'União / Federação', code: 'uniao'},
    {name: 'Estadual / Regional', code: 'UF'}
]);


let exercicio = Number(props.exercicio)
const exercicios = ref([
    {name: exercicio, code: exercicio},
    {name: exercicio + 1, code: exercicio + 1}
]);

const selectedExercicio = ref(exercicios.value[0]);
const selectedPlano = ref(planos.value[0]);

const mappedPlans = {
    pcasp: false,
    despesa: false,
    receia: false
};

const routs = {
    pcasp: {
        vinculo: 'v4/api/financeiro/contabilidade/plano-contas/pcasp/vincular-geral',
        importar: 'v4/api/financeiro/contabilidade/plano-contas/pcasp/importar-vinculo',
        mapeamento: 'v4/api/financeiro/contabilidade/plano-contas/emitir/pcasp/mapeamento',
    },
    despesa: {
        vinculo: 'v4/api/financeiro/contabilidade/plano-contas/orcamentario/despesa/vinculo-geral',
        importar: 'v4/api/financeiro/contabilidade/plano-contas/orcamentario/despesa/importar-vinculo',
        mapeamento: 'v4/api/financeiro/contabilidade/plano-contas/emitir/orcamentario/despesa/mapeamento',
    },
    receita: {
        vinculo: 'v4/api/financeiro/contabilidade/plano-contas/orcamentario/receita/vinculo-geral',
        importar: 'v4/api/financeiro/contabilidade/plano-contas/orcamentario/receita/importar-vinculo',
        mapeamento: 'v4/api/financeiro/contabilidade/plano-contas/emitir/orcamentario/receita/mapeamento',
    }
}

async function validaImportacao() {
    loading.value = 4;
    mensagemLoad.value = 'Validando Mapeamentos.';

    try {
        const response = await window.axios.get(
            `v4/api/financeiro/contabilidade/plano-contas/consulta/mapeamentos/${selectedPlano.value.code}/${selectedExercicio.value.code}`
        );

        mappedPlans.pcasp = response.data.data.pcasp;
        mappedPlans.despesa = response.data.data.despesa;
        mappedPlans.receia = response.data.data.receita;
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro', life: 6000});
    }

    loading.value = 0;
}

validaImportacao();

const confirmaAcao = () => {
    return new Promise((accept, reject) => {
        confirm.require({
            header: 'Confirme o processamento.',
            group: 'g1',
            accept,
            reject
        });
    });
}

const confirmProcessing = async (processou) => {
    let executa = true;
    if (processou) {
        await confirmaAcao().catch(() => {
            executa = false;
        })
    }
    return executa;
};

const processarPlanoPcasp = async () => {
    if (await confirmProcessing(mappedPlans.pcasp)) {
        await processarVinculo(1, routs.pcasp.vinculo, 'Processando mapeamento do PCASP.');
    }
};

const processarPlanoDespesa = async () => {
    if (await confirmProcessing(mappedPlans.despesa)) {
        await processarVinculo(2, routs.despesa.vinculo, 'Processando mapeamento da Despesa.');
    }
};

const processarPlanoReceita = async () => {
    if (await confirmProcessing(mappedPlans.receia)) {
        await processarVinculo(3, routs.receita.vinculo, 'Processando mapeamento da Receita.');
    }
};

const importarPlanoReceita = async () => {
    if (await confirmProcessing(false)) {
        await processarVinculo(5, routs.receita.importar, 'Mapeamento executado com base no exercício anterior.');
    }
}

const importarPlanoPcasp = async () => {
    if (await confirmProcessing(false)) {
        await processarVinculo(6, routs.pcasp.importar, 'Mapeamento executado com base no exercício anterior.');
    }
}

const importarPlanoDespesa = async () => {
    if (await confirmProcessing(false)) {
        await processarVinculo(7, routs.despesa.importar, 'Mapeamento executado com base no exercício anterior.');
    }
}

/**
 *
 * @param tipo => 1 = PCASP, 2 = Despesa, 3 = Receita
 * @param rout
 * @param msg
 * @returns {Promise<void>}
 */
const processarVinculo = async (tipo, rout, msg) => {
    loading.value = tipo;
    mensagemLoad.value = msg;

    const formData = new FormData();
    formData.append('exercicio', selectedExercicio.value.code);
    formData.append('tipoPlano', selectedPlano.value.code);
    if ([1,3,6].includes(tipo)) {
        formData.append('apenasAnaliticas', 1);
    }

    if ([3,5].includes(tipo)) {
        formData.append('receita', 1);
    }

    await window.axios.post(rout, formData).then(response => {
        updateMappedPlans(tipo)
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = 0);
};

const updateMappedPlans = (value) => {
    switch (value) {
        case 1:
        case 6:
            mappedPlans.pcasp = true;
            break;
        case 2:
        case 7:
            mappedPlans.despesa = true;
            break;
        case 3: // processamento default
        case 5: // importar plano anterior
            mappedPlans.receia = true;
            break;
    }
}

const imprimeMapeamento = (tipo) => {
    let rout = routs.pcasp.mapeamento;
    switch (tipo) {
        case 2:
            rout = routs.despesa.mapeamento;
            break;
        case 3:
            rout = routs.receita.mapeamento;
            break;
    }

    loading.value = 4;
    mensagemLoad.value = 'Buscando mapeamento.';

    const formData = new FormData();
    formData.append('exercicio', selectedExercicio.value.code);
    formData.append('tipoPlano', selectedPlano.value.code);

    window.axios.post(rout, formData).then(response => {
        multiDownload.value.addFile(
            `${response.data.data.csvLinkExterno}`,
            response.data.message
        );
        multiDownload.value.openModal();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.data.message, summary: 'Erro', life: 6000});
    }).finally(() => loading.value = 0);
}

</script>

<template>
    <section class="container">
        <Message severity="info" :closable="false">
            <h3>Ao clicar em "Processar" o sistema irá procurar as contas compatíveis no e-cidade e criar os vínculos
                de forma automática. </h3>
            <span class="font-bold">Importante!</span> Contas fora do padrão, não serão mapeadas. <br>
            Quando os botões estão verdes, já existe mapeamento para o plano. Seja ele automático ou manual. <br>
            <p>Se "Processar" novamente, todo o mapeamento será apagado, e refeito o vínculo automático.</p>
            Para conferir as contas não mapeadas, imprima o mapeamento clicando em:  <i class="pi pi-print" />
        </Message>
        <Panel header="Processar vínculo automático">
            <div class="card">
                <div class="flex justify-content-start flex-wrap card-container">
                    <div class="flex align-items-center md:w-10rem sm:w-10rem">
                        <label class="font-bold">Exercício:</label>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Dropdown v-model="selectedExercicio" :options="exercicios" optionLabel="name"
                                  placeholder="Selecione o exercício" @change="validaImportacao"/>
                    </div>
                </div>
            </div>
            <div class="card pt-1">
                <div class="flex justify-content-start flex-wrap card-container">
                    <div class="flex align-items-center md:w-10rem">
                        <label class="font-bold" for='plano'>Plano de Contas:</label>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Dropdown id="plano" v-model="selectedPlano" :options="planos" optionLabel="name"
                                  placeholder="Selecione o plano" @change="validaImportacao"/>
                    </div>
                </div>
            </div>

            <div class="card pt-1">
                <div class="flex justify-content-start flex-wrap card-container">
                    <div class="flex align-items-center md:w-10rem ">
                        <label class="font-bold">Plano PCASP:</label>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="processarPlanoPcasp()" label="Processar"
                                :class="{'p-button-success':mappedPlans.pcasp}"
                                icon="pi pi-cog" :icon-class="{'pi-spin':loading == 1}"/>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="imprimeMapeamento(1)" icon="pi pi-print"></Button>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="importarPlanoPcasp()" label="Importar Mapeamento Exercício Anterior"
                                icon="pi pi-cog" :icon-class="{'pi-spin':loading == 6}"/>
                    </div>
                </div>
            </div>

            <div class="card pt-1">
                <div class="flex justify-content-start flex-wrap card-container">
                    <div class="flex align-items-center md:w-10rem ">
                        <label class="font-bold">Plano Despesa:</label>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="processarPlanoDespesa()" label="Processar"
                                :class="{'p-button-success':mappedPlans.despesa}"
                                icon="pi pi-cog" :icon-class="{'pi-spin':loading==2}"/>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="imprimeMapeamento(2)" icon="pi pi-print"></Button>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="importarPlanoDespesa()" label="Importar Mapeamento Exercício Anterior"
                                icon="pi pi-cog" :icon-class="{'pi-spin':loading == 7}"/>
                    </div>
                </div>
            </div>


            <div class="card pt-1">
                <div class="flex justify-content-start flex-wrap card-container">
                    <div class="flex align-items-center md:w-10rem ">
                        <label class="font-bold">Plano Receita:</label>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="processarPlanoReceita()" label="Processar"
                                :class="{'p-button-success':mappedPlans.receia}"
                                icon="pi pi-cog" :icon-class="{'pi-spin':loading == 3}"/>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="imprimeMapeamento(3)" icon="pi pi-print"></Button>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button @click="importarPlanoReceita()" label="Importar Mapeamento Exercício Anterior"
                                icon="pi pi-cog" :icon-class="{'pi-spin':loading == 5}"/>
                    </div>
                </div>
            </div>

        </Panel>

        <Toast/>
        <ConfirmDialog group="g1">
            <template #message>
                <div class="pt-3">
                    <div class="flex justify-content-start flex-wrap card-container">
                        <div class="flex align-items-center pr-3">
                            <i class="pi pi-exclamation-triangle" style="font-size: 2rem;"/>
                        </div>
                        <div class="align-items-center pl-2">
                            <h3>Atenção!!!</h3>
                            Ao confirmar essa ação, todo o mapeamento será apagado e apenas as contas compatíveis serão
                            mapeadas. <br>
                            Só realize essa ação no inicio do exercício ou para reiniciar o mapeamento.<br><br>
                            Após realizar o Vínculo Automático, imprima o relatório de mapeamento para verificar as
                            contas não mapeadas
                        </div>
                    </div>
                </div>
            </template>
        </ConfirmDialog>
        <ModalLoading :isLoading="loading > 0" :message="mensagemLoad"/>
        <MultiDownload ref="multiDownload" :header="'Mapeamento'" :position="'center'"></MultiDownload>
    </section>
</template>
