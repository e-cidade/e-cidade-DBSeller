<script setup>

import {onMounted, ref} from 'vue';
import ModalLoading from '../../../../Components/ModalLoading';
import {useToast} from 'primevue/usetoast';
import DialogPesquisaContratos from '../../../Components/DialogPesquisaContratos.vue';

const mostraPesquisaContratos = ref(false);
const inputLoading = ref(false);
const loading = ref(false);
const toast = useToast();
const rotas = {
    enviarObra: 'v4/api/patrimonial/licitacoes/licitacon/obras/incluir/',
    buscarFamilias: 'v4/api/patrimonial/licitacoes/licitacon/obras/familias/',
    buscarSubFamilias: 'v4/api/patrimonial/licitacoes/licitacon/obras/sub-familias/',
    buscarDetalhamentoCaracteristicas: 'v4/api/patrimonial/licitacoes/licitacon/obras/detalhamento-caracteristicas/',
}
const props = defineProps({
    instituicao: {Type: String, required: true},
    departamento: {Type: Number, required: true}
});
const form = ref({
    contrato: {
        data: [],
        label: 'Contrato',
        disabled: true,
        value: ref(null)
    },
    tipoInstrumento: {
        label: 'Tipo Instrumento',
        placeholder: 'Selecione um Tipo de Instrumento',
        data: [
            {
                descricao: 'TERMO DE ADESÃO',
                value: 'A',
                codigo: 1
            },
            {
                descricao: 'ATA DE REGISTRO DE PREÇO',
                value: 'ARP'
            },
            {
                descricao: 'CONTRATO',
                value: 'C',
                codigo: 2
            },
            {
                descricao: 'TERMO DE FOMENTO',
                value: 'F',
                codigo: 3
            },
            {
                descricao: 'CONTRATO DE GESTÃO',
                value: 'G',
                codigo: 7
            },
            {
                descricao: 'NOTA DE EMPENHO',
                value: 'N'
            },
            {
                descricao: 'ACORDO DE COOPERAÇÃO',
                value: 'O',
                codigo: 8
            },
            {
                descricao: 'TERMO DE PARCERIA',
                value: 'P',
                codigo: 4
            },
            {
                descricao: 'TERMO DE CREDENCIAMENTO',
                value: 'R',
                codigo: 5
            },
            {
                descricao: 'TERMO DE COLABORAÇÃO',
                value: 'T',
                codigo: 6
            },
            {
                descricao: 'TERMO DE PERMISSÃO DE USO',
                value: 'U',
                codigo: 9
            },
            {
                descricao: 'CONVÊNIO',
                value: 'V'
            },
        ],
        disabled: false,
        value: ref()
    },
    garantia: {
        label: 'Garantia',
        value: ref(false),
        disabled: false,
        required: false,
    },
    familia: {
        label: 'Família',
        placeholder: 'Selecione uma família',
        data: [],
        value: ref(null),
        disabled: true
    },
    subFamilia: {
        label: 'Sub Família',
        placeholder: 'Selecione uma sub-família',
        data: [],
        value: ref(null),
        disabled: true
    },
    detalhamentoCaracteristica: {
        label: 'Detalhamento Característica',
        placeholder: 'Selecione uma Característica',
        data: [],
        value: ref(null),
        disabled: true,
        loading: false,
    },
    cep: {
        label: 'CEP',
        value: '',
        disabled: false,
        loading: false
    },
    logradouro: {
        label: 'Logradouro',
        value: '',
        disabled: false
    },
    bairro: {
        label: 'Bairro',
        value: '',
        disabled: false
    },
    municipio: {
        label: 'Município',
        value: '',
        disabled: false
    },
    botaoBuscarContrato: {
        disabled: false,
        required: false,
    },
    botaoEnviar: {
        label: 'Enviar',
        disabled: false,
        required: false,
    },
});
const filtros = ref({
    ac16_instit: props.instituicao,
    ac16_coddepto: props.departamento,
    ac16_acordosituacao: 4
});

