<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral G conforme
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

class Registro20
{
    const TIPO_REGISTRO = '20';
    private $tipoRegistro = self::TIPO_REGISTRO;

    private $codigoInepEscola;
    private $codigoTurma;
    private $codigoInep;
    private $nomeTurma;
    private $tipoMediacaoDidaticoPedagogica;
    private $horaInicio;
    private $minutoInicio;
    private $horaFim;
    private $minutoFim;
    private $projetoVidaUnidade;

    //Dias da Semana
    private $domingo;
    private $segundaFeira;
    private $tercaFeira;
    private $quartaFeira;
    private $quintaFeira;
    private $sextaFeira;
    private $sabado;

    //Tipo de atendimento
    private $escolarizacao;
    private $atividadeComplementar;
    private $atendimentoAEE;

    // Tipo de atividade complementar
    private $codigo1;
    private $codigo2;
    private $codigo3;
    private $codigo4;
    private $codigo5;
    private $codigo6;

    private $localFuncionamentoDiferenciado = 0;
    private $modalidade;
    private $etapaCenso;
    private $codigoCurso;

    //Áreas do conhecimento/componentes curriculares
    private $quimica;
    private $fisica;
    private $matematica;
    private $biologia;
    private $ciencias;
    private $literaturaPortuguesa;
    private $literaturaEstrangeiraIngles;
    private $literaturaEstrangeiraEspanhol;
    private $literaturaEstrangeiraOutra;
    private $artes;
    private $educacaoFisica;
    private $historia;
    private $geografia;
    private $filosofia;
    private $informatica;
    private $cursosTecnicosProfissionais;
    private $libras;
    private $disciplinasPedagogicas;
    private $ensinoReligioso;
    private $linguaIndigena;
    private $estudosSociais;
    private $sociologia;
    private $literaturaEstrangeiraFrances;
    private $portuguesComoSegundaLingua;
    private $estagioSupervisionado;
    private $outrasDisciplinas;
    private $tipoEstruturaCurricular = [];
    private $formacaoGeralBasica;
    private $itinerarioFormativo;
    private $naoAplica;
    private $serieAno = null;
    private $periodosSemestrais = null;
    private $ciclos = null;
    private $gruposNaoSeriados = null;
    private $modulos = null;
    private $alternanciaRegular = null;

    private $outrasUnidades;

    /**
     * @return mixed
     */
    public function getOutrasUnidades()
    {
        return $this->outrasUnidades;
    }

