<?php

namespace App\Domain\Financeiro\Orcamento\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Models\ConlancamRecurso;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamentoAnalitica;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;
use App\Domain\Financeiro\Orcamento\Models\Dotacao;
use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use App\Domain\Financeiro\Orcamento\Models\Receita;
use Illuminate\Support\Facades\DB;

class VinculosRecursoService extends Pdf
{
    /**
     * @var array
     */
    private $filtros = [];
    /**
     * @var bool
     */
    private $temVinculoPcasp;
    /**
     * @var bool
     */
    private $temVinculoOrcamentario;

    /**
     * @var bool
     */
    private $temVinculoReceita;

    /**
     * @var bool
     */
    private $temVinculoDespesa;
    /**
     * @var bool
     */
    private $temVinculoPlanejamento;
    /**
     * @var int
     */
    protected $fonte = 8;


    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function emitir()
    {
        $this->processar();

        $filename = sprintf('tmp/vinculo_recursos-%s.pdf', time());
        $this->Output('F', $filename);

        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', $this->fonte);
    }

    private function processar()
    {
        $fonteRecurso = FonteRecurso::with('recurso')
            ->where('orctiporec_id', $this->filtros['idRecurso'])
            ->where('exercicio', $this->filtros['exercicio'])
            ->first();

        if (is_null($fonteRecurso)) {
            throw new \Exception(sprintf(
                'Não foi encontrado o código de recurso "%s" no exercício "%s".',
                $this->filtros['idRecurso'],
                $this->filtros['exercicio']
            ));
        }

        $this->addTitulo('Vínculos do Recurso no sistema');
        $this->imprimeCabecalho();
        $this->bold();
        $this->multiCell(193, 4, 'Vínculos do recurso ' . $fonteRecurso->descricao, 0, 'C');

        $dados = sprintf(
            'Siconfi: %s - Gestão: %s - Subrecurso: %S - Complemento: %s',
            $fonteRecurso->codigo_siconfi,
            $fonteRecurso->gestao,
            $fonteRecurso->recurso->o15_recurso,
            $fonteRecurso->recurso->o15_complemento
        );

        $this->cell(193, 4, $dados, 1, 1, 'C');

        $this->ln();
        $this->imprimeVinculoPcasp();
        $this->ln();
        $this->imprimeVinculoPlanoOrcamentario();
        $this->ln();
        $this->imprimeVinculoReceitas();
        $this->ln();
        $this->imprimeVinculoDespesa();
        $this->ln();
        $this->imprimeVinculoPlanejamento();
        $this->ln();
        $this->imprimeVinculoLancamento();


        if (!$this->temVinculoPcasp &&
            !$this->temVinculoOrcamentario &&
            !$this->temVinculoReceita &&
            !$this->temVinculoDespesa &&
            !$this->temVinculoLancamento &&
            !$this->temVinculoPlanejamento) {
            $this->ln(8);
            $this->multiCell(193, 4, 'Esse recurso não possui nenhum vínculo', 0, 'C');
        }
    }


    private function imprimeVinculoPcasp()
    {
        $reduzidos = $this->reduzidosPcasp();
        if (count($reduzidos)) {
            $this->imprimePlanoContas('Vínculo plano de contas PCASP no exercício', $reduzidos);
        } else {
            $reduzidos = $this->reduzidosPcasp('<');
            if (count($reduzidos)) {
                $this->bold();
                $this->cell(193, 4, 'Possui vínculo com o plano de contas PCASP em exercícios anteriores.', 0, 1);
            }
        }

        $this->temVinculoPcasp = count($reduzidos) > 0;
        if (!$this->temVinculoPcasp) {
            $this->bold();
            $this->cell(193, 4, 'Não foi encontrado nenhum vínculo com o plano de contas PCASP.', 0, 1);
        }
    }

