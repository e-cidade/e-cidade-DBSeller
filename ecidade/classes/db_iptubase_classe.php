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

class cl_iptubase
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
  public $j01_matric = 0;
  public $j01_numcgm = 0;
  public $j01_idbql = 0;
  public $j01_baixa_dia = null;
  public $j01_baixa_mes = null;
  public $j01_baixa_ano = null;
  public $j01_baixa = null;
  public $j01_codave = 0;
  public $j01_fracao = 0;
  public $j01_tipoimovel = 0;
  public $j01_distrito = null;
  public $j01_hectare = 0;
  public $j01_situcad = null;
  public $j01_datacad_dia = null;
  public $j01_datacad_mes = null;
  public $j01_datacad_ano = null;
  public $j01_datacad = null;
  public $j01_processo = 0;
  public $j01_incra = 0;
  public $j01_descrlocal = null;
  public $j01_unidade = 0;
  public $j01_areaprivativa = 0;
  public $j01_tipoproprietario = null;
  public $j01_fracaoproprietario = 0;
  // cria propriedade com as variaveis do arquivo 
  public $campos = "
                 j01_matric = int4 = Matrícula do Imóvel
                 j01_numcgm = int4 = Numcgm
                 j01_idbql = int4 = Id Lote
                 j01_baixa = date = Baixa
                 j01_codave = int4 = Codigo da Averbacao
                 j01_fracao = float8 = Fracao Ideal
                 j01_tipoproprietario = int4 = Tipo do Proprietário
                 j01_tipoimovel = int4 = Tipo de Imóvel
                 j01_distrito = char(4) = Distrito
                 j01_hectare = float8 = Hectare
                 j01_situcad = varchar(50) = Situação Cadastral
                 j01_datacad = date = Data Cadastro
                 j01_processo = int4 = Processo
                 j01_incra = int4 = Inscrição INCRA
                 j01_descrlocal = varchar(255) = Descrição da Localização
                 j01_unidade = int8 = Unidade
                 j01_areaprivativa = float8 = Area Privativa do Lote
                 j01_fracaoproprietario = float8 = Fração por proprietário
                 ";

  public function __construct()
  {
    $this->rotulo = new rotulo("iptubase");
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
      $this->j01_matric = ($this->j01_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_matric"] : $this->j01_matric);
      $this->j01_numcgm = ($this->j01_numcgm == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_numcgm"] : $this->j01_numcgm);
      $this->j01_idbql = ($this->j01_idbql == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_idbql"] : $this->j01_idbql);
      if ($this->j01_baixa == "") {
        $this->j01_baixa_dia = ($this->j01_baixa_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_baixa_dia"] : $this->j01_baixa_dia);
        $this->j01_baixa_mes = ($this->j01_baixa_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_baixa_mes"] : $this->j01_baixa_mes);
        $this->j01_baixa_ano = ($this->j01_baixa_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_baixa_ano"] : $this->j01_baixa_ano);
        if ($this->j01_baixa_dia != "") {
          $this->j01_baixa = $this->j01_baixa_ano . "-" . $this->j01_baixa_mes . "-" . $this->j01_baixa_dia;
        }
      }
      $this->j01_codave = ($this->j01_codave == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_codave"] : $this->j01_codave);
      $this->j01_fracao = ($this->j01_fracao == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_fracao"] : $this->j01_fracao);
      $this->j01_tipoproprietario = ($this->j01_tipoproprietario == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_tipoproprietario"] : $this->j01_tipoproprietario);
      $this->j01_tipoimovel = ($this->j01_tipoimovel == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_tipoimovel"] : $this->j01_tipoimovel);
      $this->j01_distrito = ($this->j01_distrito == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_distrito"] : $this->j01_distrito);
      $this->j01_hectare = ($this->j01_hectare == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_hectare"] : $this->j01_hectare);
      $this->j01_situcad = ($this->j01_situcad == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_situcad"] : $this->j01_situcad);
      if ($this->j01_datacad == "") {
        $this->j01_datacad_dia = ($this->j01_datacad_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_datacad_dia"] : $this->j01_datacad_dia);
        $this->j01_datacad_mes = ($this->j01_datacad_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_datacad_mes"] : $this->j01_datacad_mes);
        $this->j01_datacad_ano = ($this->j01_datacad_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_datacad_ano"] : $this->j01_datacad_ano);
        if ($this->j01_datacad_dia != "") {
          $this->j01_datacad = $this->j01_datacad_ano . "-" . $this->j01_datacad_mes . "-" . $this->j01_datacad_dia;
        }
      }
      $this->j01_processo = ($this->j01_processo == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_processo"] : $this->j01_processo);
      $this->j01_incra = ($this->j01_incra == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_incra"] : $this->j01_incra);
      $this->j01_descrlocal = ($this->j01_descrlocal == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_descrlocal"] : $this->j01_descrlocal);
      $this->j01_unidade = ($this->j01_unidade == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_unidade"] : $this->j01_unidade);
      $this->j01_areaprivativa = ($this->j01_areaprivativa == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_areaprivativa"] : $this->j01_areaprivativa);
      $this->j01_fracaoproprietario = ($this->j01_fracaoproprietario == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_fracaoproprietario"] : $this->j01_fracaoproprietario);
    } else {
      $this->j01_matric = ($this->j01_matric == "" ? @$GLOBALS["HTTP_POST_VARS"]["j01_matric"] : $this->j01_matric);
    }
  }

  // funcao para inclusao
  public function incluir($j01_matric)
  {
    $this->atualizacampos();
    if ($this->j01_numcgm == null) {
      $this->erro_sql = " Campo Numcgm não informado.";
      $this->erro_campo = "j01_numcgm";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->j01_idbql == null) {
      $this->erro_sql = " Campo Id Lote não informado.";
      $this->erro_campo = "j01_idbql";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    if ($this->j01_baixa == null) {
      $this->j01_baixa = "null";
    }
    if ($this->j01_codave == null) {
      $this->j01_codave = "0";
    }
    if ($this->j01_fracao == null) {
      $this->j01_fracao = "0";
    }
    if ($this->j01_tipoproprietario == null) {
      $this->j01_tipoproprietario = "";
    }

    if ($this->j01_datacad == null) {
      $this->j01_datacad = 'null';
    }

    if ($this->j01_baixa == null) {
      $this->j01_baixa = "null";
    }

    if ($this->j01_codave == null) {
      $this->j01_codave = "0";
    }

    if ($this->j01_fracao == null) {
      $this->j01_fracao = "0";
    }

    if ($this->j01_hectare == null) {
      $this->j01_hectare = "0";
    }

    if ($this->j01_processo == null) {
      $this->j01_processo = "0";
    }

    if ($this->j01_incra == null) {
      $this->j01_incra = "0";
    }

    if ($this->j01_datacad == null) {
      $this->j01_datacad = 'null';
    }

    if ($this->j01_fracaoproprietario == null) {
      $this->j01_fracaoproprietario = "100";
    }

    if ($this->j01_unidade == null) {
      $this->j01_unidade = "1";
    }
    if ($this->j01_areaprivativa == null) {
      $this->j01_areaprivativa = "0";
    }
    if ($j01_matric == "" || $j01_matric == null) {
      $result = db_query("select nextval('iptubase_j01_matric_seq')");
      if ($result == false) {
        $this->erro_banco = str_replace("\n", "", @pg_last_error());
        $this->erro_sql   = "Verifique o cadastro da sequencia: iptubase_j01_matric_seq do campo: j01_matric";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
      $this->j01_matric = pg_result($result, 0, 0);
    } else {
      $result = db_query("select last_value from iptubase_j01_matric_seq");
      if (($result != false) && (pg_result($result, 0, 0) < $j01_matric)) {
        $this->erro_sql = " Campo j01_matric maior que último número da sequencia.";
        $this->erro_banco = "Sequencia menor que este número.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      } else {
        $this->j01_matric = $j01_matric;
      }
    }
    if (($this->j01_matric == null) || ($this->j01_matric == "")) {
      $this->erro_sql = " Campo j01_matric não declarado.";
      $this->erro_banco = "Chave Primaria zerada.";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $sql = "insert into iptubase(
                                       j01_matric
                                      ,j01_numcgm
                                      ,j01_idbql
                                      ,j01_baixa
                                      ,j01_codave
                                      ,j01_fracao
                                      ,j01_tipoproprietario
                                      ,j01_tipoimovel 
                                      ,j01_distrito 
                                      ,j01_hectare 
                                      ,j01_situcad 
                                      ,j01_datacad 
                                      ,j01_processo 
                                      ,j01_incra 
                                      ,j01_descrlocal 
                                      ,j01_unidade 
                                      ,j01_areaprivativa
                                      ,j01_fracaoproprietario
                       )
                values (
                                $this->j01_matric
                               ,$this->j01_numcgm
                               ,$this->j01_idbql
                               ," . ($this->j01_baixa == "null" || $this->j01_baixa == "" ? "null" : "'" . $this->j01_baixa . "'") . "
                               ,$this->j01_codave
                               ,$this->j01_fracao
                               ,$this->j01_tipoproprietario
                               ,$this->j01_tipoimovel 
                               ,'$this->j01_distrito' 
                               ,$this->j01_hectare 
                               ,'$this->j01_situcad' 
                               ," . ($this->j01_datacad == "null" || $this->j01_datacad == "" ? "null" : "'" . $this->j01_datacad . "'") . " 
                               ,$this->j01_processo 
                               ,$this->j01_incra 
                               ,'$this->j01_descrlocal' 
                               ,$this->j01_unidade 
                               ,$this->j01_areaprivativa 
                               ,$this->j01_fracaoproprietario
                      )";
    $result = db_query($sql);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
        $this->erro_sql   = "Proprietario do Lote ($this->j01_matric) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_banco = "Proprietario do Lote já Cadastrado";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      } else {
        $this->erro_sql   = "Proprietario do Lote ($this->j01_matric) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      }
      $this->erro_status = "0";
      $this->numrows_incluir = 0;
      return false;
    }
    $this->erro_banco = "";
    $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
    $this->erro_sql .= "Valores : " . $this->j01_matric;
    $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
    $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
    $this->erro_status = "1";
    $this->numrows_incluir = pg_affected_rows($result);
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->j01_matric));
      if (($resaco != false) || ($this->numrows != 0)) {

        $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
        $acount = pg_result($resac, 0, 0);
        $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
        $resac = db_query("insert into db_acountkey values($acount,141,'$this->j01_matric','I')");
        $resac = db_query("insert into db_acount values($acount,27,141,'','" . AddSlashes(pg_result($resaco, 0, 'j01_matric')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,142,'','" . AddSlashes(pg_result($resaco, 0, 'j01_numcgm')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,143,'','" . AddSlashes(pg_result($resaco, 0, 'j01_idbql')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,144,'','" . AddSlashes(pg_result($resaco, 0, 'j01_baixa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,145,'','" . AddSlashes(pg_result($resaco, 0, 'j01_codave')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,368,'','" . AddSlashes(pg_result($resaco, 0, 'j01_fracao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1010626,'','" . AddSlashes(pg_result($resaco, 0, 'j01_tipoproprietario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011672,'','" . AddSlashes(pg_result($resaco, 0, 'j01_tipoimovel')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011742,'','" . AddSlashes(pg_result($resaco, 0, 'j01_distrito')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011743,'','" . AddSlashes(pg_result($resaco, 0, 'j01_hectare')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011744,'','" . AddSlashes(pg_result($resaco, 0, 'j01_situcad')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011745,'','" . AddSlashes(pg_result($resaco, 0, 'j01_datacad')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011746,'','" . AddSlashes(pg_result($resaco, 0, 'j01_processo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011747,'','" . AddSlashes(pg_result($resaco, 0, 'j01_incra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1011748,'','" . AddSlashes(pg_result($resaco, 0, 'j01_descrlocal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1014442,'','" . AddSlashes(pg_result($resaco, 0, 'j01_unidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        $resac = db_query("insert into db_acount values($acount,27,1014504,'','" . AddSlashes(pg_result($resaco, 0, 'j01_areaprivativa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
      }
    }
    return true;
  }

  // funcao para alteracao
  public function alterar($j01_matric = null)
  {
    $this->atualizacampos();
    $sql = " update iptubase set ";
    $virgula = "";
    if (trim($this->j01_matric) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_matric"])) {
      $sql  .= $virgula . " j01_matric = $this->j01_matric ";
      $virgula = ",";
      if (trim($this->j01_matric) == null) {
        $this->erro_sql    = " Campo Matrícula do Imóvel não informado.";
        $this->erro_campo  = "j01_matric";
        $this->erro_banco  = "";
        $this->erro_msg    = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j01_numcgm) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_numcgm"])) {
      $sql  .= $virgula . " j01_numcgm = $this->j01_numcgm ";
      $virgula = ",";
      if (trim($this->j01_numcgm) == null) {
        $this->erro_sql = " Campo Numcgm não informado.";
        $this->erro_campo = "j01_numcgm";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j01_idbql) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_idbql"])) {
      $sql  .= $virgula . " j01_idbql = $this->j01_idbql ";
      $virgula = ",";
      if (trim($this->j01_idbql) == null) {
        $this->erro_sql = " Campo Id Lote não informado.";
        $this->erro_campo = "j01_idbql";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if (trim($this->j01_baixa) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_baixa_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["j01_baixa_dia"] != "")) {
      $sql  .= $virgula . " j01_baixa = '$this->j01_baixa' ";
      $virgula = ",";
    } else {
      if (isset($GLOBALS["HTTP_POST_VARS"]["j01_baixa_dia"])) {
        $sql  .= $virgula . " j01_baixa = null ";
        $virgula = ",";
      }
    }
    if (trim($this->j01_codave) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_codave"])) {
      if (trim($this->j01_codave) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_codave"])) {
        $this->j01_codave = "0";
      }
      $sql  .= $virgula . " j01_codave = $this->j01_codave ";
      $virgula = ",";
    }
    if (trim($this->j01_fracao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_fracao"])) {
      if (trim($this->j01_fracao) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_fracao"])) {
        $this->j01_fracao = "0";
      }
      $sql  .= $virgula . " j01_fracao = $this->j01_fracao ";
      $virgula = ",";
    }
    if (trim($this->j01_tipoproprietario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_tipoproprietario"])) {
      if (trim($this->j01_tipoproprietario) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_tipoproprietario"])) {
        $this->j01_tipoproprietario = "";
      }
      $sql  .= $virgula . " j01_tipoproprietario = $this->j01_tipoproprietario ";
      $virgula = ",";
    }

    // Campos Imóvel Rural
    if (trim($this->j01_tipoimovel) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_tipoimovel"])) {
      if (trim($this->j01_tipoimovel) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_tipoimovel"])) {
        $this->j01_tipoimovel = null;
      }
      $sql .= $virgula . " j01_tipoimovel = $this->j01_tipoimovel ";
      $virgula = ",";
    }
    if (trim($this->j01_distrito) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_distrito"])) {
      if (trim($this->j01_distrito) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_distrito"])) {
        $this->j01_distrito = "0";
      }
      $sql .= $virgula . " j01_distrito = $this->j01_distrito ";
      $virgula = ",";
    }
    if (trim($this->j01_hectare) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_hectare"])) {
      if (trim($this->j01_hectare) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_hectare"])) {
        $this->j01_hectare = "0";
      }
      $sql .= $virgula . " j01_hectare = $this->j01_hectare ";
      $virgula = ",";
    }
    if (trim($this->j01_situcad) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_situcad"])) {
      if (trim($this->j01_situcad) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_situcad"])) {
        $this->j01_situcad = "0";
      }
      $sql .= $virgula . " j01_situcad = '$this->j01_situcad' ";
      $virgula = ",";
    }
    if (trim($this->j01_datacad) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_datacad"])) {
      if (trim($this->j01_datacad) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_datacad"])) {
        $this->j01_datacad = "0";
      }
      $sql .= $virgula . " j01_datacad = '$this->j01_datacad' ";
      $virgula = ",";
    }
    if (trim($this->j01_processo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_processo"])) {
      if (trim($this->j01_processo) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_processo"])) {
        $this->j01_processo = "0";
      }
      $sql .= $virgula . " j01_processo = $this->j01_processo ";
      $virgula = ",";
    }
    if (trim($this->j01_incra) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_incra"])) {
      if (trim($this->j01_incra) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_incra"])) {
        $this->j01_incra = "0";
      }
      $sql .= $virgula . " j01_incra = $this->j01_incra ";
      $virgula = ",";
    }
    if (trim($this->j01_descrlocal) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_descrlocal"])) {
      if (trim($this->j01_descrlocal) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_descrlocal"])) {
        $this->j01_descrlocal = "0";
      }
      $sql .= $virgula . " j01_descrlocal = '$this->j01_descrlocal' ";
      $virgula = ",";
    }
    if (trim($this->j01_fracaoproprietario) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_fracaoproprietario"])) {
      if (trim($this->j01_fracaoproprietario) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_fracaoproprietario"])) {
        $this->j01_fracaoproprietario = "0";
      }
      $sql  .= $virgula . " j01_fracaoproprietario = $this->j01_fracaoproprietario ";
      $virgula = ",";
    }
    if (trim($this->j01_unidade) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_unidade"])) {
      if (trim($this->j01_unidade) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_unidade"])) {
        $this->j01_unidade = "1";
      }
      $sql  .= $virgula . " j01_unidade = $this->j01_unidade ";
      $virgula = ",";
    }
    if (trim($this->j01_areaprivativa) != "" || isset($GLOBALS["HTTP_POST_VARS"]["j01_areaprivativa"])) {
      if (trim($this->j01_areaprivativa) == "" && isset($GLOBALS["HTTP_POST_VARS"]["j01_areaprivativa"])) {
        $this->j01_areaprivativa = "0";
      }
      $sql  .= $virgula . " j01_areaprivativa = $this->j01_areaprivativa ";
      $virgula = ",";
    }
    $sql .= " where ";
    if ($j01_matric != null) {
      $sql .= " j01_matric = $this->j01_matric";
    }
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      $resaco = $this->sql_record($this->sql_query_file($this->j01_matric));
      if ($this->numrows > 0) {

        for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

          $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac = db_query("insert into db_acountkey values($acount,141,'$this->j01_matric','A')");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_matric"]) || $this->j01_matric != "")
            $resac = db_query("insert into db_acount values($acount,27,141,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_matric')) . "','$this->j01_matric'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_numcgm"]) || $this->j01_numcgm != "")
            $resac = db_query("insert into db_acount values($acount,27,142,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_numcgm')) . "','$this->j01_numcgm'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_idbql"]) || $this->j01_idbql != "")
            $resac = db_query("insert into db_acount values($acount,27,143,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_idbql')) . "','$this->j01_idbql'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_baixa"]) || $this->j01_baixa != "")
            $resac = db_query("insert into db_acount values($acount,27,144,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_baixa')) . "','$this->j01_baixa'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_codave"]) || $this->j01_codave != "")
            $resac = db_query("insert into db_acount values($acount,27,145,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_codave')) . "','$this->j01_codave'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_fracao"]) || $this->j01_fracao != "")
            $resac = db_query("insert into db_acount values($acount,27,368,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_fracao')) . "','$this->j01_fracao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");

          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_tipoimovel"]) || $this->j01_tipoimovel != "")
            $resac = db_query("insert into db_acount values($acount,27,1011672,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_tipoimovel')) . "','$this->j01_tipoimovel'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_distrito"]) || $this->j01_distrito != "")
            $resac = db_query("insert into db_acount values($acount,27,1011742,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_distrito')) . "','$this->j01_distrito'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_hectare"]) || $this->j01_hectare != "")
            $resac = db_query("insert into db_acount values($acount,27,1011743,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_hectare')) . "','$this->j01_hectare'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_situcad"]) || $this->j01_situcad != "")
            $resac = db_query("insert into db_acount values($acount,27,1011744,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_situcad')) . "','$this->j01_situcad'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_datacad"]) || $this->j01_datacad != "")
            $resac = db_query("insert into db_acount values($acount,27,1011745,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_datacad')) . "','$this->j01_datacad'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_processo"]) || $this->j01_processo != "")
            $resac = db_query("insert into db_acount values($acount,27,1011746,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_processo')) . "','$this->j01_processo'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_incra"]) || $this->j01_incra != "")
            $resac = db_query("insert into db_acount values($acount,27,1011747,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_incra')) . "','$this->j01_incra'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_descrlocal"]) || $this->j01_descrlocal != "")
            $resac = db_query("insert into db_acount values($acount,27,1011748,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_descrlocal')) . "','$this->j01_descrlocal'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_unidade"]) || $this->j01_unidade != "")
            $resac = db_query("insert into db_acount values($acount,27,1014442,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_unidade')) . "','$this->j01_unidade'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_areaprivativa"]) || $this->j01_areaprivativa != "")
            $resac = db_query("insert into db_acount values($acount,27,1014504,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_areaprivativa')) . "','$this->j01_areaprivativa'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          if (isset($GLOBALS["HTTP_POST_VARS"]["j01_fracaoproprietario"]) || $this->j01_fracaoproprietario != "") {
            $resac = db_query("insert into db_acount values($acount,27,1013929,'" . AddSlashes(pg_result($resaco, $conresaco, 'j01_fracaoproprietario')) . "','$this->j01_fracaoproprietario'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          }
        }
      }
    }

    $result = db_query($sql);
    if (!$result) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Proprietario do Lote não Alterado. Alteração Abortada.\\n";
      $this->erro_sql .= "Valores : " . $this->j01_matric;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Proprietario do Lote não foi Alterado. Alteração Executada.\\n";
        $this->erro_sql .= "Valores : " . $this->j01_matric;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Alteração efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $this->j01_matric;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }

  // funcao para exclusao
  public function excluir($j01_matric = null, $dbwhere = null)
  {
    $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
    if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
      && ($lSessaoDesativarAccount === false))) {

      if (empty($dbwhere)) {

        $resaco = $this->sql_record($this->sql_query_file($j01_matric));
      } else {
        $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
      }
      if (($resaco != false) || ($this->numrows != 0)) {

        for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

          $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
          $acount = pg_result($resac, 0, 0);
          $resac  = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
          $resac  = db_query("insert into db_acountkey values($acount,141,'$j01_matric','E')");
          $resac  = db_query("insert into db_acount values($acount,27,141,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_matric')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,142,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_numcgm')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,143,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_idbql')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,144,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_baixa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,145,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_codave')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,368,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_fracao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1010626,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_vagas')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011150,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_tipoproprietario')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011672,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_tipoimovel')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011742,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_distrito')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011743,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_hectare')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011744,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_situcad')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011745,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_datacad')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011746,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_processo')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011747,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_incra')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1011748,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_descrlocal')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1014442,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_unidade')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
          $resac  = db_query("insert into db_acount values($acount,27,1014504,'','" . AddSlashes(pg_result($resaco, $iresaco, 'j01_areaprivativa')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
        }
      }
    }
    $sql = " delete from iptubase
                    where ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j01_matric)) {
        if (!empty($sql2)) {
          $sql2 .= " and ";
        }
        $sql2 .= " j01_matric = $j01_matric ";
      }
    } else {
      $sql2 = $dbwhere;
    }
    $result = db_query($sql . $sql2);
    if ($result == false) {
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Proprietario do Lote não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql .= "Valores : " . $j01_matric;
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Proprietario do Lote não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql .= "Valores : " . $j01_matric;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : " . $j01_matric;
        $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $this->numrows = pg_num_rows($result);
    if ($this->numrows == 0) {
      $this->erro_banco = "";
      $this->erro_sql   = "Record Vazio na Tabela:iptubase";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }

  public function sql_query($j01_matric = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos}                                                                                       ";
    $sql .= "  from iptubase                                                                                        ";
    $sql .= " inner join lote             on  lote.j34_idbql  = iptubase.j01_idbql                                  ";
    $sql .= " inner join cgm              on  cgm.z01_numcgm  = iptubase.j01_numcgm                                 ";
    $sql .= " inner join bairro           on  bairro.j13_codi = lote.j34_bairro                                     ";
    $sql .= " inner join setor            on  setor.j30_codi  = lote.j34_setor                                      ";
    $sql .= " inner join zonas            on  zonas.j50_zona  = lote.j34_zona                                       ";
    $sql .= " inner join tipoproprietario on tipoproprietario.j163_tipoproprietario = iptubase.j01_tipoproprietario ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j01_matric)) {
        $sql2 .= " where iptubase.j01_matric = $j01_matric ";
      }
    } else if (!empty($dbwhere)) {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if (!empty($ordem)) {
      $sql .= " order by {$ordem}";
    }
    return $sql;
  }

  public function sql_query_file($j01_matric = null, $campos = "*", $ordem = null, $dbwhere = "")
  {

    $sql  = "select {$campos} ";
    $sql .= "  from iptubase ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($j01_matric)) {
        $sql2 .= " where iptubase.j01_matric = $j01_matric ";
      }
    } else if (!empty($dbwhere)) {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if (!empty($ordem)) {
      $sql .= " order by {$ordem}";
    }
    return $sql;
  }

  public function sql_query_regmovel($j01_matric = null, $campos = "*", $ordem = null, $dbwhere = "")
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

    $sql .= "  from iptubase 												  	  		  									  ";
    $sql .= " inner join lote 		 	       on j34_idbql  		   = j01_idbql  		    ";
    $sql .= "	inner join cgm 			 	       on z01_numcgm 		   = j01_numcgm 		    ";
    $sql .= "  left join testpri 		       on j49_idbql  		   = j01_idbql  		    ";
    $sql .= "  left join testada 		       on j36_idbql  		   = j49_idbql  		    ";
    $sql .= "           					        and j36_face  		   = j49_face   		    ";
    $sql .= "      						 	          and j36_codigo 		   = j49_codigo 		    ";
    $sql .= "  left join testadanumero     on j15_idbql        = j36_idbql          ";
    $sql .= "                             and j15_face         = j36_face           ";
    $sql .= "  left join face              on j36_face         = j37_face           ";
    $sql .= "  left join ruas 		 	       on j14_codigo 		   = j49_codigo 		    ";
    $sql .= "	 left join iptuconstr 	 	   on j01_matric 		   = j39_matric 		    ";
    $sql .= "	 left join iptuant 		       on j01_matric 		   = j40_matric 		    ";
    $sql .= "  left join ruas as ruase     on ruase.j14_codigo = j39_codigo 		    ";
    $sql .= "  left join iptubaseregimovel on j04_matric	 	   = j01_matric 		    ";
    $sql .= "  left join setorregimovel    on j69_sequencial   = j04_setorregimovel ";
    $sql .= "  left join loteloc           on j06_idbql        = j01_idbql					";
    $sql .= "	 left join setorloc          on j05_codigo       = j06_setorloc				";
    $sql .= "	 left join iptubaixa         on j02_matric       = j01_matric  				";
    $sql .= "  left join lotesetorfiscal   on j91_idbql        = j01_idbql  				";
    $sql .= "  left join setorfiscal       on j91_codigo       = j90_codigo 				";
    $sql2 = "";

    if ($dbwhere == "") {
      if ($j01_matric != null) {
        $sql2 .= " where iptubase.j01_matric = $j01_matric ";
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
  public function proprietario_query($j01_matric = null, $campos = "*", $ordem = null, $dbwhere = "")
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
    $sql .= " from proprietario";
    $sql2 = "";
    if ($dbwhere == "") {
      if ($j01_matric != null) {
        $sql2 .= " where proprietario.j01_matric = $j01_matric ";
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

  public function sqlmatriculas_setor($pesquisasetor = 0)
  {
    $sql = "select lote.j34_idbql as db_lote, j34_setor, j34_quadra, j34_lote
	              , j34_area, j34_areal,j01_matric,
	              ruas.j14_nome, bairro.j13_descr
                  from lote
				  inner join bairro on j13_codi = j34_bairro
				  inner join iptubase on j01_idbql = j34_idbql
                  left outer join testpri on j34_idbql = j49_idbql
				  left outer join ruas on j14_codigo = j49_codigo";
    if ($pesquisasetor != 0) {
      $sql .= " where j34_setor = $pesquisasetor";
    }
    return $sql;
  }
  public function sqlmatriculas_ruas($pesquisaRua = 0, $numero = 0, $filtrotipo = 'todos')
  {
    $order_by = "";
    $sql = " select distinct j01_matric,j01_tipoimp,z01_nome,j40_refant,j39_numero,j39_compl,proprietario
             from proprietario";
    if ($pesquisaRua != 0) {
      $sql .= " where (j14_codigo = $pesquisaRua or codpri = $pesquisaRua)";
      $order_by = "order by  j39_numero, j01_matric";
      if ($numero != 0) {
        $sql .= "  and  CAST(j39_numero as TEXT) like '$numero%'";
        $order_by = "order by  j39_numero, j01_matric";
      }
    }
    if ($filtrotipo != 'todos') {
      $sql .= " and j01_tipoimp = '" . $filtrotipo . "'";
    }
    if ($order_by != "") {
      $sql .= " $order_by  ";
    } else {
      $sql .= " order by j01_matric, j39_numero";
    }
    return $sql;
  }

  public  function sqlmatriculas_nome($pesquisaPorNome = 0, $sCampos = "*")
  {
    $sql = "
   select distinct {$sCampos} from ( select j01_matric, 'PROPRIETARIO'::varchar(12) as proprietario, j01_idbql, cgm.z01_nome, j01_baixa,

																	   case
																	     when j01_matric is null and j18_testadanumero = true
																	       then testadanumero.j15_numero
																	     else iptuconstr.j39_numero
																	   end as numero,

                                     case
                                       when j01_matric is null and j18_testadanumero = true
                                         then testadanumero.j15_compl
                                       else iptuconstr.j39_compl
                                     end as complemento

                                     from iptubase
                                     inner join cgm            on j01_numcgm = z01_numcgm
                                     inner join cfiptu         on j18_anousu = " . db_getsession('DB_anousu') . "
																		 left join iptuconstr      on j39_matric = j01_matric
																		                          and j39_dtdemo is null
																		                          and j39_idprinc is true
																		 left join testadanumero   on testadanumero.j15_idbql = j01_idbql

                                     where j01_numcgm = $pesquisaPorNome
   union
                                      select j01_matric, 'OUTRO PROPR'::varchar(12) as proprietario, j01_idbql, cgm.z01_nome, j01_baixa,

                                      case
                                        when j01_matric is null and j18_testadanumero = true
                                          then testadanumero.j15_numero
                                        else iptuconstr.j39_numero
                                      end as numero,

                                      case
                                        when j01_matric is null and j18_testadanumero = true
                                          then testadanumero.j15_compl
                                        else iptuconstr.j39_compl
                                      end as complemento

                                      from propri
                                      inner join iptubase      on j42_matric = j01_matric
                                      inner join cgm           on j42_numcgm = z01_numcgm
                                      inner join cfiptu        on j18_anousu = " . db_getsession('DB_anousu') . "
																		  left join iptuconstr     on j39_matric = j01_matric
																		                          and j39_dtdemo is null
																		                          and j39_idprinc is true
																		  left join testadanumero  on testadanumero.j15_idbql = j01_idbql

                                      where j42_numcgm = $pesquisaPorNome
   union
                                      select j01_matric, 'PROMITENTE'::varchar(12) as proprietario, j01_idbql, cgm.z01_nome, j01_baixa,

                                      case
                                        when j01_matric is null and j18_testadanumero = true
                                          then testadanumero.j15_numero
                                        else iptuconstr.j39_numero
                                      end as numero,

                                      case
                                        when j01_matric is null and j18_testadanumero = true
                                          then testadanumero.j15_compl
                                        else iptuconstr.j39_compl
                                      end as complemento

                                      from promitente
                                      inner join iptubase      on j41_matric = j01_matric
                                      inner join cgm           on j41_numcgm = z01_numcgm
                                      inner join cfiptu        on j18_anousu = " . db_getsession('DB_anousu') . "
																	    left join iptuconstr     on j39_matric = j01_matric
																	                            and j39_dtdemo is null
																	                            and j39_idprinc is true
																	    left join testadanumero  on testadanumero.j15_idbql = j01_idbql


                                      where j41_numcgm = $pesquisaPorNome
	) as dados


	  inner join lote          on j34_idbql  = j01_idbql
	  left outer join testpri  on j49_idbql  = j01_idbql
    left outer join ruas     on j49_codigo = j14_codigo
    left outer join ruastipo on j88_codigo = j14_tipo
	  left outer join bairro   on j34_bairro = j13_codi

    ";

    return $sql;
  }

  public function sqlmatriculas_nome_numero($pesquisaPorNome = 0, $regraCgmIptu = 2)
  {
    switch ($regraCgmIptu) {
      case 0:
        $sql = "
					select distinct *
					  from (
						 select distinct * from (
									select distinct
												 j01_matric,
												 case when j39_matric is null then 'TERRITORIAL' else 'PREDIAL' end as j01_tipoimp,
												 'PROPRIETARIO'::varchar(12) as proprietario,
												 j01_idbql,
												 cgm.z01_nome,
												 j01_baixa
									from   iptubase
									left join iptuconstr on j39_matric = j01_matric and j39_dtdemo is null
									inner join cgm on j01_numcgm = z01_numcgm
									where  j01_numcgm = $pesquisaPorNome
									union
									select j01_matric,
												 case when j39_matric is null then 'TERRITORIAL' else 'PREDIAL' end as j01_tipoimp,
												 'OUTRO PROPR'::varchar(12) as proprietario,
												 j01_idbql,
												 cgm.z01_nome,
												 j01_baixa
									from   propri
									inner join iptubase on j42_matric = j01_matric
									left join iptuconstr on j39_matric = j01_matric and j39_dtdemo is null
									inner join cgm on j42_numcgm = z01_numcgm
									where  j42_numcgm = $pesquisaPorNome
									union
									select j01_matric,
												 case when j39_matric is null then 'TERRITORIAL' else 'PREDIAL' end as j01_tipoimp,
												 'PROMITENTE'::varchar(12) as proprietario,
												 j01_idbql,
												 cgm.z01_nome,
												 j01_baixa
									from   promitente
									inner join iptubase on j41_matric = j01_matric
									left join iptuconstr on j39_matric = j01_matric and j39_dtdemo is null
									inner join cgm on j41_numcgm = z01_numcgm
									where j41_numcgm = $pesquisaPorNome
						 ) as dados
						 inner join lote                   on j34_idbql = j01_idbql
						 left outer join testpri           on j49_idbql = j01_idbql
						 left outer join ruas              on j49_codigo = j14_codigo
						 left outer join bairro            on j34_bairro = j13_codi
                                                 left outer join iptubaseregimovel on j01_matric = j04_matric) as x
						 inner join proprietario_ender     on x.j01_matric = proprietario_ender.j01_matric
						";
        break;
      case 1:
        $sql = "
						select distinct * from (
						 select distinct * from (
									select distinct
												 j01_matric,
												 case when j39_matric is null then 'TERRITORIAL' else 'PREDIAL' end as j01_tipoimp,
												 'PROPRIETARIO'::varchar(12) as proprietario,
												 j01_idbql,
												 cgm.z01_nome,
												 j01_baixa
									from   iptubase
									left join iptuconstr on j39_matric = j01_matric and j39_dtdemo is null
									inner join cgm on j01_numcgm = z01_numcgm
									where  j01_numcgm = $pesquisaPorNome
									union
									select j01_matric,
												 case when j39_matric is null then 'TERRITORIAL' else 'PREDIAL' end as j01_tipoimp,
												 'OUTRO PROPR'::varchar(12) as proprietario,
												 j01_idbql,
												 cgm.z01_nome,
												 j01_baixa
									from   propri
									inner join iptubase on j42_matric = j01_matric
									left join iptuconstr on j39_matric = j01_matric and j39_dtdemo is null
									inner join cgm on j42_numcgm = z01_numcgm
									where  j42_numcgm = $pesquisaPorNome
						 ) as dados
						 inner join lote                    on j34_idbql = j01_idbql
						 left outer join testpri            on j49_idbql = j01_idbql
						 left outer join ruas               on j49_codigo = j14_codigo
						 left outer join bairro             on j34_bairro = j13_codi
                                                 left outer join iptubaseregimovel on j01_matric = j04_matric) as x
						 inner join proprietario_ender on x.j01_matric = proprietario_ender.j01_matric
				";
        break;
      case 2:
        $sql = "
			 	  select distinct * from (
						 select distinct * from (
									select distinct
												 iptubase.j01_matric,
												 case when j39_matric is null then 'TERRITORIAL'
												      else 'PREDIAL' end as j01_tipoimp,
												 case when j41_matric is null then 'PROPRIETARIO'::varchar(12)
												      else 'PROMITENTE'::varchar(12) end as proprietario,
												 j01_idbql,
												 case when j41_matric is null then a.z01_nome
												      else b.z01_nome end as z01_nome,
												 j01_baixa
									from   iptubase
									left join iptuconstr on j39_matric = iptubase.j01_matric and j39_dtdemo is null
									left join promitente on j41_matric = iptubase.j01_matric and j41_tipopro is true
									left join cgm a on a.z01_numcgm = iptubase.j01_numcgm
									left join cgm b on b.z01_numcgm = j41_numcgm
									where case when j41_matric is null then iptubase.j01_numcgm = $pesquisaPorNome
									           else j41_numcgm = $pesquisaPorNome end
						 ) as dados
						 inner join lote                    on j34_idbql = dados.j01_idbql
						 left outer join testpri            on j49_idbql = dados.j01_idbql
						 left outer join ruas               on j49_codigo = j14_codigo
						 left outer join bairro             on j34_bairro = j13_codi
                                                 left outer join iptubaseregimovel on j01_matric = j04_matric) as x
						 inner join proprietario_ender on x.j01_matric = proprietario_ender.j01_matric


			";
        break;
    }

    return $sql;
  }

  public function sqlmatriculas_imobiliaria($pesquisaPorImobiliaria = 0)
  {
    $sql = "
    select distinct * from (  select j01_matric, c.z01_nome as proprietario, j01_idbql, cgm.z01_nome
                                   from imobil
 	                 inner join iptubase on j44_matric = j01_matric
                                      inner join cgm on j01_numcgm = cgm.z01_numcgm
	                 inner join cgm c on j44_numcgm = c.z01_numcgm
                                      where j44_numcgm = $pesquisaPorImobiliaria
	) as dados
	  inner join lote on j34_idbql = j01_idbql
	  left outer join testpri on j49_idbql = j01_idbql
	  left outer join ruas on j49_codigo = j14_codigo
	  left outer join bairro on j34_bairro = j13_codi
    ";
    return $sql;
  }

  public function sqlmatriculas_bairros($pesquisaBairro = 0)
  {
    $sql = "
  select iptubase.j01_matric, cgm.z01_nome,cgm.z01_ender,cgm.z01_munic,cgm.z01_cep,cgm.z01_uf ,lote.*
          from lote
          inner join iptubase on j34_idbql = j01_idbql
          inner join cgm on z01_numcgm = j01_numcgm
   ";
    if ($pesquisaBairro != 0) {
      $sql .= "where j34_bairro = $pesquisaBairro";
    }
    return $sql;
  }
  public function proprietario_record($sql)
  {
    $result = @pg_query($sql);
    if ($result == false) {
      $this->numrows    = 0;
      $this->erro_banco = str_replace("\n", "", @pg_last_error());
      $this->erro_sql   = "Erro ao selecionar os registros.";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    $this->numrows = pg_numrows($result);
    if ($this->numrows == 0) {
      $this->erro_banco = "";
      $this->erro_sql   = "Proprietarios nao Encontrados";
      $this->erro_msg   = "Usuário: \n\n " . $this->erro_sql . " \n\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }

  public function sqlmatriculas_IDBQL($pesquisaPorIDBQL = 0)
  {
    $sql = "
    select distinct * from (  select j01_matric, 'PROPRIETARIO'::varchar(12) as proprietario, j01_idbql, cgm.z01_nome
                                      from iptubase
                                      inner join cgm on j01_numcgm = z01_numcgm
                                      where j01_idbql = $pesquisaPorIDBQL
     ) as dados
     inner join lote on j34_idbql = j01_idbql
     left outer join testpri on j49_idbql = j01_idbql
     left outer join ruas on j49_codigo = j14_codigo
     left outer join bairro on j34_bairro = j13_codi
   ";
    return $sql;
  }
  public function sqlmatriculas_setorQuadra($pesquisasetor = "", $pesquisaquadra = "")
  {
    $sql = "select lote.j34_idbql as db_lote, j34_setor, j34_quadra, j34_lote
	              , j34_area, j34_areal,j01_matric,
	              ruas.j14_nome, bairro.j13_descr
                  from lote
				  inner join bairro on j13_codi = j34_bairro
				  inner join iptubase on j01_idbql = j34_idbql
				  left outer join testpri on j34_idbql = j49_idbql
				  left outer join ruas on j14_codigo = j49_codigo";
    if ($pesquisasetor != "") {
      $sql .= " where j34_setor = '" . strtoupper($pesquisasetor) . "' and j34_quadra = '" . strtoupper($pesquisaquadra) . "'";
    }
    return $sql;
  }
  function __toString()
  {
    return "Object";
  }

  public function sql_query_constr($j01_matric = null, $campos = "*", $ordem = null, $dbwhere = "")
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
    $sql .= " from iptubase ";
    $sql .= "      inner join lote  on  lote.j34_idbql = iptubase.j01_idbql";
    $sql .= "      inner join cgm  on  cgm.z01_numcgm = iptubase.j01_numcgm";
    $sql .= "      inner join bairro  on  bairro.j13_codi = lote.j34_bairro";
    $sql .= "      inner join setor  on  setor.j30_codi = lote.j34_setor";
    $sql .= "      left outer join iptuconstr on iptubase.j01_matric = iptuconstr.j39_matric";
    $sql .= "      left outer join iptuant on iptubase.j01_matric = iptuant.j40_matric";
    $sql2 = "";
    if ($dbwhere == "") {
      if ($j01_matric != null) {
        $sql2 .= " where iptubase.j01_matric = $j01_matric ";
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

  public function sql_query_enderecoEntrega($iNumCgmProprietario = null, $sCampos = "*", $sWhere = null)
  {

    if (empty($sWhere) && !empty($iNumCgmProprietario)) {
      $sWhere = " where j01_numcgm = {$iNumCgmProprietario} ";
    } else if (!empty($sWhere) && !empty($iNumCgmProprietario)) {
      $sWhere .= " where {$sWhere} and j01_numcgm = {$iNumCgmProprietario} ";
    }

    $sSql  = "select {$sCampos}                                     ";
    $sSql .= "  from iptubase                                       ";
    $sSql .= "       left join iptuender on j43_matric = j01_matric ";
    $sSql .= $sWhere;
    return $sSql;
  }
  public function sql_query_area_total($sSetor, $sQuadra, $sLote)
  {

    $sql = "select coalesce(sum(j34_area), 0) as area_total
		          from (
	           				select distinct j34_idbql, j34_area
	           					from lote
	           				 inner join iptubase on  j01_idbql = j34_idbql
	           				 where j34_setor  = '$sSetor'
	             				 and j34_quadra = '$sQuadra'
	             				 and j34_lote   = '$sLote'
	             				 and j01_baixa is null ) as x";
    return $sql;
  }
  public function sql_query_area_contruida($sMatricula)
  {

    $sql = "select coalesce(sum(j39_area), 0) as area_construida
	            from iptuconstr
        inner join iptubase on j01_matric = j39_matric
	           where j39_matric = '$sMatricula'
   	           and j39_dtdemo is null
   	           and j01_baixa is null";

    return $sql;
  }
  public function sql_query_imobiliaria($iMatricula, $sCampos = "*")
  {
    $sql = "select $sCampos
		          from imobil
		          inner join cgm on cgm.z01_numcgm  = imobil.j44_numcgm
		         where j44_matric = $iMatricula";
    return $sql;
  }
  public function sql_query_setorfiscal($iMatricula, $sCampos = "*")
  {

    $sql = "select $sCampos
		          from lotesetorfiscal
		         inner join cadastro.setorfiscal on j90_codigo = j91_codigo
		         inner join iptubase             on j01_idbql  = j91_idbql ";
    if ($iMatricula != null) {
      $sql .= "where j01_matric = $iMatricula";
    }

    return $sql;
  }

  public function sql_query_proprietariolote($sWhere = null)
  {
    $sql = "select proprietario.*, ";
    $sql .= "      j50_descr, ";
    $sql .= "      j15_numero, ";
    $sql .= "      j15_compl, ";
    $sql .= "      round(((round((select rnfracao ";
    $sql .= "      from fc_iptu_fracionalote(j01_matric," . db_getsession("DB_anousu") . ",true,false,false)),10) ";
    $sql .= "      * lote.j34_area)/100),10) as area_matric, ";
    $sql .= "      ll.j34_descr, ";
    $sql .= "      c.z01_nome   as promitente, ";
    $sql .= "      c.z01_ender  as ender_promitente, ";
    $sql .= "      j.z01_nome   as imobiliaria, ";
    $sql .= "      j.z01_ender  as ender_imobiliaria, ";
    $sql .= "      j.z01_numcgm as z01_numimob, ";
    $sql .= "      lote.j34_totcon, ";
    $sql .= "      iptubaseregimovel.*, ";
    $sql .= "      setor.j30_descr, ";
    $sql .= "      loteloc.j06_setorloc, ";
    $sql .= "      loteloc.j06_quadraloc, ";
    $sql .= "      loteloc.j06_lote, ";
    $sql .= "      setorloc.j05_descr, ";
    $sql .= "      setorloc.j05_codigoproprio, ";
    $sql .= "      ruastipo.j88_descricao as ruadescricao, ";
    $sql .= "      iptubasecondominio.j108_condominio, ";
    $sql .= "      condominio.j107_nome, ";
    $sql .= "      predio.j111_sequencial, ";
    $sql .= "      predio.j111_nome, ";
    $sql .= "      CONCAT(tipoproprietario.j163_abreviatura || ' ' || a.z01_nome) as nomeproprietarioprincipal";
    $sql .= " from proprietario ";
    $sql .= "      inner join tipoproprietario on tipoproprietario.j163_abreviatura = proprietario.j163_abreviatura";
    $sql .= "      inner join lote              on proprietario.j01_idbql   = lote.j34_idbql ";
    $sql .= "      inner join testada";
    $sql .= "            on j34_idbql = j36_idbql";
    $sql .= "      inner join testpri";
    $sql .= "            on j49_idbql = j36_idbql";
    $sql .= "           and j49_face =  j36_face";
    $sql .= "           and j49_codigo = proprietario.j14_codigo";
    $sql .= "       left join testadanumero";
    $sql .= "            on j15_idbql = j36_idbql";
    $sql .= "           and j15_face =  j36_face";
    $sql .= "      left outer join cgm a             on proprietario.z01_numcgm  = a.z01_numcgm ";
    $sql .= "      left outer join cgm c             on j41_numcgm               = c.z01_numcgm ";
    $sql .= "      left outer join cgm j             on j44_numcgm               = j.z01_numcgm ";
    $sql .= " 	   left outer join loteloteam l      on l.j34_idbql              = proprietario.j01_idbql ";
    $sql .= " 	   left outer join loteam ll         on ll.j34_loteam            = l.j34_loteam ";
    $sql .= "      left join iptubaseregimovel on j01_matric               = j04_matric ";
    $sql .= "  	   left join zonas             on lote.j34_zona            = zonas.j50_zona ";
    $sql .= "  	   left join setor             on setor.j30_codi           = lote.j34_setor ";
    $sql .= "  	   left join loteloc           on lote.j34_idbql           = loteloc.j06_idbql ";
    $sql .= "  	   left join setorloc          on setorloc.j05_codigo      = loteloc.j06_setorloc ";
    $sql .= "      left join ruas              on proprietario.j14_codigo  = ruas.j14_codigo ";
    $sql .= "      left join ruastipo          on ruastipo.j88_codigo      = ruas.j14_tipo ";
    $sql .= "      left join iptubasecondominio on j108_matric = j01_matric ";
    $sql .= "      left join condominio on j107_sequencial = j108_condominio ";
    $sql .= "      left join iptubasepredio on j109_matric = j01_matric ";
    $sql .= "      left join predio on j111_sequencial = j109_predio ";
    $sql .= "where $sWhere ";
    $sql .= "limit 1";

    return $sql;
  }


  public function sql_query_promitentes($iMatricula, $lPrincipal = false)
  {

    $sSql = "select j41_numcgm,                            ";
    $sSql .= "       z01_nome,                              ";
    $sSql .= "       j41_tipopro as principal,              ";
    $sSql .= "       j41_promitipo                          ";
    $sSql .= "  from promitente                             ";
    $sSql .= " inner join cgm on z01_numcgm = j41_numcgm    ";
    $sSql .= "where j41_matric = {$iMatricula}              ";

    if ($lPrincipal) {
      $sSql .= " and j41_tipopro is true";
    }

    return $sSql;
  }

  public   function sql_query_proprietarios($iMatricula, $lPrincipal = false)
  {

    $sSql = "select j01_numcgm,                            ";
    $sSql .= "       z01_nome,                              ";
    $sSql .= "       true as principal                      ";
    $sSql .= "  from iptubase                               ";
    $sSql .= " inner join cgm on z01_numcgm = j01_numcgm    ";
    $sSql .= " where j01_matric = {$iMatricula}             ";

    if ($lPrincipal) {
      $sSql .= "                                              ";
      $sSql .= "   union                                      ";
      $sSql .= "                                              ";
      $sSql .= "select j42_numcgm as j01_numcgm,              ";
      $sSql .= "       z01_nome,                              ";
      $sSql .= "       false as principal                     ";
      $sSql .= "  from propri                                 ";
      $sSql .= " inner join cgm on z01_numcgm = j42_numcgm    ";
      $sSql .= "where j42_matric = {$iMatricula}              ";
    }
    return $sSql;
  }

  public function sql_query_construcoes($j01_matric = null, $campos = "*", $ordem = null, $dbwhere = "")
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

    $sql .= " from iptubase ";
    $sql .= "      inner join lote       on lote.j34_idbql      = iptubase.j01_idbql";
    $sql .= "      inner join cgm        on cgm.z01_numcgm      = iptubase.j01_numcgm";
    $sql .= "      inner join bairro     on bairro.j13_codi     = lote.j34_bairro";
    $sql .= "      inner join setor      on setor.j30_codi      = lote.j34_setor";
    $sql .= "      inner join iptuconstr on iptubase.j01_matric = iptuconstr.j39_matric";
    $sql .= "       left join iptuant    on iptubase.j01_matric = iptuant.j40_matric";
    $sql2 = "";
    if ($dbwhere == "") {

      if ($j01_matric != null) {
        $sql2 .= " where iptubase.j01_matric = $j01_matric ";
      }
    } else if ($dbwhere != "") {
      $sql2 = " where $dbwhere";
    }

    $sql .= $sql2;
    if ($ordem != null) {
      $sql .= " order by ";
      $campos_sql = explode("#", $ordem);
      $virgula    = "";
      for ($i = 0; $i < sizeof($campos_sql); $i++) {

        $sql .= $virgula . $campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }
  public function sql_queryCalculoMatricula($iMatricula, $iAnousu)
  {

    $sCampos  = "j23_anousu     , ";
    $sCampos .= "j23_matric     , ";
    $sCampos .= "j23_testad     , ";
    $sCampos .= "j23_arealo     , ";
    $sCampos .= "j23_areafr     , ";
    $sCampos .= "j23_areaed     , ";
    $sCampos .= "j23_m2terr     , ";
    $sCampos .= "j23_vlrter     , ";
    $sCampos .= "j23_aliq       , ";
    $sCampos .= "j23_vlrisen    , ";
    $sCampos .= "j23_tipoim     , ";
    $sCampos .= "j23_tipocalculo, ";
    $sCampos .= "j22_idcons     , ";
    $sCampos .= "j22_areaed     , ";
    $sCampos .= "j22_vm2        , ";
    $sCampos .= "j22_pontos     , ";
    $sCampos .= "j22_valor      , ";
    $sCampos .= "j21_receit     , ";
    $sCampos .= "j21_valor      , ";
    $sCampos .= "j21_quant      , ";
    $sCampos .= "j21_codhis     , ";
    $sCampos .= "j20_numpre       ";

    $sSql    = "select {$sCampos}                                                 ";
    $sSql   .= "  from iptucalc                                                   ";
    $sSql   .= " inner join iptucale on iptucale.j22_anousu = iptucalc.j23_anousu ";
    $sSql   .= "  								  and iptucale.j22_matric = iptucalc.j23_matric ";
    $sSql   .= " inner join iptucalv on iptucalv.j21_anousu = iptucalc.j23_anousu ";
    $sSql   .= "                    and iptucalv.j21_matric = iptucalc.j23_matric ";
    $sSql   .= " inner join iptunump on iptunump.j20_anousu = iptucalc.j23_anousu ";
    $sSql   .= "									  and iptunump.j20_matric = iptucalc.j23_matric ";
    $sSql   .= " where iptucalc.j23_anousu = {$iAnousu}				                    ";
    $sSql   .= "   and iptucalc.j23_matric = {$iMatricula}  			                ";

    return $sSql;
  }

  public function consultaDebitosMatricula($iMatric = "")
  {
    require_once(modification("libs/db_utils.php"));

    $oDaoArrematric = new cl_arrematric();

    $sCampos = " distinct cadtipo.k03_tipo, ";
    $sCampos .= "	        cadtipo.k03_descr ";

    $sWhere = " arrematric.k00_matric = {$iMatric}";

    $rsConsulta = $oDaoArrematric->sql_record($oDaoArrematric->sql_query_info(null, null, $sCampos, null, $sWhere));

    $aRetorno = db_utils::getCollectionByRecord($rsConsulta, false, false, false);

    return $aRetorno;
  }

  public function findBydId($value)
  {
    $sSqlQuery = $this->sql_query_file($value);
    $rsDados = db_query($sSqlQuery);
    if (!$rsDados) {
      throw  new \DBException('Erro ao pesquisar matrícula');
    }
    if (pg_num_rows($rsDados) > 0) {
      return pg_fetch_object($rsDados, 0);
    }

    return null;
  }

  public function sql_query_area_contruida_lote($iIdbql)
  {
    $sql = "select coalesce(sum(j39_area), 0) as area_construidalote
                from iptuconstr
          inner join iptubase on j01_matric = j39_matric
               where j01_idbql = '{$iIdbql}'
                 and j39_dtdemo is null
                 and j01_baixa is null";
    return $sql;
  }

  public function sql_query_tipos_promitentes($iMatricula)
  {
    return "select tipopromitente.*
                from tipoproprietariopromitente
              inner join tipopromitente ON tipopromitente.j164_tipopromitente = tipoproprietariopromitente.j165_tipopromitente
                where j165_tipoproprietario in 
                  (select j01_tipoproprietario from iptubase where iptubase.j01_matric = {$iMatricula})";
  }

  public function sql_query_envolvidos_matricula($iMatricula, $iNumcgm = null)
  {
    $where = "where 1 = 1";

    if (isset($iNumcgm)) {
      $where .= " and numcgm = {$iNumcgm} ";
    }

    $sql  = "select *                                                                                                              ";
    $sql .= " from                                                                                                                 ";
    $sql .= "     (                                                                                                                ";
    $sql .= "         (                                                                                                            ";
    $sql .= "             SELECT                                                                                                   ";
    $sql .= "                 z01_numcgm as numcgm,                                                                                ";
    $sql .= "                 tipoproprietario.j163_descricao as tipo,                                                             ";
    $sql .= "                 CONCAT(                                                                                              ";
    $sql .= "                     tipoproprietario.j163_descricao || ' ' || z01_nome                                               ";
    $sql .= "                 ) as nome,                                                                                           ";
    $sql .= "                 CONCAT(                                                                                              ";
    $sql .= "                     tipoproprietario.j163_abreviatura || ' ' || z01_nome                                             ";
    $sql .= "                 ) as nomecomabreviatura                                                                              ";
    $sql .= "             FROM                                                                                                     ";
    $sql .= "                 iptubase                                                                                             ";
    $sql .= "                 join protocolo.cgm ON cgm.z01_numcgm = iptubase.j01_numcgm                                           ";
    $sql .= "                 join tipoproprietario ON tipoproprietario.j163_tipoproprietario = iptubase.j01_tipoproprietario      ";
    $sql .= "             WHERE                                                                                                    ";
    $sql .= "                 iptubase.j01_matric = {$iMatricula}                                                                  ";
    $sql .= "             limit                                                                                                    ";
    $sql .= "                 1                                                                                                    ";
    $sql .= "         )                                                                                                            ";
    $sql .= "         union                                                                                                        ";
    $sql .= "         all (                                                                                                        ";
    $sql .= "             SELECT                                                                                                   ";
    $sql .= "                 z01_numcgm as numcgm,                                                                                ";
    $sql .= "                 tipoproprietario.j163_descricao as tipo,                                                             ";
    $sql .= "                 CONCAT(                                                                                              ";
    $sql .= "                     tipoproprietario.j163_descricao || ' ' || z01_nome                                               ";
    $sql .= "                 ) as nome,                                                                                           ";
    $sql .= "                 CONCAT(                                                                                              ";
    $sql .= "                     tipoproprietario.j163_abreviatura || ' ' || z01_nome                                             ";
    $sql .= "                 ) as nomecomabreviatura                                                                              ";
    $sql .= "             FROM                                                                                                     ";
    $sql .= "                 propri                                                                                               ";
    $sql .= "                 INNER JOIN cgm ON cgm.z01_numcgm = propri.j42_numcgm                                                 ";
    $sql .= "                 INNER JOIN tipoproprietario ON tipoproprietario.j163_tipoproprietario = propri.j42_tipoproprietario  ";
    $sql .= "             WHERE                                                                                                    ";
    $sql .= "                 j42_matric = {$iMatricula}                                                                           ";
    $sql .= "                 AND j42_numcgm = z01_numcgm                                                                          ";
    $sql .= "         )                                                                                                            ";
    $sql .= "         union                                                                                                        ";
    $sql .= "         all (                                                                                                        ";
    $sql .= "             SELECT                                                                                                   ";
    $sql .= "                 z01_numcgm as numcgm,                                                                                ";
    $sql .= "                 tipopromitente.j164_descricao as tipo,                                                               ";
    $sql .= "                 CONCAT(                                                                                              ";
    $sql .= "                     tipopromitente.j164_descricao || ' ' || z01_nome                                                 ";
    $sql .= "                 ) as nome,                                                                                           ";
    $sql .= "                 CONCAT(                                                                                              ";
    $sql .= "                     tipopromitente.j164_abreviatura || ' ' || z01_nome                                               ";
    $sql .= "                 ) as nomecomabreviatura                                                                              ";
    $sql .= "             FROM                                                                                                     ";
    $sql .= "                 promitente                                                                                           ";
    $sql .= "                 INNER JOIN cgm ON z01_numcgm = j41_numcgm                                                            ";
    $sql .= "                 INNER JOIN tipopromitente ON j164_promitipo = j41_promitipo                                          ";
    $sql .= "             WHERE                                                                                                    ";
    $sql .= "                 j41_matric = {$iMatricula}                                                                           ";
    $sql .= "             ORDER BY                                                                                                 ";
    $sql .= "                 j41_tipopro DESC                                                                                     ";
    $sql .= "         )                                                                                                            ";
    $sql .= "     ) as envolvidos {$where}                                                                                         ";

    return $sql;
  }

  public function sql_query_buscaOcorrenciasMatricula($matricula) {
    $sql = "
      select
          ar23_ocorrencia
      from
          histocorrenciamatric
          join histocorrencia ON histocorrencia.ar23_sequencial = histocorrenciamatric.ar25_histocorrencia
      where
          ar25_matric = $matricula and ar23_tipo = 1
    ";

    return $sql;
  }

  public function sql_query_informacoesImovel($matricula) {
    $anoSessao = db_getsession("DB_anousu");
    $sql = "with dadosimovel as ( ";
    $sql .="        select ";
    $sql .="            j01_matric as matricula, ";
    $sql .="            j01_idbql as idbql ";
    $sql .="        from ";
    $sql .="            iptubase ";
    $sql .="        where ";
    $sql .="            j01_matric = {$matricula} ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ), dadosregistroimoveis as ( ";
    $sql .="        select ";
    $sql .="            j04_matricregimo as matriculari, ";
    $sql .="            j04_quadraregimo as quadrari, ";
    $sql .="            j04_loteregimo as loteri, ";
    $sql .="            j04_setorregimovel as setorri ";
    $sql .="        from ";
    $sql .="            iptubaseregimovel ";
    $sql .="        where ";
    $sql .="            j04_matric in ( ";
    $sql .="                select ";
    $sql .="                    matricula ";
    $sql .="                from ";
    $sql .="                    dadosimovel ";
    $sql .="            ) ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ), dadoslote as ( ";
    $sql .="        select ";
    $sql .="            j34_setor as setor, ";
    $sql .="            j34_quadra as quadra, ";
    $sql .="            j34_lote as lote, ";
    $sql .="            j34_area as area ";
    $sql .="        from ";
    $sql .="            lote ";
    $sql .="        where ";
    $sql .="            j34_idbql in ( ";
    $sql .="                select ";
    $sql .="                    idbql ";
    $sql .="                from ";
    $sql .="                    dadosimovel ";
    $sql .="            ) ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ), situacao as ( ";
    $sql .="        select ";
    $sql .="            j31_descr as situacao ";
    $sql .="        from ";
    $sql .="            carconstr ";
    $sql .="            inner join caracter ON caracter.j31_codigo = carconstr.j48_caract ";
    $sql .="        where ";
    $sql .="            j48_matric in ( ";
    $sql .="                select ";
    $sql .="                    matricula ";
    $sql .="                from ";
    $sql .="                    dadosimovel ";
    $sql .="            ) ";
    $sql .="            and j31_grupo = 7200 ";
    $sql .="        order by ";
    $sql .="            j31_descr ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ), testada as ( ";
    $sql .="        select ";
    $sql .="            ruas.j14_nome as testadarua, ";
    $sql .="            ruas.j14_bairro as testadabairro, ";
    $sql .="            testadanumero.j15_numero as testadanumero, ";
    $sql .="            testadanumero.j15_compl as testadacomplemento, ";
    $sql .="            testpri.j49_face as testadatamanho ";
    $sql .="        from ";
    $sql .="            testpri ";
    $sql .="            inner join ruas ON ruas.j14_codigo = testpri.j49_codigo ";
    $sql .="            inner join testadanumero on j15_idbql = j49_idbql ";
    $sql .="        where ";
    $sql .="            j49_idbql in ( ";
    $sql .="                select ";
    $sql .="                    idbql ";
    $sql .="                from ";
    $sql .="                    dadosimovel ";
    $sql .="            ) ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ), observacao as ( ";
    $sql .="        select ";
    $sql .="            j26_obs as observacao ";
    $sql .="        from ";
    $sql .="            matricobs ";
    $sql .="        where ";
    $sql .="            j26_matric in ( ";
    $sql .="                select ";
    $sql .="                    matricula ";
    $sql .="                from ";
    $sql .="                    dadosimovel ";
    $sql .="            ) ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ), areaterreno as ( ";
    $sql .="        select ";
    $sql .="            ROUND( ";
    $sql .="                ( ";
    $sql .="                    ( ";
    $sql .="                        ROUND(rnfracao, 10) * ( ";
    $sql .="                            select ";
    $sql .="                                area ";
    $sql .="                            from ";
    $sql .="                                dadoslote ";
    $sql .="                        ) ";
    $sql .="                    ) / 100 ";
    $sql .="                ), ";
    $sql .="                2 ";
    $sql .="            ) as areaterreno ";
    $sql .="        from ";
    $sql .="            fc_iptu_fracionalote( ";
    $sql .="                ( ";
    $sql .="                    select ";
    $sql .="                        matricula ";
    $sql .="                    from ";
    $sql .="                        dadosimovel ";
    $sql .="                ) :: int, ";
    $sql .="                {$anoSessao}, ";
    $sql .="                true, ";
    $sql .="                false, ";
    $sql .="                false ";
    $sql .="            ) ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ), ";
    $sql .="    areaconstrucao as ( ";
    $sql .="        select ";
    $sql .="            (ROUND(SUM(j39_area), 2)) as areaconstrucao ";
    $sql .="        from ";
    $sql .="            iptuconstr ";
    $sql .="        where ";
    $sql .="            j39_matric in ( ";
    $sql .="                select ";
    $sql .="                    matricula ";
    $sql .="                from ";
    $sql .="                    dadosimovel ";
    $sql .="            ) ";
    $sql .="        limit ";
    $sql .="            1 ";
    $sql .="    ) ";
    $sql .="    select ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                matricula ";
    $sql .="            from ";
    $sql .="                dadosimovel ";
    $sql .="        ) as matricula, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                matriculari ";
    $sql .="            from ";
    $sql .="                dadosregistroimoveis ";
    $sql .="        ) as matriculari, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                quadrari ";
    $sql .="            from ";
    $sql .="                dadosregistroimoveis ";
    $sql .="        ) as quadrari, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                loteri ";
    $sql .="            from ";
    $sql .="                dadosregistroimoveis ";
    $sql .="        ) as loteri, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                setorri ";
    $sql .="            from ";
    $sql .="                dadosregistroimoveis ";
    $sql .="        ) as setorri, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                setor ";
    $sql .="            from ";
    $sql .="                dadoslote ";
    $sql .="        ) as setor, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                quadra ";
    $sql .="            from ";
    $sql .="                dadoslote ";
    $sql .="        ) as quadra, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                lote ";
    $sql .="            from ";
    $sql .="                dadoslote ";
    $sql .="        ) as lote, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                situacao ";
    $sql .="            from ";
    $sql .="                situacao ";
    $sql .="        ) as situacao, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                testadarua ";
    $sql .="            from ";
    $sql .="                testada ";
    $sql .="        ) as testadarua, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                testadatamanho ";
    $sql .="            from ";
    $sql .="                testada ";
    $sql .="        ) as testadatamanho, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                testadabairro ";
    $sql .="            from ";
    $sql .="                testada ";
    $sql .="        ) as testadabairro, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                testadanumero ";
    $sql .="            from ";
    $sql .="                testada ";
    $sql .="        ) as testadanumero, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                testadacomplemento ";
    $sql .="            from ";
    $sql .="                testada ";
    $sql .="        ) as testadacomplemento, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                observacao ";
    $sql .="            from ";
    $sql .="                observacao ";
    $sql .="        ) as observacao, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                areaterreno ";
    $sql .="            from ";
    $sql .="                areaterreno ";
    $sql .="        ) as areaterreno, ";
    $sql .="        ( ";
    $sql .="            select ";
    $sql .="                areaconstrucao ";
    $sql .="            from ";
    $sql .="                areaconstrucao ";
    $sql .="        ) as areaconstrucao ";

      return $sql;
  }

  public function sql_query_buscaConstrucoesMatricula($matricula, $ano) {
    $sql = "
      select
          coalesce(j22_valor, 0) as valorconstrucao,
          coalesce(j39_area, 0) as areaconstrucao,
          j31_descr as situacaoconstrucao,
          *
      from
          iptucale
          inner join iptuconstr on j39_idcons = j22_idcons
          and j39_matric = j22_matric
          join carconstr on j48_matric = j39_matric
          and j48_idcons = j39_idcons
          inner join caracter ON caracter.j31_codigo = carconstr.j48_caract
      where
          j22_matric = $matricula
          and j31_grupo = 7200
          and j22_anousu = $ano
          and j39_dtdemo is null
    ";

    return $sql;
  }

  public function sql_query_construcoesMatricula($matricula, $codigosAreaIrregular = [])
  {
    $sqlBuscaAreaIrregular = "true";

    if (count($codigosAreaIrregular) > 0) {
      $codigosCaracteristicas = implode(',',$codigosAreaIrregular);
      $sqlBuscaAreaIrregular = "(
        (
            select
                count(j48_caract)
            from
                carconstr
                inner join caracter ON caracter.j31_codigo = carconstr.j48_caract
            where
                carconstr.j48_idcons = iptuconstr.j39_idcons
                and carconstr.j48_matric = iptuconstr.j39_matric
                and j48_caract in ($codigosCaracteristicas)
          ) = 0
        )";
    }

    $sql = "
    select
        $sqlBuscaAreaIrregular as regular,
        iptubase.j01_matric,
        iptuconstr.*
    from
        iptubase
        inner join iptuconstr ON iptuconstr.j39_matric = iptubase.j01_matric
    where
        iptubase.j01_baixa is null
        and iptuconstr.j39_dtdemo is null
        and iptubase.j01_matric = $matricula";
  
    return $sql;
  }
}
