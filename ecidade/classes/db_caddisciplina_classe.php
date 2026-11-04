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
//CLASSE DA ENTIDADE caddisciplina
class cl_caddisciplina {
   // cria publiciaveis de erro
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
   // cria publiciaveis do arquivo
   public $ed232_i_codigo = 0;
   public $ed232_areaconhecimento = null;
   public $ed232_c_descr = null;
   public $ed232_c_abrev = null;
   public $ed232_c_descrcompleta = null;
   public $ed232_corhtml = null;
   // cria propriedade com as publiciaveis do arquivo
   public $campos = "
                 ed232_i_codigo = int8 = C�digo
                 ed232_areaconhecimento = int4 = �rea de Conhecimento
                 ed232_c_descr = char(30) = Descri��o
                 ed232_c_abrev = char(10) = Abreviatura
                 ed232_c_descrcompleta = publicchar(150) = Descri��o Completa
                 ed232_corhtml = publicchar(7) = Codigo Hex da Cor
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("caddisciplina");
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
       $this->ed232_i_codigo = ($this->ed232_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed232_i_codigo"]:$this->ed232_i_codigo);
       $this->ed232_areaconhecimento = ($this->ed232_areaconhecimento == ""?@$GLOBALS["HTTP_POST_VARS"]["ed232_areaconhecimento"]:$this->ed232_areaconhecimento);
       $this->ed232_c_descr = ($this->ed232_c_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["ed232_c_descr"]:$this->ed232_c_descr);
       $this->ed232_c_abrev = ($this->ed232_c_abrev == ""?@$GLOBALS["HTTP_POST_VARS"]["ed232_c_abrev"]:$this->ed232_c_abrev);
       $this->ed232_c_descrcompleta = ($this->ed232_c_descrcompleta == ""?@$GLOBALS["HTTP_POST_VARS"]["ed232_c_descrcompleta"]:$this->ed232_c_descrcompleta);
       $this->ed232_corhtml = ($this->ed232_corhtml == ""?@$GLOBALS["HTTP_POST_VARS"]["ed232_corhtml"]:$this->ed232_corhtml);
     }else{
       $this->ed232_i_codigo = ($this->ed232_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed232_i_codigo"]:$this->ed232_i_codigo);
     }
   }
   // funcao para inclusao
   public function incluir ($ed232_i_codigo){
      $this->atualizacampos();
     if($this->ed232_areaconhecimento == null ){
       $this->ed232_areaconhecimento = "null";
     }
     if($this->ed232_c_descr == null ){
       $this->erro_sql = " Campo Descri��o nao Informado.";
       $this->erro_campo = "ed232_c_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed232_c_abrev == null ){
       $this->erro_sql = " Campo Abreviatura nao Informado.";
       $this->erro_campo = "ed232_c_abrev";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed232_c_descrcompleta == null ){
       $this->erro_sql = " Campo Descri��o Completa nao Informado.";
       $this->erro_campo = "ed232_c_descrcompleta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed232_corhtml == null ){
       $this->erro_sql = " Campo Cor Html nao Informado.";
       $this->erro_campo = "ed232_corhtml";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed232_i_codigo == "" || $ed232_i_codigo == null ){
       $result = db_query("select nextval('caddisciplina_ed232_i_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: caddisciplina_ed232_i_codigo_seq do campo: ed232_i_codigo";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ed232_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from caddisciplina_ed232_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed232_i_codigo)){
         $this->erro_sql = " Campo ed232_i_codigo maior que �ltimo n�mero da sequencia.";
         $this->erro_banco = "Sequencia menor que este n�mero.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed232_i_codigo = $ed232_i_codigo;
       }
     }
     if(($this->ed232_i_codigo == null) || ($this->ed232_i_codigo == "") ){
       $this->erro_sql = " Campo ed232_i_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into caddisciplina(
                                       ed232_i_codigo
                                      ,ed232_areaconhecimento
                                      ,ed232_c_descr
                                      ,ed232_c_abrev
                                      ,ed232_c_descrcompleta
                                      ,ed232_corhtml
                       )
                values (
                                $this->ed232_i_codigo
                               ,$this->ed232_areaconhecimento
                               ,'$this->ed232_c_descr'
                               ,'$this->ed232_c_abrev'
                               ,'$this->ed232_c_descrcompleta'
                               ,'$this->ed232_corhtml'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cadastro de Disciplinas ($this->ed232_i_codigo) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cadastro de Disciplinas j� Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cadastro de Disciplinas ($this->ed232_i_codigo) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed232_i_codigo;
     $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->ed232_i_codigo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,11709,'$this->ed232_i_codigo','I')");
       $resac = db_query("insert into db_acount values($acount,2017,11709,'','".AddSlashes(pg_result($resaco,0,'ed232_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2017,18435,'','".AddSlashes(pg_result($resaco,0,'ed232_areaconhecimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2017,11710,'','".AddSlashes(pg_result($resaco,0,'ed232_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2017,11711,'','".AddSlashes(pg_result($resaco,0,'ed232_c_abrev'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2017,19242,'','".AddSlashes(pg_result($resaco,0,'ed232_c_descrcompleta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2017,1014611,'','".AddSlashes(pg_result($resaco,0,'ed232_corhtml'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($ed232_i_codigo=null) {
      $this->atualizacampos();
     $sql = " update caddisciplina set ";
     $virgula = "";
     if(trim($this->ed232_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed232_i_codigo"])){
       $sql  .= $virgula." ed232_i_codigo = $this->ed232_i_codigo ";
       $virgula = ",";
       if(trim($this->ed232_i_codigo) == null ){
         $this->erro_sql = " Campo C�digo nao Informado.";
         $this->erro_campo = "ed232_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed232_areaconhecimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed232_areaconhecimento"])){
        if(trim($this->ed232_areaconhecimento)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ed232_areaconhecimento"])){
           $this->ed232_areaconhecimento = "null" ;
        }
       $sql  .= $virgula." ed232_areaconhecimento = $this->ed232_areaconhecimento ";
       $virgula = ",";
     }
     if(trim($this->ed232_c_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed232_c_descr"])){
       $sql  .= $virgula." ed232_c_descr = '$this->ed232_c_descr' ";
       $virgula = ",";
       if(trim($this->ed232_c_descr) == null ){
         $this->erro_sql = " Campo Descri��o nao Informado.";
         $this->erro_campo = "ed232_c_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed232_c_abrev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed232_c_abrev"])){
       $sql  .= $virgula." ed232_c_abrev = '$this->ed232_c_abrev' ";
       $virgula = ",";
       if(trim($this->ed232_c_abrev) == null ){
         $this->erro_sql = " Campo Abreviatura nao Informado.";
         $this->erro_campo = "ed232_c_abrev";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed232_c_descrcompleta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed232_c_descrcompleta"])){
       $sql  .= $virgula." ed232_c_descrcompleta = '$this->ed232_c_descrcompleta' ";
       $virgula = ",";
       if(trim($this->ed232_c_descrcompleta) == null ){
         $this->erro_sql = " Campo Descri��o Completa nao Informado.";
         $this->erro_campo = "ed232_c_descrcompleta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed232_corhtml)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed232_corhtml"])){
       $sql  .= $virgula." ed232_corhtml = '$this->ed232_corhtml' ";
       $virgula = ",";
       if(trim($this->ed232_c_descrcompleta) == null ){
         $this->erro_sql = " Campo Cor Html nao Informado.";
         $this->erro_campo = "ed232_corhtml";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed232_i_codigo!=null){
       $sql .= " ed232_i_codigo = $this->ed232_i_codigo";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->ed232_i_codigo));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,11709,'$this->ed232_i_codigo','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed232_i_codigo"]) || $this->ed232_i_codigo != "")
           $resac = db_query("insert into db_acount values($acount,2017,11709,'".AddSlashes(pg_result($resaco,$conresaco,'ed232_i_codigo'))."','$this->ed232_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed232_areaconhecimento"]) || $this->ed232_areaconhecimento != "")
           $resac = db_query("insert into db_acount values($acount,2017,18435,'".AddSlashes(pg_result($resaco,$conresaco,'ed232_areaconhecimento'))."','$this->ed232_areaconhecimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed232_c_descr"]) || $this->ed232_c_descr != "")
           $resac = db_query("insert into db_acount values($acount,2017,11710,'".AddSlashes(pg_result($resaco,$conresaco,'ed232_c_descr'))."','$this->ed232_c_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed232_c_abrev"]) || $this->ed232_c_abrev != "")
           $resac = db_query("insert into db_acount values($acount,2017,11711,'".AddSlashes(pg_result($resaco,$conresaco,'ed232_c_abrev'))."','$this->ed232_c_abrev',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed232_c_descrcompleta"]) || $this->ed232_c_descrcompleta != "")
           $resac = db_query("insert into db_acount values($acount,2017,19242,'".AddSlashes(pg_result($resaco,$conresaco,'ed232_c_descrcompleta'))."','$this->ed232_c_descrcompleta',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ed232_corhtml"]) || $this->ed232_corhtml != "")
           $resac = db_query("insert into db_acount values($acount,2017,1014611,'".AddSlashes(pg_result($resaco,$conresaco,'ed232_corhtml'))."','$this->ed232_corhtml',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Disciplinas nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed232_i_codigo;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Disciplinas nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed232_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Altera��o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ed232_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($ed232_i_codigo=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($ed232_i_codigo));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,11709,'$ed232_i_codigo','E')");
         $resac = db_query("insert into db_acount values($acount,2017,11709,'','".AddSlashes(pg_result($resaco,$iresaco,'ed232_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2017,18435,'','".AddSlashes(pg_result($resaco,$iresaco,'ed232_areaconhecimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2017,11710,'','".AddSlashes(pg_result($resaco,$iresaco,'ed232_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2017,11711,'','".AddSlashes(pg_result($resaco,$iresaco,'ed232_c_abrev'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2017,19242,'','".AddSlashes(pg_result($resaco,$iresaco,'ed232_c_descrcompleta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2017,1014611,'','".AddSlashes(pg_result($resaco,$iresaco,'ed232_corhtml'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from caddisciplina
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($ed232_i_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " ed232_i_codigo = $ed232_i_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Disciplinas nao Exclu�do. Exclus�o Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed232_i_codigo;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Disciplinas nao Encontrado. Exclus�o n�o Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed232_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclus�o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$ed232_i_codigo;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
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
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:caddisciplina";
        $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $ed232_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from caddisciplina ";
     $sql .= "      left  join areaconhecimento  on  areaconhecimento.ed293_sequencial = caddisciplina.ed232_areaconhecimento";
     $sql2 = "";
     if($dbwhere==""){
       if($ed232_i_codigo!=null ){
         $sql2 .= " where caddisciplina.ed232_i_codigo = $ed232_i_codigo ";
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
   public function sql_query_file ( $ed232_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from caddisciplina ";
     $sql2 = "";
     if($dbwhere==""){
       if($ed232_i_codigo!=null ){
         $sql2 .= " where caddisciplina.ed232_i_codigo = $ed232_i_codigo ";
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
   function sql_query_relatorio($iCodigo = null, $sCampos = '*', $sOrdem = null, $sDbWhere = '') {

    $sSql = 'select ';
    if ($sCampos != '*') {

      $sCamposSql = explode('#', $sCampos);
      $sVirgula   = '';
      for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++){

        $sSql .= $sVirgula.$sCamposSql[$iCont];
        $virgula = ",";

      }

    } else {
      $sSql .= $sCampos;
    }

    $sSql .= ' from caddisciplina ';
    $sSql .= ' inner join disciplina on disciplina.ed12_i_caddisciplina = caddisciplina.ed232_i_codigo ';
    $sSql .= ' inner join regencia on regencia.ed59_i_disciplina        = disciplina.ed12_i_codigo  ';
    $sSql .= ' inner join diario on diario.ed95_i_regencia              = regencia.ed59_i_codigo ';
    $sSql .= ' inner join calendario on calendario.ed52_i_codigo        = diario.ed95_i_calendario ';
    $sSql .= ' inner join diariofinal on diariofinal.ed74_i_diario      = diario.ed95_i_codigo  ';
    $sSql2 = '';
    if ($sDbWhere == '') {

      if ($iCodigo != null ){
        $sSql2 .= " where caddisciplina.ed232_i_codigo = $iCodigo ";
      }

    } elseif ($sDbWhere != '') {
      $sSql2 = " where $sDbWhere";
    }
    $sSql .= $sSql2;

    if ($sOrdem != null) {

      $sSql      .= ' order by ';
      $sCamposSql = explode('#', $sOrdem);
      $sVirgula   = '';
      for ($iCont = 0; $iCont < sizeof($sCamposSql); $iCont++) {

        $sSql    .= $sVirgula.$sCamposSql[$iCont];
        $sVirgula = ',';

      }

    }

    return $sSql;

  }

  /**
   * Busca os v�nculos das disciplinas com o c�digo do censo
   */
  public function sql_query_censo ( $ed232_i_codigo=null,$campos="*",$ordem=null,$dbwhere="",$groupBy=null ){

    $sql = "select ";
    if ($campos != "*" ) {

      $campos_sql = explode("#",$campos);
      $virgula    = "";
      for ($i = 0; $i < sizeof($campos_sql); $i++) {

        $sql     .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    } else {
      $sql .= $campos;
    }
    $sql .= " from caddisciplina ";
    $sql .= "      inner join censocaddisciplina  on  censocaddisciplina.ed294_caddisciplina = caddisciplina.ed232_i_codigo";
    $sql .= "      inner join censodisciplina     on  censodisciplina.ed265_i_codigo         = censocaddisciplina.ed294_censodisciplina";
    $sql .= "      left join areaconhecimento     on  areaconhecimento.ed293_sequencial      = caddisciplina.ed232_areaconhecimento";
    $sql2 = "";
    if ($dbwhere == "") {

      if ($ed232_i_codigo != null) {
        $sql2 .= " where caddisciplina.ed232_i_codigo = $ed232_i_codigo ";
      }
    } else if ($dbwhere != "") {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;

    if (!empty($groupBy)) {
      $sql .= " group by  {$groupBy} ";
    }


    if ($ordem != null) {

      $sql        .= " order by ";
      $campos_sql  = explode("#",$ordem);
      $virgula     = "";
      for ($i = 0; $i < sizeof($campos_sql); $i++) {

        $sql     .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    }
    return $sql;
  }

  public function sql_query_disciplinas_na_escola( $ed12_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from caddisciplina ";
    $sql .= "      inner join disciplina  on disciplina.ed12_i_caddisciplina = caddisciplina.ed232_i_codigo";
    $sql .= "      inner join ensino      on ensino.ed10_i_codigo            = disciplina.ed12_i_ensino";
    $sql .= "      inner join cursoedu    on cursoedu.ed29_i_ensino          = ensino.ed10_i_codigo";
    $sql .= "      inner join cursoescola on cursoedu.ed29_i_codigo          = cursoescola.ed71_i_curso";
    $sql2 = "";
    if($dbwhere==""){
      if($ed12_i_codigo!=null ){
        $sql2 .= " where disciplina.ed12_i_codigo = $ed12_i_codigo ";
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
