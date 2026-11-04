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

class cl_matestoqueinimei
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
    public $m82_codigo = 0;
    public $m82_matestoqueini = 0;
    public $m82_matestoqueitem = 0;
    public $m82_quant = 0;
    public $m82_matestoqueinimeiorigem = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 m82_codigo = int8 = Código
                 m82_matestoqueini = int8 = Sequencial da implantação
                 m82_matestoqueitem = int8 = Código sequencial do lançamento
                 m82_quant = float8 = Quantidade
                 m82_matestoqueinimeiorigem = int8 = Movimentação de Origem
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("matestoqueinimei");
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
            $this->m82_codigo = ($this->m82_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["m82_codigo"]:$this->m82_codigo);
            $this->m82_matestoqueini = ($this->m82_matestoqueini == ""?@$GLOBALS["HTTP_POST_VARS"]["m82_matestoqueini"]:$this->m82_matestoqueini);
            $this->m82_matestoqueitem = ($this->m82_matestoqueitem == ""?@$GLOBALS["HTTP_POST_VARS"]["m82_matestoqueitem"]:$this->m82_matestoqueitem);
            $this->m82_quant = ($this->m82_quant == ""?@$GLOBALS["HTTP_POST_VARS"]["m82_quant"]:$this->m82_quant);
            $this->m82_matestoqueinimeiorigem = ($this->m82_matestoqueinimeiorigem == ""?@$GLOBALS["HTTP_POST_VARS"]["m82_matestoqueinimeiorigem"]:$this->m82_matestoqueinimeiorigem);
        }else{
            $this->m82_codigo = ($this->m82_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["m82_codigo"]:$this->m82_codigo);
        }
    }

    public function incluir($m82_codigo)
    {
        $this->atualizacampos();
        if($this->m82_matestoqueini == null ){
            $this->erro_sql = " Campo Sequencial da implantação não informado.";
            $this->erro_campo = "m82_matestoqueini";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->m82_matestoqueitem == null ){
            $this->erro_sql = " Campo Código sequencial do lançamento não informado.";
            $this->erro_campo = "m82_matestoqueitem";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->m82_quant == null ){
            $this->erro_sql = " Campo Quantidade não informado.";
            $this->erro_campo = "m82_quant";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->m82_matestoqueinimeiorigem == null ){
            $this->m82_matestoqueinimeiorigem = "null";
        }
        if($m82_codigo == "" || $m82_codigo == null ){
            $result = db_query("select nextval('matestoqueinimei_m82_codigo_seq')");
            if($result==false){
                $this->erro_banco = str_replace("\n","",@pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: matestoqueinimei_m82_codigo_seq do campo: m82_codigo";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->m82_codigo = pg_result($result,0,0);
        }else{
            $result = db_query("select last_value from matestoqueinimei_m82_codigo_seq");
            if(($result != false) && (pg_result($result,0,0) < $m82_codigo)){
                $this->erro_sql = " Campo m82_codigo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }else{
                $this->m82_codigo = $m82_codigo;
            }
        }
        if(($this->m82_codigo == null) || ($this->m82_codigo == "") ){
            $this->erro_sql = " Campo m82_codigo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into matestoqueinimei(
                                       m82_codigo
                                      ,m82_matestoqueini
                                      ,m82_matestoqueitem
                                      ,m82_quant
                                      ,m82_matestoqueinimeiorigem
                       )
                values (
                                $this->m82_codigo
                               ,$this->m82_matestoqueini
                               ,$this->m82_matestoqueitem
                               ,$this->m82_quant
                               ,$this->m82_matestoqueinimeiorigem
                      )";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Lançamentos da saída manual ($this->m82_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Lançamentos da saída manual já Cadastrado";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Lançamentos da saída manual ($this->m82_codigo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->m82_codigo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->m82_codigo  ));
            if(($resaco!=false)||($this->numrows!=0)){

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,6900,'$this->m82_codigo','I')");
                $resac = db_query("insert into db_acount values($acount,1135,6900,'','".AddSlashes(pg_result($resaco,0,'m82_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1135,6901,'','".AddSlashes(pg_result($resaco,0,'m82_matestoqueini'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1135,6902,'','".AddSlashes(pg_result($resaco,0,'m82_matestoqueitem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1135,6903,'','".AddSlashes(pg_result($resaco,0,'m82_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1135,1013609,'','".AddSlashes(pg_result($resaco,0,'m82_matestoqueinimeiorigem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($m82_codigo=null)
    {
        $this->atualizacampos();
        $sql = " update matestoqueinimei set ";
        $virgula = "";
        if(trim($this->m82_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m82_codigo"])){
            $sql  .= $virgula." m82_codigo = $this->m82_codigo ";
            $virgula = ",";
            if(trim($this->m82_codigo) == null ){
                $this->erro_sql = " Campo Código não informado.";
                $this->erro_campo = "m82_codigo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->m82_matestoqueini)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m82_matestoqueini"])){
            $sql  .= $virgula." m82_matestoqueini = $this->m82_matestoqueini ";
            $virgula = ",";
            if(trim($this->m82_matestoqueini) == null ){
                $this->erro_sql = " Campo Sequencial da implantação não informado.";
                $this->erro_campo = "m82_matestoqueini";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->m82_matestoqueitem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m82_matestoqueitem"])){
            $sql  .= $virgula." m82_matestoqueitem = $this->m82_matestoqueitem ";
            $virgula = ",";
            if(trim($this->m82_matestoqueitem) == null ){
                $this->erro_sql = " Campo Código sequencial do lançamento não informado.";
                $this->erro_campo = "m82_matestoqueitem";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->m82_quant)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m82_quant"])){
            $sql  .= $virgula." m82_quant = $this->m82_quant ";
            $virgula = ",";
            if(trim($this->m82_quant) == null ){
                $this->erro_sql = " Campo Quantidade não informado.";
                $this->erro_campo = "m82_quant";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->m82_matestoqueinimeiorigem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["m82_matestoqueinimeiorigem"])){
            if(trim($this->m82_matestoqueinimeiorigem)=="" && isset($GLOBALS["HTTP_POST_VARS"]["m82_matestoqueinimeiorigem"])){
                $this->m82_matestoqueinimeiorigem = "null" ;
            }
            $sql  .= $virgula." m82_matestoqueinimeiorigem = $this->m82_matestoqueinimeiorigem ";
            $virgula = ",";
        }
        $sql .= " where ";
        if($m82_codigo!=null){
            $sql .= " m82_codigo = $this->m82_codigo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->m82_codigo));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,6900,'$this->m82_codigo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m82_codigo"]) || $this->m82_codigo != "")
                        $resac = db_query("insert into db_acount values($acount,1135,6900,'".AddSlashes(pg_result($resaco,$conresaco,'m82_codigo'))."','$this->m82_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m82_matestoqueini"]) || $this->m82_matestoqueini != "")
                        $resac = db_query("insert into db_acount values($acount,1135,6901,'".AddSlashes(pg_result($resaco,$conresaco,'m82_matestoqueini'))."','$this->m82_matestoqueini',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m82_matestoqueitem"]) || $this->m82_matestoqueitem != "")
                        $resac = db_query("insert into db_acount values($acount,1135,6902,'".AddSlashes(pg_result($resaco,$conresaco,'m82_matestoqueitem'))."','$this->m82_matestoqueitem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m82_quant"]) || $this->m82_quant != "")
                        $resac = db_query("insert into db_acount values($acount,1135,6903,'".AddSlashes(pg_result($resaco,$conresaco,'m82_quant'))."','$this->m82_quant',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["m82_matestoqueinimeiorigem"]) || $this->m82_matestoqueinimeiorigem != "")
                        $resac = db_query("insert into db_acount values($acount,1135,1013609,'".AddSlashes(pg_result($resaco,$conresaco,'m82_matestoqueinimeiorigem'))."','$this->m82_matestoqueinimeiorigem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Lançamentos da saída manual não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->m82_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lançamentos da saída manual não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->m82_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->m82_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($m82_codigo=null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($m82_codigo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,6900,'$m82_codigo','E')");
                    $resac  = db_query("insert into db_acount values($acount,1135,6900,'','".AddSlashes(pg_result($resaco,$iresaco,'m82_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1135,6901,'','".AddSlashes(pg_result($resaco,$iresaco,'m82_matestoqueini'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1135,6902,'','".AddSlashes(pg_result($resaco,$iresaco,'m82_matestoqueitem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1135,6903,'','".AddSlashes(pg_result($resaco,$iresaco,'m82_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,1135,1013609,'','".AddSlashes(pg_result($resaco,$iresaco,'m82_matestoqueinimeiorigem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from matestoqueinimei
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($m82_codigo)){
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " m82_codigo = $m82_codigo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Lançamentos da saída manual não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$m82_codigo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Lançamentos da saída manual não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$m82_codigo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$m82_codigo;
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
            $this->erro_sql   = "Record Vazio na Tabela:matestoqueinimei";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    function sql_query ( $m82_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = split("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from matestoqueinimei ";
        $sql .= "      inner join matestoqueitem  on  matestoqueitem.m71_codlanc = matestoqueinimei.m82_matestoqueitem";
        $sql .= "      inner join matestoqueini   on  matestoqueini.m80_codigo = matestoqueinimei.m82_matestoqueini";
        $sql .= "      inner join matestoqueinimeipm   on  m89_matestoqueinimei = m82_codigo";
        $sql .= "      inner join matestoque      on  matestoque.m70_codigo = matestoqueitem.m71_codmatestoque";
//     $sql .= "      inner join matestoqueitem b on  b.m71_codlanc = matestoqueini.m80_matestoqueitem";
        $sql .= "      inner join matestoquetipo  on  matestoquetipo.m81_codtipo = matestoqueini.m80_codtipo";
        $sql2 = "";
        if($dbwhere==""){
            if($m82_codigo!=null ){
                $sql2 .= " where matestoqueinimei.m82_codigo = $m82_codigo ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = split("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    function sql_query_file ( $m82_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = split("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from matestoqueinimei ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($m82_codigo)){
                $sql2 .= " where matestoqueinimei.m82_codigo = $m82_codigo ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if(!empty($ordem)){
            $sql .= " order by ";
            $campos_sql = split("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    function sql_query_info ( $m82_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = split("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from matestoqueinimei ";
        $sql .= "      inner join matestoqueitem on  matestoqueitem.m71_codlanc = matestoqueinimei.m82_matestoqueitem";
        $sql .= "      inner join matestoqueini  on  matestoqueini.m80_codigo = matestoqueinimei.m82_matestoqueini";
        $sql .= "      inner join matestoque  on  matestoque.m70_codigo = matestoqueitem.m71_codmatestoque";
        $sql .= "      inner join matestoquetipo  on  matestoquetipo.m81_codtipo = matestoqueini.m80_codtipo";
        $sql .= "      inner join matmater  on  matmater.m60_codmater = matestoque.m70_codmatmater";
        $sql .= "      inner join matunid  on  matmater.m60_codmatunid = matunid.m61_codmatunid";
        $sql2 = "";
        if($dbwhere==""){
            if($m82_codigo!=null ){
                $sql2 .= " where matestoqueinimei.m82_codigo = $m82_codigo ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = split("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    function sql_query_matestoque ( $m82_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = split("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from matestoqueinimei ";
        $sql .= "      inner join matestoqueitem on  matestoqueitem.m71_codlanc = matestoqueinimei.m82_matestoqueitem";
        $sql .= "      left join matestoqueitemlote ON matestoqueitemlote.m77_matestoqueitem = matestoqueitem.m71_codlanc";
        $sql .= "      inner join matestoqueini  on  matestoqueini.m80_codigo = matestoqueinimei.m82_matestoqueini";
        $sql .= "      inner join matestoque  on  matestoque.m70_codigo = matestoqueitem.m71_codmatestoque";
        $sql .= "      inner join matestoquetipo  on  matestoquetipo.m81_codtipo = matestoqueini.m80_codtipo";
        $sql .= "      inner join matmater  on  matmater.m60_codmater = matestoque.m70_codmatmater";
        $sql .= "      left join matestoquetransf  on  matestoquetransf.m83_matestoqueini = matestoqueini.m80_codigo";
        $sql .= "      left join matestoqueinil    on  matestoqueinil.m86_matestoqueini = matestoqueini.m80_codigo";
        $sql .= "      left join matestoqueinill   on  matestoqueinill.m87_matestoqueinil = matestoqueinil.m86_codigo";
        $sql .= "      left join matestoqueini b   on  b.m80_codigo = matestoqueinill.m87_matestoqueini";
        $sql .= "      left join db_depart         on  db_depart.coddepto = matestoqueini.m80_coddepto";
        $sql2 = "";
        if($dbwhere==""){
            if($m82_codigo!=null ){
                $sql2 .= " where matestoqueinimei.m82_codigo = $m82_codigo ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = split("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    function sql_query_matestoqueitem ( $m82_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = split("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from matestoqueinimei ";
        $sql .= "      inner join matestoqueitem  on  matestoqueitem.m71_codlanc = matestoqueinimei.m82_matestoqueitem";
        $sql .= "      inner join matestoque  on  matestoque.m70_codigo = matestoqueitem.m71_codmatestoque";
        $sql2 = "";
        if($dbwhere==""){
            if($m82_codigo!=null ){
                $sql2 .= " where matestoqueinimei.m82_codigo = $m82_codigo ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = split("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    function sql_query_precomedio ($m82_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

        $sql = "select ";
        if ($campos != "*" ) {

            $campos_sql = split("#",$campos);
            $virgula    = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {

                $sql    .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from matestoqueinimei ";
        $sql .= "      inner join matestoqueini             on  matestoqueini.m80_codigo = matestoqueinimei.m82_matestoqueini    ";
        $sql .= "      inner join matestoquetipo            on m80_codtipo               = m81_codtipo                           ";
        $sql .= "      inner join matestoqueinimeipm        on m82_codigo                = m89_matestoqueinimei                  ";
        $sql .= "      inner join db_usuarios               on m80_login                 = id_usuario                            ";
        $sql .= "      inner join db_depart as dpartini     on m80_coddepto              = dpartini.	coddepto                      ";
        $sql .= "      inner join matestoqueitem            on m82_matestoqueitem        = m71_codlanc                           ";
        $sql .= "      inner join matestoque                on m71_codmatestoque         = m70_codigo                            ";
        $sql .= "      inner join db_depart as dpartestoque on m70_coddepto              = dpartestoque.coddepto                 ";
        $sql .= "      inner join db_almox on db_almox.m91_depto = dpartestoque.coddepto                                         ";
        $sql .= "      inner join matmater                  on m60_codmater 	           = m70_codmatmater                      ";
        $sql .= "      left join matmaterunisai             on m62_codmater              = m60_codmater ";
        $sql .= "      left join matunid                    on m61_codmatunid            = m62_codmatunid ";
        $sql .= "       left join matestoqueitemlote        on m77_matestoqueitem        = m71_codlanc                           ";
        $sql .= "       left join matestoqueitemfabric      on m78_matestoqueitem        = m71_codlanc                           ";
        $sql .= "       left join matfabricante             on m76_sequencial 	          = m78_matfabricante                    ";

        $sql2 = "";
        if ($dbwhere == "") {

            if ($m82_codigo != null ) {
                $sql2 .= " where matestoqueinimei.m82_codigo = $m82_codigo ";
            }
        } else if($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null ) {

            $sql       .= " order by ";
            $campos_sql = split("#",$ordem);
            $virgula    = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {

                $sql    .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
}
