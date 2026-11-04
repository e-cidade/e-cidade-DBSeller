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

use ECidade\RecursosHumanos\ESocial\Entity\Remuneracao;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorOutrosVinculos;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorOutrosVinculosService;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorOperadoraSaudeService;
use ECidade\RecursosHumanos\Pessoal\Service\ServidorProcessosJudiciaisFolhaService;
use ECidade\RecursosHumanos\ESocial\Service\TrabalhoIntermitenteService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeRepository;
use ServidorRepository;
use Servidor;
use stdClass;
use CgmBase;
use CalculoFolha;
use EventoFinanceiroFolha;
use DBCompetencia;
use DBPessoal;
use FolhaPagamento;

/**
 * Class RemuneracaoSService
 * @package ECidade\RecursosHumanos\ESocial\Service
 */
class RemuneracaoBeneficioService
{
    private $remuneracao;

    private $remuneracoes = [];

    const SALARIO = "SALARIO";
    const COMPLEMENTAR = "COMPLEMENTAR";
    const DECIMO = "DECIMO";
    const RESCISAO = "RESCISAO";
    const RESCISAOPOSTERIOR = "RESCISAOPOSTERIOR";

    const TIPOSALARIO = 0;
    const TIPOCOMPLEMENTAR = 1;
    const TIPODECIMO = 2;
    const TIPORESCISAO = 3;
    const TIPORESCISAOPOSTERIOR = 4;

    /**
     * @var int
     */
    private $anoCompetencia;

    /**
     * @var int
     */
    private $mesCompetencia;

    public function __construct($ano = '', $mes = '')
    {
        if (empty($ano)) {
            $this->anoCompetencia = DBPessoal::getAnoFolha();
        } else {
            $this->anoCompetencia = $ano;
        }

        if (empty($mes)) {
            $this->mesCompetencia = DBPessoal::getMesFolha();
        } else {
            $this->mesCompetencia = $mes;
        }
    }

    /**
     * @param CgmBase $cgm
     * @return Remuneracao
     * @throws \BusinessException
     * @throws \DBException
     */
    public function buscarPorCGM(CgmBase $cgm)
    {
        $servidores = ServidorRepository::getServidoresByCgm(
            $cgm,
            new DBCompetencia($this->anoCompetencia, $this->mesCompetencia)
        );
        $qtdServidores = 0;

        foreach ($servidores as $servidor) {
            if ($servidor->isAtivo()) {
                continue;
            }
            $matricula = $servidor->getMatricula();
            $this->remuneracao = new Remuneracao();
            $this->remuneracao->setServidor($servidor);
            $this->buscarOutrosVinculos($matricula);
            $this->buscarDadosTrabalhador($servidores[0]);
            $this->buscarProcessosJudiciais($matricula);
            $this->buscarPagamento($servidor);
            $possuiRemuneracao = false;
            foreach ($this->remuneracao->getPagamentos() as $pagamento) {
                $remuneracao = clone $this->remuneracao;
                $remuneracao->setPagamentos([$pagamento]);
                $this->remuneracoes[] = $remuneracao;
                $possuiRemuneracao = true;
            }
            if ($possuiRemuneracao) {
                $qtdServidores += 1;
            }
        }
        if (sizeof($this->remuneracoes) > 0) {
            foreach ($this->remuneracoes as &$remuneracao) {
                $remuneracao->qtdServidores = $qtdServidores;
            }
        }

        return $this->remuneracoes;
    }

    /**
     * @param $matricula
     * @throws \Exception
     */
    private function buscarOutrosVinculos($matricula)
    {

        $parametros = new stdClass();
        $parametros->matricula = $matricula;

        $serviceOutrosVinculos = new ServidorOutrosVinculosService();
        $serviceOutrosVinculos->setAnoCompetencia($this->anoCompetencia);
        $serviceOutrosVinculos->setMesCompetencia($this->mesCompetencia);
        $servidorOutrosVinculos = $serviceOutrosVinculos->buscarOutrosVinculosPorMatriculaCompetencia($parametros);
        $this->remuneracao->setServidorOutrosVinculos($servidorOutrosVinculos);
    }

