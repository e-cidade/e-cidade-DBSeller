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

include modification("conn.php");
$conn=new conecta;
$conn=$conn->con();

function db_versao_integer($sVersao) {
  $aVersao = explode(".", $sVersao);
  $iVersao = 0;
  for($i=0; $i<count($aVersao); $i++) {
    $iVersao += (int)$aVersao[$i] * pow(10 , (6 - ($i+1) * 2) );
  }
  return $iVersao;
}

$diretorio_testes = "/home/versoes";

//parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

//echo "tar:$tarefa";

if (!$tarefa || $tarefa==0)
{
  echo " verifique o número da tarefa digitada";
  exit;
}

//------------------------------------------
//  INICIO - LOCK GERA VERSAO
//------------------------------------------

$arquivo_lock = '/tmp/controleversao.lock';

$tarefa = trim($tarefa);

if (file_exists($arquivo_lock)) {

  echo "<br> Processo ja esta sendo executado, aguarde!!!";
  $fp = fopen ($arquivo_lock, "r");
  $conteudo = fread ($fp, filesize ($arquivo_lock));
  fclose ($fp);
  echo "<br><br><font color=red><b>AMBIENTE ESTA SENDO GERADO POR:  $conteudo  </b></font><br><br>";
  echo '<form name="form1" action="apaga_lock.php" method="post">';
  echo '<br><br><Input type="submit" value="Apagar Arquivo de LOCK"></form>';
  exit;

}

$fp = fopen($arquivo_lock, 'w');
fwrite($fp, $_SERVER['REMOTE_ADDR']);
fclose($fp);

//------------------------------------------
//  FIM - LOCK GERA VERSAO
//------------------------------------------



// verifica se tarefa existe
//$conn = pg_connect("host=192.168.0.1 dbname=dbseller user=postgres");

db_query("begin");


$sql = "select at40_descr,at40_obs,at43_diaini,at43_descr,at43_obs,at43_tipomov,at40_progresso
          from tarefa
               left join tarefalog on at43_tarefa = at40_sequencial
         where at40_sequencial = $tarefa order by at43_sequencial desc";
$result = db_query($sql);

if( $result == false || pg_numrows($result)==0){
  echo "Tarefa não existente $tarefa";
  exit;
}
// variavel que controla se deve ser gerado o menu
$baixa_menus = false;


//jlas - variável que controla se deve der gerado sql
$gera_sql = false;

// variavel que possui todos os sql a serem gerados
$texto_sql = "--sql a ser executado na base de dados\n";

// gera texto da tarefa
$texto  = "Tarefa : $tarefa \n";
$texto .= "Resumo : ".pg_result($result,0,'at40_descr')."\n";
$texto .= "Observ.: ".pg_result($result,0,'at40_obs')."\n";
$tarefa_progresso= pg_result($result,0,'at40_progresso')."\n";
$linhas = pg_numrows($result);


// Monta TXT com andamentos
for($lin=0;$lin < $linhas;$lin++){
  $texto .= "---------------------------------------------------------------------------\n";
  $texto .= "Data : ".pg_result($result,$lin,'at43_diaini')."\n";
  $texto .= pg_result($result,$lin,'at43_descr')."\n";
  $texto .= pg_result($result,$lin,'at43_obs')."\n";
  if(pg_result($result,$lin,'at43_tipomov') == 2){ // Movimento DUMP Menus
    $baixa_menus = (true and isset($gerarmenus));
  }

}
shell_exec( "umask 0000" );
$erro=false;
for($lin=($linhas-1); $lin>=0; $lin--){
  if(pg_result($result,$lin,'at43_tipomov') == 1 && pg_result($result,$lin,'at43_obs') ==""){
    echo "Existe movimento SQL sem registro nas observações. Verifique a tarefa!";
    $erro=true;
  }
}

