<?php

namespace ECidade\Educacao\Escola\Relatorios\GuiaTransferencia\Services;

use CalendarioRepository;
use cl_transfescolarede;
use cl_transfescolafora;
use cl_escoladiretor;
use cl_diarioavaliacao;
use cl_procavaliacao;
use cl_matricula;
use cl_progressaoparcialaluno;
use libdocumento;
use DBDate;
use EscolaProcedencia;
use ProgressaoParcialAluno;

/**
 * Class TrocaAlunosTurmaService
 * @package App\Domain\Educacao\Escola\Services
 */
class GuiaTransFerenciaService
{
    public $tipoTransferencia;
    public $alunos;
    public $obs;
    public $bolsa;
    public $isDeclaracao;
    /**
     * GuiaTransFerenciaService constructor.
     * @param $tipoTransferencia
     * @param $alunos
     */
    public function __construct($tipoTransferencia, $alunos, $obs, $bolsa, $isDeclaracao)
    {
        $this->tipoTransferencia = $tipoTransferencia;
        $this->alunos = $alunos;
        $this->obs = $obs;
        $this->bolsa = $bolsa;
        $this->isDeclaracao = $isDeclaracao;
    }

    public function buscaDadosImpressao()
    {
        $dados = [];
        if ($this->tipoTransferencia == 'TR') {
            $cltransfescolarede = new cl_transfescolarede();
            $campos  = " ed47_c_bolsafamilia,ed47_i_codigo,ed47_v_nome,ed47_d_nasc, ";
            $campos .= " censomunicnat.ed261_c_nome as ed47_i_censomunicnat,";
            $campos .= " censoufnat.ed260_c_sigla as ed47_i_censoufnat, ";
            $campos .= " ed47_v_pai,ed47_v_mae,ed103_d_data as data_transf,ed11_c_descr as descr_serie, ";
            $campos .= " ed10_c_descr as descr_ensino,ed10_c_abrev as abrev_ensino,ed103_t_obs as obs_transf, ";
            $campos .= " ed60_i_turma as turmaorigem,ed57_c_descr as descr_turma,";
            $campos .= " censomunic.ed261_c_nome as cidade,ed60_c_parecer as neeparecer,";
            $campos .= " ed103_i_matricula as codigomatricula,";
            $campos .= " (SELECT ed29_c_descr || ' - ' || ed10_c_abrev ";
            $campos .= "     from turma ";
            $campos .= "         inner join base     on ed31_i_codigo = ed57_i_base ";
            $campos .= "         inner join cursoedu on ed29_i_codigo = ed31_i_curso  ";
            $campos .= "         inner join ensino   on ed10_i_codigo = ed29_i_ensino  ";
            $campos .= "   where ed57_i_codigo = ed60_i_turma) as descr_ensino_anterior,";
            $campos .= " calendario.ed52_i_codigo, escoladestino.ed18_c_nome as escola_destino, ";
            $campos .= " escoladestino.ed18_i_codigo as codigo_escola_destino ";

            $sql = $cltransfescolarede->sql_query(
                "",
                $campos,
                "to_ascii(ed47_v_nome)",
                " ed103_i_codigo in ({$this->alunos})"
            );
            $resource = pg_query($sql);

            while ($alunos = pg_fetch_object($resource)) {
                $dados['alunos'][] = $alunos;
            }
            return $dados;
        }

        $cltransfescolafora = new cl_transfescolafora();

        $campos  = " ed47_c_bolsafamilia,ed47_i_codigo,ed47_v_nome,ed47_d_nasc, ";
        $campos .= " censomunicnat.ed261_c_nome as ed47_i_censomunicnat,censoufnat.ed260_c_sigla as ed47_i_censoufnat,";
        $campos .= " ed47_v_pai,ed47_v_mae,ed104_d_data as data_transf,ed104_t_obs as obs_transf, ";
        $campos .= " censomunic.ed261_c_nome as cidade,ed104_i_matricula as codigomatricula, ";
        $campos .= " escolaproc.ed82_i_codigo as escola_destino, ed82_i_codigo as codigo_escola_destino";

        $sql = $cltransfescolafora->sql_query(
            "",
            $campos,
            "to_ascii(ed47_v_nome)",
            "ed104_i_codigo in ({$this->alunos})"
        );
        $result = pg_query($sql);
        while ($alunos =pg_fetch_object($result)) {
            $dados['alunos'][] = $alunos;
        }
        $dados['isDeclaracao'] = $this->isDeclaracao;
        return $dados;
    }

