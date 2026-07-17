<?php

namespace App\Services\AI;

use App\Models\AiGeneration;
use App\Models\Product;
use App\Models\User;
use App\Services\AI\Exceptions\AiException;
use Illuminate\Support\Facades\DB;

class ContentGenerationService
{
    /**
     * Catálogo de tareas de generación de contenido (sección 9 del brief).
     * Cada tarea define su etiqueta visible y los campos de entrada que
     * necesita del formulario (además del producto de referencia, opcional).
     */
    public const TASKS = [
        'nombre_comercial' => ['label' => 'Nombre comercial', 'inputs' => ['tema']],
        'descripcion_corta' => ['label' => 'Descripción corta', 'inputs' => ['tema']],
        'descripcion_completa' => ['label' => 'Descripción completa', 'inputs' => ['tema']],
        'beneficios' => ['label' => 'Beneficios', 'inputs' => ['tema']],
        'caracteristicas' => ['label' => 'Características', 'inputs' => ['tema']],
        'faqs' => ['label' => 'Preguntas frecuentes', 'inputs' => ['tema']],
        'palabras_clave' => ['label' => 'Palabras clave', 'inputs' => ['tema']],
        'metadatos_seo' => ['label' => 'Metadatos SEO (meta título y descripción)', 'inputs' => ['tema']],
        'datos_estructurados' => ['label' => 'Datos estructurados (JSON-LD)', 'inputs' => ['tema']],
        'publicacion_redes' => ['label' => 'Publicación para redes sociales', 'inputs' => ['tema', 'canal']],
        'asunto_email' => ['label' => 'Asuntos de email', 'inputs' => ['tema']],
        'contenido_landing' => ['label' => 'Contenido para landing page', 'inputs' => ['tema']],
        'mensaje_whatsapp' => ['label' => 'Mensaje para WhatsApp', 'inputs' => ['tema']],
        'prompt_imagen' => ['label' => 'Prompt de imagen', 'inputs' => ['tema']],
        'mejorar_texto' => ['label' => 'Mejorar texto existente', 'inputs' => ['texto']],
        'cambiar_tono' => ['label' => 'Cambiar tono de comunicación', 'inputs' => ['texto', 'tono']],
        'crear_variantes' => ['label' => 'Crear variantes', 'inputs' => ['texto']],
        'resumir' => ['label' => 'Resumir contenido', 'inputs' => ['texto']],
        'traducir' => ['label' => 'Traducir contenido', 'inputs' => ['texto', 'idioma']],
    ];

    public function __construct(
        protected AiClientFactory $factory,
        protected AiConfig $config,
    ) {
    }

