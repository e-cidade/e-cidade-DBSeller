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

//MODULO: escola
//CLASSE DA ENTIDADE parametrodependencia
class cl_parametrodependencia {
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
   var $ed295_sequencial = 0;
   var $ed295_habilitaprogressao = 0;
   var $ed295_qtddiscdependente = 0;
   var $ed295_controledependencia = 0;
   var $ed295_controlefreq = 0;
   var $ed295_disceliminadep = 0;
   var $ed295_escola = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 ed295_sequencial = int4 = Sequencial
                 ed295_habilitaprogressao = int4 = Habilitar Progressão Parcial/Dependência
                 ed295_qtddiscdependente = int4 = Quantidade de Disciplinas Dependentes
                 ed295_controledependencia = int4 = Controle da Dependência
                 ed295_controlefreq = int4 = Controle da Frequência
                 ed295_disceliminadep = int4 = Disciplina Aprovada Elimina Dependência
                 ed295_escola = int4 = Escola
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("parametrodependencia");
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
       $this->ed295_sequencial = ($this->ed295_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_sequencial"]:$this->ed295_sequencial);
       $this->ed295_habilitaprogressao = ($this->ed295_habilitaprogressao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_habilitaprogressao"]:$this->ed295_habilitaprogressao);
       $this->ed295_qtddiscdependente = ($this->ed295_qtddiscdependente == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_qtddiscdependente"]:$this->ed295_qtddiscdependente);
       $this->ed295_controledependencia = ($this->ed295_controledependencia == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_controledependencia"]:$this->ed295_controledependencia);
       $this->ed295_controlefreq = ($this->ed295_controlefreq == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_controlefreq"]:$this->ed295_controlefreq);
       $this->ed295_disceliminadep = ($this->ed295_disceliminadep == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_disceliminadep"]:$this->ed295_disceliminadep);
       $this->ed295_escola = ($this->ed295_escola == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_escola"]:$this->ed295_escola);
     }else{
       $this->ed295_sequencial = ($this->ed295_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed295_sequencial"]:$this->ed295_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($ed295_sequencial){
      $this->atualizacampos();
     if($this->ed295_habilitaprogressao == null ){
       $this->erro_sql = " Campo Habilitar Progressão Parcial/Dependência nao Informado.";
       $this->erro_campo = "ed295_habilitaprogressao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed295_qtddiscdependente == null ){
       $this->erro_sql = " Campo Quantidade de Disciplinas Dependentes nao Informado.";
       $this->erro_campo = "ed295_qtddiscdependente";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed295_controledependencia == null ){
       $this->erro_sql = " Campo Controle da Dependência nao Informado.";
       $this->erro_campo = "ed295_controledependencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed295_controlefreq == null ){
       $this->erro_sql = " Campo Controle da Frequência nao Informado.";
       $this->erro_campo = "ed295_controlefreq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed295_disceliminadep == null ){
       $this->erro_sql = " Campo Disciplina Aprovada Elimina Dependência nao Informado.";
       $this->erro_campo = "ed295_disceliminadep";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed295_escola == null ){
       $this->erro_sql = " Campo Escola nao Informado.";
       $this->erro_campo = "ed295_escola";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed295_sequencial == "" || $ed295_sequencial == null ){
       $result = db_query("select nextval('parametrodependencia_ed295_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: parametrodependencia_ed295_sequencial_seq do campo: ed295_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ed295_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from parametrodependencia_ed295_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed295_sequencial)){
         $this->erro_sql = " Campo ed295_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed295_sequencial = $ed295_sequencial;
       }
     }
     if(($this->ed295_sequencial == null) || ($this->ed295_sequencial == "") ){
       $this->erro_sql = " Campo ed295_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into parametrodependencia(
                                       ed295_sequencial
                                      ,ed295_habilitaprogressao
                                      ,ed295_qtddiscdependente
                                      ,ed295_controledependencia
                                      ,ed295_controlefreq
                                      ,ed295_disceliminadep
                                      ,ed295_escola
                       )
                values (
                                $this->ed295_sequencial
                               ,$this->ed295_habilitaprogressao
                               ,$this->ed295_qtddiscdependente
                               ,$this->ed295_controledependencia
                               ,$this->ed295_controlefreq
                               ,$this->ed295_disceliminadep
                               ,$this->ed295_escola
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "parametro dependencia ($this->ed295_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "parametro dependencia já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "parametro dependencia ($this->ed295_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed295_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->ed295_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,18514,'$this->ed295_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,3275,18514,'','".AddSlashes(pg_result($resaco,0,'ed295_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3275,18515,'','".AddSlashes(pg_result($resaco,0,'ed295_habilitaprogressao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3275,18516,'','".AddSlashes(pg_result($resaco,0,'ed295_qtddiscdependente'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3275,18517,'','".AddSlashes(pg_result($resaco,0,'ed295_controledependencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3275,18519,'','".AddSlashes(pg_result($resaco,0,'ed295_controlefreq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3275,18520,'','".AddSlashes(pg_result($resaco,0,'ed295_disceliminadep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3275,18521,'','".AddSlashes(pg_result($resaco,0,'ed295_escola'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($ed295_sequencial=null) {
      $this->atualizacampos();
     $sql = " update parametrodependencia set ";
     $virgula = "";
     if(trim($this->ed295_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed295_sequencial"])){
       $sql  .= $virgula." ed295_sequencial = $this->ed295_sequencial ";
       $virgula = ",";
       if(trim($this->ed295_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "ed295_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed295_habilitaprogressao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed295_habilitaprogressao"])){
       $sql  .= $virgula." ed295_habilitaprogressao = $this->ed295_habilitaprogressao ";
       $virgula = ",";
       if(trim($this->ed295_habilitaprogressao) == null ){
         $this->erro_sql = " Campo Habilitar Progressão Parcial/Dependência nao Informado.";
         $this->erro_campo = "ed295_habilitaprogressao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed295_qtddiscdependente)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed295_qtddiscdependente"])){
       $sql  .= $virgula." ed295_qtddiscdependente = $this->ed295_qtddiscdependente ";
       $virgula = ",";
       if(trim($this->ed295_qtddiscdependente) == null ){
         $this->erro_sql = " Campo Quantidade de Disciplinas Dependentes nao Informado.";
         $this->erro_campo = "ed295_qtddiscdependente";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed295_controledependencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed295_controledependencia"])){
       $sql  .= $virgula." ed295_controledependencia = $this->ed295_controledependencia ";
       $virgula = ",";
       if(trim($this->ed295_controledependencia) == null ){
         $this->erro_sql = " Campo Controle da Dependência nao Informado.";
         $this->erro_campo = "ed295_controledependencia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed295_controlefreq)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed295_controlefreq"])){
       $sql  .= $virgula." ed295_controlefreq = $this->ed295_controlefreq ";
       $virgula = ",";
       if(trim($this->ed295_controlefreq) == null ){
         $this->erro_sql = " Campo Controle da Frequência nao Informado.";
         $this->erro_campo = "ed295_controlefreq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed295_disceliminadep)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed295_disceliminadep"])){
       $sql  .= $virgula." ed295_disceliminadep = $this->ed295_disceliminadep ";
       $virgula = ",";
       if(trim($this->ed295_disceliminadep) == null ){
         $this->erro_sql = " Campo Disciplina Aprovada Elimina Dependência nao Informado.";
         $this->erro_campo = "ed295_disceliminadep";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed295_escola)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed295_escola"])){
       $sql  .= $virgula." ed295_escola = $this->ed295_escola ";
       $virgula = ",";
       if(trim($this->ed295_escola) == null ){
         $this->erro_sql = " Campo Escola nao Informado.";
         $this->erro_campo = "ed295_escola";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed295_sequencial!=null){
       $sql .= " ed295_sequencial = $this->ed295_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->ed295_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18514,'$this->ed295_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed295_sequencial"]) || $this->ed295_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,3275,18514,'".AddSlashes(pg_result($resaco,$conresaco,'ed295_sequencial'))."','$this->ed295_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed295_habilitaprogressao"]) || $this->ed295_habilitaprogressao != "")
           $resac = db_query("insert into db_acount values($acount,3275,18515,'".AddSlashes(pg_result($resaco,$conresaco,'ed295_habilitaprogressao'))."','$this->ed295_habilitaprogressao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed295_qtddiscdependente"]) || $this->ed295_qtddiscdependente != "")
           $resac = db_query("insert into db_acount values($acount,3275,18516,'".AddSlashes(pg_result($resaco,$conresaco,'ed295_qtddiscdependente'))."','$this->ed295_qtddiscdependente',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed295_controledependencia"]) || $this->ed295_controledependencia != "")
           $resac = db_query("insert into db_acount values($acount,3275,18517,'".AddSlashes(pg_result($resaco,$conresaco,'ed295_controledependencia'))."','$this->ed295_controledependencia',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed295_controlefreq"]) || $this->ed295_controlefreq != "")
           $resac = db_query("insert into db_acount values($acount,3275,18519,'".AddSlashes(pg_result($resaco,$conresaco,'ed295_controlefreq'))."','$this->ed295_controlefreq',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed295_disceliminadep"]) || $this->ed295_disceliminadep != "")
           $resac = db_query("insert into db_acount values($acount,3275,18520,'".AddSlashes(pg_result($resaco,$conresaco,'ed295_disceliminadep'))."','$this->ed295_disceliminadep',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed295_escola"]) || $this->ed295_escola != "")
           $resac = db_query("insert into db_acount values($acount,3275,18521,'".AddSlashes(pg_result($resaco,$conresaco,'ed295_escola'))."','$this->ed295_escola',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "parametro dependencia nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed295_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "parametro dependencia nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed295_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed295_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($ed295_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($ed295_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,18514,'$ed295_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,3275,18514,'','".AddSlashes(pg_result($resaco,$iresaco,'ed295_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3275,18515,'','".AddSlashes(pg_result($resaco,$iresaco,'ed295_habilitaprogressao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3275,18516,'','".AddSlashes(pg_result($resaco,$iresaco,'ed295_qtddiscdependente'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3275,18517,'','".AddSlashes(pg_result($resaco,$iresaco,'ed295_controledependencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3275,18519,'','".AddSlashes(pg_result($resaco,$iresaco,'ed295_controlefreq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3275,18520,'','".AddSlashes(pg_result($resaco,$iresaco,'ed295_disceliminadep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3275,18521,'','".AddSlashes(pg_result($resaco,$iresaco,'ed295_escola'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from parametrodependencia
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($ed295_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " ed295_sequencial = $ed295_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "parametro dependencia nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed295_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "parametro dependencia nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed295_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$ed295_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:parametrodependencia";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $ed295_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from parametrodependencia ";
     $sql .= "      inner join escola  on  escola.ed18_i_codigo = parametrodependencia.ed295_escola";
    // $sql .= "      inner join bairro  on  bairro.j13_codi = escola.ed18_i_bairro";
    // $sql .= "      inner join ruas  on  ruas.j14_codigo = escola.ed18_i_rua";
    // $sql .= "      inner join db_depart  on  db_depart.coddepto = escola.ed18_i_codigo";
    // $sql .= "      inner join censouf  on  censouf.ed260_i_codigo = escola.ed18_i_censouf";
    // $sql .= "      inner join censomunic  on  censomunic.ed261_i_codigo = escola.ed18_i_censomunic";
     //$sql .= "      inner join censodistrito  on  censodistrito.ed262_i_codigo = escola.ed18_i_censodistrito";
     //$sql .= "      left  join  escola as a on  a.ed263_i_codigo = escola.ed18_i_censoorgreg";
     //$sql .= "      left  join censolinguaindig  on  censolinguaindig.ed264_i_codigo = escola.ed18_i_linguaindigena";
     $sql2 = "";
     if($dbwhere==""){
       if($ed295_sequencial!=null ){
         $sql2 .= " where parametrodependencia.ed295_sequencial = $ed295_sequencial ";
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
   public function sql_query_file ( $ed295_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from parametrodependencia ";
     $sql2 = "";
     if($dbwhere==""){
       if($ed295_sequencial!=null ){
         $sql2 .= " where parametrodependencia.ed295_sequencial = $ed295_sequencial ";
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
