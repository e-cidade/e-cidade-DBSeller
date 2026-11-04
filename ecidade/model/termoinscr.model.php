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


class termoinscr
{
  /**
   *
   */
    protected $iCodigo     = null;

    protected $aEnvolvidos = array();

    protected $aDebitos    = array();

    protected $dataEmissao = null;

    protected $iFolha      = 0;

    protected $iLivro      = 0;

    protected $iTipo       = 0;

    protected $dataLivro   = null;

    protected $massafalida = 0;

    protected $oDaoCertid  = null;

    protected $iAno        = null;

    protected $lComposicao = false;

    protected $oDataRecalculoJurosMulta = null;

  /**
   *
   */
    public function __construct($iCodigo)
    {

        if (!empty($iCodigo)) {
            $sSqlDadosCertidao  = "select termoinscr.v92_termo,";
            $sSqlDadosCertidao .= "       v92_dtinsc,";
            $sSqlDadosCertidao .= "       coalesce(certidmassa.v13_certid) as v13_certidmassa,";
            $sSqlDadosCertidao .= "       v26_numerofolha as folha,";
            $sSqlDadosCertidao .= "       v25_numero as livro,";
            $sSqlDadosCertidao .= "       v25_datainc as datalivro,";
            $sSqlDadosCertidao .= "       (case when termoinscrreg.v93_termo is not null then 2 end) as tipocertidao";
            $sSqlDadosCertidao .= "  from termoinscr";
            $sSqlDadosCertidao .= "       left outer join certidlivrofolha on v26_certid         = v92_termo";
            $sSqlDadosCertidao .= "       left outer join termoinscrreg          on termoinscr.v92_termo = termoinscrreg.v93_termo";
            $sSqlDadosCertidao .= "       left outer join certidlivro      on v26_certidlivro    = v25_sequencial ";
            $sSqlDadosCertidao .= "       left outer join certidmassa  on certidmassa.v13_certid = termoinscr.v92_termo ";
            $sSqlDadosCertidao .= "  where termoinscr.v92_termo = {$iCodigo} ";
            $sSqlDadosCertidao .= "  limit 1";

            $rsCertid           =    db_query($sSqlDadosCertidao);

            if (pg_numrows($rsCertid) > 0) {
                $oDadosCertid      = db_utils::fieldsMemory($rsCertid, 0);
                $this->iCodigo     = $oDadosCertid->v92_termo;
                $this->dataEmissao = $oDadosCertid->v92_dtinsc;
                $this->iFolha      = $oDadosCertid->folha;
                $this->iLivro      = $oDadosCertid->livro;
                $this->dataLivro   = $oDadosCertid->datalivro;
                $this->iTipo       = $oDadosCertid->tipocertidao;
                $this->massafalida = $oDadosCertid->v13_certidmassa;
                $this->iAno        = substr($oDadosCertid->v92_dtinsc, 0, 4);
            }
        }
    }

  /**
   * @return unknown
   */
    public function getDataEmissao()
    {

        return $this->dataEmissao;
    }

  /**
   * @param unknown_type $dataEmissao
   */
    public function setDataEmissao($dataEmissao)
    {

        $this->dataEmissao = $dataEmissao;
    }

  /**
   * @return unknown
   */
    public function getDataLivro()
    {

        return $this->dataLivro;
    }

  /**
   * @param unknown_type $dataLivro
   */
    public function setDataLivro($dataLivro)
    {

        $this->dataLivro = $dataLivro;
    }

  /**
   * @return unknown
   */
    public function getCodigo()
    {

        return $this->iCodigo;
    }

  /**
   * @return unknown
   */
    public function getFolha()
    {

        return $this->iFolha;
    }

  /**
   * @param unknown_type $iFolha
   */
    public function setFolha($iFolha)
    {

        $this->iFolha = $iFolha;
    }

    public function setDataRecalculoJurosMulta($oDataRecalculoJurosMulta)
    {
        $this->oDataRecalculoJurosMulta = $oDataRecalculoJurosMulta;
    }

  /**
   * @return unknown
   */
    public function getLivro()
    {

        return $this->iLivro;
    }

  /**
   * @param unknown_type $iLivro
   */
    public function setLivro($iLivro)
    {

        $this->iLivro = $iLivro;
    }

  /**
   * @return unknown
   */
    public function getTipo()
    {

        return $this->iTipo;
    }

  /**
   * @return unknown
   */
    public function getMassafalida()
    {

        return $this->massafalida;
    }

  /**
   * @param unknown_type $massafalida
   */
    public function setMassafalida($massafalida)
    {

        $this->massafalida = $massafalida;
    }


    public function getAno()
    {

        return $this->iAno;
    }


    public function getOrigensDebito()
    {

        $aOrigem = array();

        $aOrigem = $this->getOrigemDebitoDivida();

        if (count($aOrigem) == 0) {
            throw new Exception("Termo {$this->getCodigo()} sem Débitos.");
        }

        return $aOrigem;
    }


