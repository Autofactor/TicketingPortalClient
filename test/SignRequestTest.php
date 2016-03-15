<?php

namespace AlbertarniTest\TicketingPortalClient;

use PHPUnit_Framework_TestCase;
use Albertarni\TicketingPortalClient\SignRequest;

class SignRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * test
     */
    public function can_getUrl_appendUrl_to_emptyQueryString()
    {
        $baseUrl = 'http://localhost/index.php';
        $sr      = new SignRequest('whatever', $baseUrl);
        $this->assertEquals($baseUrl.'?gyumolcs=alma', $sr->getUrl(array(
                'gyumolcs' => 'alma'
        )));
    }
    /**
     * test
     */
    public function can_getUrl_appendUrl_to_existingQueryString()
    {
        $sr = new SignRequest('whatever', 'http://localhost/index.php?gyumolcs=alma');
        $this->assertEquals('http://localhost/index.php?gyumolcs=alma&zoldseg=uborka', $sr->getUrl(array(
                'zoldseg' => 'uborka'
        )));
    }
    /**
     * @test
     */
    public function can_getUrl_appendUrl_to_existingQueryString_if_dataContainsArray()
    {
        $sr = new SignRequest('whatever', 'http://localhost/index.php?gyumolcs=alma');
        $this->assertEquals('http://localhost/index.php?gyumolcs=alma&fa=tolgy&zoldsegek%5B0%5D=uborka&zoldsegek%5B1%5D=paradicsom', $sr->getUrl(array(
                'fa'        => 'tolgy',
                'zoldsegek' => array('uborka', 'paradicsom')
        )));
    }
}
