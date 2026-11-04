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

class cl_sec_parametros
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
    public $ed290_sequencial = 0;
    public $ed290_importcenso = 0;
    public $ed290_controleprogressaoparcial = 0;
    public $ed290_diasmanutencaohistorico = 0;
    public $ed290_bncc = 0;
    public $ed290_habilitaconsultaalunoporescola = 'f';
    public $ed290_habilconsmovalunescola = 'f';
    public $ed290_cordisciplinaquadro = 0;
    public $ed290_permite_alteracao_proc_avaliacao = 'f';
    public $ed290_habilitarcampostransescolar = 'f';
    public $ed290_habilitarcamposmatriculainicial = 'f';
    public $ed290_correcao_de_fluxo_manual = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ed290_sequencial = int4 = Sequencial
                 ed290_importcenso = int4 = Importação do Censo
                 ed290_controleprogressaoparcial = int4 = Controle da Progressão Parcial
                 ed290_diasmanutencaohistorico = int4 = Prazo(dias) para Manutenção do Histórico
                 ed290_bncc = int4 = Base Curricular
                 ed290_habilitaconsultaalunoporescola = bool = Habilita Consulta Aluno Por Escola
                 ed290_habilconsmovalunescola = bool = Habil. Cons. Mov. Aluno Por Escola
                 ed290_cordisciplinaquadro = int4 = Aplicar Cor Disciplinas / Quadro de Horrios
                 ed290_permite_alteracao_proc_avaliacao = bool = Permite alteracao proc avaliacao.
                 ed290_habilitarcampostransescolar = bool = Habilitar Campos Transescolar
                 ed290_habilitarcamposmatriculainicial = bool = Habilitar Matricula Inicial
                 ed290_correcao_de_fluxo_manual = int4 = Controle de Fluxo manual
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("sec_parametros");
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
            $this->ed290_sequencial = ($this->ed290_sequencial == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_sequencial"] : $this->ed290_sequencial);
            $this->ed290_importcenso = ($this->ed290_importcenso == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_importcenso"] : $this->ed290_importcenso);
            $this->ed290_controleprogressaoparcial = ($this->ed290_controleprogressaoparcial == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_controleprogressaoparcial"] : $this->ed290_controleprogressaoparcial);
            $this->ed290_diasmanutencaohistorico = ($this->ed290_diasmanutencaohistorico == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_diasmanutencaohistorico"] : $this->ed290_diasmanutencaohistorico);
            $this->ed290_bncc = ($this->ed290_bncc == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_bncc"] : $this->ed290_bncc);
            $this->ed290_habilitaconsultaalunoporescola = ($this->ed290_habilitaconsultaalunoporescola == "f" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_habilitaconsultaalunoporescola"] : $this->ed290_habilitaconsultaalunoporescola);
            $this->ed290_habilconsmovalunescola = ($this->ed290_habilconsmovalunescola == "f" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_habilconsmovalunescola"] : $this->ed290_habilconsmovalunescola);
            $this->ed290_cordisciplinaquadro = ($this->ed290_cordisciplinaquadro == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_cordisciplinaquadro"] : $this->ed290_cordisciplinaquadro);
            $this->ed290_permite_alteracao_proc_avaliacao = ($this->ed290_permite_alteracao_proc_avaliacao == "f" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_permite_alteracao_proc_avaliacao"] : $this->ed290_permite_alteracao_proc_avaliacao);
            $this->ed290_habilitarcampostransescolar = ($this->ed290_habilitarcampostransescolar == "f" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_habilitarcampostransescolar"] : $this->ed290_habilitarcampostransescolar);
            $this->ed290_habilitarcamposmatriculainicial = ($this->ed290_habilitarcamposmatriculainicial == "f" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_habilitarcamposmatriculainicial"] : $this->ed290_habilitarcamposmatriculainicial);
            $this->ed290_correcao_de_fluxo_manual = ($this->ed290_correcao_de_fluxo_manual == "" ? @$GLOBALS["HTTP_POST_VARS"]["ed290_correcao_de_fluxo_manual"] : $this->ed290_correcao_de_fluxo_manual);
        } else {
            $this->ed290_sequencial = ($this->ed290_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ed290_sequencial"] : $this->ed290_sequencial);
        }
    }

    public function incluir($ed290_sequencial)
    {
        $this->atualizacampos();
        if ($this->ed290_importcenso == null) {
            $this->erro_sql = " Campo Importação do Censo não informado.";
            $this->erro_campo = "ed290_importcenso";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_controleprogressaoparcial == null) {
            $this->erro_sql = " Campo Controle da Progressão Parcial não informado.";
            $this->erro_campo = "ed290_controleprogressaoparcial";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_diasmanutencaohistorico == null) {
            $this->erro_sql = " Campo Prazo(dias) para Manutenção do Histórico não informado.";
            $this->erro_campo = "ed290_diasmanutencaohistorico";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_bncc == null) {
            $this->erro_sql = " Campo Base Curricular não informado.";
            $this->erro_campo = "ed290_bncc";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_habilitaconsultaalunoporescola == null) {
            $this->erro_sql = " Campo Habilita Consulta Aluno Por Escola não informado.";
            $this->erro_campo = "ed290_habilitaconsultaalunoporescola";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_habilconsmovalunescola == null) {
            $this->erro_sql = " Campo Habil. Cons. Mov. Aluno Por Escola não informado.";
            $this->erro_campo = "ed290_habilconsmovalunescola";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_cordisciplinaquadro == null) {
            $this->erro_sql = " Campo Aplicar Cor Disciplinas / Quadro de Horários não informado.";
            $this->erro_campo = "ed290_cordisciplinaquadro";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_permite_alteracao_proc_avaliacao == null) {
            $this->erro_sql = " Campo Permite alteracao proc avaliacao. não informado.";
            $this->erro_campo = "ed290_permite_alteracao_proc_avaliacao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_habilitarcampostransescolar == null) {
            $this->erro_sql = " Campo Habilitar Campos Transescolar não informado.";
            $this->erro_campo = "ed290_habilitarcampostransescolar";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_habilitarcamposmatriculainicial == null) {
            $this->erro_sql = " Campo habilitar matricula inicial não informado.";
            $this->erro_campo = "ed290_habilitarcamposmatriculainicial";
            $this->erro_banco = "";
            $this->erro_msg = "Usurio: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ed290_correcao_de_fluxo_manual == null) {
            $this->erro_sql = " Campo Controle de Fluxo.";
            $this->erro_campo = "ed290_correcao_de_fluxo_manual";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($ed290_sequencial == "" || $ed290_sequencial == null) {
            $result = db_query("select nextval('sec_parametros_ed290_sequencial_seq')");
            if ($result == false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: sec_parametros_ed290_sequencial_seq do campo: ed290_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->ed290_sequencial = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from sec_parametros_ed290_sequencial_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $ed290_sequencial)) {
                $this->erro_sql = " Campo ed290_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->ed290_sequencial = $ed290_sequencial;
            }
        }
        if (($this->ed290_sequencial == null) || ($this->ed290_sequencial == "")) {
            $this->erro_sql = " Campo ed290_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into sec_parametros(
                                       ed290_sequencial
                                      ,ed290_importcenso
                                      ,ed290_controleprogressaoparcial
                                      ,ed290_diasmanutencaohistorico
                                      ,ed290_bncc
                                      ,ed290_habilitaconsultaalunoporescola
                                      ,ed290_habilconsmovalunescola
                                      ,ed290_cordisciplinaquadro
                                      ,ed290_permite_alteracao_proc_avaliacao
                                      ,ed290_habilitarcampostransescolar
                                      ,ed290_habilitarcamposmatriculainicial
                                      ,ed290_correcao_de_fluxo_manual
                       )
                values (
                                $this->ed290_sequencial
                               ,$this->ed290_importcenso
                               ,$this->ed290_controleprogressaoparcial
                               ,$this->ed290_diasmanutencaohistorico
                               ,$this->ed290_bncc
                               ,'$this->ed290_habilitaconsultaalunoporescola'
                               ,'$this->ed290_habilconsmovalunescola'
                               ,$this->ed290_cordisciplinaquadro
                               ,'$this->ed290_permite_alteracao_proc_avaliacao'
                               ,'$this->ed290_habilitarcampostransescolar'
                               ,'$this->ed290_habilitarcamposmatriculainicial'
                               ,'$this->ed290_correcao_de_fluxo_manual'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Secretaria Parametros ($this->ed290_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Secretaria Parametros já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql = "Secretaria Parametros ($this->ed290_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->ed290_sequencial;
        $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($ed290_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update sec_parametros set ";
        $virgula = "";
        if (trim($this->ed290_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_sequencial"])) {
            $sql .= $virgula." ed290_sequencial = $this->ed290_sequencial ";
            $virgula = ",";
            if (trim($this->ed290_sequencial) == null) {
                $this->erro_sql = " Campo Sequencial não informado.";
                $this->erro_campo = "ed290_sequencial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_importcenso) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_importcenso"])) {
            if (trim($this->ed290_importcenso) == "" && isset($GLOBALS["HTTP_POST_VARS"]["ed290_importcenso"])) {
                $this->ed290_importcenso = "0" ;
            }
            $sql  .= $virgula." ed290_importcenso = $this->ed290_importcenso ";
            $virgula = ",";
            if (trim($this->ed290_importcenso) == null) {
                $this->erro_sql = " Campo Importação do Censo não informado.";
                $this->erro_campo = "ed290_importcenso";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_controleprogressaoparcial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_controleprogressaoparcial"])) {
            $sql .= $virgula." ed290_controleprogressaoparcial = $this->ed290_controleprogressaoparcial ";
            $virgula = ",";
            if (trim($this->ed290_controleprogressaoparcial) == null) {
                $this->erro_sql = " Campo Controle da Progressão Parcial não informado.";
                $this->erro_campo = "ed290_controleprogressaoparcial";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_diasmanutencaohistorico) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_diasmanutencaohistorico"])) {
            $sql .= $virgula." ed290_diasmanutencaohistorico = $this->ed290_diasmanutencaohistorico ";
            $virgula = ",";
            if (trim($this->ed290_diasmanutencaohistorico) == null) {
                $this->erro_sql = " Campo Prazo(dias) para Manutenção do Histórico não informado.";
                $this->erro_campo = "ed290_diasmanutencaohistorico";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_bncc) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_bncc"])) {
            $sql .= $virgula." ed290_bncc = $this->ed290_bncc ";
            $virgula = ",";
            if (trim($this->ed290_bncc) == null) {
                $this->erro_sql = " Campo Base Curricular não informado.";
                $this->erro_campo = "ed290_bncc";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_habilitaconsultaalunoporescola) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_habilitaconsultaalunoporescola"])) {
            $sql .= $virgula." ed290_habilitaconsultaalunoporescola = '$this->ed290_habilitaconsultaalunoporescola' ";
            $virgula = ",";
            if (trim($this->ed290_habilitaconsultaalunoporescola) == null) {
                $this->erro_sql = " Campo Habilita Consulta Aluno Por Escola não informado.";
                $this->erro_campo = "ed290_habilitaconsultaalunoporescola";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_habilconsmovalunescola) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_habilconsmovalunescola"])) {
            $sql .= $virgula." ed290_habilconsmovalunescola = '$this->ed290_habilconsmovalunescola' ";
            $virgula = ",";
            if (trim($this->ed290_habilconsmovalunescola) == null) {
                $this->erro_sql = " Campo Habil. Cons. Mov. Aluno Por Escola não informado.";
                $this->erro_campo = "ed290_habilconsmovalunescola";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_cordisciplinaquadro) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_cordisciplinaquadro"])) {
            $sql .= $virgula." ed290_cordisciplinaquadro = $this->ed290_cordisciplinaquadro ";
            $virgula = ",";
            if (trim($this->ed290_cordisciplinaquadro) == null) {
                $this->erro_sql = " Campo Aplicar Cor Disciplinas / Quadro de Horrios não informado.";
                $this->erro_campo = "ed290_cordisciplinaquadro";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_permite_alteracao_proc_avaliacao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_permite_alteracao_proc_avaliacao"])) {
            $sql  .= $virgula." ed290_permite_alteracao_proc_avaliacao = '$this->ed290_permite_alteracao_proc_avaliacao' ";
            $virgula = ",";
            if (trim($this->ed290_permite_alteracao_proc_avaliacao) == null) {
                $this->erro_sql = " Campo Permite alteracao proc avaliacao. não informado.";
                $this->erro_campo = "ed290_permite_alteracao_proc_avaliacao";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_habilitarcampostransescolar) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_habilitarcampostransescolar"])) {
            $sql  .= $virgula." ed290_habilitarcampostransescolar = '$this->ed290_habilitarcampostransescolar' ";
            $virgula = ",";
            if (trim($this->ed290_habilitarcampostransescolar) == null) {
                $this->erro_sql = " Campo Habilitar Campos Transescolar não informado.";
                $this->erro_campo = "ed290_habilitarcampostransescolar";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_habilitarcamposmatriculainicial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_habilitarcamposmatriculainicial"])) {
            $sql  .= $virgula." ed290_habilitarcamposmatriculainicial = '$this->ed290_habilitarcamposmatriculainicial' ";
            $virgula = ",";
            if (trim($this->ed290_habilitarcamposmatriculainicial) == null) {
                $this->erro_sql = " Campo habilitar campos Matricula Inicial não informado!";
                $this->erro_campo = "ed290_habilitarcamposmatriculainicial";
                $this->erro_banco = "";
                $this->erro_msg = "Usurio: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->ed290_correcao_de_fluxo_manual) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ed290_correcao_de_fluxo_manual"])) {
            $sql .= $virgula." ed290_correcao_de_fluxo_manual = $this->ed290_correcao_de_fluxo_manual ";
            $virgula = ",";
            if (trim($this->ed290_correcao_de_fluxo_manual) == null) {
                $this->erro_sql = " Campo Controle da Progressão Parcial não informado.";
                $this->erro_campo = "ed290_correcao_de_fluxo_manual";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($ed290_sequencial != null) {
            $sql .= " ed290_sequencial = $this->ed290_sequencial";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Secretaria Parametros não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->ed290_sequencial;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Secretaria Parametros não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->ed290_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->ed290_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($ed290_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from sec_parametros
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed290_sequencial)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " ed290_sequencial = $ed290_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Secretaria Parametros não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$ed290_sequencial;
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Secretaria Parametros não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$ed290_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$ed290_sequencial;
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:sec_parametros";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($ed290_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos}";
        $sql .= "  from sec_parametros ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed290_sequencial)) {
                $sql2 .= " where sec_parametros.ed290_sequencial = $ed290_sequencial ";
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

    public function sql_query_file($ed290_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql = "select {$campos} ";
        $sql .= "  from sec_parametros ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($ed290_sequencial)) {
                $sql2 .= " where sec_parametros.ed290_sequencial = $ed290_sequencial ";
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
