<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\VO\CalculoApropriacaoDecimoFeriasVO;
use Exception;
use Illuminate\Support\Facades\DB;

class CalculoApropriacaoDecimoFeriasService
{
    private $mapaDocumentos = [
        "RGPS" => [
            354 => "salario_13",
            356 => "valor_patronal_13",
            358 => "fgts_13",
            366 => "ferias",
            368 => "abono",
            370 => "valor_patronal_ferias",
            372 => "fgts_ferias",
        ],
        "RPPS" => [
            350 => "salario_13",
            352 => "valor_patronal_13",
            360 => "ferias",
            362 => "abono",
            364 => "previdencia_ferias",
        ]
    ];

    private $documentos = [
        'apropriar' => [350, 352, 354, 356, 358, 360, 362, 364, 366, 368, 370, 372],
        'extornar' => [351, 353, 355, 357, 359, 361, 363, 365, 367, 369, 371, 373]
    ];

    /**
     * Documentos usados quando o saldo fica negativo
     * @var int[]
     */
    private $documentosReversao = [
        350 => 420,
        351 => 421,
        352 => 422,
        353 => 423,
        354 => 424,
        355 => 425,
        356 => 426,
        357 => 427,
        358 => 428,
        359 => 429,
        360 => 430,
        361 => 431,
        362 => 432,
        363 => 433,
        364 => 434,
        365 => 435,
        366 => 436,
        367 => 437,
        368 => 438,
        369 => 439,
        370 => 440,
        371 => 441,
        372 => 442,
        373 => 443
    ];

    private $instituicao;
    private $exercicio;
    private $mes;
    /**
     * Data da sessão
     * @var string
     */
    private $data;

    /**
     * @var CalculoApropriacaoDecimoFeriasVO[]
     */
    private $contasCalcularApropriacao;

    /** Códigos dos reduzidos das contas credoras
     * @var array
     */
    private $reduzidosCredores = [];
    /**
     * @var DBConfig
     */
    private $prefeitura;

