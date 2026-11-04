<?php

namespace App\Helpers\Configuracao;

use App\Domain\Tributario\Cadastro\Models\DbSyscampo;

class BuscaRotulosSyscampo
{
    /**
     * @param string[] $nomeCampos
     */
    public static function execute($nomeCampos)
    {
        $dados = [];
        $nomeLabels = array_map(function ($nomeCampo) {
            return DbSyscampo::select("rotulo")
                ->where('nomecam', '=', $nomeCampo)
                ->first()->toArray();
        }, $nomeCampos);
// formato $nomeLabels = [['rotulo' => 'nome_rotulo1'], ['rotulo' => 'nome_rotulo2']]

        if (count($nomeCampos) == count($nomeLabels)) { //caso todos os rotulo sejam encontrados
            for ($index = 0; $index < count($nomeCampos); $index++) {
                $dados[$nomeCampos[$index]] =
                    ucfirst($nomeLabels[$index]["rotulo"]);
            }
            // formato $dados = [['nome_campo1' => 'nome_rotulo1'], ['nome_campo2' => 'nome_rotulo2']]
        }
        return $dados;
    }
}
