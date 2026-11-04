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
namespace ECidade\RecursosHumanos\ESocial\Entity;

use ECidade\RecursosHumanos\Pessoal\Model\ServidorOperadoraSaude;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorOutrosVinculos;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorProcessosJudiciaisFolha;

/**
 * Class Remuneracao
 * @package ECidade\RecursosHumanos\ESocial\Entity
 */
class Remuneracao
{
    /**
     * @var array
     */
    private $pagamentos = array();

    /**
     * @return array
     */
    public function getPagamentos()
    {
        return $this->pagamentos;
    }

    /**
     * @param array $pagamentos
     */
    public function setPagamentos($pagamentos)
    {
        $this->pagamentos = $pagamentos;
    }

    /**
     * @var \Servidor
     */
    private $servidor;

    /**
     * @param \Servidor $servidor
     */
    public function setServidor(\Servidor $servidor)
    {
        $this->servidor = $servidor;
    }

    /**
     * @return \Servidor
     */
    public function getServidor()
    {
        return $this->servidor;
    }

    /**
     * @var ServidorOutrosVinculos[]
     */
    private $servidorOutrosVinculos = array();

    /**
     * @return ServidorOutrosVinculos[]
     */
    public function getServidorOutrosVinculos()
    {
        return $this->servidorOutrosVinculos;
    }

    /**
     * @param ServidorOutrosVinculos[] $servidorOutrosVinculos
     * @return ServidorOutrosVinculos[] $servidorOutrosVinculos
     */
    public function setServidorOutrosVinculos($servidorOutrosVinculos)
    {
        return $this->servidorOutrosVinculos = $servidorOutrosVinculos;
    }

    /**
     * @var \stdClass
     */
    private $dadosTrabalhador;

    /**
     * @return \stdClass
     */
    public function getDadosTrabalhador()
    {
        return $this->dadosTrabalhador;
    }

    /**
     * @param \stdClass $dadosTrabalhador
     */
    public function setDadosTrabalhador(\stdClass $dadosTrabalhador)
    {
        $this->dadosTrabalhador = $dadosTrabalhador;
    }

    /**
     * @var ServidorProcessosJudiciaisFolha[]
     */
    private $pocessosJudiciais = array();

    /**
     * @return ServidorProcessosJudiciaisFolha[]
     */
    public function getProcessosJudiciais()
    {
        return $this->pocessosJudiciais;
    }

    /**
     * @param ServidorProcessosJudiciaisFolha[] $pocessosJudiciais
     */
    public function setProcessosJudiciais($pocessosJudiciais)
    {
        $this->pocessosJudiciais = $pocessosJudiciais;
    }


    /**
     * @param $pagamentos
     * Soma os valores de remuneração por rubrica, lotação e tipo
    */
    public function ajustaPagamentos($pagamentos)
    {
        $rubricaUniValor = [];
        foreach ($pagamentos as $pagamento) {
            foreach ($pagamento as $y => $rubricaPagamento) {
                if (array_key_exists($rubricaPagamento->codigo . '-' .
                    $rubricaPagamento->lotacao . '-' .
                    $rubricaPagamento->nomePagamento, $rubricaUniValor)) {
                    $rubricaUniValor[$rubricaPagamento->codigo . '-' .
                        $rubricaPagamento->lotacao . '-' .
                        $rubricaPagamento->nomePagamento] +=
                        $rubricaPagamento->valor;
                } else {
                    $rubricaUniValor[$rubricaPagamento->codigo . '-' .
                        $rubricaPagamento->lotacao. '-' .
                        $rubricaPagamento->nomePagamento] =
                        $rubricaPagamento->valor;
                }
            }
        }
        $novoPagamento = [];
        foreach ($pagamentos as $i => $tipoPagamento) {
            $novoPagamento[$i] = [];
            foreach ($tipoPagamento as $rubrica) {
                $novoPagamento[$i][$rubrica->lotacao][$rubrica->codigo] = $rubrica;
            }
        }
        $pagamentos = [];
        $contador = 0;
        foreach ($novoPagamento as $pagamento) {
            $pagamentos[$contador] = [];
            foreach ($pagamento as $lotacao) {
                foreach ($lotacao as $rubrica) {
                    $valorUnificado = $rubricaUniValor[$rubrica->codigo . '-' .
                        $rubrica->lotacao . '-' .
                        $rubrica->nomePagamento];
                    $rubrica->valor = $valorUnificado;
                    $pagamentos[$contador][] = $rubrica;
                }
            }
            $contador += 1;
        }
        return $pagamentos;
    }
}
