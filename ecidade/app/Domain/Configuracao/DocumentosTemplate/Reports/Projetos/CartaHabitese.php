<?php
namespace App\Domain\Configuracao\DocumentosTemplate\Reports\Projetos;

use App\Domain\Configuracao\DocumentosTemplate\ProcessaDocumentoTemplate;
use \db_utils;
use \cl_obrashabite;

class CartaHabitese extends ProcessaDocumentoTemplate
{
  
    private $codigoHabitese;
    const GRUPOTEMPLATE = 15;
  
    public function __construct($codigoHabitese)
    {
        parent::__construct(self::GRUPOTEMPLATE);
        $this->codigoHabitese    = $codigoHabitese;
        $this->setNomeDocumento('CartaHabitese');
    }
    
    /**
     * @return array
     */
    public function configuraDadosVariaveis()
    {
        $obrasHabitese     = new cl_obrashabite();
        $campos            = "cgm                      ,
                              nome_proprietario        ,
                              cpf_cnpj_proprietario    ,
                              logradouro               ,
                              numero                   ,
                              complemento              ,
                              bairro                   ,
                              sql                      ,
                              pql                      ,
                              matricula_imovel         ,
                              cod_habite               ,
                              sequencial_habite        ,
                              ano_sequencial_habite    ,
                              sequencial_alvara        ,
                              ano_sequencial_alvara    ,
                              expedicao_alvara         ,
                              data_habite              ,
                              engenheiro               ,
                              cgm_responsavel_tecnico  ,
                              nome_responsavel_tecnico ,
                              cpf_responsavel_tecnico  ,
                              crea                     ,
                              protocolo                ,
                              data_protocolo           ,
                              area_total               ,
                              area_liberada            ,
                              endereco_obra            ,
                              numero_endereco_obra     ,
                              complemento_endereco_obra,
                              bairro_endereco_obra     ,
                              observacoes              ,
                              observacoes_inss         ,
                              tipo_habite              ,
                              nome_servidor            ,
                              cargo_servidor           ,
                              matricula_servidor       ,
                              exercicio                ,
                              tipo_construcao          ,
                              alvara                   ,
                              data_alvara              ,
                              data_expedicao_extenso   ,
                              data_atual_extenso       ,
                              cgm_responsavel_execucao ,
                              nome_responsavel_execucao,
                              cpf_responsavel_execucao ,
                              carac_ocupacao           ,
                              carac_tipo_lancamento    ,
                              carac_tipo_construcao";
        $sqlVariaveisHabitese = $obrasHabitese->sql_query_cartaHabiteseDadosTemplate($campos, $this->codigoHabitese);
        
        $rsObrasVariaveis     = $obrasHabitese->sql_record($sqlVariaveisHabitese);

        if (!$rsObrasVariaveis || $obrasHabitese->numrows == 0) {
            throw new \Exception("Não foi possível buscar os dados");
        }

        $aDadosObras               = (array) db_utils::fieldsMemory($rsObrasVariaveis, 0);
        $aDadosObras['db_anousu']  = db_getsession("DB_anousu");
        $aDadosObras['db_datausu'] = date('d/m/Y');
        $aDadosObras['db_horausu'] = date('H:i');
        
        return $aDadosObras;
    }
}
