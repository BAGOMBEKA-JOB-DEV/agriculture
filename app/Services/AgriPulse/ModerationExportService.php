<?php

namespace App\Services\AgriPulse;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ModerationExportService
{
    /**
     * Export data to specified format (fully dynamic).
     */
    public function export(Collection $collection, string $type, string $format, array $filters = []): string
    {
        // Convert collection to array format dynamically
        $data = $this->formatDataDynamically($collection);
        
        $metadata = [
            'type' => ucfirst($type),
            'total_records' => $collection->count(),
            'filters' => $filters,
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()->name ?? 'System'
        ];

        return $this->generateFile($data, $metadata, $format, $type);
    }

    /**
     * Dynamically format data from collection.
     * Automatically detects all attributes and relationships.
     */
    private function formatDataDynamically(Collection $collection): array
    {
        if ($collection->isEmpty()) {
            return [];
        }

        return $collection->map(function ($item) {
            // Get all attributes
            $data = $item->toArray();
            
            // Format the data for display
            return $this->formatRow($data);
        })->toArray();
    }

    /**
     * Format a single row of data.
     * Handles nested arrays, dates, and special values.
     */
    private function formatRow(array $data, string $prefix = ''): array
    {
        $formatted = [];

        foreach ($data as $key => $value) {
            // Create human-readable header
            $header = $this->formatHeader($prefix ? "{$prefix} {$key}" : $key);

            // Handle different value types
            if (is_null($value)) {
                $formatted[$header] = 'N/A';
            } elseif (is_bool($value)) {
                $formatted[$header] = $value ? 'Yes' : 'No';
            } elseif (is_array($value)) {
                // Handle nested objects (like user, post, etc.)
                if ($this->isAssociativeArray($value)) {
                    // For objects like user, only include key fields to avoid clutter
                    if (isset($value['name'])) {
                        $formatted[$header . ' Name'] = $value['name'];
                    } elseif (isset($value['id'])) {
                        $formatted[$header . ' ID'] = $value['id'];
                    } else {
                        // Flatten nested array
                        $nested = $this->formatRow($value, $header);
                        $formatted = array_merge($formatted, $nested);
                    }
                } else {
                    // For simple arrays, join values
                    $formatted[$header] = implode(', ', array_filter($value));
                }
            } elseif ($this->isDateString($value)) {
                // Format dates
                try {
                    $formatted[$header] = \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $formatted[$header] = $value;
                }
            } elseif (is_string($value) && strlen($value) > 200) {
                // Limit very long strings
                $formatted[$header] = \Illuminate\Support\Str::limit($value, 200);
            } else {
                $formatted[$header] = $value;
            }
        }

        // Remove internal Laravel fields that aren't useful for exports
        $formatted = collect($formatted)->except([
'pivot', 'laravel_through_key'
        ])->toArray();

        return $formatted;
    }

    /**
     * Format header name (snake_case to Title Case).
     */
    private function formatHeader(string $key): string
    {
        // Remove common suffixes that create redundancy
        $key = preg_replace('/_id$/', '', $key);
        
        // Convert snake_case to Title Case
        return str_replace('_', ' ', ucwords(str_replace('_', ' ', $key)));
    }

