<?php
namespace App\Domain\Patrimonial\Material\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Material\Services\ImportarPlanilhaService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class ImplantacaoSaldoPlanilhaController extends Controller
{
    /**
     * @param Request $request
     * @param ImportarPlanilhaService $service
     * @return DBJsonResponse|void
     * @throws Exception
     */
    public function importar(Request $request, ImportarPlanilhaService $service)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');

            $csv = file_get_contents($file->path());
            $csv = trim(str_replace(["\r\n", "\r"], "\n", $csv));

            $rows = explode(PHP_EOL, $csv);
            $headers = explode('|', array_shift($rows));

            $dados = [];

            foreach ($rows as $row) {
                $rowValues = explode('|', $row);
                $dados[] = (object)array_combine($headers, $rowValues);
            }

            if (empty($dados) || count($dados) == 0) {
                throw new Exception("Planilha inválida ou vazia");
            }

            $date = Carbon::createFromFormat('D M d Y H:i:s e+', $request->date)->format('Y-m-d');
            $currentDate = Carbon::now();

            if (Carbon::parse($date)->greaterThan($currentDate)) {
                throw new Exception("Data informada Inválida");
            }

            $response = $service->incluir($dados, $date);

            return new DBJsonResponse([], $response);
        }
    }
}
