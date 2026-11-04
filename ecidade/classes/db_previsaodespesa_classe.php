<?php
//MODULO: contabilidade
//CLASSE DA ENTIDADE previsaodespesa
class cl_previsaodespesa
{
    // cria variaveis de erro
    var $rotulo = null;
    var $query_sql = null;
    var $numrows = 0;
    var $numrows_incluir = 0;
    var $numrows_alterar = 0;
    var $numrows_excluir = 0;
    var $erro_status = null;
    var $erro_sql = null;
    var $erro_banco = null;
    var $erro_msg = null;
    var $erro_campo = null;
    var $pagina_retorno = null;
    // cria variaveis do arquivo
    var $c333_sequencial = 0;
    var $c333_ano = 0;
    var $c333_esferaorcamentaria = 0;
    var $c333_orcorgao = 0;
    var $c333_orcunidade = 0;
    var $c333_orcfuncao = 0;
    var $c333_orcsubfuncao = 0;
    var $c333_orcprograma = 0;
    var $c333_orcprojativ = 0;
    var $c333_ppasubtitulolocalizadorgasto = 0;
    var $c333_conplanoorcamento = 0;
    var $c333_identificadoruso = 0;
    var $c333_tipodetalhamento = null;
    var $c333_grupofonterecursos = null;
    var $c333_especificacaofonte = null;
    var $c333_identificadorresultadoprimario = null;
    var $c333_previsao = 0;
    var $c333_planoorcamentario = null;
    // cria propriedade com as variaveis do arquivo
    var $campos = "
                 c333_sequencial = int4 = Código 
                 c333_ano = int4 = Ano 
                 c333_esferaorcamentaria = oid = Esfera orçamentária 
                 c333_orcorgao = int4 = Orgão 
                 c333_orcunidade = int4 = Unidade 
                 c333_orcfuncao = int4 = Função 
                 c333_orcsubfuncao = int4 = Subfunção 
                 c333_orcprograma = int4 = Programa 
                 c333_orcprojativ = int4 = Ação 
                 c333_ppasubtitulolocalizadorgasto = int4 = Subtítulo 
                 c333_conplanoorcamento = int4 = Natureza da Despesa 
                 c333_identificadoruso = int4 = Identificador de Uso 
                 c333_tipodetalhamento = varchar(10) = Tipo de Detalhamento 
                 c333_grupofonterecursos = varchar(10) = Grupo da fonte de recursos 
                 c333_especificacaofonte = varchar(10) = Especificação da Fonte 
                 c333_identificadorresultadoprimario = varchar(10) = Identificador de Resultado Primário
                 c333_previsao = float8 = Previsão 
                 c333_planoorcamentario = text = Plano Orçamentáario 
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("previsaodespesa");
        $this->pagina_retorno = basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }

    //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"" . $this->erro_msg . "\");</script>";
            if ($retorna == true) {
                echo "<script>location.href='" . $this->pagina_retorno . "'</script>";
            }
        }
    }

    // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->c333_sequencial = ($this->c333_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_sequencial"] : $this->c333_sequencial);
            $this->c333_ano = ($this->c333_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_ano"] : $this->c333_ano);
            $this->c333_esferaorcamentaria = ($this->c333_esferaorcamentaria == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_esferaorcamentaria"] : $this->c333_esferaorcamentaria);
            $this->c333_orcorgao = ($this->c333_orcorgao == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_orcorgao"] : $this->c333_orcorgao);
            $this->c333_orcunidade = ($this->c333_orcunidade == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_orcunidade"] : $this->c333_orcunidade);
            $this->c333_orcfuncao = ($this->c333_orcfuncao == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_orcfuncao"] : $this->c333_orcfuncao);
            $this->c333_orcsubfuncao = ($this->c333_orcsubfuncao == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_orcsubfuncao"] : $this->c333_orcsubfuncao);
            $this->c333_orcprograma = ($this->c333_orcprograma == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_orcprograma"] : $this->c333_orcprograma);
            $this->c333_orcprojativ = ($this->c333_orcprojativ == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_orcprojativ"] : $this->c333_orcprojativ);
            $this->c333_ppasubtitulolocalizadorgasto = ($this->c333_ppasubtitulolocalizadorgasto == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_ppasubtitulolocalizadorgasto"] : $this->c333_ppasubtitulolocalizadorgasto);
            $this->c333_conplanoorcamento = ($this->c333_conplanoorcamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_conplanoorcamento"] : $this->c333_conplanoorcamento);
            $this->c333_identificadoruso = ($this->c333_identificadoruso == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_identificadoruso"] : $this->c333_identificadoruso);
            $this->c333_tipodetalhamento = ($this->c333_tipodetalhamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_tipodetalhamento"] : $this->c333_tipodetalhamento);
            $this->c333_grupofonterecursos = ($this->c333_grupofonterecursos == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_grupofonterecursos"] : $this->c333_grupofonterecursos);
            $this->c333_especificacaofonte = ($this->c333_especificacaofonte == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_especificacaofonte"] : $this->c333_especificacaofonte);
            $this->c333_identificadorresultadoprimario = ($this->c333_identificadorresultadoprimario == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_identificadorresultadoprimario"] : $this->c333_identificadorresultadoprimario);
            $this->c333_previsao = ($this->c333_previsao == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_previsao"] : $this->c333_previsao);
            $this->c333_planoorcamentario = ($this->c333_planoorcamentario == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_planoorcamentario"] : $this->c333_planoorcamentario);
        } else {
            $this->c333_sequencial = ($this->c333_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["c333_sequencial"] : $this->c333_sequencial);
        }
    }

    // funcao para Inclusão
    public function incluir($c333_sequencial)
    {
        $this->atualizacampos();
        if ($this->c333_ano == null) {
            $this->erro_sql = " Campo Ano não informado.";
            $this->erro_campo = "c333_ano";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_esferaorcamentaria == null) {
            $this->erro_sql = " Campo Esfera orçamentária não informado.";
            $this->erro_campo = "c333_esferaorcamentaria";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_orcorgao == null) {
            $this->erro_sql = " Campo Orgão não informado.";
            $this->erro_campo = "c333_orcorgao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_orcunidade == null) {
            $this->erro_sql = " Campo Unidade não informado.";
            $this->erro_campo = "c333_orcunidade";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_orcfuncao == null) {
            $this->erro_sql = " Campo Função não informado.";
            $this->erro_campo = "c333_orcfuncao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_orcsubfuncao == null) {
            $this->erro_sql = " Campo Subfunção não informado.";
            $this->erro_campo = "c333_orcsubfuncao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_orcprograma == null) {
            $this->erro_sql = " Campo Programa não informado.";
            $this->erro_campo = "c333_orcprograma";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_orcprojativ == null) {
            $this->erro_sql = " Campo Ação não informado.";
            $this->erro_campo = "c333_orcprojativ";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_ppasubtitulolocalizadorgasto == null) {
            $this->erro_sql = " Campo Subtítulo não informado.";
            $this->erro_campo = "c333_ppasubtitulolocalizadorgasto";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_conplanoorcamento == null) {
            $this->erro_sql = " Campo Natureza da Despesa não informado.";
            $this->erro_campo = "c333_conplanoorcamento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_identificadoruso == null) {
            $this->erro_sql = " Campo Identificador de Uso não informado.";
            $this->erro_campo = "c333_identificadoruso";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_tipodetalhamento == null) {
            $this->erro_sql = " Campo Tipo de Detalhamento não informado.";
            $this->erro_campo = "c333_tipodetalhamento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_grupofonterecursos == null) {
            $this->erro_sql = " Campo Grupo da fonte de recursos não informado.";
            $this->erro_campo = "c333_grupofonterecursos";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_especificacaofonte == null) {
            $this->erro_sql = " Campo Especificação da Fonte não informado.";
            $this->erro_campo = "c333_especificacaofonte";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_identificadorresultadoprimario == null) {
            $this->erro_sql = " Campo Identificador de Resultado Primário não informado.";
            $this->erro_campo = "c333_identificadorresultadoprimario";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_previsao == null) {
            $this->erro_sql = " Campo Previsão não informado.";
            $this->erro_campo = "c333_previsao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($this->c333_planoorcamentario == null) {
            $this->erro_sql = " Campo Plano Orçamentáario não informado.";
            $this->erro_campo = "c333_planoorcamentario";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        if ($c333_sequencial == "" || $c333_sequencial == null) {
            $result = db_query("select nextval('previsaodespesa_c333_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: previsaodespesa_c333_sequencial_seq do campo: c333_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
            $this->c333_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from previsaodespesa_c333_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $c333_sequencial)) {
                $this->erro_sql = " Campo c333_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            } else {
                $this->c333_sequencial = $c333_sequencial;
            }
        }
        if (($this->c333_sequencial == null) || ($this->c333_sequencial == "")) {
            $this->erro_sql = " Campo c333_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        $sql = "insert into previsaodespesa(
                                       c333_sequencial 
                                      ,c333_ano 
                                      ,c333_esferaorcamentaria 
                                      ,c333_orcorgao 
                                      ,c333_orcunidade 
                                      ,c333_orcfuncao 
                                      ,c333_orcsubfuncao 
                                      ,c333_orcprograma 
                                      ,c333_orcprojativ 
                                      ,c333_ppasubtitulolocalizadorgasto 
                                      ,c333_conplanoorcamento 
                                      ,c333_identificadoruso 
                                      ,c333_tipodetalhamento 
                                      ,c333_grupofonterecursos 
                                      ,c333_especificacaofonte 
                                      ,c333_identificadorresultadoprimario 
                                      ,c333_previsao 
                                      ,c333_planoorcamentario 
                       )
                values (
                                $this->c333_sequencial 
                               ,$this->c333_ano 
                               ,$this->c333_esferaorcamentaria 
                               ,$this->c333_orcorgao 
                               ,$this->c333_orcunidade 
                               ,$this->c333_orcfuncao 
                               ,$this->c333_orcsubfuncao 
                               ,$this->c333_orcprograma 
                               ,$this->c333_orcprojativ 
                               ,$this->c333_ppasubtitulolocalizadorgasto 
                               ,$this->c333_conplanoorcamento 
                               ,$this->c333_identificadoruso 
                               ,'$this->c333_tipodetalhamento' 
                               ,'$this->c333_grupofonterecursos' 
                               ,'$this->c333_especificacaofonte' 
                               ,'$this->c333_identificadorresultadoprimario'
                               ,$this->c333_previsao 
                               ,'$this->c333_planoorcamentario' 
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "previsaodespesa ($this->c333_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "previsaodespesa já Cadastrado";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "previsaodespesa ($this->c333_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;

            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->c333_sequencial;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "",
            str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->c333_sequencial));
            if (($resaco != false) || ($this->numrows != 0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,1009818,'$this->c333_sequencial','I')");
                $resac = db_query("insert into db_acount values($acount,1010295,1009818,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009819,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_ano')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009820,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_esferaorcamentaria')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009821,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_orcorgao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009822,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_orcunidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009823,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_orcfuncao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009824,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_orcsubfuncao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009827,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_orcprograma')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009825,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_orcprojativ')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009826,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_ppasubtitulolocalizadorgasto')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009828,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_conplanoorcamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009829,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_identificadoruso')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009830,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_tipodetalhamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009831,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_grupofonterecursos')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009832,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_especificacaofonte')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009833,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_identificadorresultadoprimario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009836,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_previsao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1010295,1009837,'','" . AddSlashes(pg_result($resaco,
                        0,
                        'c333_planoorcamentario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }

        return true;
    }

    // funcao para alteracao
    public function alterar($c333_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update previsaodespesa set ";
        $virgula = "";
        if (trim($this->c333_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_sequencial"])) {
            $sql .= $virgula . " c333_sequencial = $this->c333_sequencial ";
            $virgula = ",";
            if (trim($this->c333_sequencial) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "c333_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_ano) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_ano"])) {
            $sql .= $virgula . " c333_ano = $this->c333_ano ";
            $virgula = ",";
            if (trim($this->c333_ano) == null) {
                $this->erro_sql = " Campo Ano não informado.";
                $this->erro_campo = "c333_ano";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_esferaorcamentaria) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_esferaorcamentaria"])) {
            $sql .= $virgula . " c333_esferaorcamentaria = $this->c333_esferaorcamentaria ";
            $virgula = ",";
            if (trim($this->c333_esferaorcamentaria) == null) {
                $this->erro_sql = " Campo Esfera orçamentária não informado.";
                $this->erro_campo = "c333_esferaorcamentaria";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_orcorgao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_orcorgao"])) {
            $sql .= $virgula . " c333_orcorgao = $this->c333_orcorgao ";
            $virgula = ",";
            if (trim($this->c333_orcorgao) == null) {
                $this->erro_sql = " Campo Orgão não informado.";
                $this->erro_campo = "c333_orcorgao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_orcunidade) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_orcunidade"])) {
            $sql .= $virgula . " c333_orcunidade = $this->c333_orcunidade ";
            $virgula = ",";
            if (trim($this->c333_orcunidade) == null) {
                $this->erro_sql = " Campo Unidade não informado.";
                $this->erro_campo = "c333_orcunidade";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_orcfuncao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_orcfuncao"])) {
            $sql .= $virgula . " c333_orcfuncao = $this->c333_orcfuncao ";
            $virgula = ",";
            if (trim($this->c333_orcfuncao) == null) {
                $this->erro_sql = " Campo Função não informado.";
                $this->erro_campo = "c333_orcfuncao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_orcsubfuncao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_orcsubfuncao"])) {
            $sql .= $virgula . " c333_orcsubfuncao = $this->c333_orcsubfuncao ";
            $virgula = ",";
            if (trim($this->c333_orcsubfuncao) == null) {
                $this->erro_sql = " Campo Subfunção não informado.";
                $this->erro_campo = "c333_orcsubfuncao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_orcprograma) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_orcprograma"])) {
            $sql .= $virgula . " c333_orcprograma = $this->c333_orcprograma ";
            $virgula = ",";
            if (trim($this->c333_orcprograma) == null) {
                $this->erro_sql = " Campo Programa não informado.";
                $this->erro_campo = "c333_orcprograma";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_orcprojativ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_orcprojativ"])) {
            $sql .= $virgula . " c333_orcprojativ = $this->c333_orcprojativ ";
            $virgula = ",";
            if (trim($this->c333_orcprojativ) == null) {
                $this->erro_sql = " Campo Ação não informado.";
                $this->erro_campo = "c333_orcprojativ";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_ppasubtitulolocalizadorgasto) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_ppasubtitulolocalizadorgasto"])) {
            $sql .= $virgula . " c333_ppasubtitulolocalizadorgasto = $this->c333_ppasubtitulolocalizadorgasto ";
            $virgula = ",";
            if (trim($this->c333_ppasubtitulolocalizadorgasto) == null) {
                $this->erro_sql = " Campo Subtítulo não informado.";
                $this->erro_campo = "c333_ppasubtitulolocalizadorgasto";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_conplanoorcamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_conplanoorcamento"])) {
            $sql .= $virgula . " c333_conplanoorcamento = $this->c333_conplanoorcamento ";
            $virgula = ",";
            if (trim($this->c333_conplanoorcamento) == null) {
                $this->erro_sql = " Campo Natureza da Despesa não informado.";
                $this->erro_campo = "c333_conplanoorcamento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_identificadoruso) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_identificadoruso"])) {
            $sql .= $virgula . " c333_identificadoruso = $this->c333_identificadoruso ";
            $virgula = ",";
            if (trim($this->c333_identificadoruso) == null) {
                $this->erro_sql = " Campo Identificador de Uso não informado.";
                $this->erro_campo = "c333_identificadoruso";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_tipodetalhamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_tipodetalhamento"])) {
            $sql .= $virgula . " c333_tipodetalhamento = '$this->c333_tipodetalhamento' ";
            $virgula = ",";
            if (trim($this->c333_tipodetalhamento) == null) {
                $this->erro_sql = " Campo Tipo de Detalhamento não informado.";
                $this->erro_campo = "c333_tipodetalhamento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_grupofonterecursos) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_grupofonterecursos"])) {
            $sql .= $virgula . " c333_grupofonterecursos = '$this->c333_grupofonterecursos' ";
            $virgula = ",";
            if (trim($this->c333_grupofonterecursos) == null) {
                $this->erro_sql = " Campo Grupo da fonte de recursos não informado.";
                $this->erro_campo = "c333_grupofonterecursos";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_especificacaofonte) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_especificacaofonte"])) {
            $sql .= $virgula . " c333_especificacaofonte = '$this->c333_especificacaofonte' ";
            $virgula = ",";
            if (trim($this->c333_especificacaofonte) == null) {
                $this->erro_sql = " Campo Especificação da Fonte não informado.";
                $this->erro_campo = "c333_especificacaofonte";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_identificadorresultadoprimario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_identificadorresultadoprimario"])) {
            $sql .= $virgula . " c333_identificadorresultadoprimario = '$this->c333_identificadorresultadoprimario' ";
            $virgula = ",";
            if (trim($this->c333_identificadorresultadoprimario) == null) {
                $this->erro_sql = " Campo Identificador de Resultado Primário não informado.";
                $this->erro_campo = "c333_identificadorresultadoprimario";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_previsao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_previsao"])) {
            $sql .= $virgula . " c333_previsao = $this->c333_previsao ";
            $virgula = ",";
            if (trim($this->c333_previsao) == null) {
                $this->erro_sql = " Campo Previsão não informado.";
                $this->erro_campo = "c333_previsao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        if (trim($this->c333_planoorcamentario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["c333_planoorcamentario"])) {
            $sql .= $virgula . " c333_planoorcamentario = '$this->c333_planoorcamentario' ";
            $virgula = ",";
            if (trim($this->c333_planoorcamentario) == null) {
                $this->erro_sql = " Campo Plano Orçamentáario não informado.";
                $this->erro_campo = "c333_planoorcamentario";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }
        $sql .= " where ";
        if ($c333_sequencial != null) {
            $sql .= " c333_sequencial = $this->c333_sequencial";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->c333_sequencial));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1009818,'$this->c333_sequencial','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_sequencial"]) || $this->c333_sequencial != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009818,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_sequencial')) . "','$this->c333_sequencial'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_ano"]) || $this->c333_ano != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009819,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_ano')) . "','$this->c333_ano'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_esferaorcamentaria"]) || $this->c333_esferaorcamentaria != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009820,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_esferaorcamentaria')) . "','$this->c333_esferaorcamentaria'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_orcorgao"]) || $this->c333_orcorgao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009821,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_orcorgao')) . "','$this->c333_orcorgao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_orcunidade"]) || $this->c333_orcunidade != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009822,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_orcunidade')) . "','$this->c333_orcunidade'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_orcfuncao"]) || $this->c333_orcfuncao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009823,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_orcfuncao')) . "','$this->c333_orcfuncao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_orcsubfuncao"]) || $this->c333_orcsubfuncao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009824,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_orcsubfuncao')) . "','$this->c333_orcsubfuncao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_orcprograma"]) || $this->c333_orcprograma != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009827,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_orcprograma')) . "','$this->c333_orcprograma'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_orcprojativ"]) || $this->c333_orcprojativ != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009825,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_orcprojativ')) . "','$this->c333_orcprojativ'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_ppasubtitulolocalizadorgasto"]) || $this->c333_ppasubtitulolocalizadorgasto != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009826,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_ppasubtitulolocalizadorgasto')) . "','$this->c333_ppasubtitulolocalizadorgasto'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_conplanoorcamento"]) || $this->c333_conplanoorcamento != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009828,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_conplanoorcamento')) . "','$this->c333_conplanoorcamento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_identificadoruso"]) || $this->c333_identificadoruso != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009829,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_identificadoruso')) . "','$this->c333_identificadoruso'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_tipodetalhamento"]) || $this->c333_tipodetalhamento != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009830,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_tipodetalhamento')) . "','$this->c333_tipodetalhamento'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_grupofonterecursos"]) || $this->c333_grupofonterecursos != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009831,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_grupofonterecursos')) . "','$this->c333_grupofonterecursos'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_especificacaofonte"]) || $this->c333_especificacaofonte != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009832,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_especificacaofonte')) . "','$this->c333_especificacaofonte'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_identificadorresultadoprimario"]) || $this->c333_identificadorresultadoprimario != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009833,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_identificadorresultadoprimario')) . "','$this->c333_identificadorresultadoprimario'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_previsao"]) || $this->c333_previsao != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009836,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_previsao')) . "','$this->c333_previsao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["c333_planoorcamentario"]) || $this->c333_planoorcamentario != "") {
                        $resac = db_query("insert into db_acount values($acount,1010295,1009837,'" . AddSlashes(pg_result($resaco,
                                $conresaco,
                                'c333_planoorcamentario')) . "','$this->c333_planoorcamentario'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "previsaodespesa não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->c333_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "previsaodespesa não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->c333_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $this->c333_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);

                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($c333_sequencial = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($c333_sequencial));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,1009818,'$c333_sequencial','E')");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009818,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_sequencial')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009819,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_ano')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009820,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_esferaorcamentaria')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009821,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_orcorgao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009822,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_orcunidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009823,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_orcfuncao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009824,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_orcsubfuncao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009827,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_orcprograma')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009825,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_orcprojativ')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009826,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_ppasubtitulolocalizadorgasto')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009828,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_conplanoorcamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009829,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_identificadoruso')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009830,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_tipodetalhamento')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009831,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_grupofonterecursos')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009832,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_especificacaofonte')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009833,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_identificadorresultadoprimario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009836,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_previsao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1010295,1009837,'','" . AddSlashes(pg_result($resaco,
                            $iresaco,
                            'c333_planoorcamentario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from previsaodespesa
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c333_sequencial)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " c333_sequencial = $c333_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "previsaodespesa não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $c333_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;

            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "previsaodespesa não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $c333_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;

                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : " . $c333_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                    str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);

                return true;
            }
        }
    }

    // funcao do recordset
    public function sql_record($sql)
    {
        $result = db_query($sql);
        if (!$result) {
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:previsaodespesa";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "",
                str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";

            return false;
        }

        return $result;
    }

    // funcao do sql
    public function sql_query($c333_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from previsaodespesa ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c333_sequencial)) {
                $sql2 .= " where previsaodespesa.c333_sequencial = $c333_sequencial ";
            }
        } else {
            if (!empty($dbwhere)) {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }

        return $sql;
    }

    // funcao do sql
    public function sql_query_file($c333_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from previsaodespesa ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($c333_sequencial)) {
                $sql2 .= " where previsaodespesa.c333_sequencial = $c333_sequencial ";
            }
        } else {
            if (!empty($dbwhere)) {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }

        return $sql;
    }

    // funcao do sql
    public function sql_previsao_despesa($c333_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "
        select {$campos} 
          from previsaodespesa 
          join orcunidade on o41_orgao = c333_orcorgao
                         and o41_unidade = c333_orcunidade
                         and o41_anousu = c333_ano
          join orcfuncao on o52_funcao = c333_orcfuncao
          join orcsubfuncao on o53_subfuncao = c333_orcsubfuncao
          join orcprograma on o54_programa = c333_orcprograma
                          and o54_anousu =  c333_ano
          join orcprojativ on o55_projativ = c333_orcprojativ
                          and o55_anousu = c333_ano
          join ppasubtitulolocalizadorgasto on o11_sequencial = c333_ppasubtitulolocalizadorgasto
          join conplanoorcamento on c60_codcon = c333_conplanoorcamento
                                and c60_anousu = c333_ano
          join orcorgao on o40_anousu = o41_anousu 
                       and o40_orgao = o41_orgao
        ";
        if (empty($dbwhere)) {
            if (!empty($c333_sequencial)) {
                $sql .= " where previsaodespesa.c333_sequencial = $c333_sequencial ";
            }
        } else {
            if (!empty($dbwhere)) {
                $sql .= " where $dbwhere";
            }
        }
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }

        return $sql;
    }
}
