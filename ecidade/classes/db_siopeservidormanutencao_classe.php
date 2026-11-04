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

class cl_siopeservidormanutencao
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
    public $si06_servidor = 0; 
    public $si06_categoria = 0; 
    public $si06_situacao = 0; 
    public $si06_segmento = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 si06_servidor = int4 = Matricula do servidor 
                 si06_categoria = int4 = Categoria do Servidor 
                 si06_situacao = int4 = Situação do Servidor 
                 si06_segmento = int4 = Segmento de Atuação do Servidor 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("siopeservidormanutencao"); 
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
       $this->si06_servidor = ($this->si06_servidor == ""?@$GLOBALS["HTTP_POST_VARS"]["si06_servidor"]:$this->si06_servidor);
       $this->si06_categoria = ($this->si06_categoria == ""?@$GLOBALS["HTTP_POST_VARS"]["si06_categoria"]:$this->si06_categoria);
       $this->si06_situacao = ($this->si06_situacao == ""?@$GLOBALS["HTTP_POST_VARS"]["si06_situacao"]:$this->si06_situacao);
       $this->si06_segmento = ($this->si06_segmento == ""?@$GLOBALS["HTTP_POST_VARS"]["si06_segmento"]:$this->si06_segmento);
     }else{
       $this->si06_servidor = ($this->si06_servidor == ""?@$GLOBALS["HTTP_POST_VARS"]["si06_servidor"]:$this->si06_servidor);
     }
   }

    public function incluir($si06_servidor)
    {
      $this->atualizacampos();
     if($this->si06_categoria == null ){ 
       $this->erro_sql = " Campo Categoria do Servidor não informado.";
       $this->erro_campo = "si06_categoria";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->si06_situacao == null ){ 
       $this->erro_sql = " Campo Situação do Servidor não informado.";
       $this->erro_campo = "si06_situacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->si06_segmento == null ){ 
       $this->erro_sql = " Campo Segmento de Atuação do Servidor não informado.";
       $this->erro_campo = "si06_segmento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->si06_servidor = $si06_servidor; 
     if(($this->si06_servidor == null) || ($this->si06_servidor == "") ){ 
       $this->erro_sql = " Campo si06_servidor não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into siopeservidormanutencao(
                                       si06_servidor 
                                      ,si06_categoria 
                                      ,si06_situacao 
                                      ,si06_segmento 
                       )
                values (
                                $this->si06_servidor 
                               ,$this->si06_categoria 
                               ,$this->si06_situacao 
                               ,$this->si06_segmento 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "siopeservidormanutencao ($this->si06_servidor) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "siopeservidormanutencao já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "siopeservidormanutencao ($this->si06_servidor) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->si06_servidor;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->si06_servidor  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,214654101,'$this->si06_servidor','I')");
         $resac = db_query("insert into db_acount values($acount,268151922,214654101,'','".AddSlashes(pg_result($resaco,0,'si06_servidor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,268151922,305693204,'','".AddSlashes(pg_result($resaco,0,'si06_categoria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,268151922,210820919,'','".AddSlashes(pg_result($resaco,0,'si06_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,268151922,164388043,'','".AddSlashes(pg_result($resaco,0,'si06_segmento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($si06_servidor=null)
    {
      $this->atualizacampos();
     $sql = " update siopeservidormanutencao set ";
     $virgula = "";
     if(trim($this->si06_servidor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["si06_servidor"])){ 
       $sql  .= $virgula." si06_servidor = $this->si06_servidor ";
       $virgula = ",";
       if(trim($this->si06_servidor) == null ){ 
         $this->erro_sql = " Campo Matricula do servidor não informado.";
         $this->erro_campo = "si06_servidor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->si06_categoria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["si06_categoria"])){ 
       $sql  .= $virgula." si06_categoria = $this->si06_categoria ";
       $virgula = ",";
       if(trim($this->si06_categoria) == null ){ 
         $this->erro_sql = " Campo Categoria do Servidor não informado.";
         $this->erro_campo = "si06_categoria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->si06_situacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["si06_situacao"])){ 
       $sql  .= $virgula." si06_situacao = $this->si06_situacao ";
       $virgula = ",";
       if(trim($this->si06_situacao) == null ){ 
         $this->erro_sql = " Campo Situação do Servidor não informado.";
         $this->erro_campo = "si06_situacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->si06_segmento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["si06_segmento"])){ 
       $sql  .= $virgula." si06_segmento = $this->si06_segmento ";
       $virgula = ",";
       if(trim($this->si06_segmento) == null ){ 
         $this->erro_sql = " Campo Segmento de Atuação do Servidor não informado.";
         $this->erro_campo = "si06_segmento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($si06_servidor!=null){
       $sql .= " si06_servidor = $this->si06_servidor";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->si06_servidor));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,214654101,'$this->si06_servidor','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["si06_servidor"]) || $this->si06_servidor != "")
             $resac = db_query("insert into db_acount values($acount,268151922,214654101,'".AddSlashes(pg_result($resaco,$conresaco,'si06_servidor'))."','$this->si06_servidor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["si06_categoria"]) || $this->si06_categoria != "")
             $resac = db_query("insert into db_acount values($acount,268151922,305693204,'".AddSlashes(pg_result($resaco,$conresaco,'si06_categoria'))."','$this->si06_categoria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["si06_situacao"]) || $this->si06_situacao != "")
             $resac = db_query("insert into db_acount values($acount,268151922,210820919,'".AddSlashes(pg_result($resaco,$conresaco,'si06_situacao'))."','$this->si06_situacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["si06_segmento"]) || $this->si06_segmento != "")
             $resac = db_query("insert into db_acount values($acount,268151922,164388043,'".AddSlashes(pg_result($resaco,$conresaco,'si06_segmento'))."','$this->si06_segmento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "siopeservidormanutencao não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->si06_servidor;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "siopeservidormanutencao não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->si06_servidor;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->si06_servidor;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($si06_servidor=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($si06_servidor));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,214654101,'$si06_servidor','E')");
           $resac  = db_query("insert into db_acount values($acount,268151922,214654101,'','".AddSlashes(pg_result($resaco,$iresaco,'si06_servidor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,268151922,305693204,'','".AddSlashes(pg_result($resaco,$iresaco,'si06_categoria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,268151922,210820919,'','".AddSlashes(pg_result($resaco,$iresaco,'si06_situacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,268151922,164388043,'','".AddSlashes(pg_result($resaco,$iresaco,'si06_segmento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from siopeservidormanutencao
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($si06_servidor)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " si06_servidor = $si06_servidor ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "siopeservidormanutencao não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$si06_servidor;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "siopeservidormanutencao não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$si06_servidor;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$si06_servidor;
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
        $this->erro_sql   = "Record Vazio na Tabela:siopeservidormanutencao";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($si06_servidor = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from siopeservidormanutencao ";
     $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = siopeservidormanutencao.si06_servidor";
     $sql .= "      inner join siopecategoria  on  siopecategoria.si03_id = siopeservidormanutencao.si06_categoria";
     $sql .= "      inner join siopesituacao  on  siopesituacao.si01_id = siopeservidormanutencao.si06_situacao";
     $sql .= "      inner join siopesegmentoatuacao  on  siopesegmentoatuacao.si07_segmento = siopeservidormanutencao.si06_segmento";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
     $sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
     $sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
     $sql .= "      left  join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
     $sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
     $sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
     $sql .= "      left  join rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
     $sql .= "      inner join rhreajusteparidade  on  rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
     $sql .= "      inner join siopecategoriatipo  on  siopecategoriatipo.si02_id = siopecategoria.si03_siopecategoriatipo";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($si06_servidor)) {
         $sql2 .= " where siopeservidormanutencao.si06_servidor = $si06_servidor "; 
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

    public function sql_query_file($si06_servidor = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from siopeservidormanutencao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($si06_servidor)){
         $sql2 .= " where siopeservidormanutencao.si06_servidor = $si06_servidor "; 
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

  public function sql_query_dados_manutencao($si06_servidor = null, $campos = "*", $ordem = null, $dbwhere = "") {

    $sql  = "select {$campos}";
    $sql .= "  from siopeservidormanutencao ";
    $sql .= "       inner join siopecategoria on siopecategoria.si03_id = siopeservidormanutencao.si06_categoria";
    $sql .= "       inner join siopesituacao on siopesituacao.si01_id = siopeservidormanutencao.si06_situacao";
    $sql .= "       inner join siopecategoriatipo on siopecategoriatipo.si02_id = siopecategoria.si03_siopecategoriatipo";
    $sql2 = "";

    if (empty($dbwhere)) {
       if (!empty($si06_servidor)) {
          $sql2 .= " where siopeservidormanutencao.si06_servidor = $si06_servidor ";
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
