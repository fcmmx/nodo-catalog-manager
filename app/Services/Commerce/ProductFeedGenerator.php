<?php

namespace App\Services\Commerce;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;

/**
 * Genera el feed de catálogo de productos en los formatos que acepta
 * Meta Commerce Manager (y, por compatibilidad, Google Merchant Center):
 * CSV con encabezados estándar, o XML tipo RSS 2.0 / Google Shopping.
 *
 * Solo incluye productos publicados (status = activo) que tengan
 * precio y un enlace público (`url`) configurado, porque ambos campos
 * son obligatorios en la especificación del feed — un producto sin
 * ellos no puede aparecer en un catálogo real, así que se omite en
 * vez de inventar un valor.
 */
class ProductFeedGenerator
{
    const AVAILABILITY_MAP = [
        'disponible' => 'in stock',
        'agotado' => 'out of stock',
        'bajo_pedido' => 'available for order',
        'proximamente' => 'preorder',
    ];

    const HEADERS = ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand'];

    public function eligibleProducts(): Collection
    {
        return Product::query()
            ->where('status', 'activo')
            ->whereNotNull('price')
            ->whereNotNull('url')
            ->where('url', '!=', '')
            ->orderBy('name')
            ->get();
    }

    public function toCsv(): string
    {
        $products = $this->eligibleProducts();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, self::HEADERS);

        foreach ($products as $product) {
            fputcsv($handle, $this->row($product));
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    public function toXml(): string
    {
        $products = $this->eligibleProducts();
        $companyName = Setting::get('company_name', 'NODO 360 MARKETING TECHNOLOGY');

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', htmlspecialchars($companyName.' — Catálogo de productos'));
        $channel->addChild('link', htmlspecialchars(config('app.url')));
        $channel->addChild('description', htmlspecialchars('Feed de catálogo de productos y servicios de '.$companyName));

        foreach ($products as $product) {
            $item = $channel->addChild('item');
            $row = $this->row($product);
            $item->addChild('g:id', htmlspecialchars($row[0]));
            $item->addChild('g:title', htmlspecialchars($row[1]));
            $item->addChild('g:description', htmlspecialchars($row[2]));
            $item->addChild('g:availability', htmlspecialchars($row[3]));
            $item->addChild('g:condition', htmlspecialchars($row[4]));
            $item->addChild('g:price', htmlspecialchars($row[5]));
            $item->addChild('g:link', htmlspecialchars($row[6]));
            $item->addChild('g:image_link', htmlspecialchars($row[7]));
            $item->addChild('g:brand', htmlspecialchars($row[8]));
        }

        return $xml->asXML();
    }

    protected function row(Product $product): array
    {
        $companyName = Setting::get('company_name', 'NODO 360 MARKETING TECHNOLOGY');

        return [
            $product->sku,
            $product->name,
            strip_tags($product->short_description ?: $product->description ?: $product->name),
            self::AVAILABILITY_MAP[$product->availability] ?? 'out of stock',
            'new',
            number_format((float) $product->price, 2, '.', '').' '.$product->currency,
            $product->url,
            $product->imageUrl() ?: '',
            $companyName,
        ];
    }
}
