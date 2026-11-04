<template>
  <div class="container">
    <div>
      <div class="card w-100">
        <div class="container w-full">
          <Fieldset legend="Salão Parceiro">
            <div>
              <a href="javascript:void(0)" @click="abrirConsultaInscricao">Inscrição Municipal:</a>
              <InputText @keyup="verificaCaracteres('salao')" @focusout="selecionaInscricao(inscri);" type="text"
                v-model="inscri" class="w-2 ml-2 border-black-alpha-60 border-solid border-1" id="q02_inscr" />
              <InputText :disabled="true" class="w-8 ml-2 border-black-alpha-60 border-solid border-1" id="z01_nome"
                v-model="z01_nomeValue" />
            </div>
            <div class="flex">
              <Button type="button" id="pesquisar" value="Pesquisar" label="Continuar" class="m-auto mt-3 shadow-4"
                :disabled="continuarButton" @click="verificaRedundancia()" />
            </div>
          </Fieldset>
          <DialogConsultaInscricaoMunicipal ref="dialogConsultaInscricaoMunicipal" @selectRow="selecionaInscricao" />
        </div>
      </div>

      <Dialog v-model:visible="priemiraaba" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
        position="top" header="Salão Parceiro" class="shadow-4">
        <div class="container-salao">
          <div class="children-salao">
            <table class="form-table">
              <tbody>
                <tr>
                  <td><label for="q02_inscr">Inscrição Municipal:* </label></td>
                  <td>
                    <InputText type="text" v-model="inscri" :disabled="true"
                      class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="q02_inscr" />
                  </td>
                </tr>
                <tr>
                  <td><label for="datainiSalao">Data Inicial:* </label></td>
                  <td>
                    <Calendar @focusout="verificaCampos('salao')" v-model="datainiSalao" showIcon iconDisplay="input"
                      inputId="datainiSalao" class="salao-input" />
                  </td>
                </tr>
                <tr>
                  <td><label for="obssalao">Observação: </label></td>
                  <td>
                    <InputText type="textbox" v-model="q202_obs" :disabled="false"
                      class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="obssalao" />
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="rodape-salao mt-4 mb-4">
              <div class="button-group">
                <Button type="button" label="Voltar" @click="priemiraaba = false" class="mr-2"></Button>
                <Button type="button" label="Avancar" @click="segundaaba = true; priemiraaba = false"
                  :disabled="avancarButton"></Button>
              </div>
            </div>
          </div>
        </div>
      </Dialog>


      <Dialog v-model:visible="segundaaba" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
        position="top" header="Parceiros" class="shadow-4">
        <div class="container-salao">
          <div class="children-salao">
            <table class="form-table">
              <tbody>
                <tr>
                  <td><a href="javascript:void(0)" @click="abrirConsultaInscricaoParc">Inscrição Municipal:</a></td>
                  <td>
                    <InputText @keyup="verificaCaracteres('parceiro')"
                      @focusout="selecionaInscricaoParceiro(inscriparc); verificaCampos('parceiro');" type="text"
                      v-model="inscriparc" class="w-full border-black-alpha-60 border-solid border-1" id="q02_inscr" />
                  </td>
                  <td>
                    <InputText :disabled="true" class="w-full border-black-alpha-60 border-solid border-1"
                      id="z01_nomeParc" v-model="z01_nomeValueParc" />
                  </td>
                </tr>
                <tr>
                  <td><a href="javascript:void(0)" @click="abrirConsultaCgm">CGM:</a></td>
                  <td>
                    <InputText type="text" @keyup="verificaCaracteres('cgm')"
                      @focusout="selecionaCgm(cgmparc); verificaCampos('parceiro')" v-model="cgmparc" :disabled="false"
                      class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="cgmparceiro" />
                  </td>
                  <td>
                    <InputText :disabled="true" class="w-full border-black-alpha-60 border-solid border-1"
                      id="cgmValueParc" v-model="cgmValueParc" />
                  </td>
                </tr>
                <tr>
                  <td><label for="datainiParc">Data Inicial:* </label></td>
                  <td>
                    <Calendar @focusout="verificaCampos('parceiro')" v-model="datainiParc" showIcon iconDisplay="input"
                      inputId="datainiParc" class="salao-input" />
                  </td>
                </tr>
                <tr>
                  <td colspan="1"><label for="obssalao">Observação: </label></td>
                  <td colspan="2">
                    <InputText type="textbox" v-model="q203_obs" :disabled="false"
                      class="input-field border-black-alpha-60 border-solid border-1 salao-input" id="obssalao" />
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="rodape-salao mt-4 mb-4">
              <div class="button-group">
                <Button type="button" label="Voltar" @click="verificaRedundancia" class="mr-2"></Button>
                <Button type="button" label="Salvar" @click="segundaaba = false; submitForm(); isLoading = true"
                  :disabled="salvarButton"></Button>
              </div>
            </div>
          </div>
        </div>
        <DialogConsultaCgm ref="dialogConsultaCgm" @selectRow="selecionaCgm" />
        <DialogConsultaInscricaoMunicipal ref="dialogConsultaInscricaoMunicipalParc"
          @selectRow="selecionaInscricaoParceiro" />
      </Dialog>
    </div>
  </div>
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
  <ModalLoading :isLoading="isLoading"></ModalLoading>
