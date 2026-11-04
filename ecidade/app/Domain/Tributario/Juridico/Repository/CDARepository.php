<?php

namespace App\Domain\Tributario\Juridico\Repository;

use Certidao;
use Illuminate\Support\Facades\DB;

class CDARepository
{

    private $observacao;
    private $cda;
    private $DB_instit;
    private $DB_datausu;
    private $DB_id_usuario;

    private $clcertid;
    private $clcertdiv;
    private $clcertter;
    private $clarrecad;
    private $clarreforo;
    private $clinicialcert;
    private $clacertid;
    private $clacertdiv;
    private $clacertter;
    private $clListacda;
    private $cl_desmembramentoinicialhistorico;

    public function __construct(
        $cda,
        $observacao,
        $DB_datausu,
        $DB_id_usuario,
        $DB_instit
    ) {
        $this->cda         = $cda;
        $this->observacao    = $observacao;
        $this->DB_datausu    = $DB_datausu;
        $this->DB_id_usuario = $DB_id_usuario;
        $this->DB_instit = $DB_instit;

        require_once(modification("dbforms/db_funcoes.php"));

        $this->cl_desmembramentoinicialhistorico = new \cl_desmembramentoinicialhistorico();
        $this->clarrecad     = new \cl_arrecad;
        $this->clcertid      = new \cl_certid;
        $this->clcertdiv     = new \cl_certdiv;
        $this->clcertter     = new \cl_certter;
        $this->clarreforo    = new \cl_arreforo;
        $this->clinicialcert = new \cl_inicialcert;
        $this->clacertid     = new \cl_acertid;
        $this->clacertdiv    = new \cl_acertdiv;
        $this->clacertter    = new \cl_acertter;
        $this->clListacda    = new \cl_listacda();
    }

