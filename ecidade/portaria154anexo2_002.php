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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("fpdf151/PDFDocument.php"));

try {
    $oParam = JSON::create()->parse(str_replace("\\", "", $_GET["json"]));

    if (empty($oParam->matricula)) {
        throw new BusinessException("Matrícula não informada!");
    }

    $oServidor = \ServidorRepository::getServidoresByMatriculas(\DBPessoal::getAnoFolha(), \DBPessoal::getMesFolha(), array($oParam->matricula));
    $oServidor = $oServidor[$oParam->matricula];
    $oCgm = $oServidor->getCgm();
    $oPrefeitura = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"));
    $oCgmPrefeitura = $oPrefeitura->getCgm();

    $sql = <<<SQL
            select
                rh217_mesusu as mes,
                rh217_anousu as exercicio,
                rh217_informacao as valor,
                'slag' as addres,
                1 as editado
            from
                servidorrelatorioarquivogenerico
            where
                rh217_regist = {$oParam->matricula}
                and rh217_arquivorelatorio = 'portaria154AnexoII'
            union all
            select
                r14_mesusu as mes,
                r14_anousu as exercicio,
                round(r14_valor, 2)::varchar as valor,
                'gerfsal' as addres,
                coalesce(
                    (
                        select 
                            1 
                        from 
                            servidorrelatorioarquivogenerico 
                        where 
                            rh217_regist = r14_regist
                            and rh217_arquivorelatorio = 'portaria154AnexoII'
                            and rh217_mesusu = r14_mesusu
                            and rh217_anousu = r14_anousu limit 1
                    ),
                0) as editado
            from
                gerfsal
            where
                r14_rubric = 'R992'
                and r14_regist = {$oParam->matricula}
                union all
            select
                r48_mesusu as mes,
                r48_anousu as exercicio,
                round(r48_valor, 2)::varchar as valor,
                'gerfcom' as addres,
                coalesce(
                    (
                        select 
                            1 
                        from 
                            servidorrelatorioarquivogenerico 
                        where 
                            rh217_regist = r48_regist
                            and rh217_arquivorelatorio = 'portaria154AnexoII'
                            and rh217_mesusu = r48_mesusu
                            and rh217_anousu = r48_anousu limit 1
                    ),
                0) as editado
            from
                gerfcom
            where
                r48_rubric = 'R992'
                and r48_regist = {$oParam->matricula}
            union all
            select
                r35_mesusu as mes,
                r35_anousu as exercicio,
                round(r35_valor, 2)::varchar as valor,
                'gerfs13' as addres,
                coalesce(
                    (
                        select 
                            1 
                        from 
                            servidorrelatorioarquivogenerico 
                        where 
                            rh217_regist = r35_regist
                            and rh217_arquivorelatorio = 'portaria154AnexoII'
                            and rh217_mesusu = r35_mesusu
                            and rh217_anousu = r35_anousu limit 1
                    ),
                0) as editado
            from
                gerfs13
            where
                r35_rubric = 'R992'
                and r35_regist = {$oParam->matricula}
            order by exercicio asc, mes asc, addres asc;
SQL;

    $rs = db_query($sql);
    $qtdRegistros = pg_num_rows($rs);

    if ($qtdRegistros == 0) {
        throw new BusinessException("Nenhuma informação encontrada para a matrícula " . $oParam->matricula);
    }
    
    $aMeses = array(
        1 => "JANEIRO", 
        2 => "FEVEREIRO", 
        3 => "MARÇO", 
        4 => "ABRIL", 
        5 => "MAIO", 
        6 => "JUNHO", 
        7 => "JULHO", 
        8 => "AGOSTO", 
        9 => "SETEMBRO", 
        10 => "OUTUBRO", 
        11 => "NOVEMBRO", 
        12 => "DEZEMBRO",
        13 => "13º SALARIO / GRAT. NATALINA"
    );

    // Todo ano possui 12 meses com valor default zerado
    $aMesesDefault = array(
        1 => 0.00, 
        2 => 0.00, 
        3 => 0.00, 
        4 => 0.00, 
        5 => 0.00, 
        6 => 0.00, 
        7 => 0.00, 
        8 => 0.00, 
        9 => 0.00, 
        10 => 0.00, 
        11 => 0.00, 
        12 => 0.00,
        13 => 0.00
    );
    $registros =  array();

    for ($i = 0; $i < $qtdRegistros; $i ++) {
        $registro  = \db_utils::fieldsMemory($rs, $i);
       
        if (empty($registros[$registro->exercicio])) {
            $registros[$registro->exercicio] = $aMesesDefault;
        }

        if ($registro->editado) {
            if ($registro->addres == 'slag') {
                $registros[$registro->exercicio][$registro->mes] = $registro->valor;
            }
        } else {
            if ($registro->addres != 'slag') {
                $registros[$registro->exercicio][$registro->mes] += $registro->valor;
            }
        }
    }
    
    $quantidadeMaximaColunas = 5;
    $qtdTabelas  = ceil(sizeof($registros) / $quantidadeMaximaColunas);
    $contador = 0;
    $tabelas = array();
    $registrosTabela = $registros;
    for ($i = 0; $i < $qtdTabelas; $i++) {
        $aColunas = array("Mês");
        $j = 0;
        foreach ($registrosTabela as $key => $value) {
            if ($j < $quantidadeMaximaColunas) {
                $aColunas[] = array("exercicio" => $key, "valores" => $value);
                unset($registrosTabela[$key]);        
            } else {
                continue;
            }
            $j += 1;
        }
        $tabelas[]  = $aColunas;
    }

    $sServidor = $oCgm->getNome();
    $oPdf = new PDFDocument();
    $oPdf->disableHeaderDefault();
    $oPdf->Open();
    $oPdf->SetAutoPageBreak(false);
    $oPdf->SetFillColor(255);

    $oPdf->SetFontSize(7);
    $oPdf->setBold(true);
    
    $oPdf->AddPage();
    pdfHeader($oPdf);
    $oPdf->SetFontSize(12);
    $oPdf->Cell(200, 12, "RELAÇÃO  DAS REMUNERAÇÕES DE CONTRIBUIÇÕES", 0, 1, 'C', 0);
   
    $oPdf->SetFontSize(8);
    $oPdf->setBold(false);
    $data  = date('d/m/Y');
    $oPdf->Cell(200, 5, "REFERENTE À CERTIDÃO DE TEMPO DE CONTRIBUIÇÃO Nº__________, DE " . $data . ".", 0, 1, 'L', 0);
    $oPdf->setBold(true);
    $oPdf->SetFontSize(7);
    $oPdf->Cell(150, 4, "ÓRGÃO EXPEDIDOR:", 'TLR', 0, 'L', 0);
    $oPdf->Cell(40, 4, "CNPJ:", 'TR', 1, 'L', 0);    
    $oPdf->setBold(false);
    $oPdf->Cell(150, 4, $oCgmPrefeitura->getNome(), 'LR', 0, 'L', 0);
    $cnpj = db_formatar($oCgmPrefeitura->getCNPJ(), "cnpj");
    $oPdf->Cell(40, 4, $cnpj, 'R', 1, 'L', 0);

    $oPdf->setBold(true);
    $oPdf->Cell(150, 4, "NOME DO SERVIDOR:", 'TLR', 0, 'L', 0);
    $oPdf->Cell(40, 4, "MATRÍCULA:", 'TR', 1, 'L', 0);    
    $oPdf->setBold(false);
    $oPdf->Cell(150, 4, $oCgm->getNome(), 'LR', 0, 'L', 0);
    $oPdf->Cell(40, 4, $oServidor->getMatricula(), 'R', 1, 'L', 0);

    $oPdf->setBold(true);
    $oPdf->Cell(150, 4, "NOME DA MÃE:", 'TLR', 0, 'L', 0);
    $oPdf->Cell(40, 4, "DATA DE NASCIMETO:", 'TR', 1, 'L', 0);    
    $oPdf->setBold(false);
    $oPdf->Cell(150, 4, $oCgm->getNomeMae(), 'LR', 0, 'L', 0);
    $oPdf->Cell(40, 4, $oServidor->getDataNascimento(), 'R', 1, 'L', 0);


    $oPdf->setBold(true);
    $oPdf->Cell(60, 4, "DATA DE INICIO DA CONTRIUBIÇÃO/ADMISSÃO:", 'TLR', 0, 'L', 0);
    $oPdf->Cell(50, 4, "DATA DA EXONERAÇÃO:", 'TLR', 0, 'L', 0);
    $oPdf->Cell(40, 4, "PIS/PASEP:", 'TLR', 0, 'L', 0);
    $oPdf->Cell(40, 4, "CPF:", 'TR', 1, 'L', 0);    
    $oPdf->setBold(false);
    $oPdf->Cell(60, 4, $oServidor->getDataAdmissao(), 'LRB', 0, 'L', 0);
    $dataRescisao = $oServidor->getDataRescisao();
    if (!empty($dataRescisao)) {
        $dataRescisao = date('d/m/Y', $oServidor->getDataRescisao()->getTimeStamp());
    }
    $oPdf->Cell(50, 4, $dataRescisao, 'LRB', 0, 'L', 0);
    $oPdf->Cell(40, 4, $oServidor->getPISPASEP(), 'LRB', 0, 'L', 0);
    $oPdf->Cell(40, 4, db_formatar($oCgm->getCPF(), "CPF"), 'RB', 1, 'L', 0);

    $oPdf->ln();
    $x = $oPdf->getX();
    $y = $oPdf->getY();
    $oPdf->Line($x, $y, $x+190, $y);
    $oPdf->SetLineWidth(1);
    $oPdf->Line($x+0.5, $y+1, $x+189.5, $y+1);
    //$oPdf->SetLineWidth();
    $oPdf->Line($x, $y+2, $x+190, $y+2);
    
    $oPdf->setXY($x, $y+3);

    for ($i = 0; $i < sizeof($tabelas); $i ++) { 
        // Cabecalhos
        $oPdf->setBold(true);
        $VerificaQuebraPagina = $oPdf->getY();
        if ($VerificaQuebraPagina + 60 > 300) {
            $oPdf->AddPage();
            pdfHeader($oPdf);
        }
        for ($j = 0; $j < sizeof($tabelas[$i]); $j++) { 
            $quebra = 0;
            if ($j == sizeof($tabelas[$i]) - 1) {
                $quebra = 1;
            } 
            if ($j == 0) {
                $oPdf->Cell(40, 8, $tabelas[$i][$j], 'RLT', 0, "C");
            } else {
                $x = $oPdf->getX();
                $y = $oPdf->getY();
                $oPdf->SetFontSize(7);
                $oPdf->setBold(1);
                $oPdf->Cell(30, 4, "Ano: " . $tabelas[$i][$j]["exercicio"], 'RLT', 1);
                $oPdf->setX($x);
                $oPdf->Cell(30, 4, "Valor", 'RL', $quebra, "C");
                if (!$quebra) {
                    $oPdf->setXY($x+30, $y);
                }
            }
        }
        $oPdf->setBold(false);
        for ($iMes = 1; $iMes <= 13; $iMes++) {
            $oPdf->SetFontSize(7);
            $oPdf->Cell(40, 4, $aMeses[$iMes], 'BRLT', 0, "L");
            for ($j = 1; $j < sizeof($tabelas[$i]); $j++) {
                $quebra = 0;
                if ($j == sizeof($tabelas[$i]) - 1) {
                    $quebra = 1;
                }
                $valor = str_replace(',','.',$tabelas[$i][$j]["valores"][$iMes]);
                if ($valor !== '-') {
                    $valor = db_formatar($valor, 'f');
                }
                $oPdf->Cell(30, 4, $valor, 'TBRL', $quebra, "R");             
            }
        }
        $oPdf->ln();
    }
    $x = $oPdf->getX();
    $y = $oPdf->getY();
    $oPdf->Line($x, $y, $x+190, $y);
    $oPdf->SetLineWidth(1);
    $oPdf->Line($x+0.5, $y+1, $x+189.5, $y+1);
    //$oPdf->SetLineWidth();
    $oPdf->Line($x, $y+2, $x+190, $y+2);
    $oPdf->setXY($x, $y+3);

    $data = new \DBDate(date('d/m/Y'));
    $local = $oPrefeitura->getMunicipio() . ", " . $data->dataPorExtenso() . ".";
    
    $VerificaQuebraPagina = $oPdf->getY();
    if ($VerificaQuebraPagina + 40 > 300) {
        $oPdf->AddPage();
        pdfHeader($oPdf);
    }
    // Quadro da LOCAL
    $oPdf->SetFontSize(8);
    $oPdf->Cell(100, 4, "LOCAL E DATA:", 'TLR', 0, 'L', 0);
    $oPdf->Cell(90, 4, "CARIMBO, MATRICULA E ASSINATURA DO SERVIDOR", 'TR', 1, 'L', 0); 
    $oPdf->SetFontSize(6);
    $oPdf->Cell(100, 4, $local, 'LR', 0, 'L', 0);
    $oPdf->SetFontSize(8);
    $oPdf->Cell(90, 4, "RESPONSÁVEL:", 'R', 1, 'L', 0);

    $oPdf->Cell(100, 4, "", 'LR', 0, 'L', 0);
    $oPdf->Cell(90, 4, "", 'R', 1, 'L', 0);
    $oPdf->Cell(100, 4, "", 'LR', 0, 'L', 0);
    $oPdf->Cell(90, 4, "", 'R', 1, 'L', 0);
    $oPdf->Cell(100, 4, "", 'LR', 0, 'L', 0);
    $oPdf->Cell(90, 4, "", 'R', 1, 'L', 0);

    $oPdf->Cell(100, 4, "", 'LRB', 0, 'L', 0);
    $oPdf->Cell(90, 4, "", 'RB', 1, 'L', 0);
    $oPdf->ln();

    $VerificaQuebraPagina = $oPdf->getY();
    if ($VerificaQuebraPagina + 40 > 300) {
        $oPdf->AddPage();
        pdfHeader($oPdf);
    }

    $oPdf->SetFontSize(10);
    $oPdf->setBold(1);
    $aUnidadeGestora = array(1 => "RPPS", 2 => "RGPS");
    $oPdf->Cell(90, 4, "UNIDADE GESTORA DO " . $aUnidadeGestora[$oParam->unidadegestora], 0, 1, 'L', 0); 
    $oPdf->setBold(0);
    $oPdf->SetFontSize(8);
    $oPdf->Cell(190, 4, "HOMOLOGO o presente documento e declaro que as informações nele constantes correspondem com a verdade.", "RLT", 1, 'L', 0); 
    $oPdf->Cell(190, 4, "", "RL", 1, 'L', 0); 
    $oPdf->Cell(190, 4, "Local e data:", "RL", 1, 'L', 0); 
    $x = $oPdf->getX();
    $y = $oPdf->getY();
    $oPdf->Line($x+18, $y-1, $x+100, $y-1);
    $oPdf->Cell(190, 4, "", "RL", 1, 'L', 0); 
    $y = $oPdf->getY();
    $oPdf->Line(60, $y+3, 150, $y+3);
    $oPdf->Cell(190, 4, "", "RL", 1, 'L', 0); 
    if ($oParam->unidadegestora == 1) {
        $oPdf->Cell(190, 4, "Carimbo e assinatura do dirigente da unidade gestora do", 'RL', 1, 'C', 0); 
        $oPdf->Cell(190, 4, "Regime Próprio de Previdência Social", 'RL', 1, 'C', 0); 
    }
    $oPdf->Cell(190, 4, "", "BRL", 1, 'L', 0); 
    $oPdf->Cell(100, 4, "ESTE DOCUMENTO NÃO CONTÉM EMENDAS NEM RASURAS", 0, 1, 'L', 0); 
    
    $oPdf->showPDF();
} catch (Exception $exception) {
    return db_redireciona("db_erros.php?fechar=true&db_erro={$exception->getMessage()}");
}

