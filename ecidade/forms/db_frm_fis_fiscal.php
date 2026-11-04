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

//MODULO: fiscal
require_once(modification("classes/db_ruas_classe.php"));
require_once(modification("classes/db_bairro_classe.php"));
$clruas = new cl_ruas;
$clbairro = new cl_bairro;
$clfiscal->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("descrdepto");
$clrotulo->label("y70_codvist");
$clrotulo->label("y80_codsani");
$clrotulo->label("z01_numcgm");
$clrotulo->label("j01_matric");
$clrotulo->label("q02_inscr");
$clrotulo->label("j14_codigo");
$clrotulo->label("j14_nome");
$clrotulo->label("j13_codi");
$clrotulo->label("j13_descr");
$clrotulo->label("y12_codigo");
$clrotulo->label("y12_codi");
$clrotulo->label("y12_numero");
$clrotulo->label("y12_compl");
$clrotulo->label("y13_codigo");
$clrotulo->label("y13_codi");
$clrotulo->label("y13_numero");
$clrotulo->label("y13_compl");
$clrotulo->label("y100_sequencial");
$clrotulo->label("z01_nome");
$get="";
?>
<form name="form1" method="post" action="">
    <?php 
    $rua="";
    $bairro="";
    $numero="";
    $compl="";

    if ($db_opcao==2){
        $result = $clfiscal->sql_record($clfiscal->sql_query_info($y30_codnoti,"identifica,codigo"));
        if($clfiscal->numrows > 0){
            db_fieldsmemory($result,0);
        }
    }
