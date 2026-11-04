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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_iptunump_classe.php"));
require_once(modification("classes/db_recibounica_classe.php"));

/**
 * Constante com o código de retorno da verificacalculo, quando o IPTU está quitado
 * @todo Garantir que este código seja o mesmo retornado pela PL e do cálculo geral
 */
define("IPTU_QUITADO", 27);

$oDaoReciboUnica = new cl_recibounica();

(int)$parcelaini = 0;
(int)$parcelas   = 0;
(int)$mesini     = 0;
(float)$percentualdesconto = 0;
$diavenc         = '';

db_postmemory($HTTP_POST_VARS);

$cliptubase = new cl_iptubase;
$cliptubase->rotulo->label();
$clrotulo   = new rotulocampo;
$clrotulo->label('z01_nome');
$clrotulo->label('z01_numcgm');
$clrotulo->label('k00_histtxt');
$clrotulo->label('j01_matric');
$clrotulo->label('j27_observacao');

if (isset($calcular)) {
    // Verificamos se o parametro de separar as taxas no calculo, esta ativo
    $sql = "SELECT 
            j18_taxaseparada 
                from 
                    cadastro.cfiptu
                WHERE 
                    j18_anousu = {$anousu}
                limit 1";
    $rs = db_query($sql);
    $taxaSeparada = db_utils::fieldsMemory($rs, 0)->j18_taxaseparada;
    // Caso a taxa esteja separada, efetua o calculo via php
    if (!empty($taxaSeparada) and false) { // não deve entrar aqui ainda
        // dump("TAXA SEPARADA");
        // die();
        // TODO
        /*
            colocar logica aqui
        */
    } else {
        /**
         * Verificamos se o calculo do exercicio possui receitas vencidas
        */
        $oDaocfiptu = new cl_cfiptu;
        $sMensagem = $oDaocfiptu->verificaReceitasInvalidas($anousu);
        if (!empty($sMensagem)) {
            $sMensagemRetorno = "Erro ao executar o Cálculo!\n";
            $sMensagemRetorno .= $sMensagem;
            db_msgbox($sMensagemRetorno);
            db_redireciona('cad4_calciptu.php');
        }

        if (isset($HTTP_POST_VARS['j01_matric'])) {
            /*
             * Verifica a situação do Calculo de IPTU
             *  Situações de bloqueio:
             *   32 - Importado para Divida
             */
            $sql = "select fc_iptu_verificacalculo($j01_matric::integer,$anousu::integer, 0, 0)";
            $result = db_query($sql);

            $retorno_result = pg_fetch_result($result,0,0);

            $retorno_erro = substr($retorno_result,3,2);
            $retorno_erro = str_replace(")", "", $retorno_erro);

            if ($retorno_erro == 32) {
                $sMensagem = 'Cálculo de IPTU para este ano, já esta em Dívida Ativa';
                db_msgbox($sMensagem);
                db_redireciona('cad4_calciptu.php');
            }

            $sqlnextval = "select nextval('iptucalclog_j27_codigo_seq') as j27_codigo";
            $resultnextval = db_query($sqlnextval) or die($sqlnextval);

            if ($resultnextval == false) {
                echo "<script>alert('Erro ao gerar sequencia!');</script>";
            } else {
                db_fieldsmemory($resultnextval,0);
                db_query("begin");
                $insert = "insert into iptucalclog values ($j27_codigo,$anousu,'".date('Y-m-d',db_getsession("DB_datausu"))."','".db_hora()."',".db_getsession('DB_id_usuario').",true,1, '$j27_observacao',". ($financeiro==0?"true":"false") .")";

                $resultinsert = db_query($insert) or die($insert);
                if ($resultinsert == false) {
                    echo "<script>alert('Erro ao gerar lancamento na tabela iptucalclog!');</script>";
                } else {
                    $result = db_query("select distinct j18_anousu, j18_permvenc from cfiptu order by j18_anousu desc");
                    $j18_permvenc = 1;
                    if (pg_num_rows($result) > 0) {
                        db_fieldsmemory($result,0);
                    }
                    if ($j18_permvenc == 0) {
                        $j18_permvenc = 1;
                    } 

                    if ($j18_permvenc == 1) {
                        // esta variavel e uma string no formato de um array plpgsql, nao altere seu conteudo se voce nao tem certeza do que esta fazendo
                        $arraypl = "array['".(int)$parcelas."','".(int)$diavenc."','".(int)$mesini."']";
                    } elseif ($j18_permvenc == 2) {
                        // esta variavel e uma string no formato de um array plpgsql, nao altere seu conteudo se voce nao tem certeza do que esta fazendo
                        $arraypl = "array['".(int)$parcelaini."','".(int)$parcelafinal."']";
                    }

                    db_query("begin");

                    $sSqlVerCalculo = "select case when exists (select 1 
                                                                  from iptucalc 
                                                                 where j23_anousu = $anousu 
                                                                   and j23_matric = $j01_matric) then 
                                                        true 
                                                   else false 
                                                   end                                    as tem_iptucalc,
                                              case when exists (select 1 
                                                                  from iptucale
                                                                 where j22_anousu = $anousu 
                                                                   and j22_matric = $j01_matric) then
                                                        true
                                                   else false  
                                                   end                                    as tem_iptucale,
                                              case when exists (select 1 
                                                                  from iptucalv
                                                                 where j21_anousu = $anousu
                                                                   and j21_matric = $j01_matric) then
                                                        true 
                                                   else false 
                                              end                                         as tem_iptucalv,
                                              case when exists (select 1 
                                                                  from iptunump
                                                                 where j20_anousu = $anousu
                                                                   and j20_matric = $j01_matric) then
                                                        true
                                                   else false  
                                              end                                    as tem_iptunump;";

                    $rsVerCalculo = db_query($sSqlVerCalculo);

                    if (!$rsVerCalculo) {
                        db_query("ROLLBACK");
                        throw new DBException("Não foi possivel buscar dados de cálculo anteriror (".pg_last_error().")");
                    }

                    db_fieldsmemory($rsVerCalculo, 0);
                    
                    // Gera informações old se já existir cálculo no exercício
                    if ($tem_iptucalc == 't') {
                       // iptucalcold
                       $clIptucalcold  = new cl_iptucalcold;

                       $retIptucalcold = $clIptucalcold->salvarIptucalcOld($anousu, $j01_matric, $j27_codigo);
                       if (!$retIptucalcold == 't') {
                           db_query("ROLLBACK");
                           throw new DBException("Não foi possivel salvar informações do cálculo anteriror (".pg_last_error().")");
                       }
                    }

                    if ($tem_iptucalv == 't') {
                       // iptucalvold (valores)
                       $clIptucalvold = new cl_iptucalvold;
                       $retIptucalvold = $clIptucalvold->salvarIptucalvOld($anousu, $j01_matric, $j27_codigo);
                       if (!$retIptucalvold) {
                           db_query("ROLLBACK");
                           throw new DBException("Não foi possivel salvar informações do cálculo anteriror (valores) (".pg_last_error().")");
                       }
                    }

                    if ($tem_iptucale == 't') {
                       // iptucaleold (construcoes)
                       $clIptucaleold = new cl_iptucaleold;
                       $retIptucaleold = $clIptucaleold->salvarIptucaleOld($anousu, $j01_matric, $j27_codigo);

                       if (!$retIptucaleold) {
                           $sqlerro=true;
                           db_query("ROLLBACK");
                           throw new DBException("Não foi possivel salvar informações do cálculo anteriror (construções) (".pg_last_error().")");
                       }
                    }

                    if ($tem_iptunump == 't') {
                       // iptunumpold 
                       $clIptunumpold = new cl_iptunumpold;
                       $retIptunumpold = $clIptunumpold->salvarIptunumpOld($anousu, $j01_matric, $j27_codigo);
                       if (!$retIptunumpold) {
                           db_query("ROLLBACK");
                           throw new DBException("Não foi possivel salvar informações do cálculo anteriror (numpre) (".pg_last_error().")");
                       }
                    }

                    //taxas
                    $sSqlVerCalculoTaxa = "select case when exists 
                                                            (select j151_codigo 
                                                               from iptutaxanump inner join iptucadtaxaexe on j151_iptucadtaxaexe = j08_iptucadtaxaexe 
                                                          left join iptutaxacalv on j151_codigo = j152_iptutaxanump 
                                                              where j151_matric = $j01_matric and j08_anousu = $anousu)
                                                       then true
                                                       else false
                                                       end as                                                           tem_iptutaxanump,
                    
                                                  case when exists
                                                            (select j152_codigo 
                                                               from iptutaxanump inner join iptucadtaxaexe on j151_iptucadtaxaexe = j08_iptucadtaxaexe 
                                                         inner join iptutaxacalv on j151_codigo = j152_iptutaxanump 
                                                              where j151_matric = $j01_matric and j08_anousu = $anousu)
                                                        then true
                                                        else false
                                                  end as                                                                tem_iptutaxacalv;";

                    $rsVerCalculoTaxa = db_query($sSqlVerCalculoTaxa);

                    if (!$rsVerCalculoTaxa) {
                        db_query("ROLLBACK");
                        throw new DBException("Não foi possivel buscar dados de cálculo de taxas anteriores (".pg_last_error().")");
                    }

                    db_fieldsmemory($rsVerCalculoTaxa, 0);
                    
                    if ($tem_iptutaxanump == 't') {
                       $clIptutaxanumpold  = new cl_iptutaxanumpold;

                       $retIptutaxanumpold = $clIptutaxanumpold->salvarIptutaxanumpOld($anousu, $j01_matric, $j27_codigo);
   
                       if (!$retIptutaxanumpold == 't') {
                           db_query("ROLLBACK");
                           throw new DBException("Não foi possivel salvar informações do cálculo das taxas anteriores (".pg_last_error().")");
                       }
                    }
                    if ($tem_iptutaxacalv == 't' && !empty($clIptutaxanumpold->j159_sequencial)) {
                        $clIptutaxacalvold  = new cl_iptutaxacalvold;
 
                        $retIptutaxacalvold = $clIptutaxacalvold->salvarIptutaxacalvOld($anousu, $j01_matric, $j27_codigo, $clIptutaxanumpold->j159_sequencial);
    
                        if (!$retIptutaxacalvold == 't') {
                            db_query("ROLLBACK");
                            throw new DBException("Não foi possivel salvar informações do cálculo das taxas anteriores (".pg_last_error().")");
                        }
                     }

                    //fim tabelas old
                    db_query("select fc_putsession('DB_Gerafinanc','$financeiro')");
                    $sql = "select fc_calculoiptu($j01_matric::integer,$anousu::integer," . ($financeiro==0?"true":"false") . ",false::boolean,false::boolean,false::boolean,false::boolean,$arraypl)";
                    $result = @db_query($sql);

                    if (!$result) {
                        throw new DBException("Não foi possivel buscar configurações de iptu para o exercicio " . db_getsession('DB_anousu') . "." . pg_last_error());
                    }
                    
                    $taxa_erro = false;
                    // Verificamos se esta configuraro o parametro no exercicio atual
                    $sql = "SELECT 
                                j18_taxaseparada as habilitataxa 
                            from 
                                cadastro.cfiptu
                            WHERE 
                                j18_anousu = " . db_getsession('DB_anousu') . "
                            limit 1";
                    $rs = db_query($sql);
                    if (!$rs) {
                        throw new DBException("Não foi possivel buscar configurações de iptu para o exercicio " . db_getsession('DB_anousu') . ".");
                    }

                    $habilitaTaxas = db_utils::fieldsMemory($rs, 0)->habilitataxa;

                    if ($result && $habilitaTaxas) {                        
                        /** 
                         * @todo - vai retornar um boolean e deve ser validado se deu certo ou nao
                         */

                        $sql = "select fc_calculoiptutaxa($j01_matric::integer, $anousu::integer)::integer as codigo_calculo_taxa;";

                        $result2 = db_query($sql);

                        if (!$result2) {
                            db_query("ROLLBACK");
                        }

                        if (pg_num_rows($result2) > 0) {

                            db_fieldsmemory($result2, 0);

                            if ($codigo_calculo_taxa > 1) {

                                $msgErroCalculo = "";

                                if ($codigo_calculo_taxa == 2) {
                                    $msgErroCalculo = "Quitação";
                                } else if ($codigo_calculo_taxa == 3) {
                                    $msgErroCalculo = "Isenção";
                                } else if ($codigo_calculo_taxa == 4) {
                                    $msgErroCalculo = "Cancelamento";
                                }

                                $cliptubase->erro_msg = "Erro codigo ".$codigo_calculo_taxa." - Dados em estado de ".$msgErroCalculo." - Sistema não suporta a ocasião.";
                                $cliptubase->erro_status = '0';

                                $taxa_erro = true;
                                db_query("ROLLBACK");
                            }
                        }
                    }
                    if (!$result) {
                        $sErro = pg_last_error();
                        if (preg_match('/<erro>(.*)<\/erro>/', $sErro, $aMatchError)) {
                            $sErro = $aMatchError[1];
                        } else {
                            $sErro = "Erro ao calcular IPTU.";
                        }

                        db_query("ROLLBACK");

                        $cliptubase->erro_msg = $sErro;
                        $cliptubase->erro_status = '0';
                    } else if (pg_num_rows($result) > 0 && !$taxa_erro) {
                        $retorno_result = pg_fetch_result($result,0,0);

                        preg_match('/[0-9]*/', trim($retorno_result), $aTipoLogCalc);

                        $retorno = $aTipoLogCalc[0];

                        if ($retorno != '001') {
                            $cliptubase->erro_msg = "Erro: " . $retorno_result;
                            $cliptubase->erro_status = '0';
                        } else {
                            $cliptubase->erro_msg = "Cálculo Efetuado.";
                            $cliptubase->erro_status = '1';
                        }
                        $insert = "insert into iptucalclogmat values ($j27_codigo,$j01_matric,$retorno,'".trim(preg_replace('/^[0-9]*/', '',trim($retorno_result)))."',". ($financeiro==0?"true":"false") .")";
                        $resultinsert = db_query($insert) or die($insert);

                        db_query("commit");

                    } else if (!$taxa_erro) {

                        $cliptubase->erro_msg = pg_last_error();
                        $cliptubase->erro_status = '0';
                        db_query("ROLLBACK");
                    }

                    if ((int)$percentualdesconto > 0) {
                        $cliptunump = new cl_iptunump;
                        $result = $cliptunump->sql_record($cliptunump->sql_query_file($anousu,$j01_matric,'j20_matric#j20_numpre'));
                        if (!($result==false || $cliptunump->numrows == 0 ) && $mesini != 0) {
                            $sqlunica = db_query("BEGIN");

                            /*
                             * alteracao para incluir no cabeçalho criado para recibo unica
                             * recibounicageracao
                             */
                            require_once(modification("classes/db_recibounicageracao_classe.php"));
                            $oDaoReciboUnicaGeracao = new cl_recibounicageracao();

                            $oDaoReciboUnicaGeracao->ar40_db_usuarios = db_getsession("DB_id_usuario");
                            $oDaoReciboUnicaGeracao->ar40_dtoperacao = date("Y-m-d",db_getsession("DB_datausu"));
                            $oDaoReciboUnicaGeracao->ar40_dtvencimento = "{$anousu}-{$mesini}-{$diavenc}";
                            $oDaoReciboUnicaGeracao->ar40_percentualdesconto = $percentualdesconto;
                            $oDaoReciboUnicaGeracao->ar40_tipogeracao = "G";
                            $oDaoReciboUnicaGeracao->ar40_ativo = 'true';
                            $oDaoReciboUnicaGeracao->ar40_observacao = 'Inclusao pelo calculo de iptu (cad4_calciptu.php)';
                            $oDaoReciboUnicaGeracao->incluir(null);

                            if ($oDaoReciboUnicaGeracao->erro_status == 0) {
                                $descricao_erro = $oDaoReciboUnicaGeracao->erro_msg;
                            }

                            for ($i=0; $i<$cliptunump->numrows; $i++) {
                                db_fieldsmemory($result, $i);
                                $sqlunica = db_query("select k00_dtvenc,k00_percdes
                                                      from recibounica
                                                      where k00_numpre = $j20_numpre
                                                          and k00_dtvenc = '$anousu-$mesini-$diavenc'");
                                $erro = true;
                                $perc = 0;

                                if (pg_num_rows($sqlunica) != 0) {
                                    $perc = pg_fetch_result($sqlunica,0,'k00_percdes');
                                    $sqlresultunica = "delete from recibounica where k00_numpre = $j20_numpre and k00_dtvenc = '$anousu-$mesini-$diavenc'";
                                    $resultunica = db_query($sqlresultunica );
                                    $descricao_erro = "Vencimento Excluído.";
                                }

                                if (($perc != $percentualdesconto) || (pg_num_rows($sqlunica) == 0)) {
                                    $oDaoReciboUnica->k00_numpre = $j20_numpre;
                                    $oDaoReciboUnica->k00_dtvenc = "$anousu-$mesini-$diavenc";
                                    $oDaoReciboUnica->k00_dtoper = date("Y-m-d",db_getsession("DB_datausu"));
                                    $oDaoReciboUnica->k00_percdes = $percentualdesconto;
                                    $oDaoReciboUnica->k00_tipoger = "G";
                                    $oDaoReciboUnica->k00_recibounicageracao = $oDaoReciboUnicaGeracao->ar40_sequencial;
                                    $oDaoReciboUnica->incluir(null);

                                    if ($oDaoReciboUnica->erro_status == 0) {
                                        $descricao_erro = $oDaoReciboUnica->erro_msg;
                                    } else {
                                        $descricao_erro = "Vencimento Incluído.";
                                    }
                                }

                                $histd = "Data: ".date("Y-m-d",db_getsession("DB_datausu"));
                                $histd .= " Perc: ".$percentualdesconto." Usuário: ".db_getsession("DB_login");
                                $histd .= $k00_histtxt;

                                $sqlresultunica = " insert into arrehist(k00_numpre,
                                                       k00_numpar,
                                                       k00_hist,
                                                       k00_dtoper,
                                                       k00_hora,
                                                       k00_id_usuario,
                                                       k00_histtxt,
                                                       k00_idhist)
                                                       values ($j20_numpre,
                                                       0,
                                                       890,
                                                       '".date("Y-m-d",db_getsession("DB_datausu"))."',
                                                       '".date("G:i")."',
                                                       ".db_getsession("DB_id_usuario").",
                                                       '$histd',
                                                       nextval('arrehist_k00_idhist_seq'))";
                                $resultunica = db_query($sqlresultunica );
                                if ($resultunica == false) {
                                    $descricao_erro = "Erro ao incluir no arquivo historicos";
                                }
                            }
                            $sqlunica = db_query("ROLLBACK");
                        }
                    }
                }
            }
        } else {
            $cliptubase->erro_msg = 'Matricula não informada.';
            $cliptubase->erro_status = '0';
        }
    }
}

