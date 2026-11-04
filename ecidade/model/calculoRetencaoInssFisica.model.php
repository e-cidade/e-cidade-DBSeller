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

use App\Domain\Patrimonial\Protocolo\Model\Cgm;

require_once(modification('interfaces/calculoRetencao.interface.php'));
/**
 * realizada o calculo de retencoes do tipo pessoa fisica
 * @package empenho
 */
class calculoRetencaoInssFisica implements iCalculoRetencao {

  /**
   * tipo do calculo
   * @var integer
   */
  private $iTipo = 3;

  /**
   * Valor da base de calculo
   *
   * @var float
   */
  private $nValorBaseCalculo = 0;

  /** Valor da Dedução informado pelo usuário.
   *
   * @var float
   */
  private $nValorDeducao = 0;

  /**
   * cpf ou cnpj a calcular o imposto.
   *
   * @var integer
   */
  private $iCgcCpf = null;

  /**
   * Tabela do IRRF a ser usada
   *
   * @var object
   */
  private $oTabelaInss = null;

  /**
   * Valor da Aliquota
   *
   * @var float
   */
  private $nAliquota  = 0;

  /**
   * Valor a ser adicionado da nota
   *
   * @var float
   */
  private $nValorNota = 0;

  public $cgm;

  /**
   * Códigos Auxiliares doe Movimentos
   *
   * @var array
   */
  private $aCodigoMovimentos = array();

  /** metodo construtor da classe
   * @param integer $iTipo tipo do calculo 1 - pessoa fisica
   */
  public function __construct($iCgcCpf, $iTipo = 3) {

    $this->iTipo   = $iTipo;
    $this->iCgcCpf = (string)$iCgcCpf;
    $this->cgm = CgmFactory::getInstanceByCnpjCpf($this->iCgcCpf);

  }

