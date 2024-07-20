<style>
    .receipt {
        width: calc(75mm - 10px);
        margin: 0px auto;
        font-family: Arial, sans-serif;
    }

    .receipt h1 {
        text-align: center;
        font-size: 1.5em;
    }

    .receipt h2 {
        text-align: center;
        font-size: 1.2em;
    }

    .receipt h3 {
        text-align: center;
        font-size: 1em;
    }

    .receipt p {
        font-size: 0.8em;
    }

    .receipt .receipt-items {
        margin-top: 10px;
    }

    .receipt .receipt-items table {
        width: 100%;
        border-collapse: collapse;
    }

    .receipt .receipt-items table th {
        text-align: left;
        border-bottom: 1px solid #000;
    }

    .receipt .receipt-items table td {
        padding: 5px 0;
    }

    .receipt .receipt-items table td:first-child {
        width: 70%;
    }

    .receipt .receipt-items table td:last-child {
        text-align: right;
    }

    .receipt .receipt-items table td.total {
        border-top: 1px solid #000;
    }

    .receipt .receipt-items table td.total span {
        font-weight: bold;
    }

    .receipt .receipt-footer {
        margin-top: 10px;
    }

    .receipt .receipt-footer p {
        text-align: center;
    }

    .receipt .receipt-footer p:last-child {
        margin-top: 5px;
    }

    .receipt .receipt-footer p:last-child span {
        font-weight: bold;
    }

    @media print {
        .receipt {
            width: calc(75mm - 10px);
            margin-left: 5px;
            padding-right: 10px;
            font-family: Arial, sans-serif;
            font-size: 14px; /* Adjust font size for readability */
        }

        .receipt h1 {
            text-align: center;
            font-size: 18px; /* Larger heading for emphasis */
        }

        .receipt h2 {
            text-align: center;
            font-size: 16px; /* Slightly smaller sub-heading */
        }

        .receipt h3 {
            text-align: center;
            font-size: 14px;
        }

        .receipt p ,
        .receipt .receipt-items table th,
        .receipt .receipt-items table td{
            font-size: 14px; /* Smaller paragraph text */
            margin: 5px 0; /* Adjust margins for compactness */
        }

        .receipt .receipt-items {
            margin-top: 12px;
        }

        .receipt .receipt-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt .receipt-items table th,
        .receipt .receipt-items table td {
            padding: 3px 0; /* Compact padding for table cells */
        }

        .receipt .receipt-items table td:first-child {
            width: 60%; /* Adjust width of the first column */
        }

        .receipt .receipt-footer {
            margin-top: 10px;
        }

        .receipt .receipt-footer p {
            text-align: center;
            font-size: 14px;
            margin: 3px 0; /* Adjust margin for footer */
        }

        .receipt .receipt-footer p:last-child {
            margin-top: 5px;
            font-weight: bold;
        }
        body .receipt {
            visibility: visible !important;
        }
        html , body {
            visibility: hidden;
        }
        .row-start-2 {
            grid-row-start: 0 !important;
        }

    }

</style>

<div class="receipt">

    <h2>Appointment Number # {{ $record->id }}</h2>
    <h3>{{ $record->created_at->format('d/m/Y H:i') }}</h3>

    <div class="receipt-items">
        <table>

            <tbody>
            <tr>
                <td>Patient Name</td>
                <td>{{ $record->patient->name }}</td>
            </tr>
            <tr>
                <td>Doctor Name</td>
                <td>{{ $record->doctor->name }}</td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>{{ $record->patient->gender }}</td>
            </tr>
            @if ($record->temperature != "")
            <tr>
                <td>Temperature</td>
                <td>{{ $record->temperature }}</td>

            </tr>
            @endif
            @if ($record->weight != "")
            <tr>
                <td>Weight</td>
                <td>{{ $record->weight }}</td>
            </tr>
            @endif

            @if ($record->bp != "")
            <tr>
                <td>Blood Pressure</td>
                <td>{{ $record->bp }}</td>
            </tr>
            @endif

            </tbody>
        </table>
    </div>

    <div class="receipt-footer">
        <p>برائے کرم تشریف رکھیے اور اپنی باری کا انتظار کریں</p>
        <p><span>Have a nice day!</span></p>
    </div>
</div>


<script>
    window.onafterprint = function(e){
        const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const url  = urlParams.get('redirectUrl')
    window.location.replace(url);
    };

    window.print();

</script>
