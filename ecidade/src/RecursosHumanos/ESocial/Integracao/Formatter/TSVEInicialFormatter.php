<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use AssentamentoRepository;
use CgmJuridico;
use DBDate;
use DBPessoal;
use ECidade\RecursosHumanos\ESocial\Entity\Servidor;
use ECidade\RecursosHumanos\ESocial\Service\ServidorService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use LotacaoRepository;
use ServidorRepository;
use stdClass;

/**
 * Formata os dados do Cargo
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 * @author Augusto Oliveira <augusto.oliveira@dbseller.com.br>
 */
class TSVEInicialFormatter extends Formatter
{

    /**
     * @var Servidor
     */
    private $servidorAtual;

    /**
     * @var ServidorMovimentacao
     */
    private $movimentacaoAtual;

    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($dados)
    {
        $dadosServidor = [];
        $ignoraException = true;
        foreach ($dados as $servidor) {
            $validacao = !$servidor->isRescindido();
            if ($this->getIgnoraValidacao()) {
                $validacao = $this->getIgnoraValidacao();
            }
            if (!$servidor->temVinculoEmpregaticio() && $servidor->isAtivo($ignoraException) && $validacao) {
                $dadosServidor[] = $this->processamento($servidor);
            }
        }
        return $dadosServidor;
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
        $servidorService = new ServidorService($this->servidorAtual, $servidorMovimentacaoModel, $dadoServidor);
        $servidorEntity = $servidorService->buscarDadosServidor();
        $this->movimentacaoAtual = $servidorMovimentacaoModel;
        $this->atualizarGrupoTrabalhador($dadoServidor, $servidorEntity);
        $this->atualizarGrupoSemVinculo($dadoServidor, $servidorEntity);
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

        // Regras - Remove Vinculo Trabalhador
        $this->regraVinculo($dadoServidor);
    }