  /**
   *
   * @see iCalculoRetencao::calculaBasedeCalculo()
   */
  public function calculaBasedeCalculo() {

    /*
     * calculo da base calculo;
     * 1 - Buscamos todos as notas liquidadas do CGM(cpf) dentro do mes e
     *     somamos todas elas, e usamos como base de calculo inicial.
     * 2 - depois deduzimos as deducoes já cadastradas.
     */

    $nValorBaseCalculo = 0;
    if (empty($this->iCgcCpf)) {
      throw new Exception("Erro [2] CPF/CNPJ não Informado!\nOperação cancelada");
    }

    $oDaoNota   = new cl_empnota;
    $iMesBase   = date("m", db_getsession("DB_datausu"));
    $iAnoBase   = db_getsession("DB_anousu");
    $iInstit    = db_getsession("DB_instit");
    if ($this->dtBaseCalculo != "") {


      $iMesBase  = date("m", strtotime($this->dtBaseCalculo));
      $iAnoBase  = date("Y", strtotime($this->dtBaseCalculo));

    }
    /*
     * Buscamos nessa consulta, todos os valores pagos no mes ao credor.
     */
    $sSqlNotas  = "select coalesce(sum((case when c71_coddoc = 5 then c70_valor when c71_coddoc = 6 then c70_valor *-1 end)),0) as total";
    $sSqlNotas .= "  from conlancamord                                          ";
    $sSqlNotas .= "       inner join conlancam    on c70_codlan =  c80_codlan      ";
    $sSqlNotas .= "       inner join conlancamdoc on c70_codlan = c71_codlan    ";
    $sSqlNotas .= "       inner join pagordem on c80_codord = e50_codord        ";
    $sSqlNotas .= "       inner join empempenho   on e50_numemp = e60_numemp      ";
    $sSqlNotas .= "       inner join cgm              on e60_numcgm           = cgm.z01_numcgm    ";
    $sSqlNotas .= "       left  join pagordemconta    on e49_codord           = e50_codord  ";
    $sSqlNotas .= "       left  join cgm cgmordem     on e49_numcgm           = cgmordem.z01_numcgm  ";
    $sSqlNotas .= " where extract (year from c70_data)  = {$iAnoBase}        ";
    $sSqlNotas .= "   and extract (month from c70_data)  = {$iMesBase}        ";
    $sSqlNotas .= "   and (case when e49_numcgm is null then cgm.z01_cgccpf = '{$this->iCgcCpf}'  ";
    $sSqlNotas .= "        else cgmordem.z01_cgccpf = '{$this->iCgcCpf}' end)                      ";
    $rsNotas    = $oDaoNota->sql_record($sSqlNotas);
    if ($oDaoNota->numrows > 0) {

      $oNotas            = db_utils::fieldsMemory($rsNotas, 0);
      $nValorBaseCalculo = $oNotas->total - $this->nValorDeducao;

    }

    /*
    * Adiciona o valor selecionado para composicao da base de calculo
    */
   if (  isset($this->aCodigoMovimentos) && !empty($this->aCodigoMovimentos) && count($this->aCodigoMovimentos) > 0) {
      $sWhereIn         = implode(",",$this->aCodigoMovimentos);
      $sSqlComposicao = " select ";
      $sSqlComposicao .= "       sum(round(";
      $sSqlComposicao .= "           e81_valor + (";
      $sSqlComposicao .= "               select";
      $sSqlComposicao .= "                   coalesce(sum(e34_valordesconto), 0)";
      $sSqlComposicao .= "               from";
      $sSqlComposicao .= "                   pagordemdesconto";
      $sSqlComposicao .= "               where";
      $sSqlComposicao .= "                   e34_codord = e50_codord";
      $sSqlComposicao .= "           ),";
      $sSqlComposicao .= "           2";
      $sSqlComposicao .= "       )) as e81_valor";
      $sSqlComposicao .= "   from";
      $sSqlComposicao .= "       empage";
      $sSqlComposicao .= "       inner join empagemov on empagemov.e81_codage = empage.e80_codage";
      $sSqlComposicao .= "       inner join empord on empord.e82_codmov = empagemov.e81_codmov";
      $sSqlComposicao .= "       inner join pagordem on pagordem.e50_codord = empord.e82_codord";
      $sSqlComposicao .= "       inner join pagordemele on pagordemele.e53_codord = pagordem.e50_codord";
      $sSqlComposicao .= "       inner join empempenho on empempenho.e60_numemp = pagordem.e50_numemp";
      $sSqlComposicao .= "       inner join cgm on cgm.z01_numcgm = empempenho.e60_numcgm";
      $sSqlComposicao .= "       left join empageconcarpeculiar on empageconcarpeculiar.e79_empagemov = empagemov.e81_codmov";
      $sSqlComposicao .= "       left join corempagemov on corempagemov.k12_codmov = empagemov.e81_codmov";
      $sSqlComposicao .= "       left join empagemovconta on empagemov.e81_codmov = e98_codmov";
      $sSqlComposicao .= "       left join empageconf on empageconf.e86_codmov = empord.e82_codmov";
      $sSqlComposicao .= "       left join empageconfche on empageconf.e86_codmov = e91_codmov";
      $sSqlComposicao .= "       and e91_ativo is true";
      $sSqlComposicao .= "       left join empagepag on e85_codmov = e81_codmov";
      $sSqlComposicao .= "       left join empagetipo on e85_codtipo = e83_codtipo";
      $sSqlComposicao .= "       left join pagordemnota on e71_codord = e50_codord";
      $sSqlComposicao .= "       left join empnota on e69_codnota = e71_codnota";
      $sSqlComposicao .= "       left join corgrupocorrente on k105_data = corempagemov.k12_data";
      $sSqlComposicao .= "       and k105_autent = corempagemov.k12_autent";
      $sSqlComposicao .= "       and k105_id = corempagemov.k12_id";
      $sSqlComposicao .= "   where";
      $sSqlComposicao .= "       e81_codmov in ($sWhereIn)";
      $sSqlComposicao .= "       and e71_anulado is false";
      $sSqlComposicao .= "       and ";
      $sSqlComposicao .= "           k12_data is null";
      $sSqlComposicao .= "           ";
      $sSqlComposicao .= "       and (";
      $sSqlComposicao .= "           e81_cancelado is null";
      $sSqlComposicao .= "           and case";
      $sSqlComposicao .= "               when k12_data is not null then k105_corgrupotipo = 1";
      $sSqlComposicao .= "               else true";
      $sSqlComposicao .= "           end";
      $sSqlComposicao .= "       )";
      $rsComposicao    = $oDaoNota->sql_record($sSqlComposicao);
    
      if ($oDaoNota->numrows > 0) {
        $oComposicao            = db_utils::fieldsMemory($rsComposicao, 0);
        $nValorBaseCalculo += $oComposicao->e81_valor;

      }
    }
    if ($nValorBaseCalculo < 0) {
      $nValorBaseCalculo = 0;
    }

    $this->nValorBaseCalculo = ($nValorBaseCalculo+$this->nValorNota)-$this->nValorDeducao;
    return $this->nValorBaseCalculo;

  }

