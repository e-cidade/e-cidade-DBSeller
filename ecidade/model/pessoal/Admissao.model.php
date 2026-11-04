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

class Admissao
{
    /**
     * @var boolean
     */
    private $isTemporario;

    /**
     * @var string
     */
    private $justificativaLegal;

    /**
     * @var string
     */
    private $tipoAdmissaoMatricula;


    public function __construct($matricula = null)
    {
        if ($matricula) {
            $daoAdmissao = new cl_admissao;
            $campos =["h07_regist",
                      "h07_tipadm",
                      "h07_dato",
                      "h07_cant",
                      "h07_dhist",
                      "h07_ddem",
                      "h07_icon",
                      "h07_ires",
                      "h07_class",
                      "h07_refe",
                      "h07_area",
                      "h07_nrato",
                      "h07_nrfich",
                      "h07_impofi",
                      "h07_dpubl",
                      "h07_fundam",
                      "h07_defet",
                      "h07_tempor",
                      "h07_termin",
                      "h07_justif"
            ];
            $campos = implode(',', $campos);
            $sqlAdmissao = $daoAdmissao->sql_query("", $campos, "", "h07_regist = {$matricula} ");
            $resultadoAdmissao = \db_query($sqlAdmissao);

            if (!$resultadoAdmissao) {
                throw new \DBException("Houve um erro ao buscar dados admissional da matrícula {$matricula}.");
            }

            if (pg_num_rows($resultadoAdmissao) > 0) {
                $admissao = \db_utils::fieldsMemory($resultadoAdmissao, 0);
                $this->setIsTemporario($admissao->h07_tempor);
                $this->setJustificativaLegal($admissao->h07_justif);
                $this->setTipoAdmissaoMatricula($admissao->h07_tipadm);
            }


        }
    }

    /**
     * Get the value of isTemporario
     *
     * @return  integer
     */
    public function getIsTemporario()
    {
        return $this->isTemporario;
    }

    /**
     * Set the value of isTemporario
     *
     * @param  integer  $mesDataBase
     *
     */

    public function setIsTemporario($isTemporario)
    {
        if ($isTemporario === 't') {
            $isTemporario = true;
        }
        if ($isTemporario === 'f') {
            $isTemporario = false;
        }
        $this->isTemporario = $isTemporario;
    }

    /**
     * Get the value of justificativaLegal
     *
     * @return  string
     */
    public function getJustificativaLegal()
    {
        return $this->justificativaLegal;
    }

    /**
     * Set the value of justificativaLegal
     *
     * @param  string  $mesDataBase
     *
     */

    public function setJustificativaLegal($justificativaLegal)
    {
        $this->justificativaLegal = $justificativaLegal;
    }

        /**
     * Get the value of tipoAdmissaoMatricula
     *
     * @return  string
     */
    public function getTipoAdmissaoMatricula()
    {
        return $this->tipoAdmissaoMatricula;
    }

    /**
     * Set the value of tipoAdmissaoMatricula
     *
     * @param  string  $mesDataBase
     *
     */

    public function setTipoAdmissaoMatricula($tipoAdmissaoMatricula)
    {
        $this->tipoadmissaomatricula = $tipoAdmissaoMatricula;
    }
}
