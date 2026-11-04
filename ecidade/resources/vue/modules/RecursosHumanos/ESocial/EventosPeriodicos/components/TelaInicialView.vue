<script setup>
import { watch, ref } from "vue"
defineProps(['isLoading', 'btnDisabled'])
const periodosValue = ref();
const selectedPeriodo = ref({ key: 1 });
const errors = ref()

const alterar = () => {
  selectedPeriodo.value === 1 ? 2 : 1
};

const periodos = ref([
  { name: "Mensal (AAAA-MM)", key: "1" },
  { name: "Anual (AAAA)", key: "2" },
]);
watch(periodosValue, () => {
  const datas = periodosValue.value.split('-')
  const dataAtual = new Date()
  const validaAno = dataAtual.getFullYear()
  const validaAnoSec = parseInt(validaAno) - parseInt(datas[0])

    if (parseInt(validaAnoSec) > 150 || datas[0] > validaAno) {
      errors.value = 'Ano inválido'
      btnDisabled.value = true
      return false
    } else {
      errors.value = ''
      btnDisabled.value = false

    }
  
    if (selectedPeriodo.value.key == 1) {
      const meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']
      const mesesValidos = meses.includes(datas[1])
      if (!mesesValidos) {
        errors.value = 'Mês inválido'
        btnDisabled.value = true
        return false
      } else {
        btnDisabled.value = false
      }

      const mesAnoRegex =  new RegExp(/^\d{4}-\d{2}$/)
      const regex = mesAnoRegex.test((datas[0] + '-' + datas[1]))
      if (!regex) {
        errors.value = 'Erro de formato de data'
        btnDisabled.value = true
        return false
      }
    } else if (selectedPeriodo.value.key == 2) {

      const mesAnoRegex = new RegExp(/^\d{4}$/)
      const regex = mesAnoRegex.test(datas[0])
      if (!regex) {
        errors.value = 'Erro de formato de data'
        btnDisabled.value = true
        return false
      }
    }
})

</script>

<template>
        <div class="line">
            {{ btnDisabled }}
                <template v-if="isLoading">
                  <Skeleton width="10rem" height="2rem" class="w-full mb-3"></Skeleton>
                </template>
                <template v-else>
                  <Dropdown v-model="selectedPeriodo" :options="periodos" optionLabel="name"
                    placeholder="Mensal(AAAA-MM)" class="w-full md:w-[14rem]" @change="alterar" />
                </template>
                <Divider />
                <template v-if="isLoading">
                  <Skeleton width="10rem" height="2rem" class="w-full mb-3"></Skeleton>
                </template>
                <template v-else>
                  <template v-if="selectedPeriodo.key == 1">
                    <InputMask label="Período" v-model="periodosValue" mask="9999-99" class="w-full mb-3" />
                    <Message severity="error" v-if=" errors">{{ errors }}</Message>
                  </template>
                  <template v-else>
                    <InputMask label="Período" v-model="periodosValue" mask="9999" class="w-full mb-3" />
                    <Message severity="error" v-if=" errors">{{ errors }}</Message>
                  </template>
                </template>
              </div>
              
</template>