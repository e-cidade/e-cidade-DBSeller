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

class cl_iptucalcold
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
    public $j223_sequencial = 0; 
    public $j223_anousu = 0; 
    public $j223_matric = 0; 
    public $j223_testad = 0; 
    public $j223_arealo = 0; 
    public $j223_areafr = 0; 
    public $j223_areaed = 0; 
    public $j223_m2terr = 0; 
    public $j223_vlrter = 0; 
    public $j223_aliq = 0; 
    public $j223_vlrisen = 0; 
    public $j223_tipoim = null; 
    public $j223_manual = null; 
    public $j223_tipocalculo = 0; 
    public $j223_iptucalclog = 0; 
    public $j223_gerafinanc  =  't';
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 j223_sequencial = int4 = j223_sequencial 
                 j223_anousu = int4 = Exercício 
                 j223_matric = int4 = Mátricula 
                 j223_testad = float8 = Testada do Cálculo 
                 j223_arealo = float8 = Área Calculada 
                 j223_areafr = float8 = Área Fracionada 
                 j223_areaed = float8 = Área Total Edificada 
                 j223_m2terr = float8 = Valor M2 Terreno 
                 j223_vlrter = float8 = Valor Venal Terreno 
                 j223_aliq = float8 = Alíquota do IPTU 
                 j223_vlrisen = float8 = Valor da Isenção 
                 j223_tipoim = varchar(1) = Tipo de Imposto 
                 j223_manual = text = Log do Cálculo 
                 j223_tipocalculo = int4 = Tipo de Cálculo 
                 j223_iptucalclog = int4 = Iptucalclog 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("iptucalcold"); 
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
       $this->j223_sequencial = ($this->j223_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_sequencial"]:$this->j223_sequencial);
       $this->j223_anousu = ($this->j223_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_anousu"]:$this->j223_anousu);
       $this->j223_matric = ($this->j223_matric == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_matric"]:$this->j223_matric);
       $this->j223_testad = ($this->j223_testad == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_testad"]:$this->j223_testad);
       $this->j223_arealo = ($this->j223_arealo == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_arealo"]:$this->j223_arealo);
       $this->j223_areafr = ($this->j223_areafr == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_areafr"]:$this->j223_areafr);
       $this->j223_areaed = ($this->j223_areaed == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_areaed"]:$this->j223_areaed);
       $this->j223_m2terr = ($this->j223_m2terr == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_m2terr"]:$this->j223_m2terr);
       $this->j223_vlrter = ($this->j223_vlrter == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_vlrter"]:$this->j223_vlrter);
       $this->j223_aliq = ($this->j223_aliq == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_aliq"]:$this->j223_aliq);
       $this->j223_vlrisen = ($this->j223_vlrisen == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_vlrisen"]:$this->j223_vlrisen);
       $this->j223_tipoim = ($this->j223_tipoim == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_tipoim"]:$this->j223_tipoim);
       $this->j223_manual = ($this->j223_manual == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_manual"]:$this->j223_manual);
       $this->j223_tipocalculo = ($this->j223_tipocalculo == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_tipocalculo"]:$this->j223_tipocalculo);
       $this->j223_iptucalclog = ($this->j223_iptucalclog == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_iptucalclog"]:$this->j223_iptucalclog);
       $this->j223_gerafinanc = ($this->j223_gerafinanc == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_gerafinanc"]:$this->j223_gerafinanc);
     }else{
       $this->j223_sequencial = ($this->j223_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j223_sequencial"]:$this->j223_sequencial);
     }
   }

    public function incluir($j223_sequencial)
    {
     $this->atualizacampos();

     
     if($this->j223_anousu == null ){ 
       $this->erro_sql = " Campo Exercício não informado.";
       $this->erro_campo = "j223_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_matric == null ){ 
       $this->erro_sql = " Campo Mátricula não informado.";
       $this->erro_campo = "j223_matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_testad == null ){ 
       $this->erro_sql = " Campo Testada do Cálculo não informado.";
       $this->erro_campo = "j223_testad";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_arealo == null ){ 
       $this->erro_sql = " Campo Área Calculada não informado.";
       $this->erro_campo = "j223_arealo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_areafr == null ){ 
       $this->erro_sql = " Campo Área Fracionada não informado.";
       $this->erro_campo = "j223_areafr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_areaed == null ){ 
       $this->erro_sql = " Campo Área Total Edificada não informado.";
       $this->erro_campo = "j223_areaed";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_m2terr == null ){ 
       $this->erro_sql = " Campo Valor M2 Terreno não informado.";
       $this->erro_campo = "j223_m2terr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_vlrter == null ){ 
       $this->erro_sql = " Campo Valor Venal Terreno não informado.";
       $this->erro_campo = "j223_vlrter";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_aliq == null ){ 
       $this->erro_sql = " Campo Alíquota do IPTU não informado.";
       $this->erro_campo = "j223_aliq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_vlrisen == null ){ 
       $this->erro_sql = " Campo Valor da Isenção não informado.";
       $this->erro_campo = "j223_vlrisen";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_tipoim == null ){ 
       $this->erro_sql = " Campo Tipo de Imposto não informado.";
       $this->erro_campo = "j223_tipoim";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_manual == null ){ 
       $this->erro_sql = " Campo Log do Cálculo não informado.";
       $this->erro_campo = "j223_manual";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_tipocalculo == null ){ 
       $this->erro_sql = " Campo Tipo de Cálculo não informado.";
       $this->erro_campo = "j223_tipocalculo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j223_iptucalclog == null ){ 
       $this->erro_sql = " Campo Iptucalclog não informado.";
       $this->erro_campo = "j223_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($j223_sequencial == "" || $j223_sequencial == null ){
       $result = db_query("select nextval('iptucalcold_j223_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: iptucalcold_j223_sequencial_seq do campo: j223_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->j223_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from iptucalcold_j223_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $j223_sequencial)){
         $this->erro_sql = " Campo j223_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j223_sequencial = $j223_sequencial; 
       }
     }
     if(($this->j223_sequencial == null) || ($this->j223_sequencial == "") ){ 
       $this->erro_sql = " Campo j223_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into iptucalcold(
                                       j223_sequencial 
                                      ,j223_anousu 
                                      ,j223_matric 
                                      ,j223_testad 
                                      ,j223_arealo 
                                      ,j223_areafr 
                                      ,j223_areaed 
                                      ,j223_m2terr 
                                      ,j223_vlrter 
                                      ,j223_aliq 
                                      ,j223_vlrisen 
                                      ,j223_tipoim 
                                      ,j223_manual 
                                      ,j223_tipocalculo 
                                      ,j223_iptucalclog
                                      ,j223_gerafinanc 
                       )
                values (
                                $this->j223_sequencial 
                               ,$this->j223_anousu 
                               ,$this->j223_matric 
                               ,$this->j223_testad 
                               ,$this->j223_arealo 
                               ,$this->j223_areafr 
                               ,$this->j223_areaed 
                               ,$this->j223_m2terr 
                               ,$this->j223_vlrter 
                               ,$this->j223_aliq 
                               ,$this->j223_vlrisen 
                               ,'$this->j223_tipoim' 
                               ,'$this->j223_manual' 
                               ,$this->j223_tipocalculo 
                               ,$this->j223_iptucalclog 
                               ,'$this->j223_gerafinanc'
                      )";

     $result = db_query($sql); 
     
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "iptucalcold ($this->j223_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "iptucalcold já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "iptucalcold ($this->j223_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j223_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->j223_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014360,'$this->j223_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010971,1014360,'','".AddSlashes(pg_result($resaco,0,'j223_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014361,'','".AddSlashes(pg_result($resaco,0,'j223_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014362,'','".AddSlashes(pg_result($resaco,0,'j223_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014363,'','".AddSlashes(pg_result($resaco,0,'j223_testad'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014364,'','".AddSlashes(pg_result($resaco,0,'j223_arealo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014365,'','".AddSlashes(pg_result($resaco,0,'j223_areafr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014366,'','".AddSlashes(pg_result($resaco,0,'j223_areaed'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014367,'','".AddSlashes(pg_result($resaco,0,'j223_m2terr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014368,'','".AddSlashes(pg_result($resaco,0,'j223_vlrter'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014369,'','".AddSlashes(pg_result($resaco,0,'j223_aliq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014370,'','".AddSlashes(pg_result($resaco,0,'j223_vlrisen'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014371,'','".AddSlashes(pg_result($resaco,0,'j223_tipoim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014372,'','".AddSlashes(pg_result($resaco,0,'j223_manual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014373,'','".AddSlashes(pg_result($resaco,0,'j223_tipocalculo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010971,1014374,'','".AddSlashes(pg_result($resaco,0,'j223_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($j223_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update iptucalcold set ";
     $virgula = "";
     if(trim($this->j223_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_sequencial"])){ 
       $sql  .= $virgula." j223_sequencial = $this->j223_sequencial ";
       $virgula = ",";
       if(trim($this->j223_sequencial) == null ){ 
         $this->erro_sql = " Campo j223_sequencial não informado.";
         $this->erro_campo = "j223_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_anousu"])){ 
       $sql  .= $virgula." j223_anousu = $this->j223_anousu ";
       $virgula = ",";
       if(trim($this->j223_anousu) == null ){ 
         $this->erro_sql = " Campo Exercício não informado.";
         $this->erro_campo = "j223_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_matric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_matric"])){ 
       $sql  .= $virgula." j223_matric = $this->j223_matric ";
       $virgula = ",";
       if(trim($this->j223_matric) == null ){ 
         $this->erro_sql = " Campo Mátricula não informado.";
         $this->erro_campo = "j223_matric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_testad)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_testad"])){ 
       $sql  .= $virgula." j223_testad = $this->j223_testad ";
       $virgula = ",";
       if(trim($this->j223_testad) == null ){ 
         $this->erro_sql = " Campo Testada do Cálculo não informado.";
         $this->erro_campo = "j223_testad";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_arealo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_arealo"])){ 
       $sql  .= $virgula." j223_arealo = $this->j223_arealo ";
       $virgula = ",";
       if(trim($this->j223_arealo) == null ){ 
         $this->erro_sql = " Campo Área Calculada não informado.";
         $this->erro_campo = "j223_arealo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_areafr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_areafr"])){ 
       $sql  .= $virgula." j223_areafr = $this->j223_areafr ";
       $virgula = ",";
       if(trim($this->j223_areafr) == null ){ 
         $this->erro_sql = " Campo Área Fracionada não informado.";
         $this->erro_campo = "j223_areafr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_areaed)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_areaed"])){ 
       $sql  .= $virgula." j223_areaed = $this->j223_areaed ";
       $virgula = ",";
       if(trim($this->j223_areaed) == null ){ 
         $this->erro_sql = " Campo Área Total Edificada não informado.";
         $this->erro_campo = "j223_areaed";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_m2terr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_m2terr"])){ 
       $sql  .= $virgula." j223_m2terr = $this->j223_m2terr ";
       $virgula = ",";
       if(trim($this->j223_m2terr) == null ){ 
         $this->erro_sql = " Campo Valor M2 Terreno não informado.";
         $this->erro_campo = "j223_m2terr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_vlrter)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_vlrter"])){ 
       $sql  .= $virgula." j223_vlrter = $this->j223_vlrter ";
       $virgula = ",";
       if(trim($this->j223_vlrter) == null ){ 
         $this->erro_sql = " Campo Valor Venal Terreno não informado.";
         $this->erro_campo = "j223_vlrter";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_aliq)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_aliq"])){ 
       $sql  .= $virgula." j223_aliq = $this->j223_aliq ";
       $virgula = ",";
       if(trim($this->j223_aliq) == null ){ 
         $this->erro_sql = " Campo Alíquota do IPTU não informado.";
         $this->erro_campo = "j223_aliq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_vlrisen)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_vlrisen"])){ 
       $sql  .= $virgula." j223_vlrisen = $this->j223_vlrisen ";
       $virgula = ",";
       if(trim($this->j223_vlrisen) == null ){ 
         $this->erro_sql = " Campo Valor da Isenção não informado.";
         $this->erro_campo = "j223_vlrisen";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_tipoim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_tipoim"])){ 
       $sql  .= $virgula." j223_tipoim = '$this->j223_tipoim' ";
       $virgula = ",";
       if(trim($this->j223_tipoim) == null ){ 
         $this->erro_sql = " Campo Tipo de Imposto não informado.";
         $this->erro_campo = "j223_tipoim";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_manual)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_manual"])){ 
       $sql  .= $virgula." j223_manual = '$this->j223_manual' ";
       $virgula = ",";
       if(trim($this->j223_manual) == null ){ 
         $this->erro_sql = " Campo Log do Cálculo não informado.";
         $this->erro_campo = "j223_manual";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_tipocalculo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_tipocalculo"])){ 
       $sql  .= $virgula." j223_tipocalculo = $this->j223_tipocalculo ";
       $virgula = ",";
       if(trim($this->j223_tipocalculo) == null ){ 
         $this->erro_sql = " Campo Tipo de Cálculo não informado.";
         $this->erro_campo = "j223_tipocalculo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j223_iptucalclog)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j223_iptucalclog"])){ 
       $sql  .= $virgula." j223_iptucalclog = $this->j223_iptucalclog ";
       $virgula = ",";
       if(trim($this->j223_iptucalclog) == null ){ 
         $this->erro_sql = " Campo Iptucalclog não informado.";
         $this->erro_campo = "j223_iptucalclog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($j223_sequencial!=null){
       $sql .= " j223_sequencial = $this->j223_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->j223_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014360,'$this->j223_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_sequencial"]) || $this->j223_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014360,'".AddSlashes(pg_result($resaco,$conresaco,'j223_sequencial'))."','$this->j223_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_anousu"]) || $this->j223_anousu != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014361,'".AddSlashes(pg_result($resaco,$conresaco,'j223_anousu'))."','$this->j223_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_matric"]) || $this->j223_matric != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014362,'".AddSlashes(pg_result($resaco,$conresaco,'j223_matric'))."','$this->j223_matric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_testad"]) || $this->j223_testad != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014363,'".AddSlashes(pg_result($resaco,$conresaco,'j223_testad'))."','$this->j223_testad',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_arealo"]) || $this->j223_arealo != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014364,'".AddSlashes(pg_result($resaco,$conresaco,'j223_arealo'))."','$this->j223_arealo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_areafr"]) || $this->j223_areafr != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014365,'".AddSlashes(pg_result($resaco,$conresaco,'j223_areafr'))."','$this->j223_areafr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_areaed"]) || $this->j223_areaed != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014366,'".AddSlashes(pg_result($resaco,$conresaco,'j223_areaed'))."','$this->j223_areaed',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_m2terr"]) || $this->j223_m2terr != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014367,'".AddSlashes(pg_result($resaco,$conresaco,'j223_m2terr'))."','$this->j223_m2terr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_vlrter"]) || $this->j223_vlrter != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014368,'".AddSlashes(pg_result($resaco,$conresaco,'j223_vlrter'))."','$this->j223_vlrter',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_aliq"]) || $this->j223_aliq != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014369,'".AddSlashes(pg_result($resaco,$conresaco,'j223_aliq'))."','$this->j223_aliq',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_vlrisen"]) || $this->j223_vlrisen != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014370,'".AddSlashes(pg_result($resaco,$conresaco,'j223_vlrisen'))."','$this->j223_vlrisen',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_tipoim"]) || $this->j223_tipoim != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014371,'".AddSlashes(pg_result($resaco,$conresaco,'j223_tipoim'))."','$this->j223_tipoim',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_manual"]) || $this->j223_manual != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014372,'".AddSlashes(pg_result($resaco,$conresaco,'j223_manual'))."','$this->j223_manual',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_tipocalculo"]) || $this->j223_tipocalculo != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014373,'".AddSlashes(pg_result($resaco,$conresaco,'j223_tipocalculo'))."','$this->j223_tipocalculo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["j223_iptucalclog"]) || $this->j223_iptucalclog != "")
             $resac = db_query("insert into db_acount values($acount,1010971,1014374,'".AddSlashes(pg_result($resaco,$conresaco,'j223_iptucalclog'))."','$this->j223_iptucalclog',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "iptucalcold não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j223_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "iptucalcold não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j223_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j223_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($j223_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($j223_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014360,'$j223_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014360,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014361,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014362,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014363,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_testad'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014364,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_arealo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014365,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_areafr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014366,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_areaed'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014367,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_m2terr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014368,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_vlrter'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014369,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_aliq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014370,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_vlrisen'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014371,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_tipoim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014372,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_manual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014373,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_tipocalculo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010971,1014374,'','".AddSlashes(pg_result($resaco,$iresaco,'j223_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from iptucalcold
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($j223_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " j223_sequencial = $j223_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "iptucalcold não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j223_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "iptucalcold não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j223_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$j223_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:iptucalcold";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($j223_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from iptucalcold ";
     $sql .= "      inner join iptubase  on  iptubase.j01_matric = iptucalclogmat.j28_matric";
     $sql .= "      inner join iptucalclog  on  iptucalclog.j27_codigo = iptucalclogmat.j28_codigo";
     $sql .= "      inner join iptucalclogmat  on  iptucalclogmat.j28_codigo = iptucalcold.j223_iptucalclog and  iptucalclogmat.j28_matric = iptucalcold.j223_matric";
     $sql .= "      inner join iptucadlogcalc  on  iptucadlogcalc.j62_codigo = iptucalclogmat.j28_tipologcalc";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j223_sequencial)) {
         $sql2 .= " where iptucalcold.j223_sequencial = $j223_sequencial "; 
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

    public function sql_query_file($j223_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from iptucalcold ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j223_sequencial)){
         $sql2 .= " where iptucalcold.j223_sequencial = $j223_sequencial "; 
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

  public function salvarIptucalcOld($iAnousu, $iMatric, $iCalclog) {

    if ( !db_utils::inTransaction()) {
       throw new Exception ("Sem Transação Ativa");
    }

    if (empty($iAnousu)) {
       $this->erro_sql = " Campo Exercício não informado para gerar informações anteriores.";
       $this->erro_campo = "j223_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    if (empty($iMatric)) {
       $this->erro_sql = " Campo Matrícula não informado para gerar informações anteriores.";
       $this->erro_campo = "j223_matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    if (empty($iCalclog)) {
       $this->erro_sql = " Campo CalcLog não informado para gerar informações anteriores.";
       $this->erro_campo = "j223_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    
    // iptucalcold
    $sSqlIptucalc = "select j23_anousu,     
                            j23_matric,     
                            coalesce(j23_testad , 0)     as j23_testad,
                            coalesce(j23_arealo , 0)     as j23_arealo,
                            coalesce(j23_areafr , 0)     as j23_areafr,
                            coalesce(j23_areaed , 0)     as j23_areaed,
                            coalesce(j23_m2terr , 0)     as j23_m2terr,
                            coalesce(j23_vlrter , 0)     as j23_vlrter,
                            coalesce(j23_aliq   , 0)     as j23_aliq,
                            coalesce(j23_vlrisen, 0)     as j23_vlrisen,     
                            coalesce(j23_tipoim, '')     as j23_tipoim,
                            coalesce(j23_manual, '')     as j23_manual,
                            coalesce(j23_tipocalculo, 0) as j23_tipocalculo,
                            j23_gerafinanc 
                       from iptucalc where j23_anousu = {$iAnousu} and j23_matric = {$iMatric};";

    $rsIptucalc = db_query($sSqlIptucalc) or die($sSqlIptucalc);
    if (!$rsIptucalc) {
        throw new DBException("Não foi possivel buscar dados do cálculo (".pg_last_error().")");
    }

    global $j23_anousu     ;
    global $j23_matric     ;
    global $j23_testad     ;
    global $j23_arealo     ;
    global $j23_areafr     ;
    global $j23_areaed     ;
    global $j23_m2terr     ;
    global $j23_vlrter     ;
    global $j23_aliq       ;
    global $j23_vlrisen    ;
    global $j23_tipoim     ;
    global $j23_manual     ;
    global $j23_tipocalculo;
    global $j23_gerafinanc ;

    db_fieldsmemory($rsIptucalc, 0);

    $this->j223_anousu         = $j23_anousu;
    $this->j223_matric         = $j23_matric;
    $this->j223_testad         = $j23_testad;
    $this->j223_arealo         = $j23_arealo;
    $this->j223_areafr         = $j23_areafr;
    $this->j223_areaed         = $j23_areaed;
    $this->j223_m2terr         = $j23_m2terr;
    $this->j223_vlrter         = $j23_vlrter;
    $this->j223_aliq           = $j23_aliq;
    $this->j223_vlrisen        = $j23_vlrisen;
    $this->j223_tipoim         = $j23_tipoim;
    $this->j223_manual         = ($j23_manual)?$j23_manual:' ';
    $this->j223_tipocalculo    = $j23_tipocalculo;
    $this->j223_iptucalclog    = $iCalclog;
    $this->j223_gerafinanc     = $j23_gerafinanc;

    $this->incluir(null);
    if ($this->erro_status == 0) {
       throw new DBException("Não foi possivel salvar dados do cálculo (".pg_last_error().")");
       return false;
    }
    return true;

  }

}
