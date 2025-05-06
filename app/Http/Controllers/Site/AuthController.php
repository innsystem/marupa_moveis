<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function post(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::guard('web')->user();
            $user->load('group');

            $user_group = $user->group->name;

            switch (Str::lower($user_group)) {
                case 'admin':
                    return response()->json(['title' => 'Acesso Autorizado!', 'href' => '/admin'], 200);
                case 'developer':
                    return response()->json(['title' => 'Acesso Autorizado!', 'href' => '/admin'], 200);
                case 'user':
                    return response()->json(['title' => 'Acesso Autorizado User!', 'href' => '/user'], 200);
                default:
                    Auth::logout();
                    return redirect()->route('login')->withErrors('Tipo de usuário inválido.');
            }
        }

        return response()->json('Não foi possível autenticar. Tente novamente!', 422);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json('Usuário deslogado com sucesso', 200);
    }

    public function passwordRecovery()
    {
        // dd(SendPulseIntegration::getTemplates());

        return view('auth.password_recovery');
    }

    public function passwordRecoveryPost(Request $request)
    {
        $result = $request->all();

        $rules = [
            'email' => 'required|email',
        ];
        $messages = [
            'email.required' => 'E-mail é obrigatório',
            'email.email' => 'E-mail deve ser um endereço de e-mail válido',
        ];

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $getUser = User::where('email', $result['email'])->first();

        if (!$getUser) {
            return response()->json('Não encontramos nenhum e-mail. Tente novamente.', 422);
        }

        $codeRandom = rand(100000, 999999);

        $getUser->password_code = $codeRandom;

        try {
            $getUser->save();

            $emailOfuscated = $this->abreviarEmail($getUser->email);

            $notificationData = [
                'name' => $getUser->name,
                'email' => $getUser->email,
                'password_code' => $codeRandom,
                'email_hidden' => $emailOfuscated,
            ];

            dispatch(new ProcessNotificationJob('email', $getUser->email, 'General', 'email', 'password_recovery', $notificationData));
            if(isset($getUser->phone_ddi) && !empty($getUser->phone_ddi)) {
                dispatch(new ProcessNotificationJob('whatsapp', $getUser->phone_ddi, 'General', 'whatsapp', 'password_recovery', $notificationData));
            }

            session()->put('email_recovery', $getUser->email);

            return response()->json('O código para redefinição de senha foi enviado para seu e-mail.', 200);
        } catch (\Exception $e) {
            \Log::error('AuthController :: password recovery' . $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    public function passwordReset()
    {
        $email_recovery = session('email_recovery');

        return view('auth.password_reset', compact('email_recovery'));
    }

    public function passwordResetPost(Request $request)
    {
        $result = $request->all();

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_code' => 'required',
        ];
        $messages = [
            'email.required' => 'E-mail é obrigatório',
            'email.email' => 'E-mail deve ser um endereço de e-mail válido',
            'password.required' => 'Nova Senha é obrigatório',
            'password.min' => 'A quantidade mínima é de 6 caracteres',
            'password_code.required' => 'Código de Redefinição é obrigatório',
        ];

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $getUser = User::where('email', $result['email'])->where('password_code', $result['password_code'])->first();

        if (!$getUser) {
            return response()->json('Não foi possível validar seu código. Tente novamente.', 422);
        }

        $getUser->password = Hash::make($result['password']);
        $getUser->password_code = null;

        try {
            $getUser->save();

            return response()->json(['title' => 'Sua senha foi redefinida com sucesso, faça seu acesso', 'href' => '/auth/login'], 200);
        } catch (\Exception $e) {
            \Log::error('AuthController :: password reset' . $e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    public function abreviarEmail($email)
    {
        // Divide o e-mail em duas partes: antes e depois do "@"
        $emailParts = explode('@', $email);

        if (count($emailParts) !== 2) {
            // Retorna o e-mail original se ele não contém exatamente um "@"
            return $email;
        }

        // Extrai a parte local (antes do "@") e o domínio (depois do "@")
        $localPart = substr($emailParts[0], 0, 3); // Pega os primeiros três caracteres
        $domainPart = substr($emailParts[1], 0, 2); // Pega os primeiros dois caracteres do domínio

        // Adiciona os asteriscos para obfuscar o restante do e-mail
        // $obfuscatedEmail = $localPart . '***@' . $domainPart . '***.' . substr(strrchr($emailParts[1], '.'), 1);
        $obfuscatedEmail = $localPart . '***@' . $emailParts[1];

        return $obfuscatedEmail;
    }
}
