<script setup>
    import { ref } from 'vue';
    import { useConfirm } from "primevue/useconfirm";
    import ConfirmDialog from "primevue/confirmdialog";
    import ModalLoading from "../../Components/ModalLoading.vue";
    import Toast from 'primevue/toast';
    import { useToast } from "primevue/usetoast";
    import AncoraMatricula from "./Components/AncoraMatricula";
    
    const urlApi = 'v4/api/recursos-humanos/pessoal/previdenciacomplementar';
    const loading = ref(false);
    const matriculaComponent = ref()
    const toast = useToast();
    const optionTipoPrevidencia = ref();
    const temRegistro = ref(false);
    const confirm = useConfirm();

    const inputsDefault = function () {
        this.matricula = '';
        this.cnpj = '';
        this.tipoprevidencia = '';
        this.valordeducao = 0.00;
        this.valorcontribuicao = 0.00;
    };

    const tiposprevidencia = ref([
        {name: '1 - Privada: codIncIRRF em S-1010 = [46, 47, 48]', code: 1},
        {name: '2 - FAPI: codIncIRRF em S-1010 = [61, 62, 66]', code: 2},
        {name: '3 - Funpresp: codIncIRRF em S-1010 = [63, 64, 65]', code: 3},
    ]);

    const inputs = ref(new inputsDefault());

    const consultaDados = async (matricula) => {
        loading.value = true;
        resetaDados();
        try {
            const action = urlApi + "/busca"
            const response = await axios.post(action, {"matricula": matricula});
            if (response.data != "") {
                inputs.value.tipoprevidencia = response.data.tipoPrevidencia;
                inputs.value.cnpj = response.data.cnpj;
                inputs.value.valordeducao = response.data.deducaoRelativa;
                inputs.value.valorcontribuicao = response.data.contribuicaoPatrocinador;
                temRegistro.value = true;
            }
        } catch (e) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Ocorrêu um erro ao buscar os dados de Previdência Complementar da Matrícula: ' + matricula + '.',
                    life: 5000
                }
            );
        }
        loading.value = false;
    }

    const resetaDados = () => {
        temRegistro.value = false;
        inputs.value.tipoprevidencia = "";
        inputs.value.cnpj = "";
        inputs.value.valordeducao = 0;
        inputs.value.valorcontribuicao = 0;        
    }

    const salvar = async () => {
        const dados = {};
        dados.matricula = matriculaComponent.value.codigoMatricula;
        dados.tipoPrevidencia = inputs.value.tipoprevidencia;
        dados.cnpj = inputs.value.cnpj;
        dados.deducaoRelativa = inputs.value.valordeducao;
        dados.contribuicaoPatrocinador = inputs.value.valorcontribuicao;

        if (dados.matricula === null) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Matrícula não informada.',
                    life: 5000
                }
            );
            return false;
        }

        if (dados.tipoPrevidencia === undefined) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Tipo de Previdência não informada.',
                    life: 5000
                }
            );
            return false;
        }

        if (dados.cnpj === undefined || dados.cnpj === "") {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'CNPJ não informado.',
                    life: 5000
                }
            );
            return false;
        }

        if (dados.deducaoRelativa === undefined || dados.deducaoRelativa == 0) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Valor da dedução não informado.',
                    life: 5000
                }
            );
            return false;
        }
        loading.value = true;
        try {
            const action = urlApi + "/salvar"
            const response = await axios.post(action, dados);
            toast.add(
                {
                    severity: 'success',
                    summary: 'Aviso',
                    detail: response.data.message,
                    life: 5000
                }
            );
            temRegistro.value = true;
        } catch (e) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Ocorrêu um erro ao salvar os dados de Previdência Complementar da Matrícula: ' + matricula + '.',
                    life: 5000
                }
            );
        } 
        loading.value = false;
    }
    const excluir = () => {
        loading.value = true;
        let matricula = matriculaComponent.value.codigoMatricula;
        confirm.require({
            message: 'Deseja excluir os dados de previdência complementar da Matrícula: ' + matricula + '?',
            header: 'Exclusão de dados',
            icon: 'pi pi-info-circle',
            rejectClass: 'p-button-text p-button-text',
            acceptClass: 'p-button-danger p-button-text',
            accept: async () => {
                try {
                    const action = urlApi + "/excluir"
                    const response = axios.post(action, {"matricula": matriculaComponent.value.codigoMatricula});
                    toast.add(
                        {
                            severity: 'warn',
                            summary: 'Aviso',
                            detail: 'Dados excluídos com sucesso.',
                            life: 5000
                        }
                    );
                    resetaDados();
                } catch (e) {
                    toast.add(
                        {
                            severity: 'warn',
                            summary: 'Aviso',
                            detail: 'Ocorrêu um erro ao excluir os dados de Previdência Complementar da Matrícula: ' + matricula + '.',
                            life: 5000
                        }
                    );
                }
            },
            reject: () => {
                toast.add({ severity: 'error', summary: 'Aviso', detail: 'Operação Cancelada', life: 3000 });
            }
        });
        loading.value = false;
    }
</script>

<template>
    <ConfirmDialog></ConfirmDialog>
    <ModalLoading :is-loading="loading"/>
    <Toast />

    <div class="container mt-5">
        <div style="display: block;">
            <Panel header="Previdência Complementar">
                <div class="grid">
                    <table>
                        <AncoraMatricula @alteraMatricula="consultaDados" @semMatricula="resetaDados" ref="matriculaComponent"/>
                        <tr>
                            <td>
                                <b>Tipo de previdência complementar:</b>
                            </td>
                            <td>
                                <Dropdown
                                    v-model="inputs.tipoprevidencia"
                                    :options="tiposprevidencia"
                                    optionLabel="name"
                                    optionValue="code"
                                    placeholder="Selecione"
                                    class="w-full md:w-14rem"
                                    :ref="optionTipoPrevidencia"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>CNPJ da entidade de previdência complementar:</b>
                            </td>
                            <td>
                                <InputMask mask="99.999.999/9999-99" id="cnpj" type="text" v-model="inputs.cnpj" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Valor da deducão relativa a previdência complementar:</b>
                            </td>
                            <td>
                                <InputNumber mode="currency" currency="BRL" locale="pt-BR" id="valordeducao" type="text" v-model="inputs.valordeducao" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>
                                    <b>Valor da contribuicão do patrocinador da Previdência</b>
                                </p>
                                <p>
                                    <b>complementar do Servidor Público (Funpresp):</b>
                                </p>
                            </td>
                            <td>
                                <InputNumber mode="currency" currency="BRL" locale="pt-BR" id="valorcontribuicao" type="text" v-model="inputs.valorcontribuicao" />
                            </td>
                        </tr>
                    </table>
                </div>
                <template #loading> Buscando Informações. Aguarde. </template>
            </Panel>
        </div>
        <div>
            <Button label="Salvar" class="mt-4" @click="salvar"/>
            <template v-if="temRegistro">
                <Button label="Excluir" class="mt-4 botaoExcluir"  severity="danger" @click="excluir"/>
            </template>
        </div>
    </div>
</template>
<style>
    .botaoExcluir {
        float: right;
    }
</style>
