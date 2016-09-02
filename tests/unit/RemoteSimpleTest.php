<?php

use mpyw\Co\Co;

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
    }

    private static function curlInitWith($url, array $params = [])
    {
        $ch = curl_init();
        curl_setopt_array($ch, array_replace([
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ], $params));
        return $ch;
    }

    public function testDownload()
    {
        $ch = self::curlInitWith('http://localhost:8080/upload_form.php');
        Co::wait($ch);
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
    }

    public function testRunSecure()
    {
        $ch = self::curlInitWith('https://localhost:8081/upload_form.php');
        Co::wait($ch);
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
    }
}
