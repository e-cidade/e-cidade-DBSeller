<?php

namespace IntegracaoExterna\Infisc\IntegraDebitos;

use IntegracaoExterna\Infisc\Enums\TipoMovimentoEnum;
use IntegracaoExterna\Infisc\Enums\TipoTributoEnum;

class IntegraDebitosProcessamentoService extends IntegraDebitosBaseService
{

    private $codigoInscricao;

    private $codigoCgm;

    /**
     * @throws \Exception
     */
    public function processar()
    {
        $this->codigoInscricao = $this->buscarCodigoInscricao($this->dados->integra_empresas, $this->dados->cnpj);
        $this->codigoCgm = $this->getCodigoCgm();

        switch ($this->dados->tipo_tributo) {
            case TipoTributoEnum::ISS_VARIAVEL:
                $this->processaIssVariavel();
                break;
            case TipoTributoEnum::ISS_RETIDO:
                $this->processaIssRetido();
                break;
            default:
                throw new \Exception("Tipo de débito não tratado.");
        }
    }

    /**
     * @throws \Exception
     */
    private function processaIssVariavel()
    {
        $codigoTipoDebito = 3;

        if ($this->dados->tipo_movimento == TipoMovimentoEnum::ORIGINAL) {
            $issVarInfo = $this->getIssVarInfo(
                $codigoTipoDebito,
                $this->codigoInscricao,
                $this->dados->ano_competencia,
                $this->dados->mes_competencia,
                null,
                null,
                null,
                $this->codigoCgm
            );

            if ($issVarInfo) {
                $this->atualizaDebito($issVarInfo);
            } else {
                $issVarInfo = $this->criaDebito();
            }
        } elseif ($this->dados->tipo_movimento == TipoMovimentoEnum::COMPLEMENTAR) {
            $issVarInfo = $this->criaDebito();
        } elseif ($this->dados->tipo_movimento == TipoMovimentoEnum::SEM_MOVIMENTO) {
            $issVarInfo = $this->getIssVarInfo(
                $codigoTipoDebito,
                $this->codigoInscricao,
                $this->dados->ano_competencia,
                $this->dados->mes_competencia,
                null,
                null,
                null,
                $this->codigoCgm
            );
        } else {
            throw new \Exception("Tipo de movimento não tratado.");
        }

        if ($issVarInfo) {
            $this->salvarIssvarInfiscIntegraDebitos($issVarInfo->q05_codigo);
        }
    }

    /**
     * @throws \Exception
     */
    private function processaIssRetido()
    {
        if ($this->dados->tipo_movimento != TipoMovimentoEnum::ORIGINAL) {
            throw new \Exception("Tipo de movimento não suportado.");
        }

        $daoIssVar = $this->criaDebito();

        $this->salvarIssvarInfiscIntegraDebitos($daoIssVar->q05_codigo);
    }

