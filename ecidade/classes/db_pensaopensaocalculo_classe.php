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

//MODULO: pessoal
//CLASSE DA ENTIDADE pensaopensaocalculo
class cl_pensaopensaocalculo {
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
   var $rh118_sequencial = 0;
   var $rh118_anousu = 0;
   var $rh118_mesusu = 0;
   var $rh118_regist = 0;
   var $rh118_numcgm = 0;
   var $rh118_pensaocalculo = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 rh118_sequencial = int4 = Sequencial
                 rh118_anousu = int4 = Ano do Calculo da Folha
                 rh118_mesusu = int4 = Mes de Calculo da Pensao
                 rh118_regist = int4 = Matricula do Servidor
                 rh118_numcgm = int4 = CGM Pensionista
                 rh118_pensaocalculo = int4 = Faixa de Cálculo
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("pensaopensaocalculo");
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
       $this->rh118_sequencial = ($this->rh118_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh118_sequencial"]:$this->rh118_sequencial);
       $this->rh118_anousu = ($this->rh118_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["rh118_anousu"]:$this->rh118_anousu);
       $this->rh118_mesusu = ($this->rh118_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["rh118_mesusu"]:$this->rh118_mesusu);
       $this->rh118_regist = ($this->rh118_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["rh118_regist"]:$this->rh118_regist);
       $this->rh118_numcgm = ($this->rh118_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["rh118_numcgm"]:$this->rh118_numcgm);
       $this->rh118_pensaocalculo = ($this->rh118_pensaocalculo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh118_pensaocalculo"]:$this->rh118_pensaocalculo);
     }else{
       $this->rh118_sequencial = ($this->rh118_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh118_sequencial"]:$this->rh118_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($rh118_sequencial){
      $this->atualizacampos();
     if($this->rh118_anousu == null ){
       $this->erro_sql = " Campo Ano do Calculo da Folha nao Informado.";
       $this->erro_campo = "rh118_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh118_mesusu == null ){
       $this->erro_sql = " Campo Mes de Calculo da Pensao nao Informado.";
       $this->erro_campo = "rh118_mesusu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh118_regist == null ){
       $this->erro_sql = " Campo Matricula do Servidor nao Informado.";
       $this->erro_campo = "rh118_regist";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh118_numcgm == null ){
       $this->erro_sql = " Campo CGM Pensionista nao Informado.";
       $this->erro_campo = "rh118_numcgm";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh118_pensaocalculo == null ){
       $this->erro_sql = " Campo Faixa de Cálculo nao Informado.";
       $this->erro_campo = "rh118_pensaocalculo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($rh118_sequencial == "" || $rh118_sequencial == null ){
       $result = db_query("select nextval('pensaopensaocalculo_rh118_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: pensaopensaocalculo_rh118_sequencial_seq do campo: rh118_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->rh118_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from pensaopensaocalculo_rh118_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $rh118_sequencial)){
         $this->erro_sql = " Campo rh118_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->rh118_sequencial = $rh118_sequencial;
       }
     }
     if(($this->rh118_sequencial == null) || ($this->rh118_sequencial == "") ){
       $this->erro_sql = " Campo rh118_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into pensaopensaocalculo(
                                       rh118_sequencial
                                      ,rh118_anousu
                                      ,rh118_mesusu
                                      ,rh118_regist
                                      ,rh118_numcgm
                                      ,rh118_pensaocalculo
                       )
                values (
                                $this->rh118_sequencial
                               ,$this->rh118_anousu
                               ,$this->rh118_mesusu
                               ,$this->rh118_regist
                               ,$this->rh118_numcgm
                               ,$this->rh118_pensaocalculo
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "rh118 ($this->rh118_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "rh118 já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "rh118 ($this->rh118_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->rh118_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->rh118_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,19685,'$this->rh118_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,3498,19685,'','".AddSlashes(pg_result($resaco,0,'rh118_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3498,19688,'','".AddSlashes(pg_result($resaco,0,'rh118_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3498,19689,'','".AddSlashes(pg_result($resaco,0,'rh118_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3498,19690,'','".AddSlashes(pg_result($resaco,0,'rh118_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3498,19691,'','".AddSlashes(pg_result($resaco,0,'rh118_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3498,19687,'','".AddSlashes(pg_result($resaco,0,'rh118_pensaocalculo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($rh118_sequencial=null) {
      $this->atualizacampos();
     $sql = " update pensaopensaocalculo set ";
     $virgula = "";
     if(trim($this->rh118_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh118_sequencial"])){
       $sql  .= $virgula." rh118_sequencial = $this->rh118_sequencial ";
       $virgula = ",";
       if(trim($this->rh118_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "rh118_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh118_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh118_anousu"])){
       $sql  .= $virgula." rh118_anousu = $this->rh118_anousu ";
       $virgula = ",";
       if(trim($this->rh118_anousu) == null ){
         $this->erro_sql = " Campo Ano do Calculo da Folha nao Informado.";
         $this->erro_campo = "rh118_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh118_mesusu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh118_mesusu"])){
       $sql  .= $virgula." rh118_mesusu = $this->rh118_mesusu ";
       $virgula = ",";
       if(trim($this->rh118_mesusu) == null ){
         $this->erro_sql = " Campo Mes de Calculo da Pensao nao Informado.";
         $this->erro_campo = "rh118_mesusu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh118_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh118_regist"])){
       $sql  .= $virgula." rh118_regist = $this->rh118_regist ";
       $virgula = ",";
       if(trim($this->rh118_regist) == null ){
         $this->erro_sql = " Campo Matricula do Servidor nao Informado.";
         $this->erro_campo = "rh118_regist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh118_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh118_numcgm"])){
       $sql  .= $virgula." rh118_numcgm = $this->rh118_numcgm ";
       $virgula = ",";
       if(trim($this->rh118_numcgm) == null ){
         $this->erro_sql = " Campo CGM Pensionista nao Informado.";
         $this->erro_campo = "rh118_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh118_pensaocalculo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh118_pensaocalculo"])){
       $sql  .= $virgula." rh118_pensaocalculo = $this->rh118_pensaocalculo ";
       $virgula = ",";
       if(trim($this->rh118_pensaocalculo) == null ){
         $this->erro_sql = " Campo Faixa de Cálculo nao Informado.";
         $this->erro_campo = "rh118_pensaocalculo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($rh118_sequencial!=null){
       $sql .= " rh118_sequencial = $this->rh118_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->rh118_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,19685,'$this->rh118_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["rh118_sequencial"]) || $this->rh118_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,3498,19685,'".AddSlashes(pg_result($resaco,$conresaco,'rh118_sequencial'))."','$this->rh118_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["rh118_anousu"]) || $this->rh118_anousu != "")
           $resac = db_query("insert into db_acount values($acount,3498,19688,'".AddSlashes(pg_result($resaco,$conresaco,'rh118_anousu'))."','$this->rh118_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["rh118_mesusu"]) || $this->rh118_mesusu != "")
           $resac = db_query("insert into db_acount values($acount,3498,19689,'".AddSlashes(pg_result($resaco,$conresaco,'rh118_mesusu'))."','$this->rh118_mesusu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["rh118_regist"]) || $this->rh118_regist != "")
           $resac = db_query("insert into db_acount values($acount,3498,19690,'".AddSlashes(pg_result($resaco,$conresaco,'rh118_regist'))."','$this->rh118_regist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["rh118_numcgm"]) || $this->rh118_numcgm != "")
           $resac = db_query("insert into db_acount values($acount,3498,19691,'".AddSlashes(pg_result($resaco,$conresaco,'rh118_numcgm'))."','$this->rh118_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["rh118_pensaocalculo"]) || $this->rh118_pensaocalculo != "")
           $resac = db_query("insert into db_acount values($acount,3498,19687,'".AddSlashes(pg_result($resaco,$conresaco,'rh118_pensaocalculo'))."','$this->rh118_pensaocalculo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "rh118 nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh118_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "rh118 nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh118_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->rh118_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($rh118_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($rh118_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,19685,'$rh118_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,3498,19685,'','".AddSlashes(pg_result($resaco,$iresaco,'rh118_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3498,19688,'','".AddSlashes(pg_result($resaco,$iresaco,'rh118_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3498,19689,'','".AddSlashes(pg_result($resaco,$iresaco,'rh118_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3498,19690,'','".AddSlashes(pg_result($resaco,$iresaco,'rh118_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3498,19691,'','".AddSlashes(pg_result($resaco,$iresaco,'rh118_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3498,19687,'','".AddSlashes(pg_result($resaco,$iresaco,'rh118_pensaocalculo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from pensaopensaocalculo
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($rh118_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " rh118_sequencial = $rh118_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "rh118 nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh118_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "rh118 nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh118_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$rh118_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:pensaopensaocalculo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $rh118_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from pensaopensaocalculo ";
     $sql .= "      inner join pensao  on  pensao.r52_anousu = pensaopensaocalculo.rh118_anousu and  pensao.r52_mesusu = pensaopensaocalculo.rh118_mesusu and  pensao.r52_regist = pensaopensaocalculo.rh118_regist and  pensao.r52_numcgm = pensaopensaocalculo.rh118_numcgm";
     $sql .= "      inner join pensaocalculo  on  pensaocalculo.rh117_sequencial = pensaopensaocalculo.rh118_pensaocalculo";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = pensao.r52_numcgm";
     $sql .= "      inner join pessoal  on  pessoal.r01_anousu = pensao.r52_anousu and  pessoal.r01_mesusu = pensao.r52_mesusu and  pessoal.r01_regist = pensao.r52_regist";
     $sql .= "      inner join rhrubricas  on  rhrubricas.rh27_rubric = pensaocalculo.rh117_rubrica and  rhrubricas.rh27_instit = pensaocalculo.rh117_instit";
     $sql2 = "";
     if($dbwhere==""){
       if($rh118_sequencial!=null ){
         $sql2 .= " where pensaopensaocalculo.rh118_sequencial = $rh118_sequencial ";
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
   public function sql_query_file ( $rh118_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from pensaopensaocalculo ";
     $sql2 = "";
     if($dbwhere==""){
       if($rh118_sequencial!=null ){
         $sql2 .= " where pensaopensaocalculo.rh118_sequencial = $rh118_sequencial ";
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
