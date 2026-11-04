<?php
//MODULO: cadastro
//CLASSE DA ENTIDADE iptutaxanumpold
class cl_iptutaxanumpold { 
   // cria variaveis de erro 
   public $rotulo     = null; 
   public $query_sql  = null; 
   public $numrows    = 0; 
   public $erro_status= null; 
   public $erro_sql   = null; 
   public $erro_banco = null;  
   public $erro_msg   = null;  
   public $erro_campo = null;  
   public $pagina_retorno = null; 
   // cria variaveis do arquivo 
   public $j159_sequencial = 0; 
   public $j159_codigo = 0; 
   public $j159_matric = 0; 
   public $j159_numpre = 0; 
   public $j159_iptucadtaxaexe = 0; 
   public $j159_iptucalclog = 0; 
   // cria propriedade com as variaveis do arquivo 
   public $campos = "
                 j159_sequencial = int4 = Sequencial itputaxanumpold 
                 j159_codigo = int4 = Código 
                 j159_matric = int4 = Matrícula 
                 j159_numpre = int8 = Numpre 
                 j159_iptucadtaxaexe = int4 = Código do Cadastro de Taxa no Exercício 
                 j159_iptucalclog = int4 = Sequencial da iptucalclog 
                 ";
   //funcao construtor da classe 
   function cl_iptutaxanumpold() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("iptutaxanumpold"); 
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro 
   function erro($mostra,$retorna) { 
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->j159_sequencial = ($this->j159_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j159_sequencial"]:$this->j159_sequencial);
       $this->j159_codigo = ($this->j159_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["j159_codigo"]:$this->j159_codigo);
       $this->j159_matric = ($this->j159_matric == ""?@$GLOBALS["HTTP_POST_VARS"]["j159_matric"]:$this->j159_matric);
       $this->j159_numpre = ($this->j159_numpre == ""?@$GLOBALS["HTTP_POST_VARS"]["j159_numpre"]:$this->j159_numpre);
       $this->j159_iptucadtaxaexe = ($this->j159_iptucadtaxaexe == ""?@$GLOBALS["HTTP_POST_VARS"]["j159_iptucadtaxaexe"]:$this->j159_iptucadtaxaexe);
       $this->j159_iptucalclog = ($this->j159_iptucalclog == ""?@$GLOBALS["HTTP_POST_VARS"]["j159_iptucalclog"]:$this->j159_iptucalclog);
     }else{
       $this->j159_sequencial = ($this->j159_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j159_sequencial"]:$this->j159_sequencial);
     }
   }
   // funcao para inclusao
   function incluir ($j159_sequencial){ 
      $this->atualizacampos();
     if($this->j159_codigo == null ){ 
       $this->erro_sql = " Campo Código nao Informado.";
       $this->erro_campo = "j159_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j159_matric == null ){ 
       $this->erro_sql = " Campo Matrícula nao Informado.";
       $this->erro_campo = "j159_matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j159_numpre == null ){ 
       $this->erro_sql = " Campo Numpre nao Informado.";
       $this->erro_campo = "j159_numpre";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j159_iptucadtaxaexe == null ){ 
       $this->erro_sql = " Campo Código do Cadastro de Taxa no Exercício nao Informado.";
       $this->erro_campo = "j159_iptucadtaxaexe";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j159_iptucalclog == null ){ 
       $this->erro_sql = " Campo Sequencial da iptucalclog nao Informado.";
       $this->erro_campo = "j159_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($j159_sequencial == "" || $j159_sequencial == null ){
       $result = @db_query("select nextval('iptutaxanumpold_j159_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: iptutaxanumpold_j159_sequencial_seq do campo: j159_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->j159_sequencial = pg_result($result,0,0); 
     }else{
       $result = @db_query("select last_value from iptutaxanumpold_j159_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $j159_sequencial)){
         $this->erro_sql = " Campo j159_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j159_sequencial = $j159_sequencial; 
       }
     }
     if(($this->j159_sequencial == null) || ($this->j159_sequencial == "") ){ 
       $this->erro_sql = " Campo j159_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $result = @db_query("insert into iptutaxanumpold(
                                       j159_sequencial 
                                      ,j159_codigo 
                                      ,j159_matric 
                                      ,j159_numpre 
                                      ,j159_iptucadtaxaexe 
                                      ,j159_iptucalclog 
                       )
                values (
                                $this->j159_sequencial 
                               ,$this->j159_codigo 
                               ,$this->j159_matric 
                               ,$this->j159_numpre 
                               ,$this->j159_iptucadtaxaexe 
                               ,$this->j159_iptucalclog 
                      )");
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Histórico de taxas de IPTU ($this->j159_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Histórico de taxas de IPTU já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Histórico de taxas de IPTU ($this->j159_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j159_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $resaco = $this->sql_record($this->sql_query_file($this->j159_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014483,'$this->j159_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,1010517,1014483,'','".pg_result($resaco,0,'j159_sequencial')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010985,'','".pg_result($resaco,0,'j159_codigo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010987,'','".pg_result($resaco,0,'j159_matric')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010989,'','".pg_result($resaco,0,'j159_numpre')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010991,'','".pg_result($resaco,0,'j159_iptucadtaxaexe')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010993,'','".pg_result($resaco,0,'j159_iptucalclog')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($j159_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update iptutaxanumpold set ";
     $virgula = "";
     if(trim($this->j159_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j159_sequencial"])){ 
        if(trim($this->j159_sequencial)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j159_sequencial"])){ 
           $this->j159_sequencial = "0" ; 
        } 
       $sql  .= $virgula." j159_sequencial = $this->j159_sequencial ";
       $virgula = ",";
       if(trim($this->j159_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial itputaxanumpold nao Informado.";
         $this->erro_campo = "j159_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j159_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j159_codigo"])){ 
        if(trim($this->j159_codigo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j159_codigo"])){ 
           $this->j159_codigo = "0" ; 
        } 
       $sql  .= $virgula." j159_codigo = $this->j159_codigo ";
       $virgula = ",";
       if(trim($this->j159_codigo) == null ){ 
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "j159_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j159_matric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j159_matric"])){ 
        if(trim($this->j159_matric)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j159_matric"])){ 
           $this->j159_matric = "0" ; 
        } 
       $sql  .= $virgula." j159_matric = $this->j159_matric ";
       $virgula = ",";
       if(trim($this->j159_matric) == null ){ 
         $this->erro_sql = " Campo Matrícula nao Informado.";
         $this->erro_campo = "j159_matric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j159_numpre)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j159_numpre"])){ 
        if(trim($this->j159_numpre)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j159_numpre"])){ 
           $this->j159_numpre = "0" ; 
        } 
       $sql  .= $virgula." j159_numpre = $this->j159_numpre ";
       $virgula = ",";
       if(trim($this->j159_numpre) == null ){ 
         $this->erro_sql = " Campo Numpre nao Informado.";
         $this->erro_campo = "j159_numpre";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j159_iptucadtaxaexe)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j159_iptucadtaxaexe"])){ 
        if(trim($this->j159_iptucadtaxaexe)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j159_iptucadtaxaexe"])){ 
           $this->j159_iptucadtaxaexe = "0" ; 
        } 
       $sql  .= $virgula." j159_iptucadtaxaexe = $this->j159_iptucadtaxaexe ";
       $virgula = ",";
       if(trim($this->j159_iptucadtaxaexe) == null ){ 
         $this->erro_sql = " Campo Código do Cadastro de Taxa no Exercício nao Informado.";
         $this->erro_campo = "j159_iptucadtaxaexe";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j159_iptucalclog)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j159_iptucalclog"])){ 
        if(trim($this->j159_iptucalclog)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j159_iptucalclog"])){ 
           $this->j159_iptucalclog = "0" ; 
        } 
       $sql  .= $virgula." j159_iptucalclog = $this->j159_iptucalclog ";
       $virgula = ",";
       if(trim($this->j159_iptucalclog) == null ){ 
         $this->erro_sql = " Campo Sequencial da iptucalclog nao Informado.";
         $this->erro_campo = "j159_iptucalclog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where  j159_sequencial = $this->j159_sequencial
";
     $resaco = $this->sql_record($this->sql_query_file($this->j159_sequencial));
     if($this->numrows>0){       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014483,'$this->j159_sequencial','A')");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j159_sequencial"]))
         $resac = db_query("insert into db_acount values($acount,1010517,1014483,'".pg_result($resaco,0,'j159_sequencial')."','$this->j159_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j159_codigo"]))
         $resac = db_query("insert into db_acount values($acount,1010517,1010985,'".pg_result($resaco,0,'j159_codigo')."','$this->j159_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j159_matric"]))
         $resac = db_query("insert into db_acount values($acount,1010517,1010987,'".pg_result($resaco,0,'j159_matric')."','$this->j159_matric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j159_numpre"]))
         $resac = db_query("insert into db_acount values($acount,1010517,1010989,'".pg_result($resaco,0,'j159_numpre')."','$this->j159_numpre',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j159_iptucadtaxaexe"]))
         $resac = db_query("insert into db_acount values($acount,1010517,1010991,'".pg_result($resaco,0,'j159_iptucadtaxaexe')."','$this->j159_iptucadtaxaexe',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j159_iptucalclog"]))
         $resac = db_query("insert into db_acount values($acount,1010517,1010993,'".pg_result($resaco,0,'j159_iptucalclog')."','$this->j159_iptucalclog',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $result = @db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Histórico de taxas de IPTU nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j159_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Histórico de taxas de IPTU nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j159_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j159_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($j159_sequencial=null) { 
     $this->atualizacampos(true);
     $resaco = $this->sql_record($this->sql_query_file($this->j159_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014483,'$this->j159_sequencial','E')");
       $resac = db_query("insert into db_acount values($acount,1010517,1014483,'','".pg_result($resaco,0,'j159_sequencial')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010985,'','".pg_result($resaco,0,'j159_codigo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010987,'','".pg_result($resaco,0,'j159_matric')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010989,'','".pg_result($resaco,0,'j159_numpre')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010991,'','".pg_result($resaco,0,'j159_iptucadtaxaexe')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010517,1010993,'','".pg_result($resaco,0,'j159_iptucalclog')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $sql = " delete from iptutaxanumpold
                    where ";
     $sql2 = "";
      if($this->j159_sequencial != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " j159_sequencial = $this->j159_sequencial ";
}
     $result = @db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Histórico de taxas de IPTU nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->j159_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Histórico de taxas de IPTU nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->j159_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j159_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao do recordset 
   function sql_record($sql) { 
     $result = @db_query($sql);
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
        $this->erro_sql   = "Dados do Grupo nao Encontrado";
        $this->erro_msg   = "Usuário: \n\n ".$this->erro_sql." \n\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $j159_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from iptutaxanumpold ";
     $sql .= "      inner join iptubase  on  iptubase.j01_matric = iptutaxanumpold.j159_matric";
     $sql .= "      inner join iptucalclog  on  iptucalclog.j27_codigo = iptutaxanumpold.j159_iptucalclog";
     $sql .= "      inner join iptucadtaxaexe  on  iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanumpold.j159_iptucadtaxaexe";
     $sql .= "      inner join lote  on  lote.j34_idbql = iptubase.j01_idbql";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = iptubase.j01_numcgm";
     $sql .= "      inner join tipoproprietario  on  tipoproprietario.j163_tipoproprietario = iptubase.j01_tipoproprietario";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = iptucalclog.j27_usuario";
     $sql .= "      inner join cadvencdesc  on  cadvencdesc.q92_codigo = iptucadtaxaexe.j08_cadvencdesc";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = iptucadtaxaexe.j08_tabrec";
     $sql .= "      inner join arretipo  on  arretipo.k00_tipo = iptucadtaxaexe.j08_arretipo";
     $sql .= "      inner join db_sysfuncoes  on  db_sysfuncoes.codfuncao = iptucadtaxaexe.j08_db_sysfuncoes";
     $sql .= "      inner join procdiver  on  procdiver.dv09_procdiver = iptucadtaxaexe.j08_procdiver";
     $sql .= "      inner join iptucalh  on  iptucalh.j17_codhis = iptucadtaxaexe.j08_iptucalh";
     $sql .= "      inner join iptucadtaxa  as a on   a.j07_iptucadtaxa = iptucadtaxaexe.j08_iptucadtaxa";
     $sql2 = "";
     if($dbwhere==""){
       if($j159_sequencial!=null ){
         $sql2 .= " where iptutaxanumpold.j159_sequencial = $j159_sequencial "; 
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
   // funcao do sql 
   function sql_query_file ( $j159_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from iptutaxanumpold ";
     $sql2 = "";
     if($dbwhere==""){
       if($j159_sequencial!=null ){
         $sql2 .= " where iptutaxanumpold.j159_sequencial = $j159_sequencial "; 
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

  public function salvarIptutaxanumpOld($iAnousu, $iMatric, $iCalclog) {
 
    if ( !db_utils::inTransaction()) {
       throw new Exception ("Sem Transação Ativa");
    }

    if (empty($iAnousu)) {
       $this->erro_sql = " Campo Exercício não informado para gerar informações anteriores.";
       $this->erro_campo = "anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    if (empty($iMatric)) {
       $this->erro_sql = " Campo Matrícula não informado para gerar informações anteriores.";
       $this->erro_campo = "matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    if (empty($iCalclog)) {
       $this->erro_sql = " Campo CalcLog não informado para gerar informações anteriores.";
       $this->erro_campo = "j158_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    
    $sSqlIptutaxanump = "select j151_codigo,
                                j151_matric,
                                j151_numpre,
                                j151_iptucadtaxaexe
                           from iptutaxacalv 
                     inner join iptutaxanump   on j152_iptutaxanump   = j151_codigo 
                     inner join iptucadtaxaexe on j151_iptucadtaxaexe = j08_iptucadtaxaexe 
                          where j08_anousu = $iAnousu and j151_matric = $iMatric limit 1;";
    
    $rsIptutaxanump = db_query($sSqlIptutaxanump) or die($sSqlIptutaxanump);

    if (!$rsIptutaxanump) {
        throw new DBException("Não foi possivel buscar dados do cálculo da taxa (".pg_last_error().")");
    }
    if (pg_numrows($rsIptutaxanump) > 0) {

       global $j151_codigo;
       global $j151_matric;
       global $j151_numpre;
       global $j151_iptucadtaxaexe;
       global $j27_codigo;

       db_fieldsmemory($rsIptutaxanump,0);

       $this->j159_codigo          = $j151_codigo;
       $this->j159_matric          = $j151_matric;
       $this->j159_numpre          = $j151_numpre;
       $this->j159_iptucadtaxaexe  = $j151_iptucadtaxaexe;
       $this->j159_iptucalclog     = $j27_codigo;
       $this->incluir(null);

       if ($this->erro_status == 0) {
          throw new DBException("Não foi possivel salvar dados do cálculo das taxas (iptutaxanumpold) (".pg_last_error().")");
          return false;
       }
    }
    return true;
  }  

}
?>
