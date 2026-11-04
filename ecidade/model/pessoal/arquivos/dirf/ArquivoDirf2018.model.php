<?php

class ArquivoDirf2018 extends ArquivoDirf2015
{
    /**
     * @var bool
     */
    private $lPossuiPensionistas;

    /**
     *  Escreve a subsecao "BPFDEC"(Beneficiario Pessoa Fisica do Declarante) e seus "RTRT"(rendimentos tributaveis)
     *  @param  array $aDadosPessoaFisica
     *  @return void
     */
    public function escreverLinhasPessoaFisica(array $aDadosPessoaFisica)
    {

        foreach ($aDadosPessoaFisica as $oPessoaFisica) {
            $this->oLayout->setCampoTipoLinha(3);
            $this->oLayout->setCampoIdentLinha("BPFDEC");
            $this->oLayout->setCampo("identificador_registro", 'BPFDEC');
            $this->oLayout->setCampo("nome", $oPessoaFisica->nome);
            $this->oLayout->setCampo("cpf", $oPessoaFisica->cpf);
            $this->oLayout->setCampo("data_laudo", $oPessoaFisica->datalaudo);
            $this->oLayout->setCampo("indicador_identificacao_alimentando", $oPessoaFisica->indicador_identificacao_alimentando);
            $this->oLayout->setCampo("indicador_identificacao_previdencia_complementar", $oPessoaFisica->indicador_identificacao_previdencia_complementar);
            $this->oLayout->geraDadosLinha();


            /**
             * carregamos as informações dos pagamentos
             */
            foreach ($oPessoaFisica->pagamentos as $iTipo => $oPagamento) {

                /**
                 *  Se for algum dos tipos para RRA passa para o próximo
                 */
                if (in_array($iTipo, array(17,18,19,20,21,22,23))) {
                    continue;
                }

                $this->oLayout->setCampoTipoLinha(3);
                $this->oLayout->setCampoIdentLinha("RTRT");
                $aSiglas        = $this->oGerador->getSiglas();
                $iSiglaRegistro = $aSiglas[$iTipo];

                $this->oLayout->setCampo("idetificador_registro", $iSiglaRegistro);

                /**
                 * escreve os meses com cada valor
                 */
                for ($iMes = 1; $iMes <= 13; $iMes++) {
                    $aMes[$iMes] = '';

                    foreach ($oPagamento as $oMes) {
                        if ($oMes->rh98_mes == $iMes) {
                            $nValorDeducao65 = 0;

                            if ($oMes->rh98_rhdirftipovalor == 1) {
                                $nValorDeducao65 = $this->oGerador->getDirf()->getValorDeducaoRIP65($iMes, $oPessoaFisica->pagamentos);
                            }

                            $nValorLancar = $oMes->valor > 0 ? $oMes->valor : 0;
                            $aMes[$iMes] = db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($nValorLancar, 'f')))), 's', '0', 8, 'e', 2);
                        }
                    }
                    $this->oLayout->setCampo($this->aMeses[$iMes], $aMes[$iMes]);
                }
                $this->oLayout->geraDadosLinha();
            }

            if ($oPessoaFisica->previdencia_privada) {
                foreach ($oPessoaFisica->previdencia_privada as $iCgm => $aMeses) {
                    $oEmpresa = CgmRepository::getByCodigo($iCgm);
                    $this->oLayout->setCampoTipoLinha(3);
                    $this->oLayout->setCampoIdentLinha("INFPC");
                    $this->oLayout->setCampo("identificador_registro", "INFPC");
                    $this->oLayout->setCampo("cnpj", $oEmpresa->getCnpj());
                    $this->oLayout->setCampo("nome_empresarial", $oEmpresa->getNome());
                    $this->oLayout->geraDadosLinha();

                    $this->oLayout->setCampoTipoLinha(3);
                    $this->oLayout->setCampoIdentLinha("RTRT");
                    $this->oLayout->setCampo("idetificador_registro", "RTPP");
                    for ($iMes = 1; $iMes <= 13; $iMes++) {
                        $mes = '';
                        if (isset($aMeses[$iMes])) {
                            $mes = $aMeses[$iMes];
                        }
                        $this->oLayout->setCampo($this->aMeses[$iMes], self::converterValor($mes));
                    }
                    $this->oLayout->geraDadosLinha();
                }
            }

            /**
             * escrevemos os dados dos pensionistas
             */
            uasort($oPessoaFisica->pensionistas, function ($primeiro, $proximo) {
                return strcasecmp($primeiro->cpf, $proximo->cpf);
            });
            foreach ($oPessoaFisica->pensionistas as $oPensionista) {
                $this->oLayout->setCampoTipoLinha(3);
                $relacao_depedencia = str_pad($oPensionista->relacao_dependencia, 2, "0", STR_PAD_LEFT);

                if ($oPensionista->relacao_dependencia == 0) {
                    $relacao_depedencia = '';
                }
                $sDataPensionista = implode("", (explode("-", $oPensionista->data_nascimento)));
                $this->oLayout->setCampoIdentLinha("INFPA");
                $this->oLayout->setCampo('cpf', $oPensionista->cpf);
                $this->oLayout->setCampo('nome', $oPensionista->nome);
                $this->oLayout->setCampo('data_nascimento', $sDataPensionista);
                $this->oLayout->setCampo('relacao_dependencia', $relacao_depedencia);

                $this->oLayout->geraDadosLinha();

                $this->oLayout->setCampoTipoLinha(3);
                $this->oLayout->setCampoIdentLinha("RTRT");
                $this->oLayout->setCampo("idetificador_registro", "RTPA");
                for ($iMes = 1; $iMes <= 13; $iMes++) {
                    $mes = '';
                    if (isset($oPensionista->valores[$iMes])) {
                        $mes = $oPensionista->valores[$iMes];
                    }
                    $this->oLayout->setCampo($this->aMeses[$iMes], self::converterValor($mes));
                }
                $this->oLayout->geraDadosLinha();
            }

            /**
             * Outros dados.
             */
            if ($oPessoaFisica->totaloutros > 0) {
                $this->escreverOutrosDados(self::converterValor($oPessoaFisica->totaloutros, 13));
            }
        }
    }

    /**
     *  Escreve a secção DECPJ com suas respctivas subseções de pessoas fisica e juridica e seus IDREC
     *
     *  @param  String $sNomeInstituicao
     *  @return void
     */
    public function escreverSecaoDeclarantePessoaJuridica($sNomeInstituicao)
    {

        $this->oLayout->setCampoTipoLinha(3);
        $this->oLayout->setCampoIdentLinha("DECPJ");
        $this->oLayout->setCampo("identificador_registro", 'DECPJ');
        $this->oLayout->setCampo("responsavel_perante_cnpj", $this->oGerador->getCpfResponsavelCNPJ());
        $this->oLayout->setCampo("cnpj", $this->oGerador->getCnpj());
        $this->oLayout->setCampo("nome_empresarial", $sNomeInstituicao);

        if ($this->oGerador->getNumeroANS() > 0) {
            $this->oLayout->setCampo("plano_privado_assistencia", "S");
        }
        $this->oLayout->geraDadosLinha();

        $aLinhasDirf = $this->oGerador->getRegistros();

        foreach ($aLinhasDirf as $oLinhaDirf) {
            if ($oLinhaDirf->receita == "1889") { //Receita do RRA
                continue;
            }

            $this->escreverLinhaReceita($oLinhaDirf->receita);
            $this->escreverLinhasPessoaFisica($oLinhaDirf->fisica);
            $this->escreverLinhasPessoaJuridica($oLinhaDirf->juridica);
        }
    }

    /**
     *  Escreve a linha do beneficiario do RRA ("BPFRRA")
     *
     * @param $sNome
     * @param $sCpf
     * @return void
     */
    public function escreverLinhaBeneficiarioRRA($sNome, $sCpf)
    {

        $aBeneficiario['identificador']  = "BPFRRA";
        $aBeneficiario['cpf']            = $sCpf;
        $aBeneficiario['nome']           = $sNome;
        $aBeneficiario['natureza']       = "";
        $aBeneficiario['data_molestia']  = "";
        $aBeneficiario['indicador_identificacao_alimentando_rra']  = $this->lPossuiPensionistas ? "S" : "N";
        $aBeneficiario['pipe']           = "";
        $this->oLayout->setByLineOfDBUtils((object)$aBeneficiario, 3, 'BPFRRA');
    }

    /**
     *  Escreve seção do RRA
     *
     *  return void
     */
    public function escreverSecaoRRA()
    {

        /**
         * Após geração a estrutura ficará dessa forma
         *
         * RRA           - Rendimentos recebidos acumuladamente
         *   IDREC       - Identificação do código de receita
         *     BPFRRA    - Beneficiário pessoa física do rendimento recebido acumuladamente
         *       RTRT    - Rendimentos Tributáveis - Rendimento Tributável
         *       RTPO    - Rendimentos Tributáveis - Dedução - Previdência Oficial
         *       RTPA    - Rendimentos Tributáveis - Dedução - Pensão Alimentícia
         *       RTIRF   - Rendimentos Tributáveis - Imposto sobre a Renda Retido na Fonte
         *       RIMOG   - Rendimentos Isentos - Pensão, Aposentadoria ou Reforma por Moléstia Grave
         *       DAJUD   - Despesa com ação judicial
         *       QTMESES - Quantidade de meses
         */
        $aReceitas = $this->oGerador->getBeneficiariosRRAPorReceita();

        /**
         *  Caso não existam registros para por aqui
         */
        if (empty($aReceitas)) {
            return;
        }

        $this->escreverLinhaRRA();

        /**
         *  Percorre as receitas encontradas na geração da DIRF
         */
        foreach ($aReceitas as $iCodigoReceita => $aBeneficiarios) {
            $this->escreverLinhaReceita($iCodigoReceita);

            /**
             *  Percorre os beneficiarios da Receita
             */
            foreach ($aBeneficiarios as $oBeneficiario) {
                $possuiPensionistas = false;
                if (count($oBeneficiario->RTPA) > 0) {
                    $possuiPensionistas = true;
                }

                $this->lPossuiPensionistas = $possuiPensionistas;
                $this->escreverLinhaBeneficiarioRRA($oBeneficiario->nome, $oBeneficiario->cpf);
                /**
                 *  Remove nome e cpf para que possa percorrer as propriedades
                 */
                unset($oBeneficiario->nome, $oBeneficiario->cpf);

                /**
                 *  Percorre as propriedades do objeto com as registros mensais
                 */
                foreach ($oBeneficiario as $sTipoRegistro => $aCompetencias) {
                    if (empty($aCompetencias)) {
                        continue;
                    }

                    switch ($sTipoRegistro) {
                        case 'RTPA':
                            foreach ($aCompetencias as $oPensionista) {
                                $relacao_depedencia = $oPensionista->relacao_dependencia;

                                if ($oPensionista->relacao_dependencia == 0) {
                                    $relacao_depedencia = '';
                                }
                                $sDataPensionista = implode("", (explode("-", $oPensionista->data_nascimento)));
                                $this->oLayout->setCampoTipoLinha(3);
                                $this->oLayout->setCampoIdentLinha("INFPA");
                                $this->oLayout->setCampo('cpf', $oPensionista->cpf);
                                $this->oLayout->setCampo('nome', $oPensionista->nome);
                                $this->oLayout->setCampo('data_nascimento', $sDataPensionista);
                                $this->oLayout->setCampo('relacao_dependencia', $relacao_depedencia);
                                $this->oLayout->setCampo("idetificador_registro", 'INFPA');
                                $this->oLayout->geraDadosLinha();
                                $this->oLayout->setCampoTipoLinha(3);
                                $this->oLayout->setCampoIdentLinha("RTRT");
                                $this->oLayout->setCampo("idetificador_registro", "RTPA");
                                for ($iMes = 1; $iMes <= 13; $iMes++) {
                                    $mes = '';
                                    if (isset($oPensionista->valores[$iMes])) {
                                        $mes = $oPensionista->valores[$iMes];
                                    }
                                    $this->oLayout->setCampo($this->aMeses[$iMes], self::converterValor($mes));
                                }
                                $this->oLayout->geraDadosLinha();
                            }
                            break;

                        case 'RTPO':
                            $this->oLayout->setCampo("idetificador_registro", $sTipoRegistro);

                            for ($i=1; $i<=12; $i++) {
                                $label = $this->aMeses[$i];
                                $valor = null;
                                if (!empty($aCompetencias[$i])) {
                                    $valor = self::converterValor($aCompetencias[$i], 8);
                                }

                                $this->oLayout->setCampo($label, $valor);
                            }

                            $this->oLayout->geraDadosLinha();
                            break;
                    }

                    if (in_array($sTipoRegistro, array('RTPA', 'RTPO'))) {
                        continue;
                    }
                    $aValores = array_replace($this->aBaseValoresMensais, $aCompetencias);
                    $this->escreverLinhaValoresMensais($sTipoRegistro, $aValores);
                }
            }
        }
    }

    /**
     *  Escreve seção "PSE" Plano de Assistencia a Saude empresarial e seus "OPSE" Operadora Plano de Saude e "TPSE" - Titular do Plano de Saude
     * @throws \BusinessException
     * @throws \DBException
     */
    public function escreverSecaoANS()
    {

        /**
         * geramos as linhas do plano de saude
         */
        if (trim($this->oGerador->getNumeroANS()) != "" || trim($this->oGerador->getNumeroANS2()) != "") {
            $this->oLayout->setCampoTipoLinha(3);
            $this->oLayout->setCampoIdentLinha("PSE");
            $this->oLayout->setCampo("identificador_registro", 'PSE');
            $this->oLayout->geraDadosLinha();

            if (trim($this->oGerador->getNumeroANS()) != "") {
                $oDaoCgm   = db_utils::getDao("cgm");
                $sSqlNome  = $oDaoCgm->sql_query_file($this->oGerador->getCcgmSaude(), "z01_nome, z01_cgccpf");
                $rsNome    = db_query($sSqlNome);

                if (!$rsNome) {
                    throw new DBException("Erro ao buscar os dados da Operadora de Plano de Saude({$this->oGerador->getCcgmSaude()}).");
                }

                if (pg_num_rows($rsNome) == 0) {
                    throw new BusinessException("Nenhuma informação sobre Operadora de Plano de Saude foi encontrada.");
                }

                $oOperador = db_utils::fieldsMemory($rsNome, 0);

                $this->oLayout->setCampoTipoLinha(3);
                $this->oLayout->setCampoIdentLinha("OPSE");
                $this->oLayout->setCampo("identificador_registro", 'OPSE');
                $this->oLayout->setCampo("cnpj", str_pad($oOperador->z01_cgccpf, 14, "0", STR_PAD_LEFT));
                $this->oLayout->setCampo("nome", $oOperador->z01_nome);
                $this->oLayout->setCampo("registro_ans", str_pad($this->oGerador->getNumeroANS(), 6, "0", STR_PAD_LEFT));
                $this->oLayout->geraDadosLinha();

                /**
                 * geramos todas as pessoas que possuem valor do plano de saude maior que zero.
                 */
                $aLinhasDirf = $this->oGerador->getRegistros();

                foreach ($aLinhasDirf as $oLinhaDirf) {
                    foreach ($oLinhaDirf->fisica as $oPessoaFisica) {
                        if ($oPessoaFisica->totalsaude1 > 0) {
                            $nValorAno = db_formatar(str_replace(',', '', str_replace(
                                '.',
                                '',
                                trim(db_formatar($oPessoaFisica->totalsaude1, 'f'))
                            )), 's', '0', 9, 'e', 2);
                            $this->oLayout->setCampoTipoLinha(3);
                            $this->oLayout->setCampoIdentLinha("TPSE");
                            $this->oLayout->setCampo("identificador_registro", 'TPSE');
                            $this->oLayout->setCampo("cnpj", str_pad($oPessoaFisica->cpf, 11, "0", STR_PAD_LEFT));
                            $this->oLayout->setCampo("nome", $oPessoaFisica->nome);
                            $this->oLayout->setCampo("valor_ano", $nValorAno);
                            $this->oLayout->geraDadosLinha();
                        }
                    }
                }
            }

            if (trim($this->oGerador->getNumeroANS2()) != "") {
                $oDaoCgm   = db_utils::getDao("cgm");
                $sSqlNome  = $oDaoCgm->sql_query_file($this->oGerador->getCcgmSaude2(), "z01_nome, z01_cgccpf");
                $rsNome    = db_query($sSqlNome);

                if (!$rsNome) {
                    throw new DBException("Erro ao buscar os dados da Operadora de Plano de Saude({$this->oGerador->getCcgmSaude2()}).");
                }

                if (pg_num_rows($rsNome) == 0) {
                    throw new BusinessException("Nenhuma informação sobre Operadora de Plano de Saude foi encontrada.");
                }

                $oOperador = db_utils::fieldsMemory($rsNome, 0);

                $this->oLayout->setCampoTipoLinha(3);
                $this->oLayout->setCampoIdentLinha("OPSE");
                $this->oLayout->setCampo("identificador_registro", 'OPSE');
                $this->oLayout->setCampo("cnpj", str_pad($oOperador->z01_cgccpf, 14, "0", STR_PAD_LEFT));
                $this->oLayout->setCampo("nome", $oOperador->z01_nome);
                $this->oLayout->setCampo("registro_ans", str_pad($this->oGerador->getNumeroANS2(), 6, "0", STR_PAD_LEFT));
                $this->oLayout->geraDadosLinha();

                /**
                 * geramos todas as pessoas que possuem valor do plano de saude maior que zero.
                 */
                foreach ($aLinhasDirf as $oLinhaDirf) {
                    foreach ($oLinhaDirf->fisica as $oPessoaFisica) {
                        if ($oPessoaFisica->totalsaude2 > 0) {
                            $nValorAno = db_formatar(str_replace(',', '', str_replace(
                                '.',
                                '',
                                trim(db_formatar($oPessoaFisica->totalsaude2, 'f'))
                            )), 's', '0', 9, 'e', 2);
                            $this->oLayout->setCampoTipoLinha(3);
                            $this->oLayout->setCampoIdentLinha("TPSE");
                            $this->oLayout->setCampo("identificador_registro", 'TPSE');
                            $this->oLayout->setCampo("cnpj", str_pad($oPessoaFisica->cpf, 11, "0", STR_PAD_LEFT));
                            $this->oLayout->setCampo("nome", $oPessoaFisica->nome);
                            $this->oLayout->setCampo("valor_ano", $nValorAno);
                            $this->oLayout->geraDadosLinha();
                        }
                    }
                }
            }
        }
    }

    /**
     *  Escreve linha "INF" informações complementares para o comprovante de rendimentos
     *  @return void
     */
    public function escreverInformacoesComplementares()
    {

        $aLinhasDirf = $this->oGerador->getRegistros();
        $aInformacoesComplementares = array();

        foreach ($aLinhasDirf as $oLinhaDirf) {
            foreach ($oLinhaDirf->fisica as $oPessoaFisica) {
                if ($oPessoaFisica->informacao_complementar != '') {
                    $aInformacoesComplementares[$oPessoaFisica->cpf] = substr($oPessoaFisica->informacao_complementar, 0, 500);
                }
            }
        }
        ksort($aInformacoesComplementares);
        $aInformacoesJaImpressas = array();

        foreach ($aInformacoesComplementares as $cpf => $informacao) {
            $oPessoaFisica->identificador_registro  = 'INF';
            $oPessoaFisica->cpf                     = $cpf;
            $oPessoaFisica->Pipe                    = '';
            $oPessoaFisica->informacao_complementar = substr($informacao, 0, 500);
            $this->oLayout->setByLineOfDBUtils($oPessoaFisica, 3, 'INF');
        }
    }
}
