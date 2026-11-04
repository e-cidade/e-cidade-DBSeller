<script setup async>
    import Fieldset from 'primevue/fieldset';
    import SelectButton from 'primevue/selectbutton';
    import Button from 'primevue/button';
    import { onMounted, ref } from "vue";
    import { useToast } from "primevue/usetoast";
    import Dialog from 'primevue/dialog';
    import MultiDownload from "../../../../Components/MultiDownload.vue";
    import VerificaEscola from "../../Components/VerificaEscola.vue";
    import ModalLoading from "../../../../Components/ModalLoading.vue";

    const props = defineProps(['departamento', 'modulo']);
    const toast = useToast();
    const download = ref(null)
    const routes = {
        atividades: `v4/api/educacao/secretaria/tabelas/funcoes-atividades`,
        emissao: `v4/api/educacao/secretaria/relatorios/funcoes-atividades`,
    }

    let selectedActivities = ref([]);
    const optionsActivities = ref(null);
    const isLoading = ref(false);

    const sort = ref(['Atividade', 'Nome']);
    const sortValue = ref('Nome');
    let showValue = ref(null);
    const show = ref([
                        {
                        name: 'Data de saída',
                        value: 1,
                        disable: false
                        },
                        {
                        name: 'Data Fim',
                        value: 2,
                        disable: false
                        },
                        {
                        name: 'CPF',
                        value: 3,
                        disable: false
                        },
                        {
                        name: 'Assinatura',
                        value: 4,
                        disable: false
                        }
                    ]);
    let situationActivitiesValue = ref({ name: null, value: null, constant: null });
    const situationActivities = ref([
                                        {
                                        name: 'Ativas',
                                        value: 1,
                                        constant: true
                                        },
                                        {
                                        name: 'Inativas',
                                        value: 2,
                                        constant: true
                                        },
                                        {
                                        name: 'Todas',
                                        value: 3,
                                        constant: true
                                        }
                                    ]);
    let situationAgentsValue = ref(null);
    const situationAgents = ref([
                                    {
                                    name: 'Ativos',
                                    value: 1,
                                    constant: false
                                    },
                                    {
                                    name: 'Inativos',
                                    value: 2,
                                    constant: false
                                    },
                                    {
                                    name: 'Todos',
                                    value: 3,
                                    constant: false
                                    }
                                ]);

    const visible = ref(false);
    const dialogTitles = {
        sort: 'Ordenar por',
        show: 'Exibir',
        activities: 'Situação da atividade',
        servants: 'Situação dos servidores'
    }
    const helpMessages = {
        sort : 'Ao escolher ordenar por atividade, os registros virão em ordem alfabética levando em consideração a atividade exercida. Caso escolha ordenar por nome, os registros levarão em consideração o nome do servidor.',
        show : 'Aqui você pode escolher quais campos serão exibidos no relatório. Caso necessite, todos os campos podem ser incluídos.',
        activities : 'Aqui serão filtradas as atividades conforme sua situação (Ativas, Inativas ou Todas)',
        servants : 'Aqui serão filtradas os servidores conforme sua situação (Ativos, Inativos ou Todos)'
    }
    const helpDialogTitle = ref('');
    const helpMessage = ref('');

    // Redefinir os valores para o estado original
    function limparFiltros() {
        sortValue.value = 'Nome';
        showValue.value = null;
        situationActivitiesValue.value = null;
        situationAgentsValue.value = null;
        isSignatureSelected = false;

        // Redefinir os estados disable e constant das arrays show, situationActivities e situationAgents
        for (const item of show.value) {
            item.disable = false; // Valor original para disable
        }
        for (const item of situationActivities.value) {
            item.constant = true; // Valor original para constant
        }
        for (const item of situationAgents.value) {
            item.constant = false; // Valor original para constant
        }
    }

    let allActivityCodes = [];
    function buscaAtividades() {
        try {
            axios.get(routes.atividades).then(retorno => {
                optionsActivities.value = retorno.data.data.map(atividade => {
                    allActivityCodes.push(atividade.codigo);
                    return {
                        name: atividade.nome,
                        code: atividade.codigo
                    }
                });
                optionsActivities.value.sort((a, b) => {
                    return a.name.localeCompare(b.name);
                });
            });
        } catch (e) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
            });
        }
    }

    let isSignatureSelected = false;
    function handleShowOptionChange(options) {
        const values = options.map(option => option);
            isSignatureSelected = values.includes(4);
            updateConstants(isSignatureSelected);
    }

