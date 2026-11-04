<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Carbon\Carbon;
use ECidade\File\Csv\Dumper\Dumper;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class MatrizService
{
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
    private $comEncerramento;

    /**
     * @var mixed
     */
    private $exercicio;

    private $competencia;

    /**
     * @param array $filtros
     * @return void
     */
    public function setFiltrosArray(array $filtros)
    {
        $this->competencia = sprintf('%s-%s', $filtros['exercicio'], $filtros['mes']);
        $this->comEncerramento = false;
        if ($filtros['mes'] == 13) {
            $filtros['mes'] = 12;
            $this->comEncerramento = true;
        }

        $lastDay = cal_days_in_month(CAL_GREGORIAN, $filtros['mes'], $filtros['exercicio']);
        $di = sprintf('%s-%s-%s', $filtros['exercicio'], $filtros['mes'], '01');
        $df = sprintf('%s-%s-%s', $filtros['exercicio'], $filtros['mes'], $lastDay);
        $this->filtroDataInicio = Carbon::createFromFormat('Y-m-d', $di);
        $this->filtroDataFinal = Carbon::createFromFormat('Y-m-d', $df);

        $this->exercicio = $filtros['exercicio'];

        $this->ibge = $this->getCodigoIBGE();
    }

    public function processar()
    {
        $filename = $this->emitirCsv();

        $zipFile = "tmp/SICONFI_saldos_contabeis_{$this->competencia}.zip";
        $zipArchiveParameter = ZipArchive::CREATE;
        if (file_exists($zipFile)) {
            $zipArchiveParameter = ZipArchive::OVERWRITE;
        }

        $zipArchive = new ZipArchive();
        $zipArchive->open($zipFile, $zipArchiveParameter);
        $zipArchive->addFile($filename);
        $zipArchive->close();

        return [
            'link' => $zipFile,
            'linkExterno' => ECIDADE_REQUEST_PATH . $zipFile
        ];
    }

    /**
     * @return string
     */
    public function emitirCsv()
    {
        $dumper = new Dumper();
        $dumper->setCsvControl(';');
        $filename = 'tmp/SICONFI.csv';
        $dumper->write(function () {
            $dados = $this->getDados();
            $header = $this->header();
            yield $header[0];
            yield $header[1];

            foreach ($dados as $dado) {
                yield $this->montaLinha($dado, 'SI');
                if ($dado->saldo_credito != 0) {
                    yield $this->montaLinha($dado, 'C');
                }
                if ($dado->saldo_debito > 0) {
                    yield $this->montaLinha($dado, 'D');
                }
                yield $this->montaLinha($dado, 'SF');
            }
        }, $filename);

        /**
         * Merge arquivos externos na matriz
         */
        $files = $this->getArquivoExterno();
        if ($files) {
            foreach ($files as $data) {
                file_put_contents($filename, $data->c54_arquivo . "\n", FILE_APPEND);
            }
        }

        return $filename;
    }

    private function header()
    {
        return [
            ["{$this->ibge}EX", $this->competencia],
            [
                'CONTA', 'IC1', 'TIPO1', 'IC2', 'TIPO2', 'IC3', 'TIPO3', 'IC4', 'TIPO4', 'IC5', 'TIPO5',
                'IC6', 'TIPO6', 'Valor', 'Tipo_valor', 'Natureza_valor'
            ]
        ];
    }

    private function montaLinha($dado, $tipoSaldo)
    {
        $linha = array_merge([$dado->estrutural_padrao], $this->montaAtributos($dado));
        $classe = substr($dado->estrutural_padrao, 0, 1);
        switch ($tipoSaldo) {
            case 'SI':
                $linha[] = $dado->saldo_anterior < 0 ? $dado->saldo_anterior * -1 : $dado->saldo_anterior;
                $linha[] = 'beginning_balance';
                $linha[] = sinalContaBalanceteVerificacao($classe, $dado->saldo_anterior);
                break;
            case 'D':
                $linha[] = $dado->saldo_debito;
                $linha[] = 'period_change';
                $linha[] = 'D';
                break;
            case 'C':
                $linha[] = $dado->saldo_credito *-1;
                $linha[] = 'period_change';
                $linha[] = 'C';
                break;
            case 'SF':
                $linha[] = $dado->saldo_final < 0 ? $dado->saldo_final * -1 : $dado->saldo_final;
                $linha[] = 'ending_balance';
                $linha[] = sinalContaBalanceteVerificacao($classe, $dado->saldo_final);
                break;
        }

        return $linha;
    }

    private function montaAtributos($dado)
    {
        $co = $dado->complemento;
        $at = 'CO';
        if ($dado->complemento == 0) {
            $co = '';
            $at = '';
        }
        switch ($dado->informacoescomplementares) {
            case 'PO':
                return [$dado->poder_ordao, 'PO', '', '', '', '', '', '', '', '', '', ''];
            case 'PO,FP':
                return [$dado->poder_ordao, 'PO', $dado->indicador_superavit, 'FP', '', '', '', '', '', '', '', ''];
            case 'PO,FP,DC,FR':
                return [
                    $dado->poder_ordao, 'PO',
                    $dado->indicador_superavit, 'FP',
                    $dado->divida_consolidada, 'DC',
                    $dado->siconfi, 'FR', '', '', '', '',
                ];
            case 'PO,FP,FR,CO':
                return [
                    $dado->poder_ordao, 'PO',
                    $dado->indicador_superavit, 'FP',
                    $dado->siconfi, 'FR',
                    $co, $at, '', '', '', ''
                ];
            case 'PO,FR,CO':
                return [
                    $dado->poder_ordao, 'PO',
                    $dado->siconfi, 'FR',
                    $co, $at, '', '', '', '', '', ''
                ];
            case 'PO,FR,CO,NR':
                return [
                    $dado->poder_ordao, 'PO',
                    $dado->siconfi, 'FR',
                    $co, $at,
                    $dado->nr, 'NR', '', '', '', ''
                ];
            case 'PO,FS,FR,CO,ND':
                return [
                    $dado->poder_ordao, 'PO',
                    "{$dado->funcao}{$dado->subfuncao}", 'FS',
                    $dado->siconfi, 'FR',
                    $co, $at,
                    $dado->nd, 'ND', '', ''
                ];

            case 'PO,FS,FR,CO,ND,AI':
                return [
                    $dado->poder_ordao, 'PO',
                    "{$dado->funcao}{$dado->subfuncao}", 'FS',
                    $dado->siconfi, 'FR',
                    $co, $at,
                    $dado->nd, 'ND',
                    $dado->ai, 'AI'
                ];
        }
    }

    /**
     * @return mixed
     */
    public function getDados()
    {
        $pl = sprintf(
            "contabilidade.matriz(%s, configuracaoinstituicaosiconfi.c125_db_config, '%s', '%s', %s, %s)",
            $this->exercicio,
            $this->filtroDataInicio->format('Y-m-d'),
            $this->filtroDataFinal->format('Y-m-d'),
            $this->comEncerramento ? 'true' : 'false',
            "'{}'"
        );

        $sql = "
        with dados as (
          select
                 bl.estrutural_padrao,
                 bl.reduzido,
                 bl.exercicio,
                 bl.instituicao,
                 bl.poder_ordao,
                 bl.siconfi,
                 case
                    when bl.complemento is null then null
                    when o200_msc is true then bl.complemento
                    else 0
                 end as complemento,
                 bl.indicador_superavit,
                 bl.divida_consolidada,
                 bl.nr,
                 bl.nd,
                 bl.ai,
                 bl.funcao,
                 bl.subfuncao,
                 bl.informacoescomplementares,
                 bl.saldo_anterior,
                 bl.saldo_debito,
                 bl.saldo_credito,
                 bl.saldo_final
            from configuracaoinstituicaosiconfi
            join {$pl} bl on bl.instituicao = configuracaoinstituicaosiconfi.c125_db_config
            left join orcamento.complementofonterecurso on o200_sequencial = bl.complemento
        ), agrupa as (
          select estrutural_padrao,
                 exercicio,
                 poder_ordao,
                 siconfi,
                 complemento,
                 indicador_superavit,
                 divida_consolidada,
                 nr,
                 nd,
                 ai,
                 funcao,
                 subfuncao,
                 informacoescomplementares,
                 sum(saldo_anterior)::numeric(15, 2) as saldo_anterior,
                 sum(saldo_debito)::numeric(15, 2) as saldo_debito,
                 sum(saldo_credito)::numeric(15, 2) as saldo_credito,
                 sum(saldo_final)::numeric(15, 2) as saldo_final
             from dados
             group by 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13
        ) select *
            from agrupa
           where (saldo_debito != 0 or saldo_credito != 0 or saldo_final != 0)
           order by estrutural_padrao, poder_ordao;
        ";

        return DB::select($sql);
    }

    private function getCodigoIBGE()
    {
        return DBConfig::select('db21_codigomunicipoestado')
            ->where('prefeitura', true)
            ->first()
            ->db21_codigomunicipoestado;
    }

    private function getArquivoExterno()
    {
        $dao = new \cl_conarquivospad();

        $competencia = implode('_', array_reverse(explode('-', $this->competencia)));
        $where = sprintf("c54_nomearq = 'MSC_EXTERNO_%s'", $competencia);
        $sql = $dao->sql_query_file(null, 'c54_arquivo', null, $where);
        $rs = db_query($sql);
        if (!$rs) {
            throw new \Exception("Erro ao consultar o arquivo externo.");
        }
        if (pg_num_rows($rs) > 0) {
            return \db_utils::getCollectionByRecord($rs);
        }
        return false;
    }
}
