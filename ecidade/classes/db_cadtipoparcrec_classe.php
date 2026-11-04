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

//MODULO: caixa
//CLASSE DA ENTIDADE cadtipoparcrec
class cl_cadtipoparcrec {
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
   var $k180_cadtipoparc = 0;
   var $k180_estorc = 0;

   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 k180_cadtipoparc = int4 = Código
                 k180_estorc = varchar(15) = Estrutural da Receita
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("cadtipoparcrec");
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
       $this->k180_cadtipoparc = ($this->k180_cadtipoparc == ""?@$GLOBALS["HTTP_POST_VARS"]["k180_cadtipoparc"]:$this->k180_cadtipoparc);
       $this->k180_estorc = ($this->k180_estorc == ""?@$GLOBALS["HTTP_POST_VARS"]["k180_estorc"]:$this->k180_estorc);
     }else{
       $this->k180_cadtipoparc = ($this->k180_cadtipoparc == ""?@$GLOBALS["HTTP_POST_VARS"]["k180_cadtipoparc"]:$this->k180_cadtipoparc);
       $this->k180_estorc = ($this->k180_estorc == ""?@$GLOBALS["HTTP_POST_VARS"]["k180_estorc"]:$this->k180_estorc);
     }
   }
   // funcao para inclusao
   public function incluir ($k180_cadtipoparc,$k180_estorc){
      $this->atualizacampos();
      $this->k180_cadtipoparc = $k180_cadtipoparc;
      $this->k180_estorc = $k180_estorc;
     if(($this->k180_cadtipoparc == null) || ($this->k180_cadtipoparc == "") ){
       $this->erro_sql = " Campo k180_cadtipoparc nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->k180_estorc == null) || ($this->k180_estorc == "") ){
       $this->erro_sql = " Campo k180_estorc nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into cadtipoparcrec(
                                       k180_cadtipoparc
                                      ,k180_estorc
                       )
                values (
                                $this->k180_cadtipoparc
                               ,'$this->k180_estorc'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Receita que a regra de parcelamento usa ($this->k180_cadtipoparc."-".$this->k180_estorc) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Receita que a regra de parcelamento usa já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Receita que a regra de parcelamento usa ($this->k180_cadtipoparc."-".$this->k180_estorc) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->k180_cadtipoparc."-".$this->k180_estorc;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->k180_cadtipoparc,$this->k180_estorc));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,1011173,'$this->k180_cadtipoparc','I')");
       $resac = db_query("insert into db_acountkey values($acount,1011174,'$this->k180_estorc','I')");
       $resac = db_query("insert into db_acount values($acount,1010546,1011173,'','".AddSlashes(pg_result($resaco,0,'k180_cadtipoparc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010546,1011174,'','".AddSlashes(pg_result($resaco,0,'k180_estorc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($k180_cadtipoparc=null,$k180_estorc=null) {
      $this->atualizacampos();
     $sql = " update cadtipoparcrec set ";
     $virgula = "";
     if(trim($this->k180_cadtipoparc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k180_cadtipoparc"])){
       $sql  .= $virgula." k180_cadtipoparc = $this->k180_cadtipoparc ";
       $virgula = ",";
       if(trim($this->k180_cadtipoparc) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "k180_cadtipoparc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k180_estorc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k180_estorc"])){
       $sql  .= $virgula." k180_estorc = '$this->k180_estorc' ";
       $virgula = ",";
       if(trim($this->k180_estorc) == null ){
         $this->erro_sql = " Campo tipo de debito nao Informado.";
         $this->erro_campo = "k180_estorc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     $sql .= " where ";
     if($k180_cadtipoparc!=null){
       $sql .= " k180_cadtipoparc = $this->k180_cadtipoparc";
     }
     if($k180_estorc!=null){
       $sql .= " and  k180_estorc = $this->k180_estorc";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->k180_cadtipoparc,$this->k180_estorc));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1011173,'$this->k180_cadtipoparc','A')");
         $resac = db_query("insert into db_acountkey values($acount,1011174,'$this->k180_estorc','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["k180_cadtipoparc"]))
           $resac = db_query("insert into db_acount values($acount,1010546,1011173,'".AddSlashes(pg_result($resaco,$conresaco,'k180_cadtipoparc'))."','$this->k180_cadtipoparc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["k180_estorc"]))
           $resac = db_query("insert into db_acount values($acount,1010546,1011174,'".AddSlashes(pg_result($resaco,$conresaco,'k180_estorc'))."','$this->k180_estorc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");

       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Receita que a regra de parcelamento usa nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->k180_cadtipoparc."-".$this->k180_estorc;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Receita que a regra de parcelamento usa nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->k180_cadtipoparc."-".$this->k180_estorc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->k180_cadtipoparc."-".$this->k180_estorc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($k180_cadtipoparc=null,$k180_estorc=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($k180_cadtipoparc,$k180_estorc));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1011173,'$k180_cadtipoparc','E')");
         $resac = db_query("insert into db_acountkey values($acount,1011174,'$k180_estorc','E')");
         $resac = db_query("insert into db_acount values($acount,1010546,1011173,'','".AddSlashes(pg_result($resaco,$iresaco,'k180_cadtipoparc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010546,1011174,'','".AddSlashes(pg_result($resaco,$iresaco,'k180_estorc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from cadtipoparcrec
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($k180_cadtipoparc != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " k180_cadtipoparc = $k180_cadtipoparc ";
        }
        if($k180_estorc != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " k180_estorc = '$k180_estorc' ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Receita que a regra de parcelamento usa nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$k180_cadtipoparc."-".$k180_estorc;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Receita que a regra de parcelamento usa nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$k180_cadtipoparc."-".$k180_estorc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$k180_cadtipoparc."-".$k180_estorc;
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
        $this->erro_sql   = "Record Vazio na Tabela:cadtipoparcrec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $k180_cadtipoparc=null,$k180_estorc=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from cadtipoparcrec ";
     $sql .= "      inner join conplanoorcamento on c60_estrut = cadtipoparcrec.k180_estorc";
     $sql .= "      inner join cadtipoparc  on  cadtipoparc.k40_codigo = cadtipoparcrec.k180_cadtipoparc";
     $sql2 = "";
     if($dbwhere==""){
       if($k180_cadtipoparc!=null ){
         $sql2 .= " where c60_anousu = ".db_getsession('DB_anousu');
         $sql2 .= " and cadtipoparcrec.k180_cadtipoparc = $k180_cadtipoparc ";
       }
       if($k180_estorc!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " cadtipoparcrec.k180_estorc = '$k180_estorc' ";
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
  public function sql_query_file ( $k180_cadtipoparc=null,$k180_estorc=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from cadtipoparcrec ";
     $sql2 = "";
     if($dbwhere==""){
       if($k180_cadtipoparc!=null ){
         $sql2 .= " where cadtipoparcrec.k180_cadtipoparc = $k180_cadtipoparc ";
       }
       if($k180_estorc!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " cadtipoparcrec.k180_estorc = '$k180_estorc' ";
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
