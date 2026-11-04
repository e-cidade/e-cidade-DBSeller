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

//MODULO: protocolo
//CLASSE DA ENTIDADE tipoproc
class cl_tipoproc
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
    public $p51_codigo = 0;
    public $p51_descr = null;
    public $p51_dtlimite_dia = null;
    public $p51_dtlimite_mes = null;
    public $p51_dtlimite_ano = null;
    public $p51_dtlimite = null;
    public $p51_instit = 0;
    public $p51_tipoprocgrupo = 0;
    public $p51_identificado = 'f';
    public $p51_prottipodocumentoprocesso = 0;
    public $p51_linksaibamais = null;
    public $p51_itemmenu = null;
    public $p51_mensagem = null;
    public $p51_relatorio = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 p51_codigo = int4 = Tipo de processo
                 p51_descr = varchar(60) = Descrição
                 p51_dtlimite = date = Data Limite
                 p51_instit = int4 = Cod. Instituição
                 p51_tipoprocgrupo = int4 = Tipo Processo Grupo
                 p51_identificado = bool = Identificado
                 p51_prottipodocumentoprocesso = int4 = Tipo de Documento
                 p51_linksaibamais = varchar(255) = link para sabermais
                 p51_itemmenu = varchar(100) = item de menu
                 p51_mensagem = varchar(250) = p51_mensagem
                 p51_relatorio = int8 = Modelo
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("tipoproc");
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
       $this->p51_codigo = ($this->p51_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_codigo"]:$this->p51_codigo);
       $this->p51_descr = ($this->p51_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_descr"]:$this->p51_descr);
       if($this->p51_dtlimite == ""){
         $this->p51_dtlimite_dia = ($this->p51_dtlimite_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_dtlimite_dia"]:$this->p51_dtlimite_dia);
         $this->p51_dtlimite_mes = ($this->p51_dtlimite_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_dtlimite_mes"]:$this->p51_dtlimite_mes);
         $this->p51_dtlimite_ano = ($this->p51_dtlimite_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_dtlimite_ano"]:$this->p51_dtlimite_ano);
         if($this->p51_dtlimite_dia != ""){
            $this->p51_dtlimite = $this->p51_dtlimite_ano."-".$this->p51_dtlimite_mes."-".$this->p51_dtlimite_dia;
         }
       }
       $this->p51_instit = ($this->p51_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_instit"]:$this->p51_instit);
       $this->p51_tipoprocgrupo = ($this->p51_tipoprocgrupo == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_tipoprocgrupo"]:$this->p51_tipoprocgrupo);
       $this->p51_identificado = ($this->p51_identificado == "f"?@$GLOBALS["HTTP_POST_VARS"]["p51_identificado"]:$this->p51_identificado);
            $this->p51_prottipodocumentoprocesso = ($this->p51_prottipodocumentoprocesso == "f" ? @$GLOBALS["HTTP_POST_VARS"]["p51_prottipodocumentoprocesso"] : $this->p51_prottipodocumentoprocesso);
       $this->p51_mensagem = ($this->p51_mensagem == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_mensagem"]:$this->p51_mensagem);
       $this->p51_relatorio = ($this->p51_relatorio == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_relatorio"]:$this->p51_relatorio);
     }else{
       $this->p51_codigo = ($this->p51_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["p51_codigo"]:$this->p51_codigo);
     }
   }

    public function incluir($p51_codigo)
    {
      $this->atualizacampos();
     if($this->p51_descr == null ){
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "p51_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->p51_dtlimite == null ){
       $this->p51_dtlimite = "null";
     }
     if($this->p51_instit == null ){
       $this->erro_sql = " Campo Cod. Instituição não informado.";
       $this->erro_campo = "p51_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->p51_tipoprocgrupo == null ){
       $this->erro_sql = " Campo Tipo Processo Grupo não informado.";
       $this->erro_campo = "p51_tipoprocgrupo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->p51_identificado == null ){
       $this->erro_sql = " Campo Identificado não informado.";
       $this->erro_campo = "p51_identificado";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->p51_prottipodocumentoprocesso == null ){
       $this->erro_sql = " Campo Tipo de Documento não informado.";
       $this->erro_campo = "p51_prottipodocumentoprocesso";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->p51_relatorio == null ){
       $this->p51_relatorio = "null";
     }
     if($p51_codigo == "" || $p51_codigo == null ){
       $result = db_query("select nextval('tipoproc_p51_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: tipoproc_p51_codigo_seq do campo: p51_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->p51_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from tipoproc_p51_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $p51_codigo)){
         $this->erro_sql = " Campo p51_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->p51_codigo = $p51_codigo;
       }
     }
     if(($this->p51_codigo == null) || ($this->p51_codigo == "") ){
       $this->erro_sql = " Campo p51_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into tipoproc(
                                       p51_codigo
                                      ,p51_descr
                                      ,p51_dtlimite
                                      ,p51_instit
                                      ,p51_tipoprocgrupo
                                      ,p51_identificado
                                      ,p51_prottipodocumentoprocesso
                                      ,p51_linksaibamais
                                      ,p51_itemmenu
                                      ,p51_mensagem
                                      ,p51_relatorio
                       )
                values (
                                $this->p51_codigo
                               ,'$this->p51_descr'
                               ,".($this->p51_dtlimite == "null" || $this->p51_dtlimite == ""?"null":"'".$this->p51_dtlimite."'")."
                               ,$this->p51_instit
                               ,$this->p51_tipoprocgrupo
                               ,'$this->p51_identificado'
                               ,$this->p51_prottipodocumentoprocesso
                               ,'$this->p51_linksaibamais'
                               ,'$this->p51_itemmenu'
                               ,'$this->p51_mensagem'
                               ,$this->p51_relatorio
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Tipos de Processo ($this->p51_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Tipos de Processo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Tipos de Processo ($this->p51_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->p51_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
       $resaco = $this->sql_record($this->sql_query_file($this->p51_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,2429,'$this->p51_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,393,2429,'','".AddSlashes(pg_result($resaco,0,'p51_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,2430,'','".AddSlashes(pg_result($resaco,0,'p51_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,6143,'','".AddSlashes(pg_result($resaco,0,'p51_dtlimite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,10681,'','".AddSlashes(pg_result($resaco,0,'p51_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,14732,'','".AddSlashes(pg_result($resaco,0,'p51_tipoprocgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,14735,'','".AddSlashes(pg_result($resaco,0,'p51_identificado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,1011758,'','".AddSlashes(pg_result($resaco,0,'p51_prottipodocumentoprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,1013145,'','".AddSlashes(pg_result($resaco,0,'p51_linksaibamais'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,1013158,'','".AddSlashes(pg_result($resaco,0,'p51_itemmenu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,1013406,'','".AddSlashes(pg_result($resaco,0,'p51_mensagem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,393,1014629,'','".AddSlashes(pg_result($resaco,0,'p51_relatorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }

     return true;
   }

    public function alterar($p51_codigo=null)
    {
      $this->atualizacampos();

     $sql = " update tipoproc set ";
     $virgula = "";
     if(trim($this->p51_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_codigo"])){
       $sql  .= $virgula." p51_codigo = $this->p51_codigo ";
       $virgula = ",";
       if(trim($this->p51_codigo) == null ){
         $this->erro_sql = " Campo Tipo de processo não informado.";
         $this->erro_campo = "p51_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->p51_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_descr"])){
       $sql  .= $virgula." p51_descr = '$this->p51_descr' ";
       $virgula = ",";
       if(trim($this->p51_descr) == null ){
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "p51_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->p51_dtlimite)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_dtlimite_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["p51_dtlimite_dia"] !="") ){
       $sql  .= $virgula." p51_dtlimite = '$this->p51_dtlimite' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["p51_dtlimite_dia"])){
         $sql  .= $virgula." p51_dtlimite = null ";
         $virgula = ",";
       }
     }
     if(trim($this->p51_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_instit"])){
       $sql  .= $virgula." p51_instit = $this->p51_instit ";
       $virgula = ",";
       if(trim($this->p51_instit) == null ){
         $this->erro_sql = " Campo Cod. Instituição não informado.";
         $this->erro_campo = "p51_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->p51_tipoprocgrupo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_tipoprocgrupo"])){
       $sql  .= $virgula." p51_tipoprocgrupo = $this->p51_tipoprocgrupo ";
       $virgula = ",";
       if(trim($this->p51_tipoprocgrupo) == null ){
         $this->erro_sql = " Campo Tipo Processo Grupo não informado.";
         $this->erro_campo = "p51_tipoprocgrupo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->p51_identificado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_identificado"])){
       $sql  .= $virgula." p51_identificado = '$this->p51_identificado' ";
       $virgula = ",";
       if(trim($this->p51_identificado) == null ){
         $this->erro_sql = " Campo Identificado não informado.";
         $this->erro_campo = "p51_identificado";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->p51_prottipodocumentoprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_prottipodocumentoprocesso"])){
            $sql .= $virgula . " p51_prottipodocumentoprocesso = '$this->p51_prottipodocumentoprocesso' ";
       $virgula = ",";
       if(trim($this->p51_prottipodocumentoprocesso) == null ){
         $this->erro_sql = " Campo Tipo de Documento não informado.";
         $this->erro_campo = "p51_prottipodocumentoprocesso";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->p51_linksaibamais)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_linksaibamais"])){
       $sql  .= $virgula." p51_linksaibamais = '$this->p51_linksaibamais' ";
     }
     if(trim($this->p51_itemmenu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_itemmenu"])){
       $sql  .= $virgula." p51_itemmenu = '$this->p51_itemmenu' ";
     }
     if(trim($this->p51_mensagem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_mensagem"])){
       $sql  .= $virgula." p51_mensagem = '$this->p51_mensagem' ";
     }
     if(trim($this->p51_relatorio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p51_relatorio"])){
        if(trim($this->p51_relatorio)=="" && isset($GLOBALS["HTTP_POST_VARS"]["p51_relatorio"])){
           $this->p51_relatorio = "null" ;
        }
       $sql  .= $virgula." p51_relatorio = $this->p51_relatorio ";
     }
     $sql .= " where ";
     if($p51_codigo!=null){
       $sql .= " p51_codigo = $this->p51_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->p51_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,2429,'$this->p51_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_codigo"]) || $this->p51_codigo != "")
             $resac = db_query("insert into db_acount values($acount,393,2429,'".AddSlashes(pg_result($resaco,$conresaco,'p51_codigo'))."','$this->p51_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_descr"]) || $this->p51_descr != "")
             $resac = db_query("insert into db_acount values($acount,393,2430,'".AddSlashes(pg_result($resaco,$conresaco,'p51_descr'))."','$this->p51_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_dtlimite"]) || $this->p51_dtlimite != "")
             $resac = db_query("insert into db_acount values($acount,393,6143,'".AddSlashes(pg_result($resaco,$conresaco,'p51_dtlimite'))."','$this->p51_dtlimite',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_instit"]) || $this->p51_instit != "")
             $resac = db_query("insert into db_acount values($acount,393,10681,'".AddSlashes(pg_result($resaco,$conresaco,'p51_instit'))."','$this->p51_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_tipoprocgrupo"]) || $this->p51_tipoprocgrupo != "")
             $resac = db_query("insert into db_acount values($acount,393,14732,'".AddSlashes(pg_result($resaco,$conresaco,'p51_tipoprocgrupo'))."','$this->p51_tipoprocgrupo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_identificado"]) || $this->p51_identificado != "")
             $resac = db_query("insert into db_acount values($acount,393,14735,'".AddSlashes(pg_result($resaco,$conresaco,'p51_identificado'))."','$this->p51_identificado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_prottipodocumentoprocesso"]) || $this->p51_prottipodocumentoprocesso != "")
             $resac = db_query("insert into db_acount values($acount,393,1011758,'".AddSlashes(pg_result($resaco,$conresaco,'p51_prottipodocumentoprocesso'))."','$this->p51_prottipodocumentoprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_linksaibamais"]) || $this->p51_linksaibamais != "")
             $resac = db_query("insert into db_acount values($acount,393,1013145,'".AddSlashes(pg_result($resaco,$conresaco,'p51_linksaibamais'))."','$this->p51_linksaibamais',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_itemmenu"]) || $this->p51_itemmenu != "")
             $resac = db_query("insert into db_acount values($acount,393,1013158,'".AddSlashes(pg_result($resaco,$conresaco,'p51_itemmenu'))."','$this->p51_itemmenu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_mensagem"]) || $this->p51_mensagem != "")
             $resac = db_query("insert into db_acount values($acount,393,1013406,'".AddSlashes(pg_result($resaco,$conresaco,'p51_mensagem'))."','$this->p51_mensagem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["p51_relatorio"]) || $this->p51_relatorio != "")
             $resac = db_query("insert into db_acount values($acount,393,1014629,'".AddSlashes(pg_result($resaco,$conresaco,'p51_relatorio'))."','$this->p51_relatorio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipos de Processo não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->p51_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tipos de Processo não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->p51_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->p51_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($p51_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($p51_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,2429,'$p51_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,393,2429,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,2430,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,6143,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_dtlimite'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,10681,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,14732,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_tipoprocgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,14735,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_identificado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,1011758,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_prottipodocumentoprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,1013145,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_linksaibamais'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,1013158,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_itemmenu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,1013406,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_mensagem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,393,1014629,'','".AddSlashes(pg_result($resaco,$iresaco,'p51_relatorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from tipoproc
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($p51_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " p51_codigo = $p51_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Tipos de Processo não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$p51_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Tipos de Processo não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$p51_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$p51_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:tipoproc";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    // funcao do sql
    public function sql_query($p51_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }

        $sql .= " from tipoproc ";
        $sql .= "      inner join db_config     on db_config.codigo             = tipoproc.p51_instit";
        $sql .= "      inner join tipoprocgrupo on tipoprocgrupo.p40_sequencial = tipoproc.p51_tipoprocgrupo";
        $sql .= "      inner join cgm           on cgm.z01_numcgm               = db_config.numcgm";
        $sql .= "      inner join prottipodocumentoprocesso on tipoproc.p51_prottipodocumentoprocesso = prottipodocumentoprocesso.p91_sequencial";
        $sql .= "      left join tipoprocformareclamacao ON tipoprocformareclamacao.p43_tipoproc = tipoproc.p51_codigo";
        $sql .= "      left join formareclamacao on tipoprocformareclamacao.p43_formareclamacao = formareclamacao.p42_sequencial ";
        // Alteracao Plugin TaxonomiaDeProcessosDoMinisterioPublico - db_tipoproc_classe #1
        $sql2 = "";
        if ($dbwhere == "") {
            if ($p51_codigo != null) {
                $sql2 .= " where tipoproc.p51_codigo = $p51_codigo ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }

        return $sql;
    }

    // funcao do sql
    public function sql_query_file($p51_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from tipoproc ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($p51_codigo != null) {
                $sql2 .= " where tipoproc.p51_codigo = $p51_codigo ";
            }
        } else {
            if ($dbwhere != "") {
       $sql2 = " where $dbwhere";
     }
        }
     $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }

     return $sql;
  }

    public function sql_query_depto($p51_codigo = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*") {
            $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from tipoproc ";
        $sql .= "      inner join db_config     on db_config.codigo             = tipoproc.p51_instit";
        $sql .= "      inner join tipoprocgrupo on tipoprocgrupo.p40_sequencial = tipoproc.p51_tipoprocgrupo";
        $sql .= "      inner join cgm           on cgm.z01_numcgm               = db_config.numcgm";
        $sql .= "      left  join tipoprocdepto on tipoprocdepto.p41_tipoproc   = tipoproc.p51_codigo";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($p51_codigo != null) {
                $sql2 .= " where tipoproc.p51_codigo = $p51_codigo ";
            }
        } else {
            if ($dbwhere != "") {
                $sql2 = " where $dbwhere";
            }
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        }

        return $sql;
    }
}
