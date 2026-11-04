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

use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasRepository;

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_GET);

$taxasLancadasRepository = TaxasLancadasRepository::getInstance();

$aTaxas = $taxasLancadasRepository->getTaxas((!empty($taxa) ? " ar44_sequencial IN ({$taxa}) " : ""));

if (empty($taxa)) {
    $sTaxas = implode(",", array_map(function ($oTaxa){
        return $oTaxa->ar44_sequencial;
    }, $aTaxas));

    $_GET["taxa"] = $sTaxas;

    $sTaxas = "Todas";
} else {
    $sTaxas = implode(", ", array_map(function ($oTaxa){
        return $oTaxa->ar44_descricao;
    }, $aTaxas));
}

$filtros = getFiltros($_GET);

$alt="5";
$pdf = new PDF(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$head2 = "RELATÓRIO DE TAXAS LANÇADAS";

$dataInicio = date("d/m/Y", strtotime($dataInicio));
$dataFim = date("d/m/Y", strtotime($dataFim));
$head3 = "Período: {$dataInicio} à {$dataFim}";

$sSituacao = "";
$situacao = explode(",", $situacao);

if (in_array(1, $situacao)) {
    $sSituacao .= "Pago";
}

if (in_array(2, $situacao)) {
    (!empty($sSituacao) ? $sSituacao .= ", " : "");

    $sSituacao .= "Pendente";
}

if (in_array(3, $situacao)) {
    (!empty($sSituacao) ? $sSituacao .= ", " : "");

    $sSituacao .= "Cancelado";
}

if (in_array(4, $situacao)) {
    (!empty($sSituacao) ? $sSituacao .= ", " : "");

    $sSituacao .= "Inscrito Cob. Adm";
}

if (in_array(5, $situacao)) {
    (!empty($sSituacao) ? $sSituacao .= ", " : "");

    $sSituacao .= "Parcelado";
}

$head4 = "Situação: {$sSituacao}";

if (!empty($departamentos)) {
    $cl_db_depart = new cl_db_depart();

    $result = $cl_db_depart->sql_record($cl_db_depart->sql_query_file(null, "descrdepto", null, " coddepto IN ({$departamentos})"));

    if (!$result) {
        throw new \DBException('Erro ao buscar o departamento. \n\n Erro: '.pg_last_error());
    }

    $oDepartamentos = db_utils::getColectionByRecord($result);

    $aDepartamentos = array_map(function($oDepatamento){
        return $oDepatamento->descrdepto;
    }, $oDepartamentos);

    $sDepartamentos = implode(", ", $aDepartamentos);

    $head5 = "Departamento: {$sDepartamentos}";
}

$sGerouDebito = "";

if ($gerouDebito == 0) {
    $sGerouDebito .= "Todos";
} else {
    if ($gerouDebito == 1) {
        $sGerouDebito .= "Sim";
    } else {
        if ($gerouDebito == 2) {
            $sGerouDebito .= "Não";
        }
    }   
}

$head6 = "Gerou Débito: {$sGerouDebito}";

$head7 = "Taxa: {$sTaxas}";

$pdf->AddPage("L");
$pdf->setfillcolor(235);
$pdf->setfont('arial','b',9);

if ($modeloRelatorio == 1) {
    if (in_array(1, $filtros->situacao)) {
        $oPagos = getPago($filtros);
    } else {
        $oPagos = array();
    }
    
    if (in_array(2, $filtros->situacao)) {
        $oPendentes = getPendentes($filtros);
    } else {
        $oPendentes = array();
    }
    
    if (in_array(3, $filtros->situacao)) {
        $oCancelados = getCancelados($filtros);
    } else {
        $oCancelados = array();
    }

    if (in_array(4, $filtros->situacao)) {
        $oInscrCobAdm = getInscrCobAdm($filtros);
    } else {
        $oInscrCobAdm = array();
    }
    
    if (in_array(5, $filtros->situacao)) {
        $oParcelados = getParcelado($filtros);
    } else {
        $oParcelados = array();
    }
    
    if ($filtros->situacao[0] == "") {
        $oPagos = array();
        $oPendentes = array();
        $oCancelados = array();
        $oInscrCobAdm = array();
        $oParcelados = array();
    }
    
    $aDados = (object) array(
        "oPendentes" => $oPendentes,
        "oPagos" => $oPagos,
        "oCancelados" => $oCancelados,
        "oInscrCobAdm" => $oInscrCobAdm,
        "oParcelados" => $oParcelados
    );

    $aDados = getAjustaDebitos($aDados);

    $pdf->cell(130, $alt, 'Taxa', 1, 0, "C", 1);
    $pdf->cell(150, $alt, 'Situação', 1, 0, "C", 1);
    $pdf->ln();
    $pdf->cell(130, $alt, '', 1, 0, "C", 1);
    $pdf->cell(25, $alt, 'Pago', 1, 0, "C", 1);
    $pdf->cell(30, $alt, "Pendente", 1, 0, "C", 1);
    $pdf->cell(30, $alt, "Cancelado", 1, 0, "C", 1);
    $pdf->cell(35, $alt, "Inscrito Cob. Adm", 1, 0, "C", 1);
    $pdf->cell(30, $alt, "Parcelado", 1, 0, "C", 1);

    $pdf->setfont('arial','',7);
    $pdf->ln();

    foreach ($aDados as $taxa) {
        $pdf->cell(130, $alt, $taxa->taxa, 1, 0, "L", 1);
        $pdf->cell(25, $alt, trim(db_formatar($taxa->totalPago, "f")), 1, 0, "C", 1);
        $pdf->cell(30, $alt, trim(db_formatar($taxa->totalPendente, "f")), 1, 0, "C", 1);
        $pdf->cell(30, $alt, trim(db_formatar($taxa->totalCancelado, "f")), 1, 0, "C", 1);
        $pdf->cell(35, $alt, trim(db_formatar($taxa->totalInscrito, "f")), 1, 0, "C", 1);
        $pdf->cell(30, $alt, trim(db_formatar($taxa->totalParcelado, "f")), 1, 0, "C", 1);
        $pdf->ln();
    }
} else {
    if ($modeloRelatorio == 2) {
        $aDados = getDebitosFiltro($filtros);

        $pdf->cell(30, $alt, 'Origem', 1, 0, "C", 1);
        $pdf->cell(65, $alt, 'Contribuinte', 1, 0, "C", 1);
        $pdf->cell(40, $alt, 'Departamento', 1, 0, "C", 1);
        $pdf->cell(65, $alt, 'Taxa', 1, 0, "C", 1);
        $pdf->cell(20, $alt, "Valor", 1, 0, "C", 1);
        $pdf->cell(30, $alt, "Situação", 1, 0, "C", 1);
        $pdf->cell(30, $alt, "Data de Inclusão", 1, 0, "C", 1);
    
        $pdf->setfont('arial','',7);
        $pdf->ln();

        foreach ($aDados as $taxa) {
            $pdf->cell(30, $alt, $taxa->origem, 1, 0, "C", 1);
            $pdf->cell(65, $alt, trim($taxa->contribuinteNome), 1, 0, "L", 1);
            $pdf->cell(40, $alt, trim($taxa->departamento), 1, 0, "L", 1);
            $pdf->cell(65, $alt, trim($taxa->taxa), 1, 0, "L", 1);
            $pdf->cell(20, $alt, trim(db_formatar($taxa->valor, "f")), 1, 0, "C", 1);
            $pdf->cell(30, $alt, trim($taxa->situacao), 1, 0, "C", 1);
            $pdf->cell(30, $alt, trim($taxa->dataInclusao), 1, 0, "C", 1);
            $pdf->ln();
        }
    }
}

$pdf->Output();

function getFiltros($filtros)
{
    $filtros = (object) $filtros;

    $sJoin = "";
    $sWhere = " WHERE";
    $sWhere2 = "";
    $sWhereDebito = "";
    $sWhereRecibo = " 1 = 1";

    if (!empty($filtros->dataInicio)) {
        if ($filtros->gerouDebito == 0 OR $filtros->gerouDebito == 1) {
            $sWhere2 .= "{$sWhere} diversos.dv05_oper BETWEEN '{$filtros->dataInicio}' AND '{$filtros->dataFim}'";
        }

        if ($filtros->gerouDebito == 0 OR $filtros->gerouDebito == 2) {
            $sWhereRecibo .= " AND k00_dtoper BETWEEN '{$filtros->dataInicio}' AND '{$filtros->dataFim}'";
        }
    }

    if (!empty($filtros->departamentos)) {
        $sJoin .= " INNER JOIN taxaslancadasdepart ON ar45_taxaslancadas = ar46_taxaslancadas";

        (!empty($sWhere2) ? $sWhere = " AND " : "");

        $sWhereDebito = "{$sWhere} ar45_departamento IN ({$filtros->departamentos})";

        $sWhere2 .= $sWhereDebito;
        $sWhereRecibo .= $sWhereDebito;
    }

    if (!empty($filtros->taxa)) {
        (!empty($sWhere2) ? $sWhere = " AND " : "");

        $sWhereDebito = "ar46_taxaslancadas IN ({$filtros->taxa})";

        $sWhere2 .= "{$sWhere} {$sWhereDebito}";

        (!empty($sWhereRecibo) ? $sWhere = " AND " : "");
        
        $sWhereRecibo .= "{$sWhere} {$sWhereDebito}";
    }

    return (object) array(
        "sWhere" => $sWhere2,
        "sWhereRecibo" => $sWhereRecibo,
        "sJoin" => $sJoin,
        "gerouDebito" => $filtros->gerouDebito,
        "situacao" => explode(",", $filtros->situacao)
    );
}

// SINTÉTICO >>>

function getAjustaDebitos($aDados)
{
    $aTaxaPagos = array_map(function($pagos){
        return $pagos->taxa;
    }, $aDados->oPagos);

    $aTaxaPendentes = array_map(function($pendente){
        return $pendente->taxa;
    }, $aDados->oPendentes);

    $aTaxaCancelados = array_map(function($cancelados){
        return $cancelados->taxa;
    }, $aDados->oCancelados);

    $aTaxaInscrCobAdm = array_map(function($inscrCobAdm){
        return $inscrCobAdm->taxa;
    }, $aDados->oInscrCobAdm);

    $aTaxaParcelados = array_map(function($parcelados){
        return $parcelados->taxa;
    }, $aDados->oParcelados);

    $aTaxas = array_unique(array_merge($aTaxaPagos, $aTaxaPendentes, $aTaxaCancelados, $aTaxaInscrCobAdm, $aTaxaParcelados));

    $aDados2 = array();

    foreach ($aTaxas as $taxa) {
        $aDados3 = (object) array();

        $aDados3->taxa = $taxa;

        $aDados3->totalPago = 0;

        foreach ($aDados->oPagos as $oTaxa) {
            if ($taxa == $oTaxa->taxa) {
                $aDados3->totalPago = $oTaxa->totalpago;
            }
        }

        $aDados3->totalPendente = 0;

        foreach ($aDados->oPendentes as $oPendente) {
            if ($taxa == $oPendente->taxa) {
                $aDados3->totalPendente = $oPendente->totalpendente;
            }
        }

        $aDados3->totalCancelado = 0;

        foreach ($aDados->oCancelados as $oCancelado) {
            if ($taxa == $oCancelado->taxa) {
                $aDados3->totalCancelado = $oCancelado->totalcancelado;
            }
        }

        $aDados3->totalInsCobAdm = 0;

        foreach ($aDados->oInscrCobAdm as $oInscrCobAdm) {
            if ($taxa == $oInscrCobAdm->taxa) {
                $aDados3->totalInscrito = $oInscrCobAdm->totalinscrito;
            }
        }

        $aDados3->totalParcelado = 0;

        foreach ($aDados->oParcelados as $oParcelados) {
            if ($taxa == $oParcelados->taxa) {
                $aDados3->totalParcelado = $oParcelados->totalparcelado;
            }
        }

        $aDados2[] = $aDados3;
    }

    return $aDados2;
}

function getPago($filtros)
{
    $sql = "";

    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "1") {
        $sql .= "SELECT sum(x.totalpago) AS totalPago,
                        taxa
                   FROM (SELECT sum(arrepaga.k00_valor) AS totalPago,
                                ar44_sequencial || ' - ' || ar44_descricao AS taxa
                           FROM taxaslancadasrecibo
                          INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                          INNER JOIN diversos ON dv05_numpre = ar46_numnov
                          INNER JOIN procdiver ON dv09_procdiver = dv05_procdiver
                          INNER JOIN arrecant ON arrecant.k00_numpre = dv05_numpre
                          INNER JOIN arrepaga ON arrepaga.k00_numpre = dv05_numpre
                          {$filtros->sJoin}
                          {$filtros->sWhere}
                          GROUP BY ar44_sequencial,
                                   ar44_descricao
                            UNION ALL
                          SELECT sum(arrepaga.k00_valor) AS totalPago,
                                 ar44_sequencial || ' - ' || ar44_descricao AS taxa
                            FROM taxaslancadasrecibo
                           INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                           INNER JOIN diversos ON dv05_numpre = ar46_numnov
                           INNER JOIN procdiver ON dv09_procdiver = dv05_procdiver
                           INNER JOIN arreckey ON arreckey.k00_numpre = dv05_numpre
                           INNER JOIN abatimentoarreckey ON k128_arreckey = k00_sequencial
                           INNER JOIN abatimentorecibo ON k127_abatimento = k128_abatimento
                           INNER JOIN recibo ON recibo.k00_numpre = k127_numprerecibo
                           INNER JOIN arrepaga ON arrepaga.k00_numpre = recibo.k00_numpre
                                  AND arrepaga.k00_numpar = recibo.k00_numpar
                           {$filtros->sJoin}
                           {$filtros->sWhere}
                           GROUP BY ar44_sequencial,
                                    ar44_descricao) x
                  GROUP BY taxa";
    }

    if ($filtros->gerouDebito == "0") {
        $sql .= " UNION ALL ";
    }

    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "2") {
        $sql .= "SELECT sum(recibo.k00_valor) AS totalPago,
                        ar44_sequencial || ' - ' || ar44_descricao AS taxa
                   FROM taxaslancadasrecibo
                  INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                  INNER JOIN recibo ON recibo.k00_numpre = ar46_numnov
                  {$filtros->sJoin}
                  WHERE {$filtros->sWhereRecibo}
                    AND (SELECT COUNT(*)
                           FROM arrepaga
                          WHERE arrepaga.k00_numpre = recibo.k00_numpre
                            AND arrepaga.k00_numpar = recibo.k00_numpar) > 0
                  GROUP BY ar44_sequencial,
                           ar44_descricao";
    }

    $result = db_query($sql);

    if (!$result) {
        throw new \DBException("Erro ao buscar os debitos pagos. \n\n Erro: ".pg_last_error());
    }

    return db_utils::getColectionByRecord($result);
}

