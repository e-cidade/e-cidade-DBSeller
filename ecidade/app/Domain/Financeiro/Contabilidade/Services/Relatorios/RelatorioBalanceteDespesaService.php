<?php


namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\Services\AssinaturaService;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Despesa\BalanceteDespesaAnalitico;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Despesa\BalanceteDespesaCsv;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Despesa\BalanceteDespesaSintetico;
use App\Domain\Financeiro\Contabilidade\Services\BalanceteDespesaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class RelatorioBalanceteDespesaService extends BalanceteDespesaService
{
    const ANALITICO = 'analitico';
    const SINTETICO = 'sintetico';


    /**
     * @var string[]
     */
    private $nomeInstituicoes;

    /**
     * @var string
     */
    private $modelo;

    private $funcoesAgrupadores = [
        'orgao' => 'criaOrgao',
        'unidade' => 'criaUnidade',
        'funcao' => 'criaFuncao',
        'subfuncao' => 'criaSubfuncao',
        'programa' => 'criaPrograma',
        'projeto' => 'criarProjetos',
        'elemento' => 'criarElementos',
        'recurso' => 'criaRecursoSintetico',
    ];


    /**
     * dados processados para impressão
     * @var array
     */
    private $dados = [];
    /**
     * @var object
     */
    private $totalizador;

    /**
     * @var AssinaturaService
     */
    private $assinatura;
    /**
     * @var string
     */
    private $apresentarRecurso;

    public function setFiltrosRequest(array $filtros)
    {
        $instituicoes = str_replace('\"', '"', $filtros['instituicoes']);
        $instituicoes = \JSON::create()->parse($instituicoes);

        $this->filtrarInstituicoes = collect($instituicoes)->map(function ($instituicao) {
            $this->nomeInstituicoes[] = DBConfig::find($instituicao->codigo)->nomeinstabrev;
            return $instituicao->codigo;
        });

        $filtro = str_replace('\"', '"', $filtros['filtros']);
        $this->filtro = \JSON::create()->parse($filtro);

        $this->filtroDataInicio = Carbon::createFromFormat('d/m/Y', $filtros['dataInicio']);
        $this->filtroDataFinal = Carbon::createFromFormat('d/m/Y', $filtros['dataFinal']);
        $this->ano = $this->filtroDataFinal->year;

        $this->apresentarRecurso = $filtros['apresentarRecurso'];
        $this->modelo = $filtros['modelo'];
        if (!empty($filtros['nivel'])) {
            $this->agruparPorClassificacao = $filtros['nivel'];
        }

        /**
         * A assinatura é da instituição logada.
         */
        $this->assinatura = new AssinaturaService($filtros['DB_instit']);
    }

    public function emitir()
    {
        $dados = $this->processar();

        if (empty($dados)) {
            throw new Exception("Sem registros para o filtro selecionado.", 403);
        }

        $relatorio = $this->getInstanceBalancete();
        $relatorio->setDados($dados);
        $relatorio->setApresentarRecurso($this->apresentarRecurso);
        $relatorio->setTotalizador($this->totalizador);

        $links = $relatorio->emitir();
        if ($this->modelo === self::ANALITICO) {
            $links = array_merge($links, $this->emitirCsv($dados));
        }

        return $links;
    }

    /**
     * @param $dados
     * @return array com os links do arquivo
     */
    public function emitirCsv($dados)
    {
        $relatorio = $this->getInstanciaBalanceteCSV();
        $relatorio->setDados($dados);
        return $relatorio->emitir();
    }

    /**
     * @return array
     */
    public function getDadosProcessados()
    {
        return $this->processar();
    }

    /**
     * @return array
     */
    private function processar()
    {
        if ($this->modelo === self::ANALITICO) {
            return $this->processaModeloAnalitico();
        } else {
            return $this->processaModeloSintetico();
        }
    }

    private function processaModeloAnalitico()
    {
        $sql = $this->sqlPrincipal();
        $dados = DB::select($sql);
        $this->totalizador = $this->criaStdValores();

        foreach ($dados as $dado) {
            if (!array_key_exists($dado->reduzido, $this->dados)) {
                $dado->recursos = [];
                $this->dados[$dado->reduzido] = $dado;
            }
            $this->criaRecurso($this->dados[$dado->reduzido]->recursos, $dado);
            $this->incrementaValores($this->totalizador, $dado);
        }

        return $this->dados;
    }

    private function processaModeloSintetico()
    {
        $sql = $this->sqlSintetico();

        $dados = DB::select($sql);
        $this->totalizador = $this->criaStdValores();

        foreach ($dados as $dado) {
            $this->incrementaValores($this->totalizador, $dado);

            $primeiroAgrupador = $this->agruparPorClassificacao[0];
            $function = $this->funcoesAgrupadores[$primeiroAgrupador];
            $this->$function($this->dados, $dado);
        }

        return $this->dados;
    }

    private function criaStdValores()
    {
        return (object)[
            'saldo_inicial' => 0,
            'saldo_anterior' => 0,
            'saldo_disponivel' => 0,
            'suplementado' => 0,
            'suplementado_especial' => 0,
            'reducoes' => 0,
            'empenhado' => 0,
            'empenhado_liquido' => 0,
            'anulado' => 0,
            'liquidado' => 0,
            'pago' => 0,
            'empenhado_acumulado' => 0,
            'empenhado_liquido_acumulado' => 0,
            'anulado_acumulado' => 0,
            'liquidado_acumulado' => 0,
            'pago_acumulado' => 0,
            'a_liquidar' => 0,
            'a_pagar' => 0,
            'a_pagar_liquidado' => 0
        ];
    }

    private function incrementaValores($valores, $dado)
    {
        $valores->saldo_inicial += $dado->saldo_inicial;
        $valores->saldo_anterior += $dado->saldo_anterior;
        $valores->saldo_disponivel += $dado->saldo_disponivel;
        $valores->suplementado += $dado->suplementado;
        $valores->suplementado_especial += $dado->suplementado_especial;
        $valores->reducoes += $dado->reducoes;
        $valores->empenhado += $dado->empenhado;
        $valores->empenhado_liquido += $dado->empenhado_liquido;
        $valores->anulado += $dado->anulado;
        $valores->liquidado += $dado->liquidado;
        $valores->pago += $dado->pago;
        $valores->empenhado_acumulado += $dado->empenhado_acumulado;
        $valores->empenhado_liquido_acumulado += $dado->empenhado_liquido_acumulado;
        $valores->anulado_acumulado += $dado->anulado_acumulado;
        $valores->liquidado_acumulado += $dado->liquidado_acumulado;
        $valores->pago_acumulado += $dado->pago_acumulado;
        $valores->a_liquidar += $dado->a_liquidar;
        $valores->a_pagar += $dado->a_pagar;
        $valores->a_pagar_liquidado += $dado->a_pagar_liquidado;
    }

    /**
     *
     * @param integer $codigo
     * @param string $descricao descrição do nível
     * @param string $formatado código formatado
     * @param string $nivel nome do nivel
     * @param string|null $filho
     * @return object
     */
    private function criaObjeto($codigo, $descricao, $formatado, $nivel, $filho = null)
    {
        $data = [
            'codigo' => $codigo,
            'formatado' => $formatado,
            'descricao' => $descricao,
            'nivel' => $nivel,
            'valores' => $this->criaStdValores(),
            'recursos' => [],
            $filho => []
        ];
        if (!is_null($filho)) {
            $data[$filho] = [];
        }
        return (object)$data;
    }

    private function createIfNotExists(array &$array, $id, $descricao, $formatado, $nivel, $filho = null)
    {
        if (!array_key_exists($id, $array)) {
            $array[$id] = $this->criaObjeto($id, $descricao, $formatado, $nivel, $filho);
        }

        return $array[$id];
    }

    private function criaOrgao(array &$dados, $dadosDespesa)
    {
        $orgao = $this->createIfNotExists(
            $dados,
            $dadosDespesa->orgao,
            $dadosDespesa->descricao_orgao,
            str_pad($dadosDespesa->orgao, 2, '0', STR_PAD_LEFT),
            'orgao',
            'filho'
        );


        $this->criaRecurso($orgao->recursos, $dadosDespesa);
        $this->incrementaValores($orgao->valores, $dadosDespesa);

        $funcao = $this->getProximaFuncao('orgao');
        if (!empty($funcao)) {
            $this->$funcao($orgao->filho, $dadosDespesa);
        }
    }

    private function criaUnidade(array &$unidades, $dadosDespesa)
    {
        $id = "$dadosDespesa->orgao#$dadosDespesa->unidade";
        $unidade = $this->createIfNotExists(
            $unidades,
            $id,
            $dadosDespesa->descricao_unidade,
            str_pad($dadosDespesa->unidade, 2, '0', STR_PAD_LEFT),
            'unidade',
            'filho'
        );

        $this->criaRecurso($unidade->recursos, $dadosDespesa);
        $this->incrementaValores($unidade->valores, $dadosDespesa);

        $funcao = $this->getProximaFuncao('unidade');
        if (!empty($funcao)) {
            $this->$funcao($unidade->filho, $dadosDespesa);
        }
    }

    private function criaFuncao(array &$funcoes, $dadosDespesa)
    {
        $funcao = $this->createIfNotExists(
            $funcoes,
            $dadosDespesa->funcao,
            $dadosDespesa->descricao_funcao,
            str_pad($dadosDespesa->funcao, 2, '0', STR_PAD_LEFT),
            'funcao',
            'filho'
        );

        $this->criaRecurso($funcao->recursos, $dadosDespesa);
        $this->incrementaValores($funcao->valores, $dadosDespesa);
        $function = $this->getProximaFuncao('funcao');
        if (!empty($function)) {
            $this->$function($funcao->filho, $dadosDespesa);
        }
    }

    private function criaSubfuncao(array &$subfuncoes, $dadosDespesa)
    {
        $subfuncao = $this->createIfNotExists(
            $subfuncoes,
            $dadosDespesa->subfuncao,
            $dadosDespesa->descricao_subfuncao,
            str_pad($dadosDespesa->subfuncao, 3, '0', STR_PAD_LEFT),
            'subfuncao',
            'filho'
        );

        $this->criaRecurso($subfuncao->recursos, $dadosDespesa);
        $this->incrementaValores($subfuncao->valores, $dadosDespesa);

        $funcao = $this->getProximaFuncao('subfuncao');
        if (!empty($funcao)) {
            $this->$funcao($subfuncao->filho, $dadosDespesa);
        }
    }

    private function criaPrograma(array &$programas, $dadosDespesa)
    {
        $programa = $this->createIfNotExists(
            $programas,
            $dadosDespesa->programa,
            $dadosDespesa->descricao_programa,
            str_pad($dadosDespesa->programa, 4, '0', STR_PAD_LEFT),
            'programa',
            'filho'
        );

        $this->criaRecurso($programa->recursos, $dadosDespesa);
        $this->incrementaValores($programa->valores, $dadosDespesa);

        $funcao = $this->getProximaFuncao('programa');
        if (!empty($funcao)) {
            $this->$funcao($programa->filho, $dadosDespesa);
        }
    }

    private function criarProjetos(array &$projetos, $dadosDespesa)
    {
        $projeto = $this->createIfNotExists(
            $projetos,
            $dadosDespesa->projeto,
            $dadosDespesa->descricao_projeto,
            str_pad($dadosDespesa->projeto, 4, '0', STR_PAD_LEFT),
            'projeto',
            'filho'
        );

        $this->criaRecurso($projeto->recursos, $dadosDespesa);
        $this->incrementaValores($projeto->valores, $dadosDespesa);
        $funcao = $this->getProximaFuncao('projeto');
        if (!empty($funcao)) {
            $this->$funcao($projeto->filho, $dadosDespesa);
        }
    }

    private function criarElementos(array &$elementos, $dadosDespesa)
    {
        $elemento = $this->createIfNotExists(
            $elementos,
            $dadosDespesa->elemento,
            $dadosDespesa->descricao_elemento,
            $dadosDespesa->elemento,
            'elemento',
            'filho'
        );

        $this->criaRecurso($elemento->recursos, $dadosDespesa);
        $this->incrementaValores($elemento->valores, $dadosDespesa);
        $funcao = $this->getProximaFuncao('elemento');
        if (!empty($funcao)) {
            $this->$funcao($elemento->filho, $dadosDespesa);
        }
    }

    private function criaRecursoSintetico(array &$recursos, $dadosDespesa)
    {
        $recurso = $this->createIfNotExists(
            $recursos,
            $dadosDespesa->gestao,
            $dadosDespesa->descricao_recurso,
            $dadosDespesa->gestao,
            'recurso',
            null
        );

        $this->criaRecurso($recurso->recursos, $dadosDespesa);
        $this->incrementaValores($recurso->valores, $dadosDespesa);
    }


    private function criaRecurso(array &$recursos, $dadosDespesa)
    {
        if (!array_key_exists($dadosDespesa->recurso, $recursos)) {
            $recursos[$dadosDespesa->recurso] = $this->criaObjetoRecurso($dadosDespesa);
        }

        $recurso = $recursos[$dadosDespesa->recurso];
        $this->incrementaValoresRecurso($recurso->valores, $dadosDespesa);
    }

    private function criaObjetoRecurso($dadosDespesa)
    {
        return (object)[
            'recurso' => $dadosDespesa->gestao,
            'siconfi' => $dadosDespesa->siconfi,
            'subrecurso' => $dadosDespesa->fonte_recurso,
            'descricao_recurso' => $dadosDespesa->descricao_recurso,
            'complemento' => $dadosDespesa->complemento,
            'descricao_complemento' => $dadosDespesa->descricao_complemento,
            'valores' => $this->criaStdValoresRecursos(),
        ];
    }

    private function criaStdValoresRecursos()
    {
        return (object)[
            'empenhado' => 0,
            'empenhado_liquido' => 0,
            'anulado' => 0,
            'liquidado' => 0,
            'pago' => 0,
            'empenhado_acumulado' => 0,
            'empenhado_liquido_acumulado' => 0,
            'anulado_acumulado' => 0,
            'liquidado_acumulado' => 0,
            'pago_acumulado' => 0,
        ];
    }

    private function incrementaValoresRecurso($valores, $dado)
    {
        $valores->empenhado += $dado->empenhado;
        $valores->empenhado_liquido += $dado->empenhado_liquido;
        $valores->anulado += $dado->anulado;
        $valores->liquidado += $dado->liquidado;
        $valores->pago += $dado->pago;
        $valores->empenhado_acumulado += $dado->empenhado_acumulado;
        $valores->empenhado_liquido_acumulado += $dado->empenhado_liquido_acumulado;
        $valores->anulado_acumulado += $dado->anulado_acumulado;
        $valores->liquidado_acumulado += $dado->liquidado_acumulado;
        $valores->pago_acumulado += $dado->pago_acumulado;
    }

    private function getProximaFuncao($classificacaoAtual)
    {
        $key = array_search($classificacaoAtual, $this->agruparPorClassificacao);
        $key++;
        if (array_key_exists($key, $this->agruparPorClassificacao)) {
            return $this->funcoesAgrupadores[$this->agruparPorClassificacao[$key]];
        }

        return null;
    }

    /**
     * @return BalanceteDespesaAnalitico|BalanceteDespesaSintetico
     */
    private function getInstanceBalancete()
    {
        if ($this->modelo === self::SINTETICO) {
            $relatorio = new BalanceteDespesaSintetico();
        } else {
            $relatorio = new BalanceteDespesaAnalitico();
        }

        $relatorio->setAssinaturas($this->assinatura->assinaturaPrefeito(), $this->assinatura->assinaturaContador());

        $periodo = sprintf(
            '%s até %s',
            $this->filtroDataInicio->format('d/m/Y'),
            $this->filtroDataFinal->format('d/m/Y')
        );

        $relatorio->headers($periodo, implode(', ', $this->nomeInstituicoes));
        return $relatorio;
    }

    /**
     * @return BalanceteDespesaCsv
     */
    public function getInstanciaBalanceteCSV()
    {
        return new BalanceteDespesaCsv();
    }
}
