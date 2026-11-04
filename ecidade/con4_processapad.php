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

//ini_set('memory_limit', -1);
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPcaspService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPlanoDespesaService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPlanoReceitaService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("libs/db_libtxt.php"));
// classes do pad
require_once(modification("con4_padbal_rec.php"));
require_once(modification("con4_padbal_desp.php"));
require_once(modification("con4_padbal_ver.php"));
require_once(modification("con4_padbver_enc.php"));
require_once(modification("con4_padcta_disp.php"));
require_once(modification("con4_padcta_oper.php"));
require_once(modification("con4_padrd_extra.php"));
require_once(modification("con4_padreceita.php"));
require_once(modification("con4_padrubrica.php"));
require_once(modification("con4_padempenho.php"));
require_once(modification("con4_padliquidac.php"));
require_once(modification("con4_padpagament.php"));
require_once(modification("con4_paddecreto.php"));
require_once(modification("con4_padorgao.php"));
require_once(modification("con4_paduniorcam.php"));
require_once(modification("con4_padfuncao.php"));
require_once(modification("con4_padsubfunc.php"));
require_once(modification("con4_padprograma.php"));
require_once(modification("con4_padprojativ.php"));
require_once(modification("con4_padcredor.php"));
require_once(modification("con4_padrecurso.php"));
require_once(modification("con4_padsubprog.php"));
require_once(modification("con4_padbrec_ant.php"));
require_once(modification("con4_padrec_ant.php"));
require_once(modification("con4_padbrub_ant.php"));
require_once(modification("con4_padbver_ant.php"));
require_once(modification("con4_padbvmovant.php"));
require_once(modification("con4_padtcelivrodiariogeral.php"));

$app = require_once ECIDADE_PATH . 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$tipo_rateio = 0;
db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING']);

$cldownload = new cl_download;
$cldb_config = new cl_db_config;

$instit = "";
$separa = "";
$sArquivo = "siapc";
if (isset($tipo) && $tipo != '') {
    $sArquivo = "mgs";
}



function exemplosSemVinculo($contas)
{
    $exemplos=[];
    $i = 0;
    foreach ($contas as $conta) {
        if ($i === 10) {
            return $exemplos;
        }
        $exemplos[] = $conta->c60_estrut;
        $i++;
    }
    return $exemplos;
}

/**
 * Comentado validação do mapeamento das contas
 * @param $exercicio
 * @return void
 * @throws Exception
 */
function validaContasNaoMapeadas($exercicio)
{
    echo "  <script>
              // parent.document.form1.processar.disabled = '';
               parent.js_removeObj('msgbox');
            </script>";
    // se a instituição é cãmara não deve validar receita.
    if (InstituicaoRepository::getInstituicaoSessao()->getTipo() == 2) {
        return true;
    }

    $msg = 'Exitem contas %s com movimento não mapeadas.';
    $receita = new MapeamentoPlanoReceitaService();
    $receita->filtros(['exercicio' => $exercicio, 'tipoPlano' => 'UF']);

    $receitaSemVinculoComMovimentacao = $receita->getContasSemVinculoComMovimentacao();
    if (count($receitaSemVinculoComMovimentacao)) {
        $exemplos = exemplosSemVinculo($receitaSemVinculoComMovimentacao);
        $menu = 'Contabilidade > Cadastros > Plano de Contas > Orçamentário > Mapear Receita';
        throw new Exception(sprintf(
            $msg . 'Acesse: %s e imprima o relatório.<br>Exemplos de estruturais: %s <br>',
            'da Receita',
            $menu,
            implode(', ', $exemplos)
        ));
    }
    return true;
}

