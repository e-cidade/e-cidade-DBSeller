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

/**
 * Class EFDServicosTomadosFormatter
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class EFDServicosTomadosFormatter extends Formatter
{

    /**
     * Realiza a formatação dos dados para envio da API
     * @param array $dados
     * @return array
     */
    public function formatar($dados)
    {
        $dadosFormatado = parent::formatar($dados);
        return $this->posProcessamento($dadosFormatado);
    }

    private function posProcessamento($dadosFormatado)
    {
        $this->unsetEmpty($dadosFormatado);
        $novosDados = new \stdClass();
        $novosDados->infoProcRetPr = array();
        $novosDados->infoProcRetAd = array();
        foreach ($dadosFormatado as $stdLinha) {
            foreach ($stdLinha['infoProcRet'] as $infoProcRet) {
                if ($infoProcRet['tipo'] == 1) {
                    $infoProcRetPr = new \stdClass();
                    $infoProcRetPr->tpProcRetPrinc = $infoProcRet['tpProcRet'];
                    $infoProcRetPr->nrProcRetPrinc = $infoProcRet['nrProcRet'];
                    $infoProcRetPr->valorPrinc = $infoProcRet['valor'];
                    $infoProcRetPr->codSuspPrinc = !empty($infoProcRet['codSusp']) ? $infoProcRet['codSusp'] : null;
                    $novosDados->infoProcRetPr[] = $infoProcRetPr;
                } else {
                    $infoProcRetAd = new \stdClass();
                    $infoProcRetAd->tpProcRetAdic = $infoProcRet['tpProcRet'];
                    $infoProcRetAd->nrProcRetAdic = $infoProcRet['nrProcRet'];
                    $infoProcRetAd->valorAdic = $infoProcRet['valor'];
                    $infoProcRetAd->codSuspAdic = !empty($infoProcRet['codSusp']) ? $infoProcRet['codSusp'] : null;
                    $novosDados->infoProcRetAd[] = $infoProcRetAd;
                }
            }
        }
        if (count($novosDados->infoProcRetPr) == 0) {
            unset($novosDados->infoProcRetPr);
        }
        if (count($novosDados->infoProcRetAd) == 0) {
            unset($novosDados->infoProcRetAd);
        }

        return $novosDados;
    }
}
