<?php
require("libs/db_stdlib.php");
require("libs/db_stdlibwebseller.php");
require("libs/db_conecta.php");
include("libs/db_sessoes.php");
include("libs/db_usuariosonline.php");
include("dbforms/db_funcoes.php");
include("classes/db_rharqbanco_classe.php");

/* comando para habilitar mensagens na tela */
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);

db_postmemory($HTTP_POST_VARS);
$clrharqbanco = new cl_rharqbanco;
$clrotulo = new rotulocampo;
$clrharqbanco->rotulo->label();
$clrotulo->label('rh34_codarq');
$clrotulo->label('rh34_descr');
$clrotulo->label('db90_descr');
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');


if(isset($emite)){
  db_inicio_transacao();
  $sqlerro = false;
  $clrharqbanco->alterar($rh34_codarq);
  $rh34_sequencial += 1;
  db_fim_transacao($sqlerro);
}else if(isset($rh34_codarq)){
  $result = $clrharqbanco->sql_record($clrharqbanco->sql_query($rh34_codarq));
  if($clrharqbanco->numrows > 0){
    db_fieldsmemory($result,0);
    if($rh34_sequencial > 1){
      $rh34_sequencial += 1;
    }
  }
}


if (isset($emite) || isset($emite)){
  db_postmemory($HTTP_POST_VARS);

  $folha = $arquivo;
  $ano   = $DBtxt23;
  $mes   = $DBtxt25;

$cod_folha   = '';
$xtipo_folha = '';

  if ($folha == 'r14'){
       $xarquivo = 'salario';
       $arquivo = 'gerfsal';
       $cod_folha = '01';
       $xtipo_folha = 'FOLHA DE PAGAMENTO DE SALÁRIO - '.$ano.'/'.$mes;
  }elseif ($folha == 'r35'){
       $xarquivo = '13alario';
       $arquivo = 'gerfs13';
       $cod_folha = '03';
       $xtipo_folha = 'FOLHA DE PAGAMENTO DE 13o. SALÁRIO - '.$ano.'/'.$mes;
  }elseif ($folha == 'r22'){
       $xarquivo = 'adiantamento';
       $arquivo = 'gerfadi';
       $cod_folha = '05';
       $xtipo_folha = 'FOLHA DE PAGAMENTO DE ADIANTAMENTO - '.$ano.'/'.$mes;
  }elseif ($folha == 'r48'){
       $xarquivo = 'complementar';
       $arquivo = 'gerfcom';
       $cod_folha = '02';
       $xtipo_folha = 'FOLHA DE PAGAMENTO DE COMPLEMENTAR - '.$ano.'/'.$mes;
  }

  db_sel_instit();

  $wherepes = '';
  if(isset($semest) && $semest != 0){
    $wherepes = " and r48_semest = ".$semest;
    $head6 = $xarquivo ." ($semest)";
  }



  $sql_matriculas =
  "
  select distinct
    rh02_regist             as matricula
  , substr(z01_nome,1,35)   as nome
  , z01_cgccpf              as cpf
  , trim(rh44_agencia)      as agencia
  , rh44_conta              as contacorrente
  , rh44_dvconta            as dvcontacorrente
  , r70_estrut              as lotacao
  , r70_descr               as descr_lotacao
  , substr(rh37_descr,1,25) as funcao
  , rh01_admiss             as admissao

   from ".$arquivo."

   left outer join rhrubricas
     on rh27_rubric = ".$folha."_rubric
    and rh27_instit = ".$folha."_instit

  inner join rhpessoal
     on rh01_regist = ".$folha."_regist

   left outer join rhpessoalmov
     on rh02_anousu = ".$ano."
    and rh02_mesusu = ".$mes."
    and rh02_regist = ".$folha."_regist
    and rh02_instit = ".$folha."_instit

  inner join rhlota
     on rh02_lota  = r70_codigo
    and r70_instit = rh02_instit

   left join rhpesbanco
     on rh02_seqpes = rh44_seqpes

   left join rhfuncao
     on rh37_funcao = rh02_funcao
    and rh37_instit = rh02_instit

   left join cgm
     on rh01_numcgm = z01_numcgm

   where ".$folha."_anousu = ".$ano."
     and ".$folha."_mesusu = ".$mes."
     and ".$folha."_pd != 3
     and rh44_codban = '041'
     and rh02_instit = ".db_getsession('DB_instit')."

   order by rh02_regist ";

//  and rh02_regist in (76520)

// echo $sql_matriculas;exit;

  $arq = "/tmp/contracheque".$xarquivo.$ano.$mes.".txt";

  $arquivo_txt = fopen($arq,'w');
  $result_matriculas = pg_query($sql_matriculas);

  $imp = '';
  $sequencia = 1;

//// Registro tipo 00 - Header de Arquivo
  $imp  = '00'                                                             ; //  1 - 2  Identificador do registro header. Preencher com a constante ¿00¿.
  $imp .= '041'                                                            ; //  3 - 3  Preencher com constante ¿041¿
  $imp .= db_formatar($ano,'s','0',4,'e',0 )                               ; //  4 - 6  Ano da competência do arquivo .(REFERENCIA)
  $imp .= db_formatar($mes,'s','0',2,'e',0 )                               ; //  2 - 10 Mês da competência do arquivo . (REFERENCIA)
  $imp .= '01'                                                             ; //  2 - 12 Dia da Competência do arquivo  (REFERENCIA)
  $imp .= db_formatar(substr(trim($nomeinstabrev),0,18),'s',' ',18,'d',0 ) ; // 18 - 14 Nome do convênio , pré acordado com o departamento do banco gestor
  $imp .= $datadeposit_ano.$datadeposit_mes.$datadeposit_dia               ; //  8 - 32 Data de gravação do arquivo . Formato: AAAAMMDD.
  $imp .= db_formatar($rh34_sequencial,'s','0',4,'e',0)                    ; //  4 - 40 Numero sequêncial  do  arquivo
  $imp .= db_formatar(substr(trim($nomeinstabrev),0,12),'s',' ',12,'d',0)           ; // 12 - 44 Nome da empresa solicitante.
  $imp .= ' '                                                              ; //  1 - 56 Preencher com brancos
  $imp .= db_formatar($rh34_conta,'s','0',6,'e',0 )                        ; //  6 - 57 Código da empresa fornecido pelo Banrisul S.A.(*)
  $imp .= db_formatar(substr($rh34_convenio,0,3),'s','0',3,'e',0 )         ; //  3 - 63 Código do convenio da empresa fornecido pelo Banrisul S.A. (*)
  $imp .= ' '                                                              ; //  1 - 66 Brancos
  $imp .= $cod_folha                                                       ; //  2 - 67 01- contra-cheque, 02-folha complementar ,03 ¿ 13º salário ,04-abono , 05- outras folhas  e 06 - outros.
  $imp .= '  '                                                             ; //  2 - 69 Brancos
  $imp .= '00'                                                             ; //  2 - 71 Preecher com zeros
  $imp .= db_formatar($sequencia,'s','0',8,'e',0)                          ; //  8 - 73 Numero sequêncial do registro

  fputs($arquivo_txt,$imp."\r\n");

  $sequencia ++;

//// Registro tipo 05 - Informações de Cabeçalho    (registro opcional)
  $cnpj_inst = db_formatar($cgc,'cnpj');
  $texto = substr($nomeinst,0,27).$cnpj_inst   ;
  //1234 1234567890123456789012 999.99 999999.99  p '

  $imp  = '05'                                                             ; //  1 - 2  Identificador do registro detalhe. Preencher com a constante ¿05¿.
  $imp .= db_formatar(substr(trim($nomeinstabrev),0,27).$cnpj_inst,'s',' ',48,'d',0 ) ; // 48 - 3  Nome da Instituição + CNPJ
  $imp .= str_repeat(' ',22)                                               ; // 22 - 51
  $imp .= db_formatar($sequencia,'s','0',8,'e',0)                          ; //  8 - 73 Numero sequêncial do registro
  fputs($arquivo_txt,$imp."\r\n");
  $sequencia ++;


  $total_servidores = 0;
  $registroaux = 0;
  for($x_matricula = 0;$x_matricula < pg_numrows($result_matriculas);$x_matricula++){
    db_fieldsmemory($result_matriculas,$x_matricula);

    //// Registro tipo 10 - Informações do funcionário

    $imp  = '10'                                                           ; //  1 -  2 Identificador do registro detalhe. Preencher com a constante ¿10¿.
    $imp .= db_formatar($agencia,'s','0',4,'e',0)                          ; //  4 -  3 Agencia do funcionário.
    $imp .= db_formatar(($contacorrente.$dvcontacorrente),'s','0',10,'e',0); // 10 -  7 Conta Corrente do funcionário
    $imp .= db_formatar($matricula,'s','0',16,'e',0)                       ; // 16 - 17 Matricula do funcionário ¿ campo não obrigatório
    $imp .= db_formatar($nome,'s',' ',35,'d',0)                            ; // 35 - 33 Nome do funcionário
    $imp .= str_repeat(' ',5)                                 ; //  5 - 68 Preencher com brancos
    $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
    fputs($arquivo_txt,$imp."\r\n");
    $sequencia ++;
    $total_servidores ++;

    $imp  = '20'                                              ; //  1 - 2  Identificador do registro detalhe. Preencher com a constante ¿05¿.
    $imp .= db_formatar(substr($xtipo_folha,0,48),'s',' ',48,'d',0 ) ; // 48 - 3  Tipo de Folha e perído
    $imp .= str_repeat(' ',22)                                ; // 22 - 51
    $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
    fputs($arquivo_txt,$imp."\r\n");
    $sequencia ++;

    $texto1 = 'Nome: '.$nome;
    $imp  = '20'                                              ; //  1 - 2  Identificador do registro detalhe. Preencher com a constante ¿05¿.
    $imp .= db_formatar(substr($texto1,0,48),'s',' ',48,'d',0 ) ; // 48 - 3  Tipo de Folha e perído
    $imp .= str_repeat(' ',22)                                ; // 22 - 51
    $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
    fputs($arquivo_txt,$imp."\r\n");
    $sequencia ++;

    $texto2 = substr('Matricula: '.$matricula.' Cargo: '.$funcao,0,48);
    $imp  = '20'                                              ; //  1 - 2  Identificador do registro detalhe. Preencher com a constante ¿05¿.
    $imp .= db_formatar(substr($texto2,0,48),'s',' ',48,'d',0 ) ; // 48 - 3  Tipo de Folha e perído
    $imp .= str_repeat(' ',22)                                ; // 22 - 51
    $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
    fputs($arquivo_txt,$imp."\r\n");
    $sequencia ++;


  $sql_prov =
  "
  select
    z01_nome                as nome
  , z01_cgccpf              as cpf
  , trim(rh44_agencia)      as agencia
  , rh44_conta              as contacorrente
  , r70_estrut              as lotacao
  , r70_descr               as descr_lotacao
  , substr(rh37_descr,1,25) as funcao
  , rh01_admiss             as admissao

  ,".$folha."_regist        as registro
  ,".$folha."_rubric        as rubrica
  ,rh27_descr               as descricao
  ,".$folha."_quant         as quantidade
  ,".$folha."_pd            as pd
  ,".$folha."_valor         as valor

  , (select count( ".$folha."_rubric ) from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_pd != 3  )  as linhas

  , (select coalesce(sum( ".$folha."_valor ),0) from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_rubric in ( 'R981','R982','R983') )  as IR

  , (select coalesce( ".$folha."_valor,0)  from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_rubric in ( 'R992' ) )  as PREVIDENCIA

  , (select coalesce( ".$folha."_valor,0)  from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_rubric in ( 'R991' ) )  as FGTS

   from ".$arquivo."

   left outer join rhrubricas
     on rh27_rubric = ".$folha."_rubric
    and rh27_instit = ".$folha."_instit

  inner join rhpessoal
     on rh01_regist = ".$folha."_regist

   left outer join rhpessoalmov
     on rh02_anousu = ".$ano."
    and rh02_mesusu = ".$mes."
    and rh02_regist = ".$folha."_regist
    and rh02_instit = ".$folha."_instit

  inner join rhlota
     on rh02_lota  = r70_codigo
    and r70_instit = rh02_instit


   left join rhpesbanco
     on rh02_seqpes = rh44_seqpes
   left join rhfuncao
     on rh37_funcao = rh02_funcao
    and rh37_instit = rh02_instit

   left join cgm
     on rh01_numcgm = z01_numcgm

   where ".$folha."_anousu = ".$ano."
     and ".$folha."_mesusu = ".$mes."
     and ".$folha."_pd = 1
     and ".$folha."_regist = $matricula
     and rh44_codban = '041'
     and rh02_instit = ".$folha."_instit
   order by ".$folha."_regist , ".$folha."_pd, ".$folha."_rubric ";

// echo $sql_valores;exit;

  $result_prov = pg_query($sql_prov);

  $x_matric_compara = 0;
  $prov = 0;
  for($x_prov = 0;$x_prov < pg_numrows($result_prov);$x_prov++){
    db_fieldsmemory($result_prov,$x_prov);
/*
    if($x_matric_compara != $registro ){
      $imp  = '20'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿20¿.
      $imp .= db_formatar('P R O V E N T O S','s',' ',48,'d',0) ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
      $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
      $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
      fputs($arquivo_txt,$imp."\r\n")                               ; //
      $sequencia ++;

      $imp  = '20'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿20¿.
      $imp .= 'COD. DESCRICAO               QUANT         VALOR'; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
      //      '9999                        9.999,99 999.999,99+';
      $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
      $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
      fputs($arquivo_txt,$imp."\r\n")                               ; //
      $sequencia ++;


    }
*/

    $prov  += $valor;

    $texto = $rubrica.' '.db_formatar(substr($descricao,0,22),'s',' ',22,'d',0).' '.db_formatar(trim(db_formatar($quantidade,'f')),'s',' ',7,'e',0).' '.db_formatar(trim(db_formatar($valor,'f')),'s',' ',11,'e',0).'+';

      /// Registro Tipo 30 ¿ Linha do documento     Proventos   (registro opcional)
      $imp  = '30'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿30¿.
      $imp .= db_formatar($texto,'s',' ',48,'d',0)              ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
      $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
      $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
      fputs($arquivo_txt,$imp."\r\n")                               ; //
      $sequencia ++;

    $x_matric_compara = $registro;

  }

   /// Registro Tipo 30 ¿ Linha do documento     Proventos   (registro opcional)
   $tot_prov = 'TOTAL DE VENCIMENTOS                 '.db_formatar(trim(db_formatar($prov,'f')),'s',' ',11,'e',0);
   $imp  = '30'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿30¿.
   $imp .= db_formatar($tot_prov,'s',' ',48,'d',0)              ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
   $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
   $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
   fputs($arquivo_txt,$imp."\r\n")                               ; //
   $sequencia ++;


  $sql_desc =
  "
  select
    z01_nome                as nome
  , z01_cgccpf              as cpf
  , trim(rh44_agencia)      as agencia
  , rh44_conta              as contacorrente
  , r70_estrut              as lotacao
  , r70_descr               as descr_lotacao
  , substr(rh37_descr,1,25) as funcao
  , rh01_admiss             as admissao

  ,".$folha."_regist        as registro
  ,".$folha."_rubric        as rubrica
  ,rh27_descr               as descricao
  ,".$folha."_quant         as quantidade
  ,".$folha."_pd            as pd
  ,".$folha."_valor         as valor

  , (select count( ".$folha."_rubric ) from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_pd != 3  )  as linhas

  , (select coalesce(sum( ".$folha."_valor ),0) from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_rubric in ( 'R981','R982','R983') )  as IR

  , (select coalesce( ".$folha."_valor,0)  from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_rubric in ( 'R992' ) )  as PREVIDENCIA

  , (select coalesce( ".$folha."_valor,0)  from ".$arquivo."
     where ".$folha."_anousu = ".$ano."
       and ".$folha."_mesusu = ".$mes."
       and ".$folha."_regist = rh01_regist
       and ".$folha."_rubric in ( 'R991' ) )  as FGTS

   from ".$arquivo."

   left outer join rhrubricas
     on rh27_rubric = ".$folha."_rubric
    and rh27_instit = ".$folha."_instit

  inner join rhpessoal
     on rh01_regist = ".$folha."_regist

   left outer join rhpessoalmov
     on rh02_anousu = ".$ano."
    and rh02_mesusu = ".$mes."
    and rh02_regist = ".$folha."_regist
    and rh02_instit = ".$folha."_instit

  inner join rhlota
     on rh02_lota  = r70_codigo
    and r70_instit = rh02_instit


   left join rhpesbanco
     on rh02_seqpes = rh44_seqpes
   left join rhfuncao
     on rh37_funcao = rh02_funcao
    and rh37_instit = rh02_instit

   left join cgm
     on rh01_numcgm = z01_numcgm

   where ".$folha."_anousu = ".$ano."
     and ".$folha."_mesusu = ".$mes."
     and ".$folha."_pd = 2
     and ".$folha."_regist = $matricula
     and rh44_codban = '041'
     and rh02_instit = ".$folha."_instit
   order by ".$folha."_regist , ".$folha."_pd, ".$folha."_rubric ";

// echo $sql_valores;exit;

  $result_desc = pg_query($sql_desc);

  $x_matric_compara = 0;
  $desc = 0;
  for($x_desc = 0;$x_desc < pg_numrows($result_desc);$x_desc++){
    db_fieldsmemory($result_desc,$x_desc);
/*
    if($x_matric_compara != $registro ){
      $imp  = '20'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿20¿.
      $imp .= db_formatar('D E S C O N T O S','s',' ',48,'d',0) ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
      $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
      $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
      fputs($arquivo_txt,$imp."\r\n")                               ; //
      $sequencia ++;

      $imp  = '20'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿20¿.
      $imp .= 'COD. DESCRICAO               QUANT         VALOR'; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
      //      '9999                        9.999,99 999.999,99+';
      $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
      $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
      fputs($arquivo_txt,$imp."\r\n")                               ; //
      $sequencia ++;

    }
*/
    $desc  += $valor;

    $texto = $rubrica.' '.db_formatar(substr($descricao,0,22),'s',' ',22,'d',0).' '.db_formatar(trim(db_formatar($quantidade,'f')),'s',' ',7,'e',0).' '.db_formatar(trim(db_formatar($valor,'f')),'s',' ',11,'e',0).'-';

      /// Registro Tipo 30 ¿ Linha do documento     Proventos   (registro opcional)
      $imp  = '40'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿30¿.
      $imp .= db_formatar($texto,'s',' ',48,'d',0)              ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
      $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
      $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
      fputs($arquivo_txt,$imp."\r\n")                               ; //
      $sequencia ++;

    $x_matric_compara = $registro;

  }
   /// Registro Tipo 30 ¿ Linha do documento     Proventos   (registro opcional)
   $tot_desc = 'TOTAL DE DESCONTOS                   '.db_formatar(trim(db_formatar($desc,'f')),'s',' ',11,'e',0);
   $imp  = '40'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿30¿.
   $imp .= db_formatar($tot_desc,'s',' ',48,'d',0)              ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
   $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
   $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
   fputs($arquivo_txt,$imp."\r\n")                               ; //
   $sequencia ++;


   /// Registro Tipo 30 ¿ Linha do documento     Proventos   (registro opcional)
   $tot_liq  = 'TOTAL DE LIQUIDO                     '.db_formatar(trim(db_formatar(($prov - $desc),'f')),'s',' ',11,'e',0);
   $imp  = '50'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿30¿.
   $imp .= db_formatar($tot_liq,'s',' ',48,'d',0)              ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
   $imp .= str_repeat(' ',22)                                ; // 22 - 51 Preencher com brancos
   $imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
   fputs($arquivo_txt,$imp."\r\n")                               ; //
   $sequencia ++;






}

/// Registro Tipo 99 Trailler  de Arquivo
$imp  = '99'                                              ; //  2 -  1 Identificador do registro detalhe. Preencher com a constante ¿30¿.
$imp .= db_formatar($total_servidores,'s','0',6,'e',0)    ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
$imp .= db_formatar(($sequencia),'s','0',8,'e',0)       ; // 48 -  3 Informações que vão ser impressas nos Terminais Clientes
$imp .= str_repeat(' ',56)                                ; // 22 - 51 Preencher com brancos
$imp .= db_formatar($sequencia,'s','0',8,'e',0)           ; //  8 - 73 Numero sequêncial do registro
fputs($arquivo_txt,$imp."\r\n")                           ; //
$sequencia ++;

fclose($arquivo_txt);
}

