<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: center;
            font-size: 9pt;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .school-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .school-header h1 {
            font-size: 14pt;
            margin: 0;
        }
        .school-header h2 {
            font-size: 12pt;
            margin: 5px 0;
        }
        .school-header p {
            font-size: 9pt;
            margin: 2px 0;
        }
        .no-border,
        .no-border td,
        .no-border th {
            border: none;
        }
        .page-break {
            page-break-after: always;
        }
        .failed {
            font-weight: bold;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            display: inline-block;
            width: 200px;
        }
        @yield('extra-styles')
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
