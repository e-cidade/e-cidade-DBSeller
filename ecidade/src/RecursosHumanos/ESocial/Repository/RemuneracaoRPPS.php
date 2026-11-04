<?php
/**
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

namespace ECidade\RecursosHumanos\ESocial\Repository;

use cl_cgm;
use cl_rhpessoalmov;
use cl_regimeprevidenciainssirf;

use BusinessException;
use DBException;
use ParameterException;

use db_utils;
use DBCompetencia;
use InstituicaoRepository;

/**
 * Class RemuneracaoRPPS
 * @package ECidade\RecursosHumanos\ESocial\Repository
 */
class RemuneracaoRPPS extends \BaseClassRepository
{
    protected static $oInstance;

    /**
     * @param DBCompetencia $dbCompetencia
     * @return \stdClass[]
     * @throws DBException
     */
    public static function buscarTodosCGMCompetencia(DBCompetencia $dbCompetencia, $servidores = null)
    {
        $instituicao = InstituicaoRepository::getInstituicaoSessao()->getCodigo();
        $where = [
            "rh02_anousu = {$dbCompetencia->getAno()}",
            "rh02_mesusu = {$dbCompetencia->getMes()}",
            "rh01_admiss <= '{$dbCompetencia->getAno()}-{$dbCompetencia->getMes()}-{$dbCompetencia->getUltimoDia()}'",
            "(rh02_tbprev in (select
                                distinct on (r33_codtab)
                                r33_codtab-2
                            from
                                regimeprevidenciainssirf
                            inner join inssirf on
                                inssirf.r33_codigo = regimeprevidenciainssirf.rh129_codigo
                                and inssirf.r33_instit = regimeprevidenciainssirf.rh129_instit
                            inner join regimeprevidencia on
                                regimeprevidencia.rh127_sequencial = regimeprevidenciainssirf.rh129_regimeprevidencia
                                and regimeprevidencia.rh127_sequencial != 2
                                and regimeprevidencia.rh127_sequencial is not null
                            where
                                regimeprevidenciainssirf.rh129_instit = {$instituicao}) or rh261_regist is not null)",
            "rh02_instit = {$instituicao}"
        ];

        if (!empty($servidores)) {
            $matriculas = "rh02_regist in(";
            $max = sizeof($servidores);
            for ($i = 0; $i < $max; $i++) {
                $matriculas .= $servidores[$i]->getMatricula();
                if ($i == ($max-1)) {
                    $matriculas .= ")";
                } else {
                    $matriculas .= ",";
                }
            }
            $where[] = $matriculas;
        }

        /**
         * Reforada forma de buscar os cgm, para nao retornar cgm sem calculo no mes
         */
        $selectBase = <<<SQL
                    SELECT DISTINCT
                rh01_numcgm AS cgm
            FROM
                rhpessoalmov
                INNER JOIN rhpessoal ON rhpessoal.rh01_regist = rhpessoalmov.rh02_regist
                    AND rhpessoal.rh01_instit = rhpessoalmov.rh02_instit
                INNER JOIN cgm ON cgm.z01_numcgm = rhpessoal.rh01_numcgm
                left JOIN pessoal.inssirf ON r33_anousu = rh02_anousu
                    AND r33_mesusu = rh02_mesusu
                    AND r33_instit = rhpessoalmov.rh02_instit
                    AND r33_codtab = rhpessoalmov.rh02_tbprev + 2
                left JOIN pessoal.regimeprevidenciainssirf ON
                    rh129_codigo = r33_codigo
                    AND rh129_instit = r33_instit AND rh129_regimeprevidencia != 2
                left join pessoal.rhcedencia on rh261_regist = rh02_regist and rh261_servidorcedido = 'S'
SQL;
        $whereBase = "where "  . implode(' AND ', $where);
        /**
         * Buscamos folhas de salario, complementar, 13 e rescisao com o union abaixo
         */
        $sql = <<<SQL
            $selectBase
                inner join pessoal.gerfsal on r14_regist = rh01_regist
                    and r14_anousu = rh02_anousu and r14_mesusu = rh02_mesusu
            $whereBase
            UNION
            $selectBase
                inner join pessoal.gerfcom on r48_regist = rh01_regist
                    and r48_anousu = rh02_anousu and r48_mesusu = rh02_mesusu
            $whereBase
            UNION
            $selectBase
                inner join pessoal.gerfs13 on r35_regist = rh01_regist
                    and r35_anousu = rh02_anousu and r35_mesusu = rh02_mesusu
            $whereBase
            UNION
            $selectBase
                inner join pessoal.gerfres on r20_regist = rh01_regist
                    and r20_anousu = rh02_anousu and r20_mesusu = rh02_mesusu
            $whereBase order by cgm;
SQL;

        $rsRhPessoalMov = db_query($sql);

        if (!$rsRhPessoalMov) {
            throw new DBException("Erro ao buscar os CGM's com matrícula ativa na instituição.");
        }

        if (pg_num_rows($rsRhPessoalMov) == 0) {
            $mensagem = "Nenhum CGM, com matrícula ativa na instituição, encontrado na competência informada.";
            throw new DBException($mensagem);
        }

        return db_utils::makeCollectionFromRecord($rsRhPessoalMov, function ($retorno) {
            return $retorno->cgm;
        });
    }

    /**
     * @param int $codigoCgm
     * @return string
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     */
    public static function buscarCNPJEmpregador($codigoCgm)
    {
        if (empty($codigoCgm)) {
            throw new ParameterException('CGM não informado.');
        }

        $daoCgm = new cl_cgm();
        $sqlCgm = $daoCgm->sql_query_file($codigoCgm, 'z01_cgccpf as cnpj');
        $rsCgm = db_query($sqlCgm);

        if (!$rsCgm) {
            throw new DBException('Erro ao buscar o CNPJ do CGM.');
        }

        if (pg_num_rows($rsCgm) == 0) {
            throw new DBException("CGM {$codigoCgm} não encontrado.");
        }

        $cnpj = db_utils::fieldsMemory($rsCgm, 0)->cnpj;

        if (empty($cnpj)) {
            throw new BusinessException("CNPJ não informado para o CGM {$codigoCgm}.");
        }

        return $cnpj;
    }

    /**
     * @param $codigoRubrica
     * @return string
     */
    public static function buscarIdentificadorRubrica($codigoRubrica)
    {
        $instituicao = db_getsession('DB_instit');
        $sql = "SELECT * FROM fc_rubrica_esocial('{$codigoRubrica}', {$instituicao})";
        $rs = db_query($sql);

        if (!$rs || pg_num_rows($rs) == 0) {
            throw new Exception('Não foi possível consultar os preenchimentos da rubrica {$item->codigo}.');
        }

        return pg_fetch_result($rs, 0, 'identificador');
    }

    /**
     * @param DBCompetencia $dbCompetencia
     * @return string
     */
    public static function buscarRubricaDiferencaReajusteCompetencia(DBCompetencia $dbCompetencia)
    {
        $rubricaDiferenca = null;
        $instituicao = InstituicaoRepository::getInstituicaoSessao()->getCodigo();

        $daoRubricaDiferenca = new \cl_cfpess();
        $sqlRubricaDiferenca = $daoRubricaDiferenca->sql_query_file(
            $dbCompetencia->getAno(),
            $dbCompetencia->getMes(),
            $instituicao,
            'r11_rubricadifsalario'
        );
        $rsRubricaDiferenca = db_query($sqlRubricaDiferenca);

        $rubricaDiferenca = db_utils::fieldsMemory($rsRubricaDiferenca, 0)->r11_rubricadifsalario;

        return $rubricaDiferenca;
    }
}
