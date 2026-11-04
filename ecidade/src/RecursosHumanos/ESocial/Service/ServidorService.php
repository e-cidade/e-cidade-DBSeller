<?php

/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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

namespace ECidade\RecursosHumanos\ESocial\Service;

use cl_curric;
use db_utils;
use ECidade\RecursosHumanos\Pessoal\Repository\DependenteRepository;
use ECidade\RecursosHumanos\ESocial\Entity\Servidor as ServidorEntity;
use ECidade\RecursosHumanos\Pessoal\Model\Deficiente;
use ECidade\RecursosHumanos\Pessoal\Model\ContratoEmergencial;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorMovimentacao;
use AdmissaoDado;
use Admissao;
use DBDate;
use ECidade\RecursosHumanos\RH\PontoEletronico\Contrato\Model\ContratoJornada;
use Servidor as ServidorModel;
use stdClass;
use endereco;
use Imigrante;
use DBPessoal;
use Exception;
use cl_rhestagiovinculo;
use Cedencia;
use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\Pessoal\Repository\MenorAprendizRepository;

/**
 * Classe responsável por organizar os dados do formulário de conferência S-2200
 * Class ServidorService
 * @package ECidade\RecursosHumanos\ESocial\Service
 */
class ServidorService
{
    /**
     * @var ServidorEntity
     */
    private $servidorEntity;

    /**
     * @var ServidorModel
     */
    private $servidor;

    private $alteracao = false;

    /**
     * Array que faz o dePara entre os códigos do e-cidade e do eSocial para Raça/Cor
     * Chave: código e-cidade
     * Valor: código eSocial
     *
     * @var array
     */
    private $deParaRacaCor = [
        1 => 5,
        2 => 1,
        6 => 4,
        8 => 3,
        9 => '',
        4 => 2,
        11 => '',
    ];

    /**
     * Array que faz o dePara entre os códigos do e-cidade e do eSocial para Estado Civil
     * Chave: código e-cidade
     * Valor: código eSocial
     *
     * @var array
     */
    private $deParaEstadoCivil = [
        1 => 1,
        2 => 2,
        5 => 3,
        4 => 4,
        3 => 5,
        6 => 1,
        8 => null
    ];

    /**
     * Array que faz o dePara entre os códigos do e-cidade e do eSocial para Grau de Instrução
     * Chave: código e-cidade
     * Valor: código eSocial
     *
     * @var array
     */
    private $deParaGrauInstrucao = [
        0 => "",
        10 => '12',
        11 => '10',
        12 => '11'
    ];

    /**
     * Array que faz o dePara entre os códigos do e-cidade e do eSocial para Regime de Trabalho
     * Chave: código e-cidade
     * Valor: código eSocial
     *
     * @var array
     */
    private $deParaRegimeTrabalho = [
        1 => 2,
        2 => 1,
        3 => 2
    ];

    /**
     * Array que faz o dePara entre os códigos do e-cidade e do eSocial para tipo de dependente
     * Chave: código e-cidade
     * Valor: código eSocial
     *
     * @var array
     */
    private $deParaTipoDependente = [
        'C' => '01',
        'F' => '03',
        'P' => '09',
        'M' => '09',
        'A' => '09',
        'O' => '99'
    ];


    /**
     * @var stdClass
     */
    private $dadosServidor;

    /**
     * @var ServidorMovimentacao
     */
    private $servidorMovimentacao;

    /**
     * Array que faz o dePara entre os códigos do e-cidade e do eSocial para Unidade de Pagamento
     * Chave: código e-cidade
     * Valor: código eSocial
     *
     * @var array
     * 1 - Por hora
     * 2 - Por dia
     * 3 - Por semana
     * 4 - Por quinzena
     * 5 - Por mês
     * 6 - Por tarefa
     * 7 - Não aplicável - Salário exclusivamente variáve
     */
    private $deParaUnidadePagamento = [
        'H' => 1,
        'D' => 2,
        'S' => 3,
        'Q' => 4,
        'M' => 5,
        '0' => 5,
        '1' => 5
    ];

    /**
     * Recebe a instância do objeto Admissao populado pela matricula do contratado como referencia
     */
    private $admissaoMatricula;

    /**
     * Recebe a instância do objeto endereco
     */
    private $endereco;

    /**
     * ServidorService constructor.
     * @param ServidorModel $servidor
     * @param ServidorMovimentacao $servidorMovimentacao
     * @param $dadosServidor
     * @throws \Exception
     */
    public function __construct(
        ServidorModel $servidor,
        ServidorMovimentacao $servidorMovimentacao,
        $dadosServidor,
        $alteracao = false
    ) {
        $this->alteracao = $alteracao;
        $this->servidor = $servidor;
        $this->servidorMovimentacao = $servidorMovimentacao;
        $this->servidorEntity = new ServidorEntity();
        $this->dadosServidor = $dadosServidor;
        $this->admissaoMatricula = new Admissao($this->servidor->getMatricula());
        $this->endereco = new endereco($this->servidor->getCgm()->getEnderecoPrimario());
    }

    /**
     * @return ServidorEntity
     * @throws \BusinessException
     * @throws \DBException
     */
    public function buscarDadosServidor()
    {
        $this->dadosTrabalhador();
        $this->documentos();
        $this->dadosEndereco();
        $this->dadosImigrantes();
        $this->dadosDeficiente();
        $this->dadosContado();
        $this->dadosDependentes();
        $this->dadosVinculoTrabalho();
        $this->dadosSucessao();
        $this->celetista();
        $this->dadosMudancaCPF();
        $this->dadosAfastamento();
        $this->dadosDesligamento();
        $this->estatutario();
        $this->remuneracao();
        $this->duracaoContrato();
        $this->localTrabalhoContrato();
        $this->dadosCessao();
        $this->dadosHoraContratual();
        $this->dadosContratoTrabalho();
        $this->dadosEstagiario();
        $this->dadosCedencia();
        $this->dadosCargoFuncaoSemViculo();
        $this->dadosAlteracaoContratualSemVinculo();
        return $this->servidorEntity;
    }

