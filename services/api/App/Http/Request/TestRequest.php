<?php

namespace App\Http\Request;

use CloudCastle\Core\Request\FormRequest;

final class TestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
        ];
    }
}