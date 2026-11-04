<?php
/**
 * Classe para  apresentação dos dados do civitas no sistema
 *
 * @author   Augusto  augusto.oliveira@dbseller.com.br
 * @package  Cadastro
 * @version  $Revision: 1.0
 */
class DadosProcessamentoCivitas
{

    /**
     * @var $sMatricula
     */
    private $sMatricula;

    /**
     * @var null
     */
    private $sSchema;

    /**
     * @var null|string
     */
    private $anoAtual;

    /**
     * RecadastramentoConsultaIptuHelper constructor.
     * @param $sMatricula
     * @param null $sSchema
     */
    public function __construct($sMatricula, $sSchema = null)
    {
        $this->sMatricula = $sMatricula;
        $this->sSchema = $sSchema;
        $this->anoAtual = db_getsession('DB_anousu');
    }

    /**
     * @return bool|resource|void
     */
    public function getCalculoIptu()
    {
        if (empty($this->sMatricula)) {
            return;
        }
        db_query("select fc_putsession('DB_Gerafinanc','0')");
        $sSqlCalculo = "select fc_calculoiptu({$this->sMatricula}::integer,{$this->anoAtual}::integer,
                            true::boolean,
                            false::boolean,
                            false::boolean,
                            false::boolean,
                            false::boolean,
                            array['0','0','0']
                       )";

