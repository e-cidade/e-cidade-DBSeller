<?php

/*
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

class RegraLicitaconModalidadeTipoLicitacaoTipoObjeto extends RegraLicitacon
{

    protected $sMensagem = "A combinação entre Modalidade, Critério de Julgamento e Tipo de Objeto não é válida.\n\nVerifique as combinações possíveis entre as modalidades, os critérios de julgamentos e os tipos de objeto disponíveis no LicitaCon no Apêndice C.";

    /**
     * @var array
     */
    protected $aRegrasApendiceC = array(
        'CPP' => array(
            'NSA' => array('COM')
        ),
        'CPC' => array(
            'NSA' => array('OUS', 'SAU')
        ),
        'CHP' => array(
            'MTC' => array('CSE', 'COM', 'OUS', 'CON', 'INF', 'SAU'),
            'MPR' => array('CSE', 'COM', 'LOC', 'OUS', 'SAU', 'INF', 'CON'),
            'MTX' => array('COM', 'CSE', 'OUS', 'INF', 'SAU'),
            'TPR' => array('CSE', 'COM', 'OUS', 'CON', 'INF', 'SAU'),
            'MLO' => array('CON')
        ),
        'CCE' => array(
            'MDE' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'SAU'),
            'MOO' => array('COL', 'PER', 'CON'),
            'MOQ' => array('COL', 'PER', 'CON'),
            'MOT' => array('COL', 'PER', 'CON'),
            'MRE' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'SAU'),
            'MCA' => array('OUS', 'SAU'),
            'MPP' => array('COL', 'PER', 'CON'),
            'MTC' => array('CSE', 'INF', 'OSE', 'OUS', 'SAU', 'CON'),
            'MPR' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'PPP', 'SAU', 'CON'),
            'MTX' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'SAU'),
            'MVT' => array('COL', 'PPP', 'PER', 'CON'),
            'MTO' => array('COL', 'PER', 'CON'),
            'MTT' => array('COL', 'PPP', 'PER', 'CON'),
            'TPR' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'PPP', 'SAU', 'CON'),
            'MLO' => array('CON')
        ),
        'CCP' => array(
            'MDE' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'SAU'),
            'MOO' => array('COL', 'PER', 'CON'),
            'MOQ' => array('COL', 'PER', 'CON'),
            'MOT' => array('COL', 'PER', 'CON'),
            'MRE' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'SAU'),
            'MCA' => array('OUS', 'SAU'),
            'MPP' => array('COL', 'PER', 'CON'),
            'MTC' => array('CSE', 'INF', 'OSE', 'OUS', 'SAU', 'CON'),
            'MPR' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'PPP', 'SAU', 'CON'),
            'MTX' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'SAU'),
            'MVT' => array('COL', 'PPP', 'PER', 'CON'),
            'MTO' => array('COL', 'PER', 'CON'),
            'MTT' => array('COL', 'PPP', 'PER', 'CON'),
            'TPR' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'PPP', 'SAU', 'CON'),
            'MLO' => array('CON')
        ),
        'CNC' => array(
            'MDE' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'INF', 'SAU'),
            'MLO' => array('ALB', 'CON', 'PER', 'OUS', 'PRI', 'SAU'),
            'MOQ' => array('CON', 'PER', 'COL'),
            'MOT' => array('CON', 'PER', 'COL'),
            'MOO' => array('CON', 'PER', 'COL'),
            'MPP' => array('CON', 'PER', 'COL'),
            'MTC' => array('ALB', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'MPR' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'CON', 'INF', 'PPP', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTO' => array('CON', 'PER', 'COL'),
            'MTT' => array('CON', 'PER', 'COL', 'PPP'),
            'MVT' => array('CON', 'PER', 'COL', 'PPP'),
            'TPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU', 'CON', 'PPP')
        ),
        'CNS' => array(
            'MCA' => array('OUS', 'SAU'),
            'MTC' => array('OSE', 'OUS', 'SAU'),
            'NSA' => array('OUS', 'OSE', 'SAU')
        ),
        'CNV' => array(
            'MLO' => array('PER'),
            'MOQ' => array('PER'),
            'MOT' => array('PER'),
            'MOO' => array('PER'),
            'MPP' => array('PER'),
            'MTC' => array('CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'MPR' => array('CSE', 'CON', 'COM', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTO' => array('PER'),
            'MTT' => array('PER'),
            'MVT' => array('PER'),
            'TPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'ESE' => array(
            'MDE' => array('OUS', 'OSE', 'CSE', 'COM', 'INF', 'SAU'),
            'MOP' => array('ALB', 'CON', 'OUS', 'PER'),
            'MRE' => array('OSE'),
            'MCA' => array('OUS', 'SAU'),
            'MDB' => array('ALB'),
            'MTC' => array('OUS', 'OSE', 'CSE', 'INF', 'SAU'),
            'MPR' => array('OUS', 'OSE', 'CSE', 'COM', 'INF', 'LOC', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'TPR' => array('OUS', 'OSE', 'CSE', 'COM', 'INF', 'SAU')
        ),
        'EST' => array(
            'MDE' => array('OUS', 'OSE', 'CSE', 'COM', 'INF', 'SAU'),
            'MOP' => array('ALB', 'CON', 'OUS', 'PER'),
            'MRE' => array('OSE'),
            'MCA' => array('OUS', 'SAU'),
            'MDB' => array('ALB'),
            'MTC' => array('OUS', 'OSE', 'CSE', 'INF', 'SAU'),
            'MPR' => array('OUS', 'OSE', 'CSE', 'COM', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'TPR' => array('OUS', 'OSE', 'CSE', 'COM', 'INF', 'SAU')
        ),
        'LEE' => array(
            'MLO' => array('ALB')
        ),
        'LEI' => array(
            'MLO' => array('ALB', 'PRI')
        ),
        'MAI' => array(
            'NSA' => array('OSE', 'OUS', 'SAU')
        ),
        'PRE' => array(
            'MDE' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'INF', 'SAU'),
            'MLO' => array('ALB', 'CON', 'OUS', 'PER', 'SAU'),
            'MOO' => array('CON', 'PER'),
            'MPR' => array('CSE', 'COM', 'CON', 'LOC', 'OSE', 'OUS', 'PER', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'PCE' => array(
            'MDE' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'SAU'),
            'MLO' => array('CON', 'OUS', 'PER', 'SAU'),
            'MPR' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'SAU', 'PER'),
            'MTX' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'SAU')
        ),
        'PCP' => array(
            'MDE' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'SAU'),
            'MLO' => array('CON', 'OUS', 'PER', 'SAU'),
            'MPR' => array('COM', 'CSE', 'INF', 'LOC', 'OSE', 'OUS', 'SAU', 'PER'),
            'MTX' => array('COM', 'CSE', 'INF', 'OSE', 'OUS', 'SAU')
        ),
        'PRP' => array(
            'MDE' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'INF', 'SAU'),
            'MLO' => array('ALB', 'CON', 'OUS', 'PER', 'SAU'),
            'MOO' => array('CON', 'PER'),
            'MPR' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'PER', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'PDE' => array(
            'MDE' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'INF', 'SAU'),
            'MLO' => array('ALB', 'CON', 'OUS', 'PER', 'SAU'),
            'MOO' => array('CON', 'PER'),
            'MPR' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'PER', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'PRD' => array(
            'NSA' => array('ALB', 'CSE', 'COM', 'CON', 'LOC', 'OSE', 'OUS', 'PER', 'INF', 'SAU')
        ),
        'PRI' => array(
            'NSA' => array('ALB', 'CSE', 'COM', 'OSE', 'OUS', 'LOC', 'CON', 'INF', 'PER', 'SAU')
        ),
        'RDE' => array(
            'MDE' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU'),
            'MOP' => array('ALB'),
            'MCA' => array('OUS', 'SAU'),
            'MTC' => array('OSE', 'OUS', 'SAU'),
            'MPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'TPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'RDC' => array(
            'MDE' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU'),
            'MOP' => array('ALB'),
            'MCA' => array('OUS', 'SAU'),
            'MTC' => array('OSE', 'OUS', 'SAU'),
            'MPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'TPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'RPO' => array(
            'NSA' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'RIN' => array(
            'MLO' => array('ALB'),
            'MTC' => array('CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'MPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'TPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU')
        ),
        'TMP' => array(
            'MLO' => array('PER'),
            'MOQ' => array('PER'),
            'MOT' => array('PER'),
            'MOO' => array('PER'),
            'MPP' => array('PER'),
            'MTC' => array('CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'MPR' => array('CSE', 'COM', 'LOC', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTX' => array('COM', 'CSE', 'OSE', 'OUS', 'INF', 'SAU'),
            'MTO' => array('PER'),
            'MTT' => array('PER'),
            'MVT' => array('PER'),
            'TPR' => array('CSE', 'COM', 'OSE', 'OUS', 'INF', 'SAU')
        )
    );

    protected function getRegras()
    {
        return $this->aRegrasApendiceC;
    }

    public function regra()
    {
        $sModalidade = $this->oLicitacao->getModalidade()->getSiglaTipoCompraTribunal();
        $sTipoLicitacao = null;
        $sTipoObjeto = null;
        $aRegras = $this->getRegras();

        if (isset($this->aAtributosDinamicos[LicitacaoAtributosDinamicos::NOME_TIPO_LICITACAO])) {
            $sTipoLicitacao = $this->aAtributosDinamicos[LicitacaoAtributosDinamicos::NOME_TIPO_LICITACAO];
        }

        if (isset($this->aAtributosDinamicos[LicitacaoAtributosDinamicos::NOME_TIPO_OBJETO])) {
            $sTipoObjeto = $this->aAtributosDinamicos[LicitacaoAtributosDinamicos::NOME_TIPO_OBJETO];
        }

        if (empty($sTipoObjeto)) {
            $this->sMensagem = "O campo Tipo de Objeto é de preenchimento obrigatório.";
            return false;
        }

        if (empty($sTipoLicitacao)) {
            $this->sMensagem = "O campo Critério de Julgamento é de preenchimento obrigatório.";
            return false;
        }

        if (!isset($aRegras[$sModalidade]) || !isset($aRegras[$sModalidade][$sTipoLicitacao])) {
            return false;
        }

        if (!in_array($sTipoObjeto, $aRegras[$sModalidade][$sTipoLicitacao])) {
            return false;
        }

        return true;
    }
}
