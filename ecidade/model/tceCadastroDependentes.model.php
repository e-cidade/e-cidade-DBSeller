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
require_once(modification('model/tceEstruturaBasica.php'));

/**
 * Class tceCadastroDependentes
 */
class tceCadastroDependentes extends tceEstruturaBasica
{
    const NOME_ARQUIVO = 'DEPENDENTE.TXT';

    const CODIGO_ARQUIVO = 309;

    public $iInstit = "";
    public $sInstituicoes = "";
    public $sDataIni = "";
    public $sDataFim = "";
    public $sCodRemessa = "";

    private $oLeiaute = null;

    /**
     * tceCadastroFuncionarios constructor.
     * @param $iInstit
     * @param $sCodRemessa
     * @param $sDataIni
     * @param $sDataFim
     * @param $oData
     * @param null $oLeiaute
     * @param $sInstituicoes
     * @throws Exception
     */
    function __construct($iInstit, $sCodRemessa, $sDataIni, $sDataFim, $oData, $oLeiaute = null, $sInstituicoes)
    {
        try {
            parent::__construct(self::CODIGO_ARQUIVO, self::NOME_ARQUIVO);
        } catch (Exception $e) {
            throw $e;
        }

        $this->iInstit = $iInstit;
        $this->sInstituicoes = $sInstituicoes;
        $this->sDataIni = $sDataIni;
        $this->sDataFim = $sDataFim;
        $this->sCodRemessa = $sCodRemessa;

        if ($oLeiaute != null) {
            $this->oLeiaute = $oLeiaute;
        }
    }

    /**
     * @return string
     */
    function getNomeArquivo()
    {
        return self::NOME_ARQUIVO;
    }

    /**
     * @throws DBException
     */
    function geraArquivo()
    {
        db_criatermometro('terTCEDependente', 'Arquivo Dependente...', 'blue', 1);

        $this->oTxtLayout->setByLineOfDBUtils($this->cabecalhoPadrao($this->iInstit, $this->sDataIni, $this->sDataFim, $this->sCodRemessa), 1);
        $rsFuncionarios = db_query($this->sqlCadastroDependente($this->sDataFim, $this->sDataIni));
        $iNumRows = pg_num_rows($rsFuncionarios);
        $iTotalRegistros = 0;
        $iQuant = 0;

        for ($i = 0; $i < $iNumRows; $i++) {
            $iNew = intval($i * 100 / $iNumRows);

            if ($iNew > $iQuant) {
                $iQuant = $iNew;
                db_atutermometro($i, $iNumRows, "terTCEDependente");
            }

            $oFuncionario = db_utils::fieldsMemory($rsFuncionarios, $i);

            $this->oTxtLayout->setByLineOfDBUtils($oFuncionario, 3);
            $iTotalRegistros++;
        }

        $this->oTxtLayout->setByLineOfDBUtils($this->rodapePadrao($iTotalRegistros), 5);
        unset($rsFuncionarios);
    }

