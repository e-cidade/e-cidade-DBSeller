<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Xsd;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Exceptions\VersionNotFoundException;

/**
 * Factory dos arquivos Sigfis
 */
class XSDFactory
{
    /**
     * Retorna caminho do xsd
     *
     * @param int $version versao do arquivo
     * @param string $arquivo nome do arquivo
     * @return string
     */
    public static function get($version, $name)
    {
        $file = "src/Financeiro/Contabilidade/Exportacao/Sigfis/Xsd/v{$version}/Remessa{$name}.xsd";

        if (file_exists($file)) {
            return $file;
        }

        throw new VersionNotFoundException('XSD não encontrado para a versão ' . $version . ' Arquivo ' . $name);
    }
}