    private function imprimeVinculoPlanoOrcamentario()
    {
        $reduzidos = $this->reduzidosOrcamentario();
        if (count($reduzidos)) {
            $this->imprimePlanoContas('Vínculo plano de contas orçamentário no exercício', $reduzidos);
        } else {
            $reduzidos = $this->reduzidosPcasp('<');
            if (count($reduzidos)) {
                $this->bold();
                $this->cell(193, 4, 'Possui vínculo com o plano de contas orçamentário em exercícios anteriores', 0, 1);
            }
        }

        $this->temVinculoOrcamentario = count($reduzidos) > 0;
        if (!$this->temVinculoOrcamentario) {
            $this->bold();
            $this->cell(193, 4, 'Não foi encontrado nenhum vínculo com o plano de contas orçamentário.', 0, 1);
        }
    }

    private function cabecalhoPlanoContas($quebraPagina = false)
    {
        if ($quebraPagina) {
            $this->addPage();
        }
        $this->bold();
        $this->cell(64, 4, 'Estrutural', 1, 0, 'C');
        $this->cell(64, 4, 'Reduzido', 1, 0, 'C');
        $this->cell(64, 4, 'Instituição', 1, 1, 'C');
        $this->regular();
    }

    private function imprimePlanoContas($msg, $reduzidos)
    {
        $this->bold();
        $this->cell(193, 4, $msg, 0, 1);
        $this->cabecalhoPlanoContas();
        foreach ($reduzidos as $reduzido) {
            if ($this->getAvailHeight() < $this->hLinha) {
                $this->cabecalhoPlanoContas(true);
            }
            $this->cell(64, 4, $reduzido->c60_estrut, 1, 0, 'C');
            $this->cell(64, 4, $reduzido->c61_reduz, 1, 0, 'C');
            $this->cell(64, 4, $reduzido->c61_instit, 1, 1, 'C');
        }
    }


    private function imprimeVinculoReceitas()
    {
        $receitas = $this->receitas();
        if (count($receitas)) {
            $this->bold();
            $this->cell(193, 4, 'Vínculo das receitas no exercício', 0, 1);
            $this->cabecalhoPlanoContas();
            $this->regular();
            foreach ($receitas as $reduzido) {
                if ($this->getAvailHeight() < $this->hLinha) {
                    $this->cabecalhoPlanoContas(true);
                }
                $this->cell(64, 4, $reduzido->o57_fonte, 1, 0, 'C');
                $this->cell(64, 4, $reduzido->o70_codrec, 1, 0, 'C');
                $this->cell(64, 4, $reduzido->o70_instit, 1, 1, 'C');
            }
        } else {
            $receitas = $this->receitas('<');
            if (count($receitas)) {
                $this->bold();
                $this->cell(193, 4, 'Possui vínculo com receitas em exercícios anteriores', 0, 1);
            }
        }
        $this->temVinculoReceita = count($receitas) > 0;

        if (!$this->temVinculoReceita) {
            $this->bold();
            $this->cell(193, 4, 'Não foi encontrado nenhum vínculo com receitas', 0, 1);
        }
    }


    private function imprimeVinculoDespesa()
    {
        $dotacoes = $this->dotacoes();
        if (count($dotacoes)) {
            $this->bold();
            $this->cell(193, 4, 'Vínculo das dotações no exercício', 0, 1);
            $this->regular();
            $this->cabecalhoDotacoes();
            foreach ($dotacoes as $dotacao) {
                if ($this->getAvailHeight() < $this->hLinha) {
                    $this->cabecalhoDotacoes(true);
                }
                $this->cell(96.5, 4, $dotacao->o58_coddot, 1, 0, 'C');
                $this->cell(96.5, 4, $dotacao->o58_instit, 1, 1, 'C');
            }
        } else {
            $dotacoes = $this->receitas('<');
            if (count($dotacoes)) {
                $this->cell(193, 4, 'Possui vínculo com dotações em exercícios anteriores', 0, 1);
            }
        }
        $this->temVinculoDespesa = count($dotacoes) > 0;

        if (!$this->temVinculoDespesa) {
            $this->bold();
            $this->cell(193, 4, 'Não foi encontrado nenhum vínculo com dotações', 0, 1);
        }
    }