    public function montaDadosImpressao($arrAlunos)
    {
        foreach ($arrAlunos as $alunos) {
            foreach ($alunos as $aluno) {
                $aluno->matricula = $this->buscaDadosMatricula($aluno->codigomatricula);
                $aFiliacao = array();
                if ($aluno->ed47_v_mae != '') {
                        $aFiliacao[] = trim($aluno->ed47_v_mae);
                }
                if ($aluno->ed47_v_pai != '') {
                        $aFiliacao[] = trim($aluno->ed47_v_pai);
                }
                $dia_nasc = substr($aluno->ed47_d_nasc, 8, 2);
                $mes_nasc = substr($aluno->ed47_d_nasc, 5, 2);
                $ano_nasc = substr($aluno->ed47_d_nasc, 0, 4);
                $dia_transf = substr($aluno->data_transf, 8, 2);
                $mes_transf = substr($aluno->data_transf, 5, 2);
                $ano_transf = substr($aluno->data_transf, 0, 4);

                $oParagrafo = $this->isDeclaracao ?
                    new libdocumento(5027) :
                    new libdocumento(5010);

                $oParagrafo->nome_aluno = trim($aluno->ed47_v_nome);
                $oParagrafo->municipio_naturalidade = trim($aluno->ed47_i_censomunicnat);
                $oParagrafo->estado_naturalidade = $aluno->ed47_i_censoufnat;
                $oParagrafo->dia_nascimento = $dia_nasc;
                $oParagrafo->mes_nascimento = db_mes($mes_nasc, 1);
                $oParagrafo->ano_nascimento = $ano_nasc;

                if ($this->tipoTransferencia == "TF") {
                    $sEtapa =  trim($aluno->matricula[0]->descr_serie);
                    $sEnsino = sprintf(
                        '%s - %s',
                        trim($aluno->matricula[0]->descr_ensino),
                        trim($aluno->matricula[0]->abrev_ensino)
                    );

                    $oEscola        = new EscolaProcedencia($aluno->escola_destino);
                    $sEscolaDestino = $oEscola->getNome();
                    $aluno->descricaoEnsino = sprintf(
                        'Aproveitamento na Turma %s - %s',
                        trim($aluno->matricula[0]->descr_turma),
                        trim($aluno->matricula[0]->descr_serie)
                    );
                    $oCalendario = CalendarioRepository::getCalendarioByCodigo($aluno->matricula[0]->ed52_i_codigo);
                    $sDataInicio = $oCalendario->getDataInicio()->getDate('d/m/Y');
                    $sDataFim    = $oCalendario->getDataFinal()->getDate('d/m/Y');
                    $sObsFixa = sprintf(
                        "Período Letivo: %s até %s Escola de Destino: %s",
                        $sDataInicio,
                        $sDataFim,
                        $sEscolaDestino
                    );
                } else {
                    $sEtapa  = trim($aluno->descr_serie);
                    $sEnsino = sprintf('%s - %s', trim($aluno->descr_ensino), trim($aluno->abrev_ensino));
                    $oCalendario = CalendarioRepository::getCalendarioByCodigo($aluno->ed52_i_codigo);
                    $sDataInicio = $oCalendario->getDataInicio()->getDate('d/m/Y');
                    $sDataFim    = $oCalendario->getDataFinal()->getDate('d/m/Y');
                    $sDescricaoEnsino = explode(" - ", trim($aluno->descr_ensino_anterior));
                    $aluno->descricaoEnsino = sprintf(
                        'Aproveitamento na Turma %s - %s',
                        trim($sEnsino),
                        $sDescricaoEnsino[0]
                    );
                    $sObsFixa = sprintf(
                        "Período Letivo: %s até %s Escola de Destino: %s",
                        $sDataInicio,
                        $sDataFim,
                        $aluno->escola_destino
                    );
                }

                $oParagrafo->filiacao = implode(' e ', $aFiliacao);
                $oParagrafo->dia_transferencia = $dia_transf;
                $oParagrafo->mes_transferencia = db_mes($mes_transf, 1);
                $oParagrafo->ano_transferencia = $ano_transf;
                $oParagrafo->etapa = $sEtapa;
                $oParagrafo->ensino = $sEnsino;
                $oParagrafo->ano_matricula = isset($aluno->ed52_i_ano) ? $aluno->ed52_i_ano : "";

                $paragrafo = $oParagrafo->getDocParagrafos();
                $aluno->atestado = $paragrafo[1]->oParag->db02_texto;
                $sEscolaDestino = $aluno->escola_destino;

                if ($this->tipoTransferencia == 'TF') {
                    $oEscola        = new EscolaProcedencia($aluno->escola_destino);
                    $sEscolaDestino = $oEscola->getNome();
                }

                $bolsa = "";
                if ($this->bolsa == 2) {
                    $bolsa = $aluno->ed47_c_bolsafamilia == "S" ? "Bolsa Família Ativo" : "";
                }

                if (empty($this->obs) &&
                    empty($sObsProgressaoParcialAluno) &&
                    empty($aluno->obs_transf) &&
                    empty($bolsa)
                ) {
                    $sObservacao = ".......................................................................";
                } else {
                    $obs = urldecode(utf8_decode($this->obs));
                    $sObservacao = "OBS: ".
                        (trim($aluno->obs_transf) != '' ? $aluno->obs_transf."\n" : '').
                        (trim($obs) != '' ? $obs."\n" : '').
                        (trim($bolsa) != '' ? $bolsa."\n" : '');
                }

                $sObservacao .= $sObsFixa ;
                $aluno->obs = $sObservacao ;
                $aluno->cidadeDataTransf = sprintf(
                    '%s, %s de %s de %s',
                    trim($aluno->cidade),
                    trim($dia_transf),
                    db_mes($mes_transf, 1),
                    $ano_transf
                );
            }
        }
        return $arrAlunos;
    }


    public function buscaDadosMatricula($codigomatricula)
    {
        $dados = [];
        if ($this->tipoTransferencia == "TF") {
            $clmatricula = new cl_matricula();
            $campos = " ed47_i_codigo, ed47_c_bolsafamilia, serie.ed11_c_descr as descr_serie, ";
            $campos .= " ensino.ed10_c_descr as descr_ensino, ed10_c_abrev as abrev_ensino, ";
            $campos .= " matricula.ed60_i_turma as turmaorigem, turma.ed57_c_descr as descr_turma, ed40_i_codigo, ";
            $campos .= " matricula.ed60_c_parecer as neeparecer,";
            $campos .= " calendario.ed52_i_codigo, calendario.ed52_i_ano ";
            $sqlMatricula = $clmatricula->sql_query("", $campos, "", "ed60_i_codigo = {$codigomatricula}");
            $result_matr = pg_query($sqlMatricula);

            while ($matricula = pg_fetch_object($result_matr)) {
                $dados[] = $matricula;
            }
        }
        return $dados;
    }
}
