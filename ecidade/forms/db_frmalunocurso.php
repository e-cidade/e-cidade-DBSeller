<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

//MODULO: educação
require_once(modification("libs/db_conecta.php"));
include(modification("dbforms/db_classesgenericas.php"));

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;

$oDaoAlunoCurso->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("ed18_i_codigo");
$clrotulo->label("ed47_i_codigo");
$clrotulo->label("ed31_i_codigo");
$clrotulo->label("ed52_i_codigo");
$clrotulo->label("ed31_i_curso");
$clrotulo->label("ed79_i_serie");
$clrotulo->label("ed79_i_turno");

$db_botao1 = false;

if (isset($opcao) && $opcao == "alterar") {

  $db_opcao  = 2;
  $db_botao1 = true;
  $situacao  = $ed56_c_situacao." no calendário ".$ed52_c_descr;

  if ($escolaatual != db_getsession("DB_coddepto")) {

    $ed56_i_base       = "";
    $ed31_c_descr      = "";
    $ed56_i_calendario = "";
    $ed56_c_situacao   = "";
    $ed52_c_descr      = "";
    $ed79_i_serie      = "";
    $ed11_c_descr      = "";
    $ed79_i_turno      = "";
    $ed15_c_nome       = "";
  }
} else if (    isset($opcao) && $opcao == "excluir"
            || isset($db_opcao) && $db_opcao == 3) {

  $db_botao1 = true;
  $db_opcao  = 3;
  $situacao  = @$ed56_c_situacao." no calendário ".@$ed52_c_descr;
} else {

  if (isset($alterar)) {

    $db_opcao  = 2;
    $db_botao1 = true;
  } else {
    $db_opcao = 1;
  }
}

$ed71_i_escola = db_getsession("DB_coddepto");

if (isset($desabilita)) {
  $db_botao= false;
}

if ($db_opcao == 1 && $iLinhasAlunoCurso > 0) {
  $db_botao= false;
}

$oGet    = db_utils::postMemory( $_GET );
$oAluno  = AlunoRepository::getAlunoByCodigo( $oGet->ed56_i_aluno );
$aEtapas = array();
$sEtapas = "";

/* Verifica parâmetros da secretaria para liberar ou não os campos de Matrícula Inicial */
$clsec_parametros = new cl_sec_parametros;
$sSqlSecParametros = $clsec_parametros->sql_query_file(1, 'ed290_habilitarcamposmatriculainicial');
$result = db_query( $sSqlSecParametros );
$habilitaCampos = db_utils::fieldsMemory($result, 0)->ed290_habilitarcamposmatriculainicial;


/* Verifica se o código da escola que o aluno está matriculado é o mesmo do departamento logado */
$codEscolaMatricula = \App\Domain\Educacao\Escola\Models\Matricula::with('aluno', 'turma')
    ->where('ed60_i_aluno', $oGet->ed56_i_aluno)
    ->where('ed60_c_concluida', 'N')
    ->where('ed60_c_situacao', 'MATRICULADO')
    ->where('ed60_c_ativa', 'S')
    ->first()->turma->ed57_i_escola;
$btnsMatInicial = $codEscolaMatricula == db_getsession("DB_coddepto");
/* Usarei o $btnsMatInicial para habilitar ou não o botão de salvamento desses dados */

foreach ( $oAluno->getProgressaoParcial() as $oProgressaoParcial ) {

  if ( !$oProgressaoParcial->isAtiva() || $oProgressaoParcial->isConcluida() ) {
    continue;
  }

  $aEtapas[] = $oProgressaoParcial->getEtapa()->getCodigo();
}

