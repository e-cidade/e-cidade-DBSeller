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

//MODULO: pessoal
//CLASSE DA ENTIDADE rhpesdoc
class cl_rhpesdoc
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
    // cria variaveis do arquivo
    public $rh16_regist = 0;
    public $rh16_titele = null;
    public $rh16_zonael = null;
    public $rh16_secaoe = null;
    public $rh16_reserv = null;
    public $rh16_catres = null;
    public $rh16_ctps_n = null;
    public $rh16_ctps_s = 0;
    public $rh16_ctps_d = 0;
    public $rh16_ctps_uf = null;
    public $rh16_pis = null;
    public $rh16_carth_n = null;
    public $r16_carth_cat = null;
    public $rh16_carth_val_dia = null;
    public $rh16_carth_val_mes = null;
    public $rh16_carth_val_ano = null;
    public $rh16_carth_val = null;
    public $rh16_emissao_dia = null;
    public $rh16_emissao_mes = null;
    public $rh16_emissao_ano = null;
    public $rh16_emissao = null;
    public $rh16_data_emissao_cnh = null;
    public $rh16_orgao_classe = null;
    public $rh16_data_orgao_classe = null;
    public $rh16_orgao_emissor_classe = null;
    public $rh16_orgao_emissor_rne = null;
    public $rh16_data_emissao_rne = null;
    public $rh16_data_entrada_rne = null;
    public $rh16_data_validade_rne = null;
    public $rh16_uf_cnh = null;
    public $rh16_data_validade_orgao_classe = null;
    public $rh16_registro_rne = null;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 rh16_regist = int4 = Matrícula
                 rh16_titele = varchar(12) = Título
                 rh16_zonael = varchar(3) = Zona
                 rh16_secaoe = varchar(4) = Seção
                 rh16_reserv = varchar(15) = Certificado
                 rh16_catres = varchar(4) = Categoria
                 rh16_ctps_n = int4 = CTPS
                 rh16_ctps_s = int4 = Série
                 rh16_ctps_d = int4 = Dígito
                 rh16_ctps_uf = varchar(2) = UF da CTPS
                 rh16_pis = varchar(11) = Pis/Pasep/CI
                 rh16_carth_n = int8 = CNH
                 r16_carth_cat = varchar(3) = Categoria
                 rh16_carth_val = date = Validade
                 rh16_emissao = date = Data de emissão
                 rh16_data_emissao_cnh = date = Data de Emissao da CNH
                 rh16_orgao_classe = varchar(15) = Registro de Órgão de Classe
                 rh16_data_orgao_classe = date = Data de Órgão de Classe
                 rh16_orgao_emissor_classe = varchar(15) = Órgão Emissor da Classe
                 rh16_orgao_emissor_rne = varchar(15) = Órgão Emissor da RNE
                 rh16_data_emissao_rne = date = Data de Emissão da RNE
                 rh16_data_entrada_rne = date = Data de Entrada no País
                 rh16_data_validade_rne = date = Data de Validade da RNE
                 rh16_uf_cnh = varchar(2) = UF da CNH
                 rh16_data_validade_orgao_classe = date = Data de Validade do Órgão de Classe
                 rh16_registro_rne = varchar(15) Registro da RNE
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("rhpesdoc");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);}

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
       $this->rh16_regist = ($this->rh16_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_regist"]:$this->rh16_regist);
       $this->rh16_titele = ($this->rh16_titele == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_titele"]:$this->rh16_titele);
       $this->rh16_zonael = ($this->rh16_zonael == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_zonael"]:$this->rh16_zonael);
       $this->rh16_secaoe = ($this->rh16_secaoe == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_secaoe"]:$this->rh16_secaoe);
       $this->rh16_reserv = ($this->rh16_reserv == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_reserv"]:$this->rh16_reserv);
       $this->rh16_catres = ($this->rh16_catres == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_catres"]:$this->rh16_catres);
       $this->rh16_ctps_n = ($this->rh16_ctps_n == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_ctps_n"]:$this->rh16_ctps_n);
       $this->rh16_ctps_s = ($this->rh16_ctps_s == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_ctps_s"]:$this->rh16_ctps_s);
       $this->rh16_ctps_d = ($this->rh16_ctps_d == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_ctps_d"]:$this->rh16_ctps_d);
       $this->rh16_ctps_uf = ($this->rh16_ctps_uf == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_ctps_uf"]:$this->rh16_ctps_uf);
       $this->rh16_pis = ($this->rh16_pis == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_pis"]:$this->rh16_pis);
       $this->rh16_carth_n = ($this->rh16_carth_n == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_carth_n"]:$this->rh16_carth_n);
       $this->r16_carth_cat = ($this->r16_carth_cat == ""?@$GLOBALS["HTTP_POST_VARS"]["r16_carth_cat"]:$this->r16_carth_cat);
       $this->rh16_orgao_classe = ($this->rh16_orgao_classe == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_orgao_classe"]:$this->rh16_orgao_classe);
       $this->rh16_orgao_emissor_classe = ($this->rh16_orgao_emissor_classe == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_orgao_emissor_classe"]:$this->rh16_orgao_emissor_classe);
       $this->rh16_orgao_emissor_rne = ($this->rh16_orgao_emissor_rne == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_orgao_emissor_rne"]:$this->rh16_orgao_emissor_rne);
       $this->rh16_uf_cnh = ($this->rh16_uf_cnh == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_uf_cnh"]:$this->rh16_uf_cnh);
       $this->rh16_registro_rne = ($this->rh16_registro_rne == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_registro_rne"]:$this->rh16_registro_rne);
       if($this->rh16_carth_val == ""){
         $this->rh16_carth_val_dia = ($this->rh16_carth_val_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_carth_val_dia"]:$this->rh16_carth_val_dia);
         $this->rh16_carth_val_mes = ($this->rh16_carth_val_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_carth_val_mes"]:$this->rh16_carth_val_mes);
         $this->rh16_carth_val_ano = ($this->rh16_carth_val_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_carth_val_ano"]:$this->rh16_carth_val_ano);
         if($this->rh16_carth_val_dia != ""){
            $this->rh16_carth_val = $this->rh16_carth_val_ano."-".$this->rh16_carth_val_mes."-".$this->rh16_carth_val_dia;
         }
       }
       if($this->rh16_emissao == ""){
         $this->rh16_emissao_dia = ($this->rh16_emissao_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_emissao_dia"]:$this->rh16_emissao_dia);
         $this->rh16_emissao_mes = ($this->rh16_emissao_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_emissao_mes"]:$this->rh16_emissao_mes);
         $this->rh16_emissao_ano = ($this->rh16_emissao_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_emissao_ano"]:$this->rh16_emissao_ano);
         if($this->rh16_emissao_dia != ""){
            $this->rh16_emissao = $this->rh16_emissao_ano."-".$this->rh16_emissao_mes."-".$this->rh16_emissao_dia;
         }
       }
       if($this->rh16_data_emissao_cnh == ""){
        $this->rh16_data_emissao_cnh_dia = ($this->rh16_data_emissao_cnh_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_cnh_dia"]:$this->rh16_data_emissao_cnh_dia);
        $this->rh16_data_emissao_cnh_mes = ($this->rh16_data_emissao_cnh_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_cnh_mes"]:$this->rh16_data_emissao_cnh_mes);
        $this->rh16_data_emissao_cnh_ano = ($this->rh16_data_emissao_cnh_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_cnh_ano"]:$this->rh16_data_emissao_cnh_ano);
        if($this->rh16_data_emissao_cnh_dia != ""){
           $this->rh16_data_emissao_cnh = $this->rh16_data_emissao_cnh_ano."-".$this->rh16_data_emissao_cnh_mes."-".$this->rh16_data_emissao_cnh_dia;
        }
      }
      if($this->rh16_data_orgao_classe == ""){
        $this->rh16_data_orgao_classe_dia = ($this->rh16_data_orgao_classe_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_orgao_classe_dia"]:$this->rh16_data_orgao_classe_dia);
        $this->rh16_data_orgao_classe_mes = ($this->rh16_data_orgao_classe_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_orgao_classe_mes"]:$this->rh16_data_orgao_classe_mes);
        $this->rh16_data_orgao_classe_ano = ($this->rh16_data_orgao_classe_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_orgao_classe_ano"]:$this->rh16_data_orgao_classe_ano);
        if($this->rh16_data_orgao_classe_dia != ""){
           $this->rh16_data_orgao_classe = $this->rh16_data_orgao_classe_ano."-".$this->rh16_data_orgao_classe_mes."-".$this->rh16_data_orgao_classe_dia;
        }
      }
      if($this->rh16_data_emissao_rne == ""){
        $this->rh16_data_emissao_rne_dia = ($this->rh16_data_emissao_rne_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_rne_dia"]:$this->rh16_data_emissao_rne_dia);
        $this->rh16_data_emissao_rne_mes = ($this->rh16_data_emissao_rne_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_rne_mes"]:$this->rh16_data_emissao_rne_mes);
        $this->rh16_data_emissao_rne_ano = ($this->rh16_data_emissao_rne_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_rne_ano"]:$this->rh16_data_emissao_rne_ano);
        if($this->rh16_data_emissao_rne_dia != ""){
           $this->rh16_data_emissao_rne = $this->rh16_data_emissao_rne_ano."-".$this->rh16_data_emissao_rne_mes."-".$this->rh16_data_emissao_rne_dia;
        }
      }
      if($this->rh16_data_entrada_rne == ""){
        $this->rh16_data_entrada_rne_dia = ($this->rh16_data_entrada_rne_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_entrada_rne_dia"]:$this->rh16_data_entrada_rne_dia);
        $this->rh16_data_entrada_rne_mes = ($this->rh16_data_entrada_rne_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_entrada_rne_mes"]:$this->rh16_data_entrada_rne_mes);
        $this->rh16_data_entrada_rne_ano = ($this->rh16_data_entrada_rne_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_entrada_rne_ano"]:$this->rh16_data_entrada_rne_ano);
        if($this->rh16_data_entrada_rne_dia != ""){
           $this->rh16_data_entrada_rne = $this->rh16_data_entrada_rne_ano."-".$this->rh16_data_entrada_rne_mes."-".$this->rh16_data_entrada_rne_dia;
        }
      }
      if($this->rh16_data_validade_rne == ""){
        $this->rh16_data_validade_rne_dia = ($this->rh16_data_validade_rne_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_rne_dia"]:$this->rh16_data_validade_rne_dia);
        $this->rh16_data_validade_rne_mes = ($this->rh16_data_validade_rne_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_rne_mes"]:$this->rh16_data_validade_rne_mes);
        $this->rh16_data_validade_rne_ano = ($this->rh16_data_validade_rne_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_rne_ano"]:$this->rh16_data_validade_rne_ano);
        if($this->rh16_data_validade_rne_dia != ""){
           $this->rh16_data_validade_rne = $this->rh16_data_validade_rne_ano."-".$this->rh16_data_validade_rne_mes."-".$this->rh16_data_validade_rne_dia;
        }
      }

            if ($this->rh16_data_validade_orgao_classe == "") {
                $this->rh16_data_validade_orgao_classe_dia = ($this->rh16_data_validade_orgao_classe_dia == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_orgao_classe_dia"] : $this->rh16_data_validade_orgao_classe_dia);
                $this->rh16_data_validade_orgao_classe_mes = ($this->rh16_data_validade_orgao_classe_mes == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_orgao_classe_mes"] : $this->rh16_data_validade_orgao_classe_mes);
                $this->rh16_data_validade_orgao_classe_ano = ($this->rh16_data_validade_orgao_classe_ano == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_orgao_classe_ano"] : $this->rh16_data_validade_orgao_classe_ano);
                if ($this->rh16_data_validade_orgao_classe_dia != "") {
                    $this->rh16_data_validade_orgao_classe = $this->rh16_data_validade_orgao_classe_ano . "-" . $this->rh16_data_validade_orgao_classe_mes . "-" . $this->rh16_data_validade_orgao_classe_dia;
                }
            }
        } else {
            $this->rh16_regist = ($this->rh16_regist == "" ? @$GLOBALS["HTTP_POST_VARS"]["rh16_regist"] : $this->rh16_regist);
        }
    }

    // funcao para inclusao
    public function incluir ($rh16_regist){
      $this->atualizacampos();
     if($this->rh16_ctps_n == null ){
       $this->rh16_ctps_n = "0";
     }
     if($this->rh16_ctps_s == null ){
       $this->rh16_ctps_s = "0";
     }
     if($this->rh16_ctps_d == null ){
       $this->rh16_ctps_d = "0";
     }
     if($this->rh16_carth_n == null ){
       $this->rh16_carth_n = "";
     }
     if($this->rh16_carth_val == null ){
       $this->rh16_carth_val = "null";
     }
     if($this->rh16_emissao == null ){
       $this->rh16_emissao = "null";
     }
     if($this->rh16_data_emissao_cnh == null ){
      $this->rh16_data_emissao_cnh = "null";
    }
    if($this->rh16_orgao_classe == null ){
      $this->rh16_orgao_classe = "0";
    }
    if($this->rh16_data_orgao_classe == null ){
      $this->rh16_data_orgao_classe = "null";
    }
    if($this->rh16_orgao_emissor_classe == null ){
      $this->rh16_orgao_emissor_classe = "0";
    }
    if($this->rh16_orgao_emissor_rne == null ){
      $this->rh16_orgao_emissor_rne = "0";
    }
    if($this->rh16_data_emissao_rne == null ){
      $this->rh16_data_emissao_rne = "null";
    }
    if($this->rh16_data_entrada_rne == null ){
      $this->rh16_data_entrada_rne = "null";
    }
    if($this->rh16_data_validade_rne == null ){
      $this->rh16_data_validade_rne = "null";
    }
    if($this->rh16_uf_chn == null ){
      $this->rh16_uf_chn = "0";
    }
    if($this->rh16_data_validade_orgao_classe == null ){
      $this->rh16_data_validade_orgao_classe = "null";
    }
    if($this->rh16_registro_rne == null ){
      $this->rh16_registro_rne = "null";
    }

        $this->rh16_regist = $rh16_regist;
        if (($this->rh16_regist == null) || ($this->rh16_regist == "")) {
            $this->erro_sql = " Campo rh16_regist nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into rhpesdoc(
                                       rh16_regist
                                      ,rh16_titele
                                      ,rh16_zonael
                                      ,rh16_secaoe
                                      ,rh16_reserv
                                      ,rh16_catres
                                      ,rh16_ctps_n
                                      ,rh16_ctps_s
                                      ,rh16_ctps_d
                                      ,rh16_ctps_uf
                                      ,rh16_pis
                                      ,rh16_carth_n
                                      ,r16_carth_cat
                                      ,rh16_carth_val
                                      ,rh16_emissao
                                      ,rh16_data_emissao_cnh
                                      ,rh16_orgao_classe
                                      ,rh16_data_orgao_classe
                                      ,rh16_orgao_emissor_classe
                                      ,rh16_orgao_emissor_rne
                                      ,rh16_data_emissao_rne
                                      ,rh16_data_entrada_rne
                                      ,rh16_data_validade_rne
                                      ,rh16_uf_cnh
                                      ,rh16_data_validade_orgao_classe
                                      ,rh16_registro_rne
                       )
                values (
                                 $this->rh16_regist
                               ,'$this->rh16_titele'
                               ,'$this->rh16_zonael'
                               ,'$this->rh16_secaoe'
                               ,'$this->rh16_reserv'
                               ,'$this->rh16_catres'
                                ,'$this->rh16_ctps_n'
                                ,$this->rh16_ctps_s
                                ,$this->rh16_ctps_d
                               ,'$this->rh16_ctps_uf'
                               ,'$this->rh16_pis'
                                ,'$this->rh16_carth_n'
                               ,'$this->r16_carth_cat'
                               ," . ($this->rh16_carth_val == "null" || $this->rh16_carth_val == "" ? "null" : "'" . $this->rh16_carth_val . "'") . "
                               ," . ($this->rh16_emissao == "null" || $this->rh16_emissao == "" ? "null" : "'" . $this->rh16_emissao . "'") . "
                               ," . ($this->rh16_data_emissao_cnh == "null" || $this->rh16_data_emissao_cnh == "" ? "null" : "'" . $this->rh16_data_emissao_cnh . "'") . "
                               ,'$this->rh16_orgao_classe'
                               ," . ($this->rh16_data_orgao_classe == "null" || $this->rh16_data_orgao_classe == "" ? "null" : "'" . $this->rh16_data_orgao_classe . "'") . "
                               ,'$this->rh16_orgao_emissor_classe'
                               ,'$this->rh16_orgao_emissor_rne'
                               ," . ($this->rh16_data_emissao_rne == "null" || $this->rh16_data_emissao_rne == "" ? "null" : "'" . $this->rh16_data_emissao_rne . "'") . "
                               ," . ($this->rh16_data_entrada_rne == "null" || $this->rh16_data_entrada_rne == "" ? "null" : "'" . $this->rh16_data_entrada_rne . "'") . "
                               ," . ($this->rh16_data_validade_rne == "null" || $this->rh16_data_validade_rne == "" ? "null" : "'" . $this->rh16_data_validade_rne . "'") . "
                               ,'$this->rh16_uf_cnh'
                               ," . ($this->rh16_data_validade_orgao_classe == "null" || $this->rh16_data_validade_orgao_classe == "" ? "null" : "'" . $this->rh16_data_validade_orgao_classe . "'") . "
                               ,'$this->rh16_registro_rne'
                      )";
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "Documentos dos funcionários ($this->rh16_regist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_banco = "Documentos dos funcionários já Cadastrado";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            } else {
                $this->erro_sql = "Documentos dos funcionários ($this->rh16_regist) nao Incluído. Inclusao Abortada.";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir = 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : " . $this->rh16_regist;
        $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
        $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir = pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            $resaco = $this->sql_record($this->sql_query_file($this->rh16_regist));
            if (($resaco != false) || ($this->numrows != 0)) {
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_fetch_result($resac, 0, 0);
                $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                $resac = db_query("insert into db_acountkey values($acount,7070,'$this->rh16_regist','I')");
                $resac = db_query("insert into db_acount values($acount,1168,7070,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_regist')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7071,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_titele')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7072,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_zonael')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7073,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_secaoe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7074,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_reserv')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7075,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_catres')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7076,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_ctps_n')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7077,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_ctps_s')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7078,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_ctps_d')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7079,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_ctps_uf')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7080,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_pis')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7081,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_carth_n')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7082,'','" . addSlashes(pg_fetch_result($resaco, 0, 'r16_carth_cat')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,7083,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_carth_val')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,20252,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_emissao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013775,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_data_emissao_cnh')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013776,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_orgao_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013777,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_data_orgao_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013778,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_orgao_emissor_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013779,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_orgao_emissor_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013780,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_data_emissao_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013781,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_data_entrada_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013782,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_data_validade_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013792,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_uf_cnh')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013793,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_data_validade_orgao_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                $resac = db_query("insert into db_acount values($acount,1168,1013794,'','" . addSlashes(pg_fetch_result($resaco, 0, 'rh16_registro_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
            }
        }
        return true;
    }

    // funcao para alteracao
    public function alterar ($rh16_regist=null) {
      $this->atualizacampos();
     $sql = " update rhpesdoc set ";
     $virgula = "";
     if(trim($this->rh16_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_regist"])){
       $sql  .= $virgula." rh16_regist = $this->rh16_regist ";
       $virgula = ",";
       if(trim($this->rh16_regist) == null ){
         $this->erro_sql = " Campo Matrícula não informado.";
         $this->erro_campo = "rh16_regist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->rh16_titele)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_titele"])){
       $sql  .= $virgula." rh16_titele = '$this->rh16_titele' ";
       $virgula = ",";
     }
     if(trim($this->rh16_zonael)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_zonael"])){
       $sql  .= $virgula." rh16_zonael = '$this->rh16_zonael' ";
       $virgula = ",";
     }
     if(trim($this->rh16_secaoe)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_secaoe"])){
       $sql  .= $virgula." rh16_secaoe = '$this->rh16_secaoe' ";
       $virgula = ",";
     }
     if(trim($this->rh16_reserv)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_reserv"])){
       $sql  .= $virgula." rh16_reserv = '$this->rh16_reserv' ";
       $virgula = ",";
     }
     if(trim($this->rh16_catres)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_catres"])){
       $sql  .= $virgula." rh16_catres = '$this->rh16_catres' ";
       $virgula = ",";
     }
     if(trim($this->rh16_ctps_n)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_n"])){
        if(trim($this->rh16_ctps_n)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_n"])){
           $this->rh16_ctps_n = "0" ;
        }
       $sql  .= $virgula." rh16_ctps_n = '$this->rh16_ctps_n' ";
       $virgula = ",";
     }
     if(trim($this->rh16_ctps_s)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_s"])){
        if(trim($this->rh16_ctps_s)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_s"])){
           $this->rh16_ctps_s = "0" ;
        }
       $sql  .= $virgula." rh16_ctps_s = $this->rh16_ctps_s ";
       $virgula = ",";
     }
     if(trim($this->rh16_ctps_d)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_d"])){
        if(trim($this->rh16_ctps_d)=="" && isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_d"])){
           $this->rh16_ctps_d = "0" ;
        }
       $sql  .= $virgula." rh16_ctps_d = $this->rh16_ctps_d ";
       $virgula = ",";
     }
     if(trim($this->rh16_ctps_uf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_uf"])){
       $sql  .= $virgula." rh16_ctps_uf = '$this->rh16_ctps_uf' ";
       $virgula = ",";
     }
     if(trim($this->rh16_pis)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_pis"])){
       $sql  .= $virgula." rh16_pis = '$this->rh16_pis' ";
       $virgula = ",";
     }
     if(trim($this->rh16_carth_n)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_carth_n"])){
       $sql  .= $virgula." rh16_carth_n = '$this->rh16_carth_n' ";
       $virgula = ",";
     }
     if(trim($this->r16_carth_cat)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r16_carth_cat"])){
       $sql  .= $virgula." r16_carth_cat = '$this->r16_carth_cat' ";
       $virgula = ",";
     }
     if(trim($this->rh16_carth_val)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_carth_val_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_carth_val_dia"] !="") ){
       $sql  .= $virgula." rh16_carth_val = '$this->rh16_carth_val' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_carth_val_dia"])){
         $sql  .= $virgula." rh16_carth_val = null ";
         $virgula = ",";
       }
     }
     if(trim($this->rh16_emissao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_emissao_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_emissao_dia"] !="") ){
       $sql  .= $virgula." rh16_emissao = '$this->rh16_emissao' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_emissao_dia"])){
         $sql  .= $virgula." rh16_emissao = null ";
         $virgula = ",";
       }
     }
     if(trim($this->rh16_data_emissao_cnh)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_cnh_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_cnh_dia"] !="") ){
      $sql  .= $virgula." rh16_data_emissao_cnh = '$this->rh16_data_emissao_cnh' ";
      $virgula = ",";
    }     else{
      if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_cnh_dia"])){
        $sql  .= $virgula." rh16_data_emissao_cnh = null ";
        $virgula = ",";
      }
    }
    if(trim($this->rh16_orgao_classe)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_orgao_classe"])){
      $sql  .= $virgula." rh16_orgao_classe = '$this->rh16_orgao_classe' ";
      $virgula = ",";
    }
    if(trim($this->rh16_data_orgao_classe)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_orgao_classe_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_data_orgao_classe_dia"] !="") ){
      $sql  .= $virgula." rh16_data_orgao_classe = '$this->rh16_data_orgao_classe' ";
      $virgula = ",";
    }     else{
      if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_orgao_classe_dia"])){
        $sql  .= $virgula." rh16_data_orgao_classe = null ";
        $virgula = ",";
      }
    }
    if(trim($this->rh16_orgao_emissor_classe)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_orgao_emissor_classe"])){
      $sql  .= $virgula." rh16_orgao_emissor_classe = '$this->rh16_orgao_emissor_classe' ";
      $virgula = ",";
    }
    if(trim($this->rh16_orgao_emissor_rne)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_orgao_emissor_rne"])){
      $sql  .= $virgula." rh16_orgao_emissor_rne = '$this->rh16_orgao_emissor_rne' ";
      $virgula = ",";
    }
    if(trim($this->rh16_data_emissao_rne)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_rne_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_rne_dia"] !="") ){
      $sql  .= $virgula." rh16_data_emissao_rne = '$this->rh16_data_emissao_rne' ";
      $virgula = ",";
    }     else{
      if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_rne_dia"])){
        $sql  .= $virgula." rh16_data_emissao_rne = null ";
        $virgula = ",";
      }
    }
    if(trim($this->rh16_data_entrada_rne)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_entrada_rne_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_data_entrada_rne_dia"] !="") ){
      $sql  .= $virgula." rh16_data_entrada_rne = '$this->rh16_data_entrada_rne' ";
      $virgula = ",";
    }     else{
      if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_entrada_rne_dia"])){
        $sql  .= $virgula." rh16_data_entrada_rne = null ";
        $virgula = ",";
      }
    }
    if(trim($this->rh16_data_validade_rne)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_rne_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_rne_dia"] !="") ){
      $sql  .= $virgula." rh16_data_validade_rne = '$this->rh16_data_validade_rne' ";
      $virgula = ",";
    }     else{
      if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_rne_dia"])){
        $sql  .= $virgula." rh16_data_validade_rne = null ";
        $virgula = ",";
      }
    }
    if(trim($this->rh16_uf_cnh)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_uf_cnh"])){
      $sql  .= $virgula." rh16_uf_cnh = '$this->rh16_uf_cnh' ";
      $virgula = ",";
    }
    if(trim($this->rh16_data_validade_orgao_classe)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_orgao_classe_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_orgao_classe_dia"] !="") ){
      $sql  .= $virgula." rh16_data_validade_orgao_classe = '$this->rh16_data_validade_orgao_classe' ";
      $virgula = ",";
    }     else{
      if(isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_orgao_classe_dia"])){
        $sql  .= $virgula." rh16_data_validade_orgao_classe = null ";
        $virgula = ",";
      }
    }
    if(trim($this->rh16_registro_rne)!="" || isset($GLOBALS["HTTP_POST_VARS"]["rh16_registro_rne"])){
      $sql  .= $virgula." rh16_registro_rne = '$this->rh16_registro_rne' ";
      $virgula = ",";
    }
     $sql .= " where ";
     if($rh16_regist!=null){
       $sql .= " rh16_regist = $this->rh16_regist";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->rh16_regist));
       if($this->numrows>0){

         for($conresaco=0;$conresaco<$this->numrows;$conresaco++){

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,7070,'$this->rh16_regist','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_regist"]) || $this->rh16_regist != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7070,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_regist')) . "','$this->rh16_regist'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_titele"]) || $this->rh16_titele != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7071,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_titele')) . "','$this->rh16_titele'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_zonael"]) || $this->rh16_zonael != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7072,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_zonael')) . "','$this->rh16_zonael'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_secaoe"]) || $this->rh16_secaoe != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7073,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_secaoe')) . "','$this->rh16_secaoe'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_reserv"]) || $this->rh16_reserv != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7074,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_reserv')) . "','$this->rh16_reserv'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_catres"]) || $this->rh16_catres != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7075,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_catres')) . "','$this->rh16_catres'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_n"]) || $this->rh16_ctps_n != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7076,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_ctps_n')) . "','$this->rh16_ctps_n'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_s"]) || $this->rh16_ctps_s != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7077,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_ctps_s')) . "','$this->rh16_ctps_s'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_d"]) || $this->rh16_ctps_d != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7078,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_ctps_d')) . "','$this->rh16_ctps_d'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_ctps_uf"]) || $this->rh16_ctps_uf != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7079,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_ctps_uf')) . "','$this->rh16_ctps_uf'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_pis"]) || $this->rh16_pis != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7080,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_pis')) . "','$this->rh16_pis'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_carth_n"]) || $this->rh16_carth_n != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7081,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_carth_n')) . "','$this->rh16_carth_n'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["r16_carth_cat"]) || $this->r16_carth_cat != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7082,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'r16_carth_cat')) . "','$this->r16_carth_cat'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_carth_val"]) || $this->rh16_carth_val != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,7083,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_carth_val')) . "','$this->rh16_carth_val'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_emissao"]) || $this->rh16_emissao != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,20252,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_emissao')) . "','$this->rh16_emissao'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_cnh"]) || $this->rh16_data_emissao_cnh != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013775,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_data_emissao_cnh')) . "','$this->rh16_data_emissao_cnh'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_orgao_classe"]) || $this->rh16_orgao_classe != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013776,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_orgao_classe')) . "','$this->rh16_orgao_classe'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_orgao_classe"]) || $this->rh16_data_orgao_classe != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013777,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_data_orgao_classe')) . "','$this->rh16_data_orgao_classe'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_orgao_emissor_classe"]) || $this->rh16_orgao_emissor_classe != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013778,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_orgao_emissor_classe')) . "','$this->rh16_orgao_emissor_classe'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_orgao_emissor_rne"]) || $this->rh16_orgao_emissor_rne != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013779,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_orgao_emissor_rne')) . "','$this->rh16_orgao_emissor_rne'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_emissao_rne"]) || $this->rh16_data_emissao_rne != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013780,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_data_emissao_rne')) . "','$this->rh16_data_emissao_rne'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_entrada_rne"]) || $this->rh16_data_entrada_rne != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013781,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_data_entrada_rne')) . "','$this->rh16_data_entrada_rne'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_rne"]) || $this->rh16_data_validade_rne != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013782,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_data_validade_rne')) . "','$this->rh16_data_validade_rne'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_uf_cnh"]) || $this->rh16_uf_cnh != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013792,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_uf_cnh')) . "','$this->rh16_uf_cnh'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_data_validade_orgao_classe"]) || $this->rh16_data_validade_orgao_classe != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013793,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_data_validade_orgao_classe')) . "','$this->rh16_data_validade_orgao_classe'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                    if (isset($GLOBALS["HTTP_POST_VARS"]["rh16_regsitro_rne"]) || $this->rh16_regsitro_rne != "") {
                        $resac = db_query("insert into db_acount values($acount,1168,1013794,'" . addSlashes(pg_fetch_result($resaco, $conresaco, 'rh16_regsitro_rne')) . "','$this->rh16_regsitro_rne'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    }
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql = "Documentos dos funcionários nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : " . $this->rh16_regist;
            $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
            $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Documentos dos funcionários nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : " . $this->rh16_regist;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : " . $this->rh16_regist;
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .= str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
       }
     }

   // funcao para exclusao
   public function excluir ($rh16_regist=null,$dbwhere=null) {

        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {
            if ($dbwhere == null || $dbwhere == "") {
                $resaco = $this->sql_record($this->sql_query_file($rh16_regist));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null, "*", null, $dbwhere));
            }
            if ($resaco || ($this->numrows != 0)) {
                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {
                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_fetch_result($resac, 0, 0);
                    $resac = db_query("insert into db_acountacesso values($acount," . db_getsession("DB_acessado") . ")");
                    $resac = db_query("insert into db_acountkey values($acount,7070,'$rh16_regist','E')");
                    $resac = db_query("insert into db_acount values($acount,1168,7070,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_regist')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7071,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_titele')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7072,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_zonael')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7073,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_secaoe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7074,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_reserv')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7075,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_catres')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7076,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_ctps_n')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7077,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_ctps_s')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7078,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_ctps_d')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7079,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_ctps_uf')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7080,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_pis')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7081,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_carth_n')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7082,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'r16_carth_cat')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,7083,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_carth_val')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,20252,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_emissao')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013775,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_data_emissao_cnh')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013776,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_orgao_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013777,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_data_orgao_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013778,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_orgao_emissor_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013779,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_orgao_emissor_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013780,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_data_emissao_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013781,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_data_entrada_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013782,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_data_validade_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013792,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_uf_cnh')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013793,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_data_validade_orgao_classe')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                    $resac = db_query("insert into db_acount values($acount,1168,1013794,'','" . addSlashes(pg_fetch_result($resaco, $iresaco, 'rh16_data_registro_rne')) . "'," . db_getsession('DB_datausu') . "," . db_getsession('DB_id_usuario') . ")");
                }
            }
        }
        $sql = " delete from rhpesdoc
                    where ";
        $sql2 = "";
        if ($dbwhere == null || $dbwhere == "") {
            if ($rh16_regist != "") {
                if ($sql2 != "") {
                    $sql2 .= " and ";
                }
                $sql2 .= " rh16_regist = $rh16_regist ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql . $sql2);
        if (!$result){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Documentos dos funcionários nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$rh16_regist;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Documentos dos funcionários nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$rh16_regist;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$rh16_regist;
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
     if(!$result){
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
        $this->erro_sql   = "Record Vazio na Tabela:rhpesdoc";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $rh16_regist=null,$campos="*",$ordem=null,$dbwhere="")
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
        $sql .= " from rhpesdoc ";
        $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = rhpesdoc.rh16_regist";
        $sql .= "      inner join rhpessoalmov   on  rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
		                                         and  rhpessoalmov.rh02_anousu = " . db_anofolha() . "
																						 and  rhpessoalmov.rh02_mesusu = " . db_mesfolha() . "
																						 and  rhpessoalmov.rh02_instit = " . db_getsession("DB_instit") . " ";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
        $sql .= "      inner join rhlota  on  rhlota.r70_codigo = rhpessoalmov.rh02_lota
		                                  and  rhlota.r70_instit = rhpessoalmov.rh02_instit ";
        $sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
        $sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
        $sql .= "      inner join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao
		                                    and  rhfuncao.rh37_instit = rhpessoalmov.rh02_instit ";
        $sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
        $sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($rh16_regist != null) {
                $sql2 .= " where rhpesdoc.rh16_regist = $rh16_regist ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
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
   public function sql_query_file ( $rh16_regist=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from rhpesdoc ";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($rh16_regist != null) {
                $sql2 .= " where rhpesdoc.rh16_regist = $rh16_regist ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
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
   public function sql_query_gerfs($rh16_regist = null, $campos = "*", $ordem = null, $dbwhere = "", $sigla, $pis)
    {
        $sql = "select ";
        $campos = str_replace("#s#", $sigla, $campos);
        $campos = str_replace("#S#", $sigla, $campos);
        $dbwhere = str_replace("#s#", $sigla, $dbwhere);
        $dbwhere = str_replace("#S#", $sigla, $dbwhere);
        $ordem = str_replace("#s#", $sigla, $ordem);
        $ordem = str_replace("#S#", $sigla, $ordem);
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

        if ($sigla == 'r14') {
            $arquivo = ' gerfsal';
        } elseif ($sigla == 'r20') {
            $arquivo = ' gerfres';
        } elseif ($sigla == 'r35') {
            $arquivo = ' gerfs13';
        } elseif ($sigla == 'r22') {
            $arquivo = ' gerfadi';
        } elseif ($sigla == 'r48') {
            $arquivo = ' gerfcom';
        } elseif ($sigla == 'r53') {
            $arquivo = ' gerffx';
        } elseif ($sigla == 'r31') {
            $arquivo = ' gerffer';
        }

        $sql .= " from " . $arquivo;
        $sql .= "      inner join rhpesdoc      on rhpesdoc.rh16_regist = " . $arquivo . "." . $sigla . "_regist
                                            and rhpesdoc.rh16_pis = '" . $pis . "'";
        $sql .= "      inner join rhpessoalmov  on rhpessoalmov.rh02_anousu = " . $arquivo . "." . $sigla . "_anousu
                               					    and rhpessoalmov.rh02_mesusu = " . $arquivo . "." . $sigla . "_mesusu
                                            and rhpessoalmov.rh02_regist = rhpesdoc.rh16_regist
																						and rhpessoalmov.rh02_instit = " . db_getsession("DB_instit") . " ";

        $sql2 = "";
        if ($dbwhere == "") {
            if ($rh16_regist != null) {
                $sql2 .= " where rhpesdoc.rh16_regist = $rh16_regist ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
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

    public function sql_query_pessoal ( $rh16_regist=null,$campos="*",$ordem=null,$dbwhere=""){
    $sql = "select ";
    if($campos != "*" ){
      $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i = 0; $i < sizeof($campos_sql); $i++) {
                $sql .= $virgula . $campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from rhpesdoc ";
        $sql .= "      inner join rhpessoal on rh01_regist = rh16_regist";
        $sql2 = "";
        if ($dbwhere == "") {
            if ($rh16_regist != null) {
                $sql2 .= " where rhpesdoc.rh16_regist = $rh16_regist ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
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

    public function sql_query_pessoal_res ( $rh16_regist=null,$campos="*",$ordem=null,$dbwhere="")
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


        $sql .= " from rhpesdoc ";
        $sql .= "      inner join rhpessoal on rh01_regist = rh16_regist ";

        $sql .= "      inner join rhpessoalmov  on rhpessoalmov.rh02_anousu = " . DBPessoal::getCompetenciaFolha()->getAno() . "
                               					    and rhpessoalmov.rh02_mesusu = " . DBPessoal::getCompetenciaFolha()->getMes() . "
                                            and rhpessoalmov.rh02_regist = rhpesdoc.rh16_regist
																						and rhpessoalmov.rh02_instit = " . db_getsession("DB_instit") . " ";

        $sql .= "      left join rhpesrescisao on rh05_seqpes = rh02_seqpes ";


        $sql2 = " where rh05_seqpes is null ";

        if ($dbwhere == "") {
            if ($rh16_regist != null) {
                $sql2 .= " and rhpesdoc.rh16_regist = $rh16_regist ";
            }
        } elseif ($dbwhere != "") {
            $sql2 .= " and $dbwhere ";
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
