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

    private function assertInRange($min, $actual, $max)
    {
        $this->assertGreaterThanOrEqual($min, $actual);
        $this->assertLessThanOrEqual($max, $actual);
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
        Co::wait($ch = $this->curlInitWith('http://localhost:8080/upload_form.php'));
        $this->curlAssert200OK($ch);
    }

    public function testSimpleSecure()
    {
        Co::wait($ch = $this->curlInitWith('https://localhost:8081/upload_form.php'));
        $this->curlAssert200OK($ch);
    }

    public function testDelayed()
    {
        $start = microtime(true);
        Co::wait($ch = $this->curlInitWith('http://localhost:8080/fast_hello.php'));
        $end = microtime(true);
        $this->curlAssert200OK($ch);
        $this->assertInRange(0.1, $end - $start, 0.2);
    }

    public function testDelayedSecure()
    {
        $start = microtime(true);
        Co::wait($ch = $this->curlInitWith('https://localhost:8081/fast_hello.php'));
        $end = microtime(true);
        $this->curlAssert200OK($ch);
        $this->assertInRange(0.11, $end - $start, 0.21);
    }

    public function testDelayedGroupSingle()
    {
        $start = microtime(true);
        Co::wait($chs = $this->curlsInitWith(5, 'http://localhost:8080/fast_hello.php'));
        $end = microtime(true);
        $this->curlsAssert200OK($chs);
        $this->assertInRange(0.12, $end - $start, 0.22);
    }

    public function testDelayedGroupSingleSecure()
    {
        $start = microtime(true);
        Co::wait($chs = $this->curlsInitWith(5, 'https://localhost:8081/fast_hello.php'));
        $end = microtime(true);
        $this->curlsAssert200OK($chs);
        $this->assertInRange(0.12, $end - $start, 0.22);
    }

    public function testDelayedGroupDouble()
    {
        $start = microtime(true);
        Co::wait($chs = $this->curlsInitWith(10, 'http://localhost:8080/fast_hello.php'));
        $end = microtime(true);
        $this->curlsAssert200OK($chs);
        $this->assertInRange(0.28, $end - $start, 0.38);
    }

    public function testDelayedGroupDoubleSecure()
    {
        $start = microtime(true);
        Co::wait($chs = $this->curlsInitWith(10, 'https://localhost:8081/fast_hello.php'));
        $end = microtime(true);
        $this->curlsAssert200OK($chs);
        $this->assertInRange(0.32, $end - $start, 0.42);
    }

    public function testDelayedGroupDoubleAtOnce()
    {
        $start = microtime(true);
        Co::wait($chs = $this->curlsInitWith(10, 'http://localhost:8080/fast_hello.php'), [
            'concurrency' => 0,
        ]);
        $end = microtime(true);
        $this->curlsAssert200OK($chs);
        $this->assertInRange(0.28, $end - $start, 0.38);
    }

    public function testDelayedGroupDoubleAtOnceSecure()
    {
        $start = microtime(true);
        Co::wait($chs = $this->curlsInitWith(10, 'https://localhost:8081/fast_hello.php'), [
            'concurrency' => 0,
        ]);
        $end = microtime(true);
        $this->curlsAssert200OK($chs);
        $this->assertInRange(0.32, $end - $start, 0.42);
    }
}
