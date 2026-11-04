<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use App\Domain\Tributario\Arrecadacao\Models\TaxaGrupoTaxas;
use Illuminate\Support\Facades\DB;

class TaxaGrupoTaxasRepository
{
    /**
     * @var GrupoTaxas
     */
    private $taxaGrupoTaxasModel;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->taxaGrupoTaxasModel = new TaxaGrupoTaxas();
    }

    /**
     * @param Integer $sequencialTaxa
     * @param Integer $sequencialGrupo
     * @return void
     */
    public function adicionar(
        $sequencialTaxa,
        $sequencialGrupo
    ) {
        $this->taxaGrupoTaxasModel->create([
            "ar57_taxa" => $sequencialTaxa,
            "ar57_grupotaxas" => $sequencialGrupo
        ]);

        return;
    }

    /**
     * @param Integer $sequencialTaxa
     * @param Integer $sequencialGrupo
     * @return String
     */
    public function sqlAdicionar(
        $sequencialTaxa,
        $sequencialGrupo
    ) {
        $sql = "insert into taxagrupotaxas (ar57_taxa, ar57_grupotaxas)
                values ($sequencialTaxa, $sequencialGrupo)";

        return $sql;
    }

    /**
     * @param Integer|Null $sequencial
     * @param Integer|Null $sequencialTaxa
     * @param Integer|Null $sequencialGrupo
     * @return void
     */
    public function editar(
        $sequencial,
        $sequencialTaxa,
        $sequencialGrupo
    ) {
        $dados = [];

        if (isset($sequencial)) {
            $dados["ar57_sequencial"] = $sequencial;
        }

        if (isset($sequencialTaxa)) {
            $dados["ar57_taxa"] = $sequencialTaxa;
        }

        if (isset($sequencialGrupo)) {
            $dados["ar57_grupotaxas"] = $sequencialGrupo;
        }

        $this->taxaGrupoTaxasModel
            ->where('ar57_sequencial', $sequencial)
            ->update($dados);

        return;
    }

    /**
     * @param Integer|Null $sequencial
     * @param Integer|Null $sequencialTaxa
     * @param Integer|Null $sequencialGrupo
     * @return void
     */
    public function deletar(
        $sequencial = null,
        $sequencialTaxa = null,
        $sequencialGrupo = null
    ) {
        $where = [];

        if (isset($sequencial)) {
            $where[] = ["ar57_sequencial" => $sequencial];
        }

        if (isset($descricao)) {
            $where[] = ["ar57_taxa" => $sequencialTaxa];
        }

        if (isset($sequencialGrupo)) {
            $where[] = ["ar57_grupotaxas" => $sequencialGrupo];
        };

        if ($sequencial || $sequencialTaxa || $sequencialGrupo) {
            $this->taxaGrupoTaxasModel
                ->where($where)
                ->delete();
        }

        return;
    }

    /**
     * @param Integer|Null $sequencial
     * @param Integer|Null $sequencialTaxa
     * @param Integer|Null $sequencialGrupo
     * @return String|Void
     */
    public function sqlDeletar(
        $sequencial = null,
        $sequencialTaxa = null,
        $sequencialGrupo = null
    ) {

        $where = '1 = 1';

        if (isset($sequencial)) {
            $where .= " and ar57_sequencial = $sequencial ";
        }

        if (isset($sequencialTaxa)) {
            $where .= " and ar57_taxa = $sequencialTaxa ";
        }

        if (isset($sequencialGrupo)) {
            $where .= " and ar57_grupotaxas = $sequencialGrupo ";
        }

        if ($sequencial || $sequencialTaxa || $sequencialGrupo) {
            return "delete from taxagrupotaxas where $where";
        }

        return;
    }

    /**
     * @param Integer|Null $sequencial
     * @param Integer|Null $sequencialTaxa
     * @param Integer|Null $sequencialGrupo
     * @return array
     */
    public function buscar(
        $sequencial = null,
        $sequencialTaxa = null,
        $sequencialGrupo = null
    ) {
        return $this->busca(
            $sequencial,
            $sequencialTaxa,
            $sequencialGrupo
        )->get()->toArray();
    }

    /**
     * @param Integer|Null $sequencial
     * @param Integer|Null $sequencialTaxa
     * @param Integer|Null $sequencialGrupo
     * @return String
     */
    public function sqlBuscar(
        $sequencial = null,
        $sequencialTaxa = null,
        $sequencialGrupo = null
    ) {
        return $this->busca(
            $sequencial,
            $sequencialTaxa,
            $sequencialGrupo
        )->toSql();
    }

    /**
     * @param Integer|Null $sequencial
     * @param Integer|Null $sequencialTaxa
     * @param Integer|Null $sequencialGrupo
     */
    private function busca(
        $sequencial,
        $sequencialTaxa,
        $sequencialGrupo
    ) {
        $where = '1 = 1';

        if (isset($sequencial)) {
            $where .= " and ar57_sequencial = $sequencial ";
        }

        if (isset($descricao)) {
            $where .= " and ar57_taxa = $sequencialTaxa ";
        }

        if (isset($sequencialGrupo)) {
            $where .= " and ar57_grupotaxas = $sequencialGrupo ";
        }

        return $this->taxaGrupoTaxasModel
            ->whereRaw($where)
            ->orderBy('ar57_sequencial', 'ASC');
    }
}