    public function cancelaCDA()
    {
        $certidao = $this->cda;
        $oCertidao = new Certidao($certidao);
        $sqlerro = false;

        if ($oCertidao->isCobrancaExtrajudicial()) {
            return true;
        }

        $this->clinicialcert->sql_record(
            $this->clinicialcert->sql_query(
                null,
                null,
                "v51_inicial",
                null,
                " v50_situacao = 1 and v51_certidao = $certidao "
            )
        );

        if ($this->clinicialcert->numrows > 0) {
            return true;
        }


        $result_forotip = $this->clarreforo->sql_record(
            $this->clarreforo->sql_query_file(
                null,
                "distinct k00_numpre,k00_numpar,k00_tipo",
                null,
                "k00_certidao=$certidao"
            )
        );
        if ($this->clarreforo->numrows > 0) {
            $arreforo = pg_fetch_object($result_forotip);

            $k00_numpre = $arreforo->k00_numpre;
            $k00_numpar = $arreforo->k00_numpar;
            $k00_tipo   = $arreforo->k00_tipo;
        } else {
            return true;
        }

        $tipo = "";
        $result_certdiv = $this->clcertdiv->sql_record($this->clcertdiv->sql_query_deb(
            $certidao,
            null,
            "distinct certdiv.*, divida.*, certid.*, cgm.*, proced.* ",
            null,
            "certid.v13_certid = $certidao and divida.v01_instit = "
                . $this->DB_instit
                . " and certid.v13_instit = "
                . $this->DB_instit
        ));

        if ($this->clcertdiv->numrows > 0) {
            $tipo = "divida";
            $quantcertdiv = $this->clcertdiv->numrows;
        }

        $result_certter = $this->clcertter->sql_record(
            $this->clcertter->sql_query_deb(
                $certidao,
                null,
                "distinct *",
                null,
                "certid.v13_certid = $certidao and certid.v13_instit = "
                    . $this->DB_instit
                    . " and termo.v07_instit = "
                    . $this->DB_instit
            )
        );
        if ($this->clcertter->numrows > 0) {
            $tipo = "parc";
            $quantcertter = $this->clcertter->numrows;
        }

        if ($tipo == "") {
            return true;
        }

        $this->clacertid->v15_certid     = $certidao;
        $this->clacertid->v15_data       = date("Y-m-d", $this->DB_datausu);
        $this->clacertid->v15_hora       = db_hora();
        $this->clacertid->v15_usuario    = $this->DB_id_usuario;
        $this->clacertid->v15_instit     = $this->DB_instit;
        $this->clacertid->v15_observacao = $this->observacao;
        $parcial = 0;

        $this->clacertid->v15_parcial = "$parcial";
        $this->clacertid->incluir(null);
        $v15_codigo = $this->clacertid->v15_codigo;
        if ($this->clacertid->erro_status == 0) {
            $sqlerro = true;
        }

        if ($tipo == "divida") {
            for ($w = 0; $w < $quantcertdiv; $w++) {
                $ob_certdiv = pg_fetch_object($result_certdiv, $w);
                if ($sqlerro == false) {
                    $this->clacertdiv->v14_certid = $ob_certdiv->v14_certid;
                    $this->clacertdiv->v14_coddiv = $ob_certdiv->v14_coddiv;
                    $this->clacertdiv->v14_vlrcor = $ob_certdiv->v14_vlrcor;
                    $this->clacertdiv->v14_vlrhis = $ob_certdiv->v14_vlrhis;
                    $this->clacertdiv->v14_vlrjur = $ob_certdiv->v14_vlrjur;
                    $this->clacertdiv->v14_vlrmul = $ob_certdiv->v14_vlrmul;
                    $this->clacertdiv->v14_codacertid = $v15_codigo;
                    $this->clacertdiv->incluir($ob_certdiv->v14_certid, $ob_certdiv->v14_coddiv);
                    if ($this->clacertdiv->erro_status == 0) {
                        $sqlerro = true;
                    }
                }

                if ($sqlerro == false) {
                    $this->clcertdiv->v14_certid = $certidao;
                    $this->clcertdiv->v14_coddiv = $ob_certdiv->v01_coddiv;
                    $this->clcertdiv->excluir($certidao, $ob_certdiv->v01_coddiv);
                    if ($this->clcertdiv->erro_status == 0) {
                        $sqlerro = true;
                    }
                }
            }

            if ($sqlerro == false) {
                $this->clarrecad->k00_tipo = $k00_tipo;
                for ($arreforo = 0; $arreforo < pg_num_rows($result_forotip); $arreforo++) {
                    $resultforotip = pg_fetch_object($result_forotip, $arreforo);
                    $k00_numpre = $resultforotip->k00_numpre;
                    $k00_numpar = $resultforotip->k00_numpar;
                    $k00_tipo   = $resultforotip->k00_tipo;
                    $this->clarrecad->alterar_arrecad("k00_numpre=$k00_numpre and k00_numpar=$k00_numpar");
                    if ($this->clarrecad->erro_status == 0) {
                        $sqlerro = true;
                    }
                }
            }

            $this->clarreforo->excluir(null, "k00_certidao=$certidao");
            if ($this->clarreforo->erro_status == 0) {
                $sqlerro = true;
            }

            $this->clListacda->excluir(null, "v81_certid = $certidao");
            if ($this->clListacda->erro_status == "0") {
                $sqlerro  = true;
            }

            $this->cl_desmembramentoinicialhistorico->deleteByQuery("v37_cda = $certidao or v37_cda_old = $certidao");

            $this->clcertid->excluir($certidao);
            if ($this->clcertid->erro_status == 0) {
                $sqlerro = true;
            }
        } elseif ($tipo == "parc") {
            for ($w = 0; $w < $quantcertter; $w++) {
                $resultcertter = pg_fetch_object($result_certter, $w);
                $this->clacertter->excluir($resultcertter->v14_certid, $resultcertter->v14_parcel);
                $this->clacertter->v14_certid = $resultcertter->v14_certid;
                $this->clacertter->v14_parcel = $resultcertter->v14_parcel;
                $this->clacertter->v14_vlrcor = $resultcertter->v14_vlrcor;
                $this->clacertter->v14_vlrhis = $resultcertter->v14_vlrhis;
                $this->clacertter->v14_vlrjur = $resultcertter->v14_vlrjur;
                $this->clacertter->v14_vlrmul = $resultcertter->v14_vlrmul;
                $this->clacertter->v14_codacertid = $v15_codigo;
                $this->clacertter->incluir($resultcertter->v14_certid, $resultcertter->v14_parcel);
                if ($this->clacertter->erro_status == 0) {
                    $sqlerro = true;
                }

                if ($sqlerro == false) {
                    $this->clcertter->v14_certid = $certidao;
                    $this->clcertter->v14_parcel = $resultcertter->v07_parcel;
                    $this->clcertter->excluir($certidao, $resultcertter->v07_parcel);
                    if ($this->clcertter->erro_status == 0) {
                        $sqlerro = true;
                    }
                }
            }

            $result_forotip = $this->clarreforo->sql_record(
                $this->clarreforo->sql_query_file(
                    null,
                    "distinct k00_numpre, k00_tipo",
                    null,
                    "k00_certidao=$certidao"
                )
            );
            if ($this->clarreforo->numrows > 0) {
                db_fieldsmemory($result_forotip, 0);
            }

            if ($sqlerro == false) {
                $this->clarrecad->k00_tipo = $k00_tipo;

                for ($arreforo = 0; $arreforo < pg_numrows($result_forotip); $arreforo++) {
                    db_fieldsmemory($result_forotip, $arreforo);
                    $this->clarrecad->alterar_arrecad("k00_numpre=$k00_numpre");
                    if ($this->clarrecad->erro_status == 0) {
                        $sqlerro = true;
                    }
                }
            }

            $this->clarreforo->excluir(null, "k00_certidao=$certidao");
            if ($this->clarreforo->erro_status == 0) {
                $sqlerro = true;
            }

            $this->clListacda->excluir(null, "v81_certid = $certidao");
            if ($this->clListacda->erro_status == "0") {
                $sqlerro  = true;
            }

            $this->cl_desmembramentoinicialhistorico->deleteByQuery("v37_cda = $certidao or v37_cda_old = $certidao");

            $this->clcertid->excluir($certidao);
            if ($this->clcertid->erro_status == 0) {
                $sqlerro = true;
            }
        }
        return $sqlerro;
    }

