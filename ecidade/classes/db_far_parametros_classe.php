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

class cl_far_parametros
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
    public $fa02_i_codigo = 0; 
    public $fa02_i_dbestrutura = 0; 
    public $fa02_c_descr = null; 
    public $fa02_c_digitacao = null; 
    public $fa02_b_comprovante = 'f'; 
    public $fa02_b_numestoque = 'f'; 
    public $fa02_b_novaretirada = 'f'; 
    public $fa02_i_tipoperiodocontinuado = 0; 
    public $fa02_i_acumularsaldocontinuado = 0; 
    public $fa02_i_origemreceita = 0; 
    public $fa02_i_avisoretirada = 0; 
    public $fa02_i_cursor = 0; 
    public $fa02_i_validalote = 0; 
    public $fa02_i_validavencimento = 0; 
    public $fa02_i_acaoprog = 0; 
    public $fa02_i_verificapacientehiperdia = 0; 
    public $fa02_i_numdiasmedcontinativo = 0; 
    public $fa02_utilizaimpressoratermica = 'f'; 
    public $fa02_informa_receita_medicamento = 'f'; 
    public $fa02_alerta_nao_morador = 'f'; 
    public $fa02_ibge_municipio = 0; 
    public $fa02_utiliza_bnafar = 'f'; 
    public $fa02_altera_validade_medcontinuado = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 fa02_i_codigo = int4 = Código 
                 fa02_i_dbestrutura = int4 = Estrutura 
                 fa02_c_descr = char(60) = Descrição 
                 fa02_c_digitacao = char(2) = Digitação 
                 fa02_b_comprovante = bool = Comprovante Automatico 
                 fa02_b_numestoque = bool = Numeração do estoque 
                 fa02_b_novaretirada = bool = Nova retirada automática 
                 fa02_i_tipoperiodocontinuado = int4 = Tipo de periodo dos medicamentos continuados 
                 fa02_i_acumularsaldocontinuado = int4 = Acumular saldo medicamento continuado 
                 fa02_i_origemreceita = int4 = Origem da Receita 
                 fa02_i_avisoretirada = int4 = Aviso de Retirada 
                 fa02_i_cursor = int4 = Foco Entrega de Medicamento 
                 fa02_i_validalote = int4 = Validar Lote 
                 fa02_i_validavencimento = int4 = Validar Vencimento 
                 fa02_i_acaoprog = int4 = Ação programatica Padrão 
                 fa02_i_verificapacientehiperdia = int4 = Verificar pac. do hiperdia na retirada 
                 fa02_i_numdiasmedcontinativo = int4 = Nº dias encerramento med. continuado 
                 fa02_utilizaimpressoratermica = bool = Comprovante Impressora Térmica 
                 fa02_informa_receita_medicamento = bool = Informa tipo de receita do medicamento 
                 fa02_alerta_nao_morador = bool = Mostrar alerta não morador do munícipio 
                 fa02_ibge_municipio = int4 = Código IBGE 
                 fa02_utiliza_bnafar = bool = Utiliza Integração BNAFAR 
                 fa02_altera_validade_medcontinuado = bool = Permite alterar a validade do med. continuado 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("far_parametros"); 
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
       $this->fa02_i_codigo = ($this->fa02_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_codigo"]:$this->fa02_i_codigo);
       $this->fa02_i_dbestrutura = ($this->fa02_i_dbestrutura == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_dbestrutura"]:$this->fa02_i_dbestrutura);
       $this->fa02_c_descr = ($this->fa02_c_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_c_descr"]:$this->fa02_c_descr);
       $this->fa02_c_digitacao = ($this->fa02_c_digitacao == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_c_digitacao"]:$this->fa02_c_digitacao);
       $this->fa02_b_comprovante = ($this->fa02_b_comprovante == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_b_comprovante"]:$this->fa02_b_comprovante);
       $this->fa02_b_numestoque = ($this->fa02_b_numestoque == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_b_numestoque"]:$this->fa02_b_numestoque);
       $this->fa02_b_novaretirada = ($this->fa02_b_novaretirada == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_b_novaretirada"]:$this->fa02_b_novaretirada);
       $this->fa02_i_tipoperiodocontinuado = ($this->fa02_i_tipoperiodocontinuado == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_tipoperiodocontinuado"]:$this->fa02_i_tipoperiodocontinuado);
       $this->fa02_i_acumularsaldocontinuado = ($this->fa02_i_acumularsaldocontinuado == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_acumularsaldocontinuado"]:$this->fa02_i_acumularsaldocontinuado);
       $this->fa02_i_origemreceita = ($this->fa02_i_origemreceita == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_origemreceita"]:$this->fa02_i_origemreceita);
       $this->fa02_i_avisoretirada = ($this->fa02_i_avisoretirada == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_avisoretirada"]:$this->fa02_i_avisoretirada);
       $this->fa02_i_cursor = ($this->fa02_i_cursor == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_cursor"]:$this->fa02_i_cursor);
       $this->fa02_i_validalote = ($this->fa02_i_validalote == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_validalote"]:$this->fa02_i_validalote);
       $this->fa02_i_validavencimento = ($this->fa02_i_validavencimento == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_validavencimento"]:$this->fa02_i_validavencimento);
       $this->fa02_i_acaoprog = ($this->fa02_i_acaoprog == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_acaoprog"]:$this->fa02_i_acaoprog);
       $this->fa02_i_verificapacientehiperdia = ($this->fa02_i_verificapacientehiperdia == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_verificapacientehiperdia"]:$this->fa02_i_verificapacientehiperdia);
       $this->fa02_i_numdiasmedcontinativo = ($this->fa02_i_numdiasmedcontinativo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_numdiasmedcontinativo"]:$this->fa02_i_numdiasmedcontinativo);
       $this->fa02_utilizaimpressoratermica = ($this->fa02_utilizaimpressoratermica == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_utilizaimpressoratermica"]:$this->fa02_utilizaimpressoratermica);
       $this->fa02_informa_receita_medicamento = ($this->fa02_informa_receita_medicamento == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_informa_receita_medicamento"]:$this->fa02_informa_receita_medicamento);
       $this->fa02_alerta_nao_morador = ($this->fa02_alerta_nao_morador == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_alerta_nao_morador"]:$this->fa02_alerta_nao_morador);
       $this->fa02_ibge_municipio = ($this->fa02_ibge_municipio == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_ibge_municipio"]:$this->fa02_ibge_municipio);
       $this->fa02_utiliza_bnafar = ($this->fa02_utiliza_bnafar == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_utiliza_bnafar"]:$this->fa02_utiliza_bnafar);
       $this->fa02_altera_validade_medcontinuado = ($this->fa02_altera_validade_medcontinuado == "f"?@$GLOBALS["HTTP_POST_VARS"]["fa02_altera_validade_medcontinuado"]:$this->fa02_altera_validade_medcontinuado);
     }else{
       $this->fa02_i_codigo = ($this->fa02_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa02_i_codigo"]:$this->fa02_i_codigo);
     }
   }

    public function incluir($fa02_i_codigo)
    {
      $this->atualizacampos();
     if($this->fa02_i_dbestrutura == null ){ 
       $this->erro_sql = " Campo Estrutura não informado.";
       $this->erro_campo = "fa02_i_dbestrutura";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_c_descr == null ){ 
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "fa02_c_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_c_digitacao == null ){ 
       $this->erro_sql = " Campo Digitação não informado.";
       $this->erro_campo = "fa02_c_digitacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_b_comprovante == null ){ 
       $this->erro_sql = " Campo Comprovante Automatico não informado.";
       $this->erro_campo = "fa02_b_comprovante";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_b_numestoque == null ){ 
       $this->erro_sql = " Campo Numeração do estoque não informado.";
       $this->erro_campo = "fa02_b_numestoque";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_b_novaretirada == null ){ 
       $this->erro_sql = " Campo Nova retirada automática não informado.";
       $this->erro_campo = "fa02_b_novaretirada";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_tipoperiodocontinuado == null ){ 
       $this->erro_sql = " Campo Tipo de periodo dos medicamentos continuados não informado.";
       $this->erro_campo = "fa02_i_tipoperiodocontinuado";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_acumularsaldocontinuado == null ){ 
       $this->erro_sql = " Campo Acumular saldo medicamento continuado não informado.";
       $this->erro_campo = "fa02_i_acumularsaldocontinuado";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_origemreceita == null ){ 
       $this->erro_sql = " Campo Origem da Receita não informado.";
       $this->erro_campo = "fa02_i_origemreceita";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_avisoretirada == null ){ 
       $this->erro_sql = " Campo Aviso de Retirada não informado.";
       $this->erro_campo = "fa02_i_avisoretirada";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_cursor == null ){ 
       $this->erro_sql = " Campo Foco Entrega de Medicamento não informado.";
       $this->erro_campo = "fa02_i_cursor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_validalote == null ){ 
       $this->erro_sql = " Campo Validar Lote não informado.";
       $this->erro_campo = "fa02_i_validalote";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_validavencimento == null ){ 
       $this->erro_sql = " Campo Validar Vencimento não informado.";
       $this->erro_campo = "fa02_i_validavencimento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_acaoprog == null ){ 
       $this->fa02_i_acaoprog = "null";
     }
     if($this->fa02_i_verificapacientehiperdia == null ){ 
       $this->erro_sql = " Campo Verificar pac. do hiperdia na retirada não informado.";
       $this->erro_campo = "fa02_i_verificapacientehiperdia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_i_numdiasmedcontinativo == null ){ 
       $this->erro_sql = " Campo Nº dias encerramento med. continuado não informado.";
       $this->erro_campo = "fa02_i_numdiasmedcontinativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_utilizaimpressoratermica == null ){ 
       $this->erro_sql = " Campo Comprovante Impressora Térmica não informado.";
       $this->erro_campo = "fa02_utilizaimpressoratermica";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_informa_receita_medicamento == null ){ 
       $this->erro_sql = " Campo Informa tipo de receita do medicamento não informado.";
       $this->erro_campo = "fa02_informa_receita_medicamento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_alerta_nao_morador == null ){ 
       $this->erro_sql = " Campo Mostrar alerta não morador do munícipio não informado.";
       $this->erro_campo = "fa02_alerta_nao_morador";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_ibge_municipio == null ){ 
       $this->fa02_ibge_municipio = "null";
     }
     if($this->fa02_utiliza_bnafar == null ){ 
       $this->erro_sql = " Campo Utiliza Integração BNAFAR não informado.";
       $this->erro_campo = "fa02_utiliza_bnafar";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa02_altera_validade_medcontinuado == null ){ 
       $this->erro_sql = " Campo Permite alterar a validade do med. continuado não informado.";
       $this->erro_campo = "fa02_altera_validade_medcontinuado";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($fa02_i_codigo == "" || $fa02_i_codigo == null ){
       $result = db_query("select nextval('farparametros_fa02_i_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: farparametros_fa02_i_codigo_seq do campo: fa02_i_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->fa02_i_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from farparametros_fa02_i_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $fa02_i_codigo)){
         $this->erro_sql = " Campo fa02_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->fa02_i_codigo = $fa02_i_codigo; 
       }
     }
     if(($this->fa02_i_codigo == null) || ($this->fa02_i_codigo == "") ){ 
       $this->erro_sql = " Campo fa02_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into far_parametros(
                                       fa02_i_codigo 
                                      ,fa02_i_dbestrutura 
                                      ,fa02_c_descr 
                                      ,fa02_c_digitacao 
                                      ,fa02_b_comprovante 
                                      ,fa02_b_numestoque 
                                      ,fa02_b_novaretirada 
                                      ,fa02_i_tipoperiodocontinuado 
                                      ,fa02_i_acumularsaldocontinuado 
                                      ,fa02_i_origemreceita 
                                      ,fa02_i_avisoretirada 
                                      ,fa02_i_cursor 
                                      ,fa02_i_validalote 
                                      ,fa02_i_validavencimento 
                                      ,fa02_i_acaoprog 
                                      ,fa02_i_verificapacientehiperdia 
                                      ,fa02_i_numdiasmedcontinativo 
                                      ,fa02_utilizaimpressoratermica 
                                      ,fa02_informa_receita_medicamento 
                                      ,fa02_alerta_nao_morador 
                                      ,fa02_ibge_municipio 
                                      ,fa02_utiliza_bnafar 
                                      ,fa02_altera_validade_medcontinuado 
                       )
                values (
                                $this->fa02_i_codigo 
                               ,$this->fa02_i_dbestrutura 
                               ,'$this->fa02_c_descr' 
                               ,'$this->fa02_c_digitacao' 
                               ,'$this->fa02_b_comprovante' 
                               ,'$this->fa02_b_numestoque' 
                               ,'$this->fa02_b_novaretirada' 
                               ,$this->fa02_i_tipoperiodocontinuado 
                               ,$this->fa02_i_acumularsaldocontinuado 
                               ,$this->fa02_i_origemreceita 
                               ,$this->fa02_i_avisoretirada 
                               ,$this->fa02_i_cursor 
                               ,$this->fa02_i_validalote 
                               ,$this->fa02_i_validavencimento 
                               ,$this->fa02_i_acaoprog 
                               ,$this->fa02_i_verificapacientehiperdia 
                               ,$this->fa02_i_numdiasmedcontinativo 
                               ,'$this->fa02_utilizaimpressoratermica' 
                               ,'$this->fa02_informa_receita_medicamento' 
                               ,'$this->fa02_alerta_nao_morador' 
                               ,$this->fa02_ibge_municipio 
                               ,'$this->fa02_utiliza_bnafar' 
                               ,'$this->fa02_altera_validade_medcontinuado' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "far_parametros ($this->fa02_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "far_parametros já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "far_parametros ($this->fa02_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fa02_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->fa02_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,12121,'$this->fa02_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2103,12121,'','".AddSlashes(pg_result($resaco,0,'fa02_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,12122,'','".AddSlashes(pg_result($resaco,0,'fa02_i_dbestrutura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,12123,'','".AddSlashes(pg_result($resaco,0,'fa02_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,12125,'','".AddSlashes(pg_result($resaco,0,'fa02_c_digitacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,14685,'','".AddSlashes(pg_result($resaco,0,'fa02_b_comprovante'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,14870,'','".AddSlashes(pg_result($resaco,0,'fa02_b_numestoque'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,15323,'','".AddSlashes(pg_result($resaco,0,'fa02_b_novaretirada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,15910,'','".AddSlashes(pg_result($resaco,0,'fa02_i_tipoperiodocontinuado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,15911,'','".AddSlashes(pg_result($resaco,0,'fa02_i_acumularsaldocontinuado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,16766,'','".AddSlashes(pg_result($resaco,0,'fa02_i_origemreceita'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,17194,'','".AddSlashes(pg_result($resaco,0,'fa02_i_avisoretirada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,17295,'','".AddSlashes(pg_result($resaco,0,'fa02_i_cursor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,17364,'','".AddSlashes(pg_result($resaco,0,'fa02_i_validalote'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,17365,'','".AddSlashes(pg_result($resaco,0,'fa02_i_validavencimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,17366,'','".AddSlashes(pg_result($resaco,0,'fa02_i_acaoprog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,17389,'','".AddSlashes(pg_result($resaco,0,'fa02_i_verificapacientehiperdia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,17405,'','".AddSlashes(pg_result($resaco,0,'fa02_i_numdiasmedcontinativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,18508,'','".AddSlashes(pg_result($resaco,0,'fa02_utilizaimpressoratermica'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,1013491,'','".AddSlashes(pg_result($resaco,0,'fa02_informa_receita_medicamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,1014215,'','".AddSlashes(pg_result($resaco,0,'fa02_alerta_nao_morador'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,1014216,'','".AddSlashes(pg_result($resaco,0,'fa02_ibge_municipio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,1014053,'','".AddSlashes(pg_result($resaco,0,'fa02_utiliza_bnafar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2103,1015667,'','".AddSlashes(pg_result($resaco,0,'fa02_altera_validade_medcontinuado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($fa02_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update far_parametros set ";
     $virgula = "";
     if(trim($this->fa02_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_codigo"])){ 
       $sql  .= $virgula." fa02_i_codigo = $this->fa02_i_codigo ";
       $virgula = ",";
       if(trim($this->fa02_i_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "fa02_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_dbestrutura)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_dbestrutura"])){ 
       $sql  .= $virgula." fa02_i_dbestrutura = $this->fa02_i_dbestrutura ";
       $virgula = ",";
       if(trim($this->fa02_i_dbestrutura) == null ){ 
         $this->erro_sql = " Campo Estrutura não informado.";
         $this->erro_campo = "fa02_i_dbestrutura";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_c_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_c_descr"])){ 
       $sql  .= $virgula." fa02_c_descr = '$this->fa02_c_descr' ";
       $virgula = ",";
       if(trim($this->fa02_c_descr) == null ){ 
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "fa02_c_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_c_digitacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_c_digitacao"])){ 
       $sql  .= $virgula." fa02_c_digitacao = '$this->fa02_c_digitacao' ";
       $virgula = ",";
       if(trim($this->fa02_c_digitacao) == null ){ 
         $this->erro_sql = " Campo Digitação não informado.";
         $this->erro_campo = "fa02_c_digitacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_b_comprovante)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_b_comprovante"])){ 
       $sql  .= $virgula." fa02_b_comprovante = '$this->fa02_b_comprovante' ";
       $virgula = ",";
       if(trim($this->fa02_b_comprovante) == null ){ 
         $this->erro_sql = " Campo Comprovante Automatico não informado.";
         $this->erro_campo = "fa02_b_comprovante";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_b_numestoque)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_b_numestoque"])){ 
       $sql  .= $virgula." fa02_b_numestoque = '$this->fa02_b_numestoque' ";
       $virgula = ",";
       if(trim($this->fa02_b_numestoque) == null ){ 
         $this->erro_sql = " Campo Numeração do estoque não informado.";
         $this->erro_campo = "fa02_b_numestoque";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_b_novaretirada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_b_novaretirada"])){ 
       $sql  .= $virgula." fa02_b_novaretirada = '$this->fa02_b_novaretirada' ";
       $virgula = ",";
       if(trim($this->fa02_b_novaretirada) == null ){ 
         $this->erro_sql = " Campo Nova retirada automática não informado.";
         $this->erro_campo = "fa02_b_novaretirada";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_tipoperiodocontinuado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_tipoperiodocontinuado"])){ 
       $sql  .= $virgula." fa02_i_tipoperiodocontinuado = $this->fa02_i_tipoperiodocontinuado ";
       $virgula = ",";
       if(trim($this->fa02_i_tipoperiodocontinuado) == null ){ 
         $this->erro_sql = " Campo Tipo de periodo dos medicamentos continuados não informado.";
         $this->erro_campo = "fa02_i_tipoperiodocontinuado";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_acumularsaldocontinuado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_acumularsaldocontinuado"])){ 
       $sql  .= $virgula." fa02_i_acumularsaldocontinuado = $this->fa02_i_acumularsaldocontinuado ";
       $virgula = ",";
       if(trim($this->fa02_i_acumularsaldocontinuado) == null ){ 
         $this->erro_sql = " Campo Acumular saldo medicamento continuado não informado.";
         $this->erro_campo = "fa02_i_acumularsaldocontinuado";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_origemreceita)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_origemreceita"])){ 
       $sql  .= $virgula." fa02_i_origemreceita = $this->fa02_i_origemreceita ";
       $virgula = ",";
       if(trim($this->fa02_i_origemreceita) == null ){ 
         $this->erro_sql = " Campo Origem da Receita não informado.";
         $this->erro_campo = "fa02_i_origemreceita";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_avisoretirada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_avisoretirada"])){ 
       $sql  .= $virgula." fa02_i_avisoretirada = $this->fa02_i_avisoretirada ";
       $virgula = ",";
       if(trim($this->fa02_i_avisoretirada) == null ){ 
         $this->erro_sql = " Campo Aviso de Retirada não informado.";
         $this->erro_campo = "fa02_i_avisoretirada";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_cursor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_cursor"])){ 
       $sql  .= $virgula." fa02_i_cursor = $this->fa02_i_cursor ";
       $virgula = ",";
       if(trim($this->fa02_i_cursor) == null ){ 
         $this->erro_sql = " Campo Foco Entrega de Medicamento não informado.";
         $this->erro_campo = "fa02_i_cursor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_validalote)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_validalote"])){ 
       $sql  .= $virgula." fa02_i_validalote = $this->fa02_i_validalote ";
       $virgula = ",";
       if(trim($this->fa02_i_validalote) == null ){ 
         $this->erro_sql = " Campo Validar Lote não informado.";
         $this->erro_campo = "fa02_i_validalote";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_validavencimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_validavencimento"])){ 
       $sql  .= $virgula." fa02_i_validavencimento = $this->fa02_i_validavencimento ";
       $virgula = ",";
       if(trim($this->fa02_i_validavencimento) == null ){ 
         $this->erro_sql = " Campo Validar Vencimento não informado.";
         $this->erro_campo = "fa02_i_validavencimento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_acaoprog)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_acaoprog"])){ 
        if(trim($this->fa02_i_acaoprog)=="" && isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_acaoprog"])){ 
           $this->fa02_i_acaoprog = "0" ; 
        } 
       $sql  .= $virgula." fa02_i_acaoprog = $this->fa02_i_acaoprog ";
       $virgula = ",";
     }
     if(trim($this->fa02_i_verificapacientehiperdia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_verificapacientehiperdia"])){ 
       $sql  .= $virgula." fa02_i_verificapacientehiperdia = $this->fa02_i_verificapacientehiperdia ";
       $virgula = ",";
       if(trim($this->fa02_i_verificapacientehiperdia) == null ){ 
         $this->erro_sql = " Campo Verificar pac. do hiperdia na retirada não informado.";
         $this->erro_campo = "fa02_i_verificapacientehiperdia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_i_numdiasmedcontinativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_numdiasmedcontinativo"])){ 
       $sql  .= $virgula." fa02_i_numdiasmedcontinativo = $this->fa02_i_numdiasmedcontinativo ";
       $virgula = ",";
       if(trim($this->fa02_i_numdiasmedcontinativo) == null ){ 
         $this->erro_sql = " Campo Nº dias encerramento med. continuado não informado.";
         $this->erro_campo = "fa02_i_numdiasmedcontinativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_utilizaimpressoratermica)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_utilizaimpressoratermica"])){ 
       $sql  .= $virgula." fa02_utilizaimpressoratermica = '$this->fa02_utilizaimpressoratermica' ";
       $virgula = ",";
       if(trim($this->fa02_utilizaimpressoratermica) == null ){ 
         $this->erro_sql = " Campo Comprovante Impressora Térmica não informado.";
         $this->erro_campo = "fa02_utilizaimpressoratermica";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_informa_receita_medicamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_informa_receita_medicamento"])){ 
       $sql  .= $virgula." fa02_informa_receita_medicamento = '$this->fa02_informa_receita_medicamento' ";
       $virgula = ",";
       if(trim($this->fa02_informa_receita_medicamento) == null ){ 
         $this->erro_sql = " Campo Informa tipo de receita do medicamento não informado.";
         $this->erro_campo = "fa02_informa_receita_medicamento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_alerta_nao_morador)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_alerta_nao_morador"])){ 
       $sql  .= $virgula." fa02_alerta_nao_morador = '$this->fa02_alerta_nao_morador' ";
       $virgula = ",";
       if(trim($this->fa02_alerta_nao_morador) == null ){ 
         $this->erro_sql = " Campo Mostrar alerta não morador do munícipio não informado.";
         $this->erro_campo = "fa02_alerta_nao_morador";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_ibge_municipio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_ibge_municipio"])){ 
        if(trim($this->fa02_ibge_municipio)=="" && isset($GLOBALS["HTTP_POST_VARS"]["fa02_ibge_municipio"])){ 
           $this->fa02_ibge_municipio = "null" ; 
        } 
       $sql  .= $virgula." fa02_ibge_municipio = $this->fa02_ibge_municipio ";
       $virgula = ",";
     }
     if(trim($this->fa02_utiliza_bnafar)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_utiliza_bnafar"])){ 
       $sql  .= $virgula." fa02_utiliza_bnafar = '$this->fa02_utiliza_bnafar' ";
       $virgula = ",";
       if(trim($this->fa02_utiliza_bnafar) == null ){ 
         $this->erro_sql = " Campo Utiliza Integração BNAFAR não informado.";
         $this->erro_campo = "fa02_utiliza_bnafar";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa02_altera_validade_medcontinuado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa02_altera_validade_medcontinuado"])){ 
       $sql  .= $virgula." fa02_altera_validade_medcontinuado = '$this->fa02_altera_validade_medcontinuado' ";
       $virgula = ",";
       if(trim($this->fa02_altera_validade_medcontinuado) == null ){ 
         $this->erro_sql = " Campo Permite alterar a validade do med. continuado não informado.";
         $this->erro_campo = "fa02_altera_validade_medcontinuado";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($fa02_i_codigo!=null){
       $sql .= " fa02_i_codigo = $this->fa02_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->fa02_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,12121,'$this->fa02_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_codigo"]) || $this->fa02_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2103,12121,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_codigo'))."','$this->fa02_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_dbestrutura"]) || $this->fa02_i_dbestrutura != "")
             $resac = db_query("insert into db_acount values($acount,2103,12122,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_dbestrutura'))."','$this->fa02_i_dbestrutura',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_c_descr"]) || $this->fa02_c_descr != "")
             $resac = db_query("insert into db_acount values($acount,2103,12123,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_c_descr'))."','$this->fa02_c_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_c_digitacao"]) || $this->fa02_c_digitacao != "")
             $resac = db_query("insert into db_acount values($acount,2103,12125,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_c_digitacao'))."','$this->fa02_c_digitacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_b_comprovante"]) || $this->fa02_b_comprovante != "")
             $resac = db_query("insert into db_acount values($acount,2103,14685,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_b_comprovante'))."','$this->fa02_b_comprovante',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_b_numestoque"]) || $this->fa02_b_numestoque != "")
             $resac = db_query("insert into db_acount values($acount,2103,14870,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_b_numestoque'))."','$this->fa02_b_numestoque',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_b_novaretirada"]) || $this->fa02_b_novaretirada != "")
             $resac = db_query("insert into db_acount values($acount,2103,15323,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_b_novaretirada'))."','$this->fa02_b_novaretirada',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_tipoperiodocontinuado"]) || $this->fa02_i_tipoperiodocontinuado != "")
             $resac = db_query("insert into db_acount values($acount,2103,15910,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_tipoperiodocontinuado'))."','$this->fa02_i_tipoperiodocontinuado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_acumularsaldocontinuado"]) || $this->fa02_i_acumularsaldocontinuado != "")
             $resac = db_query("insert into db_acount values($acount,2103,15911,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_acumularsaldocontinuado'))."','$this->fa02_i_acumularsaldocontinuado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_origemreceita"]) || $this->fa02_i_origemreceita != "")
             $resac = db_query("insert into db_acount values($acount,2103,16766,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_origemreceita'))."','$this->fa02_i_origemreceita',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_avisoretirada"]) || $this->fa02_i_avisoretirada != "")
             $resac = db_query("insert into db_acount values($acount,2103,17194,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_avisoretirada'))."','$this->fa02_i_avisoretirada',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_cursor"]) || $this->fa02_i_cursor != "")
             $resac = db_query("insert into db_acount values($acount,2103,17295,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_cursor'))."','$this->fa02_i_cursor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_validalote"]) || $this->fa02_i_validalote != "")
             $resac = db_query("insert into db_acount values($acount,2103,17364,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_validalote'))."','$this->fa02_i_validalote',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_validavencimento"]) || $this->fa02_i_validavencimento != "")
             $resac = db_query("insert into db_acount values($acount,2103,17365,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_validavencimento'))."','$this->fa02_i_validavencimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_acaoprog"]) || $this->fa02_i_acaoprog != "")
             $resac = db_query("insert into db_acount values($acount,2103,17366,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_acaoprog'))."','$this->fa02_i_acaoprog',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_verificapacientehiperdia"]) || $this->fa02_i_verificapacientehiperdia != "")
             $resac = db_query("insert into db_acount values($acount,2103,17389,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_verificapacientehiperdia'))."','$this->fa02_i_verificapacientehiperdia',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_i_numdiasmedcontinativo"]) || $this->fa02_i_numdiasmedcontinativo != "")
             $resac = db_query("insert into db_acount values($acount,2103,17405,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_i_numdiasmedcontinativo'))."','$this->fa02_i_numdiasmedcontinativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_utilizaimpressoratermica"]) || $this->fa02_utilizaimpressoratermica != "")
             $resac = db_query("insert into db_acount values($acount,2103,18508,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_utilizaimpressoratermica'))."','$this->fa02_utilizaimpressoratermica',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_informa_receita_medicamento"]) || $this->fa02_informa_receita_medicamento != "")
             $resac = db_query("insert into db_acount values($acount,2103,1013491,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_informa_receita_medicamento'))."','$this->fa02_informa_receita_medicamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_alerta_nao_morador"]) || $this->fa02_alerta_nao_morador != "")
             $resac = db_query("insert into db_acount values($acount,2103,1014215,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_alerta_nao_morador'))."','$this->fa02_alerta_nao_morador',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_ibge_municipio"]) || $this->fa02_ibge_municipio != "")
             $resac = db_query("insert into db_acount values($acount,2103,1014216,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_ibge_municipio'))."','$this->fa02_ibge_municipio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_utiliza_bnafar"]) || $this->fa02_utiliza_bnafar != "")
             $resac = db_query("insert into db_acount values($acount,2103,1014053,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_utiliza_bnafar'))."','$this->fa02_utiliza_bnafar',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["fa02_altera_validade_medcontinuado"]) || $this->fa02_altera_validade_medcontinuado != "")
             $resac = db_query("insert into db_acount values($acount,2103,1015667,'".AddSlashes(pg_result($resaco,$conresaco,'fa02_altera_validade_medcontinuado'))."','$this->fa02_altera_validade_medcontinuado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "far_parametros não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->fa02_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "far_parametros não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->fa02_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fa02_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($fa02_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($fa02_i_codigo));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,12121,'$fa02_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2103,12121,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,12122,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_dbestrutura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,12123,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_c_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,12125,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_c_digitacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,14685,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_b_comprovante'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,14870,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_b_numestoque'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,15323,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_b_novaretirada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,15910,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_tipoperiodocontinuado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,15911,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_acumularsaldocontinuado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,16766,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_origemreceita'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,17194,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_avisoretirada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,17295,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_cursor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,17364,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_validalote'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,17365,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_validavencimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,17366,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_acaoprog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,17389,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_verificapacientehiperdia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,17405,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_i_numdiasmedcontinativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,18508,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_utilizaimpressoratermica'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,1013491,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_informa_receita_medicamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,1014215,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_alerta_nao_morador'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,1014216,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_ibge_municipio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,1014053,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_utiliza_bnafar'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2103,1015667,'','".AddSlashes(pg_result($resaco,$iresaco,'fa02_altera_validade_medcontinuado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from far_parametros
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($fa02_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " fa02_i_codigo = $fa02_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "far_parametros não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$fa02_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "far_parametros não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$fa02_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$fa02_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:far_parametros";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($fa02_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from far_parametros ";
     $sql .= "      inner join db_estrutura  on  db_estrutura.db77_codestrut = far_parametros.fa02_i_dbestrutura";
     $sql .= "       left join far_programa  on  far_programa.fa12_i_codigo = far_parametros.fa02_i_acaoprog";
     $sql .= "       left join sau_tipoacaoprog  on  sau_tipoacaoprog.s148_i_codigo = far_programa.fa12_i_tipoacao";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fa02_i_codigo)) {
         $sql2 .= " where far_parametros.fa02_i_codigo = $fa02_i_codigo "; 
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

    public function sql_query_file($fa02_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from far_parametros ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fa02_i_codigo)){
         $sql2 .= " where far_parametros.fa02_i_codigo = $fa02_i_codigo "; 
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

  public function sql_query2 ( $fa02_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from far_parametros ";
     $sql .= "      left join db_estrutura  on  db_estrutura.db77_codestrut = far_parametros.fa02_i_dbestrutura";
     $sql .= "      left join far_programa  on  far_programa.fa12_i_codigo = far_parametros.fa02_i_acaoprog";
     $sql .= "      left join sau_tipoacaoprog  on  sau_tipoacaoprog.s148_i_codigo = far_programa.fa12_i_tipoacao";
     $sql2 = "";
     if($dbwhere==""){
       if($fa02_i_codigo!=null ){
         $sql2 .= " where far_parametros.fa02_i_codigo = $fa02_i_codigo ";
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
