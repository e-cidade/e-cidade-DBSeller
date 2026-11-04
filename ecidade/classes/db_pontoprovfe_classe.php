<?php

//MODULO: pessoal
//CLASSE DA ENTIDADE pontoprovfe
class cl_pontoprovfe { 
   // cria variaveis de erro 
   var $rotulo     = null; 
   var $query_sql  = null; 
   var $numrows    = 0; 
   var $numrows_incluir = 0; 
   var $numrows_alterar = 0; 
   var $numrows_excluir = 0; 
   var $erro_status= null; 
   var $erro_sql   = null; 
   var $erro_banco = null;  
   var $erro_msg   = null;  
   var $erro_campo = null;  
   var $pagina_retorno = null; 
   // cria variaveis do arquivo 
   var $r91_anousu = 0; 
   var $r91_mesusu = 0; 
   var $r91_regist = 0; 
   var $r91_rubric = null; 
   var $r91_valor = 0; 
   var $r91_quant = 0; 
   var $r91_lotac = null; 
   var $r91_media = 0; 
   var $r91_calc = 0; 
   var $r91_tpp = null; 
   var $r91_instit = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 r91_anousu = int4 = Ano do Exercicio 
                 r91_mesusu = int4 = Mes do Exercicio 
                 r91_regist = int4 = Matrícula 
                 r91_rubric = char(4) = Rubrica 
                 r91_valor = float8 = Valor 
                 r91_quant = float8 = Quantidade 
                 r91_lotac = char(4) = Lotação 
                 r91_media = int4 = Numero meses incidido na ficha 
                 r91_calc = int4 = Formula de Calculo 
                 r91_tpp = char(1) = Tipo 
                 r91_instit = int4 = codigo da instituicao 
                 ";
   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("pontoprovfe"); 
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
       $this->r91_anousu = ($this->r91_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_anousu"]:$this->r91_anousu);
       $this->r91_mesusu = ($this->r91_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_mesusu"]:$this->r91_mesusu);
       $this->r91_regist = ($this->r91_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_regist"]:$this->r91_regist);
       $this->r91_rubric = ($this->r91_rubric == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_rubric"]:$this->r91_rubric);
       $this->r91_valor = ($this->r91_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_valor"]:$this->r91_valor);
       $this->r91_quant = ($this->r91_quant == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_quant"]:$this->r91_quant);
       $this->r91_lotac = ($this->r91_lotac == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_lotac"]:$this->r91_lotac);
       $this->r91_media = ($this->r91_media == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_media"]:$this->r91_media);
       $this->r91_calc = ($this->r91_calc == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_calc"]:$this->r91_calc);
       $this->r91_tpp = ($this->r91_tpp == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_tpp"]:$this->r91_tpp);
       $this->r91_instit = ($this->r91_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_instit"]:$this->r91_instit);
     }else{
       $this->r91_anousu = ($this->r91_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_anousu"]:$this->r91_anousu);
       $this->r91_mesusu = ($this->r91_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_mesusu"]:$this->r91_mesusu);
       $this->r91_regist = ($this->r91_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_regist"]:$this->r91_regist);
       $this->r91_rubric = ($this->r91_rubric == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_rubric"]:$this->r91_rubric);
       $this->r91_tpp = ($this->r91_tpp == ""?@$GLOBALS["HTTP_POST_VARS"]["r91_tpp"]:$this->r91_tpp);
     }
   }
   // funcao para Inclusão
   public function incluir ($r91_anousu,$r91_mesusu,$r91_regist,$r91_rubric,$r91_tpp){ 
      $this->atualizacampos();
     if($this->r91_valor == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "r91_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r91_quant == null ){ 
       $this->erro_sql = " Campo Quantidade não informado.";
       $this->erro_campo = "r91_quant";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r91_lotac == null ){ 
       $this->erro_sql = " Campo Lotação não informado.";
       $this->erro_campo = "r91_lotac";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r91_media == null ){ 
       $this->erro_sql = " Campo Numero meses incidido na ficha não informado.";
       $this->erro_campo = "r91_media";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r91_calc == null ){ 
       $this->erro_sql = " Campo Formula de Calculo não informado.";
       $this->erro_campo = "r91_calc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r91_instit == null ){ 
       $this->erro_sql = " Campo codigo da instituicao não informado.";
       $this->erro_campo = "r91_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->r91_anousu = $r91_anousu; 
       $this->r91_mesusu = $r91_mesusu; 
       $this->r91_regist = $r91_regist; 
       $this->r91_rubric = $r91_rubric; 
       $this->r91_tpp = $r91_tpp; 
     if(($this->r91_anousu == null) || ($this->r91_anousu == "") ){ 
       $this->erro_sql = " Campo r91_anousu não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r91_mesusu == null) || ($this->r91_mesusu == "") ){ 
       $this->erro_sql = " Campo r91_mesusu não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r91_regist == null) || ($this->r91_regist == "") ){ 
       $this->erro_sql = " Campo r91_regist não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r91_rubric == null) || ($this->r91_rubric == "") ){ 
       $this->erro_sql = " Campo r91_rubric não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r91_tpp == null) || ($this->r91_tpp == "") ){ 
       $this->erro_sql = " Campo r91_tpp não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into pontoprovfe(
                                       r91_anousu 
                                      ,r91_mesusu 
                                      ,r91_regist 
                                      ,r91_rubric 
                                      ,r91_valor 
                                      ,r91_quant 
                                      ,r91_lotac 
                                      ,r91_media 
                                      ,r91_calc 
                                      ,r91_tpp 
                                      ,r91_instit 
                       )
                values (
                                $this->r91_anousu 
                               ,$this->r91_mesusu 
                               ,$this->r91_regist 
                               ,'$this->r91_rubric' 
                               ,$this->r91_valor 
                               ,$this->r91_quant 
                               ,'$this->r91_lotac' 
                               ,$this->r91_media 
                               ,$this->r91_calc 
                               ,'$this->r91_tpp' 
                               ,$this->r91_instit 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Ponto de Provisao de Ferias ($this->r91_anousu."-".$this->r91_mesusu."-".$this->r91_regist."-".$this->r91_rubric."-".$this->r91_tpp) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Ponto de Provisao de Ferias já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Ponto de Provisao de Ferias ($this->r91_anousu."-".$this->r91_mesusu."-".$this->r91_regist."-".$this->r91_rubric."-".$this->r91_tpp) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->r91_anousu."-".$this->r91_mesusu."-".$this->r91_regist."-".$this->r91_rubric."-".$this->r91_tpp;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->r91_anousu,$this->r91_mesusu,$this->r91_regist,$this->r91_rubric,$this->r91_tpp  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,13304,'$this->r91_anousu','I')");
         $resac = db_query("insert into db_acountkey values($acount,13305,'$this->r91_mesusu','I')");
         $resac = db_query("insert into db_acountkey values($acount,13306,'$this->r91_regist','I')");
         $resac = db_query("insert into db_acountkey values($acount,13307,'$this->r91_rubric','I')");
         $resac = db_query("insert into db_acountkey values($acount,13313,'$this->r91_tpp','I')");
         $resac = db_query("insert into db_acount values($acount,2335,13304,'','".AddSlashes(pg_result($resaco,0,'r91_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13305,'','".AddSlashes(pg_result($resaco,0,'r91_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13306,'','".AddSlashes(pg_result($resaco,0,'r91_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13307,'','".AddSlashes(pg_result($resaco,0,'r91_rubric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13308,'','".AddSlashes(pg_result($resaco,0,'r91_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13309,'','".AddSlashes(pg_result($resaco,0,'r91_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13310,'','".AddSlashes(pg_result($resaco,0,'r91_lotac'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13311,'','".AddSlashes(pg_result($resaco,0,'r91_media'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13312,'','".AddSlashes(pg_result($resaco,0,'r91_calc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13313,'','".AddSlashes(pg_result($resaco,0,'r91_tpp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2335,13314,'','".AddSlashes(pg_result($resaco,0,'r91_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($r91_anousu=null,$r91_mesusu=null,$r91_regist=null,$r91_rubric=null,$r91_tpp=null) { 
      $this->atualizacampos();
     $sql = " update pontoprovfe set ";
     $virgula = "";
     if(trim($this->r91_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_anousu"])){ 
       $sql  .= $virgula." r91_anousu = $this->r91_anousu ";
       $virgula = ",";
       if(trim($this->r91_anousu) == null ){ 
         $this->erro_sql = " Campo Ano do Exercicio não informado.";
         $this->erro_campo = "r91_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_mesusu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_mesusu"])){ 
       $sql  .= $virgula." r91_mesusu = $this->r91_mesusu ";
       $virgula = ",";
       if(trim($this->r91_mesusu) == null ){ 
         $this->erro_sql = " Campo Mes do Exercicio não informado.";
         $this->erro_campo = "r91_mesusu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_regist"])){ 
       $sql  .= $virgula." r91_regist = $this->r91_regist ";
       $virgula = ",";
       if(trim($this->r91_regist) == null ){ 
         $this->erro_sql = " Campo Matrícula não informado.";
         $this->erro_campo = "r91_regist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_rubric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_rubric"])){ 
       $sql  .= $virgula." r91_rubric = '$this->r91_rubric' ";
       $virgula = ",";
       if(trim($this->r91_rubric) == null ){ 
         $this->erro_sql = " Campo Rubrica não informado.";
         $this->erro_campo = "r91_rubric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_valor"])){ 
       $sql  .= $virgula." r91_valor = $this->r91_valor ";
       $virgula = ",";
       if(trim($this->r91_valor) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "r91_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_quant)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_quant"])){ 
       $sql  .= $virgula." r91_quant = $this->r91_quant ";
       $virgula = ",";
       if(trim($this->r91_quant) == null ){ 
         $this->erro_sql = " Campo Quantidade não informado.";
         $this->erro_campo = "r91_quant";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_lotac)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_lotac"])){ 
       $sql  .= $virgula." r91_lotac = '$this->r91_lotac' ";
       $virgula = ",";
       if(trim($this->r91_lotac) == null ){ 
         $this->erro_sql = " Campo Lotação não informado.";
         $this->erro_campo = "r91_lotac";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_media)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_media"])){ 
       $sql  .= $virgula." r91_media = $this->r91_media ";
       $virgula = ",";
       if(trim($this->r91_media) == null ){ 
         $this->erro_sql = " Campo Numero meses incidido na ficha não informado.";
         $this->erro_campo = "r91_media";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_calc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_calc"])){ 
       $sql  .= $virgula." r91_calc = $this->r91_calc ";
       $virgula = ",";
       if(trim($this->r91_calc) == null ){ 
         $this->erro_sql = " Campo Formula de Calculo não informado.";
         $this->erro_campo = "r91_calc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_tpp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_tpp"])){ 
       $sql  .= $virgula." r91_tpp = '$this->r91_tpp' ";
       $virgula = ",";
       if(trim($this->r91_tpp) == null ){ 
         $this->erro_sql = " Campo Tipo não informado.";
         $this->erro_campo = "r91_tpp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r91_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r91_instit"])){ 
       $sql  .= $virgula." r91_instit = $this->r91_instit ";
       $virgula = ",";
       if(trim($this->r91_instit) == null ){ 
         $this->erro_sql = " Campo codigo da instituicao não informado.";
         $this->erro_campo = "r91_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($r91_anousu!=null){
       $sql .= " r91_anousu = $this->r91_anousu";
     }
     if($r91_mesusu!=null){
       $sql .= " and  r91_mesusu = $this->r91_mesusu";
     }
     if($r91_regist!=null){
       $sql .= " and  r91_regist = $this->r91_regist";
     }
     if($r91_rubric!=null){
       $sql .= " and  r91_rubric = '$this->r91_rubric'";
     }
     if($r91_tpp!=null){
       $sql .= " and  r91_tpp = '$this->r91_tpp'";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->r91_anousu,$this->r91_mesusu,$this->r91_regist,$this->r91_rubric,$this->r91_tpp));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,13304,'$this->r91_anousu','A')");
           $resac = db_query("insert into db_acountkey values($acount,13305,'$this->r91_mesusu','A')");
           $resac = db_query("insert into db_acountkey values($acount,13306,'$this->r91_regist','A')");
           $resac = db_query("insert into db_acountkey values($acount,13307,'$this->r91_rubric','A')");
           $resac = db_query("insert into db_acountkey values($acount,13313,'$this->r91_tpp','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_anousu"]) || $this->r91_anousu != "")
             $resac = db_query("insert into db_acount values($acount,2335,13304,'".AddSlashes(pg_result($resaco,$conresaco,'r91_anousu'))."','$this->r91_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_mesusu"]) || $this->r91_mesusu != "")
             $resac = db_query("insert into db_acount values($acount,2335,13305,'".AddSlashes(pg_result($resaco,$conresaco,'r91_mesusu'))."','$this->r91_mesusu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_regist"]) || $this->r91_regist != "")
             $resac = db_query("insert into db_acount values($acount,2335,13306,'".AddSlashes(pg_result($resaco,$conresaco,'r91_regist'))."','$this->r91_regist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_rubric"]) || $this->r91_rubric != "")
             $resac = db_query("insert into db_acount values($acount,2335,13307,'".AddSlashes(pg_result($resaco,$conresaco,'r91_rubric'))."','$this->r91_rubric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_valor"]) || $this->r91_valor != "")
             $resac = db_query("insert into db_acount values($acount,2335,13308,'".AddSlashes(pg_result($resaco,$conresaco,'r91_valor'))."','$this->r91_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_quant"]) || $this->r91_quant != "")
             $resac = db_query("insert into db_acount values($acount,2335,13309,'".AddSlashes(pg_result($resaco,$conresaco,'r91_quant'))."','$this->r91_quant',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_lotac"]) || $this->r91_lotac != "")
             $resac = db_query("insert into db_acount values($acount,2335,13310,'".AddSlashes(pg_result($resaco,$conresaco,'r91_lotac'))."','$this->r91_lotac',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_media"]) || $this->r91_media != "")
             $resac = db_query("insert into db_acount values($acount,2335,13311,'".AddSlashes(pg_result($resaco,$conresaco,'r91_media'))."','$this->r91_media',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_calc"]) || $this->r91_calc != "")
             $resac = db_query("insert into db_acount values($acount,2335,13312,'".AddSlashes(pg_result($resaco,$conresaco,'r91_calc'))."','$this->r91_calc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_tpp"]) || $this->r91_tpp != "")
             $resac = db_query("insert into db_acount values($acount,2335,13313,'".AddSlashes(pg_result($resaco,$conresaco,'r91_tpp'))."','$this->r91_tpp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r91_instit"]) || $this->r91_instit != "")
             $resac = db_query("insert into db_acount values($acount,2335,13314,'".AddSlashes(pg_result($resaco,$conresaco,'r91_instit'))."','$this->r91_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ponto de Provisao de Ferias não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->r91_anousu."-".$this->r91_mesusu."-".$this->r91_regist."-".$this->r91_rubric."-".$this->r91_tpp;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ponto de Provisao de Ferias não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->r91_anousu."-".$this->r91_mesusu."-".$this->r91_regist."-".$this->r91_rubric."-".$this->r91_tpp;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->r91_anousu."-".$this->r91_mesusu."-".$this->r91_regist."-".$this->r91_rubric."-".$this->r91_tpp;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($r91_anousu=null,$r91_mesusu=null,$r91_regist=null,$r91_rubric=null,$r91_tpp=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($r91_anousu,$r91_mesusu,$r91_regist,$r91_rubric,$r91_tpp));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,null,null,null,null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,13304,'$r91_anousu','E')");
           $resac  = db_query("insert into db_acountkey values($acount,13305,'$r91_mesusu','E')");
           $resac  = db_query("insert into db_acountkey values($acount,13306,'$r91_regist','E')");
           $resac  = db_query("insert into db_acountkey values($acount,13307,'$r91_rubric','E')");
           $resac  = db_query("insert into db_acountkey values($acount,13313,'$r91_tpp','E')");
           $resac  = db_query("insert into db_acount values($acount,2335,13304,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13305,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13306,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13307,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_rubric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13308,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13309,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13310,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_lotac'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13311,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_media'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13312,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_calc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13313,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_tpp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2335,13314,'','".AddSlashes(pg_result($resaco,$iresaco,'r91_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from pontoprovfe
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($r91_anousu)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r91_anousu = $r91_anousu ";
        }
        if (!empty($r91_mesusu)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r91_mesusu = $r91_mesusu ";
        }
        if (!empty($r91_regist)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r91_regist = $r91_regist ";
        }
        if (!empty($r91_rubric)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r91_rubric = '$r91_rubric' ";
        }
        if (!empty($r91_tpp)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r91_tpp = '$r91_tpp' ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ponto de Provisao de Ferias não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$r91_anousu."-".$r91_mesusu."-".$r91_regist."-".$r91_rubric."-".$r91_tpp;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ponto de Provisao de Ferias não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$r91_anousu."-".$r91_mesusu."-".$r91_regist."-".$r91_rubric."-".$r91_tpp;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$r91_anousu."-".$r91_mesusu."-".$r91_regist."-".$r91_rubric."-".$r91_tpp;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao do recordset 
   public function sql_record($sql) { 
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
        $this->erro_sql   = "Record Vazio na Tabela:pontoprovfe";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($r91_anousu = null,$r91_mesusu = null,$r91_regist = null,$r91_rubric = null,$r91_tpp = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from pontoprovfe ";
     $sql .= "      inner join db_config  on  db_config.codigo = pontoprovfe.r91_instit";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($r91_anousu)) {
         $sql2 .= " where pontoprovfe.r91_anousu = $r91_anousu "; 
       } 
       if (!empty($r91_mesusu)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_mesusu = $r91_mesusu "; 
       } 
       if (!empty($r91_regist)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_regist = $r91_regist "; 
       } 
       if (!empty($r91_rubric)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_rubric = '$r91_rubric' "; 
       } 
       if (!empty($r91_tpp)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_tpp = '$r91_tpp' "; 
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
   public function sql_query_file ($r91_anousu = null,$r91_mesusu = null,$r91_regist = null,$r91_rubric = null,$r91_tpp = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from pontoprovfe ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($r91_anousu)){
         $sql2 .= " where pontoprovfe.r91_anousu = $r91_anousu "; 
       } 
       if (!empty($r91_mesusu)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_mesusu = $r91_mesusu "; 
       } 
       if (!empty($r91_regist)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_regist = $r91_regist "; 
       } 
       if (!empty($r91_rubric)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_rubric = '$r91_rubric' "; 
       } 
       if (!empty($r91_tpp)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovfe.r91_tpp = '$r91_tpp' "; 
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
