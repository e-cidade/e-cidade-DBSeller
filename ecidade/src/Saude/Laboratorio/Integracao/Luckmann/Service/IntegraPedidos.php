<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2019  DBSeller Servicos de Informatica
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

namespace ECidade\Saude\Laboratorio\Integracao\Luckmann\Service;

use DBLog;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Model\Parametros as ParametrosModel;
use Exception;

/**
 * Class IntegraPedidos
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Service
 */
class IntegraPedidos extends Integra
{
    /**
     * IntegraPedidos constructor.
     * @param ParametrosModel $parametrosModel
     * @param $tipoArquivo
     * @param $arquivoMontagem
     * @throws Exception
     */
    public function __construct(ParametrosModel $parametrosModel, $tipoArquivo)
    {
        parent::__construct($parametrosModel, $tipoArquivo);
    }

    /**
     * @param $arquivo
     * @throws Exception
     */
    public function enviarArquivo($arquivo)
    {
        $retorno = $this->executarComando("cp -f --no-preserve=timestamp {$arquivo} {$this->getPastaDestino()} ");

        if (!$retorno) {
            $arquivoLog = "tmp/envio_arquivo_pedido_" . time();
            $mensagemErro = "Erro ao mover o arquivo {$arquivo} de pedidos para a ";
            $mensagemErro .= "pasta de pedidos: {$this->getPastaDestino()}";

            $oLog = new DBLog("TXT", $arquivoLog);
            $oLog->escreverLog($mensagemErro, DBLog::LOG_ERROR);

            throw new Exception('Erro ao mover o arquivo de pedidos para a pasta de destino.');
        }
    }

    /**
     * @param $arquivo
     * @throws Exception
     */
    public function salvarBackupPedidoInfinity($arquivo, $caminhoBackup)
    {

        if (!file_exists($caminhoBackup)) {
            mkdir($caminhoBackup, 0777, false);
        }

        $retorno = $this->executarComando("cp -f --no-preserve=timestamp {$arquivo} {$caminhoBackup} ");

        if (!$retorno) {
            $arquivoLog = "tmp/envio_arquivo_pedido_backup" . time();
            $mensagemErro = "Erro ao mover o arquivo {$arquivo} de pedidos para a pasta {$caminhoBackup} - Backup";

            $oLog = new DBLog("TXT", $arquivoLog);
            $oLog->escreverLog($mensagemErro, DBLog::LOG_ERROR);
        }
    }
}