onMounted(async () => {
    if (!(await verificarOrgaoFiscalizado())) {
        return;
    }

    await buscarFamilias();
    await buscarDetalhamentoCaracteristicas();
});

function bloquearCampos() {
    const camposFormulario = Object.values(form.value);
    camposFormulario.forEach(el => el.disabled = true);
}

function selecionarContrato(contrato) {
    limparCampos();

    form.value.contrato.data = contrato;
    form.value.contrato.value = `${contrato.ac16_numero}/${contrato.ac16_anousu}`;

    const tipoInstrumento = form.value.tipoInstrumento.data.find(tipo => tipo.codigo === contrato.ac16_tipoinstrumento);
    form.value.tipoInstrumento.value = tipoInstrumento ? tipoInstrumento.value : 0;

    mostraPesquisaContratos.value = false;
}

function campoEstaVazio(valor) {
    return valor === '' || valor === undefined || valor === false || valor === null;
}

function limparCampos() {
    const camposFormulario = Object.values(form.value);
    camposFormulario.forEach(el => el.value = '');
}

function limparCep(verificarCep) {
    if (form.value.cep.value.length !== 8 || verificarCep) {
        form.value.logradouro.value = '';
        form.value.municipio.value = '';
        form.value.bairro.value = '';
    }
}

function validarCamposObrigatorios() {
    const camposFormulario = Object.values(form.value);
    let existeCampoVazio = false;

    for (let campo of camposFormulario) {
        if (campo.required !== false && existeCampoVazio === false) {
            if (campoEstaVazio(campo.value)) {
                toast.add({
                    summary: 'Erro: ',
                    detail: `O campo ${campo.label} é obrigatório.`,
                    severity: "error",
                    life: 4000
                });

                existeCampoVazio = true;
            }
        }
    }

    return existeCampoVazio !== true;
}

async function buscarFamilias() {
    inputLoading.value = true;

    try {
        const response = await window.axios.get(rotas.buscarFamilias);
        form.value.familia.data = response.data.data.map(familia => {
            if (familia.descricao) {
                familia.descricao = familia.descricao.toUpperCase();
            }

            return familia;
        });

        form.value.familia.disabled = false;
    } catch (e) {
        bloquearCampos();
        toast.add({detail: 'Não foi possível buscar famílias.', summary: 'Erro: ', severity: 'error', life: 6000})
    }

    inputLoading.value = false;
}

async function buscarSubFamilias() {
    inputLoading.value = true;
    form.value.subFamilia.value = null;

    try {
        const response = await window.axios.get(rotas.buscarSubFamilias, {
            params: {
                familias: [form.value.familia.value]
            }
        });

        form.value.subFamilia.data = response.data.data.map(subFamilia => {
            if (subFamilia.descricaoTipoSubfamilia) {
                subFamilia.descricaoTipoSubfamilia = subFamilia.descricaoTipoSubfamilia.toUpperCase();
            }

            return subFamilia;
        });

        if (form.value.subFamilia.data.length <= 0) {
            return toast.add({
                detail: 'Nenhuma sub-família encontrada para esta família.',
                summary: 'Erro: ',
                severity: 'error',
                life: 6000
            })
        }

        form.value.subFamilia.disabled = false;
    } catch (e) {
        bloquearCampos();
        toast.add({detail: 'Não foi possível buscar sub-famílias.', summary: 'Erro: ', severity: 'error', life: 6000})
    }

    inputLoading.value = false;
}

async function buscarDetalhamentoCaracteristicas() {
    form.value.detalhamentoCaracteristica.loading = true;

    try {
        const response = await window.axios.get(rotas.buscarDetalhamentoCaracteristicas);
        form.value.detalhamentoCaracteristica.data = response.data.data.map(caracteristica => {
            if (caracteristica.nome) {
                caracteristica.nome = caracteristica.nome.toUpperCase();
            }

            return caracteristica;
        });
        form.value.detalhamentoCaracteristica.disabled = false;
    } catch (e) {
        bloquearCampos();
        toast.add({
            detail: 'Não foi possível buscar detalhamento de caracteristicas.',
            summary: 'Erro: ',
            severity: 'error',
            life: 6000
        });
    }

    form.value.detalhamentoCaracteristica.loading = false;
}

