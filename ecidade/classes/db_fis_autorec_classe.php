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
//CLASSE DA ENTIDADE autorec
class cl_fis_autorec {
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
   public $y57_codauto = 0;
   public $y57_receit = 0;
   public $y57_descr = null;
   public $y57_valor = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y57_codauto = int4 = Código do Auto de Infração
                 y57_receit = int4 = codigo da receita
                 y57_descr = varchar(50) = Descrição do lançamento da receita
                 y57_valor = float8 = Valor da Receita
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_autorec");
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
       $this->y57_codauto = ($this->y57_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y57_codauto"]:$this->y57_codauto);
       $this->y57_receit = ($this->y57_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y57_receit"]:$this->y57_receit);
       $this->y57_descr = ($this->y57_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["y57_descr"]:$this->y57_descr);
       $this->y57_valor = ($this->y57_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y57_valor"]:$this->y57_valor);
     }else{
       $this->y57_codauto = ($this->y57_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y57_codauto"]:$this->y57_codauto);
       $this->y57_receit = ($this->y57_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y57_receit"]:$this->y57_receit);
     }
   }
   // funcao para inclusao
public function incluir ($y57_codauto,$y57_receit){
      $this->atualizacampos();
     if($this->y57_descr == null ){
       $this->erro_sql = " Campo Descrição do lançamento da receita nao Informado.";
       $this->erro_campo = "y57_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y57_valor == null ){
      $this->y57_valor = 0;
     }
       $this->y57_codauto = $y57_codauto;
       $this->y57_receit = $y57_receit;
     if(($this->y57_codauto == null) || ($this->y57_codauto == "") ){
       $this->erro_sql = " Campo y57_codauto nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y57_receit == null) || ($this->y57_receit == "") ){
       $this->erro_sql = " Campo y57_receit nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_autorec(
                                       y57_codauto
                                      ,y57_receit
                                      ,y57_descr
                                      ,y57_valor
                       )
                values (
                                $this->y57_codauto
                               ,$this->y57_receit
                               ,'$this->y57_descr'
                               ,$this->y57_valor
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "autorec ($this->y57_codauto."-".$this->y57_receit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "autorec já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "autorec ($this->y57_codauto."-".$this->y57_receit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y57_codauto."-".$this->y57_receit;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y57_codauto=null,$y57_receit=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_autorec set ";
     $virgula = "";
     if(trim($this->y57_codauto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y57_codauto"])){
       $sql  .= $virgula." y57_codauto = $this->y57_codauto ";
       $virgula = ",";
       if(trim($this->y57_codauto) == null ){
         $this->erro_sql = " Campo Código do Auto de Infração nao Informado.";
         $this->erro_campo = "y57_codauto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y57_receit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y57_receit"])){
       $sql  .= $virgula." y57_receit = $this->y57_receit ";
       $virgula = ",";
       if(trim($this->y57_receit) == null ){
         $this->erro_sql = " Campo codigo da receita nao Informado.";
         $this->erro_campo = "y57_receit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y57_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y57_descr"])){
       $sql  .= $virgula." y57_descr = '$this->y57_descr' ";
       $virgula = ",";
       if(trim($this->y57_descr) == null ){
         $this->erro_sql = " Campo Descrição do lançamento da receita nao Informado.";
         $this->erro_campo = "y57_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y57_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y57_valor"])){
       $sql  .= $virgula." y57_valor = $this->y57_valor ";
       $virgula = ",";
       if(trim($this->y57_valor) == null ){
         $this->y57_valor = 0;
       }
     }
     $sql .= " where ";
     if($y57_codauto!=null){
       $sql .= " y57_codauto = $this->y57_codauto";
     }
     if($y57_receit!=null){
       $sql .= " and  y57_receit = $this->y57_receit";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "autorec nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y57_codauto."-".$this->y57_receit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "autorec nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y57_codauto."-".$this->y57_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y57_codauto."-".$this->y57_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y57_codauto=null,$y57_receit=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_autorec
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y57_codauto != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y57_codauto = $y57_codauto ";
        }
        if($y57_receit != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y57_receit = $y57_receit ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "autorec nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y57_codauto."-".$y57_receit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "autorec nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y57_codauto."-".$y57_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y57_codauto."-".$y57_receit;
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
        $this->erro_sql   = "Record Vazio na Tabela:autorec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y57_codauto=null,$y57_receit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autorec ";
     $sql .= "      inner join tabrec    on tabrec.k02_codigo = fis_autorec.y57_receit";
     $sql .= "      inner join fiscalizacao.fis_auto      on fis_auto.y50_codauto = fis_autorec.y57_codauto";
     $sql .= "      inner join tabrecjm  on tabrecjm.k02_codjm = tabrec.k02_codjm";
     $sql .= "      inner join db_depart on db_depart.coddepto = fis_auto.y50_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($y57_codauto!=null ){
         $sql2 .= " where fis_autorec.y57_codauto = $y57_codauto ";
       }
       if($y57_receit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_autorec.y57_receit = $y57_receit ";
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
public function sql_query_file ( $y57_codauto=null,$y57_receit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autorec ";
     $sql2 = "";
     if($dbwhere==""){
       if($y57_codauto!=null ){
         $sql2 .= " where fis_autorec.y57_codauto = $y57_codauto ";
       }
       if($y57_receit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_autorec.y57_receit = $y57_receit ";
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
