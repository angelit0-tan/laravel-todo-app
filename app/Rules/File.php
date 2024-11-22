<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class File implements ValidationRule
{

    protected $allowedExtensions;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Allowed valid extensions
        $this->allowedExtensions = collect(['jpeg', 'jpg', 'png', 'doc', 'docx', 'csv', 'txt', 'mp4']); 
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
         if (!$this->allowedExtensions->contains(strtolower($value->getClientOriginalExtension()))) {
            $fail("File type is not supported in the lists {$this->allowedExtensions}.");
         }
    }
}