    protected function getOrigemDebitoDivida()
    {
        $sqlOrigemMatric  = "  select v01_numpre as numpre,                                                ";
        $sqlOrigemMatric .= "         v01_numpar as numpar,                                                ";
        $sqlOrigemMatric .= "         coalesce(arrematric.k00_matric,0) as matric,                         ";
        $sqlOrigemMatric .= "         coalesce(iptubaseregimovel.j04_matricregimo,'') as matric_ri,         ";
        $sqlOrigemMatric .= "         coalesce(arreinscr.k00_inscr,0) as inscr,                            ";
        $sqlOrigemMatric .= "               k00_numcgm as numcgm                                           ";
        $sqlOrigemMatric .= "    from termoinscrreg                                                              ";
        $sqlOrigemMatric .= "         inner join divida on v93_coddiv = v01_coddiv                         ";
        $sqlOrigemMatric .= "                          and v01_instit = ".db_getsession('DB_instit')."     ";
        $sqlOrigemMatric .= "         left join arrematric  on arrematric.k00_numpre = divida.v01_numpre   ";
        $sqlOrigemMatric .= "         left join iptubaseregimovel on arrematric.k00_matric = iptubaseregimovel.j04_matric ";
        $sqlOrigemMatric .= "         left join arreinscr   on arreinscr.k00_numpre  =  divida.v01_numpre  ";
        $sqlOrigemMatric .= "         left join arrenumcgm  on arrenumcgm.k00_numpre  =  divida.v01_numpre ";
        $sqlOrigemMatric .= "   where v93_termo = {$this->iCodigo}                                        ";
        $sqlOrigemMatric .= "   order by v01_numpre,v01_numpar                                             ";

        $rsOrigemDebitos  = db_query($sqlOrigemMatric);
        $aOrigem          = array();
        $aOrigem          = db_utils::getCollectionByRecord($rsOrigemDebitos);
        return  $aOrigem;
    }

