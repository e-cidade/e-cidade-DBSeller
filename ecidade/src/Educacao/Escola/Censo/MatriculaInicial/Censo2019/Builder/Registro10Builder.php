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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Builder;

use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro10;

class Registro10Builder extends BuilderFormulario
{
    const FORMA_OCUPACAO_PREDIO_PROPRIO = 1;
    const FORMA_OCUPACAO_PREDIO_ALUGADO = 2;
    const FORMA_OCUPACAO_PREDIO_CEDIDO = 3;
    /**
     * @var Registro10
     */
    private $registro;

    private $codigoInep;
    private $dadosAbaInfraEstrutura = array();
    private $dadosLinguaIndigena = array();

    public function __construct()
    {
        $this->registro = new Registro10();
    }

    public function addDadosLinguaIndigena(array $dadosLinguaIndigena)
    {
        $this->dadosLinguaIndigena = $dadosLinguaIndigena;
    }

    public function addDadosFormulario(array $dadosAbaInfraEstrutura)
    {
        $this->dadosAbaInfraEstrutura = $dadosAbaInfraEstrutura;
    }

    public function addDadosINEP($codigoInep)
    {
        $this->codigoInep = $codigoInep;
    }

    private function create()
    {
        $this->registro = new Registro10();
    }

    public function build()
    {
        $this->create();
        $this->buildLinguaIndigena();
        $this->buildInep();
        $this->buildDadosFormulario();

        if ($this->registro->getNaoPossuiInternet() == 1) {
            $this->registro->setInternetBandaLarga(null);
        }

        if ($this->registro->getPredioEscolar() == 0) {
            $this->registro->setFormaOcupacaoPredio(null);
            $this->registro->setPredioEscolarCompartilhado(null);
        }

        return $this->registro;
    }

    private function buildLinguaIndigena()
    {
        if (!empty($this->dadosLinguaIndigena['ed18_i_tipolinguain']) &&
            !empty($this->dadosLinguaIndigena['ed18_i_tipolinguapt'])) {
            $this->registro->setEducacaoEscolarIndigena(1);
            $this->registro->setLinguaIndigena($this->dadosLinguaIndigena['ed18_i_tipolinguain']);
            $this->registro->setLinguaPortuguesa($this->dadosLinguaIndigena['ed18_i_tipolinguapt']);

            if ($this->registro->getLinguaIndigena() === 1) {
                $this->registro->setCodigoLinguaIndigena1($this->dadosLinguaIndigena['ed144_i_linguaindigena1']);
                $this->registro->setCodigoLinguaIndigena2($this->dadosLinguaIndigena['ed144_i_linguaindigena2']);
                $this->registro->setCodigoLinguaIndigena2($this->dadosLinguaIndigena['ed144_i_linguaindigena3']);
            }
        }
    }

    private function buildInep()
    {
        $this->registro->setCodigoInep($this->codigoInep);
    }

    private function buildDadosFormulario()
    {
        foreach ($this->dadosAbaInfraEstrutura as $grupo => $perguntas) {
            switch ($grupo) {
                case 'outras_informacoes':
                    $this->buildOutrasInformacoes($perguntas);
                    break;
                case 'escola_abre_aos_finais_de_semana_para_a_comunidade':
                    $this->buildEscolaComunidade($perguntas);
                    break;
                case 'destinacao_do_lixo':
                    $this->buildDestinacaoLixo($perguntas);
                    break;
                case 'predio_compartilhado':
                    $this->buildPredioCompartilhado($perguntas);
                    break;
                case 'abastecimento_de_energia':
                    $this->buildAbastecimentoEnergia($perguntas);
                    break;
                case 'esgoto_sanitario':
                    $this->buildEsgotoSanitario($perguntas);
                    break;
                case 'forma_de_ocupacao_do_predio':
                    $this->buildFormaOcupacaoPredio($perguntas);
                    break;
                case 'equipamentos_existentes':
                    $this->buildEquipamentos($perguntas);
                    break;
                case 'infra-estrutura':
                    $this->buildInfraEstrutura($perguntas);
                    break;
                case 'abastecimento_de_agua':
                    $this->buildAbastecimentoAgua($perguntas);
                    break;
            }
        }
    }

