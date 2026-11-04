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

class cl_edu_relatmodel
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
    public $ed217_i_codigo = 0; 
    public $ed217_i_relatorio = 0; 
    public $ed217_c_nome = null; 
    public $ed217_t_cabecalho = null; 
    public $ed217_t_rodape = null; 
    public $ed217_t_obs = null; 
    public $ed217_orientacao = 0; 
    public $ed217_gradenotas = 0; 
    public $ed217_gradeetapas = 0; 
    public $ed217_observacao = 0; 
    public $ed217_i_tipomodelo = 0; 
    public $ed217_exibeturma = 'f'; 
    public $ed217_exibecargahoraria = 'f'; 
    public $ed217_brasao = 0; 
    public $ed217_exibe_obs_diario = 'f'; 
    public $ed217_exibirmantenedora = 'f'; 
    public $ed217_exibirdistrito = 'f'; 
    public $ed217_exibirperiodo = 'f'; 
    public $ed217_exibir_etapa_obs = 'f'; 
    public $ed217_exibircertidao = 'f'; 
    public $ed217_exibiridentidade = 'f'; 
    public $ed217_exibir_assinatura_secretario = 'f'; 
    public $ed217_exibir_assinatura_adicional = 'f'; 
    public $ed217_exibir_grade_alunos = 'f'; 
    public $ed217_exibir_coluna_disciplinas = 'f'; 
    public $ed217_exibir_coluna_resultado_final = 'f'; 
    public $ed217_exibir_brasao = 'f'; 
    public $ed217_disposicao_brasao = 'LADO'; 
    public $ed217_disposicao_cabecalho = 'ESQUERDA'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 ed217_i_codigo = int4 = Código 
                 ed217_i_relatorio = int4 = Tipo de Relatório 
                 ed217_c_nome = char(20) = Nome do Modelo 
                 ed217_t_cabecalho = text = Texto do Cabeçalho 
                 ed217_t_rodape = text = Texto do Rodapé 
                 ed217_t_obs = text = Observações Gerais a Todos os Alunos 
                 ed217_orientacao = int4 = Orientação 
                 ed217_gradenotas = int4 = Grade 1 
                 ed217_gradeetapas = int4 = Grade 2 
                 ed217_observacao = int4 = Observações 
                 ed217_i_tipomodelo = int4 = Tipo do Modelo 
                 ed217_exibeturma = bool = Exibe Turma 
                 ed217_exibecargahoraria = bool = Exibir Porcentagem da Frequência 
                 ed217_brasao = int4 = Brasão 
                 ed217_exibe_obs_diario = bool = Exibe Observação do Diário 
                 ed217_exibirmantenedora = bool = Exibir Mantenedora 
                 ed217_exibirdistrito = bool = Exibir Distrito 
                 ed217_exibirperiodo = bool = Exibir Coluna Período 
                 ed217_exibir_etapa_obs = bool = Exibir Etapa na Observação 
                 ed217_exibircertidao = bool = Exibir informações da certidao de nasc. 
                 ed217_exibiridentidade = bool = Exibir informações do RG 
                 ed217_exibir_assinatura_secretario = bool = Exibir Assinatura Secretário 
                 ed217_exibir_assinatura_adicional = bool = Exibir Assinatura Adicional 
                 ed217_exibir_grade_alunos = bool = Exibir Grade Alunos 
                 ed217_exibir_coluna_disciplinas = bool = Exibir Coluna Disciplinas 
                 ed217_exibir_coluna_resultado_final = bool = Exibir Coluna Resultado Final 
                 ed217_exibir_brasao = bool = Exibir Brasão 
                 ed217_disposicao_brasao = char(20) = Disposição Brasão 
                 ed217_disposicao_cabecalho = char(20) = Disposição Cabeçalho 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("edu_relatmodel"); 
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
       $this->ed217_i_codigo = ($this->ed217_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_i_codigo"]:$this->ed217_i_codigo);
       $this->ed217_i_relatorio = ($this->ed217_i_relatorio == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_i_relatorio"]:$this->ed217_i_relatorio);
       $this->ed217_c_nome = ($this->ed217_c_nome == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_c_nome"]:$this->ed217_c_nome);
       $this->ed217_t_cabecalho = ($this->ed217_t_cabecalho == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_t_cabecalho"]:$this->ed217_t_cabecalho);
       $this->ed217_t_rodape = ($this->ed217_t_rodape == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_t_rodape"]:$this->ed217_t_rodape);
       $this->ed217_t_obs = ($this->ed217_t_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_t_obs"]:$this->ed217_t_obs);
       $this->ed217_orientacao = ($this->ed217_orientacao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_orientacao"]:$this->ed217_orientacao);
       $this->ed217_gradenotas = ($this->ed217_gradenotas == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_gradenotas"]:$this->ed217_gradenotas);
       $this->ed217_gradeetapas = ($this->ed217_gradeetapas == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_gradeetapas"]:$this->ed217_gradeetapas);
       $this->ed217_observacao = ($this->ed217_observacao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_observacao"]:$this->ed217_observacao);
       $this->ed217_i_tipomodelo = ($this->ed217_i_tipomodelo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_i_tipomodelo"]:$this->ed217_i_tipomodelo);
       $this->ed217_exibeturma = ($this->ed217_exibeturma == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibeturma"]:$this->ed217_exibeturma);
       $this->ed217_exibecargahoraria = ($this->ed217_exibecargahoraria == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibecargahoraria"]:$this->ed217_exibecargahoraria);
       $this->ed217_brasao = ($this->ed217_brasao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_brasao"]:$this->ed217_brasao);
       $this->ed217_exibe_obs_diario = ($this->ed217_exibe_obs_diario == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibe_obs_diario"]:$this->ed217_exibe_obs_diario);
       $this->ed217_exibirmantenedora = ($this->ed217_exibirmantenedora == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibirmantenedora"]:$this->ed217_exibirmantenedora);
       $this->ed217_exibirdistrito = ($this->ed217_exibirdistrito == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibirdistrito"]:$this->ed217_exibirdistrito);
       $this->ed217_exibirperiodo = ($this->ed217_exibirperiodo == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibirperiodo"]:$this->ed217_exibirperiodo);
       $this->ed217_exibir_etapa_obs = ($this->ed217_exibir_etapa_obs == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibir_etapa_obs"]:$this->ed217_exibir_etapa_obs);
       $this->ed217_exibircertidao = ($this->ed217_exibircertidao == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibircertidao"]:$this->ed217_exibircertidao);
       $this->ed217_exibiridentidade = ($this->ed217_exibiridentidade == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibiridentidade"]:$this->ed217_exibiridentidade);
       $this->ed217_exibir_assinatura_secretario = ($this->ed217_exibir_assinatura_secretario == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibir_assinatura_secretario"]:$this->ed217_exibir_assinatura_secretario);
       $this->ed217_exibir_assinatura_adicional = ($this->ed217_exibir_assinatura_adicional == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibir_assinatura_adicional"]:$this->ed217_exibir_assinatura_adicional);
       $this->ed217_exibir_grade_alunos = ($this->ed217_exibir_grade_alunos == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibir_grade_alunos"]:$this->ed217_exibir_grade_alunos);
       $this->ed217_exibir_coluna_disciplinas = ($this->ed217_exibir_coluna_disciplinas == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibir_coluna_disciplinas"]:$this->ed217_exibir_coluna_disciplinas);
       $this->ed217_exibir_coluna_resultado_final = ($this->ed217_exibir_coluna_resultado_final == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibir_coluna_resultado_final"]:$this->ed217_exibir_coluna_resultado_final);
       $this->ed217_exibir_brasao = ($this->ed217_exibir_brasao == "f"?@$GLOBALS["HTTP_POST_VARS"]["ed217_exibir_brasao"]:$this->ed217_exibir_brasao);
       $this->ed217_disposicao_brasao = ($this->ed217_disposicao_brasao == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_disposicao_brasao"]:$this->ed217_disposicao_brasao);
       $this->ed217_disposicao_cabecalho = ($this->ed217_disposicao_cabecalho == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_disposicao_cabecalho"]:$this->ed217_disposicao_cabecalho);
     }else{
       $this->ed217_i_codigo = ($this->ed217_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["ed217_i_codigo"]:$this->ed217_i_codigo);
     }
   }

    public function incluir($ed217_i_codigo)
    {
      $this->atualizacampos();
     if($this->ed217_i_relatorio == null ){ 
       $this->erro_sql = " Campo Tipo de Relatório não informado.";
       $this->erro_campo = "ed217_i_relatorio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_c_nome == null ){ 
       $this->erro_sql = " Campo Nome do Modelo não informado.";
       $this->erro_campo = "ed217_c_nome";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_orientacao == null ){ 
       $this->ed217_orientacao = "0";
     }
     if($this->ed217_gradenotas == null ){ 
       $this->ed217_gradenotas = "0";
     }
     if($this->ed217_gradeetapas == null ){ 
       $this->ed217_gradeetapas = "0";
     }
     if($this->ed217_observacao == null ){ 
       $this->ed217_observacao = "0";
     }
     if($this->ed217_i_tipomodelo == null ){ 
       $this->ed217_i_tipomodelo = "0";
     }
     if($this->ed217_exibeturma == null ){ 
       $this->erro_sql = " Campo Exibe Turma não informado.";
       $this->erro_campo = "ed217_exibeturma";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibecargahoraria == null ){ 
       $this->erro_sql = " Campo Exibir Porcentagem da Frequência não informado.";
       $this->erro_campo = "ed217_exibecargahoraria";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_brasao == null ){ 
       $this->erro_sql = " Campo Brasão não informado.";
       $this->erro_campo = "ed217_brasao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibe_obs_diario == null ){ 
       $this->erro_sql = " Campo Exibe Observação do Diário não informado.";
       $this->erro_campo = "ed217_exibe_obs_diario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibirmantenedora == null ){ 
       $this->erro_sql = " Campo Exibir Mantenedora não informado.";
       $this->erro_campo = "ed217_exibirmantenedora";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibirdistrito == null ){ 
       $this->erro_sql = " Campo Exibir Distrito não informado.";
       $this->erro_campo = "ed217_exibirdistrito";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibirperiodo == null ){ 
       $this->erro_sql = " Campo Exibir Coluna Período não informado.";
       $this->erro_campo = "ed217_exibirperiodo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibir_etapa_obs == null ){ 
       $this->erro_sql = " Campo Exibir Etapa na Observação não informado.";
       $this->erro_campo = "ed217_exibir_etapa_obs";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibircertidao == null ){ 
       $this->erro_sql = " Campo Exibir informações da certidao de nasc. não informado.";
       $this->erro_campo = "ed217_exibircertidao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibiridentidade == null ){ 
       $this->erro_sql = " Campo Exibir informações do RG não informado.";
       $this->erro_campo = "ed217_exibiridentidade";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibir_assinatura_secretario == null ){ 
       $this->erro_sql = " Campo Exibir Assinatura Secretário não informado.";
       $this->erro_campo = "ed217_exibir_assinatura_secretario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibir_assinatura_adicional == null ){ 
       $this->erro_sql = " Campo Exibir Assinatura Adicional não informado.";
       $this->erro_campo = "ed217_exibir_assinatura_adicional";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibir_grade_alunos == null ){ 
       $this->erro_sql = " Campo Exibir Grade Alunos não informado.";
       $this->erro_campo = "ed217_exibir_grade_alunos";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibir_coluna_disciplinas == null ){ 
       $this->erro_sql = " Campo Exibir Coluna Disciplinas não informado.";
       $this->erro_campo = "ed217_exibir_coluna_disciplinas";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibir_coluna_resultado_final == null ){ 
       $this->erro_sql = " Campo Exibir Coluna Resultado Final não informado.";
       $this->erro_campo = "ed217_exibir_coluna_resultado_final";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_exibir_brasao == null ){ 
       $this->erro_sql = " Campo Exibir Brasão não informado.";
       $this->erro_campo = "ed217_exibir_brasao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_disposicao_brasao == null ){ 
       $this->erro_sql = " Campo Disposição Brasão não informado.";
       $this->erro_campo = "ed217_disposicao_brasao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ed217_disposicao_cabecalho == null ){ 
       $this->erro_sql = " Campo Disposição Cabeçalho não informado.";
       $this->erro_campo = "ed217_disposicao_cabecalho";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ed217_i_codigo == "" || $ed217_i_codigo == null ){
       $result = db_query("select nextval('edu_relatmodel_ed217_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: edu_relatmodel_ed217_codigo_seq do campo: ed217_i_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->ed217_i_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from edu_relatmodel_ed217_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $ed217_i_codigo)){
         $this->erro_sql = " Campo ed217_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ed217_i_codigo = $ed217_i_codigo; 
       }
     }
     if(($this->ed217_i_codigo == null) || ($this->ed217_i_codigo == "") ){ 
       $this->erro_sql = " Campo ed217_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into edu_relatmodel(
                                       ed217_i_codigo 
                                      ,ed217_i_relatorio 
                                      ,ed217_c_nome 
                                      ,ed217_t_cabecalho 
                                      ,ed217_t_rodape 
                                      ,ed217_t_obs 
                                      ,ed217_orientacao 
                                      ,ed217_gradenotas 
                                      ,ed217_gradeetapas 
                                      ,ed217_observacao 
                                      ,ed217_i_tipomodelo 
                                      ,ed217_exibeturma 
                                      ,ed217_exibecargahoraria 
                                      ,ed217_brasao 
                                      ,ed217_exibe_obs_diario 
                                      ,ed217_exibirmantenedora 
                                      ,ed217_exibirdistrito 
                                      ,ed217_exibirperiodo 
                                      ,ed217_exibir_etapa_obs 
                                      ,ed217_exibircertidao 
                                      ,ed217_exibiridentidade 
                                      ,ed217_exibir_assinatura_secretario 
                                      ,ed217_exibir_assinatura_adicional 
                                      ,ed217_exibir_grade_alunos 
                                      ,ed217_exibir_coluna_disciplinas 
                                      ,ed217_exibir_coluna_resultado_final 
                                      ,ed217_exibir_brasao 
                                      ,ed217_disposicao_brasao 
                                      ,ed217_disposicao_cabecalho 
                       )
                values (
                                $this->ed217_i_codigo 
                               ,$this->ed217_i_relatorio 
                               ,'$this->ed217_c_nome' 
                               ,'$this->ed217_t_cabecalho' 
                               ,'$this->ed217_t_rodape' 
                               ,'$this->ed217_t_obs' 
                               ,$this->ed217_orientacao 
                               ,$this->ed217_gradenotas 
                               ,$this->ed217_gradeetapas 
                               ,$this->ed217_observacao 
                               ,$this->ed217_i_tipomodelo 
                               ,'$this->ed217_exibeturma' 
                               ,'$this->ed217_exibecargahoraria' 
                               ,$this->ed217_brasao 
                               ,'$this->ed217_exibe_obs_diario' 
                               ,'$this->ed217_exibirmantenedora' 
                               ,'$this->ed217_exibirdistrito' 
                               ,'$this->ed217_exibirperiodo' 
                               ,'$this->ed217_exibir_etapa_obs' 
                               ,'$this->ed217_exibircertidao' 
                               ,'$this->ed217_exibiridentidade' 
                               ,'$this->ed217_exibir_assinatura_secretario' 
                               ,'$this->ed217_exibir_assinatura_adicional' 
                               ,'$this->ed217_exibir_grade_alunos' 
                               ,'$this->ed217_exibir_coluna_disciplinas' 
                               ,'$this->ed217_exibir_coluna_resultado_final' 
                               ,'$this->ed217_exibir_brasao' 
                               ,'$this->ed217_disposicao_brasao' 
                               ,'$this->ed217_disposicao_cabecalho' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "edu_relatmodel ($this->ed217_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "edu_relatmodel já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "edu_relatmodel ($this->ed217_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed217_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed217_i_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14626,'$this->ed217_i_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,2571,14626,'','".AddSlashes(pg_result($resaco,0,'ed217_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,14627,'','".AddSlashes(pg_result($resaco,0,'ed217_i_relatorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,14628,'','".AddSlashes(pg_result($resaco,0,'ed217_c_nome'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,14629,'','".AddSlashes(pg_result($resaco,0,'ed217_t_cabecalho'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,14630,'','".AddSlashes(pg_result($resaco,0,'ed217_t_rodape'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,14679,'','".AddSlashes(pg_result($resaco,0,'ed217_t_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,18660,'','".AddSlashes(pg_result($resaco,0,'ed217_orientacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,18661,'','".AddSlashes(pg_result($resaco,0,'ed217_gradenotas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,18662,'','".AddSlashes(pg_result($resaco,0,'ed217_gradeetapas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,18663,'','".AddSlashes(pg_result($resaco,0,'ed217_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,19712,'','".AddSlashes(pg_result($resaco,0,'ed217_i_tipomodelo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,20292,'','".AddSlashes(pg_result($resaco,0,'ed217_exibeturma'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,20293,'','".AddSlashes(pg_result($resaco,0,'ed217_exibecargahoraria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,20559,'','".AddSlashes(pg_result($resaco,0,'ed217_brasao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1014432,'','".AddSlashes(pg_result($resaco,0,'ed217_exibe_obs_diario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1014548,'','".AddSlashes(pg_result($resaco,0,'ed217_exibirmantenedora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1014549,'','".AddSlashes(pg_result($resaco,0,'ed217_exibirdistrito'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1014554,'','".AddSlashes(pg_result($resaco,0,'ed217_exibirperiodo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1014576,'','".AddSlashes(pg_result($resaco,0,'ed217_exibir_etapa_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1014613,'','".AddSlashes(pg_result($resaco,0,'ed217_exibircertidao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1014614,'','".AddSlashes(pg_result($resaco,0,'ed217_exibiridentidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015686,'','".AddSlashes(pg_result($resaco,0,'ed217_exibir_assinatura_secretario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015687,'','".AddSlashes(pg_result($resaco,0,'ed217_exibir_assinatura_adicional'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015688,'','".AddSlashes(pg_result($resaco,0,'ed217_exibir_grade_alunos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015689,'','".AddSlashes(pg_result($resaco,0,'ed217_exibir_coluna_disciplinas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015690,'','".AddSlashes(pg_result($resaco,0,'ed217_exibir_coluna_resultado_final'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015691,'','".AddSlashes(pg_result($resaco,0,'ed217_exibir_brasao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015692,'','".AddSlashes(pg_result($resaco,0,'ed217_disposicao_brasao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2571,1015693,'','".AddSlashes(pg_result($resaco,0,'ed217_disposicao_cabecalho'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($ed217_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update edu_relatmodel set ";
     $virgula = "";
     if(trim($this->ed217_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_i_codigo"])){ 
       $sql  .= $virgula." ed217_i_codigo = $this->ed217_i_codigo ";
       $virgula = ",";
       if(trim($this->ed217_i_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "ed217_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_i_relatorio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_i_relatorio"])){ 
       $sql  .= $virgula." ed217_i_relatorio = $this->ed217_i_relatorio ";
       $virgula = ",";
       if(trim($this->ed217_i_relatorio) == null ){ 
         $this->erro_sql = " Campo Tipo de Relatório não informado.";
         $this->erro_campo = "ed217_i_relatorio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_c_nome)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_c_nome"])){ 
       $sql  .= $virgula." ed217_c_nome = '$this->ed217_c_nome' ";
       $virgula = ",";
       if(trim($this->ed217_c_nome) == null ){ 
         $this->erro_sql = " Campo Nome do Modelo não informado.";
         $this->erro_campo = "ed217_c_nome";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_t_cabecalho)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_t_cabecalho"])){ 
       $sql  .= $virgula." ed217_t_cabecalho = '$this->ed217_t_cabecalho' ";
       $virgula = ",";
     }
     if(trim($this->ed217_t_rodape)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_t_rodape"])){ 
       $sql  .= $virgula." ed217_t_rodape = '$this->ed217_t_rodape' ";
       $virgula = ",";
     }
     if(trim($this->ed217_t_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_t_obs"])){ 
       $sql  .= $virgula." ed217_t_obs = '$this->ed217_t_obs' ";
       $virgula = ",";
     }
     if(trim($this->ed217_orientacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_orientacao"])){ 
        if(trim($this->ed217_orientacao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ed217_orientacao"])){ 
           $this->ed217_orientacao = "0" ; 
        } 
       $sql  .= $virgula." ed217_orientacao = $this->ed217_orientacao ";
       $virgula = ",";
     }
     if(trim($this->ed217_gradenotas)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_gradenotas"])){ 
        if(trim($this->ed217_gradenotas)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ed217_gradenotas"])){ 
           $this->ed217_gradenotas = "0" ; 
        } 
       $sql  .= $virgula." ed217_gradenotas = $this->ed217_gradenotas ";
       $virgula = ",";
     }
     if(trim($this->ed217_gradeetapas)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_gradeetapas"])){ 
        if(trim($this->ed217_gradeetapas)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ed217_gradeetapas"])){ 
           $this->ed217_gradeetapas = "0" ; 
        } 
       $sql  .= $virgula." ed217_gradeetapas = $this->ed217_gradeetapas ";
       $virgula = ",";
     }
     if(trim($this->ed217_observacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_observacao"])){ 
        if(trim($this->ed217_observacao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ed217_observacao"])){ 
           $this->ed217_observacao = "0" ; 
        } 
       $sql  .= $virgula." ed217_observacao = $this->ed217_observacao ";
       $virgula = ",";
     }
     if(trim($this->ed217_i_tipomodelo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_i_tipomodelo"])){ 
        if(trim($this->ed217_i_tipomodelo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["ed217_i_tipomodelo"])){ 
           $this->ed217_i_tipomodelo = "0" ; 
        } 
       $sql  .= $virgula." ed217_i_tipomodelo = $this->ed217_i_tipomodelo ";
       $virgula = ",";
     }
     if(trim($this->ed217_exibeturma)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibeturma"])){ 
       $sql  .= $virgula." ed217_exibeturma = '$this->ed217_exibeturma' ";
       $virgula = ",";
       if(trim($this->ed217_exibeturma) == null ){ 
         $this->erro_sql = " Campo Exibe Turma não informado.";
         $this->erro_campo = "ed217_exibeturma";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibecargahoraria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibecargahoraria"])){ 
       $sql  .= $virgula." ed217_exibecargahoraria = '$this->ed217_exibecargahoraria' ";
       $virgula = ",";
       if(trim($this->ed217_exibecargahoraria) == null ){ 
         $this->erro_sql = " Campo Exibir Porcentagem da Frequência não informado.";
         $this->erro_campo = "ed217_exibecargahoraria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_brasao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_brasao"])){ 
       $sql  .= $virgula." ed217_brasao = $this->ed217_brasao ";
       $virgula = ",";
       if(trim($this->ed217_brasao) == null ){ 
         $this->erro_sql = " Campo Brasão não informado.";
         $this->erro_campo = "ed217_brasao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibe_obs_diario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibe_obs_diario"])){ 
       $sql  .= $virgula." ed217_exibe_obs_diario = '$this->ed217_exibe_obs_diario' ";
       $virgula = ",";
       if(trim($this->ed217_exibe_obs_diario) == null ){ 
         $this->erro_sql = " Campo Exibe Observação do Diário não informado.";
         $this->erro_campo = "ed217_exibe_obs_diario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibirmantenedora)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibirmantenedora"])){ 
       $sql  .= $virgula." ed217_exibirmantenedora = '$this->ed217_exibirmantenedora' ";
       $virgula = ",";
       if(trim($this->ed217_exibirmantenedora) == null ){ 
         $this->erro_sql = " Campo Exibir Mantenedora não informado.";
         $this->erro_campo = "ed217_exibirmantenedora";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibirdistrito)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibirdistrito"])){ 
       $sql  .= $virgula." ed217_exibirdistrito = '$this->ed217_exibirdistrito' ";
       $virgula = ",";
       if(trim($this->ed217_exibirdistrito) == null ){ 
         $this->erro_sql = " Campo Exibir Distrito não informado.";
         $this->erro_campo = "ed217_exibirdistrito";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibirperiodo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibirperiodo"])){ 
       $sql  .= $virgula." ed217_exibirperiodo = '$this->ed217_exibirperiodo' ";
       $virgula = ",";
       if(trim($this->ed217_exibirperiodo) == null ){ 
         $this->erro_sql = " Campo Exibir Coluna Período não informado.";
         $this->erro_campo = "ed217_exibirperiodo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibir_etapa_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_etapa_obs"])){ 
       $sql  .= $virgula." ed217_exibir_etapa_obs = '$this->ed217_exibir_etapa_obs' ";
       $virgula = ",";
       if(trim($this->ed217_exibir_etapa_obs) == null ){ 
         $this->erro_sql = " Campo Exibir Etapa na Observação não informado.";
         $this->erro_campo = "ed217_exibir_etapa_obs";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibircertidao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibircertidao"])){ 
       $sql  .= $virgula." ed217_exibircertidao = '$this->ed217_exibircertidao' ";
       $virgula = ",";
       if(trim($this->ed217_exibircertidao) == null ){ 
         $this->erro_sql = " Campo Exibir informações da certidao de nasc. não informado.";
         $this->erro_campo = "ed217_exibircertidao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibiridentidade)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibiridentidade"])){ 
       $sql  .= $virgula." ed217_exibiridentidade = '$this->ed217_exibiridentidade' ";
       $virgula = ",";
       if(trim($this->ed217_exibiridentidade) == null ){ 
         $this->erro_sql = " Campo Exibir informações do RG não informado.";
         $this->erro_campo = "ed217_exibiridentidade";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibir_assinatura_secretario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_assinatura_secretario"])){ 
       $sql  .= $virgula." ed217_exibir_assinatura_secretario = '$this->ed217_exibir_assinatura_secretario' ";
       $virgula = ",";
       if(trim($this->ed217_exibir_assinatura_secretario) == null ){ 
         $this->erro_sql = " Campo Exibir Assinatura Secretário não informado.";
         $this->erro_campo = "ed217_exibir_assinatura_secretario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibir_assinatura_adicional)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_assinatura_adicional"])){ 
       $sql  .= $virgula." ed217_exibir_assinatura_adicional = '$this->ed217_exibir_assinatura_adicional' ";
       $virgula = ",";
       if(trim($this->ed217_exibir_assinatura_adicional) == null ){ 
         $this->erro_sql = " Campo Exibir Assinatura Adicional não informado.";
         $this->erro_campo = "ed217_exibir_assinatura_adicional";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibir_grade_alunos)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_grade_alunos"])){ 
       $sql  .= $virgula." ed217_exibir_grade_alunos = '$this->ed217_exibir_grade_alunos' ";
       $virgula = ",";
       if(trim($this->ed217_exibir_grade_alunos) == null ){ 
         $this->erro_sql = " Campo Exibir Grade Alunos não informado.";
         $this->erro_campo = "ed217_exibir_grade_alunos";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibir_coluna_disciplinas)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_coluna_disciplinas"])){ 
       $sql  .= $virgula." ed217_exibir_coluna_disciplinas = '$this->ed217_exibir_coluna_disciplinas' ";
       $virgula = ",";
       if(trim($this->ed217_exibir_coluna_disciplinas) == null ){ 
         $this->erro_sql = " Campo Exibir Coluna Disciplinas não informado.";
         $this->erro_campo = "ed217_exibir_coluna_disciplinas";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibir_coluna_resultado_final)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_coluna_resultado_final"])){ 
       $sql  .= $virgula." ed217_exibir_coluna_resultado_final = '$this->ed217_exibir_coluna_resultado_final' ";
       $virgula = ",";
       if(trim($this->ed217_exibir_coluna_resultado_final) == null ){ 
         $this->erro_sql = " Campo Exibir Coluna Resultado Final não informado.";
         $this->erro_campo = "ed217_exibir_coluna_resultado_final";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_exibir_brasao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_brasao"])){ 
       $sql  .= $virgula." ed217_exibir_brasao = '$this->ed217_exibir_brasao' ";
       $virgula = ",";
       if(trim($this->ed217_exibir_brasao) == null ){ 
         $this->erro_sql = " Campo Exibir Brasão não informado.";
         $this->erro_campo = "ed217_exibir_brasao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_disposicao_brasao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_disposicao_brasao"])){ 
       $sql  .= $virgula." ed217_disposicao_brasao = '$this->ed217_disposicao_brasao' ";
       $virgula = ",";
       if(trim($this->ed217_disposicao_brasao) == null ){ 
         $this->erro_sql = " Campo Disposição Brasão não informado.";
         $this->erro_campo = "ed217_disposicao_brasao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ed217_disposicao_cabecalho)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ed217_disposicao_cabecalho"])){ 
       $sql  .= $virgula." ed217_disposicao_cabecalho = '$this->ed217_disposicao_cabecalho' ";
       $virgula = ",";
       if(trim($this->ed217_disposicao_cabecalho) == null ){ 
         $this->erro_sql = " Campo Disposição Cabeçalho não informado.";
         $this->erro_campo = "ed217_disposicao_cabecalho";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($ed217_i_codigo!=null){
       $sql .= " ed217_i_codigo = $this->ed217_i_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->ed217_i_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,14626,'$this->ed217_i_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_i_codigo"]) || $this->ed217_i_codigo != "")
             $resac = db_query("insert into db_acount values($acount,2571,14626,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_i_codigo'))."','$this->ed217_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_i_relatorio"]) || $this->ed217_i_relatorio != "")
             $resac = db_query("insert into db_acount values($acount,2571,14627,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_i_relatorio'))."','$this->ed217_i_relatorio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_c_nome"]) || $this->ed217_c_nome != "")
             $resac = db_query("insert into db_acount values($acount,2571,14628,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_c_nome'))."','$this->ed217_c_nome',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_t_cabecalho"]) || $this->ed217_t_cabecalho != "")
             $resac = db_query("insert into db_acount values($acount,2571,14629,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_t_cabecalho'))."','$this->ed217_t_cabecalho',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_t_rodape"]) || $this->ed217_t_rodape != "")
             $resac = db_query("insert into db_acount values($acount,2571,14630,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_t_rodape'))."','$this->ed217_t_rodape',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_t_obs"]) || $this->ed217_t_obs != "")
             $resac = db_query("insert into db_acount values($acount,2571,14679,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_t_obs'))."','$this->ed217_t_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_orientacao"]) || $this->ed217_orientacao != "")
             $resac = db_query("insert into db_acount values($acount,2571,18660,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_orientacao'))."','$this->ed217_orientacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_gradenotas"]) || $this->ed217_gradenotas != "")
             $resac = db_query("insert into db_acount values($acount,2571,18661,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_gradenotas'))."','$this->ed217_gradenotas',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_gradeetapas"]) || $this->ed217_gradeetapas != "")
             $resac = db_query("insert into db_acount values($acount,2571,18662,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_gradeetapas'))."','$this->ed217_gradeetapas',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_observacao"]) || $this->ed217_observacao != "")
             $resac = db_query("insert into db_acount values($acount,2571,18663,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_observacao'))."','$this->ed217_observacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_i_tipomodelo"]) || $this->ed217_i_tipomodelo != "")
             $resac = db_query("insert into db_acount values($acount,2571,19712,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_i_tipomodelo'))."','$this->ed217_i_tipomodelo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibeturma"]) || $this->ed217_exibeturma != "")
             $resac = db_query("insert into db_acount values($acount,2571,20292,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibeturma'))."','$this->ed217_exibeturma',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibecargahoraria"]) || $this->ed217_exibecargahoraria != "")
             $resac = db_query("insert into db_acount values($acount,2571,20293,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibecargahoraria'))."','$this->ed217_exibecargahoraria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_brasao"]) || $this->ed217_brasao != "")
             $resac = db_query("insert into db_acount values($acount,2571,20559,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_brasao'))."','$this->ed217_brasao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibe_obs_diario"]) || $this->ed217_exibe_obs_diario != "")
             $resac = db_query("insert into db_acount values($acount,2571,1014432,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibe_obs_diario'))."','$this->ed217_exibe_obs_diario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibirmantenedora"]) || $this->ed217_exibirmantenedora != "")
             $resac = db_query("insert into db_acount values($acount,2571,1014548,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibirmantenedora'))."','$this->ed217_exibirmantenedora',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibirdistrito"]) || $this->ed217_exibirdistrito != "")
             $resac = db_query("insert into db_acount values($acount,2571,1014549,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibirdistrito'))."','$this->ed217_exibirdistrito',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibirperiodo"]) || $this->ed217_exibirperiodo != "")
             $resac = db_query("insert into db_acount values($acount,2571,1014554,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibirperiodo'))."','$this->ed217_exibirperiodo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_etapa_obs"]) || $this->ed217_exibir_etapa_obs != "")
             $resac = db_query("insert into db_acount values($acount,2571,1014576,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibir_etapa_obs'))."','$this->ed217_exibir_etapa_obs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibircertidao"]) || $this->ed217_exibircertidao != "")
             $resac = db_query("insert into db_acount values($acount,2571,1014613,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibircertidao'))."','$this->ed217_exibircertidao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibiridentidade"]) || $this->ed217_exibiridentidade != "")
             $resac = db_query("insert into db_acount values($acount,2571,1014614,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibiridentidade'))."','$this->ed217_exibiridentidade',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_assinatura_secretario"]) || $this->ed217_exibir_assinatura_secretario != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015686,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibir_assinatura_secretario'))."','$this->ed217_exibir_assinatura_secretario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_assinatura_adicional"]) || $this->ed217_exibir_assinatura_adicional != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015687,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibir_assinatura_adicional'))."','$this->ed217_exibir_assinatura_adicional',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_grade_alunos"]) || $this->ed217_exibir_grade_alunos != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015688,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibir_grade_alunos'))."','$this->ed217_exibir_grade_alunos',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_coluna_disciplinas"]) || $this->ed217_exibir_coluna_disciplinas != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015689,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibir_coluna_disciplinas'))."','$this->ed217_exibir_coluna_disciplinas',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_coluna_resultado_final"]) || $this->ed217_exibir_coluna_resultado_final != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015690,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibir_coluna_resultado_final'))."','$this->ed217_exibir_coluna_resultado_final',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_exibir_brasao"]) || $this->ed217_exibir_brasao != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015691,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_exibir_brasao'))."','$this->ed217_exibir_brasao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_disposicao_brasao"]) || $this->ed217_disposicao_brasao != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015692,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_disposicao_brasao'))."','$this->ed217_disposicao_brasao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["ed217_disposicao_cabecalho"]) || $this->ed217_disposicao_cabecalho != "")
             $resac = db_query("insert into db_acount values($acount,2571,1015693,'".AddSlashes(pg_result($resaco,$conresaco,'ed217_disposicao_cabecalho'))."','$this->ed217_disposicao_cabecalho',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "edu_relatmodel não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed217_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "edu_relatmodel não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ed217_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->ed217_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($ed217_i_codigo=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($ed217_i_codigo));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,14626,'$ed217_i_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,2571,14626,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,14627,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_i_relatorio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,14628,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_c_nome'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,14629,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_t_cabecalho'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,14630,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_t_rodape'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,14679,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_t_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,18660,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_orientacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,18661,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_gradenotas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,18662,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_gradeetapas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,18663,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_observacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,19712,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_i_tipomodelo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,20292,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibeturma'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,20293,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibecargahoraria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,20559,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_brasao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1014432,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibe_obs_diario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1014548,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibirmantenedora'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1014549,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibirdistrito'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1014554,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibirperiodo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1014576,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibir_etapa_obs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1014613,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibircertidao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1014614,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibiridentidade'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015686,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibir_assinatura_secretario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015687,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibir_assinatura_adicional'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015688,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibir_grade_alunos'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015689,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibir_coluna_disciplinas'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015690,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibir_coluna_resultado_final'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015691,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_exibir_brasao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015692,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_disposicao_brasao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2571,1015693,'','".AddSlashes(pg_result($resaco,$iresaco,'ed217_disposicao_cabecalho'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from edu_relatmodel
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($ed217_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " ed217_i_codigo = $ed217_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "edu_relatmodel não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ed217_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "edu_relatmodel não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ed217_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$ed217_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:edu_relatmodel";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($ed217_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from edu_relatmodel ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed217_i_codigo)) {
         $sql2 .= " where edu_relatmodel.ed217_i_codigo = $ed217_i_codigo "; 
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

    public function sql_query_file($ed217_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from edu_relatmodel ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($ed217_i_codigo)){
         $sql2 .= " where edu_relatmodel.ed217_i_codigo = $ed217_i_codigo "; 
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
?>
