<?php
namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\LRF\RREO\AnexosRREORequest;
use App\Domain\Financeiro\Contabilidade\Requests\LRF\RREO\AnexosRGFRequest;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO\AnexoTresService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO\AnexoQuatroService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO\AnexoSeisService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO\AnexoSeteService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO\AnexoOitoService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO\AnexoDozeService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO\AnexoTrezeService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF\AnexoUmService as AnexoUmRGFService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF\AnexoDoisService as AnexoDoisRGFService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF\AnexoTresService as AnexoTresRGFService;
use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF\AnexoQuatroService as AnexoQuatroRGFService;
use App\Http\Controllers\Controller;
use Exception;

class SIAIController extends Controller
{
    public function anexoTresRREO(AnexosRREORequest $request)
    {
        $relatorio = new AnexoTresService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo III - Receita Corrente Líquida - RCL');
    }
    
    public function anexoQuatroRREO(AnexosRREORequest $request)
    {
        $relatorio = new AnexoQuatroService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo IV - Demonstrativo das Receitas e Despesas do RPPS');
    }
    
    public function anexoSeisRREO(AnexosRREORequest $request)
    {
        $relatorio = new AnexoSeisService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, ' Anexo VI - Demonstrativo dos Resultados Primário e Nominal');
    }
    
    public function anexoSeteRREO(AnexosRREORequest $request)
    {
        $relatorio = new AnexoSeteService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo VII - Demonstrativo dos Restos a Pagar');
    }
    
    public function anexoOitoRREO(AnexosRREORequest $request)
    {
        $relatorio = new AnexoOitoService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo VIII - Demonstrativo de Receitas. e Despesas MDE (FUNDEB)');
    }
    
    public function anexoDozeRREO(AnexosRREORequest $request)
    {
        $relatorio = new AnexoDozeService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo XII - Demonstrativo Saúde');
    }
    
    public function anexoTrezeRREO(AnexosRREORequest $request)
    {
        $relatorio = new AnexoTrezeService($request->get('DB_anousu'), $request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo XIII - Demonstrativo das PPPs');
    }
    
    public function anexoUmRGF(AnexosRGFRequest $request)
    {
        $relatorio = new AnexoUmRGFService($request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo I - Demonstrativo da Despesa com Pessoal');
    }
    
    public function anexoDoisRGF(AnexosRGFRequest $request)
    {
        $relatorio = new AnexoDoisRGFService($request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo II - Demonstrativo da Dívida Consolidada Líquida');
    }
    
    public function anexoTresRGF(AnexosRGFRequest $request)
    {
        $relatorio = new AnexoTresRGFService($request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo III - Demonstrativo das Garantias e Contragarantias de Valores');
    }
    
    public function anexoQuatroRGF(AnexosRGFRequest $request)
    {
        $relatorio = new AnexoQuatroRGFService($request->all());
        $files = $relatorio->gerarArquivo();
        return new DBJsonResponse($files, 'Anexo IV - Demonstrativo das Operações de Crédito');
    }
}
