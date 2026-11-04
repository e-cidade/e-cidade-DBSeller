<template>
  <div class="container">
    <div class="card w-100">
      <Fieldset legend="Alteração Salão Parceiro">
        <div>
          <a href="javascript:void(0)" @click="abrirConsultaInscricao">Inscrição Municipal:</a>
          <InputText @keyup="verificaCaracteres" @focusout="selecionaInscricao(inscri)" type="text" v-model="inscri"
            class="w-2 ml-2 border-black-alpha-60 border-1" id="q02_inscr" />
          <InputText :disabled="true" class="w-8 ml-2 border-black-alpha-60 border-1" id="z01_nome"
            v-model="z01_nomeValue" />
        </div>
        <div class="flex">
          <Button type="button" id="pesquisar" label="Continuar" @click="verificaAtividades"
            class="m-auto mt-3 shadow-4" :disabled="continuarButton" />
        </div>
      </Fieldset>
      <dialogConsultaSalaoParceiro ref="dialogConsultaSalaoParceiro" @selectRow="selecionaInscricao" />
    </div>

    <Dialog v-model:visible="selecionaAtividade" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
      position="top" header="Selecione o Cadastro" class="shadow-4">
      <div class="container-salao">
        <div class="children-salao">
          <table class="table-atividades mt-3" id="tableAtividades">
            <thead>
              <th>Sequencial</th>
              <th>Parceiros</th>
              <th>Data Inicial</th>
              <th>Data Final</th>
              <th>Observação</th>
            </thead>
            <tbody>
              <tr v-for="(atividade, index) in atividades" :key="atividade.q202_sequencial"
                @click="selecionaSalaoParceiro(atividade.q202_sequencial); q202_sequencial = atividade.q202_sequencial"
                :class="index % 2 === 0 ? 'linha-par' : 'linha-impar'" style="cursor: pointer;">
                <td>{{ atividade.q202_sequencial }}</td>
                <td>{{ contarParceiros(atividade.parceiros) }}</td>
                <td>{{ atividade.q202_dtinicial }}</td>
                <td>{{ atividade.q202_dtfinal }}</td>
                <td>{{ atividade.q202_obs }}</td>
              </tr>
            </tbody>
          </table>
          <div class="rodape-salao mt-4 mb-2">
            <div class="button-group">
              <Button type="button" label="Fechar" @click="selecionaAtividade = false" class="mr-2"></Button>
            </div>
          </div>
        </div>
      </div>
    </Dialog>

    <Dialog v-model:visible="editSalaoParceiros" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
      position="top" header="Editar Salão Parceiro" class="shadow-4">
      <div class="container-salao">
        <div class="children-salao">
          <table class="form-table">
            <tbody>
              <tr>
                <td><label for="q02_inscr">Inscrição Municipal:* </label></td>
                <td>
                  <InputText type="text" v-model="q202_inscr" :disabled="true"
                    class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="q02_inscr" />
                </td>
              </tr>
              <tr>
                <td><label for="datainiSalao">Data Inicial:* </label></td>
                <td>
                  <Calendar v-model="datainiSalao" showIcon iconDisplay="input" inputId="datainiSalao"
                    :disabled="isDataIniDisabled" class="salao-input" />
                </td>
              </tr>
              <tr>
                <td><label for="datainiSalao">Data Final: </label></td>
                <td>
                  <Calendar v-model="dataFinalSalao" showIcon iconDisplay="input" inputId="datafinalSalao"
                    :disabled="isDataFimDisabled" class="salao-input" :showIconClear="true" :showButtonBar="true"
                    :clearButtonStyle="{ 'visibility': 'visible', 'margin-top': '10px' }" />
                </td>
              </tr>
              <tr>
                <td><label for="obssalao">Observação: </label></td>
                <td>
                  <InputText type="textbox" v-model="q202_obs" :disabled="isObsDisabled"
                    class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="obssalao" />
                </td>
              </tr>
            </tbody>
          </table>
          <Fieldset legend="Parceiros">
            <table class="table-atividades mt-3">
              <thead>
                <tr>
                  <th>Sequencial</th>
                  <th>Inscrição</th>
                  <th>CGM</th>
                  <th>Parceiro</th>
                  <th>Data Inicial</th>
                  <th>Data Final</th>
                  <th>Observação</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(editAtividade, index) in editSalao[0].parceiros" :key="editAtividade.q203_sequencial"
                  @click="selecionaParceiro(editAtividade.q203_sequencial)"
                  :class="index % 2 === 0 ? 'linha-par' : 'linha-impar'" style="cursor: pointer;">
                  <td>{{ editAtividade.q203_sequencial }}</td>
                  <td>{{ editAtividade.q203_inscrparc }}</td>
                  <td>{{ editAtividade.q203_numcgm }}</td>
                  <td>{{ editAtividade.cgm_parceiro }}</td>
                  <td>{{ editAtividade.q203_dtinicial }}</td>
                  <td>{{ editAtividade.q203_dtfinal }}</td>
                  <td>{{ editAtividade.q203_obs }}</td>
                </tr>
              </tbody>
            </table>
            <div class="rodape-salao mt-1 mb-1">
              <div class="button-group">
                <Button type="button" label="Adicionar Parceiro" :disabled="addParceiroButton"
                  @click="adicionaParceiro = true; editSalaoParceiros = false; limpaCamposNovoParceiro();" class="mr-2"></Button>
              </div>
            </div>
          </Fieldset>

          <div class="rodape-salao mt-4 mb-4">
            <div class="button-group">
              <Button type="button" label="Voltar" @click="verificaAtividades(); editSalaoParceiros = false"
                class="mr-2"></Button>
              <Button type="button" label="Salvar" :disabled="salvarButton"
                @click="submitSalao(q202_sequencial)"></Button>
            </div>
          </div>
        </div>
      </div>
    </Dialog>

    <Dialog v-model:visible="editarParceiros" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
      position="top" header="Editar Parceiro" class="shadow-4">
      <div class="container-salao">
        <div class="children-salao">
          <table class="form-table">
            <tbody>
              <tr>
                <td>Inscrição Municipal:</td>
                <td>
                  <InputText type="text" v-model="q203_inscrparc" :disabled="true"
                    class="w-full border-black-alpha-60 border-solid border-1" id="q02_inscr" />
                </td>
                <td>
                  <InputText :disabled="true" class="w-full border-black-alpha-60 border-solid border-1"
                    id="z01_nomeParc" v-model="cgmparc" />
                </td>
              </tr>
              <tr>
                <td>CGM:</td>
                <td>
                  <InputText type="text" v-model="q203_numcgm" :disabled="true"
                    class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="cgmparceiro" />
                </td>
                <td>
                  <InputText :disabled="true" class="w-full border-black-alpha-60 border-solid border-1"
                    id="cgmValueParc" v-model="cgmparc" />
                </td>
              </tr>
              <tr>
                <td><label for="datainiParc">Data Inicial:* </label></td>
                <td>
                  <Calendar v-model="q203_dtinicial" showIcon iconDisplay="input" inputId="datainiParc"
                    :disabled="isDataIniDisabled" class="salao-input" />
                </td>
              </tr>
              <tr>
                <td><label for="datainiParc">Data Final: </label></td>
                <td>
                  <Calendar v-model="q203_dtfinal" showIcon iconDisplay="input" inputId="dataFimParc"
                    :disabled="isDataFimDisabled" class="salao-input" :showIconClear="true" :showButtonBar="true" />
                </td>
              </tr>
              <tr>
                <td colspan="1"><label for="obssalao">Observação: </label></td>

                <td colspan="2">
                  <InputText type="textbox" v-model="q203_obs" :disabled="isObsDisabled"
                    class="input-field border-black-alpha-60 border-solid border-1 salao-input w-full" id="obssalao" />
                </td>
              </tr>
            </tbody>
          </table>
          <div class="rodape-salao mt-4 mb-4">
            <div class="button-group">
              <Button type="button" label="Voltar" @click="; editarParceiros = false; editSalaoParceiros = true"
                class="mr-2"></Button>
              <Button type="button" label="Salvar" :disabled="salvarButtonParceiro"
                @click="submitParceiro(q203_sequencial)"></Button>
            </div>
          </div>
        </div>
      </div>
    </Dialog>

    <Dialog v-model:visible="adicionaParceiro" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
      position="top" header="Adicionar Parceiro" class="shadow-4">
      <div class="container-salao">
        <div class="children-salao">
          <table class="form-table">
            <tbody>
              <tr>
                <td><a href="javascript:void(0)" @click="abrirConsultaInscricaoParc">Inscrição Municipal:</a></td>
                <td>
                  <InputText @keyup="verificaCaracteresNovoParceiro('parceiro')"
                    @focusout="selecionaInscricaoParceiro(inscrNovoParc); verificaCampos('parceiro');" type="text"
                    v-model="inscrNovoParc" class="w-full border-black-alpha-60 border-solid border-1"
                    id="inscrNovoParceiro" />
                </td>
                <td>
                  <InputText :disabled="true" class="w-full border-black-alpha-60 border-solid border-1"
                    id="inscrNomeNovoParc" v-model="inscrNomeNovoParc" />
                </td>
              </tr>
              <tr>
                <td><a href="javascript:void(0)" @click="abrirConsultaCgmParc">CGM:</a></td>
                <td>
                  <InputText type="text" @keyup="verificaCaracteresNovoParceiro('cgm')"
                    @focusout="selecionaCgmParceiro(cgmNovoParc); verificaCampos('parceiro')" v-model="cgmNovoParc"
                    :disabled="false" class="input-field border-black-alpha-60 border-solid border-1 salao-input"
                    id="cgmNovoParceiro" />
                </td>
                <td>
                  <InputText :disabled="true" class="w-full border-black-alpha-60 border-solid border-1"
                    id="cgmNomeNovoParc" v-model="cgmNomeNovoParc" />
                </td>
              </tr>
              <tr>
                <td><label for="dataIniNovoParc">Data Inicial:* </label></td>
                <td>
                  <Calendar @focusout="verificaCampos('parceiro')" v-model="dataIniNovoParc" showIcon
                    iconDisplay="input" inputId="dataIniNovoParc" class="salao-input" />
                </td>
              </tr>
              <tr>
                <td colspan="1"><label for="obssalao">Observação: </label></td>
                <td colspan="2">
                  <InputText type="textbox" v-model="obsNovoParc" :disabled="false"
                    class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="obsNovoParc" />
                </td>
              </tr>
            </tbody>
          </table>

          <div class="rodape-salao mt-4 mb-4">
            <div class="button-group">
              <Button type="button" label="Voltar" @click="adicionaParceiro = false; editSalaoParceiros = true"
                class="mr-2"></Button>
              <Button type="button" label="Salvar" :disabled="salvarButton" @click="submitNovoParc(); isLoading = true"></Button>
            </div>
          </div>
        </div>
      </div>
    </Dialog>



    <Dialog v-model:visible="selecionaInscrByCgm" :maximizable="false" :modal="true" :closable="false"
    :style="{ width: '500px' }" position="top" header="Selecione a Inscricão" class="shadow-4">
    <div class="container-salao">
      <div class="children-salao">
        <table class="table-atividades mt-3" id="tableAtividades">
          <thead>
            <th>Inscrição</th>
          </thead>
          <tbody>
            <tr v-for="(inscrCgm, index) in inscricaoCgm" :key="inscrCgm.q02_inscr"
              @click="selecionaInscrByOpc(inscrCgm.q02_inscr); selecionaInscrByCgm = false;"
              :class="index % 2 === 0 ? 'linha-par' : 'linha-impar'" style="cursor: pointer;">
              <td>{{ inscrCgm.q02_inscr }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </Dialog>
    <DialogConsultaCgm ref="dialogConsultaCgm" @selectRow="selecionaCgmParceiro" />
    <DialogConsultaInscricaoMunicipal ref="dialogConsultaInscricaoMunicipalParc"
      @selectRow="selecionaInscricaoParceiro" />
    <ModalLoading :isLoading="isLoading"></ModalLoading>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import Fieldset from 'primevue/fieldset';
import InputText from 'primevue/inputtext';
import DialogConsultaSalaoParceiro from './components/DialogConsultaSalaoParceiro.vue';
import DialogConsultaCgm from '@modules/Patrimonial/Protocolo/Components/DialogConsultaCgm.vue';
import DialogConsultaInscricaoMunicipal from '../Arrecadacao/components/DialogConsultaInscricaoMunicipal.vue';
import ModalLoading from '@modules/Components/ModalLoading.vue';

const isLoading = ref(false);
const toast = useToast();
const dialogConsultaSalaoParceiro = ref(null);
const dialogConsultaCgm = ref(null);
const dialogConsultaInscricaoMunicipalParc = ref(null);
const continuarButton = ref(true);
const reqAnterior = ref(null);
const inscri = ref(null);
const selecionaAtividade = ref(false);
const editSalaoParceiros = ref(false);
const editarParceiros = ref(false);
const z01_nomeValue = ref(null);
const atividades = ref([]);
const editSalao = ref([]);
const editParceiros = ref([]);
const datainiSalao = ref(null);
const dataIniSalaoAnterior = ref(null);
const dataFimSalaoAnterior = ref(null);
const q202_obs = ref('');
const q202_inscr = ref(null);
const dataFinalSalao = ref('');
const cgmparc = ref(null);
const q203_obs = ref('');
const q203_numcgm = ref(null);
const q203_inscrparc = ref(null);
const q203_dtinicialAnterior = ref(null);
const q203_dtfinalAnterior = ref(null);
const q203_dtinicial = ref(null);
const q203_dtfinal = ref('');
const q202_sequencial = ref(null);
const q203_sequencial = ref(null);
const salvarButton = ref(false);
const salvarButtonParceiro = ref(false);
const isDataIniDisabled = ref(false);
const isDataFimDisabled = ref(false);
const isObsDisabled = ref(false);
const adicionaParceiro = ref(false);
const addParceiroButton = ref(false);
const cgmNovoParc = ref(null);
const inscrNovoParc = ref(null);
const cgmNomeNovoParc = ref(null);
const inscrNomeNovoParc = ref(null);
const dataIniNovoParc = ref(null);
const obsNovoParc = ref('');
const reqAnteriorParc = ref(null);
const reqAnteriorCGMParc = ref(null);
const selecionaInscrByCgm = ref(false);
const inscricaoCgm = ref([]);

function throwToast(severity = "warn", summary = "Atenção", detail = "Atenção", life = 3000) {
  toast.add({ severity, summary, detail, life });
}

function verificaCaracteres() {
  const semLetras = inscri.value.replace(/[^0-9]/g, '');
  if (semLetras !== inscri.value) {
    inscri.value = semLetras;
    throwToast("warn", "Atenção", "Apenas números são permitidos.");
  }
}

function verificaCaracteresNovoParceiro(tipo) {
  if (tipo == 'parceiro' && typeof inscrNovoParc.value === 'string' && inscrNovoParc.value.trim() !== '') {
    let semLetras = inscrNovoParc.value.replace(/[^0-9]/g, '');
    if (semLetras !== inscrNovoParc.value) {
      inscrNovoParc.value = semLetras;
      throwToast("warn", "Atenção", "Apenas números são permitidos.", 3000);
    }
  } else if (tipo == 'cgm' && typeof cgmNovoParc.value === 'string' && cgmNovoParc.value.trim() !== '') {
    let semLetras = cgmNovoParc.value.replace(/[^0-9]/g, '');
    if (semLetras !== cgmNovoParc.value) {
      cgmNovoParc.value = semLetras;
      throwToast("warn", "Atenção", "Apenas números são permitidos.", 3000);
    }
  }

}


function verificaDateParceiro() {
  var dataAtual = new Date().toISOString().split('T')[0];
  var dataParc = new Date(dataIniNovoParc.value).toISOString().split('T')[0];

  if(datainiSalao.value !== dataIniSalaoAnterior.value){
    var dataSalao = new Date(datainiSalao.value).toISOString().split('T')[0];
  }else{
    const dataIniSlipt = datainiSalao.value.split("/");
    var dataSalao = `${dataIniSlipt[2]}-${dataIniSlipt[1]}-${dataIniSlipt[0]}`;
  }
  
  if (dataParc < dataSalao) {
    throwToast("warn", "Atenção", "Data Inicial do Parceiro deve ser maior ou igual a data inicial do Salão.", 3000);
    salvarButton.value = true;
  } else if (dataParc > dataAtual) {
    throwToast("warn", "Atenção", "Data Inicial do Parceiro deve ser menor ou igual a data atual.", 3000);
    salvarButton.value = true;
  } else {
    salvarButton.value = false;
  }
}

function verificaCampos(tipo) {
  if (tipo == 'parceiro') {
    if ((cgmNovoParc.value !== '' && inscrNovoParc.value !== '')
      && dataIniNovoParc.value !== null
      && (cgmNomeNovoParc.value.startsWith('CGM ') == false && inscrNomeNovoParc.value.startsWith('inscrição ') == false)
    ) {
      verificaDateParceiro();
    } else {
      salvarButton.value = true;
    }
  }
}

function abrirConsultaInscricao() {
  dialogConsultaSalaoParceiro.value?.toggleDialog();
}

function selecionaInscricao(result) {
  inscri.value = result?.inscricao || result;
  validaInscri();
}

async function validaInscri() {
  const q02_inscr = inscri.value;

  if (q02_inscr === reqAnterior.value) {
    return false;
  }

  reqAnterior.value = q02_inscr;
  if (q02_inscr) {
    isLoading.value = true;
    const retorno = await requisitaInscr(q02_inscr);
    isLoading.value = false;

    if (retorno !== 'vazio') {
      z01_nomeValue.value = retorno['cgm_salao'];
      continuarButton.value = false;
    } else {
      throwToast("warn", "Atenção", `Inscrição (${q02_inscr}) Não é Salão Parceiro.`);
      z01_nomeValue.value = `Inscrição (${q02_inscr}) Não é Salão Parceiro.`;
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
    const response = await window.axios.post('v4/api/tributario/issqn/get-salao-parceiro', parametros, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data) {
      return response.data
    } else {
      return 'vazio'
    }
  } catch (error) {
    isLoading.value = false;
    throwToast("error", "Erro", "Houve um erro ao buscar a inscrição");
    return false;
  }
}

async function verificaAtividades() {
  isLoading.value = true;
  if (inscri.value) {
    atividades.value = [];
    const retorno = await buscaAtividades();
    isLoading.value = false;

    if (retorno !== 'vazio') {
      atividades.value = retorno;
      selecionaAtividade.value = true;
    }
  }
}

async function buscaAtividades() {
  const parametros = new FormData();
  parametros.append('q202_inscr', inscri.value);

  try {
    const response = await window.axios.post('v4/api/tributario/issqn/get-all', parametros, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    return response.data.data || 'vazio';
  } catch (error) {
    isLoading.value = false;
    throwToast("error", "Erro", "Houve um erro ao buscar as Atividades");
    return false;
  }
}

function selecionaSalaoParceiro(sequencial) {
  if (sequencial) {
    const maiorSequencial = Math.max(...atividades.value.map(atividade => atividade.q202_sequencial));
    limpaCampos('salao');
    editSalao.value = atividades.value.filter(edit => edit.q202_sequencial === sequencial);
    selecionaAtividade.value = false;
    editSalaoParceiros.value = true;
    datainiSalao.value = editSalao.value[0].q202_dtinicial;
    dataIniSalaoAnterior.value = editSalao.value[0].q202_dtinicial;
    dataFimSalaoAnterior.value = editSalao.value[0].q202_dtfinal;
    q202_obs.value = editSalao.value[0].q202_obs;
    q202_inscr.value = editSalao.value[0].q202_inscr + ' - ' + atividades.value[0].cgm_salao;
    dataFinalSalao.value = editSalao.value[0].q202_dtfinal;
    if (dataFinalSalao.value !== "") {
      addParceiroButton.value = true;
      salvarButtonParceiro.value = true;
    } else {
      addParceiroButton.value = false;
      salvarButtonParceiro.value = false;
    }
    if (maiorSequencial === sequencial) {
      salvarButton.value = false;
      isDataFimDisabled.value = false;
      isDataIniDisabled.value = false;
      isObsDisabled.value = false;
    } else {
      salvarButton.value = true;
      isDataFimDisabled.value = true;
      isDataIniDisabled.value = true;
      isObsDisabled.value = true;
    }
  }
}
function selecionaParceiro(seqParceiro) {
  if (seqParceiro) {
    limpaCampos('parceiro');
    editParceiros.value = editSalao.value[0].parceiros.filter(edit => edit.q203_sequencial === seqParceiro);
    editarParceiros.value = true;
    editSalaoParceiros.value = false;
    q203_sequencial.value = editParceiros.value[0].q203_sequencial;
    cgmparc.value = editParceiros.value[0].cgm_parceiro;
    q203_obs.value = editParceiros.value[0].q203_obs;
    q203_numcgm.value = editParceiros.value[0].q203_numcgm;
    q203_inscrparc.value = editParceiros.value[0].q203_inscrparc;
    q203_dtinicial.value = editParceiros.value[0].q203_dtinicial;
    q203_dtfinal.value = editParceiros.value[0].q203_dtfinal;
    q203_dtfinalAnterior.value = editParceiros.value[0].q203_dtfinal;
    q203_dtinicialAnterior.value = editParceiros.value[0].q203_dtinicial;
  }
}

function contarParceiros(parceiros) {
  return parceiros ? parceiros.length : 0;
}

async function submitSalao(sequencial) {
  let ultimaDataFinal;
  const primeiraDataParc = editSalao.value[0].parceiros.find(
    edit => edit.q203_sequencial === Math.min(...editSalao.value[0].parceiros.map(edit => edit.q203_sequencial))
  );

  //Filta o ultimo sequencial que tem a data final preenchida para validar a data final do salao
  const filtraParceiroValidos = editSalao.value[0].parceiros
    .filter(edit => edit.q203_dtfinal !== "");

  if (filtraParceiroValidos.length > 0) {
    ultimaDataFinal = filtraParceiroValidos.length > 0
      ? filtraParceiroValidos.reduce((max, edit) => (edit.q203_sequencial > max.q203_sequencial ? edit : max))
      : null;
  }
  //*****************************************************************/

  const parametros = new FormData();
  let dataIni;
  let dataFim;
  //Valida se a data inicial do Salão é maior que a data inicial do parceiro
  if (datainiSalao.value !== dataIniSalaoAnterior.value) {
    const [dia, mes, ano] = primeiraDataParc.q203_dtinicial.split('/');
    const dataIniParc = new Date(ano, mes - 1, dia);
    if (datainiSalao.value.toISOString().split('T')[0] > dataIniParc.toISOString().split('T')[0]) {
      throwToast("error", "Erro", "A data inicial deve ser menor ou igual a data: " + primeiraDataParc.q203_dtinicial);
      return false;
    } else {
      dataIni = datainiSalao.value.toISOString().split('T')[0];
    }
  } else if (datainiSalao.value == dataIniSalaoAnterior.value) {
    const dataIniSlipt = datainiSalao.value.split("/");
    dataIni = `${dataIniSlipt[2]}-${dataIniSlipt[1]}-${dataIniSlipt[0]}`;
  }

  if (dataFinalSalao.value !== dataFimSalaoAnterior.value && dataFinalSalao.value !== null) {
    if (ultimaDataFinal) {
      const [dia, mes, ano] = ultimaDataFinal.q203_dtfinal.split('/');
      const dataFimParc = new Date(ano, mes - 1, dia);
      if (dataFinalSalao.value.toISOString().split('T')[0] < dataFimParc.toISOString().split('T')[0]) {
        throwToast("error", "Erro", "A data final deve ser maior ou igual a data: " + ultimaDataFinal.q203_dtfinal);
        return false;
      }
    } else {
      dataFim = dataFinalSalao.value.toISOString().split('T')[0];
      parametros.append('q202_dtfinal', dataFim);
    }
  } else if (dataFinalSalao.value == dataFimSalaoAnterior.value) {
    dataFim = dataFinalSalao.value
    parametros.append('q202_dtfinal', dataFim);
  }

  parametros.append('q202_sequencial', sequencial);
  parametros.append('q202_dtinicial', dataIni);

  parametros.append('q202_obs', q202_obs.value);
  isLoading.value = true;
  try {
    const response = await window.axios.post(
      'v4/api/tributario/issqn/update-salao',
      parametros,
      {
        headers: {
          'Content-Type': 'multipart/form-data; charset=UTF-8'
        }
      }
    );

    isLoading.value = false;
    if (response.data === 'salaoAtualizado') {
      throwToast("success", "Sucesso", "Salão atualizado com sucesso");
      selecionaAtividade.value = false;
      editSalaoParceiros.value = false;
      verificaAtividades();
    } else {
      throwToast("error", "Erro", "Houve um erro ao salvar os dados", 5000);
      limpaCampos('salao');
      limpaCampos('parceiro');
      selecionaAtividade.value = false;
      editSalaoParceiros.value = false;
      editarParceiros.value = false;
    }
  } catch (error) {
    isLoading.value = false;
    throwToast("error", "Erro", "Houve um erro ao salvar os dados", 5000);
    return false;
  }
}


async function submitParceiro(sequencial) {
  if (sequencial) {
    const parametros = new FormData();
    let dataIni;
    let dataFim;
    const [dia, mes, ano] = datainiSalao.value.split('/');
    const dataSalao = new Date(ano, mes - 1, dia).toISOString().split('T')[0];
    if (q203_dtinicial.value !== q203_dtinicialAnterior.value) {

      if (q203_dtinicial.value.toISOString().split('T')[0] < dataSalao) {
        throwToast("error", "Erro", "A data inicial deve ser maior ou igual a data: " + datainiSalao.value);
        return false;
      } else {
        dataIni = q203_dtinicial.value.toISOString().split('T')[0];
        parametros.append('q203_dtinicial', dataIni);
      }
    } else {
      const dataIniSlipt = q203_dtinicial.value.split("/");
      dataIni = `${dataIniSlipt[2]}-${dataIniSlipt[1]}-${dataIniSlipt[0]}`;
      parametros.append('q203_dtinicial', dataIni);
    }

    if (q203_dtfinal.value !== q203_dtfinalAnterior.value && q203_dtfinal.value !== null) {
      const [dia, mes, ano] = q203_dtinicial.value.split('/');
      const q203_dtini = new Date(ano, mes - 1, dia).toISOString().split('T')[0];
      if (q203_dtfinal.value.toISOString().split('T')[0] < q203_dtini) {
        throwToast("error", "Erro", "A data final deve ser maior ou igual a data: " + q203_dtinicial.value);
        return false;
      } else {
        dataFim = q203_dtfinal.value.toISOString().split('T')[0];
        parametros.append('q203_dtfinal', dataFim);
      }
    }
    parametros.append('q203_sequencial', sequencial);
    parametros.append('q203_obs', q203_obs.value);
    isLoading.value = true;
    try {
      const response = await window.axios.post(
        'v4/api/tributario/issqn/update-parceiro',
        parametros,
        {
          headers: {
            'Content-Type': 'multipart/form-data; charset=UTF-8'
          }
        }
      );

      isLoading.value = false;
      if (response.data === 'parceiroAtualizado') {
        throwToast("success", "Sucesso", "Parceiro atualizado com sucesso");
        selecionaAtividade.value = false;
        editSalaoParceiros.value = false;
        editarParceiros.value = false;
        verificaAtividades();
      } else {
        throwToast("error", "Erro", "Houve um erro ao salvar os dados", 5000);
        limpaCampos('salao');
        limpaCampos('parceiro');
        selecionaAtividade.value = false;
        editSalaoParceiros.value = false;
        editarParceiros.value = false;
      }


    } catch (error) {
      isLoading.value = false;
      throwToast("error", "Erro", "Houve um erro ao salvar os dados", 5000);
      return false;
    }
  }
}

async function verificaNovoParceiro(tipo) {
  if (tipo == 'inscrParceiro') {
    //PARCEIROS
    const q02_inscr = inscrNovoParc.value;

    if (q02_inscr === reqAnteriorParc.value) {
      return false;
    }
    reqAnteriorParc.value = q02_inscr;
    if (q02_inscr == reqAnterior.value) {
      throwToast("warn", "Atenção", "Parceiro Não pode ser igual ao Salão parceiro", 5000);
      inscrNomeNovoParc.value = 'Parceiro Não pode ser igual ao Salão.';
      cgmNomeNovoParc.value = '';
      cgmNovoParc.value = '';
      return false;
    }
    if (q02_inscr !== null && q02_inscr !== '') {
      const retorno = await requisitaParc('inscri', q02_inscr);
      if (retorno !== 'vazio' && retorno.length > 0) {
        if (retorno[0]['q02_dtbaix'] == null) {
          if (retorno[0]['q38_categoria'] != null) {
            cgmNomeNovoParc.value = retorno[0]['z01_nome'];
            cgmNovoParc.value = retorno[0]['z01_numcgm'];
            inscrNovoParc.value = retorno[0]['q02_inscr'];
            inscrNomeNovoParc.value = retorno[0]['z01_nome'];
          } else {
            inscrNomeNovoParc.value = '';
            throwToast("warn", "Atenção", "inscrição precisa estar cadastrada como MEI", 5000);
            inscrNomeNovoParc.value = 'inscrição Não é MEI';
            cgmNovoParc.value = '';
            cgmNomeNovoParc.value = '';
            verificaCampos('parceiro');
          }
        } else {
          throwToast("warn", "Atenção", "inscrição já Baixada", 3000);
          inscrNomeNovoParc.value = 'inscrição (' + q02_inscr + ') já Baixada';
          cgmNomeNovoParc.value = '';
          cgmNovoParc.value = '';
          verificaCampos('parceiro');
        }
      } else {
        throwToast("warn", "Atenção", 'inscrição (' + q02_inscr + ') Não Encontrada', 3000);
        inscrNomeNovoParc.value = 'inscrição (' + q02_inscr + ') Não Encontrada';
        cgmNomeNovoParc.value = '';
        cgmNovoParc.value = '';
        verificaCampos('parceiro')
      }
    } else {
      verificaCampos('parceiro');
      inscrNomeNovoParc.value = '';
      cgmNomeNovoParc.value = '';
      cgmNovoParc.value = '';
      reqAnteriorParc.value = null;
    }
  } else if (tipo == 'cgmParceiro') {
    const numcgm = cgmparc.value;

    if (numcgm === reqAnteriorCGMParc.value) {
      return false;
    }
    reqAnteriorCGMParc.value = numcgm;

    if (numcgm !== null && numcgm !== '') {
      const retorno = await requisitaParc('cgm', numcgm);
      if (retorno !== 'vazio') {

        if (retorno.length > 1) {
          inscricaoCgm.value = [];
          inscricaoCgm.value = retorno;
          selecionaInscrByCgm.value = true;
        } else if (retorno[0]['q02_dtbaix'] == null) {

          if (retorno[0]['q02_inscr'] == reqAnterior.value) {
            throwToast("warn", "Atenção", "Parceiro não pode ser igual ao salão parceiro", 5000);
            cgmNomeNovoParc.value = "Parceiro Não pode ser igual ao salão parceiro";
            inscrNovoParc.value = '';
            inscrNomeNovoParc.value = '';
            return false;
          }

          if (retorno[0]['q38_categoria'] != null) {
            cgmNomeNovoParc.value = retorno[0]['z01_nome'];
            inscrNovoParc.value = retorno[0]['q02_inscr'];
            inscrNomeNovoParc.value = retorno[0]['z01_nome'];
            verificaCampos('parceiro');
          } else {
            cgmNomeNovoParc.value = '';
            throwToast("warn", "Atenção", "CGM precisa estar cadastrado como MEI", 5000);
            cgmNomeNovoParc.value = 'CGM não é MEI';
            inscrNovoParc.value = '';
            inscrNomeNovoParc.value = '';
            verificaCampos('parceiro');
          }
        } else {
          throwToast("warn", "Atenção", "CGM já Baixado", 3000);
          cgmNomeNovoParc.value = 'CGM (' + numcgm + ') já Baixado';
          inscrNovoParc.value = '';
          inscrNomeNovoParc.value = '';
          verificaCampos('parceiro');
        }
      } else {
        throwToast("warn", "Atenção", 'CGM (' + numcgm + ') Não Encontrado', 3000);
        cgmNomeNovoParc.value = 'CGM (' + numcgm + ') Não Encontrado';
        inscrNovoParc.value = '';
        inscrNomeNovoParc.value = '';
        verificaCampos('parceiro');
      }
    } else {
      verificaCampos('parceiro');
      cgmNomeNovoParc.value = '';
      inscrNovoParc.value = '';
      inscrNomeNovoParc.value = '';
      reqAnteriorCGMParc.value = null;

    }
  } else {
    throwToast("error", "Erro", "Houve um erro ao buscar a inscrição", 5000);
    return false;
  }
}


async function requisitaParc(tipo, dados) {
  const parametros = new FormData();
  switch (tipo) {
    case 'inscri':
      parametros.append('inscricao', dados);
      break;
    case 'cgm':
      parametros.append('cgm', dados);
      break;
    default:
      break;
  }
  try {
    const response = await window.axios.post(
      'v4/api/tributario/issqn/buscar-parceiro',
      parametros,
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    );

    const data = response.data.data;
    if (data) {
      return (data);
    } else {
      return 'vazio';
    }
  } catch (error) {
    throwToast("error", "Erro", "Houve um erro ao buscar a inscrição", 5000);
    return false;
  }
}

async function submitNovoParc() {
  const parametros = new FormData();
  const dataParc = dataIniNovoParc.value.toISOString().split('T')[0];
  if (datainiSalao.value == null) {
    datainiSalao.value = new Date();
  }
  const dataIniSlipt = datainiSalao.value.split("/");
  let dataIni = `${dataIniSlipt[2]}-${dataIniSlipt[1]}-${dataIniSlipt[0]}`;
  parametros.append('q202_inscr', inscri.value);
  parametros.append('q202_dtinicial', dataIni);
  parametros.append('q202_obs', q202_obs.value);
  parametros.append('q203_inscrparc', inscrNovoParc.value);
  parametros.append('q203_numcgm', cgmNovoParc.value);
  parametros.append('q203_dtinicial', dataParc);
  parametros.append('q203_obs', obsNovoParc.value);

  try {
    const response = await window.axios.post(
      'v4/api/tributario/issqn/save-salao-parceiro',
      parametros,
      {
        headers: {
          'Content-Type': 'multipart/form-data; charset=UTF-8'
        }
      }
    );

    const data = response.data.data;
    isLoading.value = false;
    if (data == 'inserido') {
      throwToast("success", "Sucesso", "Parceiro inserido com sucesso!", 5000);
      limpaCampos('novoParc');
      adicionaParceiro.value = false;
      selecionaAtividade.value = false;
      editSalaoParceiros.value = false;
      verificaAtividades();
    } else if (data == 'existe') {
      throwToast("warn", "Atenção", "Parceiro já cadastrado para esta inscrição", 5000);
      limpaCampos('novoParc');
      adicionaParceiro.value = false;
      selecionaAtividade.value = false;
      editSalaoParceiros.value = false;
      verificaAtividades();
    } else if (data == 'erro') {
      throwToast("error", "Erro", "Houve um erro ao salvar os dados", 5000);
      limpaCampos('novoParc');
      adicionaParceiro.value = false;
      selecionaAtividade.value = false;
      editSalaoParceiros.value = false;
      verificaAtividades();
    } else if (data == 'dataSalaoInvalida') {
      throwToast("error", "Erro", "Data de inicio do Salão Não pode ser menor que a ultima data final.", 7000);
    } else if (data == 'dataParceiroInvalida') {
      throwToast("error", "Erro", "Data de inicio do Parceiro Não pode ser menor que a ultima data final.", 7000);
    }
  } catch (error) {
    throwToast("error", "Erro", "Houve um erro ao salvar os dados", 5000);
    return false;
  }
}

function abrirConsultaInscricaoParc() {
  if (dialogConsultaInscricaoMunicipalParc.value) {
    dialogConsultaInscricaoMunicipalParc.value.toggleDialog();
  }
}

function abrirConsultaCgmParc() {
  dialogConsultaCgm.value.toggleDialog();
}

function selecionaCgmParceiro(result) {
  if (result) {
    if (result.numcgm) {
      cgmNovoParc.value = result.numcgm;
    } else {
      cgmNovoParc.value = result;
    }
  }
  verificaNovoParceiro('cgmParceiro');
}

function selecionaInscricaoParceiro(result) {
  if (result) {
    if (result.inscricao) {
      inscrNovoParc.value = result.inscricao;
    } else {
      inscrNovoParc.value = result;
    }
  }
  verificaNovoParceiro('inscrParceiro');
}

function limpaCampos(tipo) {
  if (tipo == 'salao') {
    editSalao.value = [];
    q202_inscr.value = '';
    datainiSalao.value = '';
    q202_obs.value = '';
    dataFinalSalao.value = '';
  } else if (tipo == 'parceiro') {
    editParceiros.value = [];
    q203_sequencial.value = '';
    cgmparc.value = '';
    q203_obs.value = '';
    q203_numcgm.value = '';
    q203_inscrparc.value = '';
    q203_dtinicial.value = '';
    q203_dtfinal.value = '';
  }else if (tipo == 'novoParc'){
    inscrNomeNovoParc.value = '';
    inscrNovoParc.value = '';
    dataIniNovoParc.value = '';
    obsNovoParc.value = '';
    cgmNomeNovoParc.value = '';
    cgmNovoParc.value = '';
  }
}


function limpaCamposNovoParceiro(){
  inscrNomeNovoParc.value = '';
  inscrNovoParc.value = '';
  dataIniNovoParc.value = '';
  obsNovoParc.value = '';
  cgmNomeNovoParc.value = '';
  cgmNovoParc.value = '';
  reqAnteriorParc.value = '';
  salvarButton.value = true;
}

</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.card {
  padding: 20px;
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


.label {
  font-weight: bold;
  text-decoration: underline;
}

.flex {
  display: flex;
  justify-content: center;
}

.linha-par {
  background-color: white;
}

.linha-impar {
  background-color: whitesmoke;
}

.form-table {
  width: 100%;
  border-collapse: collapse;
}

.form-table td {
  padding: 10px;
}

.rodape-salao {
  text-align: center;
  padding-top: 20px;
}

.button-group {
  display: inline-flex;
  gap: 10px;
}

.container-salao {
  padding: 20px;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
}

.children-salao {
  padding: 20px;
  width: 97%;
  max-width: 1000px;
  height: 95%;
  border: solid grey 1px;
  box-shadow: 0px 0px 15px -1px rgba(0, 0, 0, 0.75);
  border-radius: 5px;
}

.salao-input {
  width: 100%;
}
</style>
