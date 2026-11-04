<script setup>
    import { ref, onMounted } from 'vue';
    import { useToast } from "primevue/usetoast";
    import ModalLoading from "../../../Components/ModalLoading";
    import Dropdown from 'primevue/dropdown';
    import Calendar from 'primevue/calendar';
    import SelectTabelaPrevidencia from "../Components/SelectTabelaPrevidencia";
    import DialogSelecao from "../Components/DialogSelecao";
    import MultiselectLotacao from "../Components/MultiselectLotacao";
    import  * as FileSaver from 'file-saver';

    // import DialogLotacao from "../Components/DialogLotacao";

    /**
     * Utils
     */
    const loading = ref(false);
    const codigoPagamento = ref(2402);
    const lotacoes = ref()
    const previdencia = ref()
    const mes = ref(null);
    const ano = ref(null);
    const apiurl = 'v4/api/recursos-humanos/pessoal/relatorios/tipoguiaprevidencia';

    const opcaoFiltro = ref([
        {descricao:'Seleção', valor:'selecao'},
        {descricao:'Lotação', valor:'lotacao'},
    ]);

    const filtroSelecionado = ref();
    const arquivoSelecionado = ref();
    const tipoSelecionado = ref();
    const multiselect_lotacao = ref([]);
    const select_previdencia = ref();
    const dialog_selecao = ref();

    const opcaoArquivo = ref([
        { descricao: 'Salário', valor: 'salario' },
        { descricao: '13º Salário', valor: 'decimo' },
    ]);
    const opcaoGuia = ref([
        { descricao: 'Patronal (Empregador)', valor: 'patronal' },
        { descricao: 'Laboral (Segurado)', valor: 'segurado' },
    ]);
    const dataVencimento = ref();

    const exibeFiltro = () => {
        let trLotacao = document.getElementById("trLotacao");
        let trSelecao = document.getElementById("trSelecao");
        trLotacao.style.display = "none";
        trSelecao.style.display = "none";
        switch (filtroSelecionado.value) {
            case "selecao":
                trSelecao.style.display = "contents";
                break;
            case "lotacao":
                trLotacao.style.display = "contents";
                break;
            default:
                break;
        }
    }
    const toast = useToast();

    /**
     * Hooksl
    */
    onMounted(() => {
        filtroSelecionado.value = "selecao";
        tipoSelecionado.value = "patronal";
        arquivoSelecionado.value = "salario";
    });

    const changePrevidencia = () => {
        for(let prev in select_previdencia){
            previdencia.value = select_previdencia[prev].codigo
        }
    }

    const gerar = async () => {
        let dados = {};
        dados.lotacoes = [];
        dados.tabelas = [];
        dados.descricao = []
        dados.previdencia = []

        if (mes.value === "" || mes.value === null) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Mês da competência não informado.', life: 5000 });
            return false;
        }
        dados.mes = mes.value;

        if (ano.value === "" || ano.value === null) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Ano da competência não informado.', life: 5000 });
            return false;
        }
        dados.ano = ano.value;

        if (arquivoSelecionado.value === "" || arquivoSelecionado.value === null) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Arquivo não selecionado.', life: 5000 });
            return false;
        }
        dados.arquivo = arquivoSelecionado.value;

        if (codigoPagamento.value === "" || codigoPagamento.value === null) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Código de Pagamento não informado.', life: 5000 });
            return false;
        }
        dados.codigoPagamento = codigoPagamento.value;
        
        if (dataVencimento.value === undefined || dataVencimento.value === "" || dataVencimento.value === null) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Data de Vencimento não informada.', life: 5000 });
            return false;
        }
        dados.dataVencimento = dataVencimento.value.toISOString().split('T')[0];

        if (tipoSelecionado.value === "" || tipoSelecionado.value === null) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Tipo de Guia não selecionado.', life: 5000 });
            return false;
        }
        dados.tipoGuia = tipoSelecionado.value;

        switch (filtroSelecionado.value) {
            case "selecao":
                let codigoSelecao = dialog_selecao._value.codigoSelecao;
                if (codigoSelecao === "") {
                    toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Nenhuma seleção selecionada.', life: 5000 });
                    return false;
                }
                dados.selecao = codigoSelecao;
                break;
            case "lotacao":
                let temLotacao = false;
             
                multiselect_lotacao.value.lotacoes.data.forEach(element => {
                lotacoes.value = multiselect_lotacao
        
                    if (element.selecionado == true) {
                        temLotacao = true;
                        dados.lotacoes.push(element.codigo);
                    }

                if (temLotacao === false) {
                    toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Nenhuma lotação selecionada.', life: 5000 });
                    return false;
                }
                });

                break;
            default:
                toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Nenhum tipo de filtro selecionado.', life: 5000 });
                return false;
                break;
        }
    

       if (select_previdencia.value.codigo === '') {
        toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Nenhuma tabela de previdência selecionada.', life: 5000 });
            return false;
       }

       dados.tabelas = previdencia.value

        const action = apiurl + '/emitir';
        loading.value = true;

        try {
            const response = await axios.post(action, dados).then((result) => {
                FileSaver.saveAs(result.data.data.pathExterno, result.data.data.path)
            }).catch((err) => {
                console.log(err)
            });
           
            
        } catch (error) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível emitir a guia.', life: 5000 });
        }

        loading.value = false;

    }