if($erro == true){

//------------------------------------------
//  APAGA O ARQUIVO LOCK
//------------------------------------------

if (file_exists($arquivo_lock)) {

  unlink($arquivo_lock);

} else {

    $arquivo_ip_lock = '/tmp/controleversaoip.lock';

      if (file_exists($arquivo_ip_lock)) {

        $fp = fopen ($arquivo_ip_lock, "r");
        $conteudo = fread ($fp, filesize ($arquivo_ip_lock));
        fclose ($fp);
        unlink($arquivo_ip_lock);

      }

      echo "<br><br><font color=red><b>ARQUIVO DE LOCK JÁ FOI APAGADO.  $conteudo  </b></font><br><br>";

  }
  exit;
}
// Monta SQL dos andamentos
for($lin=($linhas-1); $lin>=0; $lin--){
  if(pg_result($result,$lin,'at43_tipomov') == 1){  // Movimento SQL
    $texto_sql .= pg_result($result,$lin,'at43_obs')."\n";
    $gera_sql = true;
  }
}

echo "<b>Baixando Arquivos do CVS da tarefa $tarefa </b><br>";
if ($tarefa_progresso==100)
{
  echo"<fonte color=red> <br><b>Atenção! Tarefa finalizada (progresso da tarefa $tarefa_progresso % ). </b> <br><br></font>";
}
flush();
shell_exec( "rm -rf $diretorio_testes/dbportal_prj" );
shell_exec( "rm -rf $diretorio_testes/funcoes8" );
shell_exec( "rm -rf $diretorio_testes/dbpref" );


$CVSROOT = ":pserver:dbintegracao:halegria@192.168.0.3:/home/cvs";

shell_exec( "umask 0000; cd $diretorio_testes; cvs -d $CVSROOT -z6 checkout -r T".$tarefa." dbportal_prj" );
shell_exec( "umask 0000; cd $diretorio_testes; cvs -d $CVSROOT -z6 checkout -r T".$tarefa." funcoes8" );
shell_exec( "umask 0000; cd $diretorio_testes; cvs -d $CVSROOT -z6 checkout -r T".$tarefa." dbpref" );

global $diretorios, $arq_cvs;

$arquivo = array();

function verifica_diretorio($diretorio,$tarefa){

  global $arq_cvs,$arquivo,$diretorio_testes;

  $CVSEntries = "$diretorio_testes/$diretorio/CVS/Entries";
  if( file_exists($CVSEntries) ){
    $arquivos_cvs = file( $CVSEntries );
    for($arq=0;$arq<count($arquivos_cvs);$arq++){
      $x =explode("/",$arquivos_cvs[$arq]);
      if(isset($x[1]) && $x[0] == null  ){
        $arq_cvs[ $diretorio.$x[1] ] = $diretorio.$arquivos_cvs[$arq];
      }
    }
  }else{
    return;
  }


  $d = dir("$diretorio_testes/$diretorio");

  while (false !== ($entry = $d->read())) {

    if( $entry != "CVS" &&  $entry != "." && $entry != ".." ){

      $CVSEntries = "$diretorio_testes/$diretorio/$entry/CVS/Entries";
      if( file_exists($CVSEntries) ) {
        $arquivos_cvs = file( $CVSEntries );
        for($arq=0;$arq<count($arquivos_cvs);$arq++) {
          $x =explode("/",$arquivos_cvs[$arq]);
          if(isset($x[1]) && $x[0] == null) {
            $arq_cvs[ $diretorio.$entry."/".$x[1] ] = $diretorio.$entry.$arquivos_cvs[$arq];
          }
        }
      }


      if( is_dir($diretorio_testes."/".$diretorio.$entry)) {
        if( ! file_exists($diretorio_testes."/".$diretorio.$entry) ){
          shell_exec("umask 0000;mkdir $diretorio_testes/$diretorio/$entry");
        }

        verifica_diretorio($diretorio.$entry."/",$tarefa,false);
      }else{
        $arquivo[count($arquivo)] = $diretorio.$entry;
        shell_exec("umask 0000;cp ".$diretorio.$entry." /home/versoes/".$diretorio.$entry."_".$tarefa);
      }

    }
  }
  $d->close();

  return $arquivo;

}


echo "<font size=2> Gerando Lista de Arquivos Baixados do CVS - dbportal_prj<br></font>";
flush();
$dir = "dbportal_prj/";
$arquivo = array();
$arquivos_dbportal = verifica_diretorio($dir,$tarefa,true);
//print_r($arquivos_dbportal);


