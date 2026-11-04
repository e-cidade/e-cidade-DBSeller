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
//CLASSE DA ENTIDADE fiscalrec
class cl_fis_fiscalrec {
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
   public $y42_codnoti = 0;
   public $y42_receit = 0;
   public $y42_descr = null;
   public $y42_valor = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y42_codnoti = int8 = Código da Notificação
                 y42_receit = int4 = codigo da receita
                 y42_descr = varchar(50) = Descrição do lançamento da receita
                 y42_valor = float8 = Valor da Receita
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_fiscalrec");
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
       $this->y42_codnoti = ($this->y42_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y42_codnoti"]:$this->y42_codnoti);
       $this->y42_receit = ($this->y42_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y42_receit"]:$this->y42_receit);
       $this->y42_descr = ($this->y42_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["y42_descr"]:$this->y42_descr);
       $this->y42_valor = ($this->y42_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y42_valor"]:$this->y42_valor);
     }else{
       $this->y42_codnoti = ($this->y42_codnoti == ""?@$GLOBALS["HTTP_POST_VARS"]["y42_codnoti"]:$this->y42_codnoti);
       $this->y42_receit = ($this->y42_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y42_receit"]:$this->y42_receit);
     }
   }
   // funcao para inclusao
public function incluir ($y42_codnoti,$y42_receit){
      $this->atualizacampos();
     if($this->y42_descr == null ){
       $this->erro_sql = " Campo Descrição do lançamento da receita nao Informado.";
       $this->erro_campo = "y42_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y42_valor == null ){
       $this->erro_sql = " Campo Valor da Receita nao Informado.";
       $this->erro_campo = "y42_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y42_codnoti = $y42_codnoti;
       $this->y42_receit = $y42_receit;
     if(($this->y42_codnoti == null) || ($this->y42_codnoti == "") ){
       $this->erro_sql = " Campo y42_codnoti nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y42_receit == null) || ($this->y42_receit == "") ){
       $this->erro_sql = " Campo y42_receit nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_fiscalrec(
                                       y42_codnoti
                                      ,y42_receit
                                      ,y42_descr
                                      ,y42_valor
                       )
                values (
                                $this->y42_codnoti
                               ,$this->y42_receit
                               ,'$this->y42_descr'
                               ,$this->y42_valor
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "fiscalrec ($this->y42_codnoti."-".$this->y42_receit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "fiscalrec já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "fiscalrec ($this->y42_codnoti."-".$this->y42_receit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y42_codnoti."-".$this->y42_receit;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y42_codnoti=null,$y42_receit=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_fiscalrec set ";
     $virgula = "";
     if(trim($this->y42_codnoti)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y42_codnoti"])){
       $sql  .= $virgula." y42_codnoti = $this->y42_codnoti ";
       $virgula = ",";
       if(trim($this->y42_codnoti) == null ){
         $this->erro_sql = " Campo Código da Notificação nao Informado.";
         $this->erro_campo = "y42_codnoti";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y42_receit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y42_receit"])){
       $sql  .= $virgula." y42_receit = $this->y42_receit ";
       $virgula = ",";
       if(trim($this->y42_receit) == null ){
         $this->erro_sql = " Campo codigo da receita nao Informado.";
         $this->erro_campo = "y42_receit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y42_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y42_descr"])){
       $sql  .= $virgula." y42_descr = '$this->y42_descr' ";
       $virgula = ",";
       if(trim($this->y42_descr) == null ){
         $this->erro_sql = " Campo Descrição do lançamento da receita nao Informado.";
         $this->erro_campo = "y42_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y42_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y42_valor"])){
       $sql  .= $virgula." y42_valor = $this->y42_valor ";
       $virgula = ",";
       if(trim($this->y42_valor) == null ){
         $this->erro_sql = " Campo Valor da Receita nao Informado.";
         $this->erro_campo = "y42_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y42_codnoti!=null){
       $sql .= " y42_codnoti = $this->y42_codnoti";
     }
     if($y42_receit!=null){
       $sql .= " and  y42_receit = $this->y42_receit";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fiscalrec nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y42_codnoti."-".$this->y42_receit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "fiscalrec nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y42_codnoti."-".$this->y42_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y42_codnoti."-".$this->y42_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y42_codnoti=null,$y42_receit=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_fiscalrec
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y42_codnoti != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y42_codnoti = $y42_codnoti ";
        }
        if($y42_receit != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y42_receit = $y42_receit ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fiscalrec nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y42_codnoti."-".$y42_receit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "fiscalrec nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y42_codnoti."-".$y42_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y42_codnoti."-".$y42_receit;
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
        $this->erro_sql   = "Record Vazio na Tabela:fiscalrec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y42_codnoti=null,$y42_receit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fiscalrec ";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = fis_fiscalrec.y42_receit";
     $sql .= "      inner join fiscalizacao.fis_fiscal  on  fis_fiscal.y30_codnoti = fis_fiscalrec.y42_codnoti";
     $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_fiscal.y30_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($y42_codnoti!=null ){
         $sql2 .= " where fis_fiscalrec.y42_codnoti = $y42_codnoti ";
       }
       if($y42_receit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_fiscalrec.y42_receit = $y42_receit ";
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
public function sql_query_file ( $y42_codnoti=null,$y42_receit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_fiscalrec ";
     $sql2 = "";
     if($dbwhere==""){
       if($y42_codnoti!=null ){
         $sql2 .= " where fis_fiscalrec.y42_codnoti = $y42_codnoti ";
       }
       if($y42_receit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_fiscalrec.y42_receit = $y42_receit ";
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
