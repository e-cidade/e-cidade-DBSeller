<script setup>

import { ref,computed,watch,onMounted} from "vue";
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import Calendar from 'primevue/calendar';
import Checkbox from 'primevue/checkbox';
import DialogPesquisaUnificacoesCgs from '../Components/DialogPesquisaUnificacoesCgs.vue';
import ModalLoading from "../../../Components/ModalLoading";
import {formateDate} from "../../../../utils/Strings";

const confirm = useConfirm();
const dataNascimento = ref();
const listaCgs = ref([]);
const nomeMae = ref();
const nome = ref();
const cgs = ref();
const selectedCorretos = ref([]);
const selectedErrados = ref([]);
const mostrarBotaoGravar = ref(true);
const mostrarBotaoExcluir = ref(false);
const mostrarBotaoProcessar = ref(false);
const mostrarBotaoLimparCgs = ref(true);
const mostrarAcoes = ref(true);
const loadingPrincipal = ref(false);
const codigoUnificacao = ref();
const pesquisaUnicaCgs = ref(false);

const toast = useToast();
const routes = {
    listarCgs:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/listar-cgs`,
    gravar:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/salvar`,
    detalhar:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/detalhar`,
    excluir:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/excluir`,
    processar:`v4/api/saude/ambulatorial/procedimento/unificacao-cgs/processar`
};

const loadingPesquisarCgs = ref(false);
const loadingBotaoGravar = ref(false);
const visibleDialog = ref(false);
const isDialogGravarVisible = ref(false);
const dialogGravarMensagem = ref();

onMounted( () => {
   estadoInicial();   
});

const handleRowSelected = (rowSelected) => {   
    loadingPrincipal.value= true; 
    codigoUnificacao.value = rowSelected.codigo;
    detalharUnificacao(rowSelected.codigo);
};