async function buscarCep() {
    form.value.cep.loading = true;

    let cep = form.value.cep.value;
    cep = cep ? cep.replace('-', '') : cep;

    if (cep.length !== 8) {
        form.value.cep.loading = false;
        return toast.add({detail: 'Digite um cep válido.', summary: 'Erro: ', severity: 'error', life: 6000});
    }

    try {
        const axios = require('axios');
        const response = await axios.get(`https://viacep.com.br/ws/${cep}/json/`);

        if (response.data.erro) {
            toast.add({detail: 'Cep não encontrado.', summary: 'Erro: ', severity: 'warn', life: 6000});
            limparCep(true);
        }

        form.value.logradouro.value = response.data.logradouro;
        form.value.municipio.value = response.data.localidade;
        form.value.bairro.value = response.data.bairro;
    } catch (e) {
        toast.add({
            detail: 'Não foi possível buscar os dados deste cep.',
            summary: 'Erro: ',
            severity: 'error',
            life: 6000
        })
    }

    form.value.cep.loading = false;
}

async function verificarOrgaoFiscalizado() {
    let orgaoFiscalizado = false;
    let mensagemErro = 'Orgão não fiscalizado';

    try {
        const response = await axios.get(
            'v4/api/patrimonial/licitacoes/licitacon/obras/orgao-fiscalizado/' + props.instituicao
        );
        orgaoFiscalizado = response.data.data.fiscalizado === true;
    } catch (e) {
        mensagemErro = 'Falha ao verificar fiscalização do órgao'
    }

    if (orgaoFiscalizado === false) {
        bloquearCampos();
        toast.add({detail: mensagemErro, summary: 'Erro: ', severity: 'error', life: 6000});
    }

    return orgaoFiscalizado;
}

async function enviarObra() {
    const params = {};
    params.caracteristicas = [form.value.detalhamentoCaracteristica.value];
    params.contrato = form.value.contrato.data.ac16_sequencial;
    params.tipoInstrumento = form.value.tipoInstrumento.value;
    params.codigoTipoSubFamilia = form.value.subFamilia.value;
    params.codigoTipoFamilia = form.value.familia.value;
    params.logradouro = form.value.logradouro.value;
    params.municipio = form.value.municipio.value;
    params.garantia = form.value.garantia.value;
    params.familia = form.value.familia.value;
    params.bairro = form.value.bairro.value;
    params.cep = form.value.cep.value.replace('-', '');

    if (!validarCamposObrigatorios()) {
        return;
    }
    try {
        const response = await window.axios.get(
            `${rotas.enviarObra}${form.value.contrato.data.ac16_sequencial}`,
            {
                params: params,
            });

        toast.add({
            summary: 'Sucesso: ',
            detail: 'Contrato enviado ao Licitacon Obras.',
            severity: 'success',
            life: 6000
        });
        limparCampos();
    } catch (e) {
        toast.add({
            detail: `${e.response.data.message} (${e.response.status}).`,
            summary: 'Erro:',
            severity: 'error',
            life: 6000
        });
    }
}
</script>

