<?php
namespace ECidade\RecursosHumanos\ESocial\Repository;

use Servidor;

class LotacaoServidor extends \BaseClassRepository
{
    public static function buscarLotacaoTributaria(
        Servidor $servidor,
        $anoCompetencia,
        $mesCompetencia,
        $anoCompetenciaAtual = null,
        $mesCompetenciaAtual = null
    ) {
        if (empty($mesCompetenciaAtual)) {
            $mesCompetenciaAtual = $mesCompetencia;
        }

        if (empty($anoCompetenciaAtual)) {
            $anoCompetenciaAtual = $anoCompetencia;
        }

        //Criação do mapa de trabalho para saber os dias em cada lotacao tributária
        $periodos = self::getLotacaoPorPeriodo(
            $servidor,
            $anoCompetencia,
            $mesCompetencia,
            $anoCompetenciaAtual,
            $mesCompetenciaAtual
        );
        foreach ($periodos as $periodo) {
            $mapaTrabalho[] =
                $dadomapa = new \stdClass();
                $dadomapa->dataInicio = $periodo->datainicio;
                $dadomapa->dataFim = $periodo->datafim;
                $dadomapa->lotacaoTributaria = $periodo->lotacaotributaria;
                $dadomapa->dias = 0;
                $dadomapa->ultimaLotacao =  false;
        }
        $lotacoes = [];
        if (!isset($mapaTrabalho)) {
            return [];
        }
        foreach ($mapaTrabalho as $key => $dadomapa) {
            if (!in_array($dadomapa->lotacaoTributaria, $lotacoes)) {
                $lotacoes[] = $dadomapa->lotacaoTributaria;
            }
        }

        if (count($lotacoes) == 1) {
            $mapaTrabalho[0]->dias = 30;
            $mapaTrabalho[0]->ultimaLotacao = true;
            return [$mapaTrabalho[0]];
        }
        $datasTrabalho = [];
        foreach ($lotacoes as $key => $lotacao) {
            foreach ($mapaTrabalho as $dataTrabalho) {
                if ($dataTrabalho->lotacaoTributaria == $lotacao) {
                    $datasTrabalho[$lotacao]['inicio'] = $dataTrabalho->dataInicio;
                    $datasTrabalho[$lotacao]['fim'] = $dataTrabalho->dataFim;
                }
            }
        }
        $diasTotais = 0;
        $primeiroDia = $anoCompetencia . "-" . $mesCompetencia . "-01";
        $ultimoDia = date('Y-m-t', strtotime($primeiroDia));
        $dias = [];
        $numeroLotacoes = count($lotacoes);
        $y = $numeroLotacoes;
        foreach ($datasTrabalho as $lotacao => $diasTrabalhado) {
            $dataFimJornada = $diasTrabalhado['fim'];
            if (empty($dataFimJornada)) {
                $dataFimJornada = $ultimoDia;
            }
            $dataInicioJornada = $diasTrabalhado['inicio'];

            if (date('Y-m-d', strtotime($dataInicioJornada))< date('Y-m-d', strtotime($primeiroDia))) {
                $dataInicioJornada = $primeiroDia;
            }
            $diasJornada = self::retornaDiasEntreDatas($dataInicioJornada, $dataFimJornada);
            if ($diasJornada > 30) {
                $diasJornada = (int) ($diasJornada / $numeroLotacoes);
            }

            $y -= 1;
            $dias[$lotacao] = $diasJornada;
            if ($y == 0) {
                $dias[$lotacao] = 30 - $diasTotais;
            }
            $diasTotais += $diasJornada;
        }

        $z = $numeroLotacoes;
        foreach ($dias as $lotacao => $diasTrabalhado) {
            foreach ($mapaTrabalho as $key => $dadomapa) {
                if ($dadomapa->lotacaoTributaria == $lotacao) {
                    $mapaTrabalho[$key]->dias = $diasTrabalhado;
                }
            }
            $z -= 1;
            if ($z == 0) {
                $mapaTrabalho[$key]->ultimaLotacao = true;
            }
        }
        return $mapaTrabalho;
    }

    private function retornaDiasEntreDatas($data1, $data2)
    {
        $data_inicio = new \DateTime($data1);
        $data_fim = new \DateTime($data2);
        // Resgata diferença entre as datas
        $dias = $data_inicio->diff($data_fim);
        return $dias->days+1;
    }

    public static function buscarEstabelicimentoLotacao($matricula, $mesCompetencia, $anoCompetencia)
    {
        $grupoIdeEstabLot = new \stdClass();
        $grupoIdeEstabLot->tpInsc = 1;
        $grupoIdeEstabLot->nrInsc = '';
        $grupoIdeEstabLot->codLotacao = '';

        $sql = "
            select
                b.cgc,
                c.rh268_codigolotacao
            from
                pessoal.rhpessoalmov a
            inner join configuracoes.db_config b on
                b.codigo = a.rh02_instit
            inner join recursoshumanos.rhlotacaotributaria c on
                c.rh268_numcgm = b.numcgm
            where
                a.rh02_regist = {$matricula}
            ";

        if (!empty($anoCompetencia)) {
            $sql .= " and a.rh02_anousu = {$anoCompetencia}";
        }
        if (!empty($mesCompetencia)) {
            $sql .= " and a.rh02_anousu = {$mesCompetencia}";
        }

        $resultado = db_query($sql);
        $registro = pg_numrows($resultado);
        if ($registro > 0) {
            $grupoIdeEstabLot->nrInsc = \db_utils::fieldsMemory($resultado, 0)->cgc;
            $grupoIdeEstabLot->codLotacao = \db_utils::fieldsMemory($resultado, 0)->rh268_codigolotacao;
        }
        return $grupoIdeEstabLot;
    }

