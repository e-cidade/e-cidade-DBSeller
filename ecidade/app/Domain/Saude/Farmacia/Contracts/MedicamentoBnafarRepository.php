<?php

namespace App\Domain\Saude\Farmacia\Contracts;

use Illuminate\Support\Collection;

interface MedicamentoBnafarRepository
{
    /**
     * @return Collection
     */
    public function get();

    /**
     * @return Collection
     */
    public function getInconsistencias();

    /**
     * @return integer
     */
    public function getProcessamentos();

    /**
     * @param integer $idUnidade
     * @return MedicamentoBnafarRepository
     */
    public function scopeUnidade($idUnidade);

    /**
     * @return MedicamentoBnafarRepository
     */
    public function scopeSomenteInconsistencias();

    /**
     * @return MedicamentoBnafarRepository
     */
    public function scopeSomentePendentes();

    /**
     * @param integer $idEstoqueMovimentacao
     * @return MedicamentoBnafarRepository
     */
    public function scopeEstoqueMovimentacao($idEstoqueMovimentacao);

    /**
     * @param \DateTime[] $periodo
     * @return MedicamentoBnafarRepository
     */
    public function scopePeriodo(array $periodo);
}
