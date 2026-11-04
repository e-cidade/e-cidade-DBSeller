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

class cl_orcprojativ
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
    public $o55_anousu = 0;
    public $o55_tipo = 0;
    public $o55_projativ = 0;
    public $o55_descr = null;
    public $o55_finali = null;
    public $o55_instit = 0;
    public $o55_descrunidade = null;
    public $o55_valorunidade = 0;
    public $o55_especproduto = null;
    public $o55_tipoacao = 0;
    public $o55_formaimplementacao = 0;
    public $o55_detalhamentoimp = null;
    public $o55_origemacao = null;
    public $o55_baselegal = null;
    public $o55_orcproduto = 0;
    public $o55_datacriacao_dia = null;
    public $o55_datacriacao_mes = null;
    public $o55_datacriacao_ano = null;
    public $o55_datacriacao = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 o55_anousu = int4 = Exercício 
                 o55_tipo = int4 = Tipo 
                 o55_projativ = int4 = Projetos / Atividades 
                 o55_descr = varchar(80) = Descrição 
                 o55_finali = text = Finalidade 
                 o55_instit = int4 = Código da Instituição 
                 o55_descrunidade = text = Unidade de Medida 
                 o55_valorunidade = float4 = Unidade de Medida ( Valor ) 
                 o55_especproduto = text = Especificação do Produto 
                 o55_tipoacao = int4 = Tipo de Ação 
                 o55_formaimplementacao = int4 = Forma de Implementação 
                 o55_detalhamentoimp = text = Detalhamento da Implementação 
                 o55_origemacao = text = Origem da Ação 
                 o55_baselegal = text = Base Legal 
                 o55_orcproduto = int4 = Produto 
                 o55_datacriacao = date = Data Criação
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("orcprojativ");
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
            $this->o55_anousu = ($this->o55_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_anousu"]:$this->o55_anousu);
            $this->o55_tipo = ($this->o55_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_tipo"]:$this->o55_tipo);
            $this->o55_projativ = ($this->o55_projativ == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_projativ"]:$this->o55_projativ);
            $this->o55_descr = ($this->o55_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_descr"]:$this->o55_descr);
            $this->o55_finali = ($this->o55_finali == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_finali"]:$this->o55_finali);
            $this->o55_instit = ($this->o55_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_instit"]:$this->o55_instit);
            $this->o55_descrunidade = ($this->o55_descrunidade == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_descrunidade"]:$this->o55_descrunidade);
            $this->o55_valorunidade = ($this->o55_valorunidade == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_valorunidade"]:$this->o55_valorunidade);
            $this->o55_especproduto = ($this->o55_especproduto == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_especproduto"]:$this->o55_especproduto);
            $this->o55_tipoacao = ($this->o55_tipoacao == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_tipoacao"]:$this->o55_tipoacao);
            $this->o55_formaimplementacao = ($this->o55_formaimplementacao == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_formaimplementacao"]:$this->o55_formaimplementacao);
            $this->o55_detalhamentoimp = ($this->o55_detalhamentoimp == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_detalhamentoimp"]:$this->o55_detalhamentoimp);
            $this->o55_origemacao = ($this->o55_origemacao == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_origemacao"]:$this->o55_origemacao);
            $this->o55_baselegal = ($this->o55_baselegal == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_baselegal"]:$this->o55_baselegal);
            $this->o55_orcproduto = ($this->o55_orcproduto == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_orcproduto"]:$this->o55_orcproduto);
            if ($this->o55_datacriacao == "") {
                $this->o55_datacriacao_dia = ($this->o55_datacriacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_datacriacao_dia"]:$this->o55_datacriacao_dia);
                $this->o55_datacriacao_mes = ($this->o55_datacriacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_datacriacao_mes"]:$this->o55_datacriacao_mes);
                $this->o55_datacriacao_ano = ($this->o55_datacriacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_datacriacao_ano"]:$this->o55_datacriacao_ano);
                if ($this->o55_datacriacao_dia != "") {
                    $this->o55_datacriacao = $this->o55_datacriacao_ano."-".$this->o55_datacriacao_mes."-".$this->o55_datacriacao_dia;
                }
            }
        } else {
            $this->o55_anousu = ($this->o55_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_anousu"]:$this->o55_anousu);
            $this->o55_projativ = ($this->o55_projativ == ""?@$GLOBALS["HTTP_POST_VARS"]["o55_projativ"]:$this->o55_projativ);
        }
    }

    public function incluir($o55_anousu, $o55_projativ)
    {
        $this->atualizacampos();
        if ($this->o55_tipo == null) {
            $this->erro_sql = " Campo Tipo nao Informado.";
            $this->erro_campo = "o55_tipo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o55_descr == null) {
            $this->erro_sql = " Campo Descrição nao Informado.";
            $this->erro_campo = "o55_descr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o55_instit == null) {
            $this->erro_sql = " Campo Código da Instituição não informado.";
            $this->erro_campo = "o55_instit";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->o55_valorunidade == null) {
            $this->o55_valorunidade = "0";
        }
        if (!isNiteroi() && $this->o55_tipoacao == null) {
            $this->erro_sql = " Campo Tipo de Ação nao Informado.";
            $this->erro_campo = "o55_tipoacao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (!isNiteroi() &&  $this->o55_formaimplementacao == null) {
            $this->erro_sql = " Campo Forma de Implementação nao Informado.";
            $this->erro_campo = "o55_formaimplementacao";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (!isNiteroi() &&  $this->o55_orcproduto == null) {
            $this->erro_sql = " Campo Produto nao Informado.";
            $this->erro_campo = "o55_orcproduto";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (isNiteroi()) {
            $this->o55_tipoacao = 0;
            $this->o55_formaimplementacao = 0;
            $this->o55_orcproduto = 0;
        }
        $this->o55_anousu = $o55_anousu;
        $this->o55_projativ = $o55_projativ;
        if (($this->o55_anousu == null) || ($this->o55_anousu == "")) {
            $this->erro_sql = " Campo o55_anousu não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if (($this->o55_projativ == null) || ($this->o55_projativ == "")) {
            $this->erro_sql = " Campo o55_projativ não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into orcprojativ(
                                       o55_anousu 
                                      ,o55_tipo 
                                      ,o55_projativ 
                                      ,o55_descr 
                                      ,o55_finali 
                                      ,o55_instit 
                                      ,o55_descrunidade 
                                      ,o55_valorunidade 
                                      ,o55_especproduto 
                                      ,o55_tipoacao 
                                      ,o55_formaimplementacao 
                                      ,o55_detalhamentoimp 
                                      ,o55_origemacao 
                                      ,o55_baselegal 
                                      ,o55_orcproduto 
                                      ,o55_datacriacao 
                       )
                values (
                                $this->o55_anousu 
                               ,$this->o55_tipo 
                               ,$this->o55_projativ 
                               ,'$this->o55_descr' 
                               ,'$this->o55_finali' 
                               ,$this->o55_instit 
                               ,'$this->o55_descrunidade' 
                               ,$this->o55_valorunidade 
                               ,'$this->o55_especproduto' 
                               ,$this->o55_tipoacao 
                               ,$this->o55_formaimplementacao 
                               ,'$this->o55_detalhamentoimp' 
                               ,'$this->o55_origemacao' 
                               ,'$this->o55_baselegal' 
                               ,$this->o55_orcproduto 
                               ,".($this->o55_datacriacao == "null" || $this->o55_datacriacao == ""?"null":"'".$this->o55_datacriacao."'")." 
                      )";
        $result = db_query($sql);
        if ($result==false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "Projetos / Atividades ($this->o55_anousu."-".$this->o55_projativ) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Projetos / Atividades já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "Projetos / Atividades ($this->o55_anousu."-".$this->o55_projativ) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->o55_anousu."-".$this->o55_projativ;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->o55_anousu, $this->o55_projativ));
            if (($resaco!=false)||($this->numrows!=0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,5266,'$this->o55_anousu','I')");
                $resac = db_query("insert into db_acountkey values($acount,5268,'$this->o55_projativ','I')");
                $resac = db_query("insert into db_acount values($acount,754,5266,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,5267,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,5268,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_projativ'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,5269,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,5270,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_finali'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,5271,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13672,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_descrunidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13673,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_valorunidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13674,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_especproduto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13675,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_tipoacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13676,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_formaimplementacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13677,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_detalhamentoimp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13678,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_origemacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13679,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_baselegal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,13682,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_orcproduto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,754,176461376,'','".AddSlashes(pg_fetch_result($resaco, 0, 'o55_datacriacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($o55_anousu = null, $o55_projativ = null)
    {
        $this->atualizacampos();
        $sql = " update orcprojativ set ";
        $virgula = "";
        if (trim($this->o55_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_anousu"])) {
            $sql  .= $virgula." o55_anousu = $this->o55_anousu ";
            $virgula = ",";
            if (trim($this->o55_anousu) == null) {
                $this->erro_sql = " Campo Exercício não informado.";
                $this->erro_campo = "o55_anousu";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o55_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_tipo"])) {
            $sql  .= $virgula." o55_tipo = $this->o55_tipo ";
            $virgula = ",";
            if (trim($this->o55_tipo) == null) {
                $this->erro_sql = " Campo Tipo nao Informado.";
                $this->erro_campo = "o55_tipo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o55_projativ)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_projativ"])) {
            $sql  .= $virgula." o55_projativ = $this->o55_projativ ";
            $virgula = ",";
            if (trim($this->o55_projativ) == null) {
                $this->erro_sql = " Campo Projetos / Atividades não informado.";
                $this->erro_campo = "o55_projativ";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o55_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_descr"])) {
            $sql  .= $virgula." o55_descr = '$this->o55_descr' ";
            $virgula = ",";
            if (trim($this->o55_descr) == null) {
                $this->erro_sql = " Campo Descrição nao Informado.";
                $this->erro_campo = "o55_descr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o55_finali)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_finali"])) {
            $sql  .= $virgula." o55_finali = '$this->o55_finali' ";
            $virgula = ",";
        }
        if (trim($this->o55_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_instit"])) {
            $sql  .= $virgula." o55_instit = $this->o55_instit ";
            $virgula = ",";
            if (trim($this->o55_instit) == null) {
                $this->erro_sql = " Campo Código da Instituição não informado.";
                $this->erro_campo = "o55_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o55_descrunidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_descrunidade"])) {
            $sql  .= $virgula." o55_descrunidade = '$this->o55_descrunidade' ";
            $virgula = ",";
        }
        if (trim($this->o55_valorunidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_valorunidade"])) {
            if (trim($this->o55_valorunidade)=="" && isset($GLOBALS["HTTP_POST_VARS"]["o55_valorunidade"])) {
                $this->o55_valorunidade = "0" ;
            }
            $sql  .= $virgula." o55_valorunidade = $this->o55_valorunidade ";
            $virgula = ",";
        }
        if (trim($this->o55_especproduto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_especproduto"])) {
            $sql  .= $virgula." o55_especproduto = '$this->o55_especproduto' ";
            $virgula = ",";
        }
        if (!isNiteroi() && trim($this->o55_tipoacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_tipoacao"])) {
            $sql  .= $virgula." o55_tipoacao = $this->o55_tipoacao ";
            $virgula = ",";
            if (trim($this->o55_tipoacao) == null) {
                $this->erro_sql = " Campo Tipo de Ação nao Informado.";
                $this->erro_campo = "o55_tipoacao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (!isNiteroi() && trim($this->o55_formaimplementacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_formaimplementacao"])) {
            $sql  .= $virgula." o55_formaimplementacao = $this->o55_formaimplementacao ";
            $virgula = ",";
            if (trim($this->o55_formaimplementacao) == null) {
                $this->erro_sql = " Campo Forma de Implementação nao Informado.";
                $this->erro_campo = "o55_formaimplementacao";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o55_detalhamentoimp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_detalhamentoimp"])) {
            $sql  .= $virgula." o55_detalhamentoimp = '$this->o55_detalhamentoimp' ";
            $virgula = ",";
        }
        if (trim($this->o55_origemacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_origemacao"])) {
            $sql  .= $virgula." o55_origemacao = '$this->o55_origemacao' ";
            $virgula = ",";
        }
        if (trim($this->o55_baselegal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_baselegal"])) {
            $sql  .= $virgula." o55_baselegal = '$this->o55_baselegal' ";
            $virgula = ",";
        }
        if (!isNiteroi() && trim($this->o55_orcproduto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_orcproduto"])) {
            $sql  .= $virgula." o55_orcproduto = $this->o55_orcproduto ";
            $virgula = ",";
            if (trim($this->o55_orcproduto) == null) {
                $this->erro_sql = " Campo Produto não informado.";
                $this->erro_campo = "o55_orcproduto";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->o55_datacriacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["o55_datacriacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["o55_datacriacao_dia"] !="")) {
            $sql  .= $virgula." o55_datacriacao = '$this->o55_datacriacao' ";
            $virgula = ",";
        } else {
            if (isset($GLOBALS["HTTP_POST_VARS"]["o55_datacriacao_dia"])) {
                $sql  .= $virgula." o55_datacriacao = null ";
                $virgula = ",";
            }
        }
        $sql .= " where ";
        if ($o55_anousu!=null) {
            $sql .= " o55_anousu = $this->o55_anousu";
        }
        if ($o55_projativ!=null) {
            $sql .= " and  o55_projativ = $this->o55_projativ";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->o55_anousu, $this->o55_projativ));
            if ($this->numrows > 0) {
                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {
                      $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                      $acount = pg_fetch_result($resac, 0, 0);
                      $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                      $resac = db_query("insert into db_acountkey values($acount,5266,'$this->o55_anousu','A')");
                      $resac = db_query("insert into db_acountkey values($acount,5268,'$this->o55_projativ','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_anousu"]) || $this->o55_anousu != "") {
                        $resac = db_query("insert into db_acount values($acount,754,5266,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_anousu'))."','$this->o55_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_tipo"]) || $this->o55_tipo != "") {
                        $resac = db_query("insert into db_acount values($acount,754,5267,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_tipo'))."','$this->o55_tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_projativ"]) || $this->o55_projativ != "") {
                        $resac = db_query("insert into db_acount values($acount,754,5268,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_projativ'))."','$this->o55_projativ',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_descr"]) || $this->o55_descr != "") {
                        $resac = db_query("insert into db_acount values($acount,754,5269,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_descr'))."','$this->o55_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_finali"]) || $this->o55_finali != "") {
                        $resac = db_query("insert into db_acount values($acount,754,5270,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_finali'))."','$this->o55_finali',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_instit"]) || $this->o55_instit != "") {
                        $resac = db_query("insert into db_acount values($acount,754,5271,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_instit'))."','$this->o55_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_descrunidade"]) || $this->o55_descrunidade != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13672,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_descrunidade'))."','$this->o55_descrunidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_valorunidade"]) || $this->o55_valorunidade != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13673,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_valorunidade'))."','$this->o55_valorunidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_especproduto"]) || $this->o55_especproduto != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13674,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_especproduto'))."','$this->o55_especproduto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_tipoacao"]) || $this->o55_tipoacao != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13675,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_tipoacao'))."','$this->o55_tipoacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_formaimplementacao"]) || $this->o55_formaimplementacao != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13676,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_formaimplementacao'))."','$this->o55_formaimplementacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_detalhamentoimp"]) || $this->o55_detalhamentoimp != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13677,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_detalhamentoimp'))."','$this->o55_detalhamentoimp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_origemacao"]) || $this->o55_origemacao != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13678,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_origemacao'))."','$this->o55_origemacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_baselegal"]) || $this->o55_baselegal != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13679,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_baselegal'))."','$this->o55_baselegal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_orcproduto"]) || $this->o55_orcproduto != "") {
                        $resac = db_query("insert into db_acount values($acount,754,13682,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_orcproduto'))."','$this->o55_orcproduto',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["o55_datacriacao"]) || $this->o55_datacriacao != "") {
                        $resac = db_query("insert into db_acount values($acount,754,176461376,'".AddSlashes(pg_fetch_result($resaco, $conresaco, 'o55_datacriacao'))."','$this->o55_datacriacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Projetos / Atividades não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->o55_anousu."-".$this->o55_projativ;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Projetos / Atividades não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->o55_anousu."-".$this->o55_projativ;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->o55_anousu."-".$this->o55_projativ;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($o55_anousu = null, $o55_projativ = null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
        && ($lSessaoDesativarAccount === false))) {
            if (empty($dbwhere)) {
                $resaco = $this->sql_record($this->sql_query_file($o55_anousu, $o55_projativ));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, null, "*", null, $dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,5266,'$o55_anousu','E')");
                    $resac  = db_query("insert into db_acountkey values($acount,5268,'$o55_projativ','E')");
                    $resac  = db_query("insert into db_acount values($acount,754,5266,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,5267,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,5268,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_projativ'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,5269,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,5270,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_finali'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,5271,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13672,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_descrunidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13673,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_valorunidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13674,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_especproduto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13675,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_tipoacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13676,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_formaimplementacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13677,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_detalhamentoimp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13678,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_origemacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13679,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_baselegal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,13682,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_orcproduto'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,754,176461376,'','".AddSlashes(pg_fetch_result($resaco, $iresaco, 'o55_datacriacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from orcprojativ
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o55_anousu)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " o55_anousu = $o55_anousu ";
            }
            if (!empty($o55_projativ)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " o55_projativ = $o55_projativ ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Projetos / Atividades não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$o55_anousu."-".$o55_projativ;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Projetos / Atividades não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$o55_anousu."-".$o55_projativ;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$o55_anousu."-".$o55_projativ;
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
            $this->erro_sql   = "Record Vazio na Tabela:orcprojativ";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($o55_anousu = null, $o55_projativ = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from orcprojativ ";
        $sql .= "      inner join db_config  on  db_config.codigo = orcprojativ.o55_instit";
        $sql .= "      inner join orcproduto  on  orcproduto.o22_codproduto = orcprojativ.o55_orcproduto";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      left  join orcprojativunidaderesp  on  orcprojativunidaderesp.o13_orcprojativ = orcprojativ.o55_projativ ";
        $sql .= "                                        and  orcprojativunidaderesp.o13_anousu = orcprojativ.o55_anousu ";
        $sql .= "      left  join unidaderesp on  unidaderesp.o20_sequencial = orcprojativunidaderesp.o13_unidaderesp";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o55_anousu)) {
                $sql2 .= " where orcprojativ.o55_anousu = $o55_anousu ";
            }
            if (!empty($o55_projativ)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcprojativ.o55_projativ = $o55_projativ ";
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

    public function sql_query_file($o55_anousu = null, $o55_projativ = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from orcprojativ ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($o55_anousu)) {
                $sql2 .= " where orcprojativ.o55_anousu = $o55_anousu ";
            }
            if (!empty($o55_projativ)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcprojativ.o55_projativ = $o55_projativ ";
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

    public function sql_query_projetoAtividade($o55_anousu = null, $o55_projativ = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from orcprojativ ";
        $sql .= "      inner join orcproduto on orcproduto.o22_codproduto = orcprojativ.o55_orcproduto ";
        $sql .= "      inner join orcdotacao on orcdotacao.o58_anousu     = orcprojativ.o55_anousu ";
        $sql .= "                           and orcdotacao.o58_projativ   = orcprojativ.o55_projativ ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($o55_anousu!=null) {
                $sql2 .= " where orcprojativ.o55_anousu = $o55_anousu ";
            }
            if ($o55_projativ!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcprojativ.o55_projativ = $o55_projativ ";
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

    public function sql_query_rh($o55_anousu = null, $o55_projativ = null, $campos = "*", $ordem = null, $dbwhere = "")
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
        $sql .= " from orcprojativ ";
        $sql .= "      inner join db_config  on  db_config.codigo = orcprojativ.o55_instit ";
        $sql .= "      inner join orcdotacao on orcdotacao.o58_projativ = orcprojativ.o55_projativ
                           and orcdotacao.o58_anousu = orcprojativ.o55_anousu ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($o55_anousu!=null) {
                $sql2 .= " where orcprojativ.o55_anousu = $o55_anousu ";
            }
            if ($o55_projativ!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcprojativ.o55_projativ = $o55_projativ ";
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

    public function sql_query_projetoMetas($o55_anousu = null, $o55_projativ = null, $campos = "*", $ordem = null, $dbwhere = "")
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
    
        $sql .= " from orcprojativ 
          inner join orcproduto on orcproduto.o22_codproduto = orcprojativ.o55_orcproduto 
          inner join orcdotacao on orcdotacao.o58_anousu     = orcprojativ.o55_anousu 
                               and orcdotacao.o58_projativ   = orcprojativ.o55_projativ                   
          inner join orcfuncao on o52_funcao = o58_funcao
          inner join orcsubfuncao on o53_subfuncao = o58_subfuncao                                
          left join iniciativaprojativ on iniciativaprojativ.pl12_anoorcamento  = orcprojativ.o55_anousu
                                      and iniciativaprojativ.pl12_orcprojativ = orcprojativ.o55_projativ 
          left join iniciativaobjetivosprogramaestrategico on pl16_iniciativaprojativ = pl12_codigo
          left join objetivosprogramaestrategico on pl11_codigo = pl16_objetivosprogramaestrategico
          left join programaestrategico on pl9_codigo = pl11_programaestrategico
          left join orcprojativunidademedida on o221_orcprojativ = o55_projativ
                                            and o221_anousu = o55_anousu
          left join sigfisunidademedida on o220_sequencial = o221_unidade
    ";
    
        $sql2 = "";
        if ($dbwhere=="") {
            if ($o55_anousu!=null) {
                $sql2 .= " where orcprojativ.o55_anousu = $o55_anousu ";
            }
            if ($o55_projativ!=null) {
                if ($sql2!="") {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " orcprojativ.o55_projativ = $o55_projativ ";
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
