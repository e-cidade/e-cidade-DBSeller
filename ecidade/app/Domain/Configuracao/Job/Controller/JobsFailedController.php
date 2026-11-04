<?php

namespace App\Domain\Configuracao\Job\Controller;

use App\Domain\Configuracao\Job\Model\JobFailed;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class JobsFailedController extends Controller
{
    public function index(Request $request)
    {
        $query = JobFailed::query();
        $jsonData = stripslashes(html_entity_decode($request->input('search')));
        $search = json_decode($jsonData, true);

        if (!empty($search)) {
            if (!empty($search["data_inicio"])) {
                $search["data_inicio"] = explode("T", $search["data_inicio"])[0];
                $query->whereRaw(
                    "failed_at::DATE >= ?",
                    "{$search["data_inicio"]}"
                );
            }

            if (!empty($search["data_fim"])) {
                $search["data_fim"] = explode("T", $search["data_fim"])[0];
                $query->whereRaw(
                    "failed_at::DATE <= ?",
                    "{$search["data_fim"]}"
                );
            }

            if (!empty($search["id"])) {
                $query->where(
                    "id",
                    "=",
                    $search["id"]
                );
            }

            if (!empty($search["entity"])) {
                $query->where(
                    "payload",
                    "ilike",
                    "%{$search["entity"]}%"
                );
            }

            if (!empty($search["exception"])) {
                $query->where(
                    "exception",
                    "ilike",
                    "%{$search["exception"]}%"
                );
            }
        }

        $sortField = $request->input('sortField', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');

        if (!in_array($sortField, [
            'id', 'uuid', 'class', 'failed_at', 'payload', 'connection', 'exception', 'queue'
        ])) {
            $sortField = 'id';
        }

        if ($sortField === 'uuid') {
            $query->orderByRaw("payload_to_json(payload)::jsonb->>'uuid' " . ($sortOrder === '1' ? 'asc' : 'desc'));
        } elseif ($sortField === 'class') {
            $query->orderByRaw(
                "payload_to_json(payload)::jsonb->>'displayName' "
                . ($sortOrder === '1' ? 'asc' : 'desc')
            );
        } else {
            $query->orderBy($sortField, $sortOrder === '1' ? 'asc' : 'desc');
        }

        $data = $query->paginate(
            (int)$request->get("perPage", 10)
        );

        return new DBJsonResponse($data);
    }


    public function destroy(JobFailed $failed)
    {
        $failed->delete();
        return back();
    }

    public function update(JobFailed $failed)
    {
        if ($failed->connection !== "database") {
            $failed->connection = "database";
            $failed->update();
        }
        Artisan::call('queue:retry', ['id' => [$failed->id]]);
        return back();
    }

    public function retryMany(Request $request)
    {
        $this->validate($request, [
            'failed_ids' => 'required|array',
        ]);

        JobFailed::query()
            ->whereIn("id", $request->get("failed_ids"))
            ->update(["connection" => "database"]);

        try {
            Artisan::call('queue:retry', ['id' => $request->get("failed_ids")]);
        } catch (\Exception $ex) {
            return response(["message" => $ex->getMessage()], 400);
        }

        return response(["message" => "Processando..."], 200);
    }

    public function destroyMany(Request $request)
    {
        $this->validate($request, [
            'failed_ids' => 'required',
        ]);

        JobFailed::query()->whereIn("id", $request->get("failed_ids"))->delete();
        return response(["message" => "Processando..."], 200);
    }
}
