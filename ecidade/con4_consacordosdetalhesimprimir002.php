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

require_once(modification("fpdf151/scpdf.php"));
require_once modification('fpdf151/pdf.php');
require_once(modification("libs/db_utils.php"));
require_once(modification("model/Acordo.model.php"));
require_once modification("fpdf151/PDFDocument.php");

$oGet = db_utils::postMemory($_GET);
$clAcordo = new Acordo($oGet->ac16_sequencial);
$codigoAcordo = $clAcordo->getCodigoAcordo();
$iDepartamento = $clAcordo->getDepartamento();
$iDepartamentoResponsavel = $clAcordo->getDepartamentoResponsavel();
$iSituacao = $clAcordo->getSituacao();

$sqlInfos = "
    SELECT DISTINCT acordo.ac16_sequencial,
                    ac16_numeroacordo,
                    ac16_anousu,
                    ac16_numeroprocesso,
                    (TO_CHAR(ac16_datainicio, 'dd/mm/yyyy') || ' a ' || TO_CHAR(ac16_datafim, 'dd/mm/yyyy')) AS vigencia,
                    --ac16_datafim - (ac16_datainicio - INTERVAL '1 DAY')::date AS periodo_de_vigencia_em_dias,
                    ac16_qtdperiodo,
                    ac17_descricao AS situacao,
                    acordo.ac16_numero,
                    TO_CHAR(ac16_dataassinatura, 'dd/mm/yyyy') AS ac16_dataassinatura,
                    (ac16_contratado || ' - ' || cgm.z01_nome)::varchar AS contratado,
                    acordo.ac16_objeto::text AS objeto,
                    acordo.ac16_resumoobjeto AS resumo_objeto,
                    ac28_descricao AS origem,
                    ac16_lei,
                    (ac16_numero || '/' || ac16_anousu)::varchar AS ac16_contrato_ano,
                    (ac16_acordogrupo || ' - ' || acordogrupo.ac02_descricao)::varchar AS grupo,
                    (ac16_acordocategoria || ' - ' || acordocategoria.ac50_descricao):: varchar AS categoria,
                    (ac16_acordocomissao || ' - ' || acordocomissao.ac08_descricao):: varchar AS comissao,
                    acordoclassificacao.ac46_descricao AS classificacao,
                    acordotipo.ac04_descricao AS tipo_acordo
    FROM acordo
    INNER JOIN cgm ON cgm.z01_numcgm = acordo.ac16_contratado
    INNER JOIN acordogrupo ON acordogrupo.ac02_sequencial = acordo.ac16_acordogrupo
    INNER JOIN acordosituacao ON acordosituacao.ac17_sequencial = acordo.ac16_acordosituacao
    INNER JOIN acordocomissao ON acordocomissao.ac08_sequencial = acordo.ac16_acordocomissao
    INNER JOIN acordonatureza ON acordonatureza.ac01_sequencial = acordogrupo.ac02_acordonatureza
    INNER JOIN acordotipo ON acordotipo.ac04_sequencial = acordogrupo.ac02_acordotipo
    INNER JOIN acordoposicao ON acordoposicao.ac26_acordo = acordo.ac16_sequencial
    LEFT  JOIN acordoitem ON acordoitem.ac20_acordoposicao = acordoposicao.ac26_sequencial
    LEFT  JOIN acordoorigem ON acordoorigem.ac28_sequencial = acordo.ac16_origem
    INNER JOIN acordocategoria ON acordocategoria.ac50_sequencial = acordo.ac16_acordocategoria
    INNER JOIN acordoclassificacao ON acordoclassificacao.ac46_sequencial = acordo.ac16_acordoclassificacao
    WHERE ac16_sequencial = ".$codigoAcordo."
        AND ac16_instit = ".db_getsession('DB_instit')."
        AND ac16_coddepto = ".$iDepartamento."
        AND ac16_acordosituacao in (".$iSituacao.")
    ORDER BY ac16_sequencial
";
$rsSqlInfos = db_query($sqlInfos);
db_fieldsmemory($rsSqlInfos, 0);

$textContentType = 'ISO-8859-1';

$fpdf = new PDFDocument(PDFDocument::PRINT_LANDSCAPE);

$fpdf->Open();
$fpdf->AliasNbPages();

$fpdf->setfillcolor(235);

$fpdf->SetAutoPageBreak(true, 12);

$head = "EXTRATO DE ACORDO";
$fpdf->addHeaderDescription($head);

