<?php

namespace App\Domain\Financeiro\Contabilidade\Builder;

use Exception;
use stdClass;

class RegraCalculoLinhaLrfBuilder
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $po;
    /**
     * Contas que devem ser filtradas do pcasp
     * @var \Illuminate\Support\Collection
     */
    private $pcasp;
    /**
     * Natureza da conta que deve ser filtrada ND ou NR
     * @var string
     */
    private $natureza;
    /**
     * Contas que devem ser filtradas de acordo com a Natureza.
     * @var \Illuminate\Support\Collection
     */
    private $contas;
    /**
     * Contas que NÃO DEVEM ser filtradas de acordo com a Natureza.
     * @var \Illuminate\Support\Collection
     */
    private $contasExclusao;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $siconfiExclusao;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $siconfi;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $coExclusao;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $co;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $fs;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $fsExclusao;
    /**
     * @var integer|null
     */
    private $fpExclusao;
    /**
     * @var integer|null
     */
    private $fp;
    /**
     * @var int|null
     */
    private $dc;

    public function __construct()
    {
        $this->po = collect([]);
        $this->pcasp = collect([]);
        $this->contas = collect([]);
        $this->contasExclusao = collect([]);
        $this->siconfiExclusao = collect([]);
        $this->siconfi = collect([]);
        $this->coExclusao = collect([]);
        $this->co = collect([]);
        $this->fs = collect([]);
        $this->fsExclusao = collect([]);
    }

    /**
     * @param array $contas da uniao
     * @return $this
     */
    public function addPcasp(array $contas = [])
    {
        $this->pcasp = collect($contas);
        return $this;
    }

    /**
     * Deve ser informado o tipo ND ou NR
     * @param string $natureza
     * @return RegraCalculoLinhaLrfBuilder
     * @throws Exception
     */
    public function natureza($natureza)
    {
        $natureza = strtolower($natureza);
        if (!in_array($natureza, ['nd', 'nr'])) {
            throw new Exception('O Tipo das contas deve ser informado e deve ser ND ou NR.', 403);
        }
        $this->natureza = $natureza;
        return $this;
    }

    /**
     * @param array $contas
     * @param boolean $exclusao
     * @return $this
     */
    public function addContas(array $contas = [], $exclusao = false)
    {
        if ($exclusao) {
            $this->contasExclusao = collect($contas);
            return $this;
        }

        $this->contas = collect($contas);
        return $this;
    }

    /**
     * Essa função valida apenas a "Codificação Padronizada" dos recursos. Ou seja, os últimos três dígitos do recurso.
     * Portanto, ao invés de informarmos 1500/2500 como recurso do siconfi devemos informar apenas 500.
     *
     * @todo se necessário criar uma função para validar a fonte completa do recurso
     *
     * @param array $siconfi
     * @param boolean $exclusao
     * @return $this
     */
    public function addSiconfi(array $siconfi = [], $exclusao = false)
    {
        if ($exclusao) {
            $this->siconfiExclusao = collect($siconfi);
            return $this;
        }

        $this->siconfi = collect($siconfi);
        return $this;
    }

    /**
     * @param array $siconfi
     * @param boolean $exclusao
     * @return $this
     */
    public function addComplemento(array $siconfi = [], $exclusao = false)
    {
        if ($exclusao) {
            $this->coExclusao = collect($siconfi);
            return $this;
        }

        $this->co = collect($siconfi);
        return $this;
    }

    /**
     * @param array $fs
     * @param boolean $exclusao
     * @return $this
     */
    public function addFuncaoSubfuncao(array $fs = [], $exclusao = false)
    {
        if ($exclusao) {
            $this->fsExclusao = collect($fs);
            return $this;
        }

        $this->fs = collect($fs);
        return $this;
    }

    /**
     * @param integer $fp
     * @param boolean $exclusao
     * @return $this
     * @throws Exception
     */
    public function addFp($fp, $exclusao = false)
    {
        if (!in_array($fp, [1, 2])) {
            throw new Exception('FP só pode ser informado com 1 ou 2.');
        }

        if ($exclusao) {
            $this->fpExclusao = $fp;
            return $this;
        }

        $this->fp = $fp;
        return $this;
    }

    /**
     * @param integer|null $dc
     * @return $this
     */
    public function addDc($dc = null)
    {
        $this->dc = $dc;
        return $this;
    }

    /**
     * Adiciona o Poder e Orgão (Representa a instituição)
     * @param array $po
     * @return $this
     */
    public function addPo(array $po)
    {
        $this->po = collect($po);
        return $this;
    }

    public function build()
    {
        if ((!$this->contas->isEmpty() || !$this->contasExclusao->isEmpty()) && is_null($this->natureza)) {
            throw new Exception('Você deve informar a natureza das contas ao definir a regra.');
        }
        return (object)[
            'pcasp' => $this->pcasp,
            'po' => $this->po,
            'natureza' => $this->natureza,
            "contas" => $this->contas,
            "contasExclusao" => $this->contasExclusao,
            "siconfi" => $this->siconfi,
            "siconfiExclusao" => $this->siconfiExclusao,
            "co" => $this->co,
            "coExclusao" => $this->coExclusao,
            "fs" => $this->fs,
            "fsExclusao" => $this->fsExclusao,
            "fp" => $this->fp,
            "fpExclusao" => $this->fpExclusao,
            "dc" => $this->dc,
        ];
    }
}
