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
        padding: 5px 10px;
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
                 padding: 5px 10px; /* Compact padding for table cells */
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

    

    <div class="receipt-items" >
        <table cellspacing="0"  border="1">

            <tbody>
                @foreach ($data as $key => $value)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$value}}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>


</div> 

<script>
   

    window.print();

</script>
