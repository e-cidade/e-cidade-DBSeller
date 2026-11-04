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
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_arreprescr_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_numpref_classe.php"));
require_once(modification("classes/db_termoanu_classe.php"));
require_once(modification("classes/db_fiscal_classe.php"));
require_once(modification("classes/db_levanta_classe.php"));
require_once(modification("classes/db_db_config_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));

use ECidade\File\Excel;
use ECidade\Library\SpreadSheet\Template\Parser;

parse_str($_SERVER['QUERY_STRING']);
db_postmemory($_GET);

$clrotulo = new rotulocampo;

$clrotulo->label("z01_numcgm");
$clrotulo->label("j01_matric");
$clrotulo->label("j40_refant");
$clrotulo->label("q02_inscr");
$clrotulo->label("q02_inscmu");

define("TIPO_BUSCA"           , $selectFiltroBusca);
define("LABEL_CGM"            , $LSz01_numcgm);
define("LABEL_MATRIC"         , $LSj01_matric);
define("LABEL_IPTUBASE_REFANT", $LSj40_refant);
define("LABEL_INSCR"          , $LSq02_inscr);
define("LABEL_ISSBASE_REFANT" , $LSq02_inscmu);

$wherePendente = "";
$wherePago = "";
$wherePagoParcial = "";
$whereCancelado = "";
$wherePrescrito = "";
$whereSuspenso = "";
$whereInscritoCobAdm = "";
$whereInscritoDivida = "";
$whereParcelado = "";
$colunaOrigem = "";
$colunaOrigem1 = "";
$colunaOrigem2 = "";
$groupOrigem = "";

// Querys
//$sqlPendente = "";
//$sqlPago = "";
//$sqlCancelado = "";
//$sqlPrescrito = "";
//$sqlSuspenso = "";
//$sqlInscritoCobAdm = "";
//$sqlInscritoDivida = "";

$innerJoinMatricProprietario = " INNER JOIN (select distinct on (j01_matric) 
                                                    z01_cgmpri   as z01_numcgm,
                                                    proprietario as z01_nome,
                                                    z01_cgccpf,
                                                    z01_ender,
                                                    z01_compl,
                                                    z01_numero,
                                                    z01_munic,
                                                    z01_uf,
                                                    j01_matric
                                               from proprietario) as cgm
                                         ON arrematric.k00_matric = cgm.j01_matric";

// Wheres individuais de cada filtro
if ($selectFiltroBusca == 'cgm' && !empty($numcgm)) {

    $innerJoinCGM                  = "INNER JOIN cgm ON cgm.z01_numcgm =";
    $innerJoinOrigemPago           = "left JOIN cgm ON cgm.z01_numcgm = arrecant.k00_numcgm";
    $innerJoinOrigemPendente       = "$innerJoinCGM k00_numcgm";
    $innerJoinOrigemCancelado      = "$innerJoinCGM arrecant.k00_numcgm";
    $innerJoinOrigemPrescrito      = "$innerJoinCGM arrecant.k00_numcgm";
    $innerJoinOrigemParcelado      = "$innerJoinCGM arreold.k00_numcgm";
    $innerJoinOrigemSuspenso       = "$innerJoinCGM arresusp.k00_numcgm";
    $innerJoinOrigemInscritoCobAdm = "$innerJoinCGM arreold.k00_numcgm";
    $innerJoinOrigemInscritoDivida = "$innerJoinCGM arreold.k00_numcgm";

    $wherePendente = " k00_numcgm = $numcgm
                    AND EXTRACT(year FROM k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    
    $wherePago = " (arrepaga.k00_hist NOT IN (17, 27, 61, 161, 117, 127, 400, 401, 500) or arrepaga.k00_valor < 0)
                    AND arrepaga.k00_numcgm = $numcgm
                    AND (
                        EXTRACT(
                            year
                            FROM
                                arrecant.k00_dtoper
                        ) BETWEEN $exercicio_inicial
                        AND $exercicio_final
                        OR EXTRACT(
                            year
                            FROM
                                arrepaga.k00_dtpaga
                        ) BETWEEN $exercicio_inicial
                        AND $exercicio_final
                    )
                    AND arrecant.k00_numpre is not null
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');
    
    $wherePagoParcial = "and arrenumcgm.k00_numcgm = $numcgm";

    $whereCancelado = " arrecant.k00_numcgm = $numcgm
                    AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    $wherePrescrito = " arrecant.k00_numcgm = $numcgm
                    AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    $whereSuspenso = " arresusp.k00_numcgm = $numcgm
                    AND EXTRACT(year FROM arresusp.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    $whereInscritoCobAdm = " arreold.k00_numcgm = $numcgm
                    AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    $whereInscritoDivida = " arreold.k00_numcgm = $numcgm
                    AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    $whereParcelado = " arreold.k00_numcgm = $numcgm
                    AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

} else if ($selectFiltroBusca == 'matric' && !empty($matric)) {
    // Pendente
    $innerJoinOrigemPendente  = " INNER JOIN arrematric on arrecad.k00_numpre = arrematric.k00_numpre ";
    $innerJoinOrigemPendente .= $innerJoinMatricProprietario;

    $wherePendente = " exists (
                            SELECT 1 FROM arrematric where arrematric.k00_numpre = arrecad.k00_numpre
                            AND arrematric.k00_matric = $matric
                        )
                    AND cgm.j01_matric = $matric
                    AND EXTRACT(year FROM k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Pago
    $innerJoinOrigemPago  = " INNER JOIN arrematric ON arrematric.k00_numpre = arrepaga.k00_numpre ";
    $innerJoinOrigemPago .= $innerJoinMatricProprietario;

    $wherePago = " (arrepaga.k00_hist NOT IN (17, 27, 61, 161, 117, 127, 400, 401, 500) or arrepaga.k00_valor < 0)
                    AND exists (
                        SELECT 1 FROM arrematric where arrematric.k00_numpre = arrecant.k00_numpre
                        AND arrematric.k00_matric = $matric
                    )
                    AND cgm.j01_matric = $matric
                    AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Cancelado
    $innerJoinOrigemCancelado  = " INNER JOIN arrematric ON arrematric.k00_numpre = arrecant.k00_numpre ";
    $innerJoinOrigemCancelado .= $innerJoinMatricProprietario;

    $whereCancelado = " exists (
                            SELECT 1 FROM arrematric where arrematric.k00_numpre = arrecant.k00_numpre
                            AND arrematric.k00_matric = $matric
                    )
                    AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Prescrito
    $innerJoinOrigemPrescrito  = " INNER JOIN arrematric ON arrematric.k00_numpre = arrecant.k00_numpre ";
    $innerJoinOrigemPrescrito .= $innerJoinMatricProprietario;

    $wherePrescrito = " exists (
                            SELECT 1 FROM arrematric where arrematric.k00_numpre = arrecant.k00_numpre
                            AND arrematric.k00_matric = $matric
                    )
                    AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Suspenso
    $innerJoinOrigemSuspenso  = " INNER JOIN arrematric on arresusp.k00_numpre = arrematric.k00_numpre ";
    $innerJoinOrigemSuspenso .= $innerJoinMatricProprietario;

    $whereSuspenso = " exists (
                            SELECT 1 FROM arrematric where arrematric.k00_numpre = arresusp.k00_numpre
                            AND arrematric.k00_matric = $matric
                    )
                    AND EXTRACT(year FROM arresusp.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Inscrito em Cobrança Adm
    $innerJoinOrigemInscritoCobAdm  = "INNER JOIN arrematric on arreold.k00_numpre = arrematric.k00_numpre ";
    $innerJoinOrigemInscritoCobAdm .= $innerJoinMatricProprietario;
    
    $whereInscritoCobAdm = " exists (
                            SELECT 1 FROM arrematric where arrematric.k00_numpre = arreold.k00_numpre
                            AND arrematric.k00_matric = $matric
                    )
                    AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Inscrito em Dívida Ativa
    $innerJoinOrigemInscritoDivida  = " INNER JOIN arrematric on arreold.k00_numpre = arrematric.k00_numpre ";
    $innerJoinOrigemInscritoDivida .= $innerJoinMatricProprietario;

    $whereInscritoDivida = " exists (
                            SELECT 1 FROM arrematric where arrematric.k00_numpre = arreold.k00_numpre
                            AND arrematric.k00_matric = $matric
                    )
                    AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Parcelado
    $innerJoinOrigemParcelado  = " INNER JOIN arrematric ON arreold.k00_numpre = arrematric.k00_numpre ";
    $innerJoinOrigemParcelado .= $innerJoinMatricProprietario;

    $whereParcelado = " exists (
                            SELECT 1 FROM arrematric where arrematric.k00_numpre = arreold.k00_numpre
                            AND arrematric.k00_matric = $matric
                    )
                    AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
                    AND $exercicio_final
                    AND arreinstit.k00_instit = ".db_getsession('DB_instit');

} else if ($selectFiltroBusca == 'inscr' && !empty($inscr)) {
    // Pendente
    $innerJoinOrigemPendente = " INNER JOIN arreinscr ON arreinscr.k00_numpre = arrecad.k00_numpre 
                                 INNER JOIN issbase ON arreinscr.k00_inscr = issbase.q02_inscr
                                 INNER JOIN cgm on issbase.q02_numcgm = cgm.z01_numcgm";
    /* COMENTADO E UTILIZADO O CGM DIRETO POIS NEM TODA EMPRESA TEM TABATIV UTILIZADA NA VIEW COMO INNER
                                 INNER JOIN (select q02_numcgm    as z01_numcgm,
                                                    razao         as z01_nome,
                                                    z01_cgccpf,
                                                    z01_ender,
                                                    z01_compl,
                                                    z01_numero,
                                                    z01_munic,
                                                    z01_uf,
                                                    q02_inscr
                                               from empresa where empresa.q88_tipo = 'P') as cgm
                                         ON arreinscr.k00_inscr = cgm.q02_inscr";
    */                                          
        
    $wherePendente = " exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arrecad.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Pago
    $innerJoinOrigemPago = " INNER JOIN arreinscr ON arreinscr.k00_numpre = arrepaga.k00_numpre 
                             INNER JOIN issbase ON arreinscr.k00_inscr = issbase.q02_inscr
                             INNER JOIN cgm on issbase.q02_numcgm = cgm.z01_numcgm";

    $wherePago = " (arrepaga.k00_hist NOT IN (17, 27, 61, 161, 117, 127, 400, 401, 500) or arrepaga.k00_valor < 0)
        AND arreinscr.k00_inscr = $inscr
        AND exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arrecant.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    $wherePagoParcial = " AND arreinscr.k00_inscr = $inscr ";
    // Cancelado
    $innerJoinOrigemCancelado = " INNER JOIN arreinscr ON arreinscr.k00_numpre = arrecant.k00_numpre 
                                  INNER JOIN issbase ON arreinscr.k00_inscr = issbase.q02_inscr
                                  INNER JOIN cgm on issbase.q02_numcgm = cgm.z01_numcgm";

    $whereCancelado = " exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arrecant.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Prescrito
    $innerJoinOrigemPrescrito = " INNER JOIN arreinscr ON arreinscr.k00_numpre = arrecant.k00_numpre
                                  INNER JOIN issbase ON arreinscr.k00_inscr = issbase.q02_inscr
                                  INNER JOIN cgm on issbase.q02_numcgm = cgm.z01_numcgm";

    $wherePrescrito = " exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arrecant.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM arrecant.k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Suspenso
    $innerJoinOrigemSuspenso = " INNER JOIN arreinscr ON arreinscr.k00_numpre = arresusp.k00_numpre 
                                 INNER JOIN issbase on arreinscr.k00_inscr = issbase.q02_inscr
                                 INNER JOIN cgm on cgm.z01_numcgm = issbase.q02_numcgm";

    $whereSuspenso = " exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arresusp.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM arresusp.k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Inscrito Cobrança Adm
    $innerJoinOrigemInscritoCobAdm = " INNER JOIN arreinscr ON arreinscr.k00_numpre = arreold.k00_numpre 
                                       INNER JOIN issbase on arreinscr.k00_inscr = issbase.q02_inscr
                                       INNER JOIN cgm on cgm.z01_numcgm = issbase.q02_numcgm";

    $whereInscritoCobAdm = " exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arreold.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Inscrito Divida Ativa
    $innerJoinOrigemInscritoDivida = " INNER JOIN arreinscr ON arreinscr.k00_numpre = divold.k10_numpre 
                                       INNER JOIN issbase on arreinscr.k00_inscr = issbase.q02_inscr
                                       INNER JOIN cgm on cgm.z01_numcgm = issbase.q02_numcgm";

    $whereInscritoDivida = " exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arreold.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');

    // Parcelado
    $innerJoinOrigemParcelado = " INNER JOIN arreinscr ON arreinscr.k00_numpre = arreold.k00_numpre 
                                  INNER JOIN issbase ON arreold.k00_numpre = arreinscr.k00_numpre
                                  INNER JOIN cgm on issbase.q02_numcgm = cgm.z01_numcgm "; 
    
    $whereParcelado = " exists (
            SELECT 1 FROM arreinscr where arreinscr.k00_numpre = arreold.k00_numpre
            AND arreinscr.k00_inscr = $inscr
        )
        AND EXTRACT(year FROM arreold.k00_dtoper) BETWEEN $exercicio_inicial
        AND $exercicio_final
        AND arreinstit.k00_instit = ".db_getsession('DB_instit');
}

