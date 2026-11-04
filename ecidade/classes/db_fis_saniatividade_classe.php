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

//MODULO: fiscal
//CLASSE DA ENTIDADE saniatividade
class cl_fis_saniatividade {
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
   public $y83_codsani = 0;
   public $y83_seq = 0;
   public $y83_ativ = 0;
   public $y83_dtini_dia = null;
   public $y83_dtini_mes = null;
   public $y83_dtini_ano = null;
   public $y83_dtini = null;
   public $y83_dtfim_dia = null;
   public $y83_dtfim_mes = null;
   public $y83_dtfim_ano = null;
   public $y83_dtfim = null;
   public $y83_area = 0;
   public $y83_ativprinc = 'f';
   public $y83_perman = 'f';
   public $y83_databx_dia = null;
   public $y83_databx_mes = null;
   public $y83_databx_ano = null;
   public $y83_databx = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y83_codsani = int4 = Código do Alvará sanitário
                 y83_seq = int4 = Sequência
                 y83_ativ = int4 = Codigo da atividade
                 y83_dtini = date = Data de Início da Atividade
                 y83_dtfim = date = Data Final da Atividade
                 y83_area = float8 = Área Liberada para a Atividade
                 y83_ativprinc = bool = Atividade principal
                 y83_perman = bool = Permanente
                 y83_databx = date = Data da Baixa
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_saniatividade");
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
       $this->y83_codsani = ($this->y83_codsani == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_codsani"]:$this->y83_codsani);
       $this->y83_seq = ($this->y83_seq == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_seq"]:$this->y83_seq);
       $this->y83_ativ = ($this->y83_ativ == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_ativ"]:$this->y83_ativ);
       if($this->y83_dtini == ""){
         $this->y83_dtini_dia = ($this->y83_dtini_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_dtini_dia"]:$this->y83_dtini_dia);
         $this->y83_dtini_mes = ($this->y83_dtini_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_dtini_mes"]:$this->y83_dtini_mes);
         $this->y83_dtini_ano = ($this->y83_dtini_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_dtini_ano"]:$this->y83_dtini_ano);
         if($this->y83_dtini_dia != ""){
            $this->y83_dtini = $this->y83_dtini_ano."-".$this->y83_dtini_mes."-".$this->y83_dtini_dia;
         }
       }
       if($this->y83_dtfim == ""){
         $this->y83_dtfim_dia = ($this->y83_dtfim_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_dtfim_dia"]:$this->y83_dtfim_dia);
         $this->y83_dtfim_mes = ($this->y83_dtfim_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_dtfim_mes"]:$this->y83_dtfim_mes);
         $this->y83_dtfim_ano = ($this->y83_dtfim_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_dtfim_ano"]:$this->y83_dtfim_ano);
         if($this->y83_dtfim_dia != ""){
            $this->y83_dtfim = $this->y83_dtfim_ano."-".$this->y83_dtfim_mes."-".$this->y83_dtfim_dia;
         }
       }
       $this->y83_area = ($this->y83_area == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_area"]:$this->y83_area);
       $this->y83_ativprinc = ($this->y83_ativprinc == "f"?@$GLOBALS["HTTP_POST_VARS"]["y83_ativprinc"]:$this->y83_ativprinc);
       $this->y83_perman = ($this->y83_perman == "f"?@$GLOBALS["HTTP_POST_VARS"]["y83_perman"]:$this->y83_perman);
       if($this->y83_databx == ""){
         $this->y83_databx_dia = ($this->y83_databx_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_databx_dia"]:$this->y83_databx_dia);
         $this->y83_databx_mes = ($this->y83_databx_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_databx_mes"]:$this->y83_databx_mes);
         $this->y83_databx_ano = ($this->y83_databx_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_databx_ano"]:$this->y83_databx_ano);
         if($this->y83_databx_dia != ""){
            $this->y83_databx = $this->y83_databx_ano."-".$this->y83_databx_mes."-".$this->y83_databx_dia;
         }
       }
     }else{
       $this->y83_codsani = ($this->y83_codsani == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_codsani"]:$this->y83_codsani);
       $this->y83_seq = ($this->y83_seq == ""?@$GLOBALS["HTTP_POST_VARS"]["y83_seq"]:$this->y83_seq);
     }
   }
   // funcao para inclusao
public function incluir ($y83_codsani,$y83_seq){
      $this->atualizacampos();
     if($this->y83_ativ == null ){
       $this->erro_sql = " Campo Codigo da atividade nao Informado.";
       $this->erro_campo = "y83_ativ";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y83_dtini == null ){
       $this->erro_sql = " Campo Data de Início da Atividade nao Informado.";
       $this->erro_campo = "y83_dtini_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y83_dtfim == null ){
       $this->y83_dtfim = "null";
     }
     if($this->y83_area == null ){
       $this->y83_area = "0";
     }
     if($this->y83_ativprinc == null ){
       $this->erro_sql = " Campo Atividade principal nao Informado.";
       $this->erro_campo = "y83_ativprinc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y83_perman == null ){
       $this->erro_sql = " Campo Permanente nao Informado.";
       $this->erro_campo = "y83_perman";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y83_databx == null ){
       $this->y83_databx = "null";
     }
       $this->y83_codsani = $y83_codsani;
       $this->y83_seq = $y83_seq;
     if(($this->y83_codsani == null) || ($this->y83_codsani == "") ){
       $this->erro_sql = " Campo y83_codsani nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y83_seq == null) || ($this->y83_seq == "") ){
       $this->erro_sql = " Campo y83_seq nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_saniatividade(
                                       y83_codsani
                                      ,y83_seq
                                      ,y83_ativ
                                      ,y83_dtini
                                      ,y83_dtfim
                                      ,y83_area
                                      ,y83_ativprinc
                                      ,y83_perman
                                      ,y83_databx
                       )
                values (
                                $this->y83_codsani
                               ,$this->y83_seq
                               ,$this->y83_ativ
                               ,".($this->y83_dtini == "null" || $this->y83_dtini == ""?"null":"'".$this->y83_dtini."'")."
                               ,".($this->y83_dtfim == "null" || $this->y83_dtfim == ""?"null":"'".$this->y83_dtfim."'")."
                               ,$this->y83_area
                               ,'$this->y83_ativprinc'
                               ,'$this->y83_perman'
                               ,".($this->y83_databx == "null" || $this->y83_databx == ""?"null":"'".$this->y83_databx."'")."
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "saniatividade ($this->y83_codsani."-".$this->y83_seq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "saniatividade já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "saniatividade ($this->y83_codsani."-".$this->y83_seq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y83_codsani."-".$this->y83_seq;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y83_codsani=null,$y83_seq=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_saniatividade set ";
     $virgula = "";
     if(trim($this->y83_codsani)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_codsani"])){
       $sql  .= $virgula." y83_codsani = $this->y83_codsani ";
       $virgula = ",";
       if(trim($this->y83_codsani) == null ){
         $this->erro_sql = " Campo Código do Alvará sanitário nao Informado.";
         $this->erro_campo = "y83_codsani";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y83_seq)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_seq"])){
       $sql  .= $virgula." y83_seq = $this->y83_seq ";
       $virgula = ",";
       if(trim($this->y83_seq) == null ){
         $this->erro_sql = " Campo Sequência nao Informado.";
         $this->erro_campo = "y83_seq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y83_ativ)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_ativ"])){
       $sql  .= $virgula." y83_ativ = $this->y83_ativ ";
       $virgula = ",";
       if(trim($this->y83_ativ) == null ){
         $this->erro_sql = " Campo Codigo da atividade nao Informado.";
         $this->erro_campo = "y83_ativ";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y83_dtini)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_dtini_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y83_dtini_dia"] !="") ){
       $sql  .= $virgula." y83_dtini = '$this->y83_dtini' ";
       $virgula = ",";
       if(trim($this->y83_dtini) == null ){
         $this->erro_sql = " Campo Data de Início da Atividade nao Informado.";
         $this->erro_campo = "y83_dtini_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["y83_dtini_dia"])){
         $sql  .= $virgula." y83_dtini = null ";
         $virgula = ",";
         if(trim($this->y83_dtini) == null ){
           $this->erro_sql = " Campo Data de Início da Atividade nao Informado.";
           $this->erro_campo = "y83_dtini_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->y83_dtfim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_dtfim_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y83_dtfim_dia"] !="") ){
       $sql  .= $virgula." y83_dtfim = '$this->y83_dtfim' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["y83_dtfim_dia"])){
         $sql  .= $virgula." y83_dtfim = null ";
         $virgula = ",";
       }
     }
     if(trim($this->y83_area)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_area"])){
        if(trim($this->y83_area)=="" && isset($GLOBALS["HTTP_POST_VARS"]["y83_area"])){
           $this->y83_area = "0" ;
        }
       $sql  .= $virgula." y83_area = $this->y83_area ";
       $virgula = ",";
     }
     if(trim($this->y83_ativprinc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_ativprinc"])){
       $sql  .= $virgula." y83_ativprinc = '$this->y83_ativprinc' ";
       $virgula = ",";
       if(trim($this->y83_ativprinc) == null ){
         $this->erro_sql = " Campo Atividade principal nao Informado.";
         $this->erro_campo = "y83_ativprinc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y83_perman)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_perman"])){
       $sql  .= $virgula." y83_perman = '$this->y83_perman' ";
       $virgula = ",";
       if(trim($this->y83_perman) == null ){
         $this->erro_sql = " Campo Permanente nao Informado.";
         $this->erro_campo = "y83_perman";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y83_databx)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y83_databx_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y83_databx_dia"] !="") ){
       $sql  .= $virgula." y83_databx = '$this->y83_databx' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["y83_databx_dia"])){
         $sql  .= $virgula." y83_databx = null ";
         $virgula = ",";
       }
     }
     $sql .= " where ";
     if($y83_codsani!=null){
       $sql .= " y83_codsani = $this->y83_codsani";
     }
     if($y83_seq!=null){
       $sql .= " and  y83_seq = $this->y83_seq";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "saniatividade nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y83_codsani."-".$this->y83_seq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "saniatividade nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y83_codsani."-".$this->y83_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y83_codsani."-".$this->y83_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y83_codsani=null,$y83_seq=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_saniatividade
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y83_codsani != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y83_codsani = $y83_codsani ";
        }
        if($y83_seq != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y83_seq = $y83_seq ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "saniatividade nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y83_codsani."-".$y83_seq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "saniatividade nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y83_codsani."-".$y83_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y83_codsani."-".$y83_seq;
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
        $this->erro_sql   = "Record Vazio na Tabela:saniatividade";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function alterar_atividade($y83_codsani=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_saniatividade set y83_ativprinc = '".$this->y83_estado."'";
     $sql .= " where  y83_codsani = $this->y83_codsani
";
     $result = @db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "saniatividade nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y83_codsani."-".$this->y83_seq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "saniatividade nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y83_codsani."-".$this->y83_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y83_codsani."-".$this->y83_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
public function sql_query ( $y83_codsani=null,$y83_seq=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_saniatividade ";
     $sql .= "      left join fiscalizacao.fis_sanibaixa on y83_codsani = fis_sanibaixa.y81_codsani and y83_seq = fis_sanibaixa.y81_seq  ";
     $sql .= "      inner join ativid  on  ativid.q03_ativ = fis_saniatividade.y83_ativ";
     $sql .= "      inner join fiscalizacao.fis_sanitario  on  fis_sanitario.y80_codsani = fis_saniatividade.y83_codsani";
     $sql .= "      inner join bairro  on  bairro.j13_codi = fis_sanitario.y80_codbairro";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_sanitario.y80_codrua";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = fis_sanitario.y80_numcgm";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_sanitario.y80_depto";
     $sql2 = "";
     if($dbwhere==""){
       if($y83_codsani!=null ){
         $sql2 .= " where fis_saniatividade.y83_codsani = $y83_codsani ";
       }
       if($y83_seq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_saniatividade.y83_seq = $y83_seq ";
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
public function sql_query_file ( $y83_codsani=null,$y83_seq=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_saniatividade ";
     $sql2 = "";
     if($dbwhere==""){
       if($y83_codsani!=null ){
         $sql2 .= " where fis_saniatividade.y83_codsani = $y83_codsani ";
       }
       if($y83_seq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_saniatividade.y83_seq = $y83_seq ";
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
public function sql_query_max($campo = "y83_seq",$y83_codsani=""){
   $sql = "select max($campo) ";
   $sql .= " from fiscalizacao.fis_saniatividade ";
  if($y83_codsani != ""){
    $sql .= " where y83_codsani = $y83_codsani ";
  }
  return $sql;
}
}
?>
