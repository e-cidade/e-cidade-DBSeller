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
namespace ECidade\Educacao\Escola\Censo\Log;

use FpdfMultiCellBorder;
use JSON;

class LogMatriculaInicial extends Log
{
    const REGISTRO00 = "00";
    const REGISTRO10 = "10";
    const REGISTRO20 = "20";
    const REGISTRO30 = "30";
    const REGISTRO40 = "40";
    const REGISTRO50 = "50";
    const REGISTRO60 = "60";

    public function add($type, $log)
    {
        $this->logs[$type][] = $log;
    }

    public function getLogs()
    {
        return $this->logs;
    }

    public function write()
    {
        $pdf = new FpdfMultiCellBorder();
        $pdf->exibeHeader(true);
        $pdf->Open();
        $pdf->AliasNbPages();
        $pdf->SetFillColor(235);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();
        $pdf->SetFont("arial", "b", 7);

        foreach ($this->logs as $index => $inconsistencias) {
            $pdf->SetFont("arial", "B", 8);
            $pdf->Cell(183, 4, "Registro {$index}", "B", 1);
            $pdf->SetFont("arial", "", 7);
            $fill = true;
            foreach ($inconsistencias as $inconsistencia) {
                $pdf->MultiCell(183, 4, $inconsistencia, 1, '', $fill);
                $fill = !$fill;
            }
            $pdf->ln();
        }

        $filePath = "tmp/log_censo" . time() . ".pdf";
        $pdf->Output($filePath, false, true);

        return $filePath;
    }
}
