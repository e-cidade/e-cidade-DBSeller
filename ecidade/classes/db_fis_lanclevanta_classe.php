<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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
//CLASSE DA ENTIDADE lanclevanta
class cl_fis_lanclevanta {
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
   public $nl15_sequencial = 0;
   public $nl15_lancamento = 0;
   public $nl15_levanta = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 nl15_sequencial = int4 = Lançamento/Levanta
                 nl15_lancamento = int4 = Notificação de Lançamento
                 nl15_levanta = int4 = Levantamento
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_lanclevanta");
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
       $this->nl15_sequencial = ($this->nl15_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["nl15_sequencial"]:$this->nl15_sequencial);
       $this->nl15_lancamento = ($this->nl15_lancamento == ""?@$GLOBALS["HTTP_POST_VARS"]["nl15_lancamento"]:$this->nl15_lancamento);
       $this->nl15_levanta = ($this->nl15_levanta == ""?@$GLOBALS["HTTP_POST_VARS"]["nl15_levanta"]:$this->nl15_levanta);
     }else{
       $this->nl15_sequencial = ($this->nl15_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["nl15_sequencial"]:$this->nl15_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($nl15_sequencial=null){
      $this->atualizacampos();
     if($this->nl15_lancamento == null ){
       $this->erro_sql = " Campo Notificação de Lançamento não informado!!!!.";
       $this->erro_campo = "nl15_lancamento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nl15_levanta == null ){
       $this->erro_sql = " Campo Levantamento não informado.";
       $this->erro_campo = "nl15_levanta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($nl15_sequencial == "" || $nl15_sequencial == null ){
       $result = db_query("select nextval('fis_lanclevanta_nl15_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: fis_lanclevanta_nl15_sequencial_seq do campo: nl15_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->nl15_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from fis_lanclevanta_nl15_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $nl15_sequencial)){
         $this->erro_sql = " Campo nl15_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->nl15_sequencial = $nl15_sequencial;
       }
     }
     if(($this->nl15_sequencial == null) || ($this->nl15_sequencial == "") ){
       $this->erro_sql = " Campo nl15_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_lanclevanta(
                                       nl15_sequencial
                                      ,nl15_lancamento
                                      ,nl15_levanta
                       )
                values (
                                $this->nl15_sequencial
                               ,$this->nl15_lancamento
                               ,$this->nl15_levanta
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Lançamento/Levanta ($this->nl15_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Lançamento/Levanta já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Lançamento/Levanta ($this->nl15_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql .= "Valores : ".$this->nl15_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($nl15_sequencial=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_lanclevanta set ";
     $virgula = "";
     if(trim($this->nl15_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl15_sequencial"])){
       $sql  .= $virgula." nl15_sequencial = $this->nl15_sequencial ";
       $virgula = ",";
       if(trim($this->nl15_sequencial) == null ){
         $this->erro_sql = " Campo Lançamento/Levanta não informado.";
         $this->erro_campo = "nl15_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl15_lancamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl15_lancamento"])){
       $sql  .= $virgula." nl15_lancamento = $this->nl15_lancamento ";
       $virgula = ",";
       if(trim($this->nl15_lancamento) == null ){
         $this->erro_sql = " Campo Notificação de Lançamento não informado.";
         $this->erro_campo = "nl15_lancamento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl15_levanta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl15_levanta"])){
       $sql  .= $virgula." nl15_levanta = $this->nl15_levanta ";
       $virgula = ",";
       if(trim($this->nl15_levanta) == null ){
         $this->erro_sql = " Campo Levantamento não informado.";
         $this->erro_campo = "nl15_levanta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($nl15_sequencial!=null){
       $sql .= " nl15_sequencial = $this->nl15_sequencial";
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Lançamento/Levanta nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl15_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Lançamento/Levanta nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($nl15_sequencial=null,$dbwhere=null) {

     $sql = " delete from fiscalizacao.fis_lanclevanta
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($nl15_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " nl15_sequencial = $nl15_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Lançamento/Levanta nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$nl15_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Lançamento/Levanta nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$nl15_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$nl15_sequencial;
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
     if (!$result) {
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_num_rows($result);
      if ($this->numrows == 0) {
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:lanclevanta";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ($nl15_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from fiscalizacao.fis_lanclevanta ";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_lanclevanta.nl15_lancamento";
     $sql .= "      inner join fiscalizacao.fis_levanta  on  fis_levanta.y60_codlev = fis_lanclevanta.nl15_levanta";
     $sql .= "      inner join db_config  on  db_config.codigo = fis_lancamento.nl01_instit";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_lancamento.nl01_setor";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_lancamento.nl01_codtipo";
     $sql .= "      left  join protprocesso  on  protprocesso.p58_codproc = fis_levanta.y60_proces";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($nl15_sequencial)) {
         $sql2 .= " where fis_lanclevanta.nl15_sequencial = $nl15_sequencial ";
       }
     } else if (!empty($dbwhere)) {
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if (!empty($ordem)) {
       $sql .= " order by {$ordem}";
     }
     return $sql;
  }
   // funcao do sql
   public function sql_query_file ($nl15_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from fiscalizacao.fis_lanclevanta ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($nl15_sequencial)){
         $sql2 .= " where fis_lanclevanta.nl15_sequencial = $nl15_sequencial ";
       }
     } else if (!empty($dbwhere)) {
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if (!empty($ordem)) {
       $sql .= " order by {$ordem}";
     }
     return $sql;
  }

}
