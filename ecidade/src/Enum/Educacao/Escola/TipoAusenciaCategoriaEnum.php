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

/**
 * Class TipoAusenciaCategoriaEnum
 * @package ECidade\Enum\Educacao\Escola
 */
class TipoAusenciaCategoriaEnum extends Enum
{
    const UNKNOWN = null;
    const AUSENCIA = 1;
    const HORA_EXTRA = 2;
    const SUBSTITUICAO = 3;

    protected $labels = [
        self::UNKNOWN => '',
        self::AUSENCIA => 'Ausência',
        self::HORA_EXTRA => 'Hora extra',
        self::SUBSTITUICAO => 'Substituição',
    ];

    /**
     * @return string
     * @throws \Exception
     */
    public function name()
    {
        if (!isset($this->labels[$this->getValue()])) {
            throw new \Exception('Categoria não encontrada.');
        }

        return $this->labels[$this->getValue()];
    }

    /**
     * Creates a new value of some type
     *
     * @param mixed $value
     *
     * @throws UnexpectedValueException if incompatible type is given.
     * @throws ReflectionException
     */
    public function __construct($value)
    {
        return parent::__construct($this->isValid($value) ? $value : static::UNKNOWN);
    }
}
