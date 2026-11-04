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

//MODULO: tributario
//CLASSE DA ENTIDADE isencao
class cl_isencao { 
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
   public $v10_sequencial = 0; 
   public $v10_isencaotipo = 0; 
   public $v10_dtlan_dia = null; 
   public $v10_dtlan_mes = null; 
   public $v10_dtlan_ano = null; 
   public $v10_dtlan = null; 
   public $v10_usuario = 0;
   public $v10_anoinicial = null;
   public $v10_anofinal = null;
   public $v10_observacao = '';
   // cria propriedade com as variaveis do arquivo 
   public $campos = "
                 v10_sequencial = int4 = Codigo da isenção 
                 v10_isencaotipo = int4 = Codigo do tipo de isenção 
                 v10_dtlan = date = Data do lançamento 
                 v10_usuario = int4 = Cod. Usuário 
                 v10_anoinicial = int4 = Ano inicial da isenção
                 v10_anofinal = int4 = Ano final isenção
                 v10_observacao = text = Observação isenção
                 ";
   //funcao construtor da classe 
   public function cl_isencao() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("isencao");
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
       $this->v10_sequencial = ($this->v10_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_sequencial"]:$this->v10_sequencial);
       $this->v10_isencaotipo = ($this->v10_isencaotipo == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_isencaotipo"]:$this->v10_isencaotipo);
       if($this->v10_dtlan == ""){
         $this->v10_dtlan_dia = ($this->v10_dtlan_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_dtlan_dia"]:$this->v10_dtlan_dia);
         $this->v10_dtlan_mes = ($this->v10_dtlan_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_dtlan_mes"]:$this->v10_dtlan_mes);
         $this->v10_dtlan_ano = ($this->v10_dtlan_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_dtlan_ano"]:$this->v10_dtlan_ano);
         if($this->v10_dtlan_dia != ""){
            $this->v10_dtlan = $this->v10_dtlan_ano."-".$this->v10_dtlan_mes."-".$this->v10_dtlan_dia;
         }
       }
       $this->v10_usuario = ($this->v10_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_usuario"]:$this->v10_usuario);
       $this->v10_anoinicial = ($this->v10_anoinicial == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_anoinicial"]:$this->v10_anoinicial);
       $this->v10_anofinal = ($this->v10_anofinal == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_anofinal"]:$this->v10_anofinal);
       $this->v10_observacao = ($this->v10_observacao == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_observacao"]:$this->v10_observacao);
     }else{
       $this->v10_sequencial = ($this->v10_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["v10_sequencial"]:$this->v10_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($v10_sequencial){ 
      $this->atualizacampos();
     if($this->v10_isencaotipo == null ){
       $this->erro_sql = " Campo Codigo do tipo de isenção nao Informado.";
       $this->erro_campo = "v10_isencaotipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v10_dtlan == null ){ 
       $this->erro_sql = " Campo Data do lançamento nao Informado.";
       $this->erro_campo = "v10_dtlan_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v10_usuario == null ){
       $this->erro_sql = " Campo Cod. Usuário nao Informado.";
       $this->erro_campo = "v10_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->v10_anoinicial == null ){ 
      $this->erro_sql = " Campo Ano inicial não informado.";
      $this->erro_campo = "v10_anoinicial";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    if($this->v10_anofinal == null ){ 
      $this->erro_sql = " Campo Ano final não informado.";
      $this->erro_campo = "v10_anofinal";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    if($this->v10_observacao == null ){ 
      $this->v10_observacao = '';
    }

     if($v10_sequencial == "" || $v10_sequencial == null ){
       $result = db_query("select nextval('isencao_v10_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: isencao_v10_sequencial_seq do campo: v10_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->v10_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from isencao_v10_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $v10_sequencial)){
         $this->erro_sql = " Campo v10_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->v10_sequencial = $v10_sequencial;
       }
     }
     if(($this->v10_sequencial == null) || ($this->v10_sequencial == "") ){
       $this->erro_sql = " Campo v10_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into isencao(
                                       v10_sequencial 
                                      ,v10_isencaotipo 
                                      ,v10_dtlan 
                                      ,v10_usuario 
                       )
                values (
                                $this->v10_sequencial 
                               ,$this->v10_isencaotipo
                               ,".($this->v10_dtlan == "null" || $this->v10_dtlan == ""?"null":"'".$this->v10_dtlan."'")." 
                               ,$this->v10_usuario 
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cadastro de isenções ($this->v10_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cadastro de isenções já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cadastro de isenções ($this->v10_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->v10_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->v10_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,9927,'$this->v10_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,1707,9927,'','".AddSlashes(pg_result($resaco,0,'v10_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1707,9929,'','".AddSlashes(pg_result($resaco,0,'v10_isencaotipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1707,9931,'','".AddSlashes(pg_result($resaco,0,'v10_dtlan'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1707,9932,'','".AddSlashes(pg_result($resaco,0,'v10_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1707,1015315,'','".AddSlashes(pg_result($resaco,0,'v10_anoinicial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1707,1015316,'','".AddSlashes(pg_result($resaco,0,'v10_anofinal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1707,1015317,'','".AddSlashes(pg_result($resaco,0,'v10_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($v10_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update isencao set ";
     $virgula = "";
     if(trim($this->v10_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v10_sequencial"])){
       $sql  .= $virgula." v10_sequencial = $this->v10_sequencial ";
       $virgula = ",";
       if(trim($this->v10_sequencial) == null ){
         $this->erro_sql = " Campo Codigo da isenção nao Informado.";
         $this->erro_campo = "v10_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v10_isencaotipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v10_isencaotipo"])){
       $sql  .= $virgula." v10_isencaotipo = $this->v10_isencaotipo ";
       $virgula = ",";
       if(trim($this->v10_isencaotipo) == null ){
         $this->erro_sql = " Campo Codigo do tipo de isenção nao Informado.";
         $this->erro_campo = "v10_isencaotipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v10_dtlan)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v10_dtlan_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["v10_dtlan_dia"] !="") ){ 
       $sql  .= $virgula." v10_dtlan = '$this->v10_dtlan' ";
       $virgula = ",";
       if(trim($this->v10_dtlan) == null ){
         $this->erro_sql = " Campo Data do lançamento nao Informado.";
         $this->erro_campo = "v10_dtlan_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["v10_dtlan_dia"])){
         $sql  .= $virgula." v10_dtlan = null ";
         $virgula = ",";
         if(trim($this->v10_dtlan) == null ){
           $this->erro_sql = " Campo Data do lançamento nao Informado.";
           $this->erro_campo = "v10_dtlan_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->v10_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v10_usuario"])){
       $sql  .= $virgula." v10_usuario = $this->v10_usuario ";
       $virgula = ",";
       if(trim($this->v10_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário nao Informado.";
         $this->erro_campo = "v10_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->v10_anoinicial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v10_anoinicial"])){ 
      $sql  .= $virgula." v10_anoinicial = $this->v10_anoinicial ";
      $virgula = ",";
      if(trim($this->v10_anoinicial) == null ){ 
        $this->erro_sql = " Campo ano inicial não informado.";
        $this->erro_campo = "v10_anoinicial";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if(trim($this->v10_anofinal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v10_anofinal"])){ 
      $sql  .= $virgula." v10_anofinal = $this->v10_anofinal ";
      $virgula = ",";
      if(trim($this->v10_anofinal) == null ){ 
        $this->erro_sql = " Campo ano final não informado.";
        $this->erro_campo = "v10_anofinal";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if(trim($this->v10_observacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v10_observacao"])){ 
      $sql  .= $virgula." v10_observacao = '$this->v10_observacao' ";
      $virgula = ",";
      if(trim($this->v10_observacao) == null ){ 
        $this->erro_sql = " Campo observação não informado.";
        $this->erro_campo = "v10_observacao";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
    }

     $sql .= " where ";
     if($v10_sequencial!=null){
       $sql .= " v10_sequencial = $this->v10_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->v10_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,9927,'$this->v10_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["v10_sequencial"]))
           $resac = db_query("insert into db_acount values($acount,1707,9927,'".AddSlashes(pg_result($resaco,$conresaco,'v10_sequencial'))."','$this->v10_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["v10_isencaotipo"]))
           $resac = db_query("insert into db_acount values($acount,1707,9929,'".AddSlashes(pg_result($resaco,$conresaco,'v10_isencaotipo'))."','$this->v10_isencaotipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["v10_dtlan"]))
           $resac = db_query("insert into db_acount values($acount,1707,9931,'".AddSlashes(pg_result($resaco,$conresaco,'v10_dtlan'))."','$this->v10_dtlan',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["v10_usuario"]))
           $resac = db_query("insert into db_acount values($acount,1707,9932,'".AddSlashes(pg_result($resaco,$conresaco,'v10_usuario'))."','$this->v10_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["v10_anoinicial"]))
           $resac = db_query("insert into db_acount values($acount,1707,1015315,'".AddSlashes(pg_result($resaco,$conresaco,'v10_anoinicial'))."','$this->v10_anoinicial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["v10_anofinal"]))
           $resac = db_query("insert into db_acount values($acount,1707,1015316,'".AddSlashes(pg_result($resaco,$conresaco,'v10_anofinal'))."','$this->v10_anofinal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["v10_observacao"]))
           $resac = db_query("insert into db_acount values($acount,1707,1015317,'".AddSlashes(pg_result($resaco,$conresaco,'v10_observacao'))."','$this->v10_observacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de isenções nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->v10_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de isenções nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->v10_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->v10_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($v10_sequencial=null,$dbwhere=null) { 
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($v10_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,9927,'$v10_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,1707,9927,'','".AddSlashes(pg_result($resaco,$iresaco,'v10_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1707,9929,'','".AddSlashes(pg_result($resaco,$iresaco,'v10_isencaotipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1707,9931,'','".AddSlashes(pg_result($resaco,$iresaco,'v10_dtlan'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1707,9932,'','".AddSlashes(pg_result($resaco,$iresaco,'v10_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1707,1015315,'','".AddSlashes(pg_result($resaco,$iresaco,'v10_anoinicial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1707,1015316,'','".AddSlashes(pg_result($resaco,$iresaco,'v10_anofinal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1707,1015317,'','".AddSlashes(pg_result($resaco,$iresaco,'v10_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from isencao
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($v10_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " v10_sequencial = $v10_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de isenções nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$v10_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de isenções nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$v10_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$v10_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:isencao";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $v10_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof((array)$campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from isencao ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = isencao.v10_usuario";
     $sql .= "      inner join isencaotipo  on  isencaotipo.v11_sequencial = isencao.v10_isencaotipo";
     $sql2 = "";
     if($dbwhere==""){
       if($v10_sequencial!=null ){
         $sql2 .= " where isencao.v10_sequencial = $v10_sequencial ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof((array)$campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   public function sql_query_file ( $v10_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof((array)$campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from isencao ";
     $sql2 = "";
     if($dbwhere==""){
       if($v10_sequencial!=null ){
         $sql2 .= " where isencao.v10_sequencial = $v10_sequencial ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof((array)$campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   public function sql_query_func ( $v10_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){ 
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof((array)$campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from isencao ";
     $sql .= "      inner join db_usuarios   on  db_usuarios.id_usuario = isencao.v10_usuario";
     $sql .= "      inner join isencaotipo   on  isencaotipo.v11_sequencial = isencao.v10_isencaotipo";
     $sql .= "      left  join isencaocgm    on  isencaocgm.v12_isencao     = isencao.v10_sequencial";
     $sql .= "      left  join isencaomatric on  isencaomatric.v15_isencao  = isencao.v10_sequencial";
     $sql .= "      left  join isencaoinscr  on  isencaoinscr.v16_isencao   = isencao.v10_sequencial";
     $sql2 = "";
     if($dbwhere==""){
       if($v10_sequencial!=null ){
         $sql2 .= " where isencao.v10_sequencial = $v10_sequencial ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof((array)$campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }

  public function sql_buscaIsencoesPorCgm($iCgm){
    return $this->sql_buscaIsencoes(null, $iCgm);
  }

  public function sql_buscaIsencoesPorInscricao($iInscricao){
    return $this->sql_buscaIsencoes($iInscricao, null);
  }

  private function sql_buscaIsencoes($iInscricao = null, $iCgm = null){

    $whereInscricao = "isencaoinscr.v16_inscr is null";
    $whereCgm       = "isencaocgm.v12_numcgm is null";
    $joinInscricao  = "";
    $joinCgm        = "";

    if ($iInscricao) {
      $whereInscricao = "isencaoinscr.v16_inscr = $iInscricao";
      $joinInscricao  = "inner join isencaoinscricoes on isencaoinscricoes.v16_isencao = isencao.v10_sequencial";
    }
    
    if ($iCgm) {
      $whereCgm = "isencaocgm.v12_numcgm = $iCgm";
      $joinCgm  = "inner join isencoescgm on isencoescgm.v12_isencao = isencao.v10_sequencial";
    }

    $sql = "
      with isencaoinscricoes as (
          select
              v16_sequencial,
              v16_isencao,
              v16_inscr
          from
              isencaoinscr
          where
            $whereInscricao
      ),
      
      isencoescgm as (
          select
              v12_sequencial,
              v12_isencao,
              v12_numcgm
          from
              isencaocgm
          where
            $whereCgm
      ),

      isencoes as (
          select
              v10_sequencial,
              v10_isencaotipo,
              v10_dtlan,
              v10_usuario,
              v10_anoinicial,
              v10_anofinal,
              v10_observacao
          from
              isencao 
                $joinInscricao
                $joinCgm
      ),

      isencoesmatricula as (
          select
              v15_sequencial,
              v15_isencao,
              v15_matric
          from
              isencoes
              inner join isencaomatric on isencaomatric.v15_isencao = isencoes.v10_sequencial
      ),

      isencoescalculos as (
          select
              v46_sequencial,
              v46_isencao,
              v46_cadcalc,
              v46_percentual
          from
              isencoes
              inner join isencaocalc on isencaocalc.v46_isencao = isencoes.v10_sequencial
      ),

      calculos as (
          select
              q85_codigo,
              q85_descr,
              q85_uniref,
              q85_dtoper,
              q85_codven,
              q85_var,
              q85_fixmes,
              q85_forcal,
              q85_perman,
              q85_outromun
          from
              isencoescalculos
              inner join cadcalc on cadcalc.q85_codigo = isencoescalculos.v46_cadcalc
      ),

      tiposisencao as (
          select
              v11_sequencial,
              v11_descr,
              v11_regra,
              v11_observacao
          from
              isencoes
              inner join isencaotipo on isencaotipo.v11_sequencial = isencoes.v10_isencaotipo
      ),

      usuarios as (
          select
              id_usuario,
              nome,
              login,
              usuarioativo,
              email,
              usuext,
              administrador,
              dataexpira,
              liberalotacao
          from
              isencoes
              inner join db_usuarios on db_usuarios.id_usuario = isencoes.v10_usuario
      )

      select
          distinct isencoes.v10_sequencial as sequencialisencao,
          tiposisencao.v11_descr as descricaotipoisencao,
          case
              when isencoesmatricula.v15_sequencial is not null then 'Matrícula'
              when isencaoinscricoes.v16_sequencial is not null then 'Inscrição'
              when isencoescgm.v12_sequencial is not null then 'Cgm'
          end as dl_CampoOrigem,
          case
              when isencoesmatricula.v15_sequencial is not null then isencoesmatricula.v15_matric
              when isencaoinscricoes.v16_sequencial is not null then isencaoinscricoes.v16_inscr
              when isencoescgm.v12_sequencial is not null then isencoescgm.v12_numcgm
          end as dl_Origem,
          case
              when isencoesmatricula.v15_sequencial is not null then (
                  select
                      z01_nome
                  from
                      cgm
                      inner join iptubase on iptubase.j01_numcgm = cgm.z01_numcgm
                  where
                      j01_matric = isencoesmatricula.v15_matric
                  limit
                      1
              )
              when isencaoinscricoes.v16_sequencial is not null then (
                  select
                      z01_nome
                  from
                      cgm
                      inner join issbase on issbase.q02_numcgm = cgm.z01_numcgm
                  where
                      q02_inscr = isencaoinscricoes.v16_inscr
                  limit
                      1
              )
              when isencoescgm.v12_sequencial is not null then (
                  select
                      z01_nome
                  from
                      cgm
                  where
                      z01_numcgm = isencoescgm.v12_numcgm
                  limit
                      1
              )
          end as dl_nome,
          usuarios.nome as usuario,
          isencoes.v10_anoinicial as anoinicial,
          isencoes.v10_anofinal as anofinal,
          isencoes.v10_observacao,
          isencoes.v10_dtlan as datalancamento,
          calculos.q85_descr as descricaocalculo,
          isencoescalculos.v46_percentual as percentual
      from
          isencoes
          inner join isencoescalculos ON isencoescalculos.v46_isencao = isencoes.v10_sequencial
          left join calculos ON calculos.q85_codigo = isencoescalculos.v46_cadcalc
          inner join usuarios on usuarios.id_usuario = isencoes.v10_usuario
          inner join tiposisencao on tiposisencao.v11_sequencial = isencoes.v10_isencaotipo
          left join isencoescgm on isencoescgm.v12_isencao = isencoes.v10_sequencial
          left join isencoesmatricula on isencoesmatricula.v15_isencao = isencoes.v10_sequencial
          left join isencaoinscricoes on isencaoinscricoes.v16_isencao = isencoes.v10_sequencial
      order by
          sequencialisencao;
    ";

    return $sql;
  }
}
?>