    /**
     * @param integer $instituicao
     * @param integer $exercicio
     * @param integer $mes
     * @param string $data
     */
    public function __construct($instituicao, $exercicio, $mes, $data)
    {
        $this->instituicao = $instituicao;
        $this->exercicio = $exercicio;
        $this->mes = $mes;
        $this->data = $data;
        $this->contasCalcularApropriacao = collect([]);

        $this->prefeitura = DBConfig::where('prefeitura', true)->first();
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public function calcula()
    {
        $this->contasExecutarLancamentos();
        $valoresFolha = $this->valoresFolha();
        return $this->calculaValoresLancar($valoresFolha);
    }

    /**
     * @return mixed
     */
    private function valoresFolha()
    {
        $sql = <<<SQL
with tabelas as(
  select distinct r33_instit, r33_codtab, r33_ppatro, rh129_regimeprevidencia,
         case when rh129_regimeprevidencia = 1 then 'RPPS'
              when rh129_regimeprevidencia = 2 then 'RGPS'
              when rh129_regimeprevidencia = 3 then 'RPPS' -- RPPS-EXT
         else 'SEM' end as prev,
         $this->exercicio as exercicio,
         $this->mes as mes
    from inssirf
  left join regimeprevidenciainssirf on (rh129_codigo, rh129_instit) = (r33_codigo, r33_instit)
   where r33_anousu = fc_anofolha($this->instituicao)
     and r33_mesusu = fc_mesfolha($this->instituicao)
     and r33_instit = $this->instituicao
     and r33_codtab > 2
), ferias as (
  select rh129_regimeprevidencia,
         r33_ppatro as perc_patronal,
         prev,
         round(sum(
             case when r93_pd = 1 and r93_rubric not in ('R931','R932','R940') then r93_valor else 0 end), 2
         ) as ferias,
         round(sum(
             case when r93_pd = 1 and r93_rubric in ('R931','R932','R940') then r93_valor else 0 end), 2) as abono,
         round(sum(case when r93_rubric in ('R992') then r93_valor else 0 end), 2) as base_prevfer,
         round(sum(
             case
               when r93_rubric in ('R901','R902','R903','R904','R905','R906','R907','R908','R909','R910','R911','R912')
                   then r93_valor else 0 end), 2
         ) as previdencia_ferias,
         round(sum(case when r93_rubric in ('R991') then r93_valor else 0 end), 2) as fgts_fer,
         0 as _13_salario,
         0 as base_prev13,
         0 as previdencia_13,
         0 as fgts_13
    from tabelas
    join rhpessoalmov on rh02_anousu = tabelas.exercicio
         and rh02_mesusu = tabelas.mes
         and rh02_instit = r33_instit
         and rh02_tbprev +2 = r33_codtab
    join gerfprovfer on r93_anousu = rh02_anousu
         and r93_mesusu =rh02_mesusu
         and r93_regist =rh02_regist
    group by rh129_regimeprevidencia, r33_ppatro, prev
), decimo as (
  select rh129_regimeprevidencia,
         r33_ppatro as perc_patronal,
         prev,
         0 as ferias,
         0 as abono,
         0 as base_prevfer,
         0 as previdencia_ferias,
         0 as fgts_fer,
        round(sum(case when r94_pd = 1 then r94_valor else 0 end), 2) as _13_salario,
        round(sum(case when r94_rubric in ('R992') then r94_valor else 0 end), 2) as base_prev13,
        round(sum(
          case
            when r94_rubric in ('R901','R902','R903','R904','R905','R906','R907','R908','R909','R910','R911','R912')
                then r94_valor else 0 end), 2
        ) as previdencia_13,
        round(sum(case when r94_rubric in ('R991') then r94_valor else 0 end), 2) as fgts_13
   from tabelas
   join rhpessoalmov on rh02_anousu = tabelas.exercicio
        and rh02_mesusu = tabelas.mes
        and rh02_instit = r33_instit
        and rh02_tbprev +2 = r33_codtab
   join gerfprovs13 on r94_anousu = rh02_anousu
        and r94_mesusu = rh02_mesusu
        and r94_regist = rh02_regist
   group by rh129_regimeprevidencia,r33_ppatro, prev
), calcula_valores as (
select rh129_regimeprevidencia,
       prev,
       perc_patronal,
       round(sum(base_prevfer)*(perc_patronal/100),2) as valor_patronal_ferias ,
       round(sum(ferias),2) as ferias,
       round(sum(abono),2) as abono,
       round(sum(previdencia_ferias),2) as previdencia_ferias,
       round(sum(fgts_fer)*0.08,2) as fgts_ferias,
       round(sum(base_prev13)*(perc_patronal/100),2) as valor_patronal_13,
       round(sum(_13_salario),2) as salario_13,
       round(sum(previdencia_13),2) as previdencia_13,
       round(sum(fgts_13)*0.08,2) as fgts_13
from (
    select * from ferias
    union all
    select * from decimo) as x
    group by rh129_regimeprevidencia,prev, perc_patronal
), agrupa_valores as (
    select
           case when rh129_regimeprevidencia = 3 then 1 else rh129_regimeprevidencia end as codigo_tabela_previdencia,
           prev as tabela_previdencia,
           round(sum(valor_patronal_ferias), 2) as valor_patronal_ferias,
           round(sum(ferias), 2) as ferias,
           round(sum(abono), 2) as abono,
           round(sum(previdencia_ferias), 2) as previdencia_ferias,
           round(sum(fgts_ferias), 2) as fgts_ferias,
           round(sum(valor_patronal_13), 2) as valor_patronal_13,
           round(sum(salario_13), 2) as salario_13,
           round(sum(previdencia_13), 2) as previdencia_13,
           round(sum(fgts_13), 2) as fgts_13
      from calcula_valores
   group by 1,2
) select * from agrupa_valores;
SQL;
        return DB::select($sql);
    }

    private function calculaValoresLancar($valoresFolha)
    {
        // itera sobre os valores da folha
        foreach ($valoresFolha as $valorFolha) {
            $mapaDocumentos = $this->getMapaDocumentos($valorFolha->tabela_previdencia);
            foreach ($mapaDocumentos as $documento => $coluna) {
                $this->contasCalcularApropriacao->each(
                    function (CalculoApropriacaoDecimoFeriasVO $calculo) use ($valorFolha, $documento, $coluna) {
                        if ($calculo->documento == $documento) {
                            $calculo->valorFolha = (float)$valorFolha->{$coluna};
                            $calculo->codigoTabelaPrevidencia = $valorFolha->codigo_tabela_previdencia;
                            $calculo->tabelaPrevidencia = $valorFolha->tabela_previdencia;
                        }
                    }
                );
            }
        }

        return $this->contasCalcularApropriacao->reject(function (CalculoApropriacaoDecimoFeriasVO $calculo) {
            return empty($calculo->tabelaPrevidencia);
        })->each(function (CalculoApropriacaoDecimoFeriasVO $calculo) {
            if (empty($calculo->valorBalanceteVerificacao)) {
                $calculo->valorLancar = $calculo->valorFolha;
            }

            // conta no balvel esta Credora
            if ($calculo->naturezaSaldoBalancete === 'C') {
                $valorLancar = $calculo->valorFolha - $calculo->valorBalanceteVerificacao;
                // se o valor for negativo, devemos usar o documento de reversão
                if ($valorLancar < 0) {
                    $contaRevercao = $this->buscarDocumentoReversao($this->documentosReversao[$calculo->documento]);
                    $calculo->usarContaReversao($contaRevercao);
                    $valorLancar = abs($valorLancar);
                }

                $calculo->valorLancar = $valorLancar;
            }
            // se natureza da conta for devedora:
            // o valor do lançamento é a soma do valor da folha com o saldo do balancete
            if ($calculo->naturezaSaldoBalancete === 'D') {
                $valorLancar = $calculo->valorFolha + $calculo->valorBalanceteVerificacao;
                $calculo->valorLancar = $valorLancar;
            }

            $calculo->saldo = $calculo->valorFolha;
        });
    }

    private function getMapaDocumentos($tabelaPrevidencia)
    {
        if (!array_key_exists($tabelaPrevidencia, $this->mapaDocumentos)) {
            $msg = "Pessoal > Cadastros > Tabelas > Previdência e IRRF";
            throw new Exception("Tabela de previdência não encontrada. Revise o cadastro acessando o menu: {$msg}");
        }
        return $this->mapaDocumentos[$tabelaPrevidencia];
    }

    /**
     * @param array $documentos
     * @return string
     */
    private function sqlTransacoes(array $documentos, $instituicaoLogada = null)
    {
        if ($instituicaoLogada == null) {
            $instituicaoLogada = $this->prefeitura->codigo;
        }
        $documentos = implode(', ', $documentos);
        return <<<SQL
select c45_coddoc,
       c53_descr,
       c46_codhist,
       rcc.c61_reduz as credito_reduzido,
       rcc.c61_codcon as credito_conta,
       rcc.c61_codigo as credito_recurso,
       cc.c60_estrut credito_estrutural,
       cc.c60_descr credito_descricao,
       case when cc.c60_naturezasaldo = 2 then 'C' else 'D' end as credito_natureza_saldo,
       rcd.c61_reduz as debito_reduzido,
       rcd.c61_codcon as debito_conta,
       rcd.c61_codigo as debito_recurso,
       cd.c60_estrut debito_estrutural,
       cd.c60_descr debito_descricao
  from contrans
  join contranslan on contranslan.c46_seqtrans = contrans.c45_seqtrans
  join conhistdoc on conhistdoc.c53_coddoc = contrans.c45_coddoc
  join contranslr t on t.c47_seqtranslan = contranslan.c46_seqtranslan
 -- reduzido da transacao na prefeitura
  join conplanoreduz rcc_trans on rcc_trans.c61_reduz = t.c47_credito
       and rcc_trans.c61_anousu = t.c47_anousu
 -- busca o reduzido da instituicao logada
  join conplanoreduz rcc on rcc.c61_codcon = rcc_trans.c61_codcon
       and rcc.c61_anousu = rcc_trans.c61_anousu
       and rcc.c61_instit = {$this->instituicao}
  join conplano cc on cc.c60_codcon = rcc.c61_codcon
       and cc.c60_anousu = rcc.c61_anousu
 -- reduzido da transacao na prefeitura
  join conplanoreduz rcd_trans on rcd_trans.c61_reduz = t.c47_debito
       and rcd_trans.c61_anousu = t.c47_anousu
 -- busca o reduzido da instituicao logada
  join conplanoreduz rcd on rcd.c61_codcon = rcd_trans.c61_codcon
       and rcd.c61_anousu = rcd_trans.c61_anousu
       and rcd.c61_instit = {$this->instituicao}
  join conplano cd on cd.c60_codcon = rcd.c61_codcon
       and cd.c60_anousu = rcd.c61_anousu
 where c47_instit = {$instituicaoLogada}
   and c47_anousu = {$this->exercicio}
   and c45_coddoc in ($documentos)
SQL;
    }

    private function contasExecutarLancamentos($apropriacao = true)
    {
        $dataIni = "{$this->exercicio}-{$this->mes}-01";
        $sqlTransacoes = $this->sqlTransacoes($this->getDocumentos($apropriacao));
        $sql = <<<SQL
with transacoes as (
   {$sqlTransacoes}
), executa_balancete as (
    select transacoes.*,
           fc_planosaldonovo_array($this->exercicio, credito_reduzido, '{$dataIni}', '{$this->data}', false)
      from transacoes
) select *,
         fc_planosaldonovo_array[4]::numeric as saldo_final,
         fc_planosaldonovo_array[6]::varchar(1) as sinal_final
    from executa_balancete
SQL;

        $constas = DB::select($sql);
        if (empty($constas)) {
            throw new Exception('Não foi configurado as transações dos documentos.');
        }

        $consistirDocumentos = [];
        foreach ($constas as $conta) {
            $consistirDocumentos[$conta->credito_reduzido][] = $conta->c45_coddoc;

            $this->reduzidosCredores[] = $conta->credito_reduzido;

            $vo = new CalculoApropriacaoDecimoFeriasVO();
            $vo->documento = $conta->c45_coddoc;
            $vo->documentoDescricao = $conta->c53_descr;
            $vo->historico = $conta->c46_codhist;
            $vo->naturezaCredito = $conta->credito_natureza_saldo;
            $vo->contaCredito = $conta->credito_reduzido;
            $vo->codigoContaCredito = $conta->credito_conta;
            $vo->codgoRecursoCredito = $conta->credito_recurso;
            $vo->estruturalContaCredito = $conta->credito_estrutural;
            $vo->descricaoContaCredito = $conta->credito_descricao;
            $vo->contaDebito = $conta->debito_reduzido;
            $vo->codgoContaDebito = $conta->debito_conta;
            $vo->codgoRecursoDebito = $conta->debito_recurso;
            $vo->estruturalContaDebito = $conta->debito_estrutural;
            $vo->descricaoContaDebito = $conta->debito_descricao;

            $vo->valorBalanceteVerificacao = (float)$conta->saldo_final;
            $vo->naturezaSaldoBalancete = $conta->sinal_final;
            $this->contasCalcularApropriacao->push($vo);
        }

        $mensagem = sprintf(
            "%s. %s\n%s \n\n",
            'Existe um erro de configuração nas transações.',
            'Documentos diferentes não podem possuir a mesma conta.',
            "Os seguintes documentos estão usando a mesma conta: "
        );
        $inconsistencias = [];
        foreach ($consistirDocumentos as $conta => $documentos) {
            if (count($documentos) > 1) {
                $documentos = implode(', ', $documentos);
                $inconsistencias[] = "Documentos: $documentos - conta: $conta ";
            }
        }

        if (!empty($inconsistencias)) {
            $mensagem .= implode("\n", $inconsistencias);
            $mensagem .= "\n\nContate o suporte!";
            throw new Exception($mensagem, 406);
        }

        $documentos = $this->getDocumentos($apropriacao);
        $documentosSemTransacao = [];
        foreach ($documentos as $documento) {
            $documentoEncontrado = $this->contasCalcularApropriacao->filter(function ($x) use ($documento) {
                return $x->documento == $documento;
            });

            if ($documentoEncontrado->isEmpty()) {
                $documentosSemTransacao[] = $documento;
            }
        }

        if (count($documentosSemTransacao) > 0) {
            throw new Exception(sprintf(
                'Os seguintes documentos não possuem transação: %s',
                implode(', ', $documentosSemTransacao)
            ));
        }
    }

    /**
     * @param $apropriar
     * @return int[]
     */
    private function getDocumentos($apropriar = true)
    {
        if ($apropriar) {
            return $this->documentos['apropriar'];
        }
        return $this->documentos['extornar'];
    }

    /**
     * Busca os dados do documento de reversão.
     *
     * a regra foi alterada:
     *  agora sistema irá buscar a regra reversa primeiro pela instiuição logada
     *  se não encontrar, vai seguir normal:
     *   busca a regra do doc na prefeitura, pega o o codcon do reduzido na prefeitura e busca
     *  pelo codcon o reduzido da instituição logada.
     *  isso se deu porque cliente não quer que lance na conta do codcon da prefeitura.
     *   com isso cada doc reverso pode ter sua configuração especifica na instituição logada.
     * @param $documento
     * @return mixed
     */
    private function buscarDocumentoReversao($documento)
    {
        try {
            $sql = $this->sqlTransacoes([$documento], $this->instituicao);
            return DB::select($sql)[0];
        } catch (Exception $erro) {
            try {
                $sql = $this->sqlTransacoes([$documento]);
                return DB::select($sql)[0];
            } catch (Exception $erro) {
                throw new Exception("Não foi possível buscar transação para o documento {$documento}.", 406);
            }
        }
    }
}
