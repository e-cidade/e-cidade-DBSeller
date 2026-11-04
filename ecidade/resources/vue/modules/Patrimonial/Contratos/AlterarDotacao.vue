<template>
    <div class="container">
        <fieldset>
            <legend>Alterar dotação do acordo</legend>
            <div class="container">
                <PesquisaAcordo  @alteraValor="recebeNumeroAcordo" style="display:  inline"/>
                <InputNumber class="p-inputtext-sm" v-model="numeroAcordo" inputId="withoutgrouping" :useGrouping="false" />
                <Button size="small" @click="buscarPosicoes" :disabled="botaoDesabilitado" label="Pesquisar Posições"
                        style="margin-left: 1em"
                        :loading="loading"/>
            </div>
            <div style="margin:0 0 1em 1.7em">
                <EventoAutomatico :limparCampos="limparCamposEventoAutomatico" @eventoAutomaticoAlterado="recebeEventoAutomatico"/>
            </div>
            <div>
                <fieldset>
                    <div>
                        <fieldset>
                            <legend>Posições do acordo</legend>
                            <PosicoesAcordoTable :dadosTabela="dadosTabelaPosicoes" @linhaSelecionada="buscarItens"/>

                        </fieldset>
                    </div>
                    <div>
                        <fieldset>
                                <legend>Itens</legend>
                                <ItensAcordoTable @selecionarItem="receberItensSelecionados" :dadosTabela="dadosTabelaItens" />
                                <ModalLoading :isLoading="loadingItens" :message="'Carregando itens do acordo.'"/>
                        </fieldset>
                        <div style="text-align:center">
                            <ModalLoading :isLoading="enviandoAlteracoes" :message="'Salvando Alterações'"/>

                            <Button size="small"
                                    label="Salvar Alterações"
                                    style="margin:2em"
                                    :disabled="botaoProcessarDesabilitado"
                                    @click="enviarAlteracao"
                            />
                        </div>
                    </div>
                </fieldset>
            </div>
        </fieldset>
    </div>
</template>

<script>
import PesquisaAcordo from './Components/PesquisaAcordo';
import PosicoesAcordoTable from './Components/PosicoesAcordoTable';
import ItensAcordoTable from './Components/ItensAcordoTable'
import EventoAutomatico  from './Components/EventoAutomatico'
import ModalLoading from '../../Components/ModalLoading'
import { useToast } from "primevue/usetoast";

import Button from 'primevue/button';
import {ref, computed,watch} from 'vue';


export default {
    components:{
        PesquisaAcordo,
        PosicoesAcordoTable,
        ItensAcordoTable,
        EventoAutomatico,
        Button,
        ModalLoading,
    },
    setup () {
        const toast = useToast();
        const limparCamposEventoAutomatico = ref(false);
        const numeroAcordo = ref(null);
        const botaoDesabilitado = computed( () => numeroAcordo.value == null || numeroAcordo.value == '')
        const loading = ref(false);
        const loadingItens = ref(false);
        const enviandoAlteracoes = ref(false);
        const eventoAutomatico = ref(null);
        const acordoPosicao = ref(null);
        const dadosTabelaPosicoes = ref([]);
        const dadosTabelaItens = ref([]);
        const itensSelecionados = ref([]);
        const botaoProcessarDesabilitado = computed ( () =>
            itensSelecionados.value.length == 0
        )
        function recebeNumeroAcordo (acordo) {
            numeroAcordo.value = acordo;
        }

        async function buscarPosicoes ()
        {
            dadosTabelaPosicoes.value = [];
            dadosTabelaItens.value = [];
            loading.value = true;
            const dados = await window.axios.get(`v4/api/patrimonial/contratos/consulta/buscar-acordo-posicoes/` + numeroAcordo.value);
            dados.data.data.forEach(obj => {
                if (obj.ac26_emergencial) {
                    obj.ac26_emergencial = "Sim"
                } else {
                    obj.ac26_emergencial = "Não"
                }
                obj.acordotipo = obj.ac27_sequencial + " - " + obj.ac27_descricao;
                obj.ac26_data = dataFormatter(obj.ac26_data);
            });
            dadosTabelaPosicoes.value = dados.data.data;
            loading.value = false;
            limparCamposEventoAutomatico.value = false;

        }

        async function buscarItens(event) {
            dadosTabelaItens.value = [];
            acordoPosicao.value = event;
            loadingItens.value = true;
            const dados = await window.axios.get(`v4/api/patrimonial/contratos/consulta/buscar-itens-posicao/` + event.ac26_sequencial
            )
            dadosTabelaItens.value = dados.data.data;
            loadingItens.value = false;
        }

        function dataFormatter (data) {
            const [ano, mes, dia] = data.split('-')
            return `${dia}/${mes}/${ano}`;
        }

        function receberItensSelecionados (itens) {
            itensSelecionados.value = itens.value;
        }

        async function enviarAlteracao () {
            if (validaEventoAutomatico()) {
                try {
                    enviandoAlteracoes.value = true;

                    await window.axios.post(`v4/api/patrimonial/contratos/realizar-alteracoes/`, itensSelecionados.value)
                    if (eventoAutomatico.value.checked) {
                        acordoPosicao.value.dtVigenciaInicial = dataFormatter(acordoPosicao.value.ac16_datainicio);
                        acordoPosicao.value.dtVigenciaFinal = dataFormatter(acordoPosicao.value.ac16_datafim);
                        let data = {
                            evento: eventoAutomatico.value,
                            acordoPosicao: acordoPosicao.value,
                            itens: dadosTabelaItens.value,
                            itensSelecionados: itensSelecionados.value
                        }
                        await window.axios.post(`v4/api/patrimonial/contratos/enviar-evento-automatico/`, data);
                    }
                    alert("As alterações foram realizadas com sucesso")
                    location.reload();


                } catch (e) {
                    toast.add({ severity: 'error', summary: 'Erro durante alterações',
                        detail: 'Ocorreu um erro a realizar as alteraçoes', life: 5000 });

                }
                enviandoAlteracoes.value = false;
            }
        }

        function validaEventoAutomatico() {
            if(!eventoAutomatico.value.checked) {
                return true;
            }else {
                if (eventoAutomatico.value.numTermo == null || eventoAutomatico.value.numTermo == ''){
                    toast.add({ severity: 'error', summary: 'Número do termo vazio',
                        detail: 'Caso deseje inserir evento automático, o número do termo deve ser preenchido', life: 5000 });
                    return false;
                }
                return true;
            }
        }

        function recebeEventoAutomatico(event) {
            eventoAutomatico.value = event;

        }

        function alterarNumAcordo(event) {
            numeroAcordo.value = event.value
        }

        watch(numeroAcordo,() => {
            limparCampos();

        })

        function limparCampos() {
            limparCamposEventoAutomatico.value = true;
            itensSelecionados.value = [];
            dadosTabelaItens.value = [];
            dadosTabelaPosicoes.value = [];
        }

        return {
            numeroAcordo,
            recebeNumeroAcordo,
            buscarPosicoes,
            loading,
            loadingItens,
            botaoDesabilitado,
            dadosTabelaPosicoes,
            dadosTabelaItens,
            buscarItens,
            botaoProcessarDesabilitado,
            receberItensSelecionados,
            enviarAlteracao,
            eventoAutomatico,
            validaEventoAutomatico,
            recebeEventoAutomatico,
            enviandoAlteracoes,
            alterarNumAcordo,
            limparCamposEventoAutomatico
        }
    }

};
</script>
