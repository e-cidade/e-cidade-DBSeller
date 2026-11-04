<script setup>
import { ref } from "vue";
import {useToast} from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from "primevue/confirmpopup";
import ModalLoading from "../../../Components/ModalLoading.vue";
const props = defineProps(['visible', 'regencia', 'conteudo'])
const emits = defineEmits(['update:visible', 'editar'])

const habilidades = ref([])
const loading = ref(false)
const toast = useToast()
const confirm = useConfirm()
const temReferencial = ref(false)
const visivel = ref(props.visible)
const tree = ref()
const optionsDiscipinas = ref();
const form = ref({
    habilidadesInserir: [],
    habilidadesSelecionadas: [],
    slctdDisciplinas: {
        data: null,
        disabled: false,
        label: 'Disciplinas'
    },
    pesquisa: null
})
const routes = {
    disciplinas: `v4/api/educacao/escola/diario-classe/registro-aula/get-disciplinas-bncc/${props.regencia}`,
    habilidades: `v4/api/educacao/escola/diario-classe/registro-aula/get-habilidades-bncc`,
    habilidadesDesenvolvidas: `v4/api/educacao/escola/diario-classe/registro-aula/get-habilidades-desenvolvidas`,
    salvarHabiidades: `v4/api/educacao/escola/diario-classe/registro-aula/salvar-habilidade-desenvolvida`
}

