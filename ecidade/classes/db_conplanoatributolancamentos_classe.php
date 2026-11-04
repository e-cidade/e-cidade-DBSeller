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
class cl_conplanoatributolancamentos
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
    public $c124_sequencial = 0;
    public $c124_lancamento = null;
    public $c124_natureza = null;
    public $c124_tipo = null;
    public $c124_valor = 0;
    public $c124_data_dia = null;
    public $c124_data_mes = null;
    public $c124_data_ano = null;
    public $c124_data = null;
    public $c124_conplanosistema = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c124_sequencial = int4 = Sequencial
                 c124_lancamento = int4 = Código lançamento
                 c124_natureza = char(1) = Natureza
                 c124_tipo = varchar(40) = Tipo
                 c124_valor = float8 = Valor
                 c124_data = date = Data
                 c124_conplanosistema = int4 = Código do Sistema
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("conplanoatributolancamentos");
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
       $this->c124_sequencial = ($this->c124_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_sequencial"]:$this->c124_sequencial);
       $this->c124_lancamento = ($this->c124_lancamento == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_lancamento"]:$this->c124_lancamento);
       $this->c124_natureza = ($this->c124_natureza == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_natureza"]:$this->c124_natureza);
       $this->c124_tipo = ($this->c124_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_tipo"]:$this->c124_tipo);
       $this->c124_valor = ($this->c124_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_valor"]:$this->c124_valor);
       if($this->c124_data == ""){
         $this->c124_data_dia = ($this->c124_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_data_dia"]:$this->c124_data_dia);
         $this->c124_data_mes = ($this->c124_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_data_mes"]:$this->c124_data_mes);
         $this->c124_data_ano = ($this->c124_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_data_ano"]:$this->c124_data_ano);
         if($this->c124_data_dia != ""){
            $this->c124_data = $this->c124_data_ano."-".$this->c124_data_mes."-".$this->c124_data_dia;
         }
       }
       $this->c124_conplanosistema = ($this->c124_conplanosistema == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_conplanosistema"]:$this->c124_conplanosistema);
     }else{
       $this->c124_sequencial = ($this->c124_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c124_sequencial"]:$this->c124_sequencial);
     }
   }

    public function incluir($c124_sequencial)
    {
      $this->atualizacampos();

     if($this->c124_lancamento == null ||  $this->c124_lancamento == ""){
        $this->c124_lancamento = "null";
     }
     if($this->c124_natureza == null ){
       $this->erro_sql = " Campo Natureza não informado.";
       $this->erro_campo = "c124_natureza";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c124_tipo == null ){
       $this->erro_sql = " Campo Tipo não informado.";
       $this->erro_campo = "c124_tipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c124_valor == null ){
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "c124_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c124_data == null ){
       $this->erro_sql = " Campo Data não informado.";
       $this->erro_campo = "c124_data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c124_conplanosistema == null ){
       $this->erro_sql = " Campo Código do Sistema não informado.";
       $this->erro_campo = "c124_conplanosistema";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c124_sequencial == "" || $c124_sequencial == null ){
       $result = db_query("select nextval('conplanoatributolancamentos_c124_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: conplanoatributolancamentos_c124_sequencial_seq do campo: c124_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->c124_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from conplanoatributolancamentos_c124_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $c124_sequencial)){
         $this->erro_sql = " Campo c124_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->c124_sequencial = $c124_sequencial;
       }
     }
     if(($this->c124_sequencial == null) || ($this->c124_sequencial == "") ){
       $this->erro_sql = " Campo c124_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into conplanoatributolancamentos(
                                       c124_sequencial
                                      ,c124_lancamento
                                      ,c124_natureza
                                      ,c124_tipo
                                      ,c124_valor
                                      ,c124_data
                                      ,c124_conplanosistema
                       )
                values (
                                $this->c124_sequencial
                               ,$this->c124_lancamento
                               ,'$this->c124_natureza'
                               ,'$this->c124_tipo'
                               ,$this->c124_valor
                               ,".($this->c124_data == "null" || $this->c124_data == ""?"null":"'".$this->c124_data."'")."
                               ,$this->c124_conplanosistema
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Lançamentos ($this->c124_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Lançamentos já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Lançamentos ($this->c124_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c124_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c124_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009618,'$this->c124_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010259,1009618,'','".AddSlashes(pg_result($resaco,0,'c124_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010259,1009626,'','".AddSlashes(pg_result($resaco,0,'c124_lancamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010259,1009619,'','".AddSlashes(pg_result($resaco,0,'c124_natureza'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010259,1009620,'','".AddSlashes(pg_result($resaco,0,'c124_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010259,1009621,'','".AddSlashes(pg_result($resaco,0,'c124_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010259,1009633,'','".AddSlashes(pg_result($resaco,0,'c124_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010259,1010098,'','".AddSlashes(pg_result($resaco,0,'c124_conplanosistema'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($c124_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update conplanoatributolancamentos set ";
     $virgula = "";
     if(trim($this->c124_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c124_sequencial"])){
       $sql  .= $virgula." c124_sequencial = $this->c124_sequencial ";
       $virgula = ",";
       if(trim($this->c124_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "c124_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c124_lancamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c124_lancamento"])){
       $sql  .= $virgula." c124_lancamento = $this->c124_lancamento ";
       $virgula = ",";
       if(trim($this->c124_lancamento) == null || $this->c124_lancamento == "" ){
        $this->c124_lancamento = "null";
       }
     }
     if(trim($this->c124_natureza)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c124_natureza"])){
       $sql  .= $virgula." c124_natureza = '$this->c124_natureza' ";
       $virgula = ",";
       if(trim($this->c124_natureza) == null ){
         $this->erro_sql = " Campo Natureza não informado.";
         $this->erro_campo = "c124_natureza";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c124_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c124_tipo"])){
       $sql  .= $virgula." c124_tipo = '$this->c124_tipo' ";
       $virgula = ",";
       if(trim($this->c124_tipo) == null ){
         $this->erro_sql = " Campo Tipo não informado.";
         $this->erro_campo = "c124_tipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c124_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c124_valor"])){
       $sql  .= $virgula." c124_valor = $this->c124_valor ";
       $virgula = ",";
       if(trim($this->c124_valor) == null ){
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "c124_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c124_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c124_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["c124_data_dia"] !="") ){
       $sql  .= $virgula." c124_data = '$this->c124_data' ";
       $virgula = ",";
       if(trim($this->c124_data) == null ){
         $this->erro_sql = " Campo Data não informado.";
         $this->erro_campo = "c124_data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["c124_data_dia"])){
         $sql  .= $virgula." c124_data = null ";
         $virgula = ",";
         if(trim($this->c124_data) == null ){
           $this->erro_sql = " Campo Data não informado.";
           $this->erro_campo = "c124_data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->c124_conplanosistema)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c124_conplanosistema"])){
       $sql  .= $virgula." c124_conplanosistema = $this->c124_conplanosistema ";
       $virgula = ",";
       if(trim($this->c124_conplanosistema) == null ){
         $this->erro_sql = " Campo Código do Sistema não informado.";
         $this->erro_campo = "c124_conplanosistema";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c124_sequencial!=null){
       $sql .= " c124_sequencial = $this->c124_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->c124_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009618,'$this->c124_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c124_sequencial"]) || $this->c124_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010259,1009618,'".AddSlashes(pg_result($resaco,$conresaco,'c124_sequencial'))."','$this->c124_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c124_lancamento"]) || $this->c124_lancamento != "")
             $resac = db_query("insert into db_acount values($acount,1010259,1009626,'".AddSlashes(pg_result($resaco,$conresaco,'c124_lancamento'))."','$this->c124_lancamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c124_natureza"]) || $this->c124_natureza != "")
             $resac = db_query("insert into db_acount values($acount,1010259,1009619,'".AddSlashes(pg_result($resaco,$conresaco,'c124_natureza'))."','$this->c124_natureza',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c124_tipo"]) || $this->c124_tipo != "")
             $resac = db_query("insert into db_acount values($acount,1010259,1009620,'".AddSlashes(pg_result($resaco,$conresaco,'c124_tipo'))."','$this->c124_tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c124_valor"]) || $this->c124_valor != "")
             $resac = db_query("insert into db_acount values($acount,1010259,1009621,'".AddSlashes(pg_result($resaco,$conresaco,'c124_valor'))."','$this->c124_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c124_data"]) || $this->c124_data != "")
             $resac = db_query("insert into db_acount values($acount,1010259,1009633,'".AddSlashes(pg_result($resaco,$conresaco,'c124_data'))."','$this->c124_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["c124_conplanosistema"]) || $this->c124_conplanosistema != "")
             $resac = db_query("insert into db_acount values($acount,1010259,1010098,'".AddSlashes(pg_result($resaco,$conresaco,'c124_conplanosistema'))."','$this->c124_conplanosistema',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Lançamentos não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->c124_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Lançamentos não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->c124_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->c124_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($c124_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($c124_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009618,'$c124_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010259,1009618,'','".AddSlashes(pg_result($resaco,$iresaco,'c124_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010259,1009626,'','".AddSlashes(pg_result($resaco,$iresaco,'c124_lancamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010259,1009619,'','".AddSlashes(pg_result($resaco,$iresaco,'c124_natureza'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010259,1009620,'','".AddSlashes(pg_result($resaco,$iresaco,'c124_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010259,1009621,'','".AddSlashes(pg_result($resaco,$iresaco,'c124_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010259,1009633,'','".AddSlashes(pg_result($resaco,$iresaco,'c124_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010259,1010098,'','".AddSlashes(pg_result($resaco,$iresaco,'c124_conplanosistema'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from conplanoatributolancamentos
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($c124_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " c124_sequencial = $c124_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Lançamentos não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$c124_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Lançamentos não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$c124_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$c124_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:conplanoatributolancamentos";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($c124_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from conplanoatributolancamentos ";
     $sql .= "      inner join conlancam  on  conlancam.c70_codlan = conplanoatributolancamentos.c124_lancamento";
     $sql .= "      inner join conplanosistema  on  conplanosistema.c122_sequencial = conplanoatributolancamentos.c124_conplanosistema";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c124_sequencial)) {
         $sql2 .= " where conplanoatributolancamentos.c124_sequencial = $c124_sequencial ";
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

    public function sql_query_file($c124_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from conplanoatributolancamentos ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($c124_sequencial)){
         $sql2 .= " where conplanoatributolancamentos.c124_sequencial = $c124_sequencial ";
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
     * Retorna todos os lancamentos da matriz
     *
     * @todo modificar join com as tabelas conplanoatributos. Realizar o join com a tabela infocomplementarvalor com os campos novos.
     * @param $mes
     * @param $ano
     * @param $instituicoes
     * @return string
     */
    public function sql_query_lancamentos($mes, $ano, $instituicoes, $encerramento = null)
    {

        $ultimoDiaMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        $where = "";

        if ($encerramento === false) {
            $where = "   AND (c53_tipo <> 1000 or c53_tipo is null)";
        }

        if ($mes == 12 && $encerramento === true) {
            $where = ' and c53_tipo = 1000 ';
        }


        $sql = " SELECT distinct conplanoatributolancamentos.c124_sequencial AS sequencial, ";
        $sql .= "        conplano.c60_estrut AS estrutura, ";
        $sql .= "        pcasp.conta AS estrutura_pcasp, ";
        $sql .= "        conplano.c60_codcon AS conta, ";
        $sql .= "        conlancamval.c69_anousu AS anousu, ";
        $sql .= "        infocomplementarvalor.c123_valor AS valor, ";
        $sql .= "        infocomplementarvalor.c123_conplanosistema AS sistema, ";
        $sql .= "        conplanoinfocomplementar.c121_sequencial AS codigo_infocomplementar, ";
        $sql .= "        conplanoinfocomplementar.c121_sigla AS sigla, ";
        $sql .= "        conplanoinfocomplementar.c121_descricao AS descricao, ";
        $sql .= "        conplanoatributolancamentos.c124_lancamento AS codigo_lancamento, ";
        $sql .= "        conplanoatributolancamentos.c124_valor AS valor_lancamento, ";
        $sql .= "        conplanoatributolancamentos.c124_tipo AS tipo, ";
        $sql .= "        conplanoatributolancamentos.c124_natureza AS natureza, ";
        $sql .= "        conplanoatributolancamentos.c124_data AS data_lancamento, ";
        $sql .= "        infocomplementarvalor.c123_reduzido as conta_reduzida ";
        $sql .= "   FROM infocomplementarvalor ";
        $sql .= "   join conplanoreduz on c61_reduz = c123_reduzido and c61_anousu = {$ano}";
        $sql .= "   join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu ";
        $sql .= "   join contabilidade.pcaspconplano on conplano_codigo = conplano.c60_codigo ";
        $sql .= "   join contabilidade.pcasp on pcasp.id = pcaspconplano.pcasp_id and pcasp.uniao is true";
        $sql .= "   JOIN conplanoinfocomplementar ON conplanoinfocomplementar.c121_sequencial = infocomplementarvalor.c123_infocomplementar ";
        $sql .= "   JOIN conplanoatributolancamentos ON conplanoatributolancamentos.c124_sequencial = infocomplementarvalor.c123_conplanoatributolancamentos ";
        $sql .= "   JOIN conlancamval ON conlancamval.c69_codlan = conplanoatributolancamentos.c124_lancamento ";
        $sql .= "   LEFT JOIN contabilidade.conlancamdoc ON c71_codlan = c69_codlan ";
        $sql .= "   LEFT JOIN contabilidade.conhistdoc ON c71_coddoc = c53_coddoc ";
        $sql .= "   JOIN conlancaminstit ON conlancaminstit.c02_codlan = conlancamval.c69_codlan ";
        $sql .= " WHERE conlancaminstit.c02_instit IN({$instituicoes}) ";
        $sql .= "   AND c69_data between '{$ano}-{$mes}-01' and '{$ano}-{$mes}-{$ultimoDiaMes}' and c124_conplanosistema = 1";
        $sql .= $where;

        $sql .= " union all ";

        $sql .= " SELECT DISTINCT conplanoatributolancamentos.c124_sequencial AS sequencial, ";
        $sql .= "        conplano.c60_estrut AS estrutura, ";
        $sql .= "        pcasp.conta AS estrutura_pcasp, ";
        $sql .= "        conplano.c60_codcon AS conta, ";
        $sql .= "        conplano.c60_anousu AS anousu, ";
        $sql .= "        infocomplementarvalor.c123_valor AS valor, ";
        $sql .= "        infocomplementarvalor.c123_conplanosistema AS sistema, ";
        $sql .= "        conplanoinfocomplementar.c121_sequencial AS codigo_infocomplementar, ";
        $sql .= "        conplanoinfocomplementar.c121_sigla AS sigla, ";
        $sql .= "        conplanoinfocomplementar.c121_descricao AS descricao, ";
        $sql .= "        conplanoatributolancamentos.c124_lancamento AS codigo_lancamento, ";
        $sql .= "        conplanoatributolancamentos.c124_valor AS valor_lancamento, ";
        $sql .= "        conplanoatributolancamentos.c124_tipo AS tipo, ";
        $sql .= "        conplanoatributolancamentos.c124_natureza AS natureza, ";
        $sql .= "        conplanoatributolancamentos.c124_data AS data_lancamento, ";
        $sql .= "        infocomplementarvalor.c123_reduzido as conta_reduzida ";
        $sql .= "   FROM infocomplementarvalor ";
        $sql .= "   join conplanoatributolancamentos on c123_conplanoatributolancamentos = c124_sequencial ";
        $sql .= "   LEFT JOIN contabilidade.conlancamdoc ON c71_codlan = c124_lancamento ";
        $sql .= "   LEFT JOIN contabilidade.conhistdoc ON c71_coddoc = c53_coddoc ";
        $sql .= "   join conplanoreduz on c61_reduz = c123_reduzido and c61_anousu = {$ano}";
        $sql .= "   join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu ";
        $sql .= "   join contabilidade.pcaspconplano on conplano_codigo = conplano.c60_codigo ";
        $sql .= "   join contabilidade.pcasp on pcasp.id = pcaspconplano.pcasp_id and pcasp.uniao is true";
        $sql .= "   join conplanoinfocomplementar on c123_infocomplementar = c121_sequencial ";
        $sql .= " where c124_tipo = '1' ";
        $sql .= "   and extract(month from c124_data) = {$mes} ";
        $sql .= "   and extract(year from c124_data) = {$ano} ";
        $sql .= "   and c61_instit in ({$instituicoes}) ";
        $sql .= "   and c124_conplanosistema = 1";
        $sql .= $where;
        $sql .= " order by sistema, estrutura,sequencial,codigo_lancamento,codigo_infocomplementar ";

        return $sql;
    }

    public function sql_query_delete_lancamentos_by_competencia($mes, $ano, $instituicoes)
    {

        $sql = " select distinct c124_sequencial AS sequencial from conplanoatributolancamentos ";
        $sql .= " inner join conlancamval    on c69_codlan = c124_lancamento ";
        $sql .= " inner join conlancaminstit on c02_codlan = c124_lancamento ";
        $sql .= " where extract(month from c69_data) = {$mes} ";
        $sql .= " and c69_anousu = {$ano} ";
        $sql .= " and c02_instit in ({$instituicoes}) ";

        return $sql;
    }

    /**
     * Remove os registos da matriz
     *
     * @todo - Remover join com a tabela conplanoatributos
     * @param       $mes
     * @param       $ano
     * @param array $instituicoes
     * @param array $tiposDocumentos
     * @return bool
     * @throws Exception
     */
    public function removerLancametosCompetencia(
        $mes,
        $ano,
        array $instituicoes,
        $SistemaContaCorrente = 1,
        array $listaLancamentos = null,
        array $tiposDocumentos = array()
    ) {

        $codigoInstituicoes = array();
        foreach ($instituicoes as $instituicao) {
            $codigoInstituicoes[] = $instituicao->getCodigo();
        }

        $whereListaLancamentos = '';
        if (is_array($listaLancamentos) && count($listaLancamentos) > 0) {
            $whereListaLancamentos = " and " . implode(",", $listaLancamentos);
        }

        if (!empty($SistemaContaCorrente)) {
            $whereListaLancamentos .= " and c123_conplanosistema = {$SistemaContaCorrente}";
        }

        $iUltimoDiaMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        $sDataInicial = "{$ano}-{$mes}-01";
        $sDataFinal = "{$ano}-{$mes}-{$iUltimoDiaMes}";

        $sqlInfo = " delete
                       from infocomplementarvalor
                      using conplanoatributolancamentos,
                            conlancaminstit,
                            conlancamdoc,
                            conhistdoc
                      where c123_conplanoatributolancamentos = c124_sequencial
                            {$whereListaLancamentos}
                        and c124_lancamento = c02_codlan
                        and conlancamdoc.c71_codlan = conplanoatributolancamentos.c124_lancamento
                        and conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
                        and c124_data between '" . $sDataInicial . "' and '" . $sDataFinal . "'
                        and c02_instit in (" . implode(", ", $codigoInstituicoes) . ") ";

        $sqlInfo .= !empty($tiposDocumentos) ? " and conhistdoc.c53_tipo in (".implode(', ', $tiposDocumentos).") " : '';

        $rsInfo = db_query($sqlInfo);
        if (!$rsInfo) {
            throw new \Exception("Erro ao excluir as informações complementares dos lançamentos da competência: {$mes}/{$ano}.");
        }

        /**
         * PROVISORIO
         * pois foi removido e gerou erro nos cliente, logo vamos alterar o delete de cima e de baixo
         */
        $sqlDesabilitaTriggers = "
          alter table conplanoatributolancamentos
            disable trigger all;
        ";
        $rsDesabilitaTriggers = db_query($sqlDesabilitaTriggers);

        if (!$rsDesabilitaTriggers) {
            throw new \Exception('Não foi possível desabilitar as triggers da tabela "conplanoatributolancamentos".');
        }


        $sqlAtributos = " delete
                             from conplanoatributolancamentos
                            using conlancaminstit,
                                  conlancamdoc,
                                  conhistdoc
                            where c124_data between '" . $sDataInicial . "' and '" . $sDataFinal . "'
                              and conlancamdoc.c71_codlan = conplanoatributolancamentos.c124_lancamento
                              and conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
                              and c124_lancamento = c02_codlan ";

        $sqlAtributos .= !empty($tiposDocumentos) ? " and conhistdoc.c53_tipo in (".implode(', ', $tiposDocumentos).") " : '';

        if (!empty($SistemaContaCorrente)) {
            $sqlAtributos .= " and c124_conplanosistema = {$SistemaContaCorrente}";
        }

        $rsAtributos = db_query($sqlAtributos);
        if (!$rsAtributos) {
            throw new \Exception("Erro ao excluir os atributos dos lançamentos da competência: {$mes}/{$ano}.");
        }


        if ($SistemaContaCorrente == 1) {

            $mes = !empty($tiposDocumentos) ? 13 : $mes;

            $sqlSaldo = " delete from conplanoatributosaldo
                           where c125_anousu = {$ano}
                             and c125_mesusu = {$mes}
                             and c125_conplanosistema = {$SistemaContaCorrente} ";


            $rsSaldo = db_query($sqlSaldo);
            if (!$rsSaldo) {
                throw new \Exception("Erro ao excluir os saldos das contas para a competência: {$mes}/{$ano}.");
            }
            /**
             * Devemos remover todo o saldo inicial calculado para as institicuoes
             *
             * Selecionamos todos os registros de saldo e inicial e removemos os mesmos. devemos manter os registros apenas no processamento
             */
            $sqlRemoverSaldoInicialAno  = "create temp table w_apagar_valores_atributo as ";
            $sqlRemoverSaldoInicialAno .= "select distinct c124_sequencial ";
            $sqlRemoverSaldoInicialAno .= "  from infocomplementarvalor ";
            $sqlRemoverSaldoInicialAno .= "       inner join conplanoatributolancamentos on c123_conplanoatributolancamentos = c124_sequencial ";
            $sqlRemoverSaldoInicialAno .= " where c124_lancamento is null ";
            $sqlRemoverSaldoInicialAno .= "   and extract(year from c124_data) = {$ano} ";
            $sqlRemoverSaldoInicialAno .= "  and c124_tipo = '1' ";
            $sqlRemoverSaldoInicialAno .= "  and c124_conplanosistema = 1 ";

            $sqlRemoverSaldoInicialAno .= ";create index w_apagar_valores_atributo_sequencial_in on w_apagar_valores_atributo(c124_sequencial); ";
            $rsTabelaAuxiliar = db_query($sqlRemoverSaldoInicialAno);
            if (!$rsTabelaAuxiliar) {
                throw new \Exception("Erro ao gerar dados para remocao dos registros de saldo inicial do {$ano}.");
            }

            $sqlRemoverAtributosSaldoInicial  = "delete from infocomplementarvalor ";
            $sqlRemoverAtributosSaldoInicial .= " where c123_conplanoatributolancamentos in(select c124_sequencial from w_apagar_valores_atributo)";
            $rsRemoverAtributosSaldoInicial  = db_query($sqlRemoverAtributosSaldoInicial);
            if (!$rsRemoverAtributosSaldoInicial) {
                throw new \Exception("Erro ao remover dados de atributos gerados do saldo inicial do {$ano}.");
            }

            $sqlRemoverAtributosSaldoInicial  = "delete from conplanoatributolancamentos ";
            $sqlRemoverAtributosSaldoInicial .= " where c124_sequencial in(select c124_sequencial from w_apagar_valores_atributo)";
            $rsRemoverAtributosSaldoInicial  = db_query($sqlRemoverAtributosSaldoInicial);
            if (!$rsRemoverAtributosSaldoInicial) {
                throw new \Exception("Erro ao remover dados de lançamentos  gerados na MSC do saldo inicial do {$ano}.");
            }
        }

        return true;
    }

    public function sql_query_contas_inconsistentes($ano)
    {

        $sSql = " SELECT COUNT(*) AS quantidade ";
        $sSql .= " FROM ";
        $sSql .= " ( SELECT c61_codcon AS codigo_conta ";
        $sSql .= " FROM conplanoreduz ";
        $sSql .= " WHERE c61_anousu = {$ano} ";
        $sSql .= " AND c61_reduz IN ";
        $sSql .= " ( SELECT DISTINCT c69_credito ";
        $sSql .= " FROM ";
        $sSql .= " (SELECT c69_credito ";
        $sSql .= "    FROM conlancamval ";
        $sSql .= "    WHERE c69_anousu = {$ano} ";
        $sSql .= "    UNION ALL SELECT c69_debito ";
        $sSql .= "    FROM conlancamval ";
        $sSql .= "    WHERE c69_anousu = {$ano} ) AS x ) ) AS y ";
        $sSql .= " WHERE codigo_conta NOT IN ";
        $sSql .= " (SELECT c120_conplano ";
        $sSql .= " FROM conplanoatributos ";
        $sSql .= " WHERE c120_anousu = {$ano}); ";

        return $sSql;
    }

    public function inserirSaldoContaAtributo($mes, $ano, $hashContaAtributos, $valor, $natureza, $tipo, $sistema = 1, $tipoSaldo)
    {

        $sql = " insert into conplanoatributosaldo (c125_sequencial, c125_anousu, c125_mesusu, c125_hashcontaatributos, ";
        $sql .= "                                   c125_valor, c125_natureza, c125_tipo, c125_conplanosistema, c125_tiposaldo) ";
        $sql .= " values (nextval('conplanoatributosaldo_c125_sequencial_seq'), {$ano}, {$mes}, '{$hashContaAtributos}', ";
        $sql .= "{$valor}, '{$natureza}', {$tipo}, {$sistema}, {$tipoSaldo}) ";

        $rsSql = db_query($sql);
        if (!$rsSql) {
            throw new \Exception("Erro ao inserir o saldo para a conta e atributos {$hashContaAtributos} para a competência: {$mes}/{$ano}.");
        }

        return true;
    }

    public function sql_query_valores_conplanoexe($sCampos_1, $sCampos_2, $sWhere, $sWhere2)
    {

        $sql = " SELECT $sCampos_1 ";
        $sql .= "   FROM conplanoexe ";
        $sql .= "        INNER JOIN contabilidade.conplanoreduz ON c61_reduz = c62_reduz ";
        $sql .= "                                              AND c61_anousu = c62_anousu ";
        $sql .= "        INNER JOIN contabilidade.conplanoatributos ON c120_conplano = c61_codcon ";
        $sql .= "                                                  AND c120_anousu = c61_anousu ";
        $sql .= " INNER JOIN contabilidade.conplano ON c60_codcon = c120_conplano ";
        $sql .= " AND c60_anousu = c120_anousu ";

        if (!empty($sWhere)) {
            $sql .= " where {$sWhere} ";
        }

        $sql .= " union all ";
        $sql .= " SELECT $sCampos_2 ";
        $sql .= " FROM conplanoexe ";
        $sql .= " INNER JOIN contabilidade.conplanoreduz ON c61_reduz = c62_reduz ";
        $sql .= " AND c61_anousu = c62_anousu ";
        $sql .= " INNER JOIN contabilidade.conplanoatributos ON c120_conplano = c61_codcon ";
        $sql .= " AND c120_anousu = c61_anousu ";
        $sql .= " INNER JOIN contabilidade.conplano ON c60_codcon = c120_conplano ";
        $sql .= " AND c60_anousu = c120_anousu ";

        if (!empty($sWhere2)) {
            $sql .= " where {$sWhere2} ";
        }

        return $sql;
    }
}
