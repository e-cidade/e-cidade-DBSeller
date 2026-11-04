<?php

namespace ECidade\Enum\Saude\ESF;

use ECidade\Enum\Enum;

class DoseEnum extends Enum
{
    const PRIMEIRA_DOSE = 1;
    const SEGUNDA_DOSE = 2;
    const TERCEIRA_DOSE = 3;
    const QUARTA_DOSE = 4;
    const QUINTA_DOSE = 5;
    const PRIMEIRO_REFORCO = 6;
    const SEGUNDO_REFORCO = 7;
    const DOSE = 8;
    const UNICA = 9;
    const REVACINACAO = 10;
    const TRATAMENTO_COM_UMA_DOSE = 11;
    const TRATAMENTO_COM_DUAS_DOSES = 12;
    const TRATAMENTO_COM_TRES_DOSES = 13;
    const TRATAMENTO_COM_QUATRO_DOSES = 14;
    const TRATAMENTO_COM_CINCO_DOSES = 15;
    const TRATAMENTO_COM_SEIS_DOSES = 16;
    const TRATAMENTO_COM_SETE_DOSES = 17;
    const TRATAMENTO_COM_OITO_DOSES = 18;
    const TRATAMENTO_COM_NOVE_DOSES = 19;
    const TRATAMENTO_COM_DEZ_DOSES = 20;
    const TRATAMENTO_COM_ONZE_DOSES = 21;
    const TRATAMENTO_COM_DOZE_DOSES = 22;
    const TRATAMENTO_COM_TREZE_DOSES = 23;
    const TRATAMENTO_COM_QUATORZE_DOSES = 24;
    const TRATAMENTO_COM_QUINZE_DOSES = 25;
    const TRATAMENTO_COM_DEZESSEIS_DOSES = 26;
    const TRATAMENTO_COM_DEZESSETE_DOSES = 27;
    const TRATAMENTO_COM_DEZOITO_DOSES = 28;
    const TRATAMENTO_COM_DEZENOVE_DOSES = 29;
    const TRATAMENTO_COM_VINTE_DOSES = 30;
    const TRATAMENTO_COM_VINTE_QUATRO_DOSES = 31;
    const PRIMEIRA_DOSE_REVACINACAO = 32;
    const SEGUNDA_DOSE_REVACINACAO = 33;
    const TERCEIRA_DOSE_REVACINACAO = 34;
    const QUARTA_DOSE_REVACINACAO = 35;
    const DOSE_INICIAL = 36;
    const DOSE_ADICIONAL = 37;
    const REFORCO = 38;
    const TERCEIRO_REFORCO = 39;
    const QUARTO_REFORCO = 40;
    const QUINTO_REFORCO = 41;
    const SEXTO_REFORCO = 42;

    const QUINTA_DOSE_REVACINACAO = 43;

    const PRIMEIRA_DOSE_FRACIONADA = 44;

    const SEGUNDA_DOSE_FRACIONADA = 45;

    const TERCEIRA_DOSE_FRACIONADA = 46;

    const QUARTA_DOSE_FRACIONADA = 47;

    const QUINTA_DOSE_FRACIONADA = 48;

    const PRIMEIRA_DOSE_DOBRADA = 49;

    const SEGUNDA_DOSE_DOBRADA = 50;

    const TERCEIRA_DOSE_DOBRADA = 51;

    const QUARTA_DOSE_DOBRADA = 52;

    const PRIMEIRA_DOSE_REVACINAÇÃO_DOBRADA = 53;

    const SEGUNDA_DOSE_REVACINAÇÃO_DOBRADA = 54;

    const TERCEIRA_DOSE_REVACINAÇÃO_DOBRADA = 55;

    const QUARTA_DOSE_REVACINAÇÃO_DOBRADA = 56;

    const DOSE_ZERO = 57;

    const REFORÇO_ZERO = 58;

    const PROFILAXIA_COM_1_FRASCO_AMPOLA_AMPOLA = 59;

    const PROFILAXIA_COM_2_FRASCOS_AMPOLAS_AMPOLAS = 60;

    const PROFILAXIA_COM_3_FRASCOS_AMPOLAS_AMPOLAS = 61;