// SQLPendente
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "k00_inscr AS inscr,";
    } 
    $colunaOrigem1 = "pendente.{$selectFiltroBusca},";
    $colunaOrigem2 = "pendente_2.{$selectFiltroBusca},";
}

$sqlPendente = "SELECT
        pendente_2.nome,
        pendente_2.numcgm,
        {$colunaOrigem2}
        pendente_2.cpf,
        pendente_2.endereco,
        pendente_2.complemento,
        pendente_2.numero,
        pendente_2.municipio,
        pendente_2.uf,
        pendente_2.numpre,
        pendente_2.numpar,
        pendente_2.numtot,
        pendente_2.descricao,
        pendente_2.data_vencimento,
        pendente_2.situacao,
        pendente_2.processo,
        pendente_2.data_operacao,
        sum(pendente_2.valor) AS valor,
        sum(pendente_2.corrigido) AS corrigido,
        sum(pendente_2.juros) AS juros,
        sum(pendente_2.multa) AS multa,
        sum(pendente_2.desconto) AS desconto,
        sum(pendente_2.total) AS total,
        pendente_2.data_movimento::text 
    FROM (
        SELECT
            pendente.nome,
            pendente.numcgm,
            {$colunaOrigem1}
            pendente.cpf,
            pendente.endereco,
            pendente.complemento,
            pendente.numero,
            pendente.municipio,
            pendente.uf,
            pendente.numpre,
            pendente.numpar,
            pendente.numtot,
            pendente.descricao,
            pendente.data_vencimento,
            pendente.calculo,
            pendente.situacao,
            pendente.processo,
            pendente.data_operacao,
            substr(calculo, 2, 13) :: float8 AS valor,
            substr(calculo, 15, 13) :: float8 AS corrigido,
            substr(calculo, 28, 13) :: float8 AS juros,
            substr(calculo, 41, 13) :: float8 AS multa,
            substr(calculo, 54, 13) :: float8 AS desconto,
            (substr(calculo, 15, 13) :: float8 + substr(calculo, 28, 13) :: float8 + substr(calculo, 41, 13) :: float8 - substr(calculo, 54, 13) :: float8) AS total,
            null AS data_movimento
        FROM
            (
                SELECT
                    DISTINCT
                    z01_nome AS nome,
                    z01_numcgm AS numcgm,
                    {$colunaOrigem}
                    z01_cgccpf AS cpf,
                    z01_ender AS endereco,
                    z01_compl AS complemento,
                    z01_numero AS numero,
                    z01_munic AS municipio,
                    z01_uf AS uf,
                    arrecad.k00_numpre AS numpre,
                    k00_numpar AS numpar,
                    k00_numtot AS numtot,
                    k00_descr AS descricao,
                    k00_dtvenc AS data_vencimento,
                    fc_calcula(arrecad.k00_numpre,k00_numpar,k00_receit,current_date,current_date,EXTRACT(year FROM current_date)::integer) AS calculo,
                    'Pendente' AS situacao,
                    '0' AS processo,
                    k00_dtoper AS data_operacao
                FROM
                    arrecad
                    INNER JOIN arretipo ON arrecad.k00_tipo = arretipo.k00_tipo
                    INNER JOIN arreinstit ON arrecad.k00_numpre = arreinstit.k00_numpre
                    {$innerJoinOrigemPendente}
                            WHERE
                                {$wherePendente}
                ) AS pendente
        ) AS pendente_2
        GROUP BY
            pendente_2.numpre,
            pendente_2.numpar,
            pendente_2.nome,
            pendente_2.numcgm,
            {$colunaOrigem2}
            pendente_2.cpf,
            pendente_2.endereco,
            pendente_2.complemento,
            pendente_2.numero,
            pendente_2.municipio,
            pendente_2.uf,
            pendente_2.numtot,
            pendente_2.descricao,
            pendente_2.data_vencimento,
            pendente_2.situacao,
            pendente_2.processo,
            pendente_2.data_operacao,
            pendente_2.data_movimento
";

// SQL Pago
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
        $groupOrigem = "cgm.j01_matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "arreinscr.k00_inscr AS inscr,";
        $groupOrigem = "arreinscr.k00_inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "pago.{$selectFiltroBusca},";
        $colunaOrigem2 = "pago_2.{$selectFiltroBusca},"; 
    }
}

