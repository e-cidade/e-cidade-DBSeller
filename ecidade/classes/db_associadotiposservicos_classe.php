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

class cl_associadotiposservicos
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
    public $fm09_codigo = 0; 
    public $fm09_descricao = null; 
    public $fm09_copart_percentual = 'f'; 
    public $fm09_copart_financeiro = 'f'; 
    public $fm09_valor = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 fm09_codigo = int4 = Código do Tipo de Serviço 
                 fm09_descricao = varchar = Descrição do Tipo de Serviço 
                 fm09_copart_percentual = bool = Cooparticipação por Percentual 
                 fm09_copart_financeiro = bool = Cooparticipação Financeiro 
                 fm09_valor = numeric = Valor do Serviço 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("associadotiposservicos"); 
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
       $this->fm09_codigo = ($this->fm09_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fm09_codigo"]:$this->fm09_codigo);
       $this->fm09_descricao = ($this->fm09_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["fm09_descricao"]:$this->fm09_descricao);
       $this->fm09_copart_percentual = ($this->fm09_copart_percentual == "f"?@$GLOBALS["HTTP_POST_VARS"]["fm09_copart_percentual"]:$this->fm09_copart_percentual);
       $this->fm09_copart_financeiro = ($this->fm09_copart_financeiro == "f"?@$GLOBALS["HTTP_POST_VARS"]["fm09_copart_financeiro"]:$this->fm09_copart_financeiro);
       $this->fm09_valor = ($this->fm09_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["fm09_valor"]:$this->fm09_valor);
     }else{
       $this->fm09_codigo = ($this->fm09_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fm09_codigo"]:$this->fm09_codigo);
     }
   }

    public function incluir($fm09_codigo)
    {
      $this->atualizacampos();
     if($this->fm09_descricao == null ){ 
       $this->erro_sql = " Campo Descrição do Tipo de Serviço não informado.";
       $this->erro_campo = "fm09_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fm09_copart_percentual == null ){ 
       $this->erro_sql = " Campo Cooparticipação por Percentual não informado.";
       $this->erro_campo = "fm09_copart_percentual";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fm09_copart_financeiro == null ){ 
       $this->erro_sql = " Campo Cooparticipação Financeiro não informado.";
       $this->erro_campo = "fm09_copart_financeiro";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fm09_valor == null ){ 
       $this->erro_sql = " Campo Valor do Serviço não informado.";
       $this->erro_campo = "fm09_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($fm09_codigo == "" || $fm09_codigo == null ){
       $result = db_query("select nextval('associadotiposservicos_fm09_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: associadotiposservicos_fm09_codigo_seq do campo: fm09_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->fm09_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from associadotiposservicos_fm09_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $fm09_codigo)){
         $this->erro_sql = " Campo fm09_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->fm09_codigo = $fm09_codigo; 
       }
     }
     if(($this->fm09_codigo == null) || ($this->fm09_codigo == "") ){ 
       $this->erro_sql = " Campo fm09_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into associadotiposservicos(
                                       fm09_codigo 
                                      ,fm09_descricao 
                                      ,fm09_copart_percentual 
                                      ,fm09_copart_financeiro 
                                      ,fm09_valor 
                       )
                values (
                                $this->fm09_codigo 
                               ,'$this->fm09_descricao' 
                               ,'$this->fm09_copart_percentual' 
                               ,'$this->fm09_copart_financeiro' 
                               ,$this->fm09_valor 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "associadotiposservicos ($this->fm09_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "associadotiposservicos já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "associadotiposservicos ($this->fm09_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fm09_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->fm09_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,51368227,'$this->fm09_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,261656180,51368227,'','".AddSlashes(pg_result($resaco,0,'fm09_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,261656180,188818913,'','".AddSlashes(pg_result($resaco,0,'fm09_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,261656180,277380280,'','".AddSlashes(pg_result($resaco,0,'fm09_copart_percentual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,261656180,167353059,'','".AddSlashes(pg_result($resaco,0,'fm09_copart_financeiro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,261656180,210546969,'','".AddSlashes(pg_result($resaco,0,'fm09_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($fm09_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update associadotiposservicos set ";
     $virgula = "";
     if(trim($this->fm09_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fm09_codigo"])){ 
       $sql  .= $virgula." fm09_codigo = $this->fm09_codigo ";
       $virgula = ",";
       if(trim($this->fm09_codigo) == null ){ 
         $this->erro_sql = " Campo Código do Tipo de Serviço não informado.";
         $this->erro_campo = "fm09_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fm09_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fm09_descricao"])){ 
       $sql  .= $virgula." fm09_descricao = '$this->fm09_descricao' ";
       $virgula = ",";
       if(trim($this->fm09_descricao) == null ){ 
         $this->erro_sql = " Campo Descrição do Tipo de Serviço não informado.";
         $this->erro_campo = "fm09_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fm09_copart_percentual)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fm09_copart_percentual"])){ 
       $sql  .= $virgula." fm09_copart_percentual = '$this->fm09_copart_percentual' ";
       $virgula = ",";
       if(trim($this->fm09_copart_percentual) == null ){ 
         $this->erro_sql = " Campo Cooparticipação por Percentual não informado.";
         $this->erro_campo = "fm09_copart_percentual";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fm09_copart_financeiro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fm09_copart_financeiro"])){ 
       $sql  .= $virgula." fm09_copart_financeiro = '$this->fm09_copart_financeiro' ";
       $virgula = ",";
       if(trim($this->fm09_copart_financeiro) == null ){ 
         $this->erro_sql = " Campo Cooparticipação Financeiro não informado.";
         $this->erro_campo = "fm09_copart_financeiro";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fm09_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fm09_valor"])){ 
       $sql  .= $virgula." fm09_valor = $this->fm09_valor ";
       $virgula = ",";
       if(trim($this->fm09_valor) == null ){ 
         $this->erro_sql = " Campo Valor do Serviço não informado.";
         $this->erro_campo = "fm09_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($fm09_codigo!=null){
       $sql .= " fm09_codigo = $this->fm09_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->fm09_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,51368227,'$this->fm09_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fm09_codigo"]) || $this->fm09_codigo != "")
             $resac = db_query("insert into db_acount values($acount,261656180,51368227,'".AddSlashes(pg_result($resaco,$conresaco,'fm09_codigo'))."','$this->fm09_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fm09_descricao"]) || $this->fm09_descricao != "")
             $resac = db_query("insert into db_acount values($acount,261656180,188818913,'".AddSlashes(pg_result($resaco,$conresaco,'fm09_descricao'))."','$this->fm09_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fm09_copart_percentual"]) || $this->fm09_copart_percentual != "")
             $resac = db_query("insert into db_acount values($acount,261656180,277380280,'".AddSlashes(pg_result($resaco,$conresaco,'fm09_copart_percentual'))."','$this->fm09_copart_percentual',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fm09_copart_financeiro"]) || $this->fm09_copart_financeiro != "")
             $resac = db_query("insert into db_acount values($acount,261656180,167353059,'".AddSlashes(pg_result($resaco,$conresaco,'fm09_copart_financeiro'))."','$this->fm09_copart_financeiro',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fm09_valor"]) || $this->fm09_valor != "")
             $resac = db_query("insert into db_acount values($acount,261656180,210546969,'".AddSlashes(pg_result($resaco,$conresaco,'fm09_valor'))."','$this->fm09_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "associadotiposservicos não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->fm09_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "associadotiposservicos não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->fm09_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fm09_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($fm09_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($fm09_codigo));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,51368227,'$fm09_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,261656180,51368227,'','".AddSlashes(pg_result($resaco,$iresaco,'fm09_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,261656180,188818913,'','".AddSlashes(pg_result($resaco,$iresaco,'fm09_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,261656180,277380280,'','".AddSlashes(pg_result($resaco,$iresaco,'fm09_copart_percentual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,261656180,167353059,'','".AddSlashes(pg_result($resaco,$iresaco,'fm09_copart_financeiro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,261656180,210546969,'','".AddSlashes(pg_result($resaco,$iresaco,'fm09_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from associadotiposservicos
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($fm09_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " fm09_codigo = $fm09_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "associadotiposservicos não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$fm09_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "associadotiposservicos não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$fm09_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$fm09_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:associadotiposservicos";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($fm09_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from associadotiposservicos ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fm09_codigo)) {
         $sql2 .= " where associadotiposservicos.fm09_codigo = $fm09_codigo "; 
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

    public function sql_query_file($fm09_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from associadotiposservicos ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fm09_codigo)){
         $sql2 .= " where associadotiposservicos.fm09_codigo = $fm09_codigo "; 
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
