<?php

class cl_obrasalvara
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
    public $ob04_codobra = 0;
    /**
     * @var int
     */
    public $ob04_alvara = 0;
    /**
     * @var string
     */
    public $ob04_data = null;
    /**
     * @var string
     */
    public $ob04_processo = null;
    /**
     * @var string
     */
    public $ob04_titularprocesso = null;
    /**
     * @var string
     */
    public $ob04_dtprocesso = null;
    /**
     * @var string
     */
    public $ob04_obsprocesso = null;
    /**
     * @var string
     */
    public $ob04_dtvalidade = null;
    /**
     * @var string
     */
    public $ob04_dataexpedicao = null;
    /**
     * @var string
     */
    public $ob04_classe = null;
    /**
     * @var bool
     */
    public $ob04_ativo = true;
    /**
     * @var string
     */
    public $ob04_datacancelamentoreativacao = null;
    public $ob04_idalvara = 0; 
    public $campos = "ob04_codobra = int4 = Código da Obra
                      ob04_alvara = int4 = Alvará
                      ob04_data = date = Data do Alvará
                      ob04_processo = varchar(100) = Código do Processo
                      ob04_titularprocesso = varchar(100) = Nome do Titular
                      ob04_dtprocesso = date = Data do Processo
                      ob04_obsprocesso = text = Observações
                      ob04_dtvalidade = date = Data de Validade do Alvará
                      ob04_dataexpedicao = date = Data de Expedição
                      ob04_classe = varchar(30) = Classe
                      ob04_ativo = bool = Ativo
                      ob04_datacancelamentoreativacao = date = Data de Cancelamento Ativação
                      ob04_idalvara = int4 = Id alvará";

    public function __construct()
    {
        $this->rotulo = new rotulo('obrasalvara');
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
            $this->ob04_codobra = ($this->ob04_codobra == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_codobra"] : $this->ob04_codobra);
            $this->ob04_alvara = ($this->ob04_alvara == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_alvara"] : $this->ob04_alvara);
            $this->ob04_processo = ($this->ob04_processo == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_processo"] : $this->ob04_processo);
            $this->ob04_titularprocesso = ($this->ob04_titularprocesso == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_titularprocesso"] : $this->ob04_titularprocesso);
            $this->ob04_obsprocesso = ($this->ob04_obsprocesso == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_obsprocesso"] : $this->ob04_obsprocesso);
            $this->ob04_data = ($this->ob04_data == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_data"] : $this->ob04_data);
            $this->ob04_dtprocesso = ($this->ob04_dtprocesso == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_dtprocesso"] : $this->ob04_dtprocesso);
            $this->ob04_dtvalidade = ($this->ob04_dtvalidade == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_dtvalidade"] : $this->ob04_dtvalidade);
            $this->ob04_dataexpedicao = ($this->ob04_dataexpedicao == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_dataexpedicao"] : $this->ob04_dataexpedicao);
            $this->ob04_datacancelamentoreativacao = ($this->ob04_datacancelamentoreativacao == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_datacancelamentoreativacao"] : $this->ob04_datacancelamentoreativacao);
            
            if (!empty($this->ob04_data)) {
                $data = new DBDate($this->ob04_data);
                $this->ob04_data = $data->getDate();
            }

            if (!empty($this->ob04_dtprocesso)) {
                $data = new DBDate($this->ob04_dtprocesso);
                $this->ob04_dtprocesso = $data->getDate();
            }

            if (!empty($this->ob04_dtvalidade)) {
                $data = new DBDate($this->ob04_dtvalidade);
                $this->ob04_dtvalidade = $data->getDate();
            }

            if (!empty($this->ob04_dataexpedicao)) {
                $data = new DBDate($this->ob04_dataexpedicao);
                $this->ob04_dataexpedicao = $data->getDate();
            }

            if (!empty($this->ob04_datacancelamentoreativacao)) {
                $data = new DBDate($this->ob04_datacancelamentoreativacao);
                $this->ob04_datacancelamentoreativacao = $data->getDate();
            }

            $this->ob04_classe = ($this->ob04_classe == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_classe"] : $this->ob04_classe);
            $this->ob04_ativo = ($this->ob04_ativo === "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_ativo"] : $this->ob04_ativo);
            $this->ob04_idalvara = ($this->ob04_idalvara == ""?@$GLOBALS["HTTP_POST_VARS"]["ob04_idalvara"]:$this->ob04_idalvara);
        } else {
            $this->ob04_codobra = ($this->ob04_codobra == "" ? @$GLOBALS["HTTP_POST_VARS"]["ob04_codobra"] : $this->ob04_codobra);
        }
    }

    public function incluir($ob04_codobra)
    {
        $this->atualizacampos();

        if ($this->ob04_codobra === '' || $this->ob04_codobra === null) {
            $this->erro_sql = " Campo Código da Obra não informado.";
            $this->erro_campo = "ob04_codobra";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob04_idalvara === '' || $this->ob04_idalvara === null || $this->ob04_idalvara === 0) {
          $result = db_query("select nextval('obrasalvara_ob04_idalvara_seq')"); 
          if (!$result) {
              $this->erro_banco = str_replace("\n", "", @pg_last_error());
              $this->erro_sql = "Verifique o cadastro da sequencia: obrasalvara_ob04_idalvara_seq do campo: ob04_idalvara"; 
              $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
              $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
              $this->erro_status = "0";
              return false;
          }
          $this->ob04_idalvara = pg_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM obrasalvara_ob04_idalvara_seq");
            if ($result && pg_result($result, 0, 0) < $this->ob04_idalvara) {
                $this->erro_sql = " Campo ob04_idalvara maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->ob04_idalvara = $this->ob04_idalvara;
            }
        }
        if ($this->ob04_alvara === '' || $this->ob04_alvara === null) {
            $this->erro_sql = " Campo Alvará não informado.";
            $this->erro_campo = "ob04_alvara";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob04_data === '' || $this->ob04_data === null) {
            $this->erro_sql = " Campo Data do Alvará não informado.";
            $this->erro_campo = "ob04_data_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob04_dtprocesso === null || $this->ob04_dtprocesso === '') {
            $this->ob04_dtprocesso = "null";
        }
        if ($this->ob04_dtvalidade === '' || $this->ob04_dtvalidade === null) {
            $this->erro_sql = " Campo Data de Validade do Alvará não informado.";
            $this->erro_campo = "ob04_dtvalidade_dia";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->ob04_dataexpedicao === null || $this->ob04_dataexpedicao === '') {
            $this->ob04_dataexpedicao = "null";
        }
        if ($this->ob04_ativo === null || $this->ob04_ativo === '') {
            $this->ob04_ativo = "1";
        }
        if ($this->ob04_datacancelamentoreativacao === null || $this->ob04_datacancelamentoreativacao === '') {
            $this->ob04_datacancelamentoreativacao = "null";
        }
        if ($this->ob04_codobra === null || $this->ob04_codobra === '' || $this->ob04_codobra === 0) {
            $this->erro_sql = " Campo ob04_codobra não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = '0';
            return false;
        }
        $sql = "
            INSERT INTO obrasalvara (
                ob04_codobra
                ,ob04_alvara
                ,ob04_data
                ,ob04_processo
                ,ob04_titularprocesso
                ,ob04_dtprocesso
                ,ob04_obsprocesso
                ,ob04_dtvalidade
                ,ob04_dataexpedicao
                ,ob04_classe
                ,ob04_ativo
                ,ob04_datacancelamentoreativacao
                ,ob04_idalvara
            ) VALUES (
                 " . ($this->ob04_codobra === null || $this->ob04_codobra === '' ? 'NULL' : $this->ob04_codobra) . "
                ," . ($this->ob04_alvara === null || $this->ob04_alvara === '' ? 'NULL' : $this->ob04_alvara) . "
                ," . ($this->ob04_data === null || $this->ob04_data === '' ? 'NULL' : "'{$this->ob04_data}'") . "
                ," . ($this->ob04_processo === null || $this->ob04_processo === '' ? 'NULL' : "'{$this->ob04_processo}'") . "
                ," . ($this->ob04_titularprocesso === null || $this->ob04_titularprocesso === '' ? 'NULL' : "'{$this->ob04_titularprocesso}'") . "
                ," . ($this->ob04_dtprocesso === null || $this->ob04_dtprocesso === '' ? 'NULL' : "'{$this->ob04_dtprocesso}'") . "
                ," . ($this->ob04_obsprocesso === null || $this->ob04_obsprocesso === '' ? 'NULL' : "'{$this->ob04_obsprocesso}'") . "
                ," . ($this->ob04_dtvalidade === null || $this->ob04_dtvalidade === '' ? 'NULL' : "'{$this->ob04_dtvalidade}'") . "
                ," . ($this->ob04_dataexpedicao === null || $this->ob04_dataexpedicao === '' ? 'NULL' : "'{$this->ob04_dataexpedicao}'") . "
                ," . ($this->ob04_classe === null || $this->ob04_classe === '' ? 'NULL' : "'{$this->ob04_classe}'") . "
                ," . ($this->ob04_ativo === null || $this->ob04_ativo === '' ? 'NULL' : ($this->ob04_ativo ? 'TRUE' : 'FALSE')) . "
                ," . ($this->ob04_datacancelamentoreativacao === null || $this->ob04_datacancelamentoreativacao === '' ? 'NULL' : "'{$this->ob04_datacancelamentoreativacao}'") . "
                ,$this->ob04_idalvara 
            )
        ";
     $result = db_query($sql);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n", "", @pg_last_error());
       if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
         $this->erro_sql = "alvara da obra () não Incluído. Inclusão Abortada.";
         $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "alvara da obra já cadastrado";
         $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       } else {
         $this->erro_sql = "alvara da obra () não Incluído. Inclusão Abortada.";
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

       $resaco = $this->sql_record($this->sql_query_file($this->ob04_codobra  ));
       if ($resaco != false || $this->numrows != 0) {
         $resac = db_query("SELECT nextval('db_acount_id_acount_seq') AS acount");
         $acount = pg_result($resac, 0, 0);
         $resac = db_query("INSERT INTO db_acountacesso VALUES ($acount, " . db_getsession("DB_acessado") . ")");
         $resac = db_query("INSERT INTO db_acountkey VALUES ($acount,5917,'$this->ob04_codobra','I')");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,5917,'','" . AddSlashes(pg_result($resaco,0,'ob04_codobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,5918,'','" . AddSlashes(pg_result($resaco,0,'ob04_alvara'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,5919,'','" . AddSlashes(pg_result($resaco,0,'ob04_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,18640,'','" . AddSlashes(pg_result($resaco,0,'ob04_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,18641,'','" . AddSlashes(pg_result($resaco,0,'ob04_titularprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,18642,'','" . AddSlashes(pg_result($resaco,0,'ob04_dtprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,18643,'','" . AddSlashes(pg_result($resaco,0,'ob04_obsprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,18644,'','" . AddSlashes(pg_result($resaco,0,'ob04_dtvalidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,20461,'','" . AddSlashes(pg_result($resaco,0,'ob04_dataexpedicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,1010575,'','" . AddSlashes(pg_result($resaco,0,'ob04_classe'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,1010576,'','" . AddSlashes(pg_result($resaco,0,'ob04_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("INSERT INTO db_acount VALUES ($acount,949,1010577,'','" . AddSlashes(pg_result($resaco,0,'ob04_datacancelamentoreativacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,949,1014584,'','".AddSlashes(pg_result($resaco,0,'ob04_idalvara'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ob04_codobra = null)
    {
        $this->atualizacampos();

        $sql = "UPDATE obrasalvara SET ";
        $virgula = '';
        if (trim($this->ob04_codobra) !== '' && $this->ob04_codobra !== null) {
            $sql .= "{$virgula} ob04_codobra = {$this->ob04_codobra} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Código da Obra" é obrigatório.');
        }
        if (trim($this->ob04_alvara) !== '' && $this->ob04_alvara !== null) {
            $sql .= "{$virgula} ob04_alvara = {$this->ob04_alvara} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Alvará" é obrigatório.');
        }
        if (trim($this->ob04_data) !== '' && $this->ob04_data !== null) {
            $sql .= "{$virgula} ob04_data = '{$this->ob04_data}' ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Data do Alvará" é obrigatório.');
        }
        if (trim($this->ob04_processo) !== '' && $this->ob04_processo !== null) {
            $sql .= "{$virgula} ob04_processo = '{$this->ob04_processo}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_processo = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_titularprocesso) !== '' && $this->ob04_titularprocesso !== null) {
            $sql .= "{$virgula} ob04_titularprocesso = '{$this->ob04_titularprocesso}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_titularprocesso = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_dtprocesso) !== '' && $this->ob04_dtprocesso !== null) {
            $sql .= "{$virgula} ob04_dtprocesso = '{$this->ob04_dtprocesso}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_dtprocesso = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_obsprocesso) !== '' && $this->ob04_obsprocesso !== null) {
            $sql .= "{$virgula} ob04_obsprocesso = '{$this->ob04_obsprocesso}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_obsprocesso = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_dtvalidade) !== '' && $this->ob04_dtvalidade !== null) {
            $sql .= "{$virgula} ob04_dtvalidade = '{$this->ob04_dtvalidade}' ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Data de Validade do Alvará" é obrigatório.');
        }
        if (trim($this->ob04_dataexpedicao) !== '' && $this->ob04_dataexpedicao !== null) {
            $sql .= "{$virgula} ob04_dataexpedicao = '{$this->ob04_dataexpedicao}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_dataexpedicao = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_classe) !== '' && $this->ob04_classe !== null) {
            $sql .= "{$virgula} ob04_classe = '{$this->ob04_classe}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_classe = NULL ";
            $virgula = ',';
        }
        if ($this->ob04_ativo !== '' && $this->ob04_ativo !== null) {
            $sql .= "{$virgula} ob04_ativo = " . ($this->ob04_ativo === true ? 'TRUE' : 'FALSE') . " ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_ativo = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_datacancelamentoreativacao) !== '' && $this->ob04_datacancelamentoreativacao !== null) {
            $sql .= "{$virgula} ob04_datacancelamentoreativacao = '{$this->ob04_datacancelamentoreativacao}' ";
        } else {
            $sql .= "{$virgula} ob04_datacancelamentoreativacao = NULL ";
        }
        if(trim($this->ob04_idalvara)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob04_idalvara"])){ 
          $sql  .= $virgula." ob04_idalvara = $this->ob04_idalvara ";
          $virgula = ",";
          if(trim($this->ob04_idalvara) == null ){ 
            $this->erro_sql = " Campo Id alvará não informado.";
            $this->erro_campo = "ob04_idalvara";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
          }
        }

        if ($ob04_codobra !== '' && $ob04_codobra !== null && $ob04_codobra !== 0) {
            $sql .= ' WHERE';
            $sql .= " ob04_codobra = {$this->ob04_codobra}";
        }

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ob04_codobra));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5917,'$this->ob04_codobra','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_codobra"]) || $this->ob04_codobra != "")
             $resac = db_query("insert into db_acount values($acount,949,5917,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_codobra'))."','$this->ob04_codobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_alvara"]) || $this->ob04_alvara != "")
             $resac = db_query("insert into db_acount values($acount,949,5918,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_alvara'))."','$this->ob04_alvara',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_data"]) || $this->ob04_data != "")
             $resac = db_query("insert into db_acount values($acount,949,5919,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_data'))."','$this->ob04_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_processo"]) || $this->ob04_processo != "")
             $resac = db_query("insert into db_acount values($acount,949,18640,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_processo'))."','$this->ob04_processo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_titularprocesso"]) || $this->ob04_titularprocesso != "")
             $resac = db_query("insert into db_acount values($acount,949,18641,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_titularprocesso'))."','$this->ob04_titularprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_dtprocesso"]) || $this->ob04_dtprocesso != "")
             $resac = db_query("insert into db_acount values($acount,949,18642,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_dtprocesso'))."','$this->ob04_dtprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_obsprocesso"]) || $this->ob04_obsprocesso != "")
             $resac = db_query("insert into db_acount values($acount,949,18643,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_obsprocesso'))."','$this->ob04_obsprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_dtvalidade"]) || $this->ob04_dtvalidade != "")
             $resac = db_query("insert into db_acount values($acount,949,18644,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_dtvalidade'))."','$this->ob04_dtvalidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_dataexpedicao"]) || $this->ob04_dataexpedicao != "")
             $resac = db_query("insert into db_acount values($acount,949,20461,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_dataexpedicao'))."','$this->ob04_dataexpedicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_classe"]) || $this->ob04_classe != "")
             $resac = db_query("insert into db_acount values($acount,949,1010575,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_classe'))."','$this->ob04_classe',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_ativo"]) || $this->ob04_ativo != "")
             $resac = db_query("insert into db_acount values($acount,949,1010576,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_ativo'))."','$this->ob04_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_datacancelamentoreativacao"]) || $this->ob04_datacancelamentoreativacao != "")
             $resac = db_query("insert into db_acount values($acount,949,1010577,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_datacancelamentoreativacao'))."','$this->ob04_datacancelamentoreativacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
             if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_idalvara"]) || $this->ob04_idalvara != "")
             $resac = db_query("insert into db_acount values($acount,949,1014584,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_idalvara'))."','$this->ob04_idalvara',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "alvara da obra não Alterado. Alteração Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "alvara da obra não foi Alterado. Alteração Executada.\\n";
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
   
   public function alterarAlvaraRenovacao($ob04_codobra = null)
    {
        $this->atualizacampos();

        $sql = "UPDATE obrasalvara SET ";
        $virgula = '';
        if (trim($this->ob04_codobra) !== '' && $this->ob04_codobra !== null) {
            $sql .= "{$virgula} ob04_codobra = {$this->ob04_codobra} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Código da Obra" é obrigatório.');
        }
        if (trim($this->ob04_alvara) !== '' && $this->ob04_alvara !== null) {
            $sql .= "{$virgula} ob04_alvara = {$this->ob04_alvara} ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Alvará" é obrigatório.');
        }
        if (trim($this->ob04_processo) !== '' && $this->ob04_processo !== null) {
            $sql .= "{$virgula} ob04_processo = '{$this->ob04_processo}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_processo = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_titularprocesso) !== '' && $this->ob04_titularprocesso !== null) {
            $sql .= "{$virgula} ob04_titularprocesso = '{$this->ob04_titularprocesso}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_titularprocesso = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_dtprocesso) !== '' && $this->ob04_dtprocesso !== null) {
            $sql .= "{$virgula} ob04_dtprocesso = '{$this->ob04_dtprocesso}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_dtprocesso = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_obsprocesso) !== '' && $this->ob04_obsprocesso !== null) {
            $sql .= "{$virgula} ob04_obsprocesso = '{$this->ob04_obsprocesso}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_obsprocesso = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_dtvalidade) !== '' && $this->ob04_dtvalidade !== null) {
            $sql .= "{$virgula} ob04_dtvalidade = '{$this->ob04_dtvalidade}' ";
            $virgula = ',';
        } else {
            throw new Exception('Campo "Data de Validade do Alvará" é obrigatório.');
        }
        if (trim($this->ob04_dataexpedicao) !== '' && $this->ob04_dataexpedicao !== null) {
            $sql .= "{$virgula} ob04_dataexpedicao = '{$this->ob04_dataexpedicao}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_dataexpedicao = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_classe) !== '' && $this->ob04_classe !== null) {
            $sql .= "{$virgula} ob04_classe = '{$this->ob04_classe}' ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_classe = NULL ";
            $virgula = ',';
        }
        if ($this->ob04_ativo !== '' && $this->ob04_ativo !== null) {
            $sql .= "{$virgula} ob04_ativo = " . ($this->ob04_ativo === true ? 'TRUE' : 'FALSE') . " ";
            $virgula = ',';
        } else {
            $sql .= "{$virgula} ob04_ativo = NULL ";
            $virgula = ',';
        }
        if (trim($this->ob04_datacancelamentoreativacao) !== '' && $this->ob04_datacancelamentoreativacao !== null) {
            $sql .= "{$virgula} ob04_datacancelamentoreativacao = '{$this->ob04_datacancelamentoreativacao}' ";
        } else {
            $sql .= "{$virgula} ob04_datacancelamentoreativacao = NULL ";
        }
        if(trim($this->ob04_idalvara)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ob04_idalvara"])){ 
          $sql  .= $virgula." ob04_idalvara = $this->ob04_idalvara ";
          $virgula = ",";
          if(trim($this->ob04_idalvara) == null ){ 
            $this->erro_sql = " Campo Id alvará não informado.";
            $this->erro_campo = "ob04_idalvara";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
          }
        }

        if ($ob04_codobra !== '' && $ob04_codobra !== null && $ob04_codobra !== 0) {
            $sql .= ' WHERE';
            $sql .= " ob04_codobra = {$this->ob04_codobra}";
        }

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ob04_codobra));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,5917,'$this->ob04_codobra','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_codobra"]) || $this->ob04_codobra != "")
             $resac = db_query("insert into db_acount values($acount,949,5917,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_codobra'))."','$this->ob04_codobra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_alvara"]) || $this->ob04_alvara != "")
             $resac = db_query("insert into db_acount values($acount,949,5918,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_alvara'))."','$this->ob04_alvara',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_data"]) || $this->ob04_data != "")
             $resac = db_query("insert into db_acount values($acount,949,5919,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_data'))."','$this->ob04_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_processo"]) || $this->ob04_processo != "")
             $resac = db_query("insert into db_acount values($acount,949,18640,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_processo'))."','$this->ob04_processo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_titularprocesso"]) || $this->ob04_titularprocesso != "")
             $resac = db_query("insert into db_acount values($acount,949,18641,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_titularprocesso'))."','$this->ob04_titularprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_dtprocesso"]) || $this->ob04_dtprocesso != "")
             $resac = db_query("insert into db_acount values($acount,949,18642,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_dtprocesso'))."','$this->ob04_dtprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_obsprocesso"]) || $this->ob04_obsprocesso != "")
             $resac = db_query("insert into db_acount values($acount,949,18643,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_obsprocesso'))."','$this->ob04_obsprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_dtvalidade"]) || $this->ob04_dtvalidade != "")
             $resac = db_query("insert into db_acount values($acount,949,18644,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_dtvalidade'))."','$this->ob04_dtvalidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_dataexpedicao"]) || $this->ob04_dataexpedicao != "")
             $resac = db_query("insert into db_acount values($acount,949,20461,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_dataexpedicao'))."','$this->ob04_dataexpedicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_classe"]) || $this->ob04_classe != "")
             $resac = db_query("insert into db_acount values($acount,949,1010575,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_classe'))."','$this->ob04_classe',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_ativo"]) || $this->ob04_ativo != "")
             $resac = db_query("insert into db_acount values($acount,949,1010576,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_ativo'))."','$this->ob04_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_datacancelamentoreativacao"]) || $this->ob04_datacancelamentoreativacao != "")
             $resac = db_query("insert into db_acount values($acount,949,1010577,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_datacancelamentoreativacao'))."','$this->ob04_datacancelamentoreativacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
             if (isset($GLOBALS["HTTP_POST_VARS"]["ob04_idalvara"]) || $this->ob04_idalvara != "")
             $resac = db_query("insert into db_acount values($acount,949,1014584,'".AddSlashes(pg_result($resaco,$conresaco,'ob04_idalvara'))."','$this->ob04_idalvara',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "alvara da obra não Alterado. Alteração Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "alvara da obra não foi Alterado. Alteração Executada.\\n";
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

    public function excluir($ob04_codobra=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ob04_codobra));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,5917,'$ob04_codobra','E')");
           $resac  = db_query("insert into db_acount values($acount,949,5917,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_codobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,5918,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_alvara'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,5919,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,18640,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,18641,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_titularprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,18642,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_dtprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,18643,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_obsprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,18644,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_dtvalidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,20461,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_dataexpedicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,1010575,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_classe'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,1010576,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,1010577,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_datacancelamentoreativacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,949,1014584,'','".AddSlashes(pg_result($resaco,$iresaco,'ob04_idalvara'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from obrasalvara
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ob04_codobra)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ob04_codobra = $ob04_codobra ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "alvara da obra não Excluído. Exclusão Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "alvara da obra não Encontrado. Exclusão não Efetuada.\\n";
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
        $this->erro_sql   = "Record Vazio na Tabela:obrasalvara";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ob04_codobra = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from obrasalvara ";
     $sql .= "      inner join obras  on  obras.ob01_codobra = obrasalvara.ob04_codobra";
     $sql .= "      inner join obrastiporesp  on  obrastiporesp.ob02_cod = obras.ob01_tiporesp";
     $sql .= "      inner join obrasresp     on obrasresp.ob10_codobra = obras.ob01_codobra       ";
     $sql .= "      inner join cgm           on cgm.z01_numcgm         = obrasresp.ob10_numcgm    ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob04_codobra)) {
         $sql2 .= " where obrasalvara.ob04_codobra = $ob04_codobra "; 
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

    public function sql_query_file($ob04_codobra = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from obrasalvara ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ob04_codobra)){
         $sql2 .= " where obrasalvara.ob04_codobra = $ob04_codobra "; 
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

   public function sql_query_obrasalvara($iCodigoObra) {
                                                                                                                        
    $sSql  = "select ob04_codobra,                                                                                                        ";
    $sSql .= "       ob04_alvara,                                                                                                         ";
    $sSql .= "       ob04_data,                                                                                                           ";
    $sSql .= "       ob04_processo,                                                                                                       ";
    $sSql .= "       ob04_titularprocesso,                                                                                                ";
    $sSql .= "       ob04_dtprocesso,                                                                                                     ";
    $sSql .= "       ob04_obsprocesso,                                                                                                    ";
    $sSql .= "       ob04_dataexpedicao,                                                                                                  ";
    $sSql .= "       ob04_dtvalidade,                                                                                                     ";
    $sSql .= "       ob04_ativo,                                                                                                          ";
    $sSql .= "       ob04_classe,                                                                                                         ";
    $sSql .= "       ob26_sequencial,                                                                                                     ";
    $sSql .= "       ob26_obrasalvara,                                                                                                    ";
    $sSql .= "       ob26_protprocesso,                                                                                                   ";
    $sSql .= "       case when ob26_protprocesso is null                                                                                  ";
    $sSql .= "         then false                                                                                                         ";
    $sSql .= "         else true                                                                                                          ";
    $sSql .= "       end as ob04_processosistema,                                                                                         ";
    $sSql .= "       p58_codproc,                                                                                                         ";
    $sSql .= "       p58_requer                                                                                                           ";
    $sSql .= "                                                                                                                            ";
    $sSql .= "  from obrasalvara                                                                                                          ";
    $sSql .= " inner join obras                    on obras.ob01_codobra                        = obrasalvara.ob04_codobra                ";
    $sSql .= " inner join obrastiporesp           on obrastiporesp.ob02_cod                   = obras.ob01_tiporesp                       ";
    $sSql .= "  left join obrasalvaraprotprocesso on obrasalvaraprotprocesso.ob26_obrasalvara = obrasalvara.ob04_codobra                  ";
    $sSql .= "  left join protprocesso            on protprocesso.p58_codproc                 = obrasalvaraprotprocesso.ob26_protprocesso ";
    $sSql .= " where ob04_codobra = {$iCodigoObra}                                                                                        ";
    
    return $sSql;
    
  }
   public function sql_query_txt ( $ob04_codobra=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from obrasalvara ";
     $sql .= "      inner join obras         on  obras.ob01_codobra         = obrasalvara.ob04_codobra";
     $sql .= "      inner join obrasconstr   on  obrasconstr.ob08_codobra   = obras.ob01_codobra";
     $sql .= "      inner join obrashabite   on  obrashabite.ob09_codconstr = obrasconstr.ob08_codconstr";
     $sql .= "      inner join obrastiporesp on  obrastiporesp.ob02_cod = obras.ob01_tiporesp";
     $sql .= "      inner join obraspropri   on ob03_codobra = ob04_codobra";
     $sql .= "      inner join cgm           on z01_numcgm  = obraspropri.ob03_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($ob04_codobra!=null ){
         $sql2 .= " where obrasalvara.ob04_codobra = $ob04_codobra "; 
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
   public function sql_queryobras ( $ob04_codobra=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from obrasalvara ";
     $sql .= "      inner join obras         on  obras.ob01_codobra         = obrasalvara.ob04_codobra";
     $sql .= "      inner join obrasconstr   on  obrasconstr.ob08_codobra   = obras.ob01_codobra";
     $sql2 = "";
     if($dbwhere==""){
       if($ob04_codobra!=null ){
         $sql2 .= " where obrasalvara.ob04_codobra = $ob04_codobra "; 
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
   * Busca obras conforme a matricula do imovel indicada
   * @param integer $iMatricula
   * @return string
   */
  public function sql_query_obrasCadastroImobiliario($iMatricula) {

    $sSql = "select ob01_codobra,                                                         ";
    $sSql.= "       ano_alvara,                                                           ";
    $sSql.= "       ob01_nomeobra,                                                        ";
    $sSql.= "       ob04_alvara,                                                          ";
    $sSql.= "       (ob08_area - area_ocupada) as ob08_area,                              ";
    $sSql.= "       ob08_codconstr,                                                       ";
    $sSql.= "       ob07_pavimentos,                                                      ";
    $sSql.= "       ob07_lograd,                                                          ";
    $sSql.= "       ob07_numero,                                                          ";
    $sSql.= "       ob07_compl,                                                           ";
    $sSql.= "       ob24_iptubase                                                         ";
    $sSql.= "  from (select distinct                                                      ";
    $sSql.= "               (select coalesce( sum(j39_area), '0')                         ";
    $sSql.= "                  from iptuconstrobrasconstr                                 ";
    $sSql.= "                       inner join iptuconstr   on j39_matric = j132_matric   ";
    $sSql.= "                                              and j39_idcons = j132_idconstr ";
    $sSql.= "                 where j132_obrasconstr = obrasconstr.ob08_codconstr         ";
    $sSql.= "               )                              as area_ocupada,               ";
    $sSql.= "               extract(year from ob04_data)   as ano_alvara,                 ";
    $sSql.= "               ob01_codobra,                                                 ";
    $sSql.= "               ob01_nomeobra,                                                ";
    $sSql.= "               ob04_alvara,                                                  ";
    $sSql.= "               ob08_area,                                                    ";
    $sSql.= "               ob08_codconstr,                                               ";
    $sSql.= "               coalesce(ob07_pavimentos, '0') as ob07_pavimentos,            ";
    $sSql.= "               ob07_lograd,                                                  ";
    $sSql.= "               ob07_numero,                                                  ";
    $sSql.= "               ob07_compl,                                                   ";
    $sSql.= "               c.ob24_iptubase                                               ";
    $sSql.= "        from iptubase a                                                      ";
    $sSql.= "             inner join lote           on j34_idbql      = a.j01_idbql       ";
    $sSql.= "             inner join iptubase b     on b.j01_idbql    = lote.j34_idbql    ";
    $sSql.= "             inner join obrasiptubase c  on c.ob24_iptubase  = b.j01_matric  ";
    $sSql.= "             inner join obrasalvara    on ob04_codobra   = c.ob24_obras      ";
    $sSql.= "             inner join obras          on ob01_codobra   = c.ob24_obras      ";
    $sSql.= "               inner join obrasconstr    on ob08_codobra   = ob01_codobra    ";
    $sSql.= "               inner join obrasender     on ob07_codobra   = ob08_codobra    ";
    $sSql.= "                                        and ob07_codconstr = ob08_codconstr  ";
    $sSql.= "               inner join obrasiptubase d on d.ob24_obras     = ob01_codobra ";
    $sSql.= "               inner join iptubase e     on e.j01_matric   = d.ob24_iptubase ";
    $sSql.= "               inner join iptubase f     on f.j01_idbql    = e.j01_idbql     ";
    $sSql.= "         where a.j01_matric = {$iMatricula}                                  ";
    $sSql.= "        ) as query                                                           ";
    $sSql.= "  where (ob08_area - area_ocupada) > 0                                       ";


    return $sSql;
  }
   public function sql_query_obrasalvaras_relatorio($sCampos = "*", $sWhere, $lHabite = null, $sOrderBy = null) {
  	
    $sSql  = "select distinct {$sCampos}                                                                  \n";
    $sSql .= "  from obrasalvara                                                                    \n";
    $sSql .= " inner join obrasconstr   on obrasconstr  .ob08_codobra = obrasalvara.ob04_codobra    \n";
    $sSql .= " inner join obraspropri   on obraspropri  .ob03_codobra = obrasalvara.ob04_codobra    \n";
    $sSql .= " inner join obrasender    on obrasender.ob07_codconstr  = obrasconstr.ob08_codconstr  \n";
    $sSql .= " inner join cgm           on cgm          .z01_numcgm   = obraspropri.ob03_numcgm     \n";
    $sSql .= " left  join obrasiptubase on obrasiptubase.ob24_obras   = obrasalvara.ob04_codobra    \n";
    $sSql .= " left  join obraslote     on obraslote    .ob05_codobra = obrasalvara.ob04_codobra    \n";
    $sSql .= " left  join lote          on lote         .j34_idbql    = obraslote  .ob05_idbql      \n";
    $sSql .= " left  join setor         on setor        .j30_codi     = lote       .j34_setor       \n";  

    if(is_bool($lHabite)) {
    	if ($lHabite) {
    		
    		$sSql .= " inner join ";
    		
    	} else {
    		
    		$sSql .= " left join ";
    		
    	}
    	
    	$sSql .= " obrashabite on obrashabite.ob09_codconstr = obrasconstr.ob08_codconstr \n"; 
    	
    } 
    
    if ($sWhere != '') {
    	
    	$sSql .= "where {$sWhere} ";
    	
    }
    
    if (!is_null($sOrderBy)) {
    	
    	$sSql .= " order by {$sOrderBy} "; 
    	
    }
  	
    return $sSql;
  	
  }
   public function sql_query_cartaAlvara($sCampos, $iCodigoObra) {
  
  	$sSql  = "select {$sCampos}                                                                                                           ";
  	$sSql .= "  from obrasalvara                                                                                                          ";
  	$sSql .= " inner join obras         	        on obras.ob01_codobra          		          = obrasalvara.ob04_codobra                  ";
  	$sSql .= " inner join obrastiporesp 	        on obrastiporesp.ob02_cod      		          = obras.ob01_tiporesp                       ";
  	$sSql .= " inner join obrasresp               on obrasresp.ob10_codobra      		          = obras.ob01_codobra                        ";
  	$sSql .= " inner join cgm           	        on cgm.z01_numcgm              	            = obrasresp.ob10_numcgm                     ";
  	$sSql .= " left  join obrasiptubase 	        on obrasiptubase.ob24_obras    		          = obras.ob01_codobra                        ";
  	$sSql .= " left  join iptubase 	              on iptubase.j01_matric       		            = obrasiptubase.ob24_iptubase               ";
  	$sSql .= " left  join obrasalvaraprotprocesso on obrasalvaraprotprocesso.ob26_obrasalvara = obras.ob01_codobra                        ";
  	$sSql .= " left  join protprocesso            on protprocesso.p58_codproc                 = obrasalvaraprotprocesso.ob26_protprocesso ";
  	$sSql .= " left  join loteloc                 on loteloc.j06_idbql                        = iptubase.j01_idbql                        ";
  	$sSql .= " left  join setorloc                on setorloc.j05_codigo                      = loteloc.j06_setorloc                      ";
  	$sSql .= " where obras.ob01_codobra = {$iCodigoObra}                                                                                  ";
  
  
  	return $sSql;
  	 
  }

   public function sql_query_relatorioObrasAlvara( $sWhere, $sCampos = "*", $sOrderBy = null ) {
    
    if ( !empty($sWhere) ) {
      $sWhere = " where " . $sWhere;
    }
    $sSql = " select {$sCampos}                                                                                      \n";     
    $sSql.= "   from obrasalvara                                                                                     \n";
    $sSql.= "        inner join obras                  on obras.ob01_codobra           = obrasalvara.ob04_codobra    \n";
    $sSql.= "        inner join obrasconstr            on obrasconstr.ob08_codobra     = obras.ob01_codobra          \n";
    $sSql.= "        inner join obrasender             on obrasender.ob07_codconstr    = obrasconstr.ob08_codconstr  \n";
    $sSql.= "                                         and obrasender.ob07_codobra      = obrasconstr.ob08_codobra    \n";
    $sSql.= "        inner join bairro                 on bairro.j13_codi              = obrasender.ob07_bairro      \n";
    $sSql.= "        left  join ruas                   on ruas.j14_codigo              = obrasender.ob07_lograd      \n";
    $sSql.= "        inner join obrastiporesp          on obrastiporesp.ob02_cod       = obras.ob01_tiporesp         \n";
    $sSql.= "        inner join obrasresp              on obrasresp.ob10_codobra       = obras.ob01_codobra          \n";
    $sSql.= "        inner join obrastecnicos          on obrastecnicos.ob20_codobra   = obras.ob01_codobra          \n";
    $sSql.= "        inner join obrastec               on obrastec.ob15_sequencial     = obrastecnicos.ob20_obrastec \n";
    $sSql.= "        inner join cgm as cgm_responsavel on cgm_responsavel.z01_numcgm   = obrasresp.ob10_numcgm       \n";
    $sSql.= "        inner join cgm as cgm_tecnico     on cgm_tecnico.z01_numcgm       = obrastec.ob15_numcgm        \n";
    $sSql.= " {$sWhere}                                                                                              \n";
    
    if ( !empty($sOrderBy) ) {
      $sSql .=" order by {$sOrderBy}    ";
    }
    return $sSql;  
  }
   /**
   * Incluir obraalvara setando alvara, o metodo incluir pega nextval ou last_val
   * 
   * @param integer $ob04_codobra
   * @return boolean
   */
  public function incluirAlvara ($ob04_codobra){
  	$this->atualizacampos();
  	 
  	if($this->ob04_data == null ){
  		$this->erro_sql = " Campo Data do alvará nao Informado.";
  		$this->erro_campo = "ob04_data_dia";
  		$this->erro_banco = "";
  		$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
  		$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
  		$this->erro_status = "0";
  		return false;
  	}
  	if($this->ob04_dtprocesso == null ){
  		$this->ob04_dtprocesso = "null";
  	}
  	if($this->ob04_dtvalidade == null ){
  		$this->ob04_dtvalidade = "null";
  	}

    if(empty($this->ob04_alvara)){
      $res = db_query("SELECT max(ob04_alvara) as alvara FROM obrasalvara");
      $maxAlvara = db_utils::fieldsMemory($res, 0)->alvara;
      $alvara = (int)$maxAlvara + 1;

      if($res==false){
        
        $this->erro_banco = str_replace("\n","",@pg_last_error());
        $this->erro_sql   = "Verifique o cadastro do campo: ob04_alvara";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }

      $this->ob04_alvara = $alvara;
    }

  	/**
  	 * Valida se código do alvará ja está cadastrado
  	 */
    if(!empty($this->ob04_alvara)) {

      $anoAlvara = intval(substr($this->ob04_data, 0, 4));

    	$rsAlvara = db_query("select ob04_codobra, ob04_data from obrasalvara where ob04_alvara = {$this->ob04_alvara} 
                            and date_part('year', ob04_data) = {$anoAlvara}");
    	if (pg_num_rows($rsAlvara) > 0) {
    		
    		$this->erro_msg    = "Código do alvará já registrado para a obra ". pg_result($rsAlvara,0,0);
    		$this->erro_status = "0";
    		return false;
    	}
    }
  	
  	if(($this->ob04_codobra == null) || ($this->ob04_codobra == "") ){
  		$this->erro_sql = " Campo ob04_codobra nao declarado.";
  		$this->erro_banco = "Chave Primaria zerada.";
  		$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
  		$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
  		$this->erro_status = "0";
  		return false;
  	}

    if ($this->ob04_idalvara === '' || $this->ob04_idalvara === null || $this->ob04_idalvara === 0) {
      $result = db_query("select nextval('obrasalvara_ob04_idalvara_seq')"); 
      if (!$result) {
          $this->erro_banco = str_replace("\n", "", @pg_last_error());
          $this->erro_sql = "Verifique o cadastro da sequencia: obrasalvara_ob04_idalvara_seq do campo: ob04_idalvara"; 
          $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
          $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
          $this->erro_status = "0";
          return false;
      }
      $this->ob04_idalvara = pg_result($result, 0, 0);
    } else {
        $result = db_query("SELECT last_value FROM obrasalvara_ob04_idalvara_seq");
        if ($result && pg_result($result, 0, 0) < $this->ob04_idalvara) {
            $this->erro_sql = " Campo ob04_idalvara maior que último número da sequencia.";
            $this->erro_banco = "Sequencia menor que este número.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        } else {
            $this->ob04_idalvara = $this->ob04_idalvara;
        }
    }

  	$sql = "insert into obrasalvara(ob04_codobra,
  	                                ob04_alvara,
  	                                ob04_data,
  	                                ob04_processo,
  	                                ob04_titularprocesso,
  	                                ob04_dtprocesso,
  	                                ob04_obsprocesso,
  	                                ob04_dtvalidade,
                                    ob04_dataexpedicao,
                                    ob04_datacancelamentoreativacao,
                                    ob04_ativo,
                                    ob04_classe,
                                    ob04_idalvara)
                  values ($this->ob04_codobra,
                          $this->ob04_alvara,
                          ".($this->ob04_data == "null" || $this->ob04_data == ""?"null":"'".$this->ob04_data."'").",
                          '$this->ob04_processo',
                          '$this->ob04_titularprocesso',
                          ".($this->ob04_dtprocesso == "null" || $this->ob04_dtprocesso == ""?"null":"'".$this->ob04_dtprocesso."'").",
                          '$this->ob04_obsprocesso',
                          ".($this->ob04_dtvalidade == "null" || $this->ob04_dtvalidade == ""?"null":"'".$this->ob04_dtvalidade."'").",
                          ".($this->ob04_dataexpedicao == "null" || $this->ob04_dataexpedicao == ""?"null":"'".$this->ob04_dataexpedicao."'").",
                          ".($this->ob04_datacancelamentoreativacao == "null" || $this->ob04_datacancelamentoreativacao == ""?"null":"'".$this->ob04_datacancelamentoreativacao."'").",
                          " . ($this->ob04_ativo === null || $this->ob04_ativo === '' ? 'NULL' : ($this->ob04_ativo ? 'TRUE' : 'FALSE')) . ",
                          ". ($this->ob04_classe === null || $this->ob04_classe === '' ? 'NULL' : "'{$this->ob04_classe}'") . ",
                          $this->ob04_idalvara)";

  	$result = db_query($sql);
  	if($result==false){
  		$this->erro_banco = str_replace("\n","",@pg_last_error());
  		if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
  			$this->erro_sql   = "alvara da obra ($this->ob04_codobra) nao Incluído. Inclusao Abortada.";
  			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
  			$this->erro_banco = "alvara da obra já Cadastrado";
  			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
  		}else{
  			$this->erro_sql   = "alvara da obra ($this->ob04_codobra) nao Incluído. Inclusao Abortada.";
  			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
  			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
  		}
  		$this->erro_status = "0";
  		$this->numrows_incluir= 0;
  		return false;
  	}
  	$this->erro_banco = "";
  	$this->erro_sql = "Inclusao efetuada com Sucesso\\n";
  	$this->erro_sql .= "Valores : ".$this->ob04_codobra;
  	$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
  	$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
  	$this->erro_status = "1";
  	$this->numrows_incluir= pg_affected_rows($result);
  	$resaco = $this->sql_record($this->sql_query_file($this->ob04_codobra));
  	if(($resaco!=false)||($this->numrows!=0)){
  		$resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
  		$acount = pg_result($resac,0,0);
  		$resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
  		$resac = db_query("insert into db_acountkey values($acount,5917,'$this->ob04_codobra','I')");
  		$resac = db_query("insert into db_acount values($acount,949,5917,'','".AddSlashes(pg_result($resaco,0,'ob04_codobra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  		$resac = db_query("insert into db_acount values($acount,949,5918,'','".AddSlashes(pg_result($resaco,0,'ob04_alvara'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  		$resac = db_query("insert into db_acount values($acount,949,5919,'','".AddSlashes(pg_result($resaco,0,'ob04_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  		$resac = db_query("insert into db_acount values($acount,949,18640,'','".AddSlashes(pg_result($resaco,0,'ob04_processo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  		$resac = db_query("insert into db_acount values($acount,949,18641,'','".AddSlashes(pg_result($resaco,0,'ob04_titularprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  		$resac = db_query("insert into db_acount values($acount,949,18642,'','".AddSlashes(pg_result($resaco,0,'ob04_dtprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  		$resac = db_query("insert into db_acount values($acount,949,18643,'','".AddSlashes(pg_result($resaco,0,'ob04_obsprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  		$resac = db_query("insert into db_acount values($acount,949,18644,'','".AddSlashes(pg_result($resaco,0,'ob04_dtvalidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
      $resac = db_query("insert into db_acount values($acount,949,1014584,'','".AddSlashes(pg_result($resaco,0,'ob04_idalvara'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
  	}
  	return true;
  }

  public function sql_query_cartaAlvaraDadosTemplate($sCampos, $iCodigoObra) {
  
  	$sSql  = "select {$sCampos} ";
    $sSql .= "   from ( select x.*,";
    $sSql .= "            case ";
    $sSql .= "            when (anos > 0  and meses > 0 and dias > 0) then ";
    $sSql .= "              anos || string_ano || ', ' || meses || string_mes || 'e ' || dias || string_dia ";
    $sSql .= "            when (anos > 0  and meses > 0 and dias = 0)    then ";
    $sSql .= "              anos || string_ano || ' e ' || meses ";
    $sSql .= "            when (anos = 0  and meses > 0 and dias > 0)    then ";
    $sSql .= "              meses || string_mes || 'e ' || dias || string_dia ";
    $sSql .= "            when (anos > 0  and meses = 0 and dias > 0)    then ";
    $sSql .= "              anos || string_ano || 'e ' || dias || string_dia ";
    $sSql .= "            when (anos > 0  and meses = 0 and dias = 0)       then ";
    $sSql .= "              anos || string_ano ";
    $sSql .= "            when (anos = 0  and meses > 0 and dias = 0)       then ";
    $sSql .= "              meses || string_mes ";
    $sSql .= "            when (anos = 0  and meses = 0 and dias > 0)       then ";
    $sSql .= "              dias || string_dia ";
    $sSql .= "            else '0' ";
    $sSql .= "             end as validade_alvara ";
    $sSql .= "            from (select obraspropri.ob03_numcgm           as cgm, ";
    $sSql .= "                         acgm.z01_nome                     as nome_proprietario, ";
    $sSql .= "                         (select STRING_AGG(cgm.z01_nome, ', ') from projetos.obrasoutrosprop inner join protocolo.cgm on ob32_numcgm = z01_numcgm where ob32_codobra = {$iCodigoObra}) as nome_outros_proprietarios, ";
    $sSql .= "                         acgm.z01_cgccpf                   as cpf_cnpj_proprietario, ";
    $sSql .= "                         acgm.z01_ender                    as logradouro, ";
    $sSql .= "                         acgm.z01_numero                   as numero, ";
    $sSql .= "                         acgm.z01_compl                    as complemento, ";
		$sSql .= "                         acgm.z01_bairro                   as bairro, ";
		$sSql .= "                         bcgm.z01_numcgm                   as cgm_resp_projeto, ";
		$sSql .= "                         bcgm.z01_cgccpf                   as cpf_resp_projeto, ";
		$sSql .= "                         bcgm.z01_nome                     as nome_resp_projeto, ";
		$sSql .= "                         bobrastec.ob15_crea               as crea_resp_projeto, ";
		$sSql .= "                         aobrastecprofissao.ob30_descricao as prof_resp_projeto, ";
		$sSql .= "                         ccgm.z01_numcgm                   as cgm_resp_acomp_obra, ";
		$sSql .= "                         ccgm.z01_cgccpf                   as cpf_resp_acomp_obra, ";
		$sSql .= "                         ccgm.z01_nome                     as nome_resp_acomp_obra, ";
		$sSql .= "                         cobrastec.ob15_crea               as crea_resp_acomp_obra, ";
		$sSql .= "                         bobrastecprofissao.ob30_descricao as prof_resp_acomp_obra, ";
    $sSql .= "                         lote.j34_setor || '-' || setor.j30_descr || '/' || lote.j34_quadra || '/' || lote.j34_lote as sql, ";
    $sSql .= "                         setorloc.j05_codigoproprio || '-' || setorloc.j05_descr || '/' || loteloc.j06_quadraloc  || '/' || loteloc.j06_lote as pql, ";
    $sSql .= "                         setorloc.j05_codigoproprio || '-' || setorloc.j05_descr as setor_pql, ";
    $sSql .= "                         loteloc.j06_quadraloc    as quadra_pql, ";
    $sSql .= "                         loteloc.j06_lote as lote_pql, ";
    $sSql .= "                         iptubase.j01_matric      as matricula_imovel, ";
    $sSql .= "                         obrasalvara.ob04_codobra as cod_obra, ";
    $sSql .= "                         obrasalvara.ob04_alvara  as seq_alvara, ";
    $sSql .= "                         obrasalvara.ob04_alvara  as sequencial_alvara, ";
    $sSql .= "                         fc_dataextenso(coalesce(obrasalvara.ob04_dataexpedicao, current_date)) as data_expedicao_extenso, ";
    $sSql .= "                         extract (year from obrasalvara.ob04_data)    as ano_sequencial_alvara, ";
    $sSql .= "                         to_char(obrasalvara.ob04_data,'dd/mm/yyyy' ) as data_inicio_alvara, ";
    $sSql .= "                         to_char(obrasalvara.ob04_dtvalidade, 'dd/mm/yyyy') as data_final_alvara, ";
    $sSql .= "                         to_char(obrasalvara.ob04_data,'dd/mm/yyyy' )       as data_aprovacao, ";
    $sSql .= "                         engenheiro.z01_nome                                as engenheiro, ";
    $sSql .= "                         aobrastec.ob15_crea                                as crea, ";
    $sSql .= "                         case ";
    $sSql .= "                         when ob26_sequencial is not null then ";
    $sSql .= "                               to_char(protprocesso.p58_codproc,'9999999999') ";
    $sSql .= "                             else ob04_processo ";
    $sSql .= "                           end                                                as protocolo, ";
    $sSql .= "                         to_char(case ";
    $sSql .= "                                 when ob26_sequencial is not null then ";
    $sSql .= "                                   protprocesso.p58_dtproc ";
    $sSql .= "                                 else ob04_dtprocesso ";
    $sSql .= "                                 end, 'dd/mm/yyyy')                         as data_protocolo, ";
    $sSql .= "                         obrasconstr.ob08_area                              as area_total, ";
    $sSql .= "                         obrasender.ob07_areaatual                                      as area_total_atual, ";
    $sSql .= "                         obrasender.ob07_unidades                                       as unidade, ";
    $sSql .= "                         obrasender.ob07_pavimentos                                     as pavimentos, ";
    $sSql .= "                         j88_sigla || ' ' || ruas.j14_nome                              as endereco_obra, ";
    $sSql .= "                         obrasender.ob07_numero                                         as numero_endereco_obra, ";
    $sSql .= "                         obrasender.ob07_compl                                          as complemento_endereco_obra, ";
    $sSql .= "                         bairro.j13_descr                                               as bairro_endereco_obra, ";
    $sSql .= "                         (select z01_nome ";
    $sSql .= "                            from db_usuacgm ";
    $sSql .= "                           inner join cgm on cgm.z01_numcgm = db_usuacgm.cgmlogin ";
    $sSql .= "                           where db_usuacgm.id_usuario = ".db_getsession('DB_id_usuario').")               as nome_servidor, ";
    $sSql .= "                         (select rh37_descr ";
    $sSql .= "                            from rhpessoal ";
    $sSql .= "                           inner join rhfuncao    on rhpessoal.rh01_funcao = rhfuncao.rh37_funcao ";
    $sSql .= "                                                 and rhfuncao.rh37_instit  = ".db_getsession('DB_instit');
    $sSql .= "                           inner join db_usuacgm  on db_usuacgm.cgmlogin   = rhpessoal.rh01_numcgm ";
    $sSql .= "                                                 and db_usuacgm.id_usuario = ".db_getsession('DB_id_usuario');
    $sSql .= "                           left join pessoal.rhpessoalmov  on rh02_regist = rh01_regist ";
    $sSql .= "                                                          and rh02_anousu = fc_anofolha(".db_getsession('DB_instit')."::integer) ";
    $sSql .= "                                                          and rh02_mesusu = fc_mesfolha(".db_getsession('DB_instit')."::integer) ";
    $sSql .= "                                                          and rh02_instit = ".db_getsession('DB_instit');
    $sSql .= "                           left join pessoal.rhpesrescisao on rh02_seqpes = rh05_seqpes ";
    $sSql .= "                          where rh05_seqpes is null ";
    $sSql .= "                          order by rh01_regist desc limit 1 ";
    $sSql .= "                          )   as cargo_servidor, ";
    $sSql .= "                         (select rh01_regist ";
    $sSql .= "                            from rhpessoal ";
    $sSql .= "                               inner join db_usuacgm  on db_usuacgm.cgmlogin   = rhpessoal.rh01_numcgm ";
    $sSql .= "                                                     and db_usuacgm.id_usuario = ".db_getsession('DB_id_usuario');
    $sSql .= "                                left join pessoal.rhpessoalmov  on rh02_regist = rh01_regist ";
    $sSql .= "                                                               and rh02_anousu = fc_anofolha(".db_getsession('DB_instit')."::integer) ";
    $sSql .= "                                                               and rh02_mesusu = fc_mesfolha(".db_getsession('DB_instit')."::integer) ";
    $sSql .= "                                                               and rh02_instit = ".db_getsession('DB_instit');
    $sSql .= "                                left join pessoal.rhpesrescisao on rh02_seqpes = rh05_seqpes ";
    $sSql .= "                             where rh05_seqpes is null ";
    $sSql .= "                             order by rh01_regist desc limit 1 ";
    $sSql .= "                          )  as matricula_servidor, ";
    $sSql .= "                          ob04_obsprocesso     as observacoes, ";
    $sSql .= "                          a.j31_descr          as carac_ocupacao, ";
    $sSql .= "                          b.j31_descr          as carac_tipo_construcao, ";
    $sSql .= "                          c.j31_descr          as carac_tipo_lancamento, ";
    $sSql .= "                          extract (day from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data))   as dias, ";
    $sSql .= "                          extract (month from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data)) as meses, ";
    $sSql .= "                          extract (year from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data))  as anos, ";
    $sSql .= "                          case ";
    $sSql .= "                            when extract (day from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data)) = 1 then ";
    $sSql .= "                              ' dia ' ";
    $sSql .= "                            when extract (day from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data)) > 1 then ";
    $sSql .= "                              ' dias ' ";
    $sSql .= "                          end as string_dia, ";
    $sSql .= "                          case ";
    $sSql .= "                            when extract (month from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data)) = 1 then ";
    $sSql .= "                              ' mês ' ";
    $sSql .= "                            when extract (month from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data)) > 1 then ";
    $sSql .= "                              ' meses ' ";
    $sSql .= "                          end as string_mes, ";
    $sSql .= "                          case ";
    $sSql .= "                            when extract (year from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data)) = 1 then ";
    $sSql .= "                              ' ano ' ";
    $sSql .= "                            when extract (year from age(obrasalvara.ob04_dtvalidade, obrasalvara.ob04_data)) > 1 then ";
    $sSql .= "                              ' anos ' ";
    $sSql .= "                          end as string_ano, ";
    $sSql .= "                          to_char(ob04_dataexpedicao,'dd/mm/yyyy') as data_expedicao, ";
    $sSql .= "                          obras.ob01_nomeobra                      as nome_obra, ";
    $sSql .= "                          case when trim(coalesce(obras.ob01_numeroartprojeto, '0')) != '0' then ";
    $sSql .= "                                    obras.ob01_numeroartprojeto ";
    $sSql .= "                               when trim(coalesce(obras.ob01_numerorrtprojeto, '0')) != '0' then ";
    $sSql .= "                                    obras.ob01_numerorrtprojeto ";
    $sSql .= "                               else '' ";
    $sSql .= "                          end                                                             as art_rrt_responsavel_projeto, ";
    $sSql .= "                          case when trim(coalesce(obras.ob01_numeroarttecnico, '0')) != '0' then ";
    $sSql .= "                                    obras.ob01_numeroarttecnico ";
    $sSql .= "                               when trim(coalesce(obras.ob01_numerorrttecnico, '0')) != '0' then ";
    $sSql .= "                                    obras.ob01_numerorrttecnico ";
    $sSql .= "                               else '' ";
    $sSql .= "                          end                                                             as art_rrt_responsavel_tecnico, ";
    $sSql .= "                          case when obrasresp_cgm.z01_numcgm is not null then ";
    $sSql .= "                                    obrasresp_cgm.z01_numcgm::varchar ";
    $sSql .= "                               else '' ";
    $sSql .= "                          end                                                             as cgm_responsavel_execucao, ";
    $sSql .= "                          case when obrasresp_cgm.z01_nome is not null then ";
    $sSql .= "                                    obrasresp_cgm.z01_nome::varchar ";
    $sSql .= "                               else '' ";
    $sSql .= "                          end                                                             as nome_responsavel_execucao, ";
    $sSql .= "                          case when obrasresp_cgm.z01_cgccpf is not null then ";
    $sSql .= "                                    obrasresp_cgm.z01_cgccpf::varchar ";
    $sSql .= "                               else '' ";
    $sSql .= "                          end                                                             as cpf_responsavel_execucao ";
    $sSql .= "                     from obrasalvara ";
    $sSql .= "                        inner join obras                                    on obras.ob01_codobra          = obrasalvara.ob04_codobra ";
    $sSql .= "                        inner join obrasconstr                              on obrasconstr.ob08_codobra    = obrasalvara.ob04_codobra ";
    $sSql .= "                         left join obrasiptubase                            on obrasiptubase.ob24_obras    = obras.ob01_codobra ";
    $sSql .= "                         left join iptubase                                 on iptubase.j01_matric         = obrasiptubase.ob24_iptubase ";
    $sSql .= "                        left join obraspropri                              on obraspropri.ob03_codobra    = obras.ob01_codobra ";
    $sSql .= "                        left join cgm    acgm                              on acgm.z01_numcgm             = obraspropri.ob03_numcgm ";
    $sSql .= "                        left join lote                                     on lote.j34_idbql              = iptubase.j01_idbql ";
    $sSql .= "                        left join setor                                    on setor.j30_codi              = lote.j34_setor ";
    $sSql .= "                         left join loteloc                                  on loteloc.j06_idbql           = iptubase.j01_idbql ";
    $sSql .= "                         left join setorloc                                 on setorloc.j05_codigo         = loteloc.j06_setorloc ";
		$sSql .= "            inner join obrastecnicos                            on obrastecnicos.ob20_codobra  = obras.ob01_codobra ";
    $sSql .= "                        inner join obrastec aobrastec                       on aobrastec.ob15_sequencial   = obrastecnicos.ob20_obrastec ";
		$sSql .= "            inner join cgm engenheiro                           on engenheiro.z01_numcgm       = aobrastec.ob15_numcgm ";
		$sSql .= "            inner join obrastec as bobrastec                    on ob01_responsavelprojeto     = bobrastec.ob15_sequencial ";
		$sSql .= "            inner join obrastecprofissao as aobrastecprofissao  on bobrastec.ob15_profissao    = aobrastecprofissao.ob30_sequencial ";
		$sSql .= "            inner join cgm as bcgm                              on bobrastec.ob15_numcgm       = bcgm.z01_numcgm ";
		$sSql .= "            inner join obrastec as cobrastec                    on ob01_arquitetoobra          = cobrastec.ob15_sequencial ";
		$sSql .= "            inner join obrastecprofissao as bobrastecprofissao  on cobrastec.ob15_profissao    = bobrastecprofissao.ob30_sequencial ";
		$sSql .= "            inner join cgm as ccgm                              on cobrastec.ob15_numcgm       = ccgm.z01_numcgm ";
		$sSql .= "	     left join obrasalvaraprotprocesso                  on ob26_obrasalvara            = obrasalvara.ob04_codobra ";
		$sSql .= "	     left join protprocesso                             on protprocesso.p58_codproc    = obrasalvaraprotprocesso.ob26_protprocesso ";
		$sSql .= "	    inner join obrasender                               on obrasender.ob07_codconstr   = obrasconstr.ob08_codconstr ";
		$sSql .= "	    inner join ruas                                     on ruas.j14_codigo             = obrasender.ob07_lograd ";
		$sSql .= "	    inner join ruastipo                                 on j14_tipo                    = j88_codigo ";
		$sSql .= "	    inner join bairro                                   on bairro.j13_codi             = obrasender.ob07_bairro ";
		$sSql .= "	    inner join caracter a                               on a.j31_codigo                = obrasconstr.ob08_ocupacao ";
		$sSql .= "	    inner join caracter b                               on b.j31_codigo                = obrasconstr.ob08_tipoconstr ";
		$sSql .= "	    inner join caracter c                               on c.j31_codigo                = obrasconstr.ob08_tipolanc ";
    $sSql .= "      left join obrasresp                                 on obras.ob01_codobra          = obrasresp.ob10_codobra ";
    $sSql .= "      left join cgm obrasresp_cgm                         on obrasresp.ob10_numcgm       = obrasresp_cgm.z01_numcgm ";
		$sSql .= "	    where obrasalvara.ob04_codobra = {$iCodigoObra}) as x ) as alvara";                                                                            
    $sSql .= "	    group by sequencial_alvara,
                              ano_sequencial_alvara,
                              validade_alvara,
                              data_inicio_alvara,
                              data_final_alvara,
                              cgm,
                              nome_proprietario,
                              nome_outros_proprietarios,
                              cpf_cnpj_proprietario,
                              matricula_imovel,
                              data_expedicao_extenso, 
                              logradouro,
                              numero,
                              bairro,
                              complemento,
                              sql,
                              pql,
                              setor_pql,
                              quadra_pql,
                              lote_pql,
                              cod_obra,
                              engenheiro,
                              seq_alvara,
                              crea,
                              area_total,
                              area_total_atual,
                              unidade,
                              pavimentos,
                              protocolo,
                              data_protocolo,
                              data_aprovacao,
                              endereco_obra,
                              numero_endereco_obra,
                              bairro_endereco_obra,
                              complemento_endereco_obra,
                              nome_servidor,
                              cargo_servidor,
                              matricula_servidor,
                              carac_ocupacao,
                              carac_tipo_construcao,
                              carac_tipo_lancamento,
                              observacoes,
                              data_expedicao,
                              cgm_resp_projeto,
                              cpf_resp_projeto,
                              nome_resp_projeto,
                              crea_resp_projeto,
                              prof_resp_projeto,
                              cgm_resp_acomp_obra,
                              cpf_resp_acomp_obra,
                              nome_resp_acomp_obra,
                              crea_resp_acomp_obra,
                              prof_resp_acomp_obra,
                              nome_obra,
                              art_rrt_responsavel_projeto,
                              art_rrt_responsavel_tecnico,
                              cgm_responsavel_execucao,
                              nome_responsavel_execucao,
                              cpf_responsavel_execucao";                                                                            "; ";
  
  	return $sSql; 	 
  }
}