function updateConstants(isSignatureSelected) {
        // console.log('isSignatureSelected: ' + isSignatureSelected)
        // situationActivitiesValue = { name: 'Ativas', value: 1, constant: false };
        for (const item of situationAgents.value) {
            if (isSignatureSelected) {
                if (item.value === 1) {
                    item.constant = false;
                } else {
                    item.constant = true;
                }
            }
            else {
                item.constant = false;
            }
        }

        for (const item of situationActivities.value) {
            if (isSignatureSelected) {
                if (item.value === 1) {
                    item.constant = false;
                } else {
                    item.constant = true;
                }
            }
        }
        return isSignatureSelected;
    }

    // Habilitar ou desabilitar Situa��o Atividade conforme sele��o da Situa��o Servidores
    function handleAgentsOption(newValue) {
        if (newValue !== null) {
            const isActive = newValue.value === 1;
            const isInactive = newValue.value === 2;
            const isAll = newValue.value === 3;

            for (const item of situationActivities.value) { // Situa��o da Atividade
                if (isSignatureSelected && isActive && item.value === 1) {
                    item.constant = false;
                } else if (!isSignatureSelected && !isInactive) {
                    item.constant = false;
                } else if (!isSignatureSelected && isInactive) {
                    item.constant = true;
                    situationActivitiesValue.value = 2;
                } else {
                    item.constant = true;
                }
            }

            for (const item of show.value) { // Exibir
                if (isActive && item.value === 1) { // Situa��o Servidor 'Ativos' => Desabilita 'Data de sa�da'
                    item.disable = true;
                } else if ((isInactive || isAll) && item.value === 4) { // Situa��o Servidor 'Ativos' ou 'Todos' => Desabilita 'Assinatura'
                    item.disable = true;
                } else if (item.value !== 2) {
                    item.disable = false;
                }
            }
        } else {
            for (const item of situationActivities.value) {
                item.constant = true;
            }
            for (const item of show.value) {
                if (item.value === 1) {
                    item.disable = false;
                }
            }
        }
    }

    function handleActivitiesOption(newValue) {
        if (newValue !== null) {
            const isActive = newValue.value === 1;
            const isInactive = newValue.value === 2;
            const isAll = newValue.value === 3;

            for (const item of show.value) { // Exibir
                if (isActive && item.value === 2) { // Situa��o Atividade 'Ativas' => Desabilita 'Data Fim'
                    item.disable = true;
                } else if ((isInactive || isAll) && item.value === 2) { // Situa��o Atividade 'Inativas' ou 'Todas' => Habilita 'Data Fim'
                    item.disable = false;
                } else if ((isInactive || isAll) && item.value === 4) { // Situa��o Servidor 'Ativos' ou 'Todos' => Desabilita 'Assinatura'
                    item.disable = true;
                } else if (isActive && item.value === 4) {
                    item.disable = false;
                }
            }
        } else {
            for (const item of show.value) {
                if (item.value === 2 || item.value === 4) {
                    item.disable = false;
                }
            }
        }
    }

