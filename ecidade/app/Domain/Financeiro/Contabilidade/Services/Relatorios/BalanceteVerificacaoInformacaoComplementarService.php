<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao\BalanceteVerificacaoInformacaoComplementar;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BalanceteVerificacaoInformacaoComplementarService
{

    /**
     * @var \Illuminate\Support\Collection
     */
    private $filtrarInstituicoes;
    /**
     * @var Carbon
     */
    private $filtroDataInicio;
    /**
     * @var Carbon
     */
    private $filtroDataFinal;

    /**
     * @var mixed
     */
    private $exercicio;

    private $estruturais = [];
    private $estruturaisUniao = [];
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

    public function setFiltrosArray(array $filtros)
    {
        $this->filtrarInstituicoes = collect($filtros['instituicoes']);
        $this->filtroDataInicio = Carbon::createFromFormat('Y-m-d', $filtros['dataInicial']);
        $this->filtroDataFinal = Carbon::createFromFormat('Y-m-d', $filtros['dataFinal']);
        $this->comEncerramento = $filtros['comEncerramento'];
        $this->exercicio = $filtros['exercicio'];

        if (!empty($filtros['estruturais'])) {
            $this->estruturais = $filtros['estruturais'];
        }

        if (!empty($filtros['estruturaisUniao'])) {
            $this->estruturaisUniao = $filtros['estruturaisUniao'];
        }
        $this->indicadorSuperavit = $filtros['indicadorSuperavit'];
        $this->sistemaContas = $filtros['sistemaContas'];
        $this->contasComMovimento = $filtros['contasComMovimento'];
    }

    public function emitir()
    {
        $dados = $this->processar();

        if (empty($dados)) {
            throw new Exception("Sem registros para o filtro selecionado.", 403);
        }

        return $this->emitirCsv($dados);
    }

    private function emitirCsv($dados)
    {
        $relatorio = new BalanceteVerificacaoInformacaoComplementar();
        $relatorio->setDados($dados);

        return $relatorio->emitir();
    }

    /**
     * @return array
     */
    public function processar()
    {
        $instituicoes = $this->filtrarInstituicoes->implode(',');

        $sqlFiltraReduzidos = $this->sqlFiltrarReduzidos();
        if ($sqlFiltraReduzidos !== 'null') {
            $sqlFiltraReduzidos = "(select array_agg(c61_reduz) from ({$sqlFiltraReduzidos}) as x )::int[]";
        }

        $pl = sprintf(
            "contabilidade.matriz(%s, db_config.codigo, '%s', '%s', %s, %s)",
            $this->exercicio,
            $this->filtroDataInicio->format('Y-m-d'),
            $this->filtroDataFinal->format('Y-m-d'),
            $this->comEncerramento ? 'true' : 'false',
            $sqlFiltraReduzidos
        );

        $where = "
            db_config.codigo in ($instituicoes)
            and (saldo_anterior != 0 or saldo_debito != 0 or saldo_credito != 0 or saldo_final != 0)
        ";
        $sql = "
        select bl.*
          from db_config
          join {$pl} bl on bl.instituicao = db_config.codigo
         where {$where}
         order by estrutural, instituicao, siconfi, complemento, nr, nd;
        ";

        return DB::select($sql);
    }

    public function sqlFiltrarReduzidos()
    {
        $sqlFiltraReduzidos = 'null';
        $instituicoes = $this->filtrarInstituicoes->implode(',');
        $filtrarReduzidos = [];
        if ($this->indicadorSuperavit != 'T') {
            $filtrarReduzidos[] = "c60_identificadorfinanceiro = '{$this->indicadorSuperavit}'";
        }

        if ($this->sistemaContas != 99) {
            $filtrarReduzidos[] = "c60_consistemaconta = {$this->sistemaContas}";
        }
        if (!empty($this->estruturais)) {
            $filtrarReduzidos['estruturais'] = $this->montaFiltroEstrutural($this->estruturais, 'c60_estrut');
        }
        if (!empty($this->estruturaisUniao)) {
            $filtrarReduzidos['estruturais'] = $this->montaFiltroEstrutural($this->estruturaisUniao, 'conta');
            $filtrarReduzidos[] = 'uniao is true';
            $filtrarReduzidos[] = "exercicio = {$this->exercicio}";
        }

        if (!empty($filtrarReduzidos)) {
            $filtrarReduzidos[] = "c61_instit in ($instituicoes)";
            $filtrarReduzidos[] = "c61_anousu = {$this->exercicio}";
            $filtrar = implode(' and ', $filtrarReduzidos);

            $sqlReduz = "
            select c61_reduz
              from contabilidade.conplano
              join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
              join contabilidade.pcaspconplano ON conplano_codigo = c60_codigo
              join contabilidade.pcasp ON pcasp.id = pcasp_id
             where {$filtrar}
            ";
            $sqlFiltraReduzidos = "select c61_reduz from ({$sqlReduz}) as x";
        }

        return $sqlFiltraReduzidos;
    }

    private function montaFiltroEstrutural($estruturais, $campo)
    {
        $like = [];
        foreach ($estruturais as $estrutural) {
            $like[] = "{$campo} like '{$estrutural}%'";
        }
        return sprintf('(%s)', implode(' or ', $like));
    }
}
