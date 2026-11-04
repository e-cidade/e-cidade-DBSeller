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
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));

//$oCalendario = CalendarioRepository::getCalendarioByCodigo($iCalendario[0]);
//$oEscola = EscolaRepository::getEscolaByCodigo($iEscola);
//$aEtapas = array(0); // o valor da etapa sempre será 0 pois foi requerido escolher a opção todas que possui value = 0 no edu2_QuadroDeVagas001.php.
//$aEscola = array($oEscola);
//$aCalendario = array($oCalendario);


if ($iEscola == 0) { // Wallace 2018-06-13 Se escola estiver  vazia serão selecionadas todas as escolas da rede
    $aCalendario = array();
    $aCalendario = explode(",", $iCalendario); //Wallace 2018-06-13 Transforma calendário em um array com os nomes dos calendários
    $iCalendario = "";

    for ($i = 0; $i < sizeof($aCalendario); $i++) { //Wallace 2018-06-13 Com os nomes dos calendários, montará um where para ser usado no banco de dados
        if ($i == 0) {
            $iCalendario = "'$aCalendario[$i]'";
        } else {
            $iCalendario .= " OR ed52_c_descr='" . $aCalendario[$i] . "' ";
        }

        if ($i == sizeof($aCalendario) - 1) {
            $iCalendario .= ")";
        }
    }

    $aEscola = array();
    $sCampos = " Distinct ed18_i_codigo,ed52_i_codigo,ed18_codigoreferencia,ed18_c_nome";
    $sWhere = " ";
    $sWhere .= " (ed52_c_descr  = $iCalendario ";
    $sOrder = " ed18_codigoreferencia"; //wallace 2018-06-15 Ordenar por referência.

    $oDaoTurma = new cl_turma();
    $sSql = $oDaoTurma->sql_query_turmaMatriculasAtivas(null, $sCampos, $sOrder, $sWhere);// Wallace 2018-06-13 Traz apenas escolas do calendário que possuem matrícula.
    $rs = db_query($sSql);

    if ($rs && pg_num_rows($rs) > 0) {

        $iLinhas = pg_num_rows($rs);
        for ($i = 0; $i < $iLinhas; $i++) { // Wallace 2018-06-13 Aloca os códigos da escola e calendário da escola em seus respectivos arrays

            $codigosEscolasId[] = db_utils::fieldsMemory($rs, $i)->ed18_i_codigo;
            $codigosCalendariosId[] = db_utils::fieldsMemory($rs, $i)->ed52_i_codigo;
        }

        for ($i = 0; $i < sizeof($codigosEscolasId); $i++) { //Wallace 2018-06-13 Com os códigos alocados busca o objeto de cada Escola e Calendário da Escola.

            $oEscola = EscolaRepository::getEscolaByCodigo($codigosEscolasId[$i]);
            $aEscola[$i] = $oEscola; // Array de objetos Escola

            $oCalendario = CalendarioRepository::getCalendarioByCodigo($codigosCalendariosId[$i]);
            $aCalendario[$i] = $oCalendario; //Array de objetos Calendário
        }
    }

    $aEtapas = array(); // Wallace 2018-06-13 Zera array Etapa
    $sCampos = " distinct ed11_i_codigo";
    $sWhere = " ";
    $sWhere .= " (ed52_c_descr  = $iCalendario"; // Wallace 2018-06-13 Busca todas as etapas referentes ao calendário selecionados

    $oDaoTurma = new cl_turma();
    $sSql = $oDaoTurma->sql_query_turmaMatriculasAtivas(null, $sCampos, null, $sWhere);
    $rs = db_query($sSql);

    if ($rs && pg_num_rows($rs) > 0) {
        $iLinhas = pg_num_rows($rs);
        for ($i = 0; $i < $iLinhas; $i++) { // Wallace 2018-06-13 Aloca os códigos das Etapas que no caso sempre serão todas
            $aEtapas[$i] = db_utils::fieldsMemory($rs, $i)->ed11_i_codigo;

        }
    }

} else { // Wallace 2018-06-13 Senão  a pesquisa será feita com a escola selecionada
    $oEscola = EscolaRepository::getEscolaByCodigo($iEscola);
    $aCalendario = array();
    $aCalendario = explode(",", $iCalendario);//Wallace 2018-06-13 Transforma calendário em um array com os nomes dos calendários
    $iCalendario = "";

    for ($i = 0; $i < sizeof($aCalendario); $i++) {  //Wallace 2018-06-13 Com os nomes dos calendários, montará um where para ser usado no banco de dados
        if ($i == 0) {

            $iCalendario = "'$aCalendario[$i]'";
        } else {

            $iCalendario .= " OR ed52_c_descr='" . $aCalendario[$i] . "' ";
        }

        if ($i == sizeof($aCalendario) - 1) {

            $iCalendario .= ") ";
        }
    }

    $sCampos = " Distinct ed18_i_codigo,ed52_i_codigo,ed18_codigoreferencia,ed18_c_nome";
    $sWhere = "     ed57_i_escola     = $iEscola";
    $sWhere .= " AND  (ed52_c_descr  = $iCalendario ";
    $sOrder = " ed18_codigoreferencia"; //wallace 2018-06-15 Ordenar por referência.

    $oDaoTurma = new cl_turma();
    $sSql = $oDaoTurma->sql_query_turmaMatriculasAtivas(null, $sCampos, $sOrder, $sWhere);// Wallace 2018-06-13 Traz apenas escolas do calendário que possuem matrícula.
    $rs = db_query($sSql);

    if ($rs && pg_num_rows($rs) > 0) { // Wallace 2018-06-13 Aloca os códigos da escola e calendário da escola em seus respectivos arrays

        $iLinhas = pg_num_rows($rs);
        for ($i = 0; $i < $iLinhas; $i++) { //Wallace 2018-06-13 Com os códigos alocados busca o objeto de cada Escola e Calendário da Escola.
            $codigosEscolasId[$i] = db_utils::fieldsMemory($rs, $i)->ed18_i_codigo;
            $codigosCalendariosId[$i] = db_utils::fieldsMemory($rs, $i)->ed52_i_codigo;
        }

        for ($i = 0; $i < sizeof($codigosCalendariosId); $i++) {

            $oEscola = EscolaRepository::getEscolaByCodigo($codigosEscolasId[$i]);
            $aEscola[$i] = $oEscola; // Array de objetos Escola
            $oCalendario = CalendarioRepository::getCalendarioByCodigo($codigosCalendariosId[$i]);
            $aCalendario[$i] = $oCalendario;//Array de objetos Calendário
        }

    }

    $aEtapas = array();// Wallace 2018-06-13 Zera array Etapa
    $sCampos = " distinct ed11_i_codigo";
    $sWhere = "     ed57_i_escola     = $iEscola";
    $sWhere .= " AND  (ed52_c_descr  = $iCalendario "; // Wallace 2018-06-13 Busca todas as etapas referentes ao calendário selecionados

    $oDaoTurma = new cl_turma();
    $sSql = $oDaoTurma->sql_query_turmaMatriculasAtivas(null, $sCampos, null, $sWhere);
    $rs = db_query($sSql);

    if ($rs && pg_num_rows($rs) > 0) { // Wallace 2018-06-13 Aloca os códigos das Etapas que no caso sempre serão todas

        $iLinhas = pg_num_rows($rs);
        for ($i = 0; $i < $iLinhas; $i++) {
            $aEtapas[] = db_utils::fieldsMemory($rs, $i)->ed11_i_codigo;
        }
    }
}

$oRelatorio = new RelatorioQuadroDeVagas($aCalendario, $aEtapas, $aEscola, false, $iTipoRelatorio);
$oRelatorio->gerarRelatorio();
