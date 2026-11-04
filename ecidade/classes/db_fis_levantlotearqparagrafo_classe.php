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

//MODULO: caixa
//CLASSE DA ENTIDADE levantlotearqpar
class cl_fis_levantlotearqpar {
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
   public $y124_codigo          = 0;
   public $y124_levantlotearq   = 0;
   public $y124_paragrafo       = 0;
   public $y124_texto           = null;

   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y124_codigo          = int4 = Sequencial do Paragrafo
                 y124_levantlotearq   = int4 = Código do Levantamento Lote
                 y124_paragrafo       = int4 = Código do Paragrafo
                 y124_texto           = text = Conteúdo do Parágrafo
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_levantlotearqparagrafo");
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
       $this->y124_codigo = ($this->y124_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_codigo"]:$this->y124_codigo);
       $this->y124_levantlotearq = ($this->y124_levantlotearq == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_levantlotearq"]:$this->y124_levantlotearq);
       $this->y124_paragrafo = ($this->y124_paragrafo == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_paragrafo"]:$this->y124_paragrafo);
       $this->y124_texto = ($this->y124_texto == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_texto"]:$this->y124_texto);
     }else{
       $this->y124_codigo = ($this->y124_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y124_codigo"]:$this->y124_codigo);
     }
   }
   // funcao para inclusao
   public function incluir ($y124_codigo){
     $this->atualizacampos();

     if($this->y124_levantlotearq == 0 ){
       $this->erro_sql = " Campo Levantamento Lote nao Informado.";
       $this->erro_campo = "y124_levantlotearq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y124_paragrafo == 0 ){
       $this->erro_sql = " Campo Parágrafo nao Informado.";
       $this->erro_campo = "y124_paragrafo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y124_texto == null ){
       $this->erro_sql = " Campo Texto do Parágrafo nao Informado.";
       $this->erro_campo = "y124_texto";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($y124_codigo == "" || $y124_codigo == null ){
       $result = @pg_query("select nextval('fis_levantlotearqparagrafo_y124_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: fis_levantlotearqparagrafo_y124_codigo_seq do campo: y124_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->y124_codigo = pg_result($result,0,0);
     }else{
       $result = @pg_query("select last_value from fis_levantlotearqparagrafo_y124_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $y124_codigo)){
         $this->erro_sql = " Campo $y124_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->y124_codigo = $y124_codigo;
       }
     }
     if(($this->y124_codigo == null) || ($this->y124_codigo == "") ){
       $this->erro_sql = " Campo y124_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "INSERT INTO fis_levantlotearqparagrafo(
                                      y124_codigo,
                                      y124_levantlotearq,
                                      y124_paragrafo,
                                      y124_texto)
                               VALUES ($this->y124_codigo,
                                       $this->y124_levantlotearq,
                                       $this->y124_paragrafo,
                                       '$this->y124_texto')";
 //                                      echo $sql."<br />";
     $result = @pg_exec($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Parágrafo ($this->y124_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Arquivo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Parágrafo ($this->y124_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql   = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql  .= "Valores : ".$this->y124_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir = pg_affected_rows($result);
     /*


     return true;
     */
     return true;
   }
   // funcao para alteracao
   public function alterar ($y124_codigo=null) {
      $this->atualizacampos();
      /*
     $sql = " update fis_levantlotearq set ";
     $virgula = "";
     if(trim($this->id_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["id_usuario"])){
       $sql  .= $virgula." id_usuario = $this->id_usuario ";
       $virgula = ",";
       if(trim($this->id_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário nao Informado.";
         $this->erro_campo = "id_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k15_codbco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k15_codbco"])){
       $sql  .= $virgula." k15_codbco = $this->k15_codbco ";
       $virgula = ",";
       if(trim($this->k15_codbco) == null ){
         $this->erro_sql = " Campo Banco nao Informado.";
         $this->erro_campo = "k15_codbco";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k15_codage)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k15_codage"])){
       $sql  .= $virgula." k15_codage = '$this->k15_codage' ";
       $virgula = ",";
       if(trim($this->k15_codage) == null ){
         $this->erro_sql = " Campo Agência nao Informado.";
         $this->erro_campo = "k15_codage";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->codret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["codret"])){
       $sql  .= $virgula." codret = $this->codret ";
       $virgula = ",";
       if(trim($this->codret) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "codret";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->arqret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["arqret"])){
       $sql  .= $virgula." arqret = '$this->arqret' ";
       $virgula = ",";
       if(trim($this->arqret) == null ){
         $this->erro_sql = " Campo Arquivo de retorno nao Informado.";
         $this->erro_campo = "arqret";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->dtretorno)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dtretorno_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["dtretorno_dia"] !="") ){
       $sql  .= $virgula." dtretorno = '$this->dtretorno' ";
       $virgula = ",";
       if(trim($this->dtretorno) == null ){
         $this->erro_sql = " Campo Data arquivo nao Informado.";
         $this->erro_campo = "dtretorno_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["dtretorno_dia"])){
         $sql  .= $virgula." dtretorno = null ";
         $virgula = ",";
         if(trim($this->dtretorno) == null ){
           $this->erro_sql = " Campo Data arquivo nao Informado.";
           $this->erro_campo = "dtretorno_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->dtarquivo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dtarquivo_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["dtarquivo_dia"] !="") ){
       $sql  .= $virgula." dtarquivo = '$this->dtarquivo' ";
       $virgula = ",";
       if(trim($this->dtarquivo) == null ){
         $this->erro_sql = " Campo Data Arquivo nao Informado.";
         $this->erro_campo = "dtarquivo_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["dtarquivo_dia"])){
         $sql  .= $virgula." dtarquivo = null ";
         $virgula = ",";
         if(trim($this->dtarquivo) == null ){
           $this->erro_sql = " Campo Data Arquivo nao Informado.";
           $this->erro_campo = "dtarquivo_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->k00_conta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k00_conta"])){
       $sql  .= $virgula." k00_conta = $this->k00_conta ";
       $virgula = ",";
       if(trim($this->k00_conta) == null ){
         $this->erro_sql = " Campo Conta nao Informado.";
         $this->erro_campo = "k00_conta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->autent)!="" || isset($GLOBALS["HTTP_POST_VARS"]["autent"])){
       $sql  .= $virgula." autent = '$this->autent' ";
       $virgula = ",";
       if(trim($this->autent) == null ){
         $this->erro_sql = " Campo Autentica nao Informado.";
         $this->erro_campo = "autent";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($codret!=null){
       $sql .= " codret = $this->codret";
     }


     $result = @pg_exec($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Arquivos de Baixa banco nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codret;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Arquivos de Baixa banco nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
     */
   }
   // funcao para exclusao
   public function excluir ($y124_codigo=null,$dbwhere=null) {
    /*
     $sql = " delete from fis_levantlotearq
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($codret != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codret = $codret ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Arquivos de Baixa banco nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codret;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Arquivos de Baixa banco nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
     */
   }
   // funcao do recordset
   public function sql_record($sql) {
     $result = @pg_query($sql);
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
        $this->erro_sql   = "Record Vazio na Tabela:fis_levantlotearqparagrafo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $y124_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fis_levantlotearqparagrafo ";
 //    $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_levantlotearq.id_usuario";
 //   $sql .= "      inner join saltes  on  saltes.k13_conta = fis_levantlotearq.k00_conta";
     $sql2 = "";
     if($dbwhere==""){
       if($y124_codigo!=null ){
         $sql2 .= " where fis_levantlotearqparagrafo.y124_codigo = $y124_codigo ";
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
   public function sql_query_file ( $y124_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fis_levantlotearqparagrafo ";
     $sql2 = "";
     if($dbwhere==""){
       if($y124_codigo!=null ){
         $sql2 .= " where fis_levantlotearqparagrafo.y124_codigo = $y124_codigo ";
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
