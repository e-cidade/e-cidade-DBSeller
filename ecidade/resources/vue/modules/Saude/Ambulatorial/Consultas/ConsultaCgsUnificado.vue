<script setup>

import { ref,computed,watch,onMounted} from "vue";
import DialogPesquisaCgsUnificado from '../Components/DialogPesquisaCgsUnificado.vue';
import {formateDate} from "../../../../utils/Strings";
import ModalLoading from "../../../Components/ModalLoading";

const codigo_unificacao = ref();
const cgs_correto = ref();
const data_unificacao = ref();
const nome_correto = ref();
const hora_unificacao = ref();
const login_unificacao = ref();
const login_processamento = ref();
const hora_processamento = ref();
const data_processamento = ref();

const listaCartoesSus = ref([]);
const listaCgsErrados = ref([]);
const loadingPrincipal = ref(false);
const codigoUnificacao = ref();

const routes = {
    listarCgs:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/listar-cgs`,
    gravar:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/salvar`,
    detalhar:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/detalhar`,
    excluir:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/excluir`,
    processar:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/processar`
};

const visibleDialog = ref(false);

const modoConsulta = ref(false);

onMounted( () => {
   
    consultarUnificacoes();
   
});

const handleRowSelected = (rowSelected) => {   
    loadingPrincipal.value= true; 
    codigoUnificacao.value = rowSelected.codigo;
    detalharUnificacao(rowSelected.codigo);
};

const detalharUnificacao = async(codigo) => {
    try{                    
        let parametros = {};        
        parametros.codigo_unificacao = codigo;   
        parametros.unificado = true; 
        let response = (await window.axios.post(routes.detalhar,parametros)).data;
        if(!response.erro){
            loadingPrincipal.value= false;               
            processarDetalhesUnificacao(response.data);            
        }
    } catch(e){
        loadingPrincipal.value= false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    } 
}

const processarDetalhesUnificacao = (data) =>{
    let detalhesUnificacao = data.detalhesUnificacao;
    
    if(detalhesUnificacao.length > 0){
        codigo_unificacao.value = detalhesUnificacao[0].codigo_unificacao;
        nome_correto.value = detalhesUnificacao[0].nome_correto;
        cgs_correto.value = detalhesUnificacao[0].cgs_correto;
        data_unificacao.value = formateDate(detalhesUnificacao[0].data_unificacao);
        hora_unificacao.value = detalhesUnificacao[0].hora_unificacao;
        login_unificacao.value = detalhesUnificacao[0].login_unificacao;
        data_processamento.value = formateDate(detalhesUnificacao[0].data_processamento);
        hora_processamento.value = detalhesUnificacao[0].hora_processamento;
        login_processamento.value = detalhesUnificacao[0].login_processamento;
    }

    let errados = detalhesUnificacao.map(item => ({
        codigo: item.codigo_unificacao,
        cgs: item.cgs_errado,
        nome: item.nome_errado        
    }));

    listaCgsErrados.value = errados;
    listaCartoesSus.value = data.cartoesUnificacao;
}

const consultarUnificacoes = () => {
    estadoInicial();
    visibleDialog.value = true;
}

const estadoInicial = () => {
    codigo_unificacao.value = "";
    cgs_correto.value = "";
    data_unificacao.value = "";
    nome_correto.value = "";
    hora_unificacao.value = "";
    login_unificacao.value = "";
    listaCgsErrados.value = [];
}

</script>

<template>
    <ModalLoading :isLoading="loadingPrincipal"/>
    <section class = "container">       
        <Panel header="CGS Correto"> 
            <div class="grid">
                <div class="flex flex-column col-2">
                    <label for="codigo"><b>Código Unificação</b></label>
                    <InputText disabled type="text" v-model="codigo_unificacao" variant="filled" class="custom-input" />
                </div>
                <div class="flex flex-column col-2">
                    <label for="cgs"><b>CGS</b></label>
                    <InputText disabled type="text" v-model="cgs_correto" variant="filled" class="custom-input"/>                    
                </div>
                <div class="flex flex-column col-8">
                    <label for="cgs"><b>Nome</b></label>
                    <InputText disabled type="text" v-model="nome_correto" variant="filled" class="custom-input"/>                    
                </div>
            </div>
            <div class="grid">

                <div class="flex flex-column col-3">
                    <label for="data"><b>Data Unificação</b></label>
                    <InputText disabled type="text" v-model="data_unificacao" variant="filled" class="custom-input"/>
                </div>

                <div class="flex flex-column col-2">
                    <label for="hora"><b>Hora Unificação</b></label>
                    <InputText disabled type="text" v-model="hora_unificacao" variant="filled" class="custom-input"/>
                </div>

                <div class="flex flex-column col-3">
                    <label for="login"><b>Login Unificação</b></label>
                    <InputText disabled type="text" v-model="login_unificacao" variant="filled" class="custom-input"/>
                </div>                
            </div> 
            <div class="grid">
                <div class="flex flex-column col-3">
                    <label for="data"><b>Data Processamento</b></label>
                    <InputText disabled type="text" v-model="data_processamento" variant="filled" class="custom-input"/>
                </div>

                <div class="flex flex-column col-2">
                    <label for="hora"><b>Hora Processamento</b></label>
                    <InputText disabled type="text" v-model="hora_processamento" variant="filled" class="custom-input"/>
                </div>

                <div class="flex flex-column col-3">
                    <label for="login"><b>Login Processamento</b></label>
                    <InputText disabled type="text" v-model="login_processamento" variant="filled" class="custom-input"/>
                </div>
            </div>
            
            <div class="grid mt-2">
                <div class = "col-12">
                    <Fieldset legend="Cartões do SUS Vinculados" >
                        <DataTable  :value="listaCartoesSus" >
                            <Column field="cgs" header="CGS"></Column>
                            <Column field="numero" header="Número"></Column>
                            <Column field="tipo" header="Tipo"></Column>                                                   
                        </DataTable>   
                    </Fieldset>
                </div>
            </div>
        </Panel>   
    </section>    
    
    <section class = "container">       
        <Panel header="CGSs Errados"> 
            <DataTable  :value="listaCgsErrados"  scrollHeight="300px">
                <Column field="codigo" header="Código"></Column>
                <Column field="cgs" header="CGS"></Column>
                <Column field="nome" header="Nome"></Column>                                                   
            </DataTable>                  
        </Panel>   
    </section>

    <section class = "container">        
        <div class= "flex justify-content-center align-items-center">
            <Button 
                type="button" 
                label="Consultar Unificações" 
                icon="pi pi-search"
                severity="info"
                @click="consultarUnificacoes()"
                />
        </div>        
    </section>     

    <section>
        <DialogPesquisaCgsUnificado @update:modelValue="handleRowSelected" v-model:visible="visibleDialog"/>
    </section>
</template>

<style scoped>
.custom-input:disabled {
  color: #000; 
  background-color: #f5f5f5; 
  opacity: 1; 
}
</style>