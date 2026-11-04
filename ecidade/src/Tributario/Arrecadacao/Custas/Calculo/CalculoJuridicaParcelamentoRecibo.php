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

namespace ECidade\Tributario\Arrecadacao\Custas\Calculo;

use db_utils;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Divida\Termo\Termo;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use \Recibo;

final class CalculoJuridicaParcelamentoRecibo extends CalculoColecao implements Interfaces\Calculo
{
    private $recibo;

    private $processoForo;

    private $termo;

    public function __construct(Recibo $recibo, ProcessoForo $processoForo, Termo $termo)
    {
        $this->recibo = $recibo;
        $this->processoForo = $processoForo;
        $this->termo = $termo;
    }

    public function calcular()
    {
        $dataVencimento = $this->recibo->getDataVencimento();
        $anoVencimento = substr($this->recibo->getDataVencimento(), 0, 4);
        $processoForo = $this->processoForo->getCodigo();
        $numpre = $this->termo->getNumpre();



        $sSqlReparcelamento = "select min(rinumpre) as numpre_origem from fc_origemparcelamento($numpre);";
        $rsOrigem = db_query($sSqlReparcelamento);
        $numpre = \db_utils::fieldsMemory($rsOrigem, 0)->numpre_origem;


        $sql = "select 
                        sum(fc_corre(arreold.k00_receit, 
                                     arreold.k00_dtoper, 
                                     k00_valor, 
                                     '$dataVencimento', 
                                     $anoVencimento, 
                                     arreold.k00_dtvenc)) +                        
                        sum(round((fc_corre(arreold.k00_receit, 
                                            arreold.k00_dtoper, 
                                            k00_valor, 
                                            '$dataVencimento', 
                                            $anoVencimento, 
                                            arreold.k00_dtvenc)) * 
                            fc_juros(arreold.k00_receit, arreold.k00_dtvenc, 
                                    '$dataVencimento', 
                                    arreold.k00_dtoper, 
                                    false,$anoVencimento)::numeric(20, 10) ,2) ) +      
                        sum(round((fc_corre(arreold.k00_receit, 
                                            arreold.k00_dtoper, 
                                            k00_valor, 
                                            '$dataVencimento', 
                                            $anoVencimento, 
                                            arreold.k00_dtvenc)) * 
                            fc_multa(arreold.k00_receit, 
                                     arreold.k00_dtvenc, 
                                     '$dataVencimento', 
                                     arreold.k00_dtoper, 
                                     $anoVencimento)::numeric(20, 10), 2)) as valor 
                   from termo 
                        inner join termoini on termoini.parcel = termo.v07_parcel 
                        inner join processoforoinicial on processoforoinicial.v71_inicial = termoini.inicial 
                        inner join inicialcert on inicialcert.v51_inicial = termoini.inicial 
                        inner join certdiv on certdiv.v14_certid = inicialcert.v51_certidao 
                        inner join divida on divida.v01_coddiv = certdiv.v14_coddiv 
                        inner join arreold on arreold.k00_numpre = divida.v01_numpre 
                                          and arreold.k00_numpar = divida.v01_numpar 
                        inner join arreoldcalc on arreoldcalc.k00_numpre = arreold.k00_numpre 
                                              and arreoldcalc.k00_numpar = arreold.k00_numpar 
                                              and arreoldcalc.k00_receit = arreold.k00_receit 
                  where termo.v07_numpre = $numpre
                    and processoforoinicial.v71_processoforo = $processoForo                 
                    
                    ";

        $sSqlProcessoForoDebitos = "
        
          SELECT coalesce(sum(fc_corre(v91_receit, 
                                       v91_dtoper, 
                                       v91_vlrhist, 
                                       '$dataVencimento', 
                                       $anoVencimento, 
                                       v91_dtvenc)) +
                 sum(round((fc_corre(v91_receit, 
                                     v91_dtoper, 
                                     v91_vlrhist, 
                                     '$dataVencimento', 
                                     $anoVencimento, 
                                     v91_dtvenc)) *
                 fc_juros(v91_receit, 
                          v91_dtvenc, 
                          '$dataVencimento', 
                          v91_dtoper, 
                          FALSE, 
                          $anoVencimento)::numeric(20, 10), 2)) +
                   sum(round((fc_corre(v91_receit, 
                                       v91_dtoper, 
                                       v91_vlrhist, 
                                       '$dataVencimento', 
                                       $anoVencimento, 
                                       v91_dtvenc)) *
                   fc_multa(v91_receit, 
                            v91_dtvenc, 
                            '$dataVencimento', 
                            v91_dtoper, 
                            $anoVencimento)::numeric(20, 10), 2)), 0) AS valor
           FROM processoforodebitos
          where v91_processoforo = $processoForo;                    
        ";
        $rsProcessoForoDebitos = db_query($sSqlProcessoForoDebitos);
        if (pg_numrows($rsProcessoForoDebitos) > 0) {
            $nValor = \db_utils::fieldsMemory($rsProcessoForoDebitos, 0)->valor;
            if ($nValor > 0) {
                $sql =  $sSqlProcessoForoDebitos;
            }
        }


        //valida se tiver valores de iniciais mistas
/*
        $sSqlInicialMista = "

        select
        coalesce( sum(fc_corre(arreold.k00_receit,
                       arreold.k00_dtoper,
                       k00_valor,
                       '$dataVencimento',
                       $anoVencimento, arreold.k00_dtvenc)) +
        sum(round((fc_corre(arreold.k00_receit,
                            arreold.k00_dtoper,
                            k00_valor,
                            '$dataVencimento',
                             $anoVencimento, arreold.k00_dtvenc)) *
            fc_juros(arreold.k00_receit,
                      arreold.k00_dtvenc,
                      '$dataVencimento',
                      arreold.k00_dtoper,
                      false,$anoVencimento)::numeric(20, 10) ,2) ) +
        sum(round((fc_corre(arreold.k00_receit,
                     arreold.k00_dtoper,
                     k00_valor,
                     '$dataVencimento',
                     $anoVencimento,
                     arreold.k00_dtvenc)) *
            fc_multa(arreold.k00_receit,
                     arreold.k00_dtvenc,
                     '$dataVencimento',
                     arreold.k00_dtoper,
                      $anoVencimento)::numeric(20, 10), 2)),0) as valor
            FROM termo
      INNER JOIN termoini ON termoini.parcel = termo.v07_parcel
      INNER JOIN processoforoinicial ON processoforoinicial.v71_inicial = termoini.inicial
      INNER JOIN inicialcert ON inicialcert.v51_inicial = termoini.inicial
      INNER JOIN certter ON certter.v14_certid = inicialcert.v51_certidao
      inner join termodiv on termodiv.parcel = v14_parcel
      INNER JOIN divida ON divida.v01_coddiv = termodiv.coddiv
      INNER JOIN arreold ON arreold.k00_numpre = divida.v01_numpre
                        AND arreold.k00_numpar = divida.v01_numpar
      INNER JOIN arreoldcalc ON arreoldcalc.k00_numpre = arreold.k00_numpre
                            AND arreoldcalc.k00_numpar = arreold.k00_numpar
                            AND arreoldcalc.k00_receit = arreold.k00_receit
  where termo.v07_numpre = $numpre
    and processoforoinicial.v71_processoforo = $processoForo

        ";
        $rsIniciaisMista = db_query( $sSqlInicialMista);
        if (  pg_numrows($rsIniciaisMista) > 0 ) {

            $nValor = \db_utils::fieldsMemory($rsIniciaisMista,0  )->valor;
            if ($nValor > 0 ) {

                throw new \Exception("Parcelamento de Iniciais Mistas, contate suporte.");
            }
        }
*/


        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Não foi possivel consultar o valor base de calculo das custas.");
        }

        return $this->colecao($rs);
    }

    protected function colecao($rs)
    {
        $linhaComValorCalculado = pg_fetch_object($rs, 0);
        $valor = $linhaComValorCalculado->valor;
        $valores = array();

        foreach (range(1, $this->termo->getTotalParcelas()) as $parcela) {
            $valores[$parcela] = $this->factory($valor, 0, 0, 0);
        }

        return $valores;
    }
}
