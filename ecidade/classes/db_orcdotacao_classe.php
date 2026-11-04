<?php
class cl_orcdotacao
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
    public $o58_anousu = 0;
    public $o58_coddot = 0;
    public $o58_orgao = 0;
    public $o58_unidade = 0;
    public $o58_funcao = 0;
    public $o58_subfuncao = 0;
    public $o58_programa = null;
    public $o58_projativ = 0;
    public $o58_codele = 0;
    public $o58_codigo = 0;
    public $o58_valor = 0;
    public $o58_instit = 0;
    public $o58_localizadorgastos = null;
    public $o58_datacriacao_dia = null;
    public $o58_datacriacao_mes = null;
    public $o58_datacriacao_ano = null;
    public $o58_datacriacao = null;
    public $o58_concarpeculiar = null;
    public $o58_esferaorcamentaria = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 o58_anousu = int4 = Exercício
                 o58_coddot = int4 = Reduzido
                 o58_orgao = int4 = Código Orgão
                 o58_unidade = int4 = Código Unidade
                 o58_funcao = int4 = Código da Função
                 o58_subfuncao = int4 = Sub Função
                 o58_programa = int4 = Programas Orçamento
                 o58_projativ = int4 = Projetos / Atividades
                 o58_codele = int4 = Código Elemento
                 o58_codigo = int4 = Tipo de Recurso
                 o58_valor = float8 = Previsão
                 o58_instit = int4 = Instituição
                 o58_localizadorgastos = int4 = Localizador dos Gastos
                 o58_datacriacao = date = Data da Criação
                 o58_concarpeculiar = varchar(100) = C.Peculiar/ C. Aplicação
                 o58_esferaorcamentaria = int4 = Esfera Orçamentária
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("orcdotacao");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->o58_anousu = ($this->o58_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_anousu"] : $this->o58_anousu);
            $this->o58_coddot = ($this->o58_coddot == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_coddot"] : $this->o58_coddot);
            $this->o58_orgao = ($this->o58_orgao == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_orgao"] : $this->o58_orgao);
            $this->o58_unidade = ($this->o58_unidade == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_unidade"] : $this->o58_unidade);
            $this->o58_funcao = ($this->o58_funcao == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_funcao"] : $this->o58_funcao);
            $this->o58_subfuncao = ($this->o58_subfuncao == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_subfuncao"] : $this->o58_subfuncao);
            if (is_null($this->o58_programa)) {
                $this->o58_programa = $GLOBALS["HTTP_POST_VARS"]["o58_programa"];
            }
            $this->o58_projativ = ($this->o58_projativ == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_projativ"] : $this->o58_projativ);
            $this->o58_codele = ($this->o58_codele == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_codele"] : $this->o58_codele);
            $this->o58_codigo = ($this->o58_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_codigo"] : $this->o58_codigo);
            $this->o58_valor = ($this->o58_valor == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_valor"] : $this->o58_valor);
            $this->o58_instit = ($this->o58_instit == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_instit"] : $this->o58_instit);
            if (is_null($this->o58_localizadorgastos)) {
                $this->o58_localizadorgastos = $GLOBALS["HTTP_POST_VARS"]["o58_localizadorgastos"];
            }
            if ($this->o58_datacriacao == "") {
                $this->o58_datacriacao_dia = ($this->o58_datacriacao_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_datacriacao_dia"] : $this->o58_datacriacao_dia);
                $this->o58_datacriacao_mes = ($this->o58_datacriacao_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_datacriacao_mes"] : $this->o58_datacriacao_mes);
                $this->o58_datacriacao_ano = ($this->o58_datacriacao_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_datacriacao_ano"] : $this->o58_datacriacao_ano);
                if ($this->o58_datacriacao_dia != "") {
                    $this->o58_datacriacao = $this->o58_datacriacao_ano . "-" . $this->o58_datacriacao_mes . "-" . $this->o58_datacriacao_dia;
                }
            }
            $this->o58_concarpeculiar = ($this->o58_concarpeculiar == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_concarpeculiar"] : $this->o58_concarpeculiar);
            $this->o58_esferaorcamentaria = ($this->o58_esferaorcamentaria == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_esferaorcamentaria"] : $this->o58_esferaorcamentaria);
        } else {
            $this->o58_anousu = ($this->o58_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_anousu"] : $this->o58_anousu);
            $this->o58_coddot = ($this->o58_coddot == "" ? @$GLOBALS["HTTP_POST_VARS"]["o58_coddot"] : $this->o58_coddot);
        }
    }

    public function incluir($o58_anousu, $o58_coddot)
    {
        $this->atualizacampos();
        if ($this->o58_orgao == null) {
            $this->erro_sql = " Campo Código Orgão não informado.";
            $this->erro_campo = "o58_orgao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_unidade == null) {
            $this->erro_sql = " Campo Código Unidade não informado.";
            $this->erro_campo = "o58_unidade";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_funcao == null) {
            $this->erro_sql = " Campo Código da Função não informado.";
            $this->erro_campo = "o58_funcao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_subfuncao == null) {
            $this->erro_sql = " Campo Sub Função não informado.";
            $this->erro_campo = "o58_subfuncao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }

        if ($this->o58_programa === null) {
            $this->erro_sql = " Campo Programas Orçamento não informado.";
            $this->erro_campo = "o58_programa";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_projativ == null) {
            $this->erro_sql = " Campo Projetos / Atividades não informado.";
            $this->erro_campo = "o58_projativ";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_codele == null) {
            $this->erro_sql = " Campo Código Elemento não informado.";
            $this->erro_campo = "o58_codele";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_codigo == null) {
            $this->erro_sql = " Campo Tipo de Recurso não informado.";
            $this->erro_campo = "o58_codigo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_valor == null) {
            $this->erro_sql = " Campo Previsão não informado.";
            $this->erro_campo = "o58_valor";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_instit == null) {
            $this->erro_sql = " Campo Instituição não informado.";
            $this->erro_campo = "o58_instit";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_localizadorgastos === null) {
            $this->erro_sql = " Campo Localizador dos Gastos não informado.";
            $this->erro_campo = "o58_localizadorgastos";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_datacriacao == null) {
            $this->o58_datacriacao = "null";
        }
        if ($this->o58_concarpeculiar == null) {
            $this->erro_sql = " Campo C.Peculiar/ C. Aplicação não informado.";
            $this->erro_campo = "o58_concarpeculiar";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o58_esferaorcamentaria == null) {
            $this->o58_esferaorcamentaria = '0';
        }
        $this->o58_anousu = $o58_anousu;
        $this->o58_coddot = $o58_coddot;
        if (($this->o58_anousu == null) || ($this->o58_anousu == "")) {
            $this->erro_sql = " Campo o58_anousu não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (($this->o58_coddot == null) || ($this->o58_coddot == "")) {
            $this->erro_sql = " Campo o58_coddot não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into orcdotacao(
                                       o58_anousu
                                      ,o58_coddot
                                      ,o58_orgao
                                      ,o58_unidade
                                      ,o58_funcao
                                      ,o58_subfuncao
                                      ,o58_programa
                                      ,o58_projativ
                                      ,o58_codele
                                      ,o58_codigo
                                      ,o58_valor
                                      ,o58_instit
                                      ,o58_localizadorgastos
                                      ,o58_datacriacao
                                      ,o58_concarpeculiar
                                      ,o58_esferaorcamentaria
                       )
                values (
                                $this->o58_anousu
                               ,$this->o58_coddot
                               ,$this->o58_orgao
                               ,$this->o58_unidade
                               ,$this->o58_funcao
                               ,$this->o58_subfuncao
                               ,$this->o58_programa
                               ,$this->o58_projativ
                               ,$this->o58_codele
                               ,$this->o58_codigo
                               ,$this->o58_valor
                               ,$this->o58_instit
                               ,$this->o58_localizadorgastos
                               ," . ($this->o58_datacriacao == "null" || $this->o58_datacriacao == "" ? "null" : "'" . $this->o58_datacriacao . "'") . "
                               ,'$this->o58_concarpeculiar'
                               ,$this->o58_esferaorcamentaria
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Dotações Lançadas ($this->o58_anousu." - ".$this->o58_coddot) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Dotações Lançadas já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Dotações Lançadas ($this->o58_anousu." - ".$this->o58_coddot) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->o58_anousu . "-" . $this->o58_coddot;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);

        return true;
    }

    public function alterar($o58_anousu = null, $o58_coddot = null)
    {
        $this->atualizacampos();
        $sql = " update orcdotacao set ";
        $virgula = "";
        if (trim($this->o58_anousu) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_anousu"])) {
            $sql .= $virgula . " o58_anousu = $this->o58_anousu ";
            $virgula = ",";
            if (trim($this->o58_anousu) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "o58_anousu";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_coddot) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_coddot"])) {
            $sql .= $virgula . " o58_coddot = $this->o58_coddot ";
            $virgula = ",";
            if (trim($this->o58_coddot) == null) {
                $this->erro_sql = " Campo Reduzido não informado.";
                $this->erro_campo = "o58_coddot";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_orgao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_orgao"])) {
            $sql .= $virgula . " o58_orgao = $this->o58_orgao ";
            $virgula = ",";
            if (trim($this->o58_orgao) == null) {
                $this->erro_sql = " Campo Código Orgão não informado.";
                $this->erro_campo = "o58_orgao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_unidade) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_unidade"])) {
            $sql .= $virgula . " o58_unidade = $this->o58_unidade ";
            $virgula = ",";
            if (trim($this->o58_unidade) == null) {
                $this->erro_sql = " Campo Código Unidade não informado.";
                $this->erro_campo = "o58_unidade";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_funcao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_funcao"])) {
            $sql .= $virgula . " o58_funcao = $this->o58_funcao ";
            $virgula = ",";
            if (trim($this->o58_funcao) == null) {
                $this->erro_sql = " Campo Código da Função não informado.";
                $this->erro_campo = "o58_funcao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_subfuncao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_subfuncao"])) {
            $sql .= $virgula . " o58_subfuncao = $this->o58_subfuncao ";
            $virgula = ",";
            if (trim($this->o58_subfuncao) == null) {
                $this->erro_sql = " Campo Sub Função não informado.";
                $this->erro_campo = "o58_subfuncao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_programa) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_programa"])) {
            $sql .= $virgula . " o58_programa = $this->o58_programa ";
            $virgula = ",";
            if (trim($this->o58_programa) == null) {
                $this->erro_sql = " Campo Programas Orçamento não informado.";
                $this->erro_campo = "o58_programa";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_projativ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_projativ"])) {
            $sql .= $virgula . " o58_projativ = $this->o58_projativ ";
            $virgula = ",";
            if (trim($this->o58_projativ) == null) {
                $this->erro_sql = " Campo Projetos / Atividades não informado.";
                $this->erro_campo = "o58_projativ";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_codele) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_codele"])) {
            $sql .= $virgula . " o58_codele = $this->o58_codele ";
            $virgula = ",";
            if (trim($this->o58_codele) == null) {
                $this->erro_sql = " Campo Código Elemento não informado.";
                $this->erro_campo = "o58_codele";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_codigo"])) {
            $sql .= $virgula . " o58_codigo = $this->o58_codigo ";
            $virgula = ",";
            if (trim($this->o58_codigo) == null) {
                $this->erro_sql = " Campo Tipo de Recurso não informado.";
                $this->erro_campo = "o58_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_valor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_valor"])) {
            $sql .= $virgula . " o58_valor = $this->o58_valor ";
            $virgula = ",";
            if (trim($this->o58_valor) == null) {
                $this->erro_sql = " Campo Previsão não informado.";
                $this->erro_campo = "o58_valor";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_instit) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_instit"])) {
            $sql .= $virgula . " o58_instit = $this->o58_instit ";
            $virgula = ",";
            if (trim($this->o58_instit) == null) {
                $this->erro_sql = " Campo Instituição não informado.";
                $this->erro_campo = "o58_instit";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_localizadorgastos) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_localizadorgastos"])) {
            $sql .= $virgula . " o58_localizadorgastos = $this->o58_localizadorgastos ";
            $virgula = ",";
            if (trim($this->o58_localizadorgastos) == null) {
                $this->erro_sql = " Campo Localizador dos Gastos não informado.";
                $this->erro_campo = "o58_localizadorgastos";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_datacriacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_datacriacao_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["o58_datacriacao_dia"] != "")) {
            $sql .= $virgula . " o58_datacriacao = '$this->o58_datacriacao' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["o58_datacriacao_dia"])) {
                $sql .= $virgula . " o58_datacriacao = null ";
                $virgula = ",";
            }
        }
        if (trim($this->o58_concarpeculiar) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_concarpeculiar"])) {
            $sql .= $virgula . " o58_concarpeculiar = '$this->o58_concarpeculiar' ";
            $virgula = ",";
            if (trim($this->o58_concarpeculiar) == null) {
                $this->erro_sql = " Campo C.Peculiar/ C. Aplicação não informado.";
                $this->erro_campo = "o58_concarpeculiar";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o58_esferaorcamentaria) != "" || isset($GLOBALS["HTTP_POST_VARS"]["o58_esferaorcamentaria"])) {
            $sql .= $virgula . " o58_esferaorcamentaria = $this->o58_esferaorcamentaria ";
            $virgula = ",";
            if (trim($this->o58_esferaorcamentaria) == null) {
                $this->erro_sql = " Campo Esfera Orçamentária não informado.";
                $this->erro_campo = "o58_esferaorcamentaria";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($o58_anousu != null) {
            $sql .= " o58_anousu = $this->o58_anousu";
        }
        if ($o58_coddot != null) {
            $sql .= " and  o58_coddot = $this->o58_coddot";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Dotações Lançadas não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->o58_anousu . "-" . $this->o58_coddot;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Dotações Lançadas não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->o58_anousu . "-" . $this->o58_coddot;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->o58_anousu . "-" . $this->o58_coddot;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($o58_anousu = null, $o58_coddot = null, $dbwhere = null)
    {
        $sql = " delete from orcdotacao
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o58_anousu)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " o58_anousu = $o58_anousu ";
            }
            if (!empty($o58_coddot)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " o58_coddot = $o58_coddot ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Dotações Lançadas não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $o58_anousu . "-" . $o58_coddot;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Dotações Lançadas não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $o58_anousu . "-" . $o58_coddot;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $o58_anousu . "-" . $o58_coddot;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:orcdotacao";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    /**
     * @param $o58_anousu
     * @param $o58_coddot
     * @param $campos
     * @param $ordem
     * @param $dbwhere
     * @return string
     * @deprecated quando precisamos da fonte de recurso
     */
    public function sql_query($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from orcdotacao ";
        $sql .= "      inner join db_config  on  db_config.codigo = orcdotacao.o58_instit";
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = orcdotacao.o58_codigo";
        $sql .= "      left join complementofonterecurso  on  orctiporec.o15_complemento = o200_sequencial";
        $sql .= "      inner join orcfuncao  on  orcfuncao.o52_funcao = orcdotacao.o58_funcao";
        $sql .= "      inner join orcsubfuncao  on  orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao";
        $sql .= "      inner join orcprograma  on  orcprograma.o54_anousu = orcdotacao.o58_anousu and  orcprograma.o54_programa = orcdotacao.o58_programa";
        $sql .= "      inner join orcelemento  on  orcelemento.o56_codele = orcdotacao.o58_codele and  orcelemento.o56_anousu = orcdotacao.o58_anousu";
        $sql .= "      inner join orcprojativ  on  orcprojativ.o55_anousu = orcdotacao.o58_anousu and  orcprojativ.o55_projativ = orcdotacao.o58_projativ";
        $sql .= "      inner join orcorgao  on  orcorgao.o40_anousu = orcdotacao.o58_anousu and  orcorgao.o40_orgao = orcdotacao.o58_orgao";
        $sql .= "      inner join orcunidade  on  orcunidade.o41_anousu = orcdotacao.o58_anousu and  orcunidade.o41_orgao = orcdotacao.o58_orgao and  orcunidade.o41_unidade = orcdotacao.o58_unidade";
        $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = orcdotacao.o58_concarpeculiar";
        $sql .= "      inner join ppasubtitulolocalizadorgasto  on  ppasubtitulolocalizadorgasto.o11_sequencial = orcdotacao.o58_localizadorgastos";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql .= "      inner join orcproduto  on  orcproduto.o22_codproduto = orcprojativ.o55_orcproduto";
        $sql .= "      inner join orcorgao  as a on   a.o40_anousu = orcunidade.o41_anousu and   a.o40_orgao = orcunidade.o41_orgao";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o58_anousu)) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if (!empty($o58_coddot)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
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

    public function sql_query2($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "
        select {$campos}
          from orcdotacao
          join db_config on db_config.codigo = orcdotacao.o58_instit
          join orctiporec on orctiporec.o15_codigo = orcdotacao.o58_codigo
          join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
               and fonterecurso.exercicio = orcdotacao.o58_anousu
          join complementofonterecurso on orctiporec.o15_complemento = o200_sequencial
          join orcfuncao on orcfuncao.o52_funcao = orcdotacao.o58_funcao
          join orcsubfuncao on orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao
          join orcprograma on orcprograma.o54_anousu = orcdotacao.o58_anousu
               and  orcprograma.o54_programa = orcdotacao.o58_programa
          join orcelemento on orcelemento.o56_codele = orcdotacao.o58_codele
               and  orcelemento.o56_anousu = orcdotacao.o58_anousu
          join orcprojativ on orcprojativ.o55_anousu = orcdotacao.o58_anousu
               and  orcprojativ.o55_projativ = orcdotacao.o58_projativ
          join orcorgao on orcorgao.o40_anousu = orcdotacao.o58_anousu
               and  orcorgao.o40_orgao = orcdotacao.o58_orgao
          join orcunidade on orcunidade.o41_anousu = orcdotacao.o58_anousu
               and  orcunidade.o41_orgao = orcdotacao.o58_orgao
               and  orcunidade.o41_unidade = orcdotacao.o58_unidade
          join concarpeculiar on concarpeculiar.c58_sequencial = orcdotacao.o58_concarpeculiar
          join ppasubtitulolocalizadorgasto on ppasubtitulolocalizadorgasto.o11_sequencial = orcdotacao.o58_localizadorgastos
          join cgm on cgm.z01_numcgm = db_config.numcgm
          join db_tipoinstit on db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit
          join orcproduto on orcproduto.o22_codproduto = orcprojativ.o55_orcproduto
          join orcorgao  as a on   a.o40_anousu = orcunidade.o41_anousu and   a.o40_orgao = orcunidade.o41_orgao";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o58_anousu)) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if (!empty($o58_coddot)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
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

    public function sql_query_file($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from orcdotacao ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o58_anousu)) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if (!empty($o58_coddot)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
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

    function sql_query_ele($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < count($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from orcdotacao ";
        $sql .= "      inner join db_config on db_config.codigo = orcdotacao.o58_instit and db_config.codigo = " . db_getsession("DB_instit");
        $sql .= "      inner join orcelemento  on  orcelemento.o56_codele = orcdotacao.o58_codele and orcelemento.o56_anousu = orcdotacao.o58_anousu";
        $sql .= "      inner join orctiporec on  orcdotacao.o58_codigo = o15_codigo";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($o58_anousu != null) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if ($o58_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < count($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    function sql_query_autoriza($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < count($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from orcdotacao ";
        $sql .= "      inner join db_config  on  db_config.codigo = orcdotacao.o58_instit";
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = orcdotacao.o58_codigo";
        $sql .= "      inner join orcfuncao  on  orcfuncao.o52_funcao = orcdotacao.o58_funcao";
        $sql .= "      inner join orcsubfuncao  on  orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao";
        $sql .= "      inner join orcprograma  on  orcprograma.o54_anousu = orcdotacao.o58_anousu and  orcprograma.o54_programa = orcdotacao.o58_programa";
        $sql .= "      inner join orcprojativ  on  orcprojativ.o55_anousu = orcdotacao.o58_anousu and  orcprojativ.o55_projativ = orcdotacao.o58_projativ";
        $sql .= "      inner join orcelemento  on  orcelemento.o56_codele = orcdotacao.o58_codele and orcelemento.o56_anousu = orcdotacao.o58_anousu ";
        $sql .= "      inner join orcorgao  on  orcorgao.o40_anousu = orcdotacao.o58_anousu and  orcorgao.o40_orgao = orcdotacao.o58_orgao";
        $sql .= "      inner join orcunidade  on  orcunidade.o41_anousu = orcdotacao.o58_anousu and  orcunidade.o41_orgao = orcdotacao.o58_orgao and  orcunidade.o41_unidade = orcdotacao.o58_unidade";
        $sql .= "      inner join orcorgao  as d on   d.o40_anousu = orcunidade.o41_anousu and   d.o40_orgao = orcunidade.o41_orgao";
        $sql .= "      inner join db_departorg on db_departorg.db01_anousu = orcdotacao.o58_anousu and db_departorg.db01_orgao = orcorgao.o40_orgao and db_departorg.db01_unidade = orcunidade.o41_unidade ";
        $sql .= "      inner join db_depart on db_depart.coddepto = db_departorg.db01_coddepto ";
        $sql .= "      inner join db_depusu on db_depusu.coddepto = db_depart.coddepto ";
        $sql .= "      inner join db_usuarios on db_usuarios.id_usuario = db_depusu.id_usuario ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($o58_anousu != null) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if ($o58_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < count($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    function sql_query_dotacao($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < count($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from orcdotacao ";
        $sql .= "      inner join db_config    on db_config.codigo = orcdotacao.o58_instit           ";
        $sql .= " 													  and db_config.codigo = " . db_getsession("DB_instit");
        $sql .= "      inner join orcelemento  on orcelemento.o56_codele    = orcdotacao.o58_codele   ";
        $sql .= "                             and orcelemento.o56_anousu    = orcdotacao.o58_anousu      ";
        $sql .= "      inner join orcprojativ  on orcprojativ.o55_projativ  = orcdotacao.o58_projativ ";
        $sql .= "                             and orcprojativ.o55_anousu    = orcdotacao.o58_anousu ";
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo     = orcdotacao.o58_codigo ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($o58_anousu != null) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if ($o58_coddot != null) {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < count($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_dotacao_finalidade($campos = "*", $ordem = null, $where = null)
    {
        $sWhere = "";
        if (!empty($where)) {
            $sWhere = "where {$where} ";
        }

        $sOrdem = "";
        if (!empty($ordem)) {
            $sOrdem = "order by {$ordem} ";
        }

        $sSql = " select $campos                     ";
        $sSql .= "   from orcdotacao";
        $sSql .= "        left join siconfidotacaofinalidade on c119_coddot = o58_coddot";
        $sSql .= "                                          and c119_anousu = o58_anousu";
        $sSql .= "  $sWhere ";
        $sSql .= "  $sOrdem ";

        return $sSql;
    }

    public function sql_recurso($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "
          select {$campos}
            from orcdotacao
            join orctiporec on orctiporec.o15_codigo = orcdotacao.o58_codigo
            join fonterecurso on orctiporec_id = orctiporec.o15_codigo
                 and exercicio = o58_anousu
            join complementofonterecurso on o200_sequencial = o15_complemento
        ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o58_anousu)) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if (!empty($o58_coddot)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
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

    public function sql_dotacao_planodespesa($o58_anousu = null, $o58_coddot = null, $campos = "*", $ordem = null, $dbwhere = "", $groupBy = null, $utilizaPlanoUniao = 't')
    {
        $sql = "
          select {$campos}
            from orcdotacao
            inner join orcelemento                   on orcelemento.o56_codele  = orcdotacao.o58_codele
                                                    and orcelemento.o56_anousu  = orcdotacao.o58_anousu
            inner join conplanoorcamento             on c60_codcon = o56_codele
                                                    and c60_anousu = o56_anousu
            inner join planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            inner join planodespesa                  on planodespesa.id = planodespesa_id
                                                    and planodespesa.exercicio = c60_anousu
                                                    and planodespesa.uniao = '{$utilizaPlanoUniao}'
            inner join orcprojativ                   on orcprojativ.o55_projativ  = orcdotacao.o58_projativ
                                                    and orcprojativ.o55_anousu    = orcdotacao.o58_anousu
            inner join orcfuncao                     on o52_funcao = o58_funcao
            inner join orcsubfuncao                  on o53_subfuncao = o58_subfuncao
            inner join orctiporec                    on  orctiporec.o15_codigo     = orcdotacao.o58_codigo
            inner join fonterecurso                  on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                                                    and fonterecurso.exercicio = o58_anousu
        ";

        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o58_anousu)) {
                $sql2 .= " where orcdotacao.o58_anousu = $o58_anousu ";
            }
            if (!empty($o58_coddot)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcdotacao.o58_coddot = $o58_coddot ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;

        if (!empty($groupBy)) {
            $sql .= " group by {$groupBy}";
        }

        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }
}
