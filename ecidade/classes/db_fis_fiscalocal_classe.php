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
//CLASSE DA ENTIDADE fiscalocal
class cl_fis_fiscalocal {
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
   public $y12_codnoti = 0;
   public $y12_codigo = 0;
   public $y12_codi = 0;
   public $y12_numero = 0;
   public $y12_compl = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y12_codnoti = int8 = Código da Notificação
                 y12_codigo = int4 = Rua/Avenida
                 y12_codi = int4 = Bairro
                 y12_numero = int4 = Número
                 y12_compl = varchar(10) = Complemento
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_fiscalocal");
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
       $this->y12_codnoti = ($this->y12_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y12_codnoti"]:$this->y12_codnoti);
       $this->y12_codigo = ($this->y12_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y12_codigo"]:$this->y12_codigo);
       $this->y12_codi = ($this->y12_codi == ""?@$GLOBALS["HTTP_POST_VARS"]["y12_codi"]:$this->y12_codi);
       $this->y12_numero = ($this->y12_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["y12_numero"]:$this->y12_numero);
       $this->y12_compl = ($this->y12_compl == ""?@$GLOBALS["HTTP_POST_VARS"]["y12_compl"]:$this->y12_compl);
     }else{
       $this->y12_codnoti = ($this->y12_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y12_codnoti"]:$this->y12_codnoti);
     }
   }
   // funcao para inclusao
public function incluir ($y12_codnoti){
      $this->atualizacampos();
     if($this->y12_codigo == null ){
       $this->erro_sql = " Campo Rua/Avenida nao Informado.";
       $this->erro_campo = "y12_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y12_codi == null ){
       $this->erro_sql = " Campo Bairro nao Informado.";
       $this->erro_campo = "y12_codi";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y12_numero == null ){
       $this->erro_sql = " Campo Número nao Informado.";
       $this->erro_campo = "y12_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y12_codnoti = $y12_codnoti;
     if(($this->y12_codnoti == null) || ($this->y12_codnoti == "") ){
       $this->erro_sql = " Campo y12_codnoti nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_fiscalocal(
                                       y12_codnoti
                                      ,y12_codigo
                                      ,y12_codi
                                      ,y12_numero
                                      ,y12_compl
                       )
                values (
                                $this->y12_codnoti
                               ,$this->y12_codigo
                               ,$this->y12_codi
                               ,$this->y12_numero
                               ,'$this->y12_compl'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "local da notificação ($this->y12_codnoti) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "local da notificação já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "local da notificação ($this->y12_codnoti) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y12_codnoti;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y12_codnoti=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_fiscalocal set ";
     $virgula = "";
     if(trim($this->y12_codnoti)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y12_codnoti"])){
       $sql  .= $virgula." y12_codnoti = $this->y12_codnoti ";
       $virgula = ",";
       if(trim($this->y12_codnoti) == null ){
         $this->erro_sql = " Campo Código da Notificação nao Informado.";
         $this->erro_campo = "y12_codnoti";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y12_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y12_codigo"])){
       $sql  .= $virgula." y12_codigo = $this->y12_codigo ";
       $virgula = ",";
       if(trim($this->y12_codigo) == null ){
         $this->erro_sql = " Campo Rua/Avenida nao Informado.";
         $this->erro_campo = "y12_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y12_codi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y12_codi"])){
       $sql  .= $virgula." y12_codi = $this->y12_codi ";
       $virgula = ",";
       if(trim($this->y12_codi) == null ){
         $this->erro_sql = " Campo Bairro nao Informado.";
         $this->erro_campo = "y12_codi";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y12_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y12_numero"])){
       $sql  .= $virgula." y12_numero = $this->y12_numero ";
       $virgula = ",";
       if(trim($this->y12_numero) == null ){
         $this->erro_sql = " Campo Número nao Informado.";
         $this->erro_campo = "y12_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y12_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y12_compl"])){
       $sql  .= $virgula." y12_compl = '$this->y12_compl' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($y12_codnoti!=null){
       $sql .= " y12_codnoti = $this->y12_codnoti";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local da notificação nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y12_codnoti;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local da notificação nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y12_codnoti;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y12_codnoti;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y12_codnoti=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_fiscalocal
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y12_codnoti != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y12_codnoti = $y12_codnoti ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local da notificação nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y12_codnoti;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local da notificação nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y12_codnoti;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y12_codnoti;
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
        $this->erro_sql   = "Record Vazio na Tabela:fiscalocal";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y12_codnoti=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fiscalocal ";
     $sql .= "      inner join bairro  on  bairro.j13_codi = fis_fiscalocal.y12_codi";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_fiscalocal.y12_codigo";
     $sql .= "      inner join fiscalizacao.fis_fiscal  on  fis_fiscal.y30_codnoti = fis_fiscalocal.y12_codnoti";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscal.y30_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($y12_codnoti!=null ){
         $sql2 .= " where fis_fiscalocal.y12_codnoti = $y12_codnoti ";
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
public function sql_query_file ( $y12_codnoti=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fiscalocal ";
     $sql2 = "";
     if($dbwhere==""){
       if($y12_codnoti!=null ){
         $sql2 .= " where fis_fiscalocal.y12_codnoti = $y12_codnoti ";
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
