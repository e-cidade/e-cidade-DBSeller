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

class cl_proced
{
   // cria variaveis de erro 
    public $rotulo = null; 
    public $query_sql = null; 
    public $numrows = 0; 
    public $numrows_incluir = 0; 
    public $numrows_alterar = 0; 
    public $numrows_excluir = 0; 
    public $erro_status = null; 
    public $erro_sql = null; 
    public $erro_banco = null;  
    public $erro_msg = null;  
    public $erro_campo = null;  
    public $pagina_retorno = null; 
    /* Variáveis do Arquivo */
    public $v03_codigo = 0; 
    public $v03_descr = null; 
    public $v03_dcomp = null; 
    public $v03_receit = 0; 
    public $v03_tributaria = 0; 
    public $v03_instit = 0; 
    public $v03_procedtipo = 0; 
    public $v03_limite_dia = null; 
    public $v03_limite_mes = null; 
    public $v03_limite_ano = null; 
    public $v03_limite = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 v03_codigo = int4 = Código 
                 v03_descr = varchar(20) = Descrição Abreviada 
                 v03_dcomp = varchar(40) = Descrição Completa 
                 v03_receit = int4 = Receita de Dívida Ativa 
                 k00_hist = int4 = Histórico de Cálculo
                 v03_tributaria = int4 = Tipo de Procedência 
                 v03_instit = int4 = Cod. Instituição 
                 v03_procedtipo = int4 = Tipo de Procedência 
                 v03_limite = date = Data Limite 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("proced"); 
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\")</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    public function atualizacampos($exclusao = false)
    {
     if($exclusao==false){
       $this->v03_codigo = ($this->v03_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_codigo"]:$this->v03_codigo);
       $this->v03_descr = ($this->v03_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_descr"]:$this->v03_descr);
       $this->v03_dcomp = ($this->v03_dcomp == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_dcomp"]:$this->v03_dcomp);
       $this->v03_receit = ($this->v03_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_receit"]:$this->v03_receit);
       $this->k00_hist = ($this->k00_hist == ""?@$GLOBALS["HTTP_POST_VARS"]["k00_hist"]:$this->k00_hist);
       $this->v03_tributaria = ($this->v03_tributaria == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_tributaria"]:$this->v03_tributaria);
       $this->v03_instit = ($this->v03_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_instit"]:$this->v03_instit);
       $this->v03_procedtipo = ($this->v03_procedtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_procedtipo"]:$this->v03_procedtipo);
       if($this->v03_limite == ""){
         $this->v03_limite_dia = ($this->v03_limite_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_limite_dia"]:$this->v03_limite_dia);
         $this->v03_limite_mes = ($this->v03_limite_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_limite_mes"]:$this->v03_limite_mes);
         $this->v03_limite_ano = ($this->v03_limite_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_limite_ano"]:$this->v03_limite_ano);
         if($this->v03_limite_dia != ""){
            $this->v03_limite = $this->v03_limite_ano."-".$this->v03_limite_mes."-".$this->v03_limite_dia;
         }
       }
     }else{
       $this->v03_codigo = ($this->v03_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["v03_codigo"]:$this->v03_codigo);
     }
   }

    public function incluir($v03_codigo)
    {
      $this->atualizacampos();
     if($this->v03_descr == null ){ 
       $this->erro_sql = " Campo Descrição Abreviada não informado.";
       $this->erro_campo = "v03_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v03_dcomp == null ){ 
       $this->erro_sql = " Campo Descrição Completa não informado.";
       $this->erro_campo = "v03_dcomp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v03_receit == null ){ 
       $this->erro_sql = " Campo Receita de Dívida Ativa não informado.";
       $this->erro_campo = "v03_receit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->k00_hist == null ){
       $this->erro_sql = " Campo Histórico de Cálculo nao Informado.";
       $this->erro_campo = "k00_hist";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v03_tributaria == null ){
       $this->erro_sql = " Campo Tipo de Procedência nao Informado.";
       $this->erro_campo = "v03_tributaria";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v03_instit == null ){ 
       $this->erro_sql = " Campo Cod. Instituição não informado.";
       $this->erro_campo = "v03_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->v03_procedtipo == null ){ 
       $this->erro_sql = " Campo Tipo de Procedência não informado.";
       $this->erro_campo = "v03_procedtipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     
     if($v03_codigo == "" || $v03_codigo == null ){
       $result = db_query("select nextval('proced_v03_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: proced_v03_codigo_seq do campo: v03_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->v03_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from proced_v03_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $v03_codigo)){
         $this->erro_sql = " Campo v03_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->v03_codigo = $v03_codigo; 
       }
     }
     if(($this->v03_codigo == null) || ($this->v03_codigo == "") ){ 
       $this->erro_sql = " Campo v03_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into proced(
                                       v03_codigo 
                                      ,v03_descr 
                                      ,v03_dcomp 
                                      ,v03_receit 
                                      ,k00_hist 
                                      ,v03_tributaria 
                                      ,v03_instit 
                                      ,v03_procedtipo 
                                      ,v03_limite 
                       )
                values (
                                $this->v03_codigo 
                               ,'$this->v03_descr' 
                               ,'$this->v03_dcomp' 
                               ,$this->v03_receit
                               ,$this->k00_hist
                               ,$this->v03_tributaria 
                               ,$this->v03_instit 
                               ,$this->v03_procedtipo 
                               ,".($this->v03_limite == "null" || $this->v03_limite == ""?"null":"'".$this->v03_limite."'")." 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = " ($this->v03_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = " já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = " ($this->v03_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->v03_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->v03_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,493,'$this->v03_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,93,493,'','".AddSlashes(pg_result($resaco,0,'v03_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,494,'','".AddSlashes(pg_result($resaco,0,'v03_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,495,'','".AddSlashes(pg_result($resaco,0,'v03_dcomp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,496,'','".AddSlashes(pg_result($resaco,0,'v03_receit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,375,'','".AddSlashes(pg_result($resaco,0,'k00_hist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,8075,'','".AddSlashes(pg_result($resaco,0,'v03_tributaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,10575,'','".AddSlashes(pg_result($resaco,0,'v03_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,18162,'','".AddSlashes(pg_result($resaco,0,'v03_procedtipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,93,1014238,'','".AddSlashes(pg_result($resaco,0,'v03_limite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($v03_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update proced set ";
     $virgula = "";
     if(trim($this->v03_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_codigo"])){ 
       $sql  .= $virgula." v03_codigo = $this->v03_codigo ";
       $virgula = ",";
       if(trim($this->v03_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "v03_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v03_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_descr"])){ 
       $sql  .= $virgula." v03_descr = '$this->v03_descr' ";
       $virgula = ",";
       if(trim($this->v03_descr) == null ){ 
         $this->erro_sql = " Campo Descrição Abreviada não informado.";
         $this->erro_campo = "v03_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v03_dcomp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_dcomp"])){ 
       $sql  .= $virgula." v03_dcomp = '$this->v03_dcomp' ";
       $virgula = ",";
       if(trim($this->v03_dcomp) == null ){ 
         $this->erro_sql = " Campo Descrição Completa não informado.";
         $this->erro_campo = "v03_dcomp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v03_receit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_receit"])){ 
       $sql  .= $virgula." v03_receit = $this->v03_receit ";
       $virgula = ",";
       if(trim($this->v03_receit) == null ){ 
         $this->erro_sql = " Campo Receita de Dívida Ativa não informado.";
         $this->erro_campo = "v03_receit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k00_hist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k00_hist"])){
        $sql  .= $virgula." k00_hist = $this->k00_hist ";
        $virgula = ",";
        if(trim($this->k00_hist) == null ){
          $this->erro_sql = " Campo Histórico de Cálculo nao Informado.";
          $this->erro_campo = "k00_hist";
          $this->erro_banco = "";
          $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
          $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      
          return false;
        }
    }
      
     if(trim($this->v03_tributaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_tributaria"])){ 
       $sql  .= $virgula." v03_tributaria = $this->v03_tributaria ";
       $virgula = ",";
       if(trim($this->v03_tributaria) == null ){ 
         $this->erro_sql = " Campo Tipo de Procedência não informado.";
         $this->erro_campo = "v03_tributaria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v03_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_instit"])){ 
       $sql  .= $virgula." v03_instit = $this->v03_instit ";
       $virgula = ",";
       if(trim($this->v03_instit) == null ){ 
         $this->erro_sql = " Campo Cod. Instituição não informado.";
         $this->erro_campo = "v03_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v03_procedtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_procedtipo"])){ 
       $sql  .= $virgula." v03_procedtipo = $this->v03_procedtipo ";
       $virgula = ",";
       if(trim($this->v03_procedtipo) == null ){ 
         $this->erro_sql = " Campo Tipo de Procedência não informado.";
         $this->erro_campo = "v03_procedtipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->v03_limite)!="" || isset($GLOBALS["HTTP_POST_VARS"]["v03_limite_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["v03_limite_dia"] !="") ){ 
       $sql  .= $virgula." v03_limite = '$this->v03_limite' ";
       $virgula = ",";
       if(trim($this->v03_limite) == null ){ 
         $this->erro_sql = " Campo Data Limite não informado.";
         $this->erro_campo = "v03_limite_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
         $sql  .= $virgula." v03_limite = null ";
         $virgula = ",";
         
       }
     
     $sql .= " where ";
     if($v03_codigo!=null){
       $sql .= " v03_codigo = $this->v03_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->v03_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,493,'$this->v03_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_codigo"]) || $this->v03_codigo != "")
             $resac = db_query("insert into db_acount values($acount,93,493,'".AddSlashes(pg_result($resaco,$conresaco,'v03_codigo'))."','$this->v03_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_descr"]) || $this->v03_descr != "")
             $resac = db_query("insert into db_acount values($acount,93,494,'".AddSlashes(pg_result($resaco,$conresaco,'v03_descr'))."','$this->v03_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_dcomp"]) || $this->v03_dcomp != "")
             $resac = db_query("insert into db_acount values($acount,93,495,'".AddSlashes(pg_result($resaco,$conresaco,'v03_dcomp'))."','$this->v03_dcomp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_receit"]) || $this->v03_receit != "")
             $resac = db_query("insert into db_acount values($acount,93,496,'".AddSlashes(pg_result($resaco,$conresaco,'v03_receit'))."','$this->v03_receit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["k00_hist"]) || $this->k00_hist != "")
             $resac = db_query("insert into db_acount values($acount,93,375,'".AddSlashes(pg_result($resaco,$conresaco,'k00_hist'))."','$this->k00_hist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_tributaria"]) || $this->v03_tributaria != "")
             $resac = db_query("insert into db_acount values($acount,93,8075,'".AddSlashes(pg_result($resaco,$conresaco,'v03_tributaria'))."','$this->v03_tributaria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_instit"]) || $this->v03_instit != "")
             $resac = db_query("insert into db_acount values($acount,93,10575,'".AddSlashes(pg_result($resaco,$conresaco,'v03_instit'))."','$this->v03_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_procedtipo"]) || $this->v03_procedtipo != "")
             $resac = db_query("insert into db_acount values($acount,93,18162,'".AddSlashes(pg_result($resaco,$conresaco,'v03_procedtipo'))."','$this->v03_procedtipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["v03_limite"]) || $this->v03_limite != "")
             $resac = db_query("insert into db_acount values($acount,93,1014238,'".AddSlashes(pg_result($resaco,$conresaco,'v03_limite'))."','$this->v03_limite',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->v03_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->v03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->v03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($v03_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($v03_codigo));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,493,'$v03_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,93,493,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,494,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,495,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_dcomp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,496,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_receit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,375,'','".AddSlashes(pg_result($resaco,$iresaco,'k00_hist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,8075,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_tributaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,10575,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,18162,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_procedtipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,93,1014238,'','".AddSlashes(pg_result($resaco,$iresaco,'v03_limite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from proced
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($v03_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " v03_codigo = $v03_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$v03_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$v03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$v03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function sql_record($sql)
    {
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
        $this->erro_sql   = "Record Vazio na Tabela:proced";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($v03_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from proced ";
     $sql .= "      inner join histcalc  on  histcalc.k01_codigo = proced.k00_hist";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = proced.v03_receit";
     $sql .= "      inner join db_config  on  db_config.codigo = proced.v03_instit";
     $sql .= "      inner join tipoproced  on  tipoproced.v07_sequencial = proced.v03_tributaria";
     $sql .= "      inner join procedtipo  on  procedtipo.v28_sequencial = proced.v03_procedtipo";
     $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
     $sql .= "      inner join tabrectipo  on  tabrectipo.k116_sequencial = tabrec.k02_tabrectipo";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql .= "      inner join procedtipogrupo  on  procedtipogrupo.v29_sequencial = procedtipo.v28_grupo";
     $sql .= "      left  join procedarretipo on v06_proced = v03_codigo";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($v03_codigo)) {
         $sql2 .= " where proced.v03_codigo = $v03_codigo "; 
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

    public function sql_query_file($v03_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from proced ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($v03_codigo)){
         $sql2 .= " where proced.v03_codigo = $v03_codigo "; 
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
  public function sql_query_arretipo ( $v03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
    
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
    $sql .= " from proced ";
    $sql .= "      inner join procedarretipo on procedarretipo.v06_proced = proced.v03_codigo";
    $sql .= "      inner join arretipo       on arretipo.k00_tipo         = procedarretipo.v06_arretipo";
    $sql2 = "";
    if($dbwhere==""){
      if($v03_codigo!=null ){
        $sql2 .= " where proced.v03_codigo = $v03_codigo ";
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
