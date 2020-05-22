<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Autofactor\TicketingPortalClient\Api\Issue;


use Autofactor\TicketingPortalClient\Api\Model;

class Issue extends Model
{
    protected $url = 'issue';

    protected $fillable = ['id', 'title', 'description', 'attachments'];

}
