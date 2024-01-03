<?php

namespace App\Http\Requests\Auth;

use App\Enums\BuffetStatus;
use App\Models\Buffet;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');

        $buffet = Buffet::where('slug', $this->buffet)->first();
        // Buffet nao existe
        if(!$buffet) {
            throw ValidationException::withMessages([
                'buffet' => trans('auth.failed'),
            ]);
        }
        $credentials['buffet_id'] = $buffet->id;

        if (!$user = $this->validateBuffet($credentials, $buffet)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }


        // Caso o usuario seja um administrador, o buffet_id é nulo, logo preciso adaptar as credenciais enviadas
        $credentials['buffet_id'] = $user->buffet_id;

        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    protected function validateBuffet(array $credentials, Buffet $buffet)
    {
        // if(!$buffet) return false;

        // if($buffet->status !== BuffetStatus::ACTIVE->name) {
        //     // retornar erro depois
        // }

        // Busca a existencia de um usuario em que o e-mail é válido e o buffet_id seja igual ao buffet que está tentando logar
        $users = User::where('email', $credentials['email'])->get();
        $isValidBuffet = $users->first(function ($user) use ($buffet) {
            return $user->buffet_id === $buffet->id;
        });

        if($isValidBuffet) return $isValidBuffet;

        // Caso não exista este usuário cadastrado, verifica se é administrador do buffet
        $isValidOwnerBuffet = $users->first(function ($user) use ($buffet) {
            return $user->id === $buffet->owner_id;
        });

        return $isValidOwnerBuffet ?? false;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
