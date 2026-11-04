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

//MODULO: caixa
//CLASSE DA ENTIDADE fis_levantlotearq
class cl_fis_levantlotearqcont {
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
   public $y123_codigo          = 0;
   public $y123_levantlotearq   = 0;
   public $y123_inscr           = 0;
   public $y123_processo        = null;
   public $y123_acaofiscal      = 0;
   public $y123_codfiscal       = null;
   public $y123_levcontato      = null;
   public $y123_dtlevanta       = null;
   public $y123_perinicial      = null;
   public $y123_perfinal        = null;
   public $y123_anocomp         = 0;
   public $y123_mescomp         = 0;
   public $y123_bruto           = 0;
   public $y123_aliquota        = 0;
   public $y123_pago            = 0;
   public $y123_dtpago          = null;
   public $y123_descricao       = null;
   public $y123_tprespons       = 0;
   public $y123_nomecomple      = null;
   public $y123_cgccpf          = null;
   public $y123_ender           = null;
   public $y123_numero          = 0;
   public $y123_bairro          = null;
   public $y123_munic           = null;
   public $y123_uf              = null;
   public $y123_cep             = null;

   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y123_codigo          = int4                    = Código da Linha
                 y123_levantlotearq   = int4                    = Código do LevantamentoLote
                 y123_inscr           = int4                    = Inscrição
                 y123_processo        = character varying(20)   = Processo Administrativo
                 y123_acaofiscal      = int4                    = Ação Fiscal do Processo Fiscal
                 y123_codfiscal       = varchar(200)            = Código Fiscal
                 y123_levcontato      = character varying(100)  = Contato do Levantamento
                 y123_dtlevanta       = date                    = Data do Levantamento
                 y123_perinicial      = date                    = Período Inicial do Levantamento
                 y123_perfinal        = date                    = Período Final do Levantamento
                 y123_anocomp         = int4                    = Ano da Competência
                 y123_mescomp         = int4                    = Mês da Competência
                 y123_bruto           = double precision        = Valor Bruto
                 y123_aliquota        = double precision        = Alíquota
                 y123_pago            = double precision        = Valor Pago
                 y123_dtpago          = date                    = Data do Pagamento
                 y123_descricao       = character varying(100)  = Descrição do Pagamento
                 y123_tprespons       = int4                    = Tipo do Responsável (1 - Solidário | 2 - Subsidiário)
                 y123_nomecomple      = character varying(100)  = Nome do Responsável
                 y123_cgccpf          = character varying(14)   = CPF do Responsável
                 y123_ender           = character varying(100)  = Endereço do Responsável
                 y123_numero          = int4                    = Número do Endereço Responsável
                 y123_bairro          = character varying(40)   = Bairro do Responsável
                 y123_munic           = character varying(40)   = Município do Responsável
                 y123_uf              = character varying(2)    = UF do Responsável
                 y123_cep             = character varying(8)    = CEP do Responsável
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_levantlotearqcont");
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
       $this->y123_codigo = ($this->y123_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_codigo"]:$this->y123_codigo);
       $this->y123_levantlotearq = ($this->y123_levantlotearq == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_levantlotearq"]:$this->y123_levantlotearq);
       $this->y123_inscr = ($this->y123_inscr == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_inscr"]:$this->y123_inscr);
       $this->y123_processo = ($this->y123_processo == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_processo"]:$this->y123_processo);
       $this->y123_acaofiscal = ($this->y123_acaofiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_acaofiscal"]:$this->y123_acaofiscal);
       $this->y123_codfiscal = ($this->y123_codfiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_codfiscal"]:$this->y123_codfiscal);
       $this->y123_levcontato = ($this->y123_levcontato == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_levcontato"]:$this->y123_levcontato);
       $this->y123_dtlevanta = ($this->y123_dtlevanta == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_dtlevanta"]:$this->y123_dtlevanta);
       $this->y123_perinicial = ($this->y123_perinicial == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_perinicial"]:$this->y123_perinicial);
       $this->y123_perfinal = ($this->y123_perfinal == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_perfinal"]:$this->y123_perfinal);
       $this->y123_anocomp = ($this->y123_anocomp == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_anocomp"]:$this->y123_anocomp);
       $this->y123_mescomp = ($this->y123_mescomp == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_mescomp"]:$this->y123_mescomp);
       $this->y123_bruto = ($this->y123_bruto == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_bruto"]:$this->y123_bruto);
       $this->y123_aliquota = ($this->y123_aliquota == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_aliquota"]:$this->y123_aliquota);
       $this->y123_pago = ($this->y123_pago == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_pago"]:$this->y123_pago);
       $this->y123_dtpago = ($this->y123_dtpago == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_dtpago"]:$this->y123_dtpago);
       $this->y123_descricao = ($this->y123_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_descricao"]:$this->y123_descricao);
       $this->y123_tprespons = ($this->y123_tprespons == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_tprespons"]:$this->y123_tprespons);
       $this->y123_nomecomple = ($this->y123_nomecomple == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_nomecomple"]:$this->y123_nomecomple);
       $this->y123_cgccpf = ($this->y123_cgccpf == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_cgccpf"]:$this->y123_cgccpf);
       $this->y123_ender = ($this->y123_ender == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_ender"]:$this->y123_ender);
       $this->y123_numero = ($this->y123_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_numero"]:$this->y123_numero);
       $this->y123_bairro = ($this->y123_bairro == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_bairro"]:$this->y123_bairro);
       $this->y123_munic = ($this->y123_munic == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_munic"]:$this->y123_munic);
       $this->y123_uf = ($this->y123_uf == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_uf"]:$this->y123_uf);
       $this->y123_cep = ($this->y123_cep == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_cep"]:$this->y123_cep);
     }else{
       $this->y123_codigo = ($this->y123_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y123_codigo"]:$this->y123_codigo);
     }
   }
   // funcao para inclusao
   public function incluir ($y123_codigo){
     $this->atualizacampos();
     if($this->y123_levantlotearq == 0 ){
       $this->erro_sql = " Campo Levantamento Lote nao Informado.";
       $this->erro_campo = "y123_levantlotearq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_inscr == 0 ){
       $this->erro_sql = " Campo Inscricao nao Informado.";
       $this->erro_campo = "y123_inscr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_inscr == null ){
       $this->erro_sql = " Campo Processo Administrativo nao Informado.";
       $this->erro_campo = "y123_processo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_acaofiscal == 0 ){
       $this->erro_sql = " Campo Ação Fiscal nao Informado.";
       $this->erro_campo = "y123_acaofiscal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_codfiscal == null ){
       $this->erro_sql = " Campo Fiscal nao Informado.";
       $this->erro_campo = "y123_codfiscal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_dtlevanta == null ){
       $this->erro_sql = " Campo Data do Levantamento nao Informado.";
       $this->erro_campo = "y123_dtlevanta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_perinicial == null ){
       $this->erro_sql = " Campo Periodo Inicial nao Informado.";
       $this->erro_campo = "y123_perinicial";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_perfinal == null ){
       $this->erro_sql = " Campo Periodo Final nao Informado.";
       $this->erro_campo = "y123_perfinal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_anocomp == 0 ){
       $this->erro_sql = " Campo Ano da Competencia nao Informado.";
       $this->erro_campo = "y123_anocomp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_mescomp == 0 ){
       $this->erro_sql = " Campo Mes da Competencia nao Informado.";
       $this->erro_campo = "y123_mescomp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_bruto == 0 ){
       $this->erro_sql = " Campo Valor Bruto nao Informado.";
       $this->erro_campo = "y123_bruto";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_aliquota == 0 ){
       $this->erro_sql = " Campo Aliquota nao Informado.";
       $this->erro_campo = "y123_aliquota";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y123_dtpago == null ){
       $dtpago = "null";
     } else {
       $dtpago = "'".$this->y123_dtpago."'";
     }
     if($y123_codigo == "" || $y123_codigo == null ){
       $result = @pg_query("select nextval('fis_levantlotearqcont_y123_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: fis_levantlotearqcont_y123_codigo_seq do campo: y123_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->y123_codigo = pg_result($result,0,0);
     }else{
       $result = @pg_query("select last_value from fis_levantlotearqcont_y123_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $y123_codigo)){
         $this->erro_sql = " Campo $y123_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->y123_codigo = $y123_codigo;
       }
     }
     if(($this->y123_codigo == null) || ($this->y123_codigo == "") ){
       $this->erro_sql = " Campo y123_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "INSERT INTO fis_levantlotearqcont(
                                      y123_codigo,
                                      y123_levantlotearq,
                                      y123_inscr,
                                      y123_processo,
                                      y123_acaofiscal,
                                      y123_codfiscal,
                                      y123_levcontato,
                                      y123_dtlevanta,
                                      y123_perinicial,
                                      y123_perfinal,
                                      y123_anocomp,
                                      y123_mescomp,
                                      y123_bruto,
                                      y123_aliquota,
                                      y123_pago,
                                      y123_dtpago,
                                      y123_descricao,
                                      y123_tprespons,
                                      y123_nomecomple,
                                      y123_cgccpf,
                                      y123_ender,
                                      y123_numero,
                                      y123_bairro,
                                      y123_munic,
                                      y123_uf,
                                      y123_cep
                                      )
                               VALUES (
                                      $this->y123_codigo,
                                      $this->y123_levantlotearq,
                                      $this->y123_inscr,
                                      '$this->y123_processo',
                                      $this->y123_acaofiscal,
                                      '$this->y123_codfiscal',
                                      '$this->y123_levcontato',
                                      '$this->y123_dtlevanta',
                                      '$this->y123_perinicial',
                                      '$this->y123_perfinal',
                                      $this->y123_anocomp,
                                      $this->y123_mescomp,
                                      $this->y123_bruto,
                                      $this->y123_aliquota,
                                      $this->y123_pago,
                                      $dtpago,
                                      '$this->y123_descricao',
                                      $this->y123_tprespons,
                                      '$this->y123_nomecomple',
                                      '$this->y123_cgccpf',
                                      '$this->y123_ender',
                                      $this->y123_numero,
                                      '$this->y123_bairro',
                                      '$this->y123_munic',
                                      '$this->y123_uf',
                                      '$this->y123_cep'
                                      )";
                                      //echo $sql."<br />";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Arquivos de Levantamento Lote ($this->y123_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Arquivo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Arquivos de Levantamento Lote ($this->y123_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql   = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql  .= "Valores : ".$this->y123_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir = pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($y123_codigo=null) {
      $this->atualizacampos();

   }
   // funcao do recordset
   public function sql_record($sql) {
     $result = @pg_query($sql);
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
        $this->erro_sql   = "Record Vazio na Tabela:fis_levantlotearqcont";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $y123_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fis_levantlotearqcont ";
     $sql2 = "";
     if($dbwhere==""){
       if($y123_codigo!=null ){
         $sql2 .= " where fis_levantlotearqcont.y123_codigo = $y123_codigo ";
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
   // funcao do sql
   public function sql_query_file ( $y123_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fis_levantlotearqcont ";
     $sql2 = "";
     if($dbwhere==""){
       if($y123_codigo!=null ){
         $sql2 .= " where fis_levantlotearqcont.y123_codigo = $y123_codigo ";
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
}
?>
