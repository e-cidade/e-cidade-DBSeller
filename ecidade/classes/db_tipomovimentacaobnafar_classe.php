<?php

class cl_tipomovimentacaobnafar
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
    public $fa68_codigo = 0;
    public $fa68_descricao = null;
    public $fa68_tipo = null;
   	// cria propriedade com as variaveis do arquivo
    public $campos = "
                 fa68_codigo = int4 = Código
                 fa68_descricao = varchar(200) = Descrição
                 fa68_tipo = char(1) = Tipo
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("tipomovimentacaobnafar");
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
			$this->fa68_codigo = ($this->fa68_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa68_codigo"]:$this->fa68_codigo);
			$this->fa68_descricao = ($this->fa68_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["fa68_descricao"]:$this->fa68_descricao);
			$this->fa68_tipo = ($this->fa68_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa68_tipo"]:$this->fa68_tipo);
		}else{
			$this->fa68_codigo = ($this->fa68_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa68_codigo"]:$this->fa68_codigo);
		}
	}

    public function incluir($fa68_codigo)
    {
		$this->atualizacampos();
		if($this->fa68_descricao == null ){
			$this->erro_sql = " Campo Descrição não informado.";
			$this->erro_campo = "fa68_descricao";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		if($this->fa68_tipo == null ){
			$this->erro_sql = " Campo Tipo não informado.";
			$this->erro_campo = "fa68_tipo";
			$this->erro_banco = "";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		if($fa68_codigo == "" || $fa68_codigo == null ){
			$result = db_query("select nextval('tipomovimentacaobnafar_fa68_codigo_seq')");
			if($result==false){
				$this->erro_banco = str_replace("\n","",@pg_last_error());
				$this->erro_sql   = "Verifique o cadastro da sequencia: tipomovimentacaobnafar_fa68_codigo_seq do campo: fa68_codigo";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
			$this->fa68_codigo = pg_result($result,0,0);
		}else{
			$result = db_query("select last_value from tipomovimentacaobnafar_fa68_codigo_seq");
			if(($result != false) && (pg_result($result,0,0) < $fa68_codigo)){
				$this->erro_sql = " Campo fa68_codigo maior que último número da sequencia.";
				$this->erro_banco = "Sequencia menor que este número.";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}else{
				$this->fa68_codigo = $fa68_codigo;
			}
		}
		$sql = "insert into tipomovimentacaobnafar(
										fa68_codigo
										,fa68_descricao
										,fa68_tipo
						)
					values (
									$this->fa68_codigo
								,'$this->fa68_descricao'
								,'$this->fa68_tipo'
						)";
		$result = db_query($sql);
		if($result==false){
			$this->erro_banco = str_replace("\n","",@pg_last_error());
			if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
				$this->erro_sql   = "Tipo de Movimentação ($this->fa68_codigo) não Incluído. Inclusão Abortada.";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_banco = "Tipo de Movimentação já Cadastrado";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			}else{
				$this->erro_sql   = "Tipo de Movimentação ($this->fa68_codigo) não Incluído. Inclusão Abortada.";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			}
			$this->erro_status = "0";
			$this->numrows_incluir= 0;
			return false;
		}
		$this->erro_banco = "";
		$this->erro_sql = "Inclusão efetuada com sucesso.\\n";
		$this->erro_sql .= "Valores : ".$this->fa68_codigo;
		$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
		$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
		$this->erro_status = "1";
		$this->numrows_incluir= pg_affected_rows($result);
		$lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
		if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount) && ($lSessaoDesativarAccount === false))) {

			$resaco = $this->sql_record($this->sql_query_file($this->fa68_codigo  ));
			if(($resaco!=false)||($this->numrows!=0)){

				$resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
				$acount = pg_result($resac,0,0);
				$resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
				$resac = db_query("insert into db_acountkey values($acount,1014040,'$this->fa68_codigo','I')");
				$resac = db_query("insert into db_acount values($acount,1010908,1014040,'','".AddSlashes(pg_result($resaco,0,'fa68_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
				$resac = db_query("insert into db_acount values($acount,1010908,1014041,'','".AddSlashes(pg_result($resaco,0,'fa68_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
				$resac = db_query("insert into db_acount values($acount,1010908,1014042,'','".AddSlashes(pg_result($resaco,0,'fa68_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
			}
		}
		return true;
   }

    public function alterar($fa68_codigo=null)
    {
		$this->atualizacampos();
		$sql = " update tipomovimentacaobnafar set ";
		$virgula = "";
		if(trim($this->fa68_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa68_codigo"])){
			$sql  .= $virgula." fa68_codigo = $this->fa68_codigo ";
			$virgula = ",";
			if(trim($this->fa68_codigo) == null ){
				$this->erro_sql = " Campo Código não informado.";
				$this->erro_campo = "fa68_codigo";
				$this->erro_banco = "";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
		}
		if(trim($this->fa68_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa68_descricao"])){
			$sql  .= $virgula." fa68_descricao = '$this->fa68_descricao' ";
			$virgula = ",";
			if(trim($this->fa68_descricao) == null ){
				$this->erro_sql = " Campo Descrição não informado.";
				$this->erro_campo = "fa68_descricao";
				$this->erro_banco = "";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
		}
		if(trim($this->fa68_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa68_tipo"])){
			$sql  .= $virgula." fa68_tipo = '$this->fa68_tipo' ";
			$virgula = ",";
			if(trim($this->fa68_tipo) == null ){
				$this->erro_sql = " Campo Tipo não informado.";
				$this->erro_campo = "fa68_tipo";
				$this->erro_banco = "";
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "0";
				return false;
			}
		}
		$sql .= " where ";
		if($fa68_codigo!=null){
			$sql .= " fa68_codigo = $this->fa68_codigo";
		}
		$lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
		if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount) && ($lSessaoDesativarAccount === false))) {

			$resaco = $this->sql_record($this->sql_query_file($this->fa68_codigo));
			if ($this->numrows > 0) {

				for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

					$resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
					$acount = pg_result($resac,0,0);
					$resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
					$resac = db_query("insert into db_acountkey values($acount,1014040,'$this->fa68_codigo','A')");
					if (isset($GLOBALS["HTTP_POST_VARS"]["fa68_codigo"]) || $this->fa68_codigo != "")
						$resac = db_query("insert into db_acount values($acount,1010908,1014040,'".AddSlashes(pg_result($resaco,$conresaco,'fa68_codigo'))."','$this->fa68_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
					if (isset($GLOBALS["HTTP_POST_VARS"]["fa68_descricao"]) || $this->fa68_descricao != "")
						$resac = db_query("insert into db_acount values($acount,1010908,1014041,'".AddSlashes(pg_result($resaco,$conresaco,'fa68_descricao'))."','$this->fa68_descricao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
					if (isset($GLOBALS["HTTP_POST_VARS"]["fa68_tipo"]) || $this->fa68_tipo != "")
						$resac = db_query("insert into db_acount values($acount,1010908,1014042,'".AddSlashes(pg_result($resaco,$conresaco,'fa68_tipo'))."','$this->fa68_tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
				}
			}
		}
		$result = db_query($sql);
		if (!$result) {
			$this->erro_banco = str_replace("\n","",@pg_last_error());
			$this->erro_sql   = "Tipo de Movimentação não Alterado. Alteração Abortada.\\n";
				$this->erro_sql .= "Valores : ".$this->fa68_codigo;
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			$this->numrows_alterar = 0;
			return false;
		} else {
			if (pg_affected_rows($result) == 0) {
				$this->erro_banco = "";
				$this->erro_sql = "Tipo de Movimentação não foi Alterado. Alteração Executada.\\n";
				$this->erro_sql .= "Valores : ".$this->fa68_codigo;
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "1";
				$this->numrows_alterar = 0;
				return true;
			} else {
				$this->erro_banco = "";
				$this->erro_sql = "Alteração efetuada com sucesso.\\n";
				$this->erro_sql .= "Valores : ".$this->fa68_codigo;
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "1";
				$this->numrows_alterar = pg_affected_rows($result);
				return true;
			}
		}
	}

    public function excluir($fa68_codigo=null, $dbwhere = null)
    {
		$lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
		if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount) && ($lSessaoDesativarAccount === false))) {

			if (empty($dbwhere)) {

				$resaco = $this->sql_record($this->sql_query_file($fa68_codigo));
			} else {
				$resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
			}
			if (($resaco != false) || ($this->numrows!=0)) {

				for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

					$resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
					$acount = pg_result($resac,0,0);
					$resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
					$resac  = db_query("insert into db_acountkey values($acount,1014040,'$fa68_codigo','E')");
					$resac  = db_query("insert into db_acount values($acount,1010908,1014040,'','".AddSlashes(pg_result($resaco,$iresaco,'fa68_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
					$resac  = db_query("insert into db_acount values($acount,1010908,1014041,'','".AddSlashes(pg_result($resaco,$iresaco,'fa68_descricao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
					$resac  = db_query("insert into db_acount values($acount,1010908,1014042,'','".AddSlashes(pg_result($resaco,$iresaco,'fa68_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
				}
			}
		}
		$sql = " delete from tipomovimentacaobnafar
						where ";
		$sql2 = "";
		if (empty($dbwhere)) {
			if (!empty($fa68_codigo)){
				if (!empty($sql2)) {
					$sql2 .= " and ";
				}
				$sql2 .= " fa68_codigo = $fa68_codigo ";
			}
		} else {
			$sql2 = $dbwhere;
		}
		$result = db_query($sql.$sql2);
		if ($result == false) {
			$this->erro_banco = str_replace("\n","",@pg_last_error());
			$this->erro_sql   = "Tipo de Movimentação não Excluído. Exclusão Abortada.\\n";
			$this->erro_sql .= "Valores : ".$fa68_codigo;
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			$this->numrows_excluir = 0;
			return false;
		} else {
			if (pg_affected_rows($result) == 0) {
				$this->erro_banco = "";
				$this->erro_sql = "Tipo de Movimentação não Encontrado. Exclusão não Efetuada.\\n";
				$this->erro_sql .= "Valores : ".$fa68_codigo;
				$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
				$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
				$this->erro_status = "1";
				$this->numrows_excluir = 0;
				return true;
			} else {
				$this->erro_banco = "";
				$this->erro_sql = "Exclusão efetuada com sucesso.\\n";
				$this->erro_sql .= "Valores : ".$fa68_codigo;
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
			$this->erro_sql   = "Record Vazio na Tabela:tipomovimentacaobnafar";
			$this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
			$this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
			$this->erro_status = "0";
			return false;
		}
		return $result;
	}

    public function sql_query($fa68_codigo = null,$campos = "*", $ordem = null, $dbwhere = "")
	{
		$sql  = "select {$campos}";
		$sql .= "  from tipomovimentacaobnafar ";
		$sql2 = "";
		if (empty($dbwhere)) {
			if (!empty($fa68_codigo)) {
				$sql2 .= " where tipomovimentacaobnafar.fa68_codigo = $fa68_codigo ";
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

    public function sql_query_file($fa68_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
	{
		$sql  = "select {$campos} ";
		$sql .= "  from tipomovimentacaobnafar ";
		$sql2 = "";
		if (empty($dbwhere)) {
			if (!empty($fa68_codigo)){
				$sql2 .= " where tipomovimentacaobnafar.fa68_codigo = $fa68_codigo ";
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
