<?php

namespace App\Http\Requests\Auth;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // ✅ PERBAIKAN 1: Mengubah validasi dari 'email' menjadi 'username'
        return [
            // Dihapus ['required', 'string', 'email']
            'username' => ['required', 'string'], // Menggunakan 'username' sebagai string biasa
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

        // ✅ PERBAIKAN 2: Mengubah Auth::attempt untuk menggunakan 'username'
        // CATATAN: Auth::attempt akan mencari kolom yang didefinisikan di getAuthIdentifierName()
        if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // ✅ PERBAIKAN 3.1: Mengubah kunci pesan error validasi dari 'email' ke 'username'
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
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
            // ✅ PERBAIKAN 3.2: Mengubah kunci pesan error throttling dari 'email' ke 'username'
            'username' => trans('auth.throttle', [
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
        // ✅ PERBAIKAN 3.3: Mengubah string yang digunakan untuk rate limiting dari 'email' ke 'username'
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}