function getPendentes($filtros)
{
    $sql = "";

    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "1") {
        $sql .= "SELECT ((sum(x.totalCorrigido) + sum(x.totalJuros) + sum(x.totalMulta)) - sum(x.totalDesconto)) AS totalPendente,
                   x.taxa
              FROM (SELECT sum(k22_vlrcor) AS totalCorrigido,
                           sum(k22_juros) AS totalJuros,
                           sum(k22_multa) AS totalMulta,
                           sum(k22_desconto) AS totalDesconto,
                           ar44_sequencial || ' - ' || ar44_descricao AS taxa
                      FROM taxaslancadasrecibo
                     INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                     INNER JOIN diversos ON dv05_numpre = ar46_numnov
                     INNER JOIN procdiver ON dv09_procdiver = dv05_procdiver
                     INNER JOIN arrecad ON k00_numpre = dv05_numpre
                     INNER JOIN debitos ON k22_numpre = k00_numpre
                            AND k22_numpar = k00_numpar
                            AND k22_receit = k00_receit
                            AND k22_data = (SELECT k22_data
                                              FROM debitos d
                                             WHERE d.k22_numpre = k00_numpre
                                               AND d.k22_numpar = k00_numpar
                                               AND d.k22_receit = k00_receit
                                             ORDER BY k22_data DESC
                                             LIMIT 1)
                     {$filtros->sJoin}
                     {$filtros->sWhere}
                     GROUP BY ar44_sequencial,
                              ar44_descricao) x 
             GROUP BY x.taxa";

    }

    if ($filtros->gerouDebito == "0") {
        $sql .= " UNION ALL ";
    }

    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "2") {
        $sql .= "SELECT sum(recibo.k00_valor) AS totalPendente,
                       ar44_sequencial || ' - ' || ar44_descricao AS taxa
                  FROM taxaslancadasrecibo
                 INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                 INNER JOIN recibo ON recibo.k00_numpre = ar46_numnov
                 {$filtros->sJoin}
                 WHERE {$filtros->sWhereRecibo}
                   AND (SELECT COUNT(*)
                          FROM arrepaga
                         WHERE arrepaga.k00_numpre = recibo.k00_numpre
                           AND arrepaga.k00_numpar = recibo.k00_numpar) = 0
                 GROUP BY ar44_sequencial,
                          ar44_descricao";
    }

    $result = db_query($sql);

    if (!$result) {
        throw new \DBException('Erro ao buscar os debitos pendentes. \n\n Erro: '.pg_last_error());
    }

    return db_utils::getColectionByRecord($result);
}