echo "<font size=2>Gerando Lista de Arquivos Baixados do CVS - funcoes8<br></font>";
flush();
$dir = "funcoes8/";
$arquivo = array();
$arquivos_funcoes = verifica_diretorio($dir,$tarefa,true);
//print_r($arquivos_funcoes);

echo "<font size=2>Gerando Lista de Arquivos Baixados do CVS - dbpref<br></font>";
flush();
$dir = "dbpref/";
$arquivo = array();
$arquivos_dbpref = verifica_diretorio($dir,$tarefa,true);

//print_r($arquivos_dbpref);

//print_r($arq_cvs);exit;
// gravar os dados dos arquivos do arquivo Entries do CVS
$dados_arquivos = array();

if(!isset($arq_cvs)){
  echo "<font color=red> <br><b>Tarefa sem tag!Consultar a tarefa e verificar andamento.</b><br></font>";
}else{

  reset($arq_cvs);

}

for($ga=0;$ga<count($arq_cvs);$ga++){

  $dados =explode("/",$arq_cvs[key($arq_cvs)]);

  $totsplit = count($dados);

  if( isset($dados[($totsplit-4)]) && $dados[($totsplit-4)] != null ) {

    $dados_arquivos[key($arq_cvs)][1] = $dados[($totsplit-4)];
    $dados_arquivos[key($arq_cvs)][2] = substr($dados[($totsplit-3)],20,4)."-".substr($dados[($totsplit-3)],4,3)."-".trim(substr($dados[($totsplit-3)],8,2));
    $dados_arquivos[key($arq_cvs)][3] = substr($dados[($totsplit-3)],11,8);


  }
  next($arq_cvs);

}

//print_r($dados_arquivos);exit;

$dir = "$diretorio_testes/dbportal_prj/";

// grava os arquivos alterados na tarefa dbportal_prj

$sql = "delete from tarefa_arquivos where at80_tarefa = $tarefa ";
$resarq = db_query($sql);

//print_r($arquivos_dbportal);

