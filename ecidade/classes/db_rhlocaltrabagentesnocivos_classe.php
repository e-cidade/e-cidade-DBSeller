<?php

class cl_rhlocaltrabagentesnocivos
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
    public $rh256_sequencial = 0; 
    public $rh256_rhlocaltrab = 0; 
    public $rh256_instituicao = 0; 
    public $rh256_agentenocivo = null; 
    public $rh256_tipoavaliacao = 0; 
    public $rh256_intensidadeconcentracao = null; 
    public $rh256_tolerancialimite = null; 
    public $rh256_medida = null; 
    public $rh256_tecnicamedicao = null; 
    public $rh256_processo = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 rh256_sequencial = int4 = Sequencial 
                 rh256_rhlocaltrab = int4 = Local de trabalho 
                 rh256_instituicao = int4 = Instituiçao 
                 rh256_agentenocivo = varchar(10) = Agente Nocivo 
                 rh256_tipoavaliacao = int4 = Tipo de Avaliação do Agente Nocivo 
                 rh256_intensidadeconcentracao = varchar(10) = Intensidade, concentracao ou dose 
                 rh256_tolerancialimite = varchar(10) = Limite de tolerância 
                 rh256_medida = varchar(3) = Medida 
                 rh256_tecnicamedicao = varchar(40) = Técnica utilizada 
                 rh256_processo = varchar(21) = Processo 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("rhlocaltrabagentesnocivos"); 
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
       $this->rh256_sequencial = ($this->rh256_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_sequencial"]:$this->rh256_sequencial);
       $this->rh256_rhlocaltrab = ($this->rh256_rhlocaltrab == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_rhlocaltrab"]:$this->rh256_rhlocaltrab);
       $this->rh256_instituicao = ($this->rh256_instituicao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_instituicao"]:$this->rh256_instituicao);
       $this->rh256_agentenocivo = ($this->rh256_agentenocivo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_agentenocivo"]:$this->rh256_agentenocivo);
       $this->rh256_tipoavaliacao = ($this->rh256_tipoavaliacao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_tipoavaliacao"]:$this->rh256_tipoavaliacao);
       $this->rh256_intensidadeconcentracao = ($this->rh256_intensidadeconcentracao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_intensidadeconcentracao"]:$this->rh256_intensidadeconcentracao);
       $this->rh256_tolerancialimite = ($this->rh256_tolerancialimite == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_tolerancialimite"]:$this->rh256_tolerancialimite);
       $this->rh256_medida = ($this->rh256_medida == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_medida"]:$this->rh256_medida);
       $this->rh256_tecnicamedicao = ($this->rh256_tecnicamedicao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_tecnicamedicao"]:$this->rh256_tecnicamedicao);
       $this->rh256_processo = ($this->rh256_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_processo"]:$this->rh256_processo);
     }else{
       $this->rh256_sequencial = ($this->rh256_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh256_sequencial"]:$this->rh256_sequencial);
     }
   }

    public function incluir($rh256_sequencial)
    {
      $this->atualizacampos();
     if($this->rh256_rhlocaltrab == null ){ 
       $this->erro_sql = " Campo Local de trabalho não informado.";
       $this->erro_campo = "rh256_rhlocaltrab";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh256_instituicao == null ){ 
       $this->erro_sql = " Campo Instituiçao não informado.";
       $this->erro_campo = "rh256_instituicao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh256_tipoavaliacao == null ){ 
       $this->rh256_tipoavaliacao = "0";
     }
     if($rh256_sequencial == "" || $rh256_sequencial == null ){
       $result = db_query("select nextval('rhlocaltrabagentesnocivos_rh256_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: rhlocaltrabagentesnocivos_rh256_sequencial_seq do campo: rh256_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->rh256_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from rhlocaltrabagentesnocivos_rh256_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $rh256_sequencial)){
         $this->erro_sql = " Campo rh256_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->rh256_sequencial = $rh256_sequencial; 
       }
     }
     if(($this->rh256_sequencial == null) || ($this->rh256_sequencial == "") ){ 
       $this->erro_sql = " Campo rh256_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into rhlocaltrabagentesnocivos(
                                       rh256_sequencial 
                                      ,rh256_rhlocaltrab 
                                      ,rh256_instituicao 
                                      ,rh256_agentenocivo 
                                      ,rh256_tipoavaliacao 
                                      ,rh256_intensidadeconcentracao 
                                      ,rh256_tolerancialimite 
                                      ,rh256_medida 
                                      ,rh256_tecnicamedicao 
                                      ,rh256_processo 
                       )
                values (
                                $this->rh256_sequencial 
                               ,$this->rh256_rhlocaltrab 
                               ,$this->rh256_instituicao 
                               ,'$this->rh256_agentenocivo' 
                               ,$this->rh256_tipoavaliacao 
                               ,'$this->rh256_intensidadeconcentracao' 
                               ,'$this->rh256_tolerancialimite' 
                               ,'$this->rh256_medida' 
                               ,'$this->rh256_tecnicamedicao' 
                               ,'$this->rh256_processo' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Agentes nocivos do local de trabalho ($this->rh256_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Agentes nocivos do local de trabalho já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Agentes nocivos do local de trabalho ($this->rh256_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh256_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh256_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1013663,'$this->rh256_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010858,1013663,'','".AddSlashes(pg_result($resaco,0,'rh256_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013664,'','".AddSlashes(pg_result($resaco,0,'rh256_rhlocaltrab'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013665,'','".AddSlashes(pg_result($resaco,0,'rh256_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013666,'','".AddSlashes(pg_result($resaco,0,'rh256_agentenocivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013672,'','".AddSlashes(pg_result($resaco,0,'rh256_tipoavaliacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013673,'','".AddSlashes(pg_result($resaco,0,'rh256_intensidadeconcentracao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013674,'','".AddSlashes(pg_result($resaco,0,'rh256_tolerancialimite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013675,'','".AddSlashes(pg_result($resaco,0,'rh256_medida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1013676,'','".AddSlashes(pg_result($resaco,0,'rh256_tecnicamedicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010858,1015580,'','".AddSlashes(pg_result($resaco,0,'rh256_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($rh256_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update rhlocaltrabagentesnocivos set ";
     $virgula = "";
     if(trim($this->rh256_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_sequencial"])){ 
       $sql  .= $virgula." rh256_sequencial = $this->rh256_sequencial ";
       $virgula = ",";
       if(trim($this->rh256_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "rh256_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh256_rhlocaltrab)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_rhlocaltrab"])){ 
       $sql  .= $virgula." rh256_rhlocaltrab = $this->rh256_rhlocaltrab ";
       $virgula = ",";
       if(trim($this->rh256_rhlocaltrab) == null ){ 
         $this->erro_sql = " Campo Local de trabalho não informado.";
         $this->erro_campo = "rh256_rhlocaltrab";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh256_instituicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_instituicao"])){ 
       $sql  .= $virgula." rh256_instituicao = $this->rh256_instituicao ";
       $virgula = ",";
       if(trim($this->rh256_instituicao) == null ){ 
         $this->erro_sql = " Campo Instituiçao não informado.";
         $this->erro_campo = "rh256_instituicao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh256_agentenocivo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_agentenocivo"])){ 
       $sql  .= $virgula." rh256_agentenocivo = '$this->rh256_agentenocivo' ";
       $virgula = ",";
     }
     if(trim($this->rh256_tipoavaliacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_tipoavaliacao"])){ 
        if(trim($this->rh256_tipoavaliacao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh256_tipoavaliacao"])){ 
           $this->rh256_tipoavaliacao = "0" ; 
        } 
       $sql  .= $virgula." rh256_tipoavaliacao = $this->rh256_tipoavaliacao ";
       $virgula = ",";
     }
     if(trim($this->rh256_intensidadeconcentracao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_intensidadeconcentracao"])){ 
       $sql  .= $virgula." rh256_intensidadeconcentracao = '$this->rh256_intensidadeconcentracao' ";
       $virgula = ",";
     }
     if(trim($this->rh256_tolerancialimite)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_tolerancialimite"])){ 
       $sql  .= $virgula." rh256_tolerancialimite = '$this->rh256_tolerancialimite' ";
       $virgula = ",";
     }
     if(trim($this->rh256_medida)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_medida"])){ 
       $sql  .= $virgula." rh256_medida = '$this->rh256_medida' ";
       $virgula = ",";
     }
     if(trim($this->rh256_tecnicamedicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_tecnicamedicao"])){ 
       $sql  .= $virgula." rh256_tecnicamedicao = '$this->rh256_tecnicamedicao' ";
       $virgula = ",";
     }
     if(trim($this->rh256_processo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh256_processo"])){ 
       $sql  .= $virgula." rh256_processo = '$this->rh256_processo' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($rh256_sequencial!=null){
       $sql .= " rh256_sequencial = $this->rh256_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh256_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1013663,'$this->rh256_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_sequencial"]) || $this->rh256_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013663,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_sequencial'))."','$this->rh256_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_rhlocaltrab"]) || $this->rh256_rhlocaltrab != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013664,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_rhlocaltrab'))."','$this->rh256_rhlocaltrab',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_instituicao"]) || $this->rh256_instituicao != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013665,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_instituicao'))."','$this->rh256_instituicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_agentenocivo"]) || $this->rh256_agentenocivo != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013666,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_agentenocivo'))."','$this->rh256_agentenocivo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_tipoavaliacao"]) || $this->rh256_tipoavaliacao != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013672,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_tipoavaliacao'))."','$this->rh256_tipoavaliacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_intensidadeconcentracao"]) || $this->rh256_intensidadeconcentracao != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013673,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_intensidadeconcentracao'))."','$this->rh256_intensidadeconcentracao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_tolerancialimite"]) || $this->rh256_tolerancialimite != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013674,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_tolerancialimite'))."','$this->rh256_tolerancialimite',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_medida"]) || $this->rh256_medida != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013675,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_medida'))."','$this->rh256_medida',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_tecnicamedicao"]) || $this->rh256_tecnicamedicao != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1013676,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_tecnicamedicao'))."','$this->rh256_tecnicamedicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh256_processo"]) || $this->rh256_processo != "")
             $resac = db_query("insert into db_acount values($acount,1010858,1015580,'".AddSlashes(pg_result($resaco,$conresaco,'rh256_processo'))."','$this->rh256_processo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agentes nocivos do local de trabalho não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh256_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Agentes nocivos do local de trabalho não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh256_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh256_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($rh256_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($rh256_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1013663,'$rh256_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013663,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013664,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_rhlocaltrab'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013665,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013666,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_agentenocivo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013672,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_tipoavaliacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013673,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_intensidadeconcentracao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013674,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_tolerancialimite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013675,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_medida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1013676,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_tecnicamedicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010858,1015580,'','".AddSlashes(pg_result($resaco,$iresaco,'rh256_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from rhlocaltrabagentesnocivos
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh256_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh256_sequencial = $rh256_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agentes nocivos do local de trabalho não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh256_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Agentes nocivos do local de trabalho não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh256_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh256_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:rhlocaltrabagentesnocivos";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($rh256_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from rhlocaltrabagentesnocivos ";
     $sql .= "      inner join rhlocaltrab  on  rhlocaltrab.rh55_codigo = rhlocaltrabagentesnocivos.rh256_rhlocaltrab and  rhlocaltrab.rh55_instit = rhlocaltrabagentesnocivos.rh256_instituicao";
     $sql .= "      inner join db_config  on  db_config.codigo = rhlocaltrab.rh55_instit";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh256_sequencial)) {
         $sql2 .= " where rhlocaltrabagentesnocivos.rh256_sequencial = $rh256_sequencial "; 
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

    public function sql_query_file($rh256_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from rhlocaltrabagentesnocivos ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh256_sequencial)){
         $sql2 .= " where rhlocaltrabagentesnocivos.rh256_sequencial = $rh256_sequencial "; 
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