function getCancelados($filtros)
{
    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "1") {
        $sql = "SELECT ((sum(x.totalCorrigido) + sum(x.totalJuros) + sum(x.totalMulta)) - sum(x.totalDesconto)) AS totalCancelado,
                       x.taxa
                  FROM (SELECT sum(k24_vlrcor) AS totalCorrigido,
                               sum(k24_juros) AS totalJuros,
                               sum(k24_multa) AS totalMulta,
                               sum(k24_desconto) AS totalDesconto,
                               ar44_sequencial || ' - ' || ar44_descricao AS taxa
                          FROM taxaslancadasrecibo
                         INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                         INNER JOIN diversos ON dv05_numpre = ar46_numnov
                         INNER JOIN procdiver ON dv09_procdiver = dv05_procdiver
                         INNER JOIN cancdebitosreg ON k21_numpre = dv05_numpre
                         INNER JOIN cancdebitosprocreg ON k24_cancdebitosreg = k21_sequencia
                         {$filtros->sJoin}
                         {$filtros->sWhere}
                         GROUP BY ar44_sequencial,
                                 ar44_descricao) x
                 GROUP BY taxa;";

        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar os debitos cancelados. \n\n Erro: '.pg_last_error());
        }

        return db_utils::getColectionByRecord($result);
    } else {
        return array();
    }
}

