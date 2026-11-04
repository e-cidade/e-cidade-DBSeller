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

class cl_pcforne
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
    public $pc60_numcgm = 0;
    public $pc60_dtlanc_dia = null;
    public $pc60_dtlanc_mes = null;
    public $pc60_dtlanc_ano = null;
    public $pc60_dtlanc = null;
    public $pc60_obs = null;
    public $pc60_bloqueado = 'f';
    public $pc60_hora = null;
    public $pc60_usuario = 0;
    public $pc60_indicativocprb = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 pc60_numcgm = int4 = Fornecedor
                 pc60_dtlanc = date = Data Lançamento
                 pc60_obs = text = Objeto Social
                 pc60_bloqueado = bool = Bloqueado
                 pc60_hora = char(5) = Hora
                 pc60_usuario = int4 = Cod. Usuário
                 pc60_indicativocprb = bool = Contribuinte do CPRB
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("pcforne");
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
       $this->pc60_numcgm = ($this->pc60_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_numcgm"]:$this->pc60_numcgm);
       if($this->pc60_dtlanc == ""){
         $this->pc60_dtlanc_dia = ($this->pc60_dtlanc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_dtlanc_dia"]:$this->pc60_dtlanc_dia);
         $this->pc60_dtlanc_mes = ($this->pc60_dtlanc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_dtlanc_mes"]:$this->pc60_dtlanc_mes);
         $this->pc60_dtlanc_ano = ($this->pc60_dtlanc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_dtlanc_ano"]:$this->pc60_dtlanc_ano);
         if($this->pc60_dtlanc_dia != ""){
            $this->pc60_dtlanc = $this->pc60_dtlanc_ano."-".$this->pc60_dtlanc_mes."-".$this->pc60_dtlanc_dia;
         }
       }
       $this->pc60_obs = ($this->pc60_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_obs"]:$this->pc60_obs);
       $this->pc60_bloqueado = ($this->pc60_bloqueado == "f"?@$GLOBALS["HTTP_POST_VARS"]["pc60_bloqueado"]:$this->pc60_bloqueado);
       $this->pc60_hora = ($this->pc60_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_hora"]:$this->pc60_hora);
       $this->pc60_usuario = ($this->pc60_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_usuario"]:$this->pc60_usuario);
       $this->pc60_indicativocprb = ($this->pc60_indicativocprb == "f"?@$GLOBALS["HTTP_POST_VARS"]["pc60_indicativocprb"]:$this->pc60_indicativocprb);
     }else{
       $this->pc60_numcgm = ($this->pc60_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["pc60_numcgm"]:$this->pc60_numcgm);
     }
   }

    public function incluir($pc60_numcgm)
    {
      $this->atualizacampos();
     if($this->pc60_dtlanc == null ){
       $this->erro_sql = " Campo Data Lançamento não informado.";
       $this->erro_campo = "pc60_dtlanc_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pc60_bloqueado == null ){
       $this->erro_sql = " Campo Bloqueado não informado.";
       $this->erro_campo = "pc60_bloqueado";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pc60_hora == null ){
       $this->erro_sql = " Campo Hora não informado.";
       $this->erro_campo = "pc60_hora";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pc60_usuario == null ){
       $this->erro_sql = " Campo Cod. Usuário não informado.";
       $this->erro_campo = "pc60_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pc60_indicativocprb == null ){
       $this->erro_sql = " Campo Contribuinte do CPRB não informado.";
       $this->erro_campo = "pc60_indicativocprb";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->pc60_numcgm = $pc60_numcgm;
     if(($this->pc60_numcgm == null) || ($this->pc60_numcgm == "") ){
       $this->erro_sql = " Campo pc60_numcgm não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into pcforne(
                                       pc60_numcgm
                                      ,pc60_dtlanc
                                      ,pc60_obs
                                      ,pc60_bloqueado
                                      ,pc60_hora
                                      ,pc60_usuario
                                      ,pc60_indicativocprb
                       )
                values (
                                $this->pc60_numcgm
                               ,".($this->pc60_dtlanc == "null" || $this->pc60_dtlanc == ""?"null":"'".$this->pc60_dtlanc."'")."
                               ,'$this->pc60_obs'
                               ,'$this->pc60_bloqueado'
                               ,'$this->pc60_hora'
                               ,$this->pc60_usuario
                               ,'$this->pc60_indicativocprb'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Fornecedores ($this->pc60_numcgm) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Fornecedores já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Fornecedores ($this->pc60_numcgm) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pc60_numcgm;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->pc60_numcgm  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,5986,'$this->pc60_numcgm','I')");
         $resac = db_query("insert into db_acount values($acount,959,5986,'','".AddSlashes(pg_result($resaco,0,'pc60_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,959,5987,'','".AddSlashes(pg_result($resaco,0,'pc60_dtlanc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,959,5988,'','".AddSlashes(pg_result($resaco,0,'pc60_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,959,5989,'','".AddSlashes(pg_result($resaco,0,'pc60_bloqueado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,959,7812,'','".AddSlashes(pg_result($resaco,0,'pc60_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,959,7811,'','".AddSlashes(pg_result($resaco,0,'pc60_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,959,1010236,'','".AddSlashes(pg_result($resaco,0,'pc60_indicativocprb'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($pc60_numcgm=null)
    {
      $this->atualizacampos();
     $sql = " update pcforne set ";
     $virgula = "";
     if(trim($this->pc60_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc60_numcgm"])){
       $sql  .= $virgula." pc60_numcgm = $this->pc60_numcgm ";
       $virgula = ",";
       if(trim($this->pc60_numcgm) == null ){
         $this->erro_sql = " Campo Fornecedor não informado.";
         $this->erro_campo = "pc60_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pc60_dtlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc60_dtlanc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["pc60_dtlanc_dia"] !="") ){
       $sql  .= $virgula." pc60_dtlanc = '$this->pc60_dtlanc' ";
       $virgula = ",";
       if(trim($this->pc60_dtlanc) == null ){
         $this->erro_sql = " Campo Data Lançamento não informado.";
         $this->erro_campo = "pc60_dtlanc_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["pc60_dtlanc_dia"])){
         $sql  .= $virgula." pc60_dtlanc = null ";
         $virgula = ",";
         if(trim($this->pc60_dtlanc) == null ){
           $this->erro_sql = " Campo Data Lançamento não informado.";
           $this->erro_campo = "pc60_dtlanc_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->pc60_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc60_obs"])){
       $sql  .= $virgula." pc60_obs = '$this->pc60_obs' ";
       $virgula = ",";
     }
     if(trim($this->pc60_bloqueado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc60_bloqueado"])){
       $sql  .= $virgula." pc60_bloqueado = '$this->pc60_bloqueado' ";
       $virgula = ",";
       if(trim($this->pc60_bloqueado) == null ){
         $this->erro_sql = " Campo Bloqueado não informado.";
         $this->erro_campo = "pc60_bloqueado";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pc60_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc60_hora"])){
       $sql  .= $virgula." pc60_hora = '$this->pc60_hora' ";
       $virgula = ",";
       if(trim($this->pc60_hora) == null ){
         $this->erro_sql = " Campo Hora não informado.";
         $this->erro_campo = "pc60_hora";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pc60_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc60_usuario"])){
       $sql  .= $virgula." pc60_usuario = $this->pc60_usuario ";
       $virgula = ",";
       if(trim($this->pc60_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário não informado.";
         $this->erro_campo = "pc60_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pc60_indicativocprb)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc60_indicativocprb"])){
       $sql  .= $virgula." pc60_indicativocprb = '$this->pc60_indicativocprb' ";
       $virgula = ",";
       if(trim($this->pc60_indicativocprb) == null ){
         $this->erro_sql = " Campo Contribuinte do CPRB não informado.";
         $this->erro_campo = "pc60_indicativocprb";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($pc60_numcgm!=null){
       $sql .= " pc60_numcgm = $this->pc60_numcgm";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->pc60_numcgm));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5986,'$this->pc60_numcgm','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pc60_numcgm"]) || $this->pc60_numcgm != "")
             $resac = db_query("insert into db_acount values($acount,959,5986,'".AddSlashes(pg_result($resaco,$conresaco,'pc60_numcgm'))."','$this->pc60_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pc60_dtlanc"]) || $this->pc60_dtlanc != "")
             $resac = db_query("insert into db_acount values($acount,959,5987,'".AddSlashes(pg_result($resaco,$conresaco,'pc60_dtlanc'))."','$this->pc60_dtlanc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pc60_obs"]) || $this->pc60_obs != "")
             $resac = db_query("insert into db_acount values($acount,959,5988,'".AddSlashes(pg_result($resaco,$conresaco,'pc60_obs'))."','$this->pc60_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pc60_bloqueado"]) || $this->pc60_bloqueado != "")
             $resac = db_query("insert into db_acount values($acount,959,5989,'".AddSlashes(pg_result($resaco,$conresaco,'pc60_bloqueado'))."','$this->pc60_bloqueado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pc60_hora"]) || $this->pc60_hora != "")
             $resac = db_query("insert into db_acount values($acount,959,7812,'".AddSlashes(pg_result($resaco,$conresaco,'pc60_hora'))."','$this->pc60_hora',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pc60_usuario"]) || $this->pc60_usuario != "")
             $resac = db_query("insert into db_acount values($acount,959,7811,'".AddSlashes(pg_result($resaco,$conresaco,'pc60_usuario'))."','$this->pc60_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["pc60_indicativocprb"]) || $this->pc60_indicativocprb != "")
             $resac = db_query("insert into db_acount values($acount,959,1010236,'".AddSlashes(pg_result($resaco,$conresaco,'pc60_indicativocprb'))."','$this->pc60_indicativocprb',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Fornecedores não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->pc60_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Fornecedores não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->pc60_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pc60_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($pc60_numcgm=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($pc60_numcgm));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,5986,'$pc60_numcgm','E')");
           $resac  = db_query("insert into db_acount values($acount,959,5986,'','".AddSlashes(pg_result($resaco,$iresaco,'pc60_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,959,5987,'','".AddSlashes(pg_result($resaco,$iresaco,'pc60_dtlanc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,959,5988,'','".AddSlashes(pg_result($resaco,$iresaco,'pc60_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,959,5989,'','".AddSlashes(pg_result($resaco,$iresaco,'pc60_bloqueado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,959,7812,'','".AddSlashes(pg_result($resaco,$iresaco,'pc60_hora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,959,7811,'','".AddSlashes(pg_result($resaco,$iresaco,'pc60_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,959,1010236,'','".AddSlashes(pg_result($resaco,$iresaco,'pc60_indicativocprb'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from pcforne
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($pc60_numcgm)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " pc60_numcgm = $pc60_numcgm ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Fornecedores não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$pc60_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Fornecedores não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$pc60_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$pc60_numcgm;
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
        $this->erro_sql   = "Record Vazio na Tabela:pcforne";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($pc60_numcgm = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from pcforne ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = pcforne.pc60_numcgm";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = pcforne.pc60_usuario";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pc60_numcgm)) {
         $sql2 .= " where pcforne.pc60_numcgm = $pc60_numcgm ";
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

    public function sql_query_file($pc60_numcgm = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from pcforne ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pc60_numcgm)){
         $sql2 .= " where pcforne.pc60_numcgm = $pc60_numcgm ";
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

   function sql_query_conta ( $pc60_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from pcforne ";
     $sql .= "      inner join cgm on  cgm.z01_numcgm = pcforne.pc60_numcgm";
     $sql .= "      left join pcfornecon on  pc63_numcgm = pcforne.pc60_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($pc60_numcgm!=null ){
         $sql2 .= " where pcforne.pc60_numcgm = $pc60_numcgm ";
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
