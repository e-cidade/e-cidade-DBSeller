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


require_once(modification('std/DBLargeObject.php'));

use App\Domain\Configuracao\Helpers\StorageHelper;

/**
 * Caminho das mensagens json do documento
 */
define('URL_MENSAGEM_PROCESSO_DOCUMENTO', 'patrimonial.protocolo.ProcessoDocumento.');

/**
 * Model para documentos anexados ao processo do protocolo
 *
 * @package Protocolo
 * @version $Revision: 1.25 $
 * @author Jeferson Belmiro <jeferson.belmiro@dbseller.com.br>
 */
class ProcessoDocumento
{

    /**
     * Codigo do documento
     * - campo p01_sequencial
     *
     * @var int
     * @access private
     */
    private $iCodigo;

    /**
     * Processo do protocolo
     * - campo p01_protprocesso
     *
     * @var processoProtocolo
     * @access private
     */
    private $oProcessoProtocolo;

    /**
     * Descricao do documento
     * - campo p01_descricao
     *
     * @var mixed
     * @access private
     */
    private $sDescricao;

    /**
     * OID do documento anexado ao processo
     * - campo p01_documento
     *
     * @var int
     * @access private
     */
    private $iOid;

    /**
     * Tamanho limite do arquivo em bytes
     * - Limite 30mb
     *
     * @var int
     * @access private
     */
    private $iLimiteTamanho = 31457280;

    /**
     * Extensões não permitidas para os documentos
     *
     * @var array
     * @access private
     */
    private $aExtensoesInvalidas = array('exe');

    /**
     * Caminho completo do arquivo
     * - Usado para salvar ou exportar do banco
     *
     * @var string
     * @access private
     */
    private $sCaminhoArquivo;

    /**
     *
     * - Usado para salvar o usuario que incluio o documento
     *
     * @var integer
     * @access private
     */
    private $oUsuario;

    /**
     *
     * - Usado para salvar o id do  andamento
     *
     * @var integer
     * @access private
     */
    private $oProcandamint;


    /**
     * Data do arquivo
     * @var \DBDate
     */
    private $data;


    /**
     * Campo que determina se o arquivo foi ou será salvo na nuvem
     * @var boolean
     */
    private $storage = false;

    private $sNomeDocumento;

    private $p01_nomedocumento;

    private $assinado;

    private $assinadoPor;

    private $ordem = null;

    private $hash;


    /**
     * Contrutor da classe, executa lazy load
     *
     * @param int $iCodigo
     * @access public
     * @return void
     */
    public function __construct($iCodigo = 0)
    {

        /**
         * Documento nao inforamdo, contrutor nao fara nada
         */
        if (empty($iCodigo)) {
            return false;
        }

        $oDaoProtprocessodocumento = db_utils::getDao('protprocessodocumento');
        $sSqlDocumento = $oDaoProtprocessodocumento->sql_query_documento_usuario($iCodigo);

        $rsDocumento = $oDaoProtprocessodocumento->sql_record($sSqlDocumento);

        if ($oDaoProtprocessodocumento->erro_status == "0") {
            $oStdMsgErro = (object)array("iDocumento" => "$iCodigo");
            throw new BusinessException(_M(
                URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_buscar_documento_pelo_codigo',
                $oStdMsgErro
            ));
        }

        $oDocumento = db_utils::fieldsMemory($rsDocumento, 0);

        $this->iCodigo = $oDocumento->p01_sequencial;
        $this->oProcessoProtocolo = $oDocumento->p01_protprocesso;
        $this->sDescricao = $oDocumento->p01_descricao;
        $this->iOid = $oDocumento->p01_documento;
        $this->oProcandamint = $oDocumento->p01_procandamint;

        $this->oUsuario = new UsuarioSistema($oDocumento->p01_usuario);

        $this->data = $oDocumento->p01_data;
        $this->sNomeDocumento = substr($oDocumento->p01_descricao, 0, 15) . " " . $oDocumento->p01_nomedocumento;

        $this->p01_nomedocumento = $oDocumento->p01_nomedocumento;

        $this->storage = $oDocumento->p01_estorage;

        $this->ordem = $oDocumento->p01_ordem;
        $this->setHash($oDocumento->p01_ordem);
    }