/*
if(isset($y30_codnoti) && $y30_codnoti != "" && $db_opcao != 1){
  $result = $clfiscal->sql_record($clfiscal->sql_querycgm($y30_codnoti));
  if($clfiscal->numrows > 0){
    db_fieldsmemory($result,0);
  }
}
*/
    if(isset($procfiscal) && $procfiscal != '') {
      // db_input('procfiscal',5,@$Iy100_sequencial,true,'hidden',1,'');
        $sqlProcFiscal = "select y100_sequencial,z01_numcgm,z01_nome,y103_inscr as q02_inscr,
        y102_matric as j01_matric, y104_codsani as y80_codsani, y70_codvist
        from fiscalizacao.fis_procfiscal
        left join fiscalizacao.fis_procfiscalcgm on fis_procfiscalcgm.y101_procfiscal = fis_procfiscal.y100_sequencial
        left join cgm on fis_procfiscalcgm.y101_numcgm = cgm.z01_numcgm
        left join fiscalizacao.fis_procfiscalmatric on fis_procfiscalmatric.y102_procfiscal = fis_procfiscal.y100_sequencial
        left join fiscalizacao.fis_procfiscalinscr on fis_procfiscalinscr.y103_procfiscal = fis_procfiscal.y100_sequencial
        left join fiscalizacao.fis_procfiscalsani on fis_procfiscalsani.y104_procfiscal = fis_procfiscal.y100_sequencial
        left join fiscalizacao.fis_procfiscalvistorias on fis_procfiscalvistorias.y109_procfiscal = fis_procfiscal.y100_sequencial
        left join fiscalizacao.fis_vistorias on fis_vistorias.y70_codvist = fis_procfiscalvistorias.y109_codvist
        where fis_procfiscal.y100_sequencial = $procfiscal";
        $resultProcFiscal = pg_query($sqlProcFiscal);
        $linhas = pg_num_rows($resultProcFiscal);
            if ($linhas != 0) {
                db_fieldsmemory($resultProcFiscal, 0);
            }
        if($q02_inscr != ""){
          unset($z01_numcgm );
        }elseif($j01_matric != ""){
          unset($z01_numcgm );
        }elseif($y80_codsani != ""){
          unset($z01_numcgm );
        }

  // if (isset($y80_codsani)  && $y80_codsani  != "") {

  //   include(modification("classes/db_fis_sanitario_classe.php"));
  //   $clsanitarioEndereco = new cl_fis_sanitario;
  //   $resultEndereco = $clsanitarioEndereco->sql_record($clsanitarioEndereco->sql_query($y80_codsani));
  //   if($clsanitarioEndereco->numrows > 0){
  //     db_fieldsmemory($resultEndereco,0);
  //     $rua=$y80_codrua;
  //     $bairro=$y80_codbairro;
  //     $numero=$y80_numero;
  //     $compl=$y80_compl;
  //   }

  //   $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('fis3_fis_consultasani002.php?y80_codsani=$y80_codsani');return false;\" href=''>sanitário: ".$y80_codsani." &nbsp;|&nbsp;".@$z01_nome."</a>";
  // } elseif(isset($y70_codvist) && $y70_codvist != "") {
  //   include(modification("classes/db_fis_vistlocal_classe.php"));
  //   $clvistlocalEndereco = new cl_fis_vistlocal;
  //   $resultEndereco = $clvistlocalEndereco->sql_record($clvistlocalEndereco->sql_query($y70_codvist));
  //   if($clvistlocalEndereco->numrows > 0){
  //     db_fieldsmemory($resultEndereco,0);
  //     $rua=$y10_codigo;
  //     $bairro=$y10_codi;
  //     $numero=$y10_numero;
  //     $compl=$y10_compl;
  //   }

  //   $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('fis3_fis_consultavist002.php?y70_codvist=$y70_codvist');return false;\" href=''>vistoria: ".$y70_codvist." &nbsp;|&nbsp;".@$z01_nome."</a>";
  // } elseif(isset($j01_matric) && $j01_matric != "") {
  //   include(modification("classes/db_iptubase_classe.php"));
  //   $cliptubaseEndereco = new cl_iptubase;
  //   $resultEndereco = $cliptubaseEndereco->sql_record($cliptubaseEndereco->proprietario_query($j01_matric));
  //   if($cliptubaseEndereco->numrows > 0){
  //     db_fieldsmemory($resultEndereco,0);
  //     $rua=$j14_codigo;
  //     $bairro=$j34_bairro;
  //     $numero=$j39_numero;
  //     $compl=$j39_compl;
  //   }
  //   $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('cad3_conscadastro_002.php?cod_matricula=$j01_matric');return false\" href=''>matrícula: ".$j01_matric." &nbsp;|&nbsp;".@$z01_nome."</a>";
  // } elseif(isset($q02_inscr)  && $q02_inscr  != "") {

  //   include(modification("classes/db_issbase_classe.php"));
  //   $clissbaseEndereco = new cl_issbase;
  //   $resultEndereco = $clissbaseEndereco->sql_record($clissbaseEndereco->empresa_query($q02_inscr));
  //   if ($clissbaseEndereco->numrows > 0) {
  //     db_fieldsmemory($resultEndereco,0);
  //     $rua=$q02_lograd;
  //     $bairro=$q02_bairro;
  if( $rua != "" ){
    $sqlRuaNome = 'select j14_nome as j14_nome_exec from ruas where j14_codigo = '.$rua;
    $rsRuaNome  = db_query($sqlRuaNome);
    db_fieldsmemory($rsRuaNome,0);
  }

  if( $bairro != "" ){
    $sqlRuaBairro = 'select j13_descr as j13_descr_exec from bairro where j13_codi = '.$bairro;
    $rsRuaBairro  = db_query($sqlRuaBairro);
    db_fieldsmemory($rsRuaBairro,0);
  }
  //     $numero=$z01_numero;
  //     $compl=$z01_compl;
  //   }

  //   $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('iss3_consinscr003.php?numeroDaInscricao=$q02_inscr');return false\" href=''>inscrição: ".$q02_inscr." &nbsp;|&nbsp;".@$z01_nome."</a>";
  // } else {
  //     if(isset($z01_numcgm) && $z01_numcgm != ''){
  //       include(modification("classes/db_cgm_classe.php"));
  //       $clcgmEndereco = new cl_cgm;
  //       $resultEnderecoCGM = $clcgmEndereco->sql_record($clcgmEndereco->sql_query($z01_numcgm));
  //       if($clcgmEndereco->numrows > 0){
  //         db_fieldsmemory($resultEnderecoCGM,0);
  //         $numero=$z01_numero;
  //         $compl=$z01_compl;
  //       }
  //       include(modification("classes/db_db_cgmruas_classe.php"));
  //       $clcgmruasEndereco = new cl_db_cgmruas;
  //       $resultEnderecoRuas = $clcgmruasEndereco->sql_record($clcgmruasEndereco->sql_query($z01_numcgm));
  //       if($clcgmruasEndereco->numrows > 0){
  //         db_fieldsmemory($resultEnderecoRuas,0);
  //         $rua=$j14_codigo;
  //       }
  //       include(modification("classes/db_db_cgmbairro_classe.php"));
  //       $clcgmbairroEndereco = new cl_db_cgmbairro;
  //       $resultEnderecoBairro = $clcgmbairroEndereco->sql_record($clcgmbairroEndereco->sql_query($z01_numcgm));
  //       if($clcgmbairroEndereco->numrows > 0){
  //         db_fieldsmemory($resultEnderecoBairro,0);
  //         $bairro=$j13_codi;
  //       }

  //       $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('prot3_conscgm002.php?fechar=func_nome&numcgm=$z01_numcgm');return false\" href=''>CGM: ".$z01_numcgm." &nbsp;|&nbsp;".@$z01_nome."</a>";
  //     }
  // }
    } // Fim código para extensão

    if(isset($j01_matric) && $j01_matric != "" || (isset($identifica)&&$identifica=="Matrícula")){
        if(isset($identifica)){
            $j01_matric=$codigo;
        }

        db_input('j01_matric',5,$Ij01_matric,true,'hidden',1,"");
        include(modification("classes/db_iptubase_classe.php"));
        $cliptubase = new cl_iptubase;
        $get = "&tipo=y102_matric&valor=$j01_matric";
        $result = $cliptubase->sql_record($cliptubase->sql_query($j01_matric));
        if($cliptubase->numrows > 0){
            db_fieldsmemory($result,0);
            include(modification("classes/db_cgm_classe.php"));
            $clcgm = new cl_cgm;
            $result = $clcgm->sql_record($clcgm->sql_query($j01_numcgm));
            if($clcgm->numrows > 0){
                db_fieldsmemory($result,0);
            }
        }

        if ($db_opcao==1){
            $result = $cliptubase->sql_record($cliptubase->proprietario_query($j01_matric));
            if($cliptubase->numrows > 0){
                db_fieldsmemory($result,0);
                $rua=$j14_codigo;
                $bairro=$j34_bairro;
                $numero=$j39_numero;
                $compl=$j39_compl;
            }
        }

        $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('cad3_conscadastro_002.php?cod_matricula=$j01_matric');return false\" href=''>matrícula: ".$j01_matric." &nbsp;|&nbsp;".@$z01_nome."</a>";

    }elseif(isset($q02_inscr)  && $q02_inscr  != ""|| (isset($identifica)&&$identifica=="Inscrição")){
        if(isset($identifica)){
            $q02_inscr=$codigo;
        }

        db_input('q02_inscr',5,$Iq02_inscr,true,'hidden',1,"");
        include(modification("classes/db_issbase_classe.php"));
        $clissbase = new cl_issbase;
        $get = "&tipo=y103_inscr&valor=$q02_inscr";
        $result = $clissbase->sql_record($clissbase->sql_query($q02_inscr));
        if($clissbase->numrows > 0){
            db_fieldsmemory($result,0);
            include(modification("classes/db_cgm_classe.php"));
            $clcgm = new cl_cgm;
            $result = $clcgm->sql_record($clcgm->sql_query($q02_numcgm));
            if($clcgm->numrows > 0){
                db_fieldsmemory($result,0);
            }
        }

        if ($db_opcao==1){
            $result = $clissbase->sql_record($clissbase->empresa_query($q02_inscr));
            if($clissbase->numrows > 0){
                db_fieldsmemory($result,0);
                $rua=$q02_lograd;
                $bairro=$q02_bairro;
    $sqlRuaNome = 'select j14_nome as j14_nome_exec from ruas where j14_codigo = '.$rua;
    $rsRuaNome  = db_query($sqlRuaNome);
    db_fieldsmemory($rsRuaNome,0);

    $sqlRuaBairro = 'select j13_descr as j13_descr_exec from bairro where j13_codi = '.$bairro;
    $rsRuaBairro  = db_query($sqlRuaBairro);
    db_fieldsmemory($rsRuaBairro,0);

                $numero=$z01_numero;
                $compl=$z01_compl;
            }
        }

        $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('iss3_consinscr003.php?numeroDaInscricao=$q02_inscr');return false\" href=''>inscrição: ".$q02_inscr." &nbsp;|&nbsp;".@$z01_nome."</a>";

    }elseif(isset($y80_codsani)  && $y80_codsani  != "" || (isset($identifica)&&$identifica=="Sanitário")){
    	if(isset($identifica)){
    		$y80_codsani=$codigo;
    	}

        db_input('y80_codsani',5,$Iy80_codsani,true,'hidden',1,"");
        include(modification("classes/db_fis_sanitario_classe.php"));
        $clsanitario = new cl_fis_sanitario;
        $get = "&tipo=y104_codsani&valor=$y80_codsani";
        $result = $clsanitario->sql_record($clsanitario->sql_query($y80_codsani));

        if($clsanitario->numrows > 0){
            db_fieldsmemory($result,0);
            if($db_opcao==1){
                $rua=$y80_codrua;
                $bairro=$y80_codbairro;
                $numero=$y80_numero;
                $compl=$y80_compl;
            }
            include(modification("classes/db_cgm_classe.php"));
            $clcgm = new cl_cgm;
            $result = $clcgm->sql_record($clcgm->sql_query($y80_numcgm));

            if($clcgm->numrows > 0){
                db_fieldsmemory($result,0);
            }
        }

        $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('fis3_fis_consultasani002.php?y80_codsani=$y80_codsani');return false;\" href=''>sanitário: ".$y80_codsani." &nbsp;|&nbsp;".@$z01_nome."</a>";


    }elseif(isset($y70_codvist)  && $y70_codvist != "" || (isset($identifica)&&$identifica=="Vistorias")){
    	if(isset($identifica)){
    		$y70_codvist=$codigo;
    	}

        db_input('y70_codvist',5,$Iy70_codvist,true,'hidden',1,"");
        include(modification("classes/db_fis_vistorias_classe.php"));
        $clvistorias = new cl_fis_vistorias;
        $result = $clvistorias->sql_record($clvistorias->sql_query($y70_codvist));

        if($clvistorias->numrows > 0){
            db_fieldsmemory($result,0);
            if ($db_opcao==1){
                include(modification("classes/db_fis_vistlocal_classe.php"));
                $clvistlocal = new cl_fis_vistlocal;
                $result = $clvistlocal->sql_record($clvistlocal->sql_query($y70_codvist));
                if($clvistlocal->numrows > 0){
                    db_fieldsmemory($result,0);
                    $rua=$y10_codigo;
                    $bairro=$y10_codi;
                    $numero=$y10_numero;
                    $compl=$y10_compl;
                }
            }
            include(modification("classes/db_fis_vistinscr_classe.php"));
            $clvistinscr = new cl_fis_vistinscr;
            $result = $clvistinscr->sql_record($clvistinscr->sql_query($y70_codvist));
            if($clvistinscr->numrows > 0){
                db_fieldsmemory($result,0);
            }

            include(modification("classes/db_fis_vistmatric_classe.php"));
            $clvistmatric = new cl_fis_vistmatric;
            $result = $clvistmatric->sql_record($clvistmatric->sql_query($y70_codvist));
            if($clvistmatric->numrows > 0){
                db_fieldsmemory($result,0);
            }
            include(modification("classes/db_fis_vistsanitario_classe.php"));
            $clvistsanitario = new cl_fis_vistsanitario;
            $result = $clvistsanitario->sql_record($clvistsanitario->sql_query($y70_codvist));
            if($clvistsanitario->numrows > 0){
                db_fieldsmemory($result,0);
            }
            include(modification("classes/db_fis_vistcgm_classe.php"));
            $clvistcgm = new cl_fis_vistcgm;
            $result = $clvistcgm->sql_record($clvistcgm->sql_query($y70_codvist));
            if($clvistcgm->numrows > 0){
                db_fieldsmemory($result,0);
            }
            include(modification("classes/db_cgm_classe.php"));
            $clcgm = new cl_cgm;
            $result = $clcgm->sql_record($clcgm->sql_query($z01_numcgm));
            if($clcgm->numrows > 0){
                db_fieldsmemory($result,0);
            }
        }

        $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('fis3_fis_consultavist002.php?y70_codvist=$y70_codvist');return false;\" href=''>vistoria: ".$y70_codvist." &nbsp;|&nbsp;".@$z01_nome."</a>";

    }elseif(isset($z01_numcgm) && $z01_numcgm != "" || (isset($identifica)&&$identifica=="Cgm")){
        if(isset($identifica)){
            $z01_numcgm=$codigo;
        }

        db_input('z01_numcgm',5,$Iz01_numcgm,true,'hidden',1,"");
        include(modification("classes/db_cgm_classe.php"));
        $clcgm = new cl_cgm;
        $get = "&tipo=y101_numcgm&valor=$z01_numcgm";
        $result = $clcgm->sql_record($clcgm->sql_query($z01_numcgm));
        if($clcgm->numrows > 0){
            db_fieldsmemory($result,0);
            $numero=$z01_numero;
            $compl=$z01_compl;
        }

        include(modification("classes/db_db_cgmruas_classe.php"));
        $clcgmruas = new cl_db_cgmruas;
        $result = $clcgmruas->sql_record($clcgmruas->sql_query($z01_numcgm));
        if($clcgmruas->numrows > 0){
            db_fieldsmemory($result,0);
            $rua=$j14_codigo;
        }

        include(modification("classes/db_db_cgmbairro_classe.php"));
        $clcgmbairro = new cl_db_cgmbairro;
        $result = $clcgmbairro->sql_record($clcgmbairro->sql_query($z01_numcgm));
        if($clcgmbairro->numrows > 0){
            db_fieldsmemory($result,0);
            $bairro=$j13_codi;
        }

        $dados = "<a style='text-decoration:none;color:#6699cc;background-color:yellow' onMouseOver='this.style.color=\"blue\"' onMouseOut='this.style.color=\"#6699cc\"' onClick=\"js_abre('prot3_conscgm002.php?fechar=func_nome&numcgm=$z01_numcgm');return false\" href=''>CGM: ".$z01_numcgm." &nbsp;|&nbsp;".@$z01_nome."</a>";

    }

