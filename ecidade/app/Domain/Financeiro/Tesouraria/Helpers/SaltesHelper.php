<?php

namespace App\Domain\Financeiro\Tesouraria\Helpers;

use App\Domain\Financeiro\Tesouraria\Models\Saltes;
use cl_caiparametro;
use db_utils;
use Exception;

class SaltesHelper
{
    protected static $folhaUsaContaExtra;

    public static function usaContaExtraPlanilhaSlipFolha($instituicao)
    {
        if (is_null(self::$folhaUsaContaExtra)) {
            $paramentro = new cl_caiparametro();
            $rs = db_query($paramentro->sql_query_file($instituicao, 'k29_folhautilizarcontaextra'));
            self::$folhaUsaContaExtra = db_utils::fieldsMemory($rs, 0)->k29_folhautilizarcontaextra === 't';
        }
        return self::$folhaUsaContaExtra;
    }


    /**
     * Retornar a conta a ser utilizada na planilha ou slip da folha conforme o parâmetro
     *
     * @param integer $conta
     * @param integer $instituicao
     * @return integer
     * @throws Exception
     */
    public static function getContaPlanilhaSlipFolha($conta, $instituicao)
    {
        if (!self::usaContaExtraPlanilhaSlipFolha($instituicao)) {
            return $conta;
        }

        $saltes = Saltes::find($conta);
        $extra = $saltes->contaExtra;
        if (empty($extra)) {
            throw new Exception(sprintf(
                'Não foi possível encontrar a conta extra da conta %. Acesse: %',
                $conta,
                'Tesouraria > Cadastros > Contas > Contas Tesouraria > Alteração de Conta'
            ));
        }

        return $extra->k109_contaextra;
    }
}
