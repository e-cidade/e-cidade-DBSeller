<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

class cl_fis_grupotipoandamento_tipoandam{
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

   public $fi30_sequencial = null;
   public $fi30_grupo  = 0;
   public $fi30_tipoandam = 0;

   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_grupotipoandamento_tipoandam");
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

   // somente para o campo data
   public function atualizacampos() {

   }

   // funcao para inclusao
   public function incluir (){
    $this->atualizacampos();

     if($this->fi30_grupo == "" ){
       $this->erro_sql    = "Grupo não informado.";
       $this->erro_campo  = "fi30_grupo";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->fi30_tipoandam == "" ){
       $this->erro_sql    = "Tipo de andamento não informado.";
       $this->erro_campo  = "fi30_grupo";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into fiscalizacao.fis_grupotipoandamento_tipoandam(
                            fi30_grupo
                           ,fi30_tipoandam
                       )
                values (
                             $this->fi30_grupo
                           , $this->fi30_tipoandam
                      )";
     $result = db_query($sql);

     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = $sql."grupotipoandamento_tipoandam - Erro ao incluir no banco (duplicate key). Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Erro!!!!";
         $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = $sql."grupotipoandamento_tipoandam - Erro ao incluir no banco. Inclusao Abortada.";
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
   public function alterar () {

     if($this->fi30_sequencial == null ){
       $this->erro_sql    = "Código não informado.";
       $this->erro_campo  = "fi30_grupo";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->fi30_grupo == "" ){
       $this->erro_sql    = "Grupo não informado.";
       $this->erro_campo  = "fi30_grupo";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->fi30_tipoandam == "" ){
       $this->erro_sql    = "Tipo de andamento não informado.";
       $this->erro_campo  = "fi30_grupo";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = " update fiscalizacao.fis_grupotipoandamento_tipoandam set
                 fi30_grupo     = $this->fi30_grupo
                ,fi30_tipoandam = $this->fi30_tipoandam
              where
                fi30_sequencial = $this->fi30_sequencial ";

     $result = db_query($sql);

     if($result==false ){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "grupotipoandamento_tipoandam nao Alterado. Alteracao Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->fi30_sequencial;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco      = "";
         $this->erro_sql        = "grupotipoandamento_tipoandam nao foi Alterado. Alteracao Abortada.\\n";
         $this->erro_sql       .= "Valores : ".$this->fi30_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->fi30_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir () {
      if($this->fi30_sequencial != null){
       $sql = " delete from fiscalizacao.fis_grupotipoandamento_tipoandam
                        where fi30_sequencial = $this->fi30_sequencial";

      }
      $result = db_query($sql);
     if($result==false){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "grupotipoandamento_tipoandam nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->fi30_sequencial;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco      = "";
         $this->erro_sql        = "grupotipoandamento_tipoandam nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql       .= "Valores : ".$this->fi30_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->fi30_sequencial;
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
