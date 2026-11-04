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

class cl_rhcargo
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
    public $rh04_instit = 0;
    public $rh04_codigo = 0;
    public $rh04_descr = null;
    public $rh04_datafinal_dia = null;
    public $rh04_datafinal_mes = null;
    public $rh04_datafinal_ano = null;
    public $rh04_datafinal = null;
    public $rh04_datainicial_dia = null;
    public $rh04_datainicial_mes = null;
    public $rh04_datainicial_ano = null;
    public $rh04_datainicial = null;
    public $rh04_descricaoatividades = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 rh04_instit = int4 = Cod. Instituição
                 rh04_codigo = int4 = Código da função
                 rh04_descr = varchar(40) = Descrição da função
                 rh04_datafinal = date = Data Final
                 rh04_datainicial = date = Data Inicial
                 rh04_descricaoatividades = text = Atividades desempenhadas
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("rhcargo");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if($retorna==true){
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if($exclusao==false){
            $this->rh04_instit = ($this->rh04_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_instit"]:$this->rh04_instit);
            $this->rh04_codigo = ($this->rh04_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_codigo"]:$this->rh04_codigo);
            $this->rh04_descr = ($this->rh04_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_descr"]:$this->rh04_descr);
            if($this->rh04_datafinal == ""){
                $this->rh04_datafinal_dia = ($this->rh04_datafinal_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_datafinal_dia"]:$this->rh04_datafinal_dia);
                $this->rh04_datafinal_mes = ($this->rh04_datafinal_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_datafinal_mes"]:$this->rh04_datafinal_mes);
                $this->rh04_datafinal_ano = ($this->rh04_datafinal_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_datafinal_ano"]:$this->rh04_datafinal_ano);
                if($this->rh04_datafinal_dia != ""){
                    $this->rh04_datafinal = $this->rh04_datafinal_ano."-".$this->rh04_datafinal_mes."-".$this->rh04_datafinal_dia;
                }
            }
            if($this->rh04_datainicial == ""){
                $this->rh04_datainicial_dia = ($this->rh04_datainicial_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_datainicial_dia"]:$this->rh04_datainicial_dia);
                $this->rh04_datainicial_mes = ($this->rh04_datainicial_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_datainicial_mes"]:$this->rh04_datainicial_mes);
                $this->rh04_datainicial_ano = ($this->rh04_datainicial_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_datainicial_ano"]:$this->rh04_datainicial_ano);
                if($this->rh04_datainicial_dia != ""){
                    $this->rh04_datainicial = $this->rh04_datainicial_ano."-".$this->rh04_datainicial_mes."-".$this->rh04_datainicial_dia;
                }
            }
            $this->rh04_descricaoatividades = ($this->rh04_descricaoatividades == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_descricaoatividades"]:$this->rh04_descricaoatividades);
        }else{
            $this->rh04_instit = ($this->rh04_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_instit"]:$this->rh04_instit);
            $this->rh04_codigo = ($this->rh04_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["rh04_codigo"]:$this->rh04_codigo);
        }
    }

    public function incluir($rh04_codigo,$rh04_instit)
    {
        $this->atualizacampos();
        if($this->rh04_descr == null ){
            $this->erro_sql = " Campo Descrição da função não informado.";
            $this->erro_campo = "rh04_descr";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->rh04_datafinal == null ){
            $this->rh04_datafinal = "null";
        }
        if($this->rh04_datainicial == null ){
            $this->rh04_datainicial = "null";
        }
        if($rh04_codigo == "" || $rh04_codigo == null ){
            $result = db_query("select nextval('rhcargo_rh04_codigo_seq')");
            if($result==false){
                $this->erro_banco = str_replace("\n","",@pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: rhcargo_rh04_codigo_seq do campo: rh04_codigo";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->rh04_codigo = pg_result($result,0,0);
        }else{
            $result = db_query("select last_value from rhcargo_rh04_codigo_seq");
            if(($result != false) && (pg_result($result,0,0) < $rh04_codigo)){
                $this->erro_sql = " Campo rh04_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }else{
                $this->rh04_codigo = $rh04_codigo;
            }
        }
        if(($this->rh04_codigo == null) || ($this->rh04_codigo == "") ){
            $this->erro_sql = " Campo rh04_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if(($this->rh04_instit == null) || ($this->rh04_instit == "") ){
            $this->erro_sql = " Campo rh04_instit não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into rhcargo(
                                       rh04_instit
                                      ,rh04_codigo
                                      ,rh04_descr
                                      ,rh04_datafinal
                                      ,rh04_datainicial
                                      ,rh04_descricaoatividades
                       )
                values (
                                $this->rh04_instit
                               ,$this->rh04_codigo
                               ,'$this->rh04_descr'
                               ,".($this->rh04_datafinal == "null" || $this->rh04_datafinal == ""?"null":"'".$this->rh04_datafinal."'")."
                               ,".($this->rh04_datainicial == "null" || $this->rh04_datainicial == ""?"null":"'".$this->rh04_datainicial."'")."
                               ,'$this->rh04_descricaoatividades'
                      )";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Cargo dos funcionários ($this->rh04_codigo."-".$this->rh04_instit) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Cargo dos funcionários já Cadastrado";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Cargo dos funcionários ($this->rh04_codigo."-".$this->rh04_instit) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->rh04_codigo."-".$this->rh04_instit;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->rh04_codigo,$this->rh04_instit  ));
            if(($resaco!=false)||($this->numrows!=0)){

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,8764,'$this->rh04_codigo','I')");
                $resac = db_query("insert into db_acountkey values($acount,9903,'$this->rh04_instit','I')");
                $resac = db_query("insert into db_acount values($acount,1496,9903,'','".AddSlashes(pg_result($resaco,0,'rh04_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1496,8764,'','".AddSlashes(pg_result($resaco,0,'rh04_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1496,8765,'','".AddSlashes(pg_result($resaco,0,'rh04_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1496,1009975,'','".AddSlashes(pg_result($resaco,0,'rh04_datafinal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1496,1009974,'','".AddSlashes(pg_result($resaco,0,'rh04_datainicial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1496,1013708,'','".AddSlashes(pg_result($resaco,0,'rh04_descricaoatividades'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($rh04_codigo=null,$rh04_instit=null)
    {
        $this->atualizacampos();
        $sql = " update rhcargo set ";
        $virgula = "";
        if(trim($this->rh04_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh04_instit"])){
            $sql  .= $virgula." rh04_instit = $this->rh04_instit ";
            $virgula = ",";
            if(trim($this->rh04_instit) == null ){
                $this->erro_sql = " Campo Cod. Instituição não informado.";
                $this->erro_campo = "rh04_instit";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->rh04_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh04_codigo"])){
            $sql  .= $virgula." rh04_codigo = $this->rh04_codigo ";
            $virgula = ",";
            if(trim($this->rh04_codigo) == null ){
                $this->erro_sql = " Campo Código da função não informado.";
                $this->erro_campo = "rh04_codigo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->rh04_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh04_descr"])){
            $sql  .= $virgula." rh04_descr = '$this->rh04_descr' ";
            $virgula = ",";
            if(trim($this->rh04_descr) == null ){
                $this->erro_sql = " Campo Descrição da função não informado.";
                $this->erro_campo = "rh04_descr";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->rh04_datafinal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh04_datafinal_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh04_datafinal_dia"] !="") ){
            $sql  .= $virgula." rh04_datafinal = '$this->rh04_datafinal' ";
            $virgula = ",";
        }     else{
            if(isset($GLOBALS["HTTP_POST_VARS"]["rh04_datafinal_dia"])){
                $sql  .= $virgula." rh04_datafinal = null ";
                $virgula = ",";
            }
        }
        if(trim($this->rh04_datainicial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh04_datainicial_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh04_datainicial_dia"] !="") ){
            $sql  .= $virgula." rh04_datainicial = '$this->rh04_datainicial' ";
            $virgula = ",";
        }     else{
            if(isset($GLOBALS["HTTP_POST_VARS"]["rh04_datainicial_dia"])){
                $sql  .= $virgula." rh04_datainicial = null ";
                $virgula = ",";
            }
        }
        if(trim($this->rh04_descricaoatividades)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh04_descricaoatividades"])){
            $sql  .= $virgula." rh04_descricaoatividades = '$this->rh04_descricaoatividades' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if($rh04_codigo!=null){
            $sql .= " rh04_codigo = $this->rh04_codigo";
        }
        if($rh04_instit!=null){
            $sql .= " and  rh04_instit = $this->rh04_instit";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->rh04_codigo,$this->rh04_instit));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,8764,'$this->rh04_codigo','A')");
                    $resac = db_query("insert into db_acountkey values($acount,9903,'$this->rh04_instit','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh04_instit"]) || $this->rh04_instit != "")
                        $resac = db_query("insert into db_acount values($acount,1496,9903,'".AddSlashes(pg_result($resaco,$conresaco,'rh04_instit'))."','$this->rh04_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh04_codigo"]) || $this->rh04_codigo != "")
                        $resac = db_query("insert into db_acount values($acount,1496,8764,'".AddSlashes(pg_result($resaco,$conresaco,'rh04_codigo'))."','$this->rh04_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh04_descr"]) || $this->rh04_descr != "")
                        $resac = db_query("insert into db_acount values($acount,1496,8765,'".AddSlashes(pg_result($resaco,$conresaco,'rh04_descr'))."','$this->rh04_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh04_datafinal"]) || $this->rh04_datafinal != "")
                        $resac = db_query("insert into db_acount values($acount,1496,1009975,'".AddSlashes(pg_result($resaco,$conresaco,'rh04_datafinal'))."','$this->rh04_datafinal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh04_datainicial"]) || $this->rh04_datainicial != "")
                        $resac = db_query("insert into db_acount values($acount,1496,1009974,'".AddSlashes(pg_result($resaco,$conresaco,'rh04_datainicial'))."','$this->rh04_datainicial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh04_descricaoatividades"]) || $this->rh04_descricaoatividades != "")
                        $resac = db_query("insert into db_acount values($acount,1496,1013708,'".AddSlashes(pg_result($resaco,$conresaco,'rh04_descricaoatividades'))."','$this->rh04_descricaoatividades',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Cargo dos funcionários não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->rh04_codigo."-".$this->rh04_instit;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Cargo dos funcionários não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->rh04_codigo."-".$this->rh04_instit;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->rh04_codigo."-".$this->rh04_instit;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($rh04_codigo=null,$rh04_instit=null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($rh04_codigo,$rh04_instit));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,8764,'$rh04_codigo','E')");
                    $resac  = db_query("insert into db_acountkey values($acount,9903,'$rh04_instit','E')");
                    $resac  = db_query("insert into db_acount values($acount,1496,9903,'','".AddSlashes(pg_result($resaco,$iresaco,'rh04_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1496,8764,'','".AddSlashes(pg_result($resaco,$iresaco,'rh04_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1496,8765,'','".AddSlashes(pg_result($resaco,$iresaco,'rh04_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1496,1009975,'','".AddSlashes(pg_result($resaco,$iresaco,'rh04_datafinal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1496,1009974,'','".AddSlashes(pg_result($resaco,$iresaco,'rh04_datainicial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1496,1013708,'','".AddSlashes(pg_result($resaco,$iresaco,'rh04_descricaoatividades'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from rhcargo
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh04_codigo)){
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " rh04_codigo = $rh04_codigo ";
            }
            if (!empty($rh04_instit)){
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " rh04_instit = $rh04_instit ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Cargo dos funcionários não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$rh04_codigo."-".$rh04_instit;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Cargo dos funcionários não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$rh04_codigo."-".$rh04_instit;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$rh04_codigo."-".$rh04_instit;
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
            $this->erro_sql   = "Record Vazio na Tabela:rhcargo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($rh04_codigo = null,$rh04_instit = null,$campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos}";
        $sql .= "  from rhcargo ";
        $sql .= "      inner join db_config  on  db_config.codigo = rhcargo.rh04_instit";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh04_codigo)) {
                $sql2 .= " where rhcargo.rh04_codigo = $rh04_codigo ";
            }
            if (!empty($rh04_instit)) {
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " rhcargo.rh04_instit = $rh04_instit ";
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

    public function sql_query_file($rh04_codigo = null,$rh04_instit = null, $campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos} ";
        $sql .= "  from rhcargo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh04_codigo)){
                $sql2 .= " where rhcargo.rh04_codigo = $rh04_codigo ";
            }
            if (!empty($rh04_instit)){
                if ( !empty($sql2) ) {
                    $sql2 .= " and ";
                } else {
                    $sql2 .= " where ";
                }
                $sql2 .= " rhcargo.rh04_instit = $rh04_instit ";
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

}
