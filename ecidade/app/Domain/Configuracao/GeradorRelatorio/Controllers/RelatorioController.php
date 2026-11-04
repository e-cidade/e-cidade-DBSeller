<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Controllers;

use App\Domain\Configuracao\GeradorRelatorio\Enums\TipoVisualizacaoRelatorioEnum;
use App\Domain\Configuracao\GeradorRelatorio\Models\Relatorio;
use App\Domain\Configuracao\GeradorRelatorio\Repositories\RelatorioRepository;
use App\Domain\Configuracao\GeradorRelatorio\Requests\ImportarTemplateRelatorioRequest;
use App\Domain\Configuracao\GeradorRelatorio\Requests\SalvarRelatorioRequest;
use App\Domain\Configuracao\GeradorRelatorio\Services\RelatorioService;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
class RelatorioController extends Controller
{
    /**
     * @param Request $request
     * @param RelatorioRepository $repository
     * @return DBJsonResponse
     * @throws \ReflectionException
     *
     * @api {get} configuracao/gerador/relatorios 01 - Buscar relatorios
     * @apiName BuscarRelatorios
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiQuery {Number=1,2,3,4} tipoVisualizacao=3 Tipo de visualizacao dos dados.
     *                                               1 => Relatorios do usuario.
     *                                               2 => Relatorios por Departamento
     *                                               3 => Relatorios Publicos
     *                                               4 => Relatorios do BI
     * @apiSuccess {Object[]} data Array de relatorios
     * @apiSuccess {Number} data.codigo codigo do relatorio
     * @apiSuccess {String} data.descricao descricao do relatorio
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiSuccessExample {json} Exemplo:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *            "codigo": 1,
     *            "descricao": "Relatorio 1"
     *         },
     *         {
     *            "codigo": 2,
     *            "descricao": "Relatorio 2"
     *         },
     *       ],
     *       "error": false,
     *       "message": ""
     *     }
     *
     * @apiUse Error
     */
    public function index(Request $request, RelatorioRepository $repository)
    {
        $tipoVisualizacao = (int)$request->get('tipoVisualizacao', TipoVisualizacaoRelatorioEnum::PUBLICO);
        $dados = $repository->getByVisualizacao(new TipoVisualizacaoRelatorioEnum($tipoVisualizacao));

        return new DBJsonResponse($dados);
    }

