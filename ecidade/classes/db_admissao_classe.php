<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

//MODULO: recursoshumanos
//CLASSE DA ENTIDADE admissao
class cl_admissao
{
    // cria variaveis de erro
    public $rotulo = null;
    public $query_sql = null;
    public $numrows = 0;
    public $erro_status = null;
    public $erro_sql = null;
    public $erro_banco = null;
    public $erro_msg = null;
    public $erro_campo = null;
    public $pagina_retorno = null;
    // cria variaveis do arquivo
    public $h07_regist = 0;
    public $h07_tipadm = null;
    public $h07_dato_dia = null;
    public $h07_dato_mes = null;
    public $h07_dato_ano = null;
    public $h07_dato = null;
    public $h07_cant = null;
    public $h07_dhist_dia = null;
    public $h07_dhist_mes = null;
    public $h07_dhist_ano = null;
    public $h07_dhist = null;
    public $h07_ddem_dia = null;
    public $h07_ddem_mes = null;
    public $h07_ddem_ano = null;
    public $h07_ddem = null;
    public $h07_icon = null;
    public $h07_ires = null;
    public $h07_class = null;
    public $h07_refe = null;
    public $h07_area = null;
    public $h07_nrato = null;
    public $h07_impofi = null;
    public $h07_nrfich = null;
    public $h07_dpubl_dia = null;
    public $h07_dpubl_mes = null;
    public $h07_dpubl_ano = null;
    public $h07_dpubl = null;
    public $h07_fundam = null;
    public $h07_defet_dia = null;
    public $h07_defet_mes = null;
    public $h07_defet_ano = null;
    public $h07_defet = null;
    public $h07_tempor = 'f';
    public $h07_termin_dia = null;
    public $h07_termin_mes = null;
    public $h07_termin_ano = null;
    public $h07_termin = null;
    public $h07_justif = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 h07_regist = int4 = Codigo do Funcionario
                 h07_tipadm = varchar(2) = tipo de admissao
                 h07_dato = date = Data do Ato
                 h07_cant = varchar(5) = Cargo Anterior
                 h07_dhist = date = Data do Historico
                 h07_ddem = date = Demissão
                 h07_icon = varchar(1) = Concurso
                 h07_ires = varchar(1) = Responsavel
                 h07_class = int4 = Classificação
                 h07_refe = int4 = Referência (Concurso)
                 h07_area = int4 = Codigo da Area
                 h07_nrato = varchar(12) = No. do Ato
                 h07_impofi = varchar(30) = Imprensa Oficial
                 h07_nrfich = varchar(6) = Ficha
                 h07_dpubl = date = Publicação
                 h07_fundam = int4 = Fundamentação
                 h07_defet = date = Efetivação
                 h07_tempor = bool = Temporário
                 h07_termin = date = Término
                 h07_justif = varchar(100) = Justificativa
                 ";

