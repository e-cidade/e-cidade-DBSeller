<?php
/**
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

use App\Domain\RecursosHumanos\RH\Relatorios\Services\ConfigAssentPeriodoService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($_POST);

$classenta   = new cl_assenta;
$rotulocampo = new rotulocampo;
$rotulocampo->label("rh01_regist");
$rotulocampo->label("z01_nome");

/**
 * Configuracao do relatorio
 *
 * - filtro adicionais (Selecao, Lotacao...)
 * - filtro assentamentos do departamento (Apenas assentamento do departamento)
 */
$configAssentPeriodoService = new ConfigAssentPeriodoService;
$configAssentPeriodo = $configAssentPeriodoService->getByInstit(db_getsession("DB_instit"));

$filtrosadicionais  = false;
$filtroassentdepart = false;

if ($configAssentPeriodo) {
    if ($configAssentPeriodo->rh512_filtroadicionais) {
        $filtrosadicionais = true;
    }

    if ($configAssentPeriodo->rh512_filtroassentdepart) {
        $filtroassentdepart = true;
    }
}

$result_assenta = $classenta->sql_record($classenta->sql_query_file(null," h16_assent, h16_dtconc ", " h16_dtconc, h16_assent"));
if ($classenta->numrows > 0) {
  db_fieldsmemory($result_assenta, 0);
  $datai_dia = db_subdata($h16_dtconc,"d");
  $datai_mes = db_subdata($h16_dtconc,"m");
  $datai_ano = db_subdata($h16_dtconc,"a");
}

if ($filtrosadicionais) {
    $data_inicial = '1990-01-01';
    $datai_dia = db_subdata($data_inicial,"d");
    $datai_mes = db_subdata($data_inicial,"m");
    $datai_ano = db_subdata($data_inicial,"a");
    $dataf_dia = date('d', db_getsession("DB_datausu"));
    $dataf_mes = date('m', db_getsession("DB_datausu"));
    $dataf_ano = date('Y', db_getsession("DB_datausu"));
}

if ($filtrosadicionais) {
    $gform               = new cl_formulario_rel_pes;
    $iCodigoDepartamento = db_getsession('DB_coddepto');
    $dbwhere             = "";

    if ($filtroassentdepart) {
        $dbwhere = "
            exists (SELECT 1
            FROM tipoassedb_depart
            WHERE rh184_db_depart = ".db_getsession('DB_coddepto')."
            AND rh184_tipoasse = h12_codigo)
        ";
    }

    $result_tipoassent = $classenta->sql_record($classenta->sql_query_tipo(null, "h12_codigo, h12_assent || ' - ' || h12_descr as h12_descr", "h12_descr",$dbwhere));
} else {
    $result_tipoassent = $classenta->sql_record($classenta->sql_query_tipo(null, "h12_codigo, h12_assent || ' - ' || h12_descr as h12_descr", "h12_descr"));
}
?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>

