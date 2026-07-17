<?php

namespace Database\Seeders;

use App\Models\ImageTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ImageTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Plantilla maestra NODO 360',
                'format' => 'cuadrado',
                'background_type' => 'color',
                'background_value' => '#F8FAFC',
                'overlay_gradient' => true,
                'primary_color' => '#2563EB',
                'accent_color' => '#DC2626',
                'title_position' => 'center',
                'show_price' => true,
                'show_qr' => true,
                'footer_text' => 'NODO 360 Marketing Technology',
                'is_master' => true,
            ],
            [
                'name' => 'Publicación cuadrada',
                'format' => 'cuadrado',
                'background_type' => 'color',
                'background_value' => '#0F172A',
                'overlay_gradient' => true,
                'primary_color' => '#2563EB',
                'accent_color' => '#DC2626',
                'title_position' => 'center',
                'show_price' => false,
                'show_qr' => false,
                'footer_text' => 'NODO 360 Marketing Technology',
                'is_master' => false,
            ],
            [
                'name' => 'Publicación vertical (feed)',
                'format' => 'vertical',
                'background_type' => 'color',
                'background_value' => '#0F172A',
                'overlay_gradient' => true,
                'primary_color' => '#7C3AED',
                'accent_color' => '#DC2626',
                'title_position' => 'bottom',
                'show_price' => true,
                'show_qr' => false,
                'footer_text' => 'NODO 360 Marketing Technology',
                'is_master' => false,
            ],
            [
                'name' => 'Historia (stories)',
                'format' => 'historia',
                'background_type' => 'color',
                'background_value' => '#0F172A',
                'overlay_gradient' => true,
                'primary_color' => '#2563EB',
                'accent_color' => '#DC2626',
                'title_position' => 'bottom',
                'show_price' => false,
                'show_qr' => true,
                'footer_text' => null,
                'is_master' => false,
            ],
            [
                'name' => 'Banner horizontal',
                'format' => 'horizontal',
                'background_type' => 'color',
                'background_value' => '#0F172A',
                'overlay_gradient' => true,
                'primary_color' => '#2563EB',
                'accent_color' => '#DC2626',
                'title_position' => 'center',
                'show_price' => true,
                'show_qr' => false,
                'footer_text' => 'NODO 360 Marketing Technology',
                'is_master' => false,
            ],
            [
                'name' => 'Portada de colección',
                'format' => 'portada',
                'background_type' => 'color',
                'background_value' => '#0F172A',
                'overlay_gradient' => true,
                'primary_color' => '#7C3AED',
                'accent_color' => '#DC2626',
                'title_position' => 'center',
                'show_price' => false,
                'show_qr' => false,
                'footer_text' => 'NODO 360 Marketing Technology',
                'is_master' => false,
            ],
        ];

        foreach ($templates as $data) {
            $format = ImageTemplate::FORMATS[$data['format']];

            ImageTemplate::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'width' => $format['width'],
                    'height' => $format['height'],
                ])
            );
        }
    }
}
