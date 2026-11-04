<?php

class cl_orcprojativunidademedida
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
    public $o221_sequencial = 0; 
    public $o221_orcprojativ = 0; 
    public $o221_anousu = 0; 
    public $o221_unidade = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 o221_sequencial = int4 = Sequencial 
                 o221_orcprojativ = int4 = Acao 
                 o221_anousu = int4 = Ano 
                 o221_unidade = int4 = Sequencial da unidade de medida 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("orcprojativunidademedida"); 
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
       $this->o221_sequencial = ($this->o221_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["o221_sequencial"]:$this->o221_sequencial);
       $this->o221_orcprojativ = ($this->o221_orcprojativ == ""?@$GLOBALS["HTTP_POST_VARS"]["o221_orcprojativ"]:$this->o221_orcprojativ);
       $this->o221_anousu = ($this->o221_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["o221_anousu"]:$this->o221_anousu);
       $this->o221_unidade = ($this->o221_unidade == ""?@$GLOBALS["HTTP_POST_VARS"]["o221_unidade"]:$this->o221_unidade);
     }else{
       $this->o221_sequencial = ($this->o221_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["o221_sequencial"]:$this->o221_sequencial);
     }
   }

    public function incluir($o221_sequencial)
    {
      $this->atualizacampos();
     if($this->o221_orcprojativ == null ){ 
       $this->erro_sql = " Campo Acao não informado.";
       $this->erro_campo = "o221_orcprojativ";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->o221_anousu == null ){ 
       $this->erro_sql = " Campo Ano não informado.";
       $this->erro_campo = "o221_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->o221_unidade == null ){ 
       $this->erro_sql = " Campo Sequencial da unidade de medida não informado.";
       $this->erro_campo = "o221_unidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($o221_sequencial == "" || $o221_sequencial == null ){
       $result = db_query("select nextval('orcprojativunidademedida_o221_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: orcprojativunidademedida_o221_sequencial_seq do campo: o221_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->o221_sequencial = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from orcprojativunidademedida_o221_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $o221_sequencial)){
         $this->erro_sql = " Campo o221_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->o221_sequencial = $o221_sequencial; 
       }
     }
     if(($this->o221_sequencial == null) || ($this->o221_sequencial == "") ){ 
       $this->erro_sql = " Campo o221_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into orcprojativunidademedida(
                                       o221_sequencial 
                                      ,o221_orcprojativ 
                                      ,o221_anousu 
                                      ,o221_unidade 
                       )
                values (
                                $this->o221_sequencial 
                               ,$this->o221_orcprojativ 
                               ,$this->o221_anousu 
                               ,$this->o221_unidade 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Unidade de medida da ação ($this->o221_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Unidade de medida da ação já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Unidade de medida da ação ($this->o221_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o221_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 

    public function alterar($o221_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update orcprojativunidademedida set ";
     $virgula = "";
     if(trim($this->o221_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o221_sequencial"])){ 
       $sql  .= $virgula." o221_sequencial = $this->o221_sequencial ";
       $virgula = ",";
       if(trim($this->o221_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "o221_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o221_orcprojativ)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o221_orcprojativ"])){ 
       $sql  .= $virgula." o221_orcprojativ = $this->o221_orcprojativ ";
       $virgula = ",";
       if(trim($this->o221_orcprojativ) == null ){ 
         $this->erro_sql = " Campo Acao não informado.";
         $this->erro_campo = "o221_orcprojativ";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o221_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o221_anousu"])){ 
       $sql  .= $virgula." o221_anousu = $this->o221_anousu ";
       $virgula = ",";
       if(trim($this->o221_anousu) == null ){ 
         $this->erro_sql = " Campo Ano não informado.";
         $this->erro_campo = "o221_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->o221_unidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o221_unidade"])){ 
       $sql  .= $virgula." o221_unidade = $this->o221_unidade ";
       $virgula = ",";
       if(trim($this->o221_unidade) == null ){ 
         $this->erro_sql = " Campo Sequencial da unidade de medida não informado.";
         $this->erro_campo = "o221_unidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($o221_sequencial!=null){
       $sql .= " o221_sequencial = $this->o221_sequencial";
     }
     
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidade de medida da ação não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->o221_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidade de medida da ação não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->o221_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o221_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($o221_sequencial=null, $dbwhere = null)
    {
     $sql = " delete from orcprojativunidademedida
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($o221_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " o221_sequencial = $o221_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Unidade de medida da ação não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$o221_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Unidade de medida da ação não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$o221_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$o221_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:orcprojativunidademedida";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($o221_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from orcprojativunidademedida ";
     $sql .= "      inner join orcprojativ  on  orcprojativ.o55_anousu = orcprojativunidademedida.o221_anousu and  orcprojativ.o55_projativ = orcprojativunidademedida.o221_orcprojativ";
     $sql .= "      inner join sigfisunidademedida  on  sigfisunidademedida.o220_sequencial = orcprojativunidademedida.o221_unidade";
     $sql .= "      inner join db_config  on  db_config.codigo = orcprojativ.o55_instit";
     $sql .= "      inner join orcproduto  on  orcproduto.o22_codproduto = orcprojativ.o55_orcproduto";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($o221_sequencial)) {
         $sql2 .= " where orcprojativunidademedida.o221_sequencial = $o221_sequencial "; 
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

    public function sql_query_file($o221_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from orcprojativunidademedida ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($o221_sequencial)){
         $sql2 .= " where orcprojativunidademedida.o221_sequencial = $o221_sequencial "; 
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
