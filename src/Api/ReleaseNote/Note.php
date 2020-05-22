<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Autofactor\TicketingPortalClient\Api\ReleaseNote;


use Autofactor\TicketingPortalClient\Api\Model;

class Note extends Model
{
    protected $url = 'notes';

    protected $fillable = ['id', 'text', 'position'];

}
