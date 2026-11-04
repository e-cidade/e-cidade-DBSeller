<?php
namespace ECidade\RecursosHumanos\ESocial\Service;

use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use BusinessException;
use ECidade\RecursosHumanos\ESocial\Agendamento\ProcessamentoStaticFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Integracao\ESocial;
use ECidade\RecursosHumanos\ESocial\Model\JobEsocial;

use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\V3\Extension\Registry;
use FilaESocialTask;
use Exception;
use stdClass;
use db_utils;
use ServidorRepository;

/**
 * Class ProcessamentoExternoService
 * @package ECidade\RecursosHumanos\ESocial\Service
 * @author   Lucas Jarrier de Aquino Cavalcanti - lucas.cavalcanti@dbseller.com.br
 */
class ProcessamentoExternoService
{
    
    public static function processamentoExterno($evento, $dados)
    {
        $retorno = new stdClass();
        if ($evento) {
            $alteracao = false;
            switch ($evento) {
                case Tipo::S1010:
                    $instituicao = db_getsession("DB_instit");
                    $processamentoInstance = ProcessamentoStaticFactory::factory(
                        2,
                        $dados->cgm,
                        $instituicao,
                        "1010"
                    );
                    if (!empty($dados->rubrica)) {
                        $temp[] = $dados->rubrica;
                        $processamentoInstance->setlistaRubricas($temp);
                    }
                    $alteracao = $processamentoInstance->processar();
                    $titulo = Tipo::getDescricoes("1010");
                    $retorno->sMessage = "Processamento realizado para o arquivo {$titulo}.";
                    echo json_encode(['msg' => $retorno]);
                    break;
                case Tipo::S2200:
                    $instituicao = db_getsession("DB_instit");
                    $competencia = CompetenciaHelper::get();
                    $servidores = ServidorRepository::getServidoresByMatriculas(
                        $competencia->getAno(),
                        $competencia->getMes(),
                        [$dados->matricula]
                    );
                    $processamentoInstance = ProcessamentoStaticFactory::factory(
                        Tipo::SERVIDOR,
                        $dados->empregador,
                        $instituicao,
                        "2200"
                    );
                    
                    $processamentoInstance->setServidores($servidores);
                    $alteracao = $processamentoInstance->processar();
                    echo json_encode($alteracao);
                    $titulo = Tipo::getDescricoes("2200");
                    break;
                default:
                    throw new Exception("Nenhum evento foi informado, contate o suporte.");
            }
            if (!$alteracao) {
                $retorno->sMessage = "Não encontramos alterações para o arquivo {$titulo}.";
            } else {
                /**
                 * Etapa de Envio para eSocial.
                 */
                $processamentoExterno = new ProcessamentoExternoService();
                $processamentoExterno::enviarEventosParaApi($evento, $dados);
            }
        } else {
            throw new BusinessException("Nenhum evento foi informado, contate o suporte.");
        }
    }