  /**
   *
   * @see iCalculoRetencao::calcularRetencao()
   */
  public function calcularRetencao() {

    $this->nValorBaseCalculo = $this->calculaBasedeCalculo();
    $this->getTabelaInss();
    $this->nAliquota = $this->oTabelaInss->r33_perc;
    $produtoresRurais = array(35, 4120);

    $nValorRetido = 0;
    /**
     * Calculamos o valor da retencao, multiplicando  a base de calculo pela aliquota;
     */
    if ($this->nValorBaseCalculo > $this->oTabelaInss->teto) {
      $this->nValorBaseCalculo = $this->oTabelaInss->teto;
    }

    /**
     * vamos verificar se o fornecedor é uma pessoa juridica
     * se tem o tipoempresa = 35 - produtor rural
     * a aliquota deverá ser a mesma do cadastro
     * e o valor base será o valor da nota
     */
    $oFornecedor = new fornecedor($this->cgm->getCodigo());
    $produtorRural = false;
    if ( isset($oFornecedor->getCgm()->getTipoEmpresa()[0]->z03_tipoempresa) && !empty($oFornecedor->getCgm()->getTipoEmpresa()[0]->z03_tipoempresa) ) {

        $produtorRural = in_array($oFornecedor->getCgm()->getTipoEmpresa()[0]->z03_tipoempresa, $produtoresRurais);

        if ($produtorRural) {

            $this->nAliquota = $this->nValorAliquota;
            $this->nValorBaseCalculo = $this->nValorNota;
        }
    }

    //$nValorRetido = $this->nValorBaseCalculo * ($this->oTabelaInss->r33_perc/100);
    $nValorRetido = $this->nValorBaseCalculo * ($this->nAliquota / 100);
    $n2 = $nValorRetido;

    /**
     * Buscamos o valor já retido no mes e deduzimos do valor retido.
     */
    $oDaoNota         = new cl_empnota;
    $iMesBase         = date("m", db_getsession("DB_datausu"));
    $iAnoBase         = db_getsession("DB_anousu");
    $iInstit          = db_getsession("DB_instit");
    $sSqlValorRetido  = "select sum(e23_valorretencao) as valorRetido                               ";
    $sSqlValorRetido .= "  from retencaoreceitas                                                    ";
    $sSqlValorRetido .= "       inner join retencaopagordem on e23_retencaopagordem = e20_sequencial";
    $sSqlValorRetido .= "       inner join retencaotiporec  on e23_retencaotiporec  = e21_sequencial";
    $sSqlValorRetido .= "       inner join pagordemnota     on e20_pagordem         = e71_codord    ";
    $sSqlValorRetido .= "       inner join empnota          on e71_codnota          = e69_codnota   ";
    $sSqlValorRetido .= "       inner join empempenho       on e69_numemp           = e60_numemp    ";
    $sSqlValorRetido .= "       inner join cgm              on e60_numcgm           = z01_numcgm    ";
    $sSqlValorRetido .= " where e69_anousu                       = {$iAnoBase}                     ";
    $sSqlValorRetido .= "   and extract(month from e23_dtcalculo) = {$iMesBase}                     ";
    $sSqlValorRetido .= "   and z01_cgccpf                        = '{$this->iCgcCpf}'              ";
    $sSqlValorRetido .= "   and e23_recolhido is true                                               ";
    $sSqlValorRetido .= "   and e23_ativo     is true                                               ";
    $sSqlValorRetido .= "   and e71_anulado  is false                                               ";
    $sSqlValorRetido .= "   and e21_retencaotipocalc = {$this->iTipo}                               ";
    $rsValorRetido    = $oDaoNota->sql_record($sSqlValorRetido);

    if ($oDaoNota->numrows > 0 and !$produtorRural ) {

      $nValorJaRetido = (float)db_utils::fieldsMemory($rsValorRetido, 0)->valorretido;
      $nValorRetido  -= $nValorJaRetido;

    }
    /**
     * caso o usuário marcou alguma outra retencao, e caso ela possuir retencao,
     * calculamos o valor ja retido desses calculos
     * Buscamos o valor já retido no mes e deduzimos do valor retido.
     */
    if ( isset($this->aCodigoMovimentos) && !empty($this->aCodigoMovimentos) &&  count($this->aCodigoMovimentos) > 0) {

      $sWhereIn         = implode(",",$this->aCodigoMovimentos);
      $sSqlValorRetido  = "select sum(e23_valorretencao) as valorRetido                               ";
      $sSqlValorRetido .= "  from retencaoreceitas                                                    ";
      $sSqlValorRetido .= "       inner join retencaopagordem  on e23_retencaopagordem = e20_sequencial";
      $sSqlValorRetido .= "       inner join retencaotiporec   on e23_retencaotiporec  = e21_sequencial";
      $sSqlValorRetido .= "       inner join pagordemnota      on e20_pagordem         = e71_codord    ";
      $sSqlValorRetido .= "       inner join empnota           on e71_codnota          = e69_codnota   ";
      $sSqlValorRetido .= "       inner join empempenho        on e69_numemp           = e60_numemp    ";
      $sSqlValorRetido .= "       inner join cgm               on e60_numcgm           = z01_numcgm    ";
      $sSqlValorRetido .= "       inner join retencaoempagemov on e23_sequencial       = e27_retencaoreceitas ";
      $sSqlValorRetido .= " where e69_anousu                        = {$iAnoBase}                     ";
      $sSqlValorRetido .= "   and extract(month from e23_dtcalculo) = {$iMesBase}                     ";
      $sSqlValorRetido .= "   and z01_cgccpf                        = '{$this->iCgcCpf}'              ";
      $sSqlValorRetido .= "   and e23_recolhido is false                                              ";
      $sSqlValorRetido .= "   and e23_ativo     is true                                               ";
      $sSqlValorRetido .= "   and e71_anulado  is false                                               ";
      $sSqlValorRetido .= "   and e27_principal is true                                               ";
      $sSqlValorRetido .= "   and e21_retencaotipocalc = {$this->iTipo}                               ";
      $sSqlValorRetido .= "   and e27_empagemov in({$sWhereIn})                                        ";
      $rsValorRetido    = $oDaoNota->sql_record($sSqlValorRetido);
      if ($oDaoNota->numrows > 0 and !$produtorRural) {

        $nValorJaRetido = db_utils::fieldsMemory($rsValorRetido, 0)->valorretido;
        $nValorRetido  -= $nValorJaRetido;

      }
    }
    if ($nValorRetido < 0) {
      $nValorRetido = 0;
    }

    return $nValorRetido;
  }

