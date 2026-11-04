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

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories\BalanceteVerificacaoFactory;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\SaldoRecurso;
use ECidade\Financeiro\Orcamento\Registry\ComplementoRegistry;

require_once('model/contabilidade/contacorrente/AC/ContaCorrenteFonteRecurso.model.php');

class bal_ver
{

    protected $arq = null;
    protected $header;

    public function __construct($header)
    {
        $this->header = $header;
    }

    public function processa($instit = 1, $data_ini = "", $data_fim = "", $tribinst, $subelemento = "")
    {
        $anousu = db_getsession("DB_anousu");

        if ($anousu >= 2023) {
            $instituicoes = InstituicaoRepository::getInstituicaoConsolida(db_getsession('DB_instit'));

            $service = BalanceteVerificacaoFactory::getService($anousu, $instituicoes, $data_ini, $data_fim);
            $service->setHeader($this->header);
            $service->processa();

            return true;
        }
        umask(74);
        $this->arq = fopen("tmp/BAL_VER.TXT", 'w+');
        fputs($this->arq, $this->header);
        fputs($this->arq, "\r\n");

        global $instituicoes, $contador, $nomeinst, $sinal_anterior, $sinal_final;

        $oDataInicial = new DBDate($data_ini);
        $oDataFinal = new DBDate($data_fim);
        $where = " c61_instit in ($instit)";
        //$where = " c61_instit in ($instit) and c60_estrut like '8211%'";
        $instituicoesParaProcessamento = array();
        foreach (explode(",", $instit) as $codigoInstituicao) {
            $instituicoesParaProcessamento[] = InstituicaoRepository::getInstituicaoByCodigo($codigoInstituicao);
        }

        $estruturalProcessado = array();
        $sLancamentosEncerramento = db_getsession('DB_anousu') >= 2014 ? 'false' : 'true';
        $anousu = db_getsession("DB_anousu");
        $result = db_planocontassaldo_matriz(
            $anousu,
            $data_ini,
            $data_fim,
            false,
            $where,
            '',
            false,
            $sLancamentosEncerramento
        );

        $contador = 0;

        $array_reduzidos_quebra_linha = array();
        $array_teste = array();
        $array_erro = array();

        $iTotalRegistros = pg_num_rows($result);

        for ($x = 0; $x < $iTotalRegistros; $x++) {
            global $instituicoes, $c61_instit, $c61_reduz, $nivel, $estrutural, $saldo_anterior, $saldo_anterior_debito, $saldo_anterior_credito, $saldo_final, $c60_descr, $c61_codigo;
            db_fieldsmemory($result, $x);

            $aLinhaArquivo = [];
            $aLinhasDisponibilidadeRecurso = [];
            $oRecurso = null;
            $recurso = '0000';
            $codigo_siconfi = '0000';
            $complemento = '0000';
            /**
             * @todo revisar essa coisa
             */
            if (!empty($c61_codigo)) {
                $oRecurso = \ECidade\Financeiro\Orcamento\Repository\RecursoRepository::getByCodigo($c61_codigo);
                $recurso = $oRecurso->getRecurso();
                $codigo_siconfi = substr($oRecurso->getFonteRecurso($anousu)->codigo_siconfi, 1);
                $co = ComplementoRegistry::get($oRecurso->getComplemento());
                if ($co->isTribunal()) {
                    $complemento = str_pad($co->getCodigo(), 4, '0', STR_PAD_LEFT);
                }
            }

            if (substr($estrutural, 0, 5) == '82111' && !empty($c61_reduz)) {
                $oStdDadosVerificacao = db_utils::fieldsMemory($result, $x);

                $hash = sprintf('%s#%s', $estrutural, $c61_instit);
                if (!in_array($hash, $estruturalProcessado)) {
                    $estruturalProcessado[] = $hash;

                    $aLinhasDisponibilidadeRecurso = self::constroiLinhDDR(
                        $oStdDadosVerificacao,
                        $oDataInicial,
                        $oDataFinal
                    );
                }

                unset($oStdDadosVerificacao);
            } else {
                $aLinhaArquivo[] = formatar($estrutural, 20, 'n');
                $orgaoUnidade = '0000';
                if (!empty($c61_instit)) {
                    $orgaoUnidade = $instituicoes[$c61_instit];
                    if ($instituicoes[$c61_instit] instanceof Instituicao) {
                        $orgaoUnidade = $instituicoes[$c61_instit]->getCodigoTribunal();
                    }
                }
                $valorSaldoAnterior = $saldo_anterior;

                $aLinhaArquivo[] = $orgaoUnidade; // 21 a 24 Código do Órgão + Unidade Orçamentária
                $saldo_anterior = abs($saldo_anterior);

                if ($sinal_anterior == 'D') {
                    $aLinhaArquivo[] = formatar(dbround_php_52($saldo_anterior, 2), 13, 'v');
                    $aLinhaArquivo[] = formatar(0, 13, 'v');
                } else {
                    $aLinhaArquivo[] = formatar(0, 13, 'v');
                    $aLinhaArquivo[] = formatar(dbround_php_52($saldo_anterior, 2), 13, 'v');
                }
                $saldo_anterior_debito = abs($saldo_anterior_debito);
                $saldo_anterior_credito = abs($saldo_anterior_credito);
                $aLinhaArquivo[] = formatar($saldo_anterior_debito, 13, 'v'); // 51 a 63 Movimentação - Conta Débito
                $aLinhaArquivo[] = formatar($saldo_anterior_credito, 13, 'v'); // 64 a 76 Movimentação - Conta Crédito

                $saldo_final = abs($saldo_final);

                // saldo atual da conta
                if ($sinal_final == 'D') {
                    $aLinhaArquivo[] = formatar(dbround_php_52($saldo_final, 2), 13, 'v');
                    $aLinhaArquivo[] = formatar(0, 13, 'v');
                } else {
                    $aLinhaArquivo[] = formatar(0, 13, 'v');
                    $aLinhaArquivo[] = formatar(dbround_php_52($saldo_final, 2), 13, 'v');
                }

                if (!(gettype(strpos($c60_descr, "\n")) == "boolean")) {
                    $array_reduzidos_quebra_linha[] = $c61_reduz;
                    $c60_descr = str_replace("\n", ' ', $c60_descr);
                }

                $aLinhaArquivo[] = formatar($c60_descr, 148, 'c'); // 103 a 250 Especificação Conta
                $aLinhaArquivo[] = ($c61_reduz == 0 ? 'S' : 'A'); // 251 Tipo de Nível da Conta

                // pesquisa nivel da conta
                $aLinhaArquivo[] = self::getNivel($estrutural); // 252 a 253 Número do Nível da Conta
                $aLinhaArquivo[] = ' ';
                $aLinhaArquivo[] = self::getEscrituracao($estrutural);
                $aLinhaArquivo[] = self::getNaturezaInformacao($estrutural);
                $aLinhaArquivo[] = self::getIndicadorSuperavit($estrutural);

                $recursoVinculado = $c61_reduz == 0 ? '0000' : str_pad($recurso, 4, '0', STR_PAD_LEFT);
                if ($anousu >= 2023 && $valorSaldoAnterior == 0) {
                    $recursoVinculado = '0000';
                }
                $aLinhaArquivo[] = $recursoVinculado;

                if ($anousu >= 2020) {
                    $complementoFonteRecurso = '0000';
                    // A partir de 2023 a informação deste campo é obrigatória para as contas com Saldo Anterior maior
                    // que zero. Para as contas com Saldo Anterior igual a zero este campo deve obrigatoriamente ser
                    // preenchido com sequência de zeros
                    if ((!empty($oRecurso) && $recursoVinculado != '0000')
                        && ($anousu >= 2023 && $valorSaldoAnterior != 0)) {
                        $complementoFonteRecurso = $complemento;
                    }

                    $aLinhaArquivo[] = str_pad($complementoFonteRecurso, 4, '0', STR_PAD_LEFT);
                }
                if ($anousu == 2022) {
                    $aLinhaArquivo[] = "00000000"; // 266 a 269 Fonte de Recurso e 270 a 273 CO
                }
                if ($anousu >= 2023) {
                    $aLinhaArquivo[] = str_pad($codigo_siconfi, 4, '0', STR_PAD_LEFT); // 266 a 269 Fonte de Recurso
                    $aLinhaArquivo[] = $complemento; // 270 a 273 CO
                }
            }

            if (count($aLinhasDisponibilidadeRecurso) > 0) {
                foreach ($aLinhasDisponibilidadeRecurso as $aLinhaPorRecurso) {
                    $contador++;
                    $line = implode('', $aLinhaPorRecurso);
                    fputs($this->arq, $line);
                    fputs($this->arq, "\r\n");
                }
            } else {
                if (count($aLinhaArquivo) > 0) {
                    $contador++;
                    $line = implode('', $aLinhaArquivo);

                    fputs($this->arq, $line);
                    fputs($this->arq, "\r\n");
                }
            }

            self::validarEstrutural($array_teste, $x, $estrutural, $c61_reduz, $saldo_anterior, $sinal_anterior);
        }

        $maxnivelanalitico = 0;
        $maxnivelsintetico = 0;
        for ($x = 0; $x < sizeof($array_teste); $x++) {
            if (!isset($array_teste[$x][1])) {
                continue;
            }
            if ($array_teste[$x][1] == "A") {
                if ($array_teste[$x][2] > $maxnivelanalitico) {
                    $maxnivelanalitico = $array_teste[$x][2];
                }
            }

            if ($array_teste[$x][1] == "S") {
                if ($array_teste[$x][2] > $maxnivelsintetico) {
                    $maxnivelsintetico = $array_teste[$x][2];
                }
            }
        }

        $numerro = 0;

        /**
         * @todo
         * entender esse laço de verificação de contas e implementar um método que valida essas informações.
         * Todos arquivos do PAD fazem a mesma coisa.
         */
        for ($nivel_atual = $maxnivelsintetico; $nivel_atual > 0; $nivel_atual--) {
            for ($x = 0; $x < sizeof($array_teste); $x++) {
                if (!isset($array_teste[$x][1]) || !isset($array_teste[$x][2])) {
                    continue;
                }

                if ($array_teste[$x][1] == "S" && $array_teste[$x][2] == $nivel_atual) {
                    $estrutural_sintetico = $array_teste[$x][0];
                    $soma_sintetico = $array_teste[$x][3];
                    $soma_analitico = 0;

                    for ($y = $x + 1; $y < sizeof($array_teste); $y++) {
                        if (!isset($array_teste[$y][1]) || !isset($array_teste[$y][2])) {
                            continue;
                        }

                        if ($array_teste[$y][1] == "S" && $array_teste[$y][2] <= $nivel_atual) {
                            break;
                        } elseif ($array_teste[$y][1] == "A" && $array_teste[$y][2] > $nivel_atual) {
                            $soma_analitico += $array_teste[$y][3];
                        }
                    }

                    if (round($soma_sintetico, 2) != round($soma_analitico, 2)) {
                        $array_erro[$numerro][0] = $estrutural_sintetico;
                        $array_erro[$numerro][1] = 2;
                        $numerro++;
                    }
                }
            }
        }

        if (sizeof($array_erro) > 0) {
            echo "<br><b>PROVAVEIS ERROS NOS ESTRUTURAIS:</b><br>";

            for ($x = 0; $x < count($array_erro); $x++) {
                echo $array_erro[$x][0] . "<br>";
            }
        }


        if (sizeof($array_reduzidos_quebra_linha) > 0) {
            $linha_reduzidos = "";
            for ($x = 0; $x < sizeof($array_reduzidos_quebra_linha); $x++) {
                $linha_reduzidos .= $array_reduzidos_quebra_linha[$x] . ($x == sizeof($array_reduzidos_quebra_linha) - 1 ? "." : ",");
            }
            echo "<font size='1' color='red'><br><b>AVISO: reduzidos de contas com descriï¿½ï¿½o contendo quebras de linha: $linha_reduzidos<br>O sistema retirou as quebras de linha na geracao do TXT, mas vocï¿½ deve acertar isso para nï¿½o ter problemas com outras rotinas, acessando o cadastro do plano de contas em Contabilidade->Cadastros->Plano de Contas->Alteraï¿½ï¿½o.</b><br></font>";
        }
        //  trailer
        $contador = espaco(10 - (strlen($contador)), '0') . $contador;
        $line = "FINALIZADOR" . $contador;
        fputs($this->arq, $line);
        fputs($this->arq, "\r\n");

        fclose($this->arq);

        $teste = "true";

        @db_query("DROP TABLE work_pl");

        return $teste;
    }


