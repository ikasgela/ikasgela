<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        // https://medium.com/@scuttlebyte/running-headless-laravel-dusk-3-0-tests-in-docker-environments-f396752a9ffe

        $options = (new ChromeOptions)->addArguments([
            '--window-size=1920,1920',
            '--disable-gpu',
            '--headless',
            '--no-sandbox',
        ]);

        if (config('test.use_selenium')) {
            return RemoteWebDriver::create(
                'http://selenium:4444/wd/hub', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )->setCapability('acceptInsecureCerts', true));
        }
    }
}
