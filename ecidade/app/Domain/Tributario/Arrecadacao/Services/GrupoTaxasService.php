<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\GrupoTaxasRepository;
use App\Domain\Tributario\Arrecadacao\Requests\AdicionarGrupoTaxasRequest;
use App\Domain\Tributario\Arrecadacao\Requests\BuscarGrupoTaxasRequest;
use App\Domain\Tributario\Arrecadacao\Requests\DeletarGrupoTaxasRequest;
use App\Domain\Tributario\Arrecadacao\Requests\EditarGrupoTaxasRequest;

class GrupoTaxasService
{
    /**
     * @var GrupoTaxasRepository
     */
    private $grupoTaxasRepository;

    /**
     * @param GrupoTaxasRepository $grupoTaxasRepository
     * @return void
     */
    public function __construct(GrupoTaxasRepository $grupoTaxasRepository)
    {
        $this->grupoTaxasRepository = $grupoTaxasRepository;
    }

    /**
     * @param AdicionarGrupoTaxasRequest $request
     * @return void
     */
    public function adicionar(AdicionarGrupoTaxasRequest $request)
    {
        $this->grupoTaxasRepository->adicionar(
            $request->sequencial,
            $request->descricao,
            $request->datalimite ? $request->datalimite : null,
            $request->procedenciaprinc,
            $request->origem
        );

        return;
    }

    /**
     * @param EditarGrupoTaxasRequest $request
     * @return void
     */
    public function editar(EditarGrupoTaxasRequest $request)
    {
        $sequencial = isset($request->sequencial) ? $request->sequencial : null;
        $descricao = isset($request->descricao) ? $request->descricao : null;
        $datalimite = isset($request->datalimite) ? $request->datalimite : null;
        $procedenciaprinc = isset($request->procedenciaprinc) ? $request->procedenciaprinc : null;
        $origem = isset($request->origem) ? $request->origem : null;

        $this->grupoTaxasRepository->editar(
            $sequencial,
            $descricao,
            $datalimite,
            $procedenciaprinc,
            $origem
        );

        return;
    }

    /**
     * @param DeletarGrupoTaxasRequest $request
     * @return void
     */
    public function deletar(DeletarGrupoTaxasRequest $request)
    {
        $this->grupoTaxasRepository->deletar($request->sequencial);

        return;
    }

    /**
     * @param BuscarGrupoTaxasRequest $request
     * @return Array
     */
    public function buscar(BuscarGrupoTaxasRequest $request)
    {
        $sequencial = isset($request->sequencial) ? $request->sequencial : null;
        $descricao = isset($request->descricao) ? $request->descricao : null;
        $datalimite = isset($request->datalimite) ? $request->datalimite : null;
        $origem = isset($request->origem) ? $request->origem : null;

        $porPagina = $request->porPagina ? $request->porPagina : 10;

        $resultado = $this->grupoTaxasRepository->buscar(
            $sequencial,
            $descricao,
            $datalimite,
            $origem,
            $porPagina
        );

        return $resultado;
    }

    /**
     * @param function getRotulos(
     * @return Array
     */
    public function getRotulos()
    {
        $dados = [];
        $nomeCampos = [
            "ar55_sequencial",
            "ar55_descricao",
            "ar55_datalimite",
            "ar55_procedenciaprinc",
            "ar55_origem",
        ];

        $nomeLabels = $this->grupoTaxasRepository->getRotulos($nomeCampos);

        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $dados[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $dados;
    }

    /**
     * @param String $campos
     * @param String $where
     * @return String
     */
    public function getSqlQueryGrupoTaxas($campos = '*', $where = '')
    {
        $sql = "
            select
                $campos
            from
                grupotaxas
                inner join grupotaxasorigem
                    ON grupotaxasorigem.ar56_sequencial = grupotaxas.ar55_origem
                inner join diversos.procdiver 
                    ON procdiver.dv09_procdiver = grupotaxas.ar55_procedenciaprinc
            $where
        ";

        return $sql;
    }
}