    private function buildAbastecimentoAgua($pergunta)
    {
        foreach ($pergunta as $opcoes) {
            $this->registro->setForneceAguaPotavel($this->exist('fornece_agua_potavel_para_o_consumo_humano', $opcoes));
            $this->registro->setAguaRedePublica($this->exist('abastecimento_de_agua_rede_publica', $opcoes));
            $this->registro->setPocoArtesiano($this->exist('poco_artesiano', $opcoes));
            $this->registro->setCacimbaCisternaPoco($this->exist('cacimba_cisterna_poco', $opcoes));
            $this->registro->setFonteRio($this->exist('fonte_rio_igarape_riacho', $opcoes));
            $this->registro->setSemAgua($this->exist('abastecimento_de_agua_inexistente', $opcoes));
            $this->registro->setCarroPipa($this->exist('abastecimento_de_agua_carro_pipa', $opcoes));
        }
    }

    private function buildInfraEstrutura($perguntas)
    {
        if (array_key_exists('local_de_funcionamento', $perguntas)) {
            $this->buildLocalFuncionamento($perguntas['local_de_funcionamento']);
        }

        if (array_key_exists('dependencias_existentes_na_escola', $perguntas)) {
            $this->buildDependenciasEscola($perguntas['dependencias_existentes_na_escola']);
        }

        if (array_key_exists('recursos_acessibilidade_deficiencia', $perguntas)) {
            $this->buildRecursosAcessibilidade($perguntas['recursos_acessibilidade_deficiencia']);
        }

        if (array_key_exists('numero_salas_utilizadas_dentro_do_predio', $perguntas)) {
            $numero_salas_utilizadas_dentro_do_predio = $perguntas['numero_salas_utilizadas_dentro_do_predio'];
            $this->registro->setNumeroSalasDentroPredioEscolar(
                $numero_salas_utilizadas_dentro_do_predio['numero_salas_utilizadas_dentro_do_predio'][0]->resposta
            );
        }

        if (array_key_exists('numero_salas_utilizadas_fora_do_predio', $perguntas)) {
            $numero_salas_utilizadas_fora_do_predio = $perguntas['numero_salas_utilizadas_fora_do_predio'];
            $this->registro->setNumeroSalasForaPredioEscolar(
                $numero_salas_utilizadas_fora_do_predio['numero_salas_utilizadas_fora_do_predio'][0]->resposta
            );
        }

        if (array_key_exists('numero_salas_climatizadas', $perguntas)) {
            $numero_salas_climatizadas = $perguntas['numero_salas_climatizadas'];
            $this->registro->setNumeroSalasClimatizada(
                $numero_salas_climatizadas['numero_salas_climatizadas'][0]->resposta
            );
        }

        if (array_key_exists('numero_salas_acessibilidade', $perguntas)) {
            $numero_salas_acessibilidade = $perguntas['numero_salas_acessibilidade'];
            $this->registro->setNumeroSalasComAcessibilidade(
                $numero_salas_acessibilidade['numero_salas_acessibilidade'][0]->resposta
            );
        }

        if (array_key_exists('acesso_a_internet', $perguntas)) {
            $this->buildAcessoInternet($perguntas['acesso_a_internet']);
        }

        if (array_key_exists('equipamentos_alunos_usam_acessar_internet', $perguntas)) {
            $opcoes = $perguntas['equipamentos_alunos_usam_acessar_internet'];
            $this->registro->setComputadoresDisponiveis($this->exist('equipamentos_da_escola', $opcoes));
            $this->registro->setDispositivosPessoais($this->exist('equipamentos_pessoais', $opcoes));
        }

        if (array_key_exists('banda_larga', $perguntas)) {
            $this->registro->setInternetBandaLarga($this->respostaObjetiva($perguntas['banda_larga']));
        }

        if (array_key_exists('rede_local_computadores', $perguntas)) {
            $this->buildRedeLocalComputadores($perguntas['rede_local_computadores']);
        }

        if (array_key_exists('material_ensino', $perguntas)) {
            $this->buildMateriaisEnsino($perguntas['material_ensino']);
        }
    }

    private function buildLocalFuncionamento($opcoes)
    {
        $this->registro->setPredioEscolar($this->exist('predio_escolar', $opcoes));
        $this->registro->setSalaOutraEscola($this->exist('salas_em_outra_escola', $opcoes));
        $this->registro->setGalpaoRanchoPaiolBarracao($this->exist('galpao_rancho_paiol', $opcoes));
        $this->registro->setUnidadeAtendimentoSocioeducativa($this->exist('unidade_de_internacao', $opcoes));
        $this->registro->setUnidadePrisional($this->exist('unidade_prisional', $opcoes));
        $this->registro->setOutroLocal($this->exist('local_de_funcionamento_outros', $opcoes));
    }

