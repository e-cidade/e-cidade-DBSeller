<?php
//MODULO: juridico
//CLASSE DA ENTIDADE advog
class cl_advog {
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
   var $v57_numcgm = 0;
   var $v57_oab = null;
   var $v57_matriculaadvogado = null;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 v57_numcgm = int4 = Código
                 v57_oab = varchar(20) = OAB
                 v57_matriculaadvogado = varchar(20) = Matrícula do Advogado
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("advog");
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
       $this->v57_numcgm = ($this->v57_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["v57_numcgm"]:$this->v57_numcgm);
       $this->v57_oab = ($this->v57_oab == ""?@$GLOBALS["HTTP_POST_VARS"]["v57_oab"]:$this->v57_oab);
       $this->v57_matriculaadvogado = ($this->v57_matriculaadvogado == ""?@$GLOBALS["HTTP_POST_VARS"]["v57_matriculaadvogado"]:$this->v57_matriculaadvogado);
     }else{
       $this->v57_numcgm = ($this->v57_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["v57_numcgm"]:$this->v57_numcgm);
     }
   }
   // funcao para inclusao
   public function incluir ($v57_numcgm){
      $this->atualizacampos();
     if($this->v57_oab == null ){
       $this->erro_sql = " Campo OAB não informado.";
       $this->erro_campo = "v57_oab";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->v57_numcgm = $v57_numcgm;
     if(($this->v57_numcgm == null) || ($this->v57_numcgm == "") ){
       $this->erro_sql = " Campo v57_numcgm nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into advog(
                                       v57_numcgm
                                      ,v57_oab
                                      ,v57_matriculaadvogado
                       )
                values (
                                $this->v57_numcgm
                               ,'$this->v57_oab'
                               ,'$this->v57_matriculaadvogado'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Advogados ($this->v57_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Advogados já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Advogados ($this->v57_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->v57_numcgm;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->v57_numcgm  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,2560,'$this->v57_numcgm','I')");
         $resac = db_query("insert into db_acount values($acount,94,2560,'','".AddSlashes(pg_result($resaco,0,'v57_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,94,499,'','".AddSlashes(pg_result($resaco,0,'v57_oab'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,94,1010006,'','".AddSlashes(pg_result($resaco,0,'v57_matriculaadvogado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($v57_numcgm=null) {
      $this->atualizacampos();
     $sql = " update advog set ";
     $virgula = "";
     if(trim($this->v57_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v57_numcgm"])){
       $sql  .= $virgula." v57_numcgm = $this->v57_numcgm ";
       $virgula = ",";
       if(trim($this->v57_numcgm) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "v57_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v57_oab)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v57_oab"])){
       $sql  .= $virgula." v57_oab = '$this->v57_oab' ";
       $virgula = ",";
       if(trim($this->v57_oab) == null ){
         $this->erro_sql = " Campo OAB não informado.";
         $this->erro_campo = "v57_oab";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v57_matriculaadvogado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v57_matriculaadvogado"])){
       $sql  .= $virgula." v57_matriculaadvogado = '$this->v57_matriculaadvogado' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($v57_numcgm!=null){
       $sql .= " v57_numcgm = $this->v57_numcgm";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->v57_numcgm));
       if($this->numrows>0){

         for($conresaco=0;$conresaco<$this->numrows;$conresaco++){

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,2560,'$this->v57_numcgm','A')");
           if(isset($GLOBALS["HTTP_POST_VARS"]["v57_numcgm"]) || $this->v57_numcgm != "")
             $resac = db_query("insert into db_acount values($acount,94,2560,'".AddSlashes(pg_result($resaco,$conresaco,'v57_numcgm'))."','$this->v57_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["v57_oab"]) || $this->v57_oab != "")
             $resac = db_query("insert into db_acount values($acount,94,499,'".AddSlashes(pg_result($resaco,$conresaco,'v57_oab'))."','$this->v57_oab',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["v57_matriculaadvogado"]) || $this->v57_matriculaadvogado != "")
             $resac = db_query("insert into db_acount values($acount,94,1010006,'".AddSlashes(pg_result($resaco,$conresaco,'v57_matriculaadvogado'))."','$this->v57_matriculaadvogado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Advogados nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->v57_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Advogados nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->v57_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->v57_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($v57_numcgm=null,$dbwhere=null) {

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if ($dbwhere==null || $dbwhere=="") {

         $resaco = $this->sql_record($this->sql_query_file($v57_numcgm));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,2560,'$v57_numcgm','E')");
           $resac  = db_query("insert into db_acount values($acount,94,2560,'','".AddSlashes(pg_result($resaco,$iresaco,'v57_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,94,499,'','".AddSlashes(pg_result($resaco,$iresaco,'v57_oab'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,94,1010006,'','".AddSlashes(pg_result($resaco,$iresaco,'v57_matriculaadvogado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from advog
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($v57_numcgm != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " v57_numcgm = $v57_numcgm ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Advogados nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$v57_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Advogados nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$v57_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$v57_numcgm;
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
        $this->erro_sql   = "Record Vazio na Tabela:advog";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $v57_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from advog ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = advog.v57_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($v57_numcgm!=null ){
         $sql2 .= " where advog.v57_numcgm = $v57_numcgm ";
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
   public function sql_query_file ( $v57_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from advog ";
     $sql2 = "";
     if($dbwhere==""){
       if($v57_numcgm!=null ){
         $sql2 .= " where advog.v57_numcgm = $v57_numcgm ";
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
    public function sql_query_advog_inicial ( $v57_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
        $sql .= " from advog ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = advog.v57_numcgm";
        $sql .= "      inner join inicial  on  inicial.v50_advog = advog.v57_numcgm";
        $sql2 = "";
        if($dbwhere==""){
            if($v57_numcgm!=null ){
                $sql2 .= " where advog.v57_numcgm = $v57_numcgm ";
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
