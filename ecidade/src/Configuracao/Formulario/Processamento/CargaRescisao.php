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

namespace ECidade\Configuracao\Formulario\Processamento;

use ECidade\Configuracao\Formulario\Model\Formulario as FormularioModel;
use ECidade\Configuracao\Formulario\Resposta\Model\Resposta;
use ECidade\Configuracao\Formulario\Resposta\Repository\Resposta as RespostaRepository;
use ECidade\File\Csv\Dumper\Dumper;

class CargaRescisao extends Carga
{
    /**
     * @var array
     */
    private $rescisoes = array();

    /**
     * @var \Instituicao
     */
    private $instituicao;

    /**
     * @var FormularioModel
     */
    protected $formulario;

    /**
     * @var \DBCompetencia
     */
    private $competencia;

    /**
     * @var string
     */
    private $vinculoemprego;

    /**
     * @var array
     */
    public $rubricasSemGrupo = array();

    private $callbackSaveForm;

    /**
     * Define as Matriculas que devem ser importadas
     * @param array $rescisoes
     */
    public function setRescisoes(array $rescisoes)
    {
        $this->rescisoes = $rescisoes;
    }

    /**
     * Define a instituição do processamento
     * @param \Instituicao $instituicao
     */
    public function setInstituicao(\Instituicao $instituicao)
    {
        $this->instituicao = $instituicao;
    }

    /**
     * Define a competencia do processamento
     * @param \DBCompetencia $competencia
     */
    public function setCompetencia(\DBCompetencia $competencia)
    {
        $this->competencia = $competencia;
    }

    /**
     * executa o processamento da Carga
     */
    public function executar()
    {
        $rescisoes = $this->prepararDadosDaRescisao();
        $formulario = $this->formulario;

        if (empty($rescisoes)) {
            $msg  = 'Nenhuma matrícula foi processada.';
            $msg .= 'Verifique se os desligamentos selecionados possuem alguma rubrica.';
            throw new \Exception($msg);
        }

        foreach ($rescisoes as $rescisao) {
            $this->salvarFormulario($formulario, $rescisao);
        }
    }

    /**
     * Prepara os dados da consulta e retorna o seu resource
     * @return array
     * @throws \DBException
     */
    private function prepararDadosDaRescisao()
    {
        mb_internal_encoding("UTF-8");
        $sqlDadosRescisao = $this->getSqlDadosRescisao();

        $rsCarga = db_query($sqlDadosRescisao);

        if (!$rsCarga) {
            $msg  = "Não foi possivel rodar a carga do formulario ";
            $msg .= $this->formulario->getNome();
            $msg .= ". Verifique o código da carga.";
            throw new \DBException($msg);
        }

        $instancia = $this;
        $dados = \db_utils::makeCollectionFromRecord($rsCarga, function ($dados) use ($instancia) {
            $rescisao = $dados;
            $rescisao->desligamento_rubricas_json = '';
            $rubricas = $instancia->getRubricasDaRescisao($rescisao->matricula);

            if (count($rubricas) > 0) {
                foreach ($rubricas as $index => $rubrica) {
                    if (empty($rubrica->identificador_grupo)) {
                        $instancia->rubricasSemGrupo[$rescisao->matricula][] = $rubrica->codrubr;
                    }
                    $rubrica->idetabrubr = htmlentities($rubrica->idetabrubr);
                }

                unset($rubrica);
                $json = json_encode($rubricas);
                $rescisao->desligamento_rubricas_json = html_entity_decode(utf8_decode($json));
            }

            return $rescisao;
        });

        return $dados;
    }

    public function arquivoInconsistencias()
    {
        if (empty($this->rubricasSemGrupo)) {
            return null;
        }

        $dados = array(
            array(
                'Matricula',
                'Rubricas'
            )
        );

        foreach ($this->rubricasSemGrupo as $matricula => $rubricas) {
            $dados[] = array(
                (string) $matricula,
                implode(', ', $rubricas)
            );
        }

        $csv = new Dumper();
        $csv->setCsvControl(';');
        $file = $csv->dumpToFile($dados, 'tmp/carga_comissao_' . date('dmY') . '.csv');

        return $file;
    }

    /**
     * Adiciona as Respostas para a pergunta
     * @param \ECidade\Configuracao\Formulario\Model\Formulario $formulario
     * @param                                                   $oDadosConsulta
     * @throws \BusinessException
     * @throws \ParameterException
     */
    private function salvarFormulario(FormularioModel $formulario, $oDadosConsulta)
    {
        $oResposta = $this->pesquisarRespostaDoFormularioComOsCamposChave($oDadosConsulta);
        if (empty($oResposta)) {
            $oResposta = new Resposta();
            $oResposta->setFormulario($formulario);
            $oResposta->setData(new \DBDate(date('Y-m-d')));
        }
        foreach ($formulario->getPerguntas() as $pergunta) {
            if (isset($oDadosConsulta->{$pergunta->getIdentificadorCampo()})) {
                $oResposta->adicionarRespostaParaPergunta(
                    $pergunta,
                    $oDadosConsulta->{$pergunta->getIdentificadorCampo()}
                );
            }
        }

        RespostaRepository::persist($oResposta);

        $codigoResposta = $oResposta->getCodigo();

        call_user_func($this->callbackSaveForm, $codigoResposta, $oDadosConsulta, $this->formulario->getCodigo());
    }

