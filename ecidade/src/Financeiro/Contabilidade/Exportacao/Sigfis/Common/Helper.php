<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common;

final class Helper
{
    /**
     * Converte uma string em UTF-8 e limita o seu tamanho
     * caso o limit for informado
     *
     * @param string $string
     * @param int|false $limit
     * @return string
     */
    public static function convertAndLimit($string, $limit = false)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');

        if ($limit && mb_strlen($string) > $limit) {
            $string = mb_substr($string, 0, $limit - 3) . '...';
        }

        return $string;
    }
}
