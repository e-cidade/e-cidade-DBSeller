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
 * Class tceCadastroFuncionarios
 */
class tceCadastroFuncionarios extends tceEstruturaBasica
{
    const NOME_ARQUIVO = 'TCE_4820.TXT';

    const CODIGO_ARQUIVO = 35;

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
        db_criatermometro('terTCE4820', 'Arquivo TCE4820...', 'blue', 1);

        $this->oTxtLayout->setByLineOfDBUtils($this->cabecalhoPadrao($this->iInstit, $this->sDataIni, $this->sDataFim, $this->sCodRemessa), 1);
        $rsFuncionarios = db_query($this->sqlCadastroFuncionarios($this->sDataFim, $this->sDataIni));
        $iNumRows = pg_num_rows($rsFuncionarios);
        $iTotalRegistros = 0;
        $iQuant = 0;

        for ($i = 0; $i < $iNumRows; $i++) {
            $iNew = intval($i * 100 / $iNumRows);

            if ($iNew > $iQuant) {
                $iQuant = $iNew;
                db_atutermometro($i, $iNumRows, "terTCE4820");
            }

            $oFuncionario = db_utils::fieldsMemory($rsFuncionarios, $i);
            $oFuncionario->salarioinicialcargo = $this->buscarSalarioInicial($oFuncionario->codigoregistrofuncionario);

            if ($oFuncionario->regimejuridico == 'O') {
                $oFuncionario->observacoes = 'Extra Quadro';
            }

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
    function sqlCadastroFuncionarios($sDatafim, $sDataini)
    {
        list ($iAnoUsuFim, $iMesUsuFim, $iDiaUsuFim) = explode("-", $sDatafim);
        list ($iAnoUsuIni, $iMesUsuIni, $iDiaUsuIni) = explode("-", $sDataini);

        $sql = <<<SQL
        select
              *,
              CASE
                  WHEN observacoes_original = '' and naturezacargo = 'O' THEN 'outro vinculo'
                  WHEN observacoes_original != '' THEN observacoes_original
                  ELSE ''
                END AS observacoes
            from (
              select (cast(rh02_anousu::varchar||'-'||rh02_mesusu::varchar||'-01'::varchar as date))  as dataatualizacao,
                  rh01_regist                   as codigoregistrofuncionario,
                  rh01_regist                   as matricula,
                  z01_cgccpf                    as cpf,
                  z01_nome                      as nomefuncionario,
                  rh01_nasc                     as datanacimento,
                  rh01_admiss                   as dataadmissao,
                  rh01_admiss                   as datavantagem,
                  '00'                          as matricula_vinculo,
                  coalesce(to_char(rh05_recis, 'DDMMYYYY')) as datademissao,
                  rh01_funcao                   as codigocargofuncionario,
                  rh37_descr                    as nomecargofuncionario,
                  rh02_lota                     as codigosetor,
                  r70_descr                     as nomesetor,
                  case
                    when upper(rh01_sexo) = 'M'
                      then 1
                    else 2
                  end                           as sexofuncionario,
                  cast( cast( replace(trim( substr(db_fxxx(rh01_regist,rh02_anousu,rh02_mesusu,rh02_instit),46,11)),',','.') as numeric) as integer) as quantidadedependentesfinsirrf,
                  case
                    when upper(rh30_vinculo) = 'A' then '01'
                    when upper(rh30_vinculo) = 'I' then '02'
                    when upper(rh30_vinculo) = 'P' then '03'
                    else '99'
                  end as situacaofuncionario,
                  case
                    when rh30_regime = 1 then 'E'
                    when rh30_regime = 2 then 'C'
                    else 'O'
                  end as regimejuridico,
                  case
                    when rh71_sequencial = 1 then 'E'
                    when rh71_sequencial = 2 then 'C'
                    when rh71_sequencial = 3 then 'T'
                    when rh71_sequencial = 5 then 'A'
                    when rh71_sequencial = 6 then 'S'
                    else 'O'
                  end as naturezacargo,
SQL;
        $sqlTabelaRegimePrev = " (
            select coalesce
            (
                (select distinct inss.rh129_regimeprevidencia from
                    (
                        SELECT distinct rh129_regimeprevidencia
                        FROM rhpessoalmov
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
                        ON r33_instit = rh02_instit AND r33_anousu = rh02_anousu AND r33_mesusu = rh02_mesusu AND rh02_tbprev + 2 = r33_codtab
                        WHERE
                        rh02_regist = rhpessoal.rh01_regist
                        AND rh02_anousu = {$iAnoUsuIni}
                        AND rh02_mesusu between {$iMesUsuIni} and {$iMesUsuFim}
                    ) as inss limit 1
                )
            , 01)
        )
        ";
        $sql .= <<<SQL
                  to_char({$sqlTabelaRegimePrev}, 'FM00') as regimeprevidenciario,
                  z01_ident                     as registrogeralindentificacao,
                  rh37_cbo                      as cbo,
                  rh16_pis                      as nitpispasep,
                  rh02_tpcont                   as categoriatrabalhador,
                  trim(z01_ender)               as endereco,
                  z01_munic                     as cidade,
                  z01_uf                        as unidadeferderacaouf,
                  z01_cep                       as cep,
                  case when rh02_tbprev = 0 then 'Servidor sem previdencia.' else '' end as observacoes_original,
                  case
                    when rh02_folha = 'D' then rh02_horasdiarias
                    when rh02_folha = 'M' then rh02_hrsmen
                    when rh02_folha = 'S' then rh02_hrssem
                    else null
                  end as carga_horaria,
                  case
                    when rh02_folha = 'Q' then null
                    else rh02_folha
                  end as tipo_carga_horaria,
                  rh02_folha                    as tipo_carga_horaria,
                  coalesce((select rh261_credencial from rhcedencia where rhcedencia.rh261_regist = rh01_regist order by rh261_sequencial desc limit 1), 'X') as tipo_cedencia,
                  coalesce((select rh261_onus from rhcedencia where rhcedencia.rh261_regist = rh01_regist order by rh261_sequencial desc limit 1), 'X') as onus_origem,
                  coalesce((select case when rh261_ressarcimento = 'S' then 'S' else 'N' end as ressarcimento from rhcedencia where rhcedencia.rh261_regist = rh01_regist order by rh261_sequencial desc limit 1), 'X') as ressarcimento,
                  coalesce((select rh261_datamovimentacao from rhcedencia where rhcedencia.rh261_regist = rh01_regist order by rh261_sequencial desc limit 1), null) as data_movimentacao_cedencia,
                  case
                    when rh02_cnpjcedencia = '0' then ''
                    else rh02_cnpjcedencia
                  end as cnpj_origem_destino,
                  coalesce(LPAD(rhlotaexe.rh26_orgao::text, 2, '0'), '00') || coalesce(LPAD(rhlotaexe.rh26_unidade::text, 2, '0'), '00') AS orgao_unidade
            from rhpessoal
                  inner join cgm              on cgm.z01_numcgm                   = rhpessoal.rh01_numcgm
                  inner join rhpessoalmov     on rhpessoalmov.rh02_regist         = rhpessoal.rh01_regist
                                            and rhpessoalmov.rh02_anousu         = {$iAnoUsuFim}
                                            and rhpessoalmov.rh02_mesusu         = {$iMesUsuFim}
                                            and rhpessoalmov.rh02_instit         in ({$this->sInstituicoes})
                  inner join rhfuncao         on rhfuncao.rh37_funcao             = rhpessoalmov.rh02_funcao
                                            and rhfuncao.rh37_instit             = rhpessoalmov.rh02_instit
                  left  join rhpesdoc         on rhpesdoc.rh16_regist             = rhpessoal.rh01_regist
                  left  join cfpess           on cfpess.r11_anousu                = rhpessoalmov.rh02_anousu
                                            and cfpess.r11_mesusu                = rhpessoalmov.rh02_mesusu
                                            and cfpess.r11_tbprev                = {$sqlTabelaRegimePrev}
                                            and cfpess.r11_instit                = rhpessoalmov.rh02_instit
                  inner join rhlota           on rhlota.r70_codigo                = rhpessoalmov.rh02_lota
                                            and rhlota.r70_instit                = rh02_instit
                  inner join rhregime         on rhregime.rh30_codreg             = rhpessoalmov.rh02_codreg
                                            and rhregime.rh30_instit             = rh02_instit
                  left  join rhnaturezaregime on rhnaturezaregime.rh71_sequencial = rhregime.rh30_naturezaregime
                  left  join rhpesrescisao    on rhpesrescisao.rh05_seqpes        = rhpessoalmov.rh02_seqpes
                  left  join rhlotaexe        on rhlotaexe.rh26_codigo            = rhlota.r70_codigo AND rh26_anousu = {$iAnoUsuFim}
            where rh02_instit in ({$this->sInstituicoes})
            and ( rhpesrescisao.rh05_recis is null
              or rhpesrescisao.rh05_recis >= '{$iAnoUsuIni}-{$iMesUsuIni}-{$iDiaUsuIni}'
              or rh02_regist in (select distinct
                                        r20_regist
                                  from gerfres
                                  where (r20_anousu >= {$iAnoUsuIni} and r20_mesusu >= {$iMesUsuIni} )
                                    and (r20_anousu <= {$iAnoUsuFim} and r20_mesusu <= {$iMesUsuFim} )
                                )
              or
                exists (
                      select 1
                        from gerffer
                      where r31_regist = rh01_regist
                        and (r31_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r31_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                        and r31_instit in ({$this->sInstituicoes})
                      union
                      select 1
                        from gerfadi
                      where r22_regist = rh01_regist
                        and (r22_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r22_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                        and r22_instit in ({$this->sInstituicoes})
                      union
                      select 1
                        from gerfcom
                      where r48_regist = rh01_regist
                        and (r48_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r48_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                        and r48_instit in ({$this->sInstituicoes})
                      union
                      select 1
                        from gerfres
                      where r20_regist = rh01_regist
                        and (r20_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r20_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                        and r20_instit in ({$this->sInstituicoes})
                      union
                      select 1
                        from gerfs13
                      where r35_regist = rh01_regist
                        and (r35_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r35_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                        and r35_instit in ({$this->sInstituicoes})
                      union
                      select 1
                        from gerfsal
                      where r14_regist = rh01_regist
                        and (r14_anousu between {$iAnoUsuIni} and {$iAnoUsuFim} and r14_mesusu between {$iMesUsuIni} and {$iMesUsuFim})
                        and r14_instit in ({$this->sInstituicoes})
                      )
              )
          order by rh01_regist
        ) as x
SQL;
        return $sql;
    }

    /**
     * @param $matricula
     * @return string
     * @throws DBException
     */
    private function buscarSalarioInicial($matricula)
    {
        $instituicao = db_getsession('DB_instit');
        $sql = "select rh02_anousu as ano, rh02_mesusu as mes, to_char(rh02_anousu,'FM0000')||to_char(rh02_mesusu,'FM00') as anomesusu ";
        $sql .= "from rhpessoalmov left join  rhpespadrao     on rhpessoalmov.rh02_seqpes    = rhpespadrao.rh03_seqpes ";

        $sql .= " INNER JOIN rhregime ";
        $sql .= " ON rh30_codreg = rh02_codreg AND rh30_instit = rh02_instit ";
        $sql .= " INNER JOIN padroes ";
        $sql .= " ON r02_codigo = rh03_padrao AND r02_regime = rh30_regime ";
        $sql .= "
            AND R02_ANOUSU = RH02_ANOUSU
            AND R02_MESUSU = RH02_MESUSU
            AND R02_INSTIT = RH02_INSTIT
        ";

        $sql .= "
            where
            rh02_regist = {$matricula} and
            rh02_instit = {$instituicao}
            and (
                r02_valor != 0
                or rh02_salari != 0
            ) limit 1
         ";

        $rs = db_query($sql);

        if (!$rs || pg_num_rows($rs) == 0) {

            $dao = new cl_rhpessoalmov();
            $campos = "rh02_anousu as ano, rh02_mesusu as mes, to_char(rh02_anousu,'FM0000')||to_char(rh02_mesusu,'FM00') as anomesusu ";
            $where = "rh02_regist = {$matricula} limit 1";
            $sql = $dao->sql_query_file(null, $instituicao, $campos, null, $where);

            $rs = db_query($sql);
        }

        if(!$rs || pg_num_rows($rs) == 0) {
            throw new DBException("Erro ao buscar a competência inicial do servidor {$matricula}.");
        }

        $retornoCompetenciaInicial = db_utils::fieldsMemory($rs, 0);

        $dao = new cl_cfpess();
        $instituicao = db_getsession('DB_instit');
        $campos = "substr(r11_subpes,1,4) as anousuimplan, substr(r11_subpes,6,2) as mesusuimplan, replace(r11_subpes,'/','') as anomesimplan ";
        $ordem  = "r11_anousu desc, r11_mesusu desc limit 1";
        $sql    = $dao->sql_query_file(null, null, $instituicao, $campos, $ordem, null);
        $rs     = db_query($sql);

        if(!$rs || pg_num_rows($rs) == 0) {
            throw new DBException("Erro ao buscar o ano/mes de implantação da folha.");
        }

        $retornoCfpess = db_utils::fieldsMemory($rs, 0);

        $sqlVariaveisFxx = "select *";
        $sqlVariaveisFxx .= "  from fc_variaveis_matricula(";
        $sqlVariaveisFxx .= "    {$matricula},";

        /* Usa o ano/mes da cfpess como teto de informacao, se ano da rhpessoalmov for menor que o ano da cfpess, usa, esse */

        if ( $retornoCompetenciaInicial->anomesusu > $retornoCfpess->anomesimplan ){
           $sqlVariaveisFxx .= "    {$retornoCompetenciaInicial->ano},";
           $sqlVariaveisFxx .= "    {$retornoCompetenciaInicial->mes},";
        }else{
           $sqlVariaveisFxx .= "    {$retornoCfpess->anousuimplan},";
           $sqlVariaveisFxx .= "    {$retornoCfpess->mesusuimplan},";
        }

        $sqlVariaveisFxx .= "    {$instituicao}";
        $sqlVariaveisFxx .= "  )";

        $rsVariaveisFxx = db_query($sqlVariaveisFxx);

        if(!$matricula) {
            throw new DBException("Erro ao buscar o salário inicial do servidor {$matricula}.");
        }

        $retornoVariaveis = db_utils::fieldsMemory($rsVariaveisFxx, 0);

        if(empty($retornoVariaveis->f010)) {
            return str_repeat('0', 17);
        }

        if(!strpos($retornoVariaveis->f010, '.')) {
            return str_pad($retornoVariaveis->f010 . '00', 17, '0', STR_PAD_LEFT);
        }

        return str_pad(str_replace('.', '', $retornoVariaveis->f010), 17, '0', STR_PAD_LEFT);
    }
}
