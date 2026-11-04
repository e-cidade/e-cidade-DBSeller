<?php

class cl_documentos_andamento
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
    public $p116_codigo = 0;
    public $p116_descricao = null;
    public $p116_protprocesso = 0;
    public $p116_protprocessodocumento = 0;
    public $p116_atividade_atual = 0;
    public $p116_proxima_atividade = 0;
    public $p116_data_criacao_dia = null;
    public $p116_data_criacao_mes = null;
    public $p116_data_criacao_ano = null;
    public $p116_data_criacao = null;
    public $p116_data_modificacao_dia = null;
    public $p116_data_modificacao_mes = null;
    public $p116_data_modificacao_ano = null;
    public $p116_data_modificacao = null;
    public $p116_codigo_origem = 0;
    public $p116_qrcode = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 p116_codigo = int8 = Código
                 p116_descricao = varchar(255) = Descrição
                 p116_protprocesso = int8 = Processo
                 p116_protprocessodocumento = int8 = Documento
                 p116_atividade_atual = int8 = Atividade atual
                 p116_proxima_atividade = int8 = Próxima atividade
                 p116_data_criacao = date = Data de criação
                 p116_data_modificacao = date = Data de Modificação
                 p116_codigo_origem = int8 = Documento de Origem
                 p116_qrcode = varchar(36) = Código Identificador
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("documentos_andamento");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna==true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao==false) {
            $this->p116_codigo = ($this->p116_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_codigo"]:$this->p116_codigo);
            $this->p116_descricao = ($this->p116_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_descricao"]:$this->p116_descricao);
            $this->p116_protprocesso = ($this->p116_protprocesso == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_protprocesso"]:$this->p116_protprocesso);
            $this->p116_protprocessodocumento = ($this->p116_protprocessodocumento == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_protprocessodocumento"]:$this->p116_protprocessodocumento);
            $this->p116_atividade_atual = ($this->p116_atividade_atual == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_atividade_atual"]:$this->p116_atividade_atual);
            $this->p116_proxima_atividade = ($this->p116_proxima_atividade == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_proxima_atividade"]:$this->p116_proxima_atividade);
            if ($this->p116_data_criacao == "") {
                $this->p116_data_criacao_dia = ($this->p116_data_criacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_data_criacao_dia"]:$this->p116_data_criacao_dia);
                $this->p116_data_criacao_mes = ($this->p116_data_criacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_data_criacao_mes"]:$this->p116_data_criacao_mes);
                $this->p116_data_criacao_ano = ($this->p116_data_criacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_data_criacao_ano"]:$this->p116_data_criacao_ano);
                if ($this->p116_data_criacao_dia != "") {
                    $this->p116_data_criacao = $this->p116_data_criacao_ano."-".$this->p116_data_criacao_mes."-".$this->p116_data_criacao_dia;
                }
            }
            if ($this->p116_data_modificacao == "") {
                $this->p116_data_modificacao_dia = ($this->p116_data_modificacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_data_modificacao_dia"]:$this->p116_data_modificacao_dia);
                $this->p116_data_modificacao_mes = ($this->p116_data_modificacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_data_modificacao_mes"]:$this->p116_data_modificacao_mes);
                $this->p116_data_modificacao_ano = ($this->p116_data_modificacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_data_modificacao_ano"]:$this->p116_data_modificacao_ano);
                if ($this->p116_data_modificacao_dia != "") {
                    $this->p116_data_modificacao = $this->p116_data_modificacao_ano."-".$this->p116_data_modificacao_mes."-".$this->p116_data_modificacao_dia;
                }
            }
            $this->p116_codigo_origem = ($this->p116_codigo_origem == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_codigo_origem"]:$this->p116_codigo_origem);
            $this->p116_qrcode = ($this->p116_qrcode == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_qrcode"]:$this->p116_qrcode);
        } else {
            $this->p116_codigo = ($this->p116_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["p116_codigo"]:$this->p116_codigo);
        }
    }

    public function incluir($p116_codigo)
    {
        $this->atualizacampos();
        if ($this->p116_descricao == null) {
            $this->erro_sql = " Campo Descrição não informado.";
            $this->erro_campo = "p116_descricao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p116_protprocesso == null) {
            $this->erro_sql = " Campo Processo não informado.";
            $this->erro_campo = "p116_protprocesso";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p116_protprocessodocumento == null) {
            $this->erro_sql = " Campo Documento não informado.";
            $this->erro_campo = "p116_protprocessodocumento";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p116_atividade_atual == null) {
            $this->erro_sql = " Campo Atividade atual não informado.";
            $this->erro_campo = "p116_atividade_atual";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p116_proxima_atividade == null) {
            $this->p116_proxima_atividade = "0";
        }
        if ($this->p116_data_criacao == null) {
            $this->erro_sql = " Campo Data de criação não informado.";
            $this->erro_campo = "p116_data_criacao_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p116_data_modificacao == null) {
            $this->erro_sql = " Campo Data de Modificação não informado.";
            $this->erro_campo = "p116_data_modificacao_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p116_codigo_origem == null) {
            $this->erro_sql = " Campo Documento de Origem não informado.";
            $this->erro_campo = "p116_codigo_origem";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p116_qrcode == null) {
            $this->erro_sql = " Campo Código Identificador não informado.";
            $this->erro_campo = "p116_qrcode";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($p116_codigo == "" || $p116_codigo == null) {
            $result = db_query("select nextval('documentos_andamento_p116_codigo_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: documentos_andamento_p116_codigo_seq do campo: p116_codigo";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->p116_codigo = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from documentos_andamento_p116_codigo_seq");
            if (($result != false) && (pg_fetch_result($result, 0, 0) < $p116_codigo)) {
                $this->erro_sql = " Campo p116_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->p116_codigo = $p116_codigo;
            }
        }
        if (($this->p116_codigo == null) || ($this->p116_codigo == "")) {
            $this->erro_sql = " Campo p116_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into documentos_andamento(
                                       p116_codigo
                                      ,p116_descricao
                                      ,p116_protprocesso
                                      ,p116_protprocessodocumento
                                      ,p116_atividade_atual
                                      ,p116_proxima_atividade
                                      ,p116_data_criacao
                                      ,p116_data_modificacao
                                      ,p116_codigo_origem
                                      ,p116_qrcode
                       )
                values (
                                $this->p116_codigo
                               ,'$this->p116_descricao'
                               ,$this->p116_protprocesso
                               ,$this->p116_protprocessodocumento
                               ,$this->p116_atividade_atual
                               ,$this->p116_proxima_atividade
                               ,".($this->p116_data_criacao == "null" || $this->p116_data_criacao == ""?"null":"'".$this->p116_data_criacao."'")."
                               ,".($this->p116_data_modificacao == "null" || $this->p116_data_modificacao == ""?"null":"'".$this->p116_data_modificacao."'")."
                               ,$this->p116_codigo_origem
                               ,'$this->p116_qrcode'
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Documentos para andamento ($this->p116_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Documentos para andamento já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Documentos para andamento ($this->p116_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->p116_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->p116_codigo));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,1014011,'$this->p116_codigo','I')");
                $resac = db_query("insert into db_acount values($acount,1010902,1014011,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014012,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014013,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_protprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014014,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_protprocessodocumento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014015,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_atividade_atual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014016,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_proxima_atividade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014017,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_data_criacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014018,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_data_modificacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014065,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_codigo_origem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1010902,1014190,'','".addSlashes(pg_fetch_result($resaco, 0, 'p116_qrcode'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($p116_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update documentos_andamento set ";
        $virgula = "";
        if (trim($this->p116_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_codigo"])) {
            $sql  .= $virgula." p116_codigo = $this->p116_codigo ";
            $virgula = ",";
            if (trim($this->p116_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "p116_codigo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p116_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_descricao"])) {
            $sql  .= $virgula." p116_descricao = '$this->p116_descricao' ";
            $virgula = ",";
            if (trim($this->p116_descricao) == null) {
                $this->erro_sql = " Campo Descrição não informado.";
                $this->erro_campo = "p116_descricao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p116_protprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_protprocesso"])) {
            $sql  .= $virgula." p116_protprocesso = $this->p116_protprocesso ";
            $virgula = ",";
            if (trim($this->p116_protprocesso) == null) {
                $this->erro_sql = " Campo Processo não informado.";
                $this->erro_campo = "p116_protprocesso";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p116_protprocessodocumento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_protprocessodocumento"])) {
            $sql  .= $virgula." p116_protprocessodocumento = $this->p116_protprocessodocumento ";
            $virgula = ",";
            if (trim($this->p116_protprocessodocumento) == null) {
                $this->erro_sql = " Campo Documento não informado.";
                $this->erro_campo = "p116_protprocessodocumento";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p116_atividade_atual)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_atividade_atual"])) {
            $sql  .= $virgula." p116_atividade_atual = $this->p116_atividade_atual ";
            $virgula = ",";
            if (trim($this->p116_atividade_atual) == null) {
                $this->erro_sql = " Campo Atividade atual não informado.";
                $this->erro_campo = "p116_atividade_atual";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p116_proxima_atividade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_proxima_atividade"])) {
            if (trim($this->p116_proxima_atividade)=="" && isset($GLOBALS["HTTP_POST_VARS"]["p116_proxima_atividade"])) {
                $this->p116_proxima_atividade = "0" ;
            }
            $sql  .= $virgula." p116_proxima_atividade = $this->p116_proxima_atividade ";
            $virgula = ",";
        }
        if (trim($this->p116_data_criacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_data_criacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["p116_data_criacao_dia"] !="")) {
            $sql  .= $virgula." p116_data_criacao = '$this->p116_data_criacao' ";
            $virgula = ",";
            if (trim($this->p116_data_criacao) == null) {
                $this->erro_sql = " Campo Data de criação não informado.";
                $this->erro_campo = "p116_data_criacao_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["p116_data_criacao_dia"])) {
                $sql  .= $virgula." p116_data_criacao = null ";
                $virgula = ",";
                if (trim($this->p116_data_criacao) == null) {
                      $this->erro_sql = " Campo Data de criação não informado.";
                      $this->erro_campo = "p116_data_criacao_dia";
                      $this->erro_banco = "";
                      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                      $this->erro_status = "0";
                      return false;
                }
            }
        }
        if (trim($this->p116_data_modificacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_data_modificacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["p116_data_modificacao_dia"] !="")) {
            $sql  .= $virgula." p116_data_modificacao = '$this->p116_data_modificacao' ";
            $virgula = ",";
            if (trim($this->p116_data_modificacao) == null) {
                $this->erro_sql = " Campo Data de Modificação não informado.";
                $this->erro_campo = "p116_data_modificacao_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["p116_data_modificacao_dia"])) {
                $sql  .= $virgula." p116_data_modificacao = null ";
                $virgula = ",";
                if (trim($this->p116_data_modificacao) == null) {
                      $this->erro_sql = " Campo Data de Modificação não informado.";
                      $this->erro_campo = "p116_data_modificacao_dia";
                      $this->erro_banco = "";
                      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                      $this->erro_status = "0";
                      return false;
                }
            }
        }
        if (trim($this->p116_codigo_origem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_codigo_origem"])) {
            $sql  .= $virgula." p116_codigo_origem = $this->p116_codigo_origem ";
            $virgula = ",";
            if (trim($this->p116_codigo_origem) == null) {
                $this->erro_sql = " Campo Documento de Origem não informado.";
                $this->erro_campo = "p116_codigo_origem";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->p116_qrcode)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p116_qrcode"])) {
            $sql  .= $virgula." p116_qrcode = '$this->p116_qrcode' ";
            $virgula = ",";
            if (trim($this->p116_qrcode) == null) {
                $this->erro_sql = " Campo Código Identificador não informado.";
                $this->erro_campo = "p116_qrcode";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($p116_codigo!=null) {
            $sql .= " p116_codigo = $this->p116_codigo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->p116_codigo));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_fetch_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,1014011,'$this->p116_codigo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_codigo"]) || $this->p116_codigo != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014011,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_codigo'))."','$this->p116_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_descricao"]) || $this->p116_descricao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014012,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_descricao'))."','$this->p116_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_protprocesso"]) || $this->p116_protprocesso != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014013,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_protprocesso'))."','$this->p116_protprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_protprocessodocumento"]) || $this->p116_protprocessodocumento != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014014,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_protprocessodocumento'))."','$this->p116_protprocessodocumento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_atividade_atual"]) || $this->p116_atividade_atual != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014015,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_atividade_atual'))."','$this->p116_atividade_atual',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_proxima_atividade"]) || $this->p116_proxima_atividade != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014016,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_proxima_atividade'))."','$this->p116_proxima_atividade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_data_criacao"]) || $this->p116_data_criacao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014017,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_data_criacao'))."','$this->p116_data_criacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_data_modificacao"]) || $this->p116_data_modificacao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014018,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_data_modificacao'))."','$this->p116_data_modificacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_codigo_origem"]) || $this->p116_codigo_origem != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014065,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_codigo_origem'))."','$this->p116_codigo_origem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["p116_qrcode"]) || $this->p116_qrcode != "") {
                        $resac = db_query("insert into db_acount values($acount,1010902,1014190,'".addSlashes(pg_fetch_result($resaco, $conresaco, 'p116_qrcode'))."','$this->p116_qrcode',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Documentos para andamento não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->p116_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Documentos para andamento não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->p116_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->p116_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($p116_codigo = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($p116_codigo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,1014011,'$p116_codigo','E')");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014011,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014012,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014013,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_protprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014014,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_protprocessodocumento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014015,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_atividade_atual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014016,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_proxima_atividade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014017,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_data_criacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014018,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_data_modificacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014065,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_codigo_origem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1010902,1014190,'','".addSlashes(pg_fetch_result($resaco, $iresaco, 'p116_qrcode'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from documentos_andamento
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p116_codigo)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " p116_codigo = $p116_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Documentos para andamento não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$p116_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Documentos para andamento não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$p116_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$p116_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:documentos_andamento";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($p116_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from documentos_andamento ";
        $sql .= "      inner join protprocesso  on  protprocesso.p58_codproc = documentos_andamento.p116_protprocesso";
        $sql .= "      inner join protprocessodocumento  on  protprocessodocumento.p01_sequencial = documentos_andamento.p116_protprocessodocumento";
        $sql .= "      inner join processo_atividadesexecucao  on  processo_atividadesexecucao.p118_codigo = documentos_andamento.p116_atividade_atual and  processo_atividadesexecucao.p118_codigo = documentos_andamento.p116_proxima_atividade";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = protprocesso.p58_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = protprocesso.p58_instit";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = protprocesso.p58_id_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = protprocesso.p58_coddepto";
        $sql .= "      inner join tipoproc  on  tipoproc.p51_codigo = protprocesso.p58_codigo";
        $sql .= "      inner join tipoprocesso  on  tipoprocesso.p109_sequencial = protprocesso.p58_tipoprocesso";
        $sql .= "      left  join db_usuarios  as a on   a.id_usuario = protprocessodocumento.p01_usuario";
        $sql .= "      inner join protprocesso  on  protprocesso.p58_codproc = protprocessodocumento.p01_protprocesso";
        $sql .= "      left  join procandamint  on  procandamint.p78_sequencial = protprocessodocumento.p01_procandamint";
        $sql .= "      inner join protprocesso  on  protprocesso.p58_codproc = processo_atividadesexecucao.p118_protprocesso";
        $sql .= "      inner join atividadesexecucao  on  atividadesexecucao.p114_codigo = processo_atividadesexecucao.p118_atividadesexecucao";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p116_codigo)) {
                $sql2 .= " where documentos_andamento.p116_codigo = $p116_codigo ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($p116_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from documentos_andamento ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p116_codigo)) {
                $sql2 .= " where documentos_andamento.p116_codigo = $p116_codigo ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }
    public function sql_query_documentos_usuario(
        $p116_codigo = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = ""
    ) {
        $sql = "SELECT
                    {$campos}
                FROM
                     documentos_andamento
                JOIN processo_atividadesexecucao proxima_atividade
                ON proxima_atividade.p118_codigo = documentos_andamento.p116_proxima_atividade
                JOIN processo_usuarios
                ON processo_usuarios.p119_protprocesso = proxima_atividade.p118_protprocesso
                AND proxima_atividade.p118_atividadesexecucao = processo_usuarios.p119_atividadeexecucao
                JOIN protprocesso
                ON  protprocesso.p58_codproc  = documentos_andamento.p116_protprocesso
                JOIN protocolo.tipoproc
                ON protocolo.tipoproc.p51_codigo = protprocesso.p58_codigo

                ";

        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p116_codigo)) {
                $sql2 .= " where documentos_andamento.p116_codigo = $p116_codigo ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }
}
