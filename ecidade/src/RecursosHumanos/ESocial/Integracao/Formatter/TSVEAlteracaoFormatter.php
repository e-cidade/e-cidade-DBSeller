<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use AssentamentoRepository;
use ECidade\RecursosHumanos\ESocial\Entity\Servidor;
use ECidade\RecursosHumanos\ESocial\Service\ServidorService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use stdClass;
use Exception;
use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

/**
 * Formata os dados do Cargo
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class TSVEAlteracaoFormatter extends Formatter
{
    /**
     * @var
     */
    public $servidorAtual;

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
            $registroAlteracao = ServidorAlteracao::findMatriculaByLayout(
                $servidor->getMatricula(),
                Tipo::S2306,
                false,
                true
            );
            $dataAlterada= null;
            if (!$registroAlteracao) {
                continue;
            }
            if (!empty($registroAlteracao->getDataS2306())) {
                $dataAlterada = $registroAlteracao->getDataS2306()->getDate();
            }

            if (!$servidor->temVinculoEmpregaticio() &&
                $servidor->isAtivo($ignoraException) &&
                $validacao &&
                !empty($dataAlterada)) {
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
        $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout(
            $dadoServidor->referencia,
            Tipo::S2306,
            false,
            true
        );
        $servidorAlteracao->setProcessamentoS2306(true);
        $servidorAlteracao->save();
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
        $this->atualizarGrupoTrabalhador($dadoServidor, $servidorEntity);
        $this->atualizarGrupoAlteracaoContratual($dadoServidor, $servidorEntity);
    }

    /**
     * @param $dadoServidor
     * @param Servidor $servidorEntity
     */
    private function atualizarGrupoTrabalhador(&$dadoServidor, Servidor $servidorEntity)
    {
        //Dados - Informações pessoais do trabalhador.
        $dadoServidor->ideTrabSemVinculo = [];
        $dadoServidor->ideTrabSemVinculo['cpfTrab'] = $servidorEntity->getDadosTrabalhador()['cpfTrab'];
        if ($this->validaEnvioMatricula($this->servidorAtual->getMatricula(), $this->getEmpregador())) {
            $dadoServidor->ideTrabSemVinculo['matricula'] = $servidorEntity->getVinculoTrabalho()['matricula'];
        }
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $dadoServidor->ideTrabSemVinculo['matricula'] = $this->servidorAtual->getMatriculaAnterior();
        }
        $dadoServidor->ideTrabSemVinculo['codCateg'] = $servidorEntity->getContratoTrabalho()['codCateg'];
        if (isset($dadoServidor->trabalhador)) {
            unset($dadoServidor->trabalhador);
        }
        if (isset($dadoServidor->vinculo)) {
            unset($dadoServidor->vinculo);
        }

        //Regras -  Informações pessoais do trabalhador.
        $this->regraPessoalTrabalhador($dadoServidor);
    }

    /**
    * @param $dadoServidor
    */
    private function regraPessoalTrabalhador(&$dadoServidor)
    {
        //PROPRIEDADES ENVIDAS PELO S-2300 E CONFORME REGRA
        //NÃO PODEM SER ENVIADAS NO S-2306
        if (!empty($dadoServidor->ideTrabSemVinculo['matricula'])) {
            unset($dadoServidor->ideTrabSemVinculo['codCateg']);
        }
        if (!empty($dadoServidor->ideTrabSemVinculo['codCateg'])) {
            unset($dadoServidor->ideTrabSemVinculo['matricula']);
        }
    }


    /**
     * @param $dadoServidor
     * @param Servidor $servidorEntity
     */
    private function atualizarGrupoAlteracaoContratual(&$dadoServidor, Servidor $servidorEntity)
    {
        //TSVE - Alteração Contratual.
        $dadoServidor->infoTSVAlteracao = [];

        $dadoServidor->infoTSVAlteracao['dtAlteracao'] = '';

        if (!empty($servidorEntity->getAlteracaoContratualSemVinculo()['dtAlteracao'])) {
            $dadoServidor->infoTSVAlteracao['dtAlteracao'] =
                $servidorEntity->getAlteracaoContratualSemVinculo()['dtAlteracao'];
        }
        $dadoServidor->infoTSVAlteracao['natAtividade'] = 1;
        //Regra - TSVE - Alteração Contratual.
        $this->regraAltercaoContratual($dadoServidor);

        //Grupo que apresenta o cargo e/ou função ocupada pelo TSVE.
        $dadosCargoFuncao = $servidorEntity->getCargoFuncaoSemVinculo();
        if (isset($dadosCargoFuncao['nmCargo'])) {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmCargo'] =
            $dadosCargoFuncao['nmCargo'];
        }
        if (isset($dadosCargoFuncao['CBOCargo'])) {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOCargo'] =
            $dadosCargoFuncao['CBOCargo'];
        }
        if (isset($dadosCargoFuncao['nmFuncao'])) {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmFuncao'] =
            $dadosCargoFuncao['nmFuncao'];
        }
        if (isset($dadosCargoFuncao['CBOFuncao'])) {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOFuncao'] =
            $dadosCargoFuncao['CBOFuncao'];
        }
        //Regra - Grupo que apresenta o cargo e/ou função ocupada pelo TSVE.
        $this->regraInfoComplementaresCargoFuncao($dadoServidor);


        //Informações da remuneração e periodicidade de pagamento.
        $dadoServidor->infoTSVAlteracao['infoComplementares']['remuneracao'] = $servidorEntity->getRemuneracao();
        //Regra - Informações da remuneração e periodicidade de pagamento.
        $this->regraRemuneracao($dadoServidor);

        //Informações relativas ao dirigente sindical.
        $dadosDirigenteSindical = $servidorEntity->getVinculoTrabalho();
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoDirigenteSindical']['tpRegPrev'] =
            $dadosDirigenteSindical['tpRegPrev'];
        //Regra - Informações relativas ao dirigente sindical.
        $this->regraDirigenteSindical($dadoServidor);

        if (!empty($servidorEntity->getCedencia())) {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['infoTrabCedido']['tpRegPrev'] =
                $servidorEntity->getCedencia()['tpRegPrev'];
        }
        //Regra - Informações relativas ao trabalhador cedido/em exercício em outro órgão.
        $this->regraCedencia($dadoServidor);

        //Informações relativas a servidor público exercente de mandato eletivo.
        $dadosMandElet = $servidorEntity->getVinculoTrabalho();
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']['indRemunCargo'] = '';
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']['tpRegPrev'] =
            $dadosMandElet['tpRegPrev'];
        //Regra - Informações relativas a servidor público exercente de mandato eletivo.
        $this->regraInfoMandElet($dadoServidor);

        //Informações relativas ao estagiário ou ao beneficiário.
        $dadosEstagiario = $servidorEntity->getEstagiario();
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['natEstagio'] = '';
        $natEstagio = $dadosEstagiario['infoEstagiario']['natEstagio'];
        if ($natEstagio !== "") {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['natEstagio'] =
            $natEstagio;
        }

        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['nivEstagio'] = null;
        $nivEstagio = $dadosEstagiario['infoEstagiario']['nivEstagio'];
        if ($nivEstagio !== "") {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['nivEstagio'] =
            (int) $nivEstagio;
        }
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['dtPrevTerm'] = '';
        $dtPrevTerm = $dadosEstagiario['infoEstagiario']['dtPrevTerm'];
        if ($dtPrevTerm !== "") {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['dtPrevTerm'] =
            $dtPrevTerm;
        }
        //Regra - Informações relativas ao estagiário ou ao beneficiário
        $this->regraEstagiario($dadoServidor);

        $dadosLocalTrabalho = $servidorEntity->getLocalTrabalhoContrato();
        $dadoServidor->infoTSVAlteracao['infoComplementares']['localTrabGeral'] = [];
        if (sizeof($dadosLocalTrabalho) > 0) {
            $dado = $dadosLocalTrabalho['localTrabGeral'];
            $dadoServidor->infoTSVAlteracao['infoComplementares']['localTrabGeral'] = $dado;
        }

        //Instituição de ensino ou entidade de formação/qualificação.
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['cnpjInstEnsino'] = [];
        $instEnsino = $dadosEstagiario['infoEstagiario']['instEnsino']['cnpjInstEnsino'];
        if ($instEnsino !== "") {
            $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['cnpjInstEnsino'] =
            $instEnsino;
        }
        //Regra - Instituição de ensino ou entidade de formação/qualificação.
        $this->regraEntidadeEnsino($dadoServidor);

        //Agente de integração.
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['ageIntegracao'] = [];
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['ageIntegracao']['cnpjAgntInteg'] =
            $dadosEstagiario['infoEstagiario']['ageIntegracao']['cnpjAgntInteg'];
        //Regra - Agente de integração.
        $this->regraAgente($dadoServidor);

        //Supervisor do estágio.
        $dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['supervisorEstagio']['cpfSupervisor'] =
        $dadosEstagiario['infoEstagiario']['supervisorEstagio']['cpfSupervisor'];
        //Regra - Supervisor do estágio.
        $this->regraSupervisor($dadoServidor);
    }

    /**
    * @param $dadoServidor
    */
    private function regraAltercaoContratual(&$dadoServidor)
    {
        if (!in_array($this->
            servidorAtual->
            getVinculo()->
            getCodigoCategoria(), ['201', '202', '401', '731', '734', '738'])) {
                unset($dadoServidor->infoTSVAlteracao['natAtividade']);
        }
        if (empty($dadoServidor->infoTSVAlteracao['natAtividade'])) {
                unset($dadoServidor->infoTSVAlteracao['natAtividade']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraInfoComplementaresCargoFuncao(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmCargo'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmCargo']);
        }
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOCargo'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOCargo']);
        }
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmFuncao'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmFuncao']);
        }
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOFuncao'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOFuncao']);
        }

        if ($this->servidorAtual->getVinculo()->getCodigoCategoria() == '410') {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmCargo']);
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOCargo']);
        } else {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmFuncao']);
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOFuncao']);
        }

        $flagCargoFuncaoVazio = true;

        if (!empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmCargo'])) {
            $flagCargoFuncaoVazio = false;
        }
        if (!empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOCargo'])) {
            $flagCargoFuncaoVazio = false;
        }
        if (!empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['nmFuncao'])) {
            $flagCargoFuncaoVazio = false;
        }
        if (!empty($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']['CBOFuncao'])) {
            $flagCargoFuncaoVazio = false;
        }

        if ($flagCargoFuncaoVazio) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['cargoFuncao']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraRemuneracao(&$dadoServidor)
    {

        $flagRegraRemuneracao = true;
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['remuneracao']['dscSalVar'])) {
             unset($dadoServidor->infoTSVAlteracao['infoComplementares']['remuneracao']['dscSalVar']);
        }
        if (!empty($dadoServidor->infoTSVAlteracao['infoComplementares']['remuneracao']['vrSalFx'])) {
            $flagRegraRemuneracao = false;
        }
        if (!empty($dadoServidor->infoTSVAlteracao['infoComplementares']['remuneracao']['undSalFixo'])) {
            $flagRegraRemuneracao = false;
        }
        if ($flagRegraRemuneracao) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['remuneracao']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraDirigenteSindical(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoDirigenteSindical']['tpRegPrev'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoDirigenteSindical']);
        }
        if ($this->servidorAtual->getVinculo()->getCodigoCategoria() != '401') {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoDirigenteSindical']);
        }
    }


    /**
    * @param $dadoServidor
    */
    private function regraCedencia(&$dadoServidor)
    {
        if (isset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoTrabCedido'])) {
            if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoTrabCedido']['tpRegPrev'])) {
                unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoTrabCedido']);
            }
            if ($this->servidorAtual->getVinculo()->getCodigoCategoria() != '410') {
                unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoTrabCedido']);
            }
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraInfoMandElet(&$dadoServidor)
    {
        if ($this->servidorAtual->getVinculo()->getCodigoCategoria() == '304') {
            if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']['indRemunCargo'])) {
                unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']['indRemunCargo']);
            }
            if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']['indRemunCargo']) &&
                empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']['tpRegPrev'])) {
                unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']);
            }
        } else {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoMandElet']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraEstagiario(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['nivEstagio'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['nivEstagio']);
        }
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['areaAtuacao'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['areaAtuacao']);
        }
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['nrApol'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['nrApol']);
        }
        if (empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['natEstagio']) &&
            empty($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['dtPrevTerm'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']);
        }
        if (!in_array($this->servidorAtual->getVinculo()->getCodigoCategoria(), [901, 902, 903, 904])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraEntidadeEnsino(&$dadoServidor)
    {
        $dadoCnpj = $dadoServidor->
            infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino'];
        if (empty($dadoCnpj['cnpjInstEnsino'])) {
            unset($dadoServidor->
                infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['cnpjInstEnsino']);
        }

        if (empty($dadoCnpj['nmRazao'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['nmRazao']);
        }

        if (empty($dadoCnpj['dscLograd'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['dscLograd']);
        }

        if (empty($dadoCnpj['nrLograd'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['nrLograd']);
        }

        if (empty($dadoCnpj['bairro'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['bairro']);
        }

        if (empty($dadoCnpj['cep'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['cep']);
        }

        if (empty($dadoCnpj['codMunic'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['codMunic']);
        }

        if (empty($dadoCnpj['uf'])) {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']['instEnsino']['uf']);
        }
    }

    /**
    * @param $dadoServidor
    */
    private function regraAgente(&$dadoServidor)
    {
        $dadosCNPJ = $dadoServidor->
                infoTSVAlteracao['infoComplementares']['infoEstagiario']['ageIntegracao'];
        if (empty($dadosCNPJ['cnpjAgntInteg'])) {
            unset($dadoServidor->
                infoTSVAlteracao['infoComplementares']['infoEstagiario']['ageIntegracao']);
        }
    }

        /**
    * @param $dadoServidor
    */
    private function regraSupervisor(&$dadoServidor)
    {

        $dadosCPF = $dadoServidor->
            infoTSVAlteracao['infoComplementares']['infoEstagiario']['supervisorEstagio'];
        if (empty($dadosCPF['cpfSupervisor'])) {
            unset($dadoServidor->
                infoTSVAlteracao['infoComplementares']['infoEstagiario']['supervisorEstagio']);
        }
        if ($this->servidorAtual->getVinculo()->getCodigoCategoria() != '901') {
            unset($dadoServidor->infoTSVAlteracao['infoComplementares']['infoEstagiario']);
        }
    }
}
