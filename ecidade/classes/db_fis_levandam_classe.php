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
//CLASSE DA ENTIDADE levandam
class cl_fis_levandam {
   // cria variaveis de erro
   public $rotulo     = null;
   public $query_sql  = null;
   public $numrows    = 0;
   public $numrows_incluir = 0;
   public $numrows_alterar = 0;
   public $numrows_excluir = 0;
   public $erro_status= null;
   public $erro_sql   = null;
   public $erro_banco = null;
   public $erro_msg   = null;
   public $erro_campo = null;
   public $pagina_retorno = null;
   // cria variaveis do arquivo
   public $y67_codlev = 0;
   public $y67_codandam = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y67_codlev = int4 = Código do Levantamento
                 y67_codandam = int8 = Codigo do Andamento Gerado
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_levandam");
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
   // funcao para atualizar campos
public function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->y67_codlev = ($this->y67_codlev == ""?@$GLOBALS["HTTP_POST_VARS"]["y67_codlev"]:$this->y67_codlev);
       $this->y67_codandam = ($this->y67_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y67_codandam"]:$this->y67_codandam);
     }else{
       $this->y67_codlev = ($this->y67_codlev == ""?@$GLOBALS["HTTP_POST_VARS"]["y67_codlev"]:$this->y67_codlev);
       $this->y67_codandam = ($this->y67_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y67_codandam"]:$this->y67_codandam);
     }
   }
   // funcao para inclusao
public function incluir ($y67_codlev,$y67_codandam){
      $this->atualizacampos();
       $this->y67_codlev = $y67_codlev;
       $this->y67_codandam = $y67_codandam;
     if(($this->y67_codlev == null) || ($this->y67_codlev == "") ){
       $this->erro_sql = " Campo y67_codlev nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y67_codandam == null) || ($this->y67_codandam == "") ){
       $this->erro_sql = " Campo y67_codandam nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_levandam(
                                       y67_codlev
                                      ,y67_codandam
                       )
                values (
                                $this->y67_codlev
                               ,$this->y67_codandam
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "levandam ($this->y67_codlev."-".$this->y67_codandam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "levandam já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "levandam ($this->y67_codlev."-".$this->y67_codandam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y67_codlev."-".$this->y67_codandam;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y67_codlev=null,$y67_codandam=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_levandam set ";
     $virgula = "";
     if(trim($this->y67_codlev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y67_codlev"])){
       $sql  .= $virgula." y67_codlev = $this->y67_codlev ";
       $virgula = ",";
       if(trim($this->y67_codlev) == null ){
         $this->erro_sql = " Campo Código do Levantamento nao Informado.";
         $this->erro_campo = "y67_codlev";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y67_codandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y67_codandam"])){
       $sql  .= $virgula." y67_codandam = $this->y67_codandam ";
       $virgula = ",";
       if(trim($this->y67_codandam) == null ){
         $this->erro_sql = " Campo Codigo do Andamento Gerado nao Informado.";
         $this->erro_campo = "y67_codandam";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y67_codlev!=null){
       $sql .= " y67_codlev = $this->y67_codlev";
     }
     if($y67_codandam!=null){
       $sql .= " and  y67_codandam = $this->y67_codandam";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "levandam nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y67_codlev."-".$this->y67_codandam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "levandam nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y67_codlev."-".$this->y67_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y67_codlev."-".$this->y67_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y67_codlev=null,$y67_codandam=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_levandam
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y67_codlev != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y67_codlev = $y67_codlev ";
        }
        if($y67_codandam != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y67_codandam = $y67_codandam ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "levandam nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y67_codlev."-".$y67_codandam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "levandam nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y67_codlev."-".$y67_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y67_codlev."-".$y67_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao do recordset
public function sql_record($sql) {
     $result = db_query($sql);
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:levandam";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
}
?>
