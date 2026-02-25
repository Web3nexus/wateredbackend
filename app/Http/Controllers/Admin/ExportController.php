<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TextCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
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
