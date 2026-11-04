<?php

use ECidade\RecursosHumanos\RH\Recadastramento\Processar;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/JSON.php"));

class Recadastramento
{

    private $request;
    private $response;

    public function __construct($request)
    {
        if (!empty($request["json"])) {
            $this->json = new services_json();
            $this->request = $this->json->decode(str_replace("\\", "", $request["json"]));
        } else {
            $this->request = (object)$request;
        }

        $this->response = (object)array(
            'success' => false,
            "message" => "",
            "data" => []
        );
        $this->route();
    }

    public function route()
    {
        set_time_limit(-1);
        ini_set('memory_limit', '-1');

        switch ($this->request->route) {
            case "dados":
                $this->getDados();
                break;
            case "conferencia":
                $this->getDadosConferencia();
                break;
            case "aprovados-instituicao":
                $this->getAprovadosPorInstituicaoDaSessao();
                break;
            case "processar":
                $this->processar();
                break;
            case "atualizar-json":
                $this->atualizarJson();
                break;
        }
    }

    public function getDadosConferencia()
    {
        $filtros = $this->request->dados;
        $where = [];

        if (!empty($filtros->cargo)) {
            array_push($where, "rh02_funcao = {$filtros->cargo}");
        }

        if (!empty($filtros->lotacao)) {
            array_push($where, "r70_codigo = {$filtros->lotacao}");
        }

        $mesusu = db_mesfolha();
        $anousu = db_anofolha();
        $clausula = "";

        if (!empty($where)) {
            $clausula = "and " . implode(" and ", $where);
        }

        $sql = "
            WITH  atendimento AS (
            SELECT
                            concat(ov01_numero, '/', ov01_anousu) AS numero_atendimento,
                            ov02_cnpjcpf,
                            ov01_requerente,
                            p58_codproc,
                            p58_instit,
                            CASE
                                WHEN trim(p51_itemmenu) = '' THEN nomeinst
                                ELSE p51_itemmenu
                            END AS orgao,
                            CASE
                                WHEN p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%' THEN
                            'APROVADO'
                                WHEN p68_codproc IS NOT NULL THEN
                            'REJEITADO'
                                ELSE
                            'REALIZADO/AGUARDANDO ATENDIMENTO'
                            END AS status,
                            CASE
                                WHEN p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%' THEN
                                1
                                WHEN p68_codproc IS NOT NULL THEN
                                2
                                ELSE
                                3
                            END AS status_codigo,
                        ov01_dataatend as data_atendimento
                        FROM
                            ouvidoriaatendimentoprocessoeletronico
                        INNER JOIN ouvidoriaatendimento ON
                            ov01_sequencial = ov33_ouvidoriaatendimento
                        INNER JOIN ouvidoriaatendimentocidadao ON
                            ov10_ouvidoriaatendimento = ov01_sequencial
                        INNER JOIN ouvidoria.cidadao ON
                            ov02_sequencial = ov10_cidadao
                        LEFT JOIN processoouvidoria ON
                            ov09_ouvidoriaatendimento = ov01_sequencial
                        LEFT JOIN protprocesso ON
                            p58_codproc = ov09_protprocesso
                        LEFT JOIN arqproc ON
                            p68_codproc = p58_codproc
                        LEFT JOIN tipoproc ON
                            p51_codigo = ov01_tipoprocesso
                        LEFT JOIN db_config ON
                            p51_instit = codigo
                        WHERE
                            p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%'

            )
            SELECT
                        numero_atendimento,
                        rh01_numcgm AS cgm,
                        rh01_regist AS matricula,
                        z01_nome AS nome,
                        z01_cgccpf AS cpf,
                        r70_descr AS descricao_lotacao,
                        rh37_descr as cargo,
                        p58_codproc
                    FROM
                        rhpessoal
                    INNER JOIN rhpessoalmov ON
                        rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
                        AND rh02_mesusu = {$mesusu}
                        AND rh02_anousu = {$anousu}
                    INNER JOIN rhlota ON
                        rhlota.r70_codigo = rhpessoalmov.rh02_lota
                        AND rhlota.r70_instit = rhpessoalmov.rh02_instit
                    INNER JOIN cgm ON
                        cgm.z01_numcgm = rhpessoal.rh01_numcgm
                    INNER JOIN db_config ON codigo = rhpessoal.rh01_instit
                    INNER JOIN rhfuncao on rh37_funcao = rh02_funcao
                        and rh37_instit = rh02_instit
                    LEFT JOIN pessoal.rhpesrescisao  ON rh05_seqpes = rh02_seqpes
                    LEFT JOIN atendimento ON
                        z01_cgccpf = ov02_cnpjcpf
                        and p58_instit = rh01_instit
                    WHERE rh05_recis IS NULL
                        and rh01_admiss <= '2021-11-15'

                        and case when " . db_getsession('DB_instit') . " in (6,7,8) then
                            rh01_instit in (6,7,8) else
                        rh01_instit = " . db_getsession('DB_instit') . "
                        end
                        and status = 'APROVADO'
                        {$clausula}
                    ORDER BY 3;
        ";

        $rs = db_query($sql);
        $dados = pg_fetch_all($rs);
        $this->response->success = true;
        $this->response->data = $dados;
        echo JSON::create()->stringify($this->response);
    }

