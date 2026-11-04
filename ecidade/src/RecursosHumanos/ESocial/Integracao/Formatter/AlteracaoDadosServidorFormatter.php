<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use ECidade\RecursosHumanos\ESocial\Service\ServidorService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use stdClass;

/**
 * Formata os dados de Alteração Cadastral do Servidor
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class AlteracaoDadosServidorFormatter extends ServidorFormatter
{
    private $servidorAtual;
    private $servidorEntity;

    private $deParaRacaCor = [
        1 => 5,
        2 => 1,
        6 => 4,
        8 => 3,
        9 => '',
        4 => 2,
        11 => '',
    ];


    private $deParaEstadoCivil = [
        1 => 1,
        2 => 2,
        5 => 3,
        4 => 4,
        3 => 5,
        6 => 1
    ];

    private $deParaGrauInstrucao = [
        0  => "",
        10 => '12',
        11 => '10',
        12 => '11'
    ];

    private $dataAlteracao;

    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($servidores, $alteracao = false)
    {
        $retorno = [];

        foreach ($servidores as $servidor) {
            if ($servidor->isAtivo() && !$servidor->isRescindido()) {
                $this->dataAlteracao =
                    ServidorAlteracao::findMatriculaByLayout(
                        $servidor->getMatricula(),
                        Tipo::S2205,
                        false,
                        true
                    );
                if ($this->dataAlteracao) {
                    $retorno[] = $this->processamento($servidor);
                    $this->dataAlteracao->setProcessamentoS2205(true);
                    $this->dataAlteracao->save();
                }
            }
        }

        return $retorno;
    }

    /**
     * @param  $servidor
     * @return mixed
     * @throws \BusinessException
     * @throws \DBException
     */
    private function processamento($servidor)
    {
        $dadoServidor        = new stdClass();
        $this->servidorAtual = $servidor;

        $servidorMovimentacaoRepository = new ServidorMovimentacaoRepository();
        $servidorMovimentacaoModel      = $servidorMovimentacaoRepository
            ->scopeAno($this->servidorAtual->getAnoCompetencia())
            ->scopeMes($this->servidorAtual->getMesCompetencia())
            ->scopeMatricula($this->servidorAtual->getMatricula())
            ->first();

        $servidorService = new ServidorService(
            $this->servidorAtual,
            $servidorMovimentacaoModel,
            clone($dadoServidor)
        );

        $this->servidorEntity = $servidorService->buscarDadosServidor();

        $dadoServidor->referencia           = $this->servidorAtual->getMatricula();
        $dadoServidor->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $dadoServidor->ideEvento            = new stdClass();

        $dadoServidor->ideEmpregador         = new stdClass();
        $dadoServidor->ideEmpregador->tpInsc = 1;
        $dadoServidor->ideEmpregador->nrInsc = 1;

        $dadoServidor->ideTrabalhador          = new stdClass();
        $dadoServidor->ideTrabalhador->cpfTrab = $this->servidorAtual->getCgm()->getCpf();

        $dadoServidor->alteracao = $this->dadosAlteracao();
        return $dadoServidor;
    }

    /**
     * Dados de alteração
     * @return stdClass
     */
    private function dadosAlteracao()
    {
        $alteracao = new stdClass();

        $alteracao->dtAlteracao      = $this->dataAlteracao->getDataS2205()->getDate();
        $alteracao->dadosTrabalhador = $this->dadosTrabalhador();

        return $alteracao;
    }

    /**
     * Dados do trabalhador
     * @return stdClass
     */
    private function dadosTrabalhador()
    {
        $dados = [];

        $dados['nmTrab'] = $this->servidorAtual->getCgm()->getNomeCompleto();
        $dados['sexo'] = $this->servidorAtual->getSexo();
        $dados['racaCor'] = $this->deParaRacaCor[$this->servidorAtual->getRacaCor()];
        if (!empty($this->deParaEstadoCivil[$this->servidorAtual->getEstadoCivil()])) {
            $dados['estCiv'] = $this->deParaEstadoCivil[$this->servidorAtual->getEstadoCivil()];
        }
        $dados['grauInstr'] = str_pad($this->servidorAtual->getGrauInstrucao(), 2, '0', STR_PAD_LEFT);

        if (array_key_exists($this->servidorAtual->getGrauInstrucao(), $this->deParaGrauInstrucao)) {
            $dados['grauInstr'] = $this->deParaGrauInstrucao[$this->servidorAtual->getGrauInstrucao()];
        }

        $dados['nmSoc'] = $this->servidorEntity->getDadosTrabalhador()['nmSoc'];
        $dados['paisNac'] = $this->servidorAtual->getCgm()->getCodigoPaisNacionalidade();

        if ($this->servidorAtual->getCgm()->getNacionalidade() == '1' ||
            empty($this->servidorAtual->getCgm()->getNacionalidade())
        ) {
            $dados['paisNac'] = '105';
        }

        $dados['endereco'] = $this->montaGrupoEndereco();

        $dados['trabImig'] = $this->servidorEntity->getImigrante();

        $dados['infoDeficiencia'] = $this->montaGrupoDeficiencia();

        $dados['dependente'] = $this->montaGrupoDependentes();

        $contato = $this->montaDadosContato();
        if (!empty($contato)) {
            $dados['contato'] = $contato;
        }

        /**
         * Remove grupos não preenchidos.
         */

        if (!isset($dados['trabImi']->tmpResid) &&
            (!isset($dados['trabImig']->condIng) || $dados['trabImig']->condIng == 0)) {
            unset($dados['trabImig']);
        }

        if (empty($dados['infoDeficiencia'])) {
            unset($dados['infoDeficiencia']);
        }

        return $dados;
    }

    /**
     * Retorna Grupo Endereço
     * @return stdClass
     * @throws \Exception
     */
    private function montaGrupoEndereco()
    {
        $dadosEndereco = new stdClass();
        $cgmServidor   = $this->servidorAtual->getCgm();
        $endereco      = new \endereco($cgmServidor->getEnderecoPrimario());

        if (empty($cgmServidor->getCodigoPaisExterior())) {
            $dadosEndereco->brasil              = new stdClass();
            $dadosEndereco->brasil->tpLograd    = $endereco->getSiglaRua();
            $dadosEndereco->brasil->dscLograd   = $cgmServidor->getLogradouro();
            $dadosEndereco->brasil->nrLograd    = $cgmServidor->getNumero();
            $dadosEndereco->brasil->complemento = $cgmServidor->getComplemento();
            $dadosEndereco->brasil->bairro      = $cgmServidor->getBairro();
            $dadosEndereco->brasil->cep         = $cgmServidor->getCep();
            $codigoMunicipio                    = $endereco->getCodigoSistemaExterno();
            if (empty($codigoMunicipio)) {
                $codigoMunicipio = \endereco::getCodigoExternoSistemaByCep($cgmServidor->getCep());
            }
            $dadosEndereco->brasil->codMunic = $codigoMunicipio ? (int)$codigoMunicipio : null;
            $dadosEndereco->brasil->uf       = $cgmServidor->getUf();
        } else {
            $dadosEndereco->exterior              = new stdClass();
            $dadosEndereco->exterior->paisResid   = $cgmServidor->getCodigoPaisExterior();
            $dadosEndereco->exterior->dscLograd   = $cgmServidor->getLogradouroExterior();
            $dadosEndereco->exterior->nrLograd    = $cgmServidor->getNumeroExterior();
            $dadosEndereco->exterior->complemento = $cgmServidor->getComplementoExterior();
            $dadosEndereco->exterior->bairro      = $cgmServidor->getBairroExterior();
            $dadosEndereco->exterior->nmCid       = $cgmServidor->getCidadeExterior();
            $dadosEndereco->exterior->codPostal   = $cgmServidor->getCodigoPostalExterior();
        }

        return $dadosEndereco;
    }

    /** Retorna Grupo Deficiencia
     * @return stdClass
     */
    private function montaGrupoDeficiencia()
    {
        $deficiencia = $this->servidorEntity->getDeficiente();

        if (empty($deficiencia['infoCota'])) {
            unset($deficiencia['infoCota']);
        }
        if (empty($deficiencia['observacao'])) {
            unset($deficiencia['observacao']);
        }
        $naoExisteGrupoDeficiencia = ((empty($deficiencia['defFisica'])
                && empty($deficiencia['defVisual'])
                && empty($deficiencia['defAuditiva'])
                && empty($deficiencia['defMental'])
                && empty($deficiencia['defIntelectual'])
                && empty($deficiencia['reabReadap'])
                && empty($deficiencia['infoCota'])) ||
            ($deficiencia['defFisica'] == 'N'
                && $deficiencia['defVisual'] == 'N'
                && $deficiencia['defAuditiva'] == 'N'
                && $deficiencia['defMental'] == 'N'
                && $deficiencia['defIntelectual'] == 'N'
                && $deficiencia['reabReadap'] == 'N'
                && $deficiencia['infoCota'] == 'N'));

        if ($naoExisteGrupoDeficiencia) {
            return null;
        } else {
            //Validação conforme layout eSocial v1.2 item 53 do S-2205
            if ((int) $this->servidorEntity->getVinculoTrabalho()['tpRegTrab'] != 1) {
                unset($deficiencia['infoCota']);
            }
            if (isset($deficiencia['infoCota'])) {
                $informaSim = false;
                foreach ($deficiencia as $opcao => $resposta) {
                    if ($opcao == 'observacao') {
                        continue;
                    }
                    if ($opcao == 'infoCota') {
                        continue;
                    }
                    if ($resposta == 'S') {
                        $informaSim = true;
                    }
                }
                if ((!$informaSim and $deficiencia['infoCota']='S')) {
                    $deficiencia['infoCota']='N';
                }
            }

            return $deficiencia;
        }
    }

    /**
     * Retorna grupo de dependentes
     * @return stdClass
     */
    private function montaGrupoDependentes()
    {
        $dependentes = $this->servidorEntity->getDependentes();

        if (!empty($dependentes)) {
            foreach ($dependentes as $chave => &$dependente) {
                if (empty($dependente['dtNascto'])) {
                    unset($dependente['dtNascto']);
                }

                if (empty($dependente['cpfDep'])) {
                    unset($dependente['cpfDep']);
                }
                // Implementada regra conforme layout do eSocial
                // sexo do dependente nao pode ser enviado para servidores tpRegPrev != 2
                if ($this->servidorEntity->getVinculoTrabalho()['tpRegPrev'] != 2) {
                    unset($dependente['sexoDep']);
                }
                // Caso servidor enviado no S2300, é removido o sexo do dependente, seguindo o layout do eSocial
                if (!$this->servidorAtual->temVinculoEmpregaticio() && !$this->servidorAtual->isEstagiario()) {
                    unset($dependente['sexoDep']);
                }

                if (empty($dependente['nmDep']) && empty($dependente['dtNascto']) && empty($dependente['cpfDep'])) {
                    unset($dependentes[$chave]);
                }

                if ($dependente['tpDep'] != "99" && isset($dependente['descrDep'])) {
                    unset($dependente['descrDep']);
                }
            }
        }
        return $dependentes;
    }

    private function montaDadosContato()
    {
        $contato = $this->servidorEntity->getContato();

        if (empty($contato['fonePrinc'])) {
            unset($contato['fonePrinc']);
        }

        if (empty($contato['emailPrinc'])) {
            unset($contato['emailPrinc']);
        }

        return $contato;
    }
}
