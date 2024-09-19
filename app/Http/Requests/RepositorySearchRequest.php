<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepositorySearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        // We don't have to check for the user's authorization in this case
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => 'required|string|max:256',
        ];
    }
}