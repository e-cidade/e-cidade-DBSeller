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

namespace App\Domain\Educacao\Escola\Services;

class CriterioTransposicaoService
{
    const DEFICIENCIA = 1;
    const BOLSA_FAMILIA = 2;
    const IRMAO_NA_ESCOLA = 3;
    const IDADE_MAIOR = 4; // Idade é critério de desempate
    const SERVIDOR_PUBLICO = 5;

    /**
     * @param $inscrito
     * @param array $criteriosEtapa
     * @return int
     */
    public function calcularPontuacao($inscrito, $criteriosEtapa)
    {
        $pontuacao = 0;
        foreach ($criteriosEtapa as $key => $criterioEtapa) {
            switch ($key) {
                case CriterioTransposicaoService::DEFICIENCIA:
                    $pontuacao += $this->getPontuacaoDeficiencia($inscrito, $criterioEtapa);
                    break;
                case CriterioTransposicaoService::BOLSA_FAMILIA:
                    $pontuacao += $this->getPontuacaoBolsaFamilia($inscrito, $criterioEtapa);
                    break;
                case CriterioTransposicaoService::IRMAO_NA_ESCOLA:
                    $pontuacao += $this->getPontuacaoIrmaoEscola($inscrito, $criterioEtapa);
                    break;
                case CriterioTransposicaoService::SERVIDOR_PUBLICO:
                    $pontuacao += $this->getPontuacaoServidorPublico($inscrito, $criterioEtapa);
                    break;
            }
        }
        return $pontuacao;
    }

    public function getPontuacaoDeficiencia($inscrito, $criterioEtapa)
    {
        return $inscrito->aluno_necessidadeespecial ? $criterioEtapa['pesoCriterio'] : 0;
    }

    private function getPontuacaoBolsaFamilia($inscrito, $criterioEtapa)
    {
        return $inscrito->bolsafamilia ? $criterioEtapa['pesoCriterio'] : 0;
    }

    private function getPontuacaoIrmaoEscola($inscrito, $criterioEtapa)
    {
        return $inscrito->irmao ? $criterioEtapa['pesoCriterio'] : 0;
    }

    private function getPontuacaoServidorPublico($inscrito, $criterioEtapa)
    {
        return $inscrito->servidor ? $criterioEtapa['pesoCriterio'] : 0;
    }
}