    /**
     * Check if array is associative (object-like).
     */
    private function isAssociativeArray($array): bool
    {
        if (!is_array($array)) {
            return false;
        }
        
        if (empty($array)) {
            return false;
        }
        
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Check if string is a date.
     */
    private function isDateString($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        // Common date patterns
        $patterns = [
            '/^\d{4}-\d{2}-\d{2}/',  // YYYY-MM-DD
            '/^\d{2}\/\d{2}\/\d{4}/', // DD/MM/YYYY or MM/DD/YYYY
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate file based on format.
     */
    private function generateFile(array $data, array $metadata, string $format, string $type): string
    {
        $filename = $this->generateFileName($type, $format);
        $filepath = storage_path("app/private/exports/agripulse/{$filename}");

        // Ensure directory exists
        if (!file_exists(storage_path('app/private/exports/agripulse'))) {
            mkdir(storage_path('app/private/exports/agripulse'), 0755, true);
        }

        switch ($format) {
            case 'pdf':
                return $this->createPDF($data, $metadata, $filepath);
            case 'excel':
                return $this->createExcel($data, $metadata, $filepath);
            case 'word':
                return $this->createWord($data, $metadata, $filepath);
            case 'csv':
                return $this->createCSV($data, $metadata, $filepath);
            default:
                throw new \Exception("Unsupported export format: {$format}");
        }
    }

    /**
     * Create PDF export using the universal template.
     */
    private function createPDF(array $data, array $metadata, string $filepath): string
    {
        $pdf = Pdf::loadView('exports.agripulse.export', [
            'data' => $data,
            'metadata' => $metadata
        ]);

        $pdf->setPaper('a4', 'landscape'); // Landscape for better table viewing
        $pdf->save($filepath);
        return $filepath;
    }

    /**
     * Create Excel export (fully dynamic).
     */
    private function createExcel(array $data, array $metadata, string $filepath): string
    {
        $export = new class($data, $metadata) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithStyles
        {
            private $data;
            private $metadata;

            public function __construct($data, $metadata)
            {
                $this->data = $data;
                $this->metadata = $metadata;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return !empty($this->data) ? array_keys($this->data[0]) : [];
            }

            public function title(): string
            {
                return \Illuminate\Support\Str::limit($this->metadata['type'], 30);
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                // Style header row
                $sheet->getStyle('1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '174938'],
                    ],
                ]);

                // Auto-size columns
                foreach (range('A', $sheet->getHighestColumn()) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                return [];
            }
        };

        Excel::store($export, 'exports/agripulse/' . basename($filepath), 'local');
        return $filepath;
    }

    /**
     * Create Word export (fully dynamic).
     */
    private function createWord(array $data, array $metadata, string $filepath): string
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection(['orientation' => 'landscape']);

        // Add title
        $section->addTitle($metadata['type'] . ' Export', 1);
        $section->addText('Exported: ' . $metadata['exported_at']);
        $section->addText('Total Records: ' . $metadata['total_records']);
        $section->addTextBreak(1);

        // Add table
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $columnWidth = 1500; // Dynamic width distribution
            
            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '999999',
                'width' => count($headers) * $columnWidth
            ]);

            // Header row
            $table->addRow();
            foreach ($headers as $header) {
                $table->addCell($columnWidth, ['bgColor' => '174938'])
                    ->addText($header, ['bold' => true, 'color' => 'FFFFFF', 'size' => 9]);
            }

            // Data rows
            foreach ($data as $row) {
                $table->addRow();
                foreach ($row as $value) {
                    $table->addCell($columnWidth)->addText((string)$value, ['size' => 9]);
                }
            }
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filepath);

        return $filepath;
    }

    /**
     * Create CSV export (fully dynamic).
     */
    private function createCSV(array $data, array $metadata, string $filepath): string
    {
        $file = fopen($filepath, 'w');

        // Add metadata as comments
        fputcsv($file, ['# ' . $metadata['type'] . ' Export']);
        fputcsv($file, ['# Exported: ' . $metadata['exported_at']]);
        fputcsv($file, ['# Total Records: ' . $metadata['total_records']]);
        fputcsv($file, []);

        // Add headers and data
        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }

        fclose($file);
        return $filepath;
    }

    /**
     * Generate unique filename.
     */
    private function generateFileName(string $type, string $format): string
    {
        $timestamp = now()->format('Y-m-d_His');
        $extension = $format === 'excel' ? 'xlsx' : ($format === 'word' ? 'docx' : $format);
        return "agripulse_{$type}_{$timestamp}.{$extension}";
    }
}
