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
 * Model para Tipos de assentamentos
 *
 * @package pessoal
 * @author Renan Silva <renan.silva@dbseller.com.br>
 */
class TipoAssentamento
{
    const ASSENTAMENTO = 'S';

    const AFASTAMENTO = 'A';

    /**
     * Sequencial do tipo de assentamento
     *
     * @var Integer
     */
    private $iSequencial;

    /**
     * Código do tipo de assentamento
     *
     * @var String
     */
    private $sCodigo;

    /**
     * Descricao do tipo de assentamento
     *
     * @var String
     */
    private $sDescricao;

    /**
     * Tipo do assentamentos
     *
     * @var String
     */
    private $sTipo;

    private $aAssentamentos;

    /**
     * Natureza
     * @var integer
     */
    private $natureza;

    /**
     * Lancamento Mensal
     * @var integer
     */
    private $lancamentomensal;

    /**
     * Lancamento Anual
     * @var integer
     */
    private $lancamentoanual;

    /**
     * Gerar faltas no ponto eletrônico
     */
    private $geraFaltas = false;

    private $tipoReajuste;

    /**
     * @var bool
     */
    private $permiteDuplicar = false;

    /**
     * @throws Exception
     */
    public function __construct($iSequencial)
    {

        if (empty($iSequencial) && $iSequencial !== 0) {
            return;
        }

        $oDaoTipoasse = new cl_tipoasse;
        $rsTipoAssentamento = db_query($oDaoTipoasse->sql_query($iSequencial, "h12_assent, h12_descr, h12_tipo, h12_natureza, h12_gerafaltas::int, h12_tiporeajuste, h12_permiteduplicar, h12_lancamentomensal, h12_lancamentoanual"));

        if (!$rsTipoAssentamento) {
            throw new DBException(pg_last_error());
        }

        if (pg_num_rows($rsTipoAssentamento) == 0) {
            throw new Exception("Nenhum Assentamento encontrado para o código informado ({$iSequencial}).");
        }

        $oTipoAssentamento = db_utils::fieldsMemory($rsTipoAssentamento, 0);

        $this->setSequencial($iSequencial);
        $this->setCodigo($oTipoAssentamento->h12_assent);
        $this->setDescricao($oTipoAssentamento->h12_descr);
        $this->setTipo($oTipoAssentamento->h12_tipo);
        $this->setNatureza($oTipoAssentamento->h12_natureza);
        $this->setGeraFaltas($oTipoAssentamento->h12_gerafaltas);
        $this->setTipoReajuste($oTipoAssentamento->h12_tiporeajuste);
        $this->setPermiteDuplicar($oTipoAssentamento->h12_permiteduplicar === 't' ? true : false);
        $this->setLancamentoMensal($oTipoAssentamento->h12_lancamentomensal);
        $this->setLancamentoAnual($oTipoAssentamento->h12_lancamentoanual);
    }

    /**
     * @return int
     */
    public function getTipoReajuste()
    {
        return $this->tipoReajuste;
    }

    /**
     * @param int $tipoReajuste
     */
    public function setTipoReajuste($tipoReajuste)
    {
        $this->tipoReajuste = $tipoReajuste;
    }

    /**
     * @param $natureza
     */
    public function setNatureza($natureza)
    {
        $this->natureza = $natureza;
    }

    /**
     * @return int
     */
    public function getNatureza()
    {
        return $this->natureza;
    }

    /**
     * @param $lancamentomensal
     */
    public function setLancamentoMensal($lancamentomensal)
    {
        $this->lancamentomensal = $lancamentomensal;
    }

    /**
     * @return int
     */
    public function getLancamentoMensal()
    {
        return $this->lancamentomensal;
    }

    /**
     * @param $lancamentomensal
     */
    public function setLancamentoAnual($lancamentoanual)
    {
        $this->lancamentoanual = $lancamentoanual;
    }

    /**
     * @return int
     */
    public function getLancamentoAnual()
    {
        return $this->lancamentoanual;
    }

    /**
     * @param type $geraFaltas
     */
    public function setGeraFaltas($geraFaltas)
    {
        $this->geraFaltas = (bool)$geraFaltas;
        return $this;
    }

    /**
     * @return Boolean
     */
    public function getGeraFaltas()
    {
        return $this->geraFaltas;
    }

