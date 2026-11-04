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

class cl_obrashabite
{
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
    /**
     * @var int
     */
    public $ob09_codhab = 0;
    /**
     * @var int
     */
    public $ob09_engprefeitura = 0;
    /**
     * @var int
     */
    public $ob09_codconstr = 0;
    /**
     * @var string
     */
    public $ob09_habite = 0;
    /**
     * @var bool
     */
    public $ob09_parcial = false;
    /**
     * @var string
     */
    public $ob09_data = null;
    /**
     * @var int
     */
    public $ob09_area = 0;
    /**
     * @var string
     */
    public $ob09_obs = null;
    /**
     * @var string
     */
    public $ob09_obsinss = null;
    /**
     * @var string
     */
    public $ob09_logradcorresp = null;
    /**
     * @var int
     */
    public $ob09_numcorresp = null;
    /**
     * @var string
     */
    public $ob09_compl = null;
    /**
     * @var string
     */
    public $ob09_bairrocorresp = null;
    /**
     * @var int
     */
    public $ob09_codibgemunic = null;
    /**
     * @var int
     */
    public $ob09_anousu = 0;
    /**
     * @var string
     */
    public $ob09_datafinalobra = null;
    /**
     * @var string
     */
    public $ob09_datacancelamentoreativacao = null;
    /**
     * @var bool
     */
    public $ob09_ativo = true;
    /**
     * @var int
     */ 
    public $campos = "ob09_codhab = int4 = Código do habite-se
                      ob09_engprefeitura = int4 = Eng. Prefeitura
                      ob09_codconstr = int4 = Código da construção
                      ob09_habite = int4 = Habite-se
                      ob09_parcial = bool = Parcial
                      ob09_data = date = Data do habite-se
                      ob09_area = float8 = Área
                      ob09_obs = text = Observação
                      ob09_obsinss = text = Observação
                      ob09_logradcorresp = varchar(55) = Rua
                      ob09_numcorresp = int4 = Número
                      ob09_compl = varchar(20) = Complemento
                      ob09_bairrocorresp = varchar(20) = Bairro
                      ob09_codibgemunic = int4 = Código IBGE
                      ob09_anousu = int4 = Exercício
                      ob09_datafinalobra = date = Data Final da Obra
                      ob09_datacancelamentoreativacao = date = Data de Cancelamento/Reativação
                      ob09_ativo = bool = Ativo";

