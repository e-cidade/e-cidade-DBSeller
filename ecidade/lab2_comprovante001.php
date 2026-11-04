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

use ECidade\Saude\Shared\MulticellPdfService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once modification("libs/db_utils.php");
require_once(modification("dbforms/db_funcoes.php"));

function imprimir(\ECidade\Pdf\Pdf $pdf, $dadosRequisicao)
{
    $pdf->SetFont('arial', '', 8);
    cabecalho($pdf, $dadosRequisicao);
    $yAntesImprimirExames = $pdf->GetY();
    cabecalhoExames($pdf, $dadosRequisicao);
    foreach ($dadosRequisicao->exames as $exame) {
        if ($pdf->GetY() >= ($pdf->getH() - 15)) {
            $pdf->addPage();
            cabecalho($pdf, $dadosRequisicao);
            cabecalhoExames($pdf, $dadosRequisicao);
        }

        $dadosCelulas = [
            'setorDescricao' => ['largura' => 45, 'texto' => $exame->setor],
            'exameDescricao' => ['largura' => 80, 'texto' => $exame->exame],
            'exameQuantidade' => ['largura' => 18, 'texto' => $exame->quantidade],
            'dataColeta' => ['largura' => 18, 'texto' => $exame->data_coleta],
            'horaColeta' => ['largura' => 13, 'texto' => $exame->hora_coleta],
            'dataEntrega' => ['largura' => 18, 'texto' => $exame->data_entrega],
        ];

        $service = new MulticellPdfService($pdf, $dadosCelulas, 4);

        $x1 = $pdf->getX();
        $y1 = $pdf->getY();
        $pdf->multiCell(45, $service->validaAlturaLinha('setorDescricao'), $exame->setor, '', 'L');
        $pdf->setXY($x1 + 45, $y1);

        $x1 = $pdf->getX();
        $y1 = $pdf->getY();
        $pdf->multiCell(80, $service->validaAlturaLinha('exameDescricao'), $exame->exame);
        $pdf->setXY($x1 + 80, $y1);

        $x1 = $pdf->getX();
        $y1 = $pdf->getY();
        $pdf->Cell(18, $service->validaAlturaLinha('exameQuantidade'), $exame->quantidade, 0, 0, 'C');
        $pdf->setXY($x1 + 18, $y1);

        if ($dadosRequisicao->modeloRequisicao == 1) {
            $pdf->ln();

        } else {
            $x1 = $pdf->getX();
            $y1 = $pdf->getY();
            $pdf->Cell(
                18,
                $service->validaAlturaLinha('dataColeta'),
                $exame->data_coleta
            );
            $pdf->setXY($x1 + 18, $y1);

            $x1 = $pdf->getX();
            $y1 = $pdf->getY();
            $pdf->Cell(
                13,
                $service->validaAlturaLinha('horaColeta'),
                $exame->hora_coleta
            );
            $pdf->setXY($x1 + 13, $y1);

            $pdf->Cell(
                18,
                $service->validaAlturaLinha('dataEntrega'),
                $exame->data_entrega,
                0,
                1
            );
        }
    }

    $observacoes = '';
    if (!empty($dadosRequisicao->requisitos)) {
        $observacoes = implode(' | ', $dadosRequisicao->requisitos);
    }

    if ($dadosRequisicao->doisPorPagina) {
        $pdf->SetY($yAntesImprimirExames + 64);
    }

    $y = $pdf->GetY();
    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(10, 4, "Observações:", 0, 1);
    $pdf->RoundedRect($pdf->GetX() - 1, $pdf->GetY(), 96, 16, 2);
    $pdf->Setfont('Arial', '', 6);
    if (!empty($dadosRequisicao->observacao)) {
        if (!empty($observacoes)) {
            $observacoes .= "\n";
        }
        $observacoes .= "{$dadosRequisicao->observacao}";
    }
    $pdf->MultiCell(86, 3, $observacoes);
    $pdf->Setfont('Arial', 'B', 6);
    $pdf->SetXY(100, $y + 10);
    $pdf->Line(120, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Cell(110, 4, $dadosRequisicao->assinatiuraModelo, 0, 1, 'C');
    $pdf->SetX(100);
    $pdf->Cell(110, 4, "{$dadosRequisicao->municipio}, {$dadosRequisicao->data_emissao}.", 0, 1, 'C');
    $pdf->ln();

    if ($dadosRequisicao->doisPorPagina && $pdf->GetY() < 150) {
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    }
}

function cabecalho(\ECidade\Pdf\Pdf $pdf, $dadosRequisicao)
{
    $logradouro = "{$dadosRequisicao->logradouro}, {$dadosRequisicao->numero}";
    $yInicio = $pdf->GetY();
    if (file_exists($dadosRequisicao->logo)) {
        $pdf->Image($dadosRequisicao->logo, 10, $yInicio, 15);
    }
    $pdf->Setfont('Arial', 'B', 9);
    $pdf->SetX(30);
    $pdf->Cell(180, 4, $dadosRequisicao->nomePrefeitura, 0, 1);
    $pdf->SetX(30);
    $pdf->Setfont('Arial', '', 9);
    $pdf->Cell(180, 4, $logradouro, 0, 1);
    $pdf->SetX(30);
    $pdf->Cell(180, 4, $dadosRequisicao->municipio, 0, 1);
    $pdf->SetX(30);
    $pdf->Cell(180, 4, $dadosRequisicao->telefone, 0, 1);
    $pdf->SetX(30);
    $pdf->Cell(180, 4, $dadosRequisicao->email, 0, 1);
    $pdf->SetXY(160, $yInicio);
    $pdf->Setfont('Arial', 'B', 10);
    $pdf->Cell(180, 4, "Requisição: {$dadosRequisicao->requisicao}", 0, 1);
    $pdf->Setfont('Arial', '', 8);

    $pdf->SetY($yInicio + 23);
    $pdf->RoundedRect($pdf->GetX() - 1, $pdf->GetY() - 1, 193, 14, 2);

    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(19, 4, "Laboratório:");
    $pdf->Setfont('Arial', '', 7);
    $pdf->Cell(160, 4, $dadosRequisicao->laboratorio, 0, 1);

    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(19, 4, "Departamento:");
    $pdf->Setfont('Arial', '', 7);
    $pdf->Cell(80, 4, "{$dadosRequisicao->departamento}", 0, 0);
    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(16, 4, "Atendente:");
    $pdf->Setfont('Arial', '', 7);
    $pdf->Cell(80, 4, "{$dadosRequisicao->atendente}", 0, 1);

    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(16, 4, "Solicitante:");
    $pdf->Setfont('Arial', '', 7);
    $pdf->Cell(80, 4, "{$dadosRequisicao->medico}", 0, 1);

    $pdf->SetY($pdf->GetY() + 3);
    $pdf->RoundedRect($pdf->GetX() - 1, $pdf->GetY() - 1, 193, 9, 2);

    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(13, 4, "Paciente:");
    $pdf->Setfont('Arial', '', 7);
    $pdf->Cell(86, 4, "{$dadosRequisicao->cgs} {$dadosRequisicao->paciente}");

    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(9, 4, "Idade:");
    $pdf->Setfont('Arial', '', 7);
    $pdf->Cell(80, 4, $dadosRequisicao->idadeCompleta, 0, 1);

    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(19, 4, "Responsável:");
    $pdf->Setfont('Arial', '', 7);
    $y = $pdf->GetY();
    $pdf->MultiCell(80, 4, "{$dadosRequisicao->responsavel}");

    $pdf->SetXY(109, $y);
    $pdf->Setfont('Arial', 'B', 7);
    $pdf->Cell(12, 4, "Contato:");
    $pdf->Setfont('Arial', '', 7);
    $pdf->MultiCell(80, 4, "{$dadosRequisicao->contato}");
    $pdf->SetY($y + 10);
}

function cabecalhoExames(\ECidade\Pdf\Pdf $pdf, $dadosRequisicao)
{
    $pdf->SetFont('arial', 'B', 7);
    $pdf->Cell(45, 4, 'SETOR');
    $pdf->Cell(80, 4, 'EXAME');
    $pdf->Cell(18, 4, 'QUANTIDADE');
    if ($dadosRequisicao->modeloRequisicao == 1) {
        $pdf->SetFont('arial', '', 7);
        $pdf->Ln();
    } else {
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(18, 4, 'COLETA');
        $pdf->Cell(13, 4, 'HORA');
        $pdf->Cell(18, 4, 'ENTREGA', 0, 1);
        $pdf->SetFont('arial', '', 7);
    }
}

$lab_parametros = new \cl_lab_parametros();

try {
    $postgresObjectParametros = db_query($lab_parametros->sql_query_file('', 'la49_modelocomprovanterequisicao'));
    $rsParametros = pg_fetch_all($postgresObjectParametros);
    $modeloComprovanteRequisicao = $rsParametros[0]['la49_modelocomprovanterequisicao'];

    $prefeitura = InstituicaoRepository::getInstituicaoPrefeitura();
    $requisicao = new RequisicaoLaboratorial($_GET['la22_i_codigo']);

    $data = DBDate::createFromTimestamp(time());
    $dadosRequisicao = new stdClass();
    $dadosRequisicao->laboratorios = [];
    foreach ($requisicao->getRequisicoesDeExames() as $exameRequisitado) {
        $laboratorio = LaboratorioRepository::getLaboratorioByCodigo($exameRequisitado->getLaboratorio());
        $id = $laboratorio->getCodigo();
        if (!array_key_exists($id, $dadosRequisicao->laboratorios)) {
            $infoLaboratorio = sprintf(
                "%s, %s, %s",
                $laboratorio->getDescricao(),
                $laboratorio->getEndereco(),
                $laboratorio->getNumero()
            );
            $dadosRequisicao->laboratorios[$id] = (object)[
                'data_emissao' => $data->dataPorExtenso(),
                'nomePrefeitura' => $prefeitura->getDescricao(),
                'municipio' => $prefeitura->getMunicipio(),
                'logradouro' => $prefeitura->getLogradouro(),
                'numero' => $prefeitura->getNumero(),
                'telefone' => $prefeitura->getTelefone(),
                'email' => $prefeitura->getEmail(),
                'logo' => 'imagens/files/logo_boleto.png',
                'requisicao' => $_GET['la22_i_codigo'],
                'medico' => $requisicao->getMedico(),
                'paciente' => $requisicao->getCgs()->getNome(),
                'cgs' => $requisicao->getCgs()->getCodigo(),
                'laboratorio' => $infoLaboratorio,
                'departamento' => $requisicao->getDepartamento()->getNomeDepartamento(),
                'atendente' => $requisicao->getUsuario()->getNome(),
                'responsavel' => $requisicao->getResponsavel(),
                'contato' => $requisicao->getContato(),
                'observacao' => $requisicao->getObservacao(),
                'modeloRequisicao' => $modeloComprovanteRequisicao,
                'assinatiuraModelo' => $modeloComprovanteRequisicao == 0 ? 'RECEBEDOR' : 'AUTORIZADOR',
                'exames' => [],
                'idadeCompleta' => $requisicao->getCgs()->getIdadeCompleta()
            ];
        }
        $setor = $exameRequisitado->getLaboratorioSetor();
        $dadosRequisicao->laboratorios[$id]->exames[] = (object)array(
            'setor' => $setor->getDescricao(),
            'exame' => $exameRequisitado->getExame()->getNome(),
            'quantidade' => $exameRequisitado->getQuantidade(),
            'data_coleta' => $exameRequisitado->getData()->convertTo(DBDate::DATA_PTBR),
            'hora_coleta' => $exameRequisitado->getHoraColeta(),
            'data_entrega' => $exameRequisitado->getDataEntrega()->convertTo(DBDate::DATA_PTBR),
        );

        $sqlRequisitos = "
         select distinct la20_t_descr
           from lab_requisicao
           join lab_requiitem on lab_requiitem.la21_i_requisicao = lab_requisicao.la22_i_codigo
           join lab_setorexame on lab_setorexame.la09_i_codigo = lab_requiitem.la21_i_setorexame
           join lab_exame on lab_exame.la08_i_codigo = lab_setorexame.la09_i_exame
           join lab_examerequisito on lab_examerequisito.la20_i_exame = lab_exame.la08_i_codigo
          where la22_i_codigo = {$requisicao->getCodigo()}
        ";
        $rs = db_query($sqlRequisitos);
        $dadosRequisicao->laboratorios[$id]->requisitos = array();
        if (!$rs) {
            throw new Exception("Erro ao buscar requisitos para executar o exâme.");
        }

        if (pg_num_rows($rs) > 0) {
            $dadosRequisicao->laboratorios[$id]->requisitos = pg_fetch_all_columns($rs);
        }
    }
} catch (Exception $e) {
    $sMsg = urlencode($e->getMessage());
    db_redireciona('db_erros.php?fechar=true&db_erro='.$sMsg);
}

$pdf = new ECidade\Pdf\Pdf();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(false, 15);

foreach ($dadosRequisicao->laboratorios as $laboratorio) {
    $pdf->AddPage();
    $laboratorio->doisPorPagina = count($laboratorio->exames) < 15;

    imprimir($pdf, $laboratorio);
    if ($laboratorio->doisPorPagina) {
        $pdf->SetY(148.5);
    } else {
        $pdf->AddPage();
    }
    imprimir($pdf, $laboratorio);
}

$pdf->Output();
