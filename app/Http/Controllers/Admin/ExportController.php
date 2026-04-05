<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TextCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportIncantation(\App\Models\Incantation $incantation)
    {
        $data = [
            'item' => $incantation,
            'title' => $incantation->title,
            'date' => now()->format('F d, Y'),
            'type' => 'Incantation'
        ];
        
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $pdf = Pdf::loadView('exports.spiritual-practice', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'serif',
        ]);
        return $pdf->download(\Illuminate\Support\Str::slug($incantation->title) . '-incantation.pdf');
    }

    public function exportRitual(\App\Models\Ritual $ritual)
    {
        $data = [
            'item' => $ritual,
            'title' => $ritual->title,
            'date' => now()->format('F d, Y'),
            'type' => 'Ritual'
        ];
        
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $pdf = Pdf::loadView('exports.spiritual-practice', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'serif',
        ]);
        return $pdf->download(\Illuminate\Support\Str::slug($ritual->title) . '-ritual.pdf');
    }
    /**
     * Export a Text Collection (e.g., Sacred Book) as PDF.
     */
    public function exportTextCollection(TextCollection $collection)
    {
        $collection->load(['chapters.entries.translations', 'tradition', 'category']);

        $data = [
            'collection' => $collection,
            'title' => $collection->name,
            'date' => now()->format('F d, Y'),
        ];

        // Increase memory and execution time for large books
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $pdf = Pdf::loadView('exports.sacred-text', $data);

        // Customize PDF settings
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'serif',
        ]);

        $filename = \Illuminate\Support\Str::slug($collection->name) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