// Ticket 108335
if(isset($intimacao) && $intimacao == 1){
    $Ty30_codnoti = 'Código da Intimação

    Campo:y30_codnoti                             ';

    $Ty30_data = 'Data da Intimação

    Campo:y30_data                             ';

    $Ty30_hora = 'Hora da Intimação

    Campo:y30_hora                             ';

    $Ty30_obs = 'Código da Intimação

    Campo:y30_codnoti                             ';

    $Ty30_nome = 'Nome da Pessoa Intimada

    Campo:y30_nome                             ';

    $Ly30_codnoti = '<strong>Código da Intimação:</strong>';
    $Ly30_data    = '<strong>Data da Intimação:</strong>';
    $Ly30_hora    = '<strong>Hora da Intimação:</strong>';
    $Ly30_obs     = '<strong>Observação da Intimação:</strong>';
    $Ly30_nome    = '<strong>Nome da Pessoa Intimada:</strong>';

}
// -------------
?>

<center>
    <table border="0">
      <tr>
        <td nowrap title="<?=@$Ty30_codnoti?>">
         <?=@$Ly30_codnoti?>
     </td>
     <td>
        <?php 
        db_input('y30_codnoti',20,$Iy30_codnoti,true,'text',3,"");
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>&nbsp;".@$dados."&nbsp;</strong>";
        ?>
    </td>
