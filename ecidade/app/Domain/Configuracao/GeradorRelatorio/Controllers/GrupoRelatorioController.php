<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Controllers;

use App\Domain\Configuracao\GeradorRelatorio\Models\GrupoRelatorio;
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
class GrupoRelatorioController extends Controller
{
    /**
     * @return DBJsonResponse
     *
     * @api {get} configuracao/gerador/relatorios/grupos 12 - Buscar grupos de relatorios
     * @apiName BuscarGruposRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiSuccess {Object[]} data Grupos cadastrados
     * @apiSuccess {Integer} data.codigo Codigo do grupo
     * @apiSuccess {String} data.descricao Descricao do grupo
     *
     * @apiUse Error
     */
    public function index()
    {
        return new DBJsonResponse(
            GrupoRelatorio::orderBy('db13_sequencial')->get(['db13_sequencial as codigo','db13_descricao as descricao'])
        );
    }
}
