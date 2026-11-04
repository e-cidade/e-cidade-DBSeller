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

/**
 * Class cl_calendarioescola
 */
class cl_calendarioescola
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
    public $ed38_i_codigo = 0;
    public $ed38_i_escola = 0;
    public $ed38_i_calendario = 0;
    public $ed38_calendariobase = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ed38_i_codigo = int8 = Código
                 ed38_i_escola = int8 = Escola
                 ed38_i_calendario = int8 = Calendário
                 ed38_calendariobase = int8 = Calendário Base
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("calendarioescola");
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
       $this->ed38_i_codigo = ($this->ed38_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed38_i_codigo"]:$this->ed38_i_codigo);
       $this->ed38_i_escola = ($this->ed38_i_escola == ""?@$GLOBALS["HTTP_POST_VARS"]["ed38_i_escola"]:$this->ed38_i_escola);
       $this->ed38_i_calendario = ($this->ed38_i_calendario == ""?@$GLOBALS["HTTP_POST_VARS"]["ed38_i_calendario"]:$this->ed38_i_calendario);
       $this->ed38_calendariobase = ($this->ed38_calendariobase == ""?@$GLOBALS["HTTP_POST_VARS"]["ed38_calendariobase"]:$this->ed38_calendariobase);
     }else{
       $this->ed38_i_codigo = ($this->ed38_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed38_i_codigo"]:$this->ed38_i_codigo);
     }
   }

    public function incluir($ed38_i_codigo)
    {
        $this->atualizacampos();
     if($this->ed38_i_escola == null ){
       $this->erro_sql = " Campo Escola não informado.";
       $this->erro_campo = "ed38_i_escola";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed38_i_calendario == null ){
       $this->erro_sql = " Campo Calendário não informado.";
       $this->erro_campo = "ed38_i_calendario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed38_calendariobase == null ){
        $this->ed38_calendariobase = 'null';
    }
     if($ed38_i_codigo == "" || $ed38_i_codigo == null ){
       $result = db_query("select nextval('calendarioescola_ed38_i_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: calendarioescola_ed38_i_codigo_seq do campo: ed38_i_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ed38_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from calendarioescola_ed38_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed38_i_codigo)){
         $this->erro_sql = " Campo ed38_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed38_i_codigo = $ed38_i_codigo;
       }
     }
     if(($this->ed38_i_codigo == null) || ($this->ed38_i_codigo == "") ){
       $this->erro_sql = " Campo ed38_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into calendarioescola(
                                       ed38_i_codigo
                                      ,ed38_i_escola
                                      ,ed38_i_calendario
                                      ,ed38_calendariobase
                       )
                values (
                                $this->ed38_i_codigo
                               ,$this->ed38_i_escola
                               ,$this->ed38_i_calendario
                               ,$this->ed38_calendariobase
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Calendários ligados a escola ($this->ed38_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Calendários ligados a escola já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Calendários ligados a escola ($this->ed38_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed38_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed38_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1008579,'$this->ed38_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,1010104,1008579,'','".AddSlashes(pg_result($resaco,0,'ed38_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010104,1008580,'','".AddSlashes(pg_result($resaco,0,'ed38_i_escola'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010104,1008581,'','".AddSlashes(pg_result($resaco,0,'ed38_i_calendario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010104,1008581,'','".AddSlashes(pg_result($resaco,0,'ed38_calendariobase'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ed38_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update calendarioescola set ";
     $virgula = "";
     if(trim($this->ed38_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed38_i_codigo"])){
       $sql  .= $virgula." ed38_i_codigo = $this->ed38_i_codigo ";
       $virgula = ",";
       if(trim($this->ed38_i_codigo) == null ){
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "ed38_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed38_i_escola)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed38_i_escola"])){
       $sql  .= $virgula." ed38_i_escola = $this->ed38_i_escola ";
       $virgula = ",";
       if(trim($this->ed38_i_escola) == null ){
         $this->erro_sql = " Campo Escola não informado.";
         $this->erro_campo = "ed38_i_escola";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed38_i_calendario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed38_i_calendario"])){
       $sql  .= $virgula." ed38_i_calendario = $this->ed38_i_calendario ";
       $virgula = ",";
       if(trim($this->ed38_i_calendario) == null ){
         $this->erro_sql = " Campo Calendário não informado.";
         $this->erro_campo = "ed38_i_calendario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed38_calendariobase)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed38_calendariobase"])){
      $sql  .= $virgula." ed38_calendariobase = $this->ed38_calendariobase ";
      $virgula = ",";
      if(trim($this->ed38_calendariobase) == null ){
        $this->erro_sql = " Campo Calendário não informado.";
        $this->erro_campo = "ed38_calendariobase";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
     $sql .= " where ";
     if($ed38_i_codigo!=null){
       $sql .= " ed38_i_codigo = $this->ed38_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed38_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1008579,'$this->ed38_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed38_i_codigo"]) || $this->ed38_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,1010104,1008579,'".AddSlashes(pg_result($resaco,$conresaco,'ed38_i_codigo'))."','$this->ed38_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed38_i_escola"]) || $this->ed38_i_escola != "")
             $resac = db_query("insert into db_acount values($acount,1010104,1008580,'".AddSlashes(pg_result($resaco,$conresaco,'ed38_i_escola'))."','$this->ed38_i_escola',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed38_i_calendario"]) || $this->ed38_i_calendario != "")
             $resac = db_query("insert into db_acount values($acount,1010104,1008581,'".AddSlashes(pg_result($resaco,$conresaco,'ed38_i_calendario'))."','$this->ed38_i_calendario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed38_calendariobase"]) || $this->ed38_calendariobase != "")
             $resac = db_query("insert into db_acount values($acount,1010104,1008581,'".AddSlashes(pg_result($resaco,$conresaco,'ed38_calendariobase'))."','$this->ed38_calendariobase',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
          }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Calendários ligados a escola não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed38_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Calendários ligados a escola não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed38_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed38_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($ed38_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ed38_i_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1008579,'$ed38_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,1010104,1008579,'','".AddSlashes(pg_result($resaco,$iresaco,'ed38_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010104,1008580,'','".AddSlashes(pg_result($resaco,$iresaco,'ed38_i_escola'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010104,1008581,'','".AddSlashes(pg_result($resaco,$iresaco,'ed38_i_calendario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010104,1008581,'','".AddSlashes(pg_result($resaco,$iresaco,'ed38_calendariobase'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from calendarioescola
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ed38_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ed38_i_codigo = $ed38_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Calendários ligados a escola não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed38_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Calendários ligados a escola não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed38_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ed38_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:calendarioescola";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ed38_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from calendarioescola ";
     $sql .= "      inner join escola  on  escola.ed18_i_codigo = calendarioescola.ed38_i_escola";
     $sql .= "      inner join calendario  on  calendario.ed52_i_codigo = calendarioescola.ed38_i_calendario";
     $sql .= "      inner join bairro  on  bairro.j13_codi = escola.ed18_i_bairro";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = escola.ed18_i_rua";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = escola.ed18_i_codigo";
     $sql .= "      inner join duracaocal  on  duracaocal.ed55_i_codigo = calendario.ed52_i_duracaocal";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed38_i_codigo)) {
         $sql2 .= " where calendarioescola.ed38_i_codigo = $ed38_i_codigo ";
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

    public function sql_query_file($ed38_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from calendarioescola ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed38_i_codigo)){
         $sql2 .= " where calendarioescola.ed38_i_codigo = $ed38_i_codigo ";
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
