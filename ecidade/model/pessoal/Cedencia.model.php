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
 * Classe para manipuação da Cedência.
 *
 * @author   Lucas Jarrier de Aquino Cavalcanti lucas.cavalcanti@dbseller.com.br
 * @package  Pessoal
 */
class Cedencia
{

    /**
     * @var String
     * C - Cedido
     * A - Adido
     * N - Não possue Cedencia.
     */
    private $tipoCedencia;

    /**
     * @var String
     * S - Possui Ônus
     * N - Não possui Õnus
     */
    private $onus;

    /**
     * @var String
     * S - Possui Ressarcimento
     * N - Não possui Ressarcimento
     */
    private $ressarcimento;

    /**
     * @var \DateTime
     * 
     * Data da movimentação do Servidor
     * referente a cedência.
     */
    private $dataMovimentacao;

    /**
     * @var \DateTime
     * 
     * Data da devolução do Servidor
     * referente a cedência.
     */
    private $dataDevolucao;

    /**
     * @var Integer
     * 
     * Numero do Cgm correspondente 
     * a instituicao da cedência.
     */
    private $numeroCgm;

    /**
     * @var String
     * 
     * Matricula da Origem da Cedência.
     */
    private $matriculaCedencia;

    /**
     * @var String
     * 
     * Servidor Cedido será informado no eSocial(S1200/S1202)?
     * S - Sim.
     * N - Não.
     */
    private $servidorCedido;

    /**
     * @var Integer
     * 
     * Matricula do servidor.
     */
    private $matricula;

    /**
     * @deprecated
     * Referencia a rhpessoalmov
     * Descontinuado!!!
     */
    private $seqpes;

    /**
     * @var Boolean
     * 
     * Valor Lógico Servidor 
     * indicado para Conselho (Cód Categoria 305).
     * 
     * @return True = Servidor Indicado.
     * @return False = Servidor não Indicado.
     */
    private $indicadoConselho;

    /**
     * @var Integer
     */
    private $codCategoriaOrigem;

    /**
     * @var \DateTime
     */
    private $dataAdmissaoOrigem;

    /**
     * @var Integer
     */
    private $tipoRegimeOrigem;

    /**
     * @var Integer
     */
    private $tipoRegimePrevidencia;

    /**
     * @var Integer
     */
    private $cnpjCedencia;

    /**
     * Cedência constructor.
     * @param int $sequencial
     * @throws Exception
     */
    public function __construct($matricula = null, $sequencial = null)
    {

        /**
         * Caso seja fornecida uma matricula,
         * o objeto sera montado considerando
         * o registro mais rescente da tabela.
         */

        if (!empty($matricula)) {
            $seq = $this->getAllCedenciasByMatricula($matricula);
            if (sizeof($seq) > 0) {
                $sequencial = $this->getAllCedenciasByMatricula($matricula)[0];
            }
        }

        if (!empty($sequencial)) {

            $sqlCedencia = "
            select 
                rh261_credencial, 
                rh261_onus,
                rh261_ressarcimento,
                rh261_datamovimentacao,
                rh261_devolucao,
                rh261_numcgm,
                rh261_matorigemcedente,
                rh261_servidorcedido,
                rh261_regist,
                rh261_seqpes,
                rh261_indicadoconselho,
                rh261_codcategoriaorigem,
                rh261_tiporegimeorigem,
                rh261_tiporegimeprev,
                rh261_dtorigemadmissao,
                z01_cgccpf
            from 
                pessoal.rhcedencia
            inner join
                protocolo.cgm
            on z01_numcgm = rh261_numcgm
            where rh261_sequencial = {$sequencial}";

            $resultRhCedencia = db_query($sqlCedencia);

            if (!$resultRhCedencia) {
                throw new DBException("Erro ao Buscar Cedência.");
            }

            if (pg_num_rows($resultRhCedencia) > 0) {
                $oRhCedencia = db_utils::fieldsMemory($resultRhCedencia, 0);

                $this->setIndicadoConselho($oRhCedencia->rh261_indicadoconselho);
                $this->setMatricula($oRhCedencia->rh261_regist);
                $this->setMatriculaCedencia($oRhCedencia->rh261_matorigemcedente);
                $this->setNumeroCgm($oRhCedencia->rh261_numcgm);
                $this->setOnus($oRhCedencia->rh261_onus);
                $this->setRessarcimento($oRhCedencia->rh261_ressarcimento);
                $this->setSeqpes($oRhCedencia->rh261_seqpes);
                $this->setServidorCedido($oRhCedencia->rh261_servidorcedido);
                $this->setTipoCedencia($oRhCedencia->rh261_credencial);
                $this->setCodCategoriaOrigem($oRhCedencia->rh261_codcategoriaorigem);
                $this->setTipoRegimeOrigem($oRhCedencia->rh261_tiporegimeorigem);
                $this->setTipoRegimePrevidencia($oRhCedencia->rh261_tiporegimeprev);
                $this->setCnpjCedencia($oRhCedencia->z01_cgccpf);

                if (!empty($oRhCedencia->rh261_datamovimentacao)) {
                    $this->setDataMovimentacao(new DBDate($oRhCedencia->rh261_datamovimentacao));
                }

                if (!empty($oRhCedencia->rh261_devolucao)) {
                    $this->setDataDevolucao(new DBDate($oRhCedencia->rh261_devolucao));
                }

                if (!empty($oRhCedencia->rh261_dtorigemadmissao)) {
                    $this->setDataAdmissaoOrigem(new DBDate($oRhCedencia->rh261_dtorigemadmissao));
                }
            }
        }
    }