$sqlPago = "SELECT
        pago_2.nome,
        pago_2.numcgm,
        {$colunaOrigem2}
        pago_2.cpf,
        pago_2.endereco,
        pago_2.complemento,
        pago_2.numero,
        pago_2.municipio,
        pago_2.uf,
        pago_2.numpre,
        pago_2.numpar,
        pago_2.numtot,
        pago_2.descricao,
        pago_2.data_vencimento,
        pago_2.situacao,
        pago_2.processo,
        pago_2.data_operacao,
        sum(pago_2.valor) AS valor,
        sum(pago_2.corrigido) AS corrigido,
        sum(pago_2.juros) AS juros,
        sum(pago_2.multa) AS multa,
        sum(pago_2.desconto) AS desconto,
        sum(pago_2.total) AS total,
        pago_2.data_movimento::text 
    FROM (
        SELECT
            pago.nome,
            pago.numcgm,
            {$colunaOrigem1}
            pago.cpf,
            pago.endereco,
            pago.complemento,
            pago.numero,
            pago.municipio,
            pago.uf,
            pago.numpre,
            pago.numpar,
            pago.numtot,
            pago.descricao,
            pago.data_vencimento,
            pago.calculo,
            pago.situacao,
            pago.processo,
            pago.data_operacao,
            pago.valor,
            pago.corrigido,
            coalesce((SELECT sum(k00_valor)
                        FROM arrepaga
                        WHERE arrepaga.k00_numpre = pago.numpre AND arrepaga.k00_numpar = pago.numpar AND arrepaga.k00_hist = 400 ), 0)  AS juros,
            coalesce((SELECT sum(k00_valor)
                        FROM arrepaga
                        WHERE arrepaga.k00_numpre = pago.numpre AND arrepaga.k00_numpar = pago.numpar AND arrepaga.k00_hist = 401 ), 0)  AS multa,
            coalesce((SELECT sum(k00_valor)
                        FROM arrepaga
                        WHERE arrepaga.k00_numpre = pago.numpre AND arrepaga.k00_numpar = pago.numpar AND (arrepaga.k00_hist = 918 or arrepaga.k00_valor < 0) ), 0)  AS desconto,
            pago.corrigido AS total,
            pago.data_movimento
        FROM
            (
                SELECT
                    distinct arrepaga.oid,
                    arrepaga.k00_hist,
                    cgm.z01_nome AS nome,
                    cgm.z01_numcgm AS numcgm,
                    {$colunaOrigem}
                    cgm.z01_cgccpf AS cpf,
                    cgm.z01_ender AS endereco,
                    cgm.z01_compl AS complemento,
                    cgm.z01_numero AS numero,
                    cgm.z01_munic AS municipio,
                    cgm.z01_uf AS uf,
                    arrecant.k00_numpre AS numpre,
                    arrepaga.k00_numpre AS numpre_arrepaga,
                    arrecant.k00_numpar AS numpar,
                    arrecant.k00_numtot AS numtot,
                    k01_descr AS descricao,
                    arrecant.k00_dtvenc AS data_vencimento,
                    arrecant.k00_valor AS valor,
                    case
                        WHEN round(arrecant.k00_valor,2)::numeric = 0.00 THEN sum(arrepaga.k00_valor)
                        ELSE arrecant.k00_valor
                    end AS corrigido,
                    '' AS calculo,
                    'Pago' AS situacao,
                    '0' AS processo,
                    arrecant.k00_dtoper AS data_operacao,
                    max(arrepaga.k00_dtpaga) AS data_movimento
                FROM
                arrepaga
                    inner join arrenumcgm on arrenumcgm.k00_numpre = arrepaga.k00_numpre
                    left join arrecant on arrecant.k00_numpre = arrepaga.k00_numpre
                    and arrecant.k00_numpar = arrepaga.k00_numpar
                    and arrecant.k00_receit = arrepaga.k00_receit
                    and arrecant.k00_hist <> 918
                    inner join arreinstit on arreinstit.k00_numpre = arrepaga.k00_numpre
                    and arreinstit.k00_instit = 1
                    inner join tabrec on tabrec.k02_codigo = arrepaga.k00_receit
                    inner join tabrecjm on tabrecjm.k02_codjm = tabrec.k02_codjm
                    inner join histcalc on histcalc.k01_codigo = arrepaga.k00_hist
                    left join arreidret on arreidret.k00_numpre = arrepaga.k00_numpre
                    and arreidret.k00_numpar = arrepaga.k00_numpar
                    left join disbanco on disbanco.idret = arreidret.idret
                    {$innerJoinOrigemPago}
                            WHERE
                            not exists (
                                select
                                    1
                                from
                                    abatimentorecibo
                                    inner join abatimento on abatimento.k125_sequencial = abatimentorecibo.k127_abatimento
                                where
                                    abatimentorecibo.k127_numprerecibo = arrepaga.k00_numpre
                                    and abatimento.k125_tipoabatimento in (1, 4)
                                limit
                                    1
                            )
                            and not exists(
                                select
                                    1
                                from
                                    abatimentoutilizacaodestino
                                    inner join abatimentoutilizacao on k157_sequencial = k170_utilizacao
                                    inner join abatimento on abatimento.k125_sequencial = k157_abatimento
                                    inner join abatimentorecibo on k127_abatimento = k125_sequencial
                                    inner join arrepaga ap on ap.k00_numpre = k170_numpre
                                    and ap.k00_numpar = k170_numpar
                                where
                                    k170_numpre = arrepaga.k00_numpre
                                    and k170_numpar = arrepaga.k00_numpar
                                    and k157_tipoutilizacao = '2'
                                    and k125_tipoabatimento = 3
                                    and ap.k00_conta = 0
                                limit
                                    1
                            )
                            and not exists(
                                select
                                    1
                                from
                                    abatimentorecibo
                                    inner join abatimento on abatimento.k125_sequencial = k127_abatimento
                                    left join termo on termo.v07_numpre = abatimentorecibo.k127_numpreoriginal
                                    and termo.v07_situacao = 2
                                where
                                    abatimentorecibo.k127_numpreoriginal = arrepaga.k00_numpre
                                    and k125_tipoabatimento = 4
                                    and termo is null
                                limit
                                    1
                            )
                            and {$wherePago}
                    GROUP BY
                        arrepaga.oid,
                        arrepaga.k00_hist,
                        cgm.z01_nome,
                        cgm.z01_numcgm,
                        cgm.z01_cgccpf,
                        cgm.z01_ender,
                        cgm.z01_compl,
                        cgm.z01_numero,
                        cgm.z01_munic,
                        cgm.z01_uf,
                        {$groupOrigem}
                        arrecant.k00_numpre,
                        arrepaga.k00_numpre,
                        arrecant.k00_numpar,
                        arrecant.k00_numtot,
                        k01_descr,
                        arrecant.k00_dtvenc,
                        arrecant.k00_valor,
                        arrecant.k00_dtoper
        ) AS pago
    ) AS pago_2
    GROUP BY
        pago_2.numpre,
        pago_2.numpar,
        pago_2.nome,
        pago_2.numcgm,
        {$colunaOrigem2}
        pago_2.cpf,
        pago_2.endereco,
        pago_2.complemento,
        pago_2.numero,
        pago_2.municipio,
        pago_2.uf,
        pago_2.numtot,
        pago_2.descricao,
        pago_2.data_vencimento,
        pago_2.situacao,
        pago_2.processo,
        pago_2.data_operacao,
        pago_2.data_movimento
";

// SQL PagoParcial
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
        $groupOrigem = "cgm.j01_matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "arreinscr.k00_inscr AS inscr,";
        $groupOrigem = "arreinscr.k00_inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "pagoParcial.{$selectFiltroBusca},";
        $colunaOrigem2 = "pagoParcial_2.{$selectFiltroBusca},"; 
    }
}

$sqlPagoParcial = "SELECT
        pagoParcial_2.nome,
        pagoParcial_2.numcgm,
        {$colunaOrigem2}
        pagoParcial_2.cpf,
        pagoParcial_2.endereco,
        pagoParcial_2.complemento,
        pagoParcial_2.numero,
        pagoParcial_2.municipio,
        pagoParcial_2.uf,
        pagoParcial_2.numpre,
        pagoParcial_2.numpar,
        pagoParcial_2.numtot,
        pagoParcial_2.descricao,
        pagoParcial_2.data_vencimento,
        pagoParcial_2.situacao,
        pagoParcial_2.processo,
        pagoParcial_2.data_operacao,
        sum(pagoParcial_2.valor) AS valor,
        sum(pagoParcial_2.corrigido) AS corrigido,
        sum(pagoParcial_2.juros) AS juros,
        sum(pagoParcial_2.multa) AS multa,
        sum(pagoParcial_2.desconto) AS desconto,
        sum(pagoParcial_2.total) AS total,
        pagoParcial_2.data_movimento::text 
    FROM (
        SELECT
            pagoParcial.nome,
            pagoParcial.numcgm,
            {$colunaOrigem1}
            pagoParcial.cpf,
            pagoParcial.endereco,
            pagoParcial.complemento,
            pagoParcial.numero,
            pagoParcial.municipio,
            pagoParcial.uf,
            pagoParcial.numpre,
            pagoParcial.numpar,
            pagoParcial.numtot,
            pagoParcial.descricao,
            pagoParcial.data_vencimento,
            pagoParcial.calculo,
            pagoParcial.situacao,
            pagoParcial.processo,
            pagoParcial.data_operacao,
            pagoParcial.valor,
            pagoParcial.corrigido,
            coalesce((SELECT sum(k00_valor)
                        FROM arrepaga
                        WHERE arrepaga.k00_numpre = pagoParcial.numpre AND arrepaga.k00_numpar = pagoParcial.numpar AND arrepaga.k00_hist = 400 ), 0)  AS juros,
            coalesce((SELECT sum(k00_valor)
                        FROM arrepaga
                        WHERE arrepaga.k00_numpre = pagoParcial.numpre AND arrepaga.k00_numpar = pagoParcial.numpar AND arrepaga.k00_hist = 401 ), 0)  AS multa,
            coalesce((SELECT sum(k00_valor)
                        FROM arrepaga
                        WHERE arrepaga.k00_numpre = pagoParcial.numpre AND arrepaga.k00_numpar = pagoParcial.numpar AND (arrepaga.k00_hist = 918 or arrepaga.k00_valor < 0) ), 0)  AS desconto,
                        pagoParcial.corrigido AS total,
            pagoParcial.data_movimento
        FROM
            (
                SELECT
                    distinct arreckey.oid,
                    cgm.z01_nome AS nome,
                    cgm.z01_numcgm AS numcgm,
                    {$colunaOrigem}
                    cgm.z01_cgccpf AS cpf,
                    cgm.z01_ender AS endereco,
                    cgm.z01_compl AS complemento,
                    cgm.z01_numero AS numero,
                    cgm.z01_munic AS municipio,
                    cgm.z01_uf AS uf,
                    '' AS calculo,
                    'Pago' AS situacao,
                    '0' AS processo,

                    arreckey.k00_numpre as numpre,
                    arreckey.k00_numpar as numpar,
                    case
                        when arrecad.k00_numtot is not null then arrecad.k00_numtot
                        when arrecant.k00_numtot is not null then arrecant.k00_numtot
                        else arrepaga.k00_numtot
                    end as numtot,
                    case
                        when arrecad.k00_dtvenc is not null then arrecad.k00_dtvenc
                        when arrecant.k00_dtvenc is not null then arrecant.k00_dtvenc
                        else arrepaga.k00_dtvenc
                    end as data_vencimento,
                    case
                        when arrecad.k00_dtoper is not null then arrecad.k00_dtoper
                        when arrecant.k00_dtoper is not null then arrecant.k00_dtoper
                        else arrepaga.k00_dtoper
                    end as data_movimento,
                    case
                        when arrecad.k00_dtoper is not null then arrecad.k00_dtoper
                        when arrecant.k00_dtoper is not null then arrecant.k00_dtoper
                        else arrepaga.k00_dtoper
                    end as data_operacao,
                    arreckey.k00_receit,
                    tabrec.k02_drecei as descricao,
                    arreckey.k00_hist,
                    histcalc.k01_descr,
                    (
                        abatimentoarreckey.k128_valorabatido + abatimentoarreckey.k128_correcao + abatimentoarreckey.k128_juros + abatimentoarreckey.k128_multa
                    ) as valor,
                    (
                        abatimentoarreckey.k128_valorabatido + abatimentoarreckey.k128_correcao + abatimentoarreckey.k128_juros + abatimentoarreckey.k128_multa
                    ) as corrigido,
                    arrepaga.k00_conta,
                    arrepaga.k00_dtpaga,
                    case
                        when arrecad.k00_tipo is not null then arrecad.k00_tipo
                        else arrecant.k00_tipo
                    end as k00_tipo,
                    coalesce(disbanco.dtpago, k00_dtpaga) as efetpagto,
                    'PARCIAL' as tipopagamento,
                    abatimento.k125_sequencial as abatimento,
                    arrepaga.k00_numpre as numpreabatimento,
                    0 as boleto
                from
                    abatimentorecibo
                    inner join abatimento on abatimento.k125_sequencial = abatimentorecibo.k127_abatimento
                    left join abatimentodisbanco on abatimentodisbanco.k132_abatimento = abatimento.k125_sequencial
                    left join disbanco on disbanco.idret = abatimentodisbanco.k132_idret
                    inner join abatimentoarreckey on abatimentoarreckey.k128_abatimento = abatimento.k125_sequencial
                    inner join arreckey on arreckey.k00_sequencial = abatimentoarreckey.k128_arreckey
                    inner join tabrec on tabrec.k02_codigo = arreckey.k00_receit
                    inner join histcalc on histcalc.k01_codigo = arreckey.k00_hist
                    left join arrepaga on arrepaga.k00_numpre = abatimentorecibo.k127_numprerecibo
                    inner join arreinstit on arreinstit.k00_numpre = arrepaga.k00_numpre
                    and arreinstit.k00_instit = 1
                    left join arrecant on arrecant.k00_numpre = arreckey.k00_numpre
                    and arrecant.k00_numpar = arreckey.k00_numpar
                    and arrecant.k00_receit = arreckey.k00_receit
                    left join arrecad on arrecad.k00_numpre = arreckey.k00_numpre
                    and arrecad.k00_numpar = arreckey.k00_numpar
                    and arrecad.k00_receit = arreckey.k00_receit
                    inner join arrenumcgm on arrenumcgm.k00_numpre = arrepaga.k00_numpre
                    {$innerJoinOrigemPago}
                where
                    abatimento.k125_tipoabatimento in (1, 4)  
                    {$wherePagoParcial}
        ) AS pagoParcial
    ) AS pagoParcial_2
    GROUP BY
        pagoParcial_2.numpre,
        pagoParcial_2.numpar,
        pagoParcial_2.nome,
        pagoParcial_2.numcgm,
        {$colunaOrigem2}
        pagoParcial_2.cpf,
        pagoParcial_2.endereco,
        pagoParcial_2.complemento,
        pagoParcial_2.numero,
        pagoParcial_2.municipio,
        pagoParcial_2.uf,
        pagoParcial_2.numtot,
        pagoParcial_2.descricao,
        pagoParcial_2.data_vencimento,
        pagoParcial_2.situacao,
        pagoParcial_2.processo,
        pagoParcial_2.data_operacao,
        pagoParcial_2.data_movimento
