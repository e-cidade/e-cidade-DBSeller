<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Core\Services\QueueService;
use App\Domain\RecursosHumanos\Pessoal\Model\ContrachequesBatches;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\ContraChequePdf;
use App\Jobs\RecursosHumanos\Pessoal\EmissaoContraChequeJob;
use DBPessoal;
use Exception;
use FolhaPagamento;

class ContraChequeService
{
    /**
     * @param $ano
     * @param $mes
     * @param $instituicao
     * @return void
     * @throws Exception
     */
    public function gerarContraChequePdf($ano, $mes, $instituicao)
    {
        $sql = $this->getSqlGerfs($instituicao, $ano, $mes);
        if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
            $sql = $this->getSqlEstruturaNova($instituicao, $ano, $mes);
        }

        $rsRhPessoalMov = db_query($sql);
        if (!$rsRhPessoalMov) {
            throw new Exception('Erro ao buscar contra cheques');
        }
        if (pg_num_rows($rsRhPessoalMov) == 0) {
            throw new Exception('Não foi encontrados contra cheques para emitir no período informado.');
        }

        $total = 0;
        $queueService = new QueueService(EmissaoContraChequeJob::class);
        while ($result = pg_fetch_object($rsRhPessoalMov)) {
            $contraChequePdf = new ContraChequePdf(
                $result->matricula,
                $ano,
                $mes,
                $result->folha,
                (int)$result->numero,
                $instituicao
            );

            dispatch(new EmissaoContraChequeJob($contraChequePdf, $queueService));
            $total++;
        }