</script>

<template>
    <ModalLoading :is-loading="loading"/>
    <Toast />
    <section class="container">
        <div class="container mt-5">
            <div style="display: block;">
                <Panel header="Tipos de Guia Previdência (GPS)">
                    <table width="100%">
                        <tr>
                            <td width="20%">
                                <label class="font-bold">Competência: </label>
                            </td>
                            <td>
                                <InputNumber
                                    placeholder="Mês"
                                    v-model="mes"
                                    :max="12"
                                    :min="1"
                                    inputStyle="width: 50px;"
                                    :useGrouping="false"
                                />
                                /
                                <InputNumber
                                    placeholder="Ano"
                                    v-model="ano"
                                    inputStyle="width: 70px;"
                                    :max="2030"
                                    :min="1900"
                                    :useGrouping="false"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="font-bold">Arquivo: </label>
                            </td>
                            <td>
                                <Dropdown 
                                    v-model="arquivoSelecionado"
                                    :options="opcaoArquivo"
                                    optionLabel="descricao"
                                    optionValue="valor"                                   
                                    class="w-full md:w-14rem"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="font-bold">Código de Pagamento: </label>
                            </td>
                            <td>
                                <InputNumber
                                    placeholder="Cód. Pagamento"
                                    v-model="codigoPagamento"
                                    inputStyle="width: 130px;"
                                    :useGrouping="false"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="font-bold">Data de Vencimento: </label>
                            </td>
                            <td>
                                <Calendar v-model="dataVencimento" placeholder="Data" dateFormat="dd/mm/yy"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="font-bold">Tipo de Guia: </label>
                            </td>
                            <td>
                                <Dropdown 
                                    v-model="tipoSelecionado"
                                    :options="opcaoGuia"
                                    optionLabel="descricao"
                                    optionValue="valor"                                   
                                    placeholder="Selecione o tipo de Guia"
                                    class="w-full md:w-14rem"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="font-bold">Tabelas da Previdência:</label>
                            </td>
                            <td>
                                <SelectTabelaPrevidencia v-model="select_previdencia" @change="changePrevidencia" ref="multiselect_previdencia"/>
                            </td>
                        </tr>
                        <tr>
                                <td>
                                    <label class="font-bold">Tipo de Filtro: </label>
                                </td>
                                <td>
                                    <Dropdown 
                                        v-model="filtroSelecionado"
                                        :options="opcaoFiltro"
                                        optionLabel="descricao"
                                        optionValue="valor"                                   
                                        placeholder="Selecione o tipo de Filtro"
                                        class="w-full md:w-14rem"
                                        :onChange="exibeFiltro"
                                    />
                                </td>
                        </tr>
                        <DialogSelecao ref="dialog_selecao"/>
                        <tr id="trLotacao">
                            <td>
                                <label class="font-bold">Lotação: </label>
                            </td>
                            <td>
                                <MultiselectLotacao v-model="multiselect_lotacao" ref="multiselect_lotacao"/>
                            </td>
                        </tr>
                    </table>
                </Panel>
            </div>
            <div>
                <Button label="Imprimir" class="mt-4" @click="gerar"/>
            </div>
        </div>
    </section>
</template>

<style>
    #trLotacao {
        display: none;
    }
</style>