";


// SQL Cancelado
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
        $groupOrigem = "cgm.j01_matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "k00_inscr AS inscr,";
        $groupOrigem = "k00_inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "cancelado.{$selectFiltroBusca},";
        $colunaOrigem2 = "cancelado_2.{$selectFiltroBusca},"; 
    }
}

$sqlCancelado = "SELECT
    cancelado_2.nome,
    cancelado_2.numcgm,
    {$colunaOrigem2}
    cancelado_2.cpf,
    cancelado_2.endereco,
    cancelado_2.complemento,
    cancelado_2.numero,
    cancelado_2.municipio,
    cancelado_2.uf,
    cancelado_2.numpre,
    cancelado_2.numpar,
    cancelado_2.numtot,
    cancelado_2.descricao,
    cancelado_2.data_vencimento,
    cancelado_2.situacao,
    cancelado_2.processo,
    cancelado_2.data_operacao,
    sum(cancelado_2.valor) AS valor,
    sum(cancelado_2.corrigido) AS corrigido,
    sum(cancelado_2.juros) AS juros,
    sum(cancelado_2.multa) AS multa,
    sum(cancelado_2.desconto) AS desconto,
    sum(cancelado_2.total) AS total,
    cancelado_2.data_movimento::text 
FROM (
    SELECT * FROM (
        SELECT DISTINCT ON (arrecant.*)
            cgm.z01_nome as nome,
            cgm.z01_numcgm as numcgm,
            {$colunaOrigem}
            cgm.z01_cgccpf as cpf,
            cgm.z01_ender as endereco,
            cgm.z01_compl as complemento,
            cgm.z01_numero as numero,
            cgm.z01_munic as municipio,
            cgm.z01_uf as uf,
            arrecant.k00_numpre as numpre,
            arrecant.k00_numpar as numpar,
            arrecant.k00_numtot as numtot,
            arretipo.k00_descr as descricao,
            arrecant.k00_dtvenc as data_vencimento,
            '' as calculo,
            'Cancelado' AS situacao,
            p58_codigo || '/' || p58_ano AS processo,
            arrecant.k00_dtoper data_operacao,
            cancdebitosprocreg.k24_vlrhis AS valor,
            cancdebitosprocreg.k24_vlrcor AS corrigido,
            cancdebitosprocreg.k24_juros AS juros,
            cancdebitosprocreg.k24_multa AS multa,
            cancdebitosprocreg.k24_desconto AS desconto,
            cancdebitosprocreg.k24_vlrhis AS total,
            cancdebitosproc.k23_data AS data_movimento
        FROM
            arrecant
            INNER JOIN cancdebitosreg ON arrecant.k00_numpre = cancdebitosreg.k21_numpre
            AND arrecant.k00_numpar = cancdebitosreg.k21_numpar
            AND arrecant.k00_receit = cancdebitosreg.k21_receit
            INNER JOIN cancdebitosprocreg ON cancdebitosprocreg.k24_cancdebitosreg = cancdebitosreg.k21_sequencia
            INNER JOIN arretipo ON arrecant.k00_tipo = arretipo.k00_tipo
            INNER JOIN cancdebitosproc ON cancdebitosproc.k23_codigo = cancdebitosprocreg.k24_codigo
            INNER JOIN cancdebitos ON cancdebitos.k20_codigo = cancdebitosreg.k21_codigo
            AND cancdebitos.k20_instit = 1
            LEFT JOIN cancdebitosprot ON k25_cancdebitos = k20_codigo
            LEFT JOIN protprocesso ON k25_codproc = p58_codproc
            INNER JOIN arreinstit ON arrecant.k00_numpre = arreinstit.k00_numpre
            {$innerJoinOrigemCancelado}
            WHERE
                {$whereCancelado}
        ) AS cancelado
) AS cancelado_2
GROUP BY
    cancelado_2.numpre,
    cancelado_2.numpar,
    cancelado_2.nome,
    cancelado_2.numcgm,
    {$colunaOrigem2}
    cancelado_2.cpf,
    cancelado_2.endereco,
    cancelado_2.complemento,
    cancelado_2.numero,
    cancelado_2.municipio,
    cancelado_2.uf,
    cancelado_2.numtot,
    cancelado_2.descricao,
    cancelado_2.data_vencimento,
    cancelado_2.situacao,
    cancelado_2.processo,
    cancelado_2.data_operacao,
    cancelado_2.data_movimento
";

// SQL Prescrito
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "arreinscr.k00_inscr AS inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "prescrito.{$selectFiltroBusca},";
        $colunaOrigem2 = "prescrito_2.{$selectFiltroBusca},"; 
    }
}
$sqlPrescrito = "SELECT
    prescrito_2.nome,
    prescrito_2.numcgm,
    {$colunaOrigem2}
    prescrito_2.cpf,
    prescrito_2.endereco,
    prescrito_2.complemento,
    prescrito_2.numero,
    prescrito_2.municipio,
    prescrito_2.uf,
    prescrito_2.numpre,
    prescrito_2.numpar,
    prescrito_2.numtot,
    prescrito_2.descricao,
    prescrito_2.data_vencimento,
    prescrito_2.situacao,
    prescrito_2.processo,
    prescrito_2.data_operacao,
    sum(prescrito_2.valor) AS valor,
    sum(prescrito_2.corrigido) AS corrigido,
    sum(prescrito_2.juros) AS juros,
    sum(prescrito_2.multa) AS multa,
    sum(prescrito_2.desconto) AS desconto,
    sum(prescrito_2.total) AS total,
    prescrito_2.data_movimento::text 
FROM (
    SELECT * FROM (
        SELECT DISTINCT ON (arrecant.*)
            cgm.z01_nome as nome,
            cgm.z01_numcgm as numcgm,
            {$colunaOrigem}
            cgm.z01_cgccpf as cpf,
            cgm.z01_ender as endereco,
            cgm.z01_compl as complemento,
            cgm.z01_numero as numero,
            cgm.z01_munic as municipio,
            cgm.z01_uf as uf,
            arrecant.k00_numpre as numpre,
            arrecant.k00_numpar as numpar,
            arrecant.k00_numtot as numtot,
            arretipo.k00_descr as descricao,
            arrecant.k00_dtvenc as data_vencimento,
            '' as calculo,
            'Prescrito' AS situacao,
            '0' AS processo,
            arrecant.k00_dtoper data_operacao,
            arreprescr.k30_valor AS valor,
            arreprescr.k30_vlrcorr AS corrigido,
            arreprescr.k30_vlrjuros AS juros,
            arreprescr.k30_multa AS multa,
            arreprescr.k30_desconto AS desconto,
            arreprescr.k30_vlrcorr + arreprescr.k30_vlrjuros + arreprescr.k30_multa + arreprescr.k30_desconto AS total,
            prescricao.k31_data AS data_movimento
        FROM
            arrecant
            INNER JOIN arreprescr ON arrecant.k00_numpre = arreprescr.k30_numpre
            AND arrecant.k00_numpar = arreprescr.k30_numpar
            AND arrecant.k00_receit = arreprescr.k30_receit
            INNER JOIN arretipo ON arrecant.k00_tipo = arretipo.k00_tipo
            INNER JOIN prescricao ON arreprescr.k30_prescricao = prescricao.k31_codigo
            INNER JOIN arreinstit ON arrecant.k00_numpre = arreinstit.k00_numpre
            {$innerJoinOrigemPrescrito}
            WHERE
                {$wherePrescrito}
        ) AS prescrito
    ) AS prescrito_2
    GROUP BY
        prescrito_2.numpre,
        prescrito_2.numpar,
        prescrito_2.nome,
        prescrito_2.numcgm,
        {$colunaOrigem2}
        prescrito_2.cpf,
        prescrito_2.endereco,
        prescrito_2.complemento,
        prescrito_2.numero,
        prescrito_2.municipio,
        prescrito_2.uf,
        prescrito_2.numtot,
        prescrito_2.descricao,
        prescrito_2.data_vencimento,
        prescrito_2.situacao,
        prescrito_2.processo,
        prescrito_2.data_operacao,
        prescrito_2.data_movimento
";

// SQL Suspenso
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "arreinscr.k00_inscr AS inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "suspenso.{$selectFiltroBusca},";
        $colunaOrigem2 = "suspenso_2.{$selectFiltroBusca},"; 
    }
}

$sqlSuspenso = "SELECT
    suspenso_2.nome,
    suspenso_2.numcgm,
    {$colunaOrigem2}
    suspenso_2.cpf,
    suspenso_2.endereco,
    suspenso_2.complemento,
    suspenso_2.numero,
    suspenso_2.municipio,
    suspenso_2.uf,
    suspenso_2.numpre,
    suspenso_2.numpar,
    suspenso_2.numtot,
    suspenso_2.descricao,
    suspenso_2.data_vencimento,
    suspenso_2.situacao,
    suspenso_2.processo,
    suspenso_2.data_operacao,
    sum(suspenso_2.valor) AS valor,
    sum(suspenso_2.corrigido) AS corrigido,
    sum(suspenso_2.juros) AS juros,
    sum(suspenso_2.multa) AS multa,
    sum(suspenso_2.desconto) AS desconto,
    sum(suspenso_2.total) AS total,
    suspenso_2.data_movimento::text 