        $loteContracheques = new ContrachequesBatches();
        $loteContracheques->rh269_instit = $instituicao;
        $loteContracheques->rh269_batch = $queueService->getBatch()->id;
        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $loteContracheques->rh269_competencia = "{$mes}/{$ano}";
        $loteContracheques->rh269_total = $total;
        $loteContracheques->save();
    }

    /**
     * @return object
     * @throws Exception
     */
    public static function buscarConfiguracaoContraCheque($tipo, $numeroFolha)
    {
        $config = (object)[
            'prefixo' => '',
            'arquivo' => '',
            'titulo' => '',
            'tipoFolha' => null,
            'sTipo' => "'x'"
        ];
        switch ($tipo) {
            case 'salario':
                $config->prefixo = 'r14';
                $config->arquivo = 'gerfsal';
                $config->titulo = 'SALÁRIO';
                $config->tipoFolha = FolhaPagamento::TIPO_FOLHA_SALARIO;
                break;
            case 'ferias':
                $config->prefixo = 'r31';
                $config->arquivo = 'gerffer';
                $config->titulo = 'FÉRIAS';
                $config->sTipo = 'r31_tpp';
                break;
            case 'rescisao':
                $config->prefixo = 'r20';
                $config->arquivo = 'gerfres';
                $config->titulo = 'RESCISÃO';
                $config->sTipo = 'r20_tpp';
                $config->tipoFolha = FolhaPagamento::TIPO_FOLHA_RESCISAO;
                break;
            case 'adiantamento':
                $config->prefixo = 'r22';
                $config->arquivo = 'gerfadi';
                $config->titulo = 'ADIANTAMENTO';
                $config->tipoFolha = FolhaPagamento::TIPO_FOLHA_ADIANTAMENTO;
                break;
            case '13salario':
                $config->prefixo = 'r35';
                $config->arquivo = 'gerfs13';
                $config->titulo = '13o. SALÁRIO';
                $config->tipoFolha = FolhaPagamento::TIPO_FOLHA_13o_SALARIO;
                break;
            case 'complementar':
                $config->prefixo = 'r48';
                $config->arquivo = 'gerfcom';
                $config->titulo = "COMPLEMENTAR {$numeroFolha}";
                $config->tipoFolha = FolhaPagamento::TIPO_FOLHA_COMPLEMENTAR;
                break;
            case 'fixo':
                $config->prefixo = 'r53';
                $config->arquivo = 'gerffx';
                $config->titulo = 'FIXO';
                break;
            case 'previden':
                $config->prefixo = 'r60';
                $config->arquivo = 'previden';
                $config->titulo = 'AJUSTE DA PREVIDÊNCIA';
                break;
            case 'irf':
                $config->prefixo = 'r61';
                $config->arquivo = 'ajusteir';
                $config->titulo = 'AJUSTE DO IRRF';
                break;
            case 'suplementar':
                $config->prefixo = 'r14';
                $config->arquivo = 'gerfsal';
                $config->titulo = "SUPLEMENTAR {$numeroFolha}";
                $config->tipoFolha = FolhaPagamento::TIPO_FOLHA_SUPLEMENTAR;
                break;
            default:
                throw new Exception("Tipo de folha não configurado para emissão de contra cheques.");
        }

        return $config;
    }

    /**
     * @param $codigoAutenticacao
     * @return array|false
     * @throws Exception
     */
    public function getByCodigoAutenticacao($codigoAutenticacao)
    {
        $daoRhEmiteContraCheque = new \cl_rhemitecontracheque();
        $sql = $daoRhEmiteContraCheque->sql_query_file(
            null,
            'rh85_estorage',
            null,
            "rh85_codautent = '{$codigoAutenticacao}' AND rh85_estorage is not null"
        );
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar contra cheque. Tente novamente mais tarde!");
        }
        if (pg_num_rows($rs) == 0) {
            return false;
        }
        $id = pg_fetch_object($rs)->rh85_estorage;
        return StorageHelper::getContentsBase64($id);
    }

    /**
     * @param $instituicao
     * @param $ano
     * @param $mes
     * @return string
     */
    private function getSqlGerfs($instituicao, $ano, $mes)
    {
        $where = ["rh01_instit = {$instituicao}", "rh02_anousu = {$ano}", "rh02_mesusu = {$mes}"];
        /**
         * Reforada forma de buscar os cgm, para nao retornar cgm sem calculo no mes
         */
        $fromBase = <<<SQL
            FROM
                rhpessoalmov
                INNER JOIN rhpessoal ON rhpessoal.rh01_regist = rhpessoalmov.rh02_regist
                    AND rhpessoal.rh01_instit = rhpessoalmov.rh02_instit
                INNER JOIN cgm ON cgm.z01_numcgm = rhpessoal.rh01_numcgm
SQL;
        $whereBase = "where "  . implode(' AND ', $where);
        /**
         * Buscamos folhas de salario, complementar, 13 e rescisao com o union abaixo
         */
        $sql = <<<SQL
            SELECT DISTINCT
                rh01_regist as matricula,
                'salario' as folha,
                r14_semest as numero
            {$fromBase}
                inner join pessoal.gerfsal on r14_regist = rh01_regist
                    and r14_anousu = rh02_anousu and r14_mesusu = rh02_mesusu
            {$whereBase}
            UNION
            SELECT DISTINCT
                rh01_regist as matricula,
                'complementar' as folha,
                r48_semest as numero
            {$fromBase}
                inner join pessoal.gerfcom on r48_regist = rh01_regist
                    and r48_anousu = rh02_anousu and r48_mesusu = rh02_mesusu
            {$whereBase}
            UNION
            SELECT DISTINCT
                rh01_regist as matricula,
                'adiantamento' as folha,
                0 as numero
            {$fromBase}
                inner join pessoal.gerfadi on r22_regist = rh01_regist
                    and r22_anousu = rh02_anousu and r22_mesusu = rh02_mesusu
            {$whereBase}
            UNION
            SELECT DISTINCT
                rh01_regist as matricula,
                '13salario' as folha,
                r35_semest as numero
            {$fromBase}
                inner join pessoal.gerfs13 on r35_regist = rh01_regist
                    and r35_anousu = rh02_anousu and r35_mesusu = rh02_mesusu
            {$whereBase}
            UNION
            SELECT DISTINCT
                rh01_regist as matricula,
                'rescisao' as folha,
                r20_semest as numero
            {$fromBase}
                inner join pessoal.gerfres on r20_regist = rh01_regist
                    and r20_anousu = rh02_anousu and r20_mesusu = rh02_mesusu
            {$whereBase} order by matricula
SQL;
        return $sql;
    }

    /**
     * @param $instituicao
     * @param $ano
     * @param $mes
     * @return string
     */
    private function getSqlEstruturaNova($instituicao, $ano, $mes)
    {
        return <<<SQL
SELECT DISTINCT rh143_regist as matricula,
        case when rh141_tipofolha = 1 then 'salario'
             when rh141_tipofolha = 2 then 'rescisao'
             when rh141_tipofolha = 3 then 'complementar'
             when rh141_tipofolha = 4 then 'adiantamento'
             when rh141_tipofolha = 5 then 'salario'
             when rh141_tipofolha = 6 then 'suplementar'
             end as folha ,
        rh141_codigo as numero
 FROM rhfolhapagamento
          INNER JOIN rhhistoricocalculo ON rh143_folhapagamento = rh141_sequencial
          INNER JOIN rhtipofolha ON rhtipofolha.rh142_sequencial = rhfolhapagamento.rh141_tipofolha
 WHERE rh141_mesusu = {$mes}
    AND rh141_anousu = {$ano}
    AND rh141_instit = {$instituicao}
SQL;
    }

    public function buscarLotes($instit)
    {
        $lotesContracheques = ContrachequesBatches::query()->where('rh269_instit', $instit)
                                                           ->orderBy('rh269_codigo', 'desc')
                                                           ->get();

        return $lotesContracheques->map(function (ContrachequesBatches $lote) {
            $totalNaFila = $lote->batch->queuedJobs()->count();
            $totalFalha = $lote->batch->queuedJobs()->whereNotNull('failed_at')->count();
            $totalProcessados = $lote->rh269_total + $totalFalha - $totalNaFila;
            $quantidade = sprintf(
                "%s de %s",
                $totalProcessados,
                $lote->rh269_total
            );

            $percentual = floor(($totalProcessados / $lote->rh269_total) * 100);
            return (object)[
                'batch_id' => $lote->rh269_batch,
                'competencia' => $lote->rh269_competencia,
                'quantidade' => $quantidade,
                'status' => $percentual,
                'cancelado' => $lote->batch->cancelled,
                'falhas' => $totalFalha
            ];
        });
    }
}