$sEtapas = implode( ", ", $aEtapas );
?>
<form name="form1" method="post" action="">
    <table class="form-container" id="alunocurso">
      <tr>
        <td valign="top">
          <table>
            <tr>
              <td nowrap title="<?=@$Ted56_i_escola?>">
                <?php db_ancora( @$Led56_i_escola, "", 3 );?>
              </td>
              <td>
                <?php db_input('ed56_i_escola', 15, $Ied56_i_escola, true, 'text', 3, "")?>
                <?php db_input('ed18_c_nome', 30, @$Ied18_c_nome, true, 'text', 3, '')?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?=@$Ted56_i_aluno?>">
                <?php db_ancora(@$Led56_i_aluno, "", 3);?>
              </td>
              <td>
                <?php db_input('ed56_i_aluno', 15, $Ied56_i_aluno, true, 'text', 3, "")?>
                <?php db_input('ed47_v_nome', 30, @$Ied47_v_nome, true, 'text', 3, '')?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?=@$Ted31_i_curso?>">
                <?php db_ancora(@$Led31_i_curso, "js_pesquisaed31_i_curso(true);", $db_opcao);?>
              </td>
              <td>
                <?php db_input('ed31_i_curso', 15, $Ied31_i_curso, true, 'text', $db_opcao,
                           " onchange='js_pesquisaed31_i_curso(false);'")
                ?>
                <?php db_input('ed29_c_descr', 30, @$Ied29_c_descr, true, 'text', 3, '')?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?=@$Ted56_i_base?>">
                <?php db_ancora(@$Led56_i_base, "js_pesquisaed56_i_base(true,document.form1.ed31_i_curso.value);",
                            $db_opcao);
                ?>
              </td>
              <td>
                <?php db_input('ed56_i_base', 15, $Ied56_i_base, true, 'text', $db_opcao,
                           " onchange='js_pesquisaed56_i_base(false,document.form1.ed31_i_curso.value);'")
                ?>
                <?php db_input('ed31_c_descr', 30, @$Ied31_c_descr, true, 'text', 3, '')?>
              </td>
            </tr>
            <tr id="matinicial" hidden>
                <td><b>Matrícula Inicial: </b></td>
                <td>
                <input id="matriculaescola" old-value="" type="text" autocomplete="off" size="20" maxlength="20" oninput="this.value.length>this.maxLength&&(this.value=this.value.slice(0,this.maxLength));value=parseInt(this.value.replace(/\D/g, '') || '');(this.value=value=='NaN'?'':value)" style="width:75px;background-color:#E6E4F1"/>
                <input id="btnSalvarMatriculaEscola" type="button" value="Salvar" onclick="salvarMatriculaEscola()" hidden <?=($btnsMatInicial==false?"disabled":"")?>/>
                </td>
            </tr>
            <tr id="datamatinicial" hidden>
                <td><b>Data Matrícula Inicial: </b></td>
                <td>
                <input id="matriculainicial" old-value="" type="text" autocomplete="off" size="10" maxlength="10" onblur="validaMatriculaInicial(this)" style="width:75px;background-color:#E6E4F1"/>
                <input id="btnSalvarMatriculaInicial" type="button" value="Salvar" onclick="salvarMatriculaInicial()" hidden <?=($btnsMatInicial==false?"disabled":"")?>/>
                </td>
            </tr>
          </table>
        </td>
        <td valign="top">
          <table>
            <tr>
              <td nowrap title="<?=@$Ted56_i_calendario?>">
                <?php db_ancora(@$Led56_i_calendario, "js_pesquisaed56_i_calendario(true);", $db_opcao);?>
              </td>
              <td>
                <?php db_input('ed56_i_calendario', 15, $Ied56_i_calendario, true, 'text', $db_opcao,
                           " onchange='js_pesquisaed56_i_calendario(false);'")
                ?>
                <?php db_input('ed52_c_descr', 20, @$Ied52_c_descr, true, 'text', 3, '')?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?=@$Ted79_i_serie?>">
                <?php db_ancora(@$Led79_i_serie, "js_pesquisaed79_i_serie(true,document.form1.ed56_i_base.value);",
                            $db_opcao);
                ?>
              </td>
              <td>
                <?php db_input('ed79_i_serie', 15, $Ied79_i_serie, true, 'text', $db_opcao,
                           " onchange='js_pesquisaed79_i_serie(false,document.form1.ed56_i_base.value);'")
                ?>
                <?php db_input('ed11_c_descr', 20, @$Ied11_c_descr, true, 'text', 3, '')?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?=@$Ted79_i_turno?>">
                <?php db_ancora(@$Led79_i_turno, "js_pesquisaed79_i_turno(true);", $db_opcao);?>
              </td>
              <td>
                <?php db_input('ed79_i_turno', 15, $Ied79_i_turno, true, 'text', $db_opcao,
                           " onchange='js_pesquisaed79_i_turno(false);'")
                ?>
                <?php db_input('ed15_c_nome', 20, @$Ied15_c_nome, true, 'text', 3, '')?>
              </td>
            </tr>
            <tr>
              <td nowrap>
                <b>Situação:</b>
              </td>
              <td>
                <input name="situacao" value="<?=@$situacao?>" type="text" size="41" disabled
                   style='background:#DEB887'>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr style="display: none;">
        <td>
          <input id="etapasProgressao" name="etapasProgressao" type="text" value="<?php echo $sEtapas; ?>" />
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">
          <br>
          <input name="ed56_i_codigo" type="hidden" value="<?=@$ed56_i_codigo?>">
          <input name="ed79_i_codigo" type="hidden" value="<?=@$ed79_i_codigo?>">

          <input name="<?=($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir"))?>"
                 type="submit" id="db_opcao" value="<?=($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 ||
                 $db_opcao == 22 ? "Alterar" : "Excluir"))?>" <?=($db_botao == false ? "disabled" : "")?> >

          <input name="cancelar" type="submit" value="Cancelar" <?=($db_botao1 == false ? "disabled" : "")?> >
          <br>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <table width="100%">
            <tr>
              <td valign="top">
                <?php
                  $campos_ac = "ed56_i_codigo,ed79_i_codigo,ed56_i_escola,
                                case when ed56_c_situacao = 'TRANSFERIDO REDE'
                                     then
                                          (select ed18_c_nome
                                             from transfescolarede
                                                  inner join matricula on ed60_i_codigo = ed103_i_matricula
                                                  inner join turma     on ed57_i_codigo = ed60_i_turma
                                                  inner join escola    on ed18_i_codigo = ed57_i_escola
                                            where ed60_i_aluno      = ed56_i_aluno
                                              and ed57_i_base       = ed56_i_base
                                              and ed57_i_calendario = ed56_i_calendario
                                            order by ed103_d_data desc limit 1)
                                else ed18_c_nome
                                 end as ed18_c_nome, ed56_i_aluno, ed47_v_nome, ed31_i_curso, ed29_c_descr,
                                        ed56_i_base, ed31_c_descr, ed56_i_calendario, ed56_c_situacao,
                                        ed52_c_descr, ed79_i_serie, ed11_c_descr, ed79_i_turno, ed15_c_nome";

                  $chavepri = array("ed56_i_codigo"     => @$ed56_i_codigo,
                                    "ed79_i_codigo"     => @$ed79_i_codigo,
                                    "ed56_i_escola"     => @$ed56_i_escola,
                                    "ed18_c_nome"       => @$ed18_c_nome,
                                    "ed56_i_aluno"      => @$ed56_i_aluno,
                                    "ed47_v_nome"       => @$ed47_v_nome,
                                    "ed31_i_curso"      => @$ed31_i_curso,
                                    "ed29_c_descr"      => @$ed29_c_descr,
                                    "ed56_i_base"       => @$ed56_i_base,
                                    "ed31_c_descr"      => @$ed31_c_descr,
                                    "ed56_i_calendario" => @$ed56_i_calendario,
                                    "ed56_c_situacao"   => @$ed56_c_situacao,
                                    "ed52_c_descr"      => @$ed52_c_descr,
                                    "ed79_i_serie"      => @$ed79_i_serie,
                                    "ed11_c_descr"      => @$ed11_c_descr,
                                    "ed79_i_turno"      => @$ed79_i_turno,
                                    "ed15_c_nome"       => @$ed15_c_nome
                                   );

                  $cliframe_alterar_excluir->chavepri     = $chavepri;
                  $cliframe_alterar_excluir->sql          = $oDaoAlunoCurso->sql_query("",
                                                                                       $campos_ac,
                                                                                       "ed29_i_codigo",
                                                                                       " ed56_i_aluno = $ed56_i_aluno");

                  $sWhere  = " (ed56_i_escola != $ed56_i_escola AND ed56_i_aluno = $ed56_i_aluno ";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'CANDIDATO'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'APROVADO'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'APROVADO PARCIAL'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'REPETENTE'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'ABANDONO ESCOLAR'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'EVADIDO'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'DESISTENTE'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'CANCELADO'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'ENCERRADO'";
                  $sWhere .= "   AND trim(ed56_c_situacao) != 'CONCLUÍDO')";
                  $sWhere .= " OR (ed56_i_aluno = $ed56_i_aluno AND ";
                  $sWhere .= "   (trim(ed56_c_situacao) = 'MATRICULADO'";
                  $sWhere .= " OR trim(ed56_c_situacao) = 'TRANSFERIDO REDE'";
                  $sWhere .= " OR trim(ed56_c_situacao) = 'TRANSFERIDO FORA'";
                  $sWhere .= " OR trim(ed56_c_situacao) = 'FALECIDO') )";

                  $sSqlDisabled = $oDaoAlunoCurso->sql_query("", $campos_ac, "ed29_i_codigo",  $sWhere );

                  $cliframe_alterar_excluir->sql_disabled  = $sSqlDisabled;
                  $cliframe_alterar_excluir->campos        = "ed18_c_nome,ed29_c_descr,ed31_c_descr,ed52_c_descr, ".
                                                             "ed56_c_situacao,ed11_c_descr,ed15_c_nome";
                  $cliframe_alterar_excluir->legenda       = "Registros";
                  $cliframe_alterar_excluir->msg_vazio     = "Não foi encontrado nenhum registro.";
                  $cliframe_alterar_excluir->textocabec    = "#DEB887";
                  $cliframe_alterar_excluir->textocorpo    = "#444444";
                  $cliframe_alterar_excluir->fundocabec    = "#444444";
                  $cliframe_alterar_excluir->fundocorpo    = "#eaeaea";
                  $cliframe_alterar_excluir->iframe_height = "150";
                  $cliframe_alterar_excluir->iframe_width  = "100%";
                  $cliframe_alterar_excluir->tamfontecabec = 9;
                  $cliframe_alterar_excluir->tamfontecorpo = 9;
                  $cliframe_alterar_excluir->opcoes        = 2;
                  $cliframe_alterar_excluir->formulario    = false;
                  $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
                ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </form>
