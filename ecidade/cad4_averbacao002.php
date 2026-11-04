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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$cldb_config             = new cl_db_config;
$claverbaescritura       = new cl_averbaescritura;
$claverbaregimovel       = new cl_averbaregimovel;
$claverbaprocesso        = new cl_averbaprocesso;
$claverbacao             = new cl_averbacao;
$claverbacgm             = new cl_averbacgm;
$claverbacgmold          = new cl_averbacgmold;
$cliptubase              = new cl_iptubase;
$clpropri                = new cl_propri;
$clpromitente            = new cl_promitente;
$clarrematric            = new cl_arrematric;
$cldivida                = new cl_divida;
$claverbadecisaojudicial = new cl_averbadecisaojudicial;
$claverbaformalpartilha  = new cl_averbaformalpartilha;
$claverbaguia            = new cl_averbaguia;
$clcgm                   = new cl_cgm;
$clcfiptu                = new cl_cfiptu;
$clarrenumcgm            = new cl_arrenumcgm;
$clarrehist              = new cl_arrehist;
$clarrecad              = new cl_arrecad;

$claverbaescritura->rotulo->label();
$claverbaformalpartilha->rotulo->label();
$claverbaescritura->rotulo->label();
$claverbadecisaojudicial->rotulo->label();
$clcgm->rotulo->label();
$claverbaguia->rotulo->label();

$camposCfiptu = "j18_desvinculadebitosaverbacao";
$sqlParametrosCfiptu = $clcfiptu->sql_query(db_getsession("DB_anousu"), $camposCfiptu);
$rsParametrosCfiptu = db_query($sqlParametrosCfiptu);
$parametrosCfiptu = db_utils::fieldsMemory($rsParametrosCfiptu,0);
$desvinculaDebitos = $parametrosCfiptu->j18_desvinculadebitosaverbacao == 't';

$debitosEncontrados = [];
$numpresDebitosEncontrados = '';

