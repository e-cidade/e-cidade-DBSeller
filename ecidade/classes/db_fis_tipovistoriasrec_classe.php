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
//CLASSE DA ENTIDADE tipovistoriasrec
class cl_fis_tipovistoriasrec {
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
   public $y78_codtipo = 0;
   public $y78_receit = 0;
   public $y78_valor = 0;
   public $y78_descr = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y78_codtipo = int4 = Código do Tipo
                 y78_receit = int4 = codigo da receita
                 y78_valor = float8 = Valor da Vistoria
                 y78_descr = varchar(50) = Descrição do Valor
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_tipovistoriasrec");
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
       $this->y78_codtipo = ($this->y78_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y78_codtipo"]:$this->y78_codtipo);
       $this->y78_receit = ($this->y78_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y78_receit"]:$this->y78_receit);
       $this->y78_valor = ($this->y78_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y78_valor"]:$this->y78_valor);
       $this->y78_descr = ($this->y78_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["y78_descr"]:$this->y78_descr);
     }else{
       $this->y78_codtipo = ($this->y78_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y78_codtipo"]:$this->y78_codtipo);
       $this->y78_receit = ($this->y78_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["y78_receit"]:$this->y78_receit);
     }
   }
   // funcao para inclusao
public function incluir ($y78_codtipo,$y78_receit){
      $this->atualizacampos();
     if($this->y78_valor == null ){
       $this->erro_sql = " Campo Valor da Vistoria nao Informado.";
       $this->erro_campo = "y78_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y78_descr == null ){
       $this->erro_sql = " Campo Descrição do Valor nao Informado.";
       $this->erro_campo = "y78_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y78_codtipo = $y78_codtipo;
       $this->y78_receit = $y78_receit;
     if(($this->y78_codtipo == null) || ($this->y78_codtipo == "") ){
       $this->erro_sql = " Campo y78_codtipo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y78_receit == null) || ($this->y78_receit == "") ){
       $this->erro_sql = " Campo y78_receit nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_tipovistoriasrec(
                                       y78_codtipo
                                      ,y78_receit
                                      ,y78_valor
                                      ,y78_descr
                       )
                values (
                                $this->y78_codtipo
                               ,$this->y78_receit
                               ,$this->y78_valor
                               ,'$this->y78_descr'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "tipovistoriasrec ($this->y78_codtipo."-".$this->y78_receit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "tipovistoriasrec já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "tipovistoriasrec ($this->y78_codtipo."-".$this->y78_receit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y78_codtipo."-".$this->y78_receit;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y78_codtipo=null,$y78_receit=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_tipovistoriasrec set ";
     $virgula = "";
     if(trim($this->y78_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y78_codtipo"])){
       $sql  .= $virgula." y78_codtipo = $this->y78_codtipo ";
       $virgula = ",";
       if(trim($this->y78_codtipo) == null ){
         $this->erro_sql = " Campo Código do Tipo nao Informado.";
         $this->erro_campo = "y78_codtipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y78_receit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y78_receit"])){
       $sql  .= $virgula." y78_receit = $this->y78_receit ";
       $virgula = ",";
       if(trim($this->y78_receit) == null ){
         $this->erro_sql = " Campo codigo da receita nao Informado.";
         $this->erro_campo = "y78_receit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y78_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y78_valor"])){
       $sql  .= $virgula." y78_valor = $this->y78_valor ";
       $virgula = ",";
       if(trim($this->y78_valor) == null ){
         $this->erro_sql = " Campo Valor da Vistoria nao Informado.";
         $this->erro_campo = "y78_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y78_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y78_descr"])){
       $sql  .= $virgula." y78_descr = '$this->y78_descr' ";
       $virgula = ",";
       if(trim($this->y78_descr) == null ){
         $this->erro_sql = " Campo Descrição do Valor nao Informado.";
         $this->erro_campo = "y78_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y78_codtipo!=null){
       $sql .= " y78_codtipo = $this->y78_codtipo";
     }
     if($y78_receit!=null){
       $sql .= " and  y78_receit = $this->y78_receit";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "tipovistoriasrec nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y78_codtipo."-".$this->y78_receit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "tipovistoriasrec nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y78_codtipo."-".$this->y78_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y78_codtipo."-".$this->y78_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y78_codtipo=null,$y78_receit=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_tipovistoriasrec
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y78_codtipo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y78_codtipo = $y78_codtipo ";
        }
        if($y78_receit != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y78_receit = $y78_receit ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "tipovistoriasrec nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y78_codtipo."-".$y78_receit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "tipovistoriasrec nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y78_codtipo."-".$y78_receit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y78_codtipo."-".$y78_receit;
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
        $this->erro_sql   = "Record Vazio na Tabela:tipovistoriasrec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y78_codtipo=null,$y78_receit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_tipovistoriasrec ";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = fis_tipovistoriasrec.y78_receit";
     $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_tipovistoriasrec.y78_codtipo";
     $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_tipovistorias.y77_coddepto";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_tipovistorias.y77_tipoandam";
     $sql2 = "";
     if($dbwhere==""){
       if($y78_codtipo!=null ){
         $sql2 .= " where fis_tipovistoriasrec.y78_codtipo = $y78_codtipo ";
       }
       if($y78_receit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_tipovistoriasrec.y78_receit = $y78_receit ";
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
public function sql_query_file ( $y78_codtipo=null,$y78_receit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_tipovistoriasrec ";
     $sql2 = "";
     if($dbwhere==""){
       if($y78_codtipo!=null ){
         $sql2 .= " where fis_tipovistoriasrec.y78_codtipo = $y78_codtipo ";
       }
       if($y78_receit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_tipovistoriasrec.y78_receit = $y78_receit ";
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
public function sql_query_tipovist ( $y78_codtipo=null,$y78_receit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_tipovistoriasrec ";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = fis_tipovistoriasrec.y78_receit";
     $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_tipovistoriasrec.y78_codtipo";
     $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_tipovistorias.y77_coddepto";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_tipovistorias.y77_tipoandam";
     $sql .= "      inner join fiscalizacao.fis_vistorias  on  fis_vistorias.y70_tipovist = fis_tipovistoriasrec.y78_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($y78_codtipo!=null ){
         $sql2 .= " where fis_tipovistoriasrec.y78_codtipo = $y78_codtipo ";
       }
       if($y78_receit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_tipovistoriasrec.y78_receit = $y78_receit ";
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
