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

use ECidade\Saude\Laboratorio\Integracao\Luckmann\Model\Parametros as ParametrosModel;
use Exception;

/**
 * Class IntegraResultados
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Service
 */
class IntegraResultados extends Integra
{
    /**
     * IntegraResultados constructor.
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
     * @return mixed
     * @throws Exception
     */
    public function receberArquivos()
    {
        exec("ls {$this->getPastaDestino()}", $saida, $return);

        if ($return !== 0) {
            throw new Exception('Não foi possível buscar os resultados.');
        }

        if (empty($saida)) {
            return false;
        }

        return $saida;
    }

    /**
     * @throws Exception
     */
    public function removerArquivos()
    {
        $retorno = $this->executarComando("rm {$this->getPastaDestino()}/*.xml");

        if (!$retorno) {
            throw new Exception('Erro ao remover os arquivos de resultados.');
        }
    }

    /**
     * @throws Exception
     */
    public function removerArquivoByName($arquivo)
    {
        $retorno = $this->executarComando("rm {$this->getPastaDestino()}/$arquivo");

        if (!$retorno) {
            throw new Exception('Erro ao remover o arquivo ' . $arquivo . ' de resultado.');
        }
    }

    /**
     * @throws Exception
     */
    public function copiarArquivoByName($arquivo)
    {
        $retorno = $this->executarComando("cp {$this->getPastaDestino()}/$arquivo tmp/");

        if (!$retorno) {
            throw new Exception('Erro ao copiar o arquivo ' . $arquivo . ' de resultado para a pasta temporaria.');
        }
    }
}