    /**
     * @return Boolean
     */
    public function getGerarFaltas()
    {
        return $this->getGeraFaltas();
    }

    /**
     * @return Boolean
     */
    public function isGerarFaltas()
    {
        return $this->getGeraFaltas();
    }

    /**
     * Retorna o código do tipo de assentamento
     * @return Integer
     */
    public function getSequencial()
    {
        return $this->iSequencial;
    }

    /**
     * Define o código do tipo de assentamento
     * @param Integer $iCodigo
     */
    public function setSequencial($iSequencial)
    {
        $this->iSequencial = $iSequencial;
    }

    /**
     * Retorna o código do tipo de assentamento
     * @return String
     */
    public function getCodigo()
    {
        return $this->sCodigo;
    }

    /**
     * Define o código do tipo de assentamento
     * @param String $iCodigo
     */
    public function setCodigo($sCodigo)
    {
        $this->sCodigo = (!empty($sCodigo)) ? $sCodigo : '';
    }

    /**
     * Retorna a descricao do tipo de assentamento
     * @return String
     */
    public function getDescricao()
    {
        return $this->sDescricao;
    }

    /**
     * Define a descricao do tipo de assentamento
     * @param String $iCodigo
     */
    public function setDescricao($sDescricao)
    {
        $this->sDescricao = (!empty($sDescricao)) ? $sDescricao : '';
    }

    /**
     * Retorna o tipo do assentamento
     * @return String
     */
    public function getTipo()
    {
        return $this->sTipo;
    }

    /**
     * Define o tipo do assentamento
     * A - Afastamento
     * S - Assentamento
     * @param String $sTipo
     */
    public function setTipo($sTipo)
    {
        $this->sTipo = $sTipo;
    }

    /**
     * @return bool
     */
    public function permiteDuplicar()
    {
        return $this->permiteDuplicar;
    }

    /**
     * @param bool $permiteDuplicar
     */
    public function setPermiteDuplicar($permiteDuplicar)
    {
        $this->permiteDuplicar = $permiteDuplicar;
    }

