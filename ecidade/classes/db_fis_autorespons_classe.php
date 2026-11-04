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
//CLASSE DA ENTIDADE autoresponsavel
class cl_fis_autorespons {
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
   public $y124_codauto = 0;
   public $y124_numcgm = 0;
   public $y124_tipo = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y124_codauto = int4 = Código do Auto de Infração
                 y124_numcgm = int4 = Numcgm
                 y124_tipo = int4 = Tipo (1 - Solidário | 2 - Subsidiário)
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_autoresponsavel");
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
       $this->y124_codauto = ($this->y124_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_codauto"]:$this->y124_codauto);
       $this->y124_numcgm = ($this->y124_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_numcgm"]:$this->y124_numcgm);
       $this->y124_tipo = ($this->y124_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_tipo"]:$this->y124_tipo);
     }else{
       $this->y124_codauto = ($this->y124_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_codauto"]:$this->y124_codauto);
       $this->y124_numcgm = ($this->y124_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_numcgm"]:$this->y124_numcgm);
       $this->y124_tipo = ($this->y124_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_tipo"]:$this->y124_tipo);
     }
   }
   // funcao para inclusao
   public function incluir ($y124_codauto,$y124_numcgm,$y124_tipo){
      $this->atualizacampos();
       $this->y124_codauto = $y124_codauto;
       $this->y124_numcgm  = $y124_numcgm;
       $this->y124_tipo    = $y124_tipo;
     if(($this->y124_codauto == null) || ($this->y124_codauto == "") ){
       $this->erro_sql = " Campo Código do Auto de Infração nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y124_numcgm == null) || ($this->y124_numcgm == "") ){
       $this->erro_sql = " Campo Numcgm nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y124_tipo == null) || ($this->y124_tipo == "") || ($this->y124_tipo == 0) ){
       $this->erro_sql = " Campo Tipo nao declarado.";
       $this->erro_banco = "Tipo não informado.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_autoresponsavel(
                                       y124_codauto
                                      ,y124_numcgm
                                      ,y124_tipo
                       )
                values (
                                $this->y124_codauto
                               ,$this->y124_numcgm
                               ,$this->y124_tipo
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Responsáveis do auto de infração ($this->y124_codauto."-".$this->y124_numcgm."-".$this->y124_tipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Responsáveis do auto de infração já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Responsáveis do auto de infração ($this->y124_codauto."-".$this->y124_numcgm."-".$this->y124_tipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y124_codauto."-".$this->y124_numcgm."-".$this->y124_tipo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
   public function alterar ($y124_codauto=null,$y124_numcgm=null, $y124_tipo=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_autoresponsavel set ";
     $virgula = "";
     if(trim($this->y124_codauto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y124_codauto"])){
       $sql  .= $virgula." y124_codauto = $this->y124_codauto ";
       $virgula = ",";
       if(trim($this->y124_codauto) == null ){
         $this->erro_sql = " Campo Código do Auto de Infração nao Informado.";
         $this->erro_campo = "y124_codauto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y124_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y124_numcgm"])){
       $sql  .= $virgula." y124_numcgm = $this->y124_numcgm ";
       $virgula = ",";
       if(trim($this->y124_numcgm) == null ){
         $this->erro_sql = " Campo Numcgm nao Informado.";
         $this->erro_campo = "y124_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y124_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y124_tipo"]) || $this->y124_tipo == 0){
       $sql  .= $virgula." y124_tipo = $this->y124_tipo ";
       $virgula = ",";
       if(trim($this->y124_tipo) == null ){
         $this->erro_sql = " Campo Tipo nao Informado.";
         $this->erro_campo = "y124_tipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y124_codauto!=null){
       $sql .= " y124_codauto = $this->y124_codauto";
     }
     if($y124_numcgm!=null){
       $sql .= " and  y124_numcgm = $this->y124_numcgm";
     }
     if($y124_tipo!=null){
       $sql .= " and  y124_tipo = $this->y124_tipo";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Responsáveis do auto de infração nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y124_codauto."-".$this->y124_numcgm."-".$this->y124_tipo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Responsáveis do auto de infração nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y124_codauto."-".$this->y124_numcgm."-".$this->y124_tipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y124_codauto."-".$this->y124_numcgm."-".$this->y124_tipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($y124_codauto=null,$y124_numcgm=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_autoresponsavel
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y124_codauto != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y124_codauto = $y124_codauto ";
        }
        if($y124_numcgm != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y124_numcgm = $y124_numcgm ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Responsáveis do auto de infração nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y124_codauto."-".$y124_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Responsáveis do auto de infração nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y124_codauto."-".$y124_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y124_codauto."-".$y124_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluirarrenumcgm ($y124_codauto=null,$y124_numcgm=null) {

     $sql  = " delete from arrenumcgm where k00_numpre IN  ";
     $sql .= " (select distinct arrenumcgm.k00_numpre from ";
     $sql .= " arreauto inner join arrenumcgm on arreauto.k00_numpre = arrenumcgm.k00_numpre ";
     $sql .= " where k00_auto = $y124_codauto and k00_numcgm = $y124_numcgm) ";
     $sql .= " and k00_numcgm = $y124_numcgm";

     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao excluir o Responsável da arrenumcgm. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y124_codauto."-".$y124_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
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
        $this->erro_sql   = "Record Vazio na Tabela:autoresponsavel";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $y124_codauto=null,$y124_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autoresponsavel ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_autoresponsavel.y124_numcgm";
     $sql .= "      inner join fiscalizacao.fis_auto  on  fis_auto.y50_codauto = fis_autoresponsavel.y124_codauto";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_auto.y50_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($y124_codauto!=null ){
         $sql2 .= " where fis_autoresponsavel.y124_codauto = $y124_codauto ";
       }
       if($y124_numcgm!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_autoresponsavel.y124_numcgm = $y124_numcgm ";
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
   public function sql_query_file ( $y124_codauto=null,$y124_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autoresponsavel ";
     $sql2 = "";
     if($dbwhere==""){
       if($y124_codauto!=null ){
         $sql2 .= " where fis_autoresponsavel.y124_codauto = $y124_codauto ";
       }
       if($y124_numcgm!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_autoresponsavel.y124_numcgm = $y124_numcgm ";
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
