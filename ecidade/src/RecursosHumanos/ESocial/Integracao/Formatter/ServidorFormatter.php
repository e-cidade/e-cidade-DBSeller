<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use ECidade\RecursosHumanos\ESocial\Entity\Servidor;
use ECidade\RecursosHumanos\ESocial\Service\ServidorService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use stdClass;
use CgmJuridico;
use DBPessoal;

/**
 * Class ServidorFormatter
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class ServidorFormatter extends Formatter
{
    /**
     * @var Servidor
     */
    private $servidorAtual;

    /**
     * @var CgmJuridico
     */
    private $empregador;

    private $alteracao = false;

    /**
     * @param  array $dados
     * @return mixed|stdClass[]
     * @throws \BusinessException
     * @throws \DBException
     */
    public function formatar($dados, $alteracao = false)
    {
        $this->alteracao = $alteracao;
        $dadosServidor = [];
        foreach ($dados as $servidor) {
            $validacao = !$servidor->isRescindido();
            if ($this->getIgnoraValidacao()) {
                $validacao = $this->getIgnoraValidacao();
            }

            if ($servidor->temVinculoEmpregaticio() && $servidor->isAtivo()
                && $validacao) {
                $dadosServidor[] = $this->processamento($servidor);
            }
        }
        return $dadosServidor;
    }

    /**
     * @param $dados
     */
    private function preProcessamento(&$dados)
    {
        foreach ($dados as $dado) {
            $this->preProcessamentoHorario($dado);
        }
    }

    /**
     * @param $dado
     */
    private function preProcessamentoHorario(&$dado)
    {
        if (isset($dado->respostas['horario']->perguntas)) {
            for ($i = 1; $i <= 32; $i++) {
                $horario = $dado->respostas['horario']->perguntas["horario_codHorContrat_{$i}"];

                if (isset($horario)) {
                    if (!empty($horario->resposta->resposta)) {
                        $dia = new stdClass();
                        $dia->nome = "dia_{$i}";
                        $dia->resposta = new stdClass();
                        $dia->resposta->resposta = ($i - 1) % 8 + 1;

                        $codigo = new stdClass();
                        $codigo->nome = "horario_codHorContrat_{$i}";
                        $codigo->resposta = new stdClass();
                        $codigo->resposta->resposta = $horario->resposta->resposta;

                        $std = new stdClass();
                        $std->nome = "horario_codHorContrat_{$i}";
                        $std->perguntas = array();
                        $std->nome = "horario_codHorContrat_{$i}";
                        $std->perguntas = array(
                            "horario_codHorContrat_{$i}" => $codigo,
                            "dia_{$i}"                   => $dia
                        );

                        $dado->respostas["horario_codHorContrat_{$i}"] = $std;
                    }
                }
            }
        }
    }

    /**
     * @param  $dadosFormatado
     * @return mixed
     * @throws \BusinessException
     * @throws \DBException
     */
    private function processamento($servidor)
    {
        $this->servidorAtual = $servidor;
        $dadoServidor = new stdClass();
        $dadoServidor->referencia = $this->servidorAtual->getMatricula();
        $dadoServidor->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $this->atualizarDadosServidor($dadoServidor);
        return $dadoServidor;
    }

    /**
     * @param $dadoServidor
     */
    private function processamentoTrabalhadorNascimento(&$dadoServidor)
    {

        //TODO - Verificar quando trabalhador for diferente de brasilelira

        if ($this->servidorAtual->getCgm()->getNacionalidade() == '1' ||
            empty($this->servidorAtual->getCgm()->getNacionalidade())
        ) {
            $dadoServidor->trabalhador['nascimento']['paisNascto'] = '105';
            $dadoServidor->trabalhador['nascimento']['paisNac'] = '105';
            unset($dadoServidor->trabalhador->trabImig);
        }
    }

    /**
     * @param $dadoServidor
     */
    private function posProcessamentoTrabalhadorDocumento(&$dadoServidor)
    {
        //Verificamos se há documentos informados
        if (isset($dadoServidor->trabalhador->documentos)) {
            $this->validaCTPS($dadoServidor);
            $this->validaRIC($dadoServidor);
            $this->validaRG($dadoServidor);
            $this->validaRNE($dadoServidor);
            $this->validaOC($dadoServidor);
            $this->validaCNH($dadoServidor);
        }
    }

    /**
     * @param $dadoServidor
     */
    private function validaCTPS(&$dadoServidor)
    {
        //Verificamos se há CTPS informado
        if (isset($dadoServidor->trabalhador->documentos->CTPS)) {
            //Quando informado UF, tem que ser em letrass maiúscula
            if (!empty($dadoServidor->trabalhador->documentos->CTPS->ufCtps)) {
                $uf = strtoupper($dadoServidor->trabalhador->documentos->CTPS->ufCtps);
                $dadoServidor->trabalhador->documentos->CTPS->ufCtps = $uf;
            }

            if (empty($dadoServidor->trabalhador->documentos->CTPS->nrCtps)
                && empty($dadoServidor->trabalhador->documentos->CTPS->serieCtps)
                && empty($dadoServidor->trabalhador->documentos->CTPS->ufCtps)
            ) {
                unset($dadoServidor->trabalhador->documentos->CTPS);
            }
        }
    }

    /**
     * @param $dadoServidor
     */
    private function validaRIC(&$dadoServidor)
    {
        //Verificamos se há RIC informado
        if (isset($dadoServidor->trabalhador->documentos->RIC)) {
            //Removido a data de expedição (dtExped) do Registro de Identificação Civil (RIC), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->RIC->dtExped)) {
                unset($dadoServidor->trabalhador->documentos->RIC->dtExped);
            }
            //Removido Registro de Identidade Civil (RIC), quando nenhum dos campos obrigatórios estiverem preenchidos.
            if (empty($dadoServidor->trabalhador->documentos->RIC->nrRic)
                && empty($dadoServidor->trabalhador->documentos->RIC->orgaoEmissor)
            ) {
                unset($dadoServidor->trabalhador->documentos->RIC);
            }
        }
    }

    /**
     * @param $dadoServidor
     */
    private function validaRG(&$dadoServidor)
    {
        // Verificamos se há RG informado
        if (isset($dadoServidor->trabalhador->documentos->RG)) {
            //Removido a data de expedição (dtExped) do Informações do Registro Geral (RG), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->RG->dtExped)) {
                unset($dadoServidor->trabalhador->documentos->RG->dtExped);
            }
            //Removido Registro Geral (RG), quando nenhum dos campos obrigatórios estiverem preenchidos.
            if (empty($dadoServidor->trabalhador->documentos->RG->nrRg)
                && empty($dadoServidor->trabalhador->documentos->RG->orgaoEmissor)
            ) {
                unset($dadoServidor->trabalhador->documentos->RG);
            }
        }
    }

    /**
     * @param $dadoServidor
     */
    private function validaRNE(&$dadoServidor)
    {
        // Verificamos se há RNE informado
        if (isset($dadoServidor->trabalhador->documentos->RNE)) {
            //Removido a data de expedição (dtExped) do Registro Nacional de Estrangeiro (RNE), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->RNE->dtExped)) {
                unset($dadoServidor->trabalhador->documentos->RNE->dtExped);
            }
            /**
             * Removido Registro Nacional de Estrangeiro (RNE), quando nenhum dos campos obrigatórios estiverem
             * preenchidos.
             */
            if (empty($dadoServidor->trabalhador->documentos->RNE->nrRne)
                && empty($dadoServidor->trabalhador->documentos->RNE->orgaoEmissor)
            ) {
                unset($dadoServidor->trabalhador->documentos->RNE);
            }
        }
    }

    /**
     * @param $dadoServidor
     */
    private function validaOC(&$dadoServidor)
    {
        //Verificamos se há OC informado
        if (isset($dadoServidor->trabalhador->documentos->OC)) {
            //Removido a data de expedição (dtExped) do Órgão de Classe (OC), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->OC->dtExped)) {
                unset($dadoServidor->trabalhador->documentos->OC->dtExped);
            }
            //Removido a data de expedição (dtExped) do Órgão de Classe (OC), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->OC->dtValid)) {
                unset($dadoServidor->trabalhador->documentos->OC->dtValid);
            }
            //Removido Órgão de Classe (OC), quando nenhum dos campos obrigatórios estiverem preenchidos.
            if (empty($dadoServidor->trabalhador->documentos->OC->nrOc)
                && empty($dadoServidor->trabalhador->documentos->OC->orgaoEmissor)
            ) {
                unset($dadoServidor->trabalhador->documentos->OC);
            }
        }
    }

    /**
     * @param $dadoServidor
     */
    private function validaCNH(&$dadoServidor)
    {
        //Verificamos se há CNH informado
        if (isset($dadoServidor->trabalhador->documentos->CNH)) {
            //Removido a data de expedição (dtExped) do Carteira Nacional de Habilitação (CNH), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->CNH->dtExped)) {
                unset($dadoServidor->trabalhador->documentos->CNH->dtExped);
            }

            //Removido a data de expedição (dtExped) do Carteira Nacional de Habilitação (CNH), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->CNH->dtPriHab)) {
                unset($dadoServidor->trabalhador->documentos->CNH->dtPriHab);
            }
            //Removido a data de expedição (dtExped) do Carteira Nacional de Habilitação (CNH), quando estiver vazia.
            if (empty($dadoServidor->trabalhador->documentos->CNH->dtValid)) {
                unset($dadoServidor->trabalhador->documentos->CNH->dtValid);
            }

            //Quando informado UF, tem que ser em letras maiúscula
            if (!empty($dadoServidor->trabalhador->documentos->CNH->ufCnh)) {
                $uf = strtoupper($dadoServidor->trabalhador->documentos->CNH->ufCnh);
                $dadoServidor->trabalhador->documentos->CNH->ufCnh = $uf;
            }
            /**
             * Removido Carteira Nacional de Habilitação (CNH), quando nenhum dos campos obrigatórios estiverem
             * preenchidos.
             */
            if (empty($dadoServidor->trabalhador->documentos->CNH->nrRegCnh)
                && empty($dadoServidor->trabalhador->documentos->CNH->ufCnh)
                && empty($dadoServidor->trabalhador->documentos->CNH->dtValid)
                && empty($dadoServidor->trabalhador->documentos->CNH->categoriaCnh)
            ) {
                unset($dadoServidor->trabalhador->documentos->CNH);
            }
        }
    }


    /**
     * @param $dadoServidor
     */
    private function regraVinculoInfoContrato(&$dadoServidor)
    {
        if (isset($dadoServidor->vinculo['infoContrato'])) {
            if (!empty($dadoServidor->vinculo['cadIni']) &&
            $dadoServidor->vinculo['cadIni'] == 'S') {
                unset($dadoServidor->vinculo['infoContrato']['acumCargo']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['nmCargo'])) {
                unset($dadoServidor->vinculo['infoContrato']['nmCargo']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['CBOCargo'])) {
                unset($dadoServidor->vinculo['infoContrato']['CBOCargo']);
                unset($dadoServidor->vinculo['infoContrato']['dtIngrCargo']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['dtIngrCargo'])) {
                unset($dadoServidor->vinculo['infoContrato']['dtIngrCargo']);
            }

            if ($dadoServidor->vinculo['cadIni'] == 'N') {
                unset($dadoServidor->vinculo['infoContrato']['dtIngrCargo']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['nmFuncao'])) {
                unset($dadoServidor->vinculo['infoContrato']['nmFuncao']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['CBOFuncao'])) {
                unset($dadoServidor->vinculo['infoContrato']['CBOFuncao']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['acumCargo'])) {
                unset($dadoServidor->vinculo['infoContrato']['acumCargo']);
            }

            if (!isset($dadoServidor->vinculo['infoContrato']['nmCargo'])) {
                unset($dadoServidor->vinculo['infoContrato']['dtIngrCargo']);
                unset($dadoServidor->vinculo['infoContrato']['CBOCargo']);
                unset($dadoServidor->vinculo['infoContrato']['dtIngrCargo']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['remuneracao'])) {
                unset($dadoServidor->vinculo['infoContrato']['remuneracao']);
            }
            if (empty($dadoServidor->vinculo['infoContrato']['remuneracao']['dscSalVar'])) {
                unset($dadoServidor->vinculo['infoContrato']['remuneracao']['dscSalVar']);
            }
            if (isset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTrabGeral'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTrabGeral']['descComp'])
            ) {
                unset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTrabGeral']['descComp']);
            }
            if (empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTrabGeral'])) {
                unset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTrabGeral']);
            }
            if (empty($dadoServidor->vinculo['infoContrato']['localTrabalho'])) {
                unset($dadoServidor->vinculo['infoContrato']['localTrabalho']);
            }
            if (isset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['tpLograd'])
            ) {
                unset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['tpLograd']);
            }
            if (isset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['complemento'])
            ) {
                unset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['complemento']);
            }
            if (isset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['bairro'])
            ) {
                unset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['bairro']);
            }
            if (isset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['dscLograd'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['nrLograd'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['cep'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['codMunic'])
                && empty($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']['uf'])) {
                unset($dadoServidor->vinculo['infoContrato']['localTrabalho']['localTempDom']);
            }
            $horaContratual = false;
            if (isset($dadoServidor->vinculo['infoContrato']['horContratual']['tpJornada']) &&
                !empty($dadoServidor->vinculo['infoContrato']['horContratual']['tpJornada'])) {
                $horaContratual = true;
            }

            if (isset($dadoServidor->vinculo['infoContrato']['horContratual']['tmpParc']) &&
                !empty($dadoServidor->vinculo['infoContrato']['horContratual']['tmpParc'])) {
                $horaContratual = true;
            }

            if (isset($dadoServidor->vinculo['infoContrato']['horContratual']['dscJorn']) &&
            !empty($dadoServidor->vinculo['infoContrato']['horContratual']['dscJorn'])) {
                $horaContratual = true;
            }

            if (isset($dadoServidor->vinculo['infoContrato']['horContratual'])
                && empty($dadoServidor->vinculo['infoContrato']['horContratual']['qtdHrsSem'])
                && $horaContratual
            ) {
                unset($dadoServidor->vinculo['infoContrato']['horContratual']['qtdHrsSem']);
            }
            if (isset($dadoServidor->vinculo['infoContrato']['horContratual'])
                && empty($dadoServidor->vinculo['infoContrato']['horContratual']['horNoturno'])
                ) {
                unset($dadoServidor->vinculo['infoContrato']['horContratual']['horNoturno']);
            }
            if (!$horaContratual) {
                unset($dadoServidor->vinculo['infoContrato']['horContratual']);
            }
            if (empty($dadoServidor->vinculo['infoContrato']['observacoes'])
                && empty($dadoServidor->vinculo['infoContrato']['observacoes']['observacao'])
            ) {
                unset($dadoServidor->vinculo['infoContrato']['observacoes']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['duracao'])) {
                unset($dadoServidor->vinculo['infoContrato']['duracao']);
            }

            if (empty($dadoServidor->vinculo['infoContrato']['duracao']['dtTerm'])) {
                unset($dadoServidor->vinculo['infoContrato']['duracao']['dtTerm']);
            }
            if (empty($dadoServidor->vinculo['infoContrato']['duracao']['objDet'])) {
                unset($dadoServidor->vinculo['infoContrato']['duracao']['objDet']);
            }
            if (empty($dadoServidor->vinculo['infoContrato']['duracao']['clauAssec'])) {
                unset($dadoServidor->vinculo['infoContrato']['duracao']['clauAssec']);
            }
        }
    }

    /**
     * @param  $dadoServidor
     * @throws \BusinessException
     * @throws \DBException
     * @throws \Exception
     */
    private function atualizarDadosServidor(&$dadoServidor)
    {
        $servidorMovimentacaoRepository = new ServidorMovimentacaoRepository();
        $servidorMovimentacaoModel = $servidorMovimentacaoRepository
            ->scopeAno($this->servidorAtual->getAnoCompetencia())
            ->scopeMes($this->servidorAtual->getMesCompetencia())
            ->scopeMatricula($this->servidorAtual->getMatricula())
            ->first();
        $servidorService = new ServidorService(
            $this->servidorAtual,
            $servidorMovimentacaoModel,
            $dadoServidor,
            $this->alteracao
        );
        $servidorEntity = $servidorService->buscarDadosServidor();
        $this->atualizarGrupoTrabalhador($dadoServidor, $servidorEntity);
        $this->atualizarGrupoVinculo($dadoServidor, $servidorEntity);
    }

    /**
     * @param $dadoServidor
     * @param Servidor $servidorEntity
     */
    private function atualizarGrupoTrabalhador(&$dadoServidor, Servidor $servidorEntity)
    {

        //Dados - Informações pessoais do trabalhador.
        $dadoServidor->trabalhador = array_merge($dadoServidor->trabalhador, $servidorEntity->getDadosTrabalhador());

        //Regras -  Informações pessoais do trabalhador.
        $this->regraPessoalTrabalhador($dadoServidor);

        // Dados - Grupo de informações do endereço do trabalhador.
        $dadoServidor->trabalhador['endereco'] = $servidorEntity->getEndereco();

        //Regras - Grupo de informações do endereço do trabalhador.
        $this->regraEnderecoTrabalhador($dadoServidor);

        //Dados - Informações do trabalhador imigrante.
        $dadoServidor->trabalhador['trabImig'] = $servidorEntity->getImigrante();

        //Regras - Informações do trabalhador imigrante.
        $this->regraTrabalhadorImigrante($dadoServidor);

        //Dados -  Pessoa com deficiência.
        $dadoServidor->trabalhador['infoDeficiencia'] = $servidorEntity->getDeficiente();

        //Regras - Pessoa com deficiência.
        $this->regraPessoaDeficiencia($dadoServidor);

        //Dados - Informações dos dependentes.
        $dadoServidor->trabalhador['dependente'] = $servidorEntity->getDependentes();

        //Regras - Informações dos dependentes.
        $this->regraDependente($dadoServidor);

        //Dados - Informações de contato.
        $dadoServidor->trabalhador['contato'] = $servidorEntity->getContato();

        //Regras - Informações de contato.
        $this->regraContato($dadoServidor);
    }

    /**
     * @param $dadoServidor
     * @param Servidor $servidorEntity
     */
    private function atualizarGrupoVinculo(&$dadoServidor, Servidor $servidorEntity)
    {
        // Dados - Informações do contrato de trabalho.
        $dadoServidor->vinculo = array_merge($dadoServidor->vinculo, $servidorEntity->getVinculoTrabalho());
        $dadoServidor->vinculo['infoContrato'] = array_merge(
            $dadoServidor->vinculo['infoContrato'],
            $servidorEntity->getContratoTrabalho()
        );
        // Regras - Informações do contrato de trabalho.
        $this->regraVinculoInfoContrato($dadoServidor);

        //Informações do regime trabalhista.
        $dadoServidor->vinculo['infoRegimeTrab'] = [];
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $dadoServidor->vinculo['matricula'] = $this->servidorAtual->getMatriculaAnterior();
        }
        // Dados - Informações de trabalhador celetista.
        if ($this->servidorAtual->isCeletista()) {
            if ($servidorEntity->getCeletista() !== null) {
                $dadoServidor->vinculo['infoRegimeTrab'] = array_merge(
                    $dadoServidor->vinculo['infoRegimeTrab'],
                    $servidorEntity->getCeletista()
                );
                // Regras - Informações de trabalhador celetista.
                $this->regraTrabalahdorCeletista($dadoServidor);
            }
        }


        // Dados - Informações de trabalhador estatutário.
        if ($this->servidorAtual->isEstatutario() || $this->servidorAtual->isExtraQuadro()) {
            $dadoServidor->vinculo['infoRegimeTrab']['infoEstatutario'] = $servidorEntity->getEstatutario();
            // Regras - Informações de trabalhador estatutário.
            $this->regraTrabalhadorEstatutario($dadoServidor);
        }


        //Dados - Grupo de informações da sucessão de vínculo trabalhista/estatutário.
        $dadoServidor->vinculo['sucessaoVinc'] = $servidorEntity->getSucessao();

        //Regras - Grupo de informações da sucessão de vínculo trabalhista/estatutário.
        $this->regraSucessao($dadoServidor);



        //Dados - Informações do empregado doméstico transferido de outro representante da mesma unidade familiar.
        //NÃO SERÃO PREENCHIDOS
        // $dadoServidor->vinculo['transfDom'] = [];
        // $dadoServidor->vinculo['transfDom']['cpfSubstituido'] = '';
        // $dadoServidor->vinculo['transfDom']['matricAnt'] = '';
        // $dadoServidor->vinculo['transfDom']['dtTransf'] = '';

        //TODO
        //Dados - Informações de mudança de CPF do trabalhador.
        $dadoServidor->vinculo['mudancaCPF'] = $servidorEntity->getMudancaCPF();

        //Regras - Informações de mudança de CPF do trabalhador.
        $this->regraMudancaCpf($dadoServidor);


        //Dados - Informações de afastamento do trabalhador.
        $dadoServidor->vinculo['afastamento'] = $servidorEntity->getAfastamento();

        //Regras - Informações de afastamento do trabalhador.
        $this->regraAfastamento($dadoServidor);

        //Dados - Informação do desligamento do trabalhador.
        $dadoServidor->vinculo['desligamento'] = $servidorEntity->getDesligamento();

        //Regras - Informação do desligamento do trabalhador.
        $this->regraDesligamento($dadoServidor);

        //Dados - Informação de cessão/exercício em outro órgão do trabalhador.
        $dadoServidor->vinculo['cessao'] = $servidorEntity->getCessao();

        //Regra - Informação de cessão/exercício em outro órgão do trabalhador.
        $this->regraCessao($dadoServidor);
    }

    /**
     * @param $dadosServidor
     */
    private function ajustePrimeiroEmprego(&$dadosServidor)
    {
        if (isset($dadosServidor->vinculo["cadIni"])) {
            if ($dadosServidor->vinculo["cadIni"] == "S") {
                if (!empty($dadosServidor->trabalhador["indPriEmpr"])) {
                    unset($dadosServidor->trabalhador["indPriEmpr"]);
                }
            }
        }
    }

    /**
     * @param $dadoServidor
     */
    private function regraTrabalahdorCeletista(&$dadoServidor)
    {
        $dadoInfoCeletista = $dadoServidor->vinculo['infoRegimeTrab']['infoCeletista'];
        if (empty($dadoInfoCeletista['dtBase'])) {
            unset($dadoInfoCeletista['dtBase']);
        }
        if (empty($dadoInfoCeletista['nrProcTrab'])) {
            unset($dadoInfoCeletista['nrProcTrab']);
        }

        $grupoCeletista = empty($dadoInfoCeletista['dtAdm'])
            && empty($dadoInfoCeletista['tpAdmissao'])
            && empty($dadoInfoCeletista['indAdmissao'])
            && empty($dadoInfoCeletista['tpRegJor'])
            && empty($dadoInfoCeletista['natAtividade'])
            && empty($dadoInfoCeletista['cnpjSindCategProf']);
        if ($grupoCeletista) {
            unset($dadoInfoCeletista);
        }

        if (isset($dadoInfoCeletista)) {
            //Informações do Fundo de Garantia do Tempo de Serviço - FGTS.
            if (empty($dadoInfoCeletista['FGTS']['dtOpcFGTS'])) {
                unset($dadoInfoCeletista['FGTS']);
            }

            //Dados sobre trabalho temporário
            if (empty($dadoInfoCeletista['trabTemporario']['hipLeg'])
                && empty($dadoInfoCeletista['trabTemporario']['justContr'])
            ) {
                unset($dadoInfoCeletista['trabTemporario']);
            }
        }

        if (!(isset($dadoInfoCeletista))) {
            unset($dadoServidor->vinculo['infoRegimeTrab']['infoCeletista']);
        } else {
            $dadoServidor->vinculo['infoRegimeTrab']['infoCeletista'] = $dadoInfoCeletista;
        }
        //REGRA DE PREENCHIMENTO DO dtIngrCargo
        if ($dadoServidor->vinculo['tpRegTrab'] == 1) {
            unset($dadoServidor->vinculo['infoContrato']['dtIngrCargo']);
        }
      
        if (!empty($dadoServidor->vinculo['infoContrato']['dtIngrCargo']) &&
            $dadoServidor->vinculo['infoContrato']['dtIngrCargo'] >
            DBPessoal::getDataFaseEsocial(4)->getDate() &&
            $dadoServidor->vinculo['cadIni'] !== 'S') {
            unset($dadoServidor->vinculo['infoContrato']['dtIngrCargo']);
        }

        if ($dadoServidor->vinculo['tpRegTrab'] == 2) {
            unset($dadoServidor->vinculo['infoRegimeTrab']['infoCeletista']);
        }
    }

    /**
     * @param $dadoServidor
     */
    private function regraTrabalhadorEstatutario(&$dadoServidor)
    {
        $dadoEstatutario = $dadoServidor->vinculo['infoRegimeTrab']['infoEstatutario'];

        if (isset($dadoEstatutario['tpPlanRP'])
                && $dadoEstatutario['tpPlanRP'] !== 0
                && $dadoEstatutario['tpPlanRP'] !== 1
                && $dadoEstatutario['tpPlanRP'] !== 2
                && $dadoEstatutario['tpPlanRP'] !== 3) {
            unset($dadoEstatutario['tpPlanRP']);
        }
        if (empty($dadoEstatutario['indTetoRGPS'])) {
            unset($dadoEstatutario['indTetoRGPS']);
        }
        if (empty($dadoEstatutario['indAbonoPerm'])) {
            unset($dadoEstatutario['indAbonoPerm']);
        }
        if (empty($dadoEstatutario['dtIniAbono'])) {
            unset($dadoEstatutario['dtIniAbono']);
        }

        $grupoEstatutario = empty($dadoEstatutario['tpProv'])
            && empty($dadoEstatutario['dtExercicio']);
        if ($grupoEstatutario) {
            unset($grupoEstatutario);
        }

        if (isset($grupoEstatutario)) {
            $dadoServidor->vinculo['infoRegimeTrab']['infoEstatutario'] = $dadoEstatutario;
        } else {
            unset($dadoServidor->vinculo['infoRegimeTrab']['infoEstatutario']);
        }

        // Caso trabalhador deficiente, não é enviada informacao sobre cotas
        if (isset($dadoServidor->trabalhador['infoDeficiencia'])
            && isset($dadoServidor->trabalhador['infoDeficiencia']['infoCota'])) {
            unset($dadoServidor->trabalhador['infoDeficiencia']['infoCota']);
        }
    }

    /**
     * @param $dadoServidor
     */
    private function regraPessoalTrabalhador(&$dadoServidor)
    {
        if (empty($dadoServidor->trabalhador['estCiv'])) {
            unset($dadoServidor->trabalhador['estCiv']);
        }

        if (empty($dadoServidor->trabalhador['nmSoc'])) {
            unset($dadoServidor->trabalhador['nmSoc']);
        }
    }

    /**
     * @param $dadoServidor
     */
    private function regraEnderecoTrabalhador(&$dadoServidor)
    {
        if (!empty($dadoServidor->trabalhador['endereco']['exterior']['paisResid'])) {
            unset($dadoServidor->trabalhador['endereco']['brasil']);
        } else {
            unset($dadoServidor->trabalhador['endereco']['exterior']);
        }
        if (isset($dadoServidor->trabalhador['endereco']['brasil'])) {
            if (empty($dadoServidor->trabalhador['endereco']['brasil']['tpLograd'])) {
                unset($dadoServidor->trabalhador['endereco']['brasil']['tpLograd']);
            }
            if (empty($dadoServidor->trabalhador['endereco']['brasil']['complemento'])) {
                unset($dadoServidor->trabalhador['endereco']['brasil']['complemento']);
            }
            if (empty($dadoServidor->trabalhador['endereco']['brasil']['bairro'])) {
                unset($dadoServidor->trabalhador['endereco']['brasil']['bairro']);
            }
        }
        if (isset($dadoServidor->trabalhador['endereco']['exterior'])) {
            if (empty($dadoServidor->trabalhador['endereco']['exterior']['complemento'])) {
                unset($dadoServidor->trabalhador['endereco']['exterior']['complemento']);
            }
            if (empty($dadoServidor->trabalhador['endereco']['exterior']['bairro'])) {
                unset($dadoServidor->trabalhador['endereco']['exterior']['bairro']);
            }
            if (empty($dadoServidor->trabalhador['endereco']['exterior']['codPostal'])) {
                unset($dadoServidor->trabalhador['endereco']['exterior']['codPostal']);
            }
        }
    }

    /**
     * @param $dadoServidor
     */
    private function regraTrabalhadorImigrante(&$dadoServidor)
    {
        if ($this->servidorAtual->getDataAdmissao()->getDate() < '2021-07-19') {
            if (empty($dadoServidor->trabalhador['trabImig']['tmpResid'])) {
                unset($dadoServidor->trabalhador['trabImig']['tmpResid']);
            }
        }

        if (empty($dadoServidor->trabalhador['trabImig']['tmpResid'])
        && empty($dadoServidor->trabalhador['trabImig']['condIng'])) {
            unset($dadoServidor->trabalhador['trabImig']);
        }
    }

    /**
     * @param $dadoServidor
     */
    private function regraPessoaDeficiencia(&$dadoServidor)
    {

        if (empty($dadoServidor->trabalhador['infoDeficiencia']['infoCota'])) {
            unset($dadoServidor->trabalhador['infoDeficiencia']['infoCota']);
        }
        if (empty($dadoServidor->trabalhador['infoDeficiencia']['observacao'])) {
            unset($dadoServidor->trabalhador['infoDeficiencia']['observacao']);
        }
        $dadoInfoDeficiencia = $dadoServidor->trabalhador['infoDeficiencia'];
        $naoExisteGrupoDeficiencia = ((empty($dadoInfoDeficiencia['defFisica'])
            && empty($dadoInfoDeficiencia['defVisual'])
            && empty($dadoInfoDeficiencia['defAuditiva'])
            && empty($dadoInfoDeficiencia['defMental'])
            && empty($dadoInfoDeficiencia['defIntelectual'])
            && empty($dadoInfoDeficiencia['reabReadap'])) ||
            ($dadoInfoDeficiencia['defFisica'] == 'N'
            && $dadoInfoDeficiencia['defVisual'] == 'N'
            && $dadoInfoDeficiencia['defAuditiva'] == 'N'
            && $dadoInfoDeficiencia['defMental'] == 'N'
            && $dadoInfoDeficiencia['defIntelectual'] == 'N'
            && $dadoInfoDeficiencia['reabReadap'] == 'N'
            ));
        if ($naoExisteGrupoDeficiencia) {
            unset($dadoServidor->trabalhador['infoDeficiencia']);
        } else {
            $dadoServidor->trabalhador['infoDeficiencia'] = $dadoInfoDeficiencia;
        }
    }

    /**
     * @param $dadoServidor
     */

    private function regraDependente(&$dadoServidor)
    {
        if (isset($dadoServidor->trabalhador['dependente'])) {
            foreach ($dadoServidor->trabalhador['dependente'] as $chave => $dependente) {
                if (empty($dadoServidor->trabalhador['dependente'][$chave]['sexoDep'])) {
                    unset($dadoServidor->trabalhador['dependente'][$chave]['sexoDep']);
                }

                if (empty($dadoServidor->trabalhador['dependente'][$chave]['cpfDep'])) {
                    unset($dadoServidor->trabalhador['dependente'][$chave]['cpfDep']);
                }
            }
        }
    }

    /**
     * @param $dadoServidor
     */

    private function regraContato(&$dadoServidor)
    {
        $dadoContato =  $dadoServidor->trabalhador['contato'];
        $dadoContato['fonePrinc'] = preg_replace('/[\D]/', '', $dadoContato['fonePrinc']);
        if (strlen(trim($dadoContato['fonePrinc']))<10 || strlen(trim($dadoContato['fonePrinc']))>12) {
            unset($dadoContato['fonePrinc']);
        }
        if (isset($dadoContato) && empty($dadoContato['fonePrinc'])) {
            unset($dadoContato['fonePrinc']);
        } else {
            $dadoContato['fonePrinc'] = trim($dadoContato['fonePrinc']);
        }

        if (isset($dadoContato) && empty($dadoContato['emailPrinc'])) {
            unset($dadoContato['emailPrinc']);
        }
        if (!isset($dadoContato['fonePrinc']) && !isset($dadoContato['emailPrinc'])) {
            unset($dadoServidor->trabalhador['contato']);
        } else {
            $dadoServidor->trabalhador['contato'] = $dadoContato;
        }
    }

    /**
     * @param $dadoServidor
     */

    private function regraSucessao(&$dadoServidor)
    {
        $dadoSucessao =  $dadoServidor->vinculo['sucessaoVinc'];

        if (empty($dadoSucessao['matricAnt'])) {
            unset($dadoSucessao['matricAnt']);
        }

        if (empty($dadoSucessao['observacao'])) {
            unset($dadoSucessao['observacao']);
        }

        $grupoSucessao = empty($dadoSucessao['tpInsc'])
            && empty($dadoSucessao['nrInsc'])
            && empty($dadoSucessao['dtTransf']);
        if ($grupoSucessao) {
            unset($grupoSucessao);
        }

        if (!(isset($grupoSucessao))) {
            unset($dadoServidor->vinculo['sucessaoVinc']);
        } else {
            $dadoServidor->vinculo['sucessaoVinc'] = $dadoSucessao;
        }
    }

    /**
     * @param $dadoServidor
     */

    private function regraMudancaCpf(&$dadoServidor)
    {
        $dadoMudanca =  $dadoServidor->vinculo['mudancaCPF'];

        if (empty($dadoMudanca['observacao'])) {
            unset($dadoMudanca['observacao']);
        }

        $grupoMudanca = empty($dadoMudanca['cpfAnt'])
            && empty($dadoMudanca['matricAnt'])
            && empty($dadoMudanca['dtAltCPF']);
        if ($grupoMudanca) {
            unset($grupoMudanca);
        }

        if (!(isset($grupoMudanca))) {
            unset($dadoServidor->vinculo['mudancaCPF']);
        } else {
            $dadoServidor->vinculo['mudancaCPF'] = $dadoMudanca;
        }
    }

    /**
     * @param $dadoServidor
     */

    private function regraAfastamento(&$dadoServidor)
    {
        $dadoAfastamento =  $dadoServidor->vinculo['afastamento'];

        $grupoAfastamento = empty($dadoAfastamento['dtIniAfast'])
            && empty($dadoAfastamento['codMotAfast']);
        if ($grupoAfastamento) {
            unset($grupoAfastamento);
        }

        if (!(isset($grupoAfastamento))) {
            unset($dadoServidor->vinculo['afastamento']);
        } else {
            $dadoServidor->vinculo['afastamento'] = $dadoAfastamento;
        }
    }

    /**
     * @param $dadoServidor
     */

    private function regraDesligamento(&$dadoServidor)
    {
        $dadoDesligamento =  $dadoServidor->vinculo['desligamento'];

        $grupoDesligamento = empty($dadoDesligamento['dtDeslig']);
        if ($grupoDesligamento) {
            unset($grupoDesligamento);
        }

        if (!(isset($grupoDesligamento))) {
            unset($dadoServidor->vinculo['desligamento']);
        } else {
            $dadoServidor->vinculo['desligamento'] = $dadoDesligamento;
        }
    }

    /**
     * @param $dadoServidor
     */

    private function regraCessao(&$dadoServidor)
    {
        $dadoCessao =  $dadoServidor->vinculo['cessao'];

        $grupoCessao = empty($dadoCessao['dtIniCessao']);
        if ($grupoCessao) {
            unset($grupoCessao);
        }

        if (!(isset($grupoCessao))) {
            unset($dadoServidor->vinculo['cessao']);
        } else {
            $dadoServidor->vinculo['cessao'] = $dadoCessao;
        }
    }

    /**
     * Get the value of empregador
     *
     * @return  CgmJuridico
     */
    public function getEmpregador()
    {
        return $this->empregador;
    }

    /**
     * Set the value of empregador
     *
     * @param  CgmJuridico  $empregador
     *
     * @return  self
     */
    public function setEmpregador(CgmJuridico $empregador)
    {
        $this->empregador = $empregador;
    }

    protected function getServidorAtual()
    {
        return $this->servidorAtual;
    }
}
