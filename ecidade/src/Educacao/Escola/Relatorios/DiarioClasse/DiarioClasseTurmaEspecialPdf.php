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

namespace ECidade\Educacao\Escola\Relatorios\DiarioClasse;

use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\DadosDiarioClasse;

/**
 * Class DiarioClasseTurmaEspecialPdf
 * @package ECidade\Educacao\Escola\Relatorios\DiarioClasse
 */
class DiarioClasseTurmaEspecialPdf extends DiarioClassePdf
{

    protected function cabecalho(DadosDiarioClasse $dadosDiarioClasse, $request = null)
    {
        $this->cabecalhoDadosInstituicao($dadosDiarioClasse);
        $calendario = $dadosDiarioClasse->getCalendario();

        $posicaoX = 140;
        $tamanhoLinha = 90;

        $this->SetXY($posicaoX, 8);

        $textoAtividadeComplementar = "Turma de Atividade Educacional Especial";
        if (!is_null($dadosDiarioClasse->getAtividadeComplementar())) {
            $atividadeComplementar = $dadosDiarioClasse->getAtividadeComplementar()->getDescricao();
            $textoAtividadeComplementar = "Atividade Complementar: {$atividadeComplementar}";
        }

        $this->Cell($tamanhoLinha, 4, $textoAtividadeComplementar, 0, 0, "L");
        $this->Cell($tamanhoLinha, 4, "Calendário: {$calendario->getDescricao()}", 0, 1, "L");

        $this->SetX($posicaoX);
        $this->Cell($tamanhoLinha, 4, "Turma: {$dadosDiarioClasse->getTurma()->getNome()}", 0, 0, "L");
        $this->Cell($tamanhoLinha, 4, "Turno: {$dadosDiarioClasse->getTurno()->getDescricao()}", 0, 1, "L");

        $this->SetX($posicaoX);
        $this->Cell($tamanhoLinha, 4, "Profissional: {$dadosDiarioClasse->getNomeRegente()}", 0, 0, "L");

        $this->roundedrect(8, 8, 280, 20, 2, '', '1234');
        $this->SetXY(8, 30);
    }
}
