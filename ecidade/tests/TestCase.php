<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class TestCase
 * @package Tests
 */
class TestCase extends MockeryTestCase
{
    /**
     * The Faker instance.
     *
     * @var Generator
     */
    protected $faker;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->setUpExtensions();
        $this->setUpFaker();
    }

    /**
     * @return void
     */
    protected function setUpFaker()
    {
        $this->faker = $this->makeFaker();
    }

    /**
     * @param string|null $locale
     * @return Generator
     */
    protected function faker($locale = null)
    {
        return is_null($locale) ? $this->faker : $this->makeFaker($locale);
    }

    /**
     * @param string|null $locale
     * @return Generator
     */
    protected function makeFaker($locale = null)
    {
        return Factory::create($locale ?: 'pt_BR');
    }

    /**
     * @return void
     */
    private function setUpExtensions()
    {
        date_default_timezone_set('America/Sao_Paulo');
        error_reporting(E_ALL);
        ini_set('display_errors', true);
    }
}
