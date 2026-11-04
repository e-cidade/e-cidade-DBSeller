<?php


namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use DBDate;
use DateTime;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use ECidade\Financeiro\Contabilidade\Relatorio\DemonstrativoFiscal;
use App\Domain\Financeiro\Contabilidade\Relatorios\EvolucaoReceitaXls;

class EvolucaoReceitaService
{
    /**
     * Estrutural da receita
     * @var string
     */
    private $filtrarNatureza;

    /**
     * Lista de IDs das instituições selecionadas
     * @var \Illuminate\Support\Collection
     */
    private $filtrarInstituicoes;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $filtrarRecursos;

    /**
     * @var Carbon
     */
    private $filtroDataInicio;

    /**
     * @var Carbon
     */
    private $filtroDataFinal;

    /**
     * @var array
     */
    private $filtrarOrgaoUnidade = [];

    /**
     * @var integer
     */
    private $agrupador;
    /**
     * @var bool
     */
    private $filtrarApenasComMovimentacao = false;
    /**
     * @var int
     */
    private $ano;

    /**
     * @var string[]
     */
    private $nomeInstituicoes = [];

    /**
     * @var array
     */
    private $mesesProcessar = [];

    /**
     * @var string
     */
    private $db_inst = '';

    /**
     * @var Instituicao
     */
    private $emissor;

    /**
     * @var string
     */
    private $enteFederativo;

    private $parser;

    public function __construct()
    {
        $this->parser = new EvolucaoReceitaXls('storage/financeiro/modelo_evolucao_receita.xlsx');
    }

    public function setFiltrosRequest(array $filtros)
    {

        if (!empty($filtros['instituicoes'])) {
            $instituicoes = str_replace('\"', '"', $filtros['instituicoes']);
            $instituicoes = \JSON::create()->parse($instituicoes);

            $this->filtrarInstituicoes = collect($instituicoes)->map(function ($instituicao) {
                $this->nomeInstituicoes[] = $instituicao->nome;
                return $instituicao->codigo;
            });
        }

        if (!empty($filtros['DB_instit'])) {
            $this->db_inst = $filtros['DB_instit'];
        }

        if (!empty($filtros['mes'])) {
            $this->filtroMes = $filtros['mes'];
            $meses = $this->getMesesProcessar($filtros['mes']);
            $this->mesesProcessar = $meses;

            $mesFinal = array_pop($meses);

            $filtros["natureza"] = "";
            $filtros["nivel_agrupar"] = "0";
            $filtros["apenasComMovimentacao"] = "1";
            $filtros['dataInicio'] = Carbon::createFromFormat('Y-m-d', $mesFinal->data_inicio)->format('d/m/Y');
            $filtros['dataFinal'] = Carbon::createFromFormat('Y-m-d', $mesFinal->data_fim)->format('d/m/Y');

            $balancete = new BalanceteReceitaComplementoService();
            $balancete->setFiltrosRequest($filtros);
            $this->dados = $balancete->getArvore();
            foreach ($this->dados as $dado) {
                $dado->{$mesFinal->coluna} = $dado->arrecadado_periodo;
            }


            foreach ($meses as $mes) {
                $filtros['dataInicio'] = Carbon::createFromFormat('Y-m-d', $mes->data_inicio)->format('d/m/Y');
                $filtros['dataFinal'] = Carbon::createFromFormat('Y-m-d', $mes->data_fim)->format('d/m/Y');
                $balancete = new BalanceteReceitaComplementoService();
                $balancete->setFiltrosRequest($filtros);
                $dadosMensal = $balancete->getArvore();

                foreach ($this->dados as $key => $dado) {
                    $dado->{$mes->coluna} = 0;
                    if (!empty($dadosMensal) && array_key_exists($key, $dadosMensal)) {
                        $dado->{$mes->coluna} = $dadosMensal[$key]->arrecadado_periodo;
                    }
                }
            }
        }

        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($this->db_inst);
        $this->processaEnteFederativo();
    }

    private function somaUltimosDozeMeses($dado)
    {
        $total = 0;
        $range = range(1, 12);
        foreach ($range as $mesNumero) {
            $total += $dado->{'mes_'.$mesNumero};
        }

        return $total;
    }

    public function emitir()
    {
        $this->organizaLinhas();
        $this->dados = [];
        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }
        $this->linhasOrganizadas = [];

        foreach ($this->mesesProcessar as $mesProcessar) {
            $mes = "{$mesProcessar->nome_abreviado}/{$mesProcessar->ano}";
            $this->parser->setVariavel($mesProcessar->coluna, $mes);
        }

        $this->parser->setVariavel('instituicoes', implode(", ", $this->nomeInstituicoes));

        $mes1 = $this->mesesProcessar[0];
        $mes12 = $this->mesesProcessar[11];

        $mesesPeriodo = sprintf(
            '%s - %s',
            "{$mes1->nome}/{$mes1->ano}",
            "{$mes12->nome}/{$mes12->ano}"
        );

        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo(mb_strtoupper($this->mesesProcessar[11]->nome, 'ISO-8859-1'));
        $this->parser->setMesesPeriodo($mesesPeriodo);
        $this->mesesProcessar = [];
        $filename = $this->parser->gerar();
        return [
            'xls' => $filename,
            'xlsLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function getMesesProcessar($mes)
    {
        $mesesProcessar = [];
        $exercicio = db_getsession("DB_anousu");
        $mesFinal = $mes;

        $dataInical = new DateTime("{$exercicio}-01-01");
        $dia = cal_days_in_month(CAL_GREGORIAN, $mesFinal, $exercicio);
        $dataFinal = new DBDate("{$dia}/{$mesFinal}/{$exercicio}");
        if ($dataFinal->getMes() != 12) {
            $mesInicial = $dataFinal->getMes() + 1;
            $ano = $dataFinal->getAno() - 1;
            $dataInical = new DateTime("{$ano}-{$mesInicial}-01");
        }

        $meses = 1;
        $listaMeses = \DBDate::getMesesExtenso();
        while ($meses <= 12) {
            $mes = $dataInical->format('m');
            $ano = $dataInical->format('Y');
            $dia = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            $mesesProcessar[] = (object)[
                'nome' => $listaMeses[(int)$mes],
                'nome_abreviado' => \DBDate::getMesAbreviado((int)$mes),
                'mes' => $mes,
                'ano' => $ano,
                'coluna' => "mes_{$meses}",
                'data_inicio' => $dataInical->format('Y-m-d'),
                'data_fim' => "{$ano}-{$mes}-{$dia}",
            ];

            $dataInical->modify('+1 month');
            $meses++;
        }

        return $mesesProcessar;
    }

    protected function processaEnteFederativo()
    {
        $this->enteFederativo = DemonstrativoFiscal::getEnteFederativo($this->emissor);
        if ($this->emissor->getTipo() != \Instituicao::TIPO_PREFEITURA) {
            $this->enteFederativo .= "\n" . $this->emissor->getDescricao();
        }
    }

    /**
     * Quando template é por session, esse metodo joga as linhas dentro de cada sessão
     */
    protected function organizaLinhas()
    {
        if (empty($this->dados)) {
            throw new Exception("Não foram encontrados valores de receitas.");
        }

        foreach ($this->dados as $dado) {
            $dado->total_acumulado_12_meses = $this->somaUltimosDozeMeses($dado);
            $this->linhasOrganizadas['receitas'][] = $dado;
        }
    }
}
