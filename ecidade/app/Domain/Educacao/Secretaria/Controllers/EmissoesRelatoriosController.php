<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Secretaria\Relatorios\Alunos\AlunosEstrangeirosCSV;
use App\Domain\Educacao\Secretaria\Relatorios\Alunos\AlunosEstrangeirosPdf;
use App\Domain\Educacao\Secretaria\Services\AlunosEstrangeirosService;
use App\Domain\Educacao\Escola\Services\RelatorioAtividadeService;
use App\Domain\Educacao\Secretaria\Relatorios\Atividades\AtividadesProfissionaisPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmissoesRelatoriosController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} educacao/secretaria/emissoes-relatorios/alunos-estrangeiros
     * 01 - Emitir Relatório de Alunos Estrangeiros
     * @apiName EmitirRelatorioAlunosEstrangeiros
     * @apiGroup Educacao-Secretaria
     * @apiPermission usuario
     * @apiVersion 1.0.0
     *
     * @apiDescription Retorna um link de download do arquivo gerado
     *
     * @apiParam {Array} escolas IDs das escolas para as quais o relatório será gerado.
     * @apiParam {Number} ano Ano para o qual o relatório será gerado.
     * @apiParam {Array} tipos Tipos de relatórios a serem gerados ('csv', 'pdf').
     *
     * @apiSuccess {String[]} data Array de URLs dos relatórios gerados.
     * @apiSuccess {String} data.url URL de download do relatório gerado.
     * @apiSuccess {false} error Indica que não ocorreu erro.
     *
     * @apiSuccessExample {json} Exemplo:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *            "url": "http://exemplo.com/relatorio1.pdf"
     *         },
     *         {
     *            "url": "http://exemplo.com/relatorio2.csv"
     *         }
     *       ],
     *       "error": false,
     *       "message": ""
     *     }
     */
    public function emitirAlunosEstrangeiros(Request $request, AlunosEstrangeirosService $service)
    {
        $retorno = [];
        $escolas = $request->get('escolas');
        $ano = $request->get('ano');
        $tipos = $request->get('tipo');
        $dados = $service->getDadosRelatorio($escolas, $ano);
        foreach ($tipos as $tipo) {
            if ($tipo == 'csv') {
                $retorno[] = (new AlunosEstrangeirosCSV($dados))->emitir();
            }

            if ($tipo == 'pdf') {
                $retorno[] = (new AlunosEstrangeirosPdf($dados))->emitir();
            }
        }

        return new DBJsonResponse($retorno);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {get} educacao/secretaria/emissoes-relatorios/atividades 02 - Emitir Relatório de Atividades Profissionais
     * @apiName EmitirRelatorioAtividadesProfissionais
     * @apiGroup Educacao-Secretaria
     * @apiPermission usuario
     * @apiVersion 1.0.0
     *
     * @apiDescription Retorna um link de download do arquivo gerado
     *
     * @apiParam {Array} parametros Parâmetros para o relatório.
     *
     * @apiSuccess {File} Arquivo de relatório gerado (formato: PDF).
     * @apiSuccess {false} error Indica que não ocorreu erro.
     *
     * @apiSuccessExample {json} Exemplo:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *            "url": "http://exemplo.com/relatorio.pdf"
     *         }
     *       ],
     *       "error": false,
     *       "message": ""
     *     }
     */
    public function emitirAtividades(Request $request, RelatorioAtividadeService $service)
    {
        $retorno = [];
        $dados = $service->getDados($request->all());
        $retorno[] = (new AtividadesProfissionaisPdf($dados))->emitir();

        return new DBJsonResponse($retorno);
    }
}