    public function getDados()
    {
        $mesusu = db_mesfolha();
        $anousu = db_anofolha();

        $sql = "

WITH  atendimento AS (
   SELECT
                concat(ov01_numero, '/', ov01_anousu) AS numero_atendimento,
                ov02_cnpjcpf,
                ov01_requerente,
                p58_instit,
                CASE
                    WHEN trim(p51_itemmenu) = '' THEN nomeinst
                    ELSE p51_itemmenu
                END AS orgao,
                CASE
                    WHEN p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%' THEN
                'APROVADO'
                    WHEN p68_codproc IS NOT NULL THEN
                'REJEITADO'
                    ELSE
                'REALIZADO/AGUARDANDO ATENDIMENTO'
                END AS status,
                 CASE
                    WHEN p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%' THEN
                    1
                    WHEN p68_codproc IS NOT NULL THEN
                    2
                    ELSE
                    3
                END AS status_codigo,
               ov01_dataatend as data_atendimento
            FROM
                ouvidoriaatendimentoprocessoeletronico
            INNER JOIN ouvidoriaatendimento ON
                ov01_sequencial = ov33_ouvidoriaatendimento
            INNER JOIN ouvidoriaatendimentocidadao ON
                ov10_ouvidoriaatendimento = ov01_sequencial
            INNER JOIN ouvidoria.cidadao ON
                ov02_sequencial = ov10_cidadao
            LEFT JOIN processoouvidoria ON
                ov09_ouvidoriaatendimento = ov01_sequencial
            LEFT JOIN protprocesso ON
                p58_codproc = ov09_protprocesso
            LEFT JOIN arqproc ON
                p68_codproc = p58_codproc
            LEFT JOIN tipoproc ON
                p51_codigo = ov01_tipoprocesso
            LEFT JOIN db_config ON
                p51_instit = codigo
            WHERE
                 p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%'
)
SELECT
            rh01_numcgm AS cgm,
            rh01_regist AS matricula,
            z01_nome AS nome,
            z01_cgccpf AS cpf,
            rh02_lota AS codigo_lotacao,
            r70_descr AS descricao_lotacao,
            nomeinst AS nome_instituicao,
            atendimento.*,
            coalesce(status,'NÃO REALIZADO') as status,
            rh05_recis
        FROM
            rhpessoal
        INNER JOIN rhpessoalmov ON
            rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
            AND rh02_mesusu = {$mesusu}
            AND rh02_anousu = {$anousu}
        INNER JOIN rhlota ON
            rhlota.r70_codigo = rhpessoalmov.rh02_lota
            AND rhlota.r70_instit = rhpessoalmov.rh02_instit
        INNER JOIN cgm ON
            cgm.z01_numcgm = rhpessoal.rh01_numcgm
        INNER JOIN db_config ON codigo = rhpessoal.rh01_instit
        LEFT JOIN pessoal.rhpesrescisao  ON rh05_seqpes = rh02_seqpes
        LEFT JOIN atendimento ON
            z01_cgccpf = ov02_cnpjcpf
            and p58_instit = rh01_instit
        WHERE rh05_recis IS NULL
            and rh01_admiss <= '2021-11-15'
        ORDER BY 3;
    ";

        $rs = db_query($sql);
        $dados = pg_fetch_all($rs);
        $this->response->success = true;
        $this->response->data = $dados;
        echo JSON::create()->stringify($this->response);
    }

