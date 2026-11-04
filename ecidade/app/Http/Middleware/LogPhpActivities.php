<?php

namespace App\Http\Middleware;

use App\QueryLogger;
use Closure;

class LogPhpActivities
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (\TraceLog::getInstance()->isActive()) {
            $oTraceLog = \TraceLog::getInstance();
            $messageTrace = $response->status() === 200 ? "[INFO -  "
                : "[ERROR - ";
            $messageTrace .= "Request started: ".$request->fullUrl()." \n";
            $messageTrace .= $oTraceLog->getFormatedBacktrace(
                debug_backtrace()
            );
            $oTraceLog->write($messageTrace);
            $queries = QueryLogger::getQueries();
            foreach ($queries as $query) {
                $oTraceLog->makeMessage($query["sql"], false);
            }

            $queriesErro = QueryLogger::getQueriesError();
            foreach ($queriesErro as $query) {
                $oTraceLog->makeMessage($query["sql"], true);
            }
            $messageTrace .= "\nResponse status: ".$response->status()."\n";
            $oTraceLog->write($messageTrace);

            return $response;
        }

        return $response;
    }
}
