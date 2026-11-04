<?php

/**
 * DO NOT CHANGE THIS FILE
 * ONLY USE THIS TO COPY FOR YOUR config/application.php FILE
 */

use ECidade\V3\Extension\Logger;
use ECidade\V3\Extension\Registry;

Registry::get('app.config')->merge(array(
    /**
     * Charset do projeto
     * @type string
     */
    'charset' => 'UTF-8',

    /**
     * Exibir erros
     * @type boolean
     */
    'php.display_errors' => false,

    /**
     * Tipos de erros para capturar
     */
    'php.error_reporting' => E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_STRICT,

    /**
     * Lista de URL's de api's usadas pelo ecidade
     */
    'app.api' => array(
        'centraldeajuda' => 'http://centraldeajuda.dbseller.com.br/help/api/index.php/',
        'esocial' => array(
            'url' => 'http://', // informe a api do eSocial
            'login' => '', // login do cliente
            'password' => '' // senha do cliente
        ),
        'estorage' => array(
            'url' => '', // informe a api do e-Storage
            'grant_type' => '', // tipo de credenciais do e-Storage
            'client_id' => '', // login do e-Storage
            'client_secret' => '', // senha do e-Storage
            'client_id_ouvidoria' => '', //login do Ouvidoria no storage
            "disableSSLverification"=> false,
            // Sem url/client_id/client_secret, o storage usa disco local (fallback)
            'local_path' => 'storage/estorage' // diret?rio para persist?ncia local
        ),
        'eauth' => array(
            "url" => "",
            "client_id"=> "",
            "client_secret"=> "",
            "grant_type"=> "client_credentials",
            "municipio"=>"",
            "frontend_client_id"=>"",
            "disableSSLverification"=> false
        ),
        'processoeletronico' => array(
            "url" => "http://",
            "client_id"=> "",
            "client_secret"=> "",
            "grant_type"=> "client_credentials"
        )
    ),

    /**
     * Configuração de proxy para o e-cidade
     */
    'app.proxy' => array(
        'http' => '', // e.g. 192.168.0.1:3128
        'https' => '', // e.g. 192.168.0.1:3128
        'tcp' => ''  // e.g. 192.168.0.1:3128
    ),

    /**
     * Requisicoes que usaram sessao
     * @type string - glob pattern
     */
    'app.request.session.attachOn' => '*.php',

    /**
     * Requisicoes que usaram sessao somente leitura
     * @type string - glob pattern
     */
    'app.request.session.readOnlyOn' => '{skins/*,*.js,*.css}',

    /**
     * Extensoes de arquivos para cachear - 304
     * @type array
     */
    'app.request.asset.cacheable.extension' => array('js', 'css', 'jpg', 'jpeg', 'png', 'bmp', 'ttf', 'gif'),

    /**
     * Gerenciador de erros - [Default|Whoops]
     * @var string
     */
    'app.error.handler' => 'Default',

    /**
     * Log de erros do php
     * @type boolean
     */
    'app.error.log' => true,

    /**
     * Caminho do arquivo para gravar erros do php
     * @type string
     */
    'app.error.log.path' => ECIDADE_EXTENSION_LOG_PATH . 'error.log',

    /**
     * @type string
     */
    'app.error.log.mask' => "{type} - {message} in {file} on line {line}\n{trace}",

    /**
     * @type string
     */
    'app.error.log.mask.trace' => "#{index} {file}:{line} - {class}{type}{function}({args})\n",

    /**
     * Eventos
     * - app.error: executado a cada erro, respeitando a config php.error_reporting
     * - app.shutdown: executado ao final de cada requisicao
     * @type array
     */
    'app.events' => array('app.error' => '\ECidade\V3\Error\EventHandler'),

    /**
     * @type Integer
     * 0 : quiet
     * 1 : info    [info]
     * 2 : notice  [info, notice]
     * 3 : warning [info, notice, warning]
     * 4 : error   [info, notice, warning, error]
     * 5 : debug   [info, notice, warning, error, debug]
     */
    'app.log.verbosity' => Logger::ERROR,

    /**
     * @type String
     */
    'app.log.path' => ECIDADE_EXTENSION_LOG_PATH . 'application.log',

    /**
     * @type String
     */
    'app.modifications.log.path' => ECIDADE_EXTENSION_LOG_PATH . 'modifications.log',

    /**
     * UTF8 -> UNICODE
     * ISO-8859-1 -> LATIN1
     * @see https://secure.php.net/manual/pt_BR/function.pg-set-client-encoding.php
     * @type string
     **/
    'db.client_encoding' => 'LATIN1',

    /**
     * @var int
     * @see https://laravel.com/docs/5.4/passport
     */
    'api.client.id' => 9999999,

    /**
     *
     * @var string
     * @see https://laravel.com/docs/5.4/passport
     */
    'api.client.secret' => '4ed0NUlmQI20AbQZCrzeXxOOlDfjB73ib6juqqpS',
));