    private function getAprovadosPorInstituicaoDaSessao()
    {

        $offset = is_numeric($this->request->offset) ? $this->request->offset : 0;
        $limit = is_numeric($this->request->limit) ? $this->request->limit : 10;
        $order = "ORDER BY cpf";
        if (!empty($this->request->sort) and !empty($this->request->order)) {
            $order = "ORDER BY {$this->request->sort} {$this->request->order}";
        }

        $where = "";
        if (!empty($this->request->searchMatricula)) {
            $matriculas = explode(",", $this->request->searchMatricula);
            $matriculasAux = [];
            foreach ($matriculas as $matricula) {
                if (empty($matricula)) {
                    continue;
                }
                $matriculasAux[] = $matricula;
            }
            if (count($matriculasAux) > 0) {
                $where .= " AND  rh01_regist IN (".join(",", $matriculasAux).") ";
            }
        }

        if (!empty($this->request->nome)) {
            $where.= "AND z01_nome ilike '%{$this->request->nome}%'";
        }

        $mesusu = db_mesfolha();
        $anousu = db_anofolha();
        $instituicao = db_getsession("DB_instit");
        if ($instituicao == 6) {
            $instituicao = '6,7,8';
        }

        $paginate = "LIMIT {$limit} OFFSET {$offset}";

        db_query("BEGIN;");

        try {
            db_query("drop table  IF EXISTS  tmp_atendimento_aux;");
            db_query("CREATE TEMP TABLE tmp_atendimento_aux AS (
                            SELECT
                                concat(ov01_numero, '/', ov01_anousu) AS numero_atendimento,
                                ov02_cnpjcpf,
                                ov01_requerente,
                                CASE
                                    WHEN trim(p51_itemmenu) = '' THEN nomeinst
                                    ELSE p51_itemmenu
                                END AS orgao,
                                CASE
                                    WHEN p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%' THEN
                                'APROVADO'
                                    WHEN p68_codproc IS NOT NULL THEN
                                'REJEITADO'
                                    ELSE
                                'REALIZADO/AGUARDANDO ATENDIMENTO'
                                END AS status,
                                 CASE
                                    WHEN p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%' THEN
                                    1
                                    WHEN p68_codproc IS NOT NULL THEN
                                    2
                                    ELSE
                                    3
                                END AS status_codigo,
                               ov01_dataatend as data_atendimento,
                               ov33_informacoesprocesso as json,
                               p58_dtproc AS aceito_data,
                               p58_hora AS aceito_hora
                            FROM
                                ouvidoriaatendimentoprocessoeletronico
                            INNER JOIN ouvidoriaatendimento ON
                                ov01_sequencial = ov33_ouvidoriaatendimento
                            INNER JOIN ouvidoriaatendimentocidadao ON
                                ov10_ouvidoriaatendimento = ov01_sequencial
                            INNER JOIN ouvidoria.cidadao ON
                                ov02_sequencial = ov10_cidadao
                            LEFT JOIN processoouvidoria ON
                                ov09_ouvidoriaatendimento = ov01_sequencial
                            LEFT JOIN protprocesso ON
                                p58_codproc = ov09_protprocesso
                            LEFT JOIN arqproc ON
                                p68_codproc = p58_codproc
                            LEFT JOIN tipoproc ON
                                p51_codigo = ov01_tipoprocesso
                            LEFT JOIN db_config ON
                                p51_instit = codigo
                            WHERE
                                p58_obs ILIKE '%Recadastramento acesso arquivado pelo sistema%'
                            and p51_instit IN ({$instituicao})
            );");

            $sql = "
                    SELECT [FIELDS] FROM (
                    SELECT
                    DISTINCT ON (rh01_regist,numero_atendimento)
                        rh01_numcgm AS cgm,
                        rh01_regist AS matricula,
                        z01_nome AS nome,
                        z01_cgccpf AS cpf,
                        rh02_lota AS codigo_lotacao,
                        r70_descr AS descricao_lotacao,
                        nomeinst AS nome_instituicao,
                        rh01_instit as codigo_instituicao,
                        tmp_atendimento_aux.*,
                        coalesce(status,'NÃO REALIZADO') as status,
                        rh05_recis,
                        json
                    FROM
                        rhpessoal
                    INNER JOIN rhpessoalmov ON
                        rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
                        AND rh02_mesusu = {$mesusu}
                        AND rh02_anousu = {$anousu}
                    INNER JOIN rhlota ON
                        rhlota.r70_codigo = rhpessoalmov.rh02_lota
                        AND rhlota.r70_instit = rhpessoalmov.rh02_instit
                    INNER JOIN cgm ON
                        cgm.z01_numcgm = rhpessoal.rh01_numcgm
                    INNER JOIN db_config ON codigo = rhpessoal.rh01_instit
                    LEFT JOIN pessoal.rhpesrescisao  ON rh05_seqpes = rh02_seqpes
                    INNER JOIN tmp_atendimento_aux ON
                        z01_cgccpf = ov02_cnpjcpf
                    WHERE rh05_recis IS NULL
                    AND rh02_instit IN ({$instituicao})
                    AND rh01_regist NOT IN (
                         SELECT h260_regist
                            FROM
                          recursoshumanos.processamentorecadastramento WHERE h260_status  = true AND h260_regist = rh01_regist
                        )
                    [WHERE]
                    ORDER BY matricula,numero_atendimento ASC, aceito_data DESC, aceito_hora DESC
                    [PAGINATE]
                    ) AS tabela_aux
                    WHERE TRUE
                    [ORDER]
                    ;";

            $sql_com_paginacao = $sql;
            $sql_com_paginacao = str_replace(
                "[FIELDS]",
                "*",
                $sql_com_paginacao
            );

            $sql_com_paginacao = str_replace(
                "[PAGINATE]",
                $paginate,
                $sql_com_paginacao
            );

            $sql_com_paginacao = str_replace(
                "[ORDER]",
                $order,
                $sql_com_paginacao
            );

            $sql_com_paginacao = str_replace(
                "[WHERE]",
                $where,
                $sql_com_paginacao
            );

            $rs = db_query($sql_com_paginacao);
            $dados = pg_fetch_all($rs);


            $sql_total = $sql;
            $sql_total = str_replace(
                "[FIELDS]",
                "count(*) as total",
                $sql_total
            );

            $sql_total = str_replace(
                "[PAGINATE]",
                "",
                $sql_total
            );

            $sql_total = str_replace(
                "[ORDER]",
                "",
                $sql_total
            );

            $sql_total = str_replace(
                "[WHERE]",
                $where,
                $sql_total
            );

            $rs = db_query($sql_total);
            $total = pg_fetch_object($rs);


            db_query("drop table  IF EXISTS  tmp_atendimento_aux;");

            $this->response->rows = !empty($dados) ? $dados : [];
            $this->response->total = (int)$total->total;
            $this->response->success = true;
            db_query("COMMIT;");
        } catch (\Exception $ex) {
            db_query("ROLLBACK;");
        }

        echo JSON::create()->stringify($this->response);
    }