db_postmemory($_POST);
$db_opcao = 22;
$db_botao = false;

 if(isset($chavepesquisa)){

  $db_opcao = 3;
  $db_botao = true;

  $result = $claverbacao->sql_record($claverbacao->sql_query($chavepesquisa));
  db_fieldsmemory($result,0);

  $result_proc = $claverbaprocesso->sql_record($claverbaprocesso->sql_query($j75_codigo,"j77_codproc,p58_numero, p58_ano, p58_requer"));

  if ($claverbaprocesso->numrows > 0) {

    db_fieldsmemory($result_proc,0);
    $p58_numero = $p58_numero . '/' . $p58_ano;
  }
  $result_reg = $claverbaregimovel->sql_record($claverbaregimovel->sql_query($j75_codigo));
  if ($claverbaregimovel->numrows>0){
    db_fieldsmemory($result_reg,0);
  }
  $result_escr = $claverbaescritura->sql_record($claverbaescritura->sql_query(null,"*",null,"j94_averbacao =".$j75_codigo));
  if ($claverbaescritura->numrows>0){
    db_fieldsmemory($result_escr,0);
  }
	if($j93_averbagrupo == 6){
	  $sqlguia = "select averbaguia.*,averbaguiaitbi.*,it03_nome
							    from averbaguia
							         left join averbaguiaitbi on j104_sequencial = j103_averbaguia
							         left join itbinome       on j104_guia       = it03_guia
							   where j104_averbacao = $chavepesquisa
								   and upper(it03_tipo) = 'C'
								 	 and it03_princ is true ";

	  $resultguia = db_query($sqlguia);
	  $linhasguia = pg_num_rows($resultguia);
	  if($linhasguia>0){
		db_fieldsmemory($resultguia,0);
		$nome = $it03_nome;
		$guia=1;
	  }else{
	  	//se não encotrar é pr é sem guia itbi
			$sqlGuiaSemItbi = "select * from averbaguia where j104_averbacao = $chavepesquisa ";
			$rsGuiaSemItbi  = db_query($sqlGuiaSemItbi);
			$linhasGuiaSemItbi = pg_num_rows($rsGuiaSemItbi);
			if($linhasGuiaSemItbi>0){
				db_fieldsmemory($rsGuiaSemItbi,0);
				$guianao = $j104_guia;
			  $guia = 2;
			}
	  }
	}
	if($j93_averbagrupo == 5){

	  $sqlsentenca = "select *
                      from averbadecisaojudicial
	                   where j101_averbacao = $chavepesquisa";
    $resultsentenca = db_query($sqlsentenca);
	  $linhassentenca = pg_num_rows($resultsentenca);
	  if($linhassentenca>0){
		  db_fieldsmemory($resultsentenca,0);
	  }
	}
	if($j93_averbagrupo == 4){

	  $sqlformal = "select *
                    from averbaformalpartilha
	                       left join averbaformalpartilhacgm on j102_averbaformalpartilha = j100_sequencial
	                 where j100_averbacao = $chavepesquisa";
	  $resultformal = db_query($sqlformal);
	  $linhasformal = pg_num_rows($resultformal);
	  if($linhasformal>0){
		db_fieldsmemory($resultformal,0);
		$z01_numcgm1 = $j102_numcgm;
	  }
	}
    //Busca os Adquirentes
    $sqlGetAdquirentes = "select 
                                j76_codigo as codigo, 
                                z01_numcgm as numcgm, 
                                z01_nome as nome,
                                z01_cgccpf as cgccpf,
                                j163_abreviatura as tipopromitente,  
                                j164_abreviatura as tipoproprietario,
                                j76_principal as isprincipal,
                                j163_tipoproprietario as codigotipoproprietario,
                                j164_tipopromitente as codigotipopromitente,
                                j164_promitipo as promitipo,
                                j163_descricao as descricaoproprietario,
                                j164_descricao as descricaopromitente
                            from averbacgm 
                            inner join cgm on averbacgm.j76_numcgm = cgm.z01_numcgm 
                            left join tipopromitente on averbacgm.j76_tipopromitente = tipopromitente.j164_tipopromitente 
                            left join tipoproprietario on averbacgm.j76_tipoproprietario = tipoproprietario.j163_tipoproprietario 
                            where j76_averbacao = $chavepesquisa";
    $resultGetAdquirentes = db_query($sqlGetAdquirentes);
    $linhasGetAdquirentes = pg_num_rows($resultGetAdquirentes);
    $adquirentes = array();
    for ($w = 0; $w < $linhasGetAdquirentes; $w++) {
        db_fieldsmemory($resultGetAdquirentes, $w);

        $regraProprietarioPromitente = $tipopromitente == '' ? 'Tipo de Promitente' : 'Tipo de Proprietário';
        if (isset($codigotipopromitente)) {
          $codigoTipoProprietarioPromitente = $codigotipoproprietario;
          $abreviaturaProprietarioPromitente = !empty($tipoproprietario) ? $tipoproprietario : $descricaoproprietario;
        } else {
          $codigoTipoProprietarioPromitente = $codigotipopromitente;
          $abreviaturaProprietarioPromitente = !empty($tipopromitente) ? $tipopromitente : $descricaopromitente;
        }

        $adquirentes[$w]['codigo'] = $codigo;
        $adquirentes[$w]['numcgm'] = $numcgm;
        $adquirentes[$w]['nome'] = $nome;
        $adquirentes[$w]['cgccpf'] = $cgccpf;
        $adquirentes[$w]['regra'] = $regraProprietarioPromitente;
        $adquirentes[$w]['codigotipo'] = $codigoTipoProprietarioPromitente;
        $adquirentes[$w]['tipo'] = $abreviaturaProprietarioPromitente;
        $adquirentes[$w]['promitipo'] = $promitipo;
        $adquirentes[$w]['isprincipal'] = $isprincipal == 't' ? 'SIM' : 'NÃO';
    }
    if ($j75_situacao == 2) {
        //Busca os Transmitentes
        $sqlGetTransmitentes = "select 
                                    j79_codigo as codigo, 
                                    z01_numcgm as numcgm, 
                                    z01_nome as nome,
                                    z01_cgccpf as cgccpf,
                                    j163_abreviatura as tipopromitente,  
                                    j164_abreviatura as tipoproprietario,
                                    j79_principal as isprincipal,
                                    j163_tipoproprietario as codigotipoproprietario,
                                    j164_tipopromitente as codigotipopromitente,
                                    j164_promitipo as promitipo,
                                    j163_descricao as descricaoproprietario,
                                    j164_descricao as descricaopromitente
                                from averbacgmold 
                                inner join cgm on averbacgmold.j79_numcgm = cgm.z01_numcgm 
                                left join tipopromitente on averbacgmold.j79_tipopromitente = tipopromitente.j164_tipopromitente 
                                left join tipoproprietario on averbacgmold.j79_tipoproprietario = tipoproprietario.j163_tipoproprietario 
                                where j79_averbacao = $chavepesquisa";
        $resultGetTransmitentes = db_query($sqlGetTransmitentes);
        $linhasGetTransmitentes = pg_num_rows($resultGetTransmitentes);
        $transmitentes = array();
        for ($w = 0; $w < $linhasGetTransmitentes; $w++) {
            db_fieldsmemory($resultGetTransmitentes, $w);

            $regraProprietarioPromitente = $tipopromitente == '' ? 'Tipo de Promitente' : 'Tipo de Proprietário';
            if (isset($codigoTipoProprietarioPromitente)) {
              $codigoTipoProprietarioPromitente = $codigotipoproprietario;
              $abreviaturaProprietarioPromitente = !empty($tipoproprietario) ? $tipoproprietario : $descricaoproprietario;
            } else {
              $codigoTipoProprietarioPromitente = $codigotipopromitente;
              $abreviaturaProprietarioPromitente = !empty($tipopromitente) ? $tipopromitente : $descricaopromitente;
            }

            $transmitentes[$w]['codigo'] = $codigo;
            $transmitentes[$w]['numcgm'] = $numcgm;
            $transmitentes[$w]['nome'] = $nome;
            $transmitentes[$w]['cgccpf'] = $cgccpf;
            $transmitentes[$w]['regra'] = $tipopromitente == '' ? 'Tipo de Promitente' : 'Tipo de Proprietário';
            $transmitentes[$w]['codigotipo'] = $codigoTipoProprietarioPromitente;
            $transmitentes[$w]['tipo'] = $abreviaturaProprietarioPromitente;
            $transmitentes[$w]['promitipo'] = $promitipo;
            $transmitentes[$w]['isprincipal'] = $isprincipal == 't'? 'SIM' : 'NÃO';
        }
    } else {

        $transmitentes = array();
        // Se for tipo proprietario
        if ($j75_regra == 1) {
            //Verifica se possui algum promitente.
            $promitente = $clpromitente->sql_record($clpromitente->sql_query($j75_matric, $j41_numcgm = null, $campos = "*", $ordem = "j41_tipopro desc", $dbwhere = "j41_matric = $j75_matric"));
            //Busca regra configuração da instituição
          
            $dbconfig = $cldb_config->sql_record($cldb_config->sql_query_file(db_getsession("DB_instit"), 'db21_regracgmiptu'));
            db_fieldsmemory($dbconfig, 0);

            if (!isset($sqlerro) || $sqlerro == false) {
                //Se somente proprietario então o transmitente é o proprietario da matricula
                $indice = 0;
                if ($db21_regracgmiptu == 1 || $db21_regracgmiptu == '0' || $clpromitente->numrows == 0) {
                    $iptubase = $cliptubase->sql_record($cliptubase->sql_query_file($j75_matric, "j01_numcgm, j01_tipoproprietario"));
                    db_fieldsmemory($iptubase, 0);
                    $getDescricaoCgm = $clcgm->sql_record($clcgm->sql_query_file($j01_numcgm, "z01_nome, z01_cgccpf"));
                    db_fieldsmemory($getDescricaoCgm, 0);
                    //Busca o tipo do Transmitente
                    $sqlGetTipoTransmitente = "select 
                                            j163_abreviatura, j163_descricao, j163_tipoproprietario
                                        from tipoproprietario 
                                        where j163_tipoproprietario = $j01_tipoproprietario";
                    $resultGetTipoTransmitente = db_query($sqlGetTipoTransmitente);
                    db_fieldsmemory($resultGetTipoTransmitente, 0);

                    if (!isset($sqlerro) || $sqlerro == false) {
                        $transmitentes[$indice]['codigo'] = $j75_codigo;
                        $transmitentes[$indice]['numcgm'] = $j01_numcgm;
                        $transmitentes[$indice]['nome'] = $z01_nome;
                        $transmitentes[$indice]['cgccpf'] = $z01_cgccpf;
                        $transmitentes[$indice]['isprincipal'] = 'SIM';
                        $transmitentes[$indice]['codigotipo'] = isset($j163_tipoproprietario) ? $j163_tipoproprietario : $j163_descricao;
                        $transmitentes[$indice]['tipo'] = !empty($j163_abreviatura) ? $j163_abreviatura : $j163_descricao;
                        $transmitentes[$indice]['promitipo'] = null;
                        $transmitentes[$indice]['regra'] = 'Tipo de Proprietário';
                    }
                    if ($db21_regracgmiptu == 1 || $db21_regracgmiptu == 0 || $clpromitente->numrows == 0) {
                        if (!isset($sqlerro) || $sqlerro == false) {
                            $result_propri = $clpropri->sql_record($clpropri->sql_query($j75_matric));
                            for ($w = 0; $w < $clpropri->numrows; $w++) {
                                db_fieldsmemory($result_propri, $w);
                                $getDescricaoCgm = $clcgm->sql_record($clcgm->sql_query_file($j42_numcgm, "z01_nome, z01_cgccpf"));
                                db_fieldsmemory($getDescricaoCgm, 0);
                                //Busca o tipo do Transmitente
                                $sqlGetTipoTransmitente = "select 
                                                j163_abreviatura, j163_descricao, j163_tipoproprietario
                                            from tipoproprietario 
                                            where j163_tipoproprietario = $j42_tipoproprietario";
                                $resultGetTipoTransmitente = db_query($sqlGetTipoTransmitente);
                                db_fieldsmemory($resultGetTipoTransmitente, 0);

                                if ($sqlerro == false) {
                                    $indice++;
                                    $transmitentes[$indice]['codigo'] = $j75_codigo;
                                    $transmitentes[$indice]['numcgm'] = $j42_numcgm;
                                    $transmitentes[$indice]['nome'] = $z01_nome;
                                    $transmitentes[$indice]['cgccpf'] = $z01_cgccpf;
                                    $transmitentes[$indice]['isprincipal'] = 'NÃO';
                                    $transmitentes[$indice]['codigotipo'] = $j163_tipoproprietario;
                                    $transmitentes[$indice]['tipo'] = !empty($j163_abreviatura) ? $j163_abreviatura : $j163_descricao;
                                    $transmitentes[$indice]['promitipo'] = null;
                                    $transmitentes[$indice]['regra'] = 'Tipo de Proprietário';
                                }
                                
                            }
                        }
                    }
                }
                if (($db21_regracgmiptu == 0 || $db21_regracgmiptu == 2) && $clpromitente->numrows > 0) {
                    if ($sqlerro == false) {
                        for ($w = 0; $w < $clpromitente->numrows; $w++) {
                            db_fieldsmemory($promitente, $w);
                            $getDescricaoCgm = $clcgm->sql_record($clcgm->sql_query_file($j41_numcgm, "z01_nome, z01_cgccpf"));
                            db_fieldsmemory($getDescricaoCgm, 0);
                            if ($sqlerro == false) {
                                $indice++;
                                $transmitentes[$indice]['codigo'] = $j75_codigo;
                                $transmitentes[$indice]['numcgm'] = $j41_numcgm;
                                $transmitentes[$indice]['nome'] = $z01_nome;
                                $transmitentes[$indice]['cgccpf'] = $z01_cgccpf;
                                if ($j41_tipopro == 't') {
                                    $transmitentes[$indice]['isprincipal'] = 'SIM';
                                } else if ($j41_tipopro == 'f') {
                                    $transmitentes[$indice]['isprincipal'] = 'NÃO';
                                }
                                $transmitentes[$indice]['codigotipo'] = $j164_tipopromitente;
                                $transmitentes[$indice]['tipo'] = !empty($j164_abreviatura) ? $j164_abreviatura : $j164_descricao;
                                $transmitentes[$indice]['promitipo'] = $j164_promitipo;
                                $transmitentes[$indice]['regra'] = 'Tipo de Promitente';
                            }
                        }
                    }
                }
            }
            // Se for tipo promitente
        } else if ($j75_regra == 2) {
            //Busca regra configuração da instituição
            $dbconfig = $cldb_config->sql_record($cldb_config->sql_query_file(db_getsession("DB_instit"), 'db21_regracgmiptu'));
            db_fieldsmemory($dbconfig, 0);
            //Busca o Proprietário
            $iptubase = $cliptubase->sql_record($cliptubase->sql_query_file($j75_matric, "j01_numcgm, j01_tipoproprietario"));
            //Busca os outros proprietarios da matricula
            $outrosProprietarios = $clpropri->sql_record($clpropri->sql_query($j75_matric));
            //Busca os Promitentes
            $promitente = $clpromitente->sql_record($clpromitente->sql_query($j75_matric));

            if ($cliptubase->numrows > 0 || $clpromitente->numrows > 0) {
                if (!isset($sqlerro) || $sqlerro == false) {
                    //Busca todos os promitentes e salva eles na averbacgmold, dando a eles o papel de transmitentes.
                    if ($clpromitente->numrows > 0 and $db21_regracgmiptu != 1) {
                        for ($w = 0; $w < $clpromitente->numrows; $w++) {
                            db_fieldsmemory($promitente, $w);
                            $getDescricaoCgm = $clcgm->sql_record($clcgm->sql_query_file($j41_numcgm, "z01_nome, z01_cgccpf"));
                            db_fieldsmemory($getDescricaoCgm, 0);
                            $transmitentes[$w]['codigo'] = $j75_codigo;
                            $transmitentes[$w]['numcgm'] = $j41_numcgm;
                            $transmitentes[$w]['nome'] = $z01_nome;
                            $transmitentes[$w]['cgccpf'] = $z01_cgccpf;
                            $transmitentes[$w]['codigotipo'] = $j164_tipopromitente;
                            $transmitentes[$w]['tipo'] = !empty($j164_abreviatura) ? $j164_abreviatura : $j164_descricao;
                            $transmitentes[$w]['promitipo'] = $j164_promitipo;
                            if ($j41_tipopro == 't') {
                                $transmitentes[$w]['isprincipal'] = 'SIM';
                            } else {
                                $transmitentes[$w]['isprincipal'] = 'NÃO';
                            }
                            $transmitentes[$w]['regra'] = 'Tipo de Promitente';
                        }
                    } else {
                        //Busca o proprietário e salva na averbacgmold, dando a ele o papel de transmitente
                        db_fieldsmemory($iptubase, 0);
                        $getDescricaoCgm = $clcgm->sql_record($clcgm->sql_query_file($j01_numcgm, "z01_nome, z01_cgccpf"));
                        db_fieldsmemory($getDescricaoCgm, 0);
                        //Busca o tipo do Transmitente
                        $sqlGetTipoTransmitente = "select 
                                                        j163_abreviatura, j163_descricao
                                                    from tipoproprietario 
                                                    where j163_tipoproprietario = $j01_tipoproprietario";
                        $resultGetTipoTransmitente = db_query($sqlGetTipoTransmitente);
                        db_fieldsmemory($resultGetTipoTransmitente, 0);

                        $transmitentes[0]['codigo'] = $j75_codigo;
                        $transmitentes[0]['numcgm'] = $j01_numcgm;
                        $transmitentes[0]['nome'] = $z01_nome;
                        $transmitentes[0]['cgccpf'] = $z01_cgccpf;
                        $transmitentes[0]['codigotipo'] = $j163_tipoproprietario;
                        $transmitentes[0]['tipo'] = !empty($j163_abreviatura) ? $j163_abreviatura : $j163_descricao;
                        $transmitentes[0]['promitipo'] = null;
                        $transmitentes[0]['isprincipal'] = 'SIM';
                        $transmitentes[0]['regra'] = 'Tipo de Proprietário';
                        //Busca todos os outros proprietarios e salva eles na averbacgmold, dando a eles o papel de transmitentes.
                        if ($clpropri->numrows > 0) {
                            for ($w = 0; $w < $clpropri->numrows; $w++) {
                                db_fieldsmemory($outrosProprietarios, $w);
                                $getDescricaoCgm = $clcgm->sql_record($clcgm->sql_query_file($j42_numcgm, "z01_nome, z01_cgccpf"));
                                db_fieldsmemory($getDescricaoCgm, 0);
                                //Busca o tipo do Transmitente
                                $sqlGetTipoTransmitente = "select 
                                                        j163_abreviatura, j163_descricao
                                                    from tipoproprietario 
                                                    where j163_tipoproprietario = $j42_tipoproprietario";
                                $resultGetTipoTransmitente = db_query($sqlGetTipoTransmitente);
                                db_fieldsmemory($resultGetTipoTransmitente, 0);
                                $transmitentes[$w + 1]['codigo'] = $j75_codigo;
                                $transmitentes[$w + 1]['numcgm'] = $j42_numcgm;
                                $transmitentes[$w + 1]['nome'] = $z01_nome;
                                $transmitentes[$w + 1]['cgccpf'] = $z01_cgccpf;
                                $transmitentes[$w + 1]['codigotipo'] = $j163_tipoproprietario;
                                $transmitentes[$w + 1]['tipo'] = !empty($j163_abreviatura) ? $j163_abreviatura : $j163_descricao;
                                $transmitentes[$w + 1]['promitipo'] = null;
                                $transmitentes[$w + 1]['isprincipal'] = 'NÃO';
                                $transmitentes[$w + 1]['regra'] = 'Tipo de Proprietário';
                            }
                        }
                    }
                }
            }
        }
    }

  // buscando debitos de inicial do foro
  if ($desvinculaDebitos) {
    $camposPesquisaDebitos = 'arrecad.k00_numpre,cadtipo.k03_descr';
    $wherePesquisaDebitos = "arrematric.k00_matric = {$j75_matric} and arrecad.k00_tipo = 34";
    $sqlBuscaDebitos = $clarrematric->sql_query_info(null,$j75_matric,$camposPesquisaDebitos,null,$wherePesquisaDebitos);
    $rsBuscaDebitos = db_query($sqlBuscaDebitos);
    $debitosEncontrados = db_utils::getCollectionByRecord($rsBuscaDebitos);
    $numpresDebitosEncontrados = implode(
    ',',
    array_unique(array_map(
      function($item){
        return $item->k00_numpre;
      },
      $debitosEncontrados
      ))
    );
  }
}

