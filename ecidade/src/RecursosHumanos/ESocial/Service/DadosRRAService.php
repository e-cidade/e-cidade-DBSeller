<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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
namespace ECidade\RecursosHumanos\ESocial\Service;

use BusinessException;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\TributoBaseRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\TributoIRRFRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\AdvogadoRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ProcessoJudicial\ServidorRepository;

/**
 * Class RemuneracaoRGPSService
 * @package ECidade\RecursosHumanos\ESocial\Service
 */
class DadosRRAService
{
    /**
     * @var int
     */
    private $anoCompetencia;

    /**
     * @var int
     */
    private $mesCompetencia;

    /**
     * @var Servidor
     */
    private $servidor;

    public static function getDados($ano, $mes, $servidor)
    {
        $retorno = false;

        $processosRepository = new TributoBaseRepository();
        /**
         * buscamos o tributo de RRA pela competencia de pagamento e o servidor
         */
        $tributosBase = $processosRepository->scopeCompetenciaPagamento($ano, $mes)
            ->scopeMatriculaServidor($servidor->getMatricula())
            ->get();
        if (sizeof($tributosBase) > 0) {
            foreach ($tributosBase as $tributoBase) {
                $servidorRepositoryProcesso = new ServidorRepository();
                /** tendo tributo de RRA na competencia informada, buscamos o processo */
                $processosServidor = $servidorRepositoryProcesso
                    ->scopeSequencial($tributoBase->getSequencialProcessoServidor())
                    ->get();
                if (sizeof($processosServidor) > 0) {
                    $processoServidor = $processosServidor[0]->getProcessoJudicial();
                    if (sizeof($processoServidor) > 0) {
                        $processoServidor = $processoServidor[0];
                        /**
                         * $processoServidor->getOrigem() == 1 Judicial
                         * 2 = Demanda submetida à CCP ou ao NINTER = invalido
                         * //invalido no caso dos eventos de folha seria o processo Administrativo
                         * porem nao temos como relacionar matricula com esses processos atualmente (S-1070)
                         */
                        if ($processoServidor->getOrigem() == 1) {
                            $tributoIRRFRepository = new TributoIRRFRepository();
                            $tributosIRRF = $tributoIRRFRepository
                                ->scopeSequencialServidor($tributoBase->getSequencialProcessoServidor())
                                ->get();
                            foreach ($tributosIRRF as $tributoIRRF) {
                                $dados = new \stdClass();

                                // Informações complementares relativas a Rendimentos Recebidos Acumuladamente - RRA.
                                $dados->infoRRA = new \stdClass();
                                $dados->infoRRA->tpProcRRA = 2;
                                $dados->infoRRA->nrProcRRA = $processoServidor->getNumeroProcesso();
                                $dados->infoRRA->descRRA = $tributoIRRF->getDescricaoRendimentoAcumula();
                                $dados->infoRRA->qtdMesesRRA = (int) $tributoIRRF->getQuantidadeMesAcumula();
                                // Detalhamento das despesas com processo judicial.
                                $dados->infoRRA->despProcJud = new \stdClass();
                                $dados->infoRRA->despProcJud->vlrDespCustas =
                                    (double) $tributoIRRF->getValorDespesaCusta();
                                $dados->infoRRA->despProcJud->vlrDespAdvogados =
                                    (double) $tributoIRRF->getValorDespesaAdvogados();
                
                                // Identificação dos advogados.
                                $advogadoRepository = new AdvogadoRepository();
                                $advogados = $advogadoRepository
                                    ->scopeSequencialTributoIRRF($tributoIRRF->getSequencial())
                                    ->get();
                                if (sizeof($advogados) > 0) {
                                    $dados->infoRRA->ideAdv = [];
                                }
                                foreach ($advogados as $advogado) {
                                    $adv = new \stdClass();
                                    $adv->tpInsc = (int) $advogado->getTipoInscricao();
                                    $adv->nrInsc = $advogado->getNumeroInscricao();
                                    $adv->vlrAdv = (double) $advogado->getValorDespesa();
                                    $dados->infoRRA->ideAdv[] = $adv;
                                }
                                $retorno = $dados;
                            }
                        }
                    }
                }
            }
        }
        return $retorno;
    }
}
