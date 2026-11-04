<?php
/**
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

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use ECidade\RecursosHumanos\ESocial\Entity\Servidor;
use ECidade\RecursosHumanos\ESocial\Service\ServidorService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;

use stdClass;
use DBDate;
use CgmJuridico;

class CadastroBeneficiarioFormatter extends Formatter
{
    /**
     * @var \Servidor
     */
    private $servidorAtual;

    /**
     * @var CgmJuridico
     */
    private $empregador;

    /**
     * @param  array $dados
     * @return array|\Assentamento[]
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $dadosServidor = [];
        foreach ($dados->beneficiarios as $servidor) {
            if (!$servidor->isAtivo()) {
                $this->servidorAtual = $servidor;
                $dadosServidor[] = $this->processar($servidor);
            }
        }
        return $dadosServidor;
    }

    private function processar($servidor)
    {
        $dadoServidor = new \stdClass();
        $dadoServidor->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $this->formatarDados($dadoServidor);

        return $dadoServidor;
    }

    private function formatarDados(&$dadoServidor)
    {
        $cgmServidor = $this->servidorAtual->getCgm();

        $dadoServidor->referencia = $cgmServidor->getCpf();
        $dadoServidor->beneficiario["cpfBenef"] = $cgmServidor->getCpf();
        $dadoServidor->beneficiario["nmBenefic"] = $cgmServidor->getNomeCompleto();
        $dadoServidor->beneficiario["dtNascto"] = $cgmServidor->getDataNascimento();
        $dadoServidor->beneficiario["dtInicio"] = $this->verificaDataAdmissao()->getDate();
        $dadoServidor->beneficiario["sexo"] = $cgmServidor->getSexo();
        $dadoServidor->beneficiario["racaCor"] = (int) $this->deParaRacaCor($this->servidorAtual->getRacaCor());
        $estadoCivil = $this->deParaEstadoCivil($this->servidorAtual->getEstadoCivil());
        if ($estadoCivil) {
            $dadoServidor->beneficiario["estCiv"] = $estadoCivil;
        }
        $dadoServidor->beneficiario["incFisMen"] = $this->verificaDeficiencia();
        $dataLaudo = $this->VerificaDataLaudo();
        if ($dataLaudo) {
            $dadoServidor->beneficiario["dtIncFisMen"] = $dataLaudo;
        }
        $dadoServidor->beneficiario["endereco"] = $this->montarGrupoEndereco();
        $dependentes = $this->montarGrupoDependente();
        if (sizeof($dependentes) > 0) {
            $dadoServidor->beneficiario["dependente"] = $dependentes;
        }
    }

    private function montarGrupoEndereco()
    {
        $retornoEndereco = array();
        $cgmServidor = $this->servidorAtual->getCgm();
        $endereco = new \endereco($cgmServidor->getEnderecoPrimario());

        if (empty($cgmServidor->getCodigoPaisExterior())) {
            $enderecoBrasil = new \stdClass();
            $enderecoBrasil->tpLograd = $endereco->getSiglaRua();
            $enderecoBrasil->dscLograd = $endereco->getDescricaoRua();
            $enderecoBrasil->nrLograd = $endereco->getNumeroLocal();
            if (!empty($endereco->getComplementoEndereco())) {
                $enderecoBrasil->complemento = $endereco->getComplementoEndereco();
            }
            if (!empty($endereco->getDescricaoBairro())) {
                $enderecoBrasil->bairro = $endereco->getDescricaoBairro();
            }
            $enderecoBrasil->cep = $endereco->getCepEndereco();
            $codigoMunicipio = $endereco->getCodigoSistemaExterno();
            if (empty($codigoMunicipio)) {
                $codigoMunicipio = \endereco::getCodigoExternoSistemaByCep(
                    $endereco->getCepEndereco(),
                    $this->servidorAtual->getMatricula()
                );
            }
            $enderecoBrasil->codMunic = $codigoMunicipio;
            $enderecoBrasil->uf = $endereco->getSiglaEstado();

            $retornoEndereco['brasil'] = $enderecoBrasil;
        } else {
            $enderecoExterior = new \stdClass();
            $enderecoExterior->paisResid   = $cgmServidor->getCodigoPaisExterior();
            $enderecoExterior->dscLograd   = $cgmServidor->getLogradouroExterior();
            $enderecoExterior->nrLograd    = $cgmServidor->getNumeroExterior();
            $enderecoExterior->complemento = $cgmServidor->getComplementoExterior();
            $enderecoExterior->bairro      = $cgmServidor->getBairroExterior();
            $enderecoExterior->nmCid       = $cgmServidor->getCidadeExterior();
            $enderecoExterior->codPostal   = $cgmServidor->getCodigoPostalExterior();

            $retornoEndereco['exterior'] = $enderecoExterior;
        }

        return $retornoEndereco;
    }

    private function montarGrupoDependente()
    {

        $retornoDependentes = [];
        foreach ($this->servidorAtual->getDependentes() as $dados) {
            $dependente = [];
            $tpDep = "";
            if (!empty($dados->getTipoParentesco())) {
                $tpDep = $dados->getTipoParentesco();
            } else {
                $tpDep = $this->deParaTipoDependente($dados->getGrauParentesco());
            }
            if (!empty($tpDep)) {
                $dependente["tpDep"] = $tpDep;
                if ($dependente["tpDep"] == "99") {
                    $dependente['descrDep'] = $dados->getDescricaoParentesco();
                }
            }
            $dependente["nmDep"] = $dados->getNome();
            if (!empty($dados->getDataNascimento())) {
                $dependente["dtNascto"] = $dados->getDataNascimento()->getDate();
            }

            $dependente["sexoDep"]   = $dados->getSexo();
            $irrf = $this->deParaIRRF($dados->getTipo());
            if ($irrf) {
                $dependente["depIRRF"] = $irrf;
            }
            if ($dependente["depIRRF"] == 'S') {
                $dependente["cpfDep"]    = $dados->getCpf();
            }
            $incFisMen = $this->deParaCondicaoEspecial($dados->getCondicaoEspecial());
            if ($incFisMen) {
                $dependente["incFisMen"] = $incFisMen;
            }
            $retornoDependentes[] = $dependente;
        }

        return $retornoDependentes;
    }

    private function deParaTipoDependente($vinculoDependente)
    {

        /* aDadosTabela07:
         * 01 - Cônjuge
         * 02 - Companheiro(a) com o(a) qual tenha filho ou viva há mais de 5 (cinco) anos ou possua declaração
         * de união estável
         * 03 - Filho(a) ou enteado(a)
         * 04 - Filho(a) ou enteado(a), universitário(a) ou cursando escola técnica de 2º grau
         * 06 - Irmão(ã), neto(a) ou bisneto(a) sem arrimo dos pais, do(a) qual detenha a guarda judicial
         * 07 - Irmão(ã), neto(a) ou bisneto(a) sem arrimo dos pais, universitário(a) ou cursando escola técnica
         * de 2° grau, do(a) qual detenha a guarda judicial
         * 09 - Pais, avós e bisavós
         * 10 - Menor pobre do qual detenha a guarda judicial
         * 11 - A pessoa absolutamente incapaz, da qual seja tutor ou curador
         * 12 - Ex-cônjuge
         * 99 - Agregado/Outros
         *
         * Tipos de vinculo existentes no sistema (rhdepend.rh31_gparen)
         * C-Conjuge,
         * F-Filho(a),
         * P-Pai,
         * M-Mãe,
         * A-Avó(ô),
         * O-Outros.
         */
        $dePara = array( "A" => "09",
            "C" => "01",
            "F" => "03",
            "M" => "09",
            "P" => "09",
            "O" => "99"
        );
        $retorno = $dePara[$vinculoDependente];
        if (empty($retorno)) {
            return false;
        }
        return $retorno;
    }

    private function deParaIRRF($irrf)
    {

        /*
         * Campo: rhdepend.rh31_irf
         *  Se valor: 0 preencher N-Não
         *  Se valor: 1;2;3;4;5;6;7 e 8 preencher com S-Sim
         */
        $dePara = array(
            "0" => "N",
            "1" => "S",
            "2" => "S",
            "3" => "S",
            "4" => "S",
            "5" => "S",
            "6" => "S",
            "7" => "S",
            "8" => "S",
            ""  => null
        );
        $retorno = $dePara[$irrf];
        if (empty($retorno)) {
            return false;
        }
        return $retorno;
    }

    private function deParaCondicaoEspecial($condicao)
    {

        /*
         * Campo: rhdepend.rh31_especi
         *  Se valor: N preencher N-Não
         *  Se valor: C e S preencher com S-Sim
         */
        $dePara = array( "N" => "N",
            "C" => "S",
            "S" => "S"
        );
        $retorno = $dePara[$condicao];
        if (empty($retorno)) {
            return false;
        }
        return $retorno;
    }

    private function deParaRacaCor($codigo)
    {

        /*
         * Raça e cor do beneficiário.
         * Valores válidos:
         * 1 - Branca
         * 2 - Preta
         * 3 - Parda
         * 4 - Amarela
         * 5 - Indígena
         * 6 - Não informado
         *
         * rhpessoal.rh01_raca
         * 1 - Indígena
         * 2 - Branca
         * 4 - Preta
         * 6 - Amarela
         * 8 - Parda
         * 9 - Não informado
         */
        $dePara = array( "1" => "5",
            "2" => "1",
            "4" => "2",
            "6" => "4",
            "8" => "3",
            "9" => "6",
        );
        $retorno = $dePara[$codigo];
        if (empty($retorno)) {
            $retorno = 9;
        }
        return $retorno;
    }

    private function deParaEstadoCivil($codigo)
    {
        /*
         * Estado civil do beneficiário.
         * Valores válidos:
         * 1 - Solteiro
         * 2 - Casado
         * 3 - Divorciado
         * 4 - Separado
         * 5 - Viúvo
         *
         * rhpessoal.rh01_estciv
         * 1 - Solteiro
         * 2 - Casado
         * 3 - Viuvo
         * 4 - Sep. Consensual
         * 5 - Divorciado
         * 6 - Uniao estavel
         */
        $dePara = array(
            1 => 1,
            2 => 2,
            3 => 5,
            4 => 4,
            5 => 3,
            6 => 2,
            8 => null
        );
        $retorno = $dePara[$codigo];
        if (empty($retorno)) {
            return false;
        }
        return $retorno;
    }

    private function verificaDeficiencia()
    {
        $retorno = "N";
        if ($this->servidorAtual->movimentacao->isDeficienteFisico() == "t"
            || $this->servidorAtual->movimentacao->isPortadorMolestia() == "t"
        ) {
            $retorno = "S";
        }
        return $retorno;
    }

    private function verificaDataLaudo()
    {

        $retorno = false;
        if ($this->verificaDeficiencia() == "S") {
            if (!empty($this->servidorAtual->movimentacao->getDataLaudoMolestia())) {
                $retorno = $this->servidorAtual->movimentacao->getDataLaudoMolestia()->format("Y-m-d");
            }
        }
        return $retorno;
    }

    private function verificaDataAdmissao()
    {

        $dataObrigatoriedade = \DBPessoal::getDataFaseEsocial(2);
        if (empty($dataObrigatoriedade)) {
            $msg  = "Campo Fase 02 (Eventos Não Periódicos) não configurado.\n";
            $msg .= "Verifique o cadastro 'Dados Datas Envios eSocial' acessando o ";
            $msg .= "menu DB:RECURSOSHUMANOS > Pessoal > Procedimentos > Manutenção de Parâmetros > Gerais";
            throw new \Exception($msg);
        }
        $dataAdmissao = $this->servidorAtual->getDataAdmissao();
        if ($dataAdmissao->getTimeStamp() < $dataObrigatoriedade->getTimeStamp()) {
            $dataAdmissao = $dataObrigatoriedade;
        }
        return $dataAdmissao;
    }

    /**
     * Get the value of empregador
     *
     * @return CgmJuridico
     */
    public function getEmpregador()
    {
        return $this->empregador;
    }

    /**
     * Set the value of empregador
     *
     * @param CgmJuridico $empregador
     *
     * @return self
     */
    public function setEmpregador(CgmJuridico $empregador)
    {
        $this->empregador = $empregador;
    }

    public function getServidorAtual()
    {
        return $this->servidorAtual;
    }
}
