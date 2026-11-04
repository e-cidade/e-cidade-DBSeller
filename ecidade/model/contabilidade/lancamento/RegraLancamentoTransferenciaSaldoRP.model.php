<?php

require_once(modification("interfaces/IRegraLancamentoContabil.interface.php"));
/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */


/**
 * Classe responsavel por criar a regra de lancamento de inscricao dos Restos a pagar
 * @author Rafael Lopes
 * @package Contabilidade
 * @subpackage lancamento
 * @version $Revision: 1.0 $
 * Class RegraLancamentoEncerramentoRP
 */
class RegraLancamentoTransferenciaSaldoRP implements IRegraLancamentoContabil
{

    protected $documentosTransferencia = array(2034, 2035);
    /**
     * @param int $iCodigoDocumento
     * @param int $iCodigoLancamento
     * @param ILancamentoAuxiliar| $oLancamentoAuxiliar
     * @return bool|RegraLancamentoContabil
     * @throws Exception
     */
    public function getRegraLancamento($iCodigoDocumento, $iCodigoLancamento,
                                       ILancamentoAuxiliar $oLancamentoAuxiliar)
    {
      //dd($oLancamentoAuxiliar->getNumeroEmpenho());

        if (in_array($iCodigoDocumento, $this->documentosTransferencia)) {

            $oDaoTransacao = new cl_contranslr;
            $sWhere        = "     c45_coddoc      = {$iCodigoDocumento}";
            $sWhere       .= " and c45_anousu      = ".db_getsession("DB_anousu");
            $sWhere       .= " and c46_seqtranslan = {$iCodigoLancamento}";
            $sSqlTransacao = $oDaoTransacao->sql_queryRegraLancamento(null, "*", null, $sWhere);
            $rsTransacao   = $oDaoTransacao->sql_record($sSqlTransacao);

            if ($oDaoTransacao->numrows == 0) {
              $sMsgErro = "Não há lançamentos configurados para o documento {$iCodigoDocumento}.";
              throw new BusinessException($sMsgErro);
            }
            for ($iLinhaRegra = 0; $iLinhaRegra < $oDaoTransacao->numrows; $iLinhaRegra++){

                $oDadosTransacao = db_utils::fieldsMemory($rsTransacao, $iLinhaRegra);

                 $regra = new RegraLancamentoContabil($oDadosTransacao->c47_seqtranslr);

                 /**
                  * Para o doc 2034 somente
                  * se deve inverter as contas é pq o empenho possui os doc 207 e 2 e NAO possui o 209
                  */
                 if ($this->inverterContas($oLancamentoAuxiliar->getNumeroEmpenho()) && $iCodigoDocumento == 2034 ) {

                   $oRegraLancamento = clone $regra;
                   $contaCredito = $regra->getContaCredito();
                   $contaDebito = $regra->getContaDebito();
                   $oRegraLancamento->setContaDebito($contaCredito);
                   $oRegraLancamento->setContaCredito($contaDebito);

                   return $oRegraLancamento;
                 }

                 return $regra;

              }

        }


}


  protected function possuiDocObrigatorio($numeroEmpenho, $docObrigatorio)
  {
    $possuiDocObrigatorio = false;
    $SqlDocObrigatorio = <<<SQL

    select c71_coddoc
      from conlancamemp 
      join conlancamdoc on c75_codlan = c71_codlan
     where c75_numemp = $numeroEmpenho
       and c71_coddoc in ($docObrigatorio)

SQL;
    $rsDocObrigatorio = db_query($SqlDocObrigatorio);
    if (pg_num_rows($rsDocObrigatorio) > 0) {

      $possuiDocObrigatorio = true;
    }  
    return $possuiDocObrigatorio;
  }
   /**
    * vai aplicar uma condicao para saber se inverte debito e credito da regra:
    * se o empenho possui os doc 207 e 2 e NAO possui o 209
    */
   protected function inverterContas($numeroEmpenho)
   {

      $lInverterContas = false;

      $sql = <<<SQL

        select documentos 
          from (
            select array_to_string(array_accum(c71_coddoc),',') as documentos
              from conlancamemp 
              join conlancamdoc on c75_codlan = c71_codlan
             where c75_numemp = $numeroEmpenho
               and c71_coddoc in (203, 205, 207, 2)
          ) as dd
        where documentos <> ''
SQL;
      $rs = db_query($sql);
      if (pg_num_rows($rs) > 0) {

        $documentos = db_utils::fieldsMemory($rs, 0)->documentos;
        $documentos = explode(',', $documentos);

        // se possuir os doc 207 e 2 verificamos se possui o 209
        // se nao possuir o 209 deverá inverter as contas

        switch ($documentos) {

            case (in_array(207, $documentos) && in_array(2, $documentos)):

            $lInverterContas = !$this->possuiDocObrigatorio($numeroEmpenho, '209');
            break;

          case (in_array(203, $documentos) && in_array(2, $documentos)):

            $lInverterContas = !$this->possuiDocObrigatorio($numeroEmpenho, '201');
            break;

          case (in_array(205, $documentos) && in_array(2, $documentos)):  

            $lInverterContas = !$this->possuiDocObrigatorio($numeroEmpenho, '211');
            break;

        }

      }
      return $lInverterContas;
   } 

}