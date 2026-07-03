<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class RoutineConflictException extends RuntimeException
{
    public function __construct(string $message = 'Routine conflict detected.')
    {
        parent::__construct($message);
    }
}
