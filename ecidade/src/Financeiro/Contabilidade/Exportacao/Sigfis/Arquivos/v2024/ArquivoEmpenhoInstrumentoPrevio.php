<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoEmpenhoInstrumentoPrevio extends ArquivoBase
{
    protected $sNomeArquivo = 'EmpenhoInstrumentoPrevio';
    const LICITACAO = '1';
    const DISPENSA = '2';
    const INEXIGIBILIDADE = '3';
    const ATO_ADESAO_REGISTRO_PRECO = '4';
    const INEXISTENTE_JUSTIFICATIVA = '99';


    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu','e60_codcom',
            'o58_orgao', 'o58_unidade', 'e171_dados'
        ];

        $empenhos = DB::table('empempenho')
            ->select($campos)
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('e60_anousu', 'o58_anousu')
                    ->on('e60_coddot', 'o58_coddot');
            })
            ->leftJoin('empempenhooutrosdados', 'e171_numemp', '=', 'e60_numemp')
            ->leftJoin('emptipoatojuridico', 'e166_empempenho', '=', 'e60_numemp')
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('e60_emiss', [$this->dtDataInicial, $this->dtDataFinal])
            ->where('e166_tipoatojuridico', '!=', 99)
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrardo nenhum empenho para o arquivo de Remessa ');
        }

        $obj = new stdClass();
        $obj->EmpenhosInstrumentosPrevios = [];

        foreach ($empenhos as $empenho) {
            if (empty($empenho->e171_dados)) {
                continue;
            }

            $outrosDados = json_decode($empenho->e171_dados);
            if (json_last_error() !== 0) {
                $this->addLog("[Empenho {$empenho->e60_numemp}]: Erro ao decodificar json {$empenho->e171_dados}");
            }

            $atoJuridico = (isset($outrosDados->ato_juridico)?$outrosDados->ato_juridico:"");

            $ugAtoJuridico = (isset($outrosDados->ug_ato_juridico)?$outrosDados->ug_ato_juridico:"");

            $codigoInstrumentoPrevio = (isset($outrosDados->instrumento_previo)?$outrosDados->instrumento_previo:"");

            $ugInstrumentoPrevio = (isset($outrosDados->ug_instrumento_previo)?$outrosDados->ug_instrumento_previo:"");

            if (empty($codigoInstrumentoPrevio)) {
                $codigoInstrumentoPrevio = $atoJuridico;
            }

            if (empty($codigoInstrumentoPrevio)) {
                continue;
            }

            if (empty($ugInstrumentoPrevio)) {
                $ugInstrumentoPrevio = $ugAtoJuridico;
            }

            if (empty($ugInstrumentoPrevio)) {
                $ugInstrumentoPrevio = $this->sCodigoTribunal;
            }

            $codigoTipoInstrumentoPrevio = $this->deParaCodigoInstrumentoPrevio($empenho->e60_codcom);

            if ($codigoTipoInstrumentoPrevio != '99') {
                $data = (object)[
                    'Identificador' => $empenho->e60_numemp,
                    'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                    'Competencia' => $this->competencia,
                    'CodigoOrgao' => $empenho->o58_orgao,
                    'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
                    'NumeroAtoJuridicoTCE' =>  $codigoInstrumentoPrevio,
                    'CodigoUnidadeGestoraAtoJuridico' => $ugInstrumentoPrevio,
                    'NumeroEmpenho' => $empenho->e60_codemp,
                    'AnoEmpenho' => $empenho->e60_anousu,
                    'CodigoTipoInstrumentoPrevio' => $codigoTipoInstrumentoPrevio
                ];

                $obj->EmpenhosInstrumentosPrevios[] = (object)['EmpenhoInstrumentoPrevio' => $data];
            }
        }

        $this->aDados = $obj;
    }

    public function deParaCodigoInstrumentoPrevio($e60_codcom)
    {

        /**
         * Valores de compra da pctipocompra (pc50_codcom)
         */
        $licitacoes = [
            '1','2','3','4','10','12','16','17','19','20','21',
            '22','23','24','25','26'
        ];
        $dispensas = ['5','18','27','15'];
        $inexigibilidades = ['6','11'];
        $atosAdesao = [''];
        $inexistentes = ['7','8','9','13','14'];
        if (in_array($e60_codcom, $licitacoes)) {
            return static::LICITACAO;
        } elseif (in_array($e60_codcom, $dispensas)) {
            return static::DISPENSA;
        } elseif (in_array($e60_codcom, $inexigibilidades)) {
            return static::INEXIGIBILIDADE;
        } elseif (in_array($e60_codcom, $atosAdesao)) {
            return static::ATO_ADESAO_REGISTRO_PRECO;
        } elseif (in_array($e60_codcom, $inexistentes)) {
            return static::INEXISTENTE_JUSTIFICATIVA;
        }
    }
}
