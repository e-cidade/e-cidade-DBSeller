<?php

namespace App\Domain\Tributario\Juridico\Repository;

use Certidao;
use Exception;

class InicialRepository
{

    private $cda;
    private $inicial;
    private $observacao;
    private $clinicialcert;
    private $clinicialnumpre;
    private $clarrecad;
    private $clinicial;
    private $clinicialmov;
    private $clinicialnomes;


    public function __construct($cda, $inicial, $observacao)
    {
        $this->cda        = $cda;
        $this->inicial    =  $inicial;
        $this->observacao = $observacao;

        $this->clinicialcert   = new \cl_inicialcert;
        $this->clinicialnumpre = new \cl_inicialnumpre;
        $this->clarrecad       = new \cl_arrecad;
        $this->clinicial       = new \cl_inicial;
        $this->clinicialmov    = new \cl_inicialmov;
        $this->clinicialnomes  = new \cl_inicialnomes;
    }

    public function desvinculaCDA()
    {
        $v50_inicial = $this->inicial;
        $cda = $this->cda;

        $sqlPardiv = "select
                            v04_tipoinicial,v04_tipocertidao as tipocertidao 
                        from pardiv 
                        where v04_instit  = " . db_getsession('DB_instit');
        $rsPardiv  = db_query($sqlPardiv);
        $linhasPardiv = pg_num_rows($rsPardiv);
        if ($linhasPardiv > 0) {
            $tipocertidao = pg_fetch_result($rsPardiv, 1);
        } else {
            return true;
        }

        $sql  = " select distinct v01_numpre ";
        $sql .= " from ( select certid.v13_certid, ";
        $sql .= "               divida.v01_numpre ";
        $sql .= "           from certid ";
        $sql .= "                inner join inicialcert on inicialcert.v51_certidao = certid.v13_certid ";
        $sql .= "                inner join certdiv on certdiv.v14_certid = certid.v13_certid ";
        $sql .= "                inner join divida  on divida.v01_coddiv  = certdiv.v14_coddiv ";
        $sql .= "         where v51_inicial  = $v50_inicial ";
        $sql .= "           and v51_certidao = " . $cda;
        $sql .= "     union ";
        $sql .= "         select certid.v13_certid, ";
        $sql .= "               termo.v07_numpre ";
        $sql .= "           from certid ";
        $sql .= "                inner join inicialcert on inicialcert.v51_certidao = certid.v13_certid ";
        $sql .= "                inner join certter  on certter.v14_certid = certid.v13_certid ";
        $sql .= "                inner join termo    on termo.v07_parcel   = certter.v14_parcel ";
        $sql .= "         where v51_inicial  = $v50_inicial ";
        $sql .= "           and v51_certidao = " . $cda . " ) as x ";

        $rsExcArrecad = db_query($sql);
        $intNumrows   = pg_num_rows($rsExcArrecad);

        // for excluindo da inicial numpre as certidoes desmarcadas e dando update no arrecad
        for ($ii = 0; $ii < $intNumrows; $ii++) {
            $v01_numpre = pg_fetch_row($rsExcArrecad, $ii)[0];

            $this->clinicialnumpre->excluir(null, " v59_inicial = $v50_inicial and v59_numpre = $v01_numpre ");

            if ($this->clinicialnumpre->erro_status == 0) {
                return true;
            }

            $this->clarrecad->k00_tipo = $tipocertidao;
            $this->clarrecad->alterar_arrecad(" k00_numpre = $v01_numpre ");

            if ($this->clarrecad->erro_status == 0) {
                return true;
            }
        }

        $this->clinicialcert->v51_inicial = $v50_inicial;
        $this->clinicialcert->v51_certidao =  $cda;
        $this->clinicialcert->excluir($v50_inicial, $cda);

        if ($this->clinicialcert->erro_status == 0) {
            return true;
        }
        return false;
    }

    public function anulaInicial()
    {
        $oInicial = new \inicial();
        $sObservacaoAnulacao  = $this->observacao;
        $iCodigoInicial = $this->inicial;

        try {
            $oInicial->setObservacaoMovimentacao($sObservacaoAnulacao);
            $oInicial->anulaInicial($iCodigoInicial, 9);
        } catch (Exception $eException) {
            return true;
        }
        return false;
    }