    function getDevedoresEnvolvidos($sTipoEndereco = 'o')
    {

        $aParams  = db_stdClass::getParametro("pardiv", array(db_getsession("DB_instit")));

        if (count($aParams) == 0) {
            throw new Exception("Sem parametros para o módulo dívida configurados");
        }

        $oPardiv = $aParams[0];

        $sExpressaoFalecimento = "";
        if ($oPardiv->v04_confexpfalec != 2) {
            $sExpressaoFalecimento = $oPardiv->v04_expfalecimentocda;
        }

        if ($oPardiv->v04_envolprinciptu == "f") {
            $lRegra = "false";
        } else {
            $lRegra = "true";
        }

        $aMatric              = array();
        $aInscr               = array();
        $aCgm                 = array();
        $aImoveisEnvolvidos   = array();
        $aEmpresasEnvolvidos  = array();
        $aDevedoresEnvolvidos = array();
        $aOrigens = $this->getOrigensDebito();

        foreach ($aOrigens as $oOrigens) {
            if ($oOrigens->matric > 0 && in_array($oOrigens->matric, $aMatric)) {
                continue;
            } else {
                if ($oOrigens->matric > 0) {

                  /**
                   * Procuramos o texto para o possuidor da matricula
                   */
                    $sqlPossuidor    = " select j18_textoprom                           ";
                    $sqlPossuidor   .= "   from cfiptu                                  ";
                    $sqlPossuidor   .= "  where j18_anousu= ".db_getsession("DB_anousu") ;
                    $resultPossuidor = db_query($sqlPossuidor);
                    $linhasPossuidor = pg_num_rows($resultPossuidor);
                    $possuidor = "POSSUIDOR";
                    if ($linhasPossuidor > 0) {
                        $oTextoPossuido = db_utils::fieldsmemory($resultPossuidor, 0);
                        if (trim($oTextoPossuido->j18_textoprom) != "") {
                            $possuidor = $oTextoPossuido->j18_textoprom;
                        }
                    }

                /**
                 * Buscamos as matriculas da divida
                 */
                    $sSqlEnvol    = "
                      select *
                      from fc_busca_envolvidos({$lRegra},{$oPardiv->v04_envolcdaiptu},'M',{$oOrigens->matric})";
                    $rsEnvol      = db_query($sSqlEnvol) or die($sSqlEnvol);
                    $iLinhasEnvol = pg_num_rows($rsEnvol);
                    if ($oPardiv->v04_envolcdaiptu == 2 && $iLinhasEnvol == 0) {
                        $sSqlEnvol  = " select j01_numcgm as rinumcgm,   ";
                        $sSqlEnvol .= "        1          as ritipoenvol ";
                        $sSqlEnvol .= "   from iptubase                  ";
                        $sSqlEnvol .= "  where j01_matric = {$oOrigens->matric}    ";
                        $rsEnvol      = db_query($sSqlEnvol) or die($sSqlEnvol);
                        $iLinhasEnvol = pg_num_rows($rsEnvol);
                    }

                    for ($i = 0; $i < $iLinhasEnvol; $i++) {
                        $oDevedor = new stdClass();
                        $oEnvol   = db_utils::fieldsMemory($rsEnvol, $i);

                        $sSqlDadosEnvol  = " select z01_numcgm,                     ";
                        $sSqlDadosEnvol .= "        z01_nome,                       ";
                        $sSqlDadosEnvol .= "        z01_cgccpf,                     ";
                        $sSqlDadosEnvol .= "        z01_telef,                      ";
                        $sSqlDadosEnvol .= "        z01_telcel,                     ";
                        $sSqlDadosEnvol .= "        z01_ender,                      ";
                        $sSqlDadosEnvol .= "        z01_numero,                     ";
                        $sSqlDadosEnvol .= "        z01_compl,                      ";
                        $sSqlDadosEnvol .= "        z01_bairro,                     ";
                        $sSqlDadosEnvol .= "        z01_munic,                      ";
                        $sSqlDadosEnvol .= "        z01_cep,                        ";
                        $sSqlDadosEnvol .= "        z01_uf,                         ";
                        $sSqlDadosEnvol .= "        z01_dtfalecimento               ";
                        $sSqlDadosEnvol .= "   from cgm                             ";
                        $sSqlDadosEnvol .= "  where z01_numcgm = {$oEnvol->rinumcgm}";
                        $rsDadosEnvol      = db_query($sSqlDadosEnvol) or die($sSqlDadosEnvol);
                        $iLinhasDadosEnvol = pg_num_rows($rsDadosEnvol);
                        if ($iLinhasDadosEnvol > 0) {
                            $oDadosEnvol = db_utils::fieldsMemory($rsDadosEnvol, 0);
                            if (trim($oDadosEnvol->z01_dtfalecimento) != '' && strlen($oDadosEnvol->z01_cgccpf) == 11
                            && $oDadosEnvol != '00000000000') {
                                  $oDevedor->nome = $sExpressaoFalecimento." ".$oDadosEnvol->z01_nome;
                            } else {
                                $oDevedor->nome = $oDadosEnvol->z01_nome;
                            }
                            $oDevedor->numcgm   = $oDadosEnvol->z01_numcgm;
                            $oDevedor->telefone = $oDadosEnvol->z01_telef;
                            $oDevedor->celular  = $oDadosEnvol->z01_telcel;
                            $oDevedor->endereco = "";
                            $oDevedor->endereco = $oDadosEnvol->z01_ender != "" ? $oDadosEnvol->z01_ender : "";
                            if (trim($oDadosEnvol->z01_numero) !="0" and trim($oDadosEnvol->z01_numero)!="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->z01_numero} ";
                            }
                            if (trim($oDadosEnvol->z01_compl)  !="0" and trim($oDadosEnvol->z01_compl) !="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->z01_compl} ";
                            }
                            if (trim($oDadosEnvol->z01_bairro) !="0" and trim($oDadosEnvol->z01_bairro)!="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->z01_bairro} ";
                            }
                            if (trim($oDadosEnvol->z01_munic)  !="0" and trim($oDadosEnvol->z01_munic) !="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->z01_munic}/{$oDadosEnvol->z01_uf} ";
                            }
                            if (trim($oDadosEnvol->z01_cep) !="0" and trim($oDadosEnvol->z01_cep) !="") {
                                $oDevedor->endereco .= "- CEP {$oDadosEnvol->z01_cep}";
                            }

                        /**
                         * Verifica o tipo do Devedor
                         */
                            if ($oEnvol->ritipoenvol == "1" || $oEnvol->ritipoenvol == "2") {
                                $oDevedor->tipo = "PROPRIETÁRIO";
                            } else {
                                $oDevedor->tipo = $possuidor;
                            }

                            if (strlen($oDadosEnvol->z01_cgccpf) == 14) {
                                $oDevedor->cgcCpf = db_formatar($oDadosEnvol->z01_cgccpf, "cnpj");
                            } else {
                                $oDevedor->cgcCpf = db_formatar($oDadosEnvol->z01_cgccpf, "cpf");
                            }
                            $aDevedoresEnvolvidos[] = $oDevedor;
                        }
                    }

                /**
                 * Retornamos os dados do imovel
                 */
                    $sSqlProprietario  = " select *                    ";
                    $sSqlProprietario .= " from proprietario         ";
                    $sSqlProprietario .= " left join ruascep on proprietario.codpri = ruascep.j29_codigo";
                    $sSqlProprietario .= " where proprietario.j01_matric = $oOrigens->matric ";
                    $rsProprietario    = db_query($sSqlProprietario) or die($sSqlProprietario);
                    $oProprietario     = db_utils::fieldsMemory($rsProprietario, 0);
                    $oImovel           = new stdClass();

                    $sqlcidade = "select munic, uf, cep from db_config where codigo = ".db_getsession('DB_instit');
                    $resultcidade     = db_query($sqlcidade);
                    $oCidade          = db_utils::fieldsmemory($resultcidade, 0);
                    $oImovel->cep    = $oProprietario->j29_cep ? $oProprietario->j29_cep : $oCidade->cep;

                    /**
                     * quando solicitado  o endereço de origem
                     */

                    if ($sTipoEndereco == "o") {
                        $oImovel->endereco = $oProprietario->nomepri
                            . (isset($oProprietario->j39_numero) && $oProprietario->j39_numero != "" ?
                                ", " . $oProprietario->j39_numero : ""
                            )
                            . (isset($oProprietario->j39_compl) && $oProprietario->j39_compl != "" ?
                                ", " . $oProprietario->j39_compl : "");
                        $oImovel->bairro  = $oProprietario->j13_descr;
                        $oImovel->cidade = $oCidade->munic.' / '.$oCidade->uf;

                    } elseif ($sTipoEndereco == "c") {
                        $oImovel->endereco = $oProprietario->z01_ender.
                                 ($oProprietario->z01_numero != ""?', ' . $oProprietario->z01_numero:"").
                                 ($oProprietario->z01_compl != ""?"/" . $oProprietario->z01_compl:"");
                        $oImovel->bairro   = $oProprietario->z01_bairro;
                        $oImovel->cidade   = $oProprietario->z01_munic.' / '.$oProprietario->z01_uf;
                    }

                    $oImovel->setor       = $oProprietario->j34_setor;
                    $oImovel->quadra      = $oProprietario->j34_quadra;
                    $oImovel->lote        = $oProprietario->j34_lote;
                    $oImovel->matricula   = $oOrigens->matric;
                    $oImovel->matricula_ri  = $oOrigens->matric_ri;

                    $oImovel->refanterior = $oProprietario->j40_refant;

                    $oImovel->setorloc      = $oProprietario->pql_localizacao;

                    $aImoveisEnvolvidos[] = $oImovel;
                    $aMatric[]            = $oOrigens->matric;
                }
            }

          /**
           * Verificando as inscrições
           */
            if ($oOrigens->inscr > 0 && in_array($oOrigens->inscr, $aInscr)) {
                continue;
            } else {
                if ($oOrigens->inscr > 0) {
                    $sSqlEnvol = "
                        select *
                        from fc_busca_envolvidos({$lRegra},{$oPardiv->v04_envolcdaiss},'I',{$oOrigens->inscr})";
                    $rsEnvol      = db_query($sSqlEnvol) or die($sSqlEnvol);
                    $iLinhasEnvol = pg_num_rows($rsEnvol);
                    for ($i = 0; $i < $iLinhasEnvol; $i++) {
                        $oDevedor = new stdClass();
                        $oEnvol = db_utils::fieldsMemory($rsEnvol, $i);
                        if (empty($oEnvol->rinumcgm)) {
                            continue;
                        }
                        $sSqlDadosEnvol  = " select z01_numcgm,                     ";
                        $sSqlDadosEnvol .= "        z01_nome,                       ";
                        $sSqlDadosEnvol .= "        z01_cgccpf,                     ";
                        $sSqlDadosEnvol .= "        z01_telef,                      ";
                        $sSqlDadosEnvol .= "        z01_telcel,                     ";
                        $sSqlDadosEnvol .= "        z01_numero,                     ";
                        $sSqlDadosEnvol .= "        z01_ender  as ender,            ";
                        $sSqlDadosEnvol .= "        z01_numero as numero,           ";
                        $sSqlDadosEnvol .= "        z01_compl  as compl,            ";
                        $sSqlDadosEnvol .= "        z01_bairro as bairro,           ";
                        $sSqlDadosEnvol .= "        z01_munic  as munic,            ";
                        $sSqlDadosEnvol .= "        z01_cep    as cep,              ";
                        $sSqlDadosEnvol .= "        z01_uf     as uf,               ";
                        $sSqlDadosEnvol .= "        z01_dtfalecimento               ";
                        $sSqlDadosEnvol .= "   from cgm                             ";
                        $sSqlDadosEnvol .= "  where z01_numcgm = {$oEnvol->rinumcgm}";
                        $rsDadosEnvol      = db_query($sSqlDadosEnvol);
                        $iLinhasDadosEnvol = pg_num_rows($rsDadosEnvol);
                        if ($iLinhasDadosEnvol > 0) {
                            $oDadosEnvol = db_utils::fieldsMemory($rsDadosEnvol, 0);

                            if (trim($oDadosEnvol->z01_dtfalecimento) != '' && strlen($oDadosEnvol->z01_cgccpf) == 11) {
                                $oDevedor->nome = $sExpressaoFalecimento." ".$oDadosEnvol->z01_nome;
                            } else {
                                $oDevedor->nome = $oDadosEnvol->z01_nome;
                            }

                            $oDevedor->numcgm   = $oDadosEnvol->z01_numcgm;
                            $oDevedor->telefone = $oDadosEnvol->z01_telef;
                            $oDevedor->celular  = $oDadosEnvol->z01_telcel;
                            $oDevedor->endereco = "";
                            $oDevedor->endereco = $oDadosEnvol->ender;
                            if (trim($oDadosEnvol->numero) !="0" and trim($oDadosEnvol->numero)!="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->numero} ";
                            }
                            if (trim($oDadosEnvol->compl)  !="0" and trim($oDadosEnvol->compl) !="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->compl} ";
                            }
                            if (trim($oDadosEnvol->bairro) !="0" and trim($oDadosEnvol->bairro)!="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->bairro} ";
                            }
                            if (trim($oDadosEnvol->munic)  !="0" and trim($oDadosEnvol->munic) !="") {
                                $oDevedor->endereco .= ",{$oDadosEnvol->munic}/{$oDadosEnvol->uf}";
                            }
                            if (trim($oDadosEnvol->cep)    !="0" and trim($oDadosEnvol->cep)   !="") {
                                $oDevedor->endereco .= "- CEP {$oDadosEnvol->cep} .";
                            }

                            if (strlen($oDadosEnvol->z01_cgccpf) > 11) {
                                if ($oEnvol->ritipoenvol == "4") {
                                    $oDevedor->tipo = "EMPRESA";
                                } elseif ($oEnvol->ritipoenvol == "5") {
                                    $oDevedor->tipo = "SÓCIO";
                                }
                            } else {
                                $oDevedor->tipo = "CONTRIBUINTE";
                            }

                            if (strlen($oDadosEnvol->z01_cgccpf) > 11) {
                                $oDevedor->cgcCpf = db_formatar($oDadosEnvol->z01_cgccpf, "cnpj");
                            } else {
                                $oDevedor->cgcCpf = db_formatar($oDadosEnvol->z01_cgccpf, "cpf");
                            }
                        }
                        $aDevedoresEnvolvidos[] = $oDevedor;
                    }

                  /**
                   * Retorna os dados da Inscrição
                   */

                    $sql = "select
                                issruas.q02_inscr as inscricao,
                                case when ruastipo.j88_sigla != '' then ruastipo.j88_sigla || ' ' end ||
                                ruas.j14_nome || ', ' ||
                                issruas.q02_numero || ' ' ||
                                case when issruas.q02_compl != '' then issruas.q02_compl else '' end as ref_ao_alvara,
                                bairro.j13_descr bairro,
                                issruas.z01_cep cep
                            from issruas
                            inner join ruas on ruas.j14_codigo = issruas.j14_codigo
                            left join ruastipo on ruas.j14_tipo = ruastipo.j88_codigo
                            inner join issbairro on issruas.q02_inscr = issbairro.q13_inscr
                            inner join bairro on issbairro.q13_bairro = bairro.j13_codi
                            where issruas.q02_inscr = $oOrigens->inscr";

                    $rsSql = db_query($sql) or die($sql);
                    $dadosInscricao = db_utils::fieldsMemory($rsSql, 0);


                    $sql = "select
                                munic || ' / ' || uf as municipio_uf
                            from db_config
                            where codigo = " . db_getsession("DB_instit");

                    $rsSql = db_query($sql) or die($sql);
                    $dadosInstituicao = db_utils::fieldsMemory($rsSql, 0);

                    $oEmpresa = new \stdClass();
                    $oEmpresa->inscricao = $dadosInscricao->inscricao;
                    $oEmpresa->endereco  = $dadosInscricao->ref_ao_alvara;
                    $oEmpresa->bairro    = $dadosInscricao->bairro;
                    $oEmpresa->cidade    = $dadosInstituicao->municipio_uf;
                    $oEmpresa->cep       = $dadosInscricao->cep;
                    $aInscr[]            = $oOrigens->inscr;
                    $aEmpresasEnvolvidos[] = $oEmpresa;
                }
            }

          /**
           * Verificamos o CGM
           */
            if (in_array($oOrigens->numcgm, $aCgm)) {
                continue;
            } else {
                if ($oOrigens->matric == 0  && $oOrigens->inscr == 0 && !empty($oOrigens->numcgm)) {
                    $sSqlCgm  = " select *                             ";
                    $sSqlCgm .= "   from cgm                           ";
                    $sSqlCgm .= "  where z01_numcgm = $oOrigens->numcgm";
                    $rsCgm = db_query($sSqlCgm) or die($sSqlCgm);
                    $oCgm  = db_utils::fieldsMemory($rsCgm, 0);
                    $oDevedor = new stdClass();
                    $oDevedor->endereco = $oCgm->z01_ender;

                    if (trim($oCgm->z01_numero)!="0" and trim($oCgm->z01_numero)!="") {
                        $oDevedor->endereco .= ",{$oCgm->z01_numero} ";
                    }
                    if (trim($oCgm->z01_compl)!="0" and trim($oCgm->z01_compl)!="") {
                        $oDevedor->endereco .= ",{$oCgm->z01_compl} ";
                    }
                    if (trim($oCgm->z01_bairro)!="0" and  trim($oCgm->z01_bairro)!="") {
                        $oDevedor->endereco .= ",{$oCgm->z01_bairro} ";
                    }
                    if (trim($oCgm->z01_munic) !="0" and trim($oCgm->z01_munic)!="") {
                        $oDevedor->endereco .= ",{$oCgm->z01_munic}/{$oCgm->z01_uf} ";
                    }

                    if (trim($oCgm->z01_cep) !="0" and trim($oCgm->z01_cep)!="") {
                        $oDevedor->endereco .= "- CEP {$oCgm->z01_cep} .";
                    }

                    $oDevedor->numcgm   = $oCgm->z01_numcgm;
                    $oDevedor->telefone = $oCgm->z01_telef;
                    $oDevedor->celular = $oCgm->z01_telcel;
                    $oDevedor->nome     = $oCgm->z01_nome;
                    if (strlen($oCgm->z01_cgccpf) > 11) {
                        $oDevedor->cgcCpf = db_formatar($oCgm->z01_cgccpf, 'cnpj');
                    } else {
                        $oDevedor->cgcCpf = db_formatar($oCgm->z01_cgccpf, 'cpf');
                    }
                    $oDevedor->tipo = "";
                    $aCgm[]    = $oOrigens->numcgm;
                    $aDevedoresEnvolvidos[] =  $oDevedor;
                }
            }
        }
        $oRetorno = new stdClass();
        $oRetorno->aDevedores = array_map("unserialize", array_unique(array_map("serialize", $aDevedoresEnvolvidos)));
        $oRetorno->aImoveis   = $aImoveisEnvolvidos;
        $oRetorno->aEmpresas  = $aEmpresasEnvolvidos;
        return $oRetorno;
    }

