<?php

namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Orcamento\Models\ClassificacaoFonteRecurso;
use App\Domain\Financeiro\Orcamento\Resources\ClassificacoesFonteRecursoMaisSiconfi;
use App\Domain\Financeiro\Orcamento\Services\ClassificacaoFonteRecursoService;

class ClassificacaoFonteRecursoController
{
    /**
     * @return DBJsonResponse
     * @api {get} v4/api/financeiro/orcamento/recursos/classificacoes 01 Classificacoes dos recursos
     * @apiDescription Retorna as classificações dos recursos
     * @apiName ConsultaRecursosClassificacoes
     * @apiGroup orcamento-recursos-classificacoes
     *
     * @apiHeaderExample {json} Request Header
     *    { "Authorization": "Bearer token" ,
     *      "Content-Type": "application/json",
     *      "Accept": "application/json"
     *   }
     *
     * @apiSuccessExample {json} Resposta
     *       HTTP/1.1 200 OK
     *       {
     *         "error":false,
     *         "message": "Classificação de fonte de recurso.",
     *         "data": [{"id": 1, "descricao": "Nao se aplica"}, ...]
     *       }
     */
    public function get()
    {
        return new DBJsonResponse(
            ClassificacaoFonteRecurso::all()->sortBy('id'),
            'Classificação de fonte de recurso.'
        );
    }

    /**
     * @return DBJsonResponse
     * @api {get} v4/api/financeiro/orcamento/classificacao/com-siconfi 02 - classificacoes com recursos do siconfi
     * @apiDescription Retorna as classificaçoes de recursos com os recursos do siconfi.
     * @apiName ConsultaClassificacoesComSiconfi
     * @apiGroup orcamento-recursos-classificacoes
     *
     * @apiHeaderExample {json} Request Header
     *     { "Authorization": "Bearer token" ,
     *       "Content-Type": "application/json",
     *       "Accept": "application/json"
     *    }
     *
     * @apiSuccessExample {json} Resposta
     *    HTTP/1.1 200 OK
     *    {
     *      "error":false,
     *      "message": "Classificação de fonte de recurso.",
     *      "data":
     *      [{
     *         "id": "",
     *         "descricao": "",
     *         "fontes_siconfi":
     *         [{
     *              "codigo_siconfi": "",
     *              "descricao": "",
     *              "classificacaofr_id": "",
     *              "finalidade": ""
     *         }, ...]
     *     }]
     *    }
     */
    public function comSiconfi()
    {
        $service = new ClassificacaoFonteRecursoService();

        return new DBJsonResponse(
            array_values($service->getComRecursosSiconfi()->toArray()),
            'Classificações com recursos'
        );
    }
}
