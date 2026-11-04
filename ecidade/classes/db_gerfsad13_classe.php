<?
//MODULO: pessoal
//CLASSE DA ENTIDADE gerfsad13
class cl_gerfsad13 {
   // cria variaveis de erro
   var $rotulo     = null;
   var $query_sql  = null;
   var $numrows    = 0;
   var $erro_status= null;
   var $erro_sql   = null;
   var $erro_banco = null;
   var $erro_msg   = null;
   var $erro_campo = null;
   var $pagina_retorno = null;
   // cria variaveis do arquivo
   var $r95_anousu = 0;
   var $r95_mesusu = 0;
   var $r95_regist = 0;
   var $r95_rubric = null;
   var $r95_valor = 0;
   var $r95_pd = 0;
   var $r95_quant = 0;
   var $r95_lotac = null;
   var $r95_semest = 0;
   var $r95_tpp = null;
   var $r95_instit = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 r95_anousu = int4 = Ano da Competência do Cálculo
                 r95_mesusu = int4 = Mês da Competência do Cálculo
                 r95_regist = int4 = Código da Matrícula do Servidor
                 r95_rubric = varchar(4) = Código da Rubrica
                 r95_valor = float4 = Valor da Rubrica
                 r95_pd = int4 = Tipo da Rubrica
                 r95_quant = float4 = Quantidade da Rubrica
                 r95_lotac = varchar(4) = Lotação do Servidor
                 r95_semest = int4 = Semestre da Rubrica
                 r95_tpp = varchar(1) = tpp
                 r95_instit = int4 = Instituição do Cálculo
                 ";
   //funcao construtor da classe
   function cl_gerfsad13() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("gerfsad13");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro
   function erro($mostra,$retorna) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->r95_anousu = ($this->r95_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_anousu"]:$this->r95_anousu);
       $this->r95_mesusu = ($this->r95_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_mesusu"]:$this->r95_mesusu);
       $this->r95_regist = ($this->r95_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_regist"]:$this->r95_regist);
       $this->r95_rubric = ($this->r95_rubric == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_rubric"]:$this->r95_rubric);
       $this->r95_valor = ($this->r95_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_valor"]:$this->r95_valor);
       $this->r95_pd = ($this->r95_pd == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_pd"]:$this->r95_pd);
       $this->r95_quant = ($this->r95_quant == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_quant"]:$this->r95_quant);
       $this->r95_lotac = ($this->r95_lotac == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_lotac"]:$this->r95_lotac);
       $this->r95_semest = ($this->r95_semest == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_semest"]:$this->r95_semest);
       $this->r95_tpp = ($this->r95_tpp == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_tpp"]:$this->r95_tpp);
       $this->r95_instit = ($this->r95_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["r95_instit"]:$this->r95_instit);
     }else{
     }
   }
   // funcao para inclusao
   function incluir (){
      $this->atualizacampos();
     if($this->r95_anousu == null ){
       $this->erro_sql = " Campo Ano da Competência do Cálculo nao Informado.";
       $this->erro_campo = "r95_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r95_mesusu == null ){
       $this->erro_sql = " Campo Mês da Competência do Cálculo nao Informado.";
       $this->erro_campo = "r95_mesusu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r95_regist == null ){
       $this->erro_sql = " Campo Código da Matrícula do Servidor nao Informado.";
       $this->erro_campo = "r95_regist";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r95_rubric == null ){
       $this->erro_sql = " Campo Código da Rubrica nao Informado.";
       $this->erro_campo = "r95_rubric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r95_valor == null ){
       $this->r95_valor = "0";
     }
     if($this->r95_pd == null ){
       $this->r95_pd = "0";
     }
     if($this->r95_quant == null ){
       $this->r95_quant = "0";
     }
     if($this->r95_lotac == null ){
       $this->erro_sql = " Campo Lotação do Servidor nao Informado.";
       $this->erro_campo = "r95_lotac";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r95_semest == null ){
       $this->r95_semest = "0";
     }
     if($this->r95_tpp == null ){
       $this->erro_sql = " Campo tpp nao Informado.";
       $this->erro_campo = "r95_tpp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r95_instit == null ){
       $this->erro_sql = " Campo Instituição do Cálculo nao Informado.";
       $this->erro_campo = "r95_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $result = @db_query("insert into gerfsad13(
                                       r95_anousu
                                      ,r95_mesusu
                                      ,r95_regist
                                      ,r95_rubric
                                      ,r95_valor
                                      ,r95_pd
                                      ,r95_quant
                                      ,r95_lotac
                                      ,r95_semest
                                      ,r95_tpp
                                      ,r95_instit
                       )
                values (
                                $this->r95_anousu
                               ,$this->r95_mesusu
                               ,$this->r95_regist
                               ,'$this->r95_rubric'
                               ,$this->r95_valor
                               ,$this->r95_pd
                               ,$this->r95_quant
                               ,'$this->r95_lotac'
                               ,$this->r95_semest
                               ,'$this->r95_tpp'
                               ,$this->r95_instit
                      )");
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "r95 () nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "r95 já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "r95 () nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     return true;
   }
   // funcao para alteracao
   function alterar ( $oid=null ) {
      $this->atualizacampos();
     $sql = " update gerfsad13 set ";
     $virgula = "";
     if(trim($this->r95_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_anousu"])){
        if(trim($this->r95_anousu)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_anousu"])){
           $this->r95_anousu = "0" ;
        }
       $sql  .= $virgula." r95_anousu = $this->r95_anousu ";
       $virgula = ",";
       if(trim($this->r95_anousu) == null ){
         $this->erro_sql = " Campo Ano da Competência do Cálculo nao Informado.";
         $this->erro_campo = "r95_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r95_mesusu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_mesusu"])){
        if(trim($this->r95_mesusu)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_mesusu"])){
           $this->r95_mesusu = "0" ;
        }
       $sql  .= $virgula." r95_mesusu = $this->r95_mesusu ";
       $virgula = ",";
       if(trim($this->r95_mesusu) == null ){
         $this->erro_sql = " Campo Mês da Competência do Cálculo nao Informado.";
         $this->erro_campo = "r95_mesusu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r95_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_regist"])){
        if(trim($this->r95_regist)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_regist"])){
           $this->r95_regist = "0" ;
        }
       $sql  .= $virgula." r95_regist = $this->r95_regist ";
       $virgula = ",";
       if(trim($this->r95_regist) == null ){
         $this->erro_sql = " Campo Código da Matrícula do Servidor nao Informado.";
         $this->erro_campo = "r95_regist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r95_rubric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_rubric"])){
       $sql  .= $virgula." r95_rubric = '$this->r95_rubric' ";
       $virgula = ",";
       if(trim($this->r95_rubric) == null ){
         $this->erro_sql = " Campo Código da Rubrica nao Informado.";
         $this->erro_campo = "r95_rubric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r95_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_valor"])){
        if(trim($this->r95_valor)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_valor"])){
           $this->r95_valor = "0" ;
        }
       $sql  .= $virgula." r95_valor = $this->r95_valor ";
       $virgula = ",";
     }
     if(trim($this->r95_pd)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_pd"])){
        if(trim($this->r95_pd)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_pd"])){
           $this->r95_pd = "0" ;
        }
       $sql  .= $virgula." r95_pd = $this->r95_pd ";
       $virgula = ",";
     }
     if(trim($this->r95_quant)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_quant"])){
        if(trim($this->r95_quant)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_quant"])){
           $this->r95_quant = "0" ;
        }
       $sql  .= $virgula." r95_quant = $this->r95_quant ";
       $virgula = ",";
     }
     if(trim($this->r95_lotac)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_lotac"])){
       $sql  .= $virgula." r95_lotac = '$this->r95_lotac' ";
       $virgula = ",";
       if(trim($this->r95_lotac) == null ){
         $this->erro_sql = " Campo Lotação do Servidor nao Informado.";
         $this->erro_campo = "r95_lotac";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r95_semest)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_semest"])){
        if(trim($this->r95_semest)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_semest"])){
           $this->r95_semest = "0" ;
        }
       $sql  .= $virgula." r95_semest = $this->r95_semest ";
       $virgula = ",";
     }
     if(trim($this->r95_tpp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_tpp"])){
       $sql  .= $virgula." r95_tpp = '$this->r95_tpp' ";
       $virgula = ",";
       if(trim($this->r95_tpp) == null ){
         $this->erro_sql = " Campo tpp nao Informado.";
         $this->erro_campo = "r95_tpp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r95_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r95_instit"])){
        if(trim($this->r95_instit)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r95_instit"])){
           $this->r95_instit = "0" ;
        }
       $sql  .= $virgula." r95_instit = $this->r95_instit ";
       $virgula = ",";
       if(trim($this->r95_instit) == null ){
         $this->erro_sql = " Campo Instituição do Cálculo nao Informado.";
         $this->erro_campo = "r95_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where oid = $oid ";
     $result = @db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "r95 nao Alterado. Alteracao Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "r95 nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
   // funcao para exclusao
   function excluir ( $oid=null ) {
     $this->atualizacampos(true);
     $sql = " delete from gerfsad13
                    where ";
     $sql2 = "";
     $sql2 = "oid = $oid";
     $result = @db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "r95 nao Excluído. Exclusão Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "r95 nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }
     }
   }
   // funcao do recordset
   function sql_record($sql) {
     $result = @db_query($sql);
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_numrows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Dados do Grupo nao Encontrado";
        $this->erro_msg   = "Usuário: \n\n ".$this->erro_sql." \n\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   function sql_query ( $oid = null,$campos="gerfsad13.oid,*",$ordem=null,$dbwhere=""){
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
     $sql .= " from gerfsad13 ";
     $sql2 = "";
     if($dbwhere==""){
       if( $oid != "" && $oid != null){
          $sql2 = " where gerfsad13.oid = $oid";
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
   // funcao do sql
   function sql_query_file ( $oid = null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from gerfsad13 ";
     $sql2 = "";
     if($dbwhere==""){
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
}
?>
