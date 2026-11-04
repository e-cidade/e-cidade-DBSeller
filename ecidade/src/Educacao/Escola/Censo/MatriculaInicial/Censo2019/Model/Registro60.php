<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 03/05/2019
 * Time: 12:16
 */

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model;

use ECidade\Enum\Educacao\Secretaria\ComposicaoItinerarioFormativoIntegradoEnum;
use ECidade\Enum\Educacao\Secretaria\TipoItinerarioFormativoEnum;

/**
 * Class Registro60
 * @package ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model
 */
class Registro60
{
    const TIPO_REGISTRO = '60';
    protected $tipoRegistro = self::TIPO_REGISTRO;
    protected $codigoInepEscola;
    protected $codigoPessoa;
    protected $codigoInep;
    protected $codigoTurma;
    protected $codigoTurmaInep;
    protected $codigoMatricula;
    protected $etapaTurmaMultietapa;

    //Tipo do itinerário formativo
    protected $liguagensTecnlogias;
    protected $matematicaTecnlogias;
    protected $cienciasNaturezaTecnlogias;
    protected $cienciasHumanas;
    protected $formacaoTecnicaProfissional;
    protected $itenarioFormativoIntegrado;

    //Composição do itinerário formativo integrado
    protected $liguagensTecnlogiasIntegrado;
    protected $matematicaTecnlogiasIntegrado;
    protected $cienciasNaturezaTecnlogiasIntegrado;
    protected $cienciasHumanasIntegrado;
    protected $formacaoTecnicaProfissionalIntegrado;
    protected $cursoFormacaoTecnicaProfissionalIntegrado;
    protected $itenarioConcomitanteIntegrado;

    // Tipo de atendimento educacionalespecializado
    protected $desenvolvimentoFuncoesCognitivas;
    protected $desenvolvimentoVidaAutonoma;
    protected $enriquecimentoCurricular;
    protected $EnsinoInformaticaAcessivel;
    protected $ensinoLibras;
    protected $ensinoPortuguesaSegundaLingua;
    protected $ensinoCalculoSoroban;
    protected $ensinoSistemaBraille;
    protected $ensinoTecnicasOrientacaoMobilidade;
    protected $ensinoCAA;
    protected $ensinoRecursosOpticosNaoOpticos;

    protected $recebeEscolarizacaoEspecial;
    protected $transporteEscolarPublico;
    protected $responsavelTransporteEscolar;
// Tipo de veículo utilizado no transporte escolar público
    protected $bicicleta;
    protected $microonibus;
    protected $onibus;
    protected $tracaoAnimal;
    protected $vansKombis;
    protected $outro;
    protected $aquaviarioAte5;
    protected $aquaviarioEntre5A15;
    protected $aquaviarioEntre15A35;
    protected $aquaviarioAcima35;
    protected $areasItinerarioFormativo = [];
    protected $composicaoItinerarioIntegrado = [];

    protected $codigoCursoTecnico;

    /**
     * @return mixed
     */
    public function getCodigoCursoTecnico()
    {
        return $this->codigoCursoTecnico;
    }

