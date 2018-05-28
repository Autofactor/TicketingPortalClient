<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Albertarni\TicketingPortalClient\Api\ReleaseNote;


use Albertarni\TicketingPortalClient\Api\Model;

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
        'notes' => 'Albertarni\TicketingPortalClient\Api\ReleaseNote\Note'
    ];
}
