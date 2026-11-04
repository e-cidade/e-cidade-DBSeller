<?php

namespace App\Domain\Educacao\Escola\Services;

use ECidade\Educacao\Escola\Model\ProfissionalEscola;

class PermissaoDiarioService
{
    const SEM_PERMISSAO = 0;
    const PERMISSAO_PROFESSOR = 1;
    const PERMISSAO_TOTAL = 2;

    public function getMaiorPermissaoProfissional(ProfissionalEscola $profissionalEscola)
    {
        $maiorPermissao = self::SEM_PERMISSAO;

        foreach ($profissionalEscola->getAtividades() as $atividadeProfissionalEscola) {
            $permissaoDiario = $this->getPermissaoAtividadeEscolar($atividadeProfissionalEscola->getAtividadeEscolar());
            
            if ($permissaoDiario > $maiorPermissao) {
                $maiorPermissao = $permissaoDiario;
            }
        }

        return $maiorPermissao;
    }

    public function getPermissaoAtividadeEscolar(\AtividadeEscolar $atividadeEscolar)
    {
        if ($atividadeEscolar->getPermissaoDiario() == 1) {
            return PermissaoDiarioService::PERMISSAO_TOTAL;
        }

        if ($atividadeEscolar->getPermissaoDiario() == 0 && $atividadeEscolar->isDocente()) {
            return PermissaoDiarioService::PERMISSAO_PROFESSOR;
        }

        return PermissaoDiarioService::SEM_PERMISSAO;
    }
}
