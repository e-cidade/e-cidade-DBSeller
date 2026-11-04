<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use stdClass;

class InformacoesComplementaresEventosPeriodicosFormatter extends Formatter
{
    private $isDecimoTerceiro = false;


    public function formatar($dados)
    {
        $dadoFormatado = new stdClass();
        $dadoFormatado->inscricao_empregador = $dados->inscricao_empregador;
        $dadoFormatado->referencia = $dados->eso40_nr_insc . "-" . $dados->eso40_periodo;
        $dadoFormatado->indApuracao = $this->isDecimoTerceiro ? 2 : 1;
        $dadoFormatado->perApur = $dados->eso40_periodo;
        $this->montaGrupoIdeEmpregador($dadoFormatado, $dados);
        $this->montaGrupoInfoSubstPatr($dadoFormatado, $dados);
        $this->montaGrupoInfoSubstPatrOpPort($dadoFormatado, $dados);
        $this->montaGrupoInfoAtivConcom($dadoFormatado, $dados);
        $this->montaGrupoInfoPercTransf11096($dadoFormatado, $dados);
        return $dadoFormatado;
    }

    private function montaGrupoIdeEmpregador(&$dadoFormatado, $dados)
    {
        $dadoFormatado->ideEmpregador = new stdClass();
        $dadoFormatado->ideEmpregador->tpInsc = $dados->eso40_tp_insc;
        $dadoFormatado->ideEmpregador->nrInsc = $dados->eso40_num_insc;
    }

    private function montaGrupoInfoSubstPatr(&$dadoFormatado, $dados)
    {
        $dadoFormatado->infoSubstPatr = new stdClass();

        $dadoFormatado->infoSubstPatr->indSubstPatr = $dados->eso40_ind_subst_patr;
        $dadoFormatado->infoSubstPatr->percRedContrib = $dados->eso40_perc_red_contrib;
        if ($dadoFormatado->infoSubstPatr->indSubstPatr == null &&
            $dadoFormatado->infoSubstPatr->percRedContrib == null) {
            unset($dadoFormatado->infoSubstPatr);
        }
    }

    private function montaGrupoInfoSubstPatrOpPort(&$dadoFormatado, $dados)
    {
        $dadoFormatado->infoSubstPatrOpPort = [];
        $dadoFormatado->infoSubstPatrOpPort[] = new stdClass();
        $dadoFormatado->infoSubstPatrOpPort[0]->codLotacao = $dados->eso40_cod_lotacao;
        if ($dadoFormatado->infoSubstPatrOpPort[0]->codLotacao == null) {
            unset($dadoFormatado->infoSubstPatrOpPort);
        }
    }

    private function montaGrupoInfoAtivConcom(&$dadoFormatado, $dados)
    {
        $dadoFormatado->infoAtivConcom = new stdClass();
        $dadoFormatado->infoAtivConcom->fatorMes = $dados->eso40_fator_mes;
        $dadoFormatado->infoAtivConcom->fator13 = $dados->eso40_fator_13;
        if ($dadoFormatado->infoAtivConcom->fatorMes == null
            && $dadoFormatado->infoAtivConcom->fator13 == null) {
            unset($dadoFormatado->infoAtivConcom);
        }
    }

    private function montaGrupoInfoPercTransf11096(&$dadoFormatado, $dados)
    {
        $dadoFormatado->infoPercTransf11096 = new stdClass();
        $dadoFormatado->infoPercTransf11096->percTransf = $dados->eso40_perc_transf;

        if ($dadoFormatado->infoPercTransf11096->percTransf == null) {
            unset($dadoFormatado->infoPercTransf11096);
        }
    }

    public function setDecimoTerceiro()
    {
        $this->isDecimoTerceiro = true;
    }
}