for($i=0;$i<count($arquivos_dbportal);$i++){

  //ados_arq =explode("/",$arquivos_dbportal[$i]);

  // verificar se este arquivo já existe para outra tarefa e que a versao do cvs seja maior na outra tarefa
  // se existe na sub-release ERRO, nao pode ser esta versao e deverá voltar para a programação.
  // se estiver em teste verificar com a programacao para acertar as tags das tarefas e testar junto as tarefas
  //      ( deverá necessariamente gerar uma unica sub-release  para as tarefas neste caso )
  // remover o ambiente de teste

  // já existe uma arquivo em uma sub-release com numero maior que esta sub-release

  $sql = "select at80_versaocvs, db30_codversao, db30_codrelease, db29_tarefa
    from tarefa_arquivos
    inner join db_versaotarefa on at80_tarefa = db29_tarefa
    inner join db_versao       on db29_codver = db30_codver

    where at80_tarefa   <> $tarefa and
    at80_arquivos =  '".$arquivos_dbportal[$i]."'
    order by at80_versaocvs desc limit 1 ";

  $result = db_query($sql);

  if( pg_numrows($result) > 0 ){

    $versao_anterior   = pg_result($result,0,'at80_versaocvs');
    $versao_release    = pg_result($result,0,'db30_codversao');
    $versao_subrelease = pg_result($result,0,'db30_codrelease');
    $versao_tarefa     = pg_result($result,0,'db29_tarefa');


    if( db_versao_integer($versao_anterior) > db_versao_integer($dados_arquivos[$arquivos_dbportal[$i]][1]) ) {

      echo "<br>
        Não poderá ser testado este programa (".$arquivos_dbportal[$i]." CVS: <strong>".$dados_arquivos[$arquivos_dbportal[$i]][1]."</strong> Data:".$dados_arquivos[$arquivos_dbportal[$i]][2]." )<br>
        porque já existe uma versão do CVS maior cadastrada no controle de versão do DBPortal.<br>

        <font color=red > Tarefa $tarefa versão CVS ".$dados_arquivos[$arquivos_dbportal[$i]][1]." possui o fonte com versão menor que a Tarefa: $versao_tarefa que está na Release: $versao_release Sub-release: $versao_subrelease <font><strong><br>";

      echo "<br><strong>Ambiente de teste não gerado.</strong><br>";
      // remove os diretorios de teste
      shell_exec( "rm -rf $diretorio_testes/dbportal_prj_tarefa_$tarefa" );
      shell_exec( "rm -rf $diretorio_testes/funcoes8_tarefa_$tarefa" );
      shell_exec( "rm -rf $diretorio_testes/dbpref_tarefa_$tarefa" );

      exit;

    }

  }


  $sql = "select * from (
    select distinct at80_versaocvs, at80_tarefa, at80_data, db29_tarefa,at40_progresso
    from tarefa_arquivos
    left join db_versaotarefa on at80_tarefa = db29_tarefa
    left join tarefa          on at40_sequencial=at80_tarefa
    where at80_tarefa   <> $tarefa and
    at80_arquivos =  '".$arquivos_dbportal[$i]."'
    ) as x
    order by to_number(at80_versaocvs,'99999999999') ";

  $result = db_query($sql);

  $mensagem = false;
  if( pg_numrows($result) > 0 ){


    for( $x = 0; $x < pg_numrows($result);$x++){
      $versao_cvs        = pg_result($result,$x,'at80_versaocvs');
      $tarefa_executa    = pg_result($result,$x,'at80_tarefa');
      $tarefa_data       = pg_result($result,$x,'at80_data');
      $tarefa_progresso  = pg_result($result,$x,'at40_progresso');

      if ($tarefa_progresso<>100)
      {

        if($mensagem == false){
          echo "<font size=2><br>Verifique as tarefas abaixo, pois deverão seguir conjuntamente até virarem versão do DBPortal.<br></font>";
          $mensagem = true;
        }

        // echo " Aqui-1 tarefa:$tarefa_progresso";
        echo "<font size=2><b> Tarefa $tarefa versão CVS: ".$dados_arquivos[$arquivos_dbportal[$i]][1]." </b> possui o fonte ".$arquivos_dbportal[$i]." na Tarefa : $tarefa_executa Versão do CVS: $versao_cvs</font><br>";
      }
      $v1 = str_replace('.','',$dados_arquivos[$arquivos_dbportal[$i]][1]);
      $v2 = str_replace('.','',$versao_cvs);
      if(  pg_result($result,$x,'db29_tarefa') != "" && (float)$v1 < (float)$v2 ){
        echo "<font  color='red'>ERRO ..... CVS Atual: ".$dados_arquivos[$arquivos_dbportal[$i]][1]."Tarefa : $tarefa_executa Data: $tarefa_data Versão do CVS: $versao_cvs Arquivo: ".$arquivos_dbportal[$i]." NAO PODE SER ATUALIZADA ESTA TAREFA.</strong> <br>";
        echo "Processamento Abortado.</font>";
        exit;
      }

    }
  }

  // inclui arquivos na tarefa_arquivos
  //   echo
  $sql = "insert into tarefa_arquivos values (nextval('tarefa_arquivos_at80_seqarquivo_seq'),$tarefa,
    '".$arquivos_dbportal[$i]."',
    '".$dados_arquivos[$arquivos_dbportal[$i]][1]."',
    '".$dados_arquivos[$arquivos_dbportal[$i]][2]."',
    '".$dados_arquivos[$arquivos_dbportal[$i]][3]."')";

  $resarq = db_query($sql);

}

