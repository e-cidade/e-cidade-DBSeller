<?php

namespace App\Domain\Configuracao\Job\Controller;

use App\Domain\Configuracao\Job\Model\Job;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query();
        $jsonData = stripslashes(html_entity_decode($request->input('search')));
        $search = json_decode($jsonData, true);

        if (!empty($search)) {
            if (!empty($search["data_inicio"])) {
                $search["data_inicio"] = explode("T", $search["data_inicio"])[0];
                $query->whereRaw("to_timestamp(created_at)::DATE >= ?", "{$search["data_inicio"]}");
            }

            if (!empty($search["data_fim"])) {
                $search["data_fim"] = explode("T", $search["data_fim"])[0];
                $query->whereRaw("to_timestamp(created_at)::DATE <= ?", "{$search["data_fim"]}");
            }

            if (!empty($search["id"])) {
                $query->where("id", "=", $search["id"]);
            }

            if (!empty($search["entity"])) {
                $query->where(
                    "payload",
                    "ilike",
                    "%{$search["entity"]}%"
                );
            }
        }

        $sortField = $request->input('sortField', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');

        if (!in_array($sortField, [
            'id', 'uuid', 'class', 'created_at', 'available_at', 'reserved_at', 'attempts', 'queue'
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

    public function destroy(Job $failed)
    {
        $failed->delete();

        return back();
    }

    public function destroyMany(Request $request)
    {
        $this->validate($request, [
            'failed_ids' => 'required',
        ]);

        Job::query()->whereIn("id", $request->get("failed_ids"))->delete();

        return response(["message" => "Processando..."], 200);
    }
}
