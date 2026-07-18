<?php

namespace App\Services\SeoAudit;

use Illuminate\Support\Facades\Http;

/**
 * Analiza una URL externa real (descarga su HTML, robots.txt, sitemap.xml
 * y llms.txt) y califica señales de SEO tradicional, AEO (optimización
 * para motores de respuesta por IA como ChatGPT/Perplexity) y GEO
 * (optimización para motores generativos: rastreadores de IA, llms.txt).
 * Cada punto de la calificación corresponde a una señal verificada
 * realmente en el contenido descargado — nada se simula ni se inventa.
 */
class WebsiteAuditor
{
    protected const AI_CRAWLERS = ['gptbot', 'claudebot', 'anthropic-ai', 'google-extended', 'perplexitybot', 'ccbot'];

    /**
     * @return array{score:int, seo_score:int, aeo_score:int, geo_score:int, results:array}
     *
     * @throws WebsiteAuditorException
     */
    public function audit(string $url): array
    {
        $response = $this->fetch($url);
        $html = $response->body();

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        $robotsTxt = $this->fetchQuietly($this->baseUrl($url).'/robots.txt');
        $sitemapOk = $this->headOk($this->baseUrl($url).'/sitemap.xml');
        $llmsTxt = $this->fetchQuietly($this->baseUrl($url).'/llms.txt');

        $seo = $this->seoChecks($url, $dom, $xpath, $sitemapOk);
        $aeo = $this->aeoChecks($dom, $xpath);
        $geo = $this->geoChecks($dom, $xpath, $robotsTxt, $llmsTxt);

        $results = ['seo' => $seo, 'aeo' => $aeo, 'geo' => $geo];
        $totalScore = $seo['score'] + $aeo['score'] + $geo['score'];

        return [
            'score' => $totalScore,
            'seo_score' => $seo['score'],
            'aeo_score' => $aeo['score'],
            'geo_score' => $geo['score'],
            'results' => $results,
        ];
    }

    protected function fetch(string $url): \Illuminate\Http\Client\Response
    {
        try {
            $response = Http::timeout(15)->withUserAgent('NODO360-SEO-Auditor/1.0')->get($url);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw WebsiteAuditorException::networkError($e->getMessage());
        }

        if ($response->failed()) {
            throw WebsiteAuditorException::httpError($response->status());
        }

        return $response;
    }