    /**
     * Organiza os dados do grupo 'trabalhador'
     */
    private function dadosTrabalhador()
    {
        $this->dadosServidor->trabalhador = [];

        $dadosTrabalhador = [];

        if ($this->servidor->getCgm() instanceof \CgmFisico) {
            $dadosTrabalhador['cpfTrab'] = $this->servidor->getCgm()->getCpf();
            $dadosTrabalhador['nmTrab'] = $this->servidor->getCgm()->getNomeCompleto();
            $dadosTrabalhador['sexo'] = $this->servidor->getSexo();
            $dadosTrabalhador['racaCor'] = $this->deParaRacaCor[$this->servidor->getRacaCor()];

            /**
             * Validamos se foi preenchida uma opcao do cadastro do servidor valida
             *  caso a opcao seja invalida, removemos o campo para forcar erro de preenchimento no envio
             *  assim o usuario irá efetuar o devido preenchimento
             * Essa regra foi implementada devida a atualizacao da versao S1.2
             */
            if (empty($dadosTrabalhador['racaCor'])) {
                unset($dadosTrabalhador['racaCor']);
            }

            if (isset($this->dadosServidor->trabalhador['estCiv'])) {
                $dadosTrabalhador['estCiv'] = $this->dadosServidor->trabalhador['estCiv'];
            }

            if (array_key_exists($this->servidor->getCgm()->getEstadoCivil(), $this->deParaEstadoCivil)) {
                $dadosTrabalhador['estCiv'] = $this->deParaEstadoCivil[$this->servidor->getCgm()->getEstadoCivil()];
            }

            $dadosTrabalhador['grauInstr'] = str_pad($this->servidor->getGrauInstrucao(), 2, '0', STR_PAD_LEFT);

            if (array_key_exists($this->servidor->getGrauInstrucao(), $this->deParaGrauInstrucao)) {
                foreach ($this->deParaGrauInstrucao as $key => $value) {
                    if ($this->servidor->getGrauInstrucao() == $value) {
                        $dadosTrabalhador['grauInstr'] = strval($key);
                    }
                }
            }

            $dadosTrabalhador['nmSoc'] = $this->servidor->getCgm()->getNomeSocial();
            $dadosTrabalhador['nascimento'] = [];
            try {
                $dadosTrabalhador['nascimento']['dtNascto'] = $this->servidor->getDataNascimento()->getDate();
            } catch (\ParameterException $e) {
                $sMsgErro = $this->getMensagemValidaData('data nascimento');
                throw new \ParameterException($sMsgErro);
            }
            $dadosTrabalhador['nascimento']['dtNascto'] = $this->servidor->getDataNascimento()->getDate();
            $dadosTrabalhador['nascimento']['paisNascto'] = $this->servidor->getCgm()->getCodigoPaisNascimento();
            $dadosTrabalhador['nascimento']['paisNac'] = $this->servidor->getCgm()->getCodigoPaisNacionalidade();

            if ($this->servidor->getCgm()->getNacionalidade() == '1' ||
            empty($this->servidor->getCgm()->getNacionalidade())
            ) {
                $dadosTrabalhador['nascimento']['paisNascto'] = '105';
                $dadosTrabalhador['nascimento']['paisNac'] = '105';
            }
        }
        $this->servidorEntity->setDadosTrabalhador($dadosTrabalhador);
    }


    /**
     * Responsável por chamar os métodos do grupo de documentos
     * @throws \DBException
     */
    private function documentos()
    {
        $this->documentosCtps();

        if (!empty($this->dadosServidor->trabalhador['documentos'])) {
            foreach ($this->dadosServidor->trabalhador['documentos'] as $indice => $documento) {
                if ($indice === ServidorEntity::DOCUMENTOS_CTPS) {
                    continue;
                }

                $this->servidorEntity->setDocumentos($documento, $indice);
            }
        }
    }

    /**
     * Organiza os dados do grupo 'trabalhador->documentos'
     * @throws \DBException
     */
    private function documentosCtps()
    {
        if ($this->servidor->getDocumentos()->iNumeroCTPS !== '') {
            $documentos = array();
            $documentos['nrCtps'] = $this->servidor->getDocumentos()->iNumeroCTPS;
            $documentos['serieCtps'] = $this->servidor->getDocumentos()->iSerieCTPS;
            $documentos['ufCtps'] = $this->servidor->getDocumentos()->sUfCTPS;

            $this->servidorEntity->setDocumentos($documentos, ServidorEntity::DOCUMENTOS_CTPS);
        }
    }

    /**
     * Organiza os dados do grupo 'trabalhador->endereco'
     * @throws \DBException
     */
    private function dadosEndereco()
    {
        $this->dadosServidor->trabalhador['endereco'] = [];
        $this->dadosServidor->trabalhador['endereco']['brasil'] = [];

        $dadosEndereco = [];
        if ($this->servidor->getCgm() instanceof \CgmFisico) {
            /**
             *  Alterando regra pois os dados do getCgm as vezes nao estao batendo com o endereco primario, que é
             *  o que é exibido na alteração do cgm
             */
            $dadosEndereco['brasil']['dscLograd'] = $this->endereco->getDescricaoRua();
            $dadosEndereco['brasil']['nrLograd'] = $this->endereco->getNumeroLocal();
            $dadosEndereco['brasil']['bairro'] = $this->endereco->getDescricaoBairro();
            $dadosEndereco['brasil']['cep'] = $this->endereco->getCepEndereco();
            $dadosEndereco['brasil']['uf'] = $this->endereco->getSiglaEstado();
            $dadosEndereco['brasil']['tpLograd'] = $this->endereco->getSiglaRua();
            $codigoMunicipio = $this->endereco->getCodigoSistemaExterno();

            if (empty($dadosEndereco['brasil']['nrLograd'])) {
                $dadosEndereco['brasil']['nrLograd'] = 'S/N';
            }
            if ($this->servidor->getCgm()->getComplemento() !== '') {
                $dadosEndereco['brasil']['complemento'] = substr($this->servidor->getCgm()->getComplemento(), 0, 30);
            }

            // caso nao tenha o codigo ibge, buscamos pelo cep do endereco primario
            if (empty($codigoMunicipio)) {
                $matricula = $this->servidor->getMatricula();
                $enderecoCep = $this->endereco->getCepEndereco();
                $codigoMunicipio = endereco::getCodigoExternoSistemaByCep($enderecoCep, $matricula);
            }

            $dadosEndereco['brasil']['codMunic'] =  $codigoMunicipio;
            $this->dadosServidor->trabalhador['endereco']['exterior'] = [];

            $dadosEndereco['exterior']['paisResid'] = $this->servidor->getCgm()->getCodigoPaisExterior();
            $dadosEndereco['exterior']['dscLograd'] = $this->servidor->getCgm()->getLogradouroExterior();
            $dadosEndereco['exterior']['nrLograd'] = $this->servidor->getCgm()->getNumeroExterior();
            $dadosComplementoExterior = $this->servidor->getCgm()->getComplementoExterior();
            $dadosEndereco['exterior']['complemento'] = substr($dadosComplementoExterior, 0, 30);
            $dadosEndereco['exterior']['bairro'] = $this->servidor->getCgm()->getBairroExterior();
            $dadosEndereco['exterior']['nmCid'] = $this->servidor->getCgm()->getCidadeExterior();
            $dadosEndereco['exterior']['codPostal'] = $this->servidor->getCgm()->getCodigoPostalExterior();
        }
        $this->servidorEntity->setEndereco($dadosEndereco);
    }

