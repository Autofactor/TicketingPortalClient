<?php
/**
 * Created by PhpStorm.
 * User: ffogarasi
 * Date: 12/27/17
 * Time: 5:52 PM
 */

namespace Albertarni\TicketingPortalClient\Api\Help;


use Albertarni\TicketingPortalClient\Api\Model;

class Help extends Model
{
    const TYPE_HELP = 'help';
    const TYPE_FAQ = 'faq';

    protected $url = 'help';

    protected $fillable = ['id', 'type', 'title', 'content', 'tags'];

}