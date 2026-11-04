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
//CLASSE DA ENTIDADE vistoriaandam
class cl_fis_vistoriaandam {
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
   public $y68_codvist = 0;
   public $y68_codandam = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y68_codvist = int4 = Código da Vistoria
                 y68_codandam = int8 = Codigo do Andamento Gerado
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_vistoriaandam");
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
       $this->y68_codvist = ($this->y68_codvist == ""?@$GLOBALS["HTTP_POST_VARS"]["y68_codvist"]:$this->y68_codvist);
       $this->y68_codandam = ($this->y68_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y68_codandam"]:$this->y68_codandam);
     }else{
       $this->y68_codvist = ($this->y68_codvist == ""?@$GLOBALS["HTTP_POST_VARS"]["y68_codvist"]:$this->y68_codvist);
       $this->y68_codandam = ($this->y68_codandam == ""?@$GLOBALS["HTTP_POST_VARS"]["y68_codandam"]:$this->y68_codandam);
     }
   }
   // funcao para inclusao
public function incluir ($y68_codvist,$y68_codandam){
      $this->atualizacampos();
       $this->y68_codvist = $y68_codvist;
       $this->y68_codandam = $y68_codandam;
     if(($this->y68_codvist == null) || ($this->y68_codvist == "") ){
       $this->erro_sql = " Campo y68_codvist nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y68_codandam == null) || ($this->y68_codandam == "") ){
       $this->erro_sql = " Campo y68_codandam nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_vistoriaandam(
                                       y68_codvist
                                      ,y68_codandam
                       )
                values (
                                $this->y68_codvist
                               ,$this->y68_codandam
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "vistoriaandam ($this->y68_codvist."-".$this->y68_codandam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "vistoriaandam já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "vistoriaandam ($this->y68_codvist."-".$this->y68_codandam) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y68_codvist."-".$this->y68_codandam;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y68_codvist=null,$y68_codandam=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_vistoriaandam set ";
     $virgula = "";
     if(trim($this->y68_codvist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y68_codvist"])){
       $sql  .= $virgula." y68_codvist = $this->y68_codvist ";
       $virgula = ",";
       if(trim($this->y68_codvist) == null ){
         $this->erro_sql = " Campo Código da Vistoria nao Informado.";
         $this->erro_campo = "y68_codvist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y68_codandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y68_codandam"])){
       $sql  .= $virgula." y68_codandam = $this->y68_codandam ";
       $virgula = ",";
       if(trim($this->y68_codandam) == null ){
         $this->erro_sql = " Campo Codigo do Andamento Gerado nao Informado.";
         $this->erro_campo = "y68_codandam";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y68_codvist!=null){
       $sql .= " y68_codvist = $this->y68_codvist";
     }
     if($y68_codandam!=null){
       $sql .= " and  y68_codandam = $this->y68_codandam";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "vistoriaandam nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y68_codvist."-".$this->y68_codandam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "vistoriaandam nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y68_codvist."-".$this->y68_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y68_codvist."-".$this->y68_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y68_codvist=null,$y68_codandam=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_vistoriaandam
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y68_codvist != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y68_codvist = $y68_codvist ";
        }
        if($y68_codandam != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y68_codandam = $y68_codandam ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "vistoriaandam nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y68_codvist."-".$y68_codandam;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "vistoriaandam nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y68_codvist."-".$y68_codandam;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y68_codvist."-".$y68_codandam;
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
        $this->erro_sql   = "Record Vazio na Tabela:vistoriaandam";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y68_codvist=null,$y68_codandam=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_vistoriaandam ";
     $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_vistoriaandam.y68_codandam";
     $sql .= "      inner join fiscalizacao.fis_vistorias  on  fis_vistorias.y70_codvist = fis_vistoriaandam.y68_codvist";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_fandam.y39_id_usuario";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_fandam.y39_codtipo";
     $sql .= "      inner join db_usuarios  as a on   a.id_usuario = fis_vistorias.y70_id_usuario";
     $sql .= "      inner join fiscalizacao.fis_fandam  as b on   b.y39_codandam = fis_vistorias.y70_ultandam";
     $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistorias.y70_tipovist";
     $sql2 = "";
     if($dbwhere==""){
       if($y68_codvist!=null ){
         $sql2 .= " where fis_vistoriaandam.y68_codvist = $y68_codvist ";
       }
       if($y68_codandam!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_vistoriaandam.y68_codandam = $y68_codandam ";
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
public function sql_query_file ( $y68_codvist=null,$y68_codandam=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_vistoriaandam ";
     $sql2 = "";
     if($dbwhere==""){
       if($y68_codvist!=null ){
         $sql2 .= " where fis_vistoriaandam.y68_codvist = $y68_codvist ";
       }
       if($y68_codandam!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_vistoriaandam.y68_codandam = $y68_codandam ";
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
