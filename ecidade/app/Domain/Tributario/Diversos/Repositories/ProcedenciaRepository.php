<?php

namespace App\Domain\Tributario\Diversos\Repositories;

use App\Domain\Tributario\Cadastro\Models\DbSyscampo;
use App\Domain\Tributario\Diversos\Models\Procedencia;

class ProcedenciaRepository
{
    /**
     * @var Procedencia
     */
    private $procedenciaModel;

    public function __construct()
    {
        $this->procedenciaModel = new Procedencia();
    }

    /**
     * Metodo para buscar as labels dos campos
     * @param array $nomeCampos
     * @return array
     */
    public function getRotulos($nomeCampos)
    {
        $labels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")->where('nomecam', '=', $nomeCampo)->first()->toArray();
        }, $nomeCampos);

        return $labels;
    }

    /**
     * Retorna uma lista de registros com base em um ou mais parametros
     *
     * @param integer $sequencial
     * @param integer $receita
     * @param string $descricaoabreviada
     * @param string $descricao
     * @param boolean $filtraTipoCobrancaFalso
     * @param integer $porPagina
     */
    public function getByParams(
        $sequencial,
        $receita,
        $descricaoabreviada,
        $descricao,
        $filtraTipoCobrancaFalso,
        $porPagina
    ) {
        $where = " 1 = 1 ";

        if ($sequencial && trim(strval($sequencial) != '')) {
            $where .= " and dv09_procdiver = $sequencial ";
        }

        if ($receita && trim(strval($receita) != '')) {
            $where .= " and dv09_receit = $receita ";
        }

        if ($descricaoabreviada && trim(strval($descricaoabreviada) != '')) {
            $where .= " and dv09_descra ilike '%$descricaoabreviada%' ";
        }

        if ($descricao && trim(strval($descricao) != '')) {
            $where .= " and dv09_descr ilike '%$descricao%' ";
        }

        if ($filtraTipoCobrancaFalso) {
            $where .= " and dv09_cobranca is false ";
        }

        $resultadoPesquisa = $this->procedenciaModel->whereRaw(trim($where))
            ->orderBy('dv09_procdiver', 'ASC')
            ->paginate($porPagina);

        return $resultadoPesquisa;
    }
}
