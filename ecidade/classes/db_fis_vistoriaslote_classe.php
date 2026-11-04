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
//CLASSE DA ENTIDADE vistoriaslote
class cl_fis_vistoriaslote
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
    public $y06_vistoriaslote = 0;
    public $y06_data_dia = null;
    public $y06_data_mes = null;
    public $y06_data_ano = null;
    public $y06_data = null;
    public $y06_hora = null;
    public $y06_usuario = 0;
    public $y06_codtipo = 0;
    public $y06_instit = 0;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 y06_vistoriaslote = int8 = Codigo do lote das vistorias
                 y06_data = date = Data do lancamento geral
                 y06_hora = varchar(10) = Hora do lancamento
                 y06_usuario = int4 = Cod. Usuário
                 y06_codtipo = int4 = Código do Tipo
                 y06_instit = int4 = Cod. Instituição
                 ";
    //funcao construtor da classe
    public function __construct()
    {
         //classes dos rotulos dos campos
         $this->rotulo = new rotulo("fis_vistoriaslote");
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
            $this->y06_vistoriaslote = ($this->y06_vistoriaslote == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_vistoriaslote"]:$this->y06_vistoriaslote);
            if ($this->y06_data == "") {
                $this->y06_data_dia = ($this->y06_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_data_dia"]:$this->y06_data_dia);
                $this->y06_data_mes = ($this->y06_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_data_mes"]:$this->y06_data_mes);
                $this->y06_data_ano = ($this->y06_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_data_ano"]:$this->y06_data_ano);
                if ($this->y06_data_dia != "") {
                    $this->y06_data = $this->y06_data_ano."-".$this->y06_data_mes."-".$this->y06_data_dia;
                }
            }
            $this->y06_hora = ($this->y06_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_hora"]:$this->y06_hora);
            $this->y06_usuario = ($this->y06_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_usuario"]:$this->y06_usuario);
            $this->y06_codtipo = ($this->y06_codtipo == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_codtipo"]:$this->y06_codtipo);
            $this->y06_instit = ($this->y06_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_instit"]:$this->y06_instit);
        } else {
            $this->y06_vistoriaslote = ($this->y06_vistoriaslote == ""?@$GLOBALS["HTTP_POST_VARS"]["y06_vistoriaslote"]:$this->y06_vistoriaslote);
        }
    }
   // funcao para inclusao
    public function incluir($y06_vistoriaslote = null)
    {
          $this->atualizacampos();
        if ($this->y06_data == null) {
            $this->erro_sql = " Campo Data do lancamento geral nao Informado.";
            $this->erro_campo = "y06_data_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y06_hora == null) {
            $this->erro_sql = " Campo Hora do lancamento nao Informado.";
            $this->erro_campo = "y06_hora";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y06_usuario == null) {
            $this->erro_sql = " Campo Cod. Usuário nao Informado.";
            $this->erro_campo = "y06_usuario";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y06_codtipo == null) {
            $this->erro_sql = " Campo Código do Tipo nao Informado.";
            $this->erro_campo = "y06_codtipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->y06_instit == null) {
            $this->erro_sql = " Campo Cod. Instituição nao Informado.";
            $this->erro_campo = "y06_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into fiscalizacao.fis_vistoriaslote(
                                       y06_data
                                      ,y06_hora
                                      ,y06_usuario
                                      ,y06_codtipo
                                      ,y06_instit
                       )
                values (
                                ".($this->y06_data == "null" || $this->y06_data == ""?"null":"'".$this->y06_data."'")."
                               ,'$this->y06_hora'
                               ,$this->y06_usuario
                               ,$this->y06_codtipo
                               ,$this->y06_instit
                      ) returning y06_vistoriaslote ";
        $result = db_query($sql);
        $this->y06_vistoriaslote = db_utils::fieldsmemory($result, 0)->y06_vistoriaslote;

        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Lote das vistorias ($this->y06_vistoriaslote) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Lote das vistorias já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Lote das vistorias ($this->y06_vistoriaslote) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->y06_vistoriaslote;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);

        return true;
    }
   // funcao para alteracao
    public function alterar($y06_vistoriaslote = null)
    {
        $this->atualizacampos();
        $sql = " update fiscalizacao.fis_vistoriaslote set ";
        $virgula = "";
        if (trim($this->y06_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y06_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y06_data_dia"] !="")) {
            $sql  .= $virgula." y06_data = '$this->y06_data' ";
            $virgula = ",";
            if (trim($this->y06_data) == null) {
                $this->erro_sql = " Campo Data do lancamento geral nao Informado.";
                $this->erro_campo = "y06_data_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["y06_data_dia"])) {
                $sql  .= $virgula." y06_data = null ";
                $virgula = ",";
                if (trim($this->y06_data) == null) {
                    $this->erro_sql = " Campo Data do lancamento geral nao Informado.";
                    $this->erro_campo = "y06_data_dia";
                    $this->erro_banco = "";
                    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                    $this->erro_status = "0";
                    return false;
                }
            }
        }
        if (trim($this->y06_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y06_hora"])) {
            $sql  .= $virgula." y06_hora = '$this->y06_hora' ";
            $virgula = ",";
            if (trim($this->y06_hora) == null) {
                $this->erro_sql = " Campo Hora do lancamento nao Informado.";
                $this->erro_campo = "y06_hora";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y06_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y06_usuario"])) {
            $sql  .= $virgula." y06_usuario = $this->y06_usuario ";
            $virgula = ",";
            if (trim($this->y06_usuario) == null) {
                $this->erro_sql = " Campo Cod. Usuário nao Informado.";
                $this->erro_campo = "y06_usuario";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y06_codtipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y06_codtipo"])) {
            $sql  .= $virgula." y06_codtipo = $this->y06_codtipo ";
            $virgula = ",";
            if (trim($this->y06_codtipo) == null) {
                $this->erro_sql = " Campo Código do Tipo nao Informado.";
                $this->erro_campo = "y06_codtipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->y06_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y06_instit"])) {
            $sql  .= $virgula." y06_instit = $this->y06_instit ";
            $virgula = ",";
            if (trim($this->y06_instit) == null) {
                $this->erro_sql = " Campo Cod. Instituição nao Informado.";
                $this->erro_campo = "y06_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if ($y06_vistoriaslote!=null) {
            $sql .= " y06_vistoriaslote = $this->y06_vistoriaslote";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Lote das vistorias nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->y06_vistoriaslote;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lote das vistorias nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->y06_vistoriaslote;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->y06_vistoriaslote;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir($y06_vistoriaslote = null, $dbwhere = null)
    {
         $sql = " delete from fiscalizacao.fis_vistoriaslote where ";
         $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($y06_vistoriaslote != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " y06_vistoriaslote = $y06_vistoriaslote ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Lote das vistorias nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$y06_vistoriaslote;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lote das vistorias nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$y06_vistoriaslote;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$y06_vistoriaslote;
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
            $this->erro_sql   = "Record Vazio na Tabela:vistoriaslote";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    public function sql_query($y06_vistoriaslote = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistoriaslote ";
        $sql .= "      inner join db_config  on  db_config.codigo = fis_vistoriaslote.y06_instit";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_vistoriaslote.y06_usuario";
        $sql .= "      inner join fiscalizacao.fis_tipovistorias  on  fis_tipovistorias.y77_codtipo = fis_vistoriaslote.y06_codtipo";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = fis_tipovistorias.y77_instit";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_tipovistorias.y77_coddepto";
        $sql .= "      inner join fiscalizacao.fis_tipoandam  on  fis_tipoandam.y41_codtipo = fis_tipovistorias.y77_tipoandam";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y06_vistoriaslote!=null) {
                $sql2 .= " where fis_vistoriaslote.y06_vistoriaslote = $y06_vistoriaslote ";
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
    public function sql_query_file($y06_vistoriaslote = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from fiscalizacao.fis_vistoriaslote ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($y06_vistoriaslote!=null) {
                $sql2 .= " where fis_vistoriaslote.y06_vistoriaslote = $y06_vistoriaslote ";
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