    private function buildFormaOcupacaoPredio($perguntas)
    {
        $this->registro->setFormaOcupacaoPredio(
            $this->respostaObjetiva($perguntas['pergunta_forma_de_ocupacao_do_predio'])
        );
    }

    private function buildPredioCompartilhado($perguntas)
    {
        if (isset($perguntas['pergunta_predio_compartilhado'])) {
            foreach ($perguntas['pergunta_predio_compartilhado'] as $opcao) {
                if (is_array($opcao)) {
                    $this->registro->setPredioEscolarCompartilhado($opcao[0]->valor_resposta);
                }
            }
        }

        if ($this->registro->getPredioEscolarCompartilhado() == 1) {
            foreach ($perguntas as $pergunta => $respostas) {
                foreach ($respostas as $resposta => $opcao) {
                    switch ($resposta) {
                        case 'resposta_codigo_inep_do_predio_compartilhado_1':
                            $this->registro->setCodigoEscolaCompartilha1($opcao[0]->resposta);
                            break;
                        case 'resposta_codigo_inep_do_predio_compartilhado_2':
                            $this->registro->setCodigoEscolaCompartilha2($opcao[0]->resposta);
                            break;
                        case 'resposta_codigo_inep_do_predio_compartilhado_3':
                            $this->registro->setCodigoEscolaCompartilha3($opcao[0]->resposta);
                            break;
                        case 'resposta_codigo_inep_do_predio_compartilhado_4':
                            $this->registro->setCodigoEscolaCompartilha4($opcao[0]->resposta);
                            break;
                        case 'resposta_codigo_inep_do_predio_compartilhado_5':
                            $this->registro->setCodigoEscolaCompartilha5($opcao[0]->resposta);
                            break;
                        case 'resposta_codigo_inep_do_predio_compartilhado_6':
                            $this->registro->setCodigoEscolaCompartilha6($opcao[0]->resposta);
                            break;
                    }
                }
            }
        }
    }

    private function buildAbastecimentoEnergia($perguntas)
    {
        if (array_key_exists('pergunta_abastecimento_de_energia', $perguntas)) {
            $opcoes = $perguntas['pergunta_abastecimento_de_energia'];
            $this->registro->setSemEnergiaEletrica($this->exist('abastecimento_de_energia_inexistente', $opcoes));
            $this->registro->setEnergiaRenovavel(
                $this->exist('abastecimento_de_energia_outrosenegria_alternativa', $opcoes)
            );
            $this->registro->setLuzRedePublica($this->exist('abastecimento_de_energia_rede_publica', $opcoes));
            $this->registro->setGeradorCombustivelFossil($this->exist('gerador_movido_a_combustivel_fossil', $opcoes));
        }
    }

    private function buildEsgotoSanitario($perguntas)
    {
        if (array_key_exists('pergunta_esgoto_sanitario', $perguntas)) {
            $opcoes = $perguntas['pergunta_esgoto_sanitario'];
            $this->registro->setEsgotoRedePublica($this->exist('esgoto_rede_publica', $opcoes));
            $this->registro->setFossaRudimentar($this->exist('fossa_comum', $opcoes));
            $this->registro->setFossaSeptica($this->exist('fossa_septica', $opcoes));
            $this->registro->setSemEsgotamentoSanitario($this->exist('esgoto_inexistente', $opcoes));
        }
    }

    private function buildDestinacaoLixo($perguntas)
    {
        if (array_key_exists('pergunta_destinacao_do_lixo', $perguntas)) {
            $opcoes = $perguntas['pergunta_destinacao_do_lixo'];
            $this->registro->setDescartaLixo($this->exist('descarta_em_outra_area', $opcoes));
            $this->registro->setEnterraLixo($this->exist('enterra', $opcoes));
            $this->registro->setLevaLixo(
                $this->exist('leva_a_uma_destinaçcao_final_licenciada_pelo_poder_publico', $opcoes)
            );
            $this->registro->setQueimaLixo($this->exist('queima', $opcoes));
            $this->registro->setServicoColeta($this->exist('servico_de_coleta', $opcoes));
        }

        if (array_key_exists('tratamento_lixo', $perguntas)) {
            $opcoes = $perguntas['tratamento_lixo'];
            $this->registro->setSeparacaoLixo($this->exist('separacao_lixo', $opcoes));
            $this->registro->setReciclagemLixo($this->exist('reciclagem', $opcoes));
            $this->registro->setReaproveitamentoLixo($this->exist('reaproveitamento', $opcoes));
            $this->registro->setNaoTrataLixo($this->exist('nao_faz_tratamento', $opcoes));
        }
    }

