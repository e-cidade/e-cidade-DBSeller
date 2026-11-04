<?php
/**
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
class cl_conlancaminfocomplementarvalor
{

    const SICONF = 1;
    const CONTA_CORRENTE = 2;


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
    public $c126_sequencial = 0;
    public $c126_codlan = 0;
    public $c126_reduz = 0;
    public $c126_infocomplementar = 0;
    public $c126_valor = null;
    public $c126_tiposistema = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c126_sequencial = int4 = Código
                 c126_codlan = int4 = Código do lançamento
                 c126_reduz = int4 = Reduzido da conta
                 c126_infocomplementar = int4 = Código da Informação complementar
                 c126_valor = varchar(20) = Valor da informação complementar
                 c126_tiposistema = int4 =
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conlancaminfocomplementarvalor");
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
       $this->c126_sequencial = ($this->c126_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c126_sequencial"]:$this->c126_sequencial);
       $this->c126_codlan = ($this->c126_codlan == ""?@$GLOBALS["HTTP_POST_VARS"]["c126_codlan"]:$this->c126_codlan);
       $this->c126_reduz = ($this->c126_reduz == ""?@$GLOBALS["HTTP_POST_VARS"]["c126_reduz"]:$this->c126_reduz);
       $this->c126_infocomplementar = ($this->c126_infocomplementar == ""?@$GLOBALS["HTTP_POST_VARS"]["c126_infocomplementar"]:$this->c126_infocomplementar);
       $this->c126_valor = ($this->c126_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["c126_valor"]:$this->c126_valor);
       $this->c126_tiposistema = ($this->c126_tiposistema == ""?@$GLOBALS["HTTP_POST_VARS"]["c126_tiposistema"]:$this->c126_tiposistema);
     }else{
       $this->c126_sequencial = ($this->c126_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c126_sequencial"]:$this->c126_sequencial);
     }
   }

    public function incluir($c126_sequencial)
    {
      $this->atualizacampos();
     if($this->c126_codlan == null ){
       $this->erro_sql = " Campo Código do lançamento não informado.";
       $this->erro_campo = "c126_codlan";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c126_reduz == null ){
       $this->erro_sql = " Campo Reduzido da conta não informado.";
       $this->erro_campo = "c126_reduz";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c126_infocomplementar == null ){
       $this->erro_sql = " Campo Código da Informação complementar não informado.";
       $this->erro_campo = "c126_infocomplementar";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c126_valor == null ){
       $this->erro_sql = " Campo Valor da informação complementar não informado.";
       $this->erro_campo = "c126_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c126_tiposistema == null ){
       $this->erro_sql = " Campo  não informado.";
       $this->erro_campo = "c126_tiposistema";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c126_sequencial == "" || $c126_sequencial == null ){
       $result = db_query("select nextval('conlancaminfocomplementarvalor_c126_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: conlancaminfocomplementarvalor_c126_sequencial_seq do campo: c126_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->c126_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from conlancaminfocomplementarvalor_c126_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $c126_sequencial)){
         $this->erro_sql = " Campo c126_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c126_sequencial = $c126_sequencial;
       }
     }
     if(($this->c126_sequencial == null) || ($this->c126_sequencial == "") ){
       $this->erro_sql = " Campo c126_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into conlancaminfocomplementarvalor(
                                       c126_sequencial
                                      ,c126_codlan
                                      ,c126_reduz
                                      ,c126_infocomplementar
                                      ,c126_valor
                                      ,c126_tiposistema
                       )
                values (
                                $this->c126_sequencial
                               ,$this->c126_codlan
                               ,$this->c126_reduz
                               ,$this->c126_infocomplementar
                               ,'$this->c126_valor'
                               ,$this->c126_tiposistema
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Vínculo de informações complementares dos lançamen ($this->c126_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Vínculo de informações complementares dos lançamen já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Vínculo de informações complementares dos lançamen ($this->c126_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c126_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c126_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009655,'$this->c126_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010266,1009655,'','".AddSlashes(pg_result($resaco,0,'c126_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010266,1009656,'','".AddSlashes(pg_result($resaco,0,'c126_codlan'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010266,1009657,'','".AddSlashes(pg_result($resaco,0,'c126_reduz'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010266,1009658,'','".AddSlashes(pg_result($resaco,0,'c126_infocomplementar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010266,1009659,'','".AddSlashes(pg_result($resaco,0,'c126_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010266,1009660,'','".AddSlashes(pg_result($resaco,0,'c126_tiposistema'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($c126_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update conlancaminfocomplementarvalor set ";
     $virgula = "";
     if(trim($this->c126_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c126_sequencial"])){
       $sql  .= $virgula." c126_sequencial = $this->c126_sequencial ";
       $virgula = ",";
       if(trim($this->c126_sequencial) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "c126_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c126_codlan)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c126_codlan"])){
       $sql  .= $virgula." c126_codlan = $this->c126_codlan ";
       $virgula = ",";
       if(trim($this->c126_codlan) == null ){
         $this->erro_sql = " Campo Código do lançamento não informado.";
         $this->erro_campo = "c126_codlan";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c126_reduz)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c126_reduz"])){
       $sql  .= $virgula." c126_reduz = $this->c126_reduz ";
       $virgula = ",";
       if(trim($this->c126_reduz) == null ){
         $this->erro_sql = " Campo Reduzido da conta não informado.";
         $this->erro_campo = "c126_reduz";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c126_infocomplementar)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c126_infocomplementar"])){
       $sql  .= $virgula." c126_infocomplementar = $this->c126_infocomplementar ";
       $virgula = ",";
       if(trim($this->c126_infocomplementar) == null ){
         $this->erro_sql = " Campo Código da Informação complementar não informado.";
         $this->erro_campo = "c126_infocomplementar";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c126_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c126_valor"])){
       $sql  .= $virgula." c126_valor = '$this->c126_valor' ";
       $virgula = ",";
       if(trim($this->c126_valor) == null ){
         $this->erro_sql = " Campo Valor da informação complementar não informado.";
         $this->erro_campo = "c126_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c126_tiposistema)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c126_tiposistema"])){
       $sql  .= $virgula." c126_tiposistema = $this->c126_tiposistema ";
       $virgula = ",";
       if(trim($this->c126_tiposistema) == null ){
         $this->erro_sql = " Campo  não informado.";
         $this->erro_campo = "c126_tiposistema";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c126_sequencial!=null){
       $sql .= " c126_sequencial = $this->c126_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c126_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009655,'$this->c126_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c126_sequencial"]) || $this->c126_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010266,1009655,'".AddSlashes(pg_result($resaco,$conresaco,'c126_sequencial'))."','$this->c126_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c126_codlan"]) || $this->c126_codlan != "")
             $resac = db_query("insert into db_acount values($acount,1010266,1009656,'".AddSlashes(pg_result($resaco,$conresaco,'c126_codlan'))."','$this->c126_codlan',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c126_reduz"]) || $this->c126_reduz != "")
             $resac = db_query("insert into db_acount values($acount,1010266,1009657,'".AddSlashes(pg_result($resaco,$conresaco,'c126_reduz'))."','$this->c126_reduz',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c126_infocomplementar"]) || $this->c126_infocomplementar != "")
             $resac = db_query("insert into db_acount values($acount,1010266,1009658,'".AddSlashes(pg_result($resaco,$conresaco,'c126_infocomplementar'))."','$this->c126_infocomplementar',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c126_valor"]) || $this->c126_valor != "")
             $resac = db_query("insert into db_acount values($acount,1010266,1009659,'".AddSlashes(pg_result($resaco,$conresaco,'c126_valor'))."','$this->c126_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c126_tiposistema"]) || $this->c126_tiposistema != "")
             $resac = db_query("insert into db_acount values($acount,1010266,1009660,'".AddSlashes(pg_result($resaco,$conresaco,'c126_tiposistema'))."','$this->c126_tiposistema',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Vínculo de informações complementares dos lançamen não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c126_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Vínculo de informações complementares dos lançamen não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c126_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c126_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($c126_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c126_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009655,'$c126_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010266,1009655,'','".AddSlashes(pg_result($resaco,$iresaco,'c126_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010266,1009656,'','".AddSlashes(pg_result($resaco,$iresaco,'c126_codlan'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010266,1009657,'','".AddSlashes(pg_result($resaco,$iresaco,'c126_reduz'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010266,1009658,'','".AddSlashes(pg_result($resaco,$iresaco,'c126_infocomplementar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010266,1009659,'','".AddSlashes(pg_result($resaco,$iresaco,'c126_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010266,1009660,'','".AddSlashes(pg_result($resaco,$iresaco,'c126_tiposistema'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from conlancaminfocomplementarvalor
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c126_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c126_sequencial = $c126_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Vínculo de informações complementares dos lançamen não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c126_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Vínculo de informações complementares dos lançamen não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c126_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c126_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:conlancaminfocomplementarvalor";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($c126_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from conlancaminfocomplementarvalor ";
     $sql .= "      inner join conlancam  on  conlancam.c70_codlan = conlancaminfocomplementarvalor.c126_codlan";
     $sql .= "      inner join conplanoinfocomplementar  on  conplanoinfocomplementar.c121_sequencial = conlancaminfocomplementarvalor.c126_infocomplementar";
     $sql .= "      inner join conplanosistema  on  conplanosistema.c122_sequencial = conlancaminfocomplementarvalor.c126_tiposistema";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c126_sequencial)) {
         $sql2 .= " where conlancaminfocomplementarvalor.c126_sequencial = $c126_sequencial ";
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

    public function sql_query_file($c126_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from conlancaminfocomplementarvalor ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c126_sequencial)){
         $sql2 .= " where conlancaminfocomplementarvalor.c126_sequencial = $c126_sequencial ";
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


    /**
     * @param $sCampos
     * @param $sWhere
     * @return string
     */
    public function sql_query_informacao_complementar_valor($sCampos, $sWhere)
    {
        $sql = " select {$sCampos} from conlancaminfocomplementarvalor inner join conplanoinfocomplementar on c126_infocomplementar = c121_sequencial ";

        if (!empty($sWhere)) {
            $sql .= " where {$sWhere} ";
        }

        return $sql;
    }


    /**
     * Ajusta os valores das informações complementares dos lançamentos caso haja configurações para eles
     * @param array $aCodigosLancamentos
     * @return bool
     * @throws Exception
     */
    public function ajustarValorInformacaoComplementar($aCodigosLancamentos)
    {
        if (empty($aCodigosLancamentos)) {
            return;
        }

        $codigosLancamentos = implode(",", $aCodigosLancamentos);

        $sql  = " UPDATE infocomplementarvalor a ";
        $sql .= " SET    c123_valor = c.c126_valor ";
        $sql .= " FROM   conplanoatributolancamentos  b ";
        $sql .= " JOIN   conlancaminfocomplementarvalor c ON c.c126_codlan = b.c124_lancamento ";
        $sql .= " WHERE  c.c126_codlan in ({$codigosLancamentos}) ";
        $sql .= "   AND c.c126_reduz = a.c123_reduzido ";
        $sql .= "   AND c.c126_infocomplementar = a.c123_infocomplementar ";
        $sql .= "   AND c.c126_tiposistema = a.c123_conplanosistema ";
        $sql .= "   AND a.c123_conplanoatributolancamentos = b.c124_sequencial ";

        $rsLancamentos = db_query($sql);
        if (!$rsLancamentos) {
            throw new \Exception("Erro ao ajustar o valor das informações complementares de acordo com a configurações do lançamento.");
        }

        return true;
    }

    /**
     * Altera valores das informações complementares que já existem na estrutura conlancaminfocomplementarvalor
     * @param $valor
     * @param $sWhere
     * @return bool
     * @throws DBException
     */
    public function alterarValorInfoComplementarPorCondicao($valor, $sWhere)
    {
        $sql = "update conlancaminfocomplementarvalor set c126_valor = '{$valor}' where {$sWhere}";
        $rs = db_query($sql);

        if (!$rs) {
            throw new \DBException("Erro ao alterar o valor da informação complementar.");
        }

        return true;
    }

    /**
     * Exclui os valores das informações complementares a partir do código do lançamento e reduzido da conta
     * @param int $iCodLancamento
     * @param int $iReduzidoConta
     * @throws Exception
     */
    public function excluirInformacaoComplementarLancamento($iCodLancamento, $iReduzidoConta)
    {
        $sql = "DELETE FROM conlancaminfocomplementarvalor WHERE c126_codlan = $iCodLancamento AND c126_reduz = $iReduzidoConta";

        $rsQueryResult = db_query($sql);

        if (!$rsQueryResult) {
            throw new \Exception("Erro ao excluir valores das informações complementares");
        }
    }

    /**
     * @param $ano
     * @param $codigoInstituicao
     */
    public function montarEstrutura($ano, $codigoInstituicao, $codigoContaCorrente, $tipoSistema=2, $unidadeGestora)
    {
        $whereConplanoSistemaAtributos     = "";
        $whereUnidadeGestora               = "";
        $innerJoinConplanoSistemaAtributos = "";
        $orderBy  = " c124_data,       ";
        $orderBy .= " c123_reduzido,   ";
        $orderBy .= " c124_lancamento, ";
        $orderBy .= " c121_sequencial  ";

        if ( $tipoSistema == self::CONTA_CORRENTE ) {

            $innerJoinConplanoSistemaAtributos  = " inner join conplanosistemaatributos on c129_conplanoinfocomplementar = c123_infocomplementar ";
            $innerJoinConplanoSistemaAtributos .= "                                    and c129_conplanosistema          = c123_conplanosistema ";
            $innerJoinConplanoSistemaAtributos .= " inner join conplanosistema          on c122_sequencial               = c129_conplanosistema ";
            $leftJoinUnidadeGestora             = " left join conlancamdepartamento     on conlancamdepartamento.c128_conlancam = c124_lancamento ";


            $whereConplanoSistemaAtributos      = " and c129_conplanosistema = {$codigoContaCorrente}";
            $whereConplanoSistemaAtributos     .= " and c122_tipo = 2";
            if ( ! empty($unidadeGestora) ) {
                $whereUnidadeGestora  = " and exists ( select 1 ";
                $whereUnidadeGestora .= "                from unidadegestoradepartamentos ugp ";
                $whereUnidadeGestora .= "               where ugp.k180_unidadegestora = {$unidadeGestora}  ";
                $whereUnidadeGestora .= "                 and ugp.k180_depart = conlancamdepartamento.c128_departamento ) ";
            }
            $orderBy  = " c124_data, ";
            $orderBy .= " c123_reduzido, ";
            $orderBy .= " c124_lancamento,";
            $orderBy .= " c129_ordem";
        }


        $sql  = " create temp table w_movimentacao_conta_corrente as ";
        $sql .= "   with movimentacao_conta_corrente as (            ";
        $sql .= " select c124_sequencial,                                                                      ";
        $sql .= "        c121_sequencial,                                                                      ";
        $sql .= "        c124_data,                                                                            ";
        $sql .= "        c123_reduzido,                                                                        ";
        $sql .= "        o15_codigo,                                                                           ";
        $sql .= "        o15_descr,                                                                            ";
        $sql .= "        c60_estrut,                                                                           ";
        $sql .= "        c60_descr,                                                                            ";
        $sql .= "        c124_lancamento,                                                                      ";
        $sql .= "        0 as c53_coddoc,                                                                      ";
        $sql .= "        ''::text as c53_descr,                                                                ";
        $sql .= "        c121_sigla || '#'::text || c123_valor AS atributo,                                    ";
        $sql .= "        c124_natureza,                                                                        ";
        $sql .= "        c129_ordem,                                                                           ";
        $sql .= "        c124_valor                                                                            ";
        $sql .= "   from contabilidade.infocomplementarvalor ";
        $sql .= "        inner join conplanoatributolancamentos on c123_conplanoatributolancamentos = c124_sequencial ";
        $sql .= "        inner join conplanoinfocomplementar    on c121_sequencial   = c123_infocomplementar          ";
        $sql .= "        {$innerJoinConplanoSistemaAtributos} ";
        $sql .= "        inner join conlancamdoc                on c71_codlan = c124_lancamento                ";
        $sql .= "        {$leftJoinUnidadeGestora} ";
        $sql .= "        inner join conhistdoc                  on c53_coddoc = c71_coddoc                     ";
        $sql .= "        inner join conplanoreduz               on c61_reduz  = c123_reduzido and c61_anousu = {$ano} ";
        $sql .= "        inner join orctiporec                  on o15_codigo = c61_codigo                            ";
        $sql .= "        inner join conplano                    on c60_codcon = c61_codcon and c60_anousu = c61_anousu ";
        $sql .= "   where c61_instit = {$codigoInstituicao} ";
        $sql .= "         {$whereConplanoSistemaAtributos} ";
        $sql .= "         {$whereUnidadeGestora} ";
        $sql .= " ), movimentos_ordenados as ( ";
        $sql .= "         select * ";
        $sql .= "         from movimentacao_conta_corrente ";
        $sql .= "    order by {$orderBy} ";
        $sql .= " ), agrupa_atributos_conta_correntes as ( ";
        $sql .= "  select c124_sequencial as id,                                            ";
        $sql .= "                  c124_data as data,                                       ";
        $sql .= "                  c123_reduzido as reduzido,                               ";
        $sql .= "                  o15_codigo as codigo_recurso,                            ";
        $sql .= "                  o15_descr as descricao_recurso,                          ";
        $sql .= "                  c60_estrut as estrutural,                                ";
        $sql .= "                  c60_descr as descricao_estrutural,                       ";
        $sql .= "                  c124_lancamento as lancamento,                           ";
        $sql .= "                  c53_coddoc as documento,                                 ";
        $sql .= "                  c53_descr as documento_descricao,                        ";
        $sql .= "                  array_to_string(array_accum(atributo), ',') as atributos,";
        $sql .= "                  c124_natureza as natureza,                               ";
        $sql .= "                  c124_valor as valor_lancamento                           ";
        $sql .= "   from movimentos_ordenados ";
        $sql .= "   group by c124_sequencial, ";
        $sql .= "            c124_data, ";
        $sql .= "            c123_reduzido, ";
        $sql .= "            o15_codigo, ";
        $sql .= "            c60_descr, ";
        $sql .= "            o15_descr, ";
        $sql .= "            c60_estrut, ";
        $sql .= "            c124_lancamento, ";
        $sql .= "            c53_coddoc, ";
        $sql .= "            c53_descr, ";
        $sql .= "            c124_natureza, ";
        $sql .= "            c124_valor ";
        $sql .= "            order by atributos, c124_data";
        $sql .= " ) ";

        $sql .= " select * from agrupa_atributos_conta_correntes ";
        db_query($sql);

        $sql  = " create index w_movimentacao_conta_corrente_data on  w_movimentacao_conta_corrente(data); ";
        $sql .= " create index w_movimentacao_conta_corrente_estrutural on  w_movimentacao_conta_corrente(estrutural); ";
        $sql .= " create index w_movimentacao_conta_corrente_reduzido on  w_movimentacao_conta_corrente(reduzido); ";
        $sql .= " create index w_movimentacao_conta_corrente_documento on  w_movimentacao_conta_corrente(documento); ";
        $sql .= " create index w_movimentacao_conta_corrente_atributos on  w_movimentacao_conta_corrente(atributos); ";

        db_query($sql);
    }

    /**
     * @param string $campos
     * @param string $where
     * @param $ano
     * @param $codigoInstituicao
     * @param bool $calculoSaldoAnterior
     * @return string
     */
    public function sqlQueryRazaoContaCorrente($campos = '*', $where = '')
    {
        $sql = "select {$campos} from w_movimentacao_conta_corrente ";
        if (!empty($where)) {
            $sql .= " where {$where}";
        }
        //db_criatabela(db_query($sql));
        //exit;
        return $sql;
    }


}
