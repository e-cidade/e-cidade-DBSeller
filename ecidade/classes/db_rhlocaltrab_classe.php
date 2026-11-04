<?php

class cl_rhlocaltrab
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
    public $rh55_instit = 0; 
    public $rh55_codigo = 0; 
    public $rh55_estrut = null; 
    public $rh55_descr = null; 
    public $rh55_tipolocal = 0; 
    public $rh55_endereco = null; 
    public $rh55_tipoestabelecimento = 0; 
    public $rh55_tipoinscricao = 0; 
    public $rh55_numeroinscricao = null; 
    public $rh55_observacaoregistrosambientais = null; 
    public $rh55_inep = 0; 
    public $rh55_lotacaotributaria = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 rh55_instit = int4 = Cod. Instituição 
                 rh55_codigo = int4 = Cód. Local 
                 rh55_estrut = varchar(20) = Estrutural 
                 rh55_descr = varchar(40) = Descrição 
                 rh55_tipolocal = int4 = Tipo de local de Trabalho 
                 rh55_endereco = varchar(80) = Endereço 
                 rh55_tipoestabelecimento = int4 = Tipo de Estabelecimento 
                 rh55_tipoinscricao = int4 = Tipo de inscrição 
                 rh55_numeroinscricao = varchar(14) = Numero de Inscrição 
                 rh55_observacaoregistrosambientais = text = Observações registros ambientais 
                 rh55_inep = int4 = Código do Inep 
                 rh55_lotacaotributaria = varchar(30) = Lotação Tributária 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("rhlocaltrab"); 
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
       $this->rh55_instit = ($this->rh55_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_instit"]:$this->rh55_instit);
       $this->rh55_codigo = ($this->rh55_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_codigo"]:$this->rh55_codigo);
       $this->rh55_estrut = ($this->rh55_estrut == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_estrut"]:$this->rh55_estrut);
       $this->rh55_descr = ($this->rh55_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_descr"]:$this->rh55_descr);
       $this->rh55_tipolocal = ($this->rh55_tipolocal == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_tipolocal"]:$this->rh55_tipolocal);
       $this->rh55_endereco = ($this->rh55_endereco == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_endereco"]:$this->rh55_endereco);
       $this->rh55_tipoestabelecimento = ($this->rh55_tipoestabelecimento == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_tipoestabelecimento"]:$this->rh55_tipoestabelecimento);
       $this->rh55_tipoinscricao = ($this->rh55_tipoinscricao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_tipoinscricao"]:$this->rh55_tipoinscricao);
       $this->rh55_numeroinscricao = ($this->rh55_numeroinscricao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_numeroinscricao"]:$this->rh55_numeroinscricao);
       $this->rh55_observacaoregistrosambientais = ($this->rh55_observacaoregistrosambientais == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_observacaoregistrosambientais"]:$this->rh55_observacaoregistrosambientais);
       $this->rh55_inep = ($this->rh55_inep == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_inep"]:$this->rh55_inep);
       $this->rh55_lotacaotributaria = ($this->rh55_lotacaotributaria == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_lotacaotributaria"]:$this->rh55_lotacaotributaria);
     }else{
       $this->rh55_instit = ($this->rh55_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_instit"]:$this->rh55_instit);
       $this->rh55_codigo = ($this->rh55_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh55_codigo"]:$this->rh55_codigo);
     }
   }

    public function incluir($rh55_codigo,$rh55_instit)
    {
      $this->atualizacampos();
     if($this->rh55_inep == null ){ 
       $this->erro_sql = " Campo Código do Inep não informado.";
       $this->erro_campo = "rh55_inep";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh55_tipolocal == null ){ 
       $this->erro_sql = " Campo Tipo de local de Trabalho não informado.";
       $this->erro_campo = "rh55_tipolocal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh55_endereco == null ){ 
       $this->erro_sql = " Campo Endereço não informado.";
       $this->erro_campo = "rh55_endereco";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh55_tipoestabelecimento == null ){ 
       $this->erro_sql = " Campo Tipo de Estabelecimento não informado.";
       $this->erro_campo = "rh55_tipoestabelecimento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh55_tipoinscricao == null ){ 
       $this->erro_sql = " Campo Tipo de inscrição não informado.";
       $this->erro_campo = "rh55_tipoinscricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh55_numeroinscricao == null ){ 
       $this->erro_sql = " Campo Numero de Inscrição não informado.";
       $this->erro_campo = "rh55_numeroinscricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($rh55_codigo == "" || $rh55_codigo == null ){
       $result = db_query("select nextval('rhlocaltrab_rh55_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: rhlocaltrab_rh55_codigo_seq do campo: rh55_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->rh55_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from rhlocaltrab_rh55_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $rh55_codigo)){
         $this->erro_sql = " Campo rh55_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->rh55_codigo = $rh55_codigo; 
       }
     }
     if(($this->rh55_codigo == null) || ($this->rh55_codigo == "") ){ 
       $this->erro_sql = " Campo rh55_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->rh55_instit == null) || ($this->rh55_instit == "") ){ 
       $this->erro_sql = " Campo rh55_instit não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into rhlocaltrab(
                                       rh55_instit 
                                      ,rh55_codigo 
                                      ,rh55_estrut 
                                      ,rh55_descr 
                                      ,rh55_tipolocal 
                                      ,rh55_endereco 
                                      ,rh55_tipoestabelecimento 
                                      ,rh55_tipoinscricao 
                                      ,rh55_numeroinscricao 
                                      ,rh55_observacaoregistrosambientais 
                                      ,rh55_inep 
                                      ,rh55_lotacaotributaria 
                       )
                values (
                                $this->rh55_instit 
                               ,$this->rh55_codigo 
                               ,'$this->rh55_estrut' 
                               ,'$this->rh55_descr' 
                               ,$this->rh55_tipolocal 
                               ,'$this->rh55_endereco' 
                               ,$this->rh55_tipoestabelecimento 
                               ,$this->rh55_tipoinscricao 
                               ,'$this->rh55_numeroinscricao' 
                               ,'$this->rh55_observacaoregistrosambientais' 
                               ,$this->rh55_inep 
                               ,'$this->rh55_lotacaotributaria' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = " ($this->rh55_codigo."-".$this->rh55_instit) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = " já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = " ($this->rh55_codigo."-".$this->rh55_instit) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
     $this->erro_sql .= "Valores : ".$this->rh55_codigo."-".$this->rh55_instit;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh55_codigo,$this->rh55_instit  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,9014,'$this->rh55_codigo','I')");
         $resac = db_query("insert into db_acountkey values($acount,9908,'$this->rh55_instit','I')");
         $resac = db_query("insert into db_acount values($acount,1542,9908,'','".AddSlashes(pg_result($resaco,0,'rh55_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,9014,'','".AddSlashes(pg_result($resaco,0,'rh55_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,9015,'','".AddSlashes(pg_result($resaco,0,'rh55_estrut'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,9016,'','".AddSlashes(pg_result($resaco,0,'rh55_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,1013657,'','".AddSlashes(pg_result($resaco,0,'rh55_tipolocal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,1013658,'','".AddSlashes(pg_result($resaco,0,'rh55_endereco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,1013659,'','".AddSlashes(pg_result($resaco,0,'rh55_tipoestabelecimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,1013660,'','".AddSlashes(pg_result($resaco,0,'rh55_tipoinscricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,1013661,'','".AddSlashes(pg_result($resaco,0,'rh55_numeroinscricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,1013662,'','".AddSlashes(pg_result($resaco,0,'rh55_observacaoregistrosambientais'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,128162071,'','".AddSlashes(pg_result($resaco,0,'rh55_inep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1542,1014450,'','".AddSlashes(pg_result($resaco,0,'rh55_lotacaotributaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($rh55_codigo=null,$rh55_instit=null)
    {
      $this->atualizacampos();
     $sql = " update rhlocaltrab set ";
     $virgula = "";
     if(trim($this->rh55_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_instit"])){ 
       $sql  .= $virgula." rh55_instit = $this->rh55_instit ";
       $virgula = ",";
       if(trim($this->rh55_instit) == null ){ 
         $this->erro_sql = " Campo Cod. Instituição não informado.";
         $this->erro_campo = "rh55_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_codigo"])){ 
       $sql  .= $virgula." rh55_codigo = $this->rh55_codigo ";
       $virgula = ",";
       if(trim($this->rh55_codigo) == null ){ 
         $this->erro_sql = " Campo Cód. Local não informado.";
         $this->erro_campo = "rh55_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_estrut)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_estrut"])){ 
       $sql  .= $virgula." rh55_estrut = '$this->rh55_estrut' ";
       $virgula = ",";
     }
     if(trim($this->rh55_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_descr"])){ 
       $sql  .= $virgula." rh55_descr = '$this->rh55_descr' ";
       $virgula = ",";
     }
     if(trim($this->rh55_inep)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_inep"])){ 
       $sql  .= $virgula." rh55_inep = $this->rh55_inep ";
       $virgula = ",";
       if(trim($this->rh55_inep) == null ){ 
         $this->erro_sql = " Campo Código do Inep não informado.";
         $this->erro_campo = "rh55_inep";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_tipolocal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_tipolocal"])){ 
       $sql  .= $virgula." rh55_tipolocal = $this->rh55_tipolocal ";
       $virgula = ",";
       if(trim($this->rh55_tipolocal) == null ){ 
         $this->erro_sql = " Campo Tipo de local de Trabalho não informado.";
         $this->erro_campo = "rh55_tipolocal";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_endereco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_endereco"])){ 
       $sql  .= $virgula." rh55_endereco = '$this->rh55_endereco' ";
       $virgula = ",";
       if(trim($this->rh55_endereco) == null ){ 
         $this->erro_sql = " Campo Endereço não informado.";
         $this->erro_campo = "rh55_endereco";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_tipoestabelecimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_tipoestabelecimento"])){ 
       $sql  .= $virgula." rh55_tipoestabelecimento = $this->rh55_tipoestabelecimento ";
       $virgula = ",";
       if(trim($this->rh55_tipoestabelecimento) == null ){ 
         $this->erro_sql = " Campo Tipo de Estabelecimento não informado.";
         $this->erro_campo = "rh55_tipoestabelecimento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_tipoinscricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_tipoinscricao"])){ 
       $sql  .= $virgula." rh55_tipoinscricao = $this->rh55_tipoinscricao ";
       $virgula = ",";
       if(trim($this->rh55_tipoinscricao) == null ){ 
         $this->erro_sql = " Campo Tipo de inscrição não informado.";
         $this->erro_campo = "rh55_tipoinscricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_numeroinscricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_numeroinscricao"])){ 
       $sql  .= $virgula." rh55_numeroinscricao = '$this->rh55_numeroinscricao' ";
       $virgula = ",";
       if(trim($this->rh55_numeroinscricao) == null ){ 
         $this->erro_sql = " Campo Numero de Inscrição não informado.";
         $this->erro_campo = "rh55_numeroinscricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh55_observacaoregistrosambientais)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_observacaoregistrosambientais"])){ 
       $sql  .= $virgula." rh55_observacaoregistrosambientais = '$this->rh55_observacaoregistrosambientais' ";
       $virgula = ",";
     }
     if(trim($this->rh55_lotacaotributaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh55_lotacaotributaria"])){ 
      $sql  .= $virgula." rh55_lotacaotributaria = '$this->rh55_lotacaotributaria' ";
      $virgula = ",";
     }
     $sql .= " where ";
     if($rh55_codigo!=null){
       $sql .= " rh55_codigo = $this->rh55_codigo";
     }
     if($rh55_instit!=null){
       $sql .= " and  rh55_instit = $this->rh55_instit";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh55_codigo,$this->rh55_instit));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,9014,'$this->rh55_codigo','A')");
           $resac = db_query("insert into db_acountkey values($acount,9908,'$this->rh55_instit','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_instit"]) || $this->rh55_instit != "")
             $resac = db_query("insert into db_acount values($acount,1542,9908,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_instit'))."','$this->rh55_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_codigo"]) || $this->rh55_codigo != "")
             $resac = db_query("insert into db_acount values($acount,1542,9014,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_codigo'))."','$this->rh55_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_estrut"]) || $this->rh55_estrut != "")
             $resac = db_query("insert into db_acount values($acount,1542,9015,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_estrut'))."','$this->rh55_estrut',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_descr"]) || $this->rh55_descr != "")
             $resac = db_query("insert into db_acount values($acount,1542,9016,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_descr'))."','$this->rh55_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_tipolocal"]) || $this->rh55_tipolocal != "")
             $resac = db_query("insert into db_acount values($acount,1542,1013657,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_tipolocal'))."','$this->rh55_tipolocal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_endereco"]) || $this->rh55_endereco != "")
             $resac = db_query("insert into db_acount values($acount,1542,1013658,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_endereco'))."','$this->rh55_endereco',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_tipoestabelecimento"]) || $this->rh55_tipoestabelecimento != "")
             $resac = db_query("insert into db_acount values($acount,1542,1013659,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_tipoestabelecimento'))."','$this->rh55_tipoestabelecimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_tipoinscricao"]) || $this->rh55_tipoinscricao != "")
             $resac = db_query("insert into db_acount values($acount,1542,1013660,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_tipoinscricao'))."','$this->rh55_tipoinscricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_numeroinscricao"]) || $this->rh55_numeroinscricao != "")
             $resac = db_query("insert into db_acount values($acount,1542,1013661,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_numeroinscricao'))."','$this->rh55_numeroinscricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_observacaoregistrosambientais"]) || $this->rh55_observacaoregistrosambientais != "")
             $resac = db_query("insert into db_acount values($acount,1542,1013662,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_observacaoregistrosambientais'))."','$this->rh55_observacaoregistrosambientais',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_inep"]) || $this->rh55_inep != "")
             $resac = db_query("insert into db_acount values($acount,1542,128162071,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_inep'))."','$this->rh55_inep',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh55_lotacaotributaria"]) || $this->rh55_lotacaotributaria != "")
             $resac = db_query("insert into db_acount values($acount,1542,1014450,'".AddSlashes(pg_result($resaco,$conresaco,'rh55_lotacaotributaria'))."','$this->rh55_lotacaotributaria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh55_codigo."-".$this->rh55_instit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh55_codigo."-".$this->rh55_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh55_codigo."-".$this->rh55_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($rh55_codigo=null,$rh55_instit=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($rh55_codigo,$rh55_instit));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,9014,'$rh55_codigo','E')");
           $resac  = db_query("insert into db_acountkey values($acount,9908,'$rh55_instit','E')");
           $resac  = db_query("insert into db_acount values($acount,1542,9908,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,9014,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,9015,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_estrut'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,9016,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,1013657,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_tipolocal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,1013658,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_endereco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,1013659,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_tipoestabelecimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,1013660,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_tipoinscricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,1013661,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_numeroinscricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,1013662,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_observacaoregistrosambientais'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,128162071,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_inep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1542,1014450,'','".AddSlashes(pg_result($resaco,$iresaco,'rh55_lotacaotributaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from rhlocaltrab
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh55_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh55_codigo = $rh55_codigo ";
        }
        if (!empty($rh55_instit)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh55_instit = $rh55_instit ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh55_codigo."-".$rh55_instit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh55_codigo."-".$rh55_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh55_codigo."-".$rh55_instit;
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
        $this->erro_sql   = "Record Vazio na Tabela:rhlocaltrab";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($rh55_codigo = null,$rh55_instit = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from rhlocaltrab ";
     $sql .= "      inner join db_config  on  db_config.codigo = rhlocaltrab.rh55_instit";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh55_codigo)) {
         $sql2 .= " where rhlocaltrab.rh55_codigo = $rh55_codigo "; 
       } 
       if (!empty($rh55_instit)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " rhlocaltrab.rh55_instit = $rh55_instit "; 
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

    public function sql_query_file($rh55_codigo = null,$rh55_instit = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from rhlocaltrab ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh55_codigo)){
         $sql2 .= " where rhlocaltrab.rh55_codigo = $rh55_codigo "; 
       } 
       if (!empty($rh55_instit)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " rhlocaltrab.rh55_instit = $rh55_instit "; 
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

  
  function sql_query_centro_custo( $rh55_codigo=null,$rh55_instit=null,$campos="*",$ordem=null,$dbwhere=""){ 
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = split("#",$campos);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }else{
      $sql .= $campos;
    }
    $sql .= " from rhlocaltrab ";
    $sql .= "      inner join db_config   on  db_config.codigo = rhlocaltrab.rh55_instit";
    $sql .= "      left  join rhlocaltrabcustoplano on  rh55_instit  = rh86_instit and rh55_codigo = rh86_rhlocaltrab";
    $sql .= "      left  join custocriteriorateio on  rh86_criteriorateio = cc08_sequencial";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
    $sql2 = "";
    if($dbwhere==""){
      if($rh55_codigo!=null ){
        $sql2 .= " where rhlocaltrab.rh55_codigo = $rh55_codigo "; 
      } 
      if($rh55_instit!=null ){
        if($sql2!=""){
           $sql2 .= " and ";
        }else{
           $sql2 .= " where ";
        } 
        $sql2 .= " rhlocaltrab.rh55_instit = $rh55_instit "; 
      } 
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if($ordem != null ){
      $sql .= " order by ";
      $campos_sql = split("#",$ordem);
      $virgula = "";
      for($i=0;$i<sizeof($campos_sql);$i++){
        $sql .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }

  /**
  * @param string $campos
  * @param null $where
  * @return string
  */
  public function sql_query_servidor($campos = "*", $where = null, $order = null)
  {
      $sql  = " select {$campos} ";
      $sql .= " from rhlocaltrab ";
      $sql .= " inner join rhpeslocaltrab on rh56_localtrab = rh55_codigo ";
      $sql .= " inner join rhpessoalmov on rh02_seqpes = rh56_seqpes ";
      if (!empty($where)) {
          $sql .= " where {$where} ";
      }
      if (!empty($order)) {
        $sql .= " order by {$order} ";
      }
      return $sql;
  }

}
