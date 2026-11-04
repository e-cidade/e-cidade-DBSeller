<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\RecursosHumanos\Pessoal\Services\AjudaCusto;

use App\Domain\RecursosHumanos\Pessoal\Model\AjudaCusto;
use App\Domain\RecursosHumanos\Pessoal\Model\ConfiguracaoAjudaCusto;
use App\Domain\RecursosHumanos\Pessoal\Model\LancamentoAjudaCusto;
use App\Domain\RecursosHumanos\Pessoal\Model\Ponto\PontoSalario;
use App\Domain\RecursosHumanos\Pessoal\Model\RhDepend;
use App\Domain\RecursosHumanos\Pessoal\Model\RhPessoal;
use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use App\Domain\RecursosHumanos\Pessoal\Requests\AjudaCusto\AjudaCustoConfigRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\AjudaCusto\AjudaCustoRequest;
use Carbon\Carbon;
use DBCompetencia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ServidorRepository;

class AjudaCustoService
{
    public function saveConfigFromRequest(AjudaCustoConfigRequest $request)
    {
        $configAjudaCusto = new ConfiguracaoAjudaCusto();

        if ($request->id) {
            $configAjudaCusto = ConfiguracaoAjudaCusto::find($request->id);
        }

        $configAjudaCusto->rh311_instit           = $request->DB_instit;
        $configAjudaCusto->rh311_valormax         = $request->valor_limite;
        $configAjudaCusto->rh311_percentual       = $request->percentual;
        $configAjudaCusto->rh311_limite           = $request->maximo_dependente;
        $configAjudaCusto->rh311_idademax         = $request->idade_maxima;
        $configAjudaCusto->rh311_rubric           = $request->rubrica;
        $configAjudaCusto->rh311_validadependente = $request->habilitaDependente;
        $configAjudaCusto->rh311_rubricdepend     = !$request->habilitaDependente ? null : $request->rubricaDependente;
        $configAjudaCusto->rh311_grauparentesco   = json_encode((object)$request->tiposParentescos);
        $configAjudaCusto->rh311_servidordependente   = $request->permiteServidorDependente;
        $configAjudaCusto->save();
    }

    public function getConfigByInstit($instituicao)
    {
        $configAjudaCusto = new ConfiguracaoAjudaCusto();
        return $configAjudaCusto->configuracao($instituicao);
    }

    public function getServidoresLancados(Request $request)
    {
        $dadosAjudaCusto = AjudaCusto::with('dependente')
            ->with('servidor')
            ->where('rh312_instit', '=', $request->DB_instit)
            ->get();

        $data = [];
        foreach ($dadosAjudaCusto as $ajudaCusto) {
            $data[] = [
                "id"          => $ajudaCusto->rh312_sequencial,
                "matricula"   => $ajudaCusto->servidor->rh01_regist,
                "nome"        => $ajudaCusto->servidor->cgm->z01_nome,
                "dependente"  => $ajudaCusto->dependente ? $ajudaCusto->dependente->rh31_nome : null,
                "valor"       => $ajudaCusto->rh312_valor,
                "competencia" => $ajudaCusto->rh312_competencia,
            ];
        }

        return $data;
    }

    public function lancamentos($id)
    {
        $lancamento = AjudaCusto::find($id);

        list($mes, $ano) = explode("/", $lancamento->rh312_competencia);
        
        $startDate = Carbon::create($ano, $mes, 1);
        $endDate = clone $startDate;
        $endDate->addMonths($lancamento->rh312_quantidade);

        $competenciasAteFinal = [];
        while ($startDate->lessThanOrEqualTo($endDate)) {
            $competenciasAteFinal[] = $startDate->format('m/Y');
            $startDate->addMonth();
        }

        $competencias = $lancamento->lancamentos->map(function ($lancado) use (&$competenciasAteFinal) {
            $competenciaProcessada = Carbon::create($lancado->rh313_ano, $lancado->rh313_mes, 1);
            $processado = array_search($competenciaProcessada->format('m/Y'), $competenciasAteFinal);
            if ($processado !== null) {
                unset($competenciasAteFinal[$processado]);
                return (object)[
                    'referencia' => $competenciaProcessada->format('m/Y'),
                    'valor'      => $lancado->rh313_valor,
                    'processado' => true
                ];
            }
        });

        foreach ($competenciasAteFinal as $referencia) {
            $competencias->push([
                'referencia' => $referencia,
                'valor' => $lancamento->rh312_valor,
                'processado' => false,
            ]);
        }

        $competencias = array_filter($competencias->toArray());
        ksort($competencias);

        return [
            'competencias' => $competencias
        ];
    }

