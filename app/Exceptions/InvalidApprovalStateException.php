<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InvalidApprovalStateException extends RuntimeException
{
    public function __construct(string $message = 'Invalid approval state transition.')
    {
        parent::__construct($message);
    }
}