    private function cabecalhoDotacoes($quebraPagina = false)
    {
        if ($quebraPagina) {
            $this->addPage();
        }
        $this->bold();
        $this->cell(96.5, 4, 'Reduzido', 1, 0, 'C');
        $this->cell(96.5, 4, 'Instituição', 1, 1, 'C');
        $this->regular();
    }

    private function imprimeVinculoPlanejamento()
    {
        $this->temVinculoPlanejamento = $this->vinculoPlanejamento() > 0;
        $msg = "Possui vínculo no planejamento.";
        if (!$this->temVinculoPlanejamento) {
            $msg = "Não possui vínculo no planejamento.";
        }
        $this->bold();
        $this->cell(193, 4, $msg, 0, 1);
    }


    private function imprimeVinculoLancamento()
    {
        $this->temVinculoLancamento = $this->lancamento() > 0;
        $msg = "Possui vínculo com lançamentos contábeis.";
        if (!$this->temVinculoLancamento) {
            $msg = "Não possui vínculo com lançamentos contábeis.";
        }
        $this->bold();
        $this->cell(193, 4, $msg, 0, 1);
    }

    /**
     * @param string $operadorExercicio
     * @return ConplanoReduzido[]
     */
    private function reduzidosPcasp($operadorExercicio = '=')
    {
        return ConplanoReduzido::query()
            ->join('contabilidade.conplano', function ($join) {
                $join->on('c60_codcon', 'c61_codcon')
                    ->on('c60_anousu', 'c61_anousu');
            })
            ->where('c61_codigo', $this->filtros['idRecurso'])
            ->where('c61_anousu', $operadorExercicio, $this->filtros['exercicio'])
            ->orderBy('c60_estrut')
            ->get();
    }

    /**
     * @param string $operadorExercicio
     * @return ConplanoOrcamentoAnalitica[]
     */
    public function reduzidosOrcamentario($operadorExercicio = '=')
    {
        return ConplanoOrcamentoAnalitica::query()
            ->join('contabilidade.conplanoorcamento', function ($join) {
                $join->on('c60_codcon', 'c61_codcon')
                    ->on('c60_anousu', 'c61_anousu');
            })
            ->where('c61_codigo', $this->filtros['idRecurso'])
            ->where('c61_anousu', $operadorExercicio, $this->filtros['exercicio'])
            ->orderBy('c60_estrut')
            ->get();
    }

    /**
     * @param string $operadorExercicio
     * @return Receita[]
     */
    public function receitas($operadorExercicio = '=')
    {
        return Receita::query()
            ->join('orcfontes', function ($join) {
                $join->on('o57_codfon', 'o70_codfon')
                    ->on('o57_anousu', 'o70_anousu');
            })
            ->where('o70_codigo', $this->filtros['idRecurso'])
            ->where('o70_anousu', $operadorExercicio, $this->filtros['exercicio'])
            ->orderBy('o57_fonte')
            ->get();
    }

    /**
     * @param string $operadorExercicio
     * @return Dotacao[]
     */
    public function dotacoes($operadorExercicio = '=')
    {
        return Dotacao::query()
            ->where('o58_codigo', $this->filtros['idRecurso'])
            ->where('o58_anousu', $operadorExercicio, $this->filtros['exercicio'])
            ->orderBy('o58_coddot')
            ->get();
    }

    /**
     * @return mixed
     */
    public function lancamento()
    {
        return ConlancamRecurso::query()
            ->where('c130_orctiporec', $this->filtros['idRecurso'])
            ->count();
    }

    /**
     * @return integer
     */
    private function vinculoPlanejamento()
    {
        $recurso = $this->filtros['idRecurso'];
        $sql = "
        select count(*)
          from (
            select 1 from estimativareceita
             where recurso_id = {$recurso}
             union all
            select 1 from detalhamentoiniciativa
             where pl20_recurso = {$recurso}
        ) as x
        ";

        return DB::select(DB::raw($sql))[0]->count;
    }
}
