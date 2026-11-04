<?php
/**
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

namespace ECidade\Tributario\Arrecadacao\Custas\Validador;

use db_utils;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use \Recibo;

final class FixaJuridicaRecibo implements Interfaces\Validador
{
    private $validacao;

    private $recibo;
    
    private $processoForo;

    public function __construct(Recibo $recibo, ProcessoForo $processoForo)
    {
        $this->recibo = $recibo;
        $this->processoForo = $processoForo;
    }

    public function processarValidacao()
    {
        // |-0.5 Processo do foro tem termotaxafixa de termo nao anulado?
        // |--sim
        // |  | remove as fixas
        // |--nao
        // |  | continua processamento
        // |
        // |-1 Eu recibo tenho todos os debitos desse processo que não esteja parcelado?
        // |--sim
        // |  | mantem as fixas
        // |--nao
        // |  |-2 O ultimo recibo valido com custas fixas tem todos os debitos nao parcelados do processo?
        // |  |--sim
        // |  |  |-3 Todos os debitos do processo nao parcelados tem recibos sem custas depois do recibo geral?
        // |  |  |--sim
        // |  |  |  | mantem as fixas
        // |  |  |--nao
        // |  |  |  | remove as fixas
        // |  |--nao
        // |  |  |-4 Eu tenho debitos do processo do ultimo recibo valido com custas?
        // |  |  |--sim
        // |  |  |  | mantem as fixas
        // |  |  |--nao
        // |  |  |  |-5 Todos os debitos tem recibo valido?
        // |  |  |  |--nao
        // |  |  |  |  | remove as fixas
        // |  |  |  |--sim
        // |  |  |  |  |-6 Eu tenho os ultimos debitos sem recibo?
        // |  |  |  |  |--sim
        // |  |  |  |  |  | mantem as fixas
        // |  |  |  |  |--nao
        // |  |  |  |  |  | remove as fixas
        
        $remover = true;

        $numnov = $this->recibo->getNumpreRecibo();
        $processoForo = $this->processoForo->getCodigo();
       
        
        $sql = "select 1 as total 
                  from termotaxafixa 
                       inner join termo on termo.v07_parcel = termotaxafixa.ar42_parcel
                                       and termo.v07_situacao = 1
                 where ar42_processoforo = $processoForo 
                   and ar42_fixa is true ";

        $rs = db_query($sql);
        
        if (pg_num_rows($rs) > 0) {
            return true;
        }

        // 1
        $sql = "select (
                select count(distinct inicialnumpre.v59_numpre)
                  from processoforoinicial
                       inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
                       inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
                       left join termoini on termoini.inicial = inicial.v50_inicial
                       left join termo on termo.v07_parcel = termoini.parcel
                                       and termo.v07_situacao = 1
                 where processoforoinicial.v71_processoforo = $processoForo
                   and processoforoinicial.v71_anulado is false  
                   and termo.v07_parcel is null
                ) - (
                select count(distinct recibopaga.k00_numpre)
                  from recibopaga
                       inner join inicialnumpre on inicialnumpre.v59_numpre = recibopaga.k00_numpre
                       inner join inicial on inicial.v50_inicial = inicialnumpre.v59_inicial
                       inner join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial 
                 where recibopaga.k00_numnov = $numnov 
                   and processoforoinicial.v71_processoforo = $processoForo
                   and processoforoinicial.v71_anulado is false                                                     
                ) as total ";

        $resource = db_query($sql);

        $result = pg_fetch_object($resource, 0);

        if ($result->total == 0) {
            $this->log("1");

            $remover = false;
        } else {
            $this->log("2");

            // 2
            $sql = "select (
                    select count(distinct inicialnumpre.v59_numpre)
                      from processoforoinicial
                           inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
                           inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
                           left join termoini on termoini.inicial = inicial.v50_inicial
                           left join termo on termo.v07_parcel = termoini.parcel
                                           and termo.v07_situacao = 1
                     where processoforoinicial.v71_processoforo = $processoForo 
                       and termo.v07_parcel is null
                    ) - (
                    select count(distinct recibopaga.k00_numpre)
                      from recibopaga
                           inner join inicialnumpre on inicialnumpre.v59_numpre = recibopaga.k00_numpre
                           inner join inicial on inicial.v50_inicial = inicialnumpre.v59_inicial
                           inner join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial 
                     where recibopaga.k00_numnov IN (
                            select distinct recibopaga.k00_numnov
                              from processoforoinicial
                               inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
                               inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
                               left join termoini on termoini.inicial = inicial.v50_inicial
                               left join termo on termo.v07_parcel = termoini.parcel
                                               and termo.v07_situacao = 1
                               inner join recibopaga on recibopaga.k00_numpre = inicialnumpre.v59_numpre
                               inner join taxa on taxa.ar36_receita = recibopaga.k00_receit
                               left join disbanco on disbanco.k00_numpre = recibopaga.k00_numnov

                               where processoforoinicial.v71_processoforo = $processoForo 
                                 and termo.v07_parcel is null
                                 and taxa.ar36_valor > 0
                                 and recibopaga.k00_dtpaga >= CURRENT_DATE
                                 and disbanco.k00_numpre is null
    order by 1 desc limit 1
                        ) 
                       and processoforoinicial.v71_processoforo = $processoForo 
                    ) as total
            ";

            $resource = db_query($sql);

            $result = pg_fetch_object($resource, 0);

            if ($result->total == 0) {
                $this->log("3");

                // 3
                $sql = "

select (
select count(distinct inicial.v50_inicial)
  from processoforoinicial
       inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
       inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
       left join termoini on termoini.inicial = inicial.v50_inicial
       left join termo on termo.v07_parcel = termoini.parcel
                       and termo.v07_situacao = 1
 where processoforoinicial.v71_processoforo = $processoForo 

)-(

select 

(

select count(distinct inicial.v50_inicial)
  from recibopaga
       inner join inicialnumpre on inicialnumpre.v59_numpre = recibopaga.k00_numpre
       inner join inicial on inicial.v50_inicial = inicialnumpre.v59_inicial
       inner join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial 
 where recibopaga.k00_numnov > (
        select distinct recibopaga.k00_numnov
          from processoforoinicial
           inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
           inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
           left join termoini on termoini.inicial = inicial.v50_inicial
           left join termo on termo.v07_parcel = termoini.parcel
                           and termo.v07_situacao = 1
           inner join recibopaga on recibopaga.k00_numpre = inicialnumpre.v59_numpre
           inner join taxa on taxa.ar36_receita = recibopaga.k00_receit
           left join disbanco on disbanco.k00_numpre = recibopaga.k00_numnov

           where processoforoinicial.v71_processoforo = $processoForo 
             and termo.v07_parcel is null
             and taxa.ar36_valor > 0
             and recibopaga.k00_dtpaga >= CURRENT_DATE
             and disbanco.k00_numpre is null
        order by 1 desc limit 1
    ) 
   and processoforoinicial.v71_processoforo = $processoForo 
   and recibopaga.k00_numnov != $numnov 
)
+
(

select count(distinct inicial.v50_inicial)
  from recibopaga
       inner join inicialnumpre on inicialnumpre.v59_numpre = recibopaga.k00_numpre
       inner join inicial on inicial.v50_inicial = inicialnumpre.v59_inicial
       inner join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial 
 where recibopaga.k00_numnov = $numnov 

)


) as total
                ";

                $resource = db_query($sql);

                $result = pg_fetch_object($resource, 0);

                $this->log("DEPOIS TO RECIBAO -> ".$result->total." inicias não tem recibo");
                
                if ($result->total <= 0) {
                    $remover = false;
                } else {
                    $remover = true;
                }
            } else {
                //4

                $this->log("4");
                $sql = "select 1
                          from recibopaga
                         where recibopaga.k00_numnov = $numnov
                           and recibopaga.k00_numpre in (select v59_numpre
                                                           from inicialnumpre
                                                          where inicialnumpre.v59_inicial in (

                        select distinct inicial.v50_inicial
                          from recibopaga
                               inner join inicialnumpre on inicialnumpre.v59_numpre = recibopaga.k00_numpre
                               inner join inicial on inicial.v50_inicial = inicialnumpre.v59_inicial
                               inner join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial 
                         where recibopaga.k00_numnov IN (
                                select distinct recibopaga.k00_numnov
                                  from processoforoinicial
                                   inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
                                   inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
                                   left join termoini on termoini.inicial = inicial.v50_inicial
                                   left join termo on termo.v07_parcel = termoini.parcel
                                                   and termo.v07_situacao = 1
                                   inner join recibopaga on recibopaga.k00_numpre = inicialnumpre.v59_numpre
                                   inner join taxa on taxa.ar36_receita = recibopaga.k00_receit
                                   left join disbanco on disbanco.k00_numpre = recibopaga.k00_numnov

                                   where processoforoinicial.v71_processoforo = $processoForo 
                                     and termo.v07_parcel is null
                                     and taxa.ar36_valor > 0
                                     and recibopaga.k00_dtpaga >= CURRENT_DATE
                                     and disbanco.k00_numpre is null
                                order by recibopaga.k00_numnov desc 
                                limit 1
                            ) 
                           and processoforoinicial.v71_processoforo = $processoForo 
                        ))
                        limit 1
                ";

                $resource = db_query($sql);

                if (pg_num_rows($resource) > 0) {
                    $remover = false;
                } else {
                    $this->log("5");
                    $sql = "
                            select (
                                    select array_agg(distinct inicial.v50_inicial)
                                      from processoforoinicial
                                           inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
                                           inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
                                           left join termoini on termoini.inicial = inicial.v50_inicial
                                           left join termo on termo.v07_parcel = termoini.parcel
                                                           and termo.v07_situacao = 1
                                     where processoforoinicial.v71_processoforo = $processoForo 

                                    ) <> (

                                    select array_agg(distinct inicial.v50_inicial)
                                      from processoforoinicial
                                           inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
                                           inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
                                           left join termoini on termoini.inicial = inicial.v50_inicial
                                           left join termo on termo.v07_parcel = termoini.parcel
                                                           and termo.v07_situacao = 1
                                           inner join recibopaga on recibopaga.k00_numpre = inicialnumpre.v59_numpre
                                           left join disbanco on disbanco.k00_numpre = recibopaga.k00_numnov
                                     where processoforoinicial.v71_processoforo = $processoForo 
                                       and termo.v07_parcel is null
                                       and recibopaga.k00_dtpaga >= CURRENT_DATE
                                       and disbanco.k00_numpre is null
                                       and recibopaga.k00_numnov < $numnov 

                                    ) as todos_tem
                    ";

                    $resource = db_query($sql);

                    $result = pg_fetch_object($resource, 0);

                    if ($result->todos_tem != 't') {
                        if (db_utils::ativaParcelamentoCustas()) {
                            $remover = false;
                        } else {
                            $remover = true;
                        }
                    } else {
                        $this->log("6");
                        // 6
                        $sql = "

    select (
    select array_agg(distinct inicial.v50_inicial)
      from processoforoinicial
           inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
           inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
           left join termoini on termoini.inicial = inicial.v50_inicial
           left join termo on termo.v07_parcel = termoini.parcel
                           and termo.v07_situacao = 1
     where processoforoinicial.v71_processoforo = $processoForo
    ) <> (
    select array_agg(distinct v50_inicial) from (

    select inicial.v50_inicial
      from processoforoinicial
           inner join inicial on inicial.v50_inicial = processoforoinicial.v71_inicial
           inner join inicialnumpre on inicialnumpre.v59_inicial = inicial.v50_inicial
           left join termoini on termoini.inicial = inicial.v50_inicial
           left join termo on termo.v07_parcel = termoini.parcel
                           and termo.v07_situacao = 1
           inner join recibopaga on recibopaga.k00_numpre = inicialnumpre.v59_numpre
           left join disbanco on disbanco.k00_numpre = recibopaga.k00_numnov
     where processoforoinicial.v71_processoforo = $processoForo
       and termo.v07_parcel is null
       and recibopaga.k00_dtpaga >= CURRENT_DATE
       and disbanco.k00_numpre is null
       and k00_numnov < $numnov 
    union 
    select inicial.v50_inicial
      from recibopaga
           inner join inicialnumpre on inicialnumpre.v59_numpre = recibopaga.k00_numpre
           inner join inicial on inicial.v50_inicial = inicialnumpre.v59_inicial
           inner join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial 
     where recibopaga.k00_numnov = $numnov 
    ) as x
    ) as tem_tudo 
                        ";

                        $resource = db_query($sql);

                        $result = pg_fetch_object($resource, 0);

                        $this->log("LAST da GALERA -> ".$result->tem_tudo." <-");
                        $this->log("LAST da GALERA -> ".print_r($result->tem_tudo, 1)." <-");
                        
                        if ($result->tem_tudo == 't') {
                            $remover = true;
                        } else {
                            $remover = false;
                        }
                    }
                }
            }
        }

        $this->log(" ");

        return $remover;
    }

    private function log($s)
    {
        // file_put_contents("tmp/log.txt", $s . "\n", FILE_APPEND);
    }
}
