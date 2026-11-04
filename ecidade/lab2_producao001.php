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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_lab_laboratorio_classe.php"));
require_once(modification('libs/db_utils.php'));
$cllab_laboratorio = new cl_lab_laboratorio;
$clrotulo          = new rotulocampo;

$iUsuario = db_getsession('DB_login');
$iDepto = db_getsession('DB_coddepto');

$oDaolab_labusuario;

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
 <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
 <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >

<table valign="top" marginwidth="0" width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="center" valign="top" bgcolor="#CCCCCC">
      <center>
      <br><br>
      <fieldset style='width: 75%;'> <legend><b>Relatório de Produtividade</b></legend>
      <form name='form1'>
        <table border="0">
          <tr>
              <td nowrap>
                <b style="margin-left: 5px"> Inicio:</b>
                  <?php db_inputdata('la02_d_datainicio',
                      @$la02_d_datainicio_dia,
                      @$la02_d_datainicio_mes,
                      @$la02_d_datainicio_ano,
                      true,
                      'text',
                      1);?>
                  <b style="margin-left: 30px">Fim:</b>
                  <?php db_inputdata('la02_d_datafim',
                      @$la02_d_datafim_dia,
                      @$la02_d_datasaida_mes,
                      @$la02_d_datasaida_ano,
                      true,
                      'text',
                      1);?>
              </td>
          </tr>
          <tr>
            <td align="right" colspan="4">
              <?php
              $sSql           = $cllab_laboratorio->sql_query("","la02_i_codigo,la02_c_descr");
              $rsLaboratorios = $cllab_laboratorio->sql_record($sSql);
              $labs = [
                'TODOS' => 'TODOS',
              ];
              while($state = pg_fetch_array($rsLaboratorios)) {
                $labs[$state['la02_i_codigo']] = $state['la02_c_descr'];
              }
              db_multiploselect("la02_i_codigo",
                                "la02_c_descr",
                                "nselecionados",
                                "sselecionados",
                                $labs,
                                array(),
                                10,
                                400, '', '', 'true', 'setParametrosPesquisaSetor(); setTamanhoSelect();');
              ?>
            </td>
          </tr>
          <tr id="linhaFiltroSetor">
           <td colspan="4">
            <div style="margin-top: 20px; margin-bottom: 10px;" id="lancadorSetor"></div>
           </td>
          </tr>
            <tr id="linhaFiltroExame">
                <td colspan="4">
                    <div style="margin-bottom: 15px;" id="lancadorExame"></div>
                </td>
            </tr>
            <tr id="avisoTipoRelatorio" style="display: none;">
                <td colspan="4">
                    <span style="color: red">Os filtros SETOR e EXAME estão aplicados apenas para o Tipo SINTÉTICO.</span>
                </td>
            </tr>
          <tr>
            <td>
              <b>Tipo:</b>
                <?php $aX = array(1=>'SINTÉTICO',2=>'ANALÍTICO');
                db_select('tipo',$aX,true,1);?>
            </td>
          </tr>
        </table>
        <input type="button" name="gerar" value="Gerar" onclick="js_gerar()" >
      </form>
      </fieldset>
      </center>
    </td>
 </tr>
</table>
<?php
  db_menu();
?>
</body>
</html>
<script>
const dataInicial = document.getElementById('la02_d_datainicio');
const dataFinal = document.getElementById('la02_d_datafim');
const selectSelecionados = document.getElementById('sselecionados');
const selectTipo = document.getElementById('tipo');

var lancadorSetor = new DBLancador('lancadorSetor');
lancadorSetor.sTextoFieldset = 'Setor';
lancadorSetor.sTituloJanela = "Pesquisa Setor";
lancadorSetor.setGridHeight(80);
lancadorSetor.setGridLargura([50, 200, 50]);
lancadorSetor.setTamanhoInputDescricao( 100 );
lancadorSetor.setLabelAncora('Setor:');
lancadorSetor.setNomeInstancia('lancadorSetor');
lancadorSetor.setCallbackBotao(function() {
    setParametrosPesquisaExames();
});
lancadorSetor.setCallbackRemover(function() {
    setParametrosPesquisaExames();
});
setParametrosPesquisaSetor();
lancadorSetor.show(document.getElementById('lancadorSetor'));

