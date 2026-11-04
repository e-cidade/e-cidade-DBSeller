<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use DBDate;
use DBException;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\Recurso;
use ECidade\V3\Extension\Registry;

/**
 * Formata os dados do preenchimento no padrão esperado pela API
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 * @author Andrio Costa <andrio.costa@dbseller.com.br>
 */
class Formatter
{
    protected $dePara = array();
    private $ignoraValidacao = false;
    /**
     * @var \CgmJuridico | null
     */
    private $empregador = null;

    /**
     * Define o template do recurso a ser formatado
     *
     * @param array $dePara
     */
    public function setDePara($dePara)
    {
        $this->dePara = $dePara;
    }

    public function getDePara()
    {
        return $this->dePara;
    }

    /**
     * Formata o dados do formulário de acordo com o template
     *
     * @param array $dados
     * @return \stdClass[]
     */
    public function formatar($dados)
    {
        $aDadosIntegracao = array();

        foreach ($dados as $dadosPreenchimento) {
            $aDadosIntegracao[] = $this->formataPreenchimento($dadosPreenchimento);
        }
        return $aDadosIntegracao;
    }

    /**
     * Cria o objeto a ser enviado para a API já formatado
     *
     * @param \stdClass $dadosPreenchimento
     * @return \stdClass
     */
    private function formataPreenchimento($dadosPreenchimento)
    {
        $preenchimento = new \stdClass();

        if (Tipo::isEFDReinf($dadosPreenchimento->tipo)) {
            $preenchimento->inscricao_contribuinte = $dadosPreenchimento->inscricao_contribuinte;
        } else {
            $preenchimento->inscricao_empregador = $dadosPreenchimento->inscricao_empregador;
        }

        $preenchimento->referencia  = $dadosPreenchimento->responsavel;
        $preenchimento = $this->criarGrupo($preenchimento, $this->dePara, $dadosPreenchimento);
        return $preenchimento;
    }

    /**
     * Cria o nível do grupo de dados.
     * Esse metodo é recursivo, criando os subgrupos também
     *
     * @param \stdClass $preenchimento     objeto onde vai ser criado o grupo
     * @param array    $dePara             com os dados do depara do grupo criado
     * @param \stdClass $dadosPreenchimento dados do preenchimento do formulário no e-cidade
     * @return \stdClass
     */
    private function criarGrupo($preenchimento, $dePara, $dadosPreenchimento)
    {
        foreach ($dePara as $key => $dadosDePara) {
            $sNomeGrupo = $key;

            //Valida se o nome do grupo esta
            if (isset($dadosDePara['nome_api'])) {
                $sNomeGrupo = $dadosDePara['nome_api'];
            }

            if (!isset($preenchimento->{$sNomeGrupo})) {
                // cria o objeto do grupo para envio na API
                $preenchimento->{$sNomeGrupo} = new \stdClass();
            }

            // Quando o grupo é uma coleção de dados
            if (isset($dadosDePara['type']) && $dadosDePara['type'] == 'array') {
                if (!is_array($preenchimento->{$sNomeGrupo})) {
                    $preenchimento->{$sNomeGrupo} = array();
                }
            }

            // Valida se o grupo existe no array
            if (!isset($dadosPreenchimento->respostas[$key])) {
                if (isset($dadosDePara['groups'])) {
                    $preenchimento->{$sNomeGrupo} = $this->criarGrupo(
                        $preenchimento->{$sNomeGrupo},
                        $dadosDePara['groups'],
                        $dadosPreenchimento
                    );
                }
                continue;
            }

            // pega o que foi respondido no grupo
            $respostasPerguntasFormulario = $dadosPreenchimento->respostas[$key]->perguntas;

            if (isset($dadosDePara['items'])) {
                $this->criarItens(
                    $preenchimento->{$sNomeGrupo},
                    $dadosDePara['items'],
                    $respostasPerguntasFormulario
                );
            }

            // valida se existe array de propriedades
            if (isset($dadosDePara['properties'])) {
                $this->criaPropriedades(
                    $preenchimento->{$sNomeGrupo},
                    $dadosDePara['properties'],
                    $respostasPerguntasFormulario
                );
            }

            // Se o grupo atual tem subgrupo
            if (isset($dadosDePara['groups'])) {
                $preenchimento->{$sNomeGrupo} = $this->criarGrupo(
                    $preenchimento->{$sNomeGrupo},
                    $dadosDePara['groups'],
                    $dadosPreenchimento
                );
            }
        }
        return $preenchimento;
    }

    /**
     * Cria os itens de uma coleção de dados
     *
     * @param array $grupo atual
     * @param array $itens
     * @param array $respostasPerguntasFormulario contendo as respostas do grupo percorrido
     */
    private function criarItens(array &$grupo, $itens, $respostasPerguntasFormulario)
    {
        $data = new \stdClass();
        $this->criaPropriedades($data, $itens['properties'], $respostasPerguntasFormulario);
        $grupo[] = $data;
    }