for($i=0;$i<count($arquivos_funcoes);$i++){

  //echo $dados_arquivos[$arquivos_funcoes[$i]][1]."--".$arquivos_funcoes[$i];

  // verificar se este arquivo já existe para outra tarefa e que a versao do cvs seja maior na outra tarefa
  // se existe na sub-release ERRO, nao pode ser esta versao e deverá voltar para a programação.
  // se estiver em teste verificar com a programacao para acertar as tags das tarefas e testar junto as tarefas
  //      ( deverá necessariamente gerar uma unica sub-release  para as tarefas neste caso )
  // remover o ambiente de teste

  // já existe uma arquivo em uma sub-release com numero maior que esta sub-release

  $sql = "select distinct at80_versaocvs, db30_codversao, db30_codrelease,at80_tarefa
    from tarefa_arquivos
    inner join db_versaotarefa on at80_tarefa = db29_tarefa
    inner join db_versao       on db29_codver = db30_codver
    where at80_tarefa   <> $tarefa and
    at80_arquivos =  '".$arquivos_funcoes[$i]."'
    order by at80_versaocvs desc limit 1 ";

  $result = db_query($sql);

  if( pg_numrows($result) > 0 ){

    $versao_anterior = pg_result($result,0,'at80_versaocvs');
    $versao_release  = pg_result($result,0,'db30_codversao');
    $versao_subrelease = pg_result($result,0,'db30_codrelease');
    $versao_tarefa = pg_result($result,0,'at80_tarefa');
    if( db_versao_integer($versao_anterior) > db_versao_integer($dados_arquivos[$arquivos_funcoes[$i]][1]) ) {

      echo "Já existe uma tarefa com sub-release cadastrada no sistema<strong>
        MAIOR QUE A ATUAL.</strong> <br>
        Verifique com a programação. <br>
        Arquivo: '".$arquivos_funcoes[$i]."'<br><strong>
        <font color=red> Tarefa:$versao_tarefa - versao CVS $versao_anterior está na Release: $versao_release Sub-release: $versao_subrelease </font><strong><br>
        <strong> Versão CVS Atual: ".$dados_arquivos[$arquivos_funcoes[$i]][1]." Data: ".$dados_arquivos[$arquivos_funcoes[$i]][2]."</strong>";

      // remove os diretorios de teste
      shell_exec( "rm -rf $diretorio_testes/dbportal_prj_tarefa_$tarefa" );
      shell_exec( "rm -rf $diretorio_testes/funcoes8_tarefa_$tarefa" );
      shell_exec( "rm -rf $diretorio_testes/dbpref_tarefa_$tarefa" );

      exit;

    }

  }


  $sql = "select distinct at80_versaocvs, at80_tarefa, at80_data,at40_progresso
    from tarefa_arquivos
    left join db_versaotarefa on at80_tarefa = db29_tarefa
    left join tarefa          on at40_sequencial=at80_tarefa
    where at80_tarefa   <> $tarefa and
    at80_arquivos =  '".$arquivos_funcoes[$i]."' and
    db29_tarefa is null
    order by at80_versaocvs ";

  $result = db_query($sql);

  $mensagem=false;

  if( pg_numrows($result) > 0 ){

    for( $x = 0; $x < pg_numrows($result);$x++){

      $versao_cvs       = pg_result($result,$x,'at80_versaocvs');
      $tarefa_executa   = pg_result($result,$x,'at80_tarefa');
      $tarefa_data      = pg_result($result,$x,'at80_data');
      $tarefa_progresso = pg_result($result,$x,'at40_progresso');

      if ($tarefa_progresso<>100)
      {

        if($mensagem == false){
          //echo "<br>Verifique as tarefas abaixo, pois deverão seguir conjuntamente até virarem versão do DBPortal.<br>";
          echo "<font size=2><br>Verifique as tarefas abaixo, pois deverão seguir conjuntamente até virarem versão do DBPortal.<br></font>";
          $mensagem = true;
        }


        echo "<font size=2> <b> Tarefa $tarefa versão CVS: ".$dados_arquivos[$arquivos_funcoes[$i]][1]. "</b> possui o fonte ".$arquivos_funcoes[$i]." na tarefa : $tarefa_executa Versão do CVS: $versao_cvs <br> </font>";
      }
    }

  }

  // inclui arquivos na tarefa_arquivos

  $sql = "insert into tarefa_arquivos values (nextval('tarefa_arquivos_at80_seqarquivo_seq'),$tarefa,
    '".$arquivos_funcoes[$i]."',
    '".$dados_arquivos[$arquivos_funcoes[$i]][1]."',
    '".$dados_arquivos[$arquivos_funcoes[$i]][2]."',
    '".$dados_arquivos[$arquivos_funcoes[$i]][3]."')";

  $resarq = db_query($sql);

}

