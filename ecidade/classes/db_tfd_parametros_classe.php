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

class cl_tfd_parametros
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
    public $tf11_i_codigo = 0;
    public $tf11_i_utilizagradehorario = 0;
    public $tf11_i_campofoco = 0;
    public $tf11_especmedico = 0;
    public $tf11_obriga_hora_saida = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 tf11_i_codigo = int4 = Código
                 tf11_i_utilizagradehorario = int4 = Utiliza Grade de Horário
                 tf11_i_campofoco = int4 = Foco no Pedido de TFD
                 tf11_especmedico = int4 = Especmedico
                 tf11_obriga_hora_saida = bool = Hora Saída obrigatório no Agendamento
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("tfd_parametros");
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
       $this->tf11_i_codigo = ($this->tf11_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["tf11_i_codigo"]:$this->tf11_i_codigo);
       $this->tf11_i_utilizagradehorario = ($this->tf11_i_utilizagradehorario == ""?@$GLOBALS["HTTP_POST_VARS"]["tf11_i_utilizagradehorario"]:$this->tf11_i_utilizagradehorario);
       $this->tf11_i_campofoco = ($this->tf11_i_campofoco == ""?@$GLOBALS["HTTP_POST_VARS"]["tf11_i_campofoco"]:$this->tf11_i_campofoco);
       $this->tf11_especmedico = ($this->tf11_especmedico == ""?@$GLOBALS["HTTP_POST_VARS"]["tf11_especmedico"]:$this->tf11_especmedico);
       $this->tf11_obriga_hora_saida = ($this->tf11_obriga_hora_saida == "f"?@$GLOBALS["HTTP_POST_VARS"]["tf11_obriga_hora_saida"]:$this->tf11_obriga_hora_saida);
     }else{
       $this->tf11_i_codigo = ($this->tf11_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["tf11_i_codigo"]:$this->tf11_i_codigo);
     }
   }

    public function incluir($tf11_i_codigo)
    {
      $this->atualizacampos();
     if($this->tf11_i_utilizagradehorario == null ){
       $this->erro_sql = " Campo Utiliza Grade de Horário não informado.";
       $this->erro_campo = "tf11_i_utilizagradehorario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->tf11_i_campofoco == null ){
       $this->erro_sql = " Campo Foco no Pedido de TFD não informado.";
       $this->erro_campo = "tf11_i_campofoco";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->tf11_especmedico == null ){
       $this->tf11_especmedico = "null";
     }
     if($this->tf11_obriga_hora_saida == null ){
       $this->erro_sql = " Campo Hora Saída obrigatório no Agendamento não informado.";
       $this->erro_campo = "tf11_obriga_hora_saida";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($tf11_i_codigo == "" || $tf11_i_codigo == null ){
       $result = db_query("select nextval('tfd_parametros_tf11_i_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: tfd_parametros_tf11_i_codigo_seq do campo: tf11_i_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->tf11_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from tfd_parametros_tf11_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $tf11_i_codigo)){
         $this->erro_sql = " Campo tf11_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->tf11_i_codigo = $tf11_i_codigo;
       }
     }
     if(($this->tf11_i_codigo == null) || ($this->tf11_i_codigo == "") ){
       $this->erro_sql = " Campo tf11_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into tfd_parametros(
                                       tf11_i_codigo
                                      ,tf11_i_utilizagradehorario
                                      ,tf11_i_campofoco
                                      ,tf11_especmedico
                                      ,tf11_obriga_hora_saida
                       )
                values (
                                $this->tf11_i_codigo
                               ,$this->tf11_i_utilizagradehorario
                               ,$this->tf11_i_campofoco
                               ,$this->tf11_especmedico
                               ,'$this->tf11_obriga_hora_saida'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "tfd_parametros ($this->tf11_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "tfd_parametros já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "tfd_parametros ($this->tf11_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->tf11_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->tf11_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,16371,'$this->tf11_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2867,16371,'','".AddSlashes(pg_result($resaco,0,'tf11_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2867,16372,'','".AddSlashes(pg_result($resaco,0,'tf11_i_utilizagradehorario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2867,17592,'','".AddSlashes(pg_result($resaco,0,'tf11_i_campofoco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2867,18250,'','".AddSlashes(pg_result($resaco,0,'tf11_especmedico'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2867,1014316,'','".AddSlashes(pg_result($resaco,0,'tf11_obriga_hora_saida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($tf11_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update tfd_parametros set ";
     $virgula = "";
     if(trim($this->tf11_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tf11_i_codigo"])){
       $sql  .= $virgula." tf11_i_codigo = $this->tf11_i_codigo ";
       $virgula = ",";
       if(trim($this->tf11_i_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "tf11_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tf11_i_utilizagradehorario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tf11_i_utilizagradehorario"])){
       $sql  .= $virgula." tf11_i_utilizagradehorario = $this->tf11_i_utilizagradehorario ";
       $virgula = ",";
       if(trim($this->tf11_i_utilizagradehorario) == null ){
         $this->erro_sql = " Campo Utiliza Grade de Horário não informado.";
         $this->erro_campo = "tf11_i_utilizagradehorario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tf11_i_campofoco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tf11_i_campofoco"])){
       $sql  .= $virgula." tf11_i_campofoco = $this->tf11_i_campofoco ";
       $virgula = ",";
       if(trim($this->tf11_i_campofoco) == null ){
         $this->erro_sql = " Campo Foco no Pedido de TFD não informado.";
         $this->erro_campo = "tf11_i_campofoco";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tf11_especmedico)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tf11_especmedico"])){
        if(trim($this->tf11_especmedico)=="" && isset($GLOBALS["HTTP_POST_VARS"]["tf11_especmedico"])){
           $this->tf11_especmedico = "null" ;
        }
       $sql  .= $virgula." tf11_especmedico = $this->tf11_especmedico ";
       $virgula = ",";
     }
     if(trim($this->tf11_obriga_hora_saida)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tf11_obriga_hora_saida"])){
       $sql  .= $virgula." tf11_obriga_hora_saida = '$this->tf11_obriga_hora_saida' ";
       $virgula = ",";
       if(trim($this->tf11_obriga_hora_saida) == null ){
         $this->erro_sql = " Campo Hora Saída obrigatório no Agendamento não informado.";
         $this->erro_campo = "tf11_obriga_hora_saida";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($tf11_i_codigo!=null){
       $sql .= " tf11_i_codigo = $this->tf11_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->tf11_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,16371,'$this->tf11_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["tf11_i_codigo"]) || $this->tf11_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2867,16371,'".AddSlashes(pg_result($resaco,$conresaco,'tf11_i_codigo'))."','$this->tf11_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["tf11_i_utilizagradehorario"]) || $this->tf11_i_utilizagradehorario != "")
             $resac = db_query("insert into db_acount values($acount,2867,16372,'".AddSlashes(pg_result($resaco,$conresaco,'tf11_i_utilizagradehorario'))."','$this->tf11_i_utilizagradehorario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["tf11_i_campofoco"]) || $this->tf11_i_campofoco != "")
             $resac = db_query("insert into db_acount values($acount,2867,17592,'".AddSlashes(pg_result($resaco,$conresaco,'tf11_i_campofoco'))."','$this->tf11_i_campofoco',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["tf11_especmedico"]) || $this->tf11_especmedico != "")
             $resac = db_query("insert into db_acount values($acount,2867,18250,'".AddSlashes(pg_result($resaco,$conresaco,'tf11_especmedico'))."','$this->tf11_especmedico',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["tf11_obriga_hora_saida"]) || $this->tf11_obriga_hora_saida != "")
             $resac = db_query("insert into db_acount values($acount,2867,1014316,'".AddSlashes(pg_result($resaco,$conresaco,'tf11_obriga_hora_saida'))."','$this->tf11_obriga_hora_saida',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "tfd_parametros não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->tf11_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "tfd_parametros não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->tf11_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->tf11_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($tf11_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($tf11_i_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,16371,'$tf11_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2867,16371,'','".AddSlashes(pg_result($resaco,$iresaco,'tf11_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2867,16372,'','".AddSlashes(pg_result($resaco,$iresaco,'tf11_i_utilizagradehorario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2867,17592,'','".AddSlashes(pg_result($resaco,$iresaco,'tf11_i_campofoco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2867,18250,'','".AddSlashes(pg_result($resaco,$iresaco,'tf11_especmedico'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2867,1014316,'','".AddSlashes(pg_result($resaco,$iresaco,'tf11_obriga_hora_saida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from tfd_parametros
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($tf11_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " tf11_i_codigo = $tf11_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "tfd_parametros não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$tf11_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "tfd_parametros não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$tf11_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$tf11_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:tfd_parametros";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($tf11_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from tfd_parametros ";
     $sql .= "      left  join especmedico  on  especmedico.sd27_i_codigo = tfd_parametros.tf11_especmedico";
     $sql .= "      inner join rhcbo  on  rhcbo.rh70_sequencial = especmedico.sd27_i_rhcbo";
     $sql .= "      inner join unidademedicos  on  unidademedicos.sd04_i_codigo = especmedico.sd27_i_undmed";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($tf11_i_codigo)) {
         $sql2 .= " where tfd_parametros.tf11_i_codigo = $tf11_i_codigo ";
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

    public function sql_query_file($tf11_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from tfd_parametros ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($tf11_i_codigo)){
         $sql2 .= " where tfd_parametros.tf11_i_codigo = $tf11_i_codigo ";
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
  // funcao do sql query geral
  public function sql_query_geral ( $tf11_i_codigo=null, $campos="*", $ordem=null, $dbwhere="") {

    $sql = "select ";
    if ($campos != "*") {

      $campos_sql = explode("#",$campos);
      $virgula    = "";
      for ($i = 0; $i < sizeof($campos_sql); $i++) {

        $sql    .= $virgula.$campos_sql[$i];
        $virgula = ",";

      }

    } else {
      $sql .= $campos;
    }
    $sql .= " from tfd_parametros ";
    $sql .= "      left join especmedico    on sd27_i_codigo   = tfd_parametros.tf11_especmedico";
    $sql .= "      left join rhcbo          on rh70_sequencial = especmedico.sd27_i_rhcbo";
    $sql .= "      left join unidademedicos on sd04_i_codigo   = especmedico.sd27_i_undmed";
    $sql .= "      left join medicos        on sd03_i_codigo   = unidademedicos.sd04_i_medico";
    $sql .= "      left join cgm            on z01_numcgm      = medicos.sd03_i_cgm";
    $sql .= "      left join unidades       on sd02_i_codigo   = unidademedicos.sd04_i_unidade";
    $sql .= "      left join db_depart      on coddepto        = unidades.sd02_i_codigo";
    $sql2 = "";
    if ($dbwhere == "") {
      if ($tf11_i_codigo != null ) {
        $sql2 .= " where tfd_parametros.tf11_i_codigo = $tf11_i_codigo ";
      }
    } else if($dbwhere != "") {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if ($ordem != null ) {

      $sql       .= " order by ";
      $campos_sql = explode("#",$ordem);
      $virgula    = "";
      for ($i = 0; $i < sizeof($campos_sql); $i++) {

        $sql    .= $virgula.$campos_sql[$i];
        $virgula = ",";

      }

    }
    return $sql;

  }
}
