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


/**
 * @author   Lucas Jarrier de Aquino Cavalcanti lucas.cavalcanti@dbseller.com.br
 * @package  Pessoal
 */
class RubricaEsocial
{

    private $inicioValidade;
    private $fimValidade;
    private $codIncCP;
    private $codIncIRRF;
    private $codIncFGTS;
    private $codIncCPRP;
    private $codIncPisPasep;
    private $tetoRemuneracao;
    private $naturezaRubrica;
    private $subgrupotce;
    private $tipoProcesso;
    private $numeroProcessoCP;
    private $extensaoDecisao;
    private $codigoSuspensaoProcessoCP;
    private $numeroProcessoIRRF;
    private $codigoSuspensaoIRRF;
    private $numeroProcessoFGTS;
    private $numeroProcessoPisPasep;
    private $codigoSuspensaoPisPasep;

    /**
     * RubricaEsocial constructor.
     * @param int $sequencial
     * @param string $codigoRubrica
     * @throws DBException
     */
    public function __construct($codigoRubrica = null, $sequencial = null)
    {
        if (!empty($codigoRubrica) || !empty($sequencial)) {
            $instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"))->getCodigo();
            $where = "";

            if (!empty($sequencial)) {
                $where .= "where eso26_sequencial = {$sequencial}";
            } elseif (!empty($codigoRubrica)) {
                $where .= "where
                    eso26_rubrica = '{$codigoRubrica}' and {$instituicao} = eso26_instituicao";
            }

            $camposTabela = array('eso26_rubrica',
                            'eso26_datainicial',
                            'eso26_datafinal',
                            'eso26_codinccp',
                            'eso26_codincirrf',
                            'eso26_codincfgts',
                            'eso26_codinccprp',
                            'eso26_codincpispasep',
                            'eso26_natureza',
                            'eso26_subgrupotce',
                            'eso26_tetoremun',
                            'eso26_tpproc',
                            'eso26_nrprocprocessocp',
                            'eso26_extdecisao',
                            'eso26_codsuspprocessocp',
                            'eso26_nrprocirrf',
                            'eso26_codsuspirrf',
                            'eso26_nrprocfgts',
                            'eso26_nrprocpispasep',
                            'eso26_codsusppispasep');

            $colunasRs = implode(", ", $camposTabela);
            $sql = "select {$colunasRs} from esocial.esocialrubricas {$where}";
            $result = db_query($sql);

            if (!$result) {
                throw new DBException("Erro ao buscar rubrica {$codigoRubrica}.");
            }

            if (pg_num_rows($result) > 0) {
                $oRhRubricas = db_utils::fieldsMemory($result, 0);
                $this->setCodIncCP($oRhRubricas->eso26_codinccp);
                $this->setCodIncIRRF($oRhRubricas->eso26_codincirrf);
                $this->setCodIncFGTS($oRhRubricas->eso26_codincfgts);
                $this->setCodIncCPRP($oRhRubricas->eso26_codinccprp);
                $this->setCodIncPisPasep($oRhRubricas->eso26_codincpispasep);
                $this->setTetoRemuneracao($oRhRubricas->eso26_tetoremun);
                $this->setInicioValidade($oRhRubricas->eso26_datainicial);
                $this->setFimValidade($oRhRubricas->eso26_datafinal);
                $this->setNaturezaRubrica($oRhRubricas->eso26_natureza);
                $this->setSubgrupotce($oRhRubricas->eso26_subgrupotce);
                $this->setTipoProcesso($oRhRubricas->eso26_tpproc);
                $this->setNumeroProcessoCP($oRhRubricas->eso26_nrprocprocessocp);
                $this->setExtensaoDecisao($oRhRubricas->eso26_extdecisao);
                $this->setCodigoSuspensaoProcessoCP($oRhRubricas->eso26_codsuspprocessocp);
                $this->setNumeroProcessoIRRF($oRhRubricas->eso26_nrprocirrf);
                $this->setCodigoSuspensaoIRRF($oRhRubricas->eso26_codsuspirrf);
                $this->setNumeroProcessoFGTS($oRhRubricas->eso26_nrprocfgts);
                $this->setNumeroProcessoPisPasep($oRhRubricas->eso26_nrprocpispasep);
                $this->setCodigoSuspensaoPisPasep($oRhRubricas->eso26_codsusppispasep);
            }
        }
    }

    public function getInicioValidade()
    {
        return $this->inicioValidade;
    }

