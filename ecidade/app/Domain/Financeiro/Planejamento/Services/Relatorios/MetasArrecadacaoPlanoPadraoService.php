<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use Illuminate\Database\Eloquent\Collection;

class MetasArrecadacaoPlanoPadraoService extends MetasArrecadacaoService
{
    /**
     * @return array
     */
    public function processar()
    {
        $estimativas = $this->getEstimativas();
        $this->agruparPorReceita($estimativas);
        $this->totalizar();

        return $this->dados;
    }

    protected function agruparPorReceita(Collection $estimativas)
    {
        $dados = $this->processaEstimativas($estimativas);
        $this->dados['dados'] = $this->agrupaPeloEmentarioPadrao($dados);
        return $this->dados['dados'];
    }

    private function ementarioPadrao()
    {
        $campos = ['conta', 'nome'];
        $exercicio = $this->exercicioMapeamento();

        return PlanoReceita::query()
            ->select($campos)
            ->selectRaw("array_to_string(array_agg(c60_estrut), ',') as estruturais")
            ->join('planoreceitaconplanoorcamento', 'planoreceita_id', 'planoreceita.id')
            ->join('conplanoorcamento', 'c60_codigo', 'conplanoorcamento_codigo')
            ->join('estimativareceita', function ($join) {
                $join->on('orcfontes_id', '=', 'c60_codcon')
                    ->where('planejamento_id', '=', $this->planejamento->pl2_codigo);
            })
            ->join('conplanoorcamentoanalitica', function ($join) {
                $join->on('c61_codcon', '=', 'c60_codcon')
                    ->on('c61_anousu', '=', 'c60_anousu');
            })
            ->where('exercicio', $exercicio)
            ->where('uniao', $this->ementario === 'uniao')
            ->whereRaw('(substring(c60_estrut, 1, 1)::int = 4 or substring(c60_estrut, 1, 1)::int = 9)')
            ->groupBy($campos)
            ->orderBy('conta')
            ->get()
            ->map(function (PlanoReceita $planoReceita) {
                $planoReceita->ecidade = explode(',', $planoReceita->estruturais);
                return $planoReceita;
            });
    }

    private function agrupaPeloEmentarioPadrao($estimativasAgrupadas)
    {
        $agrupadosEmentarioPadrao = [];
        $ementario = $this->ementarioPadrao();
        foreach ($ementario as $conta) {
            $padrao = $this->stdContaEmentario($conta);
            foreach ($estimativasAgrupadas as $index => $estimativaEcidade) {
                if (in_array($estimativaEcidade->estrutural, $conta->ecidade)) {
                    $padrao->valor += (float)$estimativaEcidade->valor;

                    if ($this->periodicidade === 'mensal') {
                        $this->somaMensal($padrao, $estimativaEcidade);
                    }
                    if ($this->periodicidade === 'bimestral') {
                        $this->somaBimestral($padrao, $estimativaEcidade);
                    }

                    unset($estimativasAgrupadas[$index]);
                }
            }

            $agrupadosEmentarioPadrao[] = $padrao;
        }

        return $agrupadosEmentarioPadrao;
    }

    private function stdContaEmentario($conta)
    {
        $periodicidade = [
            "bimestre_1" => 0,
            "bimestre_2" => 0,
            "bimestre_3" => 0,
            "bimestre_4" => 0,
            "bimestre_5" => 0,
            "bimestre_6" => 0,
        ];
        if ($this->periodicidade === 'mensal') {
            $periodicidade = [
                "janeiro" => 0,
                "fevereiro" => 0,
                "marco" => 0,
                "abril" => 0,
                "maio" => 0,
                "junho" => 0,
                "julho" => 0,
                "agosto" => 0,
                "setembro" => 0,
                "outubro" => 0,
                "novembro" => 0,
                "dezembro" => 0,
            ];
        }
        $estruturalReceitaPadrao = new EstruturalReceitaPadrao($conta->conta);
        $conta = [
            "estrutural" => $estruturalReceitaPadrao->getEstrutural(),
            "estrutural_mascara" => $estruturalReceitaPadrao->getEstruturalComMascara(),
            "natureza" => $conta->nome,
            "valor" => 0
        ];

        return (object)array_merge($conta, $periodicidade);
    }

    private function somaMensal(&$padrao, $estimativaEcidade)
    {
        $padrao->janeiro += $estimativaEcidade->janeiro;
        $padrao->fevereiro += $estimativaEcidade->fevereiro;
        $padrao->marco += $estimativaEcidade->marco;
        $padrao->abril += $estimativaEcidade->abril;
        $padrao->maio += $estimativaEcidade->maio;
        $padrao->junho += $estimativaEcidade->junho;
        $padrao->julho += $estimativaEcidade->julho;
        $padrao->agosto += $estimativaEcidade->agosto;
        $padrao->setembro += $estimativaEcidade->setembro;
        $padrao->outubro += $estimativaEcidade->outubro;
        $padrao->novembro += $estimativaEcidade->novembro;
        $padrao->dezembro += $estimativaEcidade->dezembro;
    }

    private function somaBimestral(&$padrao, $estimativaEcidade)
    {
        $padrao->bimestre_1 += $estimativaEcidade->bimestre_1;
        $padrao->bimestre_2 += $estimativaEcidade->bimestre_2;
        $padrao->bimestre_3 += $estimativaEcidade->bimestre_3;
        $padrao->bimestre_4 += $estimativaEcidade->bimestre_4;
        $padrao->bimestre_5 += $estimativaEcidade->bimestre_5;
        $padrao->bimestre_6 += $estimativaEcidade->bimestre_6;
    }
}
