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
//CLASSE DA ENTIDADE levusu
class cl_fis_levusu {
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
   public $y61_codlev = 0;
   public $y61_id_usuario = 0;
   public $y61_obs = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y61_codlev = int4 = Código do Levantamento
                 y61_id_usuario = int4 = Cod. Fiscal
                 y61_obs = text = Observação do Fiscal
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_levusu");
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
       $this->y61_codlev = ($this->y61_codlev == ""?@$GLOBALS["HTTP_POST_VARS"]["y61_codlev"]:$this->y61_codlev);
       $this->y61_id_usuario = ($this->y61_id_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y61_id_usuario"]:$this->y61_id_usuario);
       $this->y61_obs = ($this->y61_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y61_obs"]:$this->y61_obs);
     }else{
       $this->y61_codlev = ($this->y61_codlev == ""?@$GLOBALS["HTTP_POST_VARS"]["y61_codlev"]:$this->y61_codlev);
       $this->y61_id_usuario = ($this->y61_id_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y61_id_usuario"]:$this->y61_id_usuario);
     }
   }
   // funcao para inclusao
public function incluir ($y61_codlev,$y61_id_usuario){
      $this->atualizacampos();
       $this->y61_codlev = $y61_codlev;
       $this->y61_id_usuario = $y61_id_usuario;
     if(($this->y61_codlev == null) || ($this->y61_codlev == "") ){
       $this->erro_sql = " Campo y61_codlev nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y61_id_usuario == null) || ($this->y61_id_usuario == "") ){
       $this->erro_sql = " Campo y61_id_usuario nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_levusu(
                                       y61_codlev
                                      ,y61_id_usuario
                                      ,y61_obs
                       )
                values (
                                $this->y61_codlev
                               ,$this->y61_id_usuario
                               ,'$this->y61_obs'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "levusu ($this->y61_codlev."-".$this->y61_id_usuario) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "levusu já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "levusu ($this->y61_codlev."-".$this->y61_id_usuario) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y61_codlev."-".$this->y61_id_usuario;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y61_codlev=null,$y61_id_usuario=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_levusu set ";
     $virgula = "";
     if(trim($this->y61_codlev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y61_codlev"])){
       $sql  .= $virgula." y61_codlev = $this->y61_codlev ";
       $virgula = ",";
       if(trim($this->y61_codlev) == null ){
         $this->erro_sql = " Campo Código do Levantamento nao Informado.";
         $this->erro_campo = "y61_codlev";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y61_id_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y61_id_usuario"])){
       $sql  .= $virgula." y61_id_usuario = $this->y61_id_usuario ";
       $virgula = ",";
       if(trim($this->y61_id_usuario) == null ){
         $this->erro_sql = " Campo Cod. Fiscal nao Informado.";
         $this->erro_campo = "y61_id_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y61_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y61_obs"])){
       $sql  .= $virgula." y61_obs = '$this->y61_obs' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($y61_codlev!=null){
       $sql .= " y61_codlev = $this->y61_codlev";
     }
     if($y61_id_usuario!=null){
       $sql .= " and  y61_id_usuario = $this->y61_id_usuario";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "levusu nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y61_codlev."-".$this->y61_id_usuario;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "levusu nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y61_codlev."-".$this->y61_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y61_codlev."-".$this->y61_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y61_codlev=null,$y61_id_usuario=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_levusu
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y61_codlev != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y61_codlev = $y61_codlev ";
        }
        if($y61_id_usuario != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y61_id_usuario = $y61_id_usuario ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "levusu nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y61_codlev."-".$y61_id_usuario;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "levusu nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y61_codlev."-".$y61_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y61_codlev."-".$y61_id_usuario;
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
        $this->erro_sql   = "Record Vazio na Tabela:levusu";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y61_codlev=null,$y61_id_usuario=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_levusu ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_levusu.y61_id_usuario";
     $sql .= "      inner join fiscalizacao.fis_levanta  on  fis_levanta.y60_codlev = fis_levusu.y61_codlev";
     $sql2 = "";
     if($dbwhere==""){
       if($y61_codlev!=null ){
         $sql2 .= " where fis_levusu.y61_codlev = $y61_codlev ";
       }
       if($y61_id_usuario!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_levusu.y61_id_usuario = $y61_id_usuario ";
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
public function sql_query_file ( $y61_codlev=null,$y61_id_usuario=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_levusu ";
     $sql2 = "";
     if($dbwhere==""){
       if($y61_codlev!=null ){
         $sql2 .= " where fis_levusu.y61_codlev = $y61_codlev ";
       }
       if($y61_id_usuario!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_levusu.y61_id_usuario = $y61_id_usuario ";
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
