<script setup>
import ModalLoading from "../../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import {onMounted, ref} from "vue";
import ConfirmPopup from "primevue/confirmpopup";
import ManutencaoConteudoDesenvolvido from "../../Components/ManutencaoConteudoDesenvolvido.vue";
import DialogPesquisaRegentes from "../../Components/DialogPesquisaRegentes.vue";
import LancamentoHabilidadesBNCC from "../../Components/LancamentoHabilidadesBNCC.vue";

const props = defineProps(['user', 'escola'])
const mostraPesquisaRegente = ref(false);
const mostraLancamentoHabilidade = ref(false)
const form = ref({
    codigo: null,
    regente: {
        data: {name: null, code: null},
        label: 'Regente',
        disabled: true
    },
    slctdTurma: {
        data: [],
        label: 'Turma',
        disabled: false
    },
    slctdEtapa: {
        data: null,
        label: 'Etapa',
        disabled: true
    },
    slctdTipoInstrumento: {
        data: null,
        label: 'Instrumento Avaliativo',
        disabled: false
    },
    slctdRegencia: {
        data: null,
        label: 'Regência',
        disabled: true
    },
    slctdTurno: {
        data: null,
        label: 'Turno',
        disabled: true
    },
    slctdReferencia: {
        data: null,
        label: 'Referência',
        disabled: false
    },
    aulasDadas: {
        disabled: true,
        label: 'Aulas Dadas',
        value: 1
    },
    data: {
        disabled: true,
        value: null,
        label: 'Data'
    },
    conteudo: {
        disabled: true,
        value: null,
        label: 'Conteúdo Desenvolvido'
    },
    btnSalvar: {
        label: 'Salvar Conteúdo',
        disabled: true
    },
    btnLancarHabilidade: {
        label: 'Lançar Habilidade',
        disabled: true
    }
})

const mostraManutencao = ref(false)
const optionsTurma = ref([]);
const optionsTurno = ref([])
const optionsReferencia = ref([])
const optionsEtapa = ref([])
const optionsRegencia = ref([])
const confirm = useConfirm();
const toast = useToast()
const loading = ref(false)
const usuario = ref(null);
const routes = {
    dadosRegente: `v4/api/educacao/escola/diario-classe/registro-aula/get-dados-regente`,
    instrumentos: `v4/api/educacao/secretaria/tipos-insturmentos-avaliativos/byEnsino`,
    salvarConteudo:  `v4/api/educacao/escola/diario-classe/registro-aula/salvar-conteudo`,
    buscarConteudos: `v4/api/educacao/escola/diario-classe/registro-aula/get-conteudos-regente-escola`
}
const optionsTipoInstrumento = ref([]);
const ensinos = ref();

const getDadosRegente = async (escola = props.escola, user = props.user) => {
    try {
        loading.value = true
        usuario.value = (await window.axios.get(`${routes.dadosRegente}/${escola}/${user}`)).data.data
        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
    form.value.regente.data = {name: usuario.value.nome, code: usuario.value.usuario}
    optionsTurma.value = usuario.value.turmas
}

const getTiposInstrumentosAvaliativos = async () => {
    try {
        form.value.data.disabled = true;
        optionsRegencia.value = form.value.slctdEtapa.data.regencias.map(regencia => {
            return {code: regencia.codigo, nome: regencia.disciplina.disciplina.nome}
        })
        if (optionsRegencia.value.length === 1) {
                        form.value.slctdRegencia.data = optionsRegencia.value[0];
                        form.value.data.disabled = false;
        }
        let ensino = form.value.slctdEtapa.data.ensino.codigo
        loading.value = true
        optionsTipoInstrumento.value = (await window.axios.get(`${routes.instrumentos}/${ensino}`)).data.data
        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const salvarConteudo = async () => {
    let parametros = {}
    if (form.value.codigo !== null) {
        parametros.codigo = form.value.codigo
    }
    parametros.usuario = usuario.value.usuario
    parametros.turma = form.value.slctdTurma.data.codigo
    parametros.etapa = form.value.slctdEtapa.data.codigo
    parametros.regencia = form.value.slctdRegencia.data.code
    parametros.conteudo = form.value.conteudo.value
    parametros.data = form.value.data.value
    parametros.turnoReferente = form.value.slctdReferencia.data.value
    parametros.tipoInstrumento =
        form.value.slctdTipoInstrumento.data === null ? null : form.value.slctdTipoInstrumento.data.codigo
    parametros.aulasDadas = form.value.aulasDadas.value
    try {
        loading.value = true
        let conteudo = (await window.axios.post(routes.salvarConteudo, parametros)).data.data
        form.value.codigo = conteudo.codigo
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Salvo com Sucesso!',
            life: 5000
        });
        mostraLancamentoHabilidade.value = true
    } catch (e) {
        loading.value = false
        let msg = e.response.data.errors != undefined ? e.response.data.errors.data[0] : e.response.data.message
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: msg,
            life: 5000
        });
    }
}

