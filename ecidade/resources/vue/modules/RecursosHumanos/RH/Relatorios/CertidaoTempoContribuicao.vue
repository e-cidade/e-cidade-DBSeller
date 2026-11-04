<script setup>
    import { ref } from 'vue';
    import ModalLoading from "../../../Components/ModalLoading.vue";
    import Toast from 'primevue/toast';
    import { useToast } from "primevue/usetoast";
    import AncoraMatricula from "../../Pessoal/Components/AncoraMatricula";
    import Calendar from 'primevue/calendar';
    import MultiDownload from "../../../Components/MultiDownload.vue";

    const urlApi = 'v4/api/recursos-humanos/rh/relatorios/certidaotempocontribuicao';
    const loading = ref(false);
    const matriculaComponent = ref()
    const toast = useToast();
    const periodoInicial = ref();
    const numero = ref();
    const ano = ref();
    const periodoFinal = ref();
    const download = ref(null);
 
    const gerar = async () => {
        let dados = {};
        dados.matricula = matriculaComponent.value.codigoMatricula;
        dados.periodoInicial = null;
        dados.periodoFinal = null;
        let dataAtual = new Date();
        dataAtual.setHours(0,0,0,0);

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

        if (numero.value == null || numero.value == "" || numero.value == 0) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Número não informado.',
                    life: 5000
                }
            );
            return false;
        }

        if (ano.value == null || ano.value == "" || ano.value == 0) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Ano não informado.',
                    life: 5000
                }
            );
            return false;
        }
        dados.numero = numero.value;
        dados.ano = ano.value;
        // Validacoes de inputs de periodo
        if (periodoInicial.value != undefined && periodoInicial.value != "" && periodoInicial.value !== null) {
            dados.periodoInicial = periodoInicial.value.toISOString().split('T')[0];
        }
        if (periodoFinal.value != undefined && periodoFinal.value != "" && periodoFinal.value !== null) {
            dados.periodoFinal = periodoFinal.value.toISOString().split('T')[0];
        }

        // Validamos se a data inicial é superior a data atual
        if (dados.periodoInicial != null) {
            if (periodoInicial.value > dataAtual) {
                toast.add(
                    {
                        severity: 'warn',
                        summary: 'Aviso',
                        detail: 'Período Inicial superior a data atual.',
                        life: 5000
                    }
                );
                return false;
            }
        }
        if (dados.periodoFinal != null) {
            if (periodoFinal.value > dataAtual) {
                toast.add(
                    {
                        severity: 'warn',
                        summary: 'Aviso',
                        detail: 'Período Final superior a data atual.',
                        life: 5000
                    }
                );
                return false;
            }
        }
        if (dados.periodoInicial != null && dados.periodoFinal != null) {
            if (periodoInicial.value > periodoFinal.value) {
                toast.add(
                    {
                        severity: 'warn',
                        summary: 'Aviso',
                        detail: 'Período Inicial superior ao Período Final.',
                        life: 5000
                    }
                );
                return false;
            }
        }
        loading.value = true;
        try {
            const action = urlApi + "/gerar"
            const response = await axios.post(action, dados);
            download.value.addFile(
                response.data.data.pdfLinkExterno,
                `Certidão`
            )
            download.value.openModal();
        } catch (e) {
            toast.add(
                {
                    severity: 'warn',
                    summary: 'Aviso',
                    detail: 'Ocorrêu um erro ao gerar a Certidão de Tempo de Contribuição da Matrícula: ' + dados.matricula + '.',
                    life: 5000
                }
            );
        } 
        loading.value = false;
    }
</script>

<template>
    <ConfirmDialog></ConfirmDialog>
    <ModalLoading :is-loading="loading"/>
    <Toast />

    <div class="container mt-5">
        <div style="display: block;">
            <Panel header="Certidão de Tempo de Contribuição">
                <div class="grid">
                    <table>
                        <AncoraMatricula :exibeRescindido="true" @alteraMatricula="consultaDados" @semMatricula="resetaDados" ref="matriculaComponent"/>
                        <tr>
                            <td>
                                <label class="font-bold">Período: </label>
                            </td>
                            <td>
                                <Calendar :pt="{input:{style: 'width:100px'}}" v-model="periodoInicial" placeholder="Data Inicial" dateFormat="dd/mm/yy" showButtonBar/>
                                À
                                <Calendar :pt="{input:{style: 'width:100px'}}" v-model="periodoFinal" placeholder="Data Final" dateFormat="dd/mm/yy" showButtonBar/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Nº/ANO:</b>
                            </td>
                            <td>
                                <InputNumber style="width: 50px" mode="decimal" :useGrouping="false" :max="99999" :min="1" v-model="numero" :pt="{input:{style: 'width:50px'}}"/>
                                /
                                <InputNumber style="width: 70px" mode="decimal" :useGrouping="false" :max="2300" :min="1950" id="ano" v-model="ano" :pt="{input:{style: 'width:70px'}}"/>
                            </td>
                        </tr>
                    </table>
                </div>
                <template #loading> Buscando Informações. Aguarde. </template>
                <div>
                    <Button label="Gerar" class="mt-4" @click="gerar"/>
                </div>
            </Panel>
            <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
        </div>
    </div>
</template>