    public function salvar(AjudaCustoRequest $request)
    {
        $ajudaCusto = new AjudaCusto();

        if (!empty($request->id)) {
            $ajudaCusto = AjudaCusto::find($request->id);
        }

        $ajudaCusto->rh312_instit              = $request->DB_instit;
        $ajudaCusto->rh312_regist              = $request->matricula;
        $ajudaCusto->rh312_habilita_dependente = $request->habilitaDependente;
        $ajudaCusto->rh312_dependente          = !$request->habilitaDependente ? null : $request->codigo_dependente;
        $ajudaCusto->rh312_local               = $request->local;
        $ajudaCusto->rh312_especializacao      = $request->especializacao;
        $ajudaCusto->rh312_graduacao           = $request->graduacao;
        $ajudaCusto->rh312_unidade_ensino      = $request->unidade_ensino;
        $ajudaCusto->rh312_valor               = $request->valor;
        $ajudaCusto->rh312_quantidade          = $request->quantidade;
        $ajudaCusto->rh312_competencia         = $request->competencia;
        $ajudaCusto->rh312_ativo               = true;
        $ajudaCusto->rh312_bolsa_publica       = $request->bolsa_publica;

        $this->validaLancamento($ajudaCusto);

        $ajudaCusto->save();
    }

    public function excluir($id)
    {
        $ajudasLancandas = AjudaCusto::with('lancamentos')->find($id);
        $competencia = CompetenciaHelper::get();

        $ajudasLancandas->lancamentos->each(function ($lancamento) use ($competencia, $ajudasLancandas) {
            if ($competencia->getAno() !== $lancamento->rh313_ano
                && $competencia->getMes() !== $lancamento->rh313_mes) {
                    throw new Exception("Já existe ajuda de custo lançado em outra competência da folha.");
            }

            $pontofs = new PontoSalario();
            $pontofs->r10_anousu = $lancamento->rh313_ano;
            $pontofs->r10_mesusu = $lancamento->rh313_mes;
            $pontofs->r10_regist = $ajudasLancandas->rh312_regist;
            $pontofs->r10_rubric = $lancamento->rh313_rubric;
            $pontofs->delete();
            $lancamento->delete();
        });

        AjudaCusto::find($id)->delete();
    }

    public function getAjudaCusto($id)
    {
        $ajudaCusto = AjudaCusto::with('servidor')
            ->with('dependente')
            ->with('unidadeEnsino')
            ->find($id);

        return [
            'id' => $ajudaCusto->rh312_sequencial,
            'matricula' => $ajudaCusto->rh312_regist,
            'habilitaDependente' => $ajudaCusto->rh312_habilita_dependente,
            'codigo_dependente' => $ajudaCusto->rh312_dependente,
            'local' => $ajudaCusto->rh312_local,
            'especializacao' => $ajudaCusto->rh312_especializacao,
            'graduacao' => $ajudaCusto->rh312_graduacao,
            'unidade_ensino' => $ajudaCusto->unidadeEnsino->z01_nome,
            'codigo_unidade' => $ajudaCusto->unidadeEnsino->z01_numcgm,
            'valor' => (float)$ajudaCusto->rh312_valor,
            'quantidade' => $ajudaCusto->rh312_quantidade,
            'competencia' => $ajudaCusto->rh312_competencia,
            'servidor' => $ajudaCusto->servidor->cgm->z01_nome,
            'dependente' => $ajudaCusto->dependente ? $ajudaCusto->dependente->rh31_nome : null,
            'bolsa_publica' => $ajudaCusto->rh312_bolsa_publica
        ];
    }