function getInscrCobAdm($filtros)
{
    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "1") {
        $sql = "SELECT ((sum(x.totalCorrigido) + sum(x.totalJuros) + sum(x.totalMulta)) - sum(x.totalDesconto)) AS totalInscrito,
                       x.taxa
                  FROM (SELECT sum(k22_vlrcor) AS totalCorrigido,
                               sum(k22_juros) AS totalJuros,
                               sum(k22_multa) AS totalMulta,
                               sum(k22_desconto) AS totalDesconto,
                               ar44_sequencial || ' - ' || ar44_descricao AS taxa
                          FROM taxaslancadasrecibo
                         INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                         INNER JOIN diversos ON dv05_numpre = ar46_numnov
                         INNER JOIN diverimportaold ON dv13_numpre = dv05_numpre
                         INNER JOIN diversos d ON d.dv05_coddiver = dv13_diversos
                         INNER JOIN arrecad ON k00_numpre = d.dv05_numpre
                         INNER JOIN debitos ON k22_numpre = k00_numpre
                                           AND k22_numpar = k00_numpar
                                           AND k22_receit = k00_receit
                                           AND k22_data = (SELECT k22_data
                                                             FROM debitos d
                                                            WHERE d.k22_numpre = k00_numpre
                                                              AND d.k22_numpar = k00_numpar
                                                              AND d.k22_receit = k00_receit
                                                            ORDER BY k22_data DESC
                                                            LIMIT 1)
                         {$filtros->sJoin}
                         {$filtros->sWhere}
                         GROUP BY ar44_sequencial,
                                  ar44_descricao) x 
                 GROUP BY x.taxa";

        $result = db_query($sql);

        if (!$result) {
            throw new \DBException("Erro ao buscar os debitos inscritos em cobrança administrativa. \n\n Erro: ".pg_last_error());
        }

        return db_utils::getColectionByRecord($result);
    } else {
        return array();
    }
}

