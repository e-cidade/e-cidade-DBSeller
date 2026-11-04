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

class cl_fis_tipoandam_datafim{
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

   public $fi31_sequencial = null;
   public $fi31_data       = '';
   public $fi31_tipoandam  = 0;

   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_tipoandam_datafim");
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
     $this->fi31_data_dia = ($this->fi31_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["fi31_data_dia"]:$this->fi31_data_dia);
     $this->fi31_data_mes = ($this->fi31_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["fi31_data_mes"]:$this->fi31_data_mes);
     $this->fi31_data_ano = ($this->fi31_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["fi31_data_ano"]:$this->fi31_data_ano);
     if($this->fi31_data_dia != ""){
        $this->fi31_data = $this->fi31_data_ano."-".$this->fi31_data_mes."-".$this->fi31_data_dia;
     }
   }

   // funcao para inclusao
   public function incluir (){
    $this->atualizacampos();

     if($this->fi31_data == '' ){
       $this->erro_sql    = "Data fim não informado.";
       $this->erro_campo  = "fi31_data";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->fi31_tipoandam == "" ){
       $this->erro_sql    = "Tipo de andamento para a data fim não informado.";
       $this->erro_campo  = "fi31_data";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into fiscalizacao.fis_tipoandam_datafim(
                            fi31_data
                           ,fi31_tipoandam
                       )
                values (
                            '$this->fi31_data'
                           , $this->fi31_tipoandam
                      )";
     $result = db_query($sql);

     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = $sql."tipoandam_datafim - Erro ao incluir no banco (duplicate key). Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Erro!!!!";
         $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = $sql."tipoandam_datafim - Erro ao incluir no banco. Inclusao Abortada.";
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
     $this->atualizacampos();

     if($this->fi31_sequencial == null ){
       $this->erro_sql    = "Código não informado.";
       $this->erro_campo  = "fi31_data";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->fi31_data == '' ){
       $this->erro_sql    = "Tipo de andamento para a data fim não informado.";
       $this->erro_campo  = "fi31_data";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->fi31_tipoandam == "" ){
       $this->erro_sql    = "Tipo de andamento para a data fim não informado.";
       $this->erro_campo  = "fi31_data";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = " update fiscalizacao.fis_tipoandam_datafim set
                 fi31_data      = '$this->fi31_data'
                ,fi31_tipoandam =  $this->fi31_tipoandam
              where
                fi31_sequencial =  $this->fi31_sequencial ";

     $result = db_query($sql);

     if($result==false ){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "tipoandam_datafim nao Alterado. Alteracao Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->fi31_sequencial;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco      = "";
         $this->erro_sql        = "tipoandam_datafim nao foi Alterado. Alteracao Abortada.\\n";
         $this->erro_sql       .= "Valores : ".$this->fi31_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->fi31_sequencial;
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
      if($this->fi31_sequencial != null){
       $sql = " delete from fiscalizacao.fis_tipoandam_datafim
                        where fi31_sequencial = $this->fi31_sequencial";

      }
      $result = db_query($sql);
     if($result==false){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "tipoandam_datafim nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->fi31_sequencial;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco      = "";
         $this->erro_sql        = "tipoandam_datafim nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql       .= "Valores : ".$this->fi31_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->fi31_sequencial;
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
