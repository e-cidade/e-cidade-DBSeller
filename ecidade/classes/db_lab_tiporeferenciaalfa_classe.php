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

class cl_lab_tiporeferenciaalfa
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
    public $la29_i_codigo = 0;
    public $la29_i_valorref = 0;
    public $la29_v_fixo = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 la29_i_codigo = int4 = Código
                 la29_i_valorref = int4 = Valor Referencial
                 la29_v_fixo = varchar(30) = Valor Referencial Fixo
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("lab_tiporeferenciaalfa");
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
       $this->la29_i_codigo = ($this->la29_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["la29_i_codigo"]:$this->la29_i_codigo);
       $this->la29_i_valorref = ($this->la29_i_valorref == ""?@$GLOBALS["HTTP_POST_VARS"]["la29_i_valorref"]:$this->la29_i_valorref);
       $this->la29_v_fixo = ($this->la29_v_fixo == ""?@$GLOBALS["HTTP_POST_VARS"]["la29_v_fixo"]:$this->la29_v_fixo);
     }else{
       $this->la29_i_codigo = ($this->la29_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["la29_i_codigo"]:$this->la29_i_codigo);
     }
   }

    public function incluir($la29_i_codigo)
    {
      $this->atualizacampos();
     if($this->la29_i_valorref == null ){
       $this->erro_sql = " Campo Valor Referencial não informado.";
       $this->erro_campo = "la29_i_valorref";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($la29_i_codigo == "" || $la29_i_codigo == null ){
       $result = db_query("select nextval('lab_tiporeferenciaalfa_la29_i_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: lab_tiporeferenciaalfa_la29_i_codigo_seq do campo: la29_i_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->la29_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from lab_tiporeferenciaalfa_la29_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $la29_i_codigo)){
         $this->erro_sql = " Campo la29_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->la29_i_codigo = $la29_i_codigo;
       }
     }
     if(($this->la29_i_codigo == null) || ($this->la29_i_codigo == "") ){
       $this->erro_sql = " Campo la29_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into lab_tiporeferenciaalfa(
                                       la29_i_codigo
                                      ,la29_i_valorref
                                      ,la29_v_fixo
                       )
                values (
                                $this->la29_i_codigo
                               ,$this->la29_i_valorref
                               ,'$this->la29_v_fixo'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tipo de referência alfanumérico ($this->la29_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tipo de referência alfanumérico já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tipo de referência alfanumérico ($this->la29_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->la29_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->la29_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,16500,'$this->la29_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2902,16500,'','".AddSlashes(pg_result($resaco,0,'la29_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2902,16501,'','".AddSlashes(pg_result($resaco,0,'la29_i_valorref'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2902,16502,'','".AddSlashes(pg_result($resaco,0,'la29_v_fixo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($la29_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update lab_tiporeferenciaalfa set ";
     $virgula = "";
     if(trim($this->la29_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la29_i_codigo"])){
       $sql  .= $virgula." la29_i_codigo = $this->la29_i_codigo ";
       $virgula = ",";
       if(trim($this->la29_i_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "la29_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la29_i_valorref)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la29_i_valorref"])){
       $sql  .= $virgula." la29_i_valorref = $this->la29_i_valorref ";
       $virgula = ",";
       if(trim($this->la29_i_valorref) == null ){
         $this->erro_sql = " Campo Valor Referencial não informado.";
         $this->erro_campo = "la29_i_valorref";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->la29_v_fixo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la29_v_fixo"])){
       $sql  .= $virgula." la29_v_fixo = '$this->la29_v_fixo' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($la29_i_codigo!=null){
       $sql .= " la29_i_codigo = $this->la29_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->la29_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,16500,'$this->la29_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la29_i_codigo"]) || $this->la29_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2902,16500,'".AddSlashes(pg_result($resaco,$conresaco,'la29_i_codigo'))."','$this->la29_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la29_i_valorref"]) || $this->la29_i_valorref != "")
             $resac = db_query("insert into db_acount values($acount,2902,16501,'".AddSlashes(pg_result($resaco,$conresaco,'la29_i_valorref'))."','$this->la29_i_valorref',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["la29_v_fixo"]) || $this->la29_v_fixo != "")
             $resac = db_query("insert into db_acount values($acount,2902,16502,'".AddSlashes(pg_result($resaco,$conresaco,'la29_v_fixo'))."','$this->la29_v_fixo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo de referência alfanumérico não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->la29_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tipo de referência alfanumérico não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->la29_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->la29_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($la29_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($la29_i_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,16500,'$la29_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2902,16500,'','".AddSlashes(pg_result($resaco,$iresaco,'la29_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2902,16501,'','".AddSlashes(pg_result($resaco,$iresaco,'la29_i_valorref'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2902,16502,'','".AddSlashes(pg_result($resaco,$iresaco,'la29_v_fixo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from lab_tiporeferenciaalfa
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($la29_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " la29_i_codigo = $la29_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipo de referência alfanumérico não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$la29_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tipo de referência alfanumérico não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$la29_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$la29_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:lab_tiporeferenciaalfa";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($la29_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from lab_tiporeferenciaalfa ";
     $sql .= "      inner join lab_valorreferencia  on  lab_valorreferencia.la27_i_codigo = lab_tiporeferenciaalfa.la29_i_valorref";
     $sql .= "      left  join lab_undmedida  on  lab_undmedida.la13_i_codigo = lab_valorreferencia.la27_i_unidade";
     $sql .= "      inner join lab_atributo  on  lab_atributo.la25_i_codigo = lab_valorreferencia.la27_i_atributo";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($la29_i_codigo)) {
         $sql2 .= " where lab_tiporeferenciaalfa.la29_i_codigo = $la29_i_codigo ";
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

    public function sql_query_file($la29_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from lab_tiporeferenciaalfa ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($la29_i_codigo)){
         $sql2 .= " where lab_tiporeferenciaalfa.la29_i_codigo = $la29_i_codigo ";
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

}