function getParcelado($filtros)
{
    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "1") {
        $sql = "SELECT ((sum(x.totalCorrigido) + sum(x.totalJuros) + sum(x.totalMulta)) - sum(x.totalDesconto)) AS totalParcelado,
                       x.taxa
                  FROM (SELECT sum(termodiver.dv10_valor) AS totalCorrigido,
                               sum(termodiver.dv10_juros) AS totalJuros,
                               sum(termodiver.dv10_multa) AS totalMulta,
                               sum(termodiver.dv10_desconto) AS totalDesconto,
                               ar44_sequencial || ' - ' || ar44_descricao AS taxa
                          FROM taxaslancadasrecibo
                         INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                         INNER JOIN diversos ON dv05_numpre = ar46_numnov
                         INNER JOIN termodiver ON dv10_numpreant = dv05_numpre
                         INNER JOIN termo ON v07_parcel = dv10_parcel
                                         AND (SELECT COUNT(*)
                                                FROM termoanu
                                               WHERE v09_parcel = v07_parcel) = 0
                         {$filtros->sJoin}
                         {$filtros->sWhere}
                         GROUP BY ar44_sequencial,
                                  ar44_descricao) x 
                 GROUP BY x.taxa";

        $result = db_query($sql);

        if (!$result) {
            throw new \DBException("Erro ao buscar os debitos parcelados. \n\n Erro: ".pg_last_error());
        }

        return db_utils::getColectionByRecord($result);
    } else {
        return array();
    }
}

