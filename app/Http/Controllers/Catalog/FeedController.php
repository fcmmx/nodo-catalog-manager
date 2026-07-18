<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\CommerceSyncLog;
use App\Services\Commerce\CommerceFeedConfig;
use App\Services\Commerce\ProductFeedGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function csv(Request $request, string $token, CommerceFeedConfig $config, ProductFeedGenerator $generator): Response
    {
        $this->guardToken($token, $config);

        $products = $generator->eligibleProducts();
        $csv = $generator->toCsv();

        CommerceSyncLog::create([
            'type' => 'feed_csv', 'status' => 'exitoso', 'products_count' => $products->count(),
            'message' => 'Feed CSV solicitado.', 'ip_address' => $request->ip(),
        ]);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="catalogo-nodo360.csv"');
    }

    public function xml(Request $request, string $token, CommerceFeedConfig $config, ProductFeedGenerator $generator): Response
    {
        $this->guardToken($token, $config);

        $products = $generator->eligibleProducts();
        $xml = $generator->toXml();

        CommerceSyncLog::create([
            'type' => 'feed_xml', 'status' => 'exitoso', 'products_count' => $products->count(),
            'message' => 'Feed XML solicitado.', 'ip_address' => $request->ip(),
        ]);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="catalogo-nodo360.xml"');
    }

    protected function guardToken(string $token, CommerceFeedConfig $config): void
    {
        if (! hash_equals($config->feedToken(), $token)) {
            abort(403, 'Token de feed inválido.');
        }
    }
}
