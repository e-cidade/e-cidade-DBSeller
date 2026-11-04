<?php

class cl_avaliacaogruporespostaadmissaopreliminar
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
    public $eso18_sequencial = 0;
    public $eso18_avaliacaogruporesposta = 0;
    public $eso18_cgm = 0;
    public $eso18_cpf = null;
    public $eso18_regist = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 eso18_sequencial = int4 = Sequencial
                 eso18_avaliacaogruporesposta = int4 = Resposta
                 eso18_cgm = int4 = CGM
                 eso18_cpf = varchar(11) = CPF
                 eso18_regist = int4 = Matricula
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("avaliacaogruporespostaadmissaopreliminar");
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
            $this->eso18_sequencial = ($this->eso18_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["eso18_sequencial"]:$this->eso18_sequencial);
            $this->eso18_avaliacaogruporesposta = ($this->eso18_avaliacaogruporesposta == ""?@$GLOBALS["HTTP_POST_VARS"]["eso18_avaliacaogruporesposta"]:$this->eso18_avaliacaogruporesposta);
            $this->eso18_cgm = ($this->eso18_cgm == ""?@$GLOBALS["HTTP_POST_VARS"]["eso18_cgm"]:$this->eso18_cgm);
            $this->eso18_cpf = ($this->eso18_cpf == ""?@$GLOBALS["HTTP_POST_VARS"]["eso18_cpf"]:$this->eso18_cpf);
            $this->eso18_regist = ($this->eso18_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["eso18_regist"]:$this->eso18_regist);
        }else{
            $this->eso18_sequencial = ($this->eso18_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["eso18_sequencial"]:$this->eso18_sequencial);
        }
    }

    public function incluir($eso18_sequencial)
    {
        $this->atualizacampos();
        if($this->eso18_avaliacaogruporesposta == null ){
            $this->erro_sql = " Campo Resposta não informado.";
            $this->erro_campo = "eso18_avaliacaogruporesposta";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->eso18_cgm == null ){
            $this->erro_sql = " Campo CGM não informado.";
            $this->erro_campo = "eso18_cgm";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->eso18_cpf == null ){
            $this->erro_sql = " Campo CPF não informado.";
            $this->erro_campo = "eso18_cpf";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->eso18_regist == null ){
            $this->erro_sql = " Campo Matricula não informado.";
            $this->erro_campo = "eso18_regist";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($eso18_sequencial == "" || $eso18_sequencial == null ){
            $result = db_query("select nextval('avaliacaogruporespostaadmissaopreliminar_eso18_sequencial_seq')");
            if($result==false){
                $this->erro_banco = str_replace("\n","",@pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: avaliacaogruporespostaadmissaopreliminar_eso18_sequencial_seq do campo: eso18_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->eso18_sequencial = pg_result($result,0,0);
        }else{
            $result = db_query("select last_value from avaliacaogruporespostaadmissaopreliminar_eso18_sequencial_seq");
            if(($result != false) && (pg_result($result,0,0) < $eso18_sequencial)){
                $this->erro_sql = " Campo eso18_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }else{
                $this->eso18_sequencial = $eso18_sequencial;
            }
        }
        if(($this->eso18_sequencial == null) || ($this->eso18_sequencial == "") ){
            $this->erro_sql = " Campo eso18_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into avaliacaogruporespostaadmissaopreliminar(
                                       eso18_sequencial
                                      ,eso18_avaliacaogruporesposta
                                      ,eso18_cgm
                                      ,eso18_cpf
                                      ,eso18_regist
                       )
                values (
                                $this->eso18_sequencial
                               ,$this->eso18_avaliacaogruporesposta
                               ,$this->eso18_cgm
                               ,'$this->eso18_cpf'
                               ,$this->eso18_regist
                      )";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "avaliacaogruporespostaadmissaopreliminar ($this->eso18_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "avaliacaogruporespostaadmissaopreliminar já Cadastrado";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "avaliacaogruporespostaadmissaopreliminar ($this->eso18_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->eso18_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->eso18_sequencial  ));
            if(($resaco!=false)||($this->numrows!=0)){

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,1009936,'$this->eso18_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010314,1009936,'','".AddSlashes(pg_result($resaco,0,'eso18_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010314,1009937,'','".AddSlashes(pg_result($resaco,0,'eso18_avaliacaogruporesposta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010314,1009938,'','".AddSlashes(pg_result($resaco,0,'eso18_cgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010314,1009939,'','".AddSlashes(pg_result($resaco,0,'eso18_cpf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010314,1013464,'','".AddSlashes(pg_result($resaco,0,'eso18_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($eso18_sequencial=null)
    {
        $this->atualizacampos();
        $sql = " update avaliacaogruporespostaadmissaopreliminar set ";
        $virgula = "";
        if(trim($this->eso18_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso18_sequencial"])){
            $sql  .= $virgula." eso18_sequencial = $this->eso18_sequencial ";
            $virgula = ",";
            if(trim($this->eso18_sequencial) == null ){
                $this->erro_sql = " Campo Sequencial não informado.";
                $this->erro_campo = "eso18_sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->eso18_avaliacaogruporesposta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso18_avaliacaogruporesposta"])){
            $sql  .= $virgula." eso18_avaliacaogruporesposta = $this->eso18_avaliacaogruporesposta ";
            $virgula = ",";
            if(trim($this->eso18_avaliacaogruporesposta) == null ){
                $this->erro_sql = " Campo Resposta não informado.";
                $this->erro_campo = "eso18_avaliacaogruporesposta";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->eso18_cgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso18_cgm"])){
            $sql  .= $virgula." eso18_cgm = $this->eso18_cgm ";
            $virgula = ",";
            if(trim($this->eso18_cgm) == null ){
                $this->erro_sql = " Campo CGM não informado.";
                $this->erro_campo = "eso18_cgm";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->eso18_cpf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso18_cpf"])){
            $sql  .= $virgula." eso18_cpf = '$this->eso18_cpf' ";
            $virgula = ",";
            if(trim($this->eso18_cpf) == null ){
                $this->erro_sql = " Campo CPF não informado.";
                $this->erro_campo = "eso18_cpf";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->eso18_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso18_regist"])){
            $sql  .= $virgula." eso18_regist = $this->eso18_regist ";
            $virgula = ",";
            if(trim($this->eso18_regist) == null ){
                $this->erro_sql = " Campo Matricula não informado.";
                $this->erro_campo = "eso18_regist";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if($eso18_sequencial!=null){
            $sql .= " eso18_sequencial = $this->eso18_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->eso18_sequencial));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,1009936,'$this->eso18_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso18_sequencial"]) || $this->eso18_sequencial != "")
                        $resac = db_query("insert into db_acount values($acount,1010314,1009936,'".AddSlashes(pg_result($resaco,$conresaco,'eso18_sequencial'))."','$this->eso18_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso18_avaliacaogruporesposta"]) || $this->eso18_avaliacaogruporesposta != "")
                        $resac = db_query("insert into db_acount values($acount,1010314,1009937,'".AddSlashes(pg_result($resaco,$conresaco,'eso18_avaliacaogruporesposta'))."','$this->eso18_avaliacaogruporesposta',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso18_cgm"]) || $this->eso18_cgm != "")
                        $resac = db_query("insert into db_acount values($acount,1010314,1009938,'".AddSlashes(pg_result($resaco,$conresaco,'eso18_cgm'))."','$this->eso18_cgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso18_cpf"]) || $this->eso18_cpf != "")
                        $resac = db_query("insert into db_acount values($acount,1010314,1009939,'".AddSlashes(pg_result($resaco,$conresaco,'eso18_cpf'))."','$this->eso18_cpf',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["eso18_regist"]) || $this->eso18_regist != "")
                        $resac = db_query("insert into db_acount values($acount,1010314,1013464,'".AddSlashes(pg_result($resaco,$conresaco,'eso18_regist'))."','$this->eso18_regist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "avaliacaogruporespostaadmissaopreliminar não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->eso18_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "avaliacaogruporespostaadmissaopreliminar não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->eso18_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->eso18_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($eso18_sequencial=null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($eso18_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,1009936,'$eso18_sequencial','E')");
                    $resac  = db_query("insert into db_acount values($acount,1010314,1009936,'','".AddSlashes(pg_result($resaco,$iresaco,'eso18_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010314,1009937,'','".AddSlashes(pg_result($resaco,$iresaco,'eso18_avaliacaogruporesposta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010314,1009938,'','".AddSlashes(pg_result($resaco,$iresaco,'eso18_cgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010314,1009939,'','".AddSlashes(pg_result($resaco,$iresaco,'eso18_cpf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010314,1013464,'','".AddSlashes(pg_result($resaco,$iresaco,'eso18_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from avaliacaogruporespostaadmissaopreliminar
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($eso18_sequencial)){
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " eso18_sequencial = $eso18_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "avaliacaogruporespostaadmissaopreliminar não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$eso18_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "avaliacaogruporespostaadmissaopreliminar não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$eso18_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$eso18_sequencial;
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
            $this->erro_sql   = "Record Vazio na Tabela:avaliacaogruporespostaadmissaopreliminar";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($eso18_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos}";
        $sql .= "  from avaliacaogruporespostaadmissaopreliminar ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = avaliacaogruporespostaadmissaopreliminar.eso18_cgm";
        $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = avaliacaogruporespostaadmissaopreliminar.eso18_regist";
        $sql .= "      inner join avaliacaogruporesposta  on  avaliacaogruporesposta.db107_sequencial = avaliacaogruporespostaadmissaopreliminar.eso18_avaliacaogruporesposta";
        //$sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
        //$sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        //$sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        //$sql .= "      left  join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
        //$sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        //$sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        //$sql .= "      left  join rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
        //$sql .= "      inner join rhreajusteparidade  on  rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($eso18_sequencial)) {
                $sql2 .= " where avaliacaogruporespostaadmissaopreliminar.eso18_sequencial = $eso18_sequencial ";
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

    public function sql_query_file($eso18_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos} ";
        $sql .= "  from avaliacaogruporespostaadmissaopreliminar ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($eso18_sequencial)){
                $sql2 .= " where avaliacaogruporespostaadmissaopreliminar.eso18_sequencial = $eso18_sequencial ";
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

    public function sql_avaliacao_preenchida( $eso18_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "" )
    {
        $sql  = "select {$campos} ";
        $sql .= "  from avaliacaogruporespostaadmissaopreliminar ";
        $sql .= "  join avaliacaogruporesposta on db107_sequencial = eso18_avaliacaogruporesposta ";
        $sql .= "  join avaliacaogrupoperguntaresposta on db108_avaliacaogruporesposta = db107_sequencial";
        $sql .= "  join avaliacaoresposta ON db106_sequencial = db108_avaliacaoresposta ";
        $sql .= "  join avaliacaoperguntaopcao ON db104_sequencial = db106_avaliacaoperguntaopcao ";
        $sql .= "  join avaliacaopergunta ON db103_sequencial = db104_avaliacaopergunta ";
        $sql .= "  join avaliacaogrupopergunta ON db103_avaliacaogrupopergunta = db102_sequencial ";
        $sql .= "  join avaliacao ON db102_avaliacao = db101_sequencial ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($eso18_sequencial)){
                $sql2 .= " where avaliacaogruporespostacgm.eso18_sequencial = {$eso18_sequencial} ";
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

    public function buscaRespostasPorPergunta($pergunta, $preenchimento, $campos = "*", $ordem = null)
    {
        $sql  = "select {$campos}";
        $sql .= "  from avaliacaogruporespostaadmissaopreliminar ";
        $sql .= "      inner join avaliacaogruporesposta on db107_sequencial = eso18_avaliacaogruporesposta";
        $sql .= "      inner join avaliacaogrupoperguntaresposta on db108_avaliacaogruporesposta = db107_sequencial";
        $sql .= "      inner join avaliacaoresposta on db106_sequencial = db108_avaliacaoresposta";
        $sql .= "      inner join avaliacaoperguntaopcao on db104_sequencial = db106_avaliacaoperguntaopcao";
        $sql .= "      inner join avaliacaopergunta on db103_sequencial = db104_avaliacaopergunta";
        $sql .= " where db103_sequencial = {$pergunta}";
        $sql .= "   and db107_sequencial = {$preenchimento}";
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

}