for($i=0;$i<count($arquivos_dbpref);$i++){

  //$dados_arq =explode("/",$arquivos_dbpref[$i]);
  //print_r($dados_arq)."<br>";


  // verificar se este arquivo já existe para outra tarefa e que a versao do cvs seja maior na outra tarefa
  // se existe na sub-release ERRO, nao pode ser esta versao e deverá voltar para a programação.
  // se estiver em teste verificar com a programacao para acertar as tags das tarefas e testar junto as tarefas
  //      ( deverá necessariamente gerar uma unica sub-release  para as tarefas neste caso )
  // remover o ambiente de teste

  // já existe uma arquivo em uma sub-release com numero maior que esta sub-release

  $sql = "select at80_versaocvs, db30_codversao, db30_codrelease,at80_tarefa
    from tarefa_arquivos
    inner join db_versaotarefa on at80_tarefa = db29_tarefa
    inner join db_versao       on db29_codver = db30_codver
    where at80_tarefa   <> $tarefa and
    at80_arquivos =  '".$arquivos_dbpref[$i]."'
    order by at80_versaocvs desc limit 1 ";

  $result = db_query($sql);

  if( pg_numrows($result) > 0 ){

    $versao_anterior   = pg_result($result,0,'at80_versaocvs');
    $versao_release    = pg_result($result,0,'db30_codversao');
    $versao_subrelease = pg_result($result,0,'db30_codrelease');
    $versao_tarefa     =pg_result($result,0,'at80_tarefa');

    if( db_versao_integer($versao_anterior) > db_versao_integer($dados_arquivos[$arquivos_dbpref[$i]][1]) ) {

      echo "Já existe uma tarefa com sub-release cadastrada no sistema<strong>
        MAIOR QUE A ATUAL.</strong> <br>
        Verifique com a programação. <br>
        Arquivo: '".$arquivos_dbpref[$i]."'<br><strong>
        <font color=red> Tarefa: $versao_tarefa - CVS $versao_anterior está na Release: $versao_release Sub-release: $versao_subrelease </font><strong><br>
        <strong> Versão CVS Atual: ".$dados_arquivos[$arquivos_dbpref[$i]][1]." Data: ".$dados_arquivos[$arquivos_dbpref[$i]][2]."</strong>";

      // remove os diretorios de teste
      shell_exec( "rm -rf $diretorio_testes/dbportal_prj_tarefa_$tarefa" );
      shell_exec( "rm -rf $diretorio_testes/funcoes8_tarefa_$tarefa" );
      shell_exec( "rm -rf $diretorio_testes/dbpref_tarefa_$tarefa" );

      exit;

    }

  }


  $sql = "select distinct at80_versaocvs, at80_tarefa, at80_data, at40_progresso
    from tarefa_arquivos
    left join db_versaotarefa on at80_tarefa = db29_tarefa
    left join tarefa          on at40_sequencial=at80_tarefa
    where at80_tarefa   <> $tarefa and
    at80_arquivos =  '".$arquivos_dbpref[$i]."' and
    db29_tarefa is null
    order by at80_versaocvs ";

  $result = db_query($sql);

  $mensagem=false;
  if( pg_numrows($result) > 0 ){

    for( $x = 0; $x < pg_numrows($result);$x++){

      $versao_cvs      = pg_result($result,$x,'at80_versaocvs');
      $tarefa_executa  = pg_result($result,$x,'at80_tarefa');
      $tarefa_data     = pg_result($result,$x,'at80_data');
      $tarefa_progresso = pg_result($result,$x,'at40_progresso');
      //echo "<strong>CVS Atual: ".$dados_arquivos[$arquivos_dbpref[$i]][1]." Tarefa : $tarefa_executa Data: $tarefa_data Versão do CVS: $versao_cvs Arquivo: ".$arquivos_dbpref[$i]."</strong> <br>";

      if ($tarefa_progresso<>100) {

        if($mensagem == false){
          //echo "<br>Verifique as tarefas abaixo, pois deverão seguir conjuntamente até virarem versão do DBPortal.<br>";
          echo "<font size=2><br>Verifique as tarefas abaixo, pois deverão seguir conjuntamente até virarem versão do DBPortal.<br></font>";
          $mensagem = true;
        }

        echo "<font size=2> <b> Tarefa $tarefa versão CVS: ".$dados_arquivos[$arquivos_dbpref[$i]][1]."</b> possui o fonte ".$arquivos_dbpref[$i]." na Tarefa : $tarefa_executa Versão do CVS: $versao_cvs</font><br>";
      }
    }

  }

  // inclui arquivos na tarefa_arquivos

  $sql = "insert into tarefa_arquivos values (nextval('tarefa_arquivos_at80_seqarquivo_seq'),$tarefa,
    '".$arquivos_dbpref[$i]."',
    '".$dados_arquivos[$arquivos_dbpref[$i]][1]."',
    '".$dados_arquivos[$arquivos_dbpref[$i]][2]."',
    '".$dados_arquivos[$arquivos_dbpref[$i]][3]."')";

  $resarq = db_query($sql);

}

