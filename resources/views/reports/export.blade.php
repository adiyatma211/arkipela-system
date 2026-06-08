<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            margin: 24px;
        }

        h1 {
            margin-bottom: 4px;
            font-size: 24px;
        }

        .period {
            margin-bottom: 20px;
            color: #6b7280;
            font-size: 13px;
        }

        h2 {
            margin: 24px 0 8px;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #eef4ff;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="period">Period: {{ $periodLabel }}</div>

    @foreach ($sections as $section)
        <h2>{{ $section['title'] }}</h2>
        <table>
            <thead>
                <tr>
                    @foreach ($section['columns'] as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($section['rows'] as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($section['columns']) }}">No data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>
