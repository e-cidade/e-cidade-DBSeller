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

//MODULO: Samu
//CLASSE DA ENTIDADE sam_kit_material_item
class cl_sam_kit_material_item {
   // cria variaveis de erro
   var $rotulo     = null;
   var $query_sql  = null;
   var $numrows    = 0;
   var $numrows_incluir = 0;
   var $numrows_alterar = 0;
   var $numrows_excluir = 0;
   var $erro_status= null;
   var $erro_sql   = null;
   var $erro_banco = null;
   var $erro_msg   = null;
   var $erro_campo = null;
   var $pagina_retorno = null;
   // cria variaveis do arquivo
   var $sm04_sequencial = 0;
   var $sm04_material = 0;
   var $sm04_kit_material = 0;
   var $sm04_quantidade = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 sm04_sequencial = int4 = Código
                 sm04_material = int4 = Material
                 sm04_kit_material = int4 = kit_material
                 sm04_quantidade = numeric(10) = Quantidade
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("sam_kit_material_item");
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
       $this->sm04_sequencial = ($this->sm04_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sm04_sequencial"]:$this->sm04_sequencial);
       $this->sm04_material = ($this->sm04_material == ""?@$GLOBALS["HTTP_POST_VARS"]["sm04_material"]:$this->sm04_material);
       $this->sm04_kit_material = ($this->sm04_kit_material == ""?@$GLOBALS["HTTP_POST_VARS"]["sm04_kit_material"]:$this->sm04_kit_material);
       $this->sm04_quantidade = ($this->sm04_quantidade == ""?@$GLOBALS["HTTP_POST_VARS"]["sm04_quantidade"]:$this->sm04_quantidade);
     }else{
       $this->sm04_sequencial = ($this->sm04_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sm04_sequencial"]:$this->sm04_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($sm04_sequencial){
      $this->atualizacampos();
     if($this->sm04_material == null ){
       $this->erro_sql = " Campo Material nao Informado.";
       $this->erro_campo = "sm04_material";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->sm04_kit_material == null ){
       $this->erro_sql = " Campo kit_material nao Informado.";
       $this->erro_campo = "sm04_kit_material";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->sm04_quantidade == null ){
       $this->erro_sql = " Campo Quantidade nao Informado.";
       $this->erro_campo = "sm04_quantidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($sm04_sequencial == "" || $sm04_sequencial == null ){
       $result = db_query("select nextval('sam_kit_material_item_sm04_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: sam_kit_material_item_sm04_sequencial_seq do campo: sm04_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->sm04_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from sam_kit_material_item_sm04_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $sm04_sequencial)){
         $this->erro_sql = " Campo sm04_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->sm04_sequencial = $sm04_sequencial;
       }
     }
     if(($this->sm04_sequencial == null) || ($this->sm04_sequencial == "") ){
       $this->erro_sql = " Campo sm04_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into sam_kit_material_item(
                                       sm04_sequencial
                                      ,sm04_material
                                      ,sm04_kit_material
                                      ,sm04_quantidade
                       )
                values (
                                $this->sm04_sequencial
                               ,$this->sm04_material
                               ,$this->sm04_kit_material
                               ,$this->sm04_quantidade
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Kit Material Item ($this->sm04_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Kit Material Item já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Kit Material Item ($this->sm04_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->sm04_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->sm04_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,18588,'$this->sm04_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,3290,18588,'','".AddSlashes(pg_result($resaco,0,'sm04_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3290,18589,'','".AddSlashes(pg_result($resaco,0,'sm04_material'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3290,18591,'','".AddSlashes(pg_result($resaco,0,'sm04_kit_material'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3290,18593,'','".AddSlashes(pg_result($resaco,0,'sm04_quantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($sm04_sequencial=null) {
      $this->atualizacampos();
     $sql = " update sam_kit_material_item set ";
     $virgula = "";
     if(trim($this->sm04_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sm04_sequencial"])){
       $sql  .= $virgula." sm04_sequencial = $this->sm04_sequencial ";
       $virgula = ",";
       if(trim($this->sm04_sequencial) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "sm04_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->sm04_material)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sm04_material"])){
       $sql  .= $virgula." sm04_material = $this->sm04_material ";
       $virgula = ",";
       if(trim($this->sm04_material) == null ){
         $this->erro_sql = " Campo Material nao Informado.";
         $this->erro_campo = "sm04_material";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->sm04_kit_material)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sm04_kit_material"])){
       $sql  .= $virgula." sm04_kit_material = $this->sm04_kit_material ";
       $virgula = ",";
       if(trim($this->sm04_kit_material) == null ){
         $this->erro_sql = " Campo kit_material nao Informado.";
         $this->erro_campo = "sm04_kit_material";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->sm04_quantidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sm04_quantidade"])){
       $sql  .= $virgula." sm04_quantidade = $this->sm04_quantidade ";
       $virgula = ",";
       if(trim($this->sm04_quantidade) == null ){
         $this->erro_sql = " Campo Quantidade nao Informado.";
         $this->erro_campo = "sm04_quantidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($sm04_sequencial!=null){
       $sql .= " sm04_sequencial = $this->sm04_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->sm04_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18588,'$this->sm04_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["sm04_sequencial"]) || $this->sm04_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,3290,18588,'".AddSlashes(pg_result($resaco,$conresaco,'sm04_sequencial'))."','$this->sm04_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["sm04_material"]) || $this->sm04_material != "")
           $resac = db_query("insert into db_acount values($acount,3290,18589,'".AddSlashes(pg_result($resaco,$conresaco,'sm04_material'))."','$this->sm04_material',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["sm04_kit_material"]) || $this->sm04_kit_material != "")
           $resac = db_query("insert into db_acount values($acount,3290,18591,'".AddSlashes(pg_result($resaco,$conresaco,'sm04_kit_material'))."','$this->sm04_kit_material',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["sm04_quantidade"]) || $this->sm04_quantidade != "")
           $resac = db_query("insert into db_acount values($acount,3290,18593,'".AddSlashes(pg_result($resaco,$conresaco,'sm04_quantidade'))."','$this->sm04_quantidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Kit Material Item nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->sm04_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Kit Material Item nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->sm04_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->sm04_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($sm04_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($sm04_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18588,'$sm04_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,3290,18588,'','".AddSlashes(pg_result($resaco,$iresaco,'sm04_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3290,18589,'','".AddSlashes(pg_result($resaco,$iresaco,'sm04_material'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3290,18591,'','".AddSlashes(pg_result($resaco,$iresaco,'sm04_kit_material'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3290,18593,'','".AddSlashes(pg_result($resaco,$iresaco,'sm04_quantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from sam_kit_material_item
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($sm04_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " sm04_sequencial = $sm04_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Kit Material Item nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$sm04_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Kit Material Item nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$sm04_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$sm04_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:sam_kit_material_item";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $sm04_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from sam_kit_material_item ";
     $sql .= "      inner join matmater  on  matmater.m60_codmater = sam_kit_material_item.sm04_material";
     $sql .= "      inner join sam_kit_material  on  sam_kit_material.sm03_sequencial = sam_kit_material_item.sm04_kit_material";
     $sql .= "      inner join matunid  on  matunid.m61_codmatunid = matmater.m60_codmatunid";
     $sql2 = "";
     if($dbwhere==""){
       if($sm04_sequencial!=null ){
         $sql2 .= " where sam_kit_material_item.sm04_sequencial = $sm04_sequencial ";
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
   // funcao do sql
   public function sql_query_file ( $sm04_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from sam_kit_material_item ";
     $sql2 = "";
     if($dbwhere==""){
       if($sm04_sequencial!=null ){
         $sql2 .= " where sam_kit_material_item.sm04_sequencial = $sm04_sequencial ";
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
