<!-- School Header -->
<div class="school-header">
    <p>Republic of the Philippines</p>
    <p><strong>Department of Education</strong></p>
    <p>{{ $school['region'] }}</p>
    <p style="font-size: 11pt; margin-top: 5px;"><strong>{{ $school['name'] }}</strong></p>
    <p>School ID: {{ $school['lrn_id'] }} &bull; {{ $school['address'] }}</p>
    <h2 style="margin-top: 10px;">REPORT CARD (SF9)</h2>
    <p>School Year {{ $schoolYear->name }}</p>
</div>

<!-- Student Information -->
<table class="no-border" style="margin-bottom: 10px; font-size: 9pt;">
    <tr>
        <td class="text-left" style="width: 15%; padding: 2px 0;"><strong>Name:</strong></td>
        <td class="text-left" style="width: 35%; padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->full_name }}</td>
        <td class="text-left" style="width: 10%; padding: 2px 0;"><strong>LRN:</strong></td>
        <td class="text-left" style="width: 40%; padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->lrn }}</td>
    </tr>
    <tr>
        <td class="text-left" style="padding: 2px 0;"><strong>Grade:</strong></td>
        <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->grade_level_label }}</td>
        <td class="text-left" style="padding: 2px 0;"><strong>Teacher:</strong></td>
        <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ $teacher->name ?? 'N/A' }}</td>
    </tr>
</table>

<!-- Learning Areas Table -->
<table style="margin-bottom: 10px;">
    <thead>
        <tr>
            <th class="text-left" style="width: 30%;">Learning Areas</th>
            <th style="width: 10%;">Q1</th>
            <th style="width: 10%;">Q2</th>
            <th style="width: 10%;">Q3</th>
            <th style="width: 10%;">Q4</th>
            <th style="width: 15%;">Final Grade</th>
            <th style="width: 15%;">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subjectGrades as $sg)
            <tr>
                <td class="text-left">{{ $sg['subject']->name }}</td>
                @foreach(['Q1', 'Q2', 'Q3', 'Q4'] as $q)
                    <td>
                        @if($sg['quarters'][$q] !== null)
                            {{ number_format((float)$sg['quarters'][$q], 0) }}
                        @endif
                    </td>
                @endforeach
                <td class="bold">
                    @if($sg['final_grade'] !== null)
                        {{ number_format($sg['final_grade'], 2) }}
                    @endif
                </td>
                <td>
                    @if($sg['remarks'])
                        {{ $sg['remarks'] }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f0f0f0;">
            <td class="text-left bold">General Average</td>
            <td colspan="4"></td>
            <td class="bold">
                @if($generalAverage !== null)
                    {{ number_format($generalAverage, 2) }}
                @endif
            </td>
            <td class="bold">
                @if($generalRemarks)
                    {{ $generalRemarks }}
                @endif
            </td>
        </tr>
    </tfoot>
</table>

<!-- Attendance Summary -->
<p style="font-size: 9pt; margin-bottom: 5px;"><strong>Attendance Record</strong></p>
<table style="margin-bottom: 15px; font-size: 8pt;">
    <thead>
        <tr>
            <th class="text-left" style="width: 25%;">Month</th>
            <th style="width: 25%;">Days Present</th>
            <th style="width: 25%;">Days Absent</th>
            <th style="width: 25%;">Days Tardy</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalPresent = 0; $totalAbsent = 0; $totalTardy = 0;
        @endphp
        @foreach($attendance as $month)
            @php
                $totalPresent += $month['present'];
                $totalAbsent += $month['absent'];
                $totalTardy += $month['tardy'];
            @endphp
            <tr>
                <td class="text-left">{{ $month['label'] }}</td>
                <td>{{ $month['present'] ?: '' }}</td>
                <td>{{ $month['absent'] ?: '' }}</td>
                <td>{{ $month['tardy'] ?: '' }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f0f0f0;">
            <td class="text-left bold">Total</td>
            <td class="bold">{{ $totalPresent }}</td>
            <td class="bold">{{ $totalAbsent }}</td>
            <td class="bold">{{ $totalTardy }}</td>
        </tr>
    </tfoot>
</table>

<!-- Signatures -->
<table class="no-border" style="font-size: 9pt; margin-top: 20px;">
    <tr>
        <td style="width: 50%; padding: 0;">
            <p style="margin-bottom: 25px;">Prepared by:</p>
            <p style="border-top: 1px solid #333; display: inline-block; padding-top: 3px; width: 200px; text-align: center;">
                <strong>{{ $teacher->name ?? '' }}</strong>
            </p>
            <p style="font-size: 8pt; text-align: left;">Class Adviser</p>
        </td>
        <td style="width: 50%; padding: 0;">
            <p style="margin-bottom: 25px;">Noted by:</p>
            <p style="border-top: 1px solid #333; display: inline-block; padding-top: 3px; width: 200px; text-align: center;">
                &nbsp;
            </p>
            <p style="font-size: 8pt; text-align: left;">Parent/Guardian</p>
        </td>
    </tr>
</table>
