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

class cl_acordoitem
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
    public $ac20_sequencial = 0;
    public $ac20_acordoposicao = 0;
    public $ac20_pcmater = 0;
    public $ac20_quantidade = 0;
    public $ac20_valorunitario = 0;
    public $ac20_valortotal = 0;
    public $ac20_elemento = 0;
    public $ac20_ordem = 0;
    public $ac20_matunid = 0;
    public $ac20_resumo = null;
    public $ac20_tipocontrole = 0;
    public $ac20_servicoquantidade = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ac20_sequencial = int4 = Sequencial
                 ac20_acordoposicao = int4 = Acordo
                 ac20_pcmater = int4 = Código do Item
                 ac20_quantidade = int4 = Quantidade
                 ac20_valorunitario = float8 = Valor Unitário
                 ac20_valortotal = float8 = Valor Total
                 ac20_elemento = int4 = Desdobramento
                 ac20_ordem = int4 = Ordem
                 ac20_matunid = int4 = Unidade
                 ac20_resumo = text = Resumo
                 ac20_tipocontrole = int4 = Forma de Controle
                 ac20_servicoquantidade = bool = Serviço Controlado por Quantidade
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("acordoitem");
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
       $this->ac20_sequencial = ($this->ac20_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_sequencial"]:$this->ac20_sequencial);
       $this->ac20_acordoposicao = ($this->ac20_acordoposicao == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_acordoposicao"]:$this->ac20_acordoposicao);
       $this->ac20_pcmater = ($this->ac20_pcmater == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_pcmater"]:$this->ac20_pcmater);
       $this->ac20_quantidade = ($this->ac20_quantidade === ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_quantidade"]:$this->ac20_quantidade);
       $this->ac20_valorunitario = ($this->ac20_valorunitario === ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_valorunitario"]:$this->ac20_valorunitario);
       $this->ac20_valortotal = ($this->ac20_valortotal === ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_valortotal"]:$this->ac20_valortotal);
       $this->ac20_elemento = ($this->ac20_elemento == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_elemento"]:$this->ac20_elemento);
       $this->ac20_ordem = ($this->ac20_ordem == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_ordem"]:$this->ac20_ordem);
       $this->ac20_matunid = ($this->ac20_matunid == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_matunid"]:$this->ac20_matunid);
       $this->ac20_resumo = ($this->ac20_resumo == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_resumo"]:$this->ac20_resumo);
       $this->ac20_tipocontrole = ($this->ac20_tipocontrole == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_tipocontrole"]:$this->ac20_tipocontrole);
       $this->ac20_servicoquantidade = ($this->ac20_servicoquantidade === null?'f':$this->ac20_servicoquantidade);
     }else{
       $this->ac20_sequencial = ($this->ac20_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ac20_sequencial"]:$this->ac20_sequencial);
     }
   }

    public function incluir($ac20_sequencial)
    {


      $this->atualizacampos();
     if($this->ac20_acordoposicao == null ){
       $this->erro_sql = " Campo Acordo não informado.";
       $this->erro_campo = "ac20_acordoposicao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_pcmater == null ){
       $this->erro_sql = " Campo Código do Item não informado.";
       $this->erro_campo = "ac20_pcmater";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_quantidade === null ){
       $this->erro_sql = " Campo Quantidade não informado.";
       $this->erro_campo = "ac20_quantidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_valorunitario === null ){
       $this->erro_sql = " Campo Valor Unitário não informado.";
       $this->erro_campo = "ac20_valorunitario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_valortotal === null ){
       $this->erro_sql = " Campo Valor Total não informado.";
       $this->erro_campo = "ac20_valortotal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_elemento == null ){
       $this->erro_sql = " Campo Desdobramento não informado.";
       $this->erro_campo = "ac20_elemento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_ordem == null ){
       $this->erro_sql = " Campo Ordem não informado.";
       $this->erro_campo = "ac20_ordem";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_matunid == null ){
       $this->erro_sql = " Campo Unidade não informado.";
       $this->erro_campo = "ac20_matunid";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ac20_tipocontrole == null ){
       $this->erro_sql = " Campo Forma de Controle não informado.";
       $this->erro_campo = "ac20_tipocontrole";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->ac20_servicoquantidade === null ){
       $this->erro_sql = " Campo Serviço Controlado por Quantidade não informado.";
       $this->erro_campo = "ac20_servicoquantidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ac20_sequencial == "" || $ac20_sequencial == null ){
       $result = db_query("select nextval('acordoitem_ac20_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: acordoitem_ac20_sequencial_seq do campo: ac20_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ac20_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from acordoitem_ac20_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $ac20_sequencial)){
         $this->erro_sql = " Campo ac20_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ac20_sequencial = $ac20_sequencial;
       }
     }
     if(($this->ac20_sequencial == null) || ($this->ac20_sequencial == "") ){
       $this->erro_sql = " Campo ac20_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into acordoitem(
                                       ac20_sequencial
                                      ,ac20_acordoposicao
                                      ,ac20_pcmater
                                      ,ac20_quantidade
                                      ,ac20_valorunitario
                                      ,ac20_valortotal
                                      ,ac20_elemento
                                      ,ac20_ordem
                                      ,ac20_matunid
                                      ,ac20_resumo
                                      ,ac20_tipocontrole
                                      ,ac20_servicoquantidade
                       )
                values (
                                $this->ac20_sequencial
                               ,$this->ac20_acordoposicao
                               ,$this->ac20_pcmater
                               ,$this->ac20_quantidade
                               ,$this->ac20_valorunitario
                               ,$this->ac20_valortotal
                               ,$this->ac20_elemento
                               ,$this->ac20_ordem
                               ,$this->ac20_matunid
                               ,'$this->ac20_resumo'
                               ,$this->ac20_tipocontrole
                               ,'$this->ac20_servicoquantidade'
                      )";


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Acordo Item ($this->ac20_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Acordo Item já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Acordo Item ($this->ac20_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ac20_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ac20_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,16170,'$this->ac20_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,2837,16170,'','".AddSlashes(pg_result($resaco,0,'ac20_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16171,'','".AddSlashes(pg_result($resaco,0,'ac20_acordoposicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16172,'','".AddSlashes(pg_result($resaco,0,'ac20_pcmater'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16173,'','".AddSlashes(pg_result($resaco,0,'ac20_quantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16174,'','".AddSlashes(pg_result($resaco,0,'ac20_valorunitario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16175,'','".AddSlashes(pg_result($resaco,0,'ac20_valortotal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16176,'','".AddSlashes(pg_result($resaco,0,'ac20_elemento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16232,'','".AddSlashes(pg_result($resaco,0,'ac20_ordem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16581,'','".AddSlashes(pg_result($resaco,0,'ac20_matunid'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,16582,'','".AddSlashes(pg_result($resaco,0,'ac20_resumo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,18056,'','".AddSlashes(pg_result($resaco,0,'ac20_tipocontrole'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2837,1013465,'','".AddSlashes(pg_result($resaco,0,'ac20_servicoquantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar($ac20_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update acordoitem set ";
     $virgula = "";
     if(trim($this->ac20_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_sequencial"])){
       $sql  .= $virgula." ac20_sequencial = $this->ac20_sequencial ";
       $virgula = ",";
       if(trim($this->ac20_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "ac20_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_acordoposicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_acordoposicao"])){
       $sql  .= $virgula." ac20_acordoposicao = $this->ac20_acordoposicao ";
       $virgula = ",";
       if(trim($this->ac20_acordoposicao) == null ){
         $this->erro_sql = " Campo Acordo não informado.";
         $this->erro_campo = "ac20_acordoposicao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_pcmater)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_pcmater"])){
       $sql  .= $virgula." ac20_pcmater = $this->ac20_pcmater ";
       $virgula = ",";
       if(trim($this->ac20_pcmater) == null ){
         $this->erro_sql = " Campo Código do Item não informado.";
         $this->erro_campo = "ac20_pcmater";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_quantidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_quantidade"])){
       $sql  .= $virgula." ac20_quantidade = $this->ac20_quantidade ";
       $virgula = ",";
       if(trim($this->ac20_quantidade) == null ){
         $this->erro_sql = " Campo Quantidade não informado.";
         $this->erro_campo = "ac20_quantidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_valorunitario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_valorunitario"])){
       $sql  .= $virgula." ac20_valorunitario = $this->ac20_valorunitario ";
       $virgula = ",";
       if(trim($this->ac20_valorunitario) == null ){
         $this->erro_sql = " Campo Valor Unitário não informado.";
         $this->erro_campo = "ac20_valorunitario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_valortotal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_valortotal"])){
       $sql  .= $virgula." ac20_valortotal = $this->ac20_valortotal ";
       $virgula = ",";
       if(trim($this->ac20_valortotal) == null ){
         $this->erro_sql = " Campo Valor Total não informado.";
         $this->erro_campo = "ac20_valortotal";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_elemento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_elemento"])){
       $sql  .= $virgula." ac20_elemento = $this->ac20_elemento ";
       $virgula = ",";
       if(trim($this->ac20_elemento) == null ){
         $this->erro_sql = " Campo Desdobramento não informado.";
         $this->erro_campo = "ac20_elemento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_ordem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_ordem"])){
       $sql  .= $virgula." ac20_ordem = $this->ac20_ordem ";
       $virgula = ",";
       if(trim($this->ac20_ordem) == null ){
         $this->erro_sql = " Campo Ordem não informado.";
         $this->erro_campo = "ac20_ordem";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_matunid)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_matunid"])){
       $sql  .= $virgula." ac20_matunid = $this->ac20_matunid ";
       $virgula = ",";
       if(trim($this->ac20_matunid) == null ){
         $this->erro_sql = " Campo Unidade não informado.";
         $this->erro_campo = "ac20_matunid";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_resumo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_resumo"])){
       $sql  .= $virgula." ac20_resumo = '$this->ac20_resumo' ";
       $virgula = ",";
     }
     if(trim($this->ac20_tipocontrole)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_tipocontrole"])){
       $sql  .= $virgula." ac20_tipocontrole = $this->ac20_tipocontrole ";
       $virgula = ",";
       if(trim($this->ac20_tipocontrole) == null ){
         $this->erro_sql = " Campo Forma de Controle não informado.";
         $this->erro_campo = "ac20_tipocontrole";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ac20_servicoquantidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ac20_servicoquantidade"])){
       $sql  .= $virgula." ac20_servicoquantidade = '$this->ac20_servicoquantidade' ";
       $virgula = ",";
       if(trim($this->ac20_servicoquantidade) == null ){
         $this->erro_sql = " Campo Serviço Controlado por Quantidade não informado.";
         $this->erro_campo = "ac20_servicoquantidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ac20_sequencial!=null){
       $sql .= " ac20_sequencial = $this->ac20_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ac20_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,16170,'$this->ac20_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_sequencial"]) || $this->ac20_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,2837,16170,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_sequencial'))."','$this->ac20_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_acordoposicao"]) || $this->ac20_acordoposicao != "")
             $resac = db_query("insert into db_acount values($acount,2837,16171,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_acordoposicao'))."','$this->ac20_acordoposicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_pcmater"]) || $this->ac20_pcmater != "")
             $resac = db_query("insert into db_acount values($acount,2837,16172,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_pcmater'))."','$this->ac20_pcmater',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_quantidade"]) || $this->ac20_quantidade != "")
             $resac = db_query("insert into db_acount values($acount,2837,16173,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_quantidade'))."','$this->ac20_quantidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_valorunitario"]) || $this->ac20_valorunitario != "")
             $resac = db_query("insert into db_acount values($acount,2837,16174,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_valorunitario'))."','$this->ac20_valorunitario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_valortotal"]) || $this->ac20_valortotal != "")
             $resac = db_query("insert into db_acount values($acount,2837,16175,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_valortotal'))."','$this->ac20_valortotal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_elemento"]) || $this->ac20_elemento != "")
             $resac = db_query("insert into db_acount values($acount,2837,16176,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_elemento'))."','$this->ac20_elemento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_ordem"]) || $this->ac20_ordem != "")
             $resac = db_query("insert into db_acount values($acount,2837,16232,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_ordem'))."','$this->ac20_ordem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_matunid"]) || $this->ac20_matunid != "")
             $resac = db_query("insert into db_acount values($acount,2837,16581,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_matunid'))."','$this->ac20_matunid',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_resumo"]) || $this->ac20_resumo != "")
             $resac = db_query("insert into db_acount values($acount,2837,16582,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_resumo'))."','$this->ac20_resumo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_tipocontrole"]) || $this->ac20_tipocontrole != "")
             $resac = db_query("insert into db_acount values($acount,2837,18056,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_tipocontrole'))."','$this->ac20_tipocontrole',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ac20_servicoquantidade"]) || $this->ac20_servicoquantidade != "")
             $resac = db_query("insert into db_acount values($acount,2837,1013465,'".AddSlashes(pg_result($resaco,$conresaco,'ac20_servicoquantidade'))."','$this->ac20_servicoquantidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Acordo Item não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ac20_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Acordo Item não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ac20_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ac20_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($ac20_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ac20_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,16170,'$ac20_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,2837,16170,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16171,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_acordoposicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16172,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_pcmater'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16173,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_quantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16174,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_valorunitario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16175,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_valortotal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16176,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_elemento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16232,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_ordem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16581,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_matunid'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,16582,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_resumo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,18056,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_tipocontrole'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2837,1013465,'','".AddSlashes(pg_result($resaco,$iresaco,'ac20_servicoquantidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from acordoitem
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ac20_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ac20_sequencial = $ac20_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Acordo Item não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ac20_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Acordo Item não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ac20_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ac20_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:acordoitem";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ac20_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from acordoitem ";
     $sql .= "      inner join pcmater  on  pcmater.pc01_codmater = acordoitem.ac20_pcmater";
     $sql .= "      inner join matunid  on  matunid.m61_codmatunid = acordoitem.ac20_matunid";
     $sql .= "      inner join acordoposicao  on  acordoposicao.ac26_sequencial = acordoitem.ac20_acordoposicao";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = pcmater.pc01_id_usuario";
     $sql .= "      inner join pcsubgrupo  on  pcsubgrupo.pc04_codsubgrupo = pcmater.pc01_codsubgrupo";
     $sql .= "      inner join acordo  as a on   a.ac16_sequencial = acordoposicao.ac26_acordo";
     $sql .= "      inner join acordoposicaotipo  on  acordoposicaotipo.ac27_sequencial = acordoposicao.ac26_acordoposicaotipo";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ac20_sequencial)) {
         $sql2 .= " where acordoitem.ac20_sequencial = $ac20_sequencial ";
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

    public function sql_query_file($ac20_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from acordoitem ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ac20_sequencial)){
         $sql2 .= " where acordoitem.ac20_sequencial = $ac20_sequencial ";
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

   function sql_query_material( $ac20_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from acordoitem ";
     $sql .= "      inner join pcmater     on  pcmater.pc01_codmater  = acordoitem.ac20_pcmater";
     $sql .= "      inner join matunid     on  matunid.m61_codmatunid = acordoitem.ac20_matunid";
     $sql .= "      inner join orcelemento on orcelemento.o56_codele  = acordoitem.ac20_elemento";
     $sql .= "                            and orcelemento.o56_anousu  = ".db_getsession("DB_anousu");
     $sql2 = "";
     if($dbwhere==""){
       if($ac20_sequencial!=null ){
         $sql2 .= " where acordoitem.ac20_sequencial = $ac20_sequencial ";
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
   *
   * Busca os períodos do item.
   * @return string SQL
   */
  function sql_query_periodo($ac20_sequencial=null, $campos="*", $ordem=null, $dbwhere="") {
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
     $sql .= " from acordoitem ";
     $sql .= "      inner join acordoitemperiodo on acordoitemperiodo.ac41_acordoitem = acordoitem.ac20_sequencial ";
     $sql2 = "";
     if ($dbwhere=="") {
       if($ac20_sequencial!=null ){
         $sql2 .= " where acordoitem.ac20_sequencial = $ac20_sequencial ";
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
   function sql_query_completo( $ac20_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $iAnoSessao = db_getsession("DB_anousu");
     $sql .=" from acordoitem";
     $sql .="   inner join pcmater                            on pcmater.pc01_codmater = acordoitem.ac20_pcmater";
     $sql .="   inner join matunid                            on matunid.m61_codmatunid = acordoitem.ac20_matunid";
     $sql .="   inner join orcelemento                        on orcelemento.o56_codele = acordoitem.ac20_elemento";
     $sql .="                                                and orcelemento.o56_anousu = {$iAnoSessao}";
     $sql .="   left  join acordoliclicitem                   on acordoliclicitem.ac24_acordoitem = acordoitem.ac20_sequencial";
     $sql .="   left  join liclicitem                         on liclicitem.l21_codigo = acordoliclicitem.ac24_liclicitem";
     $sql .="   left  join pcprocitem as pcprocitem_licitacao on pcprocitem_licitacao.pc81_codprocitem = liclicitem.l21_codpcprocitem";
     $sql .="   left  join solicitem as solicitem_licitacao   on solicitem_licitacao.pc11_codigo = pcprocitem_licitacao.pc81_solicitem";
     $sql .="   left  join acordopcprocitem                   on ac20_sequencial = ac23_acordoitem";
     $sql .="   left  join pcprocitem as pcprocitem_compras   on pcprocitem_compras.pc81_codprocitem = acordopcprocitem.ac23_pcprocitem";
     $sql .="   left  join solicitem as solicitem_compras     on pcprocitem_compras.pc81_solicitem = solicitem_compras.pc11_codigo";
     $sql .="   left  join acordoempempitem                   on ac44_acordoitem = ac20_sequencial";
     $sql .="   left  join empempitem                         on ac44_empempitem = e62_sequencial";
     $sql .="   left  join empempenho                         on empempenho.e60_numemp = empempitem.e62_numemp";
     $sql .="   left  join acordoitemvinculo                  on ac33_acordoitemfilho = ac20_sequencial";

     $sql2 = "";
     if ($dbwhere=="") {
       if($ac20_sequencial!=null ){
         $sql2 .= " where acordoitem.ac20_sequencial = $ac20_sequencial ";
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
   * Retorna os dados dos itens para o portal da transparência
   *
   * @param  string $sCampos
   * @param  string $sOrdem
   * @param  string $sWhere
   * @return string
   */
  public function sql_query_transparencia($sCampos = "*", $sOrdem = null, $sWhere = "") {

    $sSql  = "select {$sCampos} \n";
    $sSql .= "  from acordoitem                                                       \n";
    $sSql .= "       left join acordoliclicitem on ac24_acordoitem = ac20_sequencial  \n";
    $sSql .= "       inner join acordoposicao on ac26_sequencial = ac20_acordoposicao \n";
    $sSql .= "       inner join acordo on ac16_sequencial = ac26_acordo               \n";
    $sSql .= "       left join pcmater on pc01_codmater = ac20_pcmater                \n";
    $sSql .= "       left join matunid on m61_codmatunid = ac20_matunid               \n";

    if (!empty($sWhere)) {
      $sSql .= " where {$sWhere} \n";
    }

    if (!empty($sOrdem)) {
      $sSql .= " order by {$sOrdem} ";
    }

    return $sSql;
  }
}
