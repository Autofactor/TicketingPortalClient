<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Autofactor\TicketingPortalClient\Api\Tag;


use Autofactor\TicketingPortalClient\Api\Model;

class Tag extends Model
{
    const TYPE_MODULE = 'module';

    protected $url = 'tags';

    protected $fillable = ['id', 'name', 'type'];

}
