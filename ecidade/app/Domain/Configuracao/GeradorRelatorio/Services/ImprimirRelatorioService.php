<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Services;

use App\Domain\Configuracao\GeradorRelatorio\Factories\RelatorioFactory;
use App\Domain\Configuracao\GeradorRelatorio\Mappers\RelatorioJsonMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ImprimirRelatorioService
{
    private $mapper;

    public function __construct(RelatorioJsonMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $dados = $this->executeSql();
        if ($dados->isEmpty()) {
            throw new \BusinessException('Nenhum registro retornado.', 404);
        }

        if ($this->mapper->getTipo() === 2) {
            $templateService = new ImprimirRelatorioTemplateService($this->mapper->getCodigo());
            return $templateService->execute((array)$dados->first(), $this->mapper->getCampos());
        }

        $relatorio = RelatorioFactory::get($this->mapper->getTipoSaida());

        $relatorio->setLayout($this->mapper->getLayout());
        $relatorio->setCampos($this->mapper->getCampos());
        $relatorio->setDados($dados->toArray());

        return $relatorio->emitir();
    }

    /**
     * @return Collection
     */
    private function executeSql()
    {
        $table = $this->mapper->getQueryFrom();
        $campos = $this->mapper->getQueryColumns($this->mapper->getTipo() === 2);
        $query = DB::table($table)
            ->select($campos);

        $where = $this->mapper->getQueryWhere();
        if (!empty($where)) {
            $query->where($where);
        }

        $ordem = $this->mapper->getQueryOrderBy();
        if (!empty($ordem)) {
            $query->orderByRaw(implode(', ', $ordem));
        }

        /**
         * @todo fazer de forma paginada para evitar overflow?
         */

        return $query->get();
    }
}