    private function buildDependenciasEscola($opcoes)
    {
        $this->registro->setAlmoxarifado($this->exist('almoxarifado', $opcoes));
        $this->registro->setAreaVegetacaoGramado($this->exist('area_vegetacao_ou_gramado', $opcoes));
        $this->registro->setAuditorio($this->exist('auditorio', $opcoes));
        $this->registro->setBanheiro($this->exist('banheiro_dentro_do_predio', $opcoes));
        $this->registro->setBanheiroAcessivelPessoasDeficiencia(
            $this->exist('banheiro_adequado_a_alunos_com_deficiencia_ou_mobilidade_reduzida', $opcoes)
        );
        $this->registro->setBanheiroEducacaoInfantil(
            $this->exist('banheiro_adequado_a_educacao_infantil', $opcoes)
        );
        $this->registro->setBanheiroExclusivoFuncionarios(
            $this->exist('banheiro_exclusivo_para_os_funcionarios', $opcoes)
        );
        $this->registro->setBanheiroComChuveiro($this->exist('banheiro_com_chuveiro', $opcoes));
        $this->registro->setBiblioteca($this->exist('biblioteca', $opcoes));
        $this->registro->setCozinha($this->exist('cozinha', $opcoes));
        $this->registro->setDespensa($this->exist('despensa', $opcoes));
        $this->registro->setDormitorioAluno($this->exist('dormitorio_aluno', $opcoes));
        $this->registro->setDormitorioProfessor($this->exist('dormitorio_professor', $opcoes));
        $this->registro->setLaboratorioCiencias($this->exist('laboratorio_de_ciencias', $opcoes));
        $this->registro->setLaboratorioInformatica($this->exist('laboratorio_de_informatica', $opcoes));
        $this->registro->setLaboratorioEducacaoProfissional(
            $this->exist('laboratorio_educacao_profissional', $opcoes)
        );
        $this->registro->setParqueInfantil($this->exist('parque_infantil', $opcoes));
        $this->registro->setPatiocoberto($this->exist('patio_coberto', $opcoes));
        $this->registro->setPatiodescoberto($this->exist('patio_descoberto', $opcoes));
        $this->registro->setPiscina($this->exist('piscina', $opcoes));
        $this->registro->setQuadraEsportesCoberta($this->exist('quadra_de_esportes_coberta', $opcoes));
        $this->registro->setQuadraEsportesDescoberta($this->exist('quadra_de_esportes_descoberta', $opcoes));
        $this->registro->setRefeitorio($this->exist('refeitorio', $opcoes));
        $this->registro->setSalaRepousoAluno($this->exist('sala_repouso_aluno', $opcoes));
        $this->registro->setAtelieArtes($this->exist('atelie_artes', $opcoes));
        $this->registro->setSalaMusica($this->exist('sala_musica', $opcoes));
        $this->registro->setSalaDanca($this->exist('estudio_danca', $opcoes));
        $this->registro->setSalaMultiuso($this->exist('sala_multiuso', $opcoes));
        $this->registro->setTerreirao($this->exist('terreirao', $opcoes));
        $this->registro->setViveiro($this->exist('viveiro', $opcoes));
        $this->registro->setSalaDiretoria($this->exist('sala_de_diretoria', $opcoes));
        $this->registro->setSalaLeitura($this->exist('sala_de_leitura', $opcoes));
        $this->registro->setSalaProfessores($this->exist('sala_de_professores', $opcoes));
        $this->registro->setSalaProfessores($this->exist('sala_de_professores', $opcoes));
        $this->registro->setSalaRecursosMultifuncionaisAEE(
            $this->exist(
                'sala_de_recursos_multifuncionais_para_atendimento_educacional_especializado_aee',
                $opcoes
            )
        );
        $this->registro->setSalaSecretaria($this->exist('sala_de_secretaria', $opcoes));
        $this->registro->setSalaEducacaoProfissional($this->exist('salas_oficinas_educacao_profissional', $opcoes));
        $this->registro->setSalaGravacaoEdicao($this->exist('estudio_gravacao_edicao', $opcoes));
        $this->registro->setAreaHortaPlantioProducaoAgricola(
            $this->exist('area_horta_plantio_e_ou_producao_agricola', $opcoes)
        );
        $this->registro->setNenhumaDependencias($this->exist('nenhuma_das_dependencias_relacionadas', $opcoes));
    }