// <<< SINTÉTICO

function getDebitosFiltro($filtros)
{
    $aDados1 = array();

    $sql = "";

    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "1") {
        $sql = "SELECT ar44_sequencial || ' - ' || ar44_descricao AS taxa,
                       ar46_numnov AS numpre,
                       (CASE WHEN descrdepto IS NOT NULL AND ar46_tipoemissao = 0
                             THEN descrdepto
                             WHEN descrdepto IS NULL AND ar46_tipoemissao = 1
                             THEN 'PORTAL'
                             ELSE ''
                           END) AS departamento,
                           TO_CHAR(dv05_oper, 'DD/MM/YYYY') AS dataInclusao
                  FROM taxaslancadasrecibo
                 INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                 INNER JOIN diversos ON dv05_numpre = ar46_numnov
                  LEFT JOIN db_depart ON coddepto = ar46_departamento
                 {$filtros->sJoin}
                 {$filtros->sWhere}";
    }

    if ($filtros->gerouDebito == "0") {
        $sql .= " UNION ALL ";
    }

    if ($filtros->gerouDebito == "0" OR $filtros->gerouDebito == "2") {
        $sql .= "SELECT ar44_sequencial || ' - ' || ar44_descricao AS taxa,
                        ar46_numnov AS numpre,
                        (CASE WHEN descrdepto IS NOT NULL AND ar46_tipoemissao = 0
                              THEN descrdepto
                              WHEN descrdepto IS NULL AND ar46_tipoemissao = 1
                              THEN 'PORTAL'
                              ELSE ''
                            END) AS departamento,
                            TO_CHAR(k00_dtoper, 'DD/MM/YYYY') AS dataInclusao
                   FROM taxaslancadasrecibo
                  INNER JOIN taxaslancadas ON ar44_sequencial = ar46_taxaslancadas
                  INNER JOIN recibo ON recibo.k00_numpre = ar46_numnov
                   LEFT JOIN db_depart ON coddepto = ar46_departamento
                  {$filtros->sJoin}
                  WHERE {$filtros->sWhereRecibo}";
    }

    $result = db_query($sql);

    if (!$result) {
        throw new \DBException("Erro ao buscar os debitos filtrados. \n\n Erro: ".pg_last_error());
    }

    $aDebitos = db_utils::getColectionByRecord($result);

    foreach ($aDebitos as $aDebito) {
        $filtros2 = (object) array(
            "sWhere" => "",
            "sWhereRecibo" => "",
            "sJoin" => "",
            "gerouDebito" => "",
            "situacao" => ""
        );

        $filtros2->sWhere = $filtros->sWhere;
        $filtros2->sWhereRecibo = $filtros->sWhereRecibo;
        $filtros2->sJoin = $filtros->sJoin;
        $filtros2->gerouDebito = $filtros->gerouDebito;
        $filtros2->situacao = $filtros->situacao;

        $sWhere = "ar46_numnov = {$aDebito->numpre}";

        (!empty($filtros2->sWhere) ? $sWhere2 = " AND " : $sWhere2 = " WHERE ");
        $filtros2->sWhere .= " {$sWhere2} {$sWhere}";

        (!empty($filtros2->sWhereRecibo) ? $sWhere2 = " AND " : $sWhere2 = " WHERE ");
        $filtros2->sWhereRecibo .= " {$sWhere2} {$sWhere}";

        $situacao = "";
        $situacao2 = "";

        if (in_array(2, $filtros2->situacao)) {
            $oPendentes = getPendentes($filtros2)[0];
            if (!empty($oPendentes)) {                
                $situacao = "Pendente";
            }
        } else {
            $oPendentes = array();
        }

        if (empty($oPendentes) AND in_array(1, $filtros2->situacao)) {
            $oPagos = getPago($filtros2)[0];
            if (!empty($oPagos)) {
                $situacao = "Pago";
            }
        } else {
            $oPagos = array();
        }
        
        if (empty($oPagos) AND in_array(3, $filtros2->situacao)) {
            $oCancelados = getCancelados($filtros2)[0];
            if (!empty($oCancelados)) {
                $situacao = "Cancelado";
            }
        } else {
            $oCancelados = array();
        }

        if (empty($oCancelados) AND in_array(4, $filtros2->situacao)) {
            $oInscritos = getInscrCobAdm($filtros2)[0];
            if (!empty($oInscritos)) {
                $situacao = "Inscrito";
                $situacao2 = "Inscrito Cob. Adm.";
            }
        } else {
            $oInscritos = array();
        }
        
        if (empty($oInscritos) AND in_array(5, $filtros2->situacao)) {
            $oParcelados = getParcelado($filtros2)[0];
            if (!empty($oParcelados)) {
                $situacao = "Parcelado";
            }
        } else {
            $oParcelados = array();
        }

        if (empty($situacao) AND empty($situacao2)) {
            continue;
        }

        $oDados2 = (object) array();

        $oDados = (object) array(
            "Pendente" => $oPendentes,
            "Pago" => $oPagos,
            "Cancelado" => $oCancelados,
            "Inscrito" => $oInscritos,
            "Parcelado" => $oParcelados
        );

        $oOrigem = getOrigem($aDebito->numpre);

        $sSituacaoValor = strtolower("total".$situacao);

        $oDados2->origem = $oOrigem->origem;
        $oDados2->contribuinteNome = $oOrigem->contribuinte;
        $oDados2->taxa = $aDebito->taxa;
        $oDados2->valor = $oDados->$situacao->$sSituacaoValor;
        $oDados2->situacao = (!empty($situacao2) ? $situacao2 : $situacao);
        $oDados2->departamento = $aDebito->departamento;
        $oDados2->dataInclusao = $aDebito->datainclusao;

        $aDados1[$aDebito->numpre] = $oDados2;
    }

    return $aDados1;
}

