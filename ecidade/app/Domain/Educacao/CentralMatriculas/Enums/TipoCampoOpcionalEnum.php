<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Educacao\CentralMatriculas\Enums;

use ECidade\Enum\Enum;

class TipoCampoOpcionalEnum extends Enum
{
    const ESTADO_CIVIL = 1;
    const CARTA0_SUS = 2;
    const MATRICULA_SERVIDOR = 3;
    const BOLSA_FAMILIA = 4;
    const CPF_ALUNO = 5;
    const COR_RACA = 6;

    const NOME_SOCIAL = 7;
    const MAE_VITIMA_VIOLENCIA = 8;
    const RENDA_FAMILIAR = 9;
    const PROFISSOES = 10;
    const MAE_ECONOMICAMENTE_ATIVA = 11;

    public function name()
    {
        $data = array(
            self::ESTADO_CIVIL => "Estado Civil",
            self::CARTA0_SUS => "Cartão Sus",
            self::MATRICULA_SERVIDOR => "Matricula Servidor",
            self::BOLSA_FAMILIA => "Bolsa Familia",
            self::CPF_ALUNO => "CPF Aluno",
            self::COR_RACA => "Cor/Raça",
            self::NOME_SOCIAL => "Nome Social",
            self::MAE_VITIMA_VIOLENCIA => "Mãe Vitima de Violência",
            self::RENDA_FAMILIAR => "Renda Familiar",
            self::PROFISSOES => "Profissões",
            self::MAE_ECONOMICAMENTE_ATIVA => "Mãe Economicamente Ativa"
        );

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Tipo não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
