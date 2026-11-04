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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_"."c"."onecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);

$oPost    = db_utils::postMemory($_POST);

$iAnoUsu  = db_getsession("DB_anousu");

$anofolha = DBPessoal::getAnoFolha();
$mesfolha = DBPessoal::getMesFolha();

$clinssirf = new cl_inssirf;

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<br>
<table width="100%" style="margin-top: 10px;" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td height="430" align="center" valign="top" bgcolor="#CCCCCC">

            <form id="form1" name="form1" method="post" action="">

                <center>

                    <fieldset style="width:650px"><legend><strong>Gerar Arquivo Cálculo Atuarial</strong></legend>

                        <table border="0" align="left">

                            <tr>
                                <td>
                                    <strong>Ano / Mês:</strong>
                                </td>
                                <td>
                                    <?php
                                    db_input("anofolha", 10,0, true, 'text', 1);
                                    echo "/";
                                    db_input("mesfolha", 10,0, true, 'text', 1);
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong>Arquivo:</strong></td>
                                <td>
                                    <?php

                                    $aArquivo = array(
                                        "1" => "Ativos",
                                        "2" => "Aposentados",
                                        "3" => "Pensionistas",
                                        "4" => "Inativos Tesouro",
                                        "5" => "Pensionistas Tesouro"
                                    );
                                    $aArquivo = array(
                                        'A' => 'Ativo',
                                        'I' => 'Inativos',
                                        'P' => 'Pensionistas',
                                        'E' => 'Exonerados'
                                    );
                                    db_select('iArquivo', $aArquivo, true, 4 );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="Tabela de Previdência">
                                    <strong>Tabela de Previdência:&nbsp;</strong>
                                </td>
                                <td>
                                    <?php
                                    $sSqlTabPrev = $clinssirf->sql_query_file(null,db_getsession('DB_instit'),"distinct (r33_codtab - 2) as r33_codtab,r33_nome","r33_codtab"," r33_anousu = ".$anofolha." and r33_mesusu = ".$mesfolha." and r33_codtab > 2");
                                    $res = $clinssirf->sql_record($sSqlTabPrev);
                                    db_selectrecord('tabprev', $res, true, 4);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <?php
                                    db_ancora("<b>Seleção de Professores:</b>","js_pesquisaprof(true)",1);
                                    ?>
                                </td>
                                <td align="left">
                                    <?php
                                    db_input('r44_selec',10,null ,true,'text',2,'onchange="js_pesquisaprof(false)"');
                                    db_input('r44_descr',40,null ,true,'text',3,'');
                                    ?>
                                </td>
                            </tr>
                        </table>

                    </fieldset>

                    <input style="margin-top: 10px;" type="submit" id="gerar" name='gerar' value="Processar">

                </center>

            </form>

        </td>
    </tr>
</table>
</center>
</body>
</html>

<script>

  function js_pesquisaprof(mostra){

    if(mostra==true){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_selecao','func_selecao.php?funcao_js=parent.js_mostraprof1|r44_selec|r44_descr','Pesquisa',true);
    }else{
      if(document.form1.r44_selec.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_selecao','func_selecao.php?pesquisa_chave='+document.form1.r44_selec.value+'&funcao_js=parent.js_mostraprof','Pesquisa',false);
      }else{
        document.form1.r44_descr.value = '';
      }
    }
  }

  function js_mostraprof(chave,erro){
    document.form1.r44_descr.value = chave;
    if(erro==true){
      document.form1.r44_selec.focus();
      document.form1.r44_selec.value = '';
    }
  }

  function js_mostraprof1(chave1,chave2){

    document.form1.r44_selec.value = chave1;
    document.form1.r44_descr.value   = chave2;
    db_iframe_selecao.hide();
  }

</script>

<script type="text/javascript">

  function js_detectaarquivo(arquivo, sArquivo) {

    var listagem = arquivo + "#Download arquivo: " + sArquivo + " |";
    js_montarlista( listagem, "form1" );
  }

</script>

<?php