    /**
     * @param $sDatafim
     * @param $sDataini
     * @return string
     */
    function sqlCadastroDependente($sDatafim, $sDataini)
    {
        list ($iAnoUsuFim, $iMesUsuFim, $iDiaUsuFim) = explode("-", $sDatafim);
        list ($iAnoUsuIni, $iMesUsuIni, $iDiaUsuIni) = explode("-", $sDataini);

        $sql = <<<SQL
            select
                rh01_regist || '00' as matricula,
                rh31_nome as nomedependente,
                dp01_cpf as cpf_dependente,
                rh31_dtnasc as nascimento_dependente,
                case
                    when rh31_tipoparentesco in ('01') then '01' --conjuje
                    when rh31_tipoparentesco in ('03','04') then '02' --filho
                    when rh31_tipoparentesco in ('02') then '03' --companheiro
                    when rh31_tipoparentesco in ('') then '04' -- enteado
                    when rh31_tipoparentesco in ('11') then '05' -- filho invalido
                    when ((rh31_tipoparentesco in ('09') and dp01_sexo = 'F') OR (rh31_gparen = 'M')) then '06' -- mae
                    when ((rh31_tipoparentesco in ('09') and dp01_sexo = 'M') OR (rh31_gparen = 'P')) then '07' -- pai
                    when rh31_tipoparentesco in ('10') then '08' -- menor sob guarda
                    when rh31_tipoparentesco in ('06','07') then '09' -- tutelado
                    else '99' -- outros

                end as parentesco_dependente,
                '' as observacao
            from rhpessoal
                inner join cgm on cgm.z01_numcgm = rhpessoal.rh01_numcgm
                inner join rhpessoalmov on rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
                    and rhpessoalmov.rh02_anousu = {$iAnoUsuFim}
                    and rhpessoalmov.rh02_mesusu = {$iMesUsuFim}
                    and rhpessoalmov.rh02_instit in ({$this->sInstituicoes})
                inner join rhfuncao on rhfuncao.rh37_funcao = rhpessoalmov.rh02_funcao
                    and rhfuncao.rh37_instit = rhpessoalmov.rh02_instit
                left join rhpesdoc on rhpesdoc.rh16_regist = rhpessoal.rh01_regist
                left join cfpess on cfpess.r11_anousu = rhpessoalmov.rh02_anousu
                    and cfpess.r11_mesusu = rhpessoalmov.rh02_mesusu
                    and cfpess.r11_tbprev = (
                            select coalesce
                            (
                                (select distinct inss.rh129_regimeprevidencia
                                    from (
                                        SELECT distinct rh129_regimeprevidencia
                                        FROM
                                            rhpessoalmov
                                            LEFT JOIN
                                            (
                                                SELECT  distinct r33_codtab
                                                    ,r33_anousu
                                                    ,r33_mesusu
                                                    ,r33_instit
                                                    ,rh129_regimeprevidencia
                                                FROM inssirf
                                                INNER JOIN regimeprevidenciainssirf
                                                ON rh129_codigo = r33_codigo AND r33_instit = rh129_instit
                                            ) AS x
                                            ON r33_instit = rh02_instit
                                            AND r33_anousu = rh02_anousu
                                            AND r33_mesusu = rh02_mesusu
                                            AND rh02_tbprev + 2 = r33_codtab
                                        WHERE
                                            rh02_regist = rhpessoal.rh01_regist
                                            AND rh02_anousu = {$iAnoUsuIni}
                                            AND rh02_mesusu between {$iMesUsuIni} and {$iMesUsuFim}
                                    ) as inss limit 1
                                )
                            , 01)
                        )
                    and cfpess.r11_instit = rhpessoalmov.rh02_instit
                inner join rhlota on rhlota.r70_codigo = rhpessoalmov.rh02_lota
                    and rhlota.r70_instit = rh02_instit
                inner join rhregime on rhregime.rh30_codreg = rhpessoalmov.rh02_codreg
                    and rhregime.rh30_instit = rh02_instit
                left join rhnaturezaregime on rhnaturezaregime.rh71_sequencial = rhregime.rh30_naturezaregime
                left join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                left join rhlotaexe on rhlotaexe.rh26_codigo = rhlota.r70_codigo AND rh26_anousu = {$iAnoUsuFim}
                inner join rhdepend on rhpessoal.rh01_regist = rh31_regist
                left join rhdependeplug on dp01_rhdepend = rh31_codigo
           where rh02_instit in ({$this->sInstituicoes})
           and ( rhpesrescisao.rh05_recis is null
            or rhpesrescisao.rh05_recis >= '{$iAnoUsuIni}-{$iMesUsuIni}-{$iDiaUsuIni}'
            or rh02_regist in (
                select distinct r20_regist
                from gerfres
                where (r20_anousu >= {$iAnoUsuIni} and r20_mesusu >= {$iMesUsuIni} )
                    and (r20_anousu <= {$iAnoUsuFim} and r20_mesusu <= {$iMesUsuFim} )
            )
            or exists (
                select
                    1
                from
                    gerffer
                where
                    r31_regist = rh01_regist
                    and (r31_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r31_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                    and r31_instit in ({$this->sInstituicoes})
                union
                select
                    1
                from
                    gerfadi
                where
                    r22_regist = rh01_regist
                    and (r22_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r22_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                    and r22_instit in ({$this->sInstituicoes})
                union
                select
                    1
                from
                    gerfcom
                where
                    r48_regist = rh01_regist
                    and (r48_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r48_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                    and r48_instit in ({$this->sInstituicoes})
                union
                select
                    1
                from
                    gerfres
                where
                    r20_regist = rh01_regist
                    and (r20_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r20_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                    and r20_instit in ({$this->sInstituicoes})
                union
                select
                    1
                from
                    gerfs13
                where
                    r35_regist = rh01_regist
                    and (r35_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r35_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                    and r35_instit in ({$this->sInstituicoes})
                union
                select
                    1
                from
                    gerfsal
                where
                    r14_regist = rh01_regist
                    and (r14_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r14_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                    and r14_instit in ({$this->sInstituicoes})
            )
        )
        order by rh01_regist
SQL;

        return $sql;
    }
}
