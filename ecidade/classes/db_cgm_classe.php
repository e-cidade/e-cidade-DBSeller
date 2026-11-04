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
//CLASSE DA ENTIDADE cgm
class cl_cgm {
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
   public $z01_numcgm = 0;
   public $z01_nome = null;
   public $z01_ender = null;
   public $z01_numero = 0;
   public $z01_compl = null;
   public $z01_bairro = null;
   public $z01_munic = null;
   public $z01_uf = null;
   public $z01_cep = null;
   public $z01_cxpostal = null;
   public $z01_cadast_dia = null;
   public $z01_cadast_mes = null;
   public $z01_cadast_ano = null;
   public $z01_cadast = null;
   public $z01_telef = null;
   public $z01_ident = null;
   public $z01_login = 0;
   public $z01_incest = null;
   public $z01_telcel = null;
   public $z01_email = null;
   public $z01_endcon = null;
   public $z01_numcon = 0;
   public $z01_comcon = null;
   public $z01_baicon = null;
   public $z01_muncon = null;
   public $z01_ufcon = null;
   public $z01_cepcon = null;
   public $z01_cxposcon = null;
   public $z01_telcon = null;
   public $z01_celcon = null;
   public $z01_emailc = null;
   public $z01_nacion = 0;
   public $z01_estciv = 0;
   public $z01_profis = null;
   public $z01_tipcre = 0;
   public $z01_cgccpf = null;
   public $z01_fax = null;
   public $z01_nasc_dia = null;
   public $z01_nasc_mes = null;
   public $z01_nasc_ano = null;
   public $z01_nasc = null;
   public $z01_pai = null;
   public $z01_mae = null;
   public $z01_sexo = null;
   public $z01_ultalt_dia = null;
   public $z01_ultalt_mes = null;
   public $z01_ultalt_ano = null;
   public $z01_ultalt = null;
   public $z01_contato = null;
   public $z01_hora = null;
   public $z01_nomefanta = null;
   public $z01_cnh = null;
   public $z01_categoria = null;
   public $z01_dtemissao_dia = null;
   public $z01_dtemissao_mes = null;
   public $z01_dtemissao_ano = null;
   public $z01_dtemissao = null;
   public $z01_dthabilitacao_dia = null;
   public $z01_dthabilitacao_mes = null;
   public $z01_dthabilitacao_ano = null;
   public $z01_dthabilitacao = null;
   public $z01_nomecomple = null;
   public $z01_dtvencimento_dia = null;
   public $z01_dtvencimento_mes = null;
   public $z01_dtvencimento_ano = null;
   public $z01_dtvencimento = null;
   public $z01_dtfalecimento_dia = null;
   public $z01_dtfalecimento_mes = null;
   public $z01_dtfalecimento_ano = null;
   public $z01_dtfalecimento = null;
   public $z01_escolaridade = null;
   public $z01_naturalidade = null;
   public $z01_identdtexp_dia = null;
   public $z01_identdtexp_mes = null;
   public $z01_identdtexp_ano = null;
   public $z01_identdtexp = null;
   public $z01_identorgao = null;
   public $z01_localtrabalho = null;
   public $z01_renda = 0;
   public $z01_trabalha = 'f';
   public $z01_pis = null;
   public $z01_obs = null;
   public $z01_genero = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 z01_numcgm = int4 = Numcgm
                 z01_nome = varchar(40) = Nome/Razão Social
                 z01_ender = varchar(100) = Endereço
                 z01_numero = int4 = Numero
                 z01_compl = varchar(100) = Complemento
                 z01_bairro = varchar(40) = Bairro
                 z01_munic = varchar(40) = Município
                 z01_uf = varchar(2) = UF
                 z01_cep = varchar(8) = CEP
                 z01_cxpostal = varchar(20) = Caixa Postal
                 z01_cadast = date = Data do cadastramento
                 z01_telef = varchar(12) = Telefone
                 z01_ident = varchar(20) = Identidade
                 z01_login = int4 = Login
                 z01_incest = varchar(15) = Inscricao Estadual
                 z01_telcel = varchar(12) = Celular
                 z01_email = varchar(100) = email
                 z01_endcon = varchar(100) = Endereco Comercial
                 z01_numcon = int4 = Numero
                 z01_comcon = varchar(20) = Complemento
                 z01_baicon = varchar(40) = Bairro
                 z01_muncon = varchar(40) = Municipio Comercial
                 z01_ufcon = varchar(2) = Estado Comercial
                 z01_cepcon = varchar(8) = CEP
                 z01_cxposcon = varchar(20) = Caixa Postal
                 z01_telcon = varchar(12) = Telefone comercial
                 z01_celcon = varchar(12) = Celular comercial
                 z01_emailc = varchar(100) = email comercial
                 z01_nacion = int4 = Nacionalidade
                 z01_estciv = int4 = Estado civil
                 z01_profis = varchar(40) = Profissao
                 z01_tipcre = int4 = Tipo de credor
                 z01_cgccpf = varchar(14) = CNPJ/CPF
                 z01_fax = varchar(12) = Fax
                 z01_nasc = date = Nascimento
                 z01_pai = varchar(40) = Pai
                 z01_mae = varchar(40) = Mãe
                 z01_sexo = varchar(1) = Sexo
                 z01_ultalt = date = Ultima Alteração
                 z01_contato = varchar(40) = Contato
                 z01_hora = varchar(5) = Hora do Cadastramento
                 z01_nomefanta = varchar(100) = Nome Fantasia
                 z01_cnh = varchar(20) = CNH
                 z01_categoria = varchar(2) = Categoria CNH
                 z01_dtemissao = date = Emissão CNH
                 z01_dthabilitacao = date = Primeira CNH
                 z01_nomecomple = varchar(100) = Nome Completo
                 z01_dtvencimento = date = Vencimento CNH
                 z01_dtfalecimento = date = Falecimento
                 z01_escolaridade = varchar(50) = Escolaridade
                 z01_naturalidade = varchar(100) = Naturalidade
                 z01_identdtexp = date = Data Expedição
                 z01_identorgao = varchar(50) = Orgão Emissor
                 z01_localtrabalho = varchar(100) = Local de Trabalho
                 z01_renda = float4 = Renda
                 z01_trabalha = bool = Trabalha
                 z01_pis = varchar(11) = Pis/Pasep/CI
                 z01_obs = text = Observações
                 ";
   //funcao construtor da classe
   public function __construct(){
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("cgm");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro
   public function erro($mostra=null,$retorna=null) {
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
       $this->z01_numcgm = ($this->z01_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_numcgm"]:$this->z01_numcgm);
       $this->z01_nome = ($this->z01_nome == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_nome"]:$this->z01_nome);
       $this->z01_ender = ($this->z01_ender == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_ender"]:$this->z01_ender);
       $this->z01_numero = ($this->z01_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_numero"]:$this->z01_numero);
       $this->z01_compl = ($this->z01_compl == "" && isset($GLOBALS["HTTP_POST_VARS"]["z01_compl"]) ? $GLOBALS["HTTP_POST_VARS"]["z01_compl"] : $this->z01_compl);
       $this->z01_bairro = ($this->z01_bairro == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_bairro"]:$this->z01_bairro);
       $this->z01_munic = ($this->z01_munic == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_munic"]:$this->z01_munic);
       $this->z01_uf = ($this->z01_uf == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_uf"]:$this->z01_uf);
       $this->z01_cep = ($this->z01_cep == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_cep"]:$this->z01_cep);
       $this->z01_cxpostal = (is_null($this->z01_cxpostal)?@$GLOBALS["HTTP_POST_VARS"]["z01_cxpostal"]:$this->z01_cxpostal);
       if($this->z01_cadast == ""){
         $this->z01_cadast_dia = ($this->z01_cadast_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_cadast_dia"]:$this->z01_cadast_dia);
         $this->z01_cadast_mes = ($this->z01_cadast_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_cadast_mes"]:$this->z01_cadast_mes);
         $this->z01_cadast_ano = ($this->z01_cadast_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_cadast_ano"]:$this->z01_cadast_ano);
         if($this->z01_cadast_dia != ""){
            $this->z01_cadast = $this->z01_cadast_ano."-".$this->z01_cadast_mes."-".$this->z01_cadast_dia;
         }
       }
       $this->z01_telef = (is_null($this->z01_telef)?@$GLOBALS["HTTP_POST_VARS"]["z01_telef"]:$this->z01_telef);
       $this->z01_ident = ($this->z01_ident == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_ident"]:$this->z01_ident);
       $this->z01_login = ($this->z01_login == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_login"]:$this->z01_login);
       $this->z01_incest = (is_null($this->z01_incest)?@$GLOBALS["HTTP_POST_VARS"]["z01_incest"]:$this->z01_incest);
       $this->z01_telcel = (is_null($this->z01_telcel)?@$GLOBALS["HTTP_POST_VARS"]["z01_telcel"]:$this->z01_telcel);
       $this->z01_email = (is_null($this->z01_email)?@$GLOBALS["HTTP_POST_VARS"]["z01_email"]:$this->z01_email);
       $this->z01_endcon = ($this->z01_endcon == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_endcon"]:$this->z01_endcon);
       $this->z01_numcon = ($this->z01_numcon == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_numcon"]:$this->z01_numcon);
       $this->z01_comcon = ($this->z01_comcon == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_comcon"]:$this->z01_comcon);
       $this->z01_baicon = ($this->z01_baicon == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_baicon"]:$this->z01_baicon);
       $this->z01_muncon = ($this->z01_muncon == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_muncon"]:$this->z01_muncon);
       $this->z01_ufcon = ($this->z01_ufcon == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_ufcon"]:$this->z01_ufcon);
       $this->z01_cepcon = ($this->z01_cepcon == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_cepcon"]:$this->z01_cepcon);
       $this->z01_cxposcon = (is_null($this->z01_cxposcon)?@$GLOBALS["HTTP_POST_VARS"]["z01_cxposcon"]:$this->z01_cxposcon);
       $this->z01_telcon = (is_null($this->z01_telcon)?@$GLOBALS["HTTP_POST_VARS"]["z01_telcon"]:$this->z01_telcon);
       $this->z01_celcon = (is_null($this->z01_celcon)?@$GLOBALS["HTTP_POST_VARS"]["z01_celcon"]:$this->z01_celcon);
       $this->z01_emailc = (is_null($this->z01_emailc)?@$GLOBALS["HTTP_POST_VARS"]["z01_emailc"]:$this->z01_emailc);
       $this->z01_nacion = ($this->z01_nacion == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_nacion"]:$this->z01_nacion);
       $this->z01_estciv = ($this->z01_estciv == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_estciv"]:$this->z01_estciv);
       $this->z01_profis = ($this->z01_profis == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_profis"]:$this->z01_profis);
       $this->z01_tipcre = ($this->z01_tipcre == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_tipcre"]:$this->z01_tipcre);
       $this->z01_cgccpf = ($this->z01_cgccpf == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_cgccpf"]:$this->z01_cgccpf);
       $this->z01_fax = (is_null($this->z01_fax)?@$GLOBALS["HTTP_POST_VARS"]["z01_fax"]:$this->z01_fax);

       if($this->z01_nasc == ""){
         $this->z01_nasc_dia = (is_null($this->z01_nasc_dia)?@$GLOBALS["HTTP_POST_VARS"]["z01_nasc_dia"]:$this->z01_nasc_dia);
         $this->z01_nasc_mes = (is_null($this->z01_nasc_mes)?@$GLOBALS["HTTP_POST_VARS"]["z01_nasc_mes"]:$this->z01_nasc_mes);
         $this->z01_nasc_ano = (is_null($this->z01_nasc_ano)?@$GLOBALS["HTTP_POST_VARS"]["z01_nasc_ano"]:$this->z01_nasc_ano);
         $this->z01_nasc = null;
         if($this->z01_nasc_dia != ""){
            $this->z01_nasc = $this->z01_nasc_ano."-".$this->z01_nasc_mes."-".$this->z01_nasc_dia;
         }
       }
       $this->z01_pai = ($this->z01_pai == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_pai"]:$this->z01_pai);
       $this->z01_mae = ($this->z01_mae == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_mae"]:$this->z01_mae);
       $this->z01_sexo = ($this->z01_sexo == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_sexo"]:$this->z01_sexo);
       if($this->z01_ultalt == ""){
         $this->z01_ultalt_dia = ($this->z01_ultalt_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_ultalt_dia"]:$this->z01_ultalt_dia);
         $this->z01_ultalt_mes = ($this->z01_ultalt_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_ultalt_mes"]:$this->z01_ultalt_mes);
         $this->z01_ultalt_ano = ($this->z01_ultalt_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_ultalt_ano"]:$this->z01_ultalt_ano);
         if($this->z01_ultalt_dia != ""){
            $this->z01_ultalt = $this->z01_ultalt_ano."-".$this->z01_ultalt_mes."-".$this->z01_ultalt_dia;
         }
       }
       $this->z01_contato = (is_null($this->z01_contato)?@$GLOBALS["HTTP_POST_VARS"]["z01_contato"]:$this->z01_contato);
       $this->z01_hora = ($this->z01_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_hora"]:$this->z01_hora);
       $this->z01_nomefanta = (is_null($this->z01_nomefanta)? (isset($GLOBALS["HTTP_POST_VARS"]["z01_nomefanta"]) ? $GLOBALS["HTTP_POST_VARS"]["z01_nomefanta"] : null) : $this->z01_nomefanta);
       $this->z01_cnh = ($this->z01_cnh == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_cnh"]:$this->z01_cnh);
       $this->z01_categoria = ($this->z01_categoria == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_categoria"]:$this->z01_categoria);
       if($this->z01_dtemissao == ""){
         $this->z01_dtemissao_dia = ($this->z01_dtemissao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtemissao_dia"]:$this->z01_dtemissao_dia);
         $this->z01_dtemissao_mes = ($this->z01_dtemissao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtemissao_mes"]:$this->z01_dtemissao_mes);
         $this->z01_dtemissao_ano = ($this->z01_dtemissao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtemissao_ano"]:$this->z01_dtemissao_ano);
         if($this->z01_dtemissao_dia != ""){
            $this->z01_dtemissao = $this->z01_dtemissao_ano."-".$this->z01_dtemissao_mes."-".$this->z01_dtemissao_dia;
         }
       }
       if($this->z01_dthabilitacao == ""){
         $this->z01_dthabilitacao_dia = ($this->z01_dthabilitacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao_dia"]:$this->z01_dthabilitacao_dia);
         $this->z01_dthabilitacao_mes = ($this->z01_dthabilitacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao_mes"]:$this->z01_dthabilitacao_mes);
         $this->z01_dthabilitacao_ano = ($this->z01_dthabilitacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao_ano"]:$this->z01_dthabilitacao_ano);
         if($this->z01_dthabilitacao_dia != ""){
            $this->z01_dthabilitacao = $this->z01_dthabilitacao_ano."-".$this->z01_dthabilitacao_mes."-".$this->z01_dthabilitacao_dia;
         }
       }

       $this->z01_nomecomple = ($this->z01_nomecomple == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_nomecomple"]:$this->z01_nomecomple);
       if($this->z01_dtvencimento == ""){
         $this->z01_dtvencimento_dia = ($this->z01_dtvencimento_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento_dia"]:$this->z01_dtvencimento_dia);
         $this->z01_dtvencimento_mes = ($this->z01_dtvencimento_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento_mes"]:$this->z01_dtvencimento_mes);
         $this->z01_dtvencimento_ano = ($this->z01_dtvencimento_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento_ano"]:$this->z01_dtvencimento_ano);
         if($this->z01_dtvencimento_dia != ""){
            $this->z01_dtvencimento = $this->z01_dtvencimento_ano."-".$this->z01_dtvencimento_mes."-".$this->z01_dtvencimento_dia;
         }
       }
       if($this->z01_dtfalecimento == ""){
         $this->z01_dtfalecimento_dia = ($this->z01_dtfalecimento_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtfalecimento_dia"]:$this->z01_dtfalecimento_dia);
         $this->z01_dtfalecimento_mes = ($this->z01_dtfalecimento_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtfalecimento_mes"]:$this->z01_dtfalecimento_mes);
         $this->z01_dtfalecimento_ano = ($this->z01_dtfalecimento_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_dtfalecimento_ano"]:$this->z01_dtfalecimento_ano);
         if($this->z01_dtfalecimento_dia != ""){
            $this->z01_dtfalecimento = $this->z01_dtfalecimento_ano."-".$this->z01_dtfalecimento_mes."-".$this->z01_dtfalecimento_dia;
         }
       }
       $this->z01_escolaridade = ($this->z01_escolaridade == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_escolaridade"]:$this->z01_escolaridade);
       $this->z01_naturalidade = ($this->z01_naturalidade == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_naturalidade"]:$this->z01_naturalidade);
       if($this->z01_identdtexp == ""){
         $this->z01_identdtexp_dia = ($this->z01_identdtexp_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_identdtexp_dia"]:$this->z01_identdtexp_dia);
         $this->z01_identdtexp_mes = ($this->z01_identdtexp_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_identdtexp_mes"]:$this->z01_identdtexp_mes);
         $this->z01_identdtexp_ano = ($this->z01_identdtexp_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_identdtexp_ano"]:$this->z01_identdtexp_ano);
         if($this->z01_identdtexp_dia != ""){
            $this->z01_identdtexp = $this->z01_identdtexp_ano."-".$this->z01_identdtexp_mes."-".$this->z01_identdtexp_dia;
         }
       }
       $this->z01_identorgao = ($this->z01_identorgao == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_identorgao"]:$this->z01_identorgao);
       $this->z01_localtrabalho = ($this->z01_localtrabalho == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_localtrabalho"]:$this->z01_localtrabalho);
       $this->z01_renda = ($this->z01_renda == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_renda"]:$this->z01_renda);
       $this->z01_trabalha = ($this->z01_trabalha == "f"?@$GLOBALS["HTTP_POST_VARS"]["z01_trabalha"]:$this->z01_trabalha);
       $this->z01_pis = ($this->z01_pis == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_pis"]:$this->z01_pis);
       $this->z01_obs = ($this->z01_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_obs"]:$this->z01_obs);
       $this->z01_genero = ($this->z01_genero == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_genero"]:$this->z01_genero);
     }else{
       $this->z01_numcgm = ($this->z01_numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["z01_numcgm"]:$this->z01_numcgm);
     }
   }
   // funcao para inclusao
   public function incluir ($z01_numcgm){
      $this->atualizacampos();
     if($this->z01_nome == null ){
       $this->erro_sql = " Campo Nome/Razão Social nao Informado.";
       $this->erro_campo = "z01_nome";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->z01_ender == null ){
       $this->erro_sql = " Campo Endereço nao Informado.";
       $this->erro_campo = "z01_ender";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->z01_numero == null ){
       $this->z01_numero = "0";
     }
     if($this->z01_cadast == null ){
       $this->z01_cadast = "null";
     }
     if($this->z01_login == null ){
       $this->z01_login = "0";
     }
     if($this->z01_numcon == null ){
       $this->z01_numcon = "0";
     }
     if($this->z01_nacion == null ){
       $this->z01_nacion = "0";
     }
     if($this->z01_estciv == null ){
       $this->z01_estciv = "0";
     }
     if($this->z01_tipcre == null ){
       $this->z01_tipcre = "0";
     }
     if($this->z01_nasc == null ){
       $this->z01_nasc = "null";
     }
     if($this->z01_ultalt == null ){
       $this->z01_ultalt = "null";
     }
     if($this->z01_dtemissao == null ){
       $this->z01_dtemissao = "null";
     }
     if($this->z01_dthabilitacao == null ){
       $this->z01_dthabilitacao = "null";
     }
     if($this->z01_dtvencimento == null ){
       $this->z01_dtvencimento = "null";
     }
     if($this->z01_dtfalecimento == null ){
       $this->z01_dtfalecimento = "null";
     }
     if($this->z01_identdtexp == null ){
       $this->z01_identdtexp = "null";
     }
     if($this->z01_renda == null ){
       $this->z01_renda = "0";
     }
     if($this->z01_trabalha == null ){
       $this->z01_trabalha = "f";
     }
     if($this->z01_genero == null ){
      $this->z01_genero = "f";
    }
     if($z01_numcgm == "" || $z01_numcgm == null ){
       $result = db_query("select nextval('cgm_z01_numcgm_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: cgm_z01_numcgm_seq do campo: z01_numcgm";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->z01_numcgm = pg_fetch_result($result,0,0);
     }else{
       $result = db_query("select last_value from cgm_z01_numcgm_seq");
       if(($result != false) && (pg_fetch_result($result,0,0) < $z01_numcgm)){
         $this->erro_sql = " Campo z01_numcgm maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->z01_numcgm = $z01_numcgm;
       }
     }
     if(($this->z01_numcgm == null) || ($this->z01_numcgm == "") ){
       $this->erro_sql = " Campo z01_numcgm nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into cgm(
                                       z01_numcgm
                                      ,z01_nome
                                      ,z01_ender
                                      ,z01_numero
                                      ,z01_compl
                                      ,z01_bairro
                                      ,z01_munic
                                      ,z01_uf
                                      ,z01_cep
                                      ,z01_cxpostal
                                      ,z01_cadast
                                      ,z01_telef
                                      ,z01_ident
                                      ,z01_login
                                      ,z01_incest
                                      ,z01_telcel
                                      ,z01_email
                                      ,z01_endcon
                                      ,z01_numcon
                                      ,z01_comcon
                                      ,z01_baicon
                                      ,z01_muncon
                                      ,z01_ufcon
                                      ,z01_cepcon
                                      ,z01_cxposcon
                                      ,z01_telcon
                                      ,z01_celcon
                                      ,z01_emailc
                                      ,z01_nacion
                                      ,z01_estciv
                                      ,z01_profis
                                      ,z01_tipcre
                                      ,z01_cgccpf
                                      ,z01_fax
                                      ,z01_nasc
                                      ,z01_pai
                                      ,z01_mae
                                      ,z01_sexo
                                      ,z01_ultalt
                                      ,z01_contato
                                      ,z01_hora
                                      ,z01_nomefanta
                                      ,z01_cnh
                                      ,z01_categoria
                                      ,z01_dtemissao
                                      ,z01_dthabilitacao
                                      ,z01_nomecomple
                                      ,z01_dtvencimento
                                      ,z01_dtfalecimento
                                      ,z01_escolaridade
                                      ,z01_naturalidade
                                      ,z01_identdtexp
                                      ,z01_identorgao
                                      ,z01_localtrabalho
                                      ,z01_renda
                                      ,z01_trabalha
                                      ,z01_pis
                                      ,z01_obs
                                      ,z01_genero
                       )
                values (
                                $this->z01_numcgm
                               ,'$this->z01_nome'
                               ,'$this->z01_ender'
                               ,$this->z01_numero
                               ,'$this->z01_compl'
                               ,'$this->z01_bairro'
                               ,'$this->z01_munic'
                               ,'$this->z01_uf'
                               ,'$this->z01_cep'
                               ,'$this->z01_cxpostal'
                               ,".($this->z01_cadast == "null" || $this->z01_cadast == ""?"null":"'".$this->z01_cadast."'")."
                               ,'$this->z01_telef'
                               ,'$this->z01_ident'
                               ,$this->z01_login
                               ,'$this->z01_incest'
                               ,'$this->z01_telcel'
                               ,'$this->z01_email'
                               ,'$this->z01_endcon'
                               ,$this->z01_numcon
                               ,'$this->z01_comcon'
                               ,'$this->z01_baicon'
                               ,'$this->z01_muncon'
                               ,'$this->z01_ufcon'
                               ,'$this->z01_cepcon'
                               ,'$this->z01_cxposcon'
                               ,'$this->z01_telcon'
                               ,'$this->z01_celcon'
                               ,'$this->z01_emailc'
                               ,$this->z01_nacion
                               ,$this->z01_estciv
                               ,'$this->z01_profis'
                               ,$this->z01_tipcre
                               ,'$this->z01_cgccpf'
                               ,'$this->z01_fax'
                               ," . ($this->z01_nasc == "null" || $this->z01_nasc == "" ? "null" : "'" . $this->z01_nasc . "'") . "
                               ,'$this->z01_pai'
                               ,'$this->z01_mae'
                               ,'$this->z01_sexo'
                               ," . ($this->z01_ultalt == "null" || $this->z01_ultalt == "" ? "null" : "'" . $this->z01_ultalt . "'") . "
                               ,'$this->z01_contato'
                               ,'$this->z01_hora'
                               ,'$this->z01_nomefanta'
                               ,'$this->z01_cnh'
                               ,'$this->z01_categoria'
                               ," . ($this->z01_dtemissao == "null" || $this->z01_dtemissao == "" ? "null" : "'" . $this->z01_dtemissao . "'") . "
                               ," . ($this->z01_dthabilitacao == "null" || $this->z01_dthabilitacao == "" ? "null" : "'" . $this->z01_dthabilitacao . "'") . "
                               ,'$this->z01_nomecomple'
                               ," . ($this->z01_dtvencimento == "null" || $this->z01_dtvencimento == "" ? "null" : "'" . $this->z01_dtvencimento . "'") . "
                               ," . ($this->z01_dtfalecimento == "null" || $this->z01_dtfalecimento == "" ? "null" : "'" . $this->z01_dtfalecimento . "'") . "
                               ,'$this->z01_escolaridade'
                               ,'$this->z01_naturalidade'
                               ," . ($this->z01_identdtexp == "null" || $this->z01_identdtexp == "" ? "null" : "'" . $this->z01_identdtexp . "'") . "
                               ,'$this->z01_identorgao'
                               ,'$this->z01_localtrabalho'
                               ,$this->z01_renda
                               ,'$this->z01_trabalha'
                               ,'$this->z01_pis'
                               ,'$this->z01_obs'
                               ,'$this->z01_genero'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cadastro Geral de Contribuinte ($this->z01_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cadastro Geral de Contribuinte já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cadastro Geral de Contribuinte ($this->z01_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->z01_numcgm;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);

     return true;
   }
   // funcao para alteracao
   public function alterar ($z01_numcgm=null) {
      $this->atualizacampos();
     $sql = " update cgm set ";
     $virgula = "";
     if(trim($this->z01_numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_numcgm"])){
       $sql  .= $virgula." z01_numcgm = $this->z01_numcgm ";
       $virgula = ",";
       if(trim($this->z01_numcgm) == null ){
         $this->erro_sql = " Campo Numcgm nao Informado.";
         $this->erro_campo = "z01_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->z01_nome)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_nome"])){
       $sql  .= $virgula." z01_nome = '$this->z01_nome' ";
       $virgula = ",";
       if(trim($this->z01_nome) == null ){
         $this->erro_sql = " Campo Nome/Razão Social nao Informado.";
         $this->erro_campo = "z01_nome";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->z01_ender)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_ender"])){
       $sql  .= $virgula." z01_ender = '$this->z01_ender' ";
       $virgula = ",";
       if(trim($this->z01_ender) == null ){
         $this->erro_sql = " Campo Endereço nao Informado.";
         $this->erro_campo = "z01_ender";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->z01_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_numero"])){
        if(trim($this->z01_numero)=="" && isset($GLOBALS["HTTP_POST_VARS"]["z01_numero"])){
           $this->z01_numero = "0" ;
        }
       $sql  .= $virgula." z01_numero = $this->z01_numero ";
       $virgula = ",";
     }
     if($this->z01_compl !== null || isset($GLOBALS["HTTP_POST_VARS"]["z01_compl"])){
       $sql  .= $virgula." z01_compl = '$this->z01_compl' ";
       $virgula = ",";
     }
     if(trim($this->z01_bairro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_bairro"])){
       $sql  .= $virgula." z01_bairro = '$this->z01_bairro' ";
       $virgula = ",";
     }
     if(trim($this->z01_munic)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_munic"])){
       $sql  .= $virgula." z01_munic = '$this->z01_munic' ";
       $virgula = ",";
     }
     if(trim($this->z01_uf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_uf"])){
       $sql  .= $virgula." z01_uf = '$this->z01_uf' ";
       $virgula = ",";
     }
     if(trim($this->z01_cep)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_cep"])){
       $sql  .= $virgula." z01_cep = '$this->z01_cep' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_cxpostal) || isset($GLOBALS["HTTP_POST_VARS"]["z01_cxpostal"])){
       $sql  .= $virgula." z01_cxpostal = '$this->z01_cxpostal' ";
       $virgula = ",";
     }

     if(trim($this->z01_cadast)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_cadast_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_cadast_dia"] !="") ){
       $sql  .= $virgula." z01_cadast = '$this->z01_cadast' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cadast_dia"])){
         $sql  .= $virgula." z01_cadast = null ";
         $virgula = ",";
       }
     }
     if(!is_null($this->z01_telef) || isset($GLOBALS["HTTP_POST_VARS"]["z01_telef"])){
       $sql  .= $virgula." z01_telef = '$this->z01_telef' ";
       $virgula = ",";
     }

     if (trim($this->z01_ident) !== null) {
       $sql  .= $virgula." z01_ident = '{$this->z01_ident}' ";
       $virgula = ",";
     }

     if(trim($this->z01_login)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_login"])){
        if(trim($this->z01_login)=="" && isset($GLOBALS["HTTP_POST_VARS"]["z01_login"])){
           $this->z01_login = "0" ;
        }
       $sql  .= $virgula." z01_login = $this->z01_login ";
       $virgula = ",";
     }

     if (!is_null($this->z01_incest)) {
       $sql  .= $virgula." z01_incest = '{$this->z01_incest}' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_telcel) || isset($GLOBALS["HTTP_POST_VARS"]["z01_telcel"])){
       $sql  .= $virgula." z01_telcel = '$this->z01_telcel' ";
       $virgula = ",";
     }

     if(!is_null($this->z01_email)|| isset($GLOBALS["HTTP_POST_VARS"]["z01_email"])){
       $sql  .= $virgula." z01_email = '$this->z01_email' ";
       $virgula = ",";
     }

     if(trim($this->z01_endcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_endcon"])){
       $sql  .= $virgula." z01_endcon = '$this->z01_endcon' ";
       $virgula = ",";
     }
     if(trim($this->z01_numcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_numcon"])){
        if(trim($this->z01_numcon)=="" && isset($GLOBALS["HTTP_POST_VARS"]["z01_numcon"])){
           $this->z01_numcon = "0" ;
        }
       $sql  .= $virgula." z01_numcon = $this->z01_numcon ";
       $virgula = ",";
     }
     if(trim($this->z01_comcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_comcon"])){
       $sql  .= $virgula." z01_comcon = '$this->z01_comcon' ";
       $virgula = ",";
     }
     if(trim($this->z01_baicon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_baicon"])){
       $sql  .= $virgula." z01_baicon = '$this->z01_baicon' ";
       $virgula = ",";
     }
     if(trim($this->z01_muncon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_muncon"])){
       $sql  .= $virgula." z01_muncon = '$this->z01_muncon' ";
       $virgula = ",";
     }
     if(trim($this->z01_ufcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_ufcon"])){
       $sql  .= $virgula." z01_ufcon = '$this->z01_ufcon' ";
       $virgula = ",";
     }
     if(trim($this->z01_cepcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_cepcon"])){
       $sql  .= $virgula." z01_cepcon = '$this->z01_cepcon' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_cxposcon) || isset($GLOBALS["HTTP_POST_VARS"]["z01_cxposcon"])){
       $sql  .= $virgula." z01_cxposcon = '$this->z01_cxposcon' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_telcon) || isset($GLOBALS["HTTP_POST_VARS"]["z01_telcon"])){
       $sql  .= $virgula." z01_telcon = '$this->z01_telcon' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_celcon) || isset($GLOBALS["HTTP_POST_VARS"]["z01_celcon"])){
       $sql  .= $virgula." z01_celcon = '$this->z01_celcon' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_emailc) || isset($GLOBALS["HTTP_POST_VARS"]["z01_emailc"])){
       $sql  .= $virgula." z01_emailc = '$this->z01_emailc' ";
       $virgula = ",";
     }
     if(trim($this->z01_nacion)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_nacion"])){
        if(trim($this->z01_nacion)=="" && isset($GLOBALS["HTTP_POST_VARS"]["z01_nacion"])){
           $this->z01_nacion = "0" ;
        }
       $sql  .= $virgula." z01_nacion = $this->z01_nacion ";
       $virgula = ",";
     }
     if(trim($this->z01_estciv)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_estciv"])){
        if(trim($this->z01_estciv)=="" && isset($GLOBALS["HTTP_POST_VARS"]["z01_estciv"])){
           $this->z01_estciv = "0" ;
        }
       $sql  .= $virgula." z01_estciv = $this->z01_estciv ";
       $virgula = ",";
     }

     if (trim($this->z01_profis) !== null) {
       $sql  .= $virgula." z01_profis = '{$this->z01_profis}' ";
       $virgula = ",";
     }

     if(trim($this->z01_tipcre)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_tipcre"])){
        if(trim($this->z01_tipcre)=="" && isset($GLOBALS["HTTP_POST_VARS"]["z01_tipcre"])){
           $this->z01_tipcre = "0" ;
        }
       $sql  .= $virgula." z01_tipcre = $this->z01_tipcre ";
       $virgula = ",";
     }
     if(trim($this->z01_cgccpf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_cgccpf"])){
       $sql  .= $virgula." z01_cgccpf = '$this->z01_cgccpf' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_fax) || isset($GLOBALS["HTTP_POST_VARS"]["z01_fax"])){
       $sql  .= $virgula." z01_fax = '$this->z01_fax' ";
       $virgula = ",";
     }

     if (!is_null($this->z01_nasc) || isset($GLOBALS["HTTP_POST_VARS"]["z01_nasc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_nasc_dia"] !="") ) {
         $sql  .= $virgula." z01_nasc = '$this->z01_nasc' ";
         $virgula = ",";
     } else {
         $sql  .= $virgula." z01_nasc = null" ;
         $virgula = ",";
     }

     if (trim($this->z01_pai) !== null) {
         $sql  .= $virgula." z01_pai = '$this->z01_pai' ";
         $virgula = ",";
     }

     if (trim($this->z01_mae) !== null) {
         $sql  .= $virgula." z01_mae = '$this->z01_mae' ";
         $virgula = ",";
     }

     if(trim($this->z01_sexo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_sexo"])){
       $sql  .= $virgula." z01_sexo = '$this->z01_sexo' ";
       $virgula = ",";
     }
     if(trim($this->z01_ultalt)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_ultalt_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_ultalt_dia"] !="") ){
       $sql  .= $virgula." z01_ultalt = '$this->z01_ultalt' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_ultalt_dia"])){
         $sql  .= $virgula." z01_ultalt = null ";
         $virgula = ",";
       }
     }
     if(!is_null($this->z01_contato) || isset($GLOBALS["HTTP_POST_VARS"]["z01_contato"])){
       $sql  .= $virgula." z01_contato = '$this->z01_contato' ";
       $virgula = ",";
     }
     if(trim($this->z01_hora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_hora"])){
       $sql  .= $virgula." z01_hora = '$this->z01_hora' ";
       $virgula = ",";
     }

     if (trim($this->z01_nomefanta) !== "" || isset($GLOBALS["HTTP_POST_VARS"]["z01_nomefanta"])) {
       $sql  .= $virgula . " z01_nomefanta = '$this->z01_nomefanta' ";
       $virgula = ",";
     } else {
         $sql  .= $virgula . " z01_nomefanta = '$this->z01_nomefanta' ";
         $virgula = ",";
     }

     if(trim($this->z01_cnh)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_cnh"])){
       $sql  .= $virgula." z01_cnh = '$this->z01_cnh' ";
       $virgula = ",";
     }
     if(trim($this->z01_categoria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_categoria"])){
       $sql  .= $virgula." z01_categoria = '$this->z01_categoria' ";
       $virgula = ",";
     }
     if(trim($this->z01_dtemissao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_dtemissao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_dtemissao_dia"] !="") ){
       $sql  .= $virgula." z01_dtemissao = '$this->z01_dtemissao' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtemissao_dia"])){
         $sql  .= $virgula." z01_dtemissao = null ";
         $virgula = ",";
       }
     }
     if(trim($this->z01_dthabilitacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao_dia"] !="") ){
       $sql  .= $virgula." z01_dthabilitacao = '$this->z01_dthabilitacao' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao_dia"])){
         $sql  .= $virgula." z01_dthabilitacao = null ";
         $virgula = ",";
       }
     }
     if(!is_null($this->z01_nomecomple) || isset($GLOBALS["HTTP_POST_VARS"]["z01_nomecomple"])){
       $sql  .= $virgula." z01_nomecomple = '$this->z01_nomecomple' ";
       $virgula = ",";
     }
     if(trim($this->z01_dtvencimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento_dia"] !="") ){
       $sql  .= $virgula." z01_dtvencimento = '$this->z01_dtvencimento' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento_dia"])){
         $sql  .= $virgula." z01_dtvencimento = null ";
         $virgula = ",";
       }
     }
     if(trim($this->z01_dtfalecimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_dtfalecimento_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_dtfalecimento_dia"] !="") ){
       $sql  .= $virgula." z01_dtfalecimento = '$this->z01_dtfalecimento' ";
       $virgula = ",";
     }     else{
         $sql  .= $virgula." z01_dtfalecimento = null ";
         $virgula = ",";
     }

     if(trim($this->z01_escolaridade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_escolaridade"])){
       $sql  .= $virgula." z01_escolaridade = '$this->z01_escolaridade' ";
       $virgula = ",";
     }

     if (trim($this->z01_naturalidade) !== null) {
         $sql  .= $virgula." z01_naturalidade = '{$this->z01_naturalidade}' ";
         $virgula = ",";
     }

     if(trim($this->z01_identdtexp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_identdtexp_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["z01_identdtexp_dia"] !="") ){
         $sql  .= $virgula." z01_identdtexp = '$this->z01_identdtexp' ";
         $virgula = ",";
     } else {
         $sql  .= $virgula." z01_identdtexp = null ";
         $virgula = ",";
     }

     if (trim($this->z01_identorgao) !== null) {
       $sql  .= $virgula." z01_identorgao = '{$this->z01_identorgao}' ";
       $virgula = ",";
     }

     if (trim($this->z01_localtrabalho) !== null) {
       $sql  .= $virgula." z01_localtrabalho = '{$this->z01_localtrabalho}' ";
       $virgula = ",";
     }


     if(!is_null($this->z01_renda) || isset($GLOBALS["HTTP_POST_VARS"]["z01_renda"])){
        if(!is_null($this->z01_renda) && isset($GLOBALS["HTTP_POST_VARS"]["z01_renda"])){
           $this->z01_renda = "0" ;
        }
       $sql  .= $virgula." z01_renda = $this->z01_renda ";
       $virgula = ",";
     } else {
         $sql  .= $virgula." z01_renda = null ";
         $virgula = ",";
     }
     if(trim($this->z01_trabalha)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_trabalha"])){
       $sql  .= $virgula." z01_trabalha = '$this->z01_trabalha' ";
       $virgula = ",";
     }
     if(trim($this->z01_pis)!="" || isset($GLOBALS["HTTP_POST_VARS"]["z01_pis"])){
       $sql  .= $virgula." z01_pis = '$this->z01_pis' ";
       $virgula = ",";
     } else {
         $sql  .= $virgula." z01_pis = '$this->z01_pis' ";
         $virgula = ",";
     }

     if (trim($this->z01_obs) !== null) {
       $sql  .= $virgula." z01_obs = '{$this->z01_obs}' ";
       $virgula = ",";
     }

     if (trim($this->z01_genero) !== null) {
      $sql  .= $virgula." z01_genero = '{$this->z01_genero}' ";
      $virgula = ",";
    }

     $sql .= " where ";
     if($z01_numcgm!=null){
       $sql .= " z01_numcgm = $this->z01_numcgm";
     }

     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro Geral de Contribuinte nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->z01_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro Geral de Contribuinte nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->z01_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->z01_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($z01_numcgm=null,$dbwhere=null) {
     $sql = " delete from cgm
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($z01_numcgm != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " z01_numcgm = $z01_numcgm ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro Geral de Contribuinte nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$z01_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro Geral de Contribuinte nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$z01_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$z01_numcgm;
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
     if($result==false){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_num_rows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:cgm";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql .= " left join cgmfisico on z04_numcgm=cgm.z01_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   // funcao do sql
   public function sql_query_file ( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql2 = "";
     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
  public function sql_query_cpf ( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql .= " inner join db_cgmcpf on db_cgmcpf.z01_numcgm=cgm.z01_numcgm";
     $sql .= " left join cgmfisico on z04_numcgm=cgm.z01_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
  public function sql_query_cgc ( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql .= " inner join db_cgmcgc on db_cgmcgc.z01_numcgm=cgm.z01_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
  public function sqlnome ($nome="",$campos="*",$filtro=0){
    $nome = strtoupper($nome);

  $sql = "
      select $campos
      from cgm
  ";
    if ($filtro==1){
      $sql .= " inner join db_cgmcpf on db_cgmcpf.z01_numcgm=cgm.z01_numcgm ";

    }elseif($filtro==2){
       $sql .= " inner join db_cgmcgc on db_cgmcgc.z01_numcgm=cgm.z01_numcgm ";
    }
    $sql .= " left join cgmfisico on z04_numcgm=cgm.z01_numcgm";
    if ($nome !=""){
      $sql .= "
      where to_ascii(upper(z01_nome)) like to_ascii('$nome%')
                   ";
  }
   $sql .= " order by to_ascii(z01_nome)";
  return $sql;
  }
  public function sqlCodnome ($codnome=0,$campos="*"){
    $sql = "  select $campos   from cgm";
    if ($codnome != 0){
      $sql .= "  where z01_numcgm = $codnome	  ";
   }
     $sql .= " order by z01_nome";
	return $sql;
}
  public function sql_query_ordemcompra ( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql .= "      left outer join db_cgmbairro on cgm.z01_numcgm = db_cgmbairro.z01_numcgm";
     $sql .= "      left outer join db_cgmcgc on cgm.z01_numcgm = db_cgmcgc.z01_numcgm";
     $sql .= "      left outer join db_cgmcpf on cgm.z01_numcgm = db_cgmcpf.z01_numcgm";
     $sql .= "      left outer join db_cgmruas on cgm.z01_numcgm = db_cgmruas.z01_numcgm";
     $sql .= "      inner join matordem on cgm.z01_numcgm = matordem. m51_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
  public function sql_query_ender( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql .= "      left outer join db_cgmbairro on cgm.z01_numcgm = db_cgmbairro.z01_numcgm";
     $sql .= "      left outer join bairro on bairro.j13_codi = db_cgmbairro.j13_codi";
     $sql .= "      left outer join db_cgmruas on cgm.z01_numcgm = db_cgmruas.z01_numcgm";
     $sql .= "      left outer join ruas on ruas.j14_codigo = db_cgmruas.j14_codigo";
     $sql2 = "";
     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
    }
     return $sql;
}
  public function sql_query_veic ( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere="",$dbinner=""){
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql .= "      left join  rhpessoal on  z01_numcgm = rh01_numcgm ";
     $sql .= "      left join veicmotoristas on z01_numcgm  = ve05_numcgm";
     $sql2 = "";

     if ($dbinner != ""){
          $sql .= $dbinner;
     }

     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
  public function sql_query_cgmmunicipio ( $z01_numcgm=null, $campos="*", $ordem=null, $dbwhere=""){
  	 $dbInstit = db_getsession("DB_instit");
     $sql = "select ";
     if($campos != "*" ){
         $campos_sql = explode("#", $campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cgm ";
     $sql .= " inner join db_config on upper(trim(fc_remove_acentos(z01_munic))) = upper(trim(fc_remove_acentos(munic))) and codigo = ".$dbInstit;
     $sql2 = "";
     if($dbwhere==""){
       if($z01_numcgm!=null ){
         $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
         $campos_sql = explode("#", $ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }


  public function sql_query_endereco($z01_numcgm=null, $campos="*", $ordem=null, $dbwhere="") {

    $sSql = "select ";
    if ($campos != "*" ) {

        $campos_sql = explode("#", $campos);
      $virgula    = "";

      for ($i=0; $i < sizeof($campos_sql); $i++) {

        $sSql    .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    } else {
      $sSql .= $campos;
    }

    $sSql .= " from cgm                                                                                     ";
    $sSql .= "      left join cgmendereco   on z01_numcgm   = z07_numcgm                                    ";
    $sSql .= "      left join endereco      on z07_endereco = db76_sequencial                               ";
    $sSql .= "      left join cadenderlocal on db76_cadenderlocal = db75_sequencial                         ";
    $sSql .= "      left join cadenderbairrocadenderrua on db87_sequencial = db75_cadenderbairrocadenderrua ";
    $sSql .= "      left join cadenderrua   on db74_sequencial = db87_cadenderrua                           ";
    $sSql .= "      left join cadenderbairro on db73_sequencial = db87_cadenderbairro                       ";

    $sSql2 = "";

    if ($dbwhere == "") {

      if ($z01_numcgm != null ) {
        $sSql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
      }
    } else if($dbwhere != "") {
      $sSql2 = " where $dbwhere";
    }
    $sSql .= $sSql2;
    if ($ordem != null ) {
      $sSql       .= " order by ";
        $campos_sql = explode("#", $ordem);
      $virgula     = "";

      for ($i=0; $i < sizeof($campos_sql); $i++) {

        $sSql    .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    }

    return $sSql;
  }

  public function alterarCgmBase ($z01_numcgm=null) {

     $sql = " update cgm set ";
     $virgula = "";

     if( trim($this->z01_numcgm) != "" ){
       $sql  .= $virgula." z01_numcgm = $this->z01_numcgm ";
       $virgula = ",";
       if(trim($this->z01_numcgm) == null ){
         $this->erro_sql = " Campo Numcgm nao Informado.";
         $this->erro_campo = "z01_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->z01_nome) != "" ){
       $sql  .= $virgula." z01_nome = '$this->z01_nome' ";
       $virgula = ",";
       if(trim($this->z01_nome) == null ){
         $this->erro_sql = " Campo Nome/Razão Social nao Informado.";
         $this->erro_campo = "z01_nome";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->z01_ender)!="" ){
       $sql  .= $virgula." z01_ender = '$this->z01_ender' ";
       $virgula = ",";
       if(trim($this->z01_ender) == null ){
         $this->erro_sql = " Campo Endereço nao Informado.";
         $this->erro_campo = "z01_ender";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->z01_numero)!="" ){
        if(trim($this->z01_numero)=="" ){
           $this->z01_numero = "0" ;
        }
       $sql  .= $virgula." z01_numero = $this->z01_numero ";
       $virgula = ",";
     }
     if($this->z01_compl !== null){
       $sql  .= $virgula." z01_compl = '$this->z01_compl' ";
       $virgula = ",";
     }
     if(trim($this->z01_bairro)!="" ){
       $sql  .= $virgula." z01_bairro = '$this->z01_bairro' ";
       $virgula = ",";
     }
     if(trim($this->z01_munic)!=""){
       $sql  .= $virgula." z01_munic = '$this->z01_munic' ";
       $virgula = ",";
     }
     if(trim($this->z01_uf)!=""){
       $sql  .= $virgula." z01_uf = '$this->z01_uf' ";
       $virgula = ",";
     }
     if(trim($this->z01_cep)!="" ){
       $sql  .= $virgula." z01_cep = '$this->z01_cep' ";
       $virgula = ",";
     }

     if(trim($this->z01_cadast)!="" ){
       $sql  .= $virgula." z01_cadast = '$this->z01_cadast' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cadast_dia"])){
         $sql  .= $virgula." z01_cadast = null ";
         $virgula = ",";
       }
     }
     $sql  .= $virgula." z01_telef = '$this->z01_telef' ";
     $virgula = ",";
     if(trim($this->z01_ident)!="" ){
       $sql  .= $virgula." z01_ident = '$this->z01_ident' ";
       $virgula = ",";
     }
     if(trim($this->z01_login)!=""){
        if(trim($this->z01_login)==""){
           $this->z01_login = "0" ;
        }
       $sql  .= $virgula." z01_login = $this->z01_login ";
       $virgula = ",";
     }
     if(trim($this->z01_incest)!="" ){
       $sql  .= $virgula." z01_incest = '$this->z01_incest' ";
       $virgula = ",";
     }

       $sql  .= $virgula." z01_cxpostal = '$this->z01_cxpostal' ";
       $virgula = ",";

     $sql  .= $virgula." z01_telcel = '$this->z01_telcel' ";
     $virgula = ",";


     $sql    .= $virgula." z01_endcon = '$this->z01_endcon' ";
     $virgula = ",";

     if(trim($this->z01_numcon)==""){
       $this->z01_numcon = "0" ;
     }

     $sql  .= $virgula." z01_numcon = $this->z01_numcon ";
     $virgula = ",";

     $sql  .= $virgula." z01_comcon = '$this->z01_comcon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_baicon = '$this->z01_baicon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_muncon = '$this->z01_muncon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_ufcon = '$this->z01_ufcon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_cepcon = '$this->z01_cepcon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_cxposcon = '$this->z01_cxposcon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_telcon = '$this->z01_telcon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_celcon = '$this->z01_celcon' ";
     $virgula = ",";

     $sql  .= $virgula." z01_emailc = '$this->z01_emailc' ";
     $virgula = ",";

     $sql  .= $virgula." z01_email = '$this->z01_email' ";
     $virgula = ",";

     if(trim($this->z01_nacion)!="" ){
        if(trim($this->z01_nacion)=="" ){
           $this->z01_nacion = "0" ;
        }
       $sql  .= $virgula." z01_nacion = $this->z01_nacion ";
       $virgula = ",";
     }
     if(trim($this->z01_estciv)!="" ){
        if(trim($this->z01_estciv)=="" ){
           $this->z01_estciv = "0" ;
        }
       $sql  .= $virgula." z01_estciv = $this->z01_estciv ";
       $virgula = ",";
     }

     $sql  .= $virgula." z01_profis = '$this->z01_profis' ";
     $virgula = ",";

     if(trim($this->z01_tipcre)!=""){
        if(trim($this->z01_tipcre)==""){
           $this->z01_tipcre = "0" ;
        }
       $sql  .= $virgula." z01_tipcre = $this->z01_tipcre ";
       $virgula = ",";
     }
     if(trim($this->z01_cgccpf)!=""){
       $sql  .= $virgula." z01_cgccpf = '$this->z01_cgccpf' ";
       $virgula = ",";
     }
     if(trim($this->z01_fax)!=""){
       $sql  .= $virgula." z01_fax = '$this->z01_fax' ";
       $virgula = ",";
     }

     if(trim($this->z01_nasc)!=""){
       $sql  .= $virgula." z01_nasc = '$this->z01_nasc' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_nasc_dia"])){
         $sql  .= $virgula." z01_nasc = null ";
         $virgula = ",";
       }
     }
     if(trim($this->z01_pai)!="" ){
       $sql  .= $virgula." z01_pai = '$this->z01_pai' ";
       $virgula = ",";
     }
     if(trim($this->z01_mae)!="" ){
       $sql  .= $virgula." z01_mae = '$this->z01_mae' ";
       $virgula = ",";
     }
     if(trim($this->z01_sexo)!=""){
       $sql  .= $virgula." z01_sexo = '$this->z01_sexo' ";
       $virgula = ",";
     }
     if(trim($this->z01_ultalt)!=""){
       $sql  .= $virgula." z01_ultalt = '$this->z01_ultalt' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_ultalt_dia"])){
         $sql  .= $virgula." z01_ultalt = null ";
         $virgula = ",";
       }
     }

     if(!is_null($this->z01_contato)){
       $sql  .= $virgula." z01_contato = '$this->z01_contato' ";
       $virgula = ",";
     }
     if(trim($this->z01_hora)!=""){
       $sql  .= $virgula." z01_hora = '$this->z01_hora' ";
       $virgula = ",";
     }
     if(!is_null($this->z01_nomefanta)){
       $sql  .= $virgula." z01_nomefanta = '$this->z01_nomefanta' ";
       $virgula = ",";
     }
     if(trim($this->z01_cnh)!=""){
       $sql  .= $virgula." z01_cnh = '$this->z01_cnh' ";
       $virgula = ",";
     }
     if(trim($this->z01_categoria)!=""){
       $sql  .= $virgula." z01_categoria = '$this->z01_categoria' ";
       $virgula = ",";
     }
     if(trim($this->z01_dtemissao)!=""){
       $sql  .= $virgula." z01_dtemissao = '$this->z01_dtemissao' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtemissao_dia"])){
         $sql  .= $virgula." z01_dtemissao = null ";
         $virgula = ",";
       }
     }
     if(trim($this->z01_dthabilitacao)!=""){
       $sql  .= $virgula." z01_dthabilitacao = '$this->z01_dthabilitacao' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao_dia"])){
         $sql  .= $virgula." z01_dthabilitacao = null ";
         $virgula = ",";
       }
     }

     if(trim($this->z01_nomecomple)!=""){
       $sql  .= $virgula." z01_nomecomple = '$this->z01_nomecomple' ";
       $virgula = ",";
     }
     if(trim($this->z01_dtvencimento)!=""){
       $sql  .= $virgula." z01_dtvencimento = '$this->z01_dtvencimento' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento_dia"])){
         $sql  .= $virgula." z01_dtvencimento = null ";
         $virgula = ",";
       }
     }
     if(trim($this->z01_dtfalecimento)!=""){
       $sql  .= $virgula." z01_dtfalecimento = '$this->z01_dtfalecimento' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtfalecimento_dia"])){
         $sql  .= $virgula." z01_dtfalecimento = null ";
         $virgula = ",";
       }
     }
     if(trim($this->z01_obs)!="" ){
       $sql  .= $virgula." z01_obs = '$this->z01_obs' ";
       $virgula = ",";
     }
     if(trim($this->z01_genero)!="" ){
      $sql  .= $virgula." z01_genero = '$this->z01_genero' ";
      $virgula = ",";
    }
     $sql .= " where ";
     if($z01_numcgm!=null){
       $sql .= " z01_numcgm = $this->z01_numcgm";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->z01_numcgm));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,216,'$this->z01_numcgm','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_numcgm"]) || $this->z01_numcgm != "")
           $resac = db_query("insert into db_acount values($acount,42,216,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_numcgm'))."','$this->z01_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_nome"]) || $this->z01_nome != "")
           $resac = db_query("insert into db_acount values($acount,42,217,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_nome'))."','$this->z01_nome',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_ender"]) || $this->z01_ender != "")
           $resac = db_query("insert into db_acount values($acount,42,218,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_ender'))."','$this->z01_ender',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_numero"]) || $this->z01_numero != "")
           $resac = db_query("insert into db_acount values($acount,42,732,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_numero'))."','$this->z01_numero',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_compl"]) || $this->z01_compl != "")
           $resac = db_query("insert into db_acount values($acount,42,733,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_compl'))."','$this->z01_compl',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_bairro"]) || $this->z01_bairro != "")
           $resac = db_query("insert into db_acount values($acount,42,227,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_bairro'))."','$this->z01_bairro',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_munic"]) || $this->z01_munic != "")
           $resac = db_query("insert into db_acount values($acount,42,219,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_munic'))."','$this->z01_munic',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_uf"]) || $this->z01_uf != "")
           $resac = db_query("insert into db_acount values($acount,42,220,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_uf'))."','$this->z01_uf',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cep"]) || $this->z01_cep != "")
           $resac = db_query("insert into db_acount values($acount,42,221,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_cep'))."','$this->z01_cep',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cxpostal"]) || $this->z01_cxpostal != "")
           $resac = db_query("insert into db_acount values($acount,42,738,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_cxpostal'))."','$this->z01_cxpostal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cadast"]) || $this->z01_cadast != "")
           $resac = db_query("insert into db_acount values($acount,42,222,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_cadast'))."','$this->z01_cadast',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_telef"]) || $this->z01_telef != "")
           $resac = db_query("insert into db_acount values($acount,42,223,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_telef'))."','$this->z01_telef',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_ident"]) || $this->z01_ident != "")
           $resac = db_query("insert into db_acount values($acount,42,224,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_ident'))."','$this->z01_ident',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_login"]) || $this->z01_login != "")
           $resac = db_query("insert into db_acount values($acount,42,226,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_login'))."','$this->z01_login',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_incest"]) || $this->z01_incest != "")
           $resac = db_query("insert into db_acount values($acount,42,228,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_incest'))."','$this->z01_incest',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_telcel"]) || $this->z01_telcel != "")
           $resac = db_query("insert into db_acount values($acount,42,229,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_telcel'))."','$this->z01_telcel',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_email"]) || $this->z01_email != "")
           $resac = db_query("insert into db_acount values($acount,42,230,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_email'))."','$this->z01_email',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_endcon"]) || $this->z01_endcon != "")
           $resac = db_query("insert into db_acount values($acount,42,231,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_endcon'))."','$this->z01_endcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_numcon"]) || $this->z01_numcon != "")
           $resac = db_query("insert into db_acount values($acount,42,734,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_numcon'))."','$this->z01_numcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_comcon"]) || $this->z01_comcon != "")
           $resac = db_query("insert into db_acount values($acount,42,735,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_comcon'))."','$this->z01_comcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_baicon"]) || $this->z01_baicon != "")
           $resac = db_query("insert into db_acount values($acount,42,233,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_baicon'))."','$this->z01_baicon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_muncon"]) || $this->z01_muncon != "")
           $resac = db_query("insert into db_acount values($acount,42,232,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_muncon'))."','$this->z01_muncon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_ufcon"]) || $this->z01_ufcon != "")
           $resac = db_query("insert into db_acount values($acount,42,234,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_ufcon'))."','$this->z01_ufcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cepcon"]) || $this->z01_cepcon != "")
           $resac = db_query("insert into db_acount values($acount,42,235,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_cepcon'))."','$this->z01_cepcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cxposcon"]) || $this->z01_cxposcon != "")
           $resac = db_query("insert into db_acount values($acount,42,739,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_cxposcon'))."','$this->z01_cxposcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_telcon"]) || $this->z01_telcon != "")
           $resac = db_query("insert into db_acount values($acount,42,236,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_telcon'))."','$this->z01_telcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_celcon"]) || $this->z01_celcon != "")
           $resac = db_query("insert into db_acount values($acount,42,237,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_celcon'))."','$this->z01_celcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_emailc"]) || $this->z01_emailc != "")
           $resac = db_query("insert into db_acount values($acount,42,238,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_emailc'))."','$this->z01_emailc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_nacion"]) || $this->z01_nacion != "")
           $resac = db_query("insert into db_acount values($acount,42,239,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_nacion'))."','$this->z01_nacion',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_estciv"]) || $this->z01_estciv != "")
           $resac = db_query("insert into db_acount values($acount,42,240,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_estciv'))."','$this->z01_estciv',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_profis"]) || $this->z01_profis != "")
           $resac = db_query("insert into db_acount values($acount,42,241,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_profis'))."','$this->z01_profis',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_tipcre"]) || $this->z01_tipcre != "")
           $resac = db_query("insert into db_acount values($acount,42,242,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_tipcre'))."','$this->z01_tipcre',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cgccpf"]) || $this->z01_cgccpf != "")
           $resac = db_query("insert into db_acount values($acount,42,1126,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_cgccpf'))."','$this->z01_cgccpf',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_fax"]) || $this->z01_fax != "")
           $resac = db_query("insert into db_acount values($acount,42,6736,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_fax'))."','$this->z01_fax',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_nasc"]) || $this->z01_nasc != "")
           $resac = db_query("insert into db_acount values($acount,42,6737,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_nasc'))."','$this->z01_nasc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_pai"]) || $this->z01_pai != "")
           $resac = db_query("insert into db_acount values($acount,42,6738,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_pai'))."','$this->z01_pai',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_mae"]) || $this->z01_mae != "")
           $resac = db_query("insert into db_acount values($acount,42,6739,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_mae'))."','$this->z01_mae',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_sexo"]) || $this->z01_sexo != "")
           $resac = db_query("insert into db_acount values($acount,42,6740,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_sexo'))."','$this->z01_sexo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_ultalt"]) || $this->z01_ultalt != "")
           $resac = db_query("insert into db_acount values($acount,42,6741,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_ultalt'))."','$this->z01_ultalt',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_contato"]) || $this->z01_contato != "")
           $resac = db_query("insert into db_acount values($acount,42,6742,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_contato'))."','$this->z01_contato',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_hora"]) || $this->z01_hora != "")
           $resac = db_query("insert into db_acount values($acount,42,6743,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_hora'))."','$this->z01_hora',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_nomefanta"]) || $this->z01_nomefanta != "")
           $resac = db_query("insert into db_acount values($acount,42,6749,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_nomefanta'))."','$this->z01_nomefanta',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_cnh"]) || $this->z01_cnh != "")
           $resac = db_query("insert into db_acount values($acount,42,7294,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_cnh'))."','$this->z01_cnh',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_categoria"]) || $this->z01_categoria != "")
           $resac = db_query("insert into db_acount values($acount,42,7295,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_categoria'))."','$this->z01_categoria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtemissao"]) || $this->z01_dtemissao != "")
           $resac = db_query("insert into db_acount values($acount,42,7296,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_dtemissao'))."','$this->z01_dtemissao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dthabilitacao"]) || $this->z01_dthabilitacao != "")
           $resac = db_query("insert into db_acount values($acount,42,7297,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_dthabilitacao'))."','$this->z01_dthabilitacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_nomecomple"]) || $this->z01_nomecomple != "")
           $resac = db_query("insert into db_acount values($acount,42,7309,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_nomecomple'))."','$this->z01_nomecomple',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtvencimento"]) || $this->z01_dtvencimento != "")
           $resac = db_query("insert into db_acount values($acount,42,7344,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_dtvencimento'))."','$this->z01_dtvencimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_dtfalecimento"]) || $this->z01_dtfalecimento != "")
           $resac = db_query("insert into db_acount values($acount,42,14490,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_dtfalecimento'))."','$this->z01_dtfalecimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_obs"]) || $this->z01_obs != "")
           $resac = db_query("insert into db_acount values($acount,42,18201,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_obs'))."','$this->z01_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["z01_genero"]) || $this->z01_genero != "")
           $resac = db_query("insert into db_acount values($acount,42,1013801,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'z01_genero'))."','$this->z01_genero',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }

     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro Geral de Contribuinte nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->z01_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro Geral de Contribuinte nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->z01_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->z01_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

  public function sql_query_endereco_licitacon($sCampos, $sOrderBy, $sWhere) {

    if (empty($sCampos)) {
      $sCampos = " * ";
    }

    $sSql  = " select {$sCampos} ";
    $sSql .= " from cgm ";
    $sSql .= "    left join cgmendereco on cgmendereco.z07_sequencial = (                                              ";
    $sSql .= "                                                            select z07_sequencial                        ";
    $sSql .= "                                                            from cgmendereco                             ";
    $sSql .= "                                                            where z07_numcgm = cgm.z01_numcgm            ";
    $sSql .= "                                                              and z07_tipo = 'P' order by z07_sequencial ";
    $sSql .= "                                                            limit 1                                      ";
    $sSql .= "                                                           )                                             ";
    $sSql .= "    left join endereco                  on db76_sequencial = z07_endereco                                ";
    $sSql .= "    left join cadenderlocal             on db75_sequencial = db76_cadenderlocal                          ";
    $sSql .= "    left join cadenderbairrocadenderrua on db87_sequencial = db75_cadenderbairrocadenderrua              ";
    $sSql .= "    left join cadenderbairro            on db87_cadenderbairro = db73_sequencial                         ";
    $sSql .= "    left join cadendermunicipio         on db73_cadendermunicipio = db72_sequencial                      ";
    $sSql .= "    left join cadendermunicipiosistema  on db72_sequencial = db125_cadendermunicipio                     ";
    $sSql .= "                                            and db125_db_sistemaexterno = 4                              ";

    if (!empty($sWhere)) {
      $sSql .= " where {$sWhere} ";
    }

    if (!empty($sOrderBy)) {
      $sSql .= " order by  {$sOrderBy} ";
    }

    return $sSql;
  }

   /**
    * Monta query para consultar os CGMs considerando o tipo de CGM
    * @param  array  $aWhere  Array de condicoes ('campo' => 'valor')
    * @param  string $sCampos Campos a retornar na consulta
    * @param  string $sTipo   juridico CgmJuridico
    *                                       fisico    CgmFisico
    *                                       ''          ambos
    * @return string                SQL query da consulta
    */
   public function sql_query_cgmtipo($aWhere = array(), $sCampos = "*", $sTipo = ''){

    if ($sTipo == 'fisico') {
      $sJoin = "inner join cgmfisico on (cgm.z01_numcgm = cgmfisico.z04_numcgm)";
    } else if ($sTipo == 'juridico') {
      $sJoin = "inner join cgmjuridico on (cgm.z01_numcgm = cgmfisico.z08_numcgm)";
    } else {
      $sJoin  = "left join cgmfisico on (cgm.z01_numcgm = cgmfisico.z04_numcgm)";
      $sJoin .= "left join cgmjuridico on (cgm.z01_numcgm = cgmfisico.z08_numcgm)";
    }
    /* Monta SQL query*/
    $sSql  = "SELECT ";
    $sSql .= $sCampos;
    $sSql .= " FROM cgm ";
    $sSql .= $sJoin;

    /* Monta a condicao where*/
    if (!empty($aWhere)) {

      $sSql .= " WHERE";
      /* Percprre array de condicoes para montar o where */
      foreach ($aWhere as $sCampo => $sValor) {

        /* Se nao foi o primeiro item do array adiciona AND antes da condicao */
        if (array_search($sCampo, array_keys($aWhere)) > 0) {
          $sSql .= " AND ";
        }

        /* Se a conticao for o nome faz tratamendo para acentuacao e case-insensitive */
        if ($sCampo == 'z01_nome') {
          $sNome = strtoupper($sValor);
          $sSql .= " to_ascii(upper(z01_nome)) ilike to_ascii('" . $sNome . "%')";

        } else {
          $sSql .= " {$sCampo} = {$sValor}";
        }
      }
    }
    $sSql .= " order by to_ascii(z01_nome)";

    return $sSql;
  }

  public function sql_matricula ( $z01_numcgm=null,$campos="*",$ordem=null,$dbwhere="",$dbinner=""){
      $sql = "select ";
      if($campos != "*" ){
          $campos_sql = explode("#", $campos);
          $virgula = "";
          for($i=0;$i<sizeof($campos_sql);$i++){
              $sql .= $virgula.$campos_sql[$i];
              $virgula = ",";
          }
      }else{
          $sql .= $campos;
      }
      $sql .= " from cgm ";
      $sql .= " inner join  rhpessoal on  z01_numcgm = rh01_numcgm ";
      $sql2 = "";

      if ($dbinner != ""){
          $sql .= $dbinner;
      }

      if($dbwhere==""){
          if($z01_numcgm!=null ){
              $sql2 .= " where cgm.z01_numcgm = $z01_numcgm ";
          }
      }else if($dbwhere != ""){
          $sql2 = " where $dbwhere";
      }
      $sql .= $sql2;
      if($ordem != null ){
          $sql .= " order by ";
          $campos_sql = explode("#", $ordem);
          $virgula = "";
          for($i=0;$i<sizeof($campos_sql);$i++){
              $sql .= $virgula.$campos_sql[$i];
              $virgula = ",";
          }
      }
      return $sql;
  }

  public function sqlQueryCamposCadastroCgm($id, $campos=array('*'))
  {
    $campos = implode(', ', $campos);

    $sSql = "
      SELECT
        {$campos}
      FROM
        cgm
      LEFT JOIN cgmtipoempresa
        ON cgmtipoempresa.z03_numcgm = cgm.z01_numcgm
      LEFT JOIN cgmjuridico
        ON cgmjuridico.z08_numcgm = cgm.z01_numcgm
      LEFT JOIN cgmendereco
        ON cgmendereco.z07_numcgm = cgm.z01_numcgm
      LEFT JOIN endereco
        ON endereco.db76_sequencial = cgmendereco.z07_endereco
      LEFT JOIN cadenderlocal
        ON cadenderlocal.db75_sequencial = endereco.db76_cadenderlocal
      LEFT JOIN cadenderbairrocadenderrua
        ON cadenderbairrocadenderrua.db87_sequencial = cadenderlocal.db75_cadenderbairrocadenderrua
      LEFT JOIN cadenderrua
        ON cadenderrua.db74_sequencial = cadenderbairrocadenderrua.db87_cadenderrua
      LEFT JOIN cadenderbairro
        ON cadenderbairro.db73_sequencial = cadenderbairrocadenderrua.db87_cadenderbairro
      WHERE
        z01_numcgm = {$id}
    ";

    return $sSql;
  }
}
