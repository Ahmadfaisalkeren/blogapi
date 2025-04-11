<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $result = $this->authService->login($credentials);

        if ($result['status'] === 'error') {
            return response()->json([
                'error' => $result['message']
            ], 401);
        }

        return response()->json($result, 200);
    }

    public function user($id)
    {
        $user = $this->authService->getUser($id);

        return response()->json([
            'status' => 200,
            'message' => 'User Fetched Successfully',
            'user' => $user,
        ]);
    }
}
