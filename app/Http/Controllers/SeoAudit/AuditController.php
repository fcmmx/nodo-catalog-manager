<?php

namespace App\Http\Controllers\SeoAudit;

use App\Http\Controllers\Controller;
use App\Models\SeoAudit;
use App\Services\SeoAudit\WebsiteAuditor;
use App\Services\SeoAudit\WebsiteAuditorException;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver auditoria');

        $audits = SeoAudit::query()
            ->with('creator')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('seo-audit.index', ['audits' => $audits]);
    }

    public function store(Request $request, WebsiteAuditor $auditor): RedirectResponse
    {
        $this->authorize('crear auditoria');

        $data = $request->validate(['url' => ['required', 'url', 'max:2048']]);

        try {
            $result = $auditor->audit($data['url']);

            $audit = SeoAudit::create([
                'url' => $data['url'],
                'status' => 'completado',
                'score' => $result['score'],
                'seo_score' => $result['seo_score'],
                'aeo_score' => $result['aeo_score'],
                'geo_score' => $result['geo_score'],
                'results' => $result['results'],
                'created_by' => $request->user()->id,
            ]);
        } catch (WebsiteAuditorException $e) {
            $audit = SeoAudit::create([
                'url' => $data['url'],
                'status' => 'error',
                'error_message' => $e->getMessage(),
                'created_by' => $request->user()->id,
            ]);

            return redirect()->route('seo-audit.show', $audit)->with('error', 'No se pudo completar la auditoría: '.$e->getMessage());
        }

        return redirect()->route('seo-audit.show', $audit)->with('success', 'Auditoría completada.');
    }

    public function show(SeoAudit $audit): View
    {
        $this->authorize('ver auditoria');

        return view('seo-audit.show', ['audit' => $audit]);
    }

    public function destroy(SeoAudit $audit): RedirectResponse
    {
        $this->authorize('crear auditoria');

        $audit->delete();

        return redirect()->route('seo-audit.index')->with('success', 'Auditoría eliminada.');
    }

    public function downloadPdf(SeoAudit $audit): Response
    {
        $this->authorize('ver auditoria');

        $html = view('seo-audit.pdf', ['audit' => $audit])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        $filename = 'auditoria-seo-'.$audit->id.'.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