</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table  align="center">
  <form name="form1" method="post" action="" >
  <tr>
    <td width="122" align="left" title="<?=$Trh01_regist?>">
      <?php
        db_ancora(@$Lrh01_regist, "js_pesquisarh01_regist(true);", 1);
      ?>
    </td>
    <td>
      <?php
        db_input('rh01_regist', 8, $Irh01_regist, true, 'text', 1, " onchange='js_pesquisarh01_regist(false);'")
      ?>
      <?php
        db_input('z01_nome', 30, $Iz01_nome, true, 'text', 3, '');
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="Período certidão" align="left">
      <b>Período:</b>
    </td>
    <td nowrap>
      <?php
        db_inputdata("datai", @$datai_dia, @$datai_mes, @$datai_ano, true, 'text', 1);
      ?>
      <b>&nbsp;a&nbsp;</b>
      <?php
        db_inputdata("dataf", @$dataf_dia, @$dataf_mes, @$dataf_ano, true, 'text', 1);
      ?>
    </td>
  </tr>
  <tr>
    <td align="left" ><strong>Ordem :&nbsp;&nbsp;</strong>
    </td>
    <td align="left">
        <?php
            $arr_ordem = array("a"=>"Alfabetica","n"=>"Numerica","c"=>"Cargo","d"=>"Data");
            db_select('ordem',$arr_ordem,true,4,"");

            if ($filtrosadicionais) {
                echo '&nbsp;&nbsp;&nbsp;&nbsp;<strong>Ordem por tipo de Resumo:</strong>';
                $arr_ordem = array("s"=>"Sim","n"=>"Não");
                db_select('ordemresumo',$arr_ordem,true,4,"");
            }
        ?>
    </td>
  </tr>
  <tr>
    <td align="left" ><strong>Imprime Descricao :&nbsp;&nbsp;</strong>
    </td>
    <td align="left">
        <?php
            $arr_descr = array("n"=>"Nao","s"=>"Sim");
            db_select('descr',$arr_descr,true,4,"");
        ?>
      </td>
  </tr>
  <tr>
  <tr>
    <td><b>Tipo de exportação: </b></td>
    <td>
        <select name="export_type">
            <option value="pdf" selected>PDF</option>
            <option value="xlsx">Excel</option>
            <option value="csv">CSV</option>
        </select>
    </td>
  </tr>
      <?php
        if ($filtrosadicionais) :
            if (!isset($tipo)) {
			    $tipo = "g";
			}

            if (!isset($filtro)) {
			    $filtro = "i";
			}

			$gform->tipores               = true;
 			$gform->usalota               = true;           	  // PERMITIR SELEÇÃO DE LOTAÇÕES
 			$gform->usaLotaFieldsetClass  = true;                 // PERMITIR SELEÇÃO DE LOTAÇÕES
 			$gform->usaorga               = true;                 // PERMITIR SELEÇÃO DE ÓRGÃO/Secretaria
 			$gform->usacarg               = true;                 // PERMITIR SELEÇÃO DE Cargo
            $gform->usaregi               = true;                 // PERMITIR SELEÇÃO DE Matricula
            $gform->usaloca               = true;                 // PERMITIR SELEÇÃO DE Local de trabalho
 			$gform->masnome               = "ordem";
 			$gform->ca1nome               = "cargoi";             // NOME DO CAMPO DO CARGO INICIAL
 			$gform->ca2nome               = "cargof";             // NOME DO CAMPO DO CARGO FINAL
 			$gform->ca3nome               = "selcargo";
 			$gform->ca4nome               = "Cargo";
 			$gform->lo1nome               = "lotaci";             // NOME DO CAMPO DA LOTAÇÃO INICIAL
 			$gform->lo2nome               = "lotacf";             // NOME DO CAMPO DA LOTAÇÃO FINAL
 			$gform->lo3nome               = "sellot";
 			$gform->or1nome               = "local1";             // NOME DO CAMPO DO ÓRGÃO INICIAL
 			$gform->or2nome               = "local2";             // NOME DO CAMPO DO ÓRGÃO FINAL
 			$gform->or3nome               = "sellocal";           // NOME DO CAMPO DE SELEÇÃO DE ÓRGÃOS
 			$gform->or4nome               = "Locais de trabalho"; // NOME DO CAMPO DE SELEÇÃO DE ÓRGÃOS
            $gform->or1nome               = "orgaoi";             // NOME DO CAMPO DO ÓRGÃO INICIAL
 			$gform->or2nome               = "orgaof";             // NOME DO CAMPO DO ÓRGÃO FINAL
 			$gform->or3nome               = "selorg";             // NOME DO CAMPO DE SELEÇÃO DE ÓRGÃOS
 			$gform->or4nome               = "Secretaria";         // NOME DO CAMPO DE SELEÇÃO DE ÓRGÃOS
 			$gform->trenome               = "tipo";               // NOME DO CAMPO TIPO DE RESUMO
 			$gform->tfinome               = "filtro";             // NOME DO CAMPO TIPO DE FILTRO
 			$gform->resumopadrao          = "g";                  // TIPO DE RESUMO PADRÃO
 			$gform->filtropadrao          = "i";
 			$gform->strngtipores          = "glocmt";              // OPÇÕES PARA MOSTRAR NO TIPO DE RESUMO g - geral,
 			$gform->selecao               = true;
 			$gform->onchpad               = true;                 // MUDAR AS OPÇÕES AO SELECIONAR OS TIPOS DE FILTRO OU RESUMO
 			$gform->manomes               = false;
			$gform->gera_form( db_anofolha(), db_mesfolha() );
        endif
		?>
      </tr>
  <tr>
    <td nowrap colspan="2">

    <?php
        $arr_tipoassent_inicial = Array();
        $arr_tipoassent_final   = Array();
        if (isset($classenta->numrows)) {
          for ($i=0; $i<$classenta->numrows; $i++) {
            db_fieldsmemory($result_tipoassent, $i);
            if (!isset($objeto2) || (isset($objeto2) && !in_array($h12_codigo, $objeto2))) {
              $arr_tipoassent_inicial[$h12_codigo] = $h12_descr;
            } else {
              $arr_tipoassent_final[$h12_codigo] = $h12_descr;
            }
          }
        }
        db_multiploselect("valor","descr", "", "", $arr_tipoassent_inicial, $arr_tipoassent_final, 10, 350, "", "", true);
    ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align = "center">
      <input name="relatorio" id="relatorio" type="button" value="Relatório" onclick="js_emite();" >
    </td>
  </tr>
  </form>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_emite() {
  qry  = "&regist="+ document.form1.rh01_regist.value;
  qry += "&ordem="+ document.form1.ordem.value;
  qry += "&descr="+ document.form1.descr.value;
  qry += "&perinicial=" + document.form1.datai_ano.value+'-'+document.form1.datai_mes.value+'-'+document.form1.datai_dia.value;
  qry += "&perfinal=" + document.form1.dataf_ano.value+'-'+document.form1.dataf_mes.value+'-'+document.form1.dataf_dia.value;
  qry += "&tipos=" + js_db_multiploselect_retornaselecionados();
  qry += "&export=" + document.form1.export_type.value;

    <?php if ($filtrosadicionais) : ?>
        qry += "&ordemresumo=" + document.form1.ordemresumo.value;
        qry += "&selecao="     + document.form1.selecao.value;
        qry += "&tipo="		   + document.form1.tipo.value;

        if (document.form1.selcargo) {
            if (document.form1.selcargo.length > 0) {
                faixacargo = js_campo_recebe_valores();
                qry+= "&fca="+faixacargo;
            }
        } else if (document.form1.cargoi) {
            carini = document.form1.cargoi.value;
            carfim = document.form1.cargof.value;
            qry+= "&cai="+carini;
            qry+= "&caf="+carfim;
        }

        if (document.form1.sellot) {
            if (document.form1.sellot.length > 0) {
                faixalot = js_campo_recebe_valores();
                qry+= "&flt="+faixalot;
            }
        } else if (document.form1.lotaci) {
            lotini = document.form1.lotaci.value;
            lotfim = document.form1.lotacf.value;
            qry+= "&lti="+lotini;
            qry+= "&ltf="+lotfim;
        }

        if (document.form1.selorg) {
            if (document.form1.selorg.length > 0) {
                faixaorg = js_campo_recebe_valores();
                qry+= "&for="+faixaorg;
            }
        } else if(document.form1.orgaoi) {
            orgini = document.form1.orgaoi.value;
            orgfim = document.form1.orgaof.value;
            qry+= "&ori="+orgini;
            qry+= "&orf="+orgfim;
        }

        if (document.form1.sellocal) {
            if (document.form1.sellocal.length > 0) {
                faixaorg = js_campo_recebe_valores();
                qry+= "&loc="+faixaorg;
            }
        } else if (document.form1.local1) {
            orgini = document.form1.local1.value;
            orgfim = document.form1.local2.value;
            qry+= "&lc1="+orgini;
            qry+= "&lc2="+orgfim;
        }

        if (document.form1.selregist) {
            if (document.form1.selregist.length > 0) {
                faixaorg = js_campo_recebe_valores();
                qry+= "&reg="+faixaorg;
            }
        } else if (document.form1.registro1) {
            orgini = document.form1.registro1.value;
            orgfim = document.form1.registro2.value;
            qry+= "&reg1="+orgini;
            qry+= "&reg2="+orgfim;
        }
    <?php endif ?>

  const export_type = document.form1.export_type.value;
  if (export_type != 'pdf') {
    let url = `rec2_relassentaporperiodo002.php?${qry}`;
    js_divCarregando('Gerando o Arquivo...', 'downlaod-msg');

    fetch(url)
    .then(res => {
        if (res.ok) {
            res.json().then(data => {
                if (data.erro) {
                    alert(data.msg);
                    return false;
                }

                let filePath = data.file;
                js_viewFileDownload(filePath);
            })
        }
    })
    .catch(error => alert('Erro ao gerar o arquivo.'))
    .finally(() => js_removeObj('downlaod-msg'));
  } else {
    jan = window.open('rec2_relassentaporperiodo002.php?'+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
  }
}
function js_pesquisarh01_regist(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhpessoal','func_rhpessoal.php?filtro_lotacao=true&funcao_js=parent.js_mostrapessoal1|rh01_regist|z01_nome&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',true);
  }else{
    if(document.form1.rh01_regist.value != ''){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhpessoal','func_rhpessoal.php?filtro_lotacao=true&pesquisa_chave='+document.form1.rh01_regist.value+'&funcao_js=parent.js_mostrapessoal&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',false);
    }else{
      document.form1.z01_nome.value = '';
      js_seleciona_combo(document.form1.objeto2);
//      document.form1.submit();
    }
  }
}
function js_mostrapessoal(chave,erro){
  document.form1.z01_nome.value = chave;
  if(erro==true){
    document.form1.rh01_regist.focus();
    document.form1.rh01_regist.value = '';
  }else{
    js_seleciona_combo(document.form1.objeto2);
//    document.form1.submit();
  }
}
function js_mostrapessoal1(chave1,chave2){
  document.form1.rh01_regist.value = chave1;
  document.form1.z01_nome.value   = chave2;
  db_iframe_rhpessoal.hide();
  js_seleciona_combo(document.form1.objeto2);
//  document.form1.submit();
}
function js_relatorio2(){
  var F = document.form1;
  var datai = "";
  var dataf = "";
  if(F.datai_dia.value != "" && F.datai_mes.value != "" && F.datai_ano.value != ""){
    datai = F.datai_ano.value+'-'+F.datai_mes.value+'-'+F.datai_dia.value;
  }
  if(F.dataf_dia.value != "" && F.dataf_mes.value != "" && F.dataf_ano.value != ""){
    dataf = F.dataf_ano.value+'-'+F.dataf_mes.value+'-'+F.dataf_dia.value;
  }
  if(datai == "" && dataf == ""){
    alert("Informe o período de admissão.");
    F.datai_dia.focus();
  }else{
    qry = "?datai="+datai;
    qry+= "&dataf="+dataf;
    qry+= "&ordem="+F.ordem.value;
    qry+= "&regime="+F.regime.value;
    if(F.listaponto.checked == true){
      qry+= "&fixo=s";
    }else{
      qry+= "&fixo=n";
    }
   qry+= "&lota="+F.lota.value;
//    jan = window.open('rec2_gradeefetividade002.php'+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scro
//    jan.moveTo(0,0);
  }
}

function js_viewFileDownload(filePath) {
    const fileName = 'relatorio-assentamento-por-periodo';
    oDownload = new DBDownload()
    oDownload.addFile(filePath, fileName)
    oDownload.show();
}
</script>
