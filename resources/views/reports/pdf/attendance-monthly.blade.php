@extends('reports.pdf.layout')

@section('content')
    <!-- School Header -->
    <div class="school-header">
        <p>Republic of the Philippines</p>
        <p><strong>Department of Education</strong></p>
        <p>{{ $school['region'] }}</p>
        <p style="font-size: 11pt; margin-top: 5px;"><strong>{{ $school['name'] }}</strong></p>
        <p>School ID: {{ $school['lrn_id'] }} &bull; {{ $school['address'] }}</p>
        <h2 style="margin-top: 10px;">MONTHLY ATTENDANCE REPORT</h2>
        <p>{{ $gradeLevel }} &mdash; {{ $monthName }}</p>
    </div>

    <!-- Attendance Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th class="text-left" style="width: 30%;">Student Name</th>
                <th style="width: 15%;">LRN</th>
                <th style="width: 10%;">Present</th>
                <th style="width: 10%;">Absent</th>
                <th style="width: 10%;">Late</th>
                <th style="width: 10%;">Excused</th>
                <th style="width: 10%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totPresent = 0; $totAbsent = 0; $totLate = 0; $totExcused = 0; $totTotal = 0;
            @endphp
            @foreach($studentRows as $index => $row)
                @php
                    $totPresent += $row['present'];
                    $totAbsent += $row['absent'];
                    $totLate += $row['late'];
                    $totExcused += $row['excused'];
                    $totTotal += $row['total'];
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $row['student']->full_name }}</td>
                    <td style="font-size: 8pt;">{{ $row['student']->lrn }}</td>
                    <td>{{ $row['present'] ?: '' }}</td>
                    <td>{{ $row['absent'] ?: '' }}</td>
                    <td>{{ $row['late'] ?: '' }}</td>
                    <td>{{ $row['excused'] ?: '' }}</td>
                    <td>{{ $row['total'] ?: '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0;">
                <td colspan="3" class="text-left bold">Total</td>
                <td class="bold">{{ $totPresent }}</td>
                <td class="bold">{{ $totAbsent }}</td>
                <td class="bold">{{ $totLate }}</td>
                <td class="bold">{{ $totExcused }}</td>
                <td class="bold">{{ $totTotal }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <table class="no-border" style="font-size: 9pt; margin-top: 25px;">
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