if(isset($processar)){
  $sqlerro=false;
  db_inicio_transacao();
  $result_tipo = $claverbacao->sql_record($claverbacao->sql_query($j75_codigo,"j75_regra,j75_matric"));

  db_fieldsmemory($result_tipo,0);
  if ($j75_regra==1){
    $result_princ=$cliptubase->sql_record($cliptubase->sql_query_file($j75_matric,"j01_numcgm"));
    db_fieldsmemory($result_princ,0);
    $claverbacgmold->j79_averbacao = $j75_codigo;
    $claverbacgmold->j79_numcgm = $j01_numcgm;
    $claverbacgmold->j79_tipo = 1;
    $claverbacgmold->j79_principal = "1";
    $claverbacgmold->j79_tipoproprietario = getProprietarioPromitenteAverbacao($j01_numcgm, $adquirentes, $transmitentes)['codigotipo'];
    $claverbacgmold->incluir(null);
    if($claverbacgmold->erro_status==0){
      $sqlerro=true;
      $erro_msg = $claverbacgmold->erro_msg;
    }

    $cliptubase->j01_tipoproprietario = getProprietarioPromitenteAverbacao($j01_numcgm, $adquirentes, $transmitentes)['codigotipo'];
    $cliptubase->j01_matric = $j75_matric;
    $cliptubase->alterar($j75_matric);

    if ($sqlerro==false){
      $result_promitente = $clpromitente->sql_record($clpromitente->sql_query($j75_matric));
      for($w=0;$w<$clpromitente->numrows;$w++){
        db_fieldsmemory($result_promitente,$w);
        if ($sqlerro==false){
          $claverbacgmold->j79_averbacao = $j75_codigo;
          $claverbacgmold->j79_numcgm = $j41_numcgm;
          $claverbacgmold->j79_tipo = 2;
          $claverbacgmold->j79_tipopromitente = getProprietarioPromitenteAverbacao($j41_numcgm, $adquirentes, $transmitentes)['codigotipo'];
          if ($j41_tipopro=='t'){
            $claverbacgmold->j79_principal = '1';
          }else if ($j41_tipopro=='f'){
            $claverbacgmold->j79_principal = '0';
          }
          $claverbacgmold->incluir(null);
          if($claverbacgmold->erro_status==0){
            $sqlerro=true;
            $erro_msg = $claverbacgmold->erro_msg;
          }
        }
      }
    }
    if ($sqlerro==false){
      $clpromitente->j41_matric = $j75_matric;
      $clpromitente->excluir($j75_matric);
      if ($clpromitente->erro_status==0){
        $sqlerro=true;
        $erro_msg = $clpromitente->erro_msg;
      }
    }
    if ($sqlerro==false){
      $result_propri = $clpropri->sql_record($clpropri->sql_query($j75_matric));
      for($w=0;$w<$clpropri->numrows;$w++){
        db_fieldsmemory($result_propri,$w);
        if ($sqlerro==false){
          $claverbacgmold->j79_averbacao = $j75_codigo;
          $claverbacgmold->j79_numcgm = $j42_numcgm;
          $claverbacgmold->j79_tipo = 1;
          $claverbacgmold->j79_principal = '0';
          $claverbacgmold->j79_tipoproprietario = getProprietarioPromitenteAverbacao($j42_numcgm, $adquirentes, $transmitentes)['codigotipo'];
          $claverbacgmold->incluir(null);
          if($claverbacgmold->erro_status==0){
            $sqlerro=true;
            $erro_msg = $claverbacgmold->erro_msg;
            break;
          }
        }
      }
    }
    if ($sqlerro==false){
      $clpropri->j42_matric = $j75_matric;
      $clpropri->excluir($j75_matric);
      if ($clpropri->erro_status==0){
        $sqlerro=true;
        $erro_msg = $clpropri->erro_msg;
      }
    }
    if ($sqlerro==false){
      $result_new_propri = $claverbacgm->sql_record($claverbacgm->sql_query_file(null,"*",null,"j76_averbacao=$j75_codigo"));
      for($w=0;$w<$claverbacgm->numrows;$w++){
        db_fieldsmemory($result_new_propri,$w);
        if ($sqlerro==false){
          if ($j76_principal=="f"){
            $clpropri->j42_tipoproprietario = getProprietarioPromitenteAverbacao($j76_numcgm, $adquirentes, $transmitentes)['codigotipo'];
            $clpropri->incluir($j75_matric,$j76_numcgm);
            if ($clpropri->erro_status==0){
              $sqlerro=true;
              $erro_msg = $clpropri->erro_msg;
            }
          }else if ($j76_principal=="t"){
            $cliptubase->j01_numcgm=$j76_numcgm;
            $cliptubase->j01_matric=$j75_matric;
            $cliptubase->j01_tipoproprietario = getProprietarioPromitenteAverbacao($j76_numcgm, $adquirentes, $transmitentes)['codigotipo'];
            $cliptubase->alterar($j75_matric);
            if ($cliptubase->erro_status==0){
              $sqlerro=true;
              $erro_msg=$cliptubase->erro_msg;
            }

            if ($desvinculaDebitos && $sqlerro == false && !empty($vinculadebitoscgm) && !empty($numpredebitos)) {
              $aNumpres = explode(',',$numpredebitos);

              foreach($aNumpres as $numpreDebito) {
                $clarrematric->excluir($numpreDebito,$j75_matric);
                if ($clarrematric->erro_status == 0) {
                  $sqlerro=true;
                  $erro_msg = $clarrematric->erro_msg;
                }

                $clarrenumcgm->incluir($vinculadebitoscgm,$numpreDebito);
                if ($clarrenumcgm->erro_status == 0) {
                  $sqlerro=true;
                  $erro_msg = $clarrenumcgm->erro_msg;
                }

                $camposArrecad = 'arrecad.k00_numpre,arrecad.k00_numpar,arrecad.k00_hist,arrecad.k00_dtoper,histcalc.k01_codigo';
                $sqlInformacaoDebitos = $clarrecad->sql_query(null, $camposArrecad, null, "arrecad.k00_numpre = {$numpreDebito}");
                $rsInformacaoDebitos = db_query($sqlInformacaoDebitos);
                $informacaoDebitos = db_utils::getCollectionByRecord($rsInformacaoDebitos);

                foreach($informacaoDebitos as $informacaoDebito) { 
                  $clarrehist->k00_numpre = $informacaoDebito->k00_numpre;
                  $clarrehist->k00_numpar = $informacaoDebito->k00_numpar;
                  $clarrehist->k00_hist = $informacaoDebito->k00_hist;
                  $clarrehist->k00_dtoper = $informacaoDebito->k00_dtoper;
                  $clarrehist->k00_hora = db_hora();
                  $clarrehist->k00_limithist = date("Y-m-d",db_getsession("DB_datausu"));
                  $clarrehist->k00_idhist = $informacaoDebito->k01_codigo;
                  $clarrehist->k00_id_usuario = db_getsession("DB_id_usuario");
                  $clarrehist->k00_histtxt = "Débito transferido da matrícula {$j75_matric} para o CGM {$vinculadebitoscgm} pela averbação de lote.";
                  $clarrehist->incluir(null);
                  if ($clarrehist->erro_status == 0) {
                    $sqlerro=true;
                    $erro_msg = $clarrehist->erro_msg;
                  }
                }
              }

            } else if ($sqlerro==false){
              $result_div = $clarrematric->sql_record($clarrematric->sql_query_div(null,null,"*",null,"arrematric.k00_matric=$j75_matric"));
              $numrows_div = $clarrematric->numrows;
              for($x=0;$x<$numrows_div;$x++){
                db_fieldsmemory($result_div,$x);
                if ($sqlerro==false){
                  $cldivida->v01_numcgm = $j76_numcgm;
                  $cldivida->v01_coddiv = $v01_coddiv;
                  $cldivida->alterar($v01_coddiv);
                  if ($cldivida->erro_status==0){
                    $sqlerro=true;
                    $erro_msg=$cldivida->erro_msg;
                  }
                }
              }
            }
          }
        }
      }
    }
  }else if ($j75_regra==2){
    $result_promitente = $clpromitente->sql_record($clpromitente->sql_query($j75_matric));
    if ($clpromitente->numrows>0){
      if ($sqlerro==false){
        for($w=0;$w<$clpromitente->numrows;$w++){
          db_fieldsmemory($result_promitente,$w);
          if ($sqlerro==false){
            $claverbacgmold->j79_averbacao = $j75_codigo;
            $claverbacgmold->j79_numcgm = $j41_numcgm;
            $claverbacgmold->j79_tipo = 2;
            $claverbacgmold->j79_tipopromitente = getProprietarioPromitenteAverbacao($j41_numcgm, $adquirentes, $transmitentes)['codigotipo'];
            if ($j41_tipopro=='t'){
              $claverbacgmold->j79_principal = 1;
            }else if ($j41_tipopro=='f'){
              $claverbacgmold->j79_principal = "0";
            }
            $claverbacgmold->incluir(null);
            if($claverbacgmold->erro_status==0){
              $sqlerro=true;
              $erro_msg = $claverbacgmold->erro_msg;
            }
          }
        }
      }
      if ($sqlerro==false){
        $clpromitente->j41_matric = $j75_matric;
        $clpromitente->excluir($j75_matric);
        if ($clpromitente->erro_status==0){
          $sqlerro=true;
          $erro_msg = $clpromitente->erro_msg;
       }
      }
    }else{
      $result_princ=$cliptubase->sql_record($cliptubase->sql_query_file($j75_matric,"j01_numcgm"));
      db_fieldsmemory($result_princ,0);
      $claverbacgmold->j79_averbacao = $j75_codigo;
      $claverbacgmold->j79_numcgm = $j01_numcgm;
      $claverbacgmold->j79_tipo = 1;
      $claverbacgmold->j79_principal = "1";
      $claverbacgmold->j79_tipoproprietario = getProprietarioPromitenteAverbacao($j01_numcgm, $adquirentes, $transmitentes)['codigotipo'];
      $claverbacgmold->incluir(null);
      if($claverbacgmold->erro_status==0){
        $sqlerro=true;
        $erro_msg = $claverbacgmold->erro_msg;
      }
    }
    if ($sqlerro==false){
      $result_new_promitente = $claverbacgm->sql_record($claverbacgm->sql_query_file(null,"*",null,"j76_averbacao=$j75_codigo"));
      for($w=0;$w<$claverbacgm->numrows;$w++){
        db_fieldsmemory($result_new_promitente,$w);

        $sSqlBuscaPromitente = $clpromitente->sql_query_file($j75_matric, $j76_numcgm);
        $rsBuscaPromitente = db_query($sSqlBuscaPromitente);
        $promitenteNaoCadastrado = pg_num_rows($rsBuscaPromitente) == 0;

        if ($sqlerro==false && $promitenteNaoCadastrado){

          $clpromitente->j41_promitipo = 'C';
          if ($j76_principal=='t'){
            $clpromitente->j41_tipopro  = 1;
          }else if ($j76_principal=='f'){
            $clpromitente->j41_tipopro  ="0";
          }

          $clpromitente->j41_promitipo = getProprietarioPromitenteAverbacao($j76_numcgm, $adquirentes, $transmitentes)['promitipo'];
          $clpromitente->j41_tipopromitente = getProprietarioPromitenteAverbacao($j76_numcgm, $adquirentes, $transmitentes)['codigotipo'];
          $clpromitente->incluir($j75_matric,$j76_numcgm);
          if ($clpromitente->erro_status==0){
            $sqlerro=true;
            $erro_msg = $clpromitente->erro_msg;
          }
          $result_ativa_trigger = $cliptubase->sql_record($cliptubase->sql_query_file($j75_matric));
          db_fieldsmemory($result_ativa_trigger,0);
          $cliptubase->j01_numcgm = $j01_numcgm;
          $cliptubase->j01_matric = $j01_matric;
          $cliptubase->alterar($j75_matric);
          if ($cliptubase->erro_status==0){
            $sqlerro=true;
            $erro_msg=$cliptubase->erro_msg;
          }
        }
      }
    }
  }
  if ($sqlerro==false){
    $claverbacao->j75_situacao = 2;
    $claverbacao->j75_codigo = $j75_codigo;
    $claverbacao->j75_responsavel = empty($j75_responsavel)?db_getsession("DB_id_usuario"):$j75_responsavel;
    
    $claverbacao->alterar($j75_codigo);
    if($claverbacao->erro_status==0){
      $sqlerro=true;
      $erro_msg = $claverbacao->erro_msg;
    }
  }
  
  if($sqlerro==false) {
    $daoIptubaseregimovel = new cl_iptubaseregimovel();
    $sql = $daoIptubaseregimovel->sql_query_file(null, "j04_sequencial", null, "j04_matric = {$j75_matric}");
    $rs = db_query($sql);
    if(!$rs) {
      $sqlerro=true;
      $erro_msg = "Erro ao buscar o registro de imóvel.";
    } else {
      if(pg_num_rows($rs) > 0) {
        $j04_sequencial = db_utils::fieldsMemory($rs, 0)->j04_sequencial;
        
        $daoIptubaseregimovel->j04_sequencial = $j04_sequencial;
        $daoIptubaseregimovel->j04_matricregimo = $j78_matric;
        $daoIptubaseregimovel->alterar($j04_sequencial);
        
        if($daoIptubaseregimovel->erro_status==0) {
          $sqlerro = true;
          $erro_msg = "Erro ao atualizar a Matrícula do Registro do Imóvel.";
        }
      }
    }
  }

  db_fim_transacao($sqlerro);
  $db_opcao = 3;
  $db_botao = true;
}

