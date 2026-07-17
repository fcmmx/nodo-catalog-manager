<?php

namespace App\Services\Images;

use App\Models\ImageTemplate;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Typography\FontFactory;

class ImageComposer
{
    protected string $fontsPath;

    public function __construct(
        protected ImageManager $manager,
        protected QrCodeGenerator $qrCodeGenerator,
    ) {
        $this->fontsPath = resource_path('fonts');
    }

    /**
     * Compone la imagen final y regresa el objeto de imagen (sin guardar).
     *
     * @param  array{title?:?string,subtitle?:?string,cta_text?:?string,price_text?:?string,qr_target_url?:?string,background_path?:?string,logo_path?:?string}  $options
     */
    public function compose(ImageTemplate $template, array $options): ImageInterface
    {
        $canvas = $this->buildBackground($template, $options['background_path'] ?? null);

        if ($template->overlay_gradient) {
            $this->applyDarkOverlay($canvas);
        }

        if (! empty($options['logo_path'])) {
            $this->placeLogo($canvas, $options['logo_path']);
        }

        $this->placeText($canvas, $template, $options);

        if ($template->show_price && ! empty($options['price_text'])) {
            $this->placePriceBadge($canvas, $template, $options['price_text']);
        }

        if (! empty($options['cta_text'])) {
            $this->placeCta($canvas, $template, $options['cta_text']);
        }

        if ($template->show_qr && ! empty($options['qr_target_url'])) {
            $this->placeQr($canvas, $options['qr_target_url']);
        }

        if ($template->footer_text) {
            $this->placeFooter($canvas, $template->footer_text);
        }

        return $canvas;
    }

    protected function buildBackground(ImageTemplate $template, ?string $backgroundPath): ImageInterface
    {
        if ($backgroundPath && file_exists($backgroundPath)) {
            return $this->manager->read($backgroundPath)->cover($template->width, $template->height);
        }

        $canvas = $this->manager->create($template->width, $template->height);

        $base = $template->background_type === 'color' && $template->background_value
            ? $template->background_value
            : '#F8FAFC';

        $this->fillGradient($canvas, $base, $template->primary_color);

        return $canvas;
    }