    function getDebitos($lRemissao)
    {

        $aDebitos = array();
        $aDebitos = $this->getDebitosDivida($lRemissao);


      /**
       * Verificamos se existem procedencias que devemos agrupar, e
       * agrupamos com a seguinte lógica :
       *   - Criamos um hash com os campos exercício/parcela/origem/procedência (v24_procedagrupa)
       *   - Comparar exercício/parcela/origem/procedência
       *   - é somado todos os valores e e os outros campos (exercício/livro e folha/data inscrição/data vencimento)
       *     é utilizado sempre o do registro da procedência principal que está agrupando
       */

      /**
       * Array com todos os debitos agrupados
       */
        $aDebitosAgrupado    = array();

      /**
       * Debitos que sao agrupadores debitos sem procedenciaagrupa
       */
        $aDebitosAgrupadores = array();

      /**
       * Debitos com procedenciaagrupa
       */
        $aDebitosParaAgrupar = array();

      /**
       * Verificamos quais debitos estao configurados para agrupar
       */
        $i     = 0;
        $sHash = "";

        foreach ($aDebitos as $oOrigem) {
            if ($oOrigem->procedenciaagrupar != "") {
                $sHash = $oOrigem->exercicio.$oOrigem->numpar.$oOrigem->procedenciaagrupar;
                if (isset($aDebitosParaAgrupar[$sHash])) {
                    $aDebitosParaAgrupar[$sHash]->valorhistorico += $oOrigem->valorhistorico;
                    $aDebitosParaAgrupar[$sHash]->valorcorrigido += $oOrigem->valorcorrigido;
                    $aDebitosParaAgrupar[$sHash]->valorcorrecao  += $oOrigem->valorcorrigido - $oOrigem->valorhistorico;
                    $aDebitosParaAgrupar[$sHash]->valormulta     += $oOrigem->valormulta;
                    $aDebitosParaAgrupar[$sHash]->valorjuros     += $oOrigem->valorjuros;
                    $aDebitosParaAgrupar[$sHash]->valortotal     += $oOrigem->valortotal;
                } else {
                    $aDebitosParaAgrupar[$sHash] = $oOrigem;
                    $aDebitosParaAgrupar[$sHash]->hash = $sHash;
                }
            } else {
                $sHash = $oOrigem->exercicio.$oOrigem->numpar.$oOrigem->codigoprocedencia;
                $aDebitosAgrupadores[$i] = $oOrigem;
                $aDebitosAgrupadores[$i]->hash = $sHash;
                $i++;
            }
        }

        foreach ($aDebitosParaAgrupar as $sHash => $oDebitoAgrupar) {
            $iTotalDebitosAgrupadores = count($aDebitosAgrupadores);

            $lFound = '';
            for ($i=0; $i < $iTotalDebitosAgrupadores; $i ++) {
                if ($aDebitosAgrupadores[$i]->hash == $sHash) {
                    $aDebitosAgrupadores[$i]->valorhistorico += $oDebitoAgrupar->valorhistorico;
                    $aDebitosAgrupadores[$i]->valorcorrigido += $oDebitoAgrupar->valorcorrigido;
                    $aDebitosAgrupadores[$i]->valorcorrecao += $oDebitoAgrupar->valorcorrigido
                        - $oDebitoAgrupar->valorhistorico;
                    $aDebitosAgrupadores[$i]->valormulta     += $oDebitoAgrupar->valormulta;
                    $aDebitosAgrupadores[$i]->valorjuros     += $oDebitoAgrupar->valorjuros;
                    $aDebitosAgrupadores[$i]->valortotal     += $oDebitoAgrupar->valortotal;
                    $lFound = true;
                    break;
                } else {
                    $lFound = false;
                }
            }

            if (!$lFound) {
                $aDebitosAgrupadores[] = $oDebitoAgrupar;
            }
        }

        ksort($aDebitosAgrupadores);

      /**
       * Percorremos os outros debitos e fizemso os agrupamentos
       */
        unset($aDebitosParaAgrupar);
        return $aDebitosAgrupadores;
    }