<template>
    <ModalLoading :is-loading="loading"></ModalLoading>

    <section>
        <Panel class="container no-select" header="Obras">
            <br/>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                                <InputText
                                    id="contrato"
                                    v-model="form.contrato.value"
                                    :disabled="form.contrato.disabled"
                                />
                            <label for="contrato" class="required">{{ form.contrato.label }}</label>
                        </span>
                        <Button
                            icon="pi pi-search"
                            aria-label="Filter"
                            style="width: 100px"
                            @click="mostraPesquisaContratos = true"
                            :disabled="form.botaoBuscarContrato.disabled"
                        />
                    </div>
                </div>

                <div class="field col-12 md:col-4">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <Dropdown
                                id="tipoInstrumento"
                                option-label="descricao"
                                option-value="value"
                                v-model="form.tipoInstrumento.value"
                                :placeholder="form.tipoInstrumento.placeholder"
                                :options="form.tipoInstrumento.data"
                                :disabled="form.tipoInstrumento.disabled"
                            />
                            <label class="required">{{ form.tipoInstrumento.label }}</label>
                        </span>
                    </div>
                </div>

                <div class="field col-12 md:col-2">
                    <div class="p-inputgroup flex-1 mt-2">
                        <Checkbox
                            v-model="form.garantia.value"
                            :binary="true"
                            :disabled="form.garantia.disabled"
                        />
                        <label for="garantia" class="mt-1 ml-2 required">{{ form.garantia.label }}</label>
                    </div>
                </div>
            </div>

            <div class="p-fluid grid mt-1">
                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <Dropdown
                                id="familia"
                                option-label="descricao"
                                option-value="codigoTipoFamilia"
                                v-model="form.familia.value"
                                :placeholder="form.familia.placeholder"
                                :options="form.familia.data"
                                :disabled="form.familia.disabled"
                                :loading="inputLoading"
                                @change="buscarSubFamilias()"
                            />
                            <label class="required">{{ form.familia.label }}</label>
                        </span>
                    </div>
                </div>

                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <Dropdown
                                id="subFamilia"
                                option-label="descricaoTipoSubfamilia"
                                option-value="codigoTipoSubfamilia"
                                v-model="form.subFamilia.value"
                                :placeholder="form.subFamilia.placeholder"
                                :options="form.subFamilia.data"
                                :disabled="form.subFamilia.disabled"
                                :loading="inputLoading"
                            />
                            <label class="required">{{ form.subFamilia.label }}</label>
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-fluid grid mt-1">
                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <Dropdown
                                id="detalhamentoCaracteristicas"
                                option-label="nome"
                                option-value="chave"
                                v-model="form.detalhamentoCaracteristica.value"
                                :placeholder="form.detalhamentoCaracteristica.placeholder"
                                :options="form.detalhamentoCaracteristica.data"
                                :disabled="form.detalhamentoCaracteristica.disabled"
                                :loading="form.detalhamentoCaracteristica.loading"
                            />
                            <label class="required">{{ form.detalhamentoCaracteristica.label }}</label>
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-fluid grid mt-1">
                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <InputMask
                                id="cep"
                                mask="99999-999"
                                v-model="form.cep.value"
                                :loading="form.cep.loading"
                                :disabled="form.cep.disabled"
                                @change="limparCep()"
                            />
                            <label class="required">{{ form.cep.label }}</label>
                        </span>
                        <Button
                            icon="pi pi-search"
                            style="width: 100px"
                            @click="() => buscarCep()"
                            :loading="form.cep.loading"
                            :disabled="form.cep.disabled"
                        />
                    </div>
                </div>

                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <InputText
                                id="logradouro"
                                v-model="form.logradouro.value"
                                :loading="form.cep.loading"
                                :disabled="form.logradouro.disabled"
                            />
                            <label class="required">{{ form.logradouro.label }}</label>
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-fluid grid mt-1">
                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <InputText
                                id="bairro"
                                v-model="form.bairro.value"
                                :loading="form.cep.loading"
                                :disabled="form.bairro.disabled"
                            />
                            <label class="required">{{ form.bairro.label }}</label>
                        </span>
                    </div>
                </div>

                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <InputText
                                id="municipio"
                                v-model="form.municipio.value"
                                :loading="form.cep.loading"
                                :disabled="form.municipio.disabled"
                            />
                            <label class="required">{{ form.municipio.label }}</label>
                        </span>
                    </div>
                </div>
            </div>
        </Panel>

        <div class="container">
            <div class="flex justify-content-end">
                <Button
                    class="p-button" icon="pi pi-send"
                    @click="enviarObra"
                    :label="form.botaoEnviar.label"
                    :disabled="form.botaoEnviar.disabled"
                />
            </div>
        </div>

    </section>

    <DialogPesquisaContratos
        v-if="mostraPesquisaContratos"
        @linha-selecionada="selecionarContrato"
        v-model:visible="mostraPesquisaContratos"
        :filtros="filtros"
    />
</template>

<style scoped>
.no-select {
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
</style>