const limpaCampos = () => {
    form.value.codigo = null
    form.value.slctdTurma.data = null
    form.value.slctdEtapa.data = null
    form.value.slctdTipoInstrumento.data = null
    optionsTipoInstrumento.value.length = 0
    form.value.slctdRegencia.data = null
    form.value.slctdTurno.data = null
    form.value.slctdReferencia.data = null
    form.value.data.value = null
    form.value.conteudo.value = null
    form.value.aulasDadas.value = 1
}
const liberaCampos = () => {
    form.value.slctdTurma.disabled = false
    form.value.slctdEtapa.disabled = form.value.slctdTurma.data === null
    form.value.slctdTurno.disabled = form.value.slctdEtapa.data === null
    form.value.slctdRegencia.disabled = form.value.slctdTurno.data === null
    form.value.data.disabled = form.value.slctdRegencia.data === null
    form.value.conteudo.disabled = form.value.data.value === null
    form.value.btnLancarHabilidade.disabled = form.value.conteudo === null
    form.value.btnSalvar.disabled = form.value.slctdEtapa.disabled ||
        form.value.slctdRegencia.disabled ||
        form.value.slctdTurno.disabled ||
        form.value.data.disabled ||
        form.value.conteudo.value === null || form.value.conteudo.value === ''
}

const setaEtapaTurno = () => {
    optionsEtapa.value = form.value.slctdTurma.data.etapas
    if (optionsEtapa.value.length === 1) {
        form.value.slctdEtapa.data = optionsEtapa.value[0];
        getTiposInstrumentosAvaliativos()
    }
    optionsTurno.value = [form.value.slctdTurma.data.turno]
    if (optionsTurno.value.length === 1) {
        form.value.slctdTurno.data = optionsTurno.value[0];
    }
    optionsReferencia.value = form.value.slctdTurma.data.turnoReferente;
    form.value.slctdReferencia.data = optionsReferencia.value[0];
    form.value.aulasDadas.disabled = form.value.slctdTurma.data.medidaFrequencia === 'D';
    liberaCampos();
}

const preencheCampos = async (data) => {
    mostraManutencao.value = false
    form.value.codigo = data.codigo
    form.value.slctdTurma.data = optionsTurma.value.filter(opt => data.turma.codigo === opt.codigo)[0]
    setaEtapaTurno()
    form.value.slctdEtapa.data = optionsEtapa.value.filter(opt => data.regencia.etapa.codigo === opt.codigo)[0]
    await getTiposInstrumentosAvaliativos()
    if (data.tipoInstrumento !== null) {
        form.value.slctdTipoInstrumento.data = optionsTipoInstrumento.value.filter(opt => data.tipoInstrumento.codigo === opt.codigo)[0]
    }
    form.value.slctdRegencia.data = optionsRegencia.value.filter(opt => data.regencia.codigo === opt.code)[0]
    form.value.slctdTurno.data = optionsTurno.value.filter(opt => data.turno.codigo === opt.codigo)[0]
    form.value.slctdReferencia.data = optionsReferencia.value.filter(opt => data.turnoReferente.value === opt.value)[0]
    let date = new Date(data.data)
    form.value.data.value = new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());
    form.value.aulasDadas.value = data.aulasDadas
    form.value.conteudo.value = data.conteudo
    form.value.slctdTurma.disabled = true
    form.value.conteudo.disabled = false
    form.value.btnSalvar.disabled = false

    liberaCampos()
}

