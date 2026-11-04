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

//MODULO: fiscal
//CLASSE DA ENTIDADE lancusu
class cl_fis_lancusu {
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
   public $nl14_codlanc = 0;
   public $nl14_id_usuario = 0;
   public $nl14_obs = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 nl14_codlanc = int4 = Código do lancamento de Infração
                 nl14_id_usuario = int4 = Cod. Usuário
                 nl14_obs = text = Observação do Fiscal
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_lancusu");
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
       $this->nl14_codlanc = ($this->nl14_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl14_codlanc"]:$this->nl14_codlanc);
       $this->nl14_id_usuario = ($this->nl14_id_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["nl14_id_usuario"]:$this->nl14_id_usuario);
       $this->nl14_obs = ($this->nl14_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["nl14_obs"]:$this->nl14_obs);
     }else{
       $this->nl14_codlanc = ($this->nl14_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl14_codlanc"]:$this->nl14_codlanc);
       $this->nl14_id_usuario = ($this->nl14_id_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["nl14_id_usuario"]:$this->nl14_id_usuario);
     }
   }
   // funcao para inclusao
   public function incluir ($nl14_codlanc,$nl14_id_usuario){
      $this->atualizacampos();
       $this->nl14_codlanc = $nl14_codlanc;
       $this->nl14_id_usuario = $nl14_id_usuario;
     if(($this->nl14_codlanc == null) || ($this->nl14_codlanc == "") ){
       $this->erro_sql = " Campo nl14_codlanc nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->nl14_id_usuario == null) || ($this->nl14_id_usuario == "") ){
       $this->erro_sql = " Campo nl14_id_usuario nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_lancusu(
                                       nl14_codlanc
                                      ,nl14_id_usuario
                                      ,nl14_obs
                       )
                values (
                                $this->nl14_codlanc
                               ,$this->nl14_id_usuario
                               ,'$this->nl14_obs'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "lancusu ($this->nl14_codlanc."-".$this->nl14_id_usuario) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "lancusu já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "lancusu ($this->nl14_codlanc."-".$this->nl14_id_usuario) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl14_codlanc."-".$this->nl14_id_usuario;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($nl14_codlanc=null,$nl14_id_usuario=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_lancusu set ";
     $virgula = "";
     if(trim($this->nl14_codlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl14_codlanc"])){
       $sql  .= $virgula." nl14_codlanc = $this->nl14_codlanc ";
       $virgula = ",";
       if(trim($this->nl14_codlanc) == null ){
         $this->erro_sql = " Campo Código do lancamento de Infração nao Informado.";
         $this->erro_campo = "nl14_codlanc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl14_id_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl14_id_usuario"])){
       $sql  .= $virgula." nl14_id_usuario = $this->nl14_id_usuario ";
       $virgula = ",";
       if(trim($this->nl14_id_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário nao Informado.";
         $this->erro_campo = "nl14_id_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl14_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl14_obs"])){
       $sql  .= $virgula." nl14_obs = '$this->nl14_obs' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($nl14_codlanc!=null){
       $sql .= " nl14_codlanc = $this->nl14_codlanc";
     }
     if($nl14_id_usuario!=null){
       $sql .= " and  nl14_id_usuario = $this->nl14_id_usuario";
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "lancusu nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl14_codlanc."-".$this->nl14_id_usuario;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "lancusu nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl14_codlanc."-".$this->nl14_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl14_codlanc."-".$this->nl14_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($nl14_codlanc=null,$nl14_id_usuario=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_lancusu
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($nl14_codlanc != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl14_codlanc = $nl14_codlanc ";
        }
        if($nl14_id_usuario != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl14_id_usuario = $nl14_id_usuario ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "lancusu nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$nl14_codlanc."-".$nl14_id_usuario;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "lancusu nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$nl14_codlanc."-".$nl14_id_usuario;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$nl14_codlanc."-".$nl14_id_usuario;
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
        $this->erro_sql   = "Record Vazio na Tabela:lancusu";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $nl14_codlanc=null,$nl14_id_usuario=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lancusu ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_lancusu.nl14_id_usuario";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lancusu.nl14_codlanc";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_lancamento.nl01_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($nl14_codlanc!=null ){
         $sql2 .= " where fis_lancusu.nl14_codlanc = $nl14_codlanc ";
       }
       if($nl14_id_usuario!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_lancusu.nl14_id_usuario = $nl14_id_usuario ";
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
   public function sql_query_file ( $nl14_codlanc=null,$nl14_id_usuario=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lancusu ";
     $sql2 = "";
     if($dbwhere==""){
       if($nl14_codlanc!=null ){
         $sql2 .= " where fis_lancusu.nl14_codlanc = $nl14_codlanc ";
       }
       if($nl14_id_usuario!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_lancusu.nl14_id_usuario = $nl14_id_usuario ";
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
