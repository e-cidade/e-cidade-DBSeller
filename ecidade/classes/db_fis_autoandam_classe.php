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
//CLASSE DA ENTIDADE autoandam
class cl_fis_autoandam {
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
   public $y58_codauto = 0;
   public $y58_codandam = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y58_codauto = int4 = Código do Auto de Infração
                 y58_codandam = int8 = Codigo do Andamento Gerado
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_autoandam");
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
       $this->y58_codauto = ($this->y58_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y58_codauto"]:$this->y58_codauto);
       $this->y58_codandam = ($this->y58_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y58_codandam"]:$this->y58_codandam);
     }else{
       $this->y58_codauto = ($this->y58_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y58_codauto"]:$this->y58_codauto);
       $this->y58_codandam = ($this->y58_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y58_codandam"]:$this->y58_codandam);
     }
   }
   // funcao para inclusao
public function incluir ($y58_codauto,$y58_codandam){
      $this->atualizacampos();
       $this->y58_codauto = $y58_codauto;
       $this->y58_codandam = $y58_codandam;
     if(($this->y58_codauto == null) || ($this->y58_codauto == "") ){
       $this->erro_sql = " Campo y58_codauto nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y58_codandam == null) || ($this->y58_codandam == "") ){
       $this->erro_sql = " Campo y58_codandam nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_autoandam(
                                       y58_codauto
                                      ,y58_codandam
                       )
                values (
                                $this->y58_codauto
                               ,$this->y58_codandam
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "autoandam ($this->y58_codauto."-".$this->y58_codandam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "autoandam já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "autoandam ($this->y58_codauto."-".$this->y58_codandam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y58_codauto."-".$this->y58_codandam;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y58_codauto=null,$y58_codandam=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_autoandam set ";
     $virgula = "";
     if(trim($this->y58_codauto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y58_codauto"])){
       $sql  .= $virgula." y58_codauto = $this->y58_codauto ";
       $virgula = ",";
       if(trim($this->y58_codauto) == null ){
         $this->erro_sql = " Campo Código do Auto de Infração nao Informado.";
         $this->erro_campo = "y58_codauto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y58_codandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y58_codandam"])){
       $sql  .= $virgula." y58_codandam = $this->y58_codandam ";
       $virgula = ",";
       if(trim($this->y58_codandam) == null ){
         $this->erro_sql = " Campo Codigo do Andamento Gerado nao Informado.";
         $this->erro_campo = "y58_codandam";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y58_codauto!=null){
       $sql .= " y58_codauto = $this->y58_codauto";
     }
     if($y58_codandam!=null){
       $sql .= " and  y58_codandam = $this->y58_codandam";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "autoandam nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y58_codauto."-".$this->y58_codandam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "autoandam nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y58_codauto."-".$this->y58_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y58_codauto."-".$this->y58_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y58_codauto=null,$y58_codandam=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_autoandam
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y58_codauto != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y58_codauto = $y58_codauto ";
        }
        if($y58_codandam != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y58_codandam = $y58_codandam ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "autoandam nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y58_codauto."-".$y58_codandam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "autoandam nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y58_codauto."-".$y58_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y58_codauto."-".$y58_codandam;
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
        $this->erro_sql   = "Record Vazio na Tabela:autoandam";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y58_codauto=null,$y58_codandam=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autoandam ";
     $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_autoandam.y58_codandam";
     $sql .= "      inner join fiscalizacao.fis_auto  on  fis_auto.y50_codauto = fis_autoandam.y58_codauto";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_fandam.y39_id_usuario";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fandam.y39_codtipo";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_auto.y50_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($y58_codauto!=null ){
         $sql2 .= " where fis_autoandam.y58_codauto = $y58_codauto ";
       }
       if($y58_codandam!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_autoandam.y58_codandam = $y58_codandam ";
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
public function sql_query_file ( $y58_codauto=null,$y58_codandam=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autoandam ";
     $sql2 = "";
     if($dbwhere==""){
       if($y58_codauto!=null ){
         $sql2 .= " where fis_autoandam.y58_codauto = $y58_codauto ";
       }
       if($y58_codandam!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_autoandam.y58_codandam = $y58_codandam ";
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
