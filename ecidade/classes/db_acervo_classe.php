<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2018  DBSeller Servicos de Informatica
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

class cl_acervo
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
    public $bi06_seq = 0;
    public $bi06_biblioteca = 0;
    public $bi06_dataregistro_dia = null;
    public $bi06_dataregistro_mes = null;
    public $bi06_dataregistro_ano = null;
    public $bi06_dataregistro = null;
    public $bi06_edicao = null;
    public $bi06_titulo = null;
    public $bi06_classcdd = null;
    public $bi06_tipoitem = 0;
    public $bi06_editora = 0;
    public $bi06_classiliteraria = 0;
    public $bi06_anoedicao = "null";
    public $bi06_colecaoacervo = "null";
    public $bi06_cutter = null;
    public $bi06_idioma = 0;
    public $bi06_subtitulo = null;
    public $bi06_titulooriginal = null;
    public $bi06_numeroitem = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 bi06_seq = int8 = Código do Acervo
                 bi06_biblioteca = int8 = Biblioteca
                 bi06_dataregistro = date = Data de Registro
                 bi06_edicao = char(10) = Edição
                 bi06_titulo = char(100) = Título
                 bi06_classcdd = char(30) = Classificação C.D.D
                 bi06_tipoitem = int8 = Tipo do Item
                 bi06_editora = int8 = Editora
                 bi06_classiliteraria = int8 = Classificação Literária
                 bi06_anoedicao = int4 = Ano da Edição
                 bi06_colecaoacervo = int4 = Sequencial
                 bi06_cutter = varchar(30) = Código Cutter
                 bi06_idioma = int4 = Idioma
                 bi06_subtitulo = varchar(100) = Subtítulo
                 bi06_titulooriginal = varchar(100) = Título Original
                 bi06_numeroitem = int4 = Número do Item
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("acervo");
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
            $this->bi06_seq = ($this->bi06_seq == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_seq"]:$this->bi06_seq);
            $this->bi06_biblioteca = ($this->bi06_biblioteca == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_biblioteca"]:$this->bi06_biblioteca);
            if ($this->bi06_dataregistro == "") {
                $this->bi06_dataregistro_dia = ($this->bi06_dataregistro_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_dataregistro_dia"]:$this->bi06_dataregistro_dia);
                $this->bi06_dataregistro_mes = ($this->bi06_dataregistro_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_dataregistro_mes"]:$this->bi06_dataregistro_mes);
                $this->bi06_dataregistro_ano = ($this->bi06_dataregistro_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_dataregistro_ano"]:$this->bi06_dataregistro_ano);
                if ($this->bi06_dataregistro_dia != "") {
                    $this->bi06_dataregistro = $this->bi06_dataregistro_ano."-".$this->bi06_dataregistro_mes."-".$this->bi06_dataregistro_dia;
                }
            }
            $this->bi06_edicao = ($this->bi06_edicao == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_edicao"]:$this->bi06_edicao);
            $this->bi06_titulo = ($this->bi06_titulo == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_titulo"]:$this->bi06_titulo);
            $this->bi06_classcdd = ($this->bi06_classcdd == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_classcdd"]:$this->bi06_classcdd);
            $this->bi06_tipoitem = ($this->bi06_tipoitem == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_tipoitem"]:$this->bi06_tipoitem);
            $this->bi06_editora = ($this->bi06_editora == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_editora"]:$this->bi06_editora);
            $this->bi06_classiliteraria = ($this->bi06_classiliteraria == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_classiliteraria"]:$this->bi06_classiliteraria);
            $this->bi06_anoedicao = ($this->bi06_anoedicao == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_anoedicao"]:$this->bi06_anoedicao);
            $this->bi06_colecaoacervo = ($this->bi06_colecaoacervo == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_colecaoacervo"]:$this->bi06_colecaoacervo);
            $this->bi06_cutter = ($this->bi06_cutter == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_cutter"]:$this->bi06_cutter);
            $this->bi06_idioma = ($this->bi06_idioma == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_idioma"]:$this->bi06_idioma);
            $this->bi06_subtitulo = ($this->bi06_subtitulo == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_subtitulo"]:$this->bi06_subtitulo);
            $this->bi06_titulooriginal = ($this->bi06_titulooriginal == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_titulooriginal"]:$this->bi06_titulooriginal);
            $this->bi06_numeroitem = ($this->bi06_numeroitem == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_numeroitem"]:$this->bi06_numeroitem);
        } else {
            $this->bi06_seq = ($this->bi06_seq == ""?@$GLOBALS["HTTP_POST_VARS"]["bi06_seq"]:$this->bi06_seq);
        }
    }

    public function incluir($bi06_seq)
    {
        $this->atualizacampos();
        if ($this->bi06_biblioteca == null) {
            $this->erro_sql = " Campo Biblioteca não informado.";
            $this->erro_campo = "bi06_biblioteca";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->bi06_dataregistro == null) {
            $this->erro_sql = " Campo Data de Registro não informado.";
            $this->erro_campo = "bi06_dataregistro_dia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->bi06_edicao == null) {
            $this->bi06_edicao = "0";
        }
        if ($this->bi06_titulo == null) {
            $this->erro_sql = " Campo Título não informado.";
            $this->erro_campo = "bi06_titulo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->bi06_tipoitem == null) {
            $this->erro_sql = " Campo Tipo do Item não informado.";
            $this->erro_campo = "bi06_tipoitem";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->bi06_editora == null) {
            $this->erro_sql = " Campo Editora não informado.";
            $this->erro_campo = "bi06_editora";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->bi06_classiliteraria == null) {
            $this->erro_sql = " Campo Classificação Literária não informado.";
            $this->erro_campo = "bi06_classiliteraria";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->bi06_anoedicao == null) {
            $this->bi06_anoedicao = "0";
        }
        if ($this->bi06_colecaoacervo == null) {
            $this->bi06_colecaoacervo = "null";
        }
        if ($this->bi06_idioma == null) {
            $this->erro_sql = " Campo Idioma não informado.";
            $this->erro_campo = "bi06_idioma";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->bi06_numeroitem == null) {
            $this->bi06_numeroitem = "0";
        }
        if ($bi06_seq == "" || $bi06_seq == null) {
            $result = db_query("select nextval('acervo_bi06_seq_seq')");
            if ($result==false) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: acervo_bi06_seq_seq do campo: bi06_seq";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->bi06_seq = pg_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from acervo_bi06_seq_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $bi06_seq)) {
                $this->erro_sql = " Campo bi06_seq maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->bi06_seq = $bi06_seq;
            }
        }
        if (($this->bi06_seq == null) || ($this->bi06_seq == "")) {
            $this->erro_sql = " Campo bi06_seq não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into acervo(
                                       bi06_seq
                                      ,bi06_biblioteca
                                      ,bi06_dataregistro
                                      ,bi06_edicao
                                      ,bi06_titulo
                                      ,bi06_classcdd
                                      ,bi06_tipoitem
                                      ,bi06_editora
                                      ,bi06_classiliteraria
                                      ,bi06_anoedicao
                                      ,bi06_colecaoacervo
                                      ,bi06_cutter
                                      ,bi06_idioma
                                      ,bi06_subtitulo
                                      ,bi06_titulooriginal
                                      ,bi06_numeroitem
                       )
                values (
                                $this->bi06_seq
                               ,$this->bi06_biblioteca
                               ,".($this->bi06_dataregistro == "null" || $this->bi06_dataregistro == ""?"null":"'".$this->bi06_dataregistro."'")."
                               ,'$this->bi06_edicao'
                               ,'$this->bi06_titulo'
                               ,'$this->bi06_classcdd'
                               ,$this->bi06_tipoitem
                               ,$this->bi06_editora
                               ,$this->bi06_classiliteraria
                               ,$this->bi06_anoedicao
                               ,$this->bi06_colecaoacervo
                               ,'$this->bi06_cutter'
                               ,$this->bi06_idioma
                               ,'$this->bi06_subtitulo'
                               ,'$this->bi06_titulooriginal'
                               ,$this->bi06_numeroitem
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Acervo ($this->bi06_seq) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Acervo já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Acervo ($this->bi06_seq) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->bi06_seq;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($bi06_seq = null)
    {
        $this->atualizacampos();
        $sql = " update acervo set ";
        $virgula = "";
        if (trim($this->bi06_seq)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_seq"])) {
            $sql  .= $virgula." bi06_seq = $this->bi06_seq ";
            $virgula = ",";
            if (trim($this->bi06_seq) == null) {
                $this->erro_sql = " Campo Código do Acervo não informado.";
                $this->erro_campo = "bi06_seq";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->bi06_biblioteca)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_biblioteca"])) {
            $sql  .= $virgula." bi06_biblioteca = $this->bi06_biblioteca ";
            $virgula = ",";
            if (trim($this->bi06_biblioteca) == null) {
                $this->erro_sql = " Campo Biblioteca não informado.";
                $this->erro_campo = "bi06_biblioteca";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->bi06_dataregistro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_dataregistro_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["bi06_dataregistro_dia"] !="")) {
            $sql  .= $virgula." bi06_dataregistro = '$this->bi06_dataregistro' ";
            $virgula = ",";
            if (trim($this->bi06_dataregistro) == null) {
                $this->erro_sql = " Campo Data de Registro não informado.";
                $this->erro_campo = "bi06_dataregistro_dia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["bi06_dataregistro_dia"])) {
                $sql  .= $virgula." bi06_dataregistro = null ";
                $virgula = ",";
                if (trim($this->bi06_dataregistro) == null) {
                      $this->erro_sql = " Campo Data de Registro não informado.";
                      $this->erro_campo = "bi06_dataregistro_dia";
                      $this->erro_banco = "";
                      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                      $this->erro_status = "0";
                      return false;
                }
            }
        }
        if (trim($this->bi06_edicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_edicao"])) {
            $sql  .= $virgula." bi06_edicao = '$this->bi06_edicao' ";
            $virgula = ",";
        }
        if (trim($this->bi06_titulo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_titulo"])) {
            $sql  .= $virgula." bi06_titulo = '$this->bi06_titulo' ";
            $virgula = ",";
            if (trim($this->bi06_titulo) == null) {
                $this->erro_sql = " Campo Título não informado.";
                $this->erro_campo = "bi06_titulo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->bi06_classcdd)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_classcdd"])) {
            $sql  .= $virgula." bi06_classcdd = '$this->bi06_classcdd' ";
            $virgula = ",";
        }
        if (trim($this->bi06_tipoitem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_tipoitem"])) {
            $sql  .= $virgula." bi06_tipoitem = $this->bi06_tipoitem ";
            $virgula = ",";
            if (trim($this->bi06_tipoitem) == null) {
                $this->erro_sql = " Campo Tipo do Item não informado.";
                $this->erro_campo = "bi06_tipoitem";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->bi06_editora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_editora"])) {
            $sql  .= $virgula." bi06_editora = $this->bi06_editora ";
            $virgula = ",";
            if (trim($this->bi06_editora) == null) {
                $this->erro_sql = " Campo Editora não informado.";
                $this->erro_campo = "bi06_editora";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->bi06_classiliteraria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_classiliteraria"])) {
            $sql  .= $virgula." bi06_classiliteraria = $this->bi06_classiliteraria ";
            $virgula = ",";
            if (trim($this->bi06_classiliteraria) == null) {
                $this->erro_sql = " Campo Classificação Literária não informado.";
                $this->erro_campo = "bi06_classiliteraria";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->bi06_anoedicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_anoedicao"])) {
            if (trim($this->bi06_anoedicao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["bi06_anoedicao"])) {
                $this->bi06_anoedicao = "0" ;
            }
            $sql  .= $virgula." bi06_anoedicao = $this->bi06_anoedicao ";
            $virgula = ",";
        }
        if (trim($this->bi06_colecaoacervo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_colecaoacervo"])) {
            if (trim($this->bi06_colecaoacervo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["bi06_colecaoacervo"])) {
                $this->bi06_colecaoacervo = "0" ;
            }
            $sql  .= $virgula." bi06_colecaoacervo = $this->bi06_colecaoacervo ";
            $virgula = ",";
        }
        if (trim($this->bi06_cutter)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_cutter"])) {
            $sql  .= $virgula." bi06_cutter = '$this->bi06_cutter' ";
            $virgula = ",";
        }
        if (trim($this->bi06_idioma)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_idioma"])) {
            $sql  .= $virgula." bi06_idioma = $this->bi06_idioma ";
            $virgula = ",";
            if (trim($this->bi06_idioma) == null) {
                $this->erro_sql = " Campo Idioma não informado.";
                $this->erro_campo = "bi06_idioma";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->bi06_subtitulo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_subtitulo"])) {
            $sql  .= $virgula." bi06_subtitulo = '$this->bi06_subtitulo' ";
            $virgula = ",";
        }
        if (trim($this->bi06_titulooriginal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_titulooriginal"])) {
            $sql  .= $virgula." bi06_titulooriginal = '$this->bi06_titulooriginal' ";
            $virgula = ",";
        }
        if (trim($this->bi06_numeroitem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bi06_numeroitem"])) {
            if (trim($this->bi06_numeroitem)=="" && isset($GLOBALS["HTTP_POST_VARS"]["bi06_numeroitem"])) {
                $this->bi06_numeroitem = "0" ;
            }
            $sql  .= $virgula." bi06_numeroitem = $this->bi06_numeroitem ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($bi06_seq!=null) {
            $sql .= " bi06_seq = $this->bi06_seq";
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Acervo não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->bi06_seq;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Acervo não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->bi06_seq;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->bi06_seq;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($bi06_seq = null, $dbwhere = null)
    {
        $sql = " delete from acervo
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($bi06_seq)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " bi06_seq = $bi06_seq ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Acervo não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$bi06_seq;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Acervo não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$bi06_seq;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$bi06_seq;
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
            $this->erro_sql   = "Record Vazio na Tabela:acervo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($bi06_seq = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql  = "select {$campos}";
        $sql .= "  from acervo ";
        $sql .= "      left join exemplar ON exemplar.bi23_acervo = acervo.bi06_seq";
        $sql .= "      left  join colecaoacervo  on  colecaoacervo.bi29_sequencial = acervo.bi06_colecaoacervo";
        $sql .= "      inner join idioma  on  idioma.bi22_sequencial = acervo.bi06_idioma";
        $sql .= "      inner join editora  on  editora.bi02_codigo = acervo.bi06_editora";
        $sql .= "      inner join classiliteraria  on  classiliteraria.bi03_codigo = acervo.bi06_classiliteraria";
        $sql .= "      inner join tipoitem  on  tipoitem.bi05_codigo = acervo.bi06_tipoitem";
        $sql .= "      inner join biblioteca  on  biblioteca.bi17_codigo = acervo.bi06_biblioteca";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = biblioteca.bi17_coddepto";
        $sql .= "      left join localacervo  on  localacervo.bi20_acervo = acervo.bi06_seq";
        $sql .= "      left join localizacao  on  localizacao.bi09_codigo = localacervo.bi20_localizacao";
        $sql .= "      left join aquisicao on aquisicao.bi04_codigo = exemplar.bi23_aquisicao";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($bi06_seq)) {
                $sql2 .= " where acervo.bi06_seq = $bi06_seq ";
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

    public function sql_query_file($bi06_seq = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from acervo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($bi06_seq)) {
                $sql2 .= " where acervo.bi06_seq = $bi06_seq ";
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

    function sql_query_autores($bi06_seq = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = split("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from acervo ";
        $sql .= "      inner join editora  on  editora.bi02_codigo = acervo.bi06_editora";
        $sql .= "      inner join classiliteraria  on  classiliteraria.bi03_codigo = acervo.bi06_classiliteraria";
        $sql .= "      inner join tipoitem  on  tipoitem.bi05_codigo = acervo.bi06_tipoitem";
        $sql .= "      inner join biblioteca  on  biblioteca.bi17_codigo = acervo.bi06_biblioteca";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = biblioteca.bi17_coddepto";
        $sql .= "      inner join autoracervo  on  autoracervo.bi21_acervo = acervo.bi06_seq";
        $sql .= "      left  join colecaoacervo  on  colecaoacervo.bi29_sequencial = acervo.bi06_colecaoacervo";
        $sql .= "      left join localacervo  on  localacervo.bi20_acervo = acervo.bi06_seq";
        $sql .= "      left join localizacao  on  localizacao.bi09_codigo = localacervo.bi20_localizacao";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($bi06_seq!=null) {
                $sql2 .= " where acervo.bi06_seq = $bi06_seq ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = split("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
}
