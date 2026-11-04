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

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories\ReceitaFactory;

class receita
{
    protected $arq = null;
    protected $header;
    protected $emissaoMGS = false;

    public function __construct($header)
    {
        $this->header = $header;
    }

    public function isMGS($bool)
    {
        $this->emissaoMGS = $bool;
    }

    public function acerta_valor($valor, $quant)
    {
        if ($valor < 0) {
            $valor *= -1;
            $valor = "-" . formatar($valor, $quant - 1, 'v');
        } else {
            $valor = formatar($valor, $quant, 'v');
        }
        return $valor;
    }

    /**
     * @param int $instit
     * @param string $data_ini
     * @param string $data_fim
     * @param null $tribinst
     * @param string $subelemento
     * @return string
     * @throws BusinessException
     */
    public function processa($instit = 1, $data_ini = "", $data_fim = "", $tribinst = null, $subelemento = "")
    {
        $anousu = db_getsession("DB_anousu");
        if ($anousu >= 2023) {
            $instituicoes = InstituicaoRepository::getInstituicaoConsolida(db_getsession('DB_instit'));
            $service = ReceitaFactory::getService($anousu, $instituicoes, $data_ini, $data_fim);
            $service->setHeader($this->header);

            $service->processa();

            return true;
        }

        umask(74);
        $this->arq = fopen("tmp/RECEITA.TXT", 'w+');
        fputs($this->arq, $this->header);
        fputs($this->arq, "\r\n");

        global $o70_anousu, $o70_instit, $instituicoes, $o70_codrec, $contador, $o70_valor, $nomeinst, $o57_fonte, $o57_fontes, $janeiro, $fevereiro, $marco, $abril, $maio, $junho, $julho, $agosto, $setembro, $outubro, $novembro, $dezembro, $o70_concarpeculiar;
        global $prev_jan, $prev_fev, $complemento,
               $prev_mar, $prev_abr, $prev_mai, $prev_jun, $prev_jul, $prev_ago, $prev_set, $prev_out, $prev_nov, $prev_dez;
        $contador = 0;

        $xtipo = 0;
        $origem = "B";
        $opcao = 3;

        $clreceita_saldo_mes = new \ECidade\Financeiro\Contabilidade\Balancete\Receita\Mensal();
        $clreceita_saldo_mes->setDataInicial($data_ini);
        $clreceita_saldo_mes->setDataFinal($data_fim);
        $clreceita_saldo_mes->setAno(DB_getSession("DB_anousu"));
        $clreceita_saldo_mes->setInstitucoes(explode(",", $instit));
        $clreceita_saldo_mes->setPrevisaoCronograma(true);
        $rsReceita = $clreceita_saldo_mes->getDados();
        $valortotal = 0;
        $mesfim = substr($data_fim, 5, 2) + 0;

        $totalLinhas = pg_num_rows($rsReceita);
        for ($i = 1; $i < $totalLinhas; $i++) {
            db_fieldsmemory($rsReceita, $i);
            $dadosReceita = db_utils::fieldsMemory($rsReceita, $i);

            // $o70_codrec = 0 é uma conta sintética
            $o15_complemento = ($dadosReceita->o70_codrec == 0 ? 0 : $complemento);
            // pesquisa orgaotrib
            $orgaotrib = $instituicoes[$o70_instit];

//      if (substr($o57_fonte,0,1) != "9")
            if ($o70_anousu > 2007) {
                if (db_conplano_grupo($o70_anousu, substr($o57_fonte, 0, 1) . "%", 9000) == false) {
                    $line = formatar(substr($o57_fonte, 1, 14), 20, 'n'); // recompisoção
                } else {
                    $line = formatar(substr($o57_fonte, 0, 15), 20, 'n'); // recompisoção
                }
            } else {
                $line = formatar(substr($o57_fonte, 1, 14), 20, 'n'); // recompisoção
            }

            $line .= formatar($orgaotrib, 4, 'n');

            $concarpeculiar = "000";
            $o70_codigo = "0000";

            if ($o70_anousu > 2007) {
                if ($o70_codrec > 0) {
                    $sql_orcreceita = "
                        select o70_concarpeculiar, o15_recurso
                          from orcamento.orcreceita
                          join orcamento.orctiporec on o15_codigo = o70_codigo
                         where orcreceita.o70_anousu = $o70_anousu
                           and orcreceita.o70_codrec = $o70_codrec
                    ";

                    $res_orcreceita = @db_query($sql_orcreceita) or die($sql_orcreceita);
                    if (@pg_numrows($res_orcreceita) != 0) {
                        $dados = db_utils::fieldsMemory($res_orcreceita, 0);
                        $concarpeculiar = formatar($dados->o70_concarpeculiar, 3, "n");
                        $o70_codigo = $dados->o15_recurso;
                    }
                }

                if (db_conplano_grupo($o70_anousu, substr($o57_fonte, 0, 1) . "%", 9000) == true) {
                    if ($concarpeculiar == "000" and 1 == 2) {
                        $concarpeculiar = "101";
                    }
                }
            }

            if (db_conplano_grupo($o70_anousu, substr($o57_fonte, 0, 2) . "%", 9000) == true) {  // 49
                if ($dezembro <> 0) {
                    $dezembro = abs($dezembro) * -1;
                }

                if ($prev_dez <> 0) {
                    $prev_dez = abs($prev_dez) * -1;
                }
            }

            $line .= $this->acerta_valor($janeiro, 13);
            $line .= $this->acerta_valor($fevereiro, 13);
            if ($mesfim > 2) {
                $line .= $this->acerta_valor($marco, 13);
                $line .= $this->acerta_valor($abril, 13);
            } else {
                $line .= $this->acerta_valor(0, 13);
                $line .= $this->acerta_valor(0, 13);
            }
            if ($mesfim > 4) {
                $line .= $this->acerta_valor($maio, 13);
                $line .= $this->acerta_valor($junho, 13);
            } else {
                $line .= $this->acerta_valor(0, 13);
                $line .= $this->acerta_valor(0, 13);
            }
            if ($mesfim > 6) {
                $line .= $this->acerta_valor($julho, 13);
                $line .= $this->acerta_valor($agosto, 13);
            } else {
                $line .= $this->acerta_valor(0, 13);
                $line .= $this->acerta_valor(0, 13);
            }
            if ($mesfim > 8) {
                $line .= $this->acerta_valor($setembro, 13);
                $line .= $this->acerta_valor($outubro, 13);
            } else {
                $line .= $this->acerta_valor(0, 13);
                $line .= $this->acerta_valor(0, 13);
            }
            if ($mesfim > 10) {
                $line .= $this->acerta_valor($novembro, 13);
                $line .= $this->acerta_valor($dezembro, 13);
            } else {
                $line .= $this->acerta_valor(0, 13);
                $line .= $this->acerta_valor(0, 13);
            }

            $valortotal += $janeiro + $fevereiro + $marco + $abril + $maio + $junho + $julho + $agosto + $setembro + $outubro + $novembro + $dezembro;

            $line .= $this->acerta_valor(dbround_php_52($prev_jan + $prev_fev, 2), 12);
            $line .= $this->acerta_valor(dbround_php_52($prev_mar + $prev_abr, 2), 12);
            $line .= $this->acerta_valor(dbround_php_52($prev_mai + $prev_jun, 2), 12);
            $line .= $this->acerta_valor(dbround_php_52($prev_jul + $prev_ago, 2), 12);
            $line .= $this->acerta_valor(dbround_php_52($prev_set + $prev_out, 2), 12);
            $line .= $this->acerta_valor(dbround_php_52($prev_nov + $prev_dez, 2), 12);
            if ($o70_anousu > 2007) {
                $line .= $concarpeculiar;
                if (db_getsession('DB_anousu') > 2008) {
                    $line .= formatar($o70_codigo, 4, 'n');
                }
            }

            if (db_getsession("DB_anousu") >= 2020) {
                $line .= str_pad($o15_complemento, 4, '0', STR_PAD_LEFT);
            }

            $contador++;
            if (db_getsession("DB_anousu") >= 2022) {
                $line .= "00000000";
            }
            fputs($this->arq, $line);
            fputs($this->arq, "\r\n");
        }

        //     echo $valortotal;exit;
        //  trailer
        $contador = espaco(10 - (strlen($contador)), '0') . $contador;
        $line = "FINALIZADOR" . $contador;
        fputs($this->arq, $line);
        fputs($this->arq, "\r\n");

        fclose($this->arq);

        @db_query("drop table work_plano");

        $teste = "true";
        return $teste;
    }
}
