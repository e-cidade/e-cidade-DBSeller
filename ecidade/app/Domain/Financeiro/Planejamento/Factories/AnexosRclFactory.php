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

namespace App\Domain\Financeiro\Planejamento\Factories;

use Exception;

class AnexosRclFactory
{
    /**
     * O retorno deve seguir o exemplo:
     * ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
     * onde:
     * $relatorio é o código do relatório
     * $programa é a view para selecionar os filtros
     * $rota é a rota de impressão
     * @param $exercicio
     * @return void
     * @throws Exception
     */
    public static function getPrograma($exercicio)
    {
        switch ($exercicio) {
            case 2022:
            default:
                return self::getDadosViewOutrosAnexos($exercicio);
        }
    }

    /**
     * getDadosViewOutrosAnexos
     *
     * @param $exercicio
     * @return void
     */
    private static function getDadosViewOutrosAnexos($exercicio)
    {
        return OutrosAnexosRclFactory::getDadosView($exercicio);
    }
}
