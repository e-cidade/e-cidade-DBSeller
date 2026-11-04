<?php

use ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Relatorio\ImpressaoMatricialMapaColeta;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification('libs/db_utils.php'));

$retorno = new stdClass();
$retorno->erro = false;
$retorno->mensagem = '';

try {
    $oGet = db_utils::postMemory($_GET);

    $data1 = explode('/', $oGet->dataInicial);
    $data2 = explode('/', $oGet->dataFinal);

    $dataInicial = date("Y-m-d", mktime(0, 0, 0, $data1[1], $data1[0], $data1[2]));
    $dataFinal = date("Y-m-d", mktime(0, 0, 0, $data2[1], $data2[0], $data2[2]));

    $codigoLaboratorio = $oGet->laboratorio;
    $codigoSetor = $oGet->setor;

    if (empty($oGet->laboratorio) ||
        empty($oGet->dataInicial) ||
        empty($oGet->dataFinal)
    ) {
        throw new Exception("É necessário informar todos os campos do filtro.");
    }

    $whereSetor = '';
    if (!empty($codigoSetor)) {
        $whereSetor .= "AND lab_labsetor.la24_i_setor = {$codigoSetor}";
    }

    /*
     * Consulta principal
     */
    $sqlConteudo = "
        SELECT
            lab_requisicao.la22_i_codigo,
            lab_requisicao.la22_d_data,
            cgs_und.z01_v_nome,
            lab_exame.la08_c_sigla,
            la21_c_situacao
        FROM lab_requisicao
        INNER JOIN lab_requiitem
            ON lab_requisicao.la22_i_codigo = lab_requiitem.la21_i_requisicao
        INNER JOIN lab_setorexame
            ON lab_requiitem.la21_i_setorexame = lab_setorexame.la09_i_codigo
        INNER JOIN lab_labsetor
            ON lab_setorexame.la09_i_labsetor = lab_labsetor.la24_i_codigo
        INNER JOIN lab_exame
            ON lab_setorexame.la09_i_exame = lab_exame.la08_i_codigo
        INNER JOIN cgs_und
            ON cgs_und.z01_i_cgsund = lab_requisicao.la22_i_cgs
        WHERE
            la22_d_data BETWEEN '{$dataInicial}' AND '{$dataFinal}'
            AND lab_labsetor.la24_i_laboratorio = {$codigoLaboratorio}
            {$whereSetor}
            AND lab_requiitem.la21_c_situacao LIKE('30 - Coletado')
        ORDER BY
            cgs_und.z01_v_nome,
            lab_requisicao.la22_i_codigo
    ";

    $postgresObject = db_query($sqlConteudo);

    if (pg_num_rows($postgresObject) === 0) {
        throw new Exception('Não foram encontradas as informações com os dados informados no filtro.');
    }

    $rs = pg_fetch_all($postgresObject);

    $conteudo = new stdClass();
    $conteudo->cabecalho = new stdClass();

    /*
     * Busca nome do Laboratório
     */
    $sqlLaboratorios = "
        SELECT 
            la02_i_codigo,
            la02_c_descr,
            la02_c_endereco,
            la02_c_numero,
            la02_i_telefone
        FROM lab_laboratorio
        WHERE la02_i_codigo = {$codigoLaboratorio}
    ";

    $pgOjbectLaboratorio = db_query($sqlLaboratorios);

    if (pg_num_rows($pgOjbectLaboratorio) === 0) {
        throw new Exception('Ocorreu um erro. Laboratório não encontrado.');
    }

    $rsLaboratorio = pg_fetch_assoc($pgOjbectLaboratorio);

    /*
     * Monta estrutra utilizada para elaborar o relatório.
     */
    $conteudo->cabecalho->nomeLaboratorio = trim($rsLaboratorio[0]['la02_c_descr']);
    $conteudo->cabecalho->periodoInicial = $oGet->dataInicial;
    $conteudo->cabecalho->periodoFinal = $oGet->dataFinal;

    /*
     * Busca para validar Setor
     */
    if(!empty($codigoSetor)) {
        $sqlSetor = "
            SELECT la24_i_codigo, la23_c_descr
            FROM lab_labsetor
            INNER JOIN lab_setor
                ON lab_labsetor.la24_i_setor = la23_i_codigo
            WHERE
                la24_i_laboratorio = {$codigoLaboratorio}
                AND la24_i_setor = {$codigoSetor}
        ";

        $pgObjectSetor = db_query($sqlSetor);

        if (pg_num_rows($pgObjectSetor) === 0) {
            throw new Exception('Ocorreu um erro. Setor não encontrado.');
        }

        $setor = pg_fetch_assoc($pgObjectSetor, 0)['la23_c_descr'];
        $conteudo->cabecalho->nomeSetor = $setor;
    }

    $conteudo->coletas = array();

    $codigoRequisicaoAnterior = $rs[0]['la22_i_codigo'];
    $coleta = new stdClass();
    foreach($rs as $key => $row) {
        if ($codigoRequisicaoAnterior != $row['la22_i_codigo'] || $key == 0) {
            if ($key != 0) {
                $conteudo->coletas[] = $coleta;
                $coleta = new stdClass();
            }

            $coleta->nome = $row['z01_v_nome'];
            $coleta->requisicao = $row['la22_i_codigo'];
            $coleta->dataRequisicao = $row['la22_d_data'];

            $arrayData =  explode('-', $coleta->dataRequisicao);
            $coleta->dataRequisicao = date("d/m/Y", mktime(0, 0, 0, $arrayData[1], $arrayData[2], $arrayData[0]));
            $coleta->exames = array();
        }

        $coleta->exames[] = trim($row['la08_c_sigla']);
        $codigoRequisicaoAnterior = $row['la22_i_codigo'];
    }

    /*
     * Adiciona a última coleta, retornada na consulta,
     * na estrutura.
     */
    $conteudo->coletas[] = $coleta;

    /*
     * Consulta dados cabeçalho
     */
    $campos = 'trim(ender) as rua, munic, numero, uf, cgc, telef, email, url, logo';
    $pgDadosCabecalho = db_query(
        $conn,
        "select {$campos}
        from db_config where codigo = " . db_getsession("DB_instit")
    );

    $rsDadosCabecalho = pg_fetch_assoc($pgDadosCabecalho, 0);
    $dadosCabecalho = new stdClass();

    $conteudo->cabecalho->pathImagem = 'imagens/files/' . $rsDadosCabecalho[0]['logo'];
    $conteudo->cabecalho->laboratorio = $rsLaboratorio['la02_c_descr'];
    $conteudo->cabecalho->municipioDepartamento = $rsDadosCabecalho['munic'];
    $conteudo->cabecalho->ufDepartamento = $rsDadosCabecalho['uf'];
    $conteudo->cabecalho->enderecoLaboratorio = trim($rsLaboratorio['la02_c_endereco']) . ', ' . trim($rsLaboratorio['la02_c_numero']);
    $conteudo->cabecalho->telefoneLaboratorio = trim($rsLaboratorio['la02_i_telefone']);
    $conteudo->cabecalho->emailDepartamento = trim($rsDadosCabecalho['email']);
    $conteudo->cabecalho->cnpjDepartamento = $rsDadosCabecalho['cgc'];
    $conteudo->cabecalho->siteDepartamento = $rsDadosCabecalho['url'];

    /*
     * Imprime relatório conforme modelo informado.
     * 1 - PDF
     * 2 - Matricial
     */
    if ($oGet->modelo == 1) {
        include_once('lab2_mapacoletapdf.php');
    } else {
        $impressaoMatricial = new ImpressaoMatricialMapaColeta();
        $impressaoMatricial->gerarArquivo($conteudo);
        $retorno->mensagem = "Dados enviados para a impressora.";
        $retorno->dados = $impressaoMatricial->getConteudo();
        $retorno->utilizarAutenticadoraNova = $impressaoMatricial->isUtilizarAutenticadoraNova();
    }

} catch (Exception $e) {
    if ($oGet->modelo == 1) {
        db_redireciona('db_erros.php?db_erro=' . urlencode($e->getMessage()));
    } else {
        $retorno->erro = true;
        $retorno->mensagem = $e->getMessage();
    }
}

if ($oGet->modelo == 2) {
    echo JSON::create()->stringify($retorno);
}