    public static function validarEstrutural(
        &$array_teste,
        $x,
        $estrutural,
        $c61_reduz,
        $saldo_anterior,
        $sinal_anterior
    )
    {
        $sql_nivel = "select fc_nivel_plano2005('$estrutural') as nivel";
        $result_nivel = db_query($sql_nivel) or die($sql_nivel);
        $nivel = db_utils::fieldsMemory($result_nivel, 0)->nivel;

        $array_teste[$x][0] = $estrutural;
        $array_teste[$x][1] = ($c61_reduz == 0 ? 'S' : 'A');
        $array_teste[$x][2] = $nivel;
        if ($sinal_anterior == 'D') {
            $saldo_anterior = $saldo_anterior * -1;
        }
        $array_teste[$x][3] = $saldo_anterior;
    }


    public static function getNivel($sEstrutural)
    {
        $sql = "select fc_nivel_plano2005('$sEstrutural') as nivel ";
        $resultsis = db_query($sql);
        return formatar(db_utils::fieldsMemory($resultsis, 0)->nivel, 2, 'n');
    }

    public static function getConsultaContaCorrente($sReduzidos, $sWhere)
    {
        $sSqlBuscaValor = "  select x.c19_orctiporec,";
        $sSqlBuscaValor .= "         sum(case when c69_debito in ({$sReduzidos}) then c69_valor else 0 end) as valor_debito,";
        $sSqlBuscaValor .= "         sum(case when c69_credito in ({$sReduzidos}) then c69_valor else 0 end) as valor_credito";
        $sSqlBuscaValor .= "    from ( select distinct conlancamval.*, c19_orctiporec";
        $sSqlBuscaValor .= "             from conlancam ";
        $sSqlBuscaValor .= "                  inner join conlancamval on c69_codlan = c70_codlan ";
        $sSqlBuscaValor .= "                  inner join contacorrentedetalheconlancamval on c28_conlancamval = c69_sequen ";
        $sSqlBuscaValor .= "                  inner join contacorrentedetalhe on c19_sequencial = c28_contacorrentedetalhe ";
        $sSqlBuscaValor .= "            where {$sWhere} ";
        $sSqlBuscaValor .= "          ) as x ";
        $sSqlBuscaValor .= " group by x.c19_orctiporec;";
        return $sSqlBuscaValor;
    }

