<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class GradeNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'No matching grade found for the given mark.')
    {
        parent::__construct($message);
    }
}