    /**
     * Retorna o codigo do documento
     *
     * @access public
     * @return int
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * Define processo protocolo
     *
     * @param processoProtocolo $oProcessoProtocolo
     * @access public
     * @return void
     */
    public function setProcessoProtocolo(processoProtocolo $oProcessoProtocolo)
    {
        $this->oProcessoProtocolo = $oProcessoProtocolo;
    }

    /**
     * Retorno o processo do protocolo
     *
     * @access public
     * @return processoProtocolo
     */
    public function getProcessoProtocolo()
    {
        return $this->oProcessoProtocolo;
    }

    /**
     * Define a descricao do documento
     *
     * @param string $sDescricao
     * @access public
     * @return void
     */
    public function setDescricao($sDescricao)
    {
        $this->sDescricao = $sDescricao;
    }

    /**
     * Retorna a descricao do documento
     *
     * @access public
     * @return string
     */
    public function getDescricao()
    {
        return $this->sDescricao;
    }

    /**
     * Define o OID do documento
     *
     * @param int $iOid
     * @access public
     * @return void
     */
    public function setOID($iOid)
    {
        $this->iOid = $iOid;
    }

    /**
     * Retorna o OID do documento
     *
     * @access public
     * @return int
     */
    public function getOID()
    {
        return $this->iOid;
    }

    /**
     * Define o caminho do arquivo
     *
     * @access public
     * @return int
     */
    public function setCaminhoArquivo($sCaminhoArquivo)
    {
        $this->sCaminhoArquivo = $sCaminhoArquivo;
    }

    /**
     * Retorna o caminho do arquivo
     *
     * @access public
     * @return int
     */
    public function getCaminhoArquivo()
    {
        return $this->sCaminhoArquivo;
    }

    /**
     * Retorna o nome do documento
     * @access public
     * @return string
     */
    public function getNomeDocumento()
    {
        return $this->sNomeDocumento;
    }

    /**
     * @param $nome
     */
    public function setNomeDocumento($nome)
    {
        $this->sNomeDocumento = $nome;
    }

    /**
     * @return \DBDate
     */
    public function getData()
    {

        return $this->data;
    }

    /**
     * @param \DBDate $data
     */
    public function setData($data)
    {

        $this->data = $data;
    }

    /**
     * Define o Usuario
     * @param  $oUsuario UsuarioSistema
     * @access public
     */

    public function setUsuario(\UsuarioSistema $oUsuario)
    {
        $this->oUsuario = $oUsuario;
    }

    /**
     * Retorna objecto UsuarioSistema do usuario
     * @access public
     * @return  UsuarioSistema
     */
    public function getUsuario()
    {
        return $this->oUsuario;
    }


    /**
     * Define  o ondamento
     * @param  $iProcandamint integer
     * @access public
     */

    public function setProcandamint($oProcandamint)
    {
        $this->oProcandamint = $oProcandamint;
    }

    /**
     * Retorna o id do procandamint
     * @access public
     * @return integer
     */
    public function getProcandamint()
    {
        return $this->oProcandamint;
    }

    /**
     * Retorna se o documento est? ou n?o no estorage
     * @access public
     * @return integer
     */
    public function estorage($storage = null)
    {
        if (!empty($storage)) {
            $this->storage = $storage;
            return $this;
        }

        return $this->storage;
    }

