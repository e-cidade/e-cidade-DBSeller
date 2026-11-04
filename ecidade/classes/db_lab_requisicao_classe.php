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

class cl_lab_requisicao
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
    public $la22_i_codigo = 0;
    public $la22_i_departamento = 0;
    public $la22_i_usuario = 0;
    public $la22_i_cgs = 0;
    public $la22_c_responsavel = null;
    public $la22_d_data_dia = null;
    public $la22_d_data_mes = null;
    public $la22_d_data_ano = null;
    public $la22_d_data = null;
    public $la22_c_hora = null;
    public $la22_c_medico = null;
    public $la22_d_dum_dia = null;
    public $la22_d_dum_mes = null;
    public $la22_d_dum_ano = null;
    public $la22_d_dum = null;
    public $la22_t_medicamento = null;
    public $la22_t_diagnostico = null;
    public $la22_t_observacao = null;
    public $la22_i_autoriza = 0;
    public $la22_c_contato = null;
    public $la22_volumeamostra = null;
    public $la22_altura = null;
    public $la22_peso = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 la22_i_codigo = int4 = Código
                 la22_i_departamento = int4 = Departamento
                 la22_i_usuario = int4 = Usuário
                 la22_i_cgs = int4 = Paciente
                 la22_c_responsavel = char(50) = Responsável
                 la22_d_data = date = Data
                 la22_c_hora = char(5) = Hora
                 la22_c_medico = char(50) = Médico
                 la22_d_dum = date = Dum
                 la22_t_medicamento = text = Medicamento
                 la22_t_diagnostico = text = Diagnóstico
                 la22_t_observacao = text = Observação
                 la22_i_autoriza = int4 = Autoriza
                 la22_c_contato = char(50) = Contato:
                 la22_volumeamostra = int4 = Volume Amostra
                 la22_altura = int4 = Altura
                 la22_peso = float8 = Peso
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("lab_requisicao");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->la22_i_codigo = ($this->la22_i_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_i_codigo"] : $this->la22_i_codigo);
            $this->la22_i_departamento = ($this->la22_i_departamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_i_departamento"] : $this->la22_i_departamento);
            $this->la22_i_usuario = ($this->la22_i_usuario == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_i_usuario"] : $this->la22_i_usuario);
            $this->la22_i_cgs = ($this->la22_i_cgs == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_i_cgs"] : $this->la22_i_cgs);
            $this->la22_c_responsavel = ($this->la22_c_responsavel == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_c_responsavel"] : $this->la22_c_responsavel);
            if ($this->la22_d_data == "") {
                $this->la22_d_data_dia = ($this->la22_d_data_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_d_data_dia"] : $this->la22_d_data_dia);
                $this->la22_d_data_mes = ($this->la22_d_data_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_d_data_mes"] : $this->la22_d_data_mes);
                $this->la22_d_data_ano = ($this->la22_d_data_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_d_data_ano"] : $this->la22_d_data_ano);
                if ($this->la22_d_data_dia != "") {
                    $this->la22_d_data = $this->la22_d_data_ano."-".$this->la22_d_data_mes."-".$this->la22_d_data_dia;
                }
            }
            $this->la22_c_hora = ($this->la22_c_hora == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_c_hora"] : $this->la22_c_hora);
            $this->la22_c_medico = ($this->la22_c_medico == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_c_medico"] : $this->la22_c_medico);
            if ($this->la22_d_dum == "") {
                $this->la22_d_dum_dia = ($this->la22_d_dum_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_d_dum_dia"] : $this->la22_d_dum_dia);
                $this->la22_d_dum_mes = ($this->la22_d_dum_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_d_dum_mes"] : $this->la22_d_dum_mes);
                $this->la22_d_dum_ano = ($this->la22_d_dum_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_d_dum_ano"] : $this->la22_d_dum_ano);
                if ($this->la22_d_dum_dia != "") {
                    $this->la22_d_dum = $this->la22_d_dum_ano."-".$this->la22_d_dum_mes."-".$this->la22_d_dum_dia;
                }
            }
            $this->la22_t_medicamento = ($this->la22_t_medicamento == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_t_medicamento"] : $this->la22_t_medicamento);
            $this->la22_t_diagnostico = ($this->la22_t_diagnostico == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_t_diagnostico"] : $this->la22_t_diagnostico);
            $this->la22_t_observacao = ($this->la22_t_observacao == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_t_observacao"] : $this->la22_t_observacao);
            $this->la22_i_autoriza = ($this->la22_i_autoriza == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_i_autoriza"] : $this->la22_i_autoriza);
            $this->la22_c_contato = ($this->la22_c_contato == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_c_contato"] : $this->la22_c_contato);
            $this->la22_volumeamostra = ($this->la22_volumeamostra == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_volumeamostra"] : $this->la22_volumeamostra);
            $this->la22_altura = ($this->la22_altura == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_altura"] : $this->la22_altura);
            $this->la22_peso = ($this->la22_peso == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_peso"] : $this->la22_peso);
        } else {
            $this->la22_i_codigo = ($this->la22_i_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["la22_i_codigo"] : $this->la22_i_codigo);
        }
    }

    public function incluir($la22_i_codigo)
    {
        $this->atualizacampos();
        if ($this->la22_i_departamento == null) {
            $this->erro_sql = " Campo Departamento não informado.";
            $this->erro_campo = "la22_i_departamento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($this->la22_i_usuario == null) {
            $this->erro_sql = " Campo Usuário não informado.";
            $this->erro_campo = "la22_i_usuario";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($this->la22_i_cgs == null) {
            $this->erro_sql = " Campo Paciente não informado.";
            $this->erro_campo = "la22_i_cgs";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($this->la22_d_data == null) {
            $this->erro_sql = " Campo Data não informado.";
            $this->erro_campo = "la22_d_data_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($this->la22_c_hora == null) {
            $this->erro_sql = " Campo Hora não informado.";
            $this->erro_campo = "la22_c_hora";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($this->la22_d_dum == null) {
            $this->la22_d_dum = "null";
        }
        if ($this->la22_i_autoriza == null) {
            $this->erro_sql = " Campo Autoriza não informado.";
            $this->erro_campo = "la22_i_autoriza";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        if ($this->la22_volumeamostra == null) {
            $this->la22_volumeamostra = 'NULL';
        }
        if ($this->la22_altura == null) {
            $this->la22_altura = 'NULL';
        }
        if ($this->la22_peso == null) {
            $this->la22_peso = 'NULL';
        }
        if ($la22_i_codigo == "" || $la22_i_codigo == null) {
            $result = db_query("select nextval('lab_requisicao_la22_i_codigo_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: lab_requisicao_la22_i_codigo_seq do campo: la22_i_codigo";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
            $this->la22_i_codigo = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from lab_requisicao_la22_i_codigo_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $la22_i_codigo)) {
                $this->erro_sql = " Campo la22_i_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            } else {
                $this->la22_i_codigo = $la22_i_codigo;
            }
        }
        if (($this->la22_i_codigo == null) || ($this->la22_i_codigo == "")) {
            $this->erro_sql = " Campo la22_i_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into lab_requisicao(
                                       la22_i_codigo
                                      ,la22_i_departamento
                                      ,la22_i_usuario
                                      ,la22_i_cgs
                                      ,la22_c_responsavel
                                      ,la22_d_data
                                      ,la22_c_hora
                                      ,la22_c_medico
                                      ,la22_d_dum
                                      ,la22_t_medicamento
                                      ,la22_t_diagnostico
                                      ,la22_t_observacao
                                      ,la22_i_autoriza
                                      ,la22_c_contato
                                      ,la22_volumeamostra
                                      ,la22_altura
                                      ,la22_peso
                       )
                values (
                                $this->la22_i_codigo
                               ,$this->la22_i_departamento
                               ,$this->la22_i_usuario
                               ,$this->la22_i_cgs
                               ,'$this->la22_c_responsavel'
                               ,".($this->la22_d_data == "null" || $this->la22_d_data == "" ? "null" : "'".$this->la22_d_data."'")."
                               ,'$this->la22_c_hora'
                               ,'$this->la22_c_medico'
                               ,".($this->la22_d_dum == "null" || $this->la22_d_dum == "" ? "null" : "'".$this->la22_d_dum."'")."
                               ,'$this->la22_t_medicamento'
                               ,'$this->la22_t_diagnostico'
                               ,'$this->la22_t_observacao'
                               ,$this->la22_i_autoriza
                               ,'$this->la22_c_contato'
                               ,$this->la22_volumeamostra
                               ,$this->la22_altura
                               ,$this->la22_peso
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "lab_requisicao ($this->la22_i_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "lab_requisicao já Cadastrado";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            } else {
                $this->erro_sql = "lab_requisicao ($this->la22_i_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->la22_i_codigo;
        $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .= str_replace(
            '"',
            "",
            str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
        );
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->la22_i_codigo));
            if (($resaco != false) || ($this->numrows != 0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,15824,'$this->la22_i_codigo','I')");
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15824,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_i_codigo')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15826,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_i_departamento')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15827,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_i_usuario')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15828,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_i_cgs')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15829,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_c_responsavel')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15831,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_d_data')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15832,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_c_hora')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15833,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_c_medico')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15834,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_d_dum')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15835,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_t_medicamento')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15836,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_t_diagnostico')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15837,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_t_observacao')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,15839,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_i_autoriza')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,16210,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_c_contato')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,1015474,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_volumeamostra')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,1015473,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_altura')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
                $resac = db_query(
                    "insert into db_acount values($acount,2773,1015472,'','".AddSlashes(
                        pg_result($resaco, 0, 'la22_peso')
                    )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                );
            }
        }
        return true;
    }

    public function alterar($la22_i_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update lab_requisicao set ";
        $virgula = "";
        if (trim($this->la22_i_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_i_codigo"])) {
            $sql .= $virgula." la22_i_codigo = $this->la22_i_codigo ";
            $virgula = ",";
            if (trim($this->la22_i_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "la22_i_codigo";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la22_i_departamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_i_departamento"])) {
            $sql .= $virgula." la22_i_departamento = $this->la22_i_departamento ";
            $virgula = ",";
            if (trim($this->la22_i_departamento) == null) {
                $this->erro_sql = " Campo Departamento não informado.";
                $this->erro_campo = "la22_i_departamento";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la22_i_usuario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_i_usuario"])) {
            $sql .= $virgula." la22_i_usuario = $this->la22_i_usuario ";
            $virgula = ",";
            if (trim($this->la22_i_usuario) == null) {
                $this->erro_sql = " Campo Usuário não informado.";
                $this->erro_campo = "la22_i_usuario";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la22_i_cgs) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_i_cgs"])) {
            $sql .= $virgula." la22_i_cgs = $this->la22_i_cgs ";
            $virgula = ",";
            if (trim($this->la22_i_cgs) == null) {
                $this->erro_sql = " Campo Paciente não informado.";
                $this->erro_campo = "la22_i_cgs";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la22_c_responsavel) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_c_responsavel"])) {
            $sql .= $virgula." la22_c_responsavel = '$this->la22_c_responsavel' ";
            $virgula = ",";
        }
        if (trim(
                $this->la22_d_data
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_d_data_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["la22_d_data_dia"] != "")) {
            $sql .= $virgula." la22_d_data = '$this->la22_d_data' ";
            $virgula = ",";
            if (trim($this->la22_d_data) == null) {
                $this->erro_sql = " Campo Data não informado.";
                $this->erro_campo = "la22_d_data_dia";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["la22_d_data_dia"])) {
                $sql .= $virgula." la22_d_data = null ";
                $virgula = ",";
                if (trim($this->la22_d_data) == null) {
                    $this->erro_sql = " Campo Data não informado.";
                    $this->erro_campo = "la22_d_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg .= str_replace(
                        '"',
                        "",
                        str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                    );
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->la22_c_hora) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_c_hora"])) {
            $sql .= $virgula." la22_c_hora = '$this->la22_c_hora' ";
            $virgula = ",";
            if (trim($this->la22_c_hora) == null) {
                $this->erro_sql = " Campo Hora não informado.";
                $this->erro_campo = "la22_c_hora";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la22_c_medico) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_c_medico"])) {
            $sql .= $virgula." la22_c_medico = '$this->la22_c_medico' ";
            $virgula = ",";
        }
        if (trim(
                $this->la22_d_dum
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_d_dum_dia"]) && ($GLOBALS["HTTP_POST_VARS"]["la22_d_dum_dia"] != "")) {
            $sql .= $virgula." la22_d_dum = '$this->la22_d_dum' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["la22_d_dum_dia"])) {
                $sql .= $virgula." la22_d_dum = null ";
                $virgula = ",";
            }
        }
        if (trim($this->la22_t_medicamento) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_t_medicamento"])) {
            $sql .= $virgula." la22_t_medicamento = '$this->la22_t_medicamento' ";
            $virgula = ",";
        }
        if (trim($this->la22_t_diagnostico) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_t_diagnostico"])) {
            $sql .= $virgula." la22_t_diagnostico = '$this->la22_t_diagnostico' ";
            $virgula = ",";
        }
        if (trim($this->la22_t_observacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_t_observacao"])) {
            $sql .= $virgula." la22_t_observacao = '$this->la22_t_observacao' ";
            $virgula = ",";
        }
        if (trim($this->la22_i_autoriza) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_i_autoriza"])) {
            $sql .= $virgula." la22_i_autoriza = $this->la22_i_autoriza ";
            $virgula = ",";
            if (trim($this->la22_i_autoriza) == null) {
                $this->erro_sql = " Campo Autoriza não informado.";
                $this->erro_campo = "la22_i_autoriza";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->la22_c_contato) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la22_c_contato"])) {
            $sql .= $virgula." la22_c_contato = '$this->la22_c_contato' ";
            $virgula = ",";
        }
        if (trim(
                $this->la22_volumeamostra
            ) != "" || (isset($GLOBALS["HTTP_POST_VARS"]["la22_volumeamostra"]) && ($GLOBALS["HTTP_POST_VARS"]["la22_volumeamostra"] != ""))) {
            $sql .= $virgula." la22_volumeamostra = $this->la22_volumeamostra ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["la22_volumeamostra"])) {
                $sql .= $virgula." la22_volumeamostra = null ";
                $virgula = ",";
            }
        }
        if (trim(
                $this->la22_altura
            ) != "" || (isset($GLOBALS["HTTP_POST_VARS"]["la22_altura"]) && ($GLOBALS["HTTP_POST_VARS"]["la22_altura"] != ""))) {
            $sql .= $virgula." la22_altura = $this->la22_altura ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["la22_altura"])) {
                $sql .= $virgula." la22_altura = null ";
                $virgula = ",";
            }
        }
        if (trim(
                $this->la22_peso
            ) != "" || (isset($GLOBALS["HTTP_POST_VARS"]["la22_peso"]) && ($GLOBALS["HTTP_POST_VARS"]["la22_peso"] != ""))) {
            $sql .= $virgula." la22_peso = $this->la22_peso ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["la22_peso"])) {
                $sql .= $virgula." la22_peso = null ";
                $virgula = ",";
            }
        }
        $sql .= " where ";
        if ($la22_i_codigo != null) {
            $sql .= " la22_i_codigo = $this->la22_i_codigo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->la22_i_codigo));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,15824,'$this->la22_i_codigo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_i_codigo"]) || $this->la22_i_codigo != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15824,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_i_codigo')
                            )."','$this->la22_i_codigo',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_i_departamento"]) || $this->la22_i_departamento != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15826,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_i_departamento')
                            )."','$this->la22_i_departamento',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_i_usuario"]) || $this->la22_i_usuario != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15827,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_i_usuario')
                            )."','$this->la22_i_usuario',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_i_cgs"]) || $this->la22_i_cgs != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15828,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_i_cgs')
                            )."','$this->la22_i_cgs',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_c_responsavel"]) || $this->la22_c_responsavel != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15829,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_c_responsavel')
                            )."','$this->la22_c_responsavel',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_d_data"]) || $this->la22_d_data != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15831,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_d_data')
                            )."','$this->la22_d_data',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_c_hora"]) || $this->la22_c_hora != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15832,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_c_hora')
                            )."','$this->la22_c_hora',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_c_medico"]) || $this->la22_c_medico != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15833,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_c_medico')
                            )."','$this->la22_c_medico',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_d_dum"]) || $this->la22_d_dum != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15834,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_d_dum')
                            )."','$this->la22_d_dum',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_t_medicamento"]) || $this->la22_t_medicamento != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15835,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_t_medicamento')
                            )."','$this->la22_t_medicamento',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_t_diagnostico"]) || $this->la22_t_diagnostico != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15836,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_t_diagnostico')
                            )."','$this->la22_t_diagnostico',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_t_observacao"]) || $this->la22_t_observacao != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15837,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_t_observacao')
                            )."','$this->la22_t_observacao',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_i_autoriza"]) || $this->la22_i_autoriza != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,15839,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_i_autoriza')
                            )."','$this->la22_i_autoriza',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_c_contato"]) || $this->la22_c_contato != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,16210,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_c_contato')
                            )."','$this->la22_c_contato',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_volumeamostra"]) || $this->la22_volumeamostra != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,1015474,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_volumeamostra')
                            )."','$this->la22_volumeamostra',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_altura"]) || $this->la22_altura != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,1015473,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_altura')
                            )."','$this->la22_altura',".db_getsession('DB_datausu').",".db_getsession(
                                'DB_id_usuario'
                            ).")"
                        );
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["la22_peso"]) || $this->la22_peso != "") {
                        $resac = db_query(
                            "insert into db_acount values($acount,2773,1015472,'".AddSlashes(
                                pg_result($resaco, $conresaco, 'la22_peso')
                            )."','$this->la22_peso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                        );
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "lab_requisicao não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->la22_i_codigo;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "lab_requisicao não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->la22_i_codigo;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->la22_i_codigo;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($la22_i_codigo = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($la22_i_codigo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows != 0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,15824,'$la22_i_codigo','E')");
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15824,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_i_codigo')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15826,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_i_departamento')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15827,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_i_usuario')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15828,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_i_cgs')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15829,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_c_responsavel')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15831,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_d_data')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15832,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_c_hora')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15833,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_c_medico')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15834,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_d_dum')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15835,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_t_medicamento')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15836,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_t_diagnostico')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15837,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_t_observacao')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,15839,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_i_autoriza')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,16210,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_c_contato')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,1015474,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_volumeamostra')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,1015473,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_altura')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                    $resac = db_query(
                        "insert into db_acount values($acount,2773,1015472,'','".AddSlashes(
                            pg_result($resaco, $iresaco, 'la22_peso')
                        )."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")"
                    );
                }
            }
        }
        $sql = " delete from lab_requisicao
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la22_i_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " la22_i_codigo = $la22_i_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "lab_requisicao não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$la22_i_codigo;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "lab_requisicao não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$la22_i_codigo;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$la22_i_codigo;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
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
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:lab_requisicao";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace(
                '"',
                "",
                str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
            );
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($la22_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from lab_requisicao ";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = lab_requisicao.la22_i_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = lab_requisicao.la22_i_departamento";
        $sql .= "      inner join cgs_und  on  cgs_und.z01_i_cgsund = lab_requisicao.la22_i_cgs";
        $sql .= "      inner join db_config  on  db_config.codigo = db_depart.instit";
        $sql .= "      left  join familiamicroarea  on  familiamicroarea.sd35_i_codigo = cgs_und.z01_i_familiamicroarea";
        $sql .= "      inner join cgs  as a on   a.z01_i_numcgs = cgs_und.z01_i_cgsund";
        $sql .= "      left join numerocontroleinternorequisicao as ncir on ncir.la65_requisicao = lab_requisicao.la22_i_codigo";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la22_i_codigo)) {
                $sql2 .= " where lab_requisicao.la22_i_codigo = $la22_i_codigo ";
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

    public function sql_query_file($la22_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from lab_requisicao ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la22_i_codigo)) {
                $sql2 .= " where lab_requisicao.la22_i_codigo = $la22_i_codigo ";
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

    function sql_query_requiitem(
        $la22_i_codigo = null,
        $campos = "*",
        $ordem = null,
        $dbwhere = "",
        $lRequisitos = false
    ) {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = split("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from lab_requisicao ";
        $sql .= "      inner join db_usuarios      on  db_usuarios.id_usuario          = lab_requisicao.la22_i_usuario";
        $sql .= "      inner join db_depart        on  db_depart.coddepto              = lab_requisicao.la22_i_departamento";
        $sql .= "      inner join cgs_und          on  cgs_und.z01_i_cgsund            = lab_requisicao.la22_i_cgs";
        $sql .= "      left  join lab_medico       on  lab_medico.la38_i_requisicao    = lab_requisicao.la22_i_codigo";
        $sql .= "      left  join medicos          on  lab_medico.la38_i_medico        = medicos.sd03_i_codigo";
        $sql .= "      left  join cgm              on  medicos.sd03_i_cgm              = cgm.z01_numcgm";
        $sql .= "      inner join lab_requiitem    on  lab_requiitem.la21_i_requisicao = lab_requisicao.la22_i_codigo";
        $sql .= "      left  join lab_coletaitem   on  lab_coletaitem.la32_i_requiitem = lab_requiitem.la21_i_codigo";
        $sql .= "      inner join lab_setorexame   on  lab_setorexame.la09_i_codigo    = lab_requiitem.la21_i_setorexame";
        $sql .= "      inner join lab_exame        on  lab_exame.la08_i_codigo         = lab_setorexame.la09_i_exame";
        $sql .= "      left  join lab_exameatributo on  lab_exameatributo.la42_i_exame  = lab_exame.la08_i_codigo";
        $sql .= "      inner join lab_labsetor     on  lab_labsetor.la24_i_codigo      = lab_setorexame.la09_i_labsetor";
        $sql .= "      inner join lab_setor        on  lab_setor.la23_i_codigo         = lab_labsetor.la24_i_setor";
        $sql .= "      inner join lab_laboratorio  on  lab_laboratorio.la02_i_codigo   = lab_labsetor.la24_i_laboratorio";
        if ($lRequisitos) {
            $sql .= "      inner join lab_examerequisito on lab_examerequisito.la20_i_exame = lab_exame.la08_i_codigo   ";
            $sql .= "      inner join lab_requisito on lab_requisito.la12_i_codigo = lab_examerequisito.la20_i_requisito ";
        }
        $sql2 = "";
        if ($dbwhere == "") {
            if ($la22_i_codigo != null) {
                $sql2 .= " where lab_requisicao.la22_i_codigo = $la22_i_codigo ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    function sql_query_coleta_amostra($la22_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";

        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";

            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from lab_requisicao ";
        $sql .= "      inner join db_usuarios       on  db_usuarios.id_usuario          = lab_requisicao.la22_i_usuario";
        $sql .= "      inner join db_depart         on  db_depart.coddepto              = lab_requisicao.la22_i_departamento";
        $sql .= "      inner join cgs_und           on  cgs_und.z01_i_cgsund            = lab_requisicao.la22_i_cgs";
        $sql .= "      left  join lab_medico        on  lab_medico.la38_i_requisicao    = lab_requisicao.la22_i_codigo";
        $sql .= "      left  join medicos           on  lab_medico.la38_i_medico        = medicos.sd03_i_codigo";
        $sql .= "      left  join cgm               on  medicos.sd03_i_cgm              = cgm.z01_numcgm";
        $sql .= "      inner join lab_requiitem     on  lab_requiitem.la21_i_requisicao = lab_requisicao.la22_i_codigo";
        $sql .= "      inner join lab_setorexame    on  lab_setorexame.la09_i_codigo    = lab_requiitem.la21_i_setorexame";
        $sql .= "      inner join lab_exame         on  lab_exame.la08_i_codigo         = lab_setorexame.la09_i_exame";
        $sql .= "      left  join lab_exameatributo on  lab_exameatributo.la42_i_exame  = lab_exame.la08_i_codigo";
        $sql .= "      inner join lab_labsetor      on  lab_labsetor.la24_i_codigo      = lab_setorexame.la09_i_labsetor";
        $sql .= "      inner join lab_setor         on  lab_setor.la23_i_codigo         = lab_labsetor.la24_i_setor";
        $sql .= "      inner join lab_laboratorio   on  lab_laboratorio.la02_i_codigo   = lab_labsetor.la24_i_laboratorio";
        $sql .= "      left  join lab_coletaitem    on  lab_coletaitem.la32_i_requiitem = lab_requiitem.la21_i_codigo";

        $sql2 = "";

        if ($dbwhere == "") {
            if ($la22_i_codigo != null) {
                $sql2 .= " where lab_requisicao.la22_i_codigo = {$la22_i_codigo} ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where {$dbwhere}";
            }
        }

        $sql .= $sql2;

        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";

            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }

        return $sql;
    }

    public function sql_query_responsavel_requisicao($codigoRequisicao)
    {
        $sSql = "
          SELECT cgs_und.z01_v_nome FROM lab_requisicao
          INNER join cgs_und ON cgs_und.z01_i_cgsund = lab_requisicao.la22_i_cgs
          WHERE la22_i_codigo = {$codigoRequisicao}
      ";

        return $sSql;
    }

    /**
     * Retorna a relação de de requisições com seus exames e laboratório.
     * @param  string  $idsItensRequisicoes
     * @param  int  $limit
     * @param  int  $offset
     * @return string
     */
    public function getRelacaoRequisicaoExames($idsItensRequisicoes, $limit, $offset)
    {
        return "
                select la21_i_requisicao as requisicao, la22_d_data as data_requisicao, la08_i_codigo as cod_exame,
                la08_c_descr as exame, z01_v_nome as nome_paciente, la02_c_descr as lab_descr from lab_requiitem
                inner join lab_requisicao on la22_i_codigo = la21_i_requisicao
                inner join cgs_und on z01_i_cgsund = la22_i_cgs
                inner join lab_setorexame on la09_i_codigo = la21_i_setorexame
                inner join lab_exame on la09_i_exame = la08_i_codigo
                inner join db_depart on coddepto = la22_i_departamento
                inner join lab_labdepart on la03_i_departamento = coddepto
                inner join lab_laboratorio on la02_i_codigo = la03_i_laboratorio
                where la21_i_codigo in ({$idsItensRequisicoes})
                and la21_c_situacao = '50 - Lancado'
                order by la22_i_codigo
                limit {$limit} offset {$offset};
            ";
    }

    /**
     * Recupera os exames vínculados a um grupo de liberação.
     * @param  int  $grupoLiberacaoId
     * @return string
     */
    public function getExamesByGrupoLiberacao($grupoLiberacaoId)
    {
        return "
                select la08_i_codigo, la08_c_sigla, la08_c_descr from gruposliberacao
                inner join gruposliberacao_labexame on la72_grupoliberacao = la71_sequencial
                inner join lab_exame on la08_i_codigo = la72_labexame
                where la71_sequencial = {$grupoLiberacaoId};
            ";
    }
}
