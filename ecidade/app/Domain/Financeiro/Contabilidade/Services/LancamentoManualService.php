<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\ConlancamRetificacao;
use App\Domain\Financeiro\Contabilidade\Models\EncerramentoPeriodoContabil;
use App\Domain\Financeiro\Contabilidade\Models\Lancamento;
use App\Domain\Financeiro\Contabilidade\Models\Lote;
use App\Domain\Financeiro\Contabilidade\Repositories\LancamentoManualRepository;
use cl_conlancamretificacao;
use EventoContabil;
use Exception;
use Illuminate\Support\Facades\DB;
use ReceitaContabilRepository;

class LancamentoManualService
{
    const DOCUMENTO = 3000;
    /**
     * @var int
     */
    private $idLote;
    /**
     * @var mixed
     */
    private $exercicio;

    /**
     * @throws Exception
     */
    public function criarLancamentos(array $dados)
    {
        $this->validaEncerramentoPeriodoContabil($dados['exercicio'], $dados['instituicao'], $dados['data']);
        $this->exercicio = $dados['exercicio'];
        $lancamentosCriados = [];

        $this->criarLote($dados);

        foreach ($dados['lancamentos'] as $lancamento) {
            $lancamentosCriados[] = $this->criaLancamento(
                $lancamento,
                $dados['instituicao'],
                $dados['exercicio'],
                $dados['data']
            );
        }

        return ['idLote' => $this->idLote, 'lancamentos' => $lancamentosCriados];
    }

    /**
     * Retorna informações dos lançamentos manuais para a consulta e edição
     * @param array $request
     * @return mixed
     */
    public function getLancamentosManuais($filtros)
    {
        return (new LancamentoManualRepository())->getResmo($filtros);
    }

    /**
     * Apaga um lançamento manual, e se era o ultimo lançamento do lote, apaga o Lote junto.
     * @param Lancamento $lancamento
     * @return void
     * @throws Exception
     */
    public function excluirLancamento(Lancamento $lancamento)
    {
        $lote = $lancamento->lote->shift();

        $this->deletarLancamento($lancamento->c70_codlan);

        if ($lote->lancamentos->count() === 0) {
            Lote::destroy($lote->c160_codigo);
        }
    }

    /**
     * @param Lote $lote
     * @return void
     * @throws Exception
     */
    public function excluirLote(Lote $lote)
    {
        $lote->lancamentos()
            ->orderBy('c70_codlan', 'desc')
            ->get()
            ->each(function (Lancamento $lancamento) {
                $this->deletarLancamento($lancamento->c70_codlan);
            });

        Lote::destroy($lote->c160_codigo);
    }

    /**
     * @param array $dados
     * @return true
     * @throws Exception
     */
    public function alterarLancamento(array $dados)
    {
        $this->exercicio = $dados['exercicio'];
        $instituicao = $dados['instituicao'];
        $this->validaEncerramentoPeriodoContabil($dados['exercicio'], $instituicao, $dados['data']);
        $lancamentoOriginal = $dados['retificar']['lancamento'];
        $lote = Lancamento::find($lancamentoOriginal)->lote->shift();
        $this->idLote = $lote->c160_codigo;

        $lancamentoExtorno = $this->refifica($dados['retificar'], $instituicao, $dados['exercicio'], $dados['data']);

        $dados['lancamento']['observacao'] .= sprintf(
            ' Lançamento criado a partir da retificação do lançamento %s. Lançamento de extorno criado: %s',
            $lancamentoOriginal,
            $lancamentoExtorno
        );

        $novoLancamento = $this->criaLancamento(
            $dados['lancamento'],
            $instituicao,
            $dados['exercicio'],
            $dados['data']
        );
        $this->registraExtorno($lancamentoOriginal, $lancamentoExtorno, $novoLancamento);

        return true;
    }

    /**
     * @param array $dadosLancamento
     * @param integer $instituicao
     * @param integer $exercicio
     * @param string $data
     * @param int $documento
     * @return integer
     * @throws Exception
     */
    public function criaLancamento(array $dadosLancamento, $instituicao, $exercicio, $data, $documento = 3000)
    {
        $lancamentoAuxiliar = $this->buildLancamentoAuxiliar($dadosLancamento, $documento);
        $eventoContabil = new EventoContabil($documento, $exercicio, $instituicao);
        return $eventoContabil->executaLancamento($lancamentoAuxiliar, $data);
    }

    public function proximoLote($exercicio, $instituicao)
    {
        $data = Lote::query()
            ->where('c160_exercicio', $exercicio)
            ->where('c160_instituicao', $instituicao)
            ->count();

        return sprintf(
            '%s%s%s',
            str_pad($instituicao, 2, '0', STR_PAD_LEFT),
            $exercicio,
            str_pad($data + 1, 5, '0', STR_PAD_LEFT)
        );
    }