FROM (
    SELECT * FROM (
        SELECT DISTINCT ON (arresusp.*)
            cgm.z01_nome as nome,
            cgm.z01_numcgm as numcgm,
            {$colunaOrigem}
            cgm.z01_cgccpf as cpf,
            cgm.z01_ender as endereco,
            cgm.z01_compl as complemento,
            cgm.z01_numero as numero,
            cgm.z01_munic as municipio,
            cgm.z01_uf as uf,
            arresusp.k00_numpre as numpre,
            arresusp.k00_numpar as numpar,
            arresusp.k00_numtot as numtot,
            arretipo.k00_descr as descricao,
            arresusp.k00_dtvenc as data_vencimento,
            '' as calculo,
            'Suspenso' AS situacao,
            '0' AS processo,
            arresusp.k00_dtoper as data_operacao,
            arresusp.k00_valor AS valor,
            arresusp.k00_vlrcor AS corrigido,
            arresusp.k00_vlrjur AS juros,
            arresusp.k00_vlrmul AS multa,
            arresusp.k00_vlrdes AS desconto,
            arresusp.k00_vlrcor + arresusp.k00_vlrjur + arresusp.k00_vlrmul + arresusp.k00_vlrdes AS total,
            suspensao.ar18_data AS data_movimento
        FROM
            arresusp
            INNER JOIN arretipo ON arresusp.k00_tipo = arretipo.k00_tipo
            INNER JOIN suspensao ON suspensao.ar18_sequencial = arresusp.k00_suspensao
            INNER JOIN arreinstit ON arresusp.k00_numpre = arreinstit.k00_numpre
            {$innerJoinOrigemSuspenso}
            WHERE
                {$whereSuspenso}
        ) AS suspenso
    ) AS suspenso_2
    GROUP BY
        suspenso_2.numpre,
        suspenso_2.numpar,
        {$colunaOrigem2}
        suspenso_2.nome,
        suspenso_2.numcgm,
        suspenso_2.cpf,
        suspenso_2.endereco,
        suspenso_2.complemento,
        suspenso_2.numero,
        suspenso_2.municipio,
        suspenso_2.uf,
        suspenso_2.numtot,
        suspenso_2.descricao,
        suspenso_2.data_vencimento,
        suspenso_2.situacao,
        suspenso_2.processo,
        suspenso_2.data_operacao,
        suspenso_2.data_movimento
";

// SQL Inscrito Cobrança Adm
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "arreinscr.k00_inscr AS inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "inscrcobadm.{$selectFiltroBusca},";
        $colunaOrigem2 = "inscrcobadm_2.{$selectFiltroBusca},"; 
    }
}

$sqlInscritoCobAdm = "SELECT
        inscrcobadm_2.nome,
        inscrcobadm_2.numcgm,
        {$colunaOrigem2}
        inscrcobadm_2.cpf,
        inscrcobadm_2.endereco,
        inscrcobadm_2.complemento,
        inscrcobadm_2.numero,
        inscrcobadm_2.municipio,
        inscrcobadm_2.uf,
        inscrcobadm_2.numpre,
        inscrcobadm_2.numpar,
        inscrcobadm_2.numtot,
        inscrcobadm_2.descricao,
        inscrcobadm_2.data_vencimento,
        inscrcobadm_2.situacao,
        inscrcobadm_2.processo,
        inscrcobadm_2.data_operacao,
        sum(inscrcobadm_2.valor) AS valor,
        sum(inscrcobadm_2.corrigido) AS corrigido,
        sum(inscrcobadm_2.juros) AS juros,
        sum(inscrcobadm_2.multa) AS multa,
        sum(inscrcobadm_2.desconto) AS desconto,
        sum(inscrcobadm_2.total) AS total,
        inscrcobadm_2.data_movimento::text 
    FROM (SELECT
            inscrcobadm.nome,
            inscrcobadm.numcgm,
            {$colunaOrigem1}
            inscrcobadm.cpf,
            inscrcobadm.endereco,
            inscrcobadm.complemento,
            inscrcobadm.numero,
            inscrcobadm.municipio,
            inscrcobadm.uf,
            inscrcobadm.numpre,
            inscrcobadm.numpar,
            inscrcobadm.numtot,
            inscrcobadm.descricao,
            inscrcobadm.data_vencimento,
            inscrcobadm.calculo,
            inscrcobadm.situacao,
            inscrcobadm.processo,
            inscrcobadm.data_operacao,
            coalesce(substr(inscrcobadm.calculo, 02, 13) :: float8, 0) AS valor,
            coalesce(substr(inscrcobadm.calculo, 15, 13) :: float8, 0) AS corrigido,
            coalesce(substr(inscrcobadm.calculo, 28, 13) :: float8, 0) AS juros,
            coalesce(substr(inscrcobadm.calculo, 41, 13) :: float8, 0) AS multa,
            coalesce(substr(inscrcobadm.calculo, 54, 13) :: float8, 0) AS desconto,
            (
                substr(inscrcobadm.calculo, 15, 13) :: float8 + substr(inscrcobadm.calculo, 28, 13) :: float8 + substr(inscrcobadm.calculo, 41, 13) :: float8 - substr(inscrcobadm.calculo, 54, 13) :: float8
            ) AS total,
            inscrcobadm.data_movimento
        FROM
            (SELECT
                DISTINCT ON (arreold.*)
                cgm.z01_nome as nome,
                cgm.z01_numcgm as numcgm,
                {$colunaOrigem}
                cgm.z01_cgccpf as cpf,
                cgm.z01_ender as endereco,
                cgm.z01_compl as complemento,
                cgm.z01_numero as numero,
                cgm.z01_munic as municipio,
                cgm.z01_uf as uf,
                arreold.k00_numpre as numpre,
                arreold.k00_numpar as numpar,
                arreold.k00_numtot as numtot,
                arretipo.k00_descr as descricao,
                arreold.k00_dtvenc as data_vencimento,
                fc_calculaold(dv13_numpre :: int,dv13_numpar :: int,dv13_receita,dv05_dtinsc,dv05_dtinsc,EXTRACT(year FROM dv05_dtinsc)::integer) as calculo,
                'Inscrito em Cob. Adm' AS situacao,
                '0' AS processo,
                arreold.k00_dtoper data_operacao,
                diversos.dv05_dtinsc AS data_movimento
            FROM
                diverimportaold
                INNER JOIN diversos ON dv13_diversos = dv05_coddiver
                INNER JOIN arreold ON arreold.k00_numpre = dv13_numpre
                AND k00_numpar = dv13_numpar
                INNER JOIN arretipo ON arreold.k00_tipo = arretipo.k00_tipo
                INNER JOIN arreinstit ON arreold.k00_numpre = arreinstit.k00_numpre
                {$innerJoinOrigemInscritoCobAdm}
                    WHERE
                        {$whereInscritoCobAdm}
            ) AS inscrcobadm
        ) AS inscrcobadm_2
        GROUP BY 
            inscrcobadm_2.numpre,
            inscrcobadm_2.numpar,
            inscrcobadm_2.nome,
            inscrcobadm_2.numcgm,
            {$colunaOrigem2}
            inscrcobadm_2.cpf,
            inscrcobadm_2.endereco,
            inscrcobadm_2.complemento,
            inscrcobadm_2.numero,
            inscrcobadm_2.municipio,
            inscrcobadm_2.uf,
            inscrcobadm_2.numtot,
            inscrcobadm_2.descricao,
            inscrcobadm_2.data_vencimento,
            inscrcobadm_2.situacao,
            inscrcobadm_2.processo,
            inscrcobadm_2.data_operacao,
            inscrcobadm_2.data_movimento
";

// SQL Inscrito Divida Ativa
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "arreinscr.k00_inscr AS inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "inscrdiv.{$selectFiltroBusca},";
        $colunaOrigem2 = "inscrdiv_2.{$selectFiltroBusca},"; 
    }
}

$sqlInscritoDivida = "SELECT
        inscrdiv_2.nome,
        inscrdiv_2.numcgm,
        {$colunaOrigem2}
        inscrdiv_2.cpf,
        inscrdiv_2.endereco,
        inscrdiv_2.complemento,
        inscrdiv_2.numero,
        inscrdiv_2.municipio,
        inscrdiv_2.uf,
        inscrdiv_2.numpre,
        inscrdiv_2.numpar,
        inscrdiv_2.numtot,
        inscrdiv_2.descricao,
        inscrdiv_2.data_vencimento,
        inscrdiv_2.situacao,
        inscrdiv_2.processo,
        inscrdiv_2.data_operacao,
        sum(inscrdiv_2.valor) AS valor,
        sum(inscrdiv_2.corrigido) AS corrigido,
        sum(inscrdiv_2.juros) AS juros,
        sum(inscrdiv_2.multa) AS multa,
        sum(inscrdiv_2.desconto) AS desconto,
        sum(inscrdiv_2.total) AS total,
        inscrdiv_2.data_movimento::text 
    FROM (
        SELECT
            inscrdiv.nome,
            inscrdiv.numcgm,
            {$colunaOrigem1}
            inscrdiv.cpf,
            inscrdiv.endereco,
            inscrdiv.complemento,
            inscrdiv.numero,
            inscrdiv.municipio,
            inscrdiv.uf,
            inscrdiv.numpre,
            inscrdiv.numpar,
            inscrdiv.numtot,
            inscrdiv.descricao,
            inscrdiv.data_vencimento,
            inscrdiv.calculo,
            inscrdiv.situacao,
            inscrdiv.processo,
            inscrdiv.data_operacao,
            coalesce(substr(inscrdiv.calculo, 02, 13) :: float8, 0) AS valor,
            coalesce(substr(inscrdiv.calculo, 15, 13) :: float8, 0) AS corrigido,
            coalesce(substr(inscrdiv.calculo, 28, 13) :: float8, 0) AS juros,
            coalesce(substr(inscrdiv.calculo, 41, 13) :: float8, 0) AS multa,
            coalesce(substr(inscrdiv.calculo, 54, 13) :: float8, 0) AS desconto,
            (substr(inscrdiv.calculo, 15, 13) :: float8 + substr(inscrdiv.calculo, 28, 13) :: float8 + substr(inscrdiv.calculo, 41, 13) :: float8 - substr(inscrdiv.calculo, 54, 13) :: float8) AS total,
            inscrdiv.data_movimento
        FROM
            (
                SELECT
                    DISTINCT ON (arreold.*)
                    cgm.z01_nome as nome,
                    cgm.z01_numcgm as numcgm,
                    {$colunaOrigem}
                    cgm.z01_cgccpf as cpf,
                    cgm.z01_ender as endereco,
                    cgm.z01_compl as complemento,
                    cgm.z01_numero as numero,
                    cgm.z01_munic as municipio,
                    cgm.z01_uf as uf,
                    arreold.k00_numpre as numpre,
                    arreold.k00_numpar as numpar,
                    arreold.k00_numtot as numtot,
                    arretipo.k00_descr as descricao,
                    arreold.k00_dtvenc as data_vencimento,
                    fc_calculaold(arreold.k00_numpre,arreold.k00_numpar,arreold.k00_receit,divida.v01_dtinclusao,divida.v01_dtinclusao,EXTRACT(year FROM divida.v01_dtinclusao)::integer) as calculo,
                    'Inscrito em Dívida' AS situacao,
                    '0' AS processo,
                    arreold.k00_dtoper as data_operacao,
                    divida.v01_dtinclusao AS data_movimento
                FROM
                    arreold
                    INNER JOIN divold ON arreold.k00_numpre = divold.k10_numpre
                    AND arreold.k00_numpar = divold.k10_numpar
                    AND arreold.k00_receit = divold.k10_receita
                    INNER JOIN arretipo ON arreold.k00_tipo = arretipo.k00_tipo
                    INNER JOIN divida ON divida.v01_coddiv = divold.k10_coddiv
                    INNER JOIN arreinstit ON arreold.k00_numpre = arreinstit.k00_numpre
                    {$innerJoinOrigemInscritoDivida}
                        WHERE
                            {$whereInscritoDivida}
                ) AS inscrdiv
            ) AS inscrdiv_2
            GROUP BY
                inscrdiv_2.numpre,
                inscrdiv_2.numpar,
                inscrdiv_2.nome,
                inscrdiv_2.numcgm,
                {$colunaOrigem2}
                inscrdiv_2.cpf,
                inscrdiv_2.endereco,
                inscrdiv_2.complemento,
                inscrdiv_2.numero,
                inscrdiv_2.municipio,
                inscrdiv_2.uf,
                inscrdiv_2.numtot,
                inscrdiv_2.descricao,
                inscrdiv_2.data_vencimento,
                inscrdiv_2.situacao,
                inscrdiv_2.processo,
                inscrdiv_2.data_operacao,
                inscrdiv_2.data_movimento