    private function buildRecursosAcessibilidade($opcoes)
    {
        $this->registro->setCorrimao($this->exist('corrimao_e_guarda_copos', $opcoes));
        $this->registro->setElevador($this->exist('elevador', $opcoes));
        $this->registro->setPisoTatil($this->exist('pisos_tateis', $opcoes));
        $this->registro->setPortasComVao80Cm($this->exist('portas_vao_livre', $opcoes));
        $this->registro->setRampas($this->exist('rampas', $opcoes));
        $this->registro->setSinalizacaoSonora($this->exist('sinalizacao_sonora', $opcoes));
        $this->registro->setSinalizacaoTatil($this->exist('sinalizacao_tatil', $opcoes));
        $this->registro->setSinalizacaoVisual($this->exist('sinalizacao_visual', $opcoes));
        $this->registro->setSinalizacaoAlarmeLuminoso($this->exist('sinalizacao_alarme_luminoso', $opcoes));
        $this->registro->setNenhumRecursosAcessibilidade($this->exist('nenhum_recurso_acessibilidade', $opcoes));
    }

    private function buildEquipamentos($perguntas)
    {
        if (array_key_exists('pergunta_equipamentos_existentes', $perguntas)) {
            $this->buildEquipamentosExistentes($perguntas['pergunta_equipamentos_existentes']);
            if (array_key_exists('pergunta_quantidades_equipamentos', $perguntas)) {
                $this->buildQuantidadeEquipamentos($perguntas['pergunta_quantidades_equipamentos']);
            }
            if (array_key_exists('pergunta_quantidades_computadores', $perguntas)) {
                $this->buildQuantidadeComputadores($perguntas['pergunta_quantidades_computadores']);
            }
        }
    }

    private function buildEquipamentosExistentes($opcao)
    {
        $this->registro->setAntenaParabolica($this->exist('antena_parabolica', $opcao));
        $this->registro->setComputador($this->exist('computadores', $opcao));
        $this->registro->setCopiadora($this->exist('copiadora', $opcao));
        $this->registro->setImpressora($this->exist('impressora', $opcao));
        $this->registro->setImpressoraMultifuncional($this->exist('impressora_multifuncional', $opcao));
        $this->registro->setScanner($this->exist('scanner', $opcao));
        if (!$this->exist('antena_parabolica', $opcao) &&
            !$this->exist('equipamentos_computadores_mesa', $opcao) &&
            !$this->exist('copiadora', $opcao) &&
            !$this->exist('impressora', $opcao) &&
            !$this->exist('impressora_multifuncional', $opcao) &&
            !$this->exist('scanner', $opcao)) {
            $this->registro->setNenhumEquipamentosListados(1);
        }
    }

    private function buildQuantidadeEquipamentos($opcao)
    {
        //94 a 98 Quantidade de equipamentos para o processo ensino aprendizagem
        $this->registro->setAparelhoDVDBluray($this->respostaDescritiva('dvd', $opcao));
        $this->registro->setAparelhoSom($this->respostaDescritiva('aparelho_de_som', $opcao));
        $this->registro->setAparelhoTelevisao($this->respostaDescritiva('aparelho_de_televisao', $opcao));
        $this->registro->setLousaDigital($this->respostaDescritiva('lousa_digital', $opcao));
        $this->registro->setProjetorMultimidia($this->respostaDescritiva('projetor_multimidia_data_show', $opcao));
    }

    private function buildQuantidadeComputadores($opcao)
    {
        // 99 a 101 Quantidade de computadores em uso pelos alunos
        $this->registro->setComputadorDesktop($this->respostaDescritiva('equipamentos_computadores_mesa', $opcao));
        $this->registro->setComputadorPortateis($this->respostaDescritiva('computadores_portateis', $opcao));
        $this->registro->setTablets($this->respostaDescritiva('tablets', $opcao));
    }