    private function validaLancamento(AjudaCusto $ajudaCusto)
    {
        $configuracao = $this->getConfigByInstit($ajudaCusto->rh312_instit);
        $totalLancado = AjudaCusto::where('rh312_regist', '=', $ajudaCusto->rh312_regist)
            ->where('rh312_ativo', '=', 't');

        if ($ajudaCusto->rh312_sequencial) {
            $totalLancado->where('rh312_sequencial', '!=', $ajudaCusto->rh312_sequencial);
        }
        $lancamentosEfetuados = $totalLancado->get();

        if ($ajudaCusto->rh312_dependente) {
            if (!empty($configuracao->rh311_limite)) {
                if ($lancamentosEfetuados->count() >= $configuracao->rh311_limite) {
                    throw new Exception("Limite de dependentes atingido.", 400);
                }
            }

            $dependente = RhDepend::with('dependeplug')
                ->find($ajudaCusto->rh312_dependente);

            $this->validaLancamentoByDepentente($dependente, $ajudaCusto->rh312_sequencial);

            if ($dependente->idade() > $configuracao->rh311_idademax) {
                throw new Exception("Dependente já atingiu a idade limite.", 400);
            }

            $idadeLancamento = Carbon::parse($dependente->rh31_dtnasc)
                ->subMonth($ajudaCusto->rh312_quantidade)->age;

            if ($idadeLancamento > $configuracao->rh311_idademax) {
                throw new Exception("Dependente irá atingir a idade limite no período informado.", 400);
            }
        }

        if (!$configuracao->rh311_servidordependente && $lancamentosEfetuados->count() > 0) {
            throw new Exception("Já existe uma ajuda de custo lançada para o servidor ou para seu dependente", 400);
        }

        if (!$ajudaCusto->rh312_dependente) {
            $servidor = RhPessoal::with('cgm')->find($ajudaCusto->rh312_regist);
            $this->validaLancamentoByServidor($servidor, $ajudaCusto->rh312_sequencial);
        }
    }

    public function validaLancamentoByDepentente(RhDepend $dependente, $id = null)
    {
        $cpf = $dependente->dependeplug->dp01_cpf;
        $ajudasLancadas = AjudaCusto::join('rhdepend', 'rh31_codigo', 'rh312_dependente')
            ->join('rhdependeplug', 'dp01_rhdepend', 'rh31_codigo')
            ->where('dp01_cpf', '=', $cpf)
            ->where('rh312_ativo', '=', 't');

        if ($id) {
            $ajudasLancadas->where('rh312_sequencial', '!=', $id);
        }

        if (count($ajudasLancadas->get()) > 0) {
            throw new Exception("Já existe lançamentos ativos de ajuda para esse dependente", 400);
        }
    }

    public function validaLancamentoByServidor(RhPessoal $servidor, $id = null)
    {
        $cpf = $servidor->cgm->z01_cgccpf;
        $ajudasLancadas = AjudaCusto::join('rhpessoal', 'rh01_regist', 'rh312_regist')
            ->join('cgm', 'z01_numcgm', 'rh01_numcgm')
            ->where('z01_cgccpf', '=', $cpf)
            ->where('rh312_habilita_dependente', '=', 'f')
            ->where('rh312_ativo', '=', 't');

        if ($id) {
            $ajudasLancadas->where('rh312_sequencial', '!=', $id);
        }

        if (count($ajudasLancadas->get()) > 0) {
            throw new Exception("Já existe lançamentos ativos de ajuda para esse servidor", 400);
        }
    }

