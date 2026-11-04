<script setup>
import {computed, onMounted, ref} from "vue";

const props = defineProps(['modelValue', 'inputId', 'maxSelectedLabels']);
const emit = defineEmits(['update:modelValue'])

const instituicoesSelecionadas = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const instituicoes = ref([]);

const buscaInstituicoes = async () => {

  const response = await window.axios.get('v4/api/configuracao/instituicao/usuarioLogado');

  for (const instituicao of response.data.data) {
      let opcao = {
          name: instituicao.nomeinst,
          code: instituicao.codigo
      };

      instituicoes.value.push(opcao)
      if (instituicao.instituicaoLogada) {
          instituicoesSelecionadas.value.push(opcao)
      }
  }
}

onMounted(() => {
  buscaInstituicoes();
});

</script>

<template>
    <MultiSelect v-model="instituicoesSelecionadas" :options="instituicoes"
                 :inputId="inputId ?? 'dd-instituicoes'"
                 :maxSelectedLabels="maxSelectedLabels ?? 3"
                 display="chip"
                 optionLabel="name"
                 placeholder="Selecione a(s) Instituições"/>
</template>
