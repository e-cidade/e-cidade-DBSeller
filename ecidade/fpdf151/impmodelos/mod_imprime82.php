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

use App\Domain\Tributario\Arrecadacao\Repositories\RecibobarpixRepository;

$existemDemaisTransmitentes = array_key_exists(1, $this->arrayTransmitentes);
$existemDemaisAdquirentes = array_key_exists(1, $this->arrayAdquirentes);
$existemDemaisConstrucoes = (isset($this->arrayit09_codigo)
    && array_key_exists(9, $this->arrayit09_codigo));
$geraVersoPagina = ($existemDemaisTransmitentes || $existemDemaisAdquirentes || $existemDemaisConstrucoes);

$valorTotalValido = ((float)($this->it14_valorpaga + $this->tx_banc) > 0);

for ($indexPagina = 0; $indexPagina < 1; $indexPagina++) {
    $alturaLinhas = 4;
    $Y = 16;

    $this->objpdf->AddPage();
    if (!$this->lLiberado) {
        marcaDaAguaNaoLiberado($this);
    }
    if (!$valorTotalValido) {
        foreach ($this->aDadosFormasPgto as $formaPgto) {
            if ((float)$formaPgto['Valor'] > 0) {
                marcaDaAguaIsencao($this, $formaPgto['Descricao']);
                break;
            }
        }
    }
    cabecalho($this, $Y);
    identificacaoLiberacaoEInclusao($this);
    quadroIdentificacaoTransmitenteEAdquirente($this, $alturaLinhas);
    quadroDadosImovelEConstrucoes($this, $alturaLinhas);
    quadroObservacoes($this, $Y + 95, $alturaLinhas);
    dadosImovelRural($this, $alturaLinhas);

    if ($valorTotalValido) {
        guiaCaixa($this, $Y, $alturaLinhas);
        quadroValoresTotais($this, $Y + 184, $alturaLinhas, 'Guia do Contribuinte');
        linhaDigitavel($this, $Y + 198, $alturaLinhas);
        mensagemRecibo($this, $Y + 210, $alturaLinhas+2);
        qrCodePix($this, $Y + 198, $alturaLinhas);
    }

    if ($geraVersoPagina) {
        alertaVerso($this, $valorTotalValido ? $Y : $Y + 23, $alturaLinhas);
    }

    if ($geraVersoPagina) {
        $this->objpdf->AddPage();
        if (!$this->lLiberado) {
            marcaDaAguaNaoLiberado($this);
        }
        cabecalhoGuiaVerso($this);
        quadroDemaisTransmitentes($this, $alturaLinhas, $this->objpdf->getY() + 5);
        quadroDemaisAdquirentes($this, $alturaLinhas, $this->objpdf->getY() + 5);
        quadroDemaisConstrucoes($this, $alturaLinhas, $this->objpdf->getY() + 5);
    }
}

function marcaDaAguaNaoLiberado($thisObj)
{
    $thisObj->objpdf->SetFont('Arial', 'B', 98);
    $thisObj->objpdf->SetFillColor(228);
    $thisObj->objpdf->TextWithRotation(30, 30, "NÃO LIBERADA", -60, 2);
    $thisObj->objpdf->SetFillColor(235);
}

function marcaDaAguaIsencao($thisObj, $texto)
{
    $thisObj->objpdf->SetFont('Arial', 'B', 60);
    $thisObj->objpdf->SetFillColor(232);
    $thisObj->objpdf->TextWithRotation(20, 280, $texto, 0, 2);
    $thisObj->objpdf->SetFillColor(235);
}

function cabecalho($thisObj, $y)
{
    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', 'B', 10);
    $thisObj->objpdf->cell(100, 3, $thisObj->nomeinst, 0, 0, "L", 0);
    $thisObj->objpdf->cell(100, 3, 'Vencimento: ' . db_formatar($thisObj->datavencimento, 'd'), 0, 1, "L", 0);

    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(100, 3, 'Imposto Sobre Transmissão de Bens Imóveis (ITBI)', 0, 0, "L", 0);
    $thisObj->objpdf->cell(50, 3, 'Recibo Emitido em: ' . db_formatar($thisObj->dataemissao, 'd'), 0, 1, "L", 0);

    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(100, 3, 'Tipo de Transmissão: ' . $thisObj->it04_descr, 0, 0, "L", 0);
    $thisObj->objpdf->cell(50, 3, 'Código de Arrecadação: ' . $thisObj->numpreitbi, 0, 1, "L", 0);

    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(100, 3, 'Processo: ' . $thisObj->processo, 0, 1, "L", 0);

    $thisObj->objpdf->sety($y + 9);
    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', 'B', 10);
    $thisObj->objpdf->cell(100, 3, 'Guia de Recolhimento N' . chr(176) . ' SMF/' . db_formatar($thisObj->itbi, 's', '0', 5) . '/' . $thisObj->ano, 0, 0, "L", 0);
    $thisObj->objpdf->cell(50, 5, $thisObj->tipoitbi == "urbano" ? "ITBI URBANO" : "ITBI RURAL", 0, 1, "C", 0);
}

