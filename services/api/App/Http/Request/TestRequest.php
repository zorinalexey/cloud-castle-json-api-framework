<?php

namespace App\Http\Request;

use CloudCastle\Core\Request\FormRequest;

final class TestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'date:default,2025-01-01:format,d.m.Y',
        ];
    }
}