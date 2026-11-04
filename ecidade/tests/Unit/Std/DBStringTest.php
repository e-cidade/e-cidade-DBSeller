<?php

namespace Tests\Unit\Std;

use Tests\TestCase;

class DBStringTest extends TestCase
{

    const CARACTERES_COM_ACENTOS_MINUSCULO = "áàãâéêíóôõúüç";
    const CARACTERES_COM_ACENTOS_MAIUSCULO = "ÁÀÃÂÉÊÍÓÔÕÚÜÇ";
    const CARACTERES_SEM_ACENTOS_MINUSCULO = "aaaaeeiooouuc";
    const CARACTERES_SEM_ACENTOS_MAIUSCULO = "AAAAEEIOOOUUC";

    public function testupperCaseRemoveCaracteresEspeciaisUTF8()
    {
        $string = \DBString::upperCaseRemoveCaracteresEspeciais(
            self::CARACTERES_COM_ACENTOS_MINUSCULO
        );
        $this->assertEquals(
            \DBString::CARACTERES_COM_ACENTOS_MAIUSCULO,
            $string
        );
    }

    public function testupperCaseRemoveCaracteresEspeciaisISO88591()
    {
        $string = \DBString::upperCaseRemoveCaracteresEspeciais(\DBString::CARACTERES_COM_ACENTOS_MINUSCULO);
        $this->assertEquals(\DBString::CARACTERES_COM_ACENTOS_MAIUSCULO, $string);
    }

    public function testRemoverCaracteresEspeciaisAcentosUTF8()
    {
        $string = \DBString::removerCaracteresEspeciaisAcentos(
            self::CARACTERES_COM_ACENTOS_MINUSCULO . self::CARACTERES_COM_ACENTOS_MAIUSCULO
        );

        $this->assertEquals(
            \DBString::CARACTERES_SEM_ACENTOS_MINUSCULO . \DBString::CARACTERES_SEM_ACENTOS_MAIUSCULO,
            $string
        );
    }

    public function testRemoverCaracteresEspeciaisAcentosISO88591()
    {
        $string = \DBString::removerCaracteresEspeciaisAcentos(
            \DBString::CARACTERES_COM_ACENTOS_MINUSCULO . \DBString::CARACTERES_COM_ACENTOS_MAIUSCULO
        );

        $this->assertEquals(
            \DBString::CARACTERES_SEM_ACENTOS_MINUSCULO . \DBString::CARACTERES_SEM_ACENTOS_MAIUSCULO,
            $string
        );
    }

    public function testUpperCaseRemoveCaracteresEspeciaisCrase()
    {
        $string = \DBString::upperCaseRemoveCaracteresEspeciais(
            \DBString::EXEMPLO_NOME_COM_CARACTERES_ESPECIAIS_PARA_TESTE
        );

        $this->assertEquals(
            \DBString::EXEMPLO_ESPERADO_NOME_COM_CARACTERES_PARA_TESTE,
            $string
        );
    }
}
