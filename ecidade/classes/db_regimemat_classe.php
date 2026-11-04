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

class cl_regimemat
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
    public $ed218_i_codigo = 0; 
    public $ed218_c_nome = null; 
    public $ed218_c_abrev = null; 
    public $ed218_c_divisao = null; 
    public $ed218_organizacaoturma = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 ed218_i_codigo = int8 = Código 
                 ed218_c_nome = char(30) = Descrição 
                 ed218_c_abrev = char(10) = Abreviatura 
                 ed218_c_divisao = char(1) = Possui divisões 
                 ed218_organizacaoturma = int4 = Forma de Organização da Turma 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("regimemat"); 
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
       $this->ed218_i_codigo = ($this->ed218_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed218_i_codigo"]:$this->ed218_i_codigo);
       $this->ed218_c_nome = ($this->ed218_c_nome == ""?@$GLOBALS["HTTP_POST_VARS"]["ed218_c_nome"]:$this->ed218_c_nome);
       $this->ed218_c_abrev = ($this->ed218_c_abrev == ""?@$GLOBALS["HTTP_POST_VARS"]["ed218_c_abrev"]:$this->ed218_c_abrev);
       $this->ed218_c_divisao = ($this->ed218_c_divisao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed218_c_divisao"]:$this->ed218_c_divisao);
       $this->ed218_organizacaoturma = ($this->ed218_organizacaoturma == ""?@$GLOBALS["HTTP_POST_VARS"]["ed218_organizacaoturma"]:$this->ed218_organizacaoturma);
     }else{
       $this->ed218_i_codigo = ($this->ed218_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed218_i_codigo"]:$this->ed218_i_codigo);
     }
   }

    public function incluir($ed218_i_codigo)
    {
      $this->atualizacampos();
     if($this->ed218_c_nome == null ){ 
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "ed218_c_nome";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed218_c_abrev == null ){ 
       $this->erro_sql = " Campo Abreviatura não informado.";
       $this->erro_campo = "ed218_c_abrev";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed218_c_divisao == null ){ 
       $this->erro_sql = " Campo Possui divisões não informado.";
       $this->erro_campo = "ed218_c_divisao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed218_organizacaoturma == null ){ 
       $this->ed218_organizacaoturma = "0";
     }
     if($ed218_i_codigo == "" || $ed218_i_codigo == null ){
       $result = db_query("select nextval('regimemat_ed218_i_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: regimemat_ed218_i_codigo_seq do campo: ed218_i_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ed218_i_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from regimemat_ed218_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed218_i_codigo)){
         $this->erro_sql = " Campo ed218_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed218_i_codigo = $ed218_i_codigo; 
       }
     }
     if(($this->ed218_i_codigo == null) || ($this->ed218_i_codigo == "") ){ 
       $this->erro_sql = " Campo ed218_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into regimemat(
                                       ed218_i_codigo 
                                      ,ed218_c_nome 
                                      ,ed218_c_abrev 
                                      ,ed218_c_divisao 
                                      ,ed218_organizacaoturma 
                       )
                values (
                                $this->ed218_i_codigo 
                               ,'$this->ed218_c_nome' 
                               ,'$this->ed218_c_abrev' 
                               ,'$this->ed218_c_divisao' 
                               ,$this->ed218_organizacaoturma 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Regime de Matrícula ($this->ed218_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Regime de Matrícula já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Regime de Matrícula ($this->ed218_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed218_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed218_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14914,'$this->ed218_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2625,14914,'','".AddSlashes(pg_result($resaco,0,'ed218_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2625,14915,'','".AddSlashes(pg_result($resaco,0,'ed218_c_nome'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2625,14916,'','".AddSlashes(pg_result($resaco,0,'ed218_c_abrev'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2625,14917,'','".AddSlashes(pg_result($resaco,0,'ed218_c_divisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2625,1014154,'','".AddSlashes(pg_result($resaco,0,'ed218_organizacaoturma'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($ed218_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update regimemat set ";
     $virgula = "";
     if(trim($this->ed218_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed218_i_codigo"])){ 
       $sql  .= $virgula." ed218_i_codigo = $this->ed218_i_codigo ";
       $virgula = ",";
       if(trim($this->ed218_i_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "ed218_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed218_c_nome)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed218_c_nome"])){ 
       $sql  .= $virgula." ed218_c_nome = '$this->ed218_c_nome' ";
       $virgula = ",";
       if(trim($this->ed218_c_nome) == null ){ 
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "ed218_c_nome";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed218_c_abrev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed218_c_abrev"])){ 
       $sql  .= $virgula." ed218_c_abrev = '$this->ed218_c_abrev' ";
       $virgula = ",";
       if(trim($this->ed218_c_abrev) == null ){ 
         $this->erro_sql = " Campo Abreviatura não informado.";
         $this->erro_campo = "ed218_c_abrev";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed218_c_divisao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed218_c_divisao"])){ 
       $sql  .= $virgula." ed218_c_divisao = '$this->ed218_c_divisao' ";
       $virgula = ",";
       if(trim($this->ed218_c_divisao) == null ){ 
         $this->erro_sql = " Campo Possui divisões não informado.";
         $this->erro_campo = "ed218_c_divisao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed218_organizacaoturma)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed218_organizacaoturma"])){ 
        if(trim($this->ed218_organizacaoturma)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ed218_organizacaoturma"])){ 
           $this->ed218_organizacaoturma = "0" ; 
        } 
       $sql  .= $virgula." ed218_organizacaoturma = $this->ed218_organizacaoturma ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($ed218_i_codigo!=null){
       $sql .= " ed218_i_codigo = $this->ed218_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed218_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,14914,'$this->ed218_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed218_i_codigo"]) || $this->ed218_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2625,14914,'".AddSlashes(pg_result($resaco,$conresaco,'ed218_i_codigo'))."','$this->ed218_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed218_c_nome"]) || $this->ed218_c_nome != "")
             $resac = db_query("insert into db_acount values($acount,2625,14915,'".AddSlashes(pg_result($resaco,$conresaco,'ed218_c_nome'))."','$this->ed218_c_nome',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed218_c_abrev"]) || $this->ed218_c_abrev != "")
             $resac = db_query("insert into db_acount values($acount,2625,14916,'".AddSlashes(pg_result($resaco,$conresaco,'ed218_c_abrev'))."','$this->ed218_c_abrev',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed218_c_divisao"]) || $this->ed218_c_divisao != "")
             $resac = db_query("insert into db_acount values($acount,2625,14917,'".AddSlashes(pg_result($resaco,$conresaco,'ed218_c_divisao'))."','$this->ed218_c_divisao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed218_organizacaoturma"]) || $this->ed218_organizacaoturma != "")
             $resac = db_query("insert into db_acount values($acount,2625,1014154,'".AddSlashes(pg_result($resaco,$conresaco,'ed218_organizacaoturma'))."','$this->ed218_organizacaoturma',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Regime de Matrícula não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed218_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Regime de Matrícula não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed218_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed218_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($ed218_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ed218_i_codigo));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,14914,'$ed218_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2625,14914,'','".AddSlashes(pg_result($resaco,$iresaco,'ed218_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2625,14915,'','".AddSlashes(pg_result($resaco,$iresaco,'ed218_c_nome'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2625,14916,'','".AddSlashes(pg_result($resaco,$iresaco,'ed218_c_abrev'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2625,14917,'','".AddSlashes(pg_result($resaco,$iresaco,'ed218_c_divisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2625,1014154,'','".AddSlashes(pg_result($resaco,$iresaco,'ed218_organizacaoturma'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from regimemat
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ed218_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ed218_i_codigo = $ed218_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Regime de Matrícula não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed218_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Regime de Matrícula não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed218_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ed218_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:regimemat";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ed218_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from regimemat ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed218_i_codigo)) {
         $sql2 .= " where regimemat.ed218_i_codigo = $ed218_i_codigo "; 
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

    public function sql_query_file($ed218_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from regimemat ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed218_i_codigo)){
         $sql2 .= " where regimemat.ed218_i_codigo = $ed218_i_codigo "; 
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
?>
