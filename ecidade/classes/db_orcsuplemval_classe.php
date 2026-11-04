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

class cl_orcsuplemval
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
    public $o47_codsup = 0;
    public $o47_anousu = 0;
    public $o47_coddot = 0;
    public $o47_valor = 0;
    public $o47_concarpeculiar = null;
    public $o47_planoorcamentariolinhapacto = 0;
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 o47_codsup = int4 = Código Suplementação 
                 o47_anousu = int4 = Exercício 
                 o47_coddot = int4 = Reduzido da Dotação 
                 o47_valor = float8 = Valor 
                 o47_concarpeculiar = varchar(100) = C.Peculiar/ C. Aplicação 
                 o47_planoorcamentariolinhapacto = int4 = Linha de Pacto 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("orcsuplemval"); 
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
       $this->o47_codsup = ($this->o47_codsup == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_codsup"]:$this->o47_codsup);
       $this->o47_anousu = ($this->o47_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_anousu"]:$this->o47_anousu);
       $this->o47_coddot = ($this->o47_coddot == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_coddot"]:$this->o47_coddot);
       $this->o47_valor = ($this->o47_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_valor"]:$this->o47_valor);
       $this->o47_concarpeculiar = ($this->o47_concarpeculiar == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_concarpeculiar"]:$this->o47_concarpeculiar);
       $this->o47_planoorcamentariolinhapacto = ($this->o47_planoorcamentariolinhapacto == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_planoorcamentariolinhapacto"]:$this->o47_planoorcamentariolinhapacto);
     }else{
       $this->o47_codsup = ($this->o47_codsup == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_codsup"]:$this->o47_codsup);
       $this->o47_anousu = ($this->o47_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_anousu"]:$this->o47_anousu);
       $this->o47_coddot = ($this->o47_coddot == ""?@$GLOBALS["HTTP_POST_VARS"]["o47_coddot"]:$this->o47_coddot);
     }
   }

    public function incluir($o47_codsup,$o47_anousu,$o47_coddot)
    {
      $this->atualizacampos();
     if($this->o47_valor == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "o47_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->o47_concarpeculiar == null ){ 
       $this->erro_sql = " Campo C.Peculiar/ C. Aplicação não informado.";
       $this->erro_campo = "o47_concarpeculiar";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->o47_planoorcamentariolinhapacto == null ){ 
       $this->o47_planoorcamentariolinhapacto = "0";
     }
       $this->o47_codsup = $o47_codsup; 
       $this->o47_anousu = $o47_anousu; 
       $this->o47_coddot = $o47_coddot; 
     if(($this->o47_codsup == null) || ($this->o47_codsup == "") ){ 
       $this->erro_sql = " Campo o47_codsup não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->o47_anousu == null) || ($this->o47_anousu == "") ){ 
       $this->erro_sql = " Campo o47_anousu não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->o47_coddot == null) || ($this->o47_coddot == "") ){ 
       $this->erro_sql = " Campo o47_coddot não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into orcsuplemval(
                                       o47_codsup 
                                      ,o47_anousu 
                                      ,o47_coddot 
                                      ,o47_valor 
                                      ,o47_concarpeculiar 
                                      ,o47_planoorcamentariolinhapacto 
                       )
                values (
                                $this->o47_codsup 
                               ,$this->o47_anousu 
                               ,$this->o47_coddot 
                               ,$this->o47_valor 
                               ,'$this->o47_concarpeculiar' 
                               ,$this->o47_planoorcamentariolinhapacto 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Valor das Suplementações ($this->o47_codsup."-".$this->o47_anousu."-".$this->o47_coddot) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Valor das Suplementações já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Valor das Suplementações ($this->o47_codsup."-".$this->o47_anousu."-".$this->o47_coddot) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o47_codsup."-".$this->o47_anousu."-".$this->o47_coddot;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->o47_codsup,$this->o47_anousu,$this->o47_coddot  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,5324,'$this->o47_codsup','I')");
         $resac = db_query("insert into db_acountkey values($acount,5325,'$this->o47_anousu','I')");
         $resac = db_query("insert into db_acountkey values($acount,5326,'$this->o47_coddot','I')");
         $resac = db_query("insert into db_acount values($acount,787,5324,'','".AddSlashes(pg_result($resaco,0,'o47_codsup'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,787,5325,'','".AddSlashes(pg_result($resaco,0,'o47_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,787,5326,'','".AddSlashes(pg_result($resaco,0,'o47_coddot'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,787,5327,'','".AddSlashes(pg_result($resaco,0,'o47_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,787,18159,'','".AddSlashes(pg_result($resaco,0,'o47_concarpeculiar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,787,1010476,'','".AddSlashes(pg_result($resaco,0,'o47_planoorcamentariolinhapacto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($o47_codsup=null,$o47_anousu=null,$o47_coddot=null)
    {
      $this->atualizacampos();
     $sql = " update orcsuplemval set ";
     $virgula = "";
     if(trim($this->o47_codsup)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o47_codsup"])){ 
       $sql  .= $virgula." o47_codsup = $this->o47_codsup ";
       $virgula = ",";
       if(trim($this->o47_codsup) == null ){ 
         $this->erro_sql = " Campo Código Suplementação não informado.";
         $this->erro_campo = "o47_codsup";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o47_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o47_anousu"])){ 
       $sql  .= $virgula." o47_anousu = $this->o47_anousu ";
       $virgula = ",";
       if(trim($this->o47_anousu) == null ){ 
         $this->erro_sql = " Campo Exercício não informado.";
         $this->erro_campo = "o47_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o47_coddot)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o47_coddot"])){ 
       $sql  .= $virgula." o47_coddot = $this->o47_coddot ";
       $virgula = ",";
       if(trim($this->o47_coddot) == null ){ 
         $this->erro_sql = " Campo Reduzido da Dotação não informado.";
         $this->erro_campo = "o47_coddot";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o47_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o47_valor"])){ 
       $sql  .= $virgula." o47_valor = $this->o47_valor ";
       $virgula = ",";
       if(trim($this->o47_valor) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "o47_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o47_concarpeculiar)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o47_concarpeculiar"])){ 
       $sql  .= $virgula." o47_concarpeculiar = '$this->o47_concarpeculiar' ";
       $virgula = ",";
       if(trim($this->o47_concarpeculiar) == null ){ 
         $this->erro_sql = " Campo C.Peculiar/ C. Aplicação não informado.";
         $this->erro_campo = "o47_concarpeculiar";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o47_planoorcamentariolinhapacto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o47_planoorcamentariolinhapacto"])){ 
        if(trim($this->o47_planoorcamentariolinhapacto)=="" && isset($GLOBALS["HTTP_POST_VARS"]["o47_planoorcamentariolinhapacto"])){ 
           $this->o47_planoorcamentariolinhapacto = "0" ; 
        } 
       $sql  .= $virgula." o47_planoorcamentariolinhapacto = $this->o47_planoorcamentariolinhapacto ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($o47_codsup!=null){
       $sql .= " o47_codsup = $this->o47_codsup";
     }
     if($o47_anousu!=null){
       $sql .= " and  o47_anousu = $this->o47_anousu";
     }
     if($o47_coddot!=null){
       $sql .= " and  o47_coddot = $this->o47_coddot";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->o47_codsup,$this->o47_anousu,$this->o47_coddot));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5324,'$this->o47_codsup','A')");
           $resac = db_query("insert into db_acountkey values($acount,5325,'$this->o47_anousu','A')");
           $resac = db_query("insert into db_acountkey values($acount,5326,'$this->o47_coddot','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["o47_codsup"]) || $this->o47_codsup != "")
             $resac = db_query("insert into db_acount values($acount,787,5324,'".AddSlashes(pg_result($resaco,$conresaco,'o47_codsup'))."','$this->o47_codsup',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["o47_anousu"]) || $this->o47_anousu != "")
             $resac = db_query("insert into db_acount values($acount,787,5325,'".AddSlashes(pg_result($resaco,$conresaco,'o47_anousu'))."','$this->o47_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["o47_coddot"]) || $this->o47_coddot != "")
             $resac = db_query("insert into db_acount values($acount,787,5326,'".AddSlashes(pg_result($resaco,$conresaco,'o47_coddot'))."','$this->o47_coddot',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["o47_valor"]) || $this->o47_valor != "")
             $resac = db_query("insert into db_acount values($acount,787,5327,'".AddSlashes(pg_result($resaco,$conresaco,'o47_valor'))."','$this->o47_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["o47_concarpeculiar"]) || $this->o47_concarpeculiar != "")
             $resac = db_query("insert into db_acount values($acount,787,18159,'".AddSlashes(pg_result($resaco,$conresaco,'o47_concarpeculiar'))."','$this->o47_concarpeculiar',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["o47_planoorcamentariolinhapacto"]) || $this->o47_planoorcamentariolinhapacto != "")
             $resac = db_query("insert into db_acount values($acount,787,1010476,'".AddSlashes(pg_result($resaco,$conresaco,'o47_planoorcamentariolinhapacto'))."','$this->o47_planoorcamentariolinhapacto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Valor das Suplementações não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->o47_codsup."-".$this->o47_anousu."-".$this->o47_coddot;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Valor das Suplementações não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->o47_codsup."-".$this->o47_anousu."-".$this->o47_coddot;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o47_codsup."-".$this->o47_anousu."-".$this->o47_coddot;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($o47_codsup=null,$o47_anousu=null,$o47_coddot=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($o47_codsup,$o47_anousu,$o47_coddot));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,null,null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,5324,'$o47_codsup','E')");
           $resac  = db_query("insert into db_acountkey values($acount,5325,'$o47_anousu','E')");
           $resac  = db_query("insert into db_acountkey values($acount,5326,'$o47_coddot','E')");
           $resac  = db_query("insert into db_acount values($acount,787,5324,'','".AddSlashes(pg_result($resaco,$iresaco,'o47_codsup'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,787,5325,'','".AddSlashes(pg_result($resaco,$iresaco,'o47_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,787,5326,'','".AddSlashes(pg_result($resaco,$iresaco,'o47_coddot'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,787,5327,'','".AddSlashes(pg_result($resaco,$iresaco,'o47_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,787,18159,'','".AddSlashes(pg_result($resaco,$iresaco,'o47_concarpeculiar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,787,1010476,'','".AddSlashes(pg_result($resaco,$iresaco,'o47_planoorcamentariolinhapacto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from orcsuplemval
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($o47_codsup)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " o47_codsup = $o47_codsup ";
        }
        if (!empty($o47_anousu)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " o47_anousu = $o47_anousu ";
        }
        if (!empty($o47_coddot)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " o47_coddot = $o47_coddot ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Valor das Suplementações não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$o47_codsup."-".$o47_anousu."-".$o47_coddot;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Valor das Suplementações não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$o47_codsup."-".$o47_anousu."-".$o47_coddot;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$o47_codsup."-".$o47_anousu."-".$o47_coddot;
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
        $this->erro_sql   = "Record Vazio na Tabela:orcsuplemval";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($o47_codsup = null,$o47_anousu = null,$o47_coddot = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from orcsuplemval ";
     $sql .= "      inner join orcdotacao  on  orcdotacao.o58_anousu = orcsuplemval.o47_anousu and  orcdotacao.o58_coddot = orcsuplemval.o47_coddot";
     $sql .= "      inner join orcsuplem  on  orcsuplem.o46_codsup = orcsuplemval.o47_codsup";
     $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = orcsuplemval.o47_concarpeculiar";
     $sql .= "      left  join planoorcamentariolinhapacto  on  planoorcamentariolinhapacto.o156_sequencial = orcsuplemval.o47_planoorcamentariolinhapacto";
     $sql .= "      inner join db_config  on  db_config.codigo = orcdotacao.o58_instit";
     $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = orcdotacao.o58_codigo";
     $sql .= "      inner join orcfuncao  on  orcfuncao.o52_funcao = orcdotacao.o58_funcao";
     $sql .= "      inner join orcsubfuncao  on  orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao";
     $sql .= "      inner join orcprograma  on  orcprograma.o54_anousu = orcdotacao.o58_anousu and  orcprograma.o54_programa = orcdotacao.o58_programa";
     $sql .= "      inner join orcelemento  on  orcelemento.o56_codele = orcdotacao.o58_codele and  orcelemento.o56_anousu = orcdotacao.o58_anousu";
     $sql .= "      inner join orcprojativ  on  orcprojativ.o55_anousu = orcdotacao.o58_anousu and  orcprojativ.o55_projativ = orcdotacao.o58_projativ";
     $sql .= "      inner join orcorgao  on  orcorgao.o40_anousu = orcdotacao.o58_anousu and  orcorgao.o40_orgao = orcdotacao.o58_orgao";
     $sql .= "      inner join orcunidade  on  orcunidade.o41_anousu = orcdotacao.o58_anousu and  orcunidade.o41_orgao = orcdotacao.o58_orgao and  orcunidade.o41_unidade = orcdotacao.o58_unidade";
     $sql .= "      inner join concarpeculiar  as a on   a.c58_sequencial = orcdotacao.o58_concarpeculiar";
     $sql .= "      inner join ppasubtitulolocalizadorgasto  on  ppasubtitulolocalizadorgasto.o11_sequencial = orcdotacao.o58_localizadorgastos";
     $sql .= "      inner join orcsuplemtipo  on  orcsuplemtipo.o48_tiposup = orcsuplem.o46_tiposup";
     $sql .= "      inner join orcprojeto  on  orcprojeto.o39_codproj = orcsuplem.o46_codlei";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($o47_codsup)) {
         $sql2 .= " where orcsuplemval.o47_codsup = $o47_codsup "; 
       } 
       if (!empty($o47_anousu)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " orcsuplemval.o47_anousu = $o47_anousu "; 
       } 
       if (!empty($o47_coddot)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " orcsuplemval.o47_coddot = $o47_coddot "; 
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

    public function sql_query_file($o47_codsup = null,$o47_anousu = null,$o47_coddot = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from orcsuplemval ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($o47_codsup)){
         $sql2 .= " where orcsuplemval.o47_codsup = $o47_codsup "; 
       } 
       if (!empty($o47_anousu)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " orcsuplemval.o47_anousu = $o47_anousu "; 
       } 
       if (!empty($o47_coddot)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " orcsuplemval.o47_coddot = $o47_coddot "; 
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

