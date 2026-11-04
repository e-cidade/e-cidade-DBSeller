<?php

namespace App\Domain\Core\Config;

class Desktop
{
    public static function config()
    {
        foreach (static::getFiles() as $file) {
            list($file, $fileExample) = $file;
            if (file_exists($file)) {
                echo "Arquivo existe: ${file}\n";
                continue;
            }

            copy($fileExample, $file);
            echo "Arquivo copiado: ${fileExample} -> ${file}\n";
        }
    }

    protected static function getFiles()
    {
        return [
            ['config/require_extensions.xml', 'config/require_extensions.xml.dist'],
            ['config/plugins.json', 'config/plugins.json.dist'],
            ['libs/db_conn.php', 'libs/db_conn.php.dist'],
            ['config/application.php', 'config/application.default.php'],
            ['config/ecidade_config.php', 'config/ecidade_config.php.dist'],
            ['config/patrimonio/incorporacao_bem.ini', 'config/patrimonio/incorporacao_bem.ini.dist'],
            ['libs/db_acessa.php', 'libs/db_acessa.php.dist'],
            ['.env', '.env.example'],
            /**
             * Solução paleativa para atender demanda do cliente em modificar arquivos de imagens.
             * @todo procurar forma melhor de fazer
             */
            [
                'extension/package/Desktop/assets/img/Window/background.jpg',
                'extension/package/Desktop/assets/img/Window/background.default.jpg'
            ],
            ['imagens/ecidade/login/logotipo_ecidade.png', 'imagens/ecidade/login/logotipo_ecidade.default.png'],
            ['imagens/ecidade/login/imagem-01.jpg', 'imagens/ecidade/login/imagem-01.default.jpg'],
            ['imagens/ecidade/login/imagem-02.jpg', 'imagens/ecidade/login/imagem-02.default.jpg'],
            ['imagens/ecidade/login/imagem-03.jpg', 'imagens/ecidade/login/imagem-03.default.jpg'],
            ['imagens/ecidade/login/imagem-04.jpg', 'imagens/ecidade/login/imagem-04.default.jpg'],
            ['imagens/files/Brasao.jpg', 'imagens/files/Brasao.default.jpg'],
            ['imagens/files/Brasao.png', 'imagens/files/Brasao.default.png'],
            ['imagens/files/correios.jpg', 'imagens/files/correios.default.jpg'],
            ['imagens/files/logo_boleto.jpg', 'imagens/files/logo_boleto.default.jpg'],
            ['imagens/files/logo_boleto.png', 'imagens/files/logo_boleto.default.png'],
        ];
    }
}
