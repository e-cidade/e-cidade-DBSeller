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
class cl_rhvinculoaprendiz
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
    public $rh312_sequencial = 0; 
    public $rh312_matricula = 0; 
    public $rh312_instit = 0; 
    public $rh312_modalidade = 0; 
    public $rh312_cnpjqualificadora = null; 
    public $rh312_cnpjefetivada = null; 
    public $rh312_cnpjpratica = null; 
    // cria propriedade com as variaveis do arquivo 
    public $campos = "
        rh312_sequencial = int4 =  
        rh312_matricula = int4 =  
        rh312_instit = int4 =  
        rh312_modalidade = int4 =  
        rh312_cnpjqualificadora = varchar(14) =  
        rh312_cnpjefetivada = varchar(14) =  
        rh312_cnpjpratica = varchar(14) =  
    ";

    public function __construct()
    {
        $this->rotulo = new rotulo("rhvinculoaprendiz"); 
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if (($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )) {
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if ($retorna == true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if ($exclusao == false) {
            $this->rh312_sequencial = ($this->rh312_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_sequencial"]:$this->rh312_sequencial);
            $this->rh312_matricula = ($this->rh312_matricula == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_matricula"]:$this->rh312_matricula);
            $this->rh312_modalidade = ($this->rh312_modalidade == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_modalidade"]:$this->rh312_modalidade);
            $this->rh312_instit = ($this->rh312_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_instit"]:$this->rh312_instit);
            $this->rh312_cnpjqualificadora = ($this->rh312_cnpjqualificadora == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_cnpjqualificadora"]:$this->rh312_cnpjqualificadora);
            $this->rh312_cnpjefetivada = ($this->rh312_cnpjefetivada == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_cnpjefetivada"]:$this->rh312_cnpjefetivada);
            $this->rh312_cnpjpratica = ($this->rh312_cnpjpratica == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_cnpjpratica"]:$this->rh312_cnpjpratica);
        } else {
            $this->rh312_sequencial = ($this->rh312_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["rh312_sequencial"]:$this->rh312_sequencial);
        }
    }

    public function incluir($rh312_sequencial)
    {
        $this->atualizacampos();
        if ($this->rh312_matricula == null) { 
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "rh312_matricula";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh312_instit == null){ 
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "rh312_instit";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh312_modalidade == null){ 
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "rh312_modalidade";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->rh312_cnpjqualificadora == null) { 
            $this->rh312_cnpjqualificadora = "";
        }
        if ($this->rh312_cnpjefetivada == null) { 
            $this->rh312_cnpjefetivada = "";
        }
        if ($this->rh312_cnpjpratica == null) { 
            $this->rh312_cnpjpratica = "";
        }
        if ($rh312_sequencial == "" || $rh312_sequencial == null) {
            $result = db_query("select nextval('rhvinculoaprendiz_id_seq')"); 
            if ($result == false) {
                $this->erro_banco = str_replace("\n","",@pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: rhvinculoaprendiz_id_seq do campo: rh312_sequencial"; 
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"',"",str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false; 
            }
            $this->rh312_sequencial = pg_result($result, 0, 0); 
        } else {
            $result = db_query("select last_value from rhvinculoaprendiz_id_seq");
            if (($result != false) && (pg_result($result, 0, 0) < $rh312_sequencial)) {
                $this->erro_sql = " Campo rh312_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"',"",str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->rh312_sequencial = $rh312_sequencial; 
            }
        }
        if (($this->rh312_sequencial == null) || ($this->rh312_sequencial == "")) { 
            $this->erro_sql = " Campo rh312_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "
            insert into pessoal.rhvinculoaprendiz(
                rh312_sequencial 
                ,rh312_matricula 
                ,rh312_instit 
                ,rh312_modalidade 
                ,rh312_cnpjqualificadora 
                ,rh312_cnpjefetivada 
                ,rh312_cnpjpratica 
            )
            values (
                $this->rh312_sequencial 
                ,$this->rh312_matricula 
                ,$this->rh312_instit 
                ,$this->rh312_modalidade 
                ,'$this->rh312_cnpjqualificadora' 
                ,'$this->rh312_cnpjefetivada' 
                ,'$this->rh312_cnpjpratica' 
            )";
        $result = db_query($sql); 
        if ($result == false) { 
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0 ) {
                $this->erro_sql = " ($this->rh312_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = " já Cadastrado";
                $this->erro_msg .= str_replace('"', "",str_replace("'","", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = " ($this->rh312_sequencial) não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->rh312_sequencial;
        $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    } 

    public function alterar($rh312_sequencial = null)
    {
        $this->atualizacampos();
        $sql = " update pessoal.rhvinculoaprendiz set ";
        $virgula = "";
        if (trim($this->rh312_sequencial) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh312_sequencial"])) { 
        $sql .= $virgula . " rh312_sequencial = $this->rh312_sequencial ";
        $virgula = ",";
        if (trim($this->rh312_sequencial) == null) { 
            $this->erro_sql = " Campo  não informado.";
            $this->erro_campo = "rh312_sequencial";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        }
        if (trim($this->rh312_matricula) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh312_matricula"])) {
            $sql .= $virgula." rh312_matricula = $this->rh312_matricula ";
            $virgula = ",";
            if (trim($this->rh312_matricula) == null) { 
                $this->erro_sql = " Campo não informado.";
                $this->erro_campo = "rh312_matricula";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh312_instit) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh312_instit"])) { 
            $sql .= $virgula . " rh312_instit = $this->rh312_instit ";
            $virgula = ",";
            if (trim($this->rh312_instit) == null) { 
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "rh312_instit";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh312_modalidade) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh312_modalidade"])) { 
            $sql .= $virgula . " rh312_modalidade = $this->rh312_modalidade ";
            $virgula = ",";
            if (trim($this->rh312_modalidade) == null) { 
                $this->erro_sql = " Campo  não informado.";
                $this->erro_campo = "rh312_modalidade";
                $this->erro_banco = "";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if (trim($this->rh312_cnpjqualificadora) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh312_cnpjqualificadora"])) { 
            $sql .= $virgula . " rh312_cnpjqualificadora = '$this->rh312_cnpjqualificadora' ";
            $virgula = ",";
        }
        if (trim($this->rh312_cnpjefetivada) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh312_cnpjefetivada"])) { 
            $sql .= $virgula." rh312_cnpjefetivada = '$this->rh312_cnpjefetivada' ";
            $virgula = ",";
        }
        if (trim($this->rh312_cnpjpratica) != "" || isset($GLOBALS["HTTP_POST_VARS"]["rh312_cnpjpratica"])) { 
            $sql  .= $virgula." rh312_cnpjpratica = '$this->rh312_cnpjpratica' ";
            $virgula = ",";
        }
        $sql .= " where ";
        if ($rh312_sequencial != null) {
            $sql .= " rh312_sequencial = $this->rh312_sequencial";
        }

        $result = db_query($sql);
        if (!$result) { 
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->rh312_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
        if (pg_affected_rows($result) == 0) {
            $this->erro_banco = "";
            $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
            $this->erro_sql .= "Valores : " . $this->rh312_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "1";
            $this->numrows_alterar = 0;
            return true;
        } else {
            $this->erro_banco = "";
            $this->erro_sql = "Alteração efetuada com sucesso.\\n";
            $this->erro_sql .= "Valores : " . $this->rh312_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "1";
            $this->numrows_alterar = pg_affected_rows($result);
            return true;
        } 
        } 
    } 

    public function excluir($rh312_sequencial = null, $dbwhere = null)
    {
        $sql = " delete from pessoal.rhvinculoaprendiz
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh312_sequencial)){
                if (!empty($sql2)) {
                $sql2 .= " and ";
                }
                $sql2 .= " rh312_sequencial = $rh312_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if ($result == false) { 
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = " não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : " . $rh312_sequencial;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg.=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : " . $rh312_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$rh312_sequencial;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
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
            $this->numrows = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Erro ao selecionar os registros.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql = "Record Vazio na Tabela:rhvinculoaprendiz";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($rh312_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "")
    { 
        $sql = "select {$campos}";
        $sql .= "  from pessoal.rhvinculoaprendiz ";
        $sql .= "      inner join rhpessoal on rhpessoal.rh01_regist = rhvinculoaprendiz.rh312_matricula";
        $sql .= "      inner join cgm on cgm.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      inner join rhestcivil on rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      inner join rhraca on rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      left  join rhfuncao on rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
        $sql .= "      inner join rhinstrucao on rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      inner join rhnacionalidade on rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        $sql .= "      left  join rhsindicato on rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
        $sql .= "      inner join rhreajusteparidade on rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh312_sequencial)) {
                $sql2 .= " where rhvinculoaprendiz.rh312_sequencial = $rh312_sequencial "; 
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

    public function sql_query_file($rh312_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select {$campos} ";
        $sql .= "  from pessoal.rhvinculoaprendiz ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($rh312_sequencial)){
                $sql2 .= " where rhvinculoaprendiz.rh312_sequencial = $rh312_sequencial "; 
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
