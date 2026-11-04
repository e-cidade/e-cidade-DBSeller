<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024\ArquivoBase;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Exceptions\VersionNotFoundException;

class SigfisFactory
{
    /**
     * Factory dos arquivos Sigfis
     *
     * @param int $version versao do arquivo
     * @param string $arquivo nome do arquivo
     * @return ArquivoBase
     */
    public static function create($version, $arquivo)
    {
        $nameSpace = 'ECidade\\Financeiro\\Contabilidade\\Exportacao\\Sigfis\\Arquivos\\';
        $classArquivo = "v{$version}\\" . 'Arquivo' . ucfirst($arquivo);
        $className = $nameSpace . $classArquivo;

        if (class_exists($className, true)) {
            return new $className($version);
        }

        throw new VersionNotFoundException('Arquivo não encontrado para a versão ' . $version);
    }
}
