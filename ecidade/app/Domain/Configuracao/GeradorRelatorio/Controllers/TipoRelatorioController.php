<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Controllers;

use App\Domain\Configuracao\GeradorRelatorio\Models\TipoRelatorio;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;

/**
 * @apiDefine usuario Usuario access only
 * Requisicao deve ser feita por um usuario logado
 */
/**
 * @apiDefine Error
 * @apiError {[]} data
 * @apiError {true} error Indica que ocorreu um erro
 * @apiError {String} message Mensagem de erro
 */
class TipoRelatorioController extends Controller
{
    /**
     * @return DBJsonResponse
     *
     * @api {get} configuracao/gerador/relatorios/tipos 13 - Buscar tipos de relatorios
     * @apiName BuscarTiposRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiSuccess {Object[]} data Tipos cadastrados
     * @apiSuccess {Integer} data.codigo Codigo do tipo
     * @apiSuccess {String} data.descricao Descricao do tipo
     *
     * @apiUse Error
     */
    public function index()
    {
        return new DBJsonResponse(
            TipoRelatorio::query()->get(['db14_sequencial as codigo', 'db14_descricao as descricao'])
        );
    }
}