    public function processar(Request $request)
    {
        $configuracao = $this->getConfigByInstit($request->DB_instit);
        $competencia = CompetenciaHelper::get();

        LancamentoAjudaCusto::where('rh313_instit', '=', $request->DB_instit)
            ->where('rh313_ano', '=', $competencia->getAno())
            ->where('rh313_mes', '=', $competencia->getMes())
            ->delete();


        $dadosProcessamento = AjudaCusto::where('rh312_ativo', '=', 't')
            ->where('rh312_instit', '=', $request->DB_instit);

        $dados = $dadosProcessamento->get();
        $inativacao = [];

        foreach ($dados as $lancamento) {
            list($mes, $ano) = explode("/", $lancamento->rh312_competencia);

            if ($lancamento->totalLancamentos() == 0 &&
                "{$ano}{$mes}" > "{$competencia->getAno()}{$competencia->getMes()}"
            ) {
                continue;
            }

            if ($lancamento->totalLancamentos() == $lancamento->rh312_quantidade) {
                $inativacao[] = $lancamento;
                continue;
            }

            $servidor = ServidorRepository::getInstanciaByCodigo($lancamento->rh312_regist);
            if (!$servidor || $servidor->getTipoRegime() == 3) {
                continue;
            }

            if ($lancamento->totalLancamentos() > $lancamento->rh312_quantidade) {
                throw new Exception(
                    "Inconsistência encontrada, valores lançados superiores a quantidade de parcelas.\n" .
                    "Total de lancamento efetuados : {$lancamento->totalLancamentos()}\n" .
                    "Total Previsto : {$lancamento->rh312_quantidade}\n" .
                    "Verificar o Servidor {$lancamento->rh312_regist}"
                );
            }

            $valorLancamento = $lancamento->rh312_valor > $configuracao->rh311_valormax
                                                ? $configuracao->rh311_valormax
                                                : $lancamento->rh312_valor;

            if (!empty($configuracao->rh311_percentual) && $lancamento->rh312_valor < $configuracao->rh311_valormax) {
                $novoValorLancamento = $lancamento->rh312_valor +
                    ($lancamento->rh312_valor * ($configuracao->rh311_percentual / 100));

                $valorLancamento = ($novoValorLancamento < $configuracao->rh311_valormax)
                    ? $novoValorLancamento
                    : $configuracao->rh311_valormax;
            }

            $rubricaLancamento = $configuracao->rh311_rubric;
            if ($configuracao->rh311_validadependente && $lancamento->rh312_dependente) {
                $rubricaLancamento = $configuracao->rh311_rubricdepend;
            }

            $modelLancamento = new LancamentoAjudaCusto();
            $modelLancamento->rh313_ajuda_custo = $lancamento->rh312_sequencial;
            $modelLancamento->rh313_ano         = $competencia->getAno();
            $modelLancamento->rh313_mes         = $competencia->getMes();
            $modelLancamento->rh313_valor       = $valorLancamento;
            $modelLancamento->rh313_rubric      = $rubricaLancamento;
            $modelLancamento->rh313_instit      = $lancamento->rh312_instit;
            $modelLancamento->save();
        }

        $this->processaLancamentoPonto($competencia, $configuracao->rh311_instit);

        foreach ($inativacao as $itemInativar) {
            $itemInativar->rh312_ativo = false;
            $itemInativar->save();
        }
    }

    private function processaLancamentoPonto(DBCompetencia $competencia, $instituicao = null)
    {
        $lancamentoPonto = LancamentoAjudaCusto::select([
            'rh313_ano',
            'rh313_mes',
            'rh313_rubric',
            'rh312_regist',
            'rh312_instit',
            DB::raw('sum(rh313_valor) as rh313_valor'),
            DB::raw('(
            select
                rh02_lota
            from
                rhpessoalmov
            where
                rh02_regist = rh312_regist
                and rh02_anousu = rh313_ano
                and rh02_mesusu = rh313_mes) as lotacao')
        ])
        ->join('ajudacusto', 'rh313_ajuda_custo', 'rh312_sequencial')
        ->where('rh313_ano', '=', $competencia->getAno())
        ->where('rh313_mes', '=', $competencia->getMes())
        ->where('rh312_instit', '=', $instituicao)
        ->where('rh312_ativo', '=', 't')
        ->groupBy([
            'rh313_ano',
            'rh313_mes',
            'rh313_rubric',
            'rh312_regist',
            'rh312_instit'
        ])
        ->get();

        foreach ($lancamentoPonto as $processamento) {
            $pontofs = new PontoSalario();
            $pontofs->r10_anousu = $processamento->rh313_ano;
            $pontofs->r10_mesusu = $processamento->rh313_mes;
            $pontofs->r10_regist = $processamento->rh312_regist;
            $pontofs->r10_rubric = $processamento->rh313_rubric;
            $pontofs->r10_valor  = $processamento->rh313_valor;
            $pontofs->r10_quant  = '1';
            $pontofs->r10_lotac  = $processamento->lotacao;
            $pontofs->r10_datlim = '0';
            $pontofs->r10_instit = $processamento->rh312_instit;
            $pontofs->save();
        }
    }