    const PROFILAXIA_COM_4_FRASCOS_AMPOLAS_AMPOLAS = 62;

    const PROFILAXIA_COM_5_FRASCOS_AMPOLAS_AMPOLAS = 63;

    const PROFILAXIA_COM_6_FRASCOS_AMPOLAS_AMPOLAS = 64;

    const PROFILAXIA_COM_7_FRASCOS_AMPOLAS_AMPOLAS = 65;

    const PROFILAXIA_COM_8_FRASCOS_AMPOLAS_AMPOLAS = 66;

    const PROFILAXIA_COM_9_FRASCOS_AMPOLAS_AMPOLAS = 67;

    const PROFILAXIA_COM_10_FRASCOS_AMPOLAS_AMPOLAS = 68;

    const PROFILAXIA_COM_11_FRASCOS_AMPOLAS_AMPOLAS = 69;

    const PROFILAXIA_COM_12_FRASCOS_AMPOLAS_AMPOLAS = 70;

    const PROFILAXIA_COM_13_FRASCOS_AMPOLAS_AMPOLAS = 71;

    const PROFILAXIA_COM_14_FRASCOS_AMPOLAS_AMPOLAS = 72;

    const PROFILAXIA_COM_15_FRASCOS_AMPOLAS_AMPOLAS = 73;

    const PROFILAXIA_COM_16_FRASCOS_AMPOLAS_AMPOLAS = 74;

    const PROFILAXIA_COM_17_FRASCOS_AMPOLAS_AMPOLAS = 75;

    const PROFILAXIA_COM_18_FRASCOS_AMPOLAS_AMPOLAS = 76;

    const PROFILAXIA_COM_19_FRASCOS_AMPOLAS_AMPOLAS = 77;

    const PROFILAXIA_COM_20_FRASCOS_AMPOLAS_AMPOLAS = 78;

    const PROFILAXIA_COM_21_FRASCOS_AMPOLAS_AMPOLAS = 79;

    const PROFILAXIA_COM_22_FRASCOS_AMPOLAS_AMPOLAS = 80;

    const PROFILAXIA_COM_23_FRASCOS_AMPOLAS_AMPOLAS = 81;

    const PROFILAXIA_COM_24_FRASCOS_AMPOLAS_AMPOLAS = 82;

