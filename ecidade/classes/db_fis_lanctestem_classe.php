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
//CLASSE DA ENTIDADE lanctestem
class cl_fis_lanctestem {
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
   public $nl21_codlanc = 0;
   public $nl21_numcgm = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 nl21_codlanc = int4 = Código da Notificação de Lançamento
                 nl21_numcgm = int4 = Numcgm
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_lanctestem");
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
       $this->nl21_codlanc = ($this->nl21_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl21_codlanc"]:$this->nl21_codlanc);
       $this->nl21_numcgm = ($this->nl21_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["nl21_numcgm"]:$this->nl21_numcgm);
     }else{
       $this->nl21_codlanc = ($this->nl21_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl21_codlanc"]:$this->nl21_codlanc);
       $this->nl21_numcgm = ($this->nl21_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["nl21_numcgm"]:$this->nl21_numcgm);
     }
   }
   // funcao para inclusao
   public function incluir ($nl21_codlanc,$nl21_numcgm){
      $this->atualizacampos();
       $this->nl21_codlanc = $nl21_codlanc;
       $this->nl21_numcgm = $nl21_numcgm;
     if(($this->nl21_codlanc == null) || ($this->nl21_codlanc == "") ){
       $this->erro_sql = " Campo nl21_codlanc nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->nl21_numcgm == null) || ($this->nl21_numcgm == "") ){
       $this->erro_sql = " Campo nl21_numcgm nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_lanctestem(
                                       nl21_codlanc
                                      ,nl21_numcgm
                       )
                values (
                                $this->nl21_codlanc
                               ,$this->nl21_numcgm
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "testemunhas da Notificação de Lançamento ($this->nl21_codlanc."-".$this->nl21_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "testemunhas da Notificação de Lançamento já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "testemunhas da Notificação de Lançamento ($this->nl21_codlanc."-".$this->nl21_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl21_codlanc."-".$this->nl21_numcgm;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($nl21_codlanc=null,$nl21_numcgm=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_lanctestem set ";
     $virgula = "";
     if(trim($this->nl21_codlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl21_codlanc"])){
       $sql  .= $virgula." nl21_codlanc = $this->nl21_codlanc ";
       $virgula = ",";
       if(trim($this->nl21_codlanc) == null ){
         $this->erro_sql = " Campo Código da Notificação de Lançamento nao Informado.";
         $this->erro_campo = "nl21_codlanc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl21_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl21_numcgm"])){
       $sql  .= $virgula." nl21_numcgm = $this->nl21_numcgm ";
       $virgula = ",";
       if(trim($this->nl21_numcgm) == null ){
         $this->erro_sql = " Campo Numcgm nao Informado.";
         $this->erro_campo = "nl21_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($nl21_codlanc!=null){
       $sql .= " nl21_codlanc = $this->nl21_codlanc";
     }
     if($nl21_numcgm!=null){
       $sql .= " and  nl21_numcgm = $this->nl21_numcgm";
     }

     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "testemunhas da Notificação de Lançamento nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl21_codlanc."-".$this->nl21_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "testemunhas da Notificação de Lançamento nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl21_codlanc."-".$this->nl21_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl21_codlanc."-".$this->nl21_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($nl21_codlanc=null,$nl21_numcgm=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_lanctestem
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($nl21_codlanc != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl21_codlanc = $nl21_codlanc ";
        }
        if($nl21_numcgm != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl21_numcgm = $nl21_numcgm ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "testemunhas da Notificação de Lançamento nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$nl21_codlanc."-".$nl21_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "testemunhas da Notificação de Lançamento nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$nl21_codlanc."-".$nl21_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$nl21_codlanc."-".$nl21_numcgm;
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
        $this->erro_sql   = "Record Vazio na Tabela:lanctestem";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $nl21_codlanc=null,$nl21_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lanctestem ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_lanctestem.nl21_numcgm";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lanctestem.nl21_codlanc";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_lancamento.nl01_setor";
     $sql2 = "";
     if($dbwhere==""){
       if($nl21_codlanc!=null ){
         $sql2 .= " where fis_lanctestem.nl21_codlanc = $nl21_codlanc ";
       }
       if($nl21_numcgm!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_lanctestem.nl21_numcgm = $nl21_numcgm ";
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
   public function sql_query_file ( $nl21_codlanc=null,$nl21_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lanctestem ";
     $sql2 = "";
     if($dbwhere==""){
       if($nl21_codlanc!=null ){
         $sql2 .= " where fis_lanctestem.nl21_codlanc = $nl21_codlanc ";
       }
       if($nl21_numcgm!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_lanctestem.nl21_numcgm = $nl21_numcgm ";
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
