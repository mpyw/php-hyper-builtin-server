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
        Co::setDefaultOptions(['concurrency' => 0]);
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

    private function curlsInitWith($n, $url, array $params = [])
    {
        $curls = [];
        for ($i = 0; $i < $n; ++$i) {
            $curls[] = $this->curlInitWith($url, $params);
        }
        return $curls;
    }

    private function curlAssert200OK($ch)
    {
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
    }

    private function curlsAssert200OK(array $curls)
    {
        foreach ($curls as $ch) {
            $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }
    }

    public function testSimple()
    {
        Co::wait($ch = $this->curlInitWith('http://localhost:8000/upload_form.php'));
        $this->curlAssert200OK($ch);
    }

    public function testSimpleSecure()
    {
        Co::wait($ch = $this->curlInitWith('https://localhost:44300/upload_form.php'));
        $this->curlAssert200OK($ch);
    }

    public function testDelayed()
    {
        Co::wait($ch = $this->curlInitWith('http://localhost:8000/fast_hello.php'));
        $this->curlAssert200OK($ch);
    }

    public function testDelayedSecure()
    {
        Co::wait($ch = $this->curlInitWith('https://localhost:44300/fast_hello.php'));
        $this->curlAssert200OK($ch);
    }

    public function testDelayedGroupSingle()
    {
        Co::wait($chs = $this->curlsInitWith(5, 'http://localhost:8000/fast_hello.php'));
        $this->curlsAssert200OK($chs);
    }

    public function testDelayedGroupSingleSecure()
    {
        Co::wait($chs = $this->curlsInitWith(5, 'https://localhost:44300/fast_hello.php'));
        $this->curlsAssert200OK($chs);
    }

    public function testDelayedGroupDouble()
    {
        Co::wait($chs = $this->curlsInitWith(10, 'http://localhost:8000/fast_hello.php'));
        $this->curlsAssert200OK($chs);
    }

    public function testDelayedGroupDoubleSecure()
    {
        Co::wait($chs = $this->curlsInitWith(10, 'https://localhost:44300/fast_hello.php'));
        $this->curlsAssert200OK($chs);
    }

    public function testDelayedGroupDoubleAtOnce()
    {
        Co::wait($chs = $this->curlsInitWith(10, 'http://localhost:8000/fast_hello.php'), [
            'concurrency' => 0,
        ]);
        $this->curlsAssert200OK($chs);
    }

    public function testDelayedGroupDoubleAtOnceSecure()
    {
        Co::wait($chs = $this->curlsInitWith(10, 'https://localhost:44300/fast_hello.php'), [
            'concurrency' => 0,
        ]);
        $this->curlsAssert200OK($chs);
    }
}
