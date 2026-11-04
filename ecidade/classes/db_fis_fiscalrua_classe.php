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
//CLASSE DA ENTIDADE fiscalrua
class cl_fis_fiscalrua {
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
   public $y33_codnoti = 0;
   public $y33_codigo = 0;
   public $y33_numero = 0;
   public $y33_compl = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y33_codnoti = int8 = Código da Notificação
                 y33_codigo = int4 = cód. Rua/Avenida
                 y33_numero = int4 = Número
                 y33_compl = varchar(20) = Complemento
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_fiscalrua");
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
       $this->y33_codnoti = ($this->y33_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y33_codnoti"]:$this->y33_codnoti);
       $this->y33_codigo = ($this->y33_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y33_codigo"]:$this->y33_codigo);
       $this->y33_numero = ($this->y33_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["y33_numero"]:$this->y33_numero);
       $this->y33_compl = ($this->y33_compl == ""?@$GLOBALS["HTTP_POST_VARS"]["y33_compl"]:$this->y33_compl);
     }else{
       $this->y33_codnoti = ($this->y33_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y33_codnoti"]:$this->y33_codnoti);
     }
   }
   // funcao para inclusao
   public function incluir ($y33_codnoti){
      $this->atualizacampos();
     if($this->y33_codigo == null ){
       $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
       $this->erro_campo = "y33_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y33_numero == null ){
       $this->erro_sql = " Campo Número nao Informado.";
       $this->erro_campo = "y33_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y33_codnoti = $y33_codnoti;
     if(($this->y33_codnoti == null) || ($this->y33_codnoti == "") ){
       $this->erro_sql = " Campo y33_codnoti nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_fiscalrua(
                                       y33_codnoti
                                      ,y33_codigo
                                      ,y33_numero
                                      ,y33_compl
                       )
                values (
                                $this->y33_codnoti
                               ,$this->y33_codigo
                               ,$this->y33_numero
                               ,'$this->y33_compl'
                      )";
     $result = @db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "fiscalrua ($this->y33_codnoti) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "fiscalrua já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "fiscalrua ($this->y33_codnoti) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao Efetivada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y33_codnoti;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";


     return true;
   }
   // funcao para alteracao
   public function alterar ($y33_codnoti=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_fiscalrua set ";
     $virgula = "";
     if(trim($this->y33_codnoti)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y33_codnoti"])){
       $sql  .= $virgula." y33_codnoti = $this->y33_codnoti ";
       $virgula = ",";
       if(trim($this->y33_codnoti) == null ){
         $this->erro_sql = " Campo Código da Notificação nao Informado.";
         $this->erro_campo = "y33_codnoti";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y33_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y33_codigo"])){
       $sql  .= $virgula." y33_codigo = $this->y33_codigo ";
       $virgula = ",";
       if(trim($this->y33_codigo) == null ){
         $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
         $this->erro_campo = "y33_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y33_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y33_numero"])){
       $sql  .= $virgula." y33_numero = $this->y33_numero ";
       $virgula = ",";
       if(trim($this->y33_numero) == null ){
         $this->erro_sql = " Campo Número nao Informado.";
         $this->erro_campo = "y33_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y33_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y33_compl"])){
       $sql  .= $virgula." y33_compl = '$this->y33_compl' ";
       $virgula = ",";
     }
     $sql .= " where  y33_codnoti = $this->y33_codnoti
";


     $result = @db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fiscalrua nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y33_codnoti;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "fiscalrua nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y33_codnoti;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração Efetivada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y33_codnoti;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($y33_codnoti=null) {
     $sql = " delete from fiscalizacao.fis_fiscalrua
                    where ";
     $sql2 = "";
      if($y33_codnoti != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " y33_codnoti = $y33_codnoti ";
}
     $result = @db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fiscalrua nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y33_codnoti;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "fiscalrua nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y33_codnoti;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão Efetivada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y33_codnoti;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
   // funcao do recordset
   public function sql_record($sql) {
     $result = @db_query($sql);
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
   // funcao do sql
   public function sql_query ( $y33_codnoti=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fiscalrua ";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = fiscalrua.y33_codigo";
     $sql .= "      inner join fiscalizacao.fis_fiscal  on  fis_fiscal.y30_codnoti = fiscalrua.y33_codnoti";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscal.y30_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($y33_codnoti!=null ){
         $sql2 .= " where fiscalrua.y33_codnoti = $y33_codnoti ";
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
   public function sql_query_file ( $y33_codnoti=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fiscalrua ";
     $sql2 = "";
     if($dbwhere==""){
       if($y33_codnoti!=null ){
         $sql2 .= " where fiscalrua.y33_codnoti = $y33_codnoti ";
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
