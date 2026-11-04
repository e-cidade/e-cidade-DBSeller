<?php

namespace App\Domain\Configuracao\Plugin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;

class PluginController extends Controller
{
    public function getConfig(Request $request)
    {
        if ($request->plugin) {
            $sPathConfig = "plugins/{$request->plugin}/config.ini";
            if (!is_file($sPathConfig)) {
                return new DBJsonResponse(null);
            }
            $aConfiguracao = parse_ini_file($sPathConfig);
            return new DBJsonResponse($aConfiguracao);
        } else {
            return new DBJsonResponse(null);
        }
    }
}
