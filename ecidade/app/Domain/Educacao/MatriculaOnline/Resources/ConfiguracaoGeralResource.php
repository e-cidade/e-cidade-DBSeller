<?php

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Educacao\MatriculaOnline\Models\ConfiguracaoGeral;

class ConfiguracaoGeralResource
{
    public static function toResponse(ConfiguracaoGeral $campo)
    {
        return (object) [
            'validadeAlocacao' => $campo->mo25_validade_alocacao,
            'exibeListaEspera' => $campo->mo25_habilitarconsultalistaespera,
            'permiteEdicao' => $campo->mo25_habilitaredicao,
            'permiteReemissao' => $campo->mo25_habilitarreemissao,
            'habilitarValidacaoCpfCandidato' => $campo->mo25_habilitarvalidacaocpfcandidato,
            'apenasEscolasComVagas' => $campo->mo25_apenasescolacomvagas,
            'validaAlunoMatriculado' => $campo->mo25_validaalunomatriculado,
            'exibeClassificacaoListaEspera' => $campo->mo25_exibirclassificacaolistaespera,
            'liConcordoLegislacao' => $campo->mo25_liconcordolegislacao,
            'validaLimiteInscricoes' => $campo->mo25_validalimiteinscricoes,
            'automatizaSItuacaoAlocado' => $campo->mo25_automatizarsituacaoalocado,
            'tituloComprovante' => $campo->mo25_titulocomprovante,
            'processarDesignacaoEmail' => $campo->mo25_notificarprocessardesignacaoemail,
            'processarDesignacaoSms' => $campo->mo25_notificarprocessardesignacaosms,
            'processarDesignacaoWhatsapp' => $campo->mo25_notificarprocessardesignacaowhatsapp,
            'reprocessardesignacaoautomaticaemail' => $campo->mo25_notificarreprocessardesignacaoautomaticaemail,
            'reprocessardesignacaoautomaticasms' => $campo->mo25_notificarreprocessardesignacaoautomaticasms,
            'reprocessardesignacaoautomaticawhatsapp' => $campo->mo25_notificarreprocessardesignacaoautomaticawhatsapp,
            'manutencaolistaesperaemail' => $campo->mo25_notificarmanutencaolistaesperaemail,
            'manutencaolistaesperasms' => $campo->mo25_notificarmanutencaolistaesperasms,
            'manutencaolistaesperawhatsapp' => $campo->mo25_notificarmanutencaolistaesperawhatsapp,
            'telefonecentralmatriculas' => $campo->mo25_telefonecentralmatriculas
        ];
    }
}
