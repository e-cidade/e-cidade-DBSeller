<script setup>
import { onMounted, ref} from "vue";
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from 'primevue/useconfirm';
import {FilterMatchMode,FilterOperator} from 'primevue/api';
import ModalLoading from "../../../Components/ModalLoading.vue";

const toast = useToast();
const confirm = useConfirm();
const loadingPrincipal = ref(false);
const loadingButtonSalvar = ref(false);
const loadingButtonExcluir = ref(false);
const loadingButtonCancelar = ref(false);
const buttonSalvarVisible = ref(true);
const buttonExcluirVisible = ref(false);
const buttonCancelarVisible = ref(false);
const expandedRows = ref([]);

const form = ref({
    codigo:{
        value:null,
        disabled:true,
        label:"Código"
    },
    descricao:{
        value:null,
        disabled:false,
        label: "Descrição"
    },
    ativo:{
        value:[true],
        disabled:false,
        label: "Ativo"
    }
});

const unidades = ref();
const filtrosTabela = ref({
    'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
    'descricao': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
})

const routes = {
    pesquisarUnidades:`v4/api/saude/ambulatorial/consulta/unidades-origem`,
    salvarUnidade:`v4/api/saude/ambulatorial/cadastro/unidades-origem/save`,
    excluirUnidade:`v4/api/saude/ambulatorial/cadastro/unidades-origem/delete`
};

const pesquisaUnidades = async () => {    
    try{
        loadingPrincipal.value = true
        unidades.value = (await window.axios.post(routes.pesquisarUnidades)).data.data
        loadingPrincipal.value = false
        
    } catch(e){
        loadingPrincipal.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const editarEvent = (data) => {  
    buttonExcluirVisible.value = false;
    buttonCancelarVisible.value = true;
    buttonSalvarVisible.value = true;
         
    form.value.ativo.disabled = false;
    form.value.descricao.disabled = false;     
    form.value.descricao.value = data.descricao;
    form.value.ativo.value = data.ativo ? [data.ativo] : [];
    form.value.codigo.value = data.codigo;

    scrollToTop();
}

const excluirEvent = (data) => {
    buttonSalvarVisible.value = false;
    buttonExcluirVisible.value = true;
    buttonCancelarVisible.value = true;

    form.value.ativo.disabled = true;
    form.value.descricao.disabled = true;
    form.value.descricao.value = data.descricao;    
    form.value.ativo.value = data.ativo ? [data.ativo] : [];    
    form.value.codigo.value = data.codigo;    

    scrollToTop();
}


const cancelar = () => {
    loadingButtonCancelar.value = true;
    setTimeout(() =>{
        limpaCampos();
    },300);
}

const salvarUnidade = async () => {

    let parametros = {};

    if(form.value.codigo.value != null && form.value.codigo.value > 0){
        parametros.codigo = form.value.codigo.value;
    }

    parametros.descricao = form.value.descricao.value;
    parametros.ativo =  form.value.ativo.value.length > 0;

    try{
        loadingButtonSalvar.value = true;
        let response = (await window.axios.post(routes.salvarUnidade,parametros)).data;
        loadingButtonSalvar.value = false;
        if(!response.erro){            
            toast.add({
               severity: 'success',
               summary: 'Sucesso!',
               detail: response.message,
               life: 5000
            });
            limpaCampos();
            pesquisaUnidades();   
        }
    } catch(e){
        loadingButtonSalvar.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro!',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const excluirUnidadeDialog = (event) => { 

confirm.require({
    target:event.currentTarget,
    message: 'Deseja mesmo excluir a unidade?',
    header: 'Confirmação',
    icon: 'pi pi-exclamation-triangle',
    rejectClass: 'p-button-info p-button-outlined',
    rejectIcon: 'pi pi-times-circle',
    acceptClass: 'p-button-danger',
    acceptIcon:'pi pi-trash', 
    rejectLabel: 'Cancelar',
    acceptLabel: 'Excluir',        
    accept: () => {            
        excluirUnidade();
    }
});    
}

const excluirUnidade = async () => {
    let parametros = {};

    if(form.value.codigo.value != null && form.value.codigo.value > 0){
        parametros.codigo = form.value.codigo.value;
    }

    try{
        loadingButtonExcluir.value = true;
        let response = (await window.axios.post(routes.excluirUnidade,parametros)).data;
        loadingButtonExcluir.value = false;
        if(!response.erro){            
            toast.add({
               severity: 'success',
               summary: 'Sucesso!',
               detail: response.message,
               life: 5000
            });
            limpaCampos();
            pesquisaUnidades();   
        }
    } catch(e){
        loadingButtonExcluir.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro!',
            detail: e.response.data.message,
            life: 5000
        });
    }    
}

const limpaCampos = () =>{
    loadingButtonCancelar.value = false;
    buttonCancelarVisible.value = false;
    buttonExcluirVisible.value = false;
    buttonSalvarVisible.value = true;   
    form.value.ativo.disabled = false;
    form.value.descricao.disabled = false;             
    form.value.codigo.value = "";
    form.value.descricao.value = "";
    form.value.ativo.value= [true];
}

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });    
}