    /**
     * Organiza os dados do grupo 'vinculo' (Grupo de informações do vínculo)
     * @throws \DBException
     */
    private function dadosVinculoTrabalho()
    {
        $this->dadosServidor->vinculo = [];

        $dadosVinculoTrabalho = [];
        $dadosVinculoTrabalho['matricula'] = $this->servidor->getMatricula();
        $dadosVinculoTrabalho['tpRegTrab'] = $this->deParaRegimeTrabalho[$this->servidor->getTipoRegime()];
        $dadosVinculoTrabalho['tpRegPrev'] = null;

        /**
         *  Regra do Else
         *  Sempre que tiver um Adido sem previdência, colocar "RPPS".
         */

        if ($this->servidor->getTabelaPrevidencia() != 0) {
            $dadosVinculoTrabalho['tpRegPrev'] = $this->servidor->isRgps() ? 1 : 2;
        } else {
            $registroCedencia = new Cedencia($this->servidor->getMatricula());
            if ($registroCedencia->getTipoCedencia() == 'A') {
                $dadosVinculoTrabalho['tpRegPrev'] = 2;
            }
        }
        
        try {
            $dataAdmissao = $this->servidor->getDataAdmissao()->getDate();
        } catch (\ParameterException $e) {
            $sMsgErro = $this->getMensagemValidaData('data admissão');
            throw new \ParameterException($sMsgErro);
        }

        
        try {
            $dataObrigatoriedade = DBPessoal::getDataFaseEsocial(2);
        } catch (\ParameterException $e) {
            $msg = "Favor revisar a data de obrigatoriedade da fase 2, na seguinte rotina: \n " .
                "DB:RECURSOSHUMANOS > Pessoal > Procedimentos > Manutenção de Parâmetros > Gerais";
            $sMsgErro = $this->getMensagemValidaData($msg, true);
            throw new \ParameterException($sMsgErro);
        }

        if (!$dataObrigatoriedade) {
            throw new Exception("Data de obrigatoriedade inválida para este evento.");
        } else {
            $dataObrigatoriedade = DBPessoal::getDataFaseEsocial(2)->getDate();
        }
        if ($dataAdmissao > $dataObrigatoriedade) {
            $dadosVinculoTrabalho['cadIni'] = 'N';
        } else {
            $dadosVinculoTrabalho['cadIni'] = 'S';
        }

        $this->servidorEntity->setVinculoTrabalho($dadosVinculoTrabalho);
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoRegimeTrab->infoCeletista'
     */
    private function celetista()
    {

        $celetista = $this->servidor->isClt();

        if ($celetista) {
            $dadosCeletista = [];
            $dadosCeletista['infoCeletista'] = [];
            try {
                $dadosCeletista['infoCeletista']['dtAdm'] = $this->servidor->getDataAdmissao()->getDate();
            } catch (\ParameterException $e) {
                $sMsgErro = $this->getMensagemValidaData('data admissão celetista');
                throw new \ParameterException($sMsgErro);
            }
            $dadosCeletista['infoCeletista']['tpAdmissao'] = (int) $this->servidor->getVinculo()->getTipoAdmissao();
            $dadosCeletista['infoCeletista']['indAdmissao'] = 1;
            //NÃO SERÁ PREEENCHIDO
            //$dadosCeletista['infoCeletista']['nrProcTrab'] = '';

            if ($this->servidorMovimentacao->getTipoRegime() == 2) {
                $dadosCeletista['infoCeletista']['tpRegJor'] = $this->servidorMovimentacao->getRegimeJornadaTrabalho();
            }
            $dadosCeletista['infoCeletista']['natAtividade'] = 1; // 1- URBANO 2- RURAL
            $admissaoDados = new AdmissaoDado($this->servidor->getMatricula());
            $valorDataBase = $admissaoDados->getMesDataBase();

            $dadosCeletista['infoCeletista']['dtBase'] = (int) $valorDataBase;
            $dadosCeletista['infoCeletista']['cnpjSindCategProf'] = "";
            if (!empty($this->servidor->getSindicato())  && !empty($this->servidor->getSindicato()->getCnpj())) {
                $dadosCeletista['infoCeletista']['cnpjSindCategProf'] = preg_replace(
                    '\'[^0-9]\'',
                    '',
                    $this->servidor
                        ->getSindicato()
                        ->getCnpj()
                );
            }
            if (!empty($this->servidor->getDataOptanteFgts())) {
                $categoria = (int) $this->servidor->getVinculo()->getCodigoCategoria();
                $dataBase1 = new \DBDate('1988-10-05');
                $dataBase2 = new \DBDate('2015-10-01');
                if ($dadosCeletista['infoCeletista']['tpAdmissao'] == 6
                    || ($categoria != 114 && $this->servidor->getDataAdmissao() >= $dataBase1)
                    || ($categoria == 114 && $this->servidor->getDataAdmissao() >= $dataBase2)
                ) {
                    // não envia, seguindo o layout s1.0
                } else {
                    $dadosCeletista['infoCeletista']['FGTS'] = [];
                    $dadosCeletista['infoCeletista']['FGTS']['dtOpcFGTS'] =
                        $this->servidor->getDataOptanteFgts()->rh15_data;
                }
            }
            if ($this->admissaoMatricula->getIsTemporario()) {
                $dadosCeletista['infoCeletista']['trabTemporario'] = [];
                $hipoteseLegalTrabTemp = $admissaoDados->getHipoteseLegalTrabTemp();
                $admissao = new Admissao($this->servidor->getMatricula());

                $dadosCeletista['infoCeletista']['trabTemporario']['hipLeg'] = (int) $hipoteseLegalTrabTemp;
                $dadosCeletista['infoCeletista']['trabTemporario']['justContr'] = $admissao->getJustificativaLegal();
                $dadosCeletista['infoCeletista']['trabTemporario']['ideEstabVinc'] = [];
                $dadosCeletista['infoCeletista']['trabTemporario']['ideEstabVinc']['tpInsc'] = 1;
                if ($this->servidor->getLocalTrabalhoPrincial() !== null) {
                    $dadosCeletista['infoCeletista']['trabTemporario']['ideEstabVinc']['nrInsc'] =
                    $this->servidor
                        ->getLocalTrabalhoPrincial()
                        ->getInstituicao()
                        ->getCNPJ();
                }
            }
            $menorAprendiz = MenorAprendizRepository::findByMatricula($this->servidor->getMatricula());
            if (!empty($menorAprendiz)) {
                $dadosCeletista['infoCeletista']['aprend']['indAprend'] = (int) $menorAprendiz->getModalidade();
                if ($menorAprendiz->getModalidade() == 1) {
                    $dadosCeletista['infoCeletista']['aprend']['cnpjEntQual'] = $menorAprendiz->getCnpjDireta();
                } else {
                    // Valida CNPJ/CPF
                    if (strlen($menorAprendiz->getCnpjIndireta()) == 14) {
                        $dadosCeletista['infoCeletista']['aprend']['tpInsc'] = 1;
                    } else {
                        $dadosCeletista['infoCeletista']['aprend']['tpInsc'] = 2;
                    }
                    $dadosCeletista['infoCeletista']['aprend']['nrInsc'] = $menorAprendiz->getCnpjIndireta();
                }
                if (!empty($menorAprendiz->getCnpjPratica())) {
                    $dadosCeletista['infoCeletista']['aprend']['cnpjPrat'] = $menorAprendiz->getCnpjPratica();
                }
            }
            $this->servidorEntity->setCeletista($dadosCeletista);
        }
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoContrato->remuneracao'
     * @throws \DBException
     */
    private function remuneracao()
    {
        if (!isset($this->dadosServidor->vinculo['infoContrato'])) {
            $this->dadosServidor->vinculo['infoContrato'] = array();
        }

        if (!isset($this->dadosServidor->vinculo['infoContrato']['remuneracao'])) {
            $this->dadosServidor->vinculo['infoContrato']['remuneracao'] = array();
        }

        $dadosRemuneracao = array();
        //Condição para o evento eSocial S2200
        if ($this->servidor->isCeletista()) {
            $dadosRemuneracao['vrSalFx'] = (float) number_format($this->servidor->getSalario(true), 2, '.', '');
            $dadosRemuneracao['undSalFixo'] =
                $this->deParaUnidadePagamento[$this->servidorMovimentacao->getTipoSalario()];

            if ($dadosRemuneracao['undSalFixo'] == 6 || $dadosRemuneracao['undSalFixo'] == 7) {
                $dadosRemuneracao['dscSalVar'] = '';//TEM QUE SER PREENCHIDO
            }
        }


        //Condição para o evento eSocial S2300
        if (!$this->servidor->temVinculoEmpregaticio() &&
            $this->servidor->isAtivo()) {
            $dadosRemuneracao['vrSalFx'] = (float) number_format($this->servidor->getSalario(true), 2, '.', '');
            $tipoSalario = $this->servidorMovimentacao->getTipoSalario();
            $tipoSalario = $tipoSalario == 1 ? 'M' : $tipoSalario;
            if (!empty($this->deParaUnidadePagamento[$tipoSalario])) {
                $dadosRemuneracao['undSalFixo'] =
                    $this->deParaUnidadePagamento[$tipoSalario];
                if ($dadosRemuneracao['undSalFixo'] == 6 || $dadosRemuneracao['undSalFixo'] == 7) {
                    $dadosRemuneracao['dscSalVar'] = '';//TEM QUE SER PREENCHIDO
                }
            }
        }

        $this->servidorEntity->setRemuneracao($dadosRemuneracao);
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoRegimeTrab->infoCeletista->FGTS'
     */
    private function dadosFgts()
    {
        $dadosFgts = array();
        if (!empty($this->servidor->getDataOptanteFgts()->rh15_data)) {
            $dadosFgts['dtOpcFGTS'] = $this->servidor->getDataOptanteFgts()->rh15_data;
        }
        $dadosFgts['opcFGTS'] = !empty($dadosFgts['dtOpcFGTS']) ? 1 : 2;

        $this->servidorEntity->setFgts($dadosFgts);
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoContrato->filiacaoSindical'
     */
    private function filiacaoSindical()
    {
        if ($this->servidor->getSindicato() !== null) {
            $dadosFiliacaoSindical = array();
            $cnpj = preg_replace('\'[^0-9]\'', '', $this->servidor->getSindicato()->getCnpj());

            if (empty($cnpj)) {
                return;
            }

            $dadosFiliacaoSindical['cnpjSindTrab'] = $cnpj;

            $this->servidorEntity->setFiliacaoSindical($dadosFiliacaoSindical);
        }
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoContrato->horContratual'
     */
    private function dadosHoraContratual()
    {
        if ($this->servidorMovimentacao->getHorasSemanais() !== null) {
            if (!isset($this->dadosServidor->vinculo['infoContrato'])) {
                $this->dadosServidor->vinculo['infoContrato'] = [];
            }

            $this->dadosServidor->vinculo['infoContrato']['horContratual'] = [];

            $horasSemanais = [];
            $horasSemanais['qtdHrsSem'] = '';
            if ($this->servidor->getVinculo()->getCodigoCategoria() !== 111) {
                $horasSemanais['qtdHrsSem'] = $this->servidorMovimentacao->getHorasSemanais();
            }
            $instituicao = db_getsession("DB_instit");
            $contratoJornada = new ContratoJornada($this->servidor->getMatricula(), $instituicao);
            $horasSemanais['tpJornada'] = $contratoJornada->getTipoJornada();
            $horasSemanais['tmpParc'] = $contratoJornada->getTempoParcial();
            $horasSemanais['horNoturno'] = $contratoJornada->getHorarioNoturno();
            $horasSemanais['dscJorn'] = $contratoJornada->getDescricaoJornada();


            $this->servidorEntity->setHoraContratual($horasSemanais);
        }
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoContrato'
     */
    private function dadosContratoTrabalho()
    {

        $this->dadosServidor->vinculo['infoContrato'] = [];

        $dadosFormulario = $this->dadosServidor->vinculo['infoContrato'];
        $dadoContratoTrabalho = [];

        $dadoContratoTrabalho = $this->dadosCargoFuncao();
        $dadoContratoTrabalho['codCateg'] = (int) $this->servidor->getVinculo()->getCodigoCategoria();
        $dadoContratoTrabalho['remuneracao'] = $this->servidorEntity->getRemuneracao();
        $dadoContratoTrabalho['duracao'] = $this->servidorEntity->getDuracaoContrato();
        $dadoContratoTrabalho['localTrabalho'] = $this->servidorEntity->getLocalTrabalhoContrato();
        $dadoContratoTrabalho['horContratual'] = $this->servidorEntity->getHoraContratual();
        $dadoContratoTrabalho['observacoes'] = $this->servidorEntity->getObservacaoContratoTrabalho();
        $dadoContratoTrabalho['treiCap'] = [];
        $dadosTreiCap = $this->getTreiCap();

        // Prenche os dados da busca no Array 'codTreiCap'
        foreach ($dadosTreiCap as $treiCaps) {
            $dados = [];
            $dados['codTreiCap'] = (int) $treiCaps;
            $dadoContratoTrabalho['treiCap'][] = $dados;
        }
        $this->servidorEntity->setContratoTrabalho($dadoContratoTrabalho);
    }


    private function getTreiCap()
    {

        $curric = new cl_curric();
        $treiCap = [];
        $numCgm =$this->servidor->getCgm()->getCodigo();


        $result = $curric->sql_query(null, 'h02_tipotreinamento', null, "h03_numcgm = " . $numCgm);
        $rsCurric = db_query($result);
        $totalDadosCurric= pg_num_rows($rsCurric);
        for ($i = 0; $i < $totalDadosCurric; $i++) {
            $oCurric = db_utils::fieldsMemory($rsCurric, $i);
            if (!empty($oCurric->h02_tipotreinamento)) {
                $treiCap[] = $oCurric->h02_tipotreinamento;
            }
        }
        return $treiCap;
    }

    /**
     * Organiza os dados do grupo 'vinculo->desligamento'
     * @throws \BusinessException
     */
    private function dadosDesligamento()
    {
        $dadosDesligamento = [];

        if (!empty($this->servidor->getDataRescisao())) {
            $dataFormatada = $this->servidor->getDataRescisao()->format('Y-m-d');
            try {
                $dataRescicao = new DBDate($dataFormatada);
            } catch (\ParameterException $e) {
                $sMsgErro = $this->getMensagemValidaData('data rescisão');
                throw new \ParameterException($sMsgErro);
            }
            
            try {
                $dataObrigatoriedade = DBPessoal::getDataFaseEsocial(2);
            } catch (\ParameterException $e) {
                $msg = "Favor revisar a data de obrigatoriedade da fase 2, na seguinte rotina: \n " .
                    "DB:RECURSOSHUMANOS > Pessoal > Procedimentos > Manutenção de Parâmetros > Gerais";
                $sMsgErro = $this->getMensagemValidaData($msg, true);
                throw new \ParameterException($sMsgErro);
            }
            
            if ($dataObrigatoriedade > $dataRescicao) {
                $dadosDesligamento['dtDeslig'] = $dataFormatada;
            }
        }
        $this->servidorEntity->setDesligamento($dadosDesligamento);
    }
    /**
     * Organiza os dados do grupo 'vinculo->infoContrato->duracao'
     * @throws \DBException
     */
    private function duracaoContrato()
    {
        $dadosDuracaoContrato = [];
        $contratoEmergencial = new ContratoEmergencial($this->servidor->getMatricula());
        $vinculo = $this->servidorEntity->getVinculoTrabalho();
        if (isset($vinculo['tpRegTrab']) && $vinculo['tpRegTrab'] == 1) {
            $isTemporario = $this->admissaoMatricula->getIsTemporario() == false ? 'f' : 't';
            if ($isTemporario == 't') {
                $dadosDuracaoContrato['clauAssec'] = $contratoEmergencial->getAsseCuratoria();
                $dadosDuracaoContrato['dtTerm'] = $contratoEmergencial->getDataFim();

                $dadosDuracaoContrato['tpContr'] = '';
                if (!empty($dadosDuracaoContrato['dtTerm'])
                    || !empty($dadosDuracaoContrato['clauAssec'])
                ) {
                    $dadosDuracaoContrato['tpContr'] = 2;
                } else {
                    $dadosDuracaoContrato['tpContr'] = 1;
                }
                if ($dadosDuracaoContrato['tpContr'] == 3) {
                    $dadosDuracaoContrato['objDet'] = $isTemporario;
                }
            } else {
                // Comentado if pois estavamos tendo diversos retornos de erros devido ao nao preenchimento do campo
                // if (empty($contratoEmergencial->getCodigo())) {
                    //seta o tipo de contrato como prazo indeterminado
                    $dadosDuracaoContrato['tpContr'] = 1;
                // }
            }
        }
        $this->servidorEntity->setDuracaoContrato($dadosDuracaoContrato);
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoContrato->localTrabalho'
     * @throws \DBException
     */
    private function localTrabalhoContrato()
    {
        if (!isset($this->dadosServidor->vinculo['infoContrato'])) {
            $this->dadosServidor->vinculo['infoContrato'] = [];
        }

        if (!isset($this->dadosServidor->vinculo['infoContrato']['localTrabalho'])) {
            $this->dadosServidor->vinculo['infoContrato']['localTrabalho'] = [];
        }

        if (!isset($this->dadosServidor->vinculo['infoContrato']['localTrabalho']['localTrabGeral'])) {
            $this->dadosServidor->vinculo['infoContrato']['localTrabalho']['localTrabGeral'] = [];
        }

        $tipoInscricao = '';
        $numeroInscricao = '';
        $descricao = '';
        
        if ($this->servidor->getLocalTrabalhoPrincial()) {
            $tipoInscricao = $this->servidor->getLocalTrabalhoPrincial()->getTipoInscricao();
            $numeroInscricao = $this->servidor->getLocalTrabalhoPrincial()->getNumeroInscricao();
            $descricao = $this->servidor->getLocalTrabalhoPrincial()->getDescricao();
            if ($tipoInscricao == "" || empty($tipoInscricao)) {
                $tipoInscricao = 1;
                $numeroInscricao = $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getCNPJ();
                $descricao = $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getCgm()->getNome();
            }
        }

        $dadosLocalTrabalhoContrato = [];

        if ($tipoInscricao != "") {
            $dadosLocalTrabalhoContrato['localTrabGeral']['tpInsc'] = (int) $tipoInscricao;
        }
        if ($numeroInscricao != "") {
            $dadosLocalTrabalhoContrato['localTrabGeral']['nrInsc'] = $numeroInscricao;
        }
        $dadosLocalTrabalhoContrato['localTrabGeral']['descComp'] = substr($descricao, 0, 80);

        /*
        TEMPORARIAMENTE EM DESUSO
        if (!isset($this->dadosServidor->vinculo['infoContrato']['localTrabalho']['localTempDom'])) {
            $this->dadosServidor->vinculo['infoContrato']['localTrabalho']['localTempDom'] = [];
        }

        $dadosLocalTrabalhoContrato['localTempDom']['tpLograd'] = $this->endereco->getSiglaRua();
        $dadosLocalTrabalhoContrato['localTempDom']['dscLograd'] =
            $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getLogradouro();
        $dadosLocalTrabalhoContrato['localTempDom']['nrLograd'] =
            $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getNumero();
        if (empty($dadosLocalTrabalhoContrato['localTempDom']['nrLograd'])) {
            $dadosLocalTrabalhoContrato['localTempDom']['nrLograd'] = '';
        }
        $dadosLocalTrabalhoContrato['localTempDom']['complemento'] =
            $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getComplemento();
        $dadosLocalTrabalhoContrato['localTempDom']['bairro'] =
            $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getBairro();
        $dadosLocalTrabalhoContrato['localTempDom']['cep'] =
            $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getCep();
        $dadosLocalTrabalhoContrato['localTempDom']['codMunic'] =
            $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getMunicipio();
        $dadosLocalTrabalhoContrato['localTempDom']['uf'] =
            $this->servidor->getLocalTrabalhoPrincial()->getInstituicao()->getUf();
        $dadosLocalTrabalhoContrato['localTrabGeral']['tpInsc'] = 1;
        $dadosLocalTrabalhoContrato['localTrabGeral']['nrInsc'] = '28521748000159';*/
        $this->servidorEntity->setLocalTrabalhoContrato($dadosLocalTrabalhoContrato);
    }

    /**
     * Organiza os dados do grupo 'trabalhador->dependente'
     */
    private function dadosDependentes()
    {
        $dependenteRepository = new DependenteRepository();
        $dependentes = $dependenteRepository
            ->scopeMatricula($this->servidor->getMatricula())
            ->orderBy(array('rh31_nome'))
            ->scopeIrrfComplemetar('true', '!=')
            ->setUseJoin(true)
            ->get([
                'rh31_nome',
                'rh31_dtnasc',
                'rh31_irf',
                'dp01_cpf',
                'rh31_fins_previdenciarios',
                'rh31_gparen',
                'dp01_sexo',
                'rh31_tipoparentesco',
                'rh31_descricaoparentesco',
            ]);
        $dadosDependentes = [];
        foreach ($dependentes as $dependente) {
            $dados = [];
            $dados['nmDep'] = $dependente->getNome();
            $dados['dtNascto'] = "";
            if (!is_null($dependente->getDataNascimento())) {
                try {
                    $dados['dtNascto'] = $dependente->getDataNascimento()->getDate();
                } catch (\ParameterException $e) {
                    $sMsgErro = $this->getMensagemValidaData('data nascimento de dependente');
                    throw new \ParameterException($sMsgErro);
                }
            }
            $dados['cpfDep'] = $dependente->getCpf();
            if (!empty($dependente->getTipoParentesco())) {
                $dados['tpDep'] = $dependente->getTipoParentesco();
                if ($dependente->getTipoParentesco() == "99") {
                    if (!empty($dependente->getDescricaoParentesco())) {
                        $dados['descrDep'] = $dependente->getDescricaoParentesco();
                    }
                }
            } else {
                $dados['tpDep'] = $this->deParaTipoDependente[$dependente->getGrauParentesco()];
            }
            $dados['sexoDep'] = $dependente->getSexo();
            $regPrev = $this->servidor->isRgps() ? 1 : 2;
            if ($regPrev !== 2) {
                unset($dados['sexoDep']);
            }
            $dados['depIRRF'] = ($dependente->getTipo() == 0 || empty($dependente->getTipo())) ? 'N' : 'S';
            if ($dependente->getSalarioFamilia() == 'N' || empty($dependente->getSalarioFamilia())) {
                $dados['depSF'] = 'N';
            } else {
                $dados['depSF'] = 'S';
            }
            if ($dependente->getCondicaoEspecial() == 'N' || empty($dependente->getCondicaoEspecial())) {
                $dados['incTrab'] = 'N';
            } else {
                $dados['incTrab'] = 'S';
            }
            if ($dados['depIRRF'] != 'S') {
                $dados['cpfDep'] = '';
            }
            $dadosDependentes[] = $dados;
        }
        $this->servidorEntity->setDependentes($dadosDependentes);
    }

    /**
     * Organiza os dados do grupo 'trabalhador->trabImig'
     */
    private function dadosImigrantes()
    {
        $imigrante = new Imigrante($this->servidor->getMatricula(), $this->servidor->getInstituicao()->getSequencial());

        $dadosImigrante = [];
        if (!empty($imigrante->getCodigoResidencia())) {
            $dadosImigrante['tmpResid'] = (int) $imigrante->getCodigoResidencia();
        }
        $dadosImigrante['condIng'] = (int) $imigrante->getCodigoCondicao();

        $this->servidorEntity->setImigrante($dadosImigrante);
    }

    /**
     * Organiza os dados do grupo 'trabalhador->contato'
     */
    private function dadosContado()
    {
        $dadosContato = [];
        $dadosContato['fonePrinc'] = $this->servidor->getCgm()->getTelefone();
        $dadosContato['emailPrinc'] = $this->servidor->getCgm()->getEmail();

        $this->servidorEntity->setContato($dadosContato);
    }

    /**
     * Organiza os dados do grupo 'vinculo->infoRegimeTrab->infoCeletista'
     */
    private function estatutario()
    {
        $dadoEstatutario = [];
        $tipoRegime = $this->servidorMovimentacao->getTipoRegime();
        if ($tipoRegime == 1 || $tipoRegime == 3) {
            $tipoProvimento = '';
            if ($this->servidor->getVinculo()->getCodigoCategoria() == '302') {
                $tipoProvimento = 2;
            } elseif ($this->servidor->getVinculo()->getTpProvimento() == 0) {
                $tipoProvimento = '99';
            } else {
                $tipoProvimento = $this->servidor->getVinculo()->getTpProvimento();
            }

            $matricula = $this->servidor->getMatricula();

            $dadoEstatutario['tpProv'] = (int) $tipoProvimento;
            try {
                $dadoEstatutario['dtExercicio'] = $this->servidor->getDataAdmissao()->getDate();
            } catch (\ParameterException $e) {
                $sMsgErro = $this->getMensagemValidaData('data admissão estatutário');
                throw new \ParameterException($sMsgErro);
            }
            

            $regPrev = $this->servidor->isRgps() ? 1 : 2;
            if ($regPrev == 2) {
                $dadoEstatutario['tpPlanRP'] = (int) $this->servidorMovimentacao->getTipoSegregacao();
                $dadoEstatutario['indTetoRGPS'] = $this->servidorMovimentacao->getIndTetoRGPS($matricula);
                $dadoEstatutario['indAbonoPerm'] = 'N';
                $dadoEstatutario['dtIniAbono'] = '';
                try {
                    $dataObrigatoriedade = DBPessoal::getDataFaseEsocial(2)->getDate();
                } catch (\ParameterException $e) {
                    $msg = "Favor revisar a data de obrigatoriedade da fase 2, na seguinte rotina: \n " .
                        "DB:RECURSOSHUMANOS > Pessoal > Procedimentos > Manutenção de Parâmetros > Gerais";
                    $sMsgErro = $this->getMensagemValidaData($msg, true);
                    throw new \ParameterException($sMsgErro);
                }
                
                if (!empty($this->servidorMovimentacao->getDataPermanenciaAbonada())) {
                    $dataAbono = $this->servidorMovimentacao->getDataPermanenciaAbonada()->format('Y-m-d');
                }
                if (!empty($dataAbono)) {
                    //Regra S-2200
                    if (!$this->alteracao && $dataAbono < $dataObrigatoriedade) {
                         $dadoEstatutario['dtIniAbono'] = $dataAbono;
                         $dadoEstatutario['indAbonoPerm'] = 'S';
                    }
                    //Regra S-2205
                    if ($this->alteracao && $dataAbono >= $dataObrigatoriedade) {
                        $dadoEstatutario['dtIniAbono'] = $dataAbono;
                        $dadoEstatutario['indAbonoPerm'] = 'S';
                    }
                }
            }
        }
        $this->servidorEntity->setEstatutario($dadoEstatutario);
    }

    /**
     * Organiza os dados do grupo 'trabalhador->infoDeficiencia'
     */
    private function dadosDeficiente()
    {
        $deficiente = new Deficiente($this->servidor->getMatricula());
        $dadoDeficiente = [];
        $dadoDeficiente['defFisica'] = $deficiente->getFisica() == 't' ? 'S' : 'N';
        $dadoDeficiente['defVisual'] = $deficiente->getVisual() == 't' ? 'S' : 'N';
        $dadoDeficiente['defAuditiva'] = $deficiente->getAuditiva() == 't' ? 'S' : 'N';
        $dadoDeficiente['defMental'] = $deficiente->getMental() == 't' ? 'S' : 'N';
        $dadoDeficiente['defIntelectual'] = $deficiente->getIntelectual() == 't' ? 'S' : 'N';
        $dadoDeficiente['reabReadap'] = $deficiente->getReabilitado() == 't' ? 'S' : 'N';
        $dadoDeficiente['infoCota'] = $deficiente->getCota() == 't' ? 'S' : 'N';
        $dadoDeficiente['observacao'] = trim(preg_replace('/\s+/', ' ', $deficiente->getObservacao()));
        $this->servidorEntity->setDeficiente($dadoDeficiente);
    }

    /**
     * Organiza os dados do grupo 'vinculo->sucessaoVinc'
     */
    private function dadosSucessao()
    {
        //TODO
        $dadoSucessao = [];
        $dadoSucessao['tpInsc'] = '';
        $dadoSucessao['nrInsc'] = '';
        $dadoSucessao['matricAnt'] = '';
        $dadoSucessao['dtTransf'] = '';
        $dadoSucessao['observacao'] = '';

        $this->servidorEntity->setSucessao($dadoSucessao);
    }

    /**
     * Organiza os dados do grupo 'vinculo->mudancaCPF'
     */
    private function dadosMudancaCPF()
    {
        //TODO
        $dadoMudancaCPF = [];
        $dadoMudancaCPF['cpfAnt'] = '';
        $dadoMudancaCPF['matricAnt'] = '';
        $dadoMudancaCPF['dtAltCPF'] = '';
        $dadoMudancaCPF['observacao'] = '';

        $this->servidorEntity->setMudancaCPF($dadoMudancaCPF);
    }

    /**
     * Organiza os dados do grupo 'vinculo->afastamento'
     */
    private function dadosAfastamento()
    {
        $servidor = \ServidorRepository::getInstanciaByCodigo($this->servidor->getMatricula());
        try {
            $dataObrigatoriedade = \DBPessoal::getDataFaseEsocial(2);
        } catch (\ParameterException $e) {
            $msg = "Favor revisar a data de obrigatoriedade da fase 2, na seguinte rotina: \n " .
                "DB:RECURSOSHUMANOS > Pessoal > Procedimentos > Manutenção de Parâmetros > Gerais";
            $sMsgErro = $this->getMensagemValidaData($msg, true);
            throw new \ParameterException($sMsgErro);
        }
        
        $servidorValidaData = \AssentamentoRepository::servidorValidaData($servidor, $dataObrigatoriedade);
        $dadoAfastamento = [];
        if (!empty($servidorValidaData->h16_dtconc)) {
            $dadoAfastamento['dtIniAfast'] = $servidorValidaData->h16_dtconc;
        }

        if (!empty($servidorValidaData->db110_valor)) {
            $dadoAfastamento['codMotAfast'] = str_pad($servidorValidaData->db110_valor, 2, "0", STR_PAD_LEFT);
        }

        $this->servidorEntity->setAfastamento($dadoAfastamento);
    }

    /**
     * Organiza os dados do grupo 'vinculo->cessao'
     */
    private function dadosCessao()
    {
        $dadoCessao = [];
        $iniCessao = new Cedencia($this->servidor->getMatricula());
        try {
            $dadoCessao['dtIniCessao'] =
            $iniCessao->getDataMovimentacao() ? $iniCessao->getDataMovimentacao()->getDate() : '';
        } catch (\ParameterException $e) {
            $sMsgErro = $this->getMensagemValidaData('data início de cessão');
            throw new \ParameterException($sMsgErro);
        }
        
        $this->servidorEntity->setCessao($dadoCessao);
    }

    /**
     * Cargos e funções
     * @throws \DBException
     */
    private function dadosCargoFuncao()
    {
        $codigoCategoria = $this->servidor->getVinculo()->getCodigoCategoria();
        $regime = (int) $this->servidor->getTipoRegime();
        $funcao = '';
        $cboFuncao = '';
        $cargoFuncao['nmCargo'] = $this->servidor->getDadosCargo()->rh37_descr;
        $cargoFuncao['CBOCargo'] = $this->servidor->getDadosCargo()->rh37_cbo;
        try {
            $cargoFuncao['dtIngrCargo'] = $this->servidor->getDataAdmissao()->getDate();
        } catch (\ParameterException $e) {
            $sMsgErro = $this->getMensagemValidaData('data admissão');
            throw new \ParameterException($sMsgErro);
        }

        if (!empty($this->servidor->getDadosCargo()->rh37_acumcargo)) {
            $cargoFuncao['acumCargo'] = $this->servidor->getDadosCargo()->rh37_acumcargo === 'true' ? 'S' : 'N';
        }

        //Se não há preenchimento de cargo
        if (!empty($this->servidorMovimentacao->getCargo())) {
            $daoFuncao = new \cl_rhcargo;
            $sSql = $daoFuncao->
                sql_query_file($this->servidorMovimentacao->getCargo(), db_getsession("DB_instit"), 'rh04_descr');
            $rsFuncao = db_query($sSql);
            if (!$rsFuncao) {
                throw new \DBException("Erro ao executar a query: {$sSql}");
            }
            $funcao = \db_utils::fieldsMemory($rsFuncao, 0)->rh04_descr;
            if (empty($funcao)) {
                $funcao = '';
            }

            $daoCboFuncao = new \cl_rhfuncao;
            $sql = $daoCboFuncao->
                sql_query_file($this->servidorMovimentacao->getFuncao(), db_getsession("DB_instit"), 'rh37_cbo');

            $rsCboFuncao = db_query($sql);
            if (!$rsCboFuncao) {
                throw new \DBException("Erro ao executar a query: {$sql}");
            }
            $cboFuncao = \db_utils::fieldsMemory($rsCboFuncao, 0)->rh37_cbo;
            if (empty($cboFuncao)) {
                $cboFuncao = '';
            }
            $cargoFuncao['nmFuncao'] = $funcao;
            $cargoFuncao['CBOFuncao'] = $cboFuncao;
        }

        /**
         * Devemos enviar informacoes da funcao exclusivamente quando tpRegTrab = 2 e TpProv = 2,
         *  que é cargo em comissão.
         *  $this->deParaRegimeTrabalho = tbRegTrab
         *  $this->servidor->getVinculo()->getCodigoCategoria() == '302' = seguindo o manual é o tpProv = 2
         *  ou simplesmente $this->servidor->getVinculo()->getTpProvimento() = 2
         */
        if ($this->deParaRegimeTrabalho[$this->servidor->getTipoRegime()] == 2
            && ($this->servidor->getVinculo()->getTpProvimento() ==  2
                || $this->servidor->getVinculo()->getCodigoCategoria() == '302')
        ) {
            $cargoFuncao['nmCargo'] = '';
            $cargoFuncao['CBOCargo'] = '';
            $cargoFuncao['dtIngrCargo'] = '';
        } else {
            $cargoFuncao['CBOFuncao'] = '';
            $cargoFuncao['nmFuncao'] = '';
        }

        return $cargoFuncao;
    }

    /**
     * Estagiário
    * @throws \DBException
    */
    private function estagiario()
    {
        $estagiario = new cl_rhestagiovinculo();
        $numMatricula =$this->servidor->getMatricula();
        $campos = ['rh260_sequencial',
                   'rh260_matricula',
                   'rh260_naturezaestagio',
                   'rh260_nivelestagio',
                   'rh260_dataterminoestagio',
                   'rh260_cnpjinstensino',
                   'rh260_cnpjagentintegracao',
                   'rh260_areaatuacao',
                   'rh260_apoliceseguro',
                   'rh260_cpfsupervisor'
        ];
        $campos = implode(', ', $campos);
        $resultado = $estagiario->sql_query(null, $campos, null, "rh260_matricula = " . $numMatricula);
        $rsEstagiario = db_query($resultado);
        if (!$rsEstagiario) {
            throw new \DBException("Erro ao executar a query: {$resultado}");
        }
        $estagiario = db_utils::fieldsMemory($rsEstagiario, 0);

        return $estagiario;
    }

    /**
    * Estagiário eSocial
    * @throws \DBException
    */
    private function dadosEstagiario()
    {
     //Condição para o evento eSocial S2300
        if (!$this->servidor->temVinculoEmpregaticio() &&
            $this->servidor->isAtivo()) {
            $registroEstagiario = $this->estagiario();
            $dadoEstagiario = [];
            $dadoEstagiario['infoEstagiario']['natEstagio'] = $registroEstagiario->rh260_naturezaestagio;
            $dadoEstagiario['infoEstagiario']['nivEstagio'] = (int) $registroEstagiario->rh260_nivelestagio;
            $dadoEstagiario['infoEstagiario']['areaAtuacao'] = $registroEstagiario->rh260_areaatuacao;
            $dadoEstagiario['infoEstagiario']['nrApol'] = $registroEstagiario->rh260_apoliceseguro;
            $dadoEstagiario['infoEstagiario']['dtPrevTerm'] = $registroEstagiario->rh260_dataterminoestagio;
            $dadoEstagiario['infoEstagiario']['instEnsino']['cnpjInstEnsino'] =
                $registroEstagiario->rh260_cnpjinstensino;
            $dadoEstagiario['infoEstagiario']['instEnsino']['nmRazao'] = '';
            $dadoEstagiario['infoEstagiario']['instEnsino']['dscLograd'] = '';
            $dadoEstagiario['infoEstagiario']['instEnsino']['nrLograd'] = '';
            $dadoEstagiario['infoEstagiario']['instEnsino']['bairro'] = '';
            $dadoEstagiario['infoEstagiario']['instEnsino']['codMunic'] = '';
            $dadoEstagiario['infoEstagiario']['instEnsino']['uf'] = '';
            $dadoEstagiario['infoEstagiario']['ageIntegracao']['cnpjAgntInteg'] =
                $registroEstagiario->rh260_cnpjagentintegracao;
            $dadoEstagiario['infoEstagiario']['supervisorEstagio']['cpfSupervisor'] =
                $registroEstagiario->rh260_cpfsupervisor;
            $this->servidorEntity->setEstagiario($dadoEstagiario);
        }
    }

    /**
     * Cedência
     */
    private function dadosCedencia()
    {
     //Condição para o evento eSocial S2300
        if (!$this->servidor->temVinculoEmpregaticio() &&
            $this->servidor->isAtivo()) {
            $registroCedencia = new Cedencia($this->servidor->getMatricula());
            $tipoCedencia = $registroCedencia->getTipoCedencia();
            $dataDevolucao = $registroCedencia->getDataDevolucao();
            if (($tipoCedencia == 'C' ||
                $tipoCedencia == 'A') &&
                empty($dataDevolucao)) {
                $dadoCedencia = [];
                $dadoCedencia['categOrig'] = $registroCedencia->getCodCategoriaOrigem() ?
                    (int) $registroCedencia->getCodCategoriaOrigem() : '';
                $dadoCedencia['cnpjCednt'] = $registroCedencia->getCnpjCedencia();
                $dadoCedencia['matricCed'] = $registroCedencia->getMatriculaCedencia();
                if (!empty($registroCedencia->getDataAdmissaoOrigem())) {
                    try {
                        $dadoCedencia['dtAdmCed'] = $registroCedencia->getDataAdmissaoOrigem()->getDate();
                    } catch (\ParameterException $e) {
                        $sMsgErro = $this->getMensagemValidaData('data admissão');
                        throw new \ParameterException($sMsgErro);
                    }
                }
                $dadoCedencia['tpRegTrab'] = $registroCedencia->getTipoRegimeOrigem() ?
                    (int) $registroCedencia->getTipoRegimeOrigem() : '';
                $dadoCedencia['tpRegPrev'] = $registroCedencia->getTipoRegimePrevidencia() ?
                    (int) $registroCedencia->getTipoRegimePrevidencia() : '';
                $this->servidorEntity->setCedencia($dadoCedencia);
            }
        }
    }

    /**
     * Cargos e funções sem vinculo
     * @throws \DBException
     */
    private function dadosCargoFuncaoSemViculo()
    {
        //Condição para o evento eSocial S2300
        if (!$this->servidor->temVinculoEmpregaticio() &&
            $this->servidor->isAtivo()) {
            $dadoCargoFuncao = [];

            $dadoCargoFuncao['nmCargo'] = $this->servidor->getDadosCargo()->rh37_descr;
            $dadoCargoFuncao['CBOCargo'] = $this->servidor->getDadosCargo()->rh37_cbo;

            $daoCboFuncao = new \cl_rhcargo;
            if (!empty($this->servidorMovimentacao->getCargo())) {
                $sql = $daoCboFuncao->sql_query_file(
                    $this->servidorMovimentacao->getCargo(),
                    db_getsession("DB_instit"),
                    'rh04_descr'
                );
                $rsCboFuncao = db_query($sql);
                if (!$rsCboFuncao) {
                    throw new \DBException("Erro ao executar a query: {$sql}");
                }
                $dadoCargoFuncao['nmFuncao'] = \db_utils::fieldsMemory($rsCboFuncao, 0)->rh04_descr;
                $dadoCargoFuncao['CBOFuncao'] = $this->servidor->getDadosCargo()->rh37_cbo;
            }
            $this->servidorEntity->setCargoFuncaoSemVinculo($dadoCargoFuncao);
        }
    }

    /**
     * Alteração contratual sem vinculo
     * @throws \DBException
     */
    private function dadosAlteracaoContratualSemVinculo()
    {
        //Condição para o evento eSocial S2306
        if (!$this->servidor->temVinculoEmpregaticio() &&
            $this->servidor->isAtivo()) {
            $registroAlteracao = ServidorAlteracao::findMatriculaByLayout(
                $this->servidor->getMatricula(),
                Tipo::S2306,
                false,
                true
            );
            $dadoAlteracaoContratual = [];
            if ($registroAlteracao) {
                try {
                    $dadoAlteracaoContratual['dtAlteracao'] = $registroAlteracao->getDataS2306()->getDate();
                } catch (\ParameterException $e) {
                    $sMsgErro = $this->getMensagemValidaData('data alteração evento S-2306');
                    throw new \ParameterException($sMsgErro);
                }
            }
            $this->servidorEntity->setAlteracaoContratualSemVinculo($dadoAlteracaoContratual);
        }
    }

    /**
     * Valida a data
     * Retorna uma mensagem amigável quando a data não é válida.
     * @throws \DBException
     */
    private function getMensagemValidaData($mensagem = '', $configuracao = false)
    {
        $matricula = $this->servidor->getMatricula();
        $nome = $this->servidor->getCgm()->getNome();
        $mensagemErro = "O servidor {$matricula} - {$nome} possui data inválida em {$mensagem}. Favor Revisar";
        if ($configuracao) {
            $mensagemErro = $mensagem;
        }
        return $mensagemErro;
    }
}
