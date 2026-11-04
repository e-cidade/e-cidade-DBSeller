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

namespace ECidade\Enum\Educacao\Escola;

use ECidade\Enum\Enum;
use Exception;

/**
 * Class SituacaoMatriculaEnum
 *
 * @package ECidade\Enum\Educacao\Escola
 */
class SituacaoMatriculaEnum extends Enum
{
    const AVANCADO = 'AVANÇADO';
    const CANCELADO = 'CANCELADO';
    const MATRICULADO = 'MATRICULADO';
    const TRANSFERIDO_REDE = 'TRANSFERIDO REDE';
    const TRANSFERIDO_FORA = 'TRANSFERIDO FORA';
    const EVADIDO = 'EVADIDO';
    const FALECIDO = 'FALECIDO';
    const TROCA_TURMA = 'TROCA DE TURMA';
    const TROCA_MODALIDADE = 'TROCA DE MODALIDADE';
    const MATRICULA_INDEVIDA = 'MATRICULA INDEVIDA';
    const MATRICULA_TRANCADA = 'MATRICULA TRANCADA';
    const RECLASSIFICADO = 'RECLASSIFICADO';
    const DESISTENTE = 'DESISTENTE';
    const CLASSIFICADO = 'CLASSIFICADO';
    const INFREQUENTE = 'INFREQUENTE';
    const ABANDONO_ESCOLAR = 'ABANDONO ESCOLAR';

    /**
     * @return string
     * @throws Exception
     */
    public function sigla()
    {
        $data = array(
            self::AVANCADO => 'AV',
            self::CANCELADO => 'CA',
            self::MATRICULADO => 'MA',
            self::TRANSFERIDO_REDE => 'TR',
            self::TRANSFERIDO_FORA => 'TF',
            self::EVADIDO => 'EV',
            self::ABANDONO_ESCOLAR => 'AE',
            self::FALECIDO => 'FA',
            self::TROCA_TURMA => 'TT',
            self::TROCA_MODALIDADE => 'TM',
            self::MATRICULA_INDEVIDA => 'MI',
            self::MATRICULA_TRANCADA => 'MT',
            self::RECLASSIFICADO => 'RE',
            self::DESISTENTE => 'DE',
            self::CLASSIFICADO => 'CL',
            self::INFREQUENTE => 'INF'
         );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Sigla não encontrada.');
        }

        return $data[$this->getValue()];
    }
}
