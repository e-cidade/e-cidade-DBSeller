<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use DBDate;
use Illuminate\Database\Eloquent\Collection;

class StatusPacienteResource
{
    /**
     * @param Collection $movimentacoes
     * @return array
     */
    public static function toArray(Collection $movimentacoes)
    {
        $retorno = [];

        foreach ($movimentacoes as $movimentacao) {
            $index = $movimentacao->sd24_i_codigo;
            if (!array_key_exists($index, $retorno)) {
                $retorno[$index] = self::toObject($movimentacao);
                continue;
            }
            $retorno[$index]->historicos[] = self::atendimentoToObject($movimentacao);
        }

        return array_values($retorno);
    }

    /**
     * Cria um array onde cado índice equivale a um atendimento, contendo o objeto do primeiro atendimento
     * e um array de objetos de históricos deste atendimento.
     */
    public static function toObject($movimentacao)
    {
        return (object)[
            'faa' => $movimentacao->sd24_i_codigo,
            'cgs' => $movimentacao->sd24_i_numcgs,
            'nome' => $movimentacao->z01_v_nome,
            'nomeSocial' => $movimentacao->z01_nome_social,
            'dataNascimento' => date("d/m/Y", strtotime($movimentacao->z01_d_nasc)),
            'idadeCompleta'=> self::calcularIdadeCompleta($movimentacao->z01_d_nasc),
            'motivo' => $movimentacao->s144_c_descr,
            'data' => date('d/m/Y', strtotime($movimentacao->sd24_d_cadastro)),
            'hora' => $movimentacao->sd24_c_cadastro,
            'prioridade' => $movimentacao->sd91_local === 1 ? '' : $movimentacao->sd78_descricao,
            'corPrioridade' => $movimentacao->sd91_local === 1 ? '' : $movimentacao->sd78_cor,
            'setor' => $movimentacao->sd91_descricao,
            'medicoAtendendo' => $movimentacao->sd91_local === 1 ? '' : $movimentacao->atendimento,
            'situacao' => self::situacao($movimentacao->sd102_situacao),
            'codigoSituacao' => $movimentacao->sd102_situacao,
            'medicoEncaminhado' => $movimentacao->encaminhado,
            'historicos' => []
        ];
    }

    public static function atendimentoToObject($movimentacao)
    {
        return (object)[
            'data' => date('d/m/Y', strtotime($movimentacao->sd102_data)),
            'hora' => $movimentacao->sd102_hora,
            'prioridade' => $movimentacao->sd91_local === 1 ? '' : $movimentacao->sd78_descricao,
            'corPrioridade' => $movimentacao->sd91_local === 1 ? '' : $movimentacao->sd78_cor,
            'setor' => $movimentacao->sd91_descricao,
            'medicoAtendendo' => $movimentacao->sd91_local === 1 ? '' : $movimentacao->atendimento,
            'situacao' => self::situacao($movimentacao->sd102_situacao),
            'codigoSituacao' => $movimentacao->sd102_situacao,
            'medicoEncaminhado' => $movimentacao->encaminhado,
        ];
    }

    private static function situacao($valor)
    {
        $situacao = 'Aguard Atendimento';

        if ($valor === 2) {
            $situacao = 'Em Atendimento';
        }

        if ($valor === 3) {
            $situacao = 'Finalizado';
        }

        if ($valor === 5) {
            $situacao = 'Encaminhado';
        }

        return $situacao;
    }

    private static function calcularIdadeCompleta($dataNascimento)
    {
        $dataNascimentoObjeto = new DBDate($dataNascimento);
        $dataAtual = new DBDate(date("Y-m-d"));
        $intervalo_dias = DBDate::getIntervaloEntreDatas($dataNascimentoObjeto, $dataAtual)->days;
        return DBDate::getIdadeCompleta($intervalo_dias);
    }
}
