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
//CLASSE DA ENTIDADE fandamusu
class cl_fis_fandamusu {
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
   public $y40_codandam = 0;
   public $y40_id_usuario = 0;
   public $y40_obs = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y40_codandam = int8 = Codigo do Andamento Gerado
                 y40_id_usuario = int4 = Cod. Usuário
                 y40_obs = text = Observação do Usuário
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_fandamusu");
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
       $this->y40_codandam = ($this->y40_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y40_codandam"]:$this->y40_codandam);
       $this->y40_id_usuario = ($this->y40_id_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y40_id_usuario"]:$this->y40_id_usuario);
       $this->y40_obs = ($this->y40_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y40_obs"]:$this->y40_obs);
     }else{
       $this->y40_codandam = ($this->y40_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y40_codandam"]:$this->y40_codandam);
       $this->y40_id_usuario = ($this->y40_id_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y40_id_usuario"]:$this->y40_id_usuario);
     }
   }
   // funcao para inclusao
public function incluir ($y40_codandam,$y40_id_usuario){
      $this->atualizacampos();
     if($this->y40_obs == null ){
       $this->erro_sql = " Campo Observação do Usuário nao Informado.";
       $this->erro_campo = "y40_obs";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y40_codandam = $y40_codandam;
       $this->y40_id_usuario = $y40_id_usuario;
     if(($this->y40_codandam == null) || ($this->y40_codandam == "") ){
       $this->erro_sql = " Campo y40_codandam nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y40_id_usuario == null) || ($this->y40_id_usuario == "") ){
       $this->erro_sql = " Campo y40_id_usuario nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_fandamusu(
                                       y40_codandam
                                      ,y40_id_usuario
                                      ,y40_obs
                       )
                values (
                                $this->y40_codandam
                               ,$this->y40_id_usuario
                               ,'$this->y40_obs'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "fandamusu ($this->y40_codandam."-".$this->y40_id_usuario) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "fandamusu já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "fandamusu ($this->y40_codandam."-".$this->y40_id_usuario) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y40_codandam."-".$this->y40_id_usuario;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y40_codandam=null,$y40_id_usuario=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_fandamusu set ";
     $virgula = "";
     if(trim($this->y40_codandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y40_codandam"])){
       $sql  .= $virgula." y40_codandam = $this->y40_codandam ";
       $virgula = ",";
       if(trim($this->y40_codandam) == null ){
         $this->erro_sql = " Campo Codigo do Andamento Gerado nao Informado.";
         $this->erro_campo = "y40_codandam";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y40_id_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y40_id_usuario"])){
       $sql  .= $virgula." y40_id_usuario = $this->y40_id_usuario ";
       $virgula = ",";
       if(trim($this->y40_id_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário nao Informado.";
         $this->erro_campo = "y40_id_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y40_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y40_obs"])){
       $sql  .= $virgula." y40_obs = '$this->y40_obs' ";
       $virgula = ",";
       if(trim($this->y40_obs) == null ){
         $this->erro_sql = " Campo Observação do Usuário nao Informado.";
         $this->erro_campo = "y40_obs";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y40_codandam!=null){
       $sql .= " y40_codandam = $this->y40_codandam";
     }
     if($y40_id_usuario!=null){
       $sql .= " and  y40_id_usuario = $this->y40_id_usuario";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fandamusu nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y40_codandam."-".$this->y40_id_usuario;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "fandamusu nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y40_codandam."-".$this->y40_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y40_codandam."-".$this->y40_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y40_codandam=null,$y40_id_usuario=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_fandamusu
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y40_codandam != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y40_codandam = $y40_codandam ";
        }
        if($y40_id_usuario != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y40_id_usuario = $y40_id_usuario ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fandamusu nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y40_codandam."-".$y40_id_usuario;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "fandamusu nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y40_codandam."-".$y40_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y40_codandam."-".$y40_id_usuario;
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
        $this->erro_sql   = "Record Vazio na Tabela:fandamusu";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y40_codandam=null,$y40_id_usuario=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fandamusu ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_fandamusu.y40_id_usuario";
     $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_fandamusu.y40_codandam";
     $sql .= "      inner join db_usuarios  as a on   a.id_usuario = fis_fandam.y39_id_usuario";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fandam.y39_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($y40_codandam!=null ){
         $sql2 .= " where fis_fandamusu.y40_codandam = $y40_codandam ";
       }
       if($y40_id_usuario!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_fandamusu.y40_id_usuario = $y40_id_usuario ";
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
public function sql_query_file ( $y40_codandam=null,$y40_id_usuario=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fandamusu ";
     $sql2 = "";
     if($dbwhere==""){
       if($y40_codandam!=null ){
         $sql2 .= " where fis_fandamusu.y40_codandam = $y40_codandam ";
       }
       if($y40_id_usuario!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_fandamusu.y40_id_usuario = $y40_id_usuario ";
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
