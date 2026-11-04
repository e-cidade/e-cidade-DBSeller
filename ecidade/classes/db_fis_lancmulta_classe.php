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
//CLASSE DA ENTIDADE lancmulta
class cl_fis_lancmulta {
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
   public $nl28_codigo = 0;
   public $nl28_codlanc = 0;
   public $nl28_codtipo = 0;
   public $nl28_valor = 0;
   public $nl28_lanctipo = 0;

   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 nl28_codigo   = int4   = Codigo Sequencial
                 nl28_codlanc  = int4   = Código da Notificação de Lançamento
                 nl28_codtipo  = int8   = Código da Procedência
                 nl28_valor    = float8 = Valor
                 nl28_lanctipo = int4   = chave lanctipo
                 ";

   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_lancmulta");
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
       $this->nl28_codigo = ($this->nl28_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl28_codigo"]:$this->nl28_codigo);
       $this->nl28_codlanc = ($this->nl28_codlanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl28_codlanc"]:$this->nl28_codlanc);
       $this->nl28_codtipo = ($this->nl28_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl28_codtipo"]:$this->nl28_codtipo);
       $this->nl28_valor = ($this->nl28_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["nl28_valor"]:$this->nl28_valor);
       $this->nl28_lanctipo = ($this->nl28_lanctipo == ""? 'null' :$this->nl28_lanctipo);

     }else{
       $this->nl28_codigo = ($this->nl28_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl28_codigo"]:$this->nl28_codigo);
     }
   }

   // funcao para inclusao
   public function incluir ($nl28_codigo){
     $this->atualizacampos();

     if($this->nl28_codlanc == null ){
       $this->erro_sql = " Campo Código da Notificação de Lançamento nao Informado.";
       $this->erro_campo = "nl28_codlanc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nl28_codtipo == null ){
       $this->erro_sql = " Campo Código da Procedência nao Informado.";
       $this->erro_campo = "nl28_codtipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->nl28_valor == null ){
      $this->nl28_valor = 0;
     }

     if($this->nl28_lanctipo == null ){
       $this->erro_sql = " Campo Tipo de Correção nao Informado.";
       $this->erro_campo = "nl28_lanctipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($nl28_codigo == "" || $nl28_codigo == null ){
       $result = db_query("select nextval('fis_lancmulta_nl28_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: fis_lancmulta_nl28_codigo_seq do campo: nl28_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->nl28_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from fis_lancmulta_nl28_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $nl28_codigo)){
         $this->erro_sql = " Campo nl28_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->nl28_codigo = $nl28_codigo;
       }
     }
     if(($this->nl28_codigo == null) || ($this->nl28_codigo == "") ){
       $this->erro_sql = " Campo nl28_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into fiscalizacao.fis_lancmulta(
                                       nl28_codigo
                                      ,nl28_codlanc
                                      ,nl28_codtipo
                                      ,nl28_valor
                                      ,nl28_lanctipo
                       )
                values (
                                $this->nl28_codigo
                               ,$this->nl28_codlanc
                               ,$this->nl28_codtipo
                               ,$this->nl28_valor
                               ,$this->nl28_lanctipo
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "lancmulta ($this->nl28_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "lancmulta já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "lancmulta ($this->nl28_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql .= "Valores : ".$this->nl28_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);

     return true;
   }

   // funcao para alteracao
   public function alterar ($nl28_codigo=null) {
     $this->atualizacampos();
     $sql = " update fiscalizacao.fis_lancmulta set ";
     $virgula = "";
     if(trim($this->nl28_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl28_codigo"])){
       $sql  .= $virgula." nl28_codigo = $this->nl28_codigo ";
       $virgula = ",";
       if(trim($this->nl28_codigo) == null ){
         $this->erro_sql = " Campo Codigo Sequencial nao Informado.";
         $this->erro_campo = "nl28_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl28_codlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl28_codlanc"])){
       $sql  .= $virgula." nl28_codlanc = $this->nl28_codlanc ";
       $virgula = ",";
       if(trim($this->nl28_codlanc) == null ){
         $this->erro_sql = " Campo Código da Notificação de Lançamento nao Informado.";
         $this->erro_campo = "nl28_codlanc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl28_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl28_codtipo"])){
       $sql  .= $virgula." nl28_codtipo = $this->nl28_codtipo ";
       $virgula = ",";
       if(trim($this->nl28_codtipo) == null ){
         $this->erro_sql = " Campo Código da Procedência nao Informado.";
         $this->erro_campo = "nl28_codtipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl28_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl28_valor"])){
       $sql  .= $virgula." nl28_valor = $this->nl28_valor ";
       $virgula = ",";
       if(trim($this->nl28_valor) == null ){
         $this->erro_sql = " Campo Valor nao Informado.";
         $this->erro_campo = "nl28_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl28_lanctipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl28_lanctipo"])){
       $sql  .= $virgula." nl28_lanctipo = $this->nl28_lanctipo ";
       $virgula = ",";
       if(trim($this->nl28_lanctipo) == null ){
         $this->erro_sql = " Campo Tipo de Correção nao Informado.";
         $this->erro_campo = "nl28_lanctipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     $sql .= " where ";
     if($nl28_codigo!=null){
       $sql .= " nl28_codigo = $this->nl28_codigo";
     }

     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "lancmulta nao Alterado. Alteracao Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->nl28_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "lancmulta nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl28_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl28_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

   // funcao para exclusao
   public function excluir ($nl28_codigo=null,$dbwhere=null) {

     $sql = " delete from fiscalizacao.fis_lancmulta
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($nl28_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl28_codigo = $nl28_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "lancmulta nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$nl28_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "lancmulta nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$nl28_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$nl28_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:lancmulta";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

   public function sql_query ( $nl28_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lancmulta ";
     $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  y29_codtipo = fis_lancmulta.nl28_codtipo";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lancmulta.nl28_codlanc";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = y29_coddepto";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = y29_tipoandam";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = y29_tipofisc";
     $sql .= "      inner join db_depart  as a on   a.coddepto = fis_lancamento.nl01_setor";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  as b on   b.y27_codtipo = fis_lancamento.nl01_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($nl28_codigo!=null ){
         $sql2 .= " where fis_lancmulta.nl28_codigo = $nl28_codigo ";
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

   public function sql_query_baixa ( $nl28_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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

     $sql .= " from fiscalizacao.fis_lancmulta ";
     $sql .= "      inner join fiscalizacao.fis_fiscalproc            on y29_codtipo                   = fis_lancmulta.nl28_codtipo                  ";

     $sql .= "      left  join lancmultabaixa         on lancmultabaixa.nl23_codlancmulta            = fis_lancmulta.nl28_codigo                   ";
     $sql .= "      left  join lancmultabaixaproc     on lancmultabaixaproc.nl24_baixaproc          = lancmultabaixa.nl23_codbaixaproc        ";
     $sql .= "      left  join lancmultabaixaprocproc on lancmultabaixaprocproc.nl25_baixaproc     = lancmultabaixaproc.nl24_baixaproc       ";
     $sql .= "      left  join protprocesso          on protprocesso.p58_codproc                 = lancmultabaixaprocproc.nl25_processo   ";
     $sql2 = "";
     if($dbwhere==""){
       if($nl28_codigo!=null ){
         $sql2 .= " where fis_lancmulta.nl28_codigo = $nl28_codigo ";
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

   public function sql_query_lancmultatipo( $nl28_codigo=null,$campos="*",$ordem=null,$dbwhere="" ){
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
     $sql .= " from fiscalizacao.fis_lancmulta ";
     $sql .= "      inner join fiscalizacao.fis_fiscalproc a on  a.y29_codtipo = fis_lancmulta.nl28_codtipo";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lancmulta.nl28_codlanc";
     $sql .= "      left  join fiscalizacao.fis_lanctipo    on  nl18_codigo = nl28_lanctipo ";
     $sql .= "      left  join fiscalizacao.fis_fiscalproc b on  b.y29_codtipo = nl18_codtipo ";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = a.y29_coddepto";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = a.y29_tipoandam";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = a.y29_tipofisc";
     $sql2 = "";
     if($dbwhere==""){
       if($nl28_codigo!=null ){
         $sql2 .= " where fis_lancmulta.nl28_codigo = $nl28_codigo ";
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

   public function sql_query_file ( $nl28_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lancmulta ";
     $sql2 = "";
     if($dbwhere==""){
       if($nl28_codigo!=null ){
         $sql2 .= " where fis_lancmulta.nl28_codigo = $nl28_codigo ";
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

   public function sql_query_rec ( $nl28_codlanc=null,$nl28_codtipo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_lancmulta ";
     $sql .= "      inner join fiscalizacao.fis_fiscalproc  on  y29_codtipo = fis_lancmulta.nl28_codtipo";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lancmulta.nl28_codlanc";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = y29_coddepto";
     $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = y29_tipoandam";
     $sql .= "      inner join db_depart  as a on   a.coddepto = fis_lancamento.nl01_setor";
     $sql .= "      inner join fiscalizacao.fis_fiscalprocrec on fis_fiscalprocrec.y45_codtipo=y29_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($nl28_codlanc!=null ){
         $sql2 .= " where fis_lancmulta.nl28_codlanc = $nl28_codlanc ";
       }
       if($nl28_codtipo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_lancmulta.nl28_codtipo = $nl28_codtipo ";
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