    /**
     * @throws \Exception
     */
    private function criaDebito()
    {
        $dadosNumpre = $this->origemManager->runRawQuery(
            "select nextval('numpref_k03_numpre_seq') as numpre"
        )->get();

        if (!$dadosNumpre) {
            throw new \Exception("Ocorreu um erro ao gerar o débito");
        }

        $numpre = $dadosNumpre->numpre;
        $codigoCgm = $this->codigoCgm;

        $daoIssVar = new \cl_issvar();
        $daoIssVar->q05_numpre = $numpre;
        $daoIssVar->q05_numpar = $this->dados->mes_competencia;
        $daoIssVar->q05_valor  = $this->dados->valor;
        $daoIssVar->q05_ano    = $this->dados->ano_competencia;
        $daoIssVar->q05_mes    = $this->dados->mes_competencia;
        $daoIssVar->q05_aliq   = "0";
        $daoIssVar->q05_bruto  = $this->dados->valor;
        $daoIssVar->q05_vlrinf = $this->dados->valor;
        $daoIssVar->incluir(null);

        if ($daoIssVar->erro_status == "0") {
            throw new \Exception($daoIssVar->erro_msg);
        }

        if ($this->codigoInscricao) {
            $daoArreInscr = new \cl_arreinscr();
            $daoArreInscr->k00_numpre = $numpre;
            $daoArreInscr->k00_inscr  = $this->codigoInscricao;
            $daoArreInscr->k00_perc   = 100;

            // exclui se ja existir, senão da duplicate key
            $daoArreInscr->excluir($numpre, $this->codigoInscricao);
            $daoArreInscr->incluir($numpre, $this->codigoInscricao);

            if ($daoArreInscr->erro_status == "0") {
                throw new \Exception($daoArreInscr->erro_msg);
            }
        }

        $daoArreNumcgm = new \cl_arrenumcgm();
        $daoArreNumcgm->k00_numpre = $numpre;
        $daoArreNumcgm->k00_numcgm  = $codigoCgm;

        // exclui se ja existir, senão da duplicate key
        $daoArreNumcgm->excluir($codigoCgm, $numpre);
        $daoArreNumcgm->incluir($codigoCgm, $numpre);

        if ($daoArreNumcgm->erro_status == "0") {
            throw new \Exception($daoArreNumcgm->erro_msg);
        }

        $dadosDebito = $this->getDadosDebitoFromTipo();

        $daoArrecad = new \cl_arrecad();
        $daoArrecad->k00_dtvenc = $dadosDebito->dataVencimento;
        $daoArrecad->k00_numcgm = $codigoCgm;
        $daoArrecad->k00_dtoper = $daoArrecad->k00_dtvenc;
        $daoArrecad->k00_valor  = $this->dados->valor;
        $daoArrecad->k00_numpre = $numpre;
        $daoArrecad->k00_numpar = $this->dados->mes_competencia;
        $daoArrecad->k00_numdig = '0';
        $daoArrecad->k00_tipojm = '0';
        $daoArrecad->k00_numtot = 1;
        $daoArrecad->k00_receit = $dadosDebito->receita;
        $daoArrecad->k00_tipo   = $dadosDebito->tipo;
        $daoArrecad->k00_hist   = $dadosDebito->historico;
        $daoArrecad->incluir();

        if ($daoArrecad->erro_status == '0') {
            throw new \Exception($daoArrecad->erro_msg);
        }

        return $daoIssVar;
    }

    /**
     * @throws \Exception
     */
    private function atualizaDebito($issVarInfo)
    {
        $daoIssVar = new \cl_issvar();
        $daoIssVar->q05_bruto = $this->dados->valor;
        $daoIssVar->q05_valor = $this->dados->valor;
        $daoIssVar->q05_vlrinf = $this->dados->valor;
        $daoIssVar->q05_codigo = $issVarInfo->q05_codigo;
        $daoIssVar->alterar($issVarInfo->q05_codigo);

        if ($daoIssVar->erro_status == "0") {
            throw new \Exception($daoIssVar->erro_msg);
        }

        $daoArrecad = new \cl_arrecad();
        $daoArrecad->k00_valor = $this->dados->valor;
        $daoArrecad->alterar_arrecad(
            "k00_numpre = {$issVarInfo->q05_numpre} and k00_numpar = {$issVarInfo->q05_numpar}"
        );

        if ($daoArrecad->erro_status == '0') {
            throw new \Exception($daoArrecad->erro_msg);
        }
    }

    /**
     * @throws \Exception
     */
    private function getConfiguracaoVencimento()
    {
        $confVencIssqnVariavel = $this->origemManager->query(
            "confvencissqnvariavel",
            "*",
            ["q144_ano = {$this->dados->ano_competencia}"]
        )->get();

        if (!$confVencIssqnVariavel) {
            throw new \Exception(
                "Não foi encontrado configuração de vencimento [competencia: {$this->dados->ano_competencia}]."
            );
        }

        return $confVencIssqnVariavel;
    }

    /**
     * @throws \Exception
     */
    private function getDataVencimentoIssVariavel($configuracaoVencimento)
    {
        $cadVencIssqnVariavel = $this->origemManager->query(
            "cadvenc",
            "q82_venc as datavencimento",
            [
                "q82_codigo = {$configuracaoVencimento->q144_codvenc}",
                "q82_parc = {$this->dados->mes_competencia}"
            ]
        )->get();

        if (!$cadVencIssqnVariavel) {
            $mensagemErro = "Não foi encontrado cadastro de vencimento";
            $mensagemErro .= " [competencia: {$this->ano_competencia}/{$this->mes_competencia}]";
            throw new \Exception($mensagemErro);
        }

        return $cadVencIssqnVariavel->datavencimento;
    }