    /**
     * @param mixed $outrasUnidades
     * @return Registro20
     */
    public function setOutrasUnidades($outrasUnidades)
    {
        $this->outrasUnidades = $outrasUnidades;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEletivas()
    {
        return $this->eletivas;
    }

    /**
     * @param mixed $eletivas
     * @return Registro20
     */
    public function setEletivas($eletivas)
    {
        $this->eletivas = $eletivas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLibrasUnidadeCurricular()
    {
        return $this->librasUnidadeCurricular;
    }

    /**
     * @param mixed $librasUnidadeCurricular
     * @return Registro20
     */
    public function setLibrasUnidadeCurricular($librasUnidadeCurricular)
    {
        $this->librasUnidadeCurricular = $librasUnidadeCurricular;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinguaEspanhol()
    {
        return $this->linguaEspanhol;
    }

    /**
     * @param mixed $linguaEspanhol
     * @return Registro20
     */
    public function setLinguaEspanhol($linguaEspanhol)
    {
        $this->linguaEspanhol = $linguaEspanhol;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinguaFrances()
    {
        return $this->linguaFrances;
    }

    /**
     * @param mixed $linguaFrances
     * @return Registro20
     */
    public function setLinguaFrances($linguaFrances)
    {
        $this->linguaFrances = $linguaFrances;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinguaOutra()
    {
        return $this->linguaOutra;
    }

    /**
     * @param mixed $linguaOutra
     * @return Registro20
     */
    public function setLinguaOutra($linguaOutra)
    {
        $this->linguaOutra = $linguaOutra;
        return $this;
    }

    /**
     * @return null
     */
    public function getTrilhaAprofundamento()
    {
        return $this->trilhaAprofundamento;
    }

    /**
     * @param null $trilhaAprofundamento
     * @return Registro20
     */
    public function setTrilhaAprofundamento($trilhaAprofundamento)
    {
        $this->trilhaAprofundamento = $trilhaAprofundamento;
        return $this;
    }

    /**
     * @return array
     */
    public function getFormaOrganizaTurma()
    {
        return $this->formaOrganizaTurma;
    }

    /**
     * @param array $formaOrganizaTurma
     * @return Registro20
     */
    public function setFormaOrganizaTurma($formaOrganizaTurma)
    {
        $this->formaOrganizaTurma = $formaOrganizaTurma;
        return $this;
    }
    private $eletivas;
    private $librasUnidadeCurricular;
    private $linguaIndigenaUnidade;
    private $linguaEspanhol;
    private $linguaFrances;
    private $linguaOutra;
    private $projetoVida = 0;
    private $trilhaAprofundamento = null;
    private $formaOrganizaTurma = [];

    /**
     * @return mixed
     */
    public function getLinguaIndigenaUnidade()
    {
        return $this->linguaIndigenaUnidade;
    }

    /**
     * @param mixed $linguaIndigenaUnidade
     * @return Registro20
     */
    public function setLinguaIndigenaUnidade($linguaIndigenaUnidade)
    {
        $this->linguaIndigenaUnidade = $linguaIndigenaUnidade;
        return $this;
    }


    /**
     * @return string
     */
    public function getSerieAno()
    {
        return $this->serieAno;
    }


    public function setSerieAno($serieAno)
    {
        $this->serieAno = $serieAno;
        return $this;
    }

     /**
     * @return string
     */
    public function getFromaOrganizaTurma()
    {
        return $this->formaOrganizaTurma;
    }


    public function setFromaOrganizaTurma($formaOrganizaTurma)
    {
        $this->formaOrganizaTurma[] = $formaOrganizaTurma;
        return $this;
    }

     /**
     * @return string
     */
    public function getPeriodosSemestrais()
    {
        return $this->periodosSemestrais;
    }


    public function setPeriodosSemestrais($periodosSemestrais)
    {
        $this->periodosSemestrais = $periodosSemestrais;
        return $this;
    }

     /**
     * @return string
     */
    public function getCiclos()
    {
        return $this->ciclos;
    }


    public function setCiclos($ciclos)
    {
        $this->ciclos = $ciclos;
        return $this;
    }

        /**
     * @return string
     */
    public function getGruposNaoSeriados()
    {
        return $this->gruposNaoSeriados;
    }


    public function setGruposNaoSeriados($gruposNaoSeriados)
    {
        $this->gruposNaoSeriados = $gruposNaoSeriados;
        return $this;
    }

      /**
     * @return string
     */
    public function getModulos()
    {
        return $this->modulos;
    }


    public function setModulos($modulos)
    {
        $this->modulos = $modulos;
        return $this;
    }

       /**
     * @return string
     */
    public function getAlternanciaRegular()
    {
        return $this->alternanciaRegular;
    }


    public function setAlternanciaRegular($alternanciaRegular)
    {
        $this->alternanciaRegular = $alternanciaRegular;
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
     * @param string $tipoRegistro
     * @return Registro20
     */
    public function setTipoRegistro($tipoRegistro)
    {
        $this->tipoRegistro = $tipoRegistro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoInepEscola()
    {
        return $this->codigoInepEscola;
    }

    /**
     * @param mixed $codigoInepEscola
     * @return Registro20
     */
    public function setCodigoInepEscola($codigoInepEscola)
    {
        $this->codigoInepEscola = $codigoInepEscola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoTurma()
    {
        return $this->codigoTurma;
    }

    /**
     * @param mixed $codigoTurma
     * @return Registro20
     */
    public function setCodigoTurma($codigoTurma)
    {
        $this->codigoTurma = $codigoTurma;
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
     * @return Registro20
     */
    public function setCodigoInep($codigoInep)
    {
        $this->codigoInep = $codigoInep;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNomeTurma()
    {
        return $this->nomeTurma;
    }

    /**
     * @param mixed $nomeTurma
     * @return Registro20
     */
    public function setNomeTurma($nomeTurma)
    {
        $this->nomeTurma = $nomeTurma;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoMediacaoDidaticoPedagogica()
    {
        return $this->tipoMediacaoDidaticoPedagogica;
    }

    /**
     * @param mixed $tipoMediacaoDidaticoPedagogica
     * @return Registro20
     */
    public function setTipoMediacaoDidaticoPedagogica($tipoMediacaoDidaticoPedagogica)
    {
        $this->tipoMediacaoDidaticoPedagogica = $tipoMediacaoDidaticoPedagogica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    /**
     * @param mixed $horaInicio
     * @return Registro20
     */
    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinutoInicio()
    {
        return $this->minutoInicio;
    }

    /**
     * @param mixed $minutoInicio
     * @return Registro20
     */
    public function setMinutoInicio($minutoInicio)
    {
        $this->minutoInicio = $minutoInicio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHoraFim()
    {
        return $this->horaFim;
    }

    /**
     * @param mixed $horaFim
     * @return Registro20
     */
    public function setHoraFim($horaFim)
    {
        $this->horaFim = $horaFim;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinutoFim()
    {
        return $this->minutoFim;
    }

    /**
     * @param mixed $minutoFim
     * @return Registro20
     */
    public function setMinutoFim($minutoFim)
    {
        $this->minutoFim = $minutoFim;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomingo()
    {
        return $this->domingo;
    }

    /**
     * @param mixed $domingo
     * @return Registro20
     */
    public function setDomingo($domingo)
    {
        $this->domingo = $domingo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSegundaFeira()
    {
        return $this->segundaFeira;
    }

    /**
     * @param mixed $segundaFeira
     * @return Registro20
     */
    public function setSegundaFeira($segundaFeira)
    {
        $this->segundaFeira = $segundaFeira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTercaFeira()
    {
        return $this->tercaFeira;
    }

    /**
     * @param mixed $tercaFeira
     * @return Registro20
     */
    public function setTercaFeira($tercaFeira)
    {
        $this->tercaFeira = $tercaFeira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuartaFeira()
    {
        return $this->quartaFeira;
    }

    /**
     * @param mixed $quartaFeira
     * @return Registro20
     */
    public function setQuartaFeira($quartaFeira)
    {
        $this->quartaFeira = $quartaFeira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuintaFeira()
    {
        return $this->quintaFeira;
    }

    /**
     * @param mixed $quintaFeira
     * @return Registro20
     */
    public function setQuintaFeira($quintaFeira)
    {
        $this->quintaFeira = $quintaFeira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSextaFeira()
    {
        return $this->sextaFeira;
    }

    /**
     * @param mixed $sextaFeira
     * @return Registro20
     */
    public function setSextaFeira($sextaFeira)
    {
        $this->sextaFeira = $sextaFeira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSabado()
    {
        return $this->sabado;
    }

    /**
     * @param mixed $sabado
     * @return Registro20
     */
    public function setSabado($sabado)
    {
        $this->sabado = $sabado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEscolarizacao()
    {
        return $this->escolarizacao;
    }

    /**
     * @param mixed $escolarizacao
     * @return Registro20
     */
    public function setEscolarizacao($escolarizacao)
    {
        $this->escolarizacao = $escolarizacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAtividadeComplementar()
    {
        return $this->atividadeComplementar;
    }

    /**
     * @param mixed $atividadeComplementar
     * @return Registro20
     */
    public function setAtividadeComplementar($atividadeComplementar)
    {
        $this->atividadeComplementar = $atividadeComplementar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAtendimentoAEE()
    {
        return $this->atendimentoAEE;
    }

    /**
     * @param mixed $atendimentoAEE
     * @return Registro20
     */
    public function setAtendimentoAEE($atendimentoAEE)
    {
        $this->atendimentoAEE = $atendimentoAEE;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigo1()
    {
        return $this->codigo1;
    }

    /**
     * @param mixed $codigo1
     * @return Registro20
     */
    public function setCodigo1($codigo1)
    {
        $this->codigo1 = $codigo1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigo2()
    {
        return $this->codigo2;
    }

    /**
     * @param mixed $codigo2
     * @return Registro20
     */
    public function setCodigo2($codigo2)
    {
        $this->codigo2 = $codigo2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigo3()
    {
        return $this->codigo3;
    }

    /**
     * @param mixed $codigo3
     * @return Registro20
     */
    public function setCodigo3($codigo3)
    {
        $this->codigo3 = $codigo3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigo4()
    {
        return $this->codigo4;
    }

    /**
     * @param mixed $codigo4
     * @return Registro20
     */
    public function setCodigo4($codigo4)
    {
        $this->codigo4 = $codigo4;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigo5()
    {
        return $this->codigo5;
    }

    /**
     * @param mixed $codigo5
     * @return Registro20
     */
    public function setCodigo5($codigo5)
    {
        $this->codigo5 = $codigo5;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigo6()
    {
        return $this->codigo6;
    }

    /**
     * @param mixed $codigo6
     * @return Registro20
     */
    public function setCodigo6($codigo6)
    {
        $this->codigo6 = $codigo6;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocalFuncionamentoDiferenciado()
    {
        return $this->localFuncionamentoDiferenciado;
    }

    /**
     * @param mixed $localFuncionamentoDiferenciado
     * @return Registro20
     */
    public function setLocalFuncionamentoDiferenciado($localFuncionamentoDiferenciado)
    {
        $this->localFuncionamentoDiferenciado = $localFuncionamentoDiferenciado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModalidade()
    {
        return $this->modalidade;
    }

    /**
     * @param mixed $modalidade
     * @return Registro20
     */
    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEtapaCenso()
    {
        return $this->etapaCenso;
    }

    /**
     * @param mixed $etapaCenso
     * @return Registro20
     */
    public function setEtapaCenso($etapaCenso)
    {
        $this->etapaCenso = $etapaCenso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoCurso()
    {
        return $this->codigoCurso;
    }

    /**
     * @param mixed $codigoCurso
     * @return Registro20
     */
    public function setCodigoCurso($codigoCurso)
    {
        $this->codigoCurso = $codigoCurso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuimica()
    {
        return $this->quimica;
    }

    /**
     * @param mixed $quimica
     * @return Registro20
     */
    public function setQuimica($quimica)
    {
        $this->quimica = $quimica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFisica()
    {
        return $this->fisica;
    }

    /**
     * @param mixed $fisica
     * @return Registro20
     */
    public function setFisica($fisica)
    {
        $this->fisica = $fisica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMatematica()
    {
        return $this->matematica;
    }

    /**
     * @param mixed $matematica
     * @return Registro20
     */
    public function setMatematica($matematica)
    {
        $this->matematica = $matematica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBiologia()
    {
        return $this->biologia;
    }

    /**
     * @param mixed $biologia
     * @return Registro20
     */
    public function setBiologia($biologia)
    {
        $this->biologia = $biologia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCiencias()
    {
        return $this->ciencias;
    }

    /**
     * @param mixed $ciencias
     * @return Registro20
     */
    public function setCiencias($ciencias)
    {
        $this->ciencias = $ciencias;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLiteraturaPortuguesa()
    {
        return $this->literaturaPortuguesa;
    }

    /**
     * @param mixed $literaturaPortuguesa
     * @return Registro20
     */
    public function setLiteraturaPortuguesa($literaturaPortuguesa)
    {
        $this->literaturaPortuguesa = $literaturaPortuguesa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLiteraturaEstrangeiraIngles()
    {
        return $this->literaturaEstrangeiraIngles;
    }

    /**
     * @param mixed $literaturaEstrangeiraIngles
     * @return Registro20
     */
    public function setLiteraturaEstrangeiraIngles($literaturaEstrangeiraIngles)
    {
        $this->literaturaEstrangeiraIngles = $literaturaEstrangeiraIngles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLiteraturaEstrangeiraEspanhol()
    {
        return $this->literaturaEstrangeiraEspanhol;
    }

    /**
     * @param mixed $literaturaEstrangeiraEspanhol
     * @return Registro20
     */
    public function setLiteraturaEstrangeiraEspanhol($literaturaEstrangeiraEspanhol)
    {
        $this->literaturaEstrangeiraEspanhol = $literaturaEstrangeiraEspanhol;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLiteraturaEstrangeiraOutra()
    {
        return $this->literaturaEstrangeiraOutra;
    }

    /**
     * @param mixed $literaturaEstrangeiraOutra
     * @return Registro20
     */
    public function setLiteraturaEstrangeiraOutra($literaturaEstrangeiraOutra)
    {
        $this->literaturaEstrangeiraOutra = $literaturaEstrangeiraOutra;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getArtes()
    {
        return $this->artes;
    }

    /**
     * @param mixed $artes
     * @return Registro20
     */
    public function setArtes($artes)
    {
        $this->artes = $artes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEducacaoFisica()
    {
        return $this->educacaoFisica;
    }

    /**
     * @param mixed $educacaoFisica
     * @return Registro20
     */
    public function setEducacaoFisica($educacaoFisica)
    {
        $this->educacaoFisica = $educacaoFisica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHistoria()
    {
        return $this->historia;
    }

    /**
     * @param mixed $historia
     * @return Registro20
     */
    public function setHistoria($historia)
    {
        $this->historia = $historia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeografia()
    {
        return $this->geografia;
    }

    /**
     * @param mixed $geografia
     * @return Registro20
     */
    public function setGeografia($geografia)
    {
        $this->geografia = $geografia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilosofia()
    {
        return $this->filosofia;
    }

    /**
     * @param mixed $filosofia
     * @return Registro20
     */
    public function setFilosofia($filosofia)
    {
        $this->filosofia = $filosofia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInformatica()
    {
        return $this->informatica;
    }

    /**
     * @param mixed $informatica
     * @return Registro20
     */
    public function setInformatica($informatica)
    {
        $this->informatica = $informatica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCursosTecnicosProfissionais()
    {
        return $this->cursosTecnicosProfissionais;
    }

    /**
     * @param mixed $cursosTecnicosProfissionais
     * @return Registro20
     */
    public function setCursosTecnicosProfissionais($cursosTecnicosProfissionais)
    {
        $this->cursosTecnicosProfissionais = $cursosTecnicosProfissionais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLibras()
    {
        return $this->libras;
    }

    /**
     * @param mixed $libras
     * @return Registro20
     */
    public function setLibras($libras)
    {
        $this->libras = $libras;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisciplinasPedagogicas()
    {
        return $this->disciplinasPedagogicas;
    }

    /**
     * @param mixed $disciplinasPedagogicas
     * @return Registro20
     */
    public function setDisciplinasPedagogicas($disciplinasPedagogicas)
    {
        $this->disciplinasPedagogicas = $disciplinasPedagogicas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnsinoReligioso()
    {
        return $this->ensinoReligioso;
    }

    /**
     * @param mixed $ensinoReligioso
     * @return Registro20
     */
    public function setEnsinoReligioso($ensinoReligioso)
    {
        $this->ensinoReligioso = $ensinoReligioso;
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
     * @return Registro20
     */
    public function setLinguaIndigena($linguaIndigena)
    {
        $this->linguaIndigena = $linguaIndigena;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstudosSociais()
    {
        return $this->estudosSociais;
    }

    /**
     * @param mixed $estudosSociais
     * @return Registro20
     */
    public function setEstudosSociais($estudosSociais)
    {
        $this->estudosSociais = $estudosSociais;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSociologia()
    {
        return $this->sociologia;
    }

    /**
     * @param mixed $sociologia
     * @return Registro20
     */
    public function setSociologia($sociologia)
    {
        $this->sociologia = $sociologia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLiteraturaEstrangeiraFrances()
    {
        return $this->literaturaEstrangeiraFrances;
    }

    /**
     * @param mixed $literaturaEstrangeiraFrances
     * @return Registro20
     */
    public function setLiteraturaEstrangeiraFrances($literaturaEstrangeiraFrances)
    {
        $this->literaturaEstrangeiraFrances = $literaturaEstrangeiraFrances;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPortuguesComoSegundaLingua()
    {
        return $this->portuguesComoSegundaLingua;
    }

    /**
     * @param mixed $portuguesComoSegundaLingua
     * @return Registro20
     */
    public function setPortuguesComoSegundaLingua($portuguesComoSegundaLingua)
    {
        $this->portuguesComoSegundaLingua = $portuguesComoSegundaLingua;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstagioSupervisionado()
    {
        return $this->estagioSupervisionado;
    }

    /**
     * @param mixed $estagioSupervisionado
     * @return Registro20
     */
    public function setEstagioSupervisionado($estagioSupervisionado)
    {
        $this->estagioSupervisionado = $estagioSupervisionado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutrasDisciplinas()
    {
        return $this->outrasDisciplinas;
    }

    /**
     * @param mixed $outrasDisciplinas
     * @return Registro20
     */
    public function setOutrasDisciplinas($outrasDisciplinas)
    {
        $this->outrasDisciplinas = $outrasDisciplinas;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTurmaUnificada()
    {
        return $this->turmaUnificada;
    }

    /**
     * @param bool $turmaUnificada
     * @return Registro20
     */
    public function setTurmaUnificada($turmaUnificada)
    {
        $this->turmaUnificada = $turmaUnificada;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNomeEtapaTurma()
    {
        return $this->nomeEtapaTurma;
    }

    /**
     * @param mixed $nomeEtapaTurma
     * @return Registro20
     */
    public function setNomeEtapaTurma($nomeEtapaTurma)
    {
        $this->nomeEtapaTurma = $nomeEtapaTurma;
        return $this;
    }

      /**
     * @return mixed
     */
    public function getTipoEstruturaCurricular()
    {
        return $this->tipoEstruturaCurricular;
    }

    /**
     * @param mixed $nomeEtapaTurma
     * @return Registro20
     */
    public function setTipoEstruturaCurricular($tipo)
    {
        $this->tipoEstruturaCurricular = $tipo;
        return $this;
    }

    public function setFormacaoGeralBasica()
    {
        $estruturas = $this->getTipoEstruturaCurricular();
        $this->formacaoGeralBasica = 0;
        if (in_array(0, $estruturas)) {
            $this->formacaoGeralBasica = 1;
        }
    }

    public function getFormacaoGeralBasica()
    {
        return $this->formacaoGeralBasica;
    }

    public function setItinerarioFormativo()
    {
        $estruturas = $this->getTipoEstruturaCurricular();

        $this->itinerarioFormativo = 0;
        if (in_array(1, $estruturas)) {
            $this->itinerarioFormativo = 1;
        }
    }

    public function getItinerarioFormativo()
    {
        return $this->itinerarioFormativo;
    }

    public function setNaoAplica()
    {
        $estruturas = $this->getTipoEstruturaCurricular();

        $this->naoAplica = 0;
        if (in_array(2, $estruturas)) {
            $this->naoAplica = 1;
        }
    }

    public function getNaoAplica()
    {
        return $this->naoAplica;
    }

    public function nulaProjetoVida()
    {
        $this->projetoVida = null;
        $this->projetoVidaUnidade = null;
    }

    public function setProjetoVida($projetoVida)
    {
        $this->projetoVida = $projetoVida;
    }
    public function getProjetoVida()
    {
        return $this->projetoVida;
    }

    public function setProjetoVidaUnidade($projetoVida)
    {
        $this->projetoVidaUnidade = $projetoVida;
    }
    public function getProjetoVidaUnidade()
    {
        return $this->projetoVidaUnidade;
    }

    public function toArray()
    {
        return array(
            "tipoRegistro" => $this->getTipoRegistro(),
            "codigoInepEscola" => $this->getCodigoInepEscola(),
            "codigoTurma" => $this->getCodigoTurma(),
            "codigoInep" => $this->getCodigoInep(),
            "nomeTurma" => $this->getNomeTurma(),
            "tipoMediacaoDidaticoPedagogica" => $this->getTipoMediacaoDidaticoPedagogica(),
            "horaInicio" => $this->getHoraInicio(),
            "minutoInicio" => $this->getMinutoInicio(),
            "horaFim" => $this->getHoraFim(),
            "minutoFim" => $this->getMinutoFim(),
            "domingo" => $this->getDomingo(),
            "segundaFeira" => $this->getSegundaFeira(),
            "tercaFeira" => $this->getTercaFeira(),
            "quartaFeira" => $this->getQuartaFeira(),
            "quintaFeira" => $this->getQuintaFeira(),
            "sextaFeira" => $this->getSextaFeira(),
            "sabado" => $this->getSabado(),
            "escolarizacao" => $this->getEscolarizacao(),
            "atividadeComplementar" => $this->getAtividadeComplementar(),
            "atendimentoAEE" => $this->getAtendimentoAEE(),
            "formacaoGeralBasica"  => $this->getFormacaoGeralBasica(),
            "itinerarioFormativo"  => $this->getItinerarioFormativo(),
            "naoAplica"  => $this->getNaoAplica(),
            "codigo1" => $this->getCodigo1(),
            "codigo2" => $this->getCodigo2(),
            "codigo3" => $this->getCodigo3(),
            "codigo4" => $this->getCodigo4(),
            "codigo5" => $this->getCodigo5(),
            "codigo6" => $this->getCodigo6(),
            "localFuncionamentoDiferenciado" => $this->getLocalFuncionamentoDiferenciado(),
            "modalidade" => $this->getModalidade(),
            "etapaCenso" => $this->getEtapaCenso(),
            "codigoCurso" => $this->getCodigoCurso(),
            "serieAno" => $this->getSerieAno(),
            "periodosSemestrais" => $this->getPeriodosSemestrais(),
            "ciclos" => $this->getCiclos(),
            "gruposNaoSeriados" => $this->getGruposNaoSeriados(),
            "modulos" => $this->getModulos(),
            "alternanciaRegular" => $this->getAlternanciaRegular(),
            "eletivas" => null,
            "librasUnidadeCurricular" => null,
            "lingaIndigena" => null,
            "linguaEspanhol" => null,
            "linguaFrances" => null,
            "linguaOutra" => null,
            "projetoVida" => $this->getProjetoVida(),
            "trilhaAprofundamento" => null,
            "outrasUnidades" => $this->getOutrasUnidades(),
            "quimica" => $this->getQuimica(),
            "fisica" => $this->getFisica(),
            "matematica" => $this->getMatematica(),
            "biologia" => $this->getBiologia(),
            "ciencias" => $this->getCiencias(),
            "literaturaPortuguesa" => $this->getLiteraturaPortuguesa(),
            "literaturaEstrangeiraIngles" => $this->getLiteraturaEstrangeiraIngles(),
            "literaturaEstrangeiraEspanhol" => $this->getLiteraturaEstrangeiraEspanhol(),
            "literaturaEstrangeiraOutra" => $this->getLiteraturaEstrangeiraOutra(),
            "artes" => $this->getArtes(),
            "educacaoFisica" => $this->getEducacaoFisica(),
            "historia" => $this->getHistoria(),
            "geografia" => $this->getGeografia(),
            "filosofia" => $this->getFilosofia(),
            "informatica" => $this->getInformatica(),
            "cursosTecnicosProfissionais" => $this->getCursosTecnicosProfissionais(),
            "libras" => $this->getLibras(),
            "disciplinasPedagogicas" => $this->getDisciplinasPedagogicas(),
            "ensinoReligioso" => $this->getEnsinoReligioso(),
            "linguaIndigena" => $this->getLinguaIndigena(),
            "linguaIndigenaUnidade" => $this->getLinguaIndigenaUnidade(),
            "estudosSociais" => $this->getEstudosSociais(),
            "sociologia" => $this->getSociologia(),
            "literaturaEstrangeiraFrances" => $this->getLiteraturaEstrangeiraFrances(),
            "portuguesComoSegundaLingua" => $this->getPortuguesComoSegundaLingua(),
            "estagioSupervisionado" => $this->getEstagioSupervisionado(),
            "projetoVidaUnidade" => $this->getProjetoVidaUnidade(),
            "outrasDisciplinas" => $this->getOutrasDisciplinas(),
            "librasPrimeiraLingua" => 0
        );
    }
}