    private function buildAcessoInternet($opcao)
    {
        $this->registro->setInternetParaAdministrativo($this->exist('para_uso_administrativo', $opcao));
        $this->registro->setInternetParaEnsino($this->exist('para_uso_no_ensino', $opcao));
        $this->registro->setInternetParaAluno($this->exist('para_uso_dos_alunos', $opcao));
        $this->registro->setInternetParaComunidade($this->exist('para_uso_da_comunidade', $opcao));
        $this->registro->setNaoPossuiInternet($this->exist('nao_possui_acesso_internet', $opcao));
    }

    private function buildRedeLocalComputadores($opcoes)
    {
        $this->registro->setRedeCabo($this->exist('rede_cabo', $opcoes));
        $this->registro->setRedeWireless($this->exist('rede_wireless', $opcoes));
        $this->registro->setNaoExisteRede($this->exist('rede_nada', $opcoes));
    }

    private function buildOutrasInformacoes($perguntas)
    {
        if (array_key_exists('alimentacao_escolar_para_os_alunos', $perguntas)) {
            $this->buildAlimentacaoAlunos($perguntas['alimentacao_escolar_para_os_alunos']);
        }

        if (array_key_exists('total_profissionais_atuam_funcoes', $perguntas)) {
            $this->buildQuantidadeProfissionais($perguntas['total_profissionais_atuam_funcoes']);
        }

        if (array_key_exists('exame_selecao', $perguntas)) {
            $this->registro->setExameSelecao($this->respostaObjetiva($perguntas['exame_selecao']));
        }

        if (array_key_exists('reserva_de_vagas', $perguntas)) {
            $this->buildReservaVagas($perguntas['reserva_de_vagas']);
        }

        if (array_key_exists('escola_possui_site', $perguntas)) {
            $this->registro->setPossuiSiteBlog($this->respostaObjetiva($perguntas['escola_possui_site']));
        }

        if (array_key_exists('escola_usa_entorno', $perguntas)) {
            $this->registro->setEscolaUsaEquipamentosParaAtividade(
                $this->respostaObjetiva($perguntas['escola_usa_entorno'])
            );
        }

        if (array_key_exists('a_escola_desenvolve_acoes_area_educacao_ambiental', $perguntas)) {
            $this->registro->setDesenvolveAcoesEducacaoAmbiental(
                $this->respostaObjetiva($perguntas['a_escola_desenvolve_acoes_area_educacao_ambiental'])
            );
        }

        if (array_key_exists('orgaos_colegiados', $perguntas)) {
            $this->buildOrgaosColegiados($perguntas['orgaos_colegiados']);
        }

        if (array_key_exists('projeto_pedagogico_atualizado', $perguntas)) {
            $this->registro->setProjetoPedagogicoAtualizado(
                $this->respostaObjetiva($perguntas['projeto_pedagogico_atualizado'])
            );
        }

        if (array_key_exists('informe_qual_forma_educacao_ambiental_desenvolvida', $perguntas)) {
            $this->buildFormasEducacaoAmbiental($perguntas['informe_qual_forma_educacao_ambiental_desenvolvida']);
        }
    }

