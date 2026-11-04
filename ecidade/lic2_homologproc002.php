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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_libdocumento.php"));

$clliclicita = new cl_liclicita;
$clliclicitem = new cl_liclicitem;
$clpcorcamforne = new cl_pcorcamforne;
$clpcorcamitemlic = new cl_pcorcamitemlic;
$clpcorcamjulg = new cl_pcorcamjulg;
$clrotulo = new rotulocampo;
$cldbconfig = new cl_db_config;
$clrotulo->label('');

parse_str($_SERVER['QUERY_STRING'], $queryString);
db_postmemory($_SERVER);

$oPDF = new PDF();
$oPDF->Open();
$oPDF->AliasNbPages();
$total = 0;
$oPDF->setfillcolor(235);
$oPDF->setfont('arial', 'b', 8);
$oPDF->setfillcolor(235);
$troca = 1;
$alt = 4;
$total = 0;
$p = 0;
$valortot = 0;
$cor = 0;
$dbinstit = db_getsession("DB_instit");

try {
    $oLibDocumento = new libdocumento(1703, null);

    if ($oLibDocumento->lErro) {
        throw new Exception($oLibDocumento->lErro);
    }

    $sql = $clliclicita->sql_query(null, "*", "l20_codigo", "l20_codigo=$l20_codigo and l20_instit = $dbinstit");
    $rsLicitacao = $clliclicita->sql_record($sql);

    if ($clliclicita->numrows == 0) {
        throw new Exception('Não existe registro cadastrado, ou licitação não julgada, ou licitação revogada.');
    }
} catch (Exception $erro) {
    db_redireciona("db_erros.php?fechar=true&db_erro={$erro->getMessage()}");
    exit;
}

$sql = "select db18_valor, db110_valor from liclicita
    inner join liclicitacadattdinamicovalorgrupo on l16_liclicita = l20_codigo
    inner join db_cadattdinamicovalorgrupo on db120_sequencial = l16_cadattdinamicovalorgrupo
    inner join db_cadattdinamicoatributosvalor on db110_cadattdinamicovalorgrupo = db120_sequencial
    inner join db_cadattdinamicoatributosopcoes on db110_valor = db18_opcao
        where l20_codigo = {$l20_codigo} and db110_db_cadattdinamicoatributos = 18";

$rs = db_query($sql);
$result = (object) pg_fetch_assoc($rs);
$fundamentacao = $result->db18_valor;

if ($result->db110_valor == "OUTD" || $result->db110_valor == "OUT" || $result->db110_valor == "OUTC" || $result->db110_valor == "OUTI" ) {
    $sql = "select db110_valor, db110_db_cadattdinamicoatributos from db_cadattdinamicoatributosvalor
            inner join db_cadattdinamicoatributos on db110_db_cadattdinamicoatributos = db_cadattdinamicoatributos.db109_sequencial
             where db110_cadattdinamicovalorgrupo in
                   (select l16_cadattdinamicovalorgrupo from liclicitacadattdinamicovalorgrupo where l16_liclicita = {$l20_codigo})
                    and db109_nome in ('numeroartigo', 'inciso', 'lei') order by db110_db_cadattdinamicoatributos asc " ;
    $rs = db_query($sql);

    $cont = 0;
    $retorno = "";
    while ($a = pg_fetch_assoc($rs)){

        if($cont == 0){
            $retorno = "Art. " . $a['db110_valor'] . ",";
        }  else if ($cont == 1){
            if($a['db110_valor'] != '0' && $a['db110_valor'] != ''){
                $retorno .= " Inc. " . $a['db110_valor'] . ",";
            }
        } else{
            $retorno .= " da Lei no " . $a['db110_valor'];
        }
        $cont++;
    };
    $fundamentacao = $retorno;
}
db_fieldsmemory($rsLicitacao, 0);

$head3 = "HOMOLOGAÇÃO DO PROCESSO ";
$head4 = "LICITAÇÃO : $l20_numero/$l20_anousu";
$head5 = "SEQUENCIAL: $l20_codigo";
$oPDF->addpage();
$oPDF->setfont('arial', 'b', 14);
$oPDF->ln();
$oPDF->cell(0, 8, "HOMOLOGAÇAO DE PROCESSO", 0, 1, "C", 0);
$oPDF->cell(0, 8, "MODALIDADE : $l03_descr", 0, 1, "C", 0);
$oPDF->cell(0, 8, "Fundamentação : $fundamentacao", 0, 1, "C", 0);
$oPDF->setfont('arial', '', 8);
$oPDF->ln(4);

$olicitacao = db_utils::fieldsMemory($rsLicitacao, 0);

$oLibDocumento->l20_numero = $olicitacao->l20_numero;
$oLibDocumento->l03_descr = $olicitacao->l03_descr;
$oLibDocumento->l20_procadmin = $olicitacao->l20_procadmin ? $olicitacao->l20_procadmin : $olicitacao->p58_numero;
$oLibDocumento->l20_datacria = $olicitacao->l20_anousu;
$oLibDocumento->l20_codigo = $olicitacao->l20_codigo;
$oLibDocumento->l30_portaria = $olicitacao->l30_portaria;
$oLibDocumento->l20_objeto = htmlspecialchars_decode($olicitacao->l20_objeto);

$sSqlDbConfig = $cldbconfig->sql_query(null, "*", null, "codigo = {$dbinstit}");
$result_munic = $cldbconfig->sql_record($sSqlDbConfig);
db_fieldsmemory($result_munic, 0);

$aParagrafos = $oLibDocumento->getDocParagrafos();
//
// for percorrendo os paragrafos do documento
//
foreach ($aParagrafos as $oParag) {
    if ($oParag->oParag->db02_tipo == "3") {
        eval($oParag->oParag->db02_texto);
    } else {
        $oParag->writeText($oPDF);
    }
}

$oPDF->Output();