    public function inclusaoInicial(
        $v50_advog,
        $v50_codlocal,
        $gera,
        $cert_ant
    ) {
        $certid = $this->cda;
        $oCertidao = new Certidao($certid);

        if ($oCertidao->isCobrancaExtrajudicial()) {
            return true;
        }

        // Verifica se ja tem Inicial
        $sSqlInicialCert  = $this->clinicialcert->sql_query(
            null,
            null,
            "v51_certidao",
            null,
            "inicialcert.v51_certidao = $certid and inicial.v50_situacao = 1"
        );
        $rsSqlInicialCert = $this->clinicialcert->sql_record($sSqlInicialCert);

        if ($this->clinicialcert->numrows > 0) {
            return true;
        }

        if ($gera == true) {
            $usuario = db_getsession("DB_id_usuario");
            $data    = date("Y-m-d", db_getsession("DB_datausu"));

            $this->clinicial->v50_instit   = db_getsession("DB_instit");
            $this->clinicial->v50_advog    = $v50_advog;
            $this->clinicial->v50_data     = $data;
            $this->clinicial->v50_id_login = $usuario;
            $this->clinicial->v50_codlocal = $v50_codlocal;
            $this->clinicial->v50_codmov   = "0";
            $this->clinicial->v50_situacao = "1";
            $this->clinicial->incluir(null);

            $inicial  = $this->clinicial->v50_inicial;

            if ($this->clinicial->erro_status == 0) {
                return true;
            }

            $this->clinicialmov->v56_obs = $this->observacao;
            $this->clinicialmov->atuinicialmov($inicial, "1");

            if ($this->clinicialmov->erro_status == 0) {
                return true;
            }
        } else {
            $sSqlInicialCert  =  $this->clinicialcert->sql_query_file(null, $cert_ant);
            $rsSqlInicialCert    =  $this->clinicialcert->sql_record($sSqlInicialCert);
            $inicial = pg_fetch_all($rsSqlInicialCert)[0]['v51_inicial'];

            if ($this->clinicialcert->numrows == 0) {
                return true;
            }
        }

        //Verifica se tem uma incluida
        $sSqlInicialCert  = $this->clinicialcert->sql_query_file($inicial, $certid);
        $rsSqlInicialCert    = $this->clinicialcert->sql_record($sSqlInicialCert);

        if ($this->clinicialcert->numrows > 0) {
            return true;
        }

        $this->clinicialcert->v51_certidao = $certid;
        $this->clinicialcert->v51_inicial  = $inicial;
        $this->clinicialcert->incluir($inicial, $certid);

        if ($this->clinicialcert->erro_status == 0) {
            return true;
        }

        $sql_info = "select distinct
                        k00_numpre,
                        k00_numcgm
                    from ( select distinct
                                    k00_numpre,
                                    k00_numcgm
                                    from certid
                                        inner join certdiv on certdiv.v14_certid = certid.v13_certid
                                        inner join divida  on certdiv.v14_coddiv = divida.v01_coddiv
                                            and divida.v01_instit  = " . db_getsession('DB_instit') . "
                                    inner join arrenumcgm  on divida.v01_numpre  = arrenumcgm.k00_numpre
                                    where certid.v13_certid = {$certid}
                                    and certid.v13_instit = " . db_getsession('DB_instit') . "
                        union
                            select distinct
                                    k00_numpre,
                                    k00_numcgm
                                    from certid
                                        inner join certter on certter.v14_certid = certid.v13_certid
                                        inner join termo on termo.v07_parcel   = certter.v14_parcel
                                                and termo.v07_instit   = " . db_getsession('DB_instit') . "
                                            inner join arrenumcgm  on termo.v07_numpre = arrenumcgm.k00_numpre
                                    where certid.v13_certid = {$certid}
                                    and v13_instit = " . db_getsession('DB_instit') . " ) as x ";

        $result_info  = db_query($sql_info);
        $numrows_info = pg_num_rows($result_info);

        for ($i = 0; $i < $numrows_info; $i++) {
            $res = pg_fetch_object($result_info, $i);

            $k00_numcgm = $res->k00_numcgm;
            $k00_numpre = $res->k00_numpre;

            if ($k00_numcgm == 0 or $k00_numpre == 0) {
                continue;
            }

            $result_nomes = $this->clinicialnomes->sql_record(
                $this->clinicialnomes->sql_query_file(
                    $inicial,
                    $k00_numcgm
                )
            );
            if ($this->clinicialnomes->numrows == 0) {
                $this->clinicialnomes->v58_inicial = $inicial;
                $this->clinicialnomes->v58_numcgm  = $k00_numcgm;
                $this->clinicialnomes->incluir($inicial, $k00_numcgm);
                if ($this->clinicialnomes->erro_status == 0) {
                    return true;
                }
            }

            $numpre = $k00_numpre;
            $result_existeininum = $this->clinicialnumpre->sql_record(
                $this->clinicialnumpre->sql_query_file(
                    null,
                    "*",
                    null,
                    "v59_inicial={$inicial} and v59_numpre={$k00_numpre}"
                )
            );
            if ($this->clinicialnumpre->numrows == 0) {
                $this->clinicialnumpre->v59_inicial = $inicial;
                $this->clinicialnumpre->v59_numpre  = $numpre;
                $this->clinicialnumpre->incluir();

                $numpre = $k00_numpre;
                if ($this->clinicialnumpre->erro_status == 0) {
                    return true;
                }
            }

            $clpardiv = new \cl_pardiv;
            $rsPardiv   = $clpardiv->sql_record($clpardiv->sql_query_file(db_getsession('DB_instit')));

            if ($clpardiv->numrows > 0) {
                $oPardiv = pg_fetch_object($rsPardiv, 0);
                $iTipoini = $oPardiv->v04_tipoinicial;
            }

            $this->clarrecad->k00_tipo = $iTipoini;
            $this->clarrecad->alterar_arrecad("k00_numpre = $k00_numpre");

            if ($this->clarrecad->erro_status == 0) {
                return true;
            }
        }
    }
}
