<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao;

use App\Domain\Integracoes\EFDReinf\Retencao\Interfaces\RetencaoInterface;
use Illuminate\Support\Facades\DB;

abstract class Retencao implements RetencaoInterface
{
    /**
     * Orgaos e unidades do usuario
     *
     * @param array $fields
     * @return object|false
     */
    protected function checkOrgaoUnidadeUsuario($orgao)
    {
        $ano  = $_SESSION['DB_anousu'];
        $user = $_SESSION['DB_id_usuario'];

        $permission = DB::table('db_permemp')
        ->join('db_usupermemp', 'db21_codperm', '=', 'db20_codperm')
        ->join(
            'orcorgao',
            'db20_anousu',
            '=',
            DB::Raw('o40_anousu and o40_orgao = db20_orgao')
        )
        ->where([
            ['o40_orgao',  '=', $orgao],
            ['o40_anousu', '=', $ano],
            ['db21_id_usuario', '=', $user]
        ])
        ->first(['db20_codperm']);

        return $permission;
    }
}
