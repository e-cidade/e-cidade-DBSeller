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

/**
 * Class AcordoComissaoMembro
 */
class AcordoComissaoMembro
{

    /**
     * @var int
     */
    const TIPO_GESTOR = 1;
    /**
     * @var int
     */
    const TIPO_SECUNDARIO = 2;
    /**
     * @var int
     */
    const TIPO_SUPLENTE = 3;
    /**
     * @var int
     */
    const TIPO_FISCAL = 4;

    /**
     * @var
     */
    private $iCodigo;
    /**
     * @var
     */
    private $sNome;
    /**
     * @var
     */
    private $iCodigoCgm;
    /**
     * @var
     */
    private $iCodigoComissao;
    /**
     * @var
     */
    private $iResponsabilidade;
    /**
     * @var
     */
    private $sDescricaoResponsabilidade;
    /**
     * @var
     */
    private $oDataInicio;
    /**
     * @var
     */
    private $oDataTermino;
    /**
     * @var null
     */
    private $numeroAtoDesignacao = null;
    /**
     * @var null
     */
    private $anoAtoDesignacao = null;
    /**
     * @var null
     */
    private $nomeArquivo = null;
    /**
     * @var null
     */
    private $arquivo = null;

    /**
     * AcordoComissaoMembro constructor.
     * @param null $iCodigo
     */
    function __construct($iCodigo = null)
    {
        if ($iCodigo) {
            $oDaoComissaoMembro = new cl_acordocomissaomembro;
            $sSqlMembro = $oDaoComissaoMembro->sql_query(null, '*', '', " ac07_sequencial={$iCodigo}");
            $rsCM = $oDaoComissaoMembro->sql_record($sSqlMembro);

            if ($rsCM && $oDaoComissaoMembro->numrows != 0) {
                $oMembro = db_utils::fieldsMemory($rsCM, 0);

                $this->setCodigo($oMembro->ac07_sequencial);
                $this->setNome($oMembro->z01_nome);
                $this->setCodigoCgm($oMembro->ac07_numcgm);
                $this->setCodigoComissao($oMembro->ac07_acordocomissao);
                $this->setResponsabilidade($oMembro->ac07_tipomembro);
                $this->setDescricaoResponsabilidade($oMembro->ac42_descricao);
                $this->setNumeroAtoDesignacao($oMembro->ac07_numeroatodesignacao);
                $this->setAnoAtoDesignacao($oMembro->ac07_anoatodesignacao);
                $this->nomeArquivo = $oMembro->ac07_nomearquivo;
                $this->arquivo = $oMembro->ac07_arquivo;

                if ($oMembro->ac07_datainicio) {
                    $this->setDataInicio(new DBDate($oMembro->ac07_datainicio));
                }
                if ($oMembro->ac07_datatermino) {
                    $this->setDataTermino(new DBDate($oMembro->ac07_datatermino));
                }
            }
        }
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    public function save()
    {
        if (!db_utils::inTransaction()) {
            throw new Exception("Não existe transação ativa com o bando de dados.");
        }

        $oDaoComissaoMembro = new cl_acordocomissaomembro;
        $oDaoComissaoMembro->ac07_numcgm = $this->getCodigoCgm();
        $oDaoComissaoMembro->ac07_acordocomissao = $this->getCodigoComissao();
        $oDaoComissaoMembro->ac07_tipomembro = $this->getResponsabilidade();
        $oDaoComissaoMembro->ac07_numeroatodesignacao = $this->getNumeroAtoDesignacao();
        $oDaoComissaoMembro->ac07_anoatodesignacao = $this->getAnoAtoDesignacao();
        $oDaoComissaoMembro->ac07_nomearquivo = $this->getNomeArquivo();
        $oDaoComissaoMembro->ac07_arquivo = $this->getArquivo() === null ? 'null' : $this->getArquivo();
        $sPathArquivo = null;

        if($this->getNomeArquivo() != null && $this->getNomeArquivo() != "") {

          $sPathArquivo = "tmp/".$this->getNomeArquivo();
          pg_lo_export($this->getArquivo(), $sPathArquivo);
          $iTamanhoArquivo = file_exists($sPathArquivo)?filesize($sPathArquivo):0;
          $oDaoLicitaparam = new cl_licitaparam;
          $sSqlParametro   = $oDaoLicitaparam->sql_query(null, 'l12_limitetamanhoarquivo', '', "l12_instit=".db_getsession('DB_instit'));
          $rsParametro     = $oDaoLicitaparam->sql_record($sSqlParametro);
          if (!$rsParametro || ($rsParametro && $oDaoLicitaparam->numrows == 0)) {

            throw new ParameterException('Não foi encontrado parâmetro do módulo Licitação. Configure para utilizar rotina');
          }

          $oParametro      = db_utils::fieldsMemory($rsParametro, 0);
          $iTamanhoMaximo  = $oParametro->l12_limitetamanhoarquivo;
          if ( $iTamanhoArquivo > $iTamanhoMaximo ) {

            throw new FileException("Tamanho do Arquivo Excede o Limite Permitido. Tamanho do arquivo({$sPathArquivo}): {$iTamanhoArquivo}.
            Limite do tamanho configurado {$iTamanhoMaximo}");
          }

          if ($this->getCodigo()) {

            $sSqlValidaExistencia = "select *
                                       from acordos.acordocomissaomembro
                                      where ac07_sequencial  <> {$this->getCodigo()}
                                        and upper(ac07_nomearquivo) = upper('{$this->getNomeArquivo()}')";
          } else {

              $sSqlValidaExistencia = "select *
                                         from acordos.acordocomissaomembro
                                        where upper(ac07_nomearquivo) = upper('{$this->getNomeArquivo()}')";
          }

          $rsValidaExistencia = db_query($sSqlValidaExistencia);

          if(pg_numrows($rsValidaExistencia) > 0) {

            throw new FileException("O arquivo já Existe no Cadastro. Renomeie o Arquivo. {$this->getNomeArquivo()}");
          }
        }

        $oDataInicio = $this->getDataInicio();
        if ($oDataInicio) {
            $oDaoComissaoMembro->ac07_datainicio = $this->getDataInicio()->getDate();
        }

        $oDataTermino = $this->getDataTermino();
        if ($oDataTermino) {
            $oDaoComissaoMembro->ac07_datatermino = $this->getDataTermino()->getDate();
        }

        if ($oDataInicio && $oDataTermino && $oDataInicio->getTimeStamp() > $oDataTermino->getTimeStamp()) {
            throw new BusinessException('Data de Início deve ser menor ou igual a Data de Término.');
        }

        if ($this->getCodigo()) {
            $oDaoComissaoMembro->ac07_sequencial = $this->getCodigo();
            $oDaoComissaoMembro->alterar($this->getCodigo());
        } else {
            $oDaoComissaoMembro->incluir(null);
        }

        if ($oDaoComissaoMembro->erro_status == 0) {
            $sMensagem = "Houve um erro ao salvar dados do membro da comissao.\n";
            $sMensagem .= "Erro:{$oDaoComissaoMembro->erro_msg}";
            throw new Exception($sMensagem);
        }
    }

    /**
     * @throws Exception
     */
    public function excluir()
    {
        $oDaoComissaoMembro = new cl_acordocomissaomembro;
        $oDaoComissaoMembro->excluir($this->getCodigo());

        if ($oDaoComissaoMembro->erro_status == 0) {
            $sMensagem = "Houve um erro ao excluir o membro da comissao.\n";
            $sMensagem .= "Erro:{$oDaoComissaoMembro->erro_msg}";
            throw new Exception($sMensagem);
        }
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * @return mixed
     */
    public function getCodigoCgm()
    {
        return $this->iCodigoCgm;
    }

    /**
     * @return mixed
     */
    public function getCodigoComissao()
    {
        return $this->iCodigoComissao;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->sNome;
    }

    /**
     * @return mixed
     */
    public function getResponsabilidade()
    {
        return $this->iResponsabilidade;
    }

    /**
     * @param $iCodigo
     */
    public function setCodigo($iCodigo)
    {
        $this->iCodigo = $iCodigo;
        return $this;
    }

    /**
     * @param $iCodigoCgm
     */
    public function setCodigoCgm($iCodigoCgm)
    {
        $this->iCodigoCgm = $iCodigoCgm;
        return $this;
    }

    /**
     * @param $iCodigoComissao
     */
    public function setCodigoComissao($iCodigoComissao)
    {
        $this->iCodigoComissao = $iCodigoComissao;
        return $this;
    }

    /**
     * @param $sNome
     */
    public function setNome($sNome)
    {
        $this->sNome = $sNome;
        return $this;
    }

    /**
     * @param $iResponsabilidade
     */
    public function setResponsabilidade($iResponsabilidade)
    {
        $this->iResponsabilidade = $iResponsabilidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescricaoResponsabilidade()
    {
        return $this->sDescricaoResponsabilidade;
    }

    /**
     * @param $sDescricaoResponsabilidade
     */
    public function setDescricaoResponsabilidade($sDescricaoResponsabilidade)
    {
        $this->sDescricaoResponsabilidade = $sDescricaoResponsabilidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataInicio()
    {
        return $this->oDataInicio;
    }

    /**
     * @param DBDate $oDataInicio
     */
    public function setDataInicio(DBDate $oDataInicio)
    {
        $this->oDataInicio = $oDataInicio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataTermino()
    {
        return $this->oDataTermino;
    }

    /**
     * @param DBDate $oDataTermino
     */
    public function setDataTermino(DBDate $oDataTermino)
    {
        $this->oDataTermino = $oDataTermino;
        return $this;
    }

    /**
     * @return null
     */
    public function getNumeroAtoDesignacao()
    {
        return $this->numeroAtoDesignacao;
    }

    /**
     * @param $numeroAtoDesignacao
     */
    public function setNumeroAtoDesignacao($numeroAtoDesignacao)
    {
        $this->numeroAtoDesignacao = $numeroAtoDesignacao;
        return $this;
    }

    /**
     * @return null
     */
    public function getAnoAtoDesignacao()
    {
        return $this->anoAtoDesignacao;
    }

    /**
     * @param $anoAtoDesignacao
     */
    public function setAnoAtoDesignacao($anoAtoDesignacao)
    {
        $this->anoAtoDesignacao = $anoAtoDesignacao;
        return $this;
    }

    /**
     * @return null
     */
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }

    /**
     * @param $arquivo
     * @param string $separador
     */
    public function setNomeArquivo($arquivo, $separador = '_')
    {
        if ($arquivo) {
            $titulo = $arquivo->name;
            $titulo = str_replace(' ', $separador, $titulo);
            $titulo = mb_strtolower($titulo);
            $titulo = preg_replace('/[^a-zA-Z0-9_.-]/', '', $titulo);

            $this->nomeArquivo = trim($titulo, $separador);
        }
        return $this;
    }

    /**
     * @return null
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param $arquivo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = pg_lo_import($arquivo->tmp_name);
        return $this;
    }

}
