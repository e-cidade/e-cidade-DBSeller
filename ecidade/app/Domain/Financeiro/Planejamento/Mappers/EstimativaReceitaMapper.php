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

namespace App\Domain\Financeiro\Planejamento\Mappers;

/**
 * Class EstimativaReceitaMapper
 * @package App\Domain\Financeiro\Planejamento\Mappers
 */
class EstimativaReceitaMapper
{
    public $id;
    public $nivel;
    public $planejamento_id;
    public $anoorcamento;
    public $orcfontes_id;
    public $recurso_id;
    public $instituicao_id;
    public $concarpeculiar_id;
    public $orcorgao_id;
    public $orcunidade_id;
    public $codigoEsferaorcamentaria;
    public $esferaorcamentaria;
    public $valorBase = 0;
    public $inclusaomanual = false;
    public $fonte;
    public $fonte_mascara;
    public $descricao_fonte;
    public $descricao_orgao;
    public $descricao_unidade;
    public $caracteristica_peculiar;
    public $nome_instituicao;
    public $recurso;
    public $fonte_recurso;
    public $codigo_complemento;
    public $complemento;
    public $valores = [];
    public $cronograma = [];
    public $inflatores = [];
    /**
     * @var bool
     */
    public $temDesdobramento = false;
    /**
     * @var string
     */
    public $identificadorResultado;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param mixed $nivel
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
    }

    /**
     * @return mixed
     */
    public function getPlanejamentoId()
    {
        return $this->planejamento_id;
    }

    /**
     * @param mixed $planejamento_id
     */
    public function setPlanejamentoId($planejamento_id)
    {
        $this->planejamento_id = $planejamento_id;
    }

    /**
     * @return mixed
     */
    public function getAnoorcamento()
    {
        return $this->anoorcamento;
    }

    /**
     * @param mixed $anoorcamento
     */
    public function setAnoorcamento($anoorcamento)
    {
        $this->anoorcamento = $anoorcamento;
    }

    /**
     * @return mixed
     */
    public function getOrcfontesId()
    {
        return $this->orcfontes_id;
    }

    /**
     * @param mixed $orcfontes_id
     */
    public function setOrcfontesId($orcfontes_id)
    {
        $this->orcfontes_id = $orcfontes_id;
    }

    /**
     * @return mixed
     */
    public function getRecursoId()
    {
        return $this->recurso_id;
    }

    /**
     * @param mixed $recurso_id
     */
    public function setRecursoId($recurso_id)
    {
        $this->recurso_id = $recurso_id;
    }

    /**
     * @return mixed
     */
    public function getInstituicaoId()
    {
        return $this->instituicao_id;
    }

    /**
     * @param mixed $instituicao_id
     */
    public function setInstituicaoId($instituicao_id)
    {
        $this->instituicao_id = $instituicao_id;
    }

    /**
     * @return mixed
     */
    public function getConcarpeculiarId()
    {
        return $this->concarpeculiar_id;
    }

    /**
     * @param mixed $concarpeculiar_id
     */
    public function setConcarpeculiarId($concarpeculiar_id)
    {
        $this->concarpeculiar_id = $concarpeculiar_id;
    }

    /**
     * @return mixed
     */
    public function getOrcorgaoId()
    {
        return $this->orcorgao_id;
    }

    /**
     * @param mixed $orcorgao_id
     */
    public function setOrcorgaoId($orcorgao_id)
    {
        $this->orcorgao_id = $orcorgao_id;
    }

    /**
     * @return mixed
     */
    public function getOrcunidadeId()
    {
        return $this->orcunidade_id;
    }

    /**
     * @param mixed $orcunidade_id
     */
    public function setOrcunidadeId($orcunidade_id)
    {
        $this->orcunidade_id = $orcunidade_id;
    }

    /**
     * @return mixed
     */
    public function getCodigoEsferaorcamentaria()
    {
        return $this->codigoEsferaorcamentaria;
    }

    /**
     * @param mixed $codigoEsferaorcamentaria
     */
    public function setCodigoEsferaorcamentaria($codigoEsferaorcamentaria)
    {
        $this->codigoEsferaorcamentaria = $codigoEsferaorcamentaria;
    }

    /**
     * @return int
     */
    public function getValorBase()
    {
        return $this->valorBase;
    }

    /**
     * @param int $valorBase
     */
    public function setValorBase($valorBase)
    {
        $this->valorBase = $valorBase;
    }

    /**
     * @return bool
     */
    public function isInclusaomanual()
    {
        return $this->inclusaomanual;
    }

    /**
     * @param bool $inclusaomanual
     */
    public function setInclusaomanual($inclusaomanual)
    {
        $this->inclusaomanual = $inclusaomanual;
    }

    /**
     * @return mixed
     */
    public function getFonte()
    {
        return $this->fonte;
    }

    /**
     * @param mixed $fonte
     */
    public function setFonte($fonte)
    {
        $this->fonte = $fonte;
    }

    /**
     * @return mixed
     */
    public function getFonteMascara()
    {
        return $this->fonte_mascara;
    }

    /**
     * @param mixed $fonte_mascara
     */
    public function setFonteMascara($fonte_mascara)
    {
        $this->fonte_mascara = $fonte_mascara;
    }

    /**
     * @return mixed
     */
    public function getDescricaoFonte()
    {
        return $this->descricao_fonte;
    }

    /**
     * @param mixed $descricao_fonte
     */
    public function setDescricaoFonte($descricao_fonte)
    {
        $this->descricao_fonte = $descricao_fonte;
    }

    /**
     * @return mixed
     */
    public function getDescricaoOrgao()
    {
        return $this->descricao_orgao;
    }

    /**
     * @param mixed $descricao_orgao
     */
    public function setDescricaoOrgao($descricao_orgao)
    {
        $this->descricao_orgao = $descricao_orgao;
    }

    /**
     * @return mixed
     */
    public function getDescricaoUnidade()
    {
        return $this->descricao_unidade;
    }

    /**
     * @param mixed $descricao_unidade
     */
    public function setDescricaoUnidade($descricao_unidade)
    {
        $this->descricao_unidade = $descricao_unidade;
    }

    /**
     * @return mixed
     */
    public function getCaracteristicaPeculiar()
    {
        return $this->caracteristica_peculiar;
    }

    /**
     * @param mixed $caracteristica_peculiar
     */
    public function setCaracteristicaPeculiar($caracteristica_peculiar)
    {
        $this->caracteristica_peculiar = $caracteristica_peculiar;
    }

    /**
     * @return mixed
     */
    public function getNomeInstituicao()
    {
        return $this->nome_instituicao;
    }

    /**
     * @param mixed $nome_instituicao
     */
    public function setNomeInstituicao($nome_instituicao)
    {
        $this->nome_instituicao = $nome_instituicao;
    }

    /**
     * @return mixed
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * @param mixed $recurso
     */
    public function setRecurso($recurso)
    {
        $this->recurso = $recurso;
    }

    /**
     * @return mixed
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * @param mixed $complemento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    /**
     * @return mixed
     */
    public function getCodigoComplemento()
    {
        return $this->codigo_complemento;
    }

    /**
     * @param mixed $complemento
     */
    public function setCodigoComplemento($codigoComplemento)
    {
        $this->codigo_complemento = str_pad($codigoComplemento, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return array
     */
    public function getValores()
    {
        return $this->valores;
    }

    /**
     * @param array $valores
     */
    public function setValores($valores)
    {
        $this->valores = $valores;
    }

    /**
     * @return array
     */
    public function getCronograma()
    {
        return $this->cronograma;
    }

    /**
     * @param array $cronograma
     */
    public function setCronograma($cronograma)
    {
        $this->cronograma = $cronograma;
    }

    public function toArray()
    {
        $dados = [
            'id' => $this->id,
            'nivel' => $this->nivel,
            'planejamento_id' => $this->planejamento_id,
            'ano_orcamento' => $this->anoorcamento,
            'orcfontes_id' => $this->orcfontes_id,
            'recurso_id' => $this->recurso_id,
            'instituicao_id' => $this->instituicao_id,
            'concarpeculiar_id' => $this->concarpeculiar_id,
            'orcorgao_id' => $this->orcorgao_id,
            'orcunidade_id' => $this->orcunidade_id,
            'codigo_esfera_orcamentaria' => $this->codigoEsferaorcamentaria,
            'esfera_orcamentaria' => $this->esferaorcamentaria,
            'valor_base' => $this->valorBase,
            'inclusao_manual' => $this->inclusaomanual,
            'fonte' => $this->fonte,
            'fonte_mascara' => $this->fonte_mascara,
            'descricao_fonte' => $this->descricao_fonte,
            'descricao_orgao' => $this->descricao_orgao,
            'descricao_unidade' => $this->descricao_unidade,
            'caracteristica_peculiar' => $this->caracteristica_peculiar,
            'nome_instituicao' => $this->nome_instituicao,
            'recurso' => $this->recurso,
            'fonte_recurso' => $this->fonte_recurso,
            'codigo_complemento' => $this->codigo_complemento,
            'complemento' => $this->complemento,
            'valores' => $this->valores,
            'inflatores' => $this->inflatores,
            'cronograma' => $this->cronograma,
            'temDesdobramento' => $this->temDesdobramento,
            'identificador_resultado' => $this->identificadorResultado,
            'contasDesdobramento' => [],
            'fontesPai' => []
        ];

        foreach ($this->valores as $valor) {
            $dados["valor_{$valor->ano}"] = $valor->valor;
        }

        return (object) $dados;
    }

    /**
     * @param boolean $temDesdobramento
     */
    public function temDesdobramento($temDesdobramento = false)
    {
        $this->temDesdobramento = $temDesdobramento;
    }

    /**
     * @param mixed $fonte_recurso
     * @return EstimativaReceitaMapper
     */
    public function setFonteRecurso($fonte_recurso)
    {
        $this->fonte_recurso = $fonte_recurso;
        return $this;
    }

    /**
     * @param $identificador_resultado
     */
    public function setDescricaoIdentificadorResultado($identificador_resultado)
    {
        $this->identificadorResultado = $identificador_resultado;
    }

    public function setDescricaoEsferaOrcamentaria($esfera_orcamentaria)
    {
        $this->esferaorcamentaria = $esfera_orcamentaria;
    }

    /**
     * @return array
     */
    public function getInflatores()
    {
        return $this->inflatores;
    }

    /**
     * @param array $inflatores
     */
    public function setInflatores(array $inflatores)
    {
        $this->inflatores = $inflatores;
    }
}