$sql="select nome as j75_responsavel_a from db_usuarios where id_usuario=$j75_responsavel";

db_fieldsmemory(db_query($sql),0);

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
  <div class="container">
    <?php

      $botaoprocessar = true;
      require_once(modification("forms/db_frmaverbacao.php"));
    ?>
  </div>

  <?php
   if(isset($chavepesquisa)){
     if (count($transmitentes) > 0) {
  ?>
    <div class='container'>
      <fieldset>
        <legend style="font-weight: bold">Transmitentes</legend>
      <table class="form-container" border="1" style="width: 850px">
          <thead>
              <tr style="background-color: LightGray; font-weight: bold">
                <td class="text-center field-size4">Numcgm</td>
                <td class="text-center field-size8">Nome</td>
                <td class="text-center field-size4">CPF / CNPJ</td>
                <td class="text-center field-size4">Tipo</td>
                <td class="text-center field-size4">Principal</td>
              </tr>
            </thead>
            <tbody>

            <?php
              foreach($transmitentes as $transmitente){
            ?>
              <tr>
                <td class="text-center"><?= array_key_exists('numcgm', $transmitente) ? $transmitente['numcgm'] : ''?></td>
                <td class="text-center"><?= array_key_exists('nome', $transmitente) ? $transmitente['nome'] : ''?></td>
                <td class="text-center"><?= array_key_exists('cgccpf', $transmitente) ? $transmitente['cgccpf'] : ''?></td>
                <td class="text-center"><?= array_key_exists('tipo', $transmitente) ? $transmitente['tipo'] : ''?></td>
                <td class="text-center"><?= array_key_exists('isprincipal', $transmitente) ? $transmitente['isprincipal'] : ''?></td>
              </tr>
            <?php
              }
            ?>
            
            </tbody>
          </table>
        </fieldset>
    </div>
  <?php

     }

     if (count($adquirentes) > 0) {
    ?>
    <div class='container'>
      <fieldset>
        <legend style="font-weight: bold">Adquirentes</legend>
      <table class="form-container" border="1" style="width: 850px">
          <thead>
              <tr style="background-color: LightGray; font-weight: bold">
                <td class="text-center field-size4">Numcgm</td>
                <td class="text-center field-size8">Nome</td>
                <td class="text-center field-size4">CPF / CNPJ</td>
                <td class="text-center field-size4">Tipo</td>
                <td class="text-center field-size4">Principal</td>
              </tr>
            </thead>
            <tbody>

            <?php
              foreach($adquirentes as $adquirente){
            ?>
              <tr>
                <td class="text-center"><?= array_key_exists('numcgm', $adquirente) ? $adquirente['numcgm'] : ''?></td>
                <td class="text-center"><?= array_key_exists('nome', $adquirente) ? $adquirente['nome'] : ''?></td>
                <td class="text-center"><?= array_key_exists('cgccpf', $adquirente) ? $adquirente['cgccpf'] : ''?></td>
                <td class="text-center"><?= array_key_exists('tipo', $adquirente) ? $adquirente['tipo'] : ''?></td>
                <td class="text-center"><?= array_key_exists('isprincipal', $adquirente) ? $adquirente['isprincipal'] : ''?></td>
              </tr>
            <?php
              }
            ?>
            </tbody>
          </table>
        </fieldset>
    </div>
    <?php

     }
    }
  ?>

  <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  ?>
</body>
</html>
<?php
if(isset($processar)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    if($claverbacao->erro_campo!=""){
      echo "<script> document.form1.".$claverbacao->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$claverbacao->erro_campo.".focus();</script>";
    };
  }else{
    db_msgbox("Averbação Processada!");
    echo "<script>location.href = 'cad4_averbacao002.php';</script>";
  }
}

if($db_opcao==22||$db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}

function getProprietarioPromitenteAverbacao($numCgm, $adquirentes, $transmitentes) {
  foreach ($adquirentes as $adquirente) {
    if ($adquirente['numcgm'] == $numCgm) {
      return $adquirente;
    }
  }

  foreach ($transmitentes as $transmitente) {
    if ($transmitente['numcgm'] == $numCgm) {
      return $transmitente;
    }
  }

  return null;
}
?>