";

// SQL Parcelado
if (!empty($selectFiltroBusca) && $selectFiltroBusca != 'cgm') {
    if ($selectFiltroBusca == 'matric') {
        $colunaOrigem = "cgm.j01_matric AS matric,";
    } else if ($selectFiltroBusca == 'inscr') {
        $colunaOrigem = "arreinscr.k00_inscr AS inscr,";
    } 
    if ($selectFiltroBusca == 'numpre') {
        $colunaOrigem = "";
        $colunaOrigem1 = "";
        $colunaOrigem2 = "";
    }
    else {
        $colunaOrigem1 = "parcelado.{$selectFiltroBusca},";
        $colunaOrigem2 = "parcelado_2.{$selectFiltroBusca},"; 
    }
}

$sqlParcelado = "SELECT
    parcelado_2.nome,
    parcelado_2.numcgm,
    {$colunaOrigem2}
    parcelado_2.cpf,
    parcelado_2.endereco,
    parcelado_2.complemento,
    parcelado_2.numero,
    parcelado_2.municipio,
    parcelado_2.uf,
    parcelado_2.numpre,
    parcelado_2.numpar,
    parcelado_2.numtot,
    parcelado_2.descricao,
    parcelado_2.data_vencimento,
    parcelado_2.situacao,
    parcelado_2.processo,
    parcelado_2.data_operacao,
    sum(parcelado_2.valor) AS valor,
    sum(parcelado_2.corrigido) AS corrigido,
    sum(parcelado_2.juros) AS juros,
    sum(parcelado_2.multa) AS multa,
    sum(parcelado_2.desconto) AS desconto,
    sum(parcelado_2.total) AS total,
    parcelado_2.data_movimento::text 
FROM (
    SELECT * FROM (
        SELECT DISTINCT ON (arreoldcalc.*)
            cgm.z01_nome as nome,
            cgm.z01_numcgm as numcgm,
            {$colunaOrigem}
            cgm.z01_cgccpf as cpf,
            cgm.z01_ender as endereco,
            cgm.z01_compl as complemento,
            cgm.z01_numero as numero,
            cgm.z01_munic as municipio,
            cgm.z01_uf as uf,
            arreold.k00_numpre as numpre,
            arreold.k00_numpar as numpar,
            arreold.k00_numtot as numtot,
            arretipo.k00_descr as descricao,
            arreold.k00_dtvenc as data_vencimento,
            '' as calculo,
            'Parcelado' AS situacao,
            '0' AS processo,
            arreold.k00_dtoper as data_operacao,
            arreoldcalc.k00_vlrhis AS valor,
            arreoldcalc.k00_vlrcor AS corrigido,
            arreoldcalc.k00_vlrjur AS juros,
            arreoldcalc.k00_vlrmul AS multa,
            arreoldcalc.k00_vlrdes AS desconto,
            arreoldcalc.k00_vlrcor + arreoldcalc.k00_vlrjur + arreoldcalc.k00_vlrmul + arreoldcalc.k00_vlrdes AS total,
            arreoldcalc.k00_dtcalc AS data_movimento
        FROM
            arreoldcalc
            INNER JOIN arreold 
                    ON arreold.k00_numpre = arreoldcalc.k00_numpre
                   AND arreold.k00_numpar = arreoldcalc.k00_numpar
                   AND arreold.k00_receit = arreoldcalc.k00_receit
            INNER JOIN termo ON v07_numpre = arreoldcalc.k00_numpre
            LEFT JOIN termodiv ON v07_parcel = termodiv.parcel
            LEFT JOIN termoini ON v07_parcel = termoini.parcel
            LEFT JOIN termocontrib ON v07_parcel = termocontrib.parcel
            LEFT JOIN termodiver ON v07_parcel = dv10_parcel
            INNER JOIN arretipo ON arreold.k00_tipo = arretipo.k00_tipo
            INNER JOIN arreinstit ON arreold.k00_numpre = arreinstit.k00_numpre
            {$innerJoinOrigemParcelado}
                    WHERE
                        {$whereParcelado}
        ) AS parcelado
    ) AS parcelado_2
        GROUP BY
            parcelado_2.numpre,
            parcelado_2.numpar,
            parcelado_2.nome,
            parcelado_2.numcgm,
            {$colunaOrigem2}
            parcelado_2.cpf,
            parcelado_2.endereco,
            parcelado_2.complemento,
            parcelado_2.numero,
            parcelado_2.municipio,
            parcelado_2.uf,
            parcelado_2.numtot,
            parcelado_2.descricao,
            parcelado_2.data_vencimento,
            parcelado_2.situacao,
            parcelado_2.processo,
            parcelado_2.data_operacao,
            parcelado_2.data_movimento
";

// SQLs por situação de debito
switch ($selectSituacaoDebito) {
    case 'pendente':
        $sql = $sqlPendente;
        break;

    case 'pago':
        $sql = "{$sqlPago} UNION ALL {$sqlPagoParcial}";
        break;

    case 'cancelado':
        $sql = $sqlCancelado;
        break;

    case 'prescrito':
        $sql = $sqlPrescrito;
        break;

    case 'suspenso':
        $sql = $sqlSuspenso;
        break;

    case 'inscrito em cob adm':
        $sql = $sqlInscritoCobAdm;
        break;

    case 'inscrito em divida':
        $sql = $sqlInscritoDivida;
        break; 

    case 'parcelado':
        $sql = $sqlParcelado;
        break;

    default:
        $sql = "{$sqlPendente}
        UNION ALL {$sqlPago}
        UNION ALL {$sqlPagoParcial}
        UNION ALL {$sqlCancelado}
        UNION ALL {$sqlPrescrito}
        UNION ALL {$sqlSuspenso}
        UNION ALL {$sqlInscritoCobAdm}
        UNION ALL {$sqlInscritoDivida}
        UNION ALL {$sqlParcelado}
        ";
        break;
}

if (empty($matric)) {
    $matric = 0;
}

if (empty($inscr)) {
    $inscr = 0;
}


$sql  = " SELECT *, 
                    CASE WHEN '{$selectFiltroBusca}' = 'cgm' THEN
                         CASE WHEN EXISTS (select 1 
                                             from arrematric 
                                            where k00_numpre = numpre limit 1) THEN
                                   (select 'M-'||k00_matric::varchar
                                      from arrematric 
                                     where arrematric.k00_numpre = numpre limit 1
                                   )::varchar
                              WHEN EXISTS (select 1 
                                             from arreinscr
                                            where k00_numpre = numpre limit 1) THEN
                                   (select 'I-'||k00_inscr::varchar
                                      from arreinscr 
                                     where k00_numpre = numpre limit 1
                                   )::varchar
                             ELSE ''::varchar      
                         END      
                     
                         WHEN '{$selectFiltroBusca}' = 'matric' THEN
                              'M-'||$matric
 
                         WHEN '{$selectFiltroBusca}' = 'inscr' THEN
                              'I-'||$inscr
                         ELSE ''::varchar
                    END  AS origem_aux,

                    CASE WHEN '{$selectFiltroBusca}' = 'matric' THEN 
                              (select j34_setor||'/'||j34_quadra||'/'||j34_lote 
                                 from iptubase inner join lote on iptubase.j01_idbql = lote.j34_idbql
                                where iptubase.j01_matric = $matric)::varchar
                         ELSE ''::varchar
                    END AS setor_quadra_lote,

                    CASE WHEN '{$selectFiltroBusca}' = 'matric' AND 
                              EXISTS (select 1 from iptuender where j43_matric = $matric) THEN 
                              (select trim(replace(j43_ender ||', '||
                                              coalesce(j43_numimo::varchar, '')::varchar ||
                                              ', ' || coalesce(j43_comple,'') ||
                                              ', BAIRRO '|| coalesce(j43_bairro, '')
                                              , ', ,',''
                                       ))
                                from iptuender where j43_matric = $matric)::varchar
                        WHEN '{$selectFiltroBusca}' = 'matric' AND 
                              NOT EXISTS (select 1 from iptuender where j43_matric = $matric) THEN 
                              (select trim(replace(z01_enderpri ||', '||
                                              coalesce(z01_numeropri::varchar, '')::varchar ||
                                              ', ' || coalesce(z01_complpri,'') ||
                                              ', BAIRRO '|| coalesce(z01_bairropri, '')
                                              , ', ,',''
                                       ))
                                from proprietario where j01_matric = $matric)::varchar                                
                         WHEN '{$selectFiltroBusca}' = 'inscr' THEN 
                            
                            (select z01_ender ||
                                    replace(', '||coalesce(z01_numero::varchar, '') ||
                                            ', '||coalesce(z01_compl::varchar, '')::varchar ||
                                            ', BAIRRO '|| coalesce(z01_bairro, '')
                                            , ', ,',''
                                    )
                                from empresa where empresa.q02_inscr = $inscr and empresa.q88_tipo = 'P'
                            )::varchar                             
                         ELSE ''::varchar
                    END AS ender_aux,

                    CASE WHEN '{$selectFiltroBusca}' = 'matric' THEN
                              (select j40_refant from iptuant where j40_matric = $matric)::varchar
                         WHEN '{$selectFiltroBusca}' = 'inscr' THEN
                              (select q02_inscmu from issbase where q02_inscr = $inscr)::varchar
                         ELSE ''::varchar
                    END AS refant

            FROM ({$sql}) AS x";