  /**
   *
   * @see iCalculoRetencao::getAliquota()
   */
  public function getAliquota() {
    return $this->nAliquota;
  }

  /**
   *
   * @see iCalculoRetencao::getValorBaseCalculo()
   */
  public function getValorBaseCalculo() {
    return $this->nValorBaseCalculo;
  }

  /**
   *
   * @param float $nValorAliquota
   * @see iCalculoRetencao::setAliquota()
   */
  public function setAliquota($nValorAliquota) {
    $this->nValorAliquota = $nValorAliquota;
  }

  /**
   *
   * @param $nValorDeducao
   * @see iCalculoRetencao::setDeducao()
   */
  public function setDeducao($nValorDeducao) {
    $this->nValorDeducao = $nValorDeducao;
  }

  /** Retorna a tabela de Inss conforme base de calculo
   * @return boolean;
   */
  public function getTabelaInss() {

    /*
     * Buscamos o codigo da tabela a ser Deduzida
     * devemos somar +2 no codigo da tabela encontrada,
     * sempre pegando o ultimo registro
     *
     */
     $oDaoCfPess    = new cl_cfpess;
     $sSqlCodTabela = $oDaoCfPess->sql_query(null,
                                             null,
                                             null,
                                             "r11_tbprev",
                                             "r11_anousu desc,
                                             r11_mesusu desc
                                             limit 1",
                                             "r11_instit = ".db_getsession("DB_instit")
                                             );
    $rsCodTabela  = $oDaoCfPess->sql_record($sSqlCodTabela);
    if ($oDaoCfPess->numrows == 0) {

      throw new Exception("Não há nenhuma configuracao de tabela de Inss!\nConfira.");
    }

    $iCodigoTabelaInss = (db_utils::fieldsMemory($rsCodTabela,0)->r11_tbprev+2);
    /*
     * Retorna a tabela do imposto de renda que devemos usar.
     */
    $nBaseCalculo = $this->nValorBaseCalculo;
    $nValorTeto   = $this->getTetoInss($iCodigoTabelaInss);
    
    if ($nBaseCalculo > $nValorTeto) {
      $nBaseCalculo =  $nValorTeto;
    }
    
    $oDaoInssIrf     = new cl_inssirf;
    $sSqlTabelaINSS  = "select r33_inic,                                               ";
    $sSqlTabelaINSS .= "       r33_fim,                                                ";
    $sSqlTabelaINSS .= "       r33_deduzi,                                             ";
    $sSqlTabelaINSS .= "       r33_perc,                                               ";
    $sSqlTabelaINSS .= "       {$nValorTeto} as teto                                   ";
    $sSqlTabelaINSS .= "  from inssirf                                                 ";
    $sSqlTabelaINSS .= " where {$nBaseCalculo} between r33_inic and r33_fim ";
    $sSqlTabelaINSS .= "   and r33_codtab = {$iCodigoTabelaInss}                       ";
    $sSqlTabelaINSS .= "   and r33_instit = ".db_getsession("DB_instit");
    $sSqlTabelaINSS .= " order by r33_anousu desc,                                     ";
    $sSqlTabelaINSS .= "          r33_mesusu desc  limit 1                             ";
    $rsTabelaInss    = $oDaoInssIrf->sql_record($sSqlTabelaINSS);
    if ($oDaoInssIrf->numrows == 1) {
      $this->oTabelaInss = db_utils::fieldsMemory($rsTabelaInss, 0);
    } else {

      $this->oTabelaInss = new stdClass();
      $this->oTabelaInss->r33_perc = 0;
      $this->oTabelaInss->teto     = 0;
    }
    return true;
  }