if ( isset($oPost->gerar) ) {

    $iInstit          = db_getsession('DB_instit');
    $iDBinstit        = $iInstit;
    $aWhere           = array(" 1 = 1 ");

    $sDBdatausu       = date('Y-m-d',db_getsession("DB_datausu"));
    $ano              = $oPost->anofolha;
    $mes              = $oPost->mesfolha;

    $sSqlInstituicao  = " select * from db_config where codigo = {$iDBinstit} ";
    $rsInstit         = db_query($sSqlInstituicao);
    $oDadosInstit     = db_utils::fieldsMemory($rsInstit, 0);

    $clselecao        = null;
    $sCaseProfessores = "1=2";
    $sWhere           = "rh05_seqpes is null";
    $sSelecao         = '';

    if ($_POST["r44_selec"] != ''){

        $clselecao = new cl_selecao;
        $rsselec   =  $clselecao->sql_record($clselecao->sql_query($r44_selec, db_getsession("DB_instit")));
        db_fieldsmemory($rsselec,0);

        $sCaseProfessores  = "$r44_where";
        $sSelecao          = " AND $r44_where ";
        $aWhere[]          = "$r44_where";
    }

    $aCampos               = array();
    $sNomeArquivoProcessar = "";

    $sSql  = "select *, ";

    switch ($oPost->iArquivo) {

        case "A" :

            $sNomeArquivoProcessar = "ativos";

            $sSqlSoma  = "select round(sum(r14_valor),2)";
            $sSqlSoma .= "  from gerfsal ";
            $sSqlSoma .= " where r14_regist = rh01_regist";
            $sSqlSoma .= "   and r14_anousu = {$ano}";
            $sSqlSoma .= "   and r14_mesusu = {$mes}";
            $sSqlSoma .= "   and r14_instit = {$iInstit}";
            $sSqlSoma .= "   and r14_rubric = 'R985' ";

            $aWhere[]  = " rh30_vinculo = 'A' ";
            $aWhere[]  = " rh02_tbprev  = {$oPost->tabprev} ";
            $aWhere[]  = " rh05_recis is null ";

            $sSql     .= "       (select round(sum(h16_dtterm - h16_dtconc)/30) as tempo_contrib";
            $sSql     .= "          from assenta ";
            $sSql     .= "               inner join tipoasse on h12_codigo = h16_assent";
            $sSql     .= "         where h12_reltot > 0 ";
            $sSql     .= "           and h16_regist = rh01_regist";
            $sSql     .= "        having sum(h16_dtterm - h16_dtconc) <> 0 ) as tempocontrib,";
            $sSql     .= "       valor as salario";

            $aCampos[] = "composicao_massa";
            $aCampos[] = "poder";
            $aCampos[] = "orgaoentidadepoder";
            $aCampos[] = "rh01_regist";
            $aCampos[] = "z01_nome";
            $aCampos[] = "rh01_nasc";
            $aCampos[] = "rh01_sexo";
            $aCampos[] = "estcivil";
            $aCampos[] = "dataingressomunicipio";
            $aCampos[] = "dataingressoefetivo";
            $aCampos[] = "dataadmiscarreira";
            $aCampos[] = "dataassumiuultimocargo";
            $aCampos[] = "dataingressoregimeproprio";
            $aCampos[] = "tempocontribuicaoanteriorpublico";
            $aCampos[] = "tempocontribuicaoanteriorprivado";
            $aCampos[] = "cargo";
            $aCampos[] = "atividade";
            $aCampos[] = "situacao";
            $aCampos[] = "remuneracao_contribuicao";
            $aCampos[] = "total_dependente";
            $aCampos[] = "relacao_dependencia_vitalicia";
            $aCampos[] = "dependente_vitalicio_valido";
            $aCampos[] = "nasc_dependencia_vitalicia";
            $aCampos[] = "sexo_dependente_vitalicio";
            $aCampos[] = "relacao_dependencia_temporaria";
            $aCampos[] = "dependente_valido_temporario";
            $aCampos[] = "nasc_dependente_temporario";
            $aCampos[] = "sexo_dependente_temporario";
            $aCampos[] = "remuneracao_contribuicao";

            break;

        case "I" :

            $sNomeArquivoProcessar = "aposentados";

            $sAndSoma     .=  "and r14_rubric  = 'R985'";

            $sSqlSoma      = "select round(sum(r14_valor),2)";
            $sSqlSoma     .= "  from gerfsal ";
            $sSqlSoma     .= " where r14_regist = rh01_regist";
            $sSqlSoma     .= "   and r14_anousu = {$ano}";
            $sSqlSoma     .= "   and r14_mesusu = {$mes}";
            $sSqlSoma     .= "   and r14_instit  = {$iInstit}";
            $sSqlSoma     .= "   and r14_pd = 1";

            $sSql .= "       valor  as valor";

            $aWhere[]      = " rh02_tbprev  = {$oPost->tabprev} ";
            $aWhere[]      = " rh30_vinculo = 'I' ";
            $aWhere[]      = " rh05_recis is not null ";

            $aCampos[]     = "composicao_massa";
            $aCampos[]     = "poder";
            $aCampos[]     = "orgaoentidadepoder";
            $aCampos[]     = "rh01_regist";
            $aCampos[]     = "z01_nome";
            $aCampos[]     = "rh01_nasc";
            $aCampos[]     = "rh01_sexo";
            $aCampos[]     = "estcivil";
            $aCampos[]     = "dataingressomunicipio";
            $aCampos[]     = "tipobeneficio";
            $aCampos[]     = "datainiciobeneficio";
            $aCampos[]     = "tempototalcontribuicaocalculo";
            $aCampos[]     = "tempototalcontribuicaooutrosregimes";
            $aCampos[]     = "tempocontribuicaorpps";
            $aCampos[]     = "beneficiobrutoatual";
            $aCampos[]     = "compensacaoprevidenciamensal";
            $aCampos[]     = "sebeneficiopormediaremuneracao";
            $aCampos[]     = "sebeneficiopormediaintegral";
            $aCampos[]     = "cargo";
            $aCampos[]     = "situacao";
            $aCampos[]     = "total_dependente";
            $aCampos[]     = "relacao_dependencia_vitalicia";
            $aCampos[]     = "dependente_vitalicio_valido";
            $aCampos[]     = "nasc_dependencia_vitalicia";
            $aCampos[]     = "sexo_dependente_vitalicio";
            $aCampos[]     = "relacao_dependencia_temporaria";
            $aCampos[]     = "dependente_valido_temporario";
            $aCampos[]     = "nasc_dependente_temporario";
            $aCampos[]     = "sexo_dependente_temporario";
            $aCampos[]     = "remuneracao_contribuicao";

            break;

        case "P" :
            $sNomeArquivoProcessar = "pensionistas";

            $sSqlSoma      = "select round(sum(r14_valor),2)";
            $sSqlSoma     .= "  from gerfsal ";
            $sSqlSoma     .= " where r14_regist = rh01_regist";
            $sSqlSoma     .= "   and r14_anousu = {$ano}";
            $sSqlSoma     .= "   and r14_mesusu = {$mes}";
            $sSqlSoma     .= "   and r14_instit  = {$iInstit} ";
            $sSqlSoma     .= "   and r14_pd = 1";

            $aWhere[]      = " rh30_vinculo = 'P' ";

            $sSql         .= " valor  as valor";

            $aCampos[]     = "composicao_massa";
            $aCampos[]     = "poder";
            $aCampos[]     = "orgaoentidadepoder";
            $aCampos[]     = "rh01_regist";
            $aCampos[]     = "z01_nome";
            $aCampos[]     = "dataingressomunicipio";
            $aCampos[]     = "situacao";
            $aCampos[]     = "datainiciopensao";
            $aCampos[]     = "cargo";
            $aCampos[]     = "numerodepensionista";
            $aCampos[]     = "matriculapensionista";
            $aCampos[]     = "nomepensionista";
            $aCampos[]     = "condicaopensionista";
            $aCampos[]     = "datanascimentopensionista";
            $aCampos[]     = "sexopensionista";
            $aCampos[]     = "tipobeneficiopensionista";
            $aCampos[]     = "beneficiobrutoatualpensionista";
            $aCampos[]     = "compensacaoprevmensalpensionista";

            break;

        case "E" :

            $sNomeArquivoProcessar = "exonerado";
            $sSqlSoma              = "0::integer";

            $sSql                 .= " valor  as valor";

            $aWhere[]              = " rh02_tbprev  = {$oPost->tabprev} ";
            $aWhere[]              = " rh30_vinculo = 'E' ";
            $aWhere[]              = " rh05_recis is not null ";

            $aCampos[]             = "rh01_regist";
            $aCampos[]             = "z01_nome";
            $aCampos[]             = "rh01_nasc";
            $aCampos[]             = "rh01_sexo";
            $aCampos[]             = "dataingressoefetivo";
            $aCampos[]             = "dataexoneracao";

            break;
    }

    $sWhere = implode(" and ", $aWhere);

    $sSql  .= "  from ";
    $sSql  .= "      (";
    $sSql  .= "      select z01_nome,";
    $sSql  .= "             rh01_regist,";
    $sSql  .= "             rh01_sexo,";
    $sSql  .= "             case when {$sCaseProfessores} then 'P' else 'O' end ";
    $sSql  .= "             as prof,";
    $sSql  .= "             rh01_admiss,";
    $sSql  .= "             rh01_nasc  ,";
    $sSql  .= "             (select rh31_dtnasc";
    $sSql  .= "                from rhdepend";
    $sSql  .= "               where rh31_gparen = 'C' and rh31_regist = rh01_regist limit 1";
    $sSql  .= "              ) as conjuge,";
    $sSql  .= "              (select rh31_dtnasc";
    $sSql  .= "                 from rhdepend";
    $sSql  .= "                where rh31_gparen = 'F' and rh31_regist = rh01_regist order by rh31_dtnasc desc limit 1";
    $sSql  .= "              ) as filho,";
    $sSql  .= "              rh05_recis," ;
    $sSql  .= "     '{$oDadosInstit->db21_tipopoder}' as poder, ";

    $sSql  .= "     '{$oDadosInstit->nomeinst}' as orgaoentidadepoder, ";

    $sSql  .= "     case                          ";
    $sSql  .= "       when rh01_estciv = 3 then 4 ";
    $sSql  .= "       when rh01_estciv = 4 then 3 ";
    $sSql  .= "       else rh01_estciv            ";
    $sSql  .= "     end    as estcivil , ";

    $sSql  .= "     '1' as composicao_massa , ";

    $sSql  .= "    case ";
    $sSql  .= "      when rh02_deficientefisico is true then 2 ";
    $sSql  .= "      when rh02_ocorre in ('01', '02', '03') then 3 ";
    $sSql  .= "      else 1 ";
    $sSql  .= "    end as situacao, ";

    $sSql  .= "  ( select count(*) ";
    $sSql  .= "      from rhdepend ";
    $sSql  .= "     where rh31_regist = rh01_regist ";
    $sSql  .= "  ) as total_dependente, ";

    $sSql  .= "     rh01_admiss as dataingressomunicipio, ";
    $sSql  .= "     rh01_admiss as dataingressoefetivo, ";
    $sSql  .= "     rh01_admiss as dataadmiscarreira, ";
    $sSql  .= "     rh01_admiss as dataassumiuultimocargo, ";
    $sSql  .= "     rh01_admiss as dataingressoregimeproprio, ";
    $sSql  .= "     '' as tempocontribuicaoanteriorpublico,";
    $sSql  .= "     '' as tempocontribuicaoanteriorprivado,";

    $sSql  .= "     '' as tempocontribuicaoanterior, ";
    $sSql  .= "     '' as atividade, ";

    $sSql  .= "  case ";
    $sSql  .= "    when rh02_rhtipoapos in ('0601', '0603') then 1 ";
    $sSql  .= "    when rh02_rhtipoapos in ('0101', '0102') then 2 ";
    $sSql  .= "    when rh02_rhtipoapos in ('0102') then 3 ";
    $sSql  .= "    else 4 ";
    $sSql  .= "  end as tipobeneficio, ";

    $sSql  .= "  '' as datainiciobeneficio, ";
    $sSql  .= "  '' as tempototalcontribuicaocalculo, ";
    $sSql  .= "  '' as tempototalcontribuicaooutrosregimes,";
    $sSql  .= "  '' as tempocontribuicaorpps, ";
    $sSql  .= "  '' as compensacaoprevidenciamensal, ";
    $sSql  .= "  '' as sebeneficiopormediaremuneracao, ";
    $sSql  .= "  '' as sebeneficiopormediaintegral, ";
    $sSql  .= "  rh37_descr  as cargo, ";
    $sSql  .= "  rh05_recis as datainiciopensao, ";
    $sSql  .= "  rh21_regpri as numerodepensionista,";

    $sSql  .= "  rh01_regist as matriculapensionista, ";
    $sSql  .= "  z01_nome    as nomepensionista,";
    $sSql  .= "  'válido'    as condicaopensionista,";
    $sSql  .= "  rh01_nasc   as datanascimentopensionista, ";
    $sSql  .= "  rh01_sexo   as sexopensionista, ";

    $sSql  .= "case";
    $sSql  .= "  when rh02_validadepensao is not null then 'Temporário'";
    $sSql  .= "  else 'Vitalício'";
    $sSql  .= "end as tipobeneficiopensionista,";

    $sSql  .= " '' as compensacaoprevmensalpensionista, ";

    $sSql  .= "  rh05_recis as dataexoneracao, ";

    $sSql  .= "   (select sum(r14_valor)  as r14_valor";
    $sSql  .= "      from gerfsal  ";
    $sSql  .= "     where r14_anousu = rh02_anousu";
    $sSql  .= "       and r14_mesusu = rh02_mesusu";
    $sSql  .= "       and r14_regist = rh01_regist";
    $sSql  .= "       and r14_pd = 1 ) as beneficiobrutoatualpensionista , ";

    $sSql  .= "   (select sum(r14_valor)  as r14_valor";
    $sSql  .= "      from gerfsal  ";
    $sSql  .= "     where r14_anousu = rh02_anousu";
    $sSql  .= "       and r14_mesusu = rh02_mesusu";
    $sSql  .= "       and r14_regist = rh01_regist";
    $sSql  .= "       and r14_pd = 1 ) as beneficiobrutoatual , ";

    // dados dependente vitalicio
    $sSql  .= " (select case          ";
    $sSql  .= "              when rh31_gparen = 'C' then 1 ";
    $sSql  .= "              when rh31_gparen = 'F' then 2 ";
    $sSql  .= "              when rh31_gparen = 'P' then 6 ";
    $sSql  .= "              when rh31_gparen = 'M' then 6 ";
    $sSql  .= "              else 9 ";
    $sSql  .= "            end as rh31_gparen ";
    $sSql  .= "       from rhdepend   ";
    $sSql  .= "      where rh31_regist = rh01_regist";
    $sSql  .= "        and rh31_depend = 'S'  ";
    $sSql  .= "  ) as relacao_dependencia_vitalicia,    ";
    $sSql  .= "  '??' as dependente_vitalicio_valido, ";
    $sSql  .= " (select rh31_dtnasc as nasc_dependente_vitalicio        ";
    $sSql  .= "       from rhdepend   ";
    $sSql  .= "      where rh31_regist = rh01_regist";
    $sSql  .= "        and rh31_depend = 'S'  ";
    $sSql  .= "  ) as nasc_dependencia_vitalicia,    ";
    $sSql  .= "  (     select dp01_sexo ";
    $sSql  .= "          from rhdepend ";
    $sSql  .= "    inner join  rhdependeplug on dp01_rhdepend = rh31_codigo ";
    $sSql  .= "        where rh31_regist = rh01_regist ";
    $sSql  .= "          and rh31_depend = 'S' ";
    $sSql  .= "  ) as sexo_dependente_vitalicio, ";

    // dados de dependente temporario
    $sSql  .= " (select case          ";
    $sSql  .= "              when rh31_gparen = 'C' then 1 ";
    $sSql  .= "              when rh31_gparen = 'F' then 2 ";
    $sSql  .= "              when rh31_gparen = 'P' then 6 ";
    $sSql  .= "              when rh31_gparen = 'M' then 6 ";
    $sSql  .= "              else 9 ";
    $sSql  .= "            end as rh31_gparen ";
    $sSql  .= "       from rhdepend   ";
    $sSql  .= "      where rh31_regist = rh01_regist";
    $sSql  .= "        and rh31_depend <> 'S' order by rh31_dtnasc desc limit 1  ";
    $sSql  .= "  ) as relacao_dependencia_temporaria,    ";
    $sSql  .= "  '??' as dependente_valido_temporario, ";
    $sSql  .= " (select rh31_dtnasc         ";
    $sSql  .= "       from rhdepend   ";
    $sSql  .= "      where rh31_regist = rh01_regist";
    $sSql  .= "        and rh31_depend <> 'S' order by rh31_dtnasc desc limit 1  ";
    $sSql  .= "  ) as nasc_dependente_temporario,    ";
    $sSql  .= "  (     select dp01_sexo ";
    $sSql  .= "          from rhdepend ";
    $sSql  .= "    inner join  rhdependeplug on dp01_rhdepend = rh31_codigo ";
    $sSql  .= "        where rh31_regist = rh01_regist ";
    $sSql  .= "          and rh31_depend <> 'S'  order by rh31_dtnasc desc limit 1 ";
    $sSql  .= "  ) as sexo_dependente_temporario, ";

    $sSql  .= "   (select sum(r14_valor)  as r14_valor";
    $sSql  .= "      from gerfsal";
    $sSql  .= "     where  r14_anousu = rh02_anousu";
    $sSql  .= "       and r14_mesusu = rh02_mesusu";
    $sSql  .= "       and r14_regist = rh01_regist";
    $sSql  .= "       and r14_rubric = 'R992' ) as remuneracao_contribuicao , ";

    $sSql  .= "              rh30_vinculo, ";
    $sSql  .= "       ({$sSqlSoma}) as valor";
    $sSql  .= "              from rhpessoal ";
    $sSql  .= "                   inner join rhpessoalmov   on rh01_regist = rh02_regist ";
    $sSql  .= "                                            and rh02_anousu = {$ano}      ";
    $sSql  .= "                                            and rh02_mesusu = {$mes}      ";
    $sSql  .= "                                            and rh02_instit = {$iInstit}  ";
    $sSql  .= "                    left join rhfuncao       on rh37_funcao = rh02_funcao ";
    $sSql  .= "                                            and rh02_instit = rh37_instit ";
    $sSql  .= "                    left join rhpescargo     on rh20_seqpes = rh02_seqpes ";
    $sSql  .= "                                            and rh02_instit = rh20_instit ";
    $sSql  .= "                    left join rhcargo        on rh20_cargo  = rh04_codigo ";
    $sSql  .= "                                            and rh04_instit = rh20_instit ";

    $sSql  .= "                   inner join rhregime       on rh30_codreg = rh02_codreg ";
    $sSql  .= "                                            and rh30_instit = rh02_instit ";
    $sSql  .= "                   left join rhpesrescisao   on rh02_seqpes = rh05_seqpes ";
    $sSql  .= "                   inner join cgm            on rh01_numcgm = z01_numcgm  ";
    $sSql  .= "                    left join rhpesorigem    on rh01_regist = rh21_regist ";

    $sSql  .= "             where {$sWhere}";
    $sSql  .= "             order by z01_nome ";
    $sSql  .= "             ) as x ";

    $sCampos = implode(", ", $aCampos);

    $sSql    = " select {$sCampos} from ({$sSql}) as dd ";

    $rsLista = db_query($sSql);
    $iTotal  = pg_numrows($rsLista);

    $sArquivo          = "arquivoatuarial_{$sNomeArquivoProcessar}_". date("dmY"). "_.csv";
    $sNomeArquivo      = "tmp/{$sArquivo}";
    $rsArquivoBanco    = fopen($sNomeArquivo, 'w');

    if ($iTotal <= 0) {

        db_msgbox("Nenhum Registro Encontrado Para o Filtro Selecionado.");
        db_redireciona("pes4_calculoatuarialgeracaoarquivos001.php");
    }

    if ($iTotal > 0 ) {

        for ( $iInicio = 0; $iInicio < $iTotal;  $iInicio++ ) {

            $oConsulta = db_utils::fieldsMemory($rsLista, $iInicio);
            $aConsulta = (array)$oConsulta;
            $sLinha    = implode(";", $aConsulta);
            fputs($rsArquivoBanco, $sLinha."\r".PHP_EOL);
        }
    }

    fclose($rsArquivoBanco);

    echo "
          <script>
            js_detectaarquivo('$sNomeArquivo', '$sArquivo');
          </script>
         ";
}

?>