$head2 = "";
$fpdf->addHeaderDescription($head2);

$head3 = "Acordo: $ac16_sequencial - $resumo_objeto";
$fpdf->addHeaderDescription($head3);

$retrato = $oGet->retrato;

//SOMA DAS LARGURAS DEVE SER IGUAL A 190 (P) -----
//SOMA DAS LARGURAS DEVE SER IGUAL A 276 (L) -----
if (isset($retrato) && $retrato) {
    $fpdf->Addpage('P');
} else {
    $fpdf->Addpage('L');
}

$widthCol1Title = 35;
$widthCol2Title = 40;
$widthCol1Description = $retrato ? 70 : 130;
$widthCol2Description = $retrato ? 45 : 71;

if (isset($oGet->dadosAcordo) && $oGet->dadosAcordo) {
    //LINHA 01 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y1 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, 'Acordo:');

    $fpdf->setXY($widthCol1Title + 10, $y1);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $clAcordo->getCodigoAcordo());

    //LINHA 02 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y2 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, 'Tipo do Acordo:');

    $fpdf->setXY($widthCol1Title + 10, $y2);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $tipo_acordo);

    //LINHA 03 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y3 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, 'Processo:');

    $fpdf->setXY($widthCol1Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $ac16_numeroprocesso);

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y3);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 5, 'Tipo de Instrumento:');

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $tipo_acordo);

    //LINHA 04 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y3 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, mb_convert_encoding('Vigência:', $textContentType));

    $fpdf->setXY($widthCol1Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $vigencia);

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y3);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 5, 'Data da Assinatura:');

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $ac16_dataassinatura);

    //LINHA 05 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y3 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, mb_convert_encoding('Situação Atual:', $textContentType));

    $fpdf->setXY($widthCol1Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $situacao);

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y3);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 5, mb_convert_encoding('Classificação:', $textContentType));

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $classificacao);

    //LINHA 06 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y3 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, 'Contratado:');

    $fpdf->setXY($widthCol1Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $contratado, 0, 'L');

    $y44 = $fpdf->getY();

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y3);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 5, 'Grupo de Acordo:');

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y3);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $grupo);

    //LINHA 07 --------
    $fpdf->setFont('Arial', 'B', 8);

    $y4 = $fpdf->getY();
    $yMaior = ($y4 > $y44) ? $y4 : $y44;

    $fpdf->setXY(10, $yMaior);
    $fpdf->multiCell($widthCol1Title, 5, mb_convert_encoding('Período de Vigência:', $textContentType));

    $fpdf->setXY($widthCol1Title + 10, $yMaior);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $vigencia);

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $yMaior);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 5, mb_convert_encoding('Número Contrato/Ano:', $textContentType));

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $yMaior);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $ac16_contrato_ano);

    //LINHA 08 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y5 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, 'Categoria:');

    $fpdf->setXY($widthCol1Title + 10, $y5);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $categoria);

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y5);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 5, 'Departamento:');

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y5);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $iDepartamento.' - '.(new DBDepartamento($iDepartamento))->getNomeDepartamento(), 0, 'L');

    //LINHA 09 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y6 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, 'Origem:');

    $fpdf->setXY($widthCol1Title + 10, $y6);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol1Description, 5, $origem);

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y6);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 5, 'Lei:');

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y6);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $ac16_lei);

    //LINHA 10 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y8 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, mb_convert_encoding('Licitação:', $textContentType));

    $fpdf->setXY($widthCol1Title + 10, $y8);
    $fpdf->setFont('Arial', '', 7);

    if ($clAcordo->getLicitacoes()[0]) {
        $iLicitacaoModalidadeDescr = $clAcordo->getLicitacoes()[0]->getModalidade()->getDescricao();
        $iLicitacaoEdital = $clAcordo->getLicitacoes()[0]->getEdital();
        $iLicitacaoAno = $clAcordo->getLicitacoes()[0]->getAno();

        $fpdf->multiCell($widthCol1Description, 5, $iLicitacaoModalidadeDescr.' - '.$iLicitacaoEdital.'/'.$iLicitacaoAno);
    } else {
        $fpdf->multiCell($widthCol1Description, 5, '');
    }

    $y99 = $fpdf->getY();

    $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y8);
    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->multiCell($widthCol2Title, 4, mb_convert_encoding('Responsável:', $textContentType));

    $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y8);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell($widthCol2Description, 5, $iDepartamentoResponsavel.' - '.(new DBDepartamento($iDepartamentoResponsavel))->getNomeDepartamento(), 0, 'L');

    if (isset($oGet->comissoes) && $oGet->comissoes) {
        //LINHA 11 --------
        $fpdf->setFont('Arial', 'B', 8);
        $y9 = $fpdf->getY();
        $y9Maior = ($y9 > $y99) ? $y9 : $y99;

        $fpdf->setXY(10, $y9Maior);
        $fpdf->multiCell($widthCol1Title, 5, mb_convert_encoding('Comissão:', $textContentType));

        $fpdf->setXY($widthCol1Title + 10, $y9Maior);
        $fpdf->setFont('Arial', '', 7);
        $fpdf->multiCell($widthCol1Description, 5, $comissao);

        $fpdf->setXY($widthCol1Title + $widthCol1Description + 10, $y9Maior);
        $fpdf->setFont('Arial', 'B', 8);
        $fpdf->multiCell($widthCol2Title, 5, 'Membros:');

        $acordoComissao = $clAcordo->getComissao();

        $membros = array();
        foreach ($acordoComissao->getMembros() as $oMembro) {
            $cgm = $oMembro->getCodigoCgm();
            $nome = mb_convert_encoding($oMembro->getNome(), $textContentType);
            $responsabilidade = mb_convert_encoding($oMembro->getDescricaoResponsabilidade(), $textContentType);

            $membro = $cgm.' - '.$nome.' - '.$responsabilidade;
            array_push($membros, $membro);
        }

        $fpdf->setXY($widthCol1Title + $widthCol1Description + $widthCol2Title + 10, $y9);
        $fpdf->setFont('Arial', '', 7);
        $fpdf->multiCell($widthCol2Description, 4, join(', ', $membros), 0, 'L');
    }

    //LINHA 12 --------
    $fpdf->setFont('Arial', 'B', 8);
    $y10 = $fpdf->getY();
    $fpdf->multiCell($widthCol1Title, 5, 'Objeto:');

    $lineSpacing = 1;

    $y10Maior = ($y99 > $y10) ? $y99 : $y10;

    $fpdf->setXY($widthCol1Title + 10, $y10Maior);
    $fpdf->setFont('Arial', '', 7);
    $fpdf->multiCell(($retrato ? 155 : 243), 4, str_replace("\n", ' ', $objeto), 0, 'L');

    $fpdf->ln(1);

    $fpdf->line(10, $fpdf->getY(), ($retrato ? 200 : 286), $fpdf->getY());

    $fpdf->ln(1);
}

