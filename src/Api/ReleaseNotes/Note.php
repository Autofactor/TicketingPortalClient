<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Albertarni\TicketingPortalClient\Api\ReleaseNotes;


use Albertarni\TicketingPortalClient\Api\Model;

class Note extends Model
{
    protected $url = 'notes';

    protected $fillable = ['id', 'text', 'position'];

}