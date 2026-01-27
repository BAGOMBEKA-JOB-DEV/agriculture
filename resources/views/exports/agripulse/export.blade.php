<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AgriPulse {{ $metadata['type'] }} Export</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        .header { background: linear-gradient(135deg, #174938 0%, #2a7a5f 100%); color: white; padding: 20px; margin-bottom: 20px; }
        .header h1 { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .header .subtitle { font-size: 12px; opacity: 0.9; }
        .metadata { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #174938; }
        .metadata-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .metadata-item { font-size: 10px; }
        .metadata-item strong { color: #174938; display: block; margin-bottom: 2px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9px; }
        thead { background: #174938; color: white; }
        th { padding: 10px 8px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 8px; border-bottom: 1px solid #e9ecef; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody tr:hover { background: #e9ecef; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-published { background: #d4edda; color: #155724; }
        .badge-hidden { background: #fff3cd; color: #856404; }
        .badge-flagged, .badge-deleted, .badge-suspended { background: #f8d7da; color: #721c24; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-resolved { background: #d4edda; color: #155724; }
        .badge-dismissed { background: #e2e3e5; color: #6c757d; }
        .badge-active, .badge-yes { background: #d4edda; color: #155724; }
        .badge-no { background: #e2e3e5; color: #6c757d; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 2px solid #174938; font-size: 8px; color: #6c757d; text-align: center; }
        .cell-content { max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .cell-numeric { text-align: center; }
        .cell-id { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AgriPulse {{ $metadata['type'] }} Export</h1>
        <div class="subtitle">Moderation Data Export - Confidential & Internal Use Only</div>
    </div>

    <div class="metadata">
        <div class="metadata-grid">
            <div class="metadata-item">
                <strong>Export Date</strong>
                <span>{{ $metadata['exported_at'] }}</span>
            </div>
            <div class="metadata-item">
                <strong>Exported By</strong>
                <span>{{ $metadata['exported_by'] }}</span>
            </div>
            <div class="metadata-item">
                <strong>Total Records</strong>
                <span>{{ $metadata['total_records'] }}</span>
            </div>
            <div class="metadata-item">
                <strong>Active Filters</strong>
                <span>
                    @if(!empty($metadata['filters']))
                        @php
                            $appliedFilters = [];
                            foreach($metadata['filters'] as $key => $value) {
                                if($value) {
                                    $appliedFilters[] = ucfirst($key) . ': ' . (is_bool($value) ? 'Yes' : $value);
                                }
                            }
                        @endphp
                        {{ !empty($appliedFilters) ? implode(', ', $appliedFilters) : 'None' }}
                    @else
                        None
                    @endif
                </span>
            </div>
        </div>
    </div>

    @if(!empty($data))
        <table>
            <thead>
                <tr>
                    @foreach(array_keys($data[0]) as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($row as $key => $value)
                            @php
                                // Detect cell type for styling
                                $isId = strtolower($key) === 'id' || str_ends_with(strtolower($key), ' id');
                                $isNumeric = is_numeric($value) && !$isId;
                                $isStatus = str_contains(strtolower($key), 'status') || in_array(strtolower($value), ['published', 'hidden', 'flagged', 'deleted', 'pending', 'resolved', 'dismissed', 'active', 'suspended', 'yes', 'no']);
                                $cellClass = $isId ? 'cell-id' : ($isNumeric ? 'cell-numeric' : 'cell-content');
                            @endphp
                            <td class="{{ $cellClass }}">
                                @if($isStatus)
                                    <span class="badge badge-{{ strtolower($value) }}">{{ $value }}</span>
                                @elseif($isId)
                                    #{{ $value }}
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #6c757d; font-size: 12px;">
            <p>No data available for export.</p>
        </div>
    @endif

    <div class="footer">
        <p>AgriPulse Moderation System | Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>This document contains sensitive moderation data. Handle with care.</p>
    </div>
</body>
</html>
