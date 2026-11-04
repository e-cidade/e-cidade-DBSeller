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

set_time_limit(0);
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_libdocumento.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("fpdf151/assinatura.php"));
require_once(modification("classes/db_propri_classe.php"));
require_once(modification("classes/db_certid_classe.php"));
require_once(modification("classes/db_cfiptu_classe.php"));
require_once(modification("fpdf151/pdf3.php"));
require_once(modification("model/cda.model.php"));
require_once(modification("libs/db_conecta.php"));
use ECidade\Tributario\Divida\Service\TermoInscricaoService;

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

if( empty($proced) ){
  $proced = 0;
}

if( empty($dataini) ){
  $dataini = "1970-01-01";
}

if( empty($datafim) ){
  $datafim = "2100-01-01";
}

if( empty($anoexerc) ){
  $anoexerc = 0;
}

if( empty($origem) ){
  $origem = 0;
}

/**
 * Incluir na termoinscr e termoinscrreg termos de dividas antigas
 */

$instit = db_getsession('DB_instit');
$dataInicial = implode('-', array_reverse(explode('/', $dataini)));
$dataFinal = implode('-', array_reverse(explode('/', $datafim)));

if (!isset($certid) || empty($certid)) {

  $sql = "select distinct v01_numpre
  from  divida
  left  join cgm       on v01_numcgm = z01_numcgm
  left join arrematric on v01_numpre = arrematric.k00_numpre
  left join arreinscr on v01_numpre = arreinscr.k00_numpre
  where ({$anoexerc} = 0 or v01_exerc = {$anoexerc})
  and v01_dtinsc between '{$dataInicial}' and '{$dataFinal}'
  and (({$origemtipo} = 0 and {$origem} = 0) or ({$origemtipo} = 1 and v01_numcgm = {$origem}) or ({$origemtipo} = 2 and k00_matric = {$origem}) or ({$origemtipo} = 3 and k00_inscr = {$origem}))
  and ({$proced} = 0 or v01_proced = {$proced})
  and v01_coddiv not in (select v93_coddiv from termoinscrreg)
  and v01_instit = {$instit}";

  $rsDivida = db_query($sql);
  $numeroDiv = pg_num_rows($rsDivida);

  if ($numeroDiv > 0) {
    for ($iNumero = 0; $iNumero < $numeroDiv; $iNumero++) {
        $numpre = db_utils::fieldsMemory($rsDivida, $iNumero)->v01_numpre;

        $sqlDiv = "select v01_coddiv, v01_vlrhis, v01_dtvenc, v01_dtinsc, v01_dtoper, v01_instit, v03_receit
            from divida
            inner join proced on
            v03_codigo = v01_proced
            where v01_numpre = $numpre and v01_coddiv not in (select v93_coddiv from termoinscrreg)";

        $rsDiv = db_query($sqlDiv);
        $registrosDiv = pg_num_rows($rsDiv);

        for ($iCodDiv = 0; $iCodDiv < $registrosDiv; $iCodDiv++) {

          $CodDivs = db_utils::fieldsMemory($rsDiv, $iCodDiv);

          $sqlUsuario = "select CASE WHEN v02_usuario IS NULL THEN 1 ELSE v02_usuario END AS usuario
              from divimportareg
              inner join divimporta on
              v02_divimporta = v04_divimporta
              right join divida on
              v01_coddiv = v04_coddiv
              where v01_coddiv = $CodDivs->v01_coddiv";
          $rsUsuario = db_query($sqlUsuario);
          $usuario = db_utils::fieldsMemory($rsUsuario, 0)->usuario;

          TermoInscricaoService::salvar($numpre, $CodDivs, $CodDivs->v03_receit, $CodDivs->v01_dtinsc, $usuario, $CodDivs->v01_instit);
      }
    }
  }
}

/**
 * Gerar termo
 */

try{

  db_inicio_transacao();
  $oGeradorTermo = new GeradorTermo();
  if( !empty($sNomeArquivo) ){

    $oGeradorTermo->gerar($certid, $certid1, $proced, $dataInicial, $dataFinal, $anoexerc, $origemtipo, $origem, $totexe, $reemissao);
    return $oGeradorTermo->escreverArquivo($sNomeArquivo);

  }else{

    $oGeradorTermo->gerar($certid, $certid1, $proced, $dataInicial, $dataFinal, $anoexerc, $origemtipo, $origem, $totexe, $reemissao);
    $oGeradorTermo->exibirArquivo();
  }
  db_fim_transacao(true);
} catch (Exception $eErro) {
  db_fim_transacao(true);
  db_redireciona("db_erros.php?fechar=true&db_erro={$eErro->getMessage()}");
}

exit();