?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>
function js_verifica(){
  var anoi = new Number(document.form1.datai_ano.value);
  var anof = new Number(document.form1.dataf_ano.value);
  if(anoi.valueOf() > anof.valueOf()){
    alert('Intervalo de data invalido. Velirique !.');
    return false;
  }
  return true;
}


function js_emite(){
  qry = "";
  if(document.form1.rh40_sequencia){
    qry = "&rh40_sequencia="+document.form1.rh40_sequencia.value;
  }
  jan = window.open('pes2_eldcontracheque002.php?folha='+document.form1.arquivo.value+
                                                 '&ano='+document.form1.DBtxt23.value+
                                                 '&mes='+document.form1.DBtxt25.value+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
}
</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>

  <table  align="center">
    <form name="form1" method="post" action="" onsubmit="return js_verifica();">
      <tr>
         <td >&nbsp;</td>
         <td >&nbsp;</td>
      </tr>
      <tr >
        <td align="left" nowrap title="Digite o Ano / Mes de competência" >
        <strong>Ano / Mês :&nbsp;&nbsp;</strong>
        </td>
        <td>
          <?php
           $DBtxt23 = db_anofolha();
           db_input('DBtxt23',4,$IDBtxt23,true,'text',2,'')
          ?>
          &nbsp;/&nbsp;
          <?php
           $DBtxt25 = db_mesfolha();
           db_input('DBtxt25',2,$IDBtxt25,true,'text',2,'')
          ?>
        </td>
      </tr>
  <tr>
    <td><b>Data do Depósito:</b></td>
    <td>
      <?php
      if((!isset($datadeposit_dia) || (isset($datadeposit_dia) && trim($datadeposit_dia) == "")) && (!isset($datadeposit_mes) || (isset($datadeposit_mes) && trim($datadeposit_mes) == "")) && (!isset($datadeposit_ano) || (isset($datadeposit_ano) && trim($datadeposit_ano) == ""))){
        $datadeposit_dia = "";
        $datadeposit_mes = "";
        $datadeposit_ano = "";
      }
      db_inputdata('datadeposit',@$datadeposit_dia,@$datadeposit_mes,@$datadeposit_ano,true,'text',1,"");
      ?>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap title="<?=@$Trh34_codarq?>">
      <?php db_ancora(@$Lrh34_codarq,"js_pesquisa(true);",1);?>
    </td>
    <td align="left" nowrap colspan="3">
      <?php db_input("rh34_codarq",6,@$Irh34_codarq,true,"text",4,"onchange='js_pesquisa(false);'");?>
      <?php db_input("rh34_descr",40,@$Irh34_descr,true,"text",3);?>
      <?php db_input("rodape",40,0,true,"hidden",3);?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh34_codban?>">
      <?php
      db_ancora(@$Lrh34_codban,"js_pesquisarh34_codban(true);",1);
      ?>
    </td>
    <td colspan="3">
      <?php
      db_input('rh34_codban',6,$Irh34_codban,true,'text',1," onchange='js_pesquisarh34_codban(false);'")
      ?>
      <?php
      db_input('db90_descr',40,$Idb90_descr,true,'text',3,'')
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh34_agencia?>">
      <?=@$Lrh34_agencia?>
    </td>
    <td>
      <?php
      db_input('rh34_agencia',5,$Irh34_agencia,true,'text',1,"")
      ?>
    </td>
    <td nowrap title="<?=@$Trh34_dvagencia?>" align="right">
      <?=@$Lrh34_dvagencia?>
    </td>
    <td>
      <?php
      db_input('rh34_dvagencia',2,$Irh34_dvagencia,true,'text',1,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="Código da Empresa">
    <B>Código da Empresa
    </td>
    <td>
      <?php
      db_input('rh34_conta',15,$Irh34_conta,true,'text',1,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh34_convenio?>">
      <?=@$Lrh34_convenio?>
    </td>
    <td>
      <?php
      db_input('rh34_convenio',15,$Irh34_convenio,true,'text',1,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh34_sequencial?>">
      <?=@$Lrh34_sequencial?>
    </td>
    <td>
      <?php
      db_input('rh34_sequencial',15,$Irh34_sequencial,true,'text',1,"")
      ?>
    </td>
  </tr>
  <tr>
    <td><b>Arquivo:</b</td>
    <td>
     <?php
       $x = array("r14"=>"Salário","r48"=>"Complementar","r35"=>"13o. Salário","r22"=>"Adiantamento");
       db_select('arquivo',$x,true,4,"onchange='document.form1.submit();'");
     ?>
    </td>
     </tr>
     <?php
     if(isset($arquivo) && $arquivo == "r48"){
         $clgerfcom = new \cl_gerfcom();
         $sqlresource = $clgerfcom->sql_query_file($DBtxt23,$DBtxt25,null,null,"distinct r48_semest as rh40_sequencia");
         $result_semest = $clgerfcom->sql_record($sqlresource);
       if($clgerfcom->numrows > 0){
	 echo "
	  <tr>
	    <td align='left' title='Nro. Complementar'><strong>Nro. Complementar:</strong></td>
            <td>
	      <select name='rh40_sequencia'>
		<option value = '0'>Todos
	      ";
	      for($i=0; $i<$clgerfcom->numrows; $i++){
		db_fieldsmemory($result_semest, $i);
		echo "<option value = '$rh40_sequencia'>$rh40_sequencia";
	      }
	 echo "
	    </td>
	  </tr>
	      ";
       }else{
         echo "
               <tr>
                 <td colspan='2' align='center'>
                   <font color='red'>Sem complementar para este período.</font>
                 </td>
               </tr>
              ";
       }
     }
     ?>
      <tr>
        <td colspan="2" align = "center">
          <input  name="emite" id="emite" type="submit" value="Processar"  >
        </td>
      </tr>

  </form>
    </table>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
  <?php
  if(isset($emite)){
  	echo "js_montarlista('".$arq."#Arquivo gerado em: ".$arq."','form1');";
  }
  ?>
function js_pesquisa(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('top.corpo','db_iframe_rharqbanco','func_rharqbanco.php?funcao_js=parent.js_mostra1|rh34_codarq|rh34_descr','Pesquisa',true);
  }else{
    if(document.form1.rh34_codarq.value != ''){
      js_OpenJanelaIframe('top.corpo','db_iframe_rharqbanco','func_rharqbanco.php?pesquisa_chave='+document.form1.rh34_codarq.value+'&funcao_js=parent.js_mostra','Pesquisa',false);
    }else{
      document.form1.rh34_codarq.value = '';
      document.form1.rh34_descr.value = '';
      location.href = 'pes2_aleemitearqbanco001.php';
    }
  }
}
function js_mostra(chave,erro){
  if(erro==true){
    document.form1.rh34_descr.value = chave;
    document.form1.rh34_codarq.value = '';
    document.form1.rh34_codarq.focus();
    location.href = 'pes2_aleemitearqbanco001.php';
  }else{
    document.form1.submit();
  }
}
function js_mostra1(chave1,chave2){
  document.form1.rh34_codarq.value = chave1;
  document.form1.submit();
  db_iframe_rharqbanco.hide();
}
function js_pesquisarh34_codban(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('top.corpo','db_iframe_db_bancos','func_db_bancos.php?funcao_js=parent.js_mostradb_bancos1|db90_codban|db90_descr','Pesquisa',true);
  }else{
    if(document.form1.rh34_codban.value != ''){
      js_OpenJanelaIframe('top.corpo','db_iframe_db_bancos','func_db_bancos.php?pesquisa_chave='+document.form1.rh34_codban.value+'&funcao_js=parent.js_mostradb_bancos','Pesquisa',false);
    }else{
      document.form1.db90_descr.value = '';
    }
  }
}
function js_mostradb_bancos(chave,erro){
  document.form1.db90_descr.value = chave;
  if(erro==true){
    document.form1.rh34_codban.focus();
    document.form1.rh34_codban.value = '';
  }
}
function js_mostradb_bancos1(chave1,chave2){
  document.form1.rh34_codban.value = chave1;
  document.form1.db90_descr.value = chave2;
  db_iframe_db_bancos.hide();
}
</script>
<?php
if(isset($emite2)){
  if($clrharqbanco->erro_status=="0"){
    $clrharqbanco->erro(true,false);
    $db_botao=true;
    if($clrharqbanco->erro_campo!=""){
      echo "<script> document.form1.".$clrharqbanco->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clrharqbanco->erro_campo.".focus();</script>";
    };
  }else{
    echo "<script>js_emite();</script>";
  };
}
?>
