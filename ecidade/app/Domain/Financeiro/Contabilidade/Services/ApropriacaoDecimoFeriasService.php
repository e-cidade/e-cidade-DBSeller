<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\ApropriacaoDecimoFerias;
use App\Domain\Financeiro\Contabilidade\Models\ApropriacaoDecimoFeriasLancamentos;
use App\Domain\Financeiro\Contabilidade\Models\ParametroIntegracaoPatrimonial;
use App\Domain\Financeiro\Contabilidade\Models\VinculoEventosContabeis;
use App\Domain\Financeiro\Contabilidade\VO\CalculoApropriacaoDecimoFeriasVO;
use Carbon\Carbon;
use ContaCorrenteDetalhe;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LancamentoAuxiliarApopriacaoDecimoFerias;

class ApropriacaoDecimoFeriasService
{
    private $instituicao;
    private $exercicio;
    private $mes;

    private $fileLock = 'lock-apropriacao-decimo-ferias.txt';
    /**
     * @var string
     */
    private $data;

    public function __construct($instituicao, $exercicio, $mes, $data)
    {
        $this->instituicao = $instituicao;
        $this->exercicio = $exercicio;
        $this->mes = $mes;
        $this->data = $data;
    }

    public static function proximaCompetenciaApropriar($idInstituicao)
    {
        $apropriacao = ApropriacaoDecimoFerias::latest('id')
            ->where('c145_instituicao', $idInstituicao)
            ->where('c145_processado', true)
            ->first();

        if (is_null($apropriacao)) {
            $paramentro = ParametroIntegracaoPatrimonial::query()
                ->where('c01_instit', $idInstituicao)
                ->where('c01_modulo', 4)
                ->first();

            if (is_null($paramentro)) {
                $msg = "Não foi implantado a Apropriação por Competência de Férias e 13º Salário. \n";
                $msg .= "Acesse: DB:FINANCEIRO > Contabilidade > Procedimentos > Parâmetros > Integração Patrimonial";
                $msg .= " para configurar";

                throw new Exception($msg, 406);
            }

            return [
                "exercicio" => $paramentro->c01_data->format('Y'),
                "mes" => $paramentro->c01_data->format('m'),
                "nomeMes" => ucfirst($paramentro->c01_data->formatLocalized('%B')),
            ];
        }

        $mes = $apropriacao->c145_mes + 1;
        $ano = $apropriacao->c145_exercicio;

        if ($mes > 12) {
            $mes = 1;
            $ano = $apropriacao->c145_exercicio + 1;
        }

        $nomeMes = Carbon::create($ano, $mes, 1)->formatLocalized('%B');

        return [
            "exercicio" => $ano,
            "mes" => $mes,
            "nomeMes" => ucfirst($nomeMes),
        ];
    }