const preencheRegente = async (data) => {
    await getDadosRegente(props.escola, data.usuarioInterno)
    mostraPesquisaRegente.value = false
}

const fechaLancamento = (e) => {
    if (!e) {
        limpaCampos()
        liberaCampos()
    }
}

const verificaConteudo = async () => {
    if (form.value.data.value === null) {
        return;
    }

    let parametros = {}
    parametros.regencia = form.value.slctdRegencia.data.code
    parametros.data =  form.value.data.value.toLocaleDateString('en-US', {timeZone: 'UTC'});
    parametros.usuario = usuario.value.usuario

    if (form.value.slctdReferencia.data !== null) {
        parametros.turno = form.value.slctdTurno.data.codigo
    }
    if (form.value.slctdReferencia.data !== null) {
        parametros.turnoReferente = form.value.slctdReferencia.data.value
    }

    const urlParams = new URLSearchParams(parametros)
    try {
        loading.value = true
        let conteudo = (await window.axios.get(`${routes.buscarConteudos}?${urlParams.toString()}`)).data.data.shift()

        if (conteudo != undefined) {
            form.value.codigo = conteudo.codigo
            form.value.conteudo.value = conteudo.conteudo
            form.value.aulasDadas.value = conteudo.aulasDadas
            if (conteudo.tipoInstrumento !== null) {
                form.value.slctdTipoInstrumento.data = optionsTipoInstrumento.value.filter(opt => conteudo.tipoInstrumento.codigo === opt.codigo)[0]
            }
        } else {
            form.value.codigo = null
            form.value.conteudo.value = null
        }

        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
    liberaCampos()
}

const mostraManutencaoConteudo = () => {
    limpaCampos()
    mostraManutencao.value = true
}

onMounted(async () => {
    if (props.user != 1) {
        await getDadosRegente();
    }
})

</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container">
        <Panel header="Registro de Aula" style="width: 800px; margin: 0 auto">
            <br/>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-7 col-offset-1" >
                    <span class="p-float-label" v-if="user != 1">
                        <InputText
                            v-model="form.regente.data.name"
                            :disabled="form.regente.disabled"
                            @update:modelValue="liberaCampos"
                        />
                        <label for="">{{ form.regente.label }}</label>
                    </span>
                    <div class="p-inputgroup flex-1" v-else>
                        <span class="p-float-label">
                                <InputText id="anoLimite" style="width: 200px"
                                           v-model="form.regente.data.name" disabled/>
                                 <label for="">{{ form.regente.label }}</label>
                            </span>
                        <Button @click="mostraPesquisaRegente = true" icon="pi pi-search" aria-label="Filter" style="width: 100px"/>
                    </div>
                </div>
                <div class="field col-12 md:col-3" >
                    <Button class="p-button" label="Manutenção" icon="pi pi-cog"
                            :disabled="usuario === null"
                            @click="mostraManutencaoConteudo"
                    ></Button>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col">
                    <span class="p-float-label">
                        <Dropdown id="turma" optionLabel="nome" :options="optionsTurma"
                                  :disabled="form.slctdTurma.disabled"
                                  v-model="form.slctdTurma.data" @change="setaEtapaTurno"
                                  @update:modelValue="liberaCampos"/>
                        <label for="">{{ form.slctdTurma.label }}</label>
                    </span>
                </div>
                <div class="field col">
                    <span class="p-float-label">
                        <Dropdown id="etapa" optionLabel="nome" :options="optionsEtapa"
                                  v-model="form.slctdEtapa.data"
                                  :disabled="form.slctdEtapa.disabled"
                                  @change="getTiposInstrumentosAvaliativos"
                                  @update:modelValue="liberaCampos"
                        />
                        <label for="">{{ form.slctdEtapa.label }}</label>
                    </span>
                </div>
                <div class="field col" v-show="optionsReferencia.length > 1">
                    <span class="p-float-label">
                        <Dropdown id="turno" optionLabel="nome" :options="optionsTurno"
                                  v-model="form.slctdTurno.data" @update:modelValue="liberaCampos"
                                  @change="verificaConteudo"
                                  :disabled="form.slctdTurno.disabled"
                        />
                        <label for="">{{ form.slctdTurno.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid" v-show="optionsReferencia.length > 1">
                <div class="field col md:col-4 col-offset-4" >
                    <span class="p-float-label">
                        <Dropdown id="referencia" optionLabel="name" :options="optionsReferencia"
                                  :disabled="form.slctdReferencia.disabled"
                                  @change="verificaConteudo"
                                  v-model="form.slctdReferencia.data" @update:modelValue="liberaCampos"/>
                        <label for="">{{ form.slctdReferencia.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col" v-show="optionsTipoInstrumento.length > 0">
                    <span class="p-float-label">
                        <Dropdown id="tipoInstrumento" optionLabel="nome" :options="optionsTipoInstrumento"
                                  :disabled="form.slctdTipoInstrumento.disabled"
                                  v-model="form.slctdTipoInstrumento.data"
                                  @update:modelValue="liberaCampos"
                        />
                        <label for="">{{ form.slctdTipoInstrumento.label }}</label>
                    </span>
                </div>
                <div class="field col">
                    <span class="p-float-label">
                        <Dropdown id="regencia" optionLabel="nome" :options="optionsRegencia"
                                  :disabled="form.slctdRegencia.disabled"
                                  v-model="form.slctdRegencia.data"
                                  @update:modelValue="liberaCampos"
                                    @change="verificaConteudo"/>
                        <label for="">{{ form.slctdRegencia.label }}</label>
                    </span>
                </div>
                <div class="field col">
                    <span class="p-float-label">
                        <Calendar inputId="dateformat"
                                  v-model="form.data.value"
                                  :disabled="form.data.disabled"
                                  dateFormat="dd/mm/yy" @update:modelValue="verificaConteudo"/>
                        <label for="">{{ form.data.label }}</label>
                    </span>
                </div>
                <div class="field col  md:col-2">
                    <span class="p-float-label">
                        <InputNumber inputId="dateformat"
                                     v-model="form.aulasDadas.value"
                                     :disabled="form.aulasDadas.disabled"
                                     @update:modelValue="liberaCampos"/>
                        <label for="">{{ form.aulasDadas.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-8 col-offset-2">
                        <span class="p-float-label">
                            <Textarea v-model="form.conteudo.value"
                                      :disabled="form.conteudo.disabled"
                                      rows="5" cols="100" @update:modelValue="liberaCampos"/>
                            <label>{{ form.conteudo.label }}</label>
                        </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-3 col-offset-3">
                    <Button class="p-button" :label="form.btnSalvar.label" icon="pi pi-save" :disabled="form.btnSalvar.disabled"
                            @click="salvarConteudo"
                    ></Button>
                </div>
                <div class="field col-12 md:col-3">
                    <Button class="p-button" :label="form.btnLancarHabilidade.label" icon="pi pi-save" :disabled="form.codigo === null"
                            @click="mostraLancamentoHabilidade = true"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>
    <ManutencaoConteudoDesenvolvido v-model:visible="mostraManutencao" v-if="mostraManutencao" @editar="preencheCampos" @close="liberaCampos" :turmas="usuario.turmas" :usuario="usuario.usuario"></ManutencaoConteudoDesenvolvido>
    <DialogPesquisaRegentes v-model:visible="mostraPesquisaRegente" v-if="mostraPesquisaRegente" :escola="props.escola" @select-line="preencheRegente"></DialogPesquisaRegentes>
    <LancamentoHabilidadesBNCC @update:visible="fechaLancamento(e)" v-model:visible="mostraLancamentoHabilidade" v-if="mostraLancamentoHabilidade" :regencia="form.slctdRegencia.data.code" :conteudo="form.codigo"></LancamentoHabilidadesBNCC>
    <ModalLoading :isLoading="loading"/>
</template>
<style scoped>
i {
    cursor: pointer
}
:deep(.p-fieldset-legend) {
    padding: 0!important;
    border: none;
    background: transparent!important;
}

:deep(.p-fieldset-legend-text) {
    color: #a19f9d!important;
}

:deep(.p-fieldset) {
    background: #e1dede;
    padding: 0;
}
</style>