    /**
     * @return string
     */
    public function name()
    {
        $data = [
            self::PRIMEIRA_DOSE => '1ª Dose',
            self::SEGUNDA_DOSE => '2ª Dose',
            self::TERCEIRA_DOSE => '3ª Dose',
            self::QUARTA_DOSE => '4ª Dose',
            self::QUINTA_DOSE => '5ª Dose',
            self::PRIMEIRO_REFORCO => '1º Reforço',
            self::SEGUNDO_REFORCO => '2º Reforço',
            self::DOSE => 'Dose',
            self::UNICA => 'Única',
            self::REVACINACAO => 'Revacinação',
            self::TRATAMENTO_COM_UMA_DOSE => 'Tratamento com uma dose',
            self::TRATAMENTO_COM_DUAS_DOSES => 'Tratamento com duas doses',
            self::TRATAMENTO_COM_TRES_DOSES => 'Tratamento com três doses',
            self::TRATAMENTO_COM_QUATRO_DOSES => 'Tratamento com quatro doses',
            self::TRATAMENTO_COM_CINCO_DOSES => 'Tratamento com cinco doses',
            self::TRATAMENTO_COM_SEIS_DOSES => 'Tratamento com seis doses',
            self::TRATAMENTO_COM_SETE_DOSES => 'Tratamento com sete doses',
            self::TRATAMENTO_COM_OITO_DOSES => 'Tratamento com oito doses',
            self::TRATAMENTO_COM_NOVE_DOSES => 'Tratamento com nove doses',
            self::TRATAMENTO_COM_DEZ_DOSES => 'Tratamento com dez doses',
            self::TRATAMENTO_COM_ONZE_DOSES => 'Tratamento com onze doses',
            self::TRATAMENTO_COM_DOZE_DOSES => 'Tratamento com doze doses',
            self::TRATAMENTO_COM_TREZE_DOSES => 'Tratamento com treze doses',
            self::TRATAMENTO_COM_QUATORZE_DOSES => 'Tratamento com quatorze doses',
            self::TRATAMENTO_COM_QUINZE_DOSES => 'Tratamento com quinze doses',
            self::TRATAMENTO_COM_DEZESSEIS_DOSES => 'Tratamento com dezesseis doses',
            self::TRATAMENTO_COM_DEZESSETE_DOSES => 'Tratamento com dezessete doses',
            self::TRATAMENTO_COM_DEZOITO_DOSES => 'Tratamento com dezoito doses',
            self::TRATAMENTO_COM_DEZENOVE_DOSES => 'Tratamento com dezenove doses',
            self::TRATAMENTO_COM_VINTE_DOSES => 'Tratamento com vinte doses',
            self::TRATAMENTO_COM_VINTE_QUATRO_DOSES => 'Tratamento com vinte quatro doses',
            self::PRIMEIRA_DOSE_REVACINACAO => '1ª Dose Revacinação',
            self::SEGUNDA_DOSE_REVACINACAO => '2ª Dose Revacinação',
            self::TERCEIRA_DOSE_REVACINACAO => '3ª Dose Revacinação',
            self::QUARTA_DOSE_REVACINACAO => '4ª Dose Revacinação',
            self::DOSE_INICIAL => 'Dose Inicial',
            self::DOSE_ADICIONAL => 'Dose Adicional',
            self::REFORCO => 'Reforço',
            self::TERCEIRO_REFORCO => '3º Reforço',
            self::QUARTO_REFORCO => '4º Reforço',
            self::QUINTO_REFORCO => '5º Reforço',
            self::SEXTO_REFORCO => '6º Reforço',
            self::QUINTA_DOSE_REVACINACAO => '5ª Dose Revacinacao',
            self::PRIMEIRA_DOSE_FRACIONADA => '1ª Dose Fracionada',
            self::SEGUNDA_DOSE_FRACIONADA => '2ª Dose Fracionada',
            self::TERCEIRA_DOSE_FRACIONADA => '3ª Dose Fracionada',
            self::QUARTA_DOSE_FRACIONADA => '4ª Dose Fracionada',
            self::QUINTA_DOSE_FRACIONADA => '5ª Dose Fracionada',
            self::PRIMEIRA_DOSE_DOBRADA => '1ª Dose Dobrada',
            self::SEGUNDA_DOSE_DOBRADA => '2ª Dose Dobrada',
            self::TERCEIRA_DOSE_DOBRADA => '3ª Dose Dobrada',
            self::QUARTA_DOSE_DOBRADA => '4ª Dose Dobrada',
            self::PRIMEIRA_DOSE_REVACINAÇÃO_DOBRADA => '1ª Dose Revacinação Dobrada',
            self::SEGUNDA_DOSE_REVACINAÇÃO_DOBRADA => '2ª Dose Revacinação Dobrada',
            self::TERCEIRA_DOSE_REVACINAÇÃO_DOBRADA => '3ª Dose Revacinação Dobrada',
            self::QUARTA_DOSE_REVACINAÇÃO_DOBRADA => '4ª Dose Revacinação Dobrada',
            self::DOSE_ZERO => 'Dose Zero',
            self::REFORÇO_ZERO => 'Reforço Zero',
            self::PROFILAXIA_COM_1_FRASCO_AMPOLA_AMPOLA => 'Profilaxia com 1 frasco-ampola/ampola',
            self::PROFILAXIA_COM_2_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 2 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_3_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 3 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_4_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 4 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_5_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 5 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_6_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 6 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_7_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 7 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_8_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 8 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_9_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 9 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_10_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 10 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_11_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 11 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_12_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 12 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_13_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 13 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_14_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 14 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_15_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 15 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_16_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 16 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_17_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 17 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_18_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 18 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_19_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 19 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_20_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 20 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_21_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 21 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_22_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 22 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_23_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 23 frascos-ampolas/ampolas',
            self::PROFILAXIA_COM_24_FRASCOS_AMPOLAS_AMPOLAS => 'Profilaxia com 24 frascos-ampolas/ampolas'
        ];

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Opção inválida.');
        }

        return $data[$this->getValue()];
    }
}