    public function __construct()
    {
        $this->rotulo = new rotulo('obrashabite');
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if ($this->erro_status == '0' || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert('{$this->erro_msg}')</script>";
            if ($retorna == true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->ob09_codhab = ($this->ob09_codhab == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_codhab"] : $this->ob09_codhab);
            $this->ob09_engprefeitura = ($this->ob09_engprefeitura == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_engprefeitura"] : $this->ob09_engprefeitura);
            $this->ob09_codconstr = ($this->ob09_codconstr == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_codconstr"] : $this->ob09_codconstr);
            $this->ob09_habite = ($this->ob09_habite == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_habite"] : $this->ob09_habite);
            $this->ob09_parcial = ($this->ob09_parcial === "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_parcial"] : $this->ob09_parcial);
            $this->ob09_area = ($this->ob09_area == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_area"] : $this->ob09_area);
            $this->ob09_obs = ($this->ob09_obs == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_obs"] : $this->ob09_obs);
            $this->ob09_obsinss = ($this->ob09_obsinss == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_obsinss"] : $this->ob09_obsinss);
            $this->ob09_logradcorresp = ($this->ob09_logradcorresp == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_logradcorresp"] : $this->ob09_logradcorresp);
            $this->ob09_numcorresp = ($this->ob09_numcorresp == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_numcorresp"] : $this->ob09_numcorresp);
            $this->ob09_compl = ($this->ob09_compl == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_compl"] : $this->ob09_compl);
            $this->ob09_bairrocorresp = ($this->ob09_bairrocorresp == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_bairrocorresp"] : $this->ob09_bairrocorresp);
            $this->ob09_codibgemunic = ($this->ob09_codibgemunic == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_codibgemunic"] : $this->ob09_codibgemunic);
            $this->ob09_anousu = ($this->ob09_anousu == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_anousu"] : $this->ob09_anousu);
            $this->ob09_data = ($this->ob09_data == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_data"] : $this->ob09_data);
            $this->ob09_ativo = ($this->ob09_ativo === "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_ativo"] : $this->ob09_ativo);
            $this->ob09_datacancelamentoreativacao = ($this->ob09_datacancelamentoreativacao == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_datacancelamentoreativacao"] : $this->ob09_datacancelamentoreativacao);
            $this->ob09_datafinalobra = ($this->ob09_datafinalobra == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_datafinalobra"] : $this->ob09_datafinalobra);

            if (!empty($this->ob09_data)) {
                $data = explode('/', $this->ob09_data);
                $this->ob09_data = "{$data[2]}-{$data[1]}-{$data[0]}";
            }

            if (!empty($this->ob09_datafinalobra)) {
                $data = explode('/', $this->ob09_datafinalobra);
                $this->ob09_datafinalobra = "{$data[2]}-{$data[1]}-{$data[0]}";
            }

            if (!empty($this->ob09_datacancelamentoreativacao)) {
                $data = explode('/', $this->ob09_datacancelamentoreativacao);
                $this->ob09_datacancelamentoreativacao = "{$data[2]}-{$data[1]}-{$data[0]}";
            }
        } else {
            $this->ob09_codhab = ($this->ob09_codhab == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob09_codhab"] : $this->ob09_codhab);
        }
    }

    public function incluir($ob09_codhab)
    {
        $this->atualizacampos();

        if ($this->ob09_engprefeitura === '' || $this->ob09_engprefeitura === null) {
            $this->erro_sql = " Campo Eng. Prefeitura não informado.";
            $this->erro_campo = "ob09_engprefeitura";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob09_codconstr === '' || $this->ob09_codconstr === null) {
            $this->erro_sql = " Campo Código da construção não informado.";
            $this->erro_campo = "ob09_codconstr";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if ($this->ob09_parcial === '' || $this->ob09_parcial === null) {
            $this->erro_sql = " Campo Parcial não informado.";
            $this->erro_campo = "ob09_parcial";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob09_data === '' || $this->ob09_data === null) {
            $this->erro_sql = " Campo Data do habite-se não informado.";
            $this->erro_campo = "ob09_data_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob09_area === '' || $this->ob09_area === null) {
            $this->erro_sql = " Campo Área não informado.";
            $this->erro_campo = "ob09_area";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob09_numcorresp === null || $this->ob09_numcorresp === '') {
            $this->ob09_numcorresp = "0";
        }
        if ($this->ob09_codibgemunic === null || $this->ob09_codibgemunic === '') {
            $this->ob09_codibgemunic = "0";
        }
        if ($this->ob09_anousu === '' || $this->ob09_anousu === null) {
            $this->erro_sql = " Campo Exercício não informado.";
            $this->erro_campo = "ob09_anousu";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob09_ativo === '' || $this->ob09_ativo === null) {
            $this->erro_sql = " Campo Ativo não informado.";
            $this->erro_campo = "ob09_ativo";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }

        if(empty($this->ob09_habite)){
          $res = db_query("SELECT max(ob09_habite) as habite FROM obrashabite");
          $maxHabite = db_utils::fieldsMemory($res, 0)->habite;
          $habite = (int)$maxHabite + 1;

          if($res==false){
            
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Verifique o cadastro da sequencia: obrashabite_ob09_habite_seq do campo: ob09_habite";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
          }

          $this->ob09_habite = $habite;
        }

        /**
         * Valida se código do alvará ja está cadastrado
         */
        if(!empty($this->ob09_habite)) {
          $rsAlvara = db_query("select ob09_codhab from obrashabite where ob09_habite = {$this->ob09_habite} 
                                and ob09_anousu= {$this->ob09_anousu}");
          if (pg_num_rows($rsAlvara) > 0) {
            
            $this->erro_msg    = "Habite-se já registrado para a obra ". pg_result($rsAlvara,0,0);
            $this->erro_status = "0";
            return false;
          }
        }

        if ($ob09_codhab === '' || $ob09_codhab === null || $ob09_codhab === 0) {
            $result = db_query("select nextval('obrashabite_ob09_codhab_seq')");
            if (!$result) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: obrashabite_ob09_codhab_seq do campo: ob09_codhab";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->ob09_codhab = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM obrashabite_ob09_codhab_seq");
            if ($result && pg_result($result, 0, 0) < $ob09_codhab) {
                $this->erro_sql = " Campo ob09_codhab maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->ob09_codhab = $ob09_codhab;
            }
        }
        if ($this->ob09_codhab === null || $this->ob09_codhab === '' || $this->ob09_codhab === 0) {
            $this->erro_sql = " Campo ob09_codhab não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = '0';
            return false;
        }
        $sql = "
            INSERT INTO obrashabite (
                ob09_codhab
                ,ob09_engprefeitura
                ,ob09_codconstr
                ,ob09_habite
                ,ob09_parcial
                ,ob09_data
                ,ob09_area
                ,ob09_obs
                ,ob09_obsinss
                ,ob09_logradcorresp
                ,ob09_numcorresp
                ,ob09_compl
                ,ob09_bairrocorresp
                ,ob09_codibgemunic
                ,ob09_anousu
                ,ob09_datafinalobra
                ,ob09_datacancelamentoreativacao
                ,ob09_ativo
            ) VALUES (
                 " . ($this->ob09_codhab === null || $this->ob09_codhab === '' ? 'NULL' : $this->ob09_codhab) . "
                ," . ($this->ob09_engprefeitura === null || $this->ob09_engprefeitura === '' ? 'NULL' : $this->ob09_engprefeitura) . "
                ," . ($this->ob09_codconstr === null || $this->ob09_codconstr === '' ? 'NULL' : $this->ob09_codconstr) . "
                ," . ($this->ob09_habite === null || $this->ob09_habite === '' ? 'NULL' : "'{$this->ob09_habite}'") . "
                ," . ($this->ob09_parcial === null || $this->ob09_parcial === '' ? 'NULL' : ($this->ob09_parcial ? 'TRUE' : 'FALSE')) . "
                ," . ($this->ob09_data === null || $this->ob09_data === '' ? 'NULL' : "'{$this->ob09_data}'") . "
                ," . ($this->ob09_area === null || $this->ob09_area === '' ? 'NULL' : $this->ob09_area) . "
                ," . ($this->ob09_obs === null || $this->ob09_obs === '' ? 'NULL' : "'{$this->ob09_obs}'") . "
                ," . ($this->ob09_obsinss === null || $this->ob09_obsinss === '' ? 'NULL' : "'{$this->ob09_obsinss}'") . "
                ," . ($this->ob09_logradcorresp === null || $this->ob09_logradcorresp === '' ? 'NULL' : "'{$this->ob09_logradcorresp}'") . "
                ," . ($this->ob09_numcorresp === null || $this->ob09_numcorresp === '' ? 'NULL' : $this->ob09_numcorresp) . "
                ," . ($this->ob09_compl === null || $this->ob09_compl === '' ? 'NULL' : "'{$this->ob09_compl}'") . "
                ," . ($this->ob09_bairrocorresp === null || $this->ob09_bairrocorresp === '' ? 'NULL' : "'{$this->ob09_bairrocorresp}'") . "
                ," . ($this->ob09_codibgemunic === null || $this->ob09_codibgemunic === '' ? 'NULL' : $this->ob09_codibgemunic) . "
                ," . ($this->ob09_anousu === null || $this->ob09_anousu === '' ? 'NULL' : $this->ob09_anousu) . "
                ," . ($this->ob09_datafinalobra === null || $this->ob09_datafinalobra === '' ? 'NULL' : "'{$this->ob09_datafinalobra}'") . "
                ," . ($this->ob09_datacancelamentoreativacao === null || $this->ob09_datacancelamentoreativacao === '' ? 'NULL' : "'{$this->ob09_datacancelamentoreativacao}'") . "
                ," . ($this->ob09_ativo === null || $this->ob09_ativo === '' ? 'NULL' : ($this->ob09_ativo ? 'TRUE' : 'FALSE')) . " 
            )
        ";

     $result = db_query($sql);
     if ($result == false) {
       $this->erro_banco = str_replace("\n", "", @pg_last_error());
       if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
         $this->erro_sql = "habite-se da obra () não Incluído. Inclusão Abortada.";
         $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "habite-se da obra já cadastrado";
         $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       } else {
         $this->erro_sql = "habite-se da obra () não Incluído. Inclusão Abortada.";
         $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
     $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ob09_codhab  ));
       if ($resaco != false || $this->numrows != 0) {
         $resac = db_query("SELECT nextval('db_acount_id_acount_seq') AS acount");
         $acount = pg_result($resac, 0, 0);
         $resac = db_query("INSERT INTO db_acountacesso VALUES ($acount, " . db_getsession("DB_acessado") . ")");
         $resac = db_query("INSERT INTO db_acountkey VALUES ($acount,5972,'$this->ob09_codhab','I')");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,5972,'','" . AddSlashes(pg_result($resaco,0,'ob09_codhab'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11861,'','" . AddSlashes(pg_result($resaco,0,'ob09_engprefeitura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,5973,'','" . AddSlashes(pg_result($resaco,0,'ob09_codconstr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,5974,'','" . AddSlashes(pg_result($resaco,0,'ob09_habite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,5975,'','" . AddSlashes(pg_result($resaco,0,'ob09_parcial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,5976,'','" . AddSlashes(pg_result($resaco,0,'ob09_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,5977,'','" . AddSlashes(pg_result($resaco,0,'ob09_area'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,5978,'','" . AddSlashes(pg_result($resaco,0,'ob09_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11319,'','" . AddSlashes(pg_result($resaco,0,'ob09_obsinss'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11320,'','" . AddSlashes(pg_result($resaco,0,'ob09_logradcorresp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11321,'','" . AddSlashes(pg_result($resaco,0,'ob09_numcorresp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11322,'','" . AddSlashes(pg_result($resaco,0,'ob09_compl'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11323,'','" . AddSlashes(pg_result($resaco,0,'ob09_bairrocorresp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11324,'','" . AddSlashes(pg_result($resaco,0,'ob09_codibgemunic'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,11889,'','" . AddSlashes(pg_result($resaco,0,'ob09_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,1010583,'','" . AddSlashes(pg_result($resaco,0,'ob09_datafinalobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,1010582,'','" . AddSlashes(pg_result($resaco,0,'ob09_datacancelamentoreativacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,954,1010581,'','" . AddSlashes(pg_result($resaco,0,'ob09_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ob09_codhab = null)
    {
        $this->atualizacampos();

        $sql = "UPDATE obrashabite SET ";
        $virgula = '';
        if (trim($this->ob09_codhab) !== '' && $this->ob09_codhab !== null) {
            $sql .= "{$virgula} ob09_codhab = {$this->ob09_codhab} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Código do habite-se" é obrigatório.');
        }
        if (trim($this->ob09_engprefeitura) !== '' && $this->ob09_engprefeitura !== null) {
            $sql .= "{$virgula} ob09_engprefeitura = {$this->ob09_engprefeitura} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Eng. Prefeitura" é obrigatório.');
        }
        if (trim($this->ob09_codconstr) !== '' && $this->ob09_codconstr !== null) {
            $sql .= "{$virgula} ob09_codconstr = {$this->ob09_codconstr} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Código da construção" é obrigatório.');
        }
        if (trim($this->ob09_habite) !== '' && $this->ob09_habite !== null) {
            $sql .= "{$virgula} ob09_habite = {$this->ob09_habite} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Habite-se" é obrigatório.');
        }
        if (is_bool($this->ob09_parcial)) {
            $sql .= "{$virgula} ob09_parcial = " . ($this->ob09_parcial === true ? 'TRUE' : 'FALSE') . " ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Parcial" é obrigatório.');
        }
        if (trim($this->ob09_data) !== '' && $this->ob09_data !== null) {
            $sql .= "{$virgula} ob09_data = '{$this->ob09_data}' ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Data do habite-se" é obrigatório.');
        }
        if (trim($this->ob09_area) !== '' && $this->ob09_area !== null) {
            $sql .= "{$virgula} ob09_area = {$this->ob09_area} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Área" é obrigatório.');
        }
        if (trim($this->ob09_obs) !== '' && $this->ob09_obs !== null) {
            $sql .= "{$virgula} ob09_obs = '{$this->ob09_obs}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_obs = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_obsinss) !== '' && $this->ob09_obsinss !== null) {
            $sql .= "{$virgula} ob09_obsinss = '{$this->ob09_obsinss}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_obsinss = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_logradcorresp) !== '' && $this->ob09_logradcorresp !== null) {
            $sql .= "{$virgula} ob09_logradcorresp = '{$this->ob09_logradcorresp}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_logradcorresp = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_numcorresp) !== '' && $this->ob09_numcorresp !== null) {
            $sql .= "{$virgula} ob09_numcorresp = {$this->ob09_numcorresp} ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_numcorresp = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_compl) !== '' && $this->ob09_compl !== null) {
            $sql .= "{$virgula} ob09_compl = '{$this->ob09_compl}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_compl = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_bairrocorresp) !== '' && $this->ob09_bairrocorresp !== null) {
            $sql .= "{$virgula} ob09_bairrocorresp = '{$this->ob09_bairrocorresp}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_bairrocorresp = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_codibgemunic) !== '' && $this->ob09_codibgemunic !== null) {
            $sql .= "{$virgula} ob09_codibgemunic = {$this->ob09_codibgemunic} ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_codibgemunic = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_anousu) !== '' && $this->ob09_anousu !== null) {
            $sql .= "{$virgula} ob09_anousu = {$this->ob09_anousu} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Exercício" é obrigatório.');
        }
        if (trim($this->ob09_datafinalobra) !== '' && $this->ob09_datafinalobra !== null) {
            $sql .= "{$virgula} ob09_datafinalobra = '{$this->ob09_datafinalobra}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_datafinalobra = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob09_datacancelamentoreativacao) !== '' && $this->ob09_datacancelamentoreativacao !== null) {
            $sql .= "{$virgula} ob09_datacancelamentoreativacao = '{$this->ob09_datacancelamentoreativacao}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob09_datacancelamentoreativacao = NULL ";
            $virgula = ',';
        }
        if (is_bool($this->ob09_ativo)) {
            $sql .= "{$virgula} ob09_ativo = " . ($this->ob09_ativo === true ? 'TRUE' : 'FALSE') . " ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Ativo" é obrigatório.');
        }

        if ($ob09_codhab !== '' && $ob09_codhab !== null && $ob09_codhab !== 0) {
            $sql .= ' WHERE';
            $sql .= " ob09_codhab = {$this->ob09_codhab}";
        }

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ob09_codhab));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5972,'$this->ob09_codhab','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_codhab"]) || $this->ob09_codhab != "")
             $resac = db_query("insert into db_acount values($acount,954,5972,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_codhab'))."','$this->ob09_codhab',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_engprefeitura"]) || $this->ob09_engprefeitura != "")
             $resac = db_query("insert into db_acount values($acount,954,11861,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_engprefeitura'))."','$this->ob09_engprefeitura',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_codconstr"]) || $this->ob09_codconstr != "")
             $resac = db_query("insert into db_acount values($acount,954,5973,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_codconstr'))."','$this->ob09_codconstr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_habite"]) || $this->ob09_habite != "")
             $resac = db_query("insert into db_acount values($acount,954,5974,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_habite'))."','$this->ob09_habite',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_parcial"]) || $this->ob09_parcial != "")
             $resac = db_query("insert into db_acount values($acount,954,5975,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_parcial'))."','$this->ob09_parcial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_data"]) || $this->ob09_data != "")
             $resac = db_query("insert into db_acount values($acount,954,5976,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_data'))."','$this->ob09_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_area"]) || $this->ob09_area != "")
             $resac = db_query("insert into db_acount values($acount,954,5977,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_area'))."','$this->ob09_area',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_obs"]) || $this->ob09_obs != "")
             $resac = db_query("insert into db_acount values($acount,954,5978,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_obs'))."','$this->ob09_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_obsinss"]) || $this->ob09_obsinss != "")
             $resac = db_query("insert into db_acount values($acount,954,11319,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_obsinss'))."','$this->ob09_obsinss',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_logradcorresp"]) || $this->ob09_logradcorresp != "")
             $resac = db_query("insert into db_acount values($acount,954,11320,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_logradcorresp'))."','$this->ob09_logradcorresp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_numcorresp"]) || $this->ob09_numcorresp != "")
             $resac = db_query("insert into db_acount values($acount,954,11321,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_numcorresp'))."','$this->ob09_numcorresp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_compl"]) || $this->ob09_compl != "")
             $resac = db_query("insert into db_acount values($acount,954,11322,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_compl'))."','$this->ob09_compl',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_bairrocorresp"]) || $this->ob09_bairrocorresp != "")
             $resac = db_query("insert into db_acount values($acount,954,11323,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_bairrocorresp'))."','$this->ob09_bairrocorresp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_codibgemunic"]) || $this->ob09_codibgemunic != "")
             $resac = db_query("insert into db_acount values($acount,954,11324,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_codibgemunic'))."','$this->ob09_codibgemunic',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_anousu"]) || $this->ob09_anousu != "")
             $resac = db_query("insert into db_acount values($acount,954,11889,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_anousu'))."','$this->ob09_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_datafinalobra"]) || $this->ob09_datafinalobra != "")
             $resac = db_query("insert into db_acount values($acount,954,1010583,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_datafinalobra'))."','$this->ob09_datafinalobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_datacancelamentoreativacao"]) || $this->ob09_datacancelamentoreativacao != "")
             $resac = db_query("insert into db_acount values($acount,954,1010582,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_datacancelamentoreativacao'))."','$this->ob09_datacancelamentoreativacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob09_ativo"]) || $this->ob09_ativo != "")
             $resac = db_query("insert into db_acount values($acount,954,1010581,'".AddSlashes(pg_result($resaco,$conresaco,'ob09_ativo'))."','$this->ob09_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "habite-se da obra não Alterado. Alteração Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "habite-se da obra não foi Alterado. Alteração Executada.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($ob09_codhab=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ob09_codhab));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,5972,'$ob09_codhab','E')");
           $resac  = db_query("insert into db_acount values($acount,954,5972,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_codhab'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11861,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_engprefeitura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,5973,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_codconstr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,5974,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_habite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,5975,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_parcial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,5976,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,5977,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_area'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,5978,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11319,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_obsinss'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11320,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_logradcorresp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11321,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_numcorresp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11322,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_compl'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11323,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_bairrocorresp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11324,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_codibgemunic'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,11889,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,1010583,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_datafinalobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,1010582,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_datacancelamentoreativacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,954,1010581,'','".AddSlashes(pg_result($resaco,$iresaco,'ob09_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from obrashabite
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ob09_codhab)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ob09_codhab = $ob09_codhab ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "habite-se da obra não Excluído. Exclusão Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "habite-se da obra não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_num_rows($result);
      if ($this->numrows == 0) {
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:obrashabite";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ob09_codhab = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
        $sql .= " from obrashabite ";
        $sql .= "      inner join obrasconstr        on  obrasconstr.ob08_codconstr = obrashabite.ob09_codconstr";
        $sql .= "      inner join caracter           on  caracter.j31_codigo = obrasconstr.ob08_ocupacao";
        $sql .= "      inner join obras              on  obras.ob01_codobra = obrasconstr.ob08_codobra";
        $sql .= "      inner join obraspropri        on obras.ob01_codobra = obraspropri.ob03_codobra";
        $sql .= "      inner join cgm                on cgm.z01_numcgm = obraspropri.ob03_numcgm";
        $sql .= "      left  join obrashabiteprot    on obrashabiteprot.ob19_codhab = obrashabite.ob09_codhab";
        $sql .= "      left  join obrashabiteprotoff on obrashabiteprotoff.ob22_codhab = obrashabite.ob09_codhab";
        $sql .= "      left  join protprocesso       on obrashabiteprot.ob19_codproc = protprocesso.p58_codproc";
        $sql .= "      left  join obrastec           on obrastec.ob15_sequencial = obrashabite.ob09_engprefeitura";
        $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob09_codhab)) {
         $sql2 .= " where obrashabite.ob09_codhab = $ob09_codhab ";
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

    public function sql_query_file($ob09_codhab = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from obrashabite ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob09_codhab)){
         $sql2 .= " where obrashabite.ob09_codhab = $ob09_codhab ";
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

   function sql_query_obco ( $ob09_codhab=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from obrashabite ";
     $sql .= "      inner join obrasconstr  on  obrasconstr.ob08_codconstr = obrashabite.ob09_codconstr";
     $sql .= "      inner join obras  on  obras.ob01_codobra = obrasconstr.ob08_codobra";
     $sql .= "      inner join obraspropri  on obras.ob01_codobra = obraspropri.ob03_codobra";
     $sql .= "      inner join cgm  on cgm.z01_numcgm = obraspropri.ob03_numcgm";
     $sql .= "      inner join caracter a on a.j31_codigo = obrasconstr.ob08_ocupacao";
     $sql .= "      inner join caracter b on b.j31_codigo = obrasconstr.ob08_tipoconstr";
     $sql .= "      inner join caracter c on c.j31_codigo = obrasconstr.ob08_tipolanc";
     $sql2 = "";
     if($dbwhere==""){
       if($ob09_codhab!=null ){
         $sql2 .= " where obrashabite.ob09_codhab = $ob09_codhab ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_engpref ( $ob09_codhab=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from obrashabite ";
     $sql .= "			left join obrastec  on  obrastec.ob15_sequencial = obrashabite.ob09_engprefeitura ";
     $sql .= "			left join cgm       on  cgm.z01_numcgm           = obrastec.ob15_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($ob09_codhab!=null ){
         $sql2 .= " where obrashabite.ob09_codhab = $ob09_codhab ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   function sql_query_obras_habite ( $ob09_codhab=null,$campos="*",$ordem=null,$dbwhere=""){
		$sql = "select ";
		if($campos != "*" ){
			$campos_sql = explode("#",$campos);
			$virgula = "";
			for($i=0;$i<sizeof($campos_sql);$i++){
				$sql .= $virgula.$campos_sql[$i];
				$virgula = ",";
			}
		}else{
			$sql .= $campos;
		}
		$sql .= " from obrashabite ";
		$sql .= "      inner join obrasconstr        on  obrasconstr.ob08_codconstr    = obrashabite.ob09_codconstr";
		$sql .= "      inner join caracter           on  caracter.j31_codigo 					 = obrasconstr.ob08_ocupacao";
		$sql .= "      inner join obras              on  obras.ob01_codobra 					 = obrasconstr.ob08_codobra";
		$sql .= "      inner join obraspropri        on obras.ob01_codobra 						 = obraspropri.ob03_codobra";
		$sql .= "      inner join cgm                on cgm.z01_numcgm 								 = obraspropri.ob03_numcgm";
		$sql .= "      left  join obrashabiteprot    on obrashabiteprot.ob19_codhab    = obrashabite.ob09_codhab";
		$sql .= "      left  join obrashabiteprotoff on obrashabiteprotoff.ob22_codhab = obrashabite.ob09_codhab";
		$sql .= "      left  join protprocesso       on obrashabiteprot.ob19_codproc   = protprocesso.p58_codproc";
		$sql .= "      left  join obrastec           on obrastec.ob15_sequencial       = obrashabite.ob09_engprefeitura";
		$sql .= "      left  join obrasiptubase      on obrasiptubase.ob24_obras       = obras.ob01_codobra";
		$sql .= "      left  join obraslotei         on obraslotei.ob06_codobra        = obras.ob01_codobra";
		$sql2 = "";
		if($dbwhere==""){
			if($ob09_codhab!=null ){
				$sql2 .= " where obrashabite.ob09_codhab = $ob09_codhab ";
			}
		}else if($dbwhere != ""){
			$sql2 = " where $dbwhere";
		}
		$sql .= $sql2;
		if($ordem != null ){
			$sql .= " order by ";
			$campos_sql = explode("#",$ordem);
			$virgula = "";
			for($i=0;$i<sizeof($campos_sql);$i++){
				$sql .= $virgula.$campos_sql[$i];
				$virgula = ",";
			}
		}
		return $sql;
	}

    function sql_query_responsavel_carta() {

        $iIdUsuario = db_getsession('DB_id_usuario');
        $iInstit    = db_getsession('DB_instit');

        $sSql  = "select z01_nome    as nome_servidor,                                     ";
        $sSql .= "       rh37_descr  as cargo_servidor,                                    ";
        $sSql .= "       rh01_regist as matricula_servidor                                 ";
        $sSql .= "from db_usuarios                                                         ";
        $sSql .= "inner join db_usuacgm on db_usuacgm.id_usuario = db_usuarios.id_usuario  ";
        $sSql .= "inner join cgm        on cgm.z01_numcgm        = db_usuacgm.cgmlogin     ";
        $sSql .= " left join rhpessoal  on rhpessoal.rh01_numcgm = db_usuacgm.cgmlogin     ";
        $sSql .= " left join rhfuncao   on rhfuncao.rh37_funcao  = rhpessoal.rh01_funcao   ";
        $sSql .= "                     and rhfuncao.rh37_instit  = rhpessoal.rh01_instit   ";
        $sSql .= "where db_usuarios.id_usuario = {$iIdUsuario}														 ";
        $sSql .= "  and rhpessoal.rh01_instit  = {$iInstit}     														 ";

        return $sSql;

    }

    public function sql_query_cartaHabiteseDadosTemplate($sCampos, $iCodigoHabitese) {
  
      $sSql  = "select {$sCampos} ";
      $sSql .= "   from ( ";
      $sSql .= " select distinct         obraspropri.ob03_numcgm       as cgm, ";
      $sSql .= "                    cgm.z01_nome                                                   as nome_proprietario, ";
      $sSql .= "                    cgm.z01_cgccpf                                                 as cpf_cnpj_proprietario, ";
      $sSql .= "                    cgm.z01_ender       as logradouro, ";
      $sSql .= "                    cgm.z01_numero       as numero, ";
      $sSql .= "                    cgm.z01_compl       as complemento, ";
      $sSql .= "                    cgm.z01_bairro       as bairro, ";
      $sSql .= "                    lote.j34_setor             || '-' ||        setor.j30_descr            || '/' ||          lote.j34_quadra            || '/' ||        lote.j34_lote                                                  as sql, ";
      $sSql .= "                    setorloc.j05_codigoproprio || '-' ||        setorloc.j05_descr         || '/' ||        loteloc.j06_quadraloc      || '/' ||         loteloc.j06_lote                                               as pql, ";
      $sSql .= "                    iptubase.j01_matric                       as matricula_imovel, ";
      $sSql .= "                    obrashabite.ob09_habite       as cod_habite, ";
      $sSql .= "                    obrashabite.ob09_codhab       as sequencial_habite, ";
      $sSql .= "                    extract (year from obrashabite.ob09_data) as ano_sequencial_habite, ";
      $sSql .= "                    ( select array_to_string(array_accum(obrasalvara.ob04_alvara),', ') from projetos.obrashabite a inner join projetos.obrasconstr on obrasconstr.ob08_codconstr = a.ob09_codconstr inner join projetos.obras on obras.ob01_codobra = obrasconstr.ob08_codobra inner join projetos.obrasalvara on obrasalvara.ob04_codobra = obras.ob01_codobra where a.ob09_codhab = obrashabite.ob09_codhab ) as sequencial_alvara, ";
      $sSql .= "                    ( select array_to_string(array_accum(extract (year from obrasalvara.ob04_data)),', ') from projetos.obrashabite a inner join projetos.obrasconstr on obrasconstr.ob08_codconstr = a.ob09_codconstr inner join projetos.obras on obras.ob01_codobra = obrasconstr.ob08_codobra inner join projetos.obrasalvara on obrasalvara.ob04_codobra = obras.ob01_codobra where a.ob09_codhab = obrashabite.ob09_codhab ) as ano_sequencial_alvara, ";
      $sSql .= "                    ( select array_to_string(array_accum(to_char(obrasalvara.ob04_dataexpedicao,'DD-MM-YYYY')),', ') from projetos.obrashabite a inner join projetos.obrasconstr on obrasconstr.ob08_codconstr = a.ob09_codconstr inner join projetos.obras on obras.ob01_codobra = obrasconstr.ob08_codobra inner join projetos.obrasalvara on obrasalvara.ob04_codobra = obras.ob01_codobra where a.ob09_codhab = obrashabite.ob09_codhab ) as expedicao_alvara, ";
      $sSql .= "                    to_char(obrashabite.ob09_data, 'dd/mm/yyyy')                   as data_habite, ";
      $sSql .= "                    engenheiro.z01_nome       as engenheiro, ";
      $sSql .= "                    engenheiro.z01_numcgm     as cgm_responsavel_tecnico, ";
      $sSql .= "                    engenheiro.z01_nome       as nome_responsavel_tecnico, ";
      $sSql .= "                    engenheiro.z01_cgccpf     as cpf_responsavel_tecnico, ";
      $sSql .= "                    obrastec.ob15_crea        as crea, ";
      $sSql .= "                    case when ob22_codproc is not null then ob22_codproc else protprocesso.p58_codproc end as protocolo, ";
      $sSql .= "                    to_char(case when ob22_codproc is not null then ob22_data else protprocesso.p58_dtproc end, 'dd/mm/yyyy') as data_protocolo, ";
      $sSql .= "                    obrasconstr.ob08_area                                          as area_total, ";
      $sSql .= "                    obrashabite.ob09_area                                          as area_liberada, ";
      $sSql .= "                    ruastipo.j88_sigla || ' ' || ruas.j14_nome                                                  as endereco_obra, ";
      $sSql .= "                    obrasender.ob07_numero                                         as numero_endereco_obra, ";
      $sSql .= "                    obrasender.ob07_compl                                          as complemento_endereco_obra,    ";
      $sSql .= "                    bairro.j13_descr                                               as bairro_endereco_obra,         ";
      $sSql .= "                    obrashabite.ob09_obs       as observacoes,         ";
      $sSql .= "                    obrashabite.ob09_obsinss       as observacoes_inss,        ";
      $sSql .= "                    case when ob09_parcial is true then 'Parcial' else 'Total' end as tipo_habite,        ";
      $sSql .= "                    (select z01_nome            ";
      $sSql .= "                       from db_usuacgm                 ";
      $sSql .= "                      inner join cgm on cgm.z01_numcgm = db_usuacgm.cgmlogin        ";
      $sSql .= "                      where db_usuacgm.id_usuario = ".db_getsession('DB_id_usuario').")                 as nome_servidor, ";
      $sSql .= "  ";
      $sSql .= "             (select rh37_descr ";
      $sSql .= "                from rhpessoal ";
      $sSql .= "                     inner join rhfuncao    on rhpessoal.rh01_funcao = rhfuncao.rh37_funcao ";
      $sSql .= "                                           and rhfuncao.rh37_instit  = ".db_getsession('DB_instit');
      $sSql .= "                     inner join db_usuacgm  on db_usuacgm.cgmlogin   = rhpessoal.rh01_numcgm ";
      $sSql .= "                                           and db_usuacgm.id_usuario = ".db_getsession('DB_id_usuario');
      $sSql .= "                    left join pessoal.rhpessoalmov  on rh02_regist = rh01_regist ";
      $sSql .= "                                                   and rh02_anousu = fc_anofolha(".db_getsession('DB_instit')."::integer) ";
      $sSql .= "                                                   and rh02_mesusu = fc_mesfolha(".db_getsession('DB_instit')."::integer) ";
      $sSql .= "                                                   and rh02_instit = ".db_getsession('DB_instit');
      $sSql .= "             left join pessoal.rhpesrescisao on rh02_seqpes = rh05_seqpes ";
      $sSql .= "             where rh05_seqpes is null ";
      $sSql .= "             ) as cargo_servidor, ";
      $sSql .= "  ";
      $sSql .= "             (select rh01_regist ";
      $sSql .= "             from rhpessoal ";
      $sSql .= "             inner join db_usuacgm  on db_usuacgm.cgmlogin   = rhpessoal.rh01_numcgm ";
      $sSql .= "                                   and db_usuacgm.id_usuario = ".db_getsession('DB_id_usuario');
      $sSql .= "             left join pessoal.rhpessoalmov  on rh02_regist = rh01_regist ";
      $sSql .= "                                            and rh02_anousu = fc_anofolha(".db_getsession('DB_instit')."::integer)";
      $sSql .= "                                            and rh02_mesusu = fc_mesfolha(".db_getsession('DB_instit')."::integer)";
      $sSql .= "                                            and rh02_instit = ".db_getsession('DB_instit');
      $sSql .= "             left join pessoal.rhpesrescisao on rh02_seqpes = rh05_seqpes ";
      $sSql .= "             where rh05_seqpes is null ) as matricula_servidor, ";
      $sSql .= "             ob09_anousu as exercicio, ";
      $sSql .= "             ob08_tipoconstr ||'-'|| c.j31_descr as tipo_construcao, ";
      $sSql .= "             obrasalvara.ob04_alvara    as alvara, ";
      $sSql .= "             case when obrasalvara.ob04_dataexpedicao is not null then ";
      $sSql .= "                       to_char(obrasalvara.ob04_dataexpedicao,'DD/MM/YYYY') ";
      $sSql .= "                  when obrasalvara.ob04_data is not null then ";
      $sSql .= "                       to_char(obrasalvara.ob04_data,'DD/MM/YYYY') ";
      $sSql .= "                  else to_char(current_date,'DD/MM/YYYY') ";
      $sSql .= "             end                                                             as data_alvara, ";
      $sSql .= "             case when obrasalvara.ob04_dataexpedicao is not null then ";
      $sSql .= "                       fc_dataextenso(obrasalvara.ob04_dataexpedicao) ";
      $sSql .= "                  when obrasalvara.ob04_data is not null then ";
      $sSql .= "                       fc_dataextenso(obrasalvara.ob04_data) ";
      $sSql .= "                  else fc_dataextenso(current_date) ";
      $sSql .= "             end                                                             as data_expedicao_extenso, ";
      $sSql .= "             fc_dataextenso(current_date)                                    as data_atual_extenso, ";
      $sSql .= "             case when obrasresp_cgm.z01_numcgm is not null then ";
      $sSql .= "                       obrasresp_cgm.z01_numcgm::varchar ";
      $sSql .= "                  else '' ";
      $sSql .= "             end                                                             as cgm_responsavel_execucao, ";
      $sSql .= "             case when obrasresp_cgm.z01_nome is not null then ";
      $sSql .= "                       obrasresp_cgm.z01_nome::varchar ";
      $sSql .= "                  else '' ";
      $sSql .= "             end                                                             as nome_responsavel_execucao, ";
      $sSql .= "             case when obrasresp_cgm.z01_cgccpf is not null then ";
      $sSql .= "                       obrasresp_cgm.z01_cgccpf::varchar ";
      $sSql .= "                  else '' ";
      $sSql .= "             end                                                             as cpf_responsavel_execucao, ";
      $sSql .= "  ";
      $sSql .= "             (select j31_descr ";
      $sSql .= "                from caracter ";
      $sSql .= "               where obrasconstr.ob08_ocupacao   = j31_codigo ";
      $sSql .= "                 and obrasconstr.ob08_codobra    = obras.ob01_codobra ";
      $sSql .= "             )                                                               as carac_ocupacao, ";
      $sSql .= "             (select j31_descr ";
      $sSql .= "                from caracter ";
      $sSql .= "               where obrasconstr.ob08_tipolanc   = j31_codigo ";
      $sSql .= "                 and obrasconstr.ob08_codobra    = obras.ob01_codobra ";
      $sSql .= "             )                                                               as carac_tipo_lancamento, ";
      $sSql .= "             (select j31_descr ";
      $sSql .= "                from caracter ";
      $sSql .= "               where obrasconstr.ob08_tipoconstr = j31_codigo ";
      $sSql .= "                 and obrasconstr.ob08_codobra    = obras.ob01_codobra ";
      $sSql .= "             )                                                               as carac_tipo_construcao ";
      $sSql .= "  ";
      $sSql .= "              ";
      $sSql .= "             from obrashabite ";
      $sSql .= "                    inner join obrasconstr ";
      $sSql .= "                            on obrasconstr.ob08_codconstr  = obrashabite.ob09_codconstr ";
      $sSql .= "                    inner join obras ";
      $sSql .= "                            on obras.ob01_codobra          = obrasconstr.ob08_codobra ";
      $sSql .= "                    inner join obrasiptubase ";
      $sSql .= "                            on obrasiptubase.ob24_obras    = obras.ob01_codobra ";
      $sSql .= "                    inner join iptubase ";
      $sSql .= "                            on iptubase.j01_matric         = obrasiptubase.ob24_iptubase ";
      $sSql .= "                    inner join obraspropri ";
      $sSql .= "                            on obraspropri.ob03_codobra    = obras.ob01_codobra ";
      $sSql .= "                    inner join cgm ";
      $sSql .= "                            on cgm.z01_numcgm              = obraspropri.ob03_numcgm ";
      $sSql .= "                    inner join obrastecnicos ";
      $sSql .= "                            on obrastecnicos.ob20_codobra  = obras.ob01_codobra ";
      $sSql .= "                    inner join obrastec ";
      $sSql .= "                            on obrastec.ob15_sequencial    = obrastecnicos.ob20_obrastec ";
      $sSql .= "                    inner join cgm engenheiro ";
      $sSql .= "                            on engenheiro.z01_numcgm       = obrastec.ob15_numcgm ";
      $sSql .= "                    inner join obrasender ";
      $sSql .= "                            on obrasender.ob07_codconstr   = obrasconstr.ob08_codconstr ";
      $sSql .= "                    inner join ruas ";
      $sSql .= "                            on ruas.j14_codigo             = obrasender.ob07_lograd ";
      $sSql .= "                    inner join ruastipo ";
      $sSql .= "                            on j14_tipo                    = j88_codigo ";
      $sSql .= "                    inner join bairro ";
      $sSql .= "                            on bairro.j13_codi             = obrasender.ob07_bairro ";
      $sSql .= "                    inner join caracter ";
      $sSql .= "                            on caracter.j31_codigo         = obrasconstr.ob08_ocupacao ";
      $sSql .= "                    inner join caracter c ";
      $sSql .= "                            on c.j31_codigo                = obrasconstr.ob08_tipoconstr ";
      $sSql .= "                    inner join lote ";
      $sSql .= "                            on lote.j34_idbql              = iptubase.j01_idbql ";
      $sSql .= "                    inner join setor ";
      $sSql .= "                            on setor.j30_codi              = lote.j34_setor ";
      $sSql .= "                    inner join obrasalvara ";
      $sSql .= "                            on obras.ob01_codobra          = obrasalvara.ob04_codobra ";
      $sSql .= "                     left join loteloc ";
      $sSql .= "                            on loteloc.j06_idbql           = iptubase.j01_idbql ";
      $sSql .= "                     left join setorloc ";
      $sSql .= "                            on setorloc.j05_codigo         = loteloc.j06_setorloc ";
      $sSql .= "                     left join obrashabiteprot ";
      $sSql .= "                            on obrashabiteprot.ob19_codhab = obrashabite.ob09_codhab ";
      $sSql .= "                     left join protprocesso ";
      $sSql .= "                            on protprocesso.p58_codproc    = obrashabiteprot.ob19_codproc ";
      $sSql .= "                     left join obrashabiteprotoff ";
      $sSql .= "                            on ob09_codhab                 = ob22_codhab ";
      $sSql .= "                     left join obrasresp ";
      $sSql .= "                            on obras.ob01_codobra          = obrasresp.ob10_codobra ";
      $sSql .= "                     left join cgm obrasresp_cgm ";
      $sSql .= "                            on obrasresp.ob10_numcgm       = obrasresp_cgm.z01_numcgm ";
      $sSql .= "             where obrashabite.ob09_codhab = $iCodigoHabitese) as habitese"; 
      
      return $sSql; 	 
    }
  
}
