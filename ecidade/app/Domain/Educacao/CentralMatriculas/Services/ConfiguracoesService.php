<?php
namespace App\Domain\Educacao\CentralMatriculas\Services;

use App\Domain\Educacao\MatriculaOnline\Repositories\DuvidasFrequentesRepository;
use App\Domain\Educacao\MatriculaOnline\Resources\DuvidasFrequentesResource;

class ConfiguracoesService
{
    protected $duvidasRepository;

    public function __construct()
    {
        $this->duvidasRepository = new DuvidasFrequentesRepository();
    }

    public function getDuvidas()
    {
        return $this->duvidasRepository->index()->map(function ($duvidas) {
            return DuvidasFrequentesResource::toResponse($duvidas);
        });
    }
}
