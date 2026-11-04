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

use ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum\Parametros as ParametrosEnum;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Model\Parametros as ParametrosModel;
use Exception;

/**
 * Class Integra
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Service
 */
class Integra
{
    /**
     * @var ParametrosModel
     */
    private $parametros;

    /**
     * @var int
     * 1 - ParametrosEnum::PEDIDOS
     * 2 - ParametrosEnum::RESULTADOS
     */
    private $tipoArquivo;

    /**
     * @var string
     */
    private $pastaDestino;

    /**
     * Integra constructor.
     * @param ParametrosModel $parametrosModel
     * @param $tipoArquivo
     * @param $arquivoMontagem
     * @throws Exception
     */
    public function __construct(ParametrosModel $parametrosModel, $tipoArquivo)
    {
        $this->parametros = $parametrosModel;
        $this->tipoArquivo = $tipoArquivo;
        $this->pastaDestino = $this->getPastaArquivos();
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getPastaArquivos()
    {
        switch ($this->tipoArquivo) {
            case ParametrosEnum::PEDIDOS:
                $pasta = $this->parametros->getPastaPedidos();
                break;

            case ParametrosEnum::RESULTADOS:
                $pasta = $this->parametros->getPastaResultados();
                break;

            case ParametrosEnum::PEDIDOS_INIFINITY:
                $pasta = $this->parametros->getPastaPedidosInfinity();
                break;

            case ParametrosEnum::RESULTADOS_INIFINITY:
                $pasta = $this->parametros->getPastaResultadosInfinity();
                break;

            default:
                throw new Exception('Tipo de integração não encontrada.');
        }

        return trim($pasta);
    }

    /**
     * @throws Exception
     */
    protected function acessar()
    {
        if (!$this->existeArquivoMontagem()) {
            throw new Exception('Não foi possível acessar a pasta de destino.');
        }
    }

    // /**
    //  * @return bool
    //  */
    // private function existePastaDestino()
    // {
    //     return !empty($this->pastaDestino) && $this->executarComando("ls {$this->pastaDestino}");
    // }

    /**
     * @param string $comando
     * @return bool
     */
    protected function executarComando($comando)
    {
        exec($comando, $saida, $return);

        if ($return !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function existeArquivoMontagem()
    {
        $retorno = exec('mount | grep -q "integracao_externa/luckman/saida" && echo "1" || echo "0"');

        if ($retorno == "0") {
            throw new Exception('Falha de comunicação com o interfaceamento.');
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getPastaDestino()
    {
        return $this->pastaDestino;
    }
}
