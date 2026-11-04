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

use AtributoValorReferenciaNumerico;
use BusinessException;
use DBLog;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Parametros as ParametrosService;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum\Parametros as ParametrosEnum;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Builder\ImportacaoInconsistencia;
use Exception;
use RequisicaoExame;
use ResultadoExameAtributo;
use RequisicaoLaboratorial;

/**
 * Class Resultado
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Service
 */
class Resultado
{
    const RESULTADO_NAO_SALVO = 1;

    /**
     * Caminho do XML a ser lido
     * @var string
     */
    private $xml;

    /**
     * @var RequisicaoLaboratorial
     */
    private $requisicaoLaboratorial;

    /**
     * @var array
     */
    private $situacoesValidas = array(
      RequisicaoExame::COLETADO,
      RequisicaoExame::LANCADO,
      RequisicaoExame::EM_PROCESSAMENTO
    );

    /**
     * @var ImportacaoInconsistencia
     */
    private $importacaoInconsistencia;

    /**
     * @var boolean
     */
    private $arquivoComInconsistencias = false;

    /**
     * Resultado constructor.
     * @param $xml
     * @param ImportacaoInconsistencia $importacaoInconsistencia
     * @throws Exception
     */
    public function __construct($xml, ImportacaoInconsistencia $importacaoInconsistencia, $arquivoInfinity = false)
    {
        if (empty($xml)) {
            throw new Exception('XML não informado.');
        }

        $parametrosService = new ParametrosService(ParametrosEnum::JSON_CONFIGURACOES);
        $pastaResultados = $parametrosService->getParametros()->getPastaResultados();

        if ($arquivoInfinity) {
            $parametrosService = new ParametrosService(
                ParametrosEnum::JSON_CONFIGURACOES,
                ParametrosEnum::JSON_CONFIGURACOES_INFINITY
            );

            $pastaResultados = $parametrosService->getParametros()->getPastaResultadosInfinity();
        }

        if (!$this->xml = simplexml_load_file($pastaResultados . '/' . $xml)) {
            $mensagemErro = "{$xml} inválido";
            $arquivoLog = "tmp/xml_resultado_invalido_" . time();
            $oLog = new DBLog("TXT", $arquivoLog);

            $oLog->escreverLog($mensagemErro, DBLog::LOG_ERROR);

            throw new Exception('XML inválido.');
        }

        $this->importacaoInconsistencia = $importacaoInconsistencia;
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    public function salvar()
    {
        $this->requisicaoLaboratorial = new RequisicaoLaboratorial($this->getCodigoRequisicao());

        if ($this->requisicaoLaboratorial->getCodigo() === null) {
            throw new Exception("Importação não concluída. Requisição {$this->getCodigoRequisicao()} não encontrada.");
        }

        $this->percorreExamesXml();
    }

    /**
     * Código da requisição = CodigoLis
     * A requisição é referente aos 10 últimos dígitos do CodigoLis
     * @return int
     */
    public function getCodigoRequisicao()
    {
        return (int)$this->xml->Paciente->CodigoLis;
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    private function percorreExamesXml()
    {
        $exames = $this->xml->Paciente->Resultado->children();
        $totalExames = count($exames);

        for ($contador = 0; $contador < $totalExames; $contador++) {
            $exameAtual = $exames[$contador];
            $siglaExame = (string)$exameAtual->CodExame;

            $requisicaoExame = $this->getRequisicaoExameBySigla($siglaExame);

            if (!empty($requisicaoExame)) {
                $atributosValores = $this->resultadosAtributosXml($exameAtual, $requisicaoExame);

                $this->atualizarResultados($requisicaoExame, $atributosValores);
            }
        }
    }

    /**
     * @param $siglaExame
     * @return RequisicaoExame|RequisicaoExame[]
     * @throws Exception
     */
    private function getRequisicaoExameBySigla($siglaExame)
    {
        $requisicoesExame = $this->requisicaoLaboratorial->getRequisicoesDeExames();
        $requisicaoExame = null;

        foreach ($requisicoesExame as $requisicao) {
            if ($requisicao->getExame()->getSigla() === $siglaExame) {
                $requisicaoExame = $requisicao;
            }
        }

        if (empty($requisicaoExame)) {
            $mensagemErro = "Requisição: {$requisicao->getCodigoRequisicao()} - ";
            $mensagemErro .= "A sigla de exame '{$siglaExame}' informada no resultado é inválida.";

            $arquivoLog = "tmp/sigla_exame_" . time();

            $oLog = new DBLog("TXT", $arquivoLog);
            $oLog->escreverLog($mensagemErro, DBLog::LOG_ERROR);

            throw new Exception($mensagemErro);
        }

        if (!$this->validaSituacaoExame($requisicaoExame)) {
            return null;
        }

        return $requisicaoExame;
    }

    /**
     * @throws Exception
     */
    private function validaSituacaoExame(RequisicaoExame $requisicaoExame)
    {
        if (!in_array($requisicaoExame->getSituacao(), $this->situacoesValidas)) {
            return false;
        }

        return true;
    }

    /**
     * @param $exameAtual
     * @return array
     */
    private function resultadosAtributosXml($exameAtual, RequisicaoExame $requisicaoExame)
    {
        $totalResultados = count($exameAtual->Secao);
        $atributosValores = array();
        $atributos = $requisicaoExame->getExame()->getAtributos();
        $tipoReferencia = [];

        foreach ($atributos as $key => $atributo) {
            $tipoReferencia[$atributo->getSigla()] = (int)$atributo->getTipoReferencia();
        }

        for ($contador = 0; $contador < $totalResultados; $contador++) {
            $secaoAtual = $exameAtual->Secao[$contador];

            $valor = (string)$secaoAtual->Valor;
            // verifica se a string contém código unicode
            if (strlen($valor) != strlen(mb_convert_encoding($valor, 'ISO-8859-1', 'UTF-8'))) {
                $valor = mb_convert_encoding($valor, 'ISO-8859-1', 'UTF-8'); // remove unicode deixando somento utf-8
            }

            /***
             * Irá fazer o replace apenas se o tipo de referência do atributo for numérico e se a sigla do mesmo
             * estiver na lista $tipoReferencia
             */
            if (array_key_exists((string)$secaoAtual->Codigo, $tipoReferencia)
                && $tipoReferencia["$secaoAtual->Codigo"] === 2) {
                $valor = str_replace(',', '.', str_replace('.', '', $valor));
            }

            $atributosValores[(string)$secaoAtual->Codigo] = $valor;
        }

        return $atributosValores;
    }

    /**
     * @param RequisicaoExame $requisicaoExame
     * @param $atributosValores
     * @throws BusinessException
     */
    private function atualizarResultados(RequisicaoExame $requisicaoExame, $atributosValores)
    {
        $atributos = $requisicaoExame->getExame()->getAtributos();
        $resultado = $requisicaoExame->getResultado();
        $resultadosAtributos = $resultado->getResultadoDosAtributos();
        $resultadosAtributosAtualizar = array();

        foreach ($resultadosAtributos as $resultadoAtributo) {
            if (array_key_exists($resultadoAtributo->getAtributo()->getSigla(), $atributosValores)) {
                $resultadosAtributosAtualizar[$resultadoAtributo->getAtributo()->getSigla()] = $resultadoAtributo;
            }
        }
        $this->setArquivoComInconsistencias(false);
        foreach ($atributos as $atributoExame) {
            $atributoValorReferencia = $atributoExame->getValoresDeReferenciaParaExame($requisicaoExame);
            $siglaExame = $atributoExame->getSigla();
            $valor = isset($atributosValores[$siglaExame]) ? $atributosValores[$siglaExame] : '';

            if ($atributoExame->getTipo() == 2) {
                if ((str_replace('.', '', $valor) == "" ||
                str_replace(',', '', $valor) == "" ||
                str_replace('"', '', $valor) == "") && $atributoExame->preenchimentoObrigatorio()) {
                    $this->importacaoInconsistencia->addInconsistencia(
                        $this->requisicaoLaboratorial,
                        $requisicaoExame,
                        $atributoExame
                    );
                    $this->setArquivoComInconsistencias(true);

                    continue;
                }
            }

            $resultadoExameAtributo = new ResultadoExameAtributo();
            $resultadoExameAtributo->setAtributo($atributoExame);

            if (array_key_exists($siglaExame, $resultadosAtributosAtualizar)) {
                $resultadoExameAtributo = $resultadosAtributosAtualizar[$siglaExame];
            }

            $resultadoExameAtributo->setValorAbsoluto($valor);

            if ($atributoValorReferencia instanceof AtributoValorReferenciaNumerico
              && $atributoValorReferencia->getTipoCalculo() == 2) {
                $resultadoExameAtributo->setValorPercentual($valor);
                $resultadoExameAtributo->setValorAbsoluto('');
            }

            if ($atributoValorReferencia !== null && $atributoValorReferencia->getCodigo() !== null) {
                $resultadoExameAtributo->setFaixaUtilizada(
                    new AtributoValorReferenciaNumerico($atributoValorReferencia->getCodigo())
                );
            }

            if (!empty($resultadoExameAtributo->getAtributo()->getOutrasInformacoes()) &&
            !empty($this->getValorInformacao($resultadoExameAtributo, $requisicaoExame))) {
                $resultadoExameAtributo->setValorAbsoluto(
                    $this->getValorInformacao($resultadoExameAtributo, $requisicaoExame)
                );
            }

            $resultado->adicionarResultadoParaAtributo($resultadoExameAtributo, true);
        }

        $resultado->salvar();

        $requisicaoExame->setSituacao(RequisicaoExame::LANCADO);
        $requisicaoExame->salvar();
    }

    /**
     * @return array
     */
    public function getInconsistencias()
    {
        return $this->importacaoInconsistencia->getInconsistencias();
    }

    /**
     * @return array
     */
    public function getArquivoComInconsistencias()
    {
        return $this->importacaoInconsistenciaArquivo;
    }

    public function setArquivoComInconsistencias($inconsistenciasArquivo)
    {
        $this->importacaoInconsistenciaArquivo = $inconsistenciasArquivo;
    }

    /**
     * @param ResultadoExameAtributo $resultadoExameAtributo
     * @param  RequisicaoExame  $requisicaoExame
     * @return int|float
     */
    public function getValorInformacao(
        ResultadoExameAtributo $resultadoExameAtributo,
        RequisicaoExame $requisicaoExame
    ) {
        $value = 0;

        switch ($resultadoExameAtributo->getAtributo()->getOutrasInformacoes()) {
            case 'la22_peso':
                $value = $requisicaoExame->getPeso();
                break;

            case 'la22_altura':
                $value = $requisicaoExame->getAltura();
                break;

            case 'la22_volumeamostra':
                $value = $requisicaoExame->getVolumeAmostra();
                break;
        }

        return $value;
    }
}