var lancadorExame = new DBLancador('lancadorExame');
lancadorExame.sTextoFieldset = 'Exame';
lancadorExame.sTituloJanela = "Pesquisa Exame";
lancadorExame.setGridHeight(80);
lancadorExame.setGridLargura([50, 200, 50]);
lancadorExame.setTamanhoInputDescricao( 100 );
lancadorExame.setLabelAncora('Exame:');
lancadorExame.setNomeInstancia('lancadorExame');
setParametrosPesquisaExames();
lancadorExame.show(document.getElementById('lancadorExame'));

function js_gerar() {
    if (!validaCampos()) {
        return;
    }

    let selecionados = [];
    for (let selecionado of selectSelecionados.options) {
        if (selecionado.value == 'TODOS' && selectSelecionados.options.length > 1) {
            continue;
        }
        selecionados.push(selecionado.value);
    }

    let dados = [];
    dados.push(`dInicio=${dataInicial.value}`);
    dados.push(`dFim=${dataFinal.value}`);
    dados.push(`sLaboratorios=${selecionados.join(',')}`);
    dados.push(`iTipo=${selectTipo.value}`);

    dados.push(`setores=${getSetoresSelecionados(true)}`);
    dados.push(`exames=${getCodigosExame()}`);

    let url = `lab2_producao002.php?${dados.join('&')}`;
    let features = `width=${(screen.availWidth-5)},height=${(screen.availHeight-40)},scrollbars=1,location=0`;
    let Jan = window.open(url, '', features);
    Jan.moveTo(0,0);
}

function validaCampos() {
    const setoresSelecionados = lancadorSetor.getRegistros();
    const examesSelecionados = lancadorExame.getRegistros();

    if(selectSelecionados.length == 0 && setoresSelecionados.length === 0 && examesSelecionados.length === 0) {
        alert('Selecione um laboratório, ou um setor ou um exame!');
        return false;
    }
    if (selectSelecionados.length == 1 && selectSelecionados.options[0].value == 'TODOS' && selectTipo.value == 2) {
        alert('Opção TODOS não disponível para o relatório do tipo Analítico. Selecione um ou mais laboratórios para gerar o documento!');
        return false;
    }
    if (dataInicial.value == "" && dataFinal.value == "") {
        alert('Informe o período!');
        return false;
	}
	if (js_formatar(dataInicial.value, 'd') > js_formatar(dataFinal.value, 'd')) {
		alert('A data inicial não pode ser maior que a data final!');
        return false;
	}

    return true;
}

function getSetoresSelecionados(getDescricao = false) {
    const setores = [];

    for (let setor of lancadorSetor.getRegistros()) {
        setores.push(setor.sCodigo);
        if (getDescricao) {
           setores.push(setor.sDescricao);
        }
    }

    return setores;
}

function getCodigosExame() {
    const exames = [];

    for (let exame of lancadorExame.getRegistros()) {
        exames.push(exame.sCodigo);
    }

    return exames;
}

function setParametrosPesquisaSetor() {
    const laboratoriosSelecionados = [];

    for (let selecionado of selectSelecionados.options) {
        if (selecionado.value == 'TODOS') {
            continue;
        }
        laboratoriosSelecionados.push(selecionado.value);
    }
    lancadorSetor.setParametrosPesquisa('func_lab_setor.php', ['la23_i_codigo', 'la23_c_descr'], `laboratorios=${laboratoriosSelecionados.join(',')}`);
}

function setParametrosPesquisaExames() {
    const setoresSelecionados = getSetoresSelecionados();

    lancadorExame.setParametrosPesquisa(
        'func_lab_exame.php',
        ['la08_i_codigo', 'la08_c_descr'],
        `la02_i_codigo=&setores=${setoresSelecionados}`
    );
}

function setTamanhoSelect() {
    const leftSelect = document.getElementById('nselecionados');
    const rightSelect = document.getElementById('sselecionados');

    leftSelect.style.cssText = 'height: 224px; width:400px';
    rightSelect.style.cssText = 'height: 224px; width:400px';
}
setTamanhoSelect()
</script>
