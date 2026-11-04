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

//MODULO: cadastro
//CLASSE DA ENTIDADE certidaoexistencia
class cl_certidaoexistencia {
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
   var $j133_sequencial = 0;
   var $j133_db_usuarios = 0;
   var $j133_matric = 0;
   var $j133_iptuconstr = 0;
   var $j133_data_dia = null;
   var $j133_data_mes = null;
   var $j133_data_ano = null;
   var $j133_data = null;
   var $j133_hora = null;
   var $j133_arquivo = 0;
   var $j133_observacao = null;
   var $j133_processo = null;
   var $j133_titulaprocesso = null;
   var $j133_dtprocesso_dia = null;
   var $j133_dtprocesso_mes = null;
   var $j133_dtprocesso_ano = null;
   var $j133_dtprocesso = null;
   var $j133_arealote = null;
   var $j133_areareallote = null;
   var $j133_areaconstruida = null;
   var $j133_arearealconstruida = null;
   var $j133_numhabite = null;
   var $j133_dathabite = null;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 j133_sequencial = int4 = Sequencial
                 j133_db_usuarios = int4 = Usuário
                 j133_matric = int4 = Matrícula
                 j133_iptuconstr = int4 = Construção
                 j133_data = date = Data
                 j133_hora = varchar(5) = Hora
                 j133_arquivo = oid = Arquivo
                 j133_observacao = varchar(255) = Observação
                 j133_processo = varchar(100) = Número do processo
                 j133_titulaprocesso = varchar(150) = Titular do Processo
                 j133_dtprocesso = date = Data do Processo
                 j133_arealote = float = Tamanho da Área do Lote
                 j133_areareallote = float = Tamanho da Área Real do Lote
                 j133_areaconstruida = float = Tamanho da Área Construída do Lote
                 j133_arearealconstruida = float = Tamanho da Área Real Construída do Lote
                 j133_numhabite = varchar(20) = Número de Cadastro do Habite-se do Lote
                 j133_dathabite = date = Data da Emissão do Habite-se
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("certidaoexistencia");
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
       $this->j133_sequencial = ($this->j133_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_sequencial"]:$this->j133_sequencial);
       $this->j133_db_usuarios = ($this->j133_db_usuarios == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_db_usuarios"]:$this->j133_db_usuarios);
       $this->j133_matric = ($this->j133_matric == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_matric"]:$this->j133_matric);
       $this->j133_iptuconstr = ($this->j133_iptuconstr == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_iptuconstr"]:$this->j133_iptuconstr);
       if($this->j133_data == ""){
         $this->j133_data_dia = ($this->j133_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_data_dia"]:$this->j133_data_dia);
         $this->j133_data_mes = ($this->j133_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_data_mes"]:$this->j133_data_mes);
         $this->j133_data_ano = ($this->j133_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_data_ano"]:$this->j133_data_ano);
         if($this->j133_data_dia != ""){
            $this->j133_data = $this->j133_data_ano."-".$this->j133_data_mes."-".$this->j133_data_dia;
         }
       }
       $this->j133_hora = ($this->j133_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_hora"]:$this->j133_hora);
       $this->j133_arquivo = ($this->j133_arquivo == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_arquivo"]:$this->j133_arquivo);
       $this->j133_observacao = ($this->j133_observacao == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_observacao"]:$this->j133_observacao);
       $this->j133_processo = ($this->j133_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_processo"]:$this->j133_processo);
       $this->j133_arealote = ($this->j133_arealote == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_arealote"]:$this->j133_arealote);
       $this->j133_areareallote = ($this->j133_areareallote == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_areareallote"]:$this->j133_areareallote);
       $this->j133_areaconstruida = ($this->j133_areaconstruida == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_areaconstruida"]:$this->j133_areaconstruida);
       $this->j133_arearealconstruida = ($this->j133_arearealconstruida == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_arearealconstruida"]:$this->j133_arearealconstruida);
       $this->j133_numhabite = ($this->j133_numhabite == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_numhabite"]:$this->j133_numhabite);
       $this->j133_dathabite = ($this->j133_dathabite == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_dathabite"]:$this->j133_dathabite);
       if($this->j133_dtprocesso == ""){
         $this->j133_dtprocesso_dia = ($this->j133_dtprocesso_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_dtprocesso_dia"]:$this->j133_dtprocesso_dia);
         $this->j133_dtprocesso_mes = ($this->j133_dtprocesso_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_dtprocesso_mes"]:$this->j133_dtprocesso_mes);
         $this->j133_dtprocesso_ano = ($this->j133_dtprocesso_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_dtprocesso_ano"]:$this->j133_dtprocesso_ano);
         if($this->j133_dtprocesso_dia != ""){
            $this->j133_dtprocesso = $this->j133_dtprocesso_ano."-".$this->j133_dtprocesso_mes."-".$this->j133_dtprocesso_dia;
         }
       }
     }else{
       $this->j133_sequencial = ($this->j133_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j133_sequencial"]:$this->j133_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($j133_sequencial){
      $this->atualizacampos();
     if($this->j133_db_usuarios == null ){
       $this->erro_sql = " Campo Usuário nao Informado.";
       $this->erro_campo = "j133_db_usuarios";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j133_matric == null ){
       $this->erro_sql = " Campo Matrícula nao Informado.";
       $this->erro_campo = "j133_matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j133_iptuconstr == null ){
       $this->erro_sql = " Campo Construção nao Informado.";
       $this->erro_campo = "j133_iptuconstr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j133_data == null ){
       $this->erro_sql = " Campo Data nao Informado.";
       $this->erro_campo = "j133_data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j133_hora == null ){
       $this->erro_sql = " Campo Hora nao Informado.";
       $this->erro_campo = "j133_hora";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j133_dtprocesso == null ){
       $this->j133_dtprocesso = "null";
     }
     if($this->j133_arealote == null ){
       $this->j133_arealote = "null";
     }
     if($this->j133_areareallote == null ){
       $this->j133_areareallote = "null";
     }
     if($this->j133_areaconstruida == null ){
       $this->j133_areaconstruida = "null";
     }
     if($this->j133_arearealconstruida == null ){
       $this->j133_arearealconstruida = "null";
     }
     if($this->j133_numhabite == null ){
       $this->j133_numhabite = "";
     }
     if($this->j133_dathabite == null || $this->j133_dathabite == "--"){
       $this->j133_dathabite = "null";
     }
     if($j133_sequencial == "" || $j133_sequencial == null ){
       $result = db_query("select nextval('certidaoexistencia_j133_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: certidaoexistencia_j133_sequencial_seq do campo: j133_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->j133_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from certidaoexistencia_j133_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $j133_sequencial)){
         $this->erro_sql = " Campo j133_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j133_sequencial = $j133_sequencial;
       }
     }
     if(($this->j133_sequencial == null) || ($this->j133_sequencial == "") ){
       $this->erro_sql = " Campo j133_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into certidaoexistencia(
                                       j133_sequencial
                                      ,j133_db_usuarios
                                      ,j133_matric
                                      ,j133_iptuconstr
                                      ,j133_data
                                      ,j133_hora
                                      ,j133_arquivo
                                      ,j133_observacao
                                      ,j133_processo
                                      ,j133_titulaprocesso
                                      ,j133_dtprocesso
                                      ,j133_arealote
                                      ,j133_areareallote
                                      ,j133_areaconstruida
                                      ,j133_arearealconstruida
                                      ,j133_numhabite
                                      ,j133_dathabite
                       )
                values (
                                $this->j133_sequencial
                               ,$this->j133_db_usuarios
                               ,$this->j133_matric
                               ,$this->j133_iptuconstr
                               ,".($this->j133_data == "null" || $this->j133_data == ""?"null":"'".$this->j133_data."'")."
                               ,'$this->j133_hora'
                               ,$this->j133_arquivo
                               ,'$this->j133_observacao'
                               ,'$this->j133_processo'
                               ,'$this->j133_titulaprocesso'
                               ,".($this->j133_dtprocesso == "null" || $this->j133_dtprocesso == ""?"null":"'".$this->j133_dtprocesso."'")."
                               ,$this->j133_arealote
                               ,$this->j133_areareallote
                               ,$this->j133_areaconstruida
                               ,$this->j133_arearealconstruida
                               ,'$this->j133_numhabite'
                               ,".($this->j133_dathabite == "null" || $this->j133_dathabite == "" ?"null":"'".$this->j133_dathabite."'")."
                        )";
     //echo $sql;
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "certidão de existência ($this->j133_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "certidão de existência já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "certidão de existência ($this->j133_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j133_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->j133_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,18843,'$this->j133_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,3341,18843,'','".AddSlashes(pg_result($resaco,0,'j133_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18845,'','".AddSlashes(pg_result($resaco,0,'j133_db_usuarios'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18846,'','".AddSlashes(pg_result($resaco,0,'j133_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18848,'','".AddSlashes(pg_result($resaco,0,'j133_iptuconstr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18849,'','".AddSlashes(pg_result($resaco,0,'j133_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18850,'','".AddSlashes(pg_result($resaco,0,'j133_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18851,'','".AddSlashes(pg_result($resaco,0,'j133_arquivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18852,'','".AddSlashes(pg_result($resaco,0,'j133_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18853,'','".AddSlashes(pg_result($resaco,0,'j133_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18854,'','".AddSlashes(pg_result($resaco,0,'j133_titulaprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3341,18855,'','".AddSlashes(pg_result($resaco,0,'j133_dtprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($j133_sequencial=null) {
      $this->atualizacampos();
     $sql = " update certidaoexistencia set ";
     $virgula = "";
     if(trim($this->j133_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_sequencial"])){
       $sql  .= $virgula." j133_sequencial = $this->j133_sequencial ";
       $virgula = ",";
       if(trim($this->j133_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "j133_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j133_db_usuarios)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_db_usuarios"])){
       $sql  .= $virgula." j133_db_usuarios = $this->j133_db_usuarios ";
       $virgula = ",";
       if(trim($this->j133_db_usuarios) == null ){
         $this->erro_sql = " Campo Usuário nao Informado.";
         $this->erro_campo = "j133_db_usuarios";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j133_matric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_matric"])){
       $sql  .= $virgula." j133_matric = $this->j133_matric ";
       $virgula = ",";
       if(trim($this->j133_matric) == null ){
         $this->erro_sql = " Campo Matrícula nao Informado.";
         $this->erro_campo = "j133_matric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j133_iptuconstr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_iptuconstr"])){
       $sql  .= $virgula." j133_iptuconstr = $this->j133_iptuconstr ";
       $virgula = ",";
       if(trim($this->j133_iptuconstr) == null ){
         $this->erro_sql = " Campo Construção nao Informado.";
         $this->erro_campo = "j133_iptuconstr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j133_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["j133_data_dia"] !="") ){
       $sql  .= $virgula." j133_data = '$this->j133_data' ";
       $virgula = ",";
       if(trim($this->j133_data) == null ){
         $this->erro_sql = " Campo Data nao Informado.";
         $this->erro_campo = "j133_data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["j133_data_dia"])){
         $sql  .= $virgula." j133_data = null ";
         $virgula = ",";
         if(trim($this->j133_data) == null ){
           $this->erro_sql = " Campo Data nao Informado.";
           $this->erro_campo = "j133_data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->j133_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_hora"])){
       $sql  .= $virgula." j133_hora = '$this->j133_hora' ";
       $virgula = ",";
       if(trim($this->j133_hora) == null ){
         $this->erro_sql = " Campo Hora nao Informado.";
         $this->erro_campo = "j133_hora";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j133_arquivo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_arquivo"])){
       $sql  .= $virgula." j133_arquivo = $this->j133_arquivo ";
       $virgula = ",";
     }
     if(trim($this->j133_observacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_observacao"])){
       $sql  .= $virgula." j133_observacao = '$this->j133_observacao' ";
       $virgula = ",";
     }
     if(trim($this->j133_processo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_processo"])){
       $sql  .= $virgula." j133_processo = '$this->j133_processo' ";
       $virgula = ",";
     }
     if(trim($this->j133_titulaprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_titulaprocesso"])){
       $sql  .= $virgula." j133_titulaprocesso = '$this->j133_titulaprocesso' ";
       $virgula = ",";
     }
     if(trim($this->j133_dtprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j133_dtprocesso_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["j133_dtprocesso_dia"] !="") ){
       $sql  .= $virgula." j133_dtprocesso = '$this->j133_dtprocesso' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["j133_dtprocesso_dia"])){
         $sql  .= $virgula." j133_dtprocesso = null ";
         $virgula = ",";
       }
     }
     $sql .= " where ";
     if($j133_sequencial!=null){
       $sql .= " j133_sequencial = $this->j133_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->j133_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18843,'$this->j133_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_sequencial"]) || $this->j133_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,3341,18843,'".AddSlashes(pg_result($resaco,$conresaco,'j133_sequencial'))."','$this->j133_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_db_usuarios"]) || $this->j133_db_usuarios != "")
           $resac = db_query("insert into db_acount values($acount,3341,18845,'".AddSlashes(pg_result($resaco,$conresaco,'j133_db_usuarios'))."','$this->j133_db_usuarios',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_matric"]) || $this->j133_matric != "")
           $resac = db_query("insert into db_acount values($acount,3341,18846,'".AddSlashes(pg_result($resaco,$conresaco,'j133_matric'))."','$this->j133_matric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_iptuconstr"]) || $this->j133_iptuconstr != "")
           $resac = db_query("insert into db_acount values($acount,3341,18848,'".AddSlashes(pg_result($resaco,$conresaco,'j133_iptuconstr'))."','$this->j133_iptuconstr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_data"]) || $this->j133_data != "")
           $resac = db_query("insert into db_acount values($acount,3341,18849,'".AddSlashes(pg_result($resaco,$conresaco,'j133_data'))."','$this->j133_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_hora"]) || $this->j133_hora != "")
           $resac = db_query("insert into db_acount values($acount,3341,18850,'".AddSlashes(pg_result($resaco,$conresaco,'j133_hora'))."','$this->j133_hora',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_arquivo"]) || $this->j133_arquivo != "")
           $resac = db_query("insert into db_acount values($acount,3341,18851,'".AddSlashes(pg_result($resaco,$conresaco,'j133_arquivo'))."','$this->j133_arquivo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_observacao"]) || $this->j133_observacao != "")
           $resac = db_query("insert into db_acount values($acount,3341,18852,'".AddSlashes(pg_result($resaco,$conresaco,'j133_observacao'))."','$this->j133_observacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_processo"]) || $this->j133_processo != "")
           $resac = db_query("insert into db_acount values($acount,3341,18853,'".AddSlashes(pg_result($resaco,$conresaco,'j133_processo'))."','$this->j133_processo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_titulaprocesso"]) || $this->j133_titulaprocesso != "")
           $resac = db_query("insert into db_acount values($acount,3341,18854,'".AddSlashes(pg_result($resaco,$conresaco,'j133_titulaprocesso'))."','$this->j133_titulaprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j133_dtprocesso"]) || $this->j133_dtprocesso != "")
           $resac = db_query("insert into db_acount values($acount,3341,18855,'".AddSlashes(pg_result($resaco,$conresaco,'j133_dtprocesso'))."','$this->j133_dtprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     //echo $sql;
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "certidão de existência nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j133_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "certidão de existência nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j133_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j133_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($j133_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($j133_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18843,'$j133_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,3341,18843,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18845,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_db_usuarios'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18846,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18848,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_iptuconstr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18849,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18850,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18851,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_arquivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18852,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18853,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18854,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_titulaprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3341,18855,'','".AddSlashes(pg_result($resaco,$iresaco,'j133_dtprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from certidaoexistencia
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($j133_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " j133_sequencial = $j133_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "certidão de existência nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j133_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "certidão de existência nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j133_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$j133_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:certidaoexistencia";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $j133_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from certidaoexistencia ";
     $sql .= "      inner join iptuconstr  on  iptuconstr.j39_matric = certidaoexistencia.j133_matric and  iptuconstr.j39_idcons = certidaoexistencia.j133_iptuconstr";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = certidaoexistencia.j133_db_usuarios";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = iptuconstr.j39_codigo";
     $sql .= "      inner join iptubase  on  iptubase.j01_matric = iptuconstr.j39_matric";
     $sql2 = "";
     if($dbwhere==""){
       if($j133_sequencial!=null ){
         $sql2 .= " where certidaoexistencia.j133_sequencial = $j133_sequencial ";
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
   public function sql_query_file ( $j133_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from certidaoexistencia ";
     $sql2 = "";
     if($dbwhere==""){
       if($j133_sequencial!=null ){
         $sql2 .= " where certidaoexistencia.j133_sequencial = $j133_sequencial ";
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

   /**
    * metodo externo para retornar dados da certidao e do processo
    * @param integer $iCodigoCertidao
    * @param string $sCampos
    * @param string $sWhere
    */
   public function sql_queryDadosCertidao( $iCodigoCertidao, $sCampos = "*", $sWhere = "" ) {


     $sql  = " SELECT $sCampos  FROM certidaoexistencia ";
     $sql .= " LEFT JOIN certidaoexistenciaprotprocesso ON certidaoexistencia.j133_sequencial = certidaoexistenciaprotprocesso.j134_certidaoexistencia ";
     $sql2 = "";

     if ($sWhere == "") {

       if ($iCodigoCertidao != null ) {
         $sql2 .= " WHERE certidaoexistencia.j133_sequencial = $iCodigoCertidao ";
       }

     } else if ($sWhere != "") {
       $sql2 = " WHERE $sWhere";
     }
     $sql .= $sql2;

     return $sql;

   }

}
