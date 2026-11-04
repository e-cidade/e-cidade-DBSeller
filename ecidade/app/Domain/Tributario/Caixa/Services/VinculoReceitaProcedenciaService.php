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

namespace App\Domain\Tributario\Caixa\Services;

use BusinessException;

class VinculoReceitaProcedenciaService
{
    /**
     * Código da receita
     * @var integer
     */
    private $iReceita;

    /**
     * Descrição da receita
     * @var string
     */
    private $sDescricaoReceita;

    /**
     * Código da procedência
     * @var integer
     */
    private $iProcedencia;

    /**
     * Descrição da procedência
     * @var string
     */
    private $sDescricaoProcedencia;

    /**
     * Tipo de inscrição 1 - Cobranca Admin, 2 - Divida Ativa
     * @var integer
     */
    private $iTipoInscricao;

    /**
     * @param integer $iReceita
     * @throws BusinessException
     */
    public function getDados($iReceita)
    {
        if (empty($iReceita)) {
            throw new BusinessException('Receita não informada.');
        }

        $sSql = "
            select
                k202_receita as receita,
                k02_drecei as descricao_receita,
                coalesce(k202_procdiver, k202_proced) as proced,
                coalesce(dv09_descr, v03_descr) as descricao_proced
            from caixa.vinculoreceitaprocedencia
            inner join tabrec on k02_codigo = k202_receita
            left join diversos.procdiver on k202_procdiver  = dv09_procdiver
            left join divida.proced on k202_proced = proced.v03_codigo
            where k202_receita = {$iReceita}
        ";

        $rsVinculo = db_query($sSql);

        if (pg_num_rows($rsVinculo) == 0) {
            return null;
        }

        $oDadosVinculo = \db_utils::fieldsMemory($rsVinculo, 0);
        return $oDadosVinculo;
    }

    /**
     * @return integer
     */
    public function getReceita()
    {
        return $this->iReceita;
    }

    /**
     * @param integer $iReceita
     * @return void
     */
    public function setReceita($iReceita)
    {
        $this->iReceita = $iReceita;
    }

    /**
     * @return string
     */
    public function getDescricaoReceita()
    {
        return $this->sDescricaoReceita;
    }

    /**
     * @return integer
     */
    public function getProcedencia()
    {
        return $this->iProcedencia;
    }

    /**
     * @param integer $iProcedenciaDiversos
     * @return void
     */
    public function setProcedencia($iProcedencia)
    {
        $this->iProcedencia = $iProcedencia;
    }

    public function setTipoInscricao($iTipoInscricao)
    {
        $this->iTipoInscricao = $iTipoInscricao;
    }

    public function getTipoInscricao()
    {
        return $this->iTipoInscricao;
    }

    /**
     * @return string
     */
    public function getDescricaoProcedencia()
    {
        return $this->sDescricaoProcedencia;
    }

    /**
     * @throws BusinessException
     * @return bool
     */
    public function salvar()
    {
        if ($this->iReceita == null || $this->iProcedencia == null) {
            throw new BusinessException("Todos os campos devem ser preenchidos");
        }

        switch (intval($this->iTipoInscricao)) {
            case 1:
                $this->salvarProcedCobranca();
                break;
            case 2:
                $this->salvarProcedDivida();
                break;
            default:
                throw new BusinessException("Tipo de inscrição inválido");
                break;
        }
    }

    private function salvarProcedDivida()
    {
        $sSqlVerifica = "
            select k202_sequencial from caixa.vinculoreceitaprocedencia
            where k202_receita = {$this->iReceita}
        ";
        $rsVerifica   = db_query($sSqlVerifica);
        if (!$rsVerifica) {
            throw new BusinessException("Não foi possível verificar cadastro da receita");
        }

        if (pg_num_rows($rsVerifica) > 0) {
            $sSql = "update caixa.vinculoreceitaprocedencia
                   set k202_proced = {$this->iProcedencia}, k202_procdiver = null
                 where k202_receita = {$this->iReceita};";
        } else {
            $sSql = "
                insert into caixa.vinculoreceitaprocedencia(k202_receita, k202_proced)
                values({$this->iReceita}, {$this->iProcedencia})
            ";
        }

        $rsSalvar = db_query($sSql);
        if (!$rsSalvar) {
            throw new BusinessException("Não foi possível gravar os dados" . $sSql);
        }

        return true;
    }

    private function salvarProcedCobranca()
    {
        $sSqlVerifica = "
            select k202_sequencial from caixa.vinculoreceitaprocedencia
            where k202_receita = {$this->iReceita}
        ";
        $rsVerifica   = db_query($sSqlVerifica);
        if (!$rsVerifica) {
            throw new BusinessException("Não foi possível verificar cadastro da receita");
        }

        if (pg_num_rows($rsVerifica) > 0) {
            $sSql = "update caixa.vinculoreceitaprocedencia
                   set k202_procdiver = {$this->iProcedencia}, k202_proced = null
                 where k202_receita = {$this->iReceita}";
        } else {
            $sSql = "
                insert into caixa.vinculoreceitaprocedencia(k202_receita, k202_procdiver)
                values({$this->iReceita}, {$this->iProcedencia})
            ";
        }

        $rsSalvar = db_query($sSql);
        if (!$rsSalvar) {
            throw new BusinessException("Não foi possível gravar os dados" . $sSql);
        }

        return true;
    }
}
