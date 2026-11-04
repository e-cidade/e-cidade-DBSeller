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

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories\ReceitaAnteriorFactory;
use ECidade\Financeiro\Orcamento\Repository\RecursoRepository as RecursoRepositoryAlias;

class rec_ant
{
    public $arq = null;
    /**
     * @var mixed
     */
    protected $header;
    protected $emissaoMGS = false;

    public function __construct($header)
    {
        $this->header = $header;
        umask(74);
        $this->arq = fopen("tmp/REC_ANT.TXT", 'w+');
        fputs($this->arq, $header);
        fputs($this->arq, "\r\n");
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

    public function processa($instit = 1, $data_ini = "", $data_fim = "", $tribinst = null, $subelemento = "")
    {
        $anousu = db_getsession("DB_anousu");
        if ($anousu >= 2024) {
            $instituicoes = InstituicaoRepository::getInstituicaoConsolida(db_getsession('DB_instit'));
            $anterior = $anousu - 1;
            $dataInicial = "{$anterior}-01-01";
            $dataFinal = "{$anterior}-12-31";
            $service = ReceitaAnteriorFactory::getService($anterior, $instituicoes, $dataInicial, $dataFinal);
            $service->setHeader($this->header);

            $service->processa();

            return true;
        }
        global $complemento_lancamento, $o70_instit, $instituicoes, $o70_codrec, $o70_valor, $nomeinst, $o57_fonte, $o57_fontes, $janeiro, $fevereiro, $marco, $abril, $maio, $junho, $julho, $agosto, $setembro, $outubro, $novembro, $dezembro;
        $contador = 0;

        $xtipo = 0;
        $origem = "B";
        $opcao = 3;

        $clreceita_saldo_mes = new cl_receita_saldo_mes_complemento;

        $anousu = db_getsession('DB_anousu') - 1;
        $nomeArq = 'REC_ANT.TXT';

        /*
         * verifica se ja existe arquivo no banco
         */
        $oDaoArquivosPad = new cl_conarquivospad();
        $camposArquivosPad = "c54_codarq, c54_anousu, c54_nomearq, c54_arquivo";
        $whereArquivosPad = "c54_anousu = {$anousu} AND c54_nomearq = '{$nomeArq}' AND c54_codtrib = {$tribinst}";
        $sqlArquivosPad = $oDaoArquivosPad->sql_query(null, $camposArquivosPad, '', $whereArquivosPad);
        $rsDaoArquivosPad = $oDaoArquivosPad->sql_record($sqlArquivosPad);

        if ($oDaoArquivosPad->numrows > 0) {
            $oArquivo = db_utils::fieldsMemory($rsDaoArquivosPad, 0);
            $sArquivo = $oArquivo->c54_arquivo;

            fputs($this->arq, str_replace("\n\r", "", $sArquivo));
            fputs($this->arq, "\r\n");

            $contador = count(explode("\n", $sArquivo));
        } else {
            $clreceita_saldo_mes->anousu = (db_getsession('DB_anousu') - 1);
            $clreceita_saldo_mes->dtini = (db_getsession('DB_anousu') - 1) . "-01-01";
            $clreceita_saldo_mes->dtfim = (db_getsession('DB_anousu') - 1) . "-12-31";

            $clreceita_saldo_mes->instit = $instit;
            $sql = $clreceita_saldo_mes->sql_record(true);

            $sql = "
            select
                complemento as complemento_lancamento,
                o70_anousu,
                o70_codrec,
                o70_instit,
                o57_fonte,
                o57_descr,
                round(sum(adicional), 2) as adicional,
                round(sum(janeiro), 2) as janeiro,
                round(sum(fevereiro), 2) as fevereiro,
                round(sum(marco), 2) as marco,
                round(sum(abril), 2) as abril,
                round(sum(maio), 2) as maio,
                round(sum(junho), 2) as junho,
                round(sum(julho), 2) as julho,
                round(sum(agosto), 2) as agosto,
                round(sum(setembro), 2) as setembro,
                round(sum(outubro), 2) as outubro,
                round(sum(novembro), 2) as novembro,
                round(sum(dezembro), 2) as dezembro
             from (
                select x.*,
                   case
                      when complemento_lancamento is not null
                           and complemento_lancamento in (3110, 3120, 3140, 3150, 3160, 1111, 1121, 2111, 2121, 0)
                          then complemento_lancamento
                      else 0
                   end as complemento
                 from ($sql) as x
              ) as y
            group by complemento, o70_anousu, o70_codrec, o70_instit, o57_fonte, o57_descr
            order by o57_fonte";

            $rs = db_query($sql);
            $linhas = pg_num_rows($rs);
            for ($i = 1; $i < $linhas; $i++) {
                db_fieldsmemory($rs, $i);

                // pesquisa orgaotrib
                $orgaotrib = $instituicoes[$o70_instit];

                $line = formatar(substr($o57_fonte, 1, 14), 20, 'n'); // recompisoção

                if ($anousu > 2007) {
                    if (db_conplano_grupo(@$o70_anousu, substr($o57_fonte, 0, 1) . "%", 9000) == false) {
                        $line = formatar(substr($o57_fonte, 1, 14), 20, 'n'); // recompisoção
                    } else {
                        $line = formatar(substr($o57_fonte, 0, 15), 20, 'n'); // recompisoção
                    }
                } else {
                    $line = formatar(substr($o57_fonte, 1, 14), 20, 'n'); // recompisoção
                }

                $line .= formatar($orgaotrib, 4, 'n');

                $line .= $this->acerta_valor($janeiro, 13);
                $line .= $this->acerta_valor($fevereiro, 13);
                $line .= $this->acerta_valor($marco, 13);
                $line .= $this->acerta_valor($abril, 13);
                $line .= $this->acerta_valor($maio, 13);
                $line .= $this->acerta_valor($junho, 13);
                $line .= $this->acerta_valor($julho, 13);
                $line .= $this->acerta_valor($agosto, 13);
                $line .= $this->acerta_valor($setembro, 13);
                $line .= $this->acerta_valor($outubro, 13);
                $line .= $this->acerta_valor($novembro, 13);
                $line .= $this->acerta_valor($dezembro, 13);


                if ($anousu > 2007) {
                    $sql_orcreceita = "
                        select o70_concarpeculiar,
                               o70_codigo,
                               o15_recurso
                          from orcamento.orcreceita
                               inner join orcamento.orctiporec on o15_codigo = o70_codigo
				          where o70_anousu = $anousu
				            and o70_codrec = $o70_codrec";
                    $res_orcreceita = db_query($sql_orcreceita);

                    $codigoRecurso = "0000";
                    $codigoSiconfi = "0000";
                    $concarpeculiar = "000";

                    if (pg_num_rows($res_orcreceita) != 0) {
                        $dadosReceita = db_utils::fieldsMemory($res_orcreceita, 0);
                        $concarpeculiar = formatar($dadosReceita->o70_concarpeculiar, 3, "n");
                        $oRecurso = RecursoRepositoryAlias::getByCodigo($dadosReceita->o70_codigo);
                        $fonteRecurso = $oRecurso->getFonteRecurso($anousu);
                        $codigoSiconfi = substr($fonteRecurso->codigo_siconfi, 1);

                        $codigoRecurso = $oRecurso->getFonteDeRecurso();
                    }

                    $line .= $concarpeculiar . $codigoRecurso;
                }

                $complemento = $complemento_lancamento;
                $complementoSiconfi = $complemento_lancamento;

                // para dados anteriores a 2023 sempre zerar o complemento e recurso do siconfi
                if ($anousu <= 2023) {
                    $complementoSiconfi = '0000';
                    $codigoSiconfi = '0000';
                }

                if (db_getsession('DB_anousu') >= 2020) {
                    //complemento vindo da receita_saldo_mes_complemento
                    $line .= str_pad($complemento, 4, '0', STR_PAD_LEFT);
                }

                if (db_getsession("DB_anousu") == 2022) {
                    $line .= "00000000";
                }

                if (db_getsession("DB_anousu") > 2022) {
                    $line .= str_pad($codigoSiconfi, 4, '0', STR_PAD_LEFT);
                    $line .= str_pad($complementoSiconfi, 4, '0', STR_PAD_LEFT);
                }
                $contador++;
                fputs($this->arq, $line);
                fputs($this->arq, "\r\n");
            }
        }

        //  trailer
        $contador = espaco(10 - (strlen($contador)), '0') . $contador;
        $line = "FINALIZADOR" . $contador;
        fputs($this->arq, $line);
        fputs($this->arq, "\r\n");

        fclose($this->arq);

        $teste = "true";

        @db_query("drop table if exists work_plano");

        return $teste;
    }
}
