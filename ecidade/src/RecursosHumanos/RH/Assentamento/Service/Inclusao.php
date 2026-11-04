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

namespace ECidade\RecursosHumanos\RH\Assentamento\Service;

use AfastaAssenta;
use AfastaAssentaRepository;

//use Afastamento;
use Afastamento;
use AfastamentoRepository;
use Assentamento;
use AssentamentoFuncional;
use AssentamentoFuncionalRepository;
use AssentamentoRRA;
use AssentamentoSubstituicao;
use TipoAssentamento;
use BusinessException;
use DBDate;
use DBException;
use DBPessoal;
use ECidade\RecursosHumanos\RH\Assentamento\Interfaces\Service;
use ECidade\RecursosHumanos\RH\Assentamento\Model\AssentamentoAtributoDinamico;
use ECidade\RecursosHumanos\RH\Assentamento\Model\ControleMedico;
use ECidade\RecursosHumanos\RH\Assentamento\Model\ControleMedicoExame;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\AssentamentoAtributoDinamico as AssentaAtribDinamicoRepository;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\AssentamentoRRA as AssentaRRARepository;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\AssentamentoSubstituicao as AssentaSubsRepository;
use ECidade\RecursosHumanos\RH\Assentamento\Model\AtributoDinamico;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\AtributoDinamico as AtributoDinamicoRepository;
use ECidade\RecursosHumanos\RH\Assentamento\Model\AtributoDinamicoGrupo;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\AtributoDinamicoGrupo as AtributoDinamicoGrupoRepository;
use ECidade\RecursosHumanos\RH\Assentamento\Model\HoraExtraManual;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\HoraExtraManual as HoraExtraManualRepository;
use ECidade\RecursosHumanos\RH\Assentamento\Model\JustificativaPeriodo;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\JustificativaPeriodo as JustificativaRepository;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\Assentamento as AssentamentoRepository;
use ECidade\RecursosHumanos\RH\Assentamento\Repository\Efetividade;
use ECidade\RecursosHumanos\RH\PontoEletronico\Calculo\Horas\BaseHora;
use ECidade\V3\Datasource\Database;
use Exception;
use ECidade\RecursosHumanos\RH\Assentamento\AssentamentoAbonoFalta;
use InformacoesExternasTipoAssentamento;
use ParameterException;
use PeriodoAquisitivoAssentamento;
use PeriodoAquisitivoFerias;
use Ponto;
use ProporcionalizacaoPontoSalario;
use ServidorRepository;
use ECidade\RecursosHumanos\ESocial\Repository\Assentamento as AssentamentoEsocialRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

/**
 * Classe responsavel por incluir um assentamento
 * @property Integer codigoAssentamento
 */
final class Inclusao implements Service
{
    /**
     * Objeto Assentamento que sera responsavel por criar o assentamento no database
     * @var Assentamento
     */
    private $assentamento;

    /**
     * @var null
     * Objeto Assentamento Salvo apos o precessar
     */
    private $assentamentoSalvo = null;

    /**
     * @return null
     */
    public function getAssentamentoSalvo()
    {
        return $this->assentamentoSalvo;
    }

    /**
     * Codigo da instituicao
     * tem a finalidade de desvincular dependencia da instituicao da sessao
     * @var integer
     */
    private $codigoInstituicao;

    /**
     * Identifica se o assentamento e do tipo funcional (normal) ou efetividade
     * @var bool
     */
    private $isFuncional = true;

    /**
     * Utilizado para importacao de assentamento de efetividade para a vida funcional do servidor
     * @var int
     */
    private $codigoEfetividade = null;

    /**
     * Array utilizado em assentamento do tipo justificativa onde o indice representa a marcacao selecionada em tela e o
     *    valor se a marcacao foi selecionada ou nao
     * @var array
     */
    private $periodosJustificativa = array(1 => false, 2 => false, 3 => false);

    /**
     * Lista se assentamentos que geram afastamento na folha
     *  os assentamentos dessa lista sao configurados na tela de inclusao/alteracao dos tipos de assentamentos
     * @var array
     */
    private $assentamentoGeraAfastamento = array();

    /**
     * Alguns assentamentos possuem campos dinamicos exibidos na tela
     * Nesse array o indice representa o sequencial da pergunta e o valor a resposta da mesma
     * @var array
     */
    private $atributosDinamicos = array();

    /**
     * Utilizado em assentamentos de ferias onde e necessario informar o periodo aquisitivo das ferias
     * @var integer
     */
    private $periodoAquisitivo = 0;

    /**
     * Campo generico de valor
     * utilizado em assentamentos do RRA
     * @var float
     */
    private $valor;

    /**
     * Campo generico encargo
     * utilizado em assentamentos do RRA
     * @var String
     */
    private $encargo;

    /**
     * Campo generico quantidadeMes
     * utilizado em assentamentos do RRA
     * @var String
     */
    private $quantidadeMes;