    /**
    * @param $dadoServidor
    */
    private function regraVinculo(&$dadoServidor)
    {
        if (empty($dadoServidor->vinculo['infoContrato'])) {
            unset($dadoServidor->vinculo['infoContrato']);
        }
        if (empty($dadoServidor->vinculo)) {
            unset($dadoServidor->vinculo);
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
            && empty($dadoInfoDeficiencia['reabReadap'])
            && empty($dadoInfoDeficiencia['infoCota'])) ||
            ($dadoInfoDeficiencia['defFisica'] == 'N'
            && $dadoInfoDeficiencia['defVisual'] == 'N'
            && $dadoInfoDeficiencia['defAuditiva'] == 'N'
            && $dadoInfoDeficiencia['defMental'] == 'N'
            && $dadoInfoDeficiencia['defIntelectual'] == 'N'
            && $dadoInfoDeficiencia['reabReadap'] == 'N'
            && $dadoInfoDeficiencia['infoCota'] == 'N'));

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
        if (empty($dadoServidor->trabalhador['dependente'])) {
            unset($dadoServidor->trabalhador['dependente']);
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
     * @param Servidor $servidorEntity
     */
    private function atualizarGrupoSemVinculo(&$dadoServidor, Servidor $servidorEntity)
    {
        //Trabalhador Sem Vínculo de Emprego/Estatutário - TSVE - Início.
        $dadoServidor->infoTSVInicio = [];

        $dadosTVSEInicio = $servidorEntity->getVinculoTrabalho();
        $dadoServidor->infoTSVInicio['cadIni'] = $dadosTVSEInicio['cadIni'];
        if ($this->validaEnvioMatricula($this->servidorAtual->getMatricula(), $this->getEmpregador())) {
            $dadoServidor->infoTSVInicio['matricula'] = $dadosTVSEInicio['matricula'];
        }
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $dadoServidor->infoTSVInicio['matricula'] = $this->servidorAtual->getMatriculaAnterior();
        }
        $dadosTVSEInicio = $servidorEntity->getContratoTrabalho();
        $dadoServidor->infoTSVInicio['codCateg'] = $dadosTVSEInicio['codCateg'];
        $dadoServidor->infoTSVInicio['natAtividade'] = 1; // 1 - Trabalho urbano 2 - Trabalho rural
        $dadoServidor->infoTSVInicio['nrProcTrab'] = '';
        $dadoServidor->infoTSVInicio['dtInicio'] = $this->servidorAtual->getDataAdmissao()->getDate();
        //Regra - Trabalhador Sem Vínculo de Emprego/Estatutário - TSVE - Início.
        $this->regraTSVEInicio($dadoServidor);

        //Informações de mudança de CPF do trabalhador.
        $dadoServidor->infoTSVInicio['mudancaCPF'] = $servidorEntity->getMudancaCPF();
        //Regra - Informações de mudança de CPF do trabalhador.
        $this->regraMudancaCpf($dadoServidor);

        //Grupo onde são fornecidas informações complementares, preenchidas conforme a categoria do TSVE.
        $dadoServidor->infoTSVInicio['infoComplementares'] = [];

        //Grupo que apresenta o cargo e/ou função ocupada pelo TSVE.
        $dadosCargoFuncao = $servidorEntity->getCargoFuncaoSemVinculo();
        if (isset($dadosCargoFuncao['nmCargo'])) {
            $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmCargo'] =
            $dadosCargoFuncao['nmCargo'];
        }
        if (isset($dadosCargoFuncao['CBOCargo'])) {
            $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOCargo'] =
            $dadosCargoFuncao['CBOCargo'];
        }
        if (isset($dadosCargoFuncao['nmFuncao'])) {
            $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmFuncao'] =
            $dadosCargoFuncao['nmFuncao'];
        }
        if (isset($dadosCargoFuncao['CBOFuncao'])) {
            $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOFuncao'] =
            $dadosCargoFuncao['CBOFuncao'];
        }
        //Regra - Grupo que apresenta o cargo e/ou função ocupada pelo TSVE.
        $this->regraInfoComplementaresCargoFuncao($dadoServidor);

        //Informações da remuneração e periodicidade de pagamento.
        $dadoServidor->infoTSVInicio['infoComplementares']['remuneracao'] = $servidorEntity->getRemuneracao();
        //Regra - Informações da remuneração e periodicidade de pagamento.
        $this->regraRemuneracao($dadoServidor);

        //Informações do Fundo de Garantia do Tempo de Serviço - FGTS.
        if (!empty($this->servidorAtual->getDataOptanteFgts())) {
            $dadoServidor->infoTSVInicio['infoComplementares']['FGTS']['dtOpcFGTS'] =
                $this->servidorAtual->getDataOptanteFgts()->rh15_data;
        }
        //Regra - Informações do Fundo de Garantia do Tempo de Serviço - FGTS.
        $this->regraFGTS($dadoServidor);

        //Informações relativas ao dirigente sindical.
        $dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']['categOrig'] =
            (int) $this->servidorAtual->getVinculo()->getCodigoCategoria();
        if ($this->servidorAtual->getLocalTrabalhoPrincial() !== null) {
            $dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']['tpInsc'] =
            (int) $this->servidorAtual->getLocalTrabalhoPrincial()->getTipoInscricao();
            $dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']['nrInsc'] =
            $this->servidorAtual->getLocalTrabalhoPrincial()->getInstituicao()->getCNPJ();
        }

        $dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']['dtAdmOrig'] =
            $this->servidorAtual->getDataAdmissao()->getDate();
        $dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']['matricOrig'] =
            $this->servidorAtual->getMatricula();
        $dadosDirigenteSindical = $servidorEntity->getVinculoTrabalho();
        $dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']['tpRegTrab'] =
            $dadosDirigenteSindical['tpRegTrab'];
        $dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']['tpRegPrev'] =
            $dadosDirigenteSindical['tpRegPrev'];

        $this->regraDirigenteSindical($dadoServidor);


        if (!empty($servidorEntity->getCedencia())) {
            $dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido'] = $servidorEntity->getCedencia();
        }

        $this->regraCedencia($dadoServidor);

        $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['categOrig'] =
            (int) $this->servidorAtual->getVinculo()->getCodigoCategoria();

        if (!empty($this->movimentacaoAtual->getLotacao())) {
            $lotacao = LotacaoRepository::getInstanceByCodigo($this->movimentacaoAtual->getLotacao());
            if (!empty($lotacao) && ($lotacao->getCgm() instanceof CgmJuridico)) {
                $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['cnpjOrig'] =
                    $lotacao->getCgm()->getCnpj();
            }
        }

        $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['matricOrig'] =
            $this->servidorAtual->getMatricula();

        $admissaoDados = new \AdmissaoDado($this->servidorAtual->getMatricula());
        if (!empty($admissaoDados->getDataNomeacao())) {
            $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['dtExercOrig'] =
                $admissaoDados->getDataNomeacao();
        }
        $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['indRemunCargo'] = '';
        $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['tpRegTrab'] =
            $dadosDirigenteSindical['tpRegTrab'];
        $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['tpRegPrev'] =
            $dadosDirigenteSindical['tpRegPrev'];

        $this->regraInfoMandElet($dadoServidor);


        //Informações relativas a servidor público estagiário.
        $dadosEstagiario = $servidorEntity->getEstagiario();
        $dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario'] = [];
        if (sizeof($dadosEstagiario) > 0) {
            $dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario'] = $dadosEstagiario['infoEstagiario'];
        }

        $this->regraEstagiario($dadoServidor);

        $dadosLocalTrabalho = $servidorEntity->getLocalTrabalhoContrato();
        $codigoCategoria = (int) $dadoServidor->infoTSVInicio["codCateg"];
        $codigoCategoriaValidos = [304, 305, 721, 722, 723, 731, 734, 738, 761, 771, 901, 902, 906];

        $obrigatorio = false;

        switch ((int) $codigoCategoria) {
            case ($codigoCategoria >= 200 && $codigoCategoria <= 299):
                $obrigatorio = true;
                break;
            case ($codigoCategoria >= 400 && $codigoCategoria <= 499):
                $obrigatorio = true;
                break;
            case in_array($codigoCategoria, $codigoCategoriaValidos):
                $obrigatorio = true;
                break;
            default:
                $obrigatorio = false;
        }
        if ($obrigatorio) {
            $dadoServidor->infoTSVInicio['infoComplementares']['localTrabGeral'] = [];
            if (sizeof($dadosLocalTrabalho) > 0 && $obrigatorio) {
                $dado = $dadosLocalTrabalho['localTrabGeral'];
                $dadoServidor->infoTSVInicio['infoComplementares']['localTrabGeral'] = $dado;
            }
        }


        if (empty($dadoServidor->infoTSVInicio['infoComplementares'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']);
        }

        if ($this->servidorAtual->getVinculo()->getCodigoCategoria() == '410') {
            if (isset($dadosCargoFuncao['nmFuncao'])) {
                $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmFuncao'] =
                    $dadosCargoFuncao['nmFuncao'];
            }
            if (isset($dadosCargoFuncao['CBOFuncao'])) {
                $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOFuncao'] =
                    $dadosCargoFuncao['CBOFuncao'];
            }
        } else {
            $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmCargo'] =
            $dadosCargoFuncao['nmCargo'];
            $dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOCargo'] =
            $dadosCargoFuncao['CBOCargo'];
        }

        $dadoServidor->infoTSVInicio['infoComplementares']['remuneracao'] = $servidorEntity->getRemuneracao();
        $dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido'] = $servidorEntity->getCedencia();

        if ($dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido'] == null) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido']);
        }

        $servidor = ServidorRepository::getInstanciaByCodigo($this->servidorAtual->getMatricula());
        $dataObrigatoriedade = DBPessoal::getDataFaseEsocial(2);
        $servidorValidaData = AssentamentoRepository::servidorValidaData($servidor, $dataObrigatoriedade);

        if (!empty($servidorValidaData->h16_dtconc)) {
            $dadoServidor->infoTSVInicio['afastamento']['dtIniAfast'] = $servidorValidaData->h16_dtconc;
        }

        if (!empty($servidorValidaData->db109_valordefault)) {
            $dadoServidor->infoTSVInicio['afastamento']['codMotAfast'] = $servidorValidaData->db109_valordefault;
        }

        $this->regraAfastamento($dadoServidor);
    }

