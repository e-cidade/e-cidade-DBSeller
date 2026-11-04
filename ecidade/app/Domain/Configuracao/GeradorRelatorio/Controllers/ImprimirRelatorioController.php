<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Controllers;

use App\Domain\Configuracao\GeradorRelatorio\Services\ImprimirRelatorioService;
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
class ImprimirRelatorioController extends Controller
{
    /**
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {post} configuracao/gerador/relatorios/imprimir 08 - Imprimir Relatorio
     * @apiName ImprimirRelatorio
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
    public function handle(ImprimirRelatorioService $service)
    {
        return new DBJsonResponse($service->execute(), 'Relatório gerado com sucesso!');
    }
}