echo "Criando pasta $diretorio_testes<br>";
flush();
shell_exec( "rm -rf $diretorio_testes/*tarefa_$tarefa" );

if(file_exists($dir."CVS/Entries")) {
  // gera arquivo com os dados dos movimentos da tarefa
  $fd = fopen($dir."tarefa_$tarefa.txt","w");
  if($fd) {
    fwrite($fd,$texto);
    fclose($fd);
  }
}

// gera arquivo com os comando a serem executados na base de dados
if ($gera_sql == true) {
  echo "<br><strong>Existem SQLs a serem executados para esta tarefa.</strong><br>";
  shell_exec( "umask 0000; mkdir $diretorio_testes/sql_tarefa_$tarefa" );
  $fd = fopen("$diretorio_testes/sql_tarefa_$tarefa/tarefa_$tarefa.sql","w+");
  fwrite($fd,$texto_sql);
  fclose($fd);
  shell_exec( "/usr/bin/dos2unix $diretorio_testes/sql_tarefa_$tarefa/tarefa_$tarefa.sql" );
}


// criar o arquivo de menus
if( $baixa_menus == true ){

  echo "Gerando dumps do dicionário de dados.<br>";
  flush();
  shell_exec( "umask 0000;/usr/bin/scripts/dumpmenusclientes" );
  shell_exec( "umask 0000;mkdir $diretorio_testes/menus_tarefa_$tarefa" );
}




shell_exec( "umask 0000;mv $diretorio_testes/dbportal_prj $diretorio_testes/dbportal_prj_tarefa_$tarefa" );
shell_exec( "umask 0000;mv $diretorio_testes/funcoes8 $diretorio_testes/funcoes8_tarefa_$tarefa" );
shell_exec( "umask 0000;mv $diretorio_testes/dbpref $diretorio_testes/dbpref_tarefa_$tarefa" );

if( $baixa_menus == true ){

  shell_exec( "umask 0000;cp /tmp/menus8.sql.bz2 $diretorio_testes/menus_tarefa_$tarefa" );

}

shell_exec("chmod -R 777 /home/versoes/*tarefa_$tarefa");

db_query("commit");

//------------------------------------------
//  APAGA O ARQUIVO LOCK
//------------------------------------------

if (file_exists($arquivo_lock)) {

  unlink($arquivo_lock);

} else {

  $arquivo_ip_lock = '/tmp/controleversaoip.lock';

  if (file_exists($arquivo_ip_lock)) {

    $fp = fopen ($arquivo_ip_lock, "r");
    $conteudo = fread ($fp, filesize ($arquivo_ip_lock));
    fclose ($fp);
    unlink($arquivo_ip_lock);

  }

  echo "<br><br><font color=red><b>ARQUIVO DE LOCK JÁ FOI APAGADO.  $conteudo  </b></font><br><br>";

}

echo "<br>Processo Concluído<br>";

//include modification("tarefa.php");

?>
