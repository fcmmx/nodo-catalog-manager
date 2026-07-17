<?php

namespace Tests\Unit;

use Tests\TestCase;

class TimezoneAndLocaleTest extends TestCase
{
    public function test_default_timezone_is_mexico_city(): void
    {
        $this->assertSame('America/Mexico_City', config('app.timezone'));
    }

    public function test_default_locale_is_spanish(): void
    {
        $this->assertSame('es', config('app.locale'));
    }
}
