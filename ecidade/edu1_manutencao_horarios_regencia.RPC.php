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

use ECidade\Educacao\Escola\Service\GradeHorarioService;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');


$parametros = JSON::requestParameters();
$retorno = new stdClass();
$retorno->erro = false;
$retorno->mensagem = '';

try {
    db_inicio_transacao();
    switch ($parametros->acao) {
        case 'buscarEtapas':
            if (empty($parametros->turma)) {
                throw new Exception("Informe a turma.");
            }
            $turma = TurmaRepository::getTurmaByCodigo($parametros->turma);
            $etapas = $turma->getEtapas();

            if (count($etapas) == 0) {
                throw new Exception('Turma sem etapas.');
            }

            foreach ($etapas as $etapa) {
                $retorno->etapas[] = (object) array(
                    'codigo' => $etapa->getEtapa()->getCodigo(),
                    'nome' => $etapa->getEtapa()->getNome(),
                );
            }

            break;
        case 'buscarHorariosRegentes':
            if (empty($parametros->turma)) {
                throw new Exception("Informe a turma");
            }

            if (empty($parametros->etapa)) {
                throw new Exception("Informe a etapa");
            }

            $where = array(
                "ed59_i_turma = {$parametros->turma}",
                "ed59_i_serie = {$parametros->etapa}",
            );

            $where = implode(' and ', $where);
            $sql = "
                select case when ed285_i_cgm is null then cgmpessoal.z01_nome else cgm.z01_nome end as regente
                       ,trim(ed232_c_descr) as disciplina
                       ,trim(ed08_c_descr) as periodo
                       ,ed58_i_diasemana as idiasemana
                       ,ed32_c_descr as dia_semana
                      ,ed58_ativo as ativo
                      ,ed58_tipovinculo as tipo_vinculo
                      ,ed58_datainicio as data_inicio
                      ,ed58_datafim as data_fim
                      ,ed58_i_codigo as codigo
                      ,(select distinct 1
                          from diarioclasseregenciahorario
                         where ed302_regenciahorario = ed58_i_codigo) as lancou_frequencia
                      ,(select max(ed300_datalancamento)
                          from diarioclasse
                          join diarioclasseregenciahorario on diarioclasseregenciahorario.ed302_diarioclasse = diarioclasse.ed300_sequencial
                        where ed302_regenciahorario = ed58_i_codigo) as data_lancamento_frequencia
                      ,ed58_i_rechumano as rechumano
                 from regenciahorario
                 join regencia on ed58_i_regencia = ed59_i_codigo
                 join disciplina on ed59_i_disciplina = ed12_i_codigo
                 join caddisciplina on ed12_i_caddisciplina = ed232_i_codigo
                 join rechumano on ed58_i_rechumano = ed20_i_codigo
                 join periodoescola on ed58_i_periodo = ed17_i_codigo
                 join periodoaula on ed17_i_periodoaula = ed08_i_codigo
                 join diasemana on diasemana.ed32_i_codigo = ed58_i_diasemana
                 left join rechumanocgm on ed285_i_rechumano = ed58_i_rechumano
                 left join cgm on ed285_i_cgm = cgm.z01_numcgm
                 left join rechumanopessoal on ed284_i_rechumano = ed58_i_rechumano
                 left join rhpessoal on ed284_i_rhpessoal = rh01_regist
                 left join cgm cgmpessoal on rh01_numcgm = cgmpessoal.z01_numcgm
                where {$where}
                UNION
                select case
                    when ed285_i_cgm is null
                        then 'DISCIPLINA SEM REGENTE' end as regente
                       ,trim(ed232_c_descr) as disciplina
                       ,trim(ed08_c_descr) as periodo
                       ,ed175_diasemana as idiasemana
                       ,ed32_c_descr as dia_semana
                      ,ed175_ativo as ativo
                      ,ed175_tipovinculo as tipo_vinculo
                      ,ed175_datainicio as data_inicio
                      ,ed175_datafim as data_fim
                      ,ed175_codigo as codigo
                      ,(select distinct 1
                          from diarioclasseregenciahorario
                         where ed302_regenciahorario = ed175_codigo) as lancou_frequencia
                      ,(select max(ed300_datalancamento)
                          from diarioclasse
                          join diarioclasseregenciahorario on diarioclasseregenciahorario.ed302_diarioclasse = diarioclasse.ed300_sequencial
                        where ed302_regenciahorario = ed175_codigo) as data_lancamento_frequencia
                      ,ed175_rechumano as rechumano
                 from regenciahorariodiscsemreg
                 join regencia on ed175_regencia = ed59_i_codigo
                 join disciplina on ed59_i_disciplina = ed12_i_codigo
                 join caddisciplina on ed12_i_caddisciplina = ed232_i_codigo
                 left join rechumano on ed175_rechumano = ed20_i_codigo
                 join periodoescola on ed175_periodo = ed17_i_codigo
                 join periodoaula on ed17_i_periodoaula = ed08_i_codigo
                 join diasemana on diasemana.ed32_i_codigo = ed175_diasemana
                 left join rechumanocgm on ed285_i_rechumano = ed175_rechumano
                 left join cgm on ed285_i_cgm = cgm.z01_numcgm
                 left join rechumanopessoal on ed284_i_rechumano = ed175_rechumano
                 left join rhpessoal on ed284_i_rhpessoal = rh01_regist
                 left join cgm cgmpessoal on rh01_numcgm = cgmpessoal.z01_numcgm
                where {$where}
                order by idiasemana, periodo, disciplina, ativo, regente;
            ";
            $rs = db_query($sql);
            if (!$rs){
                throw new Exception("Erro ao buscar grade de horário.");
            }

            $retorno->grade = array();
            if (pg_num_rows($rs) > 0) {
                $retorno->grade = db_utils::makeCollectionFromRecord($rs, function ($dado){
                    $dado->ativo = $dado->ativo === 't';
                    $dado->lancou_frequencia = $dado->ativo == 1;
                    return $dado;
                });
            }

            break;

        case 'remover':
            if (empty($parametros->turma)) {
                throw new Exception("Informe a turmna");
            }

            if (empty($parametros->etapa)) {
                throw new Exception("Informe a etapa");
            }

            $turma = new Turma($parametros->turma);
            $etapa = new Etapa($parametros->etapa);

            $retorno->codigos = [];
            $horarios = JSON::create()->parse($parametros->horarios);
            foreach ($horarios as $horario) {
                $retorno->codigos[] = $horario->codigo;
                GradeHorarioService::removerRegentePermanente($turma, $etapa, $horario->codigo, $horario->rechumano);
            }


            $retorno->mensagem = "Período removido da grade de horário";
            break;
        case 'atualizaGrade':
            if (empty($parametros->gradeHorarios)) {
                throw new Exception("Não foi informado a grade.");
            }

            $gradeHorarios = JSON::create()->parse($parametros->gradeHorarios);
            if (!is_array($gradeHorarios)) {
                throw new Exception("Não foi possível parsear a grade.");
            }
            foreach ($gradeHorarios as $periodoAula) {

                if ($periodoAula->rechumano == 0) {
                    $dao = new cl_regenciahorariodiscsemreg();
                    $dao->ed175_codigo = $periodoAula->codigo;
                    $dao->ed175_ativo = $periodoAula->ativo ? 'true' : 'false' ;
                    $dao->ed175_datainicio = $periodoAula->data_inicio;
                    $dao->ed175_datafim = $periodoAula->data_fim;
                } else {
                    $dao = new cl_regenciahorario();
                    $dao->ed58_i_codigo = $periodoAula->codigo;
                    $dao->ed58_ativo = $periodoAula->ativo ? 'true' : 'false' ;
                    $dao->ed58_datainicio = $periodoAula->data_inicio;
                    $dao->ed58_datafim = $periodoAula->data_fim;
                }
                $dao->alterar($periodoAula->codigo);
                if ($dao->erro_status == '0') {
                    throw new Exception($dao->erro_status);
                }
            }
            $retorno->mensagem = "Grade atualizada com sucesso.";
            break;
    }
} catch (Exception $exception) {
    $retorno->mensagem = $exception->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);

echo JSON::create()->stringify($retorno);