  /**
   * Seta o valor da base de calculo
   *
   * @param float8 $nValorBaseCalculo valor da base de calculo
   */
  public function setBaseCalculo($nValorBaseCalculo) {
    $this->nValorBaseCalculo = $nValorBaseCalculo;
  }

  /**
   * Define o valor da nota
   *
   * @param float $nValorNota valor da nota a ser contabilizado na retencao.
   */
  public function setValorNota($nValorNota) {

    $this->nValorNota = $nValorNota;
  }

  /**
   * Define a data base para calculo das retencoes;
   *
   * @param string $dtDataBase data base para caculo formato dd/mm/YYY
   */
  public function setDataBase($dtDataBase) {

    $dtDataBase = implode("-", array_reverse(explode("/", $dtDataBase)));
    $this->dtBaseCalculo = $dtDataBase;

  }
 /**
   * Define  o Codigo dos Movimentos
   *
   * @param unknown_type $aCodigosMovimentos
   */
  public function setCodigoMovimentos($aCodigosMovimentos) {
     $this->aCodigoMovimentos = $aCodigosMovimentos;
  }
  
  /**
   * Busca o valor limite do teto
   * @param integer $tabelaInss
   * @return numeric
   */
  private function getTetoInss($tabelaInss)
  {
      $sSqlTeto  = "select max(r33_fim) as teto                       ";
      $sSqlTeto .= "  from inssirf                                    ";
      $sSqlTeto .= "  where r33_codtab = {$tabelaInss}                ";
      $sSqlTeto .= "   and r33_instit = ".db_getsession("DB_instit");
      $sSqlTeto .= " group by r33_anousu, r33_mesusu                  ";
      $sSqlTeto .= " order by r33_anousu desc,                        ";
      $sSqlTeto .= "          r33_mesusu desc  limit 1                ";
      $rsTeto    = db_query($sSqlTeto);
      
      return db_utils::fieldsMemory($rsTeto,0)->teto;
  }
}