    private function buildLancamentoAuxiliar($lancamento, $documento = 3000)
    {
        $lancamentoAuxiliar = new \LancamentoAuxiliarLancamentoManual();
        $lancamentoAuxiliar->setCodigoLote($this->idLote);
        $lancamentoAuxiliar->setCodigoDocumento($documento);

        $lancamentoAuxiliar->setExercício($this->exercicio);
        $lancamentoAuxiliar->setContaCredito($lancamento['credito']['reduzido']);
        $lancamentoAuxiliar->setContaDebito($lancamento['debito']['reduzido']);
        $lancamentoAuxiliar->setHistorico($lancamento['historico']['codigo']);
        $lancamentoAuxiliar->setValorTotal($lancamento['valor']);
        $lancamentoAuxiliar->setObservacaoHistorico($lancamento['observacao']);

        if (!empty($lancamento['cgm'])) {
            $lancamentoAuxiliar->setCgm($lancamento['cgm']['numcgm']);
        }

        if (!empty($lancamento['recursoCredito'])) {
            $recurso = \RecursoRepository::getRecursoPorCodigo($lancamento['recursoCredito']['orctiporec_id']);
            $lancamentoAuxiliar->setRecursoCredito($recurso);
        }

        if (!empty($lancamento['recursoDebito'])) {
            $recurso = \RecursoRepository::getRecursoPorCodigo($lancamento['recursoDebito']['orctiporec_id']);
            $lancamentoAuxiliar->setRecursoDebito($recurso);
        }

        if (!empty($lancamento['empenho'])) {
            $empenho = \EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($lancamento['empenho']['numemp']);
            $lancamentoAuxiliar->setEmpenho($empenho);
        }

        if (!empty($lancamento['dotacao'])) {
            $dotacao = \DotacaoRepository::getDotacaoPorCodigoAno($lancamento['dotacao']['reduzido'], $this->exercicio);
            $lancamentoAuxiliar->setDotacao($dotacao);
        }

        if (!empty($lancamento['receita'])) {
            $reduzido = $lancamento['receita']['reduzido'];
            $lancamentoAuxiliar->setReceita(ReceitaContabilRepository::getReceitaByCodigo($reduzido, $this->exercicio));
        }

        if (!empty($lancamento['ajuste_saldo'])) {
            $lancamentoAuxiliar->setDadosAjusteSaldo($lancamento['ajuste_saldo']);
        }
        return $lancamentoAuxiliar;
    }

    private function criarLote(array $dados)
    {
        $data = new \DateTime();
        $dao = new \cl_lotelancamentos();
        $dao->c160_exercicio = $dados['exercicio'];
        $dao->c160_instituicao = $dados['instituicao'];
        $dao->c160_lote = $dados['numeroLote'];
        $dao->created_at = $data->format('Y-m-d H:i:s');
        $dao->updated_at = $data->format('Y-m-d H:i:s');
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            $msg = "Erro ao salvar Lote dos lançamentos\n{$dao->erro_status}";
            throw new Exception($msg);
        }

        $this->idLote = $dao->c160_codigo;
    }

    /**
     * @param integer $codigoLancamento
     * @return void
     * @throws Exception
     */
    private function deletarLancamento($codigoLancamento)
    {
        try {
            $retificado = ConlancamRetificacao::where('c135_codlannovo', $codigoLancamento)->first();
            if (is_null($retificado)) {
                DB::select("select contabilidade.excluir_lancamento_contabil($codigoLancamento)");
                return true;
            }

            ConlancamRetificacao::destroy($retificado->c135_sequencial);

            DB::select("select contabilidade.excluir_lancamento_contabil($retificado->c135_codlanestorno)");
            DB::select("select contabilidade.excluir_lancamento_contabil($retificado->c135_codlannovo)");
        } catch (Exception $e) {
            throw new Exception("Erro ao excluir lançamento {$codigoLancamento}. Favor, contate o suporte.");
        }
    }


    /**
     * O lançamento de retificação deve ser igual ao lançamento invertendo as contas débito e crédito
     * @param array $dadosLancamento
     * @param integer $instituicao
     * @param integer $exercicio
     * @param string $data
     * @return integer
     * @throws Exception
     */
    private function refifica(array $dadosLancamento, $instituicao, $exercicio, $data)
    {
        $debitar = $dadosLancamento['debito'];
        $creditar = $dadosLancamento['credito'];
        $dadosLancamento['credito'] = $debitar;
        $dadosLancamento['debito'] = $creditar;

        $dadosLancamento['recursoCredito']['orctiporec_id'] = $debitar['recurso'];
        $dadosLancamento['recursoDebito']['orctiporec_id'] = $creditar['recurso'];

        return $this->criaLancamento($dadosLancamento, $instituicao, $exercicio, $data, 3001);
    }


    /**
     * @param $lancamentoOriginal
     * @param $lancamentoExtorno
     * @param $novoLancamento
     * @return void
     * @throws Exception
     */
    private function registraExtorno($lancamentoOriginal, $lancamentoExtorno, $novoLancamento)
    {
        $dao = new cl_conlancamretificacao();
        $dao->c135_codlaninclusao = $lancamentoOriginal;
        $dao->c135_codlanestorno = $lancamentoExtorno;
        $dao->c135_codlannovo = $novoLancamento;

        $dao->incluir(null);
        if ($dao->erro_status == 0) {
            throw new Exception('Erro ao registrar retificação.');
        }
    }

    private function validaEncerramentoPeriodoContabil($exercicio, $instituicao, $data)
    {
        $dataEncerremento = EncerramentoPeriodoContabil::query()
            ->where('c99_anousu', $exercicio)
            ->where('c99_instit', $instituicao)
            ->where('c99_data', $data)
            ->first();

        if (!is_null($dataEncerremento)) {
            throw new Exception(sprintf(
                'Já foi realizado o encerramento contábil no dia %s.',
                $dataEncerremento->c99_data->format('d/m/Y')
            ));
        }
    }
}
