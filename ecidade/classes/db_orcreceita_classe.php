<?php

class cl_orcreceita
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
    public $o70_anousu = 0;
    public $o70_codrec = 0;
    public $o70_codfon = 0;
    public $o70_codigo = 0;
    public $o70_valor = 0;
    public $o70_reclan = 'f';
    public $o70_instit = 0;
    public $o70_concarpeculiar = null;
    public $o70_datacriacao_dia = null;
    public $o70_datacriacao_mes = null;
    public $o70_datacriacao_ano = null;
    public $o70_datacriacao = null;
    public $o70_orcorgao = null;
    public $o70_orcunidade = null;
    public $o70_esferaorcamentaria = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 o70_anousu = int4 = Exercício
                 o70_codrec = int4 = Código Reduzido
                 o70_codfon = int4 = Código Fonte
                 o70_codigo = int4 = Codigo do Recurso
                 o70_valor = float8 = Valor Previsto
                 o70_reclan = bool = Receita Lançada
                 o70_instit = int4 = Código da Instituição
                 o70_concarpeculiar = varchar(100) = Caracteristica Peculiar
                 o70_datacriacao = date = Data de Criação da Receita
                 o70_orcorgao = int4 = Orgao
                 o70_orcunidade = int4 = Unidade
                 o70_esferaorcamentaria = int4 = Esfera Orçamentária
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("orcreceita");
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
            $this->o70_anousu = ($this->o70_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_anousu"]:$this->o70_anousu);
            $this->o70_codrec = ($this->o70_codrec == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_codrec"]:$this->o70_codrec);
            $this->o70_codfon = ($this->o70_codfon == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_codfon"]:$this->o70_codfon);
            $this->o70_codigo = ($this->o70_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_codigo"]:$this->o70_codigo);
            $this->o70_valor = ($this->o70_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_valor"]:$this->o70_valor);
            $this->o70_reclan = ($this->o70_reclan == "f"?@$GLOBALS["HTTP_POST_VARS"]["o70_reclan"]:$this->o70_reclan);
            $this->o70_instit = ($this->o70_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_instit"]:$this->o70_instit);
            $this->o70_concarpeculiar = ($this->o70_concarpeculiar == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_concarpeculiar"]:$this->o70_concarpeculiar);
            if ($this->o70_datacriacao == "") {
                $this->o70_datacriacao_dia = ($this->o70_datacriacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_datacriacao_dia"]:$this->o70_datacriacao_dia);
                $this->o70_datacriacao_mes = ($this->o70_datacriacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_datacriacao_mes"]:$this->o70_datacriacao_mes);
                $this->o70_datacriacao_ano = ($this->o70_datacriacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_datacriacao_ano"]:$this->o70_datacriacao_ano);
                if ($this->o70_datacriacao_dia != "") {
                    $this->o70_datacriacao = $this->o70_datacriacao_ano."-".$this->o70_datacriacao_mes."-".$this->o70_datacriacao_dia;
                }
            }
            $this->o70_orcorgao = ($this->o70_orcorgao == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_orcorgao"]:$this->o70_orcorgao);
            $this->o70_orcunidade = ($this->o70_orcunidade == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_orcunidade"]:$this->o70_orcunidade);
            $this->o70_esferaorcamentaria = ($this->o70_esferaorcamentaria == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_esferaorcamentaria"]:$this->o70_esferaorcamentaria);
        } else {
            $this->o70_anousu = ($this->o70_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_anousu"]:$this->o70_anousu);
            $this->o70_codrec = ($this->o70_codrec == ""?@$GLOBALS["HTTP_POST_VARS"]["o70_codrec"]:$this->o70_codrec);
        }
    }

    public function incluir($o70_anousu, $o70_codrec)
    {
        $this->atualizacampos();
        if ($this->o70_codfon == null) {
            $this->erro_sql = " Campo Código Fonte não informado.";
            $this->erro_campo = "o70_codfon";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o70_codigo == null) {
            $this->erro_sql = " Campo Codigo do Recurso não informado.";
            $this->erro_campo = "o70_codigo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o70_valor == null) {
            $this->erro_sql = " Campo Valor Previsto não informado.";
            $this->erro_campo = "o70_valor";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o70_reclan == null) {
            $this->erro_sql = " Campo Receita Lançada não informado.";
            $this->erro_campo = "o70_reclan";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o70_instit == null) {
            $this->erro_sql = " Campo Código da Instituição não informado.";
            $this->erro_campo = "o70_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o70_concarpeculiar == null) {
            $this->erro_sql = " Campo Caracteristica Peculiar não informado.";
            $this->erro_campo = "o70_concarpeculiar";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o70_datacriacao == null) {
            $this->o70_datacriacao = "null";
        }
        if ($this->o70_orcorgao == null) {
            $this->o70_orcorgao = "null";
        }
        if ($this->o70_orcunidade == null) {
            $this->o70_orcunidade = "null";
        }
        if ($this->o70_esferaorcamentaria == null) {
            $this->o70_esferaorcamentaria = "null";
        }

        if ($o70_codrec == "" || $o70_codrec == null) {
            $result = db_query("select nextval('orcreceita_o70_codrec_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: orcreceita_o70_codrec_seq do campo: o70_codrec";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->o70_codrec = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from orcreceita_o70_codrec_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $o70_codrec)) {
                $this->erro_sql = " Campo o70_codrec maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->o70_codrec = $o70_codrec;
            }
        }
        if (($this->o70_anousu == null) || ($this->o70_anousu == "")) {
            $this->erro_sql = " Campo o70_anousu não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (($this->o70_codrec == null) || ($this->o70_codrec == "")) {
            $this->erro_sql = " Campo o70_codrec não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into orcreceita(
                                       o70_anousu
                                      ,o70_codrec
                                      ,o70_codfon
                                      ,o70_codigo
                                      ,o70_valor
                                      ,o70_reclan
                                      ,o70_instit
                                      ,o70_concarpeculiar
                                      ,o70_datacriacao
                                      ,o70_orcorgao
                                      ,o70_orcunidade
                                      ,o70_esferaorcamentaria
                       )
                values (
                                $this->o70_anousu
                               ,$this->o70_codrec
                               ,$this->o70_codfon
                               ,$this->o70_codigo
                               ,$this->o70_valor
                               ,'$this->o70_reclan'
                               ,$this->o70_instit
                               ,'$this->o70_concarpeculiar'
                               ,".($this->o70_datacriacao == "null" || $this->o70_datacriacao == ""?"null":"'".$this->o70_datacriacao."'")."
                               ,$this->o70_orcorgao
                               ,$this->o70_orcunidade
                               ,$this->o70_esferaorcamentaria
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Receitas Orçamento ($this->o70_anousu."-".$this->o70_codrec) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Receitas Orçamento já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Receitas Orçamento ($this->o70_anousu."-".$this->o70_codrec) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o70_anousu."-".$this->o70_codrec;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($o70_anousu = null, $o70_codrec = null)
    {
        $this->atualizacampos();
        $sql = " update orcreceita set ";
        $virgula = "";
        if (trim($this->o70_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_anousu"])) {
            $sql  .= $virgula." o70_anousu = $this->o70_anousu ";
            $virgula = ",";
            if (trim($this->o70_anousu) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "o70_anousu";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_codrec)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_codrec"])) {
            $sql  .= $virgula." o70_codrec = $this->o70_codrec ";
            $virgula = ",";
            if (trim($this->o70_codrec) == null) {
                $this->erro_sql = " Campo Código Reduzido não informado.";
                $this->erro_campo = "o70_codrec";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_codfon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_codfon"])) {
            $sql  .= $virgula." o70_codfon = $this->o70_codfon ";
            $virgula = ",";
            if (trim($this->o70_codfon) == null) {
                $this->erro_sql = " Campo Código Fonte não informado.";
                $this->erro_campo = "o70_codfon";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_codigo"])) {
            $sql  .= $virgula." o70_codigo = $this->o70_codigo ";
            $virgula = ",";
            if (trim($this->o70_codigo) == null) {
                $this->erro_sql = " Campo Codigo do Recurso não informado.";
                $this->erro_campo = "o70_codigo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_valor"])) {
            $sql  .= $virgula." o70_valor = $this->o70_valor ";
            $virgula = ",";
            if (trim($this->o70_valor) == null) {
                $this->erro_sql = " Campo Valor Previsto não informado.";
                $this->erro_campo = "o70_valor";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_reclan)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_reclan"])) {
            $sql  .= $virgula." o70_reclan = '$this->o70_reclan' ";
            $virgula = ",";
            if (trim($this->o70_reclan) == null) {
                $this->erro_sql = " Campo Receita Lançada não informado.";
                $this->erro_campo = "o70_reclan";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_instit"])) {
            $sql  .= $virgula." o70_instit = $this->o70_instit ";
            $virgula = ",";
            if (trim($this->o70_instit) == null) {
                $this->erro_sql = " Campo Código da Instituição não informado.";
                $this->erro_campo = "o70_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_concarpeculiar)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_concarpeculiar"])) {
            $sql  .= $virgula." o70_concarpeculiar = '$this->o70_concarpeculiar' ";
            $virgula = ",";
            if (trim($this->o70_concarpeculiar) == null) {
                $this->erro_sql = " Campo Caracteristica Peculiar não informado.";
                $this->erro_campo = "o70_concarpeculiar";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o70_datacriacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_datacriacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["o70_datacriacao_dia"] !="")) {
            $sql  .= $virgula." o70_datacriacao = '$this->o70_datacriacao' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["o70_datacriacao_dia"])) {
                $sql  .= $virgula." o70_datacriacao = null ";
                $virgula = ",";
            }
        }
        if (trim($this->o70_orcorgao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_orcorgao"])) {
            if (trim($this->o70_orcorgao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["o70_orcorgao"])) {
                $this->o70_orcorgao = "null" ;
            }
            $sql  .= $virgula." o70_orcorgao = $this->o70_orcorgao ";
            $virgula = ",";
        }
        if (trim($this->o70_orcunidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_orcunidade"])) {
            if (trim($this->o70_orcunidade)=="" && isset($GLOBALS["HTTP_POST_VARS"]["o70_orcunidade"])) {
                $this->o70_orcunidade = "null" ;
            }
            $sql  .= $virgula." o70_orcunidade = $this->o70_orcunidade ";
            $virgula = ",";
        }
        if (trim($this->o70_esferaorcamentaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o70_esferaorcamentaria"])) {
            if (trim($this->o70_esferaorcamentaria)=="" && isset($GLOBALS["HTTP_POST_VARS"]["o70_esferaorcamentaria"])) {
                $this->o70_esferaorcamentaria = "null" ;
            }
            $sql  .= $virgula." o70_esferaorcamentaria = $this->o70_esferaorcamentaria ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($o70_anousu!=null) {
            $sql .= " o70_anousu = $this->o70_anousu";
        }
        if ($o70_codrec!=null) {
            $sql .= " and  o70_codrec = $this->o70_codrec";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Receitas Orçamento não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->o70_anousu."-".$this->o70_codrec;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Receitas Orçamento não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->o70_anousu."-".$this->o70_codrec;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->o70_anousu."-".$this->o70_codrec;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($o70_anousu = null, $o70_codrec = null, $dbwhere = null)
    {
        $sql = " delete from orcreceita
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o70_anousu)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " o70_anousu = $o70_anousu ";
            }
            if (!empty($o70_codrec)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " o70_codrec = $o70_codrec ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Receitas Orçamento não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$o70_anousu."-".$o70_codrec;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Receitas Orçamento não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$o70_anousu."-".$o70_codrec;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$o70_anousu."-".$o70_codrec;
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
            $this->erro_sql   = "Record Vazio na Tabela:orcreceita";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    /**
     * @deprecated quando quiser ir na tabela fonterecurso
     * @param $o70_anousu
     * @param $o70_codrec
     * @param $campos
     * @param $ordem
     * @param $dbwhere
     * @return string
     */
    public function sql_query($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql  = "select {$campos}";
        $sql .= "  from orcreceita ";
        $sql .= "      inner join db_config  on  db_config.codigo = orcreceita.o70_instit";
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = orcreceita.o70_codigo";
        $sql .= "      inner join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento";
        $sql .= "      inner join orcfontes  on  orcfontes.o57_codfon = orcreceita.o70_codfon and  orcfontes.o57_anousu = orcreceita.o70_anousu";
        $sql .= "      inner join concarpeculiar  on  concarpeculiar.c58_sequencial = orcreceita.o70_concarpeculiar";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit ";
        $sql .= "      left  join orcunidade on o41_unidade = o70_orcunidade ";
        $sql .= "                           and o41_orgao   = o70_orcorgao ";
        $sql .= "                           and o41_anousu  = o70_anousu ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o70_anousu)) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if (!empty($o70_codrec)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
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

    /**
     * @param $o70_anousu
     * @param $o70_codrec
     * @param $campos
     * @param $ordem
     * @param $dbwhere
     * @return string
     */
    public function sql_query2($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "
        select {$campos}
          from orcreceita
          join db_config on db_config.codigo = orcreceita.o70_instit
          join orctiporec on orctiporec.o15_codigo = orcreceita.o70_codigo
          join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
               and fonterecurso.exercicio = orcreceita.o70_anousu
          join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
          join orcfontes  on  orcfontes.o57_codfon = orcreceita.o70_codfon and  orcfontes.o57_anousu = orcreceita.o70_anousu
          join concarpeculiar  on  concarpeculiar.c58_sequencial = orcreceita.o70_concarpeculiar
          join cgm  on  cgm.z01_numcgm = db_config.numcgm
          join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit
          left  join orcunidade on o41_unidade = o70_orcunidade
                     and o41_orgao   = o70_orcorgao
                     and o41_anousu  = o70_anousu ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o70_anousu)) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if (!empty($o70_codrec)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
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

    public function sql_query_file($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from orcreceita ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o70_anousu)) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if (!empty($o70_codrec)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
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

    public function sql_query_migra($anousu, $instit)
    {
        $sql ="select
		   o70_anousu,
		   o70_codrec,
		   o70_codfon,
		   o57_fonte,
		   o57_descr,
		   o70_valor,
		   sum(jan) as jan,
		   sum(fev) as fev,
		   sum(mar) as mar,
		   sum(abr) as abr,
		   sum(mai) as mai,
		   sum(jun) as jun,
		   sum(jul) as jul,
		   sum(ago) as ago,
		   sum(set) as set,
		   sum(out) as out,
		   sum(nov) as nov,
		   sum(dez) as dez
	   from (
		select
		   o70_anousu,
		   o70_codrec,
		   o70_codfon,
		   o57_fonte,
		   o57_descr,
		   o70_valor,
		   case when o71_mes=1 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as jan,
		   case when o71_mes=2 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as fev,
		   case when o71_mes=3 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as mar,
		   case when o71_mes=4 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as abr,
		   case when o71_mes=5 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as mai,
		   case when o71_mes=6 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as jun,
		   case when o71_mes=7 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as jul,
		   case when o71_mes=8 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as ago,
		   case when o71_mes=9 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as set,
		   case when o71_mes=10 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as out,
		   case when o71_mes=11 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as nov,
		   case when o71_mes=12 then sum(case when o71_coddoc=100 then o71_valor else o71_valor*-1 end) end as dez

		from orcreceita
		   left join orcreceitaval on o71_codrec=o70_codrec and o71_anousu=o70_anousu
		   left join orcfontes on o57_codfon=o70_codfon and o57_anousu=o70_anousu

		where o70_anousu=$anousu
		      and o70_instit=$instit

		group by
		   o70_anousu,
		   o70_codrec,
		   o70_codfon,
		   o57_fonte,
		   o57_descr,
		   o71_mes,
		   o70_valor

		) as x
	group by
	   o70_anousu,
	   o70_codrec,
	   o70_codfon,
	   o57_fonte,
	   o57_descr,
	   o70_valor
          ";
        return $sql;
    } // end function

    public function sql_query_plano($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from orcreceita ";
        $sql .= "      inner join db_config      on  db_config.codigo      = orcreceita.o70_instit";
        $sql .= "      inner join orctiporec     on  orctiporec.o15_codigo = orcreceita.o70_codigo";
        $sql .= "      inner join orcfontes      on  orcfontes.o57_codfon  = orcreceita.o70_codfon ";
        $sql .= "                               and  orcfontes.o57_anousu  = orcreceita.o70_anousu";
        $sql .= "      inner join conplanoreduz  on  orcfontes.o57_codfon  = conplanoreduz.c61_codcon ";
        $sql .= "                               and  orcfontes.o57_anousu  = conplanoreduz.c61_anousu";
        $sql .= "                               and  orcreceita.o70_instit = conplanoreduz.c61_instit";
        $sql .= "      inner join concarpeculiar on  concarpeculiar.c58_sequencial = orcreceita.o70_concarpeculiar";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($o70_anousu!=null) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if ($o70_codrec!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return analiseQueryPlanoOrcamento($sql);
    }
    public function sql_query_razao($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from orcreceita ";
        $sql .= "      inner join db_config  on  db_config.codigo = orcreceita.o70_instit and db_config.codigo = ".db_getsession("DB_instit");
        $sql .= "      inner join orctiporec  on  orctiporec.o15_codigo = orcreceita.o70_codigo";
        $sql .= "      inner join orcfontes  on  orcfontes.o57_codfon = orcreceita.o70_codfon and orcfontes.o57_anousu = orcreceita.o70_anousu ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($o70_anousu!=null) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if ($o70_codrec!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_atualizacoesprevisao($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= "       FROM orcreceita                                                        ";
        $sql .= " INNER JOIN db_config    ON db_config.codigo        = orcreceita.o70_instit   ";
        $sql .= " INNER JOIN orcfontes    ON orcfontes.o57_codfon    = orcreceita.o70_codfon   ";
        $sql .= "                        AND orcfontes.o57_anousu    = orcreceita.o70_anousu   ";
        $sql .= " INNER JOIN orcsuplemrec ON orcsuplemrec.o85_anousu = orcreceita.o70_anousu   ";
        $sql .= "                        AND orcsuplemrec.o85_codrec = orcreceita.o70_codrec   ";
        $sql .= " INNER JOIN orcsuplem    ON orcsuplem.o46_codsup    = orcsuplemrec.o85_codsup ";
        $sql .= " INNER JOIN orcsuplemlan ON orcsuplemlan.o49_codsup = orcsuplem.o46_codsup    ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($o70_anousu!=null) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if ($o70_codrec!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_dados_receita($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {


        $sql = "select ";

        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from orcreceita ";
        $sql .= "      inner join db_config                 on db_config.codigo                                = orcreceita.o70_instit                   ";
        $sql .= "                                          and db_config.codigo                                = ".db_getsession("DB_instit")             ;
        $sql .= "      inner join orcfontes                 on orcfontes.o57_codfon                            = orcreceita.o70_codfon                   ";
        $sql .= "                                          and orcfontes.o57_anousu                            = orcreceita.o70_anousu                   ";
        $sql .= "      inner join conplanoorcamento         on orcfontes.o57_codfon                            = conplanoorcamento.c60_codcon            ";
        $sql .= "                                          and orcfontes.o57_anousu                            = conplanoorcamento.c60_anousu                   ";


        $sql2 = "";
        if ($dbwhere == "") {
            if ($o70_anousu!=null) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if ($o70_codrec!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql.$sql2;
    }

    /**
     * query criada para atendener a nova rotina de receita fato gerador
     * será incluso join com as tabelas criadas :
     * aberturaexercicio
     * conlancamaberturaexercicio
     */
    public function sql_queryEstornoReceitaFatoGerador($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select ";

        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula    = "";

            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql    .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from orcreceita                                                                                                                   ";
        $sql .= "      inner join db_config                  on db_config.codigo              = orcreceita.o70_instit                               ";
        $sql .= "      inner join orctiporec                 on orctiporec.o15_codigo         = orcreceita.o70_codigo                               ";
        $sql .= "      inner join orcfontes                  on orcfontes.o57_codfon          = orcreceita.o70_codfon                               ";
        $sql .= "                                           and orcfontes.o57_anousu          = orcreceita.o70_anousu                               ";
        $sql .= "      inner join concarpeculiar             on concarpeculiar.c58_sequencial = orcreceita.o70_concarpeculiar                       ";
        $sql .= "      inner join cgm                        on cgm.z01_numcgm                = db_config.numcgm                                    ";
        $sql .= "      inner join db_tipoinstit              on db_tipoinstit.db21_codtipo    = db_config.db21_tipoinstit                           ";
        $sql .= "      inner join conlancamrec               on orcreceita.o70_codrec         = conlancamrec.c74_codrec                             ";
        $sql .= "                                           and orcreceita.o70_anousu         = conlancamrec.c74_anousu                             ";
        $sql .= "      inner join conlancamaberturaexercicio on conlancamrec.c74_codlan       = conlancamaberturaexercicio.c80_conlancam            ";
        $sql .= "      inner join aberturaexercicio          on conlancamaberturaexercicio.c80_aberturaexercicio = aberturaexercicio.c81_sequencial ";
        $sql .= "                                           and aberturaexercicio.c81_estornado = false                                             ";
        $sql .= "      inner join conlancam                  on conlancamrec.c74_codlan         = conlancam.c70_codlan                              ";

        $sql2 = "";

        if ($dbwhere=="") {
            if ($o70_anousu!=null) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if ($o70_codrec!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_fonte_desdobramento($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select ";

        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula    = "";

            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql    .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from orcreceita                                                                     ";
        $sql .= "      inner join orcfontes      on orcfontes.o57_codfon    = orcreceita.o70_codfon   ";
        $sql .= "                               and orcfontes.o57_anousu    = orcreceita.o70_anousu   ";
        $sql .= "      inner join orcfontesdes   on orcfontesdes.o60_anousu = orcreceita.o70_anousu   ";
        $sql .= "                               and orcfontes.o57_codfon    = orcfontesdes.o60_codfon ";

        $sql2 = "";

        if ($dbwhere=="") {
            if ($o70_anousu!=null) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if ($o70_codrec!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_validacao_receita($o70_anousu = null, $o70_codrec = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select ";

        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula    = "";

            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql    .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from orcreceita ";
        $sql .= "      inner join db_config                 on db_config.codigo                 = orcreceita.o70_instit                   ";
        $sql .= "                                          and db_config.codigo                 = ".db_getsession("DB_instit")             ;
        $sql .= "      inner join orcfontes                 on orcfontes.o57_codfon             = orcreceita.o70_codfon                   ";
        $sql .= "                                          and orcfontes.o57_anousu             = orcreceita.o70_anousu                   ";
        $sql .= "      inner join conplanoorcamento         on orcfontes.o57_codfon             = conplanoorcamento.c60_codcon            ";
        $sql .= "                                          and orcfontes.o57_anousu             = conplanoorcamento.c60_anousu            ";

        $sql .= "      left join conplanoorcamentogrupo    on conplanoorcamentogrupo.c21_codcon = conplanoorcamento.c60_codcon ";
        $sql .= "                                         and conplanoorcamentogrupo.c21_anousu = conplanoorcamento.c60_anousu ";

        $sql.= "        left join conplanoconplanoorcamento   on c72_conplanoorcamento   = conplanoorcamento.c60_codcon ";
        $sql.= "                                              and c72_anousu             = conplanoorcamento.c60_anousu ";
        $sql.= "        left join conplano                    on c72_conplano            = conplano.c60_codcon ";
        $sql.= "                                              and c72_anousu             = conplano.c60_anousu ";
        $sql.= "        left join conplanoreduz               on c61_codcon              = conplano.c60_codcon ";
        $sql.= "                                              and c61_anousu             = conplano.c60_anousu ";




        $sql2 = "";

        if ($dbwhere=="") {
            if ($o70_anousu!=null) {
                $sql2 .= " where orcreceita.o70_anousu = $o70_anousu ";
            }
            if ($o70_codrec!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcreceita.o70_codrec = $o70_codrec ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    /**
     * @param string $campos
     * @param null $ordem
     * @param string $dbwhere
     * @return string
     */
    public function sql_query_receita($campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        $sql .= $campos;

        $sql .= " from orcreceita                                                                     ";
        $sql .= "      inner join orcfontes      on orcfontes.o57_codfon    = orcreceita.o70_codfon   ";
        $sql .= "                               and orcfontes.o57_anousu    = orcreceita.o70_anousu   ";

        if (!empty($dbwhere)) {
            $sql .= " where {$dbwhere} ";
        }
        if (!empty($ordem)) {
            $sql .= " order by {$ordem} ";
        }

        return $sql;
    }

    /**
     * @param string $campos
     * @param null $ordem
    * @param string $dbwhere
     * @return string
     */
    public function sql_query_receita_planoreceita($campos = "*", $ordem = null, $dbwhere = "",$groupBy)
    {
        $sql = "         
            select $campos
                    from orcreceita                                                                                    
                inner join orcfontes on orcfontes.o57_codfon = orcreceita.o70_codfon  
                                    and orcfontes.o57_anousu = orcreceita.o70_anousu  
                inner join conplanoorcamento on c60_codcon = o57_codfon
                                           and c60_anousu = o57_anousu 
                inner join planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
                inner join planoreceita on planoreceita.id = planoreceita_id
                                       and planoreceita.exercicio = o70_anousu
                                       and planoreceita.uniao = 't'
                inner join orctiporec  on  orctiporec.o15_codigo = orcreceita.o70_codigo 
                inner join fonterecurso on fonterecurso.orctiporec_id = o15_codigo
                                        and fonterecurso.exercicio = o70_anousu
        ";
        
        if (!empty($dbwhere)) {
            $sql .= " where {$dbwhere} ";
        }
        if (!empty($ordem)) {
            $sql .= " order by {$ordem} ";
        }
        if (!empty($groupBy)) {
            $sql .= " group by {$groupBy} ";
        }
        return $sql;
    }    
}
