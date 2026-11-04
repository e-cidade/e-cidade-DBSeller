<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class ArquivoRPInscricao extends ArquivoBase
{
    protected $sNomeArquivo = 'InscricaoRestosPagar';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp',
            'e60_anousu',
            'e91_anousu',
            'e60_codemp',
            'o58_orgao',
            'o58_unidade',

            'e91_vlremp', 'e91_vlranu', 'e91_vlrliq',
            'e91_vlrliq', 'e91_vlrpag'
        ];

        $empenhos = DB::table('empresto')
            ->select($campos)
            ->join('empenho.empempenho', 'e60_numemp', 'e91_numemp')
            ->join('orcamento.orcdotacao', function ($join) {
                $join->on('e60_anousu', 'o58_anousu')
                    ->on('e60_coddot', 'o58_coddot');
            })
            ->where('e91_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception(sprintf(
                'Não foi encontrado nenhum registro para o arquivo da Remessa de %s',
                'Restos a Pagar - Inscrição'
            ));
        }

        $obj = new stdClass();
        $obj->InscricoesRestosPagar = [];

        foreach ($empenhos as $empenho) {
            $valorRPNP = round($empenho->e91_vlremp - $empenho->e91_vlranu - $empenho->e91_vlrliq, 2);
            $vaorRPP = round($empenho->e91_vlrliq - $empenho->e91_vlrpag, 2);

            $data  = (object) [
                'Identificador' => $empenho->e60_numemp,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
                'Exercicio' => $empenho->e60_anousu,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'DataInscricao' => "{$empenho->e60_anousu}-12-31",
                'Competencia' => $this->competencia,
                'ValorRestosPagarProcessado' => ($vaorRPP<0?0:$vaorRPP),
                'ValorRestosPagarNaoProcessado' => ($valorRPNP<0?0:$valorRPNP),
                'Justificativa' => '',
            ];

            $obj->InscricoesRestosPagar[] = (object)[
                'InscricaoRestosPagar' => $data
            ];
        }

        $this->aDados =  $obj;
    }
}
