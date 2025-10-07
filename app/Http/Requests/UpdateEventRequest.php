<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O campo título é obrigatório.',
            'title.string' => 'O campo título deve ser um texto.',
            'title.max' => 'O campo título não pode ter mais de 255 caracteres.',
            'description.string' => 'O campo descrição deve ser um texto.',
            'location.string' => 'O campo local deve ser um texto.',
            'location.max' => 'O campo local não pode ter mais de 255 caracteres.',
            'start_datetime.required' => 'O campo data/hora de início é obrigatório.',
            'start_datetime.date' => 'O campo data/hora de início deve ser uma data válida.',
            'end_datetime.required' => 'O campo data/hora de término é obrigatório.',
            'end_datetime.date' => 'O campo data/hora de término deve ser uma data válida.',
            'end_datetime.after' => 'A data/hora de término deve ser posterior à data de início.',
        ];
    }
}