    /**
     * @param Relatorio $relatorio
     * @param RelatorioService $service
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {get} configuracao/gerador/relatorios/:id 02 - Buscar relatorio por codigo
     * @apiName BuscarRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiParam {Number} id Codigo do relatorio
     *
     * @apiSuccess {Object} data Objeto com os dados do relatorio
     *
     * @apiSuccess {Object} data.grupo Grupo do relatorio.
     * @apiSuccess {Integer} data.grupo.codigo Codigo do grupo.
     * @apiSuccess {String} data.grupo.descricao Descricao do grupo.
     *
     * @apiSuccess {Object} data.tipo Tipo do relatorio.
     * @apiSuccess {Integer} data.tipo.codigo Codigo do tipo.
     * @apiSuccess {String} data.tipo.descricao Descricao do tipo.
     *
     * @apiSuccess {Integer} data.origem Origem do relatorio.
     * @apiSuccess {Boolean} data.isOrigemSql Determina se o relatorio é de origem sql.
     *
     * @apiSuccess {String} data.sql Query em que o relatorio sera baseado
     *
     * @apiSuccess {Object} data.campos Campos
     * @apiSuccess {Object[]} data.campos.naoConfigurados Campos nao configurados
     * @apiSuccess {Integer|null} data.campos.naoConfigurados.codigo Codigo do campo.
     * @apiSuccess {String} data.campos.naoConfigurados.nome Nome do campo.
     * @apiSuccess {String} data.campos.naoConfigurados.alias Alias do campo, exibido no relatorio.
     * @apiSuccess {Integer} data.campos.naoConfigurados.largura Largura do campo no relatorio.
     * @apiSuccess {String} data.campos.naoConfigurados.alinhamento Alinhamento do campo no relatorio.
     * c=Centro, l=Esquerda, r=Direita
     * @apiSuccess {String} data.campos.naoConfigurados.alinhamentoCabecalho Alinhamento do campo no cabecalho
     * c=Centro, l=Esquerda, r=Direita
     * @apiSuccess {String} data.campos.naoConfigurados.mascara Formatacao do campo.
     * t=Texto livre, d=Data, m=Moeda
     * @apiSuccess {String} data.campos.naoConfigurados.totalizar Tolizador do campo.
     * n=Nao, s=Soma, q=Quantidade
     * @apiSuccess {Boolean} data.campos.naoConfigurados.quebra Define se o campo deve quebrar ou nao.
     *
     * @apiSuccess {Object[]} data.campos.configurados Campos configurados
     * @apiSuccess {Integer|null} data.campos.configurados.codigo Codigo do campo.
     * @apiSuccess {String} data.campos.configurados.nome Nome do campo.
     * @apiSuccess {String} data.campos.configurados.alias Alias do campo, exibido no relatorio.
     * @apiSuccess {Integer} data.campos.configurados.largura Largura do campo no relatorio.
     * @apiSuccess {String} data.campos.configurados.alinhamento Alinhamento do campo no relatorio.
     * c=Centro, l=Esquerda, r=Direita
     * @apiSuccess {String} data.campos.configurados.alinhamentoCabecalho Alinhamento do campo no cabecalho.
     * c=Centro, l=Esquerda, r=Direita
     * @apiSuccess {String} data.campos.configurados.mascara Formatacao do campo.
     * t=Texto livre, d=Data, m=Moeda
     * @apiSuccess {String} data.campos.configurados.totalizar Tolizador do campo.
     * n=Nao, s=Soma, q=Quantidade
     * @apiSuccess {Boolean} data.campos.configurados.quebra Define se o campo deve quebrar ou nao.
     *
     * @apiSuccess {Object} data.ordem Ordenacao dos campos
     *
     * @apiSuccess {Object[]} data.ordem.naoConfigurados
     * @apiSuccess {Integer|null} data.ordem.naoConfigurados.codigo Codigo da ordem
     * @apiSuccess {String} data.ordem.naoConfigurados.nome Nome do campo
     * @apiSuccess {String} data.ordem.naoConfigurados.alias Alias do campo
     * @apiSuccess {String=asc,desc} data.ordem.naoConfigurados.tipo Tipo de ordenacao
     *
     * @apiSuccess {Object[]} data.ordem.configurados
     * @apiSuccess {Integer|null} data.ordem.configurados.codigo Codigo da ordem
     * @apiSuccess {String} data.ordem.configurados.nome Nome do campo
     * @apiSuccess {String} data.ordem.configurados.alias Alias do campo
     * @apiSuccess {String=asc,desc} data.ordem.configurados.tipo Tipo de ordenacao
     *
     * @apiSuccess {Object} data.layout Dados do layout do relatorio
     * @apiSuccess {String} data.layout.nome Nome do relatorio
     * @apiSuccess {String} data.layout.versao Versao a ser utilzada
     * @apiSuccess {String} data.layout.orientacao Orientacao do relatorio
     * @apiSuccess {String} data.layout.formato Formato do relatorio
     * @apiSuccess {String} data.layout.layout Layout do relatorio
     * @apiSuccess {String} data.layout.tipoSaida Tipo de saida do relatorio
     * @apiSuccess {Object} data.layout.margem Margens do relatorio
     * @apiSuccess {Number} data.layout.margem.direita Margem direita
     * @apiSuccess {Number} data.layout.margem.esquerda Margem esquerda
     * @apiSuccess {Number} data.layout.margem.inferior Margem inferior
     * @apiSuccess {Number} data.layout.margem.superior Margem superior
     *
     * @apiSuccess {Object[]} data.variaveis Variaveis do relatorio
     * @apiSuccess {String} data.variaveis.nome Nome da variavel.
     * @apiSuccess {String} data.variaveis.label Label da variavel a ser exibida.
     * @apiSuccess {String|Number} data.variaveis.default Valor default caso nao seja preenchida
     * @apiSuccess {String} data.variaveis.tipo Tipo de variavel
     * @apiSuccess {String} data.variaveis.sql SQL?
     *
     * @apiSuccess {Object[]} data.filtros Filtros do relatorio, utilizando quando for origem=2
     * @apiSuccess {String} data.filtros.operador Operador do filtro
     * @apiSuccess {String} data.filtros.campo Campo a ser comparado
     * @apiSuccess {String} data.filtros.condicao Condicao a ser comparado
     * @apiSuccess {String|Number} data.filtros.valor Valor a ser comparado ou variavel cadastrada
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiUse Error
     */
    public function show(Relatorio $relatorio, RelatorioService $service)
    {
        return new DBJsonResponse($service->load($relatorio));
    }