if (isset($demonstrativo)) {
    // Verificamos se o parametro de separar as taxas no calculo, esta ativo
    $sql = "SELECT 
            j18_taxaseparada 
                from 
                    cadastro.cfiptu
                WHERE 
                    j18_anousu = {$anousu}
                limit 1";
    $rs = db_query($sql);
    $taxaSeparada = db_utils::fieldsMemory($rs, 0)->j18_taxaseparada;
    // Verificar necessidade aqui no demonstrativo caso a taxa esteja separada.
    if (!empty($taxaSeparada)) {
        // dump("TAXA SEPARADA");
        // die();
        // TODO
        /*  
            caso precise
            colocar logica aqui
        */
    } else { 
        if (isset($HTTP_POST_VARS['j01_matric'])) {
            $result = db_query("select distinct j18_anousu, j18_permvenc from cfiptu order by j18_anousu desc");
            if (pg_num_rows($result) > 0) {
                db_fieldsmemory($result, 0);
            } else {
                $j18_permvenc = 0;
            }
            if ($j18_permvenc == 1) {
                // esta variavel e uma string no formato de um array plpgsql, nao altere seu conteudo se voce nao tem certeza do que esta fazendo
                $arraypl = "array['" . (int)$parcelas . "','" . (int)$diavenc . "','" . (int)$mesini . "']";
            } elseif ($j18_permvenc == 2) {
                // esta variavel e uma string no formato de um array plpgsql, nao altere seu conteudo se voce nao tem certeza do que esta fazendo
                $arraypl = "array['" . (int)$parcelaini . "','" . (int)$parcelafinal . "']";
            } else {
                $arraypl = "array['" . (int)$parcelas . "','" . (int)$diavenc . "','" . (int)$mesini . "']";
            }
            db_query("select fc_putsession('DB_Gerafinanc','0')");
            $sql = "select fc_calculoiptu($j01_matric::integer,$anousu::integer,true::boolean,false::boolean,false::boolean,false::boolean,true::boolean," . $arraypl . ")";
            $result = @db_query($sql);

            if (!$result) {
                $sErro = pg_last_error();
                $iPosIni = strpos($sErro, "<erro>");
                $iPosFin = strpos($sErro, "</erro>");
                $sErro = substr($sErro, $iPosIni, $iPosFin);
                $sErro = str_replace("<erro>", "", $sErro);
                $sErro = str_replace("</erro>", "", $sErro);

                $cliptubase->erro_msg = $sErro;
                $cliptubase->erro_status = '0';

            } else if (pg_num_rows($result) > 0) {
                $retorno_result = pg_fetch_result($result,0,0);
                preg_match('/[0-9]*/', trim($retorno_result), $aTipoLogCalc);
                $retorno = $aTipoLogCalc[0];

                if ($retorno != '001' and $retorno != ' ') {
                    $cliptubase->erro_msg = "Demonstrativo efetuado!";
                    $cliptubase->erro_status = '1';
                } else {
                    $cliptubase->erro_msg = "Erro: " . $retorno_result;
                    $cliptubase->erro_status = '0';
                }
            } else {
                $cliptubase->erro_msg = pg_last_error();
                $cliptubase->erro_status = '0';
            }
        } else {
            $cliptubase->erro_msg = 'Matricula não informada.';
            $cliptubase->erro_status = '0';
        }
    }
}
?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            js_removeObj('msgCalculo');
            var lDemonstrativo = false;

            function js_verificacalculo() {
                if (lDemonstrativo) {
                    js_divCarregando('Aguarde...', 'msgCalculo');
                    return true;
                }
                if (taxasSeparadas == 1) {
                    var taxasCheckbox = document.querySelectorAll('input[tipo=opcaoTaxa]:checked');
                    var codTaxas = "";
                    taxasCheckbox.forEach(function(el){

                        codTaxas += el.value + ",";
                    });
                    str = codTaxas.slice(0, -1);
                    var taxas = document.getElementById("taxas");
                    taxas.value = str;
                }
                if (document.form1.j01_matric.value == "") {
                    alert("Campo Matrícula do Imóvel é de preenchimento obrigatório.");
                    return false;
                }

                var verificaParametrosRecalculo = false,
                lValidado = true;
                var sRPC = "cad4_calciptu.RPC.php";

                var oParametros = {
                    "sExecucao" : "verificaPagamentos",
                    "iMatricula" : document.form1.j01_matric.value,
                    "iAnousu" : document.form1.anousu.value
                }

                new AjaxRequest(sRPC, oParametros, function(oRetorno, lErro) {
                    if (lErro) {
                        alert(oRetorno.sMensagem.urlDecode());
                        lValidado = false;
                        return false;
                    }
                    verificaParametrosRecalculo = oRetorno.lQuitado;

                    if (oRetorno.lQuitado) {
                        if (!confirm("O IPTU do exercício já está quitado. Deseja recalcular?")) {
                            lValidado = false;
                            return false;
                        }
                    }
                }).asynchronous(false).execute();

                if (!lValidado) {
                    return false;
                }

                /**
                 * Após a verificação dos pagamentos e a confirmação do usuário para executar o recalculo,
                 * validamos os se há tipo de débito e receita configurados
                 */
                if (verificaParametrosRecalculo) {
                    oParametros = {
                        "sExecucao" : "verificaParametros",
                        "iAnousu" : document.form1.anousu.value
                    }

                    new AjaxRequest(sRPC, oParametros, function(oRetorno, lErro) {
                        if (lErro) {
                            alert(oRetorno.sMensagem);
                            lValidado = false;
                            return false;
                        }
                        if (oRetorno.lIrregular) {
                            alert(oRetorno.sMensagem);
                            lValidado = false;
                            return false;
                        }
                    }).asynchronous(false).execute();
                }

                if (!lValidado) {
                    return false;
                }

                js_divCarregando('Aguarde...', 'msgCalculo');
                return true;
            }
        </script>
        <style type="text/css">
            textarea {
                color: #000000;
                background-color: #FFFFFF;
            }
            #opcaoCalculo li {
                list-style: none;
                display: block;
            }
            #opcaoCalculo ul {   
                margin: 0px;
                padding: 0px;
            }
            #opcaoCalculo input {
                margin: 0;
                padding: 0;
                margin-right: 5px;
                vertical-align: middle;
            }
        </style>
    </head>
    <body class="body-default" onLoad="document.form1.j01_matric.focus();buscaTaxas();">
        <div class="container">
            <form name="form1" action="" method="post" onSubmit="return js_verificacalculo();">
                <input type="hidden" name="taxas" id="taxas" value="">
                <fieldset width="570px;">
                    <legend>Cálculo Parcial</legend>
                    <table>
                        <tr>
                            <td title="<?php echo $Tz01_numcgm; ?>">
                                <?php db_ancora($Lj01_matric,'js_mostranomes(true);',4);?>
                            </td>
                            <td>
                                <?php
                                db_input("j01_matric",8,$Ij01_matric,true,'text',4," onchange='js_mostranomes(false);' ");
                                db_input("z01_nome",40,$Iz01_nome,true,'text',3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Ano:</strong></td>
                            <td>
                                <?php
                                $result = db_query("select distinct j18_anousu from cfiptu order by j18_anousu desc");
                                if (pg_num_rows($result) > 0) {
                                    $opcoes = array();
                                    for ($i = 0; $i < pg_num_rows($result); $i++) {
                                        db_fieldsmemory($result, $i);
                                        $opcoes[$j18_anousu] = $j18_anousu;
                                    }
                                    db_select("anousu", $opcoes, true, 1, "onChange=buscaTaxas()");
                                } else {
                                    $j18_permvenc = 0;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                            $oDaocfiptu = new cl_cfiptu;
                            $sqlcfiptu = $oDaocfiptu->sql_query_file(db_getsession("DB_anousu"),'j18_taxaseparada, j18_simulacaocalculo');
                            $rs = pg_fetch_object(db_query($sqlcfiptu));
 
                            if($rs->j18_taxaseparada == 0 && $rs->j18_simulacaocalculo == 't'){
                            ?>
                            <tr>
                                <td><strong><label for="financeiro">Calcular Financeiro:</label></strong></td>
                                <td>
                                    <?php
                                    $x = array("0" => "Sim", "1" => "Não");
                                    db_select("financeiro", $x, true, 2, "style=\"width: 55px;\"");
                                    ?>
                                </td>
                            </tr>
                            <?php
                                
                            }
                            ?>
                        
                        <tr>
                            <td title="<?php echo $Tj27_observacao;?>">
                                <label for="j27_observacao"><strong><?php echo $Lj27_observacao;?></strong></label>
                            </td>
                            <td>
                                <?php db_textarea("j27_observacao", 5, 50, 3, true, 'text', 1);?>
                            </td>
                        </tr>
                        <?php
                        $rsPar = db_query("select j18_permvenc from cfiptu where j18_anousu = " . db_getsession('DB_anousu') . " ");
                        if (pg_num_rows($rsPar) > 0) {
                            db_fieldsmemory($rsPar,0);
                        }
                        if (!isset($j18_permvenc)) {
                            $j18_permvenc = 0;
                        }
                        if ($j18_permvenc == 1) {
                            ?>
                            <tr>
                                <td><strong>Dia para vencimento:</strong></td>
                                <td>
                                    <?php db_input("diavenc", 8, "", true, 'text', 4, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Parcelas:</strong></td>
                                <td>
                                    <?php db_input("parcelas", 8, "", true, 'text', 4, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Mês inicial:</strong></td>
                                <td>
                                    <?php db_input("mesini", 8, "", true, 'text', 4, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Percentual desconto da parcela única:</strong></td>
                                <td>
                                    <?php db_input("percentualdesconto", 8, "", true, 'text', 4, "");?>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td nowrap title="<?php echo $Tk00_histtxt;?>">
                                    <?php echo $Lk00_histtxt;?>
                                </td>
                                <td>
                                    <?php
                                    if (isset($k00_histtxt)) {
                                        $k00_histtxt = trim($k00_histtxt);
                                    }
                                    db_textarea('k00_histtxt', 5, 30, $Ik00_histtxt, true, 'text', 4);
                                    ?>
                                </td>
                            </tr>
                            <?php
                        } else if ($j18_permvenc == 2) {
                            ?>
                            <tr>
                                <td><strong>Parcela inicial:</strong></td>
                                <td>
                                    <?php db_input("parcelaini", 8, "", true, 'text', 4, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Parcela final:</strong></td>
                                <td>
                                    <?php db_input("parcelafinal", 8, "", true, 'text', 4, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Percentual desconto da parcela única:</strong></td>
                                <td>
                                    <?php db_input("percentualdesconto", 8, "", true, 'text', 4, "");?>
                                </td>
                            </tr>
                            <tr>
                                <td nowrap title="<?php echo $Tk00_histtxt;?>">
                                    <?php echo $Lk00_histtxt;?>
                                </td>
                                <td>
                                    <?php
                                    $k00_histtxt = trim($k00_histtxt);
                                    db_textarea('k00_histtxt', 5, 30, $Ik00_histtxt, true, 'text', 4);
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </fieldset>
                <fieldset id="exibeTaxas" style="display:none">
                    <legend>Calcular</legend>
                    <table>
                        <tr>
                            <td id="opcaoCalculo"></td>
                        </tr>
                    </table>
                </fieldset>
                <?php

                    // @todo - sera removido esse disabled
                    $ano = db_getsession('DB_anousu');
                    $taxaseparada = null;

                    $sql = "select j18_taxaseparada
                              from cadastro.cfiptu
                             where j18_anousu = {$ano}
                             limit 1";

                    $rs = db_query($sql);

                    if (!$rs) {
                        db_redireciona("db_erros.php?fechar=true&db_erro=Não foi possivel buscar configurações de iptu para o exercicio {$ano}.");
                    }

                    $taxaseparada = db_utils::fieldsMemory($rs, 0)->j18_taxaseparada;
                ?>
                <input name="calcular" type="submit" id="calcular" value="Calcular" onClick="lDemonstrativo=false;">
                <input name="demonstrativo" type="submit" id="demonstrativo" value="Demonstrativo" onClick="lDemonstrativo=true;">
                <?php
                if (isset($calcular) && $cliptubase->erro_status != '0' ) {
                    ?>
                    <input name="Limpar" type="button" id="limpar" value="Limpar" onClick="document.form1.j01_matric.value='';document.form1.z01_nome.value=''">
                    <input name="ultimo" type="button" id="ultimo" value="&Uacute;ltimo C&aacute;lculo" onClick="func_nome.show();  func_nome.focus();">
                    <?php
                }
                ?>
                <table>
                    <tr>
                        <td>
                            <textarea id="text_demo" name="text_demo" rows="20" cols="74" style="visibility:hidden" disabled><?=$retorno_result?></textarea>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));?>
<script type="text/javascript">
    var taxasSeparadas = 0;
    function js_verificaParametros() {
        var oMesini = document.form1.mesini;
        var oParcelas = document.form1.parcelas;
        var oDiaVenc = document.form1.diavenc;

        if (!oMesini && !oParcelas && !oDiaVenc) {
            return true;
        }

        var iMesIni = new Number(oMesini.value);
        var iParcelas = new Number(oParcelas.value);
        if ((iMesIni+iParcelas) > 13) {
            alert('Não é permitido vencimento no ano posterior ao do calculo. ')
            return false;
        }
        return true;
    }

    function js_mostranomes(mostra) {
        if (mostra == true) {
            func_nome.jan.location.href = 'func_iptubase.php?funcao_js=parent.js_preenche|j01_matric|z01_nome';
            func_nome.mostraMsg();
            func_nome.show();
            func_nome.focus();
        } else {
            func_nome.jan.location.href = 'func_iptubase.php?pesquisa_chave=' + document.form1.j01_matric.value + '&funcao_js=parent.js_preenche1';
        }
    }
    function js_preenche(chave, chave1) {
        document.form1.j01_matric.value = chave;
        document.form1.z01_nome.value = chave1;
        func_nome.hide();
    }
    
    function js_preenche1(chave,chave1) {
        document.form1.z01_nome.value = chave;
        if (chave1 == false) {
            document.form1.j01_matric.select();
            document.form1.j01_matric.focus();
        }
        func_nome.hide();
    }

    function buscaTaxas() {
        var sRPC = "cad4_calciptu.RPC.php";
        var taxas = document.getElementById("taxas");
        taxas.value = "";
        oParametros = {
            "sExecucao" : "buscaTaxasExercicio",
            "exercicio" : document.form1.anousu.value
        }
        new AjaxRequest(sRPC, oParametros, function(oRetorno, lErro) {
            if (lErro) {
                alert(oRetorno.sMensagem);
                return false;
            }
            var exibeTaxas = document.getElementById("exibeTaxas");
            if (oRetorno.habilitaTaxas == 1) {
                exibeTaxas.style.display = "block";
                taxasSeparadas = 1;
                adicionaTaxas(oRetorno.taxas);
            } else {
                taxasSeparadas = 0;
                exibeTaxas.style.display = "none";
            }
        }).asynchronous(false).execute();
    }

    function adicionaTaxas(elementos) {
        var td = document.getElementById("opcaoCalculo");
        resetaTaxas();
        var ul = document.createElement("ul");

        // Adicionando IPTU
        var x = document.createElement("INPUT");
        var li = document.createElement("li");
        x.setAttribute("type", "checkbox");
        x.setAttribute("tipo", "opcaoTaxa");
        x.setAttribute("value", "IPTU");
        li.append(x);
        li.append("IPTU");
        ul.append(li);

        elementos.each(function(el){
            var x = document.createElement("INPUT");
            var li = document.createElement("li");
            x.setAttribute("type", "checkbox");
            x.setAttribute("tipo", "opcaoTaxa");
            x.setAttribute("value", el.taxa);
            li.append(x);
            li.append(el.descr);
            ul.append(li);
        });
        td.append(ul);
    }

    function resetaTaxas() {
        var td = document.getElementById("opcaoCalculo");
        td.innerHTML ="";
    }
</script>
<?php
$func_nome = new janela('func_nome', '');
$func_nome->posX = 1;
$func_nome->posY = 20;
$func_nome->largura = 770;
$func_nome->altura = 430;
$func_nome->titulo = "Pesquisa";
$func_nome->iniciarVisivel = false;
$func_nome->mostrar();

$cliptubase->erro(true, false);

if ($cliptubase->erro_status != '0') {
    if (isset($calcular)) {
        ?>
        <script>
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_funcnome', 'cad3_conscadastro_002_detalhes.php?solicitacao=Calculo&parametro=<?=$HTTP_POST_VARS['j01_matric']?>', 'Pesquisa', true);
        </script>
        <?php
    } else if (isset($demonstrativo)) {
        ?>
        <script>
            document.form1.text_demo.style.disabled   = true;
            document.form1.text_demo.style.visibility = "visible";
        </script>
        <?php
    }
}

?>