function quadroIdentificacaoTransmitenteEAdquirente($thisObj, $alturaLinhas)
{
    $thisObj->objpdf->SetFillColor(235);
    $thisObj->objpdf->SetFont('Arial', '', 5);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(20, $alturaLinhas, '', 1, 0, "C", 1);
    $thisObj->objpdf->cell(88, $alturaLinhas, 'Identificação do Transmitente', 1, 0, "C", 1);
    $thisObj->objpdf->cell(88, $alturaLinhas, 'Identificação do Adquirente', 1, 1, "C", 1);

    $thisObj->objpdf->cell(20, $alturaLinhas, 'Nome : ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 6);
    $thisObj->objpdf->cell(88, $alturaLinhas, isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_nome : '', 1, 0, "L", 0);
    $thisObj->objpdf->cell(88, $alturaLinhas, isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_nome : '', 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(20, $alturaLinhas, 'CNPJ/CPF:', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 6);
    $thisObj->objpdf->cell(88, $alturaLinhas, db_cgccpf(isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_cgccpf : ''), 1, 0, "L", 0);
    $thisObj->objpdf->cell(88, $alturaLinhas, db_cgccpf(isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_cgccpf : ''), 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(20, $alturaLinhas, 'Fone:', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 6);
    $thisObj->objpdf->cell(88, $alturaLinhas, isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_telef : '', 1, 0, "L", 0);
    $thisObj->objpdf->cell(88, $alturaLinhas, isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_telef : '', 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(20, $alturaLinhas, 'Endereço : ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 6);

    $enderTransmitente1 = (isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_ender : '') . (isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_numero : '') . (isValidArray($thisObj->arrayTransmitentes, null, 1) ? ' - ' . $thisObj->arrayTransmitentes[0]->z01_compl : '');
    $thisObj->objpdf->cell(88, $alturaLinhas, $enderTransmitente1, 1, 0, "L", 0);

    $enderAdquirente1 = (isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_ender : '') . (isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_numero : '') . (isValidArray($thisObj->arrayAdquirentes, null, 1) ? ' - ' . $thisObj->arrayAdquirentes[0]->z01_compl : '');
    $thisObj->objpdf->cell(88, $alturaLinhas, $enderAdquirente1, 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(20, $alturaLinhas, 'Município : ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 6);

    $enderTransmitente2 = (isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_munic : '') . (isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_uf : '') . (isValidArray($thisObj->arrayTransmitentes, null, 1) ? ' - ' . $thisObj->arrayTransmitentes[0]->z01_cep : '');
    $thisObj->objpdf->cell(88, $alturaLinhas, $enderTransmitente2, 1, 0, "L", 0);

    $enderAdquirente2 = (isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_munic : '') . (isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_uf : '') . (isValidArray($thisObj->arrayAdquirentes, null, 1) ? ' - ' . $thisObj->arrayAdquirentes[0]->z01_cep : '');
    $thisObj->objpdf->cell(88, $alturaLinhas, $enderAdquirente2, 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(20, $alturaLinhas, 'E-mail: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 6);
    $thisObj->objpdf->cell(88, $alturaLinhas, isValidArray($thisObj->arrayTransmitentes, null, 1) ? $thisObj->arrayTransmitentes[0]->z01_email : '', 1, 0, "L", 0);
    $thisObj->objpdf->cell(88, $alturaLinhas, isValidArray($thisObj->arrayAdquirentes, null, 1) ? $thisObj->arrayAdquirentes[0]->z01_email : '', 1, 1, "L", 0);
    $thisObj->objpdf->Ln(1);
}

function identificacaoLiberacaoEInclusao($thisObj)
{
    $thisObj->objpdf->setfillcolor(0);
    $thisObj->objpdf->SetFont('Arial', '', 4);
    $thisObj->objpdf->TextWithDirection(9.5, 116, "Incluído por: " . $thisObj->usuarioNomeIncluido, 'U');

    if (isset($thisObj->usuarioNomeLiberado) && $thisObj->usuarioNomeLiberado != "") {
        $thisObj->objpdf->TextWithDirection(9.5, 78, "Liberado por: " . $thisObj->usuarioNomeLiberado, 'U');
    }
}

function quadroDadosImovelEConstrucoes($thisObj, $alturaLinhas)
{
    $thisObj->objpdf->Ln(1);
    $thisObj->objpdf->setfillcolor(235);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);

    if ($thisObj->tipoitbi == "urbano") {
        $thisObj->objpdf->cell(88, $alturaLinhas, 'Dados do Imóvel', 1, 0, "C", 1);
    } else {
        $thisObj->objpdf->cell(88, $alturaLinhas, 'Dados da Terra', 1, 0, "C", 1);
    }
    $thisObj->objpdf->cell(2, $alturaLinhas, '', 0, 0, "C", 0);

    if ($thisObj->tipoitbi == "urbano") {
        $thisObj->objpdf->cell(106, $alturaLinhas, 'Dados das Construções', 1, 1, "C", 1);
    } else {
        $thisObj->objpdf->cell(106, $alturaLinhas, 'Dados das Benfeitorias', 1, 1, "C", 1);
    }

    $thisObj->objpdf->SetFont('Arial', '', 8);
    $y = $thisObj->objpdf->gety();

    $matriculaRI = isset($thisObj->it22_matricri)
        && !empty($thisObj->it22_matricri)
        ? $thisObj->it22_matricri
        : ((isset($thisObj->informacoesImovel->matriculari))
            ? $thisObj->informacoesImovel->matriculari : '');

    $setorRI = isset($thisObj->it29_setorloc)
        && !empty($thisObj->it29_setorloc)
        ? $thisObj->it29_setorloc
        : ((isset($thisObj->informacoesImovel->setorri))
            ? $thisObj->informacoesImovel->setorri : '');

    $quadraRI = isset($thisObj->it22_quadrari)
        && !empty($thisObj->it22_quadrari)
        ? $thisObj->it22_quadrari
        : ((isset($thisObj->informacoesImovel->quadrari))
            ? $thisObj->informacoesImovel->quadrari : '');

    $loteRI = isset($thisObj->it22_loteri)
        && !empty($thisObj->it22_loteri)
        ? $thisObj->it22_loteri
        : ((isset($thisObj->informacoesImovel->loteri))
            ? $thisObj->informacoesImovel->loteri : '');

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(15, $alturaLinhas, 'Matrícula: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(27, $alturaLinhas, isset($thisObj->informacoesImovel->matricula)
        ? $thisObj->informacoesImovel->matricula : '', 1, 0, "L", 0);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(19, $alturaLinhas, 'Matrícula RI: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(27, $alturaLinhas, $matriculaRI, 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(10, $alturaLinhas, 'Setor: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(18, $alturaLinhas, isset($thisObj->informacoesImovel->setor)
        ? $thisObj->informacoesImovel->setor : '', 1, 0, "L", 0);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(13, $alturaLinhas, 'Quadra: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(19, $alturaLinhas, isset($thisObj->informacoesImovel->quadra)
        ? $thisObj->informacoesImovel->quadra : '', 1, 0, "L", 0);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(9, $alturaLinhas, 'Lote: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(19, $alturaLinhas, isset($thisObj->informacoesImovel->lote)
        ? $thisObj->informacoesImovel->lote : '', 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(13, $alturaLinhas, 'Setor RI: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(75, $alturaLinhas, $setorRI, 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(16, $alturaLinhas, 'Quadra RI: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(19, $alturaLinhas, $quadraRI, 1, 0, "L", 0);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(12, $alturaLinhas, 'Lote RI: ', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(41, $alturaLinhas, $loteRI, 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(14, $alturaLinhas, 'Situação:', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(74, $alturaLinhas, $thisObj->it07_descr, 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(24, $alturaLinhas, 'Frente (testada):', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(64, $alturaLinhas, $thisObj->it05_frente, 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(30, $alturaLinhas, 'Área', 1, 0, "C", 1);
    $thisObj->objpdf->cell(29, $alturaLinhas, 'Real', 1, 0, "C", 1);
    $thisObj->objpdf->cell(29, $alturaLinhas, 'Transmitida', 1, 1, "C", 1);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(30, $alturaLinhas, 'Terreno', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(29, $alturaLinhas, db_formatar($thisObj->areaterreno + 0, 'f', ' ', ' ', ' ', 10) . ($thisObj->tipoitbi == "urbano" ? 'm2' : 'ha'), 1, 0, "L", 0);
    $thisObj->objpdf->cell(29, $alturaLinhas, (isValidArray($thisObj->areaterrenomat, 1) ? db_formatar($thisObj->areatran, 'f', ' ', ' ', ' ', 10) . ($thisObj->tipoitbi == "urbano" ? 'm2' : 'ha') : (isValidArray($thisObj->areaterrenomat, null, 2) && isValidString($thisObj->areaterrenomat[1], null, 3) ? $thisObj->areatran : db_formatar($thisObj->areatran, 'f', ' ', ' ', ' ', 10) . ($thisObj->tipoitbi == "urbano" ? 'm2' : 'ha'))), 1, 1, "L", 0);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(30, $alturaLinhas, 'Construções', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(29, $alturaLinhas, (@$thisObj->areatotal == 0 ? '' : (isValidArray($thisObj->areaedificadamat, 1) ? db_formatar(@$thisObj->areatotal, 'f', ' ', ' ', ' ', 6) . 'm2' : ((isValidArray($thisObj->areaedificadamat, null, 2) && isValidString($thisObj->areaedificadamat[1], null, 3)) ? db_formatar(@$thisObj->areatotal, 'f', ' ', ' ', ' ', 6) . 'm2' : db_formatar(@$thisObj->areatotal, 'f', ' ', ' ', ' ', 6) . 'm2'))), 1, 0, "L", 0);
    $thisObj->objpdf->cell(29, $alturaLinhas, (@$thisObj->areatotal == 0 ? '' : (isValidArray($thisObj->areaedificadamat, 1) ? db_formatar(@$thisObj->areatrans, 'f', ' ', ' ', ' ', 6) . 'm2' : ((isValidArray($thisObj->areaedificadamat, null, 2) && isValidString($thisObj->areaedificadamat[1], null, 3)) ? db_formatar(@$thisObj->areatrans, 'f', ' ', ' ', ' ', 6) . 'm2' : db_formatar(@$thisObj->areatrans, 'f', ' ', ' ', ' ', 6) . 'm2'))), 1, 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(30, $alturaLinhas, 'Endereço do imóvel:', 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);

    $enderecoImovel = '';
    if (
        isset($thisObj->it22_descrlograd)
        && isset($thisObj->it22_numero)
        && isset($thisObj->it22_compl)
        && isset($thisObj->it22_setor)
    ) {
        $enderecoImovel .= "{$thisObj->it22_descrlograd}";
        $enderecoImovel .= ", {$thisObj->it22_numero}";
        $enderecoImovel .= ", {$thisObj->it22_compl}";
        $enderecoImovel .= " - {$thisObj->it22_setor}";
    }

    $enderecoImovel  = empty(trim(preg_replace('/[^A-Za-z]/', '', $enderecoImovel))) ? '' : $enderecoImovel;
    $enderecoImovel  = empty(trim($enderecoImovel)) ? $thisObj->it18_localimovel : $enderecoImovel;

    $thisObj->objpdf->cell(58, $alturaLinhas, substr($enderecoImovel, 0, 31), 1, 1, "L", 0);
    $thisObj->objpdf->cell(196, $alturaLinhas, substr($enderecoImovel, 31, 123), 1, 1, "L", 0);

    $thisObj->objpdf->SetXY(100, $y);
    $thisObj->objpdf->SetFont('Arial', 'B', 7);
    $thisObj->objpdf->cell(24, $alturaLinhas, 'Descrição', 1, 0, "C", 1);
    $thisObj->objpdf->cell(34, $alturaLinhas, 'Tipo', 1, 0, "C", 1);
    $thisObj->objpdf->cell(20, $alturaLinhas, 'Área m²', 1, 0, "C", 1);
    $thisObj->objpdf->cell(20, $alturaLinhas, 'Área trans m²', 1, 0, "C", 1);
    $thisObj->objpdf->cell(8, $alturaLinhas, 'Ano', 1, 1, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 7);
    $y = $thisObj->objpdf->gety();

    for ($ii = 1; $ii <= 9; $ii++) {
        $thisObj->objpdf->setx(100);
        $thisObj->objpdf->cell(24, $alturaLinhas . '', '', 1, 0, "C");
        $thisObj->objpdf->cell(34, $alturaLinhas, '', 1, 0, "C");
        $thisObj->objpdf->cell(20, $alturaLinhas, '', 1, 0, "C");
        $thisObj->objpdf->cell(20, $alturaLinhas, '', 1, 0, "C");
        $thisObj->objpdf->cell(8, $alturaLinhas, '', 1, 1, "L");
    }
    $thisObj->objpdf->SetXY(100, $y);

    if ($thisObj->linhasresultcons > 0) {
        for ($n = 0; ($n < $thisObj->linhasresultcons && $n < 9); $n++) {
            $thisObj->objpdf->setx(100);
            $thisObj->objpdf->cell(24, $alturaLinhas, (strlen($thisObj->arrayit09_codigo[$n]) > 12 ? substr($thisObj->arrayit09_codigo[$n], 0, 12) . "..." : $thisObj->arrayit09_codigo[$n]), 0, 0, "L", 0);
            $thisObj->objpdf->cell(35, $alturaLinhas, substr($thisObj->arrayit10_codigo[$n], 0, 20), 0, 0, "L", 0);
            $thisObj->objpdf->cell(20, $alturaLinhas, db_formatar($thisObj->arrayit08_area[$n], 'f', ' ', ' ', ' ', 5), 0, 0, "R", 0);
            $thisObj->objpdf->cell(20, $alturaLinhas, db_formatar($thisObj->arrayit08_areatrans[$n], 'f', ' ', ' ', ' ', 5), 0, 0, "R", 0);
            $thisObj->objpdf->cell(8, $alturaLinhas, $thisObj->arrayit08_ano[$n], 0, 1, "L", 0);
        }
    }
};

function dadosImovelRural($thisObj, $alturaLinhas)
{
    $thisObj->objpdf->SetFont('Arial', 'B', 9);
    $linhasUtilizacao   = isset($thisObj->aDadosRuralCaractUtil) ? count($thisObj->aDadosRuralCaractUtil) : 0;
    $linhasDistribuicao = isset($thisObj->aDadosRuralCaractDist) ? count($thisObj->aDadosRuralCaractDist) : 0;
    $maiorLinha = $linhasUtilizacao > $linhasDistribuicao ? $linhasUtilizacao : $linhasDistribuicao;

    if ($maiorLinha > 0) {
        $thisObj->objpdf->sety($thisObj->objpdf->getY() + 14);
        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(33, $alturaLinhas, 'Frente', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(33, $alturaLinhas, $thisObj->it18_frente . ' ha', 1, 0, "C", 0);
        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(33, $alturaLinhas, 'Fundos', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(32, $alturaLinhas, $thisObj->it18_fundos . ' ha', 1, 0, "C", 0);
        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(33, $alturaLinhas, 'Profundidade', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(32, $alturaLinhas, $thisObj->it18_prof . ' ha', 1, 1, "C", 0);

        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(49, $alturaLinhas, 'Área total', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(49, $alturaLinhas, $thisObj->areaterreno . ' ha', 1, 0, "C", 0);
        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(49, $alturaLinhas, 'Área Transmitida', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(49, $alturaLinhas, $thisObj->areatran . ' ha', 1, 1, "C", 0);

        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(98, $alturaLinhas, 'Nome logradouro', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(98, $alturaLinhas, substr($thisObj->it18_nomelograd, 0, 60), 1, 1, "L", 0);

        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(98, $alturaLinhas, 'Localização', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(98, $alturaLinhas, substr($thisObj->it18_localimovel, 0, 60), 1, 1, "L", 0);

        $thisObj->objpdf->sety($thisObj->objpdf->getY() + 3);
        $thisObj->objpdf->SetFont('Arial', 'B', 9);
        $thisObj->objpdf->cell(98, $alturaLinhas, 'Utilização de Terra (ha)', 1, 0, "L", 1);
        $thisObj->objpdf->SetFont('Arial', '', 9);
        $thisObj->objpdf->cell(98, $alturaLinhas, 'Distribuição de Terra (ha)', 1, 1, "L", 1);

        for ($i = 0; $i < $maiorLinha; $i++) {
            $existeLinhaUtilizacao = ($linhasUtilizacao - 1) >= $i;
            $existeLinhaDistribuicao = ($linhasDistribuicao - 1) >= $i;

            if ($existeLinhaUtilizacao) {
                $thisObj->objpdf->SetFont('Arial', 'B', 9);
                $thisObj->objpdf->cell(69, $alturaLinhas, $thisObj->aDadosRuralCaractUtil[$i]['Descricao'], 1, 0, "L", 0);
                $thisObj->objpdf->SetFont('Arial', '', 9);
                $thisObj->objpdf->cell(29, $alturaLinhas, $thisObj->aDadosRuralCaractUtil[$i]['Valor'] . '%', 1, 0, "R", 0);
            } else {
                $thisObj->objpdf->SetFont('Arial', 'B', 9);
                $thisObj->objpdf->cell(69, $alturaLinhas, '', 1, 0, "L", 0);
                $thisObj->objpdf->SetFont('Arial', '', 9);
                $thisObj->objpdf->cell(29, $alturaLinhas, '', 1, 0, "R", 0);
            }

            if ($existeLinhaDistribuicao) {
                $thisObj->objpdf->SetFont('Arial', 'B', 9);
                $thisObj->objpdf->cell(69, $alturaLinhas, $thisObj->aDadosRuralCaractDist[$i]['Descricao'], 1, 0, "L", 0);
                $thisObj->objpdf->SetFont('Arial', '', 9);
                $thisObj->objpdf->cell(29, $alturaLinhas, $thisObj->aDadosRuralCaractDist[$i]['Valor'] . '%', 1, 1, "R", 0);
            } else {
                $thisObj->objpdf->SetFont('Arial', 'B', 9);
                $thisObj->objpdf->cell(69, $alturaLinhas, '', 1, 0, "L", 0);
                $thisObj->objpdf->SetFont('Arial', '', 9);
                $thisObj->objpdf->cell(29, $alturaLinhas, '', 1, 1, "R", 0);
            }
        }
    }
}

function quadroObservacoes($thisObj, $y, $alturaLinhas)
{
    $thisObj->objpdf->sety($y);
    $observacao = substr(str_replace("\n", " ", $thisObj->observacaoIncluido . " " . $thisObj->observacaoLiberado), 0, 524);

    $thisObj->objpdf->sety($y);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(98, $alturaLinhas, 'Observações:', 1, 0, "L", 1);
    $thisObj->objpdf->cell(38, $alturaLinhas, 'Emitido no Departamento', 1, 0, "C", 1);
    $thisObj->objpdf->cell(60, $alturaLinhas, $thisObj->nomeDepartamento, 1, 1, "L", 0);
    $thisObj->objpdf->SetFont('Arial', '', 7);

    $thisObj->objpdf->multiCell(196, $alturaLinhas, str_pad(substr($observacao, 0, 775), 775), 1, "L", 0);

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(27, $alturaLinhas, "Tipo", 1, 0, "C", 1);
    $thisObj->objpdf->cell(28, $alturaLinhas, "Informado", 1, 0, "C", 1);
    $thisObj->objpdf->cell(28, $alturaLinhas, "Avaliado", 1, 0, "C", 1);
    $iPosicaoXFormaPgto = $thisObj->objpdf->getX();
    $thisObj->objpdf->ln();

    if ($thisObj->tipoitbi == "urbano") {
        $thisObj->objpdf->cell(27, $alturaLinhas, "Terreno", 1, 0, "L", 1);
    } else {
        $thisObj->objpdf->cell(27, $alturaLinhas, "Terra", 1, 0, "L", 1);
    }

    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(28, $alturaLinhas, db_formatar($thisObj->it01_valorterreno, 'f'), 1, 0, "R", 0);
    $thisObj->objpdf->cell(28, $alturaLinhas, db_formatar($thisObj->it14_valoravalter, 'f'), 1, 1, "R", 0);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);

    if ($thisObj->tipoitbi == "urbano") {
        $thisObj->objpdf->cell(27, $alturaLinhas, "Construção", 1, 0, "L", 1);
    } else {
        $thisObj->objpdf->cell(27, $alturaLinhas, "Benfeitoria", 1, 0, "L", 1);
    }

    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(28, $alturaLinhas, db_formatar($thisObj->it01_valorconstr, 'f'), 1, 0, "R", 0);
    $thisObj->objpdf->cell(28, $alturaLinhas, db_formatar($thisObj->it14_valoravalconstr, 'f'), 1, 1, "R", 0);
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(27, $alturaLinhas, "Total", 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(28, $alturaLinhas, db_formatar($thisObj->it01_valortransacao, 'f'), 1, 0, "R", 0);
    $thisObj->objpdf->cell(28, $alturaLinhas, db_formatar($thisObj->it14_valoraval, 'f'), 1, 1, "R", 0);

    if (isset($thisObj->dataLiberado) && $thisObj->dataLiberado != "") {

        $thisObj->objpdf->SetFont('Arial', 'B', 8);
        $thisObj->objpdf->cell(27, $alturaLinhas, "Data de Avaliação:", 1, 0, "L", 1);

        $thisObj->objpdf->SetFont('Arial', '', 8);
        $thisObj->objpdf->cell(28, $alturaLinhas, $thisObj->dataLiberado, 1, 1, "R", 0);
    }

    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->setXY($iPosicaoXFormaPgto, $thisObj->objpdf->getY() - 20);
    $thisObj->objpdf->cell(33, $alturaLinhas, "Formas de Pagamento", 1, 0, "C", 1);
    $thisObj->objpdf->cell(27, $alturaLinhas, "Avaliado", 1, 0, "C", 1);
    $thisObj->objpdf->cell(26, $alturaLinhas, "Aliquota", 1, 0, "C", 1);
    $thisObj->objpdf->cell(27, $alturaLinhas, "Imposto", 1, 1, "C", 1);

    $nTotalImposto = 0;
    $nTotalAvaliado = 0;
    $lExibeMsg = false;

    if (count($thisObj->aDadosFormasPgto) > 0) {
        foreach ($thisObj->aDadosFormasPgto as $iInd => $aDadosFormas) {
            if ($iInd <= 2) {

                $thisObj->objpdf->setX($iPosicaoXFormaPgto);
                $thisObj->objpdf->SetFont('Arial', 'B', 8);
                $thisObj->objpdf->cell(33, $alturaLinhas, $aDadosFormas['Descricao'], 1, 0, "L", 1);
                $thisObj->objpdf->SetFont('Arial', '', 8);
                $thisObj->objpdf->cell(27, $alturaLinhas, db_formatar($aDadosFormas['Valor'], 'f'), 1, 0, "R", 0);
                $thisObj->objpdf->cell(26, $alturaLinhas, $aDadosFormas['Aliquota'] . (trim($aDadosFormas['Aliquota']) != "" ? "%" : ""), 1, 0, "R", 0);
                $thisObj->objpdf->cell(27, $alturaLinhas, db_formatar($aDadosFormas['Imposto'], 'f'), 1, 1, "R", 0);

                $iPosicaoYTotalFormaPgto = $thisObj->objpdf->getY();
            } else {
                $lExibeMsg = true;
            }

            $nTotalImposto += (float)$aDadosFormas['Imposto'];
            $nTotalAvaliado += (float)$aDadosFormas['Valor'];
        }
    }

    if ($lExibeMsg) {
        $thisObj->objpdf->SetFont('Arial', 'B', 8);
        $thisObj->objpdf->setY($y);
        $thisObj->objpdf->cell(71, $alturaLinhas, "* Existem mais formas de pagamento para esse ITBI", "T", 0, "L", 0);
        $thisObj->objpdf->setXY($iPosicaoXFormaPgto, $iPosicaoYTotalFormaPgto);
    } else {
        $thisObj->objpdf->setX($iPosicaoXFormaPgto);
    }
    $thisObj->objpdf->SetFont('Arial', 'B', 8);
    $thisObj->objpdf->cell(33, $alturaLinhas, "Total", 1, 0, "L", 1);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(27, $alturaLinhas, db_formatar($nTotalAvaliado, 'f'), 1, 0, "R", 0);
    $thisObj->objpdf->cell(26, $alturaLinhas, "", 1, 0, "R", 0);
    $thisObj->objpdf->cell(27, $alturaLinhas, db_formatar($nTotalImposto, 'f'), 1, 1, "R", 0);
}

function alertaVerso($thisObj, $y, $alturaLinhas)
{
    $thisObj->objpdf->sety($y + 230);
    $thisObj->objpdf->SetFont('Arial', '', 10);
    $thisObj->objpdf->cell(200, $alturaLinhas, 'VIDE VERSO', 0, 0, "R", 0);
}

function guiaCaixa($thisObj, $y, $alturaLinhas)
{
    $thisObj->objpdf->sety($y + 230);
    $thisObj->objpdf->SetFont('Arial', '', 10);
    $thisObj->objpdf->Line(0, $y + 235, 289, $y + 235);
    quadroValoresTotais($thisObj, $y + 237, $alturaLinhas, 'Guia do Caixa');
    linhaDigitavel($thisObj, $y + 244, $alturaLinhas);
    codigoDeBarras($thisObj, $y + 244);
}

function quadroValoresTotais($thisObj, $y, $alturaLinhas, $descricao = '')
{
    $thisObj->objpdf->sety($y);
    $thisObj->objpdf->SetFont('Arial', 'B', 10);
    $thisObj->objpdf->cell(65, $alturaLinhas + 2, 'Valor a Pagar: R$ ' . db_formatar(($thisObj->it14_valorpaga + $thisObj->tx_banc), 'f'), 1, 0, "L", 0);
    $thisObj->objpdf->cell(66, $alturaLinhas + 2, 'Código Arrecadação:  ' . $thisObj->numpreitbi, 1, 0, "L", 0);
    $thisObj->objpdf->cell(65, $alturaLinhas + 2, 'Vencimento:  ' . db_formatar($thisObj->datavencimento, 'd'), 1, 1, "L", 0);
    $thisObj->objpdf->setx(150);
    $thisObj->objpdf->cell(55, $alturaLinhas + 2, $descricao, 0, 1, "C", 0);
}

function mensagemRecibo($thisObj, $y, $alturaLinhas)
{
    if (isset($thisObj->tipoguia)) {
        if ( $thisObj->tipoguia == "n" ) {
            $thisObj->objpdf->sety($y);
            $thisObj->objpdf->SetFont('Arial','',10);
            $thisObj->objpdf->multiCell(140, $alturaLinhas, $thisObj->sMensagemRecibo, 0, "L", 0);
        }
    }
}

function linhaDigitavel($thisObj, $y, $alturaLinhas)
{
    $thisObj->objpdf->sety($y);
    $thisObj->objpdf->SetFont('Arial', 'B', 12);
    $thisObj->objpdf->cell(140, $alturaLinhas + 3, $thisObj->linha_digitavel, 1, 1, "C", 0);
}

function codigoDeBarras($thisObj, $y)
{
    $thisObj->objpdf->sety($y);
    $thisObj->objpdf->SetFillColor(000);
    $thisObj->objpdf->int25(10, $y + 9, $thisObj->codigo_barras, 15, 0.421);
}

function qrCodePix($thisObj, $y)
{
    $pixRepository = new RecibobarpixRepository();
    $pix = $pixRepository->getByCodBar($thisObj->codigobarras);

    if ($pix) {
        $imagemPng = "tmp/pix_arrecadacao_{$thisObj->numpre}" . time() . ".png";
        $url = $pix->k00_qrcode;
        \PHPQRCode\QRcode::png($url, $imagemPng, 'L', 4, 2);
        $thisObj->objpdf->rect(164, $y, 29, 29, 'D');
        $thisObj->objpdf->Image($imagemPng, 164, $y, 29);
    }
}

function cabecalhoGuiaVerso($thisObj)
{
    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', 'B', 10);
    $thisObj->objpdf->cell(100, 3, 'Verso: Guia de Recolhimento N' . chr(176) . ' SMF/' . db_formatar($thisObj->itbi, 's', '0', 5) . '/' . $thisObj->ano, 0, 0, "L", 0);
    $thisObj->objpdf->cell(100, 3, $thisObj->tipoitbi == "urbano" ? "ITBI URBANO" : 'ITBI RURAL', 0, 1, "L", 0);

    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(100, 3, 'Tipo de Transmissão: ' . $thisObj->it04_descr, 0, 0, "L", 0);
    $thisObj->objpdf->cell(50, 3, 'Código de Arrecadação: ' . $thisObj->numpreitbi, 0, 1, "L", 0);

    $thisObj->objpdf->setx(30);
    $thisObj->objpdf->SetFont('Arial', '', 8);
    $thisObj->objpdf->cell(100, 3, 'Processo: ' . $thisObj->processo, 0, 0, "L", 0);
    $thisObj->objpdf->cell(50, 3, 'Recibo Emitido em: ' . db_formatar($thisObj->dataemissao, 'd'), 0, 1, "L", 0);
}

function quadroDemaisTransmitentes($thisObj, $alturaLinhas, $y)
{
    if (count($thisObj->arrayTransmitentes) > 1) {
        $thisObj->objpdf->sety($y);
        $thisObj->objpdf->setx(90);
        $thisObj->objpdf->SetFont('Arial', 'B', 10);
        $thisObj->objpdf->cell(35, 5, 'Demais Transmitentes', 0, 1, "C", 0);

        $thisObj->objpdf->SetFillColor(235);
        $thisObj->objpdf->SetFont('Arial', 'B', 8);
        $thisObj->objpdf->cell(196, $alturaLinhas, 'Identificação do Transmitente', 1, 1, "C", 1);

        for ($ii = 1; $ii <= 12; $ii++) {
            if (array_key_exists($ii, $thisObj->arrayTransmitentes)) {
                $thisObj->objpdf->SetFont('Arial', 'B', 8);
                $thisObj->objpdf->cell(15, $alturaLinhas, 'NOME:', 1, 0, "L", 1);
                $thisObj->objpdf->SetFont('Arial', '', 8);
                $thisObj->objpdf->cell(83, $alturaLinhas, $thisObj->arrayTransmitentes[$ii]->z01_nome, 1, 0, "L", 0);
                $thisObj->objpdf->SetFont('Arial', 'B', 8);
                $thisObj->objpdf->cell(19, $alturaLinhas, 'CNPJ/CPF:', 1, 0, "L", 1);
                $thisObj->objpdf->SetFont('Arial', '', 8);
                $thisObj->objpdf->cell(79, $alturaLinhas, db_cgccpf($thisObj->arrayTransmitentes[$ii]->z01_cgccpf), 1, 1, "L", 0);
            }
        }
    }
}

function quadroDemaisAdquirentes($thisObj, $alturaLinhas, $y)
{
    if (count($thisObj->arrayAdquirentes) > 1) {
        $thisObj->objpdf->sety($y);
        $thisObj->objpdf->setx(90);
        $thisObj->objpdf->SetFont('Arial', 'B', 10);
        $thisObj->objpdf->cell(35, 5, 'Demais Adquirentes', 0, 1, "C", 0);

        $thisObj->objpdf->SetFillColor(235);
        $thisObj->objpdf->SetFont('Arial', 'B', 8);
        $thisObj->objpdf->cell(196, $alturaLinhas, 'Identificação do Adquirente', 1, 1, "C", 1);

        for ($ii = 1; $ii <= 12; $ii++) {
            if (array_key_exists($ii, $thisObj->arrayAdquirentes)) {
                $thisObj->objpdf->SetFont('Arial', 'B', 8);
                $thisObj->objpdf->cell(15, $alturaLinhas, 'NOME:', 1, 0, "L", 1);
                $thisObj->objpdf->SetFont('Arial', '', 8);
                $thisObj->objpdf->cell(83, $alturaLinhas, $thisObj->arrayAdquirentes[$ii]->z01_nome, 1, 0, "L", 0);
                $thisObj->objpdf->SetFont('Arial', 'B', 8);
                $thisObj->objpdf->cell(19, $alturaLinhas, 'CNPJ/CPF:', 1, 0, "L", 1);
                $thisObj->objpdf->SetFont('Arial', '', 8);
                $thisObj->objpdf->cell(79, $alturaLinhas, db_cgccpf($thisObj->arrayAdquirentes[$ii]->z01_cgccpf), 1, 1, "L", 0);
            }
        }
    }
}

function quadroDemaisConstrucoes($thisObj, $alturaLinhas, $y)
{
    $existemDemaisConstrucoes = (isset($thisObj->arrayit09_codigo)
        && array_key_exists(9, $thisObj->arrayit09_codigo));

    if ($existemDemaisConstrucoes) {
        $thisObj->objpdf->sety($y);
        $thisObj->objpdf->setx(90);
        $thisObj->objpdf->SetFont('Arial', 'B', 10);
        $thisObj->objpdf->cell(35, 5, 'Demais Construções', 0, 1, "C", 0);

        $thisObj->objpdf->SetFillColor(235);
        $thisObj->objpdf->SetFont('Arial', 'B', 8);
        $thisObj->objpdf->cell(196, $alturaLinhas, 'Identificação das Construções', 1, 1, "C", 1);

        $thisObj->objpdf->SetFont('Arial', 'B', 8);
        $thisObj->objpdf->cell(40, $alturaLinhas, 'Descrição', 1, 0, "L", 1);
        $thisObj->objpdf->cell(39, $alturaLinhas, 'Tipo', 1, 0, "L", 1);
        $thisObj->objpdf->cell(39, $alturaLinhas, 'Área m²', 1, 0, "L", 1);
        $thisObj->objpdf->cell(39, $alturaLinhas, 'Área trans m²', 1, 0, "L", 1);
        $thisObj->objpdf->cell(39, $alturaLinhas, 'Ano', 1, 1, "L", 1);

        for ($n = 9; $n <= 21; $n++) {
            $thisObj->objpdf->SetFont('Arial', '', 8);
            if (isset($thisObj->arrayit09_codigo) && array_key_exists($n, $thisObj->arrayit09_codigo)) {
                $thisObj->objpdf->cell(40, $alturaLinhas, (strlen($thisObj->arrayit09_codigo[$n]) > 12 ? substr($thisObj->arrayit09_codigo[$n], 0, 12) . "..." : $thisObj->arrayit09_codigo[$n]), 1, 0, "L", 0);
            }

            if (isset($thisObj->arrayit10_codigo) && array_key_exists($n, $thisObj->arrayit10_codigo)) {
                $thisObj->objpdf->cell(39, $alturaLinhas, substr($thisObj->arrayit10_codigo[$n], 0, 20), 1, 0, "L", 0);
            }

            if (isset($thisObj->arrayit08_area) && array_key_exists($n, $thisObj->arrayit08_area)) {
                $thisObj->objpdf->cell(39, $alturaLinhas, db_formatar($thisObj->arrayit08_area[$n], 'f', ' ', ' ', ' ', 5), 1, 0, "R", 0);
            }

            if (isset($thisObj->arrayit08_areatrans) && array_key_exists($n, $thisObj->arrayit08_areatrans)) {
                $thisObj->objpdf->cell(39, $alturaLinhas, db_formatar($thisObj->arrayit08_areatrans[$n], 'f', ' ', ' ', ' ', 5), 1, 0, "R", 0);
            }

            if (isset($thisObj->arrayit08_ano) && array_key_exists($n, $thisObj->arrayit08_ano)) {
                $thisObj->objpdf->cell(39, $alturaLinhas, $thisObj->arrayit08_ano[$n], 1, 1, "C", 0);
            }
        }
    }
}

function isValidArray($value, $lengthEqual = null, $lengthEqualOrGreatherThan = null)
{
    $isValid = isset($value) && is_array($value);

    if ($lengthEqual) {
        $isValid = $isValid && count($value) == $lengthEqual;
    }

    if ($lengthEqualOrGreatherThan) {
        $isValid = $isValid && count($value) >= $lengthEqualOrGreatherThan;
    }

    return $isValid;
}

function isValidString($value, $lengthEqual = null, $lengthEqualOrGreatherThan = null)
{
    $isValid = isset($value) && is_string($value);

    if ($lengthEqual) {
        $isValid = $isValid && strlen($value) == $lengthEqual;
    }

    if ($lengthEqualOrGreatherThan) {
        $isValid = $isValid && strlen($value) >= $lengthEqualOrGreatherThan;
    }

    return $isValid;
}
