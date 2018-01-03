<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Albertarni\TicketingPortalClient\Api\ReleaseNotes;


use Albertarni\TicketingPortalClient\Api\Model;

class Release extends Model
{
    protected $url = 'releases';

    protected $fillable = [
        'id',
        'released_at',
        'notes',
    ];

    protected $relations = [
        'notes' => 'Albertarni\TicketingPortalClient\Api\ReleaseNotes\Note'
    ];
}