<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate with auth/middleware if needed
    }

    public function rules(): array
    {
        return [
            'id_number'        => ['required','digits:9'],
            'first_name'       => ['required','string','max:100'],
            'middle_initial'   => ['nullable','string','size:1','regex:/^[A-Za-z]$/'],
            'last_name'        => ['required','string','max:100'],
            'course'           => ['nullable','string','max:120'],
            'blood_type'       => ['nullable','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'address'          => ['nullable','string','max:500'],
            'parent_address'   => ['nullable','string','max:500'],
            'guardian_name'    => ['nullable','string','max:120'],
            // store digits only; 10–11 PH mobile digits typically
            'guardian_contact' => ['nullable','regex:/^\d{10,11}$/'],
            'gender'           => ['required','in:M,F'],

            // image: square, >=300x300, max 5MB, jpg/png
            'picture_path'     => [
                'required','image','mimes:jpg,jpeg,png','max:5120',
                'dimensions:ratio=1/1,min_width=300,min_height=300'
            ],

            'face_embedding'   => ['required','string'],

            // confirmation checkbox
            'confirm'          => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_number.digits'       => 'Student number must be exactly 9 digits.',
            'middle_initial.regex'   => 'Middle initial must be a single letter.',
            'guardian_contact.regex' => 'Guardian contact must be 10–11 digits.',
            'gender.in'              => 'Select M or F.',
            'picture_path.dimensions'=> 'Photo must be square (1:1) and at least 300×300px.',
            'face_embedding.required'=> 'Face verification data is missing. Please re-select your photo.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // normalize guardian_contact if a masked input was used
        if ($this->has('guardian_contact')) {
            $this->merge([
                'guardian_contact' => preg_replace('/\D+/', '', (string)$this->input('guardian_contact'))
            ]);
        }

        if ($this->has('face_embedding')) {
            $this->merge([
                'face_embedding' => trim((string) $this->input('face_embedding')),
            ]);
        }
    }
}