    /**
     * Metodo responsavel por evento depois de salvar as resposta  no formulario
     *
     * @param \Closure $oClosure
     */
    public function setCallbackSaveForm(\Closure $oClosure)
    {
        $this->callbackSaveForm = $oClosure;
    }

    /**
     * @param $dados
     * @return Resposta|null
     * @throws \Exception
     */
    protected function pesquisarRespostaDoFormularioComOsCamposChave($dados)
    {
        $aPerguntasChaves = $this->formulario->getPerguntasIdentificadoras();

        if (!empty($aPerguntasChaves)) {
            $aCampos = array();

            foreach ($aPerguntasChaves as $pergunta) {
                $aCampos[] = array("pergunta" => $pergunta, "resposta" => $dados->{$pergunta->getIdentificadorCampo()});
            }

            if (count($aCampos) == 0) {
                return null;
            }

            $oResposta = RespostaRepository::getPorFormularioECampos($this->formulario, $aCampos);

            if (count($oResposta) > 1) {
                $msg  = "Foram encontrados mais de uma resposta para o formulário ";
                $msg .= $this->formulario->getNome();
                throw new \Exception($msg);
            }

            if (count($oResposta) == 1) {
                return $oResposta[0];
            }
        }

        return null;
    }

    /**
     * Rubricas da rescisão
     * @param $matricula
     * @return \stdClass[]
     */
    public function getRubricasDaRescisao($matricula)
    {
        $sqlRubricas = "select r20_rubric as codrubr,
                              r20_quant as qtdrubr,
                              r20_valor as vrrubr,                              
                              identificador as idetabrubr,
                              '' as fatorRubr,
                              '' as vrUnit,
                              case when r20_pd = 1 then 
                                  'Provento'
                                  when  r20_pd = 2 then
                                  'Desconto'
                                  when  r20_pd = 3 then
                                  'Base'
                              end  as tiprubr, 
                              rh114_agrupamentorubrica as identificador_grupo
                         from gerfres
                              inner join rhpessoalmov on rh02_regist = r20_regist
                                                     and rh02_anousu = r20_anousu
                                                     and rh02_mesusu = r20_mesusu
                                                     and rh02_instit = r20_instit
                              inner join rhpesrescisao on rh05_seqpes = rh02_seqpes
                              left join agrupamentorubricarubrica 
                                     on rh114_rubrica = r20_rubric and r20_instit = rh114_instituicao
                              left join fc_rubrica_esocial(r20_rubric, r20_instit) on r20_rubric = codigo_rubrica
                        where r20_regist = " . $matricula . "
                         and (codigo_incidencia_irrf is null 
                             or codigo_incidencia_irrf not in(
                                '31',
                                '32',
                                '33',
                                '34',
                                '35',
                                '51',
                                '52',
                                '53',
                                '54',
                                '55',
                                '81',
                                '82',
                                '83'))
                             and concat(r20_anousu, lpad(r20_mesusu, 2, 0)) >= to_char(rh05_recis, 'YYYYMM')
                             and r20_pd <> 3
                             AND r20_instit = {$this->instituicao->getCodigo()}
                         order by codrubr";

        $rsRubricas = db_query($sqlRubricas);

        if (!pg_num_rows($rsRubricas)) {
            $sqlRubricas = "
                SELECT x.rubrica AS codrubr,
                       0 AS qtdrubr,
                       1 AS vrrubr,
                       identificador AS idetabrubr,
                       '' AS fatorrubr,
                       '' AS vrunit,
                       rh114_agrupamentorubrica AS identificador_grupo,
                       'Provento' AS tiprubr
                FROM (SELECT 'R928'::text AS rubrica) AS x
                       LEFT JOIN agrupamentorubricarubrica ON rh114_rubrica = x.rubrica
                       LEFT JOIN fc_rubrica_esocial(x.rubrica, {$this->instituicao->getCodigo()})
                              ON x.rubrica = codigo_rubrica
                UNION ALL
                SELECT x.rubrica AS codrubr,
                       0 AS qtdrubr,
                       1 AS vrrubr,
                       identificador AS idetabrubr,
                       '' AS fatorrubr,
                       '' AS vrunit,
                       rh114_agrupamentorubrica AS identificador_grupo,
                       'Desconto' AS tiprubr
                FROM (SELECT 'R929'::text AS rubrica) AS x
                       LEFT JOIN agrupamentorubricarubrica ON rh114_rubrica = x.rubrica
                       LEFT JOIN fc_rubrica_esocial(x.rubrica, {$this->instituicao->getCodigo()}) 
                              ON x.rubrica = codigo_rubrica
            ";

            $rsRubricas = db_query($sqlRubricas);
        }

        return \db_utils::getCollectionByRecord($rsRubricas);
    }

