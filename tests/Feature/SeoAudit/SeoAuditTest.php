<?php

namespace Tests\Feature\SeoAudit;

use App\Models\SeoAudit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SeoAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function goodHtml(): string
    {
        $paragraph = str_repeat('Este es un párrafo de contenido de prueba con suficientes palabras para superar el umbral mínimo. ', 40);

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<title>Agencia de marketing digital NODO 360</title>
<meta name="description" content="Somos una agencia de marketing digital especializada en inteligencia artificial, automatización y crecimiento para negocios en México.">
<link rel="canonical" href="https://ejemplo-bueno.test/">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:title" content="NODO 360">
<meta property="og:description" content="Agencia de marketing digital">
<meta property="og:image" content="https://ejemplo-bueno.test/og.png">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Organization","name":"NODO 360","url":"https://ejemplo-bueno.test"}
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":"¿Qué es NODO 360?","acceptedAnswer":{"@type":"Answer","text":"Una agencia de marketing digital."}}]}
</script>
</head>
<body>
<header><nav>Menú</nav></header>
<main>
<article>
<h1>Bienvenido a NODO 360</h1>
<h2>¿Qué servicios ofrecemos?</h2>
<p>{$paragraph}</p>
<h3>Detalle del servicio</h3>
<p>{$paragraph}</p>
<img src="foto.jpg" alt="Equipo de NODO 360">
</article>
</main>
<footer>Pie de página</footer>
</body>
</html>
HTML;
    }

    protected function poorHtml(): string
    {
        return '<!DOCTYPE html><html><head><title>x</title></head><body><h2>Sin H1</h2><img src="a.jpg"></body></html>';
    }

    protected function fakeGoodSite(string $base = 'https://ejemplo-bueno.test'): void
    {
        Http::fake([
            $base => Http::response($this->goodHtml(), 200),
            $base.'/robots.txt' => Http::response("User-agent: *\nDisallow: /admin\n", 200),
            $base.'/sitemap.xml' => Http::response('<urlset></urlset>', 200),
            $base.'/llms.txt' => Http::response('# NODO 360', 200),
        ]);
    }

    public function test_user_can_run_an_audit_and_see_the_breakdown(): void
    {
        $this->fakeGoodSite();
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->post('/auditoria', ['url' => 'https://ejemplo-bueno.test']);

        $response->assertRedirect();
        $audit = SeoAudit::first();
        $this->assertSame('completado', $audit->status);
        $this->assertGreaterThan(80, $audit->score);
        $this->assertNotNull($audit->results);

        $show = $this->actingAs($user)->get("/auditoria/{$audit->id}");
        $show->assertOk();
        $show->assertSee('SEO tradicional');
    }

    public function test_user_without_permission_cannot_run_an_audit(): void
    {
        $user = $this->userWithRole('Cliente');

        $this->actingAs($user)->post('/auditoria', ['url' => 'https://ejemplo-bueno.test'])->assertForbidden();
    }

    public function test_poor_site_scores_much_lower_than_a_good_one(): void
    {
        Http::fake([
            'https://ejemplo-malo.test' => Http::response($this->poorHtml(), 200),
            'https://ejemplo-malo.test/robots.txt' => Http::response('', 404),
            'https://ejemplo-malo.test/sitemap.xml' => Http::response('', 404),
            'https://ejemplo-malo.test/llms.txt' => Http::response('', 404),
        ]);
        $this->fakeGoodSite();
        $user = $this->userWithRole('Marketing');

        $this->actingAs($user)->post('/auditoria', ['url' => 'https://ejemplo-bueno.test']);
        $this->actingAs($user)->post('/auditoria', ['url' => 'https://ejemplo-malo.test']);

        $goodScore = SeoAudit::where('url', 'https://ejemplo-bueno.test')->first()->score;
        $poorScore = SeoAudit::where('url', 'https://ejemplo-malo.test')->first()->score;

        $this->assertGreaterThan($poorScore + 30, $goodScore);
    }

    public function test_unreachable_url_is_recorded_as_an_error(): void
    {
        Http::fake([
            'https://no-existe.test' => Http::response('', 500),
        ]);
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->post('/auditoria', ['url' => 'https://no-existe.test']);

        $response->assertRedirect();
        $audit = SeoAudit::first();
        $this->assertSame('error', $audit->status);
        $this->assertNull($audit->score);
        $this->assertNotNull($audit->error_message);
    }

    public function test_ai_crawler_blocked_in_robots_txt_reduces_geo_score(): void
    {
        Http::fake([
            'https://bloquea-ia.test' => Http::response($this->goodHtml(), 200),
            'https://bloquea-ia.test/robots.txt' => Http::response("User-agent: GPTBot\nDisallow: /\n", 200),
            'https://bloquea-ia.test/sitemap.xml' => Http::response('', 404),
            'https://bloquea-ia.test/llms.txt' => Http::response('', 404),
        ]);
        $user = $this->userWithRole('Marketing');

        $this->actingAs($user)->post('/auditoria', ['url' => 'https://bloquea-ia.test']);

        $audit = SeoAudit::first();
        $checks = collect($audit->results['geo']['checks']);
        $crawlerCheck = $checks->firstWhere('key', 'ai_crawlers');
        $this->assertFalse($crawlerCheck['passed']);
    }

    public function test_faq_schema_increases_aeo_score(): void
    {
        $this->fakeGoodSite();
        $user = $this->userWithRole('Marketing');

        $this->actingAs($user)->post('/auditoria', ['url' => 'https://ejemplo-bueno.test']);

        $audit = SeoAudit::first();
        $checks = collect($audit->results['aeo']['checks']);
        $faqCheck = $checks->firstWhere('key', 'faq_schema');
        $this->assertTrue($faqCheck['passed']);
    }

    public function test_pdf_download_returns_a_real_pdf_file(): void
    {
        $this->fakeGoodSite();
        $user = $this->userWithRole('Marketing');
        $this->actingAs($user)->post('/auditoria', ['url' => 'https://ejemplo-bueno.test']);
        $audit = SeoAudit::first();

        $response = $this->actingAs($user)->get("/auditoria/{$audit->id}/pdf");

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_history_index_lists_past_audits(): void
    {
        $user = $this->userWithRole('Marketing');
        SeoAudit::create(['url' => 'https://historico.test', 'status' => 'completado', 'score' => 82, 'seo_score' => 30, 'aeo_score' => 25, 'geo_score' => 27, 'results' => ['seo' => ['score' => 30, 'max' => 40, 'checks' => []], 'aeo' => ['score' => 25, 'max' => 30, 'checks' => []], 'geo' => ['score' => 27, 'max' => 30, 'checks' => []]]]);

        $response = $this->actingAs($user)->get('/auditoria');

        $response->assertOk();
        $response->assertSee('historico.test');
    }

    public function test_user_can_delete_an_audit(): void
    {
        $user = $this->userWithRole('Marketing');
        $audit = SeoAudit::create(['url' => 'https://borrar.test', 'status' => 'completado', 'score' => 50]);

        $this->actingAs($user)->delete("/auditoria/{$audit->id}")->assertRedirect();

        $this->assertDatabaseMissing('seo_audits', ['id' => $audit->id]);
    }
}
