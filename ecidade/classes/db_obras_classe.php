<?php

class cl_obras
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
    public $ob01_arquitetoobra = null;
    public $ob01_codobra = 0;
    public $ob01_responsavelprojeto = 0;
    public $ob01_nomeobra = null;
    public $ob01_tiporesp = 0;
    public $ob01_regular = 'f';
    public $ob01_dtobra_dia = null;
    public $ob01_dtobra_mes = null;
    public $ob01_dtobra_ano = null;
    public $ob01_dtobra = null;
    public $ob01_processo = null;
    public $ob01_nometitularproc = null;
    public $ob01_dtprocesso_dia = null;
    public $ob01_dtprocesso_mes = null;
    public $ob01_dtprocesso_ano = null;
    public $ob01_dtprocesso = null;
    public $ob01_obs = null;
    public $ob01_numeroartprojeto = 0;
    public $ob01_numerorrtprojeto = 0;
    public $ob01_numeroarttecnico = 0;
    public $ob01_numerorrttecnico = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ob01_arquitetoobra = int4 = Arquiteto da Obra
                 ob01_codobra = int4 = Código da Obra
                 ob01_responsavelprojeto = int4 = Responsável pelo Projeto
                 ob01_nomeobra = varchar(55) = Nome da Obra
                 ob01_tiporesp = int4 = Tipo de Responsável
                 ob01_regular = bool = Obra Regular
                 ob01_dtobra = date = Data Obra
                 ob01_processo = varchar(40) = Código Processo
                 ob01_nometitularproc = varchar(40) = Nome Titular
                 ob01_dtprocesso = date = Data Processo
                 ob01_obs = text = Observações
                 ob01_numeroartprojeto = int8 = ART Responsável Projeto
                 ob01_numerorrtprojeto = int8 = RRT Responsável Projeto
                 ob01_numeroarttecnico = int8 = ART Responsável Técnico
                 ob01_numerorrttecnico = int8 = RRT Responsável Técnico
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("obras");
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
       $this->ob01_arquitetoobra = ($this->ob01_arquitetoobra == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_arquitetoobra"]:$this->ob01_arquitetoobra);
       $this->ob01_codobra = ($this->ob01_codobra == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_codobra"]:$this->ob01_codobra);
       $this->ob01_responsavelprojeto = ($this->ob01_responsavelprojeto == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_responsavelprojeto"]:$this->ob01_responsavelprojeto);
       $this->ob01_nomeobra = ($this->ob01_nomeobra == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_nomeobra"]:$this->ob01_nomeobra);
       $this->ob01_tiporesp = ($this->ob01_tiporesp == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_tiporesp"]:$this->ob01_tiporesp);
       $this->ob01_regular = ($this->ob01_regular == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_regular"]:$this->ob01_regular);
       if($this->ob01_dtobra == ""){
         $this->ob01_dtobra_dia = ($this->ob01_dtobra_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_dtobra_dia"]:$this->ob01_dtobra_dia);
         $this->ob01_dtobra_mes = ($this->ob01_dtobra_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_dtobra_mes"]:$this->ob01_dtobra_mes);
         $this->ob01_dtobra_ano = ($this->ob01_dtobra_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_dtobra_ano"]:$this->ob01_dtobra_ano);
         if($this->ob01_dtobra_dia != ""){
            $this->ob01_dtobra = $this->ob01_dtobra_ano."-".$this->ob01_dtobra_mes."-".$this->ob01_dtobra_dia;
         }
       }
       $this->ob01_processo = ($this->ob01_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_processo"]:$this->ob01_processo);
       $this->ob01_nometitularproc = ($this->ob01_nometitularproc == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_nometitularproc"]:$this->ob01_nometitularproc);
       if($this->ob01_dtprocesso == ""){
         $this->ob01_dtprocesso_dia = ($this->ob01_dtprocesso_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_dtprocesso_dia"]:$this->ob01_dtprocesso_dia);
         $this->ob01_dtprocesso_mes = ($this->ob01_dtprocesso_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_dtprocesso_mes"]:$this->ob01_dtprocesso_mes);
         $this->ob01_dtprocesso_ano = ($this->ob01_dtprocesso_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_dtprocesso_ano"]:$this->ob01_dtprocesso_ano);
         if($this->ob01_dtprocesso_dia != ""){
            $this->ob01_dtprocesso = $this->ob01_dtprocesso_ano."-".$this->ob01_dtprocesso_mes."-".$this->ob01_dtprocesso_dia;
         }
       }
       $this->ob01_obs = ($this->ob01_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_obs"]:$this->ob01_obs);
       $this->ob01_numeroartprojeto = ($this->ob01_numeroartprojeto == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_numeroartprojeto"]:$this->ob01_numeroartprojeto);
       $this->ob01_numerorrtprojeto = ($this->ob01_numerorrtprojeto == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_numerorrtprojeto"]:$this->ob01_numerorrtprojeto);
       $this->ob01_numeroarttecnico = ($this->ob01_numeroarttecnico == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_numeroarttecnico"]:$this->ob01_numeroarttecnico);
       $this->ob01_numerorrttecnico = ($this->ob01_numerorrttecnico == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_numerorrttecnico"]:$this->ob01_numerorrttecnico);
     }else{
       $this->ob01_codobra = ($this->ob01_codobra == ""?@$GLOBALS["HTTP_POST_VARS"]["ob01_codobra"]:$this->ob01_codobra);
     }
   }

    public function incluir($ob01_codobra)
    { 
      $this->atualizacampos();
    //  if($this->ob01_arquitetoobra == null ){
    //    $this->erro_sql = " Campo Arquiteto da Obra não informado.";
    //    $this->erro_campo = "ob01_arquitetoobra";
    //    $this->erro_banco = "";
    //    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
    //    $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
    //    $this->erro_status = "0";
    //    return false;
    //  }
    //  if($this->ob01_responsavelprojeto == null ){
    //    $this->erro_sql = " Campo Responsável pelo Projeto não informado.";
    //    $this->erro_campo = "ob01_responsavelprojeto";
    //    $this->erro_banco = "";
    //    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
    //    $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
    //    $this->erro_status = "0";
    //    return false;
    //  }
     if($this->ob01_arquitetoobra == null ){
       $this->ob01_arquitetoobra = "null";
     }if($this->ob01_responsavelprojeto == null ){
       $this->ob01_responsavelprojeto = "null";
     }
     if($this->ob01_nomeobra == null ){
       $this->erro_sql = " Campo Nome da Obra não informado.";
       $this->erro_campo = "ob01_nomeobra";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ob01_tiporesp == null ){
       $this->erro_sql = " Campo Tipo de Responsável não informado.";
       $this->erro_campo = "ob01_tiporesp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->ob01_regular == null ){
       $this->erro_sql = " Campo Obra Regular não informado.";
       $this->erro_campo = "ob01_regular";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ob01_dtobra == null ){
       $this->erro_sql = " Campo Data Obra não informado.";
       $this->erro_campo = "ob01_dtobra_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ob01_processo == null ){
       $this->ob01_processo = "null";
     }
     if($this->ob01_dtprocesso == null ){
       $this->ob01_dtprocesso = "null";
     }
     if($this->ob01_numeroartprojeto == null ){
       $this->ob01_numeroartprojeto = "0";
     }
     if($this->ob01_numerorrtprojeto == null ){
       $this->ob01_numerorrtprojeto = "0";
     }
     if($this->ob01_numeroarttecnico == null ){
      $this->ob01_numeroarttecnico = "0";
    }
    if($this->ob01_numerorrttecnico == null ){
      $this->ob01_numerorrttecnico = "0";
    }
     if($ob01_codobra == "" || $ob01_codobra == null ){
       $result = db_query("select nextval('obras_ob01_codobra_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: obras_ob01_codobra_seq do campo: ob01_codobra";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ob01_codobra = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from obras_ob01_codobra_seq");
       if(($result != false) && (pg_result($result,0,0) < $ob01_codobra)){
         $this->erro_sql = " Campo ob01_codobra maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ob01_codobra = $ob01_codobra;
       }
     }
     if(($this->ob01_codobra == null) || ($this->ob01_codobra == "") ){
       $this->erro_sql = " Campo ob01_codobra não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into obras(
                                       ob01_arquitetoobra
                                      ,ob01_codobra
                                      ,ob01_responsavelprojeto
                                      ,ob01_nomeobra
                                      ,ob01_tiporesp
                                      ,ob01_regular
                                      ,ob01_dtobra
                                      ,ob01_processo
                                      ,ob01_nometitularproc
                                      ,ob01_dtprocesso
                                      ,ob01_obs
                                      ,ob01_numeroartprojeto
                                      ,ob01_numerorrtprojeto
                                      ,ob01_numeroarttecnico
                                      ,ob01_numerorrttecnico
                       )
                values (
                                $this->ob01_arquitetoobra
                               ,$this->ob01_codobra
                               ,$this->ob01_responsavelprojeto
                               ,'$this->ob01_nomeobra'
                               ,$this->ob01_tiporesp
                               ,'$this->ob01_regular'
                               ,".($this->ob01_dtobra == "null" || $this->ob01_dtobra == ""?"null":"'".$this->ob01_dtobra."'")."
                               ,$this->ob01_processo
                               ,'$this->ob01_nometitularproc'
                               ,".($this->ob01_dtprocesso == "null" || $this->ob01_dtprocesso == ""?"null":"'".$this->ob01_dtprocesso."'")."
                               ,'$this->ob01_obs'
                               ,'$this->ob01_numeroartprojeto'
                               ,'$this->ob01_numerorrtprojeto'
                               ,'$this->ob01_numeroarttecnico'
                               ,'$this->ob01_numerorrttecnico'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "cadastro de obras ($this->ob01_codobra) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "cadastro de obras já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "cadastro de obras ($this->ob01_codobra) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ob01_codobra;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ob01_codobra  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,5909,'$this->ob01_codobra','I')");
         $resac = db_query("insert into db_acount values($acount,946,1010571,'','".AddSlashes(pg_result($resaco,0,'ob01_arquitetoobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,5909,'','".AddSlashes(pg_result($resaco,0,'ob01_codobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,1010570,'','".AddSlashes(pg_result($resaco,0,'ob01_responsavelprojeto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,5910,'','".AddSlashes(pg_result($resaco,0,'ob01_nomeobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,5913,'','".AddSlashes(pg_result($resaco,0,'ob01_tiporesp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,5914,'','".AddSlashes(pg_result($resaco,0,'ob01_regular'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,18629,'','".AddSlashes(pg_result($resaco,0,'ob01_dtobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,18630,'','".AddSlashes(pg_result($resaco,0,'ob01_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,18631,'','".AddSlashes(pg_result($resaco,0,'ob01_nometitularproc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,18632,'','".AddSlashes(pg_result($resaco,0,'ob01_dtprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,18633,'','".AddSlashes(pg_result($resaco,0,'ob01_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,1013148,'','".AddSlashes(pg_result($resaco,0,'ob01_numeroartprojeto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,1013150,'','".AddSlashes(pg_result($resaco,0,'ob01_numerorrtprojeto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,1013243,'','".AddSlashes(pg_result($resaco,0,'ob01_numeroarttecnico'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,946,1013244,'','".AddSlashes(pg_result($resaco,0,'ob01_numerorrttecnico'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ob01_codobra=null)
    {
      $this->atualizacampos();
     $sql = " update obras set ";
     $virgula = "";
     if(trim($this->ob01_arquitetoobra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_arquitetoobra"])){
      if(trim($this->ob01_arquitetoobra)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ob01_arquitetoobra"])){
        $this->ob01_arquitetoobra = 'null';
      }
      $sql  .= $virgula." ob01_arquitetoobra = $this->ob01_arquitetoobra ";
      $virgula = ",";
     }
    //  if(trim($this->ob01_arquitetoobra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_arquitetoobra"])){
    //    $sql  .= $virgula." ob01_arquitetoobra = $this->ob01_arquitetoobra ";
    //    $virgula = ",";
    //    if(trim($this->ob01_arquitetoobra) == null ){
    //      $this->erro_sql = " Campo Arquiteto da Obra não informado.";
    //      $this->erro_campo = "ob01_arquitetoobra";
    //      $this->erro_banco = "";
    //      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
    //      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
    //      $this->erro_status = "0";
    //      return false;
    //    }
    //  }
     if(trim($this->ob01_codobra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_codobra"])){
       $sql  .= $virgula." ob01_codobra = $this->ob01_codobra ";
       $virgula = ",";
       if(trim($this->ob01_codobra) == null ){
         $this->erro_sql = " Campo Código da Obra não informado.";
         $this->erro_campo = "ob01_codobra";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ob01_responsavelprojeto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_responsavelprojeto"])){
      if(trim($this->ob01_responsavelprojeto)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ob01_responsavelprojeto"])){
        $this->ob01_responsavelprojeto = "0" ;
      }
      $sql  .= $virgula." ob01_responsavelprojeto = '$this->ob01_responsavelprojeto' ";
      $virgula = ",";
     }
    //  if(trim($this->ob01_responsavelprojeto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_responsavelprojeto"])){
    //    $sql  .= $virgula." ob01_responsavelprojeto = $this->ob01_responsavelprojeto ";
    //    $virgula = ",";
    //    if(trim($this->ob01_responsavelprojeto) == null ){
    //      $this->erro_sql = " Campo Responsável pelo Projeto não informado.";
    //      $this->erro_campo = "ob01_responsavelprojeto";
    //      $this->erro_banco = "";
    //      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
    //      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
    //      $this->erro_status = "0";
    //      return false;
    //    }
    //  }
     if(trim($this->ob01_nomeobra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_nomeobra"])){
       $sql  .= $virgula." ob01_nomeobra = '$this->ob01_nomeobra' ";
       $virgula = ",";
       if(trim($this->ob01_nomeobra) == null ){
         $this->erro_sql = " Campo Nome da Obra não informado.";
         $this->erro_campo = "ob01_nomeobra";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ob01_tiporesp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_tiporesp"])){
       $sql  .= $virgula." ob01_tiporesp = $this->ob01_tiporesp ";
       $virgula = ",";
       if(trim($this->ob01_tiporesp) == null ){
         $this->erro_sql = " Campo Tipo de Responsável não informado.";
         $this->erro_campo = "ob01_tiporesp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ob01_regular)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_regular"])){
       $sql  .= $virgula." ob01_regular = '$this->ob01_regular' ";
       $virgula = ",";
       if(trim($this->ob01_regular) == null ){
         $this->erro_sql = " Campo Obra Regular não informado.";
         $this->erro_campo = "ob01_regular";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ob01_dtobra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_dtobra_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["ob01_dtobra_dia"] !="") ){
       $sql  .= $virgula." ob01_dtobra = '$this->ob01_dtobra' ";
       $virgula = ",";
       if(trim($this->ob01_dtobra) == null ){
         $this->erro_sql = " Campo Data Obra não informado.";
         $this->erro_campo = "ob01_dtobra_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["ob01_dtobra_dia"])){
         $sql  .= $virgula." ob01_dtobra = null ";
         $virgula = ",";
         if(trim($this->ob01_dtobra) == null ){
           $this->erro_sql = " Campo Data Obra não informado.";
           $this->erro_campo = "ob01_dtobra_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->ob01_processo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_processo"])){
       if ($this->ob01_processo == "") {
        $this->ob01_processo = 'null';
       }
       $sql  .= $virgula." ob01_processo = $this->ob01_processo ";
       $virgula = ",";
     }
     if(trim($this->ob01_nometitularproc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_nometitularproc"])){
       $sql  .= $virgula." ob01_nometitularproc = '$this->ob01_nometitularproc' ";
       $virgula = ",";
     }
     if(trim($this->ob01_dtprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_dtprocesso_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["ob01_dtprocesso_dia"] !="") ){
       $sql  .= $virgula." ob01_dtprocesso = '$this->ob01_dtprocesso' ";
       $virgula = ",";
     } else {
       if(isset($GLOBALS["HTTP_POST_VARS"]["ob01_dtprocesso_dia"])){
         $sql  .= $virgula." ob01_dtprocesso = null ";
         $virgula = ",";
       }
     }
     if(trim($this->ob01_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_obs"])){
       $sql  .= $virgula." ob01_obs = '$this->ob01_obs' ";
       $virgula = ",";
     }
     if(trim($this->ob01_numeroartprojeto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_numeroartprojeto"])){
        if(trim($this->ob01_numeroartprojeto)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ob01_numeroartprojeto"])){
           $this->ob01_numeroartprojeto = "0" ;
        }
       $sql  .= $virgula." ob01_numeroartprojeto = '$this->ob01_numeroartprojeto' ";
       $virgula = ",";
     }
     if(trim($this->ob01_numerorrtprojeto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_numerorrtprojeto"])){
        if(trim($this->ob01_numerorrtprojeto)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ob01_numerorrtprojeto"])){
           $this->ob01_numerorrtprojeto = "0" ;
        }
       $sql  .= $virgula." ob01_numerorrtprojeto = '$this->ob01_numerorrtprojeto' ";
       $virgula = ",";
     }
     if(trim($this->ob01_numeroarttecnico)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_numeroarttecnico"])){
        if(trim($this->ob01_numeroarttecnico)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ob01_numeroarttecnico"])){
          $this->ob01_numeroarttecnico = "0" ;
        }
        $sql  .= $virgula." ob01_numeroarttecnico = '$this->ob01_numeroarttecnico' ";
        $virgula = ",";
      }
      if(trim($this->ob01_numerorrttecnico)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob01_numerorrttecnico"])){
          if(trim($this->ob01_numerorrttecnico)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ob01_numerorrttecnico"])){
            $this->ob01_numerorrttecnico = "0" ;
          }
        $sql  .= $virgula." ob01_numerorrttecnico = '$this->ob01_numerorrttecnico' ";
        $virgula = ",";
      }
     $sql .= " where ";
     if($ob01_codobra!=null){
       $sql .= " ob01_codobra = $ob01_codobra";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ob01_codobra));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5909,'$this->ob01_codobra','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_arquitetoobra"]) || $this->ob01_arquitetoobra != "")
             $resac = db_query("insert into db_acount values($acount,946,1010571,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_arquitetoobra'))."','$this->ob01_arquitetoobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_codobra"]) || $this->ob01_codobra != "")
             $resac = db_query("insert into db_acount values($acount,946,5909,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_codobra'))."','$this->ob01_codobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_responsavelprojeto"]) || $this->ob01_responsavelprojeto != "")
             $resac = db_query("insert into db_acount values($acount,946,1010570,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_responsavelprojeto'))."','$this->ob01_responsavelprojeto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_nomeobra"]) || $this->ob01_nomeobra != "")
             $resac = db_query("insert into db_acount values($acount,946,5910,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_nomeobra'))."','$this->ob01_nomeobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_tiporesp"]) || $this->ob01_tiporesp != "")
             $resac = db_query("insert into db_acount values($acount,946,5913,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_tiporesp'))."','$this->ob01_tiporesp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_regular"]) || $this->ob01_regular != "")
             $resac = db_query("insert into db_acount values($acount,946,5914,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_regular'))."','$this->ob01_regular',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_dtobra"]) || $this->ob01_dtobra != "")
             $resac = db_query("insert into db_acount values($acount,946,18629,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_dtobra'))."','$this->ob01_dtobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_processo"]) || $this->ob01_processo != "")
             $resac = db_query("insert into db_acount values($acount,946,18630,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_processo'))."','$this->ob01_processo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_nometitularproc"]) || $this->ob01_nometitularproc != "")
             $resac = db_query("insert into db_acount values($acount,946,18631,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_nometitularproc'))."','$this->ob01_nometitularproc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_dtprocesso"]) || $this->ob01_dtprocesso != "")
             $resac = db_query("insert into db_acount values($acount,946,18632,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_dtprocesso'))."','$this->ob01_dtprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_obs"]) || $this->ob01_obs != "")
             $resac = db_query("insert into db_acount values($acount,946,18633,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_obs'))."','$this->ob01_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_numeroartprojeto"]) || $this->ob01_numeroartprojeto != "")
             $resac = db_query("insert into db_acount values($acount,946,1013148,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_numeroartprojeto'))."','$this->ob01_numeroartprojeto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_numerorrtprojeto"]) || $this->ob01_numerorrtprojeto != "")
             $resac = db_query("insert into db_acount values($acount,946,1013150,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_numerorrtprojeto'))."','$this->ob01_numerorrtprojeto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_numeroarttecnico"]) || $this->ob01_numeroarttecnico != "")
             $resac = db_query("insert into db_acount values($acount,946,1013243,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_numeroarttecnico'))."','$this->ob01_numeroarttecnico',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob01_numerorrttecnico"]) || $this->ob01_numerorrttecnico != "")
             $resac = db_query("insert into db_acount values($acount,946,1013244,'".AddSlashes(pg_result($resaco,$conresaco,'ob01_numerorrttecnico'))."','$this->ob01_numerorrttecnico',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "cadastro de obras não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ob01_codobra;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "cadastro de obras não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ob01_codobra;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ob01_codobra;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($ob01_codobra=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ob01_codobra));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,5909,'$ob01_codobra','E')");
           $resac  = db_query("insert into db_acount values($acount,946,1010571,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_arquitetoobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,5909,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_codobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,1010570,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_responsavelprojeto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,5910,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_nomeobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,5913,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_tiporesp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,5914,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_regular'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,18629,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_dtobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,18630,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,18631,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_nometitularproc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,18632,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_dtprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,18633,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,1013148,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_numeroartprojeto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,1013150,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_numerorrtprojeto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,1013243,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_numeroarttecnico'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,946,1013244,'','".AddSlashes(pg_result($resaco,$iresaco,'ob01_numerorrttecnico'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from obras
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ob01_codobra)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ob01_codobra = $ob01_codobra ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "cadastro de obras não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ob01_codobra;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "cadastro de obras não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ob01_codobra;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ob01_codobra;
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
        $this->erro_sql   = "Record Vazio na Tabela:obras";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ob01_codobra = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from obras ";
     $sql .= "      inner join obrastiporesp  on  obrastiporesp.ob02_cod = obras.ob01_tiporesp";
     $sql .= "      left join obrastec  on  obrastec.ob15_sequencial = obras.ob01_responsavelprojeto and  obrastec.ob15_sequencial = obras.ob01_arquitetoobra";
     $sql .= "      left join cgm  on  cgm.z01_numcgm = obrastec.ob15_numcgm";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob01_codobra)) {
         $sql2 .= " where obras.ob01_codobra = $ob01_codobra ";
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

    public function sql_query_file($ob01_codobra = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from obras ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob01_codobra)){
         $sql2 .= " where obras.ob01_codobra = $ob01_codobra ";
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

    function sql_query_infob ( $ob01_codobra=null,$campos="*",$ordem=null,$dbwhere=""){

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
        $sql .= " from obras                                                                             ";
        $sql .= "inner join obrasender       on obrasender.ob07_codobra    = obras.ob01_codobra          ";
        $sql .= "inner join ruas             on ruas.j14_codigo            = obrasender.ob07_lograd      ";
        $sql .= "inner join bairro           on bairro.j13_codi            = obrasender.ob07_bairro      ";
        $sql .= "inner join obrastiporesp    on obrastiporesp.ob02_cod     = obras.ob01_tiporesp         ";
        $sql .= "inner join obrasresp        on obrasresp.ob10_codobra     = obras.ob01_codobra          ";
        $sql .= "inner join cgm responsavel  on responsavel.z01_numcgm     = obrasresp.ob10_numcgm       ";
        $sql .= "inner join obraspropri      on obraspropri.ob03_codobra   = obras.ob01_codobra          ";
        $sql .= "inner join cgm proprietario on proprietario.z01_numcgm    = obraspropri.ob03_numcgm     ";
        $sql .= " left join obraslote        on obraslote.ob05_codobra     = obras.ob01_codobra          ";
        $sql .= " left join lote             on lote.j34_idbql             = obraslote.ob05_idbql        ";
        $sql .= " left join obraslotei       on obraslotei.ob06_codobra    = obras.ob01_codobra          ";
        $sql .= " left join obrasalvara      on obrasalvara.ob04_codobra   = obras.ob01_codobra          ";
        $sql .= " left join obrastecnicos    on obrastecnicos.ob20_codobra = obras.ob01_codobra          ";
        $sql .= " left join obrastec         on obrastec.ob15_sequencial   = obrastecnicos.ob20_obrastec ";
        $sql .= " left join cgm tecnico      on tecnico.z01_numcgm         = obrastec.ob15_numcgm        ";

        $sql2 = "";

        if($dbwhere==""){
            if($ob01_codobra!=null ){
                $sql2 .= " where obras.ob01_codobra = $ob01_codobra ";
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
    /**
     * SQL para retornar dados para geração do
     * arquivo SISOBRANET
     *
     * @param integer $iMes  - mes da competencia
     * @param integer $iAno  - ano da competencia
     */
    function sql_queryDadosSisobra($iMes, $iAno){

        $sSqlSisobra = "select *                                                                                           \n";
        $sSqlSisobra .= "  from (                                                                                          \n";
        $sSqlSisobra .= "  select cgmResponsavel.z01_nome                                      as nomeResponsavel,         \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_numcgm                                    as cgmResponsavel,          \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_cgccpf                                    as cpfResponsavel,          \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_ender ||', ' || cgmResponsavel.z01_numero as enderecoResponsavel,     \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_bairro                                    as bairroResponsavel,       \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_cep                                       as cepResponsavel,          \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_uf                                        as ufResponsavel,           \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_telef                                     as telefoneResponsavel,     \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_fax                                       as faxResponsavel,          \n";
        $sSqlSisobra .= "         cgmResponsavel.z01_email                                     as emailResponsavel,        \n";
        $sSqlSisobra .= "         ob01_tiporesp                                                as tipoVinculoResponsavel,  \n";
        $sSqlSisobra .= "         cgmConstrutor.z01_nome                                       as nomeConstrutor,          \n";
        $sSqlSisobra .= "         cgmConstrutor.z01_numcgm                                     as cgmConstrutor,           \n";
        $sSqlSisobra .= "         cgmConstrutor.z01_cgccpf                                     as cpfConstrutor,           \n";
        $sSqlSisobra .= "         cgmConstrutor.z01_ender ||', ' || cgmConstrutor.z01_numero   as enderecoConstrutor,      \n";
        $sSqlSisobra .= "         cgmConstrutor.z01_bairro                                     as bairroConstrutor,        \n";
        $sSqlSisobra .= "         cgmConstrutor.z01_cep                                        as cepConstrutor,           \n";
        $sSqlSisobra .= "         cgmConstrutor.z01_uf                                         as ufConstrutor,            \n";
        $sSqlSisobra .= "         ob04_alvara                                                  as alvaraObra,              \n";
        $sSqlSisobra .= "         ob04_data                                                    as dataObra,                \n";
        $sSqlSisobra .= "         ob01_nomeobra                                                as nomeObra,                \n";
        $sSqlSisobra .= "         j14_nome ||', ' || ob07_numero                               as enderObra,               \n";
        $sSqlSisobra .= "         bairro.j13_descr                                             as bairroObra,              \n";
        $sSqlSisobra .= "         j29_cep                                                      as cepObra,                 \n";
        $sSqlSisobra .= "         cgmObras.z01_telef                                           as telefoneObra,            \n";
        $sSqlSisobra .= "         cgmObras.z01_fax                                             as faxObra,                 \n";
        $sSqlSisobra .= "         ob07_inicio                                                  as dataInicioObra,          \n";
        $sSqlSisobra .= "         ob07_fim                                                     as dataFimObra,             \n";
        $sSqlSisobra .= "         case                                                                                     \n";
        $sSqlSisobra .= "           when ob08_ocupacao = 10000                                                             \n";
        $sSqlSisobra .= "             then '0'                                                                             \n";
        $sSqlSisobra .= "           when ob08_ocupacao = 10001                                                             \n";
        $sSqlSisobra .= "             then '1'                                                                             \n";
        $sSqlSisobra .= "           when ob08_ocupacao = 10002                                                             \n";
        $sSqlSisobra .= "             then '2'                                                                             \n";
        $sSqlSisobra .= "           else ''                                                                                \n";
        $sSqlSisobra .= "         end                                                         as tipoOcupacaoObra,         \n";
        $sSqlSisobra .= "         case                                                                                     \n";
        $sSqlSisobra .= "           when ob08_tipoconstr = 20000                                                           \n";
        $sSqlSisobra .= "             then '0'                                                                             \n";
        $sSqlSisobra .= "           when ob08_tipoconstr = 20001                                                           \n";
        $sSqlSisobra .= "             then '1'                                                                             \n";
        $sSqlSisobra .= "           when ob08_tipoconstr = 20002                                                           \n";
        $sSqlSisobra .= "             then '2'                                                                             \n";
        $sSqlSisobra .= "           else ''                                                                                \n";
        $sSqlSisobra .= "         end                                                         as tipoConstrucaoObra,       \n";
        $sSqlSisobra .= "         ob08_area                                                   as areaObra,                 \n";
        $sSqlSisobra .= "         case when ob08_tipolanc = 30001                                                          \n";
        $sSqlSisobra .= "           then                                                                                   \n";
        $sSqlSisobra .= "             case                                                                                 \n";
        $sSqlSisobra .= "               when ob08_ocupacao = 10000                                                         \n";
        $sSqlSisobra .= "                 then '0'                                                                         \n";
        $sSqlSisobra .= "               when ob08_ocupacao = 10001                                                         \n";
        $sSqlSisobra .= "                 then '1'                                                                         \n";
        $sSqlSisobra .= "               when ob08_ocupacao = 10002                                                         \n";
        $sSqlSisobra .= "                 then '2'                                                                         \n";
        $sSqlSisobra .= "               else ''                                                                            \n";
        $sSqlSisobra .= "             end                                                                                  \n";
        $sSqlSisobra .= "             else ''                                                                              \n";
        $sSqlSisobra .= "          end                                                        as tipoOcupacaoDemolicao,    \n";
        $sSqlSisobra .= "          case when ob08_tipolanc = 30001                                                         \n";
        $sSqlSisobra .= "            then                                                                                  \n";
        $sSqlSisobra .= "              case                                                                                \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20000                                                      \n";
        $sSqlSisobra .= "                  then '0'                                                                        \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20001                                                      \n";
        $sSqlSisobra .= "                  then '1'                                                                        \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20002                                                      \n";
        $sSqlSisobra .= "                  then '2'                                                                        \n";
        $sSqlSisobra .= "                else ''                                                                           \n";
        $sSqlSisobra .= "              end                                                                                 \n";
        $sSqlSisobra .= "            else ''                                                                               \n";
        $sSqlSisobra .= "          end                                                        as tipoDemolicao,            \n";
        $sSqlSisobra .= "          case                                                                                    \n";
        $sSqlSisobra .= "            when ob08_tipolanc = 30001                                                            \n";
        $sSqlSisobra .= "              then ob08_area                                                                      \n";
        $sSqlSisobra .= "              else null                                                                           \n";
        $sSqlSisobra .= "          end                                                      as areaDemolicao,              \n";
        $sSqlSisobra .= "          case when ob08_tipolanc = 30002                                                         \n";
        $sSqlSisobra .= "            then                                                                                  \n";
        $sSqlSisobra .= "              case                                                                                \n";
        $sSqlSisobra .= "                when ob08_ocupacao = 10000                                                        \n";
        $sSqlSisobra .= "                  then '0'                                                                        \n";
        $sSqlSisobra .= "                when ob08_ocupacao = 10001                                                        \n";
        $sSqlSisobra .= "                  then '1'                                                                        \n";
        $sSqlSisobra .= "                when ob08_ocupacao = 10002                                                        \n";
        $sSqlSisobra .= "                  then '2'                                                                        \n";
        $sSqlSisobra .= "                else ''                                                                           \n";
        $sSqlSisobra .= "              end                                                                                 \n";
        $sSqlSisobra .= "            else ''                                                                               \n";
        $sSqlSisobra .= "          end                                                        as tipoOcupacaoAcrescimo,    \n";
        $sSqlSisobra .= "          case when ob08_tipolanc = 30002                                                         \n";
        $sSqlSisobra .= "            then                                                                                  \n";
        $sSqlSisobra .= "              case                                                                                \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20000                                                      \n";
        $sSqlSisobra .= "                  then '0'                                                                        \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20001                                                      \n";
        $sSqlSisobra .= "                  then '1'                                                                        \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20002                                                      \n";
        $sSqlSisobra .= "                  then '2'                                                                        \n";
        $sSqlSisobra .= "                else ''                                                                           \n";
        $sSqlSisobra .= "              end                                                                                 \n";
        $sSqlSisobra .= "            else ''                                                                               \n";
        $sSqlSisobra .= "          end                                                        as tipoAcrescimo,            \n";
        $sSqlSisobra .= "          case                                                                                    \n";
        $sSqlSisobra .= "            when ob08_tipolanc = 30002                                                            \n";
        $sSqlSisobra .= "              then ob08_area                                                                      \n";
        $sSqlSisobra .= "              else null                                                                           \n";
        $sSqlSisobra .= "          end                                                        as areaAcrescimo,            \n";
        $sSqlSisobra .= "          ob07_areaatual                                             as areaExistente,            \n";
        $sSqlSisobra .= "          case when ob08_tipolanc = 30003                                                         \n";
        $sSqlSisobra .= "            then                                                                                  \n";
        $sSqlSisobra .= "              case                                                                                \n";
        $sSqlSisobra .= "                when ob08_ocupacao = 10000                                                        \n";
        $sSqlSisobra .= "                  then '0'                                                                        \n";
        $sSqlSisobra .= "                when ob08_ocupacao = 10001                                                        \n";
        $sSqlSisobra .= "                  then '1'                                                                        \n";
        $sSqlSisobra .= "                when ob08_ocupacao = 10002                                                        \n";
        $sSqlSisobra .= "                  then '2'                                                                        \n";
        $sSqlSisobra .= "                else ''                                                                           \n";
        $sSqlSisobra .= "              end                                                                                 \n";
        $sSqlSisobra .= "            else ''                                                                               \n";
        $sSqlSisobra .= "          end                                                        as tipoOcupacaoReforma,      \n";
        $sSqlSisobra .= "          case when ob08_tipolanc = 30003                                                         \n";
        $sSqlSisobra .= "            then                                                                                  \n";
        $sSqlSisobra .= "              case                                                                                \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20000                                                      \n";
        $sSqlSisobra .= "                  then '0'                                                                        \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20001                                                      \n";
        $sSqlSisobra .= "                  then '1'                                                                        \n";
        $sSqlSisobra .= "                when ob08_tipoconstr = 20002                                                      \n";
        $sSqlSisobra .= "                  then '2'                                                                        \n";
        $sSqlSisobra .= "                else ''                                                                           \n";
        $sSqlSisobra .= "              end                                                                                 \n";
        $sSqlSisobra .= "            else ''                                                                               \n";
        $sSqlSisobra .= "          end                                                        as tipoReforma,              \n";
        $sSqlSisobra .= "          case                                                                                    \n";
        $sSqlSisobra .= "            when ob08_tipolanc = 30003                                                            \n";
        $sSqlSisobra .= "              then ob08_area                                                                      \n";
        $sSqlSisobra .= "              else null                                                                           \n";
        $sSqlSisobra .= "          end                                                        as areaReforma,              \n";
        $sSqlSisobra .= "          ob09_habite                                                as numeroHabitese,           \n";
        $sSqlSisobra .= "          ob09_data                                                  as dataHabitese,             \n";
        $sSqlSisobra .= "          ob09_area                                                  as areaHabitese,             \n";
        $sSqlSisobra .= "          case                                                                                    \n";
        $sSqlSisobra .= "            when ob09_parcial is true                                                             \n";
        $sSqlSisobra .= "              then 'P'                                                                            \n";
        $sSqlSisobra .= "            else   'T'                                                                            \n";
        $sSqlSisobra .= "          end                                                       as tipoHabitese,              \n";
        $sSqlSisobra .= "          ob07_unidades                                             as iUnidades,                 \n";
        $sSqlSisobra .= "          ob07_pavimentos                                           as iPavimentos,               \n";
        $sSqlSisobra .= "          ob01_codobra                                              as codigoObra,                \n";
        $sSqlSisobra .= "          ob09_codhab                                               as codigoHabitese             \n";
        $sSqlSisobra .= "           from obras                                                                             \n";
        $sSqlSisobra .= "            left join obrasender    on ob01_codobra   = ob07_codobra                              \n";
        $sSqlSisobra .= "            left join ruas          on ob07_lograd    = j14_codigo                                \n";
        $sSqlSisobra .= "            left join obrastiporesp on ob01_codobra   = ob02_cod                                  \n";
        $sSqlSisobra .= "            left join obrasconstr   on ob01_codobra   = ob08_codobra                              \n";
        $sSqlSisobra .= "            left join obraspropri   on ob01_codobra   = ob03_codobra                              \n";
        $sSqlSisobra .= "            left join obrasiptubase on ob24_obras     = ob01_codobra                              \n";
        $sSqlSisobra .= "            left join iptubase      on j01_matric     = ob24_iptubase                             \n";
        $sSqlSisobra .= "            left join lote          on j34_idbql      = j01_idbql                                 \n";
        $sSqlSisobra .= "            left join bairro        on j13_codi       = ob07_bairro                               \n";
        $sSqlSisobra .= "            left join obraslotei    on ob01_codobra   = ob06_codobra                              \n";
        $sSqlSisobra .= "            left join obrashabite   on ob08_codconstr = ob09_codconstr                            \n";
        $sSqlSisobra .= "            left join cgm        as cgmResponsavel       on ob03_numcgm                     = cgmResponsavel.z01_numcgm             \n";
        $sSqlSisobra .= "            left join obrasresp  as obrasrespConstrutor  on ob01_codobra                    = obrasrespConstrutor.ob10_codobra      \n";
        $sSqlSisobra .= "            left join cgm        as cgmConstrutor        on obrasrespConstrutor.ob10_numcgm = cgmConstrutor.z01_numcgm              \n";
        $sSqlSisobra .= "            left join obrasalvara                        on ob01_codobra                    = ob04_codobra                          \n";
        $sSqlSisobra .= "            left join ruascep                            on j29_codigo                      = j14_codigo                            \n";
        $sSqlSisobra .= "            left join cgm        as cgmObras             on cgmObras.z01_numcgm             = ob03_numcgm                           \n";
        $sSqlSisobra .= "           where extract(month from ob09_data) = {$iMes}                                                                            \n";
        $sSqlSisobra .= "             and extract(year from ob09_data)  = {$iAno}                                                                            \n";
        $sSqlSisobra .= "             and ob04_codobra is not null                                                                                           \n";
        $sSqlSisobra .= "             and ob01_codobra in (select ob01_codobra                                                                               \n";
        $sSqlSisobra .= "                                     from obras                                                                                     \n";
        $sSqlSisobra .= "                                          left join obrasenvioreg    on ob01_codobra         = ob17_codobra                         \n";
        $sSqlSisobra .= "                                          left join obrasenvio       on ob16_codobrasenvio   = ob17_codobrasenvio                   \n";
        $sSqlSisobra .= "                                          left join obrasenvioreghab on ob18_codobraenvioreg = ob17_codobrasenvioreg                \n";
        $sSqlSisobra .= "                                          left join obrashabite      on ob09_codhab          = ob18_codhabite                       \n";
        $sSqlSisobra .= "                                    where case when ob09_parcial is null                                                            \n";
        $sSqlSisobra .= "                                               then case when ob16_dtini is null                                                    \n";
        $sSqlSisobra .= "                                                         then true                                                                  \n";
        $sSqlSisobra .= "                                                         else (     extract( month from ob16_dtini) = {$iMes}                       \n";
        $sSqlSisobra .= "                                                                and extract( year  from ob16_dtini) = {$iAno}                       \n";
        $sSqlSisobra .= "                                                              )                                                                     \n";
        $sSqlSisobra .= "                                                    end                                                                             \n";
        $sSqlSisobra .= "                                               else ob09_parcial is true                                                            \n";
        $sSqlSisobra .= "                                          end                                                                                       \n";
        $sSqlSisobra .= "                                  )                                                                                                 \n";
        $sSqlSisobra .= "          ) as sql_base                                                                                                             \n";
        $sSqlSisobra .= "    where case when sql_base.numerohabitese is null                                                                                 \n";
        $sSqlSisobra .= "               then sql_base.codigoobra not in (select ob17_codobra                                                                 \n";
        $sSqlSisobra .= "                                                 from obrasenvioreg                                                                 \n";
        $sSqlSisobra .= "                                                      left join obrasenvioreghab on ob18_codobraenvioreg = ob17_codobrasenvioreg    \n";
        $sSqlSisobra .= "                                                      left join obrasenvio       on ob16_codobrasenvio   = ob17_codobrasenvio       \n";
        $sSqlSisobra .= "                                                where extract( month from ob16_dtini) = {$iMes}                                     \n";
        $sSqlSisobra .= "                                                  and extract( year  from ob16_dtini) = {$iAno}                                     \n";
        $sSqlSisobra .= "                                                  and ob18_codhabite is null                                                        \n";
        $sSqlSisobra .= "                                              )                                                                                     \n";
        $sSqlSisobra .= "               else cast(sql_base.numerohabitese as integer) not in (                                                                                \n";
        $sSqlSisobra .= "                                                   select ob18_codhabite                                                            \n";
        $sSqlSisobra .= "                                                     from obrasenvioreghab                                                          \n";
        $sSqlSisobra .= "                                                          inner join obrasenvioreg on ob18_codobraenvioreg = ob17_codobrasenvioreg  \n";
        $sSqlSisobra .= "                                                          inner join obrasenvio    on ob16_codobrasenvio   = ob17_codobrasenvio     \n";
        $sSqlSisobra .= "                                                    where extract( month from ob16_dtini) = {$iMes}                                 \n";
        $sSqlSisobra .= "                                                      and extract( year  from ob16_dtini) = {$iAno}                                 \n";
        $sSqlSisobra .= "                                                  )                                                                                 \n";
        $sSqlSisobra .= "                                                                                                                                    \n";
        $sSqlSisobra .= "                                                                                                                                    \n";
        $sSqlSisobra .= "          end                                                                                                                       \n";

        return $sSqlSisobra;

    }

    /**
     * SQL para retornar dados para geração do
     * XML do SISOBRAPREF
     *
     * @param integer $iMes  - mes da competencia
     * @param integer $iAno  - ano da competencia
     */
    function sql_queryDadosSisobraWebservice($iMes, $iAno){
      $sSqlSisobra = "SELECT
                          sql_base.ob01_codobra AS codigoobra,
                          sql_base.ob08_codconstr AS codconstr,
                          sql_base.ob04_alvara AS alvaraobra,
                          sql_base.ob04_idalvara as idalvara,
                          sql_base.ob09_codhab AS codigohabitese,
                          sql_base.ob04_data AS dataalvara,
                          sql_base.ob09_data AS datahabitese,
                          CASE
                              WHEN sql_base.ob09_parcial IS TRUE THEN 'P'
                              ELSE 'T'
                          END AS tipohabitese,
                          sql_base.ob08_area AS areaobra,
                          sql_base.ob09_area as areaobrahabite,
                          sql_base.j29_cep AS cepobra,
                          sql_base.j13_descr AS bairroobra,
                          sql_base.ob07_inicio AS datainicioobra,
                          sql_base.ob07_fim AS datafimobra,
                          sql_base.ob01_nomeobra AS nomeobra,
                          sql_base.j88_descricao AS tipologradouro,
                          sql_base.j14_nome AS logradouro,
                          sql_base.ob07_numero AS numlogradouro,
                          sql_base.ob07_compl AS complogradouro,
                          sql_base.destinacao,
                          sql_base.tipoobra,
                          sql_base.categoria,
                          sql_base.ob01_obs AS obsalvara,
                          sql_base.ob09_obsinss AS obshabite,
                          sql_base.z01_cgccpf AS propriobracgccpf,
                          sql_base.ob02_cod AS respexecobra
                      FROM
                          (
                              SELECT
                                  ob01_codobra,
                                  ob08_codconstr,
                                  ob04_alvara,
                                  ob04_idalvara,
                                  ob09_codhab,
                                  ob04_data,
                                  ob09_data,
                                  ob09_parcial,
                                  ob08_area,
                                  ob09_area,
                                  j29_cep,
                                  j13_descr,
                                  ob07_inicio,
                                  ob07_fim,
                                  ob01_nomeobra,
                                  j88_descricao,
                                  j14_nome,
                                  ob07_numero,
                                  ob07_compl,
                                  a.j31_descr AS destinacao,
                                  b.j31_descr AS tipoobra,
                                  c.j31_descr AS categoria,
                                  ob01_obs,
                                  ob09_obsinss,
                                  z01_cgccpf,
                                  ob02_cod
                              FROM
                                  obras
                                  INNER JOIN obrasalvara ON ob04_codobra = ob01_codobra
                                  INNER JOIN obrasconstr ON ob08_codobra = ob01_codobra
                                  LEFT JOIN obrashabite ON ob09_codconstr = ob08_codconstr
                                  LEFT JOIN obrasender ON ob01_codobra = ob07_codobra
                                  LEFT JOIN ruas ON ob07_lograd = j14_codigo
                                  LEFT JOIN ruascep ON j29_codigo = j14_codigo
                                  LEFT JOIN bairro ON j13_codi = ob07_bairro
                                  LEFT JOIN ruastipo ON j88_codigo = j14_tipo
                                  INNER JOIN caracter a ON a.j31_codigo = obrasconstr.ob08_ocupacao
                                  INNER JOIN caracter b ON b.j31_codigo = obrasconstr.ob08_tipoconstr
                                  INNER JOIN caracter c ON c.j31_codigo = obrasconstr.ob08_tipolanc
                                  LEFT JOIN obraspropri ON ob03_codobra = ob01_codobra
                                  LEFT JOIN cgm ON ob03_numcgm = z01_numcgm
                                  LEFT JOIN obrastiporesp ON ob02_cod = ob01_tiporesp
                              WHERE
                                  extract (
                                      MONTH
                                      FROM
                                          ob04_data
                                  ) = {$iMes}
                                  AND extract (
                                      year
                                      FROM
                                          ob04_data
                                  ) = {$iAno}
                                  AND ob04_alvara NOT IN (
                                      SELECT
                                          ob31_codalvara
                                      FROM
                                          obrasenvioreg
                                          INNER JOIN obrasenvioregalvara ON ob31_obrasenvioreg = ob17_codobrasenvioreg
                                          INNER JOIN obrasenvio ON ob17_codobrasenvio = ob16_codobrasenvio
                                      WHERE
                                          ob16_dtfim >= '2021-03-01'
                                  )
                              UNION
                              SELECT
                                  ob01_codobra,
                                  ob08_codconstr,
                                  ob04_alvara,
                                  ob04_idalvara,
                                  ob09_codhab,
                                  ob04_data,
                                  ob09_data,
                                  ob09_parcial,
                                  ob08_area,
                                  ob09_area,
                                  j29_cep,
                                  j13_descr,
                                  ob07_inicio,
                                  ob07_fim,
                                  ob01_nomeobra,
                                  j88_descricao,
                                  j14_nome,
                                  ob07_numero,
                                  ob07_compl,
                                  a.j31_descr AS destinacao,
                                  b.j31_descr AS tipoobra,
                                  c.j31_descr AS categoria,
                                  ob01_obs,
                                  ob09_obsinss,
                                  z01_cgccpf,
                                  ob02_cod
                              FROM
                                  obras
                                  INNER JOIN obrasconstr ON ob08_codobra = ob01_codobra
                                  INNER JOIN obrashabite ON ob09_codconstr = ob08_codconstr
                                  INNER JOIN obrasalvara ON ob04_codobra = ob01_codobra
                                  LEFT JOIN obrasender ON ob01_codobra = ob07_codobra
                                  LEFT JOIN ruas ON ob07_lograd = j14_codigo
                                  LEFT JOIN ruascep ON j29_codigo = j14_codigo
                                  LEFT JOIN bairro ON j13_codi = ob07_bairro
                                  LEFT JOIN ruastipo ON j88_codigo = j14_tipo
                                  INNER JOIN caracter a ON a.j31_codigo = obrasconstr.ob08_ocupacao
                                  INNER JOIN caracter b ON b.j31_codigo = obrasconstr.ob08_tipoconstr
                                  INNER JOIN caracter c ON c.j31_codigo = obrasconstr.ob08_tipolanc
                                  LEFT JOIN obraspropri ON ob03_codobra = ob01_codobra
                                  LEFT JOIN cgm ON ob03_numcgm = z01_numcgm
                                  LEFT JOIN obrastiporesp ON ob02_cod = ob01_tiporesp
                              WHERE
                                  extract (
                                      MONTH
                                      FROM
                                          ob09_data
                                  ) = {$iMes}
                                  AND extract (
                                      year
                                      FROM
                                          ob09_data
                                  ) = {$iAno}
                                  AND ob09_codhab NOT IN (
                                      SELECT
                                          ob18_codhabite
                                      FROM
                                          obrasenvioreghab
                                  )
                          ) AS sql_base
                      ORDER BY
                          sql_base.ob01_codobra limit 10;
      ";
      
      return $sSqlSisobra;

    }
    function sql_query_obras_construcoes($iCodigoObra) {

        $sSql  = "select ob08_codconstr,                                                                \n";
        $sSql .= "       ob08_codobra,                                                                  \n";
        $sSql .= "       ob01_nomeobra,                                                                 \n";
        $sSql .= "       ob08_area,                                                                     \n";
        $sSql .= "       ob08_ocupacao,                                                                 \n";
        $sSql .= "       a.j31_descr as ob08_descrocupacao,                                             \n";
        $sSql .= "       ob08_tipoconstr,                                                               \n";
        $sSql .= "       b.j31_descr as ob08_descrtipoconstr,                                           \n";
        $sSql .= "       ob08_tipolanc,                                                                 \n";
        $sSql .= "       c.j31_descr as ob08_descrtipolanc,                                             \n";
        $sSql .= "       ob07_lograd,                                                                   \n";
        $sSql .= "       j14_nome,                                                                      \n";
        $sSql .= "       ob07_numero,                                                                   \n";
        $sSql .= "       ob07_compl,                                                                    \n";
        $sSql .= "       ob07_bairro,                                                                   \n";
        $sSql .= "       j13_descr,                                                                     \n";
        $sSql .= "       ob07_areaatual,                                                                \n";
        $sSql .= "       ob07_unidades,                                                                 \n";
        $sSql .= "       ob07_pavimentos,                                                               \n";
        $sSql .= "       ob07_inicio,                                                                   \n";
        $sSql .= "       ob07_fim                                                                       \n";
        $sSql .= "  from obras                                                                          \n";
        $sSql .= " inner join obrasconstr   on obrasconstr.ob08_codobra   = obras.ob01_codobra          \n";
        $sSql .= " inner join obrasender    on obrasender.ob07_codconstr  = obrasconstr.ob08_codconstr  \n";
        $sSql .= " inner join caracter a    on a.j31_codigo               = obrasconstr.ob08_ocupacao   \n";
        $sSql .= " inner join caracter b    on b.j31_codigo               = obrasconstr.ob08_tipoconstr \n";
        $sSql .= " inner join caracter c    on c.j31_codigo               = obrasconstr.ob08_tipolanc   \n";
        $sSql .= " inner join bairro        on bairro.j13_codi            = obrasender.ob07_bairro      \n";
        $sSql .= "  left join ruas          on ruas.j14_codigo            = obrasender.ob07_lograd      \n";
        $sSql .= " where obras.ob01_codobra = {$iCodigoObra}                                            \n";
        return $sSql;

    }
    /**
     * Busca dados das obras incluindo dados da matricula
     * @param  integer $iCodigoObra
     * @param  string  $sCampos
     * @param  string  $sOrdem
     * @param  strign  $sWhere
     * @return string
     */
    function sql_query_consultaObras ( $iCodigoObra = null, $sCampos = "*", $sOrdem = null, $sWhere = "" ) {

        $sql = " select                                                                                     ";

        if( $sCampos != "*" ){

            $campos_sql = explode("#",$sCampos);
            $sql       .= implode(", ", $campos_sql);
        } else {
            $sql       .= $sCampos;
        }

        $sql .= " from obras                                                                                ";
        $sql .= "      inner join obrastiporesp  on obrastiporesp.ob02_cod      = obras.ob01_tiporesp       ";
        $sql .= "      inner join obrasresp      on obrasresp.ob10_codobra      = obras.ob01_codobra        ";
        $sql .= "      inner join cgm as      r  on obrasresp.ob10_numcgm       = r.z01_numcgm              ";
        $sql .= "      inner join obraspropri    on obras.ob01_codobra          = obraspropri.ob03_codobra  ";
        $sql .= "      inner join cgm as      p  on obraspropri.ob03_numcgm     = p.z01_numcgm              ";
        $sql .= "      left  join obrasiptubase  on obras.ob01_codobra          = obrasiptubase.ob24_obras  ";
        $sql .= "      left  join obraslotei     on obras.ob01_codobra          = obraslotei.ob06_codobra   ";
        $sql .= "      left  join iptubase       on obrasiptubase.ob24_iptubase = iptubase.j01_matric       ";
        $sql .= "      left  join obrasalvara    on obras.ob01_codobra          = obrasalvara.ob04_codobra  ";
        $sql .= "      left  join obrasconstr    on obrasconstr.ob08_codobra    = obras.ob01_codobra        ";
        $sql .= "      left  join obrasender     on obrasender.ob07_codconstr   = obrasconstr.ob08_codconstr";
        $sql .= "      left  join ruas           on obrasender.ob07_lograd      = ruas.j14_codigo           ";
        $sql .= "      left  join bairro         on obrasender.ob07_bairro      = bairro.j13_codi           ";

        $sql2 = "";

        if ( !empty($sWhere) ) {
            $sql2 = " where $sWhere";
        }
        $sql .= $sql2;

        if ( !empty($sOrdem) ) {

            $sql       .= " order by ";
            $campos_sql = explode("#",$sOrdem);
            $sql       .= implode(", ", $campos_sql);
        }
        return $sql;
    }

    function sqlResponsavelArquiteto($campos = array(), $where = array())
    {
        $campos = count($campos) > 0 ? implode(', ', $campos) : '*';
        $where = count($where) > 0 ? ' where ' . implode(' AND ', $where) : '';

        $sql  = "select {$campos} ";
        $sql .= "  from obras ";
        $sql .= "       inner join obrastec tecnico_responsavel on tecnico_responsavel.ob15_sequencial = ob01_arquitetoobra ";
        $sql .= "       inner join cgm cgm_responsavel          on cgm_responsavel.z01_numcgm = tecnico_responsavel.ob15_numcgm ";
        $sql .= "       inner join obrastec tecnico_arquiteto   on tecnico_arquiteto.ob15_sequencial = ob01_arquitetoobra ";
        $sql .= "       inner join cgm cgm_arquiteto            on cgm_arquiteto.z01_numcgm = tecnico_arquiteto.ob15_numcgm ";
        $sql .= " {$where} ";

        return $sql;
    }
}
