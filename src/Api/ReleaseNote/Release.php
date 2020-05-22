<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Autofactor\TicketingPortalClient\Api\ReleaseNote;


use Autofactor\TicketingPortalClient\Api\Model;

class Release extends Model
{
    protected $url = 'release';

    protected $fillable = [
        'id',
        'released_at',
        'subject',
        'notes',
    ];

    protected $relations = [
        'notes' => 'Autofactor\TicketingPortalClient\Api\ReleaseNote\Note'
    ];
}
