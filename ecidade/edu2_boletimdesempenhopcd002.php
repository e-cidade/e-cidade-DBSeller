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

use ECidade\Pdf\Pdf;
use App\Domain\Educacao\Escola\Enums\AtendimentosEspecialEnum;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

$oGet = db_utils::postMemory($_GET);

try {
    $periodosWhere = "";
    $periodosLista = [];
    if (!empty($oGet->periodos)) {
        $periodosLista  = explode(",", $oGet->periodos);
        $periodosLista = array_map(function ($data) {
            return "'".(new DBDate(date($data)))->getDate(DBDate::DATA_EN)."'";
        }, $periodosLista);
    }
    if (count($periodosLista) > 0) {
        $periodosWhere = " AND ed197_data_emissao IN (".implode(",", $periodosLista).")";
    }

    $pdf = new Pdf();
    $pdf->init(false);
    $pdf->exibeHeader(true, \Fpdf\Pdf::HEADER_ESCOLA);
    $pdf->setExibeBrasao(true);
    $pdf->addTitulo("Boletim Parecer Descritivo - AEE", 1);
    $pdf->SetAutoPageBreak(true, 15);
    $dataAtual = new DBDate(date("Y-m-d"));

    if (empty($oGet->calendario) || empty($oGet->turma) || empty($oGet->alunos)) {
        $pdf->addPage();
        $pdf->cell(191, 4, "Não foram informados os filtros para emissão do relatório.", 0, 1, "C", 0);
        $pdf->outPut();
        exit();
    }

    if (!checarAtendimentos($oGet->alunos, $periodosWhere)) {
        $pdf->addPage();
        $pdf->cell(191, 4, "Nenhum registro encontrado.", 0, 1, "C", 0);
        $pdf->outPut();
        exit();
    }

    $escolaInformada = EscolaRepository::getEscolaByCodigo($oGet->escola);
    if (!empty($oGet->iAssinaturaAdicional)) {
        $sNomeDocente = "";
        $sFuncaoDocente = "Funcionário";
        if (!empty($oGet->iAssinaturaAdicional)) {
            $oDocente = DocenteRepository::getDocenteByCodigoRecursosHumano($oGet->iAssinaturaAdicional);
            $sNomeDocente = $oDocente->getNome();
            $sFuncaoDocente = '';

            foreach ($oDocente->getAtividades($escolaInformada) as $oAtividades) {
                if (isset($oGet->iAtividade) && $oAtividades->getAtividade()->getCodigo() == $oGet->iAtividade) {
                    $sFuncaoDocente = $oAtividades->getAtividade()->getDescricao();
                }
            }
        }

        $assAdicional = new stdClass();
        $assAdicional->nome = $sNomeDocente;
        $assAdicional->cargo = $sFuncaoDocente;
    }
    
    $calendario = $oGet->calendario;
    $escola = $oGet->escola;
    $turma = $oGet->turma;
    $turmaNome = base64_decode($oGet->turmaNome);

    $obsevacao = base64_decode($oGet->sObs);
    $secretarioNome = base64_decode($oGet->secretario);
    $diretorNome = base64_decode($oGet->diretor);
    $profissionalEspecializadoNome = base64_decode($oGet->profissionalEspecializado);
    $diaSistema = date("d", db_getsession("DB_datausu"));
    $mesSistema = date("m", db_getsession("DB_datausu"));
    $anoSistema = date("Y", db_getsession("DB_datausu"));

    $tiposAtendimentosEspeciais = AtendimentosEspecialEnum::all();
    
    $oInstit = new Instituicao(db_getsession("DB_instit"));
    $localData = $oInstit->getMunicipio().", ".(new DBDate(date('d/m/Y')))->dataPorExtenso();
    $listaAlunos = explode(",", $oGet->alunos);
    
    foreach ($listaAlunos as $aluno) {
        if (checarAtendimentos($aluno, $periodosWhere)) {
            $pdf->addPage();
        } else {
            continue;
        }

        $sqlDadosAluno = "
        SELECT ed47_v_nome as nome, ed47_d_nasc as datanascimento, ed11_c_descr as etapa,
        exists(
            select 1 from escola.necessidadealunocadeirante where 
            ed189_aluno={$aluno}
        ) as cadeirante, ed60_matricula as matricula
        FROM aluno
        JOIN matricula on matricula.ed60_i_aluno = aluno.ed47_i_codigo
        JOIN matriculaserie on matriculaserie.ed221_i_matricula = matricula.ed60_i_codigo
        JOIN serie on serie.ed11_i_codigo = matriculaserie.ed221_i_serie
        WHERE aluno.ed47_i_codigo = {$aluno}
        ORDER BY matricula.ed60_d_datamatricula DESC LIMIT 1
        ";

        $resultAluno = db_query($sqlDadosAluno);
        $alunoCorrente = db_utils::fieldsMemory($resultAluno, 0);
        $idadeCompleta = "";
        if (!empty($alunoCorrente->datanascimento)) {
            $quantidadeDias = DBDate::calculaIntervaloEntreDatas(
                $dataAtual,
                new DBDate($alunoCorrente->datanascimento),
                'd'
            );
            $idadeCompleta = DBDate::getIdadeCompleta($quantidadeDias);
        }

        $listaDeficienciasAluno = [];
        $sqlDeficienciasAluno = "
        SELECT 
        DISTINCT ed48_c_descr as deficiencia, 
        CASE WHEN ed187_sequencial is not null then ed185_descricao else '' END as subdivisao
        FROM aluno
        JOIN alunonecessidade ON alunonecessidade.ed214_i_aluno = aluno.ed47_i_codigo
        JOIN necessidade ON necessidade.ed48_i_codigo = alunonecessidade.ed214_i_necessidade
        LEFT JOIN necessidadesubdivisao ON necessidadesubdivisao.ed185_necessidade = necessidade.ed48_i_codigo
        LEFT JOIN necessidadesubdivisaoaluno ON 
            necessidadesubdivisaoaluno.ed187_necessidadesubdivisao = necessidadesubdivisao.ed185_sequencial
        WHERE aluno.ed47_i_codigo = $aluno
        ";
        
        $resultDeficenciasAluno = db_query($sqlDeficienciasAluno);
        for ($i = 0; $i < pg_num_rows($resultDeficenciasAluno); $i++) {
            $deficienciaCorrente = db_utils::fieldsMemory($resultDeficenciasAluno, $i);
            $listaDeficienciasAluno[] = !empty($deficienciaCorrente->subdivisao)
                ? $deficienciaCorrente->deficiencia." - ".$deficienciaCorrente->subdivisao
                : $deficienciaCorrente->deficiencia;
        }

        $dadosAluno = [
            'nome' => $alunoCorrente->nome,
            'data_nascimento' => db_formatar($alunoCorrente->datanascimento, "d"),
            'idade' => $idadeCompleta,
            'cadeirante' => $alunoCorrente->cadeirante == 't' ? 'Sim' : 'Não',
            'matricula' => $alunoCorrente->matricula,
            'etapa_matricula' => $alunoCorrente->etapa,
            'turma' => $alunoCorrente->turma,
            'pcd' => implode(", ", $listaDeficienciasAluno),
            'turma_nome' => $turmaNome
        ];
        imprimirHeader($pdf, $dadosAluno);

        $sqlAtendimentosAluno = "
        select distinct * 
        from escola.alunoatendimentoespecial
        where ed197_aluno = {$aluno} $periodosWhere
        order by ed197_data_emissao
        ";

        $resultAtendimentosAluno = db_query($sqlAtendimentosAluno);
        for ($j = 0; $j < pg_num_rows($resultAtendimentosAluno); $j++) {
            $atendimento = db_utils::fieldsMemory($resultAtendimentosAluno, $j);
            
            $dadosAtendimentos = [
                'observacao' => $obsevacao,
                'parecer' => $atendimento->ed197_parecer,
                'data_atendimento' => db_formatar($atendimento->ed197_data_emissao, 'd')
            ];

            $tiposAtendimentosLista = [];
            $tiposAtendimentos = json_decode($atendimento->ed197_atendimentos);
            if (count($tiposAtendimentos) > 0) {
                foreach ($tiposAtendimentos as $tiposAtendimento) {
                    if (key_exists($tiposAtendimento, $tiposAtendimentosEspeciais)) {
                        $tiposAtendimentosLista[] = $tiposAtendimentosEspeciais[$tiposAtendimento];
                    }
                }
            }
            
            $dadosAtendimentos['tipos_atendimentos'] = count($tiposAtendimentosLista) > 0 
                ? implode(", ", $tiposAtendimentosLista) 
                : [];

            $sqlRecursosUtilizados = "
            SELECT recursosutilizados.ed198_descricao
            FROM escola.recursosatendimentos 
            JOIN escola.recursosutilizados ON 
                recursosutilizados.ed198_codigo = recursosatendimentos.ed199_recursosutilizados
            WHERE ed199_alunoatendimentos = {$atendimento->ed197_codigo}";

            $listaRecursos = [];
            $resultRecursosUtilizados = db_query($sqlRecursosUtilizados);
            for ($k = 0; $k < pg_num_rows($resultRecursosUtilizados); $k++) {
                $recurso = db_utils::fieldsMemory($resultRecursosUtilizados, $k);
                $listaRecursos[] = $recurso->ed198_descricao;
            }

            $dadosAtendimentos['recursos_utilizados'] = count($listaRecursos) > 0 ? implode(", ", $listaRecursos) : [];
            imprimirAtendimentos($pdf, $dadosAtendimentos);
        }

        if (strlen($obsevacao) > 0) {
            $pdf->ln();
            $pdf->setfont('arial', 'b', 7);
            $pdf->cell(42, 4, "Observação Geral:", 0, 1, "L", 0);
            $pdf->setfont('arial', '', 7);
            $pdf->multiCell(191, 4, $obsevacao, "LTRB", "L", 0);
        }

        $dados = [
            'profissional_especializado_nome' => $profissionalEspecializadoNome,
            'secretario_nome' => $secretarioNome,
            'diretor_nome' => $diretorNome,
            'local_data' => $localData,
            'assinatura_adicional_nome' => $assAdicional->nome,
            'assinatura_adicional_cargo' => $assAdicional->cargo,
        ];
        imprimirAssinaturas($pdf, $dados);
    }
    $pdf->outPut();
} catch (Exception $e) {
    $sMsg = urlencode($e->getMessage());
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $sMsg);
}

