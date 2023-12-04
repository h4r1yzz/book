<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContestRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'contest_start_at' => 'required',
            'contest_end_at' => 'required|date|after_or_equal:contest_start_at',
            'contest_display_start_at' => 'required',
            'contest_display_end_at' => 'required|date|after_or_equal:contest_start_at',
            'contest_type' => 'required|in:single,multi',
            'gifts_bounded' => 'nullable|array',
            'gifts_bounded.*.id' => 'sometimes',
            'gifts_bounded.*.pricing_id' => 'sometimes',
            'graphics' => 'array',
            'graphics.*.type' => 'nullable',
            'graphics.*.asset_url' => 'nullable|file|max:100000',
            'graphics.*.reference' => 'nullable',
            'graphics.*.url' => 'nullable',
            'graphics.*.text' => 'required',
            'graphics.*.is_countdown_required' => 'sometimes|boolean',
            'graphics.*.title_font_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'graphics.*.countdown_font_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'graphics.*.template' => 'required',
            'tier_system' => 'required|integer',
        ];
    }
}
