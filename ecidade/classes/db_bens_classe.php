<?php

class cl_bens
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
    public $t52_bem = 0;
    public $t52_codcla = 0;
    public $t52_numcgm = 0;
    public $t52_valaqu = 0;
    public $t52_dtaqu_dia = null;
    public $t52_dtaqu_mes = null;
    public $t52_dtaqu_ano = null;
    public $t52_dtaqu = null;
    public $t52_ident = null;
    public $t52_descr = null;
    public $t52_obs = null;
    public $t52_depart = 0;
    public $t52_instit = 0;
    public $t52_bensmarca = 0;
    public $t52_bensmedida = 0;
    public $t52_bensmodelo = 0;
    public $t52_foto = 0;
    public $t52_dtinclusao_dia = null;
    public $t52_dtinclusao_mes = null;
    public $t52_dtinclusao_ano = null;
    public $t52_dtinclusao = null;
    public $t52_processo = null;
    public $t52_estorage = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 t52_bem = int8 = Código do bem
                 t52_codcla = int8 = Classificação
                 t52_numcgm = int4 = Fornecedor
                 t52_valaqu = float8 = Valor da aquisição
                 t52_dtaqu = date = Data da aquisição
                 t52_ident = varchar(20) = Placa
                 t52_descr = varchar(100) = Descrição do bem
                 t52_obs = text = Observações
                 t52_depart = int4 = Departamento
                 t52_instit = int4 = Instituição
                 t52_bensmarca = int4 = Cód Marca
                 t52_bensmedida = int4 = Cód Medida
                 t52_bensmodelo = int4 = Cód Modelo
                 t52_foto = oid = Foto do Bem
                 t52_dtinclusao = date = Data Inclusão
                 t52_processo = varchar(50) = Processo
                 t52_estorage = bool = Bens E-storage
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("bens");
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
       $this->t52_bem = ($this->t52_bem == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_bem"]:$this->t52_bem);
       $this->t52_codcla = ($this->t52_codcla == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_codcla"]:$this->t52_codcla);
       $this->t52_numcgm = ($this->t52_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_numcgm"]:$this->t52_numcgm);
       $this->t52_valaqu = ($this->t52_valaqu == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_valaqu"]:$this->t52_valaqu);
       if($this->t52_dtaqu == ""){
         $this->t52_dtaqu_dia = ($this->t52_dtaqu_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_dtaqu_dia"]:$this->t52_dtaqu_dia);
         $this->t52_dtaqu_mes = ($this->t52_dtaqu_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_dtaqu_mes"]:$this->t52_dtaqu_mes);
         $this->t52_dtaqu_ano = ($this->t52_dtaqu_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_dtaqu_ano"]:$this->t52_dtaqu_ano);
         if($this->t52_dtaqu_dia != ""){
            $this->t52_dtaqu = $this->t52_dtaqu_ano."-".$this->t52_dtaqu_mes."-".$this->t52_dtaqu_dia;
         }
       }
       $this->t52_ident = ($this->t52_ident == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_ident"]:$this->t52_ident);
       $this->t52_descr = ($this->t52_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_descr"]:$this->t52_descr);
       $this->t52_obs = ($this->t52_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_obs"]:$this->t52_obs);
       $this->t52_depart = ($this->t52_depart == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_depart"]:$this->t52_depart);
       $this->t52_instit = ($this->t52_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_instit"]:$this->t52_instit);
       $this->t52_bensmarca = ($this->t52_bensmarca == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_bensmarca"]:$this->t52_bensmarca);
       $this->t52_bensmedida = ($this->t52_bensmedida == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_bensmedida"]:$this->t52_bensmedida);
       $this->t52_bensmodelo = ($this->t52_bensmodelo == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_bensmodelo"]:$this->t52_bensmodelo);
       $this->t52_foto = ($this->t52_foto == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_foto"]:$this->t52_foto);
       if($this->t52_dtinclusao == ""){
         $this->t52_dtinclusao_dia = ($this->t52_dtinclusao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_dtinclusao_dia"]:$this->t52_dtinclusao_dia);
         $this->t52_dtinclusao_mes = ($this->t52_dtinclusao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_dtinclusao_mes"]:$this->t52_dtinclusao_mes);
         $this->t52_dtinclusao_ano = ($this->t52_dtinclusao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_dtinclusao_ano"]:$this->t52_dtinclusao_ano);
         if($this->t52_dtinclusao_dia != ""){
            $this->t52_dtinclusao = $this->t52_dtinclusao_ano."-".$this->t52_dtinclusao_mes."-".$this->t52_dtinclusao_dia;
         }
       }
       $this->t52_processo = ($this->t52_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_processo"]:$this->t52_processo);
     }else{
       $this->t52_bem = ($this->t52_bem == ""?@$GLOBALS["HTTP_POST_VARS"]["t52_bem"]:$this->t52_bem);
     }
   }

    public function incluir($t52_bem)
    {
      $this->atualizacampos();
     if($this->t52_codcla == null ){
       $this->erro_sql = " Campo Classificação não informado.";
       $this->erro_campo = "t52_codcla";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_numcgm == null ){
       $this->erro_sql = " Campo Fornecedor não informado.";
       $this->erro_campo = "t52_numcgm";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_valaqu == null ){
       $this->erro_sql = " Campo Valor da aquisição não informado.";
       $this->erro_campo = "t52_valaqu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_dtaqu == null ){
       $this->erro_sql = " Campo Data da aquisição não informado.";
       $this->erro_campo = "t52_dtaqu_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_descr == null ){
       $this->erro_sql = " Campo Descrição do bem não informado.";
       $this->erro_campo = "t52_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_depart == null ){
       $this->erro_sql = " Campo Departamento não informado.";
       $this->erro_campo = "t52_depart";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_instit == null ){
       $this->erro_sql = " Campo Instituição não informado.";
       $this->erro_campo = "t52_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_bensmarca == null ){
       $this->erro_sql = " Campo Cód Marca não informado.";
       $this->erro_campo = "t52_bensmarca";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_bensmedida == null ){
       $this->erro_sql = " Campo Cód Medida não informado.";
       $this->erro_campo = "t52_bensmedida";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_bensmodelo == null ){
       $this->erro_sql = " Campo Cód Modelo não informado.";
       $this->erro_campo = "t52_bensmodelo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->t52_estorage == null ){
       $this->erro_sql = " Campo Bens E-storage não informado.";
       $this->erro_campo = "t52_estorage";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($t52_bem == "" || $t52_bem == null ){
       $result = db_query("select nextval('bens_t52_bem_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: bens_t52_bem_seq do campo: t52_bem";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->t52_bem = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from bens_t52_bem_seq");
       if(($result != false) && (pg_result($result,0,0) < $t52_bem)){
         $this->erro_sql = " Campo t52_bem maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->t52_bem = $t52_bem;
       }
     }
     if(($this->t52_bem == null) || ($this->t52_bem == "") ){
       $this->erro_sql = " Campo t52_bem não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into bens(
                                       t52_bem
                                      ,t52_codcla
                                      ,t52_numcgm
                                      ,t52_valaqu
                                      ,t52_dtaqu
                                      ,t52_ident
                                      ,t52_descr
                                      ,t52_obs
                                      ,t52_depart
                                      ,t52_instit
                                      ,t52_bensmarca
                                      ,t52_bensmedida
                                      ,t52_bensmodelo
                                      ,t52_foto
                                      ,t52_dtinclusao
                                      ,t52_processo
                                      ,t52_estorage
                       )
                values (
                                $this->t52_bem
                               ,$this->t52_codcla
                               ,$this->t52_numcgm
                               ,$this->t52_valaqu
                               ,".($this->t52_dtaqu == "null" || $this->t52_dtaqu == ""?"null":"'".$this->t52_dtaqu."'")."
                               ,'$this->t52_ident'
                               ,'$this->t52_descr'
                               ,'$this->t52_obs'
                               ,$this->t52_depart
                               ,$this->t52_instit
                               ,$this->t52_bensmarca
                               ,$this->t52_bensmedida
                               ,$this->t52_bensmodelo
                               ,".($this->t52_foto == "" || $this->t52_foto == null ? "NULL" : "$this->t52_foto")."                                
                               ,'".date('Y-m-d', db_getsession('DB_datausu'))."'
                               ,'$this->t52_processo'
                               ,'$this->t52_estorage'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Bens ($this->t52_bem) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Bens já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Bens ($this->t52_bem) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->t52_bem;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->t52_bem  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,5764,'$this->t52_bem','I')");
         $resac = db_query("insert into db_acount values($acount,914,5764,'','".AddSlashes(pg_result($resaco,0,'t52_bem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5766,'','".AddSlashes(pg_result($resaco,0,'t52_codcla'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5769,'','".AddSlashes(pg_result($resaco,0,'t52_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5770,'','".AddSlashes(pg_result($resaco,0,'t52_valaqu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5771,'','".AddSlashes(pg_result($resaco,0,'t52_dtaqu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5772,'','".AddSlashes(pg_result($resaco,0,'t52_ident'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5773,'','".AddSlashes(pg_result($resaco,0,'t52_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5774,'','".AddSlashes(pg_result($resaco,0,'t52_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,5775,'','".AddSlashes(pg_result($resaco,0,'t52_depart'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,9811,'','".AddSlashes(pg_result($resaco,0,'t52_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,13864,'','".AddSlashes(pg_result($resaco,0,'t52_bensmarca'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,13863,'','".AddSlashes(pg_result($resaco,0,'t52_bensmedida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,13862,'','".AddSlashes(pg_result($resaco,0,'t52_bensmodelo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,1011189,'','".AddSlashes(pg_result($resaco,0,'t52_foto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,1013490,'','".AddSlashes(pg_result($resaco,0,'t52_dtinclusao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,1015170,'','".AddSlashes(pg_result($resaco,0,'t52_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,914,1015526,'','".AddSlashes(pg_result($resaco,0,'t52_estorage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($t52_bem=null)
    {
      $this->atualizacampos();
     $sql = " update bens set ";
     $virgula = "";
     if(trim($this->t52_bem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_bem"])){
       $sql  .= $virgula." t52_bem = $this->t52_bem ";
       $virgula = ",";
       if(trim($this->t52_bem) == null ){
         $this->erro_sql = " Campo Código do bem não informado.";
         $this->erro_campo = "t52_bem";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_codcla)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_codcla"])){
       $sql  .= $virgula." t52_codcla = $this->t52_codcla ";
       $virgula = ",";
       if(trim($this->t52_codcla) == null ){
         $this->erro_sql = " Campo Classificação não informado.";
         $this->erro_campo = "t52_codcla";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_numcgm"])){
       $sql  .= $virgula." t52_numcgm = $this->t52_numcgm ";
       $virgula = ",";
       if(trim($this->t52_numcgm) == null ){
         $this->erro_sql = " Campo Fornecedor não informado.";
         $this->erro_campo = "t52_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_valaqu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_valaqu"])){
       $sql  .= $virgula." t52_valaqu = $this->t52_valaqu ";
       $virgula = ",";
       if(trim($this->t52_valaqu) == null ){
         $this->erro_sql = " Campo Valor da aquisição não informado.";
         $this->erro_campo = "t52_valaqu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_dtaqu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_dtaqu_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["t52_dtaqu_dia"] !="") ){
       $sql  .= $virgula." t52_dtaqu = '$this->t52_dtaqu' ";
       $virgula = ",";
       if(trim($this->t52_dtaqu) == null ){
         $this->erro_sql = " Campo Data da aquisição não informado.";
         $this->erro_campo = "t52_dtaqu_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["t52_dtaqu_dia"])){
         $sql  .= $virgula." t52_dtaqu = null ";
         $virgula = ",";
         if(trim($this->t52_dtaqu) == null ){
           $this->erro_sql = " Campo Data da aquisição não informado.";
           $this->erro_campo = "t52_dtaqu_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->t52_ident)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_ident"])){
       $sql  .= $virgula." t52_ident = '$this->t52_ident' ";
       $virgula = ",";
     }
     if(trim($this->t52_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_descr"])){
       $sql  .= $virgula." t52_descr = '$this->t52_descr' ";
       $virgula = ",";
       if(trim($this->t52_descr) == null ){
         $this->erro_sql = " Campo Descrição do bem não informado.";
         $this->erro_campo = "t52_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_obs"])){
       $sql  .= $virgula." t52_obs = '$this->t52_obs' ";
       $virgula = ",";
     }
     if(trim($this->t52_depart)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_depart"])){
       $sql  .= $virgula." t52_depart = $this->t52_depart ";
       $virgula = ",";
       if(trim($this->t52_depart) == null ){
         $this->erro_sql = " Campo Departamento não informado.";
         $this->erro_campo = "t52_depart";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_instit"])){
       $sql  .= $virgula." t52_instit = $this->t52_instit ";
       $virgula = ",";
       if(trim($this->t52_instit) == null ){
         $this->erro_sql = " Campo Instituição não informado.";
         $this->erro_campo = "t52_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_bensmarca)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_bensmarca"])){
       $sql  .= $virgula." t52_bensmarca = $this->t52_bensmarca ";
       $virgula = ",";
       if(trim($this->t52_bensmarca) == null ){
         $this->erro_sql = " Campo Cód Marca não informado.";
         $this->erro_campo = "t52_bensmarca";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_bensmedida)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_bensmedida"])){
       $sql  .= $virgula." t52_bensmedida = $this->t52_bensmedida ";
       $virgula = ",";
       if(trim($this->t52_bensmedida) == null ){
         $this->erro_sql = " Campo Cód Medida não informado.";
         $this->erro_campo = "t52_bensmedida";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_bensmodelo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_bensmodelo"])){
       $sql  .= $virgula." t52_bensmodelo = $this->t52_bensmodelo ";
       $virgula = ",";
       if(trim($this->t52_bensmodelo) == null ){
         $this->erro_sql = " Campo Cód Modelo não informado.";
         $this->erro_campo = "t52_bensmodelo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->t52_foto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_foto"])){
       $sql  .= $virgula." t52_foto = $this->t52_foto ";
       $virgula = ",";
     }
     if(trim($this->t52_processo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_processo"])){
       $sql  .= $virgula." t52_processo = '$this->t52_processo' ";
       $virgula = ",";
     }
     if(trim($this->t52_estorage)!="" || isset($GLOBALS["HTTP_POST_VARS"]["t52_estorage"])){
       $sql  .= $virgula." t52_estorage = '$this->t52_estorage' ";
       $virgula = ",";
       if(trim($this->t52_estorage) == null ){
         $this->erro_sql = " Campo Bens E-storage não informado.";
         $this->erro_campo = "t52_estorage";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($t52_bem!=null){
       $sql .= " t52_bem = $this->t52_bem";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->t52_bem));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5764,'$this->t52_bem','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_bem"]) || $this->t52_bem != "")
             $resac = db_query("insert into db_acount values($acount,914,5764,'".AddSlashes(pg_result($resaco,$conresaco,'t52_bem'))."','$this->t52_bem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_codcla"]) || $this->t52_codcla != "")
             $resac = db_query("insert into db_acount values($acount,914,5766,'".AddSlashes(pg_result($resaco,$conresaco,'t52_codcla'))."','$this->t52_codcla',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_numcgm"]) || $this->t52_numcgm != "")
             $resac = db_query("insert into db_acount values($acount,914,5769,'".AddSlashes(pg_result($resaco,$conresaco,'t52_numcgm'))."','$this->t52_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_valaqu"]) || $this->t52_valaqu != "")
             $resac = db_query("insert into db_acount values($acount,914,5770,'".AddSlashes(pg_result($resaco,$conresaco,'t52_valaqu'))."','$this->t52_valaqu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_dtaqu"]) || $this->t52_dtaqu != "")
             $resac = db_query("insert into db_acount values($acount,914,5771,'".AddSlashes(pg_result($resaco,$conresaco,'t52_dtaqu'))."','$this->t52_dtaqu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_ident"]) || $this->t52_ident != "")
             $resac = db_query("insert into db_acount values($acount,914,5772,'".AddSlashes(pg_result($resaco,$conresaco,'t52_ident'))."','$this->t52_ident',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_descr"]) || $this->t52_descr != "")
             $resac = db_query("insert into db_acount values($acount,914,5773,'".AddSlashes(pg_result($resaco,$conresaco,'t52_descr'))."','$this->t52_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_obs"]) || $this->t52_obs != "")
             $resac = db_query("insert into db_acount values($acount,914,5774,'".AddSlashes(pg_result($resaco,$conresaco,'t52_obs'))."','$this->t52_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_depart"]) || $this->t52_depart != "")
             $resac = db_query("insert into db_acount values($acount,914,5775,'".AddSlashes(pg_result($resaco,$conresaco,'t52_depart'))."','$this->t52_depart',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_instit"]) || $this->t52_instit != "")
             $resac = db_query("insert into db_acount values($acount,914,9811,'".AddSlashes(pg_result($resaco,$conresaco,'t52_instit'))."','$this->t52_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_bensmarca"]) || $this->t52_bensmarca != "")
             $resac = db_query("insert into db_acount values($acount,914,13864,'".AddSlashes(pg_result($resaco,$conresaco,'t52_bensmarca'))."','$this->t52_bensmarca',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_bensmedida"]) || $this->t52_bensmedida != "")
             $resac = db_query("insert into db_acount values($acount,914,13863,'".AddSlashes(pg_result($resaco,$conresaco,'t52_bensmedida'))."','$this->t52_bensmedida',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_bensmodelo"]) || $this->t52_bensmodelo != "")
             $resac = db_query("insert into db_acount values($acount,914,13862,'".AddSlashes(pg_result($resaco,$conresaco,'t52_bensmodelo'))."','$this->t52_bensmodelo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_foto"]) || $this->t52_foto != "")
             $resac = db_query("insert into db_acount values($acount,914,1011189,'".AddSlashes(pg_result($resaco,$conresaco,'t52_foto'))."','$this->t52_foto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_dtinclusao"]) || $this->t52_dtinclusao != "")
             $resac = db_query("insert into db_acount values($acount,914,1013490,'".AddSlashes(pg_result($resaco,$conresaco,'t52_dtinclusao'))."','$this->t52_dtinclusao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_processo"]) || $this->t52_processo != "")
             $resac = db_query("insert into db_acount values($acount,914,1015170,'".AddSlashes(pg_result($resaco,$conresaco,'t52_processo'))."','$this->t52_processo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["t52_estorage"]) || $this->t52_estorage != "")
             $resac = db_query("insert into db_acount values($acount,914,1015526,'".AddSlashes(pg_result($resaco,$conresaco,'t52_estorage'))."','$this->t52_estorage',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }

     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Bens não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->t52_bem;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Bens não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->t52_bem;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->t52_bem;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($t52_bem=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($t52_bem));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,5764,'$t52_bem','E')");
           $resac  = db_query("insert into db_acount values($acount,914,5764,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_bem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5766,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_codcla'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5769,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5770,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_valaqu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5771,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_dtaqu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5772,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_ident'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5773,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5774,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,5775,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_depart'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,9811,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,13864,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_bensmarca'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,13863,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_bensmedida'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,13862,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_bensmodelo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,1011189,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_foto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,1013490,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_dtinclusao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,1015170,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,914,1015526,'','".AddSlashes(pg_result($resaco,$iresaco,'t52_estorage'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from bens
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($t52_bem)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " t52_bem = $t52_bem ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Bens não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$t52_bem;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Bens não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$t52_bem;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$t52_bem;
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
        $this->erro_sql   = "Record Vazio na Tabela:bens";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($t52_bem = null,$campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos}";
        $sql .= " from bens ";
        $sql .= "      inner join cgm                on  cgm.z01_numcgm = bens.t52_numcgm";
        $sql .= "      inner join db_depart          on  db_depart.coddepto = bens.t52_depart";
        $sql .= "      inner join clabens            on  clabens.t64_codcla = bens.t52_codcla";

        $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
        $sql .= "                                    and clabensconplano.t86_anousu = ".db_getsession("DB_anousu");
        $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
        $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

        $sql .= "      left  join bensdiv            on bensdiv.t33_bem = bens.t52_bem";
        $sql .= "      left  join departdiv          on  departdiv.t30_codigo = bensdiv.t33_divisao";
        $sql .= "                                   and t30_depto  = db_depart.coddepto";
//    $sql .= "      left  join departdiv as b     on b.t30_depto  = db_depart.coddepto";
        $sql .= "      left  join histbem            on histbem.t56_codbem   = bens.t52_bem and histbem.t56_depart = bens.t52_depart";
        $sql .= "      left  join situabens          on situabens.t70_situac = histbem.t56_situac";
        $sql .= "      inner join bensmarca          on  bensmarca.t65_sequencial = bens.t52_bensmarca";
        $sql .= "      inner join bensmodelo         on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
        $sql .= "      inner join bensmedida         on  bensmedida.t67_sequencial = bens.t52_bensmedida";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($t52_bem)) {
                $sql2 .= " where bens.t52_bem = $t52_bem ";
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

    public function sql_query_file($t52_bem = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from bens ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($t52_bem)){
         $sql2 .= " where bens.t52_bem = $t52_bem ";
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

   function sql_query_bensContas($t52_bem=null,$campos="*",$ordem=null,$dbwhere="") {

    $sSql = "SELECT ";

    if ($campos != "*" ){

      $sCamposSql = explode("#",$campos);
      $sVirgula = "";

      for($i = 0; $i <sizeof ($sCamposSql); $i++) {

        $sSql .= $sVirgula.$sCamposSql[$i];
        $sVirgula = ",";
      }
    } else{
      $sSql .= $campos;
    }

    $sSql .= " FROM bens ";
    $sSql .= "      INNER JOIN clabens            on  clabens.t64_codcla          = bens.t52_codcla";
    $sSql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sSql .= "                                    and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
    $sSql .= "      inner join conplano           on  conplano.c60_codcon         = clabensconplano.t86_conplano";
    $sSql .= "                                    and conplano.c60_anousu         = ".db_getsession("DB_anousu");
    $sSql .= "      inner join bensdepreciacao    on  bensdepreciacao.t44_bens    = bens.t52_bem ";
    $sSql .= "      LEFT JOIN bensbaix            on  t55_codbem                  = t52_bem";


    //Divisao
    $sSql .= "      left  join bensdiv            on bensdiv.t33_bem              = bens.t52_bem";
    $sSql .= "      left  join departdiv          on departdiv.t30_codigo         = bensdiv.t33_divisao and departdiv.t30_depto = bens.t52_depart";
    $sSql .= "      left  join bensplaca          on bensplaca.t41_bem            = bens.t52_bem";


    $sSql2 = "";

    if($dbwhere==""){

      if($t52_bem!=null ){
        $sSql2 .= " where bens.t52_bem = $t52_bem ";
      }
    }else if($dbwhere != ""){
      $sSql2 = " where $dbwhere";
    }

    $sSql .= $sSql2;

    if($ordem != null ){

      $sSql .= " order by ";
      $sCamposSql = explode("#",$ordem);
      $sVirgula = "";

      for($i = 0;$i < sizeof($sCamposSql);$i++){

        $sSql .= $sVirgula.$sCamposSql[$i];
        $sVirgula = ",";
      }
    }
    return $sSql;
  }
   function sql_query_histbem ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from bens ";
     $sql .= "      inner join cgm              on cgm.z01_numcgm              = bens.t52_numcgm";
     $sql .= "      inner join clabens          on clabens.t64_codcla          = bens.t52_codcla";
     $sql .= "      left  join benstransfcodigo on benstransfcodigo.t95_codbem = bens.t52_bem";
     $sql .= "      inner join situabens        on situabens.t70_situac        = benstransfcodigo.t95_situac";
     $sql .= "      left  join histbemtrans     on histbemtrans.t97_codtran    = benstransfcodigo.t95_codtran";
     $sql .= "      left  join benstransf       on benstransf.t93_codtran      = benstransfcodigo.t95_codtran";
     $sql .= "      left  join benstransfdiv    on benstransfdiv.t31_codtran   = benstransf.t93_codtran and";
     $sql .= "                                     benstransfdiv.t31_bem       = bens.t52_bem";
     $sql .= "      left  join departdiv        on departdiv.t30_codigo        = benstransfdiv.t31_divisao";
     $sql .= "      left  join db_depart        on db_depart.coddepto          = departdiv.t30_depto";
     $sql .= "";
     $sql2 = "";
     if($dbwhere==""){
       if($t52_bem!=null ){
         $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_depart ($coddepto=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from db_depart ";
     $sql2 = "";
     if($dbwhere==""){
       if($coddepto!=null ){
         $sql2 .= " where db_depart.coddepto = $coddepto ";
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
   function sql_query_bens ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from bens ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = bens.t52_numcgm";
     $sql .= "      inner join db_config  on  db_config.codigo = bens.t52_instit";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
     $sql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla";
     $sql .= "      inner join bensmarca  on  bensmarca.t65_sequencial = bens.t52_bensmarca";
     $sql .= "      inner join bensmodelo  on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
     $sql .= "      inner join bensmedida  on  bensmedida.t67_sequencial = bens.t52_bensmedida";
     $sql .= "      inner join cgm as c on  c.z01_numcgm = db_config.numcgm";
     $sql .= "      inner join db_config  as a on   a.codigo = db_depart.instit";
     $sql2 = "";
     if($dbwhere==""){
       if($t52_bem!=null ){
         $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_convenio ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm        on  cgm.z01_numcgm = bens.t52_numcgm";
    $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
    $sql .= "      inner join clabens    on  clabens.t64_codcla = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left  join bensdiv        on  bensdiv.t33_bem = bens.t52_bem";
    $sql .= "      left  join departdiv      on  departdiv.t30_codigo = bensdiv.t33_divisao";
    $sql .= "      left  join departdiv as b on  b.t30_depto  = db_depart.coddepto";
    $sql .= "      left  join histbem        on  histbem.t56_codbem   = bens.t52_bem and histbem.t56_depart = bens.t52_depart";
    $sql .= "      left  join situabens      on  situabens.t70_situac = histbem.t56_situac";
    $sql .= "      inner join bensmarca      on  bensmarca.t65_sequencial = bens.t52_bensmarca";
    $sql .= "      inner join bensmodelo     on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
    $sql .= "      inner join bensmedida     on  bensmedida.t67_sequencial = bens.t52_bensmedida";
    $sql .= "			inner join benscedente     on  t09_bem = bens.t52_bem";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_left_convenio ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = bens.t52_numcgm";
    $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
    $sql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left  join bensdiv        on  bensdiv.t33_bem = bens.t52_bem";
    $sql .= "      left  join departdiv      on  departdiv.t30_codigo = bensdiv.t33_divisao";
    $sql .= "      left  join departdiv as b on  b.t30_depto  = db_depart.coddepto";
    $sql .= "      left  join histbem        on  histbem.t56_codbem   = bens.t52_bem and histbem.t56_depart = bens.t52_depart";
    $sql .= "      left  join situabens      on  situabens.t70_situac = histbem.t56_situac";
    $sql .= "      inner join bensmarca      on  bensmarca.t65_sequencial = bens.t52_bensmarca";
    $sql .= "      inner join bensmodelo     on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
    $sql .= "      inner join bensmedida     on  bensmedida.t67_sequencial = bens.t52_bensmedida";
    $sql .= "      inner join db_departorg   on  db_departorg.db01_coddepto = bens.t52_depart ";
    $sql .= "			 inner join orcorgao       on  orcorgao.o40_orgao = db_departorg.db01_orgao ";
    $sql .= "			                               and orcorgao.o40_anousu = db_departorg.db01_anousu ";
    $sql .= "			 inner join orcunidade on orcunidade.o41_unidade = db_departorg.db01_unidade ";
    $sql .= "			                               and orcunidade.o41_orgao = db_departorg.db01_orgao";
    $sql .= "			                               and orcunidade.o41_anousu = db_departorg.db01_anousu";
    $sql .= "			left join benscedente      on  t09_bem = bens.t52_bem";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
  *
  * Busca:
  * Bens, Placa do Bem, Classificação e Se tem ou não depreciação
  *
  * @return String SQL
  */
 function sql_query_left_depreciacao ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere="") {
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
     $sql .= " from bens 																																					";
     $sql .= "      inner join clabens         on clabens.t64_codcla 			  = bens.t52_codcla			";
     $sql .= "      inner join bensmarca       on bensmarca.t65_sequencial  = bens.t52_bensmarca	";
     $sql .= "      inner join bensmodelo      on bensmodelo.t66_sequencial = bens.t52_bensmodelo	";
     $sql .= "      inner join bensmedida      on bensmedida.t67_sequencial = bens.t52_bensmedida	";
     $sql .= "      inner join bensplaca       on bensplaca.t41_bem 				= bens.t52_bem        ";
     $sql .= "       left join bensdepreciacao on bensdepreciacao.t44_bens  = bens.t52_bem        ";
     $sql .= "       left join bensbaix        on bensbaix.t55_codbem       = bens.t52_bem        ";
     $sql2 = "";
     if($dbwhere==""){
       if($t52_bem!=null ){
         $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_benstransf ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere="",$infowhere=""){
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

    if ($infowhere=="tudo"){
      $whereinfo = " ) as x on t95_codbem = t52_bem";
    } else {
      $whereinfo = " where coalesce(t96_codtran,0) = 0) as x on t95_codbem = t52_bem";
    }

    $sql .= " from bens ";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = bens.t52_numcgm";
    $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
    $sql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon         = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu         = ".db_getsession("DB_anousu");

    $sql .= "      left  join (select t95_codbem";
    $sql .= "                       from benstransfcodigo";
    $sql .= "                                 left join benstransfconf on t95_codtran = t96_codtran";
    $sql .= $whereinfo;
    //   $sql .= "                       where coalesce(t96_codtran,0) = 0) as x on t95_codbem = t52_bem";
    $sql .= "      left  join bensguardaitem  on  bens.t52_bem = bensguardaitem.t22_bem";
    $sql .= "      left  join bensguardaitemdev  on  bensguardaitemdev.t23_guardaitem = bensguardaitem.t22_codigo";
    $sql .= "      left  join bensbaix on bensbaix.t55_codbem = bens.t52_bem";
    $and = "";
    $sql2 = "";
    if($infowhere!="" || $dbwhere!="" || ($dbwhere=="" && $dbwhere!=null)){
      $sql .= " where ";
    }
    if($infowhere!=""){
      if($infowhere=="true"){
        $sql2 = " coalesce(t95_codbem,0) = 0 ";
      }else if($infowhere=="false"){
        $sql2 = " coalesce(t95_codbem,0) <> 0 ";
      }
      if($dbwhere!="" || ($dbwhere=="" && $dbwhere!=null)){
        if ($infowhere!="tudo"){
          $and = " and ";
        } else {
          $and = "";
        }
      }
    }
    $sql .= $sql2;
    $sql3 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql3 .= " $and  bens.t52_bem = $t52_bem ";
      }
    }else if($dbwhere != ""){
      $sql3 = " $and  $dbwhere";
    }
    $sql .= $sql3;
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
   function sql_query_transf( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm             on cgm.z01_numcgm           = bens.t52_numcgm";
    $sql .= "      inner join db_depart       on db_depart.coddepto       = bens.t52_depart";
    $sql .= "      inner join clabens         on clabens.t64_codcla       = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left join bensbaix         on bensbaix.t55_codbem      = bens.t52_bem";
    $sql .= "      left join bensmater        on bensmater.t53_codbem     = bens.t52_bem";
    $sql .= "      left join bensimoveis      on bensimoveis.t54_codbem   = bens.t52_bem                 ";
    $sql .= "      left join bensdiv          on bensdiv.t33_bem          = bens.t52_bem                 ";
    $sql .= "      left join departdiv        on bensdiv.t33_divisao      = departdiv.t30_codigo         ";
    $sql .= "      left join benstransfcodigo on t95_codbem               = bens.t52_bem                 ";
    $sql .= "      left join benstransfconf   on t96_codtran              = benstransfcodigo.t95_codtran ";

    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_dados_depreciacao ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere="") {
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm                on  cgm.z01_numcgm = bens.t52_numcgm";
    $sql .= "      inner join db_depart          on  db_depart.coddepto = bens.t52_depart";
    $sql .= "      inner join clabens            on  clabens.t64_codcla = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left  join bensdiv     on bensdiv.t33_bem = bens.t52_bem";
    $sql .= "      left  join departdiv   on  departdiv.t30_codigo = bensdiv.t33_divisao and departdiv.t30_depto = bens.t52_depart";
    //$sql .= "      left  join departdiv as b on b.t30_depto  = db_depart.coddepto and departdiv.t30_depto = bens.t52_depart";

    $sql .= "      inner join bensmarca   on  bensmarca.t65_sequencial = bens.t52_bensmarca";
    $sql .= "      inner join bensmodelo  on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
    $sql .= "      inner join bensmedida  on  bensmedida.t67_sequencial = bens.t52_bensmedida";
    $sql .= "      left join bensbaix     on  bensbaix.t55_codbem = bens.t52_bem";
    $sql .= "      left join benscedente     on t09_bem   = bens.t52_bem";
    $sql .= "      left join bensdepreciacao on t44_bens  = bens.t52_bem";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_dados_bem($t52_bem=null,$campos="*",$ordem=null,$dbwhere="") {

    $sSql = "SELECT ";

    if ($campos != "*" ){

      $sCamposSql = explode("#",$campos);
      $sVirgula = "";

      for($i = 0; $i <sizeof ($sCamposSql); $i++) {

        $sSql .= $sVirgula.$sCamposSql[$i];
        $sVirgula = ",";
      }
    } else{
      $sSql .= $campos;
    }

    $sSql  .= "   from bens                                                                                             ";
    $sSql  .= "        inner join db_departorg    on db_departorg.db01_coddepto    = bens.t52_depart                    ";
    $sSql  .= "        inner join orcorgao        on orcorgao.o40_orgao            = db_departorg.db01_orgao            ";
    $sSql  .= "                                  and orcorgao.o40_anousu           = db_departorg.db01_anousu           ";
    $sSql  .= "        inner join orcunidade      on orcunidade.o41_unidade        = db_departorg.db01_unidade          ";
    $sSql  .= "                                  and orcunidade.o41_anousu         = db_departorg.db01_anousu           ";
    $sSql  .= "        inner join db_depart       on db_depart.coddepto            = db_departorg.db01_coddepto         ";
    $sSql  .= "        left  join departdiv       on departdiv.t30_depto           = db_depart.coddepto                 ";
    $sSql  .= "        left  join bensdiv         on bensdiv.t33_divisao           = departdiv.t30_codigo               ";
    $sSql  .= "                                  and bensdiv.t33_bem               = bens.t52_bem                       ";
    $sSql  .= "        inner join clabens         on clabens.t64_codcla            = bens.t52_codcla                    ";
    $sSql  .= "        inner join histbem         on histbem.t56_codbem            = bens.t52_bem                       ";
    $sSql  .= "        inner join situabens       on situabens.t70_situac          = histbem.t56_situac                 ";
    $sSql  .= "        left  join bensdepreciacao on bensdepreciacao.t44_bens      = bens.t52_bem                       ";
    $sSql  .= "        left  join inventariobem   on inventariobem.t77_bens        = bens.t52_bem                       ";
    $sSql  .= "        left  join inventario      on inventario.t75_sequencial     = inventariobem.t77_inventario       ";
    $sSql  .= "        left  join bensplaca       on bensplaca.t41_bem             = bens.t52_bem                       ";
    $sSql  .= "        left  join benscedente     on benscedente.t09_bem           = bens.t52_bem                       ";
    $sSql  .= "        left  join benscadcedente  on benscadcedente.t04_sequencial = benscedente.t09_benscadcedente     ";
    $sSql  .= "        left  join bensbaix        on bensbaix.t55_codbem           = bens.t52_bem                       ";
    $sSql  .= "        left  join bensimoveis     on bensimoveis.t54_codbem        = bens.t52_bem                       ";
    $sSql  .= "        left  join bensmater       on bensmater.t53_codbem          = bens.t52_bem                       ";
    $sSql2 = "";

    if($dbwhere==""){

      if($t52_bem!=null ){
        $sSql2 .= " where bens.t52_bem = $t52_bem ";
      }
  		}else if($dbwhere != ""){
  		  $sSql2 = " where $dbwhere";
  		}

  		$sSql .= $sSql2;

  		if($ordem != null ){

  		  $sSql .= " order by ";
  		  $sCamposSql = explode("#",$ordem);
  		  $sVirgula = "";

  		  for($i = 0;$i < sizeof($sCamposSql);$i++){

  		    $sSql .= $sVirgula.$sCamposSql[$i];
  		    $sVirgula = ",";

  		  }
  		}
  		return $sSql;
  }
   function sql_query_dados_bem_inventario($t52_bem=null,$campos="*",$ordem=null,$dbwhere="", $iAno=null) {

    $sSql = "select ";

    if ($campos != "*" ){

      $sCamposSql = explode("#",$campos);
      $sVirgula = "";

      for($i = 0; $i <sizeof ($sCamposSql); $i++) {

        $sSql .= $sVirgula.$sCamposSql[$i];
        $sVirgula = ",";
      }
    } else{
      $sSql .= $campos;
    }

    if (empty($iAno)) {
      $iAno = db_getsession("DB_anousu");
    }

    $sSql .= "  from bens                                                                                              ";
    $sSql .= "       inner join bensplaca                on bensplaca.t41_bem            = bens.t52_bem                ";
    $sSql .= "       inner join db_depart depart_origem  on depart_origem.coddepto       = bens.t52_depart             ";
    $sSql .= "       left  join departdiv div_origem     on div_origem.t30_depto         = depart_origem.coddepto      ";
    $sSql .= "       inner join inventariobem            on inventariobem.t77_bens       = bens.t52_bem                ";
    $sSql .= "       inner join db_depart depart_destino on depart_destino.coddepto      = inventariobem.t77_db_depart ";
    $sSql .= "       left  join departdiv div_destino    on div_destino.t30_codigo       = inventariobem.t77_departdiv ";
    $sSql .= "       inner join situabens                on situabens.t70_situac         = inventariobem.t77_situabens ";
    $sSql .= "        inner join db_departorg            on db_departorg.db01_coddepto = depart_origem.coddepto    ";
    $sSql .= "                                          and db_departorg.db01_anousu   = {$iAno}             ";
    $sSql .= "        inner join orcorgao                on orcorgao.o40_orgao         = db_departorg.db01_orgao   ";
    $sSql .= "                                          and orcorgao.o40_anousu        = {$iAno}             ";
    $sSql .= "        inner join orcunidade              on orcunidade.o41_orgao       = orcorgao.o40_orgao        ";
    $sSql .= "                                          and orcunidade.o41_anousu      = {$iAno}             ";
    $sSql2 = "";

    if($dbwhere==""){

      if($t52_bem!=null ){
        $sSql2 .= " where bens.t52_bem = $t52_bem ";
      }
    }else if($dbwhere != ""){
      $sSql2 = " where $dbwhere";
    }

    $sSql .= $sSql2;

    if($ordem != null ){

      $sSql .= " order by ";
      $sCamposSql = explode("#",$ordem);
      $sVirgula = "";

      for($i = 0;$i < sizeof($sCamposSql);$i++){

        $sSql .= $sVirgula.$sCamposSql[$i];
        $sVirgula = ",";

      }
    }
    return $sSql;
  }
   function sql_query_class ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm            on  cgm.z01_numcgm = bens.t52_numcgm";
    $sql .= "      inner join db_depart      on  db_depart.coddepto = bens.t52_depart";
    $sql .= "      inner join clabens        on  clabens.t64_codcla = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left  join bensdiv        on bensdiv.t33_bem = bens.t52_bem";
    $sql .= "      left  join departdiv      on  departdiv.t30_codigo = bensdiv.t33_divisao and departdiv.t30_depto = bens.t52_depart";
    //$sql .= "      left  join departdiv as b on b.t30_depto  = db_depart.coddepto and departdiv.t30_depto = bens.t52_depart";

    $sql .= "      left  join histbem        on histbem.t56_codbem   = bens.t52_bem and histbem.t56_depart = bens.t52_depart";
    $sql .= "      left  join situabens      on situabens.t70_situac = histbem.t56_situac";
    $sql .= "      inner join bensmarca      on  bensmarca.t65_sequencial = bens.t52_bensmarca";
    $sql .= "      inner join bensmodelo     on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
    $sql .= "      inner join bensmedida     on  bensmedida.t67_sequencial = bens.t52_bensmedida";
    $sql .= "      left join bensbaix        on  bensbaix.t55_codbem = bens.t52_bem";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_termo ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm        on  cgm.z01_numcgm        = bens.t52_numcgm";
    $sql .= "      inner join db_depart  on  db_depart.coddepto    = bens.t52_depart";
    $sql .= "      inner join clabens    on  clabens.t64_codcla    = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left join bensbaix    on bensbaix.t55_codbem    = bens.t52_bem ";
    $sql .= "      left join bensmater   on bensmater.t53_codbem   = bens.t52_bem";
    $sql .= "      left join bensimoveis on bensimoveis.t54_codbem = bens.t52_bem";
    $sql .= "      left  join bensdiv    on bensdiv.t33_bem        = bens.t52_bem";
    $sql .= "      left  join departdiv  on departdiv.t30_codigo   = bensdiv.t33_divisao
    and departdiv.t30_depto    = bens.t52_depart";
    $sql .= "      inner join db_departorg on db_departorg.db01_coddepto = bens.t52_depart
    and db_departorg.db01_anousu   = ".db_getsession("DB_anousu");
    $sql .= "      inner join orcorgao   on orcorgao.o40_orgao     = db_departorg.db01_orgao ";
    $sql .= "                           and orcorgao.o40_anousu    = db_departorg.db01_anousu ";
    $sql .= "      inner join orcunidade on orcunidade.o41_unidade = db_departorg.db01_unidade ";
    $sql .= "                           and orcunidade.o41_orgao = db_departorg.db01_orgao";
    $sql .= "                           and orcunidade.o41_anousu = db_departorg.db01_anousu";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_tipodepreciacao ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){

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
    $sql .= " from bens ";
    $sql .= "   inner join bensdepreciacao on t52_bem = t44_bens";
    $sql2 = "";

    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_querybensdepto ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm        on cgm.z01_numcgm         = bens.t52_numcgm";
    $sql .= "      inner join db_depart  on db_depart.coddepto     = bens.t52_depart";
    $sql .= "      inner join clabens    on clabens.t64_codcla     = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left join bensbaix    on bensbaix.t55_codbem    = bens.t52_bem";
    $sql .= "      left join bensmater   on bensmater.t53_codbem   = bens.t52_bem";
    $sql .= "      left join bensimoveis on bensimoveis.t54_codbem = bens.t52_bem";
    $sql .= "      left join bensdiv     on bensdiv.t33_bem        = bens.t52_bem";
    $sql .= "      left join departdiv   on bensdiv.t33_divisao    = departdiv.t30_codigo";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_orgao ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $iAnoSessao = db_getsession("DB_anousu");
    $sql .= " from bens ";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = bens.t52_numcgm";
    $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
    $sql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu  = {$iAnoSessao}";
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu  = {$iAnoSessao}";
    $sql .= "      left  join bensdiv    on bensdiv.t33_bem = bens.t52_bem";
    $sql .= "      left  join departdiv  on  departdiv.t30_codigo = bensdiv.t33_divisao and departdiv.t30_depto = bens.t52_depart";
    //$sql .= "      left  join departdiv as b on b.t30_depto  = db_depart.coddepto and departdiv.t30_depto = bens.t52_depart";
    $sql .= "      left  join histbem    on histbem.t56_codbem   = bens.t52_bem and histbem.t56_depart = bens.t52_depart";
    $sql .= "      left  join situabens  on situabens.t70_situac = histbem.t56_situac";
    $sql .= "      inner join bensmarca  on  bensmarca.t65_sequencial = bens.t52_bensmarca";
    $sql .= "      inner join bensmodelo  on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
    $sql .= "      inner join bensmedida  on  bensmedida.t67_sequencial = bens.t52_bensmedida";
    $sql .= "      inner join db_departorg on db_departorg.db01_coddepto = bens.t52_depart and db_departorg.db01_anousu = ".db_getsession("DB_anousu");
    $sql .= "			inner join orcorgao on orcorgao.o40_orgao = db_departorg.db01_orgao ";
    $sql .= "			and orcorgao.o40_anousu = db_departorg.db01_anousu ";
    $sql .= "			inner join orcunidade on orcunidade.o41_unidade = db_departorg.db01_unidade ";
    $sql .= "			and orcunidade.o41_orgao = db_departorg.db01_orgao";
    $sql .= "			and orcunidade.o41_anousu = db_departorg.db01_anousu";
    $sql .= "      left join bensbaix on bensbaix.t55_codbem = bens.t52_bem ";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
   function sql_query_orgao_convenio ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from bens ";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = bens.t52_numcgm";
    $sql .= "      inner join db_depart  on  db_depart.coddepto = bens.t52_depart";
    $sql .= "      inner join clabens  on  clabens.t64_codcla = bens.t52_codcla";

    $sql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
    $sql .= "                                    and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
    $sql .= "      inner join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
    $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

    $sql .= "      left  join bensdiv    on bensdiv.t33_bem = bens.t52_bem";
    $sql .= "      left  join departdiv  on  departdiv.t30_codigo = bensdiv.t33_divisao";
    $sql .= "      left  join departdiv as b on b.t30_depto  = db_depart.coddepto";
    $sql .= "      left  join histbem    on histbem.t56_codbem   = bens.t52_bem and histbem.t56_depart = bens.t52_depart";
    $sql .= "      left  join situabens  on situabens.t70_situac = histbem.t56_situac";
    $sql .= "      inner join bensmarca  on  bensmarca.t65_sequencial = bens.t52_bensmarca";
    $sql .= "      inner join bensmodelo  on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
    $sql .= "      inner join bensmedida  on  bensmedida.t67_sequencial = bens.t52_bensmedida";
    $sql .= "      inner join db_departorg on db_departorg.db01_coddepto = bens.t52_depart ";
    $sql .= "			inner join orcorgao on orcorgao.o40_orgao = db_departorg.db01_orgao ";
    $sql .= "			and orcorgao.o40_anousu = db_departorg.db01_anousu ";
    $sql .= "			inner join orcunidade on orcunidade.o41_unidade = db_departorg.db01_unidade ";
    $sql .= "			and orcunidade.o41_orgao = db_departorg.db01_orgao";
    $sql .= "			and orcunidade.o41_anousu = db_departorg.db01_anousu";
    $sql .= "			inner join benscedente on t09_bem = bens.t52_bem";
    $sql2 = "";
    if($dbwhere==""){
      if($t52_bem!=null ){
        $sql2 .= " where bens.t52_bem = $t52_bem ";
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
    function sql_query_bensContasAnexo($t52_bem=null,$campos="*",$ordem=null,$dbwhere="") {

        $sSql = "SELECT ";

        if ($campos != "*" ){

            $sCamposSql = explode("#",$campos);
            $sVirgula = "";

            for($i = 0; $i <sizeof ($sCamposSql); $i++) {

                $sSql .= $sVirgula.$sCamposSql[$i];
                $sVirgula = ",";
            }
        } else{
            $sSql .= $campos;
        }

        $sSql .= " FROM bens ";
        $sSql .= "      INNER JOIN clabens            on  clabens.t64_codcla          = bens.t52_codcla";
        $sSql .= "      inner join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
        $sSql .= "                                    and clabensconplano.t86_anousu  = ".db_getsession("DB_anousu");
        $sSql .= "      inner join conplano           on  conplano.c60_codcon         = clabensconplano.t86_conplano";
        $sSql .= "                                    and conplano.c60_anousu         = ".db_getsession("DB_anousu");
        $sSql .= "      LEFT JOIN bensbaix            on  t55_codbem                  = t52_bem";

        //Divisao
        $sSql .= "      left  join bensdiv            on bensdiv.t33_bem              = bens.t52_bem";
        $sSql .= "      left  join departdiv          on departdiv.t30_codigo         = bensdiv.t33_divisao and departdiv.t30_depto = bens.t52_depart";


        $sSql2 = "";

        if($dbwhere==""){

            if($t52_bem!=null ){
                $sSql2 .= " where bens.t52_bem = $t52_bem ";
            }
        }else if($dbwhere != ""){
            $sSql2 = " where $dbwhere";
        }

        $sSql .= $sSql2;

        if($ordem != null ){

            $sSql .= " order by ";
            $sCamposSql = explode("#",$ordem);
            $sVirgula = "";

            for($i = 0;$i < sizeof($sCamposSql);$i++){

                $sSql .= $sVirgula.$sCamposSql[$i];
                $sVirgula = ",";
            }
        }
        return $sSql;
    }


    function sql_query_func_pesquisa ( $t52_bem=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }


        $sql .= " from bens ";
        $sql .= "      inner join cgm                on  cgm.z01_numcgm = bens.t52_numcgm";
        $sql .= "      inner join db_depart          on  db_depart.coddepto = bens.t52_depart";
        $sql .= "      inner join clabens            on  clabens.t64_codcla = bens.t52_codcla";

        $sql .= "      left join clabensconplano    on  clabensconplano.t86_clabens = clabens.t64_codcla";
        $sql .= "                                    and clabensconplano.t86_anousu = ".db_getsession("DB_anousu");
        $sql .= "      left join conplano           on  conplano.c60_codcon  = clabensconplano.t86_conplano";
        $sql .= "                                    and conplano.c60_anousu = ".db_getsession("DB_anousu");

        $sql .= "      left  join bensdiv            on bensdiv.t33_bem = bens.t52_bem";
        $sql .= "      left  join departdiv          on  departdiv.t30_codigo = bensdiv.t33_divisao";
        $sql .= "                                   and t30_depto  = db_depart.coddepto";
        $sql .= "      left  join histbem            on histbem.t56_codbem   = bens.t52_bem and histbem.t56_depart = bens.t52_depart";
        $sql .= "      left  join situabens          on situabens.t70_situac = histbem.t56_situac";
        $sql .= "      inner join bensmarca          on  bensmarca.t65_sequencial = bens.t52_bensmarca";
        $sql .= "      inner join bensmodelo         on  bensmodelo.t66_sequencial = bens.t52_bensmodelo";
        $sql .= "      inner join bensmedida         on  bensmedida.t67_sequencial = bens.t52_bensmedida";
        $sql2 = "";
        if($dbwhere==""){
            if($t52_bem!=null ){
                $sql2 .= " where bens.t52_bem = $t52_bem ";
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

    public function getSqlValoresAtuaisBem($idBem)
    {
        $sql = "
      SELECT
        coalesce(t58_valoratual, 0) as t58_valoratual,
        t44_valoratual,
        t44_valorresidual
      FROM
        bens
      LEFT JOIN bensdepreciacao
        ON t44_bens = bens.t52_bem
      LEFT JOIN (
        SELECT
          t58_valoratual,
          t58_valorresidual,
          t58_valoranterior,
          t58_bens
        FROM
          benshistoricocalculobem
        INNER JOIN benshistoricocalculo
          ON t57_sequencial = t58_benshistoricocalculo
        WHERE
          t58_bens = {$idBem}
        ORDER BY
          t57_ano DESC,
          t57_mes DESC,
          t58_sequencial DESC
        LIMIT 1
      ) estado_atual ON estado_atual.t58_bens = t52_bem
      WHERE
        bens.t52_bem = {$idBem}
      ORDER BY
        t44_ultimaavaliacao DESC;
    ";
        return $sql;
    }
}