    /**
     * @param mixed $codigoCursoTecnico
     * @return Registro60
     */
    public function setCodigoCursoTecnico($codigoCursoTecnico)
    {
        $this->codigoCursoTecnico = $codigoCursoTecnico;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAreasItinerarioFormativo()
    {
        return $this->areasItinerarioFormativo;
    }

    /**
     * @param mixed $areasItinerarioFormativo
     */
    public function setAreasItinerarioFormativo($areaItinerarioFormativo)
    {
        $enum = TipoItinerarioFormativoEnum::getAll();
        $this->areasItinerarioFormativo[$areaItinerarioFormativo] = $enum[$areaItinerarioFormativo];
        $this->setLiguagensTecnlogias();
        $this->setMatematicaTecnlogias();
        $this->setCienciasNaturezaTecnlogias();
        $this->setCienciasHumanas();
        $this->setFormacaoTecnicaProfissional();
        $this->setItenarioFormativoIntegrado();
    }

    public function getTipoRegistro()
    {
        return $this->tipoRegistro;
    }

    /**
     * @return mixed
     */
    public function getCodigoInepEscola()
    {
        return $this->codigoInepEscola;
    }

    /**
     * @param mixed $codigoInepEscola
     */
    public function setCodigoInepEscola($codigoInepEscola)
    {
        $this->codigoInepEscola = $codigoInepEscola;
    }

    /**
     * @return mixed
     */
    public function getCodigoPessoa()
    {
        return $this->codigoPessoa;
    }

    /**
     * @param mixed $codigoPessoa
     */
    public function setCodigoPessoa($codigoPessoa)
    {
        $this->codigoPessoa = $codigoPessoa;
    }

    /**
     * @return mixed
     */
    public function getCodigoInep()
    {
        return $this->codigoInep;
    }

    /**
     * @param mixed $codigoInep
     */
    public function setCodigoInep($codigoInep)
    {
        $this->codigoInep = $codigoInep;
    }

    /**
     * @return mixed
     */
    public function getCodigoTurma()
    {
        return $this->codigoTurma;
    }

    /**
     * @param mixed $codigoTurma
     */
    public function setCodigoTurma($codigoTurma)
    {
        $this->codigoTurma = $codigoTurma;
    }

    /**
     * @return mixed
     */
    public function getCodigoTurmaInep()
    {
        return $this->codigoTurmaInep;
    }

    /**
     * @param $codigoTurmaInep
     */
    public function setCodigoTurmaInep($codigoTurmaInep)
    {
        $this->codigoTurmaInep = $codigoTurmaInep;
    }

    /**
     * @return mixed
     */
    public function getCodigoMatricula()
    {
        return $this->codigoMatricula;
    }

    /**
     * @param $codigoMatricula
     */
    public function setCodigoMatricula($codigoMatricula)
    {
        $this->codigoMatricula = $codigoMatricula;
    }

    /**
     * @return mixed
     */
    public function getEtapaTurmaMultietapa()
    {
        return $this->etapaTurmaMultietapa;
    }

    /**
     * @param mixed $etapaTurmaMultietapa
     */
    public function setEtapaTurmaMultietapa($etapaTurmaMultietapa)
    {
        $this->etapaTurmaMultietapa = $etapaTurmaMultietapa;
    }

    /**
     * @return mixed
     */
    public function getLiguagensTecnlogias()
    {
        return $this->liguagensTecnlogias;
    }

    /**
     * @param mixed $liguagensTecnlogias
     */
    public function setLiguagensTecnlogias()
    {
        $areas = array_keys($this->getAreasItinerarioFormativo());
        $this->liguagensTecnlogias = 1;
        if (!in_array(TipoItinerarioFormativoEnum::LINGUAGEM_SUAS_TECNOLOGIAS, $areas)) {
            $this->liguagensTecnlogias = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getMatematicaTecnlogias()
    {
        return $this->matematicaTecnlogias;
    }

    /**
     * @param mixed $matematicaTecnlogias
     */
    public function setMatematicaTecnlogias()
    {
        $areas = array_keys($this->getAreasItinerarioFormativo());
        $this->matematicaTecnlogias = 1;
        if (!in_array(TipoItinerarioFormativoEnum::MATEMATICA_SUAS_TECNOLOGIAS, $areas)) {
            $this->matematicaTecnlogias  = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getCienciasNaturezaTecnlogias()
    {
        return $this->cienciasNaturezaTecnlogias;
    }

    /**
     * @param mixed $cienciasNaturezaTecnlogias
     */
    public function setCienciasNaturezaTecnlogias()
    {
        $areas = array_keys($this->getAreasItinerarioFormativo());
        $this->cienciasNaturezaTecnlogias = 1;
        if (!in_array(TipoItinerarioFormativoEnum::CIENCIAS_NATUREZA_SUAS_TECNOLOGIAS, $areas)) {
            $this->cienciasNaturezaTecnlogias = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getCienciasHumanas()
    {
        return $this->cienciasHumanas;
    }

    /**
     * @param mixed $cienciasHumanas
     */
    public function setCienciasHumanas()
    {
        $areas = array_keys($this->getAreasItinerarioFormativo());
        $this->cienciasHumanas = 1;
        if (!in_array(TipoItinerarioFormativoEnum::CIENCIAS_HUMANAS_SUAS_TECNOLOGIAS, $areas)) {
            $this->cienciasHumanas = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getFormacaoTecnicaProfissional()
    {
        return $this->formacaoTecnicaProfissional;
    }

    /**
     * @param mixed $formacaoTecnicaProfissional
     */
    public function setFormacaoTecnicaProfissional()
    {
        $areas = array_keys($this->getAreasItinerarioFormativo());
        $this->formacaoTecnicaProfissional = 1;
        if (!in_array(TipoItinerarioFormativoEnum::FORMACAO_TECNICA_PROFISSIONAL, $areas)) {
            $this->formacaoTecnicaProfissional = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getItenarioFormativoIntegrado()
    {
        return $this->itenarioFormativoIntegrado;
    }

    /**
     * @param mixed $itenarioFormativoIntegrado
     */
    public function setItenarioFormativoIntegrado()
    {
        $areas = array_keys($this->getAreasItinerarioFormativo());
        $this->itenarioFormativoIntegrado = 1;
        if (!in_array(TipoItinerarioFormativoEnum::ITINERARIO_FORMATIVO_INTEGRADO, $areas)) {
            $this->itenarioFormativoIntegrado = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getLiguagensTecnlogiasIntegrado()
    {
        return $this->liguagensTecnlogiasIntegrado;
    }

    /**
     * @param mixed $liguagensTecnlogiasIntegrado
     */
    public function setLiguagensTecnlogiasIntegrado()
    {

        $composicoes = array_keys($this->getComposicaoItinerarioIntegrado());
        $this->liguagensTecnlogiasIntegrado = 1;
        if (!in_array(ComposicaoItinerarioFormativoIntegradoEnum::LINGUAGEM_SUAS_TECNOLOGIAS, $composicoes)) {
            $this->liguagensTecnlogiasIntegrado = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getMatematicaTecnlogiasIntegrado()
    {
        return $this->matematicaTecnlogiasIntegrado;
    }

    /**
     * @param mixed $matematicaTecnlogiasIntegrado
     */
    public function setMatematicaTecnlogiasIntegrado()
    {
        $composicoes = array_keys($this->getComposicaoItinerarioIntegrado());
        $this->matematicaTecnlogiasIntegrado = 1;
        if (!in_array(ComposicaoItinerarioFormativoIntegradoEnum::MATEMATICA_SUAS_TECNOLOGIAS, $composicoes)) {
            $this->matematicaTecnlogiasIntegrado = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getCienciasNaturezaTecnlogiasIntegrado()
    {
        return $this->cienciasNaturezaTecnlogiasIntegrado;
    }

    /**
     * @param mixed $cienciasNaturezaTecnlogiasIntegrado
     */
    public function setCienciasNaturezaTecnlogiasIntegrado()
    {
        $composicoes = array_keys($this->getComposicaoItinerarioIntegrado());
        $this->cienciasNaturezaTecnlogiasIntegrado = 1;
        if (!in_array(ComposicaoItinerarioFormativoIntegradoEnum::CIENCIAS_NATUREZA_SUAS_TECNOLOGIAS, $composicoes)) {
            $this->cienciasNaturezaTecnlogiasIntegrado = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getCienciasHumanasIntegrado()
    {
        return $this->cienciasHumanasIntegrado;
    }

    /**
     * @param mixed $cienciasHumanasIntegrado
     */
    public function setCienciasHumanasIntegrado()
    {
        $composicoes = array_keys($this->getComposicaoItinerarioIntegrado());
        $this->cienciasHumanasIntegrado = 1;
        if (!in_array(ComposicaoItinerarioFormativoIntegradoEnum::CIENCIAS_HUMANAS_SUAS_TECNOLOGIAS, $composicoes)) {
            $this->cienciasHumanasIntegrado = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getFormacaoTecnicaProfissionalIntegrado()
    {
        return $this->formacaoTecnicaProfissionalIntegrado;
    }

    /**
     * @param mixed $formacaoTecnicaProfissionalIntegrado
     */
    public function setFormacaoTecnicaProfissionalIntegrado()
    {
        $composicoes = $this->getComposicaoItinerarioIntegrado();
        $this->formacaoTecnicaProfissionalIntegrado = 0;
        foreach ($composicoes as $composicao) {
            $codigos = array_keys($composicao['composicao']);
            if (in_array(ComposicaoItinerarioFormativoIntegradoEnum::FORMACAO_TECNICA_PROFISSIONAL, $codigos)) {
                $this->formacaoTecnicaProfissionalIntegrado = 1;
                if (!is_null($composicao['tipoCurso'])) {
                    $this->setCursoFormacaoTecnicaProfissionalIntegrado($composicao['tipoCurso']);
                }
                if (!is_null($composicao['isConcomitante'])) {
                    $this->setItenarioConcomitanteIntegrado($composicao['isConcomitante']);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCursoFormacaoTecnicaProfissionalIntegrado()
    {
        return $this->cursoFormacaoTecnicaProfissionalIntegrado;
    }

    /**
     * @param mixed $cursoFormacaoTecnicaProfissionalIntegrado
     */
    public function setCursoFormacaoTecnicaProfissionalIntegrado($tipoCurso)
    {
        $this->cursoFormacaoTecnicaProfissionalIntegrado = intval($tipoCurso);
    }

    /**
     * @return mixed
     */
    public function getItenarioConcomitanteIntegrado()
    {
        return $this->itenarioConcomitanteIntegrado;
    }

    /**
     * @param mixed $itenarioConcomitanteIntegrado
     */
    public function setItenarioConcomitanteIntegrado($isConcomitante)
    {
        $this->itenarioConcomitanteIntegrado = $isConcomitante ? 1: 0;
    }

    /**
     * @return mixed
     */
    public function getDesenvolvimentoFuncoesCognitivas()
    {
        return $this->desenvolvimentoFuncoesCognitivas;
    }

    /**
     * @param mixed $desenvolvimentoFuncoesCognitivas
     */
    public function setDesenvolvimentoFuncoesCognitivas($desenvolvimentoFuncoesCognitivas)
    {
        $this->desenvolvimentoFuncoesCognitivas = $desenvolvimentoFuncoesCognitivas;
    }

    /**
     * @return mixed
     */
    public function getDesenvolvimentoVidaAutonoma()
    {
        return $this->desenvolvimentoVidaAutonoma;
    }

    /**
     * @param mixed $desenvolvimentoVidaAutonoma
     */
    public function setDesenvolvimentoVidaAutonoma($desenvolvimentoVidaAutonoma)
    {
        $this->desenvolvimentoVidaAutonoma = $desenvolvimentoVidaAutonoma;
    }

    /**
     * @return mixed
     */
    public function getEnriquecimentoCurricular()
    {
        return $this->enriquecimentoCurricular;
    }

    /**
     * @param mixed $enriquecimentoCurricular
     */
    public function setEnriquecimentoCurricular($enriquecimentoCurricular)
    {
        $this->enriquecimentoCurricular = $enriquecimentoCurricular;
    }

    /**
     * @return mixed
     */
    public function getEnsinoInformaticaAcessivel()
    {
        return $this->EnsinoInformaticaAcessivel;
    }

    /**
     * @param mixed $EnsinoInformaticaAcessivel
     */
    public function setEnsinoInformaticaAcessivel($EnsinoInformaticaAcessivel)
    {
        $this->EnsinoInformaticaAcessivel = $EnsinoInformaticaAcessivel;
    }

    /**
     * @return mixed
     */
    public function getEnsinoLibras()
    {
        return $this->ensinoLibras;
    }

    /**
     * @param mixed $ensinoLibras
     */
    public function setEnsinoLibras($ensinoLibras)
    {
        $this->ensinoLibras = $ensinoLibras;
    }

    /**
     * @return mixed
     */
    public function getEnsinoPortuguesaSegundaLingua()
    {
        return $this->ensinoPortuguesaSegundaLingua;
    }

    /**
     * @param mixed $ensinoPortuguesaSegundaLingua
     */
    public function setEnsinoPortuguesaSegundaLingua($ensinoPortuguesaSegundaLingua)
    {
        $this->ensinoPortuguesaSegundaLingua = $ensinoPortuguesaSegundaLingua;
    }

    /**
     * @return mixed
     */
    public function getEnsinoCalculoSoroban()
    {
        return $this->ensinoCalculoSoroban;
    }

    /**
     * @param mixed $ensinoCalculoSoroban
     */
    public function setEnsinoCalculoSoroban($ensinoCalculoSoroban)
    {
        $this->ensinoCalculoSoroban = $ensinoCalculoSoroban;
    }

    /**
     * @return mixed
     */
    public function getEnsinoSistemaBraille()
    {
        return $this->ensinoSistemaBraille;
    }

    /**
     * @param mixed $ensinoSistemaBraille
     */
    public function setEnsinoSistemaBraille($ensinoSistemaBraille)
    {
        $this->ensinoSistemaBraille = $ensinoSistemaBraille;
    }

    /**
     * @return mixed
     */
    public function getEnsinoTecnicasOrientacaoMobilidade()
    {
        return $this->ensinoTecnicasOrientacaoMobilidade;
    }

    /**
     * @param mixed $ensinoTecnicasOrientacaoMobilidade
     */
    public function setEnsinoTecnicasOrientacaoMobilidade($ensinoTecnicasOrientacaoMobilidade)
    {
        $this->ensinoTecnicasOrientacaoMobilidade = $ensinoTecnicasOrientacaoMobilidade;
    }

    /**
     * @return mixed
     */
    public function getEnsinoCAA()
    {
        return $this->ensinoCAA;
    }

    /**
     * @param mixed $ensinoCAA
     */
    public function setEnsinoCAA($ensinoCAA)
    {
        $this->ensinoCAA = $ensinoCAA;
    }

    /**
     * @return mixed
     */
    public function getEnsinoRecursosOpticosNaoOpticos()
    {
        return $this->ensinoRecursosOpticosNaoOpticos;
    }

    /**
     * @param mixed $ensinoRecursosOpticosNaoOpticos
     */
    public function setEnsinoRecursosOpticosNaoOpticos($ensinoRecursosOpticosNaoOpticos)
    {
        $this->ensinoRecursosOpticosNaoOpticos = $ensinoRecursosOpticosNaoOpticos;
    }

    /**
     * @return mixed
     */
    public function getRecebeEscolarizacaoEspecial()
    {
        return $this->recebeEscolarizacaoEspecial;
    }

    /**
     * @param mixed $recebeEscolarizacaoEspecial
     */
    public function setRecebeEscolarizacaoEspecial($recebeEscolarizacaoEspecial)
    {
        $this->recebeEscolarizacaoEspecial = $recebeEscolarizacaoEspecial;
    }

    /**
     * @return mixed
     */
    public function getTransporteEscolarPublico()
    {
        return $this->transporteEscolarPublico;
    }

    /**
     * @param mixed $transporteEscolarPublico
     */
    public function setTransporteEscolarPublico($transporteEscolarPublico)
    {
        $this->transporteEscolarPublico = $transporteEscolarPublico;
    }

    /**
     * @return mixed
     */
    public function getResponsavelTransporteEscolar()
    {
        return $this->responsavelTransporteEscolar;
    }

    /**
     * @param mixed $responsavelTransporteEscolar
     */
    public function setResponsavelTransporteEscolar($responsavelTransporteEscolar)
    {
        $this->responsavelTransporteEscolar = $responsavelTransporteEscolar;
    }

    /**
     * @return mixed
     */
    public function getBicicleta()
    {
        return $this->bicicleta;
    }

    /**
     * @param mixed $bicicleta
     */
    public function setBicicleta($bicicleta)
    {
        $this->bicicleta = $bicicleta;
    }

    /**
     * @return mixed
     */
    public function getMicroonibus()
    {
        return $this->microonibus;
    }

    /**
     * @param mixed $microonibus
     */
    public function setMicroonibus($microonibus)
    {
        $this->microonibus = $microonibus;
    }

    /**
     * @return mixed
     */
    public function getOnibus()
    {
        return $this->onibus;
    }

    /**
     * @param mixed $onibus
     */
    public function setOnibus($onibus)
    {
        $this->onibus = $onibus;
    }

    /**
     * @return mixed
     */
    public function getTracaoAnimal()
    {
        return $this->tracaoAnimal;
    }

    /**
     * @param mixed $tracaoAnimal
     */
    public function setTracaoAnimal($tracaoAnimal)
    {
        $this->tracaoAnimal = $tracaoAnimal;
    }

    /**
     * @return mixed
     */
    public function getVansKombis()
    {
        return $this->vansKombis;
    }

    /**
     * @param mixed $vansKombis
     */
    public function setVansKombis($vansKombis)
    {
        $this->vansKombis = $vansKombis;
    }

    /**
     * @return mixed
     */
    public function getOutro()
    {
        return $this->outro;
    }

    /**
     * @param mixed $outro
     */
    public function setOutro($outro)
    {
        $this->outro = $outro;
    }

    /**
     * @return mixed
     */
    public function getAquaviarioAte5()
    {
        return $this->aquaviarioAte5;
    }

    /**
     * @param mixed $aquaviarioAte5
     */
    public function setAquaviarioAte5($aquaviarioAte5)
    {
        $this->aquaviarioAte5 = $aquaviarioAte5;
    }

    /**
     * @return mixed
     */
    public function getAquaviarioEntre5A15()
    {
        return $this->aquaviarioEntre5A15;
    }

    /**
     * @param mixed $aquaviarioEntre5A15
     */
    public function setAquaviarioEntre5A15($aquaviarioEntre5A15)
    {
        $this->aquaviarioEntre5A15 = $aquaviarioEntre5A15;
    }

    /**
     * @return mixed
     */
    public function getAquaviarioEntre15A35()
    {
        return $this->aquaviarioEntre15A35;
    }

    /**
     * @param mixed $aquaviarioEntre15A35
     */
    public function setAquaviarioEntre15A35($aquaviarioEntre15A35)
    {
        $this->aquaviarioEntre15A35 = $aquaviarioEntre15A35;
    }

    /**
     * @return mixed
     */
    public function getAquaviarioAcima35()
    {
        return $this->aquaviarioAcima35;
    }

    /**
     * @param mixed $aquaviarioAcima35
     */
    public function setAquaviarioAcima35($aquaviarioAcima35)
    {
        $this->aquaviarioAcima35 = $aquaviarioAcima35;
    }

    public function toArray()
    {
        return array(
            "tipoRegistro" => $this->tipoRegistro,
            "codigoInepEscola" => $this->getCodigoInepEscola(),
            "codigoPessoa" => $this->getCodigoPessoa(),
            "codigoInep" => $this->getCodigoInep(),
            "codigoTurma" => $this->getCodigoTurma(),
            "codigoTurmaInep" => $this->getCodigoTurmaInep(),
            "codigoMatricula" => $this->getCodigoMatricula(),
            "etapaTurmaMultietapa" => $this->getEtapaTurmaMultietapa(),
            "liguagensTecnlogias" => $this->getLiguagensTecnlogias(),
            "matematicaTecnlogias" => $this->getMatematicaTecnlogias(),
            "cienciasNaturezaTecnlogias" => $this->getCienciasNaturezaTecnlogias(),
            "cienciasHumanas" => $this->getCienciasHumanas(),
            "formacaoTecnicaProfissional" => $this->getFormacaoTecnicaProfissional(),
            "codigoCursoTecnico" => $this->getCodigoCursoTecnico(),
            "itenarioFormativoIntegrado" => $this->getItenarioFormativoIntegrado(),
            "liguagensTecnlogiasIntegrado" => $this->getLiguagensTecnlogiasIntegrado(),
            "matematicaTecnlogiasIntegrado" => $this->getMatematicaTecnlogiasIntegrado(),
            "cienciasNaturezaTecnlogiasIntegrado" => $this->getCienciasNaturezaTecnlogiasIntegrado(),
            "cienciasHumanasIntegrado" => $this->getCienciasHumanasIntegrado(),
            "formacaoTecnicaProfissionalIntegrado" => $this->getFormacaoTecnicaProfissionalIntegrado(),
            "cursoFormacaoTecnicaProfissionalIntegrado" => $this->getcursoFormacaoTecnicaProfissionalIntegrado(),
            "itenarioConcomitanteIntegrado" => $this->getItenarioConcomitanteIntegrado(),
            "desenvolvimentoFuncoesCognitivas" => $this->getDesenvolvimentoFuncoesCognitivas(),
            "desenvolvimentoVidaAutonoma" => $this->getDesenvolvimentoVidaAutonoma(),
            "enriquecimentoCurricular" => $this->getEnriquecimentoCurricular(),
            "EnsinoInformaticaAcessivel" => $this->getEnsinoInformaticaAcessivel(),
            "ensinoLibras" => $this->getEnsinoLibras(),
            "ensinoPortuguesaSegundaLingua" => $this->getEnsinoPortuguesaSegundaLingua(),
            "ensinoCalculoSoroban" => $this->getEnsinoCalculoSoroban(),
            "ensinoSistemaBraille" => $this->getEnsinoSistemaBraille(),
            "ensinoTecnicasOrientacaoMobilidade" => $this->getEnsinoTecnicasOrientacaoMobilidade(),
            "ensinoCAA" => $this->getEnsinoCAA(),
            "ensinoRecursosOpticosNaoOpticos" => $this->getEnsinoRecursosOpticosNaoOpticos(),
            "recebeEscolarizacaoEspecial" => $this->getRecebeEscolarizacaoEspecial(),
            "transporteEscolarPublico" => $this->getTransporteEscolarPublico(),
            "responsavelTransporteEscolar" => $this->getResponsavelTransporteEscolar(),
            "bicicleta" => $this->getBicicleta(),
            "microonibus" => $this->getMicroonibus(),
            "onibus" => $this->getOnibus(),
            "tracaoAnimal" => $this->getTracaoAnimal(),
            "vansKombis" => $this->getVansKombis(),
            "outro" => $this->getOutro(),
            "aquaviárioAte5" => $this->getAquaviarioAte5(),
            "aquaviárioEntre5A15" => $this->getAquaviarioEntre5A15(),
            "aquaviárioEntre15A35" => $this->getAquaviarioEntre15A35(),
            "aquaviárioAcima35" => $this->getAquaviarioAcima35(),
        );
    }

    /**
     * @return array
     */
    public function getComposicaoItinerarioIntegrado()
    {
        return $this->composicaoItinerarioIntegrado;
    }

    /**
     * @param array $composicaoItinerarioIntegrado
     */
    public function setComposicaoItinerarioIntegrado($tipoBase)
    {
        $composicaoItIntegrado = json_decode($tipoBase['ed182_compos_itinerario_integrado']);
        $enum = ComposicaoItinerarioFormativoIntegradoEnum::getAll();

        foreach ($composicaoItIntegrado as $composicao) {
            $this->composicaoItinerarioIntegrado[$composicao]['composicao'] = [$composicao => $enum[$composicao]];
            if (intval($composicao) == ComposicaoItinerarioFormativoIntegradoEnum::FORMACAO_TECNICA_PROFISSIONAL) {
                $tipoCurso = $tipoBase['ed182_tipo_curso_itinerario_tec_prof'];
                $isConcomitante = $tipoBase['ed182_itinerario_concomitante'] == true;
                $this->composicaoItinerarioIntegrado[$composicao]['tipoCurso'] = $tipoCurso;
                $this->composicaoItinerarioIntegrado[$composicao]['isConcomitante'] = $isConcomitante;
            }
        }
        $this->setLiguagensTecnlogiasIntegrado();
        $this->setMatematicaTecnlogiasIntegrado();
        $this->setCienciasNaturezaTecnlogiasIntegrado();
        $this->setCienciasHumanasIntegrado();
        $this->setFormacaoTecnicaProfissionalIntegrado();
    }
}