function validador() {
    const values = showValue.value != null ? showValue.value.map(item => item) : null;
    const isDataSaida = values != null ? values.includes(1) : false; // Verifica se nos filtros 'Exibir' tem 'Data de sa�da'
    const isDataFim = values != null ? values.includes(2) : false; // Verifica se nos filtros 'Exibir' tem 'Data Fim'

    if (selectedActivities.value.length === 0) {
            toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: 'Por favor, selecione ao menos uma atividade.',
                    life: 10000
                });
                return false;
        } else if (sortValue.value === null) {
            toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: 'Por favor, selecione a forma de ordenamento.',
                    life: 10000
                });
                return false;
        } else if (situationAgentsValue.value == null) {
            toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: 'Por favor, selecione uma situação para servidores.',
                    life: 10000
                });
                return false;
        } else if (situationActivitiesValue.value == null) {
            toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: 'Por favor, selecione uma situação para atividades.',
                    life: 10000
                });
                return false;
        } else if (situationAgentsValue.value.value == 1 && isDataSaida) {
            toast.add({
                severity: 'warn',
                summary: 'Atenção',
                detail: "Atenção!! Quando a 'Situação do servidor' for igual a 'Ativo', o campo 'Data de Saída' não deve ser selecionado!",
                life: 10000
            });
        return false;
        } else if (situationActivitiesValue.value.value == 1 && isDataFim) {
            toast.add({
                severity: 'warn',
                summary: 'Atenção',
                detail: "Atenção!! Quando a 'Situação da atividade' for igual a 'Ativa', o campo 'Data Fim' não deve ser selecionado!",
                life: 10000
            });
        return false;
        } else if (isSignatureSelected && (situationAgentsValue.value.value != 1 || situationActivitiesValue.value.value != 1)) {
            toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: "Atenção! Quando o campo assinatura estiver selecionado os campos 'Situação da atividade' e 'Situação dos servidores' devem ser 'Ativos'.",
                    life: 15000
                });
                return false;
        }
        return true;
    }

    function emitir() {
        try {
            if (validador()) {
                isLoading.value = true;
                const params = {
                atividade: selectedActivities.value.length > 0 ? selectedActivities.value.map(activity => activity.code) : allActivityCodes,
                order: sortValue.value,
                parametrosOpcionais: showValue.value !== null ? showValue.value.map(item => item) : [0],
                codSituacaoAtividades: situationAgentsValue.value.value == 2 ? 2 : situationActivitiesValue.value.value,
                codSituacaoServidores: situationAgentsValue.value.value,
                };

                if (params.codSituacaoAtividades === 2) {
                    params.situacaoAtividade = false
                } else if (params.codSituacaoAtividades === 1) {
                    params.situacaoAtividade = true
                }

                if (situationAgentsValue.value.value != 3) {
                    params.situacaoServidores =
                    situationAgentsValue.value.value == 1 ? true : false
                }

                axios.get(routes.emissao, { params }).then(response => {
                    response.data.data.forEach(relatorio => {
                        download.value.addFile(
                            `${relatorio.pathExterno}`,
                            `${relatorio.name}`
                        )
                    });
                        download.value.openModal();
                        isLoading.value = false;
                });
            }
        } catch (e) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: 'Ocorreu um erro ao processar a solicitação'
            });
            isLoading.value = false;
        }
    }

    let escolaExiste = ref(false);

    onMounted(async () => {
        if (escolaExiste || props.modulo == '7159') {
            buscaAtividades();
        }
    });

</script>

