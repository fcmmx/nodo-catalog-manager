<?php

namespace App\Services\Images;

use App\Models\ImageGeneration;
use App\Models\ImageTemplate;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\AI\Exceptions\AiException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageGenerationService
{
    public function __construct(
        protected ImageComposer $composer,
        protected AiImageService $aiImageService,
    ) {
    }

    /**
     * @param  array{title?:?string,subtitle?:?string,cta_text?:?string,price_text?:?string,qr_target_url?:?string,ai_prompt?:?string}  $inputs
     *
     * @throws AiException
     */
    public function generate(
        User $user,
        ImageTemplate $template,
        array $inputs,
        string $backgroundSource,
        ?UploadedFile $upload = null,
        ?Product $product = null,
    ): ImageGeneration {
        $backgroundPath = null;
        $aiPrompt = null;

        try {
            $backgroundPath = match ($backgroundSource) {
                'upload' => $upload?->getRealPath(),
                'product_image' => $this->productImagePath($product),
                'ai' => $this->resolveAiBackground($inputs['ai_prompt'] ?? '', $aiPrompt),
                default => null,
            };

            $logoPath = Setting::get('logo_path')
                ? Storage::disk('public')->path(Setting::get('logo_path'))
                : null;

            $image = $this->composer->compose($template, [
                'title' => $inputs['title'] ?? null,
                'subtitle' => $inputs['subtitle'] ?? null,
                'cta_text' => $inputs['cta_text'] ?? null,
                'price_text' => $inputs['price_text'] ?? null,
                'qr_target_url' => $inputs['qr_target_url'] ?? null,
                'background_path' => $backgroundPath,
                'logo_path' => $logoPath,
            ]);

            $filename = 'images/generated/'.Str::uuid().'.png';
            Storage::disk('public')->put($filename, (string) $image->toPng());

            return ImageGeneration::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'product_id' => $product?->id,
                'title' => $inputs['title'] ?? null,
                'subtitle' => $inputs['subtitle'] ?? null,
                'cta_text' => $inputs['cta_text'] ?? null,
                'price_text' => $inputs['price_text'] ?? null,
                'qr_target_url' => $inputs['qr_target_url'] ?? null,
                'background_source' => $backgroundSource,
                'file_path' => $filename,
                'ai_prompt' => $aiPrompt,
                'status' => 'completado',
            ]);
        } catch (AiException $e) {
            ImageGeneration::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'product_id' => $product?->id,
                'background_source' => $backgroundSource,
                'ai_prompt' => $aiPrompt,
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function productImagePath(?Product $product): ?string
    {
        if (! $product || ! $product->main_image) {
            return null;
        }

        return Storage::disk('public')->path($product->main_image);
    }

    protected function resolveAiBackground(string $prompt, ?string &$aiPrompt): ?string
    {
        if (empty($prompt)) {
            return null;
        }

        $aiPrompt = $prompt;

        return $this->aiImageService->generateBackground($prompt);
    }
}