    //funcao construtor da classe
    public function __construct()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("admissao");
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
            $this->h07_regist = ($this->h07_regist == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_regist"] : $this->h07_regist);
            $this->h07_tipadm = ($this->h07_tipadm == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_tipadm"] : $this->h07_tipadm);
            if ($this->h07_dato == "") {
                $this->h07_dato_dia = @$GLOBALS["HTTP_POST_VARS"]["h07_dato_dia"];
                $this->h07_dato_mes = @$GLOBALS["HTTP_POST_VARS"]["h07_dato_mes"];
                $this->h07_dato_ano = @$GLOBALS["HTTP_POST_VARS"]["h07_dato_ano"];
                if ($this->h07_dato_dia != "") {
                    $this->h07_dato = $this->h07_dato_ano . "-" . $this->h07_dato_mes . "-" . $this->h07_dato_dia;
                }
            }
            $this->h07_cant = ($this->h07_cant == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_cant"] : $this->h07_cant);
            if ($this->h07_dhist == "") {
                $this->h07_dhist_dia = @$GLOBALS["HTTP_POST_VARS"]["h07_dhist_dia"];
                $this->h07_dhist_mes = @$GLOBALS["HTTP_POST_VARS"]["h07_dhist_mes"];
                $this->h07_dhist_ano = @$GLOBALS["HTTP_POST_VARS"]["h07_dhist_ano"];
                if ($this->h07_dhist_dia != "") {
                    $this->h07_dhist = $this->h07_dhist_ano . "-" . $this->h07_dhist_mes . "-" . $this->h07_dhist_dia;
                }
            }
            if ($this->h07_ddem == "") {
                $this->h07_ddem_dia = @$GLOBALS["HTTP_POST_VARS"]["h07_ddem_dia"];
                $this->h07_ddem_mes = @$GLOBALS["HTTP_POST_VARS"]["h07_ddem_mes"];
                $this->h07_ddem_ano = @$GLOBALS["HTTP_POST_VARS"]["h07_ddem_ano"];
                if ($this->h07_ddem_dia != "") {
                    $this->h07_ddem = $this->h07_ddem_ano . "-" . $this->h07_ddem_mes . "-" . $this->h07_ddem_dia;
                }
            }
            $this->h07_icon = ($this->h07_icon == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_icon"] : $this->h07_icon);
            $this->h07_ires = ($this->h07_ires == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_ires"] : $this->h07_ires);
            $this->h07_class = ($this->h07_class == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_class"] : $this->h07_class);
            $this->h07_refe = ($this->h07_refe == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_refe"] : $this->h07_refe);
            $this->h07_area = ($this->h07_area == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_area"] : $this->h07_area);
            $this->h07_nrato = ($this->h07_nrato == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_nrato"] : $this->h07_nrato);
            $this->h07_impofi = ($this->h07_impofi == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_impofi"] : $this->h07_impofi);
            $this->h07_nrfich = ($this->h07_nrfich == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_nrfich"] : $this->h07_nrfich);
            if ($this->h07_dpubl == "") {
                $this->h07_dpubl_dia = @$GLOBALS["HTTP_POST_VARS"]["h07_dpubl_dia"];
                $this->h07_dpubl_mes = @$GLOBALS["HTTP_POST_VARS"]["h07_dpubl_mes"];
                $this->h07_dpubl_ano = @$GLOBALS["HTTP_POST_VARS"]["h07_dpubl_ano"];
                if ($this->h07_dpubl_dia != "") {
                    $this->h07_dpubl = $this->h07_dpubl_ano . "-" . $this->h07_dpubl_mes . "-" . $this->h07_dpubl_dia;
                }
            }
            $this->h07_fundam = ($this->h07_fundam == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_fundam"] : $this->h07_fundam);
            if ($this->h07_defet == "") {
                $this->h07_defet_dia = @$GLOBALS["HTTP_POST_VARS"]["h07_defet_dia"];
                $this->h07_defet_mes = @$GLOBALS["HTTP_POST_VARS"]["h07_defet_mes"];
                $this->h07_defet_ano = @$GLOBALS["HTTP_POST_VARS"]["h07_defet_ano"];
                if ($this->h07_defet_dia != "") {
                    $this->h07_defet = $this->h07_defet_ano . "-" . $this->h07_defet_mes . "-" . $this->h07_defet_dia;
                }
            }
            $this->h07_tempor = ($this->h07_tempor == "f" ? @$GLOBALS["HTTP_POST_VARS"]["h07_tempor"] : $this->h07_tempor);
            if ($this->h07_termin == "") {
                $this->h07_termin_dia = @$GLOBALS["HTTP_POST_VARS"]["h07_termin_dia"];
                $this->h07_termin_mes = @$GLOBALS["HTTP_POST_VARS"]["h07_termin_mes"];
                $this->h07_termin_ano = @$GLOBALS["HTTP_POST_VARS"]["h07_termin_ano"];
                if ($this->h07_termin_dia != "") {
                    $this->h07_termin = $this->h07_termin_ano . "-" . $this->h07_termin_mes . "-" . $this->h07_termin_dia;
                }
            }
            $this->h07_justif = ($this->h07_justif == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_justif"] : $this->h07_justif);
        } else {
            $this->h07_regist = ($this->h07_regist == "" ? @$GLOBALS["HTTP_POST_VARS"]["h07_regist"] : $this->h07_regist);
        }
    }

    // funcao para inclusao
    public function incluir($h07_regist)
    {
        $this->atualizacampos();
        if ($this->h07_dato == null) {
            $this->h07_dato = "null";
        }
        if ($this->h07_dhist == null) {
            $this->h07_dhist = "null";
        }
        if ($this->h07_ddem == null) {
            $this->h07_ddem = "null";
        }
        if ($this->h07_class == '') {
            $this->h07_class = "null";
        }
        if ($this->h07_refe == '') {
            $this->h07_refe = "null";
        }
        if ($this->h07_area == '') {
            $this->h07_area = "null";
        }
        if ($this->h07_dpubl == null) {
            $this->h07_dpubl = "null";
        }
        if ($this->h07_fundam == '') {
            $this->h07_fundam = "null";
        }
        if ($this->h07_defet == null) {
            $this->h07_defet = "null";
        }
        if ($this->h07_tempor == null) {
            $this->h07_tempor = "f";
        }
        if ($this->h07_termin == null) {
            $this->h07_termin = "null";
        }
        $this->h07_regist = $h07_regist;
        if (($this->h07_regist == null) || ($this->h07_regist == "")) {
            $this->erro_sql = " Campo h07_regist nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $result = @db_query("insert into admissao(
                                       h07_regist
                                      ,h07_tipadm
                                      ,h07_dato
                                      ,h07_cant
                                      ,h07_dhist
                                      ,h07_ddem
                                      ,h07_icon
                                      ,h07_ires
                                      ,h07_class
                                      ,h07_refe
                                      ,h07_area
                                      ,h07_nrato
                                      ,h07_impofi
                                      ,h07_nrfich
                                      ,h07_dpubl
                                      ,h07_fundam
                                      ,h07_defet
                                      ,h07_tempor
                                      ,h07_termin
                                      ,h07_justif
                       )
                values (
                                $this->h07_regist
                               ,'$this->h07_tipadm'
                               ," . ($this->h07_dato == "null" || $this->h07_dato == "" ? "null" : "'" . $this->h07_dato . "'") . "
                               ,'$this->h07_cant'
                               ," . ($this->h07_dhist == "null" || $this->h07_dhist == "" ? "null" : "'" . $this->h07_dhist . "'") . "
                               ," . ($this->h07_ddem == "null" || $this->h07_ddem == "" ? "null" : "'" . $this->h07_ddem . "'") . "
                               ,'$this->h07_icon'
                               ,'$this->h07_ires'
                               ,$this->h07_class
                               ,$this->h07_refe
                               ,$this->h07_area
                               ,'$this->h07_nrato'
                               ,'$this->h07_impofi'
                               ,'$this->h07_nrfich'
                               ," . ($this->h07_dpubl == "null" || $this->h07_dpubl == "" ? "null" : "'" . $this->h07_dpubl . "'") . "
                               ,$this->h07_fundam
                               ," . ($this->h07_defet == "null" || $this->h07_defet == "" ? "null" : "'" . $this->h07_defet . "'") . "
                               ,'$this->h07_tempor'
                               ," . ($this->h07_termin == "null" || $this->h07_termin == "" ? "null" : "'" . $this->h07_termin . "'") . "
                               ,'$this->h07_justif'
                      )");
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Cadastro de Admissoes                              ($this->h07_regist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Cadastro de Admissoes                              já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Cadastro de Admissoes                              ($this->h07_regist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->h07_regist;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $resaco = $this->sql_record($this->sql_query_file($this->h07_regist));
        if (($resaco != false) || ($this->numrows != 0)) {
            $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
            $acount = pg_result($resac, 0, 0);
            $resac = db_query("insert into db_acountkey values($acount,3622,'$this->h07_regist','I')");
            $resac = db_query("insert into db_acount values($acount,524,3622,'','" . pg_result($resaco, 0, 'h07_regist') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3623,'','" . pg_result($resaco, 0, 'h07_tipadm') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3624,'','" . pg_result($resaco, 0, 'h07_dato') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3625,'','" . pg_result($resaco, 0, 'h07_cant') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3626,'','" . pg_result($resaco, 0, 'h07_dhist') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3627,'','" . pg_result($resaco, 0, 'h07_ddem') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3628,'','" . pg_result($resaco, 0, 'h07_icon') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3629,'','" . pg_result($resaco, 0, 'h07_ires') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3630,'','" . pg_result($resaco, 0, 'h07_class') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3631,'','" . pg_result($resaco, 0, 'h07_refe') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3632,'','" . pg_result($resaco, 0, 'h07_area') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4612,'','" . pg_result($resaco, 0, 'h07_nrato') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4614,'','" . pg_result($resaco, 0, 'h07_impofi') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4613,'','" . pg_result($resaco, 0, 'h07_nrfich') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4615,'','" . pg_result($resaco, 0, 'h07_dpubl') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4616,'','" . pg_result($resaco, 0, 'h07_fundam') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4617,'','" . pg_result($resaco, 0, 'h07_defet') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4618,'','" . pg_result($resaco, 0, 'h07_tempor') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4619,'','" . pg_result($resaco, 0, 'h07_termin') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4620,'','" . pg_result($resaco, 0, 'h07_justif') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
        return true;
    }

    // funcao para alteracao
    public function alterar($h07_regist = null)
    {
        $this->atualizacampos();
        $sql = " update admissao set ";
        $virgula = "";
        if (trim($this->h07_regist) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_regist"])) {
            if (trim($this->h07_regist) == "" && isset($GLOBALS["HTTP_POST_VARS"]["h07_regist"])) {
                $this->h07_regist = "0";
            }
            $sql .= $virgula . " h07_regist = $this->h07_regist ";
            $virgula = ",";
            if (trim($this->h07_regist) == null) {
                $this->erro_sql = " Campo Codigo do Funcionario nao Informado.";
                $this->erro_campo = "h07_regist";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->h07_tipadm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_tipadm"])) {
            $sql .= $virgula . " h07_tipadm = '$this->h07_tipadm' ";
            $virgula = ",";
        }
        if (trim($this->h07_dato) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_dato_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["h07_dato_dia"] != "")) {
            $sql .= $virgula . " h07_dato = '$this->h07_dato' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_dato_dia"])) {
                $sql .= $virgula . " h07_dato = null ";
                $virgula = ",";
            }
        }
        if (trim($this->h07_cant) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_cant"])) {
            $sql .= $virgula . " h07_cant = '$this->h07_cant' ";
            $virgula = ",";
        }
        if (trim($this->h07_dhist) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_dhist_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["h07_dhist_dia"] != "")) {
            $sql .= $virgula . " h07_dhist = '$this->h07_dhist' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_dhist_dia"])) {
                $sql .= $virgula . " h07_dhist = null ";
                $virgula = ",";
            }
        }
        if (trim($this->h07_ddem) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_ddem_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["h07_ddem_dia"] != "")) {
            $sql .= $virgula . " h07_ddem = '$this->h07_ddem' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_ddem_dia"])) {
                $sql .= $virgula . " h07_ddem = null ";
                $virgula = ",";
            }
        }
        if (trim($this->h07_icon) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_icon"])) {
            $sql .= $virgula . " h07_icon = '$this->h07_icon' ";
            $virgula = ",";
        }
        if (trim($this->h07_ires) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_ires"])) {
            $sql .= $virgula . " h07_ires = '$this->h07_ires' ";
            $virgula = ",";
        } else {
            $sql .= $virgula . " h07_ires = null ";
        }

        if (trim($this->h07_class) != "") {
            if (trim($this->h07_class) == "") {
                $this->h07_class = "null";
            }
            $sql .= $virgula . " h07_class = $this->h07_class ";
            $virgula = ",";
        } else {
            $this->h07_class = "null";
            $sql .= $virgula . " h07_class = null ";
        }
        if (trim($this->h07_refe) != "") {
            $sql .= $virgula . " h07_refe = $this->h07_refe ";
            $virgula = ",";
        } else {
            $this->h07_refe = "null";
            $sql .= $virgula . " h07_refe = null ";
        }
        if (trim($this->h07_area) != "") {
            if (trim($this->h07_area) == "") {
                $this->h07_area = "null";
            }
            $sql .= $virgula . " h07_area = $this->h07_area ";
            $virgula = ",";
        } else {
            $this->h07_area = "null";
            $sql .= $virgula . " h07_area = null ";
        }
        if (trim($this->h07_nrato) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_nrato"])) {
            $sql .= $virgula . " h07_nrato = '$this->h07_nrato' ";
            $virgula = ",";
        }
        if (trim($this->h07_impofi) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_impofi"])) {
            $sql .= $virgula . " h07_impofi = '$this->h07_impofi' ";
            $virgula = ",";
        }
        if (trim($this->h07_nrfich) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_nrfich"])) {
            $sql .= $virgula . " h07_nrfich = '$this->h07_nrfich' ";
            $virgula = ",";
        }
        if (trim($this->h07_dpubl) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_dpubl_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["h07_dpubl_dia"] != "")) {
            $sql .= $virgula . " h07_dpubl = '$this->h07_dpubl' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_dpubl_dia"])) {
                $sql .= $virgula . " h07_dpubl = null ";
                $virgula = ",";
            }
        }
        if (trim($this->h07_fundam) != "") {
            if (trim($this->h07_fundam) == "") {
                $this->h07_fundam = "null";
            }
            $sql .= $virgula . " h07_fundam = $this->h07_fundam ";
            $virgula = ",";
        } else {
            $sql .= $virgula . " h07_fundam = null ";
        }
        if (trim($this->h07_defet) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_defet_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["h07_defet_dia"] != "")) {
            $sql .= $virgula . " h07_defet = '$this->h07_defet' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_defet_dia"])) {
                $sql .= $virgula . " h07_defet = null ";
                $virgula = ",";
            }
        }
        if (trim($this->h07_tempor) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_tempor"])) {
            $sql .= $virgula . " h07_tempor = '$this->h07_tempor' ";
            $virgula = ",";
        }
        if (trim($this->h07_termin) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_termin_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["h07_termin_dia"] != "")) {
            $sql .= $virgula . " h07_termin = '$this->h07_termin' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_termin_dia"])) {
                $sql .= $virgula . " h07_termin = null ";
                $virgula = ",";
            }
        }
        if (trim($this->h07_justif) != "" || isset($GLOBALS["HTTP_POST_VARS"]["h07_justif"])) {
            $sql .= $virgula . " h07_justif = '$this->h07_justif' ";
            $virgula = ",";
        }
        $sql .= " where  h07_regist = $this->h07_regist