<template>
    <div v-show="!escolaExiste && props.modulo != '7159'">
        <VerificaEscola @is-escola="(e) => escolaExiste = e" :departamento="props.departamento"></VerificaEscola>
    </div>
    <div v-show="escolaExiste || props.modulo == '7159'">
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <div class="container-wrapper">
            <div class="card">
                <Fieldset  style="width: 50vw; margin: 0 auto 1rem;" legend="Selecione as atividades">
                    <div class="select-activities">
                        <div class="field col-12 md:col-6">
                            <MultiSelect
                            id="multiselect"
                            v-model="selectedActivities"
                            v-bind:options="optionsActivities"
                            filter
                            optionLabel="name"
                            placeholder="Selecione as atividades"
                            v-bind:maxSelectedLabels="3"
                            class="w-full md:w-40rem">
                            <template #value>
                                <div class="py-2 px-3">
                                    <span v-if="selectedActivities.length <= 1"><b>{{ selectedActivities.length }}</b> item selecionado.</span>
                                    <span v-else>
                                        <b>{{ selectedActivities.length }}</b> iten{{ selectedActivities.length > 1 ? 's' : '' }} selecionados.
                                    </span>
                                </div>
                            </template>
                            </MultiSelect>
                        </div>
                    </div>
                </Fieldset>
                <Fieldset legend="Configurações do relatório" :toggleable="true">
                    <div class="settings-wrapper">
                        <div class="option">
                            <span>Situação dos servidores:
                                <i @click="visible = true; helpMessage = helpMessages['servants']; helpDialogTitle = dialogTitles['servants']" class="pi pi-question-circle" style="color: darkgray"></i>
                            </span>
                            <SelectButton @click="handleAgentsOption(situationAgentsValue)" v-model="situationAgentsValue" :options="situationAgents" optionLabel="name" optionDisabled="constant" />
                        </div>
                        <div class="option">
                            <span>Situação da atividade:
                                <i @click="visible = true; helpMessage = helpMessages['activities']; helpDialogTitle = dialogTitles['activities']" class="pi pi-question-circle" style="color: darkgray"></i>
                            </span>
                            <SelectButton @click="handleActivitiesOption(situationActivitiesValue)" v-model="situationActivitiesValue" :options="situationActivities" optionLabel="name" optionDisabled="constant" />
                        </div>
                        <div class="option">
                            <span>Ordernar por:
                                <i @click="visible = true; helpMessage = helpMessages['sort']; helpDialogTitle = dialogTitles['sort']" class="pi pi-question-circle" style="color: darkgray"></i>
                            </span>
                            <SelectButton v-model="sortValue" :options="sort" aria-labelledby="basic"/>
                        </div>
                        <div class="option">
                            <span>Exibir:
                                <i @click="visible = true; helpMessage = helpMessages['show']; helpDialogTitle = dialogTitles['show']" class="pi pi-question-circle" style="color: darkgray"></i>
                            </span>
                                <SelectButton @click="handleShowOptionChange(showValue)" v-model="showValue" :options="show" optionLabel="name" optionValue="value" multiple aria-labelledby="multiple" optionDisabled="disable">
                                </SelectButton>
                        </div>
                        <div class="eraser-button">
                            <Button icon="pi pi-eraser" severity="danger" label="Limpar Filtros" rounded outlined aria-label="Erase" @click="limparFiltros"></Button>
                        </div>
                    </div>
                </Fieldset>
                <div class="card flex justify-content-center s  -wrap gap-3">
                    <Button class="p-button" icon="pi pi-print" label="Imprimir" @click="emitir"></Button>
                </div>
                <Dialog v-model:visible="visible" modal :header="helpDialogTitle" :style="{ width: '50vw' }">
                    <p>{{ helpMessage }}</p>
                </Dialog>
            </div>
        </div>
        <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
        <ModalLoading :isLoading="isLoading" :message="'Carregando Dados'"></ModalLoading>
    </div>
</template>

<style lang="scss" scoped>
    .container-wrapper {
        background-color: #E0E0E0;
        max-width: 70%;
        margin: 2rem auto;
        padding: 2rem;
        box-sizing: border-box;
        border-radius: 0.6rem;
    }

    .select-activities {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    fieldset {
        margin-bottom: 1rem;
        border-radius: 0.6rem;
        background-color: #E0E0E0;
    }

    .settings-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }

    .option {
        flex-grow: 1;
        flex-basis: 200;
    }

    .eraser-button {
        align-self: flex-end;
        height: 50%;
    }

    .p-button {
        border-radius: 0.3rem;
    }

    i {
        cursor: pointer;
    }
</style>