</tr>
<!-- <tr>
    <td nowrap title="<?=@$Ty30_numbloco?>">
     <?=@$Ly30_numbloco?>
 </td>
 <td>
    <?php 
    db_input('y30_numbloco',20,$Iy30_numbloco,true,'text',$db_opcao,"");
    ?><b>
    Prazo p/ Recurso:
</b><?php 
db_inputdata('y30_prazorec',@$y30_prazorec_dia,@$y30_prazorec_mes,@$y30_prazorec_ano,true,'text',$db_opcao,"");
?>
</td>
</tr> -->
<tr style="display:none">
    <td nowrap title="<?=@$Ty30_data?>">
     <?=@$Ly30_data?>
 </td>
 <td>
    <?php 
    if(empty($y30_data_dia)){
      $y30_data_dia = date("d",db_getsession("DB_datausu"));
      $y30_data_mes = date("m",db_getsession("DB_datausu"));
      $y30_data_ano = date("Y",db_getsession("DB_datausu"));
  }
  db_inputdata('y30_data',@$y30_data_dia,@$y30_data_mes,@$y30_data_ano,true,'text',$db_opcao,"")
  ?>
  &nbsp;&nbsp;       <?=@$Ly30_hora?>
  <?php 
  db_input('y30_hora',5,$Iy30_hora,true,'text',$db_opcao,"");
  if($db_opcao == 1){
      echo "<script>document.form1.y30_hora.value = '".db_hora()."'</script>";
  }
  ?>
</td>
</tr>

<tr>
    <td nowrap title="<?=@$Ty30_obs?>">
     <?=@$Ly30_obs?>
 </td>
 <td>
    <?php 
    db_textarea('y30_obs',2,50,$Iy30_obs,true,'text',$db_opcao,"")
    ?>
</td>
</tr>
<tr>
    <td nowrap title="<?=@$Ty30_setor?>">
     <?php 
     db_ancora(@$Ly30_setor,"js_pesquisay30_setor(true);",3);
     ?>
 </td>
 <td>
    <?php 
    db_input('y30_setor',5,$Iy30_setor,true,'text',3,"")
    ?>
    <?php 
    db_input('descrdepto',40,$Idescrdepto,true,'text',3,'');
    ?>