    /**
    * @param $dadoServidor
    */
    private function regraTSVEInicio(&$dadoServidor)
    {
        if (!isset($dadoServidor->infoTSVInicio['matricula']) ||
            empty($dadoServidor->infoTSVInicio['matricula'])) {
                unset($dadoServidor->infoTSVInicio['matricula']);
        }
        if (!isset($dadoServidor->infoTSVInicio['nrProcTrab']) ||
            empty($dadoServidor->infoTSVInicio['nrProcTrab'])) {
                unset($dadoServidor->infoTSVInicio['nrProcTrab']);
        }
        if (!isset($dadoServidor->infoTSVInicio['natAtividade']) ||
            empty($dadoServidor->infoTSVInicio['natAtividade'])) {
                unset($dadoServidor->infoTSVInicio['natAtividade']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraMudancaCpf(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVInicio['mudancaCPF']['observacao'])) {
            unset($dadoServidor->infoTSVInicio['mudancaCPF']['observacao']);
        }
        if (empty($dadoServidor->infoTSVInicio['mudancaCPF']['matricAnt'])) {
            unset($dadoServidor->infoTSVInicio['mudancaCPF']['matricAnt']);
        }
        if (empty($dadoServidor->infoTSVInicio['mudancaCPF']['matricAnt'])) {
            unset($dadoServidor->infoTSVInicio['mudancaCPF']['matricAnt']);
        }

        $flagMudancaCpf = true;

        if (isset($dadoServidor->infoTSVInicio['mudancaCPF']['cpfAnt']) &&
            !empty($dadoServidor->infoTSVInicio['mudancaCPF']['cpfAnt'])) {
            $flagMudancaCpf = false;
        }
        if (isset($dadoServidor->infoTSVInicio['mudancaCPF']['matricAnt']) &&
            !empty($dadoServidor->infoTSVInicio['mudancaCPF']['matricAnt'])) {
            $flagMudancaCpf = false;
        }
        if (isset($dadoServidor->infoTSVInicio['mudancaCPF']['dtAltCPF']) &&
            !empty($dadoServidor->infoTSVInicio['mudancaCPF']['dtAltCPF'])) {
            $flagMudancaCpf = false;
        }
        if (isset($dadoServidor->infoTSVInicio['mudancaCPF']['observacao']) &&
            !empty($dadoServidor->infoTSVInicio['mudancaCPF']['observacao'])) {
            $flagMudancaCpf = false;
        }

        if ($flagMudancaCpf) {
            unset($dadoServidor->infoTSVInicio['mudancaCPF']);
        }
    }
    /**
    * @param $dadoServidor
    */
    private function regraInfoComplementaresCargoFuncao(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmCargo'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmCargo']);
        }
        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOCargo'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOCargo']);
        }
        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmFuncao'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmFuncao']);
        }
        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOFuncao'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOFuncao']);
        }

        if ($this->servidorAtual->getVinculo()->getCodigoCategoria() == '410') {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmCargo']);
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOCargo']);
        } else {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmFuncao']);
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOFuncao']);
        }

        $flagCargoFuncaoVazio = true;

        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmCargo'])) {
            $flagCargoFuncaoVazio = false;
        }
        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOCargo'])) {
            $flagCargoFuncaoVazio = false;
        }
        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['nmFuncao'])) {
            $flagCargoFuncaoVazio = false;
        }
        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']['CBOFuncao'])) {
            $flagCargoFuncaoVazio = false;
        }

        if ($flagCargoFuncaoVazio) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['cargoFuncao']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraRemuneracao(&$dadoServidor)
    {

        $flagRegraRemuneracao = true;
        if (isset($dadoServidor->infoTSVInicio['infoComplementares']['remuneracao']['dscSalVar']) &&
            empty($dadoServidor->infoTSVInicio['infoComplementares']['remuneracao']['dscSalVar'])) {
                unset($dadoServidor->infoTSVInicio['infoComplementares']['remuneracao']['dscSalVar']);
        }
        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['remuneracao']['vrSalFx'])) {
            $flagRegraRemuneracao = false;
        }
        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['remuneracao']['undSalFixo'])) {
            $flagRegraRemuneracao = false;
        }
        if ($flagRegraRemuneracao) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['remuneracao']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraFGTS(&$dadoServidor)
    {
        if (isset($dadoServidor->infoTSVInicio['infoComplementares']['FGTS']['dtOpcFGTS']) &&
            empty($dadoServidor->infoTSVInicio['infoComplementares']['FGTS']['dtOpcFGTS'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['FGTS']['dtOpcFGTS']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['FGTS'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['FGTS']);
        }
        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['FGTS']['dtOpcFGTS'])) {
            $dataObrigatoriedade = DBPessoal::getDataFaseEsocial(2)->getDate();
            if (strtotime($dadoServidor->infoTSVInicio['infoComplementares']['FGTS']['dtOpcFGTS']) <
                strtotime($dataObrigatoriedade)) {
                unset($dadoServidor->infoTSVInicio['infoComplementares']['FGTS']);
            }
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraDirigenteSindical(&$dadoServidor)
    {
        if (!in_array($this->servidorAtual->getVinculo()->getCodigoCategoria(), ['201','401','721'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoDirigenteSindical']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraCedencia(&$dadoServidor)
    {
        if (!empty($dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido'])) {
            $dadoRegraCedencia = $dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido'];
            if (!empty($dadoRegraCedencia)) {
                if (empty($dadoRegraCedencia['categOrig']) &&
                    empty($dadoRegraCedencia['cnpjCednt']) &&
                    empty($dadoRegraCedencia['matricCed']) &&
                    empty($dadoRegraCedencia['dtAdmCed']) &&
                    empty($dadoRegraCedencia['tpRegTrab']) &&
                    empty($dadoRegraCedencia['tpRegPrev']) &&
                    !(in_array($this->servidorAtual->getVinculo()->getCodigoCategoria(), ['305','410']))) {
                    unset($dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido']);
                }
            } else {
                unset($dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido']);
            }

            if ($this->servidorAtual->getVinculo()->getCodigoCategoria() == '305') {
                $dadoServidor->infoTSVInicio['infoComplementares']['infoTrabCedido']['tpRegPrev'] = 2;
            }
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraInfoMandElet(&$dadoServidor)
    {
        //REGRA INSERIDA POR NÃO POSSUI ESTE CAMPO
        if (in_array($this->servidorAtual->getVinculo()->getCodigoCategoria(), ['303','309'])) {
            $dataInicioServidor = new DBDate($dadoServidor->infoTSVInicio['dtInicio']);
            $dataExercicioServidor = $dataInicioServidor->getDiaAnterior()->getDate();

            $dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['dtExercOrig'] = $dataExercicioServidor;
            if (isset($dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['indRemunCargo']) &&
                empty($dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['indRemunCargo'])) {
                unset($dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']['indRemunCargo']);
            }
        } else {
            if (isset($dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet'])) {
                unset($dadoServidor->infoTSVInicio['infoComplementares']['infoMandElet']);
            }
        }
    }
    /**
    * @param $dadoServidor
    */
    private function regraEstagiario(&$dadoServidor)
    {
        if (in_array($dadoServidor->infoTSVInicio['codCateg'], [721, 722, 771, 901])) {
             unset($dadoServidor->infoTSVInicio['natAtividade']);
        }
        $dadosCNPJ = $dadoServidor->
            infoTSVInicio['infoComplementares']['infoEstagiario']['ageIntegracao']['cnpjAgntInteg'];
        if (empty($dadosCNPJ)) {
            unset($dadoServidor->
                infoTSVInicio['infoComplementares']['infoEstagiario']['ageIntegracao']);
        }
        $dadosCPF = $dadoServidor->
            infoTSVInicio['infoComplementares']['infoEstagiario']['supervisorEstagio']['cpfSupervisor'];
        if (empty($dadosCPF)) {
            unset($dadoServidor->
                infoTSVInicio['infoComplementares']['infoEstagiario']['supervisorEstagio']);
        }

        $dadoCnpj = $dadoServidor->
            infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['cnpjInstEnsino'];
        if (empty($dadoCnpj)) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['cnpjInstEnsino']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['nmRazao'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['nmRazao']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['dscLograd'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['dscLograd']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['nrLograd'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['nrLograd']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['bairro'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['bairro']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['cep'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['cep']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['codMunic'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['codMunic']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['uf'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['instEnsino']['uf']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['areaAtuacao'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['areaAtuacao']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['nrApol'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['nrApol']);
        }

        if (empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['dtPrevTerm'])
            && empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['nivEstagio'])
            && empty($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']['natEstagio'])) {
            unset($dadoServidor->infoTSVInicio['infoComplementares']['infoEstagiario']);
        }
    }

     /**
    * @param $dadoServidor
    */
    private function regraAfastamento(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVInicio['afastamento'])) {
            unset($dadoServidor->infoTSVInicio['afastamento']);
        }
    }

    private function posProcessamento(array $dados)
    {
        foreach ($dados as $evento) {
            if (!empty($evento->trabalhador->nascimento->codMunic)) {
                $evento->trabalhador->nascimento->codMunic = (int)$evento->trabalhador->nascimento->codMunic;
            }

            if (!empty($evento->infoTSVInicio->infoComplementares->infoTrabCedido->categOrig)) {
                $evento->infoTSVInicio->infoComplementares->infoTrabCedido
                ->categOrig = (int)$evento->infoTSVInicio->infoComplementares->infoTrabCedido->categOrig;
            }

            if (!empty($evento->infoTSVInicio->termino->dtTerm)) {
                unset($evento->infoTSVInicio->afastamento);
            }

            if (empty($evento->infoTSVInicio->infoComplementares->infoEstagiario->vlrBolsa)) {
                unset($evento->infoTSVInicio->infoComplementares->infoEstagiario->vlrBolsa);
            }
            $this->unsetEmpty($evento);
            $this->atualizaDadosTrabalhador($evento);
        }

        return $dados;
    }

    private function atualizaDadosTrabalhador($evento)
    {
        $servidor = \ServidorRepository::getInstanciaByCodigo($evento->referencia);
        $evento->trabalhador['nmTrab'] = $servidor->getCgm()->getNomeCompleto();
    }
}
