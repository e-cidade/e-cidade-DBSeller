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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model;

class Registro10
{
    const TIPO_REGISTRO = '10';
    private $tipoRegistro = self::TIPO_REGISTRO;
    private $codigoInep;
    private $materialEducacaoBilingue;

    /**
     * Local de funcionamento da escola
     */
    private $predioEscolar = 0;
    private $salaOutraEscola = 0;
    private $galpaoRanchoPaiolBarracao = 0;
    private $unidadeAtendimentoSocioeducativa = 0;
    private $unidadePrisional = 0;
    private $outroLocal = 0;

    private $formaOcupacaoPredio;
    private $predioEscolarCompartilhado;

    /**
     * Código da escola com a qual compartilha
     */
    private $codigoEscolaCompartilha1;
    private $codigoEscolaCompartilha2;
    private $codigoEscolaCompartilha3;
    private $codigoEscolaCompartilha4;
    private $codigoEscolaCompartilha5;
    private $codigoEscolaCompartilha6;

    private $forneceAguaPotavel = 0;

    /**
     * Abastecimento de água
     */
    private $aguaRedePublica;
    private $pocoArtesiano;
    private $cacimbaCisternaPoco;
    private $fonteRio;
    private $semAgua;
    private $carroPipa;

    /**
     * Fonte de energia elétrica
     */
    private $luzRedePublica;
    private $geradorCombustivelFossil;
    private $energiaRenovavel;
    private $semEnergiaEletrica;

    /**
     * Esgotamento sanitário
     */
    private $esgotoRedePublica;
    private $fossaSeptica;
    private $fossaRudimentar;
    private $semEsgotamentoSanitario;

    /**
     * Destinação do lixo
     */
    private $servicoColeta;
    private $queimaLixo;
    private $enterraLixo;
    private $levaLixo;
    private $descartaLixo;

    /**
     * Tratamento do lixo/resíduos que a escola realiza
     */
    private $separacaoLixo;
    private $reaproveitamentoLixo;
    private $reciclagemLixo;
    private $naoTrataLixo;

    /**
     * Dependências físicas existentes e utilizadas na escola
     */
    private $almoxarifado;
    private $areaVegetacaoGramado;
    private $auditorio;
    private $banheiro;
    private $banheiroAcessivelPessoasDeficiencia;
    private $banheiroEducacaoInfantil;
    private $banheiroExclusivoFuncionarios;
    private $banheiroComChuveiro;
    private $biblioteca;
    private $cozinha;
    private $despensa;
    private $dormitorioAluno;
    private $dormitorioProfessor;
    private $laboratorioCiencias;
    private $laboratorioInformatica;
    private $laboratorioEducacaoProfissional = 0;
    private $parqueInfantil;
    private $patiocoberto;
    private $patiodescoberto;
    private $piscina;
    private $quadraEsportesCoberta;
    private $quadraEsportesDescoberta;
    private $refeitorio;
    private $salaRepousoAluno;
    private $atelieArtes;
    private $salaMusica;
    private $salaDanca;
    private $salaMultiuso;
    private $terreirao;
    private $viveiro;
    private $salaDiretoria;
    private $salaLeitura;
    private $salaProfessores;
    private $salaRecursosMultifuncionaisAEE;
    private $salaSecretaria;
    private $salaEducacaoProfissional = 0;
    private $salaGravacaoEdicao = 0;
    private $areaHortaPlantioProducaoAgricola;
    private $nenhumaDependencias;

    /**
     * Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de circulação
     * internas na escola
     */
    private $corrimao;
    private $elevador;
    private $pisoTatil;
    private $portasComVao80Cm;
    private $rampas;
    private $sinalizacaoSonora;
    private $sinalizacaoTatil;
    private $sinalizacaoVisual;
    private $nenhumRecursosAcessibilidade;

    private $numeroSalasDentroPredioEscolar;
    private $numeroSalasForaPredioEscolar;
    private $numeroSalasClimatizada;
    private $numeroSalasComAcessibilidade;
    private $sinalizacaoAlarmeLuminoso;

    /**
     * Equipamentos existentes na escola para uso técnico e administrativo
     */
    private $antenaParabolica;
    private $computador;
    private $copiadora;
    private $impressora;
    private $impressoraMultifuncional;
    private $scanner;

    private $nenhumEquipamentosListados = 0;

    /**
     * Quantidade de equipamentos para o processo ensino aprendizagem
     */
    private $aparelhoDVDBluray;
    private $aparelhoSom;
    private $aparelhoTelevisao;
    private $lousaDigital;
    private $projetorMultimidia;

    /**
     * Quantidade de computadores em uso pelos alunos
     */
    private $computadorDesktop;
    private $computadorPortateis;
    private $tablets;

    /**
     * Acesso à internet
     */
    private $internetParaAdministrativo;
    private $internetParaEnsino;
    private $internetParaAluno;
    private $internetParaComunidade;
    private $naoPossuiInternet;

    /**
     * Equipamentos que os aluno(a)s usam para acessar a internet da escola
     */
    private $computadoresDisponiveis;
    private $dispositivosPessoais;

    private $internetBandaLarga;

    /**
     * Rede local de interligação de computadores
     */
    private $redeCabo;
    private $redeWireless;
    private $naoExisteRede;

    /**
     * Total de profissionais que atuam nas seguintes funções na escola
     */
    private $auxiliarSecretariaAdministrativos;
    private $auxiliarServicosGerais;
    private $bibliotecario;
    private $bombeiro;
    private $coordenador;
    private $fonoaudiologo;
    private $nutricionista;
    private $psicologo;
    private $profissionaisPreparacaoSeguraca;
    private $profissionaisApoio;
    private $secretario;
    private $seguranca;
    private $tecnicosMonitores;
    private $gestoresEscola;
    private $orientadorComunitario;
    private $tradutorInterpreteLibras;
    private $agronomosTecnicosResponsaveisProducaoAgricola;
    private $revisorTextoBrailleAssistenteVidente;

    private $naoHaFuncionarios = null;

    private $alimentacaoEscolar;

    /**
     * Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de
     * atividades de ensino aprendizagem
     */
    private $acervoMultimidia;
    private $brinquedosEducacaoInfantil;
    private $materiaisCientificos;
    private $EquipamentoAmplificacaoOuDifusaoAudio;
    private $instrumentosMusicais;
    private $jogosEducativos;
    private $materialAtividadeCultural;
    private $materialEducacaoProfissional = 0;
    private $materialDesportivRecreacao;
    private $materialEducacaoIndigena;
    private $materialEducacaoEtnicoRacial;
    private $materialEducacaoCampo;
    private $materialEducacaoQuilombola;
    private $materialEducacaoEspecial;

    private $nenhumInstrumentoListado = 0;

    private $educacaoEscolarIndigena = 0;
    private $equipamentosAtividadesHortaProducaoAgricola;

    /**
     * Língua em que o ensino é ministrado
     */
    private $linguaIndigena;
    private $linguaPortuguesa;
    private $codigoLinguaIndigena1;
    private $codigoLinguaIndigena2;
    private $codigoLinguaIndigena3;

    private $exameSelecao = 0;

    /**
     * Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s
     */
    private $reservaVagaPretoPardoIndigena;
    private $reservaVagaRenda;
    private $reservaVagaEscolaPublica;
    private $reservaVagaDeficiencia;
    private $reservaVagaOutro;
    private $semReservaVagas;

    private $possuiSiteBlog;
    private $escolaCompartilhaEspacoComunidade;
    private $escolaUsaEquipamentosParaAtividade;

    /**
     * Órgãos colegiados em funcionamento na escola
     */
    private $associacaoPais;
    private $associacaoPaisMestres;
    private $conselhoEscolar;
    private $gremioEstudantil;
    private $orgaosColegiadosOutros;
    private $orgaosColegiadosNenhum;

    private $projetoPedagogicoAtualizado = 0;

    private $desenvolveAcoesEducacaoAmbiental;

    private $comoConteudoComponentesCamposExperiencias;
    private $comoComponenteCurricularEspecialEspecifico;
    private $comoEixoEstruturanteCurriculo;
    private $emEventos;
    private $emProjetosTransversaisInterdisciplinares;
    private $nenhumaDasOpcoesListadas;

    /**
     * @return mixed
     */
    public function getMaterialEducacaoBilingue()
    {
        return $this->materialEducacaoBilingue;
    }

