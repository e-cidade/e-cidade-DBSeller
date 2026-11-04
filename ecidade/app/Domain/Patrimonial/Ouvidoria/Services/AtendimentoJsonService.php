<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Patrimonial\Ouvidoria\Model\Atendimento\Atendimento;

class AtendimentoJsonService
{
    public static function findJson($numero, $ano, $instituicao)
    {

        $atendimento = new Atendimento();
        $atendimento = $atendimento->numero($numero)
            ->ano($ano)
            ->instituicao($instituicao);

        if (!$atendimento->exists()) {
            throw new \Exception("Atendimento não encontrado!");
        }

        $atendimento = $atendimento->first();

        if (!$atendimento->has('atendimentoProcessoEletronico')) {
            throw new \Exception("Atendimento não foi criado através do processo eletrônico ");
        }


        return [
            'json' => $atendimento->atendimentoProcessoEletronico->ov33_informacoesprocesso,
               'atendimento_id' => $atendimento->getCodigo()
        ];
    }

    /**
     * @throws \Exception
     */
    public static function update($atendimentoId, $json)
    {

        require_once(modification(ECIDADE_PATH . 'model/ouvidoria/AtendimentoProcessoEletronico.model.php'));

        $atendimentoProcessoEletronico = \AtendimentoProcessoEletronico::findByAtendimento(
            $atendimentoId
        );

        if (!$atendimentoProcessoEletronico) {
            throw new \Exception("Não encontrado a solicitação de atendimento!");
        }


        $atendimentoProcessoEletronico->setInformacoesProcesso(
            $json
        );

        $atendimentoProcessoEletronico->save();
    }
}
