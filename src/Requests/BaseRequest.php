<?php

namespace JSzD\World\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class BaseRequest extends FormRequest {
    protected array $availableFields = [];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $rules = [];

        $rules['fields'] = 'sometimes|string';

        $rules['filters'] = 'sometimes|array';
        foreach ($this->availableFields as $field) {
            $rules['filters.' . $field] = 'sometimes|string';
        }

        $rules['search'] = 'sometimes|string';

        return $rules;
    }
}
