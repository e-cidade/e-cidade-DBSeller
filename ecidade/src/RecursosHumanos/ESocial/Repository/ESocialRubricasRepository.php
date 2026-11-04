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

namespace ECidade\RecursosHumanos\ESocial\Repository;

use cl_esocialrubricas;
use Exception;
use Instituicao;
use Rubrica;
use stdClass;
use InstituicaoRepository;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\V3\Extension\Registry;
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

/**
 * Class ESocialRubricasRepository
 * @package ECidade\RecursosHumanos\ESocial\Repository
 */
class ESocialRubricasRepository
{
    /**
     * @var cl_esocialrubricas
     */
    private $dao;

    /**
     * ESocialRubricasRepository constructor.
     */
    public function __construct()
    {
        $this->dao = new cl_esocialrubricas();
    }

    /**
     * @param Rubrica $rubrica
     * @param Instituicao $instituicao
     * @return stdClass
     * @throws Exception
     */
    public function getByRubricaAndInstituicao(Rubrica $rubrica, Instituicao $instituicao)
    {
        $where = array(
          "eso26_rubrica = '{$rubrica->getCodigo()}'",
          "eso26_instituicao = {$instituicao->getCodigo()}"
        );

        $sql = $this->dao->sql_query_file(null, '*', null, implode(' AND ', $where));
        $rs = db_query($sql);

        if (!$rs) {
            $mensagem = pg_last_error();
            $mensagem ="Não foi possível buscar as informações da rubrica {$rubrica->getCodigo()}."
                . " Contate o suporte. \n{$mensagem}";
            throw new Exception($mensagem);
        }

        $retorno = new stdClass();
        $retorno->rubrica = $rubrica->getCodigo();
        $retorno->instituicao = $instituicao->getCodigo();

        if (pg_num_rows($rs) === 0) {
            $retorno->sequencial = null;
            $retorno->natureza = null;
            $retorno->dataInicial = null;
            $retorno->dataFinal = null;
            $retorno->subgrupotce = null;

            $retorno->incCPRP = null;
            $retorno->incIRRF = null;
            $retorno->incFGTS = null;
            $retorno->incCPRP = null;
            $retorno->incPisPasep = null;
            $retorno->tetoremun = null;

            $retorno->tpProc = null;
            $retorno->nrProcProcessoCP = null;
            $retorno->extDecisao = null;
            $retorno->codSuspProcessoCP = null;
            $retorno->nrProcIRRF = null;
            $retorno->codSuspIRRF = null;
            $retorno->nrProcFGTS = null;
            $retorno->nrProcPisPasep = null;
            $retorno->codSuspPisPasep = null;

            return $retorno;
        }

        $resultado = pg_fetch_object($rs);

        $retorno->sequencial = $resultado->eso26_sequencial;
        $retorno->natureza = $resultado->eso26_natureza;
        $retorno->dataInicial = $resultado->eso26_datainicial;
        $retorno->dataFinal = $resultado->eso26_datafinal;
        $retorno->subgrupotce = $resultado->eso26_subgrupotce;

        $retorno->incCP     = $resultado->eso26_codinccp;
        $retorno->incIRRF   = $resultado->eso26_codincirrf;
        $retorno->incFGTS   = $resultado->eso26_codincfgts;
        $retorno->incCPRP   = $resultado->eso26_codinccprp;
        $retorno->incPisPasep = $resultado->eso26_codincpispasep;
        $retorno->tetoRemun = $resultado->eso26_tetoremun;

        $retorno->tpProc = $resultado->eso26_tpproc;
        $retorno->nrProcProcessoCP = $resultado->eso26_nrprocprocessocp;
        $retorno->extDecisao = $resultado->eso26_extdecisao;
        $retorno->codSuspProcessoCP = $resultado->eso26_codsuspprocessocp;
        $retorno->nrProcIRRF = $resultado->eso26_nrprocirrf;
        $retorno->codSuspIRRF = $resultado->eso26_codsuspirrf;
        $retorno->nrProcFGTS = $resultado->eso26_nrprocfgts;
        $retorno->nrProcPisPasep = $resultado->eso26_nrprocpispasep;
        $retorno->codSuspPisPasep = $resultado->eso26_codsusppispasep;


        return $retorno;
    }