    /**
     * @throws AiException
     */
    public function generate(User $user, string $task, array $inputs, ?Product $product = null): AiGeneration
    {
        if (! array_key_exists($task, self::TASKS)) {
            throw new \InvalidArgumentException("Tarea de IA desconocida: {$task}");
        }

        [$system, $userPrompt] = $this->buildPrompt($task, $inputs, $product);

        $client = $this->factory->make();

        try {
            $result = $client->complete($system, $userPrompt);

            return $this->log($user, $task, $userPrompt, $product, [
                'response' => $result->content,
                'input_tokens' => $result->inputTokens,
                'output_tokens' => $result->outputTokens,
                'estimated_cost' => $this->estimateCost($result->inputTokens, $result->outputTokens),
                'status' => 'completado',
            ]);
        } catch (AiException $e) {
            $this->log($user, $task, $userPrompt, $product, [
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function log(User $user, string $task, string $prompt, ?Product $product, array $attributes): AiGeneration
    {
        return AiGeneration::create(array_merge([
            'user_id' => $user->id,
            'product_id' => $product?->id,
            'task' => $task,
            'provider' => $this->config->provider(),
            'model' => $this->config->model(),
            'prompt' => $prompt,
        ], $attributes));
    }

    /**
     * Estimación aproximada de costo en USD. Las tarifas por proveedor/modelo
     * cambian con frecuencia; este cálculo es orientativo, no una factura.
     */
    protected function estimateCost(?int $inputTokens, ?int $outputTokens): ?float
    {
        if ($inputTokens === null || $outputTokens === null) {
            return null;
        }

        $ratePerMillionInput = 0.15;
        $ratePerMillionOutput = 0.60;

        return round(
            ($inputTokens / 1_000_000 * $ratePerMillionInput) + ($outputTokens / 1_000_000 * $ratePerMillionOutput),
            4
        );
    }

    protected function buildPrompt(string $task, array $inputs, ?Product $product): array
    {
        $brand = 'NODO 360 MARKETING TECHNOLOGY, una empresa mexicana de marketing y tecnología (nombre comercial del sistema: NODO Catalog Manager).';
        $system = "Eres un redactor de marketing experto en español de México, escribiendo para {$brand} Responde siempre en español, con un tono profesional, claro y persuasivo, sin inventar datos de contacto, precios o certificaciones que no se te hayan proporcionado.";

        $tema = $inputs['tema'] ?? $this->productContext($product);

        return match ($task) {
            'nombre_comercial' => [$system, "Propón 5 nombres comerciales cortos y memorables para: {$tema}. Devuelve solo la lista, uno por línea."],
            'descripcion_corta' => [$system, "Escribe una descripción corta (máximo 160 caracteres) para: {$tema}."],
            'descripcion_completa' => [$system, "Escribe una descripción completa (2 a 3 párrafos) para: {$tema}."],
            'beneficios' => [$system, "Escribe 3 a 5 beneficios, uno por línea, comenzando con guion, para: {$tema}."],
            'caracteristicas' => [$system, "Escribe 3 a 5 características técnicas o funcionales, una por línea, comenzando con guion, para: {$tema}."],
            'faqs' => [$system, "Escribe 5 preguntas frecuentes con su respuesta breve para: {$tema}. Formato: Pregunta seguida de la respuesta en la línea siguiente."],
            'palabras_clave' => [$system, "Genera una lista de 8 a 12 palabras clave relevantes en español, separadas por coma, para: {$tema}."],
            'metadatos_seo' => [$system, "Genera un meta título (máximo 60 caracteres) y una meta descripción (máximo 155 caracteres) para: {$tema}. Formato: 'Meta título: ...' y 'Meta descripción: ...' en líneas separadas."],
            'datos_estructurados' => [$system, "Genera un bloque JSON-LD válido de schema.org tipo Product o Service para: {$tema}. Responde solo con el JSON, sin explicaciones."],
            'publicacion_redes' => [$system, 'Escribe una publicación para '.($inputs['canal'] ?? 'redes sociales')." sobre: {$tema}. Incluye 3 a 5 hashtags relevantes al final."],
            'asunto_email' => [$system, "Propón 5 asuntos de correo (máximo 60 caracteres cada uno) para una campaña sobre: {$tema}. Devuelve solo la lista."],
            'contenido_landing' => [$system, "Escribe el contenido para una landing page (título principal, subtítulo y llamada a la acción) sobre: {$tema}."],
            'mensaje_whatsapp' => [$system, "Escribe un mensaje corto y amable para WhatsApp invitando a conocer: {$tema}."],
            'prompt_imagen' => [$system, "Escribe un prompt en inglés, detallado, para un generador de imágenes de IA, que ilustre visualmente: {$tema}. Estilo: minimalista, corporativo, gradientes azul y violeta con acentos rojos."],
            'mejorar_texto' => [$system, 'Mejora la redacción, claridad y persuasión del siguiente texto sin cambiar su significado: '.($inputs['texto'] ?? '')],
            'cambiar_tono' => [$system, 'Reescribe el siguiente texto con un tono '.($inputs['tono'] ?? 'más profesional').': '.($inputs['texto'] ?? '')],
            'crear_variantes' => [$system, 'Genera 3 variantes distintas del siguiente texto, cada una en un párrafo separado: '.($inputs['texto'] ?? '')],
            'resumir' => [$system, 'Resume el siguiente texto en un máximo de 3 líneas: '.($inputs['texto'] ?? '')],
            'traducir' => [$system, 'Traduce el siguiente texto al '.($inputs['idioma'] ?? 'inglés').' manteniendo el tono original: '.($inputs['texto'] ?? '')],
            default => [$system, $tema],
        };
    }

    protected function productContext(?Product $product): string
    {
        if (! $product) {
            return '';
        }

        return trim("{$product->name}. {$product->short_description}");
    }
}
