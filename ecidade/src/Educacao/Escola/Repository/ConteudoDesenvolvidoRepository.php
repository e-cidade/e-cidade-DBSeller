<?php


namespace ECidade\Educacao\Escola\Repository;

use cl_diario_classe_bncc;
use DateTime;
use ECidade\Educacao\Escola\Model\ConteudoDesenvolvido;
use ECidade\Educacao\Escola\Service\HabilidadeDesenvolvidaService;
use Exception;
use Regencia;

/**
 * Class ConteudoDesenvolvidoRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class ConteudoDesenvolvidoRepository extends Repository
{
    /**
     * @param $key
     * @return ConteudoDesenvolvido
     * @throws Exception
     */
    public static function find($key)
    {
        $dao = new cl_diario_classe_bncc;
        $sql = $dao->sql_query_file($key);
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar conteúdo desenvolvido.");
        }

        return ConteudoDesenvolvido::fromState(pg_fetch_array($rs));
    }

    /**
     * @return ConteudoDesenvolvido[]
     * @throws Exception
     */
    public function get()
    {
        $dao = new cl_diario_classe_bncc;
        $sql = $dao->sql_query(
            null,
            'diario_classe_bncc.*',
            'ed155_data',
            implode(' and ', $this->scopes)
        );
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar conteúdo desenvolvido.");
        }

        $conteudos = [];
        while ($state = pg_fetch_array($rs)) {
            $conteudos[] = ConteudoDesenvolvido::fromState($state);
        }

        foreach ($conteudos as $conteudo) {
            $habilidadeDesenvolvidaService = new HabilidadeDesenvolvidaService();
            $habilidadesDesenvolvidas = $habilidadeDesenvolvidaService->getHabilidadesConteudo($conteudo);

            foreach ($habilidadesDesenvolvidas as $habilidadeDesenvolvida) {
                $conteudo->addHabilidade($habilidadeDesenvolvida);
            }
        }

        return $conteudos;
    }

    /**
     * @param ConteudoDesenvolvido $conteudoDesenvolvido
     * @return ConteudoDesenvolvido
     * @throws Exception
     */
    public function salvar(ConteudoDesenvolvido $conteudoDesenvolvido)
    {
        $dao = new cl_diario_classe_bncc;
        $dao->ed155_codigo = $conteudoDesenvolvido->getCodigo();
        $dao->ed155_regencia = $conteudoDesenvolvido->getRegencia()->getCodigo();
        $dao->ed155_db_usuarios = $conteudoDesenvolvido->getUsuario()->getCodigo();
        $dao->ed155_data = $conteudoDesenvolvido->getData()->format('Y-m-d');
        $dao->ed155_turmaturnoreferente = $conteudoDesenvolvido->getTurno();
        $dao->ed155_conteudo = pg_escape_string(trim($conteudoDesenvolvido->getConteudo()));

        if (empty($dao->ed155_codigo)) {
            $dao->incluir(null);
        } else {
            $dao->alterar($conteudoDesenvolvido->getCodigo());
        }
        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao salvar conteúdo desenvolvido.");
        }

        $conteudoDesenvolvido->setCodigo($dao->ed155_codigo);
        return $conteudoDesenvolvido;
    }

    /**
     * @param DateTime $data
     * @return $this
     */
    public function scopeData(DateTime $data)
    {
        $this->scopes['data'] = "ed155_data = '{$data->format('Y-m-d')}'";
        return $this;
    }

    /**
     * @param Regencia $regencia
     * @return $this
     */
    public function scopeRegencia(Regencia $regencia)
    {
        $this->scopes['regencia'] = "ed155_regencia = {$regencia->getCodigo()}";
        return $this;
    }

    /**
     * @param int $turno
     * @return $this
     */
    public function scopeTurno($turno)
    {
        $this->scopes['turno'] = "ed155_turmaturnoreferente = $turno";
        if (empty($turno)) {
            $this->scopes['turno'] = "ed155_turmaturnoreferente is null";
        }
        return $this;
    }

    /**
     * @param int $turno
     * @return $this
     */
    public function scopeUsuario($usuario)
    {
        $this->scopes['usuario'] = "ed155_db_usuarios = {$usuario}";
        return $this;
    }
    /**
     * @return ConteudoDesenvolvido|null
     * @throws Exception
     */
    public function first()
    {

        $conteudos = $this->get();
        if (empty($conteudos)) {
            return null;
        }

        return $conteudos[0];
    }

    /**
     * @param ConteudoDesenvolvido $conteudoDesenvolvido
     * @return bool
     * @throws Exception
     */
    public function excluir(ConteudoDesenvolvido $conteudoDesenvolvido)
    {
        $dao = new cl_diario_classe_bncc;
        $dao->ed155_codigo = $conteudoDesenvolvido->getCodigo();

        $dao->excluir($dao->ed155_codigo);

        if ($dao->erro_status == 0) {
            throw new Exception("Erro ao excluir conteúdo desenvolvido.");
        }
        return true;
    }

    public function scopePeriodo($dataInicial, $dataFinal)
    {
        $this->scopes['periodo'] = "ed155_data between '{$dataInicial}' and '{$dataFinal}'";
        return $this;
    }
}
