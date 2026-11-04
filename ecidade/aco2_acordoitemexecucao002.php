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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification('libs/db_utils.php'));

$oGET = db_utils::postMemory($_GET);

$oInstituicao = InstituicaoRepository::getInstituicaoSessao();

$acordoItemExecucao = new cl_acordoitemexecutado();
    
$sql  =  " 
select ac16_numero, 
       ac16_anousu, 
       ac16_datainicio, 
       ac16_datafim, 
       ac16_objeto, 
       ac16_deptoresponsavel, 
       descrdepto, 
       z01_nome, 
       ac29_observacao, 
       (select z01_nome||'|'||rh01_regist  
          from acordocomissaomembro 
               inner join cgm on z01_numcgm = ac07_numcgm 
               inner join rhpessoal on rh01_numcgm = z01_numcgm 
         where ac07_acordocomissao = ac16_acordocomissao 
           and ac07_tipomembro = 4 
         limit 1) as fiscal 
          from acordo 
               inner join db_depart            on coddepto           = ac16_deptoresponsavel
               inner join cgm                  on z01_numcgm         = ac16_contratado
               inner join acordoposicao        on ac26_acordo        = ac16_sequencial
               inner join acordoitem           on ac20_acordoposicao = ac26_sequencial 
               inner join acordoitemexecutado  on ac20_sequencial    = ac29_acordoitem 
               inner join pcmater              on pc01_codmater      = ac20_pcmater    
               inner join matunid              on m61_codmatunid     = ac20_matunid    
         where ac29_sequencial = {$acordoitemexecucao}";
$rsDados  = $acordoItemExecucao->sql_record($sql);
if ($acordoItemExecucao->numrows == 0) {
    die("Nenhuma informação encontrada");
}
$dadosExecucao = db_utils::fieldsMemory($rsDados, 0);
        
$larguraLinha = 192;
$alturaLinhaTitulo = 10;
$alturaLinhaTitulo2 = 6;
$alturaLinha = 4;
$alturaEspacamento = 4;

$pdf = new Pdf();
$pdf->AliasNbPages();
$pdf->addTitulo("");
$pdf->addTitulo("RELATÓRIO MENSAL DE ACOMPANHAMENTO DE CONTRATO");
$pdf->addTitulo("");
$pdf->addTitulo("CONTRATO Nº {$dadosExecucao->ac16_numero}/{$dadosExecucao->ac16_anousu}");
$pdf->init();

$pdf->setfont('arial', 'b', 8);
$pdf->cell($larguraLinha, $alturaLinhaTitulo, "RELATÓRIO MENSAL DE ACOMPANHAMENTO DE CONTRATO", 0, 1, "C", 0);

$pdf->ln($alturaEspacamento);

$pdf->setfont('arial', 'b', 8);
$pdf->cell(
    ($larguraLinha/2),
    $alturaLinhaTitulo,
    "CONTRATO Nº {$dadosExecucao->ac16_numero}/{$dadosExecucao->ac16_anousu}",
    1,
    0,
    "L",
    0
);
$pdf->cell(
    ($larguraLinha/2),
    $alturaLinhaTitulo,
    "PRAZO DE VIGÊNCIA DO CONTRATO: "
    ."De ".db_formatar($dadosExecucao->ac16_datainicio, "d")
    ." à ".db_formatar($dadosExecucao->ac16_datafim, "d"),
    1,
    1,
    "L",
    0
);

$pdf->ln($alturaEspacamento);

$pdf->setfont('arial', 'b', 8);
$pdf->cell($larguraLinha, $alturaLinhaTitulo2, 'UNIDADE DETENTORA DO CONTRATO:', "LTR", 1, "L", 0);
$pdf->setfont('arial', '', 8);
$pdf->cell($larguraLinha, $alturaLinha, $dadosExecucao->descrdepto, "LRB", 1, "L", 0);

$pdf->ln($alturaEspacamento);

$pdf->setfont('arial', 'b', 8);
$pdf->cell($larguraLinha, $alturaLinhaTitulo2, 'OBJETO DO CONTRATO:', "LTR", 1, "L", 0);
$pdf->setfont('arial', '', 8);
$pdf->multiCell($larguraLinha, $alturaLinha, $dadosExecucao->ac16_objeto, "LRB", "J");

$pdf->ln($alturaEspacamento);

$pdf->setfont('arial', 'b', 8);
$pdf->cell($larguraLinha, $alturaLinhaTitulo2, 'EMPRESA CONTRATADA:', "LTR", 1, "L", 0);
$pdf->setfont('arial', '', 8);
$pdf->cell($larguraLinha, $alturaLinha, $dadosExecucao->z01_nome, "LRB", 1, "L", 0);

if (!empty($dadosExecucao->ac29_observacao)) {
    $pdf->ln($alturaEspacamento);

    $pdf->setfont('arial', 'b', 8);
    $pdf->cell($larguraLinha, $alturaLinhaTitulo2, 'OBSERVAÇÃO:', "LTR", 1, "L", 0);
    $pdf->setfont('arial', '', 8);
    $pdf->multiCell($larguraLinha, $alturaLinha, $dadosExecucao->ac29_observacao, "LRB");
}

$pdf->ln($alturaEspacamento);

$pdf->setfont('arial', '', 8);
$pdf->cell(
    ($larguraLinha/2),
    ($alturaLinha*3),
    mb_convert_case($oInstituicao->getMunicipio(), MB_CASE_TITLE)."/".$oInstituicao->getUf().", ".date("d/m/Y"),
    "LTRB",
    0,
    "C",
    0
);

$nomeFiscal = "";
$matriculaFiscal = "";
if (!empty($dadosExecucao->fiscal)) {
    $fiscal = explode("|", $dadosExecucao->fiscal);
    $nomeFiscal = $fiscal[0];
    $matriculaFiscal = $fiscal[1];
}

$pdf->cell(($larguraLinha/2), $alturaLinha, $nomeFiscal, "LTR", 1, "C", 0);
$pdf->cell(($larguraLinha/2), $alturaLinha, "", "", 0, "C", 0);
$pdf->cell(($larguraLinha/2), $alturaLinha, 'FISCAL DO CONTRATO', "LR", 1, "C", 0);
$pdf->cell(($larguraLinha/2), $alturaLinha, "", "", 0, "C", 0);
$pdf->cell(($larguraLinha/2), $alturaLinha, "MAT.: {$matriculaFiscal}", "LRB", 1, "C", 0);

$pdf->Output();
