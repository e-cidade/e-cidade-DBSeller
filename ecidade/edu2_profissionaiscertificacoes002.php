<?php

/*
 *  E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

define('MODULO_SECRETARIA', 7159);

$nomeEscola = db_getsession("DB_nomedepto");
$escolaSessao = db_getsession("DB_coddepto");
$modulo = db_getsession("DB_modulo");

db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING']);
$clatividaderh = new cl_atividaderh();

$dadosParaImprimir = 0;

try {
    if (!isset($_GET['filtros'])) {
        throw new Exception('Não foi informado os filtros para emissão do relatório.');
    }

    $filtros = JSON::create()->parse(base64_decode($_GET['filtros']));
    
    $escolasLista = [];
    $escolasLista = array_map(function ($codigo) {
        return $codigo;
    }, $filtros->escolas);

    $whereEscola = "";
    if (db_getsession("DB_modulo") != MODULO_SECRETARIA) {
        $whereEscola = " WHERE ed18_i_codigo = " . db_getsession("DB_coddepto");
    } else {
        if (count($escolasLista) > 0) {
            $whereEscola = " WHERE ed18_i_codigo in (".implode(",", $escolasLista).")";
        }
    }

    $whereFuncao = "";
    //
    $head1 = "Relatório de Profissionais com Certificações Anexadas";
    $head2 = "";
    $head3 = "";

    if (count($filtros->funcao) > 0) {
        $campos = "ed01_c_descr";
        $whereFuncao = "ed01_i_codigo IN (".implode(",", $filtros->funcao).")";
        $sqlFuncao = $clatividaderh->sql_query($ed01_i_codigo, $campos, "ed01_c_descr", $whereFuncao);
        $resultFuncoes = db_query($sqlFuncao);
        db_fieldsmemory($resultFuncoes, 0);

        $head2 = count($filtros->funcao) == 1 ? "\nFunção: ".$ed01_c_descr : "\nFunção: Diversas";
    }

    if ($filtros->ano > 0) {
        $head3 = "\nAno: ".$filtros->ano;
        $whereAno = "and ed52_i_ano = $filtros->ano";
    }

    $pdf = new Pdf();
    $pdf->init(false);
    $pdf->exibeHeader(true, \Fpdf\Pdf::HEADER_DEFAULT);
    $pdf->setExibeBrasao(true);

    $pdf->addTitulo($head1, 1);
    $pdf->addTitulo($head2, 2);
    $pdf->addTitulo($head3, 3);

    $pdf->AliasNbPages();
    $pdf->setfillcolor(235);
    $pdf->AddPage();
    $pdf->setfont('arial', '', 8);

    $larguraUtil = ($pdf->getW() - $pdf->getRightMargin() - $pdf->getLeftMargin());

    $sqlEscolas = "
    SELECT ed18_i_codigo as codigo_escola, ed18_c_nome as nome_escola 
    FROM escola 
    $whereEscola 
    ORDER BY ed18_c_nome
    ";

    $rsEscolas = db_query($sqlEscolas);
    for ($i = 0; $i < pg_num_rows($rsEscolas); $i++) {
        $dadosEscola = db_utils::fieldsMemory($rsEscolas, $i);

        $sqlProfissionais = "
        WITH recursos_humanos_por_escola AS (
            SELECT 
                ed20_i_codigo as codigo,
                CASE
                    WHEN ed20_i_tiposervidor = 1 THEN rh01_numcgm
                    ELSE ed285_i_cgm
                END as cgm_cod
            FROM rechumano 
            LEFT JOIN rechumanoescola on ed75_i_rechumano = ed20_i_codigo 
            LEFT JOIN rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo 
            LEFT JOIN rechumanocgm on ed285_i_rechumano = ed20_i_codigo 
            LEFT JOIN rechumanopessoal on ed284_i_rechumano = ed20_i_codigo 
            LEFT JOIN rhpessoal on ed284_i_rhpessoal = rh01_regist 
            LEFT JOIN cgm AS cgmcgm ON cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm 
            LEFT JOIN cgm AS cgmrh ON cgmrh.z01_numcgm = rhpessoal.rh01_numcgm 
            LEFT JOIN atividaderh on ed01_i_codigo = ed22_i_atividade 
            LEFT JOIN rhregime on rh30_codreg = ed20_i_rhregime 
            LEFT JOIN escola on ed18_i_codigo = ed75_i_escola 
            INNER JOIN calendarioescola on calendarioescola.ed38_i_escola = escola.ed18_i_codigo
            INNER JOIN calendario on calendario.ed52_i_codigo = calendarioescola.ed38_i_calendario
            WHERE ed75_i_escola in ({$dadosEscola->codigo_escola}) and ed75_i_saidaescola is null $whereAno
            GROUP BY codigo, cgm_cod
        )
        SELECT codigo,
        (
            SELECT
                CASE
                    WHEN ed20_i_tiposervidor = 1 THEN ed284_i_rhpessoal
                    ELSE ed285_i_cgm
                END
            FROM rechumano 
            LEFT JOIN rechumanocgm on ed285_i_rechumano = ed20_i_codigo
            LEFT JOIN rechumanopessoal on ed284_i_rechumano = ed20_i_codigo
            WHERE ed20_i_codigo = codigo LIMIT 1
        ) as matricula_cgm,
        (
            SELECT
                CASE
                    WHEN rh.ed20_i_tiposervidor = 1
                    THEN cgmrhsub.z01_nome
                    ELSE cgmcgmsub.z01_nome
                END
            FROM rechumano rh
            LEFT JOIN rechumanoescola on ed75_i_rechumano = rh.ed20_i_codigo
            LEFT JOIN rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo
            LEFT JOIN rechumanocgm on ed285_i_rechumano = rh.ed20_i_codigo
            LEFT JOIN rechumanopessoal on ed284_i_rechumano = rh.ed20_i_codigo
            LEFT JOIN rhpessoal on ed284_i_rhpessoal =  rh01_regist
            LEFT JOIN cgm as cgmcgmsub ON cgmcgmsub.z01_numcgm = rechumanocgm.ed285_i_cgm
            LEFT JOIN cgm as cgmrhsub ON cgmrhsub.z01_numcgm  = rhpessoal.rh01_numcgm
            LEFT JOIN atividaderh on ed01_i_codigo = ed22_i_atividade
            LEFT JOIN rhregime on rh30_codreg = rh.ed20_i_rhregime
            LEFT JOIN escola on ed18_i_codigo = ed75_i_escola
            INNER JOIN calendarioescola on calendarioescola.ed38_i_escola = escola.ed18_i_codigo
            INNER JOIN calendario on calendario.ed52_i_codigo = calendarioescola.ed38_i_calendario
            WHERE rh.ed20_i_codigo = codigo and ed75_i_escola in ({$dadosEscola->codigo_escola})
            and ed75_i_saidaescola is null $whereAno LIMIT 1
        ) as nome,
        COALESCE((
            SELECT ed01_c_descr
            from rechumano
            JOIN rechumanoescola on ed75_i_rechumano = ed20_i_codigo
            JOIN rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo
            JOIN atividaderh on ed01_i_codigo = ed22_i_atividade
            WHERE ed20_i_codigo = codigo and ed75_i_escola in ({$dadosEscola->codigo_escola})
                and ed75_i_saidaescola is null and ed22_datafim is null
            ORDER BY ed22_i_atividade DESC LIMIT 1),'---'
        ) as atividade,
        (
            WITH cursos_graduacao_pos as (
                SELECT 
                    'Graduação' as descricao
                FROM formacao 
                INNER JOIN censoinstsuperior on censoinstsuperior.ed257_i_codigo = formacao.ed27_i_censoinstsuperior 
                INNER JOIN rechumano on rechumano.ed20_i_codigo = formacao.ed27_i_rechumano 
                INNER JOIN cursoformacao on cursoformacao.ed94_i_codigo = formacao.ed27_i_cursoformacao 
                INNER JOIN censomunic on censomunic.ed261_i_codigo = censoinstsuperior.ed257_i_censomunic 
                LEFT JOIN rhregime on rhregime.rh30_codreg = rechumano.ed20_i_rhregime 
                INNER JOIN pais on pais.ed228_i_codigo = rechumano.ed20_i_pais 
                LEFT JOIN censouf on censouf.ed260_i_codigo = rechumano.ed20_i_censoufcert 
                    and censouf.ed260_i_codigo = rechumano.ed20_i_censoufender 
                    and censouf.ed260_i_codigo = rechumano.ed20_i_censoufnat 
                    and censouf.ed260_i_codigo = rechumano.ed20_i_censoufident 
                LEFT JOIN censomunic as a on a.ed261_i_codigo = rechumano.ed20_i_censomunicender 
                    and a.ed261_i_codigo = rechumano.ed20_i_censomunicnat 
                LEFT JOIN censoorgemissrg on censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss 
                LEFT JOIN rechumano as b on b.ed20_i_codigo = rechumano.ed20_i_censocartorio 
                WHERE ed27_i_rechumano = codigo and ed27_c_situacao = 'CON' and ed27_i_docformacao_estorage is not null
                UNION
                SELECT 'Pós-graduação' as descricao
                FROM rhformacaosuperior 
                WHERE ed183_cgm = cgm_cod and ed183_docpos_estorage is not null
            ) 
            SELECT string_agg(distinct descricao, ' / ') FROM cursos_graduacao_pos
        ) as documentos_anexados
        FROM recursos_humanos_por_escola
        WHERE 
        (
            EXISTS 
            (
                SELECT 1 
                FROM rhformacaosuperior
                WHERE ed183_cgm = cgm_cod and ed183_docpos_estorage is not null
            ) 
            OR EXISTS
            (
                SELECT 1
                FROM formacao 
                INNER JOIN censoinstsuperior on censoinstsuperior.ed257_i_codigo = formacao.ed27_i_censoinstsuperior 
                INNER JOIN rechumano on rechumano.ed20_i_codigo = formacao.ed27_i_rechumano 
                INNER JOIN cursoformacao on cursoformacao.ed94_i_codigo = formacao.ed27_i_cursoformacao 
                INNER JOIN censomunic on censomunic.ed261_i_codigo = censoinstsuperior.ed257_i_censomunic 
                LEFT JOIN rhregime on rhregime.rh30_codreg = rechumano.ed20_i_rhregime 
                INNER JOIN pais on pais.ed228_i_codigo = rechumano.ed20_i_pais 
                LEFT JOIN censouf on censouf.ed260_i_codigo = rechumano.ed20_i_censoufcert 
                    and censouf.ed260_i_codigo = rechumano.ed20_i_censoufender 
                    and censouf.ed260_i_codigo = rechumano.ed20_i_censoufnat 
                    and censouf.ed260_i_codigo = rechumano.ed20_i_censoufident 
                LEFT JOIN censomunic as a on a.ed261_i_codigo = rechumano.ed20_i_censomunicender 
                    and a.ed261_i_codigo = rechumano.ed20_i_censomunicnat 
                LEFT JOIN censoorgemissrg on censoorgemissrg.ed132_i_codigo = rechumano.ed20_i_censoorgemiss 
                LEFT JOIN rechumano as b on b.ed20_i_codigo = rechumano.ed20_i_censocartorio 
                WHERE ed27_i_rechumano = codigo and ed27_c_situacao = 'CON' and ed27_i_docformacao_estorage is not null
            )
        )
        ";
        
        if (count($filtros->funcao) > 0) {
            $sqlProfissionais .= "
            AND 
            (
                SELECT ed01_i_codigo
                FROM rechumano
                JOIN rechumanoescola on ed75_i_rechumano = ed20_i_codigo
                JOIN rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo
                JOIN atividaderh on ed01_i_codigo = ed22_i_atividade 
                WHERE rechumano.ed20_i_codigo = codigo and ed75_i_escola in ({$dadosEscola->codigo_escola}) 
                    and ed75_i_saidaescola is null and ed22_datafim is null
                ORDER BY ed01_i_codigo DESC LIMIT 1
            ) IN (".implode(',', $filtros->funcao).")
            ";
        }

        $sqlProfissionais .= "
        ORDER BY nome
        ";
        
        $rsProfissionais = db_query($sqlProfissionais);
        $numRowsProfissionais = pg_num_rows($rsProfissionais);

        if ($numRowsProfissionais > 0) {
            imprimeDadosEscola($pdf, $larguraUtil, $dadosEscola);
            imprimeHeaderRelatorio($pdf);
        }
        
        for ($j = 0; $j < $numRowsProfissionais; $j++) {
            $dadosParaImprimir++;
            $dadosProfissional = db_utils::fieldsMemory($rsProfissionais, $j);

            $backGround = $j % 2 == 0 ? 255: 240;
            $pdf->setfillcolor($backGround);

            imprimeDadosProfissional($pdf, $dadosProfissional);
        }

        if ($numRowsProfissionais > 0) {
            imprimeQuantidadeProfissionais($pdf, $larguraUtil, $numRowsProfissionais);
        }
    }

    if ($dadosParaImprimir == 0) {
        db_redireciona('db_erros.php?fechar=true&db_erro=Não existem registros cadastrados.');
        exit();
    }

    $pdf->output();
} catch (Exception $e) {
    $sMsg = urlencode($e->getMessage());
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $sMsg);
}

/// Funções auxiliares do relatório ///

function ajustaQuebraPagina($pdf)
{
    if ($pdf->getY() > $pdf->getH() - 35) {
        $pdf->AddPage();
    }
}

function imprimeHeaderRelatorio($pdf)
{
    $pdf->setfont('arial', '', 8);

    $pdf->setfillcolor(210);
    $pdf->cell(25, 5, 'Matrícula/CGM', 1, 0, "C", 1);
    $pdf->cell(75, 5, 'Nome', 1, 0, "C", 1);
    $pdf->cell(50, 5, 'Função', 1, 0, "C", 1);
    $pdf->cell(40, 5, 'Documento Anexado', 1, 0, "C", 1);
    $pdf->ln();
}

function imprimeDadosEscola($pdf, $largura, $dados)
{
    $pdf->setfont('arial', 'b', 7);
    $pdf->Cell($largura, 5, $dados->codigo_escola." - ".$dados->nome_escola, 0, 1, "L", 0);
}

function imprimeDadosProfissional($pdf, $dados)
{
    $pdf->setfont('arial', '', 7);

    $pdf->cell(25, 4, $dados->matricula_cgm, 0, 0, "C", 1);
    $pdf->Cell(75, 4, $dados->nome, 0, 0, "L", 1);
    $pdf->Cell(50, 4, $dados->atividade, 0, 0, "L", 1);
    $pdf->Cell(40, 4, substr($dados->documentos_anexados, 0, 30), 0, 0, "L", 1);
    $pdf->ln();

    ajustaQuebraPagina($pdf);
}

function imprimeQuantidadeProfissionais($pdf, $largura, $quantidade)
{
    $pdf->setfont('arial', 'b', 7);
    $pdf->Cell(20, 4, "Qtde: ".$quantidade, 0, 0, "R", 0);
    $pdf->Cell($largura - 20, 4, "", 0, 0, "L", 0);
    $pdf->ln();
    $pdf->ln();
}