    /*
    * Retorna a identificação de lotacao/lotações no periodo de competência definido.
    */
    public function getLotacaoPorPeriodo(
        \Servidor $servidor,
        $ano = null,
        $mes = null,
        $anoCompetenciaAtual = null,
        $mesCompetenciaAtual = null
    ) {

        if (empty($ano)) {
            $ano = \DBPessoal::getAnoFolha();
        }

        if (empty($mes)) {
            $mes = \DBPessoal::getMesFolha();
        }
        if (empty($anoCompetenciaAtual)) {
            $anoCompetenciaAtual = \DBPessoal::getAnoFolha();
        }

        if (empty($mesCompetenciaAtual)) {
            $mesCompetenciaAtual = \DBPessoal::getMesFolha();
        }

        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $ano = str_pad($ano, 4, '0', STR_PAD_LEFT);
        $instituicaoServidor = $servidor->getCodigoInstituicao();
        $matricula = $servidor->getMatricula();

        $daoRhLocalTrab = new \cl_rhlocaltrab();
        $where  = " rh55_instit = {$instituicaoServidor}";
        $where .= " and rh02_anousu = {$anoCompetenciaAtual}";
        $where .= " and rh02_mesusu = {$mesCompetenciaAtual}";
        $where .= " and rh02_regist = {$matricula}";
        $order = "rh56_datainicio, rh56_datafim";

        $sql = $daoRhLocalTrab->sql_query_servidor(
            "distinct (rh56_datainicio, rh55_lotacaotributaria),
            rh55_lotacaotributaria as lotacaoTributaria,
            rh56_datainicio as datainicio,
            rh56_datafim as datafim,
            date_part('year', rh56_datainicio) as anoInicio,
            date_part('month', rh56_datainicio) as mesInicio,
            date_part('year', rh56_datafim) as anoFim,
            date_part('month', rh56_datafim) as mesFim,
            lag(rh56_datafim) over (order by rh56_datafim) as dataFimAnterior
            ",
            $where,
            $order
        );
        $result = db_query($sql);
        // Validamos se não retorna nenhuma configuração de local de trabalho
        if (pg_num_rows($result) == 0) {
            // caso nao tenha, buscamos mais uma vez se existe pelo menos 1 local de trabalho, mesmo não preenchido os
            // dados de data inicio
            $sql = $daoRhLocalTrab->sql_query_servidor(
                "distinct on (rh56_datainicio)
                rh55_lotacaotributaria as lotacaoTributaria,
                rh56_datainicio as datainicio,
                rh56_datafim as datafim,
                date_part('year', rh56_datainicio) as anoInicio,
                date_part('month', rh56_datainicio) as mesInicio,
                date_part('year', rh56_datafim) as anoFim,
                date_part('month', rh56_datafim) as mesFim,
                lag(rh56_datafim) over (order by rh56_datafim) as dataFimAnterior
                ",
                $where,
                $order
            );
            $result = db_query($sql);
            //Caso exista mais de 1 lotacao tributaria configurada, retornamos erro
            if (pg_num_rows($result) > 1) {
                throw new DBException("Mais de uma lotação tributária para a matricula: {$matricula}.");
            }
            /**
             * Caso não exista Nenhuma lotação tributaria vinculada ao local de trabalho ainda
             *  Verificamos a quantidade de lotacoes configuradas para a instituicao
             *  Caso seja apenas 1, o servidor será vinculado a essa lotacao tributaria
             */
            if (pg_num_rows($result) == 0) {
                $instituicao = \InstituicaoRepository::getInstituicaoByCodigo($instituicaoServidor);
                $codigoCgm = $instituicao->getCgm()->getCodigo();
                $sql = <<<SQL
                    select
                        rh268_codigolotacao as lotacaoTributaria,
                        null as datainicio,
                        null as datafim,
                        $ano as anoinicio,
                        $mes as mesinicio,
                        null as anoFim,
                        null as mesFim,
                        null as dataFimAnterior
                    from
                        recursoshumanos.rhlotacaotributaria
                    where
                        rh268_numcgm = $codigoCgm
                    ;
SQL;
                $result = \db_query($sql);
                //Caso exista mais de 1 lotacao tributaria configurada, retornamos erro
                if (pg_num_rows($result) != 1) {
                    throw new \DBException("Erro ao buscar a lotação tributária da Matricula: {$matricula}.");
                }
            }
        }

        $lotacao = [];
        for ($row = 0; $row < pg_num_rows($result); $row++) {
            $lotacao[] = \db_utils::fieldsMemory($result, $row);
        }

        foreach ($lotacao as $key => $value) {

            /**
             * Caso não possua Data Fim.
             */
            if (empty($value->datafim)) {
                if (($value->anoinicio == $ano) && $value->mesinicio == $mes) {
                    continue;
                }
            } else {
                /**
                 * Ano Menor que ano de Competência.
                 */
                if ($value->anofim < $ano) {
                    unset($lotacao[$key]);
                }

                /**
                 * Ano Igual e mês menor.
                 */
                if ($value->anofim == $ano && $value->mesfim < $mes) {
                    unset($lotacao[$key]);
                }

                /**
                 * Ano e mês Inferior a competência Atual.
                 */
                if (!empty($value->datafim) &&
                    ($value->anofim < $ano) &&
                    ($value->mesfim < $mes)) {
                    unset($lotacao[$key]);
                }
            }

            if ($value->anoinicio > $ano) {
                unset($lotacao[$key]);
            }

            if ($value->anoinicio == $ano && $value->mesinicio > $mes) {
                unset($lotacao[$key]);
            }
        }
        return $lotacao;
    }
}
