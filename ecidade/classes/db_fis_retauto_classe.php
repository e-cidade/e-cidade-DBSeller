<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
//CLASSE DA ENTIDADE retauto
class cl_fis_retauto {
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
   public $pl11_autoold = 0;
   public $pl11_autoret = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 pl11_autoold = Código do Auto Antigo
                 pl11_autoret = Código do Auto Novo
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_retauto");
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
   // // funcao para inclusao
   public function incluir(){
     if(($this->pl11_autoold == null) || ($this->pl11_autoold == "") ){
       $this->erro_sql    = " Campo Codigo do Auto Antigo nao declarado.";
       $this->erro_banco  = " Chave Estrangeira zerada.";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->pl11_autoret == null) || ($this->pl11_autoret == "") ){
       $this->erro_sql    = " Campo do Auto Novo nao declarado.";
       $this->erro_banco  = "Chave Primaria zerada.";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into fiscalizacao.fis_retauto(
                                       pl11_autoold
                                      ,pl11_autoret
                       )
                values (
                                      $this->pl11_autoold
                                     ,$this->pl11_autoret
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql    = "retauto ($this->pl11_autoold."-".$this->pl11_autoret) nao Incluído. Inclusao Abortada.";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco  = "Processo Fiscal já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql    = "retauto ($this->pl11_autoold."-".$this->pl11_autoold) nao Incluído. Inclusao Abortada.";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco  = "";
     $this->erro_sql    = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql   .= "Valores : ".$this->pl11_autoold."-".$this->pl11_autoold;
     $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($pl11_codigo=null) {
     $sql = " update fiscalizacao.fis_retauto set ";
     $virgula = "";
     if(trim($this->pl11_autoold)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl11_autoold"])){
       $sql  .= $virgula." pl11_autoold = $this->pl11_autoold ";
       $virgula = ",";
       if(trim($this->pl11_autoold) == null ){
         $this->erro_sql    = " Campo Auto Antigo nao Informado.";
         $this->erro_campo  = "pl11_autoold";
         $this->erro_banco  = "";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pl11_autoret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl11_autoret"])){
       $sql  .= $virgula." pl11_autoret = $this->pl11_autoret ";
       $virgula = ",";
       if(trim($this->pl11_autoret) == null ){
         $this->erro_sql    = " Campo Novo Auto Informado.";
         $this->erro_campo  = "pl11_autoret";
         $this->erro_banco  = "";
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($pl11_codigo!=null){
       $sql .= " pl11_codigo = $pl11_codigo";
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "retauto nao Alterado. Alteracao Abortada.\\n";
       $this->erro_sql   .= "Valores : ".$this->pl11_autoold."-".$this->pl11_autoret;
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco  = "";
         $this->erro_sql    = "retauto nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql   .= "Valores : ".$this->pl11_autoold."-".$this->pl11_autoret;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pl11_autoold."-".$this->pl11_autoret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($pl11_codigo,$dbwhere=null) {

     $sql = " delete from fiscalizacao.fis_retauto
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($pl11_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " pl11_codigo = $pl11_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "retauto nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql   .= "Valores : ".$pl11_codigo;
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco  = "";
         $this->erro_sql    = "retauto nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql   .= "Valores : ".$pl11_codigo;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco  = "";
         $this->erro_sql    = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql   .= "Valores : ".$pl11_codigo;
         $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
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
       $this->numrows     = 0;
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "Erro ao selecionar os registros.";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco   = "";
        $this->erro_sql     = "Record Vazio na Tabela: retauto";
        $this->erro_msg     = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg    .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status  = "0";
        return false;
      }
     return $result;
   }
   public function sql_query_file ( $pl11_autoold=null,$pl11_autoret=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_retauto ";
     $sql2 = "";
     if($dbwhere==""){
       if($pl11_autoold!=null ){
         $sql2 .= " where pl11_autoold = $pl11_autoold ";
       }
       if($pl11_autoret!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " pl11_autoret = $pl11_autoret ";
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
