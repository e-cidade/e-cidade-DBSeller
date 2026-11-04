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

//MODULO: fiscal
//CLASSE DA ENTIDADE vistoriaslotevist
class cl_fis_vistoriaslotevist
{
    // cria variaveis de erro
    public $rotulo     = null;
    public $query_sql  = null;
    public $numrows    = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status= null;
    public $erro_sql   = null;
    public $erro_banco = null;
    public $erro_msg   = null;
    public $erro_campo = null;
    public $pagina_retorno = null;
    // cria variaveis do arquivo
    public $y05_vistoriaslotevist = 0;
    public $y05_vistoriaslote = 0;
    public $y05_codvist = 0;
    public $y05_codmsg = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y05_vistoriaslotevist = int8 = codigo da vistoria lancada
                 y05_vistoriaslote = int8 = Codigo do lote das vistorias
                 y05_codvist = int4 = Código da Vistoria
                 y05_codmsg = int4 = codigo da mensagem
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_vistoriaslotevist");
         $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
    //funcao erro
    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if ($retorna) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
    // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->y05_vistoriaslotevist = ($this->y05_vistoriaslotevist == ""?@$GLOBALS["HTTP_POST_VARS"]["y05_vistoriaslotevist"]:$this->y05_vistoriaslotevist);
            $this->y05_vistoriaslote = ($this->y05_vistoriaslote == ""?@$GLOBALS["HTTP_POST_VARS"]["y05_vistoriaslote"]:$this->y05_vistoriaslote);
            $this->y05_codvist = ($this->y05_codvist == ""?@$GLOBALS["HTTP_POST_VARS"]["y05_codvist"]:$this->y05_codvist);
            $this->y05_codmsg = ($this->y05_codmsg == ""?@$GLOBALS["HTTP_POST_VARS"]["y05_codmsg"]:$this->y05_codmsg);
        } else {
            $this->y05_vistoriaslotevist = ($this->y05_vistoriaslotevist == ""?@$GLOBALS["HTTP_POST_VARS"]["y05_vistoriaslotevist"]:$this->y05_vistoriaslotevist);
            $this->y05_vistoriaslote = ($this->y05_vistoriaslote == ""?@$GLOBALS["HTTP_POST_VARS"]["y05_vistoriaslote"]:$this->y05_vistoriaslote);
        }
    }
    // funcao para inclusao
    public function incluir($y05_vistoriaslotevist = null)
    {
          $this->atualizacampos();
        if ($this->y05_codvist == null) {
            $this->erro_sql = " Campo Código da Vistoria nao Informado.";
            $this->erro_campo = "y05_codvist";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y05_codmsg == null) {
            $this->erro_sql = " Campo codigo da mensagem nao Informado.";
            $this->erro_campo = "y05_codmsg";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_vistoriaslotevist(
                                       y05_vistoriaslote
                                      ,y05_codvist
                                      ,y05_codmsg
                       )
                values (
                                $this->y05_vistoriaslote
                               ,$this->y05_codvist
                               ,$this->y05_codmsg
                      ) returning y05_vistoriaslotevist ";
        $result = db_query($sql);
        $this->y05_vistoriaslotevist = db_utils::fieldsmemory($result, 0)->y05_vistoriaslotevist;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Ligação da da lotevist com a vistorias ($this->y05_vistoriaslotevist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Ligação da da lotevist com a vistorias já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Ligação da da lotevist com a vistorias ($this->y05_vistoriaslotevist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y05_vistoriaslotevist;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);


        return true;
    }
   // funcao para alteracao
    public function alterar($y05_vistoriaslotevist = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_vistoriaslotevist set ";
        $virgula = "";
        if (trim($this->y05_vistoriaslote)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y05_vistoriaslote"])) {
            $sql  .= $virgula." y05_vistoriaslote = $this->y05_vistoriaslote ";
            $virgula = ",";
            if (trim($this->y05_vistoriaslote) == null) {
                $this->erro_sql = " Campo Codigo do lote das vistorias nao Informado.";
                $this->erro_campo = "y05_vistoriaslote";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y05_codvist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y05_codvist"])) {
            $sql  .= $virgula." y05_codvist = $this->y05_codvist ";
            $virgula = ",";
            if (trim($this->y05_codvist) == null) {
                $this->erro_sql = " Campo Código da Vistoria nao Informado.";
                $this->erro_campo = "y05_codvist";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y05_codmsg)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y05_codmsg"])) {
            $sql  .= $virgula." y05_codmsg = $this->y05_codmsg ";
            $virgula = ",";
            if (trim($this->y05_codmsg) == null) {
                $this->erro_sql = " Campo codigo da mensagem nao Informado.";
                $this->erro_campo = "y05_codmsg";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y05_vistoriaslotevist!=null) {
            $sql .= " y05_vistoriaslotevist = $this->y05_vistoriaslotevist";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Ligação da da lotevist com a vistorias nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y05_vistoriaslotevist;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Ligação da da lotevist com a vistorias nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y05_vistoriaslotevist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y05_vistoriaslotevist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y05_vistoriaslotevist = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_vistoriaslotevist where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y05_vistoriaslotevist != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y05_vistoriaslotevist = $y05_vistoriaslotevist ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Ligação da da lotevist com a vistorias nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y05_vistoriaslotevist;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Ligação da da lotevist com a vistorias nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y05_vistoriaslotevist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y05_vistoriaslotevist;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows==0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:vistoriaslotevist";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y05_vistoriaslotevist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistoriaslotevist ";
        $sql .= "      inner join fiscalizacao.fis_vistorias  on  fis_vistorias.y70_codvist = fis_vistoriaslotevist.y05_codvist";
        $sql .= "      inner join fiscalizacao.fis_vistoriaslote  on  fis_vistoriaslote.y06_vistoriaslote = fis_vistoriaslotevist.y05_vistoriaslote";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_vistorias.y70_id_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_vistorias.y70_coddepto";
        $sql .= "      inner join fiscalizacao.fis_fandam  on  fis_fandam.y39_codandam = fis_vistorias.y70_ultandam";
        $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistorias.y70_tipovist";
        $sql .= "      inner join db_usuarios  as a on   a.id_usuario = fis_vistoriaslote.y06_usuario";
        $sql .= "      inner join fiscalizacao.fis_tipovistorias  as b on   b.y77_codtipo = fis_vistoriaslote.y06_codtipo";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y05_vistoriaslotevist!=null) {
                $sql2 .= " where fis_vistoriaslotevist.y05_vistoriaslotevist = $y05_vistoriaslotevist ";
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
    public function sql_query_file($y05_vistoriaslotevist = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistoriaslotevist ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y05_vistoriaslotevist!=null) {
                $sql2 .= " where fis_vistoriaslotevist.y05_vistoriaslotevist = $y05_vistoriaslotevist ";
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
}
