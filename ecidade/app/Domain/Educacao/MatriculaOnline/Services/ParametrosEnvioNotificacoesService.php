<?php

namespace App\Domain\Educacao\MatriculaOnline\Services;

class ParametrosEnvioNotificacoesService
{
    public function getParametrosTipoNotificacao($model)
    {
        $retorno = [];
        if ($model->getNotificarProcessarDesignacaoEmail() == 't') {
            $retorno[] = 'processardesignacaoemail';
        }
        if ($model->getNotificarProcessarDesignacaoSms() == 't') {
            $retorno[] = 'processardesignacaosms';
        }
        if ($model->getNotificarProcessarDesignacaoWhatsapp() == 't') {
            $retorno[] = 'processardesignacaowhatsapp';
        }
        if ($model->getNotificarReprocessarDesignacaoAutomaticaEmail() == 't') {
            $retorno[] = 'reprocessardesignacaoautomaticaemail';
        }
        if ($model->getNotificarReprocessarDesignacaoAutomaticaSms() == 't') {
            $retorno[] = 'reprocessardesignacaoautomaticasms';
        }
        if ($model->getNotificarReprocessarDesignacaoAutomaticaWhatsapp() == 't') {
            $retorno[] = 'reprocessardesignacaoautomaticawhatsapp';
        }
        if ($model->getNotificarManutencaoListaesperaEmail() == 't') {
            $retorno[] = 'manutencaolistaesperaemail';
        }
        if ($model->getNotificarManutencaoListaesperaSms() == 't') {
            $retorno[] = 'manutencaolistaesperasms';
        }
        if ($model->getNotificarManutencaoListaesperaWhatsapp() == 't') {
            $retorno[] = 'manutencaolistaesperawhatsapp';
        }
        return $retorno;
    }

    public function ajustarParametrosTipoNotificacao($model, $tiposNotificacao)
    {
        $model->setNotificarProcessarDesignacaoEmail(false);
        $model->setNotificarprocessardesignacaoSms(false);
        $model->setNotificarProcessarDesignacaoWhatsapp(false);
        $model->setNotificarReprocessarDesignacaoAutomaticaEmail(false);
        $model->setNotificarReprocessarDesignacaoAutomaticaSms(false);
        $model->setNotificarReprocessarDesignacaoAutomaticaWhatsapp(false);
        $model->setNotificarManutencaoListaesperaEmail(false);
        $model->setNotificarManutencaoListaesperaSms(false);
        $model->setNotificarManutencaoListaesperaWhatsapp(false);
        foreach ($tiposNotificacao as $tipoNotificacao) {
            if ($tipoNotificacao == "processardesignacaoemail") {
                $model->setNotificarProcessarDesignacaoEmail(true);
            }
            if ($tipoNotificacao == "processardesignacaosms") {
                $model->setNotificarprocessardesignacaoSms(true);
            }
            if ($tipoNotificacao == "processardesignacaowhatsapp") {
                $model->setNotificarProcessarDesignacaoWhatsapp(true);
            }
            if ($tipoNotificacao == "reprocessardesignacaoautomaticaemail") {
                $model->setNotificarReprocessarDesignacaoAutomaticaEmail(true);
            }
            if ($tipoNotificacao == "reprocessardesignacaoautomaticasms") {
                $model->setNotificarReprocessarDesignacaoAutomaticaSms(true);
            }
            if ($tipoNotificacao == "reprocessardesignacaoautomaticawhatsapp") {
                $model->setNotificarReprocessarDesignacaoAutomaticaWhatsapp(true);
            }
            if ($tipoNotificacao == "manutencaolistaesperaemail") {
                $model->setNotificarManutencaoListaesperaEmail(true);
            }
            if ($tipoNotificacao == "manutencaolistaesperasms") {
                $model->setNotificarManutencaoListaesperaSms(true);
            }
            if ($tipoNotificacao == "manutencaolistaesperawhatsapp") {
                $model->setNotificarManutencaoListaesperaWhatsapp(true);
            }
        }
        return $model;
    }
}
