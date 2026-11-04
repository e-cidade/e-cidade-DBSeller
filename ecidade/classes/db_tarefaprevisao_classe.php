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

//MODULO: Atendimento
//CLASSE DA ENTIDADE tarefaprevisao
class cl_tarefaprevisao {
   // cria variaveis de erro
   var $rotulo     = null;
   var $query_sql  = null;
   var $numrows    = 0;
   var $numrows_incluir = 0;
   var $numrows_alterar = 0;
   var $numrows_excluir = 0;
   var $erro_status= null;
   var $erro_sql   = null;
   var $erro_banco = null;
   var $erro_msg   = null;
   var $erro_campo = null;
   var $pagina_retorno = null;
   // cria variaveis do arquivo
   var $at81_sequencial = 0;
   var $at81_tarefa = 0;
   var $at81_usuario = 0;
   var $at81_dtlanc_dia = null;
   var $at81_dtlanc_mes = null;
   var $at81_dtlanc_ano = null;
   var $at81_dtlanc = null;
   var $at81_hora = null;
   var $at81_ordem = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 at81_sequencial = int4 = Codigo sequencial
                 at81_tarefa = int4 = Codigo da Tarefa
                 at81_usuario = int4 = Cod. Usuário
                 at81_dtlanc = date = Data de Lançamento
                 at81_hora = char(5) = Hora
                 at81_ordem = int4 = Ordem de Execução
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("tarefaprevisao");
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
       $this->at81_sequencial = ($this->at81_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_sequencial"]:$this->at81_sequencial);
       $this->at81_tarefa = ($this->at81_tarefa == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_tarefa"]:$this->at81_tarefa);
       $this->at81_usuario = ($this->at81_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_usuario"]:$this->at81_usuario);
       if($this->at81_dtlanc == ""){
         $this->at81_dtlanc_dia = ($this->at81_dtlanc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_dtlanc_dia"]:$this->at81_dtlanc_dia);
         $this->at81_dtlanc_mes = ($this->at81_dtlanc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_dtlanc_mes"]:$this->at81_dtlanc_mes);
         $this->at81_dtlanc_ano = ($this->at81_dtlanc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_dtlanc_ano"]:$this->at81_dtlanc_ano);
         if($this->at81_dtlanc_dia != ""){
            $this->at81_dtlanc = $this->at81_dtlanc_ano."-".$this->at81_dtlanc_mes."-".$this->at81_dtlanc_dia;
         }
       }
       $this->at81_hora = ($this->at81_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_hora"]:$this->at81_hora);
       $this->at81_ordem = ($this->at81_ordem == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_ordem"]:$this->at81_ordem);
     }else{
       $this->at81_sequencial = ($this->at81_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["at81_sequencial"]:$this->at81_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($at81_sequencial){
      $this->atualizacampos();
     if($this->at81_tarefa == null ){
       $this->erro_sql = " Campo Codigo da Tarefa nao Informado.";
       $this->erro_campo = "at81_tarefa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->at81_usuario == null ){
       $this->erro_sql = " Campo Cod. Usuário nao Informado.";
       $this->erro_campo = "at81_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->at81_dtlanc == null ){
       $this->erro_sql = " Campo Data de Lançamento nao Informado.";
       $this->erro_campo = "at81_dtlanc_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->at81_hora == null ){
       $this->erro_sql = " Campo Hora nao Informado.";
       $this->erro_campo = "at81_hora";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->at81_ordem == null ){
       $this->erro_sql = " Campo Ordem de Execução nao Informado.";
       $this->erro_campo = "at81_ordem";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($at81_sequencial == "" || $at81_sequencial == null ){
       $result = db_query("select nextval('tarefaprevisao_at81_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: tarefaprevisao_at81_sequencial_seq do campo: at81_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->at81_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from tarefaprevisao_at81_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $at81_sequencial)){
         $this->erro_sql = " Campo at81_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->at81_sequencial = $at81_sequencial;
       }
     }
     if(($this->at81_sequencial == null) || ($this->at81_sequencial == "") ){
       $this->erro_sql = " Campo at81_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into tarefaprevisao(
                                       at81_sequencial
                                      ,at81_tarefa
                                      ,at81_usuario
                                      ,at81_dtlanc
                                      ,at81_hora
                                      ,at81_ordem
                       )
                values (
                                $this->at81_sequencial
                               ,$this->at81_tarefa
                               ,$this->at81_usuario
                               ,".($this->at81_dtlanc == "null" || $this->at81_dtlanc == ""?"null":"'".$this->at81_dtlanc."'")."
                               ,'$this->at81_hora'
                               ,$this->at81_ordem
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Previsao de execucao das tarefas ($this->at81_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Previsao de execucao das tarefas já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Previsao de execucao das tarefas ($this->at81_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->at81_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->at81_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,14989,'$this->at81_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,2635,14989,'','".AddSlashes(pg_result($resaco,0,'at81_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2635,14990,'','".AddSlashes(pg_result($resaco,0,'at81_tarefa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2635,14991,'','".AddSlashes(pg_result($resaco,0,'at81_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2635,14992,'','".AddSlashes(pg_result($resaco,0,'at81_dtlanc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2635,14993,'','".AddSlashes(pg_result($resaco,0,'at81_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2635,15267,'','".AddSlashes(pg_result($resaco,0,'at81_ordem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($at81_sequencial=null) {
      $this->atualizacampos();
     $sql = " update tarefaprevisao set ";
     $virgula = "";
     if(trim($this->at81_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at81_sequencial"])){
       $sql  .= $virgula." at81_sequencial = $this->at81_sequencial ";
       $virgula = ",";
       if(trim($this->at81_sequencial) == null ){
         $this->erro_sql = " Campo Codigo sequencial nao Informado.";
         $this->erro_campo = "at81_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->at81_tarefa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at81_tarefa"])){
       $sql  .= $virgula." at81_tarefa = $this->at81_tarefa ";
       $virgula = ",";
       if(trim($this->at81_tarefa) == null ){
         $this->erro_sql = " Campo Codigo da Tarefa nao Informado.";
         $this->erro_campo = "at81_tarefa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->at81_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at81_usuario"])){
       $sql  .= $virgula." at81_usuario = $this->at81_usuario ";
       $virgula = ",";
       if(trim($this->at81_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário nao Informado.";
         $this->erro_campo = "at81_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->at81_dtlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at81_dtlanc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["at81_dtlanc_dia"] !="") ){
       $sql  .= $virgula." at81_dtlanc = '$this->at81_dtlanc' ";
       $virgula = ",";
       if(trim($this->at81_dtlanc) == null ){
         $this->erro_sql = " Campo Data de Lançamento nao Informado.";
         $this->erro_campo = "at81_dtlanc_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["at81_dtlanc_dia"])){
         $sql  .= $virgula." at81_dtlanc = null ";
         $virgula = ",";
         if(trim($this->at81_dtlanc) == null ){
           $this->erro_sql = " Campo Data de Lançamento nao Informado.";
           $this->erro_campo = "at81_dtlanc_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->at81_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at81_hora"])){
       $sql  .= $virgula." at81_hora = '$this->at81_hora' ";
       $virgula = ",";
       if(trim($this->at81_hora) == null ){
         $this->erro_sql = " Campo Hora nao Informado.";
         $this->erro_campo = "at81_hora";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->at81_ordem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["at81_ordem"])){
       $sql  .= $virgula." at81_ordem = $this->at81_ordem ";
       $virgula = ",";
       if(trim($this->at81_ordem) == null ){
         $this->erro_sql = " Campo Ordem de Execução nao Informado.";
         $this->erro_campo = "at81_ordem";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($at81_sequencial!=null){
       $sql .= " at81_sequencial = $this->at81_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->at81_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14989,'$this->at81_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at81_sequencial"]) || $this->at81_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,2635,14989,'".AddSlashes(pg_result($resaco,$conresaco,'at81_sequencial'))."','$this->at81_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at81_tarefa"]) || $this->at81_tarefa != "")
           $resac = db_query("insert into db_acount values($acount,2635,14990,'".AddSlashes(pg_result($resaco,$conresaco,'at81_tarefa'))."','$this->at81_tarefa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at81_usuario"]) || $this->at81_usuario != "")
           $resac = db_query("insert into db_acount values($acount,2635,14991,'".AddSlashes(pg_result($resaco,$conresaco,'at81_usuario'))."','$this->at81_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at81_dtlanc"]) || $this->at81_dtlanc != "")
           $resac = db_query("insert into db_acount values($acount,2635,14992,'".AddSlashes(pg_result($resaco,$conresaco,'at81_dtlanc'))."','$this->at81_dtlanc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at81_hora"]) || $this->at81_hora != "")
           $resac = db_query("insert into db_acount values($acount,2635,14993,'".AddSlashes(pg_result($resaco,$conresaco,'at81_hora'))."','$this->at81_hora',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["at81_ordem"]) || $this->at81_ordem != "")
           $resac = db_query("insert into db_acount values($acount,2635,15267,'".AddSlashes(pg_result($resaco,$conresaco,'at81_ordem'))."','$this->at81_ordem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Previsao de execucao das tarefas nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->at81_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Previsao de execucao das tarefas nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->at81_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->at81_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($at81_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($at81_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14989,'$at81_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,2635,14989,'','".AddSlashes(pg_result($resaco,$iresaco,'at81_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2635,14990,'','".AddSlashes(pg_result($resaco,$iresaco,'at81_tarefa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2635,14991,'','".AddSlashes(pg_result($resaco,$iresaco,'at81_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2635,14992,'','".AddSlashes(pg_result($resaco,$iresaco,'at81_dtlanc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2635,14993,'','".AddSlashes(pg_result($resaco,$iresaco,'at81_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2635,15267,'','".AddSlashes(pg_result($resaco,$iresaco,'at81_ordem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from tarefaprevisao
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($at81_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " at81_sequencial = $at81_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Previsao de execucao das tarefas nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$at81_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Previsao de execucao das tarefas nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$at81_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$at81_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:tarefaprevisao";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $at81_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from tarefaprevisao ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = tarefaprevisao.at81_usuario";
     $sql .= "      inner join tarefa  on  tarefa.at40_sequencial = tarefaprevisao.at81_tarefa";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = tarefa.at40_responsavel";
     $sql2 = "";
     if($dbwhere==""){
       if($at81_sequencial!=null ){
         $sql2 .= " where tarefaprevisao.at81_sequencial = $at81_sequencial ";
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
   public function sql_query_file ( $at81_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from tarefaprevisao ";
     $sql2 = "";
     if($dbwhere==""){
       if($at81_sequencial!=null ){
         $sql2 .= " where tarefaprevisao.at81_sequencial = $at81_sequencial ";
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