</template>


<script setup>
import { ref, watch } from 'vue';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import Fieldset from 'primevue/fieldset';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import DialogConsultaCgm from '@modules/Patrimonial/Protocolo/Components/DialogConsultaCgm.vue';
import DialogConsultaInscricaoMunicipal from '../Arrecadacao/components/DialogConsultaInscricaoMunicipal.vue';
import ModalLoading from '@modules/Components/ModalLoading.vue';
import { useToast } from "primevue/usetoast";
import axios from 'axios';
import { PrimeIcons } from 'primevue/api';

const isLoading = ref(false);
const toast = useToast();
const dialogConsultaInscricaoMunicipal = ref(null);
const dialogConsultaInscricaoMunicipalParc = ref(null);
const dialogConsultaCgm = ref(null);
const z01_nomeValue = ref('');
const z01_nomeValueParc = ref('');
const verificaCadastro = ref(null);
const cgmValueParc = ref('');
const continuarButton = ref(true);
const avancarButton = ref(true);
const salvarButton = ref(true);
const inscri = ref(null);
const inscriparc = ref(null);
const reqAnterior = ref(null);
const reqAnteriorParc = ref(null);
const reqAnteriorCGMParc = ref(null);
const priemiraaba = ref(false);
const segundaaba = ref(false);
const datainiSalao = ref(null);
const datainiParc = ref(null);
const q202_obs = ref('');
const q203_obs = ref('');
const cgmparc = ref(null);
const selecionaInscrByCgm = ref(false);
const inscricaoCgm = ref([]);
const q07_datain = ref(null);

function throwToast(severity = "warn", summary = "Atenção", detail = "Atenção", life = 3000) {
  toast.add({
    severity: severity,
    summary: summary,
    detail: detail,
    life: life,
  });
}
function verificaCaracteres(tipo) {
  if (tipo == 'salao') {
    let semLetras = inscri.value.replace(/[^0-9]/g, '');
    if (semLetras !== inscri.value) {
      inscri.value = semLetras;
      throwToast("warn", "Atenção", "Apenas números são permitidos.", 3000);
    }
  } else if (tipo == 'parceiro') {
    let semLetras = inscriparc.value.replace(/[^0-9]/g, '');
    if (semLetras !== inscriparc.value) {
      inscriparc.value = semLetras;
      throwToast("warn", "Atenção", "Apenas números são permitidos.", 3000);
    }
  } else if (tipo == 'cgm') {
    let semLetras = cgmparc.value.replace(/[^0-9]/g, '');
    if (semLetras !== cgmparc.value) {
      cgmparc.value = semLetras;
      throwToast("warn", "Atenção", "Apenas números são permitidos.", 3000);
    }
  }

}

function verificaCampos(tipo) {
  if (tipo == 'salao') {
    if (datainiSalao.value !== null) {
      verificaDateSalao();
    } else {
      avancarButton.value = true;
    }
  } else if (tipo == 'parceiro') {
    if ((cgmparc.value !== '' && inscriparc.value !== '')
      && datainiParc.value !== null
      && (cgmValueParc.value.startsWith('CGM ') == false && z01_nomeValueParc.value.startsWith('inscrição ') == false)
    ) {
      verificaDateParceiro();
    } else {
      salvarButton.value = true;
    }
  }
}


function verificaDateSalao() {
  var dataAtual = new Date().toISOString().split('T')[0];
  var dataSalao = new Date(datainiSalao.value).toISOString().split('T')[0];
  var dataIniAtividade = new Date(q07_datain.value).toISOString().split('T')[0];
  if (dataSalao > dataAtual) {
    throwToast("warn", "Atenção", "Data Inicial do Salão deve ser menor ou igual a data atual.", 3000);
    avancarButton.value = true;
  } else if(dataSalao < dataIniAtividade){
    dataIniAtividade = dataIniAtividade.split('-').reverse().join('/');
    throwToast("warn", "Atenção", "Data Inicial do Salão deve ser maior ou igual a data da Início da Atividade ( " + dataIniAtividade + " ).", 3000);
    avancarButton.value = true;

  }else {
    avancarButton.value = false;
  }
}