    protected function fillGradient(ImageInterface $canvas, string $fromHex, string $toHex): void
    {
        $from = $this->hexToRgb($fromHex);
        $to = $this->hexToRgb($toHex);
        $height = $canvas->height();

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / max(1, $height - 1);
            $r = (int) round($from[0] + ($to[0] - $from[0]) * $ratio);
            $g = (int) round($from[1] + ($to[1] - $from[1]) * $ratio);
            $b = (int) round($from[2] + ($to[2] - $from[2]) * $ratio);

            $canvas->drawRectangle(0, $y, function ($rect) use ($canvas, $r, $g, $b) {
                $rect->size($canvas->width(), 1);
                $rect->background("rgb({$r}, {$g}, {$b})");
            });
        }
    }

    protected function applyDarkOverlay(ImageInterface $canvas): void
    {
        $overlayHeight = (int) round($canvas->height() * 0.55);
        $y = $canvas->height() - $overlayHeight;

        for ($i = 0; $i < $overlayHeight; $i++) {
            $ratio = $i / max(1, $overlayHeight - 1);
            $alpha = round(0.65 * $ratio, 2);

            $canvas->drawRectangle(0, $y + $i, function ($rect) use ($canvas, $alpha) {
                $rect->size($canvas->width(), 1);
                $rect->background("rgba(11, 18, 32, {$alpha})");
            });
        }
    }

    protected function placeLogo(ImageInterface $canvas, string $logoPath): void
    {
        if (! file_exists($logoPath)) {
            return;
        }

        $logo = $this->manager->read($logoPath);
        $maxWidth = (int) round($canvas->width() * 0.22);
        $logo->scaleDown(width: $maxWidth);

        $margin = (int) round($canvas->width() * 0.04);
        $canvas->place($logo, 'top-left', $margin, $margin);
    }

    protected function placeText(ImageInterface $canvas, ImageTemplate $template, array $options): void
    {
        $margin = (int) round($canvas->width() * 0.08);
        $wrapWidth = $canvas->width() - ($margin * 2);

        $y = match ($template->title_position) {
            'top' => (int) round($canvas->height() * 0.16),
            'bottom' => (int) round($canvas->height() * 0.68),
            default => (int) round($canvas->height() * 0.42),
        };

        if (! empty($options['title'])) {
            $canvas->text($options['title'], (int) ($canvas->width() / 2), $y, function (FontFactory $font) use ($wrapWidth, $canvas) {
                $font->filename($this->fontsPath.'/Inter-Bold.ttf');
                $font->size((int) round($canvas->width() * 0.055));
                $font->color('#FFFFFF');
                $font->align('center');
                $font->valign('top');
                $font->wrap($wrapWidth);
                $font->lineHeight(1.15);
            });

            $y += (int) round($canvas->width() * 0.16);
        }

        if (! empty($options['subtitle'])) {
            $canvas->text($options['subtitle'], (int) ($canvas->width() / 2), $y, function (FontFactory $font) use ($wrapWidth, $canvas) {
                $font->filename($this->fontsPath.'/Inter-Regular.ttf');
                $font->size((int) round($canvas->width() * 0.028));
                $font->color('#E2E8F0');
                $font->align('center');
                $font->valign('top');
                $font->wrap($wrapWidth);
                $font->lineHeight(1.3);
            });
        }
    }

    protected function placePriceBadge(ImageInterface $canvas, ImageTemplate $template, string $priceText): void
    {
        $badgeWidth = (int) round($canvas->width() * 0.32);
        $badgeHeight = (int) round($canvas->width() * 0.07);
        $x = (int) round($canvas->width() * 0.06);
        $y = (int) round($canvas->height() * 0.06);

        $canvas->drawRectangle($x, $y, function ($rect) use ($badgeWidth, $badgeHeight, $template) {
            $rect->size($badgeWidth, $badgeHeight);
            $rect->background($template->accent_color);
        });

        $canvas->text($priceText, $x + (int) ($badgeWidth / 2), $y + (int) ($badgeHeight / 2), function (FontFactory $font) use ($canvas) {
            $font->filename($this->fontsPath.'/Inter-SemiBold.ttf');
            $font->size((int) round($canvas->width() * 0.026));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
    }

    protected function placeCta(ImageInterface $canvas, ImageTemplate $template, string $ctaText): void
    {
        $width = (int) round($canvas->width() * 0.4);
        $height = (int) round($canvas->width() * 0.075);
        $x = (int) round(($canvas->width() - $width) / 2);
        $y = $canvas->height() - $height - (int) round($canvas->height() * 0.1);

        $canvas->drawRectangle($x, $y, function ($rect) use ($width, $height) {
            $rect->size($width, $height);
            $rect->background('#FFFFFF');
        });

        $canvas->text($ctaText, $x + (int) ($width / 2), $y + (int) ($height / 2), function (FontFactory $font) use ($canvas, $template) {
            $font->filename($this->fontsPath.'/Inter-SemiBold.ttf');
            $font->size((int) round($canvas->width() * 0.026));
            $font->color($template->primary_color);
            $font->align('center');
            $font->valign('middle');
        });
    }

    protected function placeQr(ImageInterface $canvas, string $targetUrl): void
    {
        $size = (int) round($canvas->width() * 0.14);
        $qr = $this->qrCodeGenerator->make($targetUrl, $size);

        $margin = (int) round($canvas->width() * 0.04);
        $canvas->place($qr, 'bottom-right', $margin, $margin);
    }

    protected function placeFooter(ImageInterface $canvas, string $footerText): void
    {
        $canvas->text($footerText, (int) ($canvas->width() / 2), $canvas->height() - (int) round($canvas->height() * 0.03), function (FontFactory $font) use ($canvas) {
            $font->filename($this->fontsPath.'/Inter-Regular.ttf');
            $font->size((int) round($canvas->width() * 0.018));
            $font->color('#CBD5E1');
            $font->align('center');
            $font->valign('bottom');
        });
    }

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