    /**
     * @var bool
     */
    private $somenteRRA = true;

    /**
     * @var String
     */
    private $horaExtraManual50Diurna;

    /**
     * Codigo de preenchimento do grupo de atributos dinamicos
     * @var integer
     */
    private $codigoGrupoAtributoDinamico;

    /**
     * @param String $horaExtraManual50Diurna
     */
    public function setHoraExtraManual50Diurna($horaExtraManual50Diurna)
    {
        $this->horaExtraManual50Diurna = $horaExtraManual50Diurna;
    }

    /**
     * @param String $horaExtraManual50Noturna
     */
    public function setHoraExtraManual50Noturna($horaExtraManual50Noturna)
    {
        $this->horaExtraManual50Noturna = $horaExtraManual50Noturna;
    }

    /**
     * @param String $horaExtraManual75Diurna
     */
    public function setHoraExtraManual75Diurna($horaExtraManual75Diurna)
    {
        $this->horaExtraManual75Diurna = $horaExtraManual75Diurna;
    }

    /**
     * @param String $horaExtraManual75Noturna
     */
    public function setHoraExtraManual75Noturna($horaExtraManual75Noturna)
    {
        $this->horaExtraManual75Noturna = $horaExtraManual75Noturna;
    }

    /**
     * @param String $horaExtraManual100Diurna
     */
    public function setHoraExtraManual100Diurna($horaExtraManual100Diurna)
    {
        $this->horaExtraManual100Diurna = $horaExtraManual100Diurna;
    }

    /**
     * @param String $horaExtraManual100Noturna
     */
    public function setHoraExtraManual100Noturna($horaExtraManual100Noturna)
    {
        $this->horaExtraManual100Noturna = $horaExtraManual100Noturna;
    }

    /**
     * @var String
     */
    private $horaExtraManual50Noturna;

    /**
     * @var String
     */
    private $horaExtraManual75Diurna;

    /**
     * @var String
     */
    private $horaExtraManual75Noturna;

    /**
     * @var String
     */
    private $horaExtraManual100Diurna;

    /**
     * @var String
     */
    private $horaExtraManual100Noturna;

    /**
     * @var string
     */
    private $crmResponsavel;

    /**
     * @var string
     */
    private $nomeResponsavel;

    /**
     * @var string
     */
    private $ufCrm;

    /**
     * @var string
     */
    private $crmMedico;

    /**
     * @var int
     */
    private $resultadoAtestado;

    /**
     * @var DBDate
     */
    private $dataAtestado;

    /**
     * @var int
     */
    private $tipoExameOcupacional;

    /**
     * @var string
     */
    private $nomeMedico;

    /**
     * @var string
     */
    private $ufCrmMedico;

    /**
     * @var ControleMedicoExame[]
     */
    private $exames;

    /**
     * string
     */
    private $cpfResponsavel;

    /**
     * @return mixed
     */
    public function getCpfResponsavel()
    {
        return $this->cpfResponsavel;
    }

    /**
     * @param mixed $cpfResponsavel
     */
    public function setCpfResponsavel($cpfResponsavel)
    {
        $this->cpfResponsavel = $cpfResponsavel;
    }

    /**
     * @return string
     */
    public function getCrmResponsavel()
    {
        return $this->crmResponsavel;
    }

    /**
     * @param string $crmResponsavel
     */
    public function setCrmResponsavel($crmResponsavel)
    {
        $this->crmResponsavel = $crmResponsavel;
    }

    /**
     * @return string
     */
    public function getNomeResponsavel()
    {
        return $this->nomeResponsavel;
    }

    /**
     * @param string $nomeResponsavel
     */
    public function setNomeResponsavel($nomeResponsavel)
    {
        $this->nomeResponsavel = $nomeResponsavel;
    }

    /**
     * @param ControleMedicoExame $exame
     */
    public function adicionarExame(ControleMedicoExame $exame)
    {
        $this->exames[] = $exame;
    }

    /**
     * @return string
     */
    public function getUfCrm()
    {
        return $this->ufCrm;
    }

    /**
     * @param string $ufCrm
     */
    public function setUfCrm($ufCrm)
    {
        $this->ufCrm = $ufCrm;
    }

    /**
     * @return string
     */
    public function getCrmMedico()
    {
        return $this->crmMedico;
    }

    /**
     * @param string $crmMedico
     */
    public function setCrmMedico($crmMedico)
    {
        $this->crmMedico = $crmMedico;
    }

    /**
     * @return int
     */
    public function getResultadoAtestado()
    {
        return $this->resultadoAtestado;
    }

    /**
     * @param int $resultadoAtestado
     */
    public function setResultadoAtestado($resultadoAtestado)
    {
        $this->resultadoAtestado = $resultadoAtestado;
    }

    /**
     * @return DBDate
     */
    public function getDataAtestado()
    {
        return $this->dataAtestado;
    }