    /**
     * Persist na base o tipo de assentamento
     * @return mixed true | String mensagem de erro
     */
    public function persist()
    {
        return;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'codigo' => $this->getSequencial(),
            'descricao' => $this->getDescricao(),
            'natureza' => $this->getNatureza(),
            'tag' => $this->getCodigo(),
            'tipo' => $this->getTipo()
        );
    }

    /**
     * Transforma o objeto em um formato JSON
     * @return JSON
     */
    public function toJSON()
    {

        $aRetorno["codigo"] = $this->getSequencial();

        return json_encode((object)$aRetorno);
    }

    /**
     * Retorna os dados financeiros do tipo de assentamento
     *
     * @return false|String|StdClass   Retorna a linha da tabela cl_tipoassefinanceiro que
     *                                 vincula um tipo de assentamento a uma rubrica e uma formula
     */
    private function getTipoAssentamentoFinanceiro()
    {

        if (empty($this->iSequencial)) {
            return false;
        }

        $oDaoTipoassefinanceiro = new cl_tipoassefinanceiro;
        $sWhereTipoassefinanceiro = "     rh165_tipoasse = {$this->iSequencial}";
        $sWhereTipoassefinanceiro .= " and rh165_instit   = " . db_getsession('DB_instit');
        $sWhereTipoassefinanceiro .= " and rh165_anousu   = fc_anofolha(" . db_getsession('DB_instit') . ")";
        $sWhereTipoassefinanceiro .= " and rh165_mesusu   = fc_mesfolha(" . db_getsession('DB_instit') . ")";
        $sSqlTipoassefinanceiro = $oDaoTipoassefinanceiro->sql_query(null, "*", null, $sWhereTipoassefinanceiro);

        try {

            $rsTipoassefinanceiro = db_query($sSqlTipoassefinanceiro);

            if (!$rsTipoassefinanceiro) {
                throw new DBException("Ocorreu um erro ao buscar o tipo de assentamento financeiro.");
            }

            if (pg_num_rows($rsTipoassefinanceiro) == 0) {
                return false;
            }

            return db_utils::fieldsMemory($rsTipoassefinanceiro, 0);
        } catch (Exception $oErro) {
            return $oErro->getMessage();
        }
    }

    /**
     * Retorna a rubrica configurada para o tipo de assentamento
     *
     * @return false|Rubrica       Retorna false se não encontrar rubrica configurada a Rubrica
     */
    public function getRubricaTipoAssentamentoFinanceiro()
    {

        $oStdTipoAssentamentoFinanceiro = $this->getTipoAssentamentoFinanceiro();

        if ($oStdTipoAssentamentoFinanceiro instanceof stdClass) {
            return RubricaRepository::getInstanciaByCodigo($oStdTipoAssentamentoFinanceiro->rh165_rubric);
        }

        return false;
    }

    /**
     * Retorna a variável configuarada para o tipo de assentamento
     *
     * @return false|String       Retorna false se não encontrar varíavel configurada ou a string da variável
     */
    public function getVariavelTipoAssentamentoFinanceiro()
    {

        $oStdTipoAssentamentoFinanceiro = $this->getTipoAssentamentoFinanceiro();

        if ($oStdTipoAssentamentoFinanceiro instanceof stdClass) {
            return $oStdTipoAssentamentoFinanceiro->db148_nome;
        }

        return false;
    }

    /**
     * Retorna o tipo de lancamento configurado para o tipo de assentamento
     *
     * @return false|Integer
     */
    public function getTipoLancamentoTipoAssentamentoFinanceiro()
    {

        $oStdTipoAssentamentoFinanceiro = $this->getTipoAssentamentoFinanceiro();

        if (!empty($oStdTipoAssentamentoFinanceiro)) {
            return $oStdTipoAssentamentoFinanceiro->rh165_tipolancamento;
        }

        return false;
    }

    /**
     * @return Assentamento[]
     * @throws DBException
     */
    public function getAssentamentos()
    {

        if (empty($aAssentamentos)) {

            $oDaoAssenta = new cl_assenta;
            $sWhereAssentamentos = " h16_assent = {$this->iSequencial}";
            $sSqlAssentamentos = $oDaoAssenta->sql_query(null, "h16_codigo", null, $sWhereAssentamentos);

            try {

                $rsAssentamentos = db_query($sSqlAssentamentos);

                if (!$rsAssentamentos) {
                    throw new DBException("Ocorreu um erro ao buscar os assentamentos para este tipo.");
                }

                $iQtdeAssentamentos = pg_num_rows($rsAssentamentos);

                if ($iQtdeAssentamentos == 0) {
                    return array();
                }

                for ($iIndAssentamentos = 0; $iIndAssentamentos < $iQtdeAssentamentos; $iIndAssentamentos++) {

                    $oAssentamento = AssentamentoFactory::getByCodigo(db_utils::fieldsMemory($rsAssentamentos, $iIndAssentamentos)->h16_codigo);
                    $this->aAssentamentos[] = $oAssentamento;
                }
            } catch (Exception $oErro) {
                throw new DBException($oErro->getMessage());
            }
        }

        return $this->aAssentamentos;
    }

    public function getAtributosDinamicos()
    {

        $aAtributosDinamicos = array();

        $sSqlBuscaAtributos = 'SELECT db109_sequencial,
  	        	                     db109_descricao,
  	        	                     db109_nome,
  	        	                     db109_valordefault,
  	        	                     db109_tipo
                              FROM tipoassedb_cadattdinamico
 	                                 INNER JOIN db_cadattdinamico ON h79_db_cadattdinamico = db118_sequencial
 	                                 INNER JOIN db_cadattdinamicoatributos ON db109_db_cadattdinamico = db118_sequencial
 	                           WHERE h79_tipoasse = ' . $this->getSequencial();

        $sIndice = "AtributosDinamicosTipoAssentamento:{$this->getSequencial()}";
        if (DBRegistry::get($sIndice)) {
            return DBRegistry::get($sIndice);
        }

        $rsBuscaAtributos = db_query($sSqlBuscaAtributos);

        if (!$rsBuscaAtributos) {
            throw new DBException("Ocorreu um erro ao buscar os atributos dinâmicos para o assentamento.");
        }

        if (pg_num_rows($rsBuscaAtributos) > 0) {

            for ($iIndAtributos = 0; $iIndAtributos < pg_num_rows($rsBuscaAtributos); $iIndAtributos++) {

                $oAtributoDinamico = new stdClass;
                $oAtributoDinamico->descricaoAtributo = db_utils::fieldsMemory($rsBuscaAtributos, $iIndAtributos)->db109_descricao;
                $oAtributoDinamico->nomeAtributo = db_utils::fieldsMemory($rsBuscaAtributos, $iIndAtributos)->db109_nome;
                $oAtributoDinamico->codigoAtributo = db_utils::fieldsMemory($rsBuscaAtributos, $iIndAtributos)->db109_sequencial;
                $oAtributoDinamico->valorDefault = db_utils::fieldsMemory($rsBuscaAtributos, $iIndAtributos)->db109_valordefault;
                $oAtributoDinamico->tipoAtributo = (int)db_utils::fieldsMemory($rsBuscaAtributos, $iIndAtributos)->db109_tipo;

                $iCodigoAtributo = db_utils::fieldsMemory($rsBuscaAtributos, $iIndAtributos)->db109_sequencial;
                $aAtributosDinamicos[$iCodigoAtributo] = $oAtributoDinamico;
            }
        }

        DBRegistry::add($sIndice, $aAtributosDinamicos);
        return $aAtributosDinamicos;
    }

    public function getAtributoDinamicoPorNome($sNome)
    {

        $aAtributos = $this->getAtributosDinamicos();
        foreach ($aAtributos as $oAtributo) {
            if ($oAtributo->nomeAtributo == $sNome) {
                return $oAtributo;
            }
        }
    }

    /**
     * Obtém as informações do tipo de assentamento, incluindo os limites.
     *
     * @param integer $codigoAssentamento
     * @return array
     */
    public static function getTipoAssentamentoInfo($codigoAssentamento)
    {
        $query = "SELECT
                        h12_codigo,
                        h12_lancamentomensal,
                        h12_lancamentoanual
                    FROM
                        tipoasse
                    WHERE h12_codigo = {$codigoAssentamento} and h12_bloqueioassentamento = 't'";

        $rsTipoAssentamento = db_query($query);

        return \db_utils::getCollectionByRecord($rsTipoAssentamento, true);
    }

    /**
     * Conta assentamentos com valores diferentes de zero nos campos h12_lancamentomensal e h12_lancamentoanual.
     *
     * @return array
     * @throws DBException
     */
    public static function contaLancamentosPorServidor($dataInicial,$tipoAssentamento,$matricula)
    {
        $query = "SELECT
                    h16_regist,
                    sum(h16_quant) as quantidade_ano,
                        sum(
                            case
                                when EXTRACT(MONTH FROM h16_dtconc) = '{$dataInicial->getDate('m')}' then h16_quant else 0 end
                            ) as quantidade_mes
                    FROM
                        tipoasse
                            INNER JOIN assenta ON h16_assent = h12_codigo
                        WHERE h16_regist = $matricula AND
                              h16_assent = $tipoAssentamento AND
                              (h12_lancamentomensal <> 0 OR h12_lancamentoanual <> 0)
                            AND EXTRACT(YEAR FROM h16_dtconc) = '{$dataInicial->getDate('Y')}'
                    GROUP BY
                        h16_regist";


        $rsQuantidadeLancamentos = db_query($query);

        if (!$rsQuantidadeLancamentos) {
            throw new Exception("Erro ao buscar quantidade de lançamentos do assentamento");
        }
        return \db_utils::fieldsmemory($rsQuantidadeLancamentos, 0);
    }

    /**
     * Valida se existem outros assentamentos com bloqueio
     * no intervalo escolhido
     */
    public static function getExisteAssentamentosComBloqueio ($matricula, $dataInicial, $dataFinal)
    {
        $where = "h16_regist = $matricula
                  and h12_bloqueioassentamento = 't'
                    AND ('$dataInicial'::date BETWEEN h16_dtconc AND h16_dtterm
                        OR '$dataFinal'::date BETWEEN  h16_dtconc and h16_dtterm
                        );";
        $daoAssenta = new cl_assenta();
        $sql = $daoAssenta->sql_query_assentamentos("h16_regist", null,$where);

        $rsAssenta = db_query($sql);
        if (!$rsAssenta) {
            throw new Exception("Erro ao buscar assentamentos com bloqueio");
        }
        return (bool) pg_num_rows($rsAssenta);


    }
}
