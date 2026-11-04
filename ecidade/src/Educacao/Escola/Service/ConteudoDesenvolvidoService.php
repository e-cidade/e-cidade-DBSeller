<?php

namespace ECidade\Educacao\Escola\Service;

use DateTime;
use DBDate;
use ECidade\Educacao\Escola\Model\ConteudoDesenvolvido;
use ECidade\Educacao\Escola\Registry\ConteudoDesenvolvidoRegistry;
use ECidade\Educacao\Escola\Repository\ConteudoDesenvolvidoRepository;
use Exception;
use Regencia;
use stdClass;
use UsuarioSistema;

/**
 * Class ConteudoDesenvolvidoService
 * @package ECidade\Educacao\Escola\Service
 */
class ConteudoDesenvolvidoService
{
    /**
     * @var ConteudoDesenvolvidoRepository
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new ConteudoDesenvolvidoRepository();
    }

    /**
     * @param Regencia $regencia
     * @param DateTime $data
     * @return ConteudoDesenvolvido|null
     * @throws Exception
     */
    public function buscarConteudo(Regencia $regencia, DateTime $data, $turno)
    {
        $usuario = $_SESSION['DB_id_usuario'];
        return $this->repository->scopeRegencia($regencia)->scopeData($data)->scopeTurno($turno)
            ->scopeUsuario($usuario)->first();
    }

    /**
     * @param ConteudoDesenvolvido $conteudoDesenvolvido
     * @return ConteudoDesenvolvido
     * @throws Exception
     */
    public function salvar(ConteudoDesenvolvido $conteudoDesenvolvido)
    {
        $conteudoDesenvolvidoRepository = new ConteudoDesenvolvidoRepository();
        return $conteudoDesenvolvidoRepository->salvar($conteudoDesenvolvido);
    }

    /**
     * @param stdClass $parametros
     * @return ConteudoDesenvolvido
     * @throws Exception
     */
    public function salvarFromRpc($parametros)
    {
        if (empty($parametros->codigoUsuario)) {
            throw new Exception("Informe o usuário.");
        }
        if (empty($parametros->regencia)) {
            throw new Exception("Informe o regência.");
        }
        if (empty($parametros->data)) {
            throw new Exception("Informe uma data.");
        }
        if (empty($parametros->conteudo)) {
            throw new Exception("Informe o conteúdo desenvolvido.");
        }

        $regencia = new Regencia($parametros->regencia);
        $data = new DateTime($parametros->data);
        $dataInicio = $regencia->getTurma()->getCalendario()->getDataInicio();
        $dataFim = $regencia->getTurma()->getCalendario()->getDataFinal();

        $turnosReferente = $regencia->getTurma()->getTurnoReferente();
        $turnoReferente = $turnosReferente[$parametros->turnoReferencia];

        if (!DBDate::dataEstaNoIntervalo(new DBDate($parametros->data), $dataInicio, $dataFim)) {
            throw new Exception(
                "A data deve estar no período do calendário escolar, entre {$dataInicio} e {$dataFim}."
            );
        }

        $conteudoDesenvolvido = new ConteudoDesenvolvido();
        $conteudoDesenvolvido->setUsuario(new UsuarioSistema($parametros->codigoUsuario));
        $conteudoDesenvolvido->setRegencia($regencia);
        $conteudoDesenvolvido->setData($data);
        $conteudoDesenvolvido->setCodigoTurmaTurnoReferente($turnoReferente->ed336_codigo);
        $conteudoDesenvolvido->setConteudo($parametros->conteudo);
        if (!empty($parametros->codigo)) {
            $conteudoDesenvolvido->setCodigo($parametros->codigo);
        }
        $conteudoDesenvolvido = $this->salvar($conteudoDesenvolvido);
        return $conteudoDesenvolvido;
    }

    /**
     * @param integer $codigo
     * @return bool
     * @throws Exception
     */
    public function excluir($codigo)
    {
        if (empty($codigo)) {
            throw new Exception("Informe o código do Conteúdo Desenvolvido.");
        }
        $conteudoDesenvolvido = ConteudoDesenvolvidoRegistry::get($codigo);
        $this->repository->excluir($conteudoDesenvolvido);
        return true;
    }

    /**
     * @param Regencia $regencia
     * @param $dataInicial
     * @param $dataFinal
     * @return ConteudoDesenvolvido[]
     * @throws Exception
     */
    public function buscarConteudoPeriodo(Regencia $regencia, $dataInicial, $dataFinal)
    {
        return $this->repository->scopeRegencia($regencia)->scopePeriodo($dataInicial, $dataFinal)->get();
    }
}
