<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2014  DBSeller Servicos de Informatica
 *                      www.dbseller.com.br
 *                   e-cidade@dbseller.com.br
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

class cl_rhdepend
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
    public $rh31_codigo = 0; 
    public $rh31_regist = 0; 
    public $rh31_nome = null; 
    public $rh31_dtnasc_dia = null; 
    public $rh31_dtnasc_mes = null; 
    public $rh31_dtnasc_ano = null; 
    public $rh31_dtnasc = null; 
    public $rh31_gparen = null; 
    public $rh31_depend = null; 
    public $rh31_irf = null; 
    public $rh31_especi = null; 
    public $rh31_fins_previdenciarios = 'f'; 
    public $rh31_tipoparentesco = null; 
    public $rh31_descricaoparentesco = null; 
    public $rh31_irfcomplementar = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 rh31_codigo = int8 = Código 
                 rh31_regist = int4 = Matrícula do Servidor 
                 rh31_nome = varchar(70) = Nome do Dependente 
                 rh31_dtnasc = date = Data de Nascimento 
                 rh31_gparen = varchar(1) = Parentesco 
                 rh31_depend = varchar(1) = Salário Família 
                 rh31_irf = varchar(1) = IRF 
                 rh31_especi = varchar(1) = Especial 
                 rh31_fins_previdenciarios = bool = Dependente para fins previdenciários 
                 rh31_tipoparentesco = char(2) = tipo de parentesco esocial 
                 rh31_descricaoparentesco = varchar(100) = Descrição do Parentesco 
                 rh31_irfcomplementar = bool = Dependente Complementar somente para IR 
                 ";
    /**
     * @var array
     */
    private $join = array();

    public function __construct()
    {
        $this->rotulo = new rotulo("rhdepend"); 
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
       $this->rh31_codigo = ($this->rh31_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_codigo"]:$this->rh31_codigo);
       $this->rh31_regist = ($this->rh31_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_regist"]:$this->rh31_regist);
       $this->rh31_nome = ($this->rh31_nome == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_nome"]:$this->rh31_nome);
       if($this->rh31_dtnasc == ""){
         $this->rh31_dtnasc_dia = ($this->rh31_dtnasc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_dtnasc_dia"]:$this->rh31_dtnasc_dia);
         $this->rh31_dtnasc_mes = ($this->rh31_dtnasc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_dtnasc_mes"]:$this->rh31_dtnasc_mes);
         $this->rh31_dtnasc_ano = ($this->rh31_dtnasc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_dtnasc_ano"]:$this->rh31_dtnasc_ano);
         if($this->rh31_dtnasc_dia != ""){
            $this->rh31_dtnasc = $this->rh31_dtnasc_ano."-".$this->rh31_dtnasc_mes."-".$this->rh31_dtnasc_dia;
         }
       }
       $this->rh31_gparen = ($this->rh31_gparen == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_gparen"]:$this->rh31_gparen);
       $this->rh31_depend = ($this->rh31_depend == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_depend"]:$this->rh31_depend);
       $this->rh31_irf = ($this->rh31_irf === null ?@$GLOBALS["HTTP_POST_VARS"]["rh31_irf"]:$this->rh31_irf);
       $this->rh31_especi = ($this->rh31_especi == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_especi"]:$this->rh31_especi);
       $this->rh31_fins_previdenciarios = ($this->rh31_fins_previdenciarios == null ?@$GLOBALS["HTTP_POST_VARS"]["rh31_fins_previdenciarios"]:$this->rh31_fins_previdenciarios);
       $this->rh31_tipoparentesco = ($this->rh31_tipoparentesco == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_tipoparentesco"]:$this->rh31_tipoparentesco);
       $this->rh31_descricaoparentesco = ($this->rh31_descricaoparentesco == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_descricaoparentesco"]:$this->rh31_descricaoparentesco);
       $this->rh31_irfcomplementar = ($this->rh31_irfcomplementar == null ?@$GLOBALS["HTTP_POST_VARS"]["rh31_irfcomplementar"]:$this->rh31_irfcomplementar);
     }else{
       $this->rh31_codigo = ($this->rh31_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh31_codigo"]:$this->rh31_codigo);
     }
   }

    public function incluir($rh31_codigo)
    {
      $this->atualizacampos();
     if($this->rh31_regist == null ){ 
       $this->erro_sql = " Campo Matrícula do Servidor não informado.";
       $this->erro_campo = "rh31_regist";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_nome == null ){ 
       $this->erro_sql = " Campo Nome do Dependente não informado.";
       $this->erro_campo = "rh31_nome";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_dtnasc == null ){ 
       $this->erro_sql = " Campo Data de Nascimento não informado.";
       $this->erro_campo = "rh31_dtnasc_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_gparen == null ){ 
       $this->erro_sql = " Campo Parentesco não informado.";
       $this->erro_campo = "rh31_gparen";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_depend == null ){ 
       $this->erro_sql = " Campo Salário Família não informado.";
       $this->erro_campo = "rh31_depend";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_irf === null ){
       $this->erro_sql = " Campo IRF não informado.";
       $this->erro_campo = "rh31_irf";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_especi == null ){ 
       $this->erro_sql = " Campo Especial não informado.";
       $this->erro_campo = "rh31_especi";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_fins_previdenciarios == null ){ 
       $this->erro_sql = " Campo Dependente para fins previdenciários não informado.";
       $this->erro_campo = "rh31_fins_previdenciarios";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh31_irfcomplementar == null ){ 
       $this->erro_sql = " Campo Dependente Complementar somente para IR não informado.";
       $this->erro_campo = "rh31_irfcomplementar";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($rh31_codigo == "" || $rh31_codigo == null ){
       $result = db_query("select nextval('rhdepend_rh31_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: rhdepend_rh31_codigo_seq do campo: rh31_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->rh31_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from rhdepend_rh31_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $rh31_codigo)){
         $this->erro_sql = " Campo rh31_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->rh31_codigo = $rh31_codigo; 
       }
     }
     if(($this->rh31_codigo == null) || ($this->rh31_codigo == "") ){ 
       $this->erro_sql = " Campo rh31_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into rhdepend(
                                       rh31_codigo 
                                      ,rh31_regist 
                                      ,rh31_nome 
                                      ,rh31_dtnasc 
                                      ,rh31_gparen 
                                      ,rh31_depend 
                                      ,rh31_irf 
                                      ,rh31_especi 
                                      ,rh31_fins_previdenciarios 
                                      ,rh31_tipoparentesco 
                                      ,rh31_descricaoparentesco 
                                      ,rh31_irfcomplementar 
                       )
                values (
                                $this->rh31_codigo 
                               ,$this->rh31_regist 
                               ,'$this->rh31_nome' 
                               ,".($this->rh31_dtnasc == "null" || $this->rh31_dtnasc == ""?"null":"'".$this->rh31_dtnasc."'")." 
                               ,'$this->rh31_gparen' 
                               ,'$this->rh31_depend' 
                               ,'$this->rh31_irf' 
                               ,'$this->rh31_especi' 
                               ,'$this->rh31_fins_previdenciarios' 
                               ,'$this->rh31_tipoparentesco' 
                               ,'$this->rh31_descricaoparentesco' 
                               ,'$this->rh31_irfcomplementar' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Dependentes ($this->rh31_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Dependentes já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Dependentes ($this->rh31_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh31_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh31_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,7640,'$this->rh31_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,1186,7640,'','".AddSlashes(pg_result($resaco,0,'rh31_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,7150,'','".AddSlashes(pg_result($resaco,0,'rh31_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,7151,'','".AddSlashes(pg_result($resaco,0,'rh31_nome'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,7152,'','".AddSlashes(pg_result($resaco,0,'rh31_dtnasc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,7153,'','".AddSlashes(pg_result($resaco,0,'rh31_gparen'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,7154,'','".AddSlashes(pg_result($resaco,0,'rh31_depend'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,7155,'','".AddSlashes(pg_result($resaco,0,'rh31_irf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,7156,'','".AddSlashes(pg_result($resaco,0,'rh31_especi'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,1010557,'','".AddSlashes(pg_result($resaco,0,'rh31_fins_previdenciarios'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,1013791,'','".AddSlashes(pg_result($resaco,0,'rh31_tipoparentesco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,1015572,'','".AddSlashes(pg_result($resaco,0,'rh31_descricaoparentesco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1186,1015587,'','".AddSlashes(pg_result($resaco,0,'rh31_irfcomplementar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($rh31_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update rhdepend set ";
     $virgula = "";
     if(trim($this->rh31_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_codigo"])){ 
       $sql  .= $virgula." rh31_codigo = $this->rh31_codigo ";
       $virgula = ",";
       if(trim($this->rh31_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "rh31_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_regist"])){ 
       $sql  .= $virgula." rh31_regist = $this->rh31_regist ";
       $virgula = ",";
       if(trim($this->rh31_regist) == null ){ 
         $this->erro_sql = " Campo Matrícula do Servidor não informado.";
         $this->erro_campo = "rh31_regist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_nome)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_nome"])){ 
       $sql  .= $virgula." rh31_nome = '$this->rh31_nome' ";
       $virgula = ",";
       if(trim($this->rh31_nome) == null ){ 
         $this->erro_sql = " Campo Nome do Dependente não informado.";
         $this->erro_campo = "rh31_nome";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_dtnasc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_dtnasc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh31_dtnasc_dia"] !="") ){ 
       $sql  .= $virgula." rh31_dtnasc = '$this->rh31_dtnasc' ";
       $virgula = ",";
       if(trim($this->rh31_dtnasc) == null ){ 
         $this->erro_sql = " Campo Data de Nascimento não informado.";
         $this->erro_campo = "rh31_dtnasc_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh31_dtnasc_dia"])){ 
         $sql  .= $virgula." rh31_dtnasc = null ";
         $virgula = ",";
         if(trim($this->rh31_dtnasc) == null ){ 
           $this->erro_sql = " Campo Data de Nascimento não informado.";
           $this->erro_campo = "rh31_dtnasc_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->rh31_gparen)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_gparen"])){ 
       $sql  .= $virgula." rh31_gparen = '$this->rh31_gparen' ";
       $virgula = ",";
       if(trim($this->rh31_gparen) == null ){ 
         $this->erro_sql = " Campo Parentesco não informado.";
         $this->erro_campo = "rh31_gparen";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_depend)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_depend"])){ 
       $sql  .= $virgula." rh31_depend = '$this->rh31_depend' ";
       $virgula = ",";
       if(trim($this->rh31_depend) == null ){ 
         $this->erro_sql = " Campo Salário Família não informado.";
         $this->erro_campo = "rh31_depend";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_irf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_irf"])){ 
       $sql  .= $virgula." rh31_irf = '$this->rh31_irf' ";
       $virgula = ",";
       if(trim($this->rh31_irf) === null ){
         $this->erro_sql = " Campo IRF não informado.";
         $this->erro_campo = "rh31_irf";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_especi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_especi"])){ 
       $sql  .= $virgula." rh31_especi = '$this->rh31_especi' ";
       $virgula = ",";
       if(trim($this->rh31_especi) == null ){ 
         $this->erro_sql = " Campo Especial não informado.";
         $this->erro_campo = "rh31_especi";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_fins_previdenciarios)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_fins_previdenciarios"])){ 
       $sql  .= $virgula." rh31_fins_previdenciarios = '$this->rh31_fins_previdenciarios' ";
       $virgula = ",";
       if(trim($this->rh31_fins_previdenciarios) == null ){ 
         $this->erro_sql = " Campo Dependente para fins previdenciários não informado.";
         $this->erro_campo = "rh31_fins_previdenciarios";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh31_tipoparentesco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_tipoparentesco"])){ 
       $sql  .= $virgula." rh31_tipoparentesco = '$this->rh31_tipoparentesco' ";
       $virgula = ",";
     }
     if(trim($this->rh31_descricaoparentesco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_descricaoparentesco"])){ 
       $sql  .= $virgula." rh31_descricaoparentesco = '$this->rh31_descricaoparentesco' ";
       $virgula = ",";
     }
     if(trim($this->rh31_irfcomplementar)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh31_irfcomplementar"])){ 
       $sql  .= $virgula." rh31_irfcomplementar = '$this->rh31_irfcomplementar' ";
       $virgula = ",";
       if(trim($this->rh31_irfcomplementar) == null ){ 
         $this->erro_sql = " Campo Dependente Complementar somente para IR não informado.";
         $this->erro_campo = "rh31_irfcomplementar";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($rh31_codigo!=null){
       $sql .= " rh31_codigo = $this->rh31_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh31_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,7640,'$this->rh31_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_codigo"]) || $this->rh31_codigo != "")
             $resac = db_query("insert into db_acount values($acount,1186,7640,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_codigo'))."','$this->rh31_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_regist"]) || $this->rh31_regist != "")
             $resac = db_query("insert into db_acount values($acount,1186,7150,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_regist'))."','$this->rh31_regist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_nome"]) || $this->rh31_nome != "")
             $resac = db_query("insert into db_acount values($acount,1186,7151,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_nome'))."','$this->rh31_nome',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_dtnasc"]) || $this->rh31_dtnasc != "")
             $resac = db_query("insert into db_acount values($acount,1186,7152,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_dtnasc'))."','$this->rh31_dtnasc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_gparen"]) || $this->rh31_gparen != "")
             $resac = db_query("insert into db_acount values($acount,1186,7153,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_gparen'))."','$this->rh31_gparen',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_depend"]) || $this->rh31_depend != "")
             $resac = db_query("insert into db_acount values($acount,1186,7154,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_depend'))."','$this->rh31_depend',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_irf"]) || $this->rh31_irf != "")
             $resac = db_query("insert into db_acount values($acount,1186,7155,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_irf'))."','$this->rh31_irf',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_especi"]) || $this->rh31_especi != "")
             $resac = db_query("insert into db_acount values($acount,1186,7156,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_especi'))."','$this->rh31_especi',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_fins_previdenciarios"]) || $this->rh31_fins_previdenciarios != "")
             $resac = db_query("insert into db_acount values($acount,1186,1010557,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_fins_previdenciarios'))."','$this->rh31_fins_previdenciarios',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_tipoparentesco"]) || $this->rh31_tipoparentesco != "")
             $resac = db_query("insert into db_acount values($acount,1186,1013791,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_tipoparentesco'))."','$this->rh31_tipoparentesco',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_descricaoparentesco"]) || $this->rh31_descricaoparentesco != "")
             $resac = db_query("insert into db_acount values($acount,1186,1015572,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_descricaoparentesco'))."','$this->rh31_descricaoparentesco',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh31_irfcomplementar"]) || $this->rh31_irfcomplementar != "")
             $resac = db_query("insert into db_acount values($acount,1186,1015587,'".AddSlashes(pg_result($resaco,$conresaco,'rh31_irfcomplementar'))."','$this->rh31_irfcomplementar',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Dependentes não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh31_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Dependentes não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh31_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh31_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

   public function excluirByRegist($rh31_regist = null) { 
      $sql = "select rh31_codigo from rhdepend where rh31_regist = {$rh31_regist}";
      $result = db_query($sql);

      if($result) {
        $totalRegistros = pg_num_rows($result);
        for ($row = 0; $row < $totalRegistros; $row++) {
            $current = \db_utils::fieldsMemory($result, $row);
            $this->excluir($current->rh31_codigo);
        }
        if ($totalRegistros == 0) {
          $this->erro_banco = "";
          $this->erro_sql = "Dependentes não Encontrado. Exclusão não Efetuada.\\n";
          $this->erro_sql .= "Valores : ".$rh31_regist;
          $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
          $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
          $this->erro_status = "1";
          $this->numrows_excluir = 0;
          return true;
        }
      } else {
        throw new DBException("Erro ao apagar registros referente a matricula: {$rh31_regist}");
      }
   }

    public function excluir($rh31_codigo=null, $dbwhere = null)
    {
        if (empty($rh31_codigo)) {
            throw new Exception('Campo rh31_codigo é obrigatório!');
        }

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($rh31_codigo));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,7640,'$rh31_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,1186,7640,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,7150,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,7151,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_nome'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,7152,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_dtnasc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,7153,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_gparen'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,7154,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_depend'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,7155,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_irf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,7156,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_especi'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,1010557,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_fins_previdenciarios'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,1013791,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_tipoparentesco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,1015572,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_descricaoparentesco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1186,1015587,'','".AddSlashes(pg_result($resaco,$iresaco,'rh31_irfcomplementar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from rhdepend
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh31_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh31_codigo = $rh31_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Dependentes não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh31_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Dependentes não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh31_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh31_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:rhdepend";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($rh31_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from rhdepend ";
     $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = rhdepend.rh31_regist";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
     $sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
     $sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
     $sql .= "      left  join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
     $sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
     $sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
     $sql .= "      left  join rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
     $sql .= "      inner join rhreajusteparidade  on  rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh31_codigo)) {
         $sql2 .= " where rhdepend.rh31_codigo = $rh31_codigo "; 
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

    public function sql_query_file($rh31_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from rhdepend ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh31_codigo)){
         $sql2 .= " where rhdepend.rh31_codigo = $rh31_codigo "; 
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

   function sql_query_cgm ( $rh31_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from rhdepend ";
     $sql .= "      inner join rhpessoal    on rhpessoal.rh01_regist = rhdepend.rh31_regist ";
     $sql .= "      inner join cgm          on cgm.z01_numcgm = rhpessoal.rh01_numcgm";
     $sql .= "      inner join rhpessoalmov on rhpessoalmov.rh02_anousu = ".db_anofolha()."
                                           and rhpessoalmov.rh02_mesusu = ".db_mesfolha()."
                                           and rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
																					 and rhpessoalmov.rh02_instit = ".db_getsession("DB_instit")." ";
     $sql2 = "";
     if($dbwhere==""){
       if($rh31_codigo!=null ){
         $sql2 .= " where rhdepend.rh31_codigo = $rh31_codigo "; 
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
   function sql_query_relPREVID ( $rh31_codigo=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from rhdepend ";
     $sql .= "      inner join rhpessoal     on rhpessoal.rh01_regist     = rhdepend.rh31_regist";
     $sql .= "      inner join rhpessoalmov  on rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
		                                        and rhpessoalmov.rh02_anousu  = ".db_anofolha()." 
																						and rhpessoalmov.rh02_mesusu  = ".db_mesfolha()."
																						and rhpessoalmov.rh02_instit  = ".db_getsession("DB_instit")." ";
     $sql .= "      inner join cgm           on cgm.z01_numcgm            = rhpessoal.rh01_numcgm";
     $sql .= "      left  join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes";
     $sql2 = "";
     if($dbwhere==""){
       if($rh31_codigo!=null ){
         $sql2 .= " where rhdepend.rh31_codigo = $rh31_codigo "; 
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
  
  public function sql_query_file_dependeplug($rh31_codigo=null,$sCampos="*",$sOrdem=null,$sWhere="") {
      
      $sSql  = "select ";
      $sSql .= " {$sCampos} ";
      $sSql .= " from pessoal.rhdepend ";
      $sSql .= "      left join pessoal.rhdependeplug on rhdependeplug.dp01_rhdepend = rhdepend.rh31_codigo";
      if ($sWhere == "") {
        if ($rh31_codigo!=null ) {
            $sSql .= " where rhdepend.rh31_codigo = $rh31_codigo ";
        }
      } else if ($sWhere != "") {
        $sSql .= " where $sWhere";
      }
      if($sOrdem != null ){
          $sSql .= " order by {$sOrdem}";
      }
      return $sSql;
  }

      /**
     * @param $table
     * @param $reference
     * @param $operator
     * @param $foreign
     * @param bool $left
     * @return cl_rhdepend
     */
    public function addJoin($table, $reference, $operator, $foreign, $left = false)
    {
        if (array_key_exists($table, $this->join)) {
            $this->join[$table] .= " AND {$reference} {$operator} {$foreign}";
        } else {
            $sql = "JOIN {$table} ON {$reference} {$operator} {$foreign}";

            $this->join[$table] = $left ? "LEFT {$sql}" : $sql;
        }

        return $this;
    }

    /**
     * @param array $columns
     * @param array $where
     * @param array $order
     * @return string
     */
    public function sql($columns = array('*'), $where = array(), $order = array())
    {
        $columns = implode(', ', $columns);
        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $join = implode(' ', $this->join);
        $order = $order ? 'ORDER BY ' . implode(', ', $order) : '';

        return "SELECT {$columns} FROM rhdepend {$join} {$where} {$order}";
    }

}