    /**
     * Cria as propriedades do grupo recebido por parâmetro
     *
     * @param \stdClass $grupo
     * @param array $propriedades
     * @param array $respostasPerguntasFormulario
     */
    private function criaPropriedades($grupo, $propriedades, $respostasPerguntasFormulario)
    {
        foreach ($propriedades as $propriedade => $valor) {
            $nomeProrpriedade = is_int($propriedade) ? $valor : $propriedade;

            // valida se a propriedade do template existe nos dados recebidos
            if (!isset($respostasPerguntasFormulario[$nomeProrpriedade])) {
                continue;
            }

            // Se for um array
            if (is_array($valor)) {
                // Não aplica o cast caso valor seja vazio
                $valorCampo = $respostasPerguntasFormulario[$nomeProrpriedade]->resposta->resposta;
                if ($valorCampo !== '' && isset($valor['type'])) {
                    settype($valorCampo, $valor['type']);
                }

                if (isset($valor['type']) && in_array($valor['type'], array('int', 'integer', 'float', 'numeric'))
                    && $valorCampo === ''
                ) {
                    $valorCampo = null;
                }

                if (isset($valor['nome_api'])) {
                    $nomeProrpriedade = $valor['nome_api'];
                }

                $grupo->{$nomeProrpriedade} = $valorCampo;
            } else {
                $grupo->{$valor} = $respostasPerguntasFormulario[$nomeProrpriedade]->resposta->resposta;
            }
        }
        return $grupo;
    }

    /**
     * Valida se uma propriedade do grupo foi preechida
     *
     * @param array $propriedades
     * @return boolean
     */
    protected function validaSeGrupoFoiPreenchido($propriedades)
    {
        $preenchido = false;
        foreach ($propriedades as $propriedade) {
            if (!empty($propriedade)) {
                $preenchido = true;
            }
        }
        return $preenchido;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isEmpty(&$value)
    {
        if (is_scalar($value) && $value === 0) {
            return false;
        }
        if (is_string($value) && $value === '0') {
            return false;
        }
        if (is_object($value)) {
            $value = (array) $value;
        }
        if (is_string($value)) {
            $value = trim($value);
        }
        return empty($value);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function unsetEmpty(&$data)
    {
        foreach ($data as $key => & $value) {
            if (!$this->isEmpty($value)
                && (is_object($value) || is_array($value))
            ) {
                $this->unsetEmpty($value);
            }
            if (is_object($data) && $this->isEmpty($data->{$key})) {
                unset($data->{$key});
            }
            if (is_array($data) && $this->isEmpty($data[$key])) {
                unset($data[$key]);
            }
        }
    }

    public function truncar($valor)
    {
        $valor = abs(round($valor, 6));
        $novoValor = (string) $valor;
        $novoValor = explode(".", $novoValor);
        if (sizeof($novoValor) > 1) {
            if (strlen($novoValor[1]) > 2) {
                $novoValor[1] = substr($novoValor[1], 0, 2);
                $valor = (float)($novoValor[0] . "." . $novoValor[1]);
            }
        }
        return $valor;
    }

    /**
     * Retorna o valor da variavel ignoraValidacao
     */
    public function getIgnoraValidacao()
    {
        return $this->ignoraValidacao;
    }

    /**
     * Seta o valor da variavel ignoraValidacao
     *
     * @return  self
     */
    public function setIgnoraValidacao($ignoraValidacao)
    {
        $this->ignoraValidacao = $ignoraValidacao;

        return $this;
    }

    public function validaEnvioMatricula($matricula, \CgmJuridico $empregador)
    {
        $enviaMatricula = true;
        $sql = <<<SQL
            select
                rh213_sequencial
            from esocialenvio
            where
                rh213_responsavelpreenchimento = '{$matricula}'
                and rh213_evento = '2300'
                and rh213_empregador = {$empregador->getCodigo()}
SQL;
        $rs = db_query($sql);
        if (!$rs) {
            $msg = "Houve um erro ao validar informações de envio do arquivo S-2300 para a matricula {$matricula}.";
            throw new DBException($msg);
        }
        if (pg_numrows($rs) == 0) {
            return true;
        }

        $oESocial = new ESocial(Registry::get('app.config'), Recurso::CONSULTA_RECIBO);

        $dataInicioS10 = new DBDate('2022-06-07');

        $parametros = new \stdClass();
        $parametros->inscricaoEmpregador = $empregador->getCnpj();
        $parametros->idEvento = '2300';
        $parametros->idReferencia = $matricula;

        $oESocial->setDados($parametros);
        $dados = $oESocial->request("GET");
        $excluido = false;
        $processado = false;
        if (!empty($dados)) {
            if (sizeof($dados) > 0) {
                // verificamos o dado do S2300
                $dado = $dados[0];
                if (isset($dado->recibo)) {
                    foreach ($dado->recibo as $recibo) {
                        if ($processado) {
                            continue;
                        }
                        if ($recibo->excluido) {
                            $excluido = true;
                            $processado = true;
                        } else {
                            $dataRecibo = new DBDate(substr($recibo->created_at, 0, 10));
                            if ($dataRecibo->getTimeStamp() <= $dataInicioS10->getTimeStamp()) {
                                $enviaMatricula = false;
                                $processado = true;
                            }
                        }
                    }
                }
                if (empty(json_decode($dado->evento)->infoTSVInicio->matricula)) {
                    $enviaMatricula = false;
                }
            }
            if ($excluido) {
                // alterando regra conforme redmine 24890.
                $enviaMatricula = true;
            }
        }
        return $enviaMatricula;
    }

    /**
     * Get the value of empregador
     *
     * @return  CgmJuridico
     */
    public function getEmpregador()
    {
        return $this->empregador;
    }

    /**
     * Set the value of empregador
     *
     * @param  CgmJuridico  $empregador
     *
     * @return  self
     */
    public function setEmpregador(\CgmJuridico $empregador)
    {
        $this->empregador = $empregador;
    }
}
