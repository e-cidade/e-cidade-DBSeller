<?php
/*
 *
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

namespace ECidade\Financeiro\Orcamento\Recurso;

use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use cl_orctiporec;
use db_utils;
use DBDate;
use DBEstruturaValor;
use ECidade\Financeiro\Orcamento\Registry\ComplementoRegistry;
use Exception;
use TribunalEstrutura;

/**
 * Class Recurso
 */
class Recurso
{
    /**
     * Código do recurso.
     *
     * @var int
     */
    protected $iCodigoRecurso;

    /**
     * Descrição da finalidade do recurso.
     *
     * @var string
     */
    protected $sFinalidadeRecurso;

    /**
     * Tipo do recurso.
     *
     * @var int
     */
    protected $iTipoRecurso;

    /**
     * Data limite do recurso.
     *
     * @var string
     */
    protected $sDataLimiteRecurso;

    protected $oDBEstruturaValor;

    private $lNovo = true;

    /**
     * @var string
     */
    private $sDescricao = '';

    /**
     * Código SICONFI do recurso.
     *
     * @var string
     */
    private $sCodigoSiconfi;


    const LIVRE = 1;
    const VINCULADO = 2;

    /**
     * @var string
     */
    protected $sEspecificacao;

    /**
     * @var integer
     */
    protected $iGrupo;

    /**
     * @var integer
     */
    protected $iTipoDetalhamento;

    /**
     * @var integer
     */
    protected $iIdentificadorUso;

    /**
     * Tipo do estrutural.
     *
     * @var string
     */
    protected $sTipoEstrutural = 'Recurso';

    /**
     * @var string
     */
    protected $sCodigoTribunal = '';

    protected $codigoComplemento;

    /**
     * @var \ECidade\Financeiro\Orcamento\Model\Complemento
     */
    protected $complemento;

    /**
     * código que identifica o recurso
     * @var string
     */
    private $recurso;

    public $sEstrutural;

    /**
     * Recurso constructor.
     *
     * @param null $iCodigoRecurso
     */
    public function __construct($iCodigoRecurso = null)
    {
        /* validadod desta forma pois existe o recurso 0 (zero) */
        if (!is_null($iCodigoRecurso) && $iCodigoRecurso !== "") {
            $oDaoOrcTipoRec = new cl_orctiporec();
            $sWhereOrcTipoRec = "orctiporec.o15_codigo = {$iCodigoRecurso}";
            $sSqlOrcTipoRec = $oDaoOrcTipoRec->sql_query(
                null,
                'orctiporec.*',
                null,
                $sWhereOrcTipoRec
            );

            $rsSqlOrcTipoRec = $oDaoOrcTipoRec->sql_record($sSqlOrcTipoRec);
            if ($oDaoOrcTipoRec->numrows > 0) {
                $this->lNovo = false;
                $oOrcTipoRec = db_utils::fieldsMemory($rsSqlOrcTipoRec, 0);
                $this->iCodigoRecurso = $iCodigoRecurso;
                $this->sDescricao = $oOrcTipoRec->o15_descr;
                $this->sEstrutural = $oOrcTipoRec->o15_codtri;
                $this->sFinalidadeRecurso = $oOrcTipoRec->o15_finali;
                $this->iTipoRecurso = $oOrcTipoRec->o15_tipo;
                $this->sDataLimiteRecurso = $oOrcTipoRec->o15_datalimite;
                $this->sCodigoSiconfi = $oOrcTipoRec->o15_codigosiconfi;
                $this->sCodigoTribunal = $oOrcTipoRec->o15_codtri;
                $this->codigoComplemento = $oOrcTipoRec->o15_complemento;

                $this->iIdentificadorUso = $oOrcTipoRec->o15_loaidentificadoruso;
                $this->iTipoDetalhamento = $oOrcTipoRec->o15_loatipo;
                $this->iGrupo = $oOrcTipoRec->o15_loagrupo;
                $this->sEspecificacao = $oOrcTipoRec->o15_loaespecificacao;
                $this->recurso = $oOrcTipoRec->o15_recurso;

                $this->setEstruturaValor(new TribunalEstrutura($oOrcTipoRec->o15_db_estruturavalor));
            }
        }
    }

