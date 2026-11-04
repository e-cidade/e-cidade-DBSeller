<?php

namespace App\Domain\Configuracao\Usuario\Controller;

use App\Domain\Configuracao\Usuario\Requests\LoginRequest;
use App\Domain\Configuracao\Usuario\Requests\UnblockRequest;
use App\Domain\Configuracao\Usuario\Services\AuthService;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Core\Exceptions\SecuriImageException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @param AuthService $service
     * @param LoginRequest $request
     * @return DBJsonResponse|JsonResponse
     * @throws \Exception
     */
    public function authenticate(AuthService $service, LoginRequest $request)
    {
        try {
            $token = $service->authenticate($request);

            return new DBJsonResponse($token);
        } catch (SecuriImageException $e) {
            return response()->json([
                'code' => 'E_CAPTCHA',
                'error' => true,
                'message' => mb_convert_encoding($e->getMessage(), 'UTF-8', 'ISO-8859-1')
            ], $e->getCode());
        }
    }

    /**
     * @param AuthService $service
     * @param UnblockRequest $request
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function unblock(AuthService $service, UnblockRequest $request)
    {
        $token = $service->unblock($request);

        return new DBJsonResponse($token);
    }

    /**
     * @param AuthService $service
     * @param Request $request
     * @return ResponseFactory|Application|Response
     */
    public function logout(AuthService $service, Request $request)
    {
        $service->logout($request->user());

        return response('dead');
    }

    /**
     * @param AuthService $service
     * @return ResponseFactory|Application|Response
     */
    public function kill(AuthService $service)
    {
        $service->kill();

        return response('dead');
    }
}