    /**
     * @param stdClass $rubrica
     * @return stdClass
     * @throws Exception
     */
    public function persist(stdClass $rubrica)
    {
        $this->validate($rubrica);
        if (empty($rubrica->incPisPasep)) {
            $rubrica->incPisPasep = $this->validaTributacaoPisPasep($rubrica->cgm);
        }

        $this->dao->eso26_sequencial = $rubrica->sequencial;
        $this->dao->eso26_rubrica = $rubrica->rubrica;
        $this->dao->eso26_instituicao = $rubrica->instituicao;
        $this->dao->eso26_natureza = $rubrica->natureza;
        $this->dao->eso26_datainicial = $rubrica->dataInicial;
        $this->dao->eso26_datafinal = $rubrica->dataFinal;
        $this->dao->eso26_subgrupotce = $rubrica->subgrupotce;
        $this->dao->eso26_codinccp   = $rubrica->incCP;
        $this->dao->eso26_codincirrf = $rubrica->incIRRF;
        $this->dao->eso26_codincfgts = $rubrica->incFGTS;
        $this->dao->eso26_codinccprp = $rubrica->incCPRP;
        $this->dao->eso26_codincpispasep = $rubrica->incPisPasep;
        $this->dao->eso26_tetoremun  = $rubrica->tetoRemun;
        $this->dao->eso26_tpproc = $rubrica->tpProc;
        $this->dao->eso26_nrprocprocessocp = $rubrica->nrProcProcessoCP;
        $this->dao->eso26_extdecisao = $rubrica->extDecisao;
        $this->dao->eso26_codsuspprocessocp = $rubrica->codSuspProcessoCP;
        $this->dao->eso26_nrprocirrf = $rubrica->nrProcIRRF;
        $this->dao->eso26_codsuspirrf = $rubrica->codSuspIRRF;
        $this->dao->eso26_nrprocfgts = $rubrica->nrProcFGTS;
        $this->dao->eso26_nrprocpispasep = $rubrica->nrProcPisPasep;
        $this->dao->eso26_codsusppispasep = $rubrica->codSuspPisPasep;

        if (empty($this->dao->eso26_sequencial)) {
            $this->dao->incluir($this->dao->eso26_sequencial);
        } else {
            $this->dao->alterar($this->dao->eso26_sequencial);
        }

        if ($this->dao->erro_status === '0') {
            throw new Exception("Não foi possível salvar as informações da rubrica. Contate o suporte.");
        }

        return $this->getByRubricaAndInstituicao(
            new Rubrica($rubrica->rubrica),
            new Instituicao($rubrica->instituicao)
        );
    }

    /**
     * @param Rubrica $rubrica
     * @param Instituicao $instituicao
     * @throws Exception
     */
    public function delete(Rubrica $rubrica, Instituicao $instituicao)
    {
        $where = array(
          "eso26_rubrica = '{$rubrica->getCodigo()}'",
          "eso26_instituicao = {$instituicao->getCodigo()}"
        );

        $this->dao->excluir(null, implode(' AND ', $where));

        if ($this->dao->erro_status === '0') {
            throw new Exception("Não foi possível excluir as informações da rubrica. Contate o suporte.");
        }
    }

    /**
     * @param stdClass $rubrica
     * @throws Exception
     */
    private function validate(stdClass $rubrica)
    {
        $isPB = isParaiba();

        if (!$isPB) {
            if (empty($rubrica->incCP) && !isset($rubrica->incCP)) {
                throw new Exception('Incidência de Contrib. Previdenciária não informada.');
            }

            if (empty($rubrica->incIRRF) && !isset($rubrica->incIRRF)) {
                throw new Exception("Incidência de IRRF não informada.");
            }

            if (empty($rubrica->incFGTS) && !isset($rubrica->incFGTS)) {
                throw new Exception("Incidência de FGTS não informada.");
            }

            if (empty($rubrica->natureza)) {
                throw new Exception("Natureza da rubrica não informada.");
            }
        }

        if (empty($rubrica->rubrica)) {
            throw new Exception("Código da rubrica não informado.");
        }

        if (empty($rubrica->instituicao)) {
            throw new Exception("Código da instituição não informado.");
        }

        if (empty($rubrica->dataInicial)) {
            throw new Exception("Data de início de validade não informada.");
        }
    }

    /**
     * Define quais rubricas são válidas para o layout, de acordo com o que foi respondido no formulário S-1010
     * @param string $layout (Ex.: 2299)
     * @return array
     */
    public function validarRubricas($layout = null)
    {
        $body = new stdClass();
        $body->inscricaoEmpregador = InstituicaoRepository::getInstituicaoSessao()->getCNPJ();
        $body->idEvento = $layout;
        $service = new ESocial(Registry::get('app.config'), '/evento/consultar_rubricas_desconto_irrf');
        $service->setDados($body);
        $dadosRubrica = $service->request('GET');

        $rubricasValidas = array();
        foreach ($dadosRubrica as $dadoRubrica) {
            if (!isset($rubricasValidas[$dadoRubrica->referencia])) {
                $rubricasValidas[$dadoRubrica->referencia] = $dadoRubrica;
            }
        }
        return $rubricasValidas;
    }


    public function rubricaUnica($params)
    {

        $sql = "select eso26_sequencial from esocial.esocialrubricas where eso26_rubrica
            = '{$params->rubrica}' and eso26_instituicao = {$params->instituicao}";

        $result = db_query($sql);

        if (pg_num_rows($result)!=0) {
            throw new Exception("Rubrica já cadastrada no sistema, operação não permitida.");
        }

        return true;
    }

    private function validaTributacaoPisPasep($cgmEmpregador)
    {
        $dadosESocial = new DadosESocial();
        $dadosESocial->setCgmEmpregador($cgmEmpregador);
        $dadosEmpregador = $dadosESocial->getPorTipo(Tipo::EMPREGADOR);

        $valor = '';
        foreach ($dadosEmpregador as $dadoEmpregador) {
            if ($dadoEmpregador->responsavel == $cgmEmpregador) {
                if ($dadoEmpregador
                    ->respostas['infoCadastro']
                    ->perguntas['indTribFolhaPisPasep']
                    ->resposta->resposta == 'S') {
                    $valor = '00';
                }
                break;
            }
        }
        return $valor;
    }
}
