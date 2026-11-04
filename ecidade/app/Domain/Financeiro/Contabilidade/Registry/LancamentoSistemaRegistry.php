<?php

namespace App\Domain\Financeiro\Contabilidade\Registry;

use App\Domain\Financeiro\Contabilidade\VO\LancamentoSistemaVO;

class LancamentoSistemaRegistry
{
    private static $storage = array();

    public static function set(LancamentoSistemaVO $sistema)
    {
        $hash = sprintf('%s#%s', $sistema->reduzido, $sistema->codigoSistema);
        self::$storage[$hash] = $sistema;
    }

    public static function get($reduzido, $codigo)
    {
        $hash = sprintf('%s#%s', $reduzido, $codigo);
        if (!array_key_exists($hash, self::$storage)) {
            $vo = new LancamentoSistemaVO($reduzido, $codigo);
            self::set($vo);
        }

        return self::$storage[$hash];
    }
}