    /**
     * Busca a competencia
     * @param $idInstituicao
     * @return array
     * @throws Exception
     */
    public static function proximaCompetenciaExtornar($idInstituicao)
    {
        $apropriacao = ApropriacaoDecimoFerias::latest('id')
            ->where('c145_instituicao', $idInstituicao)
            ->where('c145_processado', true)
            ->first();

        if (is_null($apropriacao)) {
            throw new Exception("Não existe nenhuma competência de Apropriação de 13ª ou férias processada.", 403);
        }
        $nomeMes = Carbon::create($apropriacao->c145_exercicio, $apropriacao->c145_mes, 1)->formatLocalized('%B');
        return [
            "exercicio" => $apropriacao->c145_exercicio,
            "mes" => $apropriacao->c145_mes,
            "nomeMes" => ucfirst($nomeMes),
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public function buscarValoresApropriar()
    {
        $data = self::proximaCompetenciaApropriar($this->instituicao);
        if (((int) $data["exercicio"] != $this->exercicio) || ((int) $data["mes"] != $this->mes)) {
            $msg = "Competência informada não é mais válida! Será carregado a competência atualizada.";
            throw new Exception($msg, 302);
        }

        $exercicio = $this->exercicio;
        $service = new CalculoApropriacaoDecimoFeriasService($this->instituicao, $exercicio, $this->mes, $this->data);
        return $service->calcula();
    }

    /**
     * Realiza a apropriação dos valores de 13º salário e férias
     * @return void
     * @throws Exception
     */
    public function apropriar()
    {
        $this->validaLock();

        try {
            $valores = $this->buscarValoresApropriar();
            if (empty($valores)) {
                throw new Exception('Não existe valores a serem processados na competência informada.', 406);
            }
            $apropriacao = $this->salvarApropriacao();
            $this->executarLancamentos($apropriacao, $valores);
            $this->removeLock();
        } catch (Exception $exception) {
            // sempre que uma exception for lançada na apropriação, remove-se o lock
            $this->removeLock();
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     *  Realiza o estorno da apropriação dos valores de 13º salário e férias
     * @return void
     * @throws Exception
     */
    public function estornar()
    {
        $this->validaLock();

        try {
            $data = self::proximaCompetenciaExtornar($this->instituicao);
            if (((int) $data["exercicio"] != $this->exercicio) || ((int) $data["mes"] != $this->mes)) {
                $msg = "Competência informada não é mais válida! Será carregado a competência atualizada.";
                throw new Exception($msg, 302);
            }

            $msg = "Estorno do lançamento contábil referente a Apropriação de 13º e Férias. Estornando lancamento: %s";
            $apropriacao = $this->salvarApropriacao(false);

            ApropriacaoDecimoFerias::where('id', $apropriacao->id)
                ->first()
                ->lancamentos()
                ->where('c146_estornar', true)
                ->get()
                ->each(function (ApropriacaoDecimoFeriasLancamentos $lancamentoApropriacao) use ($msg) {
                    $lancamento = $lancamentoApropriacao->lancamento;
                    $observacao = sprintf($msg, $lancamento->c70_codlan);
                    $documento = $this->getDocumentoEstorno($lancamento->documentoLancamento->c71_coddoc);

                    $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();
                    $sql = "
                        select
                            c130_orctiporec
                        from 
                            conlancamrecurso
                            join conlancamval on c130_conlancam = c69_codlan
                                and c130_conta = c69_credito
                                and c130_natureza = 'C'
                                and c69_ordem = 1
                        where c130_conlancam = {$lancamento->c70_codlan}";
                    $recursoContaCredora = DB::Select($sql);

                    if (sizeof($recursoContaCredora) < 1) {
                        $msg = "Não foi encontrado o recurso vinculado ao lancamento {{$lancamento->c70_codlan}}.";
                        throw new Exception($msg);
                    }

                    $oContaCorrenteDetalhe->setRecurso(new \Recurso($recursoContaCredora[0]->c130_orctiporec));
                    $oContaCorrenteDetalhe->setContaBancaria(null);

                    $auxiliar = new LancamentoAuxiliarApopriacaoDecimoFerias();
                    $auxiliar->setValorTotal($lancamento->c70_valor);
                    $auxiliar->setObservacaoHistorico($observacao);
                    $auxiliar->setRecurso($recursoContaCredora[0]->c130_orctiporec);
                    $auxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalhe);

                    foreach ($lancamento->contasLancamento as $conta) {
                        $auxiliar->setHistorico($this->getHistorico($documento, $conta->c69_ordem));
                        $auxiliar->setContaCredito($conta->c69_debito);
                        $auxiliar->setContaDebito($conta->c69_credito);
                    }

                    $evento = new \EventoContabil($documento, $this->exercicio, $this->instituicao, true);
                    $codigoLancamento = $evento->executaLancamento($auxiliar, $this->data);

                    $dao = new \cl_apropriacaodecimoferias_lancamentos();
                    $dao->c146_apropriacaodecimoferias_id = $lancamentoApropriacao->apropriacao->id;
                    $dao->c146_regimeprevidencia = $lancamentoApropriacao->c146_regimeprevidencia;
                    $dao->c146_conlancam = $codigoLancamento;
                    $dao->c146_estornar = 'f';
                    $dao->created_at = date('Y-m-d H:i:s');
                    $dao->updated_at = date('Y-m-d H:i:s');
                    $dao->incluir(null);
                });
            $this->removeLock();
        } catch (Exception $exception) {
            // sempre que uma exception for lançada na apropriação, remove-se o lock
            $this->removeLock();
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param $processado
     * @return \cl_apropriacaodecimoferias
     * @throws Exception
     */
    private function salvarApropriacao($processado = true)
    {
        $filtro = [
            "c145_instituicao = {$this->instituicao}",
            "c145_exercicio = {$this->exercicio}",
            "c145_mes = {$this->mes}"
        ];

        $where = implode(' and ', $filtro);
        $dao = new \cl_apropriacaodecimoferias();
        $sql = $dao->sql_query_file(null, '*', null, $where);
        $rs = db_query($sql);

        $dao->c145_instituicao = $this->instituicao;
        $dao->c145_exercicio = $this->exercicio;
        $dao->c145_mes = $this->mes;
        $dao->c145_processado = $processado ? 't' : 'f';
        $dao->created_at = date('Y-m-d H:i:s');

        if (pg_num_rows($rs) === 0) {
            $dao->updated_at = date('Y-m-d H:i:s');
            $dao->incluir(null);
        } else {
            $id = \db_utils::fieldsMemory($rs, 0)->id;

            $dao->id = $id;
            $dao->c145_processado = $processado ? 't' : 'f';
            $dao->updated_at = date('Y-m-d H:i:s');
            $dao->alterar($id);
        }

        if ($dao->erro_status == 0) {
            throw new Exception('Erro ao salvar apropriação.' . $dao->erro_msg);
        }

        return $dao;
    }

    /**
     * @param \cl_apropriacaodecimoferias $apropriacao
     * @param $valores
     * @return void
     * @throws \BusinessException
     */
    private function executarLancamentos($apropriacao, $valores)
    {
        $msg = "Lançamento contábil referente a Apropriação de 13º e Férias. Valor no balancete: %s ";
        $msg .= "Valor do lançamento: %s Saldo: %s ";
        /**
         * @var $valorApropriar CalculoApropriacaoDecimoFeriasVO
         */
        foreach ($valores as $valorApropriar) {
            if (empty((float)$valorApropriar->valorLancar)) {
                continue;
            }

            $msgComplemento = sprintf(
                $msg,
                $valorApropriar->valorBalanceteVerificacao,
                $valorApropriar->valorLancar,
                $valorApropriar->saldo
            );

            $oContaCorrenteDetalhe = new ContaCorrenteDetalhe();
            $oContaCorrenteDetalhe->setRecurso(new \Recurso($valorApropriar->codgoRecursoCredito));
            $oContaCorrenteDetalhe->setContaBancaria(null);

            $auxiliar = new LancamentoAuxiliarApopriacaoDecimoFerias();
            $auxiliar->setValorTotal($valorApropriar->valorLancar);
            $auxiliar->setHistorico($valorApropriar->historico);
            $auxiliar->setObservacaoHistorico($msgComplemento);
            $auxiliar->setContaCredito($valorApropriar->contaCredito);
            $auxiliar->setContaDebito($valorApropriar->contaDebito);
            $auxiliar->setRecurso($valorApropriar->codgoRecursoCredito);
            $auxiliar->setContaCorrenteDetalhe($oContaCorrenteDetalhe);

            $evento = new \EventoContabil($valorApropriar->documento, $this->exercicio, $this->instituicao, true);
            $codigoLancamento = $evento->executaLancamento($auxiliar, $this->data);

            $dao = new \cl_apropriacaodecimoferias_lancamentos();
            $dao->c146_apropriacaodecimoferias_id = $apropriacao->id;
            $dao->c146_regimeprevidencia = $valorApropriar->codigoTabelaPrevidencia;
            $dao->c146_conlancam = $codigoLancamento;
            $dao->c146_estornar = 't';
            $dao->created_at = date('Y-m-d H:i:s');
            $dao->updated_at = date('Y-m-d H:i:s');
            $dao->incluir(null);

            if ($dao->erro_status == 0) {
                throw new Exception('Erro ao salvar apropriação.' . $dao->erro_msg, 400);
            }
        }
    }

    private function getDocumentoEstorno($codigoDocumento)
    {
        return VinculoEventosContabeis::where('c115_conhistdocinclusao', $codigoDocumento)
            ->first()
            ->c115_conhistdocestorno;
    }

    /**
     * @param $documento
     * @param $ordem
     * @return int
     */
    private function getHistorico($documento, $ordem)
    {
        $data = DB::select(<<<SQL
select c46_codhist
 from contrans
 join contranslan on contranslan.c46_seqtrans = contrans.c45_seqtrans
 join conhistdoc on conhistdoc.c53_coddoc = contrans.c45_coddoc
where c45_instit = $this->instituicao
  and c45_anousu = $this->exercicio
  and c45_coddoc = $documento
  and c46_ordem  = $ordem
SQL
        );

        if (empty($data)) {
            return 3000;
        }

        return $data[0]->c46_codhist;
    }

    /**
     * Valida se um processo de apropriação ou estorno já existe em andamento
     * @return true
     * @throws Exception
     */
    private function validaLock()
    {
        $fileLock = $this->fileLock;

        if (!Storage::exists($fileLock)) {
            Storage::put($fileLock, 'Processando apropriação/estorno de décimo ou férias');
            return true;
        }

        // busca a data de criação do arquivo.
        $data = Carbon::createFromTimestamp(Storage::lastModified($fileLock));

        // Se passou de 10 minutos, deleta o arquivo e recria, pois provavelmente o sistema deu algum erro ou foi
        // fechada a rotina antes de concluir o processo.
        // Nesse caso, removemos o arquivo, pois é considerado que o processo ja concluiu.
        if ($data->diffInMinutes((new Carbon())) > 10) {
            $this->removeLock();
            Storage::put($fileLock, 'Processando apropriação/estorno de décimo ou férias');
            return true;
        }

        $msg = 'Já existe um processo de apropriação/estorno de apropriação sendo executado por outro usuário ou ';
        $msg .= 'em outra janela. Aguarde o processo alguns minutos e acesse novamente a rotina.';
        throw new Exception($msg, 406);
    }

    /**
     * Remove o arquivo de lock
     * @return void
     */
    private function removeLock()
    {
        Storage::delete($this->fileLock);
    }
}