if (isset($oGet->movimentacoes) && $oGet->movimentacoes) {
    //TABELA MOVIMENTAÇÕES ----------
    $sqlMovimentacoes = "
    SELECT DISTINCT acordomovimentacao.ac10_hora,
                    TO_CHAR(acordomovimentacao.ac10_datamovimento, 'dd/mm/yyyy') AS ac10_datamovimento,
                    acordomovimentacao.ac10_obs,
                    acordomovimentacaotipo.ac09_descricao
    FROM acordo
    INNER JOIN acordomovimentacao ON acordomovimentacao.ac10_acordo = acordo.ac16_sequencial
    INNER JOIN acordomovimentacaotipo ON acordomovimentacaotipo.ac09_sequencial = acordomovimentacao.ac10_acordomovimentacaotipo
    WHERE ac16_sequencial = ".$codigoAcordo."
        AND ac16_instit = ".db_getsession('DB_instit')."
        AND ac16_coddepto = ".$iDepartamento."
        AND ac16_acordosituacao in (".$iSituacao.")
        AND ac16_instit = ".db_getsession('DB_instit')."
    ORDER BY ac10_datamovimento, acordomovimentacao.ac10_hora
    ";
    $rsMovimentacoes = db_query($sqlMovimentacoes);

    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->cell(30, 7, mb_convert_encoding('Movimentações:', $textContentType), 0, 1);

    $fpdf->setFillColor(220, 220, 220);

    $movimentacaoWidth1 = $retrato ? 40 : 51;
    $movimentacaoWidth2 = $retrato ? 100 : 150;
    $movimentacaoWidth3 = $retrato ? 50 : 75;
    $movimentacaoWidth4 = $retrato ? 190 : 276;

    $fpdf->cell($movimentacaoWidth1, 4, 'Hora', 1, 0, 'L', true);
    $fpdf->cell($movimentacaoWidth2, 4, mb_convert_encoding('Descrição', $textContentType), 1, 0, 'C', true);
    $fpdf->cell($movimentacaoWidth3, 4, mb_convert_encoding('Data Movimentação', $textContentType), 1, 1, 'C', true);
    $fpdf->cell($movimentacaoWidth4, 4, mb_convert_encoding('Observação', $textContentType), 1, 1, 'L', true);

    for ($k = 0; $k < pg_num_rows($rsMovimentacoes); $k++) {
        db_fieldsmemory($rsMovimentacoes, $k);
        $fpdf->setFont('Arial', '', 8);
        $fpdf->cell($movimentacaoWidth1, 4, $ac10_hora, 1, 0, 'R');
        $fpdf->cell($movimentacaoWidth2, 4, mb_convert_encoding($ac09_descricao, $textContentType), 1, 0, 'L');
        $fpdf->cell($movimentacaoWidth3, 4, $ac10_datamovimento, 1, 1, 'C');
        $fpdf->MultiCell($movimentacaoWidth4, 4, mb_convert_encoding($ac10_obs, $textContentType), 1, 1, 'L');
    }

    $fpdf->ln(2);
}

