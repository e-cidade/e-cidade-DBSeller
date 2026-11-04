<template>
    <div class="container">
    <div class="card w-100">
      <Fieldset legend="Inclusão de Dedução">
        <div>
          <a href="javascript:void(0)" @click="abrirConsultaInscricao">Inscrição Municipal:</a>
          <InputText @keyup="verificaCaracteres" @focusout="selecionaInscricao(inscri)" type="text" v-model="inscri"
            class="w-2 ml-2 border-black-alpha-60 border-1" id="q02_inscr" />
          <InputText :disabled="true" class="w-8 ml-2 border-black-alpha-60 border-1" id="z01_nome"
            v-model="z01_nomeValue" />
        </div>
        <div class="flex">
          <Button type="button" id="pesquisar" label="Continuar" @click="deducaoAtividade = true;"
            class="m-auto mt-3 shadow-4" :disabled="continuarButton" />
        </div>
      </Fieldset>
      <DialogConsultaInscricaoMunicipal ref="dialogConsultaInscricaoMunicipal" @selectRow="selecionaInscricao" />
    </div>

    <ModalLoading :isLoading="isLoading"></ModalLoading>
  </div>

  <Dialog v-model:visible="deducaoAtividade" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
      position="top" header="Dedução Máxima por Atividade" class="shadow-4">
      <div class="container-dialog">
        <div class="children-dialog">
          <table class="table-atividades mt-3" id="tableAtividades">
            <thead>
              <th>Cód. CNAE</th>
              <th>Descrição</th>
              <th>Dedução Max. %</th>
            </thead>
            <tbody>
              <tr v-for="(atividade, index) in atividades" :key="atividade.q202_sequencial"
                @click="q202_sequencial = atividade.q202_sequencial"
                :class="index % 2 === 0 ? 'linha-par' : 'linha-impar'">
                <td>{{ atividade.q71_estrutural }}</td>
                <td>{{ atividade.q71_descr }}</td>
                <td><InputNumber inputId="percent" prefix="% " v-model="atividade.q204_deducao" :min="0" :max="100" /></td>
              </tr>
            </tbody>
          </table>
          <div class="rodape-dialog mt-4 mb-2">
            <div class="button-group">
              <Button type="button" label="Fechar" @click="deducaoAtividade = false" class="mr-2"></Button>
              <Button type="button" label="Salvar" @click="submitDeducao" class="mr-2"></Button>
            </div>
          </div>
        </div>
      </div>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
import Button from 'primevue/button';
import Fieldset from 'primevue/fieldset';
import InputText from 'primevue/inputtext';
import DialogConsultaInscricaoMunicipal from '../Arrecadacao/components/DialogConsultaInscricaoMunicipal.vue';
import ModalLoading from '@modules/Components/ModalLoading.vue';

const dialogConsultaInscricaoMunicipal = ref(null);
const isLoading = ref(false);
const toast = useToast();
const inscri = ref(null);
const z01_nomeValue = ref(null);
const continuarButton = ref(true);
const reqAnterior = ref(null);
const deducaoAtividade = ref(false);
const atividades = ref([]);

function throwToast(severity = "warn", summary = "Atenção", detail = "Atenção", life = 3000) {
  toast.add({ severity, summary, detail, life });
}


function verificaCaracteres(tipo) {
    let semLetras = inscri.value.replace(/[^0-9]/g, '');
    if (semLetras !== inscri.value) {
      inscri.value = semLetras;
      throwToast("warn", "Atenção", "Apenas números são permitidos.", 3000);
    }
}

function abrirConsultaInscricao() {

  dialogConsultaInscricaoMunicipal.value?.toggleDialog();
}

function selecionaInscricao(result) {
  inscri.value = result?.inscricao || result;
  validaInscri();
}

async function validaInscri() {
  const q02_inscr = inscri.value;

  if (q02_inscr === reqAnterior.value){ 
    return false; 
  }

  reqAnterior.value = q02_inscr;
  if (q02_inscr) {
    isLoading.value = true;
    const retorno = await requisitaInscr(q02_inscr);
    isLoading.value = false;

    if (retorno !== 'vazio' && retorno.length > 0) {
      atividades.value = [];
      z01_nomeValue.value = retorno[0]['cgm_salao'];
      atividades.value = retorno;
      continuarButton.value = false;
    } else {
      throwToast("warn", "Atenção", `Inscrição (${q02_inscr}) Não é Salão Parceiro ou Não possui atividades.`);
      z01_nomeValue.value = `Inscrição (${q02_inscr}) Não é Salão Parceiro ou Não possui atividades.`;
      continuarButton.value = true;
    }
  } else {
    z01_nomeValue.value = '';
    continuarButton.value = true;
  }
}

async function requisitaInscr(q202_inscr) {
  const parametros = new FormData();
  parametros.append('q202_inscr', q202_inscr);

  try {
    const response = await window.axios.post('v4/api/tributario/issqn/get-atividades-salao', parametros, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data.data) {
      return response.data.data
    } else {
      return 'vazio'
    }
  } catch (error) {
    isLoading.value = false;
    throwToast("error", "Erro", "Houve um erro ao buscar a inscrição");
    return false;
  }
}

async function submitDeducao() {
  isLoading.value = true;
  const deducoes = atividades.value.map(atividade => ({
    q204_sequencial: atividade.q204_sequencial,
    q202_inscr: atividade.q202_inscr,
    q204_estrutural: atividade.q71_estrutural,
    q204_deducao: atividade.q204_deducao
  }));

  const params = {deducoes};

  try {
    const response = await window.axios.post('v4/api/tributario/issqn/save-atividades-salao', params);

    isLoading.value = false;
    if(response.data == 'salvo'){
      reqAnterior.value = '';
      validaInscri();
      throwToast("success", "Sucesso", "Deduções das atividades salvas com sucesso!", 5000);
    }else{
      throwToast("error", "Erro", "Houve um erro ao salvar as Atividades", 5000);
      reqAnterior.value = '';
      validaInscri();
    }
  } catch (error) {
    reqAnterior.value = '';
    validaInscri();
    isLoading.value = false;
    throwToast("error", "Erro", "Houve um erro ao salvar, por favor, tente novamente.", 5000);
    return false;
  }

}

</script>
<style scoped>
.container-dialog {
  padding: 20px;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
}

.children-dialog {
  padding: 20px;
  width: 97%;
  max-width: 1000px;
  height: 95%;
  border: solid grey 1px;
  box-shadow: 0px 0px 15px -1px rgba(0, 0, 0, 0.75);
  border-radius: 5px;
}


.table-atividades {
  width: 100%;
  border-collapse: collapse;
  margin: auto;
}

.table-atividades th,
.table-atividades td {
  border: 1px solid #ddd;
  text-align: center;
}

.table-atividades th {
  border-bottom: 2px solid #000;
}

.table-atividades tbody tr {
  height: 40px;
}

.table-atividades tbody tr:hover {
  background-color: #f1f1f1;
}

.linha-par {
  background-color: white;
}

.linha-impar {
  background-color: whitesmoke;
}

.label {
  font-weight: bold;
  text-decoration: underline;
}

.dialog-input {
  width: 100%;
}

.rodape-dialog {
  text-align: center;
  padding-top: 20px;
}

.button-group {
  display: inline-flex;
  gap: 10px;
}
</style>