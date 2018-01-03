<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 6:17 PM
 */

namespace Albertarni\TicketingPortalClient\Api\Exception;


use Throwable;

class ValidationException extends \Exception
{
    protected $valiation_errors = [];

    public function __construct($valiation_errors)
    {
        parent::__construct('', 422);
        $this->valiation_errors = $valiation_errors;

        $this->message = $this->getFirstError();
    }

    /**
     * @return array
     */
    public function getRawErrors() {
        return $this->valiation_errors;
    }


    /**
     * @return mixed|string
     */
    public function getFirstError() {
        if (empty($this->valiation_errors)) {
            return '';
        }

        $first_value = reset($this->valiation_errors);
        if (!is_array($first_value)) {
            return $first_value;
        }

        return reset($first_value);
    }
}