function verificaDateParceiro() {
  var dataAtual = new Date().toISOString().split('T')[0];
  var dataParc = new Date(datainiParc.value).toISOString().split('T')[0];
  var dataSalao = new Date(datainiSalao.value).toISOString().split('T')[0];
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

function verificaRedundancia() {
  if (verificaCadastro.value['q202_dtinicial'] !== null && verificaCadastro.value['q202_dtfinal'] !== null && verificaCadastro.value['q202_sequencial'] !== null) {
    priemiraaba.value = true;
    segundaaba.value = false;
  } else if (verificaCadastro.value['q202_dtinicial'] == null && verificaCadastro.value['q202_dtfinal'] == null && verificaCadastro.value['q202_sequencial'] == null) {
    priemiraaba.value = true;
    segundaaba.value = false;
  } else if (verificaCadastro.value['q202_dtinicial'] !== null && verificaCadastro.value['q202_dtfinal'] == null && verificaCadastro.value['q202_sequencial'] !== null) {
    priemiraaba.value = false;
    if (segundaaba.value == true) {
      segundaaba.value = false;
    } else {
      segundaaba.value = true;
    }
  }
}

async function verificaInscr(tipo) {
  if (tipo == 'salao') {
    //SALAO
    const q02_inscr = inscri.value;

    if (q02_inscr === reqAnterior.value) {
      return false;
    }
    reqAnterior.value = q02_inscr;
    if (q02_inscr !== null && q02_inscr !== '') {
      isLoading.value = true;
      const retorno = await requisitaInscr(q02_inscr);
      isLoading.value = false;
      if (retorno !== 'vazio') {
        if (retorno['q02_dtbaix'] != null) {
          z01_nomeValue.value = '';
          throwToast("warn", "Atenção", "inscrição já Baixada", 3000);
          z01_nomeValue.value = 'inscrição (' + q02_inscr + ') já Baixada';
          verificaCadastro.value = null;
          continuarButton.value = true;
        } else if (retorno['q136_salaoparceiro'] != true) {
          verificaCadastro.value = null;
          z01_nomeValue.value = '';
          throwToast("warn", "Atenção", "Sem atividade para Salão Parceiro.", 3000);
          z01_nomeValue.value = 'Sem atividade para Salão Parceiro.';
          continuarButton.value = true;
        } else if(retorno['q38_categoria'] == null || retorno['q38_categoria'] !== 3){
          verificaCadastro.value = retorno;
          z01_nomeValue.value = retorno['z01_nome'];
          q07_datain.value = retorno['q07_datain'];
          continuarButton.value = false;
        }else if(retorno['q38_categoria'] == 3){
          verificaCadastro.value = null;
          z01_nomeValue.value = '';
          throwToast("warn", "Atenção", "Salão Parceiro não pode ser MEI.", 3000);
          z01_nomeValue.value = 'Salão Parceiro não pode ser MEI.';
          continuarButton.value = true;
        }
      } else {
        throwToast("warn", "Atenção", 'inscrição (' + q02_inscr + ') Não Encontrada', 3000);
        z01_nomeValue.value = 'inscrição (' + q02_inscr + ') Não Encontrada';
        continuarButton.value = true;
      }
    } else {
      z01_nomeValue.value = '';
      continuarButton.value = true;
    }
  } else if (tipo == 'parceiro') {
    //PARCEIROS
    const q02_inscr = inscriparc.value;

    if (q02_inscr === reqAnteriorParc.value) {
      return false;
    }
    reqAnteriorParc.value = q02_inscr;
    if (q02_inscr == reqAnterior.value) {
      throwToast("warn", "Atenção", "Parceiro Não pode ser igual ao Salão parceiro", 5000);
      z01_nomeValueParc.value = 'Parceiro Não pode ser igual ao Salão.';
      cgmValueParc.value = '';
      cgmparc.value = '';
      return false;
    }
    if (q02_inscr !== null && q02_inscr !== '') {
      const retorno = await requisitaParc('inscri', q02_inscr);
      if (retorno !== 'vazio') {
        if (retorno[0]['q02_dtbaix'] == null) {
          if (retorno[0]['q38_categoria'] != null) {
            cgmValueParc.value = retorno[0]['z01_nome'];
            cgmparc.value = retorno[0]['z01_numcgm'];
            inscriparc.value = retorno[0]['q02_inscr'];
            z01_nomeValueParc.value = retorno[0]['z01_nome'];
          } else {
            z01_nomeValueParc.value = '';
            throwToast("warn", "Atenção", "inscrição precisa estar cadastrada como MEI", 5000);
            z01_nomeValueParc.value = 'inscrição Não é MEI';
            cgmValueParc.value = '';
            cgmparc.value = '';
            verificaCampos('parceiro');
          }
        } else {
          throwToast("warn", "Atenção", "inscrição já Baixada", 3000);
          z01_nomeValueParc.value = 'inscrição (' + q02_inscr + ') já Baixada';
          cgmValueParc.value = '';
          cgmparc.value = '';
          verificaCampos('parceiro');
        }
      } else {
        throwToast("warn", "Atenção", 'inscrição (' + q02_inscr + ') Não Encontrada', 3000);
        z01_nomeValueParc.value = 'inscrição (' + q02_inscr + ') Não Encontrada';
        cgmValueParc.value = '';
        cgmparc.value = '';
        verificaCampos('parceiro')
      }
    } else {
      verificaCampos('parceiro');
      z01_nomeValueParc.value = '';
      cgmValueParc.value = '';
      cgmparc.value = '';
      reqAnteriorParc.value = null;
    }
  } else if (tipo == 'cgm') {
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
            cgmValueParc.value = "Parceiro Não pode ser igual ao salão parceiro";
            inscriparc.value = '';
            z01_nomeValueParc.value = '';
            return false;
          }

          if (retorno[0]['q38_categoria'] != null) {
            cgmValueParc.value = retorno[0]['z01_nome'];
            inscriparc.value = retorno[0]['q02_inscr'];
            z01_nomeValueParc.value = retorno[0]['z01_nome'];
            verificaCampos('parceiro');
          } else {
            cgmValueParc.value = '';
            throwToast("warn", "Atenção", "CGM precisa estar cadastrado como MEI", 5000);
            cgmValueParc.value = 'CGM não é MEI';
            inscriparc.value = '';
            z01_nomeValueParc.value = '';
            verificaCampos('parceiro');
          }
        } else {
          throwToast("warn", "Atenção", "CGM já Baixado", 3000);
          cgmValueParc.value = 'CGM (' + numcgm + ') já Baixado';
          inscriparc.value = '';
          z01_nomeValueParc.value = '';
          verificaCampos('parceiro');
        }
      } else {
        throwToast("warn", "Atenção", 'CGM (' + numcgm + ') Não Encontrado', 3000);
        cgmValueParc.value = 'CGM (' + numcgm + ') Não Encontrado';
        inscriparc.value = '';
        z01_nomeValueParc.value = '';
        verificaCampos('parceiro');
      }
    } else {
      verificaCampos('parceiro');
      cgmValueParc.value = '';
      inscriparc.value = '';
      z01_nomeValueParc.value = '';
      reqAnteriorCGMParc.value = null;

    }
  } else {
    throwToast("error", "Erro", "Houve um erro ao buscar a inscrição", 5000);
    return false;
  }
}

