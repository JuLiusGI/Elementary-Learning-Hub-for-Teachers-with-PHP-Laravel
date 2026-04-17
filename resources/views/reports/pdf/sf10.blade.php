@extends('reports.pdf.layout')

@section('content')
    <!-- Header -->
    <div class="school-header">
        <p>Republic of the Philippines</p>
        <p><strong>Department of Education</strong></p>
        <p>{{ $school['region'] }}</p>
        <h2 style="margin-top: 8px;">LEARNER'S PERMANENT ACADEMIC RECORD (SF10)</h2>
        <p>FOR ELEMENTARY SCHOOL</p>
    </div>

    <!-- Student Personal Information -->
    <p style="font-size: 9pt; margin-bottom: 5px; background-color: #f0f0f0; padding: 3px 6px;"><strong>LEARNER'S INFORMATION</strong></p>
    <table class="no-border" style="margin-bottom: 10px; font-size: 9pt;">
        <tr>
            <td class="text-left" style="width: 12%; padding: 2px 0;"><strong>Name:</strong></td>
            <td class="text-left" style="width: 38%; padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }} {{ $student->suffix }}</td>
            <td class="text-left" style="width: 8%; padding: 2px 0;"><strong>LRN:</strong></td>
            <td class="text-left" style="width: 42%; padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->lrn }}</td>
        </tr>
        <tr>
            <td class="text-left" style="padding: 2px 0;"><strong>Birthdate:</strong></td>
            <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->date_of_birth->format('F d, Y') }}</td>
            <td class="text-left" style="padding: 2px 0;"><strong>Gender:</strong></td>
            <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ ucfirst($student->gender) }}</td>
        </tr>
        <tr>
            <td class="text-left" style="padding: 2px 0;"><strong>Address:</strong></td>
            <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;" colspan="3">{{ collect([$student->address_street, $student->address_barangay, $student->address_municipality, $student->address_province])->filter()->implode(', ') ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-left" style="padding: 2px 0;"><strong>Guardian:</strong></td>
            <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->guardian_name }}</td>
            <td class="text-left" style="padding: 2px 0;"><strong>Contact:</strong></td>
            <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ $student->guardian_contact ?: 'N/A' }}</td>
        </tr>
    </table>

    <!-- School Information -->
    <p style="font-size: 9pt; margin-bottom: 5px; background-color: #f0f0f0; padding: 3px 6px;"><strong>SCHOOL INFORMATION</strong></p>
    <table class="no-border" style="margin-bottom: 15px; font-size: 9pt;">
        <tr>
            <td class="text-left" style="width: 12%; padding: 2px 0;"><strong>School:</strong></td>
            <td class="text-left" style="width: 38%; padding: 2px 0; border-bottom: 1px solid #999;">{{ $school['name'] }}</td>
            <td class="text-left" style="width: 12%; padding: 2px 0;"><strong>School ID:</strong></td>
            <td class="text-left" style="width: 38%; padding: 2px 0; border-bottom: 1px solid #999;">{{ $school['lrn_id'] }}</td>
        </tr>
        <tr>
            <td class="text-left" style="padding: 2px 0;"><strong>Address:</strong></td>
            <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ $school['address'] }}</td>
            <td class="text-left" style="padding: 2px 0;"><strong>Region:</strong></td>
            <td class="text-left" style="padding: 2px 0; border-bottom: 1px solid #999;">{{ $school['region'] }}</td>
        </tr>
    </table>

    <!-- Scholastic Record -->
    <p style="font-size: 9pt; margin-bottom: 5px; background-color: #f0f0f0; padding: 3px 6px;"><strong>SCHOLASTIC RECORD</strong></p>

    @forelse($scholasticRecords as $record)
        <table class="no-border" style="margin-bottom: 5px; font-size: 8pt;">
            <tr>
                <td class="text-left" style="padding: 2px 0;">
                    <strong>School Year: {{ $record['school_year']->name }}</strong>
                    &nbsp;&bull;&nbsp;
                    <strong>Grade Level: {{ $record['grade_level'] }}</strong>
                </td>
            </tr>
        </table>

        @if($record['is_kinder'])
            <!-- Kindergarten Domain Ratings -->
            <table style="margin-bottom: 15px; font-size: 8pt;">
                <thead>
                    <tr>
                        <th class="text-left" style="width: 40%;">Developmental Domain</th>
                        <th style="width: 15%;">Q1</th>
                        <th style="width: 15%;">Q2</th>
                        <th style="width: 15%;">Q3</th>
                        <th style="width: 15%;">Q4</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record['domains'] as $da)
                        <tr>
                            <td class="text-left">{{ $da['domain'] }}</td>
                            @foreach(['Q1', 'Q2', 'Q3', 'Q4'] as $q)
                                <td>{{ $da['quarters'][$q] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <!-- Grades 1-6 Subject Grades -->
            <table style="margin-bottom: 15px; font-size: 8pt;">
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
                    @foreach($record['subjects'] as $sg)
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
                            <td>{{ $sg['remarks'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f0f0f0;">
                        <td class="text-left bold">General Average</td>
                        <td colspan="4"></td>
                        <td class="bold">
                            @if($record['general_average'] !== null)
                                {{ number_format($record['general_average'], 2) }}
                            @endif
                        </td>
                        <td class="bold">{{ $record['remarks'] ?? '' }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif
    @empty
        <p style="font-size: 9pt; color: #666; padding: 10px 0;">No scholastic records found.</p>
    @endforelse

    <!-- Certification -->
    <table class="no-border" style="font-size: 9pt; margin-top: 30px;">
        <tr>
            <td style="width: 50%; padding: 0;">
                <p style="margin-bottom: 30px;">Certified True and Correct:</p>
                <p style="border-top: 1px solid #333; display: inline-block; padding-top: 3px; width: 200px; text-align: center;">
                    &nbsp;
                </p>
                <p style="font-size: 8pt;">School Head</p>
            </td>
            <td style="width: 50%; padding: 0;">
                <p style="margin-bottom: 30px;">Date:</p>
                <p style="border-top: 1px solid #333; display: inline-block; padding-top: 3px; width: 200px; text-align: center;">
                    &nbsp;
                </p>
            </td>
        </tr>
    </table>
@endsection
