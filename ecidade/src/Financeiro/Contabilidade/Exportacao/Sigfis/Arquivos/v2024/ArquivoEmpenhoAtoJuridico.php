<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;

class ArquivoEmpenhoAtoJuridico extends ArquivoBase
{
    protected $sNomeArquivo  = 'EmpenhoAtoJuridico';
    protected $iAnoUsu;
    protected $instit;

    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçaamentária
    */
    public function gerarDados()
    {
        $campos = [
        'e60_numemp', 'e60_codemp', 'e60_anousu', 'o58_orgao', 'o58_unidade', 'e166_tipoatojuridico', 'e171_dados'
        ];

        $empenhos = DB::table('empempenho')
        ->select($campos)
        ->join('emptipoatojuridico', 'e166_empempenho', '=', 'e60_numemp')
        ->join('orcdotacao', function (JoinClause $join) {
            $join->on('e60_anousu', 'o58_anousu')
                ->on('e60_coddot', 'o58_coddot');
        })
        ->join('empempenhooutrosdados', 'e171_numemp', '=', 'e60_numemp')
        ->where('e166_tipoatojuridico', '<>', '99')
        ->whereBetween('e60_emiss', [$this->dtDataInicial, $this->dtDataFinal])
        ->whereIn('e60_instit', $this->getListaInstituicoes())
        ->orderBy("e60_codemp")
        ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrardo nenhum empenho para o arquivo de Remessa ');
        }

        $obj = new \stdClass();
        $obj->EmpenhosAtosJuridicos = [];

        foreach ($empenhos as $empenho) {
            $outrosDados = json_decode($empenho->e171_dados);
            if (json_last_error() !== 0) {
                $msg = "[Empenho {$empenho->e60_numemp}]: Erro ao decodificar json {$empenho->e171_dados}";
                $this->addLog($msg);
            }

            $atoJuridico = (isset($outrosDados->ato_juridico)?$outrosDados->ato_juridico:"");
            if (empty($atoJuridico)) {
                continue;
            }

            $dadosEmpenhoAtoJuridico = (object)[
            'Identificador' => $empenho->e60_numemp,
            'CodigoUnidadeGestora' => $this->sCodigoTribunal,
            'Competencia' => $this->competencia,
            'CodigoOrgao' => $empenho->o58_orgao,
            'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
            'NumeroAtoJuridicoTCE' => $atoJuridico,
            'CodigoUnidadeGestoraAtoJuridico' => (
                isset($outrosDados->ug_ato_juridico) && !empty($outrosDados->ug_ato_juridico)?
                $outrosDados->ug_ato_juridico:$this->sCodigoTribunal
            ),
            'NumeroEmpenho' => $empenho->e60_codemp,
            'AnoEmpenho' => $empenho->e60_anousu,
            'CodigoTipoAtoJuridico' => $empenho->e166_tipoatojuridico
            ];

            $obj->EmpenhosAtosJuridicos[] = (object)['EmpenhoAtoJuridico' => $dadosEmpenhoAtoJuridico];
        }

        $this->aDados =  $obj;
    }
}
