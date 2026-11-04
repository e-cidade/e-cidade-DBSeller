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

namespace App\Domain\Financeiro\Contabilidade\Factories;

use Exception;

class AnexosRREOFactory
{
    /**
     * O retorno deve seguir o exemplo:
     * ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
     * onde:
     * $relatorio é o código do relatório
     * $programa é a view para selecionar os filtros
     * $rota é a rota de impressão
     * @param $anexo
     * @param $exercicio
     * @return array
     * @throws Exception
     */
    public static function getPrograma($anexo, $exercicio)
    {
        switch ($anexo) {
            case 1:
                return self::getDadosViewAnexoI($exercicio);
            case 3:
                return self::getDadosViewAnexoIII($exercicio);
            case 4:
                return self::getDadosViewAnexoIV($exercicio);
            case 6:
                return self::getDadosViewAnexoVI($exercicio);
            case 8:
                return self::getDadosViewAnexoVIII($exercicio);
            case 12:
                return self::getDadosViewAnexoXII($exercicio);
            default:
                throw new Exception('Relatório não encontrado.');
        }
    }

    private static function getDadosViewAnexoIV($exercicio)
    {
        return AnexoQuatroFactory::getDadosView($exercicio);
    }

    private static function getDadosViewAnexoVIII($exercicio)
    {
        return AnexoOitoFactory::getDadosView($exercicio);
    }

    private static function getDadosViewAnexoIII($exercicio)
    {
        return AnexoTresFactory::getDadosView($exercicio);
    }

    private static function getDadosViewAnexoVI($exercicio)
    {
        return AnexoSeisFactory::getDadosView($exercicio);
    }

    private static function getDadosViewAnexoI($exercicio)
    {
        return AnexoUmFactory::getDadosView($exercicio);
    }

    private static function getDadosViewAnexoXII($exercicio)
    {
        return AnexoDozeFactory::getDadosView($exercicio);
    }
}
