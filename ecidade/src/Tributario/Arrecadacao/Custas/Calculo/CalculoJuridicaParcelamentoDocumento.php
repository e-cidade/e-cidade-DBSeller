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

use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Divida\Termo\Termo;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use \Exception;

final class CalculoJuridicaParcelamentoDocumento extends CalculoColecao implements Interfaces\Calculo
{
    private $termo;

    private $processo;

    public function __construct(Termo $termo, ProcessoForo $processo)
    {
        $this->termo = $termo;
        $this->processo = $processo;
    }

    public function calcular()
    {

        try {
            $iTermo = Termo::getOrigemTermo($this->termo->getCodigo());
            
            $sql = " select generate_series(1, termo.v07_totpar) as numpar,
                            sum(arreoldcalc.k00_vlrcor + arreoldcalc.k00_vlrjur + 
                                 arreoldcalc.k00_vlrmul + arreoldcalc.k00_vlrdes) as valor 
                       from termo                                          
                            inner join termoini on termoini.parcel = termo.v07_parcel     
                            inner join inicialcert on inicialcert.v51_inicial = termoini.inicial
                            inner join processoforoinicial on processoforoinicial.v71_inicial = termoini.inicial  
                            inner join certdiv on certdiv.v14_certid = inicialcert.v51_certidao                   
                            inner join divida on divida.v01_coddiv = certdiv.v14_coddiv                           
                            inner join arreold on arreold.k00_numpre = divida.v01_numpre                          
                                              and arreold.k00_numpar = divida.v01_numpar                          
                            inner join arreoldcalc on arreoldcalc.k00_numpre = arreold.k00_numpre                 
                                                  and arreoldcalc.k00_numpar = arreold.k00_numpar                 
                                                  and arreoldcalc.k00_receit = arreold.k00_receit                 
                      where termo.v07_parcel = {$iTermo}
                        and processoforoinicial.v71_processoforo = {$this->processo->getCodigo()}
                      group by numpar
                       
                      UNION 

                     select generate_series(1, termo.v07_totpar) as numpar,
                                   sum(certter.v14_vlrcor + certter.v14_vlrjur + certter.v14_vlrmul) as valor
                       from termo
                            inner join termoini ON termoini.parcel = termo.v07_parcel
                            inner join inicialcert ON inicialcert.v51_inicial = termoini.inicial
                            inner join certter  ON certter.v14_certid = inicialcert.v51_certidao
                            inner join processoforoinicial on processoforoinicial.v71_inicial = inicialcert.v51_inicial
                            where termo.v07_parcel = {$iTermo}
                              and processoforoinicial.v71_processoforo = {$this->processo->getCodigo()}
                            group by numpar
                         order by numpar";

            $sSqlProcessoForoDebitos = "
            
                            select generate_series(1, termo.v07_totpar) as numpar,
                                   sum(arreoldcalc.k00_vlrcor + arreoldcalc.k00_vlrjur + 
                                       arreoldcalc.k00_vlrmul + arreoldcalc.k00_vlrdes) as valor
                             FROM processoforodebitos 
                             inner join termo on  v91_numpre = v07_numpre
                             inner join arreoldcalc on k00_numpre = v91_numpre
                                                   and k00_numpar = v91_numpar
                                                   and k00_receit = v91_receit 
                            where v91_processoforo = {$this->processo->getCodigo()}
                            group by numpar 
                            order by 1 asc 
                    ";

            $rsProcessoForoDebitos = db_query($sSqlProcessoForoDebitos);

            if (pg_numrows($rsProcessoForoDebitos) > 0) {
                $nValor = \db_utils::fieldsMemory($rsProcessoForoDebitos, 0)->valor;
                if ($nValor > 0) {
                    $sql =  $sSqlProcessoForoDebitos;
                }
            }

            $rs = db_query($sql);

            if (!$rs || pg_numrows($rs) <= 0) {
                throw new Exception("Não foi possivel consultar o valor base de calculo das custas.");
            }
        } catch (Exception $exception) {
            db_redireciona("db_erros.php?fechar=true&db_erro={$exception->getMessage()}");
            exit;
        }

        return $this->colecao($rs);
    }

    protected function colecao($rs)
    {
        $rows = pg_fetch_all($rs);

        $valores = array();

        foreach ($rows as $row) {
            $valores[$row["numpar"]] = $this->factory($row["valor"], 0, 0, 0);
        }

        return $valores;
    }
}