<script>

require_once('scripts/scripts.js');
require_once('scripts/widgets/Input/DBInput.widget.js');
require_once('scripts/widgets/Input/DBInputDate.widget.js');
require_once('scripts/session.js');
require_once('scripts/classes/http/http.js');

let inputMatInicial = document.getElementById('matinicial');
let inputDataMatInicial = document.getElementById('datamatinicial');

let habilitaCampos = "<?php echo $habilitaCampos == 't' ?>";

if (habilitaCampos) {
    inputMatInicial.removeAttribute('hidden');
    inputDataMatInicial.removeAttribute('hidden');
}

let urlApi = '';
PHPSession.loadData().then(() => {
    urlApi = PHPSession.requestApi;
    buscarMatriculaEscola();
});
let carregando = 'Caregando..';
let salvando = 'Salvando..';
// $('ocupacaopai').value = carregando;
// $('ocupacaopai_label').value = carregando;
// $('ocupacaomae').value = carregando;
// $('ocupacaomae_label').value = carregando;
// $('ocupacaoresp').value = carregando;
// $('ocupacaoresp_label').value = carregando;

const showElem = (el) => {
        if (typeof el === 'string') {
            el = document.getElementById(el)
        }

        el && el.removeAttribute('hidden')
    },
    hideElem = (el) => {
        if (typeof el === 'string') {
            el = document.getElementById(el)
        }

        el && el.setAttribute('hidden', !0)
    },
    brToDate = (date) => {
        return date.split('/').reverse().join('-')
    },
    dateToBr = (date) => {
        return date.split('-').reverse().join('/')
    };

