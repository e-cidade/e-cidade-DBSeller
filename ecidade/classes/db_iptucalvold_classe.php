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

class cl_iptucalvold
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
    public $j157_sequencial = 0; 
    public $j157_anousu = 0; 
    public $j157_matric = 0; 
    public $j157_receit = 0; 
    public $j157_valor = 0; 
    public $j157_quant = 0; 
    public $j157_codhis = 0; 
    public $j157_iptucalclog = 0; 
    public $j157_gerafinanc =  't';
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 j157_sequencial = int4 = Sequencial itpucalvold 
                 j157_anousu = int4 = Exercicio 
                 j157_matric = int4 = Matricula 
                 j157_receit = int4 = Receita 
                 j157_valor = int4 = Valor 
                 j157_quant = float8 = Quantidade 
                 j157_codhis = float8 = Código do histórico 
                 j157_iptucalclog = int4 = Sequencial Iptucalclog 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("iptucalvold"); 
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
       $this->j157_sequencial = ($this->j157_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_sequencial"]:$this->j157_sequencial);
       $this->j157_anousu = ($this->j157_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_anousu"]:$this->j157_anousu);
       $this->j157_matric = ($this->j157_matric == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_matric"]:$this->j157_matric);
       $this->j157_receit = ($this->j157_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_receit"]:$this->j157_receit);
       $this->j157_valor = ($this->j157_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_valor"]:$this->j157_valor);
       $this->j157_quant = ($this->j157_quant == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_quant"]:$this->j157_quant);
       $this->j157_codhis = ($this->j157_codhis == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_codhis"]:$this->j157_codhis);
       $this->j157_iptucalclog = ($this->j157_iptucalclog == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_iptucalclog"]:$this->j157_iptucalclog);
       $this->j157_gerafinanc = ($this->j157_gerafinanc == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_gerafinanc"]:$this->j157_gerafinanc);
     }else{
       $this->j157_sequencial = ($this->j157_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j157_sequencial"]:$this->j157_sequencial);
     }
   }

    public function incluir($j157_sequencial)
    {
      $this->atualizacampos();
     if($this->j157_anousu == null ){ 
       $this->erro_sql = " Campo Exercicio não informado.";
       $this->erro_campo = "j157_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j157_matric == null ){ 
       $this->erro_sql = " Campo Matricula não informado.";
       $this->erro_campo = "j157_matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j157_receit == null ){ 
       $this->erro_sql = " Campo Receita não informado.";
       $this->erro_campo = "j157_receit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j157_valor == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "j157_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j157_quant == null ){ 
       $this->erro_sql = " Campo Quantidade não informado.";
       $this->erro_campo = "j157_quant";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j157_codhis == null ){ 
       $this->erro_sql = " Campo Código do histórico não informado.";
       $this->erro_campo = "j157_codhis";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j157_iptucalclog == null ){ 
       $this->erro_sql = " Campo Sequencial Iptucalclog não informado.";
       $this->erro_campo = "j157_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($j157_sequencial == "" || $j157_sequencial == null ){
       $result = db_query("select nextval('iptucalvold_j157_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: iptucalvold_j157_sequencial_seq do campo: j157_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->j157_sequencial = pg_fetch_result($result,0,0); 
     }else{
       $result = db_query("select last_value from iptucalvold_j157_sequencial_seq");
       if(($result != false) && (pg_fetch_result($result,0,0) < $j157_sequencial)){
         $this->erro_sql = " Campo j157_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j157_sequencial = $j157_sequencial; 
       }
     }
     if(($this->j157_sequencial == null) || ($this->j157_sequencial == "") ){ 
       $this->erro_sql = " Campo j157_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into iptucalvold(
                                       j157_sequencial 
                                      ,j157_anousu 
                                      ,j157_matric 
                                      ,j157_receit 
                                      ,j157_valor 
                                      ,j157_quant 
                                      ,j157_codhis 
                                      ,j157_iptucalclog
                                      ,j157_gerafinanc
                       )
                values (
                                $this->j157_sequencial 
                               ,$this->j157_anousu 
                               ,$this->j157_matric 
                               ,$this->j157_receit 
                               ,$this->j157_valor 
                               ,$this->j157_quant 
                               ,$this->j157_codhis 
                               ,$this->j157_iptucalclog 
                               ,'$this->j157_gerafinanc'
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Histórico Iptucalv ($this->j157_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Histórico Iptucalv já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Histórico Iptucalv ($this->j157_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j157_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->j157_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014377,'$this->j157_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010500,1014377,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010500,1010897,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010500,1010898,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010500,1010899,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_receit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010500,1010900,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010500,1010901,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010500,1010902,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_codhis'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010500,1010903,'','".AddSlashes(pg_fetch_result($resaco,0,'j157_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($j157_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update iptucalvold set ";
     $virgula = "";
     if(trim($this->j157_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_sequencial"])){ 
       $sql  .= $virgula." j157_sequencial = $this->j157_sequencial ";
       $virgula = ",";
       if(trim($this->j157_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial itpucalvold não informado.";
         $this->erro_campo = "j157_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j157_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_anousu"])){ 
       $sql  .= $virgula." j157_anousu = $this->j157_anousu ";
       $virgula = ",";
       if(trim($this->j157_anousu) == null ){ 
         $this->erro_sql = " Campo Exercicio não informado.";
         $this->erro_campo = "j157_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j157_matric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_matric"])){ 
       $sql  .= $virgula." j157_matric = $this->j157_matric ";
       $virgula = ",";
       if(trim($this->j157_matric) == null ){ 
         $this->erro_sql = " Campo Matricula não informado.";
         $this->erro_campo = "j157_matric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j157_receit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_receit"])){ 
       $sql  .= $virgula." j157_receit = $this->j157_receit ";
       $virgula = ",";
       if(trim($this->j157_receit) == null ){ 
         $this->erro_sql = " Campo Receita não informado.";
         $this->erro_campo = "j157_receit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j157_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_valor"])){ 
       $sql  .= $virgula." j157_valor = $this->j157_valor ";
       $virgula = ",";
       if(trim($this->j157_valor) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "j157_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j157_quant)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_quant"])){ 
       $sql  .= $virgula." j157_quant = $this->j157_quant ";
       $virgula = ",";
       if(trim($this->j157_quant) == null ){ 
         $this->erro_sql = " Campo Quantidade não informado.";
         $this->erro_campo = "j157_quant";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j157_codhis)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_codhis"])){ 
       $sql  .= $virgula." j157_codhis = $this->j157_codhis ";
       $virgula = ",";
       if(trim($this->j157_codhis) == null ){ 
         $this->erro_sql = " Campo Código do histórico não informado.";
         $this->erro_campo = "j157_codhis";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j157_iptucalclog)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j157_iptucalclog"])){ 
       $sql  .= $virgula." j157_iptucalclog = $this->j157_iptucalclog ";
       $virgula = ",";
       if(trim($this->j157_iptucalclog) == null ){ 
         $this->erro_sql = " Campo Sequencial Iptucalclog não informado.";
         $this->erro_campo = "j157_iptucalclog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($j157_sequencial!=null){
       $sql .= " j157_sequencial = $this->j157_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->j157_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_fetch_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014377,'$this->j157_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_sequencial"]) || $this->j157_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1014377,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_sequencial'))."','$this->j157_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_anousu"]) || $this->j157_anousu != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1010897,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_anousu'))."','$this->j157_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_matric"]) || $this->j157_matric != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1010898,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_matric'))."','$this->j157_matric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_receit"]) || $this->j157_receit != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1010899,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_receit'))."','$this->j157_receit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_valor"]) || $this->j157_valor != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1010900,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_valor'))."','$this->j157_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_quant"]) || $this->j157_quant != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1010901,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_quant'))."','$this->j157_quant',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_codhis"]) || $this->j157_codhis != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1010902,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_codhis'))."','$this->j157_codhis',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j157_iptucalclog"]) || $this->j157_iptucalclog != "")
             $resac = db_query("insert into db_acount values($acount,1010500,1010903,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'j157_iptucalclog'))."','$this->j157_iptucalclog',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Histórico Iptucalv não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j157_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Histórico Iptucalv não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j157_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j157_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($j157_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($j157_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_fetch_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014377,'$j157_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010500,1014377,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010500,1010897,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010500,1010898,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010500,1010899,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_receit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010500,1010900,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010500,1010901,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010500,1010902,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_codhis'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010500,1010903,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'j157_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from iptucalvold
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($j157_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " j157_sequencial = $j157_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Histórico Iptucalv não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j157_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Histórico Iptucalv não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j157_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$j157_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:iptucalvold";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($j157_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from iptucalvold ";
     $sql .= "      inner join iptucalclogmat  on  iptucalclogmat.j28_codigo = iptucalvold.j157_iptucalclog and  iptucalclogmat.j28_matric = iptucalvold.j157_matric";
     $sql .= "      inner join iptubase  on  iptubase.j01_matric = iptucalclogmat.j28_matric";
     $sql .= "      inner join iptucalclog  on  iptucalclog.j27_codigo = iptucalclogmat.j28_codigo";
     $sql .= "      inner join iptucadlogcalc  on  iptucadlogcalc.j62_codigo = iptucalclogmat.j28_tipologcalc";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j157_sequencial)) {
         $sql2 .= " where iptucalvold.j157_sequencial = $j157_sequencial "; 
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

    public function sql_query_file($j157_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from iptucalvold ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j157_sequencial)){
         $sql2 .= " where iptucalvold.j157_sequencial = $j157_sequencial "; 
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


  public function salvarIptucalvOld($iAnousu, $iMatric, $iCalclog) {

      if ( !db_utils::inTransaction()) {
        throw new Exception ("Sem Transação Ativa");
      }

      if (empty($iAnousu)) {
        $this->erro_sql = " Campo Exercício não informado para gerar informações anteriores.";
        $this->erro_campo = "j157_anousu";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
      if (empty($iMatric)) {
        $this->erro_sql = " Campo Matrícula não informado para gerar informações anteriores.";
        $this->erro_campo = "j157_matric";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
      if (empty($iCalclog)) {
        $this->erro_sql = " Campo CalcLog não informado para gerar informações anteriores.";
        $this->erro_campo = "j157_iptucalclog";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }

      $sSqlIptucalv = "select j21_anousu, 
                              j21_matric, 
                              coalesce(j21_receit, 0) as j21_receit, 
                              coalesce(j21_valor, 0)  as j21_valor, 
                              coalesce(j21_quant, 0)  as j21_quant, 
                              coalesce(j21_codhis, 0) as j21_codhis,
                              j21_gerafinanc 
                         from iptucalv where j21_anousu = {$iAnousu} and j21_matric = {$iMatric};";
      $rsIptucalv = db_query($sSqlIptucalv) or die($sSqlIptucalv);

      if (!$rsIptucalv) {
          throw new DBException("Não foi possivel buscar dados do cálculo (valores) (".pg_last_error().")");
      }
      for ($i = 0; $i < pg_num_rows($rsIptucalv); $i++) {

          global $j21_anousu;
          global $j21_matric;
          global $j21_receit;
          global $j21_valor;
          global $j21_quant;
          global $j21_codhis;
          global $j21_gerafinanc;

          db_fieldsmemory($rsIptucalv,$i);

          $this->j157_anousu         = $j21_anousu;
          $this->j157_matric         = $j21_matric;
          $this->j157_receit         = $j21_receit;
          $this->j157_valor          = $j21_valor;
          $this->j157_quant          = $j21_quant;
          $this->j157_codhis         = $j21_codhis;
          $this->j157_iptucalclog    = $iCalclog;
          $this->j157_gerafinanc     = $j21_gerafinanc;
          $this->incluir(null);

          if ($this->erro_status == 0) {
             throw new DBException("Não foi possivel salvar dados do cálculo (valores) (".pg_last_error().")");
             return false;
          }
      }

      return true;

    }

}
