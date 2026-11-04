<?php
/**
 *  E-cidade Software Publico para Gestao Municipal
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

class cl_infocomplementarvalor
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
    public $c123_sequencial = 0;
    public $c123_conplanoatributolancamentos = 0;
    public $c123_valor = null;
    public $c123_reduzido = 0;
    public $c123_infocomplementar = 0;
    public $c123_conplanosistema = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c123_sequencial = int4 = Sequencial
                 c123_conplanoatributolancamentos = int4 = conplanoatributolancamentos
                 c123_valor = varchar(20) = Valor da informação complementar
                 c123_reduzido = int4 = Reduzido
                 c123_infocomplementar = int4 = Informação comeplementar
                 c123_conplanosistema = int4 = Sistema
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("infocomplementarvalor");
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
       $this->c123_sequencial = ($this->c123_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c123_sequencial"]:$this->c123_sequencial);
       $this->c123_conplanoatributolancamentos = ($this->c123_conplanoatributolancamentos == ""?@$GLOBALS["HTTP_POST_VARS"]["c123_conplanoatributolancamentos"]:$this->c123_conplanoatributolancamentos);
       $this->c123_valor = ($this->c123_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["c123_valor"]:$this->c123_valor);
       $this->c123_reduzido = ($this->c123_reduzido == ""?@$GLOBALS["HTTP_POST_VARS"]["c123_reduzido"]:$this->c123_reduzido);
       $this->c123_infocomplementar = ($this->c123_infocomplementar == ""?@$GLOBALS["HTTP_POST_VARS"]["c123_infocomplementar"]:$this->c123_infocomplementar);
       $this->c123_conplanosistema = ($this->c123_conplanosistema == ""?@$GLOBALS["HTTP_POST_VARS"]["c123_conplanosistema"]:$this->c123_conplanosistema);
     }else{
       $this->c123_sequencial = ($this->c123_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c123_sequencial"]:$this->c123_sequencial);
     }
   }

    public function incluir($c123_sequencial)
    {
      $this->atualizacampos();
     if($this->c123_conplanoatributolancamentos == null ){
       $this->erro_sql = " Campo conplanoatributolancamentos não informado.";
       $this->erro_campo = "c123_conplanoatributolancamentos";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c123_valor == null ){
        $this->c123_valor = 0;
     }
     if($this->c123_reduzido == null ){
       $this->erro_sql = " Campo Reduzido não informado.";
       $this->erro_campo = "c123_reduzido";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c123_infocomplementar == null ){
       $this->erro_sql = " Campo Informação comeplementar não informado.";
       $this->erro_campo = "c123_infocomplementar";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c123_conplanosistema == null ){
       $this->erro_sql = " Campo Sistema não informado.";
       $this->erro_campo = "c123_conplanosistema";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c123_sequencial == "" || $c123_sequencial == null ){
       $result = db_query("select nextval('infocomplementarvalor_c123_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: infocomplementarvalor_c123_sequencial_seq do campo: c123_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->c123_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from infocomplementarvalor_c123_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $c123_sequencial)){
         $this->erro_sql = " Campo c123_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c123_sequencial = $c123_sequencial;
       }
     }
     if(($this->c123_sequencial == null) || ($this->c123_sequencial == "") ){
       $this->erro_sql = " Campo c123_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into infocomplementarvalor(
                                       c123_sequencial
                                      ,c123_conplanoatributolancamentos
                                      ,c123_valor
                                      ,c123_reduzido
                                      ,c123_infocomplementar
                                      ,c123_conplanosistema
                       )
                values (
                                $this->c123_sequencial
                               ,$this->c123_conplanoatributolancamentos
                               ,'$this->c123_valor'
                               ,$this->c123_reduzido
                               ,$this->c123_infocomplementar
                               ,$this->c123_conplanosistema
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Info complementar valor ($this->c123_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Info complementar valor já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Info complementar valor ($this->c123_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c123_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c123_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009622,'$this->c123_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010258,1009622,'','".AddSlashes(pg_result($resaco,0,'c123_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010258,1009624,'','".AddSlashes(pg_result($resaco,0,'c123_conplanoatributolancamentos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010258,1009625,'','".AddSlashes(pg_result($resaco,0,'c123_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010258,1009631,'','".AddSlashes(pg_result($resaco,0,'c123_reduzido'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010258,1009653,'','".AddSlashes(pg_result($resaco,0,'c123_infocomplementar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010258,1009654,'','".AddSlashes(pg_result($resaco,0,'c123_conplanosistema'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($c123_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update infocomplementarvalor set ";
     $virgula = "";
     if(trim($this->c123_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c123_sequencial"])){
       $sql  .= $virgula." c123_sequencial = $this->c123_sequencial ";
       $virgula = ",";
       if(trim($this->c123_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "c123_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c123_conplanoatributolancamentos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c123_conplanoatributolancamentos"])){
       $sql  .= $virgula." c123_conplanoatributolancamentos = $this->c123_conplanoatributolancamentos ";
       $virgula = ",";
       if(trim($this->c123_conplanoatributolancamentos) == null ){
         $this->erro_sql = " Campo conplanoatributolancamentos não informado.";
         $this->erro_campo = "c123_conplanoatributolancamentos";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c123_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c123_valor"])){
       $sql  .= $virgula." c123_valor = '$this->c123_valor' ";
       $virgula = ",";
       if(trim($this->c123_valor) == null ){
         $this->c123_valor = 0;
       }
     }
     if(trim($this->c123_reduzido)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c123_reduzido"])){
       $sql  .= $virgula." c123_reduzido = $this->c123_reduzido ";
       $virgula = ",";
       if(trim($this->c123_reduzido) == null ){
         $this->erro_sql = " Campo Reduzido não informado.";
         $this->erro_campo = "c123_reduzido";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c123_infocomplementar)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c123_infocomplementar"])){
       $sql  .= $virgula." c123_infocomplementar = $this->c123_infocomplementar ";
       $virgula = ",";
       if(trim($this->c123_infocomplementar) == null ){
         $this->erro_sql = " Campo Informação comeplementar não informado.";
         $this->erro_campo = "c123_infocomplementar";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c123_conplanosistema)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c123_conplanosistema"])){
       $sql  .= $virgula." c123_conplanosistema = $this->c123_conplanosistema ";
       $virgula = ",";
       if(trim($this->c123_conplanosistema) == null ){
         $this->erro_sql = " Campo Sistema não informado.";
         $this->erro_campo = "c123_conplanosistema";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c123_sequencial!=null){
       $sql .= " c123_sequencial = $this->c123_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c123_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009622,'$this->c123_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c123_sequencial"]) || $this->c123_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010258,1009622,'".AddSlashes(pg_result($resaco,$conresaco,'c123_sequencial'))."','$this->c123_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c123_conplanoatributolancamentos"]) || $this->c123_conplanoatributolancamentos != "")
             $resac = db_query("insert into db_acount values($acount,1010258,1009624,'".AddSlashes(pg_result($resaco,$conresaco,'c123_conplanoatributolancamentos'))."','$this->c123_conplanoatributolancamentos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c123_valor"]) || $this->c123_valor != "")
             $resac = db_query("insert into db_acount values($acount,1010258,1009625,'".AddSlashes(pg_result($resaco,$conresaco,'c123_valor'))."','$this->c123_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c123_reduzido"]) || $this->c123_reduzido != "")
             $resac = db_query("insert into db_acount values($acount,1010258,1009631,'".AddSlashes(pg_result($resaco,$conresaco,'c123_reduzido'))."','$this->c123_reduzido',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c123_infocomplementar"]) || $this->c123_infocomplementar != "")
             $resac = db_query("insert into db_acount values($acount,1010258,1009653,'".AddSlashes(pg_result($resaco,$conresaco,'c123_infocomplementar'))."','$this->c123_infocomplementar',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c123_conplanosistema"]) || $this->c123_conplanosistema != "")
             $resac = db_query("insert into db_acount values($acount,1010258,1009654,'".AddSlashes(pg_result($resaco,$conresaco,'c123_conplanosistema'))."','$this->c123_conplanosistema',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Info complementar valor não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c123_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Info complementar valor não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c123_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c123_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($c123_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c123_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009622,'$c123_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010258,1009622,'','".AddSlashes(pg_result($resaco,$iresaco,'c123_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010258,1009624,'','".AddSlashes(pg_result($resaco,$iresaco,'c123_conplanoatributolancamentos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010258,1009625,'','".AddSlashes(pg_result($resaco,$iresaco,'c123_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010258,1009631,'','".AddSlashes(pg_result($resaco,$iresaco,'c123_reduzido'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010258,1009653,'','".AddSlashes(pg_result($resaco,$iresaco,'c123_infocomplementar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010258,1009654,'','".AddSlashes(pg_result($resaco,$iresaco,'c123_conplanosistema'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from infocomplementarvalor
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c123_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c123_sequencial = $c123_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Info complementar valor não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c123_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Info complementar valor não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c123_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c123_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:infocomplementarvalor";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($c123_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from infocomplementarvalor ";
     $sql .= "      inner join conplanoinfocomplementar  on  conplanoinfocomplementar. = infocomplementarvalor.c123_infocomplementar";
     $sql .= "      inner join conplanosistema  on  conplanosistema. = infocomplementarvalor.c123_conplanosistema";
     $sql .= "      inner join conplanoatributolancamentos  on  conplanoatributolancamentos.c124_sequencial = infocomplementarvalor.c123_conplanoatributolancamentos";
     $sql .= "      inner join conlancam  on  conlancam.c70_codlan = conplanoatributolancamentos.c124_lancamento";
     $sql .= "      inner join conplanosistema  as a on   a.c122_sequencial = conplanoatributolancamentos.c124_conplanosistema";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c123_sequencial)) {
         $sql2 .= " where infocomplementarvalor.c123_sequencial = $c123_sequencial ";
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

    public function sql_query_file($c123_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from infocomplementarvalor ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c123_sequencial)){
         $sql2 .= " where infocomplementarvalor.c123_sequencial = $c123_sequencial ";
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



  public function sql_query_infocomplementar_po_by_lancamento($codigoInstituicao)
  {
      $sql = " SELECT codtrib::varchar as infocomplementar_valor FROM db_config WHERE codigo = {$codigoInstituicao}; ";
      return $sql;
  }

  public function sql_query_consulta_POs()
  {
      $sql = " SELECT distinct codtrib::varchar as codtrib, nomeinst as descricao FROM db_config ";
      return $sql;
  }

  public function sql_query_infocomplementar_fp_by_reduzido($codigoreduzido, $ano, $instituicoes)
  {
      $sql = " SELECT c60_naturezasaldo as infocomplementar_valor ";
      $sql .= " FROM conplanoreduz                                 ";
      $sql .= " INNER JOIN conplano ON c61_codcon = c60_codcon     ";
      $sql .= "    AND c61_anousu = c60_anousu                     ";
      $sql .= " WHERE c61_reduz = {$codigoreduzido}                ";
      $sql .= "    AND c61_anousu = {$ano}                         ";
      $sql .= "    AND c61_instit in ({$instituicoes});            ";

      return $sql;
  }

  public function sql_query_consulta_FPs()
  {
      $sql = " SELECT distinct c60_naturezasaldo as c60_naturezasaldo, ";
      $sql .= " 'Superávit Financeiro'::varchar as descricao ";
      $sql .= " from conplano ";

      return $sql;
  }

  public function sql_query_infocomplementar_fs_by_lancamento($codigoLancamento)
  {
      $sql = " SELECT (CASE WHEN c75_codlan IS NOT NULL THEN lpad(dotemp.o58_funcao, 2, '0')::varchar||lpad(dotemp.o58_subfuncao, 3, '0')::varchar ";
      $sql .= "     ELSE lpad(dotlan.o58_funcao, 2, '0')::varchar||lpad(dotlan.o58_subfuncao, 3, '0')::varchar END) AS infocomplementar_valor       ";
      $sql .= " FROM conlancam                                                                                                                      ";
      $sql .= " INNER JOIN conlancamdoc ON c71_codlan = c70_codlan                                                                                  ";
      $sql .= " INNER JOIN conhistdoc ON c53_coddoc = c71_coddoc                                                                                    ";
      $sql .= " LEFT JOIN conlancamemp ON c75_codlan = c70_codlan                                                                                   ";
      $sql .= " LEFT JOIN empempenho ON c75_numemp = e60_numemp                                                                                     ";
      $sql .= " LEFT JOIN orcdotacao dotemp ON e60_coddot = dotemp.o58_coddot                                                                       ";
      $sql .= "     AND e60_anousu = dotemp.o58_anousu                                                                                              ";
      $sql .= " LEFT JOIN conlancamdot ON c73_codlan = c70_codlan                                                                                   ";
      $sql .= " LEFT JOIN orcdotacao dotlan ON c73_coddot = dotlan.o58_coddot                                                                       ";
      $sql .= "     AND c73_anousu = dotlan.o58_anousu                                                                                              ";
      $sql .= " WHERE c70_Codlan = {$codigoLancamento};                                                                                             ";

      return $sql;
  }

  public function sql_query_consulta_FSs()
  {
      $sql = " select distinct (lpad(o58_funcao, 2, '0')||lpad(o58_subfuncao, 3 , '0'))::varchar as Funcao_Subfuncao, '2 Primeiros digitos função / 3 últimos digitos subfunção'::varchar as descricao from orcdotacao ";
      return $sql;
  }

  public function sql_query_infocomplementar_nr_by_lancamento($codigoLancamento)
  {
      $sql = " SELECT orcfontes.o57_fonte AS infocomplementar_valor,            ";
      $sql .= "        orcfontes.o57_descr AS descricao_nr                       ";
      $sql .= " FROM contabilidade.conlancam                                     ";
      $sql .= " INNER JOIN contabilidade.conlancamdoc ON c71_codlan = c70_codlan ";
      $sql .= " INNER JOIN contabilidade.conhistdoc ON c53_coddoc = c71_coddoc   ";
      $sql .= " LEFT JOIN contabilidade.conlancamrec ON c74_codlan = c70_codlan  ";
      $sql .= " LEFT JOIN orcamento.orcreceita ON c74_codrec = o70_codrec        ";
      $sql .= "     AND c74_anousu = o70_anousu                                  ";
      $sql .= " LEFT JOIN orcamento.orcfontes ON o57_codfon = o70_Codfon         ";
      $sql .= "     AND o57_anousu = o70_anousu                                  ";
      $sql .= " WHERE c70_Codlan = {$codigoLancamento};                          ";

      return $sql;
  }

  public function sql_query_consulta_NRs()
  {
      $sql = 'select distinct o57_fonte as infocomplementar_valor, o57_descr as descricao from orcfontes';
      return $sql;
  }

  public function sql_query_infocomplementar_nd_by_lancamento($codigoLancamento)
  {
      $sql = " SELECT (CASE WHEN c75_codlan IS NOT NULL THEN eleemp.o56_elemento::varchar ";
      $sql .= "     ELSE eledot.o56_elemento::varchar END) AS infocomplementar_valor,      ";
      $sql .= "  (CASE WHEN c75_codlan IS NOT NULL THEN eleemp.o56_descr::varchar          ";
      $sql .= "     ELSE eledot.o56_descr::varchar END) AS descricao_nd                    ";
      $sql .= " FROM conlancam                                                             ";
      $sql .= " INNER JOIN conlancamdoc ON c71_codlan = c70_codlan                         ";
      $sql .= " INNER JOIN conhistdoc ON c53_coddoc = c71_coddoc                           ";
      $sql .= " LEFT JOIN conlancamemp ON c75_codlan = c70_codlan                          ";
      $sql .= " LEFT JOIN empempenho ON c75_numemp = e60_numemp                            ";
      $sql .= " LEFT JOIN orcdotacao dotemp ON e60_coddot = dotemp.o58_coddot              ";
      $sql .= "     AND e60_anousu = dotemp.o58_anousu                                     ";
      $sql .= " LEFT JOIN orcelemento eleemp ON dotemp.o58_codele = eleemp.o56_codele      ";
      $sql .= "     AND dotemp.o58_anousu = eleemp.o56_anousu                              ";
      $sql .= " LEFT JOIN conlancamdot ON c73_codlan = c70_codlan                          ";
      $sql .= " LEFT JOIN orcdotacao dotlan ON c73_coddot = dotlan.o58_coddot              ";
      $sql .= "     AND c73_anousu = dotlan.o58_anousu                                     ";
      $sql .= " LEFT JOIN orcelemento eledot ON dotlan.o58_codele = eledot.o56_codele      ";
      $sql .= "     AND dotlan.o58_anousu = eledot.o56_anousu                              ";
      $sql .= " WHERE c70_Codlan = {$codigoLancamento};                                    ";

      return $sql;
  }

  public function sql_query_consulta_NDs()
  {
      $sql = 'select distinct o56_elemento as infocomplementar_valor, o56_descr as descricao from orcelemento';
      return $sql;
  }

  public function sql_query_infocomplementar_fr_by_lancamento($codigoLancamento, $codigoreduzido, $ano)
  {
      if (empty($codigoLancamento)) {
          $sql = " SELECT lpad(c61_codigo::varchar, 4, '0') AS infocomplementar_valor FROM conplanoreduz WHERE c61_reduz = {$codigoreduzido} AND c61_anousu = {$ano} ";

          return $sql;
      }

      $sql = " SELECT (CASE WHEN c75_codlan IS NOT NULL AND c53_tipo in(30, 31) THEN lpad(c61_codigo::varchar, 4, '0')                      ";
      $sql .= "              WHEN c75_codlan IS NOT NULL AND c53_tipo NOT in(30, 31) THEN lpad(dotemp.o58_codigo::varchar, 4, '0')           ";
      $sql .= "              WHEN c73_codlan IS NOT NULL AND c53_tipo NOT in(30, 31) THEN lpad(dotlan.o58_codigo::varchar, 4, '0')           ";
      $sql .= "              WHEN c74_codrec IS NOT NULL and dotrec.o58_codigo is not null THEN lpad(dotrec.o58_codigo::varchar, 4, '0')     ";
      $sql .= "              WHEN c74_codrec IS NOT NULL THEN lpad(o70_codigo::varchar, 4, '0')                                              ";
      $sql .= "              WHEN recursopagdebito.c61_reduz IS NOT NULL THEN lpad(c61_codigo::varchar, 4, '0')                              ";
      $sql .= "              ELSE (SELECT lpad(c61_codigo::varchar, 4, '0')                                                                  ";
      $sql .= "                    FROM conplanoreduz                                                                                        ";
      $sql .= "                    WHERE c61_reduz = {$codigoreduzido}                                                                       ";
      $sql .= "                    AND c61_anousu = {$ano})                                                                                  ";
      $sql .= "         END) AS infocomplementar_valor                                                                                       ";
      $sql .= " FROM conlancam                                                                                                               ";
      $sql .= "       INNER JOIN conlancamdoc ON c71_codlan = c70_codlan                                                                     ";
      $sql .= "       INNER JOIN conhistdoc   ON c53_coddoc = c71_coddoc                                                                     ";
      $sql .= "       LEFT JOIN conlancamemp  ON c75_codlan = c70_codlan                                                                     ";
      $sql .= "       LEFT JOIN empempenho empemp1   ON c75_numemp = empemp1.e60_numemp                                                      ";
      $sql .= "       LEFT JOIN orcdotacao dotemp ON empemp1.e60_coddot = dotemp.o58_coddot                                                  ";
      $sql .= "                                   AND empemp1.e60_anousu = dotemp.o58_anousu                                                 ";
      $sql .= "       LEFT JOIN conlancamdot ON c73_codlan = c70_codlan                                                                      ";
      $sql .= "       LEFT JOIN orcdotacao dotlan ON c73_coddot = dotlan.o58_coddot                                                          ";
      $sql .= "                                   AND c73_anousu = dotlan.o58_anousu                                                         ";
      $sql .= "       LEFT JOIN conlancamrec ON c74_codlan = c70_codlan                                                                      ";
      $sql .= "       LEFT JOIN orcreceita ON c74_codrec = o70_codrec                                                                        ";
      $sql .= "                            AND c74_anousu = o70_anousu                                                                       ";
      $sql .= "       LEFT JOIN conlancampag ON c82_codlan = c70_codlan                                                                      ";
      $sql .= "       LEFT JOIN conplanoreduz AS recursopagdebito ON c82_reduz = recursopagdebito.c61_reduz                                  ";
      $sql .= "                                                   AND c82_anousu = recursopagdebito.c61_anousu                               ";
      $sql .= "       LEFT JOIN conlancamcorrente conlancorr1 ON conlancorr1.c86_conlancam =  c70_codlan                                     ";
      $sql .= "       LEFT JOIN corgrupocorrente corgrpcor1 ON corgrpcor1.k105_data = conlancorr1.c86_data                                  ";
      $sql .= "                                              AND corgrpcor1.k105_autent = conlancorr1.c86_autent                             ";
      $sql .= "                                              AND corgrpcor1.k105_id = conlancorr1.c86_id                                     ";
      $sql .= "                                              AND corgrpcor1.k105_corgrupotipo = 3                                            ";
      $sql .= "       LEFT JOIN corgrupocorrente corgrpcor2 ON corgrpcor2.k105_corgrupo = corgrpcor1.k105_corgrupo                           ";
      $sql .= "                                             AND corgrpcor2.k105_corgrupotipo = 1                                             ";
      $sql .= "       LEFT JOIN coremp ON  k12_id     = corgrpcor2.k105_id                                                                   ";
      $sql .= "                        AND k12_data   = corgrpcor2.k105_data                                                                 ";
      $sql .= "                        AND k12_autent = corgrpcor2.k105_autent                                                               ";
      $sql .= "       LEFT JOIN empempenho empemp2 ON  k12_empen = empemp2.e60_numemp                                                        ";
      $sql .= "       LEFT JOIN orcdotacao dotrec  ON empemp2.e60_coddot = dotrec.o58_coddot                                                 ";
      $sql .= "                                    AND empemp2.e60_anousu = dotrec.o58_anousu
      ";
      $sql .= " WHERE c70_Codlan = {$codigoLancamento};                                                                                      ";

      return $sql;
  }

  public function sql_query_consulta_FRs()
  {
      $sql = " SELECT lpad(o15_codigo::varchar, 4, '0')::varchar AS o15_codigo, o15_descr as descricao FROM orctiporec ";
      return $sql;
  }

  public function sql_query_infocomplementar_dc_by_estrutural($codigoConta, $ano)
  {
      $sql = " select (CASE WHEN c60_codsis = 9 THEN 0 ELSE 1 END) AS infocomplementar_valor  from conplano where c60_codcon = {$codigoConta} and  c60_anousu = {$ano} ";

      return $sql;
  }

  public function sql_query_consulta_DCs()
  {
      $sql = " select distinct c52_codsis AS infocomplementar_valor, c52_descr as descricao from consistema ";

      return $sql;
  }

  /**
   * @param string $campos
   * @param null $where
   * @param null $order
   *
   * @return string
   */
  public function sql_query_lancamento($campos = '*', $where = null, $order = null)
  {
      $sql = "select {$campos} ";
      $sql .= "  from infocomplementarvalor ";
      $sql .= "       inner join conplanoatributolancamentos on c123_conplanoatributolancamentos = c124_sequencial ";
      $sql .= "       inner join conlancamdoc  on c124_lancamento = c71_codlan ";
      $sql .= "       inner join conhistdoc    on c71_coddoc = c53_coddoc  ";
      $sql .= "       inner join conplanoreduz on c123_reduzido = c61_reduz ";
      $sql .= "                               and extract(year from c124_data)::int = c61_anousu ";
      $sql .= "       inner join conplano      on c61_codcon = c60_codcon ";
      $sql .= "                               and c61_anousu = c60_anousu ";

      if (!empty($where)) {
          $sql .= "where {$where}";
      }
      if (!empty($order)) {
          $sql .= "where {$order}";
      }

      return $sql;
  }

}
