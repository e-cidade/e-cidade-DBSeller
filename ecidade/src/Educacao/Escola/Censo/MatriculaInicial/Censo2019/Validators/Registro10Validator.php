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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Validators;

use DBString;
use ECidade\Educacao\Escola\Censo\Log\LogMatriculaInicial;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro00;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro10;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro20;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;
use Exception;

class Registro10Validator
{
    /**
     * @var Registro10
     */
    private $registro;

    /**
     * @var Registro00
     */
    private $registro00;

    /**
     * @var Registro20[]
     */
    private $registros20;

    public function setRegistro(Registro10 $registro)
    {
        $this->registro = $registro;
    }

    public function setRegistro00(Registro00 $registro00)
    {
        $this->registro00 = $registro00;
    }

    public function setRegistros20(array $registros20)
    {
        $this->registros20 = $registros20;
    }

    /**
     * @param $dado
     * @throws Exception
     */
    public function log($dado)
    {
        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO10, $dado);
    }

    /**
     * Diz se um campo foi preenchido
     * @return boolean
     */
    private function isPreenchido($campo)
    {
        return !is_null($campo) && $campo !== "";
    }

    public function validar()
    {
        $this->validarTipoRegistro();
        $this->validarCodigoInep();
        $this->validarLocalFuncionamento();
        $this->validarPredioEscolar();
        $this->validarSalaOutraEscola();
        $this->validarGalpaoRanchoPaiolBarracao();
        $this->validarUnidadeAtendimentoSocioeducativa();
        $this->validarUnidadePrisional();
        $this->validarOutroLocal();
        $this->validarFormaOcupacaoPredio();
        $this->validarPredioEscolarCompartilhado();
        $this->validarCodigoEscolaCompartilha();
        $this->validarCodigoEscolaCompartilha1();
        $this->validarCodigoEscolaCompartilha2();
        $this->validarCodigoEscolaCompartilha3();
        $this->validarCodigoEscolaCompartilha4();
        $this->validarCodigoEscolaCompartilha5();
        $this->validarCodigoEscolaCompartilha6();
        $this->validarForneceAguaPotavel();
        $this->validarAbastecimentoAgua();
        $this->validarAguaRedePublica();
        $this->validarPocoArtesiano();
        $this->validarCacimbaCisternaPoco();
        $this->validarFonteRio();
        $this->validarSemAgua();
        $this->validarCarroPipa();
        $this->validarFonteEnergiaEletrica();
        $this->validarLuzRedePublica();
        $this->validarGeradorCombustivelFossil();
        $this->validarEnergiaRenovavel();
        $this->validarSemEnergiaEletrica();
        $this->validarEsgotamentoSanitario();
        $this->validarEsgotoRedePublica();
        $this->validarFossaSeptica();
        $this->validarFossaRudimentar();
        $this->validarSemEsgotamentoSanitario();
        $this->validarDestinacaoLixo();
        $this->validarServicoColeta();
        $this->validarQueimaLixo();
        $this->validarEnterraLixo();
        $this->validarLevaLixo();
        $this->validarDescartaLixo();
        $this->validarTratamentoLixo();
        $this->validarSeparacaoLixo();
        $this->validarReaproveitamentoLixo();
        $this->validarReciclagemLixo();
        $this->validarNaoTrataLixo();
        $this->validarDependenciasFisicas();
        $this->validarAlmoxarifado();
        $this->validarAreaVegetacaoGramado();
        $this->validarAreaHortaPlantioProducaoAgricola();
        $this->validarAudirotio();
        $this->validarBanheiro();
        $this->validarBanheiroAcessivelPessoasDeficiencia();
        $this->validarBanheiroEducacaoInfantil();
        $this->validarBanheiroExclusivoFuncionarios();
        $this->validarBanheiroComChuveiro();
        $this->validarBiblioteca();
        $this->validarCozinha();
        $this->validarDespensa();
        $this->validarDormitorioAluno();
        $this->validarDormitorioProfessor();
        $this->validarLaboratorioCiencias();
        $this->validarLaboratorioInformatica();
        $this->validarLaboratorioEducacaoProfissional();
        $this->validarParqueInfantil();
        $this->validarPatioCoberto();
        $this->validarPatioDescoberto();
        $this->validarPiscina();
        $this->validarQuadraEsportesCoberta();
        $this->validarQuadraEsportesDescoberta();
        $this->validarRefeitorio();
        $this->validarSalaRepousoAluno();
        $this->validarAtelieArtes();
        $this->validarSalaMusica();
        $this->validarSalaDanca();
        $this->validarSalaMultiuso();
        $this->validarTerreirao();
        $this->validarViveiro();
        $this->validarSalaDiretoria();
        $this->validarSalaLeitura();
        $this->validarSalaProfessores();
        $this->validarSalaRecursosMultifuncionaisAEE();
        $this->validarSalaSecretaria();
        $this->validarSalaEducacaoProfissional();
        $this->validarSalasGravacaoEdicao();
        $this->validarNenhumaDependencias();
        $this->validarRecursosAcessibilidade();
        $this->validarCorrimao();
        $this->validarElevador();
        $this->validarPisoTatil();
        $this->validarPortasComVao80Cm();
        $this->validarRampas();
        $this->validarSinalizacaoSonora();
        $this->validarSinalizacaoTatil();
        $this->validarSinalizacaoVisual();
        $this->validarNenhumRecursosAcessibilidade();
        $this->validarNumeroSalasDentroPredioEscolar();
        $this->validarNumeroSalasForaPredioEscolar();
        $this->validarNumeroSalasClimatizada();
        $this->validarNumeroSalasComAcessibilidade();
        $this->validarSinalizacaoAlarmeLuminoso();
        $this->validarEquipamentosTecnicosAdministrativos();
        $this->validarAntenaParabolica();
        $this->validarComputador();
        $this->validarCopiadora();
        $this->validarImpressora();
        $this->validarImpressoraMultifuncional();
        $this->validarScanner();
        $this->validarNenhumEquipamentosListados();
        $this->validarAparelhoDVDBluray();
        $this->validarAparelhoSom();
        $this->validarAparelhoTelevisao();
        $this->validarLousaDigital();
        $this->validarProjetorMultimidia();

        $this->validarComputadorDesktop();
        $this->validarComputadorPortateis();
        $this->validarTablets();
        $this->validarRegisQuantidadeComputadoresEmUsoPelosAlunos();

        $this->validarAcessoInternet();
        $this->validarInternetParaAdministrativo();
        $this->validarInternetParaEnsino();
        $this->validarInternetParaAluno();
        $this->validarInternetParaComunidade();
        $this->validarNaoPossuiInternet();
        $this->validarComputadoresDisponiveis();
        $this->validarDispositivosPessoais();
        $this->validarInternetBandaLarga();
        $this->validarRedeComputadores();
        // Rede local de interligacao de computadores
        $this->validarRedeCabo();
        $this->validarRedeWireless();
        $this->validarNaoExisteRede();
        // Profissionais da escola
        $this->validarProfissionaisEscola();
        $this->validarAuxiliarSecretariaAdministrativos();
        $this->validarAgronomosHorticultoresTecnicosMonitoresGestaoHorta();
        $this->validarAuxiliarServicosGerais();
        $this->validarBibliotecario();
        $this->validarBombeiro();
        $this->validarCoordenador();
        $this->validarFonoaudiologo();
        $this->validarNutricionista();
        $this->validarPsicologo();
        $this->validarProfissionaisPreparacaoSeguranca();
        $this->validarProfissionaisApoio();
        $this->validarSecretario();
        $this->validarSeguranca();
        $this->validarTecnicosMonitores();
        // Vice-diretor(a) ou diretor(a) adjunto(a), profissionais responsáveis pela gestão administrativa...
        $this->validarGestoresEscola();
        $this->validarOrientadorComunitario();
        $this->validarTradutorInterpreteLibras();
        $this->validarNaoHaFuncionarios();
        $this->validarAlimentacaoEscolar();
        $this->validarInstrumentosMateriais();
        $this->validarAcervoMultimidia();
        $this->validarBrinquedosEducacaoInfantil();
        $this->validarMateriaisCientificos();
        $this->validarEquipamentoAmplificacaoOuDifusaoAudio();
        $this->validarInstrumentosMusicais();
        $this->validarJogosEducativos();
        $this->validarMaterialAtividadeCultural();
        $this->validarmaterialEducacaoProfissional();
        $this->validarMaterialDesportivRecreacao();
        $this->validarMaterialEducacaoIndigena();
        $this->validarMaterialEducacaoEtnicoRacial();
        $this->validarMaterialEducacaoCampo();
        $this->validarEquipamentosAtividadesHortaProducaoAgricola();
        $this->validarMateriaisEducacaoEscolarQuilombola();
        $this->validarMateriaisEducacaoEscolarEspecial();
        $this->validarNenhumInstrumentoListado();
        $this->validarEducacaoEscolarIndigena();
        $this->validarLinguaEnsinoMinistrado();
        $this->validarLinguaIndigena();
        $this->validarLinguaPortuguesa();
        $this->validarCodigoLinguaIndigena1();
        $this->validarCodigoLinguaIndigena2();
        $this->validarCodigoLinguaIndigena3();
        $this->validarExameSelecao();
        $this->validarSistemaCotas();
        $this->validarReservaVagaPretoPardoIndigena();
        $this->validarReservaVagaRenda();
        $this->validarReservaVagaEscolaPublica();
        $this->validarReservaVagaDeficiencia();
        $this->validarReservaVagaOutro();
        $this->validarSemReservaVagas();
        $this->validarPossuiSiteBlog();
        $this->validarEscolaCompartilhaEspacoComunidade();
        $this->validarEscolaUsaEquipamentosParaAtividade();
        $this->validarAssociacaoPais();
        $this->validarAssociacaoPaisMestres();
        $this->validarConselhoEscolar();
        $this->validarGremioEstudantil();
        $this->validarOrgaosColegiadosOutros();
        $this->validarOrgaosColegiadosNenhum();
        $this->validarProjetoPedagogicoAtualizado();
        // Forma de desenvolvimento de acoes da area da educacao ambiental
        $this->validarEscolaDesenvolveAcoesEducacaoAmbiental();
        // Formas de desenvolvimento da educacao ambiental
        $this->validarFormasEducacaoAmbientalDesenvolvidas();
        $this->validarComoConteudoComponentesCamposExperiencias();
        $this->validarComoComponenteCurricularEspecialEspecifico();
        $this->validarComoEixoEstruturanteCurriculo();
        $this->validarEmEventos();
        $this->validarEmProjetosTransversaisInterdisciplinares();
        $this->validarNenhumaDasOpcoesListadas();
    }

    private function validarTipoRegistro()
    {
        $tipo = $this->registro->getTipoRegistro();

        if ($tipo != '10') {
            $this->log('O tipo de registro deveria ser 10');
        }
    }

    private function validarCodigoInep()
    {
        $inep = $this->registro->getCodigoInep();
        $inepAnterior = $this->registro00->getCodigoInep();
        $campo = "Código da Escola - Inep";

        if (empty($inep)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($inep != $inepAnterior) {
            $this->log(sprintf('O campo "%s" está diferente do registro 00 antecedente.', $campo));
        }
    }

    private function validarLocalFuncionamento()
    {
        $campos = array();
        $campos[] = $this->registro->getPredioEscolar();
        $campos[] = $this->registro->getSalaOutraEscola();
        $campos[] = $this->registro->getGalpaoRanchoPaiolBarracao();
        $campos[] = $this->registro->getUnidadeAtendimentoSocioeducativa();
        $campos[] = $this->registro->getUnidadePrisional();
        $campos[] = $this->registro->getOutroLocal();

        // Se nenhum dos campos foi 1...
        if (!in_array(1, $campos)) {
            $this->log(
                '"Local de funcionamento da escola" não foi preenchido corretamente.
                Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarPredioEscolar()
    {
        $predio = $this->registro->getPredioEscolar();
        $campo = "Local de funcionamento da escola - Prédio Escolar";

        if (!in_array($predio, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarSalaOutraEscola()
    {
        $sala = $this->registro->getSalaOutraEscola();
        $campo = "Sala(s) em outra escola";

        if (!in_array($sala, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarGalpaoRanchoPaiolBarracao()
    {
        $galpao = $this->registro->getGalpaoRanchoPaiolBarracao();
        $campo = "Galpão/rancho/paiol/barracão";

        if (!in_array($galpao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarUnidadeAtendimentoSocioeducativa()
    {
        $unidade = $this->registro->getUnidadeAtendimentoSocioeducativa();
        $campo = "Unidade de atendimento Socioeducativa";

        if (!in_array($unidade, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarUnidadePrisional()
    {
        $unidade = $this->registro->getUnidadePrisional();
        $campo = "Unidade Prisional";

        if (!in_array($unidade, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarOutroLocal()
    {
        $outro = $this->registro->getOutroLocal();
        $campo = "Outros";

        if (!in_array($outro, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarFormaOcupacaoPredio()
    {
        $ocupacao = $this->registro->getFormaOcupacaoPredio();
        $campo = "Forma de ocupação do prédio";

        if ($this->registro->getPredioEscolar() != 1) {
            if (!empty($ocupacao)) {
                $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            }

            return;
        }

        if (empty($ocupacao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($ocupacao, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarPredioEscolarCompartilhado()
    {
        $compartilhado = $this->registro->getPredioEscolarCompartilhado();
        $predioEscolar = $this->registro->getPredioEscolar();
        $campo = "Prédio escolar compartilhado com outra escola";

        if (!$this->isPreenchido($compartilhado) && $predioEscolar == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($compartilhado) && $predioEscolar != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($compartilhado)) {
            return;
        }

        if (!in_array($compartilhado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCodigoEscolaCompartilha()
    {
        $campos = array();
        $campos[] = $this->registro->getCodigoEscolaCompartilha1();
        $campos[] = $this->registro->getCodigoEscolaCompartilha2();
        $campos[] = $this->registro->getCodigoEscolaCompartilha3();
        $campos[] = $this->registro->getCodigoEscolaCompartilha4();
        $campos[] = $this->registro->getCodigoEscolaCompartilha5();
        $campos[] = $this->registro->getCodigoEscolaCompartilha6();

        // Remover códigos que não foram preenchidos...
        $campos = array_diff($campos, array(null));

        // Se algum dos códigos estava duplicado...
        if (count(array_unique($campos)) < count($campos)) {
            $this->log(
                '"Código da escola com a qual compartilha" não foi preenchido corretamente.
                Não pode haver dois códigos da escola com a qual compartilha iguais.'
            );
        }
    }

    private function validarCodigoEscolaCompartilha1()
    {
        $codigo = $this->registro->getCodigoEscolaCompartilha1();
        $campo = "Código da escola com a qual compartilha (1)";

        if ($this->registro->getPredioEscolarCompartilhado() != 1) {
            if (!empty($codigo)) {
                $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            }

            return;
        }

        if (empty($codigo)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (strlen($codigo) != 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro00->getCodigoInep() == $codigo) {
            $this->log(sprintf('O campo "%s" foi preenchido com o código da escola informante.', $campo));
        }
    }

    private function validarCodigoEscolaCompartilha2()
    {
        $codigo = $this->registro->getCodigoEscolaCompartilha2();
        $campo = "Código da escola com a qual compartilha (2)";

        if (empty($codigo) || $this->registro->getPredioEscolarCompartilhado() != 1) {
            return;
        }

        if (strlen($codigo) != 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro00->getCodigoInep() == $codigo) {
            $this->log(sprintf('O campo "%s" foi preenchido com o código da escola informante.', $campo));
        }
    }

    private function validarCodigoEscolaCompartilha3()
    {
        $codigo = $this->registro->getCodigoEscolaCompartilha3();
        $campo = "Código da escola com a qual compartilha (3)";

        if (empty($codigo) || $this->registro->getPredioEscolarCompartilhado() != 1) {
            return;
        }

        if (strlen($codigo) != 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro00->getCodigoInep() == $codigo) {
            $this->log(sprintf('O campo "%s" foi preenchido com o código da escola informante.', $campo));
        }
    }

    private function validarCodigoEscolaCompartilha4()
    {
        $codigo = $this->registro->getCodigoEscolaCompartilha4();
        $campo = "Código da escola com a qual compartilha (4)";

        if (empty($codigo) || $this->registro->getPredioEscolarCompartilhado() != 1) {
            return;
        }

        if (strlen($codigo) != 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro00->getCodigoInep() == $codigo) {
            $this->log(sprintf('O campo "%s" foi preenchido com o código da escola informante.', $campo));
        }
    }

    private function validarCodigoEscolaCompartilha5()
    {
        $codigo = $this->registro->getCodigoEscolaCompartilha5();
        $campo = "Código da escola com a qual compartilha (5)";

        if (empty($codigo) || $this->registro->getPredioEscolarCompartilhado() != 1) {
            return;
        }

        if (strlen($codigo) != 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro00->getCodigoInep() == $codigo) {
            $this->log(sprintf('O campo "%s" foi preenchido com o código da escola informante.', $campo));
        }
    }

    private function validarCodigoEscolaCompartilha6()
    {
        $codigo = $this->registro->getCodigoEscolaCompartilha6();
        $campo = "Código da escola com a qual compartilha (6)";

        if (empty($codigo) || $this->registro->getPredioEscolarCompartilhado() != 1) {
            return;
        }

        if (strlen($codigo) != 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro00->getCodigoInep() == $codigo) {
            $this->log(sprintf('O campo "%s" foi preenchido com o código da escola informante.', $campo));
        }
    }

    private function validarForneceAguaPotavel()
    {
        $agua = $this->registro->getForneceAguaPotavel();
        $campo = "Fornece água potável para o consumo humano";

        if (!in_array($agua, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAbastecimentoAgua()
    {
        $campos = array();
        $campos[] = $this->registro->getAguaRedePublica();
        $campos[] = $this->registro->getPocoArtesiano();
        $campos[] = $this->registro->getCacimbaCisternaPoco();
        $campos[] = $this->registro->getFonteRio();
        $campos[] = $this->registro->getSemAgua();

        // Se nenhum dos campos era 1...
        if (!in_array(1, $campos)) {
            $this->log(
                '"Abastecimento de água" não foi preenchido corretamente.
                Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarAguaRedePublica()
    {
        $publica = $this->registro->getAguaRedePublica();
        $campo = "Abastecimento de água - Rede pública";

        if (!in_array($publica, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro->getSemAgua() == 1 && $publica == 1) {
            $this->log(
                sprintf('O campo "%s" não pode ser preenchido com "Sim" quando o campo
                "Não há abastecimento de água" for preenchido com "Sim".', $campo)
            );
        }
    }

    private function validarPocoArtesiano()
    {
        $poco = $this->registro->getPocoArtesiano();
        $campo = "Abastecimento de água - Poço artesiano";

        if (!in_array($poco, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro->getSemAgua() == 1 && $poco == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há abastecimento de água"' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarCacimbaCisternaPoco()
    {
        $cacimba = $this->registro->getCacimbaCisternaPoco();
        $campo = "Abastecimento de água - Cacimba/Cisterna/Poço";

        if (!in_array($cacimba, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro->getSemAgua() == 1 && $cacimba == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há abastecimento de água"' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarFonteRio()
    {
        $fonte = $this->registro->getFonteRio();
        $campo = "Abastecimento de água - Fonte/Rio/Igarapé/Riacho/Córrego.";

        if (!in_array($fonte, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro->getSemAgua() == 1 && $fonte == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há abastecimento de água"' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSemAgua()
    {
        $semAgua = $this->registro->getSemAgua();
        $campo = "Abastecimento de água - Rede pública";

        if (!in_array($semAgua, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCarroPipa()
    {
        $carroPipa = $this->registro->getCarroPipa();
        $campo = "Abastecimento de água - Carro-pipa";

        if (!$this->isPreenchido($carroPipa)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($carroPipa, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro->getSemAgua() == 1 && $carroPipa == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há abastecimento de água"' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarFonteEnergiaEletrica()
    {
        $campos = array();
        $campos[] = $this->registro->getLuzRedePublica();
        $campos[] = $this->registro->getGeradorCombustivelFossil();
        $campos[] = $this->registro->getEnergiaRenovavel();
        $campos[] = $this->registro->getSemEnergiaEletrica();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Fonte de energia elétrica" não foi preenchido corretamente.' .
                'Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarLuzRedePublica()
    {
        $publica = $this->registro->getLuzRedePublica();
        $campo = "Fonte de energia elétrica - Rede pública";

        if (!in_array($publica, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($this->registro->getSemEnergiaEletrica() == 1 && $publica == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há energia elétrica"' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarGeradorCombustivelFossil()
    {
        $gerador = $this->registro->getGeradorCombustivelFossil();
        $campo = "Fonte de energia elétrica - Gerador movido a combustível fóssil";

        if (!in_array($gerador, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($this->registro->getSemEnergiaEletrica() == 1 && $gerador == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há energia elétrica"' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEnergiaRenovavel()
    {
        $renovavel = $this->registro->getEnergiaRenovavel();
        $campo = "Fonte de energia elétrica - Fontes de energia renováveis ou alternativas ";
        $campo .= "(gerador a biocombustível e/ou biodigestores, eólica, solar, outras)";

        if (!in_array($renovavel, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($this->registro->getSemEnergiaEletrica() == 1 && $renovavel == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Não há energia elétrica" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSemEnergiaEletrica()
    {
        $semEnergia = $this->registro->getSemEnergiaEletrica();
        $campo = "Fonte de energia elétrica - Rede pública";

        if (!in_array($semEnergia, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarEsgotamentoSanitario()
    {
        $campos = array();
        $campos[] = $this->registro->getEsgotoRedePublica();
        $campos[] = $this->registro->getFossaSeptica();
        $campos[] = $this->registro->getFossaRudimentar();
        $campos[] = $this->registro->getSemEsgotamentoSanitario();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Esgotamento sanitário" não foi preenchido corretamente. ' .
                'Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarEsgotoRedePublica()
    {
        $publica = $this->registro->getEsgotoRedePublica();
        $campo = "Esgotamento sanitário - Rede pública";

        if (!in_array($publica, array(0, 1))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($publica == 1 && $this->registro->getSemEsgotamentoSanitario() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Não há esgotamento sanitário" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarFossaSeptica()
    {
        $fossa = $this->registro->getFossaSeptica();
        $campo = "Esgotamento sanitário - Fossa séptica";

        if (!in_array($fossa, array(0, 1))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($fossa == 1 && $this->registro->getSemEsgotamentoSanitario() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Não há esgotamento sanitário" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarFossaRudimentar()
    {
        $fossa = $this->registro->getFossaRudimentar();
        $campo = "Esgotamento sanitário - Fossa rudimentar/comum";

        if (!in_array($fossa, array(0, 1))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($fossa == 1 && $this->registro->getSemEsgotamentoSanitario() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há esgotamento sanitário"' .
                ' for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSemEsgotamentoSanitario()
    {
        $semEsgoto = $this->registro->getSemEsgotamentoSanitario();
        $campo = "Esgotamento sanitário - Não há esgotamento sanitário";

        if (!in_array($semEsgoto, array(0, 1))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarDestinacaoLixo()
    {
        $campos = array();
        $campos[] = $this->registro->getServicoColeta();
        $campos[] = $this->registro->getQueimaLixo();
        $campos[] = $this->registro->getEnterraLixo();
        $campos[] = $this->registro->getLevaLixo();
        $campos[] = $this->registro->getDescartaLixo();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Destinação do lixo" não foi preenchido corretamente. ' .
                'Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarServicoColeta()
    {
        $coleta = $this->registro->getServicoColeta();
        $campo = "Destinação do lixo - Serviço de coleta";

        if (!in_array($coleta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com um valor inválido.', $campo));
            return;
        }
    }

    private function validarQueimaLixo()
    {
        $queima = $this->registro->getQueimaLixo();
        $campo = "Destinação do lixo - Queima";

        if (!in_array($queima, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com um valor inválido.', $campo));
            return;
        }
    }

    private function validarEnterraLixo()
    {
        $enterra = $this->registro->getEnterraLixo();
        $campo = "Destinação do lixo - Enterra";

        if (!in_array($enterra, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com um valor inválido.', $campo));
            return;
        }
    }

    private function validarLevaLixo()
    {
        $destinacaoFinal = $this->registro->getLevaLixo();
        $campo = "Destinação do lixo - Leva a uma destinação final licenciada pelo poder público";

        if (!in_array($destinacaoFinal, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com um valor inválido.', $campo));
            return;
        }
    }

    private function validarDescartaLixo()
    {
        $descarta = $this->registro->getDescartaLixo();
        $campo = "Destinação do lixo - Descarta em outra área";

        if (!in_array($descarta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com um valor inválido.', $campo));
            return;
        }
    }

    private function validarTratamentoLixo()
    {
        $campos = array();
        $campos[] = $this->registro->getSeparacaoLixo();
        $campos[] = $this->registro->getReaproveitamentoLixo();
        $campos[] = $this->registro->getReciclagemLixo();
        $campos[] = $this->registro->getNaoTrataLixo();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Tratamento do lixo/resíduos que a escola realiza" não foi preenchido corretamente. ' .
                'Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarSeparacaoLixo()
    {
        $separacao = $this->registro->getSeparacaoLixo();
        $campo = "Tratamento do lixo/resíduos que a escola realiza - Separação do lixo/resíduos";

        if (!in_array($separacao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($separacao == 1 && $this->registro->getNaoTrataLixo() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não faz tratamento" ' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarReaproveitamentoLixo()
    {
        $separacao = (int) $this->registro->getSeparacaoLixo();
        $reaproveita = $this->registro->getReaproveitamentoLixo();
        $campo = "Tratamento do lixo/resíduos que a escola realiza - Reaproveitamento/reutilização";

        if ($separacao === 1 && !$this->isPreenchido($reaproveita)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($reaproveita, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($reaproveita == 1 && $this->registro->getNaoTrataLixo() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não faz tratamento" ' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarReciclagemLixo()
    {
        $separacao = (int) $this->registro->getSeparacaoLixo();
        $recicla = $this->registro->getReciclagemLixo();
        $campo = "Tratamento do lixo/resíduos que a escola realiza - Reciclagem";

        if ($separacao === 1 && !$this->isPreenchido($recicla)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($recicla, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($recicla == 1 && $this->registro->getNaoTrataLixo() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não faz tratamento"' .
                ' for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarNaoTrataLixo()
    {
        $separacao = (int) $this->registro->getSeparacaoLixo();
        $naoTrata = $this->registro->getNaoTrataLixo();
        $campo = "Tratamento do lixo/resíduos que a escola realiza - Não faz tratamento";

        if ($separacao === 1 && !$this->isPreenchido($naoTrata)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($naoTrata, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarDependenciasFisicas()
    {
        $campos = array();
        $campos[] = $this->registro->getAlmoxarifado();
        $campos[] = $this->registro->getAreaVegetacaoGramado();
        $campos[] = $this->registro->getAuditorio();
        $campos[] = $this->registro->getBanheiro();
        $campos[] = $this->registro->getBanheiroAcessivelPessoasDeficiencia();
        $campos[] = $this->registro->getBanheiroEducacaoInfantil();
        $campos[] = $this->registro->getBanheiroExclusivoFuncionarios();
        $campos[] = $this->registro->getBanheiroComChuveiro();
        $campos[] = $this->registro->getBiblioteca();
        $campos[] = $this->registro->getCozinha();
        $campos[] = $this->registro->getDespensa();
        $campos[] = $this->registro->getDormitorioAluno();
        $campos[] = $this->registro->getDormitorioProfessor();
        $campos[] = $this->registro->getLaboratorioCiencias();
        $campos[] = $this->registro->getLaboratorioInformatica();
        $campos[] = $this->registro->getLaboratorioEducacaoProfissional();
        $campos[] = $this->registro->getParqueInfantil();
        $campos[] = $this->registro->getPatiocoberto();
        $campos[] = $this->registro->getPatiodescoberto();
        $campos[] = $this->registro->getPiscina();
        $campos[] = $this->registro->getQuadraEsportesCoberta();
        $campos[] = $this->registro->getQuadraEsportesDescoberta();
        $campos[] = $this->registro->getRefeitorio();
        $campos[] = $this->registro->getSalaRepousoAluno();
        $campos[] = $this->registro->getAtelieArtes();
        $campos[] = $this->registro->getSalaMusica();
        $campos[] = $this->registro->getSalaDanca();
        $campos[] = $this->registro->getSalaMultiuso();
        $campos[] = $this->registro->getTerreirao();
        $campos[] = $this->registro->getViveiro();
        $campos[] = $this->registro->getSalaDiretoria();
        $campos[] = $this->registro->getSalaLeitura();
        $campos[] = $this->registro->getSalaProfessores();
        $campos[] = $this->registro->getSalaRecursosMultifuncionaisAEE();
        $campos[] = $this->registro->getSalaSecretaria();
        $campos[] = $this->registro->getSalaEducacaoProfissional();
        $campos[] = $this->registro->getNenhumaDependencias();
        $campos[] = $this->registro->getSalaGravacaoEdicao();
        $campos[] = $this->registro->getTradutorInterpreteLibras();
        $campos[] = $this->registro->getAreaHortaPlantioProducaoAgricola();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Dependências físicas existentes e utilizadas na escola" não foi preenchido corretamente.' .
                'Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarAlmoxarifado()
    {
        $almoxarifado = $this->registro->getAlmoxarifado();
        $campo = "Dependências físicas existentes e utilizadas na escola - Almoxarifado";

        if (!in_array($almoxarifado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($almoxarifado == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Nenhuma das dependências relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }
    
    private function validarAreaVegetacaoGramado()
    {
        $area = $this->registro->getAreaVegetacaoGramado();
        $campo = "Dependências físicas existentes e utilizadas na escola - Área de vegetação ou gramado";

        if (!in_array($area, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($area == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Nenhuma das dependências relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarAreaHortaPlantioProducaoAgricola()
    {
        $areaHorta = $this->registro->getAreaHortaPlantioProducaoAgricola();
        $areaNome = "Área de horta, plantio e/ou produção agrícola";
        $campo = "Dependências físicas existentes e utilizadas na escola - $areaNome";

        if (!in_array($areaHorta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($areaHorta == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Nenhuma das dependências relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarAudirotio()
    {
        $audirotio = $this->registro->getAuditorio();
        $campo = "Dependências físicas existentes e utilizadas na escola - Auditório";

        if (!in_array($audirotio, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($audirotio == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Nenhuma das dependências relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarBanheiro()
    {
        $banheiro = $this->registro->getBanheiro();
        $campo = "Dependências físicas existentes e utilizadas na escola - Banheiro";

        if (!in_array($banheiro, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($banheiro == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Nenhuma das dependências relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }

        $banheiros = [
            $this->registro->getBanheiroAcessivelPessoasDeficiencia(),
            $this->registro->getBanheiroEducacaoInfantil(),
            $this->registro->getBanheiroExclusivoFuncionarios(),
            $this->registro->getBanheiroComChuveiro(),
        ];

        if ($banheiro != 1 && in_array(1, $banheiros)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarBanheiroAcessivelPessoasDeficiencia()
    {
        $banheiroDeficiencia = $this->registro->getBanheiroAcessivelPessoasDeficiencia();
        $campo = "Dependências físicas existentes e utilizadas na escola - Banheiro acessível adequado ao uso de ";
        $campo .= "pessoas com deficiência ou mobilidade reduzida";

        if (!in_array($banheiroDeficiencia, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($banheiroDeficiencia == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo ' .
                '"Nenhuma das dependências relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarBanheiroEducacaoInfantil()
    {
        $banheiroInfantil = $this->registro->getBanheiroEducacaoInfantil();
        $campo = "Dependências físicas existentes e utilizadas na escola - Banheiro adequado à educação infantil";

        if (!in_array($banheiroInfantil, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($banheiroInfantil == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das ' .
                'dependências relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarBanheiroExclusivoFuncionarios()
    {
        $banheiroFuncionarios = $this->registro->getBanheiroExclusivoFuncionarios();
        $campo = "Dependências físicas existentes e utilizadas na escola - Banheiro exclusivo para os funcionários";

        if (!in_array($banheiroFuncionarios, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($banheiroFuncionarios == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarBanheiroComChuveiro()
    {
        $banheiroChuveiro = $this->registro->getBanheiroComChuveiro();
        $campo = "Dependências físicas existentes e utilizadas na escola - Banheiro ou vestiário com chuveiro";

        if (!in_array($banheiroChuveiro, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($banheiroChuveiro == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarBiblioteca()
    {
        $biblioteca = $this->registro->getBiblioteca();
        $campo = "Dependências físicas existentes e utilizadas na escola - ";

        if (!in_array($biblioteca, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($biblioteca == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarCozinha()
    {
        $cozinha = $this->registro->getCozinha();
        $campo = "Dependências físicas existentes e utilizadas na escola - Cozinha";

        if (!in_array($cozinha, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($cozinha == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarDespensa()
    {
        $despensa = $this->registro->getDespensa();
        $campo = "Dependências físicas existentes e utilizadas na escola - Despensa";

        if (!in_array($despensa, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($despensa == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarDormitorioAluno()
    {
        $dormitorioAluno = $this->registro->getDormitorioAluno();
        $campo = "Dependências físicas existentes e utilizadas na escola - Dormitório de aluno(a)";

        if (!in_array($dormitorioAluno, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dormitorioAluno == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarDormitorioProfessor()
    {
        $dormitorioProfessor = $this->registro->getDormitorioProfessor();
        $campo = "Dependências físicas existentes e utilizadas na escola - Dormitório de professor(a)";

        if (!in_array($dormitorioProfessor, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dormitorioProfessor == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarLaboratorioCiencias()
    {
        $laboratorioCiencia = $this->registro->getLaboratorioCiencias();
        $campo = "Dependências físicas existentes e utilizadas na escola - Laboratório de ciências";

        if (!in_array($laboratorioCiencia, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($laboratorioCiencia == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarLaboratorioInformatica()
    {
        $laboratorioInformatica = $this->registro->getLaboratorioInformatica();
        $campo = "Dependências físicas existentes e utilizadas na escola - Laboratório de informática";

        if (!in_array($laboratorioInformatica, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($laboratorioInformatica == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarLaboratorioEducacaoProfissional()
    {
        $laboratorioEducacaoProfissional = $this->registro->getLaboratorioEducacaoProfissional();
        $campo = "
            Dependências físicas existentes e utilizadas na escola - Laboratório Específico para a Educação Profissional
            ";

        if (!in_array($laboratorioEducacaoProfissional, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($laboratorioEducacaoProfissional == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarParqueInfantil()
    {
        $parqueInfantil = $this->registro->getParqueInfantil();
        $campo = "Dependências físicas existentes e utilizadas na escola - Parque infantil";

        if (!in_array($parqueInfantil, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($parqueInfantil == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarPatioCoberto()
    {
        $patioCoberto = $this->registro->getPatiocoberto();
        $campo = "Dependências físicas existentes e utilizadas na escola - Pátio coberto";

        if (!in_array($patioCoberto, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($patioCoberto == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarPatioDescoberto()
    {
        $patioDescoberto = $this->registro->getPatiodescoberto();
        $campo = "Dependências físicas existentes e utilizadas na escola - Pátio descoberto";

        if (!in_array($patioDescoberto, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($patioDescoberto == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarPiscina()
    {
        $piscina = $this->registro->getPiscina();
        $campo = "Dependências físicas existentes e utilizadas na escola - Piscina";

        if (!in_array($piscina, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($piscina == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarQuadraEsportesCoberta()
    {
        $quadraCoberta = $this->registro->getQuadraEsportesCoberta();
        $campo = "Dependências físicas existentes e utilizadas na escola - Quadra de esportes coberta";

        if (!in_array($quadraCoberta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($quadraCoberta == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarQuadraEsportesDescoberta()
    {
        $quadraDescoberta = $this->registro->getQuadraEsportesDescoberta();
        $campo = "Dependências físicas existentes e utilizadas na escola - Quadra de esportes descoberta";

        if (!in_array($quadraDescoberta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($quadraDescoberta == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarRefeitorio()
    {
        $refeitorio = $this->registro->getRefeitorio();
        $campo = "Dependências físicas existentes e utilizadas na escola - Refeitório";

        if (!in_array($refeitorio, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($refeitorio == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaRepousoAluno()
    {
        $salaRepouso = $this->registro->getSalaRepousoAluno();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de repouso para aluno(a)";

        if (!in_array($salaRepouso, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaRepouso == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarAtelieArtes()
    {
        $atelie = $this->registro->getAtelieArtes();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala/ateliê de artes";

        if (!in_array($atelie, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($atelie == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaMusica()
    {
        $salaMusica = $this->registro->getSalaMusica();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de música/coral";

        if (!in_array($salaMusica, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaMusica == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaDanca()
    {
        $salaDanca = $this->registro->getSalaDanca();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala/estúdio de dança";

        if (!in_array($salaDanca, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaDanca == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaMultiuso()
    {
        $salaMultiuso = $this->registro->getSalaMultiuso();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala multiuso (música, dança e artes)";

        if (!in_array($salaMultiuso, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaMultiuso == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarTerreirao()
    {
        $terreirao = $this->registro->getTerreirao();
        $campo = "Dependências físicas existentes e utilizadas na escola - Terreirão ";
        $campo .= "(área para prática desportiva e recreação sem cobertura, sem piso e sem edificações)";

        if (!in_array($terreirao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($terreirao == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarViveiro()
    {
        $viveiro = $this->registro->getViveiro();
        $campo = "Dependências físicas existentes e utilizadas na escola - Viveiro/criação de animais";

        if (!in_array($viveiro, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($viveiro == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaDiretoria()
    {
        $salaDiretoria = $this->registro->getSalaDiretoria();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de diretoria";

        if (!in_array($salaDiretoria, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaDiretoria == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaLeitura()
    {
        $salaLeitura = $this->registro->getSalaLeitura();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de Leitura";

        if (!in_array($salaLeitura, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaLeitura == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaProfessores()
    {
        $salaProfessores = $this->registro->getSalaProfessores();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de professores";

        if (!in_array($salaProfessores, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaProfessores == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaRecursosMultifuncionaisAEE()
    {
        $salaAee = $this->registro->getSalaRecursosMultifuncionaisAEE();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de recursos multifuncionais ";
        $campo .= "para atendimento educacional especializado (AEE)";

        if (!in_array($salaAee, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaAee == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaSecretaria()
    {
        $salaSecretaria = $this->registro->getSalaSecretaria();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de Secretaria";

        if (!in_array($salaSecretaria, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaSecretaria == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSalaEducacaoProfissional()
    {
        $salaEducacaoProfissional = $this->registro->getSalaEducacaoProfissional();
        $campo = "Dependências físicas existentes e utilizadas na escola - Sala de Oficinas da Educação Profissional";

        if (!in_array($salaEducacaoProfissional, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaEducacaoProfissional == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    public function validarSalasGravacaoEdicao()
    {
        $salaGravacaoEdicao = $this->registro->getSalaGravacaoEdicao();
        $campo = "Estúdio de gravação e edição";

        if (!in_array($salaGravacaoEdicao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($salaGravacaoEdicao == 1 && $this->registro->getNenhumaDependencias() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhuma das dependências ' .
                'relacionadas" for preenchido com "Sim".',
                $campo
            ));
        }
    }
    private function validarNenhumaDependencias()
    {
        $nenhumaDependencias = $this->registro->getNenhumaDependencias();
        $campo = "Dependências físicas existentes e utilizadas na escola - Nenhuma das dependências relacionadas";

        if (!in_array($nenhumaDependencias, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarRecursosAcessibilidade()
    {
        $campos = array();
        $campos[] = $this->registro->getCorrimao();
        $campos[] = $this->registro->getElevador();
        $campos[] = $this->registro->getPisoTatil();
        $campos[] = $this->registro->getPortasComVao80Cm();
        $campos[] = $this->registro->getRampas();
        $campos[] = $this->registro->getSinalizacaoSonora();
        $campos[] = $this->registro->getSinalizacaoTatil();
        $campos[] = $this->registro->getSinalizacaoVisual();
        $campos[] = $this->registro->getSinalizacaoAlarmeLuminoso();
        $campos[] = $this->registro->getNenhumRecursosAcessibilidade();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ' .
                'circulação internas na escola" não foram preenchidos corretamente. Não podem ser informadas todas as' .
                ' opções com valor igual a "Não".'
            );
        }
    }

    private function validarCorrimao()
    {
        $corrimao = $this->registro->getCorrimao();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ";
        $campo .= "circulação internas na escola - Corrimão e guarda-corpos";

        if (!in_array($corrimao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($corrimao == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarElevador()
    {
        $elevador = $this->registro->getElevador();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ";
        $campo .= "circulação internas na escola - Elevador";

        if (!in_array($elevador, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($elevador == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarPisoTatil()
    {
        $pisoTatil = $this->registro->getPisoTatil();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de '.
        'circulação internas na escola - Pisos táteis";

        if (!in_array($pisoTatil, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($pisoTatil == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarPortasComVao80Cm()
    {
        $portas = $this->registro->getPortasComVao80Cm();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ";
        $campo .= "circulação internas na escola - Portas com vão livre de no mínimo 80 cm";

        if (!in_array($portas, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($portas == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarRampas()
    {
        $rampas = $this->registro->getRampas();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ";
        $campo .= "circulação internas na escola - Rampas";

        if (!in_array($rampas, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($rampas == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSinalizacaoSonora()
    {
        $sinalizacao = $this->registro->getSinalizacaoSonora();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de '.
        'circulação internas na escola - Sinalização sonora";

        if (!in_array($sinalizacao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($sinalizacao == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSinalizacaoTatil()
    {
        $sinalizacaoTatil = $this->registro->getSinalizacaoTatil();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ";
        $campo .= "circulação internas na escola - Sinalização tátil";

        if (!in_array($sinalizacaoTatil, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($sinalizacaoTatil == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarSinalizacaoVisual()
    {
        $sinalizacaoVisual = $this->registro->getSinalizacaoVisual();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ";
        $campo .= "circulação internas na escola - Sinalização visual (piso/paredes)";

        if (!in_array($sinalizacaoVisual, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($sinalizacaoVisual == 1 && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarNenhumRecursosAcessibilidade()
    {
        $nenhumRecurso = $this->registro->getNenhumRecursosAcessibilidade();
        $campo = "Recursos de acessibilidade para pessoas com deficiência ou mobilidade reduzida nas vias de ";
        $campo .= "circulação internas na escola - Nenhum dos recursos de acessibilidade listados";

        if (!in_array($nenhumRecurso, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarNumeroSalasDentroPredioEscolar()
    {
        $salasDentro = $this->registro->getNumeroSalasDentroPredioEscolar();
        $campo = "Número de salas de aula utilizadas na escola dentro do prédio escolar";

        if ($this->registro->getPredioEscolar() == 1 &&
            !$this->isPreenchido($salasDentro)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->registro->getPredioEscolar() == 0 &&
            $this->isPreenchido($salasDentro)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($salasDentro)) {
            return;
        }

        if (strlen($salasDentro) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($salasDentro)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$salasDentro) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarNumeroSalasForaPredioEscolar()
    {
        $salasFora = $this->registro->getNumeroSalasForaPredioEscolar();
        $campo = "Número de salas de aula utilizadas na escola fora do prédio escolar";

        if ($this->registro->getPredioEscolar() == 0 &&
            !$this->isPreenchido($salasFora)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($salasFora)) {
            return;
        }

        if (strlen($salasFora) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($salasFora)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$salasFora) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarNumeroSalasClimatizada()
    {
        $salasClimatizada = $this->registro->getNumeroSalasClimatizada();
        $salasFora = $this->registro->getNumeroSalasDentroPredioEscolar();
        $salasDentro = $this->registro->getNumeroSalasForaPredioEscolar();
        $campo = "Número de salas de aula climatizadas (ar condicionado, aquecedor ou climatizador)";

        if (!$this->isPreenchido($salasFora) && !$this->isPreenchido($salasDentro) &&
            $this->isPreenchido($salasClimatizada)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($salasClimatizada)) {
            return;
        }

        if (strlen($salasClimatizada) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($salasClimatizada)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$salasClimatizada) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        if ($salasFora + $salasDentro < $salasClimatizada) {
            $this->log(sprintf(
                'O campo "%s" não pode ser maior do que a soma dos campos "Número de salas de aula utilizadas ' .
                'na escola dentro do prédio escolar" e "Número de salas de aula utilizadas na escola fora do prédio ' .
                'escolar".',
                $campo
            ));
        }
    }

    private function validarNumeroSalasComAcessibilidade()
    {
        $salasAcessibilidade = $this->registro->getNumeroSalasComAcessibilidade();
        $salasClimatizada = $this->registro->getNumeroSalasClimatizada();
        $salasFora = $this->registro->getNumeroSalasDentroPredioEscolar();
        $salasDentro = $this->registro->getNumeroSalasForaPredioEscolar();
        $campo = "Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida";

        if (!$this->isPreenchido($salasFora) && !$this->isPreenchido($salasDentro) &&
            $this->isPreenchido($salasAcessibilidade)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($salasAcessibilidade)) {
            return;
        }

        if (strlen($salasAcessibilidade) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($salasAcessibilidade)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$salasAcessibilidade) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        if ($salasFora + $salasDentro < $salasAcessibilidade) {
            $this->log(sprintf(
                'O campo "%s" não pode ser maior do que a soma dos campos "Número de salas de aula utilizadas ' .
                'na escola dentro do prédio escolar" e "Número de salas de aula utilizadas na escola fora do prédio ' .
                'escolar".',
                $campo
            ));
        }

        if ($salasAcessibilidade + $salasClimatizada > 2 * ($salasDentro + $salasFora)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSinalizacaoAlarmeLuminoso()
    {
        $sinalizacaoAlarmeLuminoso = $this->registro->getSinalizacaoAlarmeLuminoso();
        $campo = "circulação internas na escola - Sinalização/alarme luminoso";

        if (!in_array($sinalizacaoAlarmeLuminoso, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($sinalizacaoAlarmeLuminoso == 1  && $this->registro->getNenhumRecursosAcessibilidade() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum dos recursos de ' .
                'acessibilidade listados" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEquipamentosTecnicosAdministrativos()
    {
        $campos = array();
        $campos[] = $this->registro->getAntenaParabolica();
        $campos[] = $this->registro->getComputador();
        $campos[] = $this->registro->getCopiadora();
        $campos[] = $this->registro->getImpressora();
        $campos[] = $this->registro->getImpressoraMultifuncional();
        $campos[] = $this->registro->getScanner();
        $campos[] = $this->registro->getNenhumEquipamentosListados();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Equipamentos existentes na escola para uso técnico e administrativo" não foi preenchido ' .
                ' corretamente. Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarAntenaParabolica()
    {
        $antena = $this->registro->getAntenaParabolica();
        $nenhumEqupamento = $this->registro->getNenhumEquipamentosListados();
        $campo = "Equipamentos existentes na escola - Antena parabólica";

        if (!in_array($antena, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($antena == 1 && $nenhumEqupamento == 1) {
            $this->log(sprintf(
                'O campo não pode ser preenchido com 1 (Sim) quando o campo' .
                'Nenhum dos equipamentos listados" for preenchido com 1 (Sim).',
                $campo
            ));
        }
    }

    private function validarComputador()
    {
        $computador = $this->registro->getComputador();
        $nenhumEqupamento = $this->registro->getNenhumEquipamentosListados();
        $campo = "Equipamentos existentes na escola - Computadores";

        if (!in_array($computador, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($computador == 1  && $nenhumEqupamento == 1) {
            $this->log(sprintf(
                'O campo não pode ser preenchido com 1 (Sim) quando o campo' .
                'Nenhum dos equipamentos listados" for preenchido com 1 (Sim).',
                $campo
            ));
        }
    }

    private function validarCopiadora()
    {
        $copiadora = $this->registro->getCopiadora();
        $nenhumEqupamento = $this->registro->getNenhumEquipamentosListados();
        $campo = "Equipamentos existentes na escola - Copiadora";

        if (!in_array($copiadora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($copiadora == 1 && $nenhumEqupamento == 1) {
            $this->log(sprintf(
                'O campo não pode ser preenchido com 1 (Sim) quando o campo' .
                'Nenhum dos equipamentos listados" for preenchido com 1 (Sim).',
                $campo
            ));
        }
    }

    private function validarImpressora()
    {
        $impressora = $this->registro->getImpressora();
        $nenhumEqupamento = $this->registro->getNenhumEquipamentosListados();
        $campo = "Equipamentos existentes na escola - Impressora";

        if (!in_array($impressora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($impressora == 1 && $nenhumEqupamento == 1) {
            $this->log(sprintf(
                'O campo não pode ser preenchido com 1 (Sim) quando o campo' .
                'Nenhum dos equipamentos listados" for preenchido com 1 (Sim).',
                $campo
            ));
        }
    }

    private function validarImpressoraMultifuncional()
    {
        $impressora = $this->registro->getImpressoraMultifuncional();
        $nenhumEqupamento = $this->registro->getNenhumEquipamentosListados();
        $campo = "Equipamentos existentes na escola - Impressora Multifuncional";

        if (!in_array($impressora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($impressora == 1 && $nenhumEqupamento == 1) {
            $this->log(sprintf(
                'O campo não pode ser preenchido com 1 (Sim) quando o campo' .
                'Nenhum dos equipamentos listados" for preenchido com 1 (Sim).',
                $campo
            ));
        }
    }

    private function validarScanner()
    {
        $impressora = $this->registro->getScanner();
        $nenhumEqupamento = $this->registro->getNenhumEquipamentosListados();
        $campo = "Equipamentos existentes na escola - Scanner";

        if (!in_array($impressora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($impressora == 1 && $nenhumEqupamento == 1) {
            $this->log(sprintf(
                'O campo não pode ser preenchido com 1 (Sim) quando o campo' .
                'Nenhum dos equipamentos listados" for preenchido com 1 (Sim).',
                $campo
            ));
        }
    }

    private function validarNenhumEquipamentosListados()
    {
        $nenhumEqupamento = $this->registro->getNenhumEquipamentosListados();
        $campo = "Nenhum dos Equipamentos existentes na escola";

        if (!in_array($nenhumEqupamento, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAparelhoDVDBluray()
    {
        $dvd = $this->registro->getAparelhoDVDBluray();
        $campo = "Quantidade de equipamentos para o processo ensino aprendizagem - Aparelho de DVD/Blu-ray";

        if (!$this->isPreenchido($dvd)) {
            return;
        }

        if (strlen($dvd) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($dvd)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$dvd) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarAparelhoSom()
    {
        $som = $this->registro->getAparelhoSom();
        $campo = "Quantidade de equipamentos para o processo ensino aprendizagem - Aparelho de som";

        if (!$this->isPreenchido($som)) {
            return;
        }

        if (strlen($som) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($som)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$som) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarAparelhoTelevisao()
    {
        $tv = $this->registro->getAparelhoTelevisao();
        $campo = "Quantidade de equipamentos para o processo ensino aprendizagem - Aparelho de Televisão";

        if (!$this->isPreenchido($tv)) {
            return;
        }

        if (strlen($tv) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($tv)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$tv) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarLousaDigital()
    {
        $lousa = $this->registro->getLousaDigital();
        $campo = "Quantidade de equipamentos para o processo ensino aprendizagem - Lousa digital";

        if (!$this->isPreenchido($lousa)) {
            return;
        }

        if (strlen($lousa) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($lousa)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$lousa) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarProjetorMultimidia()
    {
        $projetor = $this->registro->getProjetorMultimidia();
        $campo = "Quantidade de equipamentos para o processo ensino aprendizagem - Projetor Multimídia (Data show)";

        if (!$this->isPreenchido($projetor)) {
            return;
        }

        if (strlen($projetor) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($projetor)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$projetor) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarComputadorDesktop()
    {
        $desktop = $this->registro->getComputadorDesktop();
        $campo = "Quantidade de computadores em uso pelos alunos - Computadores de mesa (desktop)";

        if (!$this->isPreenchido($desktop)) {
            return;
        }

//        if ($this->isPreenchido($desktop) && $this->registro->getComputador() != 1) {
//            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
//        }

        if (strlen($desktop) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($desktop)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$desktop) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarComputadorPortateis()
    {
        $portateis = $this->registro->getComputadorPortateis();
        $campo = "Quantidade de computadores em uso pelos alunos - Computadores portáteis";

        if (!$this->isPreenchido($portateis)) {
            return;
        }

//        if ($this->isPreenchido($portateis) && $this->registro->getComputador() != 1) {
//            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
//        }

        if (strlen($portateis) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($portateis)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$portateis) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarTablets()
    {
        $tablets = $this->registro->getTablets();
        $campo = "Quantidade de computadores em uso pelos alunos - Tablets";

        if (!$this->isPreenchido($tablets)) {
            return;
        }

//        if ($this->isPreenchido($tablets) && $this->registro->getComputador() != 1) {
//            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
//        }

        if (strlen($tablets) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($tablets)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$tablets) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }
    }

    private function validarAcessoInternet()
    {
        $campos = array();
        $campos[] = $this->registro->getInternetParaAdministrativo();
        $campos[] = $this->registro->getInternetParaEnsino();
        $campos[] = $this->registro->getInternetParaAluno();
        $campos[] = $this->registro->getInternetParaComunidade();
        $campos[] = $this->registro->getNaoPossuiInternet();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Acesso à internet" não foi preenchido corretamente. Não podem ser informadas todas as ' .
                'opções com valor igual a "Não".'
            );
        }
    }

    private function validarInternetParaAdministrativo()
    {
        $admin = $this->registro->getInternetParaAdministrativo();
        $campo = "Acesso à internet - Para uso administrativo";

        if (!in_array($admin, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($admin == 1 && $this->registro->getNaoPossuiInternet() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não possui acesso à internet" ' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarInternetParaEnsino()
    {
        $ensino = $this->registro->getInternetParaEnsino();
        $campo = "Acesso à internet - Para uso no processo de ensino e aprendizagem";

        if (!in_array($ensino, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($ensino == 1 && $this->registro->getNaoPossuiInternet() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não possui acesso à internet" ' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarInternetParaAluno()
    {
        $paraAluno = $this->registro->getInternetParaAluno();
        $campo = "Acesso à internet - Para uso dos aluno(a)s";

        if (!in_array($paraAluno, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($paraAluno == 1 && $this->registro->getNaoPossuiInternet() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não possui acesso à internet" ' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarInternetParaComunidade()
    {
        $paraComunidade = $this->registro->getInternetParaComunidade();
        $campo = "Acesso à internet - Para uso da comunidade";

        if (!in_array($paraComunidade, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($paraComunidade == 1 && $this->registro->getNaoPossuiInternet() == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não possui acesso à internet" ' .
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarNaoPossuiInternet()
    {
        $semInternet = $this->registro->getNaoPossuiInternet();
        $campo = "Acesso à internet - Não possui acesso à internet";

        if (!in_array($semInternet, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarComputadoresDisponiveis()
    {
        $disponiveis = $this->registro->getComputadoresDisponiveis();
        $campo = "Equipamentos que os aluno(a)s usam para acessar a internet da escola - Computadores de mesa, ";
        $campo .= "portáteis e tablets da escola (no laboratório de informática, biblioteca, sala de aula etc.)";

        if (!in_array($disponiveis, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($disponiveis == 1 && !$this->preencheuAoMenosUmQuantidadeComputadoresEmUsoPelosAlunos()) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarDispositivosPessoais()
    {
        $pessoais = $this->registro->getDispositivosPessoais();
        $wireless = $this->registro->getRedeWireless();
        $campo = "Equipamentos que os aluno(a)s usam para acessar a internet da escola - Dispositivos pessoais ";
        $campo .= "(computadores portáteis, celulares, tablets etc.)";

        if (!in_array($pessoais, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }

        if ($pessoais == 1 && $wireless != 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Wireless" não for preenchido com Sim',
                $campo
            ));
        }
    }

    // TODO: Botar null condicionalmente (campo 109, regra 2)
    private function validarInternetBandaLarga()
    {
        $bandaLarga = $this->registro->getInternetBandaLarga();
        $semInternet = $this->registro->getNaoPossuiInternet();
        $campo = "Internet banda larga";

        if ($semInternet == 0 && !$this->isPreenchido($bandaLarga)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($semInternet == 1 && $this->isPreenchido($bandaLarga)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($bandaLarga)) {
            return;
        }

        if (!in_array($bandaLarga, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarRedeComputadores()
    {
        $campos = array();
        $campos[] = $this->registro->getRedeCabo();
        $campos[] = $this->registro->getRedeWireless();
        $campos[] = $this->registro->getNaoExisteRede();

        $valores = array_unique($campos);
        if ($this->registro->getComputador() === 0 && count($valores) === 1 && in_array(null, $valores)) {
            return;
        }

        if (!in_array(1, $campos)) {
            $this->log(
                '"Rede local de interligação de computadores" não foi preenchido corretamente. Não podem ' .
                'ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarRedeCabo()
    {
        $cabo = $this->registro->getRedeCabo();
        $computador = $this->registro->getComputador();
        $campo = "Rede local de interligação de computadores - A cabo";

        if ($computador == 0 && !$this->preencheuAoMenosUmQuantidadeComputadoresEmUsoPelosAlunos()
            && $this->isPreenchido($cabo)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($cabo, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro->getNaoExisteRede() == 1 && $cabo == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há rede local interligando ' .
                'computadores" for preenchido com "Sim"',
                $campo
            ));
        }
    }

    private function validarRedeWireless()
    {
        $wireless = $this->registro->getRedeWireless();
        $cabo = $this->registro->getRedeCabo();
        $campo = "Rede local de interligação de computadores - Wireless";

        if ($this->isPreenchido($cabo) && !$this->isPreenchido($wireless)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($cabo) && $this->isPreenchido($wireless)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($wireless, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->registro->getNaoExisteRede() == 1 && $wireless == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há rede local interligando ' .
                'computadores" for preenchido com "Sim"',
                $campo
            ));
        }
    }

    private function validarNaoExisteRede()
    {
        $semRede = $this->registro->getNaoExisteRede();
        $cabo = $this->registro->getRedeCabo();
        $campo = "Rede local de interligação de computadores - Não há rede local interligando computadores";

        if ($this->isPreenchido($cabo)  && !$this->isPreenchido($semRede)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($cabo) && $this->isPreenchido($semRede)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($semRede, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function verificaConsistenciaPreenchimentoProfissionais($campo, $isPreenchido, $naoHaFuncionarios)
    {
        if ($isPreenchido && $naoHaFuncionarios) {
            $this->log(sprintf('
            O campo %s não pode ser preenchido com "Sim" quando o campo "Não há funcionários para as funções "'.
            'listadas for preenchido com "Sim".
            ', $campo));
        }
    }

    private function validarProfissionaisEscola()
    {
        $campos = array();
        $campos[] = $this->registro->getAuxiliarSecretariaAdministrativos();
        $campos[] = $this->registro->getAuxiliarServicosGerais();
        $campos[] = $this->registro->getBibliotecario();
        $campos[] = $this->registro->getBombeiro();
        $campos[] = $this->registro->getCoordenador();
        $campos[] = $this->registro->getFonoaudiologo();
        $campos[] = $this->registro->getNutricionista();
        $campos[] = $this->registro->getPsicologo();
        $campos[] = $this->registro->getProfissionaisPreparacaoSeguraca();
        $campos[] = $this->registro->getProfissionaisApoio();
        $campos[] = $this->registro->getSecretario();
        $campos[] = $this->registro->getSeguranca();
        $campos[] = $this->registro->getTecnicosMonitores();
        $campos[] = $this->registro->getGestoresEscola();
        $campos[] = $this->registro->getOrientadorComunitario();

        $iguais = array_unique($campos);

        if (count($iguais) == 1 && is_null($iguais[0])) {
            $this->log(
                '"Total de profissionais que atuam nas seguintes funções na escola" ' .
                'não foi preenchido corretamente.'
            );
        }
    }

    private function validarAuxiliarSecretariaAdministrativos()
    {
        $administrativo = $this->registro->getAuxiliarSecretariaAdministrativos();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Auxiliares de secretaria ou ";
        $campo .= "auxiliares administrativos, atendentes";

        if (!$this->isPreenchido($administrativo)) {
            return;
        }

        if (strlen($administrativo) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($administrativo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$administrativo) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($administrativo),
            $naoHaFuncionarios == 1
        );
    }

    private function validarAgronomosHorticultoresTecnicosMonitoresGestaoHorta()
    {
        $agronomosTecnicos = $this->registro->getAgronomosTecnicosResponsaveisProducaoAgricola();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Agrônomos(as), ";
        $campo .= "horticultores(as), técnicos ou monitores(as) responsáveis pela gestão da área de horta, ";
        $campo .= "plantio e/ou produção agrícola";

        if (!$this->isPreenchido($agronomosTecnicos)) {
            return;
        }

        if (strlen($agronomosTecnicos) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($agronomosTecnicos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$agronomosTecnicos) == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($agronomosTecnicos),
            $naoHaFuncionarios == 1
        );
    }

    private function validarAuxiliarServicosGerais()
    {
        $servicosGerais = $this->registro->getAuxiliarServicosGerais();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Auxiliar de serviços gerais, ";
        $campo .= "porteiro(a), zelador(a), faxineiro(a), jardineiro(a)";

        if (!$this->isPreenchido($servicosGerais)) {
            return;
        }

        if (strlen($servicosGerais) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($servicosGerais)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$servicosGerais) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($servicosGerais),
            $naoHaFuncionarios == 1
        );
    }

    private function validarBibliotecario()
    {
        $bibliotecario = $this->registro->getBibliotecario();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Bibliotecário(a), auxiliar de ";
        $campo .= "biblioteca ou monitor(a) da sala de leitura";

        if (!$this->isPreenchido($bibliotecario)) {
            return;
        }

        if (strlen($bibliotecario) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($bibliotecario)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$bibliotecario) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($bibliotecario),
            $naoHaFuncionarios == 1
        );
    }

    private function validarBombeiro()
    {
        $bombeiro = $this->registro->getBombeiro();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Bombeiro(a) brigadista, ";
        $campo .= "profissionais de assistência a saúde (urgência e emergência), enfermeiro(a), técnico(a) de ";
        $campo .= "enfermagem e socorrista";

        if (!$this->isPreenchido($bombeiro)) {
            return;
        }

        if (strlen($bombeiro) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($bombeiro)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$bombeiro) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($bombeiro),
            $naoHaFuncionarios == 1
        );
    }

    private function validarCoordenador()
    {
        $coordenador = $this->registro->getCoordenador();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Coordenador(a) de ";
        $campo .= "turno/disciplinar";

        if (!$this->isPreenchido($coordenador)) {
            return;
        }

        if (strlen($coordenador) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($coordenador)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$coordenador) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($coordenador),
            $naoHaFuncionarios == 1
        );
    }

    private function validarFonoaudiologo()
    {
        $fonoaudiologo = $this->registro->getFonoaudiologo();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Fonoaudiólogo(a)";

        if (!$this->isPreenchido($fonoaudiologo)) {
            return;
        }

        if (strlen($fonoaudiologo) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($fonoaudiologo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$fonoaudiologo) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($fonoaudiologo),
            $naoHaFuncionarios == 1
        );
    }

    private function validarNutricionista()
    {
        $nutricionista = $this->registro->getNutricionista();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Nutricionista";

        if (!$this->isPreenchido($nutricionista)) {
            return;
        }

        if (strlen($nutricionista) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($nutricionista)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$nutricionista) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($nutricionista),
            $naoHaFuncionarios == 1
        );
    }

    private function validarPsicologo()
    {
        $psicologo = $this->registro->getPsicologo();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Psicólogo(a) escolar";

        if (!$this->isPreenchido($psicologo)) {
            return;
        }

        if (strlen($psicologo) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($psicologo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$psicologo) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($psicologo),
            $naoHaFuncionarios == 1
        );
    }

    private function validarProfissionaisPreparacaoSeguranca()
    {
        $profissionalCozinha = $this->registro->getProfissionaisPreparacaoSeguraca();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Profissionais de preparação e ";
        $campo .= "segurança alimentar, cozinheiro(a), merendeira e auxiliar de cozinha";

        if (!$this->isPreenchido($profissionalCozinha)) {
            return;
        }

        if (strlen($profissionalCozinha) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($profissionalCozinha)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$profissionalCozinha) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($profissionalCozinha),
            $naoHaFuncionarios == 1
        );
    }

    private function validarProfissionaisApoio()
    {
        $apoio = $this->registro->getProfissionaisApoio();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Profissionais de apoio e ";
        $campo .= "supervisão pedagógica: (pedagogo(a), coordenador(a) pedagógico(a), orientador(a) educacional, ";
        $campo .= "supervisor(a) escolar e coordenador(a) de área de ensino";

        if (!$this->isPreenchido($apoio)) {
            return;
        }

        if (strlen($apoio) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($apoio)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$apoio) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($apoio),
            $naoHaFuncionarios == 1
        );
    }

    private function validarSecretario()
    {
        $secretario = $this->registro->getSecretario();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Secretário(a) escolar";

        if (!$this->isPreenchido($secretario)) {
            return;
        }

        if (strlen($secretario) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($secretario)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$secretario) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($secretario),
            $naoHaFuncionarios == 1
        );
    }

    private function validarSeguranca()
    {
        $seguranca = $this->registro->getSeguranca();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Segurança, guarda ou segurança ";
        $campo .= "patrimonial";

        if (!$this->isPreenchido($seguranca)) {
            return;
        }

        if (strlen($seguranca) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($seguranca)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$seguranca) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($seguranca),
            $naoHaFuncionarios == 1
        );
    }

    private function validarTecnicosMonitores()
    {
        $tecnicos = $this->registro->getTecnicosMonitores();
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Técnicos(as), monitores(as) ou ";
        $campo .= "auxiliares de laboratório(s) de apoio a tecnologias educacionais ou em multimeios/multimídias ";
        $campo .= "eletrônico-digitais.";

        if (!$this->isPreenchido($tecnicos)) {
            return;
        }

        if (strlen($tecnicos) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($tecnicos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$tecnicos) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoProfissionais(
            $campo,
            $this->isPreenchido($tecnicos),
            $naoHaFuncionarios == 1
        );
    }

    private function validarAlimentacaoEscolar()
    {
        $alimentacao = $this->registro->getAlimentacaoEscolar();
        $campo = "Alimentação escolar para os aluno(a)s";

        if (!in_array($alimentacao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($alimentacao == 1) {
            if (empty($this->registros20)) {
                $this->log(sprintf(
                    'Quando o campo "%s" for preenchido com "Oferece", deve haver pelo menos um registro 20 ' .
                    'vinculado!',
                    $campo
                ));
                return;
            }

            $respostas = array();
            foreach ($this->registros20 as $reg20) {
                $respostas[] = $reg20->getTipoMediacaoDidaticoPedagogica();
            }

            if (!in_array(1, $respostas)) {
                $this->log(sprintf(
                    'O campo "%s" não pode ser preenchido com "Oferece" quando o campo "Tipo de mediação ' .
                    'didático-pedagógica" de todas as turmas da escola for preenchido com "Educação a distância".',
                    $campo
                ));
            }
        }
    }

    private function validarInstrumentosMateriais()
    {
        $campos = array();
        $campos[] = $this->registro->getAcervoMultimidia();
        $campos[] = $this->registro->getBrinquedosEducacaoInfantil();
        $campos[] = $this->registro->getMateriaisCientificos();
        $campos[] = $this->registro->getEquipamentoAmplificacaoOuDifusaoAudio();
        $campos[] = $this->registro->getInstrumentosMusicais();
        $campos[] = $this->registro->getJogosEducativos();
        $campos[] = $this->registro->getMaterialAtividadeCultural();
        $campos[] = $this->registro->getmaterialEducacaoProfissional();
        $campos[] = $this->registro->getMaterialDesportivRecreacao();
        $campos[] = $this->registro->getMaterialEducacaoIndigena();
        $campos[] = $this->registro->getMaterialEducacaoEtnicoRacial();
        $campos[] = $this->registro->getMaterialEducacaoCampo();
        $campos[] = $this->registro->getMaterialEducacaoQuilombola();
        $campos[] = $this->registro->getNenhumInstrumentoListado();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Instrumentos, materiais socioculturais e/ou pedagógicos em uso na' .
                'escola para o desenvolvimento de atividades de ensino aprendizagem"' .
                'não foi preenchido corretamente. Não podem ser informadas todas as opções' .
                'com valor igual a Não'
            );
        }
    }

    private function verificaConsistenciaPreenchimentoInstrumentosMateriais($campo, $isPreenchido, $naoHaFuncionarios)
    {
        if ($isPreenchido && $naoHaFuncionarios) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo'.
                '"Nenhum dos instrumentos listados" for preenchido com "Sim"'.'.
            ',
                $campo
            ));
        }
    }

    private function validarAcervoMultimidia()
    {
        $acervo = $this->registro->getAcervoMultimidia();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Acervo multimídia";

        if (!in_array($acervo, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($acervo),
            $nenhumInstrumento == 1
        );
    }

    private function validarBrinquedosEducacaoInfantil()
    {
        $brinquedos = $this->registro->getBrinquedosEducacaoInfantil();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Brinquedos para educação infantil";

        if (!in_array($brinquedos, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($brinquedos),
            $nenhumInstrumento == 1
        );
    }

    private function validarMateriaisCientificos()
    {
        $materiaisCientificos = $this->registro->getMateriaisCientificos();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Conjunto de materiais científicos";

        if (!in_array($materiaisCientificos, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materiaisCientificos),
            $nenhumInstrumento == 1
        );
    }

    private function validarEquipamentoAmplificacaoOuDifusaoAudio()
    {
        $audio = $this->registro->getEquipamentoAmplificacaoOuDifusaoAudio();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Equipamento para amplificação e difusão de som/áudio";

        if (!in_array($audio, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($audio),
            $nenhumInstrumento == 1
        );
    }

    private function validarInstrumentosMusicais()
    {
        $instrumentos = $this->registro->getInstrumentosMusicais();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Instrumentos musicais para conjunto, banda/fanfarra e/ou aulas ";
        $campo .= "de música";

        if (!in_array($instrumentos, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($instrumentos),
            $nenhumInstrumento == 1
        );
    }

    private function validarJogosEducativos()
    {
        $jogos = $this->registro->getJogosEducativos();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento ";
        $campo .= "de atividades de ensino aprendizagem - Jogos educativos";

        if (!in_array($jogos, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($jogos),
            $nenhumInstrumento == 1
        );
    }

    private function validarMaterialAtividadeCultural()
    {
        $materialCultura = $this->registro->getMaterialAtividadeCultural();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais para atividades culturais e artísticas";

        if (!in_array($materialCultura, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialCultura),
            $nenhumInstrumento == 1
        );
    }

    private function validarmaterialEducacaoProfissional()
    {
        $materialProfissional = $this->registro->getmaterialEducacaoProfissional();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais para atividades culturais e artísticas";

        if (!in_array($materialProfissional, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialProfissional),
            $nenhumInstrumento == 1
        );
    }

    private function validarMaterialDesportivRecreacao()
    {
        $materialRecreacao = $this->registro->getMaterialDesportivRecreacao();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais para prática desportiva e recreação";

        if (!in_array($materialRecreacao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialRecreacao),
            $nenhumInstrumento == 1
        );
    }

    private function validarMaterialEducacaoIndigena()
    {
        $educacaoIndigena = $this->registro->getMaterialEducacaoIndigena();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais pedagógicos para a escola indígena";

        if (!in_array($educacaoIndigena, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($educacaoIndigena),
            $nenhumInstrumento == 1
        );
    }

    private function validarMaterialEducacaoEtnicoRacial()
    {
        $materialRacial = $this->registro->getMaterialEducacaoEtnicoRacial();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais pedagógicos para a educação das relações étnicos ";
        $campo .= "raciais";

        if (!in_array($materialRacial, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialRacial),
            $nenhumInstrumento == 1
        );
    }

    private function validarMaterialEducacaoCampo()
    {
        $materialCampo = $this->registro->getMaterialEducacaoCampo();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais pedagógicos para a educação do campo";

        if (!in_array($materialCampo, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialCampo),
            $nenhumInstrumento == 1
        );
    }

    private function validarEquipamentosAtividadesHortaProducaoAgricola()
    {
        $materialAtividadeAgricola = $this->registro->getEquipamentosAtividadesHortaProducaoAgricola();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Equipamentos e instrumentos para atividades em área de ";
        $campo .= "horta, plantio e/ou produção agrícola";

        if (!$this->isPreenchido($materialAtividadeAgricola)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($materialAtividadeAgricola, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialAtividadeAgricola),
            $nenhumInstrumento == 1
        );
    }

    private function validarMateriaisEducacaoEscolarQuilombola()
    {
        $materialAtividadeQuilombola = $this->registro->getMaterialEducacaoQuilombola();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais pedagógicos para a educação escolar quilombola";

        if (!$this->isPreenchido($materialAtividadeQuilombola)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($materialAtividadeQuilombola, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialAtividadeQuilombola),
            $nenhumInstrumento == 1
        );
    }

    private function validarMateriaisEducacaoEscolarEspecial()
    {
        $materialAtividadeEspecial = $this->registro->getMaterialEducacaoEspecial();
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de ";
        $campo .= "atividades de ensino aprendizagem - Materiais pedagógicos para a educação especial";

        if (!$this->isPreenchido($materialAtividadeEspecial)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($materialAtividadeEspecial, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoInstrumentosMateriais(
            $campo,
            $this->isPreenchido($materialAtividadeEspecial),
            $nenhumInstrumento == 1
        );
    }

    private function validarNenhumInstrumentoListado()
    {
        $nenhumInstrumento = $this->registro->getNenhumInstrumentoListado();
        $campo = "Nenhum dos Instrumentos Listados";

        if (!in_array($nenhumInstrumento, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEducacaoEscolarIndigena()
    {
        $educacaoIndigena = $this->registro->getEducacaoEscolarIndigena();
        $campo = "Escola indígena";

        if (!in_array($educacaoIndigena, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            return;
        }
    }

    private function validarLinguaEnsinoMinistrado()
    {
        $linguaIndigena = $this->registro->getLinguaIndigena();
        $linguaPortuguesa = $this->registro->getLinguaPortuguesa();

        if ($linguaIndigena === 0 && $linguaPortuguesa === 0) {
            $this->log(
                'Os campos "Língua em que o ensino é ministrado - Língua indígena" e "Língua em que o ' .
                'ensino é ministrado - Língua portuguesa" estar ambos preenchidos com "Não".'
            );
        }
    }

    // TODO: Botar null condicionalmente (campo 145, regra 2)
    private function validarLinguaIndigena()
    {
        $linguaIndigena = $this->registro->getLinguaIndigena();
        $educacaoIndigena = $this->registro->getEducacaoEscolarIndigena();
        $campo = 'Língua em que o ensino é ministrado - Língua indígena';

        if ($educacaoIndigena == 1 && !$this->isPreenchido($linguaIndigena)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($linguaIndigena)) {
            return;
        }

        if ($educacaoIndigena != 1 && $this->isPreenchido($linguaIndigena)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($linguaIndigena, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    // TODO: Botar null condicionalmente (campo 146, regra 2)
    private function validarLinguaPortuguesa()
    {
        $linguaPortuguesa = $this->registro->getLinguaPortuguesa();
        $educacaoIndigena = $this->registro->getEducacaoEscolarIndigena();
        $campo = 'Língua em que o ensino é ministrado - Língua indígena';

        if ($educacaoIndigena == 1 && !$this->isPreenchido($linguaPortuguesa)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($linguaPortuguesa)) {
            return;
        }

        if ($educacaoIndigena != 1 && $this->isPreenchido($linguaPortuguesa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($linguaPortuguesa, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCodigoLinguaIndigena1()
    {
        $codigoLingua = $this->registro->getCodigoLinguaIndigena1();
        $linguaIndigena = $this->registro->getLinguaIndigena();
        $campo = 'Código da língua indígena 1';

        if ($linguaIndigena == 1 && !$this->isPreenchido($codigoLingua)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($linguaIndigena != 1 && $this->isPreenchido($codigoLingua)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarCodigoLinguaIndigena2()
    {
        $codigoLingua = $this->registro->getCodigoLinguaIndigena2();
        $codLinguaAnterior = $this->registro->getCodigoLinguaIndigena1();
        $campo = 'Código da língua indígena 2';

        if (!$this->isPreenchido($codigoLingua)) {
            return;
        }

        if ($this->isPreenchido($codigoLingua) && !$this->isPreenchido($codLinguaAnterior)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($codigoLingua == $codLinguaAnterior) {
            $this->log(
                sprintf('O campo "%s" não poder ser igual ao campo "Código da língua indígena 1".', $campo)
            );
        }
    }

    private function validarCodigoLinguaIndigena3()
    {
        $codigoLingua = $this->registro->getCodigoLinguaIndigena3();
        $codLingua2 = $this->registro->getCodigoLinguaIndigena2();
        $codLingua1 = $this->registro->getCodigoLinguaIndigena1();
        $campo = 'Código da língua indígena 3';

        if (!$this->isPreenchido($codigoLingua)) {
            return;
        }

        if ($this->isPreenchido($codigoLingua) && !$this->isPreenchido($codLingua2)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($codigoLingua == $codLingua2) {
            $this->log(
                sprintf('O campo "%s" não poder ser igual ao campo "Código da língua indígena 2".', $campo)
            );
        }

        if ($codigoLingua == $codLingua1) {
            $this->log(
                sprintf('O campo "%s" não poder ser igual ao campo "Código da língua indígena 1".', $campo)
            );
        }
    }

    private function validarExameSelecao()
    {
        $exame = $this->registro->getExameSelecao();
        $campo = 'A escola faz exame de seleção para ingresso de seus aluno(a)s (avaliação por prova e /ou ';
        $campo .= 'analise curricular)';

        if (!in_array($exame, array(0, 1))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarSistemaCotas()
    {
        $exameSelecao = $this->registro->getExameSelecao();

        if ($exameSelecao != 1) {
            return;
        }

        $campos = array();
        $campos[] = $this->registro->getReservaVagaPretoPardoIndigena();
        $campos[] = $this->registro->getReservaVagaRenda();
        $campos[] = $this->registro->getReservaVagaEscolaPublica();
        $campos[] = $this->registro->getReservaVagaDeficiencia();
        $campos[] = $this->registro->getReservaVagaOutro();
        $campos[] = $this->registro->getSemReservaVagas();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s" não foi ' .
                'preenchida corretamente. Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    // TODO: Botar null condicionalmente (campo 151, regra 2)
    private function validarReservaVagaPretoPardoIndigena()
    {
        $reservaPretoPardoIndigena = $this->registro->getReservaVagaPretoPardoIndigena();
        $exameSelecao = $this->registro->getExameSelecao();
        $semCota = $this->registro->getSemReservaVagas();
        $campo = 'Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s - Autodeclarado preto, ';
        $campo .= 'pardo ou indígena (PPI)';

        if ($exameSelecao == 1 && !$this->isPreenchido($reservaPretoPardoIndigena)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($exameSelecao == 0 && $this->isPreenchido($reservaPretoPardoIndigena)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($reservaPretoPardoIndigena)) {
            return;
        }

        if (!in_array($reservaPretoPardoIndigena, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($semCota == 1 && $reservaPretoPardoIndigena == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Sem reservas de vagas para ' .
                'sistema de cotas (ampla concorrência)" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    // TODO: Botar null condicionalmente (campo 152, regra 2)
    private function validarReservaVagaRenda()
    {
        $reservaRenda = $this->registro->getReservaVagaRenda();
        $exameSelecao = $this->registro->getExameSelecao();
        $semCota = $this->registro->getSemReservaVagas();
        $campo = 'Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s - Condição de renda';

        if ($exameSelecao == 1 && !$this->isPreenchido($reservaRenda)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($exameSelecao == 0 && $this->isPreenchido($reservaRenda)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($reservaRenda)) {
            return;
        }

        if (!in_array($reservaRenda, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($semCota == 1 && $reservaRenda == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Sem reservas de vagas para ' .
                'sistema de cotas (ampla concorrência)" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    // TODO: Botar null condicionalmente (campo 153, regra 2)
    private function validarReservaVagaEscolaPublica()
    {
        $reservaEscolaPublica = $this->registro->getReservaVagaEscolaPublica();
        $exameSelecao = $this->registro->getExameSelecao();
        $semCota = $this->registro->getSemReservaVagas();
        $campo = 'Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s - ';
        $campo .= 'Oriundo de escola pública';

        if ($exameSelecao == 1 && !$this->isPreenchido($reservaEscolaPublica)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($exameSelecao == 0 && $this->isPreenchido($reservaEscolaPublica)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($reservaEscolaPublica)) {
            return;
        }

        if (!in_array($reservaEscolaPublica, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($semCota == 1 && $reservaEscolaPublica == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Sem reservas de vagas para ' .
                'sistema de cotas (ampla concorrência)" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    // TODO: Botar null condicionalmente (campo 154, regra 2)
    private function validarReservaVagaDeficiencia()
    {
        $reservaDeficiencia = $this->registro->getReservaVagaDeficiencia();
        $exameSelecao = $this->registro->getExameSelecao();
        $semCota = $this->registro->getSemReservaVagas();
        $campo = 'Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s - Pessoa com deficiência ';
        $campo .= '(PCD)';

        if ($exameSelecao == 1 && !$this->isPreenchido($reservaDeficiencia)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($exameSelecao == 0 && $this->isPreenchido($reservaDeficiencia)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($reservaDeficiencia)) {
            return;
        }

        if (!in_array($reservaDeficiencia, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($semCota == 1 && $reservaDeficiencia == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Sem reservas de vagas para ' .
                'sistema de cotas (ampla concorrência)" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    // TODO: Botar null condicionalmente (campo 155, regra 2)
    private function validarReservaVagaOutro()
    {
        $reservaOutro = $this->registro->getReservaVagaOutro();
        $exameSelecao = $this->registro->getExameSelecao();
        $semCota = $this->registro->getSemReservaVagas();
        $campo = 'Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s - Outros grupos que ';
        $campo .= 'não os listados';

        if ($exameSelecao == 1 && !$this->isPreenchido($reservaOutro)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($exameSelecao == 0 && $this->isPreenchido($reservaOutro)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($reservaOutro)) {
            return;
        }

        if (!in_array($reservaOutro, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($semCota == 1 && $reservaOutro == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Sem reservas de vagas para ' .
                'sistema de cotas (ampla concorrência)" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    // TODO: Botar null condicionalmente (campo 156, regra 2)
    private function validarSemReservaVagas()
    {
        $semCota = $this->registro->getSemReservaVagas();
        $exameSelecao = $this->registro->getExameSelecao();
        $campo = 'Reserva de vagas por sistema de cotas para grupos específicos de aluno(a)s - ';

        if ($exameSelecao == 1 && !$this->isPreenchido($semCota)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($exameSelecao == 0 && $this->isPreenchido($semCota)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($semCota)) {
            return;
        }

        if (!in_array($semCota, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarPossuiSiteBlog()
    {
        $blog = $this->registro->getPossuiSiteBlog();
        $campo = 'A escola possui site ou blog ou página em redes sociais para comunicação institucional';

        if (!$this->isPreenchido($blog)) {
            return;
        }

        if (!in_array($blog, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEscolaCompartilhaEspacoComunidade()
    {
        $integracao = $this->registro->getEscolaCompartilhaEspacoComunidade();
        $campo = 'A escola compartilha espaços para atividades de integração escola-comunidade';

        if (!$this->isPreenchido($integracao)) {
            return;
        }

        if (!in_array($integracao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEscolaUsaEquipamentosParaAtividade()
    {
        $equipamentos = $this->registro->getEscolaUsaEquipamentosParaAtividade();
        $campo = 'A escola usa espaços e equipamentos do entorno escolar para atividades regulares com os aluno(a)s';

        if (!$this->isPreenchido($equipamentos)) {
            return;
        }

        if (!in_array($equipamentos, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }
    
    private function validarEscolaDesenvolveAcoesEducacaoAmbiental()
    {
        $acoesEducacaoAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campo = 'A escola desenvolve ações na área de educação ambiental';

        if (!$this->isPreenchido($acoesEducacaoAmbiental)) {
            return;
        }

        if (!in_array($acoesEducacaoAmbiental, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAssociacaoPais()
    {
        $associacaoPais = $this->registro->getAssociacaoPais();
        $semOrgao = $this->registro->getOrgaosColegiadosNenhum();
        $campo = 'Órgãos colegiados em funcionamento na escola - Associação de Pais';

        if (!$this->isPreenchido($associacaoPais)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($associacaoPais, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($associacaoPais == 1 && $semOrgao == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há órgãos colegiados em ' .
                'funcionamento" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarAssociacaoPaisMestres()
    {
        $associacaoPaisMestres = $this->registro->getAssociacaoPaisMestres();
        $semOrgao = $this->registro->getOrgaosColegiadosNenhum();
        $campo = 'Órgãos colegiados em funcionamento na escola - Associação de pais e mestres';

        if (!$this->isPreenchido($associacaoPaisMestres)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($associacaoPaisMestres, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($associacaoPaisMestres == 1 && $semOrgao == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há órgãos colegiados em ' .
                'funcionamento" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarConselhoEscolar()
    {
        $conselho = $this->registro->getConselhoEscolar();
        $semOrgao = $this->registro->getOrgaosColegiadosNenhum();
        $campo = 'Órgãos colegiados em funcionamento na escola - Conselho escolar';

        if (!$this->isPreenchido($conselho)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($conselho, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($conselho == 1 && $semOrgao == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há órgãos colegiados em ' .
                'funcionamento" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarGremioEstudantil()
    {
        $gremio = $this->registro->getGremioEstudantil();
        $semOrgao = $this->registro->getOrgaosColegiadosNenhum();
        $campo = 'Órgãos colegiados em funcionamento na escola - Grêmio estudantil';

        if (!$this->isPreenchido($gremio)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($gremio, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($gremio == 1 && $semOrgao == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há órgãos colegiados em ' .
                'funcionamento" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarOrgaosColegiadosOutros()
    {
        $outros = $this->registro->getOrgaosColegiadosOutros();
        $semOrgao = $this->registro->getOrgaosColegiadosNenhum();
        $campo = 'Órgãos colegiados em funcionamento na escola - Outros';

        if (!$this->isPreenchido($outros)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($outros, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($outros == 1 && $semOrgao == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não há órgãos colegiados em ' .
                'funcionamento" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarOrgaosColegiadosNenhum()
    {
        $semOrgao = $this->registro->getOrgaosColegiadosNenhum();
        $campo = 'Órgãos colegiados em funcionamento na escola - Não há órgãos colegiados em funcionamento';

        if (!$this->isPreenchido($semOrgao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($semOrgao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarProjetoPedagogicoAtualizado()
    {
        $projeto = $this->registro->getProjetoPedagogicoAtualizado();
        $campo = 'O projeto político pedagógico ou a proposta pedagógica da escola (conforme art. 12 da LDB) foi ';
        $campo .= 'atualizada nos últimos 12 meses até a data de referência';

        if (!$this->isPreenchido($projeto)) {
            return;
        }

        if (!in_array($projeto, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarRegisQuantidadeComputadoresEmUsoPelosAlunos()
    {
        $campo = "Quantidade de computadores em uso pelos alunos";
        $computadorDisponivel = $this->registro->getComputadoresDisponiveis();

        if ($computadorDisponivel == 1 && !$this->preencheuAoMenosUmQuantidadeComputadoresEmUsoPelosAlunos()) {
            $this->log(sprintf('O grupo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }
    }

    private function preencheuAoMenosUmQuantidadeComputadoresEmUsoPelosAlunos()
    {
        $eletronicos = [
            $this->isPreenchido($this->registro->getComputadorDesktop()),
            $this->isPreenchido($this->registro->getComputadorPortateis()),
            $this->isPreenchido($this->registro->getTablets()),
        ];

        return in_array(true, $eletronicos);
    }

    private function validarGestoresEscola()
    {
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Vice-diretor(a) ou diretor(a) ";
        $campo .= "adjunto(a), profissionais responsáveis pela gestão administrativa e/ou financeira";
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $gestoresEscola = $this->registro->getGestoresEscola();
        if (!$this->isPreenchido($gestoresEscola)) {
            return;
        }

        if (strlen($gestoresEscola) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($gestoresEscola)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$gestoresEscola) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        if ($this->isPreenchido($gestoresEscola) && $naoHaFuncionarios == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->isPreenchido($gestoresEscola) && $naoHaFuncionarios == 1) {
            $this->log(sprintf('
            O campo %s não pode ser preenchido com "Sim" quando o campo "Não há funcionários para as funções "'.
            'listadas for preenchido com "Sim".
            ', $campo));
        }
    }

    private function validarOrientadorComunitario()
    {
        $campo = "Total de profissionais que atuam nas seguintes funções na escola - Orientador(a) comunitário(a) ";
        $campo .= "ou assistente social";
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $orientador = $this->registro->getOrientadorComunitario();

        if (!$this->isPreenchido($orientador)) {
            return;
        }

        if (strlen($orientador) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($orientador)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$orientador) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        if ($this->isPreenchido($orientador) && $naoHaFuncionarios == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->isPreenchido($orientador) && $naoHaFuncionarios == 1) {
            $this->log(sprintf('
            O campo %s não pode ser preenchido com "Sim" quando o campo "Não há funcionários para as funções "'.
            'listadas for preenchido com "Sim".
            ', $campo));
        }
    }

    private function validarTradutorInterpreteLibras()
    {
        $campo =
            "Tradutor e Intérprete de Libras para atendimento em outros ambientes da escola que não seja sala de aula";
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();
        $tradutor = $this->registro->getTradutorInterpreteLibras();

        if (!$this->isPreenchido($tradutor)) {
            return;
        }

        if (strlen($tradutor) > 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($tradutor)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (((int)$tradutor) == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido apenas com zeros.', $campo));
        }

        if ($this->isPreenchido($tradutor) && $naoHaFuncionarios == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->isPreenchido($tradutor) && $naoHaFuncionarios == 1) {
            $this->log(sprintf('
            O campo %s não pode ser preenchido com "Sim" quando o campo "Não há funcionários para as funções "'.
            'listadas for preenchido com "Sim".
            ', $campo));
        }
    }

    private function validarNaoHaFuncionarios()
    {
        $campo = "Não há Funcionários para as Funções Listadas ";
        $naoHaFuncionarios = $this->registro->getNaoHaFuncionarios();

        if (!in_array($naoHaFuncionarios, array(1, null))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    // Formas de educacao ambiental da escola

    private function verificaConsistenciaPreenchimentoFormasEducacaoAmbiental(
        $campo,
        $isPreenchido,
        $nenhumaDasFuncoesListadas
    ) {
        if ($isPreenchido && $nenhumaDasFuncoesListadas) {
            $this->log(sprintf('
            O campo %s não pode ser preenchido com "Sim" quando o campo "Nenhuma das opções '.
            'listadas" for preenchido com "Sim".
            ', $campo));
        }
    }

    private function validarFormasEducacaoAmbientalDesenvolvidas()
    {
        $escolaDesenvolveAcoesAreaAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campos = array();
        $campos[] = $this->registro->getComoConteudoComponentesCamposExperiencias();
        $campos[] = $this->registro->getComoComponenteCurricularEspecialEspecifico();
        $campos[] = $this->registro->getComoEixoEstruturanteCurriculo();
        $campos[] = $this->registro->getEmEventos();
        $campos[] = $this->registro->getEmProjetosTransversaisInterdisciplinares();
        $campos[] = $this->registro->getNenhumaDasOpcoesListadas();

        if ($escolaDesenvolveAcoesAreaAmbiental == 1 && !in_array(1, $campos)) {
            $this->log(
                '"Forma(s) nas quais a educação ambiental é desenvolvida na escola" não foi preenchida ' .
                'corretamente. Não podem ser informadas todas as opções com valor igual a 0 "Não". '
            );
        }
    }

    private function validarComoConteudoComponentesCamposExperiencias()
    {
        $comoConteudosComponentes = $this->registro->getComoConteudoComponentesCamposExperiencias();
        $nenhumaDasOpcoesListadas = $this->registro->getNenhumaDasOpcoesListadas();
        $escolaDesenvolveAcoesAreaAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campo = 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola - Como ';
        $campo .= 'conteúdo dos componentes/campos de experiências presentes no currículo';

        if ($escolaDesenvolveAcoesAreaAmbiental != 1 && $comoConteudosComponentes == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
        }

        if (!in_array($comoConteudosComponentes, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoFormasEducacaoAmbiental(
            $campo,
            $comoConteudosComponentes == 1,
            $nenhumaDasOpcoesListadas == 1
        );
    }

    private function validarComoComponenteCurricularEspecialEspecifico()
    {
        $comoComponenteCurricularEspecial = $this->registro->getComoComponenteCurricularEspecialEspecifico();
        $nenhumaDasOpcoesListadas = $this->registro->getNenhumaDasOpcoesListadas();
        $escolaDesenvolveAcoesAreaAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campo = 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola - ';
        $campo .= 'Como um componente curricular especial, específico, flexível ou eletivo';

        if ($escolaDesenvolveAcoesAreaAmbiental != 1 && $comoComponenteCurricularEspecial == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
        }

        if (!in_array($comoComponenteCurricularEspecial, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoFormasEducacaoAmbiental(
            $campo,
            $comoComponenteCurricularEspecial == 1,
            $nenhumaDasOpcoesListadas == 1
        );
    }

    private function validarComoEixoEstruturanteCurriculo()
    {
        $comoEixoEstruturante = $this->registro->getComoEixoEstruturanteCurriculo();
        $nenhumaDasOpcoesListadas = $this->registro->getNenhumaDasOpcoesListadas();
        $escolaDesenvolveAcoesAreaAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campo = 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola - ';
        $campo .= 'Como um eixo estruturante do currículo';

        if ($escolaDesenvolveAcoesAreaAmbiental != 1 && $comoEixoEstruturante == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
        }

        if (!in_array($comoEixoEstruturante, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoFormasEducacaoAmbiental(
            $campo,
            $comoEixoEstruturante == 1,
            $nenhumaDasOpcoesListadas == 1
        );
    }

    private function validarEmEventos()
    {
        $emEventos = $this->registro->getEmEventos();
        $nenhumaDasOpcoesListadas = $this->registro->getNenhumaDasOpcoesListadas();
        $escolaDesenvolveAcoesAreaAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campo = 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola - ';
        $campo .= 'Em eventos';

        if ($escolaDesenvolveAcoesAreaAmbiental != 1 && $emEventos == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
        }

        if (!in_array($emEventos, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoFormasEducacaoAmbiental(
            $campo,
            $emEventos == 1,
            $nenhumaDasOpcoesListadas == 1
        );
    }

    private function validarEmProjetosTransversaisInterdisciplinares()
    {
        $emProjetosTransversais = $this->registro->getEmProjetosTransversaisInterdisciplinares();
        $nenhumaDasOpcoesListadas = $this->registro->getNenhumaDasOpcoesListadas();
        $escolaDesenvolveAcoesAreaAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campo = 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola - ';
        $campo .= 'Em projetos transversais ou interdisciplinares';

        if ($escolaDesenvolveAcoesAreaAmbiental != 1 && $emProjetosTransversais == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
        }

        if (!in_array($emProjetosTransversais, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $this->verificaConsistenciaPreenchimentoFormasEducacaoAmbiental(
            $campo,
            $emProjetosTransversais == 1,
            $nenhumaDasOpcoesListadas == 1
        );
    }

    private function validarNenhumaDasOpcoesListadas()
    {
        $nenhumaDasOpcoesListadas = $this->registro->getNenhumaDasOpcoesListadas();
        $escolaDesenvolveAcoesAreaAmbiental = $this->registro->getDesenvolveAcoesEducacaoAmbiental();
        $campo = 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola - ';
        $campo .= 'Nenhuma das opções listadas';

        if ($escolaDesenvolveAcoesAreaAmbiental != 1 && $nenhumaDasOpcoesListadas == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
        }

        if (!in_array($nenhumaDasOpcoesListadas, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }
}
