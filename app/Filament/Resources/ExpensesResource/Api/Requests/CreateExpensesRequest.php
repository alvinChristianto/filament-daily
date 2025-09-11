<?php

namespace App\Filament\Resources\ExpensesResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpensesRequest extends FormRequest
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
			'id_expenses' => 'required',
			'title' => 'required',
			'id_payment' => 'required',
			'record_date' => 'required',
			'category' => 'required',
			'amount' => 'required',
			'unit' => 'required',
			'price_per' => 'required',
			'price_total' => 'required',
			'origin_from' => 'required',
			'description' => 'required|string',
			'image_expenses' => 'required'
		];
    }
}