    public function getIndicadoConselho()
    {
        return $this->indicadoConselho;
    }

    public function setIndicadoConselho($indicadoConselho)
    {
        $this->indicadoConselho = $indicadoConselho;

        return $this;
    }

    public function getSeqpes()
    {
        return $this->seqpes;
    }

    public function setSeqpes($seqpes)
    {
        $this->seqpes = $seqpes;

        return $this;
    }

    public function getMatricula()
    {
        return $this->matricula;
    }

    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    public function getServidorCedido()
    {
        return $this->servidorCedido;
    }

    public function setServidorCedido($servidorCedido)
    {
        $this->servidorCedido = $servidorCedido;

        return $this;
    }

    public function getMatriculaCedencia()
    {
        return $this->matriculaCedencia;
    }

    public function setMatriculaCedencia($matriculaCedencia)
    {
        $this->matriculaCedencia = $matriculaCedencia;

        return $this;
    }

    public function getNumeroCgm()
    {
        return $this->numeroCgm;
    }

    public function setNumeroCgm($numeroCgm)
    {
        $this->numeroCgm = $numeroCgm;

        return $this;
    }

    public function getDataDevolucao()
    {
        return $this->dataDevolucao;
    }

    public function setDataDevolucao($dataDevolucao)
    {
        $this->dataDevolucao = $dataDevolucao;

        return $this;
    }

    public function getDataMovimentacao()
    {
        return $this->dataMovimentacao;
    }

    public function setDataMovimentacao($dataMovimentacao)
    {
        $this->dataMovimentacao = $dataMovimentacao;

        return $this;
    }

    public function getRessarcimento()
    {
        return $this->ressarcimento;
    }

    public function setRessarcimento($ressarcimento)
    {
        $this->ressarcimento = $ressarcimento;

        return $this;
    }

    public function getOnus()
    {
        return $this->onus;
    }

    public function setOnus($onus)
    {
        $this->onus = $onus;

        return $this;
    }

    public function getTipoCedencia()
    {
        return $this->tipoCedencia;
    }

    public function setTipoCedencia($tipoCedencia)
    {
        $this->tipoCedencia = $tipoCedencia;

        return $this;
    }

    /**
     * Retorna todas as Cedências relacionadas 
     * a uma matrícula de servidor.
     * Ordenado pela mais rescente cadastrada na tabela.
     * 
     * @return Array
     */
    public function getAllCedenciasByMatricula($matricula = null)
    {
        if (empty($matricula)) {
            $matricula = $this->matricula;
        }

        $sql = "
            select 
                rh261_sequencial 
            from 
                rhcedencia 
            where 
                rh261_regist = {$matricula}  
            order by 
                rh261_datamovimentacao desc, 
                rh261_sequencial desc";

        $result = db_query($sql);

        if (!$result) {
            throw new DBException("Erro ao Buscar Cedência.");
        }

        $totalRegistros = pg_num_rows($result);

        $sequenciais = [];

        for ($row = 0; $row < $totalRegistros; $row++) {
            $current = \db_utils::fieldsMemory($result, $row);
            $sequenciais[] = $current->rh261_sequencial;
        }

        return $sequenciais;
    }

    public function getRegimeOrigem($matricula)
    {
        $sql  = "select rh261_tiporegimeorigem from pessoal.rhcedencia
                where rh261_regist = '{$matricula}'";
        
        $result = db_query($sql);
        
        if (pg_num_rows($result) > 0) {
           return db_utils::fieldsMemory($result, 0)->rh261_tiporegimeorigem;
        }
    }

    /**
     * @return Boolean
     * 
     * retorna 'true' caso o servidor seja conselheiro, 
     * 'false' caso contrário.
     */
    public function isConselheiro()
    {
        return ($this->indicadoConselho == 't' ? true : false);
    }

    public function getDataAdmissaoOrigem()
    {
        return $this->dataAdmissaoOrigem;
    }

    public function setDataAdmissaoOrigem($dataAdmissaoOrigem)
    {
        $this->dataAdmissaoOrigem = $dataAdmissaoOrigem;

        return $this;
    }

    public function getCodCategoriaOrigem()
    {
        return $this->codCategoriaOrigem;
    }

    public function setCodCategoriaOrigem($codCategoriaOrigem)
    {
        $this->codCategoriaOrigem = $codCategoriaOrigem;

        return $this;
    }

    public function getTipoRegimeOrigem()
    {
        return $this->tipoRegimeOrigem;
    }

    public function setTipoRegimeOrigem($tipoRegimeOrigem)
    {
        $this->tipoRegimeOrigem = $tipoRegimeOrigem;

        return $this;
    }

    public function getTipoRegimePrevidencia()
    {
        return $this->tipoRegimePrevidencia;
    }

    public function setTipoRegimePrevidencia($tipoRegimePrevidencia)
    {
        $this->tipoRegimePrevidencia = $tipoRegimePrevidencia;

        return $this;
    }

    public function getCnpjCedencia()
    {
        return $this->cnpjCedencia;
    }

    public function setCnpjCedencia($cnpjCedencia)
    {
        $this->cnpjCedencia = $cnpjCedencia;

        return $this;
    }
}
