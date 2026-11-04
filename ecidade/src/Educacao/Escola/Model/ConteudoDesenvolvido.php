<?php


namespace ECidade\Educacao\Escola\Model;

use App\Domain\Educacao\Escola\Models\TurmaTurnoReferente;
use App\Domain\Educacao\Secretaria\Models\TiposIntrumentosAvaliativos;
use App\Domain\Educacao\Secretaria\Resources\TiposInstrumentosAvaliativosResource;
use DateTime;
use ECidade\Educacao\Escola\Registry\ConteudoDesenvolvidoRegistry;
use Exception;
use Regencia;
use RegenciaRepository;
use UsuarioSistema;
use UsuarioSistemaRepository;

/**
 * Class ConteudoDesenvolvido
 * @package ECidade\Educacao\Escola\Model
 */
class ConteudoDesenvolvido
{
    /**
     * @var integer
     */
    private $codigo;
    /**
     * @var Regencia
     */
    private $regencia;
    /**
     * @var UsuarioSistema
     */
    private $usuario;
    /**
     * @var DateTime
     */
    private $data;
    /**
     * @var integer
     */
    private $codigoTurmaTurnoReferente;
    /**
     * @var string
     */
    private $conteudo;
    /**
     * @var HabilidadeDesenvolvida[]
     */
    private $habilidades = [];

    private $tipoInstrumentoAvaliativo;

    private $aulasDadas;

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     * @return ConteudoDesenvolvido
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return Regencia
     */
    public function getRegencia()
    {
        return $this->regencia;
    }

    /**
     * @param Regencia $regencia
     * @return ConteudoDesenvolvido
     */
    public function setRegencia(Regencia $regencia)
    {
        $this->regencia = $regencia;
        return $this;
    }

    /**
     * @return int
     */
    public function getTurno()
    {
        return $this->codigoTurmaTurnoReferente;
    }

    /**
     * @param int $turno
     * @return ConteudoDesenvolvido
     */
    public function setCodigoTurmaTurnoReferente($codigoTurmaTurnoReferente)
    {
        $this->codigoTurmaTurnoReferente = $codigoTurmaTurnoReferente;
        return $this;
    }

    /**
     * @return UsuarioSistema
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioSistema $usuario
     * @return ConteudoDesenvolvido
     */
    public function setUsuario(UsuarioSistema $usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     * @return ConteudoDesenvolvido
     */
    public function setData(DateTime $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getConteudo()
    {
        return $this->conteudo;
    }

    /**
     * @param string $conteudo
     * @return ConteudoDesenvolvido
     */
    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
        return $this;
    }

    /**
     * @return integer
     */
    public function getAulasDadas()
    {
        return $this->aulasDadas;
    }

    /**
     * @param integer $aulasDadas
     * @return ConteudoDesenvolvido
     */
    public function setAulasDadas($aulasDadas)
    {
        $this->aulasDadas = $aulasDadas;
        return $this;
    }

    /**
     * @param array $state
     * @return ConteudoDesenvolvido
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $self = new self();
        if (array_key_exists('ed155_codigo', $state)) {
            $self->setCodigo($state['ed155_codigo']);
        }
        if (array_key_exists('ed155_regencia', $state)) {
            $self->setRegencia(RegenciaRepository::getRegenciaByCodigo($state['ed155_regencia']));
        }
        if (array_key_exists('ed155_db_usuarios', $state)) {
            $self->setUsuario(UsuarioSistemaRepository::getPorCodigo($state['ed155_db_usuarios']));
        }
        if (array_key_exists('ed155_turmaturnoreferente', $state)) {
            $self->setCodigoTurmaTurnoReferente($state['ed155_turmaturnoreferente']);
        }
        if (array_key_exists('ed155_data', $state)) {
            $self->setData(new DateTime($state['ed155_data']));
        }
        if (array_key_exists('ed155_tipo_instrumento_avaliativo', $state)) {
            $tipo = is_null($state['ed155_tipo_instrumento_avaliativo']) ?
                null :
                TiposInstrumentosAvaliativosResource::toResponse(
                    TiposIntrumentosAvaliativos::find($state['ed155_tipo_instrumento_avaliativo'])
                );
            $self->setTipoInstrumentoAvaliativo($tipo);
        }
        if (array_key_exists('ed155_conteudo', $state)) {
            $self->setConteudo($state['ed155_conteudo']);
        }
        if (array_key_exists('ed155_aulas_dadas', $state)) {
            $self->setAulasDadas($state['ed155_aulas_dadas']);
        }
        ConteudoDesenvolvidoRegistry::set($self);

        return $self;
    }

    /**
     * @param HabilidadeDesenvolvida $habilidade
     */
    public function addHabilidade(HabilidadeDesenvolvida $habilidade)
    {
        $this->habilidades[] = $habilidade;
    }

    /**
     * @return HabilidadeDesenvolvida[]
     */
    public function getHabilidades()
    {
        return $this->habilidades;
    }

    /**
     * @return mixed
     */
    public function getTipoInstrumentoAvaliativo()
    {
        return $this->tipoInstrumentoAvaliativo;
    }

    /**
     * @param mixed $tipoInstrumentoAvaliativo
     * @return ConteudoDesenvolvido
     */
    public function setTipoInstrumentoAvaliativo($tipoInstrumentoAvaliativo)
    {
        $this->tipoInstrumentoAvaliativo = $tipoInstrumentoAvaliativo;
    }
}