function getOrigem($iDebito)
{
    $sql = "SELECT 'M - '||j01_matric AS origem,
                   z01_nome AS contribuinte
              FROM arrematric
             INNER JOIN iptubase ON j01_matric = k00_matric
             INNER JOIN cgm ON z01_numcgm = j01_numcgm
             WHERE k00_numpre = $iDebito;";

    $result = db_query($sql);

    if (!$result) {
        throw new \DBException("Erro ao buscar a origem na matricula. \n\n Erro: ".pg_last_error());
    }

    if (pg_numrows($result) == 0) {
        $sql = "SELECT 'I - '||q02_inscr AS origem,
                        z01_nome AS contribuinte
                  FROM arreinscr
                 INNER JOIN issbase ON q02_inscr = k00_inscr
                 INNER JOIN cgm ON z01_numcgm = q02_numcgm
                 WHERE k00_numpre = $iDebito;";

        $result = db_query($sql);

        if (!$result) {
            throw new \DBException("Erro ao buscar a origem na inscrição. \n\n Erro: ".pg_last_error());
        }

        if (pg_numrows($result) == 0) {
            $sql = "SELECT 'C - '||z01_numcgm AS origem,
                           z01_nome AS contribuinte
                      FROM arrenumcgm
                     INNER JOIN cgm ON z01_numcgm = k00_numcgm
                     WHERE k00_numpre = $iDebito;";

            $result = db_query($sql);

            if (!$result) {
                throw new \DBException("Erro ao buscar a origem no cgm. \n\n Erro: ".pg_last_error());
            }   
        }
    }

    return db_utils::fieldsMemory($result, 0);
}