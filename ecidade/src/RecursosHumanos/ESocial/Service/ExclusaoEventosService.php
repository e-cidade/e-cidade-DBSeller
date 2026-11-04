<?php

namespace ECidade\RecursosHumanos\ESocial\Service;

use Avaliacao;
use cl_avaliacaogruporespostaexclusaoeventos;
use ECidade\RecursosHumanos\ESocial\Entity\ExclusaoEvento;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\V3\Extension\Registry;
use Exception;
use JSON;
use ParameterException;
use stdClass;

/**
 * Class ExclusaoEventosService
 * @package ECidade\RecursosHumanos\ESocial\Service
 */
class ExclusaoEventosService
{
    /**
     * @var Avaliacao
     */
    private $avaliacao;
    /**
     * @var array
     */
    private $parametros;
    /**
     * @var ExclusaoEvento
     */
    private $eventoDeExclusao;
    /**
     * @var stdClass
     */
    private $evento;

    /**
     * @throws Exception
     */
    public function salvar()
    {
        $cpfTrab = array_key_exists('cpfTrab', $this->parametros) ? $this->parametros['cpfTrab'] : null;

        $this->eventoDeExclusao = new ExclusaoEvento();
        $this->eventoDeExclusao->setTpEvento($this->parametros['tpEvento']);
        $this->eventoDeExclusao->setNrRecEvt($this->parametros['nrRecEvt']);
        $this->eventoDeExclusao->setCpfTrab($cpfTrab);

        if ($this->eventoNaoPodeSerExcluido()) {
            throw new Exception("O evento {$this->eventoDeExclusao->getTpEvento()} não pode ser excluído.");
        }

        /**
         * Removidas validacoes abaixo, pois o sistema antes de salvar, busca as informacoes na api, resultando
         * em validacoes redudantes, com o porem que está impactando em muito o processamento em lote devido
         * as 2 requisicoes abaixo serem feitas na api para cada exclusao, resultando em média de 10 segundos para
         * cada exclusao.
        */

        // if ($this->eventoNaoExiste()) {
        //     $msg = "O evento {$this->eventoDeExclusao->getTpEvento()} que está sendo excluído não existe e deve "
        //         . "referir-se ao identificado pelo recibo {$this->eventoDeExclusao->getNrRecEvt()}.";
        //     throw new Exception($msg);
        // }

        // if ($this->eventoJaFoiExcluido()) {
        //     $msg = "O evento {$this->eventoDeExclusao->getTpEvento()} identificado"
        //         . " pelo recibo {$this->eventoDeExclusao->getNrRecEvt()} já foi excluído.";
        //     throw new Exception($msg);
        // }

        $this->validarInformacoesTrabalhador();
        $this->persistir();
    }

    /**
     * @return bool
     * @throws ParameterException
     */
    private function eventoJaFoiExcluido()
    {
        $requisicao = new stdClass();
        $requisicao->idReferencia = $this->eventoDeExclusao->getNrRecEvt();

        return !!$this->request('GET', Recurso::CONSULTA_RECIBO, $requisicao);
    }

    /**
     * @param $method
     * @param $url
     * @param $body
     * @return null|stdClass
     * @throws ParameterException
     */
    private function request($method, $url, $body)
    {
        if (empty($url)) {
            throw new ParameterException('Recurso para requisição da API não informado.');
        }

        $service = new ESocial(Registry::get('app.config'), $url);
        $service->setDados($body);

        return $service->request($method);
    }

    /**
     * @return bool
     * @throws ParameterException
     */
    private function eventoNaoExiste()
    {
        $requisicao = new stdClass();
        $requisicao->idEvento = $this->eventoDeExclusao->getTpEvento();
        $requisicao->numero = $this->eventoDeExclusao->getNrRecEvt();

        $resposta = $this->request('GET', Recurso::CONSULTA_RECIBO, $requisicao);

        if (empty($resposta)) {
            return true;
        }

        $this->evento = array_pop($resposta);

        return false;
    }

    /**
     * @throws Exception
     */
    private function validarInformacoesTrabalhador()
    {
        if ($this->eventoPossuiTrabalhador()) {
            $this->validarCpfTrabalhador();
            $this->validarNisTrabalhador();
        }
    }

    /**
     * @return bool
     */
    private function eventoPossuiTrabalhador()
    {
        return JSON::hasKey('cpfTrab', $this->evento->evento) || JSON::hasKey('nisTrab', $this->evento->evento);
    }

    /**
     * @throws Exception
     */
    private function validarCpfTrabalhador()
    {
        if ($this->eventoDeExclusao->getCpfTrab()) {
            $cpf = db_formatar(JSON::search('cpfTrab', $this->evento->evento), 'CPF');

            if ($this->eventoDeExclusao->getCpfTrab() !== $cpf) {
                $msg = "O evento {$this->eventoDeExclusao->getTpEvento()} que está sendo excluído deve referir-se ao "
                    . "mesmo trabalhador identificado pelo CPF {$cpf}.";
                throw new Exception($msg);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function validarNisTrabalhador()
    {
        if ($this->eventoDeExclusao->getNisTrab()) {
            $nis = JSON::search('nisTrab', $this->evento->evento);

            if ($this->eventoDeExclusao->getNisTrab() !== $nis) {
                $msg = "O evento {$this->eventoDeExclusao->getTpEvento()} que está sendo excluído deve referir-se ao "
                    . "mesmo trabalhador identificado pelo NIS {$nis}.";
                throw new Exception($msg);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function persistir()
    {
        $dao = new cl_avaliacaogruporespostaexclusaoeventos();
        $sql = $dao->sql_query_file(null, '*', null, "eso14_protocolo = '{$this->eventoDeExclusao->getNrRecEvt()}'");
        $resultado = db_query($sql);

        if (!$resultado) {
            $msg = "Não foi possível salvar o preenchimento do formulário. Verifique os valores informados. Se o "
                . "problema persistir, contate o suporte.";
            throw new Exception($msg);
        }

        $dao->eso14_protocolo = $this->eventoDeExclusao->getNrRecEvt();
        $dao->eso14_cgm = $this->parametros['empregador'];

        if (pg_num_rows($resultado) > 0) {
            $preenchimento = pg_fetch_object($resultado);
            $dao->eso14_sequencial = $preenchimento->eso14_sequencial;
            $dao->eso14_avaliacaogruporesposta = $this->avaliacao->getAvaliacaoGrupo();
            $dao->alterar($dao->eso14_sequencial);
        } else {
            $dao->eso14_avaliacaogruporesposta = $this->avaliacao->getAvaliacaoGrupo();
            $dao->incluir(null);
        }

        if ($dao->erro_status === '0') {
            $msg = "Não foi possível salvar o preenchimento do formulário. Verifique os valores informados. Se o "
                . "problema persistir, contate o suporte.";
            throw new Exception($msg);
        }
    }

    /**
     * @param Avaliacao $avaliacao
     */
    public function setAvaliacao(Avaliacao $avaliacao)
    {
        $this->avaliacao = $avaliacao;
    }

    /**
     * @param array $parametros
     */
    public function setParametros(array $parametros)
    {
        $this->parametros = $parametros;
    }

    /**
     * @return bool
     */
    private function eventoNaoPodeSerExcluido()
    {
        $conteudo = file_get_contents('arquivos/esocial/tabelas/tabela_tipo_evento_exclusao.json');
        $eventos = JSON::create()->parse($conteudo);

        foreach ($eventos as $evento) {
            if ($evento->value === $this->eventoDeExclusao->getTpEvento()) {
                return false;
            }
        }

        return true;
    }
}
