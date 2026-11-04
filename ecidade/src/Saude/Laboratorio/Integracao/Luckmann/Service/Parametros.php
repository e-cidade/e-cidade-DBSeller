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

namespace ECidade\Saude\Laboratorio\Integracao\Luckmann\Service;

use ECidade\Saude\Laboratorio\Integracao\Luckmann\Model\Parametros as ParametrosModel;
use Exception;
use JSON;
use stdClass;

/**
 * Class Parametros
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Service
 */
class Parametros
{
    /**
     * @var object
     * Arquivo de configuracao json
     */
    private $json;

    /**
     * @var object
     * Arquivo de configuracao json para integração Infinity
     */
    private $jsonConfigInfinity;

    /**
     * @var string
     */
    private $caminhoArquivo;

    /**
     * @var string
     */
    private $caminhoArquivoInfinity;

    /**
     * Parametros constructor.
     * @param string $caminhoArquivo
     * @param string|null $caminhoArquivoInfinity
     * @throws Exception
     */
    public function __construct($caminhoArquivo, $caminhoArquivoInfinity = null)
    {
        if (empty($caminhoArquivo)) {
            throw new Exception('Caminho do arquivo de configuração não informado.');
        }

        if (!file_exists($caminhoArquivo)) {
            $this->criarArquivo($caminhoArquivo);
        }

        $this->caminhoArquivo = $caminhoArquivo;
        $this->json = JSON::create()->parse(file_get_contents($caminhoArquivo));

        if ($caminhoArquivoInfinity) {
            if (!file_exists($caminhoArquivoInfinity)) {
                $this->criarArquivo($caminhoArquivoInfinity);
            }

            $this->caminhoArquivoInfinity = $caminhoArquivoInfinity;
            $this->jsonConfigInfinity = JSON::create()->parse(file_get_contents($caminhoArquivoInfinity));
        }
    }

    /**
     * @param $caminhoArquivo
     */
    private function criarArquivo($caminhoArquivo)
    {
        $configuracao = new stdClass();
        $configuracao->pasta_arquivo_pedidos = '';
        $configuracao->pasta_arquivo_resultados = '';

        file_put_contents($caminhoArquivo, JSON::create()->stringify($configuracao));
    }

    /**
     * @return ParametrosModel
     */
    public function getParametros()
    {
        $parametrosModel = new ParametrosModel();
        $parametrosModel->setPastaPedidos($this->json->pasta_arquivo_pedidos);
        $parametrosModel->setPastaResultados($this->json->pasta_arquivo_resultados);

        if ($this->jsonConfigInfinity) {
            $parametrosModel->setPastaPedidosInfinity($this->jsonConfigInfinity->pasta_arquivo_pedidos);
            $parametrosModel->setPastaResultadosInfinity($this->jsonConfigInfinity->pasta_arquivo_resultados);
        }

        return $parametrosModel;
    }

    /**
     * @param ParametrosModel $parametrosModel
     */
    public function salvar(ParametrosModel $parametrosModel)
    {
        $this->json->pasta_arquivo_pedidos = $parametrosModel->getPastaPedidos();
        $this->json->pasta_arquivo_resultados = $parametrosModel->getPastaResultados();

        file_put_contents($this->caminhoArquivo, JSON::create()->stringify($this->json));
    }

    /**
     * @param ParametrosModel $parametrosModel
     */
    public function salvarJsonInfinity(ParametrosModel $parametrosModel)
    {
        $this->jsonConfigInfinity->pasta_arquivo_pedidos = $parametrosModel->getPastaPedidosInfinity();
        $this->jsonConfigInfinity->pasta_arquivo_resultados = $parametrosModel->getPastaResultadosInfinity();

        file_put_contents($this->caminhoArquivoInfinity, JSON::create()->stringify($this->jsonConfigInfinity));
    }
}
