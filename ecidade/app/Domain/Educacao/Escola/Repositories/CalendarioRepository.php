<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Repositories\ProfissionalEscolaRepository;
use App\Domain\Educacao\Escola\Models\Calendario;
use App\Domain\Educacao\Escola\Models\Turma;
use App\Domain\Educacao\Escola\Resources\CalendarioResource;
use App\Domain\Educacao\Escola\Resources\TurmaResource;
use App\Domain\Educacao\Escola\Models\Views\TurmasRegenteView;
use App\Domain\Educacao\Escola\Resources\TurmasRegentesViewResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarioRepository extends BaseRepository
{
    protected $modelClass = Calendario::class;

    public function buscarCalendariosAtivosEscola($escola)
    {
        $calendarios = $this->newQuery()->whereHas('calendarioEscola', function ($query) use ($escola) {
            $query->where('ed38_i_escola', $escola);
        })->apenasAtivos()->orderBy('ed52_i_ano', 'desc')->get()->map(function ($calendario) {
             return CalendarioResource::toResponse($calendario);
        });

        return $calendarios;
    }
}