        $rsCalculo = db_query($sSqlCalculo);
        return $rsCalculo;

    }


    /**
     * @return stdClass
     */
    public function getEnderecoTestada()
    {
        $sqlTestada = "SELECT  (
                                  j88_sigla||' '||j14_nome
                                  ||' Bairro: '||j13_descr
                             ) AS endereco_completo
                              FROM testada
                        INNER JOIN iptubase ON j01_idbql = j36_idbql
                        INNER JOIN lote ON j34_idbql = j01_idbql
                        INNER JOIN bairro ON j13_codi = j34_bairro
                        INNER JOIN ruas ON j14_codigo = j36_codigo
                        INNER JOIN ruastipo ON j88_codigo = j14_tipo
                             WHERE j01_matric = {$this->sMatricula}";

        $rsTestada = db_query($sqlTestada);
        $dadosTestada = db_utils::fieldsMemory($rsTestada, 0);

        return $dadosTestada;
    }


    /**
     * @return array
     */
    public function getCarateristicasdoLote()
    {


        $sSqlCaractetiscasLote = "select j32_grupo, j32_descr, caracteristicas.*
                                  from cargrup 
                                  left join ( select caracter.*
                                                from caracter
                                                    inner join carlote on j35_caract = j31_codigo
                                              where j35_idbql = {$this->getMatricula()->j01_idbql}) as caracteristicas on j31_grupo = j32_grupo
                                 where j32_tipo = 'L'                                  
                                order by j32_grupo";

        $rsCaracteristica = db_query($sSqlCaractetiscasLote);
        $iTotalLinhas = pg_num_rows($rsCaracteristica);
        $caracteristicas = array();

        for ($contador = 0; $contador < $iTotalLinhas; $contador++) {

            $dadosCaracteristica = db_utils::fieldsMemory($rsCaracteristica, $contador);
            $caracteristicas[$dadosCaracteristica->j32_grupo] = $dadosCaracteristica;
        }

        return $caracteristicas;

    }

    /**
     * @return null|stdClass
     */
    public function getLote()
    {
        $sqlLote = "SELECT
                            j34_setor as setor
                            ,j34_area as area
                            ,j06_quadraloc as quadra_localizacao
                            ,j34_quadra as quadra
                            ,j06_lote as lote_localizacao
                            ,j34_lote as lote
                            ,(
                                j88_sigla||' '||j14_nome
                                ||'Bairro: '||j13_descr
                             ) as endereco_completo
                            FROM iptubase
                            INNER JOIN lote on j34_idbql = j01_idbql
                            LEFT JOIN bairro ON j13_codi = j34_bairro
                            LEFT JOIN lotedist ON j54_idbql = j34_idbql
                            LEFT JOIN ruas ON j14_codigo = j54_codigo
                            LEFT JOIN ruastipo ON j88_codigo = j14_tipo
                            LEFT JOIN loteloc ON j06_idbql = j01_idbql
                            WHERE j01_matric = {$this->sMatricula}";


        $rsLote = db_query($sqlLote);
        $totalLinhasLote = pg_num_rows($rsLote);

        if (!$rsLote || $totalLinhasLote == 0) {
            return null;
        } else {
            return $dadosLote = db_utils::fieldsMemory($rsLote, 0);
        }
    }

    public function getMatricula()
    {
        $oDaoMatricula = new cl_iptubase();
        $sSqlMatricula = $oDaoMatricula->sql_query_file($this->sMatricula);
        $rsMatricula = db_query($sSqlMatricula);

        return db_utils::fieldsMemory($rsMatricula, 0);;

    }


    /**
     * Retorna calculo a exibir
     *
     * @return stdClass
     */

    public function getCaculo()
    {

        $sSqlDadosCalculo = " select distinct iptucalc.j23_anousu ,iptucalc.j23_matric, iptucalc.j23_testad, iptucalc.j23_arealo ";
        $sSqlDadosCalculo .= "        ,iptucalc.j23_areafr, iptucalc.j23_areaed, iptucalc.j23_m2terr, iptucalc.j23_vlrter ";
        $sSqlDadosCalculo .= "        ,iptucalc.j23_aliq, iptucalc.j23_vlrisen ";
        $sSqlDadosCalculo .= "        ,sum(j22_valor) as j22_valor";
        $sSqlDadosCalculo .= "   from iptucalc ";
        $sSqlDadosCalculo .= "   left outer join iptucale on j22_matric = j23_matric and j22_anousu = j23_anousu ";
        $sSqlDadosCalculo .= "  where j23_matric = {$this->sMatricula} and j23_anousu = {$this->anoAtual} ";
        $sSqlDadosCalculo .= "  group by iptucalc.j23_anousu, iptucalc.j23_matric, iptucalc.j23_testad, iptucalc.j23_arealo, ";
        $sSqlDadosCalculo .= "           iptucalc.j23_areafr, iptucalc.j23_areaed, iptucalc.j23_m2terr, iptucalc.j23_vlrter,  ";
        $sSqlDadosCalculo .= "           iptucalc.j23_aliq, iptucalc.j23_vlrisen ";
        $sSqlDadosCalculo .= "  order by iptucalc.j23_anousu desc ";


        $rsCalculo = db_query($sSqlDadosCalculo);

        $oDadosCalculo = db_utils::fieldsMemory($rsCalculo, 0);

        return $oDadosCalculo;
    }

    /**
     * Retorna todas as caracteristicas do Lote
     * @param $idbql
     * @return array
     */
    public function getCarateristicasdosImoveisDaMatricula($idConstr = null)
    {


        $sSqlCaractetiscasLote = "select j32_grupo, j32_descr, caracteristicas.* ";
        $sSqlCaractetiscasLote .= "  from cargrup                                 ";
        $sSqlCaractetiscasLote .= "  left join ( select caracter.*, j48_idcons      ";
        $sSqlCaractetiscasLote .= "                from caracter                      ";
        $sSqlCaractetiscasLote .= "                    inner join carconstr on j48_caract = j31_codigo";
        $sSqlCaractetiscasLote .= "                                        and j48_matric = {$this->sMatricula}";
        if (!empty($idConstr)) {
            $sSqlCaractetiscasLote .= " and j48_idcons = {$idConstr}";
        }
        $sSqlCaractetiscasLote .= "             ) as caracteristicas on j31_grupo = j32_grupo";
        $sSqlCaractetiscasLote .= "  where j32_tipo = 'C'                                  ";
        $sSqlCaractetiscasLote .= " order by j48_idcons, j32_grupo";

        $rsCaracteristica = db_query($sSqlCaractetiscasLote);
        $iTotalLinhas = pg_num_rows($rsCaracteristica);
        $caracteristicas = array();

        for ($contador = 0; $contador < $iTotalLinhas; $contador++) {

            $dadosCaracteristica = db_utils::fieldsMemory($rsCaracteristica, $contador);
            if (empty($dadosCaracteristica->j48_idcons)) {
                $dadosCaracteristica->j48_idcons = $idConstr;
            }
            if (empty($caracteristicas[$dadosCaracteristica->j48_idcons])) {
                $caracteristicas[$dadosCaracteristica->j48_idcons] = array();
            }
            $caracteristicas[$dadosCaracteristica->j48_idcons][$dadosCaracteristica->j32_grupo] = $dadosCaracteristica;
        }
        if (!empty($idConstr)) {
            return $caracteristicas[$idConstr];
        }
        return $caracteristicas;
    }

    /**
     * @return stdClass
     */
    public function getIptuPadraoValores()
    {

        $sSqlIptucalcpadrao = " SELECT j10_vlrter , j11_vlrcons FROM iptucalcpadrao  INNER JOIN iptucalcpadraoconstr 
                          on iptucalcpadraoconstr.j11_iptucalcpadrao = iptucalcpadrao.j10_sequencial
                            where  j10_matric  = {$this->sMatricula} AND j10_anousu = {$this->anoAtual} ";
        $rsIptucalcpadrao = db_query($sSqlIptucalcpadrao);

        $oIptucalcpadrao = db_utils::fieldsMemory($rsIptucalcpadrao, 0);

        return $oIptucalcpadrao;

    }


    /**
     * @return bool|resource
     */
    public function getTaxas()
    {
        $sql2 = "select k02_codigo, k02_descr, j17_codhis, j17_descr, j21_valor, ";
        $sql2 .= "       case ";
        $sql2 .= "         when iptucalhconf.j89_codhis is not null then ";
        $sql2 .= "           (select sum(x.j21_valor) ";
        $sql2 .= "					    from iptucalv x ";
        $sql2 .= "						 where x.j21_anousu = iptucalv.j21_anousu ";
        $sql2 .= "						   and x.j21_matric = iptucalv.j21_matric ";
        $sql2 .= "							 and x.j21_receit = iptucalv.j21_receit ";
        $sql2 .= "							 and x.j21_codhis = iptucalhconf.j89_codhis) ";
        $sql2 .= "         else 0  ";
        $sql2 .= "       end as j21_valorisen ";
        $sql2 .= "  from iptucalv ";
        $sql2 .= "       inner join iptucalh        on iptucalh.j17_codhis        = j21_codhis ";
        $sql2 .= "       left  join iptucalhconf    on iptucalhconf.j89_codhispai = j21_codhis ";
        $sql2 .= "       inner join tabrec          on tabrec.k02_codigo          = j21_receit ";
        $sql2 .= "       left  join iptucadtaxaexe  on iptucadtaxaexe.j08_tabrec  = j21_receit ";
        $sql2 .= "                                 and iptucadtaxaexe.j08_anousu  = {$this->anoAtual} ";
        $sql2 .= " where j21_matric = {$this->sMatricula} ";
        $sql2 .= "   and j21_anousu = {$this->anoAtual} ";
        $sql2 .= "   and j17_codhis not in (select j89_codhis from iptucalhconf) ";
        $sql2 .= " order by iptucalh.j17_codhis  ";

        $result2 = db_query($sql2);

        return $result2;

    }

    /**
     * @return bool|resource
     */
    public function getContrucao()
    {

        $sqlConstrucoes = "select distinct
                  j39_idcons as idcons,
                  j39_codigo as cod,
                  j39_area as area,
                  j39_ano as ano,
		              case
		                when j39_idprinc
		                  then 'Sim'
		                else 'Não'
		              end as principal,
		              j39_dtdemo as data_demolicacao,
                      j39_areap
                  ,j39_pavim AS pavimentos
                  ,j88_sigla||' '||j14_nome AS rua
                  ,j39_numero AS numero
                  ,j39_compl AS complemento
                  ,(     SELECT j13_descr
                          FROM iptubase
                    INNER JOIN lote ON j34_idbql = j01_idbql
                    INNER JOIN bairro ON j13_codi = j34_bairro
                         WHERE j01_matric = j39_matric
                  ) AS bairro
                  ,(
                    j88_sigla||' '||j14_nome 
                    ||(CASE WHEN j39_numero IS NOT NULL THEN ', '||j39_numero ELSE '' END) 
                    ||(CASE WHEN (j39_compl IS NOT NULL AND trim(j39_compl) <> '') THEN '/'||j39_compl ELSE '' END) 
                    ||' Bairro: '||(     SELECT j13_descr
                                           FROM iptubase
                                     INNER JOIN lote ON j34_idbql = j01_idbql
                                     INNER JOIN bairro ON j13_codi = j34_bairro
                                          WHERE j01_matric = j39_matric
                                   )
                  ) as endereco_completo
                  from iptuconstr
                  LEFT JOIN ruas ON j14_codigo = j39_codigo
                  LEFT JOIN ruastipo ON j88_codigo = j14_tipo
	              where j39_matric = {$this->sMatricula}
		        order by j39_idcons  ";

        $rsConstrucoes = db_query($sqlConstrucoes);

        return $rsConstrucoes;

    }

    public function getMatriculaNoLote()
    {
        $oDaoAtualizacaoIptuMatriculas = new \cl_atualizacaoiptuschemamatricula();
        $aMatriculasNoLote = $oDaoAtualizacaoIptuMatriculas->matriculaNoLoteDaImportacao($this->sSchema, $this->getMatricula()->j01_idbql);

        return $aMatriculasNoLote;
    }

    /**
     *
     * @param string $schema
     */
    public function defineSchema($schema = '')
    {
        if (!empty($schema)) {
            db_query("set search_path=public,{$schema}");
        } else {
            db_query("set search_path=public,cadastro");
        }
    }

}