$resinst = $cldb_config->sql_record($cldb_config->sql_query_file(null, 'codigo,codtrib', 'tribinst, codigo', 'tribinst = ' . db_getsession("DB_instit")));
if ($cldb_config->numrows > 0) {
    global $instituicoes;

    $instituicoes[0] = "0000";
    for ($i = 0; $i < $cldb_config->numrows; $i++) {
        $instituicoes[pg_fetch_result($resinst, $i, 'codigo')] = pg_fetch_result($resinst, $i, 'codtrib');
        $instit .= $separa . pg_fetch_result($resinst, $i, 'codigo');
        $separa = ",";
    }

    $anousu = db_getsession("DB_anousu");
    $header = "falhou header: verifique con4_processapad.php";
    if (isset($processar)) {
        try {
            validaContasNaoMapeadas($anousu);
        } catch (Exception $e) {
            echo '<div class="alert alert-danger text-left" role="alert">';
            echo $e->getMessage();
            echo '</div>';
            return;
        }

        $erro = "false";
        echo "<font size='1'>";
        echo "Iniciando processamento ...<br>";
        echo "instituição : $instit  ...<br>";
        echo "Período : " . db_formatar($data_ini, "d") . " à " . db_formatar($data_fim, "d") . "<br>";
        echo "Arquivos     : </font>";
        $matriz = explode('.', $lista);
        flush();

        if (count($matriz) > 1) {
            // monta header
            $res = db_query("select nomeinst,cgc from db_config where codigo=" . db_getsession("DB_instit"));
            db_fieldsmemory($res, 0);
            $ini = explode("-", $data_ini);
            $ini = "$ini[2]$ini[1]$ini[0]";
            $fim = explode("-", $data_fim);
            $fim = "$fim[2]$fim[1]$fim[0]";
            $dt = explode("-", $data_pro);
            $dt = "$dt[2]$dt[1]$dt[0]";
            $header = formatar($cgc, 14, 'n') . $ini . $fim . $dt . formatar($nomeinst, 80, 'c');

            // verifica se o orcamento foi feito no elemento ou subelemento
            $clorcparametro = new cl_orcparametro;
            $res = $clorcparametro->sql_record($clorcparametro->sql_query_file($anousu));
            db_fieldsmemory($res, 0);
            if ($o50_subelem == 't') {
                $subelemento = 'sim'; // true
            } else {
                $subelemento = 'nao'; // false, evitar problemas no select
            }
            $tribinst = db_getsession("DB_instit");
            $arqs = "";
            // carrega classes
            for ($x = 0; $x < sizeof($matriz) - 1; $x++) {
                $contador = 0;
                $classe = $matriz[$x];
                $nomeArquivo = $matriz[$x];
                if ($matriz[$x] === 'padEmpenho') {
                    $nomeArquivo = 'empenho';
                }
                $sNomeArquivo = strtoupper($nomeArquivo) . ".TXT";
                echo "<br><b><font size='1'>" . $sNomeArquivo . "</font></b>";
                $sNomeArquivoTmp = "tmp/" . $sNomeArquivo;

                if (file_exists($sNomeArquivoTmp)) {
                    unlink($sNomeArquivoTmp);
                }

                try {
                    $cl_classe = new $classe($header);
                    if ($classe === 'bal_desp') {
                        $cl_classe->setTipoRaterio($tipo_rateio);
                    }

                    $teste = $cl_classe->processa($instit, $data_ini, $data_fim, $tribinst, $subelemento);

                    if ($teste == "true") {
                        $cldownload->arquivo = $sNomeArquivo;
                        echo "... ";
                        $cldownload->download();
                        echo "  Ok";
                    } else {
                        echo "...Erro";
                        $erro = "true";
                    }
                } catch (Exception $e) {
                    echo "  Erro: {$e->getMessage()}";
                    $erro = "true";
                }

                $arqs .= " {$sNomeArquivoTmp} ";
                flush();
            }

            // aqui todos os testes = "true"
            if ($erro == "false") {
                system("rm -f tmp/{$sArquivo}.zip");
                $aListaArquivos = '';
                $zip = new ZipArchive();
                $zip->open("tmp/{$sArquivo}.zip", ZipArchive::CREATE);
                $aListaArquivos = explode(" ", $arqs);
                foreach ($aListaArquivos as $arquivo) {
                    if (empty($arquivo)) {
                        continue;
                    }
                    $zip->addFile($arquivo);
                }
                $zip->filename = $sArquivo;
                $zip->close();
                echo "<br>";
                echo "<a href='tmp/{$sArquivo}.zip'>Arquivos " . strtoupper($sArquivo) . " (zip)</a>";
            }
        } else {
            echo "<strong>Nenhum Arquivo selecionado.</strong>";
        }
    }
} else {
    echo "<strong>Instituição não configurada para geração do PAD.</strong>";
}