    /**
     * Define a ordem do documento
     *
     * @param integer $ordem
     * @return this
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    /**
     * Retorna a ordem do documento
     *
     * @return integer
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Validar arquivo
     * - tamanho limite
     * - extensão
     *
     * @access public
     * @return boolean
     */
    private function validarArquivo()
    {

        $oStdMensagemErro = new stdClass();
        $oStdMensagemErro->sCaminhoArquivo = $this->sCaminhoArquivo;
        $oStdMensagemErro->iLimiteTamanho = $this->iLimiteTamanho;

        /** filesize($this->sCaminhoArquivo) > $this->iLimiteTamanho
         * Arquivo nao encontrado
         */
        if (!file_exists($this->sCaminhoArquivo)) {
            throw new BusinessException(_M(
                URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_arquivo_invalido',
                $oStdMensagemErro
            ));
        }

        $aInformacoesArquivo = pathinfo($this->sCaminhoArquivo);

        /**
         * Arquivo maior que o permitido
         */
        if (filesize($this->sCaminhoArquivo) > $this->iLimiteTamanho) {
            throw new BusinessException(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_tamanho_limite', $oStdMensagemErro));
        }


        /**
         * Arquivo com extensao invalida
         */
        if (!empty($aInformacoesArquivo['extension']) && in_array(
            $aInformacoesArquivo['extension'],
            $this->aExtensoesInvalidas
        )) {
            $oStdMensagemErro->sExtensao = $aInformacoesArquivo['extension'];
            throw new BusinessException(_M(
                URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_extensao_invalida',
                $oStdMensagemErro
            ));
        }

        return true;
    }

    /**
     * Salvar
     *
     * @access public
     * @return boolean
     */
    public function salvar()
    {

        if (!db_utils::inTransaction()) {
            throw new DBException(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_nenhuma_transacao_banco'));
        }

        if (!empty($this->iCodigo)) {
            return $this->alterar();
        }

        /**
         * Valida arquivo, tamanho e extensao
         */
        $this->validarArquivo();

        return $this->incluir();
    }

    /**
     * Inclui documento para o processo do protocolo
     * - salva arquivo no banco
     *
     * @access private
     * @return boolean
     */
    private function incluir()
    {

        /**
         * Processo do protocolo nao informado
         */
        if (!($this->oProcessoProtocolo instanceof processoProtocolo) && $this->getProcessoProtocolo()->getCodProcesso() != '') {
            throw new Exception(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_processo_nao_informado'));
        }

        if (!$this->storage) {
            $this->iOid = $this->salvarArquivoBanco();
            $this->sNomeDocumento = basename($this->sCaminhoArquivo);
        }


        $oDaoProtprocessodocumento = new cl_protprocessodocumento();
        $oDaoProtprocessodocumento->p01_protprocesso = $this->getProcessoProtocolo()->getCodProcesso();
        $oDaoProtprocessodocumento->p01_descricao = DBString::removerCaracteresEspeciaisAcentos($this->sDescricao);
        $oDaoProtprocessodocumento->p01_documento = $this->iOid;
        $oDaoProtprocessodocumento->p01_nomedocumento = DBString::removerCaracteresEspeciaisAcentos($this->sNomeDocumento);
        $oDaoProtprocessodocumento->p01_data = date('Y-m-d');
        $oDaoProtprocessodocumento->p01_usuario = $this->getUsuario()->getIdUsuario();
        $oDaoProtprocessodocumento->p01_procandamint = $this->getProcandamint();
        $oDaoProtprocessodocumento->p01_estorage = $this->storage ? "true" : "false";
        $oDaoProtprocessodocumento->p01_ordem = $this->getOrdem();
        $oDaoProtprocessodocumento->p01_assinado = $this->getAssinado();
        $oDaoProtprocessodocumento->p01_assinado_por = $this->getAssinadoPor();
        $oDaoProtprocessodocumento->p01_documento_hash  = $this->getHash();

        $oDaoProtprocessodocumento->incluir(null);

        /**
         * Erro ao incluir documento
         */
        if ($oDaoProtprocessodocumento->erro_status == "0") {
            throw new Exception(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_incluir_documento'));
        }

        $this->iCodigo = $oDaoProtprocessodocumento->p01_sequencial;
        return _M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'documento_salvo');//true;
    }

    /**
     * Alterar documento
     * - Altera descricao do arquivo
     * - Arquivo(OID) e protocolo nao sao alterados
     *
     * @access private
     * @return boolean
     */
    private function alterar()
    {
        $oDaoProtprocessodocumento = new cl_protprocessodocumento();
        $oDaoProtprocessodocumento->p01_descricao = $this->sDescricao;
        $oDaoProtprocessodocumento->p01_sequencial = $this->iCodigo;
        $oDaoProtprocessodocumento->p01_nomedocumento = $this->sNomeDocumento;
        $oDaoProtprocessodocumento->p01_documento = $this->iOid;
        $oDaoProtprocessodocumento->p01_assinado = $this->assinado;
        $oDaoProtprocessodocumento->p01_assinado_por = $this->assinadoPor;
        $oDaoProtprocessodocumento->p01_ordem = $this->ordem;
        $oDaoProtprocessodocumento->p01_documento_hash = $this->getHash();
        $oDaoProtprocessodocumento->alterar($this->iCodigo);

        /**
         * Erro ao alterar documento
         */
        if ($oDaoProtprocessodocumento->erro_status == "0") {
            throw new Exception(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_alterar_documento'));
        }
        return _M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'documento_alterado');//true;
    }

    /**
     * Salva arquivo no banco
     * - gera OID
     *
     * @access private
     * @return int
     */
    private function salvarArquivoBanco()
    {

        $iOid = DBLargeObject::criaOID(true);
        $lEscreveuArquivo = DBLargeObject::escrita($this->sCaminhoArquivo, $iOid);

        if (!$lEscreveuArquivo) {
            throw new BusinessException(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_escrever_arquivo_banco'));
        }

        return $iOid;
    }

    /**
     * Download documento
     * - retorna o caminho do arquivo para download
     *
     * @access public
     * @return string - caminho do arquivo extraido do banco
     */
    public function download()
    {
        if ($this->isStorage()) {
            try {
                return StorageHelper::downloadArquivo($this->getOID());
            } catch (\Exception $ex) {
            }
        }

        $sCaracteres = "/[^a-z0-9\\_\.]/i";
        $sNomeArquivo = str_replace(" ", '_', $this->sNomeDocumento);
        $sNomeArquivo = preg_replace($sCaracteres, '', $sNomeArquivo);
        $sCaminhoArquivo = 'tmp/' . $sNomeArquivo;
        $lEscreveuArquivo = DBLargeObject::leitura($this->iOid, $sCaminhoArquivo);

        if (!$lEscreveuArquivo) {
            $oStdMensagemErro = new StdClass();
            $oStdMensagemErro->sCaminhoArquivo = $sCaminhoArquivo;
            throw new BusinessException(_M(
                URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_escrever_arquivo_diretorio',
                $oStdMensagemErro
            ));
        }

        return $sCaminhoArquivo;
    }

    /**
     * Exclui documento
     *
     * @access public
     * @return boolean
     */
    public function excluir()
    {

        if (empty($this->iCodigo)) {
            throw new Exception(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_documento_nao_especificado'));
        }

        $oDaoProtprocessodocumento = db_utils::getDao('protprocessodocumento');
        $oDaoProtprocessodocumento->excluir($this->iCodigo);

        if ($oDaoProtprocessodocumento->erro_status == "0") {
            throw new Exception(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_excluir_documento'));
        }

        $lExclusao = true;

        if (!empty($this->iOid) and !$this->isStorage()) {
            $lExclusao = DBLargeObject::exclusao($this->iOid);
        }

        /**
         * Erro ao excluir documento do banco
         */
        if (!$lExclusao) {
            throw new Exception(_M(URL_MENSAGEM_PROCESSO_DOCUMENTO . 'erro_excluir_documento_banco'));
        }

        return true;
    }

    /**
     * @param $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return bool
     */
    public function isStorage()
    {
        return $this->storage == 't' || $this->storage == 'true' || $this->storage == true;
    }

    public static function getLastOrdemProcesso($processo)
    {
        $oDaoProtprocessodocumento = new cl_protprocessodocumento();
        $sql = $oDaoProtprocessodocumento->query_last_order_processo($processo);
        $rs = db_query($sql);

        if (!$rs || pg_num_rows($rs) == 0) {
            throw new Exception("Erro ao buscar ordem");
        }

        $max = db_utils::fieldsMemory($rs, 0)->max;
        return !empty($max) ? $max : 0;
    }

    /**
     * @return boolean
     */
    public function getAssinado()
    {
        return $this->assinado;
    }

    /**
     * @param boolean $assinado
     */
    public function setAssinado($assinado)
    {
        $this->assinado = $assinado;
    }

    /**
     * @return integer
     */
    public function getAssinadoPor()
    {
        return $this->assinadoPor;
    }

    /**
     * @param integer $assinadoPor
     */
    public function setAssinadoPor($assinadoPor)
    {
        $this->assinadoPor = $assinadoPor;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
}