const buscaDisciplinas = async () => {
    loading.value = true
    try {
        optionsDiscipinas.value = (await window.axios.get(routes.disciplinas)).data.data
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }

    if (optionsDiscipinas.value.length === 1) {
        form.value.slctdDisciplinas.data = optionsDiscipinas.value[0]
        buscaHabilidades()
    }

    loading.value = false
}
const buscaHabilidades = async () => {
    form.value.habilidadesInserir.length = 0
    let parametros = {}
    parametros.disciplinaBncc = form.value.slctdDisciplinas.data.codigo
    parametros.regencia = props.regencia
    const urlParams = new URLSearchParams(parametros)
    try {
        loading.value = true
        let retorno = (await window.axios.get(`${routes.habilidades}?${urlParams.toString()}`)).data.data
        habilidades.value = treeMapper(retorno)
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
    await buscaHabilidadesDesenvolvidas();
}

const buscaHabilidadesDesenvolvidas = async () => {
    let parametros = {}
    parametros.disciplinaBncc = form.value.slctdDisciplinas.data.codigo
    parametros.conteudo = props.conteudo
    const urlParams = new URLSearchParams(parametros)
    try {
        loading.value = true
        let retorno = (await window.axios.get(`${routes.habilidadesDesenvolvidas}?${urlParams.toString()}`)).data.data
        retorno.forEach(habilidade => {
            habilidades.value.forEach((unidades) => {
                unidades.children.forEach((objeto) => {
                    objeto.children.forEach((hab) => {
                        if (hab.children != undefined) {
                            hab.children.forEach((referencial) => {
                                if (referencial.key === "ref-"+habilidade.codigo) {
                                    temReferencial.value = true
                                    form.value.habilidadesSelecionadas[unidades.key] = {checked: false, partialChecked: true}
                                    form.value.habilidadesSelecionadas[objeto.key] = {checked: false, partialChecked: true}
                                    form.value.habilidadesSelecionadas[hab.key] = {checked: false, partialChecked: true}
                                    form.value.habilidadesSelecionadas[referencial.key] = { checked: true, partialChecked: false }
                                }
                            })
                        } else {
                            if (hab.key === "hab-"+habilidade.codigo) {
                                form.value.habilidadesSelecionadas[unidades.key] = {checked: false, partialChecked: true}
                                form.value.habilidadesSelecionadas[objeto.key] = {checked: false, partialChecked: true}
                                form.value.habilidadesSelecionadas[hab.key] = { checked: true, partialChecked: false }
                            }
                        }
                    })
                })
            })
        })
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
    selecionaHabilidade()
}
const selecionaHabilidade = () => {
    form.value.habilidadesInserir = Object.keys(form.value.habilidadesSelecionadas)
        .filter(key => {
            if (temReferencial.value) {
                return  isNaN(parseInt(key)) && key.slice(0, 3) === 'ref'
            } else {
                return  isNaN(parseInt(key)) && key.slice(0, 3) === 'hab'
            }
        })
        .map(key => {
            if (temReferencial.value) {
                return {code: key.replace('ref-','')}
            } else {
                return {code: key.replace('hab-','')}
            }
    })
}

const treeMapper = (data) => {
    let tree = [];
    data.forEach((unidadesTematicas, key1) => {
        let unidades = {
            key: `${key1}`,
            label: unidadesTematicas.nome,
            data: unidadesTematicas.nome
        }
        unidades.children = unidadesTematicas.objetosConhecimento.map((objetosConhecimento, key2) => {
            let objetos = {
                key: `${key2}-${objetosConhecimento.nome}`,
                label: objetosConhecimento.nome,
                data: objetosConhecimento.nome
            }
            objetos.children = objetosConhecimento.habilidades.map((habilidad, key3) => {
                let habilidades = {
                    key: "hab-" + habilidad.codigo,
                    label: habilidad.nome,
                    data: habilidad.codigo
                }

                if (habilidad.referencial != undefined) {
                    temReferencial.value = true
                    habilidades.children = habilidad.referencial.map(refrencial => {
                        let referecnia = {
                            key: 'ref-' + refrencial.ed168_codigoreferencial,
                            label: refrencial.ed168_habilidade,
                            data: refrencial.ed168_codigoreferencial
                        }

                        return referecnia;
                    })
                    let reduced = [];
                    habilidades.children.forEach((item) => {
                        var duplicated  = reduced.findIndex(redItem => {
                            return item.key == redItem.key;
                        }) > -1;

                        if(!duplicated) {
                            reduced.push(item);
                        }
                    });
                    habilidades.children = reduced
                }
                return habilidades
            })
            return objetos
        })
        tree.push(unidades)
    })
    return tree
}

const excluirHabilidade = (code) => {
    if (temReferencial.value) {
        delete form.value.habilidadesSelecionadas["ref-"+code]
    } else {
        delete form.value.habilidadesSelecionadas["hab-"+code]
    }

    selecionaHabilidade()
}

const salvarHabilidade = async () => {
    let parametros = {
        disciplina: form.value.slctdDisciplinas.data.codigo,
        habilidades: form.value.habilidadesInserir.map(habilidade => habilidade.code),
        conteudoDesenvolvido: props.conteudo
    }
    try {
        loading.value = true
        await window.axios.post(routes.salvarHabiidades, parametros)
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Salvo com sucesso!`,
            life: 5000
        });
        loading.value = true
        await buscaHabilidadesDesenvolvidas()
        loading.value = false

        visivel.value = false
        emits('update:visible')
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `Falha ao salvar!`,
            life: 5000
        });
    }
}

const pesquisaHabilidade = () => {
    let tem = []
    habilidades.value.forEach(habilidade => {
        habilidade.children.forEach(objeto => {
            objeto.children.forEach(habilidade => {
                if (habilidade.children != undefined) {
                    habilidade.children.forEach(referencia => {
                        console.log(habilidade.key, "ref-" + form.value.pesquisa)
                        if (referencia.key === "ref-" +  form.value.pesquisa) {
                            if (form.value.habilidadesInserir
                                .filter(hab => hab.code === "ref-" + form.value.pesquisa).length === 0) {
                                    form.value.habilidadesInserir.push({code: form.value.pesquisa})
                                    form.value.habilidadesSelecionadas[referencia.key] =
                                        {checked: true, partialChecked: false}
                                    tem.push(true)
                            }
                        }
                    })
                } else {
                    console.log(habilidade.key, "hab-" + form.value.pesquisa)
                    if (habilidade.key === "hab-" + form.value.pesquisa) {
                        if (form.value.habilidadesInserir.filter(hb => hb.code === "hab-" + form.value.pesquisa).length === 0) {
                            form.value.habilidadesInserir.push({code: form.value.pesquisa})
                            form.value.habilidadesSelecionadas[habilidade.key] = {checked: true, partialChecked: false}
                            tem.push(true)
                        }
                    }
                }
            })
        })
    })

    if (tem.length === 0) {
        toast.add({
            severity: 'warn',
            summary: 'Não encontrado',
            detail: 'Nenhuma habilidade encontrada',
            life: 5000
        });
        form.value.pesquisa = null
    }
}

buscaDisciplinas()

</script>

<template>
    <ConfirmPopup></ConfirmPopup>
    <Dialog :visible="visivel" modal header="Lançamento de Habilidades" @update:visible="value => emits('update:visible', value)" class="p-dialog-maximized">
        <section class="mt-5" style="width: 90vw; margin: 0 auto">
            <Fieldset legend="Habilidades BNCC">
                <div class="p-fluid grid mt-3">
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="disciplinas"  :options="optionsDiscipinas"
                                      optionLabel="nome"
                                      v-model="form.slctdDisciplinas.data"
                                      :disabled="form.slctdDisciplinas.disabled" @change="buscaHabilidades"/>
                             <label for="">{{ form.slctdDisciplinas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-2">
                        <span class="p-float-label">
                            <InputText
                                v-model="form.pesquisa"
                                :disabled="habilidades.length === 0"
                            />
                            <label for="">Pesquisa</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-1">
                        <Button icon="pi pi-search" aria-label="Pesquisar" @click="pesquisaHabilidade" :disabled="habilidades.length === 0"/>
                    </div>
                </div>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-8">
                        <ScrollPanel style="width: 100%; height: 60vh">
                            <Tree ref="tree" v-if="habilidades.length > 0" v-model:selectionKeys="form.habilidadesSelecionadas" :value="habilidades" selectionMode="checkbox" @update:selection-keys="selecionaHabilidade" class="w-full"></Tree>
                            <span v-else>Selecione uma disciplina</span>
                        </ScrollPanel>
                    </div>
                    <div class="field col-12 md:col-4">
                        <ScrollPanel style="width: 100%; height: 60vh">
                            <DataTable :value="form.habilidadesInserir" v-if="habilidades.length > 0">
                                <Column field="code" header="Selecionados"></Column>
                                <Column header="Ações" style="width: 10%">
                                    <template #body="{ data }">
                                        <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluirHabilidade(data['code'])"></i>
                                    </template>
                                </Column>
                            </DataTable>
                        </ScrollPanel>
                    </div>
                </div>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2 col-offset-4">
                        <Button @click="salvarHabilidade" icon="pi pi-save" label="Salvar" style="width: 100px" v-if="habilidades.length > 0"/>
                    </div>
                </div>
            </Fieldset>
        </section>
    </Dialog>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>
    :deep(.p-fieldset-legend) {
        padding: 0!important;
        border: none;
        background: transparent!important;
    }
    :deep(.p-fieldset-legend-text) {
        color: #a19f9d!important;
    }

    i {
        cursor: pointer
    }
</style>
