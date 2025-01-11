<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TournamentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'entry_fee'   => 'required|numeric|min:0',
            'type'        => 'required|in:individual,team',
            'start_date'  => 'required|date|after:now',
            'end_date'    => 'required|date|after:start_date'
        ];
    }
}