    /**
     * Faz o envio do processamento externo para a Api do eSocial.
     */
    private static function enviarEventosParaApi($evento, $dados)
    {
        $parametros = new stdClass();
        $parametros->cgm = '';
        $parametros->layout = '';
        $parametros->tipo = '';
        $parametros->indicativoPeriodoApuracao = '';
        $parametros->ano = '';
        $parametros->mes = '';

        if ($evento) {
            switch ($evento) {
                case Tipo::S1010:
                    $parametros->cgm = $dados->cgm;
                    $parametros->layout = '1010';
                    $parametros->tipo = 2;
                    break;
                case Tipo::S2200:
                    $parametros->cgm = $dados->empregador;
                    $parametros->layout = '2200';
                    $parametros->tipo = 3;
                    break;
                default:
                    throw new Exception("Nenhum evento foi informado, contate o suporte.");
            }
        }

        if (empty($parametros->cgm)) {
            $mensagem = "É necessário escolher um empregador para realizar o envio das informações.";
            throw new \ParameterException($mensagem);
        }

        if (isset($dados->rubrica) && !empty($dados->rubrica)) {
            $parametros->matriculas[] = $dados->rubrica;
            $parametros->rubricas[] = $dados->rubrica;
        }
        /**
         * Realiza um ping na API para verificar configurações e disponibilidade do serviço
         */
        $esocialApi = new ESocial(Registry::get('app.config'), "/ping");
        $response = $esocialApi->request("GET");

        $where = " rh213_situacao = 1 and rh213_empregador = " . $parametros->cgm;
        $join = '';
        
        if (!empty($parametros->layout)) {
            $where .= " AND rh213_evento =  '{$parametros->layout}'";
            switch ($parametros->layout) {
                default:
                    if (!empty($parametros->matriculas)) {
                        $where .= DadosESocial::buscaCondicaoResponsavelPreenchimento(
                            $parametros->tipo,
                            $parametros->matriculas,
                            (object)[
                            "ano" => $parametros->ano,
                            "mes" => $parametros->mes
                            ],
                            $parametros->indicativoPeriodoApuracao
                        );
                    }
                    break;
            }
        }
        $daoEsocialEnvio = new \cl_esocialenvio();
        $sqlEsocialEnvio = $daoEsocialEnvio->sql_query_file(null, "*", null, $where, $join);
        if ($parametros->layout == '1010') {
            $codigoInstituicao = empty($parametros->instituicao)
                ? db_getsession('DB_instit') : $parametros->instituicao;
            $subQueryRubrica = explode('from', $sqlEsocialEnvio);
            $subQueryRubrica[1] = str_replace(
                "esocialenvio",
                "from esocial.esocialenvio
                inner join pessoal.rhrubricas  on rh27_rubric = rh213_responsavelpreenchimento
                and rh27_ativo
                and rh27_instit = {$codigoInstituicao} ",
                $subQueryRubrica[1]
            );
            $sqlEsocialEnvio = $subQueryRubrica[0] . $subQueryRubrica[1];
        }

        $rsEsocialEnvio = db_query($sqlEsocialEnvio);
        if (!$rsEsocialEnvio) {
            throw new Exception("Não foi possível buscar os dados dos preenchimentos.\nContate o suporte.");
        }

        if (pg_num_rows($rsEsocialEnvio) == 0) {
            $mensagem = "Nenhum preenchimento pendente foi encontrado. Realize as alterações necessárias e processe"
                . " novamente os dados.";
            throw new Exception($mensagem);
        }
        $esocialEnvios = db_utils::getCollectionByRecord($rsEsocialEnvio);
        
        foreach ($esocialEnvios as $envio) {
            $job = new JobEsocial();
            $job->setNome('FilaESocialTask');
            
            $task = new FilaESocialTask();
            $task->setTarefa($job);
            $task->iniciar($envio->rh213_sequencial);
        }
    }

    public static function empregador()
    {
        $inst = db_getsession('DB_instit');
        $sql = "select
        distinct z01_numcgm as cgm,
       z01_cgccpf as documento,
       z01_nome as nome,
       r70_instit as instituicao
   from
       pessoal.rhlota
   inner join protocolo.cgm on
       cgm.z01_numcgm = rhlota.r70_numcgm
   where
       r70_ativo is true
       and r70_instit = '{$inst}'
   order by
       z01_numcgm";
        
        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            $result = [];
            for ($i = 0; pg_num_rows($rs) > $i; $i++) {
                $result[] = db_utils::fieldsMemory($rs, $i);
            }
            return $result;
        }
    }

    public static function vinculo($matricula)
    {
        $inst = db_getsession('DB_instit');
        $sql = "select
        rhg.rh30_vinculoemprego
        from
            pessoal.rhpessoalmov rhmov
        inner join pessoal.rhregime rhg on
            rhg.rh30_codreg = rhmov.rh02_codreg
        where
        rhmov.rh02_regist = '{$matricula}'
        and rhmov.rh02_instit = '{$inst}'";
        $rs = db_query($sql);
        if (pg_num_rows($rs) == 0) {
            return false;
        } else {
            for ($i = 0; pg_num_rows($rs) >= $i; $i++) {
                return db_utils::fieldsMemory($rs, $i);
            }
        }
    }
}