function imprimirAssinaturas($pdf, $dados)
{
    $linha = "______________________________________________________";
    $profissionalEspecializado = "#linha" .
        "#profissionalEspecializado".
        "\n" . "Profissional Especializado";
    
    $orientadorEducacional = "#linha" .
        "#orientadorEducacional".
        "\n" . $dados['assinatura_adicional_cargo'];
    
    $secretarioEscolar = "#linha" .
        "#secretarioEscolar".
        "\n" . "Secretário Escolar";
    
    $diretorEscolar = "#linha" .
        "#diretorEscolar".
        "\n" . "Diretor";

    $profissionalEspecializado = ajustarAssinatura(
        $dados['profissional_especializado_nome'],
        "#profissionalEspecializado",
        $profissionalEspecializado
    );
    $orientadorEducacional = ajustarAssinatura(
        $dados['assinatura_adicional_nome'],
        "#orientadorEducacional",
        $orientadorEducacional
    );
    $secretarioEscolar = ajustarAssinatura($dados['secretario_nome'], "#secretarioEscolar", $secretarioEscolar);
    $diretorEscolar = ajustarAssinatura($dados['diretor_nome'], "#diretorEscolar", $diretorEscolar);

    $profissionalEspecializado = str_replace("#linha", $linha, $profissionalEspecializado);
    $orientadorEducacional = str_replace("#linha", $linha, $orientadorEducacional);
    $secretarioEscolar = str_replace("#linha", $linha, $secretarioEscolar);
    $diretorEscolar = str_replace("#linha", $linha, $diretorEscolar);

    $pdf->setfont('arial', 'b', 7);
    $pdf->ln();
    $pdf->cell(42, 4, $dados['local_data'], 0, 1, "L", 0);

    $largura = ($pdf->getW()) / 2;
    $pdf->ln(5);
    $posYinicial = $pdf->getY();
    $posXinicial = $pdf->getX();
    $possuiProfEspecializado = false;
    $possuiAssinaturaAdicional = false;
    
    if (strlen($dados['profissional_especializado_nome']) > 0) {
        $pdf->multiCell($largura-10, 4, $profissionalEspecializado, 0, "C", 0, 0);
        $possuiProfEspecializado = true;
    }
    if (strlen($dados['assinatura_adicional_nome']) > 0) {
        $pdf->setXY($largura, $posYinicial);
        $pdf->multiCell($largura-10, 4, $orientadorEducacional, 0, "C", 0, 0);
        $possuiAssinaturaAdicional = true;
    }

    $posYnova = $pdf->getY();
    $complementoPosY = 15;
    if ($possuiProfEspecializado || $possuiAssinaturaAdicional) {
        $complementoPosY = 4;
    }

    if (strlen($dados['secretario_nome']) > 0) {
        $pdf->setXY($posXinicial, $posYnova + $complementoPosY);
        $pdf->multiCell($largura-10, 4, $secretarioEscolar, 0, "C", 0, 0);
    }

    if (strlen($dados['diretor_nome']) > 0) {
        $pdf->setXY($largura, $posYnova + $complementoPosY);
        $pdf->multiCell($largura-10, 4, $diretorEscolar, 0, "C", 0, 0);
    }
}