$sql .= " ORDER BY data_movimento, data_operacao"; 

$result = db_query($sql) or die($sql);

if (!$result) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Erro ao buscar débitos.');
}

if (pg_num_rows($result) == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Nenhum registro encontrado para os filtros selecionados.');
}

if (empty($selectSituacaoDebito)){
    $selectSituacaoDebito = 'Todas';
}

if ($tipoArquivo == "2") {

    $dadosContribuinte = \db_utils::makeCollectionFromRecord($result, function($oDados){
        $aDadosAux = [];

        $aDadosAux[] = $oDados->numpre;
        $aDadosAux[] = $oDados->numpar;
        $aDadosAux[] = $oDados->numtot;
        $aDadosAux[] = date('Y', strtotime($oDados->data_operacao));
        $aDadosAux[] = substr($oDados->descricao,0,28);
        $aDadosAux[] = date('d/m/Y', strtotime($oDados->data_vencimento));
        $aDadosAux[] = $oDados->valor;
        $aDadosAux[] = $oDados->corrigido;
        $aDadosAux[] = $oDados->juros;
        $aDadosAux[] = $oDados->multa;
        $aDadosAux[] = $oDados->desconto;
        $aDadosAux[] = $oDados->total;
        $aDadosAux[] = strtoupper($oDados->situacao);
        $aDadosAux[] = $oDados->processo;
        $aDadosAux[] = empty($oDados->data_movimento) ? "-" : date('d/m/Y', strtotime($oDados->data_movimento));

        return $aDadosAux;
    });

    // Monta dados para o cabeçalho da instituição
    $dados = db_query("select nomeinst,
            db21_compl,
            trim(ender)||',
            '||trim(cast(numero as text)) as ender,
            trim(ender) as rua,
            munic,
            numero,
            uf,
            cgc,
            telef,
            email,
            url,
            logo
        from db_config where codigo = ".db_getsession("DB_instit")
    );
    $url = @pg_result($dados,0,"url");
    $sComplento = substr(trim(pg_result($dados,0,"db21_compl") ),0,20 );
    if ($sComplento != '' || $sComplento != null ) {
    	$sComplento = ", ".substr(trim(pg_result($dados,0,"db21_compl") ),0,20 );
    }

    // Monta cabeçalho do relatório
    $header1 = "Extrato do Contribuinte";
    $header2 = "Situação do Débito - ".strtoupper($selectSituacaoDebito);
    $header3 = "Filtro de Busca - ".strtoupper($selectFiltroBusca);

    if ($selectFiltroBusca == "cgm") {
        $header4 = "CGM: ".$numcgm;
    } else if ($selectFiltroBusca == "matric") {
        $header4 = "Matrícula: ".$matric;
    } else if ($selectFiltroBusca == "inscr") {
        $header4 = "Inscrição: ".$inscr;
    }

    if ($exercicio_inicial != $exercicio_final) {
        $header5 = "Intervalo de Exercício: De " . $exercicio_inicial . " até " . $exercicio_final;
    } else {
        $header5 = "Exercício: " . $exercicio_inicial;
    }

    // Cria XLS
    $parser = new Parser();
    $parser->addImage("imagens/files/".pg_result($dados,0,"logo"), "A1", [
        'width'=>75,
        'offsetx'=>04,
        'offsetY'=>10
    ]);

    // Células de cabeçalho instituição
    $parser->addCell('B1',utf8_encode(pg_result($dados,0,"nomeinst")));
    $parser->addCell('B2',trim(utf8_encode(pg_result($dados,0,"rua"))).", ".trim((pg_result($dados,0,"numero"))).$sComplento );
    $parser->addCell('B3',trim(utf8_encode(pg_result($dados,0,"munic")))." - ".pg_result($dados,0,"uf"));
    $parser->addCell('B4',trim(pg_result($dados,0,"telef"))."   -    CNPJ : ".db_formatar(pg_result($dados,0,"cgc"),"cnpj"));
    $parser->addCell('B5',trim(pg_result($dados,0,"email")));
    $parser->addCell('B6',$url);

    // Células de cabeçalho relatório
    $parser->addCell('K1',$header1);
    $parser->addCell('K2',utf8_encode($header2));
    $parser->addCell('K3',$header3);
    $parser->addCell('K4',utf8_encode($header4));
    $parser->addCell('K5',utf8_encode($header5));

    // Células de título das colunas dos dados
    $parser->addCell('A8','NUMPRE');
    $parser->addCell('B8','P');
    $parser->addCell('C8','T');
    $parser->addCell('D8','ANO');
    $parser->addCell('E8','TIPO DE DEBITO');
    $parser->addCell('F8','VENC');
    $parser->addCell('G8','VALOR');
    $parser->addCell('H8','CORRIGIDO');
    $parser->addCell('I8','JUROS');
    $parser->addCell('J8','MULTA');
    $parser->addCell('K8','DESCONTO');
    $parser->addCell('L8','TOTAL');
    $parser->addCell('M8','SITUACAO');
    $parser->addCell('N8','PROCESSO');
    $parser->addCell('O8','MOVIMENTO');

    // Mescla células de cabeçalho
    $parser->mergeCells('A1:A6');

    $parser->mergeCells('B1:G1');
    $parser->mergeCells('B2:G2');
    $parser->mergeCells('B3:G3');
    $parser->mergeCells('B4:G4');
    $parser->mergeCells('B5:G5');
    $parser->mergeCells('B6:G6');

    $parser->mergeCells('K1:O1');
    $parser->mergeCells('K2:O2');
    $parser->mergeCells('K3:O3');
    $parser->mergeCells('K4:O4');
    $parser->mergeCells('K5:O5');

    // Atribui estilo para as células
    $styleArrayInstit = [
        'font' => [
            'bold' => true,
            'italic' => true
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    $styleArrayCabecalhoRelatorio = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
            'rotation' => 90,
            'startColor' => [
                'argb' => 'FFBCBCBC',
            ],
            'endColor' => [
                'argb' => 'FFFFFFFF',
            ],
        ],
    ];

    $styleAlignCenter = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];

    $styleBorderBottom = [
        'borders' => [
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            ],
        ],
    ];

    $parser->addCellStyle('B1',$styleArrayInstit);
    $parser->addCellStyle('K1:K5',$styleArrayCabecalhoRelatorio);
    $parser->addCellStyle('A6:O6',$styleBorderBottom);

    // Percorre dados contribuinte
    $i=0;
    $coord=9;   
    foreach ($dadosContribuinte as $contribuinte) {
        $parser->addCell('A'.$coord,$contribuinte[0]);
        $parser->addCell('B'.$coord,$contribuinte[1]);
        $parser->addCell('C'.$coord,$contribuinte[2]);
        $parser->addCell('D'.$coord,$contribuinte[3]);
        $parser->addCell('E'.$coord,$contribuinte[4]);
        $parser->addCell('F'.$coord,$contribuinte[5]);
        $parser->addCell('G'.$coord,$contribuinte[6]);
        $parser->addCell('H'.$coord,$contribuinte[7]);
        $parser->addCell('I'.$coord,$contribuinte[8]);
        $parser->addCell('J'.$coord,$contribuinte[9]);
        $parser->addCell('K'.$coord,$contribuinte[10]);
        $parser->addCell('L'.$coord,$contribuinte[11]);
        $parser->addCell('M'.$coord,$contribuinte[12]);
        $parser->addCell('N'.$coord,$contribuinte[13]);
        $parser->addCell('O'.$coord,$contribuinte[14]);

        $parser->addCellStyle('A8:O8',$styleAlignCenter);
        $parser->addCellStyle('A'.$coord.':O'.$coord,$styleAlignCenter);

        $i++;
        $coord++;

    }

    $path = 'extratoContribuinte_'.date('d-m-Y', db_getsession('DB_datausu')).'.xlsx';

    $parser->parse();
    $parser->download($path);

    exit;
}

// Cria PDF
$oPdf = new ECidade\Pdf\Pdf('L');
$oPdf->init(false);
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(false);
$oPdf->SetFillColor(235);

$head2 = "Extrato do Contribuinte";
$oPdf->addTitulo("Extrato do Contribuinte");
$oPdf->addTitulo("");
$oPdf->addTitulo("Situação do Débito - ".strtoupper($selectSituacaoDebito));
$oPdf->addTitulo("Filtro de Busca - ".strtoupper($selectFiltroBusca));

if ($selectFiltroBusca == "cgm") {
    $oPdf->addTitulo("CGM: ".$numcgm);
} else if ($selectFiltroBusca == "matric") {
    $oPdf->addTitulo("Matrícula: ".$matric);
} else if ($selectFiltroBusca == "inscr") {
    $oPdf->addTitulo("Inscrição: ".$inscr);
}

if ($exercicio_inicial != $exercicio_final) {
    $oPdf->addTitulo("Intervalo de Exercício: De " . $exercicio_inicial . " até " . $exercicio_final);
} else {
    $oPdf->addTitulo("Exercício: " . $exercicio_inicial);
}

$oDadosCgm = db_utils::getCollectionByRecord($result);

$arrayContribuintes = [];

// Organiza dados por CGM
foreach ($oDadosCgm AS $dadoCgm) {
    $arrayContribuintes[$dadoCgm->numcgm] [] = $dadoCgm;
}

$arrayTotalSituacaoDebito = [];
$arrayDescricaoSituacaoDebito = [];
$arrayTotalGeral = [];

// Percorre dados contribuinte
foreach ($arrayContribuintes as $contribuinte) {
    $oPdf->AddPage();
    fc_dadosContribuinte($oPdf, $contribuinte[0]);
    fc_cabecalhoDebitos($oPdf);

    // Percorre dados debito do contribuinte
    foreach ($contribuinte AS $debito) {
        fc_quebraPagina($oPdf, $contribuinte[0]);
        fc_imprimedadoscontribuinte($oPdf, $debito, $selectFiltroBusca);

        if ($selectFiltroBusca == 'cgm') {
            $debitoOrigem = $debito->numcgm;
        } else if ($selectFiltroBusca == 'matric') {
            $debitoOrigem = $debito->origem_aux;
        } else if ($selectFiltroBusca == 'inscr') {
            $debitoOrigem = $debito->inscr;
        } else if ($selectFiltroBusca == 'numpre') {
            $debitoOrigem = $debito->numpre;
        }

        array_push($arrayTotalSituacaoDebito, $debito->total);
        array_push($arrayDescricaoSituacaoDebito, $debito->situacao);

        array_push($arrayTotalGeral, [$debito->situacao, $debito->total]);
    }
}
// Prepara variável para totalizadores
$totalSituacaoDebito = 0;

