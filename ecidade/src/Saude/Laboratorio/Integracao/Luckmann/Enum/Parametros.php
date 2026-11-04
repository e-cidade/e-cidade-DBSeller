<?php
/**
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

namespace ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum;

/**
 * Class Parametros
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum
 */
class Parametros
{
    const PEDIDOS = 1;
    const RESULTADOS = 2;
    const PEDIDOS_INIFINITY = 3;
    const RESULTADOS_INIFINITY = 4;

    const JSON_PEDIDOS = 'src/Saude/Laboratorio/Integracao/Luckmann/Resource/pedidos.json';
    const JSON_RESULTADOS = 'src/Saude/Laboratorio/Integracao/Luckmann/Resource/resultado.json';

    const JSON_CONFIGURACOES = 'config/saude/laboratorio/integracao/luckmann/config.json';
    const JSON_CONFIGURACOES_INFINITY = 'config/saude/laboratorio/integracao/infinity/configInfinity.json';

    const CAMINHO_BACKUP_PEDIDO_INFINITY = 'backupPedidosInfinity';
}