";
        $resaco = $this->sql_record($this->sql_query_file($this->h07_regist));
        if ($this->numrows > 0) {
            $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
            $acount = pg_result($resac, 0, 0);
            $resac = db_query("insert into db_acountkey values($acount,3622,'$this->h07_regist','A')");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_regist"]))
                $resac = db_query("insert into db_acount values($acount,524,3622,'" . pg_result($resaco, 0, 'h07_regist') . "','$this->h07_regist'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_tipadm"]))
                $resac = db_query("insert into db_acount values($acount,524,3623,'" . pg_result($resaco, 0, 'h07_tipadm') . "','$this->h07_tipadm'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_dato"]))
                $resac = db_query("insert into db_acount values($acount,524,3624,'" . pg_result($resaco, 0, 'h07_dato') . "','$this->h07_dato'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_cant"]))
                $resac = db_query("insert into db_acount values($acount,524,3625,'" . pg_result($resaco, 0, 'h07_cant') . "','$this->h07_cant'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_dhist"]))
                $resac = db_query("insert into db_acount values($acount,524,3626,'" . pg_result($resaco, 0, 'h07_dhist') . "','$this->h07_dhist'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_ddem"]))
                $resac = db_query("insert into db_acount values($acount,524,3627,'" . pg_result($resaco, 0, 'h07_ddem') . "','$this->h07_ddem'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_icon"]))
                $resac = db_query("insert into db_acount values($acount,524,3628,'" . pg_result($resaco, 0, 'h07_icon') . "','$this->h07_icon'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_ires"]))
                $resac = db_query("insert into db_acount values($acount,524,3629,'" . pg_result($resaco, 0, 'h07_ires') . "','$this->h07_ires'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_class"]))
                $resac = db_query("insert into db_acount values($acount,524,3630,'" . pg_result($resaco, 0, 'h07_class') . "','$this->h07_class'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_refe"]))
                $resac = db_query("insert into db_acount values($acount,524,3631,'" . pg_result($resaco, 0, 'h07_refe') . "','$this->h07_refe'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_area"]))
                $resac = db_query("insert into db_acount values($acount,524,3632,'" . pg_result($resaco, 0, 'h07_area') . "','$this->h07_area'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_nrato"]))
                $resac = db_query("insert into db_acount values($acount,524,4612,'" . pg_result($resaco, 0, 'h07_nrato') . "','$this->h07_nrato'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_impofi"]))
                $resac = db_query("insert into db_acount values($acount,524,4614,'" . pg_result($resaco, 0, 'h07_impofi') . "','$this->h07_impofi'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_nrfich"]))
                $resac = db_query("insert into db_acount values($acount,524,4613,'" . pg_result($resaco, 0, 'h07_nrfich') . "','$this->h07_nrfich'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_dpubl"]))
                $resac = db_query("insert into db_acount values($acount,524,4615,'" . pg_result($resaco, 0, 'h07_dpubl') . "','$this->h07_dpubl'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_fundam"]))
                $resac = db_query("insert into db_acount values($acount,524,4616,'" . pg_result($resaco, 0, 'h07_fundam') . "','$this->h07_fundam'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_defet"]))
                $resac = db_query("insert into db_acount values($acount,524,4617,'" . pg_result($resaco, 0, 'h07_defet') . "','$this->h07_defet'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_tempor"]))
                $resac = db_query("insert into db_acount values($acount,524,4618,'" . pg_result($resaco, 0, 'h07_tempor') . "','$this->h07_tempor'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_termin"]))
                $resac = db_query("insert into db_acount values($acount,524,4619,'" . pg_result($resaco, 0, 'h07_termin') . "','$this->h07_termin'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            if (isset($GLOBALS["HTTP_POST_VARS"]["h07_justif"]))
                $resac = db_query("insert into db_acount values($acount,524,4620,'" . pg_result($resaco, 0, 'h07_justif') . "','$this->h07_justif'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
        $result = @db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Cadastro de Admissoes                              nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->h07_regist;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Cadastro de Admissoes                              nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->h07_regist;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->h07_regist;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                return true;
            }
        }
    }

    // funcao para exclusao
    public function excluir($h07_regist = null)
    {
        $this->atualizacampos(true);
        $resaco = $this->sql_record($this->sql_query_file($this->h07_regist));
        if (($resaco != false) || ($this->numrows != 0)) {
            $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
            $acount = pg_result($resac, 0, 0);
            $resac = db_query("insert into db_acountkey values($acount,3622,'$this->h07_regist','E')");
            $resac = db_query("insert into db_acount values($acount,524,3622,'','" . pg_result($resaco, 0, 'h07_regist') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3623,'','" . pg_result($resaco, 0, 'h07_tipadm') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3624,'','" . pg_result($resaco, 0, 'h07_dato') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3625,'','" . pg_result($resaco, 0, 'h07_cant') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3626,'','" . pg_result($resaco, 0, 'h07_dhist') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3627,'','" . pg_result($resaco, 0, 'h07_ddem') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3628,'','" . pg_result($resaco, 0, 'h07_icon') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3629,'','" . pg_result($resaco, 0, 'h07_ires') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3630,'','" . pg_result($resaco, 0, 'h07_class') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3631,'','" . pg_result($resaco, 0, 'h07_refe') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,3632,'','" . pg_result($resaco, 0, 'h07_area') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4612,'','" . pg_result($resaco, 0, 'h07_nrato') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4614,'','" . pg_result($resaco, 0, 'h07_impofi') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4613,'','" . pg_result($resaco, 0, 'h07_nrfich') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4615,'','" . pg_result($resaco, 0, 'h07_dpubl') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4616,'','" . pg_result($resaco, 0, 'h07_fundam') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4617,'','" . pg_result($resaco, 0, 'h07_defet') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4618,'','" . pg_result($resaco, 0, 'h07_tempor') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4619,'','" . pg_result($resaco, 0, 'h07_termin') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            $resac = db_query("insert into db_acount values($acount,524,4620,'','" . pg_result($resaco, 0, 'h07_justif') . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
        $sql = " delete from admissao
                    where ";
        $sql2 = "";
        if ($this->h07_regist != "") {
            if ($sql2 != "") {
                $sql2 .= " and ";
            }
            $sql2 .= " h07_regist = $this->h07_regist ";
        }
        $result = @db_query($sql . $sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Cadastro de Admissoes                              nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->h07_regist;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Cadastro de Admissoes                              nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $this->h07_regist;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->h07_regist;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                return true;
            }
        }
    }

    // funcao do recordset
    public function sql_record($sql)
    {
        $result = @db_query($sql);
        if ($result == false) {
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Dados do Grupo nao Encontrado";
            $this->erro_msg = "Usuário: \n\n " . $this->erro_sql . " \n\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    // funcao do sql
    public function sql_query($h07_regist = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from admissao ";
        $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = admissao.h07_regist";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      inner join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
        $sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        $sql .= "      inner join rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
        $sql .= "      inner join rhreajusteparidade  on  rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
        $sql .= "      left outer join rhadmissaodado on admissao.h07_regist = rhadmissaodado.h25_sequencial";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($h07_regist != null) {
                $sql2 .= " where admissao.h07_regist = $h07_regist ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_file($h07_regist = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from admissao ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($h07_regist != null) {
                $sql2 .= " where admissao.h07_regist = $h07_regist ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    public function sql_query_dados($h07_regist = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from admissao ";
        $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = admissao.h07_regist";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      inner join rhfuncao  on  rhfuncao.rh37_funcao = admissao.h07_cant::integer";
        $sql .= "                          and  rhfuncao.rh37_instit = " . db_getsession('DB_instit');
        $sql .= "      inner join flegal    on  flegal.h04_codigo = admissao.h07_fundam ";
        $sql .= "      inner join concur    on  concur.h06_refer = admissao.h07_refe ";
        $sql .= "      inner join areas     on  areas.h05_codigo = admissao.h07_area ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($h07_regist != null) {
                $sql2 .= " where admissao.h07_regist = $h07_regist ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    function sqlQueryAdmissao($h07_regist = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from admissao ";
        $sql .= " inner join rhpessoal on rhpessoal.rh01_regist = admissao.h07_regist";
        //$sql .= " inner join rhfuncao on rhfuncao.rh37_funcao = rhpessoal.rh01_funcao";
        //$sql .= " inner join concur on concur.h06_refer = admissao.h07_refe ";
        //$sql .= " inner join flegal on flegal.h04_codigo = admissao.h07_fundam ";
        //$sql .= " inner join areas on areas.h05_codigo = admissao.h07_area ";
        $sql2 = "";

        if ($dbwhere == "") {
            if ($h07_regist != null) {
                $sql2 .= " where admissao.h07_regist = $h07_regist ";
            }
        } else if ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
}

?>
