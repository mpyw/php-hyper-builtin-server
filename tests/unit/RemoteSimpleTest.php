<?php

use mpyw\Privator\Proxy;
use mpyw\Privator\ProxyException;
use AspectMock\Test as test;

/**
 * @requires PHP 7.0
 */
class RemoteSimpleTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

    public function _before()
    {
    }

    public function _after()
    {
        test::clean();
    }

    public function testRun()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'http://localhost:8080/upload_form.php',
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec($ch);
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
    }

    public function testRunSecure()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'http://localhost:8080/upload_form.php',
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec($ch);
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
    }
}
