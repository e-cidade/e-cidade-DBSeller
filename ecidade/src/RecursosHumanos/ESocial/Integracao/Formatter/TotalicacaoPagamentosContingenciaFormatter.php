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

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

class TotalicacaoPagamentosContingenciaFormatter extends Formatter
{
    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($dados)
    {
        /**
         * @todo implementar pos informando o perioodo de apuracao
         */
        $dadosFormatado = parent::formatar($dados);
        return $this->posProcessamento($dadosFormatado, $dados);
    }

    private function posProcessamento(array $dadosFormatado, array $dados)
    {
        foreach ($dadosFormatado as $dadoFormatado) {
            $this->unsetEmpty($dadoFormatado);
            foreach ($dados as $dado) {
                if ($dadoFormatado->referencia == $dado->responsavel) {
                    $dadoFormatado->indApuracao = (int) $dado->dadosPreenchimento->indicativo_apuracao;
                    $dadoFormatado->perApur = $dado->dadosPreenchimento->periodo_apuracao;
                }
            }
        }

        return $dadosFormatado;
    }
}
