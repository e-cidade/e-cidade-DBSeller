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

//MODULO: pessoal
//CLASSE DA ENTIDADE rhpesrescisao
class cl_rhpesrescisao
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
    public $rh05_seqpes = 0; 
    public $rh05_recis_dia = null; 
    public $rh05_recis_mes = null; 
    public $rh05_recis_ano = null; 
    public $rh05_recis = null; 
    public $rh05_causa = 0; 
    public $rh05_caub = null; 
    public $rh05_aviso_dia = null; 
    public $rh05_aviso_mes = null; 
    public $rh05_aviso_ano = null; 
    public $rh05_aviso = null; 
    public $rh05_taviso = 0; 
    public $rh05_mremun = 0; 
    public $rh05_empenhado = 'f'; 
    public $rh05_trct = null; 
    public $rh05_codigoseguranca = null; 
    public $rh05_feriasavos = 0; 
    public $rh05_feriasvencidas = 0; 
    public $rh05_13salarioavos = 0; 
    public $rh05_codigorescisao = null; 
    public $rh05_tiporescisao = 0; 
    public $rh05_datapagamento_dia = null; 
    public $rh05_datapagamento_mes = null; 
    public $rh05_datapagamento_ano = null; 
    public $rh05_datapagamento = null; 
    public $rh05_observacao = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 rh05_seqpes = int4 = Sequência 
                 rh05_recis = date = Data da Rescisão 
                 rh05_causa = int4 = Causa da Rescisão 
                 rh05_caub = varchar(2) = Sub Causa de Rescisão 
                 rh05_aviso = date = Data de Aviso Prévio 
                 rh05_taviso = int4 = Tipo de Aviso 
                 rh05_mremun = float8 = Maior Remuneração 
                 rh05_empenhado = bool = Rescisão Empenhada 
                 rh05_trct = varchar(200) = TRCT 
                 rh05_codigoseguranca = varchar(200) = Código de segurança 
                 rh05_feriasavos = int4 = Avos de férias 
                 rh05_feriasvencidas = int4 = Férias vencidas 
                 rh05_13salarioavos = int4 = Avos de 13º salário 
                 rh05_codigorescisao = varchar(50) = Identificador da Rescisão 
                 rh05_tiporescisao = int4 = Tipo de Rescisão 
                 rh05_datapagamento = date = Data de Pagamento 
                 rh05_observacao = text = Observações 
                 ";

    //funcao construtor da classe
    function cl_rhpesrescisao()
    {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("rhpesrescisao"); 
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    //funcao erro
    function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"" . $this->erro_msg . "\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    // funcao para atualizar campos
    function atualizacampos($exclusao = false)
    {
     if($exclusao==false){
       $this->rh05_seqpes = ($this->rh05_seqpes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_seqpes"]:$this->rh05_seqpes);
       if($this->rh05_recis == ""){
         $this->rh05_recis_dia = ($this->rh05_recis_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_recis_dia"]:$this->rh05_recis_dia);
         $this->rh05_recis_mes = ($this->rh05_recis_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_recis_mes"]:$this->rh05_recis_mes);
         $this->rh05_recis_ano = ($this->rh05_recis_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_recis_ano"]:$this->rh05_recis_ano);
         if($this->rh05_recis_dia != ""){
            $this->rh05_recis = $this->rh05_recis_ano."-".$this->rh05_recis_mes."-".$this->rh05_recis_dia;
         }
       }
       $this->rh05_causa = ($this->rh05_causa == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_causa"]:$this->rh05_causa);
       $this->rh05_caub = ($this->rh05_caub == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_caub"]:$this->rh05_caub);
       if($this->rh05_aviso == ""){
         $this->rh05_aviso_dia = ($this->rh05_aviso_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_aviso_dia"]:$this->rh05_aviso_dia);
         $this->rh05_aviso_mes = ($this->rh05_aviso_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_aviso_mes"]:$this->rh05_aviso_mes);
         $this->rh05_aviso_ano = ($this->rh05_aviso_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_aviso_ano"]:$this->rh05_aviso_ano);
         if($this->rh05_aviso_dia != ""){
            $this->rh05_aviso = $this->rh05_aviso_ano."-".$this->rh05_aviso_mes."-".$this->rh05_aviso_dia;
         }
       }
       $this->rh05_taviso = ($this->rh05_taviso == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_taviso"]:$this->rh05_taviso);
       $this->rh05_mremun = ($this->rh05_mremun == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_mremun"]:$this->rh05_mremun);
       $this->rh05_empenhado = ($this->rh05_empenhado == "f"?@$GLOBALS["HTTP_POST_VARS"]["rh05_empenhado"]:$this->rh05_empenhado);
       $this->rh05_trct = ($this->rh05_trct == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_trct"]:$this->rh05_trct);
       $this->rh05_codigoseguranca = ($this->rh05_codigoseguranca == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_codigoseguranca"]:$this->rh05_codigoseguranca);
       $this->rh05_feriasavos = ($this->rh05_feriasavos == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_feriasavos"]:$this->rh05_feriasavos);
       $this->rh05_feriasvencidas = ($this->rh05_feriasvencidas == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_feriasvencidas"]:$this->rh05_feriasvencidas);
       $this->rh05_13salarioavos = ($this->rh05_13salarioavos == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_13salarioavos"]:$this->rh05_13salarioavos);
       $this->rh05_codigorescisao = ($this->rh05_codigorescisao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_codigorescisao"]:$this->rh05_codigorescisao);
       $this->rh05_tiporescisao = ($this->rh05_tiporescisao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_tiporescisao"]:$this->rh05_tiporescisao);

       // Caso a global rh05_tiporescisao esteja vazia, setar valor default "1".
       if($this->rh05_tiporescisao == "") {
         $this->rh05_tiporescisao = 1;
       }
       if($this->rh05_datapagamento == ""){
         $this->rh05_datapagamento_dia = ($this->rh05_datapagamento_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_datapagamento_dia"]:$this->rh05_datapagamento_dia);
         $this->rh05_datapagamento_mes = ($this->rh05_datapagamento_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_datapagamento_mes"]:$this->rh05_datapagamento_mes);
         $this->rh05_datapagamento_ano = ($this->rh05_datapagamento_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_datapagamento_ano"]:$this->rh05_datapagamento_ano);
         if($this->rh05_datapagamento_dia != ""){
            $this->rh05_datapagamento = $this->rh05_datapagamento_ano."-".$this->rh05_datapagamento_mes."-".$this->rh05_datapagamento_dia;
         }
       }
       $this->rh05_observacao = ($this->rh05_observacao == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_observacao"]:$this->rh05_observacao);
     }else{
       $this->rh05_seqpes = ($this->rh05_seqpes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh05_seqpes"]:$this->rh05_seqpes);
     }
   }

    // funcao para Inclusão
    function incluir($rh05_seqpes)
    {
      $this->atualizacampos();
     if($this->rh05_recis == null ){ 
       $this->erro_sql = " Campo Data da Rescisão não informado.";
       $this->erro_campo = "rh05_recis_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh05_causa == null ){ 
       $this->erro_sql = " Campo Causa da Rescisão não informado.";
       $this->erro_campo = "rh05_causa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh05_aviso == null ){ 
       $this->rh05_aviso = "null";
     }
     if($this->rh05_taviso == null ){ 
       $this->erro_sql = " Campo Tipo de Aviso não informado.";
       $this->erro_campo = "rh05_taviso";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh05_mremun == null ){ 
       $this->erro_sql = " Campo Maior Remuneração não informado.";
       $this->erro_campo = "rh05_mremun";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh05_empenhado == null ){ 
       $this->rh05_empenhado = "false";
     }
     if($this->rh05_feriasavos == null ){ 
       $this->rh05_feriasavos = "0";
     }
     if($this->rh05_feriasvencidas == null ){ 
       $this->rh05_feriasvencidas = "0";
     }
     if($this->rh05_13salarioavos == null ){ 
       $this->rh05_13salarioavos = "0";
     }
     if($this->rh05_tiporescisao == null ){ 
       $this->erro_sql = " Campo Tipo de Rescisão não informado.";
       $this->erro_campo = "rh05_tiporescisao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->rh05_datapagamento == null ){ 
       $this->rh05_datapagamento = "null";
     }
       $this->rh05_seqpes = $rh05_seqpes; 
     if(($this->rh05_seqpes == null) || ($this->rh05_seqpes == "") ){ 
       $this->erro_sql = " Campo rh05_seqpes não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into rhpesrescisao(
                                       rh05_seqpes 
                                      ,rh05_recis 
                                      ,rh05_causa 
                                      ,rh05_caub 
                                      ,rh05_aviso 
                                      ,rh05_taviso 
                                      ,rh05_mremun 
                                      ,rh05_empenhado 
                                      ,rh05_trct 
                                      ,rh05_codigoseguranca 
                                      ,rh05_feriasavos 
                                      ,rh05_feriasvencidas 
                                      ,rh05_13salarioavos 
                                      ,rh05_codigorescisao 
                                      ,rh05_tiporescisao 
                                      ,rh05_datapagamento 
                                      ,rh05_observacao 
                       )
                values (
                                $this->rh05_seqpes 
                               ,".($this->rh05_recis == "null" || $this->rh05_recis == ""?"null":"'".$this->rh05_recis."'")." 
                               ,$this->rh05_causa 
                               ,'$this->rh05_caub' 
                               ,".($this->rh05_aviso == "null" || $this->rh05_aviso == ""?"null":"'".$this->rh05_aviso."'")." 
                               ,$this->rh05_taviso 
                               ,$this->rh05_mremun 
                               ,'$this->rh05_empenhado' 
                               ,'$this->rh05_trct' 
                               ,'$this->rh05_codigoseguranca' 
                               ,$this->rh05_feriasavos 
                               ,$this->rh05_feriasvencidas 
                               ,$this->rh05_13salarioavos 
                               ,'$this->rh05_codigorescisao' 
                               ,$this->rh05_tiporescisao 
                               ,".($this->rh05_datapagamento == "null" || $this->rh05_datapagamento == ""?"null":"'".$this->rh05_datapagamento."'")." 
                               ,'$this->rh05_observacao' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Funcionários em rescisão ($this->rh05_seqpes) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Funcionários em rescisão já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Funcionários em rescisão ($this->rh05_seqpes) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh05_seqpes;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh05_seqpes  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,7043,'$this->rh05_seqpes','I')");
         $resac = db_query("insert into db_acount values($acount,1161,7043,'','".AddSlashes(pg_result($resaco,0,'rh05_seqpes'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,7044,'','".AddSlashes(pg_result($resaco,0,'rh05_recis'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,7045,'','".AddSlashes(pg_result($resaco,0,'rh05_causa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,7046,'','".AddSlashes(pg_result($resaco,0,'rh05_caub'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,7047,'','".AddSlashes(pg_result($resaco,0,'rh05_aviso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,7048,'','".AddSlashes(pg_result($resaco,0,'rh05_taviso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,7049,'','".AddSlashes(pg_result($resaco,0,'rh05_mremun'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,17509,'','".AddSlashes(pg_result($resaco,0,'rh05_empenhado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,19589,'','".AddSlashes(pg_result($resaco,0,'rh05_trct'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,19590,'','".AddSlashes(pg_result($resaco,0,'rh05_codigoseguranca'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,19633,'','".AddSlashes(pg_result($resaco,0,'rh05_feriasavos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,19635,'','".AddSlashes(pg_result($resaco,0,'rh05_feriasvencidas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,19634,'','".AddSlashes(pg_result($resaco,0,'rh05_13salarioavos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,1009899,'','".AddSlashes(pg_result($resaco,0,'rh05_codigorescisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,1009906,'','".AddSlashes(pg_result($resaco,0,'rh05_tiporescisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,1010252,'','".AddSlashes(pg_result($resaco,0,'rh05_datapagamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1161,1011160,'','".AddSlashes(pg_result($resaco,0,'rh05_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    // funcao para alteracao
    public function alterar($rh05_seqpes=null)
    {
      $this->atualizacampos();
     $sql = " update rhpesrescisao set ";
     $virgula = "";
     if(trim($this->rh05_seqpes)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_seqpes"])){ 
       $sql  .= $virgula." rh05_seqpes = $this->rh05_seqpes ";
       $virgula = ",";
       if(trim($this->rh05_seqpes) == null ){ 
         $this->erro_sql = " Campo Sequência não informado.";
         $this->erro_campo = "rh05_seqpes";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh05_recis)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_recis_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh05_recis_dia"] !="") ){ 
       $sql  .= $virgula." rh05_recis = '$this->rh05_recis' ";
       $virgula = ",";
       if(trim($this->rh05_recis) == null ){ 
         $this->erro_sql = " Campo Data da Rescisão não informado.";
         $this->erro_campo = "rh05_recis_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh05_recis_dia"])){ 
         $sql  .= $virgula." rh05_recis = null ";
         $virgula = ",";
         if(trim($this->rh05_recis) == null ){ 
           $this->erro_sql = " Campo Data da Rescisão não informado.";
           $this->erro_campo = "rh05_recis_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->rh05_causa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_causa"])){ 
       $sql  .= $virgula." rh05_causa = $this->rh05_causa ";
       $virgula = ",";
       if(trim($this->rh05_causa) == null ){ 
         $this->erro_sql = " Campo Causa da Rescisão não informado.";
         $this->erro_campo = "rh05_causa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh05_caub)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_caub"])){ 
       $sql  .= $virgula." rh05_caub = '$this->rh05_caub' ";
       $virgula = ",";
     }
     if(trim($this->rh05_aviso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_aviso_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh05_aviso_dia"] !="") ){ 
       $sql  .= $virgula." rh05_aviso = '$this->rh05_aviso' ";
       $virgula = ",";
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh05_aviso_dia"])){ 
         $sql  .= $virgula." rh05_aviso = null ";
         $virgula = ",";
       }
     }
     if(trim($this->rh05_taviso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_taviso"])){ 
       $sql  .= $virgula." rh05_taviso = $this->rh05_taviso ";
       $virgula = ",";
       if(trim($this->rh05_taviso) == null ){ 
         $this->erro_sql = " Campo Tipo de Aviso não informado.";
         $this->erro_campo = "rh05_taviso";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh05_mremun)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_mremun"])){ 
       $sql  .= $virgula." rh05_mremun = $this->rh05_mremun ";
       $virgula = ",";
       if(trim($this->rh05_mremun) == null ){ 
         $this->erro_sql = " Campo Maior Remuneração não informado.";
         $this->erro_campo = "rh05_mremun";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh05_empenhado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_empenhado"])){ 
       $sql  .= $virgula." rh05_empenhado = '$this->rh05_empenhado' ";
       $virgula = ",";
     }
     if(trim($this->rh05_trct)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_trct"])){ 
       $sql  .= $virgula." rh05_trct = '$this->rh05_trct' ";
       $virgula = ",";
     }
     if(trim($this->rh05_codigoseguranca)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_codigoseguranca"])){ 
       $sql  .= $virgula." rh05_codigoseguranca = '$this->rh05_codigoseguranca' ";
       $virgula = ",";
     }
     if(trim($this->rh05_feriasavos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_feriasavos"])){ 
        if(trim($this->rh05_feriasavos)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh05_feriasavos"])){ 
           $this->rh05_feriasavos = "0" ; 
        } 
       $sql  .= $virgula." rh05_feriasavos = $this->rh05_feriasavos ";
       $virgula = ",";
     }
     if(trim($this->rh05_feriasvencidas)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_feriasvencidas"])){ 
        if(trim($this->rh05_feriasvencidas)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh05_feriasvencidas"])){ 
           $this->rh05_feriasvencidas = "0" ; 
        } 
       $sql  .= $virgula." rh05_feriasvencidas = $this->rh05_feriasvencidas ";
       $virgula = ",";
     }
     if(trim($this->rh05_13salarioavos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_13salarioavos"])){ 
        if(trim($this->rh05_13salarioavos)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh05_13salarioavos"])){ 
           $this->rh05_13salarioavos = "0" ; 
        } 
       $sql  .= $virgula." rh05_13salarioavos = $this->rh05_13salarioavos ";
       $virgula = ",";
     }
     if(trim($this->rh05_codigorescisao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_codigorescisao"])){ 
       $sql  .= $virgula." rh05_codigorescisao = '$this->rh05_codigorescisao' ";
       $virgula = ",";
     }
     if(trim($this->rh05_tiporescisao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_tiporescisao"])){ 
       $sql  .= $virgula." rh05_tiporescisao = $this->rh05_tiporescisao ";
       $virgula = ",";
       if(trim($this->rh05_tiporescisao) == null ){ 
         $this->erro_sql = " Campo Tipo de Rescisão não informado.";
         $this->erro_campo = "rh05_tiporescisao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh05_datapagamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_datapagamento_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh05_datapagamento_dia"] !="") ){ 
       $sql  .= $virgula." rh05_datapagamento = '$this->rh05_datapagamento' ";
       $virgula = ",";
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh05_datapagamento_dia"])){ 
         $sql  .= $virgula." rh05_datapagamento = null ";
         $virgula = ",";
       }
     }
     if(trim($this->rh05_observacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh05_observacao"])){ 
       $sql  .= $virgula." rh05_observacao = '$this->rh05_observacao' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($rh05_seqpes!=null){
       $sql .= " rh05_seqpes = $this->rh05_seqpes";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh05_seqpes));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,7043,'$this->rh05_seqpes','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_seqpes"]) || $this->rh05_seqpes != "")
             $resac = db_query("insert into db_acount values($acount,1161,7043,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_seqpes'))."','$this->rh05_seqpes',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_recis"]) || $this->rh05_recis != "")
             $resac = db_query("insert into db_acount values($acount,1161,7044,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_recis'))."','$this->rh05_recis',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_causa"]) || $this->rh05_causa != "")
             $resac = db_query("insert into db_acount values($acount,1161,7045,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_causa'))."','$this->rh05_causa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_caub"]) || $this->rh05_caub != "")
             $resac = db_query("insert into db_acount values($acount,1161,7046,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_caub'))."','$this->rh05_caub',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_aviso"]) || $this->rh05_aviso != "")
             $resac = db_query("insert into db_acount values($acount,1161,7047,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_aviso'))."','$this->rh05_aviso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_taviso"]) || $this->rh05_taviso != "")
             $resac = db_query("insert into db_acount values($acount,1161,7048,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_taviso'))."','$this->rh05_taviso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_mremun"]) || $this->rh05_mremun != "")
             $resac = db_query("insert into db_acount values($acount,1161,7049,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_mremun'))."','$this->rh05_mremun',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_empenhado"]) || $this->rh05_empenhado != "")
             $resac = db_query("insert into db_acount values($acount,1161,17509,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_empenhado'))."','$this->rh05_empenhado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_trct"]) || $this->rh05_trct != "")
             $resac = db_query("insert into db_acount values($acount,1161,19589,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_trct'))."','$this->rh05_trct',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_codigoseguranca"]) || $this->rh05_codigoseguranca != "")
             $resac = db_query("insert into db_acount values($acount,1161,19590,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_codigoseguranca'))."','$this->rh05_codigoseguranca',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_feriasavos"]) || $this->rh05_feriasavos != "")
             $resac = db_query("insert into db_acount values($acount,1161,19633,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_feriasavos'))."','$this->rh05_feriasavos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_feriasvencidas"]) || $this->rh05_feriasvencidas != "")
             $resac = db_query("insert into db_acount values($acount,1161,19635,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_feriasvencidas'))."','$this->rh05_feriasvencidas',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_13salarioavos"]) || $this->rh05_13salarioavos != "")
             $resac = db_query("insert into db_acount values($acount,1161,19634,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_13salarioavos'))."','$this->rh05_13salarioavos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_codigorescisao"]) || $this->rh05_codigorescisao != "")
             $resac = db_query("insert into db_acount values($acount,1161,1009899,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_codigorescisao'))."','$this->rh05_codigorescisao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_tiporescisao"]) || $this->rh05_tiporescisao != "")
             $resac = db_query("insert into db_acount values($acount,1161,1009906,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_tiporescisao'))."','$this->rh05_tiporescisao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_datapagamento"]) || $this->rh05_datapagamento != "")
             $resac = db_query("insert into db_acount values($acount,1161,1010252,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_datapagamento'))."','$this->rh05_datapagamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["rh05_observacao"]) || $this->rh05_observacao != "")
             $resac = db_query("insert into db_acount values($acount,1161,1011160,'".AddSlashes(pg_result($resaco,$conresaco,'rh05_observacao'))."','$this->rh05_observacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Funcionários em rescisão não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh05_seqpes;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Funcionários em rescisão não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->rh05_seqpes;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->rh05_seqpes;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    // funcao para exclusao
    public function excluir($rh05_seqpes=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($rh05_seqpes));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,7043,'$rh05_seqpes','E')");
           $resac  = db_query("insert into db_acount values($acount,1161,7043,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_seqpes'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,7044,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_recis'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,7045,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_causa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,7046,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_caub'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,7047,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_aviso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,7048,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_taviso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,7049,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_mremun'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,17509,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_empenhado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,19589,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_trct'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,19590,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_codigoseguranca'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,19633,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_feriasavos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,19635,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_feriasvencidas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,19634,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_13salarioavos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,1009899,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_codigorescisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,1009906,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_tiporescisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,1010252,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_datapagamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1161,1011160,'','".AddSlashes(pg_result($resaco,$iresaco,'rh05_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from rhpesrescisao
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($rh05_seqpes)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " rh05_seqpes = $rh05_seqpes ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Funcionários em rescisão não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh05_seqpes;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Funcionários em rescisão não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh05_seqpes;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$rh05_seqpes;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    // funcao do recordset
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
        $this->erro_sql   = "Record Vazio na Tabela:rhpesrescisao";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    // funcao do sql
    public function sql_query($rh05_seqpes = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

     $sql  = "select {$campos}";
     $sql .= "  from rhpesrescisao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh05_seqpes)) {
         $sql2 .= " where rhpesrescisao.rh05_seqpes = $rh05_seqpes "; 
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

    // funcao do sql
    public function sql_query_file($rh05_seqpes = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

     $sql  = "select {$campos} ";
     $sql .= "  from rhpesrescisao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($rh05_seqpes)){
         $sql2 .= " where rhpesrescisao.rh05_seqpes = $rh05_seqpes "; 
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

    function atualiza_incluir()
    {
  	 $this->incluir($this->rh05_seqpes);
   }

    function sql_query_retorno($rh05_seqpes = null, $campos = "*", $ordem = null, $dbwhere = "", $anonovo, $mesnovo)
    {
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
     $sql .= " from rhpesrescisao ";
     $sql .= "      inner join rhpessoalmov on rh05_seqpes=rh02_seqpes ";
     $sql .= "      left  join rhpessoal on rh01_regist=rh02_regist ";
     $sql .= "      left  join rhpessoalmov a on a.rh02_regist=rh01_regist
		                                         and a.rh02_anousu=".$anonovo."
                                             and a.rh02_mesusu=".$mesnovo."
																						 and a.rh02_instit=".db_getsession("DB_instit")." ";
     $sql2 = "";
     if($dbwhere==""){
       if($rh05_seqpes!=null ){
         $sql2 .= " where rhpesrescisao.rh05_seqpes = $rh05_seqpes "; 
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

    function sql_query_rescisao($rh05_seqpes = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
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
     $sql .= " from rhpesrescisao ";
     $sql .= "      inner join rhpessoalmov  on  rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes";
     $sql .= "      inner join tpcontra  on  tpcontra.h13_codigo = rhpessoalmov.rh02_tpcont";
     $sql .= "      inner join rhregime  on  rhregime.rh30_codreg = rhpessoalmov.rh02_codreg
		                                    and  rhregime.rh30_instit = rhpessoalmov.rh02_instit ";
     $sql .= "      inner join rescisao  on  rescisao.r59_anousu  = rhpessoalmov.rh02_anousu 
                                        and  rescisao.r59_mesusu  = rhpessoalmov.rh02_mesusu 
                              					and  rescisao.r59_regime  = rhregime.rh30_regime 
                              					and  rescisao.r59_causa   = rhpesrescisao.rh05_causa
                              					and  rescisao.r59_caub    = rhpesrescisao.rh05_caub::char(2) 
																				and  rescisao.r59_instit  = rhpessoalmov.rh02_instit ";
                                        
     $sql2 = "";
     if($dbwhere==""){
       if($rh05_seqpes!=null ){
         $sql2 .= " where rhpesrescisao.rh05_seqpes = $rh05_seqpes "; 
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

    function sql_query_rescisao_esocial($rh05_seqpes = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
        $sql = "select ";
        if ($campos != "*" ) {
            $campos_sql = split("#",$campos);
            $virgula = "";
            for ($i=0;$i<sizeof($campos_sql);$i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
            $sql .= " from rhpesrescisao ";
            $sql .= "      inner join rhpessoalmov  on  rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes";
            $sql .= "      inner join tpcontra  on  tpcontra.h13_codigo = rhpessoalmov.rh02_tpcont";
            $sql .= "      inner join rhregime  on  rhregime.rh30_codreg = rhpessoalmov.rh02_codreg
                                            and  rhregime.rh30_instit = rhpessoalmov.rh02_instit ";
            $sql .= "      inner join rescisao  on  rescisao.r59_anousu  = rhpessoalmov.rh02_anousu 
                                            and  rescisao.r59_mesusu  = rhpessoalmov.rh02_mesusu 
                                            and  rescisao.r59_regime  = rhregime.rh30_regime 
                                            and  rescisao.r59_causa   = rhpesrescisao.rh05_causa
                                            and  rescisao.r59_instit  = rhpessoalmov.rh02_instit ";
                                        
            $sql2 = "";
        if ($dbwhere=="") {
            if ($rh05_seqpes!=null ) {
                $sql2 .= " where rhpesrescisao.rh05_seqpes = $rh05_seqpes "; 
            } 
        } else if($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null ) {
            $sql .= " order by ";
            $campos_sql = split("#",$ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }

    function sql_query_ngeraferias($rh05_seqpes = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
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
     $sql .= " from rhpesrescisao ";
     $sql .= "      inner join rhpessoalmov  on  rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes";
     $sql2 = "";
     if($dbwhere==""){
       if($rh05_seqpes!=null ){
         $sql2 .= " where rhpesrescisao.rh05_seqpes = $rh05_seqpes "; 
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
     * @param string $campos
     * @param null $where
     * @param null $ordem
     * @return string
     */
    public function buscaServidorCargaDesligamento($campos = "*", $where = null, $ordem = null)
    {
        $anoFolha = DBPessoal::getAnoFolha();
        $mesFolha = DBPessoal::getMesFolha();
        $instituicao = InstituicaoRepository::getInstituicaoSessao()->getCodigo();

        $sql = "select {$campos}";
        $sql .= "  from avaliacaogruporespostarhpesrescisao";
        $sql .= "  join rhpesrescisao on rh05_codigorescisao = eso15_codigorescisao";
        $sql .= "  join rhpessoalmov on rh05_seqpes = rh02_seqpes";
        $sql .= "                   and rh02_anousu = {$anoFolha}";
        $sql .= "                   and rh02_mesusu = {$mesFolha}";
        $sql .= "                   and rh02_instit = {$instituicao}";
        $sql .= "  join rhpessoal on rh02_regist = rh01_regist";
        $sql .= "  join cgm on rh01_numcgm = z01_numcgm";

        if (!empty($where)) {
            $sql .= " where " . $where;
        }

        if (!empty($ordem)) {
            $sql .= " order by " . $ordem;
        }

        return $sql;
    }


    /**
     * Metodo responsavel por montar sql que busca rescisao dos trabalhadores com ou sem vinculo
     *
     * @param $datainicial
     * @param $datafinal
     * @param $instituicao
     * @param DBCompetencia $competencia
     * @param bool $vinculoEmprego
     * @return string
     */
    public function sql_query_esocial($datainicial, $datafinal, $instituicao, DBCompetencia $competencia, $vinculoEmprego = true)
    {
        $sql = "select rh02_anousu as ano,";
        $sql .= "       rh02_mesusu as mes,";
        $sql .= "       rh02_regist as matricula, ";
        $sql .= "       z01_nome as nome, ";
        $sql .= "       rh05_recis as data_desligamento,";
        $sql .= "       rh02_instit as instituicao, ";
        $sql .= "       rh05_codigorescisao as codigo_rescisao, ";
        $sql .= "       current_date -  rh05_recis as dias_atraso,";
        $sql .= "       (select count(*) from gerfres where r20_regist = rh02_regist and r20_instit = rh02_instit and r20_pd <> 3) as total_rubricas";
        $sql .= "  from pessoal.rhpesrescisao ";
        $sql .= "       inner join pessoal.rhpessoalmov  on rh05_seqpes = rh02_seqpes ";
        $sql .= "       inner join pessoal.rhregime      on rh30_codreg     = rh02_codreg ";
        $sql .= "       inner join pessoal.rhpessoal     on rh01_regist = rh02_regist ";
        $sql .= "       inner join protocolo.cgm         on z01_numcgm  = rh01_numcgm ";
        $sql .= "       left join  avaliacaogruporespostatertrabasemvinc on eso24_codigorescisao = rh05_codigorescisao  ";
        $sql .= " where rh05_recis between '{$datainicial}' and '{$datafinal}'";
        $sql .= "   and rh02_instit = {$instituicao} ";
        $sql .= "   and rh02_anousu = {$competencia->getAno()} ";
        $sql .= "   and rh02_mesusu = {$competencia->getMes()} ";
        $sql .= "   and rh30_vinculoemprego is " . ($vinculoEmprego ? 'true' : 'false');
        $sql .= "   and  eso24_codigorescisao is null ";
        $sql .= "   order by 8 desc, rh02_regist";

        return $sql;
    }

    public function sql_query_esocial_desligamento($datainicial, $datafinal, $instituicao, DBCompetencia $competencia)
    {
        $sql  = "select rh02_anousu as ano,";
        $sql .= "       rh02_mesusu as mes,";
        $sql .= "       rh02_regist as matricula, ";
        $sql .= "       z01_nome as nome, ";
        $sql .= "       rh05_recis as data_desligamento,";
        $sql .= "       rh02_instit as instituicao, ";
        $sql .= "       rh05_codigorescisao as codigo_rescisao, ";
        $sql .= "       current_date -  rh05_recis as dias_atraso,";
        $sql .= "       (select count(*) from gerfres where r20_regist = rh02_regist and r20_instit = rh02_instit and r20_pd <> 3) as total_rubricas";
        $sql .= "  from pessoal.rhpesrescisao ";
        $sql .= "       inner join pessoal.rhpessoalmov  on rh05_seqpes = rh02_seqpes ";
        $sql .= "       inner join pessoal.rhregime      on rh30_codreg     = rh02_codreg ";
        $sql .= "       inner join pessoal.rhpessoal     on rh01_regist = rh02_regist ";
        $sql .= "       inner join protocolo.cgm         on z01_numcgm  = rh01_numcgm ";
        $sql .= "       left join  avaliacaogruporespostarhpesrescisao on eso15_codigorescisao = rh05_codigorescisao  ";
        $sql .= " where rh05_recis between '{$datainicial}' and '{$datafinal}'";
        $sql .= "   and rh02_instit = {$instituicao} ";
        $sql .= "   and rh02_anousu = {$competencia->getAno()} ";
        $sql .= "   and rh02_mesusu = {$competencia->getMes()} ";
        $sql .= "   and rh30_vinculoemprego is true";
        $sql .= "   and  eso15_codigorescisao is null ";
        $sql .= "   order by 8 desc, rh02_regist";

        return $sql;
    }

    function sql_query_rescisao_data($rh02_regist = null, $campos = "*", $ordem = null, $dbwhere = "")
    {
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
     $sql .= " from rhpesrescisao ";
     $sql .= "      inner join rhpessoalmov on rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes ";
                                        
     $sql2 = "";
     if($dbwhere==""){
       if($rh02_regist!=null ){
         $sql2 .= " where rhpesrescisao.rh05_seqpes = $rh02_regist "; 
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
}