    private function getDataVencimentoIssRetido($confPlanInfo)
    {
        $sSqlVencimento = "
             SELECT extract(year FROM q82_venc) AS ano_vencimento,
                    extract(month FROM q82_venc) AS mes_vencimento
               FROM cadvenc
         INNER JOIN cadvencdesc
                 ON cadvencdesc.q92_codigo = cadvenc.q82_codigo
         INNER JOIN confvencissqnvariavel
                 ON q144_codvenc = cadvencdesc.q92_codigo
              WHERE q144_ano = {$this->dados->ano_competencia}
                AND q82_parc = {$this->dados->mes_competencia}";

        $cadVencIssqn = $this->origemManager->runRawQuery($sSqlVencimento)->get();

        if (!$cadVencIssqn) {
            $mensagemErro = "Não foi encontrado cadastro de vencimento";
            $mensagemErro .= " [competencia: {$this->ano_competencia}/{$this->mes_competencia}]";
            throw new \Exception($mensagemErro);
        }

        $diaVencimento = str_pad($confPlanInfo->w10_dia, 2, "0", STR_PAD_LEFT);
        $mesVencimento = str_pad($cadVencIssqn->mes_vencimento, 2, "0", STR_PAD_LEFT);

        return "{$cadVencIssqn->ano_vencimento}-{$mesVencimento}-{$diaVencimento}";
    }

    /**
     * @throws \Exception
     */
    private function getCgmInscricao()
    {
        $dadosInscricao = $this->origemManager->query(
            "issbase",
            "q02_numcgm",
            ["q02_inscr = {$this->codigoInscricao}"]
        )->get();

        if (!$dadosInscricao) {
            throw new \Exception("Número do CGM nao encontrado.");
        }

        return  $dadosInscricao->q02_numcgm;
    }

    /**
     * @throws \Exception
     */
    private function getConfPlanInfo()
    {
        $confPlanInfo = $this->origemManager->query("db_confplan")->get();

        if (!$confPlanInfo) {
            throw new \Exception("Dados confplan não disponíveis");
        }

        return $confPlanInfo;
    }

    /**
     * @throws \Exception
     */
    private function getDadosDebitoFromTipo()
    {
        switch ($this->dados->tipo_tributo) {
            case TipoTributoEnum::ISS_VARIAVEL:
                return  $this->getDadosIssVariavel();
            case TipoTributoEnum::ISS_RETIDO:
                return $this->getDadosIssRetido();
            default:
                throw new \Exception("Tipo de débito não tratado.");
        }
    }

    private function getDadosIssVariavel()
    {
        $dados = [];

        $configuracaoVencimento = $this->getConfiguracaoVencimento();
        $dados["dataVencimento"] = $this->getDataVencimentoIssVariavel($configuracaoVencimento);
        $dados["receita"] = $configuracaoVencimento->q144_receita;
        $dados["tipo"] = $configuracaoVencimento->q144_tipo;
        $dados["historico"] = $configuracaoVencimento->q144_hist;

        return (object) $dados;
    }

    private function getDadosIssRetido()
    {
        $dados = [];

        $confPlanInfo = $this->getConfPlanInfo();
        $dados["dataVencimento"] = $this->getDataVencimentoIssRetido($confPlanInfo);
        $dados["receita"] = $confPlanInfo->w10_receit;
        $dados["tipo"] = $confPlanInfo->w10_tipo;
        $dados["historico"] = $confPlanInfo->w10_hist;

        return (object) $dados;
    }

    /**
     * @throws \Exception
     */
    private function getCodigoCgm()
    {
        if ($this->codigoInscricao) {
            return $this->getCgmInscricao();
        }

        if ($this->dados->cnpj) {
            $dadosCgm = $this->origemManager->query(
                "cgm",
                "z01_numcgm",
                [["z01_cgccpf", $this->dados->cnpj, "%s"]]
            )->get();

            if ($dadosCgm) {
                return $dadosCgm->z01_numcgm;
            }
        }

        throw new \Exception("Não foi possivel identificar o número do CGM.");
    }

    /**
     * @throws \Exception
     */
    private function salvarIssvarInfiscIntegraDebitos($codigoIssVar)
    {
        if (!$codigoIssVar) {
            throw new \Exception("Código da issvar não informado.");
        }

        $dao_issvar_infisc_integra_debitos = new \cl_issvar_infisc_integra_debitos();
        $dao_issvar_infisc_integra_debitos->q196_issvar = $codigoIssVar;
        $dao_issvar_infisc_integra_debitos->q196_integra_debitos = $this->dados->sequencial;
        $dao_issvar_infisc_integra_debitos->incluir();

        if ($dao_issvar_infisc_integra_debitos->erro_status == "0") {
            throw new \Exception($dao_issvar_infisc_integra_debitos->erro_msg);
        }
    }
}
