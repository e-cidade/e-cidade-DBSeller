<?php
/*
*     E-cidade Software Publico para Gestao Municipal
*  Copyright (C) 2009 DBSeller Servicos de Informatica
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

// Módulo: fiscal
// Classe da entidade parametrosandamento
class cl_fis_parametrosandamento {
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
    public $sequencial = 0;
    public $tem_data_ciencia = 'f';
    public $tipoandam = 0;
    public $tem_data_recurso = 'f';
    // cria propriedade com as variaveis do arquivo
    public $campos = "
    sequencial = int4 = Sequencia dos Parâmetros do Andamento
    tipo_de_peca = int4 = Tipo da peça
    manual_automatico = int4 = Manual ou automático
    tem_data_ciencia = bool = Se terá data de ciência
    tipoandam = int4 = Código do tipo de andamento
    tem_data_recurso = bool = Se terá prazo para recurso.
    ";
    //funcao construtor da classe
    public function __construct() {
    //classes dos rotulos dos campos
        $this->rotulo = new rotulo("fis_parametrosandamento");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
    //funcao erro
    public function erro($mostra,$retorna) {
        if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if($retorna==true){
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
    // funcao para atualizar campos
    public function atualizacampos($exclusao=false) {
        if($exclusao==false){
            $this->sequencial = ($this->sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sequencial"]:$this->sequencial);
            $this->tem_data_ciencia = ($this->tem_data_ciencia == "f"?@$GLOBALS["HTTP_POST_VARS"]["tem_data_ciencia"]:$this->tem_data_ciencia);
            $this->tipoandam = ($this->tipoandam == ""?@$GLOBALS["HTTP_POST_VARS"]["tipoandam"]:$this->tipoandam);
	    $this->tem_data_recurso = ($this->tem_data_recurso == "f"?@$GLOBALS["HTTP_POST_VARS"]["tem_data_recurso"]:$this->tem_data_recurso);
        }else{
            $this->sequencial = ($this->sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sequencial"]:$this->sequencial);
        }
    }

    // Método para inclusão
    public function incluir (){
        $this->atualizacampos();

        if($this->tem_data_ciencia == null ){
            $this->erro_sql = " Campo data ciência não informado.";
            $this->erro_campo = "tem_data_ciencia";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->tipoandam == null ){
            $this->erro_sql = " Campo código tipo do andamento não informado.";
            $this->erro_campo = "tipoandam";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
	if($this->tem_data_recurso == null ){
            $this->erro_sql = " Campo tem prazo para recurso não informado.";
            $this->erro_campo = "tem_data_recurso";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = " INSERT INTO fiscalizacao.fis_parametrosandamento (tem_data_ciencia, tipoandam, tem_data_recurso) ";
        $sql .= " VALUES ('$this->tem_data_ciencia', $this->tipoandam, '$this->tem_data_recurso') ";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Parâmetros ($this->sequencial) não incluídos. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Parâmetros já cadastrados";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Parâmetros ($this->sequencial) não incluídos. Inclusão abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    } // fim método incluir

    // Método para alteração
    public function alterar ($sequencial=null) {
        $this->atualizacampos();
        $sql = " UPDATE fiscalizacao.fis_parametrosandamento SET ";
        $virgula = "";
        if(trim($this->sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sequencial"])){
            $sql  .= $virgula." sequencial = $this->sequencial ";
            $virgula = ",";
            if(trim($this->sequencial) == null ){
                $this->erro_sql = " Campo sequencial não informado.";
                $this->erro_campo = "sequencial";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }

        if(trim($this->tem_data_ciencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tem_data_ciencia"])){
            $sql  .= $virgula." tem_data_ciencia = '$this->tem_data_ciencia' ";
            $virgula = ",";
            if(trim($this->tem_data_ciencia) == null ){
                $this->erro_sql = " Campo data ciência não informado.";
                $this->erro_campo = "tem_data_ciencia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->tipoandam)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tipoandam"])){
            $sql  .= $virgula." tipoandam = $this->tipoandam ";
            $virgula = ",";
            if(trim($this->tipoandam) == null ){
                $this->erro_sql = " Campo código do tipo de andamento não informado.";
                $this->erro_campo = "tipoandam";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
	if(trim($this->tem_data_recurso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tem_data_recurso"])){
            $sql  .= $virgula." tem_data_recurso = '$this->tem_data_recurso' ";
            $virgula = ",";
            if(trim($this->tem_data_recurso) == null ){
                $this->erro_sql = " Campo tem prazo para recurso não informado.";
                $this->erro_campo = "tem_data_ciencia";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " WHERE ";
        if($sequencial!=null){
            $sql .= " sequencial = $this->sequencial";
        }
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Parâmetros não alterados. Alteracão abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Parâmetros não alterados. Alteracão executada.\\n";
                $this->erro_sql .= "Valores : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            }else{
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    } // fim método alterar

    // Método para exclusão
    public function excluir ($sequencial=null,$dbwhere=null) {
        $sql = " DELETE FROM fiscalizacao.fis_parametrosandamento
        WHERE ";
        $sql2 = "";
        if($dbwhere==null || $dbwhere ==""){
            if($sequencial != ""){
                if($sql2!=""){
                    $sql2 .= " and ";
                }
                $sql2 .= " sequencial = $sequencial ";
            }
        }else{
            $sql2 = $dbwhere;
        }
        //die($sql.$sql2);
        $result = db_query($sql.$sql2);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Parâmetros não excluídos. Exclusão abortada.\\n";
            $this->erro_sql .= "Valores : ".$sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Parâmetros não encontrados. Exclusão não efetuada.\\n";
                $this->erro_sql .= "Valores : ".$sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            }else{
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    } // fim método excluir

    // Método recordset
    public function sql_record($sql) {
        $result = db_query($sql);
        if($result==false){
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if($this->numrows==0){
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela: parametrosandamento";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    } // fim método sql_record

    // Método sql
    public function sql_query ( $sequencial=null,$campos="*",$ordem=null,$dbwhere="") {
        $sql = "SELECT ";
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
        $sql .= " FROM fiscalizacao.fis_parametrosandamento ";
        $sql2 = "";
        if($dbwhere==""){
            if($sequencial!=null ){
                $sql2 .= " WHERE fis_parametrosandamento.sequencial = $sequencial ";
            }
        }else if($dbwhere != ""){
            $sql2 = " WHERE $dbwhere ";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " ORDER BY ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    } // fim método sql_query

    public function sqlGetTipoAndamWithParametrosAndamento($fields = "*", $where = "", $order = null, $group = null){
       $sql  = " select {$fields} ";
       $sql .= " from fiscalizacao.fis_tipoandam ";
       $sql .= " inner join db_config on db_config.codigo = fis_tipoandam.y41_instit ";
       $sql .= " inner join cgm on cgm.z01_numcgm = db_config.numcgm ";
       $sql .= " inner join fiscalizacao.fis_grupotipoandamento_tipoandam on fi30_tipoandam = y41_codtipo ";
       $sql .= " inner join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial ";
       $sql .= " left join fiscalizacao.fis_tipoandam_datafim on fi31_tipoandam = y41_codtipo ";
       $sql .= " left join fiscalizacao.fis_parametrosandamento on fis_parametrosandamento.tipoandam = fis_tipoandam.y41_codtipo ";

       if ($where != '') {
           $sql .= " where {$where}";
       }

       if ($order != null) {
           $sql .= " order by {$order}";
       }

       if ($group != null) {
           $sql .= " group by {$group}";
       }

       return $sql;

    } // fim método sqlGetTipoAndamWithParametrosAndamento

} // fim classe
?>