    /**
     * @return string
     */
    private function getSqlDadosRescisao()
    {
        $codigoRescisao = implode("', '", $this->rescisoes);
        $sqlDadosRescisao = 'select distinct rh02_regist as matricula,                                    
                                    cgm.z01_cgccpf as "cpfTrab",
                                    rh16_pis  as "nisTrab",
                                    r59_motivoesocial as "mtvDeslig",
                                    rh05_recis as "dtDeslig",
                                    case when rh05_taviso =  2 then \'S\' else \'N\' end as "indPagtoAPI",
                                    rh05_aviso as "dtProjFimAPI",
                                    cgm_lota.z01_numcgm as cgmempregador,
                                    rh02_seqpes,
                                    tipo_pensao,
                                    rh52_regime, 
                                    rh05_codigorescisao as desligamento_codigo_rescisao,
                                    current_date - rh05_recis as dias_da_rescisao,
                                    (case when tipo_pensao is null then null
                                          when percentual_pago > 0 and valor_pago = 0 then 1
                                          when percentual_pago = 0 and valor_pago > 0  then 2
                                          when percentual_pago > 0 and valor_pago > 0  then 3
                                    end ) as "pensAlim", 
                                    percentual_pago as "percAliment",
                                    valor_pago as "vrAlim",
                                    case when rh05_taviso in(3,2) then 4 else 0 end as "indCumprParc",
                                    rh05_codigorescisao as "ideDmDev",
                                    rh05_tiporescisao,
                                    empregador.tpinsc as "ideEstabLot_tpInsc",
                                    empregador.nrInsc as "ideEstabLot_nrInsc",
                                    lotacaotributaria.codlotacao as "ideEstabLot_codLotacao",
                                case when rh05_tiporescisao = 2 then cgm_lota.z01_cgccpf else \'\' end as "ideEstabLot"
                               from rhpessoalmov
                                    inner join rhpesrescisao on rh05_seqpes = rh02_seqpes
                                    inner join rhregime on rh02_codreg = rh30_codreg
                                    inner join rhcadregime on rh30_regime = rh52_regime
                                    inner join rhpessoal on rh02_regist = rh01_regist
                                    inner join rhlota on rh02_lota = r70_codigo
                                    inner join cgm as cgm_lota on r70_numcgm = cgm_lota.z01_numcgm
                                    inner join cgm on cgm.z01_numcgm = rh01_numcgm                                    
                                    inner join rescisao on r59_regime  = rh52_regime
                                                        and r59_anousu = rh02_anousu
                                                        and r59_mesusu = rh02_mesusu
                                                        and r59_instit = rh02_instit
                                                        and r59_causa  = rh05_causa
                                                        and (case when trim(rh05_caub) is null 
                                                                  then r59_caub = \'\'
                                                                  else rh05_caub = r59_caub 
                                                              end)
                                    left join rhpesdoc on rh16_regist = rh02_regist
                                    left join (select r52_regist as matricula ,
                                                      (array_accum(distinct (case when r52_perc > 0
                                                                                  then 1  
                                                                                  else 2 
                                                                              end))) as tipo_pensao,
                                                      sum(coalesce(r52_perc, 0)) as percentual_pago,
                                                      sum(case when coalesce(r52_perc, 0) = 0 
                                                               then coalesce(r52_valres, 0) 
                                                               else 0 
                                                           end) as valor_pago
                                                 from pensao
                                                      inner join rhpessoal on rh01_regist = r52_regist
                                                where r52_anousu = ' . $this->competencia->getAno() . '
                                                  and r52_mesusu = ' . $this->competencia->getMes() . '
                                                  and rh01_instit = ' . $this->instituicao->getCodigo() . '
                                                  and r52_pagres is true
                                                group by r52_regist) as pensao on rh02_regist = matricula
                                                
                             left join fc_empregador_esocial(cgm_lota.z01_numcgm) as empregador
                                    on empregador.codigo =  cgm_lota.z01_numcgm
                             left join fc_lotacaotributaria_esocial(cgm_lota.z01_numcgm) as lotacaotributaria  
                                    on lotacaotributaria.codigo =  cgm_lota.z01_numcgm                  
                             where rh02_mesusu  = ' . $this->competencia->getMes() . ' 
                               and rh02_anousu = ' . $this->competencia->getAno() . '
                               and rh02_instit = ' . $this->instituicao->getCodigo() . '
                               and rh30_vinculoemprego is ' . $this->vinculoemprego . '
                               and rh05_codigorescisao in (\'' . $codigoRescisao . '\')';

        return $sqlDadosRescisao;
    }

    /**
     * Define as Matriculas que devem ser importadas
     * @param string $vinculoemprego
     */
    public function setVinculoEmprego($vinculoemprego)
    {
        $this->vinculoemprego = $vinculoemprego;
    }

    /**
     * @return array
     */
    public function getRubricasSemGrupo()
    {
        return $this->rubricasSemGrupo;
    }
}
