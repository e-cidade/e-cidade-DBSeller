<?php


namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal;
use Illuminate\Support\Facades\DB;

class ServidorService
{

    public function getMatriculas($cpfCnpj)
    {
        return DB::select("select
                    pessoal.rhpessoal.rh01_regist as matricula,
                    configuracoes.db_config.nomeinst as instituicao_nome,
                    configuracoes.db_config.codigo  as instituicao_codigo,
                    pessoal.rhpessoal.rh01_admiss as data_admissao,
                    case when pessoal.rhpesrescisao.rh05_recis is not null then
                    pessoal.rhpesrescisao.rh05_recis
                    else
                    null
                    end as data_exoneracao,
                    trim(pessoal.rhfuncao.rh37_descr) as cargo
                from
                    pessoal.rhpessoal
                left join pessoal.rhpessoalmov on
                    rh02_regist = rh01_regist
                left join pessoal.rhpesrescisao on
                    rh05_seqpes = rh02_seqpes
                left join pessoal.rhfuncao on
                    rh01_funcao = rh37_funcao
                    and rh37_instit = rh02_instit
                left join configuracoes.db_config on
                    codigo = rh01_instit
                where
                    rh02_anousu = fc_anofolha(rh01_instit)
                    and rh02_mesusu = fc_mesfolha(rh01_instit)
                    and rh01_numcgm in (
                    select
                        z01_numcgm
                    from
                            protocolo.cgm
                    where
                             z01_cgccpf = '{$cpfCnpj}'
                             and trim(z01_cgccpf) <> ''
                     )
                order by
                    rh01_admiss desc
        ");
    }

    public function getAssentamentos($matricula)
    {
        return DB::select("
           SELECT
                    h16_dtconc AS data_inicio,
                    h12_descr as descricao,
                    h16_dtterm as  data_termino,
                    h16_quant as quantidade,
                    h16_nrport as numero_ato,
                    h16_atofic as tipo,
                    h16_histor||' '||h16_hist2 as historico
            FROM
                recursoshumanos.assenta
            join recursoshumanos.tipoasse ON  h16_assent=h12_codigo
           WHERE
               h16_regist = {$matricula}
           and h12_reltot < 2
          ORDER BY  h16_dtconc DESC
        ");
    }

    public function getAverbacoes($matricula)
    {
        return DB::select("
           SELECT
                    h16_dtconc AS data_inicio,
                    h12_descr as descricao,
                    h16_dtterm as data_termino,
                    h16_quant as quantidade,
                    h16_nrport as numero_ato,
                    h16_atofic as tipo,
                    h16_histor||' '||h16_hist2 as historico
            FROM
                recursoshumanos.assenta
            join recursoshumanos.tipoasse ON  h16_assent=h12_codigo
           WHERE
               h16_regist = {$matricula}
           and h12_reltot > 1
          ORDER BY  h16_dtconc DESC
        ");
    }

    public function getFerias($matricula)
    {
        $dados = DB::select("
            select
                concat(to_char(r30_perai,'dd/mm/YYYY'),' à ',to_char(r30_peraf,'dd/mm/YYYY'))  AS periodo_aquisitivo,
                r30_abono as dias_abonados,
                to_char(r30_per1i,'dd/mm/YYYY') as data_inicio_gozo_1,
                to_char(r30_per1f,'dd/mm/YYYY') as data_termino_gozo_1,
                r30_dias1 as dias_gozados_1,
                r30_proc1  as mes_pagamento_1,
                to_char(r30_per2i,'dd/mm/YYYY') as data_inicio_gozo_2,
                to_char(r30_per2f,'dd/mm/YYYY') as data_termino_gozo_2,
                r30_dias2 as dias_gozados_2,
                r30_proc2 as  mes_pagamento_2
            from
                pessoal.cadferia
            inner join pessoal.rhpessoal on pessoal.rhpessoal.rh01_regist  = pessoal.cadferia.r30_regist
            where
                 r30_regist = '{$matricula}'
            and r30_anousu= fc_anofolha(rh01_instit)
            and r30_mesusu = fc_mesfolha(rh01_instit)
            order by r30_perai desc
        ");

        return self::formatarDadosFerias($dados);
    }

    private static function formatarDadosFerias($dados)
    {

        if (empty($dados)) {
            return array();
        }

        foreach ($dados as $dado) {
            $dado->periodo_de_gozo = array();
            if (!empty($dado->data_inicio_gozo_1)) {
                $dado->periodo_de_gozo[] = array(
                    'inicio' => $dado->data_inicio_gozo_1,
                    'termino' => $dado->data_termino_gozo_1,
                    'dias' => $dado->dias_gozados_1,
                    'mes' => $dado->mes_pagamento_1,
                );
            }

            if (!empty($dado->data_inicio_gozo_2)) {
                $dado->periodo_de_gozo[] = array(
                    'inicio' => $dado->data_inicio_gozo_2,
                    'termino' => $dado->data_termino_gozo_2,
                    'dias' => $dado->dias_gozados_2,
                    'mes' => $dado->mes_pagamento_2,
                );
            }

            unset($dado->data_inicio_gozo_1);
            unset($dado->data_termino_gozo_1);
            unset($dado->dias_gozados_1);
            unset($dado->mes_pagamento_1);
            unset($dado->data_inicio_gozo_2);
            unset($dado->data_termino_gozo_2);
            unset($dado->dias_gozados_2);
            unset($dado->mes_pagamento_2);
        }

        return $dados;
    }

    public function getProximoPeriodoAquisito($matricula)
    {

        $queryBuilder = DB::table('pessoal.rhpessoal')
            ->join('pessoal.rhpessoalmov', function ($join) use ($matricula) {
                $join->on('rh02_regist', '=', 'rh01_regist')
                    ->on('rh02_anousu', '=', DB::raw("fc_anofolha(rh01_instit)"))
                    ->on('rh02_mesusu', '=', DB::raw("fc_mesfolha(rh01_instit)"));
            })
            ->leftJoin('pessoal.rhpesrescisao', 'rh05_seqpes', '=', 'rh02_seqpes')
            ->leftJoin('pessoal.cadferia', function ($join) {
                $join->on("r30_regist", "=", "rh02_regist")
                    ->on("r30_anousu", "=", "rh02_anousu")
                    ->on("r30_mesusu", "=", "rh02_mesusu");
            })
            ->leftJoin('pessoal.rhregime', 'rh30_codreg', '=', 'rh02_codreg')
            ->where('rh01_regist', '=', $matricula);

        $ultimaFeriasGozadas = $queryBuilder->max('r30_peraf');
        $dadosMatricula = $queryBuilder->select('rh01_admiss', 'rh05_recis')->first();

        if (!empty($dadosMatricula->rh05_recis)) {
            return null;
        }

        $dataAdmissao = $dadosMatricula->rh01_admiss;


        if (empty($ultimaFeriasGozadas)) {
            $ultimaFeriasGozadas = $dataAdmissao;
        }

        $dataInicioPeriodo = new \DateTime($ultimaFeriasGozadas);
        $dataFimPeriodo = new \DateTime($ultimaFeriasGozadas);

        $dataNoDia = new \DateTime();

        $oNovoPeriodo = array(
            'inicio' => $dataInicioPeriodo->modify('+1 days')->format("Y-m-d"),
            'termino' => $dataFimPeriodo->modify('+1 year')->format("Y-m-d")
        );

        if ($oNovoPeriodo["termino"] >= $dataNoDia) {
            return null;
        }
        return $oNovoPeriodo;
    }

    public function getAnosTrabalhados($matricula)
    {
        return DB::table('pessoal.rhpessoalmov')
            ->join('pessoal.rhdirfgeracao', 'rh95_ano', '=', 'rh02_anousu')
            ->where('rh02_regist', '=', $matricula)
            ->distinct('rh02_anousu')
            ->orderBy('rh02_anousu', 'desc')
            ->pluck('rh02_anousu');
    }


    public function getComprovanteIRRF($matricula, $ano)
    {
        $matriculaDoServidor = RhPessoal::find($matricula);
        if (!$matriculaDoServidor) {
            throw new \Exception("Matrícula não encontrada!");
        }
        $instituicao = $matriculaDoServidor->rh01_instit;
        $oServidor = \ServidorRepository::getInstanciaByCodigo($matricula, null, null, $instituicao);
        $oComprovante = \ComprovanteRendimentoRepository::getPorMatriculaNoAno($oServidor, $ano, $instituicao);

        $oRendimento = new \stdClass();
        /**
         * Dados Pessoais
         */
        $oRendimento->cpf = db_formatar($oComprovante->getCgm()->getCpf(), 'cpf');
        $oRendimento->nome = db_translate($oServidor->getCgm()->getNome());
        $oRendimento->resp = '';
        $oRendimento->cnpj_fonte_pagadora = db_formatar($oComprovante->getFontePagadora(), 'cnpj');
        $oRendimento->nome_fonte_pagadora = ($oComprovante->getNomeFontePagadora());
        $oRendimento->pensionistas = '';
        $oRendimento->ano = $ano;
        $oRendimento->matricula = str_replace('}', '', str_replace('{', '', $matricula));
        $oRendimento->lotacao = $oComprovante->getLotacao();
        $oRendimento->num_comprovante = 1;

        /**
         * Rendimentos
         */
        $oRendimento->rendimento = $oComprovante->getValorTotalRendimentos();
        $oRendimento->prev_oficial = $oComprovante->getValorPrevidenciaOficial();
        $oRendimento->prev_privada = $oComprovante->getValorPrevidenciaPrivada();
        $oRendimento->pensao = $oComprovante->getValorPagoEmPensao();
        $oRendimento->irrf = $oComprovante->getValorPagoIRRF();
        $oRendimento->desconto_aposentadoria = $oComprovante->getValorDescontoAposentado() +
            $oComprovante->getValorDescontoAposentadoDecimoTerceiro();
        $oRendimento->diarias = $oComprovante->getValorDiarias();
        $oRendimento->valor_molestia = $oComprovante->getValorTotalMolestiaGrave();
        $oRendimento->ind_rescisao = $oComprovante->getValorIndenizacaoRescisao();
        $oRendimento->abono = $oComprovante->getValorAbono();
        $oRendimento->outros5 = $oComprovante->getValorOutrosRendimentos();

        /**
         * Decimeto 13 e plano de saude
         */
        $oRendimento->decimo_terceiro = $oComprovante->getValorDecimoTerceiroParaComprovante();
        $oRendimento->irrf_decimo_terceiro = $oComprovante->getValorPagoIRRFDecimoTerceiro();
        $oRendimento->outros_redimentos_decimo = 0;
        $oRendimento->gasto_plano_saude = $oComprovante->getValorPlanoSaude();
        $oRendimento->pensionistas = $oComprovante->getOutrasInformacoes();

        /**
         * RRAs
         */
        $oRendimento->rra_rendimentos_tributaveis = $oComprovante->getValorRendimentosTributaveisSobreRRA();
        $oRendimento->rra_previdencia = $oComprovante->getValorPrevidenciaSobreRRA();
        $oRendimento->rra_pensao = $oComprovante->getValorPensaoSobreRRA();
        $oRendimento->rra_irrf = $oComprovante->getValorIRRFSobreRRA();
        $oRendimento->rra_despesa_acao = $oComprovante->getValorDespesaDaAcao();
        $oRendimento->rra_quantidade_meses = $oComprovante->getQuantidadeDeMeses();
        $oRendimento->rra_isentos = $oComprovante->getValorIsencaoSobreRRA();

        /**
         * Detalhamento Quadro 4 Item 7
         */
        $oRendimento->detalhamento_rubricas = $oComprovante->getDetalhamentoRubricas();

        return $oRendimento;
    }
}