function pdfHeader($oPdf) {
    
    $oInstituicao = new Instituicao(db_getsession("DB_instit"));
    $iColuna = $oPdf->GetLeftMargin();

    $oPdf->setXY($iColuna, 1);
    $oPdf->image("imagens/files/{$oInstituicao->getImagemLogo()}", $iColuna, 3, 20);

    $oPdf->setBold(true);
    $oPdf->setItalic(true);
    $oPdf->setUnderline(false);
    $oPdf->setFontSize(9);

    if (strlen($oInstituicao->getDescricao()) > 42) {
        $oPdf->setFontSize(8);
    }

    $iColunaTexto = $iColuna + 23;
    $oPdf->text($iColunaTexto, 9, $oInstituicao->getDescricao());

    $oPdf->setBold(false);
    $oPdf->setFontSize(8);

    $sComplento = substr( trim($oInstituicao->getComplemento()), 0, 20);

    if (!empty($sComplento)) {
        $sComplento = ", " . substr( trim($oInstituicao->getComplemento()), 0, 20);
    }

    $oPdf->text($iColunaTexto, 14, trim($oInstituicao->getLogradouro()) . ", " . trim($oInstituicao->getNumero()) . $sComplento );
    $oPdf->text($iColunaTexto, 18, trim($oInstituicao->getMunicipio()) . " - " . trim($oInstituicao->getUF()) );
    $oPdf->text($iColunaTexto, 22, trim($oInstituicao->getTelefone()) . "   -    CNPJ : " . db_formatar($oInstituicao->getCNPJ(), "cnpj"));
    $oPdf->text($iColunaTexto, 26, trim($oInstituicao->getEmail()) );
    $oPdf->text($iColunaTexto, 30, $oInstituicao->getSite());

    $iColunaFinal = $oPdf->getAvailWidth() + $iColuna;

    $oPdf->setFillColor(0);
    $oPdf->setLeftMargin($iColuna);
    $oPdf->setY(35);
}