    /**
     * @param stdClass $oBalanceteVerificacao
     * @param DBDate $dataInicial
     * @param DBDate $dataFinal
     *
     * @return array
     * @throws Exception
     */
    public static function constroiLinhDDR($oBalanceteVerificacao, $dataInicial, $dataFinal)
    {
        $instituicao = InstituicaoRepository::getInstituicaoByCodigo($oBalanceteVerificacao->c61_instit);
        $valoresPorRecurso = self::buscaValores($oBalanceteVerificacao, $dataInicial, $dataFinal);

        $dadosPorRecurso = [];
        foreach ($valoresPorRecurso as $valor) {
            $subrecurso = $valor['o15_recurso'];
            $complemento = str_pad($valor['o15_complemento'], 4, '0', STR_PAD_LEFT);
            $complementoSubrecurso = str_pad($valor['o15_complemento'], 4, '0', STR_PAD_LEFT);

            // validamos se o complemento é para ir para o tribunal (PAD)
            if (!ComplementoRegistry::get($valor['o15_complemento'])->isTribunal()) {
                $complemento = '0000';
                $complementoSubrecurso = '0000';
            }

            if ($valor['saldo_anterior'] == 0) {
                $subrecurso = '0000';
                $complementoSubrecurso = '0000';
            }

            $fonteSiconfi = substr($valor['codigo_siconfi'], 1);
            $hash = sprintf(
                '%s#%s%s#%s#%s#%s',
                $oBalanceteVerificacao->estrutural,
                $instituicao->getCodigoTribunal(),
                $subrecurso,
                $complementoSubrecurso,
                $fonteSiconfi,
                $complemento
            );

            // lógica para agrupar os valores pelo hash
            if (!array_key_exists($hash, $dadosPorRecurso)) {
                // linha: Código da Conta do Bal. Verificação - SG
                $dadosPorRecurso[$hash]['estrutural'] = str_pad($oBalanceteVerificacao->estrutural, 20, 0, STR_PAD_LEFT);
                // linha: Código do Órgão + Unidade Orçamentária
                $dadosPorRecurso[$hash]['orgao_unidade'] = $instituicao->getCodigoTribunal();
                // linha: Saldo Anterior - Conta Devedora
                $dadosPorRecurso[$hash]['saldo_anterior_devedora'] = 0;
                // linha: Saldo Anterior - Conta Credora
                $dadosPorRecurso[$hash]['saldo_anterior_credora'] = 0;
                // linha: Movimentação - Conta Débito
                $dadosPorRecurso[$hash]['movimentacao_debito'] = 0;
                // linha: Movimentação - Conta Crédito
                $dadosPorRecurso[$hash]['movimentacao_credito'] = 0;
                // linha: Saldo Atual - Conta Devedora
                $dadosPorRecurso[$hash]['saldo_atual_devedora'] = 0;
                // linha: Saldo Atual - Conta Credora
                $dadosPorRecurso[$hash]['saldo_atual_credora'] = 0;
                // linha: Especificação Conta do Bal. Verificação - SG
                $dadosPorRecurso[$hash]['descricao'] = str_pad(trim($oBalanceteVerificacao->c60_descr), 148, ' ', STR_PAD_RIGHT);
                // linha: Tipo de Nível da Conta
                $dadosPorRecurso[$hash]['tipo'] = $oBalanceteVerificacao->c61_reduz == 0 ? 'S' : 'A';
                // linha: Número do Nível da Conta
                $dadosPorRecurso[$hash]['nivel'] = self::getNivel($oBalanceteVerificacao->estrutural);
                // linha: Sistema Contábil
                $dadosPorRecurso[$hash]['sistema'] = ' ';
                // linha: Escrituração
                $dadosPorRecurso[$hash]['escrituracao'] = self::getEscrituracao($oBalanceteVerificacao->estrutural);
                // linha: Natureza da Informação
                $dadosPorRecurso[$hash]['natureza'] = self::getNaturezaInformacao($oBalanceteVerificacao->estrutural);
                // linha: Indicador de Superávit Financeiro
                $dadosPorRecurso[$hash]['indicador'] = self::getIndicadorSuperavit($oBalanceteVerificacao->estrutural);
                // $recurso já é a fonte de recurso
                $dadosPorRecurso[$hash]['subrecurso'] = str_pad($subrecurso, 4, '0', STR_PAD_LEFT);
                $dadosPorRecurso[$hash]['complementoSubrecurso'] = $complementoSubrecurso;
                $dadosPorRecurso[$hash]['siconfi'] = str_pad($fonteSiconfi, 4, '0', STR_PAD_LEFT);
                $dadosPorRecurso[$hash]['complemento'] = $complemento;

                // criada apenas para totalizar os saldos
                $dadosPorRecurso[$hash]['saldo_anterior'] = 0;
                $dadosPorRecurso[$hash]['saldo_final'] = 0;
            }

            $dadosPorRecurso[$hash]['movimentacao_debito'] += $valor['valor_debito'];
            $dadosPorRecurso[$hash]['movimentacao_credito'] += $valor['valor_credito'];

            // criada apenas para totalizar os saldos
            $dadosPorRecurso[$hash]['saldo_anterior'] += $valor['saldo_anterior'];
            $dadosPorRecurso[$hash]['saldo_final'] += $valor['saldo_final'];
        }

        foreach ($dadosPorRecurso as $linhaPorRecurso) {
            $saldoFinalCredor = 0;
            $saldoFinalDevedor = 0;
            $saldoAnteriorCredor = 0;
            $saldoAnteriorDevedor = 0;

            // lógica para posicionar corretamente os saldos das contas
            $saldoAnterior = $linhaPorRecurso['saldo_anterior'];
            if ($saldoAnterior < 0) {
                $saldoAnteriorCredor = abs($saldoAnterior);
            }

            if ($saldoAnterior > 0) {
                $saldoAnteriorDevedor = abs($saldoAnterior);
            }

            $saldoFinal = $linhaPorRecurso['saldo_final'];

            if ($saldoFinal < 0) {
                $saldoFinalCredor = abs($saldoFinal);
            }
            if ($saldoFinal > 0) {
                $saldoFinalDevedor = abs($saldoFinal);
            }

            // remove as colun
            unset($linhaPorRecurso['saldo_anterior']);
            unset($linhaPorRecurso['saldo_final']);

            $linhaPorRecurso['saldo_anterior_devedora'] = self::formatarSaldo($saldoAnteriorDevedor);
            $linhaPorRecurso['saldo_anterior_credora'] = self::formatarSaldo($saldoAnteriorCredor);
            $linhaPorRecurso['movimentacao_debito'] = self::formatarSaldo($linhaPorRecurso['movimentacao_debito']);
            $linhaPorRecurso['movimentacao_credito'] = self::formatarSaldo($linhaPorRecurso['movimentacao_credito']);
            $linhaPorRecurso['saldo_atual_devedora'] = self::formatarSaldo($saldoFinalDevedor);
            $linhaPorRecurso['saldo_atual_credora'] = self::formatarSaldo($saldoFinalCredor);

            $aAgrupamento[] = $linhaPorRecurso;
        }

        return $aAgrupamento;
    }

