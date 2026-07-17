<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_formatted_price_uses_mexican_thousands_and_decimal_format(): void
    {
        $product = Product::factory()->make(['price' => 12345.67, 'currency' => 'MXN', 'price_prefix_text' => null]);

        $this->assertSame('$12,345.67 MXN', $product->formattedPrice());
    }

    public function test_formatted_price_includes_prefix_text_when_present(): void
    {
        $product = Product::factory()->make(['price' => 4990, 'currency' => 'MXN', 'price_prefix_text' => 'Desde']);

        $this->assertSame('Desde $4,990.00 MXN', $product->formattedPrice());
    }

    public function test_formatted_price_handles_null_price(): void
    {
        $product = Product::factory()->make(['price' => null]);

        $this->assertSame('—', $product->formattedPrice());
    }
}
