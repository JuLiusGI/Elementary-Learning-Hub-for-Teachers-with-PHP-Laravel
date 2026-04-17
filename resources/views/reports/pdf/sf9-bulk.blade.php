@extends('reports.pdf.layout')

@section('content')
    @foreach($studentsData as $index => $data)
        @if($isKinder)
            @include('reports.pdf.partials.sf9-kinder-content', $data)
        @else
            @include('reports.pdf.partials.sf9-content', $data)
        @endif

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
@endsection
