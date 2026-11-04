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
//CLASSE DA ENTIDADE lancexec
class cl_fis_lancexec{
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
   public $nl03_codlanc = 0;
   public $nl03_codigo = 0;
   public $nl03_codi = 0;
   public $nl03_numero = 0;
   public $nl03_compl = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 nl03_codlanc = int4 = Código do notificação de lançamento
                 nl03_codigo = int4 = cód. Rua/Avenida
                 nl03_codi = int4 = Bairro
                 nl03_numero = int4 = Número
                 nl03_compl = varchar(20) = Complemento
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_lancexec");
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
       $this->nl03_codlanc = ($this->nl03_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl03_codlanc"]:$this->nl03_codlanc);
       $this->nl03_codigo = ($this->nl03_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl03_codigo"]:$this->nl03_codigo);
       $this->nl03_codi = ($this->nl03_codi == ""?@$GLOBALS["HTTP_POST_VARS"]["nl03_codi"]:$this->nl03_codi);
       $this->nl03_numero = ($this->nl03_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["nl03_numero"]:$this->nl03_numero);
       $this->nl03_compl = ($this->nl03_compl == ""?@$GLOBALS["HTTP_POST_VARS"]["nl03_compl"]:$this->nl03_compl);
     }else{
       $this->nl03_codlanc = ($this->nl03_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl03_codlanc"]:$this->nl03_codlanc);
     }
   }
   // funcao para inclusao
   public function incluir ($nl03_codlanc){
      $this->atualizacampos();
     if($this->nl03_codigo == null ){
       $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
       $this->erro_campo = "nl03_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nl03_codi == null ){
       $this->erro_sql = " Campo Bairro nao Informado.";
       $this->erro_campo = "nl03_codi";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nl03_numero == null ){
       $this->erro_sql = " Campo Número nao Informado.";
       $this->erro_campo = "nl03_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->nl03_codlanc = $nl03_codlanc;
     if(($this->nl03_codlanc == null) || ($this->nl03_codlanc == "") ){
       $this->erro_sql = " Campo nl03_codlanc nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_lancexec(
                                       nl03_codlanc
                                      ,nl03_codigo
                                      ,nl03_codi
                                      ,nl03_numero
                                      ,nl03_compl
                       )
                values (
                                $this->nl03_codlanc
                               ,$this->nl03_codigo
                               ,$this->nl03_codi
                               ,$this->nl03_numero
                               ,'$this->nl03_compl'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "local onde a notificação de lançamento foi executado ($this->nl03_codlanc) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "local onde a notificação de lançamento foi executado já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "local onde a notificação de lançamento foi executado ($this->nl03_codlanc) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl03_codlanc;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($nl03_codlanc=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_lancexec set ";
     $virgula = "";
     if(trim($this->nl03_codlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl03_codlanc"])){
       $sql  .= $virgula." nl03_codlanc = $this->nl03_codlanc ";
       $virgula = ",";
       if(trim($this->nl03_codlanc) == null ){
         $this->erro_sql = " Campo Código da notificação de lançamento nao Informado.";
         $this->erro_campo = "nl03_codlanc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl03_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl03_codigo"])){
       $sql  .= $virgula." nl03_codigo = $this->nl03_codigo ";
       $virgula = ",";
       if(trim($this->nl03_codigo) == null ){
         $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
         $this->erro_campo = "nl03_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl03_codi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl03_codi"])){
       $sql  .= $virgula." nl03_codi = $this->nl03_codi ";
       $virgula = ",";
       if(trim($this->nl03_codi) == null ){
         $this->erro_sql = " Campo Bairro nao Informado.";
         $this->erro_campo = "nl03_codi";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl03_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl03_numero"])){
       $sql  .= $virgula." nl03_numero = $this->nl03_numero ";
       $virgula = ",";
       if(trim($this->nl03_numero) == null ){
         $this->erro_sql = " Campo Número nao Informado.";
         $this->erro_campo = "nl03_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl03_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl03_compl"])){
       $sql  .= $virgula." nl03_compl = '$this->nl03_compl' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($nl03_codlanc!=null){
       $sql .= " nl03_codlanc = $this->nl03_codlanc";
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local onde a notificação de lançamento foi executado nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl03_codlanc;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local onde a notificação de lançamento foi executado nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl03_codlanc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl03_codlanc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($nl03_codlanc=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_lancexec
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($nl03_codlanc != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl03_codlanc = $nl03_codlanc ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local onde a notificação de lançamento foi executado nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$nl03_codlanc;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local onde a notificação de lançamento foi executado nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$nl03_codlanc;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$nl03_codlanc;
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
        $this->erro_sql   = "Record Vazio na Tabela:lancexec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $nl03_codlanc=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lancexec ";
     $sql .= "      inner join bairro  on  bairro.j13_codi = fis_lancexec.nl03_codi";
     $sql .= "      inner join ruas    on  ruas.j14_codigo = fis_lancexec.nl03_codigo";
     $sql .= "      inner join fiscalizacao.fis_lancamento on  fis_lancamento.nl01_codlanc = fis_lancexec.nl03_codlanc";
     $sql .= "      inner join db_config  on  db_config.codigo = fis_lancamento.nl01_instit";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_lancamento.nl01_setor";
     $sql .= "      left join fiscalizacao.fis_procfiscallanc  on  fis_procfiscallanc.nl09_lanc = fis_lancamento.nl01_codlanc";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_lancamento.nl01_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($nl03_codlanc!=null ){
         $sql2 .= " where fis_lancexec.nl03_codlanc = $nl03_codlanc ";
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
   public function sql_query_file ( $nl03_codlanc=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lancexec ";
     $sql2 = "";
     if($dbwhere==""){
       if($nl03_codlanc!=null ){
         $sql2 .= " where fis_lancexec.nl03_codlanc = $nl03_codlanc ";
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
