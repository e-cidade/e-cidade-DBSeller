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

namespace ECidade\Saude\Laboratorio\Integracao\Luckmann\Importacao;

use BusinessException;
use cl_lab_importacaorequisicaoinconsistencia;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Builder\ImportacaoInconsistencia;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\IntegraResultados;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Resultado;
use ECidade\Saude\Laboratorio\Service\ImportacaoRequisicaoInconsistenciaService;
use ECidade\Saude\Laboratorio\Repository\ImportacaoRequisicaoInconsistenciaRepository;
use Exception;
use stdClass;
use JSON;

/**
 * Class ImportacaoResultado
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Importacao
 */
class ImportacaoResultado
{
    /**
     * @var IntegraResultados
     */
    private $integraResultados;

    /**
     * @var ImportacaoInconsistencia
     */
    private $importacaoInconsistencia;

    /**
     * @var JSON
     */
    private $json;

    /**
     * @var string
     */
    private $arquivo;

    /**
     * @var ImportacaoRequisicaoInconsistenciaService
     */
    private $service;

    /**
     * ImportacaoResultado constructor.
     * @param IntegraResultados $integraResultados
     * @param ImportacaoInconsistencia $importacaoInconsistencia
     * @param JSON $json
     */
    public function __construct(
        IntegraResultados $integraResultados,
        ImportacaoInconsistencia $importacaoInconsistencia,
        JSON $json
    ) {
        $this->integraResultados = $integraResultados;
        $this->importacaoInconsistencia = $importacaoInconsistencia;
        $this->json = $json;
        $this->service = new ImportacaoRequisicaoInconsistenciaService(
            new ImportacaoRequisicaoInconsistenciaRepository(new cl_lab_importacaorequisicaoinconsistencia())
        );
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getArquivosResultado()
    {
        return $this->integraResultados->receberArquivos();
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    public function executar($arquivoInfinity = false)
    {
        $arquivos = $this->getArquivosResultado();

        if ($arquivos === false) {
            return false;
        }

        foreach ($arquivos as $arquivo) {
            $resultado = new Resultado($arquivo, $this->importacaoInconsistencia, $arquivoInfinity);
            $resultado->salvar();
            if ($resultado->getArquivoComInconsistencias()) {
                $this->copiarArquivoByName($arquivo);
            }
            $this->removerArquivoByName($arquivo);
        }

        if ($this->temInconsistencias()) {
            $this->criarJsonRelatorio();
            $inconsistencias = $this->importacaoInconsistencia->getInconsistencias();
            foreach ($inconsistencias as $inconsistencia) {
                $parametros = new stdClass();
                $parametros->inconsistencias = $inconsistencias;
                $parametros->requisicao = $inconsistencia->requisicao;
                $requisicao = $inconsistencia->requisicao;
                $this->service->delete($parametros);
                $this->service->save($parametros);
            }
            return $requisicao;
        }
    }

    /**
     * @throws Exception
     */
    private function removerArquivos()
    {
        $this->integraResultados->removerArquivos();
    }

    /**
     * @throws Exception
     */
    private function removerArquivoByName($arquivo)
    {
        $this->integraResultados->removerArquivoByName($arquivo);
    }

    /**
     * @throws Exception
     */
    private function copiarArquivoByName($arquivo)
    {
        $this->integraResultados->copiarArquivoByName($arquivo);
    }


    /**
     * @return bool
     */
    public function getInconsistencias()
    {
        return $this->importacaoInconsistencia->getInconsistencias();
    }

    /**
     * @return bool
     */
    public function temInconsistencias()
    {
        return $this->importacaoInconsistencia->temInconsistencias();
    }

    private function criarJsonRelatorio()
    {
        $inconsistencias = $this->json->stringify($this->importacaoInconsistencia->getInconsistencias());
        $this->arquivo = 'tmp/importacao_inconsistencias_' . date('Ymd_His') . '.json';

        file_put_contents($this->arquivo, $inconsistencias);
    }

    /**
     * @return string
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }
}