    /**
     * @param mixed $materialEducacaoBilingue
     * @return Registro10
     */
    public function setMaterialEducacaoBilingue($materialEducacaoBilingue)
    {
        $this->materialEducacaoBilingue = $materialEducacaoBilingue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoInep()
    {
        return $this->codigoInep;
    }

    /**
     * @param mixed $codigoInep
     * @return Registro10
     */
    public function setCodigoInep($codigoInep)
    {
        $this->codigoInep = $codigoInep;
        return $this;
    }

    /**
     * @return string
     */
    public function getTipoRegistro()
    {
        return $this->tipoRegistro;
    }

    /**
     * @return mixed
     */
    public function getPredioEscolar()
    {
        return $this->predioEscolar;
    }

    /**
     * @param mixed $predioEscolar
     * @return Registro10
     */
    public function setPredioEscolar($predioEscolar)
    {
        $this->predioEscolar = $predioEscolar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaOutraEscola()
    {
        return $this->salaOutraEscola;
    }

    /**
     * @param mixed $salaOutraEscola
     * @return Registro10
     */
    public function setSalaOutraEscola($salaOutraEscola)
    {
        $this->salaOutraEscola = $salaOutraEscola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGalpaoRanchoPaiolBarracao()
    {
        return $this->galpaoRanchoPaiolBarracao;
    }

    /**
     * @param mixed $galpaoRanchoPaiolBarracao
     * @return Registro10
     */
    public function setGalpaoRanchoPaiolBarracao($galpaoRanchoPaiolBarracao)
    {
        $this->galpaoRanchoPaiolBarracao = $galpaoRanchoPaiolBarracao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnidadeAtendimentoSocioeducativa()
    {
        return $this->unidadeAtendimentoSocioeducativa;
    }

    /**
     * @param mixed $unidadeAtendimentoSocioeducativa
     * @return Registro10
     */
    public function setUnidadeAtendimentoSocioeducativa($unidadeAtendimentoSocioeducativa)
    {
        $this->unidadeAtendimentoSocioeducativa = $unidadeAtendimentoSocioeducativa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnidadePrisional()
    {
        return $this->unidadePrisional;
    }

    /**
     * @param mixed $unidadePrisional
     * @return Registro10
     */
    public function setUnidadePrisional($unidadePrisional)
    {
        $this->unidadePrisional = $unidadePrisional;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutroLocal()
    {
        return $this->outroLocal;
    }

    /**
     * @param mixed $outroLocal
     * @return Registro10
     */
    public function setOutroLocal($outroLocal)
    {
        $this->outroLocal = $outroLocal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormaOcupacaoPredio()
    {
        return $this->formaOcupacaoPredio;
    }

    /**
     * @param mixed $formaOcupacaoPredio
     * @return Registro10
     */
    public function setFormaOcupacaoPredio($formaOcupacaoPredio)
    {
        $this->formaOcupacaoPredio = $formaOcupacaoPredio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPredioEscolarCompartilhado()
    {
        return $this->predioEscolarCompartilhado;
    }

    /**
     * @param mixed $predioEscolarCompartilhado
     * @return Registro10
     */
    public function setPredioEscolarCompartilhado($predioEscolarCompartilhado)
    {
        $this->predioEscolarCompartilhado = $predioEscolarCompartilhado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoEscolaCompartilha1()
    {
        return $this->codigoEscolaCompartilha1;
    }

    /**
     * @param mixed $codigoEscolaCompartilha1
     * @return Registro10
     */
    public function setCodigoEscolaCompartilha1($codigoEscolaCompartilha1)
    {
        $this->codigoEscolaCompartilha1 = $codigoEscolaCompartilha1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoEscolaCompartilha2()
    {
        return $this->codigoEscolaCompartilha2;
    }

    /**
     * @param mixed $codigoEscolaCompartilha2
     * @return Registro10
     */
    public function setCodigoEscolaCompartilha2($codigoEscolaCompartilha2)
    {
        $this->codigoEscolaCompartilha2 = $codigoEscolaCompartilha2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoEscolaCompartilha3()
    {
        return $this->codigoEscolaCompartilha3;
    }

    /**
     * @param mixed $codigoEscolaCompartilha3
     * @return Registro10
     */
    public function setCodigoEscolaCompartilha3($codigoEscolaCompartilha3)
    {
        $this->codigoEscolaCompartilha3 = $codigoEscolaCompartilha3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoEscolaCompartilha4()
    {
        return $this->codigoEscolaCompartilha4;
    }

    /**
     * @param mixed $codigoEscolaCompartilha4
     * @return Registro10
     */
    public function setCodigoEscolaCompartilha4($codigoEscolaCompartilha4)
    {
        $this->codigoEscolaCompartilha4 = $codigoEscolaCompartilha4;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoEscolaCompartilha5()
    {
        return $this->codigoEscolaCompartilha5;
    }

    /**
     * @param mixed $codigoEscolaCompartilha5
     * @return Registro10
     */
    public function setCodigoEscolaCompartilha5($codigoEscolaCompartilha5)
    {
        $this->codigoEscolaCompartilha5 = $codigoEscolaCompartilha5;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoEscolaCompartilha6()
    {
        return $this->codigoEscolaCompartilha6;
    }

    /**
     * @param mixed $codigoEscolaCompartilha6
     * @return Registro10
     */
    public function setCodigoEscolaCompartilha6($codigoEscolaCompartilha6)
    {
        $this->codigoEscolaCompartilha6 = $codigoEscolaCompartilha6;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getForneceAguaPotavel()
    {
        return $this->forneceAguaPotavel;
    }

    /**
     * @param mixed $forneceAguaPotavel
     * @return Registro10
     */
    public function setForneceAguaPotavel($forneceAguaPotavel)
    {
        $this->forneceAguaPotavel = $forneceAguaPotavel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAguaRedePublica()
    {
        return $this->aguaRedePublica;
    }

    /**
     * @param mixed $aguaRedePublica
     * @return Registro10
     */
    public function setAguaRedePublica($aguaRedePublica)
    {
        $this->aguaRedePublica = $aguaRedePublica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPocoArtesiano()
    {
        return $this->pocoArtesiano;
    }

    /**
     * @param mixed $pocoArtesiano
     * @return Registro10
     */
    public function setPocoArtesiano($pocoArtesiano)
    {
        $this->pocoArtesiano = $pocoArtesiano;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCacimbaCisternaPoco()
    {
        return $this->cacimbaCisternaPoco;
    }

    /**
     * @param mixed $cacimbaCisternaPoco
     * @return Registro10
     */
    public function setCacimbaCisternaPoco($cacimbaCisternaPoco)
    {
        $this->cacimbaCisternaPoco = $cacimbaCisternaPoco;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFonteRio()
    {
        return $this->fonteRio;
    }

    /**
     * @param mixed $fonteRio
     * @return Registro10
     */
    public function setFonteRio($fonteRio)
    {
        $this->fonteRio = $fonteRio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSemAgua()
    {
        return $this->semAgua;
    }

    /**
     * @param mixed $semAgua
     * @return Registro10
     */
    public function setSemAgua($semAgua)
    {
        $this->semAgua = $semAgua;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCarroPipa()
    {
        return $this->carroPipa;
    }

    /**
     * @param mixed $carroPipa
     * @return Registro10
     */
    public function setCarroPipa($carroPipa)
    {
        $this->carroPipa = $carroPipa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLuzRedePublica()
    {
        return $this->luzRedePublica;
    }

    /**
     * @param mixed $luzRedePublica
     * @return Registro10
     */
    public function setLuzRedePublica($luzRedePublica)
    {
        $this->luzRedePublica = $luzRedePublica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeradorCombustivelFossil()
    {
        return $this->geradorCombustivelFossil;
    }

    /**
     * @param mixed $geradorCombustivelFossil
     * @return Registro10
     */
    public function setGeradorCombustivelFossil($geradorCombustivelFossil)
    {
        $this->geradorCombustivelFossil = $geradorCombustivelFossil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnergiaRenovavel()
    {
        return $this->energiaRenovavel;
    }

    /**
     * @param mixed $energiaRenovavel
     * @return Registro10
     */
    public function setEnergiaRenovavel($energiaRenovavel)
    {
        $this->energiaRenovavel = $energiaRenovavel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSemEnergiaEletrica()
    {
        return $this->semEnergiaEletrica;
    }

    /**
     * @param mixed $semEnergiaEletrica
     * @return Registro10
     */
    public function setSemEnergiaEletrica($semEnergiaEletrica)
    {
        $this->semEnergiaEletrica = $semEnergiaEletrica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEsgotoRedePublica()
    {
        return $this->esgotoRedePublica;
    }

    /**
     * @param mixed $esgotoRedePublica
     * @return Registro10
     */
    public function setEsgotoRedePublica($esgotoRedePublica)
    {
        $this->esgotoRedePublica = $esgotoRedePublica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFossaSeptica()
    {
        return $this->fossaSeptica;
    }

    /**
     * @param mixed $fossaSeptica
     * @return Registro10
     */
    public function setFossaSeptica($fossaSeptica)
    {
        $this->fossaSeptica = $fossaSeptica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFossaRudimentar()
    {
        return $this->fossaRudimentar;
    }

    /**
     * @param mixed $fossaRudimentar
     * @return Registro10
     */
    public function setFossaRudimentar($fossaRudimentar)
    {
        $this->fossaRudimentar = $fossaRudimentar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSemEsgotamentoSanitario()
    {
        return $this->semEsgotamentoSanitario;
    }

    /**
     * @param mixed $semEsgotamentoSanitario
     * @return Registro10
     */
    public function setSemEsgotamentoSanitario($semEsgotamentoSanitario)
    {
        $this->semEsgotamentoSanitario = $semEsgotamentoSanitario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServicoColeta()
    {
        return $this->servicoColeta;
    }

    /**
     * @param mixed $servicoColeta
     * @return Registro10
     */
    public function setServicoColeta($servicoColeta)
    {
        $this->servicoColeta = $servicoColeta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQueimaLixo()
    {
        return $this->queimaLixo;
    }

    /**
     * @param mixed $queimaLixo
     * @return Registro10
     */
    public function setQueimaLixo($queimaLixo)
    {
        $this->queimaLixo = $queimaLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnterraLixo()
    {
        return $this->enterraLixo;
    }

    /**
     * @param mixed $enterraLixo
     * @return Registro10
     */
    public function setEnterraLixo($enterraLixo)
    {
        $this->enterraLixo = $enterraLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevaLixo()
    {
        return $this->levaLixo;
    }

    /**
     * @param mixed $levaLixo
     * @return Registro10
     */
    public function setLevaLixo($levaLixo)
    {
        $this->levaLixo = $levaLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescartaLixo()
    {
        return $this->descartaLixo;
    }

    /**
     * @param mixed $descartaLixo
     * @return Registro10
     */
    public function setDescartaLixo($descartaLixo)
    {
        $this->descartaLixo = $descartaLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeparacaoLixo()
    {
        return $this->separacaoLixo;
    }

    /**
     * @param mixed $separacaoLixo
     * @return Registro10
     */
    public function setSeparacaoLixo($separacaoLixo)
    {
        $this->separacaoLixo = $separacaoLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReaproveitamentoLixo()
    {
        return $this->reaproveitamentoLixo;
    }

    /**
     * @param mixed $reaproveitamentoLixo
     * @return Registro10
     */
    public function setReaproveitamentoLixo($reaproveitamentoLixo)
    {
        $this->reaproveitamentoLixo = $reaproveitamentoLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReciclagemLixo()
    {
        return $this->reciclagemLixo;
    }

    /**
     * @param mixed $reciclagemLixo
     * @return Registro10
     */
    public function setReciclagemLixo($reciclagemLixo)
    {
        $this->reciclagemLixo = $reciclagemLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNaoTrataLixo()
    {
        return $this->naoTrataLixo;
    }

    /**
     * @param mixed $naoTrataLixo
     * @return Registro10
     */
    public function setNaoTrataLixo($naoTrataLixo)
    {
        $this->naoTrataLixo = $naoTrataLixo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlmoxarifado()
    {
        return $this->almoxarifado;
    }

    /**
     * @param mixed $almoxarifado
     * @return Registro10
     */
    public function setAlmoxarifado($almoxarifado)
    {
        $this->almoxarifado = $almoxarifado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAreaVegetacaoGramado()
    {
        return $this->areaVegetacaoGramado;
    }

    /**
     * @param mixed $areaVegetacaoGramado
     * @return Registro10
     */
    public function setAreaVegetacaoGramado($areaVegetacaoGramado)
    {
        $this->areaVegetacaoGramado = $areaVegetacaoGramado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuditorio()
    {
        return $this->auditorio;
    }

    /**
     * @param mixed $auditorio
     * @return Registro10
     */
    public function setAuditorio($auditorio)
    {
        $this->auditorio = $auditorio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanheiro()
    {
        return $this->banheiro;
    }

    /**
     * @param mixed $banheiro
     * @return Registro10
     */
    public function setBanheiro($banheiro)
    {
        $this->banheiro = $banheiro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanheiroAcessivelPessoasDeficiencia()
    {
        return $this->banheiroAcessivelPessoasDeficiencia;
    }

    /**
     * @param mixed $banheiroAcessivelPessoasDeficiencia
     * @return Registro10
     */
    public function setBanheiroAcessivelPessoasDeficiencia($banheiroAcessivelPessoasDeficiencia)
    {
        $this->banheiroAcessivelPessoasDeficiencia = $banheiroAcessivelPessoasDeficiencia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanheiroEducacaoInfantil()
    {
        return $this->banheiroEducacaoInfantil;
    }

    /**
     * @param mixed $banheiroEducacaoInfantil
     * @return Registro10
     */
    public function setBanheiroEducacaoInfantil($banheiroEducacaoInfantil)
    {
        $this->banheiroEducacaoInfantil = $banheiroEducacaoInfantil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanheiroExclusivoFuncionarios()
    {
        return $this->banheiroExclusivoFuncionarios;
    }

    /**
     * @param mixed $banheiroExclusivoFuncionarios
     * @return Registro10
     */
    public function setBanheiroExclusivoFuncionarios($banheiroExclusivoFuncionarios)
    {
        $this->banheiroExclusivoFuncionarios = $banheiroExclusivoFuncionarios;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanheiroComChuveiro()
    {
        return $this->banheiroComChuveiro;
    }

    /**
     * @param mixed $banheiroComChuveiro
     * @return Registro10
     */
    public function setBanheiroComChuveiro($banheiroComChuveiro)
    {
        $this->banheiroComChuveiro = $banheiroComChuveiro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBiblioteca()
    {
        return $this->biblioteca;
    }

    /**
     * @param mixed $biblioteca
     * @return Registro10
     */
    public function setBiblioteca($biblioteca)
    {
        $this->biblioteca = $biblioteca;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCozinha()
    {
        return $this->cozinha;
    }

    /**
     * @param mixed $cozinha
     * @return Registro10
     */
    public function setCozinha($cozinha)
    {
        $this->cozinha = $cozinha;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDespensa()
    {
        return $this->despensa;
    }

    /**
     * @param mixed $despensa
     * @return Registro10
     */
    public function setDespensa($despensa)
    {
        $this->despensa = $despensa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDormitorioAluno()
    {
        return $this->dormitorioAluno;
    }

    /**
     * @param mixed $dormitorioAluno
     * @return Registro10
     */
    public function setDormitorioAluno($dormitorioAluno)
    {
        $this->dormitorioAluno = $dormitorioAluno;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDormitorioProfessor()
    {
        return $this->dormitorioProfessor;
    }

    /**
     * @param mixed $dormitorioProfessor
     * @return Registro10
     */
    public function setDormitorioProfessor($dormitorioProfessor)
    {
        $this->dormitorioProfessor = $dormitorioProfessor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLaboratorioCiencias()
    {
        return $this->laboratorioCiencias;
    }

    /**
     * @param mixed $laboratorioCiencias
     * @return Registro10
     */
    public function setLaboratorioCiencias($laboratorioCiencias)
    {
        $this->laboratorioCiencias = $laboratorioCiencias;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLaboratorioInformatica()
    {
        return $this->laboratorioInformatica;
    }

    /**
     * @param mixed $laboratorioInformatica
     * @return Registro10
     */
    public function setLaboratorioInformatica($laboratorioInformatica)
    {
        $this->laboratorioInformatica = $laboratorioInformatica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getlaboratorioEducacaoProfissional()
    {
        return $this->laboratorioEducacaoProfissional;
    }

    /**
     * @param mixed $laboratorioEducacaoProfissional
     * @return Registro10
     */
    public function setlaboratorioEducacaoProfissional($laboratorioEducacaoProfissional)
    {
        $this->laboratorioEducacaoProfissional = $laboratorioEducacaoProfissional;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParqueInfantil()
    {
        return $this->parqueInfantil;
    }

    /**
     * @param mixed $parqueInfantil
     * @return Registro10
     */
    public function setParqueInfantil($parqueInfantil)
    {
        $this->parqueInfantil = $parqueInfantil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatiocoberto()
    {
        return $this->patiocoberto;
    }

    /**
     * @param mixed $patiocoberto
     * @return Registro10
     */
    public function setPatiocoberto($patiocoberto)
    {
        $this->patiocoberto = $patiocoberto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatiodescoberto()
    {
        return $this->patiodescoberto;
    }

    /**
     * @param mixed $patiodescoberto
     * @return Registro10
     */
    public function setPatiodescoberto($patiodescoberto)
    {
        $this->patiodescoberto = $patiodescoberto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPiscina()
    {
        return $this->piscina;
    }

    /**
     * @param mixed $piscina
     * @return Registro10
     */
    public function setPiscina($piscina)
    {
        $this->piscina = $piscina;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuadraEsportesCoberta()
    {
        return $this->quadraEsportesCoberta;
    }

    /**
     * @param mixed $quadraEsportesCoberta
     * @return Registro10
     */
    public function setQuadraEsportesCoberta($quadraEsportesCoberta)
    {
        $this->quadraEsportesCoberta = $quadraEsportesCoberta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuadraEsportesDescoberta()
    {
        return $this->quadraEsportesDescoberta;
    }

    /**
     * @param mixed $quadraEsportesDescoberta
     * @return Registro10
     */
    public function setQuadraEsportesDescoberta($quadraEsportesDescoberta)
    {
        $this->quadraEsportesDescoberta = $quadraEsportesDescoberta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefeitorio()
    {
        return $this->refeitorio;
    }

    /**
     * @param mixed $refeitorio
     * @return Registro10
     */
    public function setRefeitorio($refeitorio)
    {
        $this->refeitorio = $refeitorio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaRepousoAluno()
    {
        return $this->salaRepousoAluno;
    }

    /**
     * @param mixed $salaRepousoAluno
     * @return Registro10
     */
    public function setSalaRepousoAluno($salaRepousoAluno)
    {
        $this->salaRepousoAluno = $salaRepousoAluno;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAtelieArtes()
    {
        return $this->atelieArtes;
    }

    /**
     * @param mixed $atelieArtes
     * @return Registro10
     */
    public function setAtelieArtes($atelieArtes)
    {
        $this->atelieArtes = $atelieArtes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaMusica()
    {
        return $this->salaMusica;
    }

    /**
     * @param mixed $salaMusica
     * @return Registro10
     */
    public function setSalaMusica($salaMusica)
    {
        $this->salaMusica = $salaMusica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaDanca()
    {
        return $this->salaDanca;
    }

    /**
     * @param mixed $salaDanca
     * @return Registro10
     */
    public function setSalaDanca($salaDanca)
    {
        $this->salaDanca = $salaDanca;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaMultiuso()
    {
        return $this->salaMultiuso;
    }

    /**
     * @param mixed $salaMultiuso
     * @return Registro10
     */
    public function setSalaMultiuso($salaMultiuso)
    {
        $this->salaMultiuso = $salaMultiuso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTerreirao()
    {
        return $this->terreirao;
    }

    /**
     * @param mixed $terreirao
     * @return Registro10
     */
    public function setTerreirao($terreirao)
    {
        $this->terreirao = $terreirao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getViveiro()
    {
        return $this->viveiro;
    }

    /**
     * @param mixed $viveiro
     * @return Registro10
     */
    public function setViveiro($viveiro)
    {
        $this->viveiro = $viveiro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaDiretoria()
    {
        return $this->salaDiretoria;
    }

    /**
     * @param mixed $salaDiretoria
     * @return Registro10
     */
    public function setSalaDiretoria($salaDiretoria)
    {
        $this->salaDiretoria = $salaDiretoria;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaLeitura()
    {
        return $this->salaLeitura;
    }

    /**
     * @param mixed $salaLeitura
     * @return Registro10
     */
    public function setSalaLeitura($salaLeitura)
    {
        $this->salaLeitura = $salaLeitura;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaProfessores()
    {
        return $this->salaProfessores;
    }

    /**
     * @param mixed $salaProfessores
     * @return Registro10
     */
    public function setSalaProfessores($salaProfessores)
    {
        $this->salaProfessores = $salaProfessores;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaRecursosMultifuncionaisAEE()
    {
        return $this->salaRecursosMultifuncionaisAEE;
    }

    /**
     * @param mixed $salaRecursosMultifuncionaisAEE
     * @return Registro10
     */
    public function setSalaRecursosMultifuncionaisAEE($salaRecursosMultifuncionaisAEE)
    {
        $this->salaRecursosMultifuncionaisAEE = $salaRecursosMultifuncionaisAEE;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaSecretaria()
    {
        return $this->salaSecretaria;
    }

    /**
     * @param mixed $salaSecretaria
     * @return Registro10
     */
    public function setSalaSecretaria($salaSecretaria)
    {
        $this->salaSecretaria = $salaSecretaria;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalaEducacaoProfissional()
    {
        return $this->salaEducacaoProfissional;
    }
    public function getSalaGravacaoEdicao()
    {
        return $this->salaGravacaoEdicao;
    }

    /**
     * @param mixed $salaEducacaoProfissional
     * @return Registro10
     */
    public function setSalaEducacaoProfissional($salaEducacaoProfissional)
    {
        $this->salaEducacaoProfissional = $salaEducacaoProfissional;
        return $this;
    }

    public function setSalaGravacaoEdicao($salaGravacaoEdicao)
    {
        $this->salaGravacaoEdicao = $salaGravacaoEdicao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNenhumaDependencias()
    {
        return $this->nenhumaDependencias;
    }

    /**
     * @param mixed $nenhumaDependencias
     * @return Registro10
     */
    public function setNenhumaDependencias($nenhumaDependencias)
    {
        $this->nenhumaDependencias = $nenhumaDependencias;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCorrimao()
    {
        return $this->corrimao;
    }

    /**
     * @param mixed $corrimao
     * @return Registro10
     */
    public function setCorrimao($corrimao)
    {
        $this->corrimao = $corrimao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getElevador()
    {
        return $this->elevador;
    }

    /**
     * @param mixed $elevador
     * @return Registro10
     */
    public function setElevador($elevador)
    {
        $this->elevador = $elevador;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPisoTatil()
    {
        return $this->pisoTatil;
    }

    /**
     * @param mixed $pisoTatil
     * @return Registro10
     */
    public function setPisoTatil($pisoTatil)
    {
        $this->pisoTatil = $pisoTatil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPortasComVao80Cm()
    {
        return $this->portasComVao80Cm;
    }

    /**
     * @param mixed $portasComVao80Cm
     * @return Registro10
     */
    public function setPortasComVao80Cm($portasComVao80Cm)
    {
        $this->portasComVao80Cm = $portasComVao80Cm;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRampas()
    {
        return $this->rampas;
    }

    /**
     * @param mixed $rampas
     * @return Registro10
     */
    public function setRampas($rampas)
    {
        $this->rampas = $rampas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSinalizacaoSonora()
    {
        return $this->sinalizacaoSonora;
    }

    /**
     * @param mixed $sinalizacaoSonora
     * @return Registro10
     */
    public function setSinalizacaoSonora($sinalizacaoSonora)
    {
        $this->sinalizacaoSonora = $sinalizacaoSonora;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSinalizacaoTatil()
    {
        return $this->sinalizacaoTatil;
    }

    /**
     * @param mixed $sinalizacaoTatil
     * @return Registro10
     */
    public function setSinalizacaoTatil($sinalizacaoTatil)
    {
        $this->sinalizacaoTatil = $sinalizacaoTatil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSinalizacaoVisual()
    {
        return $this->sinalizacaoVisual;
    }

    /**
     * @param mixed $sinalizacaoVisual
     * @return Registro10
     */
    public function setSinalizacaoVisual($sinalizacaoVisual)
    {
        $this->sinalizacaoVisual = $sinalizacaoVisual;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNenhumRecursosAcessibilidade()
    {
        return $this->nenhumRecursosAcessibilidade;
    }

    /**
     * @param mixed $nenhumRecursosAcessibilidade
     * @return Registro10
     */
    public function setNenhumRecursosAcessibilidade($nenhumRecursosAcessibilidade)
    {
        $this->nenhumRecursosAcessibilidade = $nenhumRecursosAcessibilidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumeroSalasDentroPredioEscolar()
    {
        return $this->numeroSalasDentroPredioEscolar;
    }

    /**
     * @param mixed $numeroSalasDentroPredioEscolar
     * @return Registro10
     */
    public function setNumeroSalasDentroPredioEscolar($numeroSalasDentroPredioEscolar)
    {
        $this->numeroSalasDentroPredioEscolar = $numeroSalasDentroPredioEscolar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumeroSalasForaPredioEscolar()
    {
        return $this->numeroSalasForaPredioEscolar;
    }

    /**
     * @param mixed $numeroSalasForaPredioEscolar
     * @return Registro10
     */
    public function setNumeroSalasForaPredioEscolar($numeroSalasForaPredioEscolar)
    {
        $this->numeroSalasForaPredioEscolar = $numeroSalasForaPredioEscolar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumeroSalasClimatizada()
    {
        return $this->numeroSalasClimatizada;
    }

    /**
     * @param mixed $numeroSalasClimatizada
     * @return Registro10
     */
    public function setNumeroSalasClimatizada($numeroSalasClimatizada)
    {
        $this->numeroSalasClimatizada = $numeroSalasClimatizada;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumeroSalasComAcessibilidade()
    {
        return $this->numeroSalasComAcessibilidade;
    }

    /**
     * @param mixed $numeroSalasComAcessibilidade
     * @return Registro10
     */
    public function setNumeroSalasComAcessibilidade($numeroSalasComAcessibilidade)
    {
        $this->numeroSalasComAcessibilidade = $numeroSalasComAcessibilidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSinalizacaoAlarmeLuminoso()
    {
        return $this->sinalizacaoAlarmeLuminoso;
    }

    /**
     * @param mixed $sinalizacaoAlarmeLuminoso
     * @return Registro10
     */
    public function setSinalizacaoAlarmeLuminoso($sinalizacaoAlarmeLuminoso)
    {
        $this->sinalizacaoAlarmeLuminoso = $sinalizacaoAlarmeLuminoso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAntenaParabolica()
    {
        return $this->antenaParabolica;
    }

    /**
     * @param mixed $antenaParabolica
     * @return Registro10
     */
    public function setAntenaParabolica($antenaParabolica)
    {
        $this->antenaParabolica = $antenaParabolica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComputador()
    {
        return $this->computador;
    }

    /**
     * @param mixed $computador
     * @return Registro10
     */
    public function setComputador($computador)
    {
        $this->computador = $computador;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCopiadora()
    {
        return $this->copiadora;
    }

    /**
     * @param mixed $copiadora
     * @return Registro10
     */
    public function setCopiadora($copiadora)
    {
        $this->copiadora = $copiadora;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpressora()
    {
        return $this->impressora;
    }

    /**
     * @param mixed $impressora
     * @return Registro10
     */
    public function setImpressora($impressora)
    {
        $this->impressora = $impressora;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpressoraMultifuncional()
    {
        return $this->impressoraMultifuncional;
    }

    /**
     * @param mixed $impressoraMultifuncional
     * @return Registro10
     */
    public function setImpressoraMultifuncional($impressoraMultifuncional)
    {
        $this->impressoraMultifuncional = $impressoraMultifuncional;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScanner()
    {
        return $this->scanner;
    }

    /**
     * @param mixed $scanner
     * @return Registro10
     */
    public function setScanner($scanner)
    {
        $this->scanner = $scanner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNenhumEquipamentosListados()
    {
        return $this->nenhumEquipamentosListados;
    }

    /**
     * @param mixed $nenhumEquipamentosListados
     * @return Registro10
     */
    public function setNenhumEquipamentosListados($nenhumEquipamentosListados)
    {
        $this->nenhumEquipamentosListados = $nenhumEquipamentosListados;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getAparelhoDVDBluray()
    {
        return $this->aparelhoDVDBluray;
    }

    /**
     * @param mixed $aparelhoDVDBluray
     * @return Registro10
     */
    public function setAparelhoDVDBluray($aparelhoDVDBluray)
    {
        $this->aparelhoDVDBluray = $aparelhoDVDBluray;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAparelhoSom()
    {
        return $this->aparelhoSom;
    }

    /**
     * @param mixed $aparelhoSom
     * @return Registro10
     */
    public function setAparelhoSom($aparelhoSom)
    {
        $this->aparelhoSom = $aparelhoSom;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAparelhoTelevisao()
    {
        return $this->aparelhoTelevisao;
    }

    /**
     * @param mixed $aparelhoTelevisao
     * @return Registro10
     */
    public function setAparelhoTelevisao($aparelhoTelevisao)
    {
        $this->aparelhoTelevisao = $aparelhoTelevisao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLousaDigital()
    {
        return $this->lousaDigital;
    }

    /**
     * @param mixed $lousaDigital
     * @return Registro10
     */
    public function setLousaDigital($lousaDigital)
    {
        $this->lousaDigital = $lousaDigital;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProjetorMultimidia()
    {
        return $this->projetorMultimidia;
    }

    /**
     * @param mixed $projetorMultimidia
     * @return Registro10
     */
    public function setProjetorMultimidia($projetorMultimidia)
    {
        $this->projetorMultimidia = $projetorMultimidia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComputadorDesktop()
    {
        return $this->computadorDesktop;
    }

    /**
     * @param mixed $computadorDesktop
     * @return Registro10
     */
    public function setComputadorDesktop($computadorDesktop)
    {
        $this->computadorDesktop = $computadorDesktop;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComputadorPortateis()
    {
        return $this->computadorPortateis;
    }

    /**
     * @param mixed $computadorPortateis
     * @return Registro10
     */
    public function setComputadorPortateis($computadorPortateis)
    {
        $this->computadorPortateis = $computadorPortateis;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTablets()
    {
        return $this->tablets;
    }

    /**
     * @param mixed $tablets
     * @return Registro10
     */
    public function setTablets($tablets)
    {
        $this->tablets = $tablets;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternetParaAdministrativo()
    {
        return $this->internetParaAdministrativo;
    }

    /**
     * @param mixed $internetParaAdministrativo
     * @return Registro10
     */
    public function setInternetParaAdministrativo($internetParaAdministrativo)
    {
        $this->internetParaAdministrativo = $internetParaAdministrativo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternetParaEnsino()
    {
        return $this->internetParaEnsino;
    }

    /**
     * @param mixed $internetParaEnsino
     * @return Registro10
     */
    public function setInternetParaEnsino($internetParaEnsino)
    {
        $this->internetParaEnsino = $internetParaEnsino;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternetParaAluno()
    {
        return $this->internetParaAluno;
    }

    /**
     * @param mixed $internetParaAluno
     * @return Registro10
     */
    public function setInternetParaAluno($internetParaAluno)
    {
        $this->internetParaAluno = $internetParaAluno;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternetParaComunidade()
    {
        return $this->internetParaComunidade;
    }

    /**
     * @param mixed $internetParaComunidade
     * @return Registro10
     */
    public function setInternetParaComunidade($internetParaComunidade)
    {
        $this->internetParaComunidade = $internetParaComunidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNaoPossuiInternet()
    {
        return $this->naoPossuiInternet;
    }

    /**
     * @param mixed $naoPossuiInternet
     * @return Registro10
     */
    public function setNaoPossuiInternet($naoPossuiInternet)
    {
        $this->naoPossuiInternet = $naoPossuiInternet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComputadoresDisponiveis()
    {
        return $this->computadoresDisponiveis;
    }

    /**
     * @param mixed $computadoresDisponiveis
     * @return Registro10
     */
    public function setComputadoresDisponiveis($computadoresDisponiveis)
    {
        $this->computadoresDisponiveis = $computadoresDisponiveis;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDispositivosPessoais()
    {
        return $this->dispositivosPessoais;
    }

    /**
     * @param mixed $dispositivosPessoais
     * @return Registro10
     */
    public function setDispositivosPessoais($dispositivosPessoais)
    {
        $this->dispositivosPessoais = $dispositivosPessoais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternetBandaLarga()
    {
        return $this->internetBandaLarga;
    }

    /**
     * @param mixed $internetBandaLarga
     * @return Registro10
     */
    public function setInternetBandaLarga($internetBandaLarga)
    {
        $this->internetBandaLarga = $internetBandaLarga;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRedeCabo()
    {
        return $this->redeCabo;
    }

    /**
     * @param mixed $redeCabo
     * @return Registro10
     */
    public function setRedeCabo($redeCabo)
    {
        $this->redeCabo = $redeCabo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRedeWireless()
    {
        return $this->redeWireless;
    }

    /**
     * @param mixed $redeWireless
     * @return Registro10
     */
    public function setRedeWireless($redeWireless)
    {
        $this->redeWireless = $redeWireless;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNaoExisteRede()
    {
        return $this->naoExisteRede;
    }

    /**
     * @param mixed $naoExisteRede
     * @return Registro10
     */
    public function setNaoExisteRede($naoExisteRede)
    {
        $this->naoExisteRede = $naoExisteRede;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuxiliarSecretariaAdministrativos()
    {
        return $this->auxiliarSecretariaAdministrativos;
    }

    /**
     * @param mixed $auxiliarSecretariaAdministrativos
     * @return Registro10
     */
    public function setAuxiliarSecretariaAdministrativos($auxiliarSecretariaAdministrativos)
    {
        $this->auxiliarSecretariaAdministrativos = $auxiliarSecretariaAdministrativos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuxiliarServicosGerais()
    {
        return $this->auxiliarServicosGerais;
    }

    /**
     * @param mixed $auxiliarServicosGerais
     * @return Registro10
     */
    public function setAuxiliarServicosGerais($auxiliarServicosGerais)
    {
        $this->auxiliarServicosGerais = $auxiliarServicosGerais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBibliotecario()
    {
        return $this->bibliotecario;
    }

    /**
     * @param mixed $bibliotecario
     * @return Registro10
     */
    public function setBibliotecario($bibliotecario)
    {
        $this->bibliotecario = $bibliotecario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBombeiro()
    {
        return $this->bombeiro;
    }

    /**
     * @param mixed $bombeiro
     * @return Registro10
     */
    public function setBombeiro($bombeiro)
    {
        $this->bombeiro = $bombeiro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoordenador()
    {
        return $this->coordenador;
    }

    /**
     * @param mixed $coordenador
     * @return Registro10
     */
    public function setCoordenador($coordenador)
    {
        $this->coordenador = $coordenador;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFonoaudiologo()
    {
        return $this->fonoaudiologo;
    }

    /**
     * @param mixed $fonoaudiologo
     * @return Registro10
     */
    public function setFonoaudiologo($fonoaudiologo)
    {
        $this->fonoaudiologo = $fonoaudiologo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNutricionista()
    {
        return $this->nutricionista;
    }

    /**
     * @param mixed $nutricionista
     * @return Registro10
     */
    public function setNutricionista($nutricionista)
    {
        $this->nutricionista = $nutricionista;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPsicologo()
    {
        return $this->psicologo;
    }

    /**
     * @param mixed $psicologo
     * @return Registro10
     */
    public function setPsicologo($psicologo)
    {
        $this->psicologo = $psicologo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfissionaisPreparacaoSeguraca()
    {
        return $this->profissionaisPreparacaoSeguraca;
    }

    /**
     * @param mixed $profissionaisPreparacaoSeguraca
     * @return Registro10
     */
    public function setProfissionaisPreparacaoSeguraca($profissionaisPreparacaoSeguraca)
    {
        $this->profissionaisPreparacaoSeguraca = $profissionaisPreparacaoSeguraca;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfissionaisApoio()
    {
        return $this->profissionaisApoio;
    }

    /**
     * @param mixed $profissionaisApoio
     * @return Registro10
     */
    public function setProfissionaisApoio($profissionaisApoio)
    {
        $this->profissionaisApoio = $profissionaisApoio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecretario()
    {
        return $this->secretario;
    }

    /**
     * @param mixed $secretario
     * @return Registro10
     */
    public function setSecretario($secretario)
    {
        $this->secretario = $secretario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeguranca()
    {
        return $this->seguranca;
    }

    /**
     * @param mixed $seguranca
     * @return Registro10
     */
    public function setSeguranca($seguranca)
    {
        $this->seguranca = $seguranca;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTecnicosMonitores()
    {
        return $this->tecnicosMonitores;
    }

    /**
     * @param mixed $tecnicosMonitores
     * @return Registro10
     */
    public function setTecnicosMonitores($tecnicosMonitores)
    {
        $this->tecnicosMonitores = $tecnicosMonitores;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlimentacaoEscolar()
    {
        return $this->alimentacaoEscolar;
    }

    /**
     * @param mixed $alimentacaoEscolar
     * @return Registro10
     */
    public function setAlimentacaoEscolar($alimentacaoEscolar)
    {
        $this->alimentacaoEscolar = $alimentacaoEscolar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcervoMultimidia()
    {
        return $this->acervoMultimidia;
    }

    /**
     * @param mixed $acervoMultimidia
     * @return Registro10
     */
    public function setAcervoMultimidia($acervoMultimidia)
    {
        $this->acervoMultimidia = $acervoMultimidia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrinquedosEducacaoInfantil()
    {
        return $this->brinquedosEducacaoInfantil;
    }

    /**
     * @param mixed $brinquedosEducacaoInfantil
     * @return Registro10
     */
    public function setBrinquedosEducacaoInfantil($brinquedosEducacaoInfantil)
    {
        $this->brinquedosEducacaoInfantil = $brinquedosEducacaoInfantil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMateriaisCientificos()
    {
        return $this->materiaisCientificos;
    }

    /**
     * @param mixed $materiaisCientificos
     * @return Registro10
     */
    public function setMateriaisCientificos($materiaisCientificos)
    {
        $this->materiaisCientificos = $materiaisCientificos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEquipamentoAmplificacaoOuDifusaoAudio()
    {
        return $this->EquipamentoAmplificacaoOuDifusaoAudio;
    }

    /**
     * @param mixed $EquipamentoAmplificacaoOuDifusaoAudio
     * @return Registro10
     */
    public function setEquipamentoAmplificacaoOuDifusaoAudio($EquipamentoAmplificacaoOuDifusaoAudio)
    {
        $this->EquipamentoAmplificacaoOuDifusaoAudio = $EquipamentoAmplificacaoOuDifusaoAudio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstrumentosMusicais()
    {
        return $this->instrumentosMusicais;
    }

    /**
     * @param mixed $instrumentosMusicais
     * @return Registro10
     */
    public function setInstrumentosMusicais($instrumentosMusicais)
    {
        $this->instrumentosMusicais = $instrumentosMusicais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJogosEducativos()
    {
        return $this->jogosEducativos;
    }

    /**
     * @param mixed $jogosEducativos
     * @return Registro10
     */
    public function setJogosEducativos($jogosEducativos)
    {
        $this->jogosEducativos = $jogosEducativos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaterialAtividadeCultural()
    {
        return $this->materialAtividadeCultural;
    }

    /**
     * @param mixed $materialAtividadeCultural
     * @return Registro10
     */
    public function setMaterialAtividadeCultural($materialAtividadeCultural)
    {
        $this->materialAtividadeCultural = $materialAtividadeCultural;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getmaterialEducacaoProfissional()
    {
        return $this->materialEducacaoProfissional;
    }

    /**
     * @param mixed $materialEducacaoProfissional
     * @return Registro10
     */
    public function setmaterialEducacaoProfissional($materialEducacaoProfissional)
    {
        $this->materialEducacaoProfissional = $materialEducacaoProfissional;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaterialDesportivRecreacao()
    {
        return $this->materialDesportivRecreacao;
    }

    /**
     * @param mixed $materialDesportivRecreacao
     * @return Registro10
     */
    public function setMaterialDesportivRecreacao($materialDesportivRecreacao)
    {
        $this->materialDesportivRecreacao = $materialDesportivRecreacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaterialEducacaoIndigena()
    {
        return $this->materialEducacaoIndigena;
    }

    /**
     * @param mixed $materialEducacaoIndigena
     * @return Registro10
     */
    public function setMaterialEducacaoIndigena($materialEducacaoIndigena)
    {
        $this->materialEducacaoIndigena = $materialEducacaoIndigena;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaterialEducacaoEtnicoRacial()
    {
        return $this->materialEducacaoEtnicoRacial;
    }

    /**
     * @param mixed $materialEducacaoEtnicoRacial
     * @return Registro10
     */
    public function setMaterialEducacaoEtnicoRacial($materialEducacaoEtnicoRacial)
    {
        $this->materialEducacaoEtnicoRacial = $materialEducacaoEtnicoRacial;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaterialEducacaoCampo()
    {
        return $this->materialEducacaoCampo;
    }

    /**
     * @param mixed $materialEducacaoCampo
     * @return Registro10
     */
    public function setMaterialEducacaoCampo($materialEducacaoCampo)
    {
        $this->materialEducacaoCampo = $materialEducacaoCampo;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getMaterialEducacaoQuilombola()
    {
        return $this->materialEducacaoQuilombola;
    }

    /**
     * @param mixed $materialEducacaoQuilombola
     * @return Registro10
     */
    public function setMaterialEducacaoQuilombola($materialEducacaoQuilombola)
    {
        $this->materialEducacaoQuilombola = $materialEducacaoQuilombola;
        return $this;
    }

     /**
     * @return mixed
     */
    public function getMaterialEducacaoEspecial()
    {
        return $this->materialEducacaoEspecial;
    }

    /**
     * @param mixed $materialEducacaoEspecial
     * @return Registro10
     */
    public function setMaterialEducacaoEspecial($materialEducacaoEspecial)
    {
        $this->materialEducacaoEspecial = $materialEducacaoEspecial;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getNenhumInstrumentoListado()
    {
        return $this->nenhumInstrumentoListado;
    }

    /**
     * @param mixed $nenhumInstrumentoListado
     * @return Registro10
     */
    public function setNenhumInstrumentoListado($nenhumInstrumentoListado)
    {
        $this->nenhumInstrumentoListado = $nenhumInstrumentoListado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoEscolarIndigena()
    {
        return $this->educacaoEscolarIndigena;
    }

    /**
     * @param mixed $educacaoEscolarIndigena
     * @return Registro10
     */
    public function setEducacaoEscolarIndigena($educacaoEscolarIndigena)
    {
        $this->educacaoEscolarIndigena = $educacaoEscolarIndigena;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEquipamentosAtividadesHortaProducaoAgricola()
    {
        return $this->equipamentosAtividadesHortaProducaoAgricola;
    }

    /**
     * @param mixed $equipamentosAtividadesHortaProducaoAgricola
     * @return Registro10
     */
    public function setEquipamentosAtividadesHortaProducaoAgricola($equipamentosAtividadesHortaProducaoAgricola)
    {
        $this->equipamentosAtividadesHortaProducaoAgricola = $equipamentosAtividadesHortaProducaoAgricola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinguaIndigena()
    {
        return $this->linguaIndigena;
    }

    /**
     * @param mixed $linguaIndigena
     * @return Registro10
     */
    public function setLinguaIndigena($linguaIndigena)
    {
        $this->linguaIndigena = (int) $linguaIndigena;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinguaPortuguesa()
    {
        return $this->linguaPortuguesa;
    }

    /**
     * @param mixed $linguaPortuguesa
     * @return Registro10
     */
    public function setLinguaPortuguesa($linguaPortuguesa)
    {
        $this->linguaPortuguesa = (int) $linguaPortuguesa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoLinguaIndigena1()
    {
        return $this->codigoLinguaIndigena1;
    }

    /**
     * @param mixed $codigoLinguaIndigena1
     * @return Registro10
     */
    public function setCodigoLinguaIndigena1($codigoLinguaIndigena1)
    {
        $this->codigoLinguaIndigena1 = $codigoLinguaIndigena1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoLinguaIndigena2()
    {
        return $this->codigoLinguaIndigena2;
    }

    /**
     * @param mixed $codigoLinguaIndigena2
     * @return Registro10
     */
    public function setCodigoLinguaIndigena2($codigoLinguaIndigena2)
    {
        $this->codigoLinguaIndigena2 = $codigoLinguaIndigena2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoLinguaIndigena3()
    {
        return $this->codigoLinguaIndigena3;
    }

    /**
     * @param mixed $codigoLinguaIndigena3
     * @return Registro10
     */
    public function setCodigoLinguaIndigena3($codigoLinguaIndigena3)
    {
        $this->codigoLinguaIndigena3 = $codigoLinguaIndigena3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExameSelecao()
    {
        return $this->exameSelecao;
    }

    /**
     * @param mixed $exameSelecao
     * @return Registro10
     */
    public function setExameSelecao($exameSelecao)
    {
        $this->exameSelecao = $exameSelecao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReservaVagaPretoPardoIndigena()
    {
        return $this->reservaVagaPretoPardoIndigena;
    }

    /**
     * @param mixed $reservaVagaPretoPardoIndigena
     * @return Registro10
     */
    public function setReservaVagaPretoPardoIndigena($reservaVagaPretoPardoIndigena)
    {
        $this->reservaVagaPretoPardoIndigena = $reservaVagaPretoPardoIndigena;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReservaVagaRenda()
    {
        return $this->reservaVagaRenda;
    }

    /**
     * @param mixed $reservaVagaRenda
     * @return Registro10
     */
    public function setReservaVagaRenda($reservaVagaRenda)
    {
        $this->reservaVagaRenda = $reservaVagaRenda;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReservaVagaEscolaPublica()
    {
        return $this->reservaVagaEscolaPublica;
    }

    /**
     * @param mixed $reservaVagaEscolaPublica
     * @return Registro10
     */
    public function setReservaVagaEscolaPublica($reservaVagaEscolaPublica)
    {
        $this->reservaVagaEscolaPublica = $reservaVagaEscolaPublica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReservaVagaDeficiencia()
    {
        return $this->reservaVagaDeficiencia;
    }

    /**
     * @param mixed $reservaVagaDeficiencia
     * @return Registro10
     */
    public function setReservaVagaDeficiencia($reservaVagaDeficiencia)
    {
        $this->reservaVagaDeficiencia = $reservaVagaDeficiencia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReservaVagaOutro()
    {
        return $this->reservaVagaOutro;
    }

    /**
     * @param mixed $reservaVagaOutro
     * @return Registro10
     */
    public function setReservaVagaOutro($reservaVagaOutro)
    {
        $this->reservaVagaOutro = $reservaVagaOutro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSemReservaVagas()
    {
        return $this->semReservaVagas;
    }

    /**
     * @param mixed $semReservaVagas
     * @return Registro10
     */
    public function setSemReservaVagas($semReservaVagas)
    {
        $this->semReservaVagas = $semReservaVagas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPossuiSiteBlog()
    {
        return $this->possuiSiteBlog;
    }

    /**
     * @param mixed $possuiSiteBlog
     * @return Registro10
     */
    public function setPossuiSiteBlog($possuiSiteBlog)
    {
        $this->possuiSiteBlog = $possuiSiteBlog;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEscolaCompartilhaEspacoComunidade()
    {
        return $this->escolaCompartilhaEspacoComunidade;
    }

    /**
     * @param mixed $escolaCompartilhaEspacoComunidade
     * @return Registro10
     */
    public function setEscolaCompartilhaEspacoComunidade($escolaCompartilhaEspacoComunidade)
    {
        $this->escolaCompartilhaEspacoComunidade = $escolaCompartilhaEspacoComunidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEscolaUsaEquipamentosParaAtividade()
    {
        return $this->escolaUsaEquipamentosParaAtividade;
    }

    /**
     * @param mixed $escolaUsaEquipamentosParaAtividade
     * @return Registro10
     */
    public function setEscolaUsaEquipamentosParaAtividade($escolaUsaEquipamentosParaAtividade)
    {
        $this->escolaUsaEquipamentosParaAtividade = $escolaUsaEquipamentosParaAtividade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssociacaoPais()
    {
        return $this->associacaoPais;
    }

    /**
     * @param mixed $associacaoPais
     * @return Registro10
     */
    public function setAssociacaoPais($associacaoPais)
    {
        $this->associacaoPais = $associacaoPais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssociacaoPaisMestres()
    {
        return $this->associacaoPaisMestres;
    }

    /**
     * @param mixed $associacaoPaisMestres
     * @return Registro10
     */
    public function setAssociacaoPaisMestres($associacaoPaisMestres)
    {
        $this->associacaoPaisMestres = $associacaoPaisMestres;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConselhoEscolar()
    {
        return $this->conselhoEscolar;
    }

    /**
     * @param mixed $conselhoEscolar
     * @return Registro10
     */
    public function setConselhoEscolar($conselhoEscolar)
    {
        $this->conselhoEscolar = $conselhoEscolar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGremioEstudantil()
    {
        return $this->gremioEstudantil;
    }

    /**
     * @param mixed $gremioEstudantil
     * @return Registro10
     */
    public function setGremioEstudantil($gremioEstudantil)
    {
        $this->gremioEstudantil = $gremioEstudantil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrgaosColegiadosOutros()
    {
        return $this->orgaosColegiadosOutros;
    }

    /**
     * @param mixed $orgaosColegiadosOutros
     * @return Registro10
     */
    public function setOrgaosColegiadosOutros($orgaosColegiadosOutros)
    {
        $this->orgaosColegiadosOutros = $orgaosColegiadosOutros;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrgaosColegiadosNenhum()
    {
        return $this->orgaosColegiadosNenhum;
    }

    /**
     * @param mixed $orgaosColegiadosNenhum
     * @return Registro10
     */
    public function setOrgaosColegiadosNenhum($orgaosColegiadosNenhum)
    {
        $this->orgaosColegiadosNenhum = $orgaosColegiadosNenhum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProjetoPedagogicoAtualizado()
    {
        return $this->projetoPedagogicoAtualizado;
    }

    /**
     * @param mixed $projetoPedagogicoAtualizado
     * @return Registro10
     */
    public function setProjetoPedagogicoAtualizado($projetoPedagogicoAtualizado)
    {
        $this->projetoPedagogicoAtualizado = $projetoPedagogicoAtualizado;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getDesenvolveAcoesEducacaoAmbiental()
    {
        return $this->desenvolveAcoesEducacaoAmbiental;
    }

    /**
     * @param mixed $desenvolveAcoesEducacaoAmbiental
     * @return Registro10
     */
    public function setDesenvolveAcoesEducacaoAmbiental($desenvolveAcoesEducacaoAmbiental)
    {
        $this->desenvolveAcoesEducacaoAmbiental = $desenvolveAcoesEducacaoAmbiental;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGestoresEscola()
    {
        return $this->gestoresEscola;
    }

    /**
     * @param mixed $gestoresEscola
     * @return Registro10
     */
    public function setGestoresEscola($gestoresEscola)
    {
        $this->gestoresEscola = $gestoresEscola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrientadorComunitario()
    {
        return $this->orientadorComunitario;
    }

    public function getTradutorInterpreteLibras()
    {
        return $this->tradutorInterpreteLibras;
    }

    /**
     * @param mixed $orientadorComunitario
     * @return Registro10
     */
    public function setOrientadorComunitario($orientadorComunitario)
    {
        $this->orientadorComunitario = $orientadorComunitario;
        return $this;
    }

    public function setTradutorInterpreteLibras($tradutor)
    {
        $this->tradutorInterpreteLibras = $tradutor;
        return $this;
    }

    /**
     * @param mixed $agronomosTecnicosResponsaveisProducaoAgricola
     * @return Registro10
     */
    public function setAgronomosTecnicosResponsaveisProducaoAgricola($agronomosTecnicosResponsaveisProducaoAgricola)
    {
        $this->agronomosTecnicosResponsaveisProducaoAgricola = $agronomosTecnicosResponsaveisProducaoAgricola;
        return $this;
    }

    public function getAgronomosTecnicosResponsaveisProducaoAgricola()
    {
        return $this->agronomosTecnicosResponsaveisProducaoAgricola;
    }

    /**
     * @param mixed $revisorTextoBrailleAssistenteVidente
     * @return Registro10
     */
    public function setRevisorTextoBrailleAssistenteVidente($revisorTextoBrailleAssistenteVidente)
    {
        $this->revisorTextoBrailleAssistenteVidente = $revisorTextoBrailleAssistenteVidente;
        return $this;
    }

    public function getRevisorTextoBrailleAssistenteVidente()
    {
        return $this->revisorTextoBrailleAssistenteVidente;
    }

    /**
     * @return mixed
     */
    public function getNaoHaFuncionarios()
    {
        return $this->naoHaFuncionarios;
    }

    /**
     * @param mixed $naoHaFuncionarios
     * @return Registro10
     */
    public function setNaoHaFuncionarios($naoHaFuncionarios)
    {
        $this->naoHaFuncionarios = $naoHaFuncionarios;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAreaHortaPlantioProducaoAgricola()
    {
        return $this->areaHortaPlantioProducaoAgricola;
    }

    /**
     * @param mixed $areaHortaPlantioProducaoAgricola
     * @return Registro10
     */
    public function setAreaHortaPlantioProducaoAgricola($areaHortaPlantioProducaoAgricola)
    {
        $this->areaHortaPlantioProducaoAgricola = $areaHortaPlantioProducaoAgricola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComoConteudoComponentesCamposExperiencias()
    {
        return $this->comoConteudoComponentesCamposExperiencias;
    }

    /**
     * @param mixed $comoConteudoComponentesCamposExperiencias
     * @return Registro10
     */
    public function setComoConteudoComponentesCamposExperiencias($comoConteudoComponentesCamposExperiencias)
    {
        $this->comoConteudoComponentesCamposExperiencias = $comoConteudoComponentesCamposExperiencias;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComoComponenteCurricularEspecialEspecifico()
    {
        return $this->comoComponenteCurricularEspecialEspecifico;
    }

    /**
     * @param mixed $comoComponenteCurricularEspecialEspecifico
     * @return Registro10
     */
    public function setComoComponenteCurricularEspecialEspecifico($comoComponenteCurricularEspecialEspecifico)
    {
        $this->comoComponenteCurricularEspecialEspecifico = $comoComponenteCurricularEspecialEspecifico;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComoEixoEstruturanteCurriculo()
    {
        return $this->comoEixoEstruturanteCurriculo;
    }

    /**
     * @param mixed $comoEixoEstruturanteCurriculo
     * @return Registro10
     */
    public function setComoEixoEstruturanteCurriculo($comoEixoEstruturanteCurriculo)
    {
        $this->comoEixoEstruturanteCurriculo = $comoEixoEstruturanteCurriculo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmEventos()
    {
        return $this->emEventos;
    }

    /**
     * @param mixed $emEventos
     * @return Registro10
     */
    public function setEmEventos($emEventos)
    {
        $this->emEventos = $emEventos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmProjetosTransversaisInterdisciplinares()
    {
        return $this->emProjetosTransversaisInterdisciplinares;
    }

    /**
     * @param mixed $emProjetosTransversaisInterdisciplinares
     * @return Registro10
     */
    public function setEmProjetosTransversaisInterdisciplinares($emProjetosTransversaisInterdisciplinares)
    {
        $this->emProjetosTransversaisInterdisciplinares = $emProjetosTransversaisInterdisciplinares;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNenhumaDasOpcoesListadas()
    {
        return $this->nenhumaDasOpcoesListadas;
    }

    /**
     * @param mixed $nenhumaDasOpcoesListadas
     * @return Registro10
     */
    public function setNenhumaDasOpcoesListadas($nenhumaDasOpcoesListadas)
    {
        $this->nenhumaDasOpcoesListadas = $nenhumaDasOpcoesListadas;
        return $this;
    }


    public function toArray()
    {
        return array(
            "tipoRegistro" => $this->getTipoRegistro(),
            "codigoInep" => $this->getCodigoInep(),
            "predioEscolar" => $this->getPredioEscolar(),
            "salaOutraEscola" => $this->getSalaOutraEscola(),
            "galpaoRanchoPaiolBarracao" => $this->getGalpaoRanchoPaiolBarracao(),
            "unidadeAtendimentoSocioeducativa" => $this->getUnidadeAtendimentoSocioeducativa(),
            "unidadePrisional" => $this->getUnidadePrisional(),
            "outroLocal" => $this->getOutroLocal(),
            "formaOcupacaoPredio" => $this->getFormaOcupacaoPredio(),
            "predioEscolarCompartilhado" => $this->getPredioEscolarCompartilhado(),
            "codigoEscolaCompartilha1" => $this->getCodigoEscolaCompartilha1(),
            "codigoEscolaCompartilha2" => $this->getCodigoEscolaCompartilha2(),
            "codigoEscolaCompartilha3" => $this->getCodigoEscolaCompartilha3(),
            "codigoEscolaCompartilha4" => $this->getCodigoEscolaCompartilha4(),
            "codigoEscolaCompartilha5" => $this->getCodigoEscolaCompartilha5(),
            "codigoEscolaCompartilha6" => $this->getCodigoEscolaCompartilha6(),
            "forneceAguaPotavel" => $this->getForneceAguaPotavel(),
            "aguaRedePublica" => $this->getAguaRedePublica(),
            "pocoArtesiano" => $this->getPocoArtesiano(),
            "cacimbaCisternaPoco" => $this->getCacimbaCisternaPoco(),
            "fonteRio" => $this->getFonteRio(),
            "carroPipa" => $this->getCarroPipa(),
            "semAgua" => $this->getSemAgua(),
            "luzRedePublica" => $this->getLuzRedePublica(),
            "geradorCombustivelFossil" => $this->getGeradorCombustivelFossil(),
            "energiaRenovavel" => $this->getEnergiaRenovavel(),
            "semEnergiaEletrica" => $this->getSemEnergiaEletrica(),
            "esgotoRedePublica" => $this->getEsgotoRedePublica(),
            "fossaSeptica" => $this->getFossaSeptica(),
            "fossaRudimentar" => $this->getFossaRudimentar(),
            "semEsgotamentoSanitario" => $this->getSemEsgotamentoSanitario(),
            "servicoColeta" => $this->getServicoColeta(),
            "queimaLixo" => $this->getQueimaLixo(),
            "enterraLixo" => $this->getEnterraLixo(),
            "levaLixo" => $this->getLevaLixo(),
            "descartaLixo" => $this->getDescartaLixo(),
            "separacaoLixo" => $this->getSeparacaoLixo(),
            "reaproveitamentoLixo" => $this->getReaproveitamentoLixo(),
            "reciclagemLixo" => $this->getReciclagemLixo(),
            "naoTrataLixo" => $this->getNaoTrataLixo(),
            "almoxarifado" => $this->getAlmoxarifado(),
            "areaVegetacaoGramado" => $this->getAreaVegetacaoGramado(),
            "auditorio" => $this->getAuditorio(),
            "banheiro" => $this->getBanheiro(),
            "banheiroAcessivelPessoasDeficiencia" => $this->getBanheiroAcessivelPessoasDeficiencia(),
            "banheiroEducacaoInfantil" => $this->getBanheiroEducacaoInfantil(),
            "banheiroExclusivoFuncionarios" => $this->getBanheiroExclusivoFuncionarios(),
            "banheiroComChuveiro" => $this->getBanheiroComChuveiro(),
            "biblioteca" => $this->getBiblioteca(),
            "cozinha" => $this->getCozinha(),
            "despensa" => $this->getDespensa(),
            "dormitorioAluno" => $this->getDormitorioAluno(),
            "dormitorioProfessor" => $this->getDormitorioProfessor(),
            "laboratorioCiencias" => $this->getLaboratorioCiencias(),
            "laboratorioInformatica" => $this->getLaboratorioInformatica(),
            "laboratorioEducacaoProfissional" => $this->getLaboratorioEducacaoProfissional(),
            "parqueInfantil" => $this->getParqueInfantil(),
            "patiocoberto" => $this->getPatiocoberto(),
            "patiodescoberto" => $this->getPatiodescoberto(),
            "piscina" => $this->getPiscina(),
            "quadraEsportesCoberta" => $this->getQuadraEsportesCoberta(),
            "quadraEsportesDescoberta" => $this->getQuadraEsportesDescoberta(),
            "refeitorio" => $this->getRefeitorio(),
            "salaRepousoAluno" => $this->getSalaRepousoAluno(),
            "atelieArtes" => $this->getAtelieArtes(),
            "salaMusica" => $this->getSalaMusica(),
            "salaDanca" => $this->getSalaDanca(),
            "salaMultiuso" => $this->getSalaMultiuso(),
            "terreirao" => $this->getTerreirao(),
            "viveiro" => $this->getViveiro(),
            "salaDiretoria" => $this->getSalaDiretoria(),
            "salaLeitura" => $this->getSalaLeitura(),
            "salaProfessores" => $this->getSalaProfessores(),
            "salaRecursosMultifuncionaisAEE" => $this->getSalaRecursosMultifuncionaisAEE(),
            "salaSecretaria" => $this->getSalaSecretaria(),
            "salaEducacaoProfissional" => $this->getSalaEducacaoProfissional(),
            "salasGravacaoEdicao" => $this->getSalaGravacaoEdicao(),
            "areaHortaPlantioProducaoAgricola" => $this->getAreaHortaPlantioProducaoAgricola(),
            "nenhumaDependencias" => $this->getNenhumaDependencias(),
            "corrimao" => $this->getCorrimao(),
            "elevador" => $this->getElevador(),
            "pisoTatil" => $this->getPisoTatil(),
            "portasComVao80Cm" => $this->getPortasComVao80Cm(),
            "rampas" => $this->getRampas(),
            "sinalizacaoAlarmeLuminoso" => $this->getSinalizacaoAlarmeLuminoso(),
            "sinalizacaoSonora" => $this->getSinalizacaoSonora(),
            "sinalizacaoTatil" => $this->getSinalizacaoTatil(),
            "sinalizacaoVisual" => $this->getSinalizacaoVisual(),
            "nenhumRecursosAcessibilidade" => $this->getNenhumRecursosAcessibilidade(),
            "numeroSalasDentroPredioEscolar" => $this->getNumeroSalasDentroPredioEscolar(),
            "numeroSalasForaPredioEscolar" => $this->getNumeroSalasForaPredioEscolar(),
            "numeroSalasClimatizada" => $this->getNumeroSalasClimatizada(),
            "numeroSalasComAcessibilidade" => $this->getNumeroSalasComAcessibilidade(),
            "antenaParabolica" => $this->getAntenaParabolica(),
            "computador" => $this->getComputador(),
            "copiadora" => $this->getCopiadora(),
            "impressora" => $this->getImpressora(),
            "impressoraMultifuncional" => $this->getImpressoraMultifuncional(),
            "scanner" => $this->getScanner(),
            "nenhumEquipamentosListados" => $this->getNenhumEquipamentosListados(),
            "aparelhoDVDBluray" => $this->getAparelhoDVDBluray(),
            "aparelhoSom" => $this->getAparelhoSom(),
            "aparelhoTelevisao" => $this->getAparelhoTelevisao(),
            "lousaDigital" => $this->getLousaDigital(),
            "projetorMultimidia" => $this->getProjetorMultimidia(),
            "computadorDesktop" => $this->getComputadorDesktop(),
            "computadorPortateis" => $this->getComputadorPortateis(),
            "tablets" => $this->getTablets(),
            "internetParaAdministrativo" => $this->getInternetParaAdministrativo(),
            "internetParaEnsino" => $this->getInternetParaEnsino(),
            "internetParaAluno" => $this->getInternetParaAluno(),
            "internetParaComunidade" => $this->getInternetParaComunidade(),
            "naoPossuiInternet" => $this->getNaoPossuiInternet(),
            "computadoresDisponiveis" => $this->getComputadoresDisponiveis(),
            "dispositivosPessoais" => $this->getDispositivosPessoais(),
            "internetBandaLarga" => $this->getInternetBandaLarga(),
            "redeCabo" => $this->getRedeCabo(),
            "redeWireless" => $this->getRedeWireless(),
            "naoExisteRede" => $this->getNaoExisteRede(),
            "agronomosTecnicosResponsaveisProducaoAgricola"
                => $this->getAgronomosTecnicosResponsaveisProducaoAgricola(),
            "auxiliarSecretariaAdministrativos" => $this->getAuxiliarSecretariaAdministrativos(),
            "auxiliarServicosGerais" => $this->getAuxiliarServicosGerais(),
            "bibliotecario" => $this->getBibliotecario(),
            "bombeiro" => $this->getBombeiro(),
            "coordenador" => $this->getCoordenador(),
            "fonoaudiologo" => $this->getFonoaudiologo(),
            "nutricionista" => $this->getNutricionista(),
            "psicologo" => $this->getPsicologo(),
            "profissionaisPreparacaoSeguraca" => $this->getProfissionaisPreparacaoSeguraca(),
            "profissionaisApoio" => $this->getProfissionaisApoio(),
            "secretario" => $this->getSecretario(),
            "seguranca" => $this->getSeguranca(),
            "tecnicosMonitores" => $this->getTecnicosMonitores(),
            "gestoresEscola" => $this->getGestoresEscola(),
            "orientadorComunitario" => $this->getOrientadorComunitario(),
            "tradutorInterpreteLibras" => $this->getTradutorInterpreteLibras(),
            "revisorTextoBrailleAssistenteVidente" => $this->getRevisorTextoBrailleAssistenteVidente(),
            "naoHaFuncionarios" => $this->getNaoHaFuncionarios(),
            "alimentacaoEscolar" => $this->getAlimentacaoEscolar(),
            "acervoMultimidia" => $this->getAcervoMultimidia(),
            "brinquedosEducacaoInfantil" => $this->getBrinquedosEducacaoInfantil(),
            "materiaisCientificos" => $this->getMateriaisCientificos(),
            "EquipamentoAmplificacaoOuDifusaoAudio" => $this->getEquipamentoAmplificacaoOuDifusaoAudio(),
            "equipamentosAtividadesHortaProducaoAgricola" => $this->getEquipamentosAtividadesHortaProducaoAgricola(),
            "instrumentosMusicais" => $this->getInstrumentosMusicais(),
            "jogosEducativos" => $this->getJogosEducativos(),
            "materialAtividadeCultural" => $this->getMaterialAtividadeCultural(),
            "materialEducacaoProfissional" => $this->getmaterialEducacaoProfissional(),
            "materialDesportivRecreacao" => $this->getMaterialDesportivRecreacao(),
            "materialEducacaoBilingue" => $this->getMaterialEducacaoBilingue(),
            "materialEducacaoIndigena" => $this->getMaterialEducacaoIndigena(),
            "materialEducacaoEtnicoRacial" => $this->getMaterialEducacaoEtnicoRacial(),
            "materialEducacaoCampo" => $this->getMaterialEducacaoCampo(),
            "materialEducacaoQuilombola" => $this->getMaterialEducacaoQuilombola(),
            "materialEducacaoEspecial" => $this->getMaterialEducacaoEspecial(),
            "nenhumInstrumentoListado" => $this->getNenhumInstrumentoListado(),
            "educacaoEscolarIndigena" => $this->getEducacaoEscolarIndigena(),
            "linguaIndigena" => $this->getLinguaIndigena(),
            "linguaPortuguesa" => $this->getLinguaPortuguesa(),
            "codigoLinguaIndigena1" => $this->getCodigoLinguaIndigena1(),
            "codigoLinguaIndigena2" => $this->getCodigoLinguaIndigena2(),
            "codigoLinguaIndigena3" => $this->getCodigoLinguaIndigena3(),
            "exameSelecao" => $this->getExameSelecao(),
            "reservaVagaPretoPardoIndigena" => $this->getReservaVagaPretoPardoIndigena(),
            "reservaVagaRenda" => $this->getReservaVagaRenda(),
            "reservaVagaEscolaPublica" => $this->getReservaVagaEscolaPublica(),
            "reservaVagaDeficiencia" => $this->getReservaVagaDeficiencia(),
            "reservaVagaOutro" => $this->getReservaVagaOutro(),
            "semReservaVagas" => $this->getSemReservaVagas(),
            "possuiSiteBlog" => $this->getPossuiSiteBlog(),
            "escolaCompartilhaEspacoComunidade" => $this->getEscolaCompartilhaEspacoComunidade(),
            "escolaUsaEquipamentosParaAtividade" => $this->getEscolaUsaEquipamentosParaAtividade(),
            "associacaoPais" => $this->getAssociacaoPais(),
            "associacaoPaisMestres" => $this->getAssociacaoPaisMestres(),
            "conselhoEscolar" => $this->getConselhoEscolar(),
            "gremioEstudantil" => $this->getGremioEstudantil(),
            "orgaosColegiadosOutros" => $this->getOrgaosColegiadosOutros(),
            "orgaosColegiadosNenhum" => $this->getOrgaosColegiadosNenhum(),
            "projetoPedagogicoAtualizado" => $this->getProjetoPedagogicoAtualizado(),
            "desenvolveAcoesEducacaoAmbiental" => $this->getDesenvolveAcoesEducacaoAmbiental(),
            "comoConteudoComponentesCamposExperiencias" => $this->getComoConteudoComponentesCamposExperiencias(),
            "comoComponenteCurricularEspecialEspecifico" => $this->getComoComponenteCurricularEspecialEspecifico(),
            "comoEixoEstruturanteCurriculo" => $this->getComoEixoEstruturanteCurriculo(),
            "emEventos" => $this->getEmEventos(),
            "emProjetosTransversaisInterdisciplinares" => $this->getEmProjetosTransversaisInterdisciplinares(),
            "nenhumaDasOpcoesListadas" => $this->getNenhumaDasOpcoesListadas()
        );
    }
}
