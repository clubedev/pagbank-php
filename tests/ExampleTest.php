<?php

namespace ClubeDev\PagBank\Tests;

use PHPUnit\Framework\TestCase;
use ClubeDev\PagBank\PagBank;

class ExampleTest extends TestCase
{
    public function testCanInstantiatePagBank()
    {
        $this->assertTrue(class_exists(PagBank::class));
    }
}
