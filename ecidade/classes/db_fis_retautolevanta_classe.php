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
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

//MODULO: fiscal
//CLASSE DA ENTIDADE retautolevanta
class cl_fis_retautolevanta {
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
   public $pl12_levantaold = 0;
   public $pl12_levantaret = 0;
   public $pl12_retcod     = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 pl12_levantaold = Código do Levantamento Antigo
                 pl12_levantaret = Código do Levantamento Novo
                 pl12_retcod     = Chave pl12_codigo da tabela retauto
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_retautolevanta");
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
   // // funcao para inclusao
   public function incluir(){
     // if(($this->pl12_levantaold == null) || ($this->pl12_levantaold == "") ){
     //   $this->erro_sql    = " Campo Codigo do Levantamento Antigo nao declarado.";
     //   $this->erro_banco  = " Chave Estrangeira zerada.";
     //   $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }
     // if(($this->pl12_levantaret == null) || ($this->pl12_levantaret == "") ){
     //   $this->erro_sql    = " Campo do Levantamento Novo nao declarado.";
     //   $this->erro_banco  = "Chave Primaria zerada.";
     //   $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }
     // if(($this->pl12_retcod == null) || ($this->pl12_retcod == "") ){
     //   $this->erro_sql    = " Campo do Auto Retificado nao declarado.";
     //   $this->erro_banco  = "Chave Primaria zerada.";
     //   $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     $sql = "insert into fiscalizacao.fis_retautolevanta(
                                       pl12_levantaold
                                      ,pl12_levantaret
                                      ,pl12_retcod
                       )
                values (
                                      $this->pl12_levantaold
                                     ,$this->pl12_levantaret
                                     ,$this->pl12_retcod
                      )";
// die($sql);
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql    = "retautolevanta ($this->pl12_levantaold."-".$this->pl12_levantaret) nao Incluído. Inclusao Abortada.";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco  = "Processo Fiscal já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql    = "retautolevanta ($this->pl12_levantaold."-".$this->pl12_levantaold) nao Incluído. Inclusao Abortada.";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco  = "";
     $this->erro_sql    = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql   .= "Valores : ".$this->pl12_levantaold."-".$this->pl12_levantaold;
     $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($pl12_codigo=null) {
     $sql = " update fiscalizacao.fis_retautolevanta set ";
     $virgula = "";
     if(trim($this->pl12_levantaold)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl12_levantaold"])){
       $sql  .= $virgula." pl12_levantaold = $this->pl12_levantaold ";
       $virgula = ",";
       if(trim($this->pl12_levantaold) == null ){
         $this->erro_sql    = " Campo Levantamento Antigo nao Informado.";
         $this->erro_campo  = "pl12_levantaold";
         $this->erro_banco  = "";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pl12_levantaret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl12_levantaret"])){
       $sql  .= $virgula." pl12_levantaret = $this->pl12_levantaret ";
       $virgula = ",";
       if(trim($this->pl12_levantaret) == null ){
         $this->erro_sql    = " Campo Novo Levantamento Nao Informado.";
         $this->erro_campo  = "pl12_levantaret";
         $this->erro_banco  = "";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pl12_retcod)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl12_retcod"])){
       $sql  .= $virgula." pl12_retcod = $this->pl12_retcod ";
       $virgula = ",";
       if(trim($this->pl12_retcod) == null ){
         $this->erro_sql    = " Campo Auto Retificado Nao Informado.";
         $this->erro_campo  = "pl12_retcod";
         $this->erro_banco  = "";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($pl12_codigo!=null){
       $sql .= " pl12_codigo = $pl12_codigo";
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "retautolevanta nao Alterado. Alteracao Abortada.\\n";
       $this->erro_sql   .= "Valores : ".$this->pl12_levantaold."-".$this->pl12_levantaret;
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco  = "";
         $this->erro_sql    = "retautolevanta nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql   .= "Valores : ".$this->pl12_levantaold."-".$this->pl12_levantaret;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pl12_levantaold."-".$this->pl12_levantaret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($pl12_codigo,$dbwhere=null) {

     $sql = " delete from fiscalizacao.fis_retautolevanta
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($pl12_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " pl12_codigo = $pl12_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "retautolevanta nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql   .= "Valores : ".$pl12_codigo;
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco  = "";
         $this->erro_sql    = "retautolevanta nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql   .= "Valores : ".$pl12_codigo;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco  = "";
         $this->erro_sql    = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql   .= "Valores : ".$pl12_codigo;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
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
       $this->numrows     = 0;
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "Erro ao selecionar os registros.";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco   = "";
        $this->erro_sql     = "Record Vazio na Tabela: retautolevanta";
        $this->erro_msg     = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg    .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status  = "0";
        return false;
      }
     return $result;
   }
   public function sql_query_file ( $pl12_levantaold=null,$pl12_levantaret=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from fiscalizacao.fis_retautolevanta ";
     $sql2 = "";
     if($dbwhere==""){
       if($pl12_levantaold!=null ){
         $sql2 .= " where pl12_levantaold = $pl12_levantaold ";
       }
       if($pl12_levantaret!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " pl12_levantaret = $pl12_levantaret ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
}
?>
