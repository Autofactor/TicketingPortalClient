<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 6:17 PM
 */

namespace Autofactor\TicketingPortalClient\Api\Exception;


use Throwable;

class NotFoundException extends \Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message ? : "Not found", 404);
    }
}
