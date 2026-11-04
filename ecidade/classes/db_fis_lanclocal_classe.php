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

//MODULO: Fiscal
//CLASSE DA ENTIDADE lanclocal
class cl_fis_lanclocal {
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
   public $nl02_codlanc = 0;
   public $nl02_codigo = 0;
   public $nl02_codi = 0;
   public $nl02_numero = 0;
   public $nl02_compl = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 nl02_codlanc = int4 = Código do Notificação de Lançamento
                 nl02_codigo = int4 = cód. Rua/Avenida
                 nl02_codi = int4 = Bairro
                 nl02_numero = int4 = Número
                 nl02_compl = varchar(20) = Complemento
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_lanclocal");
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
       $this->nl02_codlanc = ($this->nl02_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl02_codlanc"]:$this->nl02_codlanc);
       $this->nl02_codigo = ($this->nl02_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl02_codigo"]:$this->nl02_codigo);
       $this->nl02_codi = ($this->nl02_codi == ""?@$GLOBALS["HTTP_POST_VARS"]["nl02_codi"]:$this->nl02_codi);
       $this->nl02_numero = ($this->nl02_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["nl02_numero"]:$this->nl02_numero);
       $this->nl02_compl = ($this->nl02_compl == ""?@$GLOBALS["HTTP_POST_VARS"]["nl02_compl"]:$this->nl02_compl);
     }else{
       $this->nl02_codlanc = ($this->nl02_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl02_codlanc"]:$this->nl02_codlanc);
     }
   }
   // funcao para inclusao
   public function incluir ($nl02_codlanc){

      $this->atualizacampos();
     if($this->nl02_codigo == null ){
       $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
       $this->erro_campo = "nl02_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nl02_codi == null ){
       $this->erro_sql = " Campo Bairro nao Informado.";
       $this->erro_campo = "nl02_codi";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nl02_numero == null ){
       $this->erro_sql = " Campo Número nao Informado.";
       $this->erro_campo = "nl02_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->nl02_codlanc = $nl02_codlanc;
     if(($this->nl02_codlanc == null) || ($this->nl02_codlanc == "") ){
       $this->erro_sql = " Campo nl02_codlanc nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into fiscalizacao.fis_lanclocal (
                                       nl02_codlanc
                                      ,nl02_codigo
                                      ,nl02_codi
                                      ,nl02_numero
                                      ,nl02_compl
                       )
                values (
                                $this->nl02_codlanc
                               ,$this->nl02_codigo
                               ,$this->nl02_codi
                               ,$this->nl02_numero
                               ,'$this->nl02_compl'
                      )";

     $result = db_query($sql);

     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "local da Notificação de Lançamento ($this->nl02_codlanc) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "local do Notificação de Lançamento já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "local da Notificação de Lançamento ($this->nl02_codlanc) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql .= "Valores : ".$this->nl02_codlanc;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($nl02_codlanc=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_lanclocal set ";
     $virgula = "";
     if(trim($this->nl02_codlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl02_codlanc"])){
       $sql  .= $virgula." nl02_codlanc = $this->nl02_codlanc ";
       $virgula = ",";
       if(trim($this->nl02_codlanc) == null ){
         $this->erro_sql = " Campo Código da Notificação de Lançamento nao Informado.";
         $this->erro_campo = "nl02_codlanc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl02_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl02_codigo"])){
       $sql  .= $virgula." nl02_codigo = $this->nl02_codigo ";
       $virgula = ",";
       if(trim($this->nl02_codigo) == null ){
         $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
         $this->erro_campo = "nl02_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl02_codi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl02_codi"])){
       $sql  .= $virgula." nl02_codi = $this->nl02_codi ";
       $virgula = ",";
       if(trim($this->nl02_codi) == null ){
         $this->erro_sql = " Campo Bairro nao Informado.";
         $this->erro_campo = "nl02_codi";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl02_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl02_numero"])){
       $sql  .= $virgula." nl02_numero = $this->nl02_numero ";
       $virgula = ",";
       if(trim($this->nl02_numero) == null ){
         $this->erro_sql = " Campo Número nao Informado.";
         $this->erro_campo = "nl02_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl02_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl02_compl"])){
       $sql  .= $virgula." nl02_compl = '$this->nl02_compl' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($nl02_codlanc!=null){
       $sql .= " nl02_codlanc = $this->nl02_codlanc";
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local da Notificação de Lançamento nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl02_codlanc;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local da Notificação de Lançamento nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl02_codlanc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl02_codlanc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($nl02_codlanc=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_lanclocal
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($nl02_codlanc != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl02_codlanc = $nl02_codlanc ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local da Notificação de Lançamento nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$nl02_codlanc;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local da Notificação de Lançamento nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$nl02_codlanc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$nl02_codlanc;
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
        $this->erro_sql   = "Record Vazio na Tabela:lanclocal";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }

     return $result;
   }
   // funcao do sql
   public function sql_query ( $nl02_codlanc=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lanclocal  ";
     $sql .= "      inner join bairro  on  bairro.j13_codi = fis_lanclocal.nl02_codi";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_lanclocal.nl02_codigo";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lanclocal.nl02_codlanc";
     $sql .= "      inner join db_config  on  db_config.codigo = fis_lancamento.nl01_instit";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_lancamento.nl01_setor";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_lancamento.nl01_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($nl02_codlanc!=null ){
         $sql2 .= " where fis_lanclocal.nl02_codlanc = $nl02_codlanc ";
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
   public function sql_query_file ( $nl02_codlanc=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lanclocal ";
     $sql2 = "";
     if($dbwhere==""){
       if($nl02_codlanc!=null ){
         $sql2 .= " where fis_lanclocal.nl02_codlanc = $nl02_codlanc ";
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