    /**
     * @param Request $request
     * @param RelatorioService $service
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {post} configuracao/gerador/relatorios/novo 03 - Iniciar novo relatorio
     * @apiName IniciarRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiDescription Rota utilizada para iniciar um novo relatorio com valores default, com base em um sql ou visao.
     *
     * @apiBody {String} [sql] Query em que o relatorio sera baseado.
     * @apiBody {String} [visao] Visao em que o relatorio sera baseado.
     *
     * @apiSuccess {Relatorio} data Objeto com os dados do relatorio
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiUse Error
     */
    public function begin(Request $request, RelatorioService $service)
    {
        return new DBJsonResponse($service->build($request->sql, $request->visao));
    }

    /**
     * @param SalvarRelatorioRequest $request
     * @param RelatorioService $service
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {post} configuracao/gerador/relatorios 04 - Cadastrar relatorio
     * @apiName CriarRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiBody {Integer} grupo Grupo do relatorio.
     * @apiBody {Integer} tipo Tipo do relatorio.
     * @apiBody {Integer=1,2} origem Origem do relatorio.
     * @apiBody {Integer=1,2,3,4} tipoVisualizacao Tipo de visualizacao do relatorio.
     *
     * @apiBody {String} [sql] Query em que o relatorio sera baseado. Obrigatorio se origem=1
     * @apiBody {String} [visao] Visao em que o relatorio sera baseado. Obrigatorio se origem=2
     *
     * @apiBody {Object} relatorio Dados do relatorio.
     * @apiBody {Object[]} relatorio.campos Campos configurados.
     * @apiBody {Integer|null} relatorio.campos.codigo Codigo do campo.
     * @apiBody {String} relatorio.campos.nome Nome do campo.
     * @apiBody {String} relatorio.campos.alias Alias do campo, exibido no relatorio.
     * @apiBody {Integer} relatorio.campos.largura Largura do campo no relatorio.
     * @apiBody {String=c,l,r} relatorio.campos.alinhamento Alinhamento do campo no relatorio.
     * c=Centro, l=Esquerda, r=Direita
     * @apiBody {String=c,l,r} relatorio.campos.alinhamentoCabecalho Alinhamento do campo no cabecalho do relatorio.
     * c=Centro, l=Esquerda, r=Direita
     * @apiBody {String=t,d,m} relatorio.campos.mascara Formatacao do campo.
     * t=Texto livre, d=Data, m=Moeda
     * @apiBody {String=n,s,q} relatorio.campos.totalizar Tolizador do campo.
     * n=Nao, s=Soma, q=Quantidade
     * @apiBody {Boolean} relatorio.campos.quebra Define se o campo deve quebrar ou nao.
     *
     * @apiBody {Object[]} relatorio.ordem Ordenacao dos campos
     * @apiBody {Integer|null} relatorio.ordem.codigo Codigo da ordem
     * @apiBody {String} relatorio.ordem.nome Nome do campo
     * @apiBody {String} relatorio.ordem.alias Alias do campo
     * @apiBody {String=asc,desc} relatorio.ordem.tipo Tipo de ordenacao
     *
     * @apiBody {Object} relatorio.layout Dados do layout do relatorio
     * @apiBody {String} relatorio.layout.nome Nome do relatorio
     * @apiBody {String} relatorio.layout.versao Versao a ser utilzada
     * @apiBody {String} relatorio.layout.orientacao Orientacao do relatorio
     * @apiBody {String} relatorio.layout.formato Formato do relatorio
     * @apiBody {String} relatorio.layout.layout Layout do relatorio
     * @apiBody {String} relatorio.layout.tipoSaida Tipo de saida do relatorio
     * @apiBody {Object} relatorio.layout.margem Margens do relatorio
     * @apiBody {Number} relatorio.layout.margem.direita Margem direita
     * @apiBody {Number} relatorio.layout.margem.esquerda Margem esquerda
     * @apiBody {Number} relatorio.layout.margem.inferior Margem inferior
     * @apiBody {Number} relatorio.layout.margem.superior Margem superior
     *
     * @apiBody {Object[]} relatorio.variaveis Variaveis do relatorio
     * @apiBody {String} relatorio.variaveis.nome Nome da variavel.
     * @apiBody {String} relatorio.variaveis.label Label da variavel a ser exibida.
     * @apiBody {String|Number} relatorio.variaveis.default Valor default caso nao seja preenchida
     * @apiBody {String} relatorio.variaveis.tipo Tipo de variavel
     * @apiBody {String} relatorio.variaveis.sql SQL?
     *
     * @apiBody {Object[]} relatorio.filtros Filtros do relatorio, utilizando quando for origem=2
     * @apiBody {String=and,or} relatorio.filtros.operador Operador do filtro
     * @apiBody {String} relatorio.filtros.campo Campo a ser comparado
     * @apiBody {String} relatorio.filtros.condicao Condicao a ser comparado
     * @apiBody {String|Number} relatorio.filtros.valor Valor a ser comparado ou variavel cadastrada
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiUse Error
     */
    public function store(SalvarRelatorioRequest $request, RelatorioService $service)
    {
        $relatorio = $service->salvar((object)$request->all());

        return new DBJsonResponse($relatorio->db63_sequencial, 'Relatório criado com sucesso.', 201);
    }