    /**
     * @param DBDate $dataAtestado
     */
    public function setDataAtestado($dataAtestado)
    {
        $this->dataAtestado = $dataAtestado;
    }

    /**
     * @return int
     */
    public function getTipoExameOcupacional()
    {
        return $this->tipoExameOcupacional;
    }

    /**
     * @param int $tipoExameOcupacional
     */
    public function setTipoExameOcupacional($tipoExameOcupacional)
    {
        $this->tipoExameOcupacional = $tipoExameOcupacional;
    }

    /**
     * @return string
     */
    public function getNomeMedico()
    {
        return $this->nomeMedico;
    }

    /**
     * @param string $nomeMedico
     */
    public function setNomeMedico($nomeMedico)
    {
        $this->nomeMedico = $nomeMedico;
    }

    /**
     * @return string
     */
    public function getUfCrmMedico()
    {
        return $this->ufCrmMedico;
    }

    /**
     * @param string $ufCrmMedico
     */
    public function setUfCrmMedico($ufCrmMedico)
    {
        $this->ufCrmMedico = $ufCrmMedico;
    }

    /**
     * @return ControleMedicoExame[]
     */
    public function getExames()
    {
        return $this->exames;
    }

    /**
     * @param ControleMedicoExame[] $exames
     */
    public function setExames($exames)
    {
        $this->exames = $exames;
    }

    /**
     * @var int|null
     */
    private $codigoControleMedico = null;

    /**
     * @return int|null
     */
    public function getCodigoControleMedico()
    {
        return $this->codigoControleMedico;
    }

    /**
     * @param int|null $codigoControleMedico
     */
    public function setCodigoControleMedico($codigoControleMedico)
    {
        $this->codigoControleMedico = $codigoControleMedico;
    }

    /**
     * Variavel obrigatorio em determinados tipos de assentamento como por exemplo abono parcial
     * @var string
     */
    private $horaInicio;

    /**
     * @var String
     */
    private $horaFim;

    private $codigoAssentamentoSalvo;