    private function buildQuantidadeProfissionais($opcoes)
    {
        $this->registro->setAuxiliarSecretariaAdministrativos(
            $this->respostaDescritiva('auxiliar_secretaria', $opcoes)
        );
        $this->registro->setAuxiliarServicosGerais(
            $this->respostaDescritiva('auxiliar_servicos_gerais', $opcoes)
        );
        $this->registro->setBibliotecario($this->respostaDescritiva('bibliotecario', $opcoes));
        $this->registro->setBombeiro($this->respostaDescritiva('bombeiro_ou_saude', $opcoes));
        $this->registro->setCoordenador($this->respostaDescritiva('coordenador_de_turno', $opcoes));
        $this->registro->setFonoaudiologo($this->respostaDescritiva('fonoaudiologo', $opcoes));
        $this->registro->setNutricionista($this->respostaDescritiva('nutricionista', $opcoes));
        $this->registro->setPsicologo($this->respostaDescritiva('psicologo', $opcoes));
        $this->registro->setProfissionaisPreparacaoSeguraca($this->respostaDescritiva('cozinheiro', $opcoes));
        $this->registro->setProfissionaisApoio($this->respostaDescritiva('pedagogo', $opcoes));
        $this->registro->setSecretario($this->respostaDescritiva('secretario', $opcoes));
        $this->registro->setSeguranca($this->respostaDescritiva('seguranca', $opcoes));
        $this->registro->setTecnicosMonitores($this->respostaDescritiva('auxiliar_laboratorio', $opcoes));
        $this->registro->setGestoresEscola($this->respostaDescritiva('gestores_escola', $opcoes));
        $this->registro->setOrientadorComunitario($this->respostaDescritiva('orientador_comunitario', $opcoes));
        $this->registro->setTradutorInterpreteLibras($this->respostaDescritiva('tradutor_interprete_libras', $opcoes));
        $this->registro->setAgronomosTecnicosResponsaveisProducaoAgricola(
            $this->respostaDescritiva('agronomos_tecnicos_responsaveis_producao_agricola', $opcoes)
        );
        $this->registro->setRevisorTextoBrailleAssistenteVidente(
            $this->respostaDescritiva('revisor_de_texto_braille_assistente_vidente', $opcoes)
        );

        if (!$this->respostaDescritiva('bibliotecario', $opcoes) &&
            !$this->respostaDescritiva('bombeiro_ou_saude', $opcoes) &&
            !$this->respostaDescritiva('coordenador_de_turno', $opcoes) &&
            !$this->respostaDescritiva('fonoaudiologo', $opcoes) &&
            !$this->respostaDescritiva('nutricionista', $opcoes) &&
            !$this->respostaDescritiva('psicologo', $opcoes) &&
            !$this->respostaDescritiva('cozinheiro', $opcoes) &&
            !$this->respostaDescritiva('pedagogo', $opcoes) &&
            !$this->respostaDescritiva('secretario', $opcoes) &&
            !$this->respostaDescritiva('seguranca', $opcoes) &&
            !$this->respostaDescritiva('auxiliar_laboratorio', $opcoes) &&
            !$this->respostaDescritiva('gestores_escola', $opcoes) &&
            !$this->respostaDescritiva('orientador_comunitario', $opcoes)) {
            $this->registro->setNaoHaFuncionarios(1);
        }
    }

    private function buildMateriaisEnsino($opcoes)
    {
        $this->registro->setAcervoMultimidia($this->exist('acervo_multimidia', $opcoes));
        $this->registro->setBrinquedosEducacaoInfantil($this->exist('brinquedos_educacao_infantil', $opcoes));
        $this->registro->setMateriaisCientificos($this->exist('conjunto_materiais_cientificos', $opcoes));
        $this->registro->setEquipamentoAmplificacaoOuDifusaoAudio(
            $this->exist('equipamento_amplificacao_ou_difusao_audio', $opcoes)
        );
        $this->registro->setInstrumentosMusicais($this->exist('instrumentos_musicais', $opcoes));
        $this->registro->setJogosEducativos($this->exist('jogos_educativos', $opcoes));
        $this->registro->setMaterialAtividadeCultural($this->exist('materiais_atividades_culturais', $opcoes));
        $this->registro->setMaterialEducacaoProfissional($this->exist('materiais_para_educacao_profissional', $opcoes));
        $this->registro->setMaterialDesportivRecreacao($this->exist('materiais_esportivos_ou_recreacao', $opcoes));
        $this->registro->setMaterialEducacaoBilingue($this->exist('materiais_educacao_bilingue', $opcoes));
        $this->registro->setMaterialEducacaoIndigena($this->exist('material_educacao_indigena', $opcoes));
        $this->registro->setMaterialEducacaoEtnicoRacial($this->exist('material_educacao_etnico_racial', $opcoes));
        $this->registro->setMaterialEducacaoCampo($this->exist('material_educacao_campo', $opcoes));
        $this->registro->setEquipamentosAtividadesHortaProducaoAgricola(
            $this->exist('equipamentos_atividades_horta_producao_agricola', $opcoes)
        );
        $this->registro->setMaterialEducacaoQuilombola(
            $this->exist('materiais_pedagogicos_educacao_escolar_quilombola', $opcoes)
        );
        $this->registro->setMaterialEducacaoEspecial(
            $this->exist('materiais_pedagogicos_para_a_educacao_especial', $opcoes)
        );

        if (!$this->exist('acervo_multimidia', $opcoes) &&
            !$this->exist('brinquedos_educacao_infantil', $opcoes) &&
            !$this->exist('conjunto_materiais_cientificos', $opcoes) &&
            !$this->exist('equipamento_amplificacao_ou_difusao_audio', $opcoes) &&
            !$this->exist('instrumentos_musicais', $opcoes) &&
            !$this->exist('jogos_educativos', $opcoes)&&
            !$this->exist('materiais_atividades_culturais', $opcoes) &&
            !$this->exist('materiais_para_educacao_profissional', $opcoes) &&
            !$this->exist('materiais_esportivos_ou_recreacao', $opcoes) &&
            !$this->exist('material_educacao_indigena', $opcoes) &&
            !$this->exist('material_educacao_etnico_racial', $opcoes) &&
            !$this->exist('material_educacao_campo', $opcoes)) {
            $this->registro->setNenhumInstrumentoListado(1);
        }
    }

