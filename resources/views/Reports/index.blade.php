<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle ?? 'Report' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Helvetica';
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
            background-color: hsl(0, 100%, 100%);
            position: relative;
            overflow-x: hidden;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 400px;
            height: 400px;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: 0;
        }

        .watermark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        header, main, footer {
            position: relative;
            z-index: 1;
        }

        header {
            padding: 5px;
            border-bottom: 2px solid #dcdcdc;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header table {
            width: 100%;
        }

        header img {
            max-width: 120px;
        }

        .org-info h1 {
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #2c3e50;
        }

        .org-info p {
            font-size: 14px;
            margin: 4px 0;
            font-weight: 600;
            color: #4a4a4a;
        }

        main {
            width: 98%;
            margin: 10px auto;
            padding: 15px;
            /* background-color: #f9f9f9; */
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        main h2 {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: left;
            border-bottom: 2px solid #dcdcdc;
            padding-bottom: 10px;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            /* background-color: #f0f0f0; */
            border-radius: 8px;
        }

        table.report-table th, table.report-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #c0c0c0;
            font-size: 14px;
        }

        table.report-table th {
            /* background-color: #dcdcdc; */
            font-weight: 700;
            color: #333;
        }

        table.report-table tr:nth-child(even) {
            /* background-color: #e8e8e8; */
        }

        table.report-table tfoot td {
            font-weight: 700;
            /* background-color: #dcdcdc; */
            border-top: 2px solid #c0c0c0;
        }

        footer {
            text-align: center;
            padding: 12px;
            border-top: 2px solid #dcdcdc;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 12px;
            background-color: #ffffff;
            color: #555;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        footer p {
            margin: 0;
        }

        a {
            color: #2a72b5;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Watermark Image -->
    <div class="watermark">
        <img src="{{ public_path('assets/logo/logo2.png') }}" alt="Watermark Logo">
    </div>

    <header>
        <table>
            <tr>
                <td style="width: 130px; vertical-align: middle;">
                    <img src="{{ public_path('assets/logo/logo2.png') }}" alt="Organization Logo">
                </td>
                <td style="vertical-align: middle;">
                    <div class="org-info">
                        <h1>{{ $settings->organization_name ?? 'Techquorum Solutions' }}</h1>
                        <p>{{ $settings->organization_address ?? '1234 Example Street, City, Country' }}</p>
                        <p>
                            <a href="mailto:{{ $settings->organization_email ?? 'info@techquorum.co.tz' }}">
                                {{ $settings->organization_email ?? 'info@techquorum.co.tz' }}
                            </a> |
                            <a href="tel:{{ $settings->organization_phone ?? '0(222) 444 303' }}">
                                {{ $settings->organization_phone ?? '0(222) 444 303' }}
                            </a>
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <h2>{{ $reportTitle ?? 'Report' }}</h2>
        @if (!empty($data['tableHeaders']) && !empty($data['tableHeaders']))
            <table class="report-table">
                <thead>
                    <tr>
                        @foreach ($data['tableHeaders'] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['tableRows'] as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="{{ count($data['tableHeaders']) - 1 }}" style="text-align: right;">Total Amount:</td>
                        <td>{{ number_format(100000, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p style="text-align: center; font-size: 16px; margin-top: 20px;">No data available to display.</p>
        @endif
    </main>

    <footer>
        <p>Generated on {{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
    </footer>
</body>

</html>