    /**
     * Inclusao constructor.
     * @throws BusinessException
     * @throws DBException
     */
    public function __construct($codigoAssentamento = null)
    {
        $assentamento = AssentamentoRepository::getInstance();
        $this->assentamento = $assentamento->getByCodigo($codigoAssentamento);
        $this->setTipoAssentamentoConfigurados();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function processar()
    {
        Database::getInstance()->begin();
        //Resetamos o assentamento salvo
        $this->assentamentoSalvo = null;
        try {
            if (!$this->validate()) {
                return false;
            }

            /**
             * Inclui assentamento/afastamento
             */
            $assentamento = new Assentamento($this->assentamento->getCodigo());
            $existeAssentamentoJustificativa = false;

            switch ($this->assentamento->getInstanciaTipoAssentamento()->getNatureza()) {
                /**
                 * Verifica se já tem assentamento de justificativa para
                 * o período, caso exista irá alterar o existente
                 */
                case Assentamento::NATUREZA_JUSTIFICATIVA:
                    if (AssentamentoRepository::verificaAssentamentoJustificativa($this->assentamento)) {
                        $assentamento = new Assentamento($this->assentamento->getCodigo());
                        $existeAssentamentoJustificativa = true;
                    }
                    break;

                case Assentamento::NATUREZA_HE_MANUAL:
                    AssentamentoRepository::verficaHoraExtraManual($this->assentamento, $this->isFuncional);
                    break;
                case Assentamento::NATUREZA_AUTORIZA_HORA_EXTRA:
                    $dataTermino = $this->assentamento->getDataTermino();
                    if (empty($dataTermino)) {
                        $this->assentamento->setDataTermino($this->assentamento->getDataConcessao());
                    }
                    $this->assentamento->setDias(1);

                    /**
                     * Verifica se já existe um assentamento de autorização para a matrícula na data informada não
                     * sendo o assentamento atual
                     */
                    AssentamentoRepository::verficaAutorizacaoHoraExtra($this->assentamento);
                    break;
                case Assentamento::NATUREZA_ABONO_FALTA:
                    $assentamento = new AssentamentoAbonoFalta($this->assentamento->getCodigo());
                    $assentamento->setHoraInicio($this->horaInicio);
                    $assentamento->setHoraFim($this->horaFim);

                    $dataTermino = $this->assentamento->getDataTermino();

                    if (empty($dataTermino)) {
                        $assentamento->setDataTermino($this->assentamento->getDataConcessao());
                    }
                    break;
                default:
                    $existeAssentamentoJustificativa = false;
                    break;
            }

            $assentamento->setCodigo(null);

            if ($existeAssentamentoJustificativa === false) {

                /**
                 * Se for utilizado a rotina Importar Assentamentos para vida funcional, irá salvar os dados do novo
                 * assentamento na tabela assentamento funcional.
                 */
                if ($this->isFuncional) {
                    $assentamento = new AssentamentoFuncional($this->assentamento->getCodigo());
                    if (!empty($this->codigoEfetividade)) {
                        $assentamento->setAssentamentoEfetividade(new Assentamento($this->codigoEfetividade));
                    }
                }
            }

            if ($this->isFuncional) {
                $assentamento = new AssentamentoFuncional($this->assentamento->getCodigo());
                if (!empty($this->codigoEfetividade)) {
                    $assentamento->setAssentamentoEfetividade(new Assentamento($this->codigoEfetividade));
                }
            }
            $assentamento->setCodigo($this->assentamento->getCodigo());

            $assentamento->setMatricula($this->assentamento->getServidor()->getMatricula());
            $assentamento->setTipoAssentamento($this->assentamento->getTipoAssentamento());
            $assentamento->setDataConcessao($this->assentamento->getDataConcessao());
            $assentamento->setHistorico($this->assentamento->getHistorico());
            $assentamento->setCodigoPortaria($this->assentamento->getCodigoPortaria());
            $assentamento->setDescricaoAto($this->assentamento->getDescricaoAto());
            $assentamento->setDias($this->assentamento->getDias());
            $assentamento->setPercentual($this->assentamento->getPercentual());
            $assentamento->setSegundoHistorico('');
            $assentamento->setLoginUsuario(db_getsession("DB_id_usuario"));
            $assentamento->setDataLancamento(date("Y-m-d", db_getsession("DB_datausu")));
            $assentamento->setConvertido("false");
            $assentamento->setAnoPortaria($this->assentamento->getAnoPortaria());
            $assentamento->setHora($this->assentamento->getHora());

            $assentamento->setDataTermino($this->assentamento->getDataTermino());

            if ($this->assentamento->getInstanciaTipoAssentamento()->getNatureza()
                != Assentamento::NATUREZA_ABONO_FALTA) {
                $assentamentoRet = $assentamento->persist();
                if (gettype($assentamentoRet) == 'string') {
                    throw new BusinessException($assentamentoRet);
                }

                if ($assentamento instanceof AssentamentoFuncional) {
                    $assentamento = AssentamentoFuncionalRepository::persist($assentamentoRet);
                } else {
                    $assentamento = $assentamentoRet;
                }

                if ($this->assentamento->getInstanciaTipoAssentamento()->getNatureza()
                    == Assentamento::NATUREZA_CONTROLE_MEDICO) {
                    $controleMedico = new ControleMedico($assentamento->getCodigo());
                    $controleMedico->setCrmResponsavel($this->getCrmResponsavel());
                    $controleMedico->setNomeResponsavel($this->getNomeResponsavel());
                    $controleMedico->setCpfResponsavel($this->getCpfResponsavel());
                    $controleMedico->setUfCrm($this->getUfCrmMedico());
                    $controleMedico->setCrmMedico($this->getCrmMedico());
                    $controleMedico->setResultadoAtestado($this->getResultadoAtestado());
                    if (!empty($this->getDataAtestado())) {
                        $controleMedico->setDataAtestado($this->getDataAtestado());
                    }
                    $controleMedico->setTipoExameOcupacional($this->getTipoExameOcupacional());
                    $controleMedico->setNomeMedico($this->getNomeMedico());
                    $controleMedico->setUfCrmResponsavel($this->getUfCrm());
                    $controleMedico->setCodigo($this->getCodigoControleMedico());
                    $controleMedico->setCodigoAssentamento($assentamento->getCodigo());

                    if (empty($this->getExames()) or sizeof($this->getExames()) == 0) {
                        throw new BusinessException("Nenhum exame informado.");
                    }
                    $controleMedico->setExames($this->exames);

                    $controleMedico->salvar();
                }
            }

            /**
             * Verifica se o assentamento está configurado para gerar afastamento.
             */
            if (sizeof($this->assentamentoGeraAfastamento) > 0) {
                if (isset($this->assentamentoGeraAfastamento[$assentamento->getTipoAssentamento()])) {
                    $this->geraAfastamento($assentamento);
                }
            }

            $this->codigoAssentamentoSalvo = $assentamento->getCodigo();
            $this->salvarCamposDinamicos($assentamento);
            $this->salvaNaturezaAssentamento($assentamento);
            $this->salvaPeriodoAquisitivo($assentamento);
        } catch (Exception $exception) {
            Database::getInstance()->rollback();
            throw new BusinessException($exception->getMessage());
        }
        $this->assentamentoSalvo = $assentamento;
        AssentamentoEsocialRepository::salvarFormulario(
            $this->assentamentoSalvo,
            new DBDate(date('Y-m-d', db_getsession('DB_datausu')))
        );
        Database::getInstance()->commit();
        return true;
    }

    /**
     * @param string|integer $tipoAssentamento
     */
    public function setTipoAssentamento($tipoAssentamento)
    {
        $this->assentamento->setTipoAssentamento($tipoAssentamento);
    }

    /**
     * @return Assentamento
     * @throws DBException
     */
    public function getAssentamento()
    {
        return $this->assentamento;
    }

    /**
     * @param array $atributosDinamicos
     */
    public function setAtributosDinamicos($atributosDinamicos)
    {
        $this->atributosDinamicos = $atributosDinamicos;
    }

    private function salvarCamposDinamicos(Assentamento $assentamento)
    {
        try {
            AssentaAtribDinamicoRepository::deleteByAssentamento($assentamento->getCodigo());
            if (!empty($this->atributosDinamicos)) {
                $atributoGrupo = new AtributoDinamicoGrupo();
                AtributoDinamicoGrupoRepository::incluir($atributoGrupo);
                foreach ($this->atributosDinamicos as $atributo => $valor) {
                    $atributoValor = new AtributoDinamico();
                    $atributoValor->setGrupo($atributoGrupo->getSequencial());
                    $atributoValor->setAtributo($atributo);
                    $atributoValor->setValor($valor);
                    AtributoDinamicoRepository::incluir($atributoValor);
                }
                $assentamentoAtributo = new AssentamentoAtributoDinamico();
                $assentamentoAtributo->setCodigoAssentamento($assentamento->getCodigo());
                $assentamentoAtributo->setCodigoGrupo($atributoGrupo->getSequencial());
                AssentaAtribDinamicoRepository::incluir($assentamentoAtributo);
            }
        } catch (Exception $e) {
        }
        return true;
    }

    /**
     * @param integer $percentual
     */
    public function setPercentual($percentual)
    {
        $this->assentamento->setPercentual($percentual);
    }

    /**
     * @param integer $codigoEfetividade
     */
    public function setCodigoEfetividade($codigoEfetividade)
    {
        $this->codigoEfetividade = $codigoEfetividade;
    }

    /**
     * Metodo com função de efetuar as validações de assentamento/afastamento.
     * @return bool
     * @throws ParameterException
     * @throws Exception
     */
    private function validate()
    {
        try {
            // Validação de valor default do percentual
            $percentual = $this->assentamento->getPercentual();

            if (empty($percentual)) {
                $this->assentamento->setPercentual(0);
            }

            $hora = $this->assentamento->getHora();

            if (empty($hora)) {
                $this->assentamento->setHora('0:00');
            }

            $dataInicial = new DBDate($this->assentamento->getDataConcessao());
            $dataFinal = null;
            $dataTermino = $this->assentamento->getDataTermino();

            if (!empty($dataTermino)) {
                $dataFinal = new DBDate($dataTermino);

                if ($dataFinal->getTimeStamp() < $dataInicial->getTimeStamp()) {
                    throw new BusinessException('A data final deve ser menor ou igual a data inicial.');
                }
            }

            $this->validaAssentamentoComBloqueio($dataInicial);


            $this->validaAssentamento();
            $this->validaTipoAssentamento();
            AssentamentoRepository::verificaAssentamento($this->assentamento);
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
        return true;
    }

    /**
     * @param string $hora
     */
    public function setHora($hora)
    {
        $this->assentamento->setHora($hora);
    }

    /**
     * @param string $data
     * @throws ParameterException
     */
    public function setDataInicial($data)
    {
        $this->assentamento->setDataConcessao(new DBDate($data));
    }

    /**
     * @param string $data
     * @throws ParameterException
     */
    public function setDataTermino($data)
    {
        $this->assentamento->setDataTermino(new DBDate($data));
    }

    /**
     * @param $matricula
     * @throws BusinessException
     */
    public function setMatricula($matricula)
    {
        $this->assentamento->setMatricula($matricula);
        $servidor = ServidorRepository::getInstanciaByCodigo(
            $matricula,
            DBPessoal::getAnoFolha(),
            DBPessoal::getMesFolha(),
            $this->codigoInstituicao
        );

        $this->assentamento->setServidor($servidor);
    }

    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    /**
     * @return bool
     * @throws DBException
     * @throws BusinessException
     */
    private function validaAssentamento()
    {
        $dataFinal = null;
        $dataFinal = $this->assentamento->getDataTermino();
        if (!empty($dataFinal)) {
            $dataFinal = $this->assentamento->getDataTermino()->getDate();
        }

        try {
            AssentamentoRepository::naoPossuiAfastamento(
                $this->assentamento->getServidor()->getMatricula(),
                $this->assentamento->getInstanciaTipoAssentamento()->getSequencial(),
                $this->assentamento->getDataConcessao()->getDate(),
                $dataFinal,
                $this->isFuncional,
                $this->periodoAquisitivo,
                $this->assentamento->getCodigo()
            );
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
        if (Efetividade::efetividadeProcessada(
            $this->codigoInstituicao,
            $this->assentamento->getDataConcessao()->getDate(),
            $dataFinal
        )) {
            $mensagem = "O período de efetividade já foi processado para o período informado no assentamento."
                . "\nPara realizar manutenções em "
                . "assentamentos nesse período, "
                . "reabra o período de efetividade em Procedimentos > Efetividade > Reabrir Período.";
            throw new BusinessException($mensagem);
        }
        return true;
    }

    /**
     * @param bool $isFuncional
     */
    public function setAssentamentoFuncional($isFuncional)
    {
        $this->isFuncional = $isFuncional;
    }

    /**
     * @param string $historico
     */
    public function setHistorico($historico)
    {
        $this->assentamento->setHistorico($historico);
    }

    /**
     * @param integer $codigoPortaria
     */
    public function setCodigoPortaria($codigoPortaria)
    {
        $this->assentamento->setCodigoPortaria($codigoPortaria);
    }

    /**
     * @param string $descricao
     */
    public function setDescricaoAto($descricao)
    {
        $this->assentamento->setDescricaoAto($descricao);
    }

    /**
     * @param integer $quantidadeDias
     */
    public function setDias($quantidadeDias)
    {
        $this->assentamento->setDias($quantidadeDias);
    }

    /**
     * @param bool $periodo
     * Função que verifica as entradas de batidas.
     */
    public function setPeriodoJustificativa($periodo)
    {
        if (isset($this->periodosJustificativa[$periodo])) {
            $this->periodosJustificativa[$periodo] = true;
        }
    }

    /**
     * @throws DBException
     */
    private function setTipoAssentamentoConfigurados()
    {
        $listaInfExternas = InformacoesExternasTipoAssentamento::getTipoAssentamentoConfiguradosPorCompetencia(
            DBPessoal::getCompetenciaFolha()
        );

        if (is_array($listaInfExternas)) {
            foreach ($listaInfExternas as $infExternas) {
                $this->assentamentoGeraAfastamento[$infExternas->getTipoAssentamento()->getSequencial()] = $infExternas;
            }
        }
    }

    /**
     * @param Assentamento $assentamento
     * @return bool
     * @throws BusinessException
     * @throws DBException
     */
    private function geraAfastamento($assentamento)
    {
        $afastaAssentaRepository = AfastaAssentaRepository::getAfastamentosPorAssentamento($assentamento);
        $codigoAfastamento = null;
        if (!empty($afastaAssentaRepository) && sizeof($afastaAssentaRepository) > 0) {
            $codigoAfastamento = $afastaAssentaRepository[0]->getCodigoAfastamento();
        }
        $servidor = $assentamento->getServidor();

        $afastamento = new Afastamento();

        $situacao = $this->assentamentoGeraAfastamento[$assentamento->getTipoAssentamento()]
            ->getSituacaoAfastamento();

        $afastamentoSefip = $this->assentamentoGeraAfastamento[$assentamento->getTipoAssentamento()]
            ->getSefip();
        $retornoSefip = $this->assentamentoGeraAfastamento[$assentamento->getTipoAssentamento()]
            ->getCodigoRetorno();
        $afastamento->setCompetencia(
            $this->assentamentoGeraAfastamento[$assentamento->getTipoAssentamento()]->getCompetencia()
        );
        $afastamento->setCodigoAfastamento($codigoAfastamento);
        $afastamento->setServidor($servidor);
        $afastamento->setDataAfastamento($assentamento->getDataConcessao());
        $afastamento->setDataRetorno($assentamento->getDataTermino());
        $afastamento->setCodigoSituacao($situacao);
        $afastamento->setDataLancamento($assentamento->getDataLancamento());
        $afastamento->setCodigoAfastamentoSefip($afastamentoSefip);
        $afastamento->setCodigoRetornoSefip($retornoSefip);
        $afastamento->setObservacao($assentamento->getHistorico());

        $afastamento = AfastamentoRepository::persist($afastamento);

        if (!$afastamento instanceof Afastamento) {
            throw new BusinessException("Erro ao salvar afastamento na base de dados.");
        }
        $afastaAssenta = new AfastaAssenta($assentamento, $afastamento);

        if (empty($codigoAfastamento)) {
            $afastaAssenta = $afastaAssenta->persist();
        }

        if (!$afastaAssenta instanceof AfastaAssenta) {
            throw new BusinessException("Erro ao salvar vínculo entre assentamento e afastamento.");
        }

        /**
         * Caso servidor seja aposentado/pensionista, devemos enviar o S2416 do eSocial
         */
        if (!$servidor->isAtivo() || $servidor->isPensionista()) {
            $dataAlteracaoESocial = $this->assentamento->getDataConcessao()->getDate();
            $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($servidor->getMatricula(), Tipo::S2416);
            $servidorAlteracao->setDataS2206(new DBDate($dataAlteracaoESocial));
            $servidorAlteracao->save();
        }

        /**
         * Realiza a proporcionalização no ponto
         */
        $proporcionalizacaoPontoSalario = new ProporcionalizacaoPontoSalario(
            $servidor->getPonto(Ponto::SALARIO),
            $this->assentamentoGeraAfastamento[$assentamento->getTipoAssentamento()]->getSituacaoAfastamento(),
            $assentamento->getDataTermino()
        );
        $proporcionalizacaoPontoSalario->processar();

        return true;
    }


    public function adicionaAtributoDinamico($atributo, $valor = "")
    {
        $this->atributosDinamicos[$atributo] = $valor;
        return true;
    }

    /**
     * @param Assentamento $assentamento
     * @throws BusinessException
     * @throws DBException
     */
    public function salvaPeriodoAquisitivo($assentamento)
    {
        if (!empty($this->periodoAquisitivo)) {
            $periodoAquisitivo = new PeriodoAquisitivoAssentamento();
            $periodoAquisitivo->setAssentamento(new Assentamento($assentamento->getCodigo()));
            $periodoAquisitivo->setPeriodoAquisitivo(
                new PeriodoAquisitivoFerias($this->periodoAquisitivo)
            );
            $periodoAquisitivo->salvar();
        }
    }

    /**
     * @param $assentamento
     * @throws BusinessException
     * @throws DBException
     * @throws Exception
     */
    public function salvaNaturezaAssentamento($assentamento)
    {
        switch ($this->assentamento->getInstanciaTipoAssentamento()->getNatureza()) {
            case AssentamentoSubstituicao::CODIGO_NATUREZA:
                $this->salvarSubstituicao($assentamento);
                break;

            case AssentamentoRRA::CODIGO_NATUREZA:
                $this->salvarRRA($assentamento);
                break;

            case Assentamento::NATUREZA_JUSTIFICATIVA:
                $this->salvarPeriodos($assentamento);
                break;

            case Assentamento::NATUREZA_ABONO_FALTA:
                $this->salvarAbonoFalta($assentamento);
                break;


            case Assentamento::NATUREZA_HE_MANUAL:
                $this->salvarHoraExtraManual($assentamento);
                break;
        }
    }

    public function setEncargo($encargo)
    {
        $this->encargo = $encargo;
    }

    public function setQuantidadeMes($quantidade)
    {
        $this->quantidadeMes = $quantidade;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function setPeriodoAquisitivo($periodoAquisitivo)
    {
        $this->periodoAquisitivo = $periodoAquisitivo;
    }

    /**
     * @param Assentamento $assentamento
     * @throws DBException
     */
    private function salvarPeriodos($assentamento)
    {
        JustificativaRepository::excluirPorAssentamento($assentamento->getCodigo());
        foreach ($this->periodosJustificativa as $key => $value) {
            if ($value) {
                $justificativa = new JustificativaPeriodo();
                $justificativa->setCodigoAssentamento($assentamento->getCodigo());
                $justificativa->setPeriodo($key);
                JustificativaRepository::incluir($justificativa);
            }
        }
    }

    /**
     * @param Assentamento $assentamento
     * @throws Exception
     */
    private function salvarHoraExtraManual(Assentamento $assentamento)
    {
        HoraExtraManualRepository::delete($assentamento->getCodigo());

        $horasExtras = array(
            BaseHora::HORAS_EXTRA50 => !empty($this->horaExtraManual50Diurna)
                ? $this->horaExtraManual50Diurna
                : null,
            BaseHora::HORAS_EXTRA50_NOTURNA => !empty($this->horaExtraManual50Noturna)
                ? $this->horaExtraManual50Noturna
                : null,
            BaseHora::HORAS_EXTRA75 => !empty($this->horaExtraManual75Diurna)
                ? $this->horaExtraManual75Diurna
                : null,
            BaseHora::HORAS_EXTRA75_NOTURNA => !empty($this->horaExtraManual75Noturna)
                ? $this->horaExtraManual75Noturna
                : null,
            BaseHora::HORAS_EXTRA100 => !empty($this->horaExtraManual100Diurna)
                ? $this->horaExtraManual100Diurna
                : null,
            BaseHora::HORAS_EXTRA100_NOTURNA => !empty($this->horaExtraManual100Noturna)
                ? $this->horaExtraManual100Noturna
                : null
        );

        foreach ($horasExtras as $tipo => $hora) {
            if (empty($hora)) {
                continue;
            }
            $horaExtra = new HoraExtraManual();
            $horaExtra->setCodigoAssentamento($assentamento->getCodigo());
            $horaExtra->setHora($hora);
            $horaExtra->setTipo($tipo);

            HoraExtraManualRepository::incluir($horaExtra);
        }
    }

    /**
     * @param AssentamentoAbonoFalta $assentamento
     * @throws Exception
     */
    private function salvarAbonoFalta($assentamento)
    {
        $assentamento->validarExistenciaAssentamento(
            $assentamento->getDataConcessao(),
            $assentamento->getDataTermino(),
            $assentamento->getMatricula()
        );

        $assentamento->retornarHorasAbonar();
        $assentamento->persist();
    }

    /**
     * @param AssentamentoRRA $assentamento
     * @throws BusinessException
     * @throws DBException
     */
    private function salvarRRA($assentamento)
    {
        $assentamentoRRA = new AssentamentoRRA();
        $assentamentoRRA->setCodigo($assentamento->getCodigo());
        $assentamentoRRA->setValorTotalDevido($this->valor);
        $assentamentoRRA->setNumeroDeMeses((int)$this->quantidadeMes);
        $assentamentoRRA->setValorDosEncargosJudiciais((float)$this->encargo);

        AssentaRRARepository::salvar($assentamentoRRA, $this->somenteRRA);
    }

    /**
     * @param Assentamento $assentamento
     * @throws BusinessException
     */
    private function salvarSubstituicao($assentamento)
    {
        $matricula = $assentamento->getServidor()->getMatricula();
        if (empty($matricula)) {
            throw new BusinessException("É necessário informar o servidor a substituir.");
        }

        $assentamentoSubstituicao = new AssentamentoSubstituicao($assentamento->getCodigo());

        $assentamentoSubstituicao->setSubstituido(
            ServidorRepository::getInstanciaByCodigo(
                $assentamento->getServidor()->getMatricula(),
                DBPessoal::getAnoFolha(),
                DBPessoal::getMesFolha()
            )
        );
        AssentaSubsRepository::salvar($assentamento, $assentamentoSubstituicao);
    }

    /**
     * @throws BusinessException
     */
    public function validaTipoAssentamento()
    {
        if ($this->assentamento->getInstanciaTipoAssentamento()->getTipoReajuste() != 0) {
            if ($this->assentamento->getServidor()->isAtivo()) {
                $mensagen = "Não é possível cadastrar assentamento de reajuste salarial para servidor ativo.";
                throw new BusinessException($mensagen);
            }

            if ($this->assentamento->getServidor()->isRescindido()) {
                $mensagen = "Não é possível cadastrar assentamento de reajuste salarial para servidor rescindido.";
                throw new BusinessException($mensagen);
            }

            AssentamentoRepository::verificaReajuste($this->assentamento->getServidor()->getMatricula());
        }
    }

    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;
    }

    public function setHoraFim($horaFim)
    {
        $this->horaFim = $horaFim;
    }

    public function getCodigoAssentamento()
    {
        return $this->codigoAssentamentoSalvo;
    }

    public function setGrupoAtributoDinamico($codigoGrupo)
    {
        $this->setAtributosDinamicos(AtributoDinamicoRepository::getByGrupo($codigoGrupo));
    }

    public function clonaAtributoDinamico($codigoGrupo)
    {
        $this->setAtributosDinamicos(AtributoDinamicoRepository::getByGrupo($codigoGrupo));
    }

    public function validaAssentamentoComBloqueio($dataInicial)
    {
        $tipoAssentamentoInfo = TipoAssentamento::getTipoAssentamentoInfo(
            $this->assentamento->getTipoAssentamento()
        );

        if (empty($tipoAssentamentoInfo)) {
            return;
        }


        $limiteAssentamentosMes = $tipoAssentamentoInfo[0]->h12_lancamentomensal;
        $limiteAssentamentosAno = $tipoAssentamentoInfo[0]->h12_lancamentoanual;
        $assentamentoComBloqueio = $tipoAssentamentoInfo[0]->h12_codigo;
        $lancamento = TipoAssentamento::contaLancamentosPorServidor(
            $dataInicial,
            $this->assentamento->getTipoAssentamento(),
            $this->assentamento->getMatricula()
        );

        // Obtém a quantidade de lançamentos por servidor
        $quantidadeAssentamentosMes = $lancamento->quantidade_mes + $this->assentamento->getDias();
        $quantidadeAssentamentosAno = $lancamento->quantidade_ano + $this->assentamento->getDias();
        $assentamentoServidor = $lancamento->h16_assent;




        if (TipoAssentamento::getExisteAssentamentosComBloqueio(
            $this->assentamento->getMatricula(),
            $this->assentamento->getDataConcessao(),
            $this->assentamento->getDataTermino()
        )) {
            throw new Exception("Já existe assentamentos com bloqueio cadastrado nesse período");
        }
        if ($quantidadeAssentamentosMes > $limiteAssentamentosMes) {
            throw new BusinessException('O servidor atingiu o limite de lançamentos mensais
                                                    permitidos para este tipo de assentamento.');
        }

        if ($quantidadeAssentamentosAno > $limiteAssentamentosAno) {
            throw new BusinessException('O servidor atingiu o limite de lançamentos anuais
                                                    permitidos para este tipo de assentamento.');
        }

        if ($quantidadeAssentamentosMes > $limiteAssentamentosMes) {
            throw new BusinessException('O servidor atingiu o limite de lançamentos mensais
                                                    permitidos para este tipo de assentamento.');
        }

        if ($quantidadeAssentamentosAno > $limiteAssentamentosAno) {
            throw new BusinessException('O servidor atingiu o limite de lançamentos anuais
                                                    permitidos para este tipo de assentamento.');
        }
    }
}