    private function processar()
    {
        try {
            if (empty($this->request->selecao)) {
                throw new \Exception("Não encontrado seleção");
            }

            $erros = array();

            foreach ($this->request->selecao as $servidor) {
                $servidor = (object) $servidor;
                try {
                    $atendimento = explode("/", $servidor->numero_atendimento);
                    if (count($atendimento) != 2) {
                        throw new \Exception(
                            "Atendimento referente a matricula {$servidor->matricula}  tem formato inválido!"
                        );
                    }
                    $processar = new Processar();
                    $processar->setInstituicao(db_getsession("DB_instit"));
                    $processar->setMatricula($servidor->matricula);
                    $processar->setAtendimentoNumero($atendimento[0]);
                    $processar->setAtendimentoAno($atendimento[1]);
                    $processar->setInstituicaoMatricula($servidor->codigo_instituicao);
                    $processar->run();
                } catch (\Exception $ex) {
                      $erros[] = $ex->getMessage();
                }
            }

            $this->response->success = true;
            if (empty($erros)) {
                $this->response->message = "Processado com sucesso!";
            } else {
                $this->response->message = "Processado com pendências: ".join("<br><br>", $erros);
            }
        } catch (\Exception $ex) {
            $this->response->message = "Ocorreu um erro ao processar: ERRO {$ex->getMessage()}";
        }
        echo JSON::create()->stringify($this->response);
    }


    private function atualizarJson()
    {

        try {
            if (empty($this->request->atendimento)) {
                throw new \Exception("Não encontrado atendimento");
            }

            if (empty($this->request->formulario)) {
                throw new \Exception("Não encontrado formulario");
            }

            $atendimento = explode("/", $this->request->atendimento);
            if (count($atendimento) != 2) {
                throw new \Exception("Atendimento tem formato inválido!");
            }

            require_once(modification(ECIDADE_PATH . 'model/ouvidoria/AtendimentoOuvidoria.model.php'));
            $atendimento = \AtendimentoOuvidoria::findByNumeroAnoInstituicao(
                $atendimento[0],
                $atendimento[1],
                db_getsession("DB_instit")
            );

            if (!$atendimento) {
                throw new \Exception("Atendimento não encontrado!");
            }

            require_once(modification(ECIDADE_PATH . 'model/ouvidoria/AtendimentoProcessoEletronico.model.php'));

            $atendimentoProcessoEletronico = \AtendimentoProcessoEletronico::findByAtendimento(
                $atendimento->getId()
            );

            if (!$atendimentoProcessoEletronico) {
                throw new \Exception("Não encontrado a solicitação de atendimento!");
            }



            $atendimentoProcessoEletronico->setInformacoesProcesso(
                $this->request->formulario
            );

            $atendimentoProcessoEletronico->save();

            $this->response->success = true;
            $this->response->message = "Atualizado com sucesso";
        } catch (\Exception $ex) {
            $this->response->message = $ex->getMessage();
        }
        echo JSON::create()->stringify($this->response);
    }
}

new Recadastramento($_REQUEST);