    protected function fetchQuietly(string $url): ?string
    {
        try {
            $response = Http::timeout(8)->withUserAgent('NODO360-SEO-Auditor/1.0')->get($url);

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function headOk(string $url): bool
    {
        try {
            return Http::timeout(8)->get($url)->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    protected function baseUrl(string $url): string
    {
        $parts = parse_url($url);

        return ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');
    }

    protected function check(string $key, string $label, bool $passed, int $points, int $max, string $detail): array
    {
        return compact('key', 'label', 'passed', 'points', 'max', 'detail');
    }

    protected function seoChecks(string $url, \DOMDocument $dom, \DOMXPath $xpath, bool $sitemapOk): array
    {
        $checks = [];

        $isHttps = str_starts_with($url, 'https://');
        $checks[] = $this->check('https', 'Conexión segura (HTTPS)', $isHttps, $isHttps ? 5 : 0, 5,
            $isHttps ? 'El sitio usa HTTPS.' : 'El sitio no usa HTTPS — es un requisito básico de confianza y de posicionamiento.');

        $title = trim($this->nodeText($xpath, '//title'));
        $titleOk = $title !== '' && strlen($title) >= 10 && strlen($title) <= 60;
        $checks[] = $this->check('title', 'Título de la página (10–60 caracteres)', $titleOk, $titleOk ? 5 : 0, 5,
            $title === '' ? 'No se encontró etiqueta <title>.' : "Título actual (".strlen($title)." caracteres): \"{$title}\"");

        $description = $this->metaContent($xpath, 'description');
        $descOk = $description !== null && strlen($description) >= 50 && strlen($description) <= 160;
        $checks[] = $this->check('meta_description', 'Meta descripción (50–160 caracteres)', $descOk, $descOk ? 5 : 0, 5,
            $description === null ? 'No se encontró meta description.' : 'Longitud actual: '.strlen($description).' caracteres.');

        $h1s = $xpath->query('//h1');
        $h1Ok = $h1s->length === 1;
        $checks[] = $this->check('single_h1', 'Un solo encabezado H1', $h1Ok, $h1Ok ? 5 : 0, 5,
            "Se encontraron {$h1s->length} etiqueta(s) H1 (lo ideal es exactamente 1).");

        $canonical = $xpath->query('//link[@rel="canonical"]')->length > 0;
        $checks[] = $this->check('canonical', 'Enlace canónico', $canonical, $canonical ? 4 : 0, 4,
            $canonical ? 'Se encontró <link rel="canonical">.' : 'No se encontró <link rel="canonical">.');

        $viewport = $xpath->query('//meta[@name="viewport"]')->length > 0;
        $checks[] = $this->check('viewport', 'Meta viewport (compatible con móviles)', $viewport, $viewport ? 4 : 0, 4,
            $viewport ? 'Se encontró meta viewport.' : 'No se encontró meta viewport — la página podría no adaptarse bien a móviles.');

        $images = $xpath->query('//img');
        $withAlt = 0;
        foreach ($images as $img) {
            if (trim((string) $img->getAttribute('alt')) !== '') {
                $withAlt++;
            }
        }
        $imgTotal = $images->length;
        $altRatio = $imgTotal > 0 ? $withAlt / $imgTotal : 1;
        $altPoints = (int) round($altRatio * 6);
        $checks[] = $this->check('image_alt', 'Texto alternativo en imágenes', $altRatio >= 0.9, $altPoints, 6,
            $imgTotal > 0 ? "{$withAlt} de {$imgTotal} imagen(es) tienen atributo alt." : 'La página no tiene imágenes.');

        $robotsMeta = $this->metaContent($xpath, 'robots');
        $notBlocked = $robotsMeta === null || ! str_contains(strtolower($robotsMeta), 'noindex');
        $checks[] = $this->check('indexable', 'No bloqueada para buscadores', $notBlocked, $notBlocked ? 4 : 0, 4,
            $notBlocked ? 'La página no tiene noindex.' : 'La página tiene meta robots "noindex" — los buscadores no la indexarán.');

        $checks[] = $this->check('sitemap', 'Sitemap.xml accesible', $sitemapOk, $sitemapOk ? 2 : 0, 2,
            $sitemapOk ? 'Se encontró /sitemap.xml.' : 'No se encontró /sitemap.xml en la raíz del dominio.');

        return $this->summarize($checks);
    }

    protected function aeoChecks(\DOMDocument $dom, \DOMXPath $xpath): array
    {
        $checks = [];

        $jsonLdNodes = $xpath->query('//script[@type="application/ld+json"]');
        $schemas = $this->parseJsonLd($jsonLdNodes);

        $hasAnySchema = count($schemas) > 0;
        $checks[] = $this->check('structured_data', 'Datos estructurados (JSON-LD)', $hasAnySchema, $hasAnySchema ? 6 : 0, 6,
            $hasAnySchema ? 'Se encontraron '.count($schemas).' bloque(s) de datos estructurados.' : 'No se encontraron datos estructurados JSON-LD.');

        $hasFaq = $this->schemaTypesInclude($schemas, ['faqpage', 'qapage']);
        $checks[] = $this->check('faq_schema', 'Datos estructurados de preguntas frecuentes (FAQPage)', $hasFaq, $hasFaq ? 6 : 0, 6,
            $hasFaq ? 'Se encontró FAQPage o QAPage.' : 'No se encontró FAQPage/QAPage — este tipo de dato ayuda a que los motores de respuesta por IA citen contenido directamente.');

        $headingLevels = $this->headingLevelsInOrder($xpath);
        $hierarchyOk = $this->isHierarchyLogical($headingLevels);
        $checks[] = $this->check('heading_hierarchy', 'Jerarquía de encabezados lógica', $hierarchyOk, $hierarchyOk ? 6 : 0, 6,
            $hierarchyOk ? 'La jerarquía de encabezados (H1→H2→H3…) no tiene saltos de nivel.' : 'La jerarquía de encabezados tiene saltos de nivel o no empieza en H1.');

        $questionHeading = $this->hasQuestionHeading($xpath);
        $checks[] = $this->check('question_headings', 'Al menos un encabezado en forma de pregunta', $questionHeading, $questionHeading ? 6 : 0, 6,
            $questionHeading ? 'Se encontró al menos un encabezado que termina en "?".' : 'Ningún encabezado está formulado como pregunta — las preguntas explícitas facilitan que la IA extraiga respuestas directas.');

        $ogTitle = $this->metaProperty($xpath, 'og:title') !== null;
        $ogDesc = $this->metaProperty($xpath, 'og:description') !== null;
        $ogImage = $this->metaProperty($xpath, 'og:image') !== null;
        $ogComplete = $ogTitle && $ogDesc && $ogImage;
        $checks[] = $this->check('open_graph', 'Etiquetas Open Graph completas', $ogComplete, $ogComplete ? 6 : 0, 6,
            $ogComplete ? 'og:title, og:description y og:image están presentes.' : 'Faltan una o más etiquetas Open Graph (og:title, og:description, og:image).');

        return $this->summarize($checks);
    }

    protected function geoChecks(\DOMDocument $dom, \DOMXPath $xpath, ?string $robotsTxt, ?string $llmsTxt): array
    {
        $checks = [];

        $aiCrawlersAllowed = $this->aiCrawlersAllowed($robotsTxt);
        $checks[] = $this->check('ai_crawlers', 'robots.txt permite rastreadores de IA', $aiCrawlersAllowed, $aiCrawlersAllowed ? 10 : 0, 10,
            $robotsTxt === null
                ? 'No se encontró robots.txt (se asume permitido por defecto).'
                : ($aiCrawlersAllowed ? 'robots.txt no bloquea a GPTBot, ClaudeBot, Google-Extended ni PerplexityBot.' : 'robots.txt bloquea explícitamente a uno o más rastreadores de IA.'));

        $hasLlmsTxt = $llmsTxt !== null;
        $checks[] = $this->check('llms_txt', 'Archivo llms.txt (estándar emergente para IA)', $hasLlmsTxt, $hasLlmsTxt ? 5 : 0, 5,
            $hasLlmsTxt ? 'Se encontró /llms.txt.' : 'No se encontró /llms.txt — este archivo ayuda a los motores generativos a entender el sitio.');

        $landmarks = 0;
        foreach (['main', 'article', 'nav', 'header', 'footer'] as $tag) {
            if ($xpath->query("//{$tag}")->length > 0) {
                $landmarks++;
            }
        }
        $semanticOk = $landmarks >= 2;
        $checks[] = $this->check('semantic_html', 'Estructura HTML5 semántica', $semanticOk, $semanticOk ? 5 : 0, 5,
            "Se encontraron {$landmarks} de 5 elementos semánticos (main, article, nav, header, footer).");

        $jsonLdNodes = $xpath->query('//script[@type="application/ld+json"]');
        $schemas = $this->parseJsonLd($jsonLdNodes);
        $hasIdentity = $this->schemaTypesInclude($schemas, ['organization', 'website', 'person', 'localbusiness']);
        $checks[] = $this->check('identity_schema', 'Datos estructurados de identidad (Organization/WebSite)', $hasIdentity, $hasIdentity ? 5 : 0, 5,
            $hasIdentity ? 'Se encontraron datos estructurados de identidad.' : 'No se encontraron datos estructurados de tipo Organization, WebSite o Person.');

        $wordCount = $this->bodyWordCount($xpath);
        $contentOk = $wordCount >= 300;
        $checks[] = $this->check('content_length', 'Contenido textual suficiente (300+ palabras)', $contentOk, $contentOk ? 5 : 0, 5,
            "La página tiene aproximadamente {$wordCount} palabras de contenido visible.");

        return $this->summarize($checks);
    }

    protected function summarize(array $checks): array
    {
        $score = array_sum(array_column($checks, 'points'));
        $max = array_sum(array_column($checks, 'max'));

        return ['score' => $score, 'max' => $max, 'checks' => $checks];
    }

    protected function nodeText(\DOMXPath $xpath, string $query): string
    {
        $node = $xpath->query($query)->item(0);

        return $node ? $node->textContent : '';
    }

    protected function metaContent(\DOMXPath $xpath, string $name): ?string
    {
        $node = $xpath->query("//meta[@name=\"{$name}\"]")->item(0);
        $content = $node ? trim($node->getAttribute('content')) : '';

        return $content !== '' ? $content : null;
    }

    protected function metaProperty(\DOMXPath $xpath, string $property): ?string
    {
        $node = $xpath->query("//meta[@property=\"{$property}\"]")->item(0);
        $content = $node ? trim($node->getAttribute('content')) : '';

        return $content !== '' ? $content : null;
    }

    protected function parseJsonLd(\DOMNodeList $nodes): array
    {
        $schemas = [];
        foreach ($nodes as $node) {
            $decoded = json_decode($node->textContent, true);
            if (is_array($decoded)) {
                $schemas[] = $decoded;
            }
        }

        return $schemas;
    }

    protected function schemaTypesInclude(array $schemas, array $wantedTypesLower): bool
    {
        foreach ($schemas as $schema) {
            $entries = isset($schema['@graph']) && is_array($schema['@graph']) ? $schema['@graph'] : [$schema];
            foreach ($entries as $entry) {
                $type = $entry['@type'] ?? null;
                $types = is_array($type) ? $type : [$type];
                foreach ($types as $t) {
                    if ($t && in_array(strtolower($t), $wantedTypesLower, true)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function headingLevelsInOrder(\DOMXPath $xpath): array
    {
        $levels = [];
        $nodes = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');
        foreach ($nodes as $node) {
            $levels[] = (int) substr($node->nodeName, 1);
        }

        return $levels;
    }

    protected function isHierarchyLogical(array $levels): bool
    {
        if (empty($levels) || $levels[0] !== 1) {
            return false;
        }

        $current = 1;
        foreach ($levels as $level) {
            if ($level > $current + 1) {
                return false;
            }
            $current = $level;
        }

        return true;
    }

    protected function hasQuestionHeading(\DOMXPath $xpath): bool
    {
        $nodes = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');
        foreach ($nodes as $node) {
            if (str_ends_with(trim($node->textContent), '?')) {
                return true;
            }
        }

        return false;
    }

    protected function aiCrawlersAllowed(?string $robotsTxt): bool
    {
        if ($robotsTxt === null) {
            return true;
        }

        $lines = preg_split('/\r\n|\r|\n/', strtolower($robotsTxt));
        $currentAgents = [];
        $blocked = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_starts_with($line, 'user-agent:')) {
                $agent = trim(substr($line, 11));
                $currentAgents = [$agent];
            } elseif (str_starts_with($line, 'disallow:')) {
                $path = trim(substr($line, 9));
                if ($path === '/') {
                    foreach ($currentAgents as $agent) {
                        if ($agent === '*' || in_array($agent, self::AI_CRAWLERS, true)) {
                            $blocked[] = $agent;
                        }
                    }
                }
            }
        }

        foreach (self::AI_CRAWLERS as $crawler) {
            if (in_array($crawler, $blocked, true)) {
                return false;
            }
        }
        if (in_array('*', $blocked, true)) {
            return false;
        }

        return true;
    }

    protected function bodyWordCount(\DOMXPath $xpath): int
    {
        $body = $xpath->query('//body')->item(0);
        if (! $body) {
            return 0;
        }

        $text = preg_replace('/\s+/', ' ', $body->textContent);

        return str_word_count(trim($text));
    }
}
