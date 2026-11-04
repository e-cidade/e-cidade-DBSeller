<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\Services\AssinaturaService;
use App\Domain\Financeiro\Contabilidade\Models\Conplano;
use App\Domain\Financeiro\Contabilidade\Models\Pcasp;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao\BalanceteVerificacaoCsv;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao\BanlanceteVerificacaoPdf;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao\BanlanceteVerificacaoPlanoPadraoPdf;
use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao\BanlanceteVerificacaoSinteticoPdf;
use App\Domain\Financeiro\Contabilidade\Requests\Relatorios\BalanceteVerificacaoRequest;
use Carbon\Carbon;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalPcaspPadrao;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class RelatorioBalanceteVerificacaoService
{
    /**
     * @var mixed
     */
    protected $estruturais;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $filtrarInstituicoes;
    /**
     * @var array
     */
    private $nomeInstituicoes;
    /**
     * @var Carbon
     */
    private $filtroDataInicio;
    /**
     * @var Carbon
     */
    private $filtroDataFinal;
    /**
     * @var int
     */
    protected $exercicio;
    /**
     * @var AssinaturaService
     */
    private $assinatura;

    /**
     * Array com o plano de contas, dependendo o tipo do plano selecionado
     * @var Conplano[]|Pcasp[]
     */
    protected $planoContas;

    /**
     * Tipo de plano de contas
     * Valores válidos: ecidade, uniao, uf
     * @var string
     */
    private $tipoPlano;

    /**
     * Filtra as contas conforme os indicadores de superávit
     * Valores válidos
     * T - Todos
     * N - Não se aplica
     * F - Financeiro
     * P - Permanente
     * @var string
     */
    protected $indicadorSuperavit;

    /**
     * Filtra as contas conforme o Sistema de contas
     * Valores válidos
     * 99 - Todos
     * 0  - Não aplicável
     * 1  - Sistema Orçamentário
     * 2  - Sistema Patrimonial
     * 3  - Sistema de Controle
     * @var integer
     */
    protected $sistemaContas;

    /**
     * Executa a PL buscando os valores dos documentos de encerramento
     * @var boolean
     */
    protected $comEncerramento;

    /**
     * Apresenta apenas contas com movimento
     * @var boolean
     */
    protected $contasComMovimento;

    /**
     * Nível de subtítulo da conta
     * @var string
     */
    private $subtitulo;

    /**
     * Apresenta apenas as contas sintéticas
     * @var boolean
     */
    protected $sintetico;

    /**
     * Apresenta o nome da conta bancária ao lado da conta
     * @var boolean
     */
    protected $exibirContaBancaria;

    /**
     * Se apresenta os dados consolidado por reduzido ou por estrutural
     * @var boolean
     */
    protected $consolidarPorReduzido;

    /**
     * Lista dos recursos a serem filtrados
     * @var array
     */
    protected $recursos;

    /**
     * Array com todas as contas bancárias
     * @var array
     */
    private $contasBancarias = [];


    public function setFiltrosRequest(BalanceteVerificacaoRequest $filtros)
    {
        $this->setFiltrosArray($filtros->all());
    }

    public function setFiltrosArray(array $filtros)
    {
        $this->filtrarInstituicoes = collect($filtros['instituicoes']);
        DBConfig::whereIn('codigo', $this->filtrarInstituicoes)->get()->map(function (DBConfig $config) {
            $this->nomeInstituicoes[] = $config->nomeinstabrev;
        });

        $this->tipoPlano = $filtros['tipoPlano'];
        $this->estruturais = $filtros['estruturais'];
        $this->filtroDataInicio = Carbon::createFromFormat('Y-m-d', $filtros['dataInicial']);
        $this->filtroDataFinal = Carbon::createFromFormat('Y-m-d', $filtros['dataFinal']);
        $this->exercicio = $filtros['exercicio'];

        $this->indicadorSuperavit = $filtros['indicadorSuperavit'];
        $this->sistemaContas = $filtros['sistemaContas'];
        $this->comEncerramento = $filtros['comEncerramento'];
        $this->contasComMovimento = $filtros['contasComMovimento'];
        $this->subtitulo = $filtros['subtitulo'];
        $this->sintetico = $filtros['sintetico'];
        $this->exibirContaBancaria = $filtros['exibirContaBancaria'];
        $this->consolidarPorReduzido = $filtros['consolidarPorReduzido'];
        $this->recursos = $filtros['recursos'];

        /**
         * A assinatura é da instituição logada.
         */
        $this->assinatura = new AssinaturaService($filtros['DB_instit']);
        if ($this->tipoPlano === 'ecidade' && $this->exibirContaBancaria) {
            $this->carregaContasBancarias();
        }
    }

    public function emitir()
    {
        $dados = $this->processar();

        if (empty($dados)) {
            throw new Exception("Sem registros para o filtro selecionado.", 403);
        }

        return array_merge($this->emitirPdf($dados), $this->emitirCsv($dados));
    }

    public function emitirPdf($dados)
    {
        $periodo = (object)[
            "dataInicio" => $this->filtroDataInicio,
            "dataFim" => $this->filtroDataFinal
        ];

        $relatorio = $this->getInstanciaEmissaoPdf();
        $relatorio->setTipoPlano($this->tipoPlano)
            ->setTipo($this->sintetico)
            ->setExibeContaBancaria($this->exibirContaBancaria)
            ->setPorReduzido($this->consolidarPorReduzido)
            ->setDados($dados)
            ->headers('Balancete de Verificação', $periodo, $this->nomeInstituicoes, $this->tipoPlano);
        return $relatorio->emitir();
    }

    private function emitirCsv($dados)
    {
        $relatorio = new BalanceteVerificacaoCsv();
        $relatorio->setDados($dados);

        return $relatorio->emitir();
    }

    private function processar()
    {
        $arvore = $this->montaArvore($this->buscarDados());
        return $arvore;
    }

    private function plExecutar()
    {
        $instituicoes = $this->filtrarInstituicoes->implode(',');

        $where = ["c61_instit in ($instituicoes)", "c61_anousu = {$this->exercicio}"];
        if (!empty($this->estruturais)) {
            $estruturais = [];
            foreach ($this->estruturais as $estrutural) {
                $estruturais[] = "c60_estrut like '$estrutural%'";
            }
            $where[] = sprintf('(%s)', implode(' or ', $estruturais));
        }
        if ($this->indicadorSuperavit != 'T') {
            $where[] = "c60_identificadorfinanceiro = '{$this->indicadorSuperavit}'";
        }

        if ($this->sistemaContas !== 99) {
            $where[] = "c60_consistemaconta = {$this->sistemaContas}";
        }

        if (!empty($this->subtitulo)) {
            $where[] = "c60_estrut  like '____{$this->subtitulo}%'";
        }

        $where = implode(' and ', $where);

        $sqlFiltraReduzidos = "
        select c61_reduz
          from contabilidade.conplano
          join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
         where {$where}
        ";
        $reduzidos = DB::select($sqlFiltraReduzidos);
        if (empty($reduzidos)) {
            $str = "Não foi possível buscar dados do balancete de verificação para os filtros informados.";
            throw new Exception($str, 400);
        }

        $sqlReduz = "(select array_agg(c61_reduz) from ({$sqlFiltraReduzidos}) as x )::int[]";

        $encerramento = $this->comEncerramento ? 'true' : 'false';
        $datIni = $this->filtroDataInicio->format('Y-m-d');
        $datFim = $this->filtroDataFinal->format('Y-m-d');

        if ($this->tipoPlano === 'ecidade') {
            $pl = "contabilidade.balancete_verificacao_por_recurso";
            return "{$pl}($this->exercicio, '{$datIni}', '$datFim', $encerramento, $sqlReduz)";
        }

        if ($this->tipoPlano !== 'ecidade') {
            $pl = "contabilidade.balancete_verificacao_plano_padrao";
            $uniao = $this->tipoPlano === 'uniao' ? 'true' : 'false';
            return "{$pl}($this->exercicio, $uniao, '{$datIni}', '$datFim', $encerramento, $sqlReduz)";
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function buscarDados()
    {
        $whereRecursos = '';
        if (!empty($this->recursos)) {
            $whereRecursos = sprintf("where id_recurso in (%s)", implode(',', $this->recursos));
        }

        $ordem = $this->tipoPlano === 'ecidade' ? 'estrutural, principal desc' : 'estrutural';
        $pl = $this->plExecutar();
        $sql = "
            select x.*, codtrib as orgao_unidade
              from {$pl} x
              join configuracoes.db_config on db_config.codigo = instituicao
              {$whereRecursos}
             order by {$ordem}
        ";

        $contas = DB::select($sql);
        if (empty($contas)) {
            $str = "Não foi possível buscar dados do balancete de verificação para os filtros informados.";
            throw new Exception($str, 400);
        }

        return $contas;
    }

    /**
     * Valida se a conta tem movimentação
     * @param stdClass $conta
     * @return bool
     */
    protected function temMovimentacao($conta)
    {
        if ($conta->saldo_anterior != 0 ||
            $conta->saldo_debito != 0 ||
            $conta->saldo_credito != 0 ||
            $conta->saldo_final != 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $dadosBalancete
     * @return array
     * @throws Exception
     */
    private function montaArvore($dadosBalancete)
    {
        $arvore = [];

        foreach ($dadosBalancete as $conta) {
            if ($this->contasComMovimento && !$this->temMovimentacao($conta)) {
                continue;
            }

            $estrutural = $this->estruturalFormatter($conta->estrutural);
            $nivel = $estrutural->getNivel();
            $hash = $this->montaHash($conta);

            if (array_key_exists($hash, $arvore)) {
                $arvore[$hash]->saldo_anterior += $conta->saldo_anterior;
                $arvore[$hash]->saldo_debito += $conta->saldo_debito;
                $arvore[$hash]->saldo_credito += $conta->saldo_credito;
                $arvore[$hash]->saldo_final += $conta->saldo_final;
            }

            // Alterado adição da conta na árvore em função das opções de totalizar as contas analíticas por estrutural
            if (!array_key_exists($hash, $arvore)) {
                $arvore[$hash] = $this->stdContaAnalitica($conta, $nivel);
            }

            list($estrutural, $arvore) = $this->montaContaPai($nivel, $estrutural, $arvore, $conta);
        }

        ksort($arvore);
        return $arvore;
    }

    /**
     * @return Conplano[]|Pcasp[]
     */
    protected function getPlanoContas()
    {
        if (empty($this->planoContas)) {
            $this->planoContas = getPlanoPcasp($this->tipoPlano, $this->exercicio);
        }
        return $this->planoContas;
    }

    protected function estruturalFormatter($estrutural)
    {
        return new EstruturalPcaspPadrao($estrutural);
    }


    /**
     * @param $nivel
     * @param EstruturalPcaspPadrao $estrutural
     * @param array $arvore arvore montada até o momento
     * @param stdClass $dadosConta Dados da conta que retorna da query
     * @return array
     * @throws Exception
     */
    private function montaContaPai($nivel, EstruturalPcaspPadrao $estrutural, array $arvore, $dadosConta)
    {
        while ($nivel != 1) {
            $estrutural = $this->estruturalFormatter($estrutural->getCodigoEstruturalPai());
            $contaPlano = $this->buscaContaPlano($estrutural);

            $estrutural = $this->estruturalFormatter($contaPlano->conta);
            $hash = $estrutural->getEstrutural();
            $nivel = $estrutural->getNivel();

            if (!array_key_exists($hash, $arvore)) {
                $arvore[$hash] = $this->stdContaSintetica($dadosConta, $nivel, $contaPlano);
            }
            $arvore[$hash]->saldo_anterior += $dadosConta->saldo_anterior;
            $arvore[$hash]->saldo_debito += $dadosConta->saldo_debito;
            $arvore[$hash]->saldo_credito += $dadosConta->saldo_credito;
            $arvore[$hash]->saldo_final += $dadosConta->saldo_final;
        }

        return [$estrutural, $arvore];
    }

    /**
     * @param EstruturalPcaspPadrao $estrutural
     * @return Pcasp|Conplano
     */
    protected function buscaContaPlano(EstruturalPcaspPadrao $estrutural)
    {
        $planoContas = $this->getPlanoContas();
        if (!array_key_exists($estrutural->getEstrutural(), $planoContas)) {
            $this->buscaContaPlano($estrutural->getEstruturalPai());
        }

        return $planoContas[$estrutural->getEstrutural()];
    }

    /**
     * @param $dadosConta
     * @param $nivel
     * @return object
     */
    protected function stdConta($dadosConta, $nivel)
    {
        $estrutural = $this->estruturalFormatter($dadosConta->estrutural);
        return (object)[
            "estrutural" => $dadosConta->estrutural,
            "mascara" => $estrutural->getEstruturalComMascara(),
            "exercicio" => $this->exercicio,
            "classe" => $dadosConta->classe,
            "nome" => $dadosConta->nome,
            "instituicao" => $dadosConta->instituicao,
            "indicador_superavit" => $dadosConta->indicador_superavit,
            "natureza_informacao" => $dadosConta->natureza_informacao,
            "reduzidos" => [],
            "codigos_conplano" => [],
            "id_recurso" => null,
            "orgao_unidade" => null,
            "siconfi" => null,
            "gestao" => null,
            "subrecurso" => null,
            "complemento" => null,
            "sintetica" => false,
            "nivel" => $nivel,
            "sinal_anterior" => null,
            "sinal_final" => null,
            "saldo_anterior" => 0,
            "saldo_debito" => 0,
            "saldo_credito" => 0,
            "saldo_final" => 0,
        ];
    }

    /**
     * @param stdClass $dadosConta
     * @param $nivel
     * @return object
     */
    protected function stdContaAnalitica(stdClass $dadosConta, $nivel)
    {
        $std = $this->stdConta($dadosConta, $nivel);
        $std->nome = $this->getNomeConta($dadosConta);

        $std->reduzidos = $this->sanitizeReduzidos($dadosConta);
        $std->codigos_conplano = $this->sanitizeCodigoConplano($dadosConta);
        $std->orgao_unidade = $dadosConta->orgao_unidade;
        $std->id_recurso = $dadosConta->id_recurso;
        $std->siconfi = $dadosConta->siconfi;
        $std->gestao = $dadosConta->gestao;
        $std->subrecurso = $dadosConta->subrecurso;
        $std->complemento = $dadosConta->complemento;
        $std->saldo_anterior = $dadosConta->saldo_anterior;
        $std->saldo_debito = $dadosConta->saldo_debito;
        $std->saldo_credito = $dadosConta->saldo_credito;
        $std->saldo_final = $dadosConta->saldo_final;
        $std->sinal_anterior = $dadosConta->sinal_anterior;
        $std->sinal_final = $dadosConta->sinal_final;
        return $std;
    }

    /**
     * @param stdClass $dadosConta
     * @param $nivel
     * @param Pcasp|Conplano $conplano
     * @return object
     */
    protected function stdContaSintetica(stdClass $dadosConta, $nivel, $conplano)
    {
        $estrutural = $this->estruturalFormatter($conplano->conta);
        $std = $this->stdConta($dadosConta, $nivel);
        $std->sintetica = true;
        $std->instituicao = null;
        $std->estrutural = $conplano->conta;
        $std->mascara = $estrutural->getEstruturalComMascara();
        $std->nome = $conplano->nome;
        $std->indicador_superavit = 'N';
        $std->natureza_informacao = 'P';

        return $std;
    }

    protected function carregaContasBancarias()
    {
        $sql = "
        select c56_contabancaria as id,
               db89_db_bancos as banco,
               db83_conta as conta,
               db83_dvconta as digito_conta,
               db89_codagencia as agencia,
               db89_digito as agencia_digito
        from conplanocontabancaria
        join configuracoes.contabancaria on db83_sequencial = c56_contabancaria
        join configuracoes.bancoagencia on db89_sequencial = db83_bancoagencia
        where conplanocontabancaria.c56_anousu = $this->exercicio
        ";

        collect(DB::select($sql))->each(function ($contaBancaria) {
            $this->contasBancarias[$contaBancaria->id] = $this->montaStringBanco($contaBancaria);
        });
    }

    /**
     * @param $dados
     * @return string
     */
    protected function montaStringBanco($dados)
    {
        return sprintf('Bco: %s Age: %s Cta: %s', $dados->banco, $dados->agencia, $dados->conta);
    }

    protected function montaHash(stdClass $conta)
    {
        $estrutural = $this->estruturalFormatter($conta->estrutural)->getEstrutural();
        if ($this->tipoPlano === 'uniao') {
            return "{$estrutural}#{$conta->orgao_unidade}#{$conta->id_recurso}";
        }

        if ($this->tipoPlano === 'ecidade') {
            if (!$this->consolidarPorReduzido) {
                return "{$estrutural}";
            }

            return "{$estrutural}#{$conta->orgao_unidade}#{$conta->id_recurso}";
        }
    }

    /**
     * Retorna o nome da conta
     * @param stdClass $conta dados da conta bancária
     * @return string
     */
    protected function getNomeConta(stdClass $conta)
    {
        $nome = $conta->nome;
        if ($this->tipoPlano === 'ecidade' && $this->exibirContaBancaria) {
            if (array_key_exists($conta->conta_bancaria_id, $this->contasBancarias)) {
                $nome .= " ({$this->contasBancarias[$conta->conta_bancaria_id]})";
            }
        }

        return $nome;
    }

    protected function removeContasAnaliticas(array $arvore)
    {
        $sinteticas = [];
        foreach ($arvore as $key => $conta) {
            if ($conta->sintetica) {
                $sinteticas[$key] = $conta;
            }
        }
        return $sinteticas;
    }

    protected function sanitizeReduzidos($dadosConta)
    {
        $valor =$this->tipoPlano === 'ecidade' ? $dadosConta->reduzido : $dadosConta->reduzidos;
        return $this->sanitizePropriedade($valor);
    }

    protected function sanitizeCodigoConplano(stdClass $dadosConta)
    {
        $valor =$this->tipoPlano === 'ecidade' ? $dadosConta->codigo_conplano : $dadosConta->codigos_conplano;
        return $this->sanitizePropriedade($valor);
    }

    protected function sanitizePropriedade($valor)
    {
        if ($this->tipoPlano === 'ecidade') {
            return [$valor];
        }

        return explode(',', str_replace(['{', '}'], '', $valor));
    }

    /**
     * @return BanlanceteVerificacaoPdf|BanlanceteVerificacaoPlanoPadraoPdf
     */
    public function getInstanciaEmissaoPdf()
    {
        if ($this->sintetico) {
            return new BanlanceteVerificacaoSinteticoPdf();
        }
        if ($this->tipoPlano === 'ecidade') {
            return new BanlanceteVerificacaoPdf();
        }
        return new BanlanceteVerificacaoPlanoPadraoPdf();
    }
}