$('matriculaescola').onkeyup = function() {
    el = 'btnSalvarMatriculaEscola';
    if (this.value && this.value != this.getAttribute('old-value')) {
        showElem(el)
    } else {
        hideElem(el)
    }
};

$('matriculainicial').oninput = function() {
    mascaraMatriculaInicial(this);

    el = 'btnSalvarMatriculaInicial';
    if (this.value && this.value != this.getAttribute('old-value')) {
        showElem(el)
    } else {
        hideElem(el)
    }
};

function buscarMatriculaEscola() {
    if (!$('ed56_i_aluno').value || !$('ed56_i_escola').value) {
        return;
    }

    js_divCarregando('Buscando matrícula na escola..', 'msg_box_req_bmatriculaescolaaluno');

    HttpClient.get(
        `${urlApi}/educacao/escola/matriculaescolaaluno/${$('ed56_i_escola').value}/`+$('ed56_i_aluno').value
    ).then((response) => {
        js_removeObj('msg_box_req_bmatriculaescolaaluno');

        if (response.message) {
            alert(response.message);
        }
        if (response.error) {
            return;
        }
        response = response.data;
        if (response?.ed365_matricula) {
            let matriculainicial = response.ed365_matricula;
            $('matriculaescola').value = matriculainicial;
            $('matriculaescola').setAttribute('old-value', matriculainicial);
        }
        if (response?.ed365_datamatriculainicial) {
            let datamatriculainicial = dateToBr(response.ed365_datamatriculainicial);
            $('matriculainicial').value = datamatriculainicial;
            $('matriculainicial').setAttribute('old-value', datamatriculainicial);
        }
    });
}
function salvarMatriculaEscola() {
    if (!$('ed56_i_aluno').value || !$('ed56_i_escola').value) {
        alert('Os campos "Escola" e "Aluno" são necessarios para prosseguir.');
        return;
    }

    $('btnSalvarMatriculaEscola').value = salvando;
    js_divCarregando('Salvando matrícula na escola..', 'msg_box_req_smatriculaescolaaluno');

    HttpClient.get(
        `${urlApi}/educacao/escola/matriculaescolaaluno/${$('ed56_i_escola').value}/${$('ed56_i_aluno').value}/`+ $('matriculaescola').value
    ).then(response => {
        js_removeObj('msg_box_req_smatriculaescolaaluno');
        $('btnSalvarMatriculaEscola').value = 'Salvar';

        if (response.message) {
            alert(response.message);
        }
        if (response.error) {
            return;
        }
        response = response.data;

        $('matriculaescola').setAttribute('old-value', $('matriculaescola').value);
        hideElem('btnSalvarMatriculaEscola')
    });
}

