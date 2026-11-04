<?php

class cl_necessidadetipoatendimentoaluno
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
    public $ed188_sequencial = 0; 
    public $ed188_aluno = 0; 
    public $ed188_necessidadetipoatendimento = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 ed188_sequencial = int4 = Sequencial 
                 ed188_aluno = int4 = Aluno 
                 ed188_necessidadetipoatendimento = int4 = Tipo de Atendimento 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("necessidadetipoatendimentoaluno"); 
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
       $this->ed188_sequencial = ($this->ed188_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed188_sequencial"]:$this->ed188_sequencial);
       $this->ed188_aluno = ($this->ed188_aluno == ""?@$GLOBALS["HTTP_POST_VARS"]["ed188_aluno"]:$this->ed188_aluno);
       $this->ed188_necessidadetipoatendimento = ($this->ed188_necessidadetipoatendimento == ""?@$GLOBALS["HTTP_POST_VARS"]["ed188_necessidadetipoatendimento"]:$this->ed188_necessidadetipoatendimento);
     }else{
       $this->ed188_sequencial = ($this->ed188_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed188_sequencial"]:$this->ed188_sequencial);
     }
   }

    public function incluir($ed188_sequencial)
    {
      $this->atualizacampos();
     if($this->ed188_aluno == null ){ 
       $this->erro_sql = " Campo Aluno não informado.";
       $this->erro_campo = "ed188_aluno";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed188_necessidadetipoatendimento == null ){ 
       $this->erro_sql = " Campo Tipo de Atendimento não informado.";
       $this->erro_campo = "ed188_necessidadetipoatendimento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed188_sequencial == "" || $ed188_sequencial == null ){
       $result = db_query("select nextval('necessidadetipoatendimentoaluno_ed188_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: necessidadetipoatendimentoaluno_ed188_sequencial_seq do campo: ed188_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ed188_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from necessidadetipoatendimentoaluno_ed188_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed188_sequencial)){
         $this->erro_sql = " Campo ed188_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed188_sequencial = $ed188_sequencial; 
       }
     }
     if(($this->ed188_sequencial == null) || ($this->ed188_sequencial == "") ){ 
       $this->erro_sql = " Campo ed188_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into necessidadetipoatendimentoaluno(
                                       ed188_sequencial 
                                      ,ed188_aluno 
                                      ,ed188_necessidadetipoatendimento 
                       )
                values (
                                $this->ed188_sequencial 
                               ,$this->ed188_aluno 
                               ,$this->ed188_necessidadetipoatendimento 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Topo atendimento necessidade aluno ($this->ed188_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Topo atendimento necessidade aluno já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Topo atendimento necessidade aluno ($this->ed188_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed188_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed188_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1014756,'$this->ed188_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1011026,1014756,'','".AddSlashes(pg_result($resaco,0,'ed188_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011026,1014758,'','".AddSlashes(pg_result($resaco,0,'ed188_aluno'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1011026,1014757,'','".AddSlashes(pg_result($resaco,0,'ed188_necessidadetipoatendimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($ed188_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update necessidadetipoatendimentoaluno set ";
     $virgula = "";
     if(trim($this->ed188_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed188_sequencial"])){ 
       $sql  .= $virgula." ed188_sequencial = $this->ed188_sequencial ";
       $virgula = ",";
       if(trim($this->ed188_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ed188_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed188_aluno)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed188_aluno"])){ 
       $sql  .= $virgula." ed188_aluno = $this->ed188_aluno ";
       $virgula = ",";
       if(trim($this->ed188_aluno) == null ){ 
         $this->erro_sql = " Campo Aluno não informado.";
         $this->erro_campo = "ed188_aluno";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed188_necessidadetipoatendimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed188_necessidadetipoatendimento"])){ 
       $sql  .= $virgula." ed188_necessidadetipoatendimento = $this->ed188_necessidadetipoatendimento ";
       $virgula = ",";
       if(trim($this->ed188_necessidadetipoatendimento) == null ){ 
         $this->erro_sql = " Campo Tipo de Atendimento não informado.";
         $this->erro_campo = "ed188_necessidadetipoatendimento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed188_sequencial!=null){
       $sql .= " ed188_sequencial = $this->ed188_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed188_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1014756,'$this->ed188_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed188_sequencial"]) || $this->ed188_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1011026,1014756,'".AddSlashes(pg_result($resaco,$conresaco,'ed188_sequencial'))."','$this->ed188_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed188_aluno"]) || $this->ed188_aluno != "")
             $resac = db_query("insert into db_acount values($acount,1011026,1014758,'".AddSlashes(pg_result($resaco,$conresaco,'ed188_aluno'))."','$this->ed188_aluno',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed188_necessidadetipoatendimento"]) || $this->ed188_necessidadetipoatendimento != "")
             $resac = db_query("insert into db_acount values($acount,1011026,1014757,'".AddSlashes(pg_result($resaco,$conresaco,'ed188_necessidadetipoatendimento'))."','$this->ed188_necessidadetipoatendimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Topo atendimento necessidade aluno não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed188_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Topo atendimento necessidade aluno não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed188_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed188_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($ed188_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ed188_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1014756,'$ed188_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1011026,1014756,'','".AddSlashes(pg_result($resaco,$iresaco,'ed188_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011026,1014758,'','".AddSlashes(pg_result($resaco,$iresaco,'ed188_aluno'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1011026,1014757,'','".AddSlashes(pg_result($resaco,$iresaco,'ed188_necessidadetipoatendimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from necessidadetipoatendimentoaluno
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ed188_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ed188_sequencial = $ed188_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Topo atendimento necessidade aluno não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed188_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Topo atendimento necessidade aluno não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed188_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ed188_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:necessidadetipoatendimentoaluno";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ed188_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from necessidadetipoatendimentoaluno ";
     $sql .= "      inner join aluno  on  aluno.ed47_i_codigo = necessidadetipoatendimentoaluno.ed188_aluno";
     $sql .= "      inner join necessidadetipoatendimento  on  necessidadetipoatendimento.ed186_sequencial = necessidadetipoatendimentoaluno.ed188_necessidadetipoatendimento";
     $sql .= "      left  join pais  on  pais.ed228_i_codigo = aluno.ed47_paisresidencia and  pais.ed228_i_codigo = aluno.ed47_i_pais";
     $sql .= "      left  join censouf  on  censouf.ed260_i_codigo = aluno.ed47_i_censoufnat and  censouf.ed260_i_codigo = aluno.ed47_i_censoufident and  censouf.ed260_i_codigo = aluno.ed47_i_censoufcert and  censouf.ed260_i_codigo = aluno.ed47_i_censoufend";
     $sql .= "      left  join censomunic  on  censomunic.ed261_i_codigo = aluno.ed47_i_censomuniccert and  censomunic.ed261_i_codigo = aluno.ed47_i_censomunicnat and  censomunic.ed261_i_codigo = aluno.ed47_i_censomunicend";
     $sql .= "      left  join censoorgemissrg  on  censoorgemissrg.ed132_i_codigo = aluno.ed47_i_censoorgemissrg";
     $sql .= "      left  join censocartorio  on  censocartorio.ed291_i_codigo = aluno.ed47_i_censocartorio";
     $sql .= "      left  join tiposanguineo  on  tiposanguineo.sd100_sequencial = aluno.ed47_tiposanguineo";
     $sql .= "      left  join censoregiao  on  censoregiao.ed174_codigo = aluno.ed47_censoregiao and  censoregiao.ed174_codigo = aluno.ed47_censoregiaonat";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed188_sequencial)) {
         $sql2 .= " where necessidadetipoatendimentoaluno.ed188_sequencial = $ed188_sequencial "; 
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

    public function sql_query_file($ed188_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from necessidadetipoatendimentoaluno ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed188_sequencial)){
         $sql2 .= " where necessidadetipoatendimentoaluno.ed188_sequencial = $ed188_sequencial "; 
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