    private function buildReservaVagas($opcoes)
    {
        $this->registro->setReservaVagaPretoPardoIndigena($this->exist('preto_pardo_indigena', $opcoes));
        $this->registro->setReservaVagaRenda($this->exist('renda', $opcoes));
        $this->registro->setReservaVagaEscolaPublica($this->exist('escola_publica', $opcoes));
        $this->registro->setReservaVagaDeficiencia($this->exist('deficiencia', $opcoes));
        $this->registro->setReservaVagaOutro($this->exist('cotas_outros', $opcoes));
        $this->registro->setSemReservaVagas($this->exist('cotas_nenhum', $opcoes));
    }

    private function buildEscolaComunidade($perguntas)
    {
        if (array_key_exists('pregunta_escola_abre_aos_finais_de_semana_para_a_comunidade', $perguntas)) {
            $this->registro->setEscolaUsaEquipamentosParaAtividade(
                $this->respostaObjetiva($perguntas['pregunta_escola_abre_aos_finais_de_semana_para_a_comunidade'])
            );
        }
    }

    private function buildOrgaosColegiados($opcoes)
    {
        $this->registro->setAssociacaoPais($this->exist('associacao_pais', $opcoes) ? 1 : 0);
        $this->registro->setAssociacaoPaisMestres($this->exist('associacao_pais_e_mestres', $opcoes) ? 1 : 0);
        $this->registro->setConselhoEscolar($this->exist('conselho_escolar', $opcoes) ? 1 : 0);
        $this->registro->setGremioEstudantil($this->exist('gremio_estudantil', $opcoes) ? 1 : 0);
        $this->registro->setOrgaosColegiadosOutros($this->exist('orgaos_colegiados_outros', $opcoes) ? 1 : 0);
        $this->registro->setOrgaosColegiadosNenhum($this->exist('orgaos_colegiados_nenhum', $opcoes) ? 1 : 0);
    }
    
    private function buildFormasEducacaoAmbiental($opcoes)
    {
        $this->registro->setComoConteudoComponentesCamposExperiencias(
            $this->exist('como_conteudo_componentes_presentes_curriculo', $opcoes) ? 1 : 0
        );
        $this->registro->setComoComponenteCurricularEspecialEspecifico(
            $this->exist('como_componente_curricular_especial_especifico', $opcoes) ? 1 : 0
        );
        $this->registro->setComoEixoEstruturanteCurriculo(
            $this->exist('como_um_eixo_estruturante_do_curriculo', $opcoes) ? 1 : 0
        );
        $this->registro->setComoEixoEstruturanteCurriculo(
            $this->exist('como_um_eixo_estruturante_do_curriculo', $opcoes) ? 1 : 0
        );
        $this->registro->setEmEventos($this->exist('em_eventos', $opcoes) ? 1 : 0);
        $this->registro->setEmProjetosTransversaisInterdisciplinares(
            $this->exist('em_projetos_transversais_ou_interdisciplinares', $opcoes) ? 1 : 0
        );
        $this->registro->setNenhumaDasOpcoesListadas($this->exist('nenhuma_das_opcoes_listadas', $opcoes) ? 1 : 0);
    }

    private function buildAlimentacaoAlunos($opcoes)
    {
        $this->registro->setAlimentacaoEscolar($this->exist('alimentacao_escolar_para_os_alunos_oferece', $opcoes));
    }
}
