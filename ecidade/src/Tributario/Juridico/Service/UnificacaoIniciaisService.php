<?php

namespace ECidade\Tributario\Juridico\Service;

use ECidade\Tributario\Arrecadacao\CGF\Repository\Certidao;
use ECidade\Tributario\Juridico\Inicial\InicialCert;
use ECidade\Tributario\Juridico\Inicial\InicialNumpre;
use ECidade\Tributario\Juridico\Inicial\Model\Iniciaisunificadas;
use ECidade\Tributario\Juridico\Inicial\Repository\IniciaisunificadasRepository;
use ECidade\Tributario\Juridico\Inicial\Repository\InicialNumpreRepository;
use ECidade\Tributario\Juridico\Repository\UnificacaoIniciaisRepository;
use ECidade\V3\Extension\Registry;

final class UnificacaoIniciaisService
{
    /**
     * Agrupas as iniciais com suas respectivas CDA's
     * @param array $aIniciais
     * @return array
     */
    public function agrupaIniciais(array $aIniciais)
    {
        $aIniciaisAux = [];

        foreach ($aIniciais as $aInicial) {
            $aIniciaisAux[$aInicial->codigo_inicial]["codigo_inicial"] = $aInicial->codigo_inicial;
            if (!in_array($aInicial->origem, $aIniciaisAux[$aInicial->codigo_inicial]["origem"])) {
                $aIniciaisAux[$aInicial->codigo_inicial]["origem"][] = $aInicial->origem;
            }
            $aIniciaisAux[$aInicial->codigo_inicial]["codigo_processo"] = $aInicial->codigo_processo;
            $aIniciaisAux[$aInicial->codigo_inicial]["tipo_debito"] = $aInicial->tipo_debito;
            $aIniciaisAux[$aInicial->codigo_inicial]["data_inicial"] = date(
                'd/m/Y',
                strtotime($aInicial->data_inicial)
            );

            $aIniciaisAux[$aInicial->codigo_inicial]["cdas"][$aInicial->codigo_certidao]["codigo_certidao"]
                = $aInicial->codigo_certidao;

            $aIniciaisAux[$aInicial->codigo_inicial]["cdas"][$aInicial->codigo_certidao]["exercicio_divida"]
                = $aInicial->exercicio_divida;

            if (!in_array(
                $aInicial->nome_procedencia,
                $aIniciaisAux[$aInicial->codigo_inicial]["cdas"][$aInicial->codigo_certidao]["nome_procedencia"]
            )
            ) {
                $aIniciaisAux[$aInicial->codigo_inicial]["cdas"][$aInicial->codigo_certidao]["nome_procedencia"][]
                    = $aInicial->nome_procedencia;
            }

            $aIniciaisAux[$aInicial->codigo_inicial]["cdas"][$aInicial->codigo_certidao]["valor_total"]
                += floatval($aInicial->valor_total);
        }

        return $aIniciaisAux;
    }

    /**
     * @param $aIniciais
     * @return array
     */
    public function agrupaOrigens($aIniciais)
    {
        $aOrigens = [];

        foreach ($aIniciais as $oInicial) {
            if (!in_array($oInicial->origem, $aOrigens[$oInicial->codigo_inicial])) {
                $aOrigens[$oInicial->codigo_inicial][] = $oInicial->origem;
            }
        }

        return $aOrigens;
    }

    /**
     * @param UnificacaoIniciaisRepository $unificacaoIniciaisRepository
     * @param $iInicialPrimaria
     * @param $aIniciaisSecundarias
     * @param $aCdas
     * @param $bSomenteOrigemFiltro
     * @throws \BusinessException
     * @throws \DBException
     */
    public function unificaIniciais(
        UnificacaoIniciaisRepository $unificacaoIniciaisRepository,
        $iInicialPrimaria,
        $aIniciaisSecundarias,
        $aCdas,
        $bSomenteOrigemFiltro
    ) {
        $inicial = new \inicial();
        $iniciaisunificadasRepository = new IniciaisunificadasRepository();
        $iniciaisunificadas = new Iniciaisunificadas();

        $iniciaisunificadas->setDataunificacao(date("Y-m-d H:i:s"));
        $iniciaisunificadas->setUsuario(db_getsession("DB_id_usuario"));

        if ($bSomenteOrigemFiltro) {
            $aIniciais = [];

            foreach ($aCdas as $oCda) {
                $iInicial = $this->modificarInicialCda($unificacaoIniciaisRepository, $iInicialPrimaria, $oCda);

                if (!isset($aIniciais[$iInicial])) {
                    $aIniciais[$iInicial] = (object) [
                        "codigoInicial" => $iInicial
                    ];
                }

                $aIniciais[$iInicial]->aCdas[] = $oCda;

                $iniciaisunificadas->setInicialprimaria($iInicialPrimaria);
                $iniciaisunificadas->setInicialsecundaria($iInicial);
                $iniciaisunificadas->setCertidao($oCda);

                $iniciaisunificadasRepository->persist($iniciaisunificadas);
            }

            foreach ($aIniciais as $oInicial) {
                $inicial->setCodigoInicial($oInicial->codigoInicial);

                $sCdas = implode(", ", $oInicial->aCdas);

                $inicial->adicionarMovimentacao(
                    10,
                    "Movido CDA(s) {$sCdas} para a inicial {$iInicialPrimaria}"
                );
            }
        } else {
            $certidao = new Certidao();

            foreach ($aIniciaisSecundarias as $iInicial) {
                $aCdas = $certidao->getCdaByInicial($iInicial);

                foreach ($aCdas as $oCda) {
                    $iInicial = $this->modificarInicialCda(
                        $unificacaoIniciaisRepository,
                        $iInicialPrimaria,
                        $oCda->certidao
                    );

                    $iniciaisunificadas->setInicialprimaria($iInicialPrimaria);
                    $iniciaisunificadas->setInicialsecundaria($iInicial);
                    $iniciaisunificadas->setCertidao($oCda->certidao);

                    $iniciaisunificadasRepository->persist($iniciaisunificadas);
                }

                $inicial->anulaInicial(
                    $iInicial,
                    9,
                    "Inicial anulada pela rotina de unificação e vinculada à inicial nº {$iInicialPrimaria}"
                );
            }
        }
    }

    /**
     * @param UnificacaoIniciaisRepository $unificacaoIniciaisRepository
     * @param $iInicialPrimaria
     * @param $iCda
     * @return mixed
     * @throws \Exception
     */
    public function modificarInicialCda(
        UnificacaoIniciaisRepository $unificacaoIniciaisRepository,
        $iInicialPrimaria,
        $iCda
    ) {
        $aCda = $unificacaoIniciaisRepository->getByCda($iCda);

        $oCda = array_shift($aCda);

        $inicialCertRepository   = Registry::get('app.container')
                                             ->get('tributario.container')
                                             ->get('InicialCertRepository');

        $inicialCert = new InicialCert();
        $inicialCert->setInicial($iInicialPrimaria);
        $inicialCert->setCertidao($oCda->codigo_certidao);

        $inicialCertRepository->update($oCda->codigo_inicial, $oCda->codigo_certidao, $inicialCert);

        $inicialNumpre = new InicialNumpre();
        $inicialNumpreRepository = new InicialNumpreRepository();

//        foreach ($aCda as $oCda) {
            $inicialNumpre->setInicial($iInicialPrimaria);

            $inicialNumpreRepository->where(['v59_inicial', '=', $oCda->codigo_inicial])->save($inicialNumpre);
//        }

        return $oCda->codigo_inicial;
    }
}
