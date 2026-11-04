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

class cl_iptucaleold
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
    public $j162_sequencial = 0; 
    public $j162_anousu = 0; 
    public $j162_matric = 0; 
    public $j162_idcons = 0; 
    public $j162_areaed = 0; 
    public $j162_vm2 = 0; 
    public $j162_pontos = 0; 
    public $j162_valor = 0; 
    public $j162_iptucalclog = 0;   
    public $j162_gerafinanc = 't';
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 j162_sequencial = int4 = Sequencial 
                 j162_anousu = int4 = Exercicio 
                 j162_matric = int4 = Matricula 
                 j162_idcons = int4 = Construcao 
                 j162_areaed = float8 = Area Construida 
                 j162_vm2 = float8 = Valor M2 Construcao 
                 j162_pontos = int4 = Pontuacao 
                 j162_valor = float8 = Valor venal 
                 j162_iptucalclog = int4 = Código iptucalclog 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("iptucaleold"); 
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
       $this->j162_sequencial = ($this->j162_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_sequencial"]:$this->j162_sequencial);
       $this->j162_anousu = ($this->j162_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_anousu"]:$this->j162_anousu);
       $this->j162_matric = ($this->j162_matric == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_matric"]:$this->j162_matric);
       $this->j162_idcons = ($this->j162_idcons == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_idcons"]:$this->j162_idcons);
       $this->j162_areaed = ($this->j162_areaed == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_areaed"]:$this->j162_areaed);
       $this->j162_vm2 = ($this->j162_vm2 == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_vm2"]:$this->j162_vm2);
       $this->j162_pontos = ($this->j162_pontos == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_pontos"]:$this->j162_pontos);
       $this->j162_valor = ($this->j162_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_valor"]:$this->j162_valor);
       $this->j162_iptucalclog = ($this->j162_iptucalclog == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_iptucalclog"]:$this->j162_iptucalclog);
       $this->j162_gerafinanc = ($this->j162_gerafinanc == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_gerafinanc"]:$this->j162_gerafinanc);
     }else{
       $this->j162_sequencial = ($this->j162_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j162_sequencial"]:$this->j162_sequencial);
     }
   }

    public function incluir($j162_sequencial)
    {
      $this->atualizacampos();
     if($this->j162_anousu == null ){ 
       $this->erro_sql = " Campo Exercicio não informado.";
       $this->erro_campo = "j162_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j162_matric == null ){ 
       $this->erro_sql = " Campo Matricula não informado.";
       $this->erro_campo = "j162_matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j162_idcons == null ){ 
       $this->erro_sql = " Campo Construcao não informado.";
       $this->erro_campo = "j162_idcons";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j162_areaed == null ){ 
       $this->erro_sql = " Campo Area Construida não informado.";
       $this->erro_campo = "j162_areaed";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j162_vm2 == null ){ 
       $this->erro_sql = " Campo Valor M2 Construcao não informado.";
       $this->erro_campo = "j162_vm2";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j162_pontos == null ){ 
       $this->erro_sql = " Campo Pontuacao não informado.";
       $this->erro_campo = "j162_pontos";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j162_valor == null ){ 
       $this->erro_sql = " Campo Valor venal não informado.";
       $this->erro_campo = "j162_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->j162_iptucalclog == null ){ 
       $this->erro_sql = " Campo Iptucalclogmat não informado.";
       $this->erro_campo = "j162_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($j162_sequencial == "" || $j162_sequencial == null ){
       $result = db_query("select nextval('iptucaleold_j162_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: iptucaleold_j162_sequencial_seq do campo: j162_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->j162_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from iptucaleold_j162_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $j162_sequencial)){
         $this->erro_sql = " Campo j162_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j162_sequencial = $j162_sequencial; 
       }
     }
     if(($this->j162_sequencial == null) || ($this->j162_sequencial == "") ){ 
       $this->erro_sql = " Campo j162_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into iptucaleold(
                                       j162_sequencial 
                                      ,j162_anousu 
                                      ,j162_matric 
                                      ,j162_idcons 
                                      ,j162_areaed 
                                      ,j162_vm2 
                                      ,j162_pontos 
                                      ,j162_valor 
                                      ,j162_iptucalclog 
                                      ,j162_gerafinanc
                       )
                values (
                                $this->j162_sequencial 
                               ,$this->j162_anousu 
                               ,$this->j162_matric 
                               ,$this->j162_idcons 
                               ,$this->j162_areaed 
                               ,$this->j162_vm2 
                               ,$this->j162_pontos 
                               ,$this->j162_valor 
                               ,$this->j162_iptucalclog
                               ,'$this->j162_gerafinanc'
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = " ($this->j162_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = " já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = " ($this->j162_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j162_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->j162_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014376,'$this->j162_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010523,1014376,'','".AddSlashes(pg_result($resaco,0,'j162_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011024,'','".AddSlashes(pg_result($resaco,0,'j162_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011025,'','".AddSlashes(pg_result($resaco,0,'j162_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011026,'','".AddSlashes(pg_result($resaco,0,'j162_idcons'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011027,'','".AddSlashes(pg_result($resaco,0,'j162_areaed'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011028,'','".AddSlashes(pg_result($resaco,0,'j162_vm2'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011029,'','".AddSlashes(pg_result($resaco,0,'j162_pontos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011030,'','".AddSlashes(pg_result($resaco,0,'j162_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010523,1011032,'','".AddSlashes(pg_result($resaco,0,'j162_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($j162_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update iptucaleold set ";
     $virgula = "";
     if(trim($this->j162_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_sequencial"])){ 
       $sql  .= $virgula." j162_sequencial = $this->j162_sequencial ";
       $virgula = ",";
       if(trim($this->j162_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "j162_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j162_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_anousu"])){ 
       $sql  .= $virgula." j162_anousu = $this->j162_anousu ";
       $virgula = ",";
       if(trim($this->j162_anousu) == null ){ 
         $this->erro_sql = " Campo Exercicio não informado.";
         $this->erro_campo = "j162_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j162_matric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_matric"])){ 
       $sql  .= $virgula." j162_matric = $this->j162_matric ";
       $virgula = ",";
       if(trim($this->j162_matric) == null ){ 
         $this->erro_sql = " Campo Matricula não informado.";
         $this->erro_campo = "j162_matric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j162_idcons)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_idcons"])){ 
       $sql  .= $virgula." j162_idcons = $this->j162_idcons ";
       $virgula = ",";
       if(trim($this->j162_idcons) == null ){ 
         $this->erro_sql = " Campo Construcao não informado.";
         $this->erro_campo = "j162_idcons";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j162_areaed)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_areaed"])){ 
       $sql  .= $virgula." j162_areaed = $this->j162_areaed ";
       $virgula = ",";
       if(trim($this->j162_areaed) == null ){ 
         $this->erro_sql = " Campo Area Construida não informado.";
         $this->erro_campo = "j162_areaed";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j162_vm2)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_vm2"])){ 
       $sql  .= $virgula." j162_vm2 = $this->j162_vm2 ";
       $virgula = ",";
       if(trim($this->j162_vm2) == null ){ 
         $this->erro_sql = " Campo Valor M2 Construcao não informado.";
         $this->erro_campo = "j162_vm2";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j162_pontos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_pontos"])){ 
       $sql  .= $virgula." j162_pontos = $this->j162_pontos ";
       $virgula = ",";
       if(trim($this->j162_pontos) == null ){ 
         $this->erro_sql = " Campo Pontuacao não informado.";
         $this->erro_campo = "j162_pontos";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j162_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_valor"])){ 
       $sql  .= $virgula." j162_valor = $this->j162_valor ";
       $virgula = ",";
       if(trim($this->j162_valor) == null ){ 
         $this->erro_sql = " Campo Valor venal não informado.";
         $this->erro_campo = "j162_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->j162_iptucalclog)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j162_iptucalclog"])){ 
       $sql  .= $virgula." j162_iptucalclog = $this->j162_iptucalclog ";
       $virgula = ",";
       if(trim($this->j162_iptucalclog) == null ){ 
         $this->erro_sql = " Campo Iptucalclogmat não informado.";
         $this->erro_campo = "j162_iptucalclog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($j162_sequencial!=null){
       $sql .= " j162_sequencial = $this->j162_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->j162_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014376,'$this->j162_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_sequencial"]) || $this->j162_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1014376,'".AddSlashes(pg_result($resaco,$conresaco,'j162_sequencial'))."','$this->j162_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_anousu"]) || $this->j162_anousu != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011024,'".AddSlashes(pg_result($resaco,$conresaco,'j162_anousu'))."','$this->j162_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_matric"]) || $this->j162_matric != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011025,'".AddSlashes(pg_result($resaco,$conresaco,'j162_matric'))."','$this->j162_matric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_idcons"]) || $this->j162_idcons != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011026,'".AddSlashes(pg_result($resaco,$conresaco,'j162_idcons'))."','$this->j162_idcons',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_areaed"]) || $this->j162_areaed != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011027,'".AddSlashes(pg_result($resaco,$conresaco,'j162_areaed'))."','$this->j162_areaed',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_vm2"]) || $this->j162_vm2 != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011028,'".AddSlashes(pg_result($resaco,$conresaco,'j162_vm2'))."','$this->j162_vm2',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_pontos"]) || $this->j162_pontos != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011029,'".AddSlashes(pg_result($resaco,$conresaco,'j162_pontos'))."','$this->j162_pontos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_valor"]) || $this->j162_valor != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011030,'".AddSlashes(pg_result($resaco,$conresaco,'j162_valor'))."','$this->j162_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j162_iptucalclog"]) || $this->j162_iptucalclog != "")
             $resac = db_query("insert into db_acount values($acount,1010523,1011032,'".AddSlashes(pg_result($resaco,$conresaco,'j162_iptucalclog'))."','$this->j162_iptucalclog',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j162_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j162_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j162_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($j162_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($j162_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014376,'$j162_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010523,1014376,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011024,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011025,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011026,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_idcons'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011027,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_areaed'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011028,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_vm2'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011029,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_pontos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011030,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010523,1011032,'','".AddSlashes(pg_result($resaco,$iresaco,'j162_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from iptucaleold
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($j162_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " j162_sequencial = $j162_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j162_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j162_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$j162_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:iptucaleold";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($j162_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from iptucaleold ";
     $sql .= "      inner join iptucalclogmat  on  iptucalclogmat.j28_codigo = iptucaleold.j162_iptucalclog and  iptucalclogmat.j28_matric = iptucaleold.j162_matric";
     $sql .= "      inner join iptubase  on  iptubase.j01_matric = iptucalclogmat.j28_matric";
     $sql .= "      inner join iptucalclog  on  iptucalclog.j27_codigo = iptucalclogmat.j28_codigo";
     $sql .= "      inner join iptucadlogcalc  on  iptucadlogcalc.j62_codigo = iptucalclogmat.j28_tipologcalc";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j162_sequencial)) {
         $sql2 .= " where iptucaleold.j162_sequencial = $j162_sequencial "; 
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

    public function sql_query_file($j162_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from iptucaleold ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j162_sequencial)){
         $sql2 .= " where iptucaleold.j162_sequencial = $j162_sequencial "; 
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

  public function salvarIptucaleOld($iAnousu, $iMatric, $iCalclog) {

      if ( !db_utils::inTransaction()) {
        throw new Exception ("Sem Transação Ativa");
      }

      if (empty($iAnousu)) {
        $this->erro_sql = " Campo Exercício não informado para gerar informações anteriores.";
        $this->erro_campo = "j162_anousu";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
      if (empty($iMatric)) {
        $this->erro_sql = " Campo Matrícula não informado para gerar informações anteriores.";
        $this->erro_campo = "j162_matric";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
      if (empty($iCalclog)) {
        $this->erro_sql = " Campo CalcLog não informado para gerar informações anteriores.";
        $this->erro_campo = "j162_iptucalclog";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }

      // iptucaleold (construcoes)
      $sSqlIptucale = "select j22_anousu,
                              j22_matric,
                              coalesce(j22_idcons, 0) as j22_idcons,
                              coalesce(j22_areaed, 0) as j22_areaed,
                              coalesce(j22_vm2   , 0) as j22_vm2   ,
                              coalesce(j22_pontos, 0) as j22_pontos,
                              coalesce(j22_valor , 0) as j22_valor ,
                              j22_gerafinanc
                         from iptucale where j22_anousu = {$iAnousu} and j22_matric = {$iMatric};";
      $rsIptucale = db_query($sSqlIptucale) or die($sSqlIptucale);

      //db_criatabela($rsIptucale);
      if (!$rsIptucale) {
          throw new DBException("Não foi possivel buscar dados do cálculo (contruções) (".pg_last_error().")");
      }
      for($i = 0; $i < pg_numrows($rsIptucale); $i++) {
          db_fieldsmemory($rsIptucale,$i);
          global $j22_anousu;
          global $j22_matric;
          global $j22_idcons;
          global $j22_areaed;
          global $j22_vm2;
          global $j22_pontos;
          global $j22_valor;
          global $j27_codigo;
          global $j22_gerafinanc;

          $this->j162_anousu         = $j22_anousu;
          $this->j162_matric         = $j22_matric;
          $this->j162_idcons         = $j22_idcons;
          $this->j162_areaed         = $j22_areaed;
          $this->j162_vm2            = $j22_vm2;
          $this->j162_pontos         = $j22_pontos;
          $this->j162_valor          = $j22_valor;
          $this->j162_iptucalclog    = $j27_codigo;
          $this->j162_gerafinanc     = $j22_gerafinanc;
          $this->incluir(null);

          if ($this->erro_status == 0) {
             throw new DBException("Não foi possivel salvar dados do cálculo (construções) (".pg_last_error().")");
             return false;
          }

      }
          
      return true;

  }

}
