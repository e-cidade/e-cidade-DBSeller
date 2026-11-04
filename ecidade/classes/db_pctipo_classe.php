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

//MODULO: compras
//CLASSE DA ENTIDADE pctipo
class cl_pctipo {
   // cria variaveis de erro
   public $rotulo     = null;
   public $query_sql  = null;
   public $numrows    = 0;
   public $erro_status= null;
   public $erro_sql   = null;
   public $erro_banco = null;
   public $erro_msg   = null;
   public $erro_campo = null;
   public $pagina_retorno = null;
   // cria variaveis do arquivo
   public $pc05_codtipo = 0;
   public $pc05_descr = null;
   public $pc05_ativo = 'f';
   public $pc05_natureza = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 pc05_codtipo = int4 = Código do Tipo
                 pc05_descr = varchar(40) = Descrição do Tipo
                 pc05_ativo = bool = Ativo
                 pc05_natureza = int8 = Natureza
                 ";
   //funcao construtor da classe
   function cl_pctipo() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("pctipo");
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
       $this->pc05_codtipo = ($this->pc05_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["pc05_codtipo"]:$this->pc05_codtipo);
       $this->pc05_descr = ($this->pc05_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["pc05_descr"]:$this->pc05_descr);
       $this->pc05_ativo = ($this->pc05_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["pc05_ativo"]:$this->pc05_ativo);
       $this->pc05_natureza = ($this->pc05_natureza == ""?@$GLOBALS["HTTP_POST_VARS"]["pc05_natureza"]:$this->pc05_natureza);
     }else{
       $this->pc05_codtipo = ($this->pc05_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["pc05_codtipo"]:$this->pc05_codtipo);
     }
   }
   // funcao para inclusao
   public function incluir ($pc05_codtipo){
      $this->atualizacampos();
     if($this->pc05_descr == null ){
       $this->erro_sql = " Campo Descrição do Tipo nao Informado.";
       $this->erro_campo = "pc05_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pc05_ativo == null ){
       $this->erro_sql = " Campo Ativo nao Informado.";
       $this->erro_campo = "pc05_ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pc05_natureza == null ){
       $this->erro_sql = " Campo Natureza nao Informado.";
       $this->erro_campo = "pc05_natureza";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($pc05_codtipo == "" || $pc05_codtipo == null ){
       $result = db_query("select nextval('pctipo_pc05_codtipo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: pctipo_pc05_codtipo_seq do campo: pc05_codtipo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->pc05_codtipo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from pctipo_pc05_codtipo_seq");
       if(($result != false) && (pg_result($result,0,0) < $pc05_codtipo)){
         $this->erro_sql = " Campo pc05_codtipo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->pc05_codtipo = $pc05_codtipo;
       }
     }
     if(($this->pc05_codtipo == null) || ($this->pc05_codtipo == "") ){
       $this->erro_sql = " Campo pc05_codtipo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $result = db_query("insert into pctipo(
                                       pc05_codtipo
                                      ,pc05_descr
                                      ,pc05_ativo
                                      ,pc05_natureza
                       )
                values (
                                $this->pc05_codtipo
                               ,'$this->pc05_descr'
                               ,'$this->pc05_ativo'
                               ,$this->pc05_natureza
                      )");
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tipo ($this->pc05_codtipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tipo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tipo ($this->pc05_codtipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pc05_codtipo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $resaco = $this->sql_record($this->sql_query_file($this->pc05_codtipo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,5496,'$this->pc05_codtipo','I')");
       $resac = db_query("insert into db_acount values($acount,865,5496,'','".pg_result($resaco,0,'pc05_codtipo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,865,5497,'','".pg_escape_string(pg_result($resaco,0,'pc05_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,865,7813,'','".pg_result($resaco,0,'pc05_ativo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,865,1013449,'','".pg_result($resaco,0,'pc05_natureza')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($pc05_codtipo=null) {
      $this->atualizacampos();
     $sql = " update pctipo set ";
     $virgula = "";
     if(trim($this->pc05_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc05_codtipo"])){
        if(trim($this->pc05_codtipo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["pc05_codtipo"])){
           $this->pc05_codtipo = "0" ;
        }
       $sql  .= $virgula." pc05_codtipo = $this->pc05_codtipo ";
       $virgula = ",";
       if(trim($this->pc05_codtipo) == null ){
         $this->erro_sql = " Campo Código do Tipo nao Informado.";
         $this->erro_campo = "pc05_codtipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pc05_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc05_descr"])){
       $sql  .= $virgula." pc05_descr = '$this->pc05_descr' ";
       $virgula = ",";
       if(trim($this->pc05_descr) == null ){
         $this->erro_sql = " Campo Descrição do Tipo nao Informado.";
         $this->erro_campo = "pc05_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pc05_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc05_ativo"])){
       $sql  .= $virgula." pc05_ativo = '$this->pc05_ativo' ";
       $virgula = ",";
       if(trim($this->pc05_ativo) == null ){
         $this->erro_sql = " Campo Ativo nao Informado.";
         $this->erro_campo = "pc05_ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pc05_natureza)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc05_natureza"])){
        if(trim($this->pc05_natureza)=="" && isset($GLOBALS["HTTP_POST_VARS"]["pc05_natureza"])){
           $this->pc05_natureza = "1" ;
        }
       $sql  .= $virgula." pc05_natureza = $this->pc05_natureza ";
       $virgula = ",";
       if(trim($this->pc05_natureza) == null ){
         $this->erro_sql = " Campo Natureza nao Informado.";
         $this->erro_campo = "pc05_natureza";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where  pc05_codtipo = $this->pc05_codtipo
";
     $resaco = $this->sql_record($this->sql_query_file($this->pc05_codtipo));
     if($this->numrows>0){       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,5496,'$this->pc05_codtipo','A')");
       if(isset($GLOBALS["HTTP_POST_VARS"]["pc05_codtipo"]))
         $resac = db_query("insert into db_acount values($acount,865,5496,'".pg_result($resaco,0,'pc05_codtipo')."','$this->pc05_codtipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["pc05_descr"]))
         $resac = db_query("insert into db_acount values($acount,865,5497,'".pg_escape_string(pg_result($resaco,0,'pc05_descr'))."','$this->pc05_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["pc05_ativo"]))
         $resac = db_query("insert into db_acount values($acount,865,7813,'".pg_result($resaco,0,'pc05_ativo')."','$this->pc05_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["pc05_natureza"]))
         $resac = db_query("insert into db_acount values($acount,865,1013449,'".pg_result($resaco,0,'pc05_natureza')."','$this->pc05_natureza',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $result = @db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->pc05_codtipo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tipo nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->pc05_codtipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pc05_codtipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
   // funcao para exclusao
   function excluir ($pc05_codtipo=null) {
     $this->atualizacampos(true);
     $resaco = $this->sql_record($this->sql_query_file($this->pc05_codtipo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,5496,'$this->pc05_codtipo','E')");
       $resac = db_query("insert into db_acount values($acount,865,5496,'','".pg_result($resaco,0,'pc05_codtipo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,865,5497,'','".pg_escape_string(pg_result($resaco,0,'pc05_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,865,7813,'','".pg_result($resaco,0,'pc05_ativo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,865,1013449,'','".pg_result($resaco,0,'pc05_natureza')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $sql = " delete from pctipo
                    where ";
     $sql2 = "";
      if($this->pc05_codtipo != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " pc05_codtipo = $this->pc05_codtipo ";
}
     $result = @db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->pc05_codtipo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Tipo nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->pc05_codtipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pc05_codtipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
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
        $this->erro_sql   = "Dados do Grupo nao Encontrado";
        $this->erro_msg   = "Usuário: \n\n ".$this->erro_sql." \n\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $pc05_codtipo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from pctipo ";
     $sql2 = "";
     if($dbwhere==""){
       if($pc05_codtipo!=null ){
         $sql2 .= " where pctipo.pc05_codtipo = $pc05_codtipo ";
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
   public function sql_query_file ( $pc05_codtipo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from pctipo ";
     $sql2 = "";
     if($dbwhere==""){
       if($pc05_codtipo!=null ){
         $sql2 .= " where pctipo.pc05_codtipo = $pc05_codtipo ";
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
