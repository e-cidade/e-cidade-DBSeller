<?php

namespace App\Domain\Saude\TFD\Services;

use App\Domain\Saude\TFD\Models\Passageiro;
use App\Domain\Saude\TFD\Models\PassageiroRetorno;
use App\Domain\Saude\TFD\Models\VeiculoDestino;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class IndiqueVeiculoService
{
    /**
     * @param $viagem
     * @return array
     */
    public function getPassageirosCancelados($viagem)
    {
        $retorno = [];
        $passageiroValido = 1;

        $campos = [
            'tf01_i_codigo',
            'tf19_i_colo',
            'tf19_i_fica',
            'tf19_i_valido',
            'z01_i_cgsund',
            'z01_i_familiamicroarea',
            'z01_nome',
            'z01_v_cgccpf',
            'z01_v_ident',
            'z01_v_nome',
            'tf18_i_codigo',
            'tf18_c_horaretorno',
            'tf18_d_dataretorno',
        ];

        $dados = VeiculoDestino::query()
            ->select($campos)
            ->where('tf18_i_codigo', $viagem)
            ->where('tf19_i_valido', $passageiroValido)
            ->join('tfd_passageiroveiculo', 'tf19_i_veiculodestino', '=', 'tf18_i_codigo')
            ->join('cgs_und', 'z01_i_cgsund', '=', 'tf19_i_cgsund')
            ->join('tfd_pedidotfd', 'tf01_i_codigo', '=', 'tf19_i_pedidotfd')
            ->join('tfd_agendamentoprestadora', 'tf16_i_pedidotfd', '=', 'tf01_i_codigo')
            ->join('tfd_prestadoracentralagend', 'tf10_i_codigo', '=', 'tf16_i_prestcentralagend')
            ->join('tfd_prestadora', 'tf25_i_codigo', '=', 'tf10_i_prestadora')
            ->join('cgm', 'z01_numcgm', '=', 'tf25_i_cgm')
            ->get();

        $retorno['passageiros'] = [];
        $retorno['dataRetorno'] = '';
        $retorno['horaRetorno'] = '';

        if (count($dados) > 0) {
            foreach ($dados as $passageiro) {
                $retorno['passageiros'][] = (object)[
                    'tf01_i_codigo' => $passageiro->tf01_i_codigo,
                    'tf19_i_colo' => $passageiro->tf19_i_colo,
                    'tf19_i_fica' => $passageiro->tf19_i_fica,
                    'z01_i_cgsund' => $passageiro->z01_i_cgsund,
                    'z01_i_familiamicroarea' => $passageiro->z01_i_familiamicroarea,
                    'z01_nome' => $passageiro->z01_nome,
                    'z01_v_cgccpf' => $passageiro->z01_v_cgccpf,
                    'z01_v_ident' => $passageiro->z01_v_ident,
                    'z01_v_nome' => $passageiro->z01_v_nome,
                    'vinculado' => 1,
                    'tipo' => 1
                ];
            }

            $retorno['dataRetorno'] = date("d/m/Y", strtotime($dados[0]->tf18_d_dataretorno));
            $retorno['horaRetorno'] = $dados[0]->tf18_c_horaretorno;
        }
        return $retorno;
    }


    /**
     * @param $agendamento
     * @return Builder[]|Collection
     */
    public function getPassageirosRetorno($viagem)
    {
        $dados = PassageiroRetorno::query()
            ->select('z01_v_nome', 'tf19_i_cgsund')
            ->where('tf31_i_veiculodestino', $viagem)
            ->where('tf31_i_valido', 1)
            ->join('tfd_passageiroveiculo', 'tf19_i_codigo', '=', 'tf31_i_passageiroveiculo')
            ->join('cgs_und', 'z01_i_cgsund', '=', 'tf19_i_cgsund')
            ->get();

        return $dados;
    }

    /**
     * @param $viagem
     * @return array
     */
    public function getLotacaoCancelados($viagem)
    {
        $totalPacientes = 0;
        $totalAcompanhantes = 0;
        $totalColo = 0;

        $passageiros = Passageiro::query()->where('tf19_i_veiculodestino', $viagem)->get();

        foreach ($passageiros as $passageiro) {
            if ($passageiro->tf19_i_colo == 1) {
                $totalColo++;
            }

            if ($passageiro->tf19_i_tipopassageiro == 1) {
                $totalPacientes++;
            } else {
                $totalAcompanhantes++;
            }
        }

        return compact('totalPacientes', 'totalAcompanhantes', 'totalColo');
    }
}
