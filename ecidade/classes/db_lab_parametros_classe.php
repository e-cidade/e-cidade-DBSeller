<?php
/**
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

class cl_lab_parametros
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
    public $la49_i_codigo = 0;
    public $la49_c_estrutural = 0;
    public $la49_i_exameduplo = 0;
    public $la49_modelocoletaamostra = 0;
    public $la49_integracao = 0;
    public $la49_habilitarabsurdo = 'f';
    public $la49_modelocomprovanterequisicao = 0;
    public $la49_autorizarexamesaoconfirmar = 'f';
    public $la49_numerocontroleinterno = 'f';
    public $la49_habilitargrupo = 'f';
    public $la49_modelolaudo = 0;
    public $la49_exibeliberacaoporexame = 'f';
    public $la49_espacolinhas = 0;
    public $la49_tamanhofonte = 0;
    public $la49_integracaoinfinity = null;
    public $la49_identificaprioridade = null;
    public $la49_habilitartriagemlaboratorial = null; 
    public $la49_unidadesdispensadastriagem = null; 
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 la49_i_codigo = int4 = Código
                 la49_c_estrutural = char(50) = Estrutural
                 la49_i_exameduplo = int4 = Liberar Exames Duplos
                 la49_modelocoletaamostra = int4 = Modelo de Impressão Coleta de Amostra
                 la49_integracao = int4 = la49_integracao
                 la49_habilitarabsurdo = bool = Habilitar Absurdo Mín. e Máx.
                 la49_modelocomprovanterequisicao = int4 = la49_modelocomprovanterequisicao
                 la49_autorizarexamesaoconfirmar = bool = Autorizar Exames ao Confirmar
                 la49_numerocontroleinterno = bool = Número de Controle Interno da Requisição
                 la49_habilitargrupo = bool = Habilitar Grupo de Exames na Requisição
                 la49_modelolaudo = int2 = Modelo de Laudo
                 la49_exibeliberacaoporexame = bool = Exibir Liberação por Exame
                 la49_espacolinhas = float4 = Espacamento entre linhas
                 la49_tamanhofonte = float4 = Tamanho Fonte
                 la49_integracaoinfinity = bool = Integracao Infinity
                 la49_identificaprioridade = bool = Exibir Prioridade
                 la49_habilitartriagemlaboratorial = bool = Habilitar Triagem Laboratorial 
                 la49_unidadesdispensadastriagem = varchar(255) = Unidades Dispensadas da Triagem 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("lab_parametros");
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
            $this->la49_i_codigo = ($this->la49_i_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_i_codigo"] : $this->la49_i_codigo);
            $this->la49_c_estrutural = ($this->la49_c_estrutural == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_c_estrutural"] : $this->la49_c_estrutural);
            $this->la49_i_exameduplo = ($this->la49_i_exameduplo == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_i_exameduplo"] : $this->la49_i_exameduplo);
            $this->la49_modelocoletaamostra = ($this->la49_modelocoletaamostra == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_modelocoletaamostra"] : $this->la49_modelocoletaamostra);
            $this->la49_integracao = ($this->la49_integracao == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_integracao"] : $this->la49_integracao);
            $this->la49_habilitarabsurdo = ($this->la49_habilitarabsurdo == "f" ? @$GLOBALS["HTTP_POST_VARS"]["la49_habilitarabsurdo"] : $this->la49_habilitarabsurdo);
            $this->la49_modelocomprovanterequisicao = ($this->la49_modelocomprovanterequisicao == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_modelocomprovanterequisicao"] : $this->la49_modelocomprovanterequisicao);
            $this->la49_autorizarexamesaoconfirmar = ($this->la49_autorizarexamesaoconfirmar == "f" ? @$GLOBALS["HTTP_POST_VARS"]["la49_autorizarexamesaoconfirmar"] : $this->la49_autorizarexamesaoconfirmar);
            $this->la49_numerocontroleinterno = ($this->la49_numerocontroleinterno == "f" ? @$GLOBALS["HTTP_POST_VARS"]["la49_numerocontroleinterno"] : $this->la49_numerocontroleinterno);
            $this->la49_habilitargrupo = ($this->la49_habilitargrupo == "f" ? @$GLOBALS["HTTP_POST_VARS"]["la49_habilitargrupo"] : $this->la49_habilitargrupo);
            $this->la49_modelolaudo = ($this->la49_modelolaudo == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_modelolaudo"] : $this->la49_modelolaudo);
            $this->la49_exibeliberacaoporexame = ($this->la49_exibeliberacaoporexame == "f" ? @$GLOBALS["HTTP_POST_VARS"]["la49_exibeliberacaoporexame"] : $this->la49_exibeliberacaoporexame);
            $this->la49_espacolinhas = ($this->la49_espacolinhas == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_espacolinhas"] : $this->la49_espacolinhas);
            $this->la49_tamanhofonte = ($this->la49_tamanhofonte == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_tamanhofonte"] : $this->la49_tamanhofonte);
            $this->la49_integracaoinfinity = ($this->la49_integracaoinfinity === null
                ? (@$GLOBALS["HTTP_POST_VARS"]["la49_integracaoinfinity"] == 0 ? 'false' : 'true')
                : $this->la49_integracaoinfinity);

            $this->la49_identificaprioridade = ($this->la49_identificaprioridade === null
                ? (@$GLOBALS["HTTP_POST_VARS"]["la49_identificaprioridade"] == 'f' ? 'false' : 'true')
                : $this->la49_identificaprioridade);
            $this->la49_habilitartriagemlaboratorial = ($this->la49_habilitartriagemlaboratorial == null
                ?@ (@$GLOBALS["HTTP_POST_VARS"]["la49_habilitartriagemlaboratorial"] == 'f' ? 'false' : 'true')
                :$this->la49_habilitartriagemlaboratorial);
            $this->la49_unidadesdispensadastriagem = ($this->la49_unidadesdispensadastriagem == ""?@$GLOBALS["HTTP_POST_VARS"]["la49_unidadesdispensadastriagem"]:$this->la49_unidadesdispensadastriagem);
        } else {
            $this->la49_i_codigo = ($this->la49_i_codigo == "" ? @$GLOBALS["HTTP_POST_VARS"]["la49_i_codigo"] : $this->la49_i_codigo);
        }
    }

    public function incluir($la49_i_codigo)
    {
        $this->atualizacampos();
        if ($this->la49_modelocoletaamostra == null) {
            $this->erro_sql = " Campo Modelo de Impressão Coleta de Amostra não informado.";
            $this->erro_campo = "la49_modelocoletaamostra";
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
        if ($this->la49_habilitarabsurdo == null) {
            $this->la49_habilitarabsurdo = "true";
        }
        if ($this->la49_modelocomprovanterequisicao == null) {
            $this->erro_sql = " Campo la49_modelocomprovanterequisicao não informado.";
            $this->erro_campo = "la49_modelocomprovanterequisicao";
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
        if ($this->la49_autorizarexamesaoconfirmar == null) {
            $this->erro_sql = " Campo Autorizar Exames ao Confirmar não informado.";
            $this->erro_campo = "la49_autorizarexamesaoconfirmar";
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
        if ($this->la49_numerocontroleinterno == null) {
            $this->erro_sql = " Campo Número de Controle Interno da Requisição não informado.";
            $this->erro_campo = "la49_numerocontroleinterno";
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
        if ($this->la49_habilitargrupo == null) {
            $this->la49_habilitargrupo = "false";
        }
        if ($this->la49_modelolaudo == null) {
            $this->erro_sql = " Campo Modelo de Laudo não informado.";
            $this->erro_campo = "la49_modelolaudo";
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
        if ($this->la49_exibeliberacaoporexame == null) {
            $this->erro_sql = " Campo Exibir Liberação por Exame não informado.";
            $this->erro_campo = "la49_exibeliberacaoporexame";
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
        if ($this->la49_espacolinhas == null) {
            $this->erro_sql = " Campo Espacamento entre linhas não informado.";
            $this->erro_campo = "la49_espacolinhas";
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
        if ($this->la49_tamanhofonte == null) {
            $this->erro_sql = " Campo Tamanho Fonte não informado.";
            $this->erro_campo = "la49_tamanhofonte";
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
        if($this->la49_identificaprioridade == null ){ 
            $this->erro_sql = " Campo Habilitar Identificação Prioridade de Amostra. não informado.";
            $this->erro_campo = "la49_identificaprioridade";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }        
        if($this->la49_habilitartriagemlaboratorial == null ){ 
          $this->erro_sql = " Campo Habilitar Triagem Laboratorial não informado.";
          $this->erro_campo = "la49_habilitartriagemlaboratorial";
          $this->erro_banco = "";
          $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
          $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
          $this->erro_status = "0";
          return false;
        }        
        $this->la49_i_codigo = $la49_i_codigo;
        if (($this->la49_i_codigo == null) || ($this->la49_i_codigo == "")) {
            $this->erro_sql = " Campo la49_i_codigo não declarado.";
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
        $sql = "insert into lab_parametros(
                                       la49_i_codigo
                                      ,la49_c_estrutural
                                      ,la49_i_exameduplo
                                      ,la49_modelocoletaamostra
                                      ,la49_integracao
                                      ,la49_habilitarabsurdo
                                      ,la49_modelocomprovanterequisicao
                                      ,la49_autorizarexamesaoconfirmar
                                      ,la49_numerocontroleinterno
                                      ,la49_habilitargrupo
                                      ,la49_modelolaudo
                                      ,la49_exibeliberacaoporexame
                                      ,la49_espacolinhas
                                      ,la49_tamanhofonte
                                      ,la49_integracaoinfinity
                                      ,la49_identificaprioridade
                                      ,la49_habilitartriagemlaboratorial
                                      ,la49_unidadesdispensadastriagem 
                       )
                values (
                                $this->la49_i_codigo
                               ,$this->la49_c_estrutural
                               ,$this->la49_i_exameduplo
                               ,$this->la49_modelocoletaamostra
                               ,$this->la49_integracao
                               ,'$this->la49_habilitarabsurdo'
                               ,$this->la49_modelocomprovanterequisicao
                               ,'$this->la49_autorizarexamesaoconfirmar'
                               ,'$this->la49_numerocontroleinterno'
                               ,'$this->la49_habilitargrupo'
                               ,$this->la49_modelolaudo
                               ,'$this->la49_exibeliberacaoporexame'
                               ,$this->la49_espacolinhas
                               ,$this->la49_tamanhofonte
                               ,'$this->la49_integracaoinfinity'
                               ,'$this->la49_identificaprioridade'
                               ,'$this->la49_habilitartriagemlaboratorial' 
                               ,'$this->la49_unidadesdispensadastriagem'
                      )";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = " ($this->la49_i_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = " já Cadastrado";
                $this->erro_msg .= str_replace(
                    '"',
                    "",
                    str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
                );
            } else {
                $this->erro_sql = " ($this->la49_i_codigo) não Incluído. Inclusão Abortada.";
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
        $this->erro_sql .= "Valores : ".$this->la49_i_codigo;
        $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .= str_replace(
            '"',
            "",
            str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n")
        );
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        return true;
    }

    public function alterar($la49_i_codigo = null)
    {
        $this->atualizacampos();
        $sql = " update lab_parametros set ";
        $virgula = "";
        if (trim($this->la49_i_codigo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_i_codigo"])) {
            $sql .= $virgula." la49_i_codigo = $this->la49_i_codigo ";
            $virgula = ",";
            if (trim($this->la49_i_codigo) == null) {
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "la49_i_codigo";
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

        if (trim($this->la49_c_estrutural) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_c_estrutural"])) {
            $sql .= $virgula." la49_c_estrutural = '$this->la49_c_estrutural' ";
            $virgula = ",";
            if (trim($this->la49_c_estrutural) == null) {
                $this->erro_sql = " Campo Estrutural nao Informado.";
                $this->erro_campo = "la49_c_estrutural";
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
        if (trim($this->la49_i_exameduplo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_i_exameduplo"])) {
            if (trim($this->la49_i_exameduplo) == "" && isset($GLOBALS["HTTP_POST_VARS"]["la49_i_exameduplo"])) {
                $this->la49_i_exameduplo = "0";
            }
            $sql .= $virgula." la49_i_exameduplo = $this->la49_i_exameduplo ";
            $virgula = ",";
        }
        if (trim(
                $this->la49_modelocoletaamostra
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_modelocoletaamostra"])) {
            $sql .= $virgula." la49_modelocoletaamostra = $this->la49_modelocoletaamostra ";
            $virgula = ",";
            if (trim($this->la49_modelocoletaamostra) == null) {
                $this->erro_sql = " Campo Modelo de Impressão Coleta de Amostra não informado.";
                $this->erro_campo = "la49_modelocoletaamostra";
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
        if (trim($this->la49_integracao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_integracao"])) {
            if (trim($this->la49_integracao) == "" && isset($GLOBALS["HTTP_POST_VARS"]["la49_integracao"])) {
                $this->la49_integracao = "0";
            }
            $sql .= $virgula." la49_integracao = $this->la49_integracao ";
            $virgula = ",";
        }
        if (trim($this->la49_habilitarabsurdo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_habilitarabsurdo"])) {
            $sql .= $virgula." la49_habilitarabsurdo = '$this->la49_habilitarabsurdo' ";
            $virgula = ",";
        }
        if (trim(
                $this->la49_modelocomprovanterequisicao
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_modelocomprovanterequisicao"])) {
            $sql .= $virgula." la49_modelocomprovanterequisicao = $this->la49_modelocomprovanterequisicao ";
            $virgula = ",";
            if (trim($this->la49_modelocomprovanterequisicao) == null) {
                $this->erro_sql = " Campo la49_modelocomprovanterequisicao não informado.";
                $this->erro_campo = "la49_modelocomprovanterequisicao";
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
        if (trim(
                $this->la49_autorizarexamesaoconfirmar
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_autorizarexamesaoconfirmar"])) {
            $sql .= $virgula." la49_autorizarexamesaoconfirmar = '$this->la49_autorizarexamesaoconfirmar' ";
            $virgula = ",";
            if (trim($this->la49_autorizarexamesaoconfirmar) == null) {
                $this->erro_sql = " Campo Autorizar Exames ao Confirmar não informado.";
                $this->erro_campo = "la49_autorizarexamesaoconfirmar";
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
        if (trim(
                $this->la49_numerocontroleinterno
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_numerocontroleinterno"])) {
            $sql .= $virgula." la49_numerocontroleinterno = '$this->la49_numerocontroleinterno' ";
            $virgula = ",";
            if (trim($this->la49_numerocontroleinterno) == null) {
                $this->erro_sql = " Campo Número de Controle Interno da Requisição não informado.";
                $this->erro_campo = "la49_numerocontroleinterno";
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
        if (trim($this->la49_habilitargrupo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_habilitargrupo"])) {
            $sql .= $virgula." la49_habilitargrupo = '$this->la49_habilitargrupo' ";
            $virgula = ",";
        }
        if (trim($this->la49_modelolaudo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_modelolaudo"])) {
            $sql .= $virgula." la49_modelolaudo = $this->la49_modelolaudo ";
            $virgula = ",";
            if (trim($this->la49_modelolaudo) == null) {
                $this->erro_sql = " Campo Modelo de Laudo não informado.";
                $this->erro_campo = "la49_modelolaudo";
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
        if (trim(
                $this->la49_exibeliberacaoporexame
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_exibeliberacaoporexame"])) {
            $sql .= $virgula." la49_exibeliberacaoporexame = '$this->la49_exibeliberacaoporexame' ";
            $virgula = ",";
            if (trim($this->la49_exibeliberacaoporexame) == null) {
                $this->erro_sql = " Campo Exibir Liberação por Exame não informado.";
                $this->erro_campo = "la49_exibeliberacaoporexame";
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
        if (trim($this->la49_espacolinhas) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_espacolinhas"])) {
            $sql .= $virgula." la49_espacolinhas = $this->la49_espacolinhas ";
            $virgula = ",";
            if (trim($this->la49_espacolinhas) == null) {
                $this->erro_sql = " Campo Espacamento entre linhas não informado.";
                $this->erro_campo = "la49_espacolinhas";
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
        if (trim($this->la49_tamanhofonte) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_tamanhofonte"])) {
            $sql .= $virgula." la49_tamanhofonte = $this->la49_tamanhofonte ";
            $virgula = ",";
            if (trim($this->la49_tamanhofonte) == null) {
                $this->erro_sql = " Campo Tamanho Fonte não informado.";
                $this->erro_campo = "la49_tamanhofonte";
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
        if (trim(
                $this->la49_integracaoinfinity
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["la49_integracaoinfinity"])) {
            if (trim(
                    $this->la49_integracaoinfinity
                ) == "" && isset($GLOBALS["HTTP_POST_VARS"]["la49_integracaoinfinity"])) {
                $this->la49_integracaoinfinity = "false";
            }
            $sql .= $virgula." la49_integracaoinfinity = {$this->la49_integracaoinfinity} ";
        }
        if (trim(
                $this->la49_identificaprioridade
            ) != "" || isset($GLOBALS["HTTP_POST_VARS"]["$this->la49_identificaprioridade"])) {
            if (trim(
                    $this->la49_identificaprioridade
                ) == "" && isset($GLOBALS["HTTP_POST_VARS"]["$this->la49_identificaprioridade"])) {
                $this->la49_identificaprioridade = "false";
            }
            $sql .= $virgula." la49_identificaprioridade = {$this->la49_identificaprioridade} ";
        }

        if(trim($this->la49_habilitartriagemlaboratorial)!="" 
           || isset($GLOBALS["HTTP_POST_VARS"]["la49_habilitartriagemlaboratorial"])){ 

            if (trim(
                $this->la49_habilitartriagemlaboratorial
            ) == "" && isset($GLOBALS["HTTP_POST_VARS"]["$this->la49_habilitartriagemlaboratorial"])) {
               $this->la49_habilitartriagemlaboratorial = "false";
            }            
            $sql  .= $virgula." la49_habilitartriagemlaboratorial = {$this->la49_habilitartriagemlaboratorial} ";            
        }        
        
        if(trim($this->la49_unidadesdispensadastriagem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["la49_unidadesdispensadastriagem"])){ 
            $sql  .= $virgula." la49_unidadesdispensadastriagem = '$this->la49_unidadesdispensadastriagem' ";            
        }

        $sql .= " where ";
        if ($la49_i_codigo != null) {
            $sql .= " la49_i_codigo = $this->la49_i_codigo";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->la49_i_codigo;
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
                $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->la49_i_codigo;
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
                $this->erro_sql .= "Valores : ".$this->la49_i_codigo;
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

    public function excluir($la49_i_codigo = null, $dbwhere = null)
    {
        $sql = " delete from lab_parametros
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la49_i_codigo)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " la49_i_codigo = $la49_i_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$la49_i_codigo;
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
                $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$la49_i_codigo;
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
                $this->erro_sql .= "Valores : ".$la49_i_codigo;
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
            $this->erro_sql = "Record Vazio na Tabela:lab_parametros";
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

    public function sql_query($la49_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos}";
        $sql .= "  from lab_parametros ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la49_i_codigo)) {
                $sql2 .= " where lab_parametros.la49_i_codigo = $la49_i_codigo ";
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

    public function sql_query_file($la49_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from lab_parametros ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($la49_i_codigo)) {
                $sql2 .= " where lab_parametros.la49_i_codigo = $la49_i_codigo ";
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

}