</td>
</tr>

                <tr id="listaParagrafo">
                    <td><strong>Lista Paragrafos:</strong></td>
                    <td>
                      <select name="paragrafos" id="paragrafos">
                        <option value=""></option>
                        <?php
                            $pTipo = ((isset($intimacao) && $intimacao == 1) ? 2 : 3 );
                            $pSql  = 'select distinct pl09_paragrafo, pl09_descr from fiscalizacao.fis_paragrafo where pl09_status = true and pl09_tipo = '.$pTipo.' and pl09_coddepto = '.db_getsession('DB_coddepto');
                            $pRs   = db_query($pSql);
                            $pRows = pg_num_rows($pRs);
                            for($i=0; $i<$pRows; $i++){
                                db_fieldsmemory($pRs, $i);
                                echo '<option value="'.$pl09_paragrafo.'">'.$pl09_descr.'</option>';
                            }
                        ?>
                    </select>
                    <input id="addParagrafo" name="addParagrafo" readonly="" type="button" value="Adicionar">
                    </td>
                </tr>
                <?php
                    $textoRelato       = '';
                    $textoInfringencia = '';
                    $textoSancao       = '';
                    $textoBaseLegal    = '';
                    $codRelato         = '';
                    $codInfringencia   = '';
                    $codSancao         = '';
                    $codBaseLegal      = '';

		    if( isset($y30_codnoti) ){

                    $paSql  = "select * from fiscalizacao.fis_paragrafointimacao where pl11_intimacao = $y30_codnoti";
                    $paRs   = db_query($paSql);
                    $paRows = pg_num_rows($paRs);
                    for ($i=0; $i < $paRows; $i++) {
                        db_fieldsmemory($paRs, $i);
                        if($pl11_paragrafo == 1){
                            $textoRelato = $pl11_texto;
                            $codRelato   = $pl11_codigo;

                        } else if($pl11_paragrafo == 2){
                            $textoInfringencia = $pl11_texto;
                            $codInfringencia   = $pl11_codigo;

                        } else if($pl11_paragrafo == 3){
                            $textoSancao = $pl11_texto;
                            $codSancao   = $pl11_codigo;

                        } else if($pl11_paragrafo == 4) {
                            $textoBaseLegal = $pl11_texto;
                            $codBaseLegal   = $pl11_codigo;

                        }
		    }
		    }
                ?>


                <tr class="trParagrafo" id="paraRelato" <?=(($textoRelato=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                        <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="RELATO" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafointimacao[]" value="" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codRelato?>" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                        <input class="rm2" type="hidden" name="paragrafoTipo[]" value="1" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoRelato=='')?'disabled="disabled"':'')?>><?=$textoRelato?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>


                <tr class="trParagrafo" id="paraInfringencia" <?=(($textoInfringencia=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                    <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="INFRINGÊNCIA" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafointimacao[]" value="" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codInfringencia?>" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    <input class="rm2" type="hidden" name="paragrafoTipo[]" value="2" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>><?=$textoInfringencia?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>

                <tr class="trParagrafo" id="paraSancao" <?=(($textoSancao=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                    <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="SANÇÃO" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafointimacao[]" value="" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codSancao?>" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    <input class="rm2" type="hidden" name="paragrafoTipo[]" value="3" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoSancao=='')?'disabled="disabled"':'')?>><?=$textoSancao?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>

                <tr class="trParagrafo" id="paraBaseLegal" <?=(($textoBaseLegal=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                        <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="BASE LEGAL" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafointimacao[]" value="" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codBaseLegal?>" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                        <input class="rm2" type="hidden" name="paragrafoTipo[]" value="4" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>><?=$textoBaseLegal?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>
            <tr>
    <td nowrap title="<?=@$Ty30_nome?>">
     <?=@$Ly30_nome?>
 </td>
 <td>
    <?php 
    db_input('y30_nome',35,$Iy30_nome,true,'text',$db_opcao,"");
    if(isset($z01_nome)){
      echo "<script>document.form1.y30_nome.value = '$z01_nome'</script>";
  }
  ?>
</td>
</tr>

<tr>
    <td nowrap title="<?=@$Ty100_sequencial?>">
     <?php 
      if(isset($procfiscal) && $procfiscal != "")
          $bloqfis = 3;
        else
          $bloqfis = $db_opcao;
     db_ancora(@$Ly100_sequencial,"js_pesquisaprocfiscal(true);",$bloqfis);
     ?>
 </td>
 <td>
     <?php 
     db_input('procfiscal',10,$Iy100_sequencial,true,'text',$bloqfis," onchange='js_pesquisaprocfiscal(false);'")
     ?>
     <?php 
     db_input('nome',40,$Iz01_nome,true,'text',3,'')
     ?>
 </td>
</tr>
<?php 

if ($db_opcao==1 && $rua != ''){
 $y12_codigo=$rua;
 $y12_codi=$bairro;
 $y12_numero=$numero;
 $y12_compl=$compl;
 $y13_codigo=$rua;
 $y13_codi=$bairro;
 $y13_numero=$numero;
 $y13_compl=$compl;
 $result_rua=$clruas->sql_record($clruas->sql_query_file($rua));
 if ($clruas->numrows>0){
  db_fieldsmemory($result_rua,0);
}
$result_bairro=$clbairro->sql_record($clbairro->sql_query_file($bairro));
if($clbairro->numrows>0){
  db_fieldsmemory($result_bairro,0);
}
}else if($db_opcao == 1){
  $j14_nome = $z01_ender;
  $j13_descr = $z01_bairro;
}
?>
<tr>
    <td colspan="2" align="left">
      <fieldset>
          <legend align="center"><strong>Endereço registrado</strong></legend>
          <table>
              <tr>
                <td nowrap width="100" title="<?=@$Ty12_codigo?>">
                 <?php 
                 db_ancora(@$Ly12_codigo,"js_ruas1(true);",3);
                 ?>
             </td>
             <td>
                <?php 
                db_input('y12_codigo',10,$Iy12_codigo,true,'text',3," onChange='js_ruas1(false)'");
                db_input('j14_nome',50,$Ij14_nome,true,'text',3,"");
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?=@$Ty12_numero?>">
             <?=@$Ly12_numero?>
         </td>
         <td>
            <?php 
	      if( trim($y12_numero) == "" ){
			$y12_numero = 0;
	      }
            db_input('y12_numero',10,$Iy12_numero,true,'text',3,"")
            ?>
            <?=@$Ly12_compl?>
            <?php 
            db_input('y12_compl',20,$Iy12_compl,true,'text',3,"")
            ?>
        </td>
    </tr>
    <tr>
        <td nowrap title="<?=@$Ty12_codi?>">
          <?php 
          db_ancora(@$Ly12_codi,"js_bairro1(true);",3);
          ?>
      </td>
      <td nowrap>
          <?php 
          db_input('y12_codi',10,$Iy12_codi,true,'text',3," onChange='js_bairro1(false)'");
          db_input('j13_descr',50,$Ij13_descr,true,'text',3);
          ?>
      </td>
  </tr>
</table>
</fieldset>
</td>
</tr>
<tr>
    <td colspan="2" align="left">
      <fieldset>
          <legend align="center"><strong>Endereço localizado</strong></legend>
          <table>
              <tr>
                <td nowrap width="100" title="<?=@$Ty13_codigo?>">
                 <?php 
                 db_ancora(@$Ly13_codigo,"js_ruas(true);",$db_opcao);
                 ?>
             </td>
             <td nowrap>
              <?php 
              db_input('y13_codigo',10,$Iy13_codigo,true,'text',$db_opcao," onChange='js_ruas(false)'");
              db_input('j14_nome',50,$Ij14_nome,true,'text',3,"","j14_nome_exec");
              ?>
          </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Ty13_numero?>">
         <?=@$Ly13_numero?>
     </td>
     <td>
        <?php 
        db_input('y13_numero',10,$Iy13_numero,true,'text',$db_opcao,"")
        ?>
        <?=@$Ly13_compl?>
        <?php 
        db_input('y13_compl',20,$Iy13_compl,true,'text',$db_opcao,"")
        ?>
    </td>
</tr>
<tr>
    <td nowrap title="<?=@$Ty13_codi?>">
      <?php
      db_ancora(@$Ly13_codi,"js_bairro(true);",$db_opcao);
      ?>
  </td>
  <td nowrap>
      <?php 
      db_input('y13_codi',10,$Iy13_codi,true,'text',$db_opcao," onChange='js_bairro(false)'");
      db_input('j13_descr',50,$Ij13_descr,true,'text',3,"","j13_descr_exec");
      ?>
  </td>
</tr>
</table>
</fieldset>
</td>
</tr>
</table>
</center>
<input name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> onclick="return js_valida();"/>
<?php  if($db_opcao != 1){?>
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();"  <?=($db_opcao==22||$db_opcao==2||$db_opcao==33||$db_opcao==3)?"":"onblur='js_setatabulacao();'"?> />

<?php 
}
if ($db_opcao==22||$db_opcao==2||$db_opcao==33||$db_opcao==3){
    ?>
    <input name="novo" type="button" id="novo" value="Incluir Novo" onclick="parent.location.href='fis1_fis_fiscal005.php?como=cgm';" onblur="js_setatabulacao();">
    <?php 
}
?>
</form>
                <script type="text/javascript" src="scripts/jquery-2.1.1.min.js"></script>
                <script type="text/javascript">
                    var $a = jQuery.noConflict();
                    jQuery(document).ready(function($a) {

                        $a('body').on('click', '.remover', function() {
                            var idTr = $a(this).closest('tr').attr('id');
                            $a('#'+idTr).hide();
                            $a('#'+idTr).find('input.rm1').val('').attr('disabled','disabled');
                            $a('#'+idTr).find('input.rm2').attr('disabled','disabled');
                            $a('#'+idTr).find('textarea').val('');
                        });

                        $a('#addParagrafo').click(function() {
                            // function js_pesquisa(){
                            var pParagrafo = $a('#paragrafos').val();
                            js_OpenJanelaIframe('','db_iframe_addparagrafo','fis1_fis_addparagrago001.php?pParagrafo='+pParagrafo+'&pTipo=<?=$pTipo?>','Pesquisa',true);

                        });
                    });
                </script>

<?php 
if($db_opcao==1){
	if ($rua==""||$bairro==""){
       $op=1;
       if (isset($z01_numcgm)&&$z01_numcgm!=""){
        $sql_cgm = "select z01_munic from cgm where z01_numcgm = $z01_numcgm";
        $result_cgm = pg_query($sql_cgm);
        if (pg_numrows($result_cgm)>0){
            db_fieldsmemory($result_cgm,0);
            $sql_munic = "select munic from db_config";
            $result_munic = pg_query($sql_munic);
            db_fieldsmemory($result_munic,0);

            }
        }
    }
}
?>
<script>
function js_valida(){
	if(document.form1.y13_codigo.value == ''){
		alert('Informe a Rua do Endereço Localizado!');
		return false;
	}
	if(document.form1.y13_codi.value == '' || document.form1.y13_codi.value == 0 ){
                alert('Informe O Bairro do Endereço Localizado, Invalido!');
                return false;
        }
	if(document.form1.y13_numero.value == ''){
                alert('Informe O Numero Endereço Localizado!');
                return false;
        }

}
    function js_setatabulacao(){
      js_tabulacaoforms("form1","y30_numbloco",true,1,"y30_numbloco",true);
  }
  function js_bairro(mostra){
      if(mostra == true){
        js_OpenJanelaIframe('','db_iframe_bairros','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro|j13_codi|j13_descr','pesquisa',true);
    }else{
        js_OpenJanelaIframe('','db_iframe_bairros','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro1&pesquisa_chave='+document.form1.y13_codi.value,'pesquisa',false);
    }
}
function js_preenchebairro(chave,chave1){
  document.form1.y13_codi.value = chave;
  document.form1.j13_descr_exec.value = chave1;
  db_iframe_bairros.hide();
}
function js_preenchebairro1(chave,erro){
  document.form1.j13_descr_exec.value = chave;
  if(erro == true){
    document.form1.y13_codi.focus();
    document.form1.y13_codi.value='';
}
db_iframe_bairros.hide();
}
function js_bairro1(mostra){
  if(mostra == true){
    js_OpenJanelaIframe('','db_iframe_bairros','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro2|j13_codi|j13_descr','pesquisa',true);
}else{
    js_OpenJanelaIframe('','db_iframe_bairros','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro22&pesquisa_chave='+document.form1.y12_codi.value,'pesquisa',false);
}
}
function js_preenchebairro2(chave,chave1){
  document.form1.y12_codi.value = chave;
  document.form1.j13_descr.value = chave1;
  db_iframe_bairros.hide();
}
function js_preenchebairro22(chave,erro){
  document.form1.j13_descr.value = chave;
  if(erro == true){
    document.form1.y12_codi.focus();
    document.form1.y12_codi.value='';
}
db_iframe_bairros.hide();
}
function js_ruas(mostra){
  if(mostra == true){
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheruas|j14_codigo|j14_nome','Pesquisa',true);
}else{
    document.form1.j14_nome_exec.value = '';
    document.form1.y13_numero.value = '';
    document.form1.y13_compl.value = '';
    document.form1.y13_codi.value = '';
    document.form1.j13_descr_exec.value = '';
    document.form1.y13_numero.focus();
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheruas1&pesquisa_chave='+document.form1.y13_codigo.value+'','Pesquisa',false);
}
}
function js_preencheruas(chave1,chave2){
  document.form1.y13_codigo.value = chave1;
  document.form1.j14_nome_exec.value = chave2;
  db_iframe_ruas.hide();
}
function js_preencheruas1(chave1,chave2){
  document.form1.j14_nome_exec.value = chave2;
  if(erro == true){
    document.form1.y13_codigo.focus();
    document.form1.y13_codigo.value='';
}
db_iframe_ruas.hide();
}
function js_ruas1(mostra){
  if(mostra == true){
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheender|j14_codigo|j14_nome','Pesquisa',true);
}else{
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheender1&pesquisa_chave='+document.form1.y12_codigo.value+'','Pesquisa',false);
}
}
function js_preencheender(chave1, chave2){
  document.form1.y12_codigo.value = chave1;
  document.form1.j14_nome.value = chave2;
  db_iframe_ruas.hide();
}
function js_preencheender1(chave1, chave2){
  document.form1.j14_nome.value = chave2;
  if(erro==true){
    document.form1.y12_codigo.focus();
    document.form1.y12_codigo.value = '';
}
}
function js_pesquisay30_setor(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_db_depart','func_db_depart.php?funcao_js=parent.js_mostradb_depart1|coddepto|descrdepto','Pesquisa',true);
}else{
    js_OpenJanelaIframe('','db_iframe_db_depart','func_db_depart.php?pesquisa_chave='+document.form1.y30_setor.value+'&funcao_js=parent.js_mostradb_depart','Pesquisa',false);
}
}
function js_mostradb_depart(chave,erro){
  document.form1.descrdepto.value = chave;
  if(erro==true){
    document.form1.y30_setor.focus();
    document.form1.y30_setor.value = '';
}
}
function js_mostradb_depart1(chave1,chave2){
  document.form1.y30_setor.value = chave1;
  document.form1.descrdepto.value = chave2;
  db_iframe_db_depart.hide();
}

function js_pesquisaprocfiscal(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?funcao_js=parent.js_mostraprocfiscal1|y100_sequencial|z01_nome|db_depart_protocolo|db_descr_depart|db_depart_atual<?=$get?>','Pesquisa',true);
}else{
   if(document.form1.procfiscal.value != ''){
    js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?pesquisa_chave='+document.form1.procfiscal.value+'&funcao_js=parent.js_mostraprocfiscal<?=$get?>','Pesquisa',false,'0','1','775','390');
}else{
   document.form1.nome.value = '';
}
}
}
function js_mostraprocfiscal(chave,erro,dep_prot,depart,dep_atual){
   if (dep_prot == dep_atual) {
     document.form1.nome.value = chave;
     if(erro==true){
      document.form1.procfiscal.focus();
      document.form1.procfiscal.value = '';
  }
}
else {
    alert('Processo de protocolo não está neste departamento atualmente! \nDepartamento atual do processo:'+depart);
    document.form1.procfiscal.focus();
    document.form1.procfiscal.value = '';
    document.form1.nome.value = '';
    return false;
}
}
function js_mostraprocfiscal1(chave1,chave2,dep_prot,depart,dep_atual){
  if (dep_prot == dep_atual) {
  	document.form1.procfiscal.value = chave1;
  	document.form1.nome.value = chave2;
  	db_iframe_procfiscal.hide();
  }
  else {
    alert('Processo de protocolo não está neste departamento atualmente! \nDepartamento atual do processo:'+depart);
    return false;
}
}
function js_abre(pagina){
  js_OpenJanelaIframe('','db_iframe_consulta',pagina,'Pesquisa',true,0);
}
<?php if(!isset($pesqandam)){
    ?>
    function js_pesquisa(){
      js_OpenJanelaIframe('','db_iframe_fiscal','func_fis_fiscal.php?funcao_js=parent.js_preenchepesquisa|y30_codnoti<?=$getIntimacao?>','Pesquisa',true);
  }
  function js_preenchepesquisa(chave){
      db_iframe_fiscal.hide();
      <?php 
      if($db_opcao == 2 || $db_opcao == 22){
          echo " location.href = 'fis1_fis_fiscal002.php?abas=1".$getIntimacao."&chavepesquisa='+chave;";
      }elseif($db_opcao == 33 || $db_opcao == 3){
          echo " location.href = 'fis1_fis_fiscal003.php?abas=1".$getIntimacao."&chavepesquisa='+chave;";
      }
      ?>
  }
  <?php 
}
?>
</script>
</script>
<?php 
if($db_opcao==1){
  echo "<script>document.form1.y30_setor.value='".db_getsession("DB_coddepto")."'</script>";
  echo "<script>js_pesquisay30_setor(false)</script>";
}
if($db_opcao != 1){
  if(isset($y12_codigo) && $y12_codigo != ""){
    echo "<script>js_OpenJanelaIframe('','db_iframe_bairros','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro1&pesquisa_chave=$y12_codi','pesquisa',false);</script>";
    echo "<script>js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheruas1&pesquisa_chave=$y12_codigo','Pesquisa',false);</script>";
    echo "<script>document.form1.y12_codigo.value = '$y12_codigo';js_ruas1(false);</script>";
    echo "<script>document.form1.y12_codi.value='$y12_codi';js_bairro1(false)</script>";
}
  if(isset($j13_codi) && $j13_codi != ""){
      echo "<script>js_OpenJanelaIframe('','db_iframe_bairros','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro1&pesquisa_chave=$j13_codi','pesquisa',false);</script>";
      echo "<script>document.form1.y13_codi.value='$j13_codi'; </script>";
      echo "<script>document.form1.y13_codigo.value='$y13_codigo';js_bairro(false);</script>";
      echo "<script>document.form1.y12_codi.readOnly = true</script>";
  }
}

if($db_opcao == 1){
  if(isset($q02_inscr) && $q02_inscr != ""){
    require_once(modification("classes/db_issruas_classe.php"));
    $clissruas = new cl_issruas;
    $result = $clissruas->sql_record($clissruas->sql_query_inscr($q02_inscr));
    if($clissruas->numrows == 0){
       $clissbase = new cl_issbase;
       $result1    = $clissbase->sql_record($clissbase->sql_query($q02_inscr,"z01_ender as j14_nome, z01_numero as y12_numero,z01_compl as y12_compl,z01_bairro as j13_descr"));
       db_fieldsmemory($result1,0);
        echo "<script>document.form1.j14_nome.value = '$j14_nome';</script>";
        echo "<script>document.form1.y12_numero.value = '$y12_numero';</script>";
        echo "<script>document.form1.y12_compl.value = '$y12_compl';</script>";
        echo "<script>document.form1.j13_descr.value = '$j13_descr';</script>";
        echo "<script>document.form1.y12_codigo.value = '';</script>";
        echo "<script>document.form1.y12_codi.value = '';</script>";


}else{
  db_fieldsmemory($result,0);
  echo "<script>js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheruas1&pesquisa_chave=$j14_codigo','Pesquisa',false);</script>";
  echo "<script>document.form1.y13_codigo.value = '$j14_codigo';</script>";
  echo "<script>document.form1.y13_codi.value = '$j13_codi';</script>";
  echo "<script>document.form1.y12_codi.value = '$j13_codi';</script>";
  echo "<script>document.form1.y12_codigo.value = '$j14_codigo';js_ruas1(false); js_bairro1(false);</script>";
  echo "<script>document.form1.y12_codigo.readOnly = true</script>";
}
} elseif(isset($j01_matric) && $j01_matric != ""){
  $result = pg_exec("select codpri as j14_codigo, nomepri, tipopri, j39_numero as z01_numero, j39_compl as z01_compl from proprietario where j01_matric = $j01_matric");
  db_fieldsmemory($result,0);

  echo "<script>js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheruas1&pesquisa_chave=$j14_codigo','Pesquisa',false);</script>";
  echo "<script>document.form1.y13_codigo.value = '$j14_codigo';</script>";
  echo "<script>document.form1.y12_codigo.value = '$j14_codigo';js_ruas1(false);</script>";
  echo "<script>document.form1.y12_codigo.readOnly = true</script>";

}


if(isset($z01_numero) && $z01_numero != ""){
    echo "<script>document.form1.y12_numero.value='$z01_numero';</script>";
    echo "<script>document.form1.y13_numero.value='$z01_numero';</script>";
    echo "<script>document.form1.y12_numero.readOnly = true</script>";
}
if(isset($z01_compl) && $z01_compl != ""){
    echo "<script>document.form1.y12_compl.value='$z01_compl';</script>";
    echo "<script>document.form1.y13_compl.value='$z01_compl';</script>";
    echo "<script>document.form1.y12_compl.readOnly = true</script>";
}
}
?>
