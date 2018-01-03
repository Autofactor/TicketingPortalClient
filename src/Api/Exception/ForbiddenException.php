<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 6:17 PM
 */

namespace Albertarni\TicketingPortalClient\Api\Exception;


use Throwable;

class ForbiddenException extends \Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message, 403);
    }
}