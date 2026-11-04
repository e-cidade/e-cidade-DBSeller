<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
 *  junto com este programa; se não, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

class cl_fis_valordefla{
   // cria variaveis de erro
   public $rotulo          = null;
   public $query_sql       = null;
   public $numrows         = 0;
   public $numrows_incluir = 0;
   public $numrows_alterar = 0;
   public $numrows_excluir = 0;
   public $erro_status     = null;
   public $erro_sql        = null;
   public $erro_banco      = null;
   public $erro_msg        = null;
   public $erro_campo      = null;
   public $pagina_retorno  = null;
   // cria variaveis do arquivo

  public $yl63_sequencia   = null;
  public $yl63_seq         = 0;
  public $yl63_pagoriginal = 0;

  public $campos = "
                  yl63_sequencia integer,
                  yl63_seq integer,
                  yl63_pagoriginal double precision
                                ";

   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_valordefla");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro
   public function erro($mostra,$retorna) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

   // funcao para inclusao
   public function incluir ( $yl63_sequencia , $yl63_seq , $yl63_pagoriginal){


     $iSql    = "  insert into fiscalizacao.fis_valordefla(                      ";
     $iSql   .= "                       yl63_sequencia                  ";
     $iSql   .= "                     , yl63_seq                        ";
     $iSql   .= "                     , yl63_pagoriginal                ";
     $iSql   .= "                  )                                    ";
     $iSql   .= "           values (                                    ";
     $iSql   .= "                        $yl63_sequencia                ";
     $iSql   .= "                     , '$yl63_seq'                     ";
     $iSql   .= "                     , '$yl63_pagoriginal'             ";
     $iSql   .= "                 )                                     ";
     $rsInsert = db_query($iSql);

    if( $rsInsert == false ){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = $sql."valordefla - Erro ao incluir no banco (duplicate key). Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_msg  .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = $sql."valordefla - Erro ao incluir no banco. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status     = "0";
       $this->numrows_incluir = 0;
       return false;
     }
     $this->erro_banco      = "";
     $this->erro_sql        = "Inclusao efetuada com Sucesso\\n";
     $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status     = "1";
     $this->numrows_incluir = pg_affected_rows($result);
     return true;
   }

   // funcao para alteracao
   public function alterar ( $yl63_sequencia , $yl63_seq , $yl63_pagoriginal) {


     $uSql  = " update fiscalizacao.fis_valordefla set                           ";
     $uSql .= "       yl63_pagoriginal    = '$yl63_pagoriginal',        ";
     $uSql .= "          where                                          ";
     $uSql .= "              yl63_sequencia       = $yl63_sequencia     ";
     $uSql .= "              and yl63_seq         = $yl63_seq           ";

     $rsUpdate = db_query($uSql);

     if( $rsUpdate==false ){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "valordefla não Alterado. Alteração Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->end01_codpeca;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if( pg_affected_rows($rsUpdate) == 0 ){
         $this->erro_banco      = "";
         $this->erro_sql        = "valordefla não foi Alterado. Alteração Abortada.\\n";
         $this->erro_sql       .= "Valores : ".$this->end01_codpeca;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->end01_codpeca;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

   // funcao para exclusao
   public function excluir ( $yl63_sequencia , $yl63_seq ) {


      $dSql  = " delete from fiscalizacao.fis_valordefla  where                  ";
      $dSql .= "              yl63_sequencia      =  $yl63_sequencia    ";
      if($yl63_seq != ""){
        $dSql .= "              and yl63_seq        =  $yl63_seq          ";
      }
      $rsDelete = db_query($dSql);
     if( $rsDelete == false ){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "valordefla não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->pl36_sequencial;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if( pg_affected_rows($rsDelete) == 0 ){
         $this->erro_banco      = "";
         $this->erro_sql        = "valordefla não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql       .= "Valores : ".$this->pl36_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->pl36_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }

}


?>
