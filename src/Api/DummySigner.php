<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/28/17
 * Time: 5:12 PM
 */

namespace Albertarni\TicketingPortalClient\Api;

use Albertarni\TicketingPortalClient\SignerInterface;


class DummySigner implements SignerInterface
{

    private $email = '';

    public function __construct($email)
    {
        $this->setEmail($email);
    }


    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    /**
     * @return array
     */
    public function helpdeskData()
    {
        return ['email' => $this->email];
    }
}