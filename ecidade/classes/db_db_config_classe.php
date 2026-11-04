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

class cl_db_config
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
    public $codigo = 0;
    public $nomeinst = null;
    public $ender = null;
    public $munic = null;
    public $uf = null;
    public $telef = null;
    public $ident = 0;
    public $tx_banc = 0;
    public $numbanco = null;
    public $url = null;
    public $logo = null;
    public $figura = null;
    public $dtcont_dia = null;
    public $dtcont_mes = null;
    public $dtcont_ano = null;
    public $dtcont = null;
    public $diario = 0;
    public $pref = null;
    public $vicepref = null;
    public $fax = null;
    public $cgc = null;
    public $cep = null;
    public $tpropri = 'f';
    public $tsocios = 'f';
    public $prefeitura = 'f';
    public $bairro = null;
    public $numcgm = 0;
    public $codtrib = null;
    public $tribinst = 0;
    public $segmento = 0;
    public $formvencfebraban = 0;
    public $numero = 0;
    public $nomedebconta = null;
    public $db21_tipoinstit = 0;
    public $db21_ativo = 0;
    public $db21_regracgmiss = 0;
    public $db21_regracgmiptu = 0;
    public $db21_codcli = 0;
    public $nomeinstabrev = null;
    public $db21_usasisagua = 'f';
    public $db21_codigomunicipoestado = 0;
    public $db21_datalimite_dia = null;
    public $db21_datalimite_mes = null;
    public $db21_datalimite_ano = null;
    public $db21_datalimite = null;
    public $db21_criacao_dia = null;
    public $db21_criacao_mes = null;
    public $db21_criacao_ano = null;
    public $db21_criacao = null;
    public $db21_compl = null;
    public $email = null;
    public $db21_imgmarcadagua = 0;
    public $db21_esfera = 0;
    public $db21_tipopoder = 0;
    public $db21_codtj = 0;
    public $db21_codsiconfi = null;
    public $db21_unidade_gestora_rpps = 'f';
    public $db21_efr_previdencia_compl = 'f';
    public $db21_cnpj_efr = null;
    public $db21_ente_federativo_resp = 'f';
    public $db21_valor_teto_remuneratorio = 0;
    public $db21_esfera_op = 0;
    public $db21_possui_rpps = 'f';
    public $db21_departamento = null;
    public $db21_descr_depart_abrev = null;
    public $db21_permiteinscricaocgf = 't';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 codigo = int4 = Código da Instituição
                 nomeinst = varchar(80) = Nome da Instituição
                 ender = varchar(80) = Endereço da Instituição
                 munic = varchar(40) = Município da Instituição
                 uf = char(2) = Unidade Federativa da Instituição
                 telef = char(11) = Telefone
                 ident = int4 = identidade
                 tx_banc = float8 = taxa bancaria
                 numbanco = varchar(10) = numero do banco
                 url = varchar(200) = url
                 logo = varchar(100) = logo
                 figura = varchar(100) = figura
                 dtcont = date = data da contabilidade
                 diario = int4 = Diário
                 pref = varchar(40) = prefeito
                 vicepref = varchar(40) = vice prefeito
                 fax = char(11) = fax
                 cgc = char(14) = cgc
                 cep = char(8) = cep
                 tpropri = bool = Débitos proprietário
                 tsocios = bool = Débitos Sócios
                 prefeitura = bool = Prefeitura
                 bairro = char(35) = Bairro
                 numcgm = int4 = Número do CGM
                 codtrib = char(4) = Órgão/Unidade da Instituição
                 tribinst = int4 = Instituição SIAPC/PAD
                 segmento = int4 = Segmento Código de Barras Febraban
                 formvencfebraban = int4 = Forma do vencimento Febraban
                 numero = int4 = Número do endereço
                 nomedebconta = char(20) = Nome da instituição no débito em conta
                 db21_tipoinstit = int4 = Tipo de Instituição
                 db21_ativo = int4 = Ativo
                 db21_regracgmiss = int4 = Regra CGM issbase
                 db21_regracgmiptu = int4 = Regra Cgm Iptu
                 db21_codcli = int4 = Código do cliente
                 nomeinstabrev = varchar(20) = Nome da instituição para relatório
                 db21_usasisagua = bool = Usa sistema de água
                 db21_codigomunicipoestado = int4 = Código do município no estado
                 db21_datalimite = date = Data limite que instituição é valida
                 db21_criacao = date = Data de criação da instituição
                 db21_compl = varchar(20) = Complemento do endereço
                 email = varchar(200) = email
                 db21_imgmarcadagua = oid = Marca D'agua Instituição
                 db21_esfera = int4 = Esfera
                 db21_tipopoder = int4 = Tipo de Poder
                 db21_codtj = int4 = Código do município na TJ
                 db21_codsiconfi = varchar(15) = Código SICONFI
                 db21_unidade_gestora_rpps = bool = Unidade Gestora do RPPS
                 db21_efr_previdencia_compl = bool = EFR instituiu previdência complementar
                 db21_cnpj_efr = varchar(20) = CNPJ do Ente Federativo Responsável
                 db21_ente_federativo_resp = bool = Órgão Público é o EFR
                 db21_valor_teto_remuneratorio = float8 = Valor do teto remuneratório específico
                 db21_esfera_op = int4 = Esfera administrativa do órgão público
                 db21_possui_rpps = bool = Regime Próprio de Previdência Social
                 db21_departamento = int4 = Código Departamento Principal
                 db21_descr_depart_abrev = varchar(100) = Descrição Abreviada do Departamento
                 db21_permiteinscricaocgf = bool = Permite inscrição econômica CGF 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("db_config");
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
       $this->codigo = ($this->codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["codigo"]:$this->codigo);
       $this->nomeinst = ($this->nomeinst == ""?@$GLOBALS["HTTP_POST_VARS"]["nomeinst"]:$this->nomeinst);
       $this->ender = ($this->ender == ""?@$GLOBALS["HTTP_POST_VARS"]["ender"]:$this->ender);
       $this->munic = ($this->munic == ""?@$GLOBALS["HTTP_POST_VARS"]["munic"]:$this->munic);
       $this->uf = ($this->uf == ""?@$GLOBALS["HTTP_POST_VARS"]["uf"]:$this->uf);
       $this->telef = ($this->telef == ""?@$GLOBALS["HTTP_POST_VARS"]["telef"]:$this->telef);
       $this->ident = ($this->ident == ""?@$GLOBALS["HTTP_POST_VARS"]["ident"]:$this->ident);
       $this->tx_banc = ($this->tx_banc == ""?@$GLOBALS["HTTP_POST_VARS"]["tx_banc"]:$this->tx_banc);
       $this->numbanco = ($this->numbanco == ""?@$GLOBALS["HTTP_POST_VARS"]["numbanco"]:$this->numbanco);
       $this->url = ($this->url == ""?@$GLOBALS["HTTP_POST_VARS"]["url"]:$this->url);
       $this->logo = ($this->logo == ""?@$GLOBALS["HTTP_POST_VARS"]["logo"]:$this->logo);
       $this->figura = ($this->figura == ""?@$GLOBALS["HTTP_POST_VARS"]["figura"]:$this->figura);
       if($this->dtcont == ""){
         $this->dtcont_dia = ($this->dtcont_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["dtcont_dia"]:$this->dtcont_dia);
         $this->dtcont_mes = ($this->dtcont_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["dtcont_mes"]:$this->dtcont_mes);
         $this->dtcont_ano = ($this->dtcont_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["dtcont_ano"]:$this->dtcont_ano);
         if($this->dtcont_dia != ""){
            $this->dtcont = $this->dtcont_ano."-".$this->dtcont_mes."-".$this->dtcont_dia;
         }
       }
       $this->diario = ($this->diario == ""?@$GLOBALS["HTTP_POST_VARS"]["diario"]:$this->diario);
       $this->pref = ($this->pref == ""?@$GLOBALS["HTTP_POST_VARS"]["pref"]:$this->pref);
       $this->vicepref = ($this->vicepref == ""?@$GLOBALS["HTTP_POST_VARS"]["vicepref"]:$this->vicepref);
       $this->fax = ($this->fax == ""?@$GLOBALS["HTTP_POST_VARS"]["fax"]:$this->fax);
       $this->cgc = ($this->cgc == ""?@$GLOBALS["HTTP_POST_VARS"]["cgc"]:$this->cgc);
       $this->cep = ($this->cep == ""?@$GLOBALS["HTTP_POST_VARS"]["cep"]:$this->cep);
       $this->tpropri = ($this->tpropri == "f"?@$GLOBALS["HTTP_POST_VARS"]["tpropri"]:$this->tpropri);
       $this->tsocios = ($this->tsocios == "f"?@$GLOBALS["HTTP_POST_VARS"]["tsocios"]:$this->tsocios);
       $this->prefeitura = ($this->prefeitura == "f"?@$GLOBALS["HTTP_POST_VARS"]["prefeitura"]:$this->prefeitura);
       $this->bairro = ($this->bairro == ""?@$GLOBALS["HTTP_POST_VARS"]["bairro"]:$this->bairro);
       $this->numcgm = ($this->numcgm == ""?@$GLOBALS["HTTP_POST_VARS"]["numcgm"]:$this->numcgm);
       $this->codtrib = ($this->codtrib == ""?@$GLOBALS["HTTP_POST_VARS"]["codtrib"]:$this->codtrib);
       $this->tribinst = ($this->tribinst == ""?@$GLOBALS["HTTP_POST_VARS"]["tribinst"]:$this->tribinst);
       $this->segmento = ($this->segmento == ""?@$GLOBALS["HTTP_POST_VARS"]["segmento"]:$this->segmento);
       $this->formvencfebraban = ($this->formvencfebraban == ""?@$GLOBALS["HTTP_POST_VARS"]["formvencfebraban"]:$this->formvencfebraban);
       $this->numero = ($this->numero == ""?@$GLOBALS["HTTP_POST_VARS"]["numero"]:$this->numero);
       $this->nomedebconta = ($this->nomedebconta == ""?@$GLOBALS["HTTP_POST_VARS"]["nomedebconta"]:$this->nomedebconta);
       $this->db21_tipoinstit = ($this->db21_tipoinstit == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_tipoinstit"]:$this->db21_tipoinstit);
       $this->db21_ativo = ($this->db21_ativo == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_ativo"]:$this->db21_ativo);
       $this->db21_regracgmiss = ($this->db21_regracgmiss == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_regracgmiss"]:$this->db21_regracgmiss);
       $this->db21_regracgmiptu = ($this->db21_regracgmiptu == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_regracgmiptu"]:$this->db21_regracgmiptu);
       $this->db21_codcli = ($this->db21_codcli == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_codcli"]:$this->db21_codcli);
       $this->nomeinstabrev = ($this->nomeinstabrev == ""?@$GLOBALS["HTTP_POST_VARS"]["nomeinstabrev"]:$this->nomeinstabrev);
       $this->db21_usasisagua = ($this->db21_usasisagua == "f"?@$GLOBALS["HTTP_POST_VARS"]["db21_usasisagua"]:$this->db21_usasisagua);
       $this->db21_codigomunicipoestado = ($this->db21_codigomunicipoestado == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_codigomunicipoestado"]:$this->db21_codigomunicipoestado);
       if($this->db21_datalimite == ""){
         $this->db21_datalimite_dia = ($this->db21_datalimite_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_datalimite_dia"]:$this->db21_datalimite_dia);
         $this->db21_datalimite_mes = ($this->db21_datalimite_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_datalimite_mes"]:$this->db21_datalimite_mes);
         $this->db21_datalimite_ano = ($this->db21_datalimite_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_datalimite_ano"]:$this->db21_datalimite_ano);
         if($this->db21_datalimite_dia != ""){
            $this->db21_datalimite = $this->db21_datalimite_ano."-".$this->db21_datalimite_mes."-".$this->db21_datalimite_dia;
         }
       }
       if($this->db21_criacao == ""){
         $this->db21_criacao_dia = ($this->db21_criacao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_criacao_dia"]:$this->db21_criacao_dia);
         $this->db21_criacao_mes = ($this->db21_criacao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_criacao_mes"]:$this->db21_criacao_mes);
         $this->db21_criacao_ano = ($this->db21_criacao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_criacao_ano"]:$this->db21_criacao_ano);
         if($this->db21_criacao_dia != ""){
            $this->db21_criacao = $this->db21_criacao_ano."-".$this->db21_criacao_mes."-".$this->db21_criacao_dia;
         }
       }
       $this->db21_compl = ($this->db21_compl == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_compl"]:$this->db21_compl);
       $this->email = ($this->email == ""?@$GLOBALS["HTTP_POST_VARS"]["email"]:$this->email);
       $this->db21_imgmarcadagua = ($this->db21_imgmarcadagua == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_imgmarcadagua"]:$this->db21_imgmarcadagua);
       $this->db21_esfera = ($this->db21_esfera == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_esfera"]:$this->db21_esfera);
       $this->db21_tipopoder = ($this->db21_tipopoder == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_tipopoder"]:$this->db21_tipopoder);
       $this->db21_codtj = ($this->db21_codtj == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_codtj"]:$this->db21_codtj);
       $this->db21_codsiconfi = ($this->db21_codsiconfi == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_codsiconfi"]:$this->db21_codsiconfi);
       $this->db21_unidade_gestora_rpps = ($this->db21_unidade_gestora_rpps == "f"?@$GLOBALS["HTTP_POST_VARS"]["db21_unidade_gestora_rpps"]:$this->db21_unidade_gestora_rpps);
       $this->db21_esfera_op = ($this->db21_esfera_op == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_esfera_op"]:$this->db21_esfera_op);
       $this->db21_valor_teto_remuneratorio = ($this->db21_valor_teto_remuneratorio == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_valor_teto_remuneratorio"]:$this->db21_valor_teto_remuneratorio);
       $this->db21_ente_federativo_resp = ($this->db21_ente_federativo_resp == "f"?@$GLOBALS["HTTP_POST_VARS"]["db21_ente_federativo_resp"]:$this->db21_ente_federativo_resp);
       $this->db21_cnpj_efr = ($this->db21_cnpj_efr == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_cnpj_efr"]:$this->db21_cnpj_efr);
       $this->db21_efr_previdencia_compl = ($this->db21_efr_previdencia_compl == "f"?@$GLOBALS["HTTP_POST_VARS"]["db21_efr_previdencia_compl"]:$this->db21_efr_previdencia_compl);
       $this->db21_possui_rpps = ($this->db21_possui_rpps == "f"?@$GLOBALS["HTTP_POST_VARS"]["db21_possui_rpps"]:$this->db21_possui_rpps);
       $this->db21_departamento = ($this->db21_departamento == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_departamento"]:$this->db21_departamento);
       $this->db21_descr_depart_abrev = ($this->db21_descr_depart_abrev == ""?@$GLOBALS["HTTP_POST_VARS"]["db21_descr_depart_abrev"]:$this->db21_descr_depart_abrev);
       $this->db21_permiteinscricaocgf = ($this->db21_permiteinscricaocgf == "f"?@$GLOBALS["HTTP_POST_VARS"]["db21_permiteinscricaocgf"]:$this->db21_permiteinscricaocgf);
     }else{
       $this->codigo = ($this->codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["codigo"]:$this->codigo);
     }
   }

    public function incluir($codigo)
    {
      $this->atualizacampos();
     if($this->nomeinst == null ){
       $this->erro_sql = " Campo Nome da Instituição não informado.";
       $this->erro_campo = "nomeinst";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ender == null ){
       $this->erro_sql = " Campo Endereço da Instituição não informado.";
       $this->erro_campo = "ender";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->munic == null ){
       $this->erro_sql = " Campo Município da Instituição não informado.";
       $this->erro_campo = "munic";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->uf == null ){
       $this->erro_sql = " Campo Unidade Federativa da Instituição não informado.";
       $this->erro_campo = "uf";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->telef == null ){
       $this->erro_sql = " Campo Telefone não informado.";
       $this->erro_campo = "telef";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ident == null ){
       $this->erro_sql = " Campo identidade não informado.";
       $this->erro_campo = "ident";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->tx_banc == null ){
       $this->tx_banc = "0";
     }
     if($this->url == null ){
       $this->erro_sql = " Campo url não informado.";
       $this->erro_campo = "url";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->dtcont == null ){
       $this->erro_sql = " Campo data da contabilidade não informado.";
       $this->erro_campo = "dtcont_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->diario == null ){
       $this->erro_sql = " Campo Diário não informado.";
       $this->erro_campo = "diario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pref == null ){
       $this->erro_sql = " Campo prefeito não informado.";
       $this->erro_campo = "pref";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->vicepref == null ){
       $this->erro_sql = " Campo vice prefeito não informado.";
       $this->erro_campo = "vicepref";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fax == null ){
       $this->erro_sql = " Campo fax não informado.";
       $this->erro_campo = "fax";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->cgc == null ){
       $this->erro_sql = " Campo cgc não informado.";
       $this->erro_campo = "cgc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->cep == null ){
       $this->erro_sql = " Campo cep não informado.";
       $this->erro_campo = "cep";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->tpropri == null ){
       $this->erro_sql = " Campo Débitos proprietário não informado.";
       $this->erro_campo = "tpropri";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->tsocios == null ){
       $this->erro_sql = " Campo Débitos Sócios não informado.";
       $this->erro_campo = "tsocios";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->prefeitura == null ){
       $this->erro_sql = " Campo Prefeitura não informado.";
       $this->erro_campo = "prefeitura";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->numcgm == null ){
       $this->erro_sql = " Campo Número do CGM não informado.";
       $this->erro_campo = "numcgm";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->codtrib == null ){
       $this->erro_sql = " Campo Órgão/Unidade da Instituição não informado.";
       $this->erro_campo = "codtrib";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->tribinst == null ){
       $this->erro_sql = " Campo Instituição SIAPC/PAD não informado.";
       $this->erro_campo = "tribinst";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->segmento == null ){
       $this->erro_sql = " Campo Segmento Código de Barras Febraban não informado.";
       $this->erro_campo = "segmento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->formvencfebraban == null ){
       $this->erro_sql = " Campo Forma do vencimento Febraban não informado.";
       $this->erro_campo = "formvencfebraban";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->numero == null ){
       $this->numero = "0";
     }
     if($this->nomedebconta == null ){
       $this->erro_sql = " Campo Nome da instituição no débito em conta não informado.";
       $this->erro_campo = "nomedebconta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_tipoinstit == null ){
       $this->erro_sql = " Campo Tipo de Instituição não informado.";
       $this->erro_campo = "db21_tipoinstit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_ativo == null ){
       $this->erro_sql = " Campo Ativo não informado.";
       $this->erro_campo = "db21_ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_regracgmiss == null ){
       $this->erro_sql = " Campo Regra CGM issbase não informado.";
       $this->erro_campo = "db21_regracgmiss";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_regracgmiptu == null ){
       $this->erro_sql = " Campo Regra Cgm Iptu não informado.";
       $this->erro_campo = "db21_regracgmiptu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_codcli == null ){
       $this->erro_sql = " Campo Código do cliente não informado.";
       $this->erro_campo = "db21_codcli";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nomeinstabrev == null ){
       $this->erro_sql = " Campo Nome da instituição para relatório não informado.";
       $this->erro_campo = "nomeinstabrev";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_usasisagua == null ){
       $this->erro_sql = " Campo Usa sistema de água não informado.";
       $this->erro_campo = "db21_usasisagua";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_codigomunicipoestado == null ){
       $this->erro_sql = " Campo Código do município no estado não informado.";
       $this->erro_campo = "db21_codigomunicipoestado";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->db21_datalimite == null ){
       $this->db21_datalimite = "null";
     }
     if($this->db21_criacao == null ){
       $this->db21_criacao = "null";
     }
     if($this->db21_esfera == null ){
       $this->db21_esfera = "0";
     }
     if($this->db21_tipopoder == null ){
       $this->db21_tipopoder = "6";
     }
     if($this->db21_codtj == null ){
       $this->erro_sql = " Campo Código do município na TJ não informado.";
       $this->erro_campo = "db21_codtj";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->db21_unidade_gestora_rpps === '' || $this->db21_unidade_gestora_rpps === null) {
       $this->erro_sql = " Campo Unidade Gestora do RPPS não informado.";
       $this->erro_campo = "db21_unidade_gestora_rpps";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->db21_ente_federativo_resp === '' || $this->db21_ente_federativo_resp === null) {
        $this->erro_sql = " Campo Órgão Público é o EFR não informado.";
        $this->erro_campo = "db21_ente_federativo_resp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->db21_efr_previdencia_compl === '' || $this->db21_efr_previdencia_compl === null) {
        $this->erro_sql = " Campo EFR instituiu previdência complementar não informado.";
        $this->erro_campo = "db21_efr_previdencia_compl";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->db21_possui_rpps === '' || $this->db21_possui_rpps === null) {
       $this->erro_sql = " Campo Regime Próprio de Previdência Social não informado.";
       $this->erro_campo = "db21_possui_rpps";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if ($this->db21_permiteinscricaocgf === '' || $this->db21_permiteinscricaocgf === null) {
      $this->erro_sql = " Campo Permite inscrição econômica CGF não informado.";
      $this->erro_campo = "db21_permiteinscricaocgf";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
     if($this->db21_departamento == null ){
       $this->db21_departamento = "null";
     }
     if($this->db21_descr_depart_abrev == null ){
       $this->db21_descr_depart_abrev = "null";
     }

     if($codigo == "" || $codigo == null ){
       $result = db_query("select nextval('db_config_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: db_config_codigo_seq do campo: codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from db_config_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $codigo)){
         $this->erro_sql = " Campo codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->codigo = $codigo;
       }
     }
     if(($this->codigo == null) || ($this->codigo == "") ){
       $this->erro_sql = " Campo codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into db_config(
                                       codigo
                                      ,nomeinst
                                      ,ender
                                      ,munic
                                      ,uf
                                      ,telef
                                      ,ident
                                      ,tx_banc
                                      ,numbanco
                                      ,url
                                      ,logo
                                      ,figura
                                      ,dtcont
                                      ,diario
                                      ,pref
                                      ,vicepref
                                      ,fax
                                      ,cgc
                                      ,cep
                                      ,tpropri
                                      ,tsocios
                                      ,prefeitura
                                      ,bairro
                                      ,numcgm
                                      ,codtrib
                                      ,tribinst
                                      ,segmento
                                      ,formvencfebraban
                                      ,numero
                                      ,nomedebconta
                                      ,db21_tipoinstit
                                      ,db21_ativo
                                      ,db21_regracgmiss
                                      ,db21_regracgmiptu
                                      ,db21_codcli
                                      ,nomeinstabrev
                                      ,db21_usasisagua
                                      ,db21_codigomunicipoestado
                                      ,db21_datalimite
                                      ,db21_criacao
                                      ,db21_compl
                                      ,email
                                      ,db21_imgmarcadagua
                                      ,db21_esfera
                                      ,db21_tipopoder
                                      ,db21_codtj
                                      ,db21_codsiconfi
                                      ,db21_unidade_gestora_rpps
                                      ,db21_esfera_op
                                      ,db21_valor_teto_remuneratorio
                                      ,db21_ente_federativo_resp
                                      ,db21_cnpj_efr
                                      ,db21_efr_previdencia_compl
                                      ,db21_possui_rpps
                                      ,db21_departamento
                                      ,db21_descr_depart_abrev
                                      ,db21_permiteinscricaocgf
                       )
                values (
                                $this->codigo
                               ,'$this->nomeinst'
                               ,'$this->ender'
                               ,'$this->munic'
                               ,'$this->uf'
                               ,'$this->telef'
                               ,$this->ident
                               ,$this->tx_banc
                               ,'$this->numbanco'
                               ,'$this->url'
                               ,'$this->logo'
                               ,'$this->figura'
                               ,".($this->dtcont == "null" || $this->dtcont == ""?"null":"'".$this->dtcont."'")."
                               ,$this->diario
                               ,'$this->pref'
                               ,'$this->vicepref'
                               ,'$this->fax'
                               ,'$this->cgc'
                               ,'$this->cep'
                               ,'$this->tpropri'
                               ,'$this->tsocios'
                               ,'$this->prefeitura'
                               ,'$this->bairro'
                               ,$this->numcgm
                               ,'$this->codtrib'
                               ,$this->tribinst
                               ,$this->segmento
                               ,$this->formvencfebraban
                               ,$this->numero
                               ,'$this->nomedebconta'
                               ,$this->db21_tipoinstit
                               ,$this->db21_ativo
                               ,$this->db21_regracgmiss
                               ,$this->db21_regracgmiptu
                               ,$this->db21_codcli
                               ,'$this->nomeinstabrev'
                               ,'$this->db21_usasisagua'
                               ,$this->db21_codigomunicipoestado
                               ,".($this->db21_datalimite == "null" || $this->db21_datalimite == ""?"null":"'".$this->db21_datalimite."'")."
                               ,".($this->db21_criacao == "null" || $this->db21_criacao == ""?"null":"'".$this->db21_criacao."'")."
                               ,'$this->db21_compl'
                               ,'$this->email'
                               ,$this->db21_imgmarcadagua
                               ,$this->db21_esfera
                               ,$this->db21_tipopoder
                               ,$this->db21_codtj
                               ,'$this->db21_codsiconfi'
                               ,'$this->db21_unidade_gestora_rpps'
                               ,".($this->db21_esfera_op == "null" || $this->db21_esfera_op == ""?"null":"'".$this->db21_esfera_op."'")."
                               ,".($this->db21_valor_teto_remuneratorio == "null" || $this->db21_valor_teto_remuneratorio == ""?"null":$this->db21_valor_teto_remuneratorio)."
                               ,'$this->db21_ente_federativo_resp'
                               ,".($this->db21_cnpj_efr == "null" || $this->db21_cnpj_efr == ""?"null":"'".$this->db21_cnpj_efr."'")."
                               ,'$this->db21_efr_previdencia_compl'
                               ,'$this->db21_possui_rpps'
                               ,$this->db21_departamento
                               ,'$this->db21_descr_depart_abrev'
                               ,'$this->db21_permiteinscricaocgf'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = " ($this->codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = " já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = " ($this->codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }

    public function alterar($codigo=null)
    {
      $this->atualizacampos();
     $sql = " update db_config set ";
     $virgula = "";
     if(trim($this->codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["codigo"])){
       $sql  .= $virgula." codigo = $this->codigo ";
       $virgula = ",";
       if(trim($this->codigo) == null ){
         $this->erro_sql = " Campo Código da Instituição não informado.";
         $this->erro_campo = "codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nomeinst)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nomeinst"])){
       $sql  .= $virgula." nomeinst = '$this->nomeinst' ";
       $virgula = ",";
       if(trim($this->nomeinst) == null ){
         $this->erro_sql = " Campo Nome da Instituição não informado.";
         $this->erro_campo = "nomeinst";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ender)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ender"])){
       $sql  .= $virgula." ender = '$this->ender' ";
       $virgula = ",";
       if(trim($this->ender) == null ){
         $this->erro_sql = " Campo Endereço da Instituição não informado.";
         $this->erro_campo = "ender";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->munic)!="" || isset($GLOBALS["HTTP_POST_VARS"]["munic"])){
       $sql  .= $virgula." munic = '$this->munic' ";
       $virgula = ",";
       if(trim($this->munic) == null ){
         $this->erro_sql = " Campo Município da Instituição não informado.";
         $this->erro_campo = "munic";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->uf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["uf"])){
       $sql  .= $virgula." uf = '$this->uf' ";
       $virgula = ",";
       if(trim($this->uf) == null ){
         $this->erro_sql = " Campo Unidade Federativa da Instituição não informado.";
         $this->erro_campo = "uf";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->telef)!="" || isset($GLOBALS["HTTP_POST_VARS"]["telef"])){
       $sql  .= $virgula." telef = '$this->telef' ";
       $virgula = ",";
       if(trim($this->telef) == null ){
         $this->erro_sql = " Campo Telefone não informado.";
         $this->erro_campo = "telef";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ident)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ident"])){
       $sql  .= $virgula." ident = $this->ident ";
       $virgula = ",";
       if(trim($this->ident) == null ){
         $this->erro_sql = " Campo identidade não informado.";
         $this->erro_campo = "ident";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tx_banc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tx_banc"])){
        if(trim($this->tx_banc)=="" && isset($GLOBALS["HTTP_POST_VARS"]["tx_banc"])){
           $this->tx_banc = "0" ;
        }
       $sql  .= $virgula." tx_banc = $this->tx_banc ";
       $virgula = ",";
     }
     if(trim($this->numbanco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["numbanco"])){
       $sql  .= $virgula." numbanco = '$this->numbanco' ";
       $virgula = ",";
     }
     if(trim($this->url)!="" || isset($GLOBALS["HTTP_POST_VARS"]["url"])){
       $sql  .= $virgula." url = '$this->url' ";
       $virgula = ",";
       if(trim($this->url) == null ){
         $this->erro_sql = " Campo url não informado.";
         $this->erro_campo = "url";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->logo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["logo"])){
       $sql  .= $virgula." logo = '$this->logo' ";
       $virgula = ",";
     }
     if(trim($this->figura)!="" || isset($GLOBALS["HTTP_POST_VARS"]["figura"])){
       $sql  .= $virgula." figura = '$this->figura' ";
       $virgula = ",";
     }
     if(trim($this->dtcont)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dtcont_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["dtcont_dia"] !="") ){
       $sql  .= $virgula." dtcont = '$this->dtcont' ";
       $virgula = ",";
       if(trim($this->dtcont) == null ){
         $this->erro_sql = " Campo data da contabilidade não informado.";
         $this->erro_campo = "dtcont_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["dtcont_dia"])){
         $sql  .= $virgula." dtcont = null ";
         $virgula = ",";
         if(trim($this->dtcont) == null ){
           $this->erro_sql = " Campo data da contabilidade não informado.";
           $this->erro_campo = "dtcont_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->diario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["diario"])){
       $sql  .= $virgula." diario = $this->diario ";
       $virgula = ",";
       if(trim($this->diario) == null ){
         $this->erro_sql = " Campo Diário não informado.";
         $this->erro_campo = "diario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pref)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pref"])){
       $sql  .= $virgula." pref = '$this->pref' ";
       $virgula = ",";
       if(trim($this->pref) == null ){
         $this->erro_sql = " Campo prefeito não informado.";
         $this->erro_campo = "pref";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->vicepref)!="" || isset($GLOBALS["HTTP_POST_VARS"]["vicepref"])){
       $sql  .= $virgula." vicepref = '$this->vicepref' ";
       $virgula = ",";
       if(trim($this->vicepref) == null ){
         $this->erro_sql = " Campo vice prefeito não informado.";
         $this->erro_campo = "vicepref";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fax)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fax"])){
       $sql  .= $virgula." fax = '$this->fax' ";
       $virgula = ",";
       if(trim($this->fax) == null ){
         $this->erro_sql = " Campo fax não informado.";
         $this->erro_campo = "fax";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->cgc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["cgc"])){
       $sql  .= $virgula." cgc = '$this->cgc' ";
       $virgula = ",";
       if(trim($this->cgc) == null ){
         $this->erro_sql = " Campo cgc não informado.";
         $this->erro_campo = "cgc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->cep)!="" || isset($GLOBALS["HTTP_POST_VARS"]["cep"])){
       $sql  .= $virgula." cep = '$this->cep' ";
       $virgula = ",";
       if(trim($this->cep) == null ){
         $this->erro_sql = " Campo cep não informado.";
         $this->erro_campo = "cep";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tpropri)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tpropri"])){
       $sql  .= $virgula." tpropri = '$this->tpropri' ";
       $virgula = ",";
       if(trim($this->tpropri) == null ){
         $this->erro_sql = " Campo Débitos proprietário não informado.";
         $this->erro_campo = "tpropri";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tsocios)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tsocios"])){
       $sql  .= $virgula." tsocios = '$this->tsocios' ";
       $virgula = ",";
       if(trim($this->tsocios) == null ){
         $this->erro_sql = " Campo Débitos Sócios não informado.";
         $this->erro_campo = "tsocios";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->prefeitura)!="" || isset($GLOBALS["HTTP_POST_VARS"]["prefeitura"])){
       $sql  .= $virgula." prefeitura = '$this->prefeitura' ";
       $virgula = ",";
       if(trim($this->prefeitura) == null ){
         $this->erro_sql = " Campo Prefeitura não informado.";
         $this->erro_campo = "prefeitura";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->bairro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["bairro"])){
       $sql  .= $virgula." bairro = '$this->bairro' ";
       $virgula = ",";
     }
     if(trim($this->numcgm)!="" || isset($GLOBALS["HTTP_POST_VARS"]["numcgm"])){
       $sql  .= $virgula." numcgm = $this->numcgm ";
       $virgula = ",";
       if(trim($this->numcgm) == null ){
         $this->erro_sql = " Campo Número do CGM não informado.";
         $this->erro_campo = "numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->codtrib)!="" || isset($GLOBALS["HTTP_POST_VARS"]["codtrib"])){
       $sql  .= $virgula." codtrib = '$this->codtrib' ";
       $virgula = ",";
       if(trim($this->codtrib) == null ){
         $this->erro_sql = " Campo Órgão/Unidade da Instituição não informado.";
         $this->erro_campo = "codtrib";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->tribinst)!="" || isset($GLOBALS["HTTP_POST_VARS"]["tribinst"])){
       $sql  .= $virgula." tribinst = $this->tribinst ";
       $virgula = ",";
       if(trim($this->tribinst) == null ){
         $this->erro_sql = " Campo Instituição SIAPC/PAD não informado.";
         $this->erro_campo = "tribinst";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->segmento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["segmento"])){
       $sql  .= $virgula." segmento = $this->segmento ";
       $virgula = ",";
       if(trim($this->segmento) == null ){
         $this->erro_sql = " Campo Segmento Código de Barras Febraban não informado.";
         $this->erro_campo = "segmento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->formvencfebraban)!="" || isset($GLOBALS["HTTP_POST_VARS"]["formvencfebraban"])){
       $sql  .= $virgula." formvencfebraban = $this->formvencfebraban ";
       $virgula = ",";
       if(trim($this->formvencfebraban) == null ){
         $this->erro_sql = " Campo Forma do vencimento Febraban não informado.";
         $this->erro_campo = "formvencfebraban";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["numero"])){
        if(trim($this->numero)=="" && isset($GLOBALS["HTTP_POST_VARS"]["numero"])){
           $this->numero = "0" ;
        }
       $sql  .= $virgula." numero = $this->numero ";
       $virgula = ",";
     }
     if(trim($this->nomedebconta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nomedebconta"])){
       $sql  .= $virgula." nomedebconta = '$this->nomedebconta' ";
       $virgula = ",";
       if(trim($this->nomedebconta) == null ){
         $this->erro_sql = " Campo Nome da instituição no débito em conta não informado.";
         $this->erro_campo = "nomedebconta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_tipoinstit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_tipoinstit"])){
       $sql  .= $virgula." db21_tipoinstit = $this->db21_tipoinstit ";
       $virgula = ",";
       if(trim($this->db21_tipoinstit) == null ){
         $this->erro_sql = " Campo Tipo de Instituição não informado.";
         $this->erro_campo = "db21_tipoinstit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_ativo"])){
       $sql  .= $virgula." db21_ativo = $this->db21_ativo ";
       $virgula = ",";
       if(trim($this->db21_ativo) == null ){
         $this->erro_sql = " Campo Ativo não informado.";
         $this->erro_campo = "db21_ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_regracgmiss)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_regracgmiss"])){
       $sql  .= $virgula." db21_regracgmiss = $this->db21_regracgmiss ";
       $virgula = ",";
       if(trim($this->db21_regracgmiss) == null ){
         $this->erro_sql = " Campo Regra CGM issbase não informado.";
         $this->erro_campo = "db21_regracgmiss";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_regracgmiptu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_regracgmiptu"])){
       $sql  .= $virgula." db21_regracgmiptu = $this->db21_regracgmiptu ";
       $virgula = ",";
       if(trim($this->db21_regracgmiptu) == null ){
         $this->erro_sql = " Campo Regra Cgm Iptu não informado.";
         $this->erro_campo = "db21_regracgmiptu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_codcli)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_codcli"])){
       $sql  .= $virgula." db21_codcli = $this->db21_codcli ";
       $virgula = ",";
       if(trim($this->db21_codcli) == null ){
         $this->erro_sql = " Campo Código do cliente não informado.";
         $this->erro_campo = "db21_codcli";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nomeinstabrev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nomeinstabrev"])){
       $sql  .= $virgula." nomeinstabrev = '$this->nomeinstabrev' ";
       $virgula = ",";
       if(trim($this->nomeinstabrev) == null ){
         $this->erro_sql = " Campo Nome da instituição para relatório não informado.";
         $this->erro_campo = "nomeinstabrev";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_usasisagua)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_usasisagua"])){
       $sql  .= $virgula." db21_usasisagua = '$this->db21_usasisagua' ";
       $virgula = ",";
       if(trim($this->db21_usasisagua) == null ){
         $this->erro_sql = " Campo Usa sistema de água não informado.";
         $this->erro_campo = "db21_usasisagua";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_codigomunicipoestado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_codigomunicipoestado"])){
       $sql  .= $virgula." db21_codigomunicipoestado = $this->db21_codigomunicipoestado ";
       $virgula = ",";
       if(trim($this->db21_codigomunicipoestado) == null ){
         $this->erro_sql = " Campo Código do município no estado não informado.";
         $this->erro_campo = "db21_codigomunicipoestado";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_datalimite)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_datalimite_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["db21_datalimite_dia"] !="") ){
       $sql  .= $virgula." db21_datalimite = '$this->db21_datalimite' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["db21_datalimite_dia"])){
         $sql  .= $virgula." db21_datalimite = null ";
         $virgula = ",";
       }
     }
     if(trim($this->db21_criacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_criacao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["db21_criacao_dia"] !="") ){
       $sql  .= $virgula." db21_criacao = '$this->db21_criacao' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["db21_criacao_dia"])){
         $sql  .= $virgula." db21_criacao = null ";
         $virgula = ",";
       }
     }
     if(trim($this->db21_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_compl"])){
       $sql  .= $virgula." db21_compl = '$this->db21_compl' ";
       $virgula = ",";
     }
     if(trim($this->email)!="" || isset($GLOBALS["HTTP_POST_VARS"]["email"])){
       $sql  .= $virgula." email = '$this->email' ";
       $virgula = ",";
     }
     if(trim($this->db21_imgmarcadagua)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_imgmarcadagua"])){
       $sql  .= $virgula." db21_imgmarcadagua = $this->db21_imgmarcadagua ";
       $virgula = ",";
     }
     if(trim($this->db21_esfera)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_esfera"])){
        if(trim($this->db21_esfera)=="" && isset($GLOBALS["HTTP_POST_VARS"]["db21_esfera"])){
           $this->db21_esfera = "0" ;
        }
       $sql  .= $virgula." db21_esfera = $this->db21_esfera ";
       $virgula = ",";
     }
     if(trim($this->db21_tipopoder)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_tipopoder"])){
        if(trim($this->db21_tipopoder)=="" && isset($GLOBALS["HTTP_POST_VARS"]["db21_tipopoder"])){
           $this->db21_tipopoder = "0" ;
        }
       $sql  .= $virgula." db21_tipopoder = $this->db21_tipopoder ";
       $virgula = ",";
     }
     if(trim($this->db21_codtj)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_codtj"])){
       $sql  .= $virgula." db21_codtj = $this->db21_codtj ";
       $virgula = ",";
       if(trim($this->db21_codtj) == null ){
         $this->erro_sql = " Campo Código do município na TJ não informado.";
         $this->erro_campo = "db21_codtj";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->db21_codsiconfi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_codsiconfi"])){
       $sql  .= $virgula." db21_codsiconfi = '$this->db21_codsiconfi' ";
       $virgula = ",";
     }
     if(trim($this->db21_unidade_gestora_rpps)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_unidade_gestora_rpps"])){
       $sql  .= $virgula." db21_unidade_gestora_rpps = '$this->db21_unidade_gestora_rpps' ";
       $virgula = ",";
       }
     if(trim($this->db21_esfera_op)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_esfera_op"])){
       $sql  .= $virgula." db21_esfera_op = $this->db21_esfera_op ";
       $virgula = ",";
       }
     if(trim($this->db21_valor_teto_remuneratorio)!=""){
       $sql  .= $virgula." db21_valor_teto_remuneratorio = $this->db21_valor_teto_remuneratorio ";
       $virgula = ",";
     } else {
       $sql  .= $virgula." db21_valor_teto_remuneratorio = NULL ";
       $virgula = ",";
     }
     if(trim($this->db21_ente_federativo_resp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_ente_federativo_resp"])){
       $sql  .= $virgula." db21_ente_federativo_resp = '$this->db21_ente_federativo_resp' ";
       $virgula = ",";
       }
     if(trim($this->db21_cnpj_efr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_cnpj_efr"])){
       $sql  .= $virgula." db21_cnpj_efr = '$this->db21_cnpj_efr' ";
       $virgula = ",";
     }
     if(trim($this->db21_departamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_departamento"])){
        if(trim($this->db21_departamento)=="" && isset($GLOBALS["HTTP_POST_VARS"]["db21_departamento"])){
           $this->db21_departamento = "0" ;
        }
       $sql  .= $virgula." db21_departamento = $this->db21_departamento ";
       $virgula = ",";
     }
     if(trim($this->db21_descr_depart_abrev)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_descr_depart_abrev"])){
      $sql  .= $virgula." db21_descr_depart_abrev = '$this->db21_descr_depart_abrev' ";
      $virgula = ",";
   }
     if(trim($this->db21_efr_previdencia_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_efr_previdencia_compl"])){
       $sql  .= $virgula." db21_efr_previdencia_compl = '$this->db21_efr_previdencia_compl' ";
       $virgula = ",";
     }
     if(trim($this->db21_possui_rpps)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_possui_rpps"])){
       $sql  .= $virgula." db21_possui_rpps = '$this->db21_possui_rpps' ";
       $virgula = ",";
     }
     if(trim($this->db21_permiteinscricaocgf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["db21_permiteinscricaocgf"])){
      $sql  .= $virgula." db21_permiteinscricaocgf = '$this->db21_permiteinscricaocgf' ";
      $virgula = ",";
    }

     $sql .= " where ";
     if($codigo!=null){
       $sql .= " codigo = $this->codigo";
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($codigo=null, $dbwhere = null)
    {
     $sql = " delete from db_config
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " codigo = $codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = " não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = " não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:db_config";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from db_config ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($codigo)) {
         $sql2 .= " where db_config.codigo = $codigo ";
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

    public function sql_query_file($codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from db_config ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($codigo)){
         $sql2 .= " where db_config.codigo = $codigo ";
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

   public function sql_query_log ( $codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from db_config ";
     $sql2 = "inner join ceplocalidades on cp05_localidades = munic
              inner join ceplogradouros on cp06_codlocalidade = cp05_codlocalidades";
     if($dbwhere==""){
       if($codigo!=null ){
         $sql2 .= " where db_config.codigo = $codigo ";
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
   public function sql_query_usu ( $codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     $sql2 = '';
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
     $sql .= " from db_config ";
     $sql .= " inner join db_userinst on id_instit = db_config.codigo ";
     if($dbwhere==""){
       if($codigo!=null ){
         $sql2 .= " where db_config.codigo = $codigo ";
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
   public function sql_query_tipoinstit ( $codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from db_config ";
     $sql2 ="inner join db_tipoinstit on db21_codtipo=db21_tipoinstit";
     if($dbwhere==""){
       if($codigo!=null ){
         $sql2 .= " where db_config.codigo = $codigo ";
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
   public function sql_query_siafi( $codigo=null,$campos="*",$ordem=null,$dbwhere=""){

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
     $sql .= " from db_config ";
     $sql2 ="       inner join municipiosiafi on municipiosiafi.q110_cnpj = db_config.cgc";
     if($dbwhere==""){
       if($codigo!=null ){
         $sql2 .= " where db_config.codigo = $codigo ";
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
   * Retorna um objeto com os dados da instituição
   * @param integer $iInstit - Instituição qual os dados devem ser retornados
   * @return mixed boolean, object db_fields
   */
  public function getParametrosInstituicao($iInstit=null) {

    if (empty($iInstit)){
      $iInstit = db_getsession("DB_instit");
    }

  	$sSql = "select * from db_config where codigo = " . $iInstit;

  	$rsSql = db_query($sSql);

  	if  ( $rsSql && pg_num_rows($rsSql) ) {
  		return db_utils::fieldsMemory($rsSql, 0);
  	}
  	return false;
  }


  public function getCodigoTom($iInstit = null) {

    if (empty($iInstit)){
      $iInstit = db_getsession("DB_instit");
    }

    $sSql  = "select db125_codigosistema                                                                  ";
    $sSql .= "  from db_config                                                                            ";
    $sSql .= "       inner join cadenderestado           on trim(db71_sigla)        = uf                  ";
    $sSql .= "       inner join cadendermunicipio        on db71_sequencial         = db72_cadenderestado ";
    $sSql .= "                                          and trim(db72_descricao)    = munic               ";
    $sSql .= "       inner join cadendermunicipiosistema on db125_cadendermunicipio = db72_sequencial     ";
    $sSql .= "                                          and db125_db_sistemaexterno = 5                   ";
    $sSql .= " where codigo = $iInstit;                                                                   ";

    $rsDbConfig = $this->sql_record($sSql);

    if ($this->numrows > 0) {
      return db_utils::fieldsMemory($rsDbConfig, 0);
    }

    return null;
  }
}
?>
