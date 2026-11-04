<template>
    <div>
        <label>Incluir evento automático: </label>
        <Checkbox v-model="evtAutomatico.checked" :binary="true" />
        <fieldset style="background-color:#e1dede" v-if="evtAutomatico.checked">
            <div>
                <div>
                        <label class="required" for="numTermo">Número do Termo: </label>
                        <InputText id="numTermo" v-model="evtAutomatico.numTermo" type="text" class="p-inputtext-sm md:w-12rem"/>
                </div>
                <div>
                        <label for="tipoAlteracao">Tipo de Alteração: </label>
                        <Dropdown id="tipoAlteracao"
                                  style="margin:0.2em 0 0 0.57em"
                                  v-model="evtAutomatico.opcaoSelecionada"
                                  :options="tipoAlteracao"
                                  optionLabel="tipo"
                                  optionValue="valor"
                                  class="p-inputtext-sm md:w-12rem"/>
                </div>
                <div>
                        <label>Justificativa: </label>
                        <Textarea style="margin-top: 0.2em" id="justificativa" v-model="evtAutomatico.justificativa" autoResize
                                  rows="7" cols="100" :maxlength="2000" />
                </div>
            </div>
        </fieldset>
    </div>
</template>

<script>
import { ref,reactive, watch,onMounted, computed} from 'vue';
import Checkbox from 'primevue/checkbox'
import Fieldset from 'primevue/fieldset'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'

export default {
    emits:['eventoAutomaticoAlterado'],
    name: "EventoAutomatico",
    props: {
        limparCampos: {
            type: Boolean,
            required: false
        }
    },
    components: {
        Checkbox,
        Fieldset,
        InputText,
        Dropdown,
        Textarea
    },
    setup( props,{emit} ) {
        const tipoAlteracao = ref([
            {'tipo':'Aditamento','valor':1},
            {'tipo':'Apostilamento','valor':2}
        ]);
        const limparCampos = computed( () => {
            return props.limparCampos;
        });
        const evtAutomatico = reactive({
            justificativa: '',
            opcaoSelecionada: 1,
            numTermo: null,
            checked: false,
        });
        onMounted( () => {
            emit('eventoAutomaticoAlterado', evtAutomatico)
        })

        watch(evtAutomatico, () => {
            emit('eventoAutomaticoAlterado', evtAutomatico)
        })

        watch(limparCampos, () => {
            if(limparCampos.value) {
                evtAutomatico.justificativa = '';
                evtAutomatico.opcaoSelecionada = 1;
                evtAutomatico.numTermo = null;
                evtAutomatico.checked = false;
            }
        })

        return {
            evtAutomatico,
            tipoAlteracao
        };
    },
};
</script>

<style>

</style>