    public function getDadosRelatorio(Request $request)
    {
        $competencia = CompetenciaHelper::get();

        $query = AjudaCusto::select([
            'rh02_regist as matricula',
            'cgm.z01_nome as nome',
            'rh31_nome as nome_dependente',
            'rh312_local as local',
            'rh312_especializacao as especializacao',
            'rh312_graduacao as graduacao',
            'unidade.z01_nome as unidade_ensino',
            'rh312_quantidade as quantidade',
            'dp01_cpf as cpf_dependente',
            'cgm.z01_cgccpf as cpf_servidor'
        ])
        ->join('rhpessoal', 'rh01_regist', DB::raw('rh312_regist and rh01_instit = rh312_instit'))
        ->join('cgm', 'cgm.z01_numcgm', 'rh01_numcgm')
        ->join('cgm as unidade', 'unidade.z01_numcgm', 'rh312_unidade_ensino')
        ->join('rhpessoalmov', 'rh02_regist', DB::raw("rh01_regist
                                                    and rh02_instit = rh01_instit
                                                    and rh02_anousu = {$competencia->getAno()}
                                                    and rh02_mesusu = {$competencia->getMes()}
                                                "))
        ->leftJoin('rhdepend', 'rh31_codigo', 'rh312_dependente')
        ->leftJoin('rhpeslocaltrab', 'rh56_seqpes', DB::raw('rh02_seqpes and rh56_princ is true'))
        ->leftJoin('rhlocaltrab', 'rh55_codigo', DB::raw('rh56_localtrab and rh55_instit = rh02_instit'))
        ->leftJoin('rhlota', 'r70_codigo', DB::raw('rh02_lota and r70_instit = rh02_instit'))
        ->leftJoin('rhfuncao', 'rh37_funcao', DB::raw('rh02_funcao and rh37_instit = rh02_instit'))
        ->leftJoin('rhdependeplug', 'dp01_rhdepend', 'rh31_codigo')
        ->where('rh312_instit', '=', $request->DB_instit)
        ->where('rh312_ativo', '=', 't')
        ->whereRaw("
            (('01/'||rh312_competencia)::date
             + interval '1 month' * rh312_quantidade >= ('{$request->data_inicial}')::date
            and ('01/'||rh312_competencia)::date <= ('{$request->data_final}')::date)");

        $registros = array_map(function ($item) {
            return $item['codigo'];
        }, $request->registros);

        $unidades = array_map(function ($item) {
            return $item['codigo'];
        }, $request->unidades);

        if (!empty($unidades)) {
            $query->whereIn('unidade.z01_numcgm', $unidades);
        }

        if ($request->tipo_resumo == 2) {
            if ($request->tipo_filtro == 3) {
                $query->whereIn('r70_codigo', $registros);
            } elseif ($request->tipo_filtro == 2) {
                $query->whereBetween('r70_codigo', [$request->inicio, $request->fim]);
            }
        }

        if ($request->tipo_resumo == 3) {
            if ($request->tipo_filtro == 3) {
                $query->whereIn('rh02_regist', $registros);
            } elseif ($request->tipo_filtro == 2) {
                $query->whereBetween('rh02_regist', [$request->inicio, $request->fim]);
            }
        }

        if ($request->tipo_resumo == 4) {
            if ($request->tipo_filtro == 3) {
                $query->whereIn('rh55_codigo', $registros);
            } elseif ($request->tipo_filtro == 2) {
                $query->whereBetween('rh55_codigo', [$request->inicio, $request->fim]);
            }
        }

        if ($request->tipo_resumo == 4) {
            if ($request->tipo_filtro == 3) {
                $query->whereIn('rh37_funcao', $registros);
            } elseif ($request->tipo_filtro == 2) {
                $query->whereBetween('rh37_funcao', [$request->inicio, $request->fim]);
            }
        }

        return $query->get();
    }
}