function ajustarAssinatura($componente, $texto_substituir, $template)
{
    $assinatura = "\n" . "{$componente}";
    return !empty($componente)
        ? str_replace($texto_substituir, $assinatura, $template)
        : str_replace($texto_substituir, "", $template);
}

function imprimirHeader($pdf, $dados)
{
    criarAreaHeader($pdf, montarArrayConfiguracao(['Nome:', 9, 182, $dados['nome'], 0, 'cel']));
    $pdf->ln();
    criarAreaHeader($pdf, montarArrayConfiguracao(
        ['Data Nascimento:', 24, 42, $dados['data_nascimento'], 0, 0, 'cel'])
    );
    criarAreaHeader($pdf, montarArrayConfiguracao(['Idade:', 10, 56, $dados['idade'], 0, 0, 'cel']));
    criarAreaHeader($pdf, montarArrayConfiguracao(['Cadeirante:', 16, 16, $dados['cadeirante'], 1, 0, 'cel']));
    criarAreaHeader($pdf, montarArrayConfiguracao(['Matrícula:', 14, 52, $dados['matricula'], 0, 0, 'cel']));
    criarAreaHeader($pdf, montarArrayConfiguracao(
        ['Etapa da Matrícula:', 24, 42, $dados['etapa_matricula'], 0, 0, 'cel'])
    );
    criarAreaHeader($pdf, montarArrayConfiguracao(['Turma:', 10, 50, $dados['turma_nome'], 1, 0, 'cel']));
    criarAreaHeader($pdf, montarArrayConfiguracao(['PCD/Altas Habilidades:', 28, 191, $dados['pcd'], 1, 1, 'multi']));
    $pdf->ln();
}