    /**
     * @param Servidor $servidor
     * @throws \BusinessException
     * @throws \DBException
     */
    private function buscarPagamento(Servidor $servidor)
    {
        $calculoFinanceiroSalario = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_SALARIO);
        $calculoFinanceiroComplementar = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_COMPLEMENTAR);
        $calculoFinanceiroDecimoTerceiro = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_13o);
        $calculoFinanceiroRescisao = $servidor->getCalculoFinanceiro(CalculoFolha::CALCULO_RESCISAO);
        /**
         * Índice:
         * 0 - CalculoFolha::CALCULO_SALARIO
         * 1 - CalculoFolha::CALCULO_COMPLEMENTAR
         * 2 - CalculoFolha::CALCULO_13
         * 3 - CalculoFolha::CALCULO_RESCISAO
         */
        $pagamentos = array();

        if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
            $calculoComplementar = $calculoFinanceiroComplementar->getEventosFinanceirosHistorico(
                FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR
            );
        } else {
            $calculoComplementar = $calculoFinanceiroComplementar->getEventosFinanceiros();
        }

        foreach ($calculoFinanceiroSalario->getEventosFinanceiros() as $eventoFinanceiro) {
            $pagamentos[self::TIPOSALARIO][] = $this->geraEventoESocial($eventoFinanceiro, self::SALARIO);
        }

        foreach ($calculoComplementar as $eventoFinanceiro) {
            $pagamentos[self::TIPOCOMPLEMENTAR][] = $this->geraEventoESocial($eventoFinanceiro, self::COMPLEMENTAR);
        }

        foreach ($calculoFinanceiroDecimoTerceiro->getEventosFinanceiros() as $eventoFinanceiro) {
            $pagamentos[self::TIPODECIMO][] = $this->geraEventoESocial($eventoFinanceiro, self::DECIMO, true);
        }
        $competenciasRescisao = $servidor->getCompetenciasPagamentosRescisao();
        if ($servidor->isRescindido() || sizeof($competenciasRescisao) > 0) {
            if (sizeof($competenciasRescisao) > 1) {
                if ($servidor->getAnoCompetencia() != $competenciasRescisao[0]->anousu
                    || $servidor->getMesCompetencia() != $competenciasRescisao[0]->mesusu) {
                    foreach ($calculoFinanceiroRescisao->getEventosFinanceiros() as $eventoFinanceiro) {
                        $pagamentos[self::TIPORESCISAOPOSTERIOR][] = $this->geraEventoESocial(
                            $eventoFinanceiro,
                            self::RESCISAOPOSTERIOR,
                            false,
                            true
                        );
                    }
                } else {
                    foreach ($calculoFinanceiroRescisao->getEventosFinanceiros() as $eventoFinanceiro) {
                        $pagamentos[self::TIPORESCISAO][] = $this->geraEventoESocial($eventoFinanceiro, self::RESCISAO);
                    }
                }
            } else {
                foreach ($calculoFinanceiroRescisao->getEventosFinanceiros() as $eventoFinanceiro) {
                    $pagamentos[self::TIPORESCISAO][] = $this->geraEventoESocial($eventoFinanceiro, self::RESCISAO);
                }
            }
        }
        $this->remuneracao->setPagamentos($pagamentos);
    }

    private function geraEventoESocial($evento, $tipoFolha, $decimo = false, $periodoanterior = false)
    {
        $rubrica = new stdClass();
        $rubrica->codigo = $evento->getRubrica()->getCodigo();
        $rubrica->descricao = $evento->getRubrica()->getDescricao();
        $rubrica->quantidade = $evento->getQuantidade();
        $rubrica->valor = $evento->getValor();
        $rubrica->tipo = $evento->getRubrica()->getTipo();
        $rubrica->decimoTerceiro = $decimo;
        $rubrica->descricaoTipo = $evento->getRubrica()->getTipo() == EventoFinanceiroFolha::PROVENTO
            ? "Provento" : "Desconto";
        $rubrica->nomePagamento = $tipoFolha;
        $rubrica->periodoAnterior = $periodoanterior;
        return $rubrica;
    }

    /**
     * @param int $anoCompetencia
     */
    public function setAnoCompetencia($anoCompetencia)
    {
        $this->anoCompetencia = $anoCompetencia;
    }

    /**
     * @param int $mesCompetencia
     */
    public function setMesCompetencia($mesCompetencia)
    {
        $this->mesCompetencia = $mesCompetencia;
    }

    public function getAnoCompetencia()
    {
        return $this->anoCompetencia;
    }

    public function getMesCompetencia()
    {
        return $this->mesCompetencia;
    }

    /**
     * @param Servidor $servidor
     */
    private function buscarDadosTrabalhador(Servidor $servidor)
    {
        $cgm = $servidor->getCgm();
        $dadosTrabalhador = new stdClass();
        $dadosTrabalhador->nome = $cgm->getNomeCompleto();
        $dadosTrabalhador->cpf = $cgm->getCpf();
        $dadosTrabalhador->nis = $cgm->getPIS();
        $dadosTrabalhador->nascimento = $cgm->getDataNascimento();

        $this->remuneracao->setDadosTrabalhador($dadosTrabalhador);
    }

    /**
     * @param int $matricula
     * @throws \Exception
     */
    private function buscarProcessosJudiciais($matricula)
    {
        $parametros = new stdClass();
        $parametros->matricula = $matricula;

        $processosJudiciaisService = new ServidorProcessosJudiciaisFolhaService();
        $processosJudiciaisService->setAnoCompetencia($this->anoCompetencia);
        $processosJudiciaisService->setMesCompetencia($this->mesCompetencia);
        $processosJudiciais = $processosJudiciaisService->buscarProcessosJudiciaisPorMatriculaCompetencia($parametros);
        $this->remuneracao->setProcessosJudiciais($processosJudiciais);
    }
}
