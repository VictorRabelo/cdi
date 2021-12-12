<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Repositories\Eloquent\Auth\AuthRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }
    
    public function login(AuthRequest $request)
    {

        $credentials = $request->all();
        
        $res = $this->authRepository->logar($credentials);

        if (!$res) {
            return response()->json(['message' => 'Usuário ou senha invalido!'], 401);
        }

        return response()->json($res, 200);
    }
    
    public function logout(Request $request)
    {
        $res = $this->authRepository->logout($request);

        if (!$res) {
            return response()->json(['message' => 'Falha ao deslogar!'], 500);
        }
        return response()->json(['message' => 'Deslogado com sucesso'], 200);
    }

    public function me()
    {
        $res = $this->authRepository->me();
        
        if(!$res){
            return response()->json(['message' => 'Falha ao processar'], 500);
        }

        return response()->json($res, 200);
    }
    
    public function alterSenha(Request $request)
    {
        $res = $this->authRepository->alterSenha($request);
        
        if(!$res){
            return response()->json(['message' => 'Falha ao processar'], 500);
        }
        
        return response()->json($res, 200);
    }
}