function criarAreaHeader($pdf, $configuracoes)
{
    $pdf->setfont('arial', 'b', 7);
    $pdf->cell(
        $configuracoes['largura_label'],
        4,
        "{$configuracoes['label']}",
        0,
        $configuracoes['quebra_label'],
        "L",
        0
    );
    $pdf->setfont('arial', '', 7);
    $configuracoes['tipo_conteudo'] == 'cel'
        ? $pdf->cell(
            $configuracoes['largura_conteudo'],
            4,
            "{$configuracoes['conteudo']}",
            0,
            $configuracoes['quebra_linha'],
            "L",
            0
        )
        : $pdf->multiCell(
            $configuracoes['largura_conteudo'],
            4,
            "{$configuracoes['conteudo']}",
            0,
            "L",
            0,
            0
        );
}

function montarArrayConfiguracao($dados)
{
    return [
        'label' => $dados[0],
        'largura_label' => $dados[1],
        'largura_conteudo' => $dados[2],
        'conteudo' => $dados[3],
        'quebra_linha' => $dados[4],
        'quebra_label' => $dados[5],
        'tipo_conteudo' => $dados[6]
    ];
}

function imprimirAtendimentos($pdf, $dados)
{
    $pdf->ln();
    $pdf->setfont('arial', 'b', 7);
    $pdf->cell(42, 4, "Tipos de Atendimentos realizados:", 0, 1, "L", 0);
    $pdf->setfont('arial', '', 7);
    $pdf->multiCell(191, 4, mb_convert_encoding($dados['tipos_atendimentos'], 'ISO-8859-1', 'UTF-8'), "LTRB", "L", 0);
    $pdf->ln();

    $pdf->setfont('arial', 'b', 7);
    $pdf->cell(26, 4, "Recursos Utilizados:", 0, 1, "L", 0);
    $pdf->setfont('arial', '', 7);
    $pdf->multiCell(191, 4, $dados['recursos_utilizados'], "LTRB", "L", 0);
    $pdf->ln();

    $pdf->setfont('arial', 'b', 7);
    $pdf->cell(20, 4, "Parecer:", 0, 1, "L", 0);
    $pdf->setfont('arial', '', 7);
    $pdf->multiCell(191, 4, $dados['parecer'], "LTRB", "L", 0);
    $pdf->setfont('arial', 'b', 7);

    $pdf->cell(178, 4, "Data do Registro: ", 0, 0, "R", 0);
    $pdf->setfont('arial', '', 7);
    $pdf->cell(13, 4, $dados['data_atendimento'], 0, 0, "R", 0);
    $pdf->ln();

    $pdf->cell(132, 4, $dados['localHora'], 0, 0, "L", false);
}

function checarAtendimentos($idsAlunos, $periodosWhere)
{
    $sqlAtendimentosAluno = "
    SELECT count(*) as atendimentos
    FROM 
    (
        select distinct * 
        from escola.alunoatendimentoespecial 
        where ed197_aluno in ($idsAlunos) $periodosWhere
    ) as temp
    ";
    $resultAtendimentosAluno = db_query($sqlAtendimentosAluno);
    $recurso = db_utils::fieldsMemory($resultAtendimentosAluno, 0);
    if ($recurso->atendimentos > 0) {
        return true;
    }
    return false;
}
