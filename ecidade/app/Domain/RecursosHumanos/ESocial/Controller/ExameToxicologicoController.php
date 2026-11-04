<?php



namespace App\Domain\RecursosHumanos\ESocial\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\ESocial\Models\ExameToxicologico;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use InstituicaoRepository;

class ExameToxicologicoController extends Controller
{
    /**
     * Insere ou atualiza um exame toxicológico.
     */
    public function store(Request $request)
    {
        // Validação dos dados recebidos
        $this->validate($request, [
            'eso41_sequencial'    => 'nullable|integer|exists:exametoxicologico,eso41_sequencial',
            'eso41_cpftrab'       => 'required|string|max:11',
            'eso41_matricula'     => 'required|integer',
            'eso41_dtexame'       => 'nullable|date',
            'eso41_cnpjlab'       => 'nullable|string|max:14',
            'eso41_nmmed'         => 'nullable|string|max:70',
            'eso41_nrcrm'         => 'nullable|string|max:10',
            'eso41_ufcrm'         => 'nullable|string|max:2',
            'eso41_instit'        => 'required|integer',
        ]);



        $attributes = $request->only([
            'eso41_sequencial',
            'eso41_cpftrab',
            'eso41_matricula',
            'eso41_dtexame',
            'eso41_cnpjlab',
            'eso41_nmmed',
            'eso41_nrcrm',
            'eso41_ufcrm',
            'eso41_instit'
        ]);

        if (isset($request['eso41_sequencial']) & !empty($attributes['eso41_sequencial'])) {
            $exame = ExameToxicologico::findOrFail($attributes['eso41_sequencial']);
            $exame->update($attributes);


            return new DBJsonResponse([
                'message' => 'Exame toxicológico atualizado com sucesso.',
                'data' => $exame
            ], 200);
        } else {
            // Criação de um novo registro
            $exame = ExameToxicologico::create($attributes);
            $exame->eso41_codseqexame = 'AA' . str_pad($exame->eso41_sequencial, 9, '0', STR_PAD_LEFT);
            $exame->save();

            return new DBJsonResponse([
                'message' => 'Exame toxicológico criado com sucesso.',
                'data' => $exame
            ], 200);
        }
    }



    public function index()
    {
        $instituicao = InstituicaoRepository::getInstituicaoSessao();
        $exames = ExameToxicologico::join('rhpessoal', 'rh01_regist', '=', 'eso41_matricula')
            ->join('protocolo.cgm', 'cgm.z01_numcgm', '=', 'rhpessoal.rh01_numcgm')
            ->where('eso41_instit', $instituicao->getSequencial())
        ->get(['exametoxicologico.*','cgm.z01_nome']);
        return new DBJsonResponse($exames, 200);
    }
}