    protected function getDebitosDivida($lRemissao)
    {
        $sqlDadosDivida  = "select v01_coddiv, ";
        $sqlDadosDivida .= "       v01_numpre, ";
        $sqlDadosDivida .= "       v01_numpar, ";
        $sqlDadosDivida .= "       v01_exerc,  ";
        $sqlDadosDivida .= "       v01_livro,  ";
        $sqlDadosDivida .= "       v01_coddiv, ";
        $sqlDadosDivida .= "       v01_folha,  ";
        $sqlDadosDivida .= "       case when v01_processo = '' then p58_codproc::varchar";
        $sqlDadosDivida .= "       else v01_processo end as v01_processo,";
        $sqlDadosDivida .= "       case when v01_dtprocesso is null then p58_dtproc ";
        $sqlDadosDivida .= "       else v01_dtprocesso end as v01_dtprocesso, ";
        $sqlDadosDivida .= "       v01_obs, ";
        $sqlDadosDivida .= "       v01_numcgm, ";
        $sqlDadosDivida .= "       v01_proced, ";
        $sqlDadosDivida .= "       v01_dtinsc, ";
        $sqlDadosDivida .= "       lote.*,    ";
        $sqlDadosDivida .= "       coalesce(certidmassa.v13_certid) as v13_certidmassa, ";
        $sqlDadosDivida .= "       coalesce(arrematric.k00_matric,0) as matric, ";
        $sqlDadosDivida .= "       coalesce(arreinscr.k00_inscr,0) as inscr, ";
        $sqlDadosDivida .= "       v03_descr, ";
        $sqlDadosDivida .= "       v92_dtinsc, ";
        $sqlDadosDivida .= "       v24_procedagrupa, ";
        $sqlDadosDivida .= "       v03_tributaria ";
        $sqlDadosDivida .= "  from termoinscrreg  ";
        $sqlDadosDivida .= "       inner join divida           on v93_coddiv             = v01_coddiv ";
        $sqlDadosDivida .= "                                  and v01_instit             = ".db_getsession('DB_instit');
        $sqlDadosDivida .= " left join termoinscr            on termoinscr.v92_termo      = termoinscrreg.v93_termo ";
        $sqlDadosDivida .= "                            and termoinscr.v92_instit      = ".db_getsession('DB_instit');
        $sqlDadosDivida .= " left join certidmassa       on certidmassa.v13_certid = termoinscr.v92_termo ";
        $sqlDadosDivida .= " left join arrematric        on arrematric.k00_numpre  = divida.v01_numpre ";
        $sqlDadosDivida .= " left join iptubase a        on arrematric.k00_matric  = a.j01_matric ";
        $sqlDadosDivida .= " left join lote              on lote.j34_idbql         = a.j01_idbql ";
        $sqlDadosDivida .= " left join arreinscr         on arreinscr.k00_numpre   =  divida.v01_numpre ";
        $sqlDadosDivida .= " left join proced            on proced.v03_codigo      = divida.v01_proced ";
        $sqlDadosDivida .= "                            and proced.v03_instit      = ".db_getsession('DB_instit');
        $sqlDadosDivida .= " left join procedenciaagrupa on   v03_codigo           = v24_proced ";
        $sqlDadosDivida .= " left join dividaprotprocesso on dividaprotprocesso.v88_divida = divida.v01_coddiv ";
        $sqlDadosDivida .= " left join protprocesso on protprocesso.p58_codproc = dividaprotprocesso.v88_protprocesso ";
        $sqlDadosDivida .= " where v93_termo = {$this->getCodigo()}";
        $sqlDadosDivida .= " order by v03_tributaria,v01_exerc, v01_proced,v01_numpre,v01_numpar,v24_procedagrupa ";

        $rsDadosDivida   = db_query($sqlDadosDivida);
        $aDebitos        = array();

        if (pg_numrows($rsDadosDivida) > 0) {
            $oInstituicao      = new Instituicao(db_getsession('DB_instit'));
            $oParametrosDivida = db_stdClass::getParametro("pardiv", array($oInstituicao->getSequencial()));

            for ($i = 0; $i < pg_numrows($rsDadosDivida); $i++) {
                $oDivida = db_utils::fieldsmemory($rsDadosDivida, $i);
            }
                $sqlDebitosOriginais = "select
                                            v93_coddiv,
                                            v01_dtinsc,
                                            v01_dtvenc,
                                            v01_dtoper,
                                            v01_numpre,
                                            v01_numpar,
                                            v93_vlrhis,
                                            v93_vlrcor,
                                            v93_vlrmul,
                                            v93_vlrjur,
                                            v03_receit
                                        from
                                            termoinscrreg
                                        inner join divida on
                                            v01_coddiv = v93_coddiv
                                        inner join proced on
                                            v03_codigo = v01_proced
                                        where
                                        v93_termo = {$this->getCodigo()} order by v01_numpre, v01_numpar";

                $rsDebitos = db_query($sqlDebitosOriginais);

                if ($rsDebitos) {
                    $iNumRowsArrecad = pg_num_rows($rsDebitos);
               } else {
                           $iNumRowsArrecad = 0;
               }

           /**
            * percorremos os debitos da arrecad
            */
                for ($iArrecad = 0; $iArrecad < $iNumRowsArrecad; $iArrecad++) {
                          $oDadosDebitoOrigem = db_utils::fieldsmemory($rsDebitos, $iArrecad);
                          $oDividaTermo = new stdClass();
                          $oDividaTermo->exercicio = $oDivida->v01_exerc;
                          $oDividaTermo->livro = $oDivida->v01_livro;
                          $oDividaTermo->codigodivida = $oDivida->v01_coddiv;
                          $oDividaTermo->folha = $oDivida->v01_folha;
                          $oDividaTermo->certidmassa = $oDivida->v13_certidmassa;
                          $oDividaTermo->observacao = $oDivida->v01_obs . "\nProcesso :" . $oDivida->v01_processo
                            . " Data Processo:" . db_formatar($oDivida->v01_dtprocesso, 'd');
                          $oDividaTermo->procedenciaagrupar = $oDivida->v24_procedagrupa;

                    if ($oDivida->v03_tributaria == "t" || $oDivida->v03_tributaria == 1) {
                        $oDividaTermo->procedenciatributaria = true;
                    } else {
                        $oDividaTermo->procedenciatributaria = false;
                    }

                    if ($oDivida->matric != 0) {
                        $oDividaTermo->origem       = "mat";
                        $oDividaTermo->codigoorigem = $oDivida->matric;

                        if (isset($oDivida->j34_setor) && $oDivida->j34_setor != "" && isset($oDivida->j34_quadra)
                        && $oDivida->j34_quadra != "" && isset($oDivida->j34_lote) && $oDivida->j34_lote != "") {
                            $oDividaTermo->origemdebito = $oDivida->j34_setor . "/" . $oDivida->j34_quadra . "/"
                                . $oDivida->j34_lote;
                        } else {
                            $oDividaTermo->origemdebito = $oDivida->j34_lote;
                        }
                    } elseif ($oDivida->inscr != 0) {
                        $oDividaTermo->origem       = "inscr";
                        $oDividaTermo->codigoorigem = $oDivida->inscr;
                        $oDividaTermo->origemdebito = ucfirst($oDividaTermo->origem)." - ".$oDivida->inscr;
                    } else {
                        $oDividaTermo->origem       = "cgm";
                        $oDividaTermo->codigoorigem = $oDivida->v01_numcgm;
                        $oDividaTermo->origemdebito = ucfirst($oDividaTermo->origem)." - ".$oDivida->v01_numcgm;
                    }

                    $oDividaTermo->procedencia       = $oDivida->v03_descr;
                    $oDividaTermo->codigoprocedencia = $oDivida->v01_proced;

                    $dDataLancamento = $this->getDataLancamentoDebito(
                        $oDadosDebitoOrigem->v01_numpre,
                        $oDadosDebitoOrigem->v01_numpar
                    );
                    if ($dDataLancamento == '') {
                        $dDataLancamento = $oDadosDebitoOrigem->v01_dtoper;
                    }

                    $codDiv = $oDadosDebitoOrigem->v93_coddiv;

                    if ($lRemissao == true) {

                        $receita = $oDadosDebitoOrigem->v03_receit;
                        $dataVencimento = $oDadosDebitoOrigem->v01_dtvenc;
                        $data = new \DateTime(date('Y-m-d', db_getsession("DB_datausu")));
                        $dataProc = $data->format('Y-m-d');
                        $ano = $data->format("Y");

                        $sqlDebitosAtualizados = "select
                                                    fc_corre($receita, '$dataVencimento', $oDadosDebitoOrigem->v93_vlrhis, '$dataProc', '$ano', '$dataVencimento') as valor_corrigido,
                                                    fc_multa($receita, '$dataVencimento', '$dataProc', '$dDataLancamento', '$ano') as multa,
                                                    fc_juros($receita, '$dataVencimento', '$dataProc', '$dDataLancamento', false, '$ano') as juros
                                                from
                                                    divida
                                                inner join termoinscrreg on
                                                    v01_coddiv = v93_coddiv
                                                where
                                                    v93_coddiv = $codDiv";

                        $rsDebitosAtualizados = db_query($sqlDebitosAtualizados);

                        if ($rsDebitosAtualizados) {
                            $iNumRowsTermo = pg_num_rows($rsDebitosAtualizados);
                    } else {
                                $iNumRowsTermo = 0;
                    }

                    for ($iTermo = 0; $iTermo< $iNumRowsTermo; $iTermo++) {
                        $oDadosDebitoAtualizado = db_utils::fieldsmemory($rsDebitosAtualizados, $iTermo);

                            $valorMulta = $oDadosDebitoAtualizado->valor_corrigido * $oDadosDebitoAtualizado->multa;
                            $valorJuros = $oDadosDebitoAtualizado->valor_corrigido * $oDadosDebitoAtualizado->juros;

                            $oDividaTermo->datalancamento    = $dDataLancamento;
                            $oDividaTermo->codigodivida      = $codDiv;
                            $oDividaTermo->datainscricao     = $oDadosDebitoOrigem->v01_dtinsc;
                            $oDividaTermo->datavencimento    = $oDadosDebitoOrigem->v01_dtvenc;
                            $oDividaTermo->dataoperacao      = $oDadosDebitoOrigem->v01_dtoper;
                            $oDividaTermo->numpre            = $oDadosDebitoOrigem->v01_numpre;
                            $oDividaTermo->numpar            = $oDadosDebitoOrigem->v01_numpar;
                            $oDividaTermo->valorcorrecao     = $oDadosDebitoAtualizado->valor_corrigido - $oDadosDebitoOrigem->v93_vlrhis;
                            $oDividaTermo->valorhistorico    = $oDadosDebitoOrigem->v93_vlrhis;
                            $oDividaTermo->valorcorrigido    = $oDadosDebitoAtualizado->valor_corrigido;
                            $oDividaTermo->valormulta        = $valorMulta;
                            $oDividaTermo->valorjuros        = $valorJuros;
                            $oDividaTermo->valortotal        = $valorJuros +
                                                                $valorMulta +
                                                                $oDadosDebitoAtualizado->valor_corrigido;
                            $aDebitos[]                      = $oDividaTermo;
                        }
                    }

                    if ($lRemissao == false) {
                    $oDividaTermo->datalancamento    = $dDataLancamento;
                    $oDividaTermo->codigodivida      = $codDiv;
                    $oDividaTermo->datainscricao     = $oDadosDebitoOrigem->v01_dtinsc;
                    $oDividaTermo->datavencimento    = $oDadosDebitoOrigem->v01_dtvenc;
                    $oDividaTermo->dataoperacao      = $oDadosDebitoOrigem->v01_dtoper;
                    $oDividaTermo->numpre            = $oDadosDebitoOrigem->v01_numpre;
                    $oDividaTermo->numpar            = $oDadosDebitoOrigem->v01_numpar;
                    $oDividaTermo->valorcorrecao     = $oDadosDebitoOrigem->v93_vlrcor - $oDadosDebitoOrigem->v93_vlrhis;
                    $oDividaTermo->valorhistorico    = $oDadosDebitoOrigem->v93_vlrhis;
                    $oDividaTermo->valorcorrigido    = $oDadosDebitoOrigem->v93_vlrcor;
                    $oDividaTermo->valormulta        = $oDadosDebitoOrigem->v93_vlrmul;
                    $oDividaTermo->valorjuros        = $oDadosDebitoOrigem->v93_vlrjur;
                    $oDividaTermo->valortotal        = $oDadosDebitoOrigem->v93_vlrjur +
                                        $oDadosDebitoOrigem->v93_vlrmul +
                                        $oDadosDebitoOrigem->v93_vlrcor;
                    $aDebitos[]                    = $oDividaTermo;

                }
            }
        }

        return $aDebitos;
    }

