<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Patrimonial\Ouvidoria\Model\TipoProcessoFormaReclamacao;
use App\Domain\Patrimonial\Ouvidoria\Services\ProcessoEletronicoService;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\TipoProc;
use ECidade\Lib\Session\DefaultSession;
use Illuminate\Support\Facades\Log;

class ProtocoloDocumentoService
{

    private $processoService;

    private $processo;
    private $transferencia;
    private $anexos = [];

    public function __construct()
    {
        $this->processoService = new  ProcessoService();
    }

    /**
     * @return Processo
     */
    public function getProcesso()
    {
        return $this->processo;
    }


    /**
     * @param Processo $processo
     * @return void
     */
    public function setProcesso(Processo $processo)
    {
        $this->processo = $processo;
    }


    /**
     * @param $codigoTipoProcesso | campo p51_codigo da tabela tipoproc
     * @param $titular | campo z01_numcgm da tabela protocolo.cgm
     * @param string $requerente | texto livre
     * @param string $observacao | texto livre
     * @return ProtocoloDocumentoService
     * @throws \Exception
     */
    public function gerarProcesso(
        $codigoTipoProcesso,
        $titular,
        $requerente = "",
        $observacao = "",
        $principal = null
    ) {

        if (!TipoProc::find($codigoTipoProcesso)
            ->exists()) {
            throw new \Exception("Tipo de Processo não encontrado!");
        }

        $titular = Cgm::find($titular);

        if (!$titular) {
            throw new \Exception("Titular não encontrado!");
        }

        $requerente = !empty($requerente) ? $requerente : $titular->z01_nome;
        $processo = new Processo();
        $processo->setCodigo($codigoTipoProcesso);
        $processo->setInterno(true);
        $processo->setPublico(false);
        $processo->setCgm($titular->z01_numcgm);
        $processo->setDespacho('Criado Processo');
        $processo->setObservacao($observacao);
        $processo->setAno(db_getsession("DB_anousu"));
        $processo->setRequerente(substr(\DBString::upperCaseRemoveCaracteresEspeciais($requerente), 0, 79));
        $processo->setTipoProcesso(Processo::TIPO_PROCESSO_ELETRONICO);
        $processo->setProcessopai($principal);
        $this->setProcesso($this->processoService->salvarProcesso($processo, true));
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function transferir($departamento, $usuario, $usuarioTransferencia = null)
    {
        $this->processoValido();
        $this->transferencia = $this->processoService->transferir(
            $this->getProcesso(),
            $departamento,
            $this->getProcesso()->getDepartamento(),
            $usuario,
            $usuarioTransferencia
        );

        if ((int) $departamento == (int) db_getsession("DB_coddepto") and empty($usuarioTransferencia)) {
            $this->processoService->receber(
                $this->getProcesso(),
                $this->transferencia,
                "Recebimento automático"
            );
            return $this;
        }

        $this->processoService->andamentoSemReceber(
            $this->getProcesso(),
            $this->transferencia,
            'Processo ' . $this->processo->getCodigoProcesso() . ' criado'
        );

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function criarCapar()
    {
        $this->processoValido();
        $this->anexos[] = $this->processoService->criarCapa(
            $this->getProcesso(),
            $this->transferencia
        );
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function anexarArquivos(array $anexos = array())
    {
        if (empty($anexos)) {
            return $this;
        }

        $this->processoValido();
        $storageConfig = StorageHelper::getStorageConfig();
        $allowed = array();

        if (isset($storageConfig->client_id_ouvidoria) && !empty($storageConfig->client_id_ouvidoria)) {
            $allowed[] = $storageConfig->client_id_ouvidoria;
        }
        $anexosAux = array();
        $ordem = 1;
        foreach ($anexos as $anexo) {
            ProcessoEletronicoService::validaAnexoBase64eAdicionaCaminhoTemporario($anexo);
            $anexoObj = (object)$anexo;

            try {
                $arquivo = StorageHelper::uploadArquivo($anexoObj->caminho, $allowed);
                $anexoAux = new \stdClass();
                $anexoAux->id = $arquivo->id;
                $anexoAux->nome = $anexoObj->nome;
                $anexosAux[] = $anexoAux;
                $model = new ProcessoDocumento();

                $model->setProcesso($this->getProcesso()->getCodigoProcesso());
                $model->setDescricao($anexoAux->nome);
                $model->setDocumento($arquivo->id);
                $model->setNomeDocumento(str_replace("'", "", $anexoAux->nome));
                $model->setUsuario(DefaultSession::getInstance()->get(DefaultSession::DB_ID_USUARIO));
                $model->setData(date("Y-m-d"));
                $model->setStorage(true);
                $model->setOrdem($ordem++);
                $this->anexos[] = $this->processoService->salvarDocumento($model);
            } catch (\Exception $ex) {
                foreach ($anexosAux as $anexoEnviado) {
                    try {
                        StorageHelper::deleteArquivo($anexoEnviado->id);
                    } catch (\Exception $ex) {
                        Log::debug("Tentou deletar aqui {$anexoEnviado}:" . $ex->getMessage());
                    }
                }
                throw new \Exception($ex->getMessage());
            }
        }


        return $this;
    }


    public function processoValido()
    {
        if (empty($this->getProcesso()) || !($this->getProcesso() instanceof Processo)) {
            throw new \Exception("Não foi possivel criar capa, processo inválido!");
        }
    }

    /**
     * @return array
     */
    public function getAnexos()
    {
        return $this->anexos;
    }
}