function salvarMatriculaInicial() {
    if (!$('ed56_i_aluno').value || !$('ed56_i_escola').value) {
        alert('Os campos "Escola" e "Aluno" são necessarios para prosseguir.');
        return;
    }

    $('btnSalvarMatriculaInicial').value = salvando;
    js_divCarregando('Salvando matrícula inicial na escola..', 'msg_box_req_smatriculainicialescolaaluno');

    HttpClient.get(
        `${urlApi}/educacao/escola/matriculainicialescolaaluno/${$('ed56_i_escola').value}/${$('ed56_i_aluno').value}/`+ brToDate($('matriculainicial').value)
    ).then(response => {
        js_removeObj('msg_box_req_smatriculainicialescolaaluno');
        $('btnSalvarMatriculaInicial').value = 'Salvar';

        if (response.message) {
            alert(response.message);
        }
        if (response.error) {
            return;
        }
        response = response.data;

        $('matriculainicial').setAttribute('old-value', $('matriculainicial').value);
        hideElem('btnSalvarMatriculaInicial')
    });
}

function isValidDate(dateString) {
    if (!/^\d{2}\/\d{2}\/\d{4}$/.test(dateString)) return false;

    let parts = dateString.split('/');
    let day = parseInt(parts[0], 10);
    let month = parseInt(parts[1], 10);
    let year = parseInt(parts[2], 10);
    if (year < 1000 || year > 9999 || month == 0 || month > 12) return false;

    let daysInMonth = [31, 28 + (year % 4 == 0 && year % 100 != 0 || year % 400 == 0), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    return day > 0 && day <= daysInMonth[month - 1];
}
function validaMatriculaInicial(el) {
    if (!isValidDate(el.value)) {
        alert('Matrícula inicial inválida.');
        hideElem('btnSalvarMatriculaInicial');
        el.value = ''
    }
}
function mascaraMatriculaInicial(el){
    let val = el.value;

    if (/^\d{2}\/\d{2}\/\d{4}$/.test(val)) { // Verifica se já está no formato correto
        return;
    }
    if (/^\d{2}\/?$/.test(val)) {
        el.value = val + '/';
    } else if (/^\d{2}\/\d{2}\/?$/.test(val)) {
        el.value = val + '/';
    } else if (/^\d{2}\/\d{2}\/\d{4}$/.test(val)) {
        // OK
    } else {
        // el.value = '';
    }
}


var sCaminhoMensagens = 'educacao.escola.db_frmalunocurso.';

function js_pesquisaed31_i_curso(mostra) {

  if (mostra == true) {
    js_OpenJanelaIframe(
                         '',
                         'db_iframe_curso',
                         'func_alunocurso.php?aluno='+document.form1.ed56_i_aluno.value+
                                            '&funcao_js=parent.js_mostracurso1|ed29_i_codigo|ed29_c_descr',
                         'Pesquisa de Cursos',
                         true
                       );
  } else {
    if (document.form1.ed31_i_curso.value != '') {
      js_OpenJanelaIframe(
                           '',
                           'db_iframe_curso',
                           'func_alunocurso.php?aluno='+document.form1.ed56_i_aluno.value
                                             +'&pesquisa_chave='+document.form1.ed31_i_curso.value
                                             +'&funcao_js=parent.js_mostracurso',
                           'Pesquisa',
                           false
                         );
    } else {
      document.form1.ed29_c_descr.value = '';
    }
  }
}

function js_mostracurso(chave, erro) {

  document.form1.ed29_c_descr.value = chave;

  if (erro == true) {

    document.form1.ed31_i_curso.focus();
    document.form1.ed31_i_curso.value = '';
  }

  document.form1.ed56_i_base.value  = '';
  document.form1.ed31_c_descr.value = '';
  document.form1.ed79_i_serie.value = '';
  document.form1.ed11_c_descr.value = '';
  document.form1.ed79_i_turno.value = '';
  document.form1.ed15_c_nome.value  = '';
}

function js_mostracurso1(chave1, chave2) {

  document.form1.ed31_i_curso.value = chave1;
  document.form1.ed29_c_descr.value = chave2;
  document.form1.ed56_i_base.value  = '';
  document.form1.ed31_c_descr.value = '';
  document.form1.ed79_i_serie.value = '';
  document.form1.ed11_c_descr.value = '';
  document.form1.ed79_i_turno.value = '';
  document.form1.ed15_c_nome.value  = '';

  db_iframe_curso.hide();
}

function js_pesquisaed56_i_base(mostra, curso) {

  if(curso == "") {

    alert( _M( sCaminhoMensagens + 'informe_curso'));
    document.form1.ed56_i_base.value                  = '';
    document.form1.ed31_i_curso.style.backgroundColor = '#99A9AE';
    document.form1.ed31_i_curso.focus();
  } else {

    if (mostra == true) {
      js_OpenJanelaIframe(
                           '',
                           'db_iframe_base',
                           'func_baseescola.php?curso='+curso
                                             +'&funcao_js=parent.js_mostrabase1|ed31_i_codigo|ed31_c_descr',
                           'Pesquisa de Bases Curriculares',
                           true
                         );
    } else {

      if (document.form1.ed56_i_base.value != '') {
        js_OpenJanelaIframe(
                             '',
                             'db_iframe_base',
                             'func_baseescola.php?curso='+curso
                                               +'&pesquisa_chave='+document.form1.ed56_i_base.value
                                               +'&funcao_js=parent.js_mostrabase',
                             'Pesquisa',
                             false
                           );
      } else {
        document.form1.ed31_c_descr.value = '';
      }
    }
  }
}

function js_mostrabase(chave, erro) {

  document.form1.ed31_c_descr.value = chave;

  if (erro == true) {

    document.form1.ed56_i_base.focus();
    document.form1.ed56_i_base.value = '';
  }

  document.form1.ed79_i_serie.value = '';
  document.form1.ed11_c_descr.value = '';
}

function js_mostrabase1(chave1, chave2) {

  document.form1.ed56_i_base.value  = chave1;
  document.form1.ed31_c_descr.value = chave2;
  document.form1.ed79_i_serie.value = '';
  document.form1.ed11_c_descr.value = '';

  db_iframe_base.hide();
}

function js_pesquisaed56_i_calendario(mostra) {

  if (mostra == true) {
    js_OpenJanelaIframe(
                         '',
                         'db_iframe_calendario',
                         'func_calendarioescola.php?funcao_js=parent.js_mostracalendario1|ed52_i_codigo|ed52_c_descr',
                         'Pesquisa de Calendários',
                         true
                       );
  } else {

    if(document.form1.ed56_i_calendario.value != ''){
      js_OpenJanelaIframe(
                           '',
                           'db_iframe_calendario',
                           'func_calendarioescola.php?pesquisa_chave='+document.form1.ed56_i_calendario.value
                                                   +'&funcao_js=parent.js_mostracalendario',
                           'Pesquisa',
                           false
                         );
    } else {
      document.form1.ed52_c_descr.value = '';
    }
  }
}

function js_mostracalendario(chave, erro) {

  document.form1.ed52_c_descr.value = chave;

  if (erro == true) {

    document.form1.ed56_i_calendario.focus();
    document.form1.ed56_i_calendario.value = '';
  }
}

function js_mostracalendario1(chave1, chave2) {

  document.form1.ed56_i_calendario.value = chave1;
  document.form1.ed52_c_descr.value      = chave2;

  db_iframe_calendario.hide();
}

function js_pesquisaed79_i_serie(mostra, base) {

  if (base == "") {

    alert( _M( sCaminhoMensagens + 'informe_base_curricular' ) );

    document.form1.ed79_i_serie.value                = '';
    document.form1.ed56_i_base.style.backgroundColor = '#99A9AE';
    document.form1.ed56_i_base.focus();
  } else {

    var sUrl = 'aluno='+document.form1.ed56_i_aluno.value;

    if ( !empty( $('etapasProgressao').value ) ) {
      sUrl += '&sEtapas=' + $('etapasProgressao').value;
    }

    if (mostra == true) {
      js_OpenJanelaIframe(
                           '',
                           'db_iframe_serie',
                           'func_seriebase.php?'+sUrl
                                            +'&base='+base
                                            +'&funcao_js=parent.js_mostraserie1|ed11_i_codigo|ed11_c_descr',
                           'Pesquisa de Etapas',
                           true
                         );
    } else {
      if (document.form1.ed79_i_serie.value != '') {
        js_OpenJanelaIframe(
                             '',
                             'db_iframe_serie',
                             'func_seriebase.php?'+sUrl
                                              +'&base='+base
                                              +'&pesquisa_chave='+document.form1.ed79_i_serie.value
                                              +'&funcao_js=parent.js_mostraserie',
                             'Pesquisa',
                             false
                           );
      } else {
        document.form1.ed11_c_descr.value = '';
      }
    }
  }
}

function js_mostraserie(chave, erro) {

  document.form1.ed11_c_descr.value = chave;

  if (erro == true) {

    document.form1.ed79_i_serie.focus();
    document.form1.ed79_i_serie.value = '';
  }
}

function js_mostraserie1(chave1, chave2) {

  document.form1.ed79_i_serie.value = chave1;
  document.form1.ed11_c_descr.value = chave2;

  db_iframe_serie.hide();
}

function js_pesquisaed79_i_turno(mostra) {

  if (document.form1.ed31_i_curso.value == "") {

    alert(_M(sCaminhoMensagens + 'informe_curso_turno'));

    document.form1.ed79_i_turno.value                 = '';
    document.form1.ed31_i_curso.style.backgroundColor = '#99A9AE';
    document.form1.ed31_i_curso.focus();
  } else {

    if (mostra == true) {
      js_OpenJanelaIframe(
                           '',
                           'db_iframe_turno',
                           'func_turnoalunocurso.php?curso='+document.form1.ed31_i_curso.value
                                                  +'&funcao_js=parent.js_mostraturno1|ed15_i_codigo|ed15_c_nome',
                           'Pesquisa de Turnos',
                           true
                         );
    } else {

      if(document.form1.ed79_i_turno.value != ''){
        js_OpenJanelaIframe(
                             '',
                             'db_iframe_turno',
                             'func_turnoalunocurso.php?curso='+document.form1.ed31_i_curso.value
                                                    +'&pesquisa_chave='+document.form1.ed79_i_turno.value
                                                    +'&funcao_js=parent.js_mostraturno',
                             'Pesquisa',
                             false
                           );
      } else {
        document.form1.ed15_c_nome.value = '';
      }
    }
  }
}

function js_mostraturno(chave, erro) {

  document.form1.ed15_c_nome.value = chave;

  if (erro == true) {

    document.form1.ed79_i_turno.focus();
    document.form1.ed79_i_turno.value = '';
  }
}

function js_mostraturno1(chave1, chave2) {

  document.form1.ed79_i_turno.value = chave1;
  document.form1.ed15_c_nome.value  = chave2;

  db_iframe_turno.hide();
}

</script>
