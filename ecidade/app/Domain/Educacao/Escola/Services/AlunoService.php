<?php

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Educacao\Escola\Models\Aluno;

class AlunoService
{

    public function atualizaContatoResponsavel($dados)
    {
        $aluno = Aluno::find($dados->id);
        if ($aluno) {
            if ($dados->telefone_responsavel !== '') {
                $aluno->ed47_celularresponsavel = $dados->telefone_responsavel;
            } else {
                $aluno->ed47_c_emailresp = $dados->email_responsavel;
            }
            $aluno->save();
        }
    }
}