    /**
     * @param Relatorio $relatorio
     * @param SalvarRelatorioRequest $request
     * @param RelatorioService $service
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {put} configuracao/gerador/relatorios/:id 05 - Alterar relatorio
     * @apiName AlterarRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiParam {Integer} id Codigo do relatorio a ser alterado
     *
     * @apiBody {Integer} grupo Grupo do relatorio.
     * @apiBody {Integer} tipo Tipo do relatorio.
     * @apiBody {Integer=1,2} origem Origem do relatorio.
     * @apiBody {Integer=1,2,3,4} tipoVisualizacao Tipo de visualizacao do relatorio.
     *
     * @apiBody {String} [sql] Query em que o relatorio sera baseado. Obrigatorio se origem=1
     * @apiBody {String} [visao] Visao em que o relatorio sera baseado. Obrigatorio se origem=2
     *
     * @apiBody {Object} relatorio Dados do relatorio.
     * @apiBody {Object[]} relatorio.campos Campos configurados.
     * @apiBody {Integer|null} relatorio.campos.codigo Codigo do campo.
     * @apiBody {String} relatorio.campos.nome Nome do campo.
     * @apiBody {String} relatorio.campos.alias Alias do campo, exibido no relatorio.
     * @apiBody {Integer} relatorio.campos.largura Largura do campo no relatorio.
     * @apiBody {String=c,l,r} relatorio.campos.alinhamento Alinhamento do campo no relatorio.
     * c=Centro, l=Esquerda, r=Direita
     * @apiBody {String=c,l,r} relatorio.campos.alinhamentoCabecalho Alinhamento do campo no cabecalho do relatorio.
     * c=Centro, l=Esquerda, r=Direita
     * @apiBody {String=t,d,m} relatorio.campos.mascara Formatacao do campo.
     * t=Texto livre, d=Data, m=Moeda
     * @apiBody {String=n,s,q} relatorio.campos.totalizar Tolizador do campo.
     * n=Nao, s=Soma, q=Quantidade
     * @apiBody {Boolean} relatorio.campos.quebra Define se o campo deve quebrar ou nao.
     *
     * @apiBody {Object[]} relatorio.ordem Ordenacao dos campos
     * @apiBody {Integer|null} relatorio.ordem.codigo Codigo da ordem
     * @apiBody {String} relatorio.ordem.nome Nome do campo
     * @apiBody {String} relatorio.ordem.alias Alias do campo
     * @apiBody {String=asc,desc} relatorio.ordem.tipo Tipo de ordenacao
     *
     * @apiBody {Object} relatorio.layout Dados do layout do relatorio
     * @apiBody {String} relatorio.layout.nome Nome do relatorio
     * @apiBody {String} relatorio.layout.versao Versao a ser utilzada
     * @apiBody {String} relatorio.layout.orientacao Orientacao do relatorio
     * @apiBody {String} relatorio.layout.formato Formato do relatorio
     * @apiBody {String} relatorio.layout.layout Layout do relatorio
     * @apiBody {String} relatorio.layout.tipoSaida Tipo de saida do relatorio
     * @apiBody {Object} relatorio.layout.margem Margens do relatorio
     * @apiBody {Number} relatorio.layout.margem.direita Margem direita
     * @apiBody {Number} relatorio.layout.margem.esquerda Margem esquerda
     * @apiBody {Number} relatorio.layout.margem.inferior Margem inferior
     * @apiBody {Number} relatorio.layout.margem.superior Margem superior
     *
     * @apiBody {Object[]} relatorio.variaveis Variaveis do relatorio
     * @apiBody {String} relatorio.variaveis.nome Nome da variavel.
     * @apiBody {String} relatorio.variaveis.label Label da variavel a ser exibida.
     * @apiBody {String|Number} relatorio.variaveis.default Valor default caso nao seja preenchida
     * @apiBody {String} relatorio.variaveis.tipo Tipo de variavel
     * @apiBody {String} relatorio.variaveis.sql SQL?
     *
     * @apiBody {Object[]} relatorio.filtros Filtros do relatorio, utilizando quando for origem=2
     * @apiBody {String=and,or} relatorio.filtros.operador Operador do filtro
     * @apiBody {String} relatorio.filtros.campo Campo a ser comparado
     * @apiBody {String} relatorio.filtros.condicao Condicao a ser comparado
     * @apiBody {String|Number} relatorio.filtros.valor Valor a ser comparado ou variavel cadastrada
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiUse Error
     */
    public function update(Relatorio $relatorio, SalvarRelatorioRequest $request, RelatorioService $service)
    {
        $service->salvar((object)$request->all(), $relatorio);

        return new DBJsonResponse([], 'Relatório salvo com sucesso.');
    }

    /**
     * @param Relatorio $relatorio
     * @param RelatorioService $service
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {delete} configuracao/gerador/relatorios/:id 06 - Apagar relatorio
     * @apiName ApagarRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiParam {Number} id Codigo do relatorio
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiUse Error
     */
    public function destroy(Relatorio $relatorio, RelatorioService $service)
    {
        $service->apagar($relatorio);

        return new DBJsonResponse([], 'Relatório apagado com sucesso.');
    }

    /**
     * @param Relatorio $relatorio
     * @param ImportarTemplateRelatorioRequest $request
     * @param RelatorioService $service
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {post} configuracao/gerador/relatorios/:id/template 07 - Importar Template docx
     * @apiName ImportarTemplate
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiParam {Number} id Codigo do relatorio
     *
     * @apiBody {Blob="docx"} template Template do tipo docx a ser importado
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiUse Error
     */
    public function template(
        Relatorio $relatorio,
        ImportarTemplateRelatorioRequest $request,
        RelatorioService $service
    ) {
        $service->importarTemplate($relatorio, $request->template);

        return new DBJsonResponse([], 'Template importado com sucesso.');
    }
}
