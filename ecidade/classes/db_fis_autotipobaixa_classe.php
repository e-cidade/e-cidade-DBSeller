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
//CLASSE DA ENTIDADE autotipobaixa
class cl_fis_autotipobaixa {
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
   public $y86_codautotipo = 0;
   public $y86_codbaixaproc = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y86_codautotipo = int4 = Codigo Sequencial
                 y86_codbaixaproc = int4 = Codigo da Baixa da Procedência do auto
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_autotipobaixa");
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
       $this->y86_codautotipo = ($this->y86_codautotipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y86_codautotipo"]:$this->y86_codautotipo);
       $this->y86_codbaixaproc = ($this->y86_codbaixaproc == ""?@$GLOBALS["HTTP_POST_VARS"]["y86_codbaixaproc"]:$this->y86_codbaixaproc);
     }else{
       $this->y86_codautotipo = ($this->y86_codautotipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y86_codautotipo"]:$this->y86_codautotipo);
     }
   }
   // funcao para inclusao
public function incluir ($y86_codautotipo){
      $this->atualizacampos();
     if($this->y86_codbaixaproc == null ){
       $this->erro_sql = " Campo Codigo da Baixa da Procedência do auto nao Informado.";
       $this->erro_campo = "y86_codbaixaproc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y86_codautotipo = $y86_codautotipo;
     if(($this->y86_codautotipo == null) || ($this->y86_codautotipo == "") ){
       $this->erro_sql = " Campo y86_codautotipo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_autotipobaixa(
                                       y86_codautotipo
                                      ,y86_codbaixaproc
                       )
                values (
                                $this->y86_codautotipo
                               ,$this->y86_codbaixaproc
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Baixa procedências do auto ($this->y86_codautotipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Baixa procedências do auto já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Baixa procedências do auto ($this->y86_codautotipo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y86_codautotipo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y86_codautotipo=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_autotipobaixa set ";
     $virgula = "";
     if(trim($this->y86_codautotipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y86_codautotipo"])){
       $sql  .= $virgula." y86_codautotipo = $this->y86_codautotipo ";
       $virgula = ",";
       if(trim($this->y86_codautotipo) == null ){
         $this->erro_sql = " Campo Codigo Sequencial nao Informado.";
         $this->erro_campo = "y86_codautotipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y86_codbaixaproc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y86_codbaixaproc"])){
       $sql  .= $virgula." y86_codbaixaproc = $this->y86_codbaixaproc ";
       $virgula = ",";
       if(trim($this->y86_codbaixaproc) == null ){
         $this->erro_sql = " Campo Codigo da Baixa da Procedência do auto nao Informado.";
         $this->erro_campo = "y86_codbaixaproc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y86_codautotipo!=null){
       $sql .= " y86_codautotipo = $this->y86_codautotipo";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Baixa procedências do auto nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y86_codautotipo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Baixa procedências do auto nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y86_codautotipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y86_codautotipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y86_codautotipo=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_autotipobaixa
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y86_codautotipo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y86_codautotipo = $y86_codautotipo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Baixa procedências do auto nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y86_codautotipo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Baixa procedências do auto nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y86_codautotipo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y86_codautotipo;
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
        $this->erro_sql   = "Record Vazio na Tabela:autotipobaixa";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y86_codautotipo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autotipobaixa ";
     $sql .= "      inner join fiscalizacao.fis_autotipo               on  fis_autotipo.y59_codigo                  = fis_autotipobaixa.y86_codautotipo";
     $sql .= "      left  join fiscalizacao.fis_autotipobaixaproc      on  fis_autotipobaixaproc.y87_baixaproc      = fis_autotipobaixa.y86_codbaixaproc";
     $sql .= "      left  join fiscalizacao.fis_autotipobaixaprocproc  on  fis_autotipobaixaprocproc.y114_baixaproc = fis_autotipobaixaproc.y87_baixaproc";
     $sql .= "      inner join fiscalizacao.fis_fiscalproc             on  fis_fiscalproc.y29_codtipo               = fis_autotipo.y59_codtipo";
     $sql .= "      inner join fiscalizacao.fis_auto                   on  fis_auto.y50_codauto                     = fis_autotipo.y59_codauto";
     $sql .= "      inner join db_usuarios            on  db_usuarios.id_usuario               = fis_autotipobaixaproc.y87_usuario";
     $sql .= "      left  join protprocesso           on  protprocesso.p58_codproc             = fis_autotipobaixaprocproc.y114_processo";
     $sql2 = "";
     if($dbwhere==""){
       if($y86_codautotipo!=null ){
         $sql2 .= " where fis_autotipobaixa.y86_codautotipo = $y86_codautotipo ";
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
public function sql_query_file ( $y86_codautotipo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autotipobaixa ";
     $sql2 = "";
     if($dbwhere==""){
       if($y86_codautotipo!=null ){
         $sql2 .= " where fis_autotipobaixa.y86_codautotipo = $y86_codautotipo ";
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
