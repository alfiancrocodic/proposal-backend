<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan untuk membuat request ini
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk mengupdate user
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'nama' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $userId,
            'jabatan' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ];
    }

    /**
     * Pesan error kustom untuk validasi
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'nama.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'jabatan.required' => 'Jabatan wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ];
    }
}