if (isset($oGet->posicoes) && $oGet->posicoes) {
    //TABELA POSIÇÕES ----------
    $sqlPosicoes = "
    SELECT DISTINCT acordoposicao.ac26_sequencial,
                    TO_CHAR(acordoposicao.ac26_data, 'dd/mm/yyyy') AS ac26_data,
                    acordoposicao.ac26_emergencial,
                    acordoposicao.ac26_numeroaditamento,
                    acordoposicaotipo.ac27_descricao,
                    (TO_CHAR(acordovigencia.ac18_datainicio, 'dd/mm/yyyy') || ' até ' || TO_CHAR(acordovigencia.ac18_datafim, 'dd/mm/yyyy'))::varchar AS vigencia,
                    SUM(ac20_valortotal) AS total_acordo_posicao
    FROM acordo
    INNER JOIN acordoposicao ON acordoposicao.ac26_acordo = acordo.ac16_sequencial
    INNER JOIN acordoposicaotipo ON acordoposicaotipo.ac27_sequencial = acordoposicao.ac26_acordoposicaotipo
    INNER JOIN acordovigencia ON acordovigencia.ac18_acordoposicao = acordoposicao.ac26_sequencial
    INNER JOIN acordoitem ON acordoitem.ac20_acordoposicao = acordoposicao.ac26_sequencial
    WHERE ac16_sequencial = ".$codigoAcordo."
        AND ac16_instit = ".db_getsession('DB_instit')."
        AND ac16_coddepto = ".$iDepartamento."
        AND ac16_acordosituacao in (".$iSituacao.")
        AND ac16_instit = ".db_getsession('DB_instit')."
    GROUP BY acordoposicao.ac26_sequencial,
            acordoposicaotipo.ac27_descricao,
            acordovigencia.ac18_datainicio,
            acordovigencia.ac18_datafim
    ORDER BY acordoposicao.ac26_sequencial
    ";
    $rsPosicoes = db_query($sqlPosicoes);

    $posicoesWidth1 = $retrato ? 20 : 30;
    $posicoesWidth2 = $retrato ? 40 : 60;
    $posicoesWidth3 = $retrato ? 20 : 28;
    $posicoesWidth4 = $retrato ? 35 : 50;
    $posicoesWidth5 = $retrato ? 20 : 30;
    $posicoesWidth6 = $retrato ? 25 : 34;
    $posicoesWidth7 = $retrato ? 30 : 44;

    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->cell(30, 5, mb_convert_encoding('Posições:', $textContentType), 0, 1);
    $fpdf->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C']);
    $fpdf->SetWidths([
        $posicoesWidth1,
        $posicoesWidth2,
        $posicoesWidth3,
        $posicoesWidth4,
        $posicoesWidth5,
        $posicoesWidth6,
        $posicoesWidth7
    ]);
    $fpdf->Row([
        mb_convert_encoding('Código', $textContentType),
        mb_convert_encoding('Vigência', $textContentType),
        mb_convert_encoding('Número', $textContentType),
        mb_convert_encoding('Situação', $textContentType),
        'Data',
        'Valor',
        'Emergencial'
    ], 5, true, 5, 1);

    for ($k = 0; $k < pg_num_rows($rsPosicoes); $k++) {
        db_fieldsmemory($rsPosicoes, $k);
        $fpdf->setFont('Arial', '', 8);

        $fpdf->SetAligns(array('C', 'C', 'C', 'L', 'C', 'C', 'C'));
        $fpdf->SetWidths(array(
            $posicoesWidth1,
            $posicoesWidth2,
            $posicoesWidth3,
            $posicoesWidth4,
            $posicoesWidth5,
            $posicoesWidth6,
            $posicoesWidth7
        ));
        $fpdf->Row(array(
            $ac26_sequencial,
            mb_convert_encoding($vigencia, $textContentType),
            $ac26_numeroaditamento,
            mb_convert_encoding($ac27_descricao, $textContentType),
            $ac26_data,
            'R$'.db_formatar($total_acordo_posicao, 'f'),
            $ac26_emergencial == 't' ? 'Sim' : 'Não'
        ));

        $somaTotais += $total_acordo_posicao;
    }

    $fpdf->ln(1);

    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->cell(($retrato ? 190 : 276), 5, 'Valor do Total do Acordo: R$'.db_formatar($somaTotais, 'f'), 0, 1, 'R');

    $fpdf->ln(2);
}