const detalharUnificacao = async(codigo_unificacao) => {
    try{                    
        let parametros = {};        
        parametros.codigo_unificacao = codigo_unificacao;    
        let response = (await window.axios.post(routes.detalhar,parametros)).data;
        if(!response.erro){
            loadingPrincipal.value= false;   
            listaCgs.value = [];            
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
    
    estadoConsultaUnificacao();
    
    let detalhesUnificacao = data.detalhesUnificacao;

    let correto = {
        cgs: detalhesUnificacao[0].cgs_correto,
        nome: detalhesUnificacao[0].nome_correto,
        cpf: detalhesUnificacao[0].cpf_correto,
        cns: detalhesUnificacao[0].cns_correto,
        data_nascimento: detalhesUnificacao[0].data_nascimento_correto,
        nome_mae: detalhesUnificacao[0].nome_mae_correto,
        situacao: detalhesUnificacao[0].situacao_correto,
        endereco: detalhesUnificacao[0].endereco_correto,
        municipio: detalhesUnificacao[0].municipio_correto
    };

    selectedCorretos.value = correto;
    selectedErrados.value = detalhesUnificacao.map(obj => obj.cgs_errado);
    
    let errados = detalhesUnificacao.map(item => ({
        cgs: item.cgs_errado,
        nome: item.nome_errado,
        cpf: item.cpf_errado,
        cns: item.cns_errado,
        data_nascimento: item.data_nascimento_errado,
        nome_mae: item.nome_mae_errado,
        situacao: item.situacao_errado,
        endereco: item.endereco_errado,
        municipio: item.municipio_errado
    }));
        
    listaCgs.value = [correto, ...errados];
}

const gravar = async() => {

    try{        
        
        let parametros = {};
        loadingBotaoGravar.value = true;
        parametros.cgs_correto = selectedCorretos.value['cgs'];
        parametros.unificado = false;
        let cgsErrados = listaCgs.value.filter(item => selectedErrados.value.includes(item.cgs) && selectedCorretos.value['cgs'] != item.cgs);
        let errados = [];
        for(let i = 0;i<cgsErrados.length;i++){
            errados.push({
                'cgs_errado':cgsErrados[i]['cgs'],
                'nome':cgsErrados[i]['nome']
            })
        }
        parametros.errados = errados;
        let response = (await window.axios.post(routes.gravar,parametros)).data;        
        if(!response.erro){   
            listaCgs.value = []; 
            loadingBotaoGravar.value = false;        
            
            dialogGravarMensagem.value = response.message;
            isDialogGravarVisible.value = true;            
        }

    } catch(e){
        loadingBotaoGravar.value = false;  
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }     
}

const listarCgs = async() => {
    try{
        loadingPesquisarCgs.value = true;
        let parametros = {};

        /**
         * Caso haja algum registro provindo da consulta
         * de unificação, limpa as unificações, para acionar
         * o filtro de pesquisa
         */        
        if('cgs' in selectedCorretos.value){
            listaCgs.value = [];
            selectedCorretos.value = [];
        }

        /**
         * Verifica se a funcionalidade de pesquisa unica 
         * por cgs deve estar ativa. Quando estiver ativa,
         * o usuario pode ir selecionando os cgs, que esses
         * vão se somando na lista dos CGSs retornados
         */
        if((cgs.value != null && cgs.value != '') && 
           (dataNascimento.value == null || dataNascimento.value == '') && 
           (nomeMae.value == null || nomeMae.value == '')  && 
           (nome.value == null || nome.value == '')
        ) {
            pesquisaUnicaCgs.value = true;
        } else {
            pesquisaUnicaCgs.value = false;
        }

        if(cgs.value != null && cgs.value.length > 0){
            parametros.cgs = cgs.value;
        }

        if(dataNascimento.value != null){
            parametros.dataNascimento = dataNascimento.value;
        }
        
        if(nomeMae.value != null && nomeMae.value.length > 0){
            parametros.nomeMae = nomeMae.value;
        }

        if(nome.value != null && nome.value.length > 0){
            parametros.nome = nome.value;
        }

        
        atualizarListaCgs((await window.axios.post(routes.listarCgs,parametros)).data.data);
    
        mostrarBotaoExcluir.value = false;
        mostrarBotaoProcessar.value = false;
        mostrarBotaoGravar.value = true;     
        loadingPesquisarCgs.value = false;   

    } catch(e){
        loadingPesquisarCgs.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }    
}

const atualizarListaCgs = (novosCgs) => {
    
    if(listaCgs.value.length > 0 && pesquisaUnicaCgs.value){
        const cgsAdicionar = novosCgs.filter(objVerificar => 
            
            !listaCgs.value.some(objCgs => 
                objCgs.cgs === objVerificar.cgs
            )
        );       
        listaCgs.value.push(...cgsAdicionar);
    } else {
        listaCgs.value = novosCgs;
    }
}

const limparPesquisa = () => {
    dataNascimento.value = null;
    nomeMae.value = null;
    nome.value = null;
    cgs.value = null;
}

const excluirLinhaCgs = (index) => {    
    listaCgs.value.splice(index,1);
}

const consultarUnificacoes = () => {
    visibleDialog.value = true;
}

const confirmarExclusao = (event) => {

    confirm.require({
        target:event.currentTarget,
        message: 'Você deseja realmente desfazer a unificação?',        
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Sim',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            excluir();
        }
    });    
}

const excluir = async() => {
    
    try{
        loadingPrincipal.value = true;
        let parametros = {};
        parametros.codigo_unificacao = codigoUnificacao.value;

        let response = (await window.axios.post(routes.excluir,parametros)).data;
        if(!response.error){
            
            estadoInicial();

            toast.add({ 
                severity: 'success',
                summary: 'Concluído!', 
                detail: response.message, 
                life: 3000 
            });
        }
             
    } catch(e){
        loadingPrincipal.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    } 
}

const confirmarProcessamento = (event) =>{
    confirm.require({
        target:event.currentTarget,
        message: [
        'Você tem certeza de que quer processar essa unificação de CGSs?',
        'Se tiver qualquer dúvida, não faça isso.',
        'Essa ação não pode ser desfeita!'
        ].join(' '),
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim',   
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',             
        rejectClass: 'p-button-secondary p-button-outlined',        
        accept: () => {
            processar();
        }
    });   
}

const processar = async() => {
    try{
        loadingPrincipal.value = true;
        let parametros = {};
        parametros.codigo_unificacao = codigoUnificacao.value;
        parametros.cgs_correto = selectedCorretos.value['cgs'];

        let response = (await window.axios.post(routes.processar,parametros)).data;
        if(!response.error){
            estadoInicial();
            toast.add({ 
                severity: 'success',
                summary: 'Concluído!', 
                detail: response.message, 
                life: 3000 
            });
        }
             
    } catch(e){
        loadingPrincipal.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }     
}

const estadoInicial = () => {

    selectedErrados.value = [];
    selectedCorretos.value = [];
    mostrarAcoes.value = true;
    listaCgs.value = [];
    loadingPrincipal.value = false;
    mostrarBotaoProcessar.value = false;
    mostrarBotaoExcluir.value = false;
    mostrarBotaoGravar.value = true;
    mostrarBotaoLimparCgs.value = true;
    pesquisaUnicaCgs.value = false;
    
}

const estadoConsultaUnificacao = () => {
    
    mostrarAcoes.value = false;
    mostrarBotaoProcessar.value = true;
    mostrarBotaoExcluir.value = true;
    mostrarBotaoGravar.value = false;        
}

const validateInputNomeMae = (event) => {
    const regex = /[^a-zA-ZÀ-ÖØ-öø-ÿ ]/g; 
    event.target.value = event.target.value.replace(regex, '');
    nomeMae.value = event.target.value; 
}

const validateInputNome = (event) => {
    const regex = /[^a-zA-ZÀ-ÖØ-öø-ÿ ]/g; 
    event.target.value = event.target.value.replace(regex, '');
    nome.value = event.target.value; 
}
const validateInputCgs = (event) => {
    const regex = /[^0-9]/g;
    event.target.value = event.target.value.replace(regex, '');
    cgs.value = event.target.value; 
}

</script>

<template>
    <ModalLoading :isLoading="loadingPrincipal"/> 
    <ConfirmDialog/>
    <Toast />
    <section class = "container w-8">
        <Panel header="Filtros">                                        
            <div class = "p-fluid grid">
               <div class="field col-3">            
                   <label for="cgs" class="font-bold ">CGS</label>
                   <InputText  v-model = "cgs" inputId="cgs"  variant="filled" @input="validateInputCgs($event)" />
               </div>
               <div class="field col-3">                
                  <label for="dataNascimento" class="font-bold "> Data de Nascimento</label>                
                  <Calendar  v-model="dataNascimento" v-mask="'##/##/####'" showIcon :showOnFocus="false" inputId="dataNascimento" />
               </div> 
               <div class="field col-6">
                  <label for="nome_mae" class="font-bold "> Nome da Mãe </label>
                  <InputText v-model = "nomeMae" inputId="nome_mae"  variant="filled" @input="validateInputNomeMae($event)" />
               </div>                                           
            </div>  

            <div class="field">
                <label for="nome" class="font-bold block ">Nome</label>
                <InputText v-model = "nome" class="w-6" inputId="nome"  variant="filled" @input="validateInputNome($event)" />
            </div>                                    
        </Panel>
    </section>

    <section  class = "container">        
        <div class= "flex justify-content-center align-items-center">
            <Button type="button" label="Pesquisar" icon="pi pi-search"  :loading="loadingPesquisarCgs" @click="listarCgs()"/>
            <Button type="button" label="Limpar Pesquisa" icon="pi pi-delete-left" severity ="warning" @click="limparPesquisa()"/>
        </div>        
    </section>

    <section class ="container">
        <Dialog header="Unificação do CGS salva com sucesso!" :style="{ width: '50rem' }" v-model:visible="isDialogGravarVisible" modal>
            <template #header>
            <div class="inline-flex align-items-center justify-content-center gap-2">
                <span class="pi pi-check-circle"></span>
                <label>Unificação do CGS salva com sucesso!</label>
                <span class="pi pi-check-circle"></span>
            </div>
            </template>            
            <div class="flex align-items-center mt-3">
                <label>
                    {{dialogGravarMensagem}}
                </label>                
            </div>            
            <div class = "flex justify-content-end ">
               <Button label="OK" @click="isDialogGravarVisible = false" />
            </div>
        </Dialog>        

    </section>

    <div class="flex flex-wrap align-items-center justify-content-center">
        <section class = "flex flex-column w-11 h-auto">       
            <Panel header="CGSs Retornados"> 
                <DataTable v-model:selection="selectedCorretos" :value="listaCgs"  scrollHeight="300px">
                    <Column selectionMode="single" header="Correto"></Column>
                    <Column field="errado" header="Errado">
                        <template  #body="slotProps">
                           <Checkbox  v-model="selectedErrados" :value="slotProps.data.cgs"></Checkbox>
                        </template>                    
                    </Column>
                    <Column field="cgs" header="CGS"></Column>
                    <Column field="nome" header="Nome"></Column>
                    <Column field="cpf" header="CPF"></Column>                
                    <Column field="data_nascimento" header="Data Nascimento">
                        <template #body="slotProps">
                            {{ slotProps.data.data_nascimento ? formateDate(slotProps.data.data_nascimento) : ''}}
                        </template>
                    </Column>                                       
                    <Column field="nome_mae" header="Nome da Mãe"></Column>
                    <Column field="endereco" header="Endereço"></Column>
                    <Column field="municipio" header="Município"></Column>
                    <Column v-if="mostrarAcoes" field="acoes" header="Ações">
                        <template #body="slotProps">
                            <Button   
                                v-show="mostrarAcoes"                      
                                v-tooltip.top="{
                                    value: 'Excluir',
                                    pt: {
                                        arrow: {
                                            style: {
                                                borderTopColor: 'var(--primary-color)'
                                            }
                                        },
                                        text: 'bg-primary font-medium'
                                    }
                                }"  
                                severity="danger" 
                                icon = "pi pi-trash round raised" 
                                @click = "excluirLinhaCgs(slotProps.index)"
                                /> 
                        </template>
                    </Column>
                </DataTable>                  
            </Panel>   
        </section>
    </div>

    <section class = "container">        
       <label><b style="color:green">CGS Correto:</b> Este cadastro não será alterado, apenas serão adicionadas 
        as informações do CGS errado.
       </label>
       <br>
       <label><b style="color: red">CGS Errado:</b> Todas as informações desse cadastro serão redirecionadas 
        para o CGS correto.
       </label>
    </section> 

    <section class = "container">        
        <div class= "flex justify-content-center align-items-center">
            <Button v-show="mostrarBotaoGravar" type="button" label="Gravar" :loading="loadingBotaoGravar" icon="pi pi-save" @click="gravar()"/>
            <Button v-show="mostrarBotaoExcluir" type="button" label="Excluir Gravação" icon="pi pi-trash" severity ="danger" @click="confirmarExclusao($event)"/>
            <Button v-show="mostrarBotaoLimparCgs" type="button" 
               label="Limpar CGSs" 
               icon="pi pi-delete-left" 
               severity ="warning"
               @click = "estadoInicial()"
               />
            <Button 
                type="button" 
                label="Consultar Unificações Pendentes" 
                icon="pi pi-search"
                severity="info"
                @click="consultarUnificacoes()"
                />
        </div>        
    </section> 
    <section class = "container">        
        <div class= "flex justify-content-center align-items-center">
            <Button v-show="mostrarBotaoProcessar" type="button" label="Processar Unificação" icon="pi pi-save" @click="confirmarProcessamento($event)"/>
        </div>        
    </section>      
    <section>
        <DialogPesquisaUnificacoesCgs @update:modelValue="handleRowSelected" v-model:visible="visibleDialog"/>
    </section>
</template>

<style scoped>
</style>