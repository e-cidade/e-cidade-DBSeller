<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use App\Domain\Tributario\Arrecadacao\Models\GrupoTaxas;
use App\Domain\Tributario\Cadastro\Models\DbSyscampo;

class GrupoTaxasRepository
{
    /**
     * @var GrupoTaxas
     */
    private $grupoTaxasModel;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->grupoTaxasModel = new GrupoTaxas();
    }

    /**
     * @param Integer $sequencial
     * @param String $descricao
     * @param String|Null $datalimite
     * @param Integer $procedenciaprinc
     * @param Integer $origem
     * @return void
     */
    public function adicionar(
        $sequencial,
        $descricao,
        $datalimite,
        $procedenciaprinc,
        $origem
    ) {
        $values = [
            "ar55_sequencial" => $sequencial,
            "ar55_descricao" => $descricao,
            "ar55_procedenciaprinc" => $procedenciaprinc,
            "ar55_origem" => $origem
        ];

        if (isset($datalimite)) {
            $values["ar55_datalimite"] = $datalimite;
        }

        $this->grupoTaxasModel->create($values);

        return;
    }

    /**
     * @param Integer|Null $sequencial
     * @param String|Null $descricao
     * @param String|Null $datalimite
     * @param Integer|Null $procedenciaprinc
     * @param Integer|Null $origem
     * @return void
     */
    public function editar(
        $sequencial,
        $descricao,
        $datalimite,
        $procedenciaprinc,
        $origem
    ) {
        $dados = [];

        if (isset($sequencial)) {
            $dados["ar55_sequencial"] = $sequencial;
        }

        if (isset($descricao)) {
            $dados["ar55_descricao"] = $descricao;
        }

        if (isset($datalimite)) {
            $dados["ar55_datalimite"] = $datalimite;
        }

        if (isset($procedenciaprinc)) {
            $dados["ar55_procedenciaprinc"] = $procedenciaprinc;
        }

        if (isset($origem)) {
            $dados["ar55_origem"] = $origem;
        }

        $this->grupoTaxasModel
            ->where('ar55_sequencial', $sequencial)
            ->update($dados);

        return;
    }

    /**
     * @param Integer $sequencial
     * @return void
     */
    public function deletar($sequencial)
    {
        $this->grupoTaxasModel
            ->where(["ar55_sequencial" => $sequencial])
            ->delete();

        return;
    }

    /**
     * @param Integer|Null $sequencial
     * @param String|Null $descricao
     * @param String|Null $datalimite
     * @param Integer|Null $origem
     * @param Integer $porPagina
     * @return Array
     */
    public function buscar(
        $sequencial = null,
        $descricao = null,
        $datalimite = null,
        $origem = null,
        $porPagina = 15
    ) {
        $resultado = $this->busca(
            $sequencial,
            $descricao,
            $datalimite,
            $origem
        )
            ->with('origem')
            ->with('taxas')
            ->orderBy('ar55_sequencial')
            ->paginate($porPagina);

        return $resultado;
    }

    /**
     * @param Integer|Null $sequencial
     * @param String|Null $descricao
     * @param String|Null $datalimite
     * @param Integer|Null $origem
     * @return Array
     */
    public function buscarSemPaginacao(
        $sequencial = null,
        $descricao = null,
        $datalimite = null,
        $origem = null
    ) {
        $resultado = $this->busca(
            $sequencial,
            $descricao,
            $datalimite,
            $origem
        )
            ->with('origem')
            ->with('taxas')
            ->orderBy('ar55_sequencial')
            ->get()->toArray();

        return $resultado;
    }

    /**
     * @param Integer $sequencial
     * @param String $descricao
     * @param String $datalimite
     * @param Integer $origem
     */
    public function busca(
        $sequencial,
        $descricao,
        $datalimite,
        $origem
    ) {
        $where = '1 = 1';

        if (isset($sequencial)) {
            $where .= " and ar55_sequencial = $sequencial ";
        }

        if (isset($descricao)) {
            $where .= " and ar55_descricao ilike '$descricao' ";
        }

        if (isset($datalimite)) {
            $where .= " and ar55_datalimite = '$datalimite' ";
        }

        if (isset($origem)) {
            $where .= " and (ar55_origem = $origem or ar55_origem = 1) ";
        }

        return $this->grupoTaxasModel
            ->whereRaw($where)
            ->orderBy('ar55_sequencial', 'ASC');
    }

    /**
     * @param Integer $sequencialtaxaslancadas
     */
    public function sqlBuscaTaxasVinculadas($sequencialtaxaslancadas)
    {
        return $this->grupoTaxasModel
            ->join(
                'taxagrupotaxas',
                'taxagrupotaxas.ar57_grupotaxas',
                '=',
                'grupotaxas.ar55_sequencial'
            )
            ->whereRaw("ar57_taxa = $sequencialtaxaslancadas")
            ->toSql();
    }

    /**
     * @param Array $nomeCampos
     * @return Array
     */
    public function getRotulos($nomeCampos)
    {
        $labels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")
                ->where('nomecam', '=', $nomeCampo)
                ->first()->toArray();
        }, $nomeCampos);

        return $labels;
    }
}
