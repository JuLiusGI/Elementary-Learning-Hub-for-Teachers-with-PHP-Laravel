@extends('reports.pdf.layout')

@section('extra-styles')
    body { padding: 15px; }
    th, td { font-size: 8pt; padding: 3px 4px; }
@endsection

@section('content')
    <!-- School Header -->
    <div class="school-header">
        <p>Republic of the Philippines</p>
        <p><strong>Department of Education</strong></p>
        <p>{{ $school['region'] }}</p>
        <p style="font-size: 11pt; margin-top: 5px;"><strong>{{ $school['name'] }}</strong></p>
        <p>School ID: {{ $school['lrn_id'] }} &bull; {{ $school['address'] }}</p>
        <h2 style="margin-top: 10px;">GRADE SUMMARY REPORT</h2>
        <p>{{ $gradeLevel }} &mdash; {{ $quarter }} &mdash; School Year {{ $schoolYear->name }}</p>
    </div>

    <!-- Grades Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th class="text-left" style="min-width: 120px;">Student Name</th>
                <th style="min-width: 80px;">LRN</th>
                @foreach($subjects as $subject)
                    <th style="font-size: 7pt;">{{ $subject->code ?: $subject->name }}</th>
                @endforeach
                <th>Average</th>
            </tr>
        </thead>
        <tbody>
            @php
                $passedCount = 0;
                $failedCount = 0;
                $allAverages = [];
            @endphp
            @foreach($studentRows as $index => $row)
                @php
                    if ($row['average'] !== null) {
                        $allAverages[] = $row['average'];
                        if ($row['average'] >= 75) $passedCount++;
                        else $failedCount++;
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left" style="font-size: 8pt;">{{ $row['student']->full_name }}</td>
                    <td style="font-size: 7pt;">{{ $row['student']->lrn }}</td>
                    @foreach($subjects as $subject)
                        @php $grade = $row['grades'][$subject->id] ?? null; @endphp
                        <td>
                            @if($grade !== null)
                                @if((float)$grade < 75)
                                    <strong>{{ number_format((float)$grade, 0) }}*</strong>
                                @else
                                    {{ number_format((float)$grade, 0) }}
                                @endif
                            @endif
                        </td>
                    @endforeach
                    <td class="bold">
                        @if($row['average'] !== null)
                            @if($row['average'] < 75)
                                <strong>{{ number_format($row['average'], 2) }}*</strong>
                            @else
                                {{ number_format($row['average'], 2) }}
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Statistics -->
    <table class="no-border" style="font-size: 8pt; margin-top: 10px;">
        <tr>
            <td class="text-left" style="width: 33%; padding: 2px 0;">
                <strong>Class Average:</strong>
                {{ count($allAverages) > 0 ? number_format(array_sum($allAverages) / count($allAverages), 2) : 'N/A' }}
            </td>
            <td class="text-left" style="width: 33%; padding: 2px 0;">
                <strong>Passed:</strong> {{ $passedCount }}
            </td>
            <td class="text-left" style="width: 33%; padding: 2px 0;">
                <strong>Failed:</strong> {{ $failedCount }}
            </td>
        </tr>
    </table>
    <p style="font-size: 7pt; color: #666; margin-top: 3px;">* Grades below 75 (failing)</p>

    <!-- Footer -->
    <table class="no-border" style="font-size: 9pt; margin-top: 20px;">
        <tr>
            <td class="text-left" style="width: 50%; padding: 0;">
                <p style="margin-bottom: 25px;">Prepared by:</p>
                <p style="border-top: 1px solid #333; display: inline-block; padding-top: 3px; width: 200px; text-align: center;">
                    <strong>{{ $teacher->name ?? '' }}</strong>
                </p>
                <p style="font-size: 8pt;">Class Adviser</p>
            </td>
            <td class="text-right" style="width: 50%; padding: 0;">
                <p style="font-size: 8pt; color: #666;">Generated: {{ now()->format('F d, Y') }}</p>
            </td>
        </tr>
    </table>
@endsection
