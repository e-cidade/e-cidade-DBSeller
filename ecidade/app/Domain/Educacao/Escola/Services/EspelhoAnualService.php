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

namespace App\Domain\Educacao\Escola\Services;

use Etapa;
use Exception;
use Turma;

class EspelhoAnualService
{
    const MODELO_GERAL = 1;
    const MODELO_POR_DISCIPLINA = 2;

    /**
     * @var Turma
     */
    private $turma;

    /**
     * @var Etapa
     */
    private $etapa;
    /**
     * @var int
     */
    private $modelo;

    /**
     * @param Turma $turma
     * @param Etapa $etapa
     * @param int $modelo
     */
    public function __construct(Turma $turma, Etapa $etapa, $modelo = self::MODELO_GERAL)
    {
        $this->turma = $turma;
        $this->etapa = $etapa;
        $this->modelo = $modelo;
    }

    /**
     * @throws Exception
     */
    public function getDados($regenciasFiltrar = [])
    {
        $dadosRelatorio = (object)[
            "modelo" => $this->modelo,
            "turma" => $this->turma,
            "etapa" => $this->etapa,
            "matriculas" => [],
            "disciplinas" => [],
            "elementos" => []
        ];

        $regencias = $this->turma->getDisciplinasPorEtapa($this->etapa);
        if ($this->modelo == self::MODELO_POR_DISCIPLINA && count($regenciasFiltrar) > 0) {
            $regencias = array_filter($regencias, function ($regencia) use ($regenciasFiltrar) {
                return in_array($regencia->getCodigo(), $regenciasFiltrar);
            });
        }
        $regencias = array_values($regencias);

        $matriculas = $this->turma->getAlunosMatriculadosNaTurmaPorSerie($this->etapa);
        $dadosRelatorio->matriculas = $matriculas;

        foreach ($regencias as $regencia) {
            $disciplina = (object)[
                "regencia" => $regencia,
                "elementosAvaliacoes" => $regencia->getProcedimentoAvaliacao()->getElementos()
            ];
            $dadosRelatorio->elementos = $regencia->getProcedimentoAvaliacao()->getElementos();
            $dadosRelatorio->disciplinas[] = $disciplina;
        }

        return $dadosRelatorio;
    }
}