async function requisitaInscr(dados) {
  const parametros = new FormData();
  parametros.append('inscricao', dados);
  try {
    const response = await window.axios.post(
      'v4/api/tributario/issqn/buscar-iscr-salao-parceiro',
      parametros,
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    );

    const data = response.data.data[0];
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

function selecionaInscricao(result) {
  if (result) {
    if (result.inscricao) {
      inscri.value = result.inscricao;
    } else {
      inscri.value = result;
    }
  }
  verificaInscr('salao');
}

function selecionaInscricaoParceiro(result) {
  if (result) {
    if (result.inscricao) {
      inscriparc.value = result.inscricao;
    } else {
      inscriparc.value = result;
    }
  }
  verificaInscr('parceiro');
}

function abrirConsultaInscricao() {
  if (dialogConsultaInscricaoMunicipal.value) {
    dialogConsultaInscricaoMunicipal.value.toggleDialog();
  }
}

function abrirConsultaInscricaoParc() {
  if (dialogConsultaInscricaoMunicipalParc.value) {
    dialogConsultaInscricaoMunicipalParc.value.toggleDialog();
  }
}

function abrirConsultaCgm() {
  dialogConsultaCgm.value.toggleDialog();
}

function selecionaCgm(result) {
  if (result) {
    if (result.numcgm) {
      cgmparc.value = result.numcgm;
    } else {
      cgmparc.value = result;
    }
  }
  verificaInscr('cgm');
}

function limpaCampos() {
  dialogConsultaInscricaoMunicipal.value = null;
  dialogConsultaInscricaoMunicipalParc.value = null;
  dialogConsultaCgm.value = null;
  z01_nomeValue.value = '';
  z01_nomeValueParc.value = '';
  verificaCadastro.value = null;
  cgmValueParc.value = '';
  continuarButton.value = true;
  avancarButton.value = true;
  salvarButton.value = true;
  inscri.value = null;
  inscriparc.value = null;
  reqAnterior.value = null;
  reqAnteriorParc.value = null;
  reqAnteriorCGMParc.value = null;
  priemiraaba.value = false;
  segundaaba.value = false;
  datainiSalao.value = null;
  datainiParc.value = null;
  q202_obs.value = '';
  q203_obs.value = '';
  cgmparc.value = null;
}

async function submitForm() {
  const parametros = new FormData();
  const dataParc = datainiParc.value.toISOString().split('T')[0];
  if (datainiSalao.value == null) {
    datainiSalao.value = new Date();
  }
  const dataSalao = datainiSalao.value.toISOString().split('T')[0];
  parametros.append('q202_inscr', inscri.value);
  parametros.append('q202_dtinicial', dataSalao);
  parametros.append('q202_obs', q202_obs.value);
  parametros.append('q203_inscrparc', inscriparc.value);
  parametros.append('q203_numcgm', cgmparc.value);
  parametros.append('q203_dtinicial', dataParc);
  parametros.append('q203_obs', q203_obs.value);

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
      throwToast("success", "Sucesso", "Salão e Parceiros inseridos com sucesso!", 5000);
      limpaCampos();
      segundaaba.value = false;
    } else if (data == 'existe') {
      throwToast("warn", "Atenção", "Parceiro já cadastrado para esta inscrição", 5000);
      limpaCampos();
      segundaaba.value = false;
    } else if (data == 'erro') {
      throwToast("error", "Erro", "Houve um erro ao salvar os dados", 5000);
      limpaCampos();
      segundaaba.value = false;
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

function selecionaInscrByOpc(cgmInscricao) {
  let cgmFilter = null;
  cgmValueParc.value = '';
  inscriparc.value = '';
  z01_nomeValueParc.value = '';

  if (cgmInscricao) {
    cgmFilter = inscricaoCgm.value.filter(cgm => cgm.q02_inscr == cgmInscricao);
    if (cgmFilter[0]['q02_inscr'] == reqAnterior.value) {
      throwToast("warn", "Atenção", "Parceiro não pode ser igual ao salão parceiro", 5000);
      cgmValueParc.value = "Parceiro Não pode ser igual ao salão parceiro";
      inscriparc.value = '';
      z01_nomeValueParc.value = '';
      return false;
    }
    if (cgmFilter[0]['q02_dtbaix'] == null) {
      if (cgmFilter[0]['q38_categoria'] != null) {
        cgmValueParc.value = cgmFilter[0]['z01_nome'];
        inscriparc.value = cgmFilter[0]['q02_inscr'];
        z01_nomeValueParc.value = cgmFilter[0]['z01_nome'];
      } else {
        z01_nomeValueParc.value = '';
        throwToast("warn", "Atenção", "inscrição precisa estar cadastrada como MEI", 5000);
        z01_nomeValueParc.value = 'inscrição Não é MEI';
        cgmValueParc.value = '';
        cgmparc.value = '';
        verificaCampos('parceiro');
      }
    } else {
      throwToast("warn", "Atenção", "inscrição já Baixada", 3000);
      z01_nomeValueParc.value = 'inscrição (' + q02_inscr + ') já Baixada';
      cgmValueParc.value = '';
      cgmparc.value = '';
      verificaCampos('parceiro');
    }
  }

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

.form-table {
  width: 100%;
  border-collapse: collapse;
}

.form-table td {
  padding: 10px;
}

label {
  display: inline-block;
  font-weight: bold;
  text-decoration: underline;
  white-space: nowrap;
}

.container-salao {
  padding: 25px;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
}

.salao-input {
  width: 100%;
}

.children-salao {
  padding: 25px;
  width: 85%;
  max-width: 1000px;
  height: 95%;
  border: solid grey 1px;
  box-shadow: 0px 0px 15px -1px rgba(0, 0, 0, 0.75);
  border-radius: 5px;
}

.rodape-salao {
  text-align: center;
  padding-top: 20px;
}

.button-group {
  display: inline-flex;
  gap: 10px;
}

.linha-par {
  background-color: white;
}

.linha-impar {
  background-color: whitesmoke;
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
</style>
