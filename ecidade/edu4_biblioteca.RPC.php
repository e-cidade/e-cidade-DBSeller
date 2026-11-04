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
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

define('MSG_EDU4_BIBLIOTECARPC', "educacao.biblioteca.edu4_bibliotecaRPC.");

$oJson                  = new services_json();
$oParam                 = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
$oRetorno               = new stdClass();
$oRetorno->iStatus      = 1;
$oRetorno->sMessage     = '';

$iDepartamento = db_getsession("DB_coddepto");

try {
    db_inicio_transacao();

    switch ($oParam->exec) {
        case "buscaCategoriaDeCarteira":
            $oRetorno->aCategorias = array();

            $sWhere  = " biblioteca.bi17_coddepto = {$iDepartamento} ";
            $sCampos = "bi07_codigo as codigo, bi07_nome as categoria ";

            $oDao = new cl_leitorcategoria;
            $sSql = $oDao->sql_query(null, $sCampos, "bi07_nome", $sWhere);
            $rs   = db_query($sSql);

            if (!$rs) {
                throw new DBException(
                    _M(MSG_EDU4_BIBLIOTECARPC . "erro_buscar_categoria_carteira")
                );
            }

            if (pg_num_rows($rs) == 0) {
                throw new Exception(
                    _M(MSG_EDU4_BIBLIOTECARPC . "nenhuma_categoria_carteira_cadastrada")
                );
            }

            $oRetorno->aCategorias = db_utils::getCollectionByRecord($rs);
            break;
        case "processarRelatorioDeLeitoresCSV":
            $sOrdem = 'bi16_codigo';
            if ($oParam->filtroOrdem == 'a') {
                $sOrdem = 'ov02_nome';
            }

            $iDepartamento = db_getsession("DB_coddepto");

            $sCampos  = "x.bi16_codigo, trim(ov02_nome) as ov02_nome, ov02_cnpjcpf, ov02_endereco, ov02_numero, ";
            $sCampos .= "case bi16_valida when 'S' then 'VÁLIDA' else 'VENCIDA' end as bi16_valida,";
            $sCampos .= "trim(bi07_nome) as categoria";

            $oDaoCarteira = new cl_carteira();
            $sSqlLeitores = $oDaoCarteira
                                ->sql_query_ultima_carteira(
                                    null,
                                    $sCampos,
                                    $sOrdem,
                                    null,
                                    $iDepartamento,
                                    $oParam->filtroCategoria
                                );
            $rsLeitores   = db_query($sSqlLeitores);

            $conteudo = "";
            $cabecalho = array("Nº Carteira","Nome", "CPF", "Endereço", "Categoria", "Situação");
        
            $linha = "RELATÓRIO DE LEITORES" . "\n";
            $linha .= implode(";", $cabecalho) . "\n";
            $conteudo .= $linha;
        
            for ($x=0; $x < pg_num_rows($rsLeitores); $x++) {
                db_fieldsmemory($rsLeitores, $x);
                $linha = [
                    trim($bi16_codigo),
                    trim($ov02_nome),
                    trim($ov02_cnpjcpf),
                    trim($ov02_endereco),
                    trim($categoria),
                    trim($bi16_valida)
                ];
                $linha = implode(";", $linha) . "\n";
                $conteudo .= $linha;
            }

            if (pg_num_rows($rsLeitores) > 0) {
                $arquivoCSV = "tmp/relatorio_biblioteca_leitores".time().".csv";
                file_put_contents($arquivoCSV, $conteudo);
                $oRetorno->pathRelatorioCSV = $arquivoCSV;
            } else {
                $oRetorno->iStatus = 0;
                $oRetorno->pathRelatorioCSV = "";
                $oRetorno->sMessage = "Nenhum registro encontrado para os filtros informados.";
            }
            break;
        case "processarRelatorioAcervoCSV":
            $clacervo = new cl_acervo;
            $clautoracervo = new cl_autoracervo;
            $classunto = new cl_assunto;
            $clexemplar = new cl_exemplar;
            $cllocalexemplar = new cl_localexemplar;

            $sql_autor = false;
            $where = " bi06_biblioteca = {$oParam->filtroBiblioteca} ";
            if ($oParam->filtroTipo != "") {
                $where .= " and bi06_tipoitem = {$oParam->filtroTipo}";
            }
            if ($oParam->filtroEditora != "") {
                $where .= " and bi06_editora = {$oParam->filtroEditora}";
            }
            if ($oParam->filtroClassificacao != "") {
                $where .= " and bi06_classiliteraria = {$oParam->filtroClassificacao}";
            }
            if ($oParam->filtroAutor != "") {
                $where .= " and bi21_autor = {$oParam->filtroAutor}";
                $sql_autor = true;
            }
            if ($oParam->filtroLocalizacao != "") {
                $where .= " and bi20localizacao = {$oParam->filtroLocalizacao}";
            }
            if ($oParam->filtroColecao != '') {
                $where .= " and bi06_colecaoacervo = {$oParam->filtroColecao}";
            }

            $campos = ["bi06_seq", "bi06_titulo", "bi06_subtitulo", "bi06_dataregistro",
            "bi09_nome", "bi20_sequencia", "bi29_abreviatura"];

            $conteudo = "";
            $cabecalho = array("Código Acervo","Título", "Subtítulo", "Data Registro", "Localização", "Local");
            
            if ($oParam->filtroRel == 'completo') {
                $camposAdicionais = ["bi17_nome", "bi06_titulooriginal", "bi22_idioma,
                bi23_anoedicao", "bi06_classcdd", "bi06_cutter", "bi23_isbn", "bi23_volume,
                bi05_nome", "bi02_nome", "bi03_classificacao", "bi04_forma", "bi23_anoedicao,
                bi23_edicao", "bi29_nome", "bi20_localizacao",
                "(select count(*) from exemplar where bi23_acervo = bi06_seq) as qtd", "bi23_codigo", "bi23_exemplar",
                "bi23_codbarras", "bi23_dataaquisicao", "bi23_paginacao", "bi23_tomo"
                ];
                $campos = array_merge($campos, $camposAdicionais);
                $cabecalho = array("Código Acervo", "Biblioteca", "Título", "Subtítulo", "Título Original", "Idioma",
                                "Data Registro", "Class. CDD", "Código Cutter", "Tipo Item", "Editora",
                                "Class. Literária", "Coleção", "Autores", "Assuntos", "Código Exemplar", "Exemplar",
                                "Cód. Barras", "Data Aquisição", "Tipo Aquisição", "Quantidade", "Ano Edição",
                                "Edição", "ISBN", "Paginação", "Volume", "Tomo", "Localizacao", "Local",
                                "Ordem de Localização"
                            );
            }

            $sql = $clacervo->sql_query("", implode(", ", $campos), $oParam->ordem, $where);
            if ($sql_autor) {
                $sql = $clacervo->sql_query_autores("", " DISTINCT " . $campos, $oParam->ordem, $where);
            }

            $result = db_query($sql);
            $linhasResult = pg_num_rows($result);

            $linha = "RELATÓRIO DE ACERVOS" . "\n";
            $linha .= implode(";", $cabecalho) . "\n";
            $conteudo .= $linha;
            
            for ($x=0; $x < $linhasResult; $x++) {
                db_fieldsmemory($result, $x);
                $sTitulo = substr($bi06_titulo, 0, 60);
                if (!empty($bi29_abreviatura)) {
                    $sTitulo .= " - {$bi29_abreviatura}";
                }
                
                $linha = [
                    $bi06_seq, $sTitulo, substr($bi06_subtitulo, 0, 63), db_formatar($bi06_dataregistro, 'd'),
                    $bi09_nome, $bi20_sequencia
                ];

                if ($oParam->filtroRel == 'completo') {
                    // Autores
                    $sqlAutorAcervo = $clautoracervo->sql_query(
                        "",
                        "bi01_nome",
                        "bi01_nome",
                        " bi21_acervo = $bi06_seq"
                    );
                    $result_autor = $clautoracervo->sql_record($sqlAutorAcervo);
                    $aAutores = [];
                    for ($y = 0; $y < $clautoracervo->numrows; $y++) {
                        db_fieldsmemory($result_autor, $y);
                        $aAutores[] = $bi01_nome;
                    }
                    $autores = implode(", ", $aAutores);
                    // Assuntos
                    $assuntos = [];
                    $sqlClassAssunto = $classunto->sql_query_file("", "bi15_assunto", "", " bi15_acervo = $bi06_seq");
                    $result_assunto = $classunto->sql_record($sqlClassAssunto);
                    for ($z = 0; $z < $classunto->numrows; $z++) {
                        db_fieldsmemory($result_assunto, $z);
                        $assuntos[] = $bi15_assunto;
                    }
                    $assuntosLista = implode(", ", $assuntos);

                    $linha = [
                        $bi06_seq, $bi17_nome, $sTitulo, substr($bi06_subtitulo, 0, 63), $bi06_titulooriginal,
                        $bi22_idioma, db_formatar($bi06_dataregistro, 'd'), $bi06_classcdd, $bi06_cutter, $bi05_nome,
                        $bi02_nome, $bi03_classificacao, $bi29_nome, $autores, $assuntosLista, $bi23_codigo,
                        $bi23_exemplar, $bi23_codbarras, db_formatar($bi23_dataaquisicao, 'd'), $bi04_forma, $qtd,
                        $bi23_anoedicao, $bi23_edicao, $bi23_isbn, $bi23_paginacao, $bi23_volume, $bi23_tomo,
                        $bi09_nome, $bi20_localizacao, $bi20_sequencia
                    ];
                }
                
                $linha = implode(";", $linha) . "\n";
                $conteudo .= $linha;
            }

            if ($linhasResult > 0) {
                $arquivoCSV = "tmp/relatorio_biblioteca_acervo".time().".csv";
                file_put_contents($arquivoCSV, $conteudo);
                $oRetorno->pathRelatorioCSV = $arquivoCSV;
            } else {
                $oRetorno->iStatus = 0;
                $oRetorno->pathRelatorioCSV = "";
                $oRetorno->sMessage = "Nenhum registro encontrado para os filtros informados.";
            }
            break;
        case "processarRelatorioItensEmprestadosCSV":
            $clemprestimoacervo = new cl_emprestimoacervo;
            $clbiblioteca = new cl_biblioteca;
            $hoje  = date("Y-m-d");

            $depto = db_getsession("DB_coddepto");
            $sSqlBiblioteca = $clbiblioteca->sql_query("", "bi17_codigo,bi17_nome", "", " bi17_coddepto = $depto");
            $rsBiblioteca   = $clbiblioteca->sql_record($sSqlBiblioteca);

            if ($clbiblioteca->numrows != 0) {
                db_fieldsmemory($rsBiblioteca, 0);
            }

            if ($oParam->filtro == 1) {
                $where  = "";
            } elseif ($oParam->filtro == 2) {
                $where  = " AND exists(select *
                                       from devolucaoacervo
                                      where devolucaoacervo.bi21_codigo = emprestimoacervo.bi19_codigo)";
            } elseif ($oParam->filtro == 3) {
                $where  = "AND not exists(select *
                                          from devolucaoacervo
                                         where devolucaoacervo.bi21_codigo = emprestimoacervo.bi19_codigo)";
            } elseif ($oParam->filtro == 4) {
                $where  = "AND bi18_devolucao < '$hoje'
                         AND not exists(select *
                                          from devolucaoacervo
                                          where devolucaoacervo.bi21_codigo = emprestimoacervo.bi19_codigo)";
            }

            $sql = "SELECT ov02_nome,
               bi18_retirada,
               bi18_devolucao,
               bi06_titulo,
               bi16_codigo,
               bi19_codigo,
               bi21_entrega
            FROM emprestimoacervo
                left  join devolucaoacervo  on bi21_codigo               = bi19_codigo
                inner join emprestimo       on bi18_codigo               = bi19_emprestimo
                inner join exemplar         on bi23_codigo               = bi19_exemplar
                inner join acervo           on bi06_seq                  = bi23_acervo
                inner join carteira         on bi16_codigo               = bi18_carteira
                inner join leitorcategoria  on bi07_codigo               = bi16_leitorcategoria
                inner join biblioteca       on bi17_codigo               = bi07_biblioteca
                inner join leitor           on bi10_codigo               = bi16_leitor
                left  join leitorcidadao    on leitorcidadao.bi28_leitor = leitor.bi10_codigo
                left  join cidadao          on cidadao.ov02_sequencial   = leitorcidadao.bi28_cidadao_sequencial
                                            and cidadao.ov02_seq          = leitorcidadao.bi28_cidadao_seq
            WHERE bi17_codigo = $bi17_codigo AND bi18_retirada BETWEEN '{$oParam->filtroData1}' AND '{$oParam->filtroData2}'
            $where";

            if ($oParam->filtroCarteira != "") {
                $sql .= " AND bi16_codigo = {$oParam->filtroCarteira}";
            }

            if ($oParam->filtroAcervo != "") {
                $sql .= " AND bi06_seq = {$oParam->filtroAcervo}";
            }

            $sql .= " ORDER BY bi18_retirada desc";

            $result = $clemprestimoacervo->sql_record($sql);
            $linhas = $clemprestimoacervo->numrows;

            $conteudo = "";
            $cabecalho = array("Código","Leitor", "Acervo", "Retirada", "Devolver até", "Devolvido em");

            $linha = "RELATÓRIO DE EMPRÉSTIMOS" . "\n";
            $linha .= implode(";", $cabecalho) . "\n";
            $conteudo .= $linha;
            
            for ($x=0; $x < $linhas; $x++) {
                db_fieldsmemory($result, $x);

                $sEntrega   = $bi21_entrega == "" ? "Não devolvido" : db_formatar($bi21_entrega, 'd');

                $linha = [
                    $bi19_codigo,
                    $ov02_nome,
                    $bi06_titulo,
                    db_formatar($bi18_retirada, 'd'),
                    db_formatar($bi18_devolucao, 'd'),
                    $sEntrega
                ];
                $linha = implode(";", $linha) . "\n";
                $conteudo .= $linha;
            }

            if ($linhas > 0) {
                $arquivoCSV = "tmp/relatorio_biblioteca_itens_emprestados".time().".csv";
                file_put_contents($arquivoCSV, $conteudo);
                $oRetorno->pathRelatorioCSV = $arquivoCSV;
            } else {
                $oRetorno->iStatus = 0;
                $oRetorno->pathRelatorioCSV = "";
                $oRetorno->sMessage = "Nenhum registro encontrado para os filtros informados.";
            }
            break;
    }

    db_fim_transacao(false);
} catch (Exception $eErro) {
    db_fim_transacao(true);
    $oRetorno->iStatus  = 2;
    $oRetorno->sMessage = $eErro->getMessage();
}
$oRetorno->erro = $oRetorno->iStatus == 2;
echo JSON::create()->stringify($oRetorno);