    public function setInicioValidade($inicioValidade)
    {
        $this->inicioValidade = $inicioValidade;
    }

    public function getFimValidade()
    {
        return $this->fimValidade;
    }

    public function setFimValidade($fimValidade)
    {
        $this->fimValidade = $fimValidade;
    }

    public function getCodIncCP()
    {
        return $this->codIncCP;
    }

    public function setCodIncCP($codIncCP)
    {
        $this->codIncCP = $codIncCP;
    }

    public function getCodIncIRRF()
    {
        return $this->codIncIRRF;
    }

    public function setCodIncIRRF($codIncIRRF)
    {
        $this->codIncIRRF = $codIncIRRF;
    }

    public function getCodIncFGTS()
    {
        return $this->codIncFGTS;
    }

    public function setCodIncFGTS($codIncFGTS)
    {
        $this->codIncFGTS = $codIncFGTS;
    }

    public function getCodIncCPRP()
    {
        return $this->codIncCPRP;
    }

    public function setCodIncCPRP($codIncCPRP)
    {
        $this->codIncCPRP = $codIncCPRP;
    }

    public function getCodIncPisPasep()
    {
        return $this->codIncPisPasep;
    }

    public function setCodIncPisPasep($codIncPisPasep)
    {
        return $this->codIncPisPasep = $codIncPisPasep;
    }

    public function getTetoRemuneracao()
    {
        return $this->tetoRemuneracao;
    }

    public function setTetoRemuneracao($tetoRemuneracao)
    {
        $this->tetoRemuneracao = $tetoRemuneracao;
    }

    public function getNaturezaRubrica()
    {
        return $this->naturezaRubrica;
    }

    public function setNaturezaRubrica($naturezaRubrica)
    {
        $this->naturezaRubrica = $naturezaRubrica;

        return $this;
    }

    public function getSubgrupotce()
    {
        return $this->subgrupotce;
    }

    public function setSubgrupotce($subgrupotce)
    {
        $this->subgrupotce = $subgrupotce;
    }

    public function getTipoProcesso()
    {
        return $this->tipoProcesso;
    }

    public function setTipoProcesso($tipoProcesso)
    {
        $this->tipoProcesso = $tipoProcesso;
    }

    public function getNumeroProcessoCP()
    {
        return $this->numeroProcessoCP;
    }

    public function setNumeroProcessoCP($numeroProcessoCP)
    {
        $this->numeroProcessoCP = $numeroProcessoCP;
    }

    public function getExtensaoDecisao()
    {
        return $this->extensaoDecisao;
    }

    public function setExtensaoDecisao($extensaoDecisao)
    {
        $this->extensaoDecisao = $extensaoDecisao;
    }

    public function getCodigoSuspensaoProcessoCP()
    {
        return $this->codigoSuspensaoProcessoCP;
    }

    public function setCodigoSuspensaoProcessoCP($codigoSuspensaoProcessoCP)
    {
        $this->codigoSuspensaoProcessoCP = $codigoSuspensaoProcessoCP;
    }

    public function getNumeroProcessoIRRF()
    {
        return $this->numeroProcessoIRRF;
    }

    public function setNumeroProcessoIRRF($numeroProcessoIRRF)
    {
        $this->numeroProcessoIRRF = $numeroProcessoIRRF;
    }

    public function getCodigoSuspensaoIRRF()
    {
        return $this->codigoSuspensaoIRRF;
    }

    public function setCodigoSuspensaoIRRF($codigoSuspensaoIRRF)
    {
        $this->codigoSuspensaoIRRF = $codigoSuspensaoIRRF;
    }

    public function getNumeroProcessoFGTS()
    {
        return $this->numeroProcessoFGTS;
    }

    public function setNumeroProcessoFGTS($numeroProcessoFGTS)
    {
        $this->numeroProcessoFGTS = $numeroProcessoFGTS;
    }

    public function getNumeroProcessoPisPasep()
    {
        return $this->numeroProcessoPisPasep;
    }

    public function setNumeroProcessoPisPasep($numeroProcessoPisPasep)
    {
        $this->numeroProcessoPisPasep = $numeroProcessoPisPasep;
    }

    public function getCodigoSuspensaoPisPasep()
    {
        return $this->codigoSuspensaoPisPasep;
    }

    public function setCodigoSuspensaoPisPasep($codigoSuspensaoPisPasep)
    {
        $this->codigoSuspensaoPisPasep = $codigoSuspensaoPisPasep;
    }
}