onMounted( () =>{
    pesquisaUnidades();
});

</script>

<template>
    <Toast />
    <ConfirmDialog/>
    <section class = "container w-6">
        <Panel header="Cadastros de Unidades de Origem">    
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2">                    
                    <label for="codigo"><b>{{form.codigo.label}}</b></label>
                    <InputText                         
                        id="codigo"
                        :disabled="form.codigo.disabled"
                        v-model="form.codigo.value"
                    />                                                 
                </div> 
                <div class="field col-12 md:col-10">                                                          
                    <label for="descricao"><b>{{ form.descricao.label }}</b></label>
                    <InputText                        
                        id="descricao"
                        v-model="form.descricao.value"
                        :disabled="form.descricao.disabled"
                    />                               
                </div>                 
            </div>                        
            <div class="field">  
                <div class="card flex justify-left gap-2">
                    <label for="ativo">Ativo</label>  
                    <Checkbox
                        v-model="form.ativo.value"    
                        inputId="ativo"
                        name="ativo"    
                        :value="true"  
                        :disabled="form.ativo.disabled"
                    />                     
                </div>                                               
            </div>                         
        </Panel>
        <br>                    
        <div class ="flex justify-content-center flex-wrap">
            
            <Button class = "m-2" v-show="buttonCancelarVisible" icon="pi pi-times-circle" label="Cancelar"  outlined :loading="loadingButtonCancelar" @click="cancelar()" />
            <Button class = "m-2" v-show = "buttonSalvarVisible" icon="pi pi-save" label="Salvar"  :loading="loadingButtonSalvar"  @click="salvarUnidade($event)" /> 
            <Button class = "m-2" v-show="buttonExcluirVisible"  icon="pi pi-trash" label="Excluir" :loading="loadingButtonExcluir" severity="danger" @click="excluirUnidadeDialog($event)" /> 
        </div>        
    </section>
    <section class="container">
        <DataTable 
            :value="unidades" 
            showGridlines
            :paginator="true"
            :rows="5"
            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink
            LastPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[5,10,15]"
            responsiveLayout="scroll"
            currentPageReportTemplate="Foram retornados {totalRecords} registros. Mostrando de {first} até {last}"
            v-model:expandedRows="expandedRows"
            v-model:filters="filtrosTabela"
            filterDisplay="menu"
            :globalFilterFields="[
                'descricao'                
            ]"
            tableStyle="min-width: 50rem"
        >
            <template #paginatorend>
                <div class="flex justify-content-between">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search"/>
                        <InputText v-model="filtrosTabela['global'].value" placeholder="Pesquisar o registro"/>
                    </span>
                </div>
            </template>
            <template #empty>
                <h1 class="text-center font-medium">
                    Nenhum Registro Encontrado
                </h1>
            </template>        
            <Column field="codigo" header="Código" class = "text-lg"></Column>
            <Column field="descricao" header="Descrição" class = "text-lg"></Column>
            <Column field="ativo" header="Ativo" class = "text-lg">
                <template #body="slotProps">
                    <i :class="{'pi pi-check text-green-500': slotProps.data.ativo, 'pi pi-times text-red-600': !slotProps.data.ativo }" style="font-size: 1.5rem;"></i>
                </template>
            </Column>  
            <Column field="acoes" header="Ações" class = "text-lg">
                <template #body="{data}">
                    <Button  
                        v-tooltip.top="{
                            value: 'Editar',
                            pt: {
                                arrow: {
                                    style: {
                                        borderTopColor: 'var(--primary-color)'
                                    }
                                },
                                text: 'bg-primary font-medium'
                            }
                        }" 
                        icon = "pi pi-pencil" 
                        @click="editarEvent(data)" />
                    <Button                         
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
                        @click="excluirEvent(data)" />                    
                </template>                
            </Column>          
        </DataTable>        
    </section>
    <ModalLoading :isLoading="loadingPrincipal"/>
</template>

<style scoped>

</style>