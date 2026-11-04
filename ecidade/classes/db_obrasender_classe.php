<?php

class cl_obrasender
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
    public $ob07_codconstr = 0;
    /**
     * @var int
     */
    public $ob07_codobra = 0;
    /**
     * @var int
     */
    public $ob07_lograd = 0;
    /**
     * @var int
     */
    public $ob07_numero = null;
    /**
     * @var string
     */
    public $ob07_compl = null;
    /**
     * @var int
     */
    public $ob07_bairro = 0;
    /**
     * @var int
     */
    public $ob07_areaatual = 0;
    /**
     * @var int
     */
    public $ob07_unidades = 0;
    /**
     * @var int
     */
    public $ob07_pavimentos = 0;
    /**
     * @var string
     */
    public $ob07_inicio = null;
    /**
     * @var string
     */
    public $ob07_fim = null;
    /**
     * @var int
     */
    public $ob07_areadescoberta = 0;
    /**
     * @var int
     */
    public $ob07_areacoberta = 0;
    public $campos = "ob07_codconstr = int4 = Código da construção
                      ob07_codobra = int4 = Código da obra
                      ob07_lograd = int4 = Logradouro
                      ob07_numero = int4 = Número
                      ob07_compl = varchar(20) = Complemento
                      ob07_bairro = int4 = Bairro
                      ob07_areaatual = float8 = Área atual
                      ob07_unidades = int4 = Unidades
                      ob07_pavimentos = int4 = Pavimentos
                      ob07_inicio = date = Data inicio
                      ob07_fim = date = Data final
                      ob07_areadescoberta = float4 = Área Descoberta
                      ob07_areacoberta = float4 = Área Coberta";

    public function __construct()
    {
        $this->rotulo = new rotulo('obrasender');
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
            $this->ob07_codconstr = ($this->ob07_codconstr == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_codconstr"] : $this->ob07_codconstr);
            $this->ob07_codobra = ($this->ob07_codobra == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_codobra"] : $this->ob07_codobra);
            $this->ob07_lograd = ($this->ob07_lograd == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_lograd"] : $this->ob07_lograd);
            $this->ob07_numero = ($this->ob07_numero == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_numero"] : $this->ob07_numero);
            $this->ob07_compl = ($this->ob07_compl == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_compl"] : $this->ob07_compl);
            $this->ob07_bairro = ($this->ob07_bairro == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_bairro"] : $this->ob07_bairro);
            $this->ob07_areaatual = ($this->ob07_areaatual === "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_areaatual"] : $this->ob07_areaatual);
            $this->ob07_unidades = ($this->ob07_unidades == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_unidades"] : $this->ob07_unidades);
            $this->ob07_pavimentos = ($this->ob07_pavimentos == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_pavimentos"] : $this->ob07_pavimentos);
            $this->ob07_inicio = ($this->ob07_inicio == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_inicio"] : $this->ob07_inicio);
            $this->ob07_fim = ($this->ob07_fim == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_fim"] : $this->ob07_fim);

            if (!empty($this->ob07_inicio)) {
                $data = new DBDate($this->ob07_inicio);
                $this->ob07_inicio = $data->getDate();
            }

            if (!empty($this->ob07_fim)) {
                $data = new DBDate($this->ob07_fim);
                $this->ob07_fim = $data->getDate();
            }

            $this->ob07_areadescoberta = ($this->ob07_areadescoberta === "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_areadescoberta"] : $this->ob07_areadescoberta);
            $this->ob07_areacoberta = ($this->ob07_areacoberta === "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_areacoberta"] : $this->ob07_areacoberta);
        } else {
            $this->ob07_codconstr = ($this->ob07_codconstr == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob07_codconstr"] : $this->ob07_codconstr);
        }
    }

    public function incluir($ob07_codconstr)
    {
        $this->atualizacampos();

        if ($this->ob07_codconstr === '' || $this->ob07_codconstr === null) {
            $this->erro_sql = " Campo Código da construção não informado.";
            $this->erro_campo = "ob07_codconstr";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_codobra === '' || $this->ob07_codobra === null) {
            $this->erro_sql = " Campo Código da obra não informado.";
            $this->erro_campo = "ob07_codobra";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_lograd === '' || $this->ob07_lograd === null) {
            $this->erro_sql = " Campo Logradouro não informado.";
            $this->erro_campo = "ob07_lograd";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_numero === null || $this->ob07_numero === '') {
            $this->ob07_numero = "0";
        }
        if ($this->ob07_bairro === '' || $this->ob07_bairro === null) {
            $this->erro_sql = " Campo Bairro não informado.";
            $this->erro_campo = "ob07_bairro";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_areaatual === '' || $this->ob07_areaatual === null) {
            $this->erro_sql = " Campo Área atual não informado.";
            $this->erro_campo = "ob07_areaatual";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_unidades === '' || $this->ob07_unidades === null) {
            $this->erro_sql = " Campo Unidades não informado.";
            $this->erro_campo = "ob07_unidades";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_pavimentos === '' || $this->ob07_pavimentos === null) {
            $this->erro_sql = " Campo Pavimentos não informado.";
            $this->erro_campo = "ob07_pavimentos";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_areadescoberta === '' || $this->ob07_areadescoberta === null) {
            $this->erro_sql = " Campo Área Descoberta não informado.";
            $this->erro_campo = "ob07_areadescoberta";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob07_areacoberta === '' || $this->ob07_areacoberta === null) {
            $this->erro_sql = " Campo Área Coberta não informado.";
            $this->erro_campo = "ob07_areacoberta";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
       $this->ob07_codconstr = $ob07_codconstr;
        if ($this->ob07_codconstr === null || $this->ob07_codconstr === '' || $this->ob07_codconstr === 0) {
            $this->erro_sql = " Campo ob07_codconstr não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = '0';
            return false;
        }
        $sql = "
            INSERT INTO obrasender (
                ob07_codconstr
                ,ob07_codobra
                ,ob07_lograd
                ,ob07_numero
                ,ob07_compl
                ,ob07_bairro
                ,ob07_areaatual
                ,ob07_unidades
                ,ob07_pavimentos
                ,ob07_inicio
                ,ob07_fim
                ,ob07_areadescoberta
                ,ob07_areacoberta
            ) VALUES (
                 " . ($this->ob07_codconstr === null || $this->ob07_codconstr === '' ? 'NULL' : $this->ob07_codconstr) . "
                ," . ($this->ob07_codobra === null || $this->ob07_codobra === '' ? 'NULL' : $this->ob07_codobra) . "
                ," . ($this->ob07_lograd === null || $this->ob07_lograd === '' ? 'NULL' : $this->ob07_lograd) . "
                ," . ($this->ob07_numero === null || $this->ob07_numero === '' ? 'NULL' : $this->ob07_numero) . "
                ," . ($this->ob07_compl === null || $this->ob07_compl === '' ? 'NULL' : "'{$this->ob07_compl}'") . "
                ," . ($this->ob07_bairro === null || $this->ob07_bairro === '' ? 'NULL' : $this->ob07_bairro) . "
                ," . ($this->ob07_areaatual === null || $this->ob07_areaatual === '' ? 'NULL' : $this->ob07_areaatual) . "
                ," . ($this->ob07_unidades === null || $this->ob07_unidades === '' ? 'NULL' : $this->ob07_unidades) . "
                ," . ($this->ob07_pavimentos === null || $this->ob07_pavimentos === '' ? 'NULL' : $this->ob07_pavimentos) . "
                ," . ($this->ob07_inicio === null || $this->ob07_inicio === '' ? 'NULL' : "'{$this->ob07_inicio}'") . "
                ," . ($this->ob07_fim === null || $this->ob07_fim === '' ? 'NULL' : "'{$this->ob07_fim}'") . "
                ," . ($this->ob07_areadescoberta === null || $this->ob07_areadescoberta === '' ? 'NULL' : $this->ob07_areadescoberta) . "
                ," . ($this->ob07_areacoberta === null || $this->ob07_areacoberta === '' ? 'NULL' : $this->ob07_areacoberta) . "
            )
        ";
     $result = db_query($sql);
     if ($result == false) {
       $this->erro_banco = str_replace("\n", "", @pg_last_error());
       if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
         $this->erro_sql = "endereço da obra () não Incluído. Inclusão Abortada.";
         $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "endereço da obra já cadastrado";
         $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       } else {
         $this->erro_sql = "endereço da obra () não Incluído. Inclusão Abortada.";
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

       $resaco = $this->sql_record($this->sql_query_file($this->ob07_codconstr  ));
       if ($resaco != false || $this->numrows != 0) {
         $resac = db_query("SELECT nextval('db_acount_id_acount_seq') AS acount");
         $acount = pg_result($resac, 0, 0);
         $resac = db_query("INSERT INTO db_acountacesso VALUES ($acount, " . db_getsession("DB_acessado") . ")");
         $resac = db_query("INSERT INTO db_acountkey VALUES ($acount,6010,'$this->ob07_codconstr','I')");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,6010,'','" . AddSlashes(pg_result($resaco,0,'ob07_codconstr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5926,'','" . AddSlashes(pg_result($resaco,0,'ob07_codobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5927,'','" . AddSlashes(pg_result($resaco,0,'ob07_lograd'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5928,'','" . AddSlashes(pg_result($resaco,0,'ob07_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5929,'','" . AddSlashes(pg_result($resaco,0,'ob07_compl'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5930,'','" . AddSlashes(pg_result($resaco,0,'ob07_bairro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5931,'','" . AddSlashes(pg_result($resaco,0,'ob07_areaatual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5932,'','" . AddSlashes(pg_result($resaco,0,'ob07_unidades'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5933,'','" . AddSlashes(pg_result($resaco,0,'ob07_pavimentos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5934,'','" . AddSlashes(pg_result($resaco,0,'ob07_inicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,5935,'','" . AddSlashes(pg_result($resaco,0,'ob07_fim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,1010579,'','" . AddSlashes(pg_result($resaco,0,'ob07_areadescoberta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,952,1010578,'','" . AddSlashes(pg_result($resaco,0,'ob07_areacoberta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ob07_codconstr = null)
    {
        $this->atualizacampos();

        $sql = "UPDATE obrasender SET ";
        $virgula = '';
        if (trim($this->ob07_codconstr) !== '' && $this->ob07_codconstr !== null) {
            $sql .= "{$virgula} ob07_codconstr = {$this->ob07_codconstr} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Código da construção" é obrigatório.');
        }
        if (trim($this->ob07_codobra) !== '' && $this->ob07_codobra !== null) {
            $sql .= "{$virgula} ob07_codobra = {$this->ob07_codobra} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Código da obra" é obrigatório.');
        }
        if (trim($this->ob07_lograd) !== '' && $this->ob07_lograd !== null) {
            $sql .= "{$virgula} ob07_lograd = {$this->ob07_lograd} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Logradouro" é obrigatório.');
        }
        if (trim($this->ob07_numero) !== '' && $this->ob07_numero !== null) {
            $sql .= "{$virgula} ob07_numero = {$this->ob07_numero} ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob07_numero = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob07_compl) !== '' && $this->ob07_compl !== null) {
            $sql .= "{$virgula} ob07_compl = '{$this->ob07_compl}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob07_compl = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob07_bairro) !== '' && $this->ob07_bairro !== null) {
            $sql .= "{$virgula} ob07_bairro = {$this->ob07_bairro} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Bairro" é obrigatório.');
        }
        if (trim($this->ob07_areaatual) !== '' && $this->ob07_areaatual !== null) {
            $sql .= "{$virgula} ob07_areaatual = {$this->ob07_areaatual} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Área atual" é obrigatório.');
        }
        if (trim($this->ob07_unidades) !== '' && $this->ob07_unidades !== null) {
            $sql .= "{$virgula} ob07_unidades = {$this->ob07_unidades} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Unidades" é obrigatório.');
        }
        if (trim($this->ob07_pavimentos) !== '' && $this->ob07_pavimentos !== null) {
            $sql .= "{$virgula} ob07_pavimentos = {$this->ob07_pavimentos} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Pavimentos" é obrigatório.');
        }
        if (trim($this->ob07_inicio) !== '' && $this->ob07_inicio !== null) {
            $sql .= "{$virgula} ob07_inicio = '{$this->ob07_inicio}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob07_inicio = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob07_fim) !== '' && $this->ob07_fim !== null) {
            $sql .= "{$virgula} ob07_fim = '{$this->ob07_fim}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob07_fim = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob07_areadescoberta) !== '' && $this->ob07_areadescoberta !== null) {
            $sql .= "{$virgula} ob07_areadescoberta = {$this->ob07_areadescoberta} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Área Descoberta" é obrigatório.');
        }
        if (trim($this->ob07_areacoberta) !== '' && $this->ob07_areacoberta !== null) {
            $sql .= "{$virgula} ob07_areacoberta = {$this->ob07_areacoberta} ";
        } else {
            throw new Exception('Campo "Área Coberta" é obrigatório.');
        }

        if ($ob07_codconstr !== '' && $ob07_codconstr !== null && $ob07_codconstr !== 0) {
            $sql .= ' WHERE';
            $sql .= " ob07_codconstr = {$this->ob07_codconstr}";
        }

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ob07_codconstr));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,6010,'$this->ob07_codconstr','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_codconstr"]) || $this->ob07_codconstr != "")
             $resac = db_query("insert into db_acount values($acount,952,6010,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_codconstr'))."','$this->ob07_codconstr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_codobra"]) || $this->ob07_codobra != "")
             $resac = db_query("insert into db_acount values($acount,952,5926,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_codobra'))."','$this->ob07_codobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_lograd"]) || $this->ob07_lograd != "")
             $resac = db_query("insert into db_acount values($acount,952,5927,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_lograd'))."','$this->ob07_lograd',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_numero"]) || $this->ob07_numero != "")
             $resac = db_query("insert into db_acount values($acount,952,5928,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_numero'))."','$this->ob07_numero',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_compl"]) || $this->ob07_compl != "")
             $resac = db_query("insert into db_acount values($acount,952,5929,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_compl'))."','$this->ob07_compl',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_bairro"]) || $this->ob07_bairro != "")
             $resac = db_query("insert into db_acount values($acount,952,5930,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_bairro'))."','$this->ob07_bairro',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_areaatual"]) || $this->ob07_areaatual != "")
             $resac = db_query("insert into db_acount values($acount,952,5931,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_areaatual'))."','$this->ob07_areaatual',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_unidades"]) || $this->ob07_unidades != "")
             $resac = db_query("insert into db_acount values($acount,952,5932,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_unidades'))."','$this->ob07_unidades',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_pavimentos"]) || $this->ob07_pavimentos != "")
             $resac = db_query("insert into db_acount values($acount,952,5933,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_pavimentos'))."','$this->ob07_pavimentos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_inicio"]) || $this->ob07_inicio != "")
             $resac = db_query("insert into db_acount values($acount,952,5934,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_inicio'))."','$this->ob07_inicio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_fim"]) || $this->ob07_fim != "")
             $resac = db_query("insert into db_acount values($acount,952,5935,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_fim'))."','$this->ob07_fim',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_areadescoberta"]) || $this->ob07_areadescoberta != "")
             $resac = db_query("insert into db_acount values($acount,952,1010579,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_areadescoberta'))."','$this->ob07_areadescoberta',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob07_areacoberta"]) || $this->ob07_areacoberta != "")
             $resac = db_query("insert into db_acount values($acount,952,1010578,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_areacoberta'))."','$this->ob07_areacoberta',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "endereço da obra não Alterado. Alteração Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "endereço da obra não foi Alterado. Alteração Executada.\\n";
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

    public function excluir($ob07_codconstr=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ob07_codconstr));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,6010,'$ob07_codconstr','E')");
           $resac  = db_query("insert into db_acount values($acount,952,6010,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_codconstr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5926,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_codobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5927,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_lograd'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5928,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5929,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_compl'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5930,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_bairro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5931,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_areaatual'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5932,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_unidades'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5933,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_pavimentos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5934,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_inicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,5935,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_fim'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,1010579,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_areadescoberta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,952,1010578,'','".AddSlashes(pg_result($resaco,$iresaco,'ob07_areacoberta'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from obrasender
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ob07_codconstr)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ob07_codconstr = $ob07_codconstr ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "endereço da obra não Excluído. Exclusão Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "endereço da obra não Encontrado. Exclusão não Efetuada.\\n";
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
        $this->erro_sql   = "Record Vazio na Tabela:obrasender";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ob07_codconstr = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from obrasender ";
     $sql .= "      inner join bairro  on  bairro.j13_codi = obrasender.ob07_bairro";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = obrasender.ob07_lograd";
     $sql .= "      inner join obrasconstr  on  obrasconstr.ob08_codconstr = obrasender.ob07_codconstr";
     $sql .= "      inner join caracter  on  caracter.j31_codigo = obrasconstr.ob08_ocupacao";
     $sql .= "      inner join obras  as a on   a.ob01_codobra = obrasconstr.ob08_codobra";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob07_codconstr)) {
         $sql2 .= " where obrasender.ob07_codconstr = $ob07_codconstr ";
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

    public function sql_query_file($ob07_codconstr = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from obrasender ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob07_codconstr)){
         $sql2 .= " where obrasender.ob07_codconstr = $ob07_codconstr ";
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

   function sql_query_constr( $ob07_codconstr=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from obrasender ";
     $sql .= "      inner join bairro  			on bairro.j13_codi 		 = obrasender.ob07_bairro";
     $sql .= "      left outer  join ruas   on ruas.j14_codigo 		 = obrasender.ob07_lograd";
     $sql .= "      inner join obrasconstr  on obrasconstr.ob08_codconstr = obrasender.ob07_codconstr";
     $sql .= "      inner join caracter  	  on caracter.j31_codigo = obrasconstr.ob08_tipoconstr";
     $sql .= "      inner join obras  as a  on a.ob01_codobra 		 = obrasconstr.ob08_codobra";
     $sql2 = "";
     if($dbwhere==""){
       if($ob07_codconstr!=null ){
         $sql2 .= " where obrasender.ob07_codconstr = $ob07_codconstr ";
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

    /**
     * Método de alteração alternativo para realizar a alter
     * @param integer $ob07_codconstr
     */
    function alterar_alternativo( $ob07_codconstr = null) {

        $this->atualizacampos();

        $sql     = " update obrasender set ";
        $virgula = "";

        if(trim($this->ob07_codconstr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_codconstr"])){

            $sql  .= $virgula." ob07_codconstr = $this->ob07_codconstr ";
            $virgula = ",";

            if (trim($this->ob07_codconstr) == null ) {

                $this->erro_sql = " Campo Código da construção nao Informado.";
                $this->erro_campo = "ob07_codconstr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ob07_codobra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_codobra"])){

            $sql  .= $virgula." ob07_codobra = $this->ob07_codobra ";
            $virgula = ",";
            if(trim($this->ob07_codobra) == null ){
                $this->erro_sql = " Campo Código da obra nao Informado.";
                $this->erro_campo = "ob07_codobra";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ob07_lograd)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_lograd"])){
            $sql  .= $virgula." ob07_lograd = $this->ob07_lograd ";
            $virgula = ",";
            if(trim($this->ob07_lograd) == null ){
                $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
                $this->erro_campo = "ob07_lograd";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ob07_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_numero"])){
            if(trim($this->ob07_numero)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ob07_numero"])){
                $this->ob07_numero = "0" ;
            }
            $sql  .= $virgula." ob07_numero = $this->ob07_numero ";
            $virgula = ",";
        }
        if(trim($this->ob07_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_compl"])){
            $sql  .= $virgula." ob07_compl = '$this->ob07_compl' ";
            $virgula = ",";
        }
        if(trim($this->ob07_bairro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_bairro"])){
            $sql  .= $virgula." ob07_bairro = $this->ob07_bairro ";
            $virgula = ",";
            if(trim($this->ob07_bairro) == null ){
                $this->erro_sql = " Campo Bairro nao Informado.";
                $this->erro_campo = "ob07_bairro";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ob07_areaatual)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_areaatual"])){
            $sql  .= $virgula." ob07_areaatual = $this->ob07_areaatual ";
            $virgula = ",";
            if(trim($this->ob07_areaatual) == null ){
                $this->erro_sql = " Campo Área atual nao Informado.";
                $this->erro_campo = "ob07_areaatual";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ob07_unidades)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_unidades"])){
            $sql  .= $virgula." ob07_unidades = $this->ob07_unidades ";
            $virgula = ",";
            if(trim($this->ob07_unidades) == null ){
                $this->erro_sql = " Campo Unidades nao Informado.";
                $this->erro_campo = "ob07_unidades";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ob07_pavimentos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_pavimentos"])){
            $sql  .= $virgula." ob07_pavimentos = $this->ob07_pavimentos ";
            $virgula = ",";
            if(trim($this->ob07_pavimentos) == null ){
                $this->erro_sql = " Campo Pavimentos nao Informado.";
                $this->erro_campo = "ob07_pavimentos";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->ob07_inicio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_inicio_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["ob07_inicio_dia"] !="") ){
            $sql  .= $virgula." ob07_inicio = '$this->ob07_inicio' ";
            $virgula = ",";
        } else {
            if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_inicio_dia"])){
                $sql  .= $virgula." ob07_inicio = null ";
                $virgula = ",";
            }
        }
        if(trim($this->ob07_fim)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_fim_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["ob07_fim_dia"] !="") ){
            $sql  .= $virgula." ob07_fim = '$this->ob07_fim' ";
            $virgula = ",";
        } else {
            $sql  .= $virgula." ob07_fim = null ";
            $virgula = ",";
        }

        if (trim($this->ob07_areadescoberta) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_areadescoberta"])) {
            $sql .= $virgula . " ob07_areadescoberta = $this->ob07_areadescoberta ";
            $virgula = ",";
            if (trim($this->ob07_areadescoberta) == null) {
                $this->erro_sql = " Campo Área Coberta não Informado.";
                $this->erro_campo = "ob07_areadescoberta";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }

        if (trim($this->ob07_areacoberta) != "" || isset($GLOBALS["HTTP_POST_VARS"]["ob07_areacoberta"])) {
            $sql .= $virgula . " ob07_areacoberta = $this->ob07_areacoberta ";
            $virgula = ",";
            if (trim($this->ob07_areacoberta) == null) {
                $this->erro_sql = " Campo Área Coberta não Informado.";
                $this->erro_campo = "ob07_areacoberta";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "",
                  str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";

                return false;
            }
        }

        $sql .= " where ";
        if($ob07_codconstr!=null){
            $sql .= " ob07_codconstr = $this->ob07_codconstr";
        }
        $resaco = $this->sql_record($this->sql_query_file($this->ob07_codconstr));
        if($this->numrows>0){
            for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,6010,'$this->ob07_codconstr','A')");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_codconstr"]))
                    $resac = db_query("insert into db_acount values($acount,952,6010,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_codconstr'))."','$this->ob07_codconstr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_codobra"]))
                    $resac = db_query("insert into db_acount values($acount,952,5926,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_codobra'))."','$this->ob07_codobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_lograd"]))
                    $resac = db_query("insert into db_acount values($acount,952,5927,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_lograd'))."','$this->ob07_lograd',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_numero"]))
                    $resac = db_query("insert into db_acount values($acount,952,5928,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_numero'))."','$this->ob07_numero',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_compl"]))
                    $resac = db_query("insert into db_acount values($acount,952,5929,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_compl'))."','$this->ob07_compl',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_bairro"]))
                    $resac = db_query("insert into db_acount values($acount,952,5930,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_bairro'))."','$this->ob07_bairro',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_areaatual"]))
                    $resac = db_query("insert into db_acount values($acount,952,5931,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_areaatual'))."','$this->ob07_areaatual',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_unidades"]))
                    $resac = db_query("insert into db_acount values($acount,952,5932,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_unidades'))."','$this->ob07_unidades',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_pavimentos"]))
                    $resac = db_query("insert into db_acount values($acount,952,5933,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_pavimentos'))."','$this->ob07_pavimentos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_inicio"]))
                    $resac = db_query("insert into db_acount values($acount,952,5934,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_inicio'))."','$this->ob07_inicio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["ob07_fim"]))
                    $resac = db_query("insert into db_acount values($acount,952,5935,'".AddSlashes(pg_result($resaco,$conresaco,'ob07_fim'))."','$this->ob07_fim',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "endereço da obra nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->ob07_codconstr;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "endereço da obra nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->ob07_codconstr;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            }else{
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->ob07_codconstr;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
}
