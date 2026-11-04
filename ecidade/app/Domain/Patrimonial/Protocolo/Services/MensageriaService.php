<?php

namespace App\Domain\Patrimonial\Protocolo\Services;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Ouvidoria\Services\ProcessoEletronicoService;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\HistoricoVisualizacaoMensageria;
use App\Domain\Patrimonial\Protocolo\Model\Mensageria;
use App\Domain\Patrimonial\Protocolo\Model\MensageriaDocumento;
use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoProcessoInterno;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Notifications\UserNotification;
use ECidade\Lib\Request\ProcessoEletronico\ProcessoEletronico;
use ECidade\V3\Extension\Registry;
use Illuminate\Support\Facades\DB;
use stdClass;

class MensageriaService
{
    private $mensageria;

    private $historicoVisualizacao;

    public function __construct()
    {
        $this->mensageria = new Mensageria();
        $this->historicoVisualizacao = new HistoricoVisualizacaoMensageria();
    }

    /**
     * @throws \Exception
     */
    public function saveMensagem($parametros)
    {
        if (!array_key_exists('cpfcnpj', $parametros) ||
            !array_key_exists('nome_usuario', $parametros)
        ) {
            $infoUser = $this->getInfoUsers($parametros['DB_id_usuario']);
            $parametros['cpfcnpj'] = $infoUser[0]->z01_cgccpf;
            $parametros['nome_usuario'] = $infoUser[0]->z01_nome;
        }

        if (array_key_exists('mensagem', $parametros) &&
            array_key_exists('processo', $parametros)
        ) {
            $this->mensageria->setMensagem($parametros['mensagem']);
            $this->mensageria->setProcesso($parametros['processo']);
            $this->mensageria->setCpfCnpj($parametros['cpfcnpj']);
            $this->mensageria->setNome($parametros['nome_usuario']);

            if (array_key_exists('acao', $parametros)) {
                if ($parametros['acao'] === 'interna') {
                    $this->mensageria->setInterna(true);
                    //ja notifica o user externo
                    $processoProtocolo = new \processoProtocolo($parametros['processo']);
                    $this->notificar($processoProtocolo);
                } else {
                    $this->mensageria->setInterna(false);
                }
            } else {
                $this->mensageria->setInterna(false);
            }

            if (array_key_exists('mensagemReferenciada', $parametros)) {
                $this->mensageria->setRespostaMensagem($parametros['mensagemReferenciada']);
            }
            $result = $this->mensageria->save();

            if ($result) {
                if (!array_key_exists('acao', $parametros) && array_key_exists('mensagemReferenciada', $parametros)) {
                    //notifica o user interno do ecidade
                    $mensagemReferenciada = Mensageria::find($parametros['mensagemReferenciada']);
                    if (!empty($mensagemReferenciada->p123_cpfcnpj)) {
                        $result = $this->getIdUser($mensagemReferenciada->p123_cpfcnpj);
                        if (count($result) > 0) {
                            $processo = Processo::find($mensagemReferenciada->p123_processo);
                            $user = Usuario::find($result[0]->id_usuario);
                            $mensagem = "Sua mensagem foi respondida pelo usuário
                            {$parametros['nome_usuario']} no processo {$processo->p58_numero}/$processo->p58_ano";
                            $user->notify(new UserNotification("Nova Mensagem", $mensagem));
                        }
                    }
                }
            } else {
                return new DBJsonResponse(['mensagem' => 'Erro ao salvar a mensagem', 'erro' => true]);
            }

            if (array_key_exists('arquivos', $parametros)) {
                if ($result) {
                    try {
                        $arquivos = $this->saveArquivosStorage($parametros['arquivos']);
                        if (count($arquivos) > 0) {
                            foreach ($arquivos as $arquivo) {
                                $mensageriaDocumento = new MensageriaDocumento();
                                $mensageriaDocumento->setCodigoStorage($arquivo->id);
                                $mensageriaDocumento->setNomeDocumento($arquivo->nome);
                                $mensageriaDocumento->setMensagem($this->mensageria->getId());
                                $mensageriaDocumento->save();
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                } else {
                    throw new \Exception('Erro ao tentar salvar mensagem');
                }
            }

            if (array_key_exists('anexos', $parametros)) {
                $anexos = collect();
                foreach ($parametros['anexos'] as $anexo) {
                    ProcessoEletronicoService::validaAnexoBase64eAdicionaCaminhoTemporario($anexo);
                    $anexoObj = (object)[
                        'caminho' => $anexo['caminho'],
                        'descricao' => $this->tratarNomeAnexos($anexo['nome'])
                    ];
                    $anexos->push($anexoObj);
                }

                if ($result) {
                    try {
                        $arquivos = $this->saveArquivosStorage(json_encode($anexos->values()));
                        if (count($arquivos) > 0) {
                            foreach ($arquivos as $arquivo) {
                                $mensageriaDocumento = new MensageriaDocumento();
                                $mensageriaDocumento->setCodigoStorage($arquivo->id);
                                $mensageriaDocumento->setNomeDocumento($arquivo->nome);
                                $mensageriaDocumento->setMensagem($this->mensageria->getId());
                                $mensageriaDocumento->save();
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \Exception($e);
                    }
                }
            }
        }

        return new DBJsonResponse(['mensagem' => 'Mensagem salva com sucesso', 'erro' => false]);
    }

    /**
     * @throws \ParameterException
     * @throws \Exception
     */
    public function saveArquivosStorage($arquivos)
    {
        $anexos = json_decode($arquivos);
        $anexosAux = array();

        foreach ($anexos as $anexo) {
            $storageConfig = StorageHelper::getStorageConfig();
            $allowed = array();

            if (isset($storageConfig->client_id_ouvidoria) && !empty($storageConfig->client_id_ouvidoria)) {
                $allowed[] = $storageConfig->client_id_ouvidoria;
            }

            try {
                $arquivo = StorageHelper::uploadArquivo($anexo->caminho, $allowed);
                $anexoAux = new stdClass();
                $anexoAux->id = $arquivo->id;
                $anexoAux->nome = $anexo->descricao;
                $anexosAux[] = $anexoAux;
            } catch (\Exception $ex) {
                foreach ($anexosAux as $anexoEnviado) {
                    try {
                        StorageHelper::deleteArquivo($anexoEnviado->id);
                    } catch (\Exception $ex) {
                        $logger = Registry::get('app.container')->get('app.logger');
                        $logger->error($ex);
                    }
                }

                throw new \Exception($ex);
            }
        }

        return $anexosAux;
    }

    public function getMensagensProcesso($parametros)
    {
        $viewer = $this->getVisualizacao($parametros);

        $mensagens = Mensageria::with('documentos', 'referencia', 'despacho')
            ->where('p123_processo', $parametros['processo'])
            ->orderBy('p123_data_criacao')
            ->get();
        $this->marcarMensagemComoVisualizada($mensagens);

        return [
            'ultima_visualizacao' => $viewer,
            'mensagens' => $mensagens
        ];
    }

    public function getVisualizacao($parametros)
    {
        if (array_key_exists('DB_id_usuario', $parametros)) {
            $infoUser = $this->getInfoUsers($parametros['DB_id_usuario']);
            $result = HistoricoVisualizacaoMensageria::where('p125_cpfcnpj', $infoUser[0]->z01_cgccpf)
                ->where('p125_processo', $parametros['processo'])->first();
        } else {
            $result = HistoricoVisualizacaoMensageria::where('p125_cpfcnpj', $parametros['cpfcnpj'])
                ->where('p125_processo', $parametros['processo'])->first();
        }

        if ($result != null) {
            $historicoModel = $this->historicoVisualizacao->find($result->p125_id);
            $historicoModel->setData(DB::raw('CURRENT_TIMESTAMP(0)'));
            $historicoModel->update();
        } else {
            $visualizacaoHistorico = new HistoricoVisualizacaoMensageria();
            $visualizacaoHistorico->setProcesso($parametros['processo']);

            if (array_key_exists('DB_id_usuario', $parametros)) {
                $visualizacaoHistorico->setCpfCnpj($infoUser[0]->z01_cgccpf);
                $visualizacaoHistorico->setNome($infoUser[0]->z01_nome);
            } else {
                $visualizacaoHistorico->setCpfCnpj($parametros['cpfcnpj']);
                $visualizacaoHistorico->setNome($parametros['nome']);
            }
            $visualizacaoHistorico->setData(DB::raw('CURRENT_TIMESTAMP(0)'));
            $visualizacaoHistorico->save();
        }

        if (HistoricoVisualizacaoMensageria::count() > 1) {
            return HistoricoVisualizacaoMensageria::orderBy('p125_data', 'desc')->skip(1)->first();
        } else {
            return HistoricoVisualizacaoMensageria::latest('p125_data')->first();
        }
    }

    public function getInfoUsers($idUser)
    {
        return DB::select("
            select
            configuracoes.db_usuarios.id_usuario,
            protocolo.cgm.z01_numcgm,
            protocolo.cgm.z01_nome,
            protocolo.cgm.z01_email,
            protocolo.cgm.z01_cgccpf
            from configuracoes.db_usuacgm
            inner join configuracoes.db_usuarios on
                configuracoes.db_usuarios.id_usuario = configuracoes.db_usuacgm.id_usuario
            inner join protocolo.cgm on protocolo.cgm.z01_numcgm = configuracoes.db_usuacgm.cgmlogin
            where configuracoes.db_usuarios.id_usuario = {$idUser};
        ");
    }

    public function vincularDespachoMensagem($codigoDespacho, $idMensagem)
    {
        $andamentoInterno = AndamentoProcessoInterno::find($codigoDespacho);
        $andamentoInterno->setMensageria($idMensagem);
        $result = $andamentoInterno->update();
        if (!$result) {
            return false;
        } else {
            $mensagemModel = Mensageria::find($idMensagem);
            $mensagemModel->setDespacho($codigoDespacho);
            $result = $mensagemModel->update();
            if (!$result) {
                return false;
            }
            return true;
        }
    }

    public function notificar(\processoProtocolo $processoAtual)
    {
        $cgm = Cgm::where('z01_numcgm', $processoAtual->getCgm())->first();
        $cpfCnpj = null;

        if ($processoAtual->isEletronico() === true) {
            $titular = \CgmFactory::getInstanceByCgm($processoAtual->getCgm());
            $atendimentoProcesso = $processoAtual->getProcessoAtendimento();
            if ($atendimentoProcesso) {
                $atendimentoCidadao = $atendimentoProcesso->getAtendimentoCidadao();
                if ($atendimentoCidadao) {
                    $cidadao = $atendimentoCidadao->getCidadao();
                }
            }

            if (!isset($cidadao)) {
                $cpfCnpj = $cgm->z01_cgccpf;
            } else {
                $cpfCnpj = $cidadao->getCnpjCpf();
            }

            $message = "O processo {$processoAtual->getNumeroProcesso()}/";
            $message .= "{$processoAtual->getAnoProcesso()} do titular ";
            $message .= " {$titular->getNome()}, tem uma nova mensagem.";

            $processoEletronico = new ProcessoEletronico();
            $processoEletronico->notificar($message, $cpfCnpj);
        }
    }

    public function marcarMensagemComoVisualizada($mensagens)
    {
        foreach ($mensagens as $mensagem) {
            if ($mensagem->p123_data_visualizada === null && $mensagem->p123_interna === false) {
                $mensagem->setDataVisualizada(DB::raw('CURRENT_TIMESTAMP(0)'));
                $mensagem->update();
            }
        }
    }

    public function tratarNomeAnexos($nomeAnexo)
    {
        $stringLimpa = preg_replace('/[^\x20-\x7E]/', '', $nomeAnexo);
        $stringSemAcentos = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $stringLimpa);
        $stringSemEspeciais = preg_replace('/[^A-Za-z0-9 ]/', '', $stringSemAcentos);
        return $stringSemEspeciais;
    }

    public function getIdUser($cpfcnpj)
    {
        return DB::select("select configuracoes.db_usuacgm.id_usuario from configuracoes.db_usuacgm
                            inner join protocolo.cgm on protocolo.cgm.z01_numcgm = configuracoes.db_usuacgm.cgmlogin
                            where protocolo.cgm.z01_cgccpf = '{$cpfcnpj}' limit 1");
    }
}
