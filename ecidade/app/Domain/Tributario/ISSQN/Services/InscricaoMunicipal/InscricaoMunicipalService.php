<?php

namespace App\Domain\Tributario\ISSQN\Services\InscricaoMunicipal;

use App\Domain\Tributario\ISSQN\Repository\InscricaoMunicipal\InscricaoMunicipalRepository;

/**
 * Classe OrdemServicoService
 * Faz os tramites referentes a ordem de servico
 */
class InscricaoMunicipalService
{
    private $repository;

    /**
     * Construtor da classe
     *
     * @return Void
     */
    public function __construct(InscricaoMunicipalRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Busca uma lista de inscricoes municipais de acordo com os parametros passados
     *
     * @param BuscarInscricoesMunicipaisRequest $request
     */
    public function getInscricoes($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $inscricaoMunicipal = $request->inscricao ? $request->inscricao : "";
        $nome = $request->nome ? $request->nome : "";
        $inscricaoAnterior = $request->inscricaoAnterior ? $request->inscricaoAnterior : "";
        $cgcpf = $request->cgcpf ? $request->cgcpf : "";
        $setorFiscal = $request->setorFiscal ? $request->setorFiscal : "";

        $resultados = $this->repository->getInscricoes(
            $porPagina,
            $inscricaoMunicipal,
            $nome,
            $inscricaoAnterior,
            $cgcpf,
            $setorFiscal
        )->toArray();

        return $resultados;
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     *
     * @return object
     */
    public function getLabelsInscricoes()
    {
        $data = [];

        $nomeCampos = [
            "q02_inscr",
            "z01_nome",
            "z01_cgccpf",
            "q02_inscmu",
            "q177_setorfiscal",
            "z01_ender",
            "z01_numero",
            "z01_compl",
            "q02_dtinic",
            "q02_dtbaix",
        ];

        $nomeLabels = $this->repository->getLabelsListaImoveis($nomeCampos);

        if (count($nomeCampos) == count($nomeLabels)) {
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $data[$nomeCampos[$index]] = ucfirst($nomeLabels[$index]["rotulo"]);
            }
        }

        return $data;
    }

    public function getHistRiscoInscr($q201_inscr)
    {
        return $this->repository->getHistRiscoInscr($q201_inscr);
    }

    public function getInscrDispSalaoParc($request)
    {
        $inscricaoMunicipal = $request->inscricao ? $request->inscricao : "";
        $resultados = $this->repository->getInscrDispSalaoParc(
            $inscricaoMunicipal
        );

        return $resultados;
    }

    public function getDispParc($request)
    {
        $inscricaoMunicipal = $request->inscricao ? $request->inscricao : "";
        $cgm = $request->cgm ? $request->cgm : "";
        $resultados = $this->repository->getDispParc(
            $inscricaoMunicipal,
            $cgm
        );

        return $resultados;
    }

    public function getSetorFiscal($dados)
    {
        $setoresFiscais = $this->repository->getSetorFiscal($dados);

        return $setoresFiscais;
    }
}