    /*
    * lista
    * retorna certid matric inscr numcgm
    */

    public static function getCDAMatricInscr($k61_codigo, $agrupa)
    {
        $where = "and v13_certid  in (select
                    distinct 
                    v13_certid
                from
                    divida
                inner join certdiv on
                    v01_coddiv = v14_coddiv	
                inner join certid on
                    v14_certid = v13_certid
                inner join arrecad a on
                    v01_numpre = a.k00_numpre
                    and v01_numpar = a.k00_numpar
                inner join arretipo b on
                    a.k00_tipo = b.k00_tipo
                left join inicialcert on
                    v14_certid = v51_certidao
                where
                    v51_inicial is null
                    and v01_numpre in (
                    select
                        distinct k61_numpre
                    from
                        listadeb
                    where
                        k61_codigo = $k61_codigo )) ";

        $where1 = "and v13_certid in (select
                    distinct 
                    v13_certid
                from
                    certid
                inner join certter on
                    v13_certid = v14_certid
                inner join termo on
                    v14_parcel = v07_parcel
                inner join arrecad a on
                    v07_numpre = a.k00_numpre
                inner join arretipo b on
                    a.k00_tipo = b.k00_tipo
                left join inicialcert on
                    v14_certid = v51_certidao
                where
                    v07_numpre in (
                    select
                        distinct k61_numpre
                    from
                        listadeb
                    where
                        k61_codigo = $k61_codigo )) ";

        $order_by = " order by ";
        if ($agrupa == "mi") {
            $order_by .= "matric,inscr";
        } elseif ($agrupa == "c") {
            $order_by .= "numcgm";
        } else {
            $order_by .= "certid";
        }

        $sql = "select *
	          from ( select distinct
                          v13_certid as certid,
                          k00_matric as matric,
                          k00_inscr as inscr,
                          case
                             when arrematric.k00_numpre is not null
                               then ( select z01_cgmpri
                                        from proprietario_nome
                                       where arrematric.k00_matric = proprietario_nome.j01_matric
                                       limit 1 )
                             else arrecad.k00_numcgm
                          end as numcgm
			               from certid
	                     		inner join certdiv           on certdiv.v14_certid    = certid.v13_certid
	                     		inner join divida            on certdiv.v14_coddiv    = divida.v01_coddiv
    	               			inner join arrecad           on arrecad.k00_numpre    = divida.v01_numpre
			               			inner join arreinstit        on arreinstit.k00_numpre = arrecad.k00_numpre
			               			    and arreinstit.k00_instit = " . db_getsession('DB_instit') . "
		                   		left  join inicialcert       on certid.v13_certid     = inicialcert.v51_certidao
    	               			left  join arrematric        on arrecad.k00_numpre    = arrematric.k00_numpre
                          left  join arreinscr         on arrecad.k00_numpre    = arreinscr.k00_numpre
			               where divida.v01_instit = " . db_getsession('DB_instit') . "
			                 and certid.v13_instit = " . db_getsession('DB_instit') . "
			                 and inicialcert.v51_certidao is null
			                 {$where}
	      	           union
	      	          select distinct
                           certid.v13_certid as certid,
                           arrematric.k00_matric as matric,
                           arreinscr.k00_inscr as inscr,
                           case
                              when arrematric.k00_numpre is not null
                                then ( select z01_cgmpri
                                         from proprietario_nome
                                        where arrematric.k00_matric = proprietario_nome.j01_matric
                                        limit 1 )
                               else arrecad.k00_numcgm
                           end as numcgm
		          	      from certid
	                		     inner join certter  on certter.v14_certid    = certid.v13_certid
	                		     inner join termo on termo.v07_parcel      = certter.v14_parcel
		          				        and termo.v07_instit      = " . db_getsession('DB_instit') . "
                    	     inner join arrecad  on arrecad.k00_numpre    = termo.v07_numpre
		          				     inner join arreinstit on arreinstit.k00_numpre = arrecad.k00_numpre
		          				        and arreinstit.k00_instit = " . db_getsession('DB_instit') . "
                  		     left  join inicialcert       on certid.v13_certid     = inicialcert.v51_certidao
                           left  join arrematric        on arrecad.k00_numpre    = arrematric.k00_numpre
                			     left  join arreinscr         on arrecad.k00_numpre    = arreinscr.k00_numpre
		          	     where certid.v13_instit = " . db_getsession('DB_instit') . "
		          	       and inicialcert.v51_certidao is null
		          	       {$where1}
 			           ) as x {$order_by}";
        return DB::select($sql);
    }
}
