<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\Formatter;
use ServidorRepository;

/**
 * Formata os dados de Comunicação de Acidente de Trabalho
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 * @author Fabio Egidio <fabio.egidio@dbseller.com.br>
 */
class CatFormatter extends Formatter
{
    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($dados)
    {
        $dadosFormatado = parent::formatar($dados);
        return $this->posProcessamento($dadosFormatado);
    }

    /**
     * Realiza uma consistencia nos dados enviados
     *
     * @param array  $dadosFormatado
     * @return array
     */
    private function posProcessamento($dadosFormatado)
    {
        foreach ($dadosFormatado as &$dadoCat) {
            if (isset($dadoCat->ideVinculo)) {
                if (empty($dadoCat->ideVinculo->matricula)) {
                    unset($dadoCat->ideVinculo->matricula);
                } else {
                    $servidor = ServidorRepository::getInstanciaByCodigo($dadoCat->ideVinculo->matricula);
                    if (!empty($servidor->getMatriculaAnterior())) {
                        $dadoCat->ideVinculo->matricula = $servidor->getMatriculaAnterior();
                    }
                }

                if (empty($dadoCat->ideVinculo->codCateg)) {
                    unset($dadoCat->ideVinculo->codCateg);
                }
            }

            if (isset($dadoCat->cat)) {
                if (empty($dadoCat->cat->hrAcid)) {
                    unset($dadoCat->cat->hrAcid);
                } else {
                    $dadoCat->cat->hrAcid = (string) $dadoCat->cat->hrAcid;
                }

                if (empty($dadoCat->cat->hrsTrabAntesAcid)) {
                    unset($dadoCat->cat->hrsTrabAntesAcid);
                } else {
                    $dadoCat->cat->hrsTrabAntesAcid = (string) $dadoCat->cat->hrsTrabAntesAcid;
                }

                if (empty($dadoCat->cat->dtObito)) {
                    unset($dadoCat->cat->dtObito);
                }

                if (empty($dadoCat->cat->obsCAT)) {
                    unset($dadoCat->cat->obsCAT);
                }
            }

            if (isset($dadoCat->cat->localAcidente)) {
                if (empty($dadoCat->cat->localAcidente->dscLocal)) {
                    unset($dadoCat->cat->localAcidente->dscLocal);
                }

                if (empty($dadoCat->cat->localAcidente->tpLograd)) {
                    unset($dadoCat->cat->localAcidente->tpLograd);
                }

                if (empty($dadoCat->cat->localAcidente->complemento)) {
                    unset($dadoCat->cat->localAcidente->complemento);
                }

                if (empty($dadoCat->cat->localAcidente->bairro)) {
                    unset($dadoCat->cat->localAcidente->bairro);
                }

                if (empty($dadoCat->cat->localAcidente->cep)) {
                    unset($dadoCat->cat->localAcidente->cep);
                }

                if (empty($dadoCat->cat->localAcidente->codMunic)) {
                    unset($dadoCat->cat->localAcidente->codMunic);
                } else {
                    $dadoCat->cat->localAcidente->codMunic = (string) $dadoCat->cat->localAcidente->codMunic;
                }

                if (empty($dadoCat->cat->localAcidente->uf)) {
                    unset($dadoCat->cat->localAcidente->uf);
                }

                if (empty($dadoCat->cat->localAcidente->pais)) {
                    unset($dadoCat->cat->localAcidente->pais);
                }

                if (empty($dadoCat->cat->localAcidente->codPostal)) {
                    unset($dadoCat->cat->localAcidente->codPostal);
                }
            }
            if (isset($dadoCat->cat->atestado)) {
                if (empty($dadoCat->cat->atestado->dscCompLesao)) {
                    unset($dadoCat->cat->atestado->dscCompLesao);
                }
                if (empty($dadoCat->cat->atestado->diagProvavel)) {
                    unset($dadoCat->cat->atestado->diagProvavel);
                }
                if (empty($dadoCat->cat->atestado->observacao)) {
                    unset($dadoCat->cat->atestado->observacao);
                }
            }

            if (isset($dadoCat->cat->parteAtingida)) {
                if (!empty($dadoCat->cat->parteAtingida->codParteAting)) {
                    $dadoCat->cat->parteAtingida->codParteAting = (string) $dadoCat->cat->parteAtingida->codParteAting;
                }
            }
            if (isset($dadoCat->cat->agenteCausador)) {
                if (!empty($dadoCat->cat->agenteCausador->codAgntCausador)) {
                    $dadoCat->cat->agenteCausador->codAgntCausador =
                        (string) $dadoCat->cat->agenteCausador->codAgntCausador;
                }
            }

            if (isset($dadoCat->cat->atestado)) {
                if (!empty($dadoCat->cat->atestado->dscLesao)) {
                    $dadoCat->cat->atestado->dscLesao = (string) $dadoCat->cat->atestado->dscLesao;
                }
            }
            if (isset($dadoCat->cat->catOrigem)) {
                if (empty($dadoCat->cat->catOrigem->nrRecCatOrig)) {
                    unset($dadoCat->cat->catOrigem);
                }
            }
        }

        return $dadosFormatado;
    }
}