    /**
     * @param $oBalanceteVerificacao
     * @param DBDate $oDataInicial
     * @param DBDate $oDataFinal
     * @return void
     */
    protected static function buscaValores($oBalanceteVerificacao, $oDataInicial, $oDataFinal)
    {
        $reduzido = $oBalanceteVerificacao->c61_reduz;
        $instituicao = $oBalanceteVerificacao->c61_instit;
        $exercicio = $oDataInicial->getAno();
        $dataIni = $oDataInicial->getDate();
        $dataFim = $oDataFinal->getDate();

        $sql = <<<SQL
WITH lancamentos AS (
   SELECT conplanoatributolancamentos.c124_sequencial AS codigo,
          conplanoatributolancamentos.c124_data AS DATA,
          conplanoatributolancamentos.c124_natureza AS natureza,
          conplanoatributolancamentos.c124_valor AS valor,
          conplanoatributolancamentos.c124_lancamento AS codigo_lancamento,
          c71_coddoc AS documento,
          infocomplementarvalor.c123_reduzido AS reduzido,
          c60_estrut AS estrutural,
          c60_descr AS nome_conta,
          infocomplementarvalor.c123_valor AS valor_atributo,
          c121_sigla AS sigla_atributo,
          c129_ordem AS ordem,
          conplanoatributolancamentos.c124_tipo AS tipo,
          codigo_siconfi,
          o15_recurso,
          o15_complemento
   FROM infocomplementarvalor
   INNER JOIN conplanoatributolancamentos ON c124_sequencial = c123_conplanoatributolancamentos
   INNER JOIN conplanosistemaatributos ON c129_conplanoinfocomplementar = c123_infocomplementar
   AND c129_conplanosistema = c123_conplanosistema
   INNER JOIN conplanoinfocomplementar ON c121_sequencial = c123_infocomplementar
   INNER JOIN conplanoreduz ON c61_reduz = c123_reduzido
   AND EXTRACT (YEAR FROM c124_data)::int = c61_anousu
   INNER JOIN conplano ON c61_codcon = c60_codcon
   AND c60_anousu = c61_anousu
   LEFT JOIN conlancam ON c70_codlan = c124_lancamento
   LEFT JOIN conlancamdoc ON c71_codlan = c70_codlan
   join orctiporec on o15_codigo = c123_valor::int
   join fonterecurso on orctiporec_id = o15_codigo
        and exercicio = c60_anousu
   WHERE c124_data <= '{$dataFim}'
     and extract(year from c124_data)::int = $exercicio
     AND c61_instit = $instituicao
     AND c61_reduz = $reduzido
     and c124_tipo = '2'
     AND c123_conplanosistema = 100
   ORDER BY c124_sequencial,
            c71_coddoc,
            c124_data,
            c123_reduzido,
            c129_ordem,
            c124_lancamento,
            c60_estrut
), conta_corrente AS (
   SELECT codigo,
          DATA,
          natureza,
          valor,
          codigo_lancamento,
          reduzido,
          estrutural,
          nome_conta,
          array_to_string(array_agg(valor_atributo||'#'||sigla_atributo ORDER BY ordem), '|') AS atributos,
          tipo,
          codigo_siconfi,
          o15_recurso,
          o15_complemento
   FROM lancamentos
   GROUP BY codigo,
            DATA,
            natureza,
            valor,
            codigo_lancamento,
            reduzido,
            estrutural,
            nome_conta,
            tipo,
            codigo_siconfi,
            o15_recurso,
            o15_complemento
   ORDER BY codigo,
            DATA,
            natureza,
            valor,
            codigo_lancamento,
            reduzido,
            estrutural
), saldo_inicial as (
    select conplanoexecontacorrenteatributo.id as codigo,
           c60_estrut as estrutural,
           c60_descr as nome_conta,
           c143_conplanoreduz as reduzido,
           c143_saldo as saldo,
           c143_natureza as natureza,
           c121_sigla as sigla_atributo,
           c144_valor as valor_atributo,
           c129_ordem as ordem_atributo,
           codigo_siconfi,
           o15_recurso,
           o15_complemento
      from contabilidade.conplanoexecontacorrente
      join contabilidade.conplanoexecontacorrenteatributo on c144_conplanoexecontacorrente = conplanoexecontacorrente.id
      join contabilidade.conplanoreduz ON (c61_reduz, c61_anousu) = (c143_conplanoreduz, c143_exercicio)
      join contabilidade.conplano ON (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
      join contabilidade.conplanoinfocomplementar on c144_conplanoinfocomplementar = c121_sequencial
      join contabilidade.conplanosistemaatributos on c129_conplanosistema  = c143_conplanosistema
           and c129_conplanoinfocomplementar = c144_conplanoinfocomplementar
      join orctiporec on o15_codigo = c144_valor::int
      join fonterecurso on orctiporec_id = o15_codigo
            and exercicio = c143_exercicio
    where c143_conplanosistema = 100
      and c143_exercicio = $exercicio
     AND c61_instit = $instituicao
     AND c61_reduz = $reduzido
), conta_corrente_si as (
  SELECT  codigo,
          estrutural,
          nome_conta,
          reduzido,
          saldo,
          natureza,
          array_to_string(array_agg(valor_atributo||'#'||sigla_atributo ORDER BY ordem_atributo), '|') AS atributos,
          codigo_siconfi, o15_recurso, o15_complemento
   FROM saldo_inicial
   GROUP BY codigo, estrutural, nome_conta, reduzido, saldo, natureza, codigo_siconfi, o15_recurso, o15_complemento
   ORDER BY codigo, estrutural, nome_conta, reduzido, saldo, natureza
), totaliza_si as (
    SELECT  codigo,
            estrutural,
            nome_conta,
            reduzido,
            atributos,
            sum(case WHEN natureza = 'C' THEN saldo * -1 else saldo end) as saldo,
            codigo_siconfi, o15_recurso, o15_complemento
      FROM conta_corrente_si
    GROUP BY codigo, estrutural, nome_conta, reduzido, atributos, codigo_siconfi, o15_recurso, o15_complemento
), saldo_movimentacao as (
  SELECT estrutural,
         nome_conta,
         reduzido,
         atributos,
         coalesce(sum(CASE WHEN DATA <= '{$dataFim}'
                      AND tipo = '2'
                      AND natureza = 'D' THEN valor END), 0) AS valor_debito,
         coalesce(sum(CASE WHEN DATA <= '{$dataFim}'
                      AND tipo = '2'
                      AND natureza = 'C' THEN valor END), 0) AS valor_credito,
         codigo_siconfi, o15_recurso, o15_complemento,
         array_to_string(array_accum(codigo_lancamento), ',') as codigos_lancamentos
  FROM conta_corrente
  GROUP BY estrutural, atributos, reduzido, nome_conta, codigo_siconfi, o15_recurso, o15_complemento
  ORDER BY estrutural
)
 select
         estrutural,
         nome_conta,
         reduzido,
         codigo_siconfi,
         o15_recurso,
         o15_complemento,
         sum(saldo_anterior) as saldo_anterior,
         sum(valor_debito) as valor_debito,
         sum(valor_credito) as valor_credito,
         sum((saldo_anterior - valor_credito) + valor_debito) as saldo_final,
         array_to_string(array_accum(codigos_lancamentos), ',') as codigos_lancamentos
  from (
  select estrutural,
         nome_conta,
         reduzido,
         saldo as saldo_anterior,
         0 as valor_debito,
         0 as valor_credito,
         codigo_siconfi,
         o15_recurso,
         o15_complemento,
         null as codigos_lancamentos
    from totaliza_si

   union

  select estrutural,
         nome_conta,
         reduzido,
         0 as saldo_anterior,
         valor_debito,
         valor_credito,
         codigo_siconfi,
         o15_recurso,
         o15_complemento,
         codigos_lancamentos
    from saldo_movimentacao
) as x

  group by estrutural,
         nome_conta,
         reduzido,
         codigo_siconfi,
         o15_recurso,
         o15_complemento
 order by estrutural,
         nome_conta,
         reduzido,
         codigo_siconfi,
         o15_recurso,
         o15_complemento
;
SQL;

        $rs = db_query($sql);
        return pg_fetch_all($rs);
    }

    /**
     * @param stdClass $oBalanceteVerificacao
     * @param DBDate $oDataInicial
     * @param DBDate $oDataFinal
     *
     * @return array
     */
    public static function constroiLinhaDisponibilizacaoRecurso(
        stdClass $oBalanceteVerificacao,
        DBDate   $oDataInicial,
        DBDate   $oDataFinal,
        array    $instituicoesProcessamento
    )
    {
        $instituicao = new Instituicao($oBalanceteVerificacao->c61_instit);

        $valoresPorRecurso = self::getSaldosPeriodo(
            $oBalanceteVerificacao,
            $instituicoesProcessamento,
            $oDataInicial,
            $oDataFinal
        );


        $aAgrupamento = array();
        foreach ($valoresPorRecurso as $recurso => $valorRecurso) {
            $aLinhaRecurso = array();

            // linha: Código da Conta do Bal. Verificação - SG
            $aLinhaRecurso[] = str_pad($oBalanceteVerificacao->estrutural, 20, 0, STR_PAD_LEFT);
            // linha: Código do Órgão + Unidade Orçamentária
            $aLinhaRecurso[] = $instituicao->getCodigoTribunal();
            // linha: Saldo Anterior - Conta Devedora
            $aLinhaRecurso[] = self::formatarSaldo($valorRecurso['D']->saldo_anterior);
            // linha: Saldo Anterior - Conta Credora
            $aLinhaRecurso[] = self::formatarSaldo($valorRecurso['C']->saldo_anterior);
            // linha: Movimentação - Conta Débito
            $aLinhaRecurso[] = self::formatarSaldo($valorRecurso['D']->movimentacao);
            // linha: Movimentação - Conta Crédito
            $aLinhaRecurso[] = self::formatarSaldo($valorRecurso['C']->movimentacao);
            // linha: Saldo Atual - Conta Devedora
            $aLinhaRecurso[] = self::formatarSaldo($valorRecurso['D']->saldo_atual);
            // linha: Saldo Atual - Conta Credora
            $aLinhaRecurso[] = self::formatarSaldo($valorRecurso['C']->saldo_atual);
            // linha: Especificação Conta do Bal. Verificação - SG
            $aLinhaRecurso[] = str_pad($oBalanceteVerificacao->c60_descr, 148, ' ', STR_PAD_RIGHT);
            // linha: Tipo de Nível da Conta
            $aLinhaRecurso[] = $oBalanceteVerificacao->c61_reduz == 0 ? 'S' : 'A';
            // linha: Número do Nível da Conta
            $aLinhaRecurso[] = self::getNivel($oBalanceteVerificacao->estrutural);
            // linha: Sistema Contábil
            $aLinhaRecurso[] = ' ';
            // linha: Escrituração
            $aLinhaRecurso[] = self::getEscrituracao($oBalanceteVerificacao->estrutural);
            // linha: Natureza da Informação
            $aLinhaRecurso[] = self::getNaturezaInformacao($oBalanceteVerificacao->estrutural);
            // linha: Indicador de Superávit Financeiro
            $aLinhaRecurso[] = self::getIndicadorSuperavit($oBalanceteVerificacao->estrutural);
            // $recurso já é a fonte de recurso
            $aLinhaRecurso[] = str_pad($recurso, 4, '0', STR_PAD_LEFT);

            /**
             * Complemento nesse caso é sempre zero
             */
            if (db_getsession('DB_anousu') >= 2020) {
                $complemento = 0;
                $aLinhaRecurso[] = str_pad($complemento, 4, '0', STR_PAD_LEFT);;
            }
            $aAgrupamento[] = $aLinhaRecurso;
        }

        // recria a matriz de agrupamento para somar valores quebrados pelo recurso
        $aDadosAgrupamento = array();
        foreach ($aAgrupamento as $aAgrup) {
            $aDadosAgrupamento[$aAgrup[15]] [] = $aAgrup;
        }
        $aNovoAgrup = array();
        foreach ($aDadosAgrupamento as $iIndice => $aGrupo) {
            $sSaldoAnteriorDevedor = 0;
            $sSaldoAnteriorCredor = 0;
            $sMovimentoDebito = 0;
            $sMovimentoCredito = 0;
            $sValorFinalDevedor = 0;
            $sValorFinalCredor = 0;

            foreach ($aGrupo as $aGrup) {
                $sSaldoAnteriorDevedor += $aGrup[2];
                $sSaldoAnteriorCredor += $aGrup[3];
                $sMovimentoDebito += $aGrup[4];
                $sMovimentoCredito += $aGrup[5];
                $sValorFinalDevedor += $aGrup[6];
                $sValorFinalCredor += $aGrup[7];
            }

            /**
             * Como são agrupados os recursos pela especificação, um pode ter saldo credor e outro devedor.
             * Só é permitido ter saldo em uma coluna de saldo.
             * Por isso é necessário efetuar o cálculo (subtrair o menor do maior)
             * e zerar o saldo do menor
             */

            if ((float)$sSaldoAnteriorDevedor > 0 && (float)$sSaldoAnteriorCredor > 0) {
                if ((float)$sSaldoAnteriorDevedor > (float)$sSaldoAnteriorCredor) {
                    $sSaldoAnteriorDevedor = strval((float)$sSaldoAnteriorDevedor - (float)$sSaldoAnteriorCredor);
                    $sSaldoAnteriorCredor = '0';
                } else {
                    $sSaldoAnteriorCredor = strval((float)$sSaldoAnteriorCredor - (float)$sSaldoAnteriorDevedor);
                    $sSaldoAnteriorDevedor = '0';
                }
            }

            if ((float)$sValorFinalDevedor > 0 && (float)$sValorFinalCredor > 0) {
                if ((float)$sValorFinalDevedor > (float)$sValorFinalCredor) {
                    $sValorFinalDevedor = strval((float)$sValorFinalDevedor - (float)$sValorFinalCredor);
                    $sValorFinalCredor = '0';
                } else {
                    $sValorFinalCredor = strval((float)$sValorFinalCredor - (float)$sValorFinalDevedor);
                    $sValorFinalDevedor = '0';
                }
            }

            $aLinha = array();
            $aLinha[0] = $aGrupo[0][0];
            $aLinha[1] = $aGrupo[0][1];
            $aLinha[2] = str_pad($sSaldoAnteriorDevedor, 13, 0, STR_PAD_LEFT);
            $aLinha[3] = str_pad($sSaldoAnteriorCredor, 13, 0, STR_PAD_LEFT);
            $aLinha[4] = str_pad($sMovimentoDebito, 13, 0, STR_PAD_LEFT);
            $aLinha[5] = str_pad($sMovimentoCredito, 13, 0, STR_PAD_LEFT);
            $aLinha[6] = str_pad($sValorFinalDevedor, 13, 0, STR_PAD_LEFT);
            $aLinha[7] = str_pad($sValorFinalCredor, 13, 0, STR_PAD_LEFT);
            $aLinha[8] = $aGrupo[0][8];
            $aLinha[9] = $aGrupo[0][9];
            $aLinha[10] = $aGrupo[0][10];
            $aLinha[11] = $aGrupo[0][11];
            $aLinha[12] = $aGrupo[0][12];
            $aLinha[13] = $aGrupo[0][13];
            $aLinha[14] = $aGrupo[0][14];
            $aLinha[15] = $aGrupo[0][15];
            $aLinha[16] = $aGrupo[0][16];
            $aNovoAgrup[] = $aLinha;
        }
        $aAgrupamento = $aNovoAgrup;


        return $aAgrupamento;
    }

    public static function getNaturezaInformacao($sEstrutural)
    {
        $iEstrtutural = substr($sEstrutural, 0, 1);
        $sNaturezaInformacao = " ";
        switch ($iEstrtutural) {
            case 1:
            case 2:
            case 3:
            case 4:
                $sNaturezaInformacao = "P";
                break;

            case 5:
            case 6:
                $sNaturezaInformacao = "O";
                break;

            case 7:
            case 8:
                $sNaturezaInformacao = "C";
                break;
        }

        if (!USE_PCASP) {
            $sNaturezaInformacao = " ";
        }
        return $sNaturezaInformacao;
    }

    public static function getEscrituracao($estrutural)
    {
        // definimos escrituração
        $sSqlEscrituracao = "    select distinct c60_codcon                                             ";
        $sSqlEscrituracao .= "      from conplano                                                        ";
        $sSqlEscrituracao .= "inner join conplanoreduz on conplano.c60_codcon = conplanoreduz.c61_codcon ";
        $sSqlEscrituracao .= "                        and conplano.c60_anousu = conplanoreduz.c61_anousu ";
        $sSqlEscrituracao .= "where c60_estrut = '{$estrutural}'                                         ";
        $sSqlEscrituracao .= "  and c60_anousu = " . db_getsession("DB_anousu");

        $rsEscrituracao = db_query($sSqlEscrituracao);
        $sEscrituracao = "N";
        if (pg_num_rows($rsEscrituracao) > 0) {
            $sEscrituracao = "S";
        }

        if (!USE_PCASP) {
            $sEscrituracao = " ";
        }
        return $sEscrituracao;
    }

    public static function getIndicadorSuperavit($estrutural)
    {
        // definimos superavit
        $sSqlSuperavit = "    select c60_identificadorfinanceiro              ";
        $sSqlSuperavit .= "      from conplano                                 ";
        $sSqlSuperavit .= "     where c60_anousu = " . db_getsession("DB_anousu");
        $sSqlSuperavit .= "       and c60_estrut = '{$estrutural}'             ";
        $rsSuperavit = db_query($sSqlSuperavit);

        if (pg_numrows($rsSuperavit) > 0) {
            $sIndicadorSuperavitFinanceiro = pg_result($rsSuperavit, 0, 'c60_identificadorfinanceiro');
            if ($sIndicadorSuperavitFinanceiro == "N") {
                $sIndicadorSuperavitFinanceiro = "P";
            }
        } else {
            $sIndicadorSuperavitFinanceiro = "P";
        }

        if (!USE_PCASP) {
            $sIndicadorSuperavitFinanceiro = " ";
        }

        return $sIndicadorSuperavitFinanceiro;
    }

    public static function getValorImplantadoDisponibilidadeRecurso(DBDate $oData)
    {
        $aWhere = array(
            "c29_anousu = {$oData->getAno()}",
            "c29_mesusu = 0",
            "substring(c60_estrut, 1, 5) = '82111'"
        );

        $sCampos = "coalesce(sum(c29_credito), 0) as valor_implantado_credito";
        $oDaoSaldoImplantado = new cl_contacorrentesaldo();
        $sSqlBuscaSaldoImplantado = $oDaoSaldoImplantado->sql_query_busca_saldo(
            $sCampos,
            null,
            implode(' and ', $aWhere)
        );

        $rsBuscaSaldo = db_query($sSqlBuscaSaldoImplantado);
        if (!$rsBuscaSaldo) {
            throw new Exception("Ocorreu um erro ao buscar o saldo implantado do conta corrente.");
        }
        return db_utils::fieldsMemory($rsBuscaSaldo, 0)->valor_implantado_credito;
    }

    /**
     * @param  $saldo
     *
     * @return string
     */
    private static function formatarSaldo($saldo)
    {
        if (!empty($saldo)) {
            $saldo = number_format($saldo, 2, '', '');
        }

        return str_pad($saldo, 13, 0, STR_PAD_LEFT);
    }

    /**
     * Retorna os saldos por periodo.
     *
     * @param stdClass $balancete
     * @param aray $codigoTribunal
     * @param DBDate $dataInicial
     * @param DBDate $dataFinal
     *
     * @return array
     */
    private static function getSaldosPeriodo($balancete, $instituicoesProcessamento, DBDate $dataInicial, DBDate $dataFinal)
    {
        $movimentacoes = self::getValorPorRecursoNaCompetencia(
            $dataFinal->getAno(),
            $dataFinal->getMes(),
            $balancete->c61_reduz,
            $instituicoesProcessamento
        );
        $dados = array();
        $dados = self::agruparSaldos($movimentacoes, $dados);

        return $dados;
    }

    /**
     * Agrupa os saldos do recurso.
     *
     * @param $saldos
     * @param $identificador
     * @param array $dados
     *
     * @return array
     */
    private static function agruparSaldos($saldos, &$dados)
    {
        $std = new stdClass();
        $std->saldo_anterior = '';
        $std->saldo_atual = '';
        $std->movimentacao = '';

        foreach ($saldos as $saldo) {
            if (!array_key_exists($saldo->recurso, $dados)) {
                $dados[$saldo->recurso]['C'] = clone $std;
                $dados[$saldo->recurso]['D'] = clone $std;
            }
            if ($saldo->natureza_saldo_anterior == 'C') {
                $dados[$saldo->recurso]['C']->saldo_anterior = $saldo->saldo_anterior;
            } else {
                $dados[$saldo->recurso]['D']->saldo_anterior = $saldo->saldo_anterior;
            }
            $dados[$saldo->recurso]['D']->movimentacao = $saldo->valor_debito;
            $dados[$saldo->recurso]['C']->movimentacao = $saldo->valor_credito;
            if ($saldo->natureza_saldo_final == 'C') {
                $dados[$saldo->recurso]['C']->saldo_atual = $saldo->saldo_final;
            } else {
                $dados[$saldo->recurso]['D']->saldo_atual = $saldo->saldo_final;
            }
        }

        return $dados;
    }

    /**
     * Busca o saldo inicial da conta
     *
     * @param $reduzido
     * @param DBDate $data
     *
     * @return array
     */
    private static function getSaldoInicial($reduzido, DBDate $data)
    {
        $sSql = "SELECT * FROM conplanoexe WHERE c62_anousu = {$data->getAno()} AND c62_reduz = {$reduzido}";
        $result = db_query($sSql);

        $dados = db_utils::getCollectionByRecord($result);
        if (empty($dados)) {
            return array();
        }

        $dados = reset($dados);
        $saldosAnteriores = array(
            (object)array(
                'saldo' => $dados->c62_vlrcre,
                'natureza' => 'C',
                'recurso' => str_pad($dados->c62_codrec, 4, 0, STR_PAD_LEFT)
            ),
            (object)array(
                'saldo' => $dados->c62_vlrdeb,
                'natureza' => 'D',
                'recurso' => str_pad($dados->c62_codrec, 4, 0, STR_PAD_LEFT)
            )
        );

        return $saldosAnteriores;
    }

    /**
     * Retorna o saldo do recurso.
     *
     * @param $estrutural
     * @param $codtrib
     * @param DBDate $data
     *
     * @return stdClass[]
     *
     * @throws Exception
     */
    private static function getSaldoRecurso($estrutural, $codtrib, DBDate $data)
    {
        $estrutural = substr($estrutural, 0, 9);

        $where = array(
            "c125_hashcontaatributos ILIKE '%{$estrutural}%'",
            "c125_hashcontaatributos ILIKE '%{$codtrib}#PO%'",
            "c125_hashcontaatributos ILIKE '%FR%'",
            "c125_anousu = {$data->getAno()}",
            "c125_mesusu = {$data->getMes()}"
        );

        $campos = array(
            "sum(c125_valor) AS saldo",
            "c125_natureza AS natureza",
            "(substring(c125_hashcontaatributos FROM (position('#FR' IN c125_hashcontaatributos) - 4) FOR 4 ) ) as recurso"
        );

        $sql = "SELECT " . implode(', ', $campos) . " FROM conplanoatributosaldo WHERE " . implode(' AND ', $where);
        $sql .= " group by 2, 3 order by recurso ";

        $result = db_query($sql);

        if (!$result) {
            throw new Exception('Erro ao buscar os saldos da matriz contábil. Estrutural: ' . $estrutural);
        }

        return db_utils::getCollectionByRecord($result);
    }


    /**
     * Retorna se a matriz foi processada.
     *
     * @param integer $ano
     * @param integer $mes
     *
     * @return bool
     */
    public static function processouMatriz($ano, $mes = 12)
    {
        $sSql = "SELECT 1 FROM conplanoatributosaldo WHERE c125_anousu = {$ano} AND c125_mesusu = {$mes} LIMIT 1";

        $result = db_query($sSql);

        return pg_num_rows($result) > 0;
    }


    /**
     * Retorna os recursos por ano
     * @param $ano
     * @param $mes
     * @param $reduzido
     * @param array $instituicoesProcessamento
     * @return stdClass[]
     * @throws Exception
     */
    static function getValorPorRecursoNaCompetencia($ano, $mes, $reduzido, array $instituicoesProcessamento)
    {
        $sqlEstrutural = 'select substr(c60_estrut, 1, 8) as estrutural ';
        $sqlEstrutural .= '  from conplano ';
        $sqlEstrutural .= '       inner join conplanoreduz on c61_codcon = c60_codcon  and c61_anousu = c60_anousu';
        $sqlEstrutural .= " where c61_reduz = {$reduzido} ";
        $sqlEstrutural .= "   and c61_anousu = {$ano}";

        $rsEstrutural = db_query($sqlEstrutural);
        $estruturalConta = db_utils::fieldsMemory($rsEstrutural, 0)->estrutural;

        $dataInicioAno = new \DateTime("{$ano}-01-01");
        $dataInicioCompetencia = $dataInicioAno;
        $dataFinal = new \DateTime("{$ano}-{$mes}-" . cal_days_in_month(CAL_GREGORIAN, $mes, $ano));
        $saldo = new SaldoRecurso();
        $recursos = $saldo->getRecursos($instituicoesProcessamento, $dataInicioCompetencia, $dataFinal, null, $estruturalConta);

        return $recursos;
    }
}
