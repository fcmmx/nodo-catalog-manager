@php
    $categories = [
        'seo' => 'SEO tradicional',
        'aeo' => 'AEO (motores de respuesta por IA)',
        'geo' => 'GEO (motores generativos)',
    ];
    $gradeColor = $audit->score >= 75 ? '#16A34A' : ($audit->score >= 40 ? '#F59E0B' : '#DC2626');
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1E293B; margin: 0; padding: 24px; }
    h1 { font-size: 18px; margin: 0 0 4px; }
    .muted { color: #64748B; font-size: 10px; }
    .score-box { display: inline-block; width: 22%; text-align: center; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px; margin-right: 2%; }
    .score-value { font-size: 22px; font-weight: bold; }
    .section-title { font-size: 13px; font-weight: bold; margin: 20px 0 8px; border-bottom: 2px solid #2563EB; padding-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    td { padding: 6px 4px; border-bottom: 1px solid #E2E8F0; vertical-align: top; font-size: 10px; }
    .status-pass { color: #16A34A; font-weight: bold; }
    .status-fail { color: #DC2626; font-weight: bold; }
    .points { text-align: right; color: #64748B; white-space: nowrap; }
    .footer { margin-top: 20px; font-size: 9px; color: #94A3B8; text-align: center; }
</style>
</head>
<body>
    <h1>Auditoría SEO / AEO / GEO</h1>
    <p class="muted">{{ $audit->url }} — {{ $audit->created_at->format('d/m/Y H:i') }}</p>

    @if ($audit->status === 'error')
        <p style="color:#DC2626;">No se pudo completar la auditoría: {{ $audit->error_message }}</p>
    @else
        <div style="margin-top: 16px;">
            <div class="score-box">
                <div class="muted">GENERAL</div>
                <div class="score-value" style="color: {{ $gradeColor }}">{{ $audit->score }}/100</div>
                <div class="muted">Nivel {{ $audit->grade() }}</div>
            </div>
            @foreach ($categories as $key => $label)
                <div class="score-box">
                    <div class="muted">{{ strtoupper($key) }}</div>
                    <div class="score-value">{{ $audit->results[$key]['score'] }}/{{ $audit->results[$key]['max'] }}</div>
                </div>
            @endforeach
        </div>

        @foreach ($categories as $key => $label)
            <div class="section-title">{{ $label }} — {{ $audit->results[$key]['score'] }}/{{ $audit->results[$key]['max'] }} puntos</div>
            <table>
                @foreach ($audit->results[$key]['checks'] as $check)
                    <tr>
                        <td style="width: 22px;" class="{{ $check['passed'] ? 'status-pass' : 'status-fail' }}">{{ $check['passed'] ? 'OK' : 'X' }}</td>
                        <td>
                            <strong>{{ $check['label'] }}</strong><br>
                            <span class="muted">{{ $check['detail'] }}</span>
                        </td>
                        <td class="points">{{ $check['points'] }}/{{ $check['max'] }}</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    @endif

    <div class="footer">Generado por NODO Catalog Manager — NODO 360 MARKETING TECHNOLOGY</div>
</body>
</html>
