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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("classes/db_orctiporec_classe.php"));

$clrotulo = new rotulocampo();
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');
db_postmemory($HTTP_POST_VARS);
?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/classes/DBViewFiltroRecursos.classe.js"></script>

<script>
function js_emite(opcao,origem) {

  obj = document.form1;
  sel_instit  = new Number(document.form1.db_selinstit.value);

  var recursos = '';
  if (filtroRecursos !== false) {
      recursos = filtroRecursos.getListaRecursos();
  }


  if(sel_instit == 0){
    alert('Você não escolheu nenhuma Instituição. Verifique!');
    return false;
  }
  if (opcao == 3){
     var data1 = new Date(document.form1.DBtxt21_ano.value,document.form1.DBtxt21_mes.value,document.form1.DBtxt21_dia.value,0,0,0);
     var data2 = new Date(document.form1.DBtxt22_ano.value,document.form1.DBtxt22_mes.value,document.form1.DBtxt22_dia.value,0,0,0);
     if(data1.valueOf() > data2.valueOf()){
       alert('Data inicial maior que data final. Verifique!');
       return false;
     }
     perini = document.form1.DBtxt21_ano.value+'-'+document.form1.DBtxt21_mes.value+'-'+document.form1.DBtxt21_dia.value;
     perfin = document.form1.DBtxt22_ano.value+'-'+document.form1.DBtxt22_mes.value+'-'+document.form1.DBtxt22_dia.value;
  }else if (opcao == 2){
     if(document.form1.mesfin.value == 0){
       mesfinal = 12;
     }else if(document.form1.mesfin.value < 10){
       mesfinal = '0'+document.form1.mesfin.value;
     }else if(document.form1.mesfin.value == 'mes'){
       alert('Mês final do intervalo invalido.Verifique!');
       return false
     }else{
       mesfinal = document.form1.mesfin.value;
     }

                if (document.form1.mesini.value == 0) {
                    mesinicial = 12;
                } else if (document.form1.mesini.value < 10) {
                    mesinicial = '0' + document.form1.mesini.value;
                } else {
                    mesinicial = document.form1.mesini.value;
                }

                perini = <?= db_getsession("DB_anousu") ?> + '-' + mesinicial + '-01';
                perfin = <?= db_getsession("DB_anousu") ?> + '-' + mesfinal + '-01';
            } else {
                perini = <?= db_getsession("DB_anousu") ?> + '-01-01';
                perfin = <?= db_getsession("DB_anousu") ?> + '-01-01';
            }
            <?php
            if (db_getsession("DB_anousu") > 2007) {
            ?>
            var sFiltro = Object.toJSON((window.CurrentWindow || parent.CurrentWindow).corpo.iframe_filtro.getFiltros());
            var sUrl = 'con2_balancrece002_2008.php?';
            sUrl += 'impressao=' + obj.impressao.value;
            sUrl += '&estrut_inicial=' + obj.estrut_inicial.value;
            sUrl += '&previsao=' + obj.previsao.value;
            sUrl += '&apresentar=' + obj.apresentarRecurso.value;
            sUrl += '&recursos_selecionados=' + recursos;
            sUrl += '&perfin=' + perfin + '&perini=' + perini + '&opcao=' + opcao;
            sUrl += '&origem=' + origem + '&db_selinstit=' + document.form1.db_selinstit.value;
            sUrl += '&nivel_agrupar=' + document.form1.nivel_agrupar.value
            sUrl += '&filtros=' + sFiltro;
            jan = window.open(sUrl, '',
                'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
            <?php
            } else {
            ?>
            jan = window.open('con2_balancrece002.php?impressao=' + obj.impressao.value + '&recursos_selecionados=' + recursos + '&perfin=' + perfin + '&perini=' + perini + '&opcao=' + opcao + '&origem=' + origem + '&db_selinstit=' + document.form1.db_selinstit.value + '&recurso=0', '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
            <?php
            }
            ?>
            jan.moveTo(0, 0);
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">

<table align="center" border="0">
    <form name="form1" id='frmRelatorio' method="post" action="">
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td align="center" colspan="3">
                <?php
                db_selinstit('', 300, 100);
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="1" align="right"><strong>Impressão:&nbsp;</strong></td>
            <td colspan="2">
                <select name=impressao>
                    <option value=retrato>Retrato</option>
                    <option value=paisagem>Paisagem</option>
                </select>
        </tr>
        <tr>
            <td colspan="1" align="right"><strong>Estruturais:&nbsp;</strong></td>
            <td colspan="2"><input name="estrut_inicial"></td>
        </tr>
        <td colspan="1" align="right"><strong>Mostrar apenas receitas com movimentação:&nbsp;</strong></td>
        <td colspan="2">
            <select name="previsao">
                <option value="sim" checked>Sim</option>
                <option value="nao">Não</option>
            </select>
        </td>
        </tr>
        <?php
        if (db_getsession("DB_anousu") > 2007) {
            ?>

            <tr>
                <td colspan="1" align="right"><strong>Tipo de Agrupamento das Deduções:&nbsp;</strong></td>
                <td colspan="2">
                    <select name="nivel_agrupar">
                        <option value="0">Lista Deduções Grupo 9</option>
                        <option value="1">Não Imprime as Deduções</option>
                        <option value="2">Deduções no Mesmo Grupo</option>
                    </select>
            </tr>

            <tr>
                <td colspan="1" align="right"><strong>Apresentar:&nbsp;</strong></td>
                <td colspan="2">
                    <select id="apresentarRecurso" name="apresentarRecurso">
                        <option selected value="3">Fonte Recurso</option>
                        <option value="9">Subrecurso</option>
                        <option value="7">Código Siconfi</option>
                    </select>
            </td>

            <?php if (FONTE_RECURSO_UNIAO) { ?>
                <tr>
                    <td colspan="1" align="right"><strong>Fonte de Recursos:&nbsp;</strong></td>
                    <td colspan=2>
                        <input type="button" name="gerar" id="gerar" value="Selecionar Recursos" onclick="abrirJanela()" />
                    </td>
                </tr>
            <?php } ?>

            <?php
        }
        db_selorcbalanco();
        ?>
    </form>
</table>
</body>
<script>

var filtroRecursos = false;

function abrirJanela() {

    if ( ! filtroRecursos) {
        filtroRecursos = new DBViewFiltroRecursos();
        filtroRecursos.construirJanela();
    }
    filtroRecursos.show();

}





</script>

</html>