// Percorre array com situações de débitos
foreach (array_unique($arrayDescricaoSituacaoDebito) as $descricao) {
    // Caso sejam todas as situações
    if ($selectSituacaoDebito == "Todas") {
        // Percorre todos os débitos e atribui a um totalizador
        foreach ($arrayTotalGeral as $valorTotal) {
            if ($valorTotal[0] == $descricao) {
                $totalSituacaoDebito = $totalSituacaoDebito + $valorTotal[1];
            }
        }
    // Caso seja apenas uma situação
    } else {
        // Percorre todos os débitos e atribui a um totalizador
        foreach ($arrayTotalSituacaoDebito as $valorTotal) {
            $totalSituacaoDebito = $totalSituacaoDebito + $valorTotal;
        }
    }

    // Imprime totalizador no rodapé do PDF
    $descricao = strtoupper($descricao);
    $oPdf->SetFont('Arial', 'B', 8);
    $oPdf->Cell(279, 5, "TOTAL $descricao: ".db_formatar($totalSituacaoDebito, "f"), 1, 0, "C", 0);
    $oPdf->Line(10, $oPdf->getY(), 289, $oPdf->getY());
    $oPdf->Ln();
    $totalSituacaoDebito = 0;
}

$oPdf->Output('I');

function fc_dadosContribuinte($oPdf, $contribuinte)
{
    $oPdf->SetFont('Arial', 'B', 8);
    $oPdf->Cell(strlen(LABEL_CGM)+10, 5, trim(LABEL_CGM).": ", 0, 0, "L", 0);
    $oPdf->SetFont('Arial', '', 8);
    $oPdf->Cell(160, 5, "$contribuinte->numcgm - $contribuinte->nome", 0, 0, "L", 0);
    $oPdf->SetFont('Arial', 'B', 8);
    $oPdf->Cell(20, 5, "CNPJ/CPF:", 0, 0, "L", 0);
    $oPdf->SetFont('Arial', '', 8);
    $oPdf->Cell(25, 5, db_cgccpf($contribuinte->cpf), 0, 1, "L", 0);
    $oPdf->SetFont('Arial', 'B', 8);

    $oPdf->Cell(15, 5, "Endereço:", 0, 0, "L", 0);
    $oPdf->SetFont('Arial', '', 8);
    $oPdf->Cell(160, 5, "$contribuinte->endereco - N° $contribuinte->numero", 0, 0, "L", 0);
    $oPdf->SetFont('Arial', 'B', 8);
    $oPdf->Cell(20, 5, "Município:", 0, 0, "L", 0);
    $oPdf->SetFont('Arial', '', 8);
    $oPdf->Cell(84, 5, "$contribuinte->municipio - $contribuinte->uf", 0, 1, "L", 0);

    if (TIPO_BUSCA != "cgm") {
       $oPdf->SetFont('Arial', 'B', 8);
       $oPdf->Cell($oPdf->getY(), 5, "Dados Complementares: ", 0, 1, "L", 0);
    }

    switch (TIPO_BUSCA) {
        case 'matric':
            $oPdf->SetFont('Arial', 'B', 8);
            $oPdf->Cell(strlen(LABEL_MATRIC)+10, 5, trim(LABEL_MATRIC).": ", 0, 0, "L", 0);
            $oPdf->SetFont('Arial', '', 8);
            $oPdf->Cell(strlen(LABEL_MATRIC)+10, 5, "$contribuinte->matric", 0, 0, "L", 0);

            $oPdf->SetFont('Arial', 'B', 8);
            $oPdf->Cell(28, 5, "Setor/Quadra/Lote : ", 0, 0, "L", 0);
            $oPdf->SetFont('Arial', '', 8);
            $oPdf->Cell(30, 5, "$contribuinte->setor_quadra_lote", 0, 0, "L", 0);

            $oPdf->SetFont('Arial', 'B', 8);
            $oPdf->Cell(strlen(LABEL_IPTUBASE_REFANT)+8, 5, trim(LABEL_IPTUBASE_REFANT).": ", 0, 0, "L", 0);
            $oPdf->SetFont('Arial', '', 8);
            $oPdf->Cell(strlen(LABEL_IPTUBASE_REFANT)+8, 5, "$contribuinte->refant", 0, 1, "L", 0);

            $oPdf->SetFont('Arial', 'B', 8);
            $oPdf->Cell(20, 5, "Logradouro:  ", 0, 0, "L", 0);
            $oPdf->SetFont('Arial', '', 8);
            $oPdf->Cell(160, 5, "$contribuinte->ender_aux", 0, 1, "L", 0);

            $oPdf->Cell($oPdf->getY(), 5, "", 0, 0, "L", 0);
            break;
        case 'inscr':
            $oPdf->SetFont('Arial', 'B', 8);
            $oPdf->Cell(strlen(LABEL_INSCR)+10, 5, trim(LABEL_INSCR).": ", 0, 0, "L", 0);
            $oPdf->SetFont('Arial', '', 8);
            $oPdf->Cell(strlen(LABEL_INSCR)+10, 5, "$contribuinte->inscr", 0, 0, "L", 0);
            $oPdf->SetFont('Arial', 'B', 8);
            $oPdf->Cell(strlen(LABEL_ISSBASE_REFANT)+8, 5, trim(LABEL_ISSBASE_REFANT).": ", 0, 0, "L", 0);
            $oPdf->SetFont('Arial', '', 8);
            $oPdf->Cell(strlen(LABEL_ISSBASE_REFANT)+8, 5, "$contribuinte->refant", 0, 1, "L", 0);
            $oPdf->Cell($oPdf->getY(), 5, "", 0, 0, "L", 0);
            break;
            
        //cgm
        default: 
  
            break;
    }

    $oPdf->Line(10, $oPdf->getY(), 289, $oPdf->getY());
    $oPdf->Ln();
}

function fc_cabecalhoDebitos($oPdf)
{
    $oPdf->SetFont('Arial', 'B', 6);
    $oPdf->Cell(13, 5, "NUMPRE", 1, 0, "C", 0);
    $oPdf->Cell(5, 5, "P", 1, 0, "C", 0);
    $oPdf->Cell(5, 5, "T", 1, 0, "C", 0);
    $oPdf->Cell(6, 5, "ANO", 1, 0, "C", 0);
    $oPdf->Cell(13, 5, "ORIGEM", 1, 0, "C", 0);
    $oPdf->Cell(34, 5, "TIPO DE DÉBITO", 1, 0, "C", 0);
    $oPdf->Cell(13, 5, "VENC.", 1, 0, "C", 0);
    $oPdf->Cell(20, 5, "VALOR", 1, 0, "C", 0);
    $oPdf->Cell(20, 5, "CORRIGIDO", 1, 0, "C", 0);
    $oPdf->Cell(20, 5, "JUROS", 1, 0, "C", 0);
    $oPdf->Cell(20, 5, "MULTA", 1, 0, "C", 0);
    $oPdf->Cell(20, 5, "DESCONTO", 1, 0, "C", 0);
    $oPdf->Cell(20, 5, "TOTAL", 1, 0, "C", 0);
    $oPdf->Cell(30, 5, "SITUAÇÃO", 1, 0, "C", 0);
    $oPdf->Cell(25, 5, "PROCESSO", 1, 0, "C", 0);
    $oPdf->Cell(15, 5, "MOVIMENTO", 1, 1, "C", 0);
    $oPdf->SetFont('Arial', '', 6);
}

function fc_imprimedadoscontribuinte($oPdf, $debito, $selectFiltroBusca) {
    $debitoOrigem = '';
    if ($selectFiltroBusca == 'cgm') {
        $debitoOrigem = "C-".$debito->numcgm;
        if (!empty($debito->origem_aux)) {
           $debitoOrigem = $debito->origem_aux;
        }
    } else if ($selectFiltroBusca == 'matric') {
        //$debitoOrigem = "M-".$debito->matric;
        $debitoOrigem = $debito->origem_aux;
    } else if ($selectFiltroBusca == 'inscr') {
        //$debitoOrigem = "I-".$debito->inscr;
        $debitoOrigem = $debito->origem_aux;
    }
    $oPdf->Cell(13, 5, $debito->numpre, 1, 0, "C", 0);
    $oPdf->Cell(5, 5,  $debito->numpar, 1, 0, "C", 0);
    $oPdf->Cell(5, 5,  $debito->numtot, 1, 0, "C", 0);
    $oPdf->Cell(6, 5, date('Y', strtotime($debito->data_operacao)), 1, 0, "C", 0);
    $oPdf->Cell(13, 5, $debitoOrigem, 1, 0, "C", 0);
    $oPdf->Cell(34, 5, substr($debito->descricao,0,28), 1, 0, "C", 0);
    $oPdf->Cell(13, 5, date('d/m/Y', strtotime($debito->data_vencimento)), 1, 0, "C", 0);
    $oPdf->Cell(20, 5, db_formatar($debito->valor, "f"), 1, 0, "C", 0);
    $oPdf->Cell(20, 5, db_formatar($debito->corrigido, "f"), 1, 0, "C", 0);
    $oPdf->Cell(20, 5, db_formatar($debito->juros, "f"), 1, 0, "C", 0);
    $oPdf->Cell(20, 5, db_formatar($debito->multa, "f"), 1, 0, "C", 0);
    $oPdf->Cell(20, 5, db_formatar($debito->desconto, "f"), 1, 0, "C", 0);
    $oPdf->Cell(20, 5, db_formatar($debito->total,"f"), 1, 0, "C", 0);
    $oPdf->Cell(30, 5, strtoupper($debito->situacao), 1, 0, "C", 0);
    $oPdf->Cell(25, 5, $debito->processo, 1, 0, "C", 0);
    $oPdf->Cell(15, 5, empty($debito->data_movimento) ? "-" : date('d/m/Y', strtotime($debito->data_movimento)), 1, 1, "C", 0);
    $oPdf->SetFont('Arial', '', 6);
}

function fc_quebraPagina($oPdf, $contribuinte)
{
    if ($oPdf->GetY() > ($oPdf->getH() - 30)) {
        $oPdf->AddPage();
        fc_dadosContribuinte($oPdf, $contribuinte);
        fc_cabecalhoDebitos($oPdf);
    }
}

?>