    public function getProcedencias()
    {

        if ($this->getTipo() == 1) {
            require_once(modification("classes/db_termo_classe.php"));
            $aOrigens     = $this->getOrigensDebito();
            $oDaoTermo    = new cl_termo;
            $campos       = " distinct v01_proced";
            $sProcedencia = "";
            $sVirgula     = "";
            $aProcedenciasAgrupadas = array();
            foreach ($aOrigens as $oOrigem) {
                $sqlProc        = $oDaoTermo->sql_query_origem_divida($oOrigem->numpre, $campos, true);
                $rsProcedencias = $oDaoTermo->sql_record($sqlProc);
                $aProcedencias  = db_utils::getCollectionByRecord($rsProcedencias);
                foreach ($aProcedencias as $oProcedencia) {
                    $aProcedenciasAgrupadas[$oProcedencia->v01_proced] = $oProcedencia->v01_proced;
                }
            }
        } else {
            $sqlDadosDivida  = "select distinct v01_proced ";
            $sqlDadosDivida .= "  from termoinscrreg  ";
            $sqlDadosDivida .= "       inner join divida           on v93_coddiv = v01_coddiv ";
            $sqlDadosDivida .= "                                  and v01_instit = ".db_getsession('DB_instit');
            $sqlDadosDivida .= " where termoinscrreg.v93_termo = {$this->getCodigo()}";
            $rsDadosDivida   = db_query($sqlDadosDivida);
            $aProced         = db_utils::getCollectionByRecord($rsDadosDivida);
            foreach ($aProced as $oProced) {
                $aProcedenciasAgrupadas[$oProced->v01_proced] = $oProced->v01_proced;
            }
        }

        if (count($aProcedenciasAgrupadas) > 1) {
            $sSqlPRocedAgrupa = "Select * from procedenciaagrupa where v24_proced in("
                . implode(",", $aProcedenciasAgrupadas) . ")";
            $rsProcedAgrupa = db_query($sSqlPRocedAgrupa);
            $aProcedencias = db_utils::getCollectionByRecord($rsProcedAgrupa);
            foreach ($aProcedencias as $oProcedAgrupa) {
                if (in_array($oProcedAgrupa->v24_procedagrupa, $aProcedenciasAgrupadas)) {
                    $aProcedenciasAgrupadas[$oProcedAgrupa->v24_procedagrupa] = $oProcedAgrupa->v24_procedagrupa;
                    unset($aProcedenciasAgrupadas[$oProcedAgrupa->v24_proced]);
                }
            }
        }
        return $aProcedenciasAgrupadas;
    }

    public function getDataLancamentoDebito($iNumpre, $iNumpar)
    {

        $oDaoInformacaoDebito = db_utils::getDao('informacaodebito');
        $sSqlInformacaoDebito = $oDaoInformacaoDebito->sql_query_retorna_dados_origem("*", $iNumpre, $iNumpar);
        $rsInformacaoDebito   = $oDaoInformacaoDebito->sql_record($sSqlInformacaoDebito);
        $dDataLancamento      = null;

        if ($oDaoInformacaoDebito->numrows > 0) {
            $dDataLancamento = db_utils::fieldsMemory($rsInformacaoDebito, 0)->k163_data;
        }

        return $dDataLancamento;
    }
}