    /**
     * Retorna fonte de recurso
     *
     * @param int $iRecurso
     *
     * @return int
     */
    public static function getFonteRecusoByCodigo($iRecurso)
    {
        $oRecurso = new Recurso($iRecurso);
        $iEspecificacao = $oRecurso->getFonteDeRecurso();
        return $iEspecificacao;
    }

    /**
     * Retorna o codigo do recurso
     *
     * @return int
     * @see getCodigo
     * @deprecated
     */
    public function getCodigoRecurso()
    {
        return $this->iCodigoRecurso;
    }

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->iCodigoRecurso;
    }

    /**
     * Retorna o tipo de recurso diponivel.
     *
     * @return int
     */
    public function getTipoRecurso()
    {
        return $this->iTipoRecurso;
    }

    /**
     * Retorna a data limite do recurso.
     *
     * @return string
     */
    public function getDataLimiteRecurso()
    {
        return $this->sDataLimiteRecurso;
    }

    /**
     * @return string
     */
    public function getFinalidadeRecurso()
    {
        return $this->sFinalidadeRecurso;
    }

    /**
     * Seta um novo código para o recurso.
     *
     * @param int $iCodigoRecurso
     *
     * @return Recurso
     */
    public function setCodigoRecurso($iCodigoRecurso)
    {
        $this->iCodigoRecurso = $iCodigoRecurso;
        return $this;
    }

    /**
     * Seta um novo tipo para o recurso.
     *
     * @param int $iTipoRecurso
     *
     * @return Recurso
     */
    public function setTipoRecurso($iTipoRecurso)
    {
        $this->iTipoRecurso = $iTipoRecurso;
        return $this;
    }

    /**
     * Seta uma nova data de limite para o recurso.
     *
     * @param string $sDataLimiteRecurso
     *
     * @return Recurso
     */
    public function setDataLimiteRecurso($sDataLimiteRecurso)
    {
        $this->sDataLimiteRecurso = $sDataLimiteRecurso;
        return $this;
    }

    /**
     * Seta uma nova finalidade para o recurso.
     *
     * @param string $sFinalidadeRecurso
     *
     * @return Recurso
     */
    public function setFinalidadeRecurso($sFinalidadeRecurso)
    {
        $this->sFinalidadeRecurso = $sFinalidadeRecurso;
        return $this;
    }

    /**
     * Retorna o código da estrutura.
     *
     * @param int $iCodigoEstrutura
     *
     * @return $iCodigoRecurso
     */
    public static function getCodigoByEstrutura($iCodigoEstrutura)
    {
        $iCodigoRecurso = null;
        $oDaoOrcTipoRec = new cl_orctiporec;
        $sSqlOrcTipoRec = $oDaoOrcTipoRec->sql_query_file(
            null,
            'o15_codigo',
            null,
            "o15_db_estruturavalor = {$iCodigoEstrutura}"
        );

        $rsSqlOrcTipoRec = $oDaoOrcTipoRec->sql_record($sSqlOrcTipoRec);
        if ($oDaoOrcTipoRec->numrows > 0) {
            $iCodigoRecurso = db_utils::fieldsMemory($rsSqlOrcTipoRec, 0)->o15_codigo;
        }

        return $iCodigoRecurso;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function salvar()
    {
        if (!db_utils::inTransaction()) {
            throw new Exception("Não existe transação ativa.");
        }
        $this->sCodigoTribunal = $this->getEstruturaValor()->getEstrutural();
        $oDaoOrcTipoRec = new cl_orctiporec();
        $oDaoOrcTipoRec->o15_descr = $this->getEstruturaValor()->getDescricao();
        $oDaoOrcTipoRec->o15_codtri = $this->getEstruturaValor()->getEstrutural();
        $oDaoOrcTipoRec->o15_finali = $this->getFinalidadeRecurso();
        $oDaoOrcTipoRec->o15_tipo = $this->getTipoRecurso();
        $oDaoOrcTipoRec->o15_datalimite = $this->getDataLimiteRecurso();
        $oDaoOrcTipoRec->o15_db_estruturavalor = $this->getEstruturaValor()->getCodigo();
        $oDaoOrcTipoRec->o15_codigosiconfi = $this->getCodigoSiconfi();
        $oDaoOrcTipoRec->o15_complemento = $this->getComplemento();
        $oDaoOrcTipoRec->o15_loaidentificadoruso = $this->iIdentificadorUso;
        $oDaoOrcTipoRec->o15_loatipo = $this->iTipoDetalhamento;
        $oDaoOrcTipoRec->o15_loagrupo = $this->iGrupo;
        $oDaoOrcTipoRec->o15_loaespecificacao = $this->sEspecificacao;

        $iCodigoRecurso = "{$this->getCodigo()}";
        $sWhereOrcTipoRec = "orctiporec.o15_codigo = {$iCodigoRecurso}";
        $sSqlOrcTipoRec = $oDaoOrcTipoRec->sql_query(
            null,
            'orctiporec.*',
            null,
            $sWhereOrcTipoRec
        );
        $rsSqlOrcTipoRec = $oDaoOrcTipoRec->sql_record($sSqlOrcTipoRec);
        if ($oDaoOrcTipoRec->numrows > 0 && !$this->lNovo) {
            $oDaoOrcTipoRec->o15_codigo = $iCodigoRecurso;
            $oDaoOrcTipoRec->alterar($oDaoOrcTipoRec->o15_codigo);
        } else {
            $oDaoOrcTipoRec->o15_codigo = $iCodigoRecurso;
            $oDaoOrcTipoRec->incluir($oDaoOrcTipoRec->o15_codigo);
            $this->setCodigoRecurso($oDaoOrcTipoRec->o15_codigo);
        }

        if ($oDaoOrcTipoRec->erro_status == 0) {
            throw new Exception($oDaoOrcTipoRec->erro_msg);
        }

        $this->validarEstruturaRecurso();
        return $this;
    }

    /**
     * @throws Exception
     */
    public function remover()
    {
        if (!db_utils::inTransaction()) {
            throw new Exception("Não existe transação ativa.");
        }

        $iCodigoRecurso = $this->getCodigo();
        if (empty($iCodigoRecurso) && $iCodigoRecurso != 0) {
            throw new Exception("Código do recurso não informado!\\nExclusão não efetuada.");
        }

        $oDaoOrcTipoRec = new cl_orctiporec;
        $sWhereOrcTipoRec = "orctiporec.o15_codigo = {$iCodigoRecurso}";
        $oDaoOrcTipoRec->excluir(null, $sWhereOrcTipoRec);
        if ($oDaoOrcTipoRec->numrows_excluir == 0) {
            throw new Exception($oDaoOrcTipoRec->erro_msg);
        }

        $this->getEstruturaValor()->remover();
        //parent::remover();
    }

    /**
     * @param DBEstruturaValor $oEstruturaValor
     *
     * @return $this
     */
    public function setEstruturaValor(DBEstruturaValor $oEstruturaValor)
    {
        $this->oDBEstruturaValor = $oEstruturaValor;
        return $this;
    }

    /**
     * Retorna uma instancia de DBEstruturaValor
     *
     * @return DBEstruturaValor
     */
    public function getEstruturaValor()
    {
        return $this->oDBEstruturaValor;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->sDescricao;
    }


    /**
     * @param string $sCodigoSiconfi
     *
     * @return Recurso
     */
    public function setCodigoSiconfi($sCodigoSiconfi)
    {
        $this->sCodigoSiconfi = $sCodigoSiconfi;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoSiconfi()
    {
        return $this->sCodigoSiconfi;
    }


    /**
     * @param $iIdentificadorUso
     */
    public function setIdentificadorUsoLOA($iIdentificadorUso)
    {
        $this->iIdentificadorUso = $iIdentificadorUso;
    }

    /**
     * @param $iTipoDetalhamento
     */
    public function setTipoDetalhamentoLOA($iTipoDetalhamento)
    {
        $this->iTipoDetalhamento = $iTipoDetalhamento;
    }

    /**
     * @param int
     */
    public function setGrupoLOA($iGrupo)
    {
        $this->iGrupo = $iGrupo;
    }

    /**
     * @param $sEspecificacao
     */
    public function setEspecificacaoLOA($sEspecificacao)
    {
        $this->sEspecificacao = $sEspecificacao;
        if (!empty($sEspecificacao) && FONTE_RECURSO_UNIAO) {
            $this->sEspecificacao = str_pad($sEspecificacao, 2, '0', STR_PAD_LEFT);
        }
    }


    /**
     * @return int
     */
    public function getIdentificadorUsoLOA()
    {
        return $this->iIdentificadorUso;
    }

    /**
     * @return int
     */
    public function getTipoDetalhamentoLOA()
    {
        return $this->iTipoDetalhamento;
    }

    /**
     * @return int
     */
    public function getGrupoLOA()
    {
        return $this->iGrupo;
    }

    /**
     * @return string
     */
    public function getEspecificacaoLOA()
    {
        return $this->sEspecificacao;
    }

    /**
     * Retorna a subfonte de recurso (o15_recurso) formatada ou não
     *
     * @param bool $formatado
     *
     * @return string
     */
    public function getFonteDeRecurso()
    {
        return $this->getRecurso();
    }

    /**
     * Verifica se existe um outro recurso com o mesmo código do tribunal
     *
     * @return bool
     * @throws Exception
     */
    private function validarEstruturaRecurso()
    {

        $dataAtual = date('Y-m-d', db_getsession('DB_datausu'));
        if (!empty($this->sDataLimiteRecurso)) {
            $dataLimite = new DBDate($this->sDataLimiteRecurso);
            $dataAtual = new DBDate($dataAtual);
            if ($dataLimite->getTimeStamp() < $dataAtual->getTimeStamp()) {
                return true;
            }
        }

        $where = implode(' and ', array(
            "o15_codigo <> {$this->iCodigoRecurso}",
            "trim(o15_codtri) = trim('{$this->sCodigoTribunal}')",
            "(o15_datalimite is null or o15_datalimite >= '{$dataAtual}')"
        ));

        $daoRecurso = new cl_orctiporec();
        $buscaRecurso = $daoRecurso->sql_query_file(null, "*", null, $where);
        $resultBusca = db_query($buscaRecurso);
        if (!$resultBusca) {
            throw new Exception("Ocorreu um erro ao consultar o código do tribunal.");
        }

        if (pg_num_rows($resultBusca) > 0) {
            $stdRecurso = db_utils::fieldsMemory($resultBusca, 0);
            $mensagem = "Este Código do Tribunal ({$this->sCodigoTribunal}) já está vinculado ao recurso ";
            $mensagem .= "{$stdRecurso->o15_codigo} - {$stdRecurso->o15_descr}. Verifique novamente o conteúdo.";
            //throw new Exception($mensagem);
        }
        return true;
    }

    /**
     * @return \ECidade\Financeiro\Orcamento\Model\Complemento
     * @throws Exception
     */
    public function getComplementoRecursoVinculado()
    {
        if (empty($this->complemento) && !is_null($this->codigoComplemento)) {
            $this->complemento = ComplementoRegistry::get($this->codigoComplemento);
        }

        return $this->complemento;
    }

    /**
     * @return string
     */
    public function getComplemento()
    {
        return $this->codigoComplemento;
    }

    /**
     * @param string $complemento
     */
    public function setComplemento($complemento)
    {
        $this->codigoComplemento = $complemento;
    }

    /**
     * @return string
     */
    public function getCodigoTribunal()
    {
        return $this->sCodigoTribunal;
    }

    /**
     * @param string $sCodigoTribunal
     */
    public function setCodigoTribunal($sCodigoTribunal)
    {
        $this->sCodigoTribunal = $sCodigoTribunal;
    }

    /**
     * Retorna a subfonte de recurso (o15_recurso)
     * @return string
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * @param $exerecicio
     * @return FonteRecurso
     */
    public function getFonteRecurso($exerecicio)
    {
        return FonteRecurso::where('orctiporec_id', $this->getCodigo())
            ->where('exercicio', $exerecicio)
            ->first();
    }
}