if (isset($oGet->empenhamentos) && $oGet->empenhamentos) {
    //TABELA EMPENHOS VINCULADOS ----------
    $sqlEmpenhosVinculados = "
        SELECT DISTINCT empautoriza.e54_autori,
                        empautoriza.e54_valor,
                        TO_CHAR(empautoriza.e54_emiss, 'dd/mm/yyyy') AS e54_emiss,
                        (empempenho.e60_codemp || '/' || empempenho.e60_anousu) AS empenho
        FROM acordo
        INNER JOIN acordoempautoriza ON acordoempautoriza.ac45_acordo = acordo.ac16_sequencial
        INNER JOIN empautoriza ON empautoriza.e54_autori = acordoempautoriza.ac45_empautoriza and empautoriza.e54_anulad is null
        INNER JOIN empempaut ON empempaut.e61_autori = empautoriza.e54_autori
        INNER JOIN empempenho ON empempenho.e60_numemp = empempaut.e61_numemp
        WHERE ac16_sequencial = ".$codigoAcordo."
            AND ac16_instit = ".db_getsession('DB_instit')."
            AND ac16_coddepto = ".$iDepartamento."
            AND ac16_acordosituacao in (".$iSituacao.")
            AND ac16_instit = ".db_getsession('DB_instit')."
        ORDER BY empautoriza.e54_autori
    ";
    $rsEmpenhosVinculados = db_query($sqlEmpenhosVinculados);

    $empenhosWidth1 = $retrato ? 48 : 69;
    $empenhosWidth2 = $retrato ? 47 : 69;
    $empenhosWidth3 = $retrato ? 48 : 69;
    $empenhosWidth4 = $retrato ? 47 : 69;

    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->cell(30, 5, 'Empenhos Vinculados:', 0, 1);
    $fpdf->cell($empenhosWidth1, 4, mb_convert_encoding('Código da Autorização', $textContentType), 1, 0, 'C', true);
    $fpdf->cell($empenhosWidth2, 4, 'Valor', 1, 0, 'C', true);
    $fpdf->cell($empenhosWidth3, 4, mb_convert_encoding('Data Emissão', $textContentType), 1, 0, 'C', true);
    $fpdf->cell($empenhosWidth4, 4, 'Empenho', 1, 1, 'C', true);

    for ($k = 0; $k < pg_num_rows($rsEmpenhosVinculados); $k++) {
        db_fieldsmemory($rsEmpenhosVinculados, $k);
        $fpdf->setFont('Arial', '', 8);
        $fpdf->cell($empenhosWidth1, 4, $e54_autori, 1, 0, 'C');
        $fpdf->cell($empenhosWidth2, 4, 'R$'.db_formatar($e54_valor, 'f'), 1, 0, 'C');
        $fpdf->cell($empenhosWidth3, 4, $e54_emiss, 1, 0, 'C');
        $fpdf->cell($empenhosWidth4, 4, $empenho, 1, 1, 'C');

        $totalEmpenhosVinculados += $e54_valor;
    }

    $fpdf->ln(1);

    $fpdf->setFont('Arial', 'B', 8);
    $fpdf->cell(($retrato ? 190 : 276), 5, 'Valor Total Empenhado: R$'.db_formatar($totalEmpenhosVinculados, 'f'), 0, 0, 'R');   
}

$fpdf->Output